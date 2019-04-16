$(document).ready(function () {
	
	$('#changepassword #old_user_pwd').on('keyup',function () {
	    if ($(this).val() == '') {
			$('#changepassword #old_user_pwd_err').empty();
		}
	});
	
    var chgpwd = $('#changepassword').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            old_user_pwd: 'required',
            newpassword: {
                required: true,
                minlength: 8,
            },
            confirmpassword: {
                required: true,
                equalTo: '#newpassword'
            }
        },
        messages: $val_message,
		errorPlacement: function(error, element) {			
			if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
		},
        submitHandler: function (form, event) {
            event.preventDefault();
			CURFORM = $('#changepassword');			
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: CURFORM.attr('action'),
                    //url: "affiliate/settings/update_password",
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {				
                        $('.alert').remove();
                        $('#changepassword #updatepwd').find('span').text($processing);
                        $('#changepassword button[type="submit"]').attr('disabled', true);
                    },
                    success: function (data) {					
						$('button[type="submit"]').attr('disabled',false);
                        $('#changepassword #oldpassword_status').hide();  
                        $('#changepassword').trigger('reset');						
                    },
                });
            }
        }
    });
        
    $('#changepassword #old_user_pwd').blur(function () {
        var oldpassword = $(this).val();
			CURFORM = $('#changepassword');
        if (!(oldpassword == '')) {
            $.ajax({
                type: 'POST',
                data: {'old_user_pwd': oldpassword},
                url: $('#changepassword').attr('data-checkpwd'),
                //url: 'affiliate/settings/password_check',
                dataType: "json",
				beforeSend: function () {
					$('.alert').remove();
				},
                success: function (data) {					
                   /*  if (data.status == 'ok') {
                        $('#changepassword #oldpassword_status').html('');
                        return true;
                    } else if (data.status == 'error') 
					{
                        $('#changepassword #newpassword').val('');
                        $('#changepassword #confirmpassword').val('');
                        $('#changepassword #oldpassword_err').addClass('help-block').show().html(data.msg);
                        return false;
                    } */
                },
				error: function (jqXHR, textStatus, errorThrown) {				
					responseText = $.parseJSON(jqXHR.responseText);
					$.each(responseText.errs,function(fld,msg){
						if($('#changepassword input[name='+fld+']').parent().hasClass('input-group')){
							$('#changepassword input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
						}else {
							$('#changepassword input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
						}
						return false;
					});
				},
            });
        }
    });
});
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
  