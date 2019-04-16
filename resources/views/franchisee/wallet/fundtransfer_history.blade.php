@extends('franchisee.layout.dashboard')
@section('title',"Fund Transfer History")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('franchisee/general.fund_transfer_history')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{trans('franchisee/general.dashboard')}}</a></li>
			<li>{{trans('franchisee/general.finance')}}</li>
        <li class="active">{{trans('franchisee/general.fund_transfer_history')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">
             <div class="box box-primary">
					<div class="box-header with-border">
	                   <form id="form_fundtransfer" class="form form-bordered" action="{{route('fr.wallet.fundtransfer.history')}}" method="post">
                       		{!! csrf_field() !!}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{\trans('franchisee/general.username')}}</label>
                                    <input type="text" id="search_term" name="search_term" class="form-control col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('franchisee/general.search_term')}}">
                                </div>                                                                                                                                                                
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('franchisee/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('franchisee/general.frm_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('franchisee/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('franchisee/general.to_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                          
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('franchisee/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('franchisee/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i> {{trans('franchisee/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i> {{trans('franchisee/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="fundtransferlist" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                                <th>{{trans('franchisee/wallet/fund_transfer_history.transfered_on')}}</th>  
                                <th>{{trans('franchisee/wallet/fund_transfer_history.to_account')}}</th>
                                <th>{{trans('franchisee/wallet/fund_transfer_history.type_of_user')}}</th>
								<th>{{trans('franchisee/general.amount')}}</th>
                         <!--   <th>{{trans('franchisee/wallet/fund_transfer_history.wallet_name')}}</th> 
						       <th>{{trans('franchisee/wallet/fund_transfer_history.transaction_id')}}</th>
                                <th>{{trans('franchisee/general.remarks')}}</th>
                                <th width="10%">{{trans('franchisee/wallet/fund_transfer_history.paidamt')}}</th>   
                                <th>{{trans('franchisee/general.status')}}</th>   -->                          
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
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/wallet/fundtransfer_history.js')}}"></script>
@stop