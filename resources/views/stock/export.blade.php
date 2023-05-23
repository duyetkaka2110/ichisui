<?php
$html = "";
$numberCol = 3;
foreach ($dataGroupArr as $key => $val) {
    $count = count($dataGroupArr[$key]);
    $numberCol += $count;
    $html .=  "<td style='text-align: center;vertical-align: center;' colspan='" . $count . "'>" . $val[0]->StoreNM . "</td>";
}

?>
<table>
    <thead>
        <tr>
            <td style=" text-align: center;vertical-align: left;">棚卸表</td>
            <td></td>
            <td></td>
            <td style=" text-align: center;vertical-align: right; ">{{ Session::get("StockYM") }}棚卸</td>
        </tr>
        <tr>
            <td rowspan="2" style=" text-align: center;vertical-align: center; width: 8px">No.</td>
            <td rowspan="2" style=" text-align: center;vertical-align: center; width: 13px">棚卸日</td>
            <td rowspan="2" style=" text-align: center;vertical-align: center; width: 13px">資材ID</td>
            <td rowspan="2" style=" text-align: center;vertical-align: center; width: 20px">品名</td>
            <td rowspan="2" style=" text-align: center;vertical-align: center; width: 20px">形状・寸法</td>
            <td rowspan="2" style=" text-align: center;vertical-align: center;">単位</td>
            <?php
            echo $html;
            ?>
            <td rowspan="2" style=" text-align: center;vertical-align: center;">計</td>
        </tr>
        <tr>
            <?php
            $htmlID = "";
            $ArrID = array();
            foreach ($dataGroupArr as $key => $val) {
                $count = count($dataGroupArr[$key]);
                foreach ($val as $v) {
                    $htmlID .= "<td>" . $key . "," . $v->ShelfID . "</td>";
                    $ArrID[] = $key . "," . $v->ShelfID;
                    echo "<td style=' text-align: center;vertical-align: center;'>" . $v->ShelfNM . "</td>";
                }
            }

            ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <?php echo $htmlID ?>
            <td></td>
        </tr>
        <?php
        $no = 0;
        if (isset($list) && $list) {
            foreach ($list as $l) {
                $no++;
        ?>
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ $l["StockDate"] }}</td>
                    <td>{{ $l["MaterialID"] }}</td>
                    <td>{{ $l["MaterialNM"] }}</td>
                    <td>{{ $l["Type"] }}</td>
                    <td>{{ $l["RealStockUnitNM"] }}</td>
                    <?php
                    foreach ($ArrID as $id) {
                        $value = "";
                        foreach ($l["StoreShelf"] as $ss) {
                            if ($id == $ss["StoreID"] . "," . $ss["ShelfID"]) {
                                if ($ss["RealStockNum"])
                                    $value = $ss["RealStockNum"];
                                break;
                            }
                        }
                        echo "<td>" . $value . "</td>";
                    }
                    ?>
                    <td></td>
                </tr>
        <?php
            }
        }
        ?>
        <tr>
            <td></td>
            <td style="background: #ffc71f; text-align: left;">合計</td>
            <td style="background: #ffc71f;"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="background: #ffc71f; text-align: left;">消費税</td>
            <td style="background: #ffc71f;"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="background: #ffc71f; text-align: left;">合計(税込)</td>
            <td style="background: #ffc71f;"></td>
            <td></td>
        </tr>
    </tbody>
</table>