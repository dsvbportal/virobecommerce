@extends('admin.common.layout')
@section('title',trans('admin/finance.admin_trans_fund_report'))
@section('breadcrumb')
<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('admin/finance.management')}}</a></li>
@stop
@section('layoutContent')
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title col-sm-6">Admin Credit & Debit </h4>
	</div>
   <div class="panel-body">
		<div id="status_msg"></div>
			 <div class="panel_controls">
                <div class="row">
            <form id="form" class="form form-bordered" action="{{route('admin.finance.admin-credit-debit-history')}}" method="post">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="from">Search</label>
                        <input type="search" id="terms" name="terms" class="form-control" placeholder="search terms" value=""/>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="from">{{trans('admin/finance.trans_type')}}</label>
                        <select name="trans_type" id="transa_type" class="form-control">
                            <option value="">Select</option>
                            <option value="1">Credit</option>
                            <option value="2">Debit</option>
                        </select>
                    </div>
                </div>
				   <div class="col-sm-3">
						<div class="form-group">
							<label for="from">Date</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input class="form-control datepicker" type="text" id="from_date" name="from_date" placeholder="From">
								<span class="input-group-addon">-</span>
								<input class="form-control datepicker" type="text" id="to_date" name="to_date" placeholder="To">
							</div>
						</div>
					</div>
				
					<div class="col-sm-3">
						<div class="form-group">
						<label for="from">&nbsp;</label>
							<div class="btn-group">
								<button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>&nbsp;
								<button type="reset" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>
							</div>
						</div>
					</div>
			</div>
	        </form>
		  </div>
        <div class="box-body">
            <div id="status_msg"></div>
            <table id="hist_table" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th>{{trans('admin/finance.report.date')}}</th>
                        <th>{{trans('admin/finance.report.trans_id')}}</th>
                        <th>User Name</th>
                        <th>{{trans('admin/finance.report.wallet')}}</th>
                        <th>{{trans('admin/finance.report.amt')}}</th>
                        <th>{{trans('admin/finance.report.hdl_amt')}}</th>
                        <th>{{trans('admin/finance.report.paid_amt')}}</th>
                        <th>{{trans('admin/finance.report.trans_by')}}</th>
                        <th>{{trans('admin/finance.report.status')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('admin.common.datatable_js')
@stop
@section('scripts')
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
<script src="{{asset('js/providers/admin/finance/admin_credit_debit_trans.js')}}"></script>
@stop

