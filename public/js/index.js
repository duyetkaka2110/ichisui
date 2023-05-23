var strclick = "dblclick";
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    strclick = "click";
}
(function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    }
})(jQuery);
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('.loading').show();
        },
        complete: function() {
            $('.loading').hide();
        },
    });
    // var activeAjaxCount = 0;

    // $(document).ajaxStart(function() {
    //     activeAjaxCount++;
    //     console.log(activeAjaxCount);
    //     $('.loading').show();
    // });

    // $(document).ajaxComplete(function() {
    //     activeAjaxCount--;
    //     console.log(activeAjaxCount);
    //     if (activeAjaxCount == 0) {
    //         $('.loading').hide();
    //     }
    // });
    // $("table.tablesorter th").append('<i class="fa fa-unsorted"></i>');
    var $tbl = $('.tablelistmain');
    //sortTable("tablesorter");
    //sortTable("tablelistkoji");
    $("table input").click(function(e) {
        e.stopPropagation();
    });
    $("table select").click(function(e) {
        e.stopPropagation();
    });
    //show 
    var $tbl = $('.tablelistkoji');
    //reset Popup
    $("#btnkojikensaku").on('click', function() {
        resetFormPopup();
        resetTable();
    });
    $('#ProjectModal').on('shown.bs.modal', function(e) {
        var $tbl = $('.tablelistkoji');
        //reset float tbody
        $tbl.floatThead({
            scrollContainer: function($tbl) {
                return $tbl.closest('.table-responsive');
            }
        });
        $tbl.floatThead("reflow");
    });
    //Enter->Submit Form Search
    $(".margin-search-title input[type='text']").bind("keypress", function(e) {
        if (e.keyCode == 13) {
            $(".BtnSearch").click();
        }
    });
    CheckDate();
    //reset input
    $("#BtnClearTablelistkoji").on("click", function() {
        resetFormPopup();
    });
    $("body").on(strclick, ".tablelist td.enable", function(e) {
        e.stopPropagation(); //<-------stop the bubbling of the event here
        var currentEle = $(this);
        var value = $(this).html();
        updateVal(currentEle, value);
    });

    function isDecimal(num) {
        return (num ^ 0) !== num;
    }
    $("body").on("change", ".number", function() {
        number = $(this).val();
        if (number < 0 || number > 999999.9 || !isDecimal(number)) {
            $(this).val("");
            errorMsg = getMsgByID("error032");
            if ($(this).attr("step") == 1) {
                errorMsg = getMsgByID("error033");
            }
            $("#MessageModal .modal-body").html(errorMsg);
            $("#MessageModal").modal();
            $("#MessageModal .close-modal").focus();
        }
    });
    $(document).on('show.bs.modal', '.modal', function() {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });
    //function sortTable
    // function sortTable(tableclass, thsort = "") {
    //     $('.' + tableclass + ' th').each(function(col) {
    //         $(this).hover(
    //             function() {
    //                 $(this).addClass('focus');
    //             },
    //             function() {
    //                 $(this).removeClass('focus');
    //             }
    //         );
    //         $(this).click(function() {

    //             $(this).parent().find("th i").attr("class", "fa fa-unsorted");
    //             if ($(this).is('.asc')) {
    //                 $(this).removeClass('asc');
    //                 $(this).addClass('desc selected');
    //                 $(this).find("i").attr("class", "fa fa-caret-down");
    //                 sortOrder = -1;
    //             } else {
    //                 $(this).addClass('asc selected');
    //                 $(this).removeClass('desc');
    //                 $(this).find("i").attr("class", "fa fa-caret-up");
    //                 sortOrder = 1;
    //             }
    //             $(this).siblings().removeClass('asc selected');
    //             $(this).siblings().removeClass('desc selected');
    //             var arrData = $('table.' + tableclass).find('tbody >tr:has(td)').get();
    //             arrData.sort(function(a, b) {
    //                 var val1 = $(a).children('td').eq(col).text().toUpperCase();
    //                 var val2 = $(b).children('td').eq(col).text().toUpperCase();
    //                 if ($.isNumeric(val1) && $.isNumeric(val2))
    //                     return sortOrder == 1 ? val1 - val2 : val2 - val1;
    //                 else
    //                     return (val1 < val2) ? -sortOrder : (val1 > val2) ? sortOrder : 0;
    //             });
    //             $.each(arrData, function(index, row) {
    //                 $('table.' + tableclass + ' tbody').append(row);
    //             });
    //             // $("#ProjectModal .modal-loading").hide();
    //         });
    //     });
    // }
    //バーコード入力時
    $("input[name='TxtBarcode']").keypress(function(event) {
        if (event.which == 13) {
            $(".BtnSearch").click();
            event.preventDefault();
        }
    });

    function resetFormPopup() {
        $(".tablelistkoji th input").val("");
        $(".tablelistkoji th select").val("");
    }

    function resetTable() {
        $(".tablelistkoji tr").removeClass("active");
        $(".tablelistkoji tr").removeClass("hidden").addClass("show");

    }

    function updateVal(currentEle, value) {
        if (currentEle.find("input.thVal").length === 0) {
            $(currentEle).html('<input class="thVal" type="number" min=0 value="" />');
            $(".thVal").val(value);
            $(".thVal").focus();
            $(".thVal").keyup(function(event) {
                if (event.keyCode == 13) {
                    updateHtml(currentEle);
                }
            });
            $(currentEle).focusout(function() { // you can use $('html')
                updateHtml(currentEle);
            });
        }
    }

    function updateHtml(currentEle) {
        if (currentEle.find("input.thVal").length !== 0) {
            val = $(".thVal").val();
            if (isInteger(val)) {
                $(currentEle).html(parseInt(val));
            } else {
                $("#MessageModal .modal-body").html("?��?��?��̐�?��?��?��?��?��?��͂�?��Ă�?��?��?��?��?��?��");
                $("#MessageModal").modal();
                $(".close-modal").focus();
                $('#MessageModal').on('hidden.bs.modal', function() {
                    $(".thVal").focus();
                })
            }
        }
    }

    function isInteger(n) {
        return /^[0-9]+$/.test(n);
    }

});

function replaceStr(str) {
    return str.replace("'", "\'");
}

function showErrorPopup(error) {
    $("#MessageModal .modal-body").html(getMsgByID(error));
    $("#MessageModal").modal();
}

function CheckDate() {
    $('.txtdateinput').on('focusin', function() {
        $(this).data('val', $(this).val());
    });
    $(".txtdateinput").on("change", function() {
        if (!moment($(this).val(), 'YYYY/MM/DD', true).isValid() && $(this).val()) {
            $(this).val($(this).data('val'));
            $("#MessageModal .modal-body").html(getMsgByID("error023"));
            $("#MessageModal").modal();
            $("#MessageModal .close-modal").focus();
        }
    });
}