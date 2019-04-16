@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/settings/change_email.change_email'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>{{trans('franchisee/settings/change_email.change_email')}}</h1>
  <ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('franchisee/general.dashboard')}} </a></li>
	<li>{{trans('franchisee/general.my_account')}}</li>
	<li>{{trans('franchisee/general.profile')}}</li>
	<li class="active">{{trans('franchisee/settings/change_email.change_email')}}</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->		
	<div class="row">
		@if(isset($verify_new_email) && $verify_new_email)
		<div class="col-sm-9"> 
			<div class="box box-primary">				
				<div class="box-body">
					<form action="{{route('fr.settings.changeemail.send_otp')}}" method="post" class="form-horizontal form-bordered" id="change-email-verify-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
						<div class="form-group" id="error">
							<label class="col-sm-4 control-label" for="oldemail">{{trans('franchisee/general.current_email_id')}}</label>
							<div class="col-sm-6">
								<input type="email" class="form-control" id="crnt_email" value="{{$userSess->email}}" disabled="disabled">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="newemail">{{trans('franchisee/general.new_email_id')}} <span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input type="text" id="email" name="email" class="form-control" placeholder="{{trans('franchisee/settings/change_email.enter_new_email_id')}}" onkeypress="return RestrictSpace(event)" data-err-msg-to="#new_email_status">
								<span id="new_email_status"></span>	
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Enter Verification Code <span class="text-danger">*</span></label>
							<div class="col-sm-6">
									<input type="text" id="vcode" name="vcode" class="form-control" placeholder="Enter your verification code"  autocomplete="false"  maxlength="8"  >
							</div>
							
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{trans('franchisee/general.tpin')}} <span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<div class="input-group">
									<input type="password" id="tpin" name="tpin" class="form-control" placeholder="{{trans('affiliate/settings/change_email.enter_tpin')}}"  autocomplete="false"  maxlength="4" onkeypress="return isNumberKey(event)" data-err-msg-to="#tpin_status">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tpin" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>		
								<span id="tpin_status"></span>								
							</div>
						</div>						
						<div class="form-group form-actions">
							<div class="col-sm-12 col-sm-offset-4">
								<button name="verification_now" type="submit" class="btn btn-primary" id="verification_now"><i class="fa fa-angle-right"></i> Continue</button>
							</div>
						</div>
					</form>
					<form action="{{route('fr.settings.changeemail.verification_otp')}}" method="post" class="form-horizontal form-bordered" id="verify-otp-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate" style="display:none">						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="otp">{{trans('franchisee/general.enter-otp')}}</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="verify_code" id="verify_code" value=""  maxlength="6" onkeypress="return isNumberKey(event)" placeholder="{{trans('affiliate/general.enter-otp')}}">
							</div>
						</div>
						<div class="form-group form-actions">
							<div class="col-sm-12 col-sm-offset-4">
								<button name="verification_otp" type="submit" class="btn btn-primary" id="verification_otp"><i class="fa fa-angle-right"></i> Verify</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>	
		@else
		<div class="col-sm-12">
			<div class="alert alert-warning"><i class="fa fa-info-circle" aria-hidden="true" style="font-size:55px;float: left;margin-right: 15px;"></i> <h4>Sorry</h4>{{$msg}}</div>
		</div>
		@endif
	</div>
</section>
<!-- /.content -->
@stop
@section('scripts') 
<script src="{{route('fr.lang',['langkey'=>'profile'])}}" charset="utf-8"></script> 
<script type="text/javascript" src="{{asset('js/providers/franchisee/account/profile.js')}}"  ></script> 
@stop