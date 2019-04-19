$(document).ready(function () {	

    var profile_update=$('#profile_updatefrm');
	$('#dob').datepicker({ 
	    maxDate: '-18y',
	    dateFormat: 'yy-mm-dd',	
		autoclose: true
	});
	
	/* Update Profile */
    profile_update.on('submit', function (e) {
        e.preventDefault();
        CURFORM=profile_update;
		var data = new FormData();		
		if (CROPPED) {					    
            data.append('attachment', uploadImageFormat($('#attachment-preview').attr('src')));			
            CROPPED = false;
        }		
        $.each(CURFORM.serializeObject(), function (k, v) {					
            data.append(k, v);
        }); 				
        $.ajax({
            url: profile_update.attr('action'),	
            type: 'POST',
            enctype: 'multipart/form-data',			
			processData: false,
            contentType: false,
            //data: profile_update.serializeObject(),
            data: data,
            success: function (op) {
			    console.log(op);
				//$('#first_name',profile_update).val(op.first_name);
				//$('#last_name',profile_update).val(op.last_name);				
            }
        });
    });		
	

    /****** virob ambika*******/

          /******personal info *******/

    $('#change_mobile_tab').on('click', function (e) {
     e.preventDefault();   
        CURFORM='';
        $.ajax({
            url: $('#change_mobile_tab').attr('ctr_url'),               
            type:'get',
            dataType:'json',
            success: function (op) {
                console.log(op);
                if(op.status == 200){
                    $('#mob_div').html(op.content);
                }

            }               
        });        
    });


    $('#change_email_tab').on('click', function (e) {

     e.preventDefault();   
        CURFORM='';
        $.ajax({
            url: $('#change_email_tab').attr('ctr_url'),               
            type:'get',
            dataType:'json',
            success: function (op) {
                console.log(op);
                if(op.status == 200){
                    $('#email_div').html(op.content);
                }

            }               
        });        
    });

     $('.main-shop-page').on('click','#change_bank_tab', function (e) {

             e.preventDefault();   
             CURFORM='';             
                $.ajax({
                    url: $('#change_bank_tab').attr('ctr_url'),               
                    type:'get',
                    dataType:'json',
                    beforeSend: function(){
                        $('.alert').hide();
                    },
                    success: function (op) {

                            $('#bank_content').html(op.content);             
                    }               
                });        
    

    });


    
    

     $('#idfy').on('click','#relogin_bank', function (e) {
         e.preventDefault();
                CURFORM='';
               $.ajax({
                    url: $('#relogin_bank').attr('setsession'),               
                    type:'post',
                   beforeSend: function(){
                       $('.alert').hide();
                   },
                    
                    success: function (op) {
                        
                                         $.ajax({
                                    url: $('#relogin_bank').attr('ctr_url'),               
                                    type:'post',
                                    dataType:'json',
                                    success: function (op) {
                                         $("#bank_content").empty();
                                         $('#bank_content').html(op.content);
                                                        
                                     }                
                                    });  
                        
                                        
                    }               
                });     

                          

    });


  $('#idfy').on('change','input[type=radio][name=varify]', function (e) {
   
                        if (this.value == 'otp') {
                                  $('#class1').css("display", "none");
                                  $('#OTPdiv').css("display", "none");
                                  $('#class2').css("display", "block");       
                                                  }
                        else{
           
                                  $('#OTPdiv').css("display", "none");
                                  $('#class2').css("display", "none");
                                  $('#class1').css("display", "block");

                              }      
});  

$('#idfy').on('click','#check_relogin_bank', function (e) {
                var pass=$('#password').val();    
                e.preventDefault();   
                CURFORM=$('#bank_frm');
                $.ajax({
                    url: $('#check_relogin_bank').attr('ctr_url'),               
                    type:'post',
                    data:{password:pass},
                    dataType:'json',
                    success: function (op) {                        
                                            if(op.status==200)   
                                            {
                                                 $.ajax({
                                                         url: $('#check_relogin_bank').attr('nxt_url'),               
                                                         type:'get',
                                                         dataType:'json',
                                                          beforeSend: function(){
                                                             $('.alert').hide();
                                                           },
                                                         success: function (op) {
                                                             $("#bank_content").empty();       
                                                             $('#bank_content').html(op.content);             
                                                                                }               
                                                      }); 
                                            }  
                                          }               
                    }); 

                 }); 

$('#idfy').on('click','#save_bank_btn', function (e) {
    e.preventDefault();
    CURFORM=$('#bank_frm'); 

                      $.ajax({
                              url: $('#save_bank_btn').attr('ctr_url'),               
                              type:'post',
                              data: $('#bank_frm').serializeObject(),
                              dataType:'json',
                               beforeSend: function(){
                                 $('.alert').hide();
                                },
                              success: function (op) {
                                 $("#bank_content").empty();
                                  $( ".main-shop-page #change_bank_tab" ).trigger( "click" );
                              }
                          }); 

  }); 

$('#idfy').on('click','#send_otp_bank_btn', function (e) {
       e.preventDefault();
       var mobile=$('#password').val();
       CURFORM=$('#bank_frm'); 
     
                                                   $.ajax({
                                                         url: $('#send_otp_bank_btn').attr('ctr_url'),               
                                                         type:'post',
                                                         
                                                         dataType:'json',
                                                           beforeSend: function(){
                                                           $('.alert').hide();
                                                          },
                                                         success: function (op) {
                                                           
                                                                                    
                                                                         $('#class2').css("display", "none");
                                                                         $('#OTPdiv').css("display", "block");
                                                                                }               
                                                      }); 
                                                                                                             
});
$('#idfy').on('click','#varify_otp_bank_btn', function (e) {
       e.preventDefault();
        var otp=$('#otp').val();
        $.ajax({
                 url: $('#varify_otp_bank_btn').attr('ctr_url'),               
                 type:'post',
                 data:{otp:otp},
                 dataType:'json',
                 success: function (op) {
                                         if(op.status==200)
                                         {
                                               $.ajax({
                                                         url: $('#varify_otp_bank_btn').attr('nxt_url'),               
                                                         type:'get',
                                                         // data:{otp:otp},
                                                         dataType:'json',
                                                         beforeSend: function(){
                                                            $('.alert').hide();
                                                           },
                                                         success: function (op) {
                                                             $("#bank_content").empty();       
                                                             $('#bank_content').html(op.content);             
                                                                                }               
                                                      });
                                         }                                
                                         }               
              });                                                    
});  


$('#idfy').on('click','.change_bank_detail', function (e) {
  e.preventDefault();

// var row_id=$('#row_id').val();
// alert(row_id);
 // $row_id=$( ".change_bank_detail" ).closest("id").val();
 var row_id=$(this).attr("id");

  CURFORM='';
                $.ajax({
                    url:$('#bank_frm').attr('ctr_url'),               
                    type:'post',
                     data:{row_id:row_id},
                    dataType:'json',
                    beforeSend: function(){
                        $('.alert').hide();
                    },
                    success: function (op) {
                         $("#bank_content").empty();
                         $('#bank_content').html(op.content);
                                        
                    }               
                });      

   // $('#details_show_div').css("display", "none");
   // $('#update_details_div').css("display", "block");
   

});

$('#idfy').on('click','#save_update_bank_btn', function (e) {
  CURFORM=$('#bank_frm'); 
   e.preventDefault();
         $.ajax({
                    url: $('#save_update_bank_btn').attr('ctr_url'),               
                    type:'post',
                    data:$('#bank_frm').serializeObject(),
                    dataType:'json',
                   beforeSend: function(){
                     $('.alert').hide();
                     },
                    success: function (op) { 
                        $("#bank_content").empty();
                        $( ".main-shop-page #change_bank_tab" ).trigger( "click" );


                    }               
                });
  });

  $('#idfy').on('click','#find_ifsc', function (e) {
  CURFORM=$('#bank_frm');
   
  var ifsc_code=$('#ifsc_code').val();
  // alert(ifsc_code);
  
   e.preventDefault();

       $.ajax({
                    url: $('#find_ifsc').attr('ctr_url'),               
                    type:'post',
                    data:{ifsc_code:ifsc_code},
                    dataType:'json',
                    beforeSend: function(){
                        $('.alert').hide();
                     },
                    success: function (op) { 
                       // console.log(op);                     
                    
                        if(op.status==200)
                        {
                          $("#save_update_bank_btn").prop('disabled', false);
                          $("#save_bank_btn").prop('disabled', false);

                        } 
                         
             
                    }               
                }); 

         
  });  

   $('#idfy').on('change','#ifsc_code', function (e) {
      
      $("#save_update_bank_btn").prop('disabled', true);
      $("#save_bank_btn").prop('disabled', true);

   });


$('#idfy').on('click','.change_status', function (e) {
    e.preventDefault();
    CURFORM=$('#bank_frm');
var status=$(this).attr("status");
var row_id=$(this).attr("row_id");
var url=$(this).attr('ctr_url');

           $.ajax({
                    url: url,               
                    type:'post',
                    data:{status:status,row_id:row_id},
                    dataType:'json',
                    beforeSend: function(){
                       $('.alert').hide();
                      },
                    success: function (op) {  
                         $.ajax({
                               url: $('#change_bank_tab').attr('ctr_url'),               
                               type:'get',
                               dataType:'json',
                               success: function (op) {

                               $('#bank_content').html(op.content);             
                                 }               
                               });     


                         

                                        
                    }               
                }); 
});

$('#idfy').on('click','.delete_bank_detail', function (e) {
    e.preventDefault();
    CURFORM=$('#bank_frm');
  var row_id=$(this).attr("row_id");
  var url=$(this).attr('ctr_url');
  var btn=$(this);

    $.ajax({
                    url: url,               
                    type:'post',
                    data:{row_id:row_id},
                    dataType:'json',
                    beforeSend: function(){
                       $('.alert').hide();
                     },
                    success: function (op) {  

                      btn.closest('.repeat').remove();
                                        
                    }               
                }); 


});

 $('.tabs-area').on('click','#change_address_tab', function (e) {
          
                e.preventDefault();   
                CURFORM='';
                $.ajax({
                    url: $('#change_address_tab').attr('ctr_url'),               
                    type:'get',
                    dataType:'json',
                    success: function (op) {                                                                
                           $('#address_content').html(op.data);               
                    }               
                }); 

    });





    /****** change Email*******/


	
    
    $('#email_div').on('click','#change_email_btn', function (e) {

        var email_change=$('#change_email');
        var email_change_btn=$('#change_email_btn');
        e.preventDefault(); 
       
       
        CURFORM=email_change;
        $.ajax({
            url: email_change.attr('action'),     
            data: email_change.serializeObject(),
            type:'post',
            success: function (op) {
               
                console.log(op);
            }               
        });        
    });

    $('#update_email_btn').on('click', function (e) {
        e.preventDefault();        
        CURFORM = $('#update_email');
        $.ajax({
            url:$('#update_email').attr('action'),     
            data:$('#update_email').serializeObject(),
            type:'post',
            success: function (op) {
                //console.log(op);
            }              
        });
    });	


     /****** virob ambika*******/


    /****** virob subin*******/

    /* Change Mobile */
	  $(document).on('click','#changeMobileButn', function (e) {
        e.preventDefault();
          CURFORM=$('#mob_div');
        $.ajax({
            url: $(this).attr('href'),
            dataType: 'JSON',
            type: 'POST',
            beforeSend:function(){
                $('.alert').remove();
            },
            success: function (data) {
                console.log(data);

                /*   if(data.status == 200){
                       $('#profile_editfrm').before("<div class='alert alert-success'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
                   } else{
                       $('#profile_editfrm').before("<div class='alert alert-danger'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");

                   }*/
            }
        });
    });

    var change_mobil_form = $('#change_mobil_form');
    change_mobil_form.on('submit', function (e) {
            e.preventDefault();
        if($('#old_mobile').val()!=$('#new_mobile').val()){
            CURFORM=change_mobil_form;
            $.ajax({
                url: change_mobil_form.attr('action'),
                data: change_mobil_form.serializeObject(),
                beforeSend:function(){
                    $('.alert').remove();
                },
                success: function (data) {
                    if(data.status == 200){

                        $('#phone_editfrm').before("<div class='alert alert-success'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");

                        $('#change_form_div').hide();
                        $('#otp_div').show();
                    } else {

                        $('#phone_editfrm').before("<div class='alert alert-danger'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");

                        $('#change_form_div').show();
                        $('#otp_div').hide();
                    }
                }
            });
        } else {

            $('#phone_editfrm').before("<div class='alert alert-danger'>"+$mobile_same+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");


        }
    });

    $(document).on('click','#resend_btn', function (e) {
        e.preventDefault();
        change_mobil_form.trigger('submit');
    });
    var otp_mobil_form = $('#otp_mobil_form');
    otp_mobil_form.on('submit', function (e) {
        e.preventDefault();
		CURFORM=otp_mobil_form;
		$.ajax({
			url: otp_mobil_form.attr('action'),
			data: otp_mobil_form.serializeObject(),
			beforeSend:function(){
				$('.alert').remove();
			},
			success: function (data) {
				/*if(data.status == 200){
					$('#phone_editfrm').before("<div class='alert alert-success'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");

				}
			   else if(data.status == 422){
					$('#phone_editfrm').before("<div class='alert alert-success'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");

				}
				else {
					$('#phone_editfrm').before("<div class='alert alert-danger'>"+data.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");

				}*/
			}
		});
	});


    /****end** virob subin*******/

	
	/* Add Address */
	
	var ADF = $('#addressFrm');
	
	$('.container').on('click','#add_address,.addAddressBtn',function (e) {
        e.preventDefault();      
        var CURELE = $(this);
		var address_head = $(this).data('heading');
		var addr_type = (CURELE.attr('id') != 'add_address') ? CURELE.attr('data-type') :'';
		//alert(address_head);
		$.ajax({
            url: $('#account_address').attr('data-url'),
			data: {type: addr_type},
			type:'POST',
            beforeSend: function (op) {                
				$('#address-model .modal-title span').text(address_head);
				$('#address-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){
				$('#address-model .modal-body').html(op.template);
				$('#address-model').modal('show');
			}	
        });		
	});



    /*  Edit Address */
	$(document).on('click','.editAddressBtn',function (e) {
        e.preventDefault();
        var CURELE = $(this);
		var address_head = $(this).data('heading');
		$.ajax({
            url: $('#account_address').attr('data-url'),
            data: {address_type: CURELE.attr('data-type')},
			type:'POST',
            beforeSend: function (op) {                
				$('#address-model .modal-title span').text(address_head);
				$('#address-model .modal-body').empty().append('<p>Loading</p>');
            },
			success:function(op){			
				$('#address-model .modal-body').html(op.template);		
                $('#addressFrm #postal_code').trigger('change');	
				$('#address-model').modal('show');
			}	
        });		
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
				url: $('#account_address').attr('data-pincode-url'),
				data: {pincode: pincode,country_id:country_id},
				beforeSend:function(){
					$('#city_id',ADF).empty();
				},
				success: function (op) {
					$('#save_chng',ADF).attr('disabled',false);
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
				error: function () {				
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
				if(xhr.status == 200){
					$('#account_address #new_addr').hide();
					$('#account_address #billing_addr').show();
					$('#account_address #shipping_addr').show();
					console.log(op.addtype);
					if(op.addtype!='' && op.address){
						$('#'+op.addtype+'_addr').show();					
						$('#'+op.addtype+'Addr').text(op.address);
                     	$('#'+op.addtype+'_addr .editAddressBtn').show();
						$('#'+op.addtype+'_addr .addAddressBtn').hide();						
					}
					$('#account-details').prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					$('#address-model').modal('hide');
					
					CURFORM.data('errmsg-fld','#offc-info');
					CURFORM.data('errmsg-placement','before');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
			},
			error: function (jqXHR, exception, op) {

				$('#address-model #addressSaveBtn').attr('disabled',false);
			}
        });
    });	
	
	var change_pwd=$('#change_pwdfrm');	
	/* Change Password */
    change_pwd.on('submit', function (e) {
        e.preventDefault();		
        CURFORM=change_pwd;
        $.ajax({
            url: change_pwd.attr('action'),		
            data: change_pwd.serializeObject(),
            success: function (op) {			   
				$('input',change_pwd).val('');				
            }
        });
    });
	
			
	/* $('#attachment-preview').on('click', function(){
		alert('ddfsasdas');
		$('#attachment').trigger('click');
	});		 */
			
	/* $('#get_address').on('click', function(e){
		 e.preventDefault();
		alert('dfgsd');
	}); */
	
 	/* $(document).on('click','#get_address',function (e) {
        e.preventDefault();	
		$.ajax({
            url: $(this).data('url'),
			type:'GET',        
			success:function(op){
				$('#address-model .modal-body').html(op.template);		
                $('#addressFrm #postal_code').trigger('change');					
				$('#address-model').modal('show');
			}	
        });		
	}); */
	
	/* $(document).on('click', '#addressSaveBtn', function (e) {
		$('#addressFrm').submit();
	});
	 */

	 
});
