<?php
return [
    'test'=>[
        'sms'=>false,
        'email'=>false,
        'email_view'=>'emails.layout.userlayout',
        'notification'=>true,
        'check_verification'=>false
    ],
    'mobile_verifycode'=>[
        'sms'=>true,
        'email'=>false,
        'email_view'=>'                  ',
        'notification'=>false,
        'check_verification'=>false
    ],   
   'change_password'=>[
        'sms'=>false,
        'email'=>true,
        'email_view'=>'emails.accounts.change_password',
        'notification'=>false,
        'check_verification'=>false
    ],
   'verify_new_email'=>[
       'sms'=>false,
       'email'=>true,
      //'email_view'=>'emails.accounts.change_email',
       'notification'=>false,
       'check_verification'=>false
    ],
	'set_profile_pin'=>[
        'sms'=>false,
        'email'=>true,
        'email_view'=>'emails.accounts.set_profile_pin',
        'notification'=>false,
        'check_verification'=>true
    ],
    'change_profile_pin'=>[
        'sms'=>false,
        'email'=>true,
       // 'email_view'=>'emails.accounts.change_profile_pin',
        'notification'=>false,
        'check_verification'=>true
    ],
	'account_verification'=>[
        'sms'=>true,
        'email'=>false,
        'email_view'=>'emails.affiliate.signup.create_user',
        'notification'=>false,
        'check_verification'=>false
    ],  
	'forgotpwd_verifycode'=>[
	  'sms'=>false,
       'email'=>true,
       //'email_view'=>'emails.accounts.change_email',
       'notification'=>false,
       'check_verification'=>false
	],	
	'affiliate' => [
		'signup'=> [
			'activated'=>[
			   'sms'=>false,
			   'email'=>true,
			   'email_view'=>'emails.affiliate.signup.activated',
			   'notification'=>false,
			   'check_verification'=>false	
		   ]
		],
		'affsignup_activated'=>[
		  'sms'=>false,
		   'email'=>true,
		   'email_view'=>'emails.affiliate.signup.activated',
		   'notification'=>false,
		   'check_verification'=>false	
		],
		'account_verification'=>[
		  'sms'=>false,
		   'email'=>true,
		   'email_view'=>'emails.affiliate.signup.create_user',
		   'notification'=>false,
		   'check_verification'=>false	
		],
	],
	 
];