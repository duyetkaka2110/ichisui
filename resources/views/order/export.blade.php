<table>
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>

        <tr height="47" style='height:35.0px'>
            <td class="xl67"></td>
            <td height="47" class=xl66 colspan="1" style='height:35.0px;'>発注書</td>
            <td class="xl67"></td>
            <td class="xl67"></td>
            <td class="xl67"></td>
            <td class="xl67"></td>
            <td class="xl67"></td>
            <td class="xl67"></td>
            <td class="xl67"></td>
            <td class=""></td>
        </tr>
        <tr height="19" style='height:14.5pt'>
            <td class=""></td>
            <td height="19" class="xl69" style='height:14.5pt;'>ご担当：</td>
            <td class="" colspan="4">{{ $data->SupplierNM }}</td>
            <td colspan=2 class="xl72">{{ $data["Date"] }}</td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr height="19" style='height:14.5pt'>
            <td class="xl70"></td>
            <td height="19" class="xl69" style='height:14.5pt'></td>
            <td class="xl70"></td>
            <td class="xl70"></td>
            <td class=""></td>
            <td class=""></td>
            <td class="xl72"></td>
            <td class="xl72"></td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr height="19" style='height:14.5pt'>
            <td class="xl70"></td>
            <td height="19" class="xl69" style='height:14.5pt;'></td>
            <td class="xl70"></td>
            <td class="xl70"></td>
            <td class="xl73" style="text-align: left" colspan=5>株式会社市原水道センター</td>
            <td class=""></td>
        </tr>
        <tr height="19" style='height:14.5pt'>
            <td class="xl70"></td>
            <td height="19" class="xl69" style='height:14.5pt;'></td>
            <td class="xl70"></td>
            <td class="xl70"></td>
            <td class="xl73" style="text-align: left" colspan=5>TEL 0436-21-7041</td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr height="19" style='height:14.5pt'>
            <td class="xl70"></td>
            <td height="19" class="xl69" style='height:14.5pt;'></td>
            <td class="xl70"></td>
            <td class="xl70"></td>
            <td class="xl73" style="text-align: left" colspan=5>FAX 0436-24-7277</td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr height="19" style='height:14.5pt'>
            <td class="xl70"></td>
            <td height="19" class="" style='height:14.5pt;'></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr height="20" style='height:19.5pt'>
            <td class="xl70"></td>
            <td height="20" class="" style='height:19.5pt;'></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
            <td class="">発注者</td>
            <td class="" style='border-left:none;'>送信者</td>
            <td class="" style='border-left:none;'>検品</td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr height="32" style='height:30.0pt'>
            <td class="xl70"></td>
            <td height="32" class="" style='height:30.0pt;'></td>
            <td class=""></td>
            <td class=""></td>
            <td class="" style='border-top:none;'></td>
            <td class="" style='border-top:none;border-left:none;'></td>
            <td class="" style='border-top:none;border-left:none;'></td>
            <td class=""></td>
            <td class=""></td>
            <td class=""></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="9" style="text-align: left">下記の通り、商品を注文いたします。</td>
        </tr>
    </tbody>
</table>
<table cellpadding="1" cellspacing="1" style="border-collapse:collapse;overflow: visible !important; width: 100%;">
    <thead height="24" style="height:18.0pt; display: table-header-group;">
        <tr style="page-break-inside: avoid;">
            <th style="   text-align: center;">発注No</th>
            <th style="   text-align: center;">品名</th>
            <th style="   text-align: center;">規格品番</th>
            <th style="   text-align: center;">資材区分</th>
            <th style="   text-align: center;">発注個数</th>
            <th style="   text-align: center;" colspan="2">備考</th>
            <th style="   text-align: center;" colspan="2">センター使用欄</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $key = 0;
        if (isset($list) && $list) {
            foreach ($list as $key => $l) {
        ?>
                <tr style="page-break-inside: avoid;">
                    <td style="  width: 40px;   text-align: left; ">{{ $l->UseMaterialID}}</td>
                    <td style="  width: 200px;   text-align: left; ">{{ $l->MaterialNM}}</td>
                    <td style="    width: 100px;     text-align: left;  ">{{ $l->Type}}</td>
                    <td style="   width: 100px;   text-align: center; ">{{ $l->MaterialClsNM}}</td>
                    <td style="    width: 40px;   text-align: center;">{{ $l->OrderNum}}</td>
                    <td style="    text-align: center;" colspan="2"></td>
                    <td style="    text-align: center;" colspan="2">{{ $l->MaterialID}}</td>
                </tr>
            <?php
            }
        }
        if ($key < 14) {
            for ($i = $key; $i < 14; $i++) {

            ?>
                <tr style="page-break-inside: avoid;">
                    <td style="     text-align: center;"></td>
                    <td style="     text-align: center;"></td>
                    <td style="     text-align: center;"></td>
                    <td style="     text-align: center;"></td>
                    <td style="      text-align: center;"></td>
                    <td style="    text-align: center;" colspan="2"></td>
                    <td style=" text-align: center;" colspan="2"></td>
                </tr>
        <?php
            }
        }
        ?>

    </tbody>
</table>