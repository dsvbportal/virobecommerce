@if(!isset($error))
<form method="post" action="{{route('fr.wallet.fund_transfer_save')}}" class="form-horizontal form-bordered" id="fundtransfer_confirm_form" autocomplete="off" onsubmit="return false;">    
    <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"> {{\trans('franchisee/wallet/fundtransfer.from_wallet')}}</label>
        <div class="col-lg-8">
            <p id="d_ewallet_id" class="form-control-static">{{$ewallet_name}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label">{{\trans('franchisee/wallet/fundtransfer.currency')}}</label>
        <div class="col-lg-8">
            <p id="d_currency_id" class="form-control-static">{{$currency_code}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="user_avail_bal">{{\trans('franchisee/wallet/fundtransfer.available_bal')}}:</label>
        <div class="col-lg-8">
            <p id="d_user_balance" class="form-control-static">{{$availbalance}}</p>         
        </div>
	   </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('franchisee/wallet/fundtransfer.to_account')}}</label>
        <div class="col-lg-8">
            <p id="d_to_account" class="form-control-static">{{$to_account}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label" for="amount">{{\trans('franchisee/wallet/fundtransfer.amount')}}</label>
        <div class="col-lg-8">
            <p id="d_totamount" class="form-control-static">{{$totamount}}</p>          
        </div>
    </div>
   
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="tac_code">Sucurity PIN *</label>
        <div class="col-lg-8">
            <div class="input-group">
                <input type="password" name="tac_code" id="tac_code" class="form-control" minlength="4" maxlength="4" />
            </div>
        </div>
    </div>
	
	  <div class="form-group" >
        <div class="col-lg-4 col-sm-1 text-right" >
            <input type="button" name="fund_transfer"  id="back" class="btn  btn-default" value="{{\trans('franchisee/wallet/fundtransfer.bck_btn')}}" />
        </div>
        <div class="col-lg-4 col-sm-1" >
            <input type="button" name="fund_transfer"  id="confirm_fund_transfer" class="btn btn-primary" value="{{\trans('franchisee/wallet/fundtransfer.confirm_transfer')}}" />
        </div>
    </div>	
	 </form> 

@else
<p id="fundtransfer_confirm_form">{{$error}}</p>
@endif