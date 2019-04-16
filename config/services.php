<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

	'ifsc' => [
        'key' => '6af0d32a35mshabca09a19e5932cp18fcdcjsn8e5a9011ad4f',
        'url' => 'https://banksindia.p.rapidapi.com/',
        'region' => 'india',
		'provider'=>[
			'name'=> 'RabidAPI',
			'website'=> 'https://rapidapi.com/',
			'account'=> 'vbadmteam18@gmail.com'
		]
    ],	
    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],	
	'sendgrid'=>[
        'url'=>'https://api.sendgrid.com/api/mail.send.json',
        'api_user'=>'Virob',
        'api_key'=>'ejugiter@123'
    ],		
	'google'=>[
		'map_api_key'=>'AIzaSyBSv4zkc1apoAfsL71cTC1od4tOzNaMgJA',
        'api_key'=>'AIzaSyCIMNDfl2tZYg-9Y3GMhN4coNEqYtw_QTs',
        'fcm_url'=>'https://fcm.googleapis.com/fcm/send',
		'captcha'=>['acc'=>'virob.social@gmail.com','pass'=>'ejugiter@123','recaptcha_site_key'=>'6LcW3HcUAAAAACHBy2-bd7YLndtZvcCwEIPk_gNt','recaptcha_secret_key'=>'6LcW3HcUAAAAADqpVP4837oN6ROngZBlCo8Q1Li-'],
    ],	
	'sms'=>[
        'user'=>'virobon',
        'key'=>'936f41a869XX',
        'senderid'=>'VIRONL',
        'url'=>'sms.virob.com/submitsms.jsp?',
    ],	
	'api'=> [
		'url' =>'http://localhost/dsvb_portal/api/v1/',
	],
	'localApi'=> [
		'url' =>'http://localhost/dsvb_portal/api/v1/',
	],	
];
