$(document).ready(function () {
    var CUSR = $('#create_user');
	var now = new Date();	
	var year_str = '<option value="">Year</option>';
	for (var i = (now.getFullYear() - 18); i >= 1908; i--){
		year_str = year_str + '<option value="' + i + '">' + i + '</option>';	
	}	
	$('#dob_year',CUSR).html(year_str);
	
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
	
	
	CUSR.on('change', '#country', function () {
        var selected = $('#country option:selected');
        $('input.country-phonecode').val(selected.data('phonecode'));
        $('span.country-phonecode').text(selected.data('phonecode'));
        $('.country-flag').attr('src', selected.data('flag'));
        $('#mobile').attr('pattern', selected.data('mobile_validation'));
	     $cForm = $(this).closest('form');
		 var id = $("#country").val();
			  if(id>0){
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
    $('#country', CUSR).trigger('change');
	  CUSR.on('change', '#state', function () {
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
	
  
	 CUSR.on('submit', function (e) {
        e.preventDefault();
        CURFORM = CUSR;
        $.ajax({
            url: CUSR.attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: CUSR.serialize(),
            beforeSend: function () {
                $(':submit', CUSR).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                $('input,select',CURFORM).val('');
				 $(':submit', CUSR).attr('disabled', false).val('Continue');
            },
            error: function (jqXhr) {
                $(':submit', CUSR).removeAttr('disabled', true).val('Continue');
            }
        });
    });
	
    $('#mobile', CUSR).on('keypress', function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
            return false;
        }
        return true;
     });
 });