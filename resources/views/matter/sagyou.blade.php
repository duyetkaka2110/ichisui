<div class="collapsetitle pb-3" id="work" data-toggle="collapse" href="#collapseWork" aria-expanded="false" aria-controls="collapseWork">
    <i class="fa fa-minus-square-o minus" aria-hidden="true"></i><i class="fa fa-plus-square-o plus" aria-hidden="true"></i>【作業】
</div>
<div class="collapse show" id="collapseWork">
    <div class="card work-body">
        <table class="worktable">
            <tr>
                <td class="lbl">作業状況</td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="WorkStatus">
                            <!-- <option value=''></option> -->
                            @foreach($ListWorkStatus as $k=>$v)
                            <option {{ old("WorkStatus") == $v->InternalValue ? 'selected' : '' }}{{ "未完了" == $v->InternalValue ? 'selected' : '' }}{{ @$WaterWork->WorkStatus == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" value="{{ @$WaterWork->WorkStatus }}" name="WorkStatusCheck" class="WorkStatusCheck" />
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <!-- スケジュール入力テーブル -->
                    <table class="table" id="schedule">
                        <thead>
                            スケジュール
                            <tr>
                                <th>作業日</th>
                                <th>開始</th>
                                <th>終了</th>
                                <th>作業区分</th>
                                <th>対象区分</th>
                                <th>作業場所</th>
                                <th>担当者</th>
                                <th>移動</th>
                                <th>作業</th>
                                <th>施工日出力対象</th>
                                <th>削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <style>
                                .input-group.input-group-sm.SchedDate {
                                    width: 95px;
                                }

                                .input-group.input-group-sm.SchedTime {
                                    width: 70px;
                                }

                                .input-group.input-group-sm.SchedTo {
                                    width: 70px;
                                }

                                .user-drop .dropdown-toggle {
                                    width: 100%;
                                    padding: 0;
                                }

                                .user-drop .dropdown-toggle::after {
                                    position: absolute;
                                    top: 14px;
                                    right: 4px;
                                }

                                .user-drop .dropdown-menu {
                                    transform: translate3d(0px, 38px, 0) !important;
                                    margin: 0;
                                    top: -2px !important;
                                }

                                .dropdown.form-control.user-drop {
                                    height: 32px;
                                    padding: 0;
                                }

                                .user-drop .dropdown-toggle .un {
                                    margin-right: -42px;
                                    padding-left: 8px;
                                    width: 250px;
                                    height: 32px;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    white-space: nowrap;
                                }

                                .user-drop .dropdown-item.active,
                                .user-drop .dropdown-item:active {
                                    color: inherit;
                                    background: inherit;
                                }

                                .flgOutputWorkDate {
                                    height: 20px;
                                }
                            </style>
                            <script>
                                // $(document).ready(function() {
                                // 期間の変更
                                //     $('.SchedFrom').datetimepicker()
                                //         .on('changeDate', function(e) {
                                //             console.info("54654")
                                //         });
                                //     $('.SchedTo').datetimepicker()
                                //         .on('changeDate', function(e) {
                                //             getSummary($('#start-date').val(), $('#end-date').val());
                                //         });
                                //     $(document).on("change", ".SchedFrom", function(e) {
                                //         console.info("test  ")
                                //     });
                                // })
                            </script>
                            <tr class="d-none btn-add-content">
                                <td>

                                    <div class="input-group input-group-sm SchedDate">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker2 txtSchedTime" name="SchedTime[]" value="{{old('SchedTime') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    </div>
                                </td>
                                <td>

                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 SchedFrom" name="SchedFrom[]" value="00:00{{old('SchedFrom') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control SchedFrom txtSchedFrom TimeText" id="SchedFrom" name="SchedFrom[]" maxlength="4" value="{{@$DefaultDate}}{{old('SchedFrom') }}" autocomplete="off">
                                        <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                                        <!-- <div class="input-group-append">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                        </div> -->
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 SchedTo" name="SchedTo[]" value="00:00{{old('SchedTo') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control SchedTo txtSchedTo TimeText" id="SchedTo" name="SchedTo[]" maxlength="4" value="{{@$DefaultDate}}{{old('SchedFrom') }}" autocomplete="off">
                                        <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                                        <!-- <div class="input-group-append">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                        </div> -->
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtWorkType" name="WorkType[]">
                                            <option value=''></option>
                                            @foreach($ListWorkType as $k=>$v)
                                            <option {{ old("WorkType") == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}&{{ @$v->FreeText1 }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtTargetType" name="TargetType[]">
                                            <option value=''></option>
                                            @foreach($ListTargetType as $k=>$v)
                                            <option {{ old("TargetType") == $v->InternalValue ? 'selected' : '' }}value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtWorkPlace" name="WorkPlace[]">
                                            <option value=''></option>
                                            @foreach($ListWorkPlace as $k=>$v)
                                            <option {{ old("WorkPlace") == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <style>
                                    </style>
                                    <div class="dropdown form-control user-drop">
                                        <!-- id="dropdownMenu1" -->
                                        <div class="btn btn-default dropdown-toggle dropdownMenu1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <div class="un">
                                                @foreach ($ListUser as $v)
                                                <?php
                                                $old_userid = (array) old("UserID");
                                                foreach ($old_userid as $k => $uid) {
                                                    if ($v->UserID == $uid) {
                                                ?>
                                                        <span class="drop drop-{{$uid}}" userid="{{$uid}}">
                                                            @if($k==0)
                                                            {{ $v->UserNM }}
                                                            @else
                                                            {{ ",". $v->UserNM }}
                                                            @endif
                                                        </span>

                                                <?php
                                                        break;
                                                    }
                                                }
                                                ?>
                                                @endforeach
                                            </div>
                                            <span class="caret"></span>
                                        </div>

                                        <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                                            @foreach ($ListUser as $k=>$v)
                                            <li class="pl-2 pr-1 dropdown-item li-{{ $v->UserID }}">
                                                <label class="m-0">
                                                    <input type="checkbox" class="usercheck" name="UserID[][]" value="{{ $v->UserID }}"> <span>{{ $v->UserNM }}</span>
                                                </label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 txtTravelTime" name="TravelTime[]" value="00:00{{old('TravelTime') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <!-- ,@$Work->TravelTime -->
                                        <input type="text" class="form-control txtTravelTime TimeText" id="TravelTime" name="TravelTime[]" maxlength="4" value="{{@$DefaultDate}}{{old('TravelTime') }}" autocomplete="off">
                                        <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                                        <!-- <div class="input-group-append">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                        </div> -->
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 txtWorkTime" name="WorkTime[]" value="00:00{{old('WorkTime') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <!-- ,@$Work->WorkTime -->

                                        <input type="text" class="form-control txtWorkTime TimeText" id="WorkTime" name="WorkTime[]" maxlength="4" value="{{@$DefaultDate}}{{old('WorkTime') }}" autocomplete="off">
                                        <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                                        <!-- <div class="input-group-append">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                        </div> -->
                                    </div>
                                </td>
                                <td><input type="checkbox" name="flgOutputWorkDate[]" class="form-control flgOutputWorkDate txtflgOutputWorkDate" value="1"></td>
                                <td><i class="fa fa-trash scheduleDelete" aria-hidden="true"></i></td>
                            </tr>

                            <!-- old data  -->
                            <?php
                            $Count = 0;
                            ?>
                            @foreach($Works as $kwork=>$Work)
                            <tr class="oldtr-{{@$Count}} schedule">
                                <td>
                                    <div class="input-group input-group-sm SchedDate">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker2 txtSchedTime" name="SchedTime[old{{$kwork}}]" value="{{old('SchedTime',@$Work->WorkDate) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">

                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <input type="text" class="form-control SchedFrom txtSchedFrom TimeText" id="SchedFrom" name="SchedFrom[old{{$kwork}}]" maxlength="4" value="{{old('SchedFrom',@$Work->WorkFromTime) }}">
                                        <!-- <input type="text" class="form-control datetimepicker3 SchedFrom" name="SchedFrom['old{{$kwork}}']" value="{{old('SchedFrom',@$Work->WorkFromTime) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->

                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 SchedTo" name="SchedTo['old{{$kwork}}']" value="{{old('SchedTo',@$Work->WorkTo) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control SchedTo txtSchedTo TimeText" id="SchedTo" name="SchedTo[old{{$kwork}}]" maxlength="4" value="{{old('SchedFrom',@$Work->WorkTo) }}" autocomplete="off">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtWorkType" name="WorkType[old{{$kwork}}]">
                                            <option value=''></option>
                                            @foreach($ListWorkType as $k=>$v)
                                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} {{ @$Work->WorkType == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}&{{ @$v->FreeText1 }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtTargetType" name="TargetType[old{{$kwork}}]">
                                            <option value=''></option>
                                            @foreach($ListTargetType as $k=>$v)
                                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} {{ @$Work->TargetType == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtWorkPlace" name="WorkPlace[old{{$kwork}}]">
                                            <option value=''></option>
                                            @foreach($ListWorkPlace as $k=>$v)
                                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} {{ @$Work->WorkPlace == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown form-control user-drop">
                                        <div class="btn btn-default dropdown-toggle dropdownMenu1" id="dropdownMenu1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <div class="un">
                                                @foreach ($Work->UserID as $key=>$k)
                                                <span class="drop drop-{{$k->UserID}}" userid="{{$k->UserID}}">
                                                    @if($key==0)
                                                    {{ $k->UserNM }}
                                                    @else
                                                    {{ ",". $k->UserNM }}
                                                    @endif
                                                </span>
                                                @endforeach
                                                @foreach ($ListUser as $v)
                                                <?php
                                                $old_userid = (array) old("UserID");
                                                foreach ($old_userid as $k => $uid) {
                                                    if ($v->UserID == $uid) {
                                                ?>
                                                        <span class="drop drop-{{$uid}}" userid="{{$uid}}">
                                                            @if($k==0)
                                                            {{ $v->UserNM }}
                                                            @else
                                                            {{ ",". $v->UserNM }}
                                                            @endif
                                                        </span>

                                                <?php
                                                        break;
                                                    }
                                                }
                                                ?>
                                                @endforeach
                                            </div>
                                            <span class="caret"></span>
                                        </div>

                                        <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                                            @foreach ($ListUser as $k=>$v)
                                            <?php
                                            $UserChecked = "";
                                            foreach ($Work->UserID as $k) {
                                                if ($v->UserID == $k->UserID) {
                                                    $UserChecked = "checked";
                                                    break;
                                                }
                                            }
                                            foreach ($old_userid as $k) {
                                                if ($v->UserID == $k) {
                                                    $UserChecked = "checked";
                                                    break;
                                                }
                                            }
                                            ?>
                                            <li class="pl-2 pr-1 dropdown-item li-{{ $v->UserID }}">
                                                <label class="m-0">
                                                    <input type="checkbox" class="usercheck" {{ @$UserChecked }} name="UserID[old{{$kwork}}][]" value="{{ $v->UserID }}"> <span>{{ $v->UserNM }}</span>
                                                </label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 txtTravelTime" name="TravelTime['old{{$kwork}}']" value="{{old('TravelTime',@$Work->TravelTime) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control txtTravelTime TimeText" id="TravelTime" name="TravelTime[old{{$kwork}}]" maxlength="4" value="{{old('TravelTime',@$Work->TravelTime) }}" autocomplete="off">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 txtWorkTime" name="WorkTime['old{{$kwork}}']" value="{{old('WorkTime',@$Work->WorkTime) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control txtWorkTime TimeText" id="txtWorkTime" name="WorkTime[old{{$kwork}}]" maxlength="4" value="{{old('txtWorkTime',@$Work->WorkTime) }}" autocomplete="off">
                                    </div>
                                </td>
                                <td><input type="checkbox" name="flgOutputWorkDate[old{{$kwork}}]" class="form-control flgOutputWorkDate" {{old('flgOutputWorkDate',@$Work->flgOutputWorkDate) ? "checked" : "" }} value="1"></td>
                                <td><i class="fa fa-trash scheduleDelete" aria-hidden="true"></i></td>
                            </tr>
                            <?php
                            $Count++;
                            ?>
                            @endforeach

                            @if(!$Works)
                            <!-- default show -->
                            <tr class="tr-0 schedule">
                                <td>
                                    <div class="input-group input-group-sm SchedDate">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker2 txtSchedTime" name="SchedTime[0]" value="{{old('SchedTime',@$WWDatetime) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">

                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 SchedFrom" name="SchedFrom[0]" value="00:00{{old('SchedFrom',@$WorkFrom) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <!-- <input type="text" class="form-control SchedFrom" name="SchedFrom[0]" value="{{old('SchedFrom',@$WorkFrom) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control SchedFrom TimeText" id="SchedFrom" name="SchedFrom[0]" maxlength="4" value="{{@$DefaultDate}}{{old('SchedFrom',@$WorkFrom) }}" autocomplete="off">
                                        <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                                        <!-- <div class="input-group-append">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                        </div> -->
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 SchedTo" name="SchedTo[0]" value="00:00{{old('SchedTo',@$WorkTo) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control SchedTo TimeText" id="SchedTo" name="SchedTo[0]" maxlength="4" value="{{@$DefaultDate}}{{old('SchedTo',@$WorkTo) }}" autocomplete="off">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtWorkType" name="WorkType[0]">
                                            <option value='' selected></option>
                                            @foreach($ListWorkType as $k=>$v)
                                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}&{{ @$v->FreeText1 }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtTargetType" name="TargetType[0]">
                                            <option value=''></option>
                                            @foreach($ListTargetType as $k=>$v)
                                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control form-control-sm txtWorkPlace" name="WorkPlace[0]">
                                            <option value=''></option>
                                            @foreach($ListWorkPlace as $k=>$v)
                                            <option {{ old("WWReceptType") == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown form-control user-drop">
                                        <div class="btn btn-default dropdown-toggle dropdownMenu1" id="dropdownMenu1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <div class="un">
                                                @foreach ($ListUser as $v)
                                                <?php
                                                $old_userid = (array) old("UserID");
                                                foreach ($old_userid as $k => $uid) {
                                                    if ($v->UserID == $uid) {
                                                ?>
                                                        <span class="drop drop-{{$uid}}" userid="{{$uid}}">
                                                            @if($k==0)
                                                            {{ $v->UserNM }}
                                                            @else
                                                            {{ ",". $v->UserNM }}
                                                            @endif
                                                        </span>

                                                <?php
                                                        break;
                                                    }
                                                }
                                                ?>
                                                @endforeach
                                            </div>
                                            <span class="caret"></span>
                                        </div>

                                        <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                                            @foreach ($ListUser as $k=>$v)
                                            <?php
                                            $UserChecked = "";
                                            foreach ($old_userid as $k) {
                                                if ($v->UserID == $k) {
                                                    $UserChecked = "checked";
                                                    break;
                                                }
                                            }
                                            ?>
                                            <li class="pl-2 pr-1 dropdown-item li-{{ $v->UserID }}">
                                                <label class="m-0">
                                                    <input type="checkbox" class="usercheck" {{ @$UserChecked }} name="UserID[0][]" value="{{ $v->UserID }}"> <span>{{ $v->UserNM }}</span>
                                                </label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 txtTravelTime" name="TravelTime[0]" value="00:00{{old('TravelTime')}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control TravelTime TimeText" id="TravelTime" name="TravelTime[0]" maxlength="4" value="{{@$DefaultDate}}{{old('TravelTime') }}" autocomplete="off">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm SchedTime">
                                        <!-- <input type="text" class="form-control datetimepicker3 txtWorkTime" name="WorkTime[0]" value="00:00{{old('WorkTime') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                                        <input type="text" class="form-control txtWorkTime TimeText" id="WorkTime" name="WorkTime[0]" maxlength="4" value="{{@$DefaultDate}}{{old('txtWorkTime') }}" autocomplete="off">
                                    </div>
                                </td>
                                <td><input type="checkbox" name="flgOutputWorkDate[0]" class="form-control flgOutputWorkDate"  value="1"></td>
                                <td><i class="fa fa-trash scheduleDelete" aria-hidden="true"></i></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <button type="button" name="btn" value="save" class="materialselected btn btn-primary" id="scheduleAdd">行追加</button>
                </td>
            </tr>
            <tr>
                <td valign="top" class="lbl">施工内容</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <input type="text" class="form-control" name="LeakagePoint" maxlength="255" value="{{old('LeakagePoint',@$WaterWork->LeakagePoint) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">伝達事項</td>
                <td>
                    <?php
                    if ($WaterWork) {
                        if ($WaterWork->CommuConcrete == 1) {
                            $CommuConcreteChecked = "checked";
                        } else {
                            $CommuConcreteChecked = "";
                        }
                        if ($WaterWork->CommuWaitMaterial == 1) {
                            $CommuWaitMaterialChecked = "checked";
                        } else {
                            $CommuWaitMaterialChecked = "";
                        }
                        if ($WaterWork->CommuConst == 1) {
                            $CommuConstChecked = "checked";
                        } else {
                            $CommuConstChecked = "";
                        }
                        if ($WaterWork->CommuOther == 1) {
                            $CommuOtherChecked = "checked";
                        } else {
                            $CommuOtherChecked = "";
                        }
                    }
                    ?>
                    <!-- <input type="checkbox" {{ @$checked }} name="flgWLeakage" value="{{old('flgWLeakage',@$WaterWork->flgWLeakage) }}"> -->

                    <label><input type="checkbox" {{ @$CommuConcreteChecked }} name="CommuConcrete" value="1" {{old('CommuConcrete',@$WaterWork->CommuConcrete) }}">コンクリート復旧</label>
                    <label><input type="checkbox" {{ @$CommuWaitMaterialChecked }} name="CommuWaitMaterial" value="1" {{old('CommuWaitMaterial',@$WaterWork->CommuWaitMaterial) }}">材料待ち</label>
                    <label><input type="checkbox" {{ @$CommuConstChecked }} name="CommuConst" value="1" {{old('CommuConst',@$WaterWork->CommuConst) }}">やり替え工事</label>
                    <label><input type="checkbox" {{ @$CommuOtherChecked }} name="CommuOther" value="1" {{old('CommuOther',@$WaterWork->CommuOther) }}">その他</label>
                </td>
            </tr>
            <tr>
                <td valign="top" class="lbl">処理状況</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <textarea class="form-control input-lg" id="usage1input1" name="ProcessingStatus" value="">{{old('ProcessingStatus',@$WaterWork->ProcessingStatus) }}</textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="table" id="useMaterial">
                        使用資材
                        <thead>
                            <tr>
                                <th>資材ID</th>
                                <th>品名</th>
                                <th>規格</th>
                                <th>使用数</th>
                                <th>ロス数</th>
                                <th>出し値</th>
                                <!-- <th>削除</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- デフォルト -->
                            @if($ListUseMaterials)
                            <?php
                            $count = 0;
                            ?>
                            @foreach($ListUseMaterials as $kListUseMaterial=>$ListUseMaterial)
                            <tr class="tr-{{$count=$count+1}} UseMaterial">
                                <td>{{@$ListUseMaterial-> MaterialID}}</td>
                                <td>{{@$ListUseMaterial-> MaterialNM}}</td>
                                <td>{{@$ListUseMaterial->Type }}</td>
                                <td> {{@$ListUseMaterial->UseNum }}</td>
                                <td>{{@$ListUseMaterial->LossNum }}</td>
                                <td>{{@$ListUseMaterial->SellPrice}}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button type="button" {{ $FlgPaymentConfirm ? 'disabled' : ''}} name="btn" value="selectMaterial" class="materialselected btn btn-primary selectMaterial" id="useMaterialAdd">資材選択</button>
                </td>
            </tr>
            <tr>
                <td class="lbl">引渡許可者</td>
                <td>
                    <div class="input-group select">
                        <select class="form-control form-control-sm" name="DeliveryUserID">
                            <option value=''></option>
                            @foreach($ListUser as $k=>$v)
                            <option {{ old("DeliveryUserID") == $v->UserID ? 'selected' : '' }} {{ @$WaterWork->DeliveryUserID == $v->UserID ? 'selected' : '' }} value="{{ @$v->UserID }}">{{ $v->UserNM }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="testlbl">検査結果<br>検査計画</td>
                <?php
                if ($WaterWork) {
                    if ($WaterWork->flgInspectionIesults == 1) {
                        $passChecked = "checked";
                        $failureChecked = "";
                    }
                    if ($WaterWork->flgInspectionIesults == 0) {
                        $failureChecked = "checked";
                        $passChecked = "";
                    }
                    if ($WaterWork->flgInspectionIesults == "") {
                        $failureChecked = "";
                        $passChecked = "";
                    }
                }
                ?>
                <td>
                    <label><input type="radio" {{ @$passChecked }} name="flgInspectionIesults" value="1" {{ old('flgInspectionIesults', @$WaterWork->flgInspectionIesults)}}">合格</label>
                    <label><input type="radio" {{ @$failureChecked }} name="flgInspectionIesults" value="0" {{ old('flgInspectionIesults', @$WaterWork->flgInspectionIesults)}}">不合格</label>
                </td>

            </tr>
            <tr>
                <td class="lbl">水道</td>
                <td>
                    <table class="waterchecktable">
                        <tr>
                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->flgWLeakage == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="flgWLeakage" value="1" {{old('flgWLeakage',@$WaterWork->flgWLeakage) }}">修繕箇所の漏水確認</label>
                            </td>
                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->flgWPilot == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="flgWPilot" value="1" {{old('flgWPilot',@$WaterWork->flgWPilot) }}">パイロットの確認</label>
                            </td>
                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->FlgWCustomerExplan == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="FlgWCustomerExplan" value="1" {{old('FlgWCustomerExplan',@$WaterWork->FlgWCustomerExplan) }}">お客様への説明</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->flgWFlood == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="flgWFlood" value="1" {{old('flgWFlood',@$WaterWork->flgWFlood) }}">蛇口の出水状況</label>
                            </td>
                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->flgWClean == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="flgWClean" value="1" {{old('flgWClean',@$WaterWork->flgWClean) }}">清掃・後片付け</label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- </table> -->
            </td>
            </tr>
            <tr>
                <td class="lbl">排水</td>
                <td>
                    <table class="drainagechecktable">
                        <tr>
                            <td class="tablelbl">
                                <!-- style="width: 155px;" -->
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->FlgDRepair == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="FlgDRepair" value="1" {{old('FlgDRepair',@$WaterWork->FlgDRepair) }}">修繕箇所の確認</label>
                            </td>

                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->FlgDDrainage == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="FlgDDrainage" value="1" {{old('FlgDDrainage',@$WaterWork->FlgDDrainage) }}">排水状況</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="tablelbl">
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->FlgDCustomerExplan == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="FlgDCustomerExplan" value="1" {{old('FlgDCustomerExplan',@$WaterWork->FlgDCustomerExplan) }}">お客様への説明</label>
                            </td>
                            <td>
                                <?php
                                if ($WaterWork) {
                                    if ($WaterWork->FlgDClean == 1) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <label><input type="checkbox" {{ @$checked }} name="FlgDClean" value="1" {{old('FlgDClean',@$WaterWork->FlgDClean) }}">清掃・後片付け</label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="lbl">指針</td>
                <td>
                    <!-- <input type="number" class="form-control text input number Guidelines" maxlength="7" value="{{old('Guidelines',@$WaterWork->Guidelines) }}" min="0" step="0.1" name="Guidelines" value="{{old('Guidelines',@$WaterWork->Guidelines) }} aria-label=" Sizing example input" aria-describedby="inputGroup-sizing-sm"> -->
                    <input type="text" class="form-control Guidelines" name="Guidelines" maxlength="8" value="{{old('Guidelines',@$WaterWork->Guidelines) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    <h4>㎥</h4>
                </td>
            </tr>
        </table>
    </div>
</div>