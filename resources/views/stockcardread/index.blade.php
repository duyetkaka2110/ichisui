@extends('layoutsphone.master', ['title' => 'バーコード読込選択'])
@section('header')

@stop
@section('content')
<style>
   
   .h-title {
        width: 100%;
        font-size: 20px;
        text-align: center;
        margin: 20px 0;
    }

    .t1 label {
        width: 5em;
        display: inline-block;
    }

    .t1 select {
        width: calc(100% - 5em - 5px);
        display: inline-block;
        padding: 5px;
        height: 30px;
        font-size: 12px;
    }

    .btn-custom2 {

        width: auto;
        padding: 5px 35px;
        line-height: unset;
        height: auto;
    }

    .margin-btn {
        text-align: center;
        margin-top: 10px;
    }

    .back {
        position: fixed;
        bottom: 10px;
        right: 10px;
    }
</style>
<section>
    <form action="/stockregister" method="POST">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <div class="h-title">資材カード読込</div>
        <div class="t1">
            <label>棚卸年月</label>
            <select class="form-control" required name="StockID" id="StockID">
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
        <div class="margin-btn">
            <button class="btn btn-primary btn-custom btn-custom2">バーコード読込</button>
        </div>
        <button  onclick="parent.history.back();return false;" class="btn btn-primary btn-custom back">戻る</button>
    </form>
</section>
@stop