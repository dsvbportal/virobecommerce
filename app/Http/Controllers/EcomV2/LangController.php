<?php

namespace App\Http\Controllers\ecom;

use Config;
//use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
//use App\Models\frontend\AffModel;

class LangController extends BaseController
{
    public function __construct ()
    {
		parent::__construct();
        if ($this->request->has('lang'))
        {
            $this->session->put('applang', $this->request->get('lang'));
        }
        $this->config->get('app.locale', $this->session->get('applang'));
    }

    public function login_user ()
    {
        echo 'loaded';
        exit;
    }

    public function langLoad ($langKey = '')
    {
		$langKey = str_replace('.js','',$langKey);		
        switch ($langKey)
        {
		 case 'login':
                echo '
				var $login_val_message  = {					
						username: {
							required: "'.trans('ecom/login.username_req').'",							
						},
						password: {
							required: "'.trans('ecom/login.password_req').'"
						}
				};	
				var $wrong_msg="'.trans('affiliate/general.something_wrong').'"';
                break;
               case 'change_mobile':
                echo '
				var $mobile_same="'.trans('ecom/account.old_and_new_same').'"';
                break;
            case 'cart_details':
                echo '
				var $cart_list_not_avalable="'.trans('ecom/product.cart_list_not_available').'"';
                break;
            case 'order_list':
                echo '
				var $order_list_not_avalable="'.trans('ecom/account.order_list_not_available').'"';
                break;
								
			/* case 'payout_settings':
                echo '
			   var $update="'.trans('affiliate/settings/general.update_processing').'";
			   var $characters="'.trans('affiliate/settings/payout_settings.only_characters').'";
			   var $characters_number="'.trans('affiliate/settings/payout_settings.character_number').'";
			   var $digits="'.trans('affiliate/settings/payout_settings.u_digits').'";
			   var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			   var $update_now= "'.trans('affiliate/settings/payout_settings.update_now').'";
			   var $updated= "'.trans('affiliate/settings/payout_settings.updated').'";
			   var $added= "'.trans('affiliate/settings/payout_settings.added').'";
			   var $success= "'.trans('affiliate/settings/payout_settings.success').'";
			   var $ecsp = "'.trans('affiliate/general.enter_correct_security_password').'";

			
			 var $cashfree_Payment  = {
			currency_id: "'.trans('affiliate/settings/payout_settings.select_currency').'",
            cashfree_account_id: {
                  required: "'.trans('affiliate/settings/payout_settings.cashfree_account_id').'",
            },
            account_name: {
                 required: "'.trans('affiliate/settings/payout_settings.account_holder_name').'",
                alpha: "'.trans('affiliate/settings/payout_settings.alphabets').'",
            },
            status: "'.trans('affiliate/settings/payout_settings.slct_status').'",
            tpin:"'.trans('affiliate/settings/payout_settings.security_pin_tbin').'"};
			
			var $paytm_Payment  = {
			currency_id: "'.trans('affiliate/settings/payout_settings.select_currency').'",
            paytm_account_id: {
                  required: "'.trans('affiliate/settings/payout_settings.paytm_account_id').'",
			},
            account_name: {
                 required: "'.trans('affiliate/settings/payout_settings.account_holder_name').'",
                alpha: "'.trans('affiliate/settings/payout_settings.alphabets').'",
            },
            status: "'.trans('affiliate/settings/payout_settings.slct_status').'",
            tpin:"'.trans('affiliate/settings/payout_settings.security_pin_tbin').'"};
			  
		   
		   var $bank_Payment  = {
			currency_id: "'.trans('affiliate/settings/payout_settings.currency').'",
			bank_account_type: "'.trans('affiliate/settings/payout_settings.bank_account_type').'",
			nick_name: "'.trans('affiliate/settings/payout_settings.nick_name').'",
			account_name: {
                required: "'.trans('affiliate/settings/payout_settings.account_holder_name').'",
                alpha: "'.trans('affiliate/settings/payout_settings.alphabets').'",
            },
			 account_no: {
                required: "'.trans('affiliate/settings/payout_settings.account_number').'",
                number: "'.trans('affiliate/settings/payout_settings.account_number').'",

            },
			bank_name:  "'.trans('affiliate/settings/payout_settings.bank_name').'",
			bank_branch: "'.trans('affiliate/settings/payout_settings.bank_branch').'",
			ifsccode:"'.trans('affiliate/settings/payout_settings.ifsc_code_length').'",
			ifsccode:"'.trans('affiliate/settings/payout_settings.ifsc_code').'",
			status: "'.trans('affiliate/settings/payout_settings.slct_status').'",
            tpin:"'.trans('affiliate/settings/payout_settings.security_pin_tbin').'",
			}; ';
            break;	 */		
		}
	}
}