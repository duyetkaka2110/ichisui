<!DOCTYPE html>
<html>

<head>
    <title>勤怠編集 | 工事管理/資材管理システム</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js" type="text/javascript"></script>
    <link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/js/main.js"></script>
</head>

<body>

    <script type="text/javascript">
        $(function() {
            $('.datetimepicker1').datetimepicker({
                format: 'YYYY/MM/DD',
                locale: 'ja',
            });
            $('#SchedFromTime').text({
                defaultDate: '08:00'
            })
        });
        $(document).on("change", ".form-control", function() {
            text = $(this).val();
            $(this).val(text.trim())
        })

        //スケジュール：作業日有効範囲チェック
        $(document).on("blur", ".SchedTime", function() {
            var now = new Date();
            SchedTime = $(this).val();
            if (SchedTime <= "1753/01/01" || SchedTime >= "9999/12/31") {
                $(this).val(now.getFullYear() + "/" + (now.getMonth() + 1) + "/" + now.getDate());
                $("#MessageModal .modal-body").html("日付の入力は1753/01/01～9999/12/31までの入力です");
                $("#MessageModal").modal();

            } else {
                if ($(this).hasClass("SchedFrom")) {
                    $(".SchedTo").val($(this).val())
                }
            }
        })

        //先頭末尾のスペース削除
        $(document).on("change", ".form-control", function() {
            text = $(this).val();
            $(this).val(text.trim())
        })

        //改行を半角スペースへ置換
        $(document).on("change", ".input-group input", function() {
            text = $(this).val();
            text = text.replace(/\r?\n/g, " ");
            $(this).val(text.trim())
        })

        $(document).ready(function() {
            $('#submitform input').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
            $(document).on("blur", ".TimeText", function() {
                result = $(this).val().indexOf(':')
                if (($(this).val()).trim().length >= 0) {
                    if (($(this).val()).trim().length == 0) {
                        $(this).val("00:00");
                    }

                    // if (($(this).val()).trim().length < 4) {
                    if (($(this).val()).trim().length <= 3) {
                        $(this).val("00:00");
                        $("#MessageModal .modal-body").html("時刻入力は４桁で入力してください");
                        $("#MessageModal").modal();
                    } else {
                        if (result !== -1) {
                            $(this).val("00:00");
                            $("#MessageModal .modal-body").html("時刻入力は半角英数字４桁で入力してください");
                            $("#MessageModal").modal();
                        }
                    }
                    if (($(this).val()).trim().length == 4) {
                        var str = $(this).val().trim();
                        var h = str.substr(0, 2);
                        var m = str.substr(2, 2);
                        if ((h + ":" + m) >= "24:00") {
                            h = 23
                            m = 59
                            $(this).val(h + ":" + m);
                            $("#MessageModal .modal-body").html("時刻の入力は「00:00」～「23:59」までの入力です。");
                            $("#MessageModal").modal();
                        }
                        checkflg = moment(h + ":" + m, "HH:mm").isValid()
                        if (!checkflg) {
                            $("#MessageModal .modal-body").html(getMsgByID("error042"));
                            $("#MessageModal").modal();
                        }
                        $(this).val(h + ":" + m);
                    }

                }
            })

            //コロン編集を削除
            $(document).on("focus", ".TimeText", function() {
                var reg = new RegExp(":", "g");
                var chgVal = $(this).val().replace(reg, "");
                if (!isNaN(chgVal)) {
                    $(this).val(chgVal); //値セット
                    $(this).select(); //全選択
                }
            })

            //保存&更新ボタンクリック時、エラーチェック
            $(".save").on("click", function() {
                flags = checkValidation();
                if (flags) {
                    $(".btn-value").val("save");
                    $("#submitform").submit();
                    $(".loading").removeClass("d-none");
                }
            })
            $(".copy").on("click", function() {
                flags = checkValidation();
                if (flags) {
                    $(".btn-value").val("copy");
                    $("#submitform").submit();
                    $(".loading").removeClass("d-none");
                }
            })
            $(".delete").on("click", function() {
                // flags = checkValidation();
                // if (flags) {
                $(".btn-value").val("del");
                $("#submitform").submit();
                $(".loading").removeClass("d-none");
                // }
            })
        })

        function checkValidation() {
            flag = true;
            // data check
            var UserID = $('input[name="UserID[]"]:checked').map(function() {
                return this.value;
            }).get();
            var SchedFrom = $('input[name="SchedFrom"]').val();
            var SchedFromTime = $('input[name="SchedFromTime"]').val();
            var SchedTo = $('input[name="SchedTo"]').val();
            var SchedToTime = $('input[name="SchedToTime"]').val();

            if (!UserID.length || !SchedFrom || !SchedFromTime || !SchedTo || !SchedToTime) {
                //     if (SchedFrom <= "1753/01/01" || SchedFrom >= "9999/12/31") {
                //         flag = false;
                //         $("#MessageModal .modal-body").html("日付の入力は1753/01/01～9999/12/31までの入力です");
                //         $("#MessageModal").modal();
                //     }
                //     if (SchedTo <= "1753/01/01" || SchedTo >= "9999/12/31") {
                //         flag = false;
                //         $("#MessageModal .modal-body").html("日付の入力は1753/01/01～9999/12/31までの入力です");
                //         $("#MessageModal").modal();
                //     }
                flag = false;
                $("#MessageModal .modal-body").html('必須項目を入力してください');
                $("#MessageModal").modal();
            }
            var FromTime = $('input[name="SchedFromTime"]').val();
            var ToTime = $('input[name="SchedToTime"]').val();
            var SchedFrom = $('input[name="SchedFrom"]').val();
            var SchedTo = $('input[name="SchedTo"]').val();

            SchedFromTime = (SchedFrom + " " + FromTime)
            SchedToTime = (SchedTo + " " + ToTime)

            if (SchedFromTime >= SchedToTime) {
                flag = false;
                $("#MessageModal .modal-body").html('開始時刻は終了時刻を超えられません');
                $("#MessageModal").modal();
            }
            return flag;
        }
    </script>


    <form action="/kintaiinsert" method="post" class="form" id="submitform" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <header>
            勤怠編集
            <button type="button" class="button btn btn-primary" onClick="history.back(); return false;">戻る</button>
            @if($visible)
            <button type="submit" name="btn" value="del" class="button btn btn-primary delete">削除</button>
            <button type="button" name="btn" value="copy" class="button btn btn-primary copy">複写</button>
            <button type="button" name="btn" value="save" class="button btn btn-primary save">更新</button>
            @else
            <button type="button" name="btn" value="save" class="button btn btn-primary save">登録</button>
            @endif
            <input type="hidden" name="btn" value="" class="btn-value" />
        </header>
        <style>
            body {
                width: 550px;
                margin: 0 auto;
                border: 1px solid;
                padding: 10px;
            }

            table {
                width: 100%;
            }

            header {
                margin: 25px 0 15px 0;
            }

            .button {
                width: 75px;
                margin-left: 10px;
                float: right;
            }

            .lbl {
                width: 170px;
            }

            .Mandatory {
                color: red;
                font-size: 13px;
            }

            table tr td {
                padding: 7px;
            }

            table .form-group {
                margin: 0;
            }

            .ScheduleNM {
                width: 250px;
            }

            .user-drop .dropdown-toggle::after {
                position: absolute;
                top: 14px;
                right: 4px;
            }

            .input-group.TimeSelect {
                width: 120px;
            }

            .colon {
                margin-top: 5px;
                margin-bottom: 0;
            }

            .loading {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                opacity: 0.5;
                z-index: 100;
            }

            .loading .spinner-border {
                top: 20%;
                position: fixed;
                left: 50%;
            }
        </style>
        <main>
            <hr>
            <table class="kintai">
                @if($visible)
                <tr>
                    <td class="lbl">No.</td>
                    <td colspan="2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-user-circle" aria-hidden="true"></i></span>
                            </div>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-font"></span></span>
                            <input type="hidden" name="SchedID" value="{{ @$Schedule->SchedID }}{{ @$SchedID }}" class="form-control">
                            <input type="text" name="SchedID" value="{{  @$Schedule->SchedID }}{{ @$SchedID }}" class="form-control" disabled>
                        </div>
                    </td>
                </tr>
                @endif
                <input type="hidden" name="prevroute" value="{{ @$prevroute }}" />
                <tr>
                    <td class="lbl">作業者<span class="Mandatory">(必須)</span></td>
                    <td colspan="2">
                        <div class="form-group">
                            <script>
                                $(".checkbox-menu").on("change", "input[type='checkbox']", function() {
                                    $(this).closest("li").toggleClass("active", this.checked);
                                });

                                $(document).on('click', '.allow-focus', function(e) {
                                    e.stopPropagation();
                                });
                                // ボタン連打防止機能
                                $(document).ready(function() {
                                    btnclick = 0;
                                    $("#submitform button").on("click", function() {
                                        btnclick++;
                                        if (btnclick > 1) {
                                            return false;
                                        }
                                    });
                                    $('.usercheck').on("change", function() {
                                        id = $(this).val();
                                        if (this.checked) {
                                            name = $(".li-" + id + " span").html()
                                            oldhtml = $('.user-drop .dropdown-toggle .un').html()
                                            if (!$.trim(oldhtml)) {
                                                html = "<span class='drop drop-" + id + "' userid='" + id + "'  >" + name + "</span>"
                                            } else {
                                                html = "<span class='drop drop-" + id + "'  userid='" + id + "' >," + name + "</span>"
                                            }
                                            $('.user-drop .dropdown-toggle .un').append(html)
                                        } else {
                                            userid = $(".un .drop").first().attr("userid")
                                            if (id == userid) {
                                                $(".drop-" + id).remove()

                                                htmlall = $(".un").html()
                                                if ($.trim(htmlall)) {
                                                    html = $(".un .drop").first().html()
                                                    html = html.replace(",", "")
                                                    html = $(".un .drop").first().html(html)
                                                }
                                            } else
                                                $(".drop-" + id).remove()
                                        }
                                    })
                                });
                            </script>
                            <style>
                                .user-drop {
                                    padding: 0;
                                    z-index: 11 !important;
                                }

                                .user-drop .dropdown-toggle {
                                    width: 100%;
                                    border: none;
                                    text-align: left;
                                    height: 32px;
                                }

                                .user-drop .dropdown-toggle .caret {
                                    position: absolute;
                                    right: 10px;
                                    top: 15px;
                                }

                                .user-drop .dropdown-toggle .un {

                                    width: 317px;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    white-space: nowrap;
                                }

                                .user-drop .dropdown-menu {
                                    padding: 5px;
                                    width: 100%;
                                    margin: 0;
                                }

                                .user-drop .btn-default.active,
                                .user-drop .btn-default:active,
                                .user-drop .open>.dropdown-toggle.btn-default {
                                    background: none !important;
                                }

                                input time .form-control.TimeSelect {
                                    border-radius: 5px;
                                }

                                .SchedFromHour {
                                    padding: 3px;
                                }

                                .SchedFromMin {
                                    padding: 3px;
                                }
                            </style>
                            <div class="dropdown form-control user-drop">
                                <div class="btn btn-default dropdown-toggle dropdownMenu1" id="dropdownMenu1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <div class="un">
                                        @foreach ($ListScheduleChecked as $key=>$k)
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
                                    $checked = "";
                                    foreach ($ListScheduleChecked as $k) {
                                        if ($v->UserID == $k->UserID) {
                                            $checked = "checked";
                                            break;
                                        }
                                    }
                                    foreach ($old_userid as $k) {
                                        if ($v->UserID == $k) {
                                            $checked = "checked";
                                            break;
                                        }
                                    }
                                    if (!$visible) {
                                        $SchedToTimeDefault = "17:00";
                                        $SchedFromTimeDefault = "08:00";
                                    }
                                    ?>
                                    <li class="li-{{ $v->UserID }}">
                                        <label>
                                            <input type="checkbox" class="usercheck" {{ @$checked }} name="UserID[]" value="{{ $v->UserID }}"> <span>{{ $v->UserNM }}</span>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">開始日時<span class="Mandatory">(必須)</span></td>
                    <td>
                        <div class="input-group ">
                            <input type="text" class="form-control datetimepicker1 SchedTime SchedFrom" name="SchedFrom" value="{{old('SchedFrom',@$Schedule->SchedFrom) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            <div class="input-group-append">
                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="input-group TimeSelect">
                            <input type="text" class="form-control TimeSelectText TimeText" id="SchedFromTime" name="SchedFromTime" maxlength="4" value="{{@$SchedFromTime}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}">
                            <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                            <div class="input-group-append">
                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                            </div>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">終了日時<span class="Mandatory">(必須)</span></td>
                    <td>
                        <div class="input-group ">
                            <input type="text" class="form-control datetimepicker1 SchedTime SchedTo" name="SchedTo" value="{{old('SchedTo',@$Schedule->SchedTo) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            <div class="input-group-append">
                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="input-group TimeSelect">
                            <input type="text" class="form-control TimeSelectText TimeText" id="SchedToTime" name="SchedToTime" maxlength="4" value="{{@$SchedToTime}}{{old('SchedToTime',@$Schedule->SchedToTime) }}">
                            <!-- <input type="time" name="SchedFromTime" min="00:00" max="24:00" value="{{@$SchedFromTimeDefault}}{{old('SchedFrom',@$Schedule->SchedFromTime) }}" class="form-control TimeSelect SchedFromTime datetimepicker2" style="border-radius: 5px;"> -->
                            <div class="input-group-append">
                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                            </div>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">勤怠区分<span class="Mandatory">(必須)</span></td>
                    <td colspan="2">

                        <div class="input-group">
                            <select class="form-control WorkerNM" id="usage1select1" name="SchedType">
                                @foreach($ListEventCls as $k=>$v)
                                <option {{ old("SchedType") == $v->InternalValue ? 'selected' : '' }} {{ @$Schedule->SchedType == $v->InternalValue ? 'selected' : '' }} value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">メモ</td>
                    <td colspan="2">
                        <div class="form-group">
                            <textarea class="form-control input-lg" id="usage1input1" name="SchedNote" value="">{{old('SchedNote', @$Schedule->SchedNote )}}</textarea>
                        </div>
                    </td>
                </tr>
            </table>
    </form>
    </main>

    <!-- loading  -->
    <div class="position-fixed w-100 h-100 loading d-none">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>


    <!-- Modal Error Message -->
    <div class="modal fade" id="MessageModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-header-error bg-danger p-2">
                    <h5 class="modal-title text-white">エラーメッセージ</h5>
                </div>
                <div class="modal-body">
                    @foreach ($errors->all() as $error)
                    <p>{!! $error !!}</p>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-modal" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    @if(($errors->any()))
    <script>
        $(document).ready(function() {
            $("#MessageModal").modal();
        });
    </script>
    @endif
</body>

</html>