@extends('mainLayout')
@section('main-title',(trim($__env->yieldContent('title')))?$__env->yieldContent('title'):'')
@section('head-style')

  <!-- Bootstrap 3.3.6 -->
        <!--link rel="stylesheet" href="{{asset('resources/assets/admin/css/bootstrap.min.css')}}"-->

<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/bootstrap/css/bootstrap.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/font-awesome/css/font-awesome.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/select2/css/select2.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/jquery.bxslider/jquery.bxslider.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/owl.carousel/owl.carousel.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/lib/jquery-ui/jquery-ui.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/css/animate.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/css/reset.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/css/style.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/themes/ecom/css/responsive.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('resources/assets/plugins/notifIt.css')}}"/>
@yield('stylesheets')
@stop
@section('head-script')
@stop
@section('body')
<body class="@yield('page-type')">
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <div id="header" class="header">
        @yield('page-header')
    </div>
    @yield('page-content')
    <footer id="footer">
        <div class="container">
            @yield('page-footer')
            <div id="footer-menu-box">
                <p class="text-center">{{$pagesettings->footer_content}}</p>
            </div>
        </div>
    </footer>
    <a href="#" class="scroll_top" title="Scroll to Top" style="display: inline;">Scroll</a>
	<!-- jQuery 2.2.3 -->  
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jquery/jquery-2.2.3.min.js')}}"></script>
	<!--script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jquery/jquery-1.11.2.min.js')}}"></script-->
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/plugins/jquery.unveil.js')}}"></script>
	
	<!-- Bootstrap 3.3.6 -->
    <!--script src="{{asset('resources/assets/admin/js/bootstrap.min.js')}}"></script-->	
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/bootstrap/js/bootstrap.min.js')}}"></script>
	
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/select2/js/select2.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jquery.bxslider/jquery.bxslider.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/owl.carousel/owl.carousel.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jquery.countdown/jquery.countdown.min.js')}}"></script>
	
	<!-- jQuery UI 1.11.4 -->       
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jQueryUI/jquery-ui.min.js')}}"></script>
	<!--script type="text/javascript" src="{{asset('resources/assets/themes/ecom/lib/jquery-ui/jquery-ui.min.js')}}"></script-->
	
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/js/jquery.actual.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('resources/assets/themes/ecom/js/theme-script.min.js')}}"></script>		
	<script type="text/javascript" src="{{asset('resources/supports/jquery.validate.min.js')}}"></script>	
	<script type="text/javascript" src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>	
	<script type="text/javascript" src="{{asset('resources/supports/app.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/providers/ecom/home.js')}}"></script>
	<!--script type="text/javascript" src="{{asset('resources/supports/ecom/app.min.js')}}"></script-->	
	<script type="text/javascript" src="{{asset('resources/supports/ecom/app-plugins.js')}}"></script>	
	<!--script type="text/javascript" src="{{asset('resources/supports/ecom/app-plugins.min.js')}}"></script-->	
	<!-- script type="text/javascript" src="{{asset('resources/supports/ecom/subscribe.js')}}"></script-->	
	<script type="text/javascript" src="{{asset('resources/supports/Jquery-loadSelect.js')}}"></script>	
	<script type="text/javascript" src="{{asset('resources/supports/pushNotifications.js')}}"></script>
	<!-- script type="text/javascript" src="https://www.gstatic.com/firebasejs/3.9.0/firebase.js"></script-->	
    @yield('scripts')
</body>
@stop
