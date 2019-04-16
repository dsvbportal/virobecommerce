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
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form id="transaction_log" class="form form-bordered" action="{{route('aff.wallet.transactions')}}" method="post">                       
                        @if($payouts)
							@foreach($payouts as $payout)
								<div class="panel panel-info">
									<div class="panel-heading">{{$payout->payment_type}}</div>
									<div class="panel-body">
										<p>{{$payout->description}}</p>
									</div>
								</div>
							@endforeach
						@endif	
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/wallet/transactions.js')}}"></script>
@stop
