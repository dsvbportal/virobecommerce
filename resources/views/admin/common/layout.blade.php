@extends('mainLayout')
@section('main-title')
@if (trim($__env->yieldContent('title')))
Admin | @yield('title')
@else
Admin
@endif
@stop
@section('head-style')
 <link href="{{asset('resources/assets/admin/new_theme/css/bootstrap min.css')}}" rel="stylesheet">
   <link href="{{asset('resources/assets/admin/new_theme/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('resources/assets/admin/new_theme/css/custom.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
	<link href="{{asset('http://50.28.18.145/affstaging/resources/assets/admin/css/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">


<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/dataTables/media/DT_bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/dataTables/extras/TableTools/media/css/TableTools.css')}}">
@stop
@section('head-script')
	<script src="{{asset('resources/assets/admin/new_theme/js/jquery min.js')}}"></script>
@stop
@section('body')
<body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="jss416"><div class="jss420 jss421"><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIyLjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAzMDAgNDcuMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzAwIDQ3LjE7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojRkFGQUZBO30KPC9zdHlsZT4KPHBhdGggY2xhc3M9InN0MCIgZD0iTTMwMCw0Ni45TDAsNDcuMVY4LjljMCwwLDIxLjEsMTQuMyw2NS4yLDE0LjFjNDAuNi0wLjIsNzYuNC0yMywxMjgtMjNDMjQzLjMsMCwzMDAsMTYuNCwzMDAsMTYuNFY0Ni45eiIvPgo8L3N2Zz4K" alt="decoration" class="jss423"></div></div>
      	@include('admin.new_theme.left_nav')
              	@include('admin.new_theme.top_nav')
	 </div>
      </div>
	  <div class="row">
	  <div class="freeaffiliatemain col-md-12">	  
     @yield('layoutContent')	 
	 </div>
	 </div>
    <script src="{{asset('resources/assets/admin/new_theme/js/bootstrapmin.js')}}"></script>
    <script src="{{asset('resources/assets/admin/new_theme/js/custom.min.js')}}"></script>


	<script src="{{asset('resources/assets/admin/js/jquery.validate.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/Datatable/js/jquery.dataTables.js')}}"></script>
	<link rel="stylesheet" href="{{asset('resources/assets/admin/Datatable/css/buttons.dataTables.min.css')}}">
	<script src="{{asset('resources/assets/admin/Datatable/js/dataTables.buttons.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/Datatable/js/buttons.print.min.js')}}"></script>

	<script src="{{asset('resources/assets/admin/datatables/dataTables.bootstrap.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/jquery-ui.min.js')}}"></script>

	<link rel="stylesheet" href="{{asset('resources/assets/admin/css/jquery-ui.css')}}">
    <script src="{{asset('resources/assets/admin/js/jquery.tagsinput.js')}}"></script>
	<script src="{{asset('resources/supports/jquery.form.js')}}"></script>

	<script src="{{asset('resources/assets/admin/js/jquery.ba-resize.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/jquery_cookie.min.js')}}"></script>



	<script src="{{asset('resources/assets/admin/js/tinynav.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/lib/multi-select/js/jquery.multi-select.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/jquery.quicksearch.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/chosen.jquery.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/chosen.ajaxaddition.jquery.js')}}"></script>

	<script src="{{asset('resources/assets/admin/js/lib/select2/select2.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/lib/iCheck/jquery.icheck.min.js')}}"></script>

	<script src="{{asset('resources/assets/admin/js/lib/jquery-steps/jquery.steps.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/js/lib/parsley/parsley.min.js')}}"></script>

   <script src="{{asset('resources/assets/admin/js/lib/datepicker/js/bootstrap-datepicker.js')}}"></script>
   <script src="{{asset('resources/assets/admin/js/lib/jQuery-slimScroll/jquery.slimscroll.min.js')}}"></script>
   <script src="{{asset('resources/assets/admin/js/lib/navgoco/jquery.navgoco.min.js')}}"></script>
   <script src="{{asset('resources/assets/admin/js/lib/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
   <script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
   <script src="{{asset('resources/assets/admin/js/jquery.uploadfile.min.js')}}"></script>

   <script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>
   <script src="{{asset('resources/supports/app.js')}}"></script>
   <script src="{{asset('resources/supports/Jquery-loadSelect.js')}}"></script>
   <script src="{{asset('resources/supports/admin/background_login.js')}}"></script>
   <script src="{{asset('resources/assets/js/lib/ckeditor/ckeditor.js')}}"></script>

   <script src="{{asset('resources/supports/pushNotifications.js')}}"></script>
   <script src="{{asset('https://www.gstatic.com/firebasejs/3.9.0/firebase.js')}}"></script>
   @yield('scripts')
   <script>
   $(document).ready(function () {
	$('.radioBtn a').on('click', function(){
			var sel = $(this).data('title');
			var tog = $(this).data('toggle');
			var para = $(this).closest('.input-group');
			$('#'+tog,para).prop('value', sel);
			$('a[data-toggle="'+tog+'"]',para).not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
			$('a[data-toggle="'+tog+'"][data-title="'+sel+'"]',para).removeClass('notActive').addClass('active');
		});
	});
	</script>
</body>
@stop
