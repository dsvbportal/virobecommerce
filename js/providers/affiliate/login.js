$(function () {
	
	if (localStorage.chkbx && localStorage.chkbx != '') {		
		$('#loginfrm #user_remember_me').attr('checked', 'checked');
		$('#loginfrm #user_remember_me').iCheck('check');
		$('#loginfrm #uname').val(localStorage.usrname);
		$('#loginfrm #password').val(localStorage.pass);	
		//alert(localStorage.usrname);
	} else {
		$('#loginfrm #user_remember_me').iCheck('uncheck');
		$('#loginfrm #user_remember_me').removeAttr('checked');			
		$('#loginfrm #uname').val('');
		$('#loginfrm #password').val('');
		//alert('uncheked state');
	}

	$('#loginfrm #user_remember_me').on('ifChecked',function() {
		//alert('check');
		if ($('#loginfrm #user_remember_me').is(':checked')) {
			// save username and password
			localStorage.usrname = $('#loginfrm #uname').val();
			localStorage.pass = $('#loginfrm #password').val();
			localStorage.chkbx = $('#loginfrm #user_remember_me').val();
		}
	});
	
	$('#loginfrm #user_remember_me').on('ifUnchecked',function() {
		//alert('uncheck');	
		localStorage.usrname = '';
		localStorage.pass = '';
		localStorage.chkbx = '';		
	});	
	
	$.validator.addMethod('uname_validate', function (value, element, params) {
		var emailfilter = /^[\w._-]+[+]?[\w._-]+@[\w.-]+\.[a-zA-Z]{2,6}$/;
		if((value.split("@").length>1) && (emailfilter.test(value))) {		
			return this.optional(element) || true;
		} else if (!(/[a-z0-9]/i.test(value))) {		
			return this.optional(element) || false;
		} else if (/[~`!#$%\^&*+=\-\_@\[\]\\';.,/(){}|\\":<>\?]/g.test(value)) {		
			return this.optional(element) || false;
		} else {
			return this.optional(element) || true;
		}
	});

	$("#loginfrm").validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		rules:{
			uname:{
				required:true,
				uname_validate:true,
				minlength:6,
				maxlength:25
			},
			password: {
				required:true,
				minlength:6,
				maxlength:12
			}
		} ,
		messages: {
			uname : {
				required : "Username is required",
				uname_validate : "Invalid Account ID (or) Email Id",
				minlength: "Username Can't be less than 6 Characters",
			},
			password: {
				required : "Password is required",
				minlength : "Invalid Password"
			}
		},	
		submitHandler: function (form, event) {
			event.preventDefault();
			if ($(form).valid()) {
				var frmObj = $(form);
				$.ajax({
					type: 'POST',
					url: frmObj.attr('action'),
					data: frmObj.serialize(),
					dataType: 'json',
					error: function (jqXHR, textStatus, errorThrown) {
						$('.alert').remove();					
						$('input.form-control',frmObj).prop('disabled',false);
						$('button[type=submit]',frmObj).prop('disabled',false);			
						op = $.parseJSON(jqXHR.responseText);										
						$(form).before('<div class="alert alert-danger">'+op.msg+'</div>');
					},
					beforeSend: function () {
						$('.alert,.help-block').remove();
						$('input.form-control',frmObj).prop('disabled',true);
						$('button[type=submit]',frmObj).prop('disabled',true);
						
						frmObj.before("<div class='alert alert-info'>Authenticating...</div>").fadeIn('slow');
					},
					success: function (op) {
						$('.alert').remove();
						if (op.status == 200) {
						//	frmObj.before("<div class='alert alert-success'>Login successful. Redirecting...</div>");
							frmObj.before("<div class='alert alert-success'>Successfully Logged In</div>");
							if ($('#user_remember_me').is(':checked')) {
								// save username and password
								localStorage.usrname = $('#loginfrm #uname').val();
								localStorage.pass = $('#loginfrm #password').val();
								localStorage.chkbx = $('#loginfrm #user_remember_me').val();
							}
							window.location.href = op.url;
						} else {
							$('input.form-control',frmObj).prop('disabled',false);
							$('button[type=submit]',frmObj).prop('disabled',false);
							frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
							frmObj.reset;
						}
					}
				});	
			}
		}
	});
	/*
	$(document).on('submit','#loginfrmssd',function (e) {
		e.preventDefault();	
		var frmObj = $(this);
		$.ajax({
			type: 'POST',
			url: frmObj.attr('action'),
			data: frmObj.serialize(),
			dataType: 'json',
			error: function (jqXHR, textStatus, errorThrown) {
				$('.alert').remove();
				$('input.form-control',frmObj).prop('disabled',false);
				$('button[type=submit]',frmObj).prop('disabled',false);			
				responseText = $.parseJSON(jqXHR.responseText);
				$.each(responseText.errs,function(fld,msg){
					if($('.signin input[name='+fld+']').parent().hasClass('input-group')){
						$('.signin input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
					} else {
						$('.signin input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
					}
				});
			},
			beforeSend: function () {
				$('.alert,.help-block').remove();
				$('input.form-control',frmObj).prop('disabled',true);
				$('button[type=submit]',frmObj).prop('disabled',true);
				frmObj.before("<div class='alert alert-info'>Authenticating...</div>").fadeIn('slow');
			},
			success: function (op) {
				$('.alert').remove();
				if (op.status == 'ok') {
					frmObj.before("<div class='alert alert-success'>Login successful. Redirecting...</div>");
					window.location.href = op.url;
				} else if (op.status == 'fail') {
					$('input.form-control',frmObj).prop('disabled',false);
					$('button[type=submit]',frmObj).prop('disabled',false);
					frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
					frmObj.reset;
				}
			}
		});		
	});
	*/


	$("#forgotfrm").validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		rules:{
			uname_id:{
				required:true,
				email:true
			},
			} ,
		messages: {
			uname_id : {
				required : "Enter a valid email address",
				email : "Enter a valid email address"
			},		
		},	
		submitHandler: function (form, event) {
			event.preventDefault();
			if ($(form).valid()) {
				var frmObj = $('#forgotfrm');
				$('.alert,.help-block').remove();
				CURFORM = frmObj;
				$.ajax({
					type: 'POST',
					url: frmObj.attr('action'),
					data: frmObj.serialize(),
					dataType: 'json',
					error: function (jqXHR, textStatus, errorThrown) {
						$('.alert').remove();
						$('#topForgotBtn',frmObj).fadeIn('slow').attr('disabled', false);
						responseText = $.parseJSON(jqXHR.responseText);
						$.each(responseText.errs,function(fld,msg){
							if($('.forgot input[name='+fld+']').parent().hasClass('input-group')){
								$('.forgot input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
							} else {
								$('.forgot input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
							}
						});
					},
					beforeSend: function () {
						$('#topForgotBtn').attr('disabled', 'disabled');
						frmObj.before("<div class='alert alert-info'>Authenticating...</div>").fadeIn('slow');
					},
					success: function (op) {
						$('.forgot .alert').remove();
						if (op.status == 200) {
							frmObj.fadeOut('fast');
						/* 	frmObj.before("<div class='alert alert-success'>"+op.msg+"</div>"); */
							//window.location.href = op.url;
						} else if (op.status == 'fail') {
							frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
							$('.forgot #uname').val("");
						}
					}
				});
			}
		}
	});

	$(document).on('click','.btn-forgot',function (e) {
		e.preventDefault();
		$('.signin').fadeOut('fast',function(){
			$('.forgot').show().fadeIn('slow');
			$('.signin div.help-block').remove();
		});
	});

	$(document).on('click','.backtoLogin',function (e) {
		e.preventDefault();	
		$('.forgot').fadeOut('fast',function(){
			$('.signin').fadeIn('slow');
			$('.forgot div.help-block').remove();
			$('.login-box.forgot .alert').remove();
			$('#forgotfrm #uname_id').val('');
			$('#forgotfrm').show();
		});
	});

	$('.pwdHS').on('click', function (e) {	
		var x = $(this).siblings('input').attr('type');
		if (x === 'password') {
			$(this).siblings('input').attr('type', 'text');
			$(this).find('i').attr('class','').attr('class','fa fa-eye');
		} else {
			$(this).siblings('input').attr('type', 'password');
			$(this).find('i').attr('class','').attr('class','fa fa-eye-slash');
		}
	});


	$("#password-resetfrm").validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		rules:{
			newpassword:{
				required:true,
				minlength:6,
			},
			confirmpassword: {
				required:true,
				minlength:6,
				equalTo: "#newpassword"
			}
		} ,
		messages: {
			newpassword : {
				required : "Password could not be empty",
				minlength : "Password atleast should contain 6 char long"
			},
			confirmpassword: {
				required : "Password could not be empty",
				minlength : "Password atleast should contain 6 char long",
				equalTo : "Your new password cannot be same as current password"
			}
		},	
		errorPlacement: function (error, element) {
			if (element.data("err-msg-to") != "")
				error.appendTo(element.data("err-msg-to"));
			else
				error.insertAfter(element);
		},
		submitHandler: function (form, event) {
			event.preventDefault();
			if ($(form).valid()) {
				var frmObj = CURFORM = $(form);
				$.ajax({
					type: 'POST',
					url: frmObj.attr('action'),
					data: frmObj.serialize(),
					dataType: 'json',				
					beforeSend: function () {
						$('button[type=submit]',frmObj).prop('disabled',true);
					},
					success: function (op) {
						$('#password-resetfrm').fadeOut('fast');
					},
					error :function(){
					    $('button[type=submit]',frmObj).prop('disabled',false);
					}
				});	
			}
		}
	});
	/* function RestrictSpace(evt) {
    if (event.keyCode == 32) {
        return false;
    }
  } */
	$('.show-pass').click(function(e){
		e.preventDefault();
		$("#password").attr("type", "text");
		$(this).css('display','none')
		$('.hide-pass').css('display','block')
	})
	$('.hide-pass').click(function(e){
		e.preventDefault();
		$("#password").attr("type", "password");
		$(this).css('display','none')
		$('.show-pass').css('display','block')		
	})
});