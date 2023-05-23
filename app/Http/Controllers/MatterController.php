<?php

/** 案件入力画面 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Image;
use Storage;
use Log;
use App\Models\M_System;
use App\Models\T_Work;
use App\Models\T_WorkUser;
use App\Models\T_WorkImg;
use App\Models\T_UseMaterialDetail;
use App\Models\T_UseMaterial;
use Faker\Core\Number;
use Illuminate\Support\Facades\Validator;
use App\GetMessage;

class MatterController extends Controller
{
    /** テーブル情報を変数に格納 */
    protected $_WaterWork = "T_WaterWork";
    protected $_Work = "T_Work";
    protected $_WorkUser = 'T_WorkUser';
    protected $_UseMaterial = "T_UseMaterial";
    protected $_UseMaterialDetail = "T_UseMaterialDetail";
    protected $_SystemTable = "M_System";
    protected $_UserTable = "M_User as U";

    public function MatterInput(Request $rq)
    {
        // 工事に追加
        $flgSagyo = $rq->flgSagyo;
        //案件入力画面
        $WWRecID = $rq->idww;
        $ListSellPrice[] = "";
        if (!$WWRecID) {
            $ListUseMaterial = "";
            $visible = false;
            $NumberingWWID = [
                'FlgDelete' => "0",
            ];
            $WWID = DB::table($this->_WaterWork)->insertGetId($NumberingWWID);
            $DefaultDate = "00:00";
        } else {
            $visible = true;
            $DefaultDate = "00:00";

            //WWRecIDからWWIDを取る
            $WWID = DB::table($this->_WaterWork)->where("WWRecID", $WWRecID)->where("FlgDelete", "0")->value("WWID");
            if (!$WWID)
                return abort("404");
        }

        $ListUser = DB::table($this->_UserTable)->select('UserID', 'UserNM')
            ->where("DeleteFlg", "0")->orderBy("SeqNo", 'asc')
            ->get()->toArray();

        // 工事区分
        $ListType =   SystemController::getSystemByCD(M_System::$WWTypeCD, true);
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
        $ListWorkType = DB::table($this->_SystemTable)->select('InternalValue', 'DispText', 'FreeText1')
            ->where("SystemCD", "000021")
            ->get()->toArray();
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
            ->select('*')
            ->selectRaw("FORMAT([T_WaterWork].ClaimDate, 'yyyy/MM/dd')AS ClaimDate")
            ->selectRaw("FORMAT([T_WaterWork].PaymentDate, 'yyyy/MM/dd')AS PaymentDate")
            ->where("FlgDelete", "0")
            ->where("WWID", $WWID)
            ->get()->first();

        //idに紐づく工事情報の取得
        $Works = DB::table($this->_Work)
            ->select('*')
            ->selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy/MM/dd')AS WorkDate")
            ->selectRaw("FORMAT([T_Work].WorkFrom, 'HH:mm') AS WorkFromTime")
            ->selectRaw("FORMAT([T_Work].WorkTo, 'HH:mm') AS WorkTo")
            ->selectRaw("CONVERT(VARCHAR(5), [T_Work].TravelTime, 108) AS TravelTime")
            ->selectRaw("CONVERT(VARCHAR(5), [T_Work].WorkTime, 108) AS WorkTime")
            ->where("FlgDelete", "0")
            ->where("WWID", $WWID)
            ->get()->toArray();

        array_multisort(array_map("strtotime", array_column($Works, "WorkDate")), SORT_ASC, $Works);
        if ($WWID) {
            $Works = DB::table($this->_Work)
                ->select('*')
                ->selectRaw("FORMAT([T_Work].WorkFrom, 'yyyy/MM/dd')AS WorkDate")
                ->selectRaw("FORMAT([T_Work].WorkFrom, 'HH:mm') AS WorkFromTime")
                ->selectRaw("FORMAT([T_Work].WorkTo, 'HH:mm') AS WorkTo")
                ->selectRaw("CONVERT(VARCHAR(5), [T_Work].TravelTime, 108) AS TravelTime")
                ->selectRaw("CONVERT(VARCHAR(5), [T_Work].WorkTime, 108) AS WorkTime")
                ->where("FlgDelete", "0")
                ->where("WWID", $WWID)
                ->get()->toArray();

            array_multisort(array_map("strtotime", array_column($Works, "WorkDate")), SORT_ASC, $Works);
        }

        //idに紐づく使用資材の取得
        $ListUseMaterial = DB::table($this->_UseMaterialDetail)
            ->select("T_UseMaterialDetail.MaterialID", "T_UseMaterialDetail.MaterialNM", "T_UseMaterialDetail.MaterialNM", "T_UseMaterialDetail.Type", "T_UseMaterialDetail.UseNum", "T_UseMaterialDetail.LossNum", "T_UseMaterialDetail.SellPrice")
            ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.UseNum as DECIMAL(7,1)) as float),'.0','') UseNum")
            ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.LossNum as DECIMAL(7,1)) as float),'.0','') LossNum")
            ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
            ->where("WWID", $WWID)->get();

        if ($ListUseMaterial) {
            //資材選択画面で選択された資材の単価の合計
            foreach ($ListUseMaterial as $key => $Work) {
                $ListSellPrice[$key] = (int) floor($ListUseMaterial[$key]->UseNum * $ListUseMaterial[$key]->SellPrice);
            }
            if ($ListSellPrice) {
                $ListSellPrice = array_sum($ListSellPrice);
            }
        }

        foreach ($Works as $key => $Work) {
            $Works[$key]->UserID = DB::table($this->_WorkUser)->select("T_WorkUser.*", "U.UserNM")
                ->join($this->_UserTable, "U.UserID", "T_WorkUser.UserID")
                ->where("WWID", $WWID)->where("WorkID", $Work->WorkID)->get(); //indentity
        }

        $WorkImages = T_WorkImg::where("FlgDelete", "0")->where("WWID", $WWID)->get();
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
            "flgSagyo" => $flgSagyo,
            "DefaultDate" => $DefaultDate
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
        $WWReceptNo = $rq->WWReceptNo;
        $WWAdress = $rq->WWAdress;
        $WWHouseNum = $rq->Chome . "-" . $rq->Address . "-" . $rq->Number;
        $WWHandlerID = $rq->WWHandlerID;
        $ReqAdress = $rq->ReqAdress;
        $ReqBuilding = $rq->ReqBuilding;
        $ReqName = $rq->ReqName;
        $ReqNameKana = $rq->ReqNameKana;
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
        $flgOutputWorkDate = $rq->flgOutputWorkDate;
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
        $CommuWaitMaterial = $rq->CommuWaitMaterial;
        $CommuOther = $rq->CommuOther;
        $CommuConst = $rq->CommuConst;
        $ProcessingStatus = $rq->ProcessingStatus;
        $DeliveryUserID = $rq->DeliveryUserID;
        $flgInspectionIesults = $rq->flgInspectionIesults;
        $flgWLeakage = $rq->flgWLeakage;
        $flgWPilot = $rq->flgWPilot;
        $FlgWCustomerExplan = $rq->FlgWCustomerExplan;
        $flgWFlood = $rq->flgWFlood;
        $flgWClean = $rq->flgWClean;
        $FlgDRepair = $rq->FlgDRepair;
        $FlgDDrainage = $rq->FlgDDrainage;
        $FlgDClean = $rq->FlgDClean;
        $FlgDCustomerExplan = $rq->FlgDCustomerExplan;
        $Guidelines = $rq->Guidelines;
        $ClaimAdress = $rq->ClaimAdress;
        $ClaimName = $rq->ClaimName;
        $ClaimBuilding = $rq->ClaimBuilding;
        $ClaimTEL = $rq->ClaimTEL;
        $ClaimDate = $rq->ClaimDate;
        $PaymentIssueDate = $rq->PaymentIssueDate;
        $ClaimType = $rq->ClaimType;
        $claimUserID = $rq->ClaimUserID;
        $PaymentStatus = $rq->PaymentStatus;
        $PaymentMemo = $rq->PaymentMemo;
        $PaymentDate = $rq->PaymentDate;
        $TechFee = $rq->TechFee;
        $MaterialFee = $rq->MaterialFee;
        $SurveyFee = $rq->SurveyFee;
        $DisposalFee = $rq->DisposalFee;
        $Discount = $rq->Discount;
        $Others = $rq->Others;
        $MaterialTravel = $rq->MaterialTravel;
        $ReqWaterNo = $rq->ReqWaterNo;


        $TechFee = str_replace(",", "", $TechFee);
        $MaterialFee = str_replace(",", "", $MaterialFee);
        $MaterialTravel = str_replace(",", "", $MaterialTravel);
        $SurveyFee = str_replace(",", "", $SurveyFee);
        $DisposalFee = str_replace(",", "", $DisposalFee);
        $Discount = str_replace(",", "", $Discount);
        $Others = str_replace(",", "", $Others);

        foreach ($WorkType as $key => $val) {
            $WorkType[$key] = explode("&", $WorkType[$key]);
        }

        //保存ボタン押下時
        if ($rq->btn == "save" || $rq->btn == "btnPayment" || $rq->btn == "btnResetPayment") {


            $WaterWork = [
                "WWName" => $WWName,
                "WWType" => $WWType,
                "WWDateTime" => $WWDateTime,
                "WWReceptType" => $WWReceptType,
                "WWReceptNo" => $WWReceptNo,
                "WWAdress" => $WWAdress,
                "WWHouseNum" => $WWHouseNum,
                "WWHandlerID" => $WWHandlerID,
                "ReqAdress" => $ReqAdress,
                "ReqBuilding" => $ReqBuilding,
                "ReqName" => $ReqName,
                "ReqNameKana" => $ReqNameKana,
                "ReqTEL" => $ReqTEL,
                "ReqContactTEL" => $ReqContactTEL,
                "ReqWaterNo" => $ReqWaterNo,
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
                "CommuWaitMaterial" => $CommuWaitMaterial,
                "CommuOther" => $CommuOther,
                "CommuConcrete" => $CommuConcrete,
                "CommuConst" => $CommuConst,
                "ProcessingStatus" => $ProcessingStatus,
                "DeliveryUserID" => $DeliveryUserID,
                "flgInspectionIesults" => $flgInspectionIesults,
                "flgWLeakage" => $flgWLeakage,
                "FlgDCustomerExplan" => $FlgDCustomerExplan,
                "FlgWCustomerExplan" => $FlgWCustomerExplan,
                "flgWPilot" => $flgWPilot,
                "flgWFlood" => $flgWFlood,
                "flgWClean" => $flgWClean,
                "FlgDRepair" => $FlgDRepair,
                "FlgDDrainage" => $FlgDDrainage,
                "FlgDClean" => $FlgDClean,
                "Guidelines" => $Guidelines,
                "ClaimAdress" => $ClaimAdress,
                "ClaimName" => $ClaimName,
                "ClaimType" => $ClaimType,
                "ClaimBuilding" => $ClaimBuilding,
                "ClaimTEL" => $ClaimTEL,
                "ClaimDate" => $ClaimDate,
                "PaymentIssueDate" => $PaymentIssueDate,
                "claimUserID" => $claimUserID,
                "PaymentStatus" => $PaymentStatus,
                "PaymentMemo" => $PaymentMemo,
                "PaymentDate" => $PaymentDate,
                "TechFee" => $TechFee,
                "MaterialFee" => $MaterialFee,
                "TravelFee" => $MaterialTravel,
                "SurveyFee" => $SurveyFee,
                "DisposalFee" => $DisposalFee,
                "Discount" => $Discount,
                "Others" => $Others,
                "FlgDelete" => "0"

            ];

            // 入金状況を「未入金」に変更する時
            if ($rq->PaymentStatus != '02') {
                $WaterWork["RecOutputUserID"] =  null;
                $WaterWork["RecOutputDate"] =  null;
            }

            // 入金確認ボタンクリック
            if ($rq->btn == "btnPayment") {
                $WaterWork["FlgPaymentConfirm"] =  1;
            }
            // 入金確認解除ボタンクリック
            if ($rq->btn == "btnResetPayment" && Auth::user()->ManageCls) {
                // 管理者のみ「入金確認解除」
                $WaterWork["FlgPaymentConfirm"] =  0;
            }
            if ($rq->ConstructionCopyCheck == 1) {
                $WaterWork["ConstrAdress"] =  $ReqAdress;
                $WaterWork["ConstrName"] =  $ReqName;
                $WaterWork["ConstrBuilding"] =  $ReqBuilding;
                $WaterWork["ConstrTEL"] =  $ReqTEL;
            }
            if ($rq->claimCopyCheck == 1) {
                $WaterWork["ClaimAdress"] =  $ReqAdress;
                $WaterWork["ClaimName"] =  $ReqName;
                $WaterWork["ClaimBuilding"] =  $ReqBuilding;
                $WaterWork["ClaimTEL"] =  $ReqTEL;
            }

            // WWRecID新規設定
            if (!$rq->WWRecID) {
                $maxID = DB::table($this->_WaterWork)->max("WWRecID");
                $rq->WWRecID = $WaterWork["WWRecID"] =  $maxID ? ($maxID + 1) : 1;
            }

            if ($WWID) {
                //更新
                $WaterWork["UpdateUserID"] =  Auth::user()->UserID;
                $WaterWork["UpdateDate"] =  date("Y-m-d H:i:s");
                DB::table($this->_WaterWork)->where("WWID", $WWID)->update($WaterWork);
                $WorkID = 1;
                $work = [];

                if ($rq->SchedTime) {
                    $Workkeys = array_keys($SchedTime);
                    $WorkUser = $work = [];
                    foreach ($Workkeys as $k => $value) {

                        if ($SchedTime[$value] && $SchedFrom[$value] && $SchedTo[$value] && $TargetType[$value] && $WorkPlace[$value] && $TravelTime[$value] && $WorkType[$value][0] && $WorkTime[$value] && $scheduleUserID) {
                            $work[] = [
                                "WWID" => $WWID,
                                "WorkID" => $WorkID,
                                "WorkType" => $WorkType[$value][0],
                                "TargetType" => $TargetType[$value],
                                "WorkPlace" => $WorkPlace[$value],
                                "WorkFrom" => $SchedTime[$value] . " " . $SchedFrom[$value],
                                "WorkTo" => $SchedTime[$value] . " " . $SchedTo[$value],
                                "TravelTime" => $TravelTime[$value],
                                "WorkTime" => $WorkTime[$value],
                                "flgOutputWorkDate" => isset($flgOutputWorkDate[$value]) ? $flgOutputWorkDate[$value] : 0,
                                "UpdateUserID" => Auth::user()->UserID,
                                "UpdateDate" =>  date("Y-m-d H:i:s")
                            ];
                            // USER
                            if (isset($scheduleUserID[$value])) {
                                foreach ($scheduleUserID[$value] as $u) {
                                    $WorkUser[] = [
                                        "WWID" => $WWID,
                                        "WorkID" => $WorkID,
                                        "UserID" => $u,
                                        "UpdateUserID" => Auth::user()->UserID,
                                        "UpdateDate" =>  date("Y-m-d H:i:s")
                                    ];
                                }
                                $WorkID++;
                            }
                        }
                    }
                }

                //工事tableへのインサート
                T_Work::where("WWID", $WWID)->delete();
                T_Work::insert($work);
                //工事ユーザへのインサート
                T_WorkUser::where("WWID", $WWID)->delete();
                T_WorkUser::insert($WorkUser);

                // duyet add start 20210928
                // 写真追加

                $Images = [];
                $imgno = T_WorkImg::where("WWID", $WWID)->get()->count();
                if ($rq->has("ImgNote")) {
                    foreach ($rq->ImgNote as $key => $note) {

                        $Images[$key]["Note"] = $note;
                        $Images[$key]["WWID"] = $WWID;
                        $Images[$key]["AddUserID"] = Auth::user()->UserID;

                        $image = $rq->file("img." . $key);
                        $mime = $image->getMimeType();
                        $imgno++;
                        $fileName   = $WWID . "_" . $imgno . "_" . time() . '_' . $image->getClientOriginalName();
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
            return redirect()->route("matterinput", $parameters = ["idww" =>  $rq->WWRecID], $status = 302, $headers = []);
        } else if ($rq->btn == "selectMaterial") {
            //資材選択画面へ遷移
            //スケジュールテーブルのインサート
            $WorkID = 1;
            $work = [];
            if ($rq->SchedTime) {
                $Workkeys = array_keys($SchedTime);
                $WorkUser = $work = [];
                foreach ($Workkeys as $k => $value) {
                    if ($SchedTime[$value] && $SchedFrom[$value] && $SchedTo[$value] &&  $TravelTime[$value] && $WorkTime[$value] && $scheduleUserID) {
                        $work[] = [
                            "WWID" => $WWID,
                            "WorkID" => $WorkID,
                            "WorkType" => $WorkType[$value][0],
                            "TargetType" => $TargetType[$value],
                            "WorkPlace" => $WorkPlace[$value],
                            "WorkFrom" => $SchedTime[$value] . " " . $SchedFrom[$value],
                            "WorkTo" => $SchedTime[$value] . " " . $SchedTo[$value],
                            "TravelTime" => $TravelTime[$value],
                            "WorkTime" => $WorkTime[$value],
                            "AddUserID" => Auth::user()->UserID,
                            "AddDate" =>  date("Y-m-d H:i:s")
                        ];
                        // USER
                        if (isset($scheduleUserID[$value])) {
                            foreach ($scheduleUserID[$value] as $u) {
                                $WorkUser[] = [
                                    "WWID" => $WWID,
                                    "WorkID" => $WorkID,
                                    "UserID" => $u,
                                    "AddUserID" => Auth::user()->UserID,
                                    "AddDate" =>  date("Y-m-d H:i:s")
                                ];
                            }
                            $WorkID++;
                        }
                    }
                }
            }
            //工事tableへのインサート
            T_Work::where("WWID", $WWID)->delete();
            T_Work::insert($work);
            //工事ユーザへのインサート
            T_WorkUser::where("WWID", $WWID)->delete();
            T_WorkUser::insert($WorkUser);

            return redirect()->route("usematerial", $parameters = ["WWID" =>  $rq->WWRecID]);
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
            // DB::table($this->_WaterWork)->where("WWID", $WWID)->delete();
            $UseMaterials = [];
            $NumberingWWID = [
                'FlgDelete' => "1",
            ];
            //案件入力画面の削除
            DB::table($this->_WaterWork)->where("WWID", $WWID)->update($NumberingWWID);
            //削除する案件入力画面に紐づくスケジュールの削除
            DB::table($this->_Work)->where("WWID", $WWID)->update($NumberingWWID);


            //削除する案件入力の案件IDを元に選択された資材の削除＋在庫の復活
            $ListUseMaterial = DB::table($this->_UseMaterialDetail)
                ->select("T_UseMaterialDetail.MaterialID", "T_UseMaterialDetail.MaterialNM", "T_UseMaterialDetail.Type", "T_UseMaterialDetail.SellPrice", "T_UseMaterialDetail.BuyPrice")
                ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.UseNum as DECIMAL(7,1)) as float),'.0','') UseNum")
                ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.LossNum as DECIMAL(7,1)) as float),'.0','') LossNum")
                // ->selectRaw("REPLACE(cast(cast(T_UseMaterialDetail.SellPrice as DECIMAL(7,1)) as float),'.0','') SellPrice")
                ->join($this->_UseMaterial, "T_UseMaterialDetail.UseMaterialID", "T_UseMaterial.UseMaterialID")
                ->where("WWID", $WWID)->get();

            foreach ($ListUseMaterial as $k => $v) {
                $StockNum = DB::table('M_MaterialShelf')
                    ->where('MaterialID', $ListUseMaterial[$k]->MaterialID)
                    ->where('StoreID', '999')
                    ->where('ShelfID', '999')->get()->first();
                if ($StockNum) $StockNum = $StockNum->StockNum;
                else $StockNum = 0;
                $Stock =  $StockNum + ($ListUseMaterial[$k]->UseNum + $ListUseMaterial[$k]->LossNum);

                DB::table('M_MaterialShelf')
                    ->where('MaterialID', $ListUseMaterial[$k]->MaterialID)
                    ->where('StoreID', '999')
                    ->where('ShelfID', '999')
                    ->update(['StockNum' => $Stock]);

                //削除する資材の使用数とロス数をマイナス値をインサート
                $UseMaterialDateil = [
                    "MaterialID" => $ListUseMaterial[$k]->MaterialID,
                    "MaterialNM" => $ListUseMaterial[$k]->MaterialNM,
                    "Type" => $ListUseMaterial[$k]->Type,
                    "InputDate" => date("Y-m-d H:i:s"),
                    "NewFlg" => "0",
                    "AutoOrderFlg" => "0",
                    "UseNum" => "-" . $ListUseMaterial[$k]->UseNum,
                    "LossNum" => "-" . $ListUseMaterial[$k]->LossNum,
                    "BuyPrice" => $ListUseMaterial[$k]->BuyPrice,
                    "SellPrice" => $ListUseMaterial[$k]->SellPrice
                ];
                $UseMaterialDateil["AddUserID"] =  Auth::user()->UserID;
                $UseMaterialDateil["AddDate"] =  date("Y-m-d H:i:s");
                $UseMaterialDateil["UpdateUserID"] =  Auth::user()->UserID;
                $UseMaterialDateil["UpdateDate"] =  date("Y-m-d H:i:s");
                $UseMaterialID = T_UseMaterialDetail::insertGetId($UseMaterialDateil);

                $UseMaterials[] = [
                    "UseMaterialID" => $UseMaterialID,
                    "WWID" => $WWID,
                    "ConstructDate" => date("Y-m-d H:i:s"),
                    "AddUserID" => Auth::user()->UserID,
                    "AddDate" =>  date("Y-m-d H:i:s"),
                    "UpdateUserID" =>  Auth::user()->UserID,
                    "UpdateDate" =>  date("Y-m-d H:i:s")
                ];
            }

            //マイナス値用のデータをインサート
            if ($UseMaterials) T_UseMaterial::insert($UseMaterials);


            //クラウドサーバで削除
            $img = T_WorkImg::where("WWID", $WWID);
            $listimg = $img->get();
            foreach ($listimg as $i) {
                $this->deleteImgOnAws($i->FileName);
            }
            $img->delete();

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
            $img = T_WorkImg::where("ImgID", $ImgID);
            $data = $img->get()->first();
            //クラウドサーバで削除
            $this->deleteImgOnAws($data->FileName);
            // DB削除
            $img->delete();
            $datareturn = [
                "status" => 1,
                "Msg" => "削除が完了しました。"
            ];
        }
        return $datareturn;
    }

    //クラウドサーバで削除
    public function deleteImgOnAws($FileName)
    {
        Storage::disk('s3')->delete(env("AWS_FOLDER") . $FileName);
    }
}
