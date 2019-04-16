$(document).ready(function () {
	var now = new Date();	
	var year_str = '<option value="">Year</option>';

	for (var i = (now.getFullYear() - 18); i >= 1908; i--){
		year_str = year_str + '<option value="' + i + '">' + i + '</option>';	
	}	

	var CUSR = $('#signupFrm');
	var EUSR = $('#extSignupFrm');	

	$('#dob_year',CUSR).html(year_str);
	$('#dob_year',EUSR).html(year_str);	
	
	$(CUSR).on('change','#dob_year',function () {
		var months = {'1': 'Jan', '2': 'Feb', '3': 'Mar', '4': 'Apr', '5': 'May', '6': 'June', '7': 'July', '8': 'Aug', '9': 'Sept', '10': 'Oct', '11': 'Nov', '12': 'Dec'};
		var month_str = '<option value="">Month</option>';
		for (var i = 1; i <= 12; i++)
		{
			month_str = month_str + '<option value="' + i + '">' + months[i] + '</option>';
		}
		$('#dob_month',CUSR).html(month_str);
		$('#dob_day',CUSR).val('');
	});

	$(CUSR).on('change','#dob_month',function () {
		var day_str = '<option value="">Day</option>';
		var year = parseInt($('#dob_year',CUSR).val());
		var month = parseInt($('#dob_month',CUSR).val());
		for (var i = 1; i <= (new Date(year, month, 0).getDate()); i++)
		{
			day_str = day_str + '<option value="' + i + '">' + i + '</option>';
		}
		$('#dob_day',CUSR).html(day_str);
	});

	$(CUSR).on('change','#dob_day',function () {
		var dob = $('#dob_year',CUSR).val() + '-' + $('#dob_month',CUSR).val() + '-' + $('#dob_day',CUSR).val();
		$('#dob',CUSR).val(dob);
	});
	
	
	$(EUSR).on('change','#dob_year',function () {
		var months = {'1': 'Jan', '2': 'Feb', '3': 'Mar', '4': 'Apr', '5': 'May', '6': 'June', '7': 'July', '8': 'Aug', '9': 'Sept', '10': 'Oct', '11': 'Nov', '12': 'Dec'};
		var month_str = '<option value="">Month</option>';
		for (var i = 1; i <= 12; i++)
		{
			month_str = month_str + '<option value="' + i + '">' + months[i] + '</option>';
		}
		$('#dob_month',EUSR).html(month_str);
		$('#dob_day',EUSR).val('');
	});

	$(EUSR).on('change','#dob_month',function () {
		var day_str = '<option value="">Day</option>';
		var year = parseInt($('#dob_year',EUSR).val());
		var month = parseInt($('#dob_month',EUSR).val());
		for (var i = 1; i <= (new Date(year, month, 0).getDate()); i++)
		{
			day_str = day_str + '<option value="' + i + '">' + i + '</option>';
		}
		$('#dob_day',EUSR).html(day_str);
	});

	$(EUSR).on('change','#dob_day',function () {
		var dob = $('#dob_year',EUSR).val() + '-' + $('#dob_month',EUSR).val() + '-' + $('#dob_day',EUSR).val();
		$('#dob',EUSR).val(dob);
	});

	CUSR.on('submit', function (e) {
		e.preventDefault();
		var frmObj = $(this);	
		var btnTxt = $('input[type=submit]', CUSR).attr('disabled', true).val();
		CURFORM = CUSR;
		$.ajax({
			type: 'POST',
			url: frmObj.attr('action'),
			data: frmObj.serialize(),
			dataType: 'json',
			error: function (jqXhr) {                
				$('input[type=submit]', CUSR).attr('disabled', false);
			},        
			beforeSend: function () {				
				$('input[type=submit]', CUSR).attr('disabled', true);
			},
			success: function (op) {											
				if(op.status==200){
					$('.regbox').hide();
					$('.regconfirm').show();
				}
			}
		});
	});
	
	

	var EXUSR = $('#extSignupFrm');	
	EXUSR.on('submit', function (e) {
		e.preventDefault();
		var frmObj = $(this);	
		CURFORM = EXUSR;
		$.ajax({
			type: 'POST',
			url: frmObj.attr('action'),
			data: frmObj.serialize(),
			dataType: 'json',
			beforeSend: function () {				
				$('input[type=submit]', EXUSR).attr('disabled', true).addClass('hidden');
			},
			success: function (op) {											
				if(op.status==200){
					$('.regbox').hide();
					$('.regconfirm').show();
				}
			},
			error: function (jqXhr) {                
				$('input[type=submit]', EXUSR).attr('disabled', false).removeClass('hidden');
			},
		});
	});

	/*$(document).on('click','.signup .backBtn',function(e){
		e.preventDefault();
		alert('sdfg')
		$('#check_acform #acpwdfld,#check_acform #verify_btn').hide();
	});*/
	
	$(document).on('click','#pwdBtn',function(e){
		e.preventDefault();
		if($('.signup #password').attr('type')=='password'){
			$('.signup #password').attr('type','text');
			$('i',$(this)).removeClass('fa-eye-slash').addClass('fa-eye');
		}
		else {
			$('.signup #password').attr('type','password');
			$('i',$(this)).removeClass('fa-eye').addClass('fa-eye-slash');
		}		 
	});
	
	

	$(document).on('click','#chk_acbtn',function(e){		
		e.preventDefault();
		$btn = $(this);
		CURFORM = $('#check_acform');
		$.ajax({
			type: 'POST',
			url: $btn.data('url'),
			data: {login_id:$('#login_id').val()},
			dataType: 'json',		       
			beforeSend: function () {
				$('.alert').remove();
				$btn.addClass('hidden');
			},
			success: function (op) {			
				if(op.allowreg==1){
					if(op.exist==1){						
						$btn.fadeOut('slow',function(){
							$('#check_acform #acpwdfld,#check_acform #verify_btn').fadeIn('slow');
						});					
					}
					else {
						$('#signup_section #'+op.acfld.fld).val(op.acfld.fldval);
						$('#signup_section #country').trigger('change');
						$('#check_acform').fadeOut('slow',function(){
							$('#signup_section').fadeIn('fast');
						});						
					}
				} else {
					$btn.removeClass('hidden');
					$('#check_acform').prepend('<div class="alert alert-'+op.msgclass+'">'+op.msg+'</div>');
				}
			},
			error:function(){
				$btn.removeClass('hidden');
			}
		});
	});


	$(document).on('click','#verify_btn',function(e){		
		e.preventDefault();
		$btn = $(this);
		CURFORM = $('#check_acform');
		$.ajax({
			type: 'POST',
			url: $btn.data('url'),
			data: {acpwd:$('#acpwd').val()},
			dataType: 'json',		       
			beforeSend: function () {				
				$btn.attr('disabled', true);
			},
			error: function () {				
				$btn.attr('disabled', false);
			},
			success: function (op) {
				$btn.attr('disabled', false);
				if(op.status==200){
					$('#extSignupFrm input#confirm_password').closest('.columns').remove();
					$('#extSignupFrm input#mobile').val(op.acinfo.mobile);
					$('#extSignupFrm input#email').val(op.acinfo.email);
					$('#extSignupFrm input#firstname').val(op.acinfo.firstname);
					$('#extSignupFrm input#lastname').val(op.acinfo.lastname);				
					$('#extSignupFrm select#country option').each(function(){
						if($(this).val()==op.acinfo.country_code){
							$(this).attr('selected','selected');
							$('#extSignupFrm select#country').trigger('change');
							return false;
						}
					});				
					$('#extSignupFrm input#postcode').val(op.acinfo.postal_code);
					$('#check_acform').fadeOut('slow',function(){
						$('#extSignupFrm').fadeIn('fast');
					});
					$('input[type=text],select').attr('disabled',true)
					$('input[type=text],select').each(function(){
						if($(this).val()===''){
							$(this).attr('disabled',false);
						} 
					});
				} else {
					$('#check_acform').prepend('<div class="alert alert-'+op.msgclass+'">'+op.msg+'</div>');
				}
			}
		});
	});
	
	$('#check_acform').on('keyup','#login_id',function(e){
		var val = $(this).val();
		if(parseInt(val) && parseInt(val)>0){
			$(this).closest('label').addClass('mobFld');
		}
		else {
			$(this).closest('label').removeClass('mobFld');
		}
	});

	$(document).on('click','#gotoLoginBtn',function(){
		window.location.href=$(this).data('url')
	});
	
	$("#signupFrm #country,#extSignupFrm #country").change(function (e) {
		$cForm = $(this).closest('form');
		$parent = $(this).closest('.input-group-text');
		
		var id = $('option:selected',$(this)).data('id');
		if(id>0){
			$('img',$parent).attr('src',$('option:selected',$(this)).data('flag'));
			$('.pcode',$parent).text($('option:selected',$(this)).data('phonecode'));
			$.ajax({
				type: 'POST',
				url: $('#state',$cForm).data('url'),
				data: {country_id:id},
				dataType: 'json',		       
				beforeSend: function () {				
					$("#state",$cForm).html('<option value="">Loading...</option>');
				},				
				success: function (op) {
					var str = '<option value="">Select State</option>';
					$.each(op.state, function (k, v) {
						str+='<option value="'+v.state_id+'">'+v.state+'</option>';
					});
					$('#state',$cForm).html(str);
				}
			});
		}		
    });
	
    $("#signupFrm #state, #extSignupFrm #state").change(function (e) {
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
	
	$(".regOptFld").click(function (e) {
		$($(this).data('rel')).fadeToggle();
	});		
	
	$("input").on("keypress", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
     });
	 
});

