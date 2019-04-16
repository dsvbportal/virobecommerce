$(function () {		
	$('#invite_now').click(function (e) {		
	    if($('#invite_email').val() != ''){		
			$.ajax({
				url: $(this).attr('data-url'),            
				data: {email: $('#invite_email').val(), referral_url: $(this).attr('data-refurl')},			
				dataType: 'json',
				success: function (op) {
					console.log(op);					
				},
				error: function (jqXHR, exception, op) {				
					responseText = $.parseJSON(jqXHR.responseText);
					if(responseText.msg != ''){
					    $('#invite_email_err').text(responseText.msg);	
					}
				}				
			}); 
	    }else {		
		    $('#invite_email_err').text('Please enter your friend email');	
		}		
    });	
	
	$('#invite_email').on('focus', function(e){	  	
	    $('#invite_email_err').empty();		
	});	
});
