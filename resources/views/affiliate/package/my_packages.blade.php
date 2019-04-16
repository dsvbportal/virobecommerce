@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/package.mypackage_page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa fa-files-o"></i>{{\trans('affiliate/package.mypackage_page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{\trans('affiliate/package.breadcrumb_title')}}</li>
        <li class="active">{{\trans('affiliate/package/subscriptions.mypackage_page_title')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->			
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-controls with-border">
                        <form id="transaction_log" class="form form-bordered" action="{{URL::to('user/transaction_log')}}" method="post">                                                    
							<div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('affiliate/general.from_date_ph')}}" value="" /> <i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('affiliate/general.to_date_ph')}}" value="" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>							
                            <div class="col-sm-3">
                                <div class="form-group mt25">
                                    <button type="button" id="search_btn" class="btn bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="reset_btn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-body">
						<table id="subscriptions" class="table table-bordered table-striped">													
							<thead>
								<th>Package</th>
								<th>Transcation ID</th>
								<th>Amount</th>								
								<th>QV</th>								
								<th>Date of Purchase</th>
								<th>Updated On</th>
								<th>Payment Method</th>
							</thead>
							<tbody>								
							</tbody>
						</table>
                    </div>
                </div>
            </div>            
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/package/subscriptions.js')}}"></script>
@stop