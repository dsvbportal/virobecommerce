$(document).ready(function () {
	if ($('#fullname').val() !== '') {			
		$('#supplier-sign-up-form').hide();
		$('#check-user').addClass('hidden');	
		$('.heading').addClass('hidden');	
	}
	
    $('#send-email-verification-link').on('click', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            url: CurEle.data('url'),
            success: function (OP) {
                $('#send-email-verification-link').text('Resend');
            }
        });
    });
    $('#verify-email-id-block').on('click', '.dismiss', function (e) {
        e.preventDefault();
        $('#verify-email-id-block').fadeOut(200);
    });
});
