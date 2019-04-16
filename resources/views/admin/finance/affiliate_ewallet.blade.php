@extends('admin.common.layout')
@section('pagetitle')
eWallet of Affiliates
@stop
@section('top_navigation')
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <h4 class="panel-title">eWallet Balance</h4>
            </div>
            <div class="panel_controls">
              
                    <form id="affiliate_ewallet" action="{{route('admin.finance.ewallet')}}" method="get">
                        <div class="row">
						    <div class="col-sm-3">
                                <input type="text"  placeholder="User Name" name="username" id="username" class="form-control" value=""/>
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
						<div class="col-sm-3">
                            <select name="ewallet_id" class="form-control" id="ewallet_id" >
                                <option value="">Select eWallet</option>
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
            <table id="ewallet_list" class="table table-striped">
                <thead>
                    <tr>
                    <th  nowrap="nowrap">{{trans('admin/finance.user_name')}}</th>
                    <th>{{trans('admin/finance.currency')}}</th>
                    <th>{{trans('admin/finance.total_credit')}}</th>
                    <th>{{trans('admin/finance.total_debit')}}</th>
                    <th>{{trans('admin/finance.available_balance')}}</th>
                    <th>{{trans('admin/finance.wallet')}}</th>
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

<script src="{{asset('js/providers/admin/finance/affiliate_ewallet.js')}}"></script>
@stop
