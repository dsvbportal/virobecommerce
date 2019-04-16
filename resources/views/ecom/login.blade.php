@extends('ecom.layouts.content_page')
@section('pagetitle')
Authentication
@stop
@section('contents')
<div class="page-content">
    <div class="row">
        <div class="col-sm-6">
            <div class="box-authentication">
                <form method="post" id="signupfrm" action="{{URL::to('api/v1/customer/sign-up')}}">
                    <h3>Create an account</h3>
                    <p>Please enter your Mobile No. to create an account.</p>
                    <div class="">
                        <label for="mobile">Mobile No</label>
                        <input id="mobile" name="mobile" placeholder="Mobile No" type="text" class="form-control">
                    </div>
                    <button type="button" class="button send-verification-code" data-url="{{URL::to('api/v1/customer/sign-up/mobile-check')}}"><i class="fa fa-user"></i> Create an account</button>
                    <div class="signup-verification" style="display: none;">
                        <label for="verification_code">Verification Code</label>
                        <input id="verification_code" name="verification_code" placeholder="Verification Code" type="text" class="form-control">
                        <label for="password">Password</label>
                        <input id="password" name="password" placeholder="Password" type="password" class="form-control">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" type="password" class="form-control">
                        <button type="submit" class="button" id="create-account"><i class="fa fa-user"></i> Create an account</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box-authentication">	
			<!--  Login Form  -->
                <form method="post" id="loginfrm" action="{{route('ecom.checklogin')}}" autocomplete="off">
                    <h3>Already registered?</h3>
					<!-- input id="redirect_to_support" name="redirect"  type="hidden" class="form-control" value="{{Url::to($url)}}"-->
					<div class="form-group">
						<label for="username">Email address/Mobile No</label>
						<input id="username" name="username" placeholder="Email address/Mobile No" type="text" class="form-control" onkeypress="return RestrictSpace(event)">
					</div>                   
					<div class="form-group">
						<label for="password_login">Password</label>
						<input id="password" name="password" placeholder="Password" type="password" class="form-control" onkeypress="return RestrictSpace(event)">
					</div>
                    <p class="forgot-pass"><a href="#" id="forgot_password">Forgot your password?</a></p>
                    <button class="button" type="submit"><i class="fa fa-lock"></i> Sign in</button>
                </form>
                <form method="post" id="forgot_pwd" style="display: none;" action="{{route('ecom.reset_pwd')}}" autocomplete="off">
                    <h3>Already registered?</h3>
                    <label for="uname">Email address/Mobile No</label>
                    <input {!!build_attribute($fpfields['uname']['attr'])!!} id="username" placeholder="Email address/Mobile No" class="form-control">
                    <div class="button-grp">
                        <p class="forgot-pass"><a href="#" id="login">Login</a></p>
                        <button type="button" class="button send-verification-code" data-url="{{route('ecom.forgot_pwd')}}"><i class="fa fa-user"></i> Check</button>
                    </div>
                    <div class="reset-verification" style="display: none;">                        
                        <input type="hidden" id="token" value="">
						<div class="form-group">
						    <label for="code">Verification Code</label>
                            <input {!!build_attribute($rpfields['code']['attr'])!!} id="verification_code" name="code" placeholder="Verification Code" type="text" class="form-control">
						</div>
						<div class="form-group">
							<div class="row">
								<label class="col-sm-8" for="password">New Password</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input {!!build_attribute($rpfields['newpwd']['attr'])!!} type="password" id="new_pwd" placeholder="Password" class="form-control" data-err-msg-to="#password_err">
										<span class="input-group-addon pwdHS" data-target="#new_pwd"><i class="fa fa-eye-slash"></i></span>
									</div>
									<span id="password_err"></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<label class="col-sm-8" for="password_confirmation">Confirm Password</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input {!!build_attribute($rpfields['conf_newpwd']['attr'])!!} type="password" id="confirm_wd" placeholder="Confirm Password" class="form-control" data-err-msg-to="#confirm_pwd_err">
										<span class="input-group-addon pwdHS" data-target="#confirm_wd"><i class="fa fa-eye-slash"></i></span>
									</div>
									<span id="confirm_pwd_err"></span>
								</div>
							</div>
						</div>
						<p style="margin-top: 15px;"><a href="#" id="resend-code"><i class="fa fa-refresh" aria-hidden="true"></i> Resend Verification Code</a></p>
                        <button class="button"><i class="fa fa-lock"></i> Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<!-- script src="{{asset('user/validate/lang/login')}} " charset="utf-8"></script -->
<script src="{{asset('validate/lang/login')}} " charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/providers/ecom/login.js')}}"></script> 
@stop
