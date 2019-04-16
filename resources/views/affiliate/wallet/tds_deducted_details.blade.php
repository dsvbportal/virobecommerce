@extends('affiliate.layout.dashboard')
@section('title','TDS Deducted Report')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i> TDS Deducted Report</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{trans('affiliate/general.wallet')}}</li>
        <li class="active">TDS Deducted Report</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary" id="report">
                <div class="box-header with-border">
                    <form id="tds_deductedfrm" class="form form-bordered" action="{{route('aff.reports.tds-deducted-report')}}" method="post">  					
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="from_date">From Date</label>
                                <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="to_date">To Date</label>
                                <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>&nbsp;
                                <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>&nbsp;
								<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('affiliate/general.export_btn')}}</button>&nbsp;
                                <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table id="tds_deducted_tbl" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <!--
                                <th>{{trans('affiliate/wallet/transactions.description')}}</th>
                                <th>{{trans('affiliate/wallet/transactions.paymode')}}</th>                              
								<th>Amount</th>
								<th>TDS</th>-->
								<th>{{trans('affiliate/general.date')}}</th>
								<th>{{trans('affiliate/wallet/transactions.earn_amount')}}</th>
								<th>{{trans('affiliate/wallet/transactions.tax_deducted')}}</th>
								<th>{{trans('affiliate/wallet/transactions.income_type')}}</th>
								<th>{{trans('affiliate/general.action')}}</th>
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
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/wallet/tds_deducted_details.js')}}"></script>
@stop
