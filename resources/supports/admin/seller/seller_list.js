$(document).ready(function () {
							
	$('#country').loadSelect({
        url: window.location.BASE + 'countries-list',
        key: 'id',
        value: 'text'        
    });
    
    var DT = $('#dt_basic').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy H:m:s');
                }
            },
             {
                data: 'company_name',
                name: 'company_name',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    return row.company_name + ' (' + row.uname + ')';
                }
             },
            {
                data: 'email',
                name: 'email',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    data = '<span>' + row.email + '</span><h5><span><b>Mobile:</b>' + ((row.phonecode) ? (row.phonecode) : 0) + '-' + row.mobile + '</span></h5>';
                    return data;
                }
            },
            {
                data: 'product_cnts',
                name: 'product_cnts',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    meta.settings.aoColumns[meta.col].bVisible = meta.settings.json.status_name == 'approvals' ? false : true;
                    data = '<h5><span><b>Prds:</b>' + row.product_cnts + '</span><span><b>Ords:</b>' + row.order_cnts + '</span></h5>';
                    return data;
                }
            },
            {
                data: 'country_name',
                name: 'country_name',
                class: 'text-left'
            },
            {
                data: 'completed_steps',
                name: 'completed_steps',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    meta.settings.aoColumns[meta.col].bVisible = meta.settings.json.status_name == '' ? false : true;
                    return $('<div>').append($('<span>').attr({class: 'text-success'}).html(row.verified_steps), '/', $('<b>').attr({class: 'text-danger'}).html(row.completed_steps))[0].outerHTML;
                }
            },            
            {
                data: 'is_verified',
                name: 'is_verified',
                class: 'text-center',
                render: function (data, type, row, meta) {
					var verify = ''
					var active = ''
                     verify = (row.is_verified == 1) ? $('<span>', {class: 'label label-success'}).text('Verified')[0].outerHTML : $('<span>', {class: 'label label-danger'}).text('Pending')[0].outerHTML;
                     active = (row.status == 1) ? $('<span>', {class: 'label label-success'}).text('Active')[0].outerHTML : $('<span>', {class: 'label label-danger'}).text('Inactive')[0].outerHTML;
					 return verify + ' / ' + active;
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += '<li><a target="_blank" href="' + json.url + '/admin/seller/details/' + row.uname + '#step-1">View Profile</a></li>';
                    action_buttons += '<li><a target="_blank" href="' + json.url + '/admin/seller/edit/' + row.uname + ' " >Edit Profile</a></li>';
                    action_buttons += '<li><a target="_blank" href="' + json.url + '/admin/seller/verification/' + row.uname + ' ">Verification List</a></li>';
                    action_buttons += '<li><a target="_blank" href="' + json.url + '/admin/seller/preferences/' + row.uname + ' ">Seller Preferences</a></li>';
                    //action_buttons += '<li class="meta-info" data-post_type_id="' + Constants.POST_TYPE.SELLER + '" data-relative_post_id="' + row.supplier_id + '" title="' + row.company_name + '"><a href="">Meta Info</a></li>';										
                    action_buttons += '<li class="meta-info" data-post_type_id="3" data-relative_post_id="' + row.supplier_id + '" title="' + row.company_name + '"><a href="">Meta Info</a></li>';										
					if (row.is_verified == 1) {
						action_buttons += '<li><a href="' + json.url + '/admin/seller/change_verify_status" class="change_status" id="' + row.account_id + '" data-status="0" >Unverify</a></li>'; 						
						if (row.status == 1) {
							action_buttons += '<li><a href="' + json.url + '/admin/seller/change_status/' + row.account_id + ' " class="change_status" id="' + row.supplier_id + '" data-status="0">Deactivate</a></li>'; 
						} else {
							action_buttons += '<li><a href="' + json.url + '/admin/seller/change_status/' + row.account_id + ' " class="change_status" id="' + row.supplier_id + '" data-status="1">Activate</a></li>';
						}						
					} else {
						action_buttons += '<li><a href="' + json.url + '/admin/seller/change_verify_status" class="change_status" id="' + row.account_id + '" data-status="1" >Verify</a></li>';
					}			
                    action_buttons += '<li><a href="' + json.url + '/admin/seller/reset_pwd/' + row.account_id + '" class="change_pwd" id="' + row.account_id + '" data-remote="false" data-toggle="modal" data-uname = "' + row.uname + '" data-company_name = "' + row.company_name + '"  data-target="#package_details_modal" >Reset Password </a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#search').click(function () {
        DT.fnDraw();
    });
    $(document).on('click', '.push', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var history_id = $(this).attr('id');
        var package_status = $('#status_col').val();
        $('#package_details_modal').modal();
        $.ajax({
            url: url,
            data: {supplier_id: $(this).attr('id')},
            beforeSend: function () {
                $('#suppliers_details .modal-body').empty();
                $('#suppliers_details .modal-body').html('Loading..');
                $('#suppliers_details').modal();
            },
            success: function (res) {
                $('#suppliers_details .modal-body').empty();
                if (res.status == 'OK') {
                    $('#suppliers_details .modal-body').html(res.contents);
                }
                else {
                    $('#suppliers_details .modal-body').html('Details Not Avaliable');
                }
            }
        });
    });
    $(document).on('click', '.change_pwd', function (e) {
        e.preventDefault();
        var UserName = $(this).attr('data-uname'), Name = $(this).attr('data-company_name'), url = $(this).attr('href'), supplier_id = $(this).attr('id');
        $('#sid', $('#suppliers_reset_pwd')).html(UserName);
        $('#uname', $('#suppliers_reset_pwd')).html(Name);
        $('#suppliers_rpwd').modal();
        $('#suppliers_reset_pwd').trigger('reset');
        $('#suppliers_reset_pwd').validate({
            errorElement: 'div',
            errorClass: 'error',
            focusInvalid: false,
            rules: {
                login_password: {
                    required: true,
                },
                confirm_login_password: {
                    required: true,
                    equalTo: '#login_password',
                },
            },
            messages: {
                login_password: {
                    required: 'Please enter your Password',
                },
                confirm_login_password: {
                    required: 'Please Retype your Password',
                },
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var datastring = $(form).serialize();
                    var url = $(this).attr('href');
                    $.ajax({
                        url: window.location.BASE + 'admin/seller/reset_pwd/' + supplier_id,
                        data: datastring,
                        beforeSend: function () {
                            $('input[type="submit"]', $(form)).val('Processing..').attr('disabled', true);
                        },
                        success: function (data) {
                            $('#suppliers_rpwd').modal('hide');
                            $('#msg').html(data.msg);
                            $('#confirm_login_password').val('');
                            $('#login_password').val('');
                            $('input[type="submit"]', $(form)).val('Submit').attr('disabled', false);
                        },
                        error: function () {
                            $('input[type="submit"]', $(form)).val('Submit').attr('disabled', false);
                            alert('Something went wrong');
                        }
                    });
                }
            }
        });
    });
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var history_id = $(this).attr('id');
        var package_status = $('#status_col').val();
        $('#package_details_modal').modal();
        $.ajax({
            url: url,
            data: {supplier_id: $(this).attr('id')},
            beforeSend: function () {
                $('#edit_data .modal-body').empty();
                $('#edit_data .modal-body').html('Loading..');
                $('#edit_data').modal();
            },
            success: function (res) {
                $('#edit_data .modal-body').empty();
                $('#edit_data .modal-body').html(res.contents);
                $('#edit_data').modal();
            }
        });
    });
});
$(document).on('click', '.change_status', function (e) {
    e.preventDefault();
    var CurEle = $(this);
    if (confirm('Are you sure? You want to ' + CurEle.text() + '?')) {
        $.ajax({
            url: CurEle.attr('href'),
            data: {status: CurEle.data('status'), account_id: CurEle.attr('id')},
            success: function (res) {
                if (res.status == 'OK') {                       
					$("#dt_basic").dataTable().fnDraw();
                }
            }
        });
    }
});
