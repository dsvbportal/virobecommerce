$(document).ready(function () {
	$('#name', $('#signup-success-div')).html($('#fullname').val());	
	
	
    $('#mobile-verification-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $('#mobile-verification-form');
        $.ajax({
            url: $('#mobile-verification-form').attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: $('#mobile-verification-form').serialize(),
            beforeSend: function () {
                $('input[type=submit]', $('#mobile-verification-form')).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {				
                $('input[type=submit]', $('#mobile-verification-form')).removeAttr('disabled', true).val('Verify');
                
                if ($('#signup-success-div').length) {
                    $('#signup-success-div').show();
                    $('#supplier-sign-up-form,#mobile-verification-div').hide();
                }
				if (OP.url != undefined) {
                    window.location.href = OP.url;
                }
            },
            error: function (jqXhr) {
                $('input[type=submit]', $('#mobile-verification-form')).removeAttr('disabled', true).val('Verify');
            }
        });
    });
    $('#resend-verification-code').on('click', function (e) {
        e.preventDefault();
		$('#verification_code').val('');
        var CurEle = $(this);
        $.ajax({
            url: CurEle.data('url')
        });
    });
   
	if ($('#fullname').val() !== '') {				
		$('#supplier-sign-up-form').hide();
		$('#check-user').addClass('hidden');	
		$('.heading').addClass('hidden');	
		 $('#resend-verification-code').trigger('click');
	}
	
	$('#verification_code').on('keypress', function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
			return false;
		}
		return true;
    });
	
	
});
