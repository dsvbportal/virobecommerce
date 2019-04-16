<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <base href="{{url('/')}}/">
  <title>@yield('title') | {{$pagesettings->site_name}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="author" content={{$pagesettings->site_name}}">  
  <meta name="csrf-token" content="{{ csrf_token() }}" />
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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
  folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/dist/css/skins/_all-skins.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/plugins/iCheck/flat/blue.css')}}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/plugins/morris/morris.css')}}">
  <!-- jQuery 2.2.3 -->
<script src="{{asset('resources/assets/themes/franchisee/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="{{asset('resources/assets/themes/franchisee/js/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
  <script src="{{asset('resources/assets/themes/franchisee/js/respond/1.4.2/respond.min.js')}}"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader-wrapper">
    <div id="loader"></div>  
</div>
<!-- ./wrapper -->
<div class="wrapper">
	@include('franchisee.common.dashboard-header')
	@include('franchisee.common.left_nav')
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
	@yield('content')
	</div>
	<!-- Content Wrapper. Contains page content -->	
	@include('franchisee.common.footer')
</div>
<!-- ./wrapper -->

<!-- jQuery UI 1.11.4 -->
<script src="{{asset('resources/assets/themes/franchisee/plugins/jQueryUI/jquery-ui.js')}}"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>$.widget.bridge('uibutton', $.ui.button);</script>
<!-- Bootstrap 3.3.6 -->
<script src="{{asset('resources/assets/themes/franchisee/js/bootstrap.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('resources/assets/themes/franchisee/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{asset('resources/assets/themes/franchisee/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('resources/assets/themes/franchisee/plugins/fastclick/fastclick.js')}}"></script>
<script src="{{asset('resources/assets/themes/franchisee/plugins/validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('resources/assets/themes/franchisee/plugins/validation/additional-methods.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('resources/assets/themes/franchisee/dist/js/app.js')}}"></script>
<script src="{{asset('resources/supports/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('resources/assets/themes/franchisee/dist/js/demo.js')}}"></script>
@yield('scripts')
<script>$('body').toggleClass('loaded');</script>
</body>
</html>
