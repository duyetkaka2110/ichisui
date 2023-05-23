<div class="collapsetitle pb-3" id="claim" data-toggle="collapse" href="#collapseBillingaddress" aria-expanded="false" aria-controls="collapseBillingaddress">
    <i class="fa fa-minus-square-o minus" aria-hidden="true"></i><i class="fa fa-plus-square-o plus" aria-hidden="true"></i>【請求】
</div>
<div class="collapse show" id="collapseBillingaddress">
    <div class="card collapsePhoto-body">
        <style>
            .collapsePhoto-body {
                padding: 0;
                border: none;
                width: 80%;
            }
        </style>
        <!-- 請求table -->
        <table>
            <tr>
                <?php
                if ($WaterWork) {
                    if ($WaterWork->ReqAdress == $WaterWork->ClaimAdress && $WaterWork->ClaimName == $WaterWork->ConstrName && $WaterWork->ReqBuilding == $WaterWork->ClaimBuilding && $WaterWork->ReqTEL == $WaterWork->ClaimTEL) {
                        $ClaimCopyChecked = "checked";
                        $ClaimDisabled = "disabled";
                    } else {
                        $ClaimCopyChecked = "";
                        $ClaimDisabled = "";
                    }
                } else {
                    $ClaimCopyChecked = "checked";
                    $ClaimDisabled = "disabled";
                }
                ?>

                <td colspan="2">
                    <label>
                        <input type="checkbox" {{$ClaimCopyChecked}} id="claimCopyCheck" name="claimCopyCheck" value="1">工事先に同じ
                    </label>
                </td>

            </tr>
            <tr>
                <td class="lbl">住所</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ClaimDisabled}} class="form-control longtext" name="ClaimAdress" maxlength="64" value="{{old('ClaimAdress',@$WaterWork->ClaimAdress) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">氏名</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ClaimDisabled}} class="form-control longtext" name="ClaimName" maxlength="64" value="{{old('ClaimName',@$WaterWork->ClaimName) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">建物名</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-building" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ClaimDisabled}} class="form-control" name="ClaimBuilding" maxlength="255" value="{{old('ClaimBuilding',@$WaterWork->ClaimBuilding) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">電話番号</td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-phone" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ClaimDisabled}} class="form-control " name="ClaimTEL" maxlength="64" value="{{old('ClaimTEL',@$WaterWork->ClaimTEL) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">請求日</td>
                <td>
                    <div class="input-group input-group-sm datetimepicker2">

                        <input type="text" autocomplete="off" class="form-control datetimepicker2" name="ClaimDate" value="{{old('ClaimDate',@$WaterWork->ClaimDate) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        <div class="input-group-append">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">請求書発行日</td>
                <td>
                    <div class="input-group input-group-sm datetimepicker2">

                        <input type="text" autocomplete="off" class="form-control datetimepicker2" name="PaymentIssueDate" value="{{old('PaymentIssueDate',@$WaterWork->PaymentIssueDate) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        <div class="input-group-append">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="lbl">支払い区分</td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="ClaimType">
                            <option value=''></option>
                            @foreach($ListClaimType as $k=>$v)
                            <option {{ old("ClaimType") == $v->InternalValue ? 'selected' : '' }} {{ @$WaterWork->ClaimType == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">担当者</td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="ClaimUserID">
                            <option value=''></option>
                            @foreach($ListUser as $k=>$v)
                            <option {{ old("ClaimUserID") == $v->UserID ? 'selected' : '' }} {{ @$WaterWork->ClaimUserID == $v->UserID ? 'selected' : '' }} value="{{ @$v->UserID }}">{{ $v->UserNM }}</option>
                            @endforeach
                        </select>
                    </div>

                </td>
            </tr>
            <tr>
                <td class="lbl">入金状況</td>
                <td>
                    <div class="input-group select">
                        @if( $FlgPaymentConfirm)
                            <input type="hidden" name="PaymentStatus" value="{{@$WaterWork->PaymentStatus}}" />
                        @endif
                        <select {{ $FlgPaymentConfirm ? 'disabled' : ''}} class="form-control form-control-sm" name="PaymentStatus">
                            <option value=''></option>
                            @foreach($ListPaymentStatus as $k=>$v)
                            <option {{ old("PaymentStatus") == $v->InternalValue ? 'selected' : '' }} {{ @$WaterWork->PaymentStatus == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>
            <?php
            if ($WaterWork) {
                if ($WaterWork->PaymentStatus == 01) {
                    $PaymentDateVisible = "style=display:none;";
                } else {
                    $PaymentDateVisible = "style=visibility:visible;";
                }
            } else {
                $PaymentDateVisible = "style=visibility:visible;";
            }
            ?>
            <tr class="PaymentDate" {{$PaymentDateVisible}}>

                <td class="lbl">入金日</td>
                <td>
                    <div class="input-group input-group-sm datetimepicker2">
                        <input type="text" {{ $FlgPaymentConfirm }} autocomplete="off" class="form-control datetimepicker2" value="{{old('ClaimDate',@$WaterWork->PaymentDate) }}" name="PaymentDate" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        <div class="input-group-append">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td valign="top" class="lbl">請求メモ</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <textarea class="form-control input-lg" id="usage1input1" name="PaymentMemo" value="">{{old('PaymentMemo',@$WaterWork->PaymentMemo) }}</textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="table tbl-total" style="width: 50%;">
                        請求明細
                        <tr>
                            <th>請求種別</th>
                            <th>請求金額</th>
                        </tr>

                        <tr>
                            <td>材料費</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" class="form-control td-okane td-3point totalText" name="MaterialFee" id="MaterialFee" value="{{@$ListSellPrice}}" disabled aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <input type="hidden" class="form-control td-okane td-3point totalText" name="MaterialFee" id="MaterialFee" value="{{@$ListSellPrice}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>調査費</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text"  {{ $FlgPaymentConfirm }} class="form-control td-okane td-3point" name="SurveyFee" id="SurveyFee" value="{{old('SurveyFee ',@$WaterWork->SurveyFee ) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>技術料</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" {{ $FlgPaymentConfirm }}  class="form-control td-okane td-3point" id="TechFee" name="TechFee" value="{{old('TechFee',@$WaterWork->TechFee) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>産廃処分費</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" {{ $FlgPaymentConfirm }}  class="form-control td-okane td-3point" name="DisposalFee" id="DisposalFee" value="{{old('SurveyFee ',@$WaterWork->DisposalFee ) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>出張費</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" {{ $FlgPaymentConfirm }}  class="form-control td-okane td-3point" name="MaterialTravel" id="MaterialTravel" value="{{old('MaterialTravel ',@$WaterWork->TravelFee ) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td>その他</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" class="form-control td-okane td-3point" name="Others" id="others" value="{{old('Others ',@$WaterWork->Others ) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr> -->
                        <tr>
                            <td>出精値引き</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" {{ $FlgPaymentConfirm }}  class="form-control td-okane-dis td-3point" name="Discount" id="Discount" value="{{old('Discount ',@$WaterWork->Discount ) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <style>
                                .totalText:disabled {
                                    color: #000000;
                                    background: #f5f5f5;
                                }

                                .tbl-total .td-3point {
                                    text-align: right;
                                }

                                .total {
                                    background-color: #b0e0e6;
                                    font-weight: bold;
                                }
                            </style>
                            <td class="total">小計</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <?php
                                    if ($WaterWork) {
                                        $subtotal = ($ListSellPrice + $WaterWork->TechFee + $WaterWork->TravelFee + $WaterWork->SurveyFee + $WaterWork->DisposalFee + $WaterWork->Others) - $WaterWork->Discount;
                                        $tax = floor($subtotal * $Tax->InternalValue);
                                        $total = $subtotal + $tax; //$tax ;
                                    }
                                    ?>
                                    <input type="text" class="form-control totalText td-3point" id="subtotal" name="subtotal" value="{{old('subtotal', @$subtotal) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="total">消費税</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" class="form-control totalText td-3point" id="tax" name="tax" value="{{old('tax' ,@$tax)}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
                                    <input type="hidden" name="taxdate" id="taxdate" value="{{@$Tax->InternalValue}}" disabled>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="total">合計（税込）</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">￥</span>
                                    </div>
                                    <input type="text" class="form-control totalText  td-3point" id="total" name="total" value="{{old('total',@$total) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>