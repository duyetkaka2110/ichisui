$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function() {
        $('.loading').removeClass('d-none');
    },
    complete: function() {
        $('.loading').addClass('d-none');
    },
});

ListMsg = []
    //メッセージ一覧取る
$.ajax({
    type: "post",
    url: "/getMsg",
    async: false,
    success: function(response) {
        try {
            ListMsg = response;
        } catch (err) {
            $("body").html('<div class="margin-error jqr">システムエラーが発生しました。<br>ご迷惑をおかけし申し訳ございません。<br>システム保守へご連絡ください。</div>');
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        $("body").html(jqXHR["responseText"]);
    }
});
//メッセージを検索する
function getMsgByID(id) {
    msg = "";
    $.each(ListMsg, function(key, m) {
        if (key == 0 && id == "error001") {
            msg = m[1];
            return false;
        }
        if (m[0] == id) {
            msg = m[1];
            return false;
        }
    });
    return msg;
}

function showAlert(alertcnt = "") {
    if (alertcnt) $("#success-alert").html(alertcnt)
    $("#success-alert").fadeTo(5000, 500).fadeIn(300, function() {
        $("#success-alert").fadeOut(300);
    });
}
$(document).on('show.bs.modal', '.modal', function() {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});