<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrderExport implements FromView, WithEvents, WithTitle
{
    protected $list;
    protected $data;
    public function __construct($list)
    {
        $this->data = $list["sup"];
        $this->list = $list["data"];
    }
    /**
     * 「Excel出力」をクリックする時
     * @access public
     * @return \Illuminate\Support\FromView
     */

    public function view(): View
    {
        return view("order.export")->with(array("list" => $this->list, "data" => $this->data));
    }

    /**
     * シート名
     * @access public
     * @return \Illuminate\Support\FromView
     */
    public function title(): string
    {
        if (!$this->data->SupplierNM)
            return "デフォルト";
        return $this->data->SupplierNM;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(14,14);
                $lastRow = $event->sheet->getHighestRow();
                if ($lastRow < 15) $lastRow = 20;
                // All headers - set font size to 14
                $event->sheet->getColumnDimension('A')->setWidth(8);
                $event->sheet->getColumnDimension('B')->setWidth(25);
                $event->sheet->getColumnDimension('C')->setWidth(13);
                $event->sheet->getColumnDimension('D')->setWidth(12);
                $event->sheet->getColumnDimension('F')->setWidth(7);
                $event->sheet->getColumnDimension('G')->setWidth(7);
                $event->sheet->getColumnDimension('H')->setWidth(7);
                $event->sheet->getColumnDimension('I')->setWidth(2);
                $cellRange = 'A1:I' . $lastRow;
                $styleCenter = [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'textRotation' => 0,
                    'wrapText' => TRUE
                ];
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()
                    ->applyFromArray($styleCenter);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setName('ＭＳ Ｐゴシック');
                $event->sheet->getDelegate()->getStyle("A2:G2")->getFont()->setSize(18);

                // Apply array of styles to B2:G8 cell range
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ]
                ];
                $event->sheet->getDelegate()->getStyle('F9:H10')->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('A14:I' . $lastRow)->applyFromArray($styleArray);

                $styleLEFT = [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ];
                $styleRIGHT = [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ];
                $event->sheet->getDelegate()->getStyle("B4:G7")->getAlignment()->applyFromArray($styleLEFT);
                $event->sheet->getDelegate()->getStyle("A12")->getAlignment()->applyFromArray($styleLEFT);
                $event->sheet->getDelegate()->getStyle("A15:B" . $lastRow)->getAlignment()->applyFromArray($styleLEFT);
                $event->sheet->getDelegate()->getStyle("A3")->getAlignment()->applyFromArray($styleRIGHT);
                $event->sheet->getDelegate()->getStyle("C3")->getAlignment()->applyFromArray($styleLEFT);
                
                // Set A1:D4 range to wrap text in cells
                $event->sheet->getDelegate()->getStyle('A1:D4')
                    ->getAlignment()->setWrapText(true);
            },
        ];
    }
}
