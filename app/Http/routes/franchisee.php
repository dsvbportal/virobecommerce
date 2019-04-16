<?php
Route::get('test', function(){
	return DB::table('account_mst')->get();
});
Route::get('validate/lang/{langkey}', ['as'=>'lang','uses'=>'LangController@langLoad']);
//Route::get('/', ['as'=>'login', 'uses'=>'FranchiseeController@login']);
Route::get('login', ['as'=>'login', 'uses'=>'FranchiseeController@login']);
Route::post('checklogin', ['as'=>'checklogin', 'uses'=>'FrAuthcontroller@login_check']);
Route::post('forgotpwd', ['as'=>'forgotpwd', 'middleware'=>['validate'],'uses'=>'FrAuthcontroller@forgotpwd']);
Route::get('resetpwd-link/{token}', ['as'=>'pwdreset-link', 'uses'=>'FrAuthcontroller@verifyForgotpwdLink']);
Route::post('resetpwd-save', ['as'=>'pwdreset-save', 'uses'=>'FrAuthcontroller@update_newpwd']);
Route::match(['get','post'],'logout', ['as'=>'logout', 'middleware'=>['frauth'], 'uses'=>'FrAuthcontroller@logout']);

Route::group(['middleware'=>['frauth', 'validate']], function()
{
	Route::get('dashboard', ['as'=>'dashboard', 'uses'=>'FranchiseeController@dashboard']);	
	
	Route::match(['get', 'post'],'sample', ['as'=>'sample', 'uses'=>'FranchiseeController@sample']);	
	
	Route::match(['get', 'post'],'orders', ['as'=>'orders', 'uses'=>'StoreController@orders_recent']);
	Route::group(['prefix'=>'profile'], function()
	{
	Route::get('/', ['as'=>'profile', 'uses'=>'FranchiseeController@myprofile']);
	Route::get('bank-info', ['as'=>'profile.bank-info', 'uses'=>'FranchiseeController@myprofile']);
	Route::post('update', ['as'=>'profile.update', 'uses'=>'FranchiseeController@updateProfile']);		
	Route::post('franchiseelogo_save', ['as'=>'profile.franchiseelogo_save', 'uses'=>'FranchiseeController@logoimage_withcrop_save']);
	Route::get('remove_profile_image', ['as'=>'profile.remove_profile_image', 'uses'=>'FranchiseeController@remove_profile_image']);
	Route::post('tempimg_upload', ['as'=>'profile.tempimg_upload', 'uses'=>'FranchiseeController@tempimg_upload']);
	Route::get('kyc', ['as'=>'profile.kyc', 'uses'=>'FranchiseeController@kyc']);
	Route::match(['get', 'post'], 'kyc-upload', ['as'=>'profile.kyc_upload', 'uses'=>'FranchiseeController@kyc_upload']);
	Route::get('change-mobile', ['as'=>'settings.change_mobile', 'uses'=>'FranchiseeController@change_mobile']);
	Route::post('change-email/verification/send', ['as'=>'settings.emailverification', 'uses'=>'FranchiseeController@sendEmailVerification']);
	Route::get('change-email/verification/{token}', ['as'=>'settings.changeemail.verification', 'uses'=>'FranchiseeController@verifylink_change_email']);
	Route::post('change-email/send-otp', ['as'=>'settings.changeemail.send_otp', 'uses'=>'FranchiseeController@sendEmailVerificationOTP']);
	Route::post('change-email/verify-otp', ['as'=>'settings.changeemail.verification_otp', 'uses'=>'FranchiseeController@verify_change_email_otp']);	
	Route::match(['post'], 'send-update-mobile-verification', ['as'=>'settings.updatemobileverification', 'uses'=>'FranchiseeController@sendUpdate_mobileVerification']);
	Route::match(['get', 'post'], 'update-mobile', ['as'=>'settings.update_mobile', 'uses'=>'FranchiseeController@update_mobile']);	
	});

    Route::group(['prefix'=>'settings'], function()
     {
		Route::get('profile-info', ['as'=>'settings.profile_info', 'uses'=>'FranchiseeController@profile_info']);
        Route::post('update-profile', ['as'=>'settings.update_profile', 'middleware'=>['validate'], 'uses'=>'FranchiseeController@update_profile']);
		Route::match(['post'], 'update_password', ['as'=>'settings.updatepwd', 'middleware'=>'validate','uses'=>'FranchiseeController@updatepwd']);
		Route::match(['get', 'post'], 'password_check', ['as'=>'settings.password_check', 'uses'=>'FranchiseeController@password_check']);
		
		/*Security Pin */
		Route::post('security-pin/save', ['as'=>'settings.securitypin.save', 'uses'=>'FranchiseeController@securitypin_save']);
		Route::post('security-pin/create', ['as'=>'settings.securitypin.create', 'middleware'=>'validate','uses'=>'FranchiseeController@securitypin_create']);
	    Route::post('security-pin/reset', ['as'=>'settings.securitypin.reset','middleware'=>'validate','uses'=>'FranchiseeController@securitypin_reset']);
	    Route::post('security-pin/verify', ['as'=>'settings.securitypin.verify','middleware'=>'validate','uses'=>'FranchiseeController@securitypin_verify']);
		Route::post('forgot_security_pin', ['as'=>'settings.forgot_security_pin','uses'=>'FranchiseeController@forgot_security_pwd']);
		Route::post('securitypin-resetotp', ['as'=>'settings.securitypin.forgototp.verify','middleware'=>'validate', 'uses'=>'FranchiseeController@securitypin_forgototp_check']);
		
		Route::match(['get', 'post'], 'reset_security_pwd/{activation_key}', ['as'=>'settings.reset_security_pwd', 'uses'=>'FranchiseeController@reset_security_pwd']);
		
		   Route::match(['get', 'post'], 'reset_update_pwd', ['as'=>'settings.reset_update_pwd', 'uses'=>'FranchiseeController@updatesecuritypwd']);
		  /* Kyc Upload */
		   Route::post('kyc-document-upload', ['as'=>'settings.kyc_document_upload', 'uses'=>'SettingsController@kycDocumentUpload']);
		  /* Bank details */
		   Route::post('get_bank_details', ['as'=>'settings.bank-info', 'uses'=>'SettingsController@get_bank_details']);
		   Route::post('get-ifsc-details', ['as'=>'settings.get-ifsc-details', 'uses'=>'SettingsController@Get_Ifsc_Bank_Details']); 
		   Route::post('bank-details', ['as'=>'settings.bank-details','middleware'=>'validate','uses'=>'SettingsController@Bank_Details']);
     
	 /* Change Email */
		   Route::post('change-email/verification/send', ['as'=>'settings.emailverification', 'uses'=>'SettingsController@sendEmailVerification']);
		   Route::get('change-email/verification/{token}', ['as'=>'settings.changeemail.verification', 'uses'=>'SettingsController@verifylink_change_email']);
		   Route::post('change-email/send-otp', ['as'=>'settings.changeemail.send_otp','middleware'=>'validate','uses'=>'SettingsController@sendEmailVerificationOTP']);
		   Route::post('change-email/verify-otp', ['as'=>'settings.changeemail.verification_otp', 'uses'=>'SettingsController@verify_change_email_otp']);
		   
			/* Change Mobile */  
		    Route::post('change-mobile/verification/send', ['as'=>'settings.mobileverification', 'uses'=>'SettingsController@sendMobileVerification']);
		    Route::get('change-mobile/verification/{token}', ['as'=>'settings.changemobile.verification', 'uses'=> 'SettingsController@verifylink_change_mobile']);
		    Route::post('change-mobile/send-otp', ['as'=>'settings.changemobile.send_otp', 'middleware'=>'validate','uses'=>'SettingsController@sendMobileVerificationOTP']);
		    Route::post('change-mobile/verify-otp', ['as'=>'settings.changemobile.verification_otp', 'uses'=>'SettingsController@verify_change_mobile_otp']);
		   /* payouts */
		    Route::get('{type}-address/', ['as'=>'settings.address', 'uses'=>'FranchiseeController@getAddress']);
			Route::post('{type}-address/save', ['as'=>'settings.address.save','middleware'=>'validate','uses'=>'FranchiseeController@saveAddress']);	
			
		 /* verify_mobile */
		  Route::post('mobile/verification/send', ['as'=>'settings.mobile.verify', 'uses'=>'SettingsController@verifyMobile_Sendotp']);
		  Route::post('mobile/verification/resend', ['as'=>'settings.mobile.verifyotp_resend', 'uses'=>'SettingsController@verifyMobile_SendotpResend']); 
		  Route::post('mobile/verification', ['as'=>'settings.mobile.verifyotp', 'uses'=>'SettingsController@verifyMobile_otp']);
	 });
	/* Reports */
	 Route::group(['prefix'=>'reports'], function()
	 {
		Route::match(['get', 'post'], 'tds-deducted-report', ['as'=>'reports.tds-deducted-report', 'uses'=>'ReportController@tds_deducted_details']);	 
		Route::match(['get', 'post'], 'earned-commission', ['as'=>'reports.earned-commission', 'uses'=>'ReportController@earned_commission']);			 
		Route::match(['get', 'post'], 'activity_log', ['as'=>'reports.activity_log', 'uses'=>'ReportController@Activity_log']);			 
		Route::match(['get', 'post'], 'merchant-due', ['as'=>'reports.merchant-due', 'uses'=>'ReportController@merchant_due']);			 
		Route::match(['get', 'post'], 'commission/{commission}/{created_on_date?}', ['as'=>'reports.details', 'uses'=>'ReportController@earned_commission_details']);
	     /*Store */
         Route::match(['get', 'post'], 'store_details', ['as'=>'reports.store_details', 'uses'=>'ReportController@earned_commission_store_details']);		
	 });	 
	
    /* Withdrawl */
	Route::group(['prefix'=>'withdrawal'], function()
	{
		Route::match(['get', 'post'], '/', ['as'=>'withdrawal.history', 'uses'=>'WithdrawalController@Withdrawals_history']); 
		Route::match(['get','post'],'details/{trans_id?}', ['as'=>'withdrawal.details', 'uses'=>'WithdrawalController@withdrawal_details']);
		Route::match(['get','post'],'cancel_request', ['as'=>'withdrawal.cancel_request', 'uses'=>'WithdrawalController@cancel_withdrawal_request']);
	    Route::match(['get', 'post'], 'make', ['as'=>'withdrawal.request', 'uses'=>'WithdrawalController@withdrawal_request']);
	    Route::post('payout_withdraw_settings', ['as'=>'withdrawal.payout_withdraw_settings', 'uses'=>'WithdrawalController@payout_withdrawal_settings']);
		Route::post('save_withdrawal', ['as'=>'withdrawal.save_withdrawal', 'uses'=>'WithdrawalController@save_withdrawal']);
		
	  /* Not Used */
		Route::match(['get', 'post'], 'create', ['as'=>'withdrawal.create', 'uses'=>'WithdrawalController@new_withdrawal']); 
	   
	    /* 
		Route::match(['get', 'post'], 'payout', ['as'=>'withdrawal.payouts', 'uses'=>'WithdrawalController@payoutTypesList']);
		Route::match(['get', 'post'], 'payout-details', ['as'=>'withdrawal.payout-details', 'uses'=>'WithdrawalController@payoutDetails']);
		Route::match(['get', 'post'], 'save', ['as'=>'withdrawal.save', 'uses'=>'WithdrawalController@saveWithdraw']);
		Route::match(['get', 'post'], '{status}/list', ['as'=>'withdrawal.list', 'uses'=>'WithdrawalController@withdrawal_list']);
		Route::match(['get', 'post'], '{status}', ['as'=>'withdrawal.history', 'uses'=>'WithdrawalController@history']);
		 */
	});

     /* Wallet */
	Route::group(['prefix'=>'wallet'], function()
	{
	  Route::match(['get'], '/', ['as'=>'wallet.balance', 'uses'=>'WalletController@my_wallet']);
	  Route::match(['get', 'post'], 'fund-transfer', ['as'=>'wallet.fundtransfer', 'uses'=>'TranferController@fundtransfer']);
	  Route::match(['get', 'post'], 'searchacc', ['as'=>'wallet.fundtransfer.usrsearch', 'uses'=>'TranferController@searchacc']);
	  Route::match(['get', 'post'], 'fund_transfer_confirm', ['as'=>'wallet.fund_transfer_confirm', 'uses'=>'TranferController@fund_transfer_to_account_confirm']);
	  //Route::match(['get', 'post'], 'get_tac_code', ['as'=>'wallet.fundtransfer.get_tac_code', 'uses'=>'TranferController@get_tac_code']); 
	  Route::match(['get', 'post'], 'fund_transfer_save', ['as'=>'wallet.fund_transfer_save',  'middleware'=>['validate'],'uses'=>'TranferController@fund_transfer_to_account']); 
	 /*Route::match(['get', 'post'], 'fund-transfer/history', ['as'=>'wallet.fundtransfer.history',  'uses'=>'TranferController@fundtransfer_history']);*/
	  Route::match(['get', 'post'], 'transactions', ['as'=>'wallet.transactions', 'uses'=>'WalletController@transactions']);
	  Route::match(['get', 'post'], 'fund-transfer/history', ['as'=>'wallet.fundtransfer.history', 'uses'=>'WalletController@fundtransfer_history']);
	 //Route::match(['get', 'post'], 'transactions', ['as'=>'wallet.transactions', 'uses'=>'WalletController@transactions']);
	});

	Route::group(['prefix'=>'addfund'], function()
	{
		 Route::get('/', ['as'=>'addfund', 'uses'=>'WalletController@my_wallet']);
	});

	Route::group(['prefix'=>'merchants','as'=>'merchants.'], function()
	{
		Route::match(['get','post'], '/', ['as'=>'list','uses'=>'ManageMerchantController@get_merchant_list']);	
	    Route::match(['get','post'],'create',['as'=>'create','uses'=>'ManageMerchantController@create_Merchant']);
	    Route::post('save', ['as'=>'save','uses'=>'ManageMerchantController@save_merchant_old']); 
		Route::post('categories/in-store', ['as'=>'categories.in-store','uses'=>'ManageMerchantController@getCategories']);
		Route::match(['get','post'],'kyc', ['as'=>'kyc','uses'=>'ManageMerchantController@getCategories']);
		Route::match(['get','post'],'manage-kyc/{code}', ['as'=>'manage_kyc','uses'=>'ManageMerchantController@Upload_KYC_details']);
		Route::match(['get','post'],'tax-information', ['as'=>'tax-information','uses'=>'ManageMerchantController@tax_information']);
		Route::match(['get','post'],'gst-information', ['as'=>'gst-information','uses'=>'ManageMerchantController@gst_information']);
		
		Route::get('verifyEmailLink/{token}', ['as' => 'verifyEmailLink', 'uses' => 'AccountController@verifyEmailLink'])->where('token', '[a-zA-Z0-9]{22,50}');
		
	});
	Route::group(['prefix'=>'user','as'=>'user.'], function()
	{
	    Route::get('create',['as'=>'create','uses'=>'UserController@create_User']);
	    Route::post('save', ['as'=>'save','uses'=>'UserController@save_User']);
	    Route::match(['get','post'],'/', ['as'=>'list','uses'=>'UserController@manage_User']);
		Route::post('geo/state', ['as'=>'state','uses'=>'UserController@getState']);
        Route::post('geo/district', ['as'=>'district','uses'=>'UserController@getDistrict']);
        Route::post('geo/city', ['as'=>'city','uses'=>'UserController@getCity']);
		
        Route::post('change-password', ['as'=>'change-password','uses'=>'UserController@Change_Password']);
	    Route::match(['get', 'post'], 'active', ['as'=>'active', 'uses'=>'UserController@user_status']);
		Route::match(['get','post'],'edit/{account_id}', ['as'=>'edit-details', 'uses'=>'UserController@edit_detail']);
	    Route::match(['get','post'],'update_details', ['as'=>'update_details', 'uses'=>'UserController@update_details']);
	    Route::get('{type}-address/{account_id}/', ['as'=>'address', 'uses'=>'UserController@getAddress']);
		Route::post('{type}-address/save', ['as'=>'address.save','uses'=>'UserController@saveAddress']);
	});
	 Route::match(['get','post'],'franchisee_commission', ['as'=>'franchisee_commission','uses'=>'FranchiseeController@sample']); 
});
?>