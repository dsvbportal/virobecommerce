@extends('franchisee.layout.dashboard')
@section('title',"Dashboard")
@section('content')
    <section class="content">
   <div class="row">
      <div class="col-lg-3 col-xl-3 col-md-6 col-sm-6 col-12">
         <div class="card gradient-pomegranate">
            <div class="card-body text-center">
               <h5>Today's Visitors</h5>
               <div class="text-center">                  
                  <h3 class="mb-2 text-dark">{{$today_visitors or '0'}}</h3>
				  <div class="mb-3 mt-1">
                     <span class="sparkline_line">
                        <canvas style="display: inline-block; width: 80px; height: 50px; vertical-align: top;" width="80" height="50"></canvas>
                     </span>
                  </div>
               </div>
            </div>
			<div class="card-footer text-center"><span><i class="fa fa-arrow-up text-success"></i> 0% increase</span><small> last week</small></div>
         </div>
      </div>
      <div class="col-lg-3 col-xl-3 col-md-6 col-sm-6 col-12">
         <div class="card gradient-green-teal">
            <div class="card-body text-center">
               <h5>New Merchants</h5>
               <div class="text-center">                  
                  <h3 class="mb-2 text-dark">{{$new_users or '0'}}</h3>
				  <div class="mb-3 mt-1">
                     <span class="sparkline_pie">
                        <canvas style="display: inline-block; width: 50px; height: 50px; vertical-align: top;" width="50" height="50"></canvas>
                     </span>
                  </div>                  
               </div>
            </div>
			<div class="card-footer text-center"><span class="hidden"><i class="fa fa-arrow-down"></i> 0% increase</span><small> last week</small></div>
         </div>
      </div>
      <div class="col-lg-3 col-xl-3 col-md-6 col-sm-6 col-12">
         <div class="card gradient-ibiza-sunset">
            <div class="card-body text-center">
               <h5>Today's Earnings</h5>
               <div class="text-center">                  
                  <h3 class="mb-2 text-dark">{{$today_earnings->commission_amount}}</h3>
				  <div class="mb-3 mt-1">
                     <span class="sparkline_bar">
                        <canvas style="display: inline-block; width: 84px; height: 50px; vertical-align: top;" width="84" height="50"></canvas>
                     </span>
                  </div>
                  
               </div>
            </div>
			<div class="card-footer text-center"><span><i class="fa fa-arrow-up text-success"></i> 0% increase</span><small> last week</small></div>
         </div>
      </div>
      <div class="col-lg-3 col-xl-3 col-md-6 col-sm-6 col-12">
         <div class="card gradient-blackberry">
            <div class="card-body text-center">
               <h5>Total Earnings</h5>
               <div class="text-center">                  
                  <h3 class="mb-2 text-dark">{{$earnings->commission_amount}}</h3>
				  <div class="mb-3 mt-1">
                     <span class="sparkline_area">
                        <canvas style="display: inline-block; width: 85px; height: 50px; vertical-align: top;" width="85" height="50"></canvas>
                     </span>
                  </div>                  
               </div>
            </div>
			<div class="card-footer text-center"><span class=""><i class="fa fa-arrow-down text-danger"></i> 0% decrease</span><small> last week</small></div>
         </div>		 
      </div>
   </div>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-6 col-xl-4 col-lg-3">
         <div class="card blue-b">
            <div class="card-block">
               <h6 class="mb-4">Daily Sales</h6>
               <div class="col-12 d-flex align-items-center">
                  <div class="pull-right m-b-0">{{ $daily_sales_perc or '0' }}%</div>
				  <div class="col-9">					
                     <h3 class="f-w-300 d-flex align-items-center m-b-0"><i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>{{ $daily_sales or '0' }}</h3>
                  </div>                  
               </div>
               <div class="progress m-t-30" style="height: 7px;">
                  <div class="progress-bar progress-bar-info progress-c-theme" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
               </div>
            </div>			
         </div>
      </div>
      <div class="col-md-6 col-xl-4 col-lg-3">
         <div class="card green-b">
            <div class="card-block">
               <h6 class="mb-4">Monthly Sales</h6>
               <div class="col-12 d-flex align-items-center">
                  <div class="pull-right m-b-0">{{ $daily_sales_perc or '0' }}%</div>
				  <div class="col-9">					
                     <h3 class="f-w-300 d-flex align-items-center m-b-0"><i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>{{ $daily_sales or '0' }}</h3>
                  </div>                  
               </div>
               <div class="progress m-t-30" style="height: 7px;">
                  <div class="progress-bar progress-bar-success progress-c-theme" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-12 col-xl-4 col-lg-3 ">
         <div class="card darkb-b">
            <div class="card-block">
               <h6 class="mb-4">Yearly Sales</h6>
               <div class="col-12 d-flex align-items-center">
                  <div class="pull-right m-b-0">{{ $daily_sales_perc or '0' }}%</div>
				  <div class="col-9">					
                     <h3 class="f-w-300 d-flex align-items-center m-b-0"><i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>{{ $daily_sales or '0' }}</h3>
                  </div>                  
               </div>
               <div class="progress m-t-30" style="height: 7px;">
                  <div class="progress-bar progress-c-theme" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-12 col-xl-4 col-lg-3 ">
         <div class="widget">
            <div class="widget-advanced">
               <div class="widget-header bg-maroon">
                  <h4>Your Account Balance</h4>
               </div>
               <div class="widget-extra-full">
					@if(!empty($balInfo))
					<table class="table table-striped"  style="margin:0px">
					<tbody>
					@foreach($balInfo as $balance)
					<tr>
						<th style="width:60%;">{{$balance->wallet_name}}</th>
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
   </div>
</section>
<section class="content">
   <div class="row">
      <div class="col-lg-12 col-xl-12 col-md-12 col-12 col-sm-12">
         <div class="card">
            <div class="card-header">
               <h4>Previous Transactions</h4>
            </div>
            <div class="card-body">
				<div class="table-responsive projectstatus">
					<form id="transaction_log" class="form form-bordered" action="{{route('fr.orders')}}" method="post">                       
					<table id="transactionlist" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>{{trans('franchisee/dashboard.brand')}}</th>
							<th>{{trans('franchisee/dashboard.date')}}</th>
							<th>{{trans('franchisee/dashboard.vendor')}}</th>
						<!--<th>{{trans('franchisee/dashboard.purchase_date')}}</th> -->
							<th>{{trans('franchisee/dashboard.cost')}}</th>
							<th>{{trans('franchisee/dashboard.sale_type')}}</th>
							<th>{{trans('franchisee/dashboard.status')}}</th>
							<!--
							<th>{{trans('franchisee/wallet/transactions.paymode')}}</th>
							<th>Details</th>
							<th>Credit</th>
							<th>Debit</th> -->
						</tr>
					</thead>					
					</table>
					</form>
				</div>
            </div>
         </div>
      </div>
   </div>
</section>
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/dashboard.js')}}"></script>
@stop
