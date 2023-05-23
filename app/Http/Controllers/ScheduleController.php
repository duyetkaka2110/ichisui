<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon;
use App\Models\User;

class ScheduleController extends Controller
{
  protected $_today;
  protected $_timetoday;
  protected $_checkday = false;
  protected $_event = [];
  public function __construct()
  {
    date_default_timezone_set('Asia/Tokyo');
    $this->_timetoday = Carbon\Carbon::now()->format("Y/m/d H:i:s");
    $this->_today = Carbon\Carbon::now()->format("Y/m/d");
  }
  public function index(Request $rq)
  {
    if ($rq->date) {
      $selectDate = Carbon\Carbon::parse($rq->date / 1000)->addDays(1)->format("Y-m-d");
    } else {
      $selectDate = Carbon\Carbon::now()->format("Y-m-d");
    }
    return view("schedule.week", compact("selectDate"));
  }
  public function day(Request $rq)
  {
    if ($rq->date) {
      $selectDate = Carbon\Carbon::parse($rq->date / 1000)->format("Y-m-d");
    } else {
      $selectDate = Carbon\Carbon::now()->format("Y-m-d");
    }
    $resourcesData = User::select('UserID as id', 'UserNM as title')
      ->selectRaw("right( POWER(10, 5) + SeqNo, 5) as  sort")
      ->where("DeleteFlg", "0")->orderBy("SeqNo", 'asc')->get();
    return view("schedule.day", compact("resourcesData", "selectDate"));
  }
  public function getdata(Request $rq)
  {
    $this->_checkday = $rq->day;
    //検索条件データを取る
    $search = $this->_getSearch($rq);
    // 勤怠一覧
    $this->_getKintai($rq, $search);
    // スケジュール一覧
    $this->_getSchedule($rq, $search);
    return $this->_event;
  }
  /**
   * イベント情報
   * @access private
   * @param array $list
   * @return json 
   */
  private function _render(array $list, string $flag)
  {
    foreach ($list as $l) {
      $temp = [
        "start" => $l->timestart,
        "end" => $l->timeend,
        "category" => $l->CateNM,
        "fromto" => $l->fromto,
        "userNM" => Helper::getUserNM($l->UserNMs),
        "WWID" => "No." . $l->ID,
        "url" => "/kintaiedit?idSched=" . $l->ID,
        "backgroundColor" => "#fcffa6",
        "borderColor" => "#bbb"
      ];
      if ($flag == "schedule") {
        // if ($l->CateNM != "その他" && $l->CateNM != "市単価") {
        $temp["url"] = "/matterinput?idww=" . $l->ID;
        $temp["backgroundColor"] = "#95e9fe";
        // }
      }
      if ($this->_checkday) $temp["resourceId"] = $l->UserID;
      $this->_event[] = $temp;
    }
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
    $data = $dataSchedule = array();
    $datastr =  $datab = "";
    // デフォルト
    if (!$request->start &&  !$request->end) {
      $request->start = Carbon\Carbon::now()->startOfWeek()->format("Y/m/d");
      $request->end = Carbon\Carbon::now()->endOfWeek()->format("Y/m/d");
    }
    if ($request->start) {
      $request->start = Carbon\Carbon::parse($request->start / 1000);
      if (!$request->day)
        $request->start->addDays(1);
      $request->start = $request->start->format("Y/m/d");
    }
    if ($request->end) {
      $request->end = Carbon\Carbon::parse($request->end / 1000);
      if (!$request->day)
        $request->end->addDays(1);
      $request->end = $request->end->format("Y/m/d");
    }
    if ($request->start || $request->end) {
      // 週次スケジュール
      $data["start"] = $data["start2"] = $data["start3"] = $request->start;
      if ($request->start && $request->end) {
        $data["end"] = $data["end2"] =  $data["end3"] = $request->end;
      }
      // 日次スケジュール
      if ($request->day) {
        $data["start"] = $data["start2"] = $data["start3"] =  $request->end;
        $data["end"] = $data["end2"] = $data["end3"] =  $request->end . " 23:59:59";
      }
      $datastr = " WHERE ((T_Schedule.SchedFrom BETWEEN :start  AND :end) 
                      OR  (T_Schedule.SchedTo BETWEEN :start3  AND :end3 )
                      OR (T_Schedule.SchedFrom < :start2 AND T_Schedule.SchedTo > :end2))
                      AND T_Schedule.FlgDelete=0";
      $dataSchedule[] = " ((b.WorkFrom BETWEEN :start AND :end )
                    OR (b.WorkTo BETWEEN :start3 AND :end3 )
                    OR (b.WorkFrom < :start2 AND b.WorkTo > :end2) )";
    }

    //-- 検索条件.受付日:Fromのみ指定されている場合
    if ($request->UserID) {
      $data["UserID"] = '%' . $request->UserID . '%';
      $datab = " WHERE b.UserIDs LIKE :UserID";
      $dataSchedule[] = " b.UserIDs LIKE :UserID";
    }

    if ($dataSchedule) {
      $dataSchedule = " AND " . implode(" AND ", $dataSchedule);
    }
    $datareturn = [
      "data" => $data,
      "datastr" => $datastr,
      "dataSchedule" => $dataSchedule,
      "datab" => $datab
    ];
    return $datareturn;
  }
  /**
   * 勤怠情報
   * @access private
   * @param array  $search 検索条件
   * @return json 
   */
  private function _getKintai(Request $request, $search)
  {

    $sql = "With a as ( 
        select
          [T_ScheduleUser].*
          , [u].[UserNM] 
        from
          [T_ScheduleUser] 
          left join [M_User] as [u] 
            on [u].[UserID] = [T_ScheduleUser].[UserID]";
    if ($request->UserID)
      $sql .= " WHERE  [T_ScheduleUser].[UserID] = '" . $request->UserID . "'";
    $sql .= ") 
      , b as ( 
        select
          T_Schedule.SchedID as ID
          ";
    if ($this->_checkday)
      $sql .= " , SU.UserID ";
    $sql .= ", T_Schedule.SchedName 
          , FORMAT(T_Schedule.SchedFrom, 'yyyy-MM-ddTHH:mm:00') AS timestart
          , FORMAT(T_Schedule.SchedTo, 'yyyy-MM-ddTHH:mm:00') AS timeend
          , FORMAT(T_Schedule.SchedFrom, 'HH:mm') + '-' + FORMAT(T_Schedule.SchedTo, 'HH:mm') AS fromto
          , category.DispText  as CateNM
          , ( 
            SELECT
              a.UserNM + ',' 
            FROM
              a 
            WHERE
              a.SchedID = [T_Schedule].SchedID FOR XML PATH ('')
          ) AS UserNMs
          , ( 
            SELECT
              a.UserID + ',' 
            FROM
              a 
            WHERE
              a.SchedID = [T_Schedule].SchedID FOR XML PATH ('')
          ) AS UserIDs 
        from
          [T_Schedule] 
          left join [M_System] as [category] 
            on [category].[SystemCD] = '000014' 
            and [category].[InternalValue] = T_Schedule.[SchedType] ";
    if ($this->_checkday)
      $sql .= " inner join T_ScheduleUser SU
                ON SU.SchedID = T_Schedule.SchedID ";
    $sql .=  $search["datastr"] . ") 
        select　distinct
         * from b " . $search["datab"];
    $list = DB::select($sql, $search["data"]);
    $this->_render($list, "kintai");
  }

  /**
   * スケジュール情報
   * @access private
   * @param array  $search 検索条件
   * @param bool $checkday true　ユーザーIDで検索
   * @return json 
   */
  private function _getSchedule(Request $request, $search)
  {

    $sql = "With a as ( 
              select
                [T_WorkUser].*
                , [u].[UserNM] 
              from
                [T_WorkUser] 
                left join [M_User] as [u] 
                  on [u].[UserID] = [T_WorkUser].[UserID]
                  ";
    if ($request->UserID)
      $sql .= " WHERE  [T_WorkUser].[UserID] = '" . $request->UserID . "'";
    $sql .= "
            ) 
            , b as ( 
              select
                [T_Work].*
                ";
    if ($this->_checkday)
      $sql .= ", a.UserID ";
    $sql .= " 
                , ( 
                  SELECT
                    a.UserNM + ',' 
                  FROM
                    a 
                  WHERE
                    a.WWID = [T_Work].WWID 
                    and a.WorkID = [T_Work].WorkID FOR XML PATH ('')
                ) AS UserNMs 
                , ( 
                  SELECT
                    a.UserID + ',' 
                  FROM
                    a 
                  WHERE
                    a.WWID = [T_Work].WWID 
                    and a.WorkID = [T_Work].WorkID FOR XML PATH ('')
                ) AS UserIDs 
              from
                [T_Work]
                ";
    if ($this->_checkday)
      $sql .= "
                inner join a 
                  on a.WWID = [T_Work].WWID 
                  and a.WorkID = [T_Work].WorkID ";
    $sql .= "
            ) 
            select　distinct
              WW.[WWRecID] as ID
              , WW.[WWName]
              , WW.[ReqName]
              , WW.[WWType]
              , [wt].[DispText]
              , [m].[DispText] as [CateNM]
              , FORMAT([b].WorkFrom, 'yyyy-MM-ddTHH:mm:00') AS timestart
              , FORMAT([b].WorkTo, 'yyyy-MM-ddTHH:mm:00') AS timeend 
              , FORMAT([b].WorkFrom, 'HH:mm') + '-' + FORMAT([b].WorkTo, 'HH:mm') AS fromto
              , b.UserNMs 
              , b.UserIDs";
    if ($this->_checkday)
      $sql .= " , b.UserID ";
    $sql .= "
            from
              [T_WaterWork] WW 
              inner join b as b 
                on WW.WWID = b.WWID 
              left join [M_System] as [wt] 
                on [wt].[SystemCD] = '000015' 
                and [wt].[InternalValue] = WW.[WWType] 
              left join [M_System] as [m] 
                on [m].[SystemCD] = '000021' 
                and [m].[InternalValue] = b.[WorkType] 
            where
            WW.[WWRecID] IS NOT NULL
            AND WW.WorkStatus <> '03'
            AND WW.[FlgDelete] = 0
               AND b.UserIDs IS NOT NULL " . $search["dataSchedule"];
    //  echo $sql;die;
    $list = DB::select($sql, $search["data"]);
    $this->_render($list, "schedule");
  }
}
