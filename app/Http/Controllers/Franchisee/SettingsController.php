<?php
namespace App\Http\Controllers\Franchisee;

use Illuminate\Support\Facades\Validator;
use App\Library\MailerLib;
use App\Http\Controllers\FrBaseController;
use App\Models\Commonsettings;
use App\Models\Franchisee\Settings;
use App\Helpers\CommonNotifSettings;

use File;
use CommonLib;
use SendSMS;

class SettingsController extends FrBaseController {    
    public function __construct ()
    {
        parent::__construct();
		$this->commonObj = new Commonsettings();
	 	$this->smsObj = new SendSMS;	 	
		$this->settingsObj = new Settings;	
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
			$op['msg'] = 'Bank Account details are updated successfully';					
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['postdata']=$postdata;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		
    }

	public function Get_Ifsc_Bank_Details ()
	{	
		$op = [];
		$postdata = $this->request->all();     		
		$postdata['account_id'] = $this->userSess->account_id;     		
		$postdata['country_id'] = $this->userSess->country_id;
		$res = $this->commonObj->Get_Ifsc_Bank_Details($postdata);		
		if(!empty($res) && $res->valid){
			$op['data'] = $res;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}else {
		    $op['msg'] = 'Please provide a valid IFSC code!';
		    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');	
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		
	}
	
	/* Kyc Upload Document */
	public function kycDocumentUpload()
	{	
	    $op = $kycDoc   = $upload = [];
		$uploaded_file  = $document_type = '';      
        $postdata 		= $this->request->all();	
	    $upload_data = array('pan_no','pan','id_proof','address_proof','cheque');
		$op['msg']	 	= trans('franchisee/account_controller.could_not_process_req');		
		if(!empty($postdata)){
			  foreach($postdata as $docCode=>$v){
           		  if(in_array($docCode,$upload_data))	
				    { 
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
									$org_name    = $attachment->getClientOriginalName();
									$ext 		 = $attachment->getClientOriginalExtension();
									$file_extentions = strtolower($ext);
									$filtered_name = \AppService::slug($org_name);					
									$file_name   = explode('_', $filtered_name);
									$file_name   = $file_name[0];
									$file_name   = $file_name.'.'.$ext;
									$folder_path = getGTZ(null, 'Y').'/';  
									$move_path 	 = $this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.LOCAL');	
																
									if(!File::exists($move_path.getGTZ(null, 'Y'))){
										File::makeDirectory($move_path.getGTZ(null, 'Y'),777,true);	
									}
									$filename = date('dmYHis').$this->userSess->account_id.'_'.$file_name;
									$uploaded_file =$this->request->file($docCode)->move($move_path.$folder_path, $filename);
									$postdata['file_upload1'] = $filename;								
								}
							else
							{
								$op[$docCode]['msg'] =trans('franchisee/account_controller.invalid_file_format');
								$op[$docCode]['status'] = false;							
							}
						}
					 if (!empty($uploaded_file))
						 {	
                           /* print_R($this->request->get($docCode.'_no')); die; */					
							$kycDoc[$docCode]['account_id'] = $this->userSess->account_id;						
							$kycDoc[$docCode]['doc_number'] = $op[$docCode]['doc_number'] = ($this->request->has($docCode.'_no')) ? $this->request->get($docCode.'_no'):'';
							$kycDoc[$docCode]['path'] = $op[$docCode]['path'] = $folder_path.$filename;
							$kycDoc[$docCode]['document_type_id'] = $document_type;                    
							$kycDoc[$docCode]['created_on'] = getGTZ(); //date('Y-m-d H:i:s');						
					    }
						
				     }
					else
					{
						$op[$docCode]['msg'] = trans('franchisee/account_controller.cant_upload_files');
						$op['msg'] = trans('franchisee/account_controller.cant_upload_files');
						$oop[$docCode]['status'] = false;					
					} 
				 }
			 }
			 else{
				        $kycDoc[$docCode]['account_id'] = $this->userSess->account_id;						
						$kycDoc[$docCode]['doc_number'] = $op[$docCode]['doc_number'] = ($this->request->has($docCode)) ? $this->request->get($docCode):'';
                        $document_type = $this->config->get('constants.KYC_DOCUMENT_TYPE.'.strtoupper($docCode));						
						$kycDoc[$docCode]['document_type_id'] = $document_type;                    
						$kycDoc[$docCode]['created_on'] = getGTZ(); //date('Y-m-d H:i:s');	
			   } 
			 
	    } 
			if(!empty($kycDoc)){
		       $upload = $this->settingsObj->kyc_document_upload($kycDoc,$this->userSess);		
			 
		    }
			if (!empty($upload))
			{	
				foreach($upload as $k=>$result)
				{	
				  if(in_array($k,$upload_data)){ 
					if($result){
						$op[$k]['msg'] = ucfirst($k).' document uploaded Successfully'; 
						$op[$k]['path'] = asset($this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.WEB').$op[$k]['path']);
						$op[$k]['doc_number'] = $op[$k]['doc_number'];
						$op[$k]['status'] = true;										
					}else{
						$op[$k]['msg'] = ucfirst($k).' document not uploaded!';
						$op[$k]['status'] = false;
					}
                  }
				 else{
					    $op[$k]['msg'] = 'document updated Successfully'; 
					    $op[$k]['doc_number'] = $op[$k]['doc_number'];
						$op[$k]['status'] = true;
					 }			 
				}
				$op['kyc_status'] = $this->settingsObj->getKycStatus($this->userSess->account_id);
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
				$op['msg']	 	= 'Uploaded Successfully'; 
			}
			else
			{
			   $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');			
			}
		}else{
		    $op['msg'] = 'Please choose file to upload Kyc Document.';
	        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');	
		}
		//print_r($op);exit;
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}
	
/* Change Email */
	public function sendEmailVerification ()
    {   
		$new_email = '';
		$op = array();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');  
		$cur_email = $this->userSess->email; 			
		$ses = [
			'account_id'=>$this->account_id,
			'vcode'=>'',
			'hash_code'=>''];			
		/* $ses['code'] = rand(100000, 999999); */
		$ses['vcode'] = $this->commonstObj->random_strings(8);
		$ses['hash_code'] = md5($ses['vcode']);		
		$this->session->set('changeEmailSess', $ses);				
		
		$token = $this->session->getId().'.'.$ses['hash_code'];			
		$data = ['vcode'=>$ses['vcode'],'email_verify_link'=>route('fr.settings.changeemail.verification',['token'=>$token])];
		CommonNotifSettings::affNotify('franchisee.account.settings.change_email_verification', $this->userSess->account_id, 0, $data,true,false);	
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
		$op['msg'] = trans('franchisee/settings/change_email.check_email_inbox',['email'=>$this->commonstObj->maskEmail($this->userSess->email)]);
		//$op['link'] = $data['email_verify_link'];
	   //$op['code']=$ses['vcode'];

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
					$data['msg'] = trans('franchisee/settings/change_email.verifyemail_sess_expire');
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
	    return view('franchisee.settings.change_email_verification',$data);	
	}
	public function sendEmailVerificationOTP ()
    {   
		$new_email = '';
		$op = array();
        $postdata = $this->request->all();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');  
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
			CommonNotifSettings::affNotify('franchisee.account.settings.change_email_verification_otp', 0, 0, $data,true,false);	
			$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
			$op['msg'] = trans('franchisee/settings/change_email.check_email_for_otp',['email'=>$this->commonstObj->maskEmail($new_email)]);
			//$op['code'] = $ses['code'];		
		    //$op['token'] = $token;
		   }
		 else{
				$op['error'] = ['vcode'=>["Invalide Verification Code"]];
				$op['status'] = $this->statusCode = 400;  
			}
		}
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }	
	public function verify_change_email_otp()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');
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
						'verify_code.required' => trans('franchisee/validator/change_email_js.verify_code'),
						'verify_code.numeric' => trans('franchisee/validator/change_email_js.numeric'), 
						'verify_code.digits_between' => trans('franchisee/validator/change_email_js.maxlength'),
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
							'old_email' => $oldEmail,
							'email' => $verifySess['new_email'],
						];
					CommonNotifSettings::affNotify('franchisee.account.settings.change_email_notification', 0, 0, $mailData,true,false);	
						return $this->response->json(['status'=>200,'msg'=>trans('franchisee/settings/change_email.update_email_success')]);
					}
				   else {
						return $this->response->json(['status'=>500,'msg'=>trans('franchisee/settings/change_email.email_req_expiry')]);
					}
				}
				else
				{
					$op['msg']=trans('franchisee/settings/change_email.invalid_otp');
				    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
				}
			}	
		}		
    }
 /* Change Email End */
 
 /*Change Mobile Start */
    public function sendMobileVerification(){

		$new_email = '';
		$op = array();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');  
		 
		$cur_email = $this->userSess->email; 			
		$ses = [
			'account_id'=>$this->account_id,
			'code'=>'',
			'hash_code'=>''];			
		$ses['code'] = rand(100000, 999999);
		$ses['hash_code'] = md5($ses['code']);		
		$this->session->set('changeMobileSess', $ses);				
		$token = $this->session->getId().'.'.$ses['hash_code'];		
		$data = ['email_verify_link'=>route('fr.settings.changemobile.verification',['token'=>$token])];
		CommonNotifSettings::affNotify('franchisee.account.settings.change_mobile_verification', $this->userSess->account_id,0,$data,true,false);
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
		$op['msg'] = trans('franchisee/settings/change_email.check_email_inbox',['email'=>$this->commonstObj->maskEmail($this->userSess->email)]);
	//	$op['link'] = $data['email_verify_link'];		
		
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
					$data['msg'] = trans('franchisee/settings/change_mobile.verifyemail_sess_expire');
				}
             }
         }  
	   return view('franchisee.settings.change_mobile_verification',$data);	
	}
	public function sendMobileVerificationOTP ()
     {   
		$new_mobile = '';
		$op = array();
		$postdata = $this->request->all();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');
		
		if (!empty($postdata))
        {			
			$rules =  [            
				'mobile' => 'required|numeric|unique:'.$this->config->get('tables.ACCOUNT_MST').',mobile',
			];
			$messages = [
			  'mobile.required' => trans('franchisee/validator/change_mobile_js.required'), 
			  'mobile.numeric' => trans('franchisee/validator/change_mobile_js.invalid_mobile'), 
			  'mobile.digits_between' => trans('franchisee/validator/change_mobile_js.mobile_max'),
			  'mobile.unique' => trans('franchisee/validator/change_mobile_js.unique'),
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
             CommonNotifSettings::affNotify('franchisee.account.settings.change_mobile_verification_otp', 0, 0,['code'=>$ses['code'],'mobile'=>$new_mobile], true,false);	

			$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
		//	$op['code'] = $ses['code'];	
			$op['msg'] = trans('franchisee/settings/change_mobile.check_mobile_inbox',['mobile'=>maskMobile($new_mobile)]);
		//	$op['token'] = $token;
	       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
         }

    }
	public function verify_change_mobile_otp()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');
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
						'verify_code.required' => trans('franchisee/validator/change_email_js.verify_code'),
						'verify_code.numeric' => trans('franchisee/validator/change_email_js.numeric'), 
						'verify_code.digits_between' => trans('franchisee/validator/change_email_js.maxlength'),
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
						CommonNotifSettings::affNotify('franchisee.account.settings.change_mobile_notify', $this->userSess->account_id, 0, $mdata,true,false);
						
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] =trans('franchisee/settings/change_mobile.update_mobile_success');
					}
					else {
						
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = trans('franchisee/settings/change_mobile.mobile_verified');				
						$op['msgclass'] = "warning";
					}
				 }
				else
				{
					$op['msg']=trans('franchisee/settings/change_mobile.invalid_otp');
				    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					
				}
			}	
		}		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
   /*End */
 
   /* Verify Mobile Number Update */
    public function verifyMobile_Sendotp ()
    {
		$new_mobile = '';
		$op = array();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');
				
		$cur_mobile = $new_mobile = $this->userSess->mobile;//$this->userSess->mobile;
		$ses = [
			'new_mobile'=>$new_mobile,
			'account_id'=>$this->account_id,
			'code'=>'',
			'hash_code'=>'']; 			
		$ses['code'] = rand(100000, 999999);
		$ses['hash_code'] = md5($ses['code']);		
		$this->session->set('verifyMobSess', $ses);	
		$token = $this->session->getId().'.'.$ses['hash_code'];		
      	CommonNotifSettings::affNotify('franchisee.account.settings.mobile_verification', 0, 0,['code'=>$ses['code'],'mobile'=>$new_mobile], true,false);
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');	
		$op['msg'] = trans('franchisee/settings/change_mobile.check_mobile_inbox',['mobile'=>maskMobile($new_mobile)]);
		$op['token'] = $token;
		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
	
	 public function verifyMobile_SendotpResend (){
		 $op = [];
         $data = [];
		 if ($this->session->has('verifyMobSess'))
         {
		  $data = $this->session->get('verifyMobSess');
		  $data['code'] = rand(100000, 999999);
          $data['hash_code']=md5($data['code']);
		  $this->session->set('verifyMobSess', $data);
          $token = $this->session->getId().'.'.$data['hash_code'];		
	     CommonNotifSettings::affNotify('franchisee.account.settings.mobile_verification', 0, 0,['code'=>$data['code'],'mobile'=>$data['new_mobile']], true,false);		  
		  $op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');	
		  $op['msg'] = trans('franchisee/settings/change_mobile.check_mobile_inbox',['mobile'=>maskMobile($data['new_mobile'])]);
		  $op['token'] = $token; 
		 }
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
	 }

   public function verifyMobile_otp()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('franchisee/general.something_wrong');
        $op['status'] = trans('franchisee/general.error');
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
						'verify_code.required' => trans('francisee/validator/change_mobile_js.verify_code'),
						'verify_code.numeric' => trans('francisee/validator/change_mobile_js.numeric'), 
						'verify_code.digits_between' => trans('francisee/validator/change_mobile_js.maxlength'),
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
						$op['url'] = route('fr.settings.mobileverification');
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = trans('franchisee/settings/change_mobile.mobile_verification_success');
					}
					else {
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = trans('franchisee/settings/change_mobile.mobile_verified');				
						$op['msgclass'] = "warning";
					}
				}
				else
				{
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('franchisee/settings/change_mobile.invalid_otp');					
				}
			}
			else {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('franchisee/settings/change_mobile.mobile_req_expiry');
			}			
		}	
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 		
    }
}