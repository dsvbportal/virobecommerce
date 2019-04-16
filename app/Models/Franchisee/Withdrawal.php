<?php

namespace App\Models\Franchisee;

use App\Models\BaseModel;
use App\Models\Franchisee\WalletModel;
//use App\Models\Franchisee\Payments;
use DB;
use CommonLib;
use AppService;

class Withdrawal extends BaseModel
{

    public function __construct ($commonObj)
    {
        parent::__construct();
        //$this->paymentObj = new Payments;
        $this->walletObj = new WalletModel;
        $this->commonObj = $commonObj;
    }
	
	/*  withdrawal_payout_list */
	public function withdrawal_payout_list ($arr = array())
    { 
        $res = DB::table($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPE').' as wpt')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wpt.payment_type_id')
                ->where('wpt.status', $this->config->get('constants.ON'))				
				/* ->whereRaw('FIND_IN_SET(?,wpt.account_type)', [$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')])     */   
				->orderBy('pt.payment_type_id','DESC')
                ->select('pt.payment_type', 'wpt.charges', 'wpt.description', 'wpt.payment_type_id', 'pt.image_name', 'pt.payment_key');
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            $withdrawal = $res->get();          
            array_walk($withdrawal, function(&$withdrawal)
            {
                $withdrawal->image_name = asset($this->config->get('constants.PAYOUT_IMAGE_PATH').$withdrawal->image_name);
            });          
            return $withdrawal;
        }
    }
	/*Withdrawal */
	  public function payoutTypeDetails ($payout_type_key)
      {
        $paymentType = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->join($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPE').' as wpt', function($join){
					$join->on('wpt.payment_type_id', '=', 'pt.payment_type_id')
					->where('wpt.status','=',$this->config->get('constants.ON'));
				})
                ->where('pt.payment_key', $payout_type_key)
				->where('pt.status', $this->config->get('constants.ON'))
                ->select('pt.payment_key', 'pt.payment_type', 'wpt.description', 'wpt.charges', 'wpt.is_country_based', 'wpt.is_user_country_based', 'wpt.countries_allowed', 'wpt.currency_allowed', 'wpt.countries_not_allowed', 'pt.payment_type_id')
                ->first();
				
        if ($paymentType)
		{			
            if ($paymentType->is_country_based == $this->config->get('constants.ON') || $paymentType->is_user_country_based == $this->config->get('constants.ON'))
            {
                $paymentType->countries_allowed = !empty($paymentType->countries_allowed) ? json_decode($paymentType->countries_allowed, true) : [];

                $paymentType->countries_not_allowed = !empty($paymentType->countries_not_allowed) ? json_decode($paymentType->countries_not_allowed, true) : [];
                if (!empty($paymentType->countries_not_allowed))
                {
                    if (!empty($paymentType->countries_allowed))
                    {
                        array_walk($paymentType->countries_not_allowed, function ($countries_not_allowed, $currency_id) use(&$paymentType)
                        {
                            if (isset($paymentType->countries_allowed[$currency_id]))
                            {
                                $paymentType->countries_allowed[$currency_id] = array_diff($paymentType->countries_allowed[$currency_id], $countries_not_allowed);
                            }
                        });
                    }
                    else
                    {
                        $all_countries = DB::table($this->config->get('tables.LOCATION_COUNTRY'))->where('status', $this->config->get('constants.ON'))->remember(30)->lists('country_id');
                        array_walk($paymentType->countries_not_allowed, function ($countries_not_allowed, $currency_id) use(&$paymentType, $all_countries)
                        {
                            $paymentType->countries_allowed[$currency_id] = array_diff($all_countries, $countries_not_allowed);
                        });
                    }
                }
                array_walk($paymentType->countries_allowed, function($cou) use(&$paymentType)
                {
                    if (!isset($paymentType->all_allowed_countries) || empty($paymentType->all_allowed_countries))
                    {
                        $paymentType->all_allowed_countries = [];
                    }
                    $paymentType->all_allowed_countries = array_merge($paymentType->all_allowed_countries, (array) $cou);
                });
                unset($paymentType->countries_not_allowed);
            }
		    $srdata['currencies'] = !empty($paymentType->currency_allowed) ? json_decode($paymentType->currency_allowed, true) : [];
		   
             $allowed_currencies = $this->get_currencies($srdata);
				$currency_allow = [];
				if(!empty($allowed_currencies)){
					foreach($allowed_currencies as $currencies){
						$currency_allow[$currencies->currency_id] = $currencies;
					}
				}
				$paymentType->currency_allowed = $currency_allow;
        }
        return $paymentType;
    }
	
		public function get_currencies($arr=array()) {
			extract($arr);
			$qry = DB::table($this->config->get('tables.CURRENCIES'))                        
							->where('status','=',$this->config->get('constants.ACTIVE'))						
							->select('currency_id','currency_symbol','currency as currency_code');	
			if(isset($currencies) && is_array($currencies)){
				$qry->whereIn('currency_id',$currencies);
			}
			else if(isset($currency_id) && is_array($currency_id)){
				$qry->whereIn('currency_id',$currency_id);
			}						
			$res = $qry->get();		
			if (!empty($res) && count($res) > 0) {
				return $res;
			} 
			return false;
		}
		
	public function withdrawal_permission($paymentTypeInfo='',$country_id=0,$currency_id=0)
	 {
		if ($paymentTypeInfo)
		{
			$allowed_curr =  $data['allowed_currency'] = '';	
			$paymentTypeInfo->is_country_based = 0;			
			if ($paymentTypeInfo->is_country_based == $this->config->get('constants.ON'))
			{
				$countries_allowed = $paymentTypeInfo->countries_allowed;					
				if (isset($countries_allowed[$currency_id]) &&  in_array($country_id,$countries_allowed[$currency_id]))
				{
					$allowed_curr[] = $country_id;
				}				
			}
			else
			{				
				$allowed_curr = $paymentTypeInfo->currency_allowed;
			}	
			
			if(!empty($allowed_curr) && array_key_exists($currency_id,$allowed_curr)){			
				$allowed_curr = [$currency_id];
				$data['allowed_currency'] = $this->get_currencies(['currencies'=>$allowed_curr]);
			}			
			return !empty($data['allowed_currency'])? $data['allowed_currency'] : false;
		}
	}	
	  public function get_withdrwal_settings ($arr = array())
    {
        extract($arr);
        $query = DB::table($this->config->get('tables.WITHDRAWAL_SETTINGS').' as wd')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'wd.currency_id')
                ->select('wd.country_id', 'wd.min_amount', 'wd.max_amount', 'wd.charges', 'wd.is_range', 'c.currency_id as currency_id', 'c.currency', 'c.currency_symbol', 'c.decimal_places', 'c.currency as currency_code');
        if (isset($payment_type_id) && !empty($payment_type_id))
        {
            $query->where('wd.payment_type_id', $payment_type_id);
        }
        if (isset($country_id) && isset($country_id))
        {
            $query->where('wd.country_id', $country_id);
        }
        if (isset($currency_id) && isset($currency_id))
        {
            if (is_array($currency_id))
            {
                $query->whereIn('wd.currency_id', $currency_id);
            }
            else
            {
                $query->where('wd.currency_id', $currency_id);
            }
        }
        $settings = $query->first();
        if (!empty($settings))
        {            
			$charges = json_decode(unserialize(stripslashes($settings->charges)));
            if ($settings->is_range)
            {
                $settings->charge = [
                    (object) [
                        'min'=>0,
                        'charge'=>(float) $charges->default->charge,
                        'charge_type'=>$charges->default->charge_type
                    ],
                    (object) [
                        'min'=>(float) $charges->range->min_amnt,
                        'charge'=>(float) $charges->range->charge,
                        'charge_type'=>$charges->range->charge_type
                    ]
                ];
            }
            else
            {				
                $settings->charge = (object) [
                            'charge'=>(float) $charges->default->charge,
                            'charge_type'=>$charges->default->charge_type
                ];
            }
            unset($settings->charges);
            unset($settings->is_range);
        }

        return $settings;
    }
 
	public function account_payout($arr)
    {
		extract($arr);
		 $res = DB::table($this->config->get('tables.AFF_ACCOUNT_PAYOUT_SETTINGS').' as po')
					->where('account_id',$account_id)
					->where('is_deleted',$this->config->get('constants.OFF'))
					->where('status',$this->config->get('constants.ON'));
			if(isset($payment_id) && !empty($payment_id)){
				$res->where('id',$payment_id);
			}		
			$res->select('id as payment_id','nick_name','payment_settings');
			$result= $res->first();
		if(!empty($result)){
			$result->settings = json_decode($result->payment_settings,true);
		}
		return $result;		 
	}
	
/* saveWithdrawal */
	public function saveWithdrawal (array $arr = array())
    {	
        extract($arr);
        $wallet_id 				= $this->config->get('constants.WALLETS.VI');
        $avaliable_balance  	= 0;
        $conversion_details 	= [];		
		$payout_info = '';
        DB::beginTransaction();
        //$transaction_id = $this->generateTransactionID();
        $balance_details = $this->commonObj->get_user_balance($account_id, $wallet_id, $currency_id);

        if ($balance_details && !empty($balance_details) && $balance_details->current_balance >= $amount)
        {

			if($payment_type_id!=$this->config->get('constants.PAYMENT_TYPES.VI_MONEY')){
				$payout_info = $this->account_payout(['account_id'=>$account_id,'payment_type_id'=>$payment_type_id]);				
			}	

			$charge = $charge_amt = 0;
            $settings	    			= $this->get_withdrwal_settings(['country_id'=>$country_id,'currency_id'=>$currency_id,'payment_type_id'=>$payment_type_id]);	
			if(!empty($settings)){
				//print_R($settings->max_amount);exit;
				if(($amount >= $settings->min_amount) && ($amount <= $settings->max_amount)){
					$charge_setting  = $settings->charge;
					$total_debit 	 = $balance_details->tot_debit + $amount;
					$current_balance = $balance_details->current_balance - $amount;					
					
					if($charge_setting->charge_type == 0){
						$charge 	 = $charge_setting->charge;
						$charge_amt  = $amount*$charge_setting->charge/100;
					}
					$paid_amt 		 = $amount - $charge_amt;
					$created_on 	 = $expected_date = getGTZ();
					$d = getGTZ($expected_date, 'd');
					if ($d <= 15)
					{
						$dd = cal_days_in_month(CAL_GREGORIAN, getGTZ($expected_date, 'm'), getGTZ($expected_date, 'Y'));
						$ds = $dd - ($d % $dd);
						$expected_on = getGTZ(date('Y-m-d', strtotime($ds.' days', strtotime($expected_date))), 'Y-m-d');
					}
					else
					{
						$ds = $d - 15;
						$expected_on = getGTZ(date('Y-m-d', strtotime('-'.$ds.' days', strtotime('1 month ', strtotime($expected_date)))), 'Y-m-d');
					}
			
					$transaction_id = AppService::getTransID($account_id);					
					$insert_withdrawal['account_info']  = !empty($payout_info)? $payout_info->payment_settings : '';
					$insert_withdrawal['amount'] 		= $amount;
					$insert_withdrawal['wallet_id'] 	= $wallet_id;
					$insert_withdrawal['currency_id'] 	= $currency_id;
					$insert_withdrawal['account_id'] 	= $account_id;            
					$insert_withdrawal['payment_type_id'] = $payment_type_id;
					$insert_withdrawal['paidamt'] 		= $paid_amt;
					$insert_withdrawal['handleamt'] 	= $charge_amt;
					$insert_withdrawal['handle_perc'] 	= $charge;
					$insert_withdrawal['transaction_id'] = $transaction_id;					
					$insert_withdrawal['created_on'] = $created_on;
					$insert_withdrawal['updated_by'] = $account_id;		
					if($payment_type_id == config('constants.PAYMENT_TYPES.VI_MONEY')){	
						$insert_withdrawal['status_id'] = $this->config->get('constants.WITHDRAWAL_STATUS.CONFIRMED');
						$insert_withdrawal['expected_on'] = $expected_on;
					} else {
						$insert_withdrawal['status_id'] = $this->config->get('constants.WITHDRAWAL_STATUS.PENDING');
					}
					$wd_id = DB::table($this->config->get('tables.WITHDRAWAL_MST'))
							->insertGetId($insert_withdrawal);
					
					//withdrawal transaction
					$withdrawamt = $amount - $charge_amt;
					$trans = [
						'from_account_id'=>$account_id,
						'from_wallet_id'=>$wallet_id,
						'currency_id'=>$currency_id,
						'amt'=>$amount,
						'paidamt'=>$amount,
						'from_transaction_id'=>$transaction_id,
						'relation_id'=>$wd_id,
						'payment_type_id'=>$payment_type_id,
						'statementline_id'=>config('stline.AFF_WITHDRAWAL_DEBIT'),
						'transaction_for'=>'WITHDRAW',
						'tds'=>0,
						'debit_remark_data'=>['payment_type_id'=>$payment_type_id,'transaction_id'=>$transaction_id],
						'credit_remark_data'=>['payment_type_id'=>$payment_type_id,'transaction_id'=>$transaction_id]
					];
					if ($charge_amt > 0)
					{
						$trans['from_handle'] = [['amt'=>$charge_amt, 
						 'transaction_for'=>'WITHDRAWAL_CHARGES', 
						 'debit_remark_data'=>['payment_type_id'=>$payment_type_id,'transaction_id'=>$transaction_id,'amount'=>CommonLib::currency_format($amount, $currency_id)],
						 'credit_remark_data'=>['payment_type_id'=>$payment_type_id,'transaction_id'=>$transaction_id,'amount'=>CommonLib::currency_format($amount, $currency_id)]]];
					}
					if ($wd_id && $trans_id = $this->updateAccountTransaction($trans))
					{
						DB::commit();				
						if ($payment_type_id == $this->config->get('constants.PAYMENT_TYPES.BANK_TRANSFER'))
						{
							$paytype = $account_details['b_accno'];
						}
						else
						{					
							$paytype = trans('general.pay_types.'.$payment_type_id);
						}
						return $transaction_id;
					}
				}else{
					return 2;
				}					
			}				
        }else{
			return 3;
		}		
        DB::rollback();
        return false;
    }
		
	/* existing_withdrawal_count */
	public function existing_withdrawal_count ($account_id = '')
    {   
        $withdrawal_count = 0;
        $current_date = getGTZ('Y-m-d');
        if (!empty($account_id))
        {
            $withdrawal_details = DB::table($this->config->get('tables.WITHDRAWAL_MST'))
                    ->whereIn('status_id', array($this->config->get('constants.WITHDRAWAL_STATUS.PENDING'),$this->config->get('constants.WITHDRAWAL_STATUS.PROCESSING')))
                    ->where('account_id', $account_id)
                    ->whereRaw("DATE(expected_on) >='".$current_date."'")
                    ->count();
            if (!empty($withdrawal_details))
                $withdrawal_count = $withdrawal_details;
        }		
        return $withdrawal_count;
    }
	 public function withdrawals_history($arr = array(), $count = false){
	       extract($arr);
	      $users = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as a')
	                       ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'a.account_id')
						   ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
						   ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
						   ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
						   ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'a.currency_id')
						   ->leftjoin($this->config->get('tables.PAYMENT_TYPES').' as pay_t', 'pay_t.payment_type_id', '=', 'a.payment_type_id')
						   ->where('a.is_deleted','=',$this->config->get('constants.OFF'))
						   ->where("a.status_id", $status)
						   ->where('um.account_id','=',$account_id)
						  ->select(DB::raw('a.*,um.uname,um.email,c.currency as code,concat_ws(" ",ud.firstname,ud.lastname) as fullname,lc.country as country,um.mobile,pay_t.payment_type,c.currency_symbol,c.decimal_places'));
         if (isset($from) && !empty($from) && isset($to) && !empty($to))
            {
                $users->whereRaw("DATE(a.created_on) >='".date('Y-m-d', strtotime($from))."'");
                $users->whereRaw("DATE(a.created_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (isset($from) && !empty($from))
            {
                $users->whereRaw("DATE(a.created_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $users->whereRaw("DATE(a.created_on) >='".date('Y-m-d', strtotime($to))."'");
            }
	       if (isset($payout_type) && !empty($payout_type))
            {
                $users->where("a.payment_type_id", $payout_type);
            }			   
			if (isset($currency) && !empty($currency))
            {
                $users->where("a.currency_id", $currency);
            }		
			
            if (isset($uname) && !empty($uname))
            {
               $users->Where("um.uname",'like',$uname);
            }	
            if (isset($orderby) && isset($order)) {
                $users->orderBy($orderby, $order);
            }
            else {
                $users->orderBy('a.wd_id', 'DESC');
            }
            if (isset($length) && !empty($length)) {
                $users->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
                return $users->count();
            }
           $withdrawals_details = $users->get();
		 if(!empty($withdrawals_details)){
		     array_walk($withdrawals_details, function(&$t)	{
					$t->created_on = showUTZ($t->created_on, 'd-M-Y H:i:s');
					$t->amount = \CommonLib::currency_format($t->amount, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);	
					$t->handleamt =  CommonLib::currency_format($t->handleamt, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->code, 'decimal_places'=>$t->decimal_places]);	
					$t->paidamt =  CommonLib::currency_format($t->paidamt, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->code, 'decimal_places'=>$t->decimal_places]);	
					$t->expected_on = showUTZ($t->expected_on, 'd-M-Y');
					$t->status_class   = $this->config->get('dispclass.withdrawal_status.'.$t->status_id.'');
					$t->status=ucfirst(strtolower($this->config->get('constants.WITHDRAWAL_STATUS.'.$t->status_id.'')));
				 	$t->actions 	   = [];
			        $t->actions[] 	   = ['class'=>'details','label'=>'Details','url'=>route('fr.withdrawal.details', ['trans_id'=>$t->transaction_id])]; 
				});
				return !empty($withdrawals_details) ? $withdrawals_details : [];			
			}
      }
	 public function getWithdrawalDetails(array $arr = array())
      {
        extract($arr);
        $query = DB::table($this->config->get('tables.ACCOUNT_WITHDRAWAL').' as wid')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wid.payment_type_id')            
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($wt)
                {
                    $wt->on('wt.wallet_id', '=', 'wid.wallet_id');                    
                })
                ->join($this->config->get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'wid.currency_id')
                ->where('wid.account_id', $account_id)
                ->where('wid.is_deleted', $this->config->get('constants.OFF')) 
                ->where('wid.transaction_id', $trans_id);
         $query->selectRaw('pt.payment_type, wid.transaction_id,wid.status_id,wid.status_id as status, wid.amount, wid.paidamt, wid.handleamt, wid.created_on, wid.expected_on, wid.cancelled_on, wid.confirmed_on, ci.currency, ci.currency_symbol, wid.account_info,wid.reason, wid.payment_details, wt.wallet as from_wallet, wid.wd_id');
                 $withdrawal = $query->first();
		//print_r($withdrawal);exit;
        if (!empty($withdrawal))
        {
            $withdrawal->amount 	  = $withdrawal->currency_symbol.' '.number_format($withdrawal->amount, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->paidamt 	  = $withdrawal->currency_symbol.' '.number_format($withdrawal->paidamt, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->handleamt    = $withdrawal->currency_symbol.' '.number_format($withdrawal->handleamt, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->expected_on  = ($withdrawal->expected_on != null) ? showUTZ($withdrawal->expected_on, 'd-M-Y') : '';
            $withdrawal->created_on   = ($withdrawal->created_on != null) ? showUTZ($withdrawal->created_on) : '';
            $withdrawal->confirmed_on = !empty($withdrawal->confirmed_on) ? showUTZ($withdrawal->confirmed_on) : '';
            $withdrawal->cancelled_on = !empty($withdrawal->cancelled_on) ? showUTZ($withdrawal->cancelled_on) : '';
            $withdrawal->reason 	  = ($withdrawal->reason != null) ? json_decode($withdrawal->reason) : '';
            $withdrawal->account_info = json_decode($withdrawal->account_info);
            $withdrawal->title 		  = trans('general.withdraw_title',['payment_type'=>$withdrawal->payment_type,'trans_id'=>$withdrawal->transaction_id]);
            $withdrawal->payment_details = ($withdrawal->payment_details) ? json_decode($withdrawal->payment_details) : '';

            if (!empty($withdrawal->account_info))
            {
                array_walk($withdrawal->account_info, function(&$a, $k)
                {					
					$a = ['label'=>trans('franchisee/withdrawal/withdrawal.account_details.'.$k), 'value'=>$a];					
                });
            }
            if (!empty($withdrawal->payment_details))
            {
                array_walk($withdrawal->payment_details, function(&$a, $k)
                {
                    $a = ['label'=>trans('franchisee/withdrawal.payment_details.'.$k), 'value'=>$a];
                });
            }
            $withdrawal->account_info = array_values((array) $withdrawal->account_info);
            $withdrawal->status_class = $this->config->get('dispclass.withdrawal_status.'.$withdrawal->status);
            $withdrawal->status = trans('general.withdrawal_status.'.$withdrawal->status);
        }
        return $withdrawal;
    }
	public function cancel_withdrawal($arr){
		extract($arr);
		$res = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wid')
				   ->whereIn('payment_status',[config('constants.WITHDRAWAL_STATUS.PENDING')])
				   ->whereIn('status_id',[config('constants.WITHDRAWAL_STATUS.PENDING')])
				   ->where('transaction_id',$trans_id)
				   ->where('account_id',$account_id)
				   ->select('wd_id','account_id','payment_type_id','currency_id','wallet_id','amount')
				   ->first();
	
		if(!empty($res)){
			$status = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wid')
			  ->whereIn('payment_status',[config('constants.WITHDRAWAL_STATUS.PENDING'),config('constants.WITHDRAWAL_STATUS.PROCESSING')])
			  ->whereIn('status_id',[config('constants.WITHDRAWAL_STATUS.PENDING'),config('constants.WITHDRAWAL_STATUS.PROCESSING')])
			  ->where('wd_id',$res->wd_id)
			  ->update(['status_id'=>config('constants.WITHDRAWAL_STATUS.CANCEL'),'payment_status'=>config('constants.WITHDRAWAL_STATUS.CANCEL')]);
			if($status)	{
				$credit_trans = [
					'from_account_id'=>$res->account_id,
					'to_account_id'=>$res->account_id,
					'to_wallet_id'=>$this->config->get('constants.WALLETS.VI'),
					'currency_id'=>$res->currency_id,
					'amt'=>$res->amount,
					'paidamt'=>$res->amount,
					'from_transaction_id'=>AppService::getTransID($res->account_id),
					'relation_id'=>$res->wd_id,
					'payment_type_id'=>$res->payment_type_id,
					'statementline_id'=>config('stline.WITHDRAWAL_REFUND'),
					'transaction_for'=>'WITHDRAW_CANCEL',
					'tds'=>0,
					'debit_remark_data'=>['amount'=>CommonLib::currency_format($res->amount,$res->currency_id)],
					'credit_remark_data'=>['amount'=>CommonLib::currency_format($res->amount,$res->currency_id)]
				];
				$result = $this->updateAccountTransaction($credit_trans,false,true);
				return true;
			}  
		}else{
			return false;
		}  
   }
	
}
