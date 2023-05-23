<!-- Modal Popup Table -->
<div class="modal fade modal-table" id="ProjectModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">工事検索</h4>
            </div>
            <div class="modal-body">
                <div class="modal-loading">
                    <div class="spinner-border text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="margin-btn-tablelistkoji">
                    <button name="BtnSearchTableListKoji" class="btn btn-primary btn-custom" id="BtnSearchTableListKoji" value="">検索</button>
                    <button name="BtnClearTablelistkoji" class="btn btn-primary btn-custom" value="" id="BtnClearTablelistkoji">条件クリア</button>
                </div>
                <div class=" table-responsive">
                    <table class="tablepopup tablelistkoji table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <div>工事区分</div>
                                    <div>
                                        <select name="TxtSearchKojikubun" id="TxtSearchKojikubun">
                                            <option value=""></option>
                                            <?php
                                            if (isset($listKojiKubun) && $listKojiKubun) {
                                                foreach ($listKojiKubun as $kubun) {
                                                    echo '<option value="' . $kubun->DispText . '">' . $kubun->DispText . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </th>
                                <th>
                                    <div>指示番号</div>
                                    <div><input name="TxtSearchKojiID" id="TxtSearchKojiID" value="59" /> </div>
                                </th>
                                <th>
                                    <div>工事場所</div>
                                    <div><input name="TxtSearchKojiBasho" id="TxtSearchKojiBasho" value="" /> </div>
                                </th>
                                <th>
                                    <div>概要</div>
                                    <div><input name="TxtSearchGaiyo" id="TxtSearchGaiyo" value="" /> </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($listKoji) && $listKoji) {
                                foreach ($listKoji as $koji) {
                            ?>
                                    <tr>
                                        <td class="{{ $koji->kinkyu_id }}">{{ $koji->DispText }}</td>
                                        <td>{{ $koji->kinkyu_num }}</td>
                                        <td>{{ $koji->basho }}</td>
                                        <td>{{ $koji->gaiyou }}</td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-center">
                <button type="button" class="btn btn-primary w-8em" id="BtnPopupKettei">決定</button>
                <button type="button" class="btn btn-primary w-8em" data-dismiss="modal">キャンセル</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Image Zoom -->
<div class="modal fade " id="ImageZoomModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white"></h4>
            </div>
            <div class="modal-body text-center">

            </div>
            <div class="modal-footer  justify-center">
                <button type="button" class="btn btn-primary  close-modal" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Image Zoom -->
<div class="modal fade " id="MessageInfoModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary p-1 pl-3">
                <h4 class="modal-title text-white">メッセージ</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer  justify-center">
                <button type="button" class="btn btn-primary w-8em close-modal" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Order History -->
<div class="modal fade" id="OrderHistoryModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white"></h4>
            </div>
            <div class="modal-body">
                <table class="tablelist tablelistdetail">
                    <thead>
                        <tr>
                            <th>ステータス</th>
                            <th>発注数</th>
                            <th>発注日</th>
                            <th>発注者</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer  justify-center">
                <button type="button" class="btn btn-primary  close-modal w-8em" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Quantity -->
<div class="modal fade" id="QuantityModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">実数入力</h4>
            </div>
            <style>
                #FormStockDetail .text {
                    width: calc(100% - 5em);
                    padding: 2px 10px;
                    height: inherit;
                    font-size: inherit;
                    display: inline-flex;
                }

                #FormStockDetail .t {
                    width: 4.5em;
                }
            </style>
            <div class="modal-body">
                <form id="FormStockDetail" action="#" method="POST">
                    <div class="margin-modal">
                        <div class="margin-bottom">
                            <label class="t">品名</label>
                            <input type="text" class="form-control text" name="Qty-MaterialNM" value="" disabled />
                            <input type="hidden" class="form-control text" name="Qty-MaterialID" value="" />
                        </div>
                        <div class="margin-bottom">
                            <label class="t">規格</label>
                            <input type="text" class="form-control text" name="Qty-Type" value="" disabled />
                        </div>
                        <div class="margin-bottom">
                            <label class="t">品別名</label>
                            <input type="text" class="form-control text" name="Qty-MaterialAlias" value="" disabled />
                        </div>
                        <div class="margin-bottom mb-3">
                            <label class="t">棚卸日</label>
                            <div class="input-group input-group-sm mg-date text p-0 datetimepicker ">
                                <input type="text" autocomplete="off" class="form-control datetimepicker WorkTimeFrom " id="TxtDate" name="Qty-Date" value="">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="table table-responsive  scrollbar scrollbar-primary">
                            <table class="tablelist tablestockdetail">
                                <thead>
                                    <tr>
                                        <th>倉庫</th>
                                        <th>棚位置</th>
                                        <th>実数</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-center">
                <button type="button" class="btn btn-primary btn-Qty-Update w-8em">登録</button>
                <button type="button" class="btn btn-primary  close-modal w-8em" data-dismiss="modal">キャンセル</button>
            </div>
        </div>
    </div>
</div>
<!-- NewStock Modal  -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static" aria-hidden="true" id="NewStockModal">
    <div class="modal-dialog ">
        <div class="modal-content">
            <form action="/createNewStock" method="POST">
                {{ csrf_field() }}
                <div class="modal-header bg-primary p-1 pl-3 ">
                    <h4 class="modal-title text-white" id="myModalLabel">新規棚卸データ作成</h4>
                </div>
                <div class="modal-body">
                    <p class="t">以下の年月の棚卸新規データを作成します</p>
                    <div class="txtinput">
                        <input type="number" disabled value="{{ Carbon\Carbon::today()->format('Y') }}" name="TxtYear" class="form-control" /> <span>年</span>　<input name="TxtMonth" type="number" value="{{ Carbon\Carbon::today()->format('m') }}" disabled class="form-control" /> <span>月</span>
                        <input type="hidden" value="{{ $MaxStockID }}" name="MaxStockID" />
                    </div>
                    <div class="note">
                        <p>※1 データの上書きは行えません　　　　　</p>
                        <p>※2 現時点での在庫数でデータを作成します</p>
                    </div>
                </div>
                <div class="modal-footer justify-center">
                    <button type="submit" class="btn btn-primary w-8em" id="modal-btn-oke-newstock">作成</button>
                    <button type="button" class="btn btn-primary w-8em confirm close-modal" data-dismiss="modal">キャンセル</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (session('msg'))
<script>
    $(document).ready(function() {
        $("#MessageInfoShowModal .modal-body p").html('<?php echo session('msg')  ?>');
        $("#MessageInfoShowModal").modal();
    });
</script>
@endif