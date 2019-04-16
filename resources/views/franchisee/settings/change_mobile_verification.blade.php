@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/settings/change_mobile.change_mobile'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>{{trans('franchisee/settings/change_mobile.change_mobile')}}</h1>
  <ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('franchisee/general.dashboard')}} </a></li>
	<li>{{trans('franchisee/general.my_account')}}</li>
	<li>{{trans('franchisee/general.profile')}}</li>
	<li class="active">{{trans('franchisee/settings/change_mobile.change_mobile')}}</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->		
	<div class="row">
		@if(isset($verify_new_mobile) && $verify_new_mobile)
		<div class="col-sm-9"> 
			<div class="box box-primary">				
				<div class="box-body">
					<form action="{{route('fr.settings.changemobile.send_otp')}}" method="post" class="form-horizontal form-bordered" id="change-mobile-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
						<div class="form-group" id="error">
							<label class="col-sm-4 control-label" for="oldemail">{{trans('franchisee/settings/change_mobile.current_mobile')}}</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="crnt_mobile" value="{{$userSess->mobile}}" disabled="disabled">
							</div>
						</div>

						
						<div class="form-group">
							<label class="col-sm-4 control-label">{{trans('franchisee/settings/change_mobile.new_mobile_id')}} <span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input type="text" id="mobile" name="mobile" class="form-control" placeholder="{{trans('franchisee/settings/change_mobile.enter_new_mobile')}}" data-err-msg-to="#new_mobile_err"   onkeypress="return isNumberKey(event)">
								<span id="new_mobile_err"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{trans('franchisee/general.tpin')}} <span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<div class="input-group">
									<input type="password" id="tpin" name="tpin" class="form-control" placeholder="{{trans('franchisee/settings/change_mobile.enter_tpin')}}" data-err-msg-to="#tpin_status" autocomplete="false"  maxlength="4" onkeypress="return isNumberKey(event)">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tpin" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>
								<span class="help-block" id="tpin_status"></span>
							</div>
							
						</div>						
						<div class="form-group form-actions">
							<div class="col-sm-12 col-sm-offset-4">
								<button name="verification_now" type="submit" class="btn btn-primary" id="verification_now"><i class="fa fa-angle-right"></i> Continue</button>
							</div>
						</div>
					</form>
					<form action="{{route('fr.settings.changemobile.verification_otp')}}" style="display:none" method="post" class="form-horizontal form-bordered" id="code_verify_form" autocomplete="off" onsubmit="return false;" novalidate="novalidate" >
				
						<div class="form-group">
							<label class="col-sm-4 control-label" for="otp">{{trans('franchisee/general.enter-otp')}}</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="verify_code" id="verify_code" value="" maxlength="6" onkeypress="return isNumberKey(event)" placeholder="{{trans('franchisee/general.enter-otp')}}">
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