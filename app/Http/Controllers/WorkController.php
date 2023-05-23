<?php

namespace App\Http\Controllers;

use App\Exports\hokoku;
use App\Exports\WorkUchiwake;
use Illuminate\Http\Request;
use App\Models\M_System;
use DB;
use App\GetSetting;
use App\GetMessage;
use Excel;
use App\Exports\WorkExport;
use App\Models\T_UseMaterial;
use App\Models\T_Work;
use Carbon;
use App\Http\Controllers\MatterExportController;

class WorkController extends Controller
{
    public $_timetoday;
    public $_today;
    protected $_join = "left";
    public function __construct()
    {
        ini_set('max_execution_time', 360);
        date_default_timezone_set('Asia/Tokyo');
        $this->_timetoday = Carbon\Carbon::now()->format("Y/m/d H:i:s");
        $this->_today = Carbon\Carbon::now()->format("Y年m月d日");
    }
    public function index(Request $rq)
    {
        $ErrMsg = null;
        // 入金状況
        $ListPaymentStatus = M_System::select('InternalValue', 'DispText')->where("SystemCD", M_System::$PaymentStatusCD)->get();
        // 工事区分
        $ListType = SystemController::getSystemByCD(M_System::$WWTypeCD, true);;
        // 作業区分
        $ListWorkType = M_System::select('InternalValue', 'DispText')->where("SystemCD",  M_System::$WorkTypeCD)->get();
        // 作業状況
        $ListWorkStatus = M_System::select('InternalValue', 'DispText')->where("SystemCD",  M_System::$WorkStatusCD)->get();
        $list = [];
        $first = true;
        if ($rq->filled("Export") || $rq->filled("btnExcelHokoku") || $rq->filled("btnSearch") || $rq->filled("page")) {
            $first = false;
            $list = $this->_getList($rq);
            if ($rq->filled("Export")) {
                ini_set('memory_limit', '-1');
                set_time_limit(0);
                ignore_user_abort(true);
                ini_set('max_execution_time', 3360);

                //Excel出力のログ
                $exportlog = new MatterExportController();
                $exportlog->ExportLog("07");

                // Ｅｘｃｅｌに出力する
                $template = "work.xlsx";
                return Excel::download(new WorkExport($list, $template), '工事受付一覧.xlsx');
            }
            if ($rq->filled("btnExcelHokoku")) {
                ini_set('memory_limit', '-1');
                set_time_limit(0);
                ignore_user_abort(true);
                ini_set('max_execution_time', 3360);
                if ($list) {
                    $listnew = $this->getExcelHokoku($list);
                    $template = "hokoku.xlsx";
                    $WorkTimeFrom = Carbon\Carbon::parse($rq->WorkTimeFrom)->format("Y年m月");
                    $lastsearch = Carbon\Carbon::parse($rq->WorkTimeTo)->format("Y年m月");
                    if ($lastsearch != $WorkTimeFrom) {
                        $lastsearch = $WorkTimeFrom . "~" . $lastsearch;
                    }
                    if ($rq->check) {
                        return ["status" => 1];
                    }
                    //Excel出力のログ
                    $exportlog = new MatterExportController();
                    $exportlog->ExportLog("08");

                    return Excel::download(new hokoku($listnew, $this->_today, $template, $lastsearch), '修繕報告書.xlsx');
                } else {
                    return ["status" => 0, "ErrMsg" => GetMessage::getMessageByID("error085")];
                }
            }
        }
        $datasearch = $rq->all();
        return view("work.index", compact("list", "first", "ListPaymentStatus", "ListType", "ListWorkType", "ListWorkStatus", "datasearch", "ErrMsg"));
    }

    // 修繕内訳書出力
    public function workExportUchiwake(Request $rq)
    {
        $sql = "WITH T_Work_Tmp as (
                SELECT *
                ,RANK() OVER (PARTITION BY  WWID ORDER BY WorkFrom DESC) AS RankNo
                FROM dbo.T_Work
                WHERE WorkType IN ('02','03','04','05','06','07') --水漏診断とその他以外
                AND TargetType != '05' --「-」以外
                )
                
                --抽出条件
                --・県水の工事数
                --・完了
                --・料金が発生している
                ,tmp as(
                SELECT TWW.WWRecID
                ,TW.TargetType
                ,TW.WorkFrom
                -- 1：平日、2：土日祝、3：平日深夜
                ,(CASE WHEN dbo.checkHoliday(TW.WorkFrom) = 1 then '2'
                        WHEN convert(varchar(20),TW.WorkFrom, 108) Between '08:00:00' and '16:59:59 ' then '1'
                    ELSE '3' END) TimeZone
                -- 1：AP、2：PM、3：17時以降
                ,(CASE WHEN convert(varchar(20),TW.WorkFrom, 108) Between '08:00:00' and '11:59:59 ' then '1'
                    WHEN convert(varchar(20),TW.WorkFrom, 108) Between '12:00:00' and '16:59:59 ' then '2'
                    ELSE '3' END) AmPm
                FROM (SELECT * FROM T_Work_Tmp WHERE RankNo = 1) TW
                INNER JOIN T_WaterWork TWW ON TWW.WWID = TW.WWID
                WHERE TWW.WWType = '01' --県水
                AND TWW.WorkStatus = '02' --完了済
                AND ISNULL(TWW.TechFee,0) + ISNULL(TWW.MaterialFee,0) + ISNULL(TWW.TravelFee,0) + ISNULL(TWW.SurveyFee,0) + ISNULL(TWW.DisposalFee,0) - ISNULL(TWW.Discount,0) > 0 --合計金額が0円より多い
                AND (TW.WorkFrom >=dateadd(hour, 9, :WorkTimeFrom)and TW.WorkFrom < dateadd(hour, 9, dateadd(day,1,:WorkTimeTo)))
                )
                
                SELECT 
                --修繕
                --対象区分の区分けは以下
                --水栓
                --給水管
                --その他（排水 + その他）
                (SELECT COUNT(*) FROM tmp WHERE TargetType = '01' AND TimeZone = '3') W_Night_Faucet --水栓：夜間
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '01' AND TimeZone = '2') W_Holiday_Faucet --水栓：休日
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '01' AND TimeZone = '1') W_Weekday_Faucet --水栓：平日
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '02' AND TimeZone = '3') W_Night_Supply --給水管：夜間
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '02' AND TimeZone = '2') W_Holiday_Supply --給水管：休日
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '02' AND TimeZone = '1') W_Weekday_rSupply --給水管：平日
                ,(SELECT COUNT(*) FROM tmp WHERE (TargetType = '03' OR TargetType = '04') AND TimeZone = '3') W_Night_etc --その他：夜間
                ,(SELECT COUNT(*) FROM tmp WHERE (TargetType = '03' OR TargetType = '04') AND TimeZone = '2') W_Holiday_etc --その他：休日
                ,(SELECT COUNT(*) FROM tmp WHERE (TargetType = '03' OR TargetType = '04') AND TimeZone = '1') W_Weekday_etc --その他：平日
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '01') W_All_WaterFaucet--水栓：計
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '02') W_All_WaterSupply --給水管：計
                ,(SELECT COUNT(*) FROM tmp WHERE TargetType = '03' OR TargetType = '04') W_All_etc--その他：計
                ,(SELECT COUNT(*) FROM tmp WHERE TimeZone = '3') W_All_Night --夜間：計
                ,(SELECT COUNT(*) FROM tmp WHERE TimeZone = '2') W_All_Holiday --休日：計
                ,(SELECT COUNT(*) FROM tmp WHERE TimeZone = '1') W_All_Weekday --平日：計
                ,(SELECT COUNT(*) FROM tmp) W_All--合計
                --時間別
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '1' AND (TimeZone = '1' OR TimeZone = '3')) T_Weekday_Am --AM：平日
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '2' AND (TimeZone = '1' OR TimeZone = '3')) T_Weekday_Pm --PM：平日
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '3' AND (TimeZone = '1' OR TimeZone = '3')) T_Weekday_Night　--17時以降：平日
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '1' AND TimeZone = '2') T_Holiday_Am --AM：休日
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '2' AND TimeZone = '2') T_Holiday_Pm --PM：休日
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '3' AND TimeZone = '2') T_Holiday_Night --17時以降：休日
                ,(SELECT COUNT(*) FROM tmp WHERE (TimeZone = '1' OR TimeZone = '3')) T_All_Weekday --平日：計
                ,(SELECT COUNT(*) FROM tmp WHERE TimeZone = '2') T_All_Holiday --休日：計
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '1') T_All_Am --AM：計
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '2') T_All_Pm --PM：計
                ,(SELECT COUNT(*) FROM tmp WHERE AmPm = '3') T_All_Night --17時以降：計
                ,(SELECT COUNT(*) FROM tmp) T_All--合計";

        //-- 検索条件.受付日:From/Toが両方指定されている場合
        if ($rq->filled("WorkTimeFrom") && $rq->filled("WorkTimeTo")) {
            $search["WorkTimeFrom"] = $rq->WorkTimeFrom;
            $search["WorkTimeTo"] = $rq->WorkTimeTo;

            $result = DB::select($sql, $search);
            if ($result) {
                //Excel出力のログ
                $exportlog = new MatterExportController();
                $exportlog->ExportLog("09");

                $template = "workuchiwake.xlsx";
                return Excel::download(new WorkUchiwake($result[0], Carbon\Carbon::parse($rq->WorkTimeTo)->format("Y年m月"), $template), '修繕内訳書.xlsx');
            }
        }
    }
    // 修繕報告書出力 
    public function getExcelHokoku($list)
    {
        if ($list) {
            foreach ($list as $key => $l) {
                //　工事
                $WorkFrom = T_Work::selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy年MM月dd日') AS WorkFrom")
                    ->where("T_Work.WWID", $l->WWIDold)
                    // ->where("T_Work.WorkType", "02") //工事
                    ->orderBy("T_Work.WorkFrom")
                    ->get()->first();
                $list[$key]->WorkFrom =  "";
                if ($WorkFrom)
                    $list[$key]->WorkFrom =  $WorkFrom["WorkFrom"];
                // 最終作業日
                $WorkEnd = T_Work::selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy年MM月dd日') AS WorkFrom")
                    ->where("T_Work.WWID", $l->WWIDold)
                    // ->where("T_Work.WorkType", "02") //工事
                    ->orderBy("T_Work.WorkFrom", "DESC")
                    ->get()->first();
                $list[$key]->WorkEnd =  "";
                if ($WorkEnd)
                    $list[$key]->WorkEnd =  $WorkEnd["WorkFrom"];
                $use = T_UseMaterial::selectRaw("ud.MaterialNM + ud.Type+' '+ CONVERT(varchar(10),ud.UseNum)+'、'   as UseMaterial",)
                    ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
                    ->where("T_UseMaterial.WWID", $l->WWIDold)
                    ->orderBy("T_UseMaterial.ConstructDate");
                $totalrowshow = 10;
                $list[$key]->listuse = $this->ArrayToString($use->get()->take($totalrowshow)->toArray());
                if ($use->get()->count() > $totalrowshow)  $list[$key]->listuse =  $list[$key]->listuse . "「他」";
            }
        }
        return  $list;
    }
    /**
     * 配列からテキストに更新する
     * @access private
     * @param array $data
     * @return テキスト 
     */
    private function ArrayToString($data)
    {
        $text = "";
        if ($data) {
            foreach ($data as $v) {
                $text .= $v["UseMaterial"];
            }
        }
        return $text;
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
        $data = $datastr =  $datawork = $dataworkb = array();
        // 受付No
        if ($request->filled("WWID")) {
            $data["WWID"] = $request->WWID;
            $datastr[] = " WW.WWRecID = :WWID ";
        }
        //受付日
        if ($request->filled("WWDateTimeFrom") || $request->filled("WWDateTimeTo")) {
            //-- 検索条件.受付日:From/Toが両方指定されている場合
            if ($request->filled("WWDateTimeFrom") && $request->filled("WWDateTimeTo")) {
                $data["WWDateTimeFrom"] = $request->WWDateTimeFrom;
                $data["WWDateTimeTo"] = $request->WWDateTimeTo . " 23:59:59";
                $datastr[] = " WW.WWDateTime BETWEEN :WWDateTimeFrom AND :WWDateTimeTo ";
            }
            //-- 検索条件.受付日:Fromのみ指定されている場合
            if ($request->filled("WWDateTimeFrom") && !$request->filled("WWDateTimeTo")) {
                $data["WWDateTimeFrom"] = $request->WWDateTimeFrom;
                $datastr[] = " WW.WWDateTime >= :WWDateTimeFrom ";
            }
            //-- 検索条件.受付日:Toのみ指定されている場合
            if (!$request->filled("WWDateTimeFrom") && $request->filled("WWDateTimeTo")) {
                $data["WWDateTimeTo"] = $request->WWDateTimeTo . " 23:59:59";
                $datastr[] = " WW.WWDateTime <= :WWDateTimeTo ";
            }
        }
        //作業日
        if ($request->filled("WorkTimeFrom") || $request->filled("WorkTimeTo")) {
            $this->_join = "inner";
            //-- 検索条件.受付日:From/Toが両方指定されている場合
            if ($request->filled("WorkTimeFrom") && $request->filled("WorkTimeTo")) {
                $data["WorkTimeFrom"] = $request->WorkTimeFrom;
                $data["WorkTimeTo"] = $request->WorkTimeTo . " 23:59:59";
                $dataworkb[] = " ba.WorkFrom BETWEEN :WorkTimeFrom AND :WorkTimeTo ";
            }
            //-- 検索条件.受付日:Fromのみ指定されている場合
            if ($request->filled("WorkTimeFrom") && !$request->filled("WorkTimeTo")) {
                $data["WorkTimeFrom"] = $request->WorkTimeFrom;
                $dataworkb[] = " ba.WorkFrom >= :WorkTimeFrom ";
            }
            //-- 検索条件.受付日:Toのみ指定されている場合
            if (!$request->filled("WorkTimeFrom") && $request->filled("WorkTimeTo")) {
                $data["WorkTimeTo"] = $request->WorkTimeTo . " 23:59:59";
                $dataworkb[] = " ba.WorkFrom <= :WorkTimeTo ";
            }
        }


        // 工事区分
        if ($request->filled("WWReceptType")) {
            $data["WWReceptType"] = $request->WWReceptType;
            $datastr[] = " WW.WWType = :WWReceptType ";
        }
        // 扱者
        if ($request->filled("WWHandlerID")) {
            $data["WWHandlerID"] = '%' . $request->WWHandlerID . '%';
            $datastr[] = " [wh].[UserNM] LIKE :WWHandlerID ";
        }
        // 依頼者氏名
        if ($request->filled("ReqName")) {
            $data["ReqName"] = '%' . $request->ReqName . '%';
            $data["ReqNameKana"] = '%' . $request->ReqName . '%';
            $datastr[] = " (WW.ReqName LIKE :ReqName OR WW.ReqNameKana LIKE :ReqNameKana) ";
        }
        // 依頼者氏名
        if ($request->filled("WWReceptNo")) {
            $data["WWReceptNo"] = '%' . $request->WWReceptNo . '%';
            $datastr[] = " (WW.WWReceptNo LIKE :WWReceptNo ) ";
        }
        // 依頼者住所 
        if ($request->filled("ReqAdress")) {
            $data["ReqAdress"] = '%' . $request->ReqAdress . '%';
            $datastr[] = " WW.ReqAdress LIKE :ReqAdress ";
        }
        // 入金状況 
        if ($request->filled("PaymentStatus")) {
            $data["PaymentStatus"] = $request->PaymentStatus;
            $datastr[] = " WW.PaymentStatus = :PaymentStatus ";
        }
        // 作業担当者氏名 
        if ($request->filled("WorkUserNM")) {
            $this->_join = "inner";
            $data["WorkUserNM"] = '%' . $request->WorkUserNM . '%';
            $dataworkb[] = " ba.UserNMs LIKE :WorkUserNM ";
        }
        // 作業区分
        if ($request->filled("WorkType")) {
            $this->_join = "inner";
            $data["WorkType"] = $request->WorkType;
            $dataworkb[] = " ba.WorkType = :WorkType ";
        }
        // 作業状況
        if ($request->filled("WorkStatus")) {
            if ($request->WorkStatus != "all") {
                $data["WorkStatus"] = $request->WorkStatus;
                $datastr[] = " WW.WorkStatus = :WorkStatus ";
            }
        }
        $datareturn = [
            "data" => $data,
            "datawork" => null,
            "dataworkb" => null,
            "datastr" => null
        ];
        if ($datastr) {
            $datareturn["datastr"] = " AND " . implode(" AND ", $datastr);
        }
        if ($datawork) {
            $datareturn["datawork"] = " WHERE " . implode(" AND ", $datawork);
        }
        if ($dataworkb) {
            $datareturn["dataworkb"] = " WHERE " . implode(" AND ", $dataworkb);
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
    private function _getList(Request $rq, bool $getPagination = false)
    {

        //検索条件データを取る
        $search = $this->_getSearch($rq);
        if ($getPagination) {
            $result = $this->renderPagination($rq, $search);
        } else {
            if ($rq->filled("Export") || $rq->filled("btnExcelHokoku")) {
                if ($rq->filled("Export")) {
                    $sql = $this->_getSQL10Work(false, false, $search, false);
                } else {
                    // 全てデータ
                    // 県水だけ
                    $sql = $this->_getSQL(false, false, $search, false, "01");
                }
                $result = DB::select($sql, $search["data"]);
            } else {
                $result = $this->renderPagination($rq, $search);
            }
        }
        return $result;
    }
    private function renderPagination(Request $rq, array $search)
    {
        $limit = GetSetting::getSettingByID("Tbl_numrow");

        // ページ設定
        if (!$rq->page) $rq->page = 0;
        else $rq->page = $rq->page - 1;
        $offset = ($rq->page * $limit);
        // SQL分を取る
        $sql = $this->_getSQL($offset, $limit, $search, false, false);
        $sqlcount = $this->_getSQL($offset, $limit, $search, true);

        // SQL分を実行する
        $count = DB::select($sqlcount, $search["data"]);
        $result = DB::select($sql, $search["data"]);

        // ページングを取る
        $myPaginator = new \Illuminate\Pagination\LengthAwarePaginator($result, $count[0]->cnt, $limit);
        return $myPaginator;
    }
    private function _getSQL10Work($offset, $limit, $search, $count = false)
    {
        $sql = " With a as ( 
                    select
                        [T_WorkUser].*
                        , [u].[UserNM] 
                    from
                        [T_WorkUser] 
                        left join [M_User] as [u] 
                        on [u].[UserID] = [T_WorkUser].[UserID]    
                    ) 
                    , ba as ( 
                      select DISTINCT
                       [T_Work].*
                        , ( 
                          SELECT
                            a.UserNM + ',' 
                          FROM
                            a 
                          WHERE
                            a.WWID = [T_Work].WWID 
                            and a.WorkID = [T_Work].WorkID
                             FOR XML PATH ('')
                        ) AS UserNMs
                        , [wt].[DispText] as [WorkTypeNM]
                        , [tt].[DispText] as [TargetTypeNM]
                        , [wp].[DispText] as [WorkPlaceNM] 
                      from
                        [T_Work] 
                        left join [M_System] as [wt] 
                          on [wt].[SystemCD] = 000021 
                          and [wt].[InternalValue] = [T_Work].[WorkType] 
                        left join [M_System] as [tt] 
                          on [tt].[SystemCD] = 000022 
                          and [tt].[InternalValue] = [T_Work].[TargetType] 
                        left join [M_System] as [wp] 
                          on [wp].[SystemCD] = 000023 
                          and [wp].[InternalValue] = [T_Work].[WorkPlace] 
                    ) 
                    , b as ( 
                      select DISTINCT
                      *,
                        row_number() over ( 
                          partition by
                            [ba].WWID 
                          order by
                            [ba].WorkFrom desc
                        ) as rank
                      from
                        ba ";
        $sql .= $search["dataworkb"] . "
                    ) , e as(
                        SELECT tud.*, tu.WWID
                        FROM
                        T_UseMaterial tu 
                        INNER JOIN T_UseMaterialDetail tud
                        ON tu.UseMaterialID = tud.UseMaterialID
                        WHERE tud.UseNum > 0
                    ), d as(
                        SELECT DISTINCT tu.WWID,
                        ( 
                          SELECT DISTINCT
                            e.MaterialNM + e.Type + ',' 
                          FROM
                            e
                            WHERE e.WWID = tu.WWID
                            FOR XML PATH ('')
                        ) AS Materials
                        FROM
                        T_UseMaterial tu 
                    )  , g as ( 
                        SELECT DISTINCT
                          tu.WWID
                          , SUM(FLOOR(tud.SellPrice * tud.UseNum)) as totalSub 
                        FROM
                          T_UseMaterial tu 
                          INNER JOIN T_UseMaterialDetail tud 
                            ON tu.UseMaterialID = tud.UseMaterialID 
                        GROUP BY
                          tu.WWID
                      )                                              
                    , c as ( 
                    SELECT
                        * 
                    FROM
                        ( 
                        SELECT
                            *
                            , row_number() over (partition by [b].WWID order by [b].rank DESC) as rank2 
                            ,  FORMAT([b].WorkFrom, 'yyyy/MM/dd HH:mm') AS WorkFromFM
                            ,  FORMAT([b].WorkTo, 'yyyy/MM/dd HH:mm') AS WorkToFM
                            ,  CONVERT(VARCHAR(5), [b].TravelTime, 108) AS TravelTimeFM
                            ,  CONVERT(VARCHAR(5), [b].WorkTime, 108) AS WorkTimeFM
                        FROM
                            b 
                        WHERE
                            b.rank <= 10
                        ) bb 
                    ";
        $sql .= $search["datawork"] . " ) ";
        $sql .= " , alls as (
                    select distinct
                    WW.[WWRecID] as WWID
                    ,ROW_NUMBER() OVER (ORDER BY WW.WWID) AS rownumber
                    , d.Materials
                    , REPLACE(COALESCE(g.totalSub,0),'.0','') totalSub
                    , WW.[WWType]
                    , WW.[WWName]
                    , WW.[WWReceptType]
                    , WW.[WWReceptNo]
                    , WW.[WWAdress]
                    , WW.[WWHouseNum]
                    , WW.[WWHandlerID]
                    , WW.[ReqAdress]
                    , WW.[ReqBuilding]
                    , WW.[ReqName]
                    , WW.[ReqTEL]
                    , WW.[ReqContactTEL]
                    , WW.[PlugNum]
                    , WW.[ReqWaterNo]
                    , WW.[PipeSize]
                    , WW.[ConstrName]
                    , WW.[ConstrTEL]
                    , WW.[WTelFlg]
                    , WW.[SurveyStatus]
                    , WW.[LeakagePoint]
                    , WW.[ConstrAdress]
                    , WW.[WorkStatus]
                    , WW.[ConstrBuilding]
                    , WW.[ProcessingStatus]
                    , WW.[DeliveryUserID]
                    , WW.[flgInspectionIesults]
                    , WW.[flgWLeakage]
                    , WW.[flgWPilot]
                    , WW.[flgWFlood]
                    , WW.[flgWClean]
                    , WW.[FlgDRepair]
                    , WW.[PaymentMemo]
                    , WW.[FlgDDrainage]
                    , WW.[FlgDClean]
                    , WW.Guidelines
                    , WW.[ClaimAdress]
                    , WW.[ClaimName]
                    , WW.[ClaimTEL]
                    , WW.[ClaimType]
                    , WW.[ClaimUserID]
                    , WW.[PaymentStatus]
                    , WW.[TechFee]
                    , WW.[TravelFee]
                    , WW.[SurveyFee]
                    , WW.[DisposalFee]
                    , WW.[Discount]
                    , WW.[Others]
                    , WW.CommuWaitMaterial
                    , WW.CommuOther
                    , WW.CommuConcrete
                    , WW.CommuConst
                    ,FLOOR(REPLACE( (  COALESCE(g.totalSub,0) +  COALESCE(WW.[TechFee],0)+COALESCE(WW.[TravelFee],0)+COALESCE(WW.[SurveyFee],0)+COALESCE(WW.[DisposalFee],0)- COALESCE(WW.[Discount],0)),'.0','')) as totalSubAll 
                    ,FLOOR(REPLACE( (  COALESCE(g.totalSub,0) +  COALESCE(WW.[TechFee],0)+COALESCE(WW.[TravelFee],0)+COALESCE(WW.[SurveyFee],0)+COALESCE(WW.[DisposalFee],0)- COALESCE(WW.[Discount],0))*(convert(float,tax.InternalValue)),'.0','')) as totalTax 
                    , CASE  
                        WHEN WW.WTelFlg IS NULL THEN 'なし' 
                        ELSE 'あり' 
                        END AS WTelFlgNM
                    , CASE  
                        WHEN WW.CommuWaitMaterial IS NULL THEN '' 
                        ELSE '材料待ち,' 
                        END AS CommuWaitMaterialNM
                    , CASE  
                        WHEN WW.CommuOther IS NULL THEN '' 
                        ELSE 'その他,' 
                        END AS CommuOtherNM
                    , CASE  
                        WHEN WW.CommuConcrete IS NULL THEN '' 
                        ELSE 'コンクリート復旧,' 
                        END AS CommuConcreteNM
                    , CASE  
                        WHEN WW.CommuConst IS NULL THEN '' 
                        ELSE 'やり替え工事,' 
                        END AS CommuConstNM
                        
                    , CASE  
                        WHEN WW.flgInspectionIesults IS NULL THEN '不合格' 
                        ELSE '合格' 
                        END AS flgInspectionIesultsNM
                        
                    , CASE WHEN WW.flgWLeakage IS NULL THEN '' ELSE 'OK' END AS flgWLeakageNM
                    , CASE WHEN WW.flgWPilot IS NULL THEN '' ELSE 'OK' END AS flgWPilotNM
                    , CASE WHEN WW.flgWFlood IS NULL THEN '' ELSE 'OK' END AS flgWFloodNM
                    , CASE WHEN WW.FlgWCustomerExplan IS NULL THEN '' ELSE 'OK' END AS FlgWCustomerExplanNM
                    , CASE WHEN WW.flgWClean IS NULL THEN '' ELSE 'OK' END AS flgWCleanNM
                    , CASE WHEN WW.FlgDRepair IS NULL THEN '' ELSE 'OK' END AS FlgDRepairNM
                    , CASE WHEN WW.FlgDDrainage IS NULL THEN '' ELSE 'OK' END AS FlgDDrainageNM
                    , CASE WHEN WW.FlgDClean IS NULL THEN '' ELSE 'OK' END AS FlgDCleanNM
                    , CASE WHEN WW.FlgDCustomerExplan IS NULL THEN '' ELSE 'OK' END AS FlgDCustomerExplan
                    , [wt].[DispText] as [WWTypeNM]
                    , [wr].[DispText] as [WWReceptTypeNM]
                    , [wa].[DispText] as [WWAdressNM]
                    , [wh].[UserNM] as [WWHandlerIDNM]
                    , [wd].[UserNM] as [DeliveryUserNM]
                    , [wc].[UserNM] as [ClaimUserNM]
                    , [ps].[DispText] as [PaymentStatusNM]
                    , [ct].[DispText] as [ClaimTypeNM]
                    , [ws].[DispText] as [WorkStatusNM]
                    , b1.WorkTypeNM as WorkTypeNM1
                    , b1.TargetTypeNM as TargetTypeNM1
                    , b1.WorkPlaceNM as WorkPlaceNM1
                    , b1.WORKFromFM AS WORKFrom1
                    , b1.WORKToFM AS WORKTo1
                    , b1.UserNMs as UserNMs1
                    , b1.TravelTimeFM as TravelTime1
                    , b1.WorkTimeFM as WorkTime1
                    
                    , b2.WorkTypeNM as WorkTypeNM2
                    , b2.TargetTypeNM as TargetTypeNM2
                    , b2.WorkPlaceNM as WorkPlaceNM2
                    , b2.WORKFromFM AS WORKFrom2
                    , b2.WORKToFM AS WORKTo2
                    , b2.UserNMs as UserNMs2
                    , b2.TravelTimeFM as TravelTime2
                    , b2.WorkTimeFM as WorkTime2
                    
                    , b3.WorkTypeNM as WorkTypeNM3
                    , b3.TargetTypeNM as TargetTypeNM3
                    , b3.WorkPlaceNM as WorkPlaceNM3
                    , b3.WORKFromFM AS WORKFrom3
                    , b3.WORKToFM AS WORKTo3
                    , b3.UserNMs as UserNMs3
                    , b3.TravelTimeFM as TravelTime3
                    , b3.WorkTimeFM as WorkTime3
                    
                    , b4.WorkTypeNM as WorkTypeNM4
                    , b4.TargetTypeNM as TargetTypeNM4
                    , b4.WorkPlaceNM as WorkPlaceNM4
                    , b4.WORKFromFM AS WORKFrom4
                    , b4.WORKToFM AS WORKTo4
                    , b4.UserNMs as UserNMs4
                    , b4.TravelTimeFM as TravelTime4
                    , b4.WorkTimeFM as WorkTime4
                    
                    , b5.WorkTypeNM as WorkTypeNM5
                    , b5.TargetTypeNM as TargetTypeNM5
                    , b5.WorkPlaceNM as WorkPlaceNM5
                    , b5.WORKFromFM AS WORKFrom5
                    , b5.WORKToFM AS WORKTo5
                    , b5.UserNMs as UserNMs5
                    , b5.TravelTimeFM as TravelTime5
                    , b5.WorkTimeFM as WorkTime5

                    , b6.WorkTypeNM as WorkTypeNM6
                    , b6.TargetTypeNM as TargetTypeNM6
                    , b6.WorkPlaceNM as WorkPlaceNM6
                    , b6.WORKFromFM AS WORKFrom6
                    , b6.WORKToFM AS WORKTo6
                    , b6.UserNMs as UserNMs6
                    , b6.TravelTimeFM as TravelTime6
                    , b6.WorkTimeFM as WorkTime6
                    
                    , b7.WorkTypeNM as WorkTypeNM7
                    , b7.TargetTypeNM as TargetTypeNM7
                    , b7.WorkPlaceNM as WorkPlaceNM7
                    , b7.WORKFromFM AS WORKFrom7
                    , b7.WORKToFM AS WORKTo7
                    , b7.UserNMs as UserNMs7
                    , b7.TravelTimeFM as TravelTime7
                    , b7.WorkTimeFM as WorkTime7
                    
                    , b8.WorkTypeNM as WorkTypeNM8
                    , b8.TargetTypeNM as TargetTypeNM8
                    , b8.WorkPlaceNM as WorkPlaceNM8
                    , b8.WORKFromFM AS WORKFrom8
                    , b8.WORKToFM AS WORKTo8
                    , b8.UserNMs as UserNMs8
                    , b8.TravelTimeFM as TravelTime8
                    , b8.WorkTimeFM as WorkTime8
                    
                    , b9.WorkTypeNM as WorkTypeNM9
                    , b9.TargetTypeNM as TargetTypeNM9
                    , b9.WorkPlaceNM as WorkPlaceNM9
                    , b9.WORKFromFM AS WORKFrom9
                    , b9.WORKToFM AS WORKTo9
                    , b9.UserNMs as UserNMs9
                    , b9.TravelTimeFM as TravelTime9
                    , b9.WorkTimeFM as WorkTime9
                    
                    , b10.WorkTypeNM as WorkTypeNM10
                    , b10.TargetTypeNM as TargetTypeNM10
                    , b10.WorkPlaceNM as WorkPlaceNM10
                    , b10.WORKFromFM AS WORKFrom10
                    , b10.WORKToFM AS WORKTo10
                    , b10.UserNMs as UserNMs10
                    , b10.TravelTimeFM as TravelTime10
                    , b10.WorkTimeFM as WorkTime10
                    
                    , FORMAT(WW.WWDateTime, 'yyyy/MM/dd') AS WWDateTime
                    , FORMAT(WW.ClaimDate, 'yyyy/MM/dd') AS ClaimDate
                    , FORMAT(WW.PaymentDate, 'yyyy/MM/dd') AS PaymentDate 
                    from
                    [T_WaterWork] WW
                    left join d
                        on d.WWID = WW.WWID
                    left join g
                        on g.WWID = WW.WWID
                    left join [M_System] as [wt] 
                        on [wt].[SystemCD] = '000015' 
                        and [wt].[InternalValue] = WW.[WWType] 
                    left join [M_System] as [ws] 
                        on [ws].[SystemCD] = '000024' 
                        and [ws].[InternalValue] = WW.[WorkStatus] 
                    left join [M_System] as [wr] 
                        on [wr].[SystemCD] = '000016' 
                        and [wr].[InternalValue] = WW.[WWReceptType] 
                    left join [M_System] as [wa] 
                        on [wa].[SystemCD] = '000017' 
                        and [wa].[InternalValue] = WW.[WWAdress] 
                    left join M_System as tax
                        on tax.SystemCD = '9999999'
                    left join [M_User] as [wh] 
                        on [wh].[UserID] = WW.[WWHandlerID] 
                    left join [M_User] as [wd] 
                        on [wd].[UserID] = WW.[DeliveryUserID] 
                    left join [M_User] as [wc] 
                        on [wc].[UserID] = WW.[ClaimUserID] 
                    left join [M_System] as [ct] 
                        on [ct].[SystemCD] = '000018' 
                        and [ct].[InternalValue] = WW.[ClaimType] 
                    left join [M_System] as [ps] 
                        on [ps].[SystemCD] = '000019' 
                        and [ps].[InternalValue] = WW.[PaymentStatus] 
                    $this->_join join (SELECT * FROM c WHERE c.rank2 = 1) as b1 
                        on WW.WWID = b1.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 2) as b2 
                        on WW.WWID = b2.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 3) as b3 
                        on WW.WWID = b3.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 4) as b4 
                        on WW.WWID = b4.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 5) as b5 
                        on WW.WWID = b5.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 6) as b6 
                        on WW.WWID = b6.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 7) as b7 
                        on WW.WWID = b7.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 8) as b8 
                        on WW.WWID = b8.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 9) as b9 
                        on WW.WWID = b9.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 10) as b10 
                        on WW.WWID = b10.WWID 
                    where
                    WW.WWRecID IS NOT NULL AND WW.[FlgDelete] = 0 AND WW.WWType != 0 AND WW.WWType IS NOT NULL AND WW.WWName IS NOT NULL ";
        $sql .= $search["datastr"];
        $sql .= "  )
        SELECT *
        ,(totalSubAll + totalTax ) as totalAll
         FROM alls 
        WHERE rownumber < 10000000 ";
        return $sql; //,
    }
    private function _getSQL($offset, $limit, $search, $count = false, $WWType = false)
    {
        $sql = " With a as ( 
                    select
                        [T_WorkUser].*
                        , [u].[UserNM] 
                    from
                        [T_WorkUser] 
                        left join [M_User] as [u] 
                        on [u].[UserID] = [T_WorkUser].[UserID]    
                    )   
                    , ba as ( 
                      select DISTINCT
                       [T_Work].*
                        , ( 
                          SELECT
                            a.UserNM + ',' 
                          FROM
                            a 
                          WHERE
                            a.WWID = [T_Work].WWID 
                            and a.WorkID = [T_Work].WorkID
                             FOR XML PATH ('')
                        ) AS UserNMs
                        , [wt].[DispText] as [WorkTypeNM]
                        , [tt].[DispText] as [TargetTypeNM]
                        , [wp].[DispText] as [WorkPlaceNM] 
                      from
                        [T_Work] 
                        left join [M_System] as [wt] 
                          on [wt].[SystemCD] = 000021 
                          and [wt].[InternalValue] = [T_Work].[WorkType] 
                        left join [M_System] as [tt] 
                          on [tt].[SystemCD] = 000022 
                          and [tt].[InternalValue] = [T_Work].[TargetType] 
                        left join [M_System] as [wp] 
                          on [wp].[SystemCD] = 000023 
                          and [wp].[InternalValue] = [T_Work].[WorkPlace] 
                    ) 
                    , b as ( 
                      select DISTINCT
                      *,
                        row_number() over ( 
                          partition by
                            [ba].WWID 
                          order by
                            [ba].WorkFrom desc
                        ) as rank
                      from
                        ba   ";
        $sql .= $search["dataworkb"] . "                                          
                        ), c as ( 
                    SELECT
                        * 
                    FROM
                        ( 
                        SELECT
                            *
                            , row_number() over (partition by [b].WWID order by [b].rank DESC) as rank2 
                            ,  FORMAT([b].WorkFrom, 'yyyy/MM/dd') AS WorkFromFM
                            , FORMAT([b].WorkFrom, 'HH:mm')+'-'+FORMAT([b].WorkTo, 'HH:mm') AS WorkTimeFM
                        FROM
                            b 
                        WHERE
                            b.rank <= 3
                        ) bb 
                    ";
        $sql .= $search["datawork"] . " ) ";
        if ($count)
            $sql .= " SELECT count(*) cnt FROM (";
        $sql .= " select distinct
                    WW.[WWRecID] as WWID
                    , WW.[WWID] as WWIDold
                    , WW.[WWType]
                    , WW.[WWName], WW.[WWReceptType]
                    , WW.[WWAdress]
                    , WW.[WWHouseNum]
                    , WW.[WWHandlerID]
                    , WW.[ReqAdress]
                    , WW.[ReqBuilding]
                    , WW.[ReqName]
                    , WW.[ReqTEL]
                    , WW.[ReqContactTEL]
                    , WW.[PlugNum]
                    , WW.[PipeSize]
                    , WW.[ConstrName]
                    , WW.[ConstrTEL]
                    , WW.[WTelFlg]
                    , WW.[SurveyStatus]
                    , WW.[LeakagePoint]
                    , WW.[CommuConcrete]
                    , WW.[CommuConst]
                    , WW.[ConstrAdress]
                    , WW.[WorkStatus]
                    , WW.[ConstrBuilding]
                    , WW.[ProcessingStatus]
                    , WW.[DeliveryUserID]
                    , WW.[flgInspectionIesults]
                    , WW.[flgWLeakage]
                    , WW.[flgWPilot]
                    , WW.[flgWFlood]
                    , WW.[flgWClean]
                    , WW.[FlgDRepair]
                    , WW.[FlgDDrainage]
                    , WW.[FlgDClean]
                    , WW.[Guidelines]
                    , WW.[ClaimAdress]
                    , WW.[ClaimName]
                    , WW.[ClaimTEL]
                    , WW.[ClaimType]
                    , WW.[ClaimUserID]
                    , WW.[PaymentStatus]
                    , WW.[TechFee]
                    , WW.[MaterialFee]
                    , [wt].[DispText] as [WWTypeNM]
                    , [wr].[DispText] as [WWReceptTypeNM]
                    , [wh].[UserNM] as [WWHandlerIDNM]
                    , [ps].[DispText] as [PaymentStatusNM]
                    , [ct].[DispText] as [ClaimTypeNM]
                    , b1.WORKFromFM AS WORKFrom1
                    , b1.WorkTimeFM as time1
                    , b1.UserNMs as UserNMs1
                    , b1.WorkTypeNM as WorkTypeNM1
                    , b2.WORKFromFM AS WORKFrom2
                    , b2.WorkTimeFM as time2
                    , b2.UserNMs as UserNMs2
                    , b2.WorkTypeNM as WorkTypeNM2
                    , b3.WORKFromFM AS WORKFrom3
                    , b3.WorkTimeFM as time3
                    , b3.UserNMs as UserNMs3
                    , b3.WorkTypeNM as WorkTypeNM3
                    , FORMAT(WW.WWDateTime, 'yyyy/MM/dd') AS WWDateTime
                    , FORMAT(WW.ClaimDate, 'yyyy/MM/dd') AS ClaimDate
                    , FORMAT(WW.PaymentDate, 'yyyy/MM/dd') AS PaymentDate 
                    from
                    [T_WaterWork] WW
                    left join [M_System] as [wt] 
                        on [wt].[SystemCD] = '000015' 
                        and [wt].[InternalValue] = WW.[WWType] 
                    left join [M_System] as [wr] 
                        on [wr].[SystemCD] = '000016' 
                        and [wr].[InternalValue] = WW.[WWReceptType] 
                    left join [M_User] as [wh] 
                        on  [wh].[UserID] = WW.[WWHandlerID] 
                    left join [M_System] as [ct] 
                        on [ct].[SystemCD] = '000018' 
                        and [ct].[InternalValue] = WW.[ClaimType] 
                    left join [M_System] as [ps] 
                        on [ps].[SystemCD] = '000019' 
                        and [ps].[InternalValue] = WW.[PaymentStatus] 
                    $this->_join join (SELECT * FROM c WHERE c.rank2 = 1) as b1
                        on WW.WWID = b1.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 2) as b2 
                        on WW.WWID = b2.WWID 
                    left join (SELECT * FROM c WHERE c.rank2 = 3) as b3 
                        on WW.WWID = b3.WWID 
                    where  WW.WWRecID IS NOT NULL AND WW.[FlgDelete] = 0  ";
        if ($WWType) {
            $sql .= " AND WW.WWType = '01' ";
        }
        $sql .= $search["datastr"];

        if ($count)
            $sql .= " ) x";
        else {
            $sql .= " order by
            WW.[WWRecID] desc ";
            if ($limit)
                $sql .= "OFFSET " . $offset . " ROWS FETCH NEXT " . $limit . " ROWS ONLY ";
        }
        return $sql;
    }
}
