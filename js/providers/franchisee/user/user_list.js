$(function () {
    var t = $('#user_list');
    t.DataTable({
        ordering: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },		 
        ajax: {
            url: $('#user_details_list').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#user_details_list input,select').serializeObject());
            }         
        },
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){      
			   var oSettings = this.fnSettings();
			   $("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
			   return nRow;
             },
        columns: [
		       {
                name: 'sino',
                class: 'text-center no-wrap',
				render:function(data, type, row, meta) {
					return '';
				}
              },
			  {   
			    data: 'fullname',
                name: 'fullname',
                class: 'text-left',
                render: function (data, type, row, meta) {  		
				 var str = '<b>'+row.fullname+'</b> (#'+row.user_code + ')';
					return str;
                }
              }, 
			 
			  {
				data: 'email',
				name: 'email',
				class: 'text-left',
				render: function (data, type, row, meta) {
				var txt = '';
				var txt='<span class="text-muted"><i class="fa fa-envelope"></i></span>'+row.email+'<br><span class="text-muted"><i class="fa fa-mobile"></i></span>'+row.mobile+'';
				return txt;
				 }
			},
			{
				data: 'address',
				name: 'address',
				class: 'text-left',
				render: function (data, type, row, meta) {
				var txt = '';
				var txt='<span class="text-muted"><i class="fa fa-map-marker"></i></span>'+row.address+'';
				return txt;
				}
			},
			 {
				data: 'signedup_on',
				name: 'signedup_on',
				 class: 'text-left',
			  },
			{
                data: 'status_name',
                name: 'status_name',
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
            },
        	
        ],
	
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.created_date;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }     
    });

	
    $('#search_btn').click(function (e) {
        t.dataTable().fnDraw();
    });

    $('#reset_btn').click(function (e) {
     /* $('input,select', $(this).closest('form')).val('');*/
		$("#search_term").val('');
		$("#from").val('');
		$("#to").val('');
        t.dataTable().fnDraw();
    });

	/* For Change Password  */
    $('#user_list').on('click', '.change_password', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#uname_user').val(CurEle.data('uname'));
        $('#fullname_label').text(CurEle.data('fullname'));
		$('#users-list-panel').hide();
		$('#change_Member_pwd').show();
    });
	
	 /* Update Member Password*/
       $("#update_member_pwdfrm").on('submit', function (e) {
            event.preventDefault();
		    CURFORM = $(this);
                $.ajax({
                    url: $("#update_member_pwdfrm").attr('action'),
					data:({
					"uname": $('#uname_user').val(),
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
						 $("#user_list").dataTable().fnDraw();	
						 $("#users-list-panel").show();
                         CURFORM.data('errmsg-fld','#list');
	                   	 CURFORM.data('errmsg-placement','before'); 
						 $('.alert').fadeOut(7000);
                     },
                    error: function (jqXHR, textStatus, errorThrown) {	
                        $('#change_email_form').attr('disabled', false);                       						
                    }
               });
         });
		 
	  /* User Block Status */
 	   $('#user_list').on('click', '.active_status', function (e) {
        e.preventDefault();
		CURFORM = $(this);
        var CurEle = $(this);
		if(confirm("Are you sure, you wants to "+CurEle.data('staus_info')+" User")){
			$.ajax({
				data: {uname: CurEle.data('account_id'), status: CurEle.data('status')},
				url: CurEle.attr('href'),
				type: "POST",
				dataType: 'JSON',
				beforeSend: function () {
					$('body').toggleClass('loaded');
					$('.alert,div.help-block').remove();
				},
				success: function (res) {
					   $("#user_list").dataTable().fnDraw();
						 CURFORM.data('errmsg-fld','#list');
	                   	 CURFORM.data('errmsg-placement','before'); 
						 $('.alert').fadeOut(7000); 
				},
				 error: function (res) {
					$('body').toggleClass('loaded');
					return false;
				}
			});
		}
    }); 
	 $("#user_list").on('click', '.edit_details', function (e) {
        e.preventDefault();
		   var CurEle = $(this);
		 $.ajax({
				data: {uname: CurEle.data('uname')},
				url: CurEle.attr('href'),
				type: "GET",
				dataType: 'JSON',
              beforeSend: function (op) {                
            },
			success:function(op){
			 $('#users-list-panel').hide();
			 $('#edit_details').html(op.template);
				  $('#dob').datepicker({
					autoclose:true, 
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 1,
					format: 'yyyy-mm-dd'
             });
			 var user_dob=op.details.dob.split("-");
			 $('#dob_year','#edit_details').html(year_str);
			 $('#dob_year').trigger("change");
			 $('#dob_month').trigger("change");
			 $("#dob_year option[value="+user_dob[0]+"]").attr('selected', 'selected');
			 if(user_dob[1]!=10){
			    var user_month=user_dob[1].replace("0", "");
				$("#dob_month option[value="+user_month+"]").attr('selected', 'selected');
			 }else{
				 $("#dob_month option[value="+user_dob[1]+"]").attr('selected', 'selected');
			 }
			    var day_str = '<option value="">Day</option>';
				var year = parseInt($('#dob_year','#user_updatefrm').val());
				var month = parseInt($('#dob_month','#user_updatefrm').val());
				for (var i = 1; i <= (new Date(year, month, 0).getDate()); i++)
				{
					day_str = day_str + '<option value="' + i + '">' + i + '</option>';
				}
				$('#dob_day','#user_updatefrm').html(day_str);
			  $('#dob_day','#edit_details').append($('<option>', $.extend({selected:'selected'},{value: user_dob[2]})).text(user_dob[2])); 
			  $('#edit_details').show();
			}	
        });		
	}); 
	
	/* Update User details*/
	
      $('#edit_details').on('submit','#user_updatefrm', function (e) {
        e.preventDefault();
        CURFORM = $("#user_updatefrm");
        $.ajax({
            url: $("#user_updatefrm").attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: $("#user_updatefrm").serialize(),
            beforeSend: function () {
                $(':submit', '#user_updatefrm').attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
				 $(':submit', '#user_updatefrm').attr('disabled', false).val('Update');
			/*	 $("#edit_details").hide();
				 $("#user_list").dataTable().fnDraw();
				  $("#users-list-panel").show();
				  CURFORM.data('errmsg-fld','#list');
	              CURFORM.data('errmsg-placement','before'); */
		         $('.alert').fadeOut(5000);	
            },
            error: function (jqXhr) {
                $(':submit', '#user_updatefrm').removeAttr('disabled', true).val('Update');
            }
        });
    });
	
	$('#edit_details').on('click','.editAddressBtn',function (e) {
        e.preventDefault();
      var $addFld = $('#addressFrm');
		var address_head = $(this).data('heading');
		$.ajax({
            url: $(this).data('url'),
			type:'GET',
            beforeSend: function (op) {                
				$('#address-model .modal-title span').text('User Address');
				$('#address-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){
				$('#address-model .modal-body').html(op.template);		
				$('#address-model').modal('show');
			var state =op.data.address.state_id;
			var district =op.data.address.district_id;
			var city=op.data.address.city_id;			
			if(state!='' && op.data.state_list.length>0){
				$('#addressFrm #state').empty();				
				$.each(op.data.state_list, function (k, e) {
					$('#addressFrm  #state').append($('<option>', $.extend(state==e.state_id?{selected:'selected'}:{},{value: e.state_id})).text(e.state));
				});
			}
			if(district!='' && op.data.district_list.length>0){
			 $('#addressFrm #district').empty();
				$.each(op.data.district_list, function (k, e) {
					$('#addressFrm  #district').append($('<option>',$.extend(district==e.district_id?{selected:'selected'}:{},{value: e.district_id})).text(e.district));
				});
			}
			if(city!='' && op.data.city_list.length>0){
				$('#addressFrm  #city_id').empty();
				$.each(op.data.city_list, function (k, e) {
					$('#addressFrm  #city_id').append($('<option>', $.extend({value: e.city_id},city==e.city_id?{selected:'selected'}:{})).text(e.city));
				});
			} 
		}	
	});		
});	


   /*Date Starts */
	var now = new Date();	
	var year_str = '<option value="">Year</option>';
	for (var i = (now.getFullYear() - 18); i >= 1908; i--){
		year_str = year_str + '<option value="' + i + '">' + i + '</option>';	
	}	
   
	$('#edit_details').on('change','#dob_year',function () {
		var months = {'1': 'Jan', '2': 'Feb', '3': 'Mar', '4': 'Apr', '5': 'May', '6': 'June', '7': 'July', '8': 'Aug', '9': 'Sept', '10': 'Oct', '11': 'Nov', '12': 'Dec'};
		var month_str = '<option value="">Month</option>';
		for (var i = 1; i <= 12; i++)
		{
			month_str = month_str + '<option value="' + i + '">' + months[i] + '</option>';
		}
		$('#dob_month','#user_updatefrm').html(month_str);
		$('#dob_day','#user_updatefrm').val('');
	});
	
	$('#edit_details').on('change','#dob_month',function () {
	    $('#dob_day','#edit_details').empty();
		var day_str = '<option value="">Day</option>';
		var year = parseInt($('#dob_year','#user_updatefrm').val());
		var month = parseInt($('#dob_month','#user_updatefrm').val());
		for (var i = 1; i <= (new Date(year, month, 0).getDate()); i++)
		{
			day_str = day_str + '<option value="' + i + '">' + i + '</option>';
		}
		$('#dob_day','#user_updatefrm').html(day_str);
	   });
		$('#edit_details').on('change','#dob_day',function () {
			var dob = $('#dob_year','#user_updatefrm').val() + '-' + $('#dob_month','#user_updatefrm').val() + '-' + $('#dob_day','#user_updatefrm').val();
			$('#dob','#user_updatefrm').val(dob);
		});
	
	/* Date Ends */
	  $('#edit_details').on('change', '#state', function () {
		$cForm = $(this).closest('form');
		var id = $(this).val();
		if(id>0){		
			$.ajax({
				type: 'POST',
				url: $('#district',$cForm).data('url'),
				data: {state_id:id},
				dataType: 'json',		       
				beforeSend: function () {				
					$("#district",$cForm).html('<option value="">Loading...</option>');
				},				
				success: function (op) {					
					var str = '<option value="">Select District</option>';
					$.each(op.district, function (k, v) {
						str+='<option value="'+v.district_id+'">'+v.district+'</option>';
					});
					$('#district',$cForm).html(str);
				}
			});
		}		
    });
	
	$('#edit_details').on('change', '#district', function () {
		
		$cForm = $(this).closest('form');
        var state_id = $("#state").val();
        var district_id = $("#district").val();
        if (state_id != '' && district_id != '' && district_id != 0) {
             $.ajax({
				type: 'POST',
				url: $('#city_id',$cForm).data('url'),
				data: {state_id: state_id, district_id: district_id},
				dataType: 'json',		       
				beforeSend: function () {				
					$("#city_id",$cForm).html('<option value="">Loading...</option>');
				},				
				success: function (op) {					
					var str = '<option value="">Select City</option>';
					$.each(op.city, function (k, v) {
						str+='<option value="'+v.city_id+'">'+v.city+'</option>';
					});
					$('#city_id',$cForm).html(str);
				}
			});
            
        } 
    });
	     $('#edit_details').on('submit','#addressFrm', function (e) {
          e.preventDefault();
          CURFORM = $(this);
          $.ajax({
            url: CURFORM.attr('action'),            
            data: CURFORM.serialize(),			
			beforeSend:function(){				
				/* $('#addressSaveBtn').attr('disabled',true); */
			},
			success: function (op, textStatus, xhr) {
				$('#addressSaveBtn').attr('disabled',false);
				if(xhr.status == 200){
					$('#franchiseeAddr').text(op.user_address);
					if(op.addtype!='' && op.address){
						$('#'+op.addtype+'Addr').text(op.address);
					}
					$('#addrss_list').prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					$('#address-model').modal('hide');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				} 
			},
			
        });
    });
	
   $('#edit_details').on('change','#addressFrm #postal_code', function () {
        var $addFld = $('#addressFrm');
        var pincode = $('#postal_code',$addFld).val();
        var country_id = $('#addressFrm #country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				beforeSend:function(){
					$('#addressFrm  #city_id').empty();
				},
				success: function (OP) {
                $('#addressFrm  #state, #addressFrm #district').prop('disabled', false).empty();	
                    $('#state').append($('<option>', {value: OP.state_id}).text(OP.state));
                    $('#district').append($('<option>', {value: OP.district_id}).text(OP.district));
				     $.each(OP.cities, function (k, e) {
                        $('#city_id').append($('<option>', {value: e.id}).text(e.text));
                    });
				},
				error: function () {
					$('#state_id, #city_id',$addFld).empty().prop('disabled', true);									
				}
			});
		}	
      
    });	
	$(document.body).on('click','.close_btn',function (e) {
        e.preventDefault();
        $('#view_details,#change_Member_pwd,#edit_details,#change_email,#change_mobile,#change_Member_security_pin').fadeOut('fast', function () {
            $('#users-list-panel').fadeIn('slow');
        });
      });
	  
	$(document).on('click','.pwdHS',function(e){
	   e.preventDefault();
		var x = $("#new_pwd").attr('type');
		if (x === 'password') {
			$("#new_pwd").attr('type', 'text');
			$(this).find('i').attr('class','').attr('class','fa fa-eye');
		} else {
			$("#new_pwd").attr('type', 'password');
			$(this).find('i').attr('class','').attr('class','fa fa-eye-slash');
		}	 
    });

});


