<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\Payments;
use DB;
use CommonLib;
use AppService;

class Withdrawal extends BaseModel
{

    public function __construct ($commonObj)
    {
        parent::__construct();
        $this->paymentObj = new Payments;
        $this->walletObj = new Wallet;
        $this->commonObj = $commonObj;
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
           // $withdrawal->conversion_details = ($withdrawal->conversion_details != null) ? json_decode($withdrawal->conversion_details) : '';
            $withdrawal->reason 	  = ($withdrawal->reason != null) ? json_decode($withdrawal->reason) : '';
            $withdrawal->account_info = json_decode($withdrawal->account_info);
            $withdrawal->title 		  = trans('general.withdraw_title',['payment_type'=>$withdrawal->payment_type,'trans_id'=>$withdrawal->transaction_id]);
            $withdrawal->payment_details = ($withdrawal->payment_details) ? json_decode($withdrawal->payment_details) : '';

            if (!empty($withdrawal->account_info))
            {
                array_walk($withdrawal->account_info, function(&$a, $k)
                {					
					$a = ['label'=>trans('affiliate/withdrawal/withdrawal.account_details.'.$k), 'value'=>$a];					
                });
            }
            if (!empty($withdrawal->payment_details))
            {
                array_walk($withdrawal->payment_details, function(&$a, $k)
                {
                    $a = ['label'=>trans('affil/withdrawal.payment_details.'.$k), 'value'=>$a];
                });
            }
            $withdrawal->account_info = array_values((array) $withdrawal->account_info);
          
           /*  $withdrawal->actions = [];
            if ($withdrawal->status == $this->config->get('constants.WITHDRAWAL_STATUS.PENDING'))
            {
                $withdrawal->actions['CANCEL'] = [
                    'title'=>'Cancel',
                    'data'=>[
                        'transaction_id'=>$withdrawal->transaction_id,
                        'status_id'=>$this->config->get('constants.WITHDRAWAL_STATUS.CANCELLED')
                    ],
                    'url'=>URL::to('supplier/withdraw/update-status')
                ];
            } */
            $withdrawal->status_class = $this->config->get('dispclass.withdrawal_status.'.$withdrawal->status);
            $withdrawal->status = trans('general.withdrawal_status.'.$withdrawal->status);
        }
        return $withdrawal;
    }

	  public function get_payout_types ()
        {
        $result = DB::table($this->config->get('tables.PAYMENT_TYPES'))
                  ->where('status', $this->config->get('constants.ON'))
                  ->select('payment_type','payment_type_id')
				  ->get();
			if(!empty($result)){
					return $result;
	  			}
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
						   ->where('um.is_affiliate','=',1)
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
					/* $t->details = route('aff.withdrawal.details',['trans_id'=>$t->transaction_id]); */
					$t->actions 	   = [];
			        $t->actions[] 	   = ['class'=>'details','label'=>'Details','url'=>route('aff.withdrawal.details', ['trans_id'=>$t->transaction_id])];
				});
				return !empty($withdrawals_details) ? $withdrawals_details : [];			
			}
       }
	   
    public function withdrawal_wallet_balance_list ($arr = array())
    {
        $res = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                ->join($this->config->get('tables.WALLET').' as wa', 'wa.wallet_id', '=', 'ab.wallet_id')
                ->join($this->config->get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'ab.currency_id')
                ->where('wa.withdrawal_status', $this->config->get('constants.ON'))
                ->where('ab.account_id', $arr['account_id'])
                ->selectRaw('ab.current_balance,ci.currency');

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
            $result = $res->get();
            if ($result)
                return $result;
            else
                return false;
        }
    }

    public function withdrawal_payout_list ($arr = array())
    {
        $res = DB::table($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPE').' as wpt')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wpt.payment_type_id')
                ->where('wpt.status', $this->config->get('constants.ON'))
				->orderBy('pt.payment_type_id','desc')
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
            //print_r($withdrawal);exit;
            array_walk($withdrawal, function(&$withdrawal)
            {
                $withdrawal->image_name = asset($this->config->get('constants.PAYOUT_IMAGE_PATH').$withdrawal->image_name);
            });

            return $withdrawal;
        }
    }

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
		    //$srdata['currencies'] = explode(',',$paymentType->currency_allowed);
			//print_R($paymentType->currency_allowed);exit;
             $allowed_currencies = $this->paymentObj->get_currencies($srdata);
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
				$data['allowed_currency'] = $this->paymentObj->get_currencies(['currencies'=>$allowed_curr]);
			}			
			return !empty($data['allowed_currency'])? $data['allowed_currency'] : false;
		}
	}	

    public function get_balance_bycurrency ($arr)
    {
        $op = $breakdowns = array();
        $current_balance = 0;
        if (!empty($arr))
        {
            extract($arr);
            $bws = $this->get_withdrwal_settings($arr);
		    if (!empty($bws))
            {
                $acct_balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ub')
                        ->join($this->config->get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'ub.currency_id')
                        ->join($this->config->get('tables.WALLET').' as w', function($subquery)
                        {
                            $subquery->on('w.wallet_id', '=', 'ub.wallet_id')
                            ->where('w.withdrawal_status', '=', $this->config->get('constants.ON'))
                            ->where('w.status', '=', $this->config->get('constants.ON'));
                        })
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                        })
                        ->where('w.wallet_id', '=', $this->config->get('constants.WALLETS.VIM'))
                        ->where('ub.account_id', $account_id)
                        ->whereRaw('ub.current_balance IS NOT NULL')
                        ->whereRaw('ub.current_balance != 0')
                        ->select('ub.current_balance', 'ub.currency_id', 'cr.currency_symbol', 'cr.currency as currency_code', 'cr.currency', 'w.wallet_id', 'wl.wallet')
                        ->get();
                if ($acct_balance)
                {
                    array_walk($acct_balance, function(&$balance) use(&$current_balance, $currency_id)
                    {
                        $balance->min = 0;
                        $rate = $this->get_currency_exchange_rate($balance->currency_id, $currency_id);
                        //print_r( $rate);exit;
                        $balance->equivalent = $balance->max = (float) $balance->current_balance * (float) $rate; //print_r($balance->equivalent);exit;
                        $current_balance = (float) $current_balance + (float) $balance->equivalent;
                    });
                    $op['balance'] = $current_balance;
                }
                $op['min'] = (float) $bws->min_amount;
                $op['max'] = (float) ($current_balance < $bws->max_amount) ? $current_balance : $bws->max_amount;
                $op['amount'] = $amount = !empty($amount) ? $amount : $op['max'];
                $breakdown_tot = 0;
                if ($acct_balance)
                {
                    array_walk($acct_balance, function(&$balance) use($breakdowns, &$breakdown_tot, $amount)
                    {
                        if (isset($breakdowns[$balance->wallet_id][$balance->currency_id]) && !empty($breakdowns[$balance->wallet_id][$balance->currency_id]))
                        {
                            $balance->breakdown = $breakdowns[$balance->wallet_id][$balance->currency_id];
                            $breakdown_tot+=$balance->breakdown;
                        }
                        else
                        {
                            $breakdown_bal = $amount - $breakdown_tot;
                            $balance->breakdown = ($breakdown_bal > 0) ? ($breakdown_bal > $balance->equivalent ? $balance->equivalent : $breakdown_bal) : 0;
                            $breakdown_tot+=$balance->breakdown;
                        }
                    });
                    $op['breakdowns'] = $acct_balance;
                }
                if (is_array($bws->charge))
                {
                    if ($bws->charge[1]->min <= $amount)
                    {
                        $op['charge'] = ($bws->charge[1]->charge_type == $this->config->get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge[1]->charge) / 100 : $bws->charge[1]->charge;
                        $op['charge_type'] = $bws->charge[1]->charge_type;
                        $op['charges'] = $bws->charge[1]->charge;
                    }
                    else
                    {
                        $op['charge'] = ($bws->charge[0]->charge_type == $this->config->get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge[0]->charge) / 100 : $bws->charge[0]->charge;
                        $op['charge_type'] = $bws->charge[0]->charge_type;
                        $op['charges'] = $bws->charge[0]->charge;
                    }
                }
                else
                {
                    $op['charge'] = ($bws->charge->charge_type == $this->config->get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge->charge) / 100 : $bws->charge->charge;
                    $op['charge_type'] = $bws->charge->charge_type;
                    $op['charges'] = $bws->charge->charge;
                }
                $op['currency_code'] = $bws->currency_code;
                $op['currency_symbol'] = $bws->currency_symbol;
                return $op; //print_r($op);exit;
            }
        }
        return false;
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

    public function get_currency_exchange_rate ($from_currency_id, $to_currency_id)
    { //print_r($to_currency_id);exit;
        if ($from_currency_id != $to_currency_id)
        {
            return DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                            ->select('rate')
                            ->where(array(
                                'from_currency_id'=>$from_currency_id,
                                'to_currency_id'=>$to_currency_id
                            ))
                            ->pluck('rate');
        }
        return 1;
    }

    public function get_preBank_info ($arrData)
    {
        extract($arrData);
        $result = DB::table($this->config->get('tables.WITHDRAWAL_MST'))
                ->where('account_id', $account_id)
                ->where('payment_type_id', $payment_type_id)
                ->where('currency_id', $currency_id)
                ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->orderBy('withdrawal_id', 'DESC')
                ->select('account_info')
                ->first();
        if (!empty($result))
        {
            return json_decode(stripslashes($result->account_info));
        }
        return false;
    }
	
	/* withdrawal with currency conversation */
    public function saveWithdrawal_old (array $arr = array())
    {
        extract($arr);
        $wallet_id = $this->config->get('constants.WALLET.PERSONAL');
        $tds_charge = 0;
        $avaliable_balance = 0;
        $from_currency = [];
        $conversion_details = [];
        DB::beginTransaction();
       // $transaction_id = $this->commonObj->generateTransactionID($account_id);
		$transaction_id = AppService::getTransID($account_id);
        $to_currency_code = $this->commonObj->get_currency_code($currency_id);
        foreach ($breakdowns as $from_wallet_id=> $currencies)
        {
            foreach ($currencies as $from_currency_id=> $break_amount)
            {
                if ($from_currency_id != $currency_id)
                {
                    if (!empty($break_amount) && $break_amount > 0)
                    {
                        $balance_bycurrency = $this->commonObj->get_user_balance($account_id, $from_wallet_id, $from_currency_id);
                        if ($balance_bycurrency)
                        {
                            $rate = $this->get_currency_exchange_rate($from_currency_id, $currency_id);
                            $withdraw_exchange_amt = (float) $break_amount / (float) $rate;
                           // $relation_transaction_id[] = $rel_trans_id =    	$this->commonObj->generateTransactionID($account_id);
							$relation_transaction_id[] = AppService::getTransID($account_id);
							$relation_transaction_id[] =  $rel_trans_id2 =  AppService::getTransID($account_id);
                            //$relation_transaction_id[] = $rel_trans_id2 = $this->commonObj->generateTransactionID($account_id);
							
                            $from_currency_code = $this->commonObj->get_currency_code($from_currency_id);
                            $from_currency_symbol = $this->commonObj->get_currency_symbol($from_currency_id);
                            $this->commonObj->updateAccountTransaction([
                                'from_account_id'=>$account_id,
                                'from_wallet_id'=>$from_wallet_id,
                                'currency_id'=>$from_currency_id,
                                'amt'=>$withdraw_exchange_amt,
                                'from_transaction_id'=>$rel_trans_id,
                                'to_account_id'=>$account_id,
                                'to_wallet_id'=>$wallet_id,
                                'to_currency_id'=>$currency_id,
                                'to_amt'=>$break_amount,
                                'to_transaction_id'=>$rel_trans_id2,
                                'relation_id'=>null,
                                'transaction_for'=>'CURRENCY_CONVERSION',
                                'debit_remark_data'=>['from_amount'=>Commonsettings::currency_format(['amt'=>$withdraw_exchange_amt, 'currency_id'=>$from_currency_id]), 'to_amount'=>Commonsettings::currency_format(['amt'=>$break_amount, 'currency_id'=>$currency_id]), 'rate'=>$rate],
                                'credit_remark_data'=>['from_amount'=>Commonsettings::currency_format(['amt'=>$withdraw_exchange_amt, 'currency_id'=>$from_currency_id]), 'to_amount'=>Commonsettings::currency_format(['amt'=>$break_amount, 'currency_id'=>$currency_id]), 'rate'=>$rate]
                            ]);
                            $conversion_details[] = [
                                'wallet_id'=>$from_wallet_id,
                                'currency_id'=>$from_currency_id,
                                'rate'=>$rate,
                                'from_amount'=>$withdraw_exchange_amt,
                                'to_amount'=>$break_amount,
                                'debit_transaction_id'=>$rel_trans_id,
                                'credit_transaction_id'=>$rel_trans_id2
                            ];
                        }
                    }
                }
            }
        }
        $balance_details = $this->commonObj->get_user_balance($account_id, $wallet_id, $currency_id);
        if ($balance_details && !empty($balance_details) && $balance_details->current_balance >= $amount)
        {
            $withdrawal_rel_trans_id = '';
            if (isset($relation_transaction_id))
            {
                $withdrawal_rel_trans_id = implode(',', $relation_transaction_id);
            }
            $withdrawal_tds = json_decode(unserialize(stripslashes($this->commonObj->getSettings('withdrawal_tds'))), true);
            if (isset($withdrawal_tds[$currency_id]))
            {
                $tds_charge = $withdrawal_tds[$currency_id];
            }
            $total_debit = $balance_details->tot_debit + $amount;
            $current_balance = $balance_details->current_balance - $amount;
            $tds = $amount * $tds_charge / 100;
            $paid_amt = $amount - $charge + $tds;
            $creeated_on = $expected_date = date('Y-m-d H:i:s');
            $d = date('d', strtotime($expected_date));
            if ($d <= 15)
            {
                $dd = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($expected_date)), date('Y', strtotime($expected_date)));
                $ds = $dd - ($d % $dd);
                $expected_on = date('d-M-Y', strtotime($ds.' days', strtotime($expected_date)));
            }
            else
            {
                $ds = $d - 15;
                $expected_on = date('d-M-Y', strtotime('-'.$ds.' days', strtotime('1 month ', strtotime($expected_date))));
            }
            $insert_withdrawal['account_info'] = json_encode(array_filter($account_details));
            $insert_withdrawal['amount'] = $amount;
            $insert_withdrawal['wallet_id'] = $wallet_id;
            $insert_withdrawal['currency_id'] = $currency_id;
            $insert_withdrawal['account_id'] = $account_id;
            $insert_withdrawal['payment_type_id'] = $payment_type_id;
            $insert_withdrawal['paidamt'] = $paid_amt;
            $insert_withdrawal['handleamt'] = $charge;
            $insert_withdrawal['handle_perc'] = $charges;
            $insert_withdrawal['transaction_id'] = $transaction_id;
            $insert_withdrawal['conversion_details'] = !empty($conversion_details) ? json_encode($conversion_details) : null;
            $insert_withdrawal['relation_transaction_id'] = $withdrawal_rel_trans_id;
            $insert_withdrawal['status_id'] = $this->config->get('constants.PENDING');
            $insert_withdrawal['created_on'] = $creeated_on;
            $insert_withdrawal['expected_on'] = $expected_on;
            $insert_withdrawal['updated_by'] = $account_id;
            $withdrawal_id = DB::table($this->config->get('tables.WITHDRAWAL_MST'))
                    ->insertGetId($insert_withdrawal);
            //withdrawal transaction
            $trans = [
                'from_account_id'=>$account_id,
                'from_wallet_id'=>$wallet_id,
                'currency_id'=>$currency_id,
                'amt'=>$amount,
                'from_transaction_id'=>$transaction_id,
                'relation_id'=>$withdrawal_id,
                'transaction_for'=>'WITHDRAW',
                'tds'=>$tds,
                'debit_remark_data'=>['amount'=>CommonLib::currency_format(['amt'=>$amount, 'currency_id'=>$currency_id])],
                'credit_remark_data'=>['amount'=>CommonLib::currency_format(['amt'=>$amount, 'currency_id'=>$currency_id])]
            ];
            if ($charge > 0)
            {
                $trans['handle'] = [['amt'=>$charge, 'transaction_for'=>'WITHDRAWAL_CHARGES']];
            }
            $insert_trans_status = $this->commonObj->updateAccountTransaction($trans);
            if ($withdrawal_id && $insert_trans_status)
            {
                //update BTC balance debit;
                if ($payment_type_id == $this->config->get('constants.BITCOIN_WITHDRAWAL'))
                {
                    $this->update_admin_btc_balance($amount, $currency_id, $this->config->get('constants.TRANSACTION_TYPE.DEBIT'));
                }
                DB::commit();
                return true;
            }
        }
        DB::rollback();
        return false;
    }
	
	public function getPaymentDetails (array $arr = array())
    {
        extract($arr);
        $op = [];		
        $res = DB::table($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPE').' as wp')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wp.payment_type_id')               
                ->join($this->config->get('tables.WITHDRAWAL_CHARGES_SETTINGS').' as wc', function($wc) use($currency_id)
                {
                    $wc->on('wc.payment_type_id', '=', 'pt.payment_type_id')
                    ->where('wc.currency_id', '=', $currency_id);
                })
                ->where(function($c)use($country_id)
                {
                    $c->where('wp.is_user_country_based', $this->config->get('constants.OFF'))
                    ->orWhere(function($c1)use($country_id)
                    {
                        $c1->where('wp.is_user_country_based', $this->config->get('constants.ON'))
                        ->where('wc.country_id', $country_id);
                    });
                })
                ->groupby('wp.payment_type_id')
                ->where('pt.payment_key', $payment_type)
                ->where('pt.status', $this->config->get('constants.ON'))
                ->selectRaw('pt.payment_type_id, pt.payment_key as payment_code, pt.payment_type as title, pt.payment_key as id, wp.description as descr, pt.image_name as icon');
        $payment_info = $res->first();
        if (!empty($payment_info))
        {
            $payment_info->icon = asset($this->config->get('constants.PAYMENT_MODE_IMG_PATH.WEB').$payment_info->icon);
            unset($payment_info->payment_type_id);
        }
        return $payment_info;
    }
	
	public function account_payout($arr)
    {
		extract($arr);
		 $res = DB::table($this->config->get('tables.AFF_ACCOUNT_PAYOUT_SETTINGS').' as po')
					->where('account_id',$account_id)
					->where('is_deleted',0)
					->where('status',1);
			if(isset($payment_id) && !empty($payment_id)){
				$res->where('id',$payment_id);
			}
			if(isset($payment_type_id) && !empty($payment_type_id)){
				$res->where('payment_type_id',$payment_type_id);
			}			
			$res->select('id as payment_id','nick_name','payment_type_id','payment_settings');
			$result= $res->first();
		if(!empty($result)){
			$result->settings = json_decode($result->payment_settings,true);
		}
		return $result;		 
	}	
	
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

}
