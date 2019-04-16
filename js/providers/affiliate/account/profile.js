var $email_validate_message={email:'',verify_code:''};
$(document).ready(function () {
    var PROF = $('#profile_editfrm');
	
	$('#add_prof_image').click(function (e) {
		e.preventDefault();
		$('#my-account').fadeOut('fast',function(){		    
			//$('#image_upload').fadeIn('slow');			
		});
		$('#profile_image_form #attachment').trigger('click');
	    $("#image_upload").show(); 
    });
	
	
	$('#image_upload .back_btn').click(function (e) {
		e.preventDefault();
		$('#image_upload').fadeOut('fast',function(){
		    $('#image_upload_show').attr('src',$('#image-holder').data('original'));
		    $('.uneditable-input').hide();
            $('.uneditable-input span').text('');
            $('#update_profile_image').attr('disabled', 'disabled');
		    $('.fileupload-exists').attr('disabled',true);
		    $('#my-account').fadeIn('slow');
		});
	});	
	
	/*$(document).on('click','#profile_edit_btn',function (e) {
		e.preventDefault();		
		$('#my_profile,#location').fadeOut('fast',function(){		    
			$('#profile_edit').fadeIn('slow');
		});
	});

	$(document).on('click','#profile_edit #back_btn',function (e) {
		$('#profile_edit').fadeOut('fast',function(){		    
			$('#my_profile').fadeIn('slow');
		});
	});	
	
	$(document).on('click','#profilefrmBtn',function (e) {
		PROF.submit();
	});
	
	PROF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = PROF;		
        $.ajax({
            url: PROF.attr('action'),
            data: PROF.serialize(),
			dataType:'JSON',
            beforeSend: function () {
                $('#profilefrmBtn').attr('disabled', true);
            },
            success: function (op) {
				CURFORM.before('<div class="alert alert-'+op.msgClass+'">'+op.msg+'</div>')
				$('#profilefrmBtn').removeAttr('disabled', false);
				if(op.status == 200){
					if(op.reload){
						window.location.reload();
					}
				} 
            },
			error: function (event, xhr, settings) {			
				$('#profilefrmBtn').removeAttr('disabled', false);
				$('.alert').fadeOut(7000);
			}
        });
    });
	*/	
	
	var PIF = $('#profile_image_form');
	
	/* Set Default Image */
	if($('#attachment-preview', PIF).attr('data-old-image') != ''){
	    $('input', PIF).attr('data-default',$('#attachment-preview', PIF).attr('data-old-image'));
	}
	$('#partner_form_sbt, #prof-image-remove', PIF).attr('disabled', true);
	$(document).on('submit', '#partner_form', function (e) {
        e.preventDefault();
        var frmObj = $(this);
        CURFORM = frmObj;
        $.ajax({
            url: frmObj.attr('action'),
            data: frmObj.serialize(),
            success: function (op) {
                console.log(op);
				$('div.alert').remove();								             
				$('#partner_form').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
                $('div.alert').fadeOut(8000);
            },
			error: function (event, xhr, settings) {
				var op = event.responseJSON;
				$('div.alert').remove();
				$('#partner_form').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
				$('div.alert').fadeOut(8000);
			}
        });
    });
    PIF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = PIF;
		var data = new FormData();
        if (CROPPED) {
            data.append('attachment', uploadImageFormat($('#attachment-preview').attr('src')));
			CROPPED=false;
        }
        $.each(CURFORM.serializeObject(), function (k, v) {
            data.append(k, v);
        });
		$.ajax({
            url: CURFORM.attr('action'),
            data: data,
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function (data) {				
                $('#attachment-preview,img.profile-user-img-edit').prop('src', data.profile);
                $('#partner_form_sbt, #prof-image-remove', PIF).attr('disabled', true);
				$('#image_upload .back_btn').trigger('click');
            },
			error: function (event, xhr, settings) {
				var op = event.responseJSON;
				$('div.alert').remove();
				PIF.before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
				$('div.alert').fadeOut(8000);
			}
        });        
    });
	
    PIF.on('change', '#attachment', function (e) {
        e.preventDefault();
        if ($('#attachment', PIF).val() == '') {
            $('#partner_form_sbt, #prof-image-remove', PIF).attr('disabled', true);
        } else {
            $('#partner_form_sbt, #prof-image-remove', PIF).attr('disabled', false);
        }
    });
	
    PIF.on('click', '#prof-image-remove', function (e) {
        e.preventDefault();
		$('#attachment').val('');
        if ($('#attachment-preview', PIF).attr('data-old-image') != '') {
            $('#attachment-preview', PIF).attr('src', $('#attachment-preview', PIF).attr('data-old-image'));
        } else {
            $('#attachment-preview', PIF).attr('src', $('input', PIF).attr('data-default'));
        }
        $('#partner_form_sbt, #prof-image-remove', PIF).attr('disabled', true);
    });	
	
	
	$(document).on('click', '#changeunameBtn', function (e) {
		$('#profile_edit').fadeOut('fast',function(){		    
			$('#change_username').fadeIn('slow');
		});
	});
	
	$(document).on('click', '#change_uname_back_btn', function (e) {
		$('input[name=new_uname]').val('');
		$('#change_username').fadeOut('fast',function(){		    
			$('#profile_edit').fadeIn('slow');
		});
	});
	
	/*$(document).on('click','#personal-editBtn',function (e) {
        e.preventDefault();
		 $.ajax({
            url: $(this).data('url'),
			type:'GET',
            beforeSend: function (op) {                
				$('#personal-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){
				$('#personal-model .modal-body').html(op.template);
				$('#personal-model').modal('show');
			}	
        });		
	}); */
	
	/* Martial Status Modal */
	$(document).on('click','#personal-editBtn',function (e) {
        e.preventDefault();
		//alert($(this).data('url'));return false;
		 $.ajax({
            url: $(this).data('url'),
			type:'GET',
            beforeSend: function (op) {                
				$('#profile-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){
				$('#profile-model .modal-body').html(op.template);
				$('#profile-model').modal('show');
			}	
        });		
	});
	
	$(document).on('click', '#profileSaveBtn', function (e) {
		$('#profileUpdateFrm').submit();
	});
	
	$(document).on('submit','#profileUpdateFrm',function (e) {		
        e.preventDefault();		
        CURFORM = $(this);	
        $.ajax({
            url: CURFORM.attr('action'),  
            data: CURFORM.serialize(),			
			beforeSend:function(){
				$('#profileSaveBtn').attr('disabled',true);
				$('div.alert',CURFORM).empty();
				$('#account-details div.alert').remove();
				CURFORM.siblings('div.alert').remove();
			},
			success: function (op, textStatus, xhr) {
				$('#profileSaveBtn').attr('disabled',false);
				if(xhr.status == 200){	
					$('#profile-model').modal('hide');
					$('#account-details #marital_status').text(op.marital_status);
					$('#account-details #gardian').text(op.gardian);
					$('#account-details').prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}else{
				    $('#profile-model').modal('hide');
				    $('#account-details').prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					//$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
			},
			error: function (jqXHR, exception, op) {
				$('#profileSaveBtn').attr('disabled',false); 
				if(jqXHR.responseJSON.msg != undefined && jqXHR.responseJSON.msg != ''){
				    CURFORM.before('<div class="alert alert-danger">'+jqXHR.responseJSON.msg+'<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				}			
			},
        });
    });
		
	$(document).on('click','#nominee-editBtn',function (e) {
        e.preventDefault();
		 $.ajax({
            url: $(this).data('url'),
			type:'GET',
            beforeSend: function (op) {                
				$('#nominee-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){
				$('#nominee-model .modal-body').html(op.template);
				$('#nomineeFrm').selectDOB({dob:op.nominee.dob});
				$('#nominee-model').modal('show');
			}	
        });		
	});
 	 $(document).on('keypress',"#fullname",function(e) {
		/*  alert($(this).val().length);  */
         if (e.which === 32 && !($(this).val().length))
        e.preventDefault();  
     });  
	 
	/* $("#fullname").on("keypress", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
     }); */
	$(document).on('submit', '#change_unamefrm', function (e) {
        e.preventDefault();
        var frmObj = $(this);
        CURFORM = frmObj;
        $.ajax({
            url: frmObj.attr('action'),
            data: frmObj.serialize(),			
            beforeSend: function (op) {                
				$('.alert').remove();								             				
            },
			success:function(op){				
				$('#change_unamefrm').before('<div class="col-sm-12 alert alert-success">' + op.msg + '</div>');
				window.location.reload();
			}		
        });
    });

	$(document).on('click', '#nomineeSaveBtn', function (e) {
		$('#nomineeFrm').submit();		
	});

	$(document).on('submit', '#nomineeFrm', function (e) {
        e.preventDefault();
        var frmObj = $(this);
        CURFORM = frmObj;
        $.ajax({
            url: frmObj.attr('action'),
            data: frmObj.serialize(),			
            beforeSend: function (op) {                
				$('#nomineeSaveBtn').removeAttr('disabled', true);
            },
			success:function(op){				
				 /* frmObj.before('<div class="alert alert-success">'+op.msg+'</div>');  */
				if(op.status==200 && !isEmpty(op.nominee)){
					$('#nominee-model').modal('hide');
					$('#nominee-info #fullname').text(op.nominee.fullname);
					$('#nominee-info #dob').text(op.nominee.gender+' / '+op.nominee.dob);
					$('#nominee-info #relation_ship').text(op.nominee.relation_ship);
				 	 CURFORM.data('errmsg-fld','#account-details');
	                 CURFORM.data('errmsg-placement','prepend');
				}
			},
			error: function (event, xhr, settings) {			
				/* frmObj.before('<div class="alert alert-warning">'+op.msg+'</div>'); */
				$('#nomineeSaveBtn').removeAttr('disabled', false);
			}
        });
    });		
	
	$(document).on('click','.editAddressBtn',function (e) {
        e.preventDefault();
		var address_head = $(this).data('heading');
		$.ajax({
            url: $(this).data('url'),
			type:'GET',
            beforeSend: function (op) {                
				$('#address-model .modal-title span').text(address_head);
				$('#address-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){
				$('#address-model .modal-body').html(op.template);		
                $('#addressFrm #postal_code').trigger('change');					
				$('#address-model').modal('show');
			}	
        });		
	});	
	
	$(document).on('change','#addressFrm #postal_code', function () {
		var $addFld = $('#addressFrm');
        var pincode = $('#postal_code',$addFld).val();
        var country_id = $('#addressFrm #country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				beforeSend:function(){
					$('#city_id',$addFld).empty();
				},
				success: function (OP) {
					$('#state_id, #city_id',$addFld).prop('disabled', false).empty();	
					var city_id = $('#city_id',$addFld).data('selected');
					var state_id = $('#state_id',$addFld).data('selected');					
					$('#state_id',$addFld).append($('<option>', {value: OP.state_id,selected:'selected'}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						/* console.log(city_id==e.id,city_id,e.id); */
						$('#city_id',$addFld).append($('<option>', $.extend(city_id==e.id?{selected:'selected'}:{},{value: e.id})).text(e.text));
					});
					$('.cityFld,.stateFld',$addFld).removeClass('hidden');
				},
				error: function () {
					$('#state_id, #city_id',$addFld).empty().prop('disabled', true);									
				}
			});
		}	
    });	
	
	$(document).on('click', '#addressSaveBtn', function (e) {
		$('#addressFrm').submit();
	});
	
	$(document).on('submit','#addressFrm',function (e) {		
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: CURFORM.serialize(),			
			beforeSend:function(){				
				/* $('#addressSaveBtn').attr('disabled',true); */
			},
			success: function (op, textStatus, xhr) {
				$('#addressSaveBtn').attr('disabled',false);
				if(xhr.status == 200){
					if(op.addtype!='' && op.address){						
						$('#'+op.addtype+'Addr').text(op.address);
					}
					$('#account-details').prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					$('#address-model').modal('hide');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
			},
			/*  error: function (jqXHR, exception, op) {
				$('#addressSaveBtn').attr('disabled',false);
				CURFORM.append('<div class="alert alert-danger">'+jqXHR.responseJSON.msg+'</div>');				 	
			},  */
        });
    });

	$(document).on('click','#changeEmailBtn', function (e) {
		e.preventDefault();
		CURFORM = $("#primary_contacts_editfrm");
        $.ajax({
			url: $(this).data('url'),
			dataType: 'JSON',
			type: 'POST',			
		});
    });
	
	$(document).on('click','#changeMobileButn', function (e) {
		e.preventDefault();
		CURFORM = $("#primary_contacts_editfrm");
        $.ajax({
			url: $(this).attr('data-url'),
			dataType: 'JSON',
			type: 'POST',		
		});
    });
	
	$(document).on('click','#verifyEmailBtn', function (e) {
		e.preventDefault();
		CURFORM = $("#primary_contacts_editfrm");
        $.ajax({
			url: $(this).data('url'),
			dataType: 'JSON',
			type: 'POST',			
		});
    });
	
	$(document).on('click','#verifyMobileBtn', function (e) {
		e.preventDefault();
		CURFORM = $("#primary_contacts_editfrm");
        $.ajax({
			url: $(this).data('url'),
			dataType: 'JSON',
			type: 'POST',
			success:function(){
				CURFORM = $('#verifymobile_otpform');
				$('#verify-mobile-model').modal('show');        
			}
		});
    });
	
	/*$(document).on('click','#changeMobileBtn', function (e) {
		e.preventDefault();
        $('#change-mobile-model').modal('show');
    });*/
	
	$(document).on('click','#update_contacts', function (e) {
	   e.preventDefault();
	   CURFORM = $("#profile_editfrm");
        $.ajax({
		    url: $(this).data('url'),
			data:{'office_phone':$('#office_phone').val(),'home_phone':$('#home_phone').val()},
			dataType: 'JSON',
			type: 'POST',			
			success: function (data) {               		
				 if(data.status == 208){
					$('#profile_editfrm').before('<div class="alert alert-warning alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>');				
				}	  
			}
		});
	});
	/*
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
        //messages : $email_validate_message.email,
        submitHandler: function (form, event) {
            event.preventDefault();
			if (CEF.valid()) {
				$.ajax({
					url: CEF.attr('action'),
					data: CEF.serialize(),
					dataType: 'JSON',
					type: 'POST',
					beforeSend: function () {
						
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
    });*/
    $('input').bind('cut copy paste', function (e) {
        e.preventDefault();
    });	    	

	/* Code_Verification_Form */
    var CVF_OTP = $('#change-email-verify-form');
    CVF_OTP.validate({        
		errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
		rules: {
			 email: {
				required: true,
				email: true,
				maxlength:40
			},
			tpin: {
				required: true,
				digits: true,
				maxlength:4,
				minlength:4			
			}
		},
		messages : $email_validate_message, 
		 errorPlacement: function (error, element) {
			if (element.data("err-msg-to") != "")				
				error.appendTo(element.data("err-msg-to"));
			else
				error.insertAfter(element);
		}, 
		submitHandler: function (form, event) {
            event.preventDefault();
            if (CVF_OTP.valid()) {
				CURFORM = CVF_OTP;
				$.ajax({
					url: CVF_OTP.attr('action'),
					data: CVF_OTP.serialize(),
					dataType: 'JSON',
					type: 'POST',
					beforeSend: function () {
						$('#verification_now',CVF_OTP).attr('disabled',true).hide();						
					}, 
					error: function (jqXHR, textStatus, errorThrown) {	
						$('#verification_now',CVF_OTP).attr('disabled',false).show();
						responseText = $.parseJSON(jqXHR.responseText);						
					}, 
					success: function (op){ 
						if(op.status==200) {
							CURFORM.data('errmsg-fld','#verify-otp-form');
							$('#change-email-verify-form').fadeOut('fast',function(){
								$('#verify-otp-form').fadeIn('slow');
							});
						}
						else {
							$('#verification_now',CVF_OTP).attr('disabled',false).show();							
						}							
					}
				});
			}
		}
	});	
	
	
    var VFO = $('#verify-otp-form');
    VFO.validate({
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
		messages : $email_validate_message, 
		submitHandler: function (form, event) {
            event.preventDefault();
			if (VFO.valid()) {
				$.ajax({
					url: VFO.attr('action'),
					data: VFO.serialize(),
					dataType: 'JSON',
					type: 'POST',					    
					beforeSend:function(){
						$('#update_email').attr('disabled',false);
					},
					success: function (data){ 							
						VFO.hide()
					}
				});
			}
        }
    });	
	
	
	$(document).on('click','#change-mobile-btn', function (e) {
		e.preventDefault();
        $('#change-mobile-form').trigger('submit');
    });
	
var CMF = $('#change-mobile-form');
    CMF.validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            mobile: {
                required: true,
                number: true,
				maxlength:10,
				minlength:10
            },
			tpin: {
				required: true,
				digits: true,
				maxlength:4,
				minlength:4			
			}
        },
       messages : $mobile_validate_message,
	    errorPlacement: function (error, element) {
			if (element.data("err-msg-to") != "")				
				error.appendTo(element.data("err-msg-to"));
			else
				error.insertAfter(element);
		},
		submitHandler: function (form, event) {
            event.preventDefault();
			
                if (CMF.valid()) {
						CURFORM = CMF;
                    $.ajax({
                        url: CMF.attr('action'),
                        data: CMF.serialize(),
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {                            
							$('#send_verify_code').attr('disabled','disabled');							
                        }, 
						error: function (jqXHR, textStatus, errorThrown) {
                            $('#send_verify_code').attr('disabled',false);							
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#change-mobile-form input[name='+fld+']').parent().hasClass('input-group')){
									$('#change-mobile-form input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
								} else {
									$('#change-mobile-form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
								return false;
							});
						},
                        success: function (op) {						    
						if(op.status==200) {
							CURFORM.data('errmsg-fld','#code_verify_form');
							$('#change-mobile-form').fadeOut('fast',function(){
								$('#code_verify_form').fadeIn('slow');
							});
						}
						else {
							$('#verification_now',CMF).attr('disabled',false).show();							
						}	
                        }
                    });
                }
        }
       
    });
	
	
	var CVF = $('#code_verify_form');
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
		messages : $mobile_validate_message, 
		submitHandler: function (form, event) {
            event.preventDefault();
                if (CVF.valid()) {
                    $.ajax({
                        url: CVF.attr('action'),
                        data: CVF.serialize(),
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {                            
							$('#update_mobile').attr('disabled','disabled');
							$('#verification_code').val("");							
                        }, 
					    error: function (jqXHR, textStatus, errorThrown) {	                            
                            $('#update_mobile').attr('disabled',false);							
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#code_verify_form input[name='+fld+']').siblings().hasClass('input-group')){
									$('#code_verify_form input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
								} else {
									$('#code_verify_form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
								return false;
							});
						}, 
                        success: function (data){ 
							CVF.hide()
						/*	$('#update_mobile').attr('disabled',false);
						    if(data.status == 200){
							    $('#crnt_number').val($('#mobile').val());
                                $('#code_verify_form').fadeOut('fast',function(){
									$('#change-mobile-form').fadeIn('slow');
									$('#verification_code').val("");
									$('#mobile').val("");
									
								});
                                $('#change-mobile-form').before($('<div >').attr({class: 'alert alert-success'}).html(data.msg)); 
                            }else{
								$('#change-mobile-form').before($('<div >').attr({class: 'alert alert-danger'}).html(data.msg)); 
                            }		*/
                        }
                    });
                }
        }
    });	

    $('#verification_code').on('focus', function () {
	    $('.alert ,div.help-block').remove();
    });
	
	$("#code_verify_form #mobile_no").click(function(event){
	   event.preventDefault();
		if (CMF.valid()) {
			$.ajax({
				url: CMF.attr('action'),
				data: CMF.serialize(),
				dataType: 'JSON',
				type: 'POST',
				beforeSend: function () {
					
					$('#send_verify_code').attr('disabled','disabled');
					
					
				}, 
				error: function (jqXHR, textStatus, errorThrown) {
                    
                    $('#send_verify_code').attr('disabled',false);					
					responseText = $.parseJSON(jqXHR.responseText);
					$.each(responseText.errs,function(fld,msg){
						if($('#change-mobile-form input[name='+fld+']').siblings().hasClass('input-group')){
							$('#change-mobile-form input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
						} else {
							$('#change-mobile-form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
						}
						return false;
					});
				},
				success: function (data) {
				    
					$('#send_verify_code').attr('disabled',false);
					if(data.status == 'ok'){
					    $('#change-mobile-form').fadeOut('fast',function(){
							$('#code_verify_form').fadeIn('slow');
							$('#code_msg').text(data.msg);
							$('#verification_code').val("");
						});	 
					}else{
						$('#change-mobile-form input[name="mobile"]').after("<div class='help-block'>"+data.msg+"</div>");
					}	
			    }
			});
		}
    });
	

	
	$(document).on('click','#code_verify_form #cancel',function (e) {
		e.preventDefault();
		$('#mobile').val("");
		$('#code_verify_form').fadeOut('fast',function(){
			$('#change-mobile-form').fadeIn('slow');
		});
	});
	
	var VMFO = $('#verifymobile_otpform');
    VMFO.validate({
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
		messages : $verify_code_validate_message, 
		submitHandler: function (form, event) {
            event.preventDefault();
			if (VMFO.valid()) {
				CURFORM = VMFO;
				$.ajax({
					url: VMFO.attr('action'),
					data: VMFO.serialize(),
					dataType: 'JSON',
					type: 'POST',					    
					beforeSend:function(){
						$('#verifymobile',VMFO).attr('disabled',false);
					},
					success: function (data){
						VMFO.resetForm();
						CURFORM = $('#primary_contacts_editfrm');
						$('#verifyMobBtnBlk .input-group-btn').remove();
						$('#verify-mobile-model').modal('hide');						
						$('#verifyMobBtnBlk').append('<span class="input-group-btn"><button title="Change Mobile" class="btn btn-success" type="button" data-url="'+data.url+'" id="changeMobileButn" ><i class="fa fa-edit"></i> Change</button></span>');						
					}
				});
			}
        }
    });	
	 
	 
	 

	/*
    $('#verify_code').on('focus', function () {
	    $('.alert ,div.help-block').remove();
    });
	
	$("#code_verification_form #email_id").click(function(event){
	   event.preventDefault();
		if (CEF.valid()) {
			$.ajax({
				url: CEF.attr('action'),
				data: CEF.serialize(),
				dataType: 'JSON',
				type: 'POST',
				beforeSend: function () {
					
					$('#send_verification_code').attr('disabled','disabled');
					
				}, 
				error: function (jqXHR, textStatus, errorThrown) {
                    
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
    });*/
	
});	