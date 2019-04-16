var email_validate_message={email:'',verify_code:''};
$(document).ready(function () {
    var CEF = $('#change-email-form');
    CEF.validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            email: {
                required: true,
                email: true,
				maxlength:40
            }
        },
        messages : $email_validate_message.email,
        submitHandler: function (form, event) {
            event.preventDefault();
			if (CEF.valid()) {
				CURFORM = CMF;
				$.ajax({
					url: CEF.attr('action'),
					data: CEF.serialize(),
					dataType: 'JSON',
					type: 'POST',
					beforeSend: function () {
						$('.alert,div.help-block').remove();
						$('#send_verification_code').attr('disabled',true);
					},
					success: function (data) {							
						if(data.status == 200){
							$('#send_verification_code').hide().attr('disabled',false);
							$('#change-email-form').fadeOut('fast',function(){
								$('#code_verification_form').fadeIn('slow');
								$(this).before("<div class='alert alert-success'>"+data.msg+"</div>");
							});					
						}else{
							$('#change-email-form input[name="email"]').after("<div class='help-block'>"+data.msg+"</div>");
						}	
					}
				});
			}
        }
    });
    $('input').bind('cut copy paste', function (e) {
        e.preventDefault();
    });
    $('#email').on('focus', function () {
	    $('.alert ,div.help-block').remove();
    }); 

	/* Code_Verification_Form */
    var CVF = $('#code_verification_form');
    CVF.validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
		rules: {
		   verify_code: {
				required: true,
				digits: true,
				maxlength:6,
				minlength:6
			
		  }
		},
		messages : $email_validate_message.verify_code, 
		submitHandler: function (form, event) {
            event.preventDefault();
                if (CVF.valid()) {
					CURFORM = CVF;
                    $.ajax({
                        url: CVF.attr('action'),
                        data: CVF.serialize(),
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            $('.alert,div.help-block').remove();
							$('#update_email').attr('disabled','disabled');
							$('#verify_code').val("");
							$('body').toggleClass('loaded');
                        }, 
					    error: function (jqXHR, textStatus, errorThrown) {	
                            $('body').toggleClass('loaded');
                            $('#update_email').attr('disabled',false);							
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#code_verification_form input[name='+fld+']').siblings().hasClass('input-group')){
									$('#code_verification_form input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
								} else {
									$('#code_verification_form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
								return false;
							});
						}, 
                        success: function (data){ 
							$('body').toggleClass('loaded');
							$('#update_email').attr('disabled',false);
						    if(data.status == 200){
							    $('#crnt_email').val($('#email').val());
                                $('#code_verification_form').fadeOut('fast',function(){
									$('#change-email-form').fadeIn('slow');
									$('#email').val("");
								});							
						    	$('#verify_code').val("");
                                $('#change-email-form').before($('<div >').attr({class: 'alert alert-success'}).html(data.msg)); 
                            }else{
								$('#change-email-form').before($('<div >').attr({class: 'alert alert-danger'}).html(data.msg)); 
                            }		
                        }
                    });
                }
        }
    });	
    $('#verify_code').on('focus', function () {
	    $('.alert ,div.help-block').remove();
    });
	$("#code_verification_form #email_id").click(function(event){
	   event.preventDefault();
		if (CEF.valid()) {
			CURFORM = CEF;
			$.ajax({
				url: CEF.attr('action'),
				data: CEF.serialize(),
				dataType: 'JSON',
				type: 'POST',
				beforeSend: function () {
					$('.alert,div.help-block').remove();
					$('#send_verification_code').attr('disabled','disabled');
					$('body').toggleClass('loaded');
				}, 
				error: function (jqXHR, textStatus, errorThrown) {
                    $('body').toggleClass('loaded');
                    $('#send_verification_code').attr('disabled',false);					
					responseText = $.parseJSON(jqXHR.responseText);
					$.each(responseText.errs,function(fld,msg){
						if($('#change-email-form input[name='+fld+']').siblings().hasClass('input-group')){
							$('#change-email-form input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
						} else {
							$('#change-email-form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
						}
						return false;
					});
				},
				success: function (data) {
				    $('body').toggleClass('loaded');
					$('#send_verification_code').attr('disabled',false);
					if(data.status == 'ok'){
					    $('#change-email-form').fadeOut('fast',function(){
							$('#code_verification_form').fadeIn('slow');
							$('#codemsg').text(data.msg);
							$('#verify_code').val("");
						});	 
					}else{
						$('#change-email-form input[name="email"]').after("<div class='help-block'>"+data.msg+"</div>");
					}	
			    }
			});
		}
    });
});

$(document).on('click','#cancel',function (e) {
	e.preventDefault();
	$('#email').val("");
	$('#code_verification_form').fadeOut('fast',function(){
		$('#change-email-form').fadeIn('slow');
	});
});	