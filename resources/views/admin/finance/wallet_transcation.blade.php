@extends('admin.common.layout')
@section('pagetitle')
Affiliate
@stop
@section('top_navigation')

@section('layoutContent')

	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
		
            <div class="panel-heading">
                <h4 class="panel-title">Wallet Transcation</h4>
            </div>
            <div class="panel_controls">
                
                   <form id="form" action="{{route('admin.finance.wallet-transcation')}}" class="form form-bordered" method="get"> 
                       <div class="row">
					   <div class="col-sm-3">
						    <div class="input-group">
                               <input type="text" id="search_text" name="search_text" class="form-control">
                              <div class="input-group-btn">
                                <button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
                              <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
							    <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="UserName" type="checkbox" checked>{{trans('admin/affiliate/admin.username')}}</label></li> 
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FullName" type="checkbox">{{trans('admin/affiliate/admin.fullname')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Email" type="checkbox">{{trans('admin/affiliate/admin.email')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Mobile" type="checkbox">{{trans('admin/affiliate/admin.mobile')}}</label></li>
                             </ul>
                            </div>
                        </div>
                       </div>
                        <div class="col-sm-3">
                            <div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input class="form-control datepicker" type="text" id="from_date" name="from_date" placeholder="From">
								<span class="input-group-addon">-</span>
								<input class="form-control datepicker" type="text" id="to_date" name="to_date" placeholder="To">
							</div>
                        </div>
						<div class="col-sm-2">
							<div class="form-group">
                            <select id="type" class="form-control" name="type">
							<option value="">Select Type</option>
							<option value="all">All</option>
							<option value="affiliate">Affiliate</option>
							<option value="franchise">Channel Partner</option>
							</select>
                           </div>
						   </div>
					<div class="col-sm-2">
					<div class="form-group">
						  <select name="currency_id" class="form-control" id="currency_id" >
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
					  <div class="col-sm-2">
					  <div class="form-group">
                            <select name="wallet_id" class="form-control" id="wallet_id" >
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
						</div>
					<div class="row form-action">
					<div class="col-sm-12">
					
                       
						
                            <button id="searchbtn" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
							<button id="resetbtn" type="button" class="btn btn-primary btn-sm"><i class="fa fa-repeat"></i> Reset</button>
							<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                            <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>
						</div>
					
				</div>
                  </form>
            </div>
          <div class="panel-body no-padding">
				<div id="msg"></div>
            <table id="wallet_transcation_list" class="table table-striped">
                <thead>
					<tr>
						 <th>{{trans('admin/finance.wallet_transcation.created_on')}}</th>
						 <th>{{trans('admin/finance.wallet_transcation.full_name')}}</th>
						 <th>{{trans('admin/finance.wallet_transcation.description')}}</th>
						 <th>{{trans('admin/finance.transcation.wallet')}}</th>
						 <th>{{trans('admin/finance.wallet_transcation.cr_amt')}}</th>
						 <th>{{trans('admin/finance.wallet_transcation.dr_amt')}}</th>
						 <th>{{trans('admin/finance.wallet_transcation.balance')}}</th>
					</tr>
				</thead>
                <tbody>
                </tbody>
            </table>
        </div>
        </div>
        @include('admin.meta-info')
</div>

@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
     <script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
	 <script src="{{asset('js/providers/admin/finance/wallet_transcation.js')}}"></script>
@stop
