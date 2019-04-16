<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Virob</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<base href="{{URL::to('/')}}/">
    <!-- Favicons -->
    <link rel="shortcut icon" href="img/favicon.ico">
    <!-- Fontawesome css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/font-awesome.min.css')}}">
    <!-- Ionicons css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/ionicons.min.css')}}">
    <!-- linearicons css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/linearicons.css')}}">
    <!-- Nice select css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/nice-select.css')}}">
    <!-- Jquery fancybox css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/jquery.fancybox.css')}}">
    <!-- Jquery ui price slider css -->
    <!--<link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/jquery-ui.min.css')}}">-->
    <!-- Meanmenu css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/meanmenu.min.css')}}">
    <!-- Nivo slider css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/nivo-slider.css')}}">
    <!-- Owl carousel css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/owl.carousel.min.css')}}">
    <!-- Bootstrap css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/bootstrap.min.css')}}">
    <!-- Custom css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/default.css')}}">
    <!-- Main css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/style.css')}}">
    <!-- Responsive css -->
    <link rel="stylesheet" href="{{asset('resources/assets/themes/shopping/css/responsive.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/plugins/select2/css/select2.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('resources/assets/plugins/notifIt.css')}}"/>
	<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/jquery-ui/jquery-ui.min.css')}}"/>
    <!-- Modernizer js -->
	<script src="{{asset('resources/assets/themes/shopping/js/vendor/jquery-3.2.1.min.js')}}"></script>
	
	<script src="{{asset('resources/assets/app.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/providers/ecom/app-plugins.js')}}"></script>
    <script src="{{asset('resources/assets/themes/shopping/js/vendor/modernizr-3.5.0.min.js')}}"></script>
</head>

<body>
 <div class="wrapper">
	  @yield('home_page_header')
	  @yield('content')
	  @include('shopping.common.footer')
 </div>

   
    <!-- jquery 3.2.1 -->
	<script type="text/javascript" src="{{asset('resources/assets/themes/shopping/js/plugins/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/providers/ecom/home.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/plugins/select2/js/select2.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>	
	<script type="text/javascript" src="{{asset('js/providers/ecom/login.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jQueryUI/jquery-ui.min.js')}}"></script>
    <!-- Countdown js -->
    <script src="{{asset('resources/assets/themes/shopping/js/jquery.countdown.min.js')}}"></script>
    <!-- Mobile menu js -->
    <script src="{{asset('resources/assets/themes/shopping/js/jquery.meanmenu.min.js')}}"></script>
    <!-- ScrollUp js -->
    <script src="{{asset('resources/assets/themes/shopping/js/jquery.scrollUp.js')}}"></script>
    <!-- Nivo slider js -->
    <script src="{{asset('resources/assets/themes/shopping/js/jquery.nivo.slider.js')}}"></script>
    <!-- Fancybox js -->
    <script src="{{asset('resources/assets/themes/shopping/js/jquery.fancybox.min.js')}}"></script>
    <!-- Jquery nice select js -->
    <script src="{{asset('resources/assets/themes/shopping/js/jquery.nice-select.min.js')}}"></script>
    <!-- Jquery ui price slider js -->
    <!--<script src="{{asset('resources/assets/themes/shopping/js/jquery-ui.min.js')}}"></script>-->
    <!-- Owl carousel -->
    <script src="{{asset('resources/assets/themes/shopping/js/owl.carousel.min.js')}}"></script>
    <!-- Bootstrap popper js -->
    <script src="{{asset('resources/assets/themes/shopping/js/popper.min.js')}}"></script>
    <!-- Bootstrap js -->
    <script src="{{asset('resources/assets/themes/shopping/js/bootstrap.min.js')}}"></script>
    <!-- Plugin js -->
    <script src="{{asset('resources/assets/themes/shopping/js/plugins.js')}}"></script>
    <!-- Main activaion js -->
    <script src="{{asset('resources/assets/themes/shopping/js/main.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/plugins/Jquery-loadSelect.js')}}"></script>
	@yield('scripts');
</body>

</html>
  