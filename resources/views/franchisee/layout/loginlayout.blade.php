<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <base href="{{url('/')}}/">
  <title>@yield('title') | {{$pagesettings->site_name}}</title> 
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="author" content="">
  <meta name="keywords" content={{$pagesettings->site_name}}">
  <meta name="description" content="{{$pagesettings->site_name}}">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/dist/css/AdminLTE.css')}}">  
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/plugins/iCheck/square/blue.css')}}">
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="{{asset('resources/assets/admin/js/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
  <script src="{{asset('resources/assets/admin/js/respond/1.4.2/respond.min.js')}}"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
        @yield('content')
	<!-- jQuery 2.2.3 -->
	<script src="{{asset('resources/assets/themes/franchisee/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="{{asset('resources/assets/themes/franchisee/js/bootstrap.min.js')}}"></script>
	<script src="{{asset('resources/assets/themes/franchisee/plugins/iCheck/icheck.min.js')}}"></script>
	<script src="{{asset('resources/assets/themes/franchisee/plugins/jquery.validate.min.js')}}"></script>
	<script src="{{asset('resources/assets/themes/franchisee/dist/js/app.js')}}"></script>
	<script src="{{asset('js/providers/affiliate/app.js')}}"></script>
	<script>
	  $(function () {
		var loader = $('input').iCheck({
		  checkboxClass: 'icheckbox_square-blue',
		  radioClass: 'iradio_square-blue',
		  increaseArea: '20%' // optional
		});
	  });
	</script>
	@yield('scripts')
	
</body>
</html>