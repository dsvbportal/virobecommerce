<?php

return [
	'api'=>[
        'v1'=>[
			'seller'=>[
				'login'=>[
                    'ATTRIBUTES'=>[
                        'username'=>[
                            'type'=>'text'
                        ],
                        'password'=>[
                            'type'=>'password'
                        ]
                    ],
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username'),
                        'password'=>Lang::get('general.fields.password')
                    ],
                    'RULES'=>['username'=>'required|min:3|max:100', 'password'=>'required|min:6|max:10']
                ],
				'forgot-password'=>[
                    'ATTRIBUTES'=>[
                        'password'=>[
                            'type'=>'password'
                        ]
                    ],
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username'),
                        'verification_code'=>Lang::get('general.fields.verification_code'),
                        'password'=>Lang::get('general.fields.password'),
                    ],
                    'RULES'=>[
                        'username'=>'required',
                        'verification_code'=>'required|digits:6',
                        'password'=>'required|min:6|max:10'
                    ]
                ],
				'sign-up'=>[
					'ATTRIBUTES'=>[
						'account_mst.pass_key'=>['type'=>'password'],
						'agree'=>['type'=>'checkbox']
					],
					'LABELS'=>[
						'account_details.firstname'=>Lang::get('general.fields.firstname'),
						'account_details.lastname'=>Lang::get('general.fields.lastname'),
						'account_mst.email'=>Lang::get('general.fields.email'),
						'account_mst.mobile'=>Lang::get('general.fields.mobile'),
						'account_mst.pass_key'=>Lang::get('general.fields.password'),
						'agree'=>Lang::get('general.fields.supplier_agree')
					],
					'RULES'=>[
						'account_details.firstname'=>'required|regex:/^[A-Za-z]{3,100}$/',
						'account_details.lastname'=>'required|regex:/^[A-Za-z]{1,50}$/',
						//'account_mst.email'=>'required|email|unique:account_mst,email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER'),
						'account_mst.email'=>'required|email|max:62|unique:'.Config::get('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0,is_closed,0',
						'account_mst.mobile'=>'required|regex:/^[0-9]{10}$/|unique:account_mst,mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER'),
						'account_mst.pass_key'=>'required|min:6|max:10',
						'agree'=>'required'
					]
				],
				'check-verification-mobile'=>[
                    'RULES'=>[
                        'verification_code'=>'required|digits:6'
                    ],
                ],
				'setup'=>[
                    'account-details'=>[
                        'LABELS'=>[
                            'account_supplier.reg_company_name'=>Lang::get('general.fields.reg_company_name'),
                            'account_supplier.company_name'=>Lang::get('general.fields.company_name'),
                            'account_supplier.website'=>Lang::get('general.fields.company_url'),
                            'account_supplier.type_of_bussiness'=>Lang::get('general.fields.type_of_bussiness'),
                            'address.country_id'=>Lang::get('general.fields.country'),
                            'address.state_id'=>Lang::get('general.fields.state'),
                            'address.postal_code'=>Lang::get('general.fields.postal_code'),
                            'address.city_id'=>Lang::get('general.fields.city'),
                            'address.street1'=>Lang::get('general.fields.street1'),
                            'address.street2'=>Lang::get('general.fields.street2'),
                        ],
                        'RULES'=>[
                            'account_supplier.reg_company_name'=>'required|min:3|max:100',
                            'account_supplier.company_name'=>'required|min:3|max:100',
                            'account_supplier.website'=>'required|url',
                            'account_supplier.type_of_bussiness'=>'required',
                            'address.country_id'=>'required',
                            'address.state_id'=>'required',
                            'address.postal_code'=>'required',
                            'address.city_id'=>'required',
                            'address.street1'=>'required',
                            'address.street2'=>'required',
                        ]
                    ],
                    'update-account'=>[
                        'ATTRIBUTES'=>[
                            'store_extras.working_hours_from'=>['type'=>'time'],
                            'store_extras.working_hours_to'=>['type'=>'time'],
                            'store_extras.working_days'=>[
                                'options'=>[],
                                'type'=>'checkbox'
                            ]
                        ],
                        'LABELS'=>[
                            'store_extras.email'=>Lang::get('general.fields.email'),
                            'store_extras.mobile_no'=>Lang::get('general.fields.mobile'),
                            'store_extras.landline_no'=>Lang::get('general.fields.landline_no'),
                            'store_extras.firstname'=>Lang::get('general.fields.firstname'),
                            'store_extras.lastname'=>Lang::get('general.fields.lastname'),
                            'store_extras.state_id'=>Lang::get('general.fields.state'),
                            'store_extras.country_id'=>Lang::get('general.fields.country'),
                            'store_extras.postal_code'=>Lang::get('general.fields.postal_code'),
                            'store_extras.city_id'=>Lang::get('general.fields.city'),
                            'store_extras.address1'=>Lang::get('general.fields.street1'),
                            'store_extras.address2'=>Lang::get('general.fields.street2'),
                            'store_extras.working_hours_from'=>Lang::get('general.fields.timing'),
                            'store_extras.working_hours_to'=>Lang::get('general.fields.timing'),
                            'store_extras.website'=>Lang::get('general.fields.store_url'),
                            'store_extras.working_days'=>Lang::get('general.fields.working_days'),
                        ],
                        'RULES'=>[
                            'store_extras.email'=>'required|email|unique:store_extras,email'.(isset($postdata['store_id']) && !empty($postdata['store_id']) ? ','.$postdata['store_id'].',store_id' : ',NULL,store_id'),
                            'store_extras.mobile_no'=>'required|regex:/^[0-9]{10}$/',
                            'store_extras.landline_no'=>'required',
                            'store_extras.firstname'=>'required|regex:/^[A-Za-z]{3,50}$/',
                            'store_extras.lastname'=>'required|regex:/^[A-Za-z]{3,20}$/',
                            'store_extras.state_id'=>'required',
                            'store_extras.country_id'=>'required',
                            'store_extras.postal_code'=>'required',
                            'store_extras.city_id'=>'required',
                            'store_extras.address1'=>'required',
                            'store_extras.address2'=>'required',
                            'store_extras.working_hours_from'=>'required',
                            'store_extras.working_hours_to'=>'required',
                            'store_extras.website'=>'required|url',
                            'store_extras.working_days'=>'required',
                        ]
                    ],
                    'update-kyc'=>[
                        'ATTRIBUTES'=>[
                            'kyc_verifiacation.dob'=>[
                                'type'=>'date'
                            ],
                            'auth_person_id_proof'=>[
                                'type'=>'file',
                                'accept'=>'image/*'
                            ],
                            'pan_card_image'=>[
                                'type'=>'file',
                                'accept'=>'image/*'
                            ]
                        ],
                        'LABELS'=>[
                            'kyc_verifiacation.pan_card_no'=>Lang::get('general.fields.pan'),
                            'kyc_verifiacation.gstin'=>Lang::get('general.fields.gstin'),
                            'kyc_verifiacation.pan_card_name'=>Lang::get('general.fields.pan_card_name'),
                            'kyc_verifiacation.dob'=>Lang::get('general.fields.dob_on_pan'),
                            'kyc_verifiacation.pan_card_image'=>Lang::get('general.fields.pan_card_image'),
                            'kyc_verifiacation.vat_no'=>Lang::get('general.fields.vat_no'),
                            'kyc_verifiacation.cst_no'=>Lang::get('general.fields.cst_no'),
                            'kyc_verifiacation.auth_person_name'=>Lang::get('general.fields.auth_person_name'),
                            'kyc_verifiacation.auth_person_id_proof'=>Lang::get('general.fields.auth_person_id_proof'),
                            'auth_person_id_proof'=>Lang::get('general.fields.auth_person_id_proof'),
                            'pan_card_image'=>Lang::get('general.fields.pan_card_image'),
                            'kyc_verifiacation.id_proof_document_type_id'=>Lang::get('general.fields.id_proof_type')
                        ],
                        'RULES'=>[
                            'kyc_verifiacation.pan_card_no'=>'required|regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/|unique:supplier_kyc_verification,pan_card_no',
                            'kyc_verifiacation.gstin'=>'required|regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/|unique:supplier_kyc_verification,gstin',
                            'kyc_verifiacation.pan_card_name'=>'required|min:3',
                            'kyc_verifiacation.dob'=>'required|date_format:Y-m-d|between:'.date('Y-m-d', strtotime('-110 years')).','.date('Y-m-d', strtotime('-18 years')),
                            'pan_card_image'=>'required|mimes:jpeg,gif,bmp,png',
                            'kyc_verifiacation.vat_no'=>'required',
                            'kyc_verifiacation.cst_no'=>'required',
                            'kyc_verifiacation.auth_person_name'=>'required',
                            'kyc_verifiacation.id_proof_document_type_id'=>'required',
                            'auth_person_id_proof'=>'required|mimes:jpeg,gif,bmp,png',
                        ]
                    ],
                    'store-banking'=>[
                        'LABELS'=>[
                            'payment_setings.bank_name'=>Lang::get('general.fields.bank_name'),
                            'payment_setings.account_holder_name'=>Lang::get('general.fields.account_holder_name'),
                            'payment_setings.account_no'=>Lang::get('general.fields.account_no'),
                            'payment_setings.account_type'=>Lang::get('general.fields.account_type'),
                            'payment_setings.ifsc_code'=>Lang::get('general.fields.ifsc_code'),
                            'payment_setings.country_id'=>Lang::get('general.fields.country'),
                            'payment_setings.state_id'=>Lang::get('general.fields.state'),
                            'payment_setings.postal_code'=>Lang::get('general.fields.postal_code'),
                            'payment_setings.city_id'=>Lang::get('general.fields.city'),
                            'payment_setings.address1'=>Lang::get('general.fields.street1'),
                            'payment_setings.address2'=>Lang::get('general.fields.street2'),
                            'payment_setings.branch'=>Lang::get('general.fields.branch'),
                            'payment_setings.pan'=>Lang::get('general.fields.pan'),
                        ],
                        'RULES'=>[
                            'payment_setings.bank_name'=>'required|min:3',
                            'payment_setings.account_holder_name'=>'required|min:3',
                            'payment_setings.account_no'=>'required|min:4|max:17',
                            'payment_setings.account_type'=>'required',
                            'payment_setings.ifsc_code'=>'required|regex:/^[A-Za-z]{4}[0][A-Za-z0-9]{6}$/',
                            'payment_setings.country_id'=>'required',
                            'payment_setings.state_id'=>'required',
                            'payment_setings.postal_code'=>'required',
                            'payment_setings.city_id'=>'required',
                            'payment_setings.address1'=>'required',
                            'payment_setings.address2'=>'required',
                            'payment_setings.branch'=>'required',
                            'payment_setings.pan'=>'required|regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/',
                        ]
                    ]
                ],
				'products'=>[
                    'save'=>[
                        'ATTRIBUTES'=>[
                            'details.weight'=>['step'=>0.1],
                            'details.height'=>['step'=>0.1],
                            'details.length'=>['step'=>0.1],
                            'details.width'=>['step'=>0.1]
                        ],
                        'LABELS'=>[
                            'product.product_name'=>Lang::get('product_browse.product_name'),
                            'details.sku'=>Lang::get('product_browse.sku'),
                            'details.eanbarcode'=>Lang::get('product_browse.eanbarcode'),
                            'details.upcbarcode'=>Lang::get('product_browse.upcbarcode'),
                            'details.description'=>Lang::get('product_browse.description'),
                            'details.is_exclusive'=>Lang::get('product_browse.is_exclusive'),
                            'details.visiblity_id'=>Lang::get('product_browse.visiblity_id'),
                            'details.weight'=>Lang::get('product_browse.weight'),
                            'details.height'=>Lang::get('product_browse.height'),
                            'details.length'=>Lang::get('product_browse.length'),
                            'details.width'=>Lang::get('product_browse.width'),
                            'tags'=>Lang::get('product_browse.tags'),
                            'meta_info.description'=>Lang::get('product_browse.meta_description'),
                            'meta_info.meta_keys'=>Lang::get('product_browse.meta_keys'),
                            'product.category_id'=>Lang::get('product_browse.category'),
                            'product.brand_id'=>Lang::get('product_browse.brand'),
                        ],
                        'RULES'=>[
                            'product.product_name'=>'required',
                            'details.sku'=>'required',
                            //'details.eanbarcode'=>'required|regex:'.getRegex('eanbarcode'),
                            //'details.upcbarcode'=>'required|regex:'.getRegex('upcbarcode'),
                            'details.eanbarcode'=>'required',
                            'details.upcbarcode'=>'required',
                            'details.description'=>'required',
                            'details.is_exclusive'=>'required',
                            'details.visiblity_id'=>'required',
                            'details.weight'=>'required|numeric|min:0.1|max:999999999',
                            'details.height'=>'required|numeric|min:0.1|max:999999999',
                            'details.length'=>'required|numeric|min:0.1|max:999999999',
                            'details.width'=>'required|numeric|min:0.1|max:999999999',
                            'tags'=>'required',
                            'meta_info.description'=>'required',
                            'meta_info.meta_keys'=>'required',
                            'product.category_id'=>'required',
                            'product.brand_id'=>'required',
                        ]
                    ]
                ]
			],
			'products'=>[
				'price'=>[
					'save'=>[
						'RULES'=>[							
							'spp.mrp_price'=>'required',
							'spp.price'=>'required',
						],
						//'MESSAGES'=>Lang::get('product_items.validation')
						'MESSAGES'=>[ 
							'supplier_product_new.store_id.required'=>'Please select Store',
							'product.category_id.required'=>'Please select Catergory',
							'product.brand_id.required'=>'Please select Brand',
							'product.product_name.required'=>'Please enter Product Name',
							'product.sku.required'=>'Please enter SKU',
							'product.description.required'=>'Please enter Description',
							'supplier_product_new.currency_id.required'=>'Please select Currency',
							'supplier_product_new.mrp_price.required'=>'Please enter MRP Price',
							'supplier_product_new.price.required'=>'Please enter Price',
						]
					],
				],
			],
		],
	],
	'seller'=>[
		'sign-up'=>[
			'ATTRIBUTES'=>[
				'account_mst.pass_key'=>['type'=>'password'],
				'agree'=>['type'=>'checkbox']
			],
			'LABELS'=>[
				'account_details.firstname'=>Lang::get('general.fields.firstname'),
				'account_details.lastname'=>Lang::get('general.fields.lastname'),
				'account_mst.email'=>Lang::get('general.fields.email'),
				'account_mst.mobile'=>Lang::get('general.fields.mobile'),
				'account_mst.pass_key'=>Lang::get('general.fields.password'),
				'agree'=>Lang::get('general.fields.supplier_agree')
			],
			'RULES'=>[
				'account_details.firstname'=>'required|regex:/^[A-Za-z]{3,100}$/',
				'account_details.lastname'=>'required|regex:/^[A-Za-z]{1,50}$/',
				//'account_mst.email'=>'required|email|unique:account_mst,email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER'),
				'account_mst.email'=>'required|email|max:62|unique:'.Config::get('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0,is_closed,0',
				'account_mst.mobile'=>'required|regex:/^[0-9]{10}$/|unique:account_mst,mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER'),
				'account_mst.pass_key'=>'required|min:6|max:10',
				'agree'=>'required'
			]
		],
	],
	
	
];