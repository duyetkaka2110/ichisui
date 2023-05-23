<?php

/** 勤怠編集画面 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Illuminate\Support\Facades\Validator;
use Carbon;
use App\Models\T_Schedule;
use App\Models\T_ScheduleUser;

class KintaiController extends Controller
{
    /** テーブル情報を変数に格納 */
    protected $_EventTable = "M_Event";
    protected $_UserTable = "M_User as U";
    protected $_SystemTable = "M_System";
    protected $_ScheduleTable = "T_Schedule";
    protected $_ScheduleUserTableSU = "T_ScheduleUser as SU";
    protected $_ScheduleUserTable = "T_ScheduleUser";

    public function Kintaiedit(Request $rq)
    {

        $url = url()->previous();
        $prevroute = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
        if ($prevroute != "b.schedule.day" && $prevroute != "b.schedule.week") {
            $prevroute = "b.schedule.day";
        }
        /** URLのスケジュールID取得 */
        $idSched = $rq->idSched;
        $SchedID = "";

        /** 新規登録の判断 */
        if (!$idSched) {
            $visible = false;
            $SchedFromTime = "08:00";
            $SchedToTime = "17:00";
        } else {
            $visible = true;
            $SchedFromTime = "";
            $SchedToTime = "";
        }

        $err = '';

        /** ユーザー情報を取得 */
        $ListUser = DB::table($this->_UserTable)->select('UserID', 'UserNM')->where("DeleteFlg", "0")->orderBy("SeqNo", 'asc')->get()->toArray();
        $ListScheduleChecked = DB::table($this->_ScheduleUserTableSU)->select('SU.SchedID', 'SU.UserID', 'U.UserNM')
            ->join($this->_UserTable, 'SU.UserID', '=', 'U.UserID')
            ->where("SchedID", $idSched)->get()->toArray();

        /** スケジュール情報を取得 */
        $ListSchedule = DB::table($this->_ScheduleTable)->select('SchedID', 'SchedName')
            ->where("FlgDelete", "0")
            ->get()->toArray();

        /** 勤怠区分の取得 */
        $ListEventCls = DB::table($this->_SystemTable)->select('InternalValue', 'DispText')
            ->where("SystemCD", "000014")
            ->get()->toArray();

        /** URLのIDを元にスケジュール情報を取得*/
        $Schedule = DB::table($this->_ScheduleTable)
            ->select('*')
            ->selectRaw("FORMAT([T_Schedule].SchedFrom, 'HH:mm') AS SchedFromTime")
            ->selectRaw("FORMAT([T_Schedule].SchedTo, 'HH:mm') AS SchedToTime")
            ->selectRaw("FORMAT([T_Schedule].SchedFrom, 'yyyy/MM/dd') AS SchedFrom")
            ->selectRaw("FORMAT([T_Schedule].SchedTo, 'yyyy/MM/dd') AS SchedTo")
            ->where("FlgDelete", "0")
            ->where("SchedID", $idSched)
            ->get()->first();
        // return $Schedule;

        /** 変数の初期化(新規の場合) */
        if ($Schedule) {
            $maxSchedId = "";
        }

        $Schedule =  (object)$Schedule;

        /** Viewに各変数の情報を渡す */
        $data = [
            "ListUser" => $ListUser,
            "ListSchedule" => $ListSchedule,
            "ListEventCls" => $ListEventCls,
            "Schedule" => $Schedule,
            "ListScheduleChecked" => $ListScheduleChecked,
            "ErrMsg" => $err,
            "SchedID" => $SchedID,
            "visible" => $visible,
            "SchedFromTime" => $SchedFromTime,
            "SchedToTime" => $SchedToTime,
            "prevroute" => $prevroute
        ];

        /** Viewを返す */
        return view("Kintai.Kintai",  $data);
    }

    public function Kintaiinsert(Request $rq)
    {
        $date = null;
        if ($rq->btn == "save") {
            /**  URLのパラメータを変数に格納 */

            $SchedID = $rq->SchedID;
            $UserID = $rq->UserID;
            $SchedType = $rq->SchedType;
            $SchedName = $rq->SchedName;
            $SchedFrom = $rq->SchedFrom . " " . $rq->SchedFromTime;
            $SchedTo = $rq->SchedTo . " " . $rq->SchedToTime;
            $SchedNote = $rq->SchedNote;
            /** 各変数を配列に格納 */
            $Schedule = [
                "SchedType" => $SchedType,
                "SchedName" => $SchedName,
                "SchedFrom" => $SchedFrom,
                "SchedTo" => $SchedTo,
                "SchedNote" => $SchedNote,
                "FlgDelete" => "0"

            ];
            if ($SchedID) {
                /** update処理 */
                $Scheduleold = DB::table($this->_ScheduleTable)->where("SchedID", $SchedID)->get()->first();
                if ($Scheduleold) {
                    $Schedule["UpdateUserID"] =  Auth::user()->UserID;
                    $Schedule["UpdateDate"] =  date("Y-m-d H:i:s");
                    T_Schedule::where("SchedID", $SchedID)->update($Schedule);
                    DB::table($this->_ScheduleUserTable)->where("SchedID", $SchedID)->delete();
                    if ($UserID) {
                        $Dateinsert = [];
                        /** ScheduleUserTableへのインサート */
                        foreach ($UserID as $insertUserID) {
                            $Dateinsert[] = [
                                "SchedID" => $SchedID,
                                "UserID" => $insertUserID,
                                "AddUserID" => Auth::user()->UserID,
                                "AddDate" => date("Y-m-d H:i:s"),
                                "UpdateUserID" => Auth::user()->UserID,
                                "UpdateDate" => date("Y-m-d H:i:s")
                            ];
                        }
                        T_ScheduleUser::insert($Dateinsert);
                    }
                } else {
                    $data = [
                        "ErrMsg" => "あああ"
                    ];
                    /** Viewを返す */
                    return back()->withErrors($data);
                }
            } else {
                /** Insert処理 */
                $SchedID = T_Schedule::insertGetId($Schedule);
                /** スケジュールユーザーテーブルへのInsert */
                $this->KintaiUserInsert($UserID, $SchedID, Auth::user()->UserID, date("Y-m-d H:i:s"));
            }
            /** 複写ボタン押下時 */
        } else if ($rq->btn == "copy") {
            /** URLのパラメータを変数に格納 */
            $SchedID = $rq->SchedID;
            $UserID = $rq->UserID;
            $SchedType = $rq->SchedType;
            $SchedName = $rq->SchedName;
            $SchedFrom = $rq->SchedFrom . " " . $rq->SchedFromTime;
            $SchedTo = $rq->SchedTo . " " . $rq->SchedToTime;
            $SchedNote = $rq->SchedNote;

            /** 各変数を配列に格納 */
            $Schedule = [
                "SchedType" => $SchedType,
                "SchedName" => $SchedName,
                "SchedFrom" => $SchedFrom,
                "SchedTo" => $SchedTo,
                "SchedNote" => $SchedNote,
                "FlgDelete" => "0"
            ];
            /** スケジュールテーブルへInsert */
            T_Schedule::insert($Schedule);
            $SchedID = DB::table($this->_ScheduleTable)->max("SchedID");
            /** スケジュールユーザーテーブルへのInsert */
            $this->KintaiUserInsert($UserID, $SchedID, Auth::user()->UserID, date("Y-m-d H:i:s"));
        } else if ($rq->btn == "del") {
            /** 削除ボタン押下時 */
            /** Deleteメソッドを呼び出す */
            $this->Kintaidelete($rq);
            DB::table($this->_ScheduleTable)->where("FlgDelete", false)->max("SchedID");
        }

        // 選択日を保存する
        $date = strtotime(Carbon\Carbon::parse($rq->SchedFrom)->addDays(1)) * 1000;
        /** スケジュール画面に遷移 */
        return redirect()->route($rq->prevroute, $parameters = ["date" => $date]);
    }
    private function Kintaidelete(Request $rq)
    {
        /** Delete処理 */
        $NumberingWWID = [
            'FlgDelete' => "1",
        ];
        DB::table($this->_ScheduleTable)->where("SchedID", $rq->SchedID)->update($NumberingWWID);
    }

    private function KintaiUserInsert($UserID, $SchedID, $AddUserID, $AddDate)
    {
        if ($UserID) {
            $Dateinsert = [];
            /** ScheduleUserTableへのインサート */
            foreach ($UserID as $insertUserID) {
                $Dateinsert[] = [
                    "SchedID" => $SchedID,
                    "UserID" => $insertUserID,
                    "AddUserID" => $AddUserID,
                    "AddDate" => $AddDate
                ];
            }
            T_ScheduleUser::insert($Dateinsert);
        }
    }
    private function getValidates(Request $rq)
    {
        $validator = Validator::make($rq->all(), [
            'UserID' => 'required',
            'SchedFrom' => 'required',
            'SchedTo' => 'required',
            'SchedType' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/kintaiedit')
                ->withErrors($validator)
                ->withInput();
        } else {
            return redirect()->route("kintaiedit");
        }
    }
}
