<?php
namespace App\Models\Franchisee;
use CommonLib;
use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;

class WalletModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();				
		$this->commonObj = new CommonModel;
    }
	
	public function my_wallets ($arr = array())
    {  
        if (!empty($arr))
        {
            extract($arr);			
			
            $qry = DB::table($this->config->get('tables.WALLET').' as w')
                    ->leftJoin($this->config->get('tables.ACCOUNT_BALANCE').' as ub', function($join) use($account_id, $currency_id)
                    {
                        $join->on('ub.wallet_id', '=', 'w.wallet_id');
                        $join->where('ub.account_id', '=', $account_id);
                        $join->where('ub.currency_id', '=', $currency_id);
                    })
                    ->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', DB::RAW('IF(ub.currency_id IS NOT NULL,ub.currency_id,'.$currency_id.')'))
                    ->leftJoin($this->config->get('tables.WALLET_LANG').' as wl', function($join)
					{
						$join->on('wl.wallet_id', '=', 'w.wallet_id');

						$join->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
					});

            if (isset($wallet_id) && $wallet_id > 0)
            {
                $qry->where('w.wallet_id', $wallet_id);
            }
			$qry->where('w.is_franchisee_wallet', $this->config->get('constants.ACTIVE'));
			
            $qry->select(DB::Raw("ub.current_balance,ub.tot_credit,ub.tot_debit,cur.currency_id,w.wallet_id,cur.currency as currency_code,cur.currency_symbol,cur.decimal_places,wl.wallet as wallet_name"));
            $qry->orderby('w.sort_order','asc');
            $result = $qry->get();		
			
            return (isset($curreny_id) || isset($wallet_id)) ? $result[0] : $result;
        }
        return NULL;
    }

    public function get_all_wallet_list ($arr = array())
    {
        $result = DB::table($this->config->get('tables.WALLET').' as w')
                ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                {
                    $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                    ->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('w.is_franchisee_wallet', $this->config->get('constants.ACTIVE'))
                ->where(array('status'=>$this->config->get('constants.ACTIVE')))
                ->get();
        if (!empty($result))
        {			
            return $result;
        }
        return NULL;
    }
	
	/* OLD Currency */
    public function get_currencies_old($arr=array()) 
	{
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
	
	/* New Currency */
	public function get_currencies ($arr = array())
    {
        extract($arr);
        $qry = DB::table($this->config->get('tables.CURRENCIES').' as c')
                ->join($this->config->get('tables.ACCOUNT_BALANCE').' as abal', 'abal.currency_id', '=', 'c.currency_id')
                ->where('abal.account_id', $this->userSess->account_id)
                ->where(array('c.status'=>$this->config->get('constants.ACTIVE')))
                ->select('c.currency as code', 'c.currency_id as id', 'abal.wallet_id', 'current_balance', 'c.currency_id');
        if (isset($currency_id) && !empty($currency_id))
        {
            $query->where('c.currency_id', $currency_id);
        }

        if (isset($wallet_id) && !empty($wallet_id))
        {
            $query->where('wallet_id', $wallet_id);
        }
        $res = $qry->get();
        if (!empty($res) && count($res) > 0)
        {
            return $res;
        }
        return false;
    }
	
   public function transactions ($arr = array())
    {
        extract($arr);
        $finalQry = '';
        $wQry2 = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs');
        if (isset($account_id))
        {
            $wQry2->where('trs.account_id', $account_id);
            if (isset($from) && !empty($from) && isset($to) && !empty($to))
            {
                $wQry2->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($from))."'");
                $wQry2->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (isset($from) && !empty($from))
            {
                $wQry2->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $wQry2->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($to))."'");
            }

            if (isset($wallet_id) && !empty($wallet_id))
            {
                $wQry2->where("trs.wallet_id", $wallet_id);
            }
			
            $wQry2->orderBy('id', 'DESC');
            if (isset($length) && !empty($length)) {
                $wQry2->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
                return $wQry2->count();
            }
            else
            {               
				 $wQry2-> join($this->config->get('tables.WALLET').' as w',function($j){
					 $j->on('w.wallet_id', '=', 'trs.wallet_id')
						->where('w.is_aff_wallet','=',$this->config->get('constants.ACTIVE'));
				 });
                $wQry2->leftJoin($this->config->get('tables.WALLET_LANG').' as b', function($join)
                {
                    $join->on('b.wallet_id', '=', 'w.wallet_id');
                    $join->where('b.lang_id', '=', $this->config->get('app.locale_id'));
                });
                $wQry2->leftJoin($this->config->get('tables.PAYMENT_TYPES').' as c', function($join)
                {
                    $join->on('c.payment_type_id', '=', 'trs.payment_type_id');
                });
                $wQry2->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'trs.currency_id');
				
                $wQry2->select(DB::Raw('trs.id,trs.statementline_id,trs.account_id,trs.created_on,trs.transaction_id,trs.amt as amount,trs.handle_amt,trs.tax,trs.paid_amt,trs.transaction_type,trs.current_balance,trs.remark,trs.wallet_id,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,b.wallet,trs.status'));
                $transactions = $wQry2->get();
                if ($transactions){
					array_walk($transactions, function(&$t)	{
						
						$t->created_on  = showUTZ($t->created_on);
						
						if (!empty($t->remark) && strpos($t->remark, '}') > 0) {
							
							$t->remark = $ordDetails = json_decode(stripslashes($t->remark));	
						
						
							if(isset($t->remark->data) && isset($t->remark->data->fr_type)){
								$t->remark->data->fr_type = trans('general.fr_type.'.$t->remark->data->fr_type);
							}
							if($t->statementline_id==config('stline.AFF_WITHDRAWAL_DEBIT')){
								if(isset($t->remark->data->payment_type_id)){
									$t->payout_type_name = trans('general.withdrawal_payment_types.'.$t->remark->data->payment_type_id);
								}
							}
							$t->statementline = trans('transactions.'.$t->statementline_id.'.franchisee.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark'])));
						
							if(isset($t->remark->data->period)&& !empty($t->remark->data->period)){
								$t->remark->data->period = showUTZ($t->remark->data->period,$t->remark->data->date_format);
							}
							$t->remark = trans('transactions.'.$t->statementline_id.'.franchisee.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
						}
						else {
							$t->remark = $t->statementline;
						}
						
						$t->status_class = trans('general.transactions.status_class.'.$t->status);	
						$t->status = ($t->statementline_id == $this->config->get('stline.FAST_START_BONUS.CREDIT')) ? trans('general.bonus_status.'.$t->status) : trans('general.transactions.status.'.$t->status);
						
						$t->Fpaidamt = \CommonLib::currency_format($t->paid_amt, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);
			
						if($t->transaction_type == 1){
							$t->CR_Fpaidamt = $t->Fpaidamt;
							$t->DR_Fpaidamt = 0;
							$t->transaction_type = 'Credit';
							$t->color = 'green';
						}
						else {
							$t->CR_Fpaidamt = 0;
							$t->DR_Fpaidamt = $t->Fpaidamt;	
							$t->transaction_type = 'Debit';		
							$t->color = 'red';							
						}
						$t->Fcurrent_balance = \CommonLib::currency_format($t->current_balance, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]); 
						unset($t->statementline);
					});
					return !empty($transactions) ? $transactions : [];					
				}
			}
        }
    }
	
	 public function transfer_history_details ($arr = array())
      {
        extract($arr);
        $fund_data = DB::table($this->config->get('tables.FRANCHISEE_FUND_TRANSFER').' as ft')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as fum', 'fum.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as tum', 'tum.account_id', '=', 'ft.to_account_id')
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ft.currency_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as fud', 'fud.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as tud', 'tud.account_id', '=', 'ft.to_account_id')
				
                ->leftjoin($this->config->get('tables.FRANCHISEE_MST').' as fm', 'fm.account_id', '=', 'ft.to_account_id')
				
                ->select('ft.from_account_id','ft.to_account_id','fum.user_code as from_user_code','tum.user_code as to_user_code','tum.is_affiliate','ft.amount', 'ft.paidamt', 'ft.status', 'ft.is_deleted', 'ft.currency_id','ft.remark','cur.currency as currency_code', 'cur.currency_symbol', DB::Raw("CONCAT_WS('',fud.firstname,fud.lastname) as from_fullname"), DB::Raw("CONCAT_WS(' ',tud.firstname,tud.lastname) as to_fullname"), 'ft.transferred_on','ft.transaction_id','ft.to_user_type','fm.franchisee_type', DB::Raw("if(ft.from_account_id='".$account_id."',(select wallet from ".$this->config->get('tables.WALLET_LANG')." where wallet_id=ft.from_user_wallet_id and lang_id='".$this->config->get('app.locale_id')."'),(select wallet from ".$this->config->get('tables.WALLET_LANG')." where wallet_id=ft.to_user_wallet_id and lang_id='".$this->config->get('app.locale_id')."')) as wallet_name"))
                ->OrderBy('ft.fft_id','desc')
                ->where('ft.is_deleted', $this->config->get('constants.NOT_DELETED'));
			   $res =  $fund_data->get();
	    if (isset($account_id) && !empty($account_id))
        {
            $fund_data->where(function($qry) use($account_id)
            {
                $qry->where("ft.from_account_id", $account_id)
                        ->orWhere("ft.to_account_id", $account_id);
            });
        }
        if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date))
        {
            $fund_data->whereRaw("DATE(ft.transferred_on) >='".date('Y-m-d', strtotime($from_date))."'");
            $fund_data->whereRaw("DATE(ft.transferred_on) <='".date('Y-m-d', strtotime($to_date))."'");
        }
        else if (isset($to_date) && !empty($to_date))
        {
            $fund_data->whereRaw("DATE(ft.transferred_on) <='".date('Y-m-d', strtotime($to_date))."'");
        }
        else if (isset($from_date) && !empty($from_date))
        {
            $fund_data->whereRaw("DATE(ft.transferred_on) >='".date('Y-m-d', strtotime($from_date))."'");
        }
        if (isset($search_term) && !empty($search_term))
        {
            $fund_data->where(function($wcond) use($search_term)
            {
                $wcond->whereRaw("concat_ws('',fud.firstname,fud.lastname) like '%$search_term%'")
                        ->orWhereRaw("concat_ws(' ',tud.firstname,tud.lastname) like '%$search_term%'")
                        ->orWhereRaw("fum.uname like '%$search_term%'")
                        ->orWhereRaw("tum.uname like '%$search_term%'")
                        ->orWhereRaw("ft.transaction_id like '%$search_term%'");
            });
        }
        if (isset($orderby) && isset($order))
        {
            $fund_data->orderBy($orderby, $order);
        }
        if (isset($length) && !empty($length))
        {
            $fund_data->skip($start)->take($length);
        }
        if (isset($count) && !empty($count))
        {
            return $fund_data->count();
        }
        else
        {
            $fund_data = $fund_data->get();
            if (!empty($fund_data))
            {
                $status_type_arr = ['0'=>'warning', '1'=>'success', '2'=>'danger', '3'=>'info'];
                array_walk($fund_data, function(&$ftdata) use($status_type_arr)
                {
				   if(!empty($ftdata->is_affiliate) && $ftdata->is_affiliate==$this->config->get('constants.ACTIVE')){
					      $ftdata->type_of_user='Affiliate';
				     }
					elseif(!empty($ftdata->franchisee_type)){
						 $ftdata->type_of_user=ucwords(strtolower($this->config->get('constants.FRANCHISEE_TYPE.'.$ftdata->franchisee_type))).' '.'Channel Partner';
					}
                    $ftdata->transferred_on  = date('d-M-Y H:i:s', strtotime($ftdata->transferred_on));
                    $ftdata->status_class   = $status_type_arr[$ftdata->status];
                    $ftdata->status_name 	= $this->config->get('constants.FUND_TRANSFER_STATUS.'.$ftdata->status);
                    $ftdata->Ffrom_name 	= $ftdata->from_fullname;
                    $ftdata->Ffrom_user_code =$ftdata->from_user_code;
                    $ftdata->Fto_name 		= $ftdata->to_fullname;
                    $ftdata->Fto_user_code 	=$ftdata->to_user_code;
                    $ftdata->Famount 		= $ftdata->currency_symbol.' '.number_format($ftdata->amount, \AppService::decimal_places($ftdata->amount), '.', ',').' '.$ftdata->currency_code;
                    $ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
                    $ftdata->tranTypeCls = ($ftdata->from_account_id == $this->userSess->account_id) ? 'danger' : 'success';
                    $ftdata->transType = ($ftdata->from_account_id == $this->userSess->account_id) ? $this->config->get('constants.TRANSACTION_TYPE.DEBIT') : $this->config->get('constants.FUND_CREDIT');
                });
                return $fund_data;
            }
            else
                return false;
        }
    }

	
	/* NEW */
	
	public function getSetting_key_charges ()
    {
        $total = 0;
        $date = date('Y-m-d');      
        $commission_charge = DB::select(DB::raw("select setting_value from settings where setting_key='user_to_account_transfer_charge' "));
        return (!empty($commission_charge) && count($commission_charge) > 0) ? $commission_charge[0] : NULL;
    }
	
	public function getWalletBalnceTotal ($postdata)
    { 
        $total = 0;
        $wallet = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as a')
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', ' = ', 'a.wallet_id')
				->join($this->config->get('tables.WALLET_LANG').' as b', 'b.wallet_id', ' = ', 'a.wallet_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'a.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as uc', 'uc.currency_id', ' = ', 'a.currency_id')
                ->select(DB::raw('a.tot_credit, a.tot_debit, a.current_balance, a.account_id, b.wallet, b.wallet_id, a.currency_id, um.uname as username,uc.currency as currency_code,uc.currency_symbol,uc.decimal_places'))
				->orderBy('a.balance_id', 'DESC');
		if (isset($postdata['username']) && $postdata['username'])
        {
            $wallet->whereRaw("um.uname = '$postdata[username]'");
        }
        if (isset($postdata['account_id']) && $postdata['account_id'])
        {
            $wallet->whereRaw("um.account_id = '$postdata[account_id]'");
        }
        if (isset($postdata['wallet_id']) && $postdata['wallet_id'])
        {
            $wallet->whereRaw("a.wallet_id = '$postdata[wallet_id]'");
        }
        if (isset($postdata['currency_id']) && $postdata['currency_id'])
        {
            $wallet->whereRaw("a.currency_id = '$postdata[currency_id]'");
        }
		$result = '';
		if($postdata['purpose']=='transfer'){
			$wallet->where("w.is_franchisee_wallet",'=',$this->config->get('constants.ACTIVE'));
			$wallet->where("w.fundtransfer_status",'=',$this->config->get('constants.ACTIVE'));
			$result = $wallet->first();
		}	
		else {
			$result = $wallet->get();
		}        
		if(!empty($result)) {
			if(is_array($result)){
				array_walk($result, function($data)	{
					$data->current_balance=CommonLib::currency_format($data->current_balance, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code, 'decimal_places'=>$data->decimal_places]);				   
				});
			} else {
				$result->current_balance=CommonLib::currency_format($result->current_balance, ['currency_symbol'=>$result->currency_symbol, 'currency_code'=>$result->currency_code, 'decimal_places'=>$result->decimal_places]);		
			}
			return $result;
		}
    }
	
	public function get_fund_transfer_settings ($arr = array())
    {
	    extract($arr);
        $query = DB::table($this->config->get('tables.FUND_TRANSFER_SETTINGS'));
        if (isset($currency_id) && !empty($currency_id))
        {
            $query->where('currency_id', $currency_id);
        }
        $query->where('transfer_type', $transfer_type);
        
        $settings = $query->first();
        
        return (!empty($settings) && count($settings) > 0) ? $settings : false;
    }

	public function get_user_settings ($arr = array())
    {
        extract($arr);
        $qry = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as accsett')
                ->where('account_id', $arr)
                ->first();
        return (!empty($qry)) ? $qry : false;
    }

	public function get_currency_name ($currency_id)
    {
        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency as code');
    }
	
	public function get_wallet_name ($wallet_id)
    {
        return DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                        })
                        ->where('w.wallet_id', $wallet_id)
                        ->value('wl.wallet');
    }
	
	public function get_user_balance ($payment_type = 0, $arr = array(), $wallet_id, $currency_id = 0, $purpose = '')
    { 
        extract($arr);
	    $balance = 0;
		$result = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as b')
                ->where([
                    'b.account_id'=>$account_id,
                    'b.wallet_id'=>$wallet_id,
                    'b.currency_id'=>$currency_id])
                ->count();
        if ($result == 0)
        {
            $curresult = DB::table($this->config->get('tables.CURRENCIES'))
                    ->where(array(
                        'currency_id'=>$currency_id,
                        'status'=>$this->config->get('constants.ON')))
                    ->count();
			
            $ewalresult = DB::table($this->config->get('tables.WALLET'))
                    ->where(array(
                       'wallet_id'=>$wallet_id,
                       'status'=>$this->config->get('constants.ON')))
                    ->count();

            if (($curresult == 1) && ($ewalresult == 1))
            {
                $insert['account_id'] = $account_id;
                $insert['current_balance'] = '0';
                $insert['tot_credit'] = '0 ';
                $insert['tot_debit'] = '0';
                $insert['currency_id'] = $currency_id;
                $insert['wallet_id'] = $wallet_id;
                $status = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->insertGetId($insert);
            }
        }

        $result = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as b')
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'b.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'w.wallet_id')
                    ->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'b.currency_id')
                ->where([
                    'b.account_id'=>$account_id,
                    'b.wallet_id'=>$wallet_id,
                    'b.currency_id'=>$currency_id])
                ->select('b.*', 'wl.wallet', 'w.wallet_code', 'c.currency as currency_code', 'c.decimal_places')
                ->first();
        return (!empty($result)) ? $result : false;
    }
	
	public function get_userdetails_byid ($account_id)
    {
        return DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')                    
                        ->where('um.account_id', $account_id)
                        ->where('um.is_deleted', $this->config->get('constants.OFF'))
                        ->select(DB::raw('um.*,ud.*'))
                        ->first();
    }
	
	public function update_user_balance ($dataArray = array())
    {
        $updata = array();
        if (count($dataArray) > 0)
        {
            $cur_balance = $tot_credit = $tot_debit = 0;
		    $bal_details = $this->get_user_balance($dataArray['payment_type'], array('account_id'=>$dataArray['account_id']), $dataArray['wallet_id'], $dataArray['currency_id'],$dataArray['purpose']=0);

			if ($bal_details && count($bal_details) > 0)
            {
                $cur_balance = $bal_details->current_balance;
                $tot_credit = $bal_details->tot_credit;
                $tot_debit = $bal_details->tot_debit;
            }
            else
                return Lang::get('general.bal_status_msg');
            if ($dataArray['transaction_type'] == $this->config->get('constants.CREDIT'))
            {
                $updata['tot_credit'] = $tot_credit + $dataArray['amount'];
                $updata['current_balance'] = $cur_balance + $dataArray['amount'];
            }
            else if ($dataArray['transaction_type'] == $this->config->get('constants.DEBIT'))
            {
                $updata['tot_debit'] = $tot_debit + $dataArray['amount'];
                $updata['current_balance'] = $cur_balance - $dataArray['amount'];
            }
            $updata['updated_on'] = getGTZ();
            if ($bal_details && count($bal_details) > 0)
            {
                 $update_status = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->where('account_id', $dataArray['account_id'])
                        ->where('currency_id', $dataArray['currency_id'])
                        ->where('wallet_id', $dataArray['wallet_id'])
                        ->update($updata); 
						
				if(isset($dataArray['return']) && ($dataArray['return'] == 'return')){
					$bal_details->current_balance = isset($updata['current_balance']) ? $updata['current_balance'] :$bal_details->current_balance;
					$bal_details->tot_credit = isset($updata['tot_credit']) ? $updata['tot_credit'] :$bal_details->tot_credit;
					$bal_details->tot_debit = isset($updata['tot_debit']) ? $updata['tot_debit'] : $bal_details->tot_debit;
					return $bal_details;
				}
				return $update_status;
            }
        }
        return false;
    }
	
	public function add_transfertund_entry ($dataArray = array())
    {
		//print_R($dataArray);exit;
        //$dataArray['timeflag'] = date("Y-m-d H:i:s");
        return DB::table($this->config->get('tables.FRANCHISEE_FUND_TRANSFER'))
                        ->insertGetId($dataArray);
    }
	
	public function add_user_transaction ($dataArray = array())
    {
        //$dataArray['timeflag'] = date("Y-m-d H:i:s");
        return DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetId($dataArray);
    }
	
	public function get_payout_byid ($id = '')
    {
        return DB::table($this->config->get('tables.PAYMENT_TYPES'))
                        //->where('type_status', 0)
                        ->where('payment_type_id', $id)
                        ->first();
    }
}