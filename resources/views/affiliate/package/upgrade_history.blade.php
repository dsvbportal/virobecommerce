@extends('affiliate.layout.dashboard')
@section('title',"Purchase History")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Purchase History</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
         <li>{{\trans('affiliate/package.breadcrumb_title')}}</li>
        <li class="active">Purchase History</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">
				<?php
				if(request()->session()->has('purchase_msg')){
				$errmsg = request()->session()->get('purchase_msg');
				echo '<div class="alert alert-'.$errmsg['msgtype'].'">'.$errmsg['msg'].'</div>';
				}
				?>
                <div class="box box-primary">					
                    <div class="box-header with-border">
                        <form id="upgrade_history" class="form form-bordered" action="{{route('aff.package.purchase-history')}}" method="post">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{trans('affiliate/wallet/transactions.search_term')}}</label>
						        	<input type="text" id="search_term" name="account_search_termterm" class="form-control is_valid_string col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('affiliate/general.search_term')}}" >
                                </div>
                            </div>                            
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="from"> {{trans('affiliate/wallet/transactions.from_date')}}</label>
                                    <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label  for="to"> {{trans('affiliate/wallet/transactions.to_date')}} </label>
                                    <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-top:25px;">
                                    <button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-body">
						<table id="purchase_upgrade_histroy" class="table table-bordered table-striped table-responsive ">
							<thead>
								<tr>                                           
									<th>{{trans('affiliate/general.sl_no')}}</th>
									<th>{{trans('affiliate/general.package')}}</th>									
									<th>{{trans('affiliate/general.transaction_id')}}</th>									
									<th>{{trans('affiliate/general.amount')}}</th>
									<th>{{trans('affiliate/general.dop')}}</th>
									<th>{{trans('affiliate/general.updated_on')}}</th>								
									<th>{{trans('affiliate/general.payment_type')}}</th>
									<th>{{trans('affiliate/general.qv')}}</th>
									<!-- <th>{{trans('affiliate/general.action')}}</th>-->
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
<script src="{{asset('js/providers/affiliate/package/upgrade_histroy.js')}}"></script>
@stop