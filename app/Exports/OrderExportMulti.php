<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use  App\Exports\OrderExport;

class OrderExportMulti implements WithMultipleSheets //, Responsable
{
    use Exportable;
    protected $department;
    protected $logs;

    public function __construct($department, $logs)
    {
        $this->department = $department;
        $this->logs = $logs;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->department as $key => $value) {
            $sheets[] = new OrderExport($value);
        }

        return $sheets;
    }
}
