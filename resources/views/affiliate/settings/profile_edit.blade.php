@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/profile.my_profile'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/profile.my_profile')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li >{{trans('affiliate/profile.page_title')}}</li>
        <li class="active">{{trans('affiliate/profile.my_profile')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->		
		@if(!empty($profileInfo) && !empty($userSess))		
        <div class="row" id="profile_edit"> 
            <div class="col-md-8">		
				<!-- About Me Box -->
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-map-marker margin-r-5"></i>{{trans('affiliate/account/profile.edit')}}</h3>
					</div>
					<div class="box-body">
						<p><span class="text-muted">Note: If you want to change your fixed settings, contact the Customer Support Team (or) Submit Your Request</span></p>
					</div>	
				</div>
				<!-- /.box -->
			</div>
		</div>	
		@endif
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts') 
<script src="{{url('affiliate/validate/lang/update_profile_image')}} " charset="utf-8"></script>
<script src="{{asset('assets/user/plugins/cropper/cropper.js')}}" ></script>
<script src="{{asset('assets/user/plugins/cropper/main.js')}}" ></script> 
<link rel="stylesheet" href="{{url('assets/user/plugins/cropper/cropper.css')}}" >
<script src="{{asset('js/providers/affiliate/account/updateprofileimage.js')}}" ></script> 
<script src="{{asset('js/providers/affiliate/account/profile.js')}}" ></script> 
@stop  