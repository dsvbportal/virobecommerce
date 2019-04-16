<?php

$regAllRules = [
			'firstname'=>'required|firstname|min:3|max:30',
			'lastname'=>'required|lastname|min:1|max:30',
			'gender'=>'required',
			'dob'=>"required|date_format:Y-m-d|before:13 years ago",
			'gardian'=>'required|full_name|min:3|max:50',
			'marital_status'=>'required',
			'email'=>'required|email|unique:account_mst,email,NULL,account_id,is_deleted,0',
			'username'=>'required|min:6|max:30|username|unique:account_mst,uname',
			'password'=>'required|min:6',
			'country'=>'required|regex:/^[A-Z]{2}$/',
			'state'=>'required',
			'district'=>'required'];
$signupRules = ['RULES'=>[]];			
if(isset($regMissingFlds) && !empty($regMissingFlds)){	
	foreach($regMissingFlds as $k=>$v){
		$signupRules['RULES'][$k] = $regAllRules[$k];
	}	
}
return [	
	'signup'=> [		
		'save'=>[
			'RULES' => [
				'firstname'=>'required|firstname|min:3|max:30',
				'lastname'=>'required|lastname|min:1|max:30',
				'email'=>'required|email|unique:account_mst,email,NULL,account_id,is_deleted,0',
				'username'=>'required|min:6|max:30|username|unique:account_mst,uname',
				'mobile'=>'required|unique:account_mst,mobile,NULL,account_id,is_deleted,0',
				'password'=>'required|min:6',				
				//'postcode'=>'required|zipcode',
				'gender'=>'required',
				'dob'=>"required|date_format:Y-m-d|before:13 years ago",
				'gardian'=>'required|full_name|min:3|max:50',
				'marital_status'=>'required',				
				'country'=>'required|regex:/^[A-Z]{2}$/',
				'state'=>'required',
				'district'=>'required'
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
				'dob.date_format'=>'Invalide Date of Birth',
				'dob.before'=>'Date of Birth should be 13 years back',
				'gardian.required' => "Father's/Husband's Name cannot be empty",
				'gardian.full_name' => "Please provide a valid Name",
				'gardian.min' => "First name must contain atleast 3 char",
				'gardian.max' => "First name must not exist 50 char",
				'marital_status.required' => "Please select your Marital Status",
				'email.required' => "Email address cannot be empty",
				'email.email' => "Please enter valid email address",
				'email.unique' => "Email address already exist",
				'mobile.required' => "Mobile Number cannot be empty",
				'mobile.mobile' => "Please enter a valid Mobile Number",
				'mobile.unique' => "This Mobile number has already been used",
				'username.required' => "Username cannot be empty",
				'username.regex' => "Please enter A-Z,0-9",
				'username.min' => "Username must contain atleast 6 char",
				'username.max' => "Username must not exist 30 char",
				'username.username' => "Username may only contain letters and numbers",
				'username.unique' => "This Username has already been used",
				'password.required' => "Password cannot be empty",
				'password.min' => "Password must be at least 6 characters",
				'country.required' => "Please select country",
				'state.required'=>"Please select State",
				'district.required'=>"Please select District",
			]
		],			
		'acupgrade'=> 
			array_merge($signupRules,
			['MESSAGES' => [
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
				'dob.date_format'=>'Invalide Date of Birth',
				'dob.before'=>'Date of Birth should be 13 years back',
				'gardian.required' => "Father's/Husband's Name cannot be empty",
				'gardian.full_name' => "Please provide a valid Name",
				'gardian.min' => "First name must contain atleast 3 char",
				'gardian.max' => "First name must not exist 50 char",
				'marital_status.required' => "Please select your Marital Status",
				'mobile.required' => "Mobile Number cannot be empty",
				'mobile.mobile' => "Please enter a valid Mobile Number",
				'mobile.unique' => "Mobile number already exist",
				'email.required' => "Email Address cannot be empty",
				'email.email' => "Please enter valide Email Address",
				'email.unique' => "Email Address already exist",
				/*'postcode.required' => "Please enter your Zipcode/Postal code",
				'postcode.zipcode' => "Invalide Zipcode/Postal code",								*/
			]])
	],
	'forgotpwd'=>[
		'RULES'=>[
			'uname_id'=>'required|email|exists:'.config('tables.ACCOUNT_MST').',email,is_deleted,0,is_closed,0',
		],
		'MESSAGES' => [
			'uname_id.required' => "Please enter email address",
			'uname_id.email' => "Please enter valide email address",
			'uname_id.exists'=> "You are not registered with us. Please sign up.",
		]		
	],
	'settings'=>[
	    'password_check'=>[
			'LABELS'=>[
				'old_user_pwd'=>trans('affiliate/settings/change_pwd_js.old_password'),					
			],
			'RULES'=>[
				'old_user_pwd'=>'required|regex:/([a-zA-Z0-9]+)/', //min:5|
			],
			'MESSAGES'=>[
				'old_user_pwd.regex' => trans('affiliate/settings/change_pwd_js.old_pwd'), 
				'old_user_pwd.min' => trans('affiliate/settings/change_pwd_js.minlength') ,
				//'oldpassword.max' => trans('affiliate/settings/change_pwd_js.maxlength') ,				
			],				
		],
	    'updatepwd'=> [
		    'LABELS'=>[
				'old_user_pwd'=>trans('affiliate/settings/changepwd.current_password'),
				'newpassword'=>trans('affiliate/settings/changepwd.new_password'),					
				'confirmpassword'=>trans('affiliate/settings/changepwd.confirm_new_pwd'),					
			],
			'RULES'=>[
				'old_user_pwd'=>'required|regex:/([a-zA-Z0-9]+)/|pwdcheck:'.config('tables.ACCOUNT_MST').',pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'), //min:5|
				'newpassword' => 'required|regex:/([a-zA-Z0-9]+)/|min:8|different:old_user_pwd',
				'confirmpassword'=> 'required|regex:/([a-zA-Z0-9]+)/|min:8|same:newpassword',
			],
			'MESSAGES'=>[
				'old_user_pwd.required' => trans('affiliate/settings/change_pwd_js.old_pwd'), 
				'old_user_pwd.regex' => trans('affiliate/settings/change_pwd_js.invalid_pwd'), 
			    'old_user_pwd.min' => trans('affiliate/settings/change_pwd_js.minlength'),
				'old_user_pwd.pwdcheck' => trans('affiliate/settings/changepwd.incorrect_pwd'),
			    //'oldpassword.max' => trans('affiliate/settings/change_pwd_js.maxlength') ,
			    'newpassword.required' => trans('affiliate/settings/change_pwd_js.new_pwd'), 
			    'newpassword.different' => trans('affiliate/settings/change_pwd_js.different'), 
			    'newpassword.regex' => trans('affiliate/settings/change_pwd_js.invalid_pwd'), 
			    'newpassword.min' => trans('affiliate/settings/change_pwd_js.minlength') ,
			    //'newpassword.max' => trans('affiliate/settings/change_pwd_js.maxlength') ,
			    'confirmpassword.required'=>trans('affiliate/settings/change_pwd_js.confirm_new_pwd') ,
			    'confirmpassword.same'=>trans('affiliate/settings/change_pwd_js.confirmpwd_smae') ,
			]
		],
		'securitypin'=>[
		    'verify'=>[
			    'LABELS'=>[
					'oldpassword'=> trans('affiliate/settings/security_pwd.current_password'),					
				],
				'RULES'=>[
					'oldpassword'=>'required|regex:/([0-9]+)/|min:4|max:4',					
				],
				'MESSAGES'=>[
					'oldpassword.required' => trans('affiliate/settings/security_pwd_js.tran_oldpassword'), 
					'oldpassword.regex' => trans('affiliate/settings/security_pwd_js.invalid_security_pin'), 
					'oldpassword.min' => trans('affiliate/settings/security_pwd_js.minlength'),
					'oldpassword.max' => trans('affiliate/settings/security_pwd_js.maxlength'),					
				],				
		    ],	
		    'reset'=>[
			    'LABELS'=>[
					'oldpassword'=> trans('affiliate/settings/security_pwd.current_password'),
					'tran_newpassword' => trans('affiliate/settings/security_pwd.new_password'),
					'tran_confirmpassword'=> trans('affiliate/settings/security_pwd.confirm_password'),
				],
				'RULES'=>[
				   'oldpassword'=> 'required|security_pin|tpin_match:'.config('tables.ACCOUNT_MST').',trans_pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'),
					'tran_newpassword' => 'required|regex:/([0-9]+)/|min:4|max:4|different:oldpassword',
					'tran_confirmpassword'=> 'required|max:4|same:tran_newpassword',
				],
				'MESSAGES'=>[
					'oldpassword.required' => trans('affiliate/validator/change_email_js.tpin_not_empty'),
					'oldpassword.security_pin' => trans('affiliate/validator/change_email_js.tpin_invalide'),
					'oldpassword.tpin_match' => trans('affiliate/validator/change_email_js.tpin_invalide'),
					'tran_newpassword.required' => trans('affiliate/settings/security_pwd_js.tran_newpassword'), 
					'tran_newpassword.regex' => trans('affiliate/settings/security_pwd_js.invalid_security_pin'), 
					'tran_newpassword.min' => trans('affiliate/settings/security_pwd_js.minlength'),
					'tran_newpassword.max' => trans('affiliate/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.required'=>trans('affiliate/settings/security_pwd_js.tran_confirmpassword'),
					'tran_confirmpassword.max'=> trans('affiliate/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.same'=>trans('affiliate/settings/security_pwd_js.different_sec_pwd'),
					'tran_newpassword.different' => trans('affiliate/settings/security_pwd_js.different'), 
				],				
		    ],
			'save'=>[
			    'LABELS'=>[
					'tran_newpassword'=>trans('affiliate/settings/security_pwd.new_password'),
					'tran_confirmpassword' =>trans('affiliate/settings/security_pwd.confirm_password'),				
				],
				'RULES'=>[
					'tran_newpassword'=>'required|regex:/([0-9]+)/|min:4|max:4',
					'tran_confirmpassword' => 'required|max:4|same:tran_newpassword',				 //regex:/([0-9]+)/
				],
				'MESSAGES'=>[
					'tran_newpassword.required' =>trans('affiliate/settings/security_pwd_js.tran_newpassword'), 
					'tran_newpassword.regex' =>trans('affiliate/settings/security_pwd_js.invalid_security_pin'), 
					'tran_newpassword.min' =>trans('affiliate/settings/security_pwd_js.minlength'),
					'tran_newpassword.max' =>trans('affiliate/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.required' =>trans('affiliate/settings/security_pwd_js.tran_confirmpassword'), 
					'tran_confirmpassword.max' =>trans('affiliate/settings/security_pwd_js.maxlength'),
					'tran_confirmpassword.same'=>trans('affiliate/settings/security_pwd_js.different_sec_pwd'),
				],				
		    ],
			'forgototp'=>[
				'verify'=>[
					'LABELS'=>[
						'otp'=>trans('affiliate/settings/security_pwd_js.otp'),					
					],
					'RULES'=>[
						'otp'=>'required|regex:/([0-9]+)/|min:6|max:6',					
					],
					'MESSAGES'=>[
						'otp.required' => trans('affiliate/settings/security_pwd_js.otp_req'),  
						'otp.regex' => trans('affiliate/settings/security_pwd_js.invalid_otp'), 
						'otp.min' => trans('affiliate/settings/security_pwd_js.otpminlen'),
						'otp.max' => trans('affiliate/settings/security_pwd_js.otpmaxlen'),					
					],				
				],	
			],
		  'create'=>[
				  'LABELS'=>[
						'new_security_pin'=>trans('affiliate/settings/security_pwd.new_password'),
						'confirm_security_pin' =>trans('affiliate/settings/security_pwd.confirm_password'),				
					],
					'RULES'=>[
						'new_security_pin'=>'required|regex:/([0-9]+)/|min:4|max:4',
						'confirm_security_pin' => 'required|max:4|same:new_security_pin',				 //regex:/([0-9]+)/
					],
					'MESSAGES'=>[
						'new_security_pin.required' =>trans('affiliate/settings/security_pwd_js.security_pin'), 
						'new_security_pin.regex' =>trans('affiliate/settings/security_pwd_js.invalid_security_pin'), 
						'new_security_pin.min' =>trans('affiliate/settings/security_pwd_js.minlength'),
						'new_security_pin.max' =>trans('affiliate/settings/security_pwd_js.maxlength'),
						'confirm_security_pin.required' =>trans('affiliate/settings/security_pwd_js.tran_confirmpassword'), 
						'confirm_security_pin.max' =>trans('affiliate/settings/security_pwd_js.maxlength'),
						'confirm_security_pin.same'=>trans('affiliate/settings/security_pwd_js.different_sec_pwd'),
				],
		   ],
		],
		'changeemail'=> [
			'send_otp'=>[
				'RULES'=>[
					'email' => 'required|email|max:40|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,is_deleted,0,is_closed,0',
					'vcode'=> 'required|regex:/([0-9a-zA-Z]+)/|min:8|max:8',
				],
				'MESSAGES'=>[
					'email.required' => trans('affiliate/validator/change_email_js.email'),
					'email.email' => trans('affiliate/validator/change_email_js.invalid_email'), 
					'email.max' => trans('affiliate/validator/change_email_js.max_length') ,
					'email.unique' => trans('affiliate/validator/change_email_js.unique'),
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
					'verify_code.required' => trans('affiliate/validator/change_email_js.verify_code'),
					'verify_code.numeric' => trans('affiliate/validator/change_email_js.numeric'), 
					'verify_code.digits_between' => trans('affiliate/validator/change_email_js.maxlength'),
				],
			],
		],
		'change_uname'=>[
			'RULES'=>[
				'new_uname'=>'required|unique:'.config('tables.ACCOUNT_MST').',uname,NULL,account_id,is_deleted,0,is_closed,0',
			],
			'MESSAGES'=>[
				'new_uname.required'=> "Please enter display name",
				'new_uname.unique'=> "Display name has already been taken",
			]
		],
		'nominee' => [
			'save' => [
				'RULES'=>[
					'fullname'=>'required|full_name|min:3|max:50',
					'gender'=>'required',
					'dob'=>'sometimes|date_format:Y-m-d|before:13 years ago',
					'relation_ship_id'=>'required|exists:'.config('tables.RELATION_SHIPS_LANG').',relation_ship_id,lang_id,1',
					
				],
				'LABELS'=>[
				'fullname'=>'Full Name',
				'gender'=>'Gender',
				'dob'=>'DOB',
				'relation_ship_id'=>'Relation Ship',
				],
				'MESSAGES'=>[
					'fullname.required' => "Full Name cannot be empty",
					'fullname.full_name' => "Name must include first and last name",
					'fullname.min' => "Full name must contain atleast 3 char",
					'fullname.max' => "Full name must not exist 50 char",
					'gender.required' => "Please select a Gender",
					'dob.required' => "Please select your Date of Birth",
					'dob.date_format'=>'Invalide Date of Birth',
					'dob.before'=>'Date of Birth should be 13 years back',
					'relation_ship_id.required' => "Please select a Relation Ship",
				]
			],
		],
		'update_pickup_address'=>[
			'RULES'=>[
				'flat_no'=>'required',
				'address.postal_code'=>'required',
				'address.city_id'=>'required',
				'address.state_id'=>'required',
			],
			'MESSAGES'=>[
				'flat_no'=>'Address is required',
				'address.postal_code.required'=>'Postalcode is required',
				'address.city_id.required'=>'City is required',
				'address.state_id.required'=>'State is required',
			]
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
		'bank-details'=>[
			'LABELS'=>[
				'payment_setings.beneficiary_name'=>'Beneficiary Name',
				'payment_setings.account_no'=>'Current Account Number',	
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
			   'payment_setings.account_no.required'=>'Current Account Number is required.',
			   'payment_setings.account_no.regex'=>'You entered an invalid account number. Try again!',
			   'payment_setings.account_no.min'=>'Account Number must be at least 4 characters',
			   'payment_setings.account_no.max'=>'Your Account Number can\'t be longer than 17 characters',
			   'payment_setings.confirm_account_no.required'=>'Confirm Account Number is required.',
			   'payment_setings.confirm_account_no.same'=>'It must be same as Current Account Number',
			   'payment_setings.ifsc_code.required'=>'IFSC Code is required.',
			   'payment_setings.ifsc_code.regex'=>'You entered an invalid IFSC Code. Try again!',
			   'payment_setings.bank_name.required'=>'Bank Name is required.',
			   'payment_setings.district.required'=>'District is required.',
			   'payment_setings.state.required'=>'State is required.',
			  
			],
		],
		'kyc_document_upload'=>[
			'LABELS'=>[			   
				'pan'=>'PAN Card',
				'pan_no'=>'EX: ABCDE1234Q',   //'PAN Number'
				'cheque'=>'Cancelled Cheque',	
				'incc'=>'Incorporation Certificate',	
				'tax'=>'TAX Registration Certificate',	
				'tax_no'=>'Ex: 12ABCDE1234Q1Z1',  //'TAX Number',
				'id_proof'=>'ID Proof',
				'address_proof'=>'Address Proof',
			],
			'RULES'=>[			    
				'pan'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 				
				'pan_no'=>'regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/', //panumber EX: ABCDE1234Q	
				//'pan_no'=>'required_with:pan|regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/', //panumber EX: ABCDE1234Q
				'cheque'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 
				'incc'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 
				'tax'=>'mimes:jpeg,gif,bmp,png,pdf|max:2048', 
				'tax_no'=>'regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/', //Ex: 12ABCDE1234Q1Z1  12ABCDE1234Q1Z1
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
			    'tax.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'tax.max'=>'TAX Registration Certificate can\'t be longer than 2 MB.',
				'tax_no.required_with'=>'TAX Number is required',
				'tax_no.regex'=>'Invalid TAX Number',
				'id_proof.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'id_proof.max'=>'Cancelled Cheque can\'t be longer than 2 MB.',			 
				'address_proof.mimes'=>'Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).',
				'address_proof.max'=>'Cancelled Cheque can\'t be longer than 2 MB.',			 
			],
		],
		'changemobile'=> [
			'send_otp'=>[
				'RULES'=>[
					'mobile' => 'required|max:10|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,is_deleted,0,is_closed,0',
					'tpin'=> 'required|security_pin|tpin_match:'.config('tables.ACCOUNT_MST').',trans_pass_key,account_id,'.(isset($userInfo->account_id) ? $userInfo->account_id : 'NULL'),
				],
				'MESSAGES'=>[
					'mobile.required' => trans('affiliate/validator/change_mobile_js.mobile'),
					'mobile.max' => trans('affiliate/validator/change_mobile_js.max_length') ,
					'mobile.unique' => trans('affiliate/validator/change_mobile_js.unique'),
					'tpin.required' => trans('affiliate/validator/change_mobile_js.tpin_not_empty'),
					'tpin.security_pin' => trans('affiliate/validator/change_mobile_js.tpin_invalide'),
					'tpin.tpin_match' => trans('affiliate/validator/change_mobile_js.tpin_invalide')
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
		'RULES'=>[
				'amount'=>'required|regex:/([0-9]+)/',
				'security_pin'=>'required|min:4|regex:/([0-9]+)/',
				'payment_type_id'=>'required',
			]
		],
	],
	'profile'=>[
	    'update'=>[
			'RULES'=>[				
				'firstname'=>'required|firstname',
				'lastname'=>'required|lastname',
				'gender'=>'sometimes|in:'.implode(',', array_values(config('constants.GENDER'))),
                'dob'=>'sometimes|date_format:Y-m-d|before:13 years ago',
				'pan_no'=>'required|panumber',
			],
			'MESSAGES'=>[
                'firstname.required'=>'Please enter first name',
				'firstname.firstname'=>'Please enter first name',
                'lastname.required'=>'Please enter last name',
				'lastname.lastname'=>'Please enter last name correctly',                
				'gender.in'=>'Select Gender',                
				'pan_no.required'=>'Please enetr PAN Number',
				'pan_no.panumber'=>'Invalid PAN Number',			
				'dob.date_format'=>'Invalide Date of Birth',
				'dob.before'=>'Date of Birth 13 years back',
            ]
		],
	],
	'package' => [
		'paymodes'=> [			
			'RULES'=>[
				'id'=>'required|exists:'.config('tables.AFF_PACKAGE_MST').',package_code,status,1',
			],
			'MESSAGES'=>[
				'id.required'=> "Package code missing",
				'id.exists'=> "Invalid Request",
			]
		],		
		'activate' => [
			'RULES'=>[
				'code'=>'required|exists:'.config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').',purchase_code,'.(isset($userInfo->account_id) ? 'account_id,'.$userInfo->account_id : '').',payment_status,1,status,5',
			],
			'MESSAGES'=>[
				'code.required'=> "Please enter display name",
				'code.exists'=> "Invalid Request",
			]			
		]
	],
	'credit_pcc_bonus'=>[
		'RULES'=>[
			'merchant_id'=>'required|min:10|max:10',
			'member_id'=>'required||min:10|max:10',
			'bill_amount'=>'required|regex:/^[0-9]*$/',
			'cv'=>'required',
			'country'=>'required|regex:/^[0-9]*$/',
			'trans_type'=>'required|regex:/^[0-9]*$/',
			'mode'=>'required|regex:/^[0-9]*$/',
		]
	]
];