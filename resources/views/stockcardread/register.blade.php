@extends('layoutsphone.master')
@section('header')

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/gijgo@1.9.13/js/messages/messages.ja-jp.js" type="text/javascript"></script>
<link rel="stylesheet" href="{{ URL::asset('css/stockcardreader.css') }}">
@stop
@section('content')
<script>
    $(document).ready(function() {

        config = {
            locale: 'ja-jp',
            uiLibrary: 'bootstrap4',
            format: "yyyy/mm/dd"
        };
        $('.date').datepicker(config);

        CheckDate();

        function CheckDate() {
            $('.date').on('focusin', function() {
                $(this).data('val', $(this).val());
            });
            $(".date").on("change", function() {
                if (!moment($(this).val(), 'YYYY/MM/DD', true).isValid() && $(this).val()) {
                    $(this).val($(this).data('val'));
                    $("#MessageModal .modal-body").html(getMsgByID("error023"));
                    $("#MessageModal").modal();
                    $("#MessageModal .close-modal").focus();
                }
            });
        }
    });
</script>
<style>
    </style>
<section>
    <form action="/setStockDetail" method="POST">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" value="{{ $StockID }}" name="StockID" />
        <input type="hidden" value="" name="StoreID" />
        <input type="hidden" value="" name="ShelfID" />
        <input type="hidden" value="" name="MaterialID" />
        <div class="h-title">棚卸登録</div>
        <table>
            <tr class="t1">
                <td><label>棚卸日</label>
                </td>
                <td><input type="text" class="form-control date" value="" name="TxtStockDate" />
                </td>
            </tr>
            <tr class="t1">
                <td><label>倉庫</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtStoreNM" />
                </td>
            </tr>
            <tr class="t1 d-none">
                <td> <label>棚</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtShelfNM" />
                </td>
            </tr>
            <tr class="t1">
                <td><label>資材ID</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtMaterialID" />
                </td>
            </tr>
            <tr class="t1">
                <td><label>品名</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtMaterialNM" />
                </td>
            </tr>
            <tr class="t1">
                <td><label>規格</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtType" />
                </td>
            </tr>
            <tr class="t1 d-none">
                <td><label>資材別名</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtMaterialAlias" />
                </td>
            </tr>
            <tr class="t1">
                <td><label>在庫数</label>
                </td>
                <td><input type="text" disabled class="form-control" value="" name="TxtStockNum" />
                </td>
            </tr>
            <tr class="t1">
                <td><label>実数</label>
                </td>
                <td><input type="number" class="form-control" min=0 value="" name="TxtRealStockNum" step=".1" />
                </td>
            </tr>
            <tr class="t1">
                <td colspan="2">
                    <div class="margin-btn">
                        <button type="submit" class="btn btn-primary btn-custom btn-custom2">登録</button>
                        <button type="button" onclick="window.location.href = 'readcard'" class="btn btn-primary btn-custom btn-custom2">戻る</button>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <button type="button" onclick="window.location.href = 'readcard'" class="btn btn-primary btn-custom  btn-barcode-back">戻る</button>
</section>
@include('stockcardread.barcode')
@stop