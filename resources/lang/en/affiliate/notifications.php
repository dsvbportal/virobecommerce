<?php
return 
    ['test' => [
        'sms'=>'Hi, its working',
        'email'=>[
            'subject'=>'Hi, its working',
            'content'=>'Hi, its working'
        ],
        'notification'=>[
            'title'=>'Test Notification',
            'msg'=>'Test Notification from Cashback'
        ]
    ], 
	'welcome' => 'sdfhashd asdflkjahsd fasjdfh lakjdfshjhaldfsjh',
	'settings'=>[  
		'create_security_pin_notification'=>[  
			'email'=>[
				'subject'=>'Security Pin reset notification',
			],
		],
		'forgot_security_pin_verify'=>[  
			'email'=>[
				'subject'=>':vcode is your :site_name account recovery code',
			],
		],
		'mobile_verification'=>[
			'sms'=>':code is your One Time Password to verify your mobile at :site_name and OTP will expire 5 minutes or once used',
		],
		'securitypwd_resetnotify'=>[  
			'email'=>[
				'subject'=>'Security Pin reset notification',
			],
		],
		'email_verification'=>[  
			'email'=>[
				'subject'=>'Account Email Verfication',
			],
		],
		'change_email_verification'=>[  
			'email'=>[
				'subject'=>'Email Change Request',
			],
		],
		'change_email_verification_otp'=>[  
			'email'=>[
				'subject'=>'Email Verfication OTP',
			],
		],
		'change_email_notification'=>[  
			'email'=>[
				'subject'=>'Email address updated successfully',
			],
		],
		'change_mobile_verification'=>[  
			'mobile'=>[
				'subject'=>'Change Mobile Verification',
			],
		],
		'change_mobile'>[
		  'mobile'=>[
				'subject'=>'Change Mobile Verification',
			],
		],
		'change-mobile'=>[
			'email'=>[
				'subject'=>'Mobile Number Updated successfully!',
			]
		],
		'reset_pwd_notification'=>[
		    'email'=>[
				'subject'=>'Password Reset Notification',
			],
		],
	],
	'pwdreset_notification'=>[       
        'email'=>'Password Reset Notification',        
    ],
	'sponsors_signup_notification'=>[       
        'email'=>[
            'subject'=>'An new affiliate has accepted your Invitation',
        ],        
    ],
	'account_verification'=>[
        'sms'=>'Welcome to :site_name. Affiliate Program!',
        'email'=>[
            'subject'=>'Verify Your Email Address',
        ],
        'notification'=>[
            'title'=>'Test Notification',
            'msg'=>'Test Notification from Cashback'
        ]
    ],
	'account_upgraded'=>[
        'sms'=>'Welcome to :site_name. Affiliate Program!',
        'email'=>[
            'subject'=>'Welcome to :site_name',
        ],
        'notification'=>[
            'title'=>'Test Notification',
            'msg'=>'Test Notification from Cashback'
        ]
    ],	
	'user_reg_msg'=>[
        'sms'=>'Welcome to :site_name. Start shopping and earn Cashback!. Fill out your profile to get verified for signup bonus.',
        'email'=>[
            'subject'=>'Welcome to Virob',
            'content'=>' Welcome to Virob'
        ],
        'notification'=>[
            'title'=>'Test Notification',
            'msg'=>'Test Notification from Cashback'
        ]
    ],
    'user_reg_msg'=>[
        'sms'=>'Welcome to :site_name. Start shopping and earn Cashback!. Fill out your profile to get verified for signup bonus.',
        'email'=>[
            'subject'=>'Welcome to Virob',
            'content'=>' Welcome to Virob'
        ],
        'notification'=>[
            'title'=>'Test Notification',
            'msg'=>'Test Notification'
        ]
    ],
    'mobile_verifycode'=>[
        'sms'=>':code is your verification code to verify your Mobile number on :site_name and it is valid for 5 minutes. Happy welcome to Virob. Complete your profile and get bonus credits.',
        'email'=>null,
        'notification'=>null
    ],
    'change_email_otp'=>[
        'sms'=>':code is your One Time Password to change your email ID for :site_name and OTP will expire after use or after 5 minutes',
        'email'=>[
            'subject'=>'OTP to Change Email ID',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Change Email ID',
            'msg'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
    'deal_purchase'=>[
        'sms'=>'You have Purchased deal successfully',
        'email'=>[
            'subject'=>'Deal purchase confirmation.',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'Deal purchase confirmation.',
            'msg'=>'You have Purchased deal successfully'
        ]
    ],
    'change_mobile_otp'=>[
        'sms'=>':code is your verification code to change your Mobile No. on :site_name and it is valid for 5 minutes. This is important for safety of your account.',
        'email'=>[
            'subject'=>'OTP to Change Mobile No.',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Change Mobile No.',
            'msg'=>':code is your verification code to change your Mobile No. on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
    'verify_new_mobile'=>[
        'sms'=>':code is your verification code to change your Mobile No. on :site_name and it is valid for 5 minutes. This is important for safety of your account.',
        'email'=>[
            'subject'=>'OTP to Verify Mobile No.',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Verify Mobile No.',
            'msg'=>':code is your verification code to change your Mobile No. on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
    'verify_email'=>[
        'sms'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.',
        'email'=>[
            'subject'=>'OTP to Verify Email ID',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Verify Email ID',
            'msg'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
	'verify_mobile'=>[
        'sms'=>':code is your verification code to verify your Mobile number on :site_name and it is valid for 5 minutes. This is important for safety of your account.',
        'email'=>[
            'subject'=>'OTP to Verify Mobile',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Verify Mobile',
            'msg'=>':code is your verification code to verify your Mobile number on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
    'user_contact_us'=>[
        'sms'=>'',
        'email'=>[
		'subject'=>'User Enquiry: :subject from :from',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Verify Email ID',
            'msg'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],   
    'verify_new_email'=>[
        'sms'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.',
        'email'=>[
            'subject'=>'OTP to Verify Email ID',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'OTP to Verify Email ID',
            'msg'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
    'forgotpwd_verifycode'=>[
        'sms'=>':code is your One Time Password to reset your Password for :site_name and OTP will expire after use or after 5 minutes.',
        'email'=>[
            'subject'=>'Virob Password Reset Request',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'Virob Password Reset Request',
            'msg'=>':code is your verification code to change your Email ID on :site_name and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],
	'customer_promotional'=>[
        'sms'=>'',
        'email'=>[
            'subject'=>'Singup Invite Email',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'Singup Invite Email',
            'msg'=>''
        ]
    ],
    'change_password'=>[
        'sms'=>'Hurrah! Your password has been changed successfully for :site_name. Please be sure to memorize it or note it in a safe place.',
        'email'=>[
            'subject'=>'Your password was successfully changed',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'Your password was successfully changed',
            'msg'=>':site_name accounts password has been reset successfully.'
        ]
    ],
    'forgot_profile_pin'=>[
        'sms'=>':code is your One Time Password to reset your security PIN for :site_name and OTP will expire after use or after 5 minutes',
        'email'=>[
            'subject'=>'Forgot Security PIN',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'Forgot Security PIN',
            'msg'=>':code is your verification code to reset your Security PIN on :site_name  and it is valid for 5 minutes. This is important for safety of your account.'
        ]
    ],    
	'refer_frnd'=>[
        'sms'=>'',
        'email'=>[
            'subject'=>':fname invited to you join Virob and get exciting benefits', 			 
            'content'=>''
        ],
        'notification'=>[
            'title'=>':fname invited to you join Virob and get exciting benefits',
            'msg'=>''
        ]
    ],
	'fund_tranfer_verify_otp'=>[
        'email'=>[
            'subject'=>'TAC Code for Fund Transfer',
        ]
	],
	'fundtransfer_fromuser'=>[       
        'email'=>'Fund Transfer Notification',        
    ],
	'fundtransfer_touser'=>[      
        'email'=>'Payment Received :: :to_transaction_id',      
    ],
	'withdraw_money'=>[
        'sms'=>'',
        'email'=>[
            'subject'=>' We\'re processing your request to money transfer',
            'content'=>''
        ],
        'notification'=>[
            'title'=>' We\'re processing your request to money transfer',
            'msg'=>''
        ]
    ],
    'set_profile_pin'=>[
        'sms'=>'Hurrah! Your security PIN has been changed successfully for :site_name. Please be sure to memorize it or note it in a safe place.',
        'email'=>[
            'subject'=>'Security PIN Updated',
            'content'=>null
        ],
        'notification'=>[
            'title'=>'Security PIN Updated',
            'msg'=>'Test Notification from Cashback'
        ]
    ],
    'change_profile_pin'=>[
        'sms'=>'Hurrah! Your security PIN has been changed successfully for :site_name. Please be sure to memorize it or note it in a safe place.',
        'email'=>[
            'subject'=>'Security PIN successfully changed ',
            'content'=>''
        ],
        'notification'=>[
            'title'=>'Security PIN successfully changed ',
            'msg'=>'Your Security PIN with :site_name  has been updated successfully'
        ]
    ],
	'signup' => [
		'activated'=> [
			'email'=>[
				'subject'=>'Welcome to :site_name - Affiliate Program',
			], 
		]
	],
];