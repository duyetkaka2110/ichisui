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
    //メッセージ一覧取る
    $.ajax({
        type: "post",
        url: "/getMsg",
        async: false,
        success: function(response) {
            ListMsg = response;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

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