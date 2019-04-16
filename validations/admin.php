<?php	
return [	
    'affiliate'=>[
		     'save'=>[
			'RULES'=>[
				'first_name'=>'required|firstname|min:3|max:30',
				'last_name'=>'required|lastname|min:3|max:30',
				'email'=>'required|email|unique:account_mst,email',
				'uname'=>'required|username|unique:account_mst,uname',
				'password'=>'required|min:6|max:10',
				'confirm_password'=>'required|min:6|max:10|password|same:password',
				'zipcode'=>'required|zipcode',
				'country'=>'required|',
			],
			'MESSAGES' => [
				'firstname.required' => "Please enter first Name",
				'firstname.firstname' => "First Name should contain only alphapets",
				'firstname.min' => "First name must contain atleast 3 char",
				'firstname.max' => "First name must not exist 30 char",
				'lastname.required' => "Please enter Last Name",
				'lastname.lastname' => "Last Name should contain only alphapets",
				'lastname.min' => "Last name must contain atleast 1 char",
				'lastname.max' => "Last name must not exist 30 char",
				'email.required' => "Please enter email address",
				'email.email' => "Please enter valide email address",
				'username.required' => "Please enter desire username",
				'username.regex' => "Please enter A-Z,0-9",
				'username.min' => "Username must contain atleast 6 char",
				'username.max' => "Username must not exist 30 char",
				'password.required' => "Please enter your password",
				'password.min' => "Password must contain atleast 6 char",
				'password.max' => "Password must not exist 30 char",
				'postcode.required' => "Please enter your Zipcode/Postal code",
				'postcode.zipcode' => "Invalide Zipcode/Postal code",
				'confirm_password.required' => "Please enter your password",
				'confirm_password.min' => "Password must contain atleast 6 char",
				'country.required' => "Please select country",
			]			
		  ]
	],
	'aff'=>[
		'root-account'=>[
			'save'=>[
				'RULES'=>[
					'first_name'=>'required|firstname|min:3|max:30',
					'last_name'=>'required|lastname|min:3|max:30',
					'email'=>'required|email|unique:account_mst,email',
					'uname'=>'required|username|unique:account_mst,uname',
					'password'=>'required|min:6',
					'confirm_password'=>'required|min:6|max:10|password|same:password',
					'zipcode'=>'required|zipcode',
					'country'=>'required|',
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
					'email.required' => "Please enter email address",
					'email.email' => "Please enter valide email address",
					'username.required' => "Username cannot be empty",
					'username.regex' => "Please enter A-Z,0-9",
					'username.min' => "Username must contain atleast 6 char",
					'username.max' => "Username must not exist 30 char",
					'password.required' => "Password cannot be empty",
					'password.min' => "Password must contain atleast 6 char",
					'postcode.required' => "Zipcode/Postal code cannot be empty",
					'postcode.zipcode' => "Invalide Zipcode/Postal code",
					'confirm_password.required' => "Please enter your password",
					'confirm_password.min' => "Password must contain atleast 6 char",
					'country.required' => "Country cannot be empty",
				]			
			]
		]
	],
	'account'=>[
		'email'=>[
			'RULES'=>[
                    //'email'=>'required|email|unique:'.config('tables.ACCOUNT_MST').',email,NULL,id,is_deleted,0,is_closed,0'
					'email'=>'required|email|unique:'.config('tables.ACCOUNT_MST').',email,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',
                ],
                'LABELS'=>[
                    //'propin_session'=>'seller/account.propin_session',
                    'email'=>'New Email'
                ],
                'MESSAGES'=>[
                    'email.required'=>'Email Id is required',
                    'email.email'=>'Please enter the Correct format email address ',
                    'email.unique'=>'The email id is already in use',
                ]
		], 
		'update_mobile'=>[
			'RULES'=>[
				/*'mobile'=>'required|regex:/^[0-9]{10}$/|unique:'.Config::get('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,3,is_deleted,0',*/					
				'mobile'=>'required|regex:/^[0-9]{10}$/|unique:'.config('tables.ACCOUNT_MST').',mobile,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',
			],
			'LABELS'=>[
				
				  'mobile'=>'seller/account.new_phone',
				  
			],
			'MESSAGES'=>[
				'mobile.required'=>'Mobile No required',
				'mobile.unique'=>'The Mobile Number is already in use',
				'mobile.regex'=>'Please Enter valid Mobile Number',
			]
		],
		'updatepwd'=>[
	        'RULES'=>[
				'new_pwd'=>'required|password|min:6|max:16',
			  ],
	        'LABELS'=>[
					'new_pwd'=>'New password',
				  ],
		     'MESSAGES'=>[
				'new_pwd.required'=>'New Password is required',
				'new_pwd.password'=>'You entered an invalid password. Try again!',
				'new_pwd.min'=>'New Password must be at least 6 characters',
				'new_pwd.max'=>'Your New password can\'t be longer than 16 characters',
				]
		],
        'updatepin'=>[
		   'RULES'=>[
				'new_pin'=>'required|security_pin|min:4|max:4',
			],
			'LABELS'=>[
				'new_pin'=>'Security PIN',
			],
			'MESSAGES'=>[
				'new_pin.required'=>'Please Enter Security PIN',
				'new_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'new_pin.min'=>'Security PIN must have 4 digit',
				'new_pin.max'=>'Your Security PIN can\'t be longer than 4 digit',
			]
		
		],
	],
	'franchisee'=>[
		'save'=>[
			'RULES'=>[
				'fran_type'=>'required|regex:/^[0-9]{1}$/|exists:'.config('tables.FRANCHISEE_LOOKUP').',franchisee_typeid',
				'first_name'=>'required|firstname|min:3|max:30',
				'last_name'=>'required|lastname|min:1|max:30',
				'gender'=>'required',
				'email'=>'required|email|unique:account_mst,email',
				'mobile'=>'required|unique:account_mst,mobile,NULL,account_id,is_deleted,0',
				'uname'=>'required|username|unique:account_mst,uname',
				'password'=>'required|min:6',
				'tpin'=>'required|security_pin',
				'zipcode'=>'required|zipcode',
				'state'=>'required|exists:'.config('tables.LOCATION_STATE').',state_id',
				'city'=>'required|exists:'.config('tables.LOCATION_CITY').',city_id',				
				'district'=>'required|exists:'.config('tables.LOCATION_DISTRICTS').',district_id',
				'country'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id',
				/*'currency'=>'required|exists:'.config('tables.CURRENCIES').',currency_id',*/
				/* 'dob'=>"required|date_format:Y-m-d|before:13 years ago", */
				'company_name'=>'required|full_name|min:5|max:50',
				'company_address'=>'required_if:office_available,1',				
				'franchisee_state'=>'required_if:office_available,1|exists:'.config('tables.LOCATION_STATE').',state_id',				
				'franchisee_district'=>'required_if:office_available,1|exists:'.config('tables.LOCATION_DISTRICTS').',district_id',
				'franchisee_city'=>'required_if:office_available,1|exists:'.config('tables.LOCATION_CITY').',city_id',				
				'franchisee_zipcode'=>'required_if:office_available,1',
			],
			'MESSAGES' => [
				'fran_type.required' => "Franchisee Type cannot be empty",
				'fran_type.exists' => "Invalid Franchisee Type",
				'firstname.required' => "First Name cannot not be empty",
				'firstname.firstname' => "First Name should contain only alphapets",
				'firstname.min' => "First name must contain atleast 3 char",
				'firstname.max' => "First name must not exist 30 char",
				'lastname.required' => "Last Name cannot not be empty",
				'lastname.lastname' => "Last Name should contain only alphapets",
				'lastname.min' => "Last name must contain atleast 1 char",
				'lastname.max' => "Last name must not exist 30 char",
				'email.required' => "Email address cannot be empty",
				'email.email' => "Please enter valide email address",
				'mobile.required' => "Mobile Number cannot be empty",
				'mobile.mobile' => "Please enter a valid Mobile Number",
				'mobile.unique' => "Mobile number already exist",
				'uname.required' => "Username cannot be empty",
				'uname.regex' => "Please enter A-Z,0-9",
				'uname.min' => "Username must contain atleast 6 char",
				'uname.max' => "Username must not exist 30 char",
				'password.required' => "Password cannot be empty",
				'password.min' => "Password must contain atleast 6 char",
				'tpin.required' => 'Security PIN cannot not be empty',
				'tpin.security_pin' => 'Invalide Security PIN',				
				'zipcode.required' => "Please enter your Zipcode/Postal code",
				'zipcode.zipcode' => "Invalide Zipcode/Postal code",				
				'state.required' => "Please select State",
				'state.exists' => "Invalid State",
				'district.required' => "Please select District",
				'district.exists' => "Invalid District",
				'city.required' => "Please select City",
				'city.exists' => "Invalid City",
				'country.required' => "Please select country",
				'country.exists' => "Invalid Country",
				'currency.required' => "Please select Currency",
				'currency.exists' => "Invalid Currency",
				/* 'dob.required' => "Please select your Date of Birth",
				'dob.date_format'=>'Date format should be (yyyy-mm-dd)',
				'dob.before'=>'Date of Birth should be 13 years back',   */
				'company_name.required' => "Franchisee Name cannot be empty",
				'company_name.full_name' => "Invalid Franchisee Name",
				'company_name.min' => "Franchisee Name must contain atleast 5 char",
				'company_name.max' => "Franchisee Name must not exist 50 char",
				'company_address.required'=>'Address cannot be empty',				
				'landmark.required' => "Please enter your Zipcode/Postal code",
				'franchisee_zipcode.required' => "Please enter your Zipcode/Postal code",				
				'franchisee_state.required' => "Please select State",
				'franchisee_state.exists' => "Invalid State",
				'franchisee_district.required' => "Please select District",
				'franchisee_district.exists' => "Invalid District",
				'franchisee_city.required' => "Please select City",
				'franchisee_city.exists' => "Invalid City",
			]
		],
		'edit-save' => [
		    'RULES'=>[
			    'company_name'=>'required|full_name|min:5|max:50',
				'office_available'=>'required|in:0,1',		
				'firstname'=>'required|firstname|min:3|max:30',
				'lastname'=>'required|lastname|min:1|max:30',
				/* 'dob'=>"required|date_format:Y-m-d|before:18 years ago", */
				'editaddr'=>'required|in:0,1',
				'edit_fr_addr'=>'required|in:0,1',
				
				'address.flatno_street'=>'required_if:editaddr,1',
				'address.landmark'=>'required_if:editaddr,1',
				'address.postal_code'=>'required_if:editaddr,1|zipcode',
			    'address.city_id'=>'required_if:editaddr,1',
				'address.state_id'=>'required_if:editaddr,1', 
				'address.district_id'=>'required_if:editaddr,1', 
				
				'fr_address.company_address'=>'required_if:edit_fr_addr,1',
				'fr_address.landmark'=>'required_if:edit_fr_addr,1',
				'fr_address.franchisee_zipcode'=>'required_if:edit_fr_addr,1|zipcode',
			    'fr_address.fr_city_id'=>'required_if:edit_fr_addr,1',
				'fr_address.fr_state_id'=>'required_if:edit_fr_addr,1', 
				'fr_address.fr_district_id'=>'required_if:edit_fr_addr,1', 
				'country'=>'required_if:editaddr,1',			
                'company_name.required' => "Franchisee Name cannot be empty",
				'company_name.full_name' => "Invalid Franchisee Name",
				'company_name.min' => "Franchisee Name must contain atleast 5 char",
				'company_name.max' => "Franchisee Name must not exist 50 char",				
			],
			'LABELS'=>[
                'office_available'=>'Office Available',
				'company_address'=>'Company address',			
				'firstname'=>'First name',
				'lastname'=>'Last name',
				'dob'=>'dob',
				'editaddr'=>'Editaddr',
				'address.flatno_street'=>'Flat Number',
				'address.landmark'=>'Landmark',
				'address.postal_code'=>'Postal Code',
			    'address.city_id'=>'City',
				'address.state_id'=>'State', 
				'country_id'=>'Country',
				'email'=>'Email',
				'office_phone'=>'Mobile',
            ],
			'MESSAGES' => [
				'office_available.required'=>'Office available is required',
				'company_address.required_if'=>'Office available is required',
				'firstname.required'=>'First Name cannot not be empty',
				'firstname.firstname'=>'First Name should contain only alphapets',
				'firstname.min'=>'First name must contain atleast 3 char',
				'firstname.max'=>'First name must not exist 50 char',
				'lastname.required'=>'Last Name cannot not be empty',
				'lastname.lastname'=>'Last Name should contain only alphapets',
				'lastname.min'=>'Last name must contain atleast 1 char',
				'lastname.max'=>'Last name must not exist 50 char',
				'dob.required' => "Please select your Date of Birth",
				'dob.date_format'=>'Date format should be (yyyy-mm-dd)',
				'dob.before'=>'Date of Birth should be 13 years back',
				'editaddr.required'=>'Address is required',
				'address.flatno_street.required_if'=>'Flat number is required',
				'address.landmark.required_if'=>'Landmark is required',
				'address.postal_code.required_if'=>'Postal code is required',
				'address.postal_code.zipcode' => "Invalide Zipcode/Postal code",	
				'address.city_id.required_if'=>'City is required',
				'address.state_id.required_if'=>'State is required', 
				'address.district_id.required_if'=>'District is required', 
				'fr_address.company_address.required_if'=>'Flat number is required',
				'fr_address.landmark.required_if'=>'Landmark is required',
				'fr_address.franchisee_zipcode.required_if'=>'Postal code is required',
				'fr_address.franchisee_zipcode.zipcode' => "Invalide Zipcode/Postal code",
				'fr_address.fr_city_id.required_if'=>'City is required',
				'fr_address.fr_state_id.required_if'=>'State is required',
				'fr_address.fr_district_id.required_if'=>'District is required',
				'country_id.required_if'=>'Country is required',
				'email.required' => "Email address cannot be empty",
				'email.email' => "Please enter valide email address",
				'office_phone.required' => "Mobile Number cannot be empty",
				'office_phone.mobile' => "Please enter a valid Mobile Number",
				'office_phone.unique' => "Mobile number already exist",
			]
		], 
		'change-email'=>[
			'RULES'=>[
				'email'=>'required|unique:'.config('tables.ACCOUNT_MST').',email,null,account_id,account_type_id,'.config('constants.ACCOUNT_TYPE.FRANCHISEE').',is_deleted,0',
				'account_id'=>'required|exists:'.config('tables.ACCOUNT_MST').',account_id,account_type_id,'.config('constants.ACCOUNT_TYPE.FRANCHISEE').',is_deleted,0',
			]
		],
		'change-mobile' => [
			'RULES'=>[
				'account_id'=>'required|exists:'.config('tables.ACCOUNT_MST').',account_id,account_type_id,'.config('constants.ACCOUNT_TYPE.FRANCHISEE').',is_deleted,0',
				'new_mobile'=>'required|unique:'.config('tables.ACCOUNT_MST').',mobile,null,account_id,is_deleted,0'
			],	       
			'MESSAGES'=>[
				'account_id.required'=>'Account ID cannot be empty',
				'account_id.exists'=>'Account ID not exist',
				'new_mobile.required'=>'New Mobile cannot be empty',
				'new_mobile.unique'=>'Mobile number already exist',
			]
		],
		'reset-pwd' => [],
		'reset-pin' => [],
		'block' => [],
		'packages'=>[
			'RULES'=>[
				'frans_type'=>'required|regex:/^[0-9]{1}$/',
				'country'=>'required|regex:/^[0-9]{1,3}$/',
			],	       
			'MESSAGES'=>[
				'frans_type.required'=>'Franchisee Type cannot be empty',
				'frans_type.regex'=>'Invalid Franchisee Type',
				'country.required'=>'Country cannot be empty',
				'country.regex'=>'Invalid Country',
			]
		],
		'validate'=>[
			'username'=>[
				'RULES'=>[
					'uname'=>'required|min:6|max:30|regex:/^[a-zA-Z][a-zA-Z0-9]+$/|unique:account_mst,uname',
				],	       
				'MESSAGES'=>[
					'uname.required' => "Please enter desire username",
					'uname.regex' => "Username must starts with alphabets followed by numbers",
					'uname.min' => "Username must contain atleast 6 char",
					'uname.max' => "Username must not exist 30 char",
					'uname.max' => "Username already exist",
				]
			],
			'email'=>[
				'RULES'=>[
					'email'=>'required|email|unique:account_mst,email',
				],	       
				'MESSAGES'=>[
					'email.required' => "Please enter email address",
					'email.email' => "Please enter valide email address",
					'email.email' => "Email address already exist",
				]
			]
		]
	],	
];