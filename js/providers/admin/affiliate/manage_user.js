$(document).ready(function () {
    var DT = $('#manage_user_list').dataTable({
		 "ordering": false,
		 "bStateSave": true,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
        columns: [
            {
                data: 'signedup_on',
                name: 'signedup_on',
            },
			{
                data: 'uname',
                name: 'uname',
				 render: function (data, type, row, meta) {
                var txt = '';
				var txt='<span class="text-muted">Affiliate ID: </span><b>'+row.user_code+'</b><br><span class="text-muted">Login ID: </span><b>'+row.uname+'</b><br><span class="text-muted">Full Name: </span><b>'+row.fullname+'</b><br><span class="text-muted">Email: </span><b>'+row.email+'</b><br><span class="text-muted">Mobile No: </span><b>'+row.mobile+'</b>';
				return txt;
				 }
            },
			{
				"data": "reffered_by",
				"name": "reffered_by"
			},
			{
				"data": "rank",
				"name": 'rank',
				class: 'text-center',
				render: function (data, type, row, meta) {
					 rank = '--';
					if(row.rank > 0){
					 rank = '<span >'+row.rank+'G</span>';
					}
					 return rank;
				}
			},
			{
				"data": "upline_id",
				"name": "upline_id"
			},
			
			{
				"data": "rootuser",
				"name": "rootuser"
			},
			{
				data: 'country_name',
				name: 'country_name',
			},
	        {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
			         active = '<span class="label label-'+row.status_class+'">'+row.status_name+'</span>';
					 return active;
                }
            },
			{
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
        ]
    });
    $('#search').click(function () {
        DT.fnDraw();
    });
	$('#reset').click(function (e) {
		  $("#search_text").val('');
		  $("#from").val('');
		  $("#to").val('');
		  DT.dataTable().fnDraw();
	});
});
	/* View and Edit Details */
	    $("#manage_user_list").on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.data != undefined && op.data != null) {
				  $("#view_details").html(op.data);
                  $('#users-list-panel,#edit_details').fadeOut('fast', function () {
				    $('#view_details').fadeIn('slow'); 
                });
            }
            else if (op.edit != undefined && op.edit != null) {
                 $('#users-list-panel,#view_details').fadeOut('fast', function () {
                    $('#edit_details').fadeIn('slow');
                });
                 $('#account_id', '#user_updatefrm').val(op.edit.account_id);
                 $('#uname_aff', '#user_updatefrm').val(op.edit.uname);
                 $('#first_name', '#user_updatefrm').val(op.edit.firstname);
                 $('#last_name', '#user_updatefrm').val(op.edit.lastname);
                 $('#dob', '#user_updatefrm').val(op.edit.dob);
                 $('#gender, option:selected').val(op.edit.gender);
				 $('#email_id', '#user_updatefrm').val(op.edit.email);
				 $('#mobile_no', '#user_updatefrm').val(op.edit.mobile);
            }
            else {
               	$("#manage_user_list").dataTable().fnDraw();
            }
        });
    });
	
	/* For Change Password  */
    $('#manage_user_list').on('click', '.change_password', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#update_member_pwdfrm').trigger('click');
        $('#uname_label').val(CurEle.data('account_id'));
        $('#uname_affiliate').val(CurEle.data('uname'));
        $('#fullname_label').text(CurEle.data('full_name'));
		$('#users-list-panel').hide();
		 $('#change_Member_pwd').show();
    });
	
	$('#manage_user_list').on('click', '.change_pin', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#update_member_pinfrm').trigger('click');
        $('#uname_pin').val(CurEle.data('account_id'));
        $('#fullname_pin').text(CurEle.data('full_name'));
        $('#user_name').val(CurEle.data('uname'));
		$('#users-list-panel').hide();
		 $('#change_Member_security_pin').show(); 
    });
$('#manage_user_list').on('click', '.edit_email', function (e) {
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
  /* For Change Mobile */
	  $('#manage_user_list').on('click', '.edit_mobile', function (e) {
        e.preventDefault();
		var CurEle = $(this);
	    $('#old_mobile').text(CurEle.data('mobile'));
	    $('#old_no').val(CurEle.data('mobile'));
		var value=CurEle.data('uname');
		var value1=CurEle.data('uname');
		var account_id=CurEle.data('account_id');
		$('#user_mobile_val').text(" ("+value1+")");
	    $('#uname_mobile').val(value);
		$("#account_id_mobile").val(account_id)
		$('#users-list-panel').hide();
		 $('#change_mobile').show();
    });
	    /* User Block Status */
 	   $('#manage_user_list').on('click', '.block_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            data: {uname: CurEle.attr('rel'), status: CurEle.data('status'), id: CurEle.data('account_id')},
            url: CurEle.attr('href'),
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
                $('body').toggleClass('loaded');
                $('.alert,div.help-block').remove();
            },
            success: function (res) {
                 if (res.status == 200) {
				     $("#manage_user_list").dataTable().fnDraw();
                 $('#manage_user_list').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000); 
                }  
			   else {
                  $('#manage_user_list').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000); 
                } 
            },
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#manage_user_list').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            }
        });
    }); 
	/*Email Check */

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
	/*change Mobile */
	$("#change_mobile_form").on('submit', function (e) {
            event.preventDefault();
			CURFORM = $("#change_mobile_form");
                $.ajax({
                    url: $("#change_mobile_form").attr('action'),
					data:{
					"uname": $('#uname_mobile').val(),
					"mobile_account_id":$('#account_id_mobile').val(),
					"mobile": $('#mobile').val(),
					},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#change_mobile_form').attr('disabled', true);
                    },
                    success: function (res) {
						 $("#mobile").val('');
                         $('#change_mobile_form').attr('disabled', false);
						 $("#change_mobile").hide();
						 $("#manage_user_list").dataTable().fnDraw();	
						 $("#users-list-panel").show();
                         CURFORM.data('errmsg-fld','#list');
	                   	 CURFORM.data('errmsg-placement','before');
						 $('.alert').fadeOut(5000);
                      },
                      error: function (jqXHR, textStatus, errorThrown) {	
                        $('#change_mobile_form').attr('disabled', false);                       						
                    }
                });
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
						'user_account_id':$('#user_account_id').val(),
					},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                    },
                    success: function (res) {                        
                         $("#email").val('');
						 $("#change_email").hide();
						 $("#manage_user_list").dataTable().fnDraw();	
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
	 /* Update Member Password*/
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
                         $("#new_pwd").val('');
						 $("#change_Member_pwd").hide();
						 $("#manage_user_list").dataTable().fnDraw();	
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
						/* if(res.status==200){ */
						 $("#new_pin").val('');
						 $("#change_Member_security_pin").hide();
						 $("#manage_user_list").dataTable().fnDraw();	
						 $("#users-list-panel").show();
                         CURFORM.data('errmsg-fld','#list');
	                   	 CURFORM.data('errmsg-placement','before');
						 $('.alert').fadeOut(5000);	
					/*	}
					 else if(res.status==422){
					     CURFORM.data('errmsg-fld','#update_member_pinfrm');
	                   	 CURFORM.data('errmsg-placement','before');													
						  $("#update_member_pinfrm").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
						} */
                    },
                    error: function (jqXHR, textStatus, errorThrown) {						
                    }
                });
        });
	
 /* Update User details*/
       $("#user_updatefrm").on('submit', function (e) {
            event.preventDefault();
			CURFORM = $(this);
                $.ajax({
                    url: $("#user_updatefrm").attr('action'),
					data:{
						"account_id":$("#account_id").val(),
						"first_name": $('#first_name').val(),
						"last_name": $('#last_name').val(),
						"dob": $('#dob').val(),
						"gender":$('#gender').val(),
						"email":$('#email_id').val(),
						"mobile":$('#mobile_no').val(),
						"uname":$('#uname_aff').val(),
					},
	                dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                       /*  $('#update_member_details').attr('disabled', true); */
                    },
                    success: function (res) {
						
                     /*   $("#user_updatefrm").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ res.msg + '</div>');
                            $('.alert').fadeOut(7000); */
							
					    /* $("#new_pin").val(''); */
						 $("#edit_details").hide();
						 $("#manage_user_list").dataTable().fnDraw();	
						 $("#users-list-panel").show();
                         CURFORM.data('errmsg-fld','#list');
	                   	 CURFORM.data('errmsg-placement','before');
						 $('.alert').fadeOut(5000);	
							
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        
                    }
                });
        });
  $(document).on('click','.pwdHS', function (e) { 
	    var x = $(this).siblings('input').attr('type');
	    if (x === 'password') {
	        $(this).siblings('input').attr('type', 'text');
	        $(this).find('i').attr('class','').attr('class','icon-eye-open');
	    } else {
	        $(this).siblings('input').attr('type', 'password');
	        $(this).find('i').attr('class','').attr('class','icon-eye-close');
	    }
	});
   function addDropDownMenu(arr, text) {  
    arr = arr || [];
    text = text || false;
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-sm btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
            .append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            data = {};
            if (! v.redirect) {
                v.class = v.class || (v.url ? 'actions' : 'show-modal');
            }
            else {
                data['target'] = v.target || '_blank';
            }
            v.url = v.url || '#';
            v.data = v.data || {};
            $.each(v.data, function (key, val) {
                data['data-' + key] = val;
            });
            options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.label)));
        });
        return options;
    }));
    return text ? content[0].outerHTML : content;
}
function addDropDownMenuActions(e, callback) {
    var Ele = e, data = Ele.data();
    callback = callback || null;
    if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
        if (data.confirm != undefined) {
            delete data.confirm;
        }
        $.ajax({
            url: Ele.attr('href'),
            data: data,
            success: function (data) {
                if (callback !== null) {
                    callback(data);
                }
            }
        });
    }
}