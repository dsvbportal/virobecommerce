var bal = '';
$(document).ready(function () {
  var member_find_url = $('#search_form').attr('action');
    $('#search_btn').click(function (e) {
        e.preventDefault();
        var member = $('#member').val();
		var trans_type = $('input[name=type]:checked').val();
        var url = $('#search_form').attr('action');
        if (member != '') {
            $('#mrerror').addClass('text-danger').html('');
			CURFORM = $('#search_form');
            $.ajax({
                url: url,
                data: {member: member,trans_type:trans_type},
                type: "post",
                dataType: "json",
                beforeSend: function () {
                    $('#search_btn').css('display', 'none');
					$('.frfld').addClass('hidden');
                },
                success: function (data) {
					user = data.userdetails;
					bal = data.balance;
					$('#mrerror').html('');                        
					$('#search_form').attr('action',user.fr_url);                       
					$('#fullname').val(user.full_name);
					$('#account_id').val(user.account_id);
					$('#uname').val(user.uname);
					$('#currency_id').empty().html('<option value="'+user.currency_id+'">'+user.currency_code+'</option>');                        
					$('#curreny_code_label').text(user.currency_code);
					if(user.is_franchasee==1){
						$.each(user,function(k,v){
							if($('.frfld #fr_'+k)!=undefined){
								$('.frfld #fr_'+k).text(v);
							}
						});
						$('.frfld').removeClass('hidden');
					}
					$('#wallet').empty();
					$.each(data.balance, function (k, v) {
						$('#wallet').append('<option value="'+k+'">'+v.wallet+'</option>');
					});
					$('#details_div').show();
					$('#currency_id').trigger('change');                   
                },
				error:function(){					
					$('#search_btn').css('display', 'block');
					$('#search_form').attr('action',member_find_url);
				}				
            })
        } else {
            $('#mrerror').addClass('text-danger').html('Please entre member email/mobile code');
				$('#search_form').attr('action',member_find_url);
        }
    });	
	
	$("input[class=trans_type]").click(function (e) {
		$('#member').trigger('keyup');
	});	
	
    $('#member').keyup(function (e) {
        $('#mrerror').html('');
        $('#fullname').val('');
        $('#account_id').val('');
        $('#uname').val('');
        $('#amount').val('');
		$('#details_div .frfld label.form-control').text();
        $('#details_div').css('display', 'none');
        $('#search_btn').css('display', 'block');
		$('#search_form').attr('action',member_find_url);
		$('#search_form select').empty();
    });
    
	$('#amount').keyup(function () {
        var min_amt = $('#min').val();
        var max_amt = $('#max').val();
        var amt = $(this).val();
        var currency = $('#currency_id option:selected').text();
        $('#submit_btn').css('display', 'none');
        if (parseFloat(amt) < min_amt) {
            $('#amt_err').addClass('text-danger').html('Min transfer amount ' + min_amt + ' ' + currency);
        } 
		else if (parseFloat(amt) > max_amt) {
            $('#amt_err').addClass('text-danger').html('Max transfer amount ' + max_amt + ' ' + currency);
        } 
		else {
            $('#amt_err').html('');
            $('#submit_btn').css('display', 'block');
        }
    })
	
    $('#search_form').validate({
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
        rules: {
            member: {required: true, maxlength: 30},
            amount: {required: true, number: true},
            currency_id: {required: true},
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
				CURFORM = $('#search_form');
                $.ajax({
                    url: $('#search_form').attr('action'),
                    data: $('#search_form').serialize(),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#update_member_details').attr('disabled', true);
                    },
                    success: function (res) {
                        if (res.status == 200) {
							$('#member').trigger('keyup');                           
							$('#update_member_details').attr('disabled', false);
							$('#search_form').attr('action',member_find_url);							
							$('#search_form select').empty();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {                        
                        responseText = $.parseJSON(jqXHR.responseText);
                        $('#search_form').attr('action',member_find_url);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#search_form [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#search_form [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#search_form [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });
    $('#currency_id,#wallet').change(function (e) {
        e.preventDefault();
        wallet = $('#wallet option:selected').val();
        currency = $('#currency_id option:selected').val();
        currency_code = $('#currency_id option:selected').text();
		trans_type = $('.trans_type:checked').val();
	    if ((wallet != '') && (currency != '')) {
			$('#submit_btn').attr('disabled',false);
            if (wallet in bal) {
                wallet_bal = bal[wallet][currency];
				if((trans_type == 2) && (wallet_bal == 0)){
					$('#submit_btn').attr('disabled',true);
				}else{
					$('#submit_btn').attr('disabled',false);
				}
			    if ((wallet_bal != undefined)) {
                    $('#avail_bal').addClass('text-danger').html('Available balance in wallet ' + wallet_bal + ' ' + currency_code);
                } else {
                    $('#avail_bal').addClass('text-danger').html('Balance not available');
                }
            } else {
				if(trans_type == 2){
					$('#submit_btn').attr('disabled',true);
				}else{
					$('#submit_btn').attr('disabled',false);
				}
                $('#avail_bal').addClass('text-danger').html('Balance not available');
			
			}
        }
    })
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
})
