<?php
Route::get('/', function () {
    return view('static.index');
});
Route::get('login', ['as'=>'login', 'uses'=>'AffiliateController@login']);
Route::post('checklogin', ['as'=>'checklogin', 'uses'=>'AffAuthcontroller@login_check']);
Route::post('forgotpwd', ['as'=>'forgotpwd', 'middleware'=>['validate'],'uses'=>'AffAuthcontroller@forgotpwd']);
Route::get('resetpwd-link/{token}', ['as'=>'pwdreset-link', 'uses'=>'AffAuthcontroller@verifyForgotpwdLink']);
Route::post('resetpwd-save', ['as'=>'pwdreset-save', 'uses'=>'AffAuthcontroller@update_newpwd']);
Route::post('signup/account-check', ['as'=>'signup.accheck', 'uses'=>'AffAuthcontroller@signup_accheck']);
Route::post('signup/account-verify', ['as'=>'signup.acverify', 'uses'=>'AffAuthcontroller@signup_acverify']);
Route::post('signup-save', ['as'=>'signup.save', 'middleware'=>['validate'], 'uses'=>'AffiliateController@save_account']);
Route::post('signup-upgrade', ['as'=>'signup.acupgrade', 'middleware'=>['validate'], 'uses'=>'AffiliateController@account_upgrade']);
Route::get('signup/activation/{token}', ['as'=>'signup.activation', 'middleware'=>['validate'], 'uses'=>'AffiliateController@signup_activation']);
Route::post('logout', ['as'=>'logout', 'middleware'=>['affauth'], 'uses'=>'AffAuthcontroller@logout']);
Route::get('recovery-pwd', ['as'=>'recoverpwd', 'uses'=>'AffAuthcontroller@recoverpwd']);
Route::get('validate/lang/{langkey}', 'LangController@langLoad');

Route::get('signup/email/verify/{vcode}', ['as'=>'signup.verifyemail', 'middleware'=>['validate'], 'uses'=>'AffiliateController@signup_email_verify'])->where('vcode', "([A-z0-9]{32})?");
Route::post('geo/state', ['as'=>'state','uses'=>'CommonController@getState']);
Route::post('geo/district', ['as'=>'district','uses'=>'CommonController@getDistrict']);

Route::group(['prefix'=>'payment-gateway-response', 'middleware'=>'checkPaymentGateWayDomain'], function()
{
	Route::post('datafeed/{payment_type}/{id?}', ['as'=>'datafeed', 'uses'=>'PaymentGatewayController@dataFeed']);
});
include('payment_gateway.php');

Route::group(['middleware'=>['affauth', 'validate']], function()
{
   Route::get('dashboard', ['as'=>'dashboard', 'uses'=>'DashboardController@dashboard']);
   /* Profile */
    Route::group(['prefix'=>'profile'], function()
    {
        Route::get('/', ['as'=>'profile', 'uses'=>'AffiliateController@myprofile']);
		Route::get('bank-info', ['as'=>'profile.bank-info', 'uses'=>'AffiliateController@myprofile']);
		Route::get('contacts', ['as'=>'profile.affiliate-details', 'uses'=>'AffiliateController@myprofile']);
		
        Route::post('update', ['as'=>'profile.update', 'uses'=>'AffiliateController@updateProfile']);		
        Route::post('profileimage_save', ['as'=>'profile.profileimage_save', 'uses'=>'AffiliateController@profileimage_withcrop_save']);
        Route::get('remove_profile_image', ['as'=>'profile.remove_profile_image', 'uses'=>'AffiliateController@remove_profile_image']);
        Route::post('tempimg_upload', ['as'=>'profile.tempimg_upload', 'uses'=>'AffiliateController@tempimg_upload']);
        Route::get('kyc', ['as'=>'profile.kyc', 'uses'=>'AffiliateController@kyc']);
        Route::match(['get', 'post'], 'kyc-upload', ['as'=>'profile.kyc_upload', 'uses'=>'AffiliateController@kyc_upload']);
		Route::get('address/{type}', ['as'=>'profile.address', 'uses'=>'AffiliateController@account_address']);
		Route::post('address/{type}', ['as'=>'profile.address.save', 'uses'=>'AffiliateController@account_address_save']);		
		
    });
	
    /* Settings */
    Route::group(['prefix'=>'settings'], function()
    {	
		Route::get('nominee', ['as'=>'settings.nominee', 'uses'=>'AffiliateController@nominee']);
		Route::post('nominee/save', ['as'=>'settings.nominee.save','middleware'=>['validate'], 'uses'=>'AffiliateController@saveNominee']);
		/* change display name */
		Route::post('change-uname', ['as'=>'settings.change_uname', 'uses'=>'SettingsController@change_uname']);
        /* change email */
		Route::get('change-email', ['as'=>'settings.change_email', 'uses'=>'SettingsController@change_email']);
		Route::post('verify-change-email', ['as'=>'settings.verify-change-email', 'uses'=>'SettingsController@verify_change_email']);		
		Route::post('change-mobile', ['as'=>'settings.change_mobile', 'uses'=>'SettingsController@sendUpdate_mobileVerification']);
		
       /* change_password */
        Route::match(['get', 'post'], 'change-pwd', ['as'=>'settings.change_pwd', 'uses'=>'SettingsController@change_pwd']);
        Route::match(['get', 'post'], 'password_check', ['as'=>'settings.password_check', 'uses'=>'SettingsController@password_check']);
        Route::match(['post'], 'update_password', ['as'=>'settings.updatepwd', 'uses'=>'SettingsController@updatepwd']);
       
	   /* change_security password */
        Route::get('security-pin', ['as'=>'settings.securitypin', 'uses'=>'SettingsController@securitypin']);
	    Route::post('security-pin/create', ['as'=>'settings.securitypin.create', 'uses'=>'SettingsController@securitypin_create']);
        Route::post('security-pin/verify', ['as'=>'settings.securitypin.verify', 'uses'=>'SettingsController@securitypin_verify']);
        Route::post('security-pin/reset', ['as'=>'settings.securitypin.reset', 'uses'=>'SettingsController@securitypin_reset']);
        Route::post('security-pin/save', ['as'=>'settings.securitypin.save', 'uses'=>'SettingsController@securitypin_save']);
        Route::post('forgot_security_pin', ['as'=>'settings.forgot_security_pin', 'uses'=>'SettingsController@forgot_security_pwd']);
        Route::post('securitypin-resetotp', ['as'=>'settings.securitypin.forgototp.verify', 'uses'=>'SettingsController@securitypin_forgototp_check']);
        Route::match(['get', 'post'], 'reset_security_pwd/{activation_key}', ['as'=>'settings.reset_security_pwd', 'uses'=>'SettingsController@reset_security_pwd']);
        Route::match(['get', 'post'], 'reset_update_pwd', ['as'=>'settings.reset_update_pwd', 'uses'=>'SettingsController@updatesecuritypwd']);

		/* verify_email */
        Route::post('email/verification/send', ['as'=>'settings.email.verify', 'uses'=>'SettingsController@verifyEmail_Send']);
		Route::get('email/verification', ['as'=>'settings.email.verify-link', 'uses'=>'SettingsController@verifyEmail_link']);
		
		/* verify_mobile */
        Route::post('mobile/verification/send', ['as'=>'settings.mobile.verify', 'uses'=>'SettingsController@verifyMobile_Sendotp']);
		Route::post('mobile/verification', ['as'=>'settings.mobile.verifyotp', 'uses'=>'SettingsController@verifyMobile_otp']);
       
	   /* change_email */
        Route::post('change-email/verification/send', ['as'=>'settings.emailverification', 'uses'=>'SettingsController@sendEmailVerification']);
		Route::get('change-email/verification/{token}', ['as'=>'settings.changeemail.verification', 'uses'=>'SettingsController@verifylink_change_email']);
		Route::post('change-email/send-otp', ['as'=>'settings.changeemail.send_otp', 'uses'=>'SettingsController@sendEmailVerificationOTP']);
		Route::post('change-email/verify-otp', ['as'=>'settings.changeemail.verification_otp', 'uses'=>'SettingsController@verify_change_email_otp']);
        
        Route::match(['post'], 'send-update-mobile-verification', ['as'=>'settings.updatemobileverification', 'uses'=>'SettingsController@sendUpdate_mobileVerification']);
        Route::match(['get', 'post'], 'update-mobile', ['as'=>'settings.update_mobile', 'uses'=>'SettingsController@update_mobile']);
        Route::get('profile-info', ['as'=>'settings.profile_info', 'uses'=>'SettingsController@profile_info']);
        Route::post('update-profile', ['as'=>'settings.update_profile', 'middleware'=>['validate'], 'uses'=>'SettingsController@update_profile']);
			
		/* payouts */
		Route::get('personal-info', ['as'=>'settings.personal', 'uses'=>'PayoutController@getPersonalInfo']);		
		Route::get('{type}-address/', ['as'=>'settings.address', 'uses'=>'AffiliateController@getAddress']);
		Route::post('{type}-address/save', ['as'=>'settings.address.save', 'uses'=>'AffiliateController@saveAddress']);		
		
		/* Bank details */
		Route::post('get_bank_details', ['as'=>'settings.bank-info', 'uses'=>'SettingsController@get_bank_details']);
	    Route::post('get-ifsc-details', ['as'=>'settings.get-ifsc-details', 'uses'=>'SettingsController@Get_Ifsc_Bank_Details']);
	    Route::post('bank-details', ['as'=>'settings.bank-details', 'uses'=>'SettingsController@Bank_Details']);
		
		/* Kyc Upload */
		Route::post('kyc-document-upload', ['as'=>'settings.kyc_document_upload', 'uses'=>'SettingsController@kycDocumentUpload']);

	   /* Change mobile */
		  Route::post('change-mobile/verification/send', ['as'=>'settings.mobileverification', 'uses'=>'SettingsController@sendMobileVerification']);
	      Route::get('change-mobile/verification/{token}', ['as'=>'settings.changemobile.verification', 'uses'=>'SettingsController@verifylink_change_mobile']);
		  Route::post('change-mobile/send-otp', ['as'=>'settings.changemobile.send_otp', 'uses'=>'SettingsController@sendMobileVerificationOTP']);
		 Route::post('change-mobile/verify-otp', ['as'=>'settings.changemobile.verification_otp', 'uses'=>'SettingsController@verify_change_mobile_otp']);
		 Route::post('update_contacts',['as'=>'settings.update_contacts', 'uses'=>'SettingsController@update_contacts']); 
    });

    /* Package */
    Route::group(['prefix'=>'package'], function()
    {
        Route::match(['get', 'post'], 'browse', ['as'=>'package.browse', 'uses'=>'PackageController@packages_browse']);
        Route::post('purchase/paymodes', ['as'=>'package.paymodes','middleware'=>['validate'],'uses'=>'PackageController@paymode_select']);
        Route::post('purchase/paymodes/{type}/info', ['as'=>'package.paymodeinfo', 'uses'=>'PackageController@paymode_select'])->where(array('paymode'=>'[0-9]+'));
        Route::post('purchase/{paymode}/confirm', ['as'=>'package.purchaseconfirm', 'uses'=>'PackageController@purchase_confirm']);
        Route::post('activate', ['as'=>'package.activate', 'uses'=>'PackageController@purchase_activate']);
        Route::get('refund/{code}', ['as'=>'package.refund', 'uses'=>'PackageController@packageRefund']);
        //Route::match(['get', 'post'], 'my-packages', ['as'=>'package.my_packages', 'uses'=>'PackageController@my_packages']);
        Route::match(['get', 'post'], 'my-packages', ['as'=>'package.my_packages', 'uses'=>'PackageController@my_packages']);
		Route::match(['get', 'post'], 'purchase-history', ['as'=>'package.purchase-history', 'uses'=>'PackageController@upgrade_history']);
    });
    /* Refferals */
    Route::group(['prefix'=>'referrals'], function()
    {
		//Route::match(['get','post'],'my-directs', ['as'=>'referrals.my_directs','uses'=>'ReferralsController@my_directs']);
        Route::match(['get', 'post'], 'my-referred-customers', ['as'=>'referrals.my_referred_customers', 'uses'=>'ReferralsController@my_referred_customers']);
        Route::match(['get', 'post'], 'my-team', ['as'=>'referrals.myteam', 'uses'=>'ReferralsController@my_team_report']);
        Route::match(['get', 'post'], 'my-directs', ['as'=>'referrals.mydirects', 'uses'=>'ReferralsController@my_referrals']);
        Route::match(['get', 'post'], 'my-genealogy', ['as'=>'referrals.my_geneology', 'uses'=>'ReferralsController@my_geneology']);
		Route::match(['get', 'post'], 'rank', ['as'=>'referrals.rank', 'uses'=>'ReferralsController@rank']);
        Route::post('genealogy/search', ['as'=>'referrals.geneology.search', 'uses'=>'ReferralsController@my_geneology']);
        Route::any('refer-and-earn', ['as'=>'referrals.refer-and-earn', 'uses'=>'ReferralsController@refer_and_earn']);
    });

    /* Wallet */
    Route::group(['prefix'=>'wallet'], function()
    {
        Route::match(['get'], '/', ['as'=>'wallet.balance', 'uses'=>'WalletController@my_wallet']);
        /* fund_transfer */
        Route::match(['get', 'post'], 'fund-transfer', ['as'=>'wallet.fundtransfer', 'uses'=>'TranferController@fundtransfer']);
        Route::match(['get', 'post'], 'searchacc', ['as'=>'wallet.fundtransfer.usrsearch', 'uses'=>'TranferController@searchacc']);
        Route::match(['get', 'post'], 'fund_transfer_confirm', ['as'=>'wallet.fund_transfer_confirm', 'uses'=>'TranferController@fund_transfer_to_account_confirm']);
        Route::match(['get', 'post'], 'get_tac_code', ['as'=>'wallet.fundtransfer.get_tac_code', 'uses'=>'TranferController@get_tac_code']);		
		Route::match(['get', 'post'], 'send_tac_code', ['as'=>'wallet.fundtransfer.get_tac_code', 'uses'=>'TranferController@get_tac_code']);		
		Route::match(['get', 'post'], 'fund_transfer_save', ['as'=>'wallet.fund_transfer_save', 'uses'=>'TranferController@fund_transfer_to_account']);
        /* fund_transferhistory */
        Route::match(['get', 'post'], 'fund-transfer/history', ['as'=>'wallet.fundtransfer.history', 'uses'=>'TranferController@fundtransfer_history']);
        Route::match(['get', 'post'], 'transactions', ['as'=>'wallet.transactions', 'uses'=>'WalletController@transactions']);
        Route::match(['get', 'post'], 'wallet_balance', ['as'=>'wallet.wallet_balance', 'uses'=>'WalletController@wallet_balance']);
    });
	
	Route::group(['prefix'=>'addfund'], function()
    {
		 Route::get('/', ['as'=>'addfund', 'uses'=>'WalletController@my_wallet']);
	});
	
    /* Withdrawl */
    Route::group(['prefix'=>'withdrawal'], function()
    {
	 	Route::match(['get', 'post'], '/', ['as'=>'withdrawal.history', 'uses'=>'WithdrawalController@Withdrawals_history']); 
		Route::match(['get', 'post'], 'create', ['as'=>'withdrawal.create', 'uses'=>'WithdrawalController@new_withdrawal']);
        Route::match(['get', 'post'], 'payout', ['as'=>'withdrawal.payouts', 'uses'=>'WithdrawalController@payoutTypesList']);
        Route::match(['get', 'post'], 'payout-details', ['as'=>'withdrawal.payout-details', 'uses'=>'WithdrawalController@payoutDetails']);
        Route::match(['get', 'post'], 'save', ['as'=>'withdrawal.save', 'uses'=>'WithdrawalController@saveWithdraw']);
		Route::match(['get', 'post'], 'make', ['as'=>'withdrawal.requst', 'uses'=>'WithdrawalController@withdrawal_request']);
		Route::post('save_withdrawal', ['as'=>'withdrawal.save_withdrawal', 'uses'=>'WithdrawalController@save_withdrawal']);
		Route::post('payout_withdraw_settings', ['as'=>'withdrawal.payout_withdraw_settings', 'uses'=>'WithdrawalController@payout_withdrawal_settings']);
		Route::match(['get','post'],'cancel_request', ['as'=>'withdrawal.cancel_request', 'uses'=>'WithdrawalController@cancel_withdrawal_request']);
		Route::match(['get','post'],'details/{trans_id?}', ['as'=>'withdrawal.details', 'uses'=>'WithdrawalController@withdrawal_details']);
		
	   /* Route::match(['get', 'post'], '{status}/list', ['as'=>'withdrawal.list', 'uses'=>'WithdrawalController@withdrawal_list']);*/
	    
    });
//Fast Start Bonus | Team Commission | Leadership Bonus | Car Bonus | Star Bonus
    Route::group(['prefix'=>'affiliate-commission'], function()
    {
        Route::match(['get', 'post'], 'fast-start-bonus', ['as'=>'reports.fast_start_bonus', 'uses'=>'AffiliatereportsController@faststart_bonus']);
        Route::match(['get', 'post'], 'team-bonus', ['as'=>'reports.team_bonus_bonus', 'uses'=>'AffiliatereportsController@team_bonus']);
        Route::match(['get', 'post'], 'leadership-bonus', ['as'=>'reports.leadership_bonus', 'uses'=>'AffiliatereportsController@leadership_bonus']);        
        Route::match(['get', 'post'], 'car-bonus', ['as'=>'reports.car_bonus', 'uses'=>'AffiliatereportsController@car_bonus']);
        Route::match(['get', 'post'], 'star-bonus', ['as'=>'reports.star_bonus', 'uses'=>'AffiliatereportsController@star_bonus']);  
		Route::match(['get', 'post'], 'survival-bonus', ['as'=>'reports.survival_bonus', 'uses'=>'AffiliatereportsController@survival_bonus']);  

		Route::match(['get', 'post'], 'team-bonus-details', ['as'=>'reports.team-bonus-details', 'uses'=>'AffiliatereportsController@team_bonus_details']);		
		Route::match(['get', 'post'], 'leadership-bonus-details', ['as'=>'reports.leadership-bonus-details', 'uses'=>'AffiliatereportsController@leadership_bonus_details']);		
    });
	Route::group(['prefix'=>'customer-commission'], function()
    {
		Route::match(['get', 'post'], 'personal-commission', ['as'=>'reports.personal_commission', 'uses'=>'AffiliatereportsController@personal_commission']);
		Route::match(['get', 'post'], 'ambassador-bonus', ['as'=>'reports.ambassador_bonus', 'uses'=>'AffiliatereportsController@ambassador_bonus']);	
		Route::match(['get', 'post'], 'monthly_bonus_details', ['as'=>'reports.monthly_bonus_details', 'uses'=>'AffiliatereportsController@monthly_bonus_details']);	
		Route::match(['get', 'post'], 'personal_bonus_details', ['as'=>'reports.personal_bonus_details', 'uses'=>'AffiliatereportsController@personal_monthly_bonus_details']);	
	});
	
	//Route::match(['get', 'post'], 'car-bonus', ['as'=>'car-bonus', 'uses'=>'AffiliatereportsController@car_bonus_commision_save']);
	Route::match(['get', 'post'], 'tds-deducted-report', ['as'=>'reports.tds-deducted-report', 'uses'=>'AffiliatereportsController@tds_deducted_details']);	
	Route::match(['get','post'],'details/{trans_id?}', ['as'=>'tds.details', 'uses'=>'AffiliatereportsController@tds_view_details']);	
	
    /* Ranks */
	//Route::match(['get', 'post'], 'ranks', ['as'=>'reports.ranks', 'uses'=>'AffiliatereportsController@ranks']);
    Route::group(['prefix'=>'ranks'], function()
    {
        Route::match(['get', 'post'], '/', ['as'=>'ranks.myrank', 'uses'=>'RanksController@myrank']);
        Route::match(['get', 'post'], 'history', ['as'=>'ranks.history', 'uses'=>'RanksController@myrank_history']);
        Route::match(['get', 'post'], 'eligibilities', ['as'=>'ranks.eligibilities', 'uses'=>'RanksController@eligibilities']);
    });

    Route::group(['prefix'=>'support'], function()
    {
        Route::match(['get', 'post'], 'tickets', ['as'=>'support.tickets', 'uses'=>'SupportController@tickets']);
        Route::post('save-tickets', ['as'=>'support.tickets_save', 'uses'=>'SupportController@save_tickets']);
        Route::post('view_ticket_detail', ['as'=>'support.tickets_detail', 'uses'=>'SupportController@view_ticket_detail']);
        Route::post('tickets_comment', ['as'=>'support.tickets_comment', 'uses'=>'SupportController@save_ticket_replies']);
        Route::post('tickets_close', ['as'=>'support.tickets_close', 'uses'=>'SupportController@close_ticket']);
        Route::post('tickets_status', ['as'=>'support.tickets_status', 'uses'=>'SupportController@tickets_status']);
        Route::match(['get', 'post'], 'tickets_details', ['as'=>'support.tickets_details', 'uses'=>'SupportController@tickets_details']);
        Route::match(['get', 'post'], 'faqs', ['as'=>'support.faqs', 'uses'=>'SupportController@get_faq']);
        //Route::match(['get', 'post'], 'faqs', ['as'=>'support.faqs', 'uses'=>'SupportController@faqs']);
        Route::post('faqs/{code?}', 'SupportController@get_faqs')->where('code', '([A-z-_]+)?');
        Route::post('faqs/search-term', 'SupportController@search_faq');
        Route::get('faqs/search_faq', 'SupportController@faq');
        Route::match(['get', 'post'], 'downloads', ['as'=>'support.downloads', 'uses'=>'SupportController@downloads']);
        Route::match(['get', 'post'], 'announcements', ['as'=>'support.announcements', 'uses'=>'SupportController@announcements']);
    });
 
	Route::post('add_cv_bons', ['as'=>'credit_pcc_bonus', 'uses'=>'BonusController@credit_pcc_bonus']);	
	Route::get('promoter_ranking', ['as'=>'promoter_ranking', 'uses'=>'scheduleController@promoter_ranking']);	
	Route::get('package_bonus', ['as'=>'package_bonus', 'uses'=>'BonusController@package_purchase_bonus']);	
	Route::group(['prefix'=>'add-money', 'as'=>'add-money.'], function()
    {
        Route::match(['get','post'],'set-amount', ['as'=>'set-amount', 'middleware'=>'validate', 'uses'=>'AddMoneyController@setAmount']);
        Route::match(['get','post'],'payment-info', ['as'=>'payment-info', 'middleware'=>'validate', 'uses'=>'AddMoneyController@paymentInfo']);
    });
});