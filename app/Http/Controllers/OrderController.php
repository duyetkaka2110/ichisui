<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon;
use App\GetMessage;
use Auth;
use DB;
use Excel;
use App\Models\T_OrderMaterial;
use App\Models\T_OrderMaterialAsOmat;
use App\Models\W_OrderMaterial;
use App\Models\M_Supplier;
use App\Exports\OrderExportMulti;
use App\Models\M_MaterialPrice;
use App\Models\M_System;
use App\Models\W_OrderMaterialAsOmat;

class OrderController extends Controller
{
    protected $_today;
    protected $_timetoday;
    protected $checkFunc = false;
    public function __construct()
    {
        ini_set('max_execution_time', 300);
        date_default_timezone_set('Asia/Tokyo');
        $this->_timetoday = Carbon\Carbon::now()->format("Y/m/d H:i:s");
        $this->_today = Carbon\Carbon::now()->format("Y/m/d");
    }


    /**
     *  資材発注画面
     * @access public
     * @param Request $rq
     * @return array
     */
    public function index(Request $request)
    {
        // 検索条件
        $dataWhere = [
            "txtUserID" => Auth::user()->UserID,

        ];
        // 資材発注一覧を取る
        $listOrder =  $this->_getListOrder();

        //仕入先一覧
        $listSupplier = M_Supplier::select("SupplierID", "SupplierNM")->where("DeleteFlg", 0)->get();

        $datasend = array(
            "list" => $listOrder,
            "listSupplier" => $listSupplier,
        );
        return view("order.index", $datasend);
    }

    /**
     *  「発注数」と「仕入単価」更新
     * @access public
     * @param Request $rq
     * @return array
     */
    public function updateOrderByID(Request $rq)
    {
        $datasend = [
            "status" => 0,
            "Msg" => GetMessage::getMessageByID("error005")
        ];
        $data = [];
        if ($rq->filled("PurUnit")) {
            $data["PurUnit"] = $rq->PurUnit;
        }
        if ($rq->filled("OrderNum")) {
            $data["OrderNum"] = $rq->OrderNum;
        }
        $order = W_OrderMaterial::where("UseMaterialID", $rq->OrderID);
        if ($order->count()) {
            if ($order->update((array) $data)) {
                $datasend = [
                    "status" => 1,
                    "Msg" => "更新が完了しました"
                ];
            } else {
                $datasend["Msg"] =  GetMessage::getMessageByID("error026");
            }
        }
        return $datasend;
    }
    /**
     *  「決定」のOKボタンをクリック時データを追加する
     * @access public
     * @param Request $rq
     * @return テキスト
     */
    public function insertupdateOrder(Request $rq)
    {
        $datasend = [
            "status" => 0,
            "Msg" => GetMessage::getMessageByID("error005")
        ];
        if ($rq->MaterialID && $rq->SupplierID) {
            $order = [
                "MaterialID" =>  $rq->MaterialID,
                "SupplierID" =>  $rq->SupplierID,
                "OrderUserID" =>  Auth::user()->UserID,
                "AddUserID" =>  Auth::user()->UserID,
                "OrderNum" =>  0,
                "PurUnit" => $this->_getPurUnit($rq->MaterialID, $rq->SupplierID),
                "TreatOrder" =>  0,
                "OrderDate" =>  $this->_timetoday,
            ];
            $id = W_OrderMaterial::insertGetId($order);
            if ($id) {
                $datasend = [
                    "status" => 1,
                    "Msg" => "決定が完了しました"
                ];
            }
        }

        return $datasend;
    }

    /**
     *  「発注)」ボタンをクリック時
     * @access public
     * @param null
     * @return array Excelファイル
     */
    public function export()
    {
        $orders = W_OrderMaterial::select("MaterialID",  "OrderNum", "OrderUserID", "SupplierID", "PurUnit", "AddUserID")
            ->selectRaw("3 as OrderStatusCD")
            ->selectRaw("CASE  
                        WHEN TreatOrder = 0 THEN 1 
                        ELSE 0 
                        END AS TreatOrder")
            ->selectRaw("'" . $this->_timetoday . "' as OrderDate")
            ->where("OrderUserID", Auth::user()->UserID)
            // ->where("OrderNum", ">=", 1)
            ->get()->toArray();
        // 発注数が1以上のレコードを、「資材発注テーブル」に追加（Ｉｎｓｅｒｔ）する
        T_OrderMaterial::insert($orders);


        // 「発注済み扱い」のスイッチがＯｆｆ、かつ、発注数が1以上のレコードについて、
        $list = $this->_getListOrder(false);
        $data = [
            "UserNM" => Auth::user()->UserNM,
            "Date" => \Carbon\Carbon::now()->format("Y/m/d")
        ];
        // 資材発注ワークテーブルから、ログインユーザが追加したレコードを削除する
        W_OrderMaterial::where("OrderUserID", Auth::user()->UserID)->delete();
        if ($list)
            // 発注書Ｅｘｃｅｌに出力する（※出力仕様については、「発注書Ｅｘｃｅｌ」の基本設計で定義する）
            return Excel::download(new OrderExportMulti($list, $data), '発注書.xlsx');
    }
    /**
     *  全てデータで確認
     * @access private
     * @param string $cmm Column名
     * @return int total
     */
    private function _countValidation(string $cmm)
    {
        $data = W_OrderMaterial::select("MaterialID")
            ->where("OrderUserID", Auth::user()->UserID)
            ->where("TreatOrder", "!=", true);
        // ->where("OrderNum", ">=", 1);
        if ($cmm != "SupplierID")
            $data = $data->where($cmm, 0);
        else
            $data = $data->where($cmm, "");
        return $data->get()->count();
    }
    /**
     *  「発注)」ボタンをクリック時データ確認
     * @access public
     * @param null
     * @return bool true false
     */
    public function checkexport()
    {
        $flag = true;
        $datareturn = ["status" => 1];
        if ($this->_countValidation("SupplierID")) {
            $flag = false;
            $datareturn = [
                "status" => 0,
                "ErrMsg" => GetMessage::getMessageByID("error086")
            ];
        }
        if ($flag)
            if ($this->_countValidation("OrderNum")) {
                $flag = false;
                $datareturn = [
                    "status" => 0,
                    "ErrMsg" => GetMessage::getMessageByID("error087")
                ];
            }
        if ($flag)
            if ($this->_countValidation("PurUnit")) {
                $flag = false;
                $datareturn = [
                    "status" => 0,
                    "ErrMsg" => GetMessage::getMessageByID("error088")
                ];
            }
        if ($flag) {
            $orders =  W_OrderMaterialAsOmat::select("omat.SupplierID", "sup.SupplierNM")
                ->leftJoin("M_Supplier as sup", "sup.SupplierID", "omat.SupplierID")
                ->where("omat.OrderUserID", Auth::user()->UserID)
                ->where("omat.TreatOrder", 0)
                ->groupBy("omat.SupplierID", "sup.SupplierNM")
                ->get()->count();
            if (!$orders) {
                $ordersall =  W_OrderMaterialAsOmat::select("omat.SupplierID", "sup.SupplierNM")
                    ->leftJoin("M_Supplier as sup", "sup.SupplierID", "omat.SupplierID")
                    ->where("omat.OrderUserID", Auth::user()->UserID)
                    ->groupBy("omat.SupplierID", "sup.SupplierNM")
                    ->get()->count();
                if ($ordersall) {
                    $datareturn = [
                        "status" => 2
                    ];
                } else {
                    $datareturn = [
                        "status" => 0,
                        "ErrMsg" => GetMessage::getMessageByID("error001")
                    ];
                }
            }
        }
        return $datareturn;
    }

    /**
     *  「一括発注追加(在庫不足分)」ボタンをクリック時
     * @access public
     * @param null
     * @return array 一括発注一覧
     */
    public function addbulk()
    {
        $datasend = [
            "status" => 0,
            "Msg" => "「一括発注」のデータがありません。"
        ];
        // 資材発注ワークテーブルに、ログインユーザが登録した、「一括発注」で追加したレコードの有無を確認
        $data = W_OrderMaterial::where("OrderUserID", Auth::user()->UserID)->where("BulkAdd", 1)->get()->count();
        // そのようなレコードがある場合
        if ($data) {
            // ポップアップ確認表示
            $datasend = [
                "status" => 1,
                "Msg" => "一括発注のレコードを一旦削除し、新たに全件追加します。<br>良いですか？"
            ];
        } else {
            // 「資材発注ワークテーブル」に追加する
            $this->checkFunc = true;
            $datasend = $this->addbulkOK();
        }
        return $datasend;
    }

    /**
     *  在庫数＋発注済み数が、在庫下限値を下回る資材を抽出し、「資材発注ワークテーブル」に追加する
     * @access private
     * @param 
     * @return array 結果
     */
    public function addbulkOK()
    {
        W_OrderMaterial::where("OrderUserID", Auth::user()->UserID)->where("BulkAdd", 1)->delete();
        $datasend = [];
        // 在庫数＋発注済み数が、在庫下限値を下回る資材を抽出する
        $listInvent = $this->_getInventLimit();
        $dataupdate = [];
        if ($listInvent) {
            //抽出した資材を、「資材発注ワークテーブル」に追加する
            //・「発注数」には、資材マスタで設定している「既定の発注数」をセットする
            //・このレコードを誰が追加したか分かるように、ログインユーザ名をセットする
            //・このレコードを「一括発注」で追加したことが分かるように、フラグを立てる
            foreach ($listInvent as $key => $d) {
                $dataupdate[] = [
                    "MaterialID" => $d->MaterialID,
                    "OrderNum" => $d->DefaultOrder,
                    "OrderUserID" =>  Auth::user()->UserID,
                    "SupplierID" =>  $d->SupplierID,
                    "OrderDate" => $this->_timetoday,
                    "AddDate" => $this->_timetoday,
                    "AddUserID" => Auth::user()->UserID,
                    "TreatOrder" => false,
                    "BulkAdd" => true,
                    "PurUnit" => $this->_getPurUnit($d->MaterialID, $d->SupplierID)
                ];
                if ($key % 100 == 0) {
                    // 「資材発注ワークテーブル」に追加する
                    W_OrderMaterial::insert($dataupdate);
                    $dataupdate = [];
                }
            }
            $datasend = [
                "status" => 1,
                "Msg" => "「一括発注」で追加しました"
            ];
            if ($this->checkFunc)
                $datasend["status"] = 2;
        }
        return $datasend;
    }

    /**
     *  仕入単価
     * @access private
     * @param $MaterialID 資材ID
     * @return float 仕入単価
     */
    private function _getPurUnit($MaterialID, $SupplierID)
    {
        $PurUnit = M_MaterialPrice::select("BuyPrice")
            ->whereDate("ActiveDateFrom", "<=", $this->_today)
            ->where("MaterialID", $MaterialID)
            ->where("SupplierID", $SupplierID)
            ->orderBy("ActiveDateFrom", "DESC")
            ->get()->first();
        if ($PurUnit) $PurUnit = $PurUnit["BuyPrice"];
        else  $PurUnit = 0;
        return $PurUnit;
    }

    /**
     *  資材マスタテーブルに在庫下限値を下回る資材を抽出する
     * @access private
     * @param null
     * @return array 資材マスタ一覧
     */
    private function _getInventLimit()
    {
        $sql = "with a as( 
                    SELECT distinct
                                mmat.MaterialID
                            , mmat.InventLimit
                            , mmat.DefaultOrder
                            , mmat.SupplierID
                            , mmats.StockNum 
                            , CASE  
                                WHEN omatttl.OrderNumTotal  IS NULL THEN '0' 
                                ELSE omatttl.OrderNumTotal 
                                END AS OrderNumTotal  
                        FROM  M_Material mmat 
                            LEFT JOIN M_MaterialShelf mmats 
                                ON mmat.MaterialID = mmats.MaterialID 
                                AND mmats.StoreID = '999' 
                                AND mmats.ShelfID = '999' 
                            LEFT JOIN ( 
                                SELECT
                                    omat.MaterialID
                                    , SUM(omat.OrderNum) as OrderNumTotal 
                                FROM
                                    T_OrderMaterial omat 
                                WHERE
                                    omat.OrderStatusCD = 3
                                GROUP BY
                                    omat.MaterialID
                            ) as omatttl 
                                ON mmat.MaterialID = omatttl.MaterialID
                                )
                        select * from a
                        WHERE  (StockNum + OrderNumTotal) < InventLimit";

        return DB::select($sql);
    }
    /**
     * 資材発注一覧を取る
     * @access public
     * @param bool $pagi true->pagination, false->list
     *          すべてデータを取る
     * @return array 資材発注一覧
     */
    public function _getListOrder($pagicheck = true)
    {

        if ($pagicheck) {
            // 画面表示
            $total = DB::raw("(SELECT omat.MaterialID , SUM(omat.OrderNum) as OrderNumTotal 
            　　　　　 FROM T_OrderMaterial omat WHERE omat.OrderStatusCD = 2 GROUP BY omat.MaterialID) AS total");
            $perpage = 20;
            $listOrder = W_OrderMaterialAsOmat::select(
                "omat.UseMaterialID",
                "omat.MaterialID",
                "mmat.MaterialNM",
                "mmat.Type",
                "sys3.DispText as MaterialClsNM",
                "omat.SupplierID",
                "omat.OrderDate",
                "omat.TreatOrder",
                "omat.OrderStatusCD",
                "sys.DispText as UnitNM",
                "sys2.DispText as UseNM"
            )
                ->selectRaw("REPLACE(cast(cast(omat.OrderNum as DECIMAL(7,1)) as float),'.0','') OrderNum")
                ->selectRaw("REPLACE(cast(cast(omat.PurUnit as DECIMAL(7,1)) as float),'.0','') PurUnit")
                ->selectRaw("REPLACE(cast(cast(mmats.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum")
                ->selectRaw("REPLACE(cast(cast(total.OrderNumTotal as DECIMAL(7,1)) as float),'.0','') OrderNumTotal")
                ->join('M_MaterialShelf as mmats', function ($join) {
                    $join->on('mmats.MaterialID', 'omat.MaterialID');
                    $join->where('mmats.StoreID', '999');
                    $join->where('mmats.ShelfID', '999');
                })
                ->join('M_Material as mmat', function ($join) {
                    $join->on('omat.MaterialID', 'mmat.MaterialID');
                })
                ->leftJoin('M_System as sys', function ($join) {
                    $join->on('sys.InternalValue', 'mmat.OrderUnitCD');
                    $join->where('sys.SystemCD', M_System::$OrderUnitCD);
                })
                ->leftJoin('M_System as sys2', function ($join) {
                    $join->on('sys2.InternalValue', 'mmat.UseUnitCD');
                    $join->where('sys2.SystemCD', M_System::$UseUnitCD);
                })
                ->leftJoin('M_System as sys3', function ($join) {
                    $join->on('sys3.InternalValue', 'mmat.MaterialCls');
                    $join->where('sys3.SystemCD', M_System::$MaterialClsCD);
                })
                ->leftJoin(
                    $total,
                    'total.MaterialID',
                    'omat.MaterialID'
                )
                ->where("omat.OrderUserID", Auth::user()->UserID)
                ->orderBy("omat.OrderDate", 'desc')->paginate($perpage);
        } else {
            // Excel Export
            // 発注先はシートで表示
            $listOrder = [];
            $SupplierID = T_OrderMaterialAsOmat::select("omat.SupplierID", "sup.SupplierNM")
                ->leftJoin("M_Supplier as sup", "sup.SupplierID", "omat.SupplierID")
                ->where("omat.OrderUserID", Auth::user()->UserID)
                ->where("omat.TreatOrder", 1)
                ->groupBy("omat.SupplierID", "sup.SupplierNM")
                ->get();

            foreach ($SupplierID as $k => $s) {
                $s->Date = $this->_today;
                $data = T_OrderMaterialAsOmat::select('omat.UseMaterialID', 'omat.OrderNum', 'omat.MaterialID', 'mmat.MaterialNM', 'mmat.Type', 'sys3.DispText as MaterialClsNM')
                    ->join('M_Material as mmat', function ($join) {
                        $join->on('omat.MaterialID', 'mmat.MaterialID');
                    })
                    ->leftJoin('M_System as sys3', function ($join) {
                        $join->on('sys3.InternalValue', 'mmat.MaterialCls');
                        $join->where('sys3.SystemCD', M_System::$MaterialClsCD);
                    })
                    ->where("omat.OrderUserID", Auth::user()->UserID)
                    ->where("omat.SupplierID", $s->SupplierID)
                    ->where("omat.TreatOrder", 1)->get();
                $listOrder[$k]["sup"] = $s;
                $listOrder[$k]["data"] = $data;
            }
            T_OrderMaterial::where("OrderUserID", Auth::user()->UserID)
                ->where("TreatOrder", 1)->update(["TreatOrder" => 0]);
        }
        return $listOrder;
    }

    /**
     * 資材発注更新
     * @access public
     * @param Request $rq
     * @return array 
     */
    public function updateOrderMaterial(Request $rq)
    {
        $datasend = [
            "status" => 0,
            "Msg" => "「資材発注」のデータがありません。"
        ];
        $old = W_OrderMaterial::where("UseMaterialID", $rq->UseMaterialID)->get()->first();
        if ($old) {
            $data = [];
            if ($rq->Flg = 'TreatOrder') {
                $data = [
                    "TreatOrder" => $old->TreatOrder ? 0 : 1,
                    "PurUnit" => $old->PurUnit
                ];
            }
            if ($rq->filled('SupplierID')) {
                $data = [
                    "SupplierID" => $rq->SupplierID,
                    "PurUnit" => $this->_getPurUnit($old->MaterialID, $rq->SupplierID)
                ];
            }
            if ($data) {
                //T_UseMaterialDetailテーブルに更新
                W_OrderMaterial::where("UseMaterialID", $rq->UseMaterialID)->update($data);
                $datasend = [
                    "status" => 1,
                    "Msg" => "更新が完了しました。",
                    "PurUnit" => $data["PurUnit"]
                ];
            }
        }
        return $datasend;
    }

    /**
     * 在庫数を取る
     * @access private
     * @param string $MaterialID
     * @return object 
     */
    private function _getStockNum($MaterialID)
    {
        $stock = DB::table("M_MaterialShelf")->select("StockNum")
            ->where("StoreID", "999")
            ->where("ShelfID", "999")
            ->where("MaterialID", $MaterialID)
            ->get()->first();
        return $stock;
    }

    /**
     * 削除ボタンをクリックする時、DB更新
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return true
     */
    public function deleteOrderMaterial(Request $request)
    {
        $MaterialID = $request->MaterialID;
        $UseMaterialID = $request->UseMaterialID;
        //T_OrderMaterialテーブルに削除
        W_OrderMaterial::where("MaterialID", $MaterialID)
            ->where("UseMaterialID", $UseMaterialID)
            ->delete();
    }
    /**
     * ファイルから全てメッセージを取る
     * @access public
     * @return array
     */
    // public function getMsgJson(Request $rq)
    // {
    //     return  GetMessage::getMessageByID($rq->id);
    // }
    public function getMsgJson()
    {
        return GetMessage::getListMessage();
    }
}
