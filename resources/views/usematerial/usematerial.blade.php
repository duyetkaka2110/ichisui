<!DOCTYPE html>
<html>

<head>
    <title>資材選択 | 工事管理/資材管理システム</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js" type="text/javascript"></script>

    <link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/js/main.js"></script>
    <link type="text/css" href="/keypad/css/jquery.keypad.css" rel="stylesheet">
    <script type="text/javascript" src="/keypad/js/jquery.plugin.js"></script>
    <script type="text/javascript" src="/keypad/js/jquery.keypad.js"></script>
    <script src="/js/usematerial.js"></script>
    <link href="/css/usematerial.css" rel="stylesheet">
</head>

<body>
    <form action="/usematerialinsert" id="usematerialinsert" method="post">
        @csrf

        <header>
            <h4>資材検索、入力</h4>
            <span class="form-group">
                <input type="hidden" class="form-control text WWID" form="usematerialinsert" name="WWID" value="{{@$WWID}}">
                <input type="hidden" class="form-control text WWID" name="WWID" value="{{old('WWID' ,@$WWID)}}">
            </span>
        </header>

        <main>
            <!-- 特注品追加 -->
            <div class="listmaterial">
                <span class="tabletext">特注品登録</span>
                <table class="CustomOrderMaterial " id="CustomOrderMaterial">
                    <thead>
                        <!-- <th> -->
                        <tr class="tabletitle">
                            <th class="MaterialNM">品名</th>
                            <th class="Type">規格</th>
                            <th class="" style="width: 76px;">使用/ロス単位</th>
                            <th>使用数</th>
                            <th>ロス数</th>
                            <th>仕入単価</th>
                            <th>仕入れ数</th>
                            <th>出値単価</th>
                            <th>追加</th>
                        </tr>
                        <!-- </th> -->
                    </thead>
                    <tbody>
                        <tr class="d-none btn-add-CusTomContent tablebody">
                            <td>
                                <span class="input-group input-group-sm longtext CustomOrder">
                                    <input type="text" class="form-control text" name="CustomMaterialNM" maxlength="100" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm longtext2 CustomOrder">
                                    <input type="text" class="form-control text" name="CustomType" maxlength="100" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm number CustomOrder">
                                    <input type="text" class="form-control text" name="CustomMaterialUnit" maxlength="2" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" class="form-control text input number" value="0" min="0" step="0.1" name="CustomUse" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" class="form-control text input number" value="0" min="0" step="0.1" name="CustomLoss" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" class="form-control text input number" value="0" min="0" step="0.1" name="BuyPrice" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" class="form-control text input number" value="0" min="0" step="0.1" name="BuyNumber" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" class="form-control text input number" value="0" min="0" step="0.1" name="SellPrice" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>

                            <td><i class="fa fa-cart-plus CustomMaterialAdd" aria-hidden="true"></i></td>
                        </tr>

                        <!-- デフォルト -->
                        <tr class="tr-0 UseMaterial tablebody">
                            <td>
                                <span class="input-group input-group-sm longtext CustomOrder">
                                    <input type="text" class="form-control text " name="CustomMaterialNM" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm longtext2 CustomOrder">
                                    <input type="text" class="form-control text " name="CustomType" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm number CustomOrder">
                                    <input type="text" class="form-control text" name="CustomMaterialUnit" maxlength="2" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="text" inputmode="none" class="inputKeypadWithDot form-control text input number e" value="0" min="0" step="0.1" name="CustomUse" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="text" inputmode="none" class="inputKeypadWithDot form-control text input number " value="0" min="0" step="0.1" name="CustomLoss" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" inputmode="none" class="inputKeypadWithoutMinus form-control text input number" value="0" min="0" step="1" name="BuyPrice" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" inputmode="none" class="inputKeypad form-control text input number" value="0" step="1" name="BuyNumber" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>
                            <td>
                                <span class="input-group input-group-sm CustomOrder number">
                                    <input type="number" inputmode="none" class="inputKeypadWithoutMinus form-control text input number" value="0" min="0" step="1" name="SellPrice" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </span>
                            </td>

                            <td><i class="fa fa-cart-plus CustomMaterialAdd" aria-hidden="true"></i></td>
                        </tr>
                    </tbody>
                </table>
                <!-- <div><button type="button" name="btn" value="save" class="CustomOrderAdd btn btn-primary" id="CustomOrderAdd">行追加</button></div> -->
                <span class="tabletext">資材マスタから登録</span>
                <table class="materialtable">
                    <thead>
                        <form action="/usematerialsearch" method="get">
                            <tr>
                                <td>
                                    <input type="hidden" class="form-control text selectMaterialID" name="WWID" value="{{old('WWID' ,@$WWID)}}">

                                    <span class="input-group input-group-sm">
                                        <input type="text" class="form-control text" maxlength="6" name="searchID" value="{{@$searchID}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    </span>
                                </td>
                                <td>
                                    <span class="input-group input-group-sm">
                                        <input type="text" class="form-control text" maxlength="10" name="searchUnitListCD" value="{{@$searchUnitListCD}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    </span>
                                </td>
                                <td>
                                    <span class="input-group select searchtCls">
                                        <select class="form-control form-control-sm searchtCls" name="searchMaterialCls">
                                            <option value=" " selected></option>
                                            @foreach($ListMaterialCls as $k=>$v)
                                            <option {{ old("WWType") == $v->InternalValue ? 'selected' : '' }} {{ @$searchMaterialCls == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </span>
                                </td>
                                <td>
                                    <span class="input-group input-group-sm longtext">
                                        <input type="text" class="form-control text" maxlength="100" name="searchMaterialNM" value="{{@$searchMaterialNM}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    </span>
                                </td>
                                <td>
                                    <span class="input-group input-group-sm Type">
                                        <input type="text" class="form-control text" maxlength="100" name="searchType" value="{{@$searchType}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    </span>
                                </td>
                                <td colspan="4" class="searchtd">

                                    <button type="button" name="btn" value="search" class="button btn btn-primary materialsearch">検索</button>

                                </td>
                            </tr>
                        </form>
                        <tr class="tabletitle">
                            <th class="MaterialID">ID</th>
                            <th>単価表</th>
                            <th class="MaterialCls">資材区分</th>
                            <th class="MaterialNM">品名</th>
                            <th class="Type">規格</th>
                            <th>在庫</th>
                            <th class="unitTh">使用数</th>
                            <th class="unitTh">ロス数</th>
                            <th>追加</th>
                        </tr>
                    </thead>
                    <!-- 資材一覧 -->
                    <tbody class="table">

                    </tbody>
                </table>
                <nav class="pagingnav">
                    <ul class="pagination">
                    </ul>
                </nav>
            </div>
            <!-- 選択資材 -->
            <div class="selectmaterials">
                <div class="ButtonInput">

                    <button type="button" value="save" form="usematerialinsert" class="save button btn btn-primary input">登録</button>
                    <button type="submit" name="btn" value="back" form="usematerialinsert" class="button btn btn-primary back">キャンセル</button>
                </div>
                <table id="SelectMaterial">
                    <tbody>
                        @if($ListUseMaterial)
                        <?php
                        $count = 0;
                        ?>
                        @foreach($ListUseMaterial as $kListUseMaterial=>$ListUseMaterial)
                        <tr class="oldtr-{{ str_replace(' ','-',$ListUseMaterial->MaterialID) }} oldtr-{{$kListUseMaterial}} oldDate">
                            <td>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectMaterialID" disabled name="selectMaterialID" value="{{old('selectMaterialID',@$ListUseMaterial->MaterialID) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control text selectMaterialID" form="usematerialinsert" value="{{old('selectMaterialID',@$ListUseMaterial->MaterialID) }}" name="selectMaterialID[old{{$kListUseMaterial}}]">
                                </div>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectMaterialNM" disabled name="selectMaterialNM" value="{{old('selectMaterialNM',@$ListUseMaterial->MaterialNM) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control text selectMaterialNM" form="usematerialinsert" value="{{old('selectMaterialNM',@$ListUseMaterial->MaterialNM) }}" name="selectMaterialNM[old{{$kListUseMaterial}}]">
                                </div>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectType" disabled name="selectType" value="{{old('selectType',@$ListUseMaterial->Type) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control text selectType" form="usematerialinsert" value="{{old('selectType',@$ListUseMaterial->Type) }}" name="selectType[old{{$kListUseMaterial}}]">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm unit">
                                    <span class="selectLbl">使用数</span>
                                    <input type="text" class="form-control shorttext selectUse" style="text-align: center;" name="selectUse" value="{{old('selectUse',@$ListUseMaterial->UseNum) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectUse hidden" form="usematerialinsert" value="{{old('selectUse',@$ListUseMaterial->UseNum) }}" name="selectUse[old{{$kListUseMaterial}}]">
                                </div>
                                <div class="input-group input-group-sm unit selectBuyPrice">
                                    <span class="selectLbl">仕入単価</span>
                                    <input type="text" class="form-control shorttext selectBuyPrice" style="text-align: center;" name="selectBuyPrice" value="{{old('selectBuyPrice',@$ListUseMaterial->BuyPrice) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectBuyPrice hidden" form="usematerialinsert" value="{{old('selectBuyPrice',@$ListUseMaterial->BuyPrice) }}" name="selectBuyPrice[old{{$kListUseMaterial}}]">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm unit">
                                    <span class="selectLbl">ロス数</span>
                                    <input type="text" class="form-control shorttext selectLoss" style="text-align: center;" name="selectLoss" value="{{old('selectLoss',@$ListUseMaterial->LossNum) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectLoss hidden" form="usematerialinsert" value="{{old('selectLoss',@$ListUseMaterial->LossNum) }}" name="selectLoss[old{{$kListUseMaterial}}]">
                                </div>

                                <div class="input-group input-group-sm unit selectSellPrice">
                                    <span class="selectLbl"> 出値単価</span>
                                    <input type="text" class="form-control shorttext selectSellPrice" style="text-align: center;" name="selectSellPrice" value="{{old('selectLoss',@$ListUseMaterial->SellPrice) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectSellPrice" form="usematerialinsert" value="{{old('selectLoss',@$ListUseMaterial->SellPrice) }}" name="selectSellPrice[old{{$kListUseMaterial}}]">
                                    <input type="hidden" class="form-control shorttext StockNum" form="usematerialinsert" value="{{old('StockNum',@$ListUseMaterial->StockNum) }}" name="StockNum[old{{$kListUseMaterial}}]">
                                    <input type="hidden" class="form-control shorttext hidden selectBuyNumber"  form="usematerialinsert" name="selectBuyNumber[]"value="{{old('selectBuyNumber',@$ListUseMaterial->BuyNumber) }}">
                                </div>
                            </td>
                            <td>
                                <i class="fa fa-trash MaterialDelete fa-2x" aria-hidden="true"></i>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        @foreach($ListOrderMaterial as $kListOrderMaterial=>$ListOrderMaterial)
                        <tr class="oldtr-{{$ListOrderMaterial->MaterialID}}-{{str_replace(' ','-',$ListOrderMaterial->MaterialNM)}}-{{str_replace(' ','-',$ListOrderMaterial->Type)}} oldtr-{{$kListOrderMaterial}} oldDate OrderDate">
                            <td>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectMaterialID" disabled name="selectMaterialID" value="{{old('selectMaterialID',@$ListOrderMaterial->MaterialID) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control text selectMaterialID" form="usematerialinsert" value="{{old('selectMaterialID',@$ListOrderMaterial->MaterialID) }}" name="selectMaterialID[Orderold{{$kListOrderMaterial}}]">
                                </div>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectMaterialNM" disabled name="selectMaterialNM" value="{{old('selectMaterialNM',@$ListOrderMaterial->MaterialNM) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control text selectMaterialNM" form="usematerialinsert" value="{{old('selectMaterialNM',@$ListOrderMaterial->MaterialNM) }}" name="selectMaterialNM[Orderold{{$kListOrderMaterial}}]">
                                </div>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectType" disabled name="selectType" value="{{old('selectType',@$ListOrderMaterial->Type) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control text selectType" form="usematerialinsert" value="{{old('selectType',@$ListOrderMaterial->Type) }}" name="selectType[Orderold{{$kListOrderMaterial}}]">
                                    <input type="hidden" class="form-control text selectType" form="usematerialinsert" value="{{old('selectCustomMaterialUnit',@$ListOrderMaterial->CustomMaterialUnit) }}" name="selectCustomMaterialUnit[Orderold{{$kListOrderMaterial}}]">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm unit">
                                    <span class="selectLbl">使用数</span>
                                    <input type="text" class="form-control shorttext selectUse" style="text-align: center;" name="selectUse" value="{{old('selectUse',@$ListOrderMaterial->UseNum) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectUse hidden" form="usematerialinsert" value="{{old('selectUse',@$ListOrderMaterial->UseNum) }}" name="selectUse[Orderold{{$kListOrderMaterial}}]">
                                    <!-- <input type="hidden" class="form-control shorttext newselectUse hidden" form="usematerialinsert"  value="0" name="selectUse[old{{$kListOrderMaterial}}]"> -->
                                </div>
                                <div class="input-group input-group-sm unit selectBuyPrice">
                                    <span class="selectLbl">仕入単価</span>
                                    <input type="text" class="form-control shorttext selectBuyPrice" style="text-align: center;" name="selectBuyPrice" value="{{old('selectBuyPrice',@$ListOrderMaterial->BuyPrice) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectBuyPrice hidden" form="usematerialinsert" value="{{old('selectBuyPrice',@$ListOrderMaterial->BuyPrice) }}" name="selectBuyPrice[Orderold{{$kListOrderMaterial}}]">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm unit">
                                    <span class="selectLbl">ロス数</span>
                                    <input type="text" class="form-control shorttext selectLoss" style="text-align: center;" name="selectLoss" value="{{old('selectLoss',@$ListOrderMaterial->LossNum) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectLoss hidden" form="usematerialinsert" value="{{old('selectLoss',@$ListOrderMaterial->LossNum) }}" name="selectLoss[Orderold{{$kListOrderMaterial}}]">
                                    <!-- <input type="hidden" class="form-control shorttext newselectLoss hidden" form="usematerialinsert" value="{{old('NewSelectLoss') }}" name="NewSelectLoss[old{{$kListOrderMaterial}}]"> -->
                                </div>

                                <div class="input-group input-group-sm unit selectSellPrice">
                                    <span class="selectLbl"> 出値単価</span>
                                    <input type="text" class="form-control shorttext selectSellPrice" style="text-align: center;" name="selectSellPrice" value="{{old('selectLoss',@$ListOrderMaterial->SellPrice) }}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext selectSellPrice hidden" form="usematerialinsert" value="{{old('selectLoss',@$ListOrderMaterial->SellPrice) }}" name="selectSellPrice[Orderold{{$kListOrderMaterial}}]">
                                    <input type="hidden" class="form-control shorttext StockNum" form="usematerialinsert" value="0" name="StockNum[Orderold{{$kListOrderMaterial}}]">
                                    <input type="hidden" class="form-control shorttext hidden selectBuyNumber"  form="usematerialinsert" value="{{old('selectBuyNumber',@$ListOrderMaterial->BuyNumber)}}" name="selectBuyNumber[Orderold{{$kListOrderMaterial}}]">
                                </div>
                            </td>
                            <td>
                                <i class="fa fa-trash MaterialDelete fa-2x" aria-hidden="true"></i>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="d-none btn-add-content">
                            <td>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectMaterialID" disabled name="selectMaterialID" value="selectMaterialID" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control hidden selectMaterialID" disabled form="usematerialinsert" name="selectMaterialID[]">
                                </div>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectMaterialNM" disabled name="selectMaterialNM" value="selectMaterialNM" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control hidden selectMaterialNM" disabled form="usematerialinsert" name="selectMaterialNM[]">
                                </div>
                                <div class="input-group input-group-sm selectmaterial">
                                    <input type="text" class="form-control text selectType" disabled name="selectType" value="selectType" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control hidden selectType" disabled form="usematerialinsert" name="selectType[]">
                                    <input type="hidden" class="form-control hidden selectCustomMaterialUnit" form="usematerialinsert" name="selectCustomMaterialUnit[]">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm unit">
                                    <span class="selectLbl">使用数</span>
                                    <input type="text" class="form-control shorttext selectUse" style="text-align: center;" name="selectUse" value="selectUse" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext hidden selectUse" disabled form="usematerialinsert" name="selectUse[]">
                                </div>
                                <div class="input-group input-group-sm unit selectBuyPrice">
                                    <span class="selectLbl">仕入単価</span>
                                    <input type="text" class="form-control shorttext selectBuyPrice" style="text-align: center;" name="selectBuyPrice" value="selectBuyPrice" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext hidden selectBuyPrice" disabled form="usematerialinsert" name="selectBuyPrice[]">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm unit">
                                    <span class="selectLbl">ロス数</span>
                                    <input type="text" class="form-control shorttext selectLoss" style="text-align: center;" name="selectLoss" value="selectLoss" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext hidden selectLoss" disabled form="usematerialinsert" name="selectLoss[]">
                                </div>

                                <div class="input-group input-group-sm unit selectSellPrice">
                                    <span class="selectLbl"> 出値単価</span>
                                    <input type="text" class="form-control shorttext selectSellPrice" style="text-align: center;" name="selectSellPrice" value="selectSellPrice" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control shorttext hidden selectSellPrice" disabled form="usematerialinsert" name="selectSellPrice[]">
                                    <input type="hidden" class="form-control shorttext hidden StockNum"  form="usematerialinsert" name="StockNum[]">
                                    <input type="hidden" class="form-control shorttext hidden selectBuyNumber"  form="usematerialinsert" name="selectBuyNumber[]">
                                </div>
                            </td>
                            <td>
                                <i class="fa fa-trash MaterialDelete fa-2x" aria-hidden="true"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
            <!-- <button type="submit" name="btn" value="save" class="button btn btn-primary input">追加</button> -->
            <!-- </form> -->
            <!-- Modal Error Message -->
            <div class="modal fade" id="MessageModal" role="dialog" data-backdrop="static">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header modal-header-error bg-danger p-2">
                            <h5 class="modal-title text-white">エラーメッセージ</h5>
                        </div>
                        <div class="modal-body">
                            @foreach ($errors->all() as $error)
                            <p>{!! $error !!}</p>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger close-modal" data-dismiss="modal">閉じる</button>
                        </div>
                    </div>
                </div>
            </div>
            @if(($errors->any()))
            <script>
                $(document).ready(function() {
                    $("#MessageModal").modal();
                });
            </script>
            @endif
        </main>
    </form>

</body>

</html>