<?php

return [
	'PAYMENT_MODE_IMG_PATH'=>['LOCAL'=>'resources/uploads/payment-mode/', 'WEB'=>'imgs/payment-mode/100/75/'],
    'DUMMY_IMG_PATH'=>'/resources/assets/imgs/dummy.png',
    'UPLOAD_FILE_PATH'=>'/resources/uploads/',
    'PRODUCT_ORIGINAL_IMG_UPLOAD_PATH'=>'/assets/uploads/product_imgs/original',
    'PRODUCT_THUMBNAIL_IMG_UPLOAD_PATH'=>'/assets/uploads/product_imgs/thumbnail',
    'PRODUCT_IMG_PATH'=>'product_imgs/',
    'PRODUCT_THUMBNAIL_PATH'=>'/assets/uploads/product_imgs/',
    'APP_SETTINGS'=>'app_settings/',
    'SUPPLIER_PANCARD'=>'resources/uploads/suppliers/pan_cards/',
    'SUPPLIER_IDPROOF'=>'resources/uploads/suppliers/id_proof/',
    'BCATEGORY_ICONS_PATH'=>[
		'API'=>'resources/uploads/categories/'
	],
	'FLAGS_PATH'=>'imgs/country-flag/',
	'BCATEGORY_ICONS_PATH'=>[
		'LOCAL'=>'resources/uploads/bcategories/icons/', 
		'API'=>'imgs/categories-icons/160/160/', 
		'UPLOAD'=>[ 
			'WIDTH'=>500, 
			'HEIGHT'=>250,
		],
	],
    'BCATEGORY_IMG_PATH'=>[
		'LOCAL'=>'resources/uploads/bcategories/imgs/', 
		'API'=>'imgs/categories-imgs/275/171/', 
		'UPLOAD'=>[ 
			'WIDTH'=>500, 
			'HEIGHT'=>250,
		],
	],
	'STORE_LOGO_PATH'=>[
		'LOCAL'=>'resources/uploads/stores/', 
		'WEB'=>'imgs/stores/200/100/', 
		'API'=>'imgs/stores/200/100/', 
		'SM'=>'imgs/stores/sm/', 
		'DEFAULT'=>'imgs/stores/75/75/store.png', 
		'WEB_100X27'=>'imgs/stores/100/27/', 
		'DEFAULT_FILE_NAME'=>'store.png',
	],
  'SELLER' => [
        'LOGO_IMG_PATH' => [
            'LOCAL' => 'resources/uploads/seller/logo/',
            'WEB' => 'imgs/seller/logo/xs/',
            'SM' => 'imgs/seller/logo/sm/',
            'DEFAULT' => 'resources/uploads/seller/logo/default-logo.png',
            'DEFAULT_FILE_NAME' => 'default-logo.png'
        ],
		'PROOF_DETAILS' => [
			'LOCAL' => 'resources/uploads/seller/proof_details/',
			'WEB' => 'imgs/seller-proof-details/xs/',
			'SM' => 'imgs/seller-proof-details/sm/',
			'MD' => 'imgs/seller-proof-details/md/',
			'LG' => 'imgs/seller-proof-details/lg/',
			'XL' => 'imgs/seller-proof-details/xl/',
			'DEFAULT' => 'imgs/seller-proof-details/sm/defaul.png',
			'DEFAULT_FILE_NAME' => 'default-logo.png'
		],
    ],
	'MERCHANT'=>[
        'LOGO_PATH'=>[
			'TEMPPATH'=>'resources/uploads/retailers/logo_cache/', 
			'LOCAL'=>'resources/uploads/merchant/', 
			'WEB'=>'imgs/merchant/75/75/', 
			'SM'=>'imgs/merchant/sm/', 
			'DEFAULT'=>'resources/uploads/merchant/default-logo.png', 
			'DEFAULT_FILE_NAME'=>'default-logo.png'
		],
        'KYC'=>['LOCAL'=>'resources/uploads/kyc/merchant', 'WEB'=>'attachments/kyc/'],
        'KYC_ORGINAL'=>['LOCAL'=>'resources/uploads/kyc/merchant/original', 'WEB'=>'attachments/kyc/'],
        'IMG_UPLOAD_SETTINGS'=>'merchant_upload_file_settings',
        'IMG_GALLERY_PATH'=>[
			'LOCAL'=>'resources/uploads/merchant/gallery/', 
			'WEB'=>'imgs/merchant-gallery/750/500/', 
			'SM'=>'imgs/merchant-gallery/sm', 
			'LG'=>'imgs/merchant-gallery/lg', 
			'DEFAULT'=>'resources/uploads/merchant/default-logo.png',
		],        
    ],
	'ONLINE'=>[
        'NETWORK'=>[            
            'LOGO_PATH'=>[
				'LOCAL'=>'resources/uploads/affilate/network/logo/', 
				'WEB'=>'imgs/online-network/350/240/', 
				'DEFAULT'=>'resources/uploads/stores/store.png'
			],
        ],
        'STORE'=>[            
            'LOGO_URL'=>['XS'=>'imgs/online-store-logo-url/xs', 'SM'=>'imgs/online-store-logo-url/sm', 'MD'=>'imgs/online-store-logo-url/md', 'LG'=>'imgs/online-store-logo-url/lg'],
            'LOGO_PATH'=>[
				'LOCAL'=>'resources/uploads/affilate/store/logo/', 
				'WEB'=>[
					'XS'=>'imgs/online-store/xs/', 
					'SM'=>'imgs/online-store/sm/', 
					'MD'=>'imgs/online-store/md/', 
					'LG'=>'imgs/online-store/lg/'
				], 
				'DEFAULT'=>'resources/uploads/stores/store.png'
			],
            'BANNER_PATH'=>['LG'=>'resources/uploads/affilate/store/banners/'],
            'COUPONS'=>['LOCAL'=>'resources/uploads/affilate/store/coupons/', 'WEB'=>['SM'=>'imgs/online-partners/coupons/sm/', 'XS'=>'imgs/online-partners/coupons/xs/']]
        ],        
        'LOGO_PATH'=>['LOCAL'=>'resources/uploads/affilate/logo', 'WEB'=>'imgs/affiliate-logos/']
    ],
];

