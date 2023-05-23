$(document).ready(function() {
    $("#matter").on("click", function() {
        $("#ConfirmModal .modal-body p").html("工事入力画面に移動します。<br>工事Noを新規で採番します。宜しいですか？")
        $("#ConfirmModal .btnOk").attr("id", "btnOpenMater")
        $("#ConfirmModal").modal()
    })

    $(document).on("click", "#btnOpenMater", function() {
        window.location.href = '/matterinput';
    })
})