<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use App\Helpers\Helper;

// class uketsuke implements FromView, WithEvents
class uketsuke implements WithEvents
{
    protected $list;
    protected $listuse;
    protected $template;
    public function __construct($list, $listuse, $template)
    {
        $this->listuse = $listuse;
        $this->list = $list;
        $this->template = $template;
    }
    /**
     * 「Excel出力」をクリックする時
     * @access public
     * @return \Illuminate\Support\FromView
     */

    // public function view(): View
    // {
    //     return view("order.export")->with(array("list" => $this->list, "data" => $this->data));
    // }
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path($this->template)), Excel::XLSX);
                $event->writer->getSheetByIndex(0);
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J2', $this->list->WWDateTimeYYYY); // 2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D4', $this->list->WWRecID); //3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G4', $this->list->WWDateTimeY); // 4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J4', $this->list->WWDateTimeH); //5
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D5', $this->list->WWTypeNM); //6
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G5', $this->list->WWAdressNM); //7
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J5', $this->list->WWHandlerNM); //8
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D6', $this->list->ReqAdress); //9
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D7', $this->list->ReqBuilding); //10
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D8', $this->list->ReqName); //11
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J8', $this->list->ReqTEL); //12
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D9', $this->list->ReqWaterNo); //13
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G9', $this->list->PipeSize); //14
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J9', $this->list->ReqContactTEL); //15
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D10', $this->list->ClaimAdress); //16
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D11', $this->list->ClaimName); //17
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J11', $this->list->ClaimTEL); //18
                if ($this->list->worktype) {
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('C12', $this->list->worktype["WorkFrom"]."（".$this->list->worktype["WorkFromDay"]. "）"); //19-1
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('G12', $this->list->worktype["WorkTimeFM"]); //21-1
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('D13', $this->list->worktype["WorkTypeNM"]); //21-1
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('D14', $this->list->worktype["TargetTypeNM"]); //21-1
                }
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J12', $this->list->WTelFlgNM); //20
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D15', $this->list->WWReceptNo); //23
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D16', $this->list->SurveyStatus); //23


                $event->getWriter()->getSheetByIndex(0)->setCellValue('C17', $this->list->ProcessingStatus); //24
                $event->getWriter()->getSheetByIndex(0)->setCellValue('H18', "引渡し許可者：　" . $this->list->DeliveryUserNM); //26
                $event->getWriter()->getSheetByIndex(0)->setCellValue('I24', ($this->list->flgInspectionIesults ? "☒" : "□") ."合格"); //検査結果
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J24', ($this->list->flgInspectionIesults ? "□" : "☒") ."不合格"); //検査結果
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C25', $this->list->WORKFrom1); //30-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D25', Helper::getUserNM($this->list->UserNMs1)); //30-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('E25', $this->list->TravelTime1); //30-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F25', $this->list->WorkTime1); //30-4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C26', $this->list->WORKFrom2); //31-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D26', Helper::getUserNM($this->list->UserNMs2)); //31-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('E26', $this->list->TravelTime2); //31-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F26', $this->list->WorkTime2); //31-4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C27', $this->list->WORKFrom3); //32-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D27', Helper::getUserNM($this->list->UserNMs3)); //32-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('E27', $this->list->TravelTime3); //32-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F27', $this->list->WorkTime3); //32-4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C28', $this->list->WORKFrom4); //33-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D28', Helper::getUserNM($this->list->UserNMs4)); //33-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('E28', $this->list->TravelTime4); //33-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F28', $this->list->WorkTime4); //33-4

                
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C29', $this->list->WORKFrom5); //33-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('D29', Helper::getUserNM($this->list->UserNMs5)); //33-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('E29', $this->list->TravelTime5); //33-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F29', $this->list->WorkTime5); //33-4

                $event->getWriter()->getSheetByIndex(0)->setCellValue('G26', ($this->list->flgWLeakage ? "☒" : "□") . "修繕箇所の漏水確認"); //34-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G27', ($this->list->flgWPilot ? "☒" : "□") . "パイロットの確認"); //34-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G28', ($this->list->FlgWCustomerExplan ? "☒" : "□") . "お客様への説明"); //34-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G29', ($this->list->flgWFlood ? "☒" : "□") . "蛇口の出水状況"); //34-4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G30', ($this->list->flgWClean ? "☒" : "□") . "清掃・後片付け"); //34-5
                $event->getWriter()->getSheetByIndex(0)->setCellValue('I26', ($this->list->FlgDRepair ? "☒" : "□") . "修繕箇所の確認"); //35-1
                $event->getWriter()->getSheetByIndex(0)->setCellValue('I27', ($this->list->FlgDDrainage ? "☒" : "□") . "排水状況"); //35-2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('I28', ($this->list->FlgDCustomerExplan ? "☒" : "□") . "お客様への説明"); //35-3
                $event->getWriter()->getSheetByIndex(0)->setCellValue('I29', ($this->list->FlgDClean ? "☒" : "□") . "清掃・後片付け"); //35-4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C31', ($this->list->total)); //36
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F31', $this->list->DoneDay); //37
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F32', $this->list->ClaimTypeNM); //お支払い区分
                $event->getWriter()->getSheetByIndex(0)->setCellValue('I32', $this->list->WorkPlaceNM1); //地下/地上/屋内
                $event->getWriter()->getSheetByIndex(0)->setCellValue('F33', Helper::getUserNM($this->list->UserNMs1)); //38
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J33', $this->list->Guidelines . "㎥"); //39
                $event->getWriter()->getSheetByIndex(0)->setCellValue('G34', $this->list->timetoday); //40
                return $event->getWriter()->getSheetByIndex(0);
            },

            // 書き込み直前イベントハンドラ
            BeforeWriting::class => function (BeforeWriting $event) {
                // テンプレート読み込みでついてくる余計な空シートを削除
                $event->writer->removeSheetByIndex(1);
                return;
            },
        ];
    }
}
