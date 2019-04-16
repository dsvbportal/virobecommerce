@extends('affiliate.layout.loginlayout')
@section('title',"Login")
@section('content')
<div class="login-logo">
    <a id="logo" href="{{url('/')}}"><img src="{{asset('resources/assets/themes/affiliate/img/affiliate-logo.png')}}" title="{{$pagesettings->site_name}}</b> Affiliate"></a>
	</div>
<div class="login-box signin" style="width:500px">		
	<!-- /.login-logo -->
	<div class="login-box-body">
		<h3 class="text-center">Reset Password</h3>
		@if($pwd_resetfrm)
		<form id="password-resetfrm" action="{{route('aff.pwdreset-save')}}" method="post">			
			<p class="text-center"><b>Enter your new password for your {{$siteConfig->site_name}} account.</b></p>
			<div class="form-group">
				<input name="token" id="token" required="1" data-valuemissing="Token is required." type="hidden" value="{{(isset($token) && !empty($token))? $token:''}}">
			</div>					
			<div class="form-group">				
				<div class="input-group">   <!-- pattern="/^\S{6,20}$/" data-patternmismatch="New Password must 6 to 20 characters"-->
					<input name="newpassword" id="newpassword" title="New Password" placeholder="New Password" is_editable="0" is_visible="0" type="password" class="form-control" data-err-msg-to="#newpwd_err">
					<span class="input-group-addon pwdHS"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
				</div>
				<span id="newpwd_err"></span>				
			</div>
			<div class="form-group">
				<div class="input-group">  
					<input name="confirmpassword" id="confirmpassword" title="Confirm New Password" placeholder="Confirm New Password" required="1" is_editable="0" is_visible="0" type="password" class="form-control" data-err-msg-to="#confirm_newpwd_err" >
					<span class="input-group-addon pwdHS"><i class="old_pin fa fa-eye-slash" aria-hidden="true"></i></span>
				</div>
				<span id="confirm_newpwd_err"></span>		
			</div>			
			<div class="form-group">				
				<button type="submit" class="btn btn-lg btn-primary btn-block">Reset my password</button>				
			</div>
			
		</form>
		<div class="well"><ul><li>The new password should be at least 6 charactes long.</li><li>It must contain upper and lower case characters and at least one number.</li></ul></div>
		<div class="login-box-footer">
    <a href="{{route('aff.login')}}">Login with your account</a>    
	</div>
		@endif
		@if(!$pwd_resetfrm)
		{{$msg}}
		@endif	
	</div>
</div>
@stop
@section('scripts')
<script type="text/javascript" src="{{asset('js/providers/affiliate/login.js')}}"  ></script>
@stop