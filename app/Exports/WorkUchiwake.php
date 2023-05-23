<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\BeforeWriting;

// class hokoku implements FromView, WithEvents
class WorkUchiwake implements WithEvents
{
    protected $list;
    protected $today;
    protected $template;
    public function __construct($list, $today, $template)
    {
        $this->today = $today;
        $this->list = $list;
        $this->template = $template;
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
                $sheet->setCellValue('G2', $this->today); 
                $sheet->setCellValue('E6', $this->list->W_Night_Faucet); // 1
                $sheet->setCellValue('F6', $this->list->W_Holiday_Faucet); 
                $sheet->setCellValue('G6', $this->list->W_Weekday_Faucet); //3
                $sheet->setCellValue('E7', $this->list->W_Night_Supply); 
                $sheet->setCellValue('F7', $this->list->W_Holiday_Supply); 
                $sheet->setCellValue('G7', $this->list->W_Weekday_rSupply);//6 
                $sheet->setCellValue('E8', $this->list->W_Night_etc); //10
                $sheet->setCellValue('F8', $this->list->W_Holiday_etc); //11
                $sheet->setCellValue('G8', $this->list->W_Weekday_etc); //12
                $sheet->setCellValue('H6', $this->list->W_All_WaterFaucet); //13
                $sheet->setCellValue('H7', $this->list->W_All_WaterSupply); //14
                $sheet->setCellValue('H8', $this->list->W_All_etc); //16
                $sheet->setCellValue('E9', $this->list->W_All_Night); //17
                $sheet->setCellValue('F9', $this->list->W_All_Holiday); 
                $sheet->setCellValue('G9', $this->list->W_All_Weekday); 
                $sheet->setCellValue('H9', $this->list->W_All); //20
                $sheet->setCellValue('D14', $this->list->T_Weekday_Am); 
                $sheet->setCellValue('E14', $this->list->T_Weekday_Pm); 
                $sheet->setCellValue('F14', $this->list->T_Weekday_Night); //23
                $sheet->setCellValue('D15', $this->list->T_Holiday_Am); 
                $sheet->setCellValue('E15', $this->list->T_Holiday_Pm); 
                $sheet->setCellValue('F15', $this->list->T_Holiday_Night); //26
                $sheet->setCellValue('G14', $this->list->T_All_Weekday); 
                $sheet->setCellValue('G15', $this->list->T_All_Holiday); //28
                $sheet->setCellValue('D16', $this->list->T_All_Am); 
                $sheet->setCellValue('E16', $this->list->T_All_Pm); 
                $sheet->setCellValue('F16', $this->list->T_All_Night); 
                $sheet->setCellValue('G16', $this->list->T_All); 
                
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
