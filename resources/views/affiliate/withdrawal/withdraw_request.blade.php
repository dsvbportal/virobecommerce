@extends('affiliate.layout.dashboard')
@section('title',"Withdraw Money")
@section('content')
    <!-- Content Header (Page header) -->
	
	<script>
		var current_balance = <?php  echo $current_balance;  ?>;
	</script>
    <section class="content-header">
      <h1>Withdraw Money</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li>{{trans('affiliate/general.Withdrawal')}}</li>
        <li class="active">Withdraw Money</li>
      </ol>
	
    </section>
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-md-9">
		<div class="box box-primary" id="payout-modes">
                <div class="box-body with-border">					
				  <table class="table table-striped table-bordered " id="payout-types">
					<thead>
						<tr>
						<th>Method</th>
						<th>Description</th>
						</tr>
					</thead>
					<tbody>
					@if($payouts)
					<?php $i=0; ?>
					@foreach($payouts as $payout)
						 <tr>
							<td class="text-center" style="width: 110px;"><img class="img img-responsive" width="100" src="{{asset($payout->image_name)}}" ></td>
							<td class="text-left">
							   <h4><a href="javascript:void(0);" class="payouts" rel="{{$payout->payment_type_id}}" data-payout-typename="{{$payout->payment_key}}">{{$payout->payment_type}}</a></h4>
							   <span class="text-muted">{{$payout->description}}</span>
							</td>							
						 </tr>
					 @endforeach
					@endif	
					<tbody>
				  </table>
			</div>
        </div>
        <div class="box box-primary tabs" id="payout-amt-info" style="display:none">
            <div class="box-header with-border flds">
				<div class="panel-header">					
					<button class="btn btn-danger btn-xs pull-right back-btn"><i class="fa fa-times"></i></button>
					<h4 class="panel-title">Bank Transfer</div>
				</div>
                <div class="panel-body">					
						<div id="msg"></div>
                    <form class="form form-horizontal" id="withdrawal-form" action="{{route('aff.withdrawal.payout_withdraw_settings')}}">
                        <input type="hidden" name="payment_type_id" id="payment_type_id" value="">
                        <div class="form-group">
							<label class="control-label col-sm-2">Balance:</label>
							<div class="col-sm-4">
								<h4 class="text-success text-left">{{!empty($balance)? $balance->currency_symbol.' '.number_format($balance->current_balance,'2'):'Insufficent balance'}}</h4>
							</div>								
                        </div>
						@if(!empty($balance)) 
                        <div class="form-group">
                            <label class="control-label col-sm-2">Amount<span class="mandatory">*</span> :</label>
                            <div class="col-sm-4">
								<div class="input-group">
									<span class="input-group-addon">{{$balance->currency_symbol}}</span>
									<input onkeypress="return isNumberKeydot(event)" class="form-control" type="text" name="amount" id="amt" value="{{number_format($balance->current_balance,'2')}}"/>
								
								</div>
								<span class="text-danger" id="bal_err"></span>
                            </div>
                        </div>						
                        <div class="form-group hidden">
                            <label class="control-label col-sm-2">Charges:</label>
                            <div class="col-sm-4">
                                <p class="form-control-static text-danger" id="charge"></p>
                            </div>
                        </div>
						<div class="form-group payout-acinfo" id="payout-local-money-transfer">
                            <label class="control-label col-sm-2">Beneficiary  Account:</label>
                            <div class="col-sm-4">
								<div class="">
									<table class="table table table-bordered">
										<tr>
											<th>Beneficiary Name</th>
											<td class="beneficiary_name"></td>
										</tr>
										<tr>
											<th>Account No</th>
											<td class="account_no"></td>
										</tr>
										<tr>
											<th>IFSC Code</th>
											<td class="ifsc_code"></td>
										</tr>
										<tr>
											<th>Bank Name</th>
											<td class="bank_name"></td>
										</tr>
									</table>								
								</div>
                            </div>
                        </div>
						@if(!empty($balance))
						<div class="form-group">                            
                            <div class="col-sm-2 text-right">
								<button class="btn btn-danger back-btn" type="button"><i class="fa fa-arrow-left"></i> Back</button>
							</div>
							<div class="col-sm-4">                                
								<button class="btn btn-success" id="submit" type="button" disabled><i class="fa fa-save"></i> Make Withdraw</button>								
                            </div>
                        </div>
						@endif
						@endif
                    </form>
					@if(!empty($balance))
					<form class="form form-horizontal" id="trans_pin_form" action="{{route('aff.withdrawal.save_withdrawal')}}" style="display:none">
						<div class="form-group">
							<label class="control-label col-sm-2">Withdraw Amount: </label>
							<div class="col-sm-4">
								<label class="control-label text-success" id="lebel-amt">{{$balance->currency_symbol.' '.number_format($balance->current_balance,'2')}}</label>
							</div>
						</div>
						<div class="form-group hidden">
							<label class="control-label col-sm-2">Charges : </label>								
							<div class="col-sm-4">
								<label class="control-label" id="lebel-charge"></label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">Security PIN:</label>
							<div class="col-sm-4">
								<input type="text" onkeypress="return isNumberKeydot(event)" name="security_pin" required maxlength="4" id="trans_pin" class="form-control">
							</div>
						</div>
					
						<div class="form-group">
							<label class="control-label col-sm-2"></label>
							<div class="col-sm-4">									
								<button class="btn btn-sm btn-success" id="confirm" >Confirm Withdraw</button>&nbsp;	
								<button class="btn btn-sm btn-danger confirm-backBtn" type="button"><i class="fa fa-arrow-left"></i> Back</button>
							</div>
						</div>
					</form>
					@endif
                </div>
            </div>				
        </div>
    </div>
</div>
</section>
<!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/withdrawal/withdrawal_request.js')}}" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('js/providers/affiliate/wallet/other_functionalities.js')}}"></script>
@stop