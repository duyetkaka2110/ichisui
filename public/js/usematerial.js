//資材マスタ：使用数エラーチェック
$(document).on("change", ".masterUse", function () {

    SelectUse = $(this).closest('tr').find('input[name="Use"]').val();
    if (SelectUse == "") {
        $(this).val(0);
    };
    if (SelectUse > 0) ans = Math.floor(SelectUse);
    else ans = Math.ceil(SelectUse);
    if (String(ans).length > 6) {
        $("#MessageModal .modal-body").html("ロス数の入力部は整数部6ケタ、少数部1ケタでの入力です。");
        $("#MessageModal").modal();
        SelectUse = String(SelectUse).substr(0, 6);
        $(this).val(SelectUse + "." + 0)
    }
})
$(document).ready(function () {
    $(document).on("change", ".inputKeypadWithDot", function () {
        thisval = $(this).val();
        if (!isDecimal(thisval) || !isDecimal(thisval)) {
            ErrMsg = "「使用数」「ロス数」「仕入単価」「仕入れ数」「出値単価」には全角文字を入力できません。";
            $(this).val(0);
            // $("#MessageModal .modal-body").html(ErrMsg);
            // $("#MessageModal").modal();
        }
    })

    $('#usematerialinsert input').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    $(document).on('keyup keypress', '.materialtable input', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    $('input[name="BuyPrice"]').on("keyup", function () {
        if (/\D/g.test(this.value)) {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
    });
    $('input[name="SellPrice"]').on("keyup", function () {
        if (/\D/g.test(this.value)) {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
    });
})
//資材マスタ：ロス数エラーチェック
$(document).on("change", ".masterLoss", function () {
    SelectLoss = $(this).closest('tr').find('input[name="Loss"]').val();
    if (SelectLoss == "") {
        $(this).val(0);
    };
    if (SelectLoss > 0) ans = Math.floor(SelectLoss);
    else ans = Math.ceil(SelectLoss);
    if (String(ans).length > 6) {
        $("#MessageModal .modal-body").html("ロス数の入力部は整数部6ケタ、少数部1ケタでの入力です。");
        $("#MessageModal").modal();
        SelectLoss = String(SelectLoss).substr(0, 6);
        $(this).val(SelectLoss + "." + 0)
    }
})

//特注品：使用数のエラーチェック
$(document).on("change", "input[name='CustomUse']", function () {
    SelectUse = $(this).val();
    if ($(this).val() == "") {
        $(this).val(0);
    };
    if (SelectUse > 0) ans = Math.floor(SelectUse);
    else ans = Math.ceil(SelectUse);
    if (String(ans).length > 6) {
        $("#MessageModal .modal-body").html("使用数の入力部は整数部6ケタ、少数部1ケタでの入力です。");
        $("#MessageModal").modal();
        SelectUse = String(SelectUse).substr(0, 6);
        $('input[name="CustomUse"]').val(SelectUse + "." + 0)
    }
})

//特注品：ロス数のエラーチェック
$(document).on("change", "input[name='CustomLoss']", function () {
    SelectLoss = $(this).val();
    if (SelectLoss == "") {
        $(this).val(0);
    };
    if (SelectLoss > 0) ans = Math.floor(SelectLoss);
    else ans = Math.ceil(SelectLoss);
    if (String(ans).length > 6) {
        $("#MessageModal .modal-body").html("使用数の入力部は整数部6ケタ、少数部1ケタでの入力です。");
        $("#MessageModal").modal();
        SelectLoss = String(SelectLoss).substr(0, 6);
        $('input[name="CustomUse"]').val(SelectLoss + "." + 0)
    }
})

// 特注品：仕入れ単価のエラーチェック
$(document).on("change", "input[name='BuyPrice']", function () {
    BuyPrice = $(this).val();
    if (BuyPrice == "") {
        $(this).val(0);
    };
    if (String(BuyPrice).length > 9) {
        $("#MessageModal .modal-body").html("仕入れ単価の桁数は9ケタまでの入力です。");
        $("#MessageModal").modal();
        BuyPrice = String(BuyPrice).substr(0, 9);
        $('input[name="BuyPrice"]').val(BuyPrice)
    }
})

// 特注品：仕入れ数のエラーチェック
$(document).on("change", "input[name='BuyNumber']", function () {
    BuyNumber = $(this).val();
    if (BuyNumber == "") {
        $(this).val(0);
    };
    if (String(BuyNumber).length > 9) {
        $("#MessageModal .modal-body").html("仕入れ数の桁数は9ケタまでの入力です。");
        $("#MessageModal").modal();
        BuyNumber = String(BuyNumber).substr(0, 9);
        $('input[name="BuyNumber"]').val(BuyNumber)
    }
})

// 特注品：出値単価のエラーチェック
$(document).on("change", "input[name='SellPrice']", function () {
    SellPrice = $(this).val();
    if (SellPrice == "") {
        $(this).val(0);
    };
    if (String(SellPrice).length > 9) {
        $("#MessageModal .modal-body").html("仕入れ数の桁数は9ケタまでの入力です。");
        $("#MessageModal").modal();
        SellPrice = String(SellPrice).substr(0, 9);
        $('input[name="SellPrice"]').val(SellPrice)
    }
})
//選択資材削除処理
$(document).on("click", ".MaterialDelete", function () {
    SelectMaterialID = $(this).closest('tr').find('.selectMaterialID').val();
    SelectMaterialNM = $(this).closest('tr').find('.selectMaterialNM').val();
    $(this).closest("tr").remove();
    // alert(SelectMaterialID)
    if (SelectMaterialNM in selectMaterialNMs) {
        delete selectMaterialNMs[SelectMaterialNM];
        delete selectMaterials[999999];
    }
    delete selectMaterials[SelectMaterialID];

});
// Checks that an input string is a decimal number, with an optional +/- sign   character.
var isDecimal_re = /^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/;

function isDecimal(s) {
    return String(s).search(isDecimal_re) != -1
}
//特注品登録行追加ボタン押下
btnclick = 1
$('#CustomOrderAdd').click(function (event) {
    btnclick++
    content = "<tr class='tr-" + btnclick + " UseMaterial tablebody'>" + $(".btn-add-CusTomContent").html() + "</tr>"
    $("#CustomOrderMaterial tbody").append(content);
});
selectMaterialNMs = [];
selectMaterialClsNMs = [];
oldSelectBuyPrice = 0;
oldSelectSellPrice = 0;

// if ('input[name="selectMaterialID[Orderold0]"]') {
//     len = $(".OrderDate").length;
//     for (var i = 0; i <= len - 1; i++) {
//         MaterialID = $('input[name="selectMaterialID[Orderold' + i + ']"]').val()
//         selectMaterialNMs[MaterialID] = MaterialID;
//     }
// }

//特注品追加ボタン押下
btnClickCustom = 0;
$(document).on("click", ".CustomMaterialAdd", function () {
    btnClickCustom++
    SelectMaterialID = "999999";
    SelectMaterialNM = $(this).closest('tr').find('input[name="CustomMaterialNM"]').val();
    SelectMaterialNMVal = SelectMaterialNM;
    SelectMaterialNM = SelectMaterialNM.replaceAll(" ", "-");
    SelectType = $(this).closest('tr').find('input[name="CustomType"]').val();
    SelectTypeVal = SelectType;
    SelectType = SelectType.replaceAll(" ", "-");
    SelectUse = $(this).closest('tr').find('input[name="CustomUse"]').val();
    SelectLoss = $(this).closest('tr').find('input[name="CustomLoss"]').val();
    SelectBuyPrice = $(this).closest('tr').find('input[name="BuyPrice"]').val();
    SelectSellPrice = $(this).closest('tr').find('input[name="SellPrice"]').val();
    SelectCustomMaterialUnit = $(this).closest('tr').find('input[name="CustomMaterialUnit"]').val();

    BuyNumber = $(this).closest('tr').find('input[name="BuyNumber"]').val();

    if (SelectUse == "") {
        SelectUse = 0;
        $(this).closest('tr').find('input[name="CustomUse"]').val(0);
    };
    if (SelectLoss == "") {
        SelectLoss = 0;
        $(this).closest('tr').find('input[name="CustomLoss"]').val(0);
    };
    if (SelectBuyPrice == "") {
        SelectBuyPrice = 0;
        $(this).closest('tr').find('input[name="BuyPrice"]').val(0);
    };
    if (SelectSellPrice == "") {
        SelectSellPrice = 0;
        $(this).closest('tr').find('input[name="SellPrice"]').val(0);
    };
    if (BuyNumber == "") {
        BuyNumber = 0;
        $(this).closest('tr').find('input[name="BuyNumber"]').val(0);
    };
    SelectStockNum = 0;
    flag = true;
    // 「使用数」「ロス数」「仕入数」とも0の場合は、「追加」アイコンをクリックしても、右側ペインに追加しない
    if (!SelectMaterialNM) {
        ErrMsg = "品名を入力してください。";
        flag = false;
    }
    if ((SelectLoss == 0 && SelectUse == 0 && BuyNumber == 0)) {
        ErrMsg = "「使用数」「ロス数」「仕入れ数」のいずれかを入力してください。";
        flag = false;
    }
    if (!isDecimal(SelectLoss) || !isDecimal(SelectUse)) {
        ErrMsg = "「使用数」「ロス数」「仕入単価」「仕入れ数」「出値単価」には全角文字を入力できません。";
        flag = false;
    }
    SelectBuyNumber = BuyNumber;
    if (flag) {
        trclass = "oldtr-" + SelectMaterialID +"-"+ SelectMaterialNM +"-"+  SelectType;
        trid = "#SelectMaterial tbody ." + trclass + " td .form-control";
        console.info(trclass)
        if ($("#SelectMaterial tr").hasClass(trclass)) {
            // 更新
            UseNum = $(trid + ".shorttext.selectUse").val()
            LossNum = $(trid + ".shorttext.selectLoss").val()
            UseNum = parseFloat(UseNum) + parseFloat(SelectUse);
            LossNum = parseFloat(LossNum) + parseFloat(SelectLoss);
            $(trid + ".shorttext.selectUse").val(UseNum)
            $(trid + ".shorttext.hidden.selectUse").val(UseNum);
            $(trid + ".shorttext.selectLoss").val(LossNum)
            $(trid + ".shorttext.hidden.selectLoss").val(LossNum);
        } else {
            // 新規
            content = "<tr class='" + trclass + " selectmaterial'>" + $(".btn-add-content").html() + "</tr>";
            $("#SelectMaterial tbody").append(content);
            $(trid + ".text.selectMaterialID").attr("name", "selectMaterialID[" + (btnClickCustom) + "]").val(SelectMaterialID);
            $(trid + ".hidden.selectMaterialID").attr("name", "selectMaterialID[Order" + (btnClickCustom) + "]").val(SelectMaterialID);
            $(trid + ".hidden.selectMaterialID").removeAttr('disabled');

            $(trid + ".text.selectMaterialNM").attr("name", "selectMaterialNM[" + (btnClickCustom) + "]").val(SelectMaterialNMVal);
            $(trid + ".hidden.selectMaterialNM").attr("name", "selectMaterialNM[Order" + (btnClickCustom) + "]").val(SelectMaterialNMVal);
            $(trid + ".hidden.selectMaterialNM").removeAttr('disabled');

            $(trid + ".text.selectType").attr("name", "selectType[" + (btnClickCustom) + "]").val(SelectTypeVal);
            $(trid + ".hidden.selectType").attr("name", "selectType[Order" + (btnClickCustom) + "]").val(SelectTypeVal);
            $(trid + ".hidden.selectType").removeAttr('disabled');

            $(trid + ".hidden.selectCustomMaterialUnit").attr("name", "selectCustomMaterialUnit[Order" + (btnClickCustom) + "]").val(SelectCustomMaterialUnit);

            $(trid + ".shorttext.selectUse").val(SelectUse);
            $(trid + ".shorttext.hidden.selectUse").attr("name", "selectUse[Order" + (btnClickCustom) + "]").val(SelectUse);
            $(trid + ".shorttext.hidden.selectUse").removeAttr('disabled');

            $(trid + ".shorttext.selectLoss").val(SelectLoss);
            $(trid + ".shorttext.hidden.selectLoss").attr("name", "selectLoss[Order" + (btnClickCustom) + "]").val(SelectLoss);
            $(trid + ".shorttext.hidden.selectLoss").removeAttr('disabled');

            $(trid + ".shorttext.selectBuyPrice").val(SelectBuyPrice);
            $(trid + ".shorttext.hidden.selectBuyPrice").attr("name", "selectBuyPrice[Order" + (btnClickCustom) + "]").val(SelectBuyPrice);
            $(trid + ".shorttext.hidden.selectBuyPrice").removeAttr('disabled');

            $(trid + ".shorttext.selectSellPrice").attr("name", "selectSellPrice[" + (btnClickCustom) + "]").val(SelectSellPrice);
            $(trid + ".shorttext.hidden.selectSellPrice").attr("name", "selectSellPrice[Order" + (btnClickCustom) + "]").val(SelectSellPrice);
            $(trid + ".shorttext.hidden.selectBuyNumber").attr("name", "selectBuyNumber[Order" + (btnClickCustom) + "]").val(SelectBuyNumber);
            $(trid + ".shorttext.hidden.selectSellPrice").removeAttr('disabled');

            $(trid + ".shorttext.hidden.StockNum").attr("name", "StockNum[Order" + (btnClickCustom) + "]").val(SelectStockNum);
            $(trid + ".shorttext.hidden.StockNum").removeAttr('disabled');
        }
    } else {
        $("#MessageModal .modal-body").html(ErrMsg);
        $("#MessageModal").modal();
    }
});
selectMaterials = [];
btnClick = 0;
oldSelectUse = 0;
oldSelectLoss = 0;
oldSelectBuyPrice = 0;
oldSelectSellPrice = 0;
//資材追加ボタン
$(document).on("click", ".MaterialAdd", function () {
    SelectUse = $(this).closest('tr').find('input[name="Use"]').val();
    SelectLoss = $(this).closest('tr').find('input[name="Loss"]').val();
    if (SelectUse == "") {
        $(this).closest('tr').find('input[name="Use"]').val(0);
    };
    if (SelectLoss == "") {
        SelectLoss = $(this).closest('tr').find('input[name="Loss"]').val(0);
    };
    if ('input[name="selectMaterialID[old0]"]') {
        len = $(".oldDate").length;
        for (var i = 0; i <= len - 1; i++) {
            MaterialID = $('input[name="selectMaterialID[old' + i + ']"]').val()
            selectMaterials[MaterialID] = MaterialID;
        }
    }

    SelectMaterialID = $(this).closest('tr').find('input[name="MaterialID"]').val();
    SelectMaterialNM = $(this).closest('tr').find('input[name="MaterialNM"]').val();
    SelectType = $(this).closest('tr').find('input[name="Type"]').val();

    SelectStockNum = $(this).closest('tr').find('input[name="StockNum"]').val();
    SelectSellPrice = $(this).closest('tr').find('input[name="SellPrice"]').val();
    SelectBuyPrice = $(this).closest('tr').find('input[name="BuyPrice"]').val();
    // SelectSellPrice = SelectUse * SelectSellPrice;
    oldSelectBuyPrice = "";
    oldSelectSellPrice = "";


    if ((!SelectUse || SelectUse == 0) && (!SelectLoss || SelectLoss == 0)) {
        $("#MessageModal .modal-body").html("使用数、ロス数のどちらかを入力してください");
        $("#MessageModal").modal();
    } else {
        trid = "#SelectMaterial tbody .tr-" + SelectMaterialID + " td .form-control";
        if (SelectMaterialID in selectMaterials) {
            selectedUse = $(trid + ".shorttext.selectUse").val()
            selectedLoss = $(trid + ".shorttext.selectLoss").val()
            selectedUse = parseFloat(selectedUse) + parseFloat(SelectUse);
            selectedLoss = parseFloat(selectedLoss) + parseFloat(SelectLoss);
            $(trid + ".shorttext.selectUse").val(selectedUse)
            $(trid + ".shorttext.selectLoss").val(selectedLoss)

            if (len = $(".oldDate").length) {
                selectedUse = $("#SelectMaterial tbody .oldtr-" + SelectMaterialID + " td .form-control.shorttext.selectUse").val()
                selectedLoss = $("#SelectMaterial tbody .oldtr-" + SelectMaterialID + " td .form-control.shorttext.selectLoss").val()
                selectedUse = parseFloat(selectedUse) + parseFloat(SelectUse);
                oldSelectUse = parseFloat(SelectUse) + parseFloat(oldSelectUse);
                selectedLoss = parseFloat(selectedLoss) + parseFloat(SelectLoss);
                oldSelectLoss = parseFloat(SelectLoss) + parseFloat(oldSelectLoss);
                $("#SelectMaterial tbody .oldtr-" + SelectMaterialID + " td .form-control.shorttext.selectUse").val(selectedUse)
                $("#SelectMaterial tbody .oldtr-" + SelectMaterialID + " td .form-control.shorttext.selectUse.hidden").val(selectedUse)
                $("#SelectMaterial tbody .oldtr-" + SelectMaterialID + " td .form-control.shorttext.selectLoss").val(selectedLoss)
                $("#SelectMaterial tbody .oldtr-" + SelectMaterialID + " td .form-control.shorttext.selectLoss.hidden").val(selectedLoss)
            }
        } else {

            content = "<tr class='tr-" + SelectMaterialID + " selectmaterial'>" + $(".btn-add-content").html() + "</tr>"
            $("#SelectMaterial tbody").append(content);
            $(trid + ".text.selectMaterialID").attr("name", "selectMaterialID[" + (btnClick) + "]").val(SelectMaterialID);
            $(trid + ".hidden.selectMaterialID").attr("name", "selectMaterialID[" + (btnClick) + "]").val(SelectMaterialID);
            $(trid + ".hidden.selectMaterialID").removeAttr('disabled');

            $(trid + ".text.selectMaterialNM").attr("name", "selectMaterialNM[" + (btnClick) + "]").val(SelectMaterialNM);
            $(trid + ".hidden.selectMaterialNM").attr("name", "selectMaterialNM[" + (btnClick) + "]").val(SelectMaterialNM);
            $(trid + ".hidden.selectMaterialNM").removeAttr('disabled');

            $(trid + ".text.selectType").attr("name", "selectType[" + (btnClick) + "]").val(SelectType);
            $(trid + ".hidden.selectType").attr("name", "selectType[" + (btnClick) + "]").val(SelectType);
            $(trid + ".hidden.selectType").removeAttr('disabled');
            $(trid + ".shorttext.selectUse").val(SelectUse);
            $(trid + ".shorttext.hidden.selectUse").attr("name", "selectUse[" + (btnClick) + "]").val(SelectUse);
            $(trid + ".shorttext.hidden.selectUse").removeAttr('disabled');
            $(trid + ".shorttext.selectLoss").val(SelectLoss);
            $(trid + ".shorttext.hidden.selectLoss").attr("name", "selectLoss[" + (btnClick) + "]").val(SelectLoss);
            $(trid + ".shorttext.hidden.selectLoss").removeAttr('disabled');
            $(trid + ".shorttext.selectBuyPrice").attr("name", "SelectBuyPrice[" + (btnClick) + "]").val(SelectBuyPrice);
            $(trid + ".shorttext.hidden.selectBuyPrice").attr("name", "selectBuyPrice[" + (btnClick) + "]").val(SelectBuyPrice);
            $(trid + ".shorttext.hidden.selectBuyPrice").removeAttr('disabled');
            $(trid + ".shorttext.selectSellPrice").attr("name", "selectSellPrice[" + (btnClick) + "]").val(SelectSellPrice);
            $(trid + ".shorttext.hidden.selectSellPrice").attr("name", "selectSellPrice[" + (btnClick) + "]").val(SelectSellPrice);
            $(trid + ".shorttext.hidden.selectSellPrice").removeAttr('disabled');
            $(trid + ".shorttext.StockNum").attr("name", "StockNum[" + (btnClick) + "]").val(SelectStockNum);
            $(trid + ".shorttext.hidden.StockNum").attr("name", "StockNum[" + (btnClick) + "]").val(SelectStockNum);
            $(trid + ".shorttext.hidden.StockNum").removeAttr('disabled');
            selectMaterials[SelectMaterialID] = SelectMaterialID;

        }
        btnClick++
    }

    // })
});
$(document).ready(function () {
    $(document).on('keyup', ".input.number", function () {
        var val = $(this).val();
        if (isNaN(val)) {
            val = val.replace(/[^0-9\.]/g, '');
            if (val.split('.').length > 2)
                val = val.replace(/\.+$/, "");
            $(this).val(val);
        }
    });

    var keypadFlag = false;
    checkdot = false;
    $("body").on("focus", ".inputKeypadWithDot", function () {
        checkdot = false;
        rederKeypadWithDot(this)
    })

    $("body").on("focus", ".inputKeypad", function () {
        checkdot = false;
        rederKeypad(this)
    })
    $("body").on("focus", ".inputKeypadWithoutMinus", function () {
        checkdot = false;
        rederKeypadWithoutMinus(this)
    })

    // keypad は type="number" の input に対応していないため、数値ボタンをカスタムで追加して無理矢理対応する
    var customNumberKeys = [
        ['ZERO', 'zero', 0],
        ['ONE', 'one', 1],
        ['TWO', 'two', 2],
        ['THREE', 'three', 3],
        ['FOUR', 'four', 4],
        ['FIVE', 'five', 5],
        ['SIX', 'six', 6],
        ['SEVEN', 'seven', 7],
        ['EIGHT', 'eight', 8],
        ['NINE', 'nine', 9],
        ['DOT', 'dot', "."]
    ];
    customNumberKeys.forEach(customNumberKey => {
        $.keypad.addKeyDef(customNumberKey[0], customNumberKey[1], function (inst) {
            thisval = $(this).val();
            if (thisval == 0) {
                // default input = 0
                if (checkdot) {

                    this.val(thisval + String(customNumberKey[2])).focus();
                } else {
                    this.val(String(customNumberKey[2])).focus();
                }

            } else {
                // 管材、 保温材」 は0 .1 単位で入力
                thisArr = thisval.split('.')
                if (thisArr.length = 2) {
                    if (!thisArr[1]) {
                        this.val(thisval + String(customNumberKey[2]));
                    }
                } else {
                    this.val(thisval + String(customNumberKey[2]));
                }
                this.focus();
            }
        });
    });
    $.keypad.addKeyDef('MINUS', 'minus', function (inst) {
        let val = (this.val())
        if (val > 0) {
            this.val(val * -1).focus();
        }
    });
    $.keypad.addKeyDef('PLUS', 'plus', function (inst) {
        thisval = this.val();
        if (!checkdot && thisval.split('.').length < 2) {
            this.val(thisval + ".").focus();
            checkdot = true;
        }
    });

    function rederKeypad(elem) {
        $(elem).keypad({
            duration: 'fast',
            keypadOnly: false,
            closeText: '閉じる',
            closeStatus: '',
            clearText: 'クリア',
            clearStatus: '',
            zeroText: '0',
            zeroStatus: '',
            oneText: '1',
            oneStatus: '',
            twoText: '2',
            twoStatus: '',
            threeText: '3',
            threeStatus: '',
            fourText: '4',
            fourStatus: '',
            fiveText: '5',
            fiveStatus: '',
            sixText: '6',
            sixStatus: '',
            sevenText: '7',
            sevenStatus: '',
            eightText: '8',
            eightStatus: '',
            nineText: '9',
            nineStatus: '',
            dotText: '.',
            dotStatus: '',
            minusText: '-',
            minusStatus: '',
            plusText: '.',
            plusStatus: '',
            layout: [
                $.keypad.SEVEN + $.keypad.EIGHT + $.keypad.NINE + $.keypad.CLOSE,
                $.keypad.FOUR + $.keypad.FIVE + $.keypad.SIX + $.keypad.CLEAR,
                $.keypad.ONE + $.keypad.TWO + $.keypad.THREE,
                $.keypad.ZERO + $.keypad.MINUS
            ],
        });
    }

    function rederKeypadWithDot(elem) {
        $(elem).keypad({
            duration: 'fast',
            keypadOnly: false,
            closeText: '閉じる',
            closeStatus: '',
            clearText: 'クリア',
            clearStatus: '',
            zeroText: '0',
            zeroStatus: '',
            oneText: '1',
            oneStatus: '',
            twoText: '2',
            twoStatus: '',
            threeText: '3',
            threeStatus: '',
            fourText: '4',
            fourStatus: '',
            fiveText: '5',
            fiveStatus: '',
            sixText: '6',
            sixStatus: '',
            sevenText: '7',
            sevenStatus: '',
            eightText: '8',
            eightStatus: '',
            nineText: '9',
            nineStatus: '',
            dotText: '.',
            dotStatus: '',
            minusText: '-',
            minusStatus: '',
            plusText: '.',
            plusStatus: '',
            layout: [
                $.keypad.SEVEN + $.keypad.EIGHT + $.keypad.NINE + $.keypad.CLOSE,
                $.keypad.FOUR + $.keypad.FIVE + $.keypad.SIX + $.keypad.CLEAR,
                $.keypad.ONE + $.keypad.TWO + $.keypad.THREE,
                $.keypad.ZERO + $.keypad.MINUS + $.keypad.PLUS
            ],
        });
    }

    function rederKeypadWithoutMinus(elem) {
        $(elem).keypad({
            duration: 'fast',
            keypadOnly: false,
            closeText: '閉じる',
            closeStatus: '',
            clearText: 'クリア',
            clearStatus: '',
            zeroText: '0',
            zeroStatus: '',
            oneText: '1',
            oneStatus: '',
            twoText: '2',
            twoStatus: '',
            threeText: '3',
            threeStatus: '',
            fourText: '4',
            fourStatus: '',
            fiveText: '5',
            fiveStatus: '',
            sixText: '6',
            sixStatus: '',
            sevenText: '7',
            sevenStatus: '',
            eightText: '8',
            eightStatus: '',
            nineText: '9',
            nineStatus: '',
            dotText: '.',
            dotStatus: '',
            minusText: '-',
            minusStatus: '',
            plusText: '.',
            plusStatus: '',
            layout: [
                $.keypad.SEVEN + $.keypad.EIGHT + $.keypad.NINE + $.keypad.CLOSE,
                $.keypad.FOUR + $.keypad.FIVE + $.keypad.SIX + $.keypad.CLEAR,
                $.keypad.ONE + $.keypad.TWO + $.keypad.THREE,
                $.keypad.ZERO
            ],
        });
    }

    $(".materialsearch").on("click", function () {
        getListMater()
    })
    $(document).on('click', ".pagination .page-link", function () {
        page = $(this).attr("page");
        getListMater(page)
    })

    function getListMater(page = 0) {
        datasend = "page=" + page
        datasend += "&WWID=" + $("[name=WWID]").val()
        datasend += "&searchID=" + $("input[name=searchID]").val()
        datasend += "&searchUnitListCD=" + $("input[name=searchUnitListCD]").val()
        datasend += "&searchMaterialCls=" + $("[name=searchMaterialCls]").val()
        datasend += "&searchMaterialNM=" + $("input[name=searchMaterialNM]").val()
        datasend += "&searchType=" + $("input[name=searchType]").val()
        // alert(datasend)

        $.ajax({
            type: "get",
            url: "/getListMaterial",
            data: datasend,
            success: function (response) {
                if (response["status"] == 1) {
                    $(".pagination").html(response["page"])
                    $(".materialtable tbody.table").html(response["html"])

                } else {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });

    }

    $(".save").on("click", function () {
        flg = true;
        $('input[name=selectUse]').each(function () {
            if ($(this).val() < 0) {
                flg = false;
                ErrMsg = "使用数";
            }
        })
        $('input[name=selectLoss]').each(function () {
            if ($(this).val() < 0) {
                flg = false;
                ErrMsg = "ロス数";
            }
        })
        $('input[name=selectBuyNumber]').each(function () {
            console.info($(this).val())
            if ($(this).val() < 0) {
                flg = false;
                ErrMsg = "仕入れ数";
            }
        })
        if (!flg) {
            $("#MessageModal .modal-body").html(ErrMsg + getMsgByID("error093"));
            $("#MessageModal").modal();
        } else {
            $(".save").attr("type", "submit");
        }
    })
});