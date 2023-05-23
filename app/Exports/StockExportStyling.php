<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use DB;
use Session;

class StockExportStyling implements FromView, WithEvents, WithCalculatedFormulas
{
    /**
     * 「Excel出力」をクリックする時
     * @access public
     * @return \Illuminate\Support\FromView
     */

    public function view(): View
    {
        $StockYM = Session::get("StockYM");
        //SQL分は使用資材一覧表示
        $sql = str_replace(", mmat.MaterialImg ", "", Session::get("SQL"));
        $data = DB::select($sql, Session::get("dataWhere"));
        $datanew = array();
        foreach ($data as $key => $val) {
            $datanew[$val->MaterialID] = (array) $val;
        }
        foreach ($data as $key => $val) {
            $datanew[$val->MaterialID]["StoreShelf"][] = (array) $val;
        }
        $sql  = "SELECT
                      mst.StoreNM 
                    , msl.ShelfNM
                    , mst.StoreID 
                    , msl.ShelfID
                FROM
                    T_StockDetail stdtl
                INNER JOIN
                T_Stock st
                ON
                stdtl.StockID = st.StockID
                INNER JOIN 
                M_Material mmat
                ON
                stdtl.MaterialID = mmat.MaterialID
				AND mmat.DeleteFlg = 0
                INNER JOIN
                M_Store mst
                ON
                mst.StoreID = stdtl.StoreID
				AND mst.DeleteFlg = 0
                INNER JOIN
                M_Shelf msl
                ON
                msl.ShelfID = stdtl.ShelfID
				AND mst.StoreID = msl.StoreID
				AND msl.DeleteFlg = 0
                 GROUP BY
                        mst.StoreNM 
                    , msl.ShelfNM
                    , mst.StoreID 
                    , msl.ShelfID 
				ORDER BY 
				mst.StoreID
				, msl.ShelfID ";
        $dataGroup = DB::select($sql);
        $dataGroupArr = array();
        foreach ($dataGroup as $val) {
            $dataGroupArr[$val->StoreID][] = $val;
        }
        return view("stock.export")->with(array("list" => $datanew, "dataGroupArr" => $dataGroupArr, "StockYM" => $StockYM));
    }

    /**
     * Column名を取る
     * @access public
     * @return Column名
     */
    public $_data = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ");

    public function getBeforeLast($txt, $getInt)
    {
        $ColNumber = '';
        foreach ($this->_data as $key => $d) {
            if ($txt == $d) {
                $ColNumber = $key;
                break;
            }
        }
        if ($getInt)
            return $ColNumber - 1;
        else
            return $this->_data[$ColNumber - 1];
    }
    /**
     * 「Excel出力」をクリックする時デザイン設定
     * @access public
     * @return \Illuminate\Support\FromView
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setWidth(6);
                $event->sheet->getColumnDimension('B')->setWidth(15);
                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('D')->setWidth(30);
                $event->sheet->getColumnDimension('E')->setWidth(20);
                $lastRow = $event->sheet->getHighestRow();
                $lastCol = $event->sheet->getHighestColumn();
                $BeforeLast = $this->getBeforeLast($lastCol, false);
                if ($lastRow < 15) $lastRow = 20;
                // All file - set font size to 10
                $cellRange = 'A1:' . $lastCol . $lastRow;
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()
                    ->applyFromArray([
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'textRotation' => 0,
                        'wrapText' => TRUE
                    ]);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setName('ＭＳ Ｐ明朝');

                // All 倉庫,棚 - set font size to 10
                $cellRange = 'G2:' . $BeforeLast . "3";
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(8);
                // Apply array of styles to A2:Vlast cell range
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000'],
                        ],
                    ]
                ];
                $event->sheet->getDelegate()->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getRowDimension(4)->setVisible(false);
                //SUMを追加
                for ($i = 5; $i <= $lastRow; $i++) {
                    $event->sheet->getDelegate()->setCellValue($lastCol . $i, '=IF(SUM(G' . $i . ':' . $BeforeLast . $i . ')>0,SUM(G' . $i . ':' . $BeforeLast . $i . '),"")');
                }
                //COUNTAを追加
                $IntBeforeLast = $this->getBeforeLast($lastCol, true);
                for ($i = 6; $i <= $IntBeforeLast; $i++) {
                    $event->sheet->getDelegate()->setCellValue($this->_data[$i] . "1", '=COUNTA(' . $this->_data[$i] . '5:' . $this->_data[$i] . $lastRow . ')');
                }

                $event->sheet->getDelegate()->getStyle('B5:B'  . $lastRow)->getAlignment()
                    ->applyFromArray([
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ]);
                //lockcell

                $event->sheet->getDelegate()->getStyle('B5:B'  . $lastRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                $event->sheet->getDelegate()->getStyle('A1:' . $lastCol . $lastRow)->getProtection()->setLocked("protected");
                $event->sheet->getDelegate()->getStyle('B5:B'  . $lastRow)->getProtection()->setLocked("unprotected");
                $event->sheet->getDelegate()->getStyle('G5:' . $BeforeLast  . $lastRow)->getProtection()->setLocked("unprotected");
                $event->sheet->getProtection()->setPassword('airily')->setSheet(true)->setInsertRows(true);

                
                $cellRange = 'B5:E'  . $lastRow;
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()
                    ->applyFromArray([
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ]);
                $cellRange = 'B'. ($lastRow-3).':B'  . $lastRow;
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()
                    ->applyFromArray([
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ]);
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
            // 'A' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}
