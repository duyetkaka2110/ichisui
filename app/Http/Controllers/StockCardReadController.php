<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Carbon;
use App\GetMessage;
use Auth;

class StockCardReadController extends Controller
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
     * 棚卸スマホ 
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
                                    WHERE stc.CloseFlg = 0
                                    ORDER BY
                                    stc.StockID DESC");


        return view("stockcardread.index")
            ->with(
                array(
                    "ListStockYM" => $ListStockYM
                )
            );
    }
    /**
     * 棚卸登録画面表示
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return view画面
     */
    public function register(Request $request)
    {
        $StockID = "";
        if ($request->StockID) $StockID =  $request->StockID;
        if (Session::has("StockID"))  $StockID = Session::get("StockID");
        if ($StockID) {
            return view("stockcardread.register")
                ->with(
                    array("StockID" => $StockID)
                );
        } else {
            return redirect("readcard")
                ->with(
                    array("error" => GetMessage::getMessageByID("error012"))
                );
        }
    }

    /**
     * 棚卸登録する
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return readcard
     */
    public function setStockDetail(Request $request)
    {
        if ($request->StockID && $request->StoreID && $request->ShelfID && $request->MaterialID) {
            $UserID = Auth::user()->UserID;

            $dataMulti = array(
                "UpdateUserID" => $UserID,
                "UpdateDate" => $this->_timetoday,
                "StockDate" => $request->TxtStockDate
            );
            //棚卸日を更新
            DB::table("T_StockDetail")
                ->where("StoreID ", "!=", 0)
                ->where("ShelfID ", "!=", 0)
                ->where("StockID", $request->StockID)
                ->where("MaterialID ", $request->MaterialID)
                ->update($dataMulti);
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
            $dataInsert =  array(
                "StockID" => $request->StockID,
                "StoreID" => $request->StoreID,
                "ShelfID" => $request->ShelfID,
                "MaterialID" => $request->MaterialID,
                "StockDate" => $request->TxtStockDate,
                "RealStockNum" => $request->TxtRealStockNum,
                "AddUserID" => $UserID,
                "UpdateUserID" => $UserID
            );
            //②テンポラリテーブルに入力したデータをINSERT
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

            //③ストアドプロシージャ「dbo.ImportStock」を実行
            DB::statement(" DECLARE @RetCode nvarchar(500)
                            EXECUTE " . env("DB_DATABASE") . ".dbo.ImportStock @RetCode OUTPUT ");
            //④テンポラリテーブルを削除
            DB::statement("DROP TABLE #TmpStock");

            return redirect()->back()
                ->with(array(
                    'StockID' => $request->StockID,
                    "msg" => "info010"
                ));
        } else {
            return redirect()->back()
                ->with(array('StockID' => $request->StockID));
        }
    }
    /**
     * バーコード読込 
     * @access public
     * @param array $request
     *          すべてデータを取る
     * @return json
     */
    public function getStockByBarcode(Request $request)
    {
        $sql = "SELECT
                    stdtl.StockID      
                , stdtl.StoreID      
                , stdtl.ShelfID
                , FORMAT(stdtl.StockDate,'yyyy/MM/dd') AS StockDate     
                , mstr.StoreNM       
                , mslf.ShelfNM       
                , stdtl.MaterialID   
                , mmat.MaterialNM    
                , mmat.MaterialAlias    
                , mmat.Type         
                , REPLACE(cast(cast(mtslf.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum 
                , REPLACE(cast(cast(SUM(stdtl.RealStockNum) as DECIMAL(7,1)) as float),'.0','') RealStockNum 
                FROM
                T_StockDetail stdtl
                INNER JOIN
                M_Material mmat
                ON
                    stdtl.MaterialID = mmat.MaterialID
                INNER JOIN M_MaterialShelf mtslf
                ON
                    mtslf.MaterialID = stdtl.MaterialID
                    AND 
                    mtslf.StoreID = '999'
                    AND 
                mtslf.ShelfID = '999'
                INNER JOIN
                M_Store mstr
                ON
                    stdtl.StoreID = mstr.StoreID
                INNER JOIN
                M_Shelf mslf
                ON
                    stdtl.StoreID = mslf.StoreID
                AND
                    stdtl.ShelfID = mslf.ShelfID
                WHERE
                stdtl.StockID = " . $request->StockID . "
                AND
                stdtl.StoreID = " . substr($request->Barcode, 6, 3) . "
                AND
                stdtl.ShelfID = " . substr($request->Barcode, 9, 3) . "
                AND
                stdtl.MaterialID = " . substr($request->Barcode, 0, 6) ."
			  GROUP BY 
			  stdtl.StockID
			  , stdtl.StoreID
			  , stdtl.ShelfID
              ,  StockDate
			  , mstr.StoreNM
			  , mslf.ShelfNM
			  , stdtl.MaterialID
			  , mmat.MaterialNM
              , mmat.MaterialAlias    
			  , mmat.Type
              , mtslf.StockNum
			  , RealStockNum ";
        $data = DB::select($sql);
        if ($data) {
            if (!$data[0]->StockDate) {
                $data[0]->StockDate = $this->_today;
            }
            echo json_encode($data[0]);
        } else {
            echo "0";
        }
    }
}
