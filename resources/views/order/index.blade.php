@extends("layouts.layout")
@section('title', '資材発注')
@section("css")
<link rel="stylesheet" href="/css/order.css">
@endsection

@section("js")
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.1.4/jquery.floatThead.js"></script>
<script src="/js/order.js"></script>
@endsection

@section("content")
<main class=" p-3">
    <div class="main-header pt-3 pb-3">
        <div class="m-title">
            <h4 class="pb-2">資材発注</h4>
        </div>
        <div class=" row">
            <div class="m-h-left text-left col pl-3">
                @if(Auth::user()->ManageCls)
                <button class="btn btn-primary pt-1 btnBulk">一括発注追加(在庫不足分)</button>
                @endif
                <button class="btn btn-primary pt-1 btnAddBulk">個別発注追加</button>
            </div>
            <div class="m-h-right text-right col pr-3">
                <button class="btn btn-primary pt-1 btnExport">発注</button>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div>
            <label id="ResultSearchCount">検索結果：<span>{{$list->total()}}</span>件</label>
        </div>
        <table class="table table-hover table-order">
            <thead class="bg-light">
                <tr>
                    <th scope="col" class="w-5em">資材ID</th>
                    <th scope="col" class="">品名</th>
                    <th scope="col" class="">規格</th>
                    <th scope="col" class="w-9em">発注先</th>
                    <th scope="col" class="w-5em">在庫数</th>
                    <th scope="col" class="w-6em">発注済数</th>
                    <th scope="col" class="w-8em">仕入単価</th>
                    <th scope="col" class="w-6em">発注数</th>
                    <th scope="col" class="w-7em">発注済扱い</th>
                    <th scope="col" class="w-4em">削除</th>
                </tr>
            </thead>
            <tbody>
                @if($list->total())
                @foreach($list as $k=>$l)
                <tr class="tr-{{ $l->UseMaterialID }}">
                    <td scope="col">{{ @$l->MaterialID }}</td>
                    <td scope="col">{{ @$l->MaterialNM }}</td>
                    <td scope="col">{{ @$l->Type }}</td>
                    <td scope="col">
                        <select class="form-control p-0 td-supplier" UseMaterialID="{{ $l->UseMaterialID }}">
                            <option value=""></option>
                            @foreach($listSupplier as $s)
                            <?php
                            $selected = "";
                            if ($s->SupplierID == $l->SupplierID)  $selected = "selected";
                            ?>
                            <option {{ $selected }} value="{{ @$s->SupplierID }}">{{ @$s->SupplierNM }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td scope="col" class=" text-right">{{ @$l->StockNum }}{{ @$l->UseNM }}</td>
                    <td scope="col" class=" text-right">{{ $l->OrderNumTotal ?  $l->OrderNumTotal : 0 }}{{ @$l->UnitNM }}</td>
                    <td scope="col" class="p-1 text-center">
                        <div class="input-group">
                            <input type="number" value="{{ $l->PurUnit ? $l->PurUnit : 0 }}" min=0 name="PurUnit" Use="{{ $l->UseMaterialID }}" class="height-inherit p-0 form-control tbl-input text-right">
                            <div class="input-group-append">
                                <span class="input-group-text p-0 pl-1 pr-1 font-13">円</span>
                            </div>
                        </div>
                    </td>
                    <td scope="col" class="p-1 text-center">
                        <div class="input-group">
                            <input type="number" value="{{ @$l->OrderNum }}" min=0 name="OrderNum" Use="{{ $l->UseMaterialID }}" class="height-inherit p-0 form-control text-right tbl-input">
                            <div class="input-group-append">
                                <span class="input-group-text p-0 pl-1 pr-1 font-13">{{ @$l->UnitNM }}</span>
                            </div>
                        </div>
                    </td>
                    <td scope="col" class=" text-center btn-switch">
                        <div class="custom-control custom-switch text-center">
                            <input type="checkbox" class="custom-control-input" {{ $l->TreatOrder ? "checked" : "" }} name="" id="customSwitches{{ $k }}" data-update="UseMaterialID={{ $l->UseMaterialID}}&MaterialID={{ $l->MaterialID}}&Flg=TreatOrder" status="{{ $l->OrderStatusCD}}">
                            <label class="custom-control-label cursor-point" for="customSwitches{{ $k }}"></label>
                        </div>
                    </td>
                    <td scope="col" class=" text-center">
                        <i class="fa fa-trash btn-delete cursor-point" aria-hidden="true" use={{ $l->UseMaterialID }} data="MaterialID={{ $l->MaterialID}}&UseMaterialID={{ $l->UseMaterialID}}"></i>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="10" class="text-center">対象データがありません。</td>
                </tr>
                @endif
            </tbody>
        </table>
        <div class="mg-pagination">
            {{ $list->links("vendor.pagination.bootstrap-4") }}
        </div>
    </div>
</main>
@endsection

@section("modal")
<!-- Modal Material -->
<div class="modal fade" id="MaterialModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-error bg-primary p-2 text-center">
                <h5 class="modal-title text-white　">資材検索画面</h5>
            </div>
            <div class="modal-body">
                <div class="m-header w-100 p-2 mb-3 mt-3">
                    <div class="position-absolute m-title">検索条件</div>
                    <table class="w-100 form-search">
                        <tr>
                            <td>
                                <label class="m-0">資材ID</label>
                                <input name="txtMaterialID" class="w-6em form-control d-inline txtsearch p-0 txtMaterialID" value="" />
                            </td>
                            <td>
                                <label class="m-0">品名</label>
                                <input name="txtMaterialNM" class="w-6em form-control d-inline txtsearch p-0 txtMaterialNM" value="" />
                            </td>
                            <td>
                                <label class="m-0">規格</label>
                                <input name="txtType" class="w-6em form-control d-inline txtsearch p-0 txtType" value="" />
                            </td>
                            <td>
                                <button class="btn btn-secondary w-100 p-0 btn-mater-search">検索</button>
                                <button class="btn btn-secondary w-100 p-0 mt-1 btn-clear">クリア</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="m-content ">
                    <table class="table table-hover table-marterial " id="tablemarterial">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="w-5em">資材ID</th>
                                <th scope="col" class="">品名</th>
                                <th scope="col" class="">規格</th>
                                <th scope="col" class="w-7em">発注先</th>
                                <th scope="col" class="w-5em">在庫数</th>
                                <th scope="col" class="w-6em">発注済数</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <nav aria-label="...">
                        <ul class="pagination matterialpagi">
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-primary btnOk w-6em">決定</button>
                <button type="button" class="btn btn-danger close-modal w-6em" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
@endsection