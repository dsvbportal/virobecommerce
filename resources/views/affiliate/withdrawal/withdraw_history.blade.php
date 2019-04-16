@extends('affiliate.layout.dashboard')
@section('title',"Withdrawal")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1> Withdrawal History</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Withdrawal</li>
        <li class="active">Withdrawal History</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">
             <div class="box box-primary" id="report">
					<div class="box-header with-border">
	                   <form id="withdrawal_log" class="form form-bordered" action="" method="post">
							<select name="currency" class="form-control hidden" id="currency" >
								 <?php
                                foreach ($currency_list as $currency)
                                {
                                    ?>
                                    <option value="<?php echo $currency->currency_id;?>"><?php echo $currency->currency;?> </option>
                                    <?php
                                }
                                ?>
                             </select>
                       {!! csrf_field() !!}                           
                           <!-- <div class="col-sm-3">
							  <label for="from">&nbsp;</label>
                                <div class="input-group">
                                <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="to" placeholder="To">
                            </div>
                            </div>-->
							 <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="wallet_id"> Status </label>
                                    <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0" selected>Pending</option>
									<option value="2">Processing</option>
									<option value="1">Confirmed</option>
									<option value="3">Cancelled</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="wallet_id"> {{trans('affiliate/withdrawal/history.payment_types')}} </label>
                                    <select name="payout_type" id="payout_type" class="form-control">
                                    <option value="">Select Payout Type</option>
                                    @if(!empty($payout_types))
                                    @foreach($payout_types as $type)
                                    <option value="{{$type->payment_type_id}}">{{$type->payment_type}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                </div>
                            </div>
                           
                            <div class="col-sm-6">
                                <div class="form-group">
                           <button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
						    <button type="button" id="resetbtn" class="btn btn-sm  btn-primary"><i class="fa fa-repeat"></i> Reset</button>
							<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                            <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>
                           
                                </div>
                            </div>
                        </form> 
					</div>
                    
            <div class="box-body">
                 <table id="withdrawal_list" class="table table-striped">
                <thead>
                    <tr>
						<!--<th nowrap="nowrap">{{trans('admin/withdrawals.request_on')}}</th>                    
						<th nowrap="nowrap">{{trans('admin/withdrawals.payment_mode')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.currency')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.amount')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.charges')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.net_pay')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.expected_date_credit')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.updated_on')}}</th>
						<th nowrap="nowrap">{{trans('admin/withdrawals.payment_details')}}</th>                    
						<th nowrap="nowrap">Details</th> -->
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.withdraw_date')}}</th> 
						<th nowrap="nowrap">{{trans('general.transaction_id')}}</th>
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.amount')}}</th> 
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.payment_mode')}}</th>
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.status')}}</th>
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.action')}}</th>
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
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/withdrawal/withdrawal.js')}}"></script>
@stop