<?php

return [
    'signup_mobile_verify_msg'=>'One Time Password (OTP) has been sent to your mobile :mobile.',
	'resent_verif_code'=>'OTP has been re-sent successfully.',
	'sent_verif_code'=>'Verification code has been sent to your mobile :mobile',
	'signup_mobile_verification'=>':code is the OTP for verifying your mobile with Virob.',	
	'something_went_wrong'=>'Oops! Something went wrong. Please try again.',
	'not_accessable'=>'Not accessible right now',
	'forgotpin_session_expire'=>'The security PIN reset link was invalid / expired, possibly because it has already been used. Please request a new security PIN reset',
    'forgotpin_account_invalid'=>'Your account is already logout, Please login to request a new security PIN reset',
    'verifyemail_account_invalid'=>'Your account is already logout, Please login to request a verify email',
	'redeem'=>[
		'otp_msg'=>':code is the OTP for Redemption.',
		'invalid_otp'=>'Invalid OTP, Please try again',
		'success_msg'=>'Successfully you have redeemed at',
		'store_share'=>[
			'email'=>[
				'title'=>':company_name',            
				'content'=>'Hey, I thought you would like this store. Enjoy ultimate shopping experience. https://play.google.com/store/apps/details?id=com.ejugiter.virob.app',        
			],
		],
	],
	'cashback'=>[
		'otp_msg'=>':code is the OTP for Redemption.',
		'invalid_otp'=>'Invalid OTP, Please try again',
		'success_msg'=>'Successfully you have redeemed at',
		'store_share'=>[
			'email'=>[
				'title'=>':company_name',            
				'content'=>'Hey, I thought you would like this store. Enjoy ultimate shopping experience. https://play.google.com/store/apps/details?id=com.ejugiter.virob.app',        
			],
		],
	],
	'pay'=>[
		'store_share'=>[
			'email'=>[
				'title'=>':mrbusiness_name',
				'content'=>'I\'m happy with the service of :mrbusiness_name, you can make experience their service.',
				//'content'=>'Hey, I thought you would like this store. Enjoy ultimate shopping experience. https://play.google.com/store/apps/details?id=com.ejugiter.xpayback.app'
				//'content'=>'Hey, I thought you would like this store. Enjoy ultimate shopping experience. :share_link'
			],
		],
	],	
	'code_sent_to_merchant_mobile'=>'Verification code has been sent to your merchant.',	
	'reset_pin'=>[
		'security_pin_updated_successfully'=>'You\'ve successfully reset your Security PIN.',
		'something_wrong'=>'Something Went Wrong',
		'security_should_not_be_same'=>'Old and New Security PIN should not be same',
		'invalid_otp'=>'Invalid OTP',
		'not_accessable'=>'Not accessible right now',
		'success'=>'You have successfully reset your password',
    ],
   'edit_profile'=>[
	  'profile_updated'=>'Profile successfully updated',
	  'no_changes'=>'There are no changes',
	  'not_accessable'=>'Not accessible right now',
	],	
	'changepwd'=>[
        'commerrr'=>'Invalid request',
        'success'=>'Your password has been changed successfully',
        'curpwd_inc'=>'Current password is not matching',
        'newpwd_same'=>'New Password should not be same as Current Password',
        'paramiss'=>'Parameter missing',
        'newpwd_success'=>'Your password has been updated!',
        'savepwd_unable'=>'Password could not able to update. Please try again later.',
        'paramiss'=>'Parameter missing',
        'curr_pwd_same'=>'Current Password is not matching',
        'curr_pwd_incorrect'=>'Current Password is incorrect, please try again',
    ],
	'forgotpwd'=>[
        'comm_error'=>'Enter valid Email ID',
        'email_inv'=>'Invalid Email ID',
        'mobile_inv'=>'Invalid Mobile',
        'acc_notfound'=>'Account not found',        
		'acc_resetlink'=>'An email has been sent to your rescue email address :email.Follow the directions in the email to reset your password',
        'acc_blocked'=>'Your Login Is Blocked. Please Contact Our Support Team.!',
        'acc_mobcode_send'=>'Verification code has been send to your mobile..!',
        'headermiss'=>'Unauthorized Access',
        'parammiss'=>'Parameter missing',
        'reset_code_miss'=>'Verification code missing',
        'reset_sess_exp'=>'Reset session expired',
        'reset_code_inv'=>'Invalid verification code',
        'reset_code_nomatch'=>'Invalid OTP',
        'reset_pwd_success'=>'You have successfully reset your password',
        'reset_pwd_fail'=>'Password Updation Failed. Please try again.',
        'reset_pwd_unable'=>"We are unable to reset your new password. Please try again later.",
        'new_pwd_is_same_as_old'=>'New and Old passwords are same',
        'newpwd_missing'=>"New password missing",
        'usrsess_miss'=>"Account session expired",
        'invalid_otp'=>"Invalid OTP",
    ],
	'security_pin_created_successfully'=>'Vi-Security PIN successfully created.',
	'invalid_security'=>'Invalid Security PIN',
	'invalid_propin'=>'Invalid Security PIN',
    'generate_security'=>'Create your Security PIN first',

	'change_pwd'=>[
	  'profile_updated'=>'Profile successfully updated',
	  'no_changes'=>'There are no changes',
	  'not_accessable'=>'Not accessible right now',
      'currenct_profile_pin'=>'Current Security PIN',
      'new_profile_pin'=>'New Security PIN',
      'invalid_id'=>'Invalid ID',
	],
	'save_pin'=>[
	  'security_pin_created_successfully'=>'Your Security PIN updated Successfully.',
	  'not_accessable'=>'Not accessible right now',
	  'invalid_security'=>'Invalid OTP',
	  'generate_security'=>'Create your Security PIN first',
	],
	'change_pin'=>[
	    'profile_pin_updated_successfully'=>'You\'ve successfully reset your Security PIN.',
        'profile_pin_created_successfully'=>'You\'ve successfully updated your Security PIN.',
	    'invalid_current_pin'=>'Your old Security Pin was entered incorrectly.Please enter It again.',
	],
	'forgot_pin'=>[
	       'verification_code'=>'Verification code has been sent to your Email ID.',
	],

	'offer_upto'=>'Upto :offerval%',
	'store_added_to_favourite'=>'Store Added To Favourite',
	'store_exist_in_favourite'=>'Store exist in your favourite',
	'thanks_to_like'=>'Thank you',
    'thanks_to_unlike'=>'Thank you',
    'store_not_liked'=>'Store not liked',
	'supplier_id'=>'Merchant ID',
	'mrbusiness_name'=>'Merchant Name',
	'location'=>'Location',
	//'cannot_pay_this_amount'=>'Bill amount must be greater than :min_amount and lesser then :max_amount',
	'cannot_pay_this_amount'=>'Something\'s not right. Try re-launching the app, or try this again',
	'bill_amount'=>'Bill Amount',
	'set_location'=>'Set Location',
];
