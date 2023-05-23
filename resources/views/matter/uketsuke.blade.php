<div class="collapsetitle pb-3" id="reception" data-toggle="collapse" href="#collapseReception" aria-expanded="false" aria-controls="collapseReception">
    <i class="fa fa-minus-square-o minus" aria-hidden="true"></i><i class="fa fa-plus-square-o plus" aria-hidden="true"></i>【受付】
</div>
<div class="collapse show" id="collapseReception">
    <div class="card reception-body">
        <style>
            .reception-body {
                border: none;
                width: 100%;
            }
        </style>
        <table>
            <!-- 受付table -->
            @if($visible)
            <tr>
                <td class="lbl">No.</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control text" name="WWRecID" value="{{ @$WaterWork->WWRecID }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
                        <input type="hidden" name="WWRecID" value="{{ @$WaterWork->WWRecID }}">
                        <input type="hidden" name="WWID" value="{{ @$WWID }}">
                    </div>
                </td>
            </tr>
            @else
            <input type="hidden" name="WWID" value="{{ @$WWID }}">
            <input type="hidden" id="WWRecID" name="WWRecID" value="">
            @endif
            <tr>
                <td class="lbl">案件名<span id="WWName">(必須)</span></td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="WWName" value="{{old('WWName',@$WaterWork->WWName) }}" autocomplete="off" maxlength="64" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">工事区分<span id="WWType">(必須)</span></td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="WWType">
                            <option value=''></option>
                            @foreach($ListType as $k=>$v)
                            <option {{ old("WWType") == $v->InternalValue ? 'selected' : '' }} {{ @$WaterWork->WWType == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="lbl">受付日</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" autocomplete="off" class="form-control datetimepicker1" name="WWDateTime" value="{{old('WWDateTime',@$WaterWork->WWDateTime) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                        <div class="input-group-append">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        </div>

                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">受付区分</td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="WWReceptType">
                            <option value=''></option>
                            @foreach($ListRecept as $k=>$v)
                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} {{ @$WaterWork->WWReceptType == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">診断番号</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control WWReceptNo" name="WWReceptNo" value="{{old('WWReceptNo',@$WaterWork->WWReceptNo) }}" autocomplete="off"  maxlength="4">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">地図</td>
                <td>
                    <div class="input-group select d-inline-block ">
                        <select class="form-control form-control-sm WWAdress" name="WWAdress">
                            <option value=''></option>
                            @foreach($ListAdress as $k=>$v)
                            <option {{ old("WWAdress") == $v->InternalValue ? 'selected' : '' }} {{ @$WaterWork->WWAdress == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                            @endforeach
                        </select>
                    </div>


                    <?php
                    if ($WaterWork) {
                        $WWHouseNum = explode("-", $WaterWork->WWHouseNum);
                    }
                    ?>
                    <div class="input-group input-group-sm post d-inline-block">
                        <input type="text" class="form-control post Chome" maxlength="5" name="Chome" value="{{old('Chome',@$WWHouseNum[0])}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                    -
                    <div class="input-group input-group-sm post d-inline-block">
                        <select class="form-control form-control-sm Address" name="Address">
                            <?php
                            $htm = "";
                            $oldview = false;
                            for ($address = 'A'; $address <= "J"; $address++) {
                                $selected  = (old("Address") == $address || (isset($WWHouseNum[1]) && $address == $WWHouseNum[1])) ? "selected" : "";
                                $htm .= '<option ' . $selected . ' value="' . $address . '">' . $address . '</option>';
                                if ($selected) $oldview = true;
                            }
                            ?>
                            @if(isset($WWHouseNum[1]) && $WWHouseNum[1] && !$oldview)
                            <option value='{{$WWHouseNum[1]}}'>{{$WWHouseNum[1]}}</option>
                            @endif
                            <option value=''></option>
                            {!! $htm !!}
                        </select>
                        <!-- <input type="text" class="form-control post Address" maxlength="5" name="Address" value="{{old('Address',@$WWHouseNum[1])}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                    </div>
                    -
                    <div class="input-group input-group-sm post d-inline-block">
                        <select class="form-control form-control-sm Number" name="Number">
                            <?php
                            $htm = "";
                            $oldview = false;
                            for ($num = 1; $num <= 5; $num++) {
                                $selected  = (old("Address") == $num || (isset($WWHouseNum[2]) && $num == $WWHouseNum[2])) ? "selected" : "";
                                $htm .= '<option ' . $selected . ' value="' . $num . '">' . $num . '</option>';
                                if ($selected) $oldview = true;
                            }
                            ?>
                            @if(isset($WWHouseNum[1]) && $WWHouseNum[1] && !$oldview)
                            <option value='{{$WWHouseNum[1]}}'>{{$WWHouseNum[1]}}</option>
                            @endif
                            <option value=''></option>
                            {!! $htm !!}
                        </select>
                        <!-- <input type="text" class="form-control post Number" maxlength="5" name="Number" value="{{old('Number',@$WWHouseNum[2])}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">扱者</td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="WWHandlerID">
                            @if($WaterWork)
                            @if($WaterWork->WWHandlerID)
                            <option value=''></option>
                            @foreach($ListUser as $k=>$v)
                            <option {{ old("ClaimUserID") == $v->UserID ? 'selected' : '' }} {{ @$WaterWork->WWHandlerID== $v->UserID ? 'selected' : '' }} value="{{ @$v->UserID }}">{{ $v->UserNM }}</option>
                            @endforeach
                            @endif

                            @if(!$WaterWork->WWHandlerID)
                            <option value=''></option>
                            @foreach($ListUser as $k=>$v)
                            <option {{ old("ClaimUserID") == $v->UserID ? 'selected' : '' }} {{ @Auth::user()->UserNM == $v->UserNM ? 'selected' : '' }} value="{{ @$v->UserID }}">{{ $v->UserNM }}</option>
                            @endforeach
                            @endif
                            @endif

                            @if(!$WaterWork)
                            <option value=''></option>
                            @foreach($ListUser as $k=>$v)
                            <option {{ old("ClaimUserID") == $v->UserID ? 'selected' : '' }} {{ @Auth::user()->UserNM == $v->UserNM ? 'selected' : '' }} value="{{ @$v->UserID }}">{{ $v->UserNM }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <td valign="top" class="lbl">受付状況</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <textarea class="form-control input-lg" id="" name="SurveyStatus" value="">{!! old('SurveyStatus',@$WaterWork->SurveyStatus) !!}</textarea>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>