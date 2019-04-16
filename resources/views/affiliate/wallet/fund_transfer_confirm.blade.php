@if(!isset($error))
@if($showmsg)
	<div class="alert alert-success">{!!$showmsg!!}</div>
@endif
<form method="post" action="{{route('aff.wallet.fund_transfer_save')}}" class="form-horizontal form-bordered" id="fundtransfer_confirm_form" autocomplete="off" onsubmit="return false;">    
    <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"> {{\trans('affiliate/wallet/fundtransfer.from_wallet')}}</label>
        <div class="col-lg-8">
            <p id="d_ewallet_id" class="form-control-static">{{$ewallet_name}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label">{{\trans('affiliate/wallet/fundtransfer.currency')}}</label>
        <div class="col-lg-8">
            <p id="d_currency_id" class="form-control-static">{{$currency_code}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="user_avail_bal">{{\trans('affiliate/wallet/fundtransfer.available_bal')}}:</label>
        <div class="col-lg-8">
            <p id="d_user_balance" class="form-control-static">{{$userObj->amount_with_decimal($availbalance).' '.$currency_code}}</p>
        </div>
	   </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('affiliate/wallet/fundtransfer.to_account')}}</label>
        <div class="col-lg-8">
            <p id="d_to_account" class="form-control-static">{{$to_account}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label" for="amount">{{\trans('affiliate/wallet/fundtransfer.amount')}}</label>
        <div class="col-lg-8">
            <p id="d_totamount" class="form-control-static">{{$userObj->amount_with_decimal($totamount).' '.$currency_code}}</p>
        </div>
    </div>
   
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="tac_code">Enter OTP *</label>
        <div class="col-lg-8">
            <div class="input-group">
                <input type="password" name="tac_code" id="tac_code" class="form-control" />
            </div>
        </div>
    </div>	
	  <div class="form-group" >
        <div class="col-lg-4 col-sm-1 text-right" >
            <input type="button" name="fund_transfer"  id="back" class="btn  btn-default" value="{{\trans('affiliate/wallet/fundtransfer.bck_btn')}}" />
        </div>
        <div class="col-lg-4 col-sm-1" >
            <input type="button" name="fund_transfer"  id="confirm_fund_transfer" class="btn btn-primary" value="{{\trans('affiliate/wallet/fundtransfer.confirm_transfer')}}" />
        </div>
    </div>	
	 </form> 

@else
<p id="fundtransfer_confirm_form">{{$error}}</p>
@endif