$(document).ready(function () {
    var regtoken='';
	var loginfrm = $('#loginfrm');	
    var signupfrm= $('#signupfrm');
    var forgot_pwd= $('#forgot_pwd');
    var varification= $('#varification');
		
   /*  signupfrm.on('click', '.send-verification-link', function (e) {
        e.preventDefault();
        var Curele=$(this);
        CURFORM=signupfrm;
        $.ajax({
            url: Curele.data('url'),
            data: {mobile: $('#mobile', signupfrm).val()},
            success: function (op) {
                Curele.hide();
                $('.signup-verification', signupfrm).show();
            },
            error: function (jqXHR, exception) {
                Curele.show();
                $('.signup-verification', signupfrm).hide();
            }
        });
    });
    signupfrm.on('submit', function (e) {
        e.preventDefault();
        CURFORM=signupfrm;
        $.ajax({
            url: signupfrm.attr('action'),
            data: signupfrm.serialize(),
        });
    }); */
	
	
	/* Login */
   /*  
    loginfrm.on('submit', function (e) {
        e.preventDefault();
        $('.alert').remove();
        CURFORM = $('#loginfrm');					
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serialize(),
            beforeSend: function () {
                $('#login_button').html('Processing...').attr('disabled', 'disabled');
                $('.errmsg_yellow').text('');
            },
            success: function (op) {
				console.log(op); return false;
				
                if ($('#redirect_to_support').val()!='')
                {
                    window.location.href=$('#redirect_to_support').val();
                }
                else
                {
                    $('#login_button').html('LOG IN').attr('disabled', false);
                    window.location.href=op.url;
                }
            },
            error: function (jqXHR, exception) {
                $('#login_button').html('LOG IN').attr('disabled', false);
            }
        });
    });	 */
		//{{url('http://localhost/paygyft/api/v1/user/login')}}


 signupfrm.on('click', '#register', function (e) {


        e.preventDefault();
        var Curele=$(this);
        CURFORM=$('#signupfrm');
        $.ajax({
            url: $('#signupfrm').attr('action'),
            data: $('#signupfrm').serialize(),
            dataType:'json',
            success: function (op) {
                console.log(op);

                  regtoken = op.regtoken;
                  $('#signupfrm').css("display", "none");
                  $('#varification').css("display", "block");
                 

            }
        });
    });
 // signupfrm.on('change', '#country', function (e) {
 //        e.preventDefault();

 //         url= $('option:selected', this).attr('data_url');
 //         code= $('option:selected', this).attr('data_code');
 //         $('#f_img').attr('src',url);
 //         $('#phonecode').text(code);

 //      });


 varification.on('click', '#resendotp', function (e) {
   
       e.preventDefault();
        var Curele=$(this);
        CURFORM=$('#varification');
        $.ajax({
            headers:{
                'regtoken':regtoken,
            },
            url: $('#resendotp').attr('ctr_url'),
            data: $('#varification').serialize(),
            success: function (op) {

                if(op.status == 200){
                    $('#signupfrom').css("display", "none");
                 
                    //$('#msg_div').css("display", "none").html('<div class="alert alert-success">Virob account successfully created.</div>');
                }
                 
            }
                
        });
    });

varification.on('click', '#varify_otp', function (e) {
    // alert($('#varification').attr('action'));
        e.preventDefault();
        var Curele=$(this);
        CURFORM=$('#varification');
        $.ajax({
            headers:{
                'regtoken':regtoken,
            },
            url: $('#varification').attr('action'),
            data: $('#varification').serialize(),
            success: function (op) {
                if(op.status == 200){


                    alert('okok');
                    // $('#signupfrom').css("display", "none");
                    // $('#varification').css("display", "none");
                    //$('#msg_div').css("display", "none").html('<div class="alert alert-success">Virob account successfully created.</div>');
                    setTimeout(function(){ window.location.href = op.url; }, 2000);                 
                }
            }                
        });
    });


	
	/* Check Login */	

	loginfrm.validate({


        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {

                error.insertAfter(element);
            }
        },
      /*  rules: {
            username: {required: true},
            password: {required: true},           
        },
*/      //  messages: $login_val_message,
        submitHandler: function (form, event) {
            event.preventDefault();		            
            if ($(form).valid()) {				
				CURFORM = loginfrm;	
               
               
				$.ajax({
					url: CURFORM.attr('action'),					
					data: CURFORM.serialize(),
					beforeSend: function () {                        
						$('#login_button').html('Processing...').attr('disabled', 'disabled');
						$('.errmsg_yellow').text('');
					},
					success: function (op) {
                         console.log(op);
                         USRTOKEN = op.token;
                         if(op.status==200){
                             $('#username').val('');
                             $('#password').val('');
                             $('.alert alert-success alert-err').remove();                             
                             $('.modal').modal('hide');
                             window.location.href=op.url;

                         }                        
						//console.log(USRTOKEN);
                        console.log(location.BASE);
						//window.location.href = location.BASE;
					/* 	if ($('#redirect_to_support').val()!='')
						{
							window.location.href=$('#redirect_to_support').val();
						}
						else
						{
							$('#login_button').html('LOG IN').attr('disabled', false);
							window.location.href=op.url;
						} */
					},
					error: function (jqXHR, exception) {

                    				
						//$('#loginfrm button[type="submit"]').attr('disabled', true);
					}
				});
            }
        }
    });	
	
	/*  Forgot password  */
	forgot_pwd.on('click', '.send-verification-link', function (e) {
        e.preventDefault();		
        var CURELE = $(this);		
        CURFORM = forgot_pwd;	
        $.ajax({
            url: CURELE.data('url'),
            data:{uname:$('#username',CURFORM).val()},    
            success: function (op) {			
			    $('.resend-link',forgot_pwd).attr('data-uname', $('#username',CURFORM).val());
                $('.send-verification', forgot_pwd).hide();		
                $('.send-link-success,.resend-link', forgot_pwd).show();
				$('#forgot_pwd_msg', forgot_pwd).text(op.msg);
				CURFORM = '';
            },
            error: function (jqXHR, exception) {
                $('.send-verification', forgot_pwd).show();
				$('.send-link-success,.resend-link', forgot_pwd).hide();              
            }
        });
    });
	
	/*  Resend forgot password  */
	$('.resend-link',forgot_pwd).on('click',function(e){		
		e.preventDefault();		
		var CURELE = $(this);	
		$.ajax({
            url: $('.send-verification-link',forgot_pwd).data('url'), 
            data:{uname: CURELE.attr('data-uname')},   
            success: function (op) {						
                $('.send-verification', forgot_pwd).hide();		
                $('.send-link-success,.resend-link', forgot_pwd).show();	
				CURFORM = '';
            },
            error: function (jqXHR, exception) {
                $('.send-verification', forgot_pwd).show();
				$('.send-link-success,.resend-link', forgot_pwd).hide();              
            }
        });
	});
	
	
	/* forgot pwd */
   /*  loginfrm.on('click', '#forgot_password', function (e) {
        e.preventDefault();
        $('.alert').remove();
        loginfrm.hide();
        forgot_pwd.show();
    }); */
	
  /*   forgot_pwd.on('click', '#login', function (e) {
        e.preventDefault();
        loginfrm.show();
        forgot_pwd.hide();
    }); */
			
	/* $('.resend-link',forgot_pwd).on('click',function(e){		
		e.preventDefault();		
		$('#forgot_pwd .send-verification-link').trigger('click');		
	}); */
	
	/* Reset Pwd */
   /*  forgot_pwd.on('submit', function (e) {
        e.preventDefault();		
        CURFORM=forgot_pwd;
        $.ajax({
            url: forgot_pwd.attr('action'),
			headers: {token:$('#forgot_pwd #token').val()},
            data: forgot_pwd.serializeObject(),
            success: function (op) {				
				$('input','#loginfrm,#forgot_pwd').val('');
                $('#loginfrm').show();
                $('#forgot_pwd').hide();
				CURFORM.data('errmsg-fld','#loginfrm');
                CURFORM.data('errmsg-placement','before');
            }
        });
    }); */
		
});