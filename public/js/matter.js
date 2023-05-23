$(document).ready(function () {

    //Get the button
    var mybutton = document.getElementById("TopBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    $(".mg-scroll").on("scroll", function () {
        scrollFunction()
    });

    function scrollFunction() {
        if ($("#reception").offset().top < 100) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }
    $("#TopBtn").on("click", function () {
        topFunction();
    })
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        $(".mg-scroll").animate({
            scrollTop: 0
        }, "normal");
        return false;
    }
    setScrollHeight();
    $(window).resize(function () {
        setScrollHeight();
    });

    function setScrollHeight() {
        $(".mg-scroll").height($(window).outerHeight() - $("header").outerHeight() - $(".header-content").outerHeight() - 25)
    }
    // 氏名カナ	
    AutoKana.bind("#ReqName", "#ReqNameKana", {
        katakana: true
    });
    //入力されたテキストの先頭末尾スペース削除
    $(document).on("change", ".form-control", function () {
        // alert($(this).val());
        text = $(this).val();
        $(this).val(text.trim())
    })
    $(document).on("change", ".form-control.input-lg", function () {
        text = $(this).val();
        $(this).val(text.trim())
    })
    //コロン編集を追加,時刻フォーマットチェック
    $(document).on("blur", ".TimeText", function () {
        result = $(this).val().indexOf(':')
        if (($(this).val()).trim().length >= 0) {
            // <4_
            // >3
            if (($(this).val()).trim().length == 0) {
                $(this).val("00:00");
            }
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
                    $(this).val("00:00");
                } else {
                    $(this).val(h + ":" + m);
                }
            }

        }
    })

    //コロン編集を削除
    $(document).on("focus", ".TimeText", function () {
        var reg = new RegExp(":", "g");
        var chgVal = $(this).val().replace(reg, "");
        if (!isNaN(chgVal)) {
            $(this).val(chgVal); //値セット
            $(this).select(); //全選択
        }
    })

    //スケジュール：作業日有効範囲チェック
    $(".txtSchedTime").on("change", function () {
        console.info($(this).val())
        txtSchedFrom = $(this).val();
        if (txtSchedFrom <= "1753/01/01" || txtSchedFrom >= "9999/12/31") {
            $(this).val(new Date());
            $("#MessageModal .modal-body").html("作業日の入力は1753/01/01～9999/12/31までの入力です");
            $("#MessageModal").modal();
        }
    })

    //番地のチェック(大文字の数字だった場合半角に変換する)
    $(".Chome").on("input", function (e) {
        $(this).val(checkNumber($(this).val()));
        // if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
        //     $("#MessageModal .modal-body").html("数値のみでの入力です。");
        //     $("#MessageModal").modal();
        //     $(this).val("")
        // }

        let value = $(e.currentTarget).val();
        value = value
            .replace(/[０-９]/g, function (s) {
                return String.fromCharCode(s.charCodeAt(0) - 65248);
            })
            .replace(/[^0-9]/g, '');
        $(e.currentTarget).val(value);
    })
    //番地のチェック(大文字の数字だった場合半角に変換する)
    $(".WWReceptNo").on("input", function (e) {
        $(this).val(checkNumber($(this).val()));
        // if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
        //     $("#MessageModal .modal-body").html("数値のみでの入力です。");
        //     $("#MessageModal").modal();
        //     $(this).val("")
        // }

        let value = $(e.currentTarget).val();
        value = value
            .replace(/[０-９]/g, function (s) {
                return String.fromCharCode(s.charCodeAt(0) - 65248);
            })
            .replace(/[^0-9]/g, '');
        $(e.currentTarget).val(value);
    })

    // $(".Address").on("change", function() {
    //     $(this).val(checkNumber($(this).val()));
    //     if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
    //         $("#MessageModal .modal-body").html("数値のみでの入力です。");
    //         $("#MessageModal").modal();
    //         $(this).val("")
    //     }
    // })
    $(".Number").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }
    })

    //電話番号のチェック(大文字の数字だった場合半角に変換する)
    $("[name=ReqTEL]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }

    });
    //連絡先TELのチェック(大文字の数字だった場合半角に変換する)
    $("[name=ReqContactTEL]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }

    });
    //お客様番号のチェック(大文字の数字だった場合半角に変換する)
    $("[name=ReqWaterNo]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }

    });
    //栓番のチェック(大文字の数字だった場合半角に変換する)
    $("[name=PlugNum]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }

    });
    //口径のチェック(大文字の数字だった場合半角に変換する処理)
    $("[name=PipeSize]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }

    });
    //工事先：電話番号のチェック(大文字の数字だった場合半角に変換する)
    $("[name=ConstrTEL]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }
    });
    //請求：電話番号のチェック(大文字の数字だった場合半角に変換する)
    $("[name=ClaimTEL]").on("change", function () {
        $(this).val(checkNumber($(this).val()));
        if (!$(this).val().match(/^\d+$/) && !$(this).val() == "") {
            $("#MessageModal .modal-body").html("数値のみでの入力です。");
            $("#MessageModal").modal();
            $(this).val("")
        }
    });

    //請求明細：技術料最大値チェック
    $("[name=TechFee]").on("change", function () {
        // $(this).val(checkNumber($(this).val()));
        if ($(this).val() >= 2147483647) {
            $("#MessageModal .modal-body").html("技術量の入力最大値は「2,147,483,647」までです。");
            $("#MessageModal").modal();
            $(this).val("2147483647")
        }
    });

    //請求明細：調査費最大値チェック
    $("[name=SurveyFee]").on("change", function () {
        // $(this).val(checkNumber($(this).val()));
        if ($(this).val() >= 2147483647) {
            $("#MessageModal .modal-body").html("調査費の入力最大値は「2,147,483,647」までです。");
            $("#MessageModal").modal();
            $(this).val("2147483647")
        }
    });

    //請求明細：出張費最大値チェック
    $("[name=MaterialTravel]").on("change", function () {
        // $(this).val(checkNumber($(this).val()));
        if ($(this).val() >= 2147483647) {
            $("#MessageModal .modal-body").html("出張費の入力最大値は「2,147,483,647」までです。");
            $("#MessageModal").modal();
            $(this).val("2147483647")
        }
    });

    //請求明細：産廃処分費最大値チェック
    $("[name=DisposalFee]").on("change", function () {
        // $(this).val(checkNumber($(this).val()));
        if ($(this).val() >= 2147483647) {
            $("#MessageModal .modal-body").html("産廃処分費の入力最大値は「2,147,483,647」までです。");
            $("#MessageModal").modal();
            $(this).val("2147483647")
        }
    });

    //請求明細：その他最大値チェック
    $("[name=Others]").on("change", function () {
        // $(this).val(checkNumber($(this).val()));
        if ($(this).val() >= 2147483647) {
            $("#MessageModal .modal-body").html("その他の入力最大値は「2,147,483,647」までです。");
            $("#MessageModal").modal();
            $(this).val("2147483647")
        }
    });

    //請求明細：出精値引き最大値チェック
    $("[name=Discount]").on("change", function () {
        // $(this).val(checkNumber($(this).val()));
        if ($(this).val() >= 2147483647) {
            $("#MessageModal .modal-body").html("出精値引きの入力最大値は「2,147,483,647」までです。");
            $("#MessageModal").modal();
            $(this).val("2147483647")
        }
    });

    //全角の数値を半角に変換
    function checkNumber(num) {
        var hen = num.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function (s) {
            return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
        });
        return hen
    }
    //     return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    // });
    // $(this).val(hen);
    // })

    //指針の小数点以下切り捨て処理
    $(".Guidelines").on("change", function () {
        Guidelines = $(this).val();
        if (!isNaN(Guidelines)) {
            Guidelines = Guidelines * 10;
            Guidelines = Math.round(Guidelines);
            Guidelines = Guidelines / 10;
            point = String(Guidelines).split(".")[1];
            if (point) {
                $(this).val(Guidelines);
            } else {
                $(this).val(Guidelines + "." + 0);
                if ($(this).val() == 0.0) {
                    $(this).val("");
                };
            }
            if (Guidelines > 0) ans = Math.floor(Guidelines);
            else ans = Math.ceil(Guidelines);
            if (String(ans).length > 6) {
                $("#MessageModal .modal-body").html("指針の入力部は整数部6ケタ、少数部1ケタでの入力です。");
                $("#MessageModal").modal();
                Guidelines = String(Guidelines).substr(0, 6);
                console.info(Guidelines)
                $(this).val(Guidelines + "." + 0);
            }
            // }

        } else {
            $(this).val("");
            $("#MessageModal .modal-body").html("指針の入力は半角数値のみ可能です。");
            $("#MessageModal").modal();
        }
    })


    //削除ボタン押下時、ドロップダウンを表示
    $(".btnDelete").on("click", function () {
        if ($(this).attr("value")) {
            $("#MessageModal .modal-body").html(getMsgByID("error096"));
            $("#MessageModal").modal();
        } else {
            $("#ConfirmModal .modal-body p").html("削除します。宜しいですか？")
            $("#ConfirmModal .btnOk").attr("id", "btnDelete")
            $("#ConfirmModal").modal()
        }
    })

    //ドロップダウン削除ボタン押下時
    $("body").on("click", "#btnDelete", function () {
        datasend = {
            "WWID": $("input[name='WWID']").val()
        }
        $.ajax({
            type: "post",
            url: "/deleteMater",
            data: datasend,
            success: function (response) {
                // console.info(response)
                if (response["status"] == 1) {
                    window.location.href = "/";
                } else {
                    $("#MessageModal .modal-body").html(response["Msg"]);
                    $("#MessageModal").modal();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("body").html(jqXHR["responseText"]);
            }
        });
    })

    //入金状況
    $('[name=PaymentStatus]').change(function () {
        if ($('[name=PaymentStatus]').val() == 01) {
            $('.PaymentDate').toggle(false);
            $('[name=PaymentDate]').val("")
        } else if ($('[name=PaymentStatus]').val() == 02) {
            $('.PaymentDate').toggle(true);
            $('[name=PaymentDate]').val(formatDate(new Date()))
        } else {
            $('.PaymentDate').toggle(true);
            $('[name=PaymentDate]').val("")
        }
    })



    // デフォルト
    $('.td-3point').each(function () {
        updateTextView($(this));
    });

    // 入力時
    $('.td-okane').on('change', function () {
        updateTextView($(this));
    });

    //請求計算
    $.urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results ? results[1] : 0;
    }
    if ($.urlParam('flgDis') == 1) {
        updateMonney(true);
    } else {
        updateMonney(false);
    }
    $(".td-okane").on("change", function () {
        updateMonney(true);
    })
    $(".td-okane-dis").on("change", function () {
        updateTextView($(this));
        updateMonney(false);
    })
    function updateMonney(discountchange) {
        TechFee = getInt($("#TechFee").val())
        MaterialFee = getInt($("#MaterialFee").val())
        MaterialTravel = getInt($("#MaterialTravel").val())
        SurveyFee = getInt($("#SurveyFee").val())
        DisposalFee = getInt($("#DisposalFee").val())
        tax = $("#taxdate").val()
        Discount = getInt($("#Discount").val());
        subtotal = (TechFee + MaterialFee + MaterialTravel + SurveyFee + DisposalFee) - Discount;
        if (discountchange) {
            dis100en = (subtotal + Discount) % 100;
            if (dis100en) {
                Discount2 = Discount - (Discount % 100) + dis100en;
                subtotal = subtotal - Discount2 + Discount
                $("#Discount").val(Discount2.toLocaleString());
            }
        }
        $("#subtotal").val(subtotal);
        taxvalue = parseInt(subtotal * tax);
        $("#tax").val((taxvalue.toLocaleString()));
        $("#total").val(parseInt(subtotal + taxvalue));
        $('.totalText').each(function () {
            updateTextView($(this));
        });
    }

    function getInt(value) {
        return parseInt(value.replace(/,/g, ''), 10);
    }
    //日付フォーマット
    $('.datetimepicker1').datetimepicker({
        format: 'YYYY/MM/DD HH:mm',
        locale: 'ja',
        defaultDate: new Date()

    });
    datetimepicker2()
    datetimepicker3()
    // dropdownMenu1()
    scheduleDelete()

    //年日付
    function datetimepicker2() {
        $('.datetimepicker2').datetimepicker({
            format: 'YYYY/MM/DD',
            locale: 'ja',
        });
    }

    //時刻
    function datetimepicker3() {
        $('.datetimepicker3').datetimepicker({
            format: 'HH:mm',
            locale: 'ja',
        });
    }

    // $("#deleteBtn").on('click', function() {
    //     $('#exampleModal')
    // });

    //担当者チェックボックス初期値
    $(".checkbox-menu").on("change", "input[type='checkbox']", function () {
        $(this).closest("li").toggleClass("active", this.checked);
    });

    $(document).on('click', '.allow-focus', function (e) {
        e.stopPropagation();
    });

    //スケジュール行追加ボタン押下時
    btnclick = 0;
    $('#scheduleAdd').click(function (event) {
        btnclick++
        content = "<tr class='tr-" + btnclick + " schedule'>" + $(".btn-add-content").html() + "</tr>"
        $("#schedule tbody").append(content);
        $("#schedule tbody .tr-" + btnclick + " .usercheck").attr("name", "UserID[" + (btnclick) + "][]")
        $("#schedule tbody .tr-" + btnclick + " .txtSchedTime").attr("name", "SchedTime[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtSchedFrom").attr("name", "SchedFrom[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtSchedTo").attr("name", "SchedTo[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtWorkStatus").attr("name", "WorkStatus[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtWorkType").attr("name", "WorkType[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtTargetType").attr("name", "TargetType[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtWorkPlace").attr("name", "WorkPlace[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtTravelTime").attr("name", "TravelTime[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtWorkTime").attr("name", "WorkTime[" + (btnclick) + "]")
        $("#schedule tbody .tr-" + btnclick + " .txtflgOutputWorkDate").attr("name", "flgOutputWorkDate[" + (btnclick) + "]")
        // $('.tr- ' + btnclick + '.usercheck').attr('checked','')

        datetimepicker2()
        datetimepicker3()
        scheduleDelete()
    });

    //スケジュール削除処理
    function scheduleDelete() {
        $(".scheduleDelete").on("click", function () {
            if ($("#schedule tbody tr").length === 1) {
                return;
            }
            $(this).closest("tr").remove();
        });
    }

    //使用資材削除処理
    $(".useMaterialDelete").on("click", function () {
        $(this).closest("tr").remove();
    });
    //使用資材追加ボタン
    // $("#useMaterialAdd").on("click", function() {
    //     $(this).closest("tr").remove();
    // });
    $('#photoAdd').click(function (event) {
        $("#photoTable tbody tr:last-child").clone(false).appendTo("#photoTable");
        // $("#schedule tbody tr:last-child").val("");
        // $("#photo tbody tr:last-child").css("display", "table-row");
    });
    $(".photoDelete").on("click", function () {
        $(this).closest("tr").remove();
    });

    //担当者チェックボックス選択時
    $("body").on("change", '.usercheck', function () {
        id = $(this).val();
        tr = "tr." + $(this).closest("tr").attr("class")
        tr = tr.replace(" schedule", "")
        if (this.checked) {
            tr = tr.replace(" schedule", "")
            name = $(tr + " .li-" + id + " span").html()
            oldhtml = $(tr + ' .user-drop .dropdown-toggle .un').html()
            if (!$.trim(oldhtml)) {
                html = "<span class='drop drop-" + id + "' userid='" + id + "'  >" + name + "</span>"
            } else {
                html = "<span class='drop drop-" + id + "'  userid='" + id + "' >," + name + "</span>"
            }
            $(tr + ' .user-drop .dropdown-toggle .un').append(html)
        } else {
            userid = $(tr + " .un .drop").first().attr("userid")
            if (id == userid) {
                $(tr + " .drop-" + id).remove()

                htmlall = $(tr + " .un").html()
                if ($.trim(htmlall)) {
                    html = $(tr + " .un .drop").first().html()
                    html = html.replace(",", "")
                    html = $(tr + " .un .drop").first().html(html)
                }
            } else
                $(tr + " .drop-" + id).remove()
        }
    })


    //工事先：依頼者と同じチェックボックス押下時
    $("#ConstructionCopyCheck").change(function () {
        if (jQuery(this).prop('checked')) {
            $('input:text[name="ConstrAdress"]').val($('input:text[name="ReqAdress"]').val());
            $('input:text[name="ConstrAdress"]').prop("disabled", true);
            $('input:text[name="ConstrName"]').val($('input:text[name="ReqName"]').val());
            $('input:text[name="ConstrName"]').prop("disabled", true);
            $('input:text[name="ConstrTEL"]').val($('input:text[name="ReqTEL"]').val());
            $('input:text[name="ConstrTEL"]').prop("disabled", true);
            $('input:text[name="ConstrBuilding"]').val($('input:text[name="ReqBuilding"]').val());
            $('input:text[name="ConstrBuilding"]').prop("disabled", true);
        } else {
            $('input:text[name="ConstrAdress"]').val('');
            $('input:text[name="ConstrAdress"]').prop("disabled", false);
            $('input:text[name="ConstrName"]').val('');
            $('input:text[name="ConstrName"]').prop("disabled", false);
            $('input:text[name="ConstrTEL"]').val('');
            $('input:text[name="ConstrTEL"]').prop("disabled", false);
            $('input:text[name="ConstrBuilding"]').val('');
            $('input:text[name="ConstrBuilding"]').prop("disabled", false);
        }
    });

    //請求：依頼者と同じチェックボックス押下時
    $("#claimCopyCheck").click(function () {
        if (jQuery(this).prop('checked')) {
            $('input:text[name="ClaimAdress"]').val($('input:text[name="ReqAdress"]').val());
            $('input:text[name="ClaimAdress"]').prop("disabled", true);
            $('input:text[name="ClaimName"]').val($('input:text[name="ReqName"]').val());
            $('input:text[name="ClaimName"]').prop("disabled", true);
            $('input:text[name="ClaimTEL"]').val($('input:text[name="ReqTEL"]').val());
            $('input:text[name="ClaimTEL"]').prop("disabled", true);
            $('input:text[name="ClaimBuilding"]').val($('input:text[name="ReqBuilding"]').val());
            $('input:text[name="ClaimBuilding"]').prop("disabled", true);
        } else {
            $('input:text[name="ClaimAdress"]').val('');
            $('input:text[name="ClaimAdress"]').prop("disabled", false);
            $('input:text[name="ClaimName"]').val('');
            $('input:text[name="ClaimName"]').prop("disabled", false);
            $('input:text[name="ClaimTEL"]').val('');
            $('input:text[name="ClaimTEL"]').prop("disabled", false);
            $('input:text[name="ClaimBuilding"]').val('');
            $('input:text[name="ClaimBuilding"]').prop("disabled", false);
        }
    });


    $('.menu a').click(function () {
        id = $(this).attr("id");
        id = id.replace("menu", "");
        $(".mg-scroll").scrollTo('#' + id);
    });
    jQuery.fn.scrollTo = function (elem, speed) {
        $(this).animate({
            scrollTop: $(this).scrollTop() - $(this).offset().top + $(elem).offset().top
        }, speed == undefined ? 500 : speed);
        return this;
    };

    function updateTextView(_obj) {
        var num = getNumber(_obj.val());
        if (num == 0) {
            _obj.val(0);
        } else {
            _obj.val(num.toLocaleString());
        }
    }

    function getNumber(_str) {
        var arr = _str.split('');
        var out = new Array();
        for (var cnt = 0; cnt < arr.length; cnt++) {
            if (isNaN(arr[cnt]) == false) {
                out.push(arr[cnt]);
            }
        }
        return Number(out.join(''));
    }

});

//リアルタイム日付取得
function formatDate(dt) {
    var y = dt.getFullYear();
    var m = ('00' + (dt.getMonth() + 1)).slice(-2);
    var d = ('00' + dt.getDate()).slice(-2);
    return (y + '/' + m + '/' + d);
}


$(document).ready(function () {
    // 「新規追加」ボタンをクリック時ポップアップが表示される
    $("#btnphotoAdd").on("click", function () {
        $("#ImageModal").modal()
    })
    // 写真表示される
    $(document).on("change", '.fileimage', function (e) {
        if (this.files.length) {
            $(".browse").html(this.files[0].name)
            var reader = new FileReader();
            reader.onload = function (e) {
                // get loaded data and render thumbnail.]
                img = '<img src="' + e.target.result + '"  class="showimg img-thumbnail col-sm-6">'
                $("#preview").html(img);
            };
            // read the image file as a data URL.
            reader.readAsDataURL(this.files[0]);
        }
    });

    // 写真追加の決定ボタンをクリック時
    btnok = 1
    $("#ImageModal .btnOk").on("click", function () {
        ErrMsg = valiAddImage()
        if (!ErrMsg) {
            html = '<tr>'
            html += ' <td><img class="showimg" alt="" src="' + $("#preview img").attr("src") + '"></td>'
            html += ' <td>'
            html += '  <input type="hidden" name="ImgNote[' + btnok + ']" value="' + $(".txtSummaryPopup").val() + '">' + $(".txtSummaryPopup").val()
            html += '</td>'
            html += ' <td><i class="fa fa-trash btnphotoDelete" aria-hidden="true" imgid=""></i></td>'
            html += '</tr>'
            $("#photoTable tbody").append(html)
            btnok++
            $(".custom-file input").removeClass("fileimage")
            $(".custom-file input").addClass("d-none")
            $(".custom-file").append('<input type="file" name="img[' + btnok + ']" class="custom-file-input fileimage" accept="image/*">')
            resetFormAddImage()
            $("#ImageModal").modal("hide")
        } else {
            $("#MessageModal .modal-body").html(ErrMsg);
            $("#MessageModal").modal();
        }
    })

    function resetFormAddImage() {
        $("#preview").html("");
        $(".txtSummaryPopup").val("");
        $(".browse").html("写真選択")
    }

    function valiAddImage() {
        ErrMsg = ""
        if (!$(".txtSummaryPopup").val()) {
            ErrMsg += "「概要」を入力してください。<br>"
        }
        if ($(".fileimage")[0].files.length === 0) {
            ErrMsg += "「写真」を選択してください。"
        }
        return ErrMsg
    }

    $('#submitbuttonform input').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    // 写真一覧の削除ボタンをクリック時
    $(document).on("click", ".btnphotoDelete", function () {
        imgid = $(this).attr("imgid")
        $(this).closest("tr").remove();
        if (imgid) {
            datasend = {
                "ImgID": imgid
            }
            $.ajax({
                type: "post",
                url: "/deleteWorkImg",
                data: datasend,
                success: function (response) {
                    if (response["status"] == 1) {
                        showAlert(response["Msg"])
                    } else {
                        $("#MessageModal .modal-body").html(response["Msg"]);
                        $("#MessageModal").modal();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("body").html(jqXHR["responseText"]);
                }
            });
        } else {
            showAlert("削除が完了しました。")
        }
    });


    // 写真一覧の削除ボタンをクリック時
    $(document).on("click", ".showimg", function () {
        $("#ShowImageModal .modal-body").html("<img src='" + $(this).attr("src") + "' alt=''>");
        $("#ShowImageModal").modal();
    });
})

$(document).ready(function () {
    //資材選択ボタン押下時、作業：スケジュールの作業日＋作業区分チェック
    $(".selectMaterial").on("click", function () {
        blankFlg = false;
        errflg = true;
        $(".selectMaterial").attr("type", "button");
        if (($(".schedule").length) == 0) {
            errflg = false;
            $("#MessageModal .modal-body").html("スケジュールが入力されているか確認してください");
            $("#MessageModal").modal();
        } else {
            if (!$("input[name='WWRecID']").val()) {
                errflg = false;
                $("#MessageModal .modal-body").html("データはまだ登録しない状態、<br>「登録」ボタンをクリックしてください");
                $("#MessageModal").modal();
            }
        }
        var txtWorkType = $("#schedule").find("tr.schedule .txtWorkType").val();

        var txtSchedFrom = $("#schedule").find("tr.schedule .txtSchedTime").val();
        var txtTargetType = $("#schedule").find("tr.schedule .txtTargetType").val();
        var txtWorkPlace = $("#schedule").find("tr.schedule .txtWorkPlace").val();
        var usercheck = $("#schedule").find("tr.schedule .un .drop").text();
        usercheck = usercheck.split(',');
        var from = $("#schedule").find("tr.schedule .SchedFrom").val();
        var to = $("#schedule").find("tr.schedule .SchedTo").val();
        var travel = $("#schedule").find("tr.schedule .txtTravelTime").val();
        var Work = $("#schedule").find("tr.schedule .txtWorkTime").val();

        var txtWorkType = $('.txtWorkType').map(function () {
            return this.value;
        }).get();
        var txtSchedFrom = $('.txtSchedTime').map(function () {
            return this.value;
        }).get();
        var txtTargetType = $('.txtTargetType').map(function () {
            return this.value;
        }).get();
        var txtWorkPlace = $('.txtWorkPlace').map(function () {
            return this.value;
        }).get();
        var from = $('.SchedFrom').map(function () {
            return this.value;
        }).get();
        var to = $('.SchedTo').map(function () {
            return this.value;
        }).get();
        var travel = $('.txtTravelTime').map(function () {
            return this.value;
        }).get();
        var Work = $('.txtWorkTime').map(function () {
            return this.value;
        }).get();

        $.each(txtWorkType, function (index, WorkType) {
            if (index != 0) {
                blankFlg = false;
                //入力されていない行だった場合次の行のエラーチェックに飛ばす
                if (!txtSchedFrom[index] && !txtWorkType[index] && !txtTargetType[index] && !usercheck[0] && !txtWorkPlace[index]) {
                    blankFlg = true;
                    return true;
                }

                WorkTypeFreeText = txtWorkType[index].split('&');

                frtxt = moment(from[index], "HH:mm");
                totxt = moment(to[index], "HH:mm");

                traveltxt = moment(travel[index], "HH:mm");
                // Worktxt = moment(Work[index], "HH:mm");
                Worktxt = moment.duration(Work[index]).asSeconds() * 1000;
                traveltxt = moment.duration(travel[index]).asSeconds() * 1000;

                BetweenTime = totxt.diff(frtxt)
                if (txtSchedFrom[index]) {
                    if (!txtWorkType[index] || !txtTargetType[index] || !usercheck[0] || !txtWorkPlace[index]) {
                        errflg = false;
                        $("#MessageModal .modal-body").html("スケジュールを入力する際は全ての入力項目欄に記入してください。");
                        $("#MessageModal").modal();
                    } else {
                        //作業日の有効日確認
                        if (txtSchedFrom[index] <= "1753/01/01" || txtSchedFrom[index] >= "9999/12/31") {
                            errflg = false;
                            $("#MessageModal .modal-body").html("作業日の入力は1753/01/01～9999/12/31までの入力です");
                            $("#MessageModal").modal();
                        }
                        if (frtxt >= totxt) {
                            errflg = false;
                            $("#MessageModal .modal-body").html(getMsgByID("error047"));
                            $("#MessageModal").modal();
                        }
                    }
                }
            }
        })
        if (blankFlg) {
            errflg = false;
            $("#MessageModal .modal-body").html("スケジュールが入力されているか確認してください");
            $("#MessageModal").modal();
        }

        if (errflg) {
            $(".selectMaterial").attr("type", "submit");
        } else {
            $(".selectMaterial").attr("type", "button");
        }



    })

    //登録＆更新ボタン押下時。各エラーチェック箇所をチェック
    $(".save").on("click", function () {
        flag = true;
        errflg = false;
        // // data check

        var WWAdress = $('[name="WWAdress"]').val()
        var Chome = $('[name="Chome"]').val()
        var Address = $('[name="Address"]').val()
        var Number = $('[name="Number"]').val()

        var WWDateTime = $('[name="WWDateTime"]').val()
        var ClaimDate = $('[name="ClaimDate"]').val()
        var PaymentDate = $('[name="PaymentDate"]').val()

        if (WWDateTime) {
            if (WWDateTime <= "1753/01/01 00:00" || WWDateTime >= "9999/12/31 23:59") {
                flag = false;
                $("#MessageModal .modal-body").html("受付日の入力は1753/01/01 00:00 ～ 9999/12/31 23:59までの入力です");
                $("#MessageModal").modal();
            }
        }

        if (ClaimDate) {
            if (ClaimDate <= "1753/01/01" || ClaimDate >= "9999/12/31") {
                flag = false;
                $("#MessageModal .modal-body").html("請求日の入力は1753/01/01～9999/12/31までの入力です");
                $("#MessageModal").modal();
            }
        }

        if (PaymentDate) {
            if (PaymentDate <= "1753/01/01" || PaymentDate >= "9999/12/31") {
                flag = false;
                $("#MessageModal .modal-body").html("入金日の入力は1753/01/01～9999/12/31までの入力です");
                $("#MessageModal").modal();
            }
        }
        ReqNameKana = $("#ReqNameKana").val();
        if (ReqNameKana) {
            if (ReqNameKana.match(/^[ァ-ヶー　]+$/)) { //"ー"の後ろの文字は全角スペースです。
            } else {
                $("#MessageModal .modal-body").html("氏名カナは、全角及びカタカナで入力してください");
                $("#MessageModal").modal();
                flag = false;
            }
        }
        var txtSchedTime = $("#schedule").find("tr.schedule .txtSchedTime").val();
        var txtWorkType = $("#schedule").find("tr.schedule .txtWorkType").val();
        var txtTargetType = $("#schedule").find("tr.schedule .txtTargetType").val();
        var txtWorkPlace = $("#schedule").find("tr.schedule .txtWorkPlace").val();

        var usercheck = $("#schedule").find("tr.schedule .un .drop").text();
        usercheck = usercheck.split(',');
        var from = $("#schedule").find("tr.schedule .SchedFrom").val();
        // console.info(from)
        var to = $("#schedule").find("tr.schedule .SchedTo").val();
        var travel = $("#schedule").find("tr.schedule .txtTravelTime").val();
        var Work = $("#schedule").find("tr.schedule .txtWorkTime").val();

        var SchedTime = $('.txtSchedTime').map(function () {
            return this.value;
        }).get();
        var from = $('.SchedFrom').map(function () {
            return this.value;
        }).get();
        var to = $('.SchedTo').map(function () {
            return this.value;
        }).get();
        var travel = $('.txtTravelTime').map(function () {
            return this.value;
        }).get();
        var Work = $('.txtWorkTime').map(function () {
            return this.value;
        }).get();
        var WorkType = $('.txtWorkType').map(function () {
            return this.value;
        }).get();
        var WorkPlace = $('.txtWorkPlace').map(function () {
            return this.value;
        }).get();
        var TargetType = $('.txtTargetType').map(function () {
            return this.value;
        }).get();
        // console.log(from)

        //必須項目のチェック
        if (!$('input:text[name="WWName"]').val()) {
            flag = false;
            $("#MessageModal .modal-body").html('必須項目を入力してください。');
            $("#MessageModal").modal();
        }
        if (!$('[name="WWType"]').val()) {
            flag = false;
            $("#MessageModal .modal-body").html('必須項目を入力してください。');
            $("#MessageModal").modal();
        }

        //数値チェック
        if ($('input:text[name="ReqTEL"]').val()) {
            if (!$('input:text[name="ReqTEL"]').val().match(/^\d+$/)) {
                flag = false;
                errflg = "error072";
            }
        }
        if ($('input:text[name="ConstrTEL"]').val()) {
            if (!$('input:text[name="ConstrTEL"]').val().match(/^\d+$/)) {
                flag = false;
                errflg = "error073";
            }
        }
        if ($('input:text[name="ClaimTEL"]').val()) {
            if (!$('input:text[name="ClaimTEL"]').val().match(/^\d+$/)) {
                flag = false;
                errflg = "error074";
            }
        }
        if ($('input:text[name="Chome"]').val()) {
            if (!$('input:text[name="Chome"]').val().match(/^\d+$/)) {
                flag = false;
                errflg = "error077";
            }
        }
        if ($('input:text[name="Address"]').val()) {
            if (!$('input:text[name="Address"]').val().match(/^\d+$/)) {
                flag = false;
                errflg = "error077";
            }
        }
        if ($('input:text[name="Number"]').val()) {
            if (!$('input:text[name="Number"]').val().match(/^\d+$/)) {
                flag = false;
                errflg = "error077";
            }
        }
        //小数点のチェック
        if ($('input:text[name="Guidelines"]').val()) {
            var Guidelines = getDecimalPointLength($('input:text[name="Guidelines"]').val());
            if (Guidelines.length > 1) {

                flag = false;
                errflg = "error078";
            }
        }

        function getDecimalPointLength(number) {
            var numbers = String(number).split('.');
            return (numbers[1]) ? numbers[1].length : 0;
        };


        $.each(from, function (index, SchedFrom) {
            if (index != 0) {

                frtxt = moment(SchedFrom, "HH:mm");
                totxt = moment(to[index], "HH:mm");

                traveltxt = moment(travel[index], "HH:mm");
                Worktxt = moment.duration(Work[index]).asSeconds() * 1000;
                traveltxt = moment.duration(travel[index]).asSeconds() * 1000;
                BetweenTime = totxt.diff(frtxt)

                //開始時刻が終了時刻よりもおおきく無いか
                if (SchedTime[index] && WorkType[index] && TargetType[index] && usercheck[0] && WorkPlace[index]) {
                    if (frtxt >= totxt) {
                        flag = false;
                        errflg = "error047";
                    }
                }
            }
        })
        if (flag) {
            $(".save").attr("type", "submit");
            $(".loading").removeClass("d-none");
        } else {
            if (errflg) {
                $("#MessageModal .modal-body").html(getMsgByID(errflg));
                $("#MessageModal").modal();
            }
        }
    })
})

$(document).ready(function () {
    // 入金確認ボタンをクリック時
    $(".btnPayment").on("click", function () {
        if ($("select[name='PaymentStatus']").val() == "02" && $("input[name='PaymentDate']").val()) {
            $(this).attr("type", "submit");
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error095"));
            $("#MessageModal").modal();

            $('html, body').animate({
                scrollTop: $("select[name='PaymentStatus']").offset().top
            }, 800);
        }
    });
    $(".btnOkRyoShu").on("click", function () {
        exportFile($(".ExportData option:selected").attr("url") + "?flag=" + $(this).attr("value"));
    });
    $(".btnExport").on("click", function () {
        errMsg = "";
        if (!$('input:text[name="WWName"]').val() || !$('[name="WWType"]').val() || !$("input[name='WWRecID']").val()) {
            errMsg = getMsgByID("error083")
            if (!$("input[name='WWRecID']").val()) {
                errMsg = getMsgByID("error092")
            }
        } else {
            id = $(".ExportData").val();
            if (id) {
                checkflg = true;
                if (id == "seikyu" && $(".WorkStatusCheck").val() == "01") {
                    checkflg = false;
                    errMsg = getMsgByID("error081")
                    if ($("select[name=WorkStatus]").val() == "02") {
                        errMsg = getMsgByID("error084")
                    }
                }
                if (id == "ryoshu") {
                    if ($(".FlgPaymentConfirm").val() != 1) {
                        errMsg = getMsgByID("error094")
                        checkflg = false;
                    } else {
                        //　再出力（出力ユーザID、出力日が入っている場合を行う際は、以下の処理とする。
                        //　一般ユーザ：必ず「（再）」を付与する。
                        //　管理者ユーザ：「（再）」の有無を選択可能とする。
                        if ($(".ManageCls").val() == 1 && $(".checkExportRyoshu").val()) {
                            checkflg = false;
                            // ポップアップが表示される
                            $("#RyoShuConfirmModal").modal();
                        }
                    }
                }
                if (checkflg) {
                    if (id == "ryoshu") {
                        $(".checkExportRyoshu").val(1)
                    }
                    exportFile($(".ExportData option:selected").attr("url"));
                }
            } else {
                errMsg = getMsgByID("error082")
            }
        }
        if (errMsg) {
            $("#MessageModal .modal-body").html(errMsg);
            $("#MessageModal").modal();
        }
    })

    function exportFile(url_download) {
        $.ajax({
            type: "get",
            xhrFields: {
                responseType: 'blob',
            },
            cache: false,
            data: "WWID=" + $("input[name='WWID']").val(),
            url: url_download,
            success: function (result, status, xhr) {
                if (result) {
                    filename = $(".ExportData option:selected").text() + "." + $(".ExportData option:selected").attr("type");
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    typeblob = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    if ($(".ExportData option:selected").attr("type") == "pdf")
                        typeblob = 'application/pdf'
                    // The actual download
                    var blob = new Blob([result], {
                        type: typeblob
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;

                    document.body.appendChild(link);

                    link.click();
                    document.body.removeChild(link);
                    result = null;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    }
});