@extends('affiliate.layout.loginlayout')
@section('title',"Login")
@section('content')
<div class="login-logo">
	<a id="logo" href="{{url('/')}}"><img src="{{asset('resources/assets/themes/affiliate/img/affiliate-logo.png')}}" title="{{$pagesettings->site_name}}</b> Affiliate"></a>	
</div>
<div class="login-box signin">	
	<!-- /.login-logo -->
	<div class="login-box-body">
		<!--<p class="login-box-msg"><b>Welcome to Virob!</b> Please login..</p>-->
		<h4 class="text-center">VIROB INFLUENCER PROGRAM</h4>
		<p class="text-center text-orange ">
			LOGIN TO VIR-O-B AFFILIATE PROGRAM
		</p>
		<form id="loginfrm"  method="POST" action="{{route('aff.checklogin')}}">
			<div class="form-group has-feedback">
				<input type="text" name="uname" id="uname" class="form-control" placeholder="Email or Affiliate ID" onkeypress="return RestrictSpace(event)">
				<span class="form-control-feedback" style="pointer-events:auto !important; "><i class="fa fa-info-circle" title="In most cases this is the email address associated with your Virob account." aria-hidden="true"></i></span>
			</div>
			<div class="form-group has-feedback">
				<input type="password" name="password" id="password"  class="form-control" placeholder="Enter Password" onkeypress="return RestrictSpace(event)">
				<span style="pointer-events:auto !important; " class="glyphicon form-control-feedback show-pass"><i class="fa fa-eye"></i></span>
				<span style="pointer-events:auto !important; display:none" class="glyphicon form-control-feedback hide-pass"><i class="fa fa-eye-slash"></i></span>
			</div>
			<div class="form-group">
				<div class="row">					
					<div class="col-xs-12">	
				   <div class="checkbox icheck">
					  <input type="checkbox" id="user_remember_me" checked> REMEMBER ME
				  </div>				
					 
					</div>
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn bg-pink btn-lg btn-block" id="toploginBtn">LOGIN</button>				
			</div>
			
		</form>	
		<div class="col-xs-6"> <a href="" class="btn-forgot checkbox">Forgot password?</a></div>
		<div class="col-xs-6"><a href="#" class="checkbox text-right">Home</a></div>
	</div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<div class="login-box forgot"  style="display:none">

  <!-- /.forgot-logo -->
  <div class="login-box-body">
  <h3>FORGOT YOUR PASSWORD?</h3>
    <form id="forgotfrm"  method="POST" action="{{route('aff.forgotpwd')}}">		
		<div class="well no-border bg-purple text-center">Enter your email address. We'll email instructions on how to reset your password. </div>	
      <div class="form-group has-feedback">
        <input type="text" name="uname_id" id="uname_id"  placeholder="Your Email Address" class="form-control" onkeypress="return RestrictSpace(event)">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group"> 
          <button type="submit" class="btn bg-pink btn-lg btn-block btn-flat"  name="topForgotBtn"  id="topForgotBtn">RESET PASSWORD</button>
      </div>
    </form>
	<div class="login-box-footer">
    <a href="{{url('forgotpwd')}}" class="backtoLogin"><b>Back to Login</b></a>    
	</div>
  </div>
  
  <!-- /.login-box-body -->
</div>
<!-- /.forgot-box -->
@stop
@section('scripts')
<script type="text/javascript" src="{{asset('js/providers/affiliate/login.js')}}"  ></script>

@stop