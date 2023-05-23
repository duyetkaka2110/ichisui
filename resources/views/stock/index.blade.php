@extends('layouts.layout')
@section('title','棚卸一覧')
@section('css')
<link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('css/stock.css') }}">
@endsection
@section("js")
<?php
if (Session::has("StockID")) {
?>
    <script>
        $(document).ready(function() {
            $("#TxtStockYM").val(<?php echo Session::get("StockID") ?>);
            $("input[name='TxtStockID']").val(<?php echo Session::get("StockID") ?>);
            UpdateListStock();
        });
    </script>
<?php
    Session::forget('StockID');
}
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js" type="text/javascript"></script>
<script src="/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ URL::asset('js/stock.js') }}"></script>

@endsection
@section('content')
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="margin-all-background">
                <div class="margin-btn-select">
                    <div class="t1">
                        <label>棚卸年月の選択</label>
                        <div class="mar-btn">
                            <select class="form-control" name="TxtStockYM" id="TxtStockYM">
                                <option value=""></option>
                                <?php
                                if (isset($ListStockYM) && $ListStockYM) {
                                    foreach ($ListStockYM as $s) {
                                        echo "<option value='" . $s->StockID . "'>" . $s->StockYM . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="t1">もしくは</div>
                    <div class="t1">
                        <div class="mar-btn">
                            <button class="btn btn-primary btn-custom" MaxStockID="{{ $MaxStockID }}" {{ $disabledNew }} data-toggle="modal" data-target="#NewStockModal">棚卸新規作成</button>
                        </div>
                    </div>
                    <div class="clear_left"></div>
                </div>
                <div class="margin-search-title">
                    <label class="title-search toggle"><i class="fa fa-minus-square-o" aria-hidden="true"></i> 検索条件</label>
                    <div class="margin-search">

                        <table class="table-margin-1">
                            <tr>
                                <td>
                                    <div>
                                        <label>資材ID</label>
                                        <input type="text" class="form-control resettext" name="TxtMaterialID" value="" />
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <label>品名</label>
                                        <input type="text" name="TxtMaterialNM" value="" class="form-control resettext" />
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <label>規格</label>
                                        <input type="text" name="TxtType" value="" class="form-control resettext" />
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <label>資材区分</label>
                                        <select class="form-control resettext text TxtMaterialCls" name="TxtMaterialCls">
                                            <option value=""></option>
                                            <?php
                                            foreach ($listMaterialCls as $l) {
                                                echo '<option value="' . $l->InternalValue . '">' . $l->DispText . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <label for="TxtStockDate">棚卸日が空欄 </label>
                                        <input type="checkbox" style="width: auto" name="TxtStockDate" id="TxtStockDate" value="" class="form-control resettext" />

                                    </div>
                                </td>
                                <td>
                                    <div class="margin-btn-search">
                                        <button class="btn btn-primary btn-custom BtnSearch">検索</button>
                                        <button class="btn btn-primary btn-custom BtnResetSearch mt-1">クリア</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="margin-btn-action">
                    <div class="float_left">
                        <label id="ResultSearchCount">検索結果：<span>0</span>件</label>
                        <button class="btn btn-primary btn-custom btn-InvenConfirm">棚卸確定</button>
                        @if(Auth::user()->ManageCls)
                        <button class="btn btn-primary btn-custom btn-InvenConfirm-Delete">棚卸確定解除</button>
                        @endif
                        <button class="btn btn-primary btn-custom btn-InvenCorrec">在庫一括修正</button>
                        <span>
                            <label>在庫一括修正日</label>
                            <input type="text" class="form-control AmountAllFixDate" disabled value="" name="AmountAllFixDate" />
                        </span>
                    </div>
                    <div class="float_right">
                        <form action="/importStock" enctype="multipart/form-data" class="importfile" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="TxtStockID" value="" />
                            <input type="file" name="FileImport" class="hidden" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                            <button type="button" class="btn btn-primary btn-custom Btn-Import">Excel読込</button>
                            <button type="button" class="btn btn-primary btn-custom Btn-Export">Excel出力</button>
                        </form>
                    </div>
                    <div class="clear_left"></div>
                </div>
                <div id="tablesroll" class="">
                    <table class="tablelist tablelistmain tablestock table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="w-3em text-center">NO</th>
                                <th class="w-5em text-center" >資材ID</th>
                                <th class="w- text-center">品名</th>
                                <th class="w- text-center">規格</th>
                                <th class="w-5em text-center">在庫数</th>
                                <th class="w-4em text-center">実数</th>
                                <th class="w-8em text-center">棚卸日</th>
                            </tr>
                        </thead>
                        <tbody class="tbody" style="width: 100%">
                        </tbody>
                    </table>
                    
                    <nav aria-label="...">
                        <ul class="pagination matterialpagi">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

@include('stock.popup')
@endsection