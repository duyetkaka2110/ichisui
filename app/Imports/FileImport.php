<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
class FileImport implements ToCollection
{
    public $data;
    /**
     * @access public
     * @param Collection $collection
     * return ExcelDataArray
     */

    public function collection(Collection $collection)
    {
        $data = ($collection->toArray());
        $this->data = $data;
    }
}
