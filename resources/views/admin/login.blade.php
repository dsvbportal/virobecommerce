@extends('mainLayout')
@section('main-title','Admin | Login')
@section('head-style')
<link rel="stylesheet" href="{{asset('resources/assets/admin/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/todc-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/style.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/theme/color_1.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('http://fonts.googleapis.com/css?family=Roboto:300&amp;subset=latin,latin-ext')}}">
<style>
    body {padding:80px 0 0}
    .container-panel{
        min-height: 100%;
    }
    textarea, input[type="password"], input[type="text"], input[type="submit"] {-webkit-appearance: none}
    .navbar-brand {font:300 15px/18px 'Roboto', sans-serif}
    .login_panel {background:#f8f8f8;padding:20px;-webkit-box-shadow: 0 0 0 4px #ededed;-moz-box-shadow: 0 0 0 4px #ededed;box-shadow: 0 0 0 4px #ededed;border:1px solid #ddd;position:relative}
    .login_head {margin-bottom:20px}
    .login_head h1 {margin:0;font:300 20px/24px 'Roboto', sans-serif}
    .login_submit {padding:10px 0}
    .login_panel label a {font-size:11px;margin-right:4px}
    .login_panel img{width:100%;}
    @media (max-width: 767px) {
        body {padding-top:40px}
        .navbar {display:none}
    }
</style>
@stop
@section('head-script')
<!--[if lt IE 9]>
<script src="{{asset('resources/assets/admin/js/ie/html5shiv.js')}}"></script>
<script src="{{asset('resources/assets/admin/js/ie/respond.min.js')}}"></script>
<![endif]-->
@stop
@section('body')
<body>
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>    
    <div class="container-panel col-xs-12 col-sm-offset-4 col-sm-6 col-md-offset-6 col-md-3 col-lg-offset-4 col-lg-3">
        <div class="login_panel login-panel" {{isset($login) && $login ? '': 'style=display:none'}}>
            <div class="row">
				<div class="col-sm-6 col-sm-offset-3 text-center">
					<img src="{{$pagesettings->site_logo}}" alt="{{$pagesettings->site_name}}">
					<div class="login_head">
						<h1>Admin Login</h1>
					</div>
				</div>
			</div>
            <div id="login_mess" style="color: red; text-align: center;">  <?php echo Session::get('msg');?>  </div>
            <form id="login_form" method="POST" action="{{URL::to('admin/login')}}">
                <div class="form-group">
                    <label for="user_login">{!!$lfields['username']['label']!!}</label>
                    <input id="user_login" class="form-control input-lg" {!!build_attribute($lfields['username']['attr'])!!}/>
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="form-group">
                    <label for="user_password">{!!$lfields['password']['label']!!}</label>
                    <input id="user_password" class="form-control input-lg" {!!build_attribute($lfields['password']['attr'])!!}/>
                    <span class="errmsg_yellow" style="display:none"></span>
                    <label class="checkbox"><input type="checkbox" name="login_remember" id="login_remember"> Remember me</label>
                </div>
                <div class="login_submit">
                    <button class="btn btn-primary btn-block btn-lg" type="submit"  name="signmebutton" id="login_button">LOGIN</button>
                </div>
                <div class="text-center">
                    <a class="form_toggle" id="forgot-password-btn" href="#"> Forgot password?</a>
                </div>
            </form>
        </div>
        <div class="login_panel forgor-pwd-panel" {{isset($forgot_password) && $forgot_password?'': 'style=display:none'}}>
            <div class="text-center">
                <img src="{{$pagesettings->site_logo}}" alt="{{$pagesettings->site_name}}">
                <div class="login_head">
                    <h1>Admin Forgot Password</h1>
                </div>
            </div>
            <form action="{{route('admin.forgot-password')}}" id="forgot-password">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control input-lg" value="" >
                </div>
                <div class="login_submit">
                    <button type="button" class="btn btn-primary btn-block btn-lg send-verification-code" data-url="{{route('admin.check-account')}}"><i class="fa fa-user"></i>Check</button>
                </div>
                <div class="reset-verification" style="display: none;">
                    <div class="form-group">
                        <label for="verification_code">Verification Code</label>
                        <input id="verification_code" name="verification_code" placeholder="Verification Code" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" data-err-msg-to="#pass_key-group" class="form-control"/>
                            <span class="input-group-btn"><button type="button" id="show-hide-password" data-alternative="Hide" class="btn btn-info">Show</button></span>
                        </div>
                        <span id="pass_key-group"></span>
                    </div>
                    <div class="login_submit">
                        <button class="btn btn-primary btn-block btn-lg">Submit</button>
                    </div>
                </div>
                <div class="text-center">
                    Never mind, <a class="form_toggle" id="login-btn" href="#">send me back to the Login screen</a>
                </div>
            </form>
        </div>
    </div>
    @include('admin.common.assets')    
	<script src="{{asset('resources/assets/admin/js/jquery.min.js')}}"></script>
	<script src="{{asset('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js')}}"></script>
	<script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>
	<script src="{{asset('resources/supports/app.js')}}"></script>
	<script src="{{asset('resources/supports/admin/login.js')}}"></script>
</body>
@stop
