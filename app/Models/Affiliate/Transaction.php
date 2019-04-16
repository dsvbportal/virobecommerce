<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Transaction extends BaseModel
{
	 public function __construct ()
    {
         parent::__construct(); 
    }
	
	
	public function add_fund_member (array $arr = array())
    {
        $user_details = $this->memberObj->get_member_details($arr);
        $bal = $this->get_account_bal($arr);

        if (($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT')) && (!empty($bal)) && ($arr['amount'] > $bal->current_balance))
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
                    $update_trans = $this->baseobj->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM','debit_remark_data'=>['amount'=>$arr['amount']]], true, false);
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
	    
}