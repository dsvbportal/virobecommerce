<?php

// if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); 	   //truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
return [
    'ON'=>1,
    'OFF'=>0,	
	'ACTIVE'=>1,	
	'INACTIVE'=>0,
	'NOT_DELETED' => 0,
	'STATUS'=>['ACTIVE'=>1,'INACTIVE'=>2],
	'GENDER'=>['MALE'=>1, 'FEMALE'=>2, 'TRANSGENDER'=>3],
	'LANG'=>['en'=>1, 'ch'=>2],
	'BONUS'=>[
		'SIGNUP'=>'SIGNUP-BONUS',
		'REFERRAL'=>'REFERRAL-BONUS',
		'TEAM'=>'TEAM-BONUS',
		'LEADERSHIP'=>'LEADERSHIP-BONUS',	 
		'CAR_BONUS'=>'CAR-BONUS',
		'STATUS_PENDING' => 0,
		'PENDING' =>'PENDING',
		'CONFIRM'=>'CONFIRM',
		'TYPE1' =>1,
		'TYPE2' =>2,
	],
	'BONUS_TYPE' => [
		'PERSONAL_BONUS'=>1,
		'FAST_START_BONUS'=>2,		
		'TEAM_BONUS'=>3,
		'LEADERSHIP_BONUS'=>4,	 
		'CAR_BONUS'=>5,
		'AMBASSADOR_BONUS'=>6,
		'STAR_BONUS'=>7
	] ,
   'CREDIT'=>1,
   'DEBIT'=>2,
   'TRANS_TYPE'=>['CREDIT'=>1, 'DEBIT'=>2],
   'FUND_TRANSFER_TYPE'=>['EXTR'=>0, 'INTR'=>1, 'FR_TO_FR'=>3, 'FR_TO_USER'=>4], 
   'FUND_TRANSFER_STATUS'=>['1'=>'Confirmed', '0'=>'Pending','2'=>'Refunded'],
   'REFERRAL_EARNINGS_STATUS'=>[
	   'PENDING'=>0,
	   'Confirmed'=>1,
	   'Lapsed'=>4,
	   'Waiting'=>5,
   ],
   'ACCOUNT_USER'=>[
	   'BLOCK'=>1,
	   'UNBLOCK'=>0
   ],     
   'TEAM_GENARATION'=>[
		"1G"=>1,
		"2G"=>2,
		"3G"=>3,
	],
	'VERIFY_AFFILIATE_STATUS'=>[
	     'PENDING'=>0,
		 'ACTIVE'=>1,
		 'REJECTED'=>2,
	],
	'ACCOUNT' => [
		'ADMIN_ID'=>1,
		'Active' => 'ACTIVE',
		'Inactive' =>'INACTIVE',
		'PROFILE_IMG'=> [
			'PATH'=>'resources/uploads/account/profileimg/', 
			'LOCAL'=>'resources/uploads/account/profileimg/', 
			'SM'=>'img/profile/sm/', 
			'DEFAULT'=>'blank.png'
		]
	],
   'currency_rate'=>[
       'ON'=>1,
	   'OFF'=>0,
	 ],
	/* payment status */
	'PAYMENT_PENDING' => 0,
	'PAYMENT_PAID' => 1,
    'WITHDRAWAL_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'PROCESSING'=>2, 'CANCEL'=>3, 'CANCELLED'=>3, 0=>'PENDING', 1=>'CONFIRMED', 2=>'PROCESSING', 3=>'CANCELLED'],
	//'withdrawal_status' => [0 => 'warning', 1 => 'success', 2 => 'info', 3 => 'danger'],
	'ADDRESS_POSTTYPE'=>['ACCOUNT'=>1, 'MERCHANT'=>2, 'STORE'=>3,'FRANCHISEE'=>5],
	'ADDRESS_TYPE'=>['PRIMARY'=>1, 'SECONDARY'=>2, 'SHIPPING'=>3,'WAREHOUSE'=>4],
	
	/* Paymodes */
	'GATEWAY_WALLET' => 1,
	'GATEWAY_PAYUMONEY' => 2,
	'GATEWAY_CASHFREE' => 3,
	'GATEWAY_PAYTM' => 4,
	'WITHDRAWAL_PAYMODES'=>['GATEWAY_WALLET'=>1, 'GATEWAY_PAYUMONEY'=>2, 'GATEWAY_CASHFREE'=>3, 'GATEWAY_PAYTM'=>4],	
	
	'PAYOUT_IMAGE_PATH' => 'assets/img/paymodes/',
	/* BankTrasfer payout */
	'BANK_TRANSFER'=>2,
	'DEFAULT_CURRENCY_ID'=>1,	
	'DEFAULT_CURRENCY_CODE'=>'USD',
	'STATUS_PENDING'=>0,
    'STATUS_CONFIRMED'=>1,
    'STATUS_RENEWED'=>2,
    'STATUS_EXPIRED'=>2,
    'STATUS_CANCELLED'=>3,
    'STATUS_LAPSED'=>4,
	'ADMIN_ADD_FUNDEMAIL'=>'admin_addedfund_email',
	/* Ending */
    'ACCOUNTABLE'=>1,
    'ACCOUNT_CREATION_STEPS'=>['START'=>0, 'ACCOUNT_DETAILS'=>1, 'STORE_BANK'=>2, 'ACCOUNT_UPDATE'=>3, 'VERIFY_KYC'=>4,'TAX_INFO' => 5],
	/* 'ACCOUNT_CREATION_STEPS' => ['START' => 0, 'GENERAL_DETAILS' => 1, 'BUSSINESS_DETAILS' => 2, 'EMAIL_VERIFICATION' => 3, 'MANAGE_CASHBACK' => 4, 'TAX_INFO' => 5, 'BANK_DETAILS' => 6], */
    'ACCOUNT_TYPE'=>['ADMIN'=>1, 'USER'=>2, 'SUPPLIER'=>3, 'SELLER'=>3,'FRANCHISEE'=>4,'SELLER_EMP'=>5,'SYS_SELLER'=>6],
    'AFFILIATE_TYPE'=>['IND'=>1, 'PRS'=>2, 'PS'=>3, 'CM'=>4],
	'FRANCHISEE_TYPE'=>['COUNTRY'=>1,'REGION'=>2,'STATE'=>3,'DISTRICT'=>4,'CITY'=>5,1=>'COUNTRY',2=>'REGION',3=>'STATE',4=>'DISTRICT',5=>'CITY'],
	'FRANCHISEE_ACCESS_TYPE' => ['PRIMARY'=>1],
    'ADDRESS_POST_TYPE'=>[ 'ACCOUNT'=>1,'MERCHANT'=>2,'SELLER'=>3, 'STORE'=>4,'FRANCHISEE'=>5],
    'RATING_POST_TYPE'=>[ 'STORE'=>3],
    'ACTIVATED'=>1,
    'ACTIVATE_SUBSCRIPTION'=>1,
    'ACTIVATE_USER_UPGRADE'=>'activate_user_upgrade',
    'ACTIVE'=>1,
    'ACTIVE_TOKEN'=>1,
    'ACTIVITY_CONVERSATION'=>1,
    'ADDRESS'=>['PRIMARY'=>1, 'SECONDARY'=>2],
    'ADD_FUNDS'=>1,
    'ADMIN_ACCOUNT_ID'=>1,
    'ACCOUNT_STATUS'=>['PENDING'=>0, 'ACTIVE'=>1],
	'APPROVAL_STATUS'=>['PENDING'=>0, 'PARTNER_HOLDING'=>1, 'PARTNER_CONFIRMED'=>2, 'PARTNER_CANCELLED'=>3, 'ADMIN_HOLDING'=>4, 'ADMIN_CONFIRMED'=>5, 'ADMIN_CANCELLED'=>6, 'SUPPLIER_HOLDING'=>7, 'SUPPLIER_CONFIRMED'=>8, 'SUPPLIER_CANCELLED'=>9],
    'APP_SETTINGS'=>'app_settings/',
    'BANNER'=>3,
    'BILLING'=>1,
    'BLOCK'=>1,
    'BLOCKED'=>1,
    'BLOCKED_SUBSCRIPTION'=>3,
    'BLOCK_SUBSCRIPTION'=>2,
    'BLOCK_TYPE'=>['BROWSE'=>1, 'PRODUCT'=>2, 'PAGE'=>3],
    'BOTTOM_ADS'=>4,
    'BROWSE_PRODUCTS_PAGE_LENGTH'=>20,
    'BROWSE_REVIEWS_PAGE_LENGTH'=>10,
    'CHARGE_PERCENTAGE'=>0,
    'CHARGE_VALUE'=>1,
    'COINS'=>2,
    'COMMISSION_TYPE'=>['FIXED'=>1, 'FLEXIBLE'=>2],
    'COMMISSION_UNIT'=>['PERCENTAGE'=>1, 'FIXED_RATE'=>2],
    'CONFIG_REFERRAL'=>'config_referral',
    'CONFIRMED'=>1,
    'COURIER_MODES'=>['STANDARD'=>1, 'EXPRESS DELIVER'=>2, 'DEFAULT'=>1],
    'CURRENT_PLAN_ACTIVE'=>1,
    'CURRENT_PLAN_INACTIVE'=>0,
    'CUSTOMER_ISSUES'=>['MODE_EMAIL'=>1, 'MODE_CALL_BACK'=>2],
    'CUSTOM_VALUES'=>1,
    'DATA_TABLE_RECORDS'=>10,
    'DB_DATE_FORMAT'=>'Y-m-d',
    'DB_DATE_TIME_FORMAT'=>'Y-m-d H:i:s',
    'DEACTIVATED'=>2,
    'DEACTIVATE_SUBSCRIPTION'=>2,
    'DECLINED_CONFIRMED'=>7,
    'DEFAULT'=>['LOGISTIC_ID'=>1, 'MODE_ID'=>1, 'CURRENCY_ID'=>1, 'SIZE_UNIT_ID'=>2, 'WEIGHT_UNIT_ID'=>6],
    'DELETED'=>1,
    'DISCOUNT_STATUS'=>['PUBLISHED'=>1],
    'DISCOUNT_VALUE_TYPE'=>['FIXED_AMOUNT'=>1, 'PERCENTAGE'=>2],
    'DISPUTE_TIMEOUT'=>12,
    'DRAFT'=>0,
    'ENTRY_ON'=>1,
    'EVEN'=>0,
    'EVERYONE'=>3,
    'EXCEEDED_USER_LIMIT'=>2,
    'EXPIRED'=>3,
    'EXPIRED_SUBSCRIPTION'=>4,
    'FEE_UNIT'=>['PERCENTAGE'=>1, 'FIXED_RATE'=>2],
    'FILTER_TYPE'=>['CHECKBOX'=>1, 'RANGE'=>2, 'COLOR'=>3],
    'FLAT'=>1,
    'FOOTER_MENU'=>2,
    'FUND_TRANSFER'=>0,
    'IMAGE'=>1,
    'IMAGE_ADS'=>2,
    'IMAGE_ADS_RIGHT_SIZE'=>['width'=>200, 'height'=>200],
    'INACTIVE'=>0,
    'INITIAL'=>0,
    'INR'=>2,
    'IN_GRACE_PERIOD'=>0,
    'LENGTH'=>25,
    'LEVEL_UPGRADE_SYSTEM_FEES'=>28,
    'LIMIT'=>0,
    'LOGO'=>2,
    'MAIN_ORDER'=>1,
    'MYR'=>4,
    'NEXT_LEVEL_UPGRADE'=>'next_level_upgrade',
    'NON_ACCOUNTABLE'=>2,
    'ODD'=>1,
    'OFF'=>0,
    'ON'=>1,
    'ORDER_STATUS'=>['PLACED'=>0, 'CONFIRMED'=>1, 'APPROVED'=>1, 'PACKED'=>2, 'DISPATCHED'=>3, 'COURIERED'=>4, 'IN_SHIPPING'=>4, 'REACHED_HUB'=>5, 'DELIVERED'=>6, 'SERVICE_IN_PROGRESS'=>7, 'SERVICE_COMPLETED'=>8, 'CANCELLED'=>9, 'RETURN_REFUND'=>10, 'REFUND_APPROVED'=>11, 'REFUND_REJECTED'=>12, 'REFUND_PICKED'=>13, 'REFUND_DISPATCHED'=>14, 'REFUNDED'=>15, 'RETURN_REPLACE'=>16, 'REPLACE_APPROVED'=>17, 'REPLACE_REJECTED'=>18, 'REPLACE_PICKED'=>19, 'REPLACE_DISPATCHED'=>20, 'REPLACED'=>21, 'CANCEL_RETURN_DISPATCHED'=>22, 'COMPLETED'=>23],
    'ORDER_STATUS_CANCELABLE_STATUS'=>[0, 1, 2],
    'PACKAGE_COMPLETED'=>2,
    'PACKAGE_NEW'=>1,
    'PACKAGE_PURCHASE_STATUS'=>['CONFIRMED'=>1, 'CANCELLED'=>3, 'EXPIRED'=>2, 'PENDING'=>0, 'WAIT_FOR_ACTIVATE'=>4,'USER_APPROVALS'=>5],
	'PACKAGE'=> ['DEFAULT_CURRENCY'=>1],
    'PAID_USER'=>1,
    'PASSUP_NO'=>0,
    'PASSUP_YES'=>1,
    'PAYMENT_MODE'=>['CREDIT_CARD'=>1, 'NET_BANKING'=>2, 'EMI'=>3, 'DEBIT_CARD'=>4, 'COD'=>5, 'GIFT_CARD'=>6,'WALLET'=>7,'CASH'=>8],
	'BONUS_WALLET'=>['vi-s'=>2, 'vi-b'=>3],	
    'PAYMENT_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2, 'REFUNDED'=>3, 'PROCESSING'=>4],
    'PAYMENT_TYPES'=>['PAYUMONEY'=>22, 'CASHFREE'=>25, 'BANK'=>12, 'COD'=>18, 'WALLET'=>9, 'CASH'=>21,'LOCAL_MONEY'=>19,'VI_MONEY'=>10],
	'PAYMODE'=>['VCASHBACK'=>3, 'SHOP_AND_EARN'=>3, 'PAY'=>1, 'REDEEM'=>2],	
	'CASHBACK_WALLET'=>1,
    'PENDING'=>0,
    'PERCENTAGE'=>0,
    'POSITION'=>1,
    'POST_PAGES'=>1,
    'POST_TYPE'=>['BRAND'=>1, 'CATEGORY'=>2, 'PRODUCT'=>3, 'PRODUCT_CMB'=>4, 'SUPPLIER'=>5, 'SUPPLIERPRODUCT'=>7, 'SUPPLIER_CATEGORY'=>8, 'SUPPLIER_BRAND'=>9],
    'POST_WIDGET'=>2,
    'PRE_DEFINED_VALUES'=>0,
    'PRIMARY_ADDRESS'=>1,
    'PRIMARY_EMAIL'=>1,
    'PRIMARY_MENU'=>1,
    'PROPERTY_TYPE'=>['PREDEFINED'=>1, 'CUSTOM'=>2],
    'PUBLISH'=>1,
    'PUBLISHED'=>1,
    'PURCHASED'=>1,
    'REJECTED'=>2,
    'RIGHT_ADS'=>2,
    'SECONDARY_ADDRESS'=>2,
    'SECONDARY_EMAIL'=>2,
    'SECONDARY_MENU'=>3,
    'SHIPPING'=>2,
    'SHOPPING_WALLET'=>2,
    'SLIDER_TYPE'=>['FEATURED'=>1, 'IMG'=>2],
    'SOCIAL_MENU'=>4,
    'SPECIFIED_USER'=>1,
    'SUBSCRIBE_CODE_PREFIX'=>'P',
    'SUBSCRIPTION'=>1,
    'SUB_ORDER'=>2,
    'SUPPLIER_PRODUCT_CODE_PREFIX'=>'SP',
    'SUPPLIER_SETTLEMENT_AUTOCREDIT'=>'supplier_settlement_autocredit',
    'TAX_VALUE_TYPE'=>['PERCENTAGE'=>1, 'VALUE'=>2],
    'TEST_MODE_OFF'=>0,
    'TEST_MODE_ON'=>1,
    'TEXT_ADS'=>1,
    'TICKET_CLOSED'=>4,
    'TOPUP'=>2,
    'TOP_ADS'=>3,
    'TRANSACTION_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2],
    'TRANSACTION'=>[0=>'PENDING', 1=>'CONFIRMED', 2=>'CANCELLED'],
    'TRANSACTION_TYPE'=>['DEBIT'=>2, 'CREDIT'=>1],
    'UNBLOCK'=>0,
    'BLOCKED'=>1,
	'UNBLOCKED'=>0,
    'UNBLOCK_SUBSCRIPTION'=>1,
    'UNIT'=>['CM'=>2, 'KG'=>6],
    'UNPUBLISH'=>2,
    'UNPUBLISHED'=>0,
    'UNVERIFIED'=>0,
    'USD'=>1,
    'USER_CODE'=>'EP',
    'VALUE_TYPE'=>['NUMERIC'=>1, 'TEXT'=>2, 'COLOR'=>3],
    'VERIFIED'=>1,
    'VIEW_DATE_FORMAT'=>'d-M-Y',
    'VIEW_DATE_TIME_FORMAT'=>'d-M-Y H:i:s',
    'VPI_RECORDS_COUNT'=>10,
    'WALLET'=>['PERSONAL'=>1, 'SELLS'=>2, 'COMMISSION'=>3],
	'WALLET_PURPOSE'=>[		
		'WITHDRAWAL' => 'withdrawal_status',
		'FUNDTRANSFER' => 'fundtransfer_status',
		'FR_FUNDTRANSFER' => 'fr_fund_transfer_status',
		'INT_FUNDTRANSFER' => 'internaltransfer_status',
		'PURCHASE' =>'purchase_status',
	],
	'WALLETS'=>['VIM'=>1, 'VIS'=>2, 'VIB'=>3, 'VIH'=>4, 'VP'=>5,'VI'=>6],
	'REDEEM_PAYMENT_TYPES'=>['cash'=>1, 'vi-m'=>2],
	//'WITHDRAWAL_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'PROCESSING'=>2, 'CANCEL'=>3, 'CANCELLED'=>3, 0=>'PENDING', 1=>'CONFIRMED', 2=>'PROCESSING', 3=>'CANCELLED'],
    'path'=>'assets/uploads/product_imgs/',
	
	'PACKAGE_PURCHASE_STATUS_PENDING' => 0,
	'PACKAGE_PURCHASE_STATUS_CONFIRMED' => 1,
	'PACKAGE_PURCHASE_STATUS_EXPIRED' =>2,
	'PACKAGE_PURCHASE_STATUS_CANCELLED' =>3,
	'PACKAGE_PURCHASE_STATUS_WAITFOR_ACTIVATE' =>4,	
	'PACKAGE_PURCHASE_STATUS_WAITFOR_USER_ACTIVATE' =>5,	
		
	/* Payments for Modes */
	'PAYMODE_PURPOSE_BUYPACKAGE' => 'buy_package',
	
	/* transaction for */
    "TRANS_FOR_ADDFUND" => 1,
	"TRANS_FOR_ORDER_PAYMENT" => 2,
	"TRANS_FOR_PURCHASE_PACKAGE" => 5,
	"TRANS_FOR_PURCHASE_COUPON" => 6,
	
	"PAYMENT_UNPAID" => 0, 
	"ORDER_CODE_PREFIX" => 'ORD', 
	"WALLETID" => [
		'VI-MONEY'=>1
	], 
	'SELLER'=>[
		'PROFIT_SHARING'=>[
            'STATUS'=>[
                'PENDING'=>0,
                'ACCEPTED'=>1,
                'REJECTED'=>2,
                'CLOSED'=>3,
            ],
        ],
		'STORE'=>[
            'STATUS'=>['DRAFT'=>0, 'ACTIVE'=>1, 'INACTIVE'=>2,],
            'IS_APPROVED'=>['NOT_APPROVED'=>0, 'APPROVED'=>1, 'REJECTED'=>2]
        ],
		'IS_VERIFIED'=>['PENDING'=>0, 'VERIFIED'=>1, 'REJECTED'=>2],
		'STATUS'=>['INACTIVE'=>2, 'ACTIVE'=>1, 'DRAFT'=>0],
	],
	'WALLET_IDS'=>'1,2,3,4,5',	
	
	'SELLERR'=>[
        'LOGO_PATH'=>['TEMPPATH'=>'resources/uploads/retailers/logo_cache/', 'LOCAL'=>'resources/uploads/merchant/', 'WEB'=>'imgs/merchant/75/75/', 'SM'=>'imgs/merchant/sm/', 'DEFAULT'=>'resources/uploads/merchant/default-logo.png', 'DEFAULT_FILE_NAME'=>'default-logo.png'],
        'KYC'=>['LOCAL'=>'resources/uploads/kyc/merchant', 'WEB'=>'attachments/kyc/'],
        'KYC_ORGINAL'=>['LOCAL'=>'resources/uploads/kyc/merchant/original', 'WEB'=>'attachments/kyc/'],
        'IMG_UPLOAD_SETTINGS'=>'merchant_upload_file_settings',
        'IMG_GALLERY_PATH'=>['LOCAL'=>'resources/uploads/merchant/gallery/', 'WEB'=>'imgs/merchant-gallery/750/500/', 'SM'=>'imgs/merchant-gallery/sm', 'LG'=>'imgs/merchant-gallery/lg', 'DEFAULT'=>'resources/uploads/merchant/default-logo.png'],
        'CASBACK_PERCENT'=>10,
        'TRIAL_EXPIRY_DAYS'=>730,
        'PROFIT_SHARING'=>[
            'STATUS'=>[
                'PENDING'=>0,
                'ACCEPTED'=>1,
                'REJECTED'=>2,
                'CLOSED'=>3,
            ]
        ],        
        'DEAL'=>[
            'IS_APPROVED'=>['NOT_APPROVED'=>0, 'APPROVED'=>1, 'REJECTED'=>2],
            'IS_PREMIUM'=>['NO'=>0, 'YES'=>1]
        ],
		'COUPONS'=>[
            'IS_APPROVED'=>['NOT_APPROVED'=>0, 'APPROVED'=>1],
            'STATUS'=>['DRAFT'=>0, 'PUBLISHED'=>1, 'UNPUBLISHED'=>2]
        ],
        'CASHBACK'=>[
            'IS_APPROVED'=>['NOT_APPROVED'=>0, 'APPROVED'=>1],
            'STATUS'=>['DRAFT'=>0, 'PUBLISHED'=>1, 'UNPUBLISHED'=>2]
        ],
    ],
	'OFF_FLAG'=>0,
	'ON_FLAG'=>1,
	'SPECIFY_WRK_HRS'=>['NOT_SPECIFY'=>1, 'GLOBAL'=>2, 'SELF'=>3],
	'DISTANCE_UNIT'=>[1=>'KM', 2=>'MILES', 'DEFAULT'=>1],
	'CATEGORY_TYPE'=>['IN_STORE'=>1, 'ONLINE_STORE'=>3],
	'CBOFFER_TYPE'=>['COUNPON'=>0, 'DISCOUNT'=>1],
	'CASHBACK_VALUE_TYPE'=>['PERCENTAGE'=>0, 'FIXED'=>1, 'POINTS'=>2],
	'COUNTRY_FLAG_PATH'=> 'resources/assets/imgs/flags/',
   'STORE_LOGO_PATH'=>['LOCAL'=>'resources/uploads/merchant/stores/', 'WEB'=>'imgs/stores/200/100/', 'SM'=>'imgs/stores/sm/', 'DEFAULT'=>'imgs/stores/75/75/store.png', 'WEB_100X27'=>'imgs/stores/100/27/', 'DEFAULT_FILE_NAME'=>'store.png'],
   'MERCHANT'=>[
        'LOGO_PATH'=>['TEMPPATH'=>'resources/uploads/retailers/logo_cache/', 'LOCAL'=>'resources/uploads/merchant/', 'WEB'=>'imgs/merchant/75/75/', 'SM'=>'imgs/merchant/sm/', 'DEFAULT'=>'resources/uploads/merchant/default-logo.png', 'DEFAULT_FILE_NAME'=>'default-logo.png'],
        'KYC'=>['LOCAL'=>'resources/uploads/kyc/merchant', 'WEB'=>'attachments/kyc/'],
        'KYC_ORGINAL'=>['LOCAL'=>'resources/uploads/kyc/merchant/original', 'WEB'=>'attachments/kyc/'],
        'IMG_UPLOAD_SETTINGS'=>'merchant_upload_file_settings',
        'IMG_GALLERY_PATH'=>['LOCAL'=>'resources/uploads/merchant/gallery/', 'WEB'=>'imgs/merchant-gallery/750/500/', 'SM'=>'imgs/merchant-gallery/sm', 'LG'=>'imgs/merchant-gallery/lg', 'DEFAULT'=>'resources/uploads/merchant/default-logo.png'],
		'STORE'=>[
            'STATUS'=>['DRAFT'=>0, 'ACTIVE'=>1, 'INACTIVE'=>2,],
            'IS_APPROVED'=>['NOT_APPROVED'=>0, 'APPROVED'=>1, 'REJECTED'=>2]
        ],
		'STATUS'=>['INACTIVE'=>2, 'ACTIVE'=>1, 'DRAFT'=>0],
        'IS_VERIFIED'=>['PENDING'=>0, 'VERIFIED'=>1, 'REJECTED'=>2],
	],
	'CASHBACK_OFFER_STATUS'=>['DRAFT'=>0, 'ACTIVE'=>1, 'INACTIVE'=>2],
	'CASHBACK_OFFER_APPROVAL'=>['PENDING'=>0, 'APPROVED'=>1, 'NOT_APPROVED'=>2],

	'TRANSACTION_CHARGE'=>0,
	'WALLET_NEW'=>['VI-SP'=>2],
	
    'PAY_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2, 'FAILED'=>3],
	'PAYMENT_MODES'=>['cash'=>1, 'vi-money'=>2, 'credit-card'=>3, 'debit-card'=>4, 'netbanking'=>5],
	'PAYMENT_GATEWAY_RESPONSE'=>[
        'PURPOSE'=>['ADD-MONEY'=>3,'PACKAGE-PURCHASE'=>4],		
        'PAYMENT_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2, 'FAILED'=>3, 'REFUND'=>4],
        'STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2, 'FAILED'=>3, 'REFUND'=>4],
    ],	
	'CASHBACK_CREDIT_WALLET'=>1,
	'ORDER'=>[
		'PAYMENT_STATUS'=>['PENDING'=>0, 'PAID'=>1, 'FAILED'=>2, 'CANCELLED'=>3, 'PARTIALLY_PAID'=>4],
		'TYPE'=>[
            'IN_STORE'=>1,
            'DEAL'=>2,
            'COUPONS'=>3,
            'ONLINE'=>4,
            1=>'IN_STORE',
            2=>'DEAL',
            3=>'COUPONS',
            4=>'ONLINE'
        ],
		'COMMISSION'=>[
            'STATUS'=>[
                'PENDING'=>0,
                'PAID'=>1
            ],
        ],
		'STATUS'=>[
            'PENDING'=>0,
            'PAID'=>1,
            'PARTIALY_PAID'=>2,
            'CANCELLED'=>3,
            'FAILED'=>4,
            'USED'=>5,
        ],
		'PAID_THROUGH' => [
            'PAY' => 1,
            'REDEEM' => 2,
            'SHOP_AND_EARN' => 3,
            1 => 'PAY',
            2 => 'REDEEM',
            3 => 'SHOP_AND_EARN'
        ],
        'PAY_THROUGH'=>[
            1=>[
                1=>'vi-m',
                2=>'redeem',
                3=>'cashback'
            ],
            2=>[
                0=>'deal-purchase'
            ],
            3=>[
                1=>'coupon-purchase'
            ]
        ],
        'order_type'=>[
            1=>[
                1=>2,
                2=>2,
                3=>3
            ],
            2=>[
                0=>1
            ],
            3=>[
            ]
        ]
    ],
	'CASHBACK_ON'=>[
        'CASH'=>'cashback_on_pay',
        'PAY'=>'cashback_on_pay',
        'REDEEM'=>'cashback_on_redeem',
        'REDEEM_PG'=>'cashback_on_redeem',
        'SHOP_AND_EARN'=>'cashback_on_shop_and_earn',
        1=>'cashback_on_pay',
        2=>'cashback_on_redeem',
        3=>'cashback_on_shop_and_earn',
    ],
	'CASHBACK_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2, 'FAILED'=>3],
	'CASHBACK_INSTANT_CREDIT'=>'cashback_instant_credit',

	/* franchisee constants */
	'FRANCHISEE_BONUS_FIXED'=>7,
	'FRANCHISEE_BONUS_FLEXIBLE'=>8,
	'FRANCHISEE_BONUS_LAPSED'=>9,
	'FRANCHISEE_BONUS_DIRECT'=>10,
	'FRANCHISEE_WALLET'=>1,
	'FRANCHISEE_COMMISSION_ADD_FUNDS'=>2,
    'FRANCHISEE_COMMISSION_ADD_FUNDS_FRMSC'=>5,
    'FRANCHISEE_COMMISSION_ADMIN_FUND_TRANS_SC'=>6,
    'FRANCHISEE_COMMISSION_FIXED_CONTRIBUTION'=>3,
    'FRANCHISEE_COMMISSION_FLEXIBLE_CONTRIBUTION'=>4,
	
    'FR_COMMISSION_TYPE'=>['FRFTU'=>1, 'ADD_FUNDS'=>2, 'FIXED_CONTRIBUTION'=>3, 'FLEXIBLE_CONTRIBUTION'=>4,'ADD_FUNDS_FRMSC'=>5,'ADMIN_FUND_TRANS_SC'=>6,'PURCHASE_PACKAGE_COMMISSION'=>7],
	 
	'FRANCHISEE_COMMISSION_TYPE'=>['FRFTU'=>1,'FRFTFR'=>2,'PK'=>3,'SEF'=>4,'MCMF'=>5,'ADFT'=>6,'PSF'=>7,'STEF'=>8,'MRCF'=>9,'PS'=>10,1=>'FRFTU',2=>'FRFTFR',3=>'PK',4=>'SEF',5=>'MCMF',6=>'ADFT',7=>'PSF',8=>'STEF',9=>'MRCF'],
	'FRANCHISEE_COMMISSION_STATUS'=>[
        	'PENDING'=>0,
            'CONFIRMED'=>1,
			'WAITING'=>2,
			'CANCELLED'=>3,
	],
	'COMISSION_STATUS_CANCELLED'=>4,
    'COMISSION_STATUS_CONFIRMED'=>1,
    'COMISSION_STATUS_PENDING'=>2,
    'COMISSION_STATUS_WAITING'=>3,

	'REDEEM_STATUS'=>['PENDING'=>0, 'CONFIRMED'=>1, 'CANCELLED'=>2, 'FAILED'=>3],
	'CASHBACK_TYPE'=>['PERCENTAGE'=>0, 'FIXED'=>1, 'POINTS'=>2],
	
	'SEX'=>[1=>'MALE', 2=>'FEMALE', 3=>'OTHER'],

	/* kyc prrof types */
	'VERIFY_DOC_PHOTO_PROOF'=>'0', 
	'VERIFY_DOC_ID_PROOF'=>1,//
	'VERIFY_DOC_ADDRESS_PROOF'=>2, //
	'VERIFY_DOC_TAX_ID_PROOF'=>3, //
	'VERIFY_DOC_NATIONAL_ID_PROOF'=>4,
	
	/* file management paths */
	'ACCOUNT_VERIFICATION_VIEWPATH'=>'doc/kyc/',
	'ACCOUNT_VERIFICATION_MIN_VIEWPATH'=>'doc/kyc/min/',
	'ACCOUNT_VERIFICATION_SRC_VIEWPATH'=>'doc/kyc/src/',
	'ACCOUNT_VERIFICATION_MIN_UPLOADPATH'=>['LOCAL'=>'resources/uploads/kyc/min/', 'WEB'=>'imgs/kyc/min/'],
	'ACCOUNT_VERIFICATION_SRC_UPLOADPATH'=>['LOCAL'=>'resources/uploads/kyc/src/', 'WEB'=>'imgs/kyc/src/'],
	'KYC_DOCUMENT_TYPE'=>['PAN'=>1,'CHEQUE'=>2,'INCC'=>3,'TAX'=>4,'ADDRESS_PROOF'=>5,'ID_PROOF'=>6],
	'ACCOUNT_VERIFICATION_STATUS'=>['PENDING'=>0,'VERIFIED'=>1,'REJECTED'=>2],
	'ACCOUNT_VERIFICATION_STATUS_CLASS'=>[0=>'warning', 1=>'success', 2=>'danger'],	
	'RANK'=>[
        'AFFILIATE'=>'1',
        'PROMOTER'=>'2',
        'DIRECTOR'=>'3',
        'AREA DIRECTOR'=>'4',
        'REGIONAL_DIRECTOR'=>'5',
        'NATIONAL_DIRECTOR'=>'6',
        'EXECUTIVE DIRECTOR'=>'7',
        'PRESIDENTIAL DIRECTOR'=>'8',
    ],
	'BONUS_STATUS' => [
		'PENDING'=>'0',
        'RELEASED'=>'1',
        'WAITING'=>'5',
        'LAPSED'=>'4'
	],
	'PACKAGE_IMG' => 'resources/uploads/packages/',
    'CUSTOMER_BONUS_TYPE'=>['PERSONAL'=>1,'AMBSSADOR'=>2],		
	'QV_CURRENCY_RATE' => 'qv_currency_rate',	
	'UPDATES_ANOUNCEMENT_VIEWPATH'=>['LOCAL'=>'resources/uploads/documets/anouncements/', 'WEB'=>'imgs/documets/anouncements/'],
	'UPDATES_DOWNLOWD_VIEWPATH'=>['LOCAL'=>'resources/uploads/documets/downloads/', 'WEB'=>'imgs/documets/downloads/'],
	'TAX_TYPE'=>['SALES'=>1,'SERCVICES'=>2,'INCOME'=>3],
	/* Account Payout Settings*/
	'ACCOUNT_PAYOUT_SETTINGS_STATUS'=>[
	  1=>'VERIFIED',
	  0=>'NOT_VERIFIED',
	  'VERIFIED'=>0,
	  'NOT_VERIFIED'=>1,
	],	
	'FRANCHISEE'=>[
     'LOGO'=> [
			'PATH'=>'resources/uploads/franchisee/logo/', 
			'LOCAL'=>'resources/uploads/franchisee/logo/', 
			'DEFAULT'=>'blank.png'
		]
	],
  'TAX_TYPES' => ['CGST' => 1, 'SGST ' => 2, 'IGST' => 3, 'TDS' => 4],
  'SELLER_ROUTE'=>'https://www.paygyft.com/merchant/verify-email-link/',
  
];