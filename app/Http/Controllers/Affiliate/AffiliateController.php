<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Settings;
use App\Models\LocationModel;
use TWMailer;
use App\Helpers\CommonNotifSettings;
use File;
use Storage;
use CommonLib;
use Validator;
class AffiliateController extends AffBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
		$this->settingsObj = new Settings;	
		$this->lcObj = new LocationModel();		
    }  
	
	public function login() {
		return view('affiliate.login');
	}
	
	public function signup($referral_name='')
	{	
		$data = [];						
		$sponsor_info=$this->affObj->referral_user_check($referral_name);		
		if($sponsor_info['status']==200){
			$data['ip_country'] = $this->commonObj->getIpCountry('103.231.216.102');			
			$data['sponsor_info']= (object)$sponsor_info;		
			$data['countries']= $this->lcObj->getCountries(['operate'=>true]);
			$data['genders']= $this->commonObj->genders();
			$this->session->set('reg_sponsor_info',$data['sponsor_info']);
		}
		else {
			$data['errmsg'] = $sponsor_info['msg'];			
		}			
		return view('affiliate.signup',$data);		
	}
	
	public function save_account ()
    {
		$data['status'] = 'error';
        $data['msg'] = '';
        $opArray = array();
        $opArray['status'] = 'error';
        $opArray['msg'] = '';
        $activation = true; /* [instant/activation] */
        $postdata = $this->request->all();	    
		if($this->session->has('reg_sponsor_info')){
			$sponsor_info = $this->session->get('reg_sponsor_info');			
			$postdata['activation'] = $activation;				
			$usrExist = $this->affObj->account_check($postdata['username'], array(
				'useravailablility'=>1,
				'existscheck'=>0,
				'referralcheck'=>0,
				'loguserlineage'=>0,
				'reqfor'=>'reg'));   				
			
			if (isset($postdata['firstname'])){
				$data['fullname'] = $postdata['firstname']." ".$postdata ['lastname'];
			}
			else if (isset($postdata['fullname'])){
				$data['fullname'] = $postdata['fullname'];
			}

			/* referral details */
			$data['referral_account_id'] = $sponsor_info->sponser_account_id;
			$data['referral_id'] = $sponsor_info->sponser_id;
			$data['referral_country'] = $sponsor_info->sponser_country;
			$data['referral_mobile'] = $sponsor_info->sponser_mobile;
			$data['referral_email'] = $sponsor_info->sponser_email;
			$data['referral_name'] = $sponsor_info->sponser_name;
			$data['referral_fullname'] = $sponsor_info->sponser_fullname;
			$data['referral_link'] = url("/".$data['referral_name']);							
			$data['code'] = rand(100000, 999999);			
			//$this->session->set('newRegSess',array_merge($data,$postdata));
			$regData = array_merge($data,$postdata);
			$token = $this->commonObj->getShareToken($data['code']);
			$res = $this->affObj->saveRegisterTemp($token['hashcode'],$regData);
			if($res ){
				$data['activate_link'] = route('aff.signup.activation',['token'=>$token['decryHashcode']]);				
				CommonNotifSettings::affNotify('affiliate.signup.verification', 0, 0, array_merge($data,$postdata),true,false);							
				$this->statusCode = $this->config->get('httperr.SUCCESS');				
				$opArray['status'] = $this->statusCode;
				//$opArray['activate_link'] = $data['activate_link'];
				$opArray['msg'] = trans('affiliate/account_controller.signup_verification');				               
			}
			
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
    }
	
	
	public function signup_activation ($token='')
    {
		$data['status'] = 'error';
        $data['msg'] = '';
        $opArray = array();
        $opArray['status'] = 'error';
        $opArray['msg'] = '';
        $activation = true; /* [instant/activation] */
		//$postdata = $this->request->all();	
	
		if(!empty($token) && strpos($token, '.'))
        {
			$regdata = $this->affObj->getRegisterTemp_data($token);
			//echo '<pre>';
			//print_r($regdata);
		
           // $this->session->setId($access_token[0], true);
            if(!empty($regdata)){		
				$postdata = $regdata->regdata;
				$validateRules = [
				];
				
				$rules = [					
					'email'=>'unique:account_mst,email,NULL,account_id,is_deleted,0',
					'username'=>'unique:account_mst,uname',
					'mobile'=>'unique:account_mst,mobile,NULL,account_id,is_deleted,0',					
				];
                $messages = [
					'email.unique' => "Email address already exist",
					'mobile.unique' => "This Mobile number has already been used",
					'username.unique' => "This Username has already been used",
				];
				
				$validator = Validator::make($postdata, $rules, $messages);
                if ($validator->fails())
                {
                    //$response = [];
					$opArray['status'] = $this->config->get('httperr.UN_PROCESSABLE');
                    $opArray['valError'] = $validator->messages(true);					
                }
				else {
				
					$usrExist = $this->affObj->account_check($postdata['username'], array(
						'useravailablility'=>1,					
						'existscheck'=>0,	
						'referralcheck'=>0,	
						'reqfor'=>'reg'));  					
					
					
					if(!empty($usrExist) && $usrExist['status']=='ok')
					{
						//$sponsor_info = $this->session->get('reg_sponsor_info');					
						$postdata['activation'] = $activation;
						$postdata['sponser_account_id'] = $postdata['referral_account_id'];
						$postdata['sponser_fullname'] = $postdata['referral_fullname'];
						$postdata['sponser_uname'] = $postdata['referral_name'];
						$postdata['sponser_email'] = $postdata['referral_email'];					
						$account_info = $this->affObj->save_account($postdata);		
						
						if (!empty($account_info))
						{						
							if (isset($postdata['firstname'])){
								$data['fullname'] = $postdata['firstname']." ".$postdata ['lastname'];
							}
							else if (isset($postdata['fullname'])){
								$data['fullname'] = $postdata['fullname'];
							}
											   
							$opArray ['msg'] = \trans('affiliate/account_controller.account_approval', array(
							 'site_name'=>$this->pagesettings->site_name,
							 'login_link'=>url('login')));
							
							$data['mobile'] = $postdata['mobile'];
							$data['username'] = $account_info->user_name;
							$data['user_code'] = $account_info->user_code;
							$data['email'] = $postdata['email'];
							$data['pwd'] = $postdata['password'];				
							$data['country'] = $account_info->country;
							$data['state'] = isset($account_info->state)? $account_info->state:'';
							$data['login_link'] = url("login");				
							$key = md5($account_info->account_id.$data['email']);
							$this->affObj->update_verification_code($account_info->account_id, $key);
							$data['email_verify_link'] = route('aff.signup.verifyemail',['vcode'=>$key]);

							$data ['domain_name'] = $this->pagesettings->site_domain;
							
							/* referral details */
							$data['referral_email'] = $postdata['referral_email'];
							$data['referral_name'] = $postdata['referral_name'];
							$data['referral_fullname'] = $postdata['referral_fullname'];
							$data['referral_link'] = url("/".$data['referral_name']);				
							
							CommonNotifSettings::affNotify('affiliate.signup.activated', $account_info->account_id, 0, $data,true);
							CommonNotifSettings::affNotify('affiliate.newsignup_sponsor_notify', $account_info->account_id, 0, $data,true);
							
							$this->statusCode = $this->config->get('httperr.SUCCESS');			
							$opArray['status'] = $this->statusCode;
							$opArray['msg'] = trans('affiliate/account_controller.signup_success');				               
							//$this->session->forget('reg_sponsor_info');
							$this->session->forget('newRegSess');
						}				
						else
						{
							$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							$opArray['status'] = $this->statusCode;
							$opArray['errmsg'] = trans('affiliate/account_controller.registration_failed');
						}
					}
					else {
						$opArray['errmsg'] = trans('affiliate/account.verifyemail_sess_expire');
					}
				}
			}
			else {
				$opArray['errmsg'] = trans('affiliate/account.verifyemail_sess_expire');
			}
		}
		else {
			$opArray['errmsg'] = trans('affiliate/account.verifyemail_sess_expire');
		}
		return view('affiliate.signup_activated',$opArray);		
    }
	
	public function account_upgrade(){		

		$data['status'] = 'error';
        $data['msg'] = '';
        $opArray = array();
        $opArray['status'] = 'error';
        $opArray['msg'] = '';
        $activation = true; 		
        $postdata = $this->request->all();	    	
		if($this->session->has('reg_sponsor_info') && $this->session->has('regSess')){
			$sponsor_info = $this->session->get('reg_sponsor_info');			
			$regSess = $this->session->get('regSess');
			$postdata['regSess'] = $regSess;
			$postdata['username'] = $regSess->uname;
			$postdata['activation'] = $activation;
			$postdata['sponser_account_id'] = $sponsor_info->sponser_account_id;
			$postdata['sponser_fullname'] = $sponsor_info->sponser_fullname;
			$postdata['sponser_uname'] = $sponsor_info->sponser_name;
			$postdata['sponser_email'] = $sponsor_info->sponser_email;			 				
			$account_info = $this->affObj->save_account_upgrade($postdata);
			$data['site_settings'] = $site_settings = $this->pagesettings;
			if (!empty($account_info))
			{
				//print_r($regSess);
				//print_r($account_info);
				$data['fullname'] = $account_info->full_name;			
				$data['uname'] = $account_info->username;
				$data['email'] = $account_info->email;
				$data['mobile'] = $account_info->mobile;
				$data['country'] = $account_info->country;
				$data['state'] = isset($account_info->state)? $account_info->state : '';
				$data['site_settings'] = $this->pagesettings;
				$data['login_link'] = route("aff.login");
				$data ['domain_name'] = $this->pagesettings->site_domain;	
				
				/* referral details */
				$data['referral_email'] = $sponsor_info->sponser_email;
				$data['referral_name'] = $sponsor_info->sponser_name;
				$data['referral_fullname'] = $sponsor_info->sponser_fullname;
				$data['referral_contact'] = $sponsor_info->sponser_mobile;
				$data['referral_link'] = url("/".$data['referral_name']);
				$email_data = array(
					'email'=>$data['email'],
					'site_domain'=>$this->pagesettings->site_domain
				);	
				
				if(isset($postdata['email'])){
		
					$data['act_link'] = url("activation/".$account_info->activation_key);
					$key = md5($account_info->account_id.$data['email']);
					$this->affObj->update_verification_code($account_info->account_id, $key);
					$data['email_verify_link'] = url('user/verify_email').'?verification_code='.$key;							
					
					CommonLib::notify(null, 'affiliate.account_upgraded', ['code'=>$verify_code], ['full_name'=>$data['fullname'], 'uname'=>$data['username'], 'mobile'=>$data['mobile'], 'email'=>$data['email']]);
					/* new User for email */
					/*$mstatus = TWMailer::send(array(
					 'to'=>$postdata['email'], 
					 'subject'=>$this->config->get('mailcontents.new_user'),				
					 'data'=>$data,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);	*/						
				} 
				else {							
					CommonNotifSettings::affNotify('affiliate.signup.activated', $account_info->account_id, 0, $data,true);	
				}				
				CommonNotifSettings::affNotify('affiliate.newsignup_sponsor_notify', $account_info->account_id, 0, $data);
				$opArray['msg'] = trans('affiliate/account_controller.signup_activation');					 
				$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');				
				$opArray['status'] = $this->statusCode;						               
			}				
			else
			{
				$this->statusCode = $this->config->get('httperr.SUCCESS');
				$opArray['status'] = $this->statusCode;
				$opArray['msg'] = trans('affiliate/account_controller.registration_failed');
			}
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);
	}
	
	
	public function signup_email_verify($vcode=''){
		$url =  '';
		$postdata = $this->request->all();		
		$data['msg'] = 'danger';;		
        if ($vcode && !empty($vcode))
        {
            $status = $this->affObj->verify_email($vcode);
            if ($status==1){				
                $data['msg'] = 'success';				
				$url = route('aff.dashboard');
				
            } else if ($status==2){
				$data['msg'] = 'success';
			}		
        }
		if($url) {
			return $this->redirect->to($url)->send();
		} else {
			return view('affiliate.email_verification',$data);		
		}
	}	
	public function myprofile()
	{
		$data = array();
		$data['userInfo'] = $this->affObj->getUserinfo(['account_id'=>$this->userSess->account_id]);
		
		$bladdRes = $this->affObj->getUserAddr($this->userSess->account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		if(!empty($bladdRes) && $bladdRes['status']==$this->config->get('httperr.SUCCESS')){
			$data['billingAddr'] = $bladdRes['address'];
		}		
		$shaddRes = $this->affObj->getUserAddr($this->userSess->account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.SHIPPING'));
		if(!empty($shaddRes) && $shaddRes['status']==$this->config->get('httperr.SUCCESS')){
			$data['shippingAddr'] = $shaddRes['address'];
		}
		$data['nominee'] = $this->affObj->getUserNominees($this->userSess->account_id);		
		//$data['kycfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.kyc_document_upload',['pan'=>'ABCDE1234Q','tax'=>'12ABCDE1234Q1Z1'],['pan_no','tax_no']);
		$data['kycfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.kyc_document_upload');	
		$data['fields'] = CommonNotifSettings::getHTMLValidation('aff.settings.bank-details');			
		$data['otp_vfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.securitypin.forgototp.verify');			
		$data['pin_sfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.securitypin.save');			
		$data['bank_account_details'] = $this->settingsObj->GetBankAccountDetails(['account_id'=>$this->userSess->account_id]);
		$prooftypes = $this->config->get('constants.KYC_DOCUMENT_TYPE');	
		foreach($prooftypes as $k=>$v){
		    $data['kyc_document'][$v] = $this->settingsObj->getKycDocument(array(
                    'account_id'=>$this->userSess->account_id,
                    'prooftype'=>$v));
		}	
      
		return view('affiliate.settings.profile',$data);		
	}	
	public function updateProfile ()
    {
        $op = [];
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');        
        $data = $this->request->all();
        $data['account_id'] = $this->userSess->account_id;        
        if ($data)
        {   
			$result = $this->affObj->updateProfile($data);
            if ($result && !is_array($result))
            {	
                $this->session->set($this->sessionName , (array) $this->userSess);
                $op['reload'] = 1;
				$op['msg'] = trans('affiliate/account/profile.updated');
				$op['msgClasss'] = 'green';
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
			else if(is_array($result)){
				$op['error'] = $result;					
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			}
            else
            {
				$op['msgClasss'] = 'yellow';				
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('affiliate/account/profile.no_changes');
            }
        }
        else
        {
            $op['msgClasss'] = 'warning';
			$op['msg'] = trans('user/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }			
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	
	/******** KYC Document ***********/
	public function kyc_doc(){	
		$data['pagesettings'] = $this->pagesettings;
		$data['user_log_info'] = $this->user_log_info;
		$data['userdetails']  = $this->userSess;	
		$data['country_id']   = $this->userSess->country_id;
		$data['useraccount']  = $this->affObj->get_user_accountinformation_byid($this->user_id);
		$data['user_name']    = $this->user_name;		
		$data['user_id']	  = $this->user_id;	
		$data['proof_type']  	= $this->userdashboardObj->get_proof_type();
		$data['kyc_doc_details']  = $this->userdashboardObj->get_kyc_doc($this->user_id);
		$user_setting = $this->commonstObj->getSettings('kyc_field');
		$data['user_settings'] = $user_settings =  json_decode(stripslashes($user_setting),true);
		$data['req_field'] = $user_settings['field_data'][1]['value'];
		foreach($user_settings['field_data'] as $usr_set){
			$usr_set_country_id = explode(',',$usr_set['country_id']);
			
			if(in_array($data['country_id'],$usr_set_country_id)){
				$data['req_field'] = $usr_set['value'];
			}
		}
		return view('affiliate.kyc_document',$data);
	}
	/*
	public function save_kyc_doc(){
		$op = array();		
		$postdata = Input::all();		
		$result = $this->userdashboardObj->save_kyc_doc($postdata, $this->user_id);
		if($result){
			$op['status'] = "ok";
			$op['msg']    = "KYC Document Update Successfully";
		}
		else{
			$op['status'] = "error";
			$op['msg']    = "Something went wrong";
		}
		echo json_encode($op);		
	}
	
	public function doc_upload_image(){		
		$op = array();
		$op['success'] = "error";
		$op['msg']    = "Please choose image to upload";
		$op['image_name'] = "";
		
		$file = Input::file('upload_image');
		$user_id = Input::get('user_id');
		$upload_type = Input::get('upload_type');
		if(!empty($file)){			
			$input = array('image' => $file);
			$rules = array(
				'image' => 'image'
			);			
			$validator = Validator::make($input, $rules);			
			if ( $validator->fails() )
			{
				$op['success']= 'error';
				$op['msg'] = $validator->getMessageBag()->toArray();
				 
				Session::flash('message', $validator->getMessageBag()->toArray()); 
				Session::flash('alert-class', 'alert-danger'); 
			}
			else {				
				$destinationPath = Config::get('constants.KYC_DOCUMENT_IMAGE_PATH_TMP');
				$filename = $file->getClientOriginalName();				
				$ext = explode('.',$filename);
				$filename = $upload_type.date('Ymdhis').'.'.$ext[1];				
				Input::file('upload_image')->move($destinationPath, $filename);						
			    $op['success'] = 'ok';
			    $op['msg'] = 'New image added successfullly';
				$op['image_name'] = $filename;	
			}
		}
		echo json_encode($op);	
	}
	
	public function remove_doc_upload_image(){
		$op = array();
		$postdata = Input::all();
		$image_name = $postdata['image_name'];
		if(File::delete(Config::get('constants.KYC_DOCUMENT_IMAGE_PATH_TMP').$image_name)){
			$op['status'] = 'ok';
		    $op['msg'] = 'Image removed';
		}else{
			$op['success'] = 'error';
		    $op['msg'] = 'Something went wrong';
		}	
		echo json_encode($op);
	}*/
	
	Public function profileimage_withcrop_save ()
    {
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $attachment = $postdata['attachment'];
        $filename = '';
        $folder_path = getGTZ(null, 'Y').'/'.getGTZ(null, 'm').'/';
        $path = $this->config->get('constants.ACCOUNT.PROFILE_IMG.PATH');
        $postdata['account_id'] = $this->userSess->account_id;
        if (File::exists($path.getGTZ(null, 'Y')))
        {
            if (!File::exists($path.getGTZ(null, 'Y').'/'.getGTZ(null, 'm')))
            {
                File::makeDirectory($path.getGTZ(null, 'Y').'/'.getGTZ(null, 'm'));
            }
        }
        else
        {
            File::makeDirectory($path.getGTZ(null, 'Y'));
            File::makeDirectory($path.getGTZ(null, 'Y').'/'.getGTZ(null, 'm'));
        }
        $org_name = $attachment->getClientOriginalName();
        $ext = $attachment->getMimeType();
        $mine_type = array('image/jpeg'=>'jpg', 'image/jpg'=>'jpg', 'image/png'=>'png', 'image/gif'=>'gif');
        $ext = $mine_type[$ext];
        $filtered_name = $this->slug($org_name);
        $file_name = explode('_', $filtered_name);
        $file_name = $file_name[0];
        $file_name = $file_name.'.jpg';
        $filename = getGTZ(null, 'dmYHis').$file_name;
        if ($attachment->move($path.$folder_path, $filename))
        {
            $postdata['filename'] = $filename;
            $postdata['docpath'] = $folder_path.$filename;
            if ($this->affObj->profile_image_upload($postdata))
            {
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				
                $op['msg'] = trans('affiliate/account/profile.profile_image_updated');				
                $op['profile'] = $this->userSess->profile_image = asset($this->config->get('constants.ACCOUNT.PROFILE_IMG.SM').$postdata['docpath']);
				if($this->userSess->has_profile_img==1){		
					File::delete($this->config->get('constants.ACCOUNT.PROFILE_IMG.PATH').$this->userSess->profile_imagename);					
				}
                $this->config->set('app.accountInfo', $this->userSess);				
                $this->session->set($this->sessionName, $this->userSess);
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/account.no_changes');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
		
		
	public function kyc()
	{
		$data['pagesettings'] = $this->pagesettings;		
		$data['useraccount']  = $this->affObj->getUserinfo(['account_id'=>$this->userSess->account_id]);
		$data['user_name']    = $this->userSess->uname;		
		$data['id_proof']  	  = $this->affObj->get_affkyc_proof_types($this->config->get('constants.VERIFY_DOC_ID_PROOF'));
		$data['address_proof']= $this->affObj->get_affkyc_proof_types($this->config->get('constants.VERIFY_DOC_ADDRESS_PROOF'));
		$data['tax_proof']    = $this->affObj->get_affkyc_proof_types($this->config->get('constants.VERIFY_DOC_TAX_ID_PROOF'));		
		$data['kyc_doc_details']  = $this->affObj->get_affkyc_doc($this->userSess->account_id);
		$data['verification_count'] = $this->affObj->check_account_verification_count(array(
                    'account_id'=>$this->userSess->account_id,
                    'prooftypes'=>array(1,2,3)));
		$user_setting = $this->commonstObj->get_setting_value('affkyc_field');
		$user_setting = !empty($user_setting)? $user_setting: '';
		$data['user_settings'] = $user_settings =  json_decode(stripslashes($user_setting),true);
		$data['req_field'] = $user_settings['field_data'][1]['value'];
		if(!empty($user_settings['field_data'])){
			foreach($user_settings['field_data'] as $usr_set){
				$usr_set_country_id = explode(',',$usr_set['country_id']);			
				if(in_array($data['country_id'],$usr_set_country_id)){
					$data['req_field'] = $usr_set['value'];
				}
			}
		}
		//echo"<prE>";print_r($data['tax_proof']);exit;
		return view('affiliate.account.kyc',$data);		
	}
	
	/* Kyc Image Upload */
	public function kyc_upload()
	{		
		$uploaded_file = '';
        $opArray = array();
        $document_type = '';		
        $postdata = $this->request->all(); 		
        if ($this->request->hasFile('verify_file'))
        { 
            $attachment = $this->request->file('verify_file');
			$size = $this->request->file('verify_file')->getSize();			
            if ($size < 2049133)
            { 
                if ($this->request->get('document_types'))
                {
                    $document_type = $this->request->get('document_types');
                }		
				//print_r($document_type);exit;
                $data['verification_count'] = $this->affObj->check_account_verification_count(array(
                    'account_id'=>$this->userSess->account_id,
                    'prooftypes'=>array(1,2,3),
                    'document_type'=>array($document_type)));
                $filename = '';			
				
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
						$folder_path = getGTZ(null, 'Y').'/';    //$folder_path = getGTZ('Y').'/'.getGTZ('m').'/';
						$path = config('constants.ACCOUNT_VERIFICATION_MIN_UPLOADPATH');
                        $move_path = config('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH');	//	
						
						if(!File::exists($path.getGTZ(null, 'Y'))){
						    File::makeDirectory($path.getGTZ(null, 'Y'),777,true);	
						}
						if(!File::exists($move_path.getGTZ(null, 'Y'))){
						    File::makeDirectory($move_path.getGTZ(null, 'Y'),777,true);	
						}
                        $filename = date('dmYHis').$this->userSess->account_id.'_'.$file_name;	
                        /* image Resizing */						
                        if ($file_extentions == 'pdf')
                        { 
                            $uploaded_file = $this->request->file('verify_file')->move($path.$folder_path, $filename);
                        }
                        else
                        {
                            $uploaded_file =$this->request->file('verify_file')->move($move_path.$folder_path, $filename);
                            $postdata['file_upload1'] = $filename;
                            //$this->imgObj->imageresize($move_path.$filename, $path.$filename, 450);
                        }
                    }
                    else
                    {
                        return \Response::json(array(
                                    'status'=>'error',
                                    'msg'=>trans('affiliate/account_controller.invalid_file_format')));
                    }
                }
                if (!isset($document_type) || empty($document_type))
                {
                    $document_type = 8;
                }
				
                if (!empty($uploaded_file))
                {
                    $user = $this->userSess->account_id;
                    $sdata['account_id'] = $user;
                    $sdata['path'] = $folder_path.$filename;
                    $sdata['document_type_id'] = $document_type;                    
                    $sdata['created_on'] = date('Y-m-d H:i:s');				
					//echo"<pre>";print_r($sdata);exit;
                    $Upload = $this->affObj->save_account_upload($sdata,$this->userSess);
                }
                if ($Upload)
                {
                    return \Response::json(array(
                                'status'=>'ok',
                                'msg'=>trans('affiliate/account_controller.file_successfully_uploaded')));
                }
                else
                {
                    return \Response::json(array(
                                'status'=>'error',
                                'msg'=>trans('affiliate/account_controller.could_not_process_req')));
                }
            }
            else
            {
                return \Response::json(array(
                            'status'=>'error',
                            'msg'=>trans('affiliate/account_controller.cant_upload_files')));
            }
        }
        else
        {
            return \Response::json(array(
                        'status'=>'error',
                        'msg'=>trans('affiliate/account_controller.select_valid_file')));
        }		
	}
	
	public function getAccount_address($type=''){
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] = 'Please try again later';
		if(!empty($type)){
			$res = $this->affObj->getAddress($this->userSess->account_id,['type'=>$type]);
			if(!empty($res)){
				 $op['address'] = $res;
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function saveAccount_address(){
		
	}
	
	public function nominee(){
		$data['nominee'] = $op['nominee'] = $this->affObj->getUserNominees($this->userSess->account_id);		
		$data['genders'] = $this->commonObj->genders();
		$data['relation_ships'] = $this->commonObj->relation_ships();
	 	$data['fields'] = CommonNotifSettings::getHTMLValidation('aff.settings.nominee.save'); 
		/*  print_R($data['fields']); die;  */
		$op['template'] = view('affiliate.settings.nominee',$data)->render();
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}

	public function saveNominee(){		
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] = 'Please try again later';
		$postdata = $this->request->all();
		if(!empty($postdata)){			
			$res = $this->affObj->saveNominee($this->userSess->account_id,$postdata);
			if($res){
				$op['nominee'] = $res['nominee'];
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = trans('affiliate/account.nominee.save');
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function getPersonalInfo() {
		$data = [];
		$data['address_type'] = $type;
		$data['country_id'] = $this->userSess->country_id;
		$op['template'] = view('affiliate.settings.personal_info',$data)->render();
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	
	public function getAddress($type='billing') {
		
		$data = [];
		$data['address_type'] = $type;
	 if($data['address_type']=='billing'){
	       $bladdRes = $this->affObj->getUserAddr($this->userSess->account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		  if(!empty($bladdRes) && $bladdRes['status']==$this->config->get('httperr.SUCCESS')){
			         $data['addresstype'] = $bladdRes['address'];
		     }	
		  }
       if($data['address_type']=='shipping'){
			$shaddRes = $this->affObj->getUserAddr($this->userSess->account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.SHIPPING'));
	     if(!empty($shaddRes) && $shaddRes['status']==$this->config->get('httperr.SUCCESS')){
			       $data['addresstype'] = $shaddRes['address'];
		      }
		  } 
		$data['country_id'] = $this->userSess->country_id;
		$op['template'] = view('affiliate.settings.address_update',$data)->render();
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function saveAddress($type=''){
		$op = [];
		$addArr = ['billing'=>$this->config->get('constants.ADDRESS_TYPE.PRIMARY'),'shipping'=>$this->config->get('constants.ADDRESS_TYPE.SHIPPING')];
		$postdata = $this->request->all();
		$type = !empty($type)? $type:$this->config->get('constants.ADDRESS_TYPE.PRIMARY');
		if($postdata)
		{				
			$sdata['relative_post_id'] = $this->userSess->account_id;
			$sdata['post_type'] =$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'); 		
			$sdata['address_type_id'] = $addArr[$type];
			$sdata['country_id'] = $this->userSess->country_id;
			$sdata['flatno_street'] = strip_tags($postdata['address']['flat_no']);
			$sdata['landmark'] = strip_tags($postdata['address']['landmark']);
			$sdata['city'] = $postdata['address']['city_id'];
			$sdata['state'] = $postdata['address']['state_id'];
			$sdata['postal_code'] = $postdata['address']['postal_code'];
			
			$country_info = $this->lcObj->getCountry(['country_id'=>$sdata['country_id'],'allow_signup'=>true]);
			$cityInfo = $this->lcObj->get_city_list(0,0,$postdata['address']['city_id']);			
			$stateInfo = $this->lcObj->getState(0,$postdata['address']['state_id']);
			$formated_address = [];
			
			if(!empty($sdata['flatno_street'])) {
				$formated_address[] = $val['flatno_street'] = $sdata['flatno_street'];
			}
			
			if(!empty($sdata['landmark'])) {
				$formated_address[] = $val['landmark'] = $sdata['landmark'];
			}			
		
			if(!empty($cityInfo)) {
				$sdata['district'] = $cityInfo->district_id;
				$formated_address[] = $val['city'] = $cityInfo->city;				
				if(empty($stateInfo) && !empty($sdata['postal_code'])) {
					$formated_address[] = $cityInfo->district.'-'.$sdata['postal_code'];
				} 
				else {
					$formated_address[] = $val['district'] = $cityInfo->district;
				}
			}
			
			if(!empty($stateInfo)) {
				if(!empty($sdata['postal_code'])) {
					$geoPnts = $this->commonObj->getLocationInfo(['pincode'=>$sdata['postal_code'],'country'=>$this->userSess->country],false,true);
					$sdata['geolat'] = $geoPnts->lat;
					$sdata['geolng'] = $geoPnts->lng;
					$formated_address[] = $stateInfo->state.'-'.$sdata['postal_code'];
				}
				else {
					$formated_address[] = $val['state'] = $stateInfo->state;
				}
			}
			
			if(!empty($country_info)) {
				$formated_address[] = $val['country'] =  $country_info->country_name;
			}
			
			$sdata['formated_address'] = !empty($formated_address)? implode(', ',$formated_address) :'';						
			$op = $this->affObj->updateAddress($sdata);	
			$this->statusCode = $op['status'];
			$op['addtype'] = $type;
			return \Response::json($op, $this->statusCode, $this->headers, $this->options);
			
		}
	}
}