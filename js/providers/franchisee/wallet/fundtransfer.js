function user_check() {
	 if ($("#to_account").val() != '') {
		CURFORM = $("#to_account").closest('form');		
        $.ajax({
			url: $('#fund-transfer #fundtransferform').attr('data-usersearch'), 
            type: "post",
            data: {username: $("#to_account").val()},
            dataType: "json",
			beforeSend: function () {				
				$('.frfld').addClass('hidden');
				$('.alert').remove();
				$('#fundtransferform .hidefld2').hide();
				$('#fundtransferform .hidefld2 input[type=text]').val('');
            },
            success: function (data) {	                
               	userchk = 1;
				$('#fund-transfer #fundtransferform').data('errmsg',false)				
				$.each(data,function(k,v){
					if($('#rec_'+k)!=undefined){
						$('#rec_'+k).val(v);
						console.log($('#rec_'+k).length);
					}
				});
				if(data.is_franchasee==1){
					$.each(data,function(k,v){
						if($('.frfld #'+k)!=undefined){
							$('.frfld #'+k).val(v);
						}
					});
					$('.frfld').removeClass('hidden');
				}				
				$('#fundtransferform .hidefld2').show();
				$('#fundtransferform #confirm_fund_transfer').removeAttr("disabled");               
            },
			error: function (jqXHR, exception, op) {				
				userchk = 0;
				$('.hidefld2,.hidefld3',CURFORM).hide();
			},
			
        });
    }
}

function checkamount() {
    var amount = $('#fundtransferform #totamount').val();
    var min_amount = parseFloat($('#fundtransferform #min_trans_amount').val());
    var max_amount = parseFloat($('#fundtransferform #max_trans_amount').val());
    $("#amount").val($("#totamount").val());
    amount = parseFloat(amount);
    if (amount > max_amount) {	
        $("#fundtransferform #amount_status").html($cant_transfer_amt + max_amount + " "+$('#currency_id option:selected').text()+".");
        $(".hidefld3").hide();
        return false;
    } else if (amount < min_amount) {
        $('#fundtransferform #amount_status').html($min_transfer_amt + min_amount + " "+$('#currency_id option:selected').text()+ ".");
        $('#fundtransferform .hidefld3').hide();
        return false;
    }else{
        $('#fundtransferform #amount_status').html('');
        $('#fundtransferform .hidefld3').show();
        return true;
    }
}

	/*$(function () {
		$('body').on('click','#get_tac_code',function (e) {
			e.preventDefault();
			$.ajax({
				url: "franchisee/wallet/send_tac_code", // Url to which the request is send
				type: "POST", // Type of request to be send, called as method
				data: {account_id: $('#from_account_id').val()}, // Data sent to server, a set of key/value pairs representing form fields and values
				dataType: "json",
				beforeSend: function () {
					$('#get_tac_code').text($processing);
					$('#get_tac_code').attr('disabled', 'disabled');
				},
				success: function (data) {  // A function to be called if request succeeds
					$('#get_tac_code').removeAttr('disabled');
					$('#get_tac_code').text($get_tac_code);
					$('#msg').html(data.msg);
					$('#msg').removeClass('hidden');
				},
				error: function () {
					alert('Something went wrong');
					$('#get_tac_code').text($get_tac_code);
				}
			});
		});
	}); */

	$('body').on('submit',"#fundtransferform",function(event){   	
		event.preventDefault();
		
		if ($("#fundtransferform").valid()) {
			
				
				if($("#fundtransferform #totamount").val() === undefined || $("#fundtransferform #totamount").val() == ''){
					alert($enter_amt);
					return false;
				}
				var disabled = $("#fundtransferform").find(':input:disabled').removeAttr('disabled');
				disabled.attr('disabled','disabled');
				$("#confirm_fund_transfer").attr('disabled', 'disabled');
				$("#confirm_fund_transfer").text($processing);
				$.ajax({					
					url: $('#fund-transfer #fundtransferform').attr('action'), //"franchisee/wallet/fund_transfer_confirm",	 // Url to which the request is send
					type: "POST", // Type of request to be send, called as method
					data: {totamount:$("#totamount").val(),'remarks':$("#remarks").val()}, // Data sent to server, a set of key/value pairs representing form fields and values
					dataType:'html',
					beforeSend: function(){
						//$("#fund_transfer").attr('disabled','disabled');						
						$("#fundtransfer_confirm_form").remove();
						$('body').toggleClass('loaded');
					},
					success: function (data) { 
						$('body').toggleClass('loaded');
						//$("#fund_transfer").removeAttr('disabled');
						$("#fundtransferform").hide();
						$("#confirm_form").html(data);
					},
					error: function () {
						$('body').toggleClass('loaded');
					   $("#fund_transfer").removeAttr('disabled');					   
					   return false;
					}
				});
			
		} else {
			 $("#fund_transfer").removeAttr('disabled');
		}	
   });
  
	$('body').on('click', '#back', function (e) {
		$("#fundtransfer_confirm_form").remove();
		$("#fundtransferform").show();
	});
	
	$('body').on('click', '#confirm_fund_transfer', function (e) {	
		var datastring = $("#fundtransfer_confirm_form").serialize()+"&submit="+$(this).val();
		CURFORM = $("#fundtransfer_confirm_form");
		 $.ajax({
			url: $("#fundtransfer_confirm_form").attr('action'), // Url to which the request is send
			type: "POST", // Type of request to be send, called as method
			data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values    
			datatype:"json",
			beforeSend: function(){
				$("#confirm_fund_transfer").attr('disabled','disabled');
				$("#back").attr('disabled','disabled');
			},
			success: function (data) {					
				$('#status_msg,#msg').empty();
				if(!(data.viewdata === undefined)){
					$("#form_fields").html(data.viewdata);
					$('#msg').html(data.msg);
				} else {
					/* CURFORM = $("#fundtransferform");
					$("#fundtransfer_confirm_form").remove();
					$("#fundtransferform").show();*/				   
					location.reload();
					CURFORM.data('errmsg-fld','#fundtransferform');
				    CURFORM.data('errmsg-placement','before');				
				}
			},
			error: function () {
				$("#confirm_fund_transfer").attr('disabled',false);
				$("#back").attr('disabled',false);					
			}
		});			
	});
	
	$.fn.initTransferFrm = function(){												
		$('.hidefld,.hidefld1,.hidefld2,.hidefld3').hide();
		$('#fundtransferform select,#fundtransferform input[type=text]').val('');
	    $('#fundtransferform #user_balance,#fundtransferform #to_account_status').text('');
	}
