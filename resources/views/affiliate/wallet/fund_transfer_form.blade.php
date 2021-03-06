
 <form method="post" action="{{route('aff.wallet.fund_transfer_confirm')}}"class="form-horizontal form-bordered" id="fundtransferform" autocomplete="off" onsubmit="return false;">
		<div class="form-group hidefld">
                <label class="col-lg-4 col-sm-2 control-label">{{\trans('affiliate/wallet/fundtransfer.wallet')}}</label>
                <div class="col-lg-8 form_field">
					<select name="wallet_id" id="wallet_id" class="form-control">
							   <option value="">{{\trans('affiliate/wallet/fundtransfer.select_wallet')}}</option>
					</select>
				</div>
        </div>
		<div class="form-group hidefld">
			<label class="col-lg-4 col-sm-2 control-label">{{\trans('affiliate/wallet/fundtransfer.currency')}}</label>
			<div class="col-lg-8 form_field">	
			  <select name="currency_id" id="currency_id" class="form-control" >
					   <option value="">{{\trans('affiliate/wallet/fundtransfer.select_currency')}}</option>	
			  </select>
			  </div>
	   </div>
		<div class="form-group">   
			   <label class="col-lg-4 col-sm-2 control-label"  for="user_avail_bal">{{\trans('affiliate/wallet/fundtransfer.available_bal')}}:</label>
			   <div class="col-lg-8 form_field">	
		    <label class="control-label"  for="user_avail_bal">@if(!empty($wallet_balance))@foreach($wallet_balance as                $wlbalance){{$wlbalance->current_balance}}
		     @endforeach
		     @endif
			 </label>
			   <span id="user_balance" style="margin-top:9px; display:inline-block">{{ isset($max_trans_amount) ? $max_trans_amount : '' }}</span>
			   <input type="hidden" name="user_avail_bal" id="user_avail_bal" class="form-control" value="<?php echo $current_balance ? $current_balance : '0';?>"  />
			   </div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('affiliate/wallet/fundtransfer.to_account')}} *</label>
			 <div class="col-lg-8 form_field">
				<div class="input-group input-group-md form_field">
				<input type="text" name="to_account" data-err-msg-to="#to_account_err_msg" id="to_account" placeholder="{{\trans('affiliate/wallet/fundtransfer.to_account_placeholder')}}" class="form-control" value="{{ (isset($rec_email) &&!empty($to_account))? $to_account : ''}}" />				
				<div class="input-group-btn">
					<button type="button" value="search" class="btn btn-md btn-success btn-flat" id="touserCheckBtn"> 
						<span class="glyphicon glyphicon-search"></span> {{\trans('affiliate/wallet/fundtransfer.search_btn')}}
					</button>
				 </div>
				</div>
				<span id="to_account_err_msg"></span>
			</div>
			<input  type="hidden" name="to_account_id" id="to_account_id" value="" />
			<input type="hidden" name="to_cur_balance" id="to_cur_balance" />
		</div>							
		<div class="form-group hidefld2">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('affiliate/wallet/fundtransfer.to_account_full_name')}}</label>
			<div class="col-lg-8 form_field">
			<input type="text" name="rec_name" id="rec_name" class="form-control" value="{{ (isset($rec_name) &&!empty($rec_name))? $rec_name : ''}}" disabled/>
			</div>
		</div>

		<div class="form-group hidefld2">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('affiliate/wallet/fundtransfer.to_account_email')}}</label>
			<div class="col-lg-8 form_field">
			<input type="text" name="rec_email" id="rec_email" class="form-control" value="{{ (isset($rec_email) &&!empty($rec_email))? $rec_email : ''}}" disabled/>
			</div>
		</div>
			<div class="form-group hidefld2">
				<label class="col-lg-4 col-sm-2 control-label" for="amount">{{\trans('affiliate/wallet/fundtransfer.amount')}} *</label>
				 <div class="col-lg-8 form_field">
				 <div class="input-group">
				 <span class="input-group-btn">
						<button class="btn btn-default" type="button">	@if(!empty($wallet_balance)){{$wallet_balance[0]->currency_code}}
						@endif</button>
				  </span>
					<input type="text" id="totamount" name="totamount" class="form-control"  onkeyup="checkamount()" onkeypress="return isNumberKey(event);"  placeholder="" value="{{ (isset($totamount) &&!empty($totamount))? $totamount : ''}}">
				 </div>
				 <input type="hidden" name="avail_balance" id="avail_balance" value="<?php echo $availbalance;?>" />
				 
					<input type="hidden" name="amount" id="amount" value="{{ (isset($totamount) &&!empty($totamount))? $totamount : ''}}" />
					
					<input type="hidden"  name="max_trans_amount"  id="max_trans_amount" value="<?php echo $availbalance;?>" />
					
				<input type="hidden"  name="min_trans_amount"  id="min_trans_amount" value="{{ (isset($min_trans_amount) &&!empty($min_trans_amount))? $min_trans_amount : ''}}"  />
				
					<input type="hidden" name="charge" id="charge" value="{{ (isset($charge) &&!empty($charge))? $charge : ''}}" />
					
					<span class="help-block" id="amount_status"></span>
				</div>
				
			</div>
			<div class="form-group hidefld2">
                 <label class="col-lg-4 col-sm-2 control-label"  for="tac_code">Remarks</label>
                  <div class="col-lg-8">
                   <textarea id="remarks" name="remarks" class="form-control" rows="3" cols="45"></textarea>
				</div>
			</div>			
			
		<div class="form-group hidefld3 form_field" >
			<div class="col-sm-offset-4 col-lg-8 hidefld3 form_field" >				
				<input type="submit" name ="fund_transfer"  id="fund_transfer" class="btn  btn-primary" value="{{\trans('affiliate/wallet/fundtransfer.transfer_btn')}}"/>				
			</div>  
           </div>   
			
			</form>
	