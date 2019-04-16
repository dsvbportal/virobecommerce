$(function () {
	
    /* Add Address */
	$('.container').on('click','#add_address',function (e) {
        e.preventDefault(); 
		$('#address-model div.alert').empty();		
		$('#postal_code,#address,#landMark,#alternate_mobile','#address-model').val('');
		$('#city_id,#state_id','#address-model').empty().val('');	
		$('.cityFld,.stateFld','#address-model').addClass('hidden');
		$('#address-model #is_default').removeAttr('checked');		
		$('#address-model .address_type').removeAttr('checked');		
		$('#address-model').modal('show');       
	});

	/*  Check Pincode */	
	$(document).on('change','#addressFrm #postal_code', function (){	
	    var ADF = $('#addressFrm');
        var pincode = $('#addressFrm #postal_code').val();
        var country_id = $('#addressFrm #country_id').val(); 	
        CURFORM = ADF;			
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: $('#addressFrm').attr('data-pincode-url'),
				data: {pincode: pincode,country_id:country_id},
				beforeSend:function(){
					$('#city_id',ADF).empty();
				},
				success: function(op) {							
					$('#addressSaveBtn').attr('disabled',false);	
					$('#state_id, #city_id',ADF).prop('disabled', false).empty();	
					var city_id = $('#city_id',ADF).data('selected');
					var state_id = $('#state_id',ADF).data('selected');					
					$('#state_id',ADF).append($('<option>', {value: op.data.state_id,selected:'selected'}).text(op.data.state));
					$.each(op.data.cities, function (k, e) {
						$('#city_id',ADF).append($('<option>', $.extend(city_id==e.id?{selected:'selected'}:{},{value: e.id})).text(e.text));
					});
					$('.cityFld,.stateFld',ADF).removeClass('hidden');
				},
				error: function() {				
					$('#state_id, #city_id',ADF).empty().prop('disabled', true);				
                    $('#addressSaveBtn').attr('disabled',true);					
				}
			});
		}	
    });	

    $(document).on('click', '#addressSaveBtn', function (e) {
		$('#addressFrm').submit();
	});
	
	/* Address Update */
	var DELIVER_ADDR = {};
	$(document).on('submit', '#addressFrm',function (e) {		
        e.preventDefault();
        CURFORM = $(this);		
        $.ajax({
            url: CURFORM.attr('action'),            
            data: CURFORM.serialize(),			
			beforeSend:function(){
				$('#address-model #addressSaveBtn').attr('disabled',true);				
			},
			success: function (op, textStatus, xhr) {	
                $('#address-model #addressSaveBtn').attr('disabled',false);
				$('.no_addr').hide();				
				$('#address-model').modal('hide');
				DELIVER_ADDR[op.address_id] = [];
				DELIVER_ADDR[op.address_id]['address_id'] = op.address_id;
				DELIVER_ADDR[op.address_id]['address_type_id'] = op.address_type_id;	
				DELIVER_ADDR[op.address_id]['address'] = op.address;	
				if($('#addr_list #addr_'+op.address_type_id).length>0){					
					$('#addr_list #addr_'+op.address_type_id+' .delivery_addr').text(op.address);
				}else {
					var str = [];
					var checkout_url= $('#addr_list').attr('data-checkout-url');										
					$.each(DELIVER_ADDR, function (k, v) {
						console.log(v);
						if($('#addr_list #addr_'+v.address_type_id).length == 0){
							var addrlbl = (v.address_type_id == '1') ? 'Home Address' : 'Shipping Address';								
							str.push($('<div>').attr({class: 'col-sm-6 address', id:'addr_'+v.address_type_id}).append(
								$('<div>').attr({class: 'col-sm-6'}).append([
									$('<h3>').attr({}).append([$('<i>').attr({class: 'fa fa-map-marker'}).append(),' '+addrlbl]),
									$('<div>').attr({class: 'delivery_addr'}).append(v.address),
									$('<div>').attr({}).append([
										$('<a>').attr({href: checkout_url+'?address_id='+v.address_id ,class: 'btn btn-warning deliver_btn'}).append('Delivery To This Address')
									]),							
								])
							));	
						}							
					});
					if($('#addr_list .address').length >0){	
						$('#addr_list .address').after(str);	
					}else {
						$('#addr_list').empty().append(str);								
					}										
				} 					
				CURFORM.data('errmsg-fld','#addr_list');
				CURFORM.data('errmsg-placement','before');				
			},
			error: function (jqXHR, exception, op) {
				$('#address-model #addressSaveBtn').attr('disabled',false);
			}			
        });
    });
		
	$('#addr_list').on('click', '.deliver_btn', function(e){		
		CURELE = $(this);		
		e.preventDefault();		
		$(this).attr('disabled', true);  
		$.ajax({
			url: $('#addr_list').attr('data-checkout-url'),				
			success: function(op) {	    
                var str= [];		
                $('#addr_list, #add_address, .back-btn','#center_column').hide();			
                $('#center_column .back_to_addr').show();			
               // $('#center_column .back-btn').attr('href',$('#center_column').attr('data-addr-url'));			
                //$('#center_column .btn_text').text('Back To Address');			
                $('#center_column .page-heading-title2').text('Select Payment Type');			
				$.each(op.payment_type.payment_types, function (k, v) {
					str.push($('<div>').attr({class: 'radio list-group-item'}).append($('<label>').append([$('<input>').attr({type: 'radio', name: 'optradio', value:v.id}).append(), $('<img>').attr({class:'pay-img', src: v.icon}).append(v.name), $('<span>').attr({class: 'pay-title'}).append(v.name)])));	                    			
				});
				str.push($('<div>').attr({style: 'margin-top:20px;'}).append($('<a>').attr({href: '#',class: 'btn btn-primary',id:'continue_button'}).append('<i class="fa fa-angle-right"></i> Continue')));
                $('#center_column #payment_types').append(str); 				
			}			
		});		       
	});
	
	$('#center_column').on('click', '.back_to_addr', function(e){
		e.preventDefault();	
		$('#center_column .deliver_btn').attr('disabled', false);  
		$('#addr_list, #add_address, .back-btn','#center_column').show();			
        $('#center_column .back_to_addr').hide();
        $('#center_column #payment_types').empty();
		$('#center_column .page-heading-title2').text('Select Delivery Address');		
	});

	$('#center_column').on('click', '#payment_types #continue_button', function (e) {
		e.preventDefault();
		var dd = document.querySelector('input[name="optradio"]:checked').value;
		var address = $('#addres_id').val();
		if (dd) {
			$.ajax({
				url: $('.deliver_btn').attr('href'),
				data: {payment_id: dd, address_id: address},
				type: 'post',
				beforeSend: function () {
					$('.alert').remove();
				},
				success: function (op) {
					$('#center_column #payment_types').hide();
					$('#center_column #err_msg').append("<div class='alert alert-success'>" + op.msg + "<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
					 my_cart_list();
				},
				error: function (jqXHR, exception, op) {
					$('#center_column #err_msg').append("<div class='alert alert-danger'>" + jqXHR.responseJSON.msg +  "<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
				}
			});
		}

	});
});
