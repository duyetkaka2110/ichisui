<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use App\Helpers\Helper;

class WorkExport implements WithEvents
{
    protected $list;
    protected $template;
    public function __construct($list, $template)
    {
        $this->list = $list;
        $this->template = $template;
    }
    /**
     * 「Excel出力」をクリックする時
     * @access public
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path($this->template)), Excel::XLSX);
                $sheet = $event->writer->getSheetByIndex(0);
                $startrow = $row = 2;
                foreach ($this->list as $v) {
                    $column = 'A';
                    $sheet->setCellValue($column . $row, $v->WWID); //1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWTypeNM); //2
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWDateTime); //3
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWReceptTypeNM); //4
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWReceptNo); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWAdressNM); //5
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWHouseNum); //6
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WWHandlerIDNM); //7
                    $column++;
                    $sheet->setCellValue($column . $row, $v->SurveyStatus); //8
                    // $sheet->getStyle($column . $row, $v->SurveyStatus)->getAlignment()->setWrapText(true);
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ReqAdress); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ReqBuilding); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ReqName); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ReqTEL); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ReqContactTEL); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ReqWaterNo); //10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->PipeSize); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ConstrAdress); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ConstrName); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ConstrBuilding); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ConstrTEL); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WTelFlg); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->WorkStatusNM); //15
                    $column++;
                    $sheet->setCellValue($column . $row, $v->LeakagePoint); //22
                    $column++;
                    $sheet->setCellValue($column . $row, Helper::getUserNM($v->CommuWaitMaterialNM . $v->CommuConcreteNM . $v->CommuConstNM . $v->CommuOtherNM)); //23
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ProcessingStatus); //24
                    $column++;
                    $sheet->setCellValue($column . $row, $v->DeliveryUserNM); //25
                    $column++;
                    $sheet->setCellValue($column . $row, $v->flgInspectionIesultsNM); //26
                    $column++;
                    $sheet->setCellValue($column . $row, $v->flgWLeakageNM); //22
                    $column++;
                    $sheet->setCellValue($column . $row, $v->flgWPilotNM); //22
                    $column++;
                    $sheet->setCellValue($column . $row, $v->FlgWCustomerExplanNM); //29
                    $column++;
                    $sheet->setCellValue($column . $row, $v->flgWFloodNM); //22
                    $column++;
                    $sheet->setCellValue($column . $row, $v->flgWCleanNM); //22
                    $column++;
                    $sheet->setCellValue($column . $row, $v->FlgDRepairNM); //22
                    $column++;
                    $sheet->setCellValue($column . $row, $v->FlgDDrainageNM); //22
                    $column++;
                    $sheet->setCellValue($column . $row, $v->FlgDCleanNM); //34
                    $column++;
                    $sheet->setCellValue($column . $row, $v->FlgDCustomerExplan); //35
                    $column++;
                    $GuidelinesCD = $v->Guidelines ? '㎥' : '';
                    $sheet->setCellValue($column . $row, $v->Guidelines . $GuidelinesCD); //36
                    $column++;

                    // 37
                    for ($i = 1; $i < 11; $i++) {
                        // 10 work
                        $WORKFrom = "WorkTypeNM" . $i;
                        $sheet->setCellValue($column . $row, $v->$WORKFrom); //35
                        $column++;
                        $time = "TargetTypeNM" . $i;
                        $sheet->setCellValue($column . $row, $v->$time); //35
                        $column++;
                        $UserNMs = "WorkPlaceNM" . $i;
                        $sheet->setCellValue($column . $row, $v->$UserNMs); //35
                        $column++;
                        $UserNMs = "UserNMs" . $i;
                        $sheet->setCellValue($column . $row, $v->$UserNMs); //35
                        $column++;
                        $UserNMs = "WORKFrom" . $i;
                        $sheet->setCellValue($column . $row, $v->$UserNMs); //35
                        $column++;
                        $UserNMs = "WORKTo" . $i;
                        $sheet->setCellValue($column . $row, $v->$UserNMs); //35
                        $column++;
                        $UserNMs = "TravelTime" . $i;
                        $sheet->setCellValue($column . $row, $v->$UserNMs); //35
                        $column++;
                        $UserNMs = "WorkTime" . $i;
                        $sheet->setCellValue($column . $row, $v->$UserNMs); //35
                        $column++;
                    }

                    $sheet->setCellValue($column . $row, Helper::getUserNM($v->Materials)); //DM 1 list use
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ClaimAdress); //DM 2 請求：住所
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ClaimName); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ClaimTEL); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ClaimDate); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ClaimTypeNM); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->ClaimUserNM); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->PaymentStatusNM); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->PaymentDate); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->PaymentMemo); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->totalSub); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->SurveyFee); //DM 10
                    $column++;
                    $sheet->setCellValue($column . $row, $v->TechFee); //DM20
                    $column++;
                    $sheet->setCellValue($column . $row, $v->DisposalFee); //DM 30
                    $column++;
                    $sheet->setCellValue($column . $row, $v->TravelFee); //DM 40
                    // $column++;
                    // $sheet->setCellValue($column . $row, $v->Others); //DM 1
                    $column++;
                    $manasu = $v->Discount? "-":"";
                    $sheet->setCellValue($column . $row,  $manasu . $v->Discount); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->totalSubAll); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->totalTax); //DM 1
                    $column++;
                    $sheet->setCellValue($column . $row, $v->totalAll); //DM 1
                    $column++;
                    $row++;
                }
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
