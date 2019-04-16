<?php
return [ 	
	'update_contact_us'=>[
        'RULES'=>[
        	'name'=>'required',
        	'email'=>'required|email', 
        	'website'=>'required',
            'subject'=>'required',                                             
            'message'=>'required',            
        ],
        'LABELS'=>[
        	'name'=>'',
        	'email'=>'', 
        	'website'=>'',
            'subject'=>'',                                             
            'message'=>'', 
                   
        ],
        'MESSAGES'=>[
            'name.required'=>'Name is required',
            'email.required'=>'Email is required', 
            'email.email'=>'Invalid Email',
            'website.required'=>'Website is required',
            'subject.required'=>'Subject is required', 
            'message.required'=>'Message is required',
        ]
    ],	
	
	'checklogin'=>[
        'RULES'=>[
            'username'=>'required|email',
            'password'=>'required|password',            
        ],
        'LABELS'=>[
            'username'=>'Username',
            'password'=>'Password',            
        ],
        'MESSAGES'=>[
            'username.required'=>'Please enter email/mobile number',
            'password.required'=>'Please enter password',
            'password.password'=>'Password cannot be less than 6 characters.',
            'username.email'=>'The email you entered is not a valid email address.',
        ]
    ],
    'sign_up_save'=>[
			'RULES'=>[
				'full_name'=>'required|full_name|min:3',
				'full_name'=>'required|min:3',
				//'full_name'=>'required|signup_first_name|signup_last_name',
				//'full_name'=>'required',
				'email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',
				//'password'=>'required|min:6|max:16|regex:/^[A-Za-z0-9]*$/',                                 
				'password'=>'required|min:6|max:16',                                 
				 'country'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1',
				 // 'mob'=>'required_with:country|db_regex:mobile_validation,location_countries,country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0,is_closed,0',	
									
			     'mob_number'=>'required_with:country|db_regex:mobile_validation,location_countries,country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0,is_closed,0',
				
				'temsandcondition'=>'required',				
							
			],	
	     'LABELS'=>[
            'full_name'=>'',
            'email'=>'',
            'password'=>'',
            'country'=>'', 
             // 'mob'=>'mob',
            'mob_number'=>'r',				
			'temsandcondition'=>'',            
            ],		
			'MESSAGES'=>[
				'full_name.required'=>'Please enter full name',
				'full_name.full_name'=>'Please enter valid full name',
				//'full_name.min'=>'Full name must be at least 3 characters',
				// 'full_name.signup_first_name'=>'Enter your valid first name',
				// 'full_name.signup_last_name'=>'Enter your valid last name',
				'email.required'=>'Please enter email id',
				'email.email'=>'Please enter a valid email Id',
				'email.max'=>'Your email can\'t be longer than 62 characters',
				'email.unique'=>'You already have an account with that email',
				'password.required'=>'Please enter password', 
				'password.min'=>'Password must be at least 6 characters',
				'password.max'=>'Your password can\'t be longer than 16 characters',	
				'password.regex'=>'The password that you\'ve entered is incorrect',	
				'country.required'=>'Please select country',				
				/* 'referral_code.required'=>'Referral Code is required', */
				'referral_code.regex'=>'Invalid Referral Code, Please try again',
				'referral_code.min'=>'The Referral Code must be 6 characters',
				'referral_code.max'=>'Referral Code can\'t be longer than 6 characters',
				'referral_code.exists'=>'Your Referral Code was entered incorrectly. Please enter It again',
				'mob_number.required_with'=>'Please enter valid mobile number',
				'mob_number.regex'=>'Please enter valid mobile number',				
				//'mob_number.unique'=>'This phone number has already been used',				
				'mob_number.unique'=>'Mobile number has already been taken',
				'temsandcondition.required'=>'please enter tems and condition',				
			], 
			
		],
		'sign_up_varification'=>[
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

	'forgot_pwd'=>[
		'RULES'=>[ 
            //'uname'=>'required|email|exists:'.config('tables.ACCOUNT_MST').',email,account_type_id,2,is_deleted,0,is_closed,0',  
            'uname'=>'required|email|exists:'.config('tables.ACCOUNT_MST').',email,account_type_id,'.config('constants.ACCOUNT_TYPE.USER').',is_deleted,0,is_closed,0',  
        ],
        'LABELS'=>[
            'uname'=>'Enter Email Address',           
        ], 
		'MESSAGES'=>[
		      'uname.required'=>'Please enter email id',
		      'uname.exists'=>'Please enter a valid e-mail address'
		],
	],
	'reset_pwd'=>[
	    'RULES'=>[
			'restoken'=>'required|regex:/^[a-f0-9]{32}$/',	
			'newpwd'=>'required|password|min:6|max:16',  
			'conf_newpwd'=>'required|max:16|same:newpwd',
		],
		'LABELS'=>[
			'restoken'=>'Token',
			'newpwd'=>'New Password',
			'conf_newpwd'=>'Confirm Password',
		],
		'MESSAGES'=>[
		    'restoken.required'=>'Token is required',
			'restoken.regex'=>'Token is invalid or expired',		 
		    'newpwd.required'=>'Please enter password',                                 
			'newpwd.password'=>'The password that you\'ve entered is incorrect.', 
			'newpwd.min'=>'Password must be at least 6 characters',
			'newpwd.max'=>'Your password can\'t be longer than 16 characters',
			'conf_newpwd.required'=>'Confirm Password is required',
            'conf_newpwd.max'=>'Your Confirm password can\'t be longer than 16 characters',
            'conf_newpwd.same'=>'Passwords do not match',
		],
	],	
	'account'=>[
	    /* 'image-upload'=>[
			'RULES'=>[
				'attachment'=>'required|file|mimes:jpg,jpeg,png|max:1024'
			],
			'LABELS'=>[
				'attachment'=>'user/account.profile_image'
			],
			'MESSAGES'=>[
				'attachment.required'=>'Profile Image is required',
				'attachment.file'=>'Please select a file in the format (jpg,jpeg,png)',
				'attachment.mimes'=>'Please select a file in the format (jpg,jpeg,png)',
				'attachment.max'=>'Profile Image can\'t be longer than 1 MB.',
			]
		], */
         'find_ifsc' =>[
        'RULES'=>[                        
            'ifsc_code'=>'required',                                
        ],
        'LABELS'=>[
            'ifsc_code'=>'IFSC Code',        
        ],
        'MESSAGES'=>[        	          
            'ifsc_code.required'=>'IFSC Code Required',      
        ]
    ],
      
		'save_bank_detail'=>[
        'RULES'=>[
            'acc_holder_name'=>'required',
            'acc_number'=>'required',             
            'confirm_acc_number'=>'required|same:acc_number',            
            'ifsc_code'=>'required',
            'tems&conditon'=>'required',            
                    
        ],
        'LABELS'=>[
            'acc_holder_name'=>'Account Holder Name',
            'acc_number'=>'Account Number', 
            'confirm_acc_number'=>'Confirm Account Number',          
            'ifsc_code'=>'IFSC Code', 
            'tems&conditon'=>'terms and condition',

        ],
        'MESSAGES'=>[
        	'acc_holder_name.required'=>'Account Holder Name Required',
            'acc_number.required'=>'Account Number Required',
            'confirm_acc_number.required'=>'please Confirm Your Account Number',
            'confirm_acc_number.same'=>'Account Number is not matching',           
            'ifsc_code.required'=>'IFSC Code Required',
            'tems&conditon.required'=>'please agree the terms and condition',      
        ]
    ],
    
		'update_bank_detail'=>[
        'RULES'=>[
            'acc_holder_name'=>'required',
            'acc_number'=>'required', 
            'confirm_acc_number'=>'required|same:acc_number',           
            'ifsc_code'=>'required',
            'tems&conditon'=>'required',

                    
        ],
        'LABELS'=>[
            'acc_holder_name'=>'Account Holder Name',
            'acc_number'=>'Account Number', 
            'confirm_acc_number'=>'Confirm Account Number',          
            'ifsc_code'=>'IFSC Code', 
            'tems&conditon'=>'terms and condition',       
        ],
        'MESSAGES'=>[
        	'acc_holder_name.required'=>'Account Holder Name Required',
            'acc_number.required'=>'Account Number Required',
            'confirm_acc_number.required'=>'please Confirm Your Account Number',
            'confirm_acc_number.same'=>'Account Number is not matching',           
            'ifsc_code.required'=>'IFSC Code Required',   
            'tems&conditon.required'=>'please agree the terms and condition',    
        ]
    ],	
		'check-pincode'=>[
			'RULES'=>[
				'pincode' => 'required',				
			 ],    
			'LABELS'=>[
				'pincode' => 'Pincode',			
			],
			 'MESSAGES'=>[
				'pincode.required'=> 'Postal Code is required',		
			]
		],	
		'save-address'=>[
			'RULES'=>[
				'address.postal_code' => 'required',
				'address.flat_no' => 'required',					
				'address.landmark' => 'required',					
				'address.city_id' => 'required',
				'address.state_id' => 'required',
				'address.address_type' => 'required',
				'address.alternate_mobile' => 'sometimes|max:10',
				'address.is_default' => 'sometimes|in:0,1',
				//'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',  
			 ],    
			'LABELS'=>[
				'address.postal_code' => 'Pincode',
				'address.flat_no' => 'Street Address',			
				'address.landmark' => 'Landmark',
				'address.city_id' => 'City',
				'address.state_id' => 'State',
				'address.address_type' => 'Address Type',
				'address.alternate_mobile' => 'Alternate Mobile No',
				'address.is_default' => '',
			],
			 'MESSAGES'=>[
				'address.postal_code.required'=> 'Postal Code is required',
				'address.flat_no.required'=> 'Street Address is required',			   
				'address.landmark.required'=> 'Landmark is required',			   
				'address.city_id.required'=> 'City is required',
				'address.state_id.required'=> 'State is required',
				'address.address_type.required'=> 'Address Type is required',
				'address.alternate_mobile.max'=> 'Mobile number can\'t be longer than 10 digits',
				'address.is_default.in'=> 'Invalid value',
			]
	    ],	
	    'update'=>[
			'RULES'=>[
				'first_name'=>'required|firstname|min:3|max:50',
				'last_name'=>'required|lastname|min:1|max:50',					
				'display_name'=>'required|min:6|regex:/^[a-z0-9]*$/'.(isset($userInfo->uname) ? request()->get('display_name') != $userInfo->uname ? '|unique:'.config('tables.ACCOUNT_MST').',uname,NULL,account_id,is_deleted,0,is_closed,0' : '' : ''),
				'last_name'=>'required|lastname|min:1|max:50',		
				'gender'=>'required|in:1,2,3',
                'dob'=>'required|date_format:Y-m-d|before:'.date('Y-m-d', strtotime('-18 years'))
			],
			'LABELS'=>[
				'first_name'=>'First Name',
				'last_name'=>'Last Name',
				'display_name'=>'Username',
				'gender'=>'Gender',
                'dob'=>'Date Of birth'
			],			
			'MESSAGES'=>[
				'first_name.required'=>'Enter first name',
				'last_name.required'=>'Enter last name',
				'display_name.required'=>'Enter username',				
				'last_name.min'=>'The lastname must be at least 1 character long',				
				'display_name.min'=>'The username must be at least 6 character long',	
                'gender.required'=>'Select your gender',
                'gender.in'=>'Gender is Invalid',
                'dob.required'=>'Please enter a date of birth',
                'dob.date_format'=>'Please enter a date in the format (YYYY-mm-dd)',
                'dob.before'=>'Please select a date before 18 years'				
			],
		],
		'update-pwd'=>[
			'RULES'=>[
				'current_password'=>'required|min:6|max:16',
				'password'=>'required|min:6|max:16|different:current_password|password',
				'conf_password'=>'required|max:16|same:password',
			],
			'LABELS'=>[
				'current_password'=>'Current Password',
				'password'=>'Password',
				'conf_password'=>'Confirm password',
			],
			'MESSAGES'=>[
				'current_password.required'=>'Current Password is required',
				'current_password.min'=>'Current Password cannot be less than 6 characters',
				'current_password.max'=>'Your Current password can\'t be longer than 16 characters',
				'password.required'=>'New Password is required',
				'password.min'=>'New Password cannot be less than 6 characters',
				'password.max'=>'Your New Password can\'t be longer than 16 characters',
				'password.password'=>'Invalid Password',
				'password.different'=>'Your New Password cannot be same as old password',
				'conf_password.required'=>'Confirm Password is required',
				'conf_password.max'=>'Your Confirm password can\'t be longer than 16 characters',
				'conf_password.same'=>'Passwords do not match',
			]
		],
		'update-mobile'=>[
			'RULES'=>[
				'new_mobile'=>'required|min:10|max:10|regex:/^[0-9]{10}$/|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,'.config('constants.ACCOUNT_TYPE.USER').',is_deleted,0',

			],
			'LABELS'=>[
				'new_mobile'=>'Current Mobile',

			],
			'MESSAGES'=>[
				'new_mobile.required'=>'mobile is required',
				'new_mobile.min'=>'mobile cannot be less than 10 characters',
				'new_mobile.max'=>'mobile can\'t be longer than 10 characters',
				'new_mobile.regex'=>'mobile must in numeric',
				'new_mobile.different'=>'Your New mobile cannot be same as old password',
			]
		],			
		'otp-validation'=>[
			'RULES'=>[
				'mobile_otp'=>'required|min:4|max:4|regex:/^[0-9]{4}$/',

			],
			'LABELS'=>[
				'mobile_otp'=>'Otp for change mobile',

			],
			'MESSAGES'=>[
				'mobile_otp.required'=>'otp is required',
				'mobile_otp.min'=>'otp cannot be less than 4 characters',
				'mobile_otp.regex'=>'otp is invalid or expired',
				'mobile_otp.max'=>'otp can\'t be longer than 4 characters',
			]
		],
		'new_email_notify'=>[
	        'RULES'=>[                    
	            'email'=>'required|email',                                
	        ],
	        'LABELS'=>[           
	            'email'=>' Enter New Email',            
	                 ],
	        'MESSAGES'=>[           
	            'email.required'=>'Email is required', 
	            'email.email'=>'Invalid email', 
	        ]
    	],
		'order-ratings-feedbacks'=>[
			'RULES'=>[
				'feedback'=>'required',
			],
			'LABELS'=>[
				'feedback'=>' Enter  Feedback',
			],
			'MESSAGES'=>[
				'feedback.required'=>'Feedback Is Required',
			]
		],



	],

	    
		

];