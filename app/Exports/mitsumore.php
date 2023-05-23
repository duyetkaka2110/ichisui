<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class mitsumore implements WithEvents
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
     * @return WithEvents
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path($this->template)), Excel::XLSX);
                $sheet = $event->writer->getSheetByIndex(0);
                $sheet->setCellValue('J5', $this->list->today); //2
                $sheet->setCellValue('B6', $this->list->ClaimName . "　　様"); //３
                $sheet->setCellValue('C10', "￥" . (number_format($this->list->total ? $this->list->total["totalSubAll"] : '') . ".-"));
                $sheet->setCellValue('C8',  $this->list->ReqWaterNo); //4
                $sheet->setCellValue('C13', $this->list->WWID); //5
                $sheet->setCellValue('C14', $this->list->WWDateTime); //6
                $sheet->setCellValue('C15', $this->list->ReqAdress); //7
                $sheet->setCellValue('C16', $this->list->ReqBuilding); //7
                $sheet->setCellValue('C17', $this->list->LeakagePoint); //8

                $startrow = $row = 21;
                foreach ($this->listuse as $v) {
                    $sheet->setCellValue('B' . ($row), $v["MaterialNM"] . " " . $v["Type"]); //8
                    $sheet->setCellValue('G' . ($row), number_format($v["SellPrice"])); //9
                    $sheet->setCellValue('H' . ($row), str_replace(".0", "", number_format($v["UseNum"], 1))); //10
                    $sheet->setCellValue('I' . ($row), $v["UseUnitNM"] ? $v["UseUnitNM"] : $v["UseUnitNM999"]); //11
                    $sheet->setCellValue('J' . ($row), number_format($v["total"])); //12
                    $sheet->getDelegate()->mergeCells('B' . $row . ':F' . $row);
                    $row++;
                }

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ]
                ];
                $sheet->getDelegate()->getStyle('B' . $startrow . ':K' . ($row + 6))->applyFromArray($styleArray);

                $styleArrayLeft = [
                    'borders' => [
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                            'color' => ['argb' => '000'],
                            'width' => '1000px'
                        ]
                    ]
                ];
                $styleArrayRight = [
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                            'color' => ['argb' => '000'],
                            'width' => '1000px'
                        ]
                    ]
                ];
                $row--;
                $sheet->getDelegate()->getStyle('H' . $startrow . ':H' . $row)->applyFromArray($styleArrayRight);
                $sheet->getDelegate()->getStyle('I' . $startrow . ':I' . $row)->applyFromArray($styleArrayLeft);
                $sheet->getDelegate()->getStyle('I' . $startrow . ':I' . $row)
                    ->getAlignment()->applyFromArray(
                        array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                    );

                $sheet->getDelegate()->getStyle('G' . $startrow . ':H' . $row)
                    ->getAlignment()->applyFromArray(
                        array('horizontal' => Alignment::HORIZONTAL_RIGHT,)
                    );

                $rowtotal = $row;
                $rowtotal++;
                $rowmerge = $rowtotal;
                // 材料費
                $sheet->setCellValue('B' . ($rowtotal), "材　　料　　費");
                $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->total ? $this->list->total["totalSub"] : ''));
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $rowtotal++;
                // 調査費
                $sheet->setCellValue('B' . ($rowtotal), "調　　査　　費");
                $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->SurveyFee));
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $rowtotal++;
                // 技　　術　　料
                $sheet->setCellValue('B' . ($rowtotal), "技　　術　　料");
                $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->TechFee));
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $rowtotal++;
                // 産廃処分費	
                $sheet->setCellValue('B' . ($rowtotal), "産　廃　処　分　費"); //15
                $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->DisposalFee)); //15
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $rowtotal++;
                // 出　　張　　費
                $sheet->setCellValue('B' . ($rowtotal), "出　　張　　費"); //15
                $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->TravelFee)); //15
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $rowtotal++;
                // その他
                // $sheet->setCellValue('B' . ($rowtotal), "そ　　の　　他"); //15
                // $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->Others)); //15
                // $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                // $rowtotal++;

                if ($this->list->Discount) {
                    // 出精値引き	
                    $sheet->setCellValue('B' . ($rowtotal), "出　精　値　引　き"); //15
                    $sheet->setCellValue('J' . ($rowtotal), "-" . number_format($this->list->Discount)); //15
                    $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                    $rowtotal++;
                }
                // 小　　　　　　計
                $sheet->setCellValue('B' . ($rowtotal), "計"); //16
                $sheet->setCellValue('J' . ($rowtotal), number_format($this->list->total ? $this->list->total["totalSubAll"] : '')); //16
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $sheet->getDelegate()->getStyle('B' . $rowmerge . ':B' . $rowtotal)
                    ->getAlignment()->applyFromArray(
                        array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                    );
                $sheet->getDelegate()->getStyle('J' . $startrow . ':J' . $rowtotal)
                    ->getAlignment()->applyFromArray(
                        array('horizontal' => Alignment::HORIZONTAL_RIGHT,)
                    );

                $rowtotal++;
                $sheet->setCellValue('B' . ($rowtotal), "※消費税につきましては、工事完了時点での消費税率に基づき算出させていただきます。"); //17

                return $sheet;
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
