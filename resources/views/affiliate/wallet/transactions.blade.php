
@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/wallet/transactions.page_title'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i> {{\trans('affiliate/wallet/transactions.page_title')}}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{trans('affiliate/general.wallet')}}</li>
        <li class="active">{{\trans('affiliate/wallet/transactions.breadcrumb_title')}}</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form id="transaction_log" class="form form-bordered" action="{{route('aff.wallet.transactions')}}" method="post">                       
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="wallet_id">Search</label>
                                <select name="wallet_id" id="wallet_id" class="form-control" data-url="{{route('aff.wallet.wallet_balance')}}">
                                    @if(!empty($wallet_list))
									@foreach ($wallet_list as $row)
                                    <option value="{{$row->wallet_id}}" {{($row->wallet_id == $default_wallet_id ? 'selected = "selected" ' : '')}}>{{$row->wallet}}</option>
                                    @endforeach
									@endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="from">From Date</label>
                                <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label  for="to">To Date</label>
                                <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" style="margin-top:25px;">
                                <button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                            </div>
                        </div>
			<!--		<div class="col-sm-3">
					<div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="fa fa-sitemap"></i></span>
						<div class="info-box-content">
							<div class="info-box-text"></div>
							<div class="info-box-number text-success" id="current_balance">0</div>
						</div>
					</div>
				</div> -->
                    </form>
                </div>
                <div class="box-body">
                    <table id="transactionlist" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:15%">{{trans('affiliate/general.date')}}</th>
								<th style="width:15%">{{trans('affiliate/wallet/transactions.paymode')}}</th>
								<th>{{trans('affiliate/wallet/transactions.details')}}</th>
								<th style="width:10%">{{trans('affiliate/wallet/transactions.credit')}}</th>
								<th style="width:10%">{{trans('affiliate/wallet/transactions.debit')}}</th>								
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		<?php /*
		<div class="col-md-3">			
			<div class="form-group">
				<div class="widget">
					<div class="widget-advanced">
						<div class="widget-header bg-maroon">
							<div class="widget-options">
								
							</div>
							<h4>Your Account Balance</h4>
						</div>
						<div class="widget-extra-full">              
							@if(!empty($balInfo))
							<table class="table table-striped"  style="margin:0px">
							<tbody>
							@foreach($balInfo as $balance)
							<tr>
								<th style="width:50%;">{{$balance->wallet_name}}</th>
								<td class="text-right text-green">{{$balance->current_balance}}</td>
							</tr>
							@endforeach
							</tbody>
							</table>
							@endif
						</div>
					</div>
				</div>
			</div>
			<a href="{{route('aff.addfund')}}" class="btn btn-block btn-lg btn-social bg-green">
                <i class="fa fa-plus-circle"></i> Add Fund
              </a>
			  <a href="{{route('aff.wallet.fundtransfer')}}" class="btn btn-block btn-lg btn-social bg-aqua">
                <i class="fa fa-plus-square"></i> Fund Transfer
              </a>			
		</div>
		*/ ?>
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/wallet/transactions.js')}}"></script>
@stop
