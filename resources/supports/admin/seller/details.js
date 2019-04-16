$(document).ready(function () {
    $('.step-status').on('click', function (e) {
        var CurEle = $(this);
        $('.alert').remove();
        $.ajax({
            url: CurEle.data('url'),
            data: {status: CurEle.is(':checked') ? 1 : 0, step_id: CurEle.data('step_id'), supplier_id: CurEle.data('supplier_id')},
            success: function (data) {
                CurEle.parents('.tab-pane').find('.table').before(data.msg);
            }
        });
    });
});
