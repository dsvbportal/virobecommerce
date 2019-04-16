function referrel_user_check() {
	var referral_username = $("#referral_name").val();
	if (referral_username) {
		$.ajax({
			url: $("#referral_name").data('url'),
			type: "POST",
			data: {referral_username: $("#referral_name").val()},
			datatype: "json",
			success: function (data) {
				data = JSON.parse(data);
				if (data.status == 'ok') {
					$("#referral_id").val(data.referral_id);
					$("#referral_name").val($("#referral_name").val());
					$("#referral_user_avail_status").text(data.msg);
					$("#referral_user_avail_status").removeClass("help-block");
				} else if (data.status == 'error') {
					$("#referral_user_avail_status").text(data.msg);
					$("#referral_user_avail_status").addClass("help-block");
				}
			}
		});
	}
}

$(document).ready(function () {
    $("#mobile").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#errmsg").html("Enter Digits Only").show().fadeOut("slow");
            return false;
        }
    });
    /*------Referrel User Checking Function--------*/
    $("#referral_name").blur(function () {
        referrel_user_check();
    });	
	$("#franchiseeListFrm").on('click','#resetBtn',function (e) {
		e.preventDefault();
		$("#franchiseeListFrm").resetForm();
		DT.fnDraw();
    });	
	
    
    $("#save_user").click(function () {
        var check = 0;
        // Setup form validation on the #create_user element
        $("#create_user").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            // Specify the validation rules
            rules: {            
				company_name: {
					required: true,              
				},				
				company_address: {
					required: true,             
				},
				first_name: {
					required: true,
					alphanumeric: true,
					minlength: 3,
					maxlength: 15
				},				
				first_name: {
					required: true,
					alphanumeric: true,
					minlength: 3,
					maxlength: 15
				},
				last_name: {
					required: true,
					alphanumeric: true,
					minlength: 3,
					maxlength: 15
				},
				uname: {
					required: true,
					alphanumeric: true,
					minlength: 6,
					maxlength: 15
				},
				email: {
					required: true,
					email: true,
				},
				mobile: {
					digits: true,
					minlength: 9,
					maxlength: 16,
				},
				password: {
					required: true,
					minlength: 5,
					maxlength: 20
				},
				tpin: {
					required: true,
					minlength: 5,
					maxlength: 20
				},
				currency: "required",
				state : "required",
				country: "required",
				zipcode: {
					required: true,
					 maxlength: 8,
					 minlength: 6
				},
				address : "required",
				city : "required"
			},
            // Specify the validation error messages
            messages: {
               	company_name: {
                    required: "Please Enter Support Center Name",                
                },
				company_address: {
                    required: "Please Enter Support Center Address",                  
                },
             	first_name: {
                    required: "Please Enter First Name .",
                   minlength: "First name must be greater then 3 characters",
                    maxlength: "First name must be less then 16 characters"
                },
				last_name: {
                    required: "Please Enter Last Name .",
                    minlength: "Last name must be greater then 3 characters",
                    maxlength: "Last name nust be less then 16 characters"
                },
				uname: {
                     required: "Please Enter User Name.",
                    minlength: "User name must be greater then 6 characters",
                    maxlength: "User name must be less then 16 characters"
                },
                email: {
                    required: "Please provide a valid email.",
                    email: "Please provide a valid email."
                },
                mobile: {
                    digits: "Invalid phone number !! Only numbers allowed",
                    required: "Please provide a valid Phone Number .",
                    maxlength: "Please provide a valid Phone Number",
                    minlength: "Please provide a valid Phone Number"
                },
            	password: {
                     required: "Please enter the Password",
                    minlength: "Password must be greater then 5 characters",
                    maxlength: "Password must be less then 20 characters"
                },
				tpin: {
                    required: "Please enter Security Password",
                    minlength: "Secutiry Password must be greater then 5 characters",
                    maxlength: "Secutiry Password be less then 20 characters"
                },
                currency: "Please select currency",
                country: "Please select country",
				state : "Please select state",
                zipcode: {
					required: "Please enter zip code",
					minlength: "Please Provide Valide zipcode",
                    maxlength: "Please Provide Valide zipcode"
				},
				address : "Please enter address",
				city : "Please enter city"
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var user_role = $("#user_role").val();
                    if (user_role != 1) {
                        var refname = $("#referral_name").val();
                        var refid = $("#referral_id").val();
                        if (refname == '' || refid == '' || refid == 0) {
                            alert("Please enter the Valid Referral User Name.");
                            return false;
                        }
                    }
                    var datastring = $(form).serialize();
                    $.ajax({
                        url: $(form).attr('action') + "/franchisee", // Url to which the request is send
                        type: "POST", // Type of request to be send, called as method
                        data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                        datatype: "json",
                        success: function (data) 	// A function to be called if request succeeds
                        {
                            data = JSON.parse(data);
                            $(".box-body").html(data.msg);
                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
                }
            }
        });
    });
	
    $(".tabval").each(function () {
        $(this).click(function () {
            $("#user_role").val($(this).val());
        });
    });
	
	$("#country").change(function () {
        var country_id = $("#country").val();
		$("#city").val('');
		$("#city option").css('display','none');
        $("#state").html('<option value="">Loading...</option>');
		$.post("get_states",{country_id :country_id },function(data){		
				var stateOpt = '<option value="">--Select State--</option>';																   
				stateOpt += data.statelist;	
				$('#state').html(stateOpt);
		},'json');
        $("#phonecode").val($("#phonecode #c_" + country_id).text());
    });
	
	$("#state").change(function () {
        var state_id = $("#state").val();
		if(state_id !=''){
			$("#city").val('');
			$("#city option").attr('hidden', true).attr('disabled', true);
			$("#city option").css('display','none');
			$('#city option[value=""]').removeAttr('hidden').removeAttr('disabled').attr('selected', true);
			$("#city .c_" + state_id).removeAttr('hidden').removeAttr('disabled').css('display','block');
		}else{
			$("#city").val('');
			$("#city option").css('display','none');
		}
    });
	 
	$(".change_pass_info").click(function (e) {
		e.preventDefault();
		$.post('admin/change_password_users', {user_id: $(this).attr('data')}, function (data) {
			$("#view_user_profile .modal-body").html(data);
			$("#view_user_profile .modal-title").html("Change Password");
			$("#view_user_profile").modal();
		})
	});
	
	$(".change_tpin_info").click(function (e) {
		e.preventDefault();
		$.post('admin/change_tpin_users', {user_id: $(this).attr('data')}, function (data) {
			$("#view_user_profile .modal-body").html(data);
			$("#view_user_profile .modal-title").html("Change PIN");
			$("#view_user_profile").modal();
		})
	});
	
	$(".edit_info").click(function (e) {
		e.preventDefault();
		$("#view_user_profile .modal-title").html("Edit Profile of Channel Partner");
		$("#view_user_profile .modal-body").html("Loading....");
		$("#view_user_profile").modal();
		$.post($(this).attr('href'), {uname: $(this).attr('data')}, function (data) {
			$("#view_user_profile .modal-body").html(data.content);
		}, 'json');
	});
	
	$("#users-list-panel").on('click',".edit_access",function (e) {
		e.preventDefault();
		$("#view_user_profile .modal-title").html("Edit Channel Partner Access");
		$("#view_user_profile .modal-body").html("Loading....");
		$("#view_user_profile").modal();
		$.post($(this).attr('href'), {uname: $(this).attr('data')}, function (data) {
			$("#view_user_profile .modal-body").html(data.content);
		}, 'json');
	});
	$(".status_btn").click(function (e) {
		e.preventDefault();
		$curLine = $(this);
		if (confirm('Are you sure you want to ' + $curLine.text() + '?')) {
			$.ajax({
				method: "POST",
				data: {'status': $curLine.attr('data-status'), 'block': $curLine.attr('data-block')},
				dataType: "json",
				url: $curLine.attr('href'),
				success: function (data) {
					if (data.status == 'ok')
					{
						$('#' + $curLine.attr('data-id')).html(data.label);
						$curLine.html(data.button_status);
						if (data.button_status == 'Block')
							$curLine.attr('data-block', '0');
						else
							$curLine.attr('data-block', '1');
					}
				}
			});
		}
	});
	$(".login_status").click(function (e) {
		e.preventDefault();
		$curLine = $(this);
		if (confirm('Are you sure you want to ' + $curLine.text() + '?')) {
			$.ajax({
				method: "POST",
				data: {'status': $curLine.attr('data-status'), 'login_block': $curLine.attr('data-block')},
				dataType: "json",
				url: $curLine.attr('href'),
				success: function (data) {
					if (data.status == 'ok')
					{
						$('#' + $curLine.attr('data-id')).html(data.label);
						$curLine.html(data.button_status);
						if (data.button_status == 'Block Login')
							$curLine.attr('data-block', '0');
						else
							$curLine.attr('data-block', '1');
					}
				}
			});
		}
	});	
	
	var t = $('#franchiseeList');
	var dataList = new Array();
    var DT = $('#franchiseeList').dataTable({
		"ordering": false,
        ajax: {
            url: $('#franchiseeListFrm').attr('action'),
            data: function (d) {
                return $.extend({}, d, $('input,select', '#franchiseeListFrm').serializeObject());
            }
        },
        columns: [
            {
                data: 'signedup_on',
                name: 'signedup_on',
                width: '10%',
                class: 'text-left',                
            }, 
			{
                data: 'company_name',
                name: 'company_name',
                class: 'text-left',                
            }, 			
			{                
                name: 'franchisee_type_name',
                class: 'text-left',
				render: function (data, type, row, meta) {
                    var str = row.franchisee_type_name;
					if(row.access_exist==0){
						str = str + ' <span class="glyphicon glyphicon-warning-sign text-danger" title="Locations are not set"></span>';
					}
					return str;
                }
            },			
         	{
                data: 'country',
                name: 'country',
                class: 'text-left'
            },			
			{
                name: 'uname',
                class: 'text-left',
				render: function (data, type, row, meta) {
                    return row.full_name +' <span class="text-muted">('+row.user_code+')</span>';
                }

            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return '<span class="label label-' + row.statusDispClass + '">' + row.status + '</span>';
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            }
        ],
		fnDrawCallback: function(settings){
			dataList = settings.json.data;
		},
		rowCallback: function( row, rdata ) {		
		  $(row).attr('data-url',rdata.account_id);
		},        
    });
	

	$(document).on('click', 'input[name=search]', function (e) {
		e.preventDefault();
        DT.fnDraw();
    });
	
    $('#franchiseeList').on('click', '.change_password', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#update_member_pwdfrm').trigger('click');
        $('#uname_label').val(CurEle.data('code'));
        $('#uname_affiliate').val(CurEle.data('uname'));
        $('#fullname_label').text(CurEle.data('full_name'));
		$('#users-list-panel').hide();
		 $('#change_Member_pwd').show();
    });
	
	$('#franchiseeList').on('click', '.change_pin', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#update_member_pinfrm').trigger('click');
        $('#uname_pin').val(CurEle.data('account_id'));
        $('#fullname_pin').text(CurEle.data('full_name'));
        $('#user_name').val(CurEle.data('uname'));
		$('#users-list-panel').hide();
		 $('#change_Member_security_pin').show(); 
    });
	
	$('#franchiseeList').on('click', '.edit_email', function (e) {
        e.preventDefault();
        var CurEle = $(this);
	    $('#old_emails').text(CurEle.data('email'));
	    $('#old_email').text(CurEle.data('email'));
	    var value=CurEle.data('uname');
	    var value1=CurEle.data('account_id');
		$('#user_value').text(" ("+value+")");
	    $('#user_name').val(value);
	    $('#user_account_id').val(value1);
		$('#users-list-panel').hide();
		 $('#change_email').show(); 
    });

	$('#franchiseeList').on('click', '.edit_mobile', function (e) {
        e.preventDefault();
		var CurEle = $(this);
		var rowData = searchRow(dataList,'account_id',CurEle.data('account_id'));		
		var value = rowData.uname;
	    $('#change_mobile_form #old_mobile').val(rowData.mobile);
	    $('#change_mobile #new_mobile').val('');		
		$('#change_mobile form').attr('action',$(this).attr('href'));
		$('#users-list-panel').hide();
		$('#change_mobile').show();
    });

	$("#change_mobile form").on('submit', function (e) {
		event.preventDefault();
		CURFORM = $(this);
		$.ajax({
			url: CURFORM.attr('action'),
			data: CURFORM.serialize(),
			dataType: 'JSON',
			type: 'POST',
			beforeSend: function () {
				$('.alert,div.help-block').remove();
				$('input[type=submit]',CURFORM).attr('disabled', true);
			},
			success: function (res) {
				$('input[type=submit]',CURFORM).attr('disabled', false); 
				$("#new_mobile").val('');				 
				 $("#change_mobile").hide();
				 DT.fnDraw();	
				 $("#users-list-panel").show();
				 CURFORM.data('errmsg-fld','#list');
				 CURFORM.data('errmsg-placement','before');
				 $('.alert').fadeOut(5000);
			  },
			  error: function (jqXHR, textStatus, errorThrown) {	
				$('input[type=submit]',CURFORM).attr('disabled', false);                   						
			}
		});
	});



 	$('#franchiseeList').on('click', '.block_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            data: {status: CurEle.data('status'), id: CurEle.data('account_id')},
            url: CurEle.attr('href'),
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
                $('body').toggleClass('loaded');
                $('.alert,div.help-block').remove();
            },
            success: function (res) {
                 if (res.status == 'ok') {
				    DT.fnDraw();
                   $('#franchiseeList').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000); 
                }  
			   else {
                 $('#franchiseeList').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000); 
                } 
            },
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#franchiseeList').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            }
        });
    }); 


    $('#change_email').on('click', '#check_email', function (e) {
        e.preventDefault();
        var email = $('#email').val();
        var old_email = $('#old_email').val();
        if ($('#email').valid()) {
            $('.alert,div.help-block,.help').remove();
            $('#update_member_email').attr('disabled', false);
            if (email != '' && email != old_email) {	
                $.ajax({
                    url: document.location.BASE + 'admin/account/email_check',
                    type: "POST",
                    data: ({
					"email" : $('#email').val(),
					"uname": $('#user_name').val(),
					
					}),
                    dataType: 'JSON',
                    type: 'POST',
                            beforeSend: function () {
                                $('.alert,div.help-block').remove();
                                $('#update_member_email').attr('disabled', true);
                                $('#check_email').attr('disabled', true);
                                $('body').toggleClass('loaded');
                            },
                      success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#check_email', '#change_email_form').attr('disabled', false);
                        if (res.status == 200) {
                            $('#update_member_email').attr('disabled', false);
                            if ($('#change_email_form [name="email"]').parent().hasClass('input-group')) {
                                $('#change_email_form [name="email"]').parent().after("<div class='help text-success'>" + res.msg + "</div>");
                            } else {
                                $('#change_email_form [name="email"]').after("<div class='help text-success'>" + res.msg + "</div>");
                            }
							
                            $('.help').fadeOut(7000);
                        } else
                        {
                            if ($('#change_email_form [name="email"]').parent().hasClass('input-group')) {
                                $('#change_email_form [name="email"]').parent().after("<div class='help-block text-danger'>" + res.msg + "</div>");
                            } else {
                                $('#change_email_form [name="email"]').after("<div class='help-block text-danger'>" + res.msg + "</div>");
                            }
                            $('.help-block').fadeOut(7000);
                        }
						$("#email").val('');
						
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
					
                        $('body').toggleClass('loaded');
                        $('#check_email', '#change_email_form').attr('disabled', false);
                        $('#update_member_details').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
						
                            if ($('#change_email_form [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#change_email_form [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#change_email_form [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
        return false;
    });

	

	/* Change Email */
	  $("#change_email_form").on('submit', function (e) {
		   e.preventDefault();
			CURFORM = $("#change_email_form");
			$.ajax({
				url: $("#change_email_form").attr('action'),
				data:{
					"uname": $('#user_name').val(),
					"email": $('#email').val(),			
					'account_id':$('#user_account_id').val(),
				},
				dataType: 'JSON',
				type: 'POST',
				beforeSend: function () {
					$('.alert,div.help-block').remove();
				},
				success: function (res) {                        
					 $("#email").val('');
					 $("#change_email").hide();
					 DT.fnDraw();
					 $("#users-list-panel").show();
					 CURFORM.data('errmsg-fld','#list');
					 CURFORM.data('errmsg-placement','before');
					 $('.alert').fadeOut(5000);
				},					
				error: function (jqXHR, textStatus, errorThrown) {	
					$('#change_email_form').attr('disabled', false);                       						
				}
			});
	  });
	$('.close_btn').click(function (e) {
	e.preventDefault();
	$('#view_details,#change_Member_pwd,#edit_details,#change_email,#change_mobile,#change_Member_security_pin').fadeOut('fast', function () {
		$('#users-list-panel').fadeIn('slow');
	});
    });
	 
    $("#update_member_pwdfrm").on('submit', function (e) {
		event.preventDefault();
		CURFORM = $(this);
		$.ajax({
			url: $("#update_member_pwdfrm").attr('action'),
			data:({
			"account_id": $('#uname_label').val(),
			"uname": $('#uname_affiliate').val(),
			"new_pwd": $('#new_pwd').val(),
		
			}),
			dataType: 'JSON',
			type: 'POST',
			beforeSend: function () {
				$('.alert,div.help-block').remove();
			},
			success: function (res) {
				if(res.status==200) {
					$("#new_pwd").val('');
					$("#change_Member_pwd").hide();
					DT.fnDraw();
					$("#users-list-panel").show();
					CURFORM.data('errmsg-fld','#list');
					CURFORM.data('errmsg-placement','before');
					$('.alert').fadeOut(5000);
				}
			 },
			error: function (jqXHR, textStatus, errorThrown) {	
				$('#change_email_form').attr('disabled', false);                       						
			}
	   });
	 });
	
	/* Update Member Pin*/
	$("#update_member_pinfrm").on('submit', function (e) {
		event.preventDefault();
		CURFORM = $(this);
		$.ajax({
			url: $("#update_member_pinfrm").attr('action'),
			data:{
			"account_id": $('#uname_pin').val(),
			"full_name": $('#fullname_pin').text(),
			"new_pin": $('#new_pin').val(),
			"uname":$("#user_name").val(),
			},
			dataType: 'JSON',
			type: 'POST',
			beforeSend: function () {
				$('.alert,div.help-block').remove();
				$('#update_member_pwd').attr('disabled', true);
			},
			success: function (res) {
				 $("#new_pin").val('');
				 $("#change_Member_security_pin").hide();
				 DT.fnDraw();
				 $("#users-list-panel").show();
				 CURFORM.data('errmsg-fld','#list');
				 CURFORM.data('errmsg-placement','before');
				 $('.alert').fadeOut(5000);	
			},
			error: function (jqXHR, textStatus, errorThrown) {						
			}
		});
	});
});