<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon;
use App\GetMessage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;
use Excel;
use DB;

class UsersController extends Controller
{

    public $_today;
    public function __construct()
    {
        date_default_timezone_set('Asia/Tokyo');
        $this->_today = Carbon\Carbon::now()->format("Y/m/d");
    }
    // ログイン
    public function login(Request $request)
    {

        // $address = storage_path("/8.xlsx");
        // $results = Excel::toArray([], $address)[0];
        // $table = "m_statuses";
        // $sql = "INSERT INTO $table <br>(";
        // $sql .= implode( ",",$results[0]);
        // $sql .= ") <br>VALUES<br>";
        // foreach ($results as $k => $r) {
        //     if ($k > 0 ) {
        //         $sql .=  "('";
                
        // $sql .= implode( "','",$r);
        //         $sql .=  "'),<br>";
        //     }
        // }
        // echo $sql;
        // die;
        //ログインした時ホームページに展開する
        if (Auth::check()) {
            return redirect("/scheduleday");
        }
        //「ログイン」ボタンをクリックする時
        if ($request->has('BtnLogin')) {

            //エラーフォーマット
            $rulus = [
                'UserID' => 'required',
                'PassWord' => 'required',
            ];

            //エラーメッセージの取得
            $message = [
                'UserID.required' => "ユーザーIDを入力してください。",
                'PassWord.required' => "パスワードを入力してください。",
            ];

            // return $message;
            if ($request->has('BtnLogin')) {
                $validator = Validator::make($request->all(), $rulus, $message);


                //エラー検知
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                    // return back()
                    //     ->withErrors($validator)
                    //     ->withInput();
                } else {
                    //ユーザー確認
                    $UserID = $request->UserID;
                    if ($UserID) $UserID = $UserID;
                    $user = User::select("UserID", "UserNM", "ManageCls", "LoginFailure", "ImpossibleLoginFlg")
                        ->where("UserID", $UserID)
                        ->where("PassWord", $request->PassWord)
                        ->where("DeleteFlg", 0)
                        ->where("ImpossibleLoginFlg", 0)
                        ->whereDate("ActiveDateFrom", "<=", $this->_today)
                        ->whereDate("ActiveDateTo", ">=", $this->_today)
                        ->get()->first();
                    $userWhereID = User::where("UserID", $UserID);
                    if ($user) {
                        //ログファイル保存
                        Auth::login($user);
                        $userWhereID->update(["LoginFailure" => 0]);

                        //一覧画面に展開する
                        return redirect("/scheduleday");
                    } else {
                        $ErrMsg = GetMessage::getMessageByID("error003");

                        $user = $userWhereID->get()->first();
                        if ($user) {
                            $dataupdate = ["LoginFailure" => $user->LoginFailure + 1];
                            if ($dataupdate["LoginFailure"] == 5) {
                                $dataupdate["LoginFailure"] = 0;
                                $dataupdate["ImpossibleLoginFlg"] = 1;
                            }
                            if (!$user->ImpossibleLoginFlg) $userWhereID->update($dataupdate);
                            if ($user->ImpossibleLoginFlg || $user->LoginFailure == 4) $ErrMsg = "該当ユーザはロックしています。管理者に連絡してください。";
                        }
                        //エラーメッセージが出る
                        return redirect()->back()->withErrors(["ErrMsg" =>  $ErrMsg]);
                    }
                }
            }
        }
        //お知らせ
        $datasend = array();
        return view("users.login")->with($datasend);
    }


    // ログイン
    public function loginPhone(Request $request)
    {
        //ログインした時ホームページに展開する
        if (Auth::check()) {
            // return redirect("/readcard");
        }
        //「ログイン」ボタンをクリックする時
        if ($request->has('BtnLogin')) {

            //エラーフォーマット
            $rulus = [
                'UserID' => 'required',
                'PassWord' => 'required',
            ];

            //エラーメッセージの取得
            $message = [
                'UserID.required' => "ユーザーIDを入力してください。",
                'PassWord.required' => "パスワードを入力してください。",
            ];

            // return $message;
            if ($request->has('BtnLogin')) {
                $validator = Validator::make($request->all(), $rulus, $message);


                //エラー検知
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                    // return back()
                    //     ->withErrors($validator)
                    //     ->withInput();
                } else {
                    //ユーザー確認
                    $UserID = $request->UserID;
                    if ($UserID) $UserID = $UserID;
                    $user = User::select("UserID", "UserNM", "ManageCls", "LoginFailure", "ImpossibleLoginFlg")
                        ->where("UserID", $UserID)
                        ->where("PassWord", $request->PassWord)
                        ->where("DeleteFlg", 0)
                        ->where("ImpossibleLoginFlg", 0)
                        ->whereDate("ActiveDateFrom", "<=", $this->_today)
                        ->whereDate("ActiveDateTo", ">=", $this->_today)
                        ->get()->first();
                    $userWhereID = User::where("UserID", $UserID);
                    if ($user) {
                        //ログファイル保存
                        Auth::login($user);
                        $userWhereID->update(["LoginFailure" => 0]);

                        //画面に展開する
                        return redirect("/readcard");
                    } else {
                        $ErrMsg = GetMessage::getMessageByID("error003");

                        $user = $userWhereID->get()->first();
                        if ($user) {
                            $dataupdate = ["LoginFailure" => $user->LoginFailure + 1];
                            if ($dataupdate["LoginFailure"] == 5) {
                                $dataupdate["LoginFailure"] = 0;
                                $dataupdate["ImpossibleLoginFlg"] = 1;
                            }
                            if (!$user->ImpossibleLoginFlg) $userWhereID->update($dataupdate);
                            if ($user->ImpossibleLoginFlg || $user->LoginFailure == 4) $ErrMsg = "該当ユーザはロックしています。管理者に連絡してください。";
                        }
                        //エラーメッセージが出る
                        return redirect()->back()->withErrors(["ErrMsg" =>  $ErrMsg]);
                    }
                }
            }
        }
        //お知らせ
        $datasend = array();
        return view("users.loginphone")->with($datasend);
    }

    //ログアウト
    public function logout()
    {
        Auth::logout();
        //ログイン画面に展開する
        return redirect("/");
    }
}
