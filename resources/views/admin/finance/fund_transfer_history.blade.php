@extends('admin.common.layout')
@section('pagetitle')
Fund Transfer History
@stop
@section('top_navigation')
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <h4 class="panel-title">Member Fund Transfer History</h4>
            </div>
            <div class="panel_controls">
			  <form id="fund_transfer" class="form form-bordered" action="{{route('admin.finance.fund-transfer-history')}}" method="get">
                <div class="row">
                        <div class="input-group col-sm-3">
                          <input type="search" id="terms" name="terms" class="form-control" placeholder="{{trans('admin/general.search_term')}}"/>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input class="form-control datepicker" type="text" id="from_date" name="from_date" placeholder="From">
								<span class="input-group-addon">-</span>
								<input class="form-control datepicker" type="text" id="to_date" name="to_date" placeholder="To">
							</div>
                        </div>
						<div class="col-sm-3">
                            <select name="wallet_id" class="form-control" id="wallet_id" >
                                <option value="">Select Wallet</option>
                                <?php
                                foreach ($eWallet_list as $eWallet)
                                {
                                    ?>
                                    <option value="<?php echo $eWallet->wallet_id;?>"><?php echo $eWallet->wallet;?> </option>
                                    <?php
                                }
                                ?>
                            </select>
                         </div>
						<div class="col-sm-3">
                            <select name="sysrole" id="sysrole"  class="form-control">
							<option value="">Select Role</option>
						    @foreach($sys_roles as $sys_role)
							@if(!empty($sys_role))
								<option value="{{$sys_role->id}}">{{$sys_role->account_type_name}}</option>
							 @endif
						    @endforeach
							</select>
                            </div>
						  </div>
					  <div class="row">
                          <div class="col-sm-3">
                            <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>
                            <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>
					   </div>
					   </div>
                  </form>
                </div>
           
            <div id="msg"></div>
                <table id="fund_transfer_history_list" class="table table-bordered table-striped" >
                <thead>
					<tr>
						<th>{{trans('admin/finance.report.date')}}</th>
                        <th>{{trans('admin/finance.report.trans_id')}}</th>
                        <th>{{trans('admin/finance.report.trans_from')}}</th>
                        <th>{{trans('admin/finance.report.trans_to')}}</th>
                        <th>{{trans('admin/finance.report.currency')}}</th>
                        <th>{{trans('admin/finance.report.wallet')}}</th>
                        <th>{{trans('admin/finance.report.amt')}}</th>
                        <th>{{trans('admin/finance.report.hdl_amt')}}</th>
                        <th>{{trans('admin/finance.report.paid_amt')}}</th>
                        <th>{{trans('admin/finance.report.status')}}</th>
					</tr>
				</thead>
                <tbody>
                </tbody>
            </table>
        </div>
      </div>
    </div>
   </div>

@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
    <script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
    <script src="{{asset('js/providers/admin/finance/fund_transfer_history.js')}}"></script>
@stop
