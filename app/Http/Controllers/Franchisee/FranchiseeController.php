<?php
namespace App\Http\Controllers\Franchisee;
use App\Http\Controllers\FrBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Franchisee\FrModel;
use App\Models\Franchisee\WalletModel;
use App\Models\Franchisee\FrTransaction;
use App\Models\Franchisee\FrReports;
use App\Models\Franchisee\Settings;
use App\Models\LocationModel;
use App\Helpers\CommonNotifSettings;
use File;
use Storage;
use CommonLib;

class FranchiseeController extends FrBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->frObj = new FrModel();
		$this->walletObj = new WalletModel();
		$this->frTransObj = new FrTransaction();
		$this->frReportObj = new FrReports();
		$this->settingsObj = new Settings;	
		$this->lcObj = new LocationModel();		
    }  
	public function login() {
		return view('franchisee.login');
	}
	public function dashboard() {
        $data = array();   
       /* $data['account_id'] = $this->userSess->account_id;
		$data['currency_id'] = $this->userSess->currency_id;
		$data['wallets'] = $this->frObj->getWalletList();
		
		$data['currency_code'] = $this->userSess->currency_code;
		$data['joining'] = $this->frReportObj->getJoiningReport($data);		
		$data['pkSales'] = $this->frReportObj->getPackageSales($data);*/
        $data['transaction_log'] = $this->frTransObj->getTransactionDetails([
            'account_id'=>$this->userSess->account_id,
			'start'=>0,
			'length'=>10]);
		
		$filter['account_id'] = $this->userSess->account_id;
		$filter['currency_id'] = $this->userSess->currency_id;
		$data['earnings']=$this->frReportObj->getTotalEarnings($filter);
		$data['today_earnings']=$this->frReportObj->getTodayEarnings($filter);
	    $balInfo = $this->walletObj->my_wallets($filter);		 
		 if($balInfo){					
			array_walk($balInfo, function(&$balInfos)
			{			
			 $balInfos->current_balance =\CommonLib::currency_format($balInfos->current_balance, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]); 
			 $balInfos->tot_credit =\CommonLib::currency_format($balInfos->tot_credit, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]);
			 $balInfos->tot_debit =\CommonLib::currency_format($balInfos->tot_debit, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]); 
			});
		}		
		$data['balInfo']=$balInfo;
		return view('franchisee.dashboard',$data);
	}
	public function sample(){
	
	    $merchant_id=59;
		$store_details= $this->frObj->get_store_details($merchant_id); 
     
	  if(!empty($store_details)){	
	    
		 foreach($store_details as $k=>$v){
			 
		       if($address_info=$this->frObj->get_store_address($v->address_id)){
				

				   $franchisee_district= $this->frObj->get_franchisee_address(['district_id'=>$address_info->district_id]);
	
				     if(!empty($franchisee_district)){
						   $data['franchisee_details']=$franchisee_district;
					     }
					    else{
						   $franchisee_state= $this->frObj->get_franchisee_address(['state_id'=>$address_info->state_id]);

						   $data['franchisee_details']=$franchisee_state;
					    }
						   $data['store_details']=$v;
						   $data['merchant_store_adrress']=$address_info;
						   
				           $franchisee_fee	= $this->frObj->Save_franchisee_commission_fee($data);
						if (!empty($franchisee_fee)) {
						    $this->statusCode =  $this->config->get('httperr.SUCCESS');
						    $op['msg'] = '';
                     }  
				 }
				 
			 }
	     }
		/*  return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  */
	}
	
		
	
	public function myprofile()
	{
		$data = array();
		$data['userInfo'] = $this->frObj->get_account_details(['account_id'=>$this->userSess->account_id]);
	    $data['franchise_logo_path']=$this->config->get('constants.FRANCHISEE.LOGO.PATH').$data['userInfo']->logo_path; 
		$data['fr_access'] = $this->frObj->franchisee_access_locations($this->userSess->franchisee_id,$this->userSess->franchisee_type);
	    $personaladdRes = $this->frObj->getUserAddr($this->userSess->account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		if(!empty($personaladdRes) && $personaladdRes['status']==$this->config->get('httperr.SUCCESS')){
			$data['personalAddr'] = $personaladdRes['address'];
		} 	
		 $franchiseeaddRes = $this->frObj->getUserAddr($this->userSess->franchisee_id,$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		if(!empty($franchiseeaddRes) && $franchiseeaddRes['status']==$this->config->get('httperr.SUCCESS')){
			$data['franchiseeAddr'] = $franchiseeaddRes['address'];
		} 
	
		$data['nominee'] = $this->frObj->getUserNominees($this->userSess->account_id);		
		//$data['kycfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.kyc_document_upload',['pan'=>'ABCDE1234Q','tax'=>'12ABCDE1234Q1Z1'],['pan_no','tax_no']);
		/* $data['kycfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.kyc_document_upload');	
		$data['fields'] = CommonNotifSettings::getHTMLValidation('aff.settings.bank-details');			
		$data['otp_vfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.securitypin.forgototp.verify');			
		$data['pin_sfields'] = CommonNotifSettings::getHTMLValidation('aff.settings.securitypin.save');			*/
	
		$data['fields'] = CommonNotifSettings::getHTMLValidation('fr.settings.bank-details');		
		$data['bank_account_details'] = $this->frObj->GetBankAccountDetails(['account_id'=>$this->userSess->account_id]);
		$data['kycfields'] = CommonNotifSettings::getHTMLValidation('fr.settings.kyc_document_upload');	
		 $prooftypes = $this->config->get('constants.KYC_DOCUMENT_TYPE');	
	    foreach($prooftypes as $k=>$v){
		       $data['kyc_document'][$v] = $this->frObj->getKycDocument(array(
                    'account_id'=>$this->userSess->account_id,
                    'prooftype'=>$v));
		} 	
		return view('franchisee.settings.profile',$data);		
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
		$this->session->set('changeEmailSess', $ses);				
		
		$token = $this->session->getId().'.'.$ses['hash_code'];			
		$data = ['email_verify_link'=>route('aff.settings.changeemail.verification',['token'=>$token])];
				
		//CommonNotifSettings::affNotify('affiliate.account.settings.change_email_verification', $this->userSess->account_id, 0, $data,true,false);	
		$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
		$op['msg'] = trans('franchisee/settings/change_email.check_email_inbox',['email'=>$this->commonstObj->maskEmail($this->userSess->email)]);
		$op['link'] = $data['email_verify_link'];		
		
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }
	
	/* Email Update */
	public function sendEmailVerificationOTP ()
    {   
		$new_email = '';
		$op = array();
        $postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');  
		if (!empty($postdata))
        { 	
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
					
			CommonNotifSettings::affNotify('affiliate.account.settings.change_email_verification_otp', $this->userSess->account_id, 0, $data,true,false);	
			$op['status'] = $this->statusCode =  $this->config->get('httperr.SUCCESS');;
			$op['msg'] = trans('affiliate/settings/change_email.check_email_for_otp',['email'=>$new_email]);
			$op['code'] = $ses['code'];		
			$op['token'] = $token;
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
					$updateRes = $this->frObj->update_user_email($verifySess['account_id'], $verifySess['new_email']);
					if ($updateRes){   
						$this->session->forget('changeEmailSess');
						$res['email']= $verifySess['new_email'];	
						$res['reset_session']='';					
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
			$op = $this->frObj->update_uname($this->userSess->account_id,$postdata['new_uname']);
			$this->statusCode = $op['status'];
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
	}	
	
	/* Mobile Number Update */
    public function sendUpdate_mobileVerification ()
    {   
		$new_mobile = '';
		$op = array();
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		
		if (!empty($postdata))
        {
			$rules =  [            
				'mobile' => 'required|numeric|digits_between:10,10|unique:'.$this->config->get('tables.ACCOUNT_MST'),
			];
			$messages = [
			  'mobile.required' => trans('affiliate/validator/change_mobile_js.mobile'), 
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
			$sess_key = md5($cur_mobile.$this->account_id);
			$verification_code = rand(111111, 999999);

			$this->session->set($sess_key, array(
				'sess_key1'=>$sess_key,
				'new_mobile'=>$new_mobile,
				'account_id'=>$this->account_id,
				'verify_code'=>$verification_code));
			$res = $this->session->get('userdata');
			$res['reset_session'] = $sess_key;
			$user_session = $this->session->set('userdata',$res);
		    try{
              CommonLib::notify(null, 'mobile_verifycode', ['code'=>$verification_code], [ 'mobile'=>$new_mobile]);			
				/* $res=$this->smsObj->send_sms(['reset_code'=>$verification_code,'phonecode'=>$this->userSess->phonecode,'mobile'=>$new_mobile,'site_name'=>$this->siteConfig->site_name],$this->config->get('sms_service.MOBILEUPDATE_RESETCODE')); */
				$op['status'] = 'ok';
				$op['verification_code'] = $verification_code;
				$op['msg'] = trans('affiliate/settings/change_mobile.check_mobile_inbox',['mobile'=>$new_mobile]); 	
            }catch(Exception $e) {
				$op['status'] = 'error';
				$op['msg'] = trans('affiliate/settings/change_mobile.error_msg');
			}			
		}
        return $this->response->json($op); 
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
			$sess_key = $res['reset_session'];
			$verifySess = $this->session->get($sess_key);
		} 
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
        $postdata = $this->request->all();;
		$postdata['email']=$this->userSess->email;
		
        if ($checkType == 1)
        { 	 
            if ($verifySess['verify_code'] == $postdata['verify_code'])
            {	
				$updateRes = $this->frObj->update_user_mobile($verifySess['account_id'], $verifySess['new_mobile']);
				
				if ($updateRes)
				{  
				   // $data = array('siteConfig'=>$this->siteConfig);
				    $htmls = View('emails.affiliate.account.settings.update_mobile')->render();
			        $mstatus = TWMailer::send(array(
					'to'=> $postdata['email'],
					'subject'=>trans('affiliate/settings/change_mobile.mobile_notify'),
					 'view'=> $htmls,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
                   ); 
					
					$res['mobile']=$verifySess['new_mobile']; 	 
					$res['reset_session']='';	
					$this->session->set('userdata',$res);
					return $this->response->json(['status'=>200,'msg'=>trans('affiliate/settings/change_mobile.update_mobile_success')]);
				}                
            }
            else
            {
			    return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_mobile.mobile_req_expiry')]);
            }
        }
        else
        {
            return App::abort('404');
        }
    } 
	
	public function profile_info() {
		
		$op = $data = [];
		$data['user_code'] = $this->userSess->user_code;
		$data['full_name'] = $this->userSess->full_name;
		$data['gardian'] = $this->userSess->gardian;
		$data['marital'] = $this->userSess->marital_status;		
		$data['marital_status'] = $this->frObj->get_marital_status();		
		$data['sfields'] = CommonNotifSettings::getHTMLValidation('fr.settings.update_profile');
		$op['template'] = view('franchisee.settings.profile_update',$data)->render();
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
			$res = $this->frObj->updateProfile($postdata);
			if($res){
				$this->userSess->gardian = trim($postdata['gardian']);
				$this->userSess->marital_status = $postdata['marital_status'];
				$this->session->set($this->sessionName, (array) $this->userSess);				
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
	        $data = $this->frObj->update_password($this->userSess->account_id, $postdata);	
			if(!empty($data['status']) == $this->config->get('httperr.SUCCESS')){
			
			    $this->userSess->pass_key = md5($this->request->newpassword);
                $this->session->set($this->sessionName, $this->userSess);
				$mdata = array(
					'full_name'=>$this->userSess->full_name,
					'uname'=>$this->userSess->uname,				
					'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
					'client_ip'=>$this->request->ip(true));
					$email_data = array('email'=>$this->userSess->email);
				CommonNotifSettings::affNotify('franchisee.account.settings.change_password_resetnotify', $this->userSess->account_id, 0, $mdata,true,false);
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
			 $oldpassword = $postdata['oldpassword'];
			 $status = $this->frObj->password_check($oldpassword, $this->userSess->account_id);	
	  switch($status){
		  case 1:
				$op['msg'] = '';
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			break;
			case 2:
				$op['error'] = ['oldpassword'=>trans('franchisee/settings/changepwd.incorrect_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
			default:
				$op['error'] = ['oldpassword'=>trans('franchisee/settings/changepwd.old_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
        }	
		
	}
	
	/* public function securitypin_verify ()
    {
		$postdata = $this->request->all();
        $oldpassword = $postdata['oldpassword'];
        $op = $this->frObj->tran_password_check($oldpassword, $this->userSess->account_id);		
        echo json_encode($op);
		//return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
    } */
	public function securitypin_create(){
		
		if (empty($this->userSess->trans_pass_key))
        {
            $data = [];
            $data['security_pin'] = $this->request->new_security_pin;
            $data['account_id'] = $this->userSess->account_id;
			
            if ($this->frObj->saveProfilePIN($data))
            {
                $this->userSess->has_pin = true;
                $this->session->set($this->sessionName, (array) $this->userSess);
               $mdata = [
						'full_name'=>$this->userSess->full_name,
						'uname'=>$this->userSess->uname,				
						'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
						'site_name' => $this->pagesettings->site_name];					
				    CommonNotifSettings::affNotify('franchisee.account.settings.create_securitypin', $this->userSess->account_id, 0, $mdata,true,false);					
					$op['msg'] = trans('franchisee/settings/security_pwd_js.profile_pin_save_success');
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
	public function securitypin_verify ()
    {
		$op = [];
		$postdata = $this->request->all();
        $oldpassword = $postdata['tran_oldpassword'];
        $status = $this->frObj->tran_password_check($oldpassword, $this->userSess->account_id);
		switch($status){
			case 1:
				$op['msg'] = '';
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			break;
			case 2:
				$op['error'] = ['tran_oldpassword'=>trans('franchisee/settings/security_pwd.incrct_trans_pwd')];
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			break;
			default:
				$op['error'] = ['tran_oldpassword'=>trans('franchisee/settings/security_pwd.empty_trans_pwd')];
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
			$result 	= $this->frObj->tran_update_password($this->userSess->account_id, $postdata);
			$op = json_decode($result,true);
			if($op['status'] == $this->config->get('httperr.SUCCESS'))
			{
			    $this->userSess->trans_pass_key = md5($this->request->tran_newpassword);
                $this->session->set($this->sessionName, $this->userSess);
				$data = [
					'full_name'=>$this->userSess->full_name,
					'uname'=>$this->userSess->uname,				
					'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
					'client_ip'=>$this->request->ip(true)];					
				CommonNotifSettings::affNotify('franchisee.account.settings.securitypwd_resetnotify', $this->userSess->account_id, 0, $data,true,false);				
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
		$data['email'] = $this->userSess->email;
		$data['uname'] = $this->userSess->uname;
		$data['full_name'] = $this->userSess->full_name;
		$data['code'] = rand(100000, 999999);
		$data['site_name'] = $this->pagesettings->site_name;
		$data['account_id'] = $this->userSess->account_id;
		$this->session->set('resetProfilePin', $data);
		$activation_key = md5($this->userSess->account_id.date('ymshis'));
		$saveRes = $this->frObj->update_account_activationkey($this->userSess->account_id,['activation_key'=>$activation_key]);
		if($saveRes){
 			CommonNotifSettings::affNotify('franchisee.account.settings.reset_security_pwd', $this->userSess->account_id, 0, $data,true,false);			
			//$op['code'] = $data['code'];			
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('franchisee/settings/security_pwd.forgot_pin_otp',['email'=>maskEmail($data['email'])]);		
		  }
		  else {
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
			$check_profilepin=$this->frObj->profilepin_check($this->userSess->account_id);			 			
			if ($check_profilepin->trans_pass_key != md5($data['security_pin']))
			{
				$data['account_id'] = $this->userSess->account_id;
				if ($this->frObj->saveProfilePIN($data))
				{ 			       	
			        $this->userSess->trans_pass_key = md5($this->request->tran_newpassword);
                    $this->session->set($this->sessionName, $this->userSess);
					$this->session->forget('resetProfilePin');	
					$mdata = [
						'full_name'=>$this->userSess->full_name,
						'uname'=>$this->userSess->uname,				
						'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
						'site_name' => $this->pagesettings->site_name];					
					
				    CommonNotifSettings::affNotify('franchisee.account.settings.create_securitypin', $this->userSess->account_id, 0, $mdata,true,false);					
					$op['msg'] = trans('franchisee\settings\security_pwd_js.profile_pin_save_success');
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
				$op['msg'] = trans('franchisee\settings\security_pwd_js.sameas_current_profile_pin');
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');				
			} 
        }
        else
        {			
            $op['msg'] = trans('franchisee/account.not_accessable');
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
                $op['msg'] = trans('franchisee/account.invalid_otp');
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else{
		    $op['status_class']='alert-danger';
            $op['msg'] = trans('franchisee/account.not_accessable');
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
	 
	public function Bank_Details ()
    {		   
		$data = $op = [];		
		$postdata = $this->request->all(); 		
		$postdata['payment_setings']['beneficiary_name'] = trim($postdata['payment_setings']['beneficiary_name']);
		$postdata['account_id'] = $this->userSess->account_id;    
		$postdata['currency_id'] = $this->userSess->currency_id; 
		$postdata['account_type'] = $this->userSess->account_type_id; 		
		$res = $this->frObj->UpdateBankDetails($postdata);					
		if ($res) 
		{		       
			/* $this->frObj->UpdateCompletedSteps(['current_step'=>$this->config->get('constants.ACCOUNT_CREATION_STEPS.BANK_DETAILS'),
												'account_type_id'=>$this->config->get('constants.ACCOUNT_TYPE.SELLER'),
												'supplier_id'=>$this->supplier_id,
												'account_id'=>$this->account_id]); */ 														
			$op['msg'] = 'Account Updated Successfully';					
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
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
				/* else
				{   
			        if(in_array()){
						$op[$docCode]['msg'] = trans('affiliate/account_controller.select_valid_file');
						$op[$docCode]['status'] = false;	
					}
				}		 */
			}
			if(!empty($kycDoc)){
		       $upload = $this->frObj->kyc_document_upload($kycDoc,$this->userSess);		
		    }
			if (!empty($upload))
			{	
				foreach($upload as $k=>$result)
				{	
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
				$op['kyc_status'] = $this->frObj->getKycStatus($this->userSess->account_id);
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
		//print_r($op);exit;
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	}
	
	/* NEW */
	Public function logoimage_withcrop_save ()
    {
		/* print_R($this->userSess); die; */
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $attachment = $postdata['attachment'];
        $filename = '';
        $folder_path = getGTZ(null, 'Y').'/'.getGTZ(null, 'm').'/';
        $path = $this->config->get('constants.FRANCHISEE.LOGO.PATH');
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
            if ($this->frObj->logo_image_upload($postdata))
            {
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				
                $op['msg'] = trans('franchisee/account/profile.profile_image_updated');				
				$op['logo_path'] = $this->userSess->franchisee_logo =asset($this->config->get('constants.FRANCHISEE.LOGO.PATH').$postdata['docpath']);
				if($this->userSess->has_logo_img==1){		
					File::delete($this->config->get('constants.FRANCHISEE.LOGO.PATH').$this->userSess->logo_imagename);		
                    $this->userSess->logo_imagename =asset($this->config->get('constants.FRANCHISEE.LOGO.PATH').$postdata['docpath']); 				
				}
                $this->config->set('app.accountInfo', $this->userSess);				
                $this->session->set($this->sessionName, $this->userSess);
				/* print_R($this->userSess); die; */
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/account.no_changes');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	public function getAddress($type='personal') {

		$data = [];
		$data['address_type'] = $type;
		if($data['address_type']=='personal'){
			 $data['addresstype'] = $this->frObj->getUserAddr($this->userSess->account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		  }
		  if($data['address_type']=='franchisee'){
			$data['addresstype'] = $this->frObj->getUserAddr($this->userSess->franchisee_id,$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		  }
		$data['country_id'] = $this->userSess->country_id;
		$op['template'] = view('franchisee.settings.address_update',$data)->render();
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	public function saveAddress($type=''){
		$op = [];
		$addArr = ['personal'=>$this->config->get('constants.ADDRESS_TYPE.PRIMARY'),'franchisee'=>$this->config->get('constants.ADDRESS_TYPE.PRIMARY')];
		$postdata = $this->request->all();
		$address_type=$type;
		$type = !empty($type)? $type:$this->config->get('constants.ADDRESS_TYPE.PRIMARY');
		if($postdata)
		{		
          if($address_type=='personal'){
			 $sdata['relative_post_id'] = $this->userSess->account_id;
			 $sdata['post_type'] =$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
		   }
		  else if($address_type=='franchisee'){
			   $sdata['relative_post_id'] = $this->userSess->franchisee_id;
			   $sdata['post_type'] =$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'); 	
		   } 
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
			$op = $this->frObj->updateAddress($sdata);	
			$this->statusCode = $op['status'];
			$op['addtype'] = $type;
			return \Response::json($op, $this->statusCode, $this->headers, $this->options);
			
		}
	}
	
		
}