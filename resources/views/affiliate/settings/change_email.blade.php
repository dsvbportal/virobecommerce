<form action="{{route('aff.settings.emailverification')}}" method="post" class="form-horizontal form-bordered" id="change-email-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
	<div class="form-group" id="error">
		<label class="col-sm-4 control-label" for="oldemail">{{trans('affiliate/general.current_email_id')}}</label>
		<div class="col-sm-8">
			<input type="email" class="form-control" id="crnt_email" value="{{$userSess->email}}" readonly="readonly">
		</div>
	</div>
	<?php /*<div class="form-group">
		<label class="col-sm-4 control-label" for="newemail">{{trans('affiliate/general.new_email_id')}}<span class="text-danger">*</span></label>
		<div class="col-sm-8">
			<input type="email" id="email" name="email" class="form-control" placeholder="{{trans('affiliate/settings/change_email.enter_new_email_id')}}">
			<div id="email_avail_status"></div>
		</div>
	</div>
	*/?>
	<div class="form-group form-actions">
		<div class="col-sm-12 col-sm-offset-4">
			<button name="Send" type="submit" class="btn btn-primary" id="send_verification_code"><i class="fa fa-angle-right"></i> Send Verification</button>
		</div>
	</div>
</form>				
<script src="{{url('affiliate/validate/lang/change-email')}}" charset="utf-8"></script> 
<script type="text/javascript" src="{{asset('js/providers/affiliate/setting/change_email.js')}}"  ></script>