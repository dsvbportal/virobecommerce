$(document).ready(function () {
	$('#resetspwd,#forgotspwd_otp,#newspwd').keypress(function(){
		$('.alert').remove();
	});
	
	$('#change_security_pin #oldpassword').on('keyup',function () {
	    if ($(this).val() == '') {
			$('#change_security_pin #tran_oldpassword_err').empty();
		}
	});
	
	$('#change_security_pin #oldpassword').blur(function () {
		CURFORM = $(this).closest('form');
        var oldpassword = $(this).val();
        if (!(oldpassword == '') && oldpassword.length==4) {
            $.ajax({
                type: 'POST',
                data: {'oldpassword': oldpassword},
                url:  $("#change_security_pin #oldpassword").data('url'),
                dataType: "json",		
				beforeSend: function () {
					$('.alert').remove();
					$('button[type="submit"]').attr('disabled', 'disabled');
                },
                success: function (data) {					
					$('button[type="submit"]').attr('disabled',false);
                   /* if (data.status == 'ok') {						
                        $('#change_security_pin #tran_oldpassword_err').html('');
                        return true;
                    }   else if (data.status == 'error') {
                        $('#tran_newpassword #tran_confirmpassword','#change_security_pin').val('');                     
                        $('#change_security_pin #tran_oldpassword_err').addClass('help-block').show().html(data.msg);
                        return false;
                    } */
                },              
            });
        }else{
		    $('#change_security_pin #tran_oldpassword_err').trigger('keyup');
		}
    });
	
    var chgpwd = $('#change_security_pin').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
           oldpassword: "required",
           tran_newpassword: {
                required: true,
                minlength: 4,
                maxlength: 4
            },
            tran_confirmpassword: {
                required: true,
                equalTo: "#tran_newpassword"
            }
        },
        messages: $chgpin_val_message,
		errorPlacement: function(error, element) {			
			if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
	   },
        submitHandler: function (form, event) {
            event.preventDefault();
		    CURFORM = $('#change_security_pin');
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: $(form).attr('action'),
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                        $('button[type="submit"]',CURFORM).attr('disabled', true);
                    },
                    success: function (data) {						
                        $('#oldpassword_status',CURFORM).hide();
						$('input,select',CURFORM).val('');		
                        //$('#change_security_pin').before('<div class="alert ' + data.alertclass +'"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
					    $('button[type="submit"]',CURFORM).attr('disabled', false);
                        //$('#update_securitypwd',CURFORM).find('span').text($update);                       				
                    },
                    /* error: function (jqXHR, textStatus, errorThrown) {
						$('body').toggleClass('loaded');	
						$('button[type="submit"]').attr('disabled',false);
						responseText = $.parseJSON(jqXHR.responseText);
						$.each(responseText.errs,function(fld,messages){
							if($('#change_security_pin input[name='+fld+']').siblings().hasClass('input-group')){
								$('#change_security_pin input[name='+fld+']').siblings().after("<div class='help-block'>"+messages+"</div>");
							}else {
								$('#change_security_pin input[name='+fld+']').after("<div class='help-block'>"+messages+"</div>");
							}
						});
						return false;
					}, */
                });
            }
        }
    });
  
	$('#change_pin').on('click','#forgot_sec_pwd,#resent_forgotpin_otp',function (e) {
		e.preventDefault();
        var datastring = '';
		$this = $(this);
        $("#processing").replaceWith('');
        $.ajax({
            url: $(this).attr('href'), // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
            datatype: "json",    
			beforeSend: function () {
				$('.alert').remove();
				$this.attr('disabled', true);
			},
			success: function (op) {	
				if(op.status == 200){	
					$this.attr('disabled', false);
				    $('#forgotspwd_otpfrm #otp').val('');
					$('#forgotspwd_otpfrm .alert').remove();
					$('#resetspwd').fadeOut('fast',function(){
						$('#forgotspwd_otp').fadeIn('fast');
					});	
					$('#forgotspwd_otpfrm').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i>' + op.msg + '</div>');
				}
            },
			error: function (jqXHR, textStatus, errorThrown) {		
				responseText = $.parseJSON(jqXHR.responseText);	
				if(responseText.msg != undefined){
				    $('#change_security_pin').before('<div class="alert alert-danger">' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				}
			}, 
        });
    });
	
	var resetotp = $('#forgotspwd_otpfrm').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {			
			otp: {
                required: true,
                minlength: 6,
                maxlength: 6
            }            
        },
        messages: $changeotp_val_message,
		errorPlacement: function(error, element) {			
			if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
	   },
        submitHandler: function (form, event) {
			event.preventDefault();	
			CURFORM = $('#forgotspwd_otpfrm');
            if ($(form).valid()) {
				var datastring = $('#forgotspwd_otpfrm').serialize();
                $.ajax({
                    url:  $('#forgotspwd_otpfrm').attr('action'),
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                    },
                    success: function (data) {
						if(data.msg=='ok'){
						    $('#forgotspwd_save input').val('');
						    $('#change_pin #change_pin_title').text('Create Security PIN');
							$('#forgotspwd_otp').fadeOut('fast',function(){
								$("#newspwd").fadeIn('fast');
								$('#newspwd input[type=text]').val();
							});	
						}
						else{
							$('#forgotspwd_otpfrm').prepend('<div class="alert ' + data.status_class +'"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
							$('button[type="submit"]').attr('disabled', false);
							$('input,select','#forgotspwd_otp').val('');
						}
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
					    responseText = $.parseJSON(jqXHR.responseText);	
						$('#forgotspwd_otp button[type="submit"]').attr('disabled',false);			
						/*if(responseText.msg != undefined){
						    $('#forgotspwd_otpfrm').prepend('<div class="alert ' + responseText.status_class +'"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + responseText.msg + '<br></div>');
						}*/									
					},
                });
			}	
		}
	}); 

    var repwd = $('#forgotspwd_save').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {			
			tran_newpassword: {
                required: true,
                minlength: 4,
                maxlength: 4
            },
            tran_confirmpassword: {
                required: true,
                equalTo: "#forgot_tran_newpassword"
            }
        },
        messages: $savepin_val_message,
		errorPlacement: function(error, element) {			
			if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
	    },
        submitHandler: function (form, event) {
			event.preventDefault();
			CURFORM = $('#forgotspwd_save');
            if ($(form).valid()) {
                var datastring = $('#forgotspwd_save').serialize();
				console.log(datastring);
                $.ajax({
                    url: $("#forgotspwd_save").attr('action'),
                    type: "POST",
                    data: datastring,
                    dataType: "JSON",
                    beforeSend: function () {
                        $('.alert').remove();
                        $('#forgotspwd_savebtn').attr('disabled', true);						
                    },
                    success: function (data) {							
						$('#change_pin #change_pin_title').text('Change Security PIN');
						$('#newspwd form').resetForm();
						$('#change_security_pin input').val('');
						$('#newspwd').fadeOut('fast',function(){
							$('#resetspwd').fadeIn('fast');
						});	
						CURFORM.data('errmsg-fld','#change_security_pin');
						CURFORM.data('errmsg-placement','before');
					    $('#forgotspwd_savebtn').attr('disabled', false);	
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
						data = $.parseJSON(jqXHR.responseText);					
						$('#forgotspwd_savebtn').attr('disabled', false);
					},
                });
            } 
        }
    });
	    var createpin = $('#create_security_pin').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
           new_security_pin: {
                required: true,
                minlength: 4,
                maxlength: 4
            },
            confirm_security_pin: {
                required: true,
                equalTo: "#new_security_pin"
            }
        },
        messages: $createpin_val_message,
		errorPlacement: function(error, element) {			
			if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
	   },
        submitHandler: function (form, event) {
            event.preventDefault();
		    CURFORM = $('#create_security_pin');
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: $(form).attr('action'),
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                        
                    },
                    success: function (data) {						
					  $('input,select',CURFORM).val('');
					   $(".change_security_form").show();
					   CURFORM.data('errmsg-fld','#change_security_pin');
						CURFORM.data('errmsg-placement','before');
					 	$(".create_security_form").hide();
                    },
                   
                });
            }
        }
    }); 
});