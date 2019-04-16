<?php
Route::get('login', ['as'=>'login', 'uses'=>'AdminController@login']);
Route::post('forgot-password', ['as'=>'forgot-password',  'uses'=>'AdminController@forgotPassword']);
Route::post('check-account', ['as'=>'check-account',  'uses'=>'AdminController@checkAccount']);
Route::post('login', ['as'=>'login',  'uses'=>'AdminController@loginCheck']);
Route::match(['get','post'],'logout', ['as'=>'logout',  'uses'=>'AdminController@logout']);
Route::post('check-login', ['as'=>'check-login',  'uses'=>'AdminController@loginCheck']);

Route::group(['namespace'=>'Admin'], function()
 {  	
	Route::group(['middleware'=>['adauth']], function()
	{
		Route::get('dashboard', ['as'=>'dashboard', 'uses'=>'AdminDashboard_Controller@dashboard']);
		
		Route::group(['prefix'=>'catalog', 'as'=>'catalog.'], function()
		{
			Route::group(['prefix'=>'products', 'as'=>'products.'], function()
			{
				Route::group(['prefix'=>'seller', 'as'=>'seller.'], function()
				{
					
					Route::group(['prefix'=>'brands'], function()
					{						
						Route::any('/', ['as'=>'list', 'uses'=>'SuppliersController@brandList']);
						Route::post('save', ['as'=>'save', 'uses'=>'SuppliersController@saveBrand']);
						Route::post('update-status/{id}', ['as'=>'update-status', 'uses'=>'SuppliersController@updatebrandStatus']);			
						Route::post('update-verification', 'SuppliersController@updateBrandVerification');
						Route::post('details', 'SuppliersController@brand_details');						
						Route::post('delete/{brand_id}', 'SuppliersController@deleteBrand');
					});
				});
			});
		});
		Route::group(['prefix'=>'seller', 'as'=>'seller.'], function()
		{			
			Route::post('reset_pwd/{account_id}', ['as'=>'reset-pwd', 'uses'=>'SuppliersController@suppliers_reset_pwd']);
			Route::post('meta-info', ['as'=>'meta-info', 'uses'=>'SuppliersController@meta_info']);
			Route::post('meta-info/save', ['as'=>'meta-info.save', 'uses'=>'SuppliersController@save_meta_info']);
			Route::post('change_verify_status', ['as'=>'change-verify-status', 'uses'=>'SuppliersController@change_verify_status']);	
			Route::post('change_status/{id}', ['as'=>'change-status', 'uses'=>'SuppliersController@suppliers_status_update']);			
			Route::post('preferences/save', ['as'=>'preferences.save', 'uses'=>'SuppliersController@supplierSavePerferences']);
			Route::get('preferences/{uname}', 'SuppliersController@supplierPerferences');
			Route::post('delete_doc', ['as'=>'delete_doc', 'uses'=>'SuppliersController@delete_doc']);
			Route::post('update', ['as'=>'update', 'uses'=>'SuppliersController@update_suppliers']);
			Route::get('edit/{uname}', ['as'=>'edit', 'uses'=>'SuppliersController@edit_suppliers']);
			Route::post('change_status', ['as'=>'change-status', 'uses'=>'SuppliersController@change_status']);
			Route::post('verify-step', ['as'=>'verify-step', 'uses'=>'SuppliersController@verifyStep']);
			Route::any('/', ['as'=>'list', 'uses'=>'SuppliersController@suppliers_list']);
			//Route::any('{status?}', ['as'=>'list', 'uses'=>'SuppliersController@suppliers_list']);
			Route::get('details/{uname}', ['as'=>'details', 'uses'=>'SuppliersController@get_suppliers_details']);			
			Route::any('verification/{uname?}', ['as'=>'verification', 'uses'=>'SuppliersController@verification']);		
			Route::post('doc-list', ['as'=>'doc-list', 'uses'=>'SuppliersController@doc_list']);		
			
			Route::group(['prefix'=>'commission', 'as'=>'commission.'], function()
			{
				Route::post('details/{for}/{id}', ['as'=>'details', 'uses'=>'SuppliersController@profitSharingDetails'])->where(['for'=>'edit|view']);
				Route::post('update-status/{id}/{stauts}', ['as'=>'update-status', 'uses'=>'SuppliersController@profitSharingStatusUpdate']);
				Route::post('delete/{id}', ['as'=>'delete', 'uses'=>'SuppliersController@profitSharingDelete']);
				Route::post('save/{id}', ['as'=>'save', 'uses'=>'SuppliersController@profitSharingSave']);
				Route::match(['get', 'post'], '/', ['as'=>'list', 'uses'=>'SuppliersController@profitSharingList']);
			});				
		});		
		Route::group(['prefix'=>'affiliate', 'as'=>'aff.'], function()
		{ 
			Route::get('create', ['as'=>'create','uses'=>'AffiliateController@create_root_user']);
			Route::post('save', ['as'=>'save','middleware'=>'validate', 'uses'=>'AffiliateController@save_root_user']);
			Route::match(['GET','POST'],'free-affiliate', ['as'=>'free_affiliate','uses'=>'AffiliateController@free_affiliate']);
			Route::match(['GET','POST'],'root-affiliate', ['as'=>'manage_root_affiliate','uses'=>'AffiliateController@manage_root_affiliates']);
			Route::match(['GET','POST'],'view', ['as'=>'view', 'uses'=>'AffiliateController@manage_affiliate']);
			Route::match(['get','post'],'check-uname', ['as'=>'root-account.check-uname', 'uses'=>'AffiliateController@checkUnameAvaliable']);
			Route::match(['get','post'],'verify', ['as'=>'verify_affiliate', 'uses'=>'AffiliateController@user_verification_list']);
			Route::match(['get','post'],'user_email_check', ['as'=>'root-account.user_email_check', 'uses'=>'AffiliateController@checkEmailAvaliable']);
			Route::match(['get','post'],'user_mobile_check', ['as'=>'root-account.user_email_check', 'uses'=>'AffiliateController@CheckMobileAvailable']);
			Route::get('quick_login', ['as'=>'qlogin', 'uses'=>'AffiliateController@qlogin']);
			Route::post('quick_login', ['as'=>'quick_login', 'uses'=>'AffiliateController@quick_login']);
			Route::any('verification/{uname?}', ['as'=>'verification', 'uses'=>'AffiliateController@documentVerification']);	
			Route::post('change_document_status', ['as'=>'change-document-status', 'uses'=>'AffiliateController@changeDocumentStatus']);
			//Route::post('delete_doc', ['as'=>'delete_doc', 'uses'=>'AffiliateController@delete_doc']);
			//Route::post('doc-list', ['as'=>'doc-list', 'uses'=>'AffiliateController@doc_list']);		
			Route::get('details/{uname}', ['as'=>'details', 'uses'=>'AffiliateController@get_suppliers_details']);	
			Route::match(['get', 'post'], 'ranks', ['as'=>'ranks.', 'uses'=>'AffiliateController@get_affiliate_ranks']);
			Route::match(['get', 'post'], 'activation-mail', ['as'=>'activation_mail', 'uses'=>'AffiliateController@activation_mail']);
			Route::match(['get', 'post'], 'activate-user', ['as'=>'activate_user', 'uses'=>'AffiliateController@activate_mail_user']);
		});
		 
		Route::group(['prefix'=>'account'], function() {
		  Route::match(['get','post'],'view/{account_id}', ['as'=>'account.view-details', 'uses'=>'AffiliateController@view_details']);
		  Route::match(['get','post'],'change-password', ['as'=>'account.change-password', 'uses'=>'AffiliateController@change_password']);
		  Route::match(['get','post'],'reset-pin', ['as'=>'account.reset-pin','uses'=>'AffiliateController@reset_security_pin']);
		  Route::post('update_pin', ['as'=>'account.updatepin','middleware'=>'validate','uses'=>'AffiliateController@updatepin']);	
		  Route::post('update_pwd', ['as'=>'account.updatepwd','middleware'=>'validate','uses'=>'AffiliateController@updatepwd']);	
		  Route::match(['get','post'],'update_details', ['as'=>'account.update_details', 'uses'=>'AffiliateController@update_details']);	
		  Route::match(['get','post'],'edit/{account_id}', ['as'=>'account.edit-details', 'uses'=>'AffiliateController@edit_detail']);
		  Route::match(['get','post'],'email', ['as'=>'account.email','middleware'=>'validate','uses'=>'AffiliateController@updating_email']);
		  Route::match(['get','post'],'update_mobile', ['as'=>'account.update_mobile','middleware'=>'validate','uses'=>'AffiliateController@update_mobile']);
		  Route::match(['get', 'post'], 'block_status', ['as'=>'account.block_status', 'uses'=>'AffiliateController@user_block_status']);
		  Route::match(['get','post'],'active_status', ['as'=>'account.active_status', 'uses'=>'AdminController@active_status']);
		});
		
		Route::post('country-list/{status?}', ['as'=>'country-list', 'uses'=>'AffiliateController@country_list']);
		
		Route::group(['prefix'=>'franchisee','as'=>'franchisee.'], function() {
			
		    Route::match(['get','post'],'{type}-address', ['as'=>'address', 'uses'=>'FranchiseeController@getAddress']);
		    Route::get('quick-login', ['as'=>'quick-login', 'uses'=>'FranchiseeController@qlogin']);
		   	Route::match(['get','post'],'quick_login', ['as'=>'quick_login', 'uses'=>'FranchiseeController@quick_login']); 
			Route::match(['get','post'],'/', ['as'=>'list','uses'=>'FranchiseeController@view_franchisee']);
			Route::get('create',['as'=>'create','uses'=>'FranchiseeController@create_franchise']);
			Route::post('save', ['as'=>'save','middleware'=>'validate','uses'=>'FranchiseeController@save_franchisee']);
			Route::get('edit/{uname}', ['as'=>'edit','middleware'=>'validate','uses'=>'FranchiseeController@franchisee_edit_profile']);
			
			Route::post('edit/{uname}/save', ['as'=>'edit-save','middleware'=>'validate','uses'=>'FranchiseeController@update_franchisee_profile']);
			
			Route::get('activity', ['as'=>'activity','uses'=>'FranchiseeController@activity_log']);
			Route::post('activity', ['as'=>'activity','uses'=>'FranchiseeController@activity_log']);
			
			Route::post('states', ['as'=>'states','uses'=>'FranchiseeController@get_states']);
		    Route::match(['get','post'],'districts', ['as'=>'districts','uses'=>'FranchiseeController@get_districts']);
			Route::post('cities', ['as'=>'cities','uses'=>'FranchiseeController@get_cities']);

			Route::get('kyc', ['as'=>'kyc','uses'=>'FranchiseeController@kyc']);
			Route::post('kyc', ['as'=>'kyc.json','uses'=>'FranchiseeController@kyc']);			
			
			Route::post('kyc/change-status/{uv_id}', ['as'=>'','uses'=>'FranchiseeController@kyc_change_status']);
			
			Route::post('kyc/delete/{uv_id}', ['as'=>'','uses'=>'FranchiseeController@kyc_delete']);
			Route::post('validate/email', ['as'=>'validate.email','middleware'=>['validate'],'uses'=>'FranchiseeController@check_email']);
			Route::post('validate/username', ['as'=>'validate.username','middleware'=>['validate'],'uses'=>'FranchiseeController@check_username']);			

			Route::post('states/check', ['as'=>'states-check','uses'=>'FranchiseeController@get_franchisee_state_phonecode']);
			Route::post('districts/check', ['as'=>'district-check','uses'=>'FranchiseeController@get_franchisee_district']);
			Route::post('city/check', ['as'=>'city-check','uses'=>'FranchiseeController@get_franchisee_city']);
			Route::post('currencies', ['as'=>'currencies','uses'=>'FranchiseeController@get_currency_list']);

			Route::post('access/check', 'FranchiseeController@check_franchise_access');
			Route::get('access/edit', ['as'=>'access.edit','uses'=>'FranchiseeController@edit_franchisee_access']);
			Route::post('access/update', ['as'=>'access.update','uses'=>'FranchiseeController@update_franchisee_accessinfo']);
			Route::post('access/add', ['as'=>'access.add','uses'=>'FranchiseeController@add_franchisee_access']);			
			Route::post('new-access/save', ['as'=>'access.savenew','uses'=>'FranchiseeController@update_newaccess']);
						
			Route::post('change-email', ['as'=>'change-email','middleware'=>['validate'],'uses'=>'FranchiseeController@updateding_email']);
			Route::post('change-mobile', ['as'=>'change-mobile','middleware'=>'validate','uses'=>'FranchiseeController@update_mobile']);
			Route::post('reset-pwd', ['as'=>'reset-pwd','middleware'=>'validate','uses'=>'FranchiseeController@reset_pwd']);
			Route::post('reset-pin', ['as'=>'reset-pin','middleware'=>'validate','uses'=>'FranchiseeController@reset_pin']);
			Route::post('login/{status}', ['as'=>'block','middleware'=>'validate','uses'=>'FranchiseeController@updateTransAccess']);	
			Route::post('address/save', ['as'=>'address.save', 'uses'=>'FranchiseeController@saveAddress']);		
			
			Route::post('block', ['as'=>'block','uses'=>'FranchiseeController@change_block_franchisee']);
			Route::post('loginblock', ['as'=>'loginblock','uses'=>'FranchiseeController@change_franchisee_loginblock']);			
			
			Route::post('check', ['as'=>'check','uses'=>'FranchiseeController@check_franchisee']);
			Route::post('mapping/check', ['as'=>'mapping.check','uses'=>'FranchiseeController@check_franchise_mapped']);			

			Route::get('packages', ['as'=>'packages','middleware'=>['validate'] ,'uses'=>'FranchiseeController@packages']);
			
			Route::get('addfund', ['as'=>'addfund','uses'=>'FinanceController@add_fund_to_frnachisee']);
			Route::post('details/check', ['as'=>'details.check','uses'=>'FinanceController@check_franchisee_details']);
			Route::post('addfund/save', ['as'=>'addfund.save','uses'=>'FinanceController@save_fund_to_frnachisee']);
			
			Route::get('fund-credits', ['as'=>'fund-credits','uses'=>'FinanceController@supportCenterFundCredits']);
			Route::post('fund-credits', ['as'=>'fund-credits.json','uses'=>'FinanceController@supportCenterFundCredits']);
			Route::post('change_fund_status', ['as'=>'change_fund_status','uses'=>'FinanceController@change_franchisee_fund_status']);
			
			Route::get('fund-transfer-commission', ['as'=>'fundtransfer-commission','uses'=>'TransferController@franchiseeFundTransferCommission']);
			Route::post('fund-transfer-commission', ['as'=>'fundtransfer-commission.json','uses'=>'TransferController@franchiseeFundTransferCommission']);
			
			/* Kyc Verification */
			Route::any('kyc-verification', ['as'=>'kyc-verification', 'uses'=>'FranchiseeController@kycDocVerification']);	
			Route::post('change_document_status', ['as'=>'change-document-status', 'uses'=>'FranchiseeController@changeKycDocStatus']);
			
			Route::match(['get', 'post'], 'fundtransfer-commission', ['as'=>'fundtransfer_commission', 'uses'=>'FranchiseeController@franchiseeFundTransferCommission']);
			
			Route::match(['get', 'post'], 'merchant-enrolment-fee', ['as'=>'mer_enrollment_fee', 'uses'=>'FranchiseeController@Merchant_enrolment_fee']);
			
			Route::match(['get', 'post'], 'profit_sharing', ['as'=>'profit_sharing', 'uses'=>'FranchiseeController@Profit_sharing']);
			
            Route::match(['get', 'post'], 'profit_sharing_details/{account_id}/{created_on_date?}', ['as'=>'profit_sharing_details', 'uses'=>'FranchiseeController@profit_sharing_details']);
			
			Route::match(['get', 'post'], 'commission/{account_id}/{created_on_date?}', ['as'=>'mer_enrollment_details', 'uses'=>'FranchiseeController@Merchant_enrolment_details']);
			
		});	
	});	
	
	Route::group(['prefix'=>'finance', 'as'=>'finance.'], function()
	{		
		Route::group(['prefix'=>'fund-transfer', 'as'=>'fund-transfer.'], function(){
			
			Route::match(['get', 'post'], 'find-merchant', ['as'=>'find_merchant', 'uses'=>'AdminFinanceController@find_merchant']);
			Route::match(['get', 'post'], 'member/{type?}/{member?}', ['as'=>'to_member', 'middleware'=>'validate', 'uses'=>'AdminFinanceController@trasnferTo_affiliate']);			
			Route::post('find-member', ['as'=>'find_member', 'uses'=>'AdminFinanceController@find_member']);
			Route::match(['get', 'post'], 'affiliate/{type?}/{member?}', ['as'=>'to_affiliate', 'middleware'=>'validate', 'uses'=>'AdminFinanceController@trasnferTo_affiliate']);
			Route::match(['get', 'post'], 'franchasee/{type?}/{member?}', ['as'=>'to_franchasee', 'middleware'=>'validate', 'uses'=>'AdminFinanceController@trasnferTo_franchasee']);
			Route::post('find-affiliate', ['as'=>'find_affiliate', 'uses'=>'AdminFinanceController@find_affiliate']);
		});

		Route::match(['get', 'post'], 'ewallet', ['as'=>'ewallet', 'uses'=>'AdminFinanceController@affiliate_ewallet']);
		Route::match(['get', 'post'], 'fund-transfer-history', ['as'=>'fund-transfer-history', 'uses'=>'AdminFinanceController@fund_transfer_history']);
		Route::match(['get', 'post'], 'admin-transfer-history', ['as'=>'admin-transfer-history', 'uses'=>'AdminFinanceController@admin_fund_transfer_history']);
		Route::match(['get','post'],'transaction-log/{for?}', ['as'=>'transaction-log', 'uses'=>'AdminFinanceController@transactionLog']);
		Route::match(['get', 'post'], 'admin-credit-debit-history', ['as'=>'admin-credit-debit-history', 'uses'=>'AdminFinanceController@admin_credit_debit_history']);
		Route::match(['get', 'post'], 'order-payments', ['as'=>'order-payments', 'uses'=>'AdminFinanceController@online_payments']);
		Route::match(['get', 'post'], 'order-payments-details/{id}', ['as'=>'order-payments-details', 'uses'=>'AdminFinanceController@online_payments_details']);
		Route::match('post', 'pay-confirm/{id}', ['as'=>'pay-confirm', 'uses'=>'AdminFinanceController@confirmPayment']);
		Route::match(['get', 'post'], 'payment-paid/{id}', ['as'=>'payment-paid', 'uses'=>'AdminFinanceController@updateStatus']);
		Route::match('post', 'paymen-refund/{id}', ['as'=>'payment-refund', 'uses'=>'AdminFinanceController@refundPayment']);
		Route::match(['get','post'],'wallet-transcation', ['as'=>'wallet-transcation', 'uses'=>'AdminFinanceController@wallet_transcation']);
		Route::match(['get','post'],'pg-transcation', ['as'=>'pg-transcation', 'uses'=>'AdminFinanceController@PaymentGateway_transcation']);
	});
	
	Route::group(['prefix'=>'commission', 'as'=>'commission.'], function() { 
		Route::match(['get','post'],'team',['as'=>'team','uses'=>'AdminReportController@team_commission']);
		Route::match(['get','post'],'leadership',['as'=>'leadership','uses'=>'AdminReportController@leadership_bonus']);
		Route::post('leadership/{period}',['as'=>'leadership.details','uses'=>'AdminReportController@leadership_bonus']);
		Route::match(['get','post'],'car',['as'=>'car','uses'=>'AdminReportController@car_bonus']);		
		Route::match(['get','post'],'star',['as'=>'star','uses'=>'AdminReportController@star_bonus']);
		Route::post('change_bonus_status', ['as'=>'change-bonus-status', 'uses'=>'AdminReportController@changeBonusStatus']);
		Route::post('change_star_bonus_status', ['as'=>'change-star-bonus-status', 'uses'=>'AdminReportController@changeStarBonusStatus']);
		Route::match(['get','post'],'ambassador',['as'=>'ambassador','uses'=>'AdminReportController@ambassador_bonus']);
		Route::match(['get','post'],'faststart_bonus',['as'=>'faststart_bonus','uses'=>'AdminReportController@faststart_bonus']);
		Route::match(['get','post'],'personal_bonus',['as'=>'personal_bonus','uses'=>'AdminReportController@personal_commission']);
		Route::match(['get','post'],'ranks',['as'=>'ranks','uses'=>'AdminReportController@get_ranks']);
		Route::match(['get','post'],'rank_log',['as'=>'rank_log','uses'=>'AdminReportController@rank_log']);
	});

	Route::group(['prefix'=>'withdrawals', 'as'=>'withdrawals.'], function(){ 
	   Route::match(['get','post'],'details/{trans_id}',['as'=>'details','uses'=>'AdminWithdrawalsController@withdrawals_details']);
	   Route::match(['get','post'],'confirm',['as'=>'confirm','uses'=>'AdminWithdrawalsController@withdrawals_confirm']);
	   Route::match(['get','post'],'Process',['as'=>'process','uses'=>'AdminWithdrawalsController@withdrawals_process']);
	   Route::match(['get','post'],'/',['as'=>'list','uses'=>'AdminWithdrawalsController@WithdrawalsList']);
	   Route::post('cancel',['as'=>'cancel','uses'=>'AdminWithdrawalsController@cancel_Withdrawal']);
	   Route::match(['get','post'],'/',['as'=>'list','uses'=>'AdminWithdrawalsController@WithdrawalsList']);
	   Route::match(['get','post'],'{status?}',['as'=>'history','uses'=>'AdminWithdrawalsController@WithdrawalsList']);	  
	});
		
	Route::group(['prefix'=>'settings', 'as'=>'settings.'], function() { 
		Route::get('change-password',['as'=>'change-pwd','uses'=>'AdminDashboard_Controller@change_password']);
		Route::post('update-password',['as'=>'update-pwd','uses'=>'AdminDashboard_Controller@updatePasswrord']);
	});
	
	Route::group(['prefix'=>'report', 'as'=>'report.'], function() { 
		 Route::match(['get','post'],'purchase-history',['as'=>'purchase_history','uses'=>'PackageController@upgrade_history']);	
         Route::post('activate', ['as'=>'activate', 'uses'=>'PackageController@purchase_activate']);     
          Route::match(['get','post'],'qualified-volume', ['as'=>'qualified_volume', 'uses'=>'AdminReportController@Qualified_volume_details']);    		 
	});	 
 });