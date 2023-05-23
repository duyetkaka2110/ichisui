<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>請求書</title>
    <style>
        @font-face {
            font-family: ipag;
            font-style: normal;
            font-weight: normal;
            src:url('{{ storage_path("fonts/ipag.ttf")}}');
        }

        @font-face {
            font-family: ipag;
            font-style: bold;
            font-weight: bold;
            src:url('{{ storage_path("fonts/ipag.ttf")}}');
        }

        body {
            font-family: ipag;
        }

        ruby {
            ruby-align: left;
        }

        .font0 {
            color: black;
            font-size: 11.0pt;
            font-weight: 400;
            font-style: normal;
            text-decoration: none;
        }

        .font7 {
            color: windowtext;
            font-size: 6.0pt;
            font-weight: 400;
            font-style: normal;
            text-decoration: none;

        }

        .font11 {
            color: black;
            font-size: 11.0pt;
            font-weight: 400;
            font-style: normal;
            text-decoration: underline;
            ;
        }

        td {
            padding-top: 1px;
            padding-right: 1px;
            padding-left: 1px;
            color: black;
            font-size: 11.0pt;
            font-weight: 400;
            font-style: normal;
            text-decoration: none;

            text-align: general;
            vertical-align: middle;
            border: none;
            white-space: nowrap;
        }

        .xl65 {
            vertical-align: bottom;
        }

        .xl66 {
            vertical-align: bottom;
            border-top: none;
            border-right: none;
            border-bottom: 1.5pt solid windowtext;
            border-left: none;
        }

        .xl67 {
            font-size: 14.0pt;
            vertical-align: bottom;
            border-top: none;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: none;
        }

        .xl68 {
            font-size: 14.0pt;
            vertical-align: bottom;
        }

        .xl69 {
            vertical-align: bottom;
            border-top: none;
            border-right: none;
            border-bottom: 2.0pt double windowtext;
            border-left: none;
        }

        .xl70 {
            font-weight: 700;
            vertical-align: bottom;
            border-top: none;
            border-right: none;
            border-bottom: 2.0pt double windowtext;
            border-left: none;
        }

        .xl71 {
            text-align: center;
            vertical-align: bottom;
            border: .5pt solid windowtext;
        }

        .xl72 {
            text-align: right;
            vertical-align: bottom;
            border: .5pt solid windowtext;
        }

        .xl73 {
            text-align: center;
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: .5pt solid windowtext;
        }

        .xl74 {
            text-align: center;
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: .5pt solid windowtext;
            border-bottom: .5pt solid windowtext;
            border-left: .5pt dotted windowtext;
        }

        .xl75 {
            text-align: left;
            vertical-align: bottom;
            border: .5pt solid windowtext;
        }

        .xl76 {
            text-align: left;
            vertical-align: bottom;
        }

        .xl77 {
            font-size: 16.0pt;
            font-weight: 700;
            text-align: center;
            vertical-align: bottom;
        }

        .xl78 {
            vertical-align: bottom;
            border: .5pt solid windowtext;
        }

        .xl79 {
            text-align: right;
            vertical-align: bottom;
        }

        .xl80 {
            text-align: center;
            vertical-align: bottom;
        }

        .xl81 {
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: .5pt solid windowtext;
        }

        .xl82 {
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: none;
        }

        .xl83 {
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: .5pt solid windowtext;
            border-bottom: .5pt solid windowtext;
            border-left: none;
        }

        .xl84 {
            text-align: center;
            vertical-align: bottom;
            border-top: none;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: none;
        }

        .xl85 {
            text-align: left;
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: .5pt solid windowtext;
        }

        .xl86 {
            text-align: left;
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: .5pt solid windowtext;
            border-bottom: .5pt solid windowtext;
            border-left: none;
        }

        .xl87 {
            text-align: right;
            vertical-align: middle;
            white-space: normal;
            padding-right: 5px;
        }

        .xl88 {
            text-align: left;
            vertical-align: bottom;
            border-top: .5pt solid windowtext;
            border-right: none;
            border-bottom: none;
            border-left: none;
        }

        .xl89 {
            text-align: left;
            vertical-align: bottom;
            border-top: none;
            border-right: none;
            border-bottom: .5pt solid windowtext;
            border-left: none;
        }

        .xl90 {
            font-size: 8.0pt;
            text-align: center;
            vertical-align: top;
        }

        .d171,
        .d-text-right {
            text-align: right;
        }

        .d-white {
            color: #fff;
        }

        table {
            overflow: visible !important;
            width: 100%;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .small-size {
            font-size: 70%;
            margin-bottom: 10px;
        }
        .tb-ginko{
            border-collapse: collapse;
            position: absolute;
            top: 0px;
            right: 30px;
        }
        .tb-ginko tr,
        .tb-ginko td{
            border: 1px solid #000;
        }
        .mar-ginko {
            position: relative;
        }
    </style>
</head>

<body>
    <table border="0" cellpadding="0" cellspacing="0" width="713" style="border-collapse:
 collapse;table-layout:fixed;width:537pt">
        <colgroup>
            <col class="xl65" width="69" span="7" style="width:52pt">
            <col class="xl65" width="24" style="width:18pt">
            <col class="xl65" width="81" style="width:61pt">
            <col class="xl65" width="102" style="width:77pt">
        </colgroup>
        <tbody>
            <tr height="24" style="height:18.0pt">
                <td class="xl65" width="69" style="width:52pt"></td>
                <td class="xl65" width="69" style="width:52pt"></td>
                <td class="xl65" width="69" style="width:52pt"></td>
                <td class="xl65" width="69" style="width:52pt"></td>
                <td class="xl65" width="69" style="width:52pt"></td>
                <td class="xl65" width="69" style="width:15pt"></td>
                <td class="xl65" width="15" style="width:15pt"></td>
                <td class="xl65" width="24" style="width:18pt"></td>
                <td class="xl65" width="81" style="width:61pt"></td>
                <td class="xl65" width="102" style="width:77pt"></td>
            </tr>
            <tr height="35" style="height:26.5pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td colspan="4" class="xl77">請　　　求　　　書</td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
            <tr height="25" style="height:18.5pt">
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66">　</td>
                <td class="xl66"></td>
            </tr>
            <tr height="5" style="height:3.75pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65 d-text-right" colspan="2">{{ @$list->PaymentIssueDate}}</td>
            </tr>
            <tr height="24">
                <td class="xl65 small-size">{{ @$list->ClaimAdress}}</td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td colspan="2" class="xl79"></td>
            </tr>
            <tr class="padding-bottom: 10px;">
                <td class="xl65  small-size">{{ @$list->ClaimBuilding}}</td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td colspan="2" class="xl79"></td>
            </tr>
            <tr height="30" style="height:18.0pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td colspan="2" class="xl79"></td>
            </tr>
            <tr height="30" style="height:22.5pt; ">
                <td class="xl67" style="padding-top: 10px;" colspan="5">{{ @$list->ClaimName}}　　様</td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
            <tr height="8" style="height:6.0pt">
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
            <tr height="8" style="height:6.0pt">
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl68"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
            <tr height="24" style="height:18.0pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
            <tr class="xl65" height="25" style="height:18.5pt">
                <td class="xl69" style="font-size: 14.0pt;">金額</td>
                <td class="xl70" style="font-size: 14.0pt;">　　￥{{ @number_format($list->totalAll)}}.-</td>
                <td class="xl69">　</td>
                <td class="xl69">　</td>
                <td class="xl69">　</td>
                <td class="xl65"></td>
                <td colspan="4" class="xl76"><ruby>千葉県<span style="display:none">
                            <rt class="font7">チバケン</rt>
                        </span></ruby><ruby>市原市<span style="display:none">
                            <rt class="font7">イチハラシ</rt>
                        </span></ruby><ruby>平田<span style="display:none">
                            <rt class="font7">ヒラタ</rt>
                        </span></ruby>１０４６－５</td>
            </tr>
            <tr class="xl65" height="25" style="height:18.5pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td colspan="4" class="xl76"><ruby>株式会社<span style="display:none">
                            <rt class="font7">カブシキカイシャ</rt>
                        </span></ruby>　<ruby>市原<span style="display:none">
                            <rt class="font7">イチハラ</rt>
                        </span></ruby><ruby>水道<span style="display:none">
                            <rt class="font7">スイドウ</rt>
                        </span></ruby>センター</td>
            </tr>
            <tr class="xl65" height="25" style="height:18.5pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td colspan="4" class="xl76">登録番号　T3040001053585</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl65"></td>
                <td colspan="5" class="">上記の通りご請求いたします。</td>
                <td class="xl76"></td>
                <td class="xl76"></td>
                <td colspan="2" class="xl76">Tel　0436-21-7041<ruby>
                        <font class="font7">
                            <rt class="font7"></rt>
                        </font>
                    </ruby></td>
            </tr>
            <tr class="" height="24" style="height:18.0pt">
                <td class=""></td>
                <td colspan="5" class="" style="border-left:none"></td>
                <td class=""></td>
                <td class=""></td>
                <td colspan="2" class="xl89">Fax 0436-24-7277</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl75">受付№</td>
                <td colspan="5" class="xl81" style="border-right:.5pt solid black;border-left:none">{{ @$list->WWID }}　</td>
                <td class="xl65"></td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl75" style="border-top:none">受付日</td>
                <td colspan="5" class="xl81" style="border-right:.5pt solid black;border-left: none">{{ @$list->WWDateTime}}</td>
                <td></td>
                <td class="mar-ginko ">
                    <table class="tb-ginko">
                        <tr>
                            <td rowspan="4" style="border:none; text-align:center">
                                振<br>
                                込<br>
                                先
                            </td>
                            <td style="width: 200px">千葉銀行　五井支店</td>
                        </tr>
                        <tr>
                            <td style="width: 200px">普通口座　3079149</td>
                        </tr>
                        <tr>
                            <td style="width: 200px">京葉銀行　五井支店</td>
                        </tr>
                        <tr>
                            <td style="width: 200px">普通口座　7035671</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl75" style="border-top:none; vertical-align: middle"><ruby>施工<span style="display:none">
                            <rt class="font7">セコウ</rt>
                        </span></ruby><ruby>日<span style="display:none">
                            <rt class="font7">ヒ</rt>
                        </span></ruby></td>
                <?php
                // 施工日は、9日分（3行）まで表示
                $wftxt = "";
                foreach ($list->WorkFrom as $k => $v) {
                    if ($wftxt) $wftxt .= "、";
                    if ($k == 3 || $k == 6) {
                        $wftxt .= "\n";
                    }
                    $wftxt .= $v->WorkFrom;
                }
                ?>
                <td colspan="5" class="xl81" style="border-right:.5pt solid black;border-left:none;white-space: pre-wrap;overflow: hidden; {{ @$k>3 ? 'font-size: 13px;' : '' }} vertical-align: middle">{{ @$wftxt}}</td>
                <td></td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl75"  rowspan="2" style="border-top:none;vertical-align: top;">施工場所</td>
                <td colspan="5" class="xl81" style="border-right:.5pt solid black;border-left:none;white-space: pre-wrap;overflow: hidden;">{{ @$list->ReqAdress }}</td>
                <td></td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td colspan="5" class="xl81" style="border-right:.5pt solid black;border-left:none;white-space: pre-wrap;overflow: hidden;">{{ @$list->ReqBuilding }}</td>
                <td></td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl75" style="border-top:none">施工内容</td>
                <td colspan="5" class="xl78" style="border-left:none;white-space: pre-wrap;overflow: hidden;">{{ @$list->LeakagePoint}} </td>
                <td></td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65 d-white">s</td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
            <tr height="24" style="height:18.0pt">
                <td class="xl65 d-white">s</td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
                <td class="xl65"></td>
            </tr>
        </tbody>
    </table>

    <table cellpadding="1" cellspacing="1" width="713" style="border-collapse:
 collapse;table-layout:fixed;width:537pt">
        <thead height="24" style="height:18.0pt;">
            <tr>
                <th class="xl71" colspan="10">修繕費内訳</th>
            </tr>
            <tr height="24" style="height:18.0pt">
                <th colspan="5" class="xl71">品 名 ・ 仕 様</th>
                <th class="xl71" style="border-left:none">単価</th>
                <th colspan="2" class="xl71" style="border-left:none">数量</th>
                <th class="xl71" style="border-left:none">金額</th>
                <th class="xl71" style="border-left:none">摘要</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listuse as $v)
            <tr height="24" style="height:18.0pt">
                <td colspan="5" class="xl75">{{ $v["MaterialNM"] ." ". $v["Type"] }}</td>
                <td class="xl72" style="border-top:none;border-left:none">{{ number_format($v["SellPrice"] )}}</td>
                <td class="xl73" style="border-top:none;border-left:none">{{ str_replace(".0","",number_format($v["UseNum"], 1)) }}</td>
                <td class="xl74" width="10" style="border-top:none; width: 10px;">{{ $v["UseUnitNM"] ?  $v["UseUnitNM"]: $v["UseUnitNM999"]}}</td>
                <td class="xl71 d171" style="border-top:none;border-left:none">{{ number_format($v["total"]) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            @endforeach
            <tr class="xl65" height="24" style="height:18.0pt">
                <td colspan="8" class="xl71">材　　料　　費</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->total->totalSub) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">調　　査　　費</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->SurveyFee) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">
                <td colspan="8" class="xl71">技　　術　　料</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->TechFee) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">産　　廃　　処　　分　　費</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->DisposalFee) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">出　　張　　費</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->TravelFee )}}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <!-- <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">そ　　の　　他</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->Others) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr> -->
            @if($list->Discount)
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">出　精　値　引　き</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">-{{@number_format($list->Discount) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            @endif
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">{{@$list->tax*100 }}%対象金額計</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->totalSub ) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">消　費　税</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->totalSubTax) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">

                <td colspan="8" class="xl71">合　　　　　　計</td>
                <td class="xl71 d-text-right" style="border-top:none;border-left:none">{{@number_format($list->totalAll ) }}</td>
                <td class="xl71" style="border-top:none;border-left:none">　</td>
            </tr>
            <tr class="xl65" height="24" style="height:18.0pt">

                <td class="xl65"></td>
                <td colspan="9" class="xl88">恐れ入りますが、お支払いは{{@$list->todayadd14 }}までにお願い致します。 </td>
            </tr>
            <!--[if supportMisalignedColumns]-->
            <tr height="0" style="display:none">
                <td width="23" style="width:0"></td>
                <td width="69" style="width:52pt"></td>
                <td width="69" style="width:52pt"></td>
                <td width="69" style="width:52pt"></td>
                <td width="69" style="width:52pt"></td>
                <td width="69" style="width:52pt"></td>
                <td width="69" style="width:52pt"></td>
                <td width="69" style="width:52pt"></td>
                <td width="24" style="width:18pt"></td>
                <td width="81" style="width:61pt"></td>
                <td width="102" style="width:77pt"></td>
            </tr>
            <!--[endif]-->
        </tbody>
    </table>
</body>

</html>