<?php

return [
	'wallet'=> [
		'fund_transfer_save'=>[
			'RULES'=>[
				'tac_code'=> 'required|regex:/([0-9]+)/|min:4|max:4|tpin_match:'.config('tables.ACCOUNT_MST').',trans_pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'),
				],
			'MESSAGES'=>[
				'tac_code.required' => trans('franchisee/validator/change_email_js.tpin_not_empty'),
				'tac_code.tpin_match' => trans('franchisee/validator/change_email_js.tpin_invalide'),
				'tac_code.regex'=>'Invalid Security PIN',			  
			    'tac_code.min'=>'Security PIN must contain 4 digits',		
			    'tac_code.max'=>'Security PIN no more than 4 digits',
			]
		],
		'fund_transfer_confirm'=>[
			'RULES'=>[
				'totamount'=> 'required:min:1',
			],
			'MESSAGES'=>[
				'totamount.required'=>'Amount is required',				
				'totamount.min'=>'Please enter amount.',				
			]
		],
	],
	'settings'=>[
	    'updatepwd'=> [
		    'LABELS'=>[
				'oldpassword'=>trans('franchisee/settings/changepwd.current_password'),
				'newpassword'=>trans('franchisee/settings/changepwd.new_password'),					
				'confirmpassword'=>trans('franchisee/settings/changepwd.confirm_new_pwd'),					
			],
			'RULES'=>[
				'oldpassword'=>'required|regex:/([a-zA-Z0-9]+)/|pwdcheck:'.config('tables.ACCOUNT_MST').',pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'), //min:5|
				'newpassword' => 'required|regex:/([a-zA-Z0-9]+)/|min:8|different:oldpassword',
				'confirmpassword'=> 'required|regex:/([a-zA-Z0-9]+)/|min:8|same:newpassword',
			],
			'MESSAGES'=>[
				'oldpassword.required' => trans('franchisee/settings/change_pwd_js.old_pwd'), 
				'oldpassword.regex' => trans('franchisee/settings/change_pwd_js.invalid_pwd'), 
			    'oldpassword.min' => trans('franchisee/settings/change_pwd_js.minlength'),
				'oldpassword.pwdcheck' => trans('franchisee/settings/changepwd.incorrect_pwd'),
			    //'oldpassword.max' => trans('affiliate/settings/change_pwd_js.maxlength') ,
			    'newpassword.required' => trans('franchisee/settings/change_pwd_js.new_pwd'), 
			    'newpassword.different' => trans('franchisee/settings/change_pwd_js.different'), 
			    'newpassword.regex' => trans('franchisee/settings/change_pwd_js.invalid_pwd'), 
			    'newpassword.min' => trans('franchisee/settings/change_pwd_js.minlength') ,
			    //'newpassword.max' => trans('affiliate/settings/change_pwd_js.maxlength') ,
			    'confirmpassword.required'=>trans('franchisee/settings/change_pwd_js.confirm_new_pwd') ,
			    'confirmpassword.same'=>trans('franchisee/settings/change_pwd_js.confirmpwd_smae') ,
			]
		],
		 
	    'bank-details'=>[
			'LABELS'=>[
				'payment_setings.beneficiary_name'=>'Beneficiary Name',
				'payment_setings.account_no'=>'Your Account Number',	
				'payment_setings.confirm_account_no'=>'Confirm Account Number',	
				//'payment_setings.ifsc_code'=>'IFSC Code',	
				'payment_setings.ifsc_code'=>'Ex:MAHB0001821 SBIN0012932',	
				'payment_setings.bank_name'=>'Bank Name',	
				'payment_setings.branch_name'=>'Branch',	
				'payment_setings.district'=>'District',	
				'payment_setings.state'=>'State',	
			],
			'RULES'=>[
				'payment_setings.beneficiary_name'=>'required|regex:/^[A-Za-z ]*$/|min:3',
				'payment_setings.account_no'=>'required|regex:/^[0-9]*$/|min:4|max:17',
				'payment_setings.confirm_account_no'=>'required|same:payment_setings.account_no',
				'payment_setings.ifsc_code'=>'required|regex:/^[A-Za-z]{4}[0][A-Za-z0-9]{6}$/', 
				'payment_setings.bank_name'=>'required',
				'payment_setings.branch_name'=>'required',
				'payment_setings.district'=>'required',
				'payment_setings.state'=>'required',
			],
		    'MESSAGES'=>[
			   'payment_setings.beneficiary_name.required'=>'Beneficiary Name is required.',
			   'payment_setings.beneficiary_name.regex'=>'You entered an invalid name. Try again!',
			   'payment_setings.beneficiary_name.min'=>'Beneficiary Name must be at least 3 characters',
			   'payment_setings.account_no.required'=>'Your Account Number is required.',
			   'payment_setings.account_no.regex'=>'You entered an invalid account number. Try again!',
			   'payment_setings.account_no.min'=>'Account Number must be at least 4 characters',
			   'payment_setings.account_no.max'=>'Your Account Number can\'t be longer than 17 characters',
			   'payment_setings.confirm_account_no.required'=>'Confirm Account Number is required.',
			   'payment_setings.confirm_account_no.same'=>'It must be same as Your Account Number',
			   'payment_setings.ifsc_code.required'=>'IFSC Code is required.',
			   'payment_setings.ifsc_code.regex'=>'You entered an invalid IFSC Code. Try again!',
			   'payment_setings.bank_name.required'=>'Bank Name is required.',
			   'payment_setings.district.required'=>'District is required.',
			   'payment_setings.state.required'=>'State is required.',
			  
			],
		],

		'changeemail'=> [
			  'send_otp'=>[
				   'RULES'=>[
					'email' => 'required|email|max:40|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,is_deleted,0,is_closed,0',
					 'tpin'=> 'required|security_pin|tpin_match:'.config('tables.ACCOUNT_MST').',trans_pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'), 
					 'vcode'=> 'required|regex:/([0-9a-zA-Z]+)/|min:8|max:8',
				],
				'MESSAGES'=>[
					'email.required' => trans('franchisee/validator/change_email_js.email'),
					'email.email' => trans('franchisee/validator/change_email_js.invalid_email'), 
					'email.max' => trans('franchisee/validator/change_email_js.max_length') ,
					'email.unique' => trans('franchisee/validator/change_email_js.unique'),
					'tpin.required' => trans('franchisee/validator/change_email_js.tpin_not_empty'),
					'tpin.security_pin' => trans('franchisee/validator/change_email_js.tpin_invalide'),
					'tpin.tpin_match' => trans('franchisee/validator/change_email_js.tpin_invalide'),
					'vcode.required' => trans('affiliate/validator/change_email_js.vcode_not_empty'),
					'vcode.regex' => trans('affiliate/validator/change_email_js.vcode_invalide'),
					'vcode.min' => trans('affiliate/validator/change_email_js.vcodeminlengt'),
					'vcode.max' => trans('affiliate/validator/change_email_js.vcodemaxlengt'),
				]
			],
			'verify_otp'=>[
				'RULES'=>[
					'verify_code' => 'required|numeric|digits_between:6,6',
				],
				'MESSAGES'=>[
					'verify_code.required' => trans('franchisee/validator/change_email_js.verify_code'),
					'verify_code.numeric' => trans('franchisee/validator/change_email_js.numeric'), 
					'verify_code.digits_between' => trans('franchisee/validator/change_email_js.maxlength'),
				],
			],
		],
		'changemobile'=> [
			'send_otp'=>[
				'RULES'=>[
				  'mobile'=>'required|db_regex:mobile_validation,location_countries,country_id,'.(isset($userInfo->country_id) ? $userInfo->country_id : 'null').'|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0,is_closed,0',
					/* 'mobile' => 'required|max:10|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0,is_closed,0', */
					'tpin'=> 'required|security_pin|tpin_match:'.config('tables.ACCOUNT_MST').',trans_pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'),
				],
				'MESSAGES'=>[
					'mobile.required' => trans('affiliate/validator/change_mobile_js.mobile'),
					'mobile.max' => trans('affiliate/validator/change_mobile_js.max_length') ,
					'mobile.unique' => trans('affiliate/validator/change_mobile_js.unique'),
					'tpin.required' => trans('affiliate/validator/change_mobile_js.tpin_not_empty'),
					'tpin.security_pin' => trans('affiliate/validator/change_mobile_js.tpin_invalide'),
					'tpin.tpin_match' => trans('affiliate/validator/change_mobile_js.tpin_invalide'),
					 'mobile.regex'=>'Invalid Mobile Number, Please try again',
				]
			],
			'verification_otp'=>[
				'RULES'=>[
					'verify_code' => 'required|numeric|digits_between:6,6',
				],
				'MESSAGES'=>[
					'verify_code.required' => trans('affiliate/validator/change_email_js.verify_code'),
					'verify_code.numeric' => trans('affiliate/validator/change_email_js.numeric'), 
					'verify_code.digits_between' => trans('affiliate/validator/change_email_js.maxlength'),
				],
			],
		],

		'kyc_document_upload'=>[
			'LABELS'=>[			   
				'pan'=>'PAN Card',
				'pan_no'=>'EX: ABCDE1234Q',   //'PAN Number'
				'cheque'=>'Cancelled Cheque',	
				'incc'=>'Incorporation Certificate',	
				/* 'tax'=>'TAX Registration Certificate',	 */
				'tax'=>'Ex: 12ABCDE1234Q1Z1',  //'TAX Number',
				'id_proof'=>'ID Proof',
				'address_proof'=>'Address Proof',
			],
			'RULES'=>[			    
				'pan'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 				
				'pan_no'=>'regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/', //panumber EX: ABCDE1234Q	
				//'pan_no'=>'required_with:pan|regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/', //panumber EX: ABCDE1234Q
				'cheque'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 
				'incc'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 
				/* 'tax'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048',  */
				'tax'=>'regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/', //Ex: 12ABCDE1234Q1Z1  12ABCDE1234Q1Z1
				//'tax_no'=>'required_with:tax|regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/', //Ex: 12ABCDE1234Q1Z1  12ABCDE1234Q1Z1
				'id_proof'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048',
				'address_proof'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048',
			],
		    'MESSAGES'=>[	
			    'pan.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'pan.max'=>'PAN Card can\'t be longer than 2 MB.',
				'pan_no.required_with'=>'PAN Number is required',	
				'pan_no.regex'=>'Invalid PAN Number',
			    'cheque.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'cheque.max'=>'Cancelled Cheque can\'t be longer than 2 MB.',			 
			    'incc.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'incc.max'=>'Incorporation Certificate can\'t be longer than 2 MB.',			  
			    /* 'tax.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'tax.max'=>'TAX Registration Certificate can\'t be longer than 2 MB.', */
				'tax.required_with'=>'TAX Number is required',
				'tax.regex'=>'Invalid TAX Number',
				'id_proof.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'id_proof.max'=>'Cancelled Cheque can\'t be longer than 2 MB.',			 
				'address_proof.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'address_proof.max'=>'Cancelled Cheque can\'t be longer than 2 MB.',			 
			],
		],
		'update_profile'=>[
		    'LABELS'=>[
				'marital_status'=>'Marital Status',
				'gardian'=>"Father's/Husband's Name",					
			],
			'RULES'=>[
				'marital_status'=>'required|in:1,2',
				'gardian'=>'required|full_name|min:3|max:50',			
			],			
			'MESSAGES'=>[
				'marital_status.required'=>'Marital status is required',
				'marital_status.in'=>'Invalide Marital status',
				'gardian.required'=>"Father’s/Husband's Name is required",
				'gardian.regex'=>'Please provide a valid Name',
				'gardian.min'=>"Father’s/Husband's Name must contain atleast 3 char",
				'gardian.max'=>"Father’s/Husband's Name must not exist 50 char",
			]
		],
		
		'address'=>[
	      'save'=>[
		    'RULES'=>[
					'address.flat_no' => 'required|regex:/([A-Za-z0-9\-\\,.]+)$/',
					'address.postal_code' => 'required|regex:/([A-Za-z0-9\-\\,.]+)$/',
					'address.city_id' => 'required|exists:'.config('tables.LOCATION_CITY').',city_id,status,1',
					'address.state_id' => 'required|exists:'.config('tables.LOCATION_STATE').',state_id,status,1',
			 ],    
			 'MESSAGES'=>[
			      'address.flat_no.required'=>trans('affiliate/profile.flat_no'),
			      'address.postal_code.required'=>trans('affiliate/profile.postal_code'),
			      'address.city_id.required'=>trans('affiliate/profile.city'),
			      'address.state_id.required'=>trans('affiliate/profile.state'),
				  'address.city_id.exists'=>'City not available',
			      'address.state_id.exists'=>'State not available',
			 ]
		  ],
	   ],
		'securitypin'=>[
		    /* 'verify'=>[
			    'LABELS'=>[
					'tran_oldpassword'=> trans('franchisee/settings/security_pwd.current_password'),					
				],
				'RULES'=>[
					'tran_oldpassword'=>'required|regex:/([0-9]+)/|min:4|max:4',					
				],
				'MESSAGES'=>[
					'tran_oldpassword.required' => trans('franchisee/settings/security_pwd_js.tran_oldpassword'), 
					'tran_oldpassword.regex' => trans('franchisee/settings/security_pwd_js.invalid_security_pin'), 
					'tran_oldpassword.min' => trans('franchisee/settings/security_pwd_js.minlength'),
					'tran_oldpassword.max' => trans('franchisee/settings/security_pwd_js.maxlength'),					
				],				
		     ], */
		    'reset'=>[
			    'LABELS'=>[
					'tran_oldpassword'=> trans('franchisee/settings/security_pwd.current_password'),
					'tran_newpassword' => trans('franchisee/settings/security_pwd.new_password'),
					'tran_confirmpassword'=> trans('franchisee/settings/security_pwd.confirm_password'),
				],
				'RULES'=>[
				   'tran_oldpassword'=> 'required|security_pin|tpin_match:'.config('tables.ACCOUNT_MST').',trans_pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'),
					'tran_newpassword' => 'required|regex:/([0-9]+)/|min:4|max:4|different:tran_oldpassword',
					'tran_confirmpassword'=> 'required|max:4|same:tran_newpassword',
				],
				'MESSAGES'=>[
					'tran_oldpassword.required' => trans('franchisee/validator/change_email_js.tpin_not_empty'),
					'tran_oldpassword.security_pin' => trans('franchisee/validator/change_email_js.tpin_invalide'),
					'tran_oldpassword.tpin_match' => trans('franchisee/settings/security_pwd.incrct_trans_pwd'),
					'tran_newpassword.required' => trans('franchisee/settings/security_pwd_js.tran_newpassword'), 
					'tran_newpassword.regex' => trans('franchisee/settings/security_pwd_js.invalid_security_pin'), 
					'tran_newpassword.min' => trans('franchisee/settings/security_pwd_js.minlength'),
					'tran_newpassword.max' => trans('franchisee/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.required'=>trans('franchisee/settings/security_pwd_js.tran_confirmpassword'),
					'tran_confirmpassword.max'=> trans('franchisee/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.same'=>trans('franchisee/settings/security_pwd_js.different_sec_pwd'),
					'tran_newpassword.different' => trans('franchisee/settings/security_pwd_js.different'), 
				],				
		    ],
			'save'=>[
			    'LABELS'=>[
					'tran_newpassword'=>trans('franchisee/settings/security_pwd.new_password'),
					'tran_confirmpassword' =>trans('franchisee/settings/security_pwd.confirm_password'),				
				],
				'RULES'=>[
					'tran_newpassword'=>'required|regex:/([0-9]+)/|min:4|max:4',
					'tran_confirmpassword' => 'required|max:4|same:tran_newpassword',				 //regex:/([0-9]+)/
				],
				'MESSAGES'=>[
					'tran_newpassword.required' =>trans('franchisee/settings/security_pwd_js.tran_newpassword'), 
					'tran_newpassword.regex' =>trans('franchisee/settings/security_pwd_js.invalid_security_pin'), 
					'tran_newpassword.min' =>trans('franchisee/settings/security_pwd_js.minlength'),
					'tran_newpassword.max' =>trans('franchisee/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.required' =>trans('franchisee/settings/security_pwd_js.tran_confirmpassword'), 
					'tran_confirmpassword.max' =>trans('franchisee/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.same'=>trans('franchisee/settings/security_pwd_js.different_sec_pwd'),
				],				
		    ],
			'forgototp'=>[
				'verify'=>[
					'LABELS'=>[
						'otp'=>trans('franchisee/settings/security_pwd_js.otp'),					
					],
					'RULES'=>[
						'otp'=>'required|regex:/([0-9]+)/|min:6|max:6',					
					],
					'MESSAGES'=>[
						'otp.required' => trans('franchisee/settings/security_pwd_js.otp_req'),  
						'otp.regex' => trans('franchisee/settings/security_pwd_js.invalid_otp'), 
						'otp.min' => trans('franchisee/settings/security_pwd_js.otpminlen'),
						'otp.max' => trans('franchisee/settings/security_pwd_js.otpmaxlen'),					
					],				
				],	
			],
		  'create'=>[
				  'LABELS'=>[
						'new_security_pin'=>trans('franchisee/settings/security_pwd.new_password'),
						'confirm_security_pin' =>trans('franchisee/settings/security_pwd.confirm_password'),				
					],
					'RULES'=>[
						'new_security_pin'=>'required|regex:/([0-9]+)/|min:4|max:4',
						'confirm_security_pin' => 'required|max:4|same:new_security_pin',				 //regex:/([0-9]+)/
					],
					'MESSAGES'=>[
						'new_security_pin.required' =>trans('franchisee/settings/security_pwd_js.security_pin'), 
						'new_security_pin.regex' =>trans('franchisee/settings/security_pwd_js.invalid_security_pin'), 
						'new_security_pin.min' =>trans('franchisee/settings/security_pwd_js.minlength'),
						'new_security_pin.max' =>trans('franchisee/settings/security_pwd_js.maxlength'),
						'confirm_security_pin.required' =>trans('franchisee/settings/security_pwd_js.tran_confirmpassword'), 
						'confirm_security_pin.max' =>trans('franchisee/settings/security_pwd_js.maxlength'),
						'confirm_security_pin.same'=>trans('franchisee/settings/security_pwd_js.different_sec_pwd'),
				],
		   ],
		],
	],
	'withdrawal' =>[
		'bank-details'=>[
			'LABELS'=>[
				'payment_setings.beneficiary_name'=>Lang::get('general.fields.beneficiary_name'),
				'payment_setings.account_no'=>Lang::get('general.fields.account_no'),	
				'payment_setings.confirm_account_no'=>Lang::get('general.fields.confirm_account_no'),	
				'payment_setings.ifsc_code'=>Lang::get('general.fields.ifsc_code'),	
				'payment_setings.bank_name'=>Lang::get('general.fields.bank_name'),	
				'payment_setings.branch_name'=>Lang::get('general.fields.branch_name'),	
			],
			'RULES'=>[
				'payment_setings.beneficiary_name'=>'required|min:3',
				'payment_setings.account_no'=>'required|min:4|max:17',
				'payment_setings.confirm_account_no'=>'required|same:payment_setings.account_no',
				'payment_setings.ifsc_code'=>'required|regex:/^[A-Za-z]{4}[0][A-Za-z0-9]{6}$/',
				'payment_setings.bank_name'=>'required',
				'payment_setings.branch_name'=>'required',
			],
		    'MESSAGES'=>[
			   'payment_setings.confirm_account_no.same'=>'Confirm Account No must be same as Current Account No'
			 ],
		],
		'save_withdrawal'=>[
		    'LABELS'=>[
				'amount'=>'Amount',
				'security_pin'=>'Security PIN',	
				'payment_type_id'=>'Payment Type ID',					
			],
		    'RULES'=>[
				'security_pin'=>'required|regex:/([0-9]+)/|min:4|max:4',
			],
			'MESSAGES'=>[
			    'security_pin.required'=>'Security PIN is required',
			    'security_pin.regex'=>'Invalid Security PIN',			  
			    'security_pin.min'=>'Security PIN must contain 4 digits',		
			    'security_pin.max'=>'Security PIN no more than 4 digits',
			],
		],
	],
 'merchants'=>[	
	  'save' => [
           'ATTRIBUTES' => [
            
        ],
        'LABELS' => [
            'firstname' => Lang::get('general.fields.firstname'),
            'lastname' => Lang::get('general.fields.lastname'),
            'buss_name' => 'Business name',
			'country' => 'Country',
            'email' => Lang::get('general.fields.email'),
            'mobile' => Lang::get('general.fields.mobile'),            
			'bcategory' => 'Category', 
            'phy_locations' => 'Physical Location',  
            'service_type' => 'Service Type'
        ],
        'RULES' => [
            'firstname' => 'required|min:3|max:100|regex:/^[A-Za-z\s]*$/',
            'lastname' => 'required|full_name|min:1|max:50',
            'buss_name' => 'required|regex:/^[a-zA-Z0-9][\sa-zA-Z0-9&.-]*$/|min:4|max:60',
            'service_type' => 'required',
            'phy_locations' => 'required',          
			'country' => 'required',				
            'email' => 'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,is_deleted,0',
            'mobile' => 'required_with:country|db_regex:mobile_validation,'.config('tables.LOCATION_COUNTRY').',country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0',			
            'bcategory' => 'required_if:service_type,1,3',
        ],
        'MESSAGES' => [
            'firstname.required' => 'First Name cannot be empty',
            'lastname.required' => 'Last Name cannot be empty',
			'lastname.full_name' => 'Please enter valid last name',
            'buss_name.required' => 'Business name cannot be empty',
            'email.required' => 'Business email cannot be empty',
            'email.email' => 'Please enter the valid business email',
            'email.unique' => 'Email ID has already been used',
            'bcategory.required_if' => 'Category cannot be empty',
            'mobile.required_with'=>'Mobile Number cannot be empty',
			'mobile.unique' => 'Mobile Number has already been used',
			'service_type.required'=>'Service Type cannot be empty',
        ]
     ],
	 'tax-information' => [
            'LABELS' => [
                'pan_name' => 'Name On PAN Card',
                'pan_number' => 'PAN Number',
                'pan_card_upload' => 'PAN Card',
            ],
            'RULES' => [
                'pan_name' => 'required|regex:/^[a-zA-Z\s]*$/',
                'pan_number' => isset($userInfo->country_id) ? ($userInfo->country_id == 77) ? 'required|regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/' : '' : '',
                'pan_card_upload' => 'required|file|mimetypes:image/jpg,image/jpeg,image/gif,image/png,application/pdf|min:20|max:2024',
            ],
            'MESSAGES' => [
                'pan_number.required' => 'Please Enter Your PAN Card Number',
                'pan_number.regex' => 'Invalid PAN, Please try again',
                'pan_name.required' => 'Please Enter Your Name on PAN Card',
                'pan_card_upload.required' => 'Please Upload Your PAN details',
                'pan_name.regex' => 'Please Enter Valid Pan Name'
            ],
        ],
	 'gst-information' => [
            'LABELS' => [
                'gstin_no' => 'GSTIN',
                'tan_no' => 'TAN',
            ],
            'RULES' => [
                'gstin_no' => 'regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/',
                'tan_no' => 'regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/'
            ],
            'MESSAGES' => [
                'gstin_no.regex' => 'Please Enter Your Valid GSTIN',
                'tan_no.regex' => 'Please Enter Your Valid TAN',
            ],
        ],
  ],
  'user'=>[
       'save'=>[
	     'ATTRIBUTES' => [
   
        ],
        'LABELS' => [
            'firstname' => Lang::get('general.fields.firstname'),
            'lastname' => Lang::get('general.fields.lastname'),
            'gender'=> 'Gender',    
			'dob'=>'Date of Birth',
			'country' => 'Country',
			'email' => Lang::get('general.fields.email'),
            'mobile' => Lang::get('general.fields.mobile'),  
            'username'=>'Username',
            'password'=>'Password',
            'state'=>'State',			
            'district'=>'District',			
        ],
        'RULES' => [
            'firstname'=>'required|firstname|min:3|max:30',
			'lastname'=>'required|lastname|min:1|max:30',
			'dob'=>"required|before:13 years ago",
			'gender'=>'required',
			'country' => 'required',
            'email' => 'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,is_deleted,0',
            'mobile' => 'required_with:country|db_regex:mobile_validation,'.config('tables.LOCATION_COUNTRY').',country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0',		
            'username'=>'required|min:6|max:30|username|unique:'.config('tables.ACCOUNT_MST').',uname,NULL,account_id,is_deleted,0,is_closed,0',
          	'password'=>'required|min:6',
			'state'=>'required',
			'district'=>'required',
        ],
        'MESSAGES' => [
			'firstname.required' => "First Name cannot be empty",
			'firstname.firstname' => "First Name should contain only alphapets",
			'firstname.min' => "The First name must be at least 3 characters",
			'firstname.max' => "First name must not exist 30 char",
			'lastname.required' => "Last Name cannot be empty",
			'lastname.lastname' => "Last Name should contain only alphapets",
			'lastname.min' => "Last name must contain atleast 1 char",
			'lastname.max' => "Last name must not exist 30 char",	
			'gender.required' => "Please select a Gender",
			'dob.required' => "Please select your Date of Birth",
		/*	'dob.date_format'=>'Invalide Date of Birth', */
			'dob.before'=>'Date of Birth should be 13 years back',
            'email.required' =>"Email address cannot be empty",
            'email.email' => "Please enter valid email address",
            'email.unique' => "Email address has already been used",
            'mobile.required_with' => "Mobile Number cannot be empty",
            'mobile.db_regex'=>"Please enter a valid Mobile Number",
			'mobile.unique' =>  "This Mobile number has already been used",
			'username.required' => "Username cannot be empty",
			'username.regex' => "Please enter A-Z,0-9",
			'username.min' => "The Username must be at least 6 characters",  
			'username.max' => "Username must not exist 30 char",
			'username.username' => "Username may only contain letters and numbers",
			'username.unique' => "Username has already been used",
			'password.required' => "Password cannot be empty",
			'password.min' => "Password must be at least 6 characters",
			'state.required'=>"Please select State",
			'district.required'=>"Please select District",
          ]
	   ],
	   'update_details'=>[
	     'ATTRIBUTES' => [
            
        ],
        'LABELS' => [
            'firstname' => Lang::get('general.fields.firstname'),
            'lastname' => Lang::get('general.fields.lastname'),
            'gender'=> 'Gender',    
			'dob'=>'Date of Birth',
			'email' => Lang::get('general.fields.email'),
            'mobile' => Lang::get('general.fields.mobile'),  	
        ],
        'RULES' => [
            'firstname'=>'required|firstname|min:3|max:30',
			'lastname'=>'required|lastname|min:1|max:30',   
		    'dob'=>'required',
			'gender'=>'required',
			'email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',
			'mobile'=>'required|regex:/^[0-9]{10}$/|unique:'.config('tables.ACCOUNT_MST').',mobile,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',

        ],
        'MESSAGES' => [
			'firstname.required' => "First Name cannot be empty",
			'firstname.firstname' => "First Name should contain only alphapets",
			'firstname.min' => "First name must contain atleast 3 char",
			'firstname.max' => "First name must not exist 30 char",
			'lastname.required' => "Last Name cannot be empty",
			'lastname.lastname' => "Last Name should contain only alphapets",
			'lastname.min' => "Last name must contain atleast 1 char",
			'lastname.max' => "Last name must not exist 30 char",	
			'gender.required' => "Please select a Gender",
			'dob.required' => "Please select your Date of Birth",
            'email.required' =>"Email address cannot be empty",
            'email.email' => "Please enter valid email address",
            'email.unique' => "Email address has already been used",
            'mobile.required' => "Mobile Number cannot be empty",
            'mobile.db_regex'=>"Please enter a valid Mobile Number",
			'mobile.unique' =>  "This Mobile number has already been used",
          ]
	   ],
	     'change-password' => [
            'RULES' => [
                'new_pwd' => 'required|password|min:6|max:16',
            ],
            'LABELS' => [
                'new_pwd' => 'New password',
            ],
            'MESSAGES' => [
                'new_pwd.required' => 'New Password is required',
                'new_pwd.password' => 'You entered an invalid password. Try again!',
                'new_pwd.min' => 'New Password must be at least 6 characters',
                'new_pwd.max' => 'Your New password can\'t be longer than 16 characters',
            ]
        ],
		'address'=>[
	      'save'=>[
		    'RULES'=>[
					'address.flat_no' => 'required',
					'address.landmark' => 'required',
					'address.postal_code' => 'required',
					'address.state_id' => 'required',
					'address.district_id' => 'required',
					'address.city_id' => 'required',
			 ],    
			 'MESSAGES'=>[
			      'address.flat_no.required'=>'Address cannot be empty',
			      'address.landmark.required'=>'Landmark cannot be empty',
			      'address.postal_code.required'=>'postal code cannot be empty',
			      'address.city_id.required'=>'City Cannot be empty',
			      'address.state_id.required'=>'State cannot be empty',
				  'address.district_id.required' =>'District cannot be empty',
			 ]
		  ],
	   ],
    ],
];