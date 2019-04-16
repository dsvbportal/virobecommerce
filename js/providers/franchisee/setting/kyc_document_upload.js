$(document).ready(function () {
   
    $('#kyc_uploadfrm').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
		var data = new FormData();
		$.each($("input[type='file']",CURFORM), function(i, file) {			
			if(file.files[0] != undefined){
				data.append(file.name, file.files[0]);
			}
		});     
        $.each(CURFORM.serializeObject(), function (k, v) {		
			if(v != ''){
				data.append(k, v);
			}
        });
		$.ajax({
            url: CURFORM.attr('action'),
            data: data,
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function (op) {
			  console.log(op);			  
			    var ERR = false;
			    var ErrMsg = '';
				$.each(op, function (k, v) {					 
				     if(k == 'kyc_status'){
					    $('#offc-info #kyc_submit').show();
					    $('#offc-info #kyc_submit').html(v.submitted_doc+'/'+v.total_doc);
					    $('#offc-info #kyc_pending').hide();
					    $('#offc-info #submitted_on').text(v.submitted_date);
					   return false;
					} 
					if(v.status == true){	
						$('#kyc_uploadfrm input[type=file]').val('');
						$('#'+k,CURFORM).hide();
						$('#'+k,CURFORM).parents('.form-group').find('h4').show();
						$('#'+k,CURFORM).parents('.form-group').find('h4 span').hide();
						$('#'+k,CURFORM).parents('.form-group').find('h4 a').attr('href',v.path);
						$('#'+k,CURFORM).parents('.form-group').find('.cancel').removeClass().addClass('label label-info edit').text($edit);
						$('#'+k,CURFORM).parents('.form-group').find('.edit').show();
						$('#'+k+'_no',CURFORM).attr('disabled','disabled');
						$("#tax").show();
					}else {
					    ERR = true;						
						ErrMsg += (v.msg != undefined && v.msg != '') ? v.msg+'<br>':'';					   
					}
				});	
				if(ERR == true && ErrMsg != ''){
					CURFORM.before('<div class="alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ ErrMsg + '</div>');
				}
            },
			error: function (event, xhr, settings) {
			}
        });        
    });
	
	$('#kyc_uploadfrm').on('click','.edit,.cancel',function () {	    
	    CURELE = $(this);
		CURELE.parents('.form-group').find('input').val('');
		ID = CURELE.parents('.form-group').find('input').attr('id');		
		if(CURELE.hasClass('edit')){			
			CURELE.parents('.form-group').find('h4').hide();
			CURELE.parents('.form-group').find('input').show();			
			CURELE.removeClass().addClass('label label-danger cancel').text($cancel_edit);		
			if(ID == 'pan' || ID == 'tax'){
				ID = ID+'_no';
				console.log(ID);
				$('#'+ID,'#kyc_uploadfrm').attr('disabled',false);
			}
		}else {	
			CURELE.parents('.form-group').find('input').hide();			
			CURELE.parents('.form-group').find('h4').show();			
			CURELE.removeClass().addClass('label label-info edit').text($edit);	
			if(ID == 'pan' || ID == 'tax'){
				ID = ID+'_no';
				$('#'+ID,'#kyc_uploadfrm').attr('disabled',true);
			}
		}		
	});
});
    

