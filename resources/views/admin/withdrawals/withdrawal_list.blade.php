@extends('admin.common.layout')
@section('pagetitle')
Pending Withdrawal
@stop
@section('top_navigation')
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="report">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Withdrawals</h4>
            </div>
            <div class="panel_controls">
              
                    <form id="withdrawal_pending" action="{{$formUrl}}" method="get">
                        <div class="row">
						    <div class="col-sm-3">
                                <input type="text"  placeholder="User Name" name="username" id="username" class="form-control" value=""/>
                             </div>
					<div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="start_date" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="end_date" placeholder="To">
                            </div>
                        </div>
						<div class="col-sm-2">
                                <div class="form-group">
                                   
                                    <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0">Pending</option>
									<option value="2">Processing</option>
									<option value="1">Confirmed</option>
									<option value="3">Cancelled</option>
                                </select>
                                </div>
                            </div>
					 <div class="col-sm-2">
                             <select name="payout_type" id="payout_type" class="form-control">
                                    <option value="">Select Payout Type</option>
                                    @if(!empty($payout_types))
                                    @foreach($payout_types as $type)
                                    <option value="{{$type->payment_type_id}}">{{$type->payment_type}}</option>
                                    @endforeach
                                    @endif
                                </select>
                         </div>
					 <div class="col-sm-2">
                              <select name="currency" class="form-control" id="currency" >
                                <option value="">Select Currency</option>
								 <?php
                                foreach ($currency_list as $currency)
                                {
                                    ?>
                                    <option value="<?php echo $currency->currency_id;?>"><?php echo $currency->currency;?> </option>
                                    <?php
                                }
                                ?>
                             </select>
                         </div>
                         </div>
					 
			  <div class="row">
                        <div class="col-sm-6">
                            <button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
							<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                            <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>
                            <button type="button" id="resetbtn" class="btn btn-sm  btn-primary"><i class="fa fa-repeat"></i> Reset</button>
                        </div>    
                        </div>    
                    </form>           
            </div>
            <div id="msg"></div>
            <table id="withdrawal_list" class="table table-striped">
                <thead>
                    <tr>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.request_on')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.uname')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.country')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.payment_mode')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.currency')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.amount')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.charges')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.net_pay')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.expected_date_credit')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.updated_on')}}</th>
                    <th  nowrap="nowrap">{{trans('admin/withdrawals.payment_details')}}</th>
                    <th>Action</th>                    
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
	<div class="col-md-12" id="details">
			
		</div>
    </div>	
</div>
   <div class="modal fade" id="change_status_model" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" style="width: 450px;">
	    <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
		<div class="modal-body">
			<form id="change_status_form" action="{{route('admin.withdrawals.confirm')}}">
				<input type="hidden" name="update_status" id="update_status">
				<input type="hidden" name="withdrawal_id" id="withdrawal_id">
				<label>Transaction Details</label>
				<div class="form-group">
					<textarea class="form-control" id="msg" name="msg"></textarea>
				</div>
				<div class="form-actions text-right">
					 <button  id="confirm_withdraw_details" class="btn btn-primary">Confirm
					 </button>
				</div>
			</form>
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/withdrawals/withdrawals.js')}}"></script>
@stop
