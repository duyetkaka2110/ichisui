$(document).ready(function() {
    $(".btn-clear").on("click", function() {
        $(".form-search td .txtsearch").val("")
        $(".form-search td select").val("")
        $(".form-search td .OrderStatus").removeClass("active")
        $(".form-search td .OrderStatus0 input").prop("checked", true);
        $(".form-search td .OrderStatus0").addClass("active")

    })
    $(".btn-Export").on("click", function() {
        total = $("#ResultSearchCount span").html()
        if (total == 0) {
            $("#MessageModal .modal-body").html(getMsgByID("error001"));
            $("#MessageModal").modal();
        } else {
            $(this).attr("type", "submit")
                // $(".formsubmit").append("<input type='hidden' name='Export' value='Export'")
            $(".formsubmit").submit();
        }
    })
    $('.datetimepicker').datetimepicker({
        format: 'YYYY/MM/DD',
        locale: 'ja',
    });
});
$(document).ready(function() {
    $(document).on("click", ".image-thumb", function() {
        $("#ImageModal h5").html($(this).find("img").attr("alt"));
        $("#ImageModal .modal-body").html($(this).html());
        $("#ImageModal").modal();
    })
    $(document).on("click", ".btnchecknum", function() {
        $.ajax({
            type: "post",
            url: "/getCheckedHistory",
            data: $(this).attr("data"),
            success: function(response) {
                if (response["status"] == 1) {
                    $("#HistoryModal h5").html(response["title"]);
                    $("#HistoryModal .modal-body table tbody").html(response["html"]);
                    $("#HistoryModal").modal();
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
    })
});
$(document).ready(function() {

    //　検品済」ボタンをクリックして「はい」ボタンをクリックする時DB更新
    $(".modal-btn-status-ok").on("click", function() {
        valuetxt = $(this).attr("data-update");
        valuetxt += "&CheckDate=" + $('input[name="txtCheckDate"]').val();
        valuetxt += "&kenpinoption=" + $('input[name="kenpinoption"]:checked').val();
        valuetxt += "&kenpinoptionnumber=" + $('input[name="kenpinoptionnumber"]').val();
        $.ajax({
            type: "get",
            url: "/setCheckDetail",
            data: valuetxt,
            success: function(response) {
                if (response == '1') {
                    $('#ConfirmKenpinModal').modal('hide');
                    $(".btn-search").click();
                } else {
                    $('#MessageModal .modal-body').html(response);
                    $('#MessageModal').modal();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

                $("#MessageModal .modal-body").html(getMsgByID("error026"));
                $("#MessageModal").modal();
            }
        });
    });

    //「検品済」ボタンをクリックする時ポップアップ表示される
    $("body").on("click", ".btnkenpin", function(e) {
        if (!$(this).hasClass("disabled")) {
            if ($(this).attr("stocknum")) {
                $("#ConfirmKenpinModal .modal-header h5").html(getMsgByID("info005") + '(検品)');
                dataupdate = $(this).attr('data-update')
                $.ajax({
                    type: "post",
                    url: "/KenpinDetail",
                    data: dataupdate,
                    success: function(response) {
                        try {
                            if (response["status"] == 1) {
                                dataArr = response["data"];
                                $('input[name="txtCheckDate"]').val(moment().format('YYYY/MM/DD'));
                                $('input[name="txtOrderDate"]').val(dataArr["OrderDate"]);
                                $('input[name="txtOrderNum"]').val(dataArr["OrderNum"]);
                                $('input[name="txtUserNM"]').val(dataArr["MaterialNM"]);
                                $('input[name="txtSupplierNM"]').val(dataArr["SupplierNM"]);
                                $('input[name="kenpinoptionnumber"]').val('');
                                $('input:radio[name=kenpinoption][value=allkenpin]').click();
                                $('.txtOrderUnitNM').html(dataArr["OrderUnitNM"]);
                                $(".modal-btn-status-ok").attr("data-update", dataupdate)
                                if (dataArr["TotalCheck"] == '.0') dataArr["TotalCheck"] = 0;
                                $('input[name="txtTotalCheck"]').val(dataArr["TotalCheck"]);
                                $("#ConfirmKenpinModal").modal();
                            }
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
                // }
            } else {
                $("#MessageModal .modal-body").html(getMsgByID("error022"));
                $("#MessageModal").modal();
            }
            return false;
        }
    });
})