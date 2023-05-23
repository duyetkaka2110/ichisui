$(document).ready(function() {
    $(".btn-clear").on("click", function() {
        $(".form-search td .txtsearch").val("")
        $(".form-search td select").val("")
        $(".form-search td .WorkStatus").removeClass("active")
        $(".form-search td .WorkStatus0").addClass("active")
        $(".form-search td .WorkStatus0 input").prop("checked", true);

    })
    $(".btn-Export").on("click", function() {
        total = $("#ResultSearchCount span").html()
        if (total == 0) {
            $("#MessageModal .modal-body").html(getMsgByID("error001"));
            $("#MessageModal").modal();
        } else {
            $.ajax({
                type: "get",
                xhrFields: {
                    responseType: 'blob',
                },
                data: $('.formsubmit').serialize() + "&Export=Export",
                url: "/work",
                async: true,
                timeout: 0, // sets timeout to 10 minute
                success: function(result, status, xhr) {
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : '工事.xlsx');

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
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        }
    })

    // 修繕報告書出力
    $(".btnExcelHokoku").on("click", function() {
        if (!$(".WorkTimeFrom").val() || !$(".WorkTimeTo").val()) {
            $("#MessageModal .modal-body").html(getMsgByID("error080"));
            $("#MessageModal").modal();
        } else {
            $.ajax({
                type: "post",
                url: "/work",
                async: true,
                timeout: 0, // sets timeout to 10 minute
                data: $('.formsubmit').serialize() + "&btnExcelHokoku=btnExcelHokoku&check=1",
                success: function(response) {
                    if (response["status"] == 1) {
                        $.ajax({
                            type: "post",
                            timeout: 0, // sets timeout to 10 minute
                            xhrFields: {
                                responseType: 'blob',
                            },
                            async: true,
                            data: $('.formsubmit').serialize() + "&btnExcelHokoku=btnExcelHokoku&check=0",
                            url: "/work",
                            success: function(result, status, xhr) {
                                var disposition = xhr.getResponseHeader('content-disposition');
                                var matches = /"([^"]*)"/.exec(disposition);
                                var filename = (matches != null && matches[1] ? matches[1] : '修繕報告書.xlsx');

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
        }
    });
    // 修繕内訳書出力
    $(".btnExcelUchiwake").on("click", function() {
        flag = true;
        if (!$(".WorkTimeFrom").val() || !$(".WorkTimeTo").val()) {
            flag = false;
            ErrMsg = getMsgByID("error080");
        }
        if ($(".WorkTimeFrom").val() && $(".WorkTimeTo").val()) {
            // ※同じ月ではない場合、以下のエラー文を表示する
            if (!moment($(".WorkTimeFrom").val()).isSame($(".WorkTimeTo").val(), 'month') || !moment($(".WorkTimeFrom").val()).isSame($(".WorkTimeTo").val(), 'year')) {
                flag = false;
                ErrMsg = getMsgByID("error097");
            }
        }
        if (flag) {
            $.ajax({
                type: "get",
                timeout: 0, // sets timeout to 10 minute
                xhrFields: {
                    responseType: 'blob',
                },
                async: true,
                data: $('.formsubmit').serialize(),
                url: "/workExportUchiwake",
                success: function(result, status, xhr) {
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : '修繕内訳書.xlsx');

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
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#MessageModal .modal-body").html(getMsgByID("error026"));
                    $("#MessageModal").modal();
                }
            });
        } else {
            $("#MessageModal .modal-body").html(ErrMsg);
            $("#MessageModal").modal();
        }
    });
    $('.datetimepicker').datetimepicker({
        format: 'YYYY/MM/DD',
        locale: 'ja',
    });
})