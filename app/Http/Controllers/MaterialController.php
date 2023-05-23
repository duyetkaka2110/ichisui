<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Material;
use App\Models\W_OrderMaterial;
use App\Models\M_Supplier;
use Auth;
use DB;

class MaterialController extends Controller
{
    public function getList(Request $rq)
    {
        $listMater = M_Material::select("mmat.MaterialID", "mmat.MaterialNM", "mmat.Type", "mmat.SupplierID")
            ->selectRaw("REPLACE(cast(cast(mmats.StockNum as DECIMAL(7,1)) as float),'.0','') StockNum")
            ->selectRaw("REPLACE(cast(cast(total.OrderNumTotal as DECIMAL(7,1)) as float),'.0','') OrderNumTotal ")
            ->join('M_MaterialShelf as mmats ', function ($join) {
                $join->on('mmats.MaterialID', 'mmat.MaterialID');
                $join->where('mmats.StoreID', '999');
                $join->where('mmats.ShelfID', '999');
            })
            ->leftJoin(
                DB::raw("(SELECT　omat.MaterialID　, SUM(omat.OrderNum) as OrderNumTotal 　
        　　　　　　FROM　 T_OrderMaterial omat 　 WHERE　omat.OrderStatusCD = 2　GROUP BY　omat.MaterialID) AS total"),
                'total.MaterialID',
                'mmat.MaterialID'
            )
            ->where("mmat.MaterialID","!=", "999999")
            ->where("mmat.DeleteFlg","0");
        // 検索条件
        if ($rq->MaterialID != '')
            $listMater =  $listMater->where("mmat.MaterialID", 'like', '%' . $rq->MaterialID . '%');
        if ($rq->MaterialNM != '')
            $listMater =  $listMater->where("mmat.MaterialNM", 'like', '%' . $rq->MaterialNM . '%');
        if ($rq->Type != '')
            $listMater =  $listMater->where("mmat.Type", 'like', '%' . $rq->Type . '%');

        $listMater =  $listMater->paginate(50)->toArray();

        $datasend = $this->renderList($listMater);
        
        return $datasend;
    }
    private function renderList($data)
    {
        $html = $page = "";
        if ($data) {
            $listSupplier = M_Supplier::select("SupplierID", "SupplierNM")->where("DeleteFlg", 0)->get();

            foreach ($data["data"] as $d) {
                $sup = "";
                foreach ($listSupplier as $s) {
                    $selected = "";
                if ($s->SupplierID == $d["SupplierID"]) $selected = "selected";
                    $sup .= '<option  ' . $selected . ' value="' . $s->SupplierID . '">' . $s->SupplierNM . '</option>';
                }
                $html .= '<tr>
                    <td scope="col" class="">' . $d["MaterialID"] . '</td>
                    <td scope="col" class="">' . $d["MaterialNM"] . '</td>
                    <td scope="col" class="">' . $d["Type"] . '</td>
                    <td scope="col" class="">
                        <select class="form-control d-inline  p-0">
                        ' . $sup . '
                        </select>
                    </td>
                    <td scope="col" class="text-right">' . $d["StockNum"] . '</td>
                    <td scope="col" class="text-right">' . $d["OrderNumTotal"] . '</td>
                      </tr>';
            }

            foreach ($data["links"] as $k => $l) {
                if (($k != 0) && ($k != (count($data["links"]) - 1))) {
                    $cls = "";
                    if ($l["active"]) {
                        $cls = " active ";
                    } else {
                        if ($l["label"] != "...") {
                            $cls = " page-select ";
                        }
                    }
                    $page .= '<li page="' . $l["label"] . '" class=" page-item ' . $cls . (($l["label"] == "...") ? " disabled" : "") . ' "><span class="page-link" >' . $l["label"] . '</span></li>';
                }
            }
        }
        return [
            "status" => 1,
            "html" => $html,
            "page" => $page
        ];
    }
}
