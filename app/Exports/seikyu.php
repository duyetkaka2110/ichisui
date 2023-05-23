<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class seikyu implements FromView
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

    public function view(): View
    {
        return view("matter.export.seikyu");
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
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J5', $this->list->today); //2
                $event->getWriter()->getSheetByIndex(0)->setCellValue('B6', $this->list->ClaimName . "　　様"); //３
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C10', "￥" . $this->list->total->totalAll); //4
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C13', $this->list->WWID); //5
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C14', $this->list->WWDateTime); //6
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C15',  $this->list->ConstrAdress . $this->list->ConstrBuilding); //7
                $event->getWriter()->getSheetByIndex(0)->setCellValue('C16', $this->list->ReqName); //8

                $startrow = $row = 21;
                foreach ($this->listuse as $v) {
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . ($row), $v["MaterialNM"]); //8
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('G' . ($row), ($v["SellPrice"])); //9
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('H' . ($row), $v["UseNum"]); //10
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('I' . ($row), $v["UseUnitNM"]); //11
                    $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . ($row), ($v["total"])); //12
                    $sheet->getDelegate()->mergeCells('B' . $row . ':F' . $row);
                    $row++;
                }

                $AllBoderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ]
                ];
                $NoneBoderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                            'color' => ['argb' => 'ffffff'],
                        ],
                    ]
                ];
                // $sheet->getDelegate()->getStyle('B' . $startrow . ':K' . ($row + 3))->applyFromArray($AllBoderStyle);

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
                // $sheet->getDelegate()->getStyle('H' . $startrow . ':H' . $row)->applyFromArray($styleArrayRight);
                // $sheet->getDelegate()->getStyle('I' . $startrow . ':I' . $row)->applyFromArray($styleArrayLeft);

                $rowtotal = $row;
                $rowtotal++;
                // 技　　術　　料
                $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . ($rowtotal), "技　　術　　料"); //14
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . ($rowtotal), ($this->list->TechFee)); //14
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $sheet->getDelegate()->getStyle('B' . $rowtotal)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                $rowtotal++;
                // 出　　張　　費
                $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . ($rowtotal), "出　　張　　費"); //15
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . ($rowtotal), ($this->list->TravelFee)); //15
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $sheet->getDelegate()->getStyle('B' . $rowtotal)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                $rowtotal++;
                // 小　　　　　　計
                $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . ($rowtotal), "小　　　　　　計"); //16
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . ($rowtotal), ($this->list->total->totalSub)); //16
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $sheet->getDelegate()->getStyle('B' . $rowtotal)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                $rowtotal++;
                // 合計
                $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . ($rowtotal), "合　　　　　　計"); //17
                $event->getWriter()->getSheetByIndex(0)->setCellValue('J' . ($rowtotal), ($this->list->total->totalAll)); //13
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':I' . $rowtotal);
                $sheet->getDelegate()->getStyle('B' . $rowtotal)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );

                $rowtotal++;
                $event->getWriter()->getSheetByIndex(0)->setCellValue('B' . ($rowtotal), "恐れ入りますが、お支払いは　nnnn年nn月nn日　　までにお願い致します。"); //17
                $sheet->getDelegate()->mergeCells('B' . $rowtotal . ':K' . $rowtotal);

                $sheet->getDelegate()->getStyle('A1:K'.$rowtotal)->getFont()->setName('ipag');
                // $sheet->getDelegate()->getStyle('A1:K'.$rowtotal)->applyFromArray($NoneBoderStyle);
                // $sheet->setAllBorders('none');
                // $sheet->setBorder('A1:F10', 'thin');
                return $event->getWriter()->getSheetByIndex(0);
            }
        ];
    }
}
