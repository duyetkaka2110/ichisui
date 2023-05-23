$(document).ready(function() {
    $(".mg-pagination .page-item a").on("click", function() {
        $('.loading').removeClass('d-none');
    })

    $("body").on("click", ".form-search .btn-clear", function() {
        $(".form-search .txtsearch").val("")
    });
    // 「個別発注追加」ボタンをクリック時
    $("body").on("click", ".btnAddBulk", function() {
            getMaterial()
        })
        // 資材マスタ一覧: paginationクリック時
    $("body").on("click", "#MaterialModal .page-select", function() {
            getMaterial($(this).attr('page'))
        })
        // 資材マスタ一覧: 検索ボタンをクリック時
    $("body").on("click", "#MaterialModal .btn-mater-search", function() {
            getMaterial()
        })
        // 選択する行
    $("body").on("click", "#MaterialModal .table tbody tr", function() {
            $("#MaterialModal .table tbody tr").removeClass("active")
            $(this).addClass("active")
        })
        // 「発注数」と「仕入単価」更新
    $("body").on("change", ".tbl-input", function() {
        if ($(this).val() == "") {
            $("#MessageModal .modal-body").html(getMsgByID("error089"));
            $("#MessageModal").modal();
        } else {
            $.ajax({
                type: "get",
                url: "/updateOrderByID",
                beforeSend: function() {
                    $('.loading').addClass('d-none');
                },
                data: $(this).attr("name") + "=" + $(this).val() + "&OrderID=" + $(this).attr("use"),
                success: function(response) {
                    if (response["status"] == 1) {
                        showAlert(response["Msg"])
                    } else {
                        $("#MessageModal .modal-body").html(response["Msg"]);
                        $("#MessageModal").modal();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {

                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        }
    })

    //「決定」ボタンをクリックする時
    $("body").on("click", "#MaterialModal .btnOk", function() {
        if ($("#MaterialModal .table tbody tr").hasClass("active")) {
            datasend = {
                "MaterialID": $("#MaterialModal .table tbody tr.active td:nth-child(1)").html(),
                "SupplierID": $("#MaterialModal .table tbody tr.active select").val(),
            }
            $.ajax({
                type: "post",
                url: "/insertupdateOrder",
                data: datasend,
                success: function(response) {
                    if (response["status"] == 1) {
                        showAlert(response["Msg"])
                        $("#MaterialModal").modal("hide")
                        window.location.href = "/order";
                    } else {
                        $("#MessageModal .modal-body").html(response["Msg"]);
                        $("#MessageModal").modal();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {

                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                },
                complete: function() {
                    $('.loading').removeClass('d-none');
                },
            });
        } else {
            $("#MessageModal .modal-body").html(getMsgByID("error016"));
            $("#MessageModal").modal();
        }
    });

    //「「発注」ボタンをクリックする時ポップアップ表示される
    $("body").on("click", ".btnExport", function(e) {
        $("#ConfirmModal .btnOk").attr("id", "btnExportOk");
        $("#ConfirmModal .modal-body p").html("これで発注します。良いですか？");
        $("#ConfirmModal").modal();
        return false;
    });
    //「「発注OK」ボタンをクリックする時
    $(document).on("click", "#btnExportOk", function() {
        $.ajax({
            type: "post",
            url: "/checkexport",
            success: function(response) {
                if (response["status"] == 1 || response["status"] == 2) {
                    $.ajax({
                        type: "post",
                        xhrFields: {
                            responseType: 'blob',
                        },
                        url: "/orderexport",
                        success: function(result, status, xhr) {
                            if (response["status"] == 1) {
                                var disposition = xhr.getResponseHeader('content-disposition');
                                var matches = /"([^"]*)"/.exec(disposition);
                                var filename = (matches != null && matches[1] ? matches[1] : '発注.xlsx');

                                // The actual download
                                var blob = new Blob([result], {
                                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                });
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(blob);
                                link.download = filename;

                                document.body.appendChild(link);

                                link.click();
                                document.body.removeChild(link);
                            }
                            $(".table-order tbody").html("")
                            $(".mg-pagination").html("")
                            $("#ResultSearchCount span").html("0")
                            showAlert("発注処理が完了しました")
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $("#MessageModal .modal-body").html(getMsgByID("error026"));
                            $("#MessageModal").modal();
                        }
                    });
                } else {
                    $("#MessageModal .modal-body").html(response["ErrMsg"]);
                    $("#MessageModal").modal();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });

    });

    function TabelReflow($tbl) {
        $tbl.floatThead({
            scrollContainer: function($tbl) {
                return $tbl.closest('.table-responsive');
            }
        });
        $tbl.floatThead("reflow");
    }
    $("#MaterialModal").on("shown.bs.modal", function() {
        setTimeout(function() { TabelReflow($(".table-marterial")); }, 500);
    });
    // 資材一覧を取る
    function getMaterial(page = 1) {
        $('.m-content').animate({
            scrollTop: (0)
        }, 'slow');
        datasend = {
            "page": page,
            "MaterialID": $(".txtMaterialID").val(),
            "MaterialNM": $(".txtMaterialNM").val(),
            "Type": $(".txtType").val(),
        }
        $.ajax({
            type: "post",
            url: "/getListMater",
            data: datasend,
            success: function(response) {
                if (response["status"] == 1) {
                    $("#MaterialModal").modal();
                    $(".table-marterial tbody").html(response["html"])
                    $(".matterialpagi").html(response["page"])
                    $('.m-content').animate({
                        scrollTop: (0)
                    }, 'slow');
                } else {
                    // Error
                    // $("#MessageModal .modal-body").html(response["Msg"]);
                    $("#MessageModal").modal();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    }
    $("body").on("change", ".td-supplier", function() {
        if ($(this).val() == "") {
            $("#MessageModal .modal-body").html(getMsgByID("error089"));
            $("#MessageModal").modal();
        } else {
            UseMaterialID = $(this).attr("UseMaterialID");
            valuetxt = "UseMaterialID=" + UseMaterialID + "&SupplierID=" + $(this).val()
            $.ajax({
                type: "post",
                url: "/updateOrderMaterial",
                data: valuetxt,
                success: function(response) {
                    if (response["status"] == 1) {
                        showAlert(response["Msg"])
                        $(".tr-" + UseMaterialID + ' input[name="PurUnit"]').val(response["PurUnit"])
                    } else {
                        // Error
                        $("#MessageModal .modal-body").html(response["Msg"]);
                        $("#MessageModal").modal();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {

                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        }
    })

    // 「一括発注追加(在庫不足分)」ボタンをクリック時
    $("body").on("click", ".btnBulk", function() {
        $.ajax({
            type: "post",
            url: "/addbulk",
            success: function(response) {
                if (response["status"] == 1) {
                    // レコードがある場合
                    $("#ConfirmModal .modal-body p").html(response["Msg"]);
                    $("#ConfirmModal .btnOk").attr("id", "modal-btn-addbulkok");
                    $("#ConfirmModal").modal();
                } else if (response["status"] == 2) {
                    // レコードがない場合
                    $('.loading').removeClass('d-none');
                    showAlert(response["Msg"])
                    location.reload();
                } else {
                    // Error
                    $("#MessageModal .modal-body").html(response["Msg"]);
                    $("#MessageModal").modal();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    })

    // 「一括発注追加(在庫不足分)」ボタンをクリック時:レコードがある場合:modal-btn-addbulkok
    $("body").on("click", "#modal-btn-addbulkok", function() {
        $.ajax({
            type: "post",
            url: "/addbulkOk",
            success: function(response) {
                if (response["status"] == 1) {
                    // レコードがある場合
                    showAlert(response["Msg"])
                    location.reload();
                } else {
                    // Error
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    })

    //「「発注済扱い」スイッチ」をクリックする時ポップアップ表示される
    $("body").on("click", ".table .btn-switch label", function(e) {
        if (e.target.tagName == 'LABEL') {
            checkboxid = $(this).attr("for")
            datatxt = $("#" + $(this).attr("for")).attr("data-update");
            $.ajax({
                type: "post",
                url: "/updateOrderMaterial",
                data: datatxt,
                success: function(response) {
                    if (response["status"]) {
                        // $('#' + checkboxid).trigger('click');
                        showAlert("更新が完了しました")
                    } else {
                        // Error
                        $("#MessageModal .modal-body").html(response["Msg"]);
                        $("#MessageModal").modal();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {

                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        }
    });


    //「削除」ボタンをクリックする時
    $("body").on("click", ".btn-delete", function() {
        //削除
        $("#ConfirmModal .modal-body p").html(getMsgByID("info011"));
        $("#ConfirmModal .btnOk").attr("id", "modal-btn-delete");
        $("#ConfirmModal .btnOk").attr("data", $(this).attr("data"));
        $("#ConfirmModal .btnOk").attr("use", $(this).attr("use"));
        $("#ConfirmModal").modal();
    })

    //「削除のはい」ボタンをクリックする時
    $("body").on("click", "#modal-btn-delete", function() {
        //削除
        use = $(this).attr("use")
        $.ajax({
            type: "post",
            url: "/deleteOrderMaterial",
            data: $(this).attr("data"),
            success: function(response) {
                $(".tr-" + use).remove()
                showAlert("更新が完了しました")
                $("#ResultSearchCount span").html($("#ResultSearchCount span").html() - 1)
            },
            error: function(jqXHR, textStatus, errorThrown) {

                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    });
});