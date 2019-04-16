<?php

namespace App\Http\Controllers\Franchisee;

use Config;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\FrModel;

class LangController extends FrBaseController
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
		$msgSec = '';
        switch ($langKey)
        {
           case 'change-pwd':
                $msgSec =  '
				var $val_message  = {
					    oldpassword: "'.trans('franchisee/settings/change_pwd_js.old_pwd').'",
						newpassword: {
							required: "'.trans('franchisee/settings/change_pwd_js.new_pwd').'",
							minlength: "'.trans('franchisee/settings/change_pwd_js.minlength').'"
						},
						confirmpassword: {
							required: "'.trans('franchisee/settings/change_pwd_js.confirm_new_pwd').'",
						    equalTo:  "'.trans('franchisee/settings/change_pwd_js.entr_same_value').'"
					   }
				};
				

				var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
				var $processing="'.trans('affiliate/general.processing_txt').'";
				var $update="'.trans('affiliate/general.update_now').'"';
                break;
            case 'profile':
                $msgSec =  '
			var $email_validate_message  = {
				 email: {
                		required: "'.trans('franchisee/validator/change_email_js.email').'",
						email:"'.trans('franchisee/validator/change_email_js.invalid_email').'",
						maxlength:"'.trans('franchisee/validator/change_email_js.max_length').'"
                  },
				  tpin:{
					 required: "'.trans('franchisee/validator/change_email_js.tpin_not_empty').'",
					 minlength: "'.trans('franchisee/validator/change_email_js.minlengt').'",					
					 maxlength: "'.trans('franchisee/validator/change_email_js.maxlengt').'"
				  },
				  verify_code: {
                		required: "'.trans('franchisee/validator/change_email_js.verify_code').'",
						digits:"'.trans('franchisee/validator/change_email_js.numeric').'",
						minlength: "'.trans('franchisee/validator/change_email_js.minlength').'",					
						maxlength: "'.trans('franchisee/validator/change_email_js.maxlength').'"						
                 },
			};
			var $email_validate_otp_message  = {
				 verify_code: {
                		required: "'.trans('franchisee/validator/change_email_js.verify_code').'",
						digits:"'.trans('franchisee/validator/change_email_js.numeric').'",
						minlength: "'.trans('franchisee/validator/change_email_js.minlength').'",					
						maxlength: "'.trans('franchisee/validator/change_email_js.maxlength').'"						
                 },

			};
			var $mobile_validate_message  = {
				 mobile: {
                		required: "'.trans('franchisee/validator/change_mobile_js.mobile').'",
						number:"'.trans('franchisee/validator/change_mobile_js.invalid_mobile').'",
						minlength: "'.trans('franchisee/validator/change_mobile_js.min_length').'",					
						maxlength: "'.trans('franchisee/validator/change_mobile_js.max_length').'"	
                 },
				 tpin:{
					 required: "'.trans('franchisee/validator/change_email_js.tpin_not_empty').'",
					 minlength: "'.trans('franchisee/validator/change_email_js.minlengt').'",					
					 maxlength: "'.trans('franchisee/validator/change_email_js.maxlengt').'"
				 },
              verify_code: {
                		required: "'.trans('affiliate/validator/change_email_js.verify_code').'",
						digits:"'.trans('affiliate/validator/change_email_js.numeric').'",
						minlength: "'.trans('affiliate/validator/change_email_js.minlength').'",					
						maxlength: "'.trans('affiliate/validator/change_email_js.maxlength').'"						
                 },
			};
			var $verify_code_validate_message = {
				verify_code: {
                		required: "'.trans('franchisee/validator/change_email_js.verify_code').'",
						digits:"'.trans('franchisee/validator/change_email_js.numeric').'",
						minlength: "'.trans('franchisee/validator/change_email_js.minlength').'",					
						maxlength: "'.trans('franchisee/validator/change_email_js.maxlength').'"						
                 },
			};
			var $edit = "'.trans('affiliate/general.edit').'";
			var $cancel_edit = "'.trans('affiliate/general.cancel_edit').'";
			var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			var $processing="'.trans('affiliate/general.processing_txt').'";
			var $update="'.trans('affiliate/general.update_txt').'";
			';
            break;
			case 'change-mobile':
                $msgSec =  '
			var $val_message  = {
				 mobile: {
                		required: "'.trans('affiliate/validator/change_mobile_js.mobile').'",
						number:"'.trans('affiliate/validator/change_mobile_js.invalid_mobile').'",
						minlength: "'.trans('affiliate/validator/change_mobile_js.min_length').'",					
						maxlength: "'.trans('affiliate/validator/change_mobile_js.max_length').'"	
                 },
				 verify_code: {
                		required: "'.trans('affiliate/validator/change_mobile_js.verify_code').'",
						digits:"'.trans('affiliate/validator/change_mobile_js.numeric').'",
						minlength: "'.trans('affiliate/validator/change_mobile_js.minlength').'",					
						maxlength: "'.trans('affiliate/validator/change_mobile_js.maxlength').'"						
                 },

			};
			var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			var $processing="'.trans('affiliate/general.processing_txt').'";
			var $update="'.trans('affiliate/general.update_txt').'";
			';
            break;	
			case 'new-ticket':
                $msgSec =  '
			var $ticket_val_message  = {
				 ticket_category_id: {
                		required: "'.trans('affiliate/support/support.select_category').'",
                 },
				 ticket_priority_id: {
                		required: "'.trans('affiliate/support/support.select_priority').'",
                 },
				 ticket_subject: {
                		required: "'.trans('affiliate/support/support.enter_subject').'",
                 },
				 ticket_message: {
                		required: "'.trans('affiliate/support/support.enter_message').'",
                 },
				 file_attachment: {
                		required: "'.trans('affiliate/support/support.file_attachment').'",
                 },
			};
			var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			var $file_format = "'.trans('affiliate/support/support.valid_file_format').'";
			var $search_term_alert = "'.trans('affiliate/support/support.search_term_alert').'";
			';
            break;
			case 'ticket_rating':
                $msgSec =  '
			var $rating_val_message  = {
				 comment: {
                		required: "'.trans('affiliate/support/support.comment').'",
                 },
				 rating: {
                		required: "'.trans('affiliate/support/support.rating').'",
                 },
			};
			var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			';
            break;	
			case 'ticket_replay':
                $msgSec =  '
			var $replay_val_message  = {
				 replay_comments: {
                		required: "'.trans('affiliate/support/support.replay_comments').'",
                 },
				 file_attachment: {
                		required: "'.trans('affiliate/support/support.file_attachment').'",
                 },
			};
			var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			';
            break;	
			case 'update_profile_image':
                $msgSec =  '
			var $select_fileto_upload = "'.trans('affiliate/account/profile.select_fileto_upload').'";
			var $image_dimension_incorrect = "'.trans('affiliate/account/profile.image_dimension_incorrect').'";
			var $file_size_high = "'.trans('affiliate/account/profile.file_size_high').'";
			var $valid_image_file = "'.trans('affiliate/account/profile.valid_image_file').'";
			var $remove_prof_image = "'.trans('affiliate/account/profile.remove_prof_image').'";
			var $choose_image = "'.trans('affiliate/account/profile.choose_image').'";
			var $something_wrong = "'.trans('affiliate/general.something_wrong').'";
			';
                break;
            case 'change-pin':
                $msgSec =  '
			var $chgpin_val_message  = {
				tran_oldpassword: "'.trans('franchisee/settings/security_pwd_js.tran_oldpassword').'",
				tran_newpassword: {
			 		required: "'.trans('franchisee/settings/security_pwd_js.tran_newpassword').'",
					minlength: "'.trans('franchisee/settings/security_pwd_js.minlength').'",
					maxlength: "'.trans('franchisee/settings/security_pwd_js.maxlength').'",
				},
				tran_confirmpassword: {
					required: "'.trans('franchisee/settings/security_pwd_js.tran_confirmpassword').'",
					equalTo: "'.trans('franchisee/settings/security_pwd_js.different_sec_pwd').'",
				}
			};
			var $createpin_val_message  = {
                new_security_pin: {
			 		required: "'.trans('affiliate/settings/security_pwd_js.security_pin').'",
					minlength: "'.trans('affiliate/settings/security_pwd_js.minlength').'",
					maxlength: "'.trans('affiliate/settings/security_pwd_js.maxlength').'",
				},
				confirm_security_pin: {
					required: "'.trans('affiliate/settings/security_pwd_js.tran_confirmpassword').'",
					equalTo: "'.trans('franchisee/settings/security_pwd_js.different_sec_pwd').'",
					
				}
			};
			var $savepin_val_message  = {				
                tran_newpassword: {
					required: "'.trans('affiliate/settings/security_pwd_js.tran_newpassword').'",
					minlength: "'.trans('affiliate/settings/security_pwd_js.minlength').'",
					maxlength: "'.trans('affiliate/settings/security_pwd_js.maxlength').'",
				},
				tran_confirmpassword: {
					required: "'.trans('affiliate/settings/security_pwd_js.tran_confirmpassword').'",
					equalTo: "'.trans('franchisee/settings/security_pwd_js.different_sec_pwd').'",
				}
			};
			var $different_sec_pwd = "'.trans('affiliate/settings/security_pwd_js.different_sec_pwd').'";
            var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			var $processing="'.trans('affiliate/general.processing_txt').'";
			var $update="'.trans('affiliate/general.update_txt').'"
			var $changeotp_val_message  = {
				otp: "'.trans('franchisee/settings/security_pwd_js.otp_req').'",
				minlength: "'.trans('franchisee/settings/security_pwd_js.otpminlen').'",
			};
            var $wrong_msg = "'.trans('affiliate/general.something_wrong').'";
			var $processing="'.trans('affiliate/general.processing_txt').'";
			var $update="'.trans('affiliate/general.update_txt').'"

			';
            break;
		    case 'fund_transfer':
                $msgSec =  '
			var $val_message  = {
			wallet_id: "'.trans('franchisee/wallet/fundtransfer_js.select_wallet').'",
            to_account:  "'.trans('franchisee/wallet/fundtransfer_js.to_uname').'",
            totamount:  "'.trans('franchisee/wallet/fundtransfer_js.enter_transfer_amt').'",
			};
			var $curr_sel = "'.trans('franchisee/wallet/fundtransfer_js.select_currency').'";
            var $wrong_msg = "'.trans('general.something_wrong').'";
			var $transfer_fund_completed="'.trans('franchisee/wallet/fundtransfer.transfer_fund_completed').'";
			var $processing="'.trans('general.processing_txt').'";
            var $cant_transfer_amt = "'.trans('franchisee/wallet/fundtransfer_js.cant_transfer_amt').'";
            var $get_tac_code = "'.trans('franchisee/wallet/fundtransfer_js.get_tac_code').'";
			var $min_transfer_amt = "'.trans('franchisee/wallet/fundtransfer_js.min_transfer_amt').' ";
			var $enter_amt = "'.trans('franchisee/wallet/fundtransfer_js.enter_amt').'";
			var $tac_code = { tac_code : "'.trans('franchisee/wallet/fundtransfer_js.enter_tac_code').'"}
			';
                break;	
				
			case 'payout_settings':
                $msgSec =  '
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
            break;	
			
			case 'user-verification' :
                $msgSec =  '
				var $document_msg  ={
					verify_file: {
					  required: "'.trans('affiliate/kyc/file_valid_msg').'"				 
					}
				};
				var $photo_id_msg = {
					document_types: {
						required: "'.trans('affiliate/kyc/doc_type_valid_msg').'"								
					},
					verify_file: {
						required: "'.trans('affiliate/kyc/file_valid_msg').'"					    
					}
				};
				var $address_proof_msg = {
				    document_types: {
					    required: "'.trans('affiliate/kyc/doc_type_valid_msg').'"						      
				    },
					verify_file: {
					   required: "'.trans('affiliate/kyc/file_valid_msg').'"
					}
				};

				var $wrong_msg = "'.trans('general.something_wrong').'"; 
			    var $processing= "'.trans('general.processing_txt').'"; 
			    var $security_pin_error = "'.trans('banktransfer_settings_js.security_pin_error').'"; 
				var $submit_btn = "'.trans('affiliate/kyc/submit_btn_txt').'"; 
			    var $success_msg = "'.trans('banktransfer_settings_js.success_txt').'";
			   ';
            break;
			
		}
		return response($msgSec)
            ->header('Content-Type', 'application/javascript');
	}
}