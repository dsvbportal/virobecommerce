<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Payouts extends BaseModel
{
	public function __construct ()
    {
         parent::__construct(); 
		 $affObj = new AffModel();  
		 $this->applang = 'en';
    }
	
	public function GetBankAccountDetails ($arr)
    {	    
		extract($arr);
		$payment_settings = DB::table($this->config->get('tables.ACCOUNT_PAYOUT_SETTINGS'))
				->where('account_id', '=', $account_id)
				->where('is_deleted', '=', 0)
				->value('payment_settings');
				
		if ($payment_settings) {
			return json_decode($payment_settings);
		} else {
			return false;
		}
    }
	
}