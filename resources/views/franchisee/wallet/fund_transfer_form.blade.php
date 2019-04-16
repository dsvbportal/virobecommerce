    <form method="post" action="{{route('fr.wallet.fund_transfer_confirm')}}"class="form-horizontal form-bordered" id="fundtransferform" autocomplete="off" data-usersearch="{{route('fr.wallet.fundtransfer.usrsearch')}}"  data-fundtransfer="" nsubmit="return false;">
		<div class="form-group hidefld">
			<label class="col-lg-4 col-sm-2 control-label">{{\trans('franchisee/wallet/fundtransfer.wallet')}}</label>
			<div class="col-lg-8 form_field">
				<select name="wallet_id" id="wallet_id" class="form-control">
					<option value="{{$wallet_balance->wallet_id}}">{{$wallet_balance->wallet}}</option>
				</select>
			</div>
        </div>
		<div class="form-group hidefld">
			<label class="col-lg-4 col-sm-2 control-label">{{\trans('franchisee/wallet/fundtransfer.currency')}}</label>
			<div class="col-lg-8 form_field">	
			    <select name="currency_id" id="currency_id" class="form-control" >
					<option value="{{$wallet_balance->currency_id}}">{{$wallet_balance->currency_code}}</option>	
			    </select>
			</div>
	    </div>
		<div class="form-group">   
		    <label class="col-lg-4 col-sm-2 control-label"  for="user_avail_bal">{{\trans('franchisee/wallet/fundtransfer.available_bal')}}:</label>
		    <div class="col-lg-8 form_field">
		    <label class="control-label"  for="user_avail_bal">{{$wallet_balance->current_balance}}</label>
		    <span id="user_balance" style="margin-top:9px; display:inline-block">{{ isset($max_trans_amount) ? $max_trans_amount : '' }}</span>
		    <input type="hidden" name="user_avail_bal" id="user_avail_bal" class="form-control" value="<?php echo $current_balance ? $current_balance : '0';?>"  />
		    </div>
		</div>
		<div class="form-group">
				<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('franchisee/wallet/fundtransfer.to_account')}} *</label>
				 <div class="col-lg-8 form_field">
				<div class="input-group input-group-md form_field">
				<input type="text" name="to_account" id="to_account" class="form-control" value="{{ (isset($rec_email) &&!empty($to_account))? $to_account : ''}}" data-err-msg-to="#to_account_err" />				
				<div class="input-group-btn">
					<button type="button" value="search" class="btn btn-md btn-success btn-flat" onclick="user_check();"> 
						<span class="glyphicon glyphicon-search"></span> Search
					</button>
				</div>
				</div>
				<span id="to_account_err"></span>
				</div>
				<input  type="hidden" name="to_account_id" id="to_account_id" value="" />
				<input  type="hidden" name="to_usercode" id="to_usercode" value="" />
				<input  type="hidden" name="to_account_type_id" id="to_account_type_id" value="" />
				<input type="hidden" name="to_cur_balance" id="to_cur_balance" />
		</div>			
		<div class="form-group hidefld2">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('franchisee/wallet/fundtransfer.to_account_full_name')}}</label>
			<div class="col-lg-8 form_field">
			<input type="text" name="full_name" id="rec_full_name" class="form-control" value="{{ (isset($rec_name) &&!empty($rec_name))? $rec_name : ''}}" disabled/>
			</div>
		</div>
		<div class="form-group hidefld2">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('franchisee/wallet/fundtransfer.to_account_email')}}</label>
			<div class="col-lg-8 form_field">
			<input type="text" name="email" id="rec_email" class="form-control" value="{{ (isset($rec_email) &&!empty($rec_email))? $rec_email : ''}}" disabled/>
			</div>
		</div>
		<div class="form-group frfld hidefld2">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('franchisee/wallet/fundtransfer.company_name')}}</label>
			<div class="col-lg-8 form_field">
			<input type="text" name="company_name" id="rec_company_name" class="form-control"  disabled/>
			</div>
		</div>
		<div class="form-group frfld hidefld2">
			<label class="col-lg-4 col-sm-2 control-label"  for="to_account">{{\trans('franchisee/wallet/fundtransfer.frtype')}}</label>
			<div class="col-lg-8 form_field">
			<input type="text" name="frtype" id="rec_frtype" class="form-control" disabled/>
			</div>
		</div>
		
		<div class="form-group hidefld2">
			<label class="col-lg-4 col-sm-2 control-label" for="amount">{{\trans('franchisee/wallet/fundtransfer.amount')}} *</label>
			 <div class="col-lg-8 form_field">
			 <div class="input-group">
			 <span class="input-group-btn">
					<button class="btn btn-default" type="button">{{$wallet_balance->currency_code}}</button>
			  </span>
				<input type="text" id="totamount" name="totamount" class="form-control"  onkeyup="checkamount()" onkeypress="return isNumberKey(event);"  placeholder="" value="{{ (isset($totamount) &&!empty($totamount))? $totamount : ''}}">
			 </div>
			 <input type="hidden" name="avail_balance" id="avail_balance" value="<?php echo $wallet_balance->current_balance; ?>" />
				<input type="hidden" name="amount" id="amount" value="{{ (isset($totamount) &&!empty($totamount))? $totamount : ''}}" />
				<input type="hidden"  name="max_trans_amount"  id="max_trans_amount" value="<?php echo $wallet_balance->current_balance;?>" />
			    <input type="hidden"  name="min_trans_amount"  id="min_trans_amount" value="{{ (isset($settings->min_amount) &&!empty($settings->min_amount))? $settings->min_amount : '5'}}"  />
				<input type="hidden" name="charge" id="charge" value="{{ (isset($settings->charge_amount) &&!empty($settings->charge_amount))? $settings->charge_amount : ''}}" />
				<span class="help-block" id="amount_status"></span>
			</div>
		</div>
		<div class="form-group hidefld2">
                 <label class="col-lg-4 col-sm-2 control-label"  for="tac_code">Remarks</label>
                  <div class="col-lg-8">
                    <div class="input-group">
                  <textarea id="remarks" name="remarks" class="form-control" rows="3" cols="45"></textarea>
                 </div>
             </div>
           </div>		
		<div class="form-group hidefld3 form_field" >
			<div class="col-sm-offset-4 col-lg-8 hidefld3 form_field" >				
				<input type="submit" name ="fund_transfer"  id="fund_transfer" class="btn  btn-primary" value="{{\trans('franchisee/wallet/fundtransfer.transfer_btn')}}"/>				
			</div>  
        </div>   
	</form>