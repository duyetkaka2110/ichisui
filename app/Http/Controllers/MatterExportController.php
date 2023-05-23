<?php

/** 案件入力画面 */

namespace App\Http\Controllers;

use App\Exports\mitsumore;
use Illuminate\Http\Request;
use DB;
use Auth;
use Image;
use Storage;
use App\Models\M_System;
use App\Models\T_Work;
use App\Models\T_WorkUser;
use App\Models\T_WorkImg;
use App\Models\T_UseMaterial;
use App\Models\M_Material2;
use Illuminate\Support\Facades\Validator;
use App\GetMessage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon;
use App\Exports\uketsuke;
use App\Exports\uchiwake;
use App\Models\T_WaterWorkAsWW;
use App\Models\T_WaterWork;
use PDF;

class MatterExportController extends Controller
{
  /** テーブル情報を変数に格納 */
  protected $_WaterWork = "T_WaterWork";
  protected $_Work = "T_Work";
  protected $_WorkUser = 'T_WorkUser';
  protected $_UseMaterial = "T_UseMaterial";
  protected $_UseMaterialDetail = "T_UseMaterialDetail";
  protected $_SystemTable = "M_System";
  protected $_UserTable = "M_User as U";
  protected $_AdtOutputReport = "T_AdtOutputReport";
  protected $_checkSeikyu = false;

  public $_timetoday;
  public $_today;
  public function __construct()
  {
    date_default_timezone_set('Asia/Tokyo');
    $this->_timetoday = Carbon\Carbon::now()->format("Y/m/d H:i:s");
    $this->_today = Carbon\Carbon::now()->format("Y年m月d日");
  }
  // 領収書_表示データ_P
  public function ExportRyoshu(Request $rq)
  {
    $data = $this->_getListUse($rq->WWID);
    $data["list"]->ReExport = "";
    $oldData = DB::table($this->_WaterWork)->where("WWID", $rq->WWID)->first();
    if (!$oldData->RecOutputUserID && !$oldData->RecOutputDate) {
      // 領収証出力時に、「RecOutputUserID」にログインユーザ、「RecOutputDate」にクリック時のシステム日付でUPDATEする
      DB::table($this->_WaterWork)->where("WWID", $rq->WWID)
        ->update([
          "RecOutputUserID" => Auth::user()->UserID,
          "RecOutputDate" => $this->_timetoday
        ]);
    } else {
      // ・再出力（出力ユーザID、出力日が入っている場合を行う際は、以下の処理とする。
      // 　・一般ユーザ：必ず「（再）」を付与する。
      // 　・管理者ユーザ：「（再）」の有無を選択可能とする。
      if (!$rq->flag) {
        //「（再）」を 付与する
        $data["list"]->ReExport = "（再）";
      }
    }
    //Excel出力のログ
    $this->ExportLog("05", $rq->WWID);

    $pdf = PDF::loadView('matter.export.ryoshu', $data);
    return $pdf->download('領収証.pdf');
  }
  // 納品書_表示データ_P
  public function ExportNohin(Request $rq)
  {
    $data = $this->_getListUse($rq->WWID);
    // return view('matter.export.nohin',  $data);

    //Excel出力のログ
    $this->ExportLog("04", $rq->WWID);

    $pdf = PDF::loadView('matter.export.nohin', $data);
    return $pdf->stream('納品書.pdf');
    return $pdf->download('納品書.pdf');
  }
  // 請求書_表示データ_P
  public function ExportSeikyu(Request $rq)
  {
    $this->_checkSeikyu = true;
    $data = $this->_getListUse($rq->WWID);

    //Excel出力のログ
    $this->ExportLog("03", $rq->WWID);
    $pdf = PDF::loadView('matter.export.seikyu', $data);
    return $pdf->download('請求書.pdf');
  }
  private function _getListUse($WWID)
  {
    $listuse = T_UseMaterial::select("ud.MaterialNM", "ud.Type", "ud.SellPrice", "sys.DispText as UseUnitNM", "ud.CustomMaterialUnit as UseUnitNM999")
      ->selectRaw(" COALESCE(floor(ud.UseNum*ud.SellPrice),0) as total")
      ->selectRaw("REPLACE(ud.UseNum,'.0','') UseNum")
      ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
      ->leftJoin("M_Material as mm", "mm.MaterialID", "ud.MaterialID")
      ->leftJoin('M_System as sys', function ($join) {
        $join->on('sys.InternalValue', 'mm.UseUnitCD');
        $join->where('sys.SystemCD', M_System::$UseUnitCD);
      })
      ->where("T_UseMaterial.WWID", $WWID)
      ->where("ud.UseNum", ">", 0)
      ->orderBy("T_UseMaterial.ConstructDate")
      ->get()->toArray();
    return [
      "list" => $this->_getList($WWID),
      "listuse" => $listuse
    ];
  }
  private function _getList($WWID)
  {

    $list = T_WaterWorkAsWW::select("WW.ClaimBuilding", "WW.ClaimAdress", "WW.PaymentDate","WW.ReqAdress","WW.ReqBuilding", "WW.ConstrAdress", "WW.ConstrBuilding", "WW.WWRecID as WWID", "WW.ClaimName",  "WW.ReqName", "WW.TechFee", "WW.TravelFee", "WW.SurveyFee", "WW.DisposalFee", "WW.Discount", "WW.ReqWaterNo", "WW.LeakagePoint", "WW.Others")
      ->selectRaw("FORMAT(WW.WWDateTime, 'yyyy年MM月dd日') AS WWDateTime")
      ->selectRaw("FORMAT(WW.PaymentDate, 'yyyy年MM月dd日') AS PaymentDate")
      ->selectRaw("FORMAT(WW.ClaimDate, 'yyyy年MM月dd日') AS ClaimDate")
      ->selectRaw("FORMAT(WW.PaymentIssueDate, 'yyyy年MM月dd日') AS PaymentIssueDate")
      ->selectRaw("FORMAT(WW.ClaimDate, 'yyyy/MM/dd/') AS ClaimDateVal")
      ->selectRaw("FORMAT(WW.PaymentIssueDate, 'yyyy/MM/dd/') AS PaymentIssueDateVal")
      ->selectRaw("(WW.ConstrAdress ) AS Address")
      ->where("WW.WWID", $WWID)->get()->first();
    if ($list) {
      if (!$list->PaymentIssueDate && $this->_checkSeikyu) {
        // ・「請求書発行日」が未入力：「請求書発行日」にシステム日付にUPDATE
        T_WaterWork::where("WWID", $WWID)->update(["PaymentIssueDate" => Carbon\Carbon::now()->format("Y/m/d")]);
      }
      $list->total =  T_UseMaterial::select("T_UseMaterial.WWID")
        ->selectRaw("COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) as totalSub")
        ->selectRaw("(COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) + ww.TechFee + ww.TravelFee)as totalAll")
        ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
        ->join("T_WaterWork as ww", "ww.WWID", "T_UseMaterial.WWID")
        ->where("T_UseMaterial.WWID", $WWID)
        ->where("ud.UseNum", ">", 0)
        ->groupBy("T_UseMaterial.WWID", "ww.TechFee", "ww.TravelFee")
        ->get()->first();
      $list->today = $this->_today;
      // ● 請求日 = NULL AND 請求書発行日 = NULL
      // 　・期限 = システム日付 +14
      $startDeadline =  Carbon\Carbon::now()->format("Y/m/d");
      if ($list->PaymentIssueDateVal != $list->ClaimDateVal) {
        if ($list->PaymentIssueDateVal && $list->ClaimDateVal) {
          // ●請求書発行日 != 請求日
          // 　・期限 = 請求書発行日 +14
          $startDeadline = $list->PaymentIssueDateVal;
        }
        if (!$list->PaymentIssueDateVal && $list->ClaimDateVal) {
          // ●請求書発行日 = NULL AND 請求日 = NOT NULL
          // 　・期限 = 請求日 +14
          $startDeadline = $list->ClaimDateVal;
        }
        if ($list->PaymentIssueDateVal && !$list->ClaimDateVal) {
          // ●請求日 = NULL AND 請求書発行日 = NOT NULL
          // 　・期限 = 請求書発行日 +14
          $startDeadline = $list->PaymentIssueDateVal;
        }
      } else {
        if ($list->PaymentIssueDateVal && $list->ClaimDateVal) {
          // ●請求書発行日 = 請求日
          // 　・期限 = 請求書発行日 +14
          $startDeadline = $list->PaymentIssueDateVal;
        }
      }
      $list->todayadd14 = Carbon\Carbon::parse($startDeadline)->addDays(14)->format("Y年m月d日");
      $list->tax = M_System::where("SystemCD", '9999999')->get()->first()->InternalValue;
      $tsub = 0;
      if ($list->total) $tsub = $list->total["totalSub"];
      $list->totalSub =  ($list->TechFee + $tsub + $list->TravelFee + $list->SurveyFee  + $list->DisposalFee - $list->Discount);
      $list->totalSubTax =  floor($list->totalSub * ($list->tax));
      $list->totalAll = floor($list->totalSub * (1 + $list->tax));
      //　工事
      $list->WorkFrom =  $this->getWorkFrom($WWID);
    }
    return $list;
  }

  // 見積書_表示データ_E
  public function ExportMitsuMore(Request $rq)
  {
    $list = T_WaterWorkAsWW::select("WW.SurveyFee", "WW.DisposalFee", "WW.Discount", "WW.Others","WW.ReqAdress","WW.ReqBuilding", "WW.ConstrAdress", "WW.ConstrBuilding", "WW.WWRecID as WWID", "WW.ClaimName", "WW.ReqName", "WW.LeakagePoint",  "WW.TechFee", "WW.TravelFee", "WW.ReqWaterNo")
      ->selectRaw("FORMAT(WW.WWDateTime, 'yyyy年MM月dd日') AS WWDateTime")
      ->selectRaw("(WW.ConstrAdress) as Address")
      ->where("WW.WWID", $rq->WWID)->get()->first();
    if ($list) {
      $list->total =  T_WaterWorkAsWW::select("WW.WWID")
        ->selectRaw("COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) as totalSub")
        ->selectRaw("(COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) + WW.TechFee + WW.TravelFee +WW.SurveyFee+ WW.DisposalFee-WW.Discount)as totalSubAll")
        ->selectRaw("FLOOR((((COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) + WW.TechFee + WW.TravelFee +WW.SurveyFee+ WW.DisposalFee-WW.Discount) * (convert(float,sys.InternalValue)))) )as totalTax")
        ->selectRaw("FLOOR((((COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) + WW.TechFee + WW.TravelFee +WW.SurveyFee+ WW.DisposalFee-WW.Discount) * (1+convert(float,sys.InternalValue))))) as totalAll")
        ->leftJoin("T_UseMaterial", "WW.WWID", "T_UseMaterial.WWID")
        ->leftJoin("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
        ->leftJoin('M_System as sys', function ($join) {
          $join->where('sys.SystemCD', M_System::$TaxCD);
        })
        ->where("WW.WWID", $rq->WWID)
        ->groupBy("WW.WWID", "WW.TechFee", "WW.TravelFee", "WW.SurveyFee", "WW.DisposalFee", "WW.Discount", "WW.Others", "sys.InternalValue")
        ->get()->first();
      $list->today = $this->_today;
      $list->todayadd30 = Carbon\Carbon::now()->addDays(30)->format("Y年m月d日");
      //　工事
      $list->WorkFrom =  $this->getWorkFrom($rq->WWID);

      //Excel出力のログ
      $this->ExportLog("02", $rq->WWID);
    }
    $listuse = T_UseMaterial::select("ud.MaterialNM", "ud.Type", "ud.UseNum", "ud.SellPrice", "sys.DispText as UseUnitNM", "ud.CustomMaterialUnit as UseUnitNM999")
      ->selectRaw("( COALESCE(floor(ud.UseNum*ud.SellPrice),0)) as total")
      ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
      ->leftJoin("M_Material as mm", "mm.MaterialID", "ud.MaterialID")
      ->leftJoin('M_System as sys', function ($join) {
        $join->on('sys.InternalValue', 'mm.UseUnitCD');
        $join->where('sys.SystemCD', M_System::$UseUnitCD);
      })
      ->where("T_UseMaterial.WWID", $rq->WWID)
      ->where("ud.UseNum", ">", 0)
      ->orderBy("T_UseMaterial.ConstructDate")
      ->get()->toArray();
    $template = "mitsumore.xlsx";
    return Excel::download(new mitsumore($list, $listuse, $template), '見積書.xlsx');
  }

  // 使用資材内訳
  public function ExportUchiWake(Request $rq)
  {
    $list = T_WaterWorkAsWW::select("WW.ConstrAdress", "WW.ConstrBuilding","WW.ReqAdress","WW.ReqBuilding", "WW.WWRecID as WWID", "WW.ClaimName", "WW.LeakagePoint", "WW.ReqName")
      ->selectRaw("FORMAT(WW.WWDateTime, 'yyyy年MM月dd日') AS WWDateTime")
      ->selectRaw("(WW.ConstrAdress ) as Address")
      ->where("WW.WWID", $rq->WWID)->get()->first();
    if ($list) {
      $totalAll =  T_UseMaterial::select("T_UseMaterial.WWID")
        ->selectRaw("COALESCE(SUM(floor(ud.SellPrice*ud.UseNum)),0) as totalAll")
        ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
        ->where("T_UseMaterial.WWID", $rq->WWID)
        ->where("ud.UseNum", ">", 0)
        ->groupBy("T_UseMaterial.WWID")
        ->get()->first();
      $list->totalAll = 0;
      if ($totalAll)
        $list->totalAll = $totalAll["totalAll"];

      //　工事
      $list->WorkFrom =  $this->getWorkFrom($rq->WWID);

      //Excel出力のログ
      $this->ExportLog("06", $rq->WWID);
    }
    $listuse = T_UseMaterial::select("ud.MaterialNM", "ud.Type", "ud.UseNum", "ud.SellPrice", "sys.DispText as UseUnitNM", "ud.CustomMaterialUnit as UseUnitNM999")
      ->selectRaw(" COALESCE(floor(ud.UseNum*ud.SellPrice),0) as total")
      ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
      ->leftJoin("M_Material as mm", "mm.MaterialID", "ud.MaterialID")
      ->leftJoin('M_System as sys', function ($join) {
        $join->on('sys.InternalValue', 'mm.UseUnitCD');
        $join->where('sys.SystemCD', M_System::$UseUnitCD);
      })
      ->where("T_UseMaterial.WWID", $rq->WWID)
      ->orderBy("T_UseMaterial.ConstructDate")
      ->get()->toArray();
    $template = "uchiwake.xlsx";
    return Excel::download(new uchiwake($list, $listuse, $template), '使用資材内訳.xlsx');
  }

  // 工事日を取る
  private function getWorkFrom(int $id)
  {

    $sql = "WITH A as (
      SELECT FORMAT(WorkFrom, 'yyyy年MM月dd日') AS WorkFrom
      FROM T_Work
      where WWID = $id
      AND flgOutputWorkDate = 1
    )
    SELECT TOP 9 * FROM A
    GROUP BY A.WorkFrom
    ORDER BY WorkFrom";
    return DB::select($sql);
  }

  // 給排水設備修繕受付書_表示データ_E
  public function ExportUketsuke(Request $rq)
  {
    $list = $this->_getUketsuke($rq->WWID);
    if ($list) {
      $list->timetoday = $this->_timetoday;
      $list->worktype = T_Work::select("sys.DispText as TargetTypeNM", "wt.DispText as WorkTypeNM")
        ->selectRaw("SUBSTRING(DATENAME(weekday,[T_Work].WorkFrom),1,1) AS WorkFromDay")
        ->selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy年MM月dd日') AS WorkFrom")
        ->selectRaw("FORMAT([T_Work].WorkFrom, 'HH時mm分')+'-'+FORMAT([T_Work].WorkTo, 'HH時mm分') AS WorkTimeFM")
        ->leftJoin('M_System as sys', function ($join) {
          $join->on('sys.InternalValue', 'T_Work.TargetType');
          $join->where('sys.SystemCD', M_System::$TargetTypeCD);
        })
        ->join('M_System as wt', function ($join) {
          $join->on('wt.InternalValue', 'T_Work.WorkType');
          $join->where('wt.SystemCD', M_System::$WorkTypeCD);
        })
        ->where("T_Work.WWID", $rq->WWID)
        ->orderBy("T_Work.WorkFrom")
        ->get()->first();
      $list->DoneDay = null;
      if ($list->WorkStatus == "02") {
        $doneday = T_Work::selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy年MM月dd日') AS WorkFrom")
          ->where("T_Work.WWID", $rq->WWID)
          ->orderBy("T_Work.WorkFrom", "DESC")
          ->get()->first();
        if ($doneday) {
          $list->DoneDay = $doneday["WorkFrom"];
        }
      }
      $tax = M_System::where("SystemCD", '9999999')->get()->first();
      $SubTotal = T_UseMaterial::selectRaw("REPLACE(SUM(floor(ud.UseNum * ud.SellPrice)),'.0','')as SubTotal")
        ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
        ->where("T_UseMaterial.WWID", $rq->WWID)
        ->get()->first();
      if (!$SubTotal) $SubTotal = 0;
      else $SubTotal = $SubTotal->SubTotal;

      $list->total = "￥" . number_format(floor(($SubTotal + $list->TechFee + $list->TravelFee + $list->SurveyFee + $list->DisposalFee  - $list->Discount) * (1 + $tax->InternalValue)));
      $listuse = T_UseMaterial::select("ud.MaterialNM", "ud.Type", "ud.UseNum")
        ->join("T_UseMaterialDetail as ud", "ud.UseMaterialID", "T_UseMaterial.UseMaterialID")
        ->where("T_UseMaterial.WWID", $rq->WWID)
        ->orderBy("T_UseMaterial.ConstructDate")
        ->take(6)->get()->toArray();

      //Excel出力のログ
      $this->ExportLog("01", $rq->WWID);

      $template = "uketsuke.xlsx";
      return Excel::download(new uketsuke($list, $listuse, $template), '給排水設備修繕受付書.xlsx');
    } else
      return "khong co du lieu";
  }

  // 帳票、Excel出力のログ記録用テーブル
  public function ExportLog($RepType, $WWID = null)
  {
    if ($RepType) {
      $data = [
        "RepType" => $RepType,
        "OutputUserID" => Auth::user()->UserID,
        "OutputDate" => $this->_timetoday,
        "AddUserID" => Auth::user()->UserID,
        "AddDate" => $this->_timetoday,
      ];
      if ($WWID) $data["WWID"] = $WWID;
      DB::table($this->_AdtOutputReport)->insert($data);
    }
  }

  private function _getUketsuke($WWID)
  {
    $sql = "With a as ( 
            select
              [T_WorkUser].*
              , [u].[UserNM] 
            from
              [T_WorkUser] 
              left join [M_User] as [u] 
                on [u].[UserID] = [T_WorkUser].[UserID]
          ) 
          , b as ( 
            select DISTINCT
              row_number() over ( 
                partition by
                  [T_Work].WWID 
                order by
                  [T_Work].WorkFrom 
              ) as rank2
              , [T_Work].*
              , ( 
                SELECT
                  a.UserNM + ',' 
                FROM
                  a 
                WHERE
                  a.WWID = [T_Work].WWID 
                  and a.WorkID = [T_Work].WorkID FOR XML PATH ('')
              ) AS UserNMs
              , [wt].[DispText] as [WorkTypeNM]
              , [tt].[DispText] as [TargetTypeNM]
              , [wp].[DispText] as [WorkPlaceNM] 
            from
              [T_Work] 
              inner join [M_System] as [wt] 
                on [wt].[SystemCD] = 000021 
                and [wt].[InternalValue] = [T_Work].[WorkType] 
              left join [M_System] as [tt] 
                on [tt].[SystemCD] = 000022 
                and [tt].[InternalValue] = [T_Work].[TargetType] 
              left join [M_System] as [wp] 
                on [wp].[SystemCD] = 000023 
                and [wp].[InternalValue] = [T_Work].[WorkPlace]
          ) 
          , c as ( 
            SELECT
              * 
            FROM
              ( 
                SELECT
                  *
                  , row_number() over ( 
                    partition by
                      [b].WWID 
                    order by
                      [b].WorkFrom ASC
                  ) as rank 
                  , FORMAT([b].WorkFrom, 'MM/dd') AS WorkFromFM
                  , FORMAT([b].WorkFrom, 'HH時mm分') + '-' + FORMAT([b].WorkTo, 'HH時mm分') AS WorkTimeFM 
                  , CONVERT(VARCHAR(5), b.TravelTime, 108) AS TravelTimeVw
                  , CONVERT(VARCHAR(5), b.WorkTime, 108) AS WorkTimeVw
                FROM
                  b 
                WHERE
                  b.rank2 <= 5
              ) bb
          ) 
          select  DISTINCT
            WW.*
            ,CASE 
                    WHEN WW.WTelFlg = 1
                    THEN '要' 
                    ELSE '' 
                    END AS WTelFlgNM
            , FORMAT(WW.WWDateTime, 'yyyy年度') as WWDateTimeYYYY
            , FORMAT(WW.WWDateTime, 'yyyy年MM月dd日') as WWDateTimeY
            , FORMAT(WW.WWDateTime, 'HH時mm分') as WWDateTimeH
            , [wh].[DispText] + WWHouseNum as WWAdressNM
            , u.UserNM as WWHandlerNM
            , ucu.UserNM as ClaimUserNM
            , ud.UserNM as DeliveryUserNM
            , ( 
              SELECT
                utemp.UserNM + ',' 
              FROM
                M_User utemp 
              WHERE
                utemp.DeleteFlg = 0
                AND utemp.ConstrCls = 1 FOR XML PATH ('')
            ) AS AllUserNM
            , [wt].[DispText] as [WWTypeNM]
            , [wr].[DispText] as [WWReceptTypeNM]
            , [wh].[DispText] as [WWHandlerIDNM]
            , [ps].[DispText] as [PaymentStatusNM]
            , [ct].[DispText] as [ClaimTypeNM]
            , b1.WORKFromFM AS WORKFrom1
            , b1.WorkTimeFM as time1
            , b1.UserNMs as UserNMs1
            , b1.WorkTypeNM as WorkTypeNM1
            , b1.TravelTimeVw as TravelTime1
            , b1.WorkTimeVw as WorkTime1
            , b1.WorkPlaceNM as WorkPlaceNM1
            , b2.WORKFromFM AS WORKFrom2
            , b2.WorkTimeFM as time2
            , b2.UserNMs as UserNMs2
            , b2.WorkTypeNM as WorkTypeNM2
            , b2.TravelTimeVw as TravelTime2
            , b2.WorkTimeVw as WorkTime2
            , b3.WORKFromFM AS WORKFrom3
            , b3.WorkTimeFM as time3
            , b3.UserNMs as UserNMs3
            , b3.WorkTypeNM as WorkTypeNM3
            , b3.TravelTimeVw as TravelTime3
            , b3.WorkTimeVw as WorkTime3
            , b4.WORKFromFM AS WORKFrom4
            , b4.WorkTimeFM as time4
            , b4.UserNMs as UserNMs4
            , b4.WorkTypeNM as WorkTypeNM4
            , b4.TravelTimeVw as TravelTime4
            , b4.WorkTimeVw as WorkTime4
            
            , b5.WORKFromFM AS WORKFrom5
            , b5.WorkTimeFM as time5
            , b5.UserNMs as UserNMs5
            , b5.WorkTypeNM as WorkTypeNM5
            , b5.TravelTimeVw as TravelTime5
            , b5.WorkTimeVw as WorkTime5
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
            left join [M_System] as [wh] 
              on [wh].[SystemCD] = '000017' 
              and [wh].[InternalValue] = WW.[WWAdress] 
            left join [M_System] as [ct] 
              on [ct].[SystemCD] = '000018' 
              and [ct].[InternalValue] = WW.[ClaimType] 
            left join [M_System] as [ps] 
              on [ps].[SystemCD] = '000019' 
              and [ps].[InternalValue] = WW.[PaymentStatus] 
            left join (SELECT * FROM c WHERE c.rank = 1) as b1 
              on WW.WWID = b1.WWID 
            left join (SELECT * FROM c WHERE c.rank = 2) as b2 
              on WW.WWID = b2.WWID 
            left join (SELECT * FROM c WHERE c.rank = 3) as b3 
              on WW.WWID = b3.WWID 
            left join (SELECT * FROM c WHERE c.rank = 4) as b4 
              on WW.WWID = b4.WWID 
            left join (SELECT * FROM c WHERE c.rank = 5) as b5 
              on WW.WWID = b5.WWID 
            left join M_User u 
              on u.UserID = WW.WWHandlerID 
            left join M_User ucu 
              on ucu.UserID = WW.ClaimUserID
            left join M_User ud 
              on ud.UserID = WW.DeliveryUserID 
          where
            WW.[FlgDelete] = 0 
            AND WW.WWRecID IS NOT NULL
            AND WW.WWID = :WWID";
    $data = ["WWID" => $WWID];
    $res =  DB::select($sql, $data);
    if ($res) return $res[0];
  }
  public function MatterInput(Request $rq)
  {
    // 工事に追加
    $flgSagyo = $rq->flgSagyo;
    //案件入力画面
    $idww = $rq->idww;
    $WWID = "";
    if (!$idww) {
      $ListUseMaterial = "";
      $visible = false;
      $NumberingWWID = [
        'FlgDelete' => "0",
      ];
      $WWID = DB::table($this->_WaterWork)->insertGetId($NumberingWWID);
      // return $WWID;
    } else {
      $visible = true;
    }

    $ListUser = DB::table($this->_UserTable)->select('UserID', 'UserNM')
      ->where("DeleteFlg", "0")
      ->get()->toArray();

    // 工事区分
    $ListType =   SystemController::getSystemByCD(M_System::$WWTypeCD);
    //受付区分
    $ListRecept =   SystemController::getSystemByCD(M_System::$WWReceptCD);
    //地図
    $ListAdress =   SystemController::getSystemByCD(M_System::$WWHandlerCD);
    //支払い区分
    $ListClaimType =   SystemController::getSystemByCD(M_System::$ClaimTypeCD);
    //入金状況
    $ListPaymentStatus =   SystemController::getSystemByCD(M_System::$PaymentStatusCD);
    //作業状況
    $ListWorkStatus =   SystemController::getSystemByCD(M_System::$WorkStatusCD);
    //スケジュール作業区分
    $ListWorkType =   SystemController::getSystemByCD(M_System::$WorkTypeCD);
    //スケジュール対象区分
    $ListTargetType =   SystemController::getSystemByCD(M_System::$TargetTypeCD);
    //スケジュール作業場所
    $ListWorkPlace =   SystemController::getSystemByCD(M_System::$WorkPlaceCD);
    //消費税率
    $Tax = DB::table($this->_SystemTable)
      ->where("SystemCD", "9999999")
      ->get()->first();

    //idを元に案件情報の取得
    $WaterWork = DB::table($this->_WaterWork)
      ->where("FlgDelete", "0")
      ->where("WWID", $idww)
      ->get()->first();

    //idに紐づく工事情報の取得
    $Works = DB::table($this->_Work)
      ->select('*')
      ->selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy/MM/dd')AS WorkDate")
      ->selectRaw("FORMAT([T_Work].WorkFrom, 'HH:mm') AS WorkFromTime")
      ->selectRaw("FORMAT([T_Work].WorkTo, 'HH:mm') AS WorkTo")
      ->where("FlgDelete", "0")
      ->where("WWID", $idww)
      ->get()->toArray();

    array_multisort(array_map("strtotime", array_column($Works, "WorkDate")), SORT_ASC, $Works);
    // return $Works;

    //idに紐づく使用資材の取得
    $ListUseMaterial = DB::table($this->_UseMaterialDetail)
      ->select("T_UseMaterialDetail.MaterialID", "T_UseMaterialDetail.MaterialNM", "T_UseMaterialDetail.MaterialNM", "T_UseMaterialDetail.Type", "T_UseMaterialDetail.UseNum", "T_UseMaterialDetail.LossNum", "T_UseMaterialDetail.SellPrice")
      ->selectRaw("REPLACE(T_UseMaterialDetail.UseNum ,'.0','') UseNum")
      ->selectRaw("REPLACE(T_UseMaterialDetail.LossNum,'.0','') LossNum")
      ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
      ->where("WWID", $idww)->get();

    //資材選択画面で選択された資材の単価の合計
    $ListSellPrice = DB::table($this->_UseMaterialDetail)
      ->selectRaw("SUM(T_UseMaterialDetail.SellPrice) as total")
      ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
      ->where("WWID", $idww)->get()->first();


    foreach ($Works as $key => $Work) {
      $Works[$key]->UserID = DB::table($this->_WorkUser)->select("T_WorkUser.*", "U.UserNM")
        ->join($this->_UserTable, "U.UserID", "T_WorkUser.UserID")
        ->where("WWID", $idww)->where("WorkID", $Work->WorkID)->get(); //indentity
    }

    $WorkImages = T_WorkImg::where("FlgDelete", "0")->where("WWID", $idww)->get();
    // return $WaterWork;
    $data = [
      "WWID" => $WWID,
      "ListUser" => $ListUser,
      "WaterWork" => $WaterWork,
      "Works" => $Works,
      "ListType" => $ListType,
      "ListRecept" => $ListRecept,
      "ListAdress" => $ListAdress,
      "ListClaimType" => $ListClaimType,
      "ListPaymentStatus" => $ListPaymentStatus,
      "ListWorkStatus" => $ListWorkStatus,
      "ListWorkType" => $ListWorkType,
      "ListTargetType" => $ListTargetType,
      "ListWorkPlace" => $ListWorkPlace,
      "ListUseMaterials" => $ListUseMaterial,
      "ListSellPrice" => $ListSellPrice,
      "Tax" => $Tax,
      "visible" => $visible,
      "WorkImages" => $WorkImages,
      "flgSagyo" => $flgSagyo
    ];
    // return $Works;
    // return $WaterWork;
    return view("matter.matter", $data);
  }

  public function Matterinsert(Request $rq)
  {
    //urlの情報を取得
    $WWID = $rq->WWID;
    $WWName = $rq->WWName;
    $WWType = $rq->WWType;
    $WWDateTime = $rq->WWDateTime;
    $WWReceptType = $rq->WWReceptType;
    $WWAdress = $rq->WWAdress;
    $WWHouseNum = $rq->Chome . "-" . $rq->Address . "-" . $rq->Number;
    $WWHandlerID = $rq->WWHandlerID;
    $ReqAdress = $rq->ReqAdress;
    $ReqBuilding = $rq->ReqBuilding;
    $ReqName = $rq->ReqName;
    $ReqTEL = $rq->ReqTEL;
    $ReqContactTEL = $rq->ReqContactTEL;
    $PlugNum = $rq->PlugNum;
    $PipeSize = $rq->PipeSize;
    $ConstrAdress = $rq->ConstrAdress;
    $ConstrName = $rq->ConstrName;
    $ConstrBuilding = $rq->ConstrBuilding;
    $ConstrTEL = $rq->ConstrTEL;
    $WTelFlg = $rq->WTelFlg;
    $SchedTime = $rq->SchedTime;
    $SchedFrom = $rq->SchedFrom;
    $SchedTo = $rq->SchedTo;
    $scheduleUserID = $rq->UserID;
    $WorkStatus = $rq->WorkStatus;
    $WorkType = $rq->WorkType;
    $TargetType = $rq->TargetType;
    $WorkPlace = $rq->WorkPlace;
    $TravelTime = $rq->TravelTime;
    $WorkTime = $rq->WorkTime;
    $SurveyStatus = $rq->SurveyStatus;
    $LeakagePoint = $rq->LeakagePoint;
    $CommuConcrete = $rq->CommuConcrete;
    $CommuConst = $rq->CommuConst;
    $ProcessingStatus = $rq->ProcessingStatus;
    $flgInspectionIesults = $rq->flgInspectionIesults;
    $flgWLeakage = $rq->flgWLeakage;
    $flgWPilot = $rq->flgWPilot;
    $flgWFlood = $rq->flgWFlood;
    $flgWClean = $rq->flgWClean;
    $FlgDRepair = $rq->FlgDRepair;
    $FlgDDrainage = $rq->FlgDDrainage;
    $FlgDClean = $rq->FlgDClean;
    $Guidelines = $rq->Guidelines;
    $ClaimAdress = $rq->ClaimAdress;
    $ClaimName = $rq->ClaimName;
    $ClaimBuilding = $rq->ClaimBuilding;
    $ClaimTEL = $rq->ClaimTEL;
    $ClaimDate = $rq->ClaimDate;
    $ClaimType = $rq->ClaimType;
    $claimUserID = $rq->ClaimUserID;
    $PaymentStatus = $rq->PaymentStatus;
    $PaymentMemo = $rq->PaymentMemo;
    $PaymentDate = $rq->PaymentDate;
    $TechFee = $rq->TechFee;
    $MaterialFee = $rq->MaterialFee;
    $MaterialTravel = $rq->MaterialTravel;


    $TechFee = str_replace(",", "", $TechFee);
    $MaterialFee = str_replace(",", "", $MaterialFee);
    $MaterialTravel = str_replace(",", "", $MaterialTravel);


    //保存ボタン押下時
    if ($rq->btn == "save") {

      //エラーフォーマット
      $rulus = [
        'WWName' => 'required|max:64',
        'WWType' => 'required',
        'ReqAdress' => 'max:255',
        'ReqBuilding' => 'max:255',
        'ReqName' => 'max:64',
        'ReqTEL' => 'max:64',
        'ReqContactTEL' => 'max:64',
        'PlugNum' => 'max:64',
        'PipeSize' => 'max:64',
        'ConstrAdress' => 'max:64',
        'ConstrBuilding' => 'max:255',
        'ConstrName' => 'max:64',
        'ConstrTEL' => 'max:64',
        'LeakagePoint' => 'max:255',
        // 'Guidelines' => 'numeric',
        'ClaimAdress' => 'max:64',
        'ClaimBuilding' => 'max:255',
        'ClaimName' => 'max:64',
        'ClaimTEL' => 'max:64'
      ];

      //エラーメッセージの取得
      $message = [
        'WWName.required' => GetMessage::getMessageByID("error049"),
        'WWName.max:64' =>  GetMessage::getMessageByID("error050"),
        'WWType.required' => GetMessage::getMessageByID("error051"),
        'ReqAdress.max:255' =>  GetMessage::getMessageByID("error052"),
        'ReqBuilding.max:255' =>  GetMessage::getMessageByID("error053"),
        'ReqName.max:64' =>  GetMessage::getMessageByID("error054"),
        'ReqTEL.max:64' => GetMessage::getMessageByID("error055"),
        'ReqContactTEL.max:64' =>  GetMessage::getMessageByID("error056"),
        'PlugNum.max:64' =>  GetMessage::getMessageByID("error057"),
        'PipeSize.max:64' => GetMessage::getMessageByID("error058"),
        'ConstrAdress.max:64' =>  GetMessage::getMessageByID("error059"),
        'ConstrBuilding.max:255' =>  GetMessage::getMessageByID("error060"),
        'ConstrName.max:64' => GetMessage::getMessageByID("error061"),
        'ConstrTEL.max:64' =>  GetMessage::getMessageByID("error062"),
        'LeakagePoint.max:255' =>  GetMessage::getMessageByID("error068"),
        // 'Guidelines.numeric' => GetMessage::getMessageByID("error057"),
        'ClaimAdress.max:64' =>  GetMessage::getMessageByID("error064"),
        'ClaimBuilding.max:255' =>  GetMessage::getMessageByID("error065"),
        'ClaimName.max:64' => GetMessage::getMessageByID("error066"),
        'ClaimTEL.max:64' => GetMessage::getMessageByID("error067"),
      ];
      $validator = Validator::make($rq->all(), $rulus, $message);


      //エラー検知
      if ($validator->fails()) {
        // dd($message) ;
        return redirect()->back()->withErrors($validator);
        // return back()
        //     ->withErrors($validator)
        //     ->withInput();
      }

      $WaterWork = [
        "WWName" => $WWName,
        "WWType" => $WWType,
        "WWDateTime" => $WWDateTime,
        "WWReceptType" => $WWReceptType,
        "WWAdress" => $WWAdress,
        "WWHouseNum" => $WWHouseNum,
        "WWHandlerID" => $WWHandlerID,
        "ReqAdress" => $ReqAdress,
        "ReqBuilding" => $ReqBuilding,
        "ReqName" => $ReqName,
        "ReqTEL" => $ReqTEL,
        "ReqContactTEL" => $ReqContactTEL,
        "PlugNum" => $PlugNum,
        "PipeSize" => $PipeSize,
        "ConstrAdress" => $ConstrAdress,
        "ConstrName" => $ConstrName,
        "ConstrBuilding" => $ConstrBuilding,
        "ConstrTEL" => $ConstrTEL,
        "WTelFlg" => $WTelFlg,
        "WorkStatus" => $WorkStatus,
        "SurveyStatus" => $SurveyStatus,
        "LeakagePoint" => $LeakagePoint,
        "CommuConcrete" => $CommuConcrete,
        "CommuConst" => $CommuConst,
        "ProcessingStatus" => $ProcessingStatus,
        "flgInspectionIesults" => $flgInspectionIesults,
        "flgWLeakage" => $flgWLeakage,
        "flgWPilot" => $flgWPilot,
        "flgWFlood" => $flgWFlood,
        "flgWClean" => $flgWClean,
        "FlgDRepair" => $FlgDRepair,
        "FlgDDrainage" => $FlgDDrainage,
        "FlgDClean" => $FlgDClean,
        "Guidelines" => $Guidelines,
        "ClaimAdress" => $ClaimAdress,
        "ClaimName" => $ClaimName,
        "ClaimBuilding" => $ClaimBuilding,
        "ClaimTEL" => $ClaimTEL,
        "ClaimDate" => $ClaimDate,
        "ClaimType" => $ClaimType,
        "claimUserID" => $claimUserID,
        "PaymentStatus" => $PaymentStatus,
        "PaymentMemo" => $PaymentMemo,
        "PaymentDate" => $PaymentDate,
        "TechFee" => $TechFee,
        "MaterialFee" => $MaterialFee,
        "TravelFee" => $MaterialTravel,
        "FlgDelete" => "0"
      ];
      if (!$WWID) {
        //　新規
        $WWID = DB::table($this->_WaterWork)->insertGetId($WaterWork);
      }

      if ($WWID) {
        //更新
        DB::table($this->_WaterWork)->where("WWID", $WWID)->update($WaterWork);
        $WorkID = 1;
        $work = [];
        $Workkeys = array_keys($SchedTime);
        $WorkUser = $work = [];
        foreach ($Workkeys as $k => $value) {
          if ($SchedTime[$value] && $SchedFrom[$value] && $SchedFrom[$value] &&  $TravelTime[$value] && $WorkTime[$value] && $scheduleUserID) {
            $work[] = [
              "WWID" => $WWID,
              "WorkID" => $WorkID,
              "WorkType" => $WorkType[$value],
              "TargetType" => $TargetType[$value],
              "WorkPlace" => $WorkPlace[$value],
              "WorkFrom" => $SchedTime[$value] . " " . $SchedFrom[$value],
              "WorkTo" => $SchedTime[$value] . " " . $SchedTo[$value],
              "TravelTime" => $TravelTime[$value],
              "WorkTime" => $WorkTime[$value]
            ];
            // USER
            if (isset($scheduleUserID[$value]))
              foreach ($scheduleUserID[$value] as $u) {
                $WorkUser[] = [
                  "WWID" => $WWID,
                  "WorkID" => $WorkID,
                  "UserID" => $u
                ];
              }
            $WorkID++;
          }
        };

        //工事tableへのインサート
        T_Work::where("WWID", $WWID)->delete();
        T_Work::insert($work);
        //工事ユーザへのインサート
        T_WorkUser::where("WWID", $WWID)->delete();
        T_WorkUser::insert($WorkUser);

        // duyet add start 20210928
        // 写真追加
        $Images = [];
        if ($rq->has("ImgNote")) {
          foreach ($rq->ImgNote as $key => $note) {

            $Images[$key]["Note"] = $note;
            $Images[$key]["WWID"] = $WWID;
            $Images[$key]["AddUserID"] = Auth::user()->UserID;

            $image = $rq->file("img." . $key);
            $mime = $image->getMimeType();
            $fileName   = time() . '.' . $image->getClientOriginalExtension();
            $img = Image::make($image->getRealPath());
            $img->resize(500, 500, function ($constraint) {
              $constraint->aspectRatio();
            });
            $img->stream(); // <-- Key point
            $Images[$key]["FileName"] = $fileName;
            $Images[$key]["FilePath"] = $this->upload_s3($img, $fileName, $mime, "public");
          }
        }
        T_WorkImg::insert($Images);
        // duyet add end 20210928
      }
      return redirect()->route("matterinput", $parameters = ["idww" =>  $WWID], $status = 302, $headers = []);
    } else {
      //資材選択画面へ遷移

      return redirect()->route("usematerial", $parameters = ["WWID" =>  $WWID]);
    }
  }


  // S3 にファイルをアップロード
  private function upload_s3($image, $fileName, $mime, $folder)
  {
    $filepath = env('AWS_FOLDER') . $fileName;

    $disk = Storage::disk('s3');
    // S3 にファイルをアップロード（パスはバケットディレクトリを起点として相対パスで記述）
    $disk->put($filepath, $image, "public");

    // S3の完全URLを得る
    $url = $disk->url($filepath);
    return $url;
  }
  public function deleteMater(Request $rq)
  {
    $flag = false;
    $WWID = $rq->WWID;
    $userCls = Auth::user()->ManageCls;
    if (!$userCls) {
      //user
      // ・以下項目のいずれかが入力されている場合、「3.削除」は非活性とし、削除は管理者ユーザのみ可能とする
      // 「作業」、「請求先」、「請求」
      $data = DB::table("T_WaterWork")->where("WWID", $WWID)->get()->count();
      if (!$data) {
        //delete
        $flag = true;
      }
    } else {
      // admin -> delete
      $flag = true;
    }

    if ($flag) {
      //delete
      DB::table($this->_WaterWork)->where("WWID", $WWID)->update($data);
      return [
        "status" => 1,
        "Msg" => "削除が完了しました。"
      ];
    } else {
      return [
        "status" => 0,
        "Msg" => "削除できません。管理者ユーザのみ可能です"
      ];
    }
  }

  public function deleteWorkImg(Request $rq)
  {
    $datareturn = [
      "status" => 0,
      "Msg" => "削除できません。管理者ユーザのみ可能です"
    ];
    $ImgID = $rq->ImgID;
    if ($ImgID) {
      T_WorkImg::where("ImgID", $ImgID)->delete();
      $datareturn = [
        "status" => 1,
        "Msg" => "削除が完了しました。"
      ];
    }
    return $datareturn;
  }
}
