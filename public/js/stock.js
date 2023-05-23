$(document).ready(function() {
    $('.datetimepicker').datetimepicker({
        format: 'YYYY/MM/DD',
        locale: 'ja',
    });
    //検索条件Labelをクリックする
    $(".margin-search-title .title-search").on("click", function() {
        if ($(".title-search i").hasClass("fa-minus-square-o")) {
            $(".title-search i").removeClass("fa-minus-square-o");
            $(".title-search i").addClass("fa-plus-square-o");
            $(this).removeClass("toggle");
        } else {
            $(".title-search i").addClass("fa-minus-square-o");
            $(".title-search i").removeClass("fa-plus-square-o");
            $(this).addClass("toggle");
        }
        $(".margin-search").slideToggle(500, function() {});

    });

    config = {
        locale: 'ja-jp',
        uiLibrary: 'bootstrap4',
        format: "yyyy/mm/dd"
    };
    // $('#TxtDate').datepicker(config);
    //Reset検索条件
    $(".BtnResetSearch").on("click", function() {
        $(".resettext").val("");
        $('.margin-search input[name="TxtStockDate"]').prop('checked', false);
    });

    //検索ボタンをクリックする時、データを表示する
    $(".BtnSearch").on("click", function() {
        UpdateListStock();
    });

    //棚卸年月の選択
    $("#TxtStockYM").on("change", function() {
        UpdateListStock();
        $("input[name='TxtStockID']").val($(this).val());
    });

    //ImageZoomModal Popup
    $("body").on('click', '.tablelistmain td img', function() {
        $("#ImageZoomModal .modal-body").html('<img src="' + $(this).attr("src") + '" alt="' + $(this).attr("alt") + '" />');
        $("#ImageZoomModal .modal-header").html($(this).attr("alt"));
        $("#ImageZoomModal").modal();
    });

    $('#QuantityModal').on('shown.bs.modal', function(e) {
        $('#QuantityModal .table-responsive').animate({
            scrollTop: 0
        }, '10', 'swing');
        $tbl = $("#QuantityModal .tablelist");
    });
    // 「「⑨実数」列のリンクをクリックする時、実数入力画面表示される
    $("body").on("click", ".tablestock td .pop", function() {
        valuetxt = $(this).attr("data-update");
        $.ajax({
            type: "post",
            url: "/getStockDetail",
            data: valuetxt,
            success: function(response) {
                try {
                    dataArr = jQuery.parseJSON(response);
                    $("#QuantityModal input[name='Qty-MaterialNM']").val(dataArr["MaterialNM"]);
                    $("#QuantityModal input[name='Qty-Type']").val(dataArr["Type"]);
                    $("#QuantityModal input[name='Qty-MaterialAlias']").val(dataArr["MaterialAlias"]);
                    $("#QuantityModal input[name='Qty-Date']").val(dataArr["StockDate"]);
                    $(".tablestockdetail tbody").html(dataArr["html"]);
                    $("#QuantityModal .btn-Qty-Update").show();
                    valuetxt = $("input.TxtQty").serialize();
                    //棚卸確定した時、登録されない
                    if (!valuetxt) {
                        $("#QuantityModal input[name='Qty-Date']").attr("disabled", true);
                        $("#QuantityModal .input-group-append").hide();
                        $("#QuantityModal .btn-Qty-Update").hide();
                    } else {
                        $("#QuantityModal input[name='Qty-Date']").attr("disabled", false);
                        $("#QuantityModal .input-group-append").show();
                        $("#QuantityModal .btn-Qty-Update").show();
                    }
                    $("#QuantityModal").modal();
                } catch (err) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    });
    //実数入力画面で「登録」ボタンをクリックする時DB更新
    $("body").on("click", ".btn-Qty-Update", function() {
        valuetxt = $("input.TxtQty").serialize();
        if (valuetxt) {
            valuetxt += "&" + $("input.TxtQtyMaterialID").serialize();
            valuetxt += "&" + $("input.TxtQtyStockID").serialize();
            valuetxt += "&" + $("input.TxtQtyStoreID").serialize();
            valuetxt += "&" + $("input.TxtQtyShelfID").serialize();
            valuetxt += "&TxtQtyDate=" + $("input[name='Qty-Date']").val();
            $.ajax({
                type: "post",
                url: "/setStockDetailPopup",
                data: valuetxt,
                success: function(response) {
                    UpdateListStock($('.pagination .page-item.active').attr("page"));
                    $("#QuantityModal").modal("hide");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        }
    });
    //「棚卸確定」ボタンをクリックする時
    $(".btn-InvenConfirm").on("click", function() {
        if ($("select[name='TxtStockYM']").val()) {
            valuetxt = "StockID=" + $("select[name='TxtStockYM']").val();
            valuetxt += "&CloseFlg=1";
            $.ajax({
                type: "post",
                url: "/updateInvenConfirm",
                data: valuetxt,
                success: function(response) {
                    if (response == "1") {
                        $("#MessageInfoModal .modal-body").html(getMsgByID("info008"));
                        $("#MessageInfoModal").modal();
                        UpdateListStock();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error006"));
            $("#MessageModal").modal();
        }
    });

    //「棚卸確定削除」ボタンをクリックする時
    $(".btn-InvenConfirm-Delete").on("click", function() {
        if ($("select[name='TxtStockYM']").val()) {
            valuetxt = "StockID=" + $("select[name='TxtStockYM']").val();
            valuetxt += "&CloseFlg=0";
            $.ajax({
                type: "post",
                url: "/updateInvenConfirm",
                data: valuetxt,
                success: function(response) {
                    if (response == "1") {
                        $("#MessageInfoModal .modal-body").html(getMsgByID("info012"));
                        $("#MessageInfoModal").modal();
                        UpdateListStock();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error006"));
            $("#MessageModal").modal();
        }
    });
    //「棚卸確定」ボタンをクリックする時
    $(".btn-InvenCorrec").on("click", function() {
        if ($("select[name='TxtStockYM']").val()) {
            valuetxt = "StockID=" + $("select[name='TxtStockYM']").val();
            $.ajax({
                type: "post",
                url: "/updateInvenCorrec",
                data: valuetxt,
                success: function(response) {
                    $("#MessageInfoModal .modal-body").html(getMsgByID("info009"));
                    $("#MessageInfoModal").modal();
                    UpdateListStock();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error006"));
            $("#MessageModal").modal();
        }
    });
    //「Excel読込」ボタンをクリックする時
    $(".Btn-Import").on("click", function() {
        if ($("select[name='TxtStockYM']").val()) {
            $("input[name='FileImport']").click();
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error006"));
            $("#MessageModal").modal();
        }
    });
    //Excel読込
    $("input[name='FileImport']").on("change", function() {
        if ($(this).val() && $("select[name='TxtStockYM']").val()) {
            $(".importfile").submit();
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error006"));
            $("#MessageModal").modal();
        }

    });

    //pagination
    $(document).on("click", ".pagination .page-select", function() {
        UpdateListStock($(this).attr("page"))
    });


    //「Excel出力」ボタンをクリックする時
    $(".Btn-Export").on("click", function() {

        if ($("select[name='TxtStockYM']").val()) {
            valuetxt = "StockYM=" + $("select[name='TxtStockYM']").val();

            rowCount = $(".tablestock tbody tr").length;
            flag = true;
            if (rowCount == 0) flag = false;
            if (rowCount == 1) {
                if ($(".tablestock tbody tr:first").hasClass("no-data")) flag = false;
            }
            if (!flag) {
                $("#MessageModal .modal-body").html(getMsgByID("error001"));
                $("#MessageModal").modal();
                $(".close-modal").focus();
            } else {
                location.href = './stock/export?' + valuetxt;
            }
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error006"));
            $("#MessageModal").modal();
            $(".close-modal").focus();
        }
    });
});

// "棚卸確定後以下の処理が実装されていない。
// ・棚卸確定後にExcel表の取込を行った際はエラーメッセージを表示する。
// ・棚卸確定後に在庫一括修正を行った際はエラーメッセージを表示する。
// ・実数入力画面を非活性にする。"
function DisabledButton(CloseFlg, AmountAllFixDate = null) {
    flagBtn = false
    if (CloseFlg == 1) {
        flag = true;
        // 在庫一括修正ボタン IS NOT NULL
        if (AmountAllFixDate) {
            flagBtn = true;
        }
    } else {
        flag = false;

    }
    $(".Btn-Import").attr("disabled", flag);
    $(".TxtQty").attr("disabled", flag);
    if (flag) {
        $(".btn-InvenConfirm").hide();
        $(".btn-InvenConfirm-Delete").show();
        $(".btn-InvenCorrec").show();
    } else {
        $(".btn-InvenConfirm").show();
        $(".btn-InvenConfirm-Delete").hide();
        $(".btn-InvenCorrec").hide();
    }
    //在庫一括修正ボタンIS NOT NULL
    $(".btn-InvenConfirm-Delete").attr("disabled", flagBtn);
    $(".Btn-Import").attr("disabled", flag);
    if (flagBtn) {
        $(".btn-InvenCorrec").hide();
    }
}


function _getDataForm(page) {
    var formData = new FormData();
    formData.append("StockYM", $("select[name='TxtStockYM']").val());
    formData.append("MaterialID", $("input[name='TxtMaterialID']").val());
    formData.append("MaterialNM", $("input[name='TxtMaterialNM']").val());
    formData.append("Type", $("input[name='TxtType']").val());
    formData.append("StockDate", $("input[name='TxtStockDate']").is(':checked'));
    formData.append("MaterialCls", $("select[name='TxtMaterialCls']").val());
    formData.append("Page", page);
    return formData;
}
//棚卸一覧画面更新
function UpdateListStock(curpage = 0) {
    tblscroll = "tablesroll";
    $('#' + tblscroll).animate({
        scrollTop: 0
    }, '10', 'swing');
    if ($("select[name='TxtStockYM']").val()) {
        formData = _getDataForm(curpage);
        $.ajax({
            type: "post",
            url: "/getListStock",
            cache: false,
            contentType: false,
            dataType: false,
            processData: false,
            data: formData,
            success: function(response) {
                try {
                    dataArr = response;
                    tblname = "tablestock";
                    $("." + tblname + " tbody.tbody").html(dataArr["html"]);
                    $("#ResultSearchCount span").html(dataArr["CountResult"]);
                    $(".AmountAllFixDate").val(dataArr["AmountAllFixDate"]);
                    $('.pagination').html(dataArr["page"])
                    DisabledButton(dataArr["CloseFlg"], dataArr["AmountAllFixDate"])
                } catch (err) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    } else {
        $("#MessageModal .modal-body").html(getMsgByID("error006"));
        $("#MessageModal").modal();
    }
}