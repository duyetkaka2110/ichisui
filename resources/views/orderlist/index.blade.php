@extends("layouts.layout")
@section('title', '発注一覧')
@section("css")
<link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link href="/css/work.css" rel="stylesheet">
<link href="/css/orderlist.css" rel="stylesheet">
@endsection

@section("js")
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js" type="text/javascript"></script>
<script src="/js/bootstrap-datetimepicker.min.js"></script>
<script src="/js/orderlist.js"></script>
@endsection

@section("content")
<main class=" p-3">
    <form action="" method="POST" class="formsubmit">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <div class="main-header pt-3">
            <div class="m-title">
                <h4 class="pb-2">発注一覧</h4>
            </div>
            <div class="m-header w-100 p-2 mb-3 mt-3 pt-3 form-search position-relative">
                <div class="collapsetitle position-absolute m-title" data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch">
                    <i class="fa fa-plus-square-o plus" aria-hidden="true"></i><i class="fa fa-minus-square-o minus" aria-hidden="true"></i> 検索条件
                </div>
                <div class="w-100 collapse show " id="collapseSearch">
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td>
                                    <label class="m-0">資材ID</label>
                                    <input name="MaterialID" type="number" class="w-6em form-control d-inline txtsearch p-0 MaterialID" value="{{ @$datasearch['MaterialID'] }}" />
                                </td>
                                <td>
                                    <label class="m-0">資材区分</label>
                                    <select name="MaterialCls" class="form-control p-0  w-7em d-inline txtsearch p-0 MaterialCls">
                                        <option value=""></option>
                                        @foreach($listMaterialCls as $k=>$v)
                                        <option <?php if (isset($datasearch['MaterialCls']) && $datasearch['MaterialCls'] == $v->InternalValue) echo 'selected'; ?> value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <label class="m-0">品名</label>
                                    <input name="MaterialNM" type="text" class="w-6em form-control d-inline txtsearch p-0 MaterialNM" value="{{ @$datasearch['MaterialNM'] }}" />
                                </td>
                                <td>
                                    <label class="m-0">規格</label>
                                    <input name="Type" type="text" class="w-6em form-control d-inline txtsearch p-0 Type" value="{{ @$datasearch['Type'] }}" />
                                </td>
                                <td>
                                    <label class="m-0">発注先</label>
                                    <select name="SupplierID" class="form-control p-0  w-7em d-inline txtsearch p-0 SupplierID">
                                        <option value=""></option>
                                        @foreach($listSupplier as $k=>$v)
                                        <option <?php if (isset($datasearch['SupplierID']) && $datasearch['SupplierID'] == $v->InternalValue) echo 'selected'; ?> value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="w-8em">
                                    <button type="submit" value="btnSearch" name="btnSearch" class="btn btn-primary w-100 p-0 btn-search">検索</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="mg-2date">
                                    <label class="m-0">発注日</label>
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker OrderDateFrom txtsearch" name="OrderDateFrom" value="{{@$datasearch['OrderDateFrom'] }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    ～
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker OrderDateTo txtsearch" name="OrderDateTo" value="{{@$datasearch['OrderDateTo'] }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2" class="mg-2date">
                                    <label class="m-0">検品日</label>
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker CheckDateFrom txtsearch" name="CheckDateFrom" value="{{@$datasearch['CheckDateFrom'] }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    ～
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker CheckDateTo txtsearch" name="CheckDateTo" value="{{@$datasearch['CheckDateTo'] }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="mg-2radio">
                                    <label class="m-0">状況</label>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        @foreach($listOrderStatus as $k=>$v)
                                        <?php
                                        $selected = null;
                                        if (isset($datasearch['OrderStatus']) && $datasearch['OrderStatus'] == $v->InternalValue)  $selected = 'checked';
                                        if (!isset($datasearch['OrderStatus']) && $k == 0)  $selected = 'checked';
                                        ?>
                                        <label class="btn btn-info p-0  OrderStatus OrderStatus{{ $k }} {{  $selected ? 'active' : '' }}">
                                            <input type="radio" {{ $selected }} name="OrderStatus" value="{{ @$v->InternalValue }}" autocomplete="off"> {{ $v->DispText }}
                                        </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="w-8em">
                                    <button type="button" class="btn btn-primary w-100 p-0 mt-1 btn-clear">クリア</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div>
                <label id="ResultSearchCount">検索結果：<span>{{@$list->total()}}</span>件</label>
            </div>
            <table class="table table-order table-hover table-work">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="w-3em text-center">No.</th>
                        <th scope="col" class="w-5em text-center">資材ID</th>
                        <th scope="col" class="w-6em text-center">資材区分</th>
                        <th scope="col" class="w-10em text-center">品名</th>
                        <th scope="col" class="w-12em text-center">規格</th>
                        <th scope="col" class="w-10em text-center">発注先</th>
                        <th scope="col" class="w-6em text-center">発注日</th>
                        <th scope="col" class="w-4em text-center">状況</th>
                        <th scope="col" class="w-5em text-center">仕入単価</th>
                        <th scope="col" class="w-4em text-center">発注数</th>
                        <th scope="col" class="w-4em text-center">検品数</th>
                        <th scope="col" class="w-6em text-center">検品日</th>
                        <th scope="col" class="w-3em text-center">検品入力</th>
                    </tr>
                </thead>
                <tbody>
                    @if($list->total())
                    <?php $no = ($list->currentPage() - 1) * $list->perPage() ?>
                    @foreach($list as $k=>$l)
                    <tr class="">
                        <td class="text-center">{{ $no+$k+1 }}</td>
                        <td>{{ $l->MaterialID }}</td>
                        <td>{{ $l->MaterialClsNM }}</td>
                        <td>{{ $l->MaterialNM }}</td>
                        <td>{{ $l->Type }}</td>
                        <td>{{ $l->SupplierNM }}</td>
                        <td class="text-center">{{ $l->OrderDate }}</td>
                        <td class="text-center">{{ $l->OrdSts }}</td>
                        <td class="text-right">{{ number_format($l->Price) }}{{ $l->Price ? '円' : '' }}</td>
                        <td class="text-right">{{ $l->OrderNum ?  $l->OrderNum.$l->UnitNM : '' }}</td>
                        <td class="text-right"><a href="#" class="btnchecknum" data="MaterialID={{$l->MaterialID}}&UseMaterialID={{$l->UseMaterialID}}">{{ $l->CheckNum ?  $l->CheckNum : '0'  }}{{ $l->UnitNM }}</a></td>
                        <td class="text-center">{{ $l->CheckNum  ? $l->CheckDate : '' }}</td>
                        <td class="text-center"><button class="btn btn-secondary w-3em p-0 btnkenpin" stocknum="{{ $l->OrderNum }}" data-update="MaterialID={{$l->MaterialID}}&UseMaterialID={{$l->UseMaterialID}}&CheckNum={{$l->CheckNum}}" type="button">検品</button></td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="14" class="text-center">対象データがありません。</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="mg-pagination">
                {{ $list->links("vendor.pagination.bootstrap-4-submit") }}
            </div>
        </div>
    </form>
</main>
<!-- Image Popup -->
<div class="modal fade" id="ImageModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"></h5>
            </div>
            <div class="modal-body text-center">
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-danger close-modal w-6em" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
<!-- History Popup -->
<div class="modal fade" id="HistoryModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"></h5>
            </div>
            <div class="modal-body text-center">
                <table class="tablelist table table-hover table-striped tablesorter tablekenpinhistory">
                    <thead>
                        <tr>
                            <th>発注先</th>
                            <th>仕入単価</th>
                            <th>発注日</th>
                            <th>発注数</th>
                            <th>検品日</th>
                            <th>検品数</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-danger close-modal w-6em" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Kenpin Modal  -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static" aria-hidden="true" id="ConfirmKenpinModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header  p-1 pl-3  bg-primary">
                <h5 class="modal-title text-white" id=""></h5>
            </div>
            <div class="modal-body">
                <ul class="p-0 m-0">
                    <li class="likenpinbi">
                        <label class="t">検品日</label>
                        <div class="input-group input-group-sm mg-date">
                            <input type="text" autocomplete="off" class="form-control datetimepicker txtCheckDate " name="txtCheckDate" value="">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <label class="t">発注日</label> <input type="text" name="txtOrderDate" disabled class="form-control text" value="" />
                    </li>
                    <li>
                        <label class="t">発注資材名</label> <input type="text" name="txtUserNM" disabled class="form-control text" value="" />
                    </li>
                    <li>
                        <label class="t">発注先</label> <input type="text" name="txtSupplierNM" disabled class="form-control text" value="" />
                    </li>
                    <li>
                        <label class="t">発注数</label> <input type="text" name="txtOrderNum" disabled class="form-control text" value="" />
                    </li>
                    <li>
                        <label class="t">検品済数</label> <input type="text" name="txtTotalCheck" disabled class="form-control text" value="" />
                    </li>
                    <li>
                        <label class="t">検品オプション</label>
                        <div class="text">
                            <span class="mar-kenpinoption">
                                <input style="margin: 10px 0 15px;" type="radio" value="allkenpin" name="kenpinoption" id="allkenpin" /> <label for="allkenpin">全部検品する</label>
                                <input type="radio" value="1kenpin" name="kenpinoption" id="bun1kenpin" /> <label for="bun1kenpin">一部検品する　</label>
                            </span><span class="mar-kenpinoptionnumber"><input type="number" value="" name="kenpinoptionnumber" /><label class="txtOrderUnitNM"> 個</label></span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm w-8em modal-btn-status-ok">はい</button>
                <button type="button" class="btn btn-primary confirm w-8em close-modal" data-dismiss="modal">いいえ</button>
            </div>
        </div>
    </div>
</div>
@endsection