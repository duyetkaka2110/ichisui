<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\BeforeWriting;

// class hokoku implements FromView, WithEvents
class hokoku implements WithEvents
{
    protected $list;
    protected $today;
    protected $template;
    protected $lastsearch;
    public function __construct($list, $today, $template,$lastsearch)
    {
        $this->today = $today;
        $this->list = $list;
        $this->template = $template;
        $this->lastsearch = $lastsearch;
    }
    /**
     * 「Excel出力」をクリックする時
     * @access public
     * @return \Illuminate\Support\FromView
     */

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path($this->template)), Excel::XLSX);
                $sheet = $event->writer->getSheetByIndex(0);
                $sheet->setCellValue('O4', $this->today); // 1
                $sheet->setCellValue('I12', $this->lastsearch."分の修繕について、次のとおり報告いたします。"); // 1

                $start = $row = 15;
                foreach ($this->list as $v) {
                    $sheet->setCellValue('B' . ($row), $v->WWID); //3
                    $sheet->setCellValue('C' . ($row), ($v->ConstrAdress )); //4
                    $sheet->setCellValue('E' . ($row), $v->ReqName); //5
                    $sheet->setCellValue('G' . ($row), $v->WWName); //6
                    $sheet->setCellValue('I' . ($row), $v->listuse); //7
                    $sheet->setCellValue('O' . ($row), ($v->WorkFrom)); //8
                    // if ($v->WorkStatus == '02')
                    //     $sheet->setCellValue('P' . ($row), ($v->WorkEnd)); //9
                        
                    $sheet->getDelegate()->mergeCells('C'.$row.':D'.$row);
                    $sheet->getDelegate()->mergeCells('E'.$row.':F'.$row);
                    $sheet->getDelegate()->mergeCells('G'.$row.':H'.$row);
                    $sheet->getDelegate()->mergeCells('I'.$row.':N'.$row);
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
                $sheet->getDelegate()->getStyle('B'.$start.':P' . ($row-1))->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle('B'.$start.':B' . ($row-1))->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
                $sheet->getDelegate()->getStyle('C'.$start.':N' . ($row-1))->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_LEFT,)
                );
                $sheet->getDelegate()->getStyle('O'.$start.':P' . ($row-1))->getAlignment()->applyFromArray(
                    array('horizontal' => Alignment::HORIZONTAL_CENTER,)
                );
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
