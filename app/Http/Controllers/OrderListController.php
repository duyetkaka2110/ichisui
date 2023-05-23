<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_System;
use App\Models\M_Supplier;
use DB;
use Auth;
use Carbon;
use App\GetSetting;
use App\GetMessage;

class OrderListController extends Controller
{
    public $_timetoday;
    public $_today;
    public function __construct()
    {
        date_default_timezone_set('Asia/Tokyo');
        $this->_timetoday = Carbon\Carbon::now()->format("Y/m/d H:i:s");
        $this->_today = Carbon\Carbon::now()->format("Y/m/d");
    }
    public function index(Request $rq)
    {
        // 資材区分
        $listMaterialCls = M_System::select('InternalValue', 'DispText')->where("SystemCD",  M_System::$MaterialClsCD)->get();

        // 発注先
        $listSupplier = M_Supplier::select("SupplierID as InternalValue", "SupplierNM as DispText")->where("DeleteFlg", 0)->get();

        // 状況
        $listOrderStatus = M_System::select('InternalValue', 'DispText')->where("SystemCD",  M_System::$OrderStatusCD)->orderBy("Seq")->get();

        $WorkUserNM = $rq->WorkUserNM;
        $WWID = $rq->WWID;
        $list = $this->_getList($rq);
        $datasearch = $rq->all();
        return view("orderlist.index", compact("list", "listMaterialCls", "listSupplier", "listOrderStatus",  "datasearch"));
    }


    /**
     * 「検品済」ボタンをクリックする
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return json
     */
    public function KenpinDetail(Request $request)
    {
        $data["status"] = 1;
        $data["data"] = $this->_getKenpinDetail($request->UseMaterialID, $request->MaterialID);
        return $data;
    }
    /**
     * 「検品済」ボタンをクリックする時、データを取る
     * @access public
     * @param string $MaterialID 資材ID
     * @param string $UseMaterialID 使用資材ID
     * @return array
     */
    private function _getKenpinDetail($UseMaterialID,  $MaterialID)
    {
        $sql = "SELECT
                    REPLACE(cast(cast(ord.OrderNum as DECIMAL(7,1)) as float),'.0','')  OrderNum
                    , REPLACE(cast(cast(ISNULL(SUM(chd.CheckNum),0) as DECIMAL(7,1)) as float),'.0','') TotalCheck
                    , FORMAT(ord.OrderDate, 'yyyy/MM/dd') AS OrderDate
                    , Us.UserNM
                    , Sup.SupplierNM
                    , sys.DispText as OrderUnitNM
                    , mat.MaterialNM
                FROM
                    T_OrderMaterial ord 
                    INNER JOIN M_User Us 
                    ON Us.UserID = ord.AddUserID 
                    INNER JOIN M_Material mat 
                        ON mat.MaterialID = ord.MaterialID 
                    LEFT JOIN M_System sys 
                        ON mat.OrderUnitCD = sys.InternalValue 
                        AND sys.SystemCD = '000008' 
                    LEFT JOIN M_Supplier Sup 
                    ON Sup.SupplierID = ord.SupplierID 
                    LEFT JOIN T_CheckDetail chd 
                    ON ord.UseMaterialID = chd.UseMaterialID 
                    AND chd.DeleteFlg = 0
                WHERE
                    ord.UseMaterialID =  :UseMaterialID
                    AND ord.MaterialID = :MaterialID 
                GROUP BY
                    ord.OrderNum
                    , ord.OrderDate
                    , sys.DispText
                    , Us.UserNM
                    , Sup.SupplierNM
                    , mat.MaterialNM
                ";

        $dataWhere["UseMaterialID"] = $UseMaterialID;
        $dataWhere["MaterialID"] = $MaterialID;
        $data = DB::select($sql, $dataWhere);
        if ($data) return  $data[0];
    }

    /**
     * 「検品済」スイッチをクリックする
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return array
     */
    public function setCheckDetail(Request $request)
    {
        $UserID = Auth::user()->UserID;
        $flg = true;
        $CheckNumBack = false;
        $ErrMsg = '';
        if (!$request->CheckDate) {
            $flg = false;
            $ErrMsg = GetMessage::getMessageByID("error070");
        } else {

            try {
                (new \Carbon\Carbon($request->CheckDate))->format('Y-m-d');
            } catch (\Exception $e) {
                $flg = false;
                $ErrMsg = GetMessage::getMessageByID("error023") . "<br>";
            }
            $data = $this->_getKenpinDetail($request->UseMaterialID, $request->MaterialID);
            if ($data) {
                // 「全部検品する」ボタンチェック
                if ($request->kenpinoption == "allkenpin") {

                    // 総検品数（＝今回検品数＋既検品数）が、発注数を越えているとエラー
                    if ($data->TotalCheck == $data->OrderNum) {
                        $flg = false;
                        $ErrMsg = GetMessage::getMessageByID("error069");
                    } else {
                        //T_CheckDetailテーブルにデータを追加する
                        $CheckNumInsert =  $data->OrderNum - $data->TotalCheck;
                        $this->_InsertCheckDetail($request, $CheckNumInsert, $UserID);

                        // 発注数＝総検品数ならば、資材発注.発注ステータスを「検品済み」に変更
                        // 検品ON
                        $OrderStatusCD = 4;
                        $this->KenpinZumi($request, $UserID, $OrderStatusCD, $CheckNumInsert, $data->OrderNum, $CheckNumBack);
                        echo true;
                    }
                }
                // 「一部検品する」ボタンチェック
                if ($request->kenpinoption == "1kenpin") {
                    $kenpinoptionnumber = $request->kenpinoptionnumber;
                    // 検品数は：入力必須チェック
                    if (!$kenpinoptionnumber) {
                        $flg = false;
                        $ErrMsg = GetMessage::getMessageByID("error044");
                    }
                    // 発注数がマイナスの時のみ、以下の処理とする
                    if ($data->OrderNum < 0) {
                        if (($kenpinoptionnumber + $data->TotalCheck) < $data->OrderNum) {
                            // 総検品数（＝今回検品数＋検品済数）が、発注数を下回る場合は、エラーメッセージを表示する	
                            $flg = false;
                            $ErrMsg = GetMessage::getMessageByID("error090");
                        }
                        // 今回検品数が、検品済数を上回っている場合は、エラーメッセージを表示する
                        if ($kenpinoptionnumber > $data->TotalCheck) {
                            $flg = false;
                            $ErrMsg = GetMessage::getMessageByID("error091");
                        }
                    } else {
                        // 総検品数（＝今回検品数＋既検品数）が、発注数を越えているとエラー
                        if (($kenpinoptionnumber + $data->TotalCheck) > $data->OrderNum) {
                            $flg = false;
                            $ErrMsg = GetMessage::getMessageByID("error069");
                        }
                        if ($kenpinoptionnumber + $data->TotalCheck < 0) {
                            $flg = false;
                            $ErrMsg = GetMessage::getMessageByID("error071");
                        }
                    }
                    if ($flg) {
                        //T_CheckDetailテーブルにデータを追加する
                        $this->_InsertCheckDetail($request,  $kenpinoptionnumber,  $UserID);

                        // 発注数＝総検品数ならば、資材発注.発注ステータスを「検品済み」に変更
                        if (($kenpinoptionnumber + $data->TotalCheck) == $data->OrderNum) {
                            // 検品済
                            $OrderStatusCD = 4;
                        } else {
                            //検品中
                            $OrderStatusCD = 3;
                        }
                        //発注済を戻る
                        if ($kenpinoptionnumber + $data->TotalCheck == 0) {
                            $OrderStatusCD = 3;
                            $CheckNumBack = true;
                        }
                        //存在ステータスは検品済
                        if ($data->TotalCheck == $data->OrderNum) {
                            $CheckNumBack = true;
                        }

                        $this->KenpinZumi($request, $UserID, $OrderStatusCD, $kenpinoptionnumber, $data->OrderNum, $CheckNumBack);
                        echo true;
                    }
                }
            } else {
                $ErrMsg = GetMessage::getMessageByID("error005");
            }
        }
        if ($ErrMsg) return $ErrMsg;
    }

    /**
     * T_CheckDetailテーブルに追加
     * @access 
     * @param Request $request
     *          すべてデータを取る
     * @param string $CheckNum 検品数
     * @param string $UserID ユーザID
     * @return bool
     */
    private function _InsertCheckDetail(Request $request, string $CheckNum, string $UserID)
    {
        $MaxID = DB::select("SELECT 
                                MAX(CkeckID) + 1 AS MaxCheckID
                            FROM
                                T_CheckDetail");
        //MaxIDがNULL場合、１に設定します
        if (!$MaxID[0]->MaxCheckID) $MaxID[0]->MaxCheckID = 1;
        $dataCheckDetail = array(
            "CkeckID" => $MaxID[0]->MaxCheckID,
            "UseMaterialID" => $request->UseMaterialID,
            "CkeckUserID" => $UserID,
            "CheckDate" => $request->CheckDate,
            "CheckNum" => $CheckNum,
            "DeleteFlg" => 0,
            "AddUserID" => $UserID,
            "AddDate" => $this->_timetoday,
        );
        DB::table("T_CheckDetail")->insert($dataCheckDetail);
    }
    /**
     * 発注数＝総検品数ならば、資材発注.発注ステータスを「検品済み」に変更->検品ON
     * @access private
     * @param Request $request
     *          すべてデータを取る
     * @param object $data 検品情報
     * @param string $UserID ユーザID
     * @param string $OrderStatusCD 発注スタータス
     * @param string $CheckNum 検品数更新
     * @return bool
     */
    private function KenpinZumi(Request $request, string $UserID, string $OrderStatusCD, string $CheckNum, string $OrderNum, bool $CheckNumBack)
    {
        $OldStock = DB::table("M_MaterialShelf")->select("StockNum")
            ->where("MaterialID", $request->MaterialID)
            ->where("StoreID ", '999')
            ->where("ShelfID  ", '999')
            ->get()->first();
        //T_OrderMaterial更新
        $dataOr = array(
            "OrderStatusCD" => $OrderStatusCD,
            "UpdateDate" => $this->_timetoday
        );
        //検品済
        if ($OrderStatusCD == 4) {
            $dataOr["CheckNum"] = $OrderNum;
            $dataOr["CheckDate"] = $this->_today;
        }
        if ($CheckNumBack) {
            $dataOr["CheckNum"] = NULL;
            $dataOr["CheckDate"] = NULL;
        }
        //検品済→「検品中｜発注済」
        if ($OrderStatusCD == 2 || $OrderStatusCD == 4) {
        }
        DB::table("T_OrderMaterial")
            ->where("MaterialID", $request->MaterialID)
            ->where("UseMaterialID", $request->UseMaterialID)
            ->update($dataOr);

        //M_MaterialShelf更新
        $dataMS = array(
            "StockNum" => $OldStock->StockNum + $CheckNum,
            "UpdateUserID" => $UserID,
            "UpdateDate" => $this->_timetoday
        );

        DB::table("M_MaterialShelf")
            ->where("MaterialID", $request->MaterialID)
            ->where("StoreID ", '999')
            ->where("ShelfID  ", '999')
            ->update($dataMS);
    }

    /**
     * 検索条件を取る
     * @access private
     * @param array $request
     *          すべて検索条件
     * @return array 
     */
    private function _getSearch(Request $request)
    {
        $data = $datastr =  $datawork = array();
        // 資材ID
        if ($request->filled("MaterialID")) {
            $data["MaterialID"] =  "%" . $request->MaterialID . "%";
            $datastr[] = " ord.MaterialID LIKE :MaterialID ";
        }
        // 資材区分
        if ($request->filled("MaterialCls")) {
            $data["MaterialCls"] = $request->MaterialCls;
            $datastr[] = " mmat.MaterialCls = :MaterialCls ";
        }
        // 品名
        if ($request->filled("MaterialNM")) {
            $data["MaterialNM"] = "%" . $request->MaterialNM . "%";
            $data["MaterialAlias"] = "%" . $request->MaterialNM . "%";
            $sql_search = " ( mmat.MaterialNM  LIKE :MaterialNM";
            $sql_search .= " OR ";
            $sql_search .= " mmat.MaterialAlias  LIKE :MaterialAlias )";
            $datastr[] = $sql_search;
        }
        // 規格
        if ($request->filled("Type")) {
            $data["Type"] = "%" . $request->Type . "%";
            $datastr[] = " mmat.Type LIKE :Type ";
        }
        // 発注先
        if ($request->filled("SupplierID")) {
            $data["SupplierID"] = $request->SupplierID;
            $datastr[] = " ord.SupplierID  = :SupplierID ";
        }
        // 状況
        if ($request->filled("OrderStatus")) {
            $data["OrderStatus"] = $request->OrderStatus;
            $datastr[] = " ord.OrderStatusCD  = :OrderStatus ";
        } else {
            $datastr[] = " ord.OrderStatusCD  = 3 ";
        }
        //発注日
        if ($request->filled("OrderDateFrom") || $request->filled("OrderDateTo")) {
            //-- 検索条件.受付日:From/Toが両方指定されている場合
            if ($request->filled("OrderDateFrom") && $request->filled("OrderDateTo")) {
                $data["OrderDateFrom"] = $request->OrderDateFrom;
                $data["OrderDateTo"] = $request->OrderDateTo . " 23:59:59";
                $datastr[] = " ord.OrderDate BETWEEN :OrderDateFrom AND :OrderDateTo ";
            }
            //-- 検索条件.受付日:Fromのみ指定されている場合
            if ($request->filled("OrderDateFrom") && !$request->filled("OrderDateTo")) {
                $data["OrderDateFrom"] = $request->OrderDateFrom;
                $datastr[] = " ord.OrderDate >= :OrderDateFrom ";
            }
            //-- 検索条件.受付日:Toのみ指定されている場合
            if (!$request->filled("OrderDateFrom") && $request->filled("OrderDateTo")) {
                $data["OrderDateTo"] = $request->OrderDateTo . " 23:59:59";
                $datastr[] = " ord.OrderDate <= :OrderDateTo ";
            }
        }

        // 検品日
        if ($request->filled("CheckDateFrom") || $request->filled("CheckDateTo")) {
            //-- 検索条件.受付日:From/Toが両方指定されている場合
            if ($request->filled("CheckDateFrom") && $request->filled("CheckDateTo")) {
                $data["CheckDateFrom"] = $request->CheckDateFrom;
                $data["CheckDateTo"] = $request->CheckDateTo . " 23:59:59";
                $datastr[] = " ord.CheckDate BETWEEN :CheckDateFrom AND :CheckDateTo ";
            }
            //-- 検索条件.受付日:Fromのみ指定されている場合
            if ($request->filled("CheckDateFrom") && !$request->filled("CheckDateTo")) {
                $data["CheckDateFrom"] = $request->CheckDateFrom;
                $datastr[] = " ord.CheckDate >= :CheckDateFrom ";
            }
            //-- 検索条件.受付日:Toのみ指定されている場合
            if (!$request->filled("CheckDateFrom") && $request->filled("CheckDateTo")) {
                $data["CheckDateTo"] = $request->CheckDateTo . " 23:59:59";
                $datastr[] = " ord.CheckDate <= :CheckDateTo ";
            }
        }
        $datareturn = [
            "data" => $data,
            "datastr" => null
        ];
        if ($datastr) {
            $datareturn["datastr"] = " WHERE " . implode(" AND ", $datastr);
        }
        return $datareturn;
    }
    /**
     * SQL文
     * @access private
     * @param array $request
     *          SQL文
     * @return string
     */
    private function _getList(Request $rq)
    {

        //検索条件データを取る
        $search = $this->_getSearch($rq);
        $limit = GetSetting::getSettingByID("Tbl_numrow");
        if (!$rq->page) $rq->page = 0;
        else $rq->page = $rq->page - 1;
        $offset = ($rq->page * $limit);
        $sql = $this->_getSQL($offset, $limit, $search, false);
        $count = DB::select($this->_getSQL($offset, $limit, $search, true), $search["data"]);
        $result = DB::select($sql, $search["data"]);
        $myPaginator = new \Illuminate\Pagination\LengthAwarePaginator($result, $count[0]->cnt, $limit);
        return $myPaginator;
    }
    private function _getSQL($offset, $limit, $search, $count = false)
    {
        $sql = "";
        if ($count)
            $sql .= " SELECT count(*) cnt FROM (";
        $sql .= "SELECT
                ord.MaterialID
                , ord.UseMaterialID
                , mmat.MaterialImg
                , mmat.MaterialNM
                , mmat.MaterialAlias
                , bg.DispText as MaterialClsNM
                , bg.FreeText1 as bcolor
                , mmat.Type
                , sup.SupplierNM
                , ordst.DispText AS OrdSts
                , sys.DispText as UnitNM
                , REPLACE(cast(cast(ord.PurUnit as DECIMAL(7,1)) as float),'.0','') Price 
                , REPLACE(cast(cast(ord.OrderNum as DECIMAL(7,1)) as float),'.0','') OrderNum 
                , REPLACE(cast(cast(SUM(chd.CheckNum) as DECIMAL(7,1)) as float),'.0','') CheckNum
                , FORMAT(ord.OrderDate, 'yyyy/MM/dd') AS OrderDate
                , FORMAT(mcd.MaxDate, 'yyyy/MM/dd') AS CheckDate
                , muse.UserNM
                , CASE 
                    WHEN ord.OrderDate IS NULL 
                    THEN '' 
                    ELSE ' checked ' 
                    END AS CompOrderFlg
                , CASE 
                    WHEN ord.CheckDate IS NULL 
                    THEN '' 
                    ELSE ' checked ' 
                    END AS CompCheckFlg
                , ord.OrderStatusCD
                FROM
                T_OrderMaterial ord 
                INNER JOIN M_Material mmat 
                    ON ord.MaterialID = mmat.MaterialID 
                LEFT JOIN M_System ordst 
                    ON ord.OrderStatusCD = ordst.InternalValue 
                    AND ordst.SystemCD = '000002' 
                LEFT JOIN M_User muse 
                    ON ord.OrderUserID = muse.UserID 
                LEFT JOIN M_Supplier sup 
                    ON sup.SupplierID = ord.SupplierID 
                LEFT JOIN M_System as sys 
                    ON mmat.OrderUnitCD = sys.InternalValue 
                    AND sys.SystemCD = '000008' 
                LEFT JOIN M_System as bg 
                    ON mmat.MaterialCls = bg.InternalValue 
                    AND bg.SystemCD = '000025' 
                LEFT JOIN T_CheckDetail chd 
                    ON ord.UseMaterialID = chd.UseMaterialID 
                    AND chd.DeleteFlg = 0 
                LEFT JOIN ( 
                    SELECT
                    UseMaterialID
                    , MAX(CheckDate) as MaxDate 
                    FROM
                    T_CheckDetail chdd 
                    GROUP BY
                    UseMaterialID
                ) mcd 
                    ON mcd.UseMaterialID = ord.UseMaterialID ";
        $sql .= $search["datastr"];
        $sql .= "
                GROUP BY
                ord.MaterialID
                , ord.UseMaterialID
                , mmat.MaterialNM
                , mmat.MaterialImg
                , mmat.MaterialAlias
                , sup.SupplierNM
                , ord.PurUnit
                , sys.DispText 
                , bg.DispText
                , bg.FreeText1
                , ordst.DispText
                , ord.CheckDate
                , mcd.MaxDate
                , mmat.Type
                , OrderNum
                , OrderDate
                , muse.UserNM
                , ord.OrderStatusCD ";
        if ($count)
            $sql .= " ) x";
        else {
            $sql .= " 
            ORDER BY 
                OrderDate DESC
                , ord.MaterialID   ";
            if ($limit)
                $sql .= "OFFSET " . $offset . " ROWS FETCH NEXT " . $limit . " ROWS ONLY ";
        }
        return $sql;
    }
    /**
     * 「検品数」をクリックする時、検品履歴画面が表示される
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return json
     */
    public function getCheckedHistory(Request $request)
    {
        $html =   $title = '';
        if ($request->MaterialID) {
            $mater = DB::table("M_Material")->select("MaterialNM", "Type")
                ->where("MaterialID", $request->MaterialID)
                ->where("DeleteFlg", 0)
                ->get()->first();
            if ($mater) {
                $title = $mater->MaterialNM . " " . $mater->Type;
            }
        }
        if ($request->UseMaterialID) {
            $sql = "SELECT
                        sup.SupplierNM
                        , FORMAT(ord.OrderDate, 'yyyy/MM/dd') AS OrderDate
                        , REPLACE ( 
                        cast(cast(ord.OrderNum as DECIMAL (7, 1)) as float)
                        , '.0'
                        , ''
                        ) OrderNum
                        , REPLACE ( 
                        cast(cast(ord.PurUnit as DECIMAL (7, 1)) as float)
                        , '.0'
                        , ''
                        ) Price
                        , REPLACE ( 
                        cast(cast(chd.CheckNum as DECIMAL (7, 1)) as float)
                        , '.0'
                        , ''
                        ) CheckNum
                        , FORMAT(chd.CheckDate, 'yyyy/MM/dd') AS CheckDate 
                    FROM
                        T_CheckDetail chd 
                        INNER JOIN T_OrderMaterial ord 
                        ON ord.UseMaterialID = chd.UseMaterialID 
                        LEFT JOIN M_Supplier sup 
                        ON sup.SupplierID = ord.SupplierID 
                    WHERE
                        chd.UseMaterialID = :UseMaterialID";
            $dataWhere = array("UseMaterialID" => $request->UseMaterialID);
            $list = DB::select($sql, $dataWhere);
            $html = '';
            if ($list) {
                foreach ($list as $data)
                    $html .= '<tr>
                                <td>' . $data->SupplierNM . '</td>
                                <td>' . number_format($data->Price) .  ($data->Price  ? '円' : '') . '</td>
                                <td>' . $data->OrderDate . '</td>
                                <td>' . $data->OrderNum . '</td>
                                <td>' . $data->CheckDate . '</td>
                                <td>' . $data->CheckNum . '</td>
                            </tr>';
            } else {
                $html .= '<tr><td colspan="6">' . GetMessage::getMessageByID("error001") . '</td></tr>';
            }
        }
        $datasend = array(
            "status" => 1,
            "title" => $title,
            "html" => $html
        );
        return $datasend;
    }
}
