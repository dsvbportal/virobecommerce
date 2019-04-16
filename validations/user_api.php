<?php
return [
	'redeem'=>[
        'store'=>[
            'search'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
				],
                'LABELS'=>[
                    'store_code'=>'general.label.merchant_id',                    
                ],
				'MESSAGES'=>[
                    'store_code.required'=>'Please enter a valid Merchant ID',                    
                    'store_code.exists'=>'Invalid QR Code/Merchant ID',                    
                ],
            ]
        ],
        'set-bill-amount'=>[
            'RULES'=>[
                'amount'=>'required|numeric|greater:10'
            ],
            'LABELS'=>[
                'amount'=>'user/account.amount'
            ],
			'MESSAGES'=>[
                'amount.required'=>'Please enter an amount',
                'amount.numeric'=>'Not Valid Amount',
                'amount.greater'=>'Bill amount must be greater than &#8377; 10',
            ]
        ],  
		'wallet-validate'=>[
            'RULES'=>[
                'wallet'=>'required|in:'.implode(',', array_keys(config('constants.BONUS_WALLET'))),
                'amount'=>'required_if:wallet,'.implode(',', array_keys(config('constants.BONUS_WALLET'))).'|numeric|greater:1',    
				'opayment_type'=>'required|in:'.implode(',', array_keys(config('constants.REDEEM_PAYMENT_TYPES'))),
            ],
            'LABELS'=>[
                'wallet'=>'user/account.wallet',
                'amount'=>'user/account.amount',
            ],
            'MESSAGES'=>[
                'wallet.required'=>'Select wallet.',
                'amount.required_if'=>'Enter amount.',
                'opayment_type.required'=>'The payment type field is required.',
            ],
        ],
		'confirm'=>[
            'RULES'=>[
                'code'=>'required|regex:/^[1-9]{1}[0-9]{5}$/',
            ],
            'LABELS'=>[
                'code'=>'OTP'
            ],
			'MESSAGES'=>[
                'code.required'=>'Please enter OTP',
                'code.regex'=>'Not Valid OTP',               
            ]
        ], 
    ],
	'pay'=>[
        'store'=>[
            'search'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
				],
                'LABELS'=>[
                    'store_code'=>'general.label.merchant_id',                    
                ],
				'MESSAGES'=>[
                    'store_code.required'=>'Please enter Merchant ID',                    
                    'store_code.exists'=>'Invalid QR Code/Merchant ID',                    
                ],
            ]
        ],
        'set-bill-amount'=>[
            'RULES'=>[
                'amount'=>'required|numeric|greater:99|lesser:500001'
            ],
            'LABELS'=>[
                'amount'=>'user/account.amount'
            ],
			'MESSAGES'=>[
                'amount.required'=>'Please enter an amount',
                'amount.numeric'=>'Not Valid Amount',
                'amount.greater'=>'Enter an amount between 100.00 and 500,000.00 INR',
                'amount.lesser'=>'Enter an amount between 100.00 and 500,000.00 INR',
            ]
        ],
        'get-payment-types'=>[
            'RULES'=>[
                //'auth_type'=>'required|regex:/^[0-9]$/',
                'auth_type'=>'required|in:1,2',
                'security_pin'=>'required_if:auth_type,1|security_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
            'LABELS'=>[
                'auth_type'=>'general.label.auth_type',
                'profile_pin'=>'general.profile_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
			'MESSAGES'=>[
                'auth_type.required'=>'Select Authentication Type.',
                'auth_type.in'=>'Invalid Authentication Type.',
                'security_pin.required_if'=>'Security Pin Required.',
                'security_pin.security_pin'=>'Invalid Security Pin.',
                'auth_status.required_if'=>'Authentication Required.',
            ],
        ],
        'get-payment-info'=>[
            'RULES'=>[
                'payment_mode'=>'required|in:'.implode(',', array_keys(config('constants.PAYMENT_MODES'))),
            ],
            'LABELS'=>[
                'payment_mode'=>'withdrawal.labels.payment_type'
            ],
			'MESSAGES'=>[
                'payment_mode.required'=>'Please select a Payment Mode',
                'payment_mode.in'=>'Invalid Payment Type'
            ]
        ],        
    ],
	'cashback'=>[
	    'store'=>[
	        'search'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
				],
                'LABELS'=>[
                    'store_code'=>'general.label.merchant_id',                    
                ],
				'MESSAGES'=>[                         
					'store_code.required'=>'Please enter Merchant ID',                    
                    'store_code.exists'=>'Invalid QR Code/Merchant ID',       
                ],
            ],
	    ],
		'set-bill-amount'=>[
			'RULES'=>[
				'bill_amount'=>'required|numeric|greater:10',
			],
			'LABELS'=>[
				'bill_amount'=>'user/account.amount',
			],
			'MESSAGES'=>[
				  'bill_amount.required'=>'Please enter an amount',
                  'bill_amount.numeric'=>'Not Valid Amount',
                  'bill_amount.greater'=>'Bill amount must be greater than &#8377; 10',
			],
	    ],
		'confirm'=>[
            'RULES'=>[
                'code'=>'required|regex:/^[1-9]{1}[0-9]{5}$/',
            ],
            'LABELS'=>[
                'code'=>'OTP'
            ],
			'MESSAGES'=>[
                'code.required'=>'Please enter OTP',
                'code.regex'=>'Invalid OTP, Please try again',               
            ]
        ], 
	],
	'online-stores'=>[
        'details'=>[
            'RULES'=>[
                'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,1,status,1,is_approved,1',
            ],
            'LABELS'=>[
                'store_code'=>'general.outlet_code',
            ]
        ]
    ],
	'favourite'=>[
        'store'=>[
            'add'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,status,1,is_approved,1,is_deleted,0',
                ],
                'LABELS'=>[
                    'store_id'=>'general.outlet'
                ]
            ],
        ],
	],
	'store'=>[
		'like'=>[
            'RULES'=>[
                'status'=>'required|in:0,1'
            ],
            'MESSAGES'=>[
                'status.required'=>'Parameter missing',
                'status.in'=>'Invalid parameter'
            ],
        ],
	],
    'set-location'=>[
        'RULES'=>[
            'locality_id'=>'required|numeric|exists:'.config('tables.LOCATION_POPULAR_LOCALITIES').',locality_id,status,1',
        ],
        'LABELS'=>[
            'locality_id'=>'general.location'
        ]
    ],
	'login'=>[
        'RULES'=>[
            'username'=>'required',
            'password'=>'required|password',            
        ],
        'LABELS'=>[
            'username'=>'Current Password',
            'password'=>'Password',            
        ],
        'MESSAGES'=>[
            'username.required'=>'Please enter email/mobile number',
            'password.required'=>'Please enter password',
        ]
    ],
	'change-pwd'=>[
        'RULES'=>[
            'current_password'=>'required|password',
            'password'=>'required|password|different:current_password',
            'conf_password'=>'required|password|same:password',
        ],
        'LABELS'=>[
            'current_password'=>'Current Password',
            'password'=>'Password',
            'conf_password'=>'Confirm password',
        ],
        'MESSAGES'=>[
            'current_password.required'=>'Current password is required.',
            //'current_password.password'=>'New Password must be different than the Current Password.',
            'password.required'=>'New Password is required.',
            'conf_password.required'=>'Confirm Password is required.',
            'password.different'=>'Your new password cannot be same as old password.',
            'conf_password.same'=>'Passwords do not match. Please try again.',
        ]
    ],	
	'signup'=>[
        'signup'=>[
			'RULES'=>[
				'full_name'=>'required|full_name',
				'email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',                                 
				'password'=>'required|password',                                 
				'country'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1',            
				'mob_number'=>'required_with:country|db_regex:mobile_validation,location_countries,country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',
				//'agree_to_rec_offers'=>'boolean',            
			],
			'LABELS'=>[
				'full_name'=>'Full Name',
				'email'=>'E-Mail',
				'password'=>'Password',
				'conf_password'=>'Confirm Password',
				'agree_to_rec_offers'=>'I agree to receive exclusive offers and promotions from virob.',
				'agree_to_term_cond'=>'I have read & understood virob terms of usage and privacy policy.',
				'country'=>'Country',
				'mob_number'=>'Mobile',
			],
			'MESSAGES'=>[
				'full_name.required'=>'Please enter full name',
				'full_name.full_name'=>'Please enter valid full name',
				'email.required'=>'Please enter email id',
				'email.email'=>'Please enter a valid email Id',
				'password.required'=>'Please enter password',                                 
				'password.password'=>'Password cannot be less than 6 characters',                                 
				'country.required'=>'Please select country',
				'mob_number.required_with'=>'Please enter valid mobile number',
				'mob_number.db_regex'=>'Please enter valid mobile number',
			]
		],
		'verify-mobile'=>[
			'RULES'=>[        
				'country'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1',            
				'mob_number'=>'required_with:country|db_regex:mobile_validation,location_countries,country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',
			],
			'LABELS'=>[           
				'country'=>'Country Code',
				'mob_number'=>'Mobile',
			],
			'MESSAGES'=>[           
				'country.required'=>'Select Country',
				'mob_number.required_with'=>'Please enter valid mobile number.',
			],			
		],
		'confirm'=>[
			'RULES'=>[
				'code'=>'required|regex:/^[0-9]{6}$/'          
			],
			'LABELS'=>[
				'code'=>'Code',            
			], 
			'MESSAGES'=>[
				'code.required'=>'Please enter valid OTP',
				'code.regex'=>'OTP is invalid or expired'
			]
		],
    ],	
	'profile-settings'=>[

	   'profile'=>[
			'update'=>[
				'RULES'=>[
					'first_name'=>'required|firstname|min:3|max:50|',
					'last_name'=>'sometimes|lastname||min:3|max:50',
					'display_name'=>'required'.
					 ($session->has('userdata.uname') && request()->display_name!= $session->get('userdata.uname') ? 
					 '|unique:'.config('tables.ACCOUNT_MST').',uname' : ''),
					],
			'LABELS'=>[
				'first_name'=>'First Name',
				'last_name'=>'Last Name',
				'display_name'=>'Display Name',
			],			
			'MESSAGES'=>[
				'first_name.required'=>'Please Enter First Name ',
				'display_name.required'=>'Please Enter Display Name',				
			]
		]
	 ],
	  'security-pin'=>[
	         'save'=>[
                'RULES'=>[
                      'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
					  'confirm_security_pin'=>'required|max:4|same:security_pin',
                  ],
			      'LABELS'=>[
			          'security_pin'=>'Security Pin',
					  'confirm_security_pin'=>'Confirm Security Pin',
			     ],
				'MESSAGES'=>[
						'security_pin.required'=>'Enter Security PIN',
						'security_pin.regex'=>'Invalid Security PIN,Please try again',
						'security_pin.min'=>'PIN must be 4 digit number',
						'security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
						'confirm_security_pin.required'=>'Enter Confirm Security PIN',
                        'confirm_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                        'confirm_security_pin.same'=>'New PIN and Confirm PIN do not match, please try again.'
                ]
	      ],

			
		'verify'=>[
			'RULES'=>[
                      'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
                  ],
			      'LABELS'=>[
			          'security_pin'=>'security Pin',
			     ],
				'MESSAGES'=>[
						'security_pin.required'=>'Enter Security PIN',                   
						'security_pin.regex'=>'Invalid Security PIN,Please try again',			
						'security_pin.min'=>'Security PIN must be 4 digit number',			
						'security_pin.max'=>'Your Security PIN can\'t be longer than 4 digits',
                ]
            ],
		'reset'=>[
                'RULES'=>[
                      'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
					   'confirm_security_pin'=>'required|max:4|same:security_pin',
					   'code'=>'required|',
                  ],
			      'LABELS'=>[
			          'security_pin'=>'Security Pin',
					  'confirm_security_pin'=>'Confirm Security Pin',
			     ],
				 'MESSAGES'=>[
				        'code.required'=>'Enter Verification Code',
						'security_pin.required'=>'Enter Security PIN',
						'security_pin.regex'=>'Invalid Security PIN, please try again',
						'security_pin.min'=>'PIN must be 4 digit number',
						'security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
						'confirm_security_pin.required'=>'Enter Confirm Security PIN',                             
						'confirm_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
						'confirm_security_pin.same'=>'New PIN and Confirm PIN do not match, please try again'
	          ]
	     ],	  
		 'change'=>[
                'RULES'=>[
                    'current_security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
                    'new_security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4|different:current_security_pin',
                    'confirm_security_pin'=>'required|max:4|same:new_security_pin',
                ],
                'LABELS'=>[
                    'current_security_pin'=>'Current Security PIN',
                    'new_security_pin'=>'New Security PIN',
                    'confirm_security_pin'=>'Confirm Security PIN'
                ],
                'MESSAGES'=>[                  
                    'current_security_pin.required'=>'Enter Current Security PIN',                   
                    'current_security_pin.regex'=>'Invalid Security PIN,Please try again',                   
                    'current_security_pin.min'=>'PIN must be 4 digit number',                   
                    'current_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',                   
                    'new_security_pin.required'=>'Enter New Security PIN ',
                    'new_security_pin.regex'=>'Invalid Security PIN,Please try again',
                    'new_security_pin.min'=>'PIN must be 4 digit number',
                    'new_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                    'new_security_pin.different'=>'Your new PIN cannot be same as old PIN',
					'confirm_security_pin.required'=>'Enter Confirm Security PIN',
					'confirm_security_pin.profile_pin'=>'PIN must be 4 digit number',
                    'confirm_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                    'confirm_security_pin.same'=>'New PIN and Confirm PIN do not match, please try again',
                ]
          ]
	  ],
	   
	    'change-email'=>[

             'sendotp'=>[
                'RULES'=>[
                    'code'=>'required|regex:/^[0-9a-z]+$/',
                    'new_email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',      
                ],
                'LABELS'=>[
                    'code'=>'Profile Pin Session',
                    'new_email'=>'Email'
                ],
				'MESSAGES'=>[
				   'code.required'=>'Please Verification Code',
				   'new_email.required'=>'Please Enter New Email',
			    ]				
           ],
            'verify-otp'=>[
                'RULES'=>[
                    'code'=>'required'
                ],
                'LABELS'=>[
                    'code'=>'verification_code'
                ],
			   'MESSAGES'=>[
                    'code.required'=>'Please Enter Verification Code'
                ]
            ]
        ],
      'change-mobile'=>[
	        'sendotp'=>[
	             'RULES'=>[
					'code'=>'required|regex:/^[0-9a-z]+$/',
					 'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',  
                ],
                'MESSAGES'=>[
					'code.required'=>'Please Enter Verification Code',
					'mobile.required'=>'Please Enter Mobile Number',
                ]
	   
	       ],
		   'verify-otp'=>[
                'RULES'=>[
                    'code'=>'required'
                ],
                'LABELS'=>[
                    'code'=>'verification_code'
                ],
				 'MESSAGES'=>[
		            'code.required'=>'Please Enter Verification Code'
		      ],
            ]
	    ]
	],
	 'country-update'=>[
        'RULES'=>[
            'country_id'=>'required',            
        ],
        'LABELS'=>[
            'country_id'=>'Country',           
        ],        
    ],
	'send-login-otp'=>[
        'RULES'=>[
            'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',            
        ],
        'LABELS'=>[
            'mobile'=>'Mobile',           
        ],        
    ],
	'login-with-otp'=>[
        'RULES'=>[
            'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',  
			'otp'=>'required'
        ],
        'LABELS'=>[
            'mobile'=>'Mobile',           
            'otp'=>'OTP',           
        ],        
    ],
	'forgot_pwd'=>[
		'RULES'=>[
            'uname'=>'required|email|exists:'.config('tables.ACCOUNT_MST').',email,account_type_id,2,is_deleted,0,is_closed,0',  
        ],
        'LABELS'=>[
            'uname'=>'Email ID',           
        ], 
		'MESSAGES'=>[
		      'uname.required'=>'Please enter email id',
		      'uname.exists'=>'Please enter a valid e-mail address'
		],
	],
	'reset_pwd'=>[
	    'RULES'=>[
			'code'=>'required',
			'newpwd'=>'required:password',
		],
		'LABELS'=>[
			'code'=>'verification_code',
			'newpwd'=>'New Password',
		],
		'MESSAGES'=>[
		      'code.required'=>'Please enter verification code',
		      'newpwd.required'=>'Please enter new password'
		],
	],	

];


