
@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/wallet/transactions.page_title'))
@section('content')																																					
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i> {{\trans('franchisee/wallet/transactions.page_title')}}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{\trans('franchisee/wallet/transactions.sub_title')}}</li>
        <li class="active">{{\trans('franchisee/wallet/transactions.page_title')}}</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form id="transaction_log" class="form form-bordered" action="{{route('fr.wallet.transactions')}}" method="post">                       
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="wallet_id">Search</label>
                                <select name="wallet_id" id="wallet_id" class="form-control">
                                    <option value="">{{trans('franchisee/wallet/transactions.select')}}</option>
                                    @if(!empty($wallet_list))
									@foreach ($wallet_list as $row)
                                    <option value="{{$row->wallet_id}}">{{$row->wallet}}</option>
                                    @endforeach
									@endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="from">{{trans('franchisee/wallet/transactions.from_date_phn')}}</label>
                                <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('franchisee/wallet/transactions.from_date_phn')}}" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label  for="to">{{trans('franchisee/wallet/transactions.to_date_phn')}}</label>
                                <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('franchisee/wallet/transactions.to_date_phn')}}" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" style="margin-top:25px;">
                                <button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('franchisee/general.search_btn')}}</button>
                                <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('franchisee/general.reset_btn')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table id="transactionlist" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{trans('franchisee/general.date')}}</th>
								<th>Particulars</th>
								<th>Wallet</th>								
								<th>{{trans('franchisee/wallet/transactions.credit')}}</th>
								<th>{{trans('franchisee/wallet/transactions.debit')}}</th>
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
<script src="{{asset('js/providers/franchisee/wallet/transactions.js')}}"></script>
@stop
