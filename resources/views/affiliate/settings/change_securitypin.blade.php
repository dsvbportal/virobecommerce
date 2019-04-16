@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/settings/security_pwd.page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-home"></i> {{\trans('affiliate/settings/security_pwd.page_title')}}</h1>
     
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Settings</li>
        <li class="active">{{\trans('affiliate/settings/security_pwd.breadcrumb_title')}}</li>
      </ol>
    </section>
	<!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row"  id="resetspwd">        
            <div class="col-md-6">
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">{{\trans('affiliate/settings/security_pwd.change')}}</h3>
                    </div>
                    <div class="panel-body">
					<form  method="post" id="changetranscationpassword" onsubmit="return false;" action="{{route('aff.settings.securitypin.reset')}}">
						<input type="hidden" name="_token" id="csrf-token" value="{!! csrf_token() !!}"/>
						<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputEmail1">{{\trans('affiliate/settings/security_pwd.current_password')}}</label>
								<div class="input-group">
									<input type="password" id="tran_oldpassword" name="tran_oldpassword" class="form-control" id="exampleInputEmail1"
							   placeholder="{{trans('affiliate/settings/security_pwd.current_sec_phn')}}" maxlength="4" data-url="{{route('aff.settings.securitypin.verify')}}">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tran_oldpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>
								<span class="help-block" id="oldpassword_status"></span>
							</div>
						</div>
						</div>
						<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="exampleInputPassword1">{{\trans('affiliate/settings/security_pwd.new_password')}}</label>
								<div class="input-group">
									<input type="password" id="tran_newpassword" name="tran_newpassword" class="form-control"  maxlength="4" id="exampleInputPassword1" 
								  placeholder="{{trans('affiliate/settings/security_pwd.new_security_pin')}}">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tran_newpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>								
								</div>
								<span class="help-block" id="oldpassword_status"></span>
							</div>
						</div>						
						<div class="col-sm-6">
							<div class="form-group">
								<label for="exampleInputPassword2">{{\trans('affiliate/settings/security_pwd.confirm_password')}}</label>
								<div class="input-group">
									<input type="password" id="tran_confirmpassword" name="tran_confirmpassword"  maxlength="4" class="form-control" id="exampleInputPassword2" 
							  placeholder="{{trans('affiliate/settings/security_pwd.confirm_security_pin')}}">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tran_confirmpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>
								<span class="help-block" id="oldpassword_status"></span>
							</div>
						</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6 link_font">
								<a href="{{route('aff.settings.forgot_security_pin')}}" id="forgot_sec_pwd">{{trans('affiliate/settings/security_pwd.forgot_security_pin')}}</a>
							</div>
							<div class="form-group col-md-6 text-right">
								<button name ="Send" type="submit" id="update_securitypwd" class="btn btn-md btn-success"><i class="fa fa-save"></i> {{\trans('affiliate/settings/security_pwd.update_btn')}}
								</button>                    
							</div>			
						</div>					
					</form>
					</div>
				</div>
            </div>
        </div>
		<!-- Small boxes (Stat box) -->
		<div class="row"  id="forgotspwd_otp" style="display:none">        
            <div class="col-md-6">
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Enter Your OTP:</h3>
                    </div>
                    <div class="panel-body">
						<p>Your 6 digit OTP code has been sent to your email address. Please check your inbox.</p>
						<form  method="post" id="forgotspwd_otpfrm" action="{{route('aff.settings.securitypin.forgototp.verify')}}">
							<div class="row">
								<div class="col-md-6">
									<label for="exampleInputEmail1">{{\trans('affiliate/settings/security_pwd.otp')}}</label>
									<div class="input-group input-info">
										<input type="text" class="form-control" name="otp" maxlength="6" id="otp" placeholder="{{trans('affiliate/settings/security_pwd.otp')}}">
										 <span class="input-group-btn">
										   <button class="btn btn-primary" type="submit"  id="verify_otp">Continue</button>
										 </span>
									</div>
								</div>							
							</div>
						</form>
					</div>
				</div>
            </div>
        </div>
		<!-- Small boxes (Stat box) -->
		<div class="row"  id="newspwd" style="display:none">        
            <div class="col-md-6">
				<form  method="post" id="forgotspwd_save" onsubmit="return false;" action="{{route('aff.settings.securitypin.save')}}">	
					<div class="box box-primary">					
						<div class="box-header with-border">
						<h3 class="box-title">Enter your Security PIN</h3>
						</div>
						<div class="panel-body">											
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="exampleInputPassword2">{{\trans('affiliate/settings/security_pwd.new_password')}}</label>
										<div class="input-group">
											<input type="password" id="forgot_tran_newpassword"  maxlength="4" name="tran_newpassword" class="form-control" id="exampleInputPassword1" 
								  placeholder="{{trans('affiliate/settings/security_pwd.new_security_pin')}}">
											<span class="input-group-btn">
												<button class="btn btn-default pwdHS" data-target="#forgot_tran_newpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
											</span>
										</div>										
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="exampleInputPassword2">{{\trans('affiliate/settings/security_pwd.confirm_password')}}</label>
										<div class="input-group">
											<input type="password" id="forgot_tran_confirmpassword"  maxlength="4" name="tran_confirmpassword" class="form-control" id="exampleInputPassword2" 
							  placeholder="{{trans('affiliate/settings/security_pwd.confirm_security_pin')}}">
											<span class="input-group-btn">
												<button class="btn btn-default pwdHS" data-target="#forgot_tran_confirmpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
											</span>
										</div>										
									</div>
								</div>			 
							</div> 		
						</div>
						<div class="panel-footer text-right">							
							<button name ="Send" type="submit" id="forgotspwd_savebtn" class="btn btn-md btn-primary"><i class="fa fa-save"></i> {{\trans('affiliate/settings/security_pwd.update_btn')}}
							</button>  
						</div>
					</div>
				</form>
            </div>
        </div>
	</section>      
@stop
@section('scripts')
<script src="{{asset('affiliate/validate/lang/change-pin.js')}}"></script>
<script src="{{asset('js/providers/affiliate/setting/update_securitypwd.js')}}"></script>
@stop               