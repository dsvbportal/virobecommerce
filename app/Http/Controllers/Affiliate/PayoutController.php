<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\Payouts;
use App\Models\Affiliate\Payments;
use App\Models\Affiliate\AffModel;
use App\Helpers\CommonNotifSettings;

class PayoutController extends AffBaseController
{
	private $packageObj = '';
    public function __construct ()
    {
        parent::__construct();
		$this->paymentObj = new Payments;
		$this->payoutsObj = new Payouts;	
    }
	
	public function bank_tranfer_settings(){
		$data['account_id'] = $this->account_id;	
		$data['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');		
		$data['fields'] = CommonNotifSettings::getHTMLValidation('affiliate.withdrawal.bank-details');			
		$data['bank_account_details'] = $this->payoutsObj->GetBankAccountDetails($data);		
		return view('affiliate.withdrawal.bank_transfer_settings', $data);
	}
	
}