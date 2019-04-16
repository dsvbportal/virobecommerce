@extends('franchisee.layout.dashboard')
@section('title',"Withdrawal")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
	   <div class="pull-right">
			    <a  href="{{route('fr.withdrawal.request')}}" ><button class="btn btn-success">{{trans('franchisee/withdrawal/withdrawal.create_withdrawal')}}</button></a>	
				</div>
      <h1> {{trans('franchisee/withdrawal/withdrawal.page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/withdrawal/withdrawal.sub_title')}}</li>
        <li class="active">{{trans('franchisee/withdrawal/withdrawal.page_title')}}</li>
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
                       {!! csrf_field() !!}                           
							 <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('franchisee/general.frm_date')}}</label>
                                    <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('franchisee/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('franchisee/general.to_date')}}</label>
                                    <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('franchisee/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
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
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="wallet_id"> {{trans('franchisee/withdrawal/withdrawal.payment_types')}} </label>
                                    <select name="payout_type" id="payout_type" class="form-control">
                                    <option value="">Select Payment Type</option>
                                    @if(!empty($payout_types))
                                    @foreach($payout_types as $type)
                                    <option value="{{$type->payment_type_id}}">{{$type->payment_type}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                </div>
                            </div>
                           
                            <div class="col-sm-4">
                             <div class="form-group " style="margin-top:25px;">
                           <button id="search" type="button" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Search</button>
						    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> Reset</button>
							<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('franchisee/general.export_btn')}}</button>
                            <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('franchisee/general.print_btn')}}</button>
                           
                                </div>
                            </div>
                        </form> 
					</div>
                    
            <div class="box-body">
                 <table id="withdrawal_list" class="table table-striped">
                <thead>
                    <tr>
						<th nowrap="nowrap">{{trans('franchisee/withdrawal/withdrawal.withdraw_date')}}</th> 
						<th nowrap="nowrap">{{trans('franchisee/withdrawal/withdrawal.transaction_id')}}</th> 
						<th nowrap="nowrap">{{trans('franchisee/withdrawal/withdrawal.amount')}}</th> 
						<th nowrap="nowrap">{{trans('franchisee/withdrawal/withdrawal.payment_mode')}}</th>
						<th nowrap="nowrap">{{trans('franchisee/withdrawal/withdrawal.status')}}</th>
						<th nowrap="nowrap">{{trans('franchisee/withdrawal/withdrawal.action')}}</th>
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
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/withdrawal/withdrawal.js')}}"></script>
@stop