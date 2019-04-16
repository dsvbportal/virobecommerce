$(document).ready(function () { 
	$('#bank_details #ifsc_code').on('keyup', function () { 
		$('#bank_details #branch_value').val('');
		$('#bank_details #bank_value').val('');	
		$('#bank_details #district').val('');	
		$('#bank_details #state').val('');	
		$('#bank_details .bank_data').hide();
		$('#bank_details #editactions button#save').attr('disabled',true);
	});	

	
	$('#bank_details #ifsc_code_verifybtn').on('click', function () { //working	beneficiary_name
		$('#bank_details #branch_value').val('');
		$('#bank_details #bank_value').val('');	
		$('#bank_details #district').val('');	
		$('#bank_details #state').val('');
		$('#bank_details .bank_det,#bank_details #editactions').hide();
		$('#bank_details #editactions button#save').attr('disabled',true).hide();
		if ($('#bank_details #ifsc_code').val().length > 10) {
		   CURFORM = $('#bank_details');		
			$.ajax({
				url: $('#bank_details #ifsc_code').attr('data-url'),            
				data: {ifsc: $('#bank_details #ifsc_code').val()},				
				dataType:'JSON',
				success: function (op) {					
				    if(op.status==200){					    						
						$('#bank_details #bank_value').val(op.data.bank);
						$('#bank_details #branch_value').val(op.data.branch);
						$('#bank_details #district').val(op.data.district);
						$('#bank_details #state').val(op.data.state);
						$('#bank_details #editactions').show();
						$('#bank_details #editactions button#save').attr('disabled',false).show();
						$('#bank_details .bank_det').show();
				    }
				},
				error: function (jqXHR, exception, op) {
					$('#bank_details #editactions').show();
					$('#bank_details #editactions button#cancel_edit').attr('disabled',false).show();
				},
			});
		}
	});
	
	$('#bank_details').on('submit',function (event) {  //working
        event.preventDefault();	
        CURFORM = $(this);
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function () {
				CURFORM.siblings('div.alert').remove();
			},
			success: function (op, textStatus, xhr) {
				/* console.log(op.postdata.payment_setings);return false; */
				$.each(op.postdata.payment_setings,function(k,v){
					$("#view_detail_list #view_"+k).text(v);					
				})
				$("#add_details").hide();				
			    $('#view_detail_list').show();			
				$('#bank_details_main #edit').show();
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.responseJSON.msg != undefined && jqXHR.responseJSON.msg != '') {
					console.log(jqXHR.responseJSON.msgs);
					CURFORM.before('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+jqXHR.responseJSON.msg+'</div>');				
				} 	
			},
        });
    });		
	
	$('#bank_details').on('click', '#no_ifsc', function (e) {
	    alert('no_ifsc');
		check = $('#no_ifsc:checked').val();
		if(check==1){
		    $('#change_Member_pin').show(); 
		    $('#retailer-qlogin-model1').modal();
		}
    });	

 	$("#bank_details_main #edit").click(function(){		
		$('.banklabels_val').each(function(k,elm){
			 k = $(this).data('target');
			 $('#add_details input#'+k).val($(this).text());	 
		}) 		
	    $("#view_detail_list").hide();	   
	    $('#add_details').show();
	    $(this).hide();
	}); 
	
    $("#bank_details #cancel_edit").click(function(){
		$("#view_detail_list").show();	   
	    $('#add_details').hide();		
		$('.banklabels_val').each(function(k,elm){
			 k = $(this).data('target');
			 $('#add_details input#'+k).val('');	 
		});		
		$("#bank_details_main #edit").show();		
	});
	
	$('#ifsc_bank_details_form').submit(function (event) {		
        event.preventDefault();	
		CURFORM = $('#bank_details');
		$('#retailer-qlogin-model1').modal('hide');
		var formData = new FormData(this);
        var ifsc_code=$("#ifsc_code_details").val();
        var bank_name=$("#bank_name").val();
        var branch_name=$("#branch_name").val();
		$('#bank_det').show();
		$('#ifsc_code').val(ifsc_code);
		$('#bank_value').val(bank_name);
		$('#branch_value').val(branch_name);
		$('#district').val(branch_name);
		$('#state').val(state);
    });
});
