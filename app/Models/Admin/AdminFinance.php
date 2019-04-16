<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Commonsettings;
use App\Models\Admin\Franchisee;
use DB;
use CommonLib;
use Log;


class AdminFinance extends BaseModel
{

    public function __construct ()
    {
        parent::__construct();
        $this->memberObj = new Member();
        $this->commonObj = new Commonsettings();
    }

    public function get_wallets ($purpose='')
    {
        $qry = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'w.wallet_id')
                        ->where('w.status', 1);
		if(!empty($purpose)){
		   $qry->where($purpose,'=',1);
		}
        return $qry->lists('wl.wallet', 'w.wallet_id');
    }

    public function add_fund_merchant (array $arr = array())
    {
        $merchant = $this->get_merchant_details($arr);
        $bal = $this->get_account_bal($arr);
        if (($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT')) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if (!empty($merchant))
        {
            $mr_accId = $merchant->account_id;
            $fund['transaction_id'] = $this->generateTransactionID();
            $fund['currency_id'] = $arr['currency_id'];
            $fund['amount'] = $arr['amount'];
            $fund['paidamt'] = $arr['amount'];
            $fund['handleamt'] = 0;
            if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $mr_accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $mr_accId;
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
            $fund['created_on'] = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['added_by'] = $arr['admin_id'];
            $fund['status'] = $this->config->get('constants.ON');
            $fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
                    ->insertGetId($fund);
            if (!empty($fund_id))
            {
                if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['to_account_id'=>$mr_accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['from_account_id'=>$mr_accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
                }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }

    public function fund_transfer_settings ()
    {
        return DB::table($this->config->get('tables.FUND_TRANASFER_SETTINGS'))
                        ->where('transfer_type', 0)
                        ->first();
    }

    public function add_fund_member (array $arr = array())
    {
        $user_details = $this->memberObj->get_member_details($arr);
        $bal = $this->get_account_bal($arr);

        if (($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT')) && (!empty($bal)) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if(!empty($user_details))
        {
            $accId 				= $user_details->account_id;
            $fund['added_by']   = $arr['admin_id'];
            $fund['transaction_id'] = $this->generateTransactionID();
            $fund['currency_id'] = $arr['currency_id'];
            $fund['amount'] = $arr['amount'];
            $fund['paidamt'] = $arr['amount'];
            $fund['remark'] = $arr['remarks'];
            $fund['handleamt'] = 0;
            if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $accId;
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
            //$fund['transfer_type'] = $trasn_type;
            $fund['created_on'] = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['status'] = $this->config->get('constants.ON');
            $fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
                    ->insertGetId($fund);
            if (!empty($fund_id))
            {
                if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'],'credit_remark_data'=>['amount'=>$arr['amount']], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM','debit_remark_data'=>['amount'=>$arr['amount']]], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
                }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }

    public function add_fund_dsa (array $arr = array())
    {
        $user_details = $this->get_dsa_details($arr);
        $bal = $this->get_account_bal($arr);
        if (($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT')) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if (!empty($user_details))
        {
            $accId = $user_details->account_id;
            $fund['added_by'] = $arr['admin_id'];
            $fund['transaction_id'] = $this->generateTransactionID();
            $fund['currency_id'] = $arr['currency_id'];
            $fund['amount'] = $arr['amount'];
            $fund['paidamt'] = $arr['amount'];
            $fund['handleamt'] = 0;
            if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $accId;
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
            $fund['created_on'] = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['status'] = $this->config->get('constants.ON');
            $fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
                    ->insertGetId($fund);
            if (!empty($fund_id))
            {
                if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
                }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }

    public function withdrawals_list (array $data = array(), $count = false)
    {
        extract($data);
        $query = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wdm')
                ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'wdm.account_id')
                ->join($this->config->get('tables.WALLET').' as wt', 'wt.wallet_id', '=', 'wdm.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wtl', function($subquery)
                {
                    $subquery->on('wtl.wallet_id', '=', 'wt.wallet_id')
                    ->where('wtl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('wdm.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'wdm.currency_id')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wdm.payment_type_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as ptl', function($subquery)
                {
                    $subquery->on('ptl.payment_type_id', '=', 'pt.payment_type_id')
                    ->where('ptl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'wdm.account_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'wdm.account_id')
                    ->where('adm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'));
                })
                ->join($this->config->get('tables.LOCATION_COUNTRIES').' as loc', 'loc.country_id', '=', 'adm.country_id')
                ->where('wdm.status_id', '=', $this->config->get('constants.WITHDRAWAL_STATUS.PENDING'))
                ->selectRaw('wdm.wd_id,wdm.payment_type_id,wdm.currency_id,wdm.wallet_id,wdm.payment_details,wdm.amount,wdm.paidamt,wdm.handleamt,wdm.expected_on,wdm.created_on,wdm.status_id,wdm.payment_status,am.uname,CONCAT_WS(\' \',ad.first_name,ad.last_name) as full_name,adm.country_id,loc.country,ptl.payment_type,cur.currency,cur.currency_symbol');
        if (!empty($from) && isset($from))
        {
            $query->whereDate('wdm.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (!empty($to) && isset($to))
        {
            $query->whereDate('wdm.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_term) && !empty($search_term))
        {
            if (!empty($filterTerms) && !empty($filterTerms))
            {
                $search_term = '%'.$search_term.'%';
                $search_field = ['UserName'=>'am.uname', 'FullName'=>'concat_ws(\' \',ad.first_name,ad.last_name)'];
                $query->where(function($sub) use($filterTerms, $search_term, $search_field)
                {
                    foreach ($filterTerms as $search)
                    {
                        if (array_key_exists($search, $search_field))
                        {
                            $sub->orWhere(DB::raw($search_field[$search]), 'like', $search_term);
                        }
                    }
                });
            }
            $query->where(function($wcond) use($search_term)
            {
                $wcond->Where('am.uname', 'like', $search_term)
                        ->orwhere('ad.first_name', 'like', $search_term)
                        ->orwhere('ad.last_name', 'like', $search_term);
            });
        }
        if (isset($orderby) && isset($order))
        {
            if ($orderby == 'created_on')
            {
                $query->orderBy('wdm.created_on', $order);
            }
            elseif ($orderby == 'uname')
            {
                $query->orderBy('wdm.uname', $order);
            }
            elseif ($orderby == 'payment_type')
            {
                $query->orderBy('ptl.payment_type', $order);
            }
            elseif ($orderby == 'country')
            {
                $query->orderBy('loc.country', $order);
            }
            elseif ($orderby == 'amount')
            {
                $query->orderBy('wdm.amount', $order);
            }
            elseif ($orderby == 'currency')
            {
                $query->orderBy('cur.currency', $order);
            }
            elseif ($orderby == 'paidamt')
            {
                $query->orderBy('wdm.paidamt', $order);
            }
            elseif ($orderby == 'handleamt')
            {
                $query->orderBy('adm.handleamt', $order);
            }
        }
        if (isset($currency) && !empty($currency))
        {
            $query->where('loc.currency', $currency);
        }
        if (isset($payment_type) && !empty($payment_type))
        {
            $query->where('ptl.payment_type', $payment_type);
        }
        if (isset($start) && isset($length))
        {
            $query->skip($start)->take($length);
        }
        if (isset($count) && !empty($count))
        {
            return $query->count();
        }
        else
        {
            $result = $query->orderBy('wdm.created_on', 'DESC')
                    ->get();
            if (!empty($result))
            {
                return $result;
            }
        }
        return null;
    }

    public function fund_transfer_history (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.FUND_TRANASFER').' as ft')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as accf', 'accf.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'accf.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as acct', 'acct.account_id', '=', 'ft.to_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'acct.account_id')
			    ->leftjoin($this->config->get('tables.WALLET_LANG').' as wtl', function($subquery)
                 {
                    $subquery->on('wtl.wallet_id', '=', 'ft.from_account_ewallet_id')
                    ->where('wtl.lang_id', '=', $this->config->get('app.locale_id'));
                })
				->leftjoin($this->config->get('tables.WALLET_LANG').' as wt2', function($subquery)
                 {
                    $subquery->on('wt2.wallet_id', '=', 'ft.to_account_ewallet_id')
                    ->where('wt2.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'ft.currency_id')
                ->join($this->config->get('tables.ACCOUNT_TYPES').' as sf', 'sf.id', '=', 'accf.account_type_id')
                ->join($this->config->get('tables.ACCOUNT_TYPES').' as st', 'st.id', '=', 'acct.account_type_id')
                ->where('ft.added_by', '!=', $this->config->get('constants.ACCOUNT.ADMIN_ID'))
                ->where('ft.is_deleted', 0)
                ->select('ft.ft_id','ft.transaction_id','ft.from_account_id','ft.to_account_id','ft.from_account_ewallet_id as from_wallet_id', 'ft.to_account_ewallet_id as to_wallet_id', 'ft.currency_id', 'ft.amount','ft.handleamt','ft.paidamt','ft.created_on', DB::raw('CONCAT_WS(\' \',acd.firstname,acd.lastname) as trans_from'), DB::raw('CONCAT_WS(\' \',accd.firstname,accd.lastname) as trans_to'), 'wt2.wallet as wallet_name', 'ft.status as status_id', 'c.currency as code','c.currency_symbol','c.decimal_places','acct.uname', 'sf.account_type_name as from_acc_roll', 'st.account_type_name as to_acc_roll', 'accf.user_code as fuser_code', 'acct.user_code as tuser_code');
				
				if (isset($terms) && !empty($terms)){
		
						if (is_numeric($terms)){
							   if(strlen($terms) <=10){
						              /* $qry->where('accf.user_code', $terms)
						                  ->Orwhere('acct.user_code', $terms); */
									 $qry->whereRaw(('accf.user_code = '.$terms. ' OR acct.user_code = '.$terms));
							   }else{
								   $qry->where('ft.transaction_id', $terms);
							   }
						}
					
					  else{
						 $qry->where(DB::Raw('CONCAT_WS(\' \',acd.firstname,acd.lastname)'), 'like', $terms)
                              ->Orwhere(DB::Raw('CONCAT_WS(\' \',accd.firstname,accd.lastname)'), 'like', $terms);
					  }
			        }
				  if (isset($wallet_id) && !empty($wallet_id))
                    {
						
                      $qry->where("ft.from_account_ewallet_id", $wallet_id)
                          ->Orwhere("ft.to_account_ewallet_id", $wallet_id);
                    }
			       if (isset($type) && ($type != '')){
					 
                         $qry->where('sf.id', $type)
                              ->Orwhere('st.id', $type);
                    }
				   if (isset($from) && isset($to) && !empty($from) && !empty($to)){ 
						 $qry->whereDate('ft.created_on', '>=', getGTZ($from,'Y-m-d'));
						 $qry->whereDate('ft.created_on', '<=', getGTZ($to,'Y-m-d'));
			        }
					 else if (!empty($from) && isset($from)){ 
						 $qry->whereDate('ft.created_on', '<=', getGTZ($from,'Y-m-d'));
					 }
					else if (!empty($to) && isset($to)){ 
						  $qry->whereDate('ft.created_on', '>=', getGTZ($to,'Y-m-d'));
					 }
					if (isset($orderby) && isset($order))
					{
						$qry->orderBy($orderby, $order);
					}
					if (isset($length) && !empty($length))
					{
						$qry->skip($start)->take($length);
					}
					if (isset($count) && !empty($count))
					{
						return $qry->count();
					} 
				   else
					{
						$result= $qry->orderBy('ft.created_on', 'desc') 
						   ->get();
					  if(!empty($result)) {
							array_walk($result, function($data)
							{
								$data->statusCls = $this->config->get('dispclass.fund_trasnfer_status.'.$data->status_id);
							    $data->status = $this->config->get('constants.FUND_TRANSFER_STATUS.'.$data->status_id);
							    $data->created_on = (!empty($data->created_on)) ? showUTZ($data->created_on) :'';
								$data->amount = CommonLib::currency_format($data->amount, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places]);
								$data->handleamt = CommonLib::currency_format($data->handleamt, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places]);
								$data->paidamt = CommonLib::currency_format($data->paidamt, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places]);
							});
                     return $result;
					  }
				   }
                return false;
    }

    public function admin_fund_transfer_history (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.FUND_TRANASFER').' as ft')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as accf', 'accf.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'accf.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as acct', 'acct.account_id', '=', 'ft.to_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'acct.account_id')
                ->leftjoin($this->config->get('tables.WALLET').' as fw', 'fw.wallet_id', '=', 'ft.from_account_ewallet_id')
                ->leftjoin($this->config->get('tables.WALLET').' as tw', 'tw.wallet_id', '=', 'ft.to_account_ewallet_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'ft.currency_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as adb', 'adb.account_id', '=', 'ft.added_by')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acu', 'acu.account_id', '=', 'ft.from_account_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as act', 'act.account_id', '=', 'ft.to_account_id')
                ->join($this->config->get('tables.ACCOUNT_TYPES').' as syt', 'syt.id', '=', 'adb.account_type_id')
                ->join($this->config->get('tables.ACCOUNT_TYPES').' as sf', 'sf.id', '=', 'acu.account_type_id')
                ->join($this->config->get('tables.ACCOUNT_TYPES').' as st', 'syt.id', '=', 'act.account_type_id')
                ->where('adb.account_type_id', 0)
                ->select('ft.ft_id', 'ft.transaction_id', 'ft.from_account_id', 'ft.to_account_id', 'ft.from_account_ewallet_id as from_wallet_id', 'ft.to_account_ewallet_id as to_wallet_id', 'ft.currency_id', 'ft.amount', 'ft.handleamt', 'ft.paidamt', 'ft.created_on', DB::raw('CONCAT_WS(\' \',acd.firstname,acd.lastname) as trans_from'), DB::raw('CONCAT_WS(\' \',accd.firstname,accd.lastname) as trans_to'), DB::raw('IF(ft.from_account_ewallet_id > 0,fw.wallet_code,tw.wallet_code) as wallet_name'), 'adb.uname as added_by', 'syt.account_type_name as added_by_role', 'ft.status as status_id', 'c.code', 'acct.uname', 'sf.account_type_name as from_acc_roll', 'st.account_type_name as to_acc_roll', 'acu.uname as funame', 'act.uname as tuname');
        if (isset($arr['skip']) && !empty($arr['skip']))
        {
            $qry->skip($arr['skip'])
                    ->take($arr['length']);
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('ft.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('ft.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($sysrole) && ($sysrole != ''))
        {
            $qry->where('sf.system_role_id', $sysrole);
        }
        if (isset($terms) && !empty($terms))
        {
            if (is_numeric($terms))
            {
                $qry->where('ft.transaction_id', $terms);
            }
            else
            {
                $qry->where(DB::Raw('CONCAT_WS(\' \',acd.firstname,acd.lastname)'), 'like', $terms)
                        ->Orwhere(DB::Raw('CONCAT_WS(\' \',accd.firstname,accd.lastname)'), 'like', $terms);
            }
        }
        $qry->orderBy('ft.created_on', 'desc');
        $qry->orderBy('ft.ft_id', 'desc');
        $result = $qry->get();
        if ($count)
        {
            return $qry->count();
        }
        if (!empty($result))
        {
            array_walk($result, function($data)
            {
                /*  if ($data->transfer_type == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                  {
                  $data->trans_type = 'Credit';
                  if ($data->from_account_id == 0)
                  {
                  $data->trans_from = 'SYSTEM';
                  }
                  }
                  else
                  {
                  $data->trans_type = 'Debit';
                  } */
                $data->statusCls = config('dispclass.fund_trasnfer.status.'.$data->status_id);
                $data->status = trans('db_trans.fund_trasnfer.status.'.$data->status_id);
                $data->created_on = showUTZ($data->created_on, 'Y-m-d H:i:s');
                 $data->amount = $data->amount.' '.$data->code; 
				$data->handleamt = $data->handleamt.' '.$data->code;
                $data->paidamt = $data->paidamt.' '.$data->code;
            });
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function transaction_log (array $arr = array(), $count = false)
     {
        extract($arr);
        $wQry2 = DB::Table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs');
		
			   $wQry2->join($this->config->get('tables.ACCOUNT_MST').' as am','am.account_id', '=', 'trs.account_id');
               
               $wQry2->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'trs.account_id');
				
                $wQry2->join($this->config->get('tables.STATEMENT_LINE').' as st', function($join)
                {
                    $join->on('st.statementline_id', '=', 'trs.statementline_id');
                });
                $wQry2->leftJoin($this->config->get('tables.WALLET_LANG').' as b', function($join)
                {
                    $join->on('b.wallet_id', '=', 'trs.wallet_id');
                    $join->where('b.lang_id', '=', $this->config->get('app.locale_id'));
                });
                $wQry2->leftJoin($this->config->get('tables.PAYMENT_TYPES').' as c', function($join)
                {
                    $join->on('c.payment_type_id', '=', 'trs.payment_type_id');
                });
                $wQry2->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'trs.currency_id');
				
                $wQry2->select(DB::Raw('trs.id,CONCAT_WS(\' \',acd.firstname,acd.lastname) as fullname,am.uname,am.email,am.mobile,trs.statementline_id,trs.account_id,trs.created_on,trs.transaction_id,trs.amt as amount,trs.handle_amt,trs.tax,trs.paid_amt,trs.transaction_type,trs.current_balance,st.statementline,trs.remark,trs.wallet_id,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,b.wallet'));
                       $wQry2->Where('trs.is_deleted','=',$this->config->get('constants.OFF'));
               if(isset($payment_type_id) && !empty($payment_type_id)){
				   
				   $wQry2->Where('trs.payment_type_id','=',$payment_type_id);
			    }
				
			  if(isset($type) && !empty($type)){
				  
					 if($type=='affiliate'){
					        $wQry2->Where('am.is_affiliate','=',$this->config->get('constants.ON'));
					 }
					else if($type=='franchise'){
					       $wQry2->Where('am.account_type_id','=',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'));
					}
					/* else if($type=='all'){
						$wQry2->Where('am.is_affiliate','=',$this->config->get('constants.ON'));
						$wQry2->orWhere('am.account_type_id','=',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'));
					} */
				}
		     if (isset($search_text) && !empty($search_text))
				{ 
					if(!empty($filterchk) && !empty($filterchk))
					{   
						$search_text='%'.$search_text.'%'; 
						$search_field=['UserName'=>'am.uname','FullName'=>'concat_ws(" ",acd.firstname,acd.lastname)','Email'=>'am.email','Mobile'=>'am.mobile'];
						$wQry2->where(function($sub) use($filterchk,$search_text,$search_field){
							foreach($filterchk as $search)
							{   
								if(array_key_exists($search,$search_field)){
								  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
								} 
							}
						});
			      }
		 	 }
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
            if (isset($currency_id) && !empty($currency_id))
            {
                $wQry2->where("trs.currency_id", $currency_id);
            }
            if (isset($orderby) && isset($order)) {
                $wQry2->orderBy($orderby, $order);
            }
            else {
                $wQry2->orderBy('trs.id', 'DESC');
            }
             if (isset($length) && !empty($length)) {
                $wQry2->skip($start)->take($length);
            } 
            if (isset($count) && !empty($count)) {
                return $wQry2->count();
            }
           $transactions = $wQry2->get();
			 /*  echo '<pre>';
			  print_R($transactions); die; */
			  
            if ($transactions){
					array_walk($transactions, function(&$t)	{
					 if($t->created_on ==''){
						  $t->created_on='';
					  }
				      else{
						  $t->created_on= showUTZ($t->created_on, 'Y-m-d H:i:s');
					 }
				        if(!empty($t->remark) && strpos($t->remark, '}') > 0) {
							$t->order_code    = (isset($ordDetails->data->order_code)) ? $ordDetails->data->order_code : '';                
							$t->remark 		  = $ordDetails = json_decode(stripslashes($t->remark));
							
							if(isset($t->remark->data)){
								
								if(isset($t->remark->data->fr_type)){
									$t->remark->data->fr_type = trans('general.fr_type.'.$t->remark->data->fr_type);
								}
								if(isset($t->remark->data->period)&& !empty($t->remark->data->period)){
									$t->remark->data->period = showUTZ($t->remark->data->period,$t->remark->data->date_format);
								}
								$t->statementline = trans('transactions.'.$t->statementline_id.'.admin.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark'])));
								
								$t->remark 	= trans('transactions.'.$t->statementline_id.'.admin.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
							}
				
						}
						else {
							$t->remark = $t->statementline;
						}	
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
	
    public function admin_credit_debit_history (array $arr = array(), $count = false)
    {	
        extract($arr);
        $qry = DB::Table($this->config->get('tables.FUND_TRANASFER').' as ft')
                ->join($this->config->get('tables.ACCOUNT_MST').' as accf', 'accf.account_id', '=', 'ft.from_account_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acct', 'acct.account_id', '=', 'ft.to_account_id')
                ->where(function($c)
                {
                    $c->whereRaw('accf.account_type_id = '.$this->config->get('constants.ACCOUNT_TYPE.ADMIN').' OR acct.account_type_id='.$this->config->get('constants.ACCOUNT_TYPE.ADMIN'));
                })
                ->where('ft.is_deleted', $this->config->get('constants.OFF'));

        if (isset($trans_type))
        {
            if (!empty($trans_type) && $trans_type == 1)
            {
                $qry->where('ft.from_account_id', $this->config->get('constants.ACCOUNT.ADMIN_ID'));
            }elseif($trans_type == 2){
				$qry->where('ft.from_account_id','!=',$this->config->get('constants.ACCOUNT.ADMIN_ID'));
			}
        }
		 
        if (isset($from_date) && !empty($from_date))
        {
            $qry->whereDate('ft.created_on', '>=', getGTZ($from_date, 'Y-m-d'));
        }
        if (isset($to_date) && !empty($to_date))
        {
            $qry->whereDate('ft.created_on', '<=', getGTZ($to_date, 'Y-m-d'));
        }
        
        if ($count)
        {
            return $qry->count();
        }
        if (isset($start))
        {
            $qry->skip($start)
                    ->take($length);
        }
        //$qry->orderBy('ft.created_on', 'desc');
        $qry->orderBy('ft.ft_id', 'desc');

        $res = $qry->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'accf.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'acct.account_id')
                ->leftjoin($this->config->get('tables.WALLET').' as fw', 'fw.wallet_id', '=', 'ft.from_account_ewallet_id')
                ->leftjoin($this->config->get('tables.WALLET').' as tw', 'tw.wallet_id', '=', 'ft.to_account_ewallet_id')
                ->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'ft.currency_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as adb', 'adb.account_id', '=', 'ft.added_by')
                ->leftjoin($this->config->get('tables.ACCOUNT_TYPES').' as syt', 'syt.id', '=', 'adb.account_type_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_TYPES').' as syfu', 'syfu.id', '=', 'accf.account_type_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_TYPES').' as sytu', 'sytu.id', '=', 'acct.account_type_id')
                ->select('ft.ft_id', 'ft.transaction_id', 'ft.from_account_id', 'ft.to_account_id', 'ft.from_account_ewallet_id as from_wallet_id', 'ft.to_account_ewallet_id as to_wallet_id', 'ft.currency_id', 'ft.amount', 'ft.handleamt', 'ft.paidamt', 'ft.created_on', DB::raw('CONCAT_WS(\' \',acd.firstname,acd.lastname) as trans_from'), DB::raw('CONCAT_WS(\' \',accd.firstname,accd.lastname) as trans_to'), DB::raw('IF(ft.from_account_ewallet_id > 0,fw.wallet_code,tw.wallet_code) as wallet_name'), 'adb.uname as added_by', 'syt.account_type_name as added_by_role', 'ft.status as status_id', 'c.currency as code', 'c.currency_symbol', 'c.decimal_places', 'accf.uname as funame', 'acct.uname as tuname','syfu.account_type_name as from_acc_role','sytu.account_type_name as to_acc_role');
				if (isset($terms) && !empty($terms))
				{
					$terms = '%'.$terms.'%';
					if (is_numeric($terms))
					{
						$res->where('ft.transaction_id', 'like', $terms);
					}
					else
					{
						$res->where(DB::Raw('concat_ws(\' \',acd.firstname,acd.lastname)'), 'like', $terms)
								->Orwhere(DB::Raw('concat_ws(\' \',accd.firstname,accd.lastname)'), 'like', $terms);
					}
				}
				$res->orderBy('ft.ft_id','desc');
				
                $result = $res->get();

        array_walk($result, function(&$data)
        {
            if ($data->from_account_id == $this->config->get('constants.ACCOUNT.ADMIN_ID'))
            {
                $trans_type = '+';
                $data->username = $data->trans_to.'('.$data->tuname.')<br><span class="text-muted small">'.$data->to_acc_role;
            }
            else
            {
                $trans_type = '-';
                $data->username = $data->trans_from.'('.$data->funame.')<br><span class="text-muted small">'.$data->from_acc_role;
            }
            $data->added_by   = $data->added_by.'<br><span class="text-muted small">'.$data->added_by_role.'</span>';
            $data->statusCls  = config('dispclass.fund_trasnfer.status.'.$data->status_id);
            $data->status 	  = trans('admin/finance.fund_trasnfer.'.$data->status_id);
            $data->created_on = showUTZ($data->created_on, 'Y-m-d H:i:s');
            $data->amount 	  = CommonLib::currency_format($data->amount, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places], true, true);
            $data->handleamt  = CommonLib::currency_format($data->handleamt, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places], true, true);
            $data->paidamt 	  = $trans_type.' '.CommonLib::currency_format($data->paidamt, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places], true, true);
        });
        return $result;
    }

    public function getTransactionDetail (array $arr = array())
    {
        extract($arr);
        $trans = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as a')
                ->leftJoin($this->config->get('tables.ACCOUNT_MST').' as ra', 'ra.account_id', '=', 'a.account_id')
                ->leftJoin($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'a.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as ptl', function($ptl)
                {
                    $ptl->on('ptl.payment_type_id', '=', 'a.payment_type_id')
                    ->where('ptl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($join)
                {
                    $join->on('wt.wallet_id', '=', 'a.wallet_id');
                    $join->where('wt.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.STATEMENTLINES_LANG').' as e', function($join)
                {
                    $join->on('e.statementline_id', '=', 'a.statementline');
                    $join->where('e.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->where('a.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('a.status', '>', 0)
                ->where('a.transaction_id', $id)
                ->selectRaw('a.account_id,a.transaction_id,ra.uname,CONCAT(ad.first_name,\' \',ad.last_name) as full_name,a.created_on,w.wallet_code,a.remark,a.post_type,a.statementline as statementline_id,a.relation_id,a.transaction_type,a.transaction_id,cur.currency,cur.currency_symbol,cur.decimal_places,a.amount,a.tax,a.handleamt,a.paidamt,a.current_balance,e.statementline,wt.wallet,ptl.payment_type,a.status')
                ->first();
        if (!empty($trans))
        {
            $trans->created_on = showUTZ($trans->created_on);
            $trans->status = trans('general.transactions.status.'.$trans->status);
            $trans->amount = CommonLib::currency_format($trans->amount, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->tax = CommonLib::currency_format($trans->tax, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->handleamt = CommonLib::currency_format($trans->handleamt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->paidamt = CommonLib::currency_format($trans->paidamt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->current_balance = CommonLib::currency_format($trans->current_balance, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'decimal_places'=>$trans->decimal_places]);
            $trans->remark = json_decode($trans->remark);
            switch ($trans->statementline_id)
            {
                case $this->config->get('stline.SIGN_UP_BONUS.CREDIT'):
                    $details = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                            ->join($this->config->get('tables.DEVICES').' as d', 'd.device_id', '=', 'am.signup_device')
                            ->join($this->config->get('tables.ACCOUNT_SETTINGS').' as s', 's.account_id', '=', 'am.account_id')
                            ->leftJoin($this->config->get('tables.ACCOUNT_MST').' as ra', 'ra.account_id', '=', 'am.referred_account_id')
                            ->selectRaw('d.device_label,d.icon as device_icon,ra.uname as referrer_uname')
                            ->where('am.acccount_id', '=', $details->account_id)
                            ->first();
                    if ($details)
                    {
                        $trans->device_label = $details->device_label;
                        $trans->device_icon = $details->device_icon;
                        $trans->referrer_uname = !empty($details->referrer_uname) ? $details->referrer_uname : trans('general.label.self');
                    }
                    break;
                case $this->config->get('stline.REFERRAL_BONUS.CREDIT'):
                    $details = DB::table($this->config->get('tables.REFERRAL_EARNINGS').' as re')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as fam', 'fam.account_id', '=', 're.from_account_id')
                            ->join($this->config->get('tables.ACCOUNT_DETAILS').' as fad', 'fad.account_id', '=', 're.from_account_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as tam', 'tam.account_id', '=', 're.to_account_id')
                            ->join($this->config->get('tables.ACCOUNT_DETAILS').' as tad', 'tad.account_id', '=', 're.to_account_id')
                            ->join($this->config->get('tables.PROMOTIONAL_OFFERS').' as po', 'po.promo_offer_id', '=', 're.commission_type')
                            ->where('earning_id', $trans->relation_id)
                            ->selectRaw('re.commission_perc,fam.uname as from_user_name,CONCAT(fad.first_name,\' \',fad.last_name) as from_name,tam.uname as to_account_name,CONCAT(tad.first_name,\' \',tad.last_name) as to_name,po.offer_name')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->commission_perc = $details->commission_perc;
                        $trans->from_user_name = $details->from_user_name;
                        $trans->from_name = $details->from_name;
                        $trans->to_account_name = $details->to_account_name;
                        $trans->to_name = $details->to_name;
                        $trans->offer_name = $details->offer_name;
                    }
                    break;
                case $this->config->get('stline.REDEEM.DEBIT'):
                    $details = DB::table($this->config->get('tables.REDEEMS.').' as r')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'r.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.PAY').' as p', 'p.order_id', '=', 'mo.order_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'r.currency_id')
                            ->where('redeem_id', $trans->relation_id)
                            ->selectRaw('r.redeem_amount,mo.bill_amount,p.to_amount as amount_due,ms.store_code,ms.store_name,am.uname as staff_id,mm.mrcode,cur.currency,cur.decimal_places,cur.currency_symbol')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->mrcode = $details->mrcode;
                        $trans->bill_amount = CommonLib::currency_format($details->bill_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places], true, true);
                        $trans->redeem_amount = CommonLib::currency_format($details->redeem_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places], true, true);
                        $trans->amount_due = CommonLib::currency_format($details->amount_due, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places], true, true);
                    }
                    break;
                case $this->config->get('stline.CURRENCY_CONVERSION.DEBIT'):
                    break;
                case $this->config->get('stline.CURRENCY_CONVERSION.CREDIT'):
                    break;
                case $this->config->get('stline.FUND_TRANS_BY_SYSTEM.CREDIT'):
                    break;
                case $this->config->get('stline.FUND_TRANS_BY_SYSTEM.DEBIT'):
                    break;

                case $this->config->get('stline.ADD_FUND.CREDIT'):
                    break;
                case $this->config->get('stline.WITHDRAW.DEBIT'):
                    $details = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wm')
                            ->where('wd_id', $trans->relation_id)
                            ->selectRaw('account_info')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->account_info = json_decode($details->account_info);
                        if (!empty($trans->account_info))
                        {
                            array_walk($trans->account_info, function(&$a, $k)
                            {
                                $a = ['label'=>trans('withdrawal.account_details.'.$k), 'value'=>$a];
                            });
                        }
                    }
                    break;
                case $this->config->get('stline.CASHBACK.CREDIT'):
                    $details = DB::table($this->config->get('tables.CASHBACKS').' as c')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'c.currency_id')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->where('c.cashback_id', $trans->relation_id)
                            ->selectRaw('c.bill_amt,cur.currency,cur.decimal_places,cur.currency_symbol,mo.order_code,mm.mrcode,ms.store_code,ms.store_name,am.uname as staff_id')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->mrcode = $details->mrcode;
                        $trans->order_code = $details->order_code;
                        $trans->bill_amt = CommonLib::currency_format($details->bill_amt, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places]);
                    }
                    break;
                case $this->config->get('stline.ORDER_PAYMENT.DEBIT'):
                    $details = DB::table($this->config->get('tables.PAY').' as p')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->where('p.pay_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,mm.mrcode,mm.mrbusiness_name,ms.store_code,ms.store_name')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->order_code = $details->order_code;
                        $trans->mrcode = $details->mrcode;
                        $trans->mrbusiness_name = $details->mrbusiness_name;
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                    }
                    break;
                case $this->config->get('stline.ORDER_TIP.CREDIT'):
                    $details = DB::table($this->config->get('tables.MERCHANT_ORDERS').' as mo')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,mm.mrcode,mm.mrbusiness_name')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->order_code = $details->order_code;
                        $trans->mrcode = $details->mrcode;
                        $trans->mrbusiness_name = $details->mrbusiness_name;
                    }
                    break;
                case $this->config->get('stline.ORDER_PAYMENT.CREDIT'):
                    $details = DB::table($this->config->get('tables.PAY').' as p')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->where('p.pay_id', $trans->relation_id)
                            ->selectRaw('p.status as payment_status,mo.order_code,mm.mrcode,mm.mrbusiness_name,ms.store_code,ms.store_name,am.uname as staff_id')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->order_code = $details->order_code;
                        $trans->mrcode = $details->mrcode;
                        $trans->mrbusiness_name = $details->mrbusiness_name;
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->payment_status = trans('general.order.payment_status.'.$details->payment_status);
                    }
                    break;
                case $this->config->get('stline.ORDER_REFUND.DEBIT'):
                    $details = DB::table($this->config->get('tables.ORDER_REFUND').' as or')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'or.order_id')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'mo.currency_id')
                            ->where('or.or_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,or.amount as refund_amount,cur.currency,cur.decimal_places,cur.currency_symbol')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->refund_amount = CommonLib::currency_format($details->refund_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places, true, true]);
                    }
                    break;
                case $this->config->get('stline.ORDER_DEAL_PURCHASE.DEBIT'):
                    $details = DB::table($this->config->get('tables.MERCHANT_ORDERS').' as mo')
                            ->join($this->config->get('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                            ->leftjoin($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.PAYBACK_DEALS').' as d', 'd.pb_deal_id', '=', 'oi.pb_deal_id')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as dc', function($dc)
                            {
                                $dc->on('dc.bcategory_id', '=', 'd.bcategory_id')
                                ->where('dc.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,oi.voucher_code,d.deal_name,dc.bcategory_name,ms.store_name,ms.store_code')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->voucher_code = $details->voucher_code;
                        $trans->deal_name = $details->deal_name;
                        $trans->bcategory_name = $details->bcategory_name;
                        $trans->store_name = $details->store_name;
                        $trans->store_code = $details->store_code;
                    }
                    break;
                case $this->config->get('stline.ORDER_DEAL_PURCHASE.CREDIT'):
                    $details = DB::table($this->config->get('tables.MERCHANT_ORDERS').' as mo')
                            ->join($this->config->get('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.PAYBACK_DEALS').' as d', 'd.pb_deal_id', '=', 'oi.pb_deal_id')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as dc', function($dc)
                            {
                                $dc->on('dc.bcategory_id', '=', 'd.bcategory_id')
                                ->where('dc.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,oi.voucher_code,d.deal_name,dc.bcategory_name,ms.store_name,ms.store_code')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->voucher_code = $details->voucher_code;
                        $trans->deal_name = $details->deal_name;
                        $trans->bcategory_name = $details->bcategory_name;
                        $trans->store_name = $details->store_name;
                        $trans->store_code = $details->store_code;
                    }
                    break;
                case $this->config->get('stline.ORDER_DEAL_PURCHASE_TAX.DEBIT'):
                    break;
                case $this->config->get('stline.ORDER_PAYMENT_COMMISSION.DEBIT'):
                    $trans->commission_amount = $trans->amount;
                    break;
                default :
                    Log::error('Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
                    return abort(500, 'Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
            }
            $d = trans('transactions.'.$trans->statementline_id.'.fields.admin');
            $trans->remark = $trans->statementline.' ('.trans('transactions.'.$trans->statementline_id.'.remarks', (array) $trans->remark->data).')';
            if (is_array($d))
            {
                array_walk($d, function(&$v, $k) use($trans)
                {
                    $v = ['label'=>$v, 'value'=>$trans->{$k}];
                });
                if (isset($trans->account_info))
                {
                    $d = array_merge($d, (array) $trans->account_info);
                }
                return $d;
            }
            else
            {
                Log::error('Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
                return abort(500, 'Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
            }
        }
        return false;
    }

    public function online_payments (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as at')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acf', 'acf.account_id', '=', 'at.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'acf.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as pty', 'pty.payment_type_id', '=', 'at.payment_type_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'at.currency_id')
                ->select('at.id', 'acf.uname', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.payment_status', 'at.status as status_id', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id');
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('at.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('at.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($purpose) && !empty($purpose))
        {
            $qry->where('at.purpose', $purpose);
        }
        if (isset($status))
        {
            $qry->where('at.status', $status);
        }
        if (isset($terms) && !empty($terms))
        {
            if (is_numeric($terms))
            {
                $qry->where('at.id', $terms);
            }
            else
            {
                $qry->where(function($query) use ($terms)
                {
                    $query->where('acf.uname', 'LIKE', '%'.$terms.'%')
                            ->orwhere(DB::Raw('CONCAT_WS(\' \',acd.first_name,acd.last_name)'), 'like', '%'.$terms.'%')
                            ->orWhere('pty.payment_type', 'LIKE', '%'.$terms.'%');
                });
            }
        }
        if (!empty($count))
        {
            return $qry->count();
        }
        else
        {
            if (isset($start))
            {
                $qry->skip($start)
                        ->take($length);
            }
            $qry->orderBy('at.created_on', 'desc');
            $qry->orderBy('at.id', 'desc');
            $result = $qry->get();
            array_walk($result, function($payment)
            {
                $payment->statusCls = config('dispclass.payment_gateway_response.status.'.$payment->status_id);
                $payment->statusLbl = trans('db_trans.payment_gateway_response.status.'.$payment->status_id);
                $payment->payment_statusCls = config('dispclass.payment_gateway_response.payment_status.'.$payment->payment_status);
                $payment->payment_statusLbl = trans('db_trans.payment_gateway_response.payment_status.'.$payment->payment_status);
                $payment->created_on = showUTZ($payment->created_on, 'Y-m-d H:i:s');
                $payment->amount = CommonLib::currency_format($payment->amount, ['currency_symbol'=>$payment->currency_symbol, 'currency_code'=>$payment->currency_code, 'decimal_places'=>$payment->decimal_places], true, true);
                $payment->action = [];
                $payment->action['details'] = ['label'=>trans('admin/finance.details'), 'url'=>route('admin.finance.order-payments-details', ['id'=>$payment->id])];
                if ($payment->payment_status == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PAYMENT_STATUS.CONFIRMED') && $payment->status_id == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.PENDING'))
                {
                    $payment->action['paid'] = ['label'=>trans('admin/finance.payment-paid'), 'url'=>route('admin.finance.payment-paid', ['id'=>$payment->id])];
                    $payment->action['pay_confirm'] = ['label'=>trans('admin/finance.pay-confirm'), 'url'=>route('admin.finance.pay-confirm', ['id'=>$payment->id])];
                }
                if ($payment->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'))
                {
                    $payment->order_code = DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                                    ->where('order_type', $this->config->get('constants.ORDER.TYPE.IN_STORE'))
                                    ->where('order_id', $payment->relative_post_id)->value('order_code');
                    $payment->purpose = trans('admin/finance.purpose.'.$payment->purpose).' ('.$payment->order_code.')';
                }
                if ($payment->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
                {
                    $payment->order_code = DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                                    ->where('order_type', $this->config->get('constants.ORDER.TYPE.DEAL'))
                                    ->where('order_id', $payment->relative_post_id)->value('order_code');
                    $payment->purpose = trans('admin/finance.purpose.'.$payment->purpose).' ('.$payment->order_code.')';
                }
                if ($payment->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
                {
                    $payment->order_code = DB::table($this->config->get('tables.ADD_MONEY'))
                                    ->where('am_id', $payment->relative_post_id)->value('am_code');
                    $payment->purpose = trans('admin/finance.purpose.'.$payment->purpose).' ('.$payment->order_code.')';
                }
                if ($payment->payment_status == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PAYMENT_STATUS.CONFIRMED') && ($payment->status_id == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CANCELLED') || $payment->status_id == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.FAILED')))
                {
                    $payment->action['refund'] = ['label'=>trans('admin/finance.refund'), 'url'=>route('admin.finance.payment-refund', ['id'=>$payment->id]), 'data'=>['confirm'=>'Are you sure to refund this payment?']];
                }
                unset($payment->order_code);
            });
            return $result;
        }
    }

    public function getway_payment_details (array $arr = array())
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as at')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acf', 'acf.account_id', '=', 'at.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'acf.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as pty', 'pty.payment_type_id', '=', 'at.payment_type_id')
                ->join($this->config->get('tables.ACCOUNT_SETTINGS').' as as', 'as.account_id', '=', 'acf.account_id')
                ->join($this->config->get('tables.LOCATION_COUNTRIES').' as lc', 'lc.country_id', '=', 'as.country_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'at.currency_id')
                ->where('at.id', $id);
        $data = $qry;
        $res = $data->first();
        if (!empty($res) && $res->purpose != null)
        {
            if ($res->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'))
            {
                $qry->join($this->config->get('tables.PAY').' as py', 'py.pay_id', '=', 'at.relative_post_id')
                        ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'py.order_id')
                        ->join($this->config->get('tables.MERCHANT_STORE_MST').' as mmst', 'mmst.store_id', '=', 'mo.store_id')
                        ->join($this->config->get('tables.MERCHANT_MST').' as mst', 'mst.mrid', '=', 'mo.mrid')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mst.account_id')
                        ->leftjoin($this->config->get('tables.ADDRESS_MST').' as addr', 'addr.address_id', '=', 'mmst.address_id')
                        ->select('at.id', 'acf.uname', 'acf.mobile as user_mobile', 'acf.email as user_email', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status as status_id', 'at.payment_status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id', 'mo.order_code', 'mo.bill_amount as amt', 'mo.status as order_status', 'mo.status as order_payment_status', 'mmst.store_name', 'mmst.store_code', 'mmst.mobile as store_mobile', 'mmst.email as store_email', 'mst.mrlogo as merchant_logo', 'mst.mrbusiness_name', DB::raw('CONCAT_WS(\' \',ad.first_name,ad.last_name) as merchant_name'), 'at.released_date', 'at.approved_date', 'at.cancelled_date', 'at.refund_date', 'addr.formated_address', 'lc.phonecode');
            }
            else if ($res->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
            {
                $qry->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'at.relative_post_id')
                        ->leftjoin($this->config->get('tables.PAY').' as py', 'py.order_id', '=', 'mo.order_id')
                        ->join($this->config->get('tables.MERCHANT_STORE_MST').' as mmst', 'mmst.store_id', '=', 'mo.store_id')
                        ->join($this->config->get('tables.MERCHANT_MST').' as mst', 'mst.mrid', '=', 'mo.mrid')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mst.account_id')
                        ->leftjoin($this->config->get('tables.ADDRESS_MST').' as addr', 'addr.address_id', '=', 'mmst.address_id')
                        ->select('at.id', 'acf.uname', 'acf.mobile as user_mobile', 'acf.email as user_email', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status as status_id', 'at.payment_status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id', 'mo.order_code', 'mo.bill_amount as amt', 'mo.status as order_status', 'mo.status as order_payment_status', 'mmst.store_name', 'mmst.store_code', 'mmst.mobile as store_mobile', 'mmst.email as store_email', 'mst.mrlogo as merchant_logo', 'mst.mrbusiness_name', DB::raw('CONCAT_WS(\' \',ad.first_name,ad.last_name) as merchant_name'), 'at.released_date', 'at.approved_date', 'at.cancelled_date', 'at.refund_date', 'addr.formated_address', 'lc.phonecode');
            }
            else if ($res->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
            {
                $qry->join($this->config->get('tables.ADD_MONEY').' as am', function($j)
                {
                    $j->on('am.am_id', '=', 'at.relative_post_id');
                });
                $qry->select('at.id', 'acf.uname', 'acf.mobile as user_mobile', 'acf.email as user_email', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status as status_id', 'at.payment_status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id', 'am.am_code as order_code', 'am.amount as amt', 'am.status as order_status', 'at.released_date', 'at.approved_date', 'at.cancelled_date', 'at.refund_date', 'lc.phonecode');
            }
            $result = $qry->first();
            if (!empty($result))
            {
                //$result->user_mobile = $result->user_mobile.' '.$result->user_mobile;
                if ($result->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY') || $result->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
                {
                    $result->description = trans('admin/finance.purpose.'.$result->purpose).' ('.$result->order_code.')';
                }
                if ($result->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
                {
                    $result->order_code = DB::table($this->config->get('tables.ADD_MONEY'))
                                    ->where('am_id', $result->relative_post_id)->value('am_code');
                    $result->description = trans('admin/finance.purpose.'.$result->purpose).' ('.$result->order_code.')';
                }
                $result->statusCls = config('dispclass.payment_gateway_response.status.'.$result->status_id);
                $result->statusLbl = trans('db_trans.payment_gateway_response.status.'.$result->status_id);
                $result->payment_statusCls = config('dispclass.payment_gateway_response.payment_status.'.$result->payment_status);
                $result->payment_statusLbl = trans('db_trans.payment_gateway_response.payment_status.'.$result->payment_status);
                $result->merchant_logo = !empty($result->merchant_logo) ? asset($this->config->get('constants.MERCHANT.LOGO_PATH.WEB').$result->merchant_logo) : asset($this->config->get('constants.MERCHANT.LOGO_PATH.DEFAULT'));
                $result->famt = CommonLib::currency_format($result->amt, ['currency_symbol'=>$result->currency_symbol, 'currency_code'=>$result->currency_code, 'decimal_places'=>$result->decimal_places], true, true);
                $result->famount = CommonLib::currency_format($result->amount, ['currency_symbol'=>$result->currency_symbol, 'currency_code'=>$result->currency_code, 'decimal_places'=>$result->decimal_places], true, true);
                return $result;
            }
        }
        return null;
    }

    public function update_payment_status (array $arr)
    {
        extract($arr);
        return DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $id)
                        ->update(['status'=>$this->config->get('constants.ON')]);
    }

    public function refundPayment (array $arr)
    {
        extract($arr);
        $payment_details = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as pg')
                ->join($this->config->get('tables.ACCOUNT_SETTINGS').' as ast', 'ast.account_id', '=', 'pg.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'pg.currency_id')
                ->where('pg.id', $id)
                ->where('pg.payment_status', $this->config->get('constants.ON'))
                ->whereIn('pg.status', [$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CANCELLED'), $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.FAILED')])
                ->select('pg.purpose', 'pg.relative_post_id', 'pg.currency_id as pg_currency', 'pg.amount', 'pg.id', 'ast.currency_id as user_currency', 'pg.account_id', 'c.code')
                ->first();
        if (!empty($payment_details))
        {
            if (($payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE') || $payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY')))
            {
                $qry = DB::table($this->config->get('tables.PAY').' as p')
                        ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                        ->where('p.pay_id', $payment_details->relative_post_id)
                        ->where('p.status', '!=', $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND'))
                        ->select('p.pay_id', 'mo.order_id')
                        ->first();
            }
            if (!empty($qry))
            {
                $exRate = $this->exchangeRate($payment_details->pg_currency, $payment_details->user_currency);
                if ($payment_details->pg_currency != $payment_details->user_currency)
                {
                    $amount = $payment_details->amount * $exRate;
                }
                else
                {
                    $amount = $payment_details->amount;
                    $exRate = 1;
                }
                $trans_id = $this->updateAccountTransaction([
                    'to_account_id'=>$payment_details->account_id,
                    'currency_id'=>$payment_details->user_currency,
                    'wallet_id'=>$this->config->get('constants.WALLET.xpc'),
                    'amt'=>$amount,
                    'relation_id'=>$payment_details->relative_post_id,
                    'transaction_for'=>'REFUND', 	
                    'credit_remark_data'=>['amount'=>CommonLib::currency_format($amount, $payment_details->user_currency, true, true), 'currency'=>$payment_details->code, 'rate'=>$exRate],
                    'debit_remark_data'=>['amount'=>$amount, 'currency'=>$payment_details->code, 'rate'=>$exRate]
                        ], false, true);
//                $trans['from_account_id'] = 1;
//                $trans['to_account_id'] = $payment_details->account_id;
//                $trans['account_id'] = $payment_details->account_id;
//                $trans['currency_id'] = $payment_details->user_currency;
//                $trans['wallet_id'] = $this->config->get('constants.WALLET.xpc');
//                $trans['transaction_type'] = $this->config->get('constants.TRANS_TYPE.CREDIT');
//                $trans['amount'] = $amount;
//                $trans['paidamt'] = $amount;
//                $trans['transaction_id'] = $this->generateTransactionID();
//                $trans['created_on'] = getGTZ('Y-m-d');
//                $trans['status'] = $this->config->get('constants.ON');
//                $trans['statementline'] = $this->config->get('stline.REFUND.CREDIT');
//                $trans['remark'] = trans('transactions.REFUND.CREDIT', ['amount'=>$amount, 'currency'=>$payment_details->code, 'rate'=>$exRate]);
//                $trans_id = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
//                        ->insertGetId($trans);
                if (!empty($trans_id))
                {
//                    $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
//                            ->where('account_id', $payment_details->account_id)
//                            ->where('currency_id', $payment_details->user_currency)
//                            ->first();
//                    $update['current_balance'] = $balance->current_balance + $amount;
//                    $update['tot_credit'] = $balance->tot_credit + $amount;
//                    DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
//                            ->where('id', $trans_id)
//                            ->update(['current_balance'=>$update['current_balance']]);
                    $updateResponce['status'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND');
                    $updateResponce['refund_date'] = getGTZ();
                    DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $payment_details->id)
                            ->update($updateResponce);
                    DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                            ->where('order_id', $qry->order_id)
                            ->update(['payment_status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND')]);
                    DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $qry->pay_id)
                            ->update(['status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND')]);
//                    $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
//                            ->where('account_id', $payment_details->account_id)
//                            ->where('currency_id', $payment_details->user_currency)
//                            ->update($update);
                    return $balance;
                }
            }
        }
        return false;
    }

    public function confirmPayment (array $arr)
    {
        extract($arr);
        $payment_details = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                ->where('id', $id)
                ->where('status', $this->config->get('constants.OFF'))
                ->first();
        if (!empty($payment_details))
        {
            if (($payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE')) || ($payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY')))
            {
                $qry = DB::table($this->config->get('tables.PAY').' as p')
                        ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                        ->where('p.pay_id', $payment_details->relative_post_id)
                        ->where('p.status', $this->config->get('constants.OFF'))
                        ->select('p.pay_id', 'mo.order_id')
                        ->first();
                if (!empty($qry))
                {
                    $updateResponce['status'] = $this->config->get('constants.ON');
                    $updateResponce['refund_date'] = getGTZ();
                    DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $payment_details->id)
                            ->update($updateResponce);
                    DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                            ->where('order_id', $qry->order_id)
                            ->update(['status'=>$this->config->get('constants.ON')]);
                    DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $qry->pay_id)
                            ->update(['status'=>$this->config->get('constants.ON')]);
                    return true;
                }
            }
        }
        return false;
    }

    public function wallet_balance (array $arr = [])
    {
        extract($arr);				
        $qry = DB::table($this->config->get('tables.WALLET').' as w')                
				->leftJoin($this->config->get('tables.ACCOUNT_BALANCE').' as ub', function($join) use($account_id,$currency_id)
                {
                    $join->on('ub.wallet_id', '=', 'w.wallet_id')
						->where('ub.currency_id', '=', $currency_id)
						->where('ub.account_id', '=', $account_id);
                })
                ->leftJoin($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'w.wallet_id')
						->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
				->leftJoin($this->config->get('tables.CURRENCIES').' as cur', function($join) use ($currency_id){
						$join->on('cur.currency_id', '=', 'ub.currency_id')
							->orWhere('cur.currency_id', '=', $currency_id);
				});         
		 
        if (isset($wallet_code) && $wallet_code > 0)
        {
            $qry->where('w.wallet_code', $wallet_code);
        }		
        if (isset($currency_id) && $currency_id > 0)
        {
            $qry->where('cur.currency_id', $currency_id);
        }
		
        if (isset($wallet) && !empty($wallet))
        {
			if(is_int($wallet)){
				$qry->where('w.wallet_id', $wallet);
			} 
			else if(is_array($wallet)){
				$qry->whereIn('w.wallet_id', $wallet);
			}
        }
        $qry->selectRaw('w.wallet_id,IF(ub.current_balance IS NOT NULL,ub.current_balance,0) as current_balance,cur.currency_id,IF(ub.tot_credit IS NOT NULL,ub.tot_credit,0) as tot_credit,IF(ub.tot_debit IS NOT NULL,ub.tot_debit,0) as tot_debit,w.wallet_code,wl.wallet as wallet_desc,wl.terms as wallet_terms,cur.currency as currency_code,cur.currency_symbol,cur.decimal_places,wl.wallet,0 as pending_balance');
        $qry->orderby('w.sort_order', 'asc');
        $result = $qry->get();
        return $result;
    }

    public function get_roles ()
    {
        return DB::table($this->config->get('tables.ACCOUNT_TYPES'))
		                ->where('is_system_user','=',0)
                        ->select('account_type_name', 'id')
						 ->get();

        /* return DB::table($this->config->get('tables.ACCOUNT_TYPES'))
                        ->where('has_wallet', 1)
                        ->select('account_type_name as system_role_name', 'id as system_role_id')->get();
 */
    }
	
	public function get_affiliate_wallets(){
		$afwalletSetting = $this->commonstObj->getSettings('affiliate_wallets');
		
		$res = DB::table($this->config->get('tables.WALLET').' AS w')
					->join($this->config->get('tables.WALLET_LANG').' AS wl',function($join){
						$join->on('wl.wallet_id','=','w.wallet_id')
							->where('wl.lang_id','=',$this->config->get('locale_id'));
					})
					->where('w.status', $this->config->get('constants.ON'))
					->whereIn('w.wallet_id',json_decode($afwalletSetting))
					->select('wl.wallet_id','w.wallet_code','wl.wallet')
					->get();
		return $res;
	}
   public function get_wallet_list()
          {
            $result = DB::table($this->config->get('tables.WALLET').' AS w')
					   ->join($this->config->get('tables.WALLET_LANG').' AS wl',function($join){
						   $join->on('wl.wallet_id','=','w.wallet_id');
					 })
					->where('w.status','=',$this->config->get('constants.ON'))
					->where('w.is_aff_wallet','=',$this->config->get('constants.ON'))
					 ->select('wl.wallet_id','w.wallet_code','wl.wallet') 
					->get();
            
        return(!empty($result) && count($result) > 0) ? $result : false;
     }
	  public function getWalletBalnceTotal ($arr = array(), $count = false)
		   {
			
			extract($arr);
			$wallet = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
			                   ->join($this->config->get('tables.ACCOUNT_DETAILS').' as us', 'us.account_id', '=', 'um.account_id')
							   ->join($this->config->get('tables.ACCOUNT_BALANCE').' as ab', 'ab.account_id', '=', 'um.account_id')
							   ->join($this->config->get('tables.WALLET').' as w', function($join){
									$join->on('w.wallet_id', '=', 'ab.wallet_id')
									->where('w.is_aff_wallet','=',$this->config->get('constants.ON'));
							   })
					           ->join($this->config->get('tables.WALLET_LANG').' AS wl',function($join){
							           $join->on('wl.wallet_id','=','w.wallet_id');
						         })
			                   ->join($this->config->get('tables.CURRENCIES').' as c', 'ab.currency_id', '=', 'c.currency_id')							  
							   ->where('um.is_affiliate','=',$this->config->get('constants.ON'))			
							   ->orwhere('um.account_type_id','=',4)
					->select(DB::raw('ab.tot_credit,um.email,concat_ws(" ",us.firstname,us.lastname) as full_name,ab.tot_debit,ab.current_balance,ab.account_id,wl.wallet,w.wallet_id,um.uname as username,c.currency as currency_code,c.currency_id,c.currency_symbol,c.decimal_places'));
			
			if (isset($uname) && $uname)
			{
				/* 	$wallet->where(DB::Raw('concat_ws(" ",us.firstname,us.lastname)'),'like',$uname); */
				     $wallet->where('um.uname','like','%'.$uname.'%'); 
			}
			if(isset($currency) && $currency)
			{
				 $wallet->where('c.currency_id',$currency);
			}
			if (isset($ewallet_id) && $ewallet_id)
			{
				
				 $wallet->where('a.wallet_id',$ewallet_id);
			}
				  if (isset($length) && !empty($length))
					{
						$wallet->skip($start)->take($length);
					}
					if (isset($count) && !empty($count))
					{
						return $wallet->count();
					}
					else
					{
						$result= $wallet->orderBy('um.account_id', 'ASC') 
									   ->get();
			if(!empty($result)) {
					array_walk($result, function(&$data)
					{
						
					$data->tot_credit =  CommonLib::currency_format($data->tot_credit, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code, 'decimal_places'=>$data->decimal_places]);
					$data->tot_debit =  CommonLib::currency_format($data->tot_debit, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code, 'decimal_places'=>$data->decimal_places]);
					$data->current_balance =  CommonLib::currency_format($data->current_balance, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code, 'decimal_places'=>$data->decimal_places]);
					});
					   return $result;
					}
			 return false;
		}
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
                        ->select(DB::raw('c.city_id,c.city as city_name,d.district_id,d.district as district_name,s.state_id,(select GROUP_CONCAT(state_id) from '.config('tables.LOCATION_STATE').'  where is_union_territory = 1 and linked_state_id = d.state_id and status = 1 GROUP BY linked_state_id) as  union_territory_id, s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region'))
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
	
	public function add_fund_franchisee(array $arr = array())
    {
		$this->frObj = new Franchisee();
		$fund_details = $arr;
        $user_details = $this->frObj->get_franchisee_details($arr);
		$op = [];
        
        if (!empty($user_details))
        {
			
			if (($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT')) && (!empty($bal)) && ($arr['amount'] > $bal->current_balance))
			{
				return ['status'=>$this->config->get('httperr.UN_PROCESSABLE'),'msg'=>"Insufficient Balance"];
			}
			else {
				$accId = $user_details->account_id;
				$franchisee_details = $this->fr_benefits($accId);
				//print_R($franchisee_details);exit;
			
				/*if(!empty($fr_commissions)){
					$purchase_commission  = $commission_paidamt = ($arr['amount'] / 100) * $franchisee_details->wallet_purchase_per;
				}*/
				if(empty($franchisee_details)){				 
					 return [
						'status'=> 422,
						'msg'=> "Please check franchisee access locatons"
					 ];
				}
				$fund['added_by'] 		= $arr['admin_id'];
				$fund['transaction_id'] = \AppService::getTransID($franchisee_details->account_id);
				
				$fund['currency_id'] 	= $arr['currency_id'];
				$fund['amount'] 		= $arr['amount'];
				$fund['paidamt'] 		= $arr['amount'];
				$fund['handleamt']  	= 0;
				if($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
				{
					$trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
					$fund['from_account_ewallet_id'] = $arr['wallet'];
					$fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
					$fund['to_account_ewallet_id'] = $arr['wallet'];
					$fund['to_account_id'] = $accId;
				}
				else
				{
					$trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
					$fund['from_account_ewallet_id'] = $arr['wallet'];
					$fund['from_account_id'] = $accId;
					$fund['to_account_ewallet_id'] = $arr['wallet'];
					$fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
				}
				//$fund['transfer_type'] = $trasn_type;
				$fund['created_on']    = getGTZ();
				$fund['transfered_on'] = getGTZ();
				$fund['status'] 	   = $this->config->get('constants.PAYMENT_STATUS.PENDING');
				$fund_id 			   = DB::table($this->config->get('tables.FUND_TRANASFER'))
											->insertGetId($fund);
				if(!empty($fund_id))
				{
					$fft_relation_id = $fund_id;
					$update_trans = false;
					if ($arr['type'] == $this->config->get('constants.CREDIT'))
					{	
						$update_trans = $this->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'],'credit_remark_data'=>['amount'=>$arr['amount']], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
					}
					elseif ($arr['type'] == $this->config->get('constants.DEBIT'))
					{
						$update_trans = $this->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM','debit_remark_data'=>['amount'=>$arr['amount']]], true, false);
					}
					if (!empty($update_trans))
					{
						$fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
											->where('ft_id','=',$fund_id)
											->update(['status'=>$this->config->get('constants.PAYMENT_STATUS.CONFIRMED')]);
											
						$op['status'] = $this->config->get('httperr.SUCCESS');
						
						if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
						{
							$op['msg'] = trans('admin/finance.fund_transfer_success');
						}
						else
						{
							$op['msg'] = trans('admin/finance.fund_transfer_debit_success');
						}
						/* share commission to uplines */
						
						if ($franchisee_details->level > 1)
						{
							$com_details_data['account_id'] = $franchisee_details->account_id;
							$middle_level_franchisees = $this->get_middle_level_franchisees(
									array(
										'to_franchisee_details'=>$franchisee_details,
										'to_account_id'=>$franchisee_details->account_id));
									
							if(!empty($middle_level_franchisees))
							{
								$currency_code = $this->commonObj->get_currency_code($fund_details['currency_id']);
								$downline_franchisee_per = $franchisee_details->wallet_purchase_per;
							
								foreach ($middle_level_franchisees as $franchisee)
								{
									$per   =  $franchisee->wallet_purchase_per - $downline_franchisee_per;
									$downline_franchisee_per = $franchisee->wallet_purchase_per;
									if ($fund_id && $per > 0)
									{
										$amount = $arr['amount'];
										$franchisee_commission = 0;
										$franchisee_commission = ($per/100) * ($amount);
										$franchisee_current_balance = $franchisee_tds_per = $tds_total_commission = $frans_tds_amount = $frans_tds_relation_id = 0;
										 /* $tds_details = $this->tds_balance_update(
												array(
													'account_id'=>$franchisee->account_id,
													'franchisee_commission'=>$franchisee_commission,
													'currency_id'=>$fund_details['currency_id'],
													'country'=>$franchisee->country));  */
										//$to_user_transaction_id = $this->admincommonObj->generateTransactionID($franchisee_details->account_id);
										
										$current_date 	= getGTZ();
										$tot_tax 		= 0;
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
												'commission_type'=>config('constants.FRANCHISEE_COMMISSION_ADMIN_FUND_TRANS_SC'),
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
												$com_details_data['fr_com_id']  = $relation_id;
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
											$remark = ['percentage'=>$per,'amount'=>number_format($amount, $decimal_places, '.', ','),'to'=>$franchisee_details->uname,'franchisee_type'=>$franchisee_details->franchisee_type_name,'currency'=>$currency_code];
											$status = DB::table(config('tables.ACCOUNT_TRANSACTION'))
													->insertGetId(
																array(
																'account_id'=>$franchisee->account_id,
																'to_account_id'=>$franchisee->account_id,
																'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),
																'statementline_id'=>config('stline.FRANCHISEE_COMMISSION_CREDIT'),
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
											if ($status)
											{
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
												
												/************* Notification  code *****************/
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
											if ($status && !empty($tds_details['service_tax_per']))
											{
												/* $update_balance4 = $this->update_franchisee_balance(
														array(
															'account_id'=>$franchisee->account_id,
															'amount'=>$tot_tax,
															'currency_id'=>$fund_details['currency_id'],
															'transaction_type'=>config('constants.DEBIT'),
															'ewallet_id'=>$fund_details['ewallet_id']));
												$decimal_places = $this->commonObj->decimal_places($tot_tax);
												$remark = $tds_details['service_tax_per'].'% of '.number_format($tot_tax, $decimal_places, '.', ',').' '.$currency_code;
												$status = DB::table(config('tables.ACCOUNT_TRANSACTION'))
														->insertGetId(
														array(
															'account_id'=>$franchisee->account_id,
															'payment_type_id'=>1,
															'statementline_id'=>76,
															'amt'=>$tot_tax,
															'paid_amt'=>$tot_tax,
															'handle_amt'=>0,
															'tax'=>0,
															'wallet_id'=>$fund_details['ewallet_id'],
															'currency_id'=>$fund_details['currency_id'],
															'transaction_type'=>config('constants.DEBIT'),
															'remark'=>json_encode(['data'=>$remark]),
															'from_account_id'=>$franchisee_details->account_id,
															'ip_address'=>$ip = \Request::getClientIp(true),
															'transaction_id'=>$transaction_id = \AppService::getTransID($franchisee_details->account_id),
															'current_balance'=>$update_balance4['current_balance'],
															'status'=>1,
															'relation_id'=>$relation_id
												)); */
											}
										}
									}
								}
							}
						}
					}
					else
					{
						$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = "Your request could not be processed. Please contact our customer support";
					}					
				}
				else {
					$op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = "Your request could not be processed. Please contact our customer support";
				}
			}
        }
        else
        {
            $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = 'Account Not found';
        }
		return $op;
    }
	
	public function fr_benefits($account_id){
		$qry =  DB::table(config('tables.FRANCHISEE_MST').' as mst')
				->join(config('tables.FRANCHISEE_BENIFITS').' as b','b.franchisee_type','=','mst.franchisee_type')
				->join(config('tables.ACCOUNT_MST').' as ac','ac.account_id','=','mst.account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','mst.account_id')
				->join(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'mst.account_id')
				->join(config('tables.FRANCHISEE_LOOKUP').' as l','l.franchisee_typeid','=','mst.franchisee_type')
				->where('mst.account_id',$account_id)
				->select('mst.account_id','b.diff_commission_per','b.flexible_commission_per','b.wallet_purchase_per','b.charity_donation_per','b.fb_id','l.level','ac.uname','b.franchisee_type','l.franchisee_type as franchisee_type_name',DB::raw("CONCAT_WS(' ',ad.firstname,ad.lastname) as fullname"),
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
	
	/* public function tds_balance_update ($arrData)
    {
        extract($arrData);
        $franchisee_tds_per = $tds_total_commission = $frans_tds_amount = $frans_tds_relation_id = $total_commission = 0;
     	
		list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json) = $this->getTax(['account_id'=>$account_id,'amount'=>$franchisee_commission,'country_id'=>$country,'statementline_id'=>75]);	
				
				$tds_data['service_tax_details'] = $tax_json;
				$tds_data['service_tax_per'] = $tot_tax_perc;
				$tds_data['service_tax'] = $tot_tax;	
				$tds_data['tax_class_id'] = $tax_class_id;	
				
				
				
        $current_date 		= date('Y-m-d');
        $date 				= explode('-', $current_date);
        $current_year 		= $date[0];
        $current_month 		= $date[1];
        $start_month 		= 4;
        $end_month 			= 3;
        $data['transferred_on'] = date('Y-m-d H:i:s');
        $data['currency_id'] 	= $currency_id;
        $data['account_id'] 		= $account_id;
        $data['tds_tot_com_credit'] = $data['tds_tot_com_debit'] = $data['tds_current_com'] = 0;
        if ($date [1] >= 4)
        {
            $start_date 	= $current_year.'-04-01';
            $end_date 		= ($current_year + 1).'-03-31';
        }
		if ($current_month <= 3)
        {
			$start_date 	= ($current_year - 1).'-04-01';
            $end_date 		= $current_year.'-03-31';
        }
        $result = DB::table(config('tables.FRANCHISEE_TDS_LOOKUP'))
                ->where('account_id', $account_id)
                ->where('currency_id', $currency_id)
                ->whereRaw("Date(transferred_on) BETWEEN '".$start_date."' AND '".$end_date."'")
                ->first();
			if($tds_data && !empty($franchisee_commission))
			{
				$tds_arr 		= json_decode(stripslashes(implode(',',$tds_data)), true);
				$country 		= $country;
				if (array_key_exists($country, $tds_arr))
				{
					$tds_min_amount = $tds_arr['min_amount'];
					//$commission_details = $this->get_franchisee_total_commission($franchisee->user_id);
					if ($result)
					{
						$franchisee_tds_per 		= $tds_arr[$country];
						$data['tds_tot_com_credit'] = $total_commission = $result->tds_tot_com_credit + $franchisee_commission;
						$tds_total_commission 		= $total_commission - $result->tds_tot_com_debit;
						$data['tds_tot_com_debit'] 	= $result->tds_tot_com_debit;
						$data['tds_current_com'] 	= $result->tds_current_com + $franchisee_commission;
					}
					else
					{
						$franchisee_tds_per 		= $tds_arr[$country];
						$data['tds_tot_com_credit'] = $total_commission = $tds_total_commission = $franchisee_commission;
						$data['tds_current_com'] 	= $total_commission;
						$data['tds_tot_com_debit']  = 0;
					}
				}
			}
			if (!empty($franchisee_tds_per) && $total_commission > $tds_min_amount)
			{
				$frans_tds_amount 			= $tds_total_commission * ($franchisee_tds_per / 100);
				$data['tds_tot_com_debit']  = $data['tds_tot_com_debit'] + $tds_total_commission;
				$data['tds_current_com'] 	= $data['tds_tot_com_credit'] - $data['tds_tot_com_debit'];
				$frans_tds_relation_id 		= DB::table(config('tables.FRANCHISEE_TDS_TRANSACTION'))
						->insertGetId(
								array(
									'account_id'=>$account_id,
									'total_commission'=>$tds_total_commission,
									'currency_id'=>$currency_id,
									'tds_per'=>$franchisee_tds_per,
									'tds'=>$frans_tds_amount,
									'transferred_on'=>getGTZ()
								));
			}
			else
			{
				$franchisee_tds_per = 0;
			}
        if($result)
        {
            DB::table(config('tables.FRANCHISEE_TDS_LOOKUP'))
                    ->where('ftds_id', $result->ftds_id)
                    ->update($data);
        }
        else
        {
            DB::table(config('tables.FRANCHISEE_TDS_LOOKUP'))
                    ->insert($data);
        }
        $data['tds_total_commission'] = $tds_total_commission;
        $data['franchisee_tds_per'] = $franchisee_tds_per;
        $data['frans_tds_amount'] = $frans_tds_amount;
        return $data;
    } */
	
	public function addFranchiseeCommissionDetails ($arr = array())
    {
        $country_id = $state_id = $district_id = $region_id = $city_id = null;
        extract($arr);
		$data = compact('country_id', 'state_id', 'district_id', 'region_id', 'city_id');
       
           //$userdetails 	 = $this->get_userdetails_byid($user_id);
            //$userdetails	 = $this->get_access_locations($arr);
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
	
	public function get_access_locations($arr){
		extract($arr);
		return DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION'),' as al')
				->where('account_id',$account_id)
				->where('relation_id',$fr_com_id)
				->where('status',1)
				->first();
				
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
	
	public function PaymentGateway_transcation(array $arr = array(), $count = false){
        extract($arr);
        $wQry2 = DB::Table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs');
		
			   $wQry2->join($this->config->get('tables.ACCOUNT_MST').' as am','am.account_id', '=', 'trs.account_id');
               
               $wQry2->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'trs.account_id');
				
                $wQry2->join($this->config->get('tables.STATEMENT_LINE').' as st', function($join)
                {
                    $join->on('st.statementline_id', '=', 'trs.statementline_id');
                });
                $wQry2->leftJoin($this->config->get('tables.WALLET_LANG').' as b', function($join)
                {
                    $join->on('b.wallet_id', '=', 'trs.wallet_id');
                    $join->where('b.lang_id', '=', $this->config->get('app.locale_id'));
                });
                $wQry2->leftJoin($this->config->get('tables.PAYMENT_TYPES').' as c', function($join)
                {
                    $join->on('c.payment_type_id', '=', 'trs.payment_type_id');
                });
                $wQry2->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'trs.currency_id');
				
                $wQry2->select(DB::Raw('trs.id,CONCAT_WS(\' \',acd.firstname,acd.lastname) as fullname,am.uname,am.email,am.mobile,trs.statementline_id,trs.account_id,trs.created_on,trs.transaction_id,trs.amt as amount,trs.handle_amt,trs.tax,trs.paid_amt,trs.transaction_type,trs.current_balance,st.statementline,trs.remark,trs.wallet_id,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,b.wallet,c.payment_type'));
                       $wQry2->Where('trs.is_deleted','=',$this->config->get('constants.OFF'));
					   $wQry2->Where('trs.payment_type_id','<>',$this->config->get('constants.PAYMENT_TYPES.WALLET'));
					   
               if(isset($payment_type_id) && !empty($payment_type_id)){
				   
				   $wQry2->Where('trs.payment_type_id','=',$payment_type_id);
			    }
			  if(isset($type) && !empty($type)){
				  
					 if($type=='affiliate'){
					        $wQry2->Where('am.is_affiliate','=',$this->config->get('constants.ON'));
					 }
					else if($type=='franchise'){
					       $wQry2->Where('am.account_type_id','=',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'));
					}
				}
		     if (isset($search_text) && !empty($search_text))
				{ 
					if(!empty($filterchk) && !empty($filterchk))
					{   
						$search_text='%'.$search_text.'%'; 
						$search_field=['UserName'=>'am.uname','FullName'=>'concat_ws(" ",acd.firstname,acd.lastname)','Email'=>'am.email','Mobile'=>'am.mobile'];
						$wQry2->where(function($sub) use($filterchk,$search_text,$search_field){
							foreach($filterchk as $search)
							{   
								if(array_key_exists($search,$search_field)){
								  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
								} 
							}
						});
			      }
		 	 }
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
            if (isset($currency_id) && !empty($currency_id))
            {
                $wQry2->where("trs.currency_id", $currency_id);
            }
            if (isset($orderby) && isset($order)) {
                $wQry2->orderBy($orderby, $order);
            }
            else {
                $wQry2->orderBy('trs.id', 'DESC');
            }
            if (isset($length) && !empty($length)) {
                $wQry2->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
                return $wQry2->count();
            }
           $transactions = $wQry2->get();
			  
            if ($transactions){
					array_walk($transactions, function(&$t)	{
					 if($t->created_on ==''){
						  $t->created_on='';
					  }
				      else{
						  $t->created_on= date('d-M-Y H:i:s', strtotime($t->created_on));
					 }
				        if(!empty($t->remark) && strpos($t->remark, '}') > 0) {
							$t->order_code    = (isset($ordDetails->data->order_code)) ? $ordDetails->data->order_code : '';                
							$t->remark 		  = $ordDetails = json_decode(stripslashes($t->remark));
							$t->statementline = trans('transactions.'.$t->statementline_id.'.user.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark'])));
							$t->remark 		  = trans('transactions.'.$t->statementline_id.'.user.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
						}
						else {
							$t->remark = $t->statementline;
						}	
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
						$t->Fcurrent_balance = $t->currency_symbol.' '.number_format($t->current_balance, \AppService::decimal_places($t->current_balance), '.', ',').' '.$t->currency_code;
						unset($t->statementline);
					});
					return !empty($transactions) ? $transactions : [];					
				    } 
    }
	
	/* public function update_account_balance ($arr = array())
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
    } */
 public function get_payment_types(){
	 
	$qry = DB::Table($this->config->get('tables.PAYMENT_TYPES').' as pay')
	        ->Where('pay.payment_type_id','<>',$this->config->get('constants.PAYMENT_TYPES.WALLET'))
			->Where('pay.status','=',$this->config->get('constants.ON'))
			->select('pay.payment_type_id','pay.payment_type')
			->get();
		if(!empty($qry)){
			return $qry;
		}
   }
}
