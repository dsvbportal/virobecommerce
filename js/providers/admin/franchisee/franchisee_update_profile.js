// JavaScript Document
$(document).ready(function(){
/*  NEW */
	var date = new Date();
	$('#dob').datepicker({
        startDate: '-18y',
		endDate:date,
        format: "yyyy-mm-dd",
        autoclose: true
    }).change(function () {
        document.getElementById('dob').dispatchEvent(new Event('input', {
            'bubbles': true,
            'cancelable': true
        }));
    });
		
	$('#edit_prf').validate({
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
			firstname: {
				minlength: 3,
				maxlength: 50
			},
			lastname: {
				minlength: 1,
				maxlength: 50
			},
		},		
		messages: {	
			"firstname": {
				minlength: "First name must be greater than 3 characters",
				maxlength: "First name must be less than 50 characters"
			},
			"lastname":{
				  minlength: "Last name must be greater than 1 characters",
				  maxlength: "Last  name must be less than 50 characters"
			},

		},
		highlight: function (e) {
			$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
		},
		success: function (e) {
			$(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
			$(e).remove();
		},
		submitHandler: function (form) {
		    CURFORM = $('#edit_prf');
			if ($(form).valid()) {
				$.ajax({
					type: 'POST',
					url: $('#edit_prf').attr('action'),
					data: $('#edit_prf').serialize(),
					dataType: 'json',
					beforeSend: function () {
						$('input[type=submit]', $(form)).val('Processing...');
						$('input[type=submit]', $(form)).attr('disabled', true);
					},
					success: function (op) {				
						if(op.address != ''){
							if(op.address.franchisee!=''){
								$('#fran_address',CURFORM).text(op.address.franchisee.address);
							}
							if(op.address.personal!=''){
							$('#address',CURFORM).text(op.address.personal.address);
							}
							/*$('#edit_prf #edit_address').trigger('click');*/
						  }
						$('input[type=submit]', $(form)).val('Save');
						$('input[type=submit]', $(form)).removeAttr('disabled');
					}
				});
			}
			return false;
		},
		invalidHandler: function (form) {
		}
	});
	
	$(document).on('click','.editAddressBtn',function (e) {
        e.preventDefault();
		  if($('#edit_prf .editAddressBtn').hasClass('edit')){
		      $('#edit_prf #editaddr').val(1);
	    	  $('#edit_prf .editAddressBtn').removeClass('btn-primary edit').addClass('btn btn-danger btn-xs cancel').html('<i class="fa  fa-close"></i> Cancel Edit');	
		var account_id=$("#account_id").val();
	    var $addFld = $('#edit_prf');
		$.ajax({
            url: $(this).data('url'),
			type:'POST',
			data:{account_id:account_id},
			dataType: 'json',
            beforeSend: function (op) {                
             },
			success:function(op){
 
			  $('#edit_prf .fr_address').show();
				$("#flatno_street").val(op.address.flatno_street);
				$("#landmark").val(op.address.landmark);
				$("#postal_code").val(op.address.postal_code); 
				var state =op.address.state_id;
				var district =op.address.district_id;
				var city=op.address.city_id;
				if(state!='' && op.state_list.length>0){
					$('#state_id',$addFld).empty();
					$.each(op.state_list, function (k, e) {
							$('#state_id',$addFld).append($('<option>', $.extend(state==e.state_id?{selected:'selected'}:{},{value: e.state_id})).text(e.state));
						});
						$('.stateFld',$addFld).removeClass('hidden');
				    }
			      if(district!='' && op.district_list.length>0){
					  $('#district_id',$addFld).empty();
						$.each(op.district_list, function (k, e) {
							$('#district_id',$addFld).append($('<option>',{value: e.district_id},$.extend(district==e.district_id?{selected:'selected'}:{})).text(e.district));
						});
						$('.districtFld',$addFld).removeClass('hidden');
				    }
				 if(city!='' && op.city_list.length>0){
					 $('#city_id',$addFld).empty();
					$.each(op.city_list, function (k, e) {
							$('#city_id',$addFld).append($('<option>', $.extend(city==e.city_id?{selected:'selected'}:{},{value: e.city_id})).text(e.city));
						});
						$('.cityFld',$addFld).removeClass('hidden');
				}
			}	
        });		
	 }
		else if($('#edit_prf .editAddressBtn').hasClass('cancel')){	
		   $('#edit_prf #editaddr').val(0);
			/* $('#state_id, #city_id','#edit_prf').prop('disabled', true).empty();	 */		
	    	$('#edit_prf .editAddressBtn').removeClass('btn btn-danger btn-xs cancel').addClass('edit').html('<i class="fa fa-edit"></i> Edit');			
			$('.fr_address').hide();
			$('.stateFld',$addFld).addClass('hidden');
			$('.districtFld',$addFld).addClass('hidden');
			$('.cityFld',$addFld).addClass('hidden');
	   }
	});	
  
  $(document).on('click','.edit_fr_address',function (e) {
	    if($('#edit_prf .edit_fr_address').hasClass('edit')){	
        e.preventDefault();
		    $('#edit_prf #edit_fr_addr').val(1);
	    	$('#edit_prf .edit_fr_address').removeClass('edit').addClass('btn btn-danger btn-xs cancel').html('<i class="fa fa-close"></i> Cancel Edit');		
		var account_id=$("#fr_account_id").val();
	    var $addFld = $('#edit_prf');
		$.ajax({
            url: $(this).data('url'),
			type:'POST',
			data:{account_id:account_id},
			dataType: 'json',
            beforeSend: function (op) {                
             },
			success:function(op){
				
			   $('#edit_prf .franchisee_address').show();
				$("#company_address").val(op.address.flatno_street);
				$("#fr_landmark").val(op.address.landmark);
				$("#franchisee_zipcode").val(op.address.postal_code); 
				var state =op.address.state_id;
				var district =op.address.district_id;
				var city=op.address.city_id;
				if(state!='' && op.state_list.length>0){
					$('#fr_state_id',$addFld).empty();
					$.each(op.state_list, function (k, e) {
							$('#fr_state_id',$addFld).append($('<option>', $.extend(state==e.state_id?{selected:'selected'}:{},{value: e.state_id})).text(e.state));
						});
						$('.fr_stateFld',$addFld).removeClass('hidden');
				    }
					else{
			        	$("#fr_state_id").html('<option value="">Select State</option>');
					   $.each(op.state_list, function (k, e) {
							$('#fr_state_id',$addFld).append($('<option>',{value: e.state_id}).text(e.state));
						});
						$('.fr_stateFld',$addFld).removeClass('hidden');
						$('.fr_districtFld',$addFld).removeClass('hidden');
						$('.fr_cityFld',$addFld).removeClass('hidden');
					}
			       if(district!='' && op.district_list.length>0){
					  $('#fr_district_id',$addFld).empty();
						$.each(op.district_list, function (k, e) {
							$('#fr_district_id',$addFld).append($('<option>',{value: e.district_id},$.extend(district==e.district_id?{selected:'selected'}:{})).text(e.district));
						});
						$('.fr_districtFld',$addFld).removeClass('hidden');
				    }
				  if(city!='' && op.city_list.length>0){
					 $('#fr_city_id',$addFld).empty();
					$.each(op.city_list, function (k, e) {
							$('#fr_city_id',$addFld).append($('<option>', $.extend(city==e.city_id?{selected:'selected'}:{},{value: e.city_id})).text(e.city));
						});
						$('.fr_cityFld',$addFld).removeClass('hidden');
				}
			}	
        });		
		}
		else if($('#edit_prf .edit_fr_address').hasClass('cancel')){	
			  e.preventDefault();
		   $('#edit_prf #edit_fr_addr').val(0);
	    	$('#edit_prf .edit_fr_address').removeClass('btn btn-danger btn-xs cancel').addClass('edit').html('<i class="fa fa-edit"></i> Edit');			
			$('.franchisee_address').hide();
			$('.fr_stateFld',$addFld).addClass('hidden');
			$('.fr_districtFld',$addFld).addClass('hidden');
			$('.fr_cityFld',$addFld).addClass('hidden');
		}
	});	
  
		  $('.simple').click(function(){
			var data= $(this).val();
			if(data==1){
				$("#editactions").show();
			}
			else{
				$("#editactions").hide();
			}
		  });
		  
		   $('.is_deposite').click(function(){
	
			var data= $(this).val();
			if(data==1){
				$(".desposite_details").show();
			}
			else{
				
				$(".desposite_details").hide();
			}
		  });
  
  $("#state_id").change(function () {
        var state_id = $("#state_id").val();
        if (state_id>0) {
            $("#district_id").html('');
            var districtOpt = "<option value=''>--Select District--</option>";
            $.post($(this).data('url'), {state_id: state_id}, function (data) {
                if (data.district_list != '' && data.district_list != null) {
                    var districts = data.district_list;
                    $.each(districts, function (key, elements) {
                        districtOpt += "<option value='" + elements.district_id + "'>" + elements.district + "</option>";
                    });
                }
                districtOpt += "<option value='0'>Others</option>";
                $("#district_id").html(districtOpt);
            }, 'json');
        } else {
            $("#district_id").html("<option value=''>--Select City--</option>");
        }
      
    });
  
  $("#district_id").change(function () {
        var state_id = $("#state_id").val();
        var district_id = $("#district_id").val();
        if (state_id != '' && district_id != '' && district_id != 0) {
          
            $("#city_id").html('');
            var cityOpt = "<option value=''>--Select City--</option>";
            $.post($(this).data('url'),{state_id: state_id, district_id: district_id}, function (data) {
                if (district_id != '' && data.city_list != '' && data.city_list != null) {
                    var cities = data.city_list;
                    $.each(cities, function (key, elements) {
                        cityOpt += "<option value='" + elements.city_id + "'>" + elements.city + "</option>";
                    });
                }
                cityOpt += "<option value='0'>Others</option>";
                $("#city_id").html(cityOpt);
            }, 'json');
        } else if (state_id != '' && district_id == 0) {
            $("#city_id").html("<option value=''>--Select City--</option><option value='0'>Others</option>");
          
        } else {
            $("#city_id").html("<option value=''>--Select City--</option>");
        }
        $("#city_id").change();
    });
 
/*Channel Partner */
   $("#fr_state_id").change(function () {
        var state_id = $("#fr_state_id").val();
      
        if (state_id>0) {
            $("#fr_district_id").html('');
            var districtOpt = "<option value=''>--Select District--</option>";
            $.post($(this).data('url'), {state_id: state_id}, function (data) {
                if (data.district_list != '' && data.district_list != null) {
                    var districts = data.district_list;
                    $.each(districts, function (key, elements) {
                        districtOpt += "<option value='" + elements.district_id + "'>" + elements.district + "</option>";
                    });
                }
                districtOpt += "<option value='0'>Others</option>";
                $("#fr_district_id").html(districtOpt);
            }, 'json');
        } else {
            $("#fr_district_id").html("<option value=''>--Select City--</option>");
        }
       
    });
  
  $("#fr_district_id").change(function () {
        var state_id = $("#fr_state_id").val();
        var district_id = $("#fr_district_id").val();
        if (state_id != '' && district_id != '' && district_id != 0) {
            $("#fr_city_id").html('');
            var cityOpt = "<option value=''>--Select City--</option>";
            $.post($(this).data('url'),{state_id: state_id, district_id: district_id}, function (data) {
                if (district_id != '' && data.city_list != '' && data.city_list != null) {
                    var cities = data.city_list;
                    $.each(cities, function (key, elements) {
                        cityOpt += "<option value='" + elements.city_id + "'>" + elements.city + "</option>";
                    });
                }
                cityOpt += "<option value='0'>Others</option>";
                $("#fr_city_id").html(cityOpt);
            }, 'json');
        } else if (state_id != '' && district_id == 0) {
            $("#fr_city_id").html("<option value=''>--Select City--</option><option value='0'>Others</option>");
        } else {
            $("#fr_city_id").html("<option value=''>--Select City--</option>");
        }
        $("#fr_city_id").change();
    });
	

});		
