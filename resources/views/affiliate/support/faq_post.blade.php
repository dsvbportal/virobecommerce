@extends('affiliate.layout.dashboard')
@section('title',"FAQs")
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>FAQ's</h1>
  <ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	<li>Support</li>
	<li class="active">FAQ's</li>
  </ol>
</section>
<section class="content">
	<!-- Small boxes (Stat box) -->
	<div class="row">        
		<!-- ./col -->
		<div class="col-sm-12">	
			<div class="panel panel-default">
				<div class="panel-body">
					<!--form class="faq-form form form-bordered" name="faq" method="post" action="{{URL::to('account/support/faqs/search-term')}}">
						{!! csrf_field() !!} 
						<div class="input-group">
							<input type="text" name="faq_word" placeholder="Enter Search Keywords" value="{{(isset($faq_word))?$faq_word:''}}">
							<span class="input-group-btn">
							  <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i> Search</button>
							</span>
						 </div>
					</form-->					
					<!-- Main content -->
					@if(!empty($faqs))
					@foreach($faqs as $key=>$val)
					<ul class="acc-tab">									   
						<li class="acc-item">
							<a href="javascript:void(0)" class="acc-title"><span>{{++$key}}.</span>{{$val->questions}} <span class="pull-right"><i class="fa fa-plus"></i></a></span>			
							<div class="acc-body">{{$val->answers}}</div>
						</li>
					</ul>
					@endforeach
					@endif
					<!-- /.row -->	
					<!-- /.content -->
				</div>
			</div>
		</div>
	</div>
</section>		
@stop