<?php

return [
    1=>[
        'admin'=>[
            'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Cashback Return',
            'details_remarks'=>'Cashback Return to :wallet',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Cashback Return',
            'details_remarks'=>'Cashback Return to :wallet',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    2=>[
        'admin'=>[
            'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Cashback Received',
            'details_remarks'=>'Cashback Received',
            'payment_details'=>[
                'payment_status'=>'Payment Details',
                'cash'=>'Paid at Outlet',
                'xpc'=>'Paid from Vi-Money',
                'cbp'=>'Paid from E-Shop Point',
                'bp'=>'Paid from Vi-Bonus',
                'netbanking'=>'Netbanking - :payment_type',
                'credit-card'=>'Credit Card - :payment_type',
                'debit-card'=>'Debit Card - :payment_type',
                'transaction_id'=>'Transaction ID',
            ],
            'fields'=>[
                'mrcode'=>'Merchant ID',
                'staff_id'=>'Staff ID',
                'order_code'=>'Order ID',
                'transaction_id'=>'Transaction ID',
                'amount'=>'Cashback Amount',
                'bill_amt'=>'Bill Amount',
                'store_code'=>'Outlet ID',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            //'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Cashback Received',
            'order_remarks'=>'Paid for order #:order_code',
            'remarks'=>'For order #:order_code',
            'order_statement_line'=>'Payment to :store_name',
            'details_remarks'=>'Cashback Received',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'payment_status'=>'Payment Details',                
                'cash'=>'Paid at Outlet',
                //'xpc'=>'Paid from Vi-Money',
                //'cbp'=>'Paid from Cashback Points',
                //'bp'=>'Paid from Bonus Points',
				'VIS'=>'Paid from Vi-Shop Point',
				'VIB'=>'Paid from Vi-Bonus',
				'VIM'=>'Paid from Vi-Money',
                'netbanking'=>'Netbanking - :payment_type',
                'credit-card'=>'Credit Card - :payment_type',
                'debit-card'=>'Debit Card - :payment_type',
                'transaction_id'=>'Transaction ID',
            ],
            'user_properties'=>[
                'status'=>['class'=>'status_class'],
                'payment_status'=>['class'=>'status_class'],
            ],
            'order'=>[
                'remark'=>'Payment of :bill_amount received by :store_name!',
                'fields'=>[
                    'order_code'=>'Order Number',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    //'bill_amount'=>'Payment Details',
                    'bill_amount'=>'Bill Amount',
                    'received_amount'=>'Cash Collected By Outlet',
                ],
            ],
        ],
    ],
    3=>[
        'user'=>[
            //'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'remarks'=>'Paid for order #:order_code',
            //'statement_line'=>'Redeemed Cashback',
            'statement_line'=>'Payment to :store_name',
            'details_remarks'=>'Payment to :store_name',            
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'user_properties'=>[
                'status'=>['class'=>'status_class'],
                'payment_status'=>['class'=>'payment_status_class'],
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
				'VIS'=>'Paid from Vi-Shop Point',
				'VIB'=>'Paid from Vi-Bonus',
				'VIM'=>'Paid from Vi-Money',
                //'cbp'=>'Paid from Cashback Point',
                //'xpc'=>'Paid from Vi-Money',
                'received_amt'=>'Paid at Outlet',
                'transaction_id'=>'Transaction ID',
                'payment_status'=>'Payment Status',
            ],
            'order'=>[
                //'remark'=>'Deal Redeemed by :store_name',
                'remark'=>'Payment of :bill_amount received by :store_name!', 
                'statement_line'=>'Refund for withdrawal request cancelled',
                'fields'=>[
                    'order_code'=>'Order Number',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'bill_amount'=>'Bill Amount',
					'VIS'=>'Paid from Vi-Shop Point',
					'VIB'=>'Paid from Vi-Bonus',
					'VIM'=>'Paid from Vi-Money',                    
                    'received_amt'=>'Paid at Outlet',
                    //'transaction_id'=>'Transaction ID',
                ]
            ]
        ],
        'admin'=>[
            'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Redeemed Cashback',
            'details_remarks'=>'Payment to :store_name',
            'fields'=>[
                'mrcode'=>'Merchant ID',
                'staff_id'=>'Staff ID',
                'transaction_id'=>'Transaction ID',
                'bill_amount'=>'Bill Amount',
                'redeem_amount'=>'Redeemed Amount',
                'amount_due'=>'Amount Due',
                'payment_type'=>'Payment Mode',
                'store_code'=>'Outlet ID'
            ],
        ],
    ],
    4=>[
        'user'=>[
            'remarks'=>':store_name order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Redeemed Cashback',
            'details_remarks'=>'Redeemed Cashback from :wallet',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'admin'=>[
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    5=>[
        'user'=>[
            'remarks'=>':from_amount debited from your account to convert into :to_amount with the rate of :rate',
            'fields'=>[
                'remark'=>'Narration',
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
            ],
            'user_properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'admin'=>[
            'remarks'=>':from_amount debited from your account to convert into :to_amount with the rate of :rate',
            'fields'=>[
                'remark'=>'Narration',
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'created_on'=>'Date',
                'status'=>'Status',
            ]
        ]
    ],
    6=>[
        'admin'=>[
            'remarks'=>':to_amount credited by converting :from_amount with the rate of :rate',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>':to_amount credited by converting :from_amount with the rate of :rate',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    //7=>['remarks'=>'Withdraw to bank account - :status',
    7=>[
        'admin'=>[
            'remarks'=>'Withdraw to bank account :amount',
            'statement_line'=>'Bank Account',
            'details_remarks'=>'You withdrew :amount to Bank Account ',
            'transaction_type'=>'Withdraw to bank account :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'retailer'=>[
            'remarks'=>'Withdraw to bank account - :status',
            'statement_line'=>'Bank Account',
            'details_remarks'=>'You withdrew :amount to Bank Account',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],
        'user'=>[
            'remarks'=>'Withdraw to bank account - :status',
            'statement_line'=>'Bank Account',
            'details_remarks'=>'You withdrew :amount to Bank Account',
            'transaction_type'=>'Withdraw to bank account - :status',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ]
    ],
    8=>[
        'admin'=>[
            'remarks'=>'For withdrawal of :amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'For withdrawal of :amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    9=>[
        'admin'=>[
            'remarks'=>'For the withdrawal request',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'For the withdrawal request',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
        ]
    ],
    10=>[
        'admin'=>[
            'remarks'=>'Chareges for the withdrawal request',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Chareges for the withdrawal request',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    11=>[
        'admin'=>[
            'remarks'=>'Withdrawal refunded to :full_name',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Withdrawal refunded to :full_name',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    12=>[
        'admin'=>[
            'remarks'=>'Refunded',
            'statement_line'=>'Refund for withdrawal request cancelled',
            'details_remarks'=>'Refund for withdrawal request cancelled',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Refunded',
            'statement_line'=>'Refund for withdrawal request cancelled',
            'details_remarks'=>'Refund for withdrawal request cancelled',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    13=>[
        'user'=>[
            'remarks'=>'Signup Bonus to :full_name',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status',
            ]
        ],
        'admin'=>[
            'remarks'=>'Signup Bonus to :full_name',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    14=>[
        'admin'=>[
            'remarks'=>':amount',
            'statement_line'=>'Sign Up Bonus',
            'details_remarks'=>'Sign Up Bonus :amount - :status',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>':amount',
            'statement_line'=>'Sign Up Bonus',
            'details_remarks'=>'Sign Up Bonus :amount - :status',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
            ],
            'payment_details'=>[
                'amount'=>'Signup Bonus',
                'paidamt'=>'Net Pay',
                'transaction_id'=>'Transaction ID',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ]
    ],
    15=>[
        'user'=>[
            'remarks'=>'Referral Bonus to :full_name',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'admin'=>[
            'remarks'=>'Referral Bonus to :full_name',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    16=>[
        'admin'=>[
            'remarks'=>':status',
            'statement_line'=>'Referral Sign Up Bonus',
            'details_remarks'=>'Referral Bonus :amount - :status',
            'referral_from'=>':full_name has used your code',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'from_user_name'=>'From Account Username',
                'from_name'=>'From Account Name',
                'to_account_name'=>'To Account Username',
                'to_name'=>'To Account Name',
                'offer_name'=>'Offer',
                'commission_perc'=>'Commission Percentage',
                'amount'=>'Commission Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>':status',
            'statement_line'=>'Referral Sign Up Bonus',
            'details_remarks'=>'Referral Bonus :amount - :status',
            'referral_from'=>':full_name has used your code',
            'fields'=>[
                'from_user_name'=>'From Account Username',
                'from_name'=>'From Account Name',
                'to_account_name'=>'To Account Username',
                'to_name'=>'To Account Name',
                'offer_name'=>'Offer',
                'commission_perc'=>'Commission Percentage',
                'amount'=>'Commission Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Signup Bonus',
                'paidamt'=>'Net Pay',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
    19=>[
        'admin'=>[
            'remarks'=>'Fund Debited by Virob',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Fund Debited by Virob',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    20=>[
        'admin'=>[
            'remarks'=>'Fund received from Virob',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],      
		'franchisee'=>[
           'remarks'=>'Fund received from Virob',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
           'remarks'=>'Fund received from Virob',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
    ],
    21=>[
        'admin'=>[
            'remarks'=>'Payment refund of #:order_code',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'order_code'=>'Order ID',
                'bill_amount'=>'Bill Amount',
                'amount'=>'Refund Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Payment refund of #:order_code',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'order_code'=>'Order ID',
                'bill_amount'=>'Bill Amount',
                'amount'=>'Refund Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    22=>[
        'admin'=>[
            'remarks'=>'Order Payment Refunded for #:order_code',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Order Payment Refunded for #:order_code',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    23=>[
        'admin'=>[
            'remarks'=>'Order commission Tax deduction',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Order commission Tax deduction',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'retialer'=>[
            'remarks'=>'Order commission Tax deduction',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    24=>[
        'admin'=>[
            'remarks'=>'Paid to the Tax :amount has been Credited from (:store_name) for the order (:order_code)',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Paid to the Tax :amount has been Credited from (:store_name) for the order (:order_code)',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Paid to the Tax :amount has been Credited from (:store_name) for the order (:order_code)',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    25=>[
        'admin'=>[
            'remarks'=>'Commission Paid :amount :full_name order #:order_code for Bill Amt :bill_amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Commission Amount',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Commission Paid :amount :full_name order #:order_code for Bill Amt :bill_amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Commission Amount',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Commission Paid :amount :full_name order #:order_code for Bill Amt :bill_amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Commission Amount',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    26=>[
        'admin'=>[
            'remarks'=>'Commission Received :amount :store_name order #:order_code for Bill Amt :bill_amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Commission Received :amount :store_name order #:order_code for Bill Amt :bill_amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Commission Received :amount :store_name order #:order_code for Bill Amt :bill_amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    27=>[
        'admin'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment to :store_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_type'=>'',
                'card'=>'',
                'bill_amount'=>'',
                'payment_status'=>'',
                'wallet_name'=>'',
                'transaction_id'=>'Transaction ID',
            ],
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment to :store_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'fields'=>[
                'amount'=>'Amount',
                'created_on'=>'Date',
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_type'=>'',
                'card'=>'',
                'bill_amount'=>'',
                'payment_status'=>'',
                'wallet_name'=>'',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'seller'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment to :store_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Bill Amount',
                'paid_at_outlet'=>'Paid at Outlet',
                'handleamt'=>'Fees',
                'tax'=>'Tax (GST)',
                'net_pay'=>'Net Pay',
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_type'=>'',
                'card'=>'',
                'bill_amount'=>'',
                'payment_status'=>'',
                'wallet_name'=>'',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
    28=>[
        'admin'=>[
            'remarks'=>'Return from order :order_code',
            'statement_line'=>'Deal Cancelled - :store_name',
            'details_remarks'=>':deal_name',
            'fields'=>[
                'amount'=>'Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Deal Price',
                'handleamt'=>'Cancellation Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to Vi-Money',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Return from order :order_code',
            'statement_line'=>'Deal Cancelled - :store_name',
            'details_remarks'=>':deal_name',
            'fields'=>[
                'amount'=>'Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Deal Price',
                'handleamt'=>'Cancellation Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to Vi-Money',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
    80=>[
        'admin'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'full_name'=>'Customer',
                'transaction_id'=>'Transaction ID',
                'amount'=>'Bill Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'payment_status'=>'Payment Status',
                'payment_type'=>'Payment Type',
                'store_code'=>'Outlet ID',
                'staff_id'=>'Staff ID',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'bill_amount'=>'Amount',
                'store_received_amt'=>'Cash collected by :store_name!',
            ],
            'redeem_payment_details'=>[
                'bill_amount'=>'',
            ],
            'pay_through'=>[
                'order'=>[
                    0=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    1=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    2=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                        'cbp'=>'Paid from Cashback Point',
                        'xpc'=>'Paid from Vi-Money',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    3=>[
                        'bill_amount'=>'Bill Amount',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ]
                ],
                'transaction'=>[
                    'seller_pay_through'=>[
                        2=>[
                            'bill_amount'=>'Bill Amount',
                            'handleamt'=>'Fees ',
                            'store_received_amt'=>'Paid at Outlet',
                            'paidamt'=>'Net Pay',
                        ]
                    ]
                ]
            ]
        ],
        'user'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'full_name'=>'Customer',
                'mobile'=>'Mobile',
                'email'=>'Email',
                'user_code'=>'Member ID',
                'transaction_id'=>'Transaction ID',
                'amount'=>'Bill Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'payment_status'=>'Payment Status',
                'payment_type'=>'Payment Type',
                'store_code'=>'Outlet ID',
                'staff_id'=>'Staff ID',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'bill_amount'=>'Amount',
                'store_received_amt'=>'Cash collected by :store_name!',
            ],
            'redeem_payment_details'=>[
                'bill_amount'=>'',
            ],
            'pay_through'=>[
                'order'=>[
                    0=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    1=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    2=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                        'cbp'=>'Paid from Cashback Point',
                        'xpc'=>'Paid from Vi-Money',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    3=>[
                        'bill_amount'=>'Bill Amount',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ]
                ],
                'transaction'=>[
                    'seller_pay_through'=>[
                        2=>[
                            'bill_amount'=>'Bill Amount',
                            'handleamt'=>'Fees ',
                            'store_received_amt'=>'Paid at Outlet',
                            'paidamt'=>'Net Pay',
                        ]
                    ]
                ]
            ]
        ],
        'seller'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'mr_details_remarks'=>'Payment of :amount received by :site_name!',
            'mr_payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Amount',
                'store_received_amt'=>'Cash collected by :store_name!',
            ],
            'redeem_payment_details'=>[
                'bill_amount'=>'',
            ],
            'pay_through'=>[
                'order'=>[
                    0=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    1=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    2=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                        'cbp'=>'Paid from Cashback Point',
                        'xpc'=>'Paid from Vi-Money',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    3=>[
                        'bill_amount'=>'Bill Amount',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ]
                ],
                'transaction'=>[
                    'seller_pay_through'=>[
                        2=>[
                            'bill_amount'=>'Bill Amount',
                            'handleamt'=>'Fees ',
                            'store_received_amt'=>'Paid at Outlet',
                            'paidamt'=>'Net Pay',
                        ]
                    ]
                ]
            ]
        ]
    ],
    29=>[
        'admin'=>[
            'remarks'=>'Deal purchase tax paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Deal purchase tax paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Deal purchase tax paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    30=>[
        'admin'=>[
            'remarks'=>'Deal Purchase tax received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Deal Purchase tax received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Deal Purchase tax received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    31=>[
        'admin'=>[
            'remarks'=>'Deal purchase referral commission Paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Deal purchase referral commission Paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'retialer'=>[
            'remarks'=>'Deal purchase referral commission Paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    32=>[
        'admin'=>[
            'remarks'=>'Deal purchase referral commission received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Deal purchase referral commission received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Deal purchase referral commission received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    33=>[
        'admin'=>[
            'remarks'=>'Deal Purchase Commission Paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Deal Purchase Commission Paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Deal Purchase Commission Paid',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    34=>[
        'admin'=>[
            'remarks'=>'Deal Purchase Payment Received',
            'statement_line'=>'Deal Purchase',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Deal Price',
                'handleamt'=>'Fees',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'transaction_id'=>'Transaction ID',
            ],
        ],
        'user'=>[
            'remarks'=>'Deal Purchase Payment Received',
            'statement_line'=>'Deal Purchase',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Deal Price',
                'handleamt'=>'Fees',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'transaction_id'=>'Transaction ID',
            ],
        ],
        'seller'=>[
            'remarks'=>'Deal Purchase Payment Received',
            'statement_line'=>'Deal Purchase',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Deal Price',
                'handleamt'=>'Fees',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'transaction_id'=>'Transaction ID',
            ],
        ]
    ],
    35=>[
        'admin'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Deal Bought from :store_name',
            'details_remarks'=>'',
            'fields'=>[
                'full_name'=>'Customer Name',
                'uname'=>'Customer Username',
                'transaction_id'=>'Transaction ID',
                'deal_name'=>'Deal Name',
                'voucher_code'=>'Deal Voucher Code',
                'amount'=>'Deal Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'bcategory_name'=>'Deal Category',
                'store_code'=>'Outlet ID',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_type'=>'',
                'card'=>'',
                'bill_amount'=>'',
                'payment_status'=>'',
                'wallet_name'=>'',
                'transaction_id'=>' Transaction ID',
            ],
            'order'=>[
                'remark'=>'',
                'fields'=>[
                    'order_code'=>'',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'pg_no'=>'Payment Details',
                    'payment_type'=>'',
                    'card'=>'',
                    'bill_amount'=>'',
                    'payment_status'=>'',
                    'wallet_name'=>'',
                ],
                'properties'=>[
                    'payment_status'=>['class'=>'status_class']
                ],
            ]
        ],
        'user'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Deal Bought from :store_name',
            'details_remarks'=>'',
            'fields'=>[
                'amount'=>'Bill Amount',
                'order_code'=>'Order Number',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_type'=>'',
                'card'=>'',
                'bill_amount'=>'',
                'payment_status'=>'',
                'wallet_name'=>'',
                'transaction_id'=>' Transaction ID',
            ],
            'order'=>[
                'remark'=>'Deal Bought from :store_name',
                'fields'=>[
                    'order_code'=>'',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'pg_no'=>'Payment Details',
                    'payment_type'=>'Payment Type',
                    'wallet_name'=>'Wallet Name',
                    'card'=>'Card',
                    'bill_amount'=>'Bill Amount',
                    'payment_status'=>'Payment Status',
                ],
                'properties'=>[
                    'payment_status'=>['class'=>'status_class']
                ],
            ]
        ],
        'seller'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Deal Bought from :store_name',
            'details_remarks'=>'',
            'fields'=>[
                'full_name'=>'Customer Name',
                'uname'=>'Customer Username',
                'transaction_id'=>'Transaction ID',
                'deal_name'=>'Deal Name',
                'voucher_code'=>'Deal Voucher Code',
                'amount'=>'Deal Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'bcategory_name'=>'Deal Category',
                'store_code'=>'Outlet ID',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_type'=>'',
                'card'=>'',
                'bill_amount'=>'',
                'payment_status'=>'',
                'wallet_name'=>'',
                'transaction_id'=>' Transaction ID',
            ],
            'order'=>[
                'user'=>[
                    'remark'=>'',
                    'fields'=>[
                        'order_code'=>'',
                        'amount'=>'Amount',
                    ],
                    'payment_details'=>[
                        'pg_no'=>'Payment Details',
                        'payment_type'=>'',
                        'card'=>'',
                        'bill_amount'=>'',
                        'payment_status'=>'',
                        'wallet_name'=>'',
                    ],
                    'properties'=>[
                        'payment_status'=>['class'=>'status_class']
                    ],
                ]
            ]
        ]
    ],
    36=>[
        'admin'=>[
            'remarks'=>'Deal brought by :full_name',
            'fields'=>[
                'full_name'=>'Merchant Account Name',
                'uname'=>'Merchant Account Username',
                'user_fullname'=>'Customer name',
                'user_account_code'=>'Customer Account Code',
                'transaction_id'=>'Transaction ID',
                'deal_name'=>'Deal Name',
                'voucher_code'=>'Deal Voucher Code',
                'amount'=>'Deal Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'bcategory_name'=>'Deal Category',
                'store_code'=>'Outlet ID',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'transaction_id'=>'Transaction ID'
            ],
            'pay_through'=>[
                'order'=>[
                    0=>[
                        []
                    ],
                    1=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    2=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                        'cbp'=>'Paid from Cashback Point',
                        'xpc'=>'Paid from Vi-Money',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    3=>[
                        'bill_amount'=>'Bill Amount',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ]
                ]
            ]
        ],
        'user'=>[
            'remarks'=>'Deal brought by :full_name',
            'fields'=>[
                'full_name'=>'Merchant Account Name',
                'uname'=>'Merchant Account Username',
                'transaction_id'=>'Transaction ID',
                'deal_name'=>'Deal Name',
                'voucher_code'=>'Deal Voucher Code',
                'amount'=>'Deal Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'bcategory_name'=>'Deal Category',
                'store_code'=>'Outlet ID',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'transaction_id'=>'Transaction ID'
            ],
            'pay_through'=>[
                'order'=>[
                    0=>[
                        []
                    ],
                    1=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    2=>[
                        'bill_amount'=>'Bill Amount',
                        'system_received_amt'=>'Cash collected by :site_name',
                        'store_received_amt'=>'Cash collected by :store_name',
                        'cbp'=>'Paid from Cashback Point',
                        'xpc'=>'Paid from Vi-Money',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ],
                    3=>[
                        'bill_amount'=>'Bill Amount',
                        'store_received_amt'=>'Cash collected by :store_name',
                    ]
                ],
            ]
        ]
    ],
    37=>[
        'admin'=>[
            'remarks'=>'Payment to system',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Payment to system',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    38=>[
        'admin'=>[
            'remarks'=>'Funds added through :pay_mode',
            'fields'=>[
                'remark'=>'Narration',
                'transaction_id'=>'Transaction ID',
                'full_name'=>'Customer Name',
                'uname'=>'Customer Username',
                'amount'=>'Amount',
                'payment_type'=>'Payment Type',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'order'=>[
                'remarks'=>'Payment of :bill_amount received by :site_name!',
                'fields'=>[
                    'order_code'=>'Order Number',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'bill_amount'=>'Bill Amount',
                    'payment_type'=>'',
                    'pg_no'=>'',
                    'card'=>'',
                ],
            ]
        ],
        'seller'=>[
            'remarks'=>'Funds added through :pay_mode',
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'fields'=>[
                'remark'=>'Narration',
                'transaction_id'=>'Transaction ID',
                'full_name'=>'Customer Name',
                'uname'=>'Customer Username',
                'amount'=>'Amount',
                'payment_type'=>'Payment Type',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'order'=>[
                'remarks'=>'Payment of :bill_amount received by :site_name!',
                'fields'=>[
                    'order_code'=>'Order Number',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'bill_amount'=>'Bill Amount',
                    'payment_type'=>'',
                    'pg_no'=>'',
                    'card'=>'',
                ]
            ]
        ],
        'user'=>[
            'remarks'=>'Funds added through :pay_mode',
            'fields'=>[
                'remark'=>'Narration',
                'transaction_id'=>'Transaction ID',
                'full_name'=>'Customer Name',
                'uname'=>'Customer Username',
                'amount'=>'Amount',
                'payment_type'=>'Payment Type',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'order'=>[
                'remark'=>'Payment of :bill_amount received by :site_name!',
                'fields'=>[
                    'order_code'=>'Order Number',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'bill_amount'=>'Bill Amount',
                    'payment_type'=>'Payment Type',
                    'pg_no'=>'',
                    'card'=>'Card',
					'wallet_amt'=>'Vi-Money',
					'transaction_id'=>'Transaction No.',
                ],
            ]
        ]
    ],
    39=>[
        'admin'=>[
            'remarks'=>'Refund :amount with the exchange rate of :rate credited by system',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Refund :amount with the exchange rate of :rate credited by system',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
            ]
        ],
        'user'=>[
            'remarks'=>'Refund :amount with the exchange rate of :rate credited by system',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    40=>[
        'admin'=>[
            'remarks'=>'Tip amount debited :amount',
            'statement_line'=>'Tip to order',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'Tip amount debited :amount',
            'statement_line'=>'Tip to order',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    41=>[
        'admin'=>[
            'remarks'=>'Tip received from :full_name to :staff_name',
            'statement_line'=>'Tip received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'full_name'=>'Customer',
                'staff_id'=>'Staff ID',
                'amount'=>'Tip Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'seller'=>[
            'remarks'=>'Tip received from :full_name',
            'statement_line'=>'Tip received',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'full_name'=>'Customer',
                'staff_id'=>'Staff ID',
                'amount'=>'Tip Amount',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    42=>[
        'admin'=>[
            'remarks'=>'TDS Deduction for the :amount ',
            'statement_line'=>'TDS Deduction',
            'details_remarks'=>'TDS Deduction for the :amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>'TDS Deduction for the :amount ',
            'statement_line'=>'TDS Deduction',
            'details_remarks'=>'TDS Deduction for the :amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    43=>[
        'admin'=>[
            'remarks'=>'TDS return for the :amount',
            'statement_line'=>'TDS return',
            'details_remarks'=>'TDS return for the :amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'created_on'=>'Date',
                'status'=>'Status',
                'paidamt'=>'Net Pay',
            ]
        ],
        'user'=>[
            'remarks'=>'TDS return for the :amount',
            'statement_line'=>'TDS return',
            'details_remarks'=>'TDS return for the :amount',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    44=>[
        'admin'=>[
            'remarks'=>'Paid for order #:order_code for Bill Amt :bill_amount',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :store_name!',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'store_received_amt'=>'Paid at Outlet',
                'handleamt'=>'Fees',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay',
            ]
        ],
        'seller'=>[
            //'remarks'=>'Paid for order #:order_code for Bill Amt :bill_amount',            
            'remarks'=>'Paid for order #:order_code',            
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :store_name!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'mobile'=>'Mobile',
                'email'=>'Email',
                'account_code'=>'Member ID',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'store_received_amt'=>'Paid at Outlet',
                'handleamt'=>'Fees',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay',
            ]
        ]
    ],
    45=>[
        'admin'=>[
            'remarks'=>':full_name order #:order_code for Bill Amt. :bill_amount',
            'statement_line'=>'Cash Received',			
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status',
            ]
        ],
        'seller'=>[
            //'remarks'=>':full_name order #:order_code for Bill Amt. :bill_amount',
            //'statement_line'=>'Cash Received',
			'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'email'=>'Email',
                'mobile'=>'Mobile',
                'bill_amount'=>'Amount',
                'account_code'=>'Member ID',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'handleamt'=>'Fees',
                'tax'=>'Tax',
				'balance'=>'Balance',
                'store_received_amt'=>'Pay at Outlet',                
                'paidamt'=>'Net Pay',
            ]
        ]
    ],
    46=>[
        'admin'=>[
            'remarks'=>':status',
            'statement_line'=>'Referral Bonus',
            'details_remarks'=>'Referral Bonus :amount - :status',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'referral_details'=>':full_name has used your code and earned cashback',
                'amount'=>'Cashback Earned',
                'paidamt'=>'Referral Bonus',
                'handleamt'=>'Tax (TDS)',
                'paidamt'=>'Net Pay',
                'transaction_id'=>'Transaction ID',
            ],
        ],
        'user'=>[
            'remarks'=>':status',
            'statement_line'=>'Referral Bonus',
            'details_remarks'=>'Referral Bonus :amount - :status',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Referral Bonus',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
            ],
            'payment_details'=>[
                'referral_details'=>':full_name has used your code and earned cashback',
                'amount'=>'Cashback Earned',
                'paidamt'=>'Referral Bonus',
                'handleamt'=>'Tax (TDS)',
                'paidamt'=>'Net Pay',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
    47=>[
        'admin'=>[
            'remarks'=>':full_name order #:order_code for Bill Amt. :bill_amount',
            'statement_line'=>'Cash Received',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ],
        'user'=>[
            'remarks'=>':full_name order #:order_code for Bill Amt. :bill_amount',
            'statement_line'=>'Cash Received',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
    ],
    50=>[
        'admin'=>[
            'remarks'=>'Return from order :order_code',
            'statement_line'=>'Deal Cancelled - :store_name',
            'details_remarks'=>':deal_name',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Deal Price',
                'handleamt'=>'Cancellation Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to Vi-Money',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Return from order :order_code',
            'statement_line'=>'Deal Cancelled - :store_name',
            'details_remarks'=>':deal_name',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Deal Price',
                'handleamt'=>'Cancellation Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to Vi-Money',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
    51=>[/* Merchant Get cashback Order  */
        'admin'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'store_received_amt'=>'Cash collected by :store_name',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'seller'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :customer',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'store_received_amt'=>'Cash collected by :store_name',
                //'transaction_id'=>'Transaction ID',
            ]
        ]
		
    ],
    52=>[/* Merchant Redeem Order details  */
        'admin'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'system_received_amt'=>'Cash collected by :site_name',
                'store_received_amt'=>'Cash collected by :store_name',
                'VIS'=>'Paid from Vi-Shop Point',
				'VIB'=>'Paid from Vi-Bonus',
				'VIM'=>'Paid from Vi-Money',
                //'transaction_id'=>'Transaction ID',
            ],
        ],
        'seller'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :customer',
            'details_remarks'=>'Payment of :amount received by :site_name!',            
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'system_received_amt'=>'Cash collected by :site_name',
                'store_received_amt'=>'Cash collected by :store_name',                
				'VIS'=>'Paid from Vi-Shop Point',
				'VIB'=>'Paid from Vi-Bonus',
				'VIM'=>'Paid from Vi-Money',
                //'transaction_id'=>'Transaction ID',
            ],
        ]
    ],
    53=>[/* Merchant PAY Order details  */
        'admin'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :full_name',
            'details_remarks'=>'Payment of :amount received by Virob!',
            'details_remarks'=>'Payment of :amount received by Virob!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'system_received_amt'=>'Cash collected by Virob',
                'store_received_amt'=>'Cash collected by :store_name',
                'transaction_id'=>'Transaction ID',
            ],
        ],
        'seller'=>[
            'remarks'=>'Paid for order #:order_code',
            'statement_line'=>'Payment from :customer',
            'details_remarks'=>'Payment of :amount received by Virob!',
            'details_remarks'=>'Payment of :amount received by :site_name!',
            'payment_remarks'=>'Payment of :amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'account_code'=>'Member ID',
                'mobile'=>'Mobile no',
                'email'=>'Email ID',
                'bill_amount'=>'Amount',
                'transaction_id'=>'Transaction ID',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'system_received_amt'=>'Cash collected by :site_name',
                'store_received_amt'=>'Cash collected by :store_name',
                //'transaction_id'=>'Transaction ID',
            ],
        ]
    ],
	61=>[
		'admin'=>[
            'remarks'=>'Fund transfered to :to_account_name',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Fund transfered to :to_account_name',
            'statement_line'=>':status',
            'details_remarks'=>'Fund Transfer',
            'fields'=>[
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
		'affiliate'=>[
            'remarks'=>'Fund transfered to :to_account_name',
            'statement_line'=>':status',
            'details_remarks'=>'Fund Transfer',
            'fields'=>[
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'retailer'=>[
            'remarks'=>':status',
            'statement_line'=>'Fund transfered to :to_account_name',
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
	],
	62=>[
		'admin'=>[
            'remarks'=>'Fund received from :from_account_name',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Fund received from :from_account_name',
            'statement_line'=>':status',
            'details_remarks'=>'Fund Transfer',
            'fields'=>[
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
		'affiliate'=>[
            'remarks'=>'Fund received from :from_account_name',
            'statement_line'=>':status',
            'details_remarks'=>'Fund Transfer',
            'fields'=>[
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'retailer'=>[
            'remarks'=>':status',
            'statement_line'=>'Fund received from :from_account_name',
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ]
        ]
	],
	65=>[/* Package Refund*/
        'admin'=>[
            'remarks'=>'Refund of :package, #:order_code',
            'statement_line'=>'Package Refund',
            'details_remarks'=>':package',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amount'=>'Package Price',
                'handleamt'=>'Refund Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to :wallet',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Refund from :package, #:order_code',
            'statement_line'=>'Package Refund',
            'details_remarks'=>':package',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Package Price',
                'handleamt'=>'Refund Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to :wallet',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
	66=>[/* Merchant PAY Order details  */
        'admin'=>[
            'remarks'=>'Voucher received for package #:code',
            'statement_line'=>'Voucher received for package #:code',
            'details_remarks'=>'Voucher received for package #:code',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Amount',
                'handleamt'=>'Cancellation Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to :wallet',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'statement_line'=>'Voucher Received ',
            'order_remarks'=>'Voucher received for package #:code',
            'remarks'=>'Voucher received for package #:code',
            'order_statement_line'=>'Voucher received for package #:code',
            'details_remarks'=>'Cashback Received',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'payment_status'=>'Payment Details',                				
				'VIM'=>'Voucher received for package #:code',
                'netbanking'=>'Netbanking - :payment_type',
                'credit-card'=>'Credit Card - :payment_type',
                'debit-card'=>'Debit Card - :payment_type',
                'transaction_id'=>'Transaction ID',
            ],            
            'order'=>[
                'remark'=>'Voucher received for package #:code',
                'fields'=>[
                    'purchase_code'=>'Order Number',
                    'amount'=>'Amount',
                ],
                'payment_details'=>[
                    'bill_amount'=>'Bill Amount',
                ],
            ],
        ]
    ],
	67=>[/* Fast Start Bonus  */
        'admin'=>[
            'remarks'=>'Received Fast Start Bonus from :user_code',
            'statement_line'=>'Fast Start Bonus',
            'details_remarks'=>'Received Fast Start Bonus from :user_code for :package',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Package Price',
                'handleamt'=>'Charges',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay ',
                'desc'=>'Received Fast Start Bonus from :user_code',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Received Fast Start Bonus from :user_code',
            'statement_line'=>'Fast Start Bonus',
            'details_remarks'=>':package',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Package Price',
                'handleamt'=>'Charges',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to :wallet',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
	68=>[/* Purchase Package  */
        'admin'=>[
            'remarks'=>'Paid for :package Package (#:code)',
            'statement_line'=>'Payment from :customer',
            'details_remarks'=>':package',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Package Price',
                'handleamt'=>'Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to :wallet',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Paid for :package Package (#:code)',
            'statement_line'=>'Payment for :package',
            'details_remarks'=>':package',
            'fields'=>[
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'amouht'=>'Deal Price',
                'handleamt'=>'Charges',
                'tax'=>'Tax (GST)',
                'paidamt'=>'Net Pay ',
                'desc'=>'Payment Details - Credited to :wallet',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
	69=>[/* Fast Start Bonus  */
        'admin'=>[
            'remarks'=>'Team Commission Received for - :form_date to :to_date',
            'statement_line'=>'Team Commission',
            'details_remarks'=>'Team Commission Received for - :form_date to :to_date',
            'fields'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay ',
                'desc'=>'Team Commission Received for - :from_date to :to_date',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Team Commission Received for - :from_date to :to_date',
            'statement_line'=>'Team Commission',
            'details_remarks'=>'Team Commission Received for - :from_date to :to_date',
            'fields'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'desc'=>'Team Commission Received for - :from_date to :to_date',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
	70=>[/* Fast Start Bonus  */
        'admin'=>[
            'remarks'=>'Leadership Bonus Received for - :form_date to :to_date',
            'statement_line'=>'Leadership Bonus',
            'details_remarks'=>'Leadership Bonus Received for - :form_date to :to_date',
            'fields'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay ',
                'desc'=>'Leadership Bonus Received for - :from_date to :to_date',
                'transaction_id'=>'Transaction ID',
            ]
        ],
        'user'=>[
            'remarks'=>'Leadership Bonus Received for - :from_date to :to_date',
            'statement_line'=>'Leadership Bonus',
            'details_remarks'=>'Leadership Bonus Received for - :from_date to :to_date',
            'fields'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'payment_details'=>[
                'income'=>'Commission',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'desc'=>'Leadership Bonus Received for - :from_date to :to_date',
                'transaction_id'=>'Transaction ID',
            ]
        ]
    ],
    'field_labels'=>[
        1=>['store'=>'Outlet', 'amount'=>'Total Amount', 'tax'=>'Tax', 'handleamt'=>'Charges', 'paidamt'=>'Paid Amount', 'payment_type'=>'Payment Type', 'transaction_id'=>'Tranaction ID', 'created_on'=>'Transaction On']
    ],
    'order_details'=>[
        'remarks'=>'Refunded',
        'statement_line'=>'Refund for withdrawal request cancelled',
        'details_remarks'=>'Payment of :amount received by :store_name !',
    ],
    'user_redeem_order'=>[
        'remarks'=>'Payment of :bill_amount received by :store_name!',
        'statement_line'=>'Refund for withdrawal request cancelled',
        'fields'=>[
            'order_code'=>'Order Number',
            'amount'=>'Amount',
        ],
        'payment_details'=>[
            'bill_amount'=>'Bill Amount',
            'cbp'=>'Paid from Cashback Point',
            'xpc'=>'Paid from Vi-Money',
            'received_amt'=>'Paid at Outlet',
        ]
    ],
	
	71=>[
        'admin'=>[
            'remarks'=>'Withdraw fund to :payout_type_name (Trans.ID: #:transaction_id)',
            'statement_line'=>':payout_type_name',
            'details_remarks'=>'Withdrawal of :amount to :payout_type_name',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Withdraw fund to :payout_type_name (Trans.ID: #:transaction_id)',
            'statement_line'=>':payout_type_name',
            'details_remarks'=>'Withdraw of :amount to :payout_type_name',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],
		'franchisee'=>[
            'remarks'=>'Withdraw fund to :payout_type_name (Trans.ID: #:transaction_id)',
            'statement_line'=>':payout_type_name',
            'details_remarks'=>'Withdraw of :amount to :payout_type_name',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ]
    ],	
	73=>[
        'admin'=>[
            'remarks'=>'Withdrawal cancel refund',
            'statement_line'=>'Bank Account',
            'details_remarks'=>'You withdraw :amount to Bank Account',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Withdrawal cancel refund',
            'statement_line'=>'Bank Account',
            'details_remarks'=>'Refund for cancel withdrawal',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],
		'franchisee'=>[
            'remarks'=>'Withdrawal cancel refund',
            'statement_line'=>'Bank Account',
            'details_remarks'=>'Refund for cancel withdrawal',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID -',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ]
    ],
	/* 75=>[
        'admin'=>[
            'remarks'=>'Wallet Sales Commission received from :from_account - :franchisee_type (:location) to :to_account',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],      
		'franchisee'=>[
            'remarks'=>'Wallet Sales Commission received from :from_account - :franchisee_type (:location) to :to_account',
			'statement_line'=>'SAP Wallet Sale',
            'fields'=>[
                'transaction_id'=>'Transaction ID',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'created_on'=>'Date',
                'status'=>'Status'
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
    ], */
	83=>[
        'admin'=>[
            'remarks'=>'Fund Transfer from :from_account_id - :from_fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Fund Received',
            'details_remarks'=>'Fund Received :amount from :from_account_id - :from_fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Fund Transfer from :from_account_id - :from_fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Fund Received',
            'details_remarks'=>'Fund Received :amount from :from_account_id - :from_fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],		
    ],
	84=>[
        'admin'=>[
            'remarks'=>'Fund Transfer to :to_fr_type - :to_account_id (Trans.ID: #:transaction_id)',
            'statement_line'=>'Fund Received',
            'details_remarks'=>'Fund Received :amount to :to_fr_type - :to_account_id',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Fund Transfer to :to_account_id - :to_fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Fund Received',
            'details_remarks'=>'Fund Received :amount to :to_account_id  :to_fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],		
    ],
	
	
	
	85=>[
        'admin'=>[
            'remarks'=>'Wallet bought from :from_account - :fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet bought',
            'details_remarks'=>'Wallet bought for :amount from :from_account_id - :fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Wallet bought from :from_account_id - :fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet bought',
            'details_remarks'=>'Wallet bought for :amount from :from_account_id - :fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
	86=>[
        'admin'=>[
            'remarks'=>'Wallet sold to :to_account - :fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sold',
            'details_remarks'=>'Wallet sold for :amount to :to_account - :fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Wallet sold to :to_account - :fr_type (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sold',
            'details_remarks'=>'Wallet sold for :amount to :to_account - :fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
	87=>[
        'admin'=>[
            'remarks'=>'Wallet bought from :from_account - :fr_type',
            'statement_line'=>'Wallet bought',
            'details_remarks'=>'Wallet bought for :amount from  :fr_type - :from_account',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'user'=>[
            'remarks'=>'Wallet bought from :from_account (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet bought',
            'details_remarks'=>'Wallet bought for :amount from  :fr_type - :from_account',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
	88=>[
        'admin'=>[
            'remarks'=>'Wallet sale to :to_account (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sold',
            'details_remarks'=>'Wallet sale to :to_account of :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id (Trans.ID: #:transaction_id)',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Wallet sale to :to_account (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sale',
            'details_remarks'=>'Wallet sale to :to_account of :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id (Trans.ID: #:transaction_id)',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],		
    ],
	89=>[
        'admin'=>[
            'remarks'=>'Wallet Sales Commission (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sales Commission',
            'details_remarks'=>'Wallet Sales Commission of :amount from  :from_account - :fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Wallet Sales Commission (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sales Commission',
            'details_remarks'=>'Wallet bought for :amount from :from_account - :fr_type',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
	90=>[
        'admin'=>[
            'remarks'=>'Wallet Sales Commission Canclled of (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sales Commission Cancelled',
            'details_remarks'=>'Wallet Sales Commission Canclled of (Trans.ID: #:transaction_id)',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Wallet Sales Commission Canclled of (Trans.ID: #:transaction_id)',
            'statement_line'=>'Wallet Sale Commission Cancelled',
            'details_remarks'=>'Wallet Sales Commission Canclled of (Trans.ID: #:transaction_id)',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],		
    ],
	91=>[
        'admin'=>[
            'remarks'=>'Wallet Sales Commission of :transfer_amt :currency - from :to_account',
            'statement_line'=>'Wallet Sales Commission',
            'details_remarks'=>'Wallet Sales Commission of :transfer_amt :currency - from  :to_account',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Wallet Sales Commission  of :transfer_amt :currency - from :to_account',
            'statement_line'=>'Wallet Sales Commission',
            'details_remarks'=>'Wallet Sales Commission of :transfer_amt :currency - from :to_account',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
	93=>[
        'admin'=>[
            'remarks'=>'Package Sales Commission from :from_account of :amount (Trans.ID: #:transaction_id)',
            'statement_line'=>'Package Sales Commission',
            'details_remarks'=>'Package Sales Commission from :from_account of :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ]
        ],
        'franchisee'=>[
            'remarks'=>'Package Sales Commission from :from_account of :amount (Trans.ID: #:transaction_id)',
            'statement_line'=>'Package Sales Commission',
            'details_remarks'=>'Package Sales Commission from :from_account of :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
   97=>[
       'admin'=>[
            'remarks'=>'Merchant Enrollment Fee for :period of :amount (Trans.ID: #:transaction_id)',
            'statement_line'=>'Merchant Enrollment Fee',
            'details_remarks'=>'Merchant Enrollment Fee for :form of :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                  'status'=>['class'=>'status_class']
            ],
       ],
       'franchisee'=>[
            'remarks'=>'Merchant Enrollment Fee for :period of :amount (Trans.ID: #:transaction_id)',
            'statement_line'=>'Merchant Enrollment Fee',
            'details_remarks'=>'Merchant Enrollment Fee for :form of :amount',
            'fields'=>[
                'transaction_id'=>'Wallet Transaction ID - :transaction_id',
                'amount'=>'Amount',
                'handleamt'=>'Fee',
                'tax'=>'Tax',
                'paidamt'=>'Net Pay',
                'remark'=>'Remark',
                'created_on'=>'Date',
                'status'=>'Status',
                'transaction_type'=>'Transaction type',
            ],
            'properties'=>[
                'status'=>['class'=>'status_class']
            ],
        ],			
    ],
	
	'franchisee_commission_type'=>[
	         1=>'Wallet Sale to User',
			 2=>'Wallet Sale to Channel Partner',
			 3=>'Package Sale Fee',
			 4=>'Store Enrollment Fee',
			 5=>'Merchant Commission Fee',
			 6=>'Admin Wallet Sale to Channel Partner',
			 7=>'Package Sale Fee',
			 8=>'Store Enrollment Fee',
			 9=>'Merchant Commission Fee',
		     10=>'Profit Sharing',
	],
	
];
