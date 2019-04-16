<?php

Route::get('/', ['as'=>'new_home', 'uses'=>'AccountController@new_home']);
Route::get('home', ['as'=>'home', 'uses'=>'AccountController@home']);
Route::get('validate/lang/{langkey}', 'LangController@langLoad');
Route::get('login', ['as'=>'login', 'uses'=>'AccountController@login']);
Route::get('sign-up', ['as'=>'sign-up', 'uses'=>'AccountController@sign_up']);
Route::post('sign-up-save', ['as'=>'sign_up_save','middleware'=>['validate'],'uses'=>'AccountController@sign_up_save']);
Route::post('sign-up-varification', ['as'=>'sign_up_varification','middleware'=>['validate'],'uses'=>'AccountController@signup_varification']);
Route::post('sign-up-resend-otp', ['as'=>'sign_up_resend_otp','middleware'=>['validate'],'uses'=>'AccountController@sign_up_resend_otp']);
Route::post('checklogin', ['as'=>'checklogin', 'middleware'=>['validate'], 'uses'=>'AccountController@checklogin']); 
Route::post('forgot-pwd',['as'=>'forgot_pwd', 'middleware'=>['validate'], 'uses'=>'AccountController@forgot_password']);
Route::get('resetpwd-link/{token}', ['as'=>'resetpwd-link', 'uses'=>'AccountController@verifyForgotpwdLink']);
Route::post('reset-pwd', ['as'=>'reset_pwd', 'middleware'=>['validate'], 'uses'=>'AccountController@reset_pwd']);

/* Ambika */
Route::get('contact-us', ['as'=>'contact_us', 'uses'=>'AccountController@contact_us']);
Route::post('update-contact-us', ['as'=>'update_contact_us', 'middleware'=>['validate'], 'uses'=>'AccountController@update_contact_us']);
Route::get('faqs/{code?}', ['as'=>'faqs', 'uses'=>'SupportController@faqs']);

Route::get('change-email/varify_link/{token}', ['as'=>'change-email.varify_link', 'uses'=>'AccountController@varify_link']);
Route::get('change-email/varify-new-link/{token}', ['as'=>'change-email.varify_new_link', 'uses'=>'AccountController@varify_new_link']);
Route::get('category/{cat}', ['as'=>'cms', 'uses'=>'ProductController@get_cat_products']);
Route::get('{cms_url}', ['as'=>'cms', 'uses'=>'CmsController@cmsPage']);
/* Ambika */

/* Page Data */
Route::match(['get','post'],'get-page-data', ['as'=>'get-page-data', 'uses'=>'DataController@pageData']);
Route::post('cart-items',['as'=>'cart-items', 'uses'=>'ProductController@cart_items']);
Route::post('cart-items-remove',['as'=>'cart-items-remove', 'uses'=>'ProductController@cart_items_remove']);

/* Product */
Route::group(['prefix'=>'product', 'as'=>'product.'], function()
{ 		
	//Route::get('list', ['as'=>'list', 'uses'=>'ProductController@productList']);
	Route::get('cart-items-view',['as'=>'cart-items-view', 'uses'=>'ProductController@cart_items_view']);
	Route::post('checkout',['as'=>'checkout', 'uses'=>'ProductController@checkout']);
	Route::post('update-cart-qty',['as'=>'update-cart-qty', 'uses'=>'ProductController@update_cart_qty']);
	Route::post('add-to-cart/{code}', ['as'=>'add-to-cart', 'uses'=>'ProductController@product_add_cart']);
	Route::post('add-to-wishlist/{code}', ['as'=>'add-to-wishlist','middleware'=>['validate'],  'uses'=>'ProductController@add_to_wishlist']);
	
	Route::get('wishlist', ['as'=>'wishlist','middleware'=>['validate'],  'uses'=>'ProductController@view_wishlist']);
	Route::post('remove-to-wishlist/{code?}', ['as'=>'remove-to-wishlist','middleware'=>['validate'],  'uses'=>'ProductController@remove_to_wishlist']);
	Route::post('browse', ['as'=>'browse','middleware'=>['validate'], 'uses'=>'ProductController@browse_products']);
	Route::post('search', ['as'=>'search', 'uses'=>'ProductController@productSearch']);
	
	Route::get('select-address', ['as'=>'select_address', 'uses'=>'ProductController@delivery_address']);
	Route::match(['get','post'],'payment-types', ['as'=>'payment-types', 'uses'=>'ProductController@payment_types']);
	Route::match(['get','post'],'{category_slug}', ['as'=>'list','uses'=>'ProductController@productList']);
	Route::get('{category}/{slug}', ['as'=>'details', 'uses'=>'ProductController@product_details']);
});

/* Change Mobile for virob - subin */
Route::get('verify-link/{token}', ['as'=>'verify-link', 'uses'=>'AccountController@verify_link']);
Route::post('update-mobile', ['as'=>'update-mobile', 'middleware'=>['validate'], 'uses'=>'AccountController@updatemobile']);
Route::post('otp-validation', ['as'=>'otp-validation', 'middleware'=>['validate'], 'uses'=>'AccountController@otp_validation']);

/* End Change Mobile for virob - subin */
Route::group(['middleware'=>['ecomauth', 'validate']], function()
{	
    Route::post('logout', ['as'=>'logout', 'uses'=>'AccountController@logout']);	
	Route::group(['prefix'=>'account', 'as'=>'account.'], function()
    { 			  
	    Route::get('profile', ['as'=>'profile', 'uses'=>'AccountController@profile']);
	    Route::get('security', ['as'=>'security', 'uses'=>'AccountController@account_security']);
		Route::post('update', ['as'=>'update',  'middleware'=>['validate'], 'uses'=>'AccountController@updateProfile']);	
	    Route::get('change-pwd', ['as'=>'change-pwd', 'uses'=>'AccountController@changepassword']);	
	    Route::post('update-pwd', ['as'=>'update-pwd', 'middleware'=>['validate'], 'uses'=>'AccountController@updatepwd']);	
		
		Route::post('check-pincode', ['as'=>'check-pincode', 'uses'=>'AccountController@checkPincode']);
		Route::any('address', ['as'=>'address', 'uses'=>'AccountController@getAddress']);
		Route::post('save-address', ['as'=>'save-address', 'uses'=>'AccountController@saveAddress']);
		
		/*  virob ambika  */
		Route::any('get-address', ['as'=>'get_address', 'uses'=>'AccountController@get_Address']);
    	Route::get('change-email', ['as'=>'change_email', 'uses'=>'AccountController@change_email']);
        Route::post('current-email-notify', ['as'=>'current_email_notify', 'uses'=>'AccountController@current_email_notify']);
        Route::post('new-email-notify', ['as'=>'new_email_notify', 'middleware'=>['validate'],'uses'=>'AccountController@new_email_notify']);
               
        Route::get('bank_detail', ['as'=>'bank_detail', 'uses'=>'AccountController@bank_detail']);

        Route::post('relogin-bank', ['as'=>'relogin_bank', 'uses'=>'AccountController@relogin_bank']);
        Route::post('check-relogin-bank', ['as'=>'check_relogin_bank', 'uses'=>'AccountController@check_relogin_bank']);
        Route::post('send-otp-bank', ['as'=>'send_otp_bank', 'uses'=>'AccountController@send_otp_bank']);
        Route::post('varify-otp-bank', ['as'=>'varify_otp_bank', 'uses'=>'AccountController@varify_otp_bank']);
        Route::post('setbanksession', ['as'=>'setbanksession','uses'=>'AccountController@setbanksession']);
       
        Route::get('add_bank_detail', ['as'=>'add_bank_detail','uses'=>'AccountController@add_bank_detail']);
        Route::post('save-bank-detail', ['as'=>'save_bank_detail', 'middleware'=>['validate'],'uses'=>'AccountController@save_bank_detail']);
        Route::post('update-bank-detail', ['as'=>'update_bank_detail', 'uses'=>'AccountController@update_bank_detail']);
        Route::post('find-ifsc', ['as'=>'find_ifsc', 'uses'=>'AccountController@find_ifsc']);
        Route::post('change-status', ['as'=>'change_status', 'uses'=>'AccountController@change_status']);
        Route::post('remove-bank', ['as'=>'remove_bank', 'uses'=>'AccountController@remove_bank']);
       
        /*  virob ambika  */
		
		/* Change Mobile for virob - subin */
		Route::get('change-mobile', ['as'=>'change-mobile', 'uses'=>'AccountController@changemobile']);
		Route::post('send-email', ['as'=>'send-email', 'uses'=>'AccountController@changemobilesend']);
		Route::get('my-orders', ['as'=>'my-orders', 'uses'=>'AccountController@my_orders']);	
		Route::post('my-orders-search', ['as'=>'my-orders-search', 'uses'=>'AccountController@my_orders_search']);
		Route::post('my-orders-details/{order_code}', ['as'=>'my-orders-details', 'uses'=>'AccountController@my_orders_details']);
		Route::post('my-orders-cancel/{order_code}/{status}', ['as'=>'my-orders-cancel', 'uses'=>'AccountController@my_orders_cancel']);
		Route::get('my-orders-pdf', ['as'=>'my-orders-pdf', 'uses'=>'AccountController@my_orders_pdf']);
		Route::post('order-ratings-feedbacks', ['as'=>'order-ratings-feedbacks','middleware'=>['validate'], 'uses'=>'AccountController@order_ratings_feedbacks']);

		/* End Change Mobile for virob - subin */
	});		
}); 