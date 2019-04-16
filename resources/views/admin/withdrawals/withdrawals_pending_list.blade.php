
@extends('admin.common.layout')
@section('pagetitle')
Pending Withdrawal
@stop
@section('top_navigation')
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <h4 class="panel-title">{{$title}}</h4>
            </div>
            <div class="panel_controls">
              
                    <form id="withdrawal_pending" action="{{route('admin.withdrawals.pending')}}" method="get">
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
					 <div class="col-sm-3">
                             <select name="payout_type" id="payout_type" class="form-control">
                                    <option value="">Select Payout Type</option>
                                    @if(!empty($payout_types))
                                    @foreach($payout_types as $type)
                                    <option value="{{$type->payment_type_id}}">{{$type->payment_type}}</option>
                                    @endforeach
                                    @endif
                                </select>
                         </div>
					 <div class="col-sm-3">
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
            <table id="withdrawal_pending_list" class="table table-striped">
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
                    
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        @include('admin.meta-info')
    </div>
    </div>


	
</div>

@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/withdrawals/withdrawals_pending.js')}}"></script>
@stop
