@extends('layouts.layout')
@section("title","案件入力")
@section("css")
<link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link href="/css/matter.css" rel="stylesheet">
@endsection
@section("js")
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js" type="text/javascript"></script>
<script src="/js/bootstrap-datetimepicker.min.js"></script>
<script src="/js/autokana.js"></script>
<script src="/js/main.js"></script>
<script src="/js/matter.js"></script>
<script>
</script>
@if($flgSagyo)
<script>
    $(document).ready(function() {
        $('html, body').animate({
            scrollTop: $('#useMaterial').offset().top
        }, 'slow');
    })
</script>
@endif
@endsection



@section('content')
<main class="p-2">
    <form action="/matterinsert" method="post" class="form" id="submitbuttonform" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="header-content">
            <span class="buttonForm">
                <span class="link">
                    <h2>案件入力 </h2>
                    <span class="menu"><a id="receptionmenu">受付</a><a id="requestermenu">工事先</a><a id="constructionmenu">依頼者</a><a id="workmenu">作業</a><a id="photomenu">写真</a><a id="claimmenu" style="border: none;">請求</a></span>
                </span>
                <div class="form-group">
                    <?php
                    $FlgPaymentConfirm = "";
                    if ($WaterWork->FlgPaymentConfirm) {
                        $FlgPaymentConfirm = " readonly ";
                    }
                    if ($visible) {
                        $disaible = "";
                        $style = "";
                    } else {
                        $disaible = "disabled";
                        $style = "style=margin-left:230px;";
                    }
                    $btnPaymentDisabled = (!Auth::user()->ManageCls && $WaterWork->FlgPaymentConfirm) ? 'disabled' : '';
                    ?>
                    <button type="button" name="btn" value="save" class="button btn btn-primary save" {{$style}}>登録</button>
                    <input type="hidden" class="ManageCls" name="ManageCls" value="{{ Auth::user()->ManageCls }}" />
                    <input type="hidden" class="FlgPaymentConfirm" name="FlgPaymentConfirm" value="{{ $WaterWork->FlgPaymentConfirm }}" />
                    <input type="hidden" class="checkExportRyoshu" name="checkExportRyoshu" value="{{ (!$WaterWork->RecOutputUserID && !$WaterWork->RecOutputDate) ? '':'1' }}" />
                    @if($visible && ((Auth::user()->ManageCls && !$WaterWork->FlgPaymentConfirm) || (!Auth::user()->ManageCls)))
                    <button type="button" {{ $btnPaymentDisabled }} name="btn" value="btnPayment" class="button btn btn-primary btnPayment">入金確認</button>
                    @endif
                    @if(Auth::user()->ManageCls && $WaterWork->FlgPaymentConfirm)
                    <button type="submit" name="btn" value="btnResetPayment" class="button btn btn-primary btnResetPayment w-9em">入金確認解除</button>
                    @endif
                    @if($visible)
                    <button type="button" value="{{ $WaterWork->FlgPaymentConfirm ? '1' : '' }}" class="button btn btn-primary btnDelete">削除</button>
                    @endif
                    <div>
                        <span style="margin-left: 23px;">帳票</span>
                        <select class="form-control form-control-sm selectFormNM ExportData d-inline-block" name="ExportData">
                            <option value=""></option>
                            <option type="xlsx" url="/ExportUketsuke" value="uketsuke">給排水設備修繕受付書</option>
                            <option type="pdf" url="/ExportSeikyu" value="seikyu">請求書</option>
                            <option type="xlsx" url="/ExportUchiWake" value="uchiwake">使用資材内訳書</option>
                            <option type="xlsx" url="/ExportMitsuMore" value="mitsumori">見積書</option>
                            <option type="pdf" url="/ExportNohin" value="nohin">納品書</option>
                            <option type="pdf" url="/ExportRyoshu" value="ryoshu">領収証</option>
                        </select>
                        <button type="button" name="btn" value="save" class="button btn btn-primary output btnExport">出力</button>
                    </div>
                </div>
            </span>
            <hr>
        </div>
        <div class="mg-scroll">
            @include("matter.uketsuke")
            @include("matter.iraisya")
            @include("matter.kouzisaki")

            @include("matter.sagyou")
            @include("matter.shashin")
            @include("matter.seikyuu")
        </div>
    </form>
    <!-- 領収書確認 -->
    <div class="modal fade" id="RyoShuConfirmModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-header-error bg-danger">
                    <h5 class="modal-title text-white">実行確認</h5>
                </div>
                <div class="modal-body">
                    <p class="m-0">領収書を再出力します。</p>
                    <p class="m-0">タイトルに「（再）」を付与しますか？</p>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-primary close-modal btnOkRyoShu w-7em" value="" data-dismiss="modal">付与する</button>
                    <button type="button" class="btn btn-primary close-modal btnOkRyoShu w-7em" value="1" data-dismiss="modal">付与しない</button>
                    <button type="button" class="btn btn-danger close-modal w-7em" data-dismiss="modal">キャンセル</button>
                </div>
            </div>
        </div>
    </div>

    <div id="sonuc_grid" tabindex=44></div>

    <img id="TopBtn" alt="トップへ" title="トップへ" src="/img/top.PNG" />
    <script>
    </script>
</main>
@endsection