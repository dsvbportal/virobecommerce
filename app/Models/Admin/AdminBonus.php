<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Admin\AffModel;
use App\Models\Admin\AdminFinance;
use DB;
use CommonLib;
use Log;

class AdminBonus extends BaseModel
{
    public function __construct ()
    {
        parent::__construct();
		$this->affObj = new AffModel;
		$this->financeObj = new AdminFinance;
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
	/*	$exists = DB::table($this->config->get('tables.REFERRAL_EARNINGS'))                   
					->where($wd)
                    ->exists();*/
$exists=false;
		if(!$exists && !empty($purchaseInfo) && $purchaseInfo->package_qv > 0) 
		{
			/* 67 - fast start bonus taxes */			
			$spInfo = $this->affObj->getSponsorInfo(['account_id'=>$purchaseInfo->account_id]);
		
			$cvConvertData = $this->commonstObj->getSettings('qv_currency_rate',true);
			$bonusInfo = $this->getBonusSetting($this->config->get('constants.BONUS_TYPE.FAST_START_BONUS'));
			$rate = isset($cvConvertData[$purchaseInfo->currency_id])? $cvConvertData[$purchaseInfo->currency_id]: 1;			
			
			$transaction_id = \AppService::getTransID($purchaseInfo->account_id);
			$earnings_qv = number_format(($purchaseInfo->package_qv * $bonusInfo->perc/100),2,'.','');
			$bonusAmt = number_format(($earnings_qv * $rate),2,'.','');
			
			if($bonusAmt>0){
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
						$usrbal_upres = $this->financeObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$bonusInfo->credit_wallet_id, 'account_id'=>$spInfo->account_id, 'currency_id'=>$purchaseInfo->currency_id, 'amount'=>$netpay, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));					
						
						$transaction_id = \AppService::getTransID($spInfo->account_id);
						
						$trans = [];
						$trans['account_id'] = $spInfo->account_id;
						$trans['from_account_id'] = $purchaseInfo->account_id;
						$trans['statementline_id'] = $this->config->get('stline.FAST_START_BONUS.CREDIT'); /* referral bonus credit */
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
							$usrbal_upres2 = $this->financeObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIH'), 'account_id'=>$spInfo->account_id, 'currency_id'=>$purchaseInfo->currency_id, 'amount'=>$ngoAmt, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
						}
					}
				} 
				return $refid;
			}			
		}
		return false;
	}	


}