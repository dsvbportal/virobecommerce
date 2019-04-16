@extends('franchisee.layout.loginlayout')
@section('title',"Login")
@section('content')
<div class="login-box signin">
	<div class="login-logo">
		<a id="logo" href="{{url('/')}}"><img src="{{asset('resources/assets/themes/franchisee/img/logo.png')}}" title="{{$pagesettings->site_name}}</b> Affiliate"></a>
	</div>
	<!-- /.login-logo -->
	<div class="login-box-body">
		<h3>Login</h3>
		<form id="loginfrm"  method="POST" action="{{route('fr.checklogin')}}">
			<div class="form-group has-feedback">
				<input type="text" name="uname" id="uname" class="form-control" placeholder="Enter Username (or) Email Id" onkeypress="return RestrictSpace(event)">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
				<input type="password" name="password" id="password"  class="form-control" placeholder="Enter Password" onkeypress="return RestrictSpace(event)" maxlength="16">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-xs-6">
					  <div class="checkbox icheck">
						  <input type="checkbox"> Remember Me
					  </div>
					</div>
					<div class="col-xs-6 text-right">
					
					  <a href="" class="btn-forgot checkbox">Forgot password?</a>
					</div>
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-md btn-block" id="toploginBtn">LOGIN NOW</button>				
			</div>
		</form>	
	</div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<div class="login-box forgot"  style="display:none">
  <div class="login-logo">
    <a id="logo" href="{{url('/')}}"><img src="{{asset('resources/assets/themes/franchisee/img/logo.png')}}" title="{{$pagesettings->site_name}}</b> Affiliate"></a>
  </div>
  <!-- /.forgot-logo -->
  <div class="login-box-body">
	<h3>Forgot Your Password?</h3>	
    <form id="forgotfrm"  method="POST" action="{{route('fr.forgotpwd')}}">		
	  <p class="text-center text-muted">Don't worry! Enter your email below and<br> we'll email you with instruction on how to <br>reset your password </p>	
      <div class="form-group has-feedback">
        <input type="text" name="uname_id" id="uname_id"  placeholder="Enter your email" class="form-control" onkeypress="return RestrictSpace(event)">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group"> 
          <button type="submit" class="btn btn-primary btn-block btn-flat"  name="topForgotBtn"  id="topForgotBtn">RESET PASSWORD</button>
      </div>
    </form>
	<div class="login-box-footer">
    <a href="javascript:void()" class="backtoLogin">Return to Login</a>    
	</div>
  </div>
  
  <!-- /.login-box-body -->
</div>
<!-- /.forgot-box -->
@stop
@section('scripts')
<script type="text/javascript" src="{{asset('js/providers/franchisee/login.js')}}"  ></script>
@stop