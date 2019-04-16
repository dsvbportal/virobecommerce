$(document).ready(function () {
    $('#tax_info').on('submit', function (event) {
        event.preventDefault();
        CURFORM = $('#tax_info');
        var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (op) {
                if(op.status==208){
				    $(CURFORM).before('<div class="alert alert-warning alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ op.msg + '</div>');
				}
            }
        });
    });
   
});

$(document).ready(function () {
    $('#gstin_form').on('submit', function (event) {
        event.preventDefault();
        CURFORM = $('#gstin_form');
		console.log(CURFORM);
        var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (op) {
				if(op.status==208){
				    $(CURFORM).before('<div class="alert alert-warning"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ op.msg + '</div>');
				}
            },
        });
    });
   
});
$('.tax-proof-edit-btn').on('click', function (e) {
        e.preventDefault();
        var Cur = $(this);
        $('.tax-proof-display', Cur.parents('.tax-proof')).hide();
        $('.tax-proof-edit', Cur.parents('.tax-proof')).show();
    });