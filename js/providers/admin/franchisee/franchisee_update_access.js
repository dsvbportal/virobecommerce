var err_franchi = 0;
$(document).ready(function(){
	var $editFrm = $('#access_edit');
	var franchi_type = $("#franchi_type",$editFrm).val();
	var franchi_name = $("#franchi_type option:selected",$editFrm).text();
	var relation_id = '';
	
	
	switch(franchi_type){
		case '1':							
			relation_id = $("#country option:selected",$editFrm).val();
			$('.country',$editFrm).css('display','block');
			$('.state',$editFrm).css('display','none');
			$('.region',$editFrm).css('display','none');
			$('.district',$editFrm).css('display','none');
			$('.city',$editFrm).css('display','none');
		break;
		case '2':
			relation_id = $("#region option:selected",$editFrm).val();	
			 $('.country',$editFrm).css('display','block');
			 $('.region',$editFrm).css('display','block');
			 $('.state',$editFrm).css('display','none');
			 $('.district',$editFrm).css('display','none');
			 $('.city',$editFrm).css('display','none');
		break;
		case '3':
			relation_id = $("#state option:selected",$editFrm).val();
			  $('.country',$editFrm).css('display','block');
			  $('.region',$editFrm).css('display','none');
			  $('.state',$editFrm).css('display','block');
			  $('.merchant_fee',$editFrm).css('display','block');
			  $('.profit_sharing',$editFrm).css('display','block');
			  $('.prf_shr_out_dist',$editFrm).css('display','block');
			  $('.district',$editFrm).css('display','none');		 
			  $('.city',$editFrm).css('display','none');
		break;
		case '4':
			relation_id = $("#district option:selected",$editFrm).val();	
			$('.country',$editFrm).css('display','block');
		    $('.state',$editFrm).css('display','block');
		    $('.district',$editFrm).css('display','block');
		    $('.merchant_fee',$editFrm).css('display','block');
			$('.profit_sharing',$editFrm).css('display','block');
		    $('.region',$editFrm).css('display','none');
		    $('.city',$editFrm).css('display','none');
		break;
		case '5':
			relation_id = $("#city option:selected",$editFrm).val();	
			$('.country',$editFrm).css('display','block');
		  $('.state',$editFrm).css('display','block');
		  $('.district',$editFrm).css('display','block');
		   $('.region',$editFrm).css('display','none');
		  $('.city',$editFrm).css('display','block');
		break;
	}
	
	//check_existing_frachise_access(franchi_type, relation_id, franchi_name);
	
 $('#franchi_type',$editFrm).change();
 
    $editFrm.on('change','#franchi_type',function(e){
		
	  var franci_type = $(this).val();	  	 
	  if(franci_type ==1){
		  $('.country',$editFrm).css('display','block');
		  $('.state',$editFrm).css('display','none');
		  $('.region',$editFrm).css('display','none');
		  $('.district',$editFrm).css('display','none');
		  $('.city',$editFrm).css('display','none');
	  }else if(franci_type == 2){
		  $('.country',$editFrm).css('display','block');
		  $('.region',$editFrm).css('display','block');
		  $('.state',$editFrm).css('display','none');
		  $('.district',$editFrm).css('display','none');
		  $('.city',$editFrm).css('display','none');
	  }else if(franci_type == 3){
		  $('.country',$editFrm).css('display','block');
		  $('.region',$editFrm).css('display','none');
		  $('.state',$editFrm).css('display','block');
		  $('.merchant_fee',$editFrm).css('display','block');
          $('.profit_sharing',$editFrm).css('display','block');
		  $('.prf_shr_out_dist',$editFrm).css('display','block');
		  $('.district',$editFrm).css('display','none');		 
		  $('.city',$editFrm).css('display','none');
	  }else if(franci_type == 4){
		  $('.country',$editFrm).css('display','block');
		  $('.state',$editFrm).css('display','block');
		  $('.district',$editFrm).css('display','block');
		  $('.merchant_fee',$editFrm).css('display','block');
		  $('.profit_sharing',$editFrm).css('display','block');
		  $('.region',$editFrm).css('display','none');
		  $('.city',$editFrm).css('display','none');
	  }else if(franci_type == 5){
		  $('.country',$editFrm).css('display','block');
		  $('.state',$editFrm).css('display','block');
		  $('.district',$editFrm).css('display','block');
		  $('.region',$editFrm).css('display','none');
		  $('.city',$editFrm).css('display','block');
	  }else{
		  $('.country',$editFrm).css('display','none');
		  $('.region',$editFrm).css('display','none');
		  $('.state',$editFrm).css('display','none');
		  $('.district',$editFrm).css('display','none');
		  $('.city',$editFrm).css('display','none');
	  }
	  
	    var franchi_type = $("#franchi_type",$editFrm).val();
		var franchi_name = $("#franchi_type option:selected",$editFrm).text();
		var relation_id = '';
		
		switch(franchi_type){
			case '1':							
				relation_id = $("#country option:selected",$editFrm).val();						
			break;
			case '2':
				relation_id = $("#region option:selected",$editFrm).val();	
			break;
			case '3':
				relation_id = $("#state option:selected",$editFrm).val();	
			break;
			case '4':
				relation_id = $("#district option:selected",$editFrm).val();	
			break;
			case '5':
				relation_id = $("#city option:selected",$editFrm).val();	
			break;
			
		}
		if(relation_id != ''){
			check_existing_frachise_access(franchi_type, relation_id, franchi_name);
		}
  });
  
  /* Channel Partner access form validation */
	$("#update_access").click(function () {
		var franchi_type = $('#franchisee_access #franchi_type',$editFrm).val();
		var franchi_name = $("#franchi_type option:selected",$editFrm).text();
		var relation_id = '';
		//var i = 0;
		switch(franchi_type){			
			case '1':							
				relation_id = $("#country option:selected",$editFrm).val();						
			break;
			case '2':
				relation_id = $("#region option:selected",$editFrm).val();	
			break;
			case '3':				
				relation_id = $("#state option:selected",$editFrm).val();	
				/*if($("#union_territory") != undefined){				
					
					$('#union_territory :selected').each(function(i, selected){ 
						i++;						
						relation_id[$i] = $(selected).val();						
					});					
				}*/
			break;
			case '4':
				relation_id = $("#district option:selected",$editFrm).val();	
			break;
			case '5':
				relation_id = $("#city option:selected",$editFrm).val();	
			break;
		}
		check_existing_frachise_access(franchi_type, relation_id, franchi_name,true);		
	    
	});	
	
	
	$("#franchisee_access",$editFrm).validate({
		errorElement: 'div',
		errorClass: 'help-block',
		focusInvalid: false,
		// Specify the validation rules
		rules: {
			country: "required",
			region: {
				required:function (element){
					return (franchi_type != '1' && franchi_type != '5' && franchi_type != '3' &&franchi_type != '4')
				}
			},
			state: {
				   required:function (element){
					  return (franchi_type != '1' && franchi_type != '2')
				}
			},
			district: {
				   required:function (element){
					  return (franchi_type != '1' && franchi_type != '2' && franchi_type != '3')
				}
			},
			city: {
				   required:function (element){
					  return (franchi_type != '1' && franchi_type != '2' && franchi_type != '3' &&franchi_type != '4' )
				}
			},
			profit_sharing: {
				required:function (element){
					  return (franchi_type != '1' && franchi_type != '2' && franchi_type != '5' )
				},
				min:0,
				max:100
			},
			/*pro_sharing_without_district: {
				required:function (element){
					  return (franchi_type != '1' && franchi_type != '2' && franchi_type != '4' && franchi_type != '5')
				},
				min:0,
				max:100
			}*/
		},
		// Specify the validation error messages
		messages: {
			country: "Please select Country",
			region: "Please select Region",
			state: "Please select State",
			district: "Please select District",
			city: "Please select city"
		},
		submitHandler: function (form, event) {
			event.preventDefault();
			//alert(err_franchi+'===='+$(form).attr('action'));
			if ($(form).valid()) {		
			   if(!err_franchi){					   
			   var datastring = $(form).serialize();
			   CURFORM = $('#franchisee_access');
				$.ajax({
					url: $(form).attr('action'), 
					type: "POST", 
					data: datastring, 
					dataType: "json",
					beforeSend:function(){
						$('#access_edit #update_access').attr('disabled',true).val("Processing..");
					},
					success: function (data) 	
					{
						CURFORM = '';
						$('#access_edit #update_access').attr('disabled',false).val("Save");
						window.location.reload();
						//$("#view_user_profile .modal-body").html('<div class="alert alert-success">'+data.msg+'</div>');								
						//$('td.franch_type'+data.account_id).text(franchi_name);								
						
					}					
				});
				}
			}
			else {
				alert('errors')
			}
		}
	});
	
	
	$("#country",$editFrm).change(function () {
        var country_id = $("#country",$editFrm).val();
       if(country_id !=''){
	   		$(".union_territory",$editFrm).css('display','none');		
			$("#union_territory",$editFrm).html('');
			$("#region",$editFrm).val('');
			$("#state",$editFrm).val('');	
			var regionOpt = "<option value=''>--Select Region--</option>";
			var stateOpt = "<option value=''>--Select State--</option>";
			var franchi_type = $("#franchi_type",$editFrm).val();		
						
			$.post($(this).attr('data-url'),{country_id : country_id},function(data){													
			 	
				if(data.region_list != '' && data.region_list != null){
						var regions = data.region_list;
						$.each(regions,function(key, elements){
							regionOpt += "<option value='"+elements.region_id+"'>"+elements.region+"</option>";				   	
					   });
				}
																
			 	if(data.state_list != '' && data.state_list != null){
						var states = data.state_list;
						$.each(states,function(key, elements){
							stateOpt += "<option value='"+elements.state_id+"'>"+elements.state+"</option>";				   	
					   });
				}
			  $("#region",$editFrm).html(regionOpt); 	
			  $("#state",$editFrm).html(stateOpt);					  
			
		   },'json');			
			
			//if(franchi_type == 1){				
				
				var relation_id =  $("#country",$editFrm).val();
				var franchi_name = $("#franchi_type option:selected",$editFrm).text();		
				check_existing_frachise_access(franchi_type, relation_id, franchi_name);
			//}
			
		}else{
			
			$("#region",$editFrm).html("<option value=''>--Select Region--</option>");	
			$("#state",$editFrm).html("<option value=''>--Select State--</option>");
		}
    });	

	// district 
	 $("#state",$editFrm).change(function () {
        var state_id = $("#state",$editFrm).val();
		$("#union_territory",$editFrm).html('');
		$(".union_territory",$editFrm).css('display','none');		
	    if(state_id !=''  && state_id != null){
			$("#district",$editFrm).html('');			
			var districtOpt = "<option value=''>--Select District--</option>";	
			var franchi_type = $("#franchi_type",$editFrm).val();
			var territoryOpt = '';
			$.post('admin/franchisee/districts/check',{state_id : state_id},function(data){
				if(data.territory_list != '' && data.territory_list != null){
						var territory = data.territory_list;
						$.each(territory,function(key, elements){
							territoryOpt += "<option value='"+elements.state_id+"' selected='selected'>"+elements.state+"</option>";							
							
					   });
				}		
				if(data.district_list != '' && data.district_list != null){
						var districts = data.district_list;
						$.each(districts,function(key, elements){
							districtOpt += "<option value='"+elements.district_id+"'>"+elements.district+"</option>";				   	
					   });
				}
				
				//districtOpt += "<option value='0'>Others</option>";
				if(territoryOpt != ''){					
					$(".union_territory",$editFrm).css('display','block');
					$("#union_territory",$editFrm).html(territoryOpt);							
				}else{
					$("#union_territory",$editFrm).html('');
					$(".union_territory",$editFrm).css('display','none');
				}
			 	$("#district",$editFrm).html(districtOpt);			 			 	
							   
		   },'json');
						 
			//if(franchi_type == 3){
				var franchi_name = $("#franchi_type option:selected",$editFrm).text();
				var relation_id =  $("#state",$editFrm).val();							
				check_existing_frachise_access(franchi_type, relation_id, franchi_name);
			//}
			  
		}else{
			$("#district",$editFrm).html("<option value=''>--Select City--</option>");			
		}
		$("#district",$editFrm).change();
		$("#city",$editFrm).change();
    });
	
	// city 
	$("#district",$editFrm).change(function () {
        var state_id = $("#state",$editFrm).val();
		var district_id = $("#district",$editFrm).val();	
		
		if(state_id !='' && district_id != '' && district_id != 0 && district_id != null){
			$("#district_others",$editFrm).css('display','none');
			$("#city",$editFrm).html('');
			
			var franchi_type = $("#franchi_type",$editFrm).val();
			
			
			var cityOpt = "<option value=''>--Select City--</option>";			
			$.post('admin/franchisee/city/check',{state_id : state_id, district_id : district_id},function(data){					
			
			 	if(district_id != '' && data.city_list != '' && data.city_list != null){
						var cities = data.city_list;
						$.each(cities,function(key, elements){
							cityOpt += "<option value='"+elements.city_id+"'>"+elements.city+"</option>";				   	
					   });
				}	
				
				//cityOpt += "<option value='0'>Others</option>";				
			 	$("#city",$editFrm).html(cityOpt);			 			 	
							   
		   },'json');
			// if(franchi_type == 4){
				var franchi_name = $("#franchi_type option:selected",$editFrm).text();
				var relation_id =  $("#district",$editFrm).val();
						
				check_existing_frachise_access(franchi_type, relation_id, franchi_name);
			//}
		}else if(state_id !='' && district_id == 0){			
			$("#district_others",$editFrm).css('display','block');
			
		}else{
			$("#city",$editFrm).html("<option value=''>--Select City--</option>");			
		}
		$("#city",$editFrm).change();
    });
  	$("#city",$editFrm).change(function(){			
							   
		var franchi_type = $("#franchi_type",$editFrm).val();
		
		if(franchi_type == 5){
			var franchi_name = $("#franchi_type option:selected",$editFrm).text();
			var relation_id =  $("#city",$editFrm).val();
					
			check_existing_frachise_access(franchi_type, relation_id, franchi_name);
		}
		
		if($("#city option:selected",$editFrm).text() == "Others"){
				$("#city_others",$editFrm).css('display','block');
		}else{
			$("#city_others",$editFrm).css('display','none');	
		}
	});
	
	$("#region",$editFrm).on('change',function(){															  
		var franchi_type = $("#franchi_type").val();
		if(franchi_type == 2){
			var franchi_name = $("#franchi_type option:selected",$editFrm).text();
			var relation_id =  $("#region",$editFrm).val();					
			check_existing_frachise_access(franchi_type, relation_id, franchi_name);
		}	
	});
	
	$("#update_changes",$editFrm).on('click',function(){		
		$('#franchisee_access',$editFrm).attr('action',$('#franchisee_access',$editFrm).attr('data-updateUrl'));
		$("#franchisee_access",$editFrm).submit();		
		$(this).attr('disabled',true).val('Processing...');
	});
  
});

function check_existing_frachise_access(franchise_type, relation_id, franchi_name,frm_submit=false){		
	$editFrm = $('#access_edit');
	CURFORM = $('#franchisee_access',$editFrm);
	err_franchi = 1;
	var country_id = ''; 
	var state_id = '';
	var district_id = '';
	if($("#country") !== undefined && $("#country option:selected",$editFrm).val() !== undefined){
		country_id = $("#country option:selected",$editFrm).val();						
	}
	if($("#state") !== undefined && $("#state option:selected",$editFrm).val() !== undefined) {
		state_id = $("#state option:selected",$editFrm).val();
	}
	if($("#district") !== undefined && $("#district option:selected",$editFrm).val() !== undefined) {
		district_id = $("#district option:selected",$editFrm).val();
	}
	$('#franchisee_access',$editFrm).attr('action',$('#franchisee_access',$editFrm).attr('data-saveUrl'));
	$.ajax({
		url: 'admin/franchisee/access/check', 
		type: "POST", 
		data: {pupose:'mapping',franchise_type : franchise_type, relation_id : relation_id, franchi_name : franchi_name,country_id : country_id, state_id : state_id, district_id : district_id}, 
		dataType: "json",	
		beforeSend: function(){
			$('#btnFld',$editFrm).addClass('hidden');
			$('#btnFld #update_access',$editFrm).attr('disabled',true);
		},
		success: function (data) {
			if(data.status == 200){				
				$('#franchisee_status',$editFrm).html('');
				$('#update_access',$editFrm).show();
				$('#savebtnFld',$editFrm).hide();
				if(	data.existfr !== undefined){
					var franchisee_mapped_users = '';						
					if(data.existfr.country_franchisee != '')
						franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Country Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.country_franchisee+' </div></div></div></div>';
					if(data.existfr.region_franchisee != '')
						franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Regional Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.region_franchisee+' </div></div></div></div>';
					if(data.existfr.state_franchisee != '')
						franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">State Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.state_franchisee+' </div></div></div></div>';
					if(data.existfr.district_franchisee != '')
						franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">District Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.district_franchisee+' </div></div></div></div>';						
					if(	franchisee_mapped_users === undefined)
						franchisee_mapped_users = '';							
					$("#franchisee_mapped_user",$editFrm).html(franchisee_mapped_users);
					
				} else {
					
				}
				$('#btnFld',$editFrm).removeClass('hidden');
				$('#btnFld #update_access',$editFrm).attr('disabled',false);
				if(frm_submit){
					err_franchi = 0;	
					$("#franchisee_access",$editFrm).submit();
				}				
				/*$.ajax({
					url: 'admin/franchisee/mapping/check', 
					type: "POST", 
					data: {franchise_type : franchise_type, country_id : country_id, state_id : state_id, district_id : district_id}, 
					dataType: "json",			
					success: function (data) {
						err_franchi = 0;						
						var franchisee_mapped_users = '';						
						if(data.existfr.country_franchisee != '')
							franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Country Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.country_franchisee+' </div></div></div></div>';
						if(data.existfr.region_franchisee != '')
							franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Regional Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.region_franchisee+' </div></div></div></div>';
						if(data.existfr.state_franchisee != '')
							franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">State Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.state_franchisee+' </div></div></div></div>';
						if(data.existfr.district_franchisee != '')
							franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">District Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.existfr.district_franchisee+' </div></div></div></div>';						
						if(	franchisee_mapped_users === undefined)
							franchisee_mapped_users = '';							
						$("#franchisee_mapped_user").html(franchisee_mapped_users);
						$('#btnFld').removeClass('hidden');
						$('#btnFld #update_access').attr('disabled',false);
						if(frm_submit){
							$("#franchisee_access").submit();
						}
					}
				});*/				
			 } else if(data.status =='error'){
				 err_franchi = 1;
				$('#franchisee_status',$editFrm).html(data.msg);
				$('#update_access',$editFrm).hide();					
			 }
		},		
	});
	
	
	
}