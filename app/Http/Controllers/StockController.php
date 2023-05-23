<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Carbon;
use App\Imports\StockImport;
use App\GetMessage;
use App\GetSetting;
use App\Exports\StockExportStyling;
use Excel;
use Storage;
use App\Models\M_System;
use App\Models\T_Stock;
use Auth;

class StockController extends Controller
{

    public $_timetoday;
    public $_today;
    public function __construct()
    {
        date_default_timezone_set('Asia/Tokyo');
        $this->_timetoday = Carbon\Carbon::now()->format("Y/m/d H:i:s");
        $this->_today = Carbon\Carbon::now()->format("Y/m/d");
    }

    /**
     * 棚卸一覧画面表示 
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return view画面
     */
    public function index(Request $request)
    {
        //  棚卸年月取得一覧表示される
        $ListStockYM = DB::select("SELECT
                                        LEFT(stc.StockYM, 4) + '/' + SUBSTRING(stc.StockYM ,5 ,2) AS StockYM
                                    , stc.StockID
                                    FROM
                                    T_Stock stc
                                    ORDER BY
                                    stc.StockID DESC");
        //棚卸新規作成: MaxStockID
        $MaxStockID = DB::select("SELECT 
                                    MAX(StockID) + 1 AS MaxStockID
                                FROM
                                    T_Stock")[0]->MaxStockID;
        //Disabled 棚卸新規作成ボタン
        $disabledNew = "";
        $ym_now = Carbon\Carbon::today()->format("Ym");
        if (DB::table("T_Stock")->where("StockYM", $ym_now)->get()->first()) {
            $disabledNew = " disabled ";
        }
        $datasend =   array(
            "ListStockYM" => $ListStockYM,
            "MaxStockID" => $MaxStockID,
            "disabledNew" => $disabledNew,
            "listMaterialCls" => M_System::select('InternalValue', 'DispText')->where("SystemCD",  M_System::$MaterialClsCD)->get()
        );
        //資材一覧画面  
        return view("stock.index", $datasend);
    }

    /**
     * 「Excel読込」をクリックする時、ExcelからDB更新
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return NULL
     */
    public function importStock(Request $request)
    {
        if ($request->hasFile('FileImport') && $request->TxtStockID) {
            $StockID = $request->TxtStockID;
            Session::put("StockID", $StockID);
            $import = new StockImport();
            Excel::import($import, request()->file('FileImport'));
            $data = $import->data;
            $StockYM = substr(str_replace("/", "", $data[0][3]), 0, 6);
            $stock = DB::table("T_Stock")->where("StockID", $StockID)
                ->where("StockYM", $StockYM)->get()->first();
            if ($stock) {
                // ①テンポラリテーブルを作成
                DB::unprepared('CREATE TABLE #TmpStock (
                                StockID int not null                                 
                            , StoreID nvarchar(3) COLLATE Japanese_CI_AS not null    
                            , ShelfID nvarchar(3) COLLATE Japanese_CI_AS not null    
                            , MaterialID nvarchar(6) COLLATE Japanese_CI_AS not null 
                            , StockDate datetime                                   
                            , RealStockNum decimal(7, 1)                                     
                            , AddUserID nvarchar(10) COLLATE Japanese_CI_AS          
                            , UpdateUserID nvarchar(10) COLLATE Japanese_CI_AS       
                            )');
                $UserID = Session::get("UserID");

                // ②テンポラリテーブルにExcelファイルの取込データをINSERT
                for ($i = 4; $i < (count($data) - 3); $i++) {
                    //Excelの棚卸日がNULLの場合は、取込を行わない。
                    if ($data[$i][0]) {
                        // In Excel dates are not the same as in other software/programming languages.
                        // Excel start 1900-0101. UNIX start 1970-01-01
                        if (is_numeric($data[$i][1]))
                            $data[$i][1] = date("Y/m/d", ($data[$i][1] - 25569) * 86400);
                        if ($data[$i][2] &&  $this->isDate($data[$i][1])) {
                            for ($j = 6; $j < (count($data[$i]) - 1); $j++) {
                                if ($data[$i][$j] && is_numeric($data[$i][$j])) {
                                    $storeShelfID = explode(",", $data[3][$j]);
                                    $dataInsert =  array(
                                        "StockID" => $StockID,
                                        "StoreID" => $storeShelfID[0],
                                        "ShelfID" => $storeShelfID[1],
                                        "MaterialID" => $data[$i][2],
                                        "StockDate" => $data[$i][1],
                                        "RealStockNum" => $data[$i][$j],
                                        "AddUserID" => $UserID,
                                        "UpdateUserID" => $UserID
                                    );
                                    DB::statement("INSERT 
                                                    INTO #TmpStock(
                                                        StockID      
                                                    , StoreID       
                                                    , ShelfID       
                                                    , MaterialID    
                                                    , StockDate     
                                                    , RealStockNum 
                                                    , AddUserID     
                                                    , UpdateUserID  
                                                    )
                                                    VALUES (
                                                        :StockID      
                                                    , :StoreID       
                                                    , :ShelfID       
                                                    , :MaterialID    
                                                    , :StockDate     
                                                    , :RealStockNum 
                                                    , :AddUserID     
                                                    , :UpdateUserID 
                                                    )", $dataInsert);
                                }
                            }
                        }
                    }
                }
                //③ストアドプロシージャ「dbo.ImportStock」を実行
                DB::statement(" DECLARE @RetCode nvarchar(500)
                            EXECUTE " . env("DB_DATABASE") . ".dbo.ImportStock @RetCode OUTPUT ");
                //④テンポラリテーブルを削除
                DB::statement("DROP TABLE #TmpStock");
                return redirect()->back();
            } else {
                return redirect()->back()->with(array("error" =>  GetMessage::getMessageByID("error013")));
            }
        } else {
            return redirect()->back()->with(array("error" =>  GetMessage::getMessageByID("error013")));
        }
    }

    /**
     * Date Check 
     * @access private
     * @param string $value
     * @return bool
     */
    private function isDate($value)
    {
        if (!$value) {
            return false;
        }
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * 新規棚卸データ作成
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return 棚卸一覧
     */
    public function createNewStock(Request $request)
    {
        $ym_now = Carbon\Carbon::today()->format("Ym");
        if (!DB::table("T_Stock")->where("StockYM", $ym_now)->get()->first()) {
            $UserID = Session::get("UserID");
            $data = array(
                "StockID" => $request->MaxStockID,
                "StockYM" => $ym_now,
                "CloseFlg" => False,
                "AmountAllFixDate" => null,
                "AddUserID" => $UserID,
                "AddDate" => $this->_timetoday,
                "UpdateUserID" => $UserID,
                "UpdateDate" => $this->_timetoday
            );
            //T_Stockテーブルに追加
            DB::table("T_Stock")->insert($data);
            //T_StockDetailテーブルに追加
            DB::statement("INSERT 
                        INTO T_StockDetail( 
                        StockID
                        , StoreID
                        , ShelfID
                        , MaterialID
                        , StockDate
                        , StockNum
                        , RealStockNum
                        , AddUserID
                        , AddDate
                        , UpdateUserID
                        , UpdateDate
                        ) 
                        SELECT
                        '" . $request->MaxStockID . "'
                        , mmsh.StoreID
                        , mmsh.ShelfID
                        , mmsh.MaterialID
                        , NULL
                        , mmsh2.StockNum
                        , 0
                        , '" . $UserID . "'
                        , '" . $this->_timetoday . "'
                        , '" . $UserID . "'
                        , '" . $this->_timetoday . "' 
                        FROM
                        　M_MaterialShelf mmsh
                        LEFT JOIN (
                              SELECT *
                              FROM M_MaterialShelf mmsh3
                            WHERE
                            mmsh3.StoreID = '999'
                            AND
                            mmsh3.ShelfID = '999') as mmsh2
                          ON mmsh2.MaterialID = mmsh.MaterialID
                        WHERE
                          mmsh.StoreID != '999'
                        AND
                          mmsh.ShelfID != '999'
                        ");
            Session::put("StockID", $request->MaxStockID);
        }
        return redirect()->back()->with(array("msg" => GetMessage::getMessageByID("info013")));
    }
    /**
     * 棚卸一覧画面表示はAjaxで使う
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return htmlテキスト
     */
    public function renderListStock(Request $request)
    {
        if (!$request->Page) $request->Page = 0;
        else $request->Page = $request->Page - 1;
        $dataWhere = $this->_getDataWhere($request);
        $list = $this->_getListStock($request, $dataWhere);

        $html = "";
        $no = $request->Page * GetSetting::getSettingByID("Tbl_numrow");
        if ($list) {
            foreach ($list as $mater) {
                $bg = "";
                if ($mater->FreeText1) {
                    $bg = ' style="background: ' . $mater->FreeText1 . '" ';
                }
                if (!$mater->MaterialImg)
                    $mater->MaterialImg = Storage::url("Material/no-image.jpg");
                $no++;
                $html .= '<tr class="tr ' . $mater->MaterialID . $mater->StockID . '"  txtpage="' . $request->Page . '">
                            <td class="text-center">' . $no . '</td>
                            <td>' . $mater->MaterialID . '</td>
                                
                                <td class="t2">
                                ' . $mater->MaterialNM . '
                                </td>
                            <td>' . $mater->Type . '</td>
                            <td class="text-right">' . $mater->StockNum . '</td>
                            <td class="text-center"><a class="pop" data-update="StockID=' . $mater->StockID . '&MaterialID=' . $mater->MaterialID . '&StockDate=' . $mater->StockDate . '" href="#" title="" >' . $mater->RealStockNum . '</a></td>
                            <td class="text-right">' . $mater->StockDate . '</td>
                        </tr>';
            }
        } else {
            if ($request->Page == 0)
                $html .= "<tr class='no-data'><td class='text-center' colspan='9'>" . GetMessage::getMessageByID("error001") . "</td></tr>";
        }

        $data = array(
            "html" => $html
        );
        $count = $this->_getCountList($request,  $dataWhere);
        $data["CountResult"] = $count;
        $data["CloseFlg"] = "";
        $data["AmountAllFixDate"] = "";
        $stock = $this->_getStockYM($request);
        if ($stock) {
            $data["CloseFlg"] = $stock->CloseFlg;
            $data["AmountAllFixDate"] = $stock->AmountAllFixDate;
        }

        $limit = GetSetting::getSettingByID("Tbl_numrow");
        $myPaginator = new \Illuminate\Pagination\LengthAwarePaginator(null, $count, $limit, $request->Page + 1);
        $myPaginator = $myPaginator->toArray();
        $page = '';
        foreach ($myPaginator["links"] as $k => $l) {
            if (($k != 0) && ($k != (count($myPaginator["links"]) - 1))) {
                $cls = "";
                if ($l["active"]) {
                    $cls = " active ";
                } else {
                    if ($l["label"] != "...") {
                        $cls = " page-select ";
                    }
                }
                $page .= '<li page="' . $l["label"] . '" class=" page-item ' . $cls . (($l["label"] == "...") ? " disabled" : "") . ' "><span class="page-link" >' . $l["label"] . '</span></li>';
            }
        }
        $data["page"]  = $page;
        return $data;
    }
    /**
     * 棚卸一覧を取る
     * @access private
     * @param array $request
     *          すべてデータを取る
     * @return array 棚卸一覧
     */
    private function _getListStock(Request $request, array $dataWhere)
    {
        $sql  = "SELECT
                    stdtl.MaterialID    
                    , stdtl.StockID                              
                    , mmat.MaterialImg                                 
                    , mmat.MaterialNM                                   
                    , mmat.MaterialAlias                 
                    , bg.FreeText1                      
                    , mmat.Type                                          
                    , REPLACE(cast(cast(mmats.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum 
                    , REPLACE(cast(cast(SUM(stdtl.RealStockNum) as DECIMAL(7,1)) as float),'.0','') RealStockNum 
                    , FORMAT(stdtl.StockDate,'yyyy/MM/dd') AS StockDate
                    , sys.DispText as RealStockUnitNM
                FROM
                    T_StockDetail stdtl
                INNER JOIN
                T_Stock st
                    ON
                    stdtl.StockID = st.StockID
                INNER JOIN 
                M_Material mmat
                    ON
                    stdtl.MaterialID = mmat.MaterialID
                INNER JOIN 
                M_MaterialShelf mmats
                    ON
                    stdtl.MaterialID = mmats.MaterialID
                    AND
                    mmats.StoreID = '999'
                    AND
                    mmats.ShelfID = '999'
                    
                INNER JOIN
                M_Store mst
                    ON
                    mst.StoreID = stdtl.StoreID
                    AND mst.DeleteFlg = 0
                INNER JOIN
                M_Shelf msl
                ON
                msl.ShelfID = stdtl.ShelfID
                AND msl.StoreID = stdtl.StoreID
                    AND msl.DeleteFlg = 0
                LEFT JOIN M_System sys 
                    ON sys.SystemCD = '000008' 
                    AND sys.InternalValue = mmat.UseUnitCD 
                LEFT JOIN M_System as bg
                    ON mmat.MaterialCls = bg.InternalValue
                    AND bg.SystemCD = '000025' ";
        //検索条件
        $sql .= $this->_getTxtSearch($request);
        $limit = GetSetting::getSettingByID("Tbl_numrow");
        $offset = ($request->Page * $limit);
        $sql .= " GROUP BY
                    stdtl.StockID
                , stdtl.MaterialID
                , mmat.MaterialImg                      
                , bg.FreeText1                    
                , mmat.FilePath       
                , mmat.MaterialNM                             
                , mmat.MaterialAlias
                , mmat.Type
                , mmats.StockNum
                , stdtl.StockDate
                , sys.DispText
                ORDER BY
                stdtl.StockID 
                OFFSET " . $offset . " ROWS FETCH NEXT " . $limit . " ROWS ONLY ";
        $this->_getListStockOld($request, $dataWhere);
        return DB::select($sql, $dataWhere);
    }

    /**
     * 棚卸一覧を取る
     * @access private
     * @param array $request
     *          すべてデータを取る
     * @return array 棚卸一覧
     */
    private function _getListStockOld(Request $request, array $dataWhere)
    {
        $sql  = "SELECT
                    stdtl.MaterialID    
                    , stdtl.StockID                              
                    , mmat.MaterialImg                                  
                    , mmat.MaterialNM                                  
                    , mmat.MaterialAlias                              
                    , mmat.Type                                         
                    , mmats.StockNum                                 
                    , REPLACE(cast(cast(SUM(stdtl.RealStockNum) as DECIMAL(7,1)) as float),'.0','') RealStockNum      
                    , FORMAT(stdtl.StockDate,'yyyy/MM/dd') AS StockDate
                    , mst.StoreNM 
                    , msl.ShelfNM
                    , mst.StoreID 
                    , msl.ShelfID
                    , sys.DispText as RealStockUnitNM
                FROM
                    T_StockDetail stdtl
                INNER JOIN
                T_Stock st
                    ON
                    stdtl.StockID = st.StockID
                INNER JOIN 
                M_Material mmat
                    ON
                    stdtl.MaterialID = mmat.MaterialID
                INNER JOIN 
                M_MaterialShelf mmats
                    ON
                    stdtl.MaterialID = mmats.MaterialID
                    AND
                    mmats.StoreID = '999'
                    AND
                    mmats.ShelfID = '999'
                INNER JOIN
                M_Store mst
                    ON
                    mst.StoreID = stdtl.StoreID
                    AND mst.DeleteFlg = 0
                INNER JOIN
                M_Shelf msl
                ON
                msl.ShelfID = stdtl.ShelfID
                AND msl.StoreID = stdtl.StoreID
                    AND msl.DeleteFlg = 0
                LEFT JOIN M_System sys
                ON sys.SystemCD = '000008'
                AND sys.InternalValue = mmat.UseUnitCD";
        //検索条件
        $sql .= $this->_getTxtSearch($request);
        $sql .= " GROUP BY
                    stdtl.StockID
                , stdtl.MaterialID
                , mmat.MaterialImg
                , mmat.MaterialNM                             
                , mmat.MaterialAlias
                , mmat.Type
                , mmats.StockNum
                , stdtl.StockDate
                , mst.StoreNM 
                , msl.ShelfNM
                , mst.StoreID 
                , msl.ShelfID
                , sys.DispText
                ORDER BY
                stdtl.StockID ";
        Session::put("SQL", $sql);
        // return DB::select($sql, $dataWhere);
    }

    /**
     * COUNT棚卸一覧を取る
     * @access private
     * @param array $request
     *          すべてデータを取る
     * @return int count
     */
    private function _getCountList(Request $request, array $dataWhere)
    {
        $sql  = "SELECT 
                        stdtl.StockID
                    , stdtl.MaterialID
                    FROM
                        T_StockDetail stdtl
                    INNER JOIN
                    T_Stock st
                        ON
                        stdtl.StockID = st.StockID
                    INNER JOIN 
                    M_Material mmat
                        ON
                        stdtl.MaterialID = mmat.MaterialID
                    INNER JOIN 
                    M_MaterialShelf mmats
                        ON
                        stdtl.MaterialID = mmats.MaterialID
                        AND
                        mmats.StoreID = '999'
                        AND
                        mmats.ShelfID = '999'
                        
                INNER JOIN
                M_Store mst
                    ON
                    mst.StoreID = stdtl.StoreID
                    AND mst.DeleteFlg = 0
                INNER JOIN
                M_Shelf msl
                ON
                msl.ShelfID = stdtl.ShelfID
                AND msl.StoreID = stdtl.StoreID
                    AND msl.DeleteFlg = 0
                    LEFT JOIN M_System sys 
                        ON sys.SystemCD = '000008' 
                        AND sys.InternalValue = mmat.UseUnitCD ";

        //検索条件
        $sql .= $this->_getTxtSearch($request);
        $sql .= " GROUP BY
                    stdtl.StockID
                , stdtl.MaterialID";
        $data =  DB::select($sql, $dataWhere);
        return count($data);
    }
    /**
     * SQL文を取る
     * @access private
     * @param array $request
     *          すべてデータを取る
     * @return string テキスト
     */
    private function _getTxtSearch(Request $request)
    {
        $sql_search = " WHERE stdtl.StoreID != '0' ";
        $flag = true;
        //資材ID
        if ($request->MaterialID != "") {
            if ($flag)
                $sql_search .= " AND ";
            $sql_search .= " mmat.MaterialID  LIKE :MaterialID";
            $flag = true;
        }
        //資材名
        if ($request->MaterialNM != "") {
            if ($flag)
                $sql_search .= " AND ";
            $sql_search .= " ( mmat.MaterialNM  LIKE :MaterialNM";
            $sql_search .= " OR ";
            $sql_search .= " mmat.MaterialAlias  LIKE :MaterialAlias )";
            $flag = true;
        }
        //形式
        if ($request->Type != "") {
            if ($flag)
                $sql_search .= " AND ";
            $sql_search .= " mmat.Type  LIKE :Type";
            $flag = true;
        }

        // -- 検索条件.棚卸日が空欄チェックボックスが「ON」の場合
        if ($request->StockDate == "true") {
            if ($flag)
                $sql_search .= " AND ";
            $sql_search .= " stdtl.StockDate IS NULL ";
            $flag = true;
        }
        //棚卸年月
        if ($request->StockYM != "") {
            if ($flag)
                $sql_search .= " AND ";
            $sql_search .= " st.StockID  = :StockID";
            $flag = true;
        }

        //資材区分
        if ($request->MaterialCls != "") {
            if ($flag)
                $sql_search .= " AND ";
            $sql_search .= " mmat.MaterialCls  = :MaterialCls";
            $flag = true;
        }
        if ($sql_search == " WHERE ") $sql_search = "";
        return $sql_search;
    }
    /**
     * データを取る
     * @access private
     * @param array $request
     *          すべてデータを取る
     * @return array bindings
     */
    private function _getDataWhere(Request $request)
    {
        $data = array();
        //資材ID
        if ($request->MaterialID != "") {
            $data["MaterialID"] = "%" . $request->MaterialID . "%";
        }
        //資材名
        if ($request->MaterialNM != "") {
            $data["MaterialNM"] = "%" . $request->MaterialNM . "%";
            $data["MaterialAlias"] = "%" . $request->MaterialNM . "%";
        }
        //形式
        if ($request->Type != "") {
            $data["Type"] = "%" . $request->Type . "%";
        }
        //棚卸年月
        if ($request->StockYM != "") {
            $data["StockID"] = $request->StockYM;
        }

        //資材区分
        if ($request->MaterialCls != "") {
            $data["MaterialCls"] = $request->MaterialCls;
        }
        Session::put("dataWhere", $data);
        return $data;
    }

    /**
     * 「実数」をクリックする時、データを取ってAjaxで使う
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return Excelファイル
     */
    public function getStockDetail(Request $request)
    {
        $sql = "SELECT
                        mmat.MaterialNM                                        
                    , mmat.MaterialAlias                        
                    , mmat.Type                                         
                    , FORMAT(stdtl.StockDate,'yyyy/MM/dd') AS StockDate 
                    , mstr.StoreNM                                      
                    , mslf.ShelfNM         
                    , REPLACE(cast(cast(stdtl.RealStockNum as DECIMAL(7,1)) as float),'.0','') RealStockNum                               
                    , stdtl.StockID                                     
                    , stdtl.StoreID                                     
                    , stdtl.ShelfID                                     
                    , stdtl.MaterialID 
                    , st.CloseFlg                                 
                    FROM
                T_StockDetail stdtl
                INNER JOIN 
                M_Material mmat
                ON
                stdtl.MaterialID = mmat.MaterialID
                INNER JOIN 
                M_Store mstr
                ON
                stdtl.StoreID = mstr.StoreID
                AND mstr.DeleteFlg = 0
                INNER JOIN 
                M_Shelf mslf
                ON
                stdtl.ShelfID = mslf.ShelfID
                AND mslf.StoreID = stdtl.StoreID
                AND mslf.DeleteFlg = 0
                INNER JOIN 
                T_Stock st
                ON
                stdtl.StockID = st.StockID
                WHERE
                stdtl.StockID = " . $request->StockID
            . " AND stdtl.MaterialID = " . $request->MaterialID;
        $data = DB::select($sql);
        $html = "";
        foreach ($data as $d) {
            $disabled = "";
            if ($d->CloseFlg == true) {
                $disabled = " disabled ";
            }
            $html .= '<tr>
                        <td>' . $d->StoreNM . '</td>
                        <td>' . $d->ShelfNM . '</td>
                        <td>
                            <input type="hidden" class="TxtQtyMaterialID" name="TxtQtyMaterialID[]" value="' . $d->MaterialID . '">
                            <input type="hidden" class="TxtQtyStockID" name="TxtQtyStockID[]" value="' . $d->StockID . '">
                            <input type="hidden" class="TxtQtyStoreID" name="TxtQtyStoreID[]" value="' . $d->StoreID . '">
                            <input type="hidden" class="TxtQtyShelfID" name="TxtQtyShelfID[]" value="' . $d->ShelfID . '">
                            <input type="number" min="0" class="TxtQty" ' . $disabled . '  step=".1" name="TxtQty[]" value="' . $d->RealStockNum . '">
                        </td>
                    </tr>';
        }
        $dataStockDetail = DB::select("SELECT
                                            mmat.MaterialNM
                                            , mmat.MaterialAlias
                                            , mmat.Type
                                            , FORMAT(stdtl.StockDate, 'yyyy/MM/dd') AS StockDate
                                        FROM
                                            T_StockDetail stdtl 
                                            INNER JOIN M_Material mmat 
                                            ON stdtl.MaterialID = mmat.MaterialID 
                                            INNER JOIN M_Store mstr 
                                            ON stdtl.StoreID = mstr.StoreID 
	                                        AND mstr.DeleteFlg = 0
                                            INNER JOIN M_Shelf mslf 
                                            ON stdtl.ShelfID = mslf.ShelfID 
                                            AND stdtl.StoreID = mslf.StoreID 
	                                        AND mslf.DeleteFlg = 0
                                        WHERE
                                            stdtl.StockID = '" . $request->StockID . "'
                                             AND stdtl.MaterialID = '" . $request->MaterialID . "'
                                        
                                        GROUP BY
                                            mmat.MaterialNM
                                            , mmat.MaterialAlias
                                            , mmat.Type
                                            ,  StockDate");
        $data = array(
            "html" => $html,
            "MaterialNM" => $dataStockDetail[0]->MaterialNM,
            "MaterialAlias" => $dataStockDetail[0]->MaterialAlias,
            "Type" => $dataStockDetail[0]->Type,
            "StockDate" =>  $dataStockDetail[0]->StockDate
        );
        echo json_encode($data);
    }

    public function setStockDetail(Request $request)
    {
        $TxtQty = $request->TxtQty;
        $TxtQtyMaterialID = $request->TxtQtyMaterialID;
        $TxtQtyStockID = $request->TxtQtyStockID;
        $TxtQtyStoreID = $request->TxtQtyStoreID;
        $TxtQtyShelfID = $request->TxtQtyShelfID;
        $TxtQtyDate = $request->TxtQtyDate;
        $UserID = Session::get("UserID");

        // ①テンポラリテーブルを作成
        DB::unprepared('CREATE TABLE #TmpStock (
                    StockID int not null                                 
                , StoreID nvarchar(3) COLLATE Japanese_CI_AS not null    
                , ShelfID nvarchar(3) COLLATE Japanese_CI_AS not null    
                , MaterialID nvarchar(6) COLLATE Japanese_CI_AS not null 
                , StockDate datetime                                   
                , RealStockNum decimal(7, 1)                                     
                , AddUserID nvarchar(10) COLLATE Japanese_CI_AS          
                , UpdateUserID nvarchar(10) COLLATE Japanese_CI_AS       
                )');

        foreach ($TxtQty as $key => $qty) {
            $dataInsert =  array(
                "StockID" => $TxtQtyStockID[$key],
                "StoreID" => $TxtQtyStoreID[$key],
                "ShelfID" => $TxtQtyShelfID[$key],
                "MaterialID" => $TxtQtyMaterialID[$key],
                "StockDate" => $TxtQtyDate,
                "RealStockNum" => $qty,
                "AddUserID" => $UserID,
                "UpdateUserID" => $UserID
            );
            DB::statement("INSERT 
                    INTO #TmpStock(
                        StockID      
                    , StoreID       
                    , ShelfID       
                    , MaterialID    
                    , StockDate     
                    , RealStockNum 
                    , AddUserID     
                    , UpdateUserID  
                    )
                    VALUES (
                        :StockID      
                    , :StoreID       
                    , :ShelfID       
                    , :MaterialID    
                    , :StockDate     
                    , :RealStockNum 
                    , :AddUserID     
                    , :UpdateUserID 
                    )", $dataInsert);
        }

        //③ストアドプロシージャ「dbo.ImportStock」を実行
        DB::statement(" DECLARE @RetCode nvarchar(500)
                            EXECUTE " . env("DB_DATABASE") . ".dbo.ImportStock @RetCode OUTPUT ");
        //④テンポラリテーブルを削除
        DB::statement("DROP TABLE #TmpStock");
        echo "1";
    }

    /**
     * 「棚卸確定」ボタンをクリックするDB更新
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return object
     */
    private function _getStockYM(Request $request)
    {
        if ($request->StockYM) {
            $data = DB::select("SELECT CloseFlg ,FORMAT(AmountAllFixDate,'yyyy/MM/dd') AS AmountAllFixDate
                        FROM T_Stock 
                        WHERE
                        StockID = $request->StockYM");
            return $data[0];
        }
    }
    /**
     * 「棚卸確定」ボタンをクリックするDB更新
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return Null
     */
    public function updateInvenConfirm(Request $request)
    {
        if ($request->StockID) {
            $UserID =  Auth::user()->UserID;
            $data = array(
                "CloseFlg" => $request->CloseFlg,
                "UpdateUserID" => $UserID,
                "UpdateDate" => $this->_timetoday
            );
            DB::table("T_Stock")->where("StockID", $request->StockID)->update($data);
            return true;
        }
    }
    /**
     * 「在庫一括修正」ボタンをクリックするDB更新
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return Null
     */
    public function updateInvenCorrec(Request $request)
    {
        if ($request->StockID) {
            $UpdateUserID = Auth::user()->UserID;
            //①棚卸テーブル、棚卸明細テーブルを元に共通テーブル式を作成
            //②使用資材棚マスタの在庫数を更新
            DB::unprepared("WITH cte_ts AS
                    (
                        SELECT
                            ts.StockID
                            , tsd.MaterialID
                            , SUM(tsd.StockNum) as StockNum
                            , SUM(tsd.RealStockNum) as RealStockNum
                        FROM
                            T_Stock ts 
                            INNER JOIN T_StockDetail tsd 
                            ON tsd.StockID = ts.StockID 
                            WHERE ts.StockID = '" . $request->StockID . "'
                        GROUP BY ts.StockID,tsd.MaterialID
                        )
                        UPDATE M_MaterialShelf
                        SET
                            M_MaterialShelf.StockNum = M_MaterialShelf.StockNum + ts.RealStockNum - ts.StockNum  
                            , M_MaterialShelf.UpdateUserID = '" . $UpdateUserID . "'                              
                            , M_MaterialShelf.UpdateDate = '" . $this->_timetoday . "'                                       
                        FROM
                            cte_ts as ts
                        WHERE
                            ts.MaterialID = M_MaterialShelf.MaterialID
                            AND M_MaterialShelf.StoreID ='999'
                            AND M_MaterialShelf.ShelfID = '999'");

            //③売上一括修正日の更新
            $data = array(
                "AmountAllFixDate" => $this->_today,
                "UpdateUserID" => $UpdateUserID,
                "UpdateDate" => $this->_timetoday
            );
            DB::table("T_Stock")->where("StockID", $request->StockID)->update($data);
        }
    }
    /**
     * 「Excel出力」ボタンをクリックする
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return Excelファイル
     */
    public function export(Request $request)
    {
        $stock = DB::select("SELECT
                                LEFT(stc.StockYM, 4) + '/' + SUBSTRING(stc.StockYM ,5 ,2) AS StockYM 
                            FROM
                            T_Stock stc
                            WHERE stc.StockID = " . $request->StockYM);
        Session::put("StockYM", $stock[0]->StockYM);
        return Excel::download(new StockExportStyling(), str_replace("/", "", $stock[0]->StockYM) . "_棚卸表.xlsx");
    }
}
