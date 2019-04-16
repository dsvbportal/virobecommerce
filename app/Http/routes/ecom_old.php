<?php

Route::get('validate/lang/{langkey}', 'LangController@langLoad');
Route::get('/', ['as'=>'home', 'uses'=>'AccountController@home']);
Route::get('login', ['as'=>'login', 'uses'=>'AccountController@login']);
Route::post('checklogin', ['as'=>'checklogin', 'middleware'=>['validate'], 'uses'=>'AccountController@checklogin']); 
Route::post('forgot-pwd',['as'=>'forgot_pwd', 'middleware'=>['validate'], 'uses'=>'AccountController@forgot_password']);
Route::post('reset-pwd', ['as'=>'reset_pwd', 'middleware'=>['validate'], 'uses'=>'AccountController@reset_pwd']);

/* Page Data */
Route::post('get-page-data', ['as'=>'get-page-data', 'uses'=>'DataController@pageData']);
Route::post('main-categories',['as'=>'main_categories', 'uses'=>'DataController@childrensCategories']);

/* Product */
Route::group(['prefix'=>'product', 'as'=>'product.'], function()
{ 		
	Route::get('list', ['as'=>'list', 'uses'=>'ProductController@productList']);		
});	

Route::group(['middleware'=>['ecomauth', 'validate']], function()
{	
    Route::post('logout', ['as'=>'logout', 'uses'=>'AccountController@logout']);
	Route::group(['prefix'=>'account', 'as'=>'account.'], function()
    { 		
	    Route::get('profile', ['as'=>'profile', 'uses'=>'AccountController@profile']);
		Route::post('update', ['as'=>'update',  'middleware'=>['validate'], 'uses'=>'AccountController@updateProfile']);	
	    Route::get('change-pwd', ['as'=>'change-pwd', 'uses'=>'AccountController@changepassword']);	
	    Route::post('update-pwd', ['as'=>'update-pwd', 'middleware'=>['validate'], 'uses'=>'AccountController@updatepwd']);	
	});	
}); 