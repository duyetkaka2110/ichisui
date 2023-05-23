<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class uchiwake implements WithEvents
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
                $sheetinsert =  $event->getWriter()->getSheetByIndex(0);
                $sheetinsert->setCellValue('C8', $this->list->ClaimName . "　　様"); //2
                $sheetinsert->setCellValue('C9', $this->list->WWID); //4
                $sheetinsert->setCellValue('C10', $this->list->WWDateTime); //4

                // 施工日は、9日分（3行）まで表示
                $wftxt = "";
                $row = 11;
                $countwork = 0;
                foreach ($this->list->WorkFrom as $k => $v) {
                    $countwork = $k;
                    if ($wftxt) $wftxt .= "、";
                    if ($k == 3 || $k == 6) $wftxt = "";
                    $wftxt .= $v->WorkFrom;
                    if ($k < 3) {
                        $sheetinsert->setCellValue('C' . $row, $wftxt);
                    }
                    if ($k < 6 && $k >= 3) {
                        $row = 12;
                        $sheetinsert->setCellValue('C' . $row, $wftxt);
                        $sheetinsert->setCellValue('B' . $row, "");
                    }
                    if ($k > 5) {
                        $row = 13;
                        $sheetinsert->setCellValue('C' . $row, $wftxt);
                        $sheetinsert->setCellValue('B' . $row, "");
                    }
                }
                if ($row != 11) {
                    $sheetinsert->getStyle('C11:C' . $row)->getFont()->setSize(10);
                }
                $sheet->getDelegate()->mergeCells('B11:B' . $row);
                $row++;
                $sheetinsert->setCellValue('B' . $row, "施工場所");
                $sheetinsert->setCellValue('C' . $row,  $this->list->ReqAdress); //6
                $sheet->getDelegate()->mergeCells('C' . $row . ':G' . $row);
                $sheet->getDelegate()->mergeCells('B' . $row . ':B' . ($row + 1));
                $row++;
                $sheetinsert->setCellValue('C' . $row,  $this->list->ReqBuilding); //6
                $sheet->getDelegate()->mergeCells('C' . $row . ':G' . $row);
                $row++;
                $sheetinsert->setCellValue('B' . $row, "施工内容");
                $sheetinsert->setCellValue('C' . $row, $this->list->LeakagePoint); //7
                $sheet->getDelegate()->mergeCells('C' . $row . ':G' . $row);
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ]
                ];
                $styleArrayTopNone = [
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                        ]
                    ]
                ];
                $styleArrayBottomNone = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                        ]
                    ]
                ];
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
                $sheet->getDelegate()->getStyle('B8:G' . $row)->applyFromArray($styleArray);
                if ($countwork > 2) {
                    $sheet->getDelegate()->getStyle('C11:G11')->applyFromArray($styleArrayBottomNone);
                    $sheet->getDelegate()->getStyle('C12:G12')->applyFromArray($styleArrayTopNone);
                }
                if ($countwork > 5) {
                    $sheet->getDelegate()->getStyle('C12:G12')->applyFromArray($styleArrayBottomNone);
                    $sheet->getDelegate()->getStyle('C13:G13')->applyFromArray($styleArrayTopNone);
                }

                $row += 3;

                $sheetinsert->setCellValue('B' . $row, "品　名　・　仕　様");
                $sheet->getDelegate()->mergeCells('B' . $row . ':F' . $row);
                $sheetinsert->setCellValue('G' . $row, "単価");
                $sheetinsert->setCellValue('H' . $row, "数量");
                $sheet->getDelegate()->mergeCells('H' . $row . ':I' . $row);
                $sheetinsert->setCellValue('J' . $row, "金額");
                $sheetinsert->setCellValue('K' . $row, "摘要");
                $sheet->getDelegate()->getStyle('B' . $row . ':K' . $row)->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle('B' . $row . ':K' . $row)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                $row++;
                $startrow = $row;
                foreach ($this->listuse as $v) {
                    $sheetinsert->setCellValue('B' . ($row), $v["MaterialNM"] . " " . $v["Type"]); //8
                    $sheetinsert->setCellValue('G' . ($row), number_format($v["SellPrice"])); //9
                    $sheetinsert->setCellValue('H' . ($row), str_replace(".0", "", number_format($v["UseNum"], 1))); //10
                    $sheetinsert->setCellValue('I' . ($row), $v["UseUnitNM"] ? $v["UseUnitNM"] : $v["UseUnitNM999"]); //11
                    $sheetinsert->setCellValue('J' . ($row), number_format($v["total"])); //12
                    $sheet->getDelegate()->mergeCells('B' . $row . ':F' . $row);
                    $row++;
                }
                $sheetinsert->setCellValue('B' . ($row), "計"); //13
                $sheetinsert->setCellValue('J' . ($row), number_format($this->list["totalAll"])); //13
                $sheet->getDelegate()->mergeCells('B' . $row . ':I' . $row);
                $sheet->getDelegate()->getStyle('B' . $row)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                $sheet->getDelegate()->getStyle('B' . $startrow . ':K' . $row)->applyFromArray($styleArray);

                $row--;
                $sheet->getDelegate()->getStyle('H' . $startrow . ':H' . $row)->applyFromArray($styleArrayRight);
                $sheet->getDelegate()->getStyle('I' . $startrow . ':I' . $row)->applyFromArray($styleArrayLeft);
                $sheet->getDelegate()->getStyle('I' . $startrow . ':I' . $row)->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                return $sheetinsert;
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
