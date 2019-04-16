@extends('franchisee.layout.dashboard')
@section('title','Earned Commission Details')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i> Earned Commission Details</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/general.reports')}}</li>
        <li class="active">Earned Commission Details</li>
		<li class="active" id="type">{{$type}}</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary" id="report">
                <div class="box-header with-border">
                    <form id="earned_store_details" class="form form-bordered"  method="post">  
					
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="from_date">{{trans('franchisee/general.frm_date')}}</label>
                                <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('franchisee/general.frm_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="to_date">{{trans('franchisee/general.to_date')}}</label>
                                <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('franchisee/general.to_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
					    <div class="col-sm-6">	
                        <div class="form-group" style="margin-top:25px;">
							<button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('franchisee/general.search_btn')}}</button>&nbsp;
							<button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('franchisee/general.reset_btn')}}</button>&nbsp;
                        </div>
                       </div>
                    </form>
                </div>
                <div class="box-body">
                    <table id="earned_commission_store_details" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
								<th>Merchant Enrollment Fee</th>
								<th>State</th>							
								<th>District</th>
								<th>Fee</th>
                              </tr>
                            </thead>
                            <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
			<div id="details">
					
			</div>
        </div>		
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/reports/merchant_enroll_fee_details.js')}}"></script>
@stop
