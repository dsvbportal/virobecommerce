<?php
namespace App\Models\Franchisee;
use App\Models\BaseModel;
use App\Models\Commonsettings;
use DB;
use AppService;

class Bonus extends BaseModel
{
	public function __construct ()
    {
         parent::__construct();
		 $this->walletObj = new WalletModel;		 
		 $this->commonObj = new Commonsettings();
    }
	public function credit_bonus($arr){
		 extract($arr);
		 $usrbal_upres = $this->walletObj->update_user_balance(array('payment_type'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$wallet, 'account_id'=>$account_id, 'currency_id'=>$currency_id, 'amount'=>$netpay, 'transaction_type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'),'return'=>'return'));					
    		$transaction_id = AppService::getTransID($account_id);
			$trans = [];
			$trans['account_id'] = $account_id;
			$trans['to_account_id'] = $account_id;
			$trans['statementline_id'] = $statementline_id; 
			$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
			$trans['relation_id'] = $fr_com_id;
			$trans['amt'] = $amount;
			$trans['tax'] = $tax;
			$trans['handle_amt'] = $ngoAmt;
			$trans['paid_amt'] = $netpay;
			$trans['currency_id'] = $currency_id;
			$trans['wallet_id'] = $wallet;
			$trans['transaction_id'] = $transaction_id;
			$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
		    $trans['remark'] = addSlashes(json_encode(['data'=>$remark]));
			$trans['created_on'] = getGTZ();
			$trans['current_balance'] = $usrbal_upres->current_balance;
			$trans['status'] = $this->config->get('constants.ACTIVE');
			$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
					->insertGetId($trans);
			return $transResID>0? $transResID : false;
	}
	

}