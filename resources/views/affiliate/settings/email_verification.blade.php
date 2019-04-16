@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/settings/verify_email.change_email'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>{{trans('affiliate/settings/verify_email.title')}}</h1>
  <ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}} </a></li>
	<li>{{trans('affiliate/general.my_account')}}</li>
	<li>{{trans('affiliate/general.profile')}}</li>
	<li class="active">{{trans('affiliate/settings/verify_email.verify_email')}}</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->		
	<div class="row">			
		<div class="col-sm-12">
			@if(isset($msg))
				@if($status==config('httperr.SUCCESS'))
				<div class="alert alert-success">
					<i class="fa fa-check-circle-o" aria-hidden="true" style="font-size:55px;float: left;margin-right: 15px;"></i>
					<h4>Verified Successfully!</h4>{!!$msg!!}
				</div>
				@else
				<div class="alert alert-warning">
					<i class="fa fa-info-circle" aria-hidden="true" style="font-size:55px;float: left;margin-right: 15px;"></i>
					<h4>Sorry</h4>{!!$msg!!}
				</div>
				@endif				
			@endif
		</div>		
	</div>
</section>
<!-- /.content -->
@stop