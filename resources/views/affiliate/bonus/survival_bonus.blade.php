@extends('affiliate.layout.dashboard')
@section('title',"Survival Bonus")
@section('content')
<!-- Content Header (Page header) -->    
@include('affiliate.common.affiliate_commission_header')
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <!-- ./col -->
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-controls with-border">
                    <form id="survival_bonus_details" class="form form-bordered" action="{{route('aff.reports.survival_bonus')}}" method="post">
                        {!! csrf_field() !!}
                        <div class="col-sm-3">
                            <div class="form-group has-feedback">
                                <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group has-feedback">
                                <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-6 mt25">
                            <div class="form-group">
                                <button type="button" id="searchbtn" class="btn btn-success"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>&nbsp;
                                <button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>&nbsp;
                                <button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i> {{trans('affiliate/general.export_btn')}}</button>&nbsp;
                                <button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table id="survival_bonus_commission" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{trans('affiliate/bonus/Survival_bonus.qualified_month')}} </th>
                                <th>{{trans('affiliate/bonus/Survival_bonus.rank')}} </th>
                                <th>{{trans('affiliate/bonus/Survival_bonus.commission')}} </th>
                                <th>{{trans('affiliate/bonus/Survival_bonus.tax')}} </th>
                                <th>{{trans('affiliate/bonus/Survival_bonus.ngo_wallet')}} </th>
                                <th>{{trans('affiliate/bonus/Survival_bonus.net_pay')}} </th>
                                <th>{{trans('affiliate/bonus/Survival_bonus.status')}} </th>
                            </tr>
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
<script src="{{asset('js/providers/affiliate/bonus/survival_bonus.js')}}"></script>
@stop
