@extends('franchisee.layout.dashboard')
@section('title','TDS Deducted Report')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i> TDS Deducted Report</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/general.reports')}}</li>
        <li class="active">TDS Deducted Report</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form id="tds_deductedfrm" class="form form-bordered" action="{{route('fr.reports.tds-deducted-report')}}" method="post">  					
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="from_date">{{trans('franchisee/general.frm_date')}}</label>
                                <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('franchisee/general.frm_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="to_date">{{trans('franchisee/general.frm_date')}}</label>
                                <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('franchisee/general.to_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                   <div class="col-sm-5">
                      <div class="form-group">
						<button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('franchisee/general.search_btn')}}</button>&nbsp;
						<button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('franchisee/general.reset_btn')}}</button>&nbsp;
						<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>  {{trans('franchisee/general.export_btn')}}</button>&nbsp;
						<button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>  {{trans('franchisee/general.print_btn')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table id="tds_deducted_tbl" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{trans('franchisee/general.date')}}</th>
                                <th>{{trans('franchisee/wallet/transactions.description')}}</th>
                                <th>{{trans('franchisee/wallet/transactions.paymode')}}</th>                              
								<th>{{trans('franchisee/general.amount')}}</th>
								<th>{{trans('franchisee/wallet/transactions.tds')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>		
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/reports/tds_deducted_details.js')}}"></script>
@stop
