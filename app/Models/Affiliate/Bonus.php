<?php
namespace App\Models\Affiliate;
use App\Models\BaseModel;
use App\Models\Affiliate\Payments;
use App\Models\Affiliate\Referrals;
use App\Models\Commonsettings;

use DB;
use AppService;

class Bonus extends BaseModel
{
	public function __construct ()
    {
         parent::__construct();
		 $this->affObj 	= new AffModel;
		 $this->walletObj = new Wallet;		 
		 $this->commonObj = new Commonsettings();
    }
	
	public function referral_bonus_details($account_id,$arr=array())
	{		
		extract($arr);	
		$refSql= DB::table($this->config->get('tables.REFERRAL_EARNINGS').' as re')
			->join($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'). ' as ast','ast.subscribe_topup_id','=','re.subscrib_topup_id')
			->join($this->config->get('tables.AFF_PACKAGE_PRICING'). ' as pri','pri.package_id','=','ast.package_id')
			->join($this->config->get('tables.ACCOUNT_MST') . ' as fum','fum.account_id','=','re.from_account_id')
			->join($this->config->get('tables.ACCOUNT_TREE') . ' as ut','ut.account_id','=','re.from_account_id')
			->join($this->config->get('tables.ACCOUNT_MST') . ' as rfum','rfum.account_id','=','ut.sponsor_id')
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as rfud','rfud.account_id','=','rfum.account_id') 
			->join($this->config->get('tables.ACCOUNT_MST') . ' as tum','tum.account_id','=','re.to_account_id')
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as tud','tud.account_id','=','tum.account_id')
			->join($this->config->get('tables.AFF_PACKAGE_MST') . ' as pm','pm.package_id','=','ast.package_id')
			->join($this->config->get('tables.AFF_PACKAGE_LANG') . ' as pl','pl.package_id','=','pm.package_id')
			->join($this->config->get('tables.CURRENCIES') . ' as cur','cur.id','=','re.currency_id')
			->join($this->config->get('tables.PAYMENT_TYPES').' as pt','pt.payment_type_id','=', 're.payout_type')
			->join($this->config->get('tables.WALLET_LANG') . ' as wal','wal.wallet_id', '=', 're.wallet_id')
			->join($this->config->get('tables.ACCOUNT_STATUS_LOOKUP').' as usl', 'usl.status_id', ' = ', 'fum.status')
			->join($this->config->get('tables.ACCOUNT_STATUS_LANG').' as uslang', function($subquery)
				{
					 $subquery->on('uslang.status_id', ' = ', 'usl.status_id')
					 ->where('uslang.lang_id', '=', $this->config->get('app.locale_id'));
			})
			->where('re.to_account_id',$this->userSess->account_id);
			$refSql->select(DB::Raw("re.*,re.ref_id,re.payout_type,rfum.account_id,rfum.uname as sponser_uname,concat_ws('',rfud.first_name,rfud.last_name) as sponser_full_name,fum.account_id,re.created_date,tum.uname as to_uname,fum.uname as from_uname,concat_ws(' ',tud.first_name, tud.last_name) as to_full_name,pl.package_name,re.amount,IF(re.payout_type=1,(select `wallet` from ".$this->config->get('tables.WALLET_LANG') . " as wal where `wal`.`wallet_id` = re.wallet_id),(select `payment_type` from ".$this->config->get('tables.PAYMENT_TYPES') . " as pt where `pt`.`payment_type_id` = re.payout_type)) as pay_mode,(select uname from ".config('tables.ACCOUNT_MST')." where account_id = (select upline_id from ".config('tables.ACCOUNT_TREE')." where account_id = re.from_account_id )) as upline_username,cur.code as currency,cur.currency_symbol,re.status,pri.price as packagepricing,usl.disp_class,uslang.status_name"));
	 	if (isset($from_date) && isset($to_date) && !empty($from_date) && !empty($to_date))
		{
			$refSql->whereRaw("DATE(re.created_date) >='".date('Y-m-d', strtotime($from_date))."'");
			$refSql->whereRaw("DATE(re.created_date) <='".date('Y-m-d', strtotime($to_date))."'");
		}
		else if (!empty($from_date) && isset($from_date))
		{
			$refSql->whereRaw("DATE(re.created_date) <='".date('Y-m-d', strtotime($from_date))."'");
		}
		else if (!empty($to_date) && isset($to_date))
		{
			$refSql->whereRaw("DATE(re.created_date) >='".date('Y-m-d', strtotime($to_date))."'");
		}
		if (isset($type_of_package) && !empty($type_of_package))
        {		
            $refSql->where("pl.package_id",$type_of_package);
        }
		
		if (isset($search_term) && !empty($search_term))
        {		
            if(!empty($filterchk) && !empty($filterchk))
			{
				$search_term='%'.$search_term.'%'; 
				$search_field=['FromUser'=>'fum.uname','Referral'=>'rfum.uname'];
				$refSql->where(function($sub) use($filterchk,$search_term,$search_field){	
					foreach($filterchk as $search)
					{  
						if(array_key_exists($search,$search_field)){
							  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_term);
						} 
					}
				});
			}			
			else{				
			   $refSql->where(function($wcond) use($search_term){
			   $wcond->whereRaw("concat_ws('',tud.first_name,tud.last_name) like '%$search_term%'")
				   ->orWhereRaw("concat_ws('',rfud.first_name,rfud.last_name) like '%$search_term%'")
				   ->orWhereRaw("rfum.uname like '%$search_term%'")
				   ->orWhereRaw("fum.uname like '%$search_term%'")
				   ->orWhereRaw("tum.uname like '%$search_term%'");
				});			
			}	 
		} 
		$refSql->orderBy('re.created_date', 'desc');	
        if (isset($length) && !empty($length))
        {
            $refSql->skip($start)->take($length);
        }
		
		if (isset($count) && !empty($count))
        {
            return $refSql->count();
        }	
		else
		{	
			$result = $refSql->get();
			if(!empty($result)) {
				$status_type_arr = ['0'=>'warning','1'=>'success','2'=>'danger','3'=>'info'];
				array_walk($result,function(&$ftdata) use($status_type_arr){
					$ftdata->Famount = $ftdata->currency_symbol.' '.number_format($ftdata->amount, \AppService::decimal_places($ftdata->amount), '.', ',').' '.$ftdata->currency;
					//$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
});
				return $result;
			}
			else
			return false;						
		}		
	}
	
	public function getBonusSetting($bonus_type,$list=false){
		if($bonus_type){
			$qry = DB::table($this->config->get('tables.AFF_BONUS_TYPES').' as bt')
						->join($this->config->get('tables.AFF_BONUS_CV_PERC') . ' as btc','btc.bonus_type','=','bt.bonus_type_id')
						->where('bt.bonus_type_id','=',$bonus_type)
						->select('bt.bonus_type_id','bt.bonus_name','bt.credit_wallet_id','bt.has_tax','bt.tax_class_id','btc.min_cv','btc.max_cv','btc.perc','btc.ngo_wallet_perc');			
			if($list==false){
				return $qry->first();
			} else {
				return $qry->get();
			}
		}
		return false;
	}
	
	public function addReferralBonus($purchaseInfo){
		$total_service_tax_per = $tax_amt = $paid_amt = 0;
        $service_tax_details = array();
        $service_tax_per = array();
		$send_receipt = 0;
		$receiptinfo_array = '';
		$pg_ststus = 0;
		$refData = [];
		$credit_bonus = 0;
		$taxAmt = $tot_tax = 0;		 
		
		$wd['subscrib_id'] = $purchaseInfo->subscribe_id;
		$wd['subscrib_topup_id'] = $purchaseInfo->subscribe_topup_id;		
		$exists = DB::table($this->config->get('tables.REFERRAL_EARNINGS'))                   
					->where($wd)
                    ->exists();						
		
		if(!$exists && !empty($purchaseInfo) && $purchaseInfo->package_qv > 0) 
		{
			/* 67 - fast start bonus taxes */			
			$spInfo = $this->affObj->getSponsorInfo(['account_id'=>$this->userSess->account_id]);
		
			$cvConvertData = $this->commonstObj->getSettings('qv_currency_rate',true);
			$bonusInfo = $this->getBonusSetting($this->config->get('constants.BONUS_TYPE.FAST_START_BONUS'));
			$rate = isset($cvConvertData[$purchaseInfo->currency_id])? $cvConvertData[$purchaseInfo->currency_id]: 0;			
			
			$transaction_id = AppService::getTransID($purchaseInfo->account_id);
			$earnings_qv = number_format(($purchaseInfo->package_qv * $bonusInfo->perc/100),2,'.','');
			$bonusAmt = number_format(($earnings_qv * $rate),2,'.','');
			
			if($bonusInfo->has_tax){
				list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json) = $this->getTax(['account_id'=>$spInfo->account_id,'amount'=>$bonusAmt,'country_id'=>$spInfo->country_id,'statementline_id'=>$this->config->get('stline.FAST_START_BONUS.CREDIT')]);	
				
				$refData['service_tax_details'] = $tax_json;
				$refData['service_tax_per'] = $tot_tax_perc;
				$refData['service_tax'] = $tot_tax;	
				$refData['tax_class_id'] = $tax_class_id;				
			}						
			$taxAmt = number_format($tot_tax,2,'.','');			
			$ngoPerc = number_format($bonusInfo->ngo_wallet_perc,2,'.','');
			$ngoAmt = number_format(($bonusAmt * $bonusInfo->ngo_wallet_perc/100),2,'.','');
			$netpay = number_format(($bonusAmt - ($taxAmt+$ngoAmt)),2,'.','');	
			
			$refData['from_account_id'] = $purchaseInfo->account_id;
			$refData['to_account_id'] = $spInfo->account_id;
			$refData['subscrib_id'] = $purchaseInfo->subscribe_id;
			$refData['subscrib_topup_id'] = $purchaseInfo->subscribe_topup_id;		
			$refData['transaction_id'] = $transaction_id;			
			$refData['currency_id'] = $purchaseInfo->currency_id;
			$refData['wallet_id'] = $bonusInfo->credit_wallet_id;
			$refData['amount'] = $purchaseInfo->paid_amt; 
			$refData['qv'] = $purchaseInfo->package_qv;
			$refData['perc'] = $bonusInfo->perc;
			$refData['c_rate'] = $rate;
			$refData['earnings_qv'] = $earnings_qv;			
			$refData['commission'] = $bonusAmt;						
			$refData['ngo_wallet_amt'] = $ngoAmt;
			$refData['net_pay'] = $netpay;			
			$refData['created_date'] = getGTZ();			
			if ($spInfo->is_root_account == $this->config->get('constants.ON'))
			{
				$refData['status'] = $this->config->get('constants.BONUS_STATUS.RELEASED');					
				$refData['confirmed_date'] = getGTZ();
				$credit_bonus = 1;
			}
			else if($spInfo->block==$this->config->get('constants.UNBLOCKED')) {
				$refData['status'] = $this->config->get('constants.BONUS_STATUS.RELEASED');	
				$refData['confirmed_date'] = getGTZ();
				$credit_bonus = 1;
			}
			else if($spInfo->block==$this->config->get('constants.BLOCKED')) {
				$refData['status'] = $this->config->get('constants.BONUS_STATUS.WAITING');				
				$credit_bonus = 0;
			}	
			
			$refid = DB::table($this->config->get('tables.REFERRAL_EARNINGS'))                    
                    ->insertGetID($refData);
			
			if($refid>0){
				if($credit_bonus){					
					$usrbal_upres = $this->walletObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$bonusInfo->credit_wallet_id, 'account_id'=>$spInfo->account_id, 'currency_id'=>$purchaseInfo->currency_id, 'amount'=>$netpay, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));					
					
					$transaction_id = AppService::getTransID($spInfo->account_id);
					
					$trans = [];
					$trans['account_id'] = $spInfo->account_id;
					$trans['from_account_id'] = $purchaseInfo->account_id;
					$trans['statementline_id'] = 67; /* referral bonus credit */
					$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
					$trans['relation_id'] = $refid;
					$trans['amt'] = $bonusAmt;
					$trans['tax'] = $taxAmt;
					$trans['handle_amt'] = $ngoAmt;
					$trans['paid_amt'] = $netpay;
					$trans['currency_id'] = $purchaseInfo->currency_id;
					$trans['wallet_id'] = $bonusInfo->credit_wallet_id;
					$trans['transaction_id'] = $transaction_id;
					$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
					$trans['remark'] = addslashes(json_encode(['data'=>['user_code'=>$purchaseInfo->user_code,'package'=>$purchaseInfo->package_name]]));
					$trans['created_on'] = getGTZ();
					$trans['current_balance'] = $usrbal_upres->current_balance;
					$trans['status'] = $this->config->get('constants.ACTIVE');
					$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
							->insertGetId($trans);
					
					if($transResID>0 && $ngoAmt>0)
					{						
						$usrbal_upres2 = $this->walletObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIH'), 'account_id'=>$spInfo->account_id, 'currency_id'=>$purchaseInfo->currency_id, 'amount'=>$ngoAmt, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
					}
				}
			} 
			return $refid;
		}
		return false;
	}	
	
	
	
	public function tds_balance_update ($arrData)
    {
        $start_date = $end_date = '';
        extract($arrData);
        $tds_per = $tds_total_commission = $tds_amount = $tds_relation_id = $total_commission = 0;
        $tds_arr = $this->commonstObj->getSettings('commission_tds',true);
		
        $current_date = getGTZ('Y-m-d');
        $date = explode('-', $current_date);
		
        $current_year = $date[0];
        $current_month = $date[1];
        $start_month = 4;
        $end_month = 3;
        $data['transferred_on'] = date('Y-m-d H:i:s');
        $data['currency_id'] = $currency_id;
        $data['account_id'] = $account_id;
        $data['tds_tot_com_credit'] = $data['tds_tot_com_debit'] = $data['tds_current_com'] = 0;
        if ($date[1] >= 4)
        {
            $start_date = $current_year.'-04-01';
            $end_date = ($current_year + 1).'-03-31';
        }
		if ($current_month <= 3)
        {
            $start_date = ($current_year - 1).'-04-01';
            $end_date = $current_year.'-03-31';
        }
        $result = DB::table($this->config->get('tables.FRANCHISEE_TDS_LOOKUP'))
                ->where('account_id', $account_id)
                ->where('currency_id', $currency_id)
                ->whereRaw("Date(transferred_on) BETWEEN '".$start_date."' AND '".$end_date."'")
                ->first();

        if ($tds_arr && !empty($commission))
        {            
            $country = $country;
            if (array_key_exists($country, $tds_arr))
            {
                $tds_min_amount = $tds_arr['min_amount'];                
                if ($result)
                {
                    $tds_per = $tds_arr[$country];
                    $data['tds_tot_com_credit'] = $total_commission = $result->tds_tot_com_credit + $commission;
                    $tds_total_commission = $total_commission - $result->tds_tot_com_debit;
                    $data['tds_tot_com_debit'] = $result->tds_tot_com_debit;
                    $data['tds_current_com'] = $result->tds_current_com + $commission;
                }
                else
                {
                    $tds_per = $tds_arr[$country];
                    $data['tds_tot_com_credit'] = $total_commission = $tds_total_commission = $commission;
                    $data['tds_current_com'] = $total_commission;
                    $data['tds_tot_com_debit'] = 0;
                }
            }
        }
        if (!empty($tds_per) && $total_commission > $tds_min_amount)
        {
            $tds_amount = $tds_total_commission * ($tds_per / 100);
            $data['tds_tot_com_debit'] = $data['tds_tot_com_debit'] + $tds_total_commission;
            $data['tds_current_com'] = $data['tds_tot_com_credit'] - $data['tds_tot_com_debit'];
            $tds_relation_id = DB::table($this->config->get('tables.FRANCHISEE_TDS_TRANSACTION'))
                    ->insertGetId(array(
                'account_id'=>$account_id,
                'total_commission'=>$tds_total_commission,
                'currency_id'=>$currency_id,
                'tds_per'=>$tds_per,
                'tds'=>$tds_amount,
                'transferred_on'=>$current_date));
        }
        else
        {
            $tds_per = 0;
        }
        if ($result)
        {
            DB::table($this->config->get('tables.FRANCHISEE_TDS_LOOKUP'))
                    ->where('ftds_id', $result->ftds_id)
                    ->update($data);
        }
        else
        {
            DB::table($this->config->get('tables.FRANCHISEE_TDS_LOOKUP'))
                    ->insert($data);
        }
        $data['tds_total_commission'] = $tds_total_commission;
        $data['tds_per'] 			  = $tds_per;
        $data['tds_amount'] 		  = $tds_amount;
        return $data;
    }
	
	public function add_bonus($arr){
		extract($arr);
		if(isset($member_id) && !empty($member_id)){
			$account_id = DB::table(config('tables.ACCOUNT_TREE').' as at')
						->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','at.account_id')
				        ->where('am.user_code',$member_id)
						->value('at.sponsor_id');
			if($account_id){
				$insdata['account_id']  = $account_id;
				$insdata['date'] 		= date('Y-m-d');
				$insdata['member_id'] 	= $member_id;
				$insdata['merchant_id'] = $merchant_id;
				$insdata['mode'] 		= $mode;
				$insdata['country'] 	= $country;
				$insdata['bill_amount'] = $bill_amount;
				$insdata['cv'] 			= $cv;
				$insdata['trans_type']  = $trans_type;
				$res = DB::table(config('tables.PERSONAL_BONUS_MONTHLY_DETAILS'))
					   ->insertGetId($insdata);
				return $res;
			}
		}
		return false;
	}
	
	
	
	public function package_purchase_bonus_commission(array $arr = array())
    {
		$fund_details	     = $arr;
    	$purchase_commission = 0;
		$op['msg'] 			 = 'error';
        if(!empty($arr['account_id']))
        {
		    $accId              = $arr['account_id'];
			$franchisee_details = $this->fr_benefits($accId);
			if(!empty($fr_commissions)){
				$purchase_commission  = $commission_paidamt = ($arr['amount'] / 100) * $franchisee_details->wallet_purchase_per;
			}
            $fund['created_on']    = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['status'] 	   = $this->config->get('constants.ON');
            if(!empty($arr['relation_id']))
            {
				$fft_relation_id = $arr['relation_id'];
					//$update_trans 	 = 1;
                	/* share commission to uplines */
					if($franchisee_details->level > 1)
					{
						$fund_details['currency_id'] 	= $franchisee_details->currency_id;
						$com_details_data['account_id'] = $franchisee_details->account_id;
						$middle_level_franchisees  		= $this->get_middle_level_franchisees(array('to_franchisee_details'=>$franchisee_details,'to_account_id'=>$franchisee_details->account_id));
						
					/* echo '<pre>';print_r($middle_level_franchisees);exit; */
				
						if(!empty($middle_level_franchisees))
						{
							$currency_code = $this->commonObj->get_currency_code($fund_details['currency_id']);
							$downline_franchisee_per = $franchisee_details->wallet_purchase_per;
							
							foreach ($middle_level_franchisees as $franchisee)
							{
								$per   		=  $franchisee->wallet_purchase_per - $downline_franchisee_per;
								$downline_franchisee_per = $franchisee->wallet_purchase_per;
								if ($fft_relation_id && $per > 0)
								{
									$amount 				 = $arr['amount'];
									$franchisee_commission 	 = 0;
									$franchisee_commission 	 = ($per/100) * ($amount);
									$franchisee_current_balance = $franchisee_tds_per = $tds_total_commission = $frans_tds_amount = $frans_tds_relation_id   = 0;
									$current_date 			 = getGTZ();
									$tot_tax 				 = 0;
									$to_user_transaction_id  = \AppService::getTransID($franchisee_details->account_id);
									list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json) = $this->getTax(['account_id'=>$franchisee->account_id,'amount'=>$franchisee_commission,'country_id'=>$franchisee->country,'statementline_id'=>config('stline.FRANCHISEE_COMMISSION_CREDIT')]);	
									$tds_details['service_tax_details'] = $tax_json;
									$tds_details['service_tax_per'] 	= $tot_tax_perc;
									$tds_details['service_tax'] 		= $tot_tax;	
									$tds_details['tax_class_id'] 		= $tax_class_id;	
									$remark = ['to'=>$franchisee_details->uname,'franchisee_type'=>$franchisee_details->franchisee_type_name,'currency'=>$currency_code,'amount'=>$franchisee_commission];
									$data = array(
											'account_id'=>$franchisee->account_id,
											'relation_id'=>$fft_relation_id,
											'currency_id'=>$fund_details['currency_id'],
											'amount'=>$amount,
											'commission_type'=>config('constants.FR_COMMISSION_TYPE.PURCHASE_PACKAGE_COMMISSION'),
											'commission_perc'=>$per,
											'commission_amount'=>$franchisee_commission,
											'status'=>config('constants.STATUS_CONFIRMED'),
											'statementline_id'=>config('stline.FRANCHISEE_COMMISSION_CREDIT'),
											'created_date'=>$current_date,
											'confirmed_date'=>$current_date,
											'remark'=>addSlashes(json_encode(['data'=>$remark])),
										);
									$relation_id = DB::table(config('tables.FRANCHISEE_COMMISSION'))
													->insertGetId($data);
									if($relation_id)
									{
											$com_details_data['fr_com_id']  	 = $relation_id;
											if (isset($franchisee->country_id) && !empty($franchisee->country_id))
												$com_details_data['country_id']  = $franchisee->country;
											if (isset($userdetails->state_id) && !empty($userdetails->state_id))
												$com_details_data['state_id'] 	 = $userdetails->state_id;
											if (isset($userdetails->district_id) && !empty($userdetails->district_id))
												$com_details_data['district_id'] = $userdetails->district_id;
											if (isset($userdetails->region_id) && !empty($userdetails->region_id))
												$com_details_data['region_id']   = $userdetails->region_id;
											if (isset($userdetails->city_id) && !empty($userdetails->city_id))
												$com_details_data['city_id'] 	 = $userdetails->city_id;
											
										$this->addFranchiseeCommissionDetails($com_details_data);
									}
									$update_balance3 = $this->update_franchisee_balance(
													array(
														'account_id'=>$franchisee->account_id,
														'amount'=>$franchisee_commission,
														'currency_id'=>$fund_details['currency_id'],
														'transaction_type'=>config('constants.CREDIT'),
														'ewallet_id'=>config('constants.WALLETS.VI')));
									if($update_balance3)
									{
										$decimal_places = $this->commonObj->decimal_places($amount);
										$remark 		= ['percentage'=>$per,'amount'=>number_format($amount, $decimal_places, '.', ','),'to'=>$franchisee_details->uname,'franchisee_type'=>$franchisee_details->franchisee_type_name,'currency'=>$currency_code];
										$status = DB::table(config('tables.ACCOUNT_TRANSACTION'))
													->insertGetId(
																array(
																'account_id'=>$franchisee->account_id,
																'to_account_id'=>$franchisee->account_id,
																'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),
																'statementline_id'=>config('stline.PACKAGE_PURCHASE_COMMISSION'),
																'amt'=>$franchisee_commission,
																'paid_amt'=>$franchisee_commission,
																'handle_amt'=>0,
																'tax'=>$tot_tax,
																'wallet_id'=>config('constants.WALLETS.VI'),
																'currency_id'=>$fund_details['currency_id'],
																'transaction_type'=>config('constants.CREDIT'),
																'remark'=>addSlashes(json_encode(['data'=>$remark])),
																'from_account_id'=>$franchisee_details->account_id,
																'ip_address'=>\Request::getClientIp(true),
																'transaction_id'=>$transaction_id = \AppService::getTransID($franchisee_details->account_id),
																'current_balance'=>$update_balance3['current_balance'],
																'status'=>1,
																'created_on'=>$current_date,
																'updated_on'=>$current_date,
																'relation_id'=>$relation_id
															));
											$op['msg'] = 'Success';
										if ($status)
										{
												/************* Notification  data *****************/
											$created_on = getGTZ();
											$ip = \Request::getClientIp(true);
											$mdata['full_name'] = $franchisee->full_name;
											$mdata['franchisee_type'] = $franchisee_details->franchisee_type;
											$mdata['franchisee_uname'] = $franchisee_details->uname;
											$mdata['franchisee_full_name'] = $franchisee_details->fullname;
											$mdata['uname'] 	 = $franchisee->uname;
											$mdata['country']	 = $franchisee->country;
											$mdata['amount'] 	 = number_format($franchisee_commission, 2, '.', ',');
											$mdata['currency'] 	 = $this->commonObj->get_currency_name($fund_details['currency_id']);
											$mdata['transaction_id'] = $transaction_id;
											$mdata['created_on'] = $created_on;
											$mdata['country'] 	 = $this->commonObj->getCountryName($franchisee->country);
											$mdata['ip'] 		 = $ip;
											
										
											/* $this->commonObj->addNotification([
												'access_ids'=>$franchisee->account_id,
												'statementline_id'=>78,
												'details'=>[
													'description'=>'You received commission amount - '.$mdata['amount'].' '.$mdata['currency'].' from '.$franchisee_details->uname,
													'url'=>'franchisee/fundtransfer-commission'
												]
											]);
											 new MailerLib(
													array(
												'to'=>$franchisee->email,
												'subject'=>$fund_details['pagesettings']->site_name.' - Commission Received :: '.$transaction_id.', Amount - '.$mdata['amount'].' '.$mdata['currency'],
												'html'=>View::make('emails.admin_add_fund_franchisee_commission', $mdata)->render(),
												'from'=>config('constants.SYSTEM_MAIL_ID'),
												'fromname'=>config('constants.DOMAIN_NAME')
											)); */
										}
									}
								}
							}
						}
					}else
					{
						return false;
					}
		        return $op;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }
	
	public function update_franchisee_balance ($arrData = '')
    {
        extract($arrData);
        if ($arrData)
        {
            $tot_credit = $tot_debit = $current_balance = 0;
            $franchisee_balance = $this->commonObj->get_user_balance($account_id, $ewallet_id, $currency_id);
            if ($franchisee_balance)
            {
                $tot_credit = $franchisee_balance->tot_credit;
                $tot_debit = $franchisee_balance->tot_debit;
                $current_balance = $franchisee_balance->current_balance;
            }
            if ($transaction_type == config('constants.CREDIT'))
            {
                $data['tot_credit'] = $tot_credit + $amount;
                $data['tot_debit'] = $tot_debit;
                $data['current_balance'] = $current_balance + $amount;
            }
            elseif ($transaction_type == config('constants.DEBIT'))
            {
                $data['tot_credit'] = $tot_credit;
                $data['tot_debit'] = $tot_debit + $amount;
                $data['current_balance'] = $current_balance - $amount;
            }
            if ($franchisee_balance)
            {
                $status = DB::table(config('tables.ACCOUNT_BALANCE'))
                        ->where('balance_id', $franchisee_balance->balance_id)
                        ->update($data);
            }
            else
            {
                $status = DB::table(config('tables.ACCOUNT_BALANCE'))
                        ->insertGetID(
                        array(
                            'ewallet_id'=>$ewallet_id,
                            'currency_id'=>$currency_id,
                            'account_id'=>$account_id,
                            'tot_credit'=>$data['tot_credit'],
                            'tot_debit'=>$data['tot_debit'],
                            'current_balance'=>$data['current_balance'],
                            'updated_date'=>getGTZ()
                ));
            }
            return $data;
        }
        return false;
    }
	
	public function addFranchiseeCommissionDetails($arr = array())
    {
        $country_id = $state_id = $district_id = $region_id = $city_id = null;
        extract($arr);
		$data = compact('country_id', 'state_id', 'district_id', 'region_id', 'city_id');
     	if(isset($country_id))
			$data['country_id'] = $country_id;
		if(isset($state_id))
			$data['state_id'] = $state_id;
		if(isset($city_id))
			$data['city_id'] = $city_id;
		if(isset($district_id))
			$data['district_id'] = $district_id;
		if(isset($region_id))
			$data['region_id'] = $region_id;
		
        if (isset($fr_com_id))
        {
            if (DB::table(config('tables.FRANCHISEE_COMMISSION_DETAILS'))
                            ->where('fr_com_id', $fr_com_id)
                            ->count() > 0)
            {
                return DB::table(config('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->where('fr_com_id', $fr_com_id)
                                ->update($data);
            }
            else
            {
                $data['fr_com_id'] = $fr_com_id;
                return DB::table(config('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->insertGetID($data);
            }
        }
    }
	
	public function fr_benefits($account_id){
		$qry =  DB::table(config('tables.FRANCHISEE_MST').' as mst')
				->join(config('tables.FRANCHISEE_BENIFITS').' as b','b.franchisee_type','=','mst.franchisee_type')
				->join(config('tables.ACCOUNT_MST').' as ac','ac.account_id','=','mst.account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','mst.account_id')
				->join(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'mst.account_id')
				->join(config('tables.FRANCHISEE_LOOKUP').' as l','l.franchisee_typeid','=','mst.franchisee_type')
				->where('mst.account_id',$account_id)
				->select('mst.account_id','mst.currency as currency_id','b.diff_commission_per','b.flexible_commission_per','b.wallet_purchase_per','b.charity_donation_per','b.fb_id','l.level','ac.uname','b.franchisee_type','l.franchisee_type as franchisee_type_name',DB::raw("CONCAT_WS(' ',ad.firstname,ad.lastname) as fullname"),
				DB::raw("CASE fal.access_location_type
					WHEN ".config('constants.FRANCHISEE_TYPE.COUNTRY')." THEN (select country as name from ".config('tables.LOCATION_COUNTRY')." where country_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.REGION')." THEN (select region as region_name from ".config('tables.LOCATION_REGIONS')."  where region_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.STATE')." THEN (select state as name from ".config('tables.LOCATION_STATE')."  where state_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.DISTRICT')." THEN (select district as district_name from ".config('tables.LOCATION_DISTRICTS')."  where district_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.CITY')." THEN (select city as city_name from ".config('tables.LOCATION_CITY')."  where city_id = fal.relation_id)
					END as franchisee_location")
				);
			$result = $qry->first();
			return $result;
	}

	public function get_middle_level_franchisees ($arr = array())
    {
        $middle_level_franchisees = array();
        extract($arr);
        $to_franchisee_location = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION'))
                ->where('account_id', $to_account_id)
                ->where('status', config('constants.ACTIVE'))
                ->where('access_location_type', $to_franchisee_details->franchisee_type)
                ->pluck('relation_id');
				
		$to_franchisee_location = $to_franchisee_location[0];
	    switch ($to_franchisee_details->franchisee_type)
        {
            case config('constants.FRANCHISEE_TYPE.REGION'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_REGIONS').' as rl')
                        ->join(config('tables.LOCATION_STATE').' as s', 's.region_id', '=', 'rl.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('rl.region_id, rl.region, s.state_id,s.state as state_name,co.country_id,co.country as country_name'))
                        ->where('rl.region_id', $to_franchisee_location)
                        ->first();
                break;
            case config('constants.FRANCHISEE_TYPE.CITY'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_CITY').' as c')
                        ->join(config('tables.LOCATION_DISTRICTS').' as d', 'd.district_id', '=', 'c.district_id')
                        ->join(config('tables.LOCATION_STATE').' as s', 's.state_id', '=', 'd.state_id')
                        ->leftjoin(config('tables.LOCATION_REGIONS').' as rl', 'rl.region_id', '=', 's.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('c.city_id,c.city_name,d.district_id,d.district as district_name,s.state_id,(select GROUP_CONCAT(state_id) from '.config('tables.LOCATION_STATE').'  where is_union_territory = 1 and linked_state_id = d.state_id and status = 1 GROUP BY linked_state_id) as  union_territory_id, s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region'))
                        ->where('c.city_id', $to_franchisee_location)
                        ->first();
                break;
            case config('constants.FRANCHISEE_TYPE.DISTRICT'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_DISTRICTS').' as d')
                        ->join(config('tables.LOCATION_STATE').' as s', 's.state_id', '=', 'd.state_id')
                        ->leftjoin(config('tables.LOCATION_REGIONS').' as rl', 'rl.region_id', '=', 's.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('d.district_id,d.district as district_name,s.state_id,(select GROUP_CONCAT(state_id) from '.config('tables.LOCATION_STATE').' where is_union_territory = 1 and linked_state_id = d.state_id and status = 1 GROUP BY linked_state_id) as  union_territory_id, s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region'))
                        ->where('d.district_id', $to_franchisee_location)
                        ->first();
                break;
            case config('constants.FRANCHISEE_TYPE.STATE'):                
                $franchisee_location_details = DB::table(config('tables.LOCATION_STATE').' as s')
                        ->leftjoin(config('tables.LOCATION_REGIONS').' as rl', 'rl.region_id', '=', 's.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('s.state_id,s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region as region'))
                        ->where('s.state_id', $to_franchisee_location)
                        ->first();
		        break;
            case config('constants.FRANCHISEE_TYPE.COUNTRY'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_COUNTRY'))
                        ->select(DB::raw('country_id,country as country_name'))
                        ->where('country_id', $to_franchisee_location)
                        ->first();
                break;
        }		
		
        if (isset($franchisee_location_details->city_id))
        {
            $city_id = $franchisee_location_details->city_id;
        }
        if (isset($franchisee_location_details->district_id))
        {
            $district_id = $franchisee_location_details->district_id;
        }
        if (isset($franchisee_location_details->state_id))
        {
            $state_id = $franchisee_location_details->state_id;
        }
        if (isset($franchisee_location_details->union_territory_id))
        {
            $union_territory_id = $franchisee_location_details->union_territory_id;
        }
        if (isset($franchisee_location_details->country_id))
        {
            $country_id = $franchisee_location_details->country_id;
        }
        if (isset($franchisee_location_details->region_id) && !empty($franchisee_location_details->region_id))
        {
            $region_id = $franchisee_location_details->region_id;
        }		
        switch ($to_franchisee_details->franchisee_type)
        {
            case config('constants.FRANCHISEE_TYPE.CITY'):
				if(!empty($city_id)){
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.CITY'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $city_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,fal.relation_id as city_id,um.uname,um.email,fl.franchisee_type,fb.wallet_purchase_per,up.country_id as country')
                        ->first();
				}
            case config('constants.FRANCHISEE_TYPE.DISTRICT'):
				if(!empty($district_id)){
					
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.DISTRICT'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $district_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fal.relation_id as district_id,fl.franchisee_type,fb.wallet_purchase_per,up.country_id as country')
                        ->first();
					
				}		
            case config('constants.FRANCHISEE_TYPE.STATE'):
				if(!empty($state_id)){
			    $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.STATE'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->whereRaw("fal.relation_id LIKE '%".$state_id."%'")
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fal.relation_id as state_id,fl.franchisee_type,fb.wallet_purchase_per,ap.country_id as country')
                        ->first();
				}	
            case config('constants.FRANCHISEE_TYPE.REGION'):
				if(!empty($region_id)){
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.REGION'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $region_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fl.franchisee_type,fal.relation_id as region_id,fb.wallet_purchase_per,ap.country_id as country')
                        ->first();
				}		
            case config('constants.FRANCHISEE_TYPE.COUNTRY'):
				if(!empty($country_id)){
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.COUNTRY'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $country_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fal.relation_id as country_id,fl.franchisee_type,fb.wallet_purchase_per,ap.country_id as country')
                        ->first();
				}		
        }
		
        return array_filter($middle_level_franchisees);
    }
	public function getCV_totals($arr=array(),$reqMonth='current'){
		$res = 0;
		if(!empty($arr['account_id']) && $arr['account_id']>0){
			$qry = DB::table(config('tables.PERSONAL_COMMISSION').' as ccm')
				->where('ccm.account_id','=',$arr['account_id'])
				->where('ccm.status','=',$this->config->get('constants.ACTIVE'))
				->select(DB::Raw('SUM(ccm.cv) as sales'));
			
			switch($reqMonth){
				case 'last':
					$qry->whereMonth('created_on','=',getGTZ('m',date('Y-m-d',strtotime('-1 month'))))
					->whereYear('created_on','=',getGTZ('Y',date('Y-m-d',strtotime('-1 month'))));
				break;
				default:
					$qry->whereMonth('created_on','=',getGTZ('m'))
					->whereYear('created_on','=',getGTZ('Y'));
				break;			
			}
			$res = $qry->first();
		}
		return 	!empty($res->sales)? $res->sales:0;
	}
	
	public function getQV_totals($arr=array(),$reqMonth='current'){
		$res = 0;
		if(!empty($arr['account_id']) && $arr['account_id']>0){						
			$referralsObj = new Referrals;
			$parent_details = $referralsObj->getUser_lineage(['account_id'=>$arr['account_id']]);			

			switch($reqMonth){
				case 'last':
					$subqry = DB::table(config('tables.ACCOUNT_TREE').' as utr')
						->join(config('tables.ACCOUNT_MST').' as um', function($join) {
							$join->on('um.account_id', ' = ', 'utr.account_id');
						})					
						->where('utr.lft_node', '>', $parent_details->lft_node)
						->where('utr.rgt_node', '<', $parent_details->rgt_node)
                        ->where('utr.nwroot_id', '=', $parent_details->nwroot_id)
						->select('utr.account_id');
					
					$qry = DB::table(DB::raw('('.$subqry->tosql().') as ut'))
                        ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usl', 'usl.account_id', ' = ', 'ut.account_id')
						->addBinding($subqry->getBindings(), 'join')           
						->whereMonth('confirm_date','=',getGTZ(date('Y-m-d',strtotime('-1 month')),'m'))
						->whereYear('confirm_date','=',getGTZ(date('Y-m-d',strtotime('-1 month')),'Y'))								
						->where('usl.status', '=', 1)
						->where('usl.payment_status', '=', 1)				
						->select(DB::Raw('SUM(usl.package_qv) as sales'));
					$res =  $qry->first();	
					
				break;
				default:
					$subqry = DB::table(config('tables.ACCOUNT_TREE').' as utr')
						->join(config('tables.ACCOUNT_MST').' as um', function($join) {
							$join->on('um.account_id', ' = ', 'utr.account_id');
						})					
						->where('utr.lft_node', '>', $parent_details->lft_node)
						->where('utr.rgt_node', '<', $parent_details->rgt_node)
                        ->where('utr.nwroot_id', '=', $parent_details->nwroot_id)
						->select('utr.account_id');
					
					$qry = DB::table(DB::raw('('.$subqry->tosql().') as ut'))
                        ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usl', 'usl.account_id', ' = ', 'ut.account_id')
						->addBinding($subqry->getBindings(), 'join')           
						->whereMonth('confirm_date','=',getGTZ('m'))
						->whereYear('confirm_date','=',getGTZ('Y'))										
						->where('usl.status', '=', 1)
						->where('usl.payment_status', '=', 1)				
						->select(DB::Raw('SUM(usl.package_qv) as sales'));
					$res =  $qry->first();							
				break;			
			}
		}

		return 	!empty($res->sales)? $res->sales:0;
	}
	
	
	public function credit_bonus($arr){
		extract($arr);
							
			$usrbal_upres = $this->walletObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$wallet, 'account_id'=>$account_id, 'currency_id'=>$currency_id, 'amount'=>$netpay, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));					
			
			$transaction_id = AppService::getTransID($account_id);
			
			$trans = [];
			$trans['account_id'] = $account_id;
			$trans['from_account_id'] = $account_id;
			$trans['statementline_id'] = $statementline_id; /* referral bonus credit */
			$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
			$trans['relation_id'] = $bid;
			$trans['amt'] = $amount;
			$trans['tax'] = $tax;
			$trans['handle_amt'] = $ngoAmt;
			$trans['paid_amt'] = $netpay;
			$trans['currency_id'] = $currency_id;
			$trans['wallet_id'] = $wallet;
			$trans['transaction_id'] = $transaction_id;
			$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
			$trans['remark'] = json_encode(['data'=>$remark]);
			$trans['created_on'] = getGTZ();
			$trans['current_balance'] = $usrbal_upres->current_balance;
			$trans['status'] = $this->config->get('constants.ACTIVE');
			$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
					->insertGetId($trans);
			
			if($transResID>0 && $ngoAmt>0)
			{						
				$usrbal_upres2 = $this->walletObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIH'), 'account_id'=>$account_id, 'currency_id'=>$currency_id, 'amount'=>$ngoAmt, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
			}
			return $transResID>0? $transResID : false;
	}
	

}