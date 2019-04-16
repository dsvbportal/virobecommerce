<?php
return 
    [
	'fundtransfer_fromuser'=>[       
        'email'=>'Fund Transfer Notification',        
    ],
	'fundtransfer_touser'=>[      
        'email'=>'Payment Received :: :to_transaction_id',      
    ],
	'settings'=>[  
	    'change_email_verification'=>[  
			'email'=>[
				'subject'=>'Email Change Request',
			],
		],
		'change_email_verification_otp'=>[  
			'email'=>[
				'subject'=>'Email Verification OTP',
			],
		],
		'change_email_notification'=>[  
			'email'=>[
				'subject'=>'Email address updated successfully',
			],
		],
	   'change_mobile_verification'=>[  
			'mobile'=>[
				'subject'=>'Mobile Number Update Request',
			],
		],
        'mobile_verification'=>[
			'sms'=>':code is your One Time Password to verify your mobile at :site_name and OTP will expire 5 minutes or once used',
		],
       'change-mobile'=>[
			'email'=>[
				'subject'=>'Mobile Number Updated successfully!',
			]
		],
	   'reset_pwd_notification'=>[
		    'email'=>[
				'subject'=>'Password successfully changed',
			],
		],
	  'forgot_security_pin_verify'=>[  
			'email'=>[
				'subject'=>'Virob : Security PIN Reset Request',
			],
		],
		'securitypwd_resetnotify'=>[  
			'email'=>[
				'subject'=>'Security Pin reset notification',
			],
		],
		'pwdreset_notification'=>[       
              'email'=>'Password Reset Notification',        
         ],
	],
	 'merchant'=>[
		   'email'=>[
					'subject'=>'Welcome to PayGyft',
	       ],
		],
];