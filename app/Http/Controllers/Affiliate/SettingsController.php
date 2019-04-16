<?php
namespace App\Http\Controllers\Affiliate;

use Illuminate\Support\Facades\Validator;
use App\Library\MailerLib;
use App\Http\Controllers\AffBaseController;
use App\Models\Commonsettings;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Settings;
use App\Helpers\CommonNotifSettings;
use File;
use CommonLib;
use SendSMS;

class SettingsController extends AffBaseController {    

	private $smsObj;
    public $userObj; 
    public $settingsObj; 
    public function __construct ()
    {
        parent::__construct();
		$this->commonstObj = new Commonsettings();
	 	$this->smsObj = new SendSMS;	 
		$this->affObj = new AffModel;	
		$this->settingsObj = new Settings;	
    }	
	
	public function change_email(){
		$data = array();		
		$op['template'] = view('affiliate.settings.change_email',$data)->render();
		$op['status'] = $this->statusCode =$this->config->get('httperr.SUCCESS');
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		
	}
		
	public function security_settings(){
		$data = array();
		$data['email']=$this->userSess->email;
		$data['mobile']=$this->userSess->mobile;
//		$data['phonecode']=$this->userSess->phonecode;
		return view('affiliate.settings.security_settings',$data);
	}
	
	/* Email Update */
	public function sendEmailVerification ()
    {
		if(!empty($this->userSess->has_pin)){
		$new_email = '';
		$op = array();
        
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');  
		 
		$cur_email = $this->userSess->email; 			
		$ses = [
			'account_id'=>$this->account_id,
			'vcode'=>'',
			'hash_code'=>''];			
		$ses['vcode'] = $this->commonstObj->random_strings(8);
		$ses['hash_code'] = md5($ses['vcode']);
		$this->session->set('changeEmailSess', $ses);				
		
		$token = $this->session->getId().'.'.$ses['hash_code'];			
		$data = ['vcode'=>$ses['vcode'],'email_verify_link'=>route('aff.settings.changeemail.verification',['token'=>$token])];
		CommonNotifSettings::affNotify('affiliate.account.settings.change_email_verification', $this->userSess->account_id, 0,$data,true,false);	 
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');
		$op['msg'] = trans('franchisee/settings/change_email.check_email_inbox',['email'=>$this->commonstObj->maskEmail($this->userSess->email)]);
	//	$op['link'] = $data['email_verify_link'];
	    $op['code']=$ses['vcode'];
      }
	  else{
		  $op['status'] = $this->statusCode =  $this->config->get('httperr.UN_PROCESSABLE');
		  $op['msg'] = 'Create your Security PIN before processing this request';
	   }
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
	
	/* Email Update */
	public function sendEmailVerificationOTP ()
    {
		$new_email = '';
		$op = array();
        $postdata = $this->request->all();
        $op['msg'] = trans('affiliate/settings.change_email.email_req_expiry');
        $op['status'] = 422;  
		
		if (!empty($postdata) && $this->session->has('changeEmailSess'))
        { 	  			
			$ses = $this->session->get('changeEmailSess');
			if(strtolower($this->session->get('changeEmailSess')['vcode'])==strtolower($postdata['vcode'])){
				$new_email= $postdata['email'];		
				$cur_email = $this->userSess->email; 
				
				$ses = [
					'new_email'=>$new_email,
					'account_id'=>$this->account_id,
					'code'=>'',
					'hash_code'=>'']; 			
				
				$ses['code'] = rand(100000, 999999);
				$ses['hash_code'] = md5($ses['code']);		
				$this->session->set('changeEmailSess', $ses);		
				
				$token = $this->session->getId().'.'.$ses['hash_code'];			
				$data = array_merge($ses,['email'=>$new_email]);
						
				CommonNotifSettings::affNotify('affiliate.account.settings.change_email_verification_otp', 0, 0, $data,true,false);	
				$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
				$op['msg'] = trans('affiliate/settings/change_email.check_email_for_otp',['email'=>$this->commonstObj->maskEmail($new_email)]);
				//$op['code'] = $ses['code'];		
				//$op['token'] = $token;
			}
			else {
				$op['error'] = ['vcode'=>["Invalide Verification Code"]];
				$op['status'] = $this->statusCode = 400;  
			}
		}
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }	
	
	public function verifylink_change_email($token)
	{
		$data = $sdata = [];  
		$data['verify_new_email'] = false;		
        if(!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);			
            if($this->session->has('changeEmailSess')){ 
                $sdata = $this->session->get('changeEmailSess');				
                $account_id = (isset($this->userSess->account_id) && !empty($this->userSess->account_id)) ? $this->userSess->account_id : '';
				$data['btnMsg'] =($sdata['account_id'] == $account_id) ? 'Click Here to Home' : 'Click Here to Login';
				if($sdata['account_id'] == $account_id && $sdata['hash_code'] == $access_token[1])
				{	
					$data['verify_new_email'] = true;					
				}				
				else
				{
					$data['msg'] = trans('affiliate/account.verifyemail_sess_expire');
				}
            }
			else
			{
				$data['msg'] = trans('franchisee/settings/change_email.verifyemail_sess_expire');
			}
        }
		else
		{
			$data['msg'] = trans('franchisee/settings/change_email.verifyemail_sess_expire');
		}
	    return view('affiliate.settings.change_email_verification',$data);	
	}
	
	public function verify_change_email_otp()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$checkType = 0;
		if($this->session->has('changeEmailSess'))
		{
			$verifySess = $this->session->get('changeEmailSess');			
		
			if (!empty($postdata))
			{
				if(isset($postdata['verify_code']))
				{
					$rules =[
						'verify_code' => 'required|numeric|digits_between:6,6',
					];
					$messages =[
						'verify_code.required' => trans('affiliate/validator/change_email_js.verify_code'),
						'verify_code.numeric' => trans('affiliate/validator/change_email_js.numeric'), 
						'verify_code.digits_between' => trans('affiliate/validator/change_email_js.maxlength'),
					];
					$validator = Validator::make($postdata,$rules,$messages);
					if ($validator->fails())
					{	
						$ers = $validator->errors();
						foreach($rules  as $key=>$formats){
							$op['errs'][$key] =  $validator->errors()->first($key);			
						}
						return $this->response->json($op,500);
					}
					$checkType = 1;
				}				
			}		
			$postdata = $this->request->all();
			if ($checkType == 1 && !empty($verifySess))
			{	
				if ($verifySess['hash_code'] == md5($postdata['verify_code']))
				{	
					$oldEmail = $this->userSess->email;
					$updateRes = $this->settingsObj->update_user_email($verifySess['account_id'], $verifySess['new_email']);
					if ($updateRes){
						$this->session->forget('changeEmailSess');
						$res['email']= $verifySess['new_email'];	
						$res['reset_session']='';					
						$mailData = [
							'email' => $oldEmail,
							'new_email' => $verifySess['new_email'],
						];
						CommonNotifSettings::affNotify('affiliate.account.settings.change_email_notification', 0, 0, $mailData,true,false);	
						
						return $this->response->json(['status'=>200,'msg'=>trans('affiliate/settings/change_email.update_email_success')]);
					}
					else {
						return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_email.email_req_expiry')]);
					}
				}
				else
				{
					return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_email.email_req_expiry')]);
				}
			}	
		}		
    }
	
	public function change_uname(){
		$op = array();		
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$op['status'] = $this->statusCode = 422;
		if(!empty($postdata['new_uname'])){
			$op = $this->settingsObj->update_uname($this->userSess->account_id,$postdata['new_uname']);
			$this->statusCode = $op['status'];
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
	}	
	
	
	public function sendMobileVerification(){
		
      if(!empty($this->userSess->has_pin)){		  
		$new_email = '';
		$op = array();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');  
		 
		$cur_email = $this->userSess->email; 			
		$ses = [
			'account_id'=>$this->account_id,
			'code'=>'',
			'hash_code'=>''];			
		$ses['code'] = rand(100000, 999999);
		$ses['hash_code'] = md5($ses['code']);		
		$this->session->set('changeMobileSess', $ses);				
		$token = $this->session->getId().'.'.$ses['hash_code'];			
		$data = ['email_verify_link'=>route('aff.settings.changemobile.verification',['token'=>$token])];
		CommonNotifSettings::affNotify('affiliate.account.settings.change_mobile_verification', $this->userSess->account_id, 0, $data,true,false);	
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
		$op['msg'] = trans('affiliate/settings/change_email.check_email_inbox',['email'=>$this->commonstObj->maskEmail($this->userSess->email)]);
		//$op['link'] = $data['email_verify_link'];	
	   }		
		else{
		  $op['status'] = $this->statusCode =  $this->config->get('httperr.UN_PROCESSABLE');
		  $op['msg'] = 'Create your Security PIN before processing this request';  
		  }
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
		
	}
	public function verifylink_change_mobile($token)
	{
		$data = $sdata = [];  
		$data['verify_new_mobile'] = false;		
        if(!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);			
            if($this->session->has('changeMobileSess')){ 
                $sdata = $this->session->get('changeMobileSess');				
                $account_id = (isset($this->userSess->account_id) && !empty($this->userSess->account_id)) ? $this->userSess->account_id : '';
				$data['btnMsg'] =($sdata['account_id'] == $account_id) ? 'Click Here to Home' : 'Click Here to Login';
				if($sdata['account_id'] == $account_id && $sdata['hash_code'] == $access_token[1])
				{	
					$data['verify_new_mobile'] = true;					
				}				
				else
				{
					$data['msg'] = trans('affiliate/account.verifyemail_sess_expire');
				}
             }
         }  
	  return view('affiliate.settings.change_mobile_verification',$data);	
	}
	
	/* Mobile Number Update */
    public function sendMobileVerificationOTP ()
    {   
		$new_mobile = '';
		$op = array();
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		
		if (!empty($postdata))
        {			
			$rules =  [            
				'mobile' => 'required|numeric|unique:'.$this->config->get('tables.ACCOUNT_MST').',mobile',
			];
			$messages = [
			  'mobile.required' => trans('affiliate/validator/change_mobile_js.required'), 
			  'mobile.numeric' => trans('affiliate/validator/change_mobile_js.invalid_mobile'), 
			  'mobile.digits_between' => trans('affiliate/validator/change_mobile_js.mobile_max'),
			  'mobile.unique' => trans('affiliate/validator/change_mobile_js.unique'),
			];
		    $validator = Validator::make($postdata, $rules,$messages);
			if ($validator->fails())
			{	
				$ers = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['errs'][$key] =  $validator->errors()->first($key);			
				}
				return $this->response->json($op,500);
			}
			$new_mobile = $postdata['mobile'];			
			$cur_mobile = $this->userSess->mobile;
			$ses = [
				'new_mobile'=>$new_mobile,
				'account_id'=>$this->account_id,
				'code'=>'',
				'hash_code'=>'']; 			
			
			$ses['code'] = rand(100000, 999999);
			$ses['hash_code'] = md5($ses['code']);		
			$this->session->set('changeMobileSess', $ses);				
			$token = $this->session->getId().'.'.$ses['hash_code'];		
	 	    			//CommonLib::notify(null, 'mobile_verifycode', ['code'=>$ses['code']], [ 'mobile'=>$new_mobile]);	
			CommonNotifSettings::affNotify('affiliate.account.settings.change_mobile_verification_otp', 0, 0,['code'=>$ses['code'],'mobile'=>$new_mobile], true,false);			
			$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
			//$op['code'] = $ses['code'];	
			$op['msg'] = trans('affiliate/settings/change_mobile.check_mobile_inbox',['mobile'=>maskMobile($new_mobile)]);
			$op['token'] = $token;
	       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
         }

    }
	public function verify_change_mobile_otp()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$checkType = 0;
		if($this->session->has('changeMobileSess'))
		{
			$verifySess = $this->session->get('changeMobileSess');			
		
			if (!empty($postdata))
			{
				if(isset($postdata['verify_code']))
				{
					$rules =[
						'verify_code' => 'required|numeric|digits_between:6,6',
					];
					$messages =[
						'verify_code.required' => trans('affiliate/validator/change_email_js.verify_code'),
						'verify_code.numeric' => trans('affiliate/validator/change_email_js.numeric'), 
						'verify_code.digits_between' => trans('affiliate/validator/change_email_js.maxlength'),
					];
					$validator = Validator::make($postdata,$rules,$messages);
					if ($validator->fails())
					{	
						$ers = $validator->errors();
						foreach($rules  as $key=>$formats){
							$op['errs'][$key] =  $validator->errors()->first($key);			
						}
						return $this->response->json($op,500);
					}
					$checkType = 1;
				}
				
			}		
			$postdata = $this->request->all();
			if ($checkType == 1 && !empty($verifySess))
			{	
				if ($verifySess['hash_code'] == md5($postdata['verify_code']))
				{	
					$updateRes = $this->settingsObj->update_user_mobile($verifySess['account_id'], $verifySess['new_mobile']);
					if ($updateRes){   
				
						$this->session->forget('changeMobileSess');
						$res['mobile']= $verifySess['new_mobile'];	
						$res['reset_session']='';		
	                   $mdata = array(
							'full_name'=>$this->userSess->full_name,
							'uname'=>$this->userSess->uname,
							'mobile'=>$verifySess['new_mobile'],				
							'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
							'client_ip'=>$this->request->ip(true));
							$email_data = array('mobile'=>$verifySess['new_mobile']);
						CommonNotifSettings::affNotify('affiliate.account.settings.change_mobile_notify', $this->userSess->account_id, 0, $mdata,true,false);
						return $this->response->json(['status'=>200,'msg'=>trans('affiliate/settings/change_mobile.update_mobile_success')]);
					}
					else {
						return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_mobile.mobile_req_expiry')]);
					}
				}
				else
				{
					return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_mobile.mobile_req_expiry')]);
				}
			}	
		}		
    }
	public function update_mobile()
    {		
		$op = $postdata=array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$checkType = 0;
		
		if($this->session->has('userdata'))
		{
			$res = $this->session->get('userdata');
			if(isset($res->reset_session)){
				$sess_key = $res->reset_session;
				$verifySess = $this->session->get($sess_key);
				if (!empty($postdata))
				{	
					if(isset($postdata['verify_code']))
					{ 
						$rules =[
							'verify_code' => 'required|numeric|digits_between:6,6',
						];
						$messages =[
							'verify_code.required' => trans('affiliate/validator/change_mobile_js.verify_code'), 
							'verify_code.numeric' => trans('affiliate/validator/change_mobile_js.numeric'), 
							'verify_code.digits_between' => trans('affiliate/validator/change_mobile_js.maxlength'), 
						];
						$validator = Validator::make($postdata,$rules,$messages);
						if ($validator->fails())
						{	
							$ers = $validator->errors();
							foreach($rules  as $key=>$formats){
								$op['errs'][$key] =  $validator->errors()->first($key);			
							}
							return $this->response->json($op,500);
						}
						$checkType = 1;
					}
				}	
			}
			$postdata = $this->request->all();;
			$postdata['email']=$this->userSess->email;
			
			if ($checkType == 1)
			{ 	 
				if ($verifySess['verify_code'] == $postdata['verify_code'])
				{	
					$updateRes = $this->settingsObj->update_user_mobile($verifySess['account_id'], $verifySess['new_mobile']);
					
					if ($updateRes)
					{  
					   // $data = array('siteConfig'=>$this->siteConfig);
					  /*   $htmls = View('emails.affiliate.account.settings.update_mobile')->render();
						$mstatus = TWMailer::send(array(
						'to'=> $postdata['email'],
						'subject'=>trans('affiliate/settings/change_mobile.mobile_notify'),
						 'view'=> $htmls,
						 'from'=>$this->pagesettings->noreplay_emailid,
						 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
					   );  */
						$mdata = array(
							'full_name'=>$this->userSess->full_name,
							'uname'=>$this->userSess->uname,
							'mobile'=>$verifySess['new_mobile'],				
							'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
							'client_ip'=>$this->request->ip(true));
							$email_data = array('mobile'=>$verifySess['new_mobile']);
						CommonNotifSettings::affNotify('affiliate.account.settings.change_mobile_notify', $this->userSess->account_id, 0, $mdata,true,false);
					
						$res->mobile=$verifySess['new_mobile']; 	 
						$res->reset_session ='';	
						$this->session->set('userdata',$res);
						return $this->response->json(['status'=>200,'msg'=>trans('affiliate/settings/change_mobile.update_mobile_success')]);
					}                
				}
				else
				{
					return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_mobile.mobile_req_expiry')]);
				}
			}				
		}  
		return App::abort('404');		
    } 
	
	public function profile_info() {
		$op = $data = [];
		$data['user_code'] = $this->userSess->user_code;
		$data['full_name'] = $this->userSess->full_name;
		$data['gardian'] = $this->userSess->gardian;
		$data['marital'] = $this->userSess->marital_status;		
		$data['marital_status'] = $this->settingsObj->get_marital_status();		
		$data['sfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.update_profile');
		$op['template'] = view('affiliate.settings.profile_update',$data)->render();
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}
	
	/* Profile Marital Status Update */
	public function update_profile() {
		$op = $data = $postdata =[];
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] = 'Something went wrong!';
		$postdata = $this->request->all();
		$postdata['account_id'] = $this->userSess->account_id;
		if(!empty($postdata)){			
			$res = $this->settingsObj->updateProfile($postdata);
			if($res){
				$this->userSess->gardian = trim($postdata['gardian']);
				$this->userSess->marital_status = $postdata['marital_status'];
				$this->session->set($this->sessionName, $this->userSess);				
				$op['marital_status'] = trans('affiliate/general.marital_status.'.$postdata['marital_status']);
				$op['gardian'] = $postdata['gardian'];
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Profile details are updated successfully';
			}else{
			    $op['msg'] = 'Profile details are already updated';
				$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}
	 
	public function change_pwd(){
		$data = array();
		return view('affiliate.settings.change_pwd',$data);
	}
	
	/* Profile Marital Status Update */
	public function update_contacts() {
		$op = $data = $postdata =[];
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] = 'Something went wrong!';
		$postdata = $this->request->all();
		$postdata['account_id'] = $this->userSess->account_id;
		if(!empty($postdata)){			
			$res = $this->settingsObj->updateProfile($postdata);
			if($res){								
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Contacts are updated successfully';
			}else{
			    $op['msg'] = 'Contacts are already updated';
				$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}

	public function securitypin(){
		$data = array();		
		return view('affiliate.settings.change_securitypin',$data);
	}
	
    public function updatepwd()
    {
        $data['status'] = $data['msg'] ='';    
        $postdata = $this->request->all();		
	    if (!empty($postdata))
		{			
	        $data = $this->affObj->update_password($this->userSess->account_id, $postdata);	
			if(!empty($data['status']) == $this->config->get('httperr.SUCCESS')){
			
			    $this->userSess->pass_key = md5($this->request->newpassword);
                $this->session->set($this->sessionName, $this->userSess);
				$mdata = array(
					'full_name'=>$this->userSess->full_name,
					'uname'=>$this->userSess->uname,				
					'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
					'client_ip'=>$this->request->ip(true));
					$email_data = array('email'=>$this->userSess->email);
				CommonNotifSettings::affNotify('affiliate.account.settings.change_password_resetnotify', $this->userSess->account_id, 0, $mdata,true,false);				
			}	          	
        }
		return $data;
	}

    public function password_check()
    {
		$op = [];
	    $data = $postdata = [];
        $postdata = $this->request->all();
		if (!empty($postdata))
		{			
			 $oldpassword = $postdata['old_user_pwd'];
			 $status = $this->affObj->password_check($oldpassword, $this->userSess->account_id);	
	  switch($status){
			case 1:
				$op['msg'] = '';
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			break;
			case 2:
				$op['error'] = ['old_user_pwd'=>trans('affiliate/settings/changepwd.incorrect_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
			default:
				$op['error'] = ['old_user_pwd'=>trans('affiliate/settings/changepwd.old_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
        }	
		
	}
	
	public function securitypin_verify ()
    {
		$op = [];
		$postdata = $this->request->all();
        $oldpassword = $postdata['oldpassword'];
        $status = $this->affObj->tran_password_check($oldpassword, $this->userSess->account_id);
		switch($status){
			case 1:
				$op['msg'] = '';
				/* trans('affiliate/settings/security_pwd.crct_trans_pwd'); */
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			break;
			case 2:
				$op['error'] = ['oldpassword'=>trans('affiliate/settings/security_pwd.incrct_trans_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
			default:
				$op['error'] = ['oldpassword'=>trans('affiliate/settings/security_pwd.empty_trans_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
    }
	
    public function securitypin_reset()
    {         
		$op['msg'] = trans('general.something_wrong');
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['alertclass'] = 'alert-warning';
	    $postdata = $this->request->all();	
		if (!empty($postdata))
		{			
			$op = $this->affObj->tran_update_password($this->userSess->account_id, $postdata);			
			if($op['status'] == $this->config->get('httperr.SUCCESS'))
			{
			    $this->userSess->has_pin = true;
                $this->session->set($this->sessionName, $this->userSess);
				$data = [
					'full_name'=>$this->userSess->full_name,
					'uname'=>$this->userSess->uname,				
					'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
					'site_name' => $this->pagesettings->site_name];					
				CommonNotifSettings::affNotify('affiliate.account.settings.securitypwd_resetnotify', $this->userSess->account_id, 0, $data,true,false);				
				$op['msg'] = $op['msg'];
				$op['alertclass'] = $op['alertclass'];
				$op['status'] = $this->statusCode = $op['status'];
			}else {
			    $op['msg'] = $op['msg'];
				$op['alertclass'] = $op['alertclass'];
				$op['status'] = $this->statusCode = $op['status'];
			}
        }
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
	}
	
	public function forgot_security_pwd ()
    {
  
		$data['email'] 		= $this->userSess->email;
		$data['uname'] 		= $this->userSess->uname;
		$data['full_name']  = $this->userSess->full_name;
		$data['code'] 		= rand(100000, 999999);
		$data['site_name']  = $this->pagesettings->site_name;
		$data['account_id'] = $this->userSess->account_id;
		$this->session->set('resetProfilePin', $data);
		$activation_key 	= md5($this->userSess->account_id.date('ymshis'));
		$saveRes 			= $this->affObj->update_account_activationkey($this->userSess->account_id,['activation_key'=>$activation_key]);
		if($saveRes){
			/*  $data['userinfo'] = $this->userSess; */
 			CommonNotifSettings::affNotify('affiliate.account.settings.reset_security_pwd', $this->userSess->account_id, 0, $data,true,false);			
			//$op['code'] = $data['code'];			
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('affiliate/settings/security_pwd.forgot_pin_otp',['email'=>maskEmail($data['email'])]);		
		}else {
		    $op['msg'] = 'Something Went wrong!';	
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');			
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
		
    public function reset_security_pwd($post_val){		
		return view('affiliate.settings.update_security_pwd');	
	}	

	public function securitypin_save()
	{   
		$data = $sdata = [];
        if ($this->session->has('resetProfilePin'))
        {
			$sdata = $this->session->get('resetProfilePin');		
			$data['security_pin'] = $this->request->tran_newpassword;  //tran_confirmpassword
			$check_profilepin=$this->affObj->profilepin_check($this->userSess->account_id);	
			
			if ($check_profilepin->trans_pass_key != md5($data['security_pin']))
			{
				$data['account_id'] = $this->userSess->account_id;
				if ($this->affObj->saveProfilePIN($data))
				{ 			       	
			        $this->userSess->has_pin = true;
                    $this->session->set($this->sessionName, $this->userSess);
					$this->session->forget('resetProfilePin');	
					$mdata = [
						'full_name'=>$this->userSess->full_name,
						'uname'=>$this->userSess->uname,				
						'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
						'site_name' => $this->pagesettings->site_name];					
					//$htmls = view('emails.affiliate.account.settings.create_securitypin', $mdata)->render(); 
				    CommonNotifSettings::affNotify('affiliate.account.settings.create_securitypin', $this->userSess->account_id, 0, $mdata,true,false);					
					$op['msg'] = trans('affiliate/settings/security_pwd_js.profile_pin_save_success');
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				}
				else
				{ 
					$op['msg'] = trans('general.something_wrong');
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				}  
			}
			else
			{		     
				$op['msg'] = trans('affiliate/settings/security_pwd_js.sameas_current_profile_pin');
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');				
			} 
        }
        else
        {			
            $op['msg'] = trans('affiliate/account.not_accessable');
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		 
	}
	
	public function securitypin_forgototp_check(){
		$data = $sdata = [];
        if ($this->session->has('resetProfilePin'))
        {
            $sdata = $this->session->get('resetProfilePin');
            if ($sdata['code'] == $this->request->otp) {
			    $op['msg']='ok';
				$op['status_class'] ='alert-success';
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else{
				$op['status_class']='alert-danger';
                $op['msg'] = trans('affiliate/account.invalid_otp');
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else{
		    $op['status_class']='alert-danger';
            $op['msg'] = trans('affiliate/account.not_accessable');
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');  
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	 
	/* Bank details */		
	public function Get_Ifsc_Bank_Details ()
	{	
		$op = [];
		$postdata = $this->request->all();     		
		$postdata['account_id'] = $this->userSess->account_id;    
		$postdata['country_id'] = $this->userSess->country_id;
		$res = $this->commonstObj->Get_Ifsc_Bank_Details($postdata);		
		if(!empty($res) && $res->valid){
			$op['data'] = $res;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}else {
		    $op['msg'] = 'Please provide a valid IFSC code!';
		    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');	
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		
	}
	 
	public function Bank_Details ()
    {		   
		$data = $op = [];		
		$postdata = $this->request->all(); 		
		$postdata['payment_setings']['beneficiary_name'] = trim($postdata['payment_setings']['beneficiary_name']);
		$postdata['account_id'] = $this->userSess->account_id;    
		$postdata['currency_id'] = $this->userSess->currency_id; 
		$postdata['account_type'] = $this->userSess->account_type_id; 		
		$res = $this->settingsObj->UpdateBankDetails($postdata);					
		if ($res) 
		{		       
			/* $this->settingsObj->UpdateCompletedSteps(['current_step'=>$this->config->get('constants.ACCOUNT_CREATION_STEPS.BANK_DETAILS'),
												'account_type_id'=>$this->config->get('constants.ACCOUNT_TYPE.SELLER'),
												'supplier_id'=>$this->supplier_id,
												'account_id'=>$this->account_id]); */ 														
			$op['msg'] = 'Bank Account details are updated successfully.';							
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['postdata']=$postdata;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		
    }
	
	/* Kyc Upload Document */
	public function kycDocumentUpload()
	{	
	    $op = $kycDoc = $upload = [];
		$uploaded_file = $document_type = '';      
        $postdata = $this->request->all();	
   
		if(!empty($postdata)){
			foreach ($postdata as $docCode=>$v){		
				if ($this->request->hasFile($docCode))
				{   
					$attachment = $this->request->file($docCode);
					$size = $this->request->file($docCode)->getSize();			
					if ($size < 2049133)
					{ 
				        $filename = '';
						$document_type = $this->config->get('constants.KYC_DOCUMENT_TYPE.'.strtoupper($docCode));
						if ($attachment != '')
						{      
							if (in_array(strtolower($attachment->getClientOriginalExtension()), array('gif','jpg','jpeg','png','pdf')))
							{   				       
								$org_name = $attachment->getClientOriginalName();
								$ext = $attachment->getClientOriginalExtension();
								$file_extentions = strtolower($ext);
								$filtered_name = \AppService::slug($org_name);					
								$file_name = explode('_', $filtered_name);
								$file_name = $file_name[0];
								$file_name = $file_name.'.'.$ext;
								$folder_path = getGTZ(null, 'Y').'/';  
								$move_path = $this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.LOCAL');	
															
								if(!File::exists($move_path.getGTZ(null, 'Y'))){
									File::makeDirectory($move_path.getGTZ(null, 'Y'),777,true);	
								}
								$filename = date('dmYHis').$this->userSess->account_id.'_'.$file_name;												
								
								$uploaded_file =$this->request->file($docCode)->move($move_path.$folder_path, $filename);
								$postdata['file_upload1'] = $filename;								
							}
							else
							{
								$op[$docCode]['msg'] =trans('affiliate/account_controller.invalid_file_format');
								$op[$docCode]['status'] = false;							
							}
						}
						if (!empty($uploaded_file))
						{						
							$kycDoc[$docCode]['account_id'] = $this->userSess->account_id;						
							$kycDoc[$docCode]['doc_number'] = $op[$docCode]['doc_number'] = ($this->request->has($docCode.'_no')) ? $this->request->get($docCode.'_no'):'';
							$kycDoc[$docCode]['path'] = $op[$docCode]['path'] = $folder_path.$filename;
							$kycDoc[$docCode]['document_type_id'] = $document_type;                    
							$kycDoc[$docCode]['created_on'] = getGTZ(); //date('Y-m-d H:i:s');						
						}
					}
					else
					{
						$op[$docCode]['msg'] = trans('affiliate/account_controller.cant_upload_files');
						$oop[$docCode]['status'] = false;					
					} 
				}				
			}
			
			if(!empty($kycDoc)){
		       $upload = $this->settingsObj->kyc_document_upload($kycDoc,$this->userSess);		
		    }
			if (!empty($upload))
			{	
				foreach($upload as $k=>$result)
				{	
					if($result){
						$op[$k]['msg'] = trans('affiliate/kyc.'.$k.'');
						//ucfirst($k).' document uploaded Successfully'; 
						$op[$k]['path'] = asset($this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.WEB').$op[$k]['path']);
						$op[$k]['doc_number'] = $op[$k]['doc_number'];
						$op[$k]['status'] = true;								
					}else{
						$op[$k]['msg'] = ucfirst($k).' document not uploaded!';
						$op[$k]['status'] = false;
					}			  
				}
				$op['kyc_status'] = $this->settingsObj->getKycStatus($this->userSess->account_id);
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
			}
			else
			{
			   $op['msg'] = trans('affiliate/account_controller.could_not_process_req');
			   $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');			
			}
		}else{
		    $op['msg'] = 'Please choose file to upload Kyc Document.';
	        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');	
		}
		/*  print_r($op);exit;  */
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}
	
	public function securitypin_create(){
		if (!$this->userSess->has_pin)
        {
            $data = [];
            $data['security_pin'] = $this->request->new_security_pin;
            $data['account_id'] = $this->userSess->account_id;
			
            if ($this->affObj->saveProfilePIN($data))
            {
                $this->userSess->has_pin = true;
                $this->session->set($this->sessionName, $this->userSess);
				$mdata = [
						'full_name'=>$this->userSess->full_name,
						'uname'=>$this->userSess->uname,				
						'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
						'site_name' => $this->pagesettings->site_name];					
				    CommonNotifSettings::affNotify('affiliate.account.settings.create_securitypin', $this->userSess->account_id, 0, $mdata,true,false);					
					$op['msg'] = trans('affiliate/settings/security_pwd_js.profile_pin_save_success');
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
             }
              else
				{ 
					$op['msg'] = trans('general.something_wrong');
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				}
        }
	    else
        {
            $op['msg'] = trans('general.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
		 return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}	
	
	/* Email Update */
	public function verifyEmail_Send ()
    {
		if($this->userSess->has_pin){
			$new_email = '';
			$op = array();
			
			$op['msg'] = trans('affiliate/general.something_wrong');
			$op['status'] = trans('affiliate/general.error');  
			 
			$cur_email = $this->userSess->email; 			
			$ses = [
				'account_id'=>$this->account_id,
				'code'=>'',
				'hash_code'=>''];			
			$ses['code'] = rand(100000, 999999);
			$ses['hash_code'] = md5($ses['code']);		
			$this->session->set('verifyEmailSess', $ses);				
			
			$token = $this->session->getId().'.'.$ses['hash_code'];			
			$data = ['email_verify_link'=>route('aff.settings.email.verify-link',['token'=>$token])];
			 CommonNotifSettings::affNotify('affiliate.account.settings.email_verification', $this->userSess->account_id, 0,$data,true,false,true);	 
			$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('affiliate/settings/verify_email.check_email_inbox',['email'=>$this->commonstObj->maskEmail($this->userSess->email)]);
			//$op['link'] = $data['email_verify_link'];		
		}
		else{
		  $op['status'] = $this->statusCode =  $this->config->get('httperr.UN_PROCESSABLE');
		  $op['msg'] = 'Create your Security PIN before processing this request';
	   }
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
	
	public function verifyEmail_link()
	{
		$data = $sdata = [];  
		$token = $this->request->token;
        if(!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);			
            if($this->session->has('verifyEmailSess')){ 
                $sdata = $this->session->get('verifyEmailSess');				
                $account_id = (isset($this->userSess->account_id) && !empty($this->userSess->account_id)) ? $this->userSess->account_id : '';
				$data['btnMsg'] =($sdata['account_id'] == $account_id) ? 'Click Here to Home' : 'Click Here to Login';
				if($sdata['account_id'] == $account_id && $sdata['hash_code'] == $access_token[1])
				{
					$res = $this->affObj->verify_email($account_id);					
					if($res){
						$data['msg'] = "Email address verified successfully."; 		
						$data['status'] = $this->config->get('httperr.SUCCESS');
					}
					else {
						$data['msg'] = "Verification session expired. Please try again. <a href='".route('aff.profile.affiliate-details')."'>Back to Contact Details</a>"; 
						$data['status'] = $this->config->get('httperr.UN_PROCESSABLE');
					}
				}				
				else
				{
					$data['msg'] = "Verification session expired. Please try again. <a href='".route('aff.profile.affiliate-details')."'>Back to Contact Details</a>"; 
					$data['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				}
            }
        }  
	    return view('affiliate.settings.email_verification',$data);	
	}	
	
	/* Mobile Number Update */
    public function verifyMobile_Sendotp ()
    {   
		$new_mobile = '';
		$op = array();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
				
		$cur_mobile = $new_mobile = '9952106187';//$this->userSess->mobile;
		$ses = [
			'new_mobile'=>$new_mobile,
			'account_id'=>$this->account_id,
			'code'=>'',
			'hash_code'=>'']; 			

		$ses['code'] = rand(100000, 999999);
		$ses['hash_code'] = md5($ses['code']);		
		$this->session->set('verifyMobSess', $ses);				
		$token = $this->session->getId().'.'.$ses['hash_code'];		
		CommonNotifSettings::affNotify('affiliate.account.settings.mobile_verification', 0, 0,['code'=>$ses['code'],'mobile'=>$new_mobile], true,false);
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
		//$op['code'] = $ses['code'];	
		$op['msg'] = trans('affiliate/settings/change_mobile.check_mobile_inbox',['mobile'=>maskMobile($new_mobile)]);
		$op['token'] = $token;
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
	
	public function verifyMobile_otp()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$checkType = 0;
		if($this->session->has('verifyMobSess'))
		{
			$verifySess = $this->session->get('verifyMobSess');			
		
			if (!empty($postdata))
			{
				if(isset($postdata['verify_code']))
				{
					$rules =[
						'verify_code' => 'required|numeric|digits_between:6,6',
					];
					$messages =[
						'verify_code.required' => trans('affiliate/validator/change_mobile_js.verify_code'),
						'verify_code.numeric' => trans('affiliate/validator/change_mobile_js.numeric'), 
						'verify_code.digits_between' => trans('affiliate/validator/change_mobile_js.maxlength'),
					];
					$validator = Validator::make($postdata,$rules,$messages);
					if ($validator->fails())
					{	
						$ers = $validator->errors();
						foreach($rules  as $key=>$formats){
							$op['errs'][$key] =  $validator->errors()->first($key);			
						}
						return $this->response->json($op,500);
					}
					$checkType = 1;
				}				
			}		
			$postdata = $this->request->all();
			if ($checkType == 1 && !empty($verifySess))
			{	
				if ($verifySess['hash_code'] == md5($postdata['verify_code']))
				{	
					$updateRes = $this->settingsObj->update_mobile_verification($this->userSess->account_id);
					if ($updateRes){   				
						$this->session->forget('verifyMobSess');
						$op['url'] = route('aff.settings.mobileverification');
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = trans('affiliate/settings/change_mobile.mobile_verification_success');
					}
					else {
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = "Mobile number already verified.";			
						$op['msgclass'] = "warning";
					}
				}
				else
				{
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('affiliate/settings/change_mobile.mobile_req_expiry');					
				}
			}
			else {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('affiliate/settings/change_mobile.mobile_req_expiry');
			}			
		}	
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 		
    }

}