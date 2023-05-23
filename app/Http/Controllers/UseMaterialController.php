<?php

/** 使用資材入力 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\M_System;
use App\Models\T_UseMaterial;
use App\Models\T_UseMaterialDetail;

class UseMaterialController extends Controller
{
    protected $_SystemTable = "M_System";
    protected $_WaterWork = "T_WaterWork";
    protected $_MaterialTable = "M_Material as M";
    protected $_StockDetail = "T_StockDetail";
    protected $_UseMaterial = "T_UseMaterial";
    protected $_UseMaterialtest = "T_UseMateiral_test";
    protected $_UseMaterialDetail = "T_UseMaterialDetail";

    public function Usematerialsearch(Request $rq)
    {
        //フォームに入力された検索条件を変数に格納
        //WWRecIDからWWIDを取る
        $WWID = DB::table($this->_WaterWork)->where("WWRecID", $rq->WWID)->value("WWID");

        //最初作業
        $SchedTime = $rq->SchedTime;
        $WorkType = $rq->WorkType;
        $searchID = $rq->searchID;
        $searchUnitListCD = $rq->searchUnitListCD;
        $searchMaterialCls = $rq->searchMaterialCls;
        $searchMaterialNM = $rq->searchMaterialNM;
        $searchType = $rq->searchType;

        $ListMaterial = "";
        $ListUseMaterial = "";
        $serachConditions[] = "";


        //資材区分
        $ListMaterialCls = SystemController::getSystemByCD(M_System::$UseMaterialClsCD);

        //編集状態
        $ListOrderMaterial = DB::table($this->_UseMaterialDetail)
            ->select("T_UseMaterialDetail.MaterialID", "T_UseMaterialDetail.CustomMaterialUnit",  "T_UseMaterialDetail.MaterialNM", "T_UseMaterialDetail.Type", "T_UseMaterialDetail.UseNum", "T_UseMaterialDetail.LossNum", "T_UseMaterialDetail.SellPrice", "T_UseMaterialDetail.BuyPrice", "T_UseMaterialDetail.CustomMaterialBuyNum as BuyNumber")
            ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.UseNum as DECIMAL(7,1)) as float),'.0','') UseNum")
            ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.LossNum as DECIMAL(7,1)) as float),'.0','') LossNum")
            ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
            ->where("T_UseMaterial.WWID", $WWID)
            ->where("T_UseMaterialDetail.MaterialID", '999999')->get();

        $ListUseMaterial = DB::table($this->_UseMaterialDetail)
            ->select("T_UseMaterialDetail.MaterialID",  "M.MaterialNM", "M.Type", "T_UseMaterialDetail.UseNum", "T_UseMaterialDetail.LossNum", "T_UseMaterialDetail.SellPrice", "T_UseMaterialDetail.BuyPrice", "T_UseMaterialDetail.CustomMaterialBuyNum as BuyNumber")
            ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.UseNum as DECIMAL(7,1)) as float),'.0','') UseNum")
            ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.LossNum as DECIMAL(7,1)) as float),'.0','') LossNum")
            ->selectRaw("REPLACE(cast(cast(mmats.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum")
            ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
            ->join($this->_MaterialTable, "M.MaterialID", "T_UseMaterialDetail.MaterialID")
            ->join('M_MaterialShelf as mmats', function ($join) {
                $join->on('mmats.MaterialID', 'M.MaterialID');
                $join->where('mmats.StoreID', '999');
                $join->where('mmats.ShelfID', '999');
            })
            ->where("WWID", $WWID)->get();

        //検索ボタン押下時
        if ($rq->btn == "search") {
            //SQL分の作成
            $ListMaterial = DB::table($this->_MaterialTable)
                ->select('M.MaterialID', 'M.MaterialUnitPriceListCD', 'M.MaterialCls', 'M.DeleteFlg', 'M.MaterialNM', 'M.Type', 'mmats.StockNum', 'sys.DispText as MaterialClsNM', 'use.DispText as UseUnitNM', 'loss.DispText as LossUnitNM', 'MP.BuyPrice', 'MP.SellPrice')
                ->selectRaw("REPLACE(cast(cast(mmats.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum")
                ->join('M_MaterialShelf as mmats', function ($join) {
                    $join->on('mmats.MaterialID', 'M.MaterialID');
                    $join->where('mmats.StoreID', '999');
                    $join->where('mmats.ShelfID', '999');
                })
                ->join('M_MaterialPrice as MP', function ($join) use ($WWID) {
                    $join->on('MP.MaterialID', 'M.MaterialID');
                    $join->where('MP.DeleteFlg', '0');
                    $join->where('MP.ActiveDateFrom',  DB::raw("(SELECT TOP 1 ActiveDateFrom
                                                                        FROM [M_MaterialPrice] as [MPP] 
                                                                            WHERE [MPP].[DeleteFlg] = 0 
                                                                            AND MPP.MaterialID = M.MaterialID
                                                                            and [MPP].[ActiveDateFrom] <= ( 
                                                                                SELECT
                                                                                    MIN(W.WorkFrom) 
                                                                                FROM
                                                                                    T_Work W 
                                                                                WHERE
                                                                                    W.WWID = " . $WWID . "
                                                                            ) 
                                                                            ORDER BY [MPP].[ActiveDateFrom] DESC)"));
                })
                ->join('M_System as sys', function ($join) {
                    $join->on('sys.InternalValue', 'M.MaterialCls');
                    $join->where('sys.SystemCD', '000025');
                })
                ->join('M_System as use', function ($join) {
                    $join->on('use.InternalValue', 'M.UseUnitCD');
                    $join->where('use.SystemCD', '000006');
                })
                ->join('M_System as loss', function ($join) {
                    $join->on('loss.InternalValue', 'M.LossUnitCD');
                    $join->where('loss.SystemCD', '000007');
                })
                ->orderBy('M.MaterialID', 'asc')
                ->where('M.DeleteFlg', "0");

            //IDの検索条件
            if ($searchID) $ListMaterial = $ListMaterial->where('M.MaterialID', 'LIKE', '%' . $searchID . "%");
            //単価表CDの検索条件
            if ($searchUnitListCD) $ListMaterial = $ListMaterial->where('M.MaterialUnitPriceListCD', 'LIKE', '%' . $searchUnitListCD . '%');
            //資材区分の検索条件
            if ($searchMaterialCls) $ListMaterial = $ListMaterial->where('M.MaterialCls', $searchMaterialCls);
            //品名の検索条件
            if ($searchMaterialNM) {
                $searchMaterialNMs = explode(" ", $searchMaterialNM);
                foreach ($searchMaterialNMs as $k => $v) {
                    $ListMaterial = $ListMaterial->where('M.MaterialNM', 'LIKE', '%' . $searchMaterialNMs[$k] . '%');
                }
            }
            if ($searchType) $ListMaterial = $ListMaterial->where('M.Type', 'LIKE', '%' . $searchType . '%');
            $ListMaterial = $ListMaterial->paginate(10);
        }
        $Material = [
            "WWID" => $rq->WWID,
            "ListUseMaterial" => $ListUseMaterial,
            "ListOrderMaterial" => $ListOrderMaterial,
            "ListMaterialCls" => $ListMaterialCls,
            "Materials" => $ListMaterial,
            "searchID" => $searchID,
            "searchUnitListCD" => $searchUnitListCD,
            "searchMaterialCls" => $searchMaterialCls,
            "searchMaterialNM" => $searchMaterialNM,
            "searchType" => $searchType
        ];
        //検索された資材をビューに返す
        return view("usematerial.usematerial", $Material);
    }

    public function getListMaterial(Request $rq)
    {
        //WWRecIDからWWIDを取る
        $WWID = DB::table($this->_WaterWork)->where("WWRecID", $rq->WWID)->value("WWID");
        $searchID = $rq->searchID;
        $searchUnitListCD = $rq->searchUnitListCD;
        $searchMaterialCls = $rq->searchMaterialCls;
        $searchMaterialNM = $rq->searchMaterialNM;
        $searchType = $rq->searchType;
        $ListMaterial = "";
        $ListUseMaterial = "";
        $serachConditions[] = "";
        //検索ボタン押下時
        //SQL分の作成
        $ListMaterial = DB::table($this->_MaterialTable)
            ->select('M.MaterialID', "MP.ActiveDateFrom", 'M.MaterialUnitPriceListCD', 'M.MaterialCls', 'M.DeleteFlg', 'M.MaterialNM', 'M.Type', 'sys.DispText as MaterialClsNM', 'use.DispText as UseUnitNM', 'loss.DispText as LossUnitNM', 'MP.BuyPrice', 'MP.SellPrice')
            ->selectRaw("REPLACE(cast(cast(mmats.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum")
            ->join('M_MaterialShelf as mmats', function ($join) {
                $join->on('mmats.MaterialID', 'M.MaterialID');
                $join->where('mmats.StoreID', '999');
                $join->where('mmats.ShelfID', '999');
            })
            ->join('M_MaterialPrice as MP', function ($join) use ($WWID) {
                $join->on('MP.MaterialID', 'M.MaterialID');
                $join->where('MP.DeleteFlg', '0');
                $join->where('MP.ActiveDateFrom',  DB::raw("(SELECT TOP 1 ActiveDateFrom
                                                                    FROM [M_MaterialPrice] as [MPP] 
                                                                        WHERE [MPP].[DeleteFlg] = 0 
                                                                        AND MPP.MaterialID = M.MaterialID
                                                                        and [MPP].[ActiveDateFrom] <= ( 
                                                                            SELECT
                                                                                MIN(W.WorkFrom) 
                                                                            FROM
                                                                                T_Work W 
                                                                            WHERE
                                                                                W.WWID = " . $WWID . "
                                                                        ) 
                                                                        ORDER BY [MPP].[ActiveDateFrom] DESC)"));
            })
            ->join('M_System as sys', function ($join) {
                $join->on('sys.InternalValue', 'M.MaterialCls');
                $join->where('sys.SystemCD', '000025');
            })
            ->join('M_System as use', function ($join) {
                $join->on('use.InternalValue', 'M.UseUnitCD');
                $join->where('use.SystemCD', '000006');
            })
            ->join('M_System as loss', function ($join) {
                $join->on('loss.InternalValue', 'M.LossUnitCD');
                $join->where('loss.SystemCD', '000007');
            })
            ->orderBy('M.MaterialID', 'asc')
            ->where('M.DeleteFlg', "0");

        //IDの検索条件
        if ($searchID) $ListMaterial = $ListMaterial->where('M.MaterialID', 'LIKE', '%' . $searchID . "%");
        //単価表CDの検索条件
        if ($searchUnitListCD) $ListMaterial = $ListMaterial->where('M.MaterialUnitPriceListCD', 'LIKE', '%' . $searchUnitListCD . '%');
        //資材区分の検索条件
        if ($searchMaterialCls) $ListMaterial = $ListMaterial->where('M.MaterialCls', $searchMaterialCls);
        //品名の検索条件
        if ($searchMaterialNM) {
            $searchMaterialNMs = explode(" ", $searchMaterialNM);
            foreach ($searchMaterialNMs as $k => $v) {
                $ListMaterial = $ListMaterial->where('M.MaterialNM', 'LIKE', '%' . $searchMaterialNMs[$k] . '%');
            }
        }
        if ($searchType) $ListMaterial = $ListMaterial->where('M.Type', 'LIKE', '%' . $searchType . '%');
        $ListMaterial = $ListMaterial->paginate(15)->toArray();
        $page = "";
        foreach ($ListMaterial["links"] as $val) {
            $p = 0;
            if ($val["url"])
                $p = explode("=", $val["url"])[1];
            $active =  "";
            if ($val["active"]) $active = " active ";
            $page .= '<li class="page-item ' . $active . '"><span class="page-link" page="' . $p . '" >' . $val["label"] . '</span></li>';
        }
        $html = "";
        $count = 0;
        foreach ($ListMaterial["data"] as $val) {
            $html .= '<tr class="tr-' . $count . ' Material">
                            <td>
                                ' . $val->MaterialID . '
                                <input type="hidden" class="form-control text" name="MaterialID" value="' . $val->MaterialID . '" disabled="" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                <input type="hidden" class="form-control text" name="SellPrice" value="' . $val->SellPrice . '" disabled="" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                <input type="hidden" class="form-control text" name="BuyPrice" value="' . $val->BuyPrice . '" disabled="" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            </td>
                            <td>
                            ' . $val->MaterialUnitPriceListCD . '
                            </td>
                            <td>
                            ' . $val->MaterialClsNM . '
                            </td>
                            <td>
                            ' . $val->MaterialNM . '
                                <input type="hidden" class="form-control text" name="MaterialNM" value="' . $val->MaterialNM . '" disabled="" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            </td>
                            <td>
                                <span class="input-group input-group-sm Type">
                                ' . $val->Type . '
                                    <input type="hidden" class="form-control text" name="Type" value="' . $val->Type . '" disabled="" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm">
                                ' . $val->StockNum . '
                                    <input type="hidden" class="form-control text" name="StockNum" value="' . $val->StockNum . '" disabled="">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm">
                                    <input type="text" inputmode="none" class="inputKeypadWithDot form-control text input number useNum masterUse" value="0" min="0" step="0.1" name="Use"  aria-describedby="inputGroup-sizing-sm">
                                    <span class="unit">' . $val->UseUnitNM . '</span>
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm">
                                    <input type="text" inputmode="none" class="inputKeypadWithDot form-control text input number LossNum masterLoss" value="0" min="0" step="0.1" name="Loss" aria-describedby="inputGroup-sizing-sm">
                                    <span class="unit">' . $val->LossUnitNM . '</span>
                                </span>
                            </td>
                            <td><i class="fa fa-cart-plus MaterialAdd" aria-hidden="true"></i></td>
                        </tr>';
            $count++;
        }
        $datareturn = [
            "status" => 1,
            "page" => $page,
            "html" => $html
        ];
        return $datareturn;
    }
    public function Usematerialinsert(Request $rq)
    {
        if ($rq->btn == "back") {
            return redirect()->route("matterinput", $parameters = ["idww" => $rq->WWID, 'flgSagyo' => 1], $status = 302, $headers = []);
        }
        //WWRecIDからWWIDを取る
        $WWID = DB::table($this->_WaterWork)->where("WWRecID", $rq->WWID)->value("WWID");
        // 材料費の更新前
        $SubTotalOld = $this->getSubTotal($WWID);
        $oldUseM = T_UseMaterial::select("T_UseMaterialDetail.UseMaterialID", "T_UseMaterialDetail.MaterialID", "T_UseMaterialDetail.UseNum", "T_UseMaterialDetail.LossNum")
            ->where('T_UseMaterial.WWID', $WWID)
            ->join($this->_UseMaterialDetail, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
            ->get();
        $UseMaterials = $UseMaterialIDs = [];
        $today = date("Y-m-d H:i:s");
        if ($rq->selectMaterialID) {
            $UMaterialKey = array_keys($rq->selectMaterialID);
            foreach ($UMaterialKey as $k => $value) {
                if (strpos($value, 'Order') === false) {
                    $UseStockNum = $rq->StockNum[$value] - ($rq->selectUse[$value] + $rq->selectLoss[$value]);
                } else {
                    $UseStockNum = "";
                }

                //選択資材を配列に格納
                $UseMaterialDateil = [
                    "MaterialID" => $rq->selectMaterialID[$value],
                    "MaterialNM" => $rq->selectMaterialNM[$value],
                    "Type" => $rq->selectType[$value],
                    "InputDate" => $today,
                    "NewFlg" => "0",
                    "AutoOrderFlg" => "0",
                    "UseNum" => $rq->selectUse[$value],
                    "LossNum" => $rq->selectLoss[$value],
                    "BuyPrice" => $rq->selectBuyPrice[$value],
                    "CustomMaterialBuyNum" => isset($rq->selectBuyNumber[$value]) ? $rq->selectBuyNumber[$value] : "",
                    "SellPrice" => isset($rq->selectSellPrice[$value]) ? $rq->selectSellPrice[$value] : "",
                ];

                if (isset($rq->selectCustomMaterialUnit[$value])) {
                    $UseMaterialDateil["CustomMaterialUnit"] = ($rq->selectCustomMaterialUnit[$value]);
                }

                $UseMaterialDateil["AddUserID"] =  Auth::user()->UserID;
                $UseMaterialDateil["AddDate"] =  date("Y-m-d H:i:s");
                $UseMaterialDateil["UpdateUserID"] =  Auth::user()->UserID;
                $UseMaterialDateil["UpdateDate"] =  date("Y-m-d H:i:s");
                $UseMaterialID = T_UseMaterialDetail::insertGetId($UseMaterialDateil);

                $UseMaterials[] = [
                    "UseMaterialID" => $UseMaterialID,
                    "WWID" => $WWID,
                    "ConstructDate" => $today,
                    "AddUserID" => Auth::user()->UserID,
                    "AddDate" =>  date("Y-m-d H:i:s"),
                    "UpdateUserID" =>  Auth::user()->UserID,
                    "UpdateDate" =>  date("Y-m-d H:i:s")
                ];

                DB::table('M_MaterialShelf')
                    ->where('MaterialID', $rq->selectMaterialID[$value])
                    ->where('StoreID', '999')
                    ->where('ShelfID', '999')
                    ->update(['StockNum' => $UseStockNum]);
            }
        }
        //DB削除
        foreach ($oldUseM as $k => $value) {
            $StockNum = DB::table('M_MaterialShelf')
                ->where('MaterialID', $value->MaterialID)
                ->where('StoreID', '999')
                ->where('ShelfID', '999')->get()->first();
            if (strpos($value, 'Order') === false && isset($StockNum->StockNum)) {
                $Stock =  $StockNum->StockNum + ($value->UseNum + $value->LossNum);
            } else {
                $Stock = "";
            }
            DB::table('M_MaterialShelf')
                ->where('MaterialID', $value->MaterialID)
                ->where('StoreID', '999')
                ->where('ShelfID', '999')
                ->update(['StockNum' => $Stock]);
            // T_UseMaterialDetail削除
            $UseMaterialIDs[] = $value->UseMaterialID;
        }
        T_UseMaterialDetail::whereIn("UseMaterialID", $UseMaterialIDs)->delete();
        T_UseMaterial::where('WWID', $WWID)->delete();
        if ($UseMaterials) T_UseMaterial::insert($UseMaterials);

        // 材料費の更新後
        $SubTotal = $this->getSubTotal($WWID);
        $flgDis = 0;
        // 出精値引きの自動計算を行う
        if ($SubTotal != $SubTotalOld) {
            $flgDis = 1;
        }
        return redirect()->route("matterinput", $parameters = ["idww" =>  $rq->WWID, 'flgDis' => $flgDis], $status = 302, $headers = []);
    }

    public function getSubTotal($WWID)
    {
        return DB::table($this->_UseMaterialDetail)
            ->selectRaw("SUM(SellPrice*UseNum) as SubTotal")
            ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
            ->where("WWID", $WWID)->get();
    }
}
