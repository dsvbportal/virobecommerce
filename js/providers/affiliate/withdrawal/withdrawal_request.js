var payment_id	= 0;
var charges 		= '';
var charge 		= '';
var withdraw_amt = 0;
var charge_amt 	= 0;  
$(document).ready(function(){
   //$('#bal_err').text('Min withdrawal amount '+settings.min_amount+' '+settings.currency);
   // $('#charge').text('0.00 '+' '+settings.currency);
   
   var balance = current_balance.balance;
   
    $('#amt').keyup(function(e){
	    e.preventDefault();
	    withdraw_amt	= parseFloat($(this).val().replace(",",""));
	    if(withdraw_amt>0){
		    $('#withdrawal-form #submit').attr('disabled',false);
			if(parseFloat(balance)  >= parseFloat(settings.min_amount)){ 
				if(withdraw_amt <= parseFloat(balance)){
				   if(parseFloat(withdraw_amt)  >= parseFloat(settings.min_amount)){
					   if(withdraw_amt <= parseFloat(settings.max_amount)){
							
						   charge_amt = withdraw_amt*charge/100;
						   $('#bal_err').text('');
						   $('#submit').attr('disabled',false);
						
					   }else{
						   $('#bal_err').text('Max withdrawal amount '+settings.max_amount+' '+settings.currency);
					   }
				   }else{
					   $('#bal_err').text('Min withdrawal amount '+settings.min_amount+' '+settings.currency);
					   $('#submit').attr('disabled',true);
				   }
			   }else{
			        charge_amt =0;
				    $('#bal_err').text('Could not withdraw more than available balance');
					$('#submit').attr('disabled',true);
			   }
			}else{
			   $('#withdrawal-form').remove();
			   $('.back-btn').css('display','none');
				 $('#msg').html('<div class="alert alert-danger">Insufficiant balance to withdraw</div>');
				 
			}
	    }else{
		    $('#withdrawal-form #submit').attr('disabled',true);
		}
	   $('#charge').text(parseFloat(charge_amt).toFixed(2)+' '+settings.currency);
    });
  
   
   $('#withdrawal-form #submit').click(function(e){
	    e.preventDefault();
	   $('#lebel-amt').text(parseFloat(withdraw_amt).toFixed(2)+' '+settings.currency)
	   $('#lebel-charge').text(parseFloat(charge_amt).toFixed(2)+' '+settings.currency)
	   $('#withdrawal-form').css('display','none');
	   $('.back-btn').css('display','none');
	   $('#trans_pin_form').css('display','block');
	   
   });
   
   $('#trans_pin_form #confirm').click(function(e){
	   e.preventDefault();
	  CURFORM 	= $('#trans_pin_form');
	  var pin 	= $('#trans_pin').val();
	  var payment_type_id = $('#payment_type_id').val();
	  if(pin !== ''){			
		  $.ajax({
			  url:$('#trans_pin_form').attr('action'),
			  data:{'amount':withdraw_amt,'security_pin':pin,'payment_type_id':payment_type_id},
			  dataType:'json',
			  type:'post',
			  success:function(op){
				 if(op.status == 200){
					 $('#trans_pin_form').css('display','none');
				 }else{
					 $('#msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+op.msg+'</div>');
				 }
			  },

		  })
	  }
	   
   });
   
   $('.confirm-backBtn').click(function(e){
		e.preventDefault();
		$('#withdrawal-form').css('display','block');
		$('#trans_pin_form').css('display','none');
		$('.back-btn').css('display','block');
			 
   });
   
   $('#back-btn').click(function(e){
		e.preventDefault();
		$('#payout-modes').css('display','block');
		$('#payout-amt-info').css('display','none');
		$('.back-btn').css('display','block');
			 
   });
   
   $('#payout-types .payouts').click(function(e){
		CURFORM = $('#payout-types');
		e.preventDefault();	  
	    payout_id = $(this).attr('rel');
		payout_typename = $(this).data('payout-typename');
		payout_title = $(this).text();
		if(payout_typename!=''){
			$.ajax({
			    url:$('#withdrawal-form').attr('action'),
			    data:{payment_type:payout_typename},
			    dataType:'json',
			    type:'post',				
			    success:function(op) {
				    settings	= op.settings; 
				    chargeInfo  = settings.charge;
				    charge 		= chargeInfo.charge;
				    balance 	= op.current_balance;					
				    $('#withdrawal-form #payment_type_id').val(payout_id);				    
				    $('.payout-acinfo').hide();
					if(op.settings!=undefined){
						if(op.payout_acinfo!=undefined){
							var payout_acinfo  = op.payout_acinfo;
							$('#payout-'+payout_typename).css('display','block');																		
							payment_id  = payout_acinfo.payment_id;							
							$.each(payout_acinfo,function(name,val){
								$('.'+name,$('#payout-'+payout_typename)).text(val);
							});
						}	
						$('#payout-amt-info .panel-title').text(payout_title)
						$('#payout-modes').fadeOut('fast',function(){
							$('#payout-amt-info').fadeIn(500);
						});	
					}
				    $('#withdrawal-form #amt').val(balance);					
				    $('#withdrawal-form #amt').trigger('keyup');
			    }
			});
		}		
   });
   
   $('.back-btn').click(function(e){
	  e.preventDefault();
		  $('#payout-modes').css('display','block');
		  $('.tabs').css('display','none');
		  $('#amt').val(0);
		  $('#trans_pin').val('');
   });

})