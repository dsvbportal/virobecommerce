<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use Lang;
use DB;
use CommonLib;
class Wallet extends BaseModel
{

    public function __construct ()
    {
        parent::__construct();
    }

    public function account_balance ($arr = array())
    {
        if (!empty($arr))
        {
            extract($arr);
            $qry = DB::table($this->config->get('tables.WALLET').' as w')
                    ->leftjoin($this->config->get('tables.ACCOUNT_BALANCE').' as ub', 'ub.wallet_id', '=', 'w.wallet_id')
                    ->leftjoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ub.currency_id')
                    ->leftjoin($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                    {
                        $join->on('wl.wallet_id', '=', 'ub.wallet_id')
                        ->where('wl.lang_id', '=', $this->config->get('app.locale'));
                    })
                    ->where('ub.account_id', $account_id);

            if (isset($wallet_id) && $wallet_id > 0)
            {
                $qry->where('ub.wallet_id', $wallet_id);
            }

            if (isset($currency_id) && $currency_id > 0)
            {
                $qry->where('ub.currency_id', $currency_id);
            }
            $qry->select(DB::Raw('current_balance,tot_credit,tot_debit,w.wallet_id,cur.currency_id ,cur.currency as  currency_code,wl.wallet as wallet_name'));
            $result = $qry->get();
            return !empty($result) ? (count($result) == 1) ? $result[0] : $result : NULL;
        }
        return NULL;
    }

    public function my_wallets ($arr = array())
    {  
        if (!empty($arr))
        {
            extract($arr);			
            //print_R($this->config->get('app.locale_id'));exit;
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
			$qry->where('w.is_aff_wallet', $this->config->get('constants.ACTIVE'));
			
            $qry->select(DB::Raw("ub.current_balance,ub.tot_credit,ub.tot_debit,cur.currency_id,w.wallet_id,cur.currency as currency_code,cur.currency_symbol,cur.decimal_places,wl.wallet as wallet_name"));
            $qry->orderby('w.sort_order','asc');
            $result = $qry->get();		
			
            return (isset($curreny_id) || isset($wallet_id)) ? $result[0] : $result;
        }
        return NULL;
    }

    public function update_account_balance ($arr = array())
    {
        $balInfo = $this->get_user_balance($this->config->get('constants.PAYMENT_TYPES.WALLET'),['account_id'=>$arr['account_id']],$arr['wallet_id'],$arr['currency_id']);
		
		$sdata = '';
        if ($arr['type'] == $this->config->get('constants.TRANSACTION_TYPE.CREDIT'))
        {
            $sdata['current_balance'] = DB::raw('current_balance+'.$arr['amount']);
            $sdata['tot_credit'] = DB::raw('tot_credit+'.$arr['amount']);
        }
        else if ($arr['type'] == $this->config->get('constants.TRANSACTION_TYPE.DEBIT'))
        {
            $sdata['current_balance'] = DB::raw('current_balance-'.$arr['amount']);
            $sdata['tot_debit'] = DB::raw('tot_debit+'.$arr['amount']);
        }

        $upRes = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                ->where('account_id', $arr['account_id'])
                ->where('wallet_id', $arr['wallet_id'])
                ->where('currency_id', $arr['currency_id'])
                ->update($sdata);

        if (isset($arr['return']))
        {
            if ($arr['return'] == 'current')
            {
                $upRes = $this->account_balance(['account_id'=>$arr['account_id'], 'wallet_id'=>$arr['wallet_id'], 'currency_id'=>$arr['currency_id']]);
            }
        }
        return !empty($upRes) ? $upRes : NULL;
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
			/* else{
				 $wQry2->where("trs.wallet_id", $this->config->get('constants.WALLETS.VP'));
			} */
            if (isset($orderby) && isset($order)) {
                $wQry2->orderBy($orderby, $order);
            }
            else {
                $wQry2->orderBy('id', 'DESC');
            }
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

							if($t->statementline_id==config('stline.AFF_WITHDRAWAL_DEBIT')){
								if(isset($t->remark->data->payment_type_id)){
									$t->payout_type_name = trans('general.withdrawal_payment_types.'.$t->remark->data->payment_type_id);
								}
							}
							else if($t->statementline_id==config('stline.TEAM-BONUS-CREDIT') || $t->statementline_id==config('stline.LEADERSHIP-BONUS-CREDIT')){								
								$t->remark->data->from_date = date('d M',strtotime($t->remark->data->from_date));
								$t->remark->data->to_date = date('d M, Y',strtotime($t->remark->data->to_date));
							}
							$t->statementline = trans('transactions.'.$t->statementline_id.'.user.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark'])));
							$t->remark = trans('transactions.'.$t->statementline_id.'.user.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));							
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
	
	/* TDS Deducted Report */
	public function tds_deducted_details ($arr = array())
    {   	   
        extract($arr);    
        $qry = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs')
		       ->join($this->config->get('tables.STATEMENT_LINE').' as st', function($join)
                {
                    $join->on('st.statementline_id', '=', 'trs.statementline_id');
                    $join->where('st.tax_type', '=', $this->config->get('constants.TAX_TYPE.INCOME'));
                })
		        ->where('trs.is_deleted',$this->config->get('constants.OFF'))
		        ->where('trs.tax','>',0);
        if (isset($account_id))
        {
            $qry->where('trs.account_id', $account_id);

            if (isset($from) && !empty($from) && isset($to) && !empty($to))
            {
                $qry->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($from))."'");
                $qry->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (isset($from) && !empty($from))
            {
                $qry->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $qry->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($to))."'");
            }

            if (isset($search_term) && !empty($search_term))
            {
                $qry->whereRaw("trs.remark like '%$search_term%'");
            }
            if (isset($wallet_id) && !empty($wallet_id))
            {
                $qry->where("trs.wallet_id", $wallet_id);
            }
            if (isset($currency_id) && !empty($currency_id))
            {
                $qry->where("trs.currency_id", $currency_id);
            }
            if (isset($orderby) && isset($order)) {
                $qry->orderBy($orderby, $order);
            }
            else {
                $qry->orderBy('trs.id', 'DESC');
                $qry->orderBy('trs.created_on', 'DESC');
            }
            if (isset($length) && !empty($length)) {
                $qry->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
			
                return $qry->count();
            }
            else   
            {		
                $qry->leftJoin($this->config->get('tables.WALLET_LANG').' as b', function($join)
                {
                    $join->on('b.wallet_id', '=', 'trs.wallet_id');
                    $join->where('b.lang_id', '=', $this->config->get('app.locale_id'));
                });              
                $qry->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'trs.currency_id');
                $qry->select(DB::Raw('trs.id,trs.statementline_id,trs.account_id,trs.created_on,trs.transaction_id,trs.amt as amount,trs.tax,trs.transaction_type,st.statementline,trs.remark,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,b.wallet'));
                $transactions = $qry->get();			
                if ($transactions){
					array_walk($transactions, function(&$t)	{
				      $t->created_on   = ($t->created_on != null) ? showUTZ($t->created_on) : '';
						if (!empty($t->remark) && strpos($t->remark, '}') > 0) {
							$t->order_code = (isset($ordDetails->data->order_code)) ? $ordDetails->data->order_code : '';                
							$t->remark = $ordDetails = json_decode(stripslashes($t->remark));							
							 $t->statementline = trans('transactions.'.$t->statementline_id.'.user.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark']))); 
							$t->remark = trans('transactions.'.$t->statementline_id.'.user.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
						}
						else {
							$t->remark = $t->statementline;
						}	
						$t->amount = \CommonLib::currency_format($t->amount, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);
						$t->tax = \CommonLib::currency_format($t->tax, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);			
						 $t->actions 	   = [];
			             $t->actions[] 	   = ['class'=>'details','label'=>'View','url'=>route('aff.tds.details', ['trans_id'=>$t->transaction_id])];
						/// unset($t->statementline); 
					});
					return !empty($transactions) ? $transactions : [];					
				}
			}
        }
    }

	public function getTdsDetails(array $arr = array()){
		extract($arr);
		 $qry = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs')
		               ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'trs.account_id')
			           ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
					    ->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'um.account_id')
			            ->leftJoin($this->config->get('tables.ACCOUNT_VERIFICATION').' as acv', function($sub)
						   {
								$sub->on('acv.account_id', '=', 'trs.account_id');
								$sub->where('acv.document_type_id', '=', $this->config->get('constants.KYC_DOCUMENT_TYPE.PAN'));
						   })
					    ->join($this->config->get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'trs.currency_id')
						->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')			
						->where('trs.account_id', $account_id)
						->where('trs.is_deleted', $this->config->get('constants.OFF')) 
						->where('trs.transaction_id', $trans_id);
						 $qry->select(DB::raw('trs.amt,trs.tax,um.user_code,um.uname,um.email,um.mobile,concat_ws(" ",ud.firstname,ud.lastname) as fullname,ci.currency_symbol,ci.currency,acv.doc_number,lc.phonecode,trs.created_on'));
                         $tds_details = $qry->first();
						  if (!empty($tds_details))
							{
							$tds_details->amt 	  = $tds_details->currency_symbol.' '.number_format($tds_details->amt, 2, '.', ',').' '.$tds_details->currency;
							$tds_details->tax 	  = $tds_details->currency_symbol.' '.number_format($tds_details->tax, 2, '.', ',').' '.$tds_details->currency;
						    $tds_details->created_on   = ($tds_details->created_on != null) ? showUTZ($tds_details->created_on) : '';
							}
						   return $tds_details;
	        }
    /*
     * name		: get_all_wallet_list
     * @param 	:
     * @return 	: Response
     * get_all_wallet_list
     */

    public function get_all_wallet_list ($arr = array())
    {
        $result = DB::table($this->config->get('tables.WALLET').' as w')
                ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                {
                    $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                    ->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('w.is_aff_wallet', $this->config->get('constants.ACTIVE'))
			//	->where('w.fundtransfer_status', $this->config->get('constants.ACTIVE'))
                ->where(array('status'=>$this->config->get('constants.ACTIVE')))
                ->get();
        if (!empty($result))
        {			
            return $result;
        }
        return NULL;
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

    public function get_currencies ($arr = array())
    {
        extract($arr);
        $qry = DB::table($this->config->get('tables.CURRENCIES').' as c')
                ->join($this->config->get('tables.ACCOUNT_BALANCE').' as abal', 'abal.currency_id', '=', 'c.currency_id')
                ->where('abal.account_id', $account_id)
                ->where(array('c.status'=>$this->config->get('constants.ACTIVE')))
                ->select('c.currency as code', 'c.currency_id as id', 'abal.wallet_id', 'current_balance', 'c.currency_id');
        if (isset($currency_id) && !empty($currency_id))
        {
            $query->where('c.currency_id', $currency_id);
        } 
		else if (isset($currency_id) && is_array($currency_id))
		{
			$query->whereIn('c.currency_id', $currency_id);
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

    public function get_fund_transfer_settings ($arr = array())
    {
	    extract($arr);
        $query = DB::table($this->config->get('tables.FUND_TRANSFER_SETTINGS'));
        if (isset($currency_id) && !empty($currency_id))
        {
            $query->where('currency_id', $currency_id);
        }
        $query->where('transfer_type', $transfer_type);
        
             $settings = $query->get();
        
        return (!empty($settings) && count($settings) > 0) ? $settings : false;
    }

    public function getWalletBalnceTotal ($postdata)
    {
        $total = 0;
        $wallet = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as a')
                ->join($this->config->get('tables.WALLET_LANG').' as b', 'b.wallet_id', ' = ', 'a.wallet_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'a.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as uc', 'uc.currency_id', ' = ', 'a.currency_id')
                ->select(DB::raw('a.tot_credit, a.tot_debit, a.current_balance, a.account_id, b.wallet, b.wallet_id, a.currency_id, um.uname as username, uc.currency as currency_code,uc.currency_symbol,uc.decimal_places'));
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
        $wallet = $wallet->orderBy('a.balance_id', 'DESC');
		
		$result=$wallet->get();
		
	       if(!empty($result)) {
						array_walk($result, function($data)
						{
							$data->current_balance=CommonLib::currency_format($data->current_balance, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code, 'decimal_places'=>$data->decimal_places]);
								});
				       return $result;
				  }
    }

    public function get_user_verification_total ($arr = array())
    {
        extract($arr);
        $result = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->where('account_id', $account_id)
                ->get();
        return count($result);
    }

    public function get_currency_name ($currency_id)
    {
        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency as code');
    }

    public function add_user_transaction ($dataArray = array())
    {
        //$dataArray['timeflag'] = date("Y-m-d H:i:s");
        return DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
                        ->insert($dataArray);
    }

    public function add_transfertund_entry ($dataArray = array())
    {
		//print_R($dataArray);exit;
        //$dataArray['timeflag'] = date("Y-m-d H:i:s");
        return DB::table($this->config->get('tables.FUND_TRANSFER'))
                        ->insert($dataArray);
    }

    public function update_user_balance ($dataArray = array())
    {
        $updata = array();
        if (count($dataArray) > 0)
        {
            $cur_balance = $tot_credit = $tot_debit = 0;
		    $bal_details = $this->get_user_balance($dataArray['payment_type'], array('account_id'=>$dataArray['account_id']), $dataArray['wallet_id'], $dataArray['currency_id'],$dataArray['purpose']);
		    if ($bal_details && count($bal_details) > 0)
            {
                $cur_balance = $bal_details->current_balance;
                $tot_credit = $bal_details->tot_credit;
                $tot_debit = $bal_details->tot_debit;
            }
            else
                return Lang::get('general.bal_status_msg');
            //	print_R($dataArray['transaction_type']); exit;
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
            $updata['updated_on'] = date("Y-m-d H:i:s");
            if ($bal_details && count($bal_details) > 0)
            {
                $update_status = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->where('account_id', $dataArray['account_id'])
                        ->where('currency_id', $dataArray['currency_id'])
                        ->where('wallet_id', $dataArray['wallet_id'])
                        ->update($updata);
            }
            return $update_status;
        }
        return false;
    }

    public function getSetting_key_charges ()
    {
        $total = 0;
        $date = date('Y-m-d');
        //print_r( $date);exit;
        $commission_charge = DB::select(DB::raw("select setting_value from settings where setting_key='user_to_account_transfer_charge' "));
        //print_r($commission_charge);exit;
        return (!empty($commission_charge) && count($commission_charge) > 0) ? $commission_charge[0] : NULL;
    }

    public function get_userdetails_byid ($account_id)
    {

        return DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                       // ->leftjoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district', '=', 'ud.district')
                        //->leftjoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.name', '=', 'ud.state')
                        ->where('um.account_id', $account_id)
                        ->where('um.is_deleted', $this->config->get('constants.OFF'))
                        ->select(DB::raw('um.*,ud.*'))
                        ->first();
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
				
			if(!empty($result)){
			    $result->fcurrent_balance = number_format($result->current_balance, \AppService::decimal_places($result->current_balance), '.', ',');
				return $result;
			}
        return false;
    }

    public function get_user_settings ($arr = array())
    {
        extract($arr);
        $qry = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as accsett')
                ->where('account_id', $arr)
                ->first();
        return (!empty($qry)) ? $qry : false;
    }

    public function transfer_history_details ($arr = array())
    {  // print_r('qqq');exit;
        extract($arr);
        $fund_data = DB::table($this->config->get('tables.FUND_TRANSFER').' as ft')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as fum', 'fum.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as tum', 'tum.account_id', '=', 'ft.to_account_id')
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ft.currency_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as fud', 'fud.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as tud', 'tud.account_id', '=', 'ft.to_account_id')
                //->join($this->config->get('tables.WALLET') . ' as fwal','fwal.wallet_id','=','ft.from_account_wallet_id')
               /* ->join($this->config->get('tables.FUNDTRANSFER_STATUS_LOOKUP').' as fts', 'fts.status_id', '=', 'ft.status')
                 ->join($this->config->get('tables.FUND_TRANSFER_STATUS_LANG').' as ftsl', function($join)
                {
                    $join->on('ftsl.status_id', '=', 'ft.status');
                    $join->where('ftsl.lang_id', '=', $this->config->get('app.locale_id'));
                }) */
                ->select('ft.from_account_id','ft.to_account_id', 'fum.uname as from_uname','fum.user_code as from_user_code', 'tum.uname as to_uname', 'tum.user_code as to_user_code', 'ft.amount', 'ft.paidamt', 'ft.status', 'ft.is_deleted', 'ft.currency_id','ft.remark','cur.currency as currency_code', 'cur.currency_symbol', DB::Raw("CONCAT_WS('',fud.firstname,fud.lastname) as from_fullname"), DB::Raw("CONCAT_WS('',tud.firstname,tud.lastname) as to_fullname"), 'ft.transfered_on',  'ft.transaction_id', DB::Raw("if(ft.from_account_id='".$account_id."',(select wallet from ".$this->config->get('tables.WALLET_LANG')." where wallet_id=ft.from_account_ewallet_id and lang_id='".$this->config->get('app.locale_id')."'),(select wallet from ".$this->config->get('tables.WALLET_LANG')." where wallet_id=ft.to_account_ewallet_id and lang_id='".$this->config->get('app.locale_id')."')) as wallet_name"))
                ->OrderBy('ft.ft_id','desc')
		    	->where('ft.from_account_ewallet_id', $this->config->get('constants.WALLETS.VP'))
                ->where('ft.to_account_ewallet_id', $this->config->get('constants.WALLETS.VP'))
				->where('fum.account_type_id','!=', $this->config->get('constants.ACCOUNT_TYPE.ADMIN'))
                ->where('tum.account_type_id','!=', $this->config->get('constants.ACCOUNT_TYPE.ADMIN'))
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
            $fund_data->whereRaw("DATE(ft.transfered_on) >='".getGTZ($from_date,'Y-m-d')."'");
            $fund_data->whereRaw("DATE(ft.transfered_on) <='".getGTZ($to_date,'Y-m-d')."'");
        }
        else if (isset($to_date) && !empty($to_date))
        {
            $fund_data->whereRaw("DATE(ft.transfered_on) <='".getGTZ($to_date,'Y-m-d')."'");
        }
        else if (isset($from_date) && !empty($from_date))
        {
            $fund_data->whereRaw("DATE(ft.transfered_on) >='".getGTZ($from_date,'Y-m-d')."'");
        }
        if (isset($search_term) && !empty($search_term))
        {
            $fund_data->where(function($wcond) use($search_term)
            {
                $wcond->whereRaw("concat_ws('',fud.firstname,fud.lastname) like '%$search_term%'")
                        ->orWhereRaw("concat_ws('',tud.firstname,tud.lastname) like '%$search_term%'")
                        ->orWhereRaw("fum.uname like '%$search_term%'")
                        ->orWhereRaw("tum.uname like '%$search_term%'")
                        ->orWhereRaw("ft.transaction_id like '%$search_term%'");
            });
        }

        /* if (isset($wallet_id) && !empty($wallet_id))
        {
            $fund_data->where(function($wcond) use($wallet_id, $account_id)
            {
                $wcond->where(function($wcond2) use($wallet_id, $account_id)
                {
                    $wcond2->where("ft.from_account_ewallet_id", $wallet_id)
                            ->Where("ft.from_account_id", $account_id);
                });
                $wcond->orWhere(function($wcond3) use($wallet_id, $account_id)
                {
                    $wcond3->where("ft.to_account_ewallet_id", $wallet_id)
                            ->Where("ft.to_account_id", $account_id);
                });
            });
        } */
        if (isset($currency_id) && !empty($currency_id))
        {
            $fund_data->where("ft.currency_id", $currency_id);
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
                    $ftdata->transfered_on  = showUTZ($ftdata->transfered_on); // date('d-M-Y H:i:s', strtotime($ftdata->transfered_on));
                    $ftdata->status_class   = $status_type_arr[$ftdata->status];
                    $ftdata->status_name 	= $this->config->get('constants.FUND_TRANSFER_STATUS.'.$ftdata->status);
                    $ftdata->from_uname 	= $ftdata->from_uname;
                    $ftdata->to_uname 		= $ftdata->to_uname;
					$ftdata->from_user_code 	= $ftdata->from_user_code;
					$ftdata->to_user_code 	= $ftdata->to_user_code;
                    $ftdata->Famount 		= $ftdata->currency_symbol.' '.number_format($ftdata->amount, \AppService::decimal_places($ftdata->amount), '.', ',');
                    $ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',');
                    $ftdata->tranTypeCls = ($ftdata->from_account_id == $this->userSess->account_id) ? 'danger' : 'success';
                    $ftdata->transType = ($ftdata->from_account_id == $this->userSess->account_id) ? $this->config->get('constants.TRANSACTION_TYPE.DEBIT') : $this->config->get('constants.FUND_CREDIT');
                });
                return $fund_data;
            }
            else
                return false;
        }
    }

    public function get_wallet_list ($cond_array = array())
    {
        if (count($cond_array) > 0)
        {
            if (isset($cond_array['withdrawal_status']) && $cond_array['withdrawal_status'] >= 0)
            {
                $result = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->applang);
                        })
                        ->where('w.withdrawal_status', $cond_array['withdrawal_status'])
                        ->where('w.fundtransfer_status', 0)
                        ->get();
            }
            if (isset($cond_array['fundtransfer_status']) && $cond_array['fundtransfer_status'] >= 0)
            {
                $result = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->applang);
                        })
                        ->where('w.fundtransfer_status', $cond_array['fundtransfer_status'])
                        ->where('w.fundtransfer_status', 0)
                        ->get();
            }
            /* if (isset($cond_array['internaltransfer_status']) && $cond_array['internaltransfer_status'] >= 0)
              {
              $result = DB::table($this->config->get('tables.WALLET').' as w')
              ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
              {
              $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
              ->where('wl.lang_id', '=', $this->applang);
              })
              ->where('w.internaltransfer_status', $cond_array['internaltransfer_status'])
              ->where('w.fr_fund_transfer_status', 0)
              ->get();
              }
              if (isset($cond_array['purchase_status']) && $cond_array['purchase_status'] >= 0)
              {
              $result = DB::table($this->config->get('tables.WALLET').' as w')
              ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
              {
              $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
              ->where('wl.lang_id', '=', $this->applang);
              })
              ->where('w.purchase_status', $cond_array['purchase_status'])
              ->where('w.fr_fund_transfer_status', 0)
              ->get();
              } */
        }
        else
        {
            $result = DB::table($this->config->get('tables.WALLET').' as w')
                    ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                    {
                        $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                        ->where('wl.lang_id', '=', $this->applang);
                    })
                    ->where('fr_fund_transfer_status', 0)
                    ->get();
        }
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
    }
	
	public function updateTDSTransactions($account_id,$currency_id,$relation_id,$tds_details=array()){
		
		$wallet_id = $this->config->get('constants.WALLETS.VIM');
		$tds_statement_line = 42;
		if($account_id>0 && $currency_id>0 && !empty($tds_details) && $tds_details['tds_amount']>0){
			
			$usrbal_Info = $this->update_account_balance(array('wallet_id'=>$wallet_id, 'account_id'=>$account_id, 'currency_id'=>$currency_id, 'amount'=>$tds_details['tds_amount'], 'type'=>$this->config->get('constants.TRANSACTION_TYPE.DEBIT'), 'return'=>'current'));
				
			$decimal_places = \AppService::decimal_places($tds_details['tds_total_commission'],'.', ',');
			$remark = addslashes(json_encode(['data'=>['perc'=>$tds_details['tds_per'],'amount'=>number_format($tds_details['tds_total_commission'], $decimal_places, '.', ',').' '.$usrbal_Info->currency_code]]));
			
			$status = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
					->insertGetId(array(
				'account_id'=>$account_id,
				'payment_type_id'=>9, /* WALLET */
				'statementline_ID'=>$tds_statement_line,
				'amt'=>$tds_details['tds_amount'],
				'paid_amt'=>$tds_details['tds_amount'],				
				'wallet_id'=>$wallet_id,
				'currency_id'=>$currency_id,
				'transaction_type'=>$this->config->get('constants.DEBIT'),
				'remark'=>$remark,				
				'ip_address'=>request()->getClientIp(true),
				'transaction_id'=>$this->generateTransactionID(),
				'current_balance'=>$usrbal_Info->current_balance,
				'status'=>$this->config->get('constants.ACTIVE'),
				'relation_id'=>$relation_id,
				'created_on'=>getGTZ()
			));
		}
	}
   public function get_wallet_balance(array $arr = array()){
	   if(!empty($arr)){
		  extract($arr);   
	     $qry= DB::table($this->config->get('tables.ACCOUNT_WALLET_BALANCE').' as ab')
		         ->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ab.currency_id')
		       ->select('ab.current_balance','cur.currency_symbol','cur.currency','cur.decimal_places')
		       ->where('account_id', $account_id);
			   if(!empty($wallet_id)){
		         $qry->where('wallet_id', $wallet_id);
			   }
			  $res =$qry->first();
			     $res->current_balance=CommonLib::currency_format($res->current_balance, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->currency, 'decimal_places'=>$res->decimal_places]); 
			 
            return $res;
	   }
   }
    //->select('ft.amount','ft.paidamt','ft.status','ft.is_deleted','fwal.wallet_name','cur.code','ft.to_account_id as to_account','ft.from_account_id as from_account','lud.last_name','fud.first_name','ft.transfered_on')



    /* public function get_wallet()
      {
      return DB::table($this->config->get('tables.WALLET').' as w')
      ->select('w.wallet_id','w.wallet_name')
      ->get();
      } */
}
