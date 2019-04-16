<?php
namespace App\Http\Controllers\Affiliate;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use App\Models\MemberAuth;
use App\Helpers\CommonNotifSettings;

class AffAuthcontroller extends AffBaseController
{
    
    public function __construct ()
    {
        parent::__construct();
		$this->affObj = new AffModel();
    }	
	
	public function login_check() {
		$postdata = $this->request->all(); 
		$op = array();
		$messages = [
		  'uname.required' => 'Please enter your Account Id / Email ID', // custom message for required rule.
		  'uname.idOremail' => 'Invalide Account Id / Email ID', // custom message for email rule.
		  'password.min' => 'The Password must be at least 6 characters',
		];
		$rules =  [            
            'uname' => 'required|idOremail',
            'password' => 'required|min:6',
        ];
		
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {	
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['error'][$key] =  $validator->errors()->first($key);			
			}
			$op['status'] = $this->status_code = $this->config->get('httperr.PARAMS_MISSING');
			return $this->response->json($op, $this->status_code, $this->headers, $this->options);
		}
		
        $op = array();
        $op['status'] = 'fail';
        $op['msg'] = 'Incorrect Username or Password';
		$postdata = $this->request->all();        
        $validate = '';
        if (!empty($postdata['uname']) && !empty($postdata['password'])) {            
            $validate = $this->affObj->account_validate($postdata);			
            if ($validate['status']==1) {     
				$acInfo = $this->session->get($this->sessionName);
				$op['msg'] = $validate['msg'];
				if(\Session::has('a_go_to')) {
					$op['url'] =  \Session::get('a_go_to');
				}
				else if($acInfo->can_sponsor==$this->config->get('constants.OFF')){
					$op['url'] =  route('aff.referrals.refer-and-earn');
				}
				else if($validate){
					$op['url'] =  route('aff.dashboard');
				}				
				$op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');				
            } 
			else {                
                $op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = $validate['msg'];
            }
        } else {
			$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = 'Account Id (or) Password should not be empty';
		}
		return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }

    public function logout ()
    {
        $op = [];
        if ($this->session->has('userdata'))
        {
            $this->session->forget('userdata');
        }
		$this->session->regenerate();
        $op['url'] = route('aff.login');
		return $this->response->json($op,200);
    }
	
	public function forgotpwd()
    {
        $op = array();
        $wdata = $this->request->all(); 
		$usrData = $this->affObj->user_name_check(['email'=>$wdata['uname_id']]);
		if (!empty($usrData))
		{
			if (($usrData->login_block != 1))
			{
				$verify_code =  rand(111111, 999999);
				$usrData->verify_code = $verify_code; 
				$resetKey = md5($verify_code);				
				$usrData->time_out = date('Y-m-d H:i:s', strtotime('+5 minutes')); /* otp valid for 5 minutes   */
				$time_out = $usrData->time_out = getGTZ($usrData->time_out, 'Y-m-d H:i:s');					
				$this->session->set('forgotpwd', [$resetKey=>$usrData]);					
				$token = $this->session->getId().'.'.$resetKey;
				$forgotpwd_link = route('aff.pwdreset-link',['token'=>$token]);
				CommonNotifSettings::affNotify('affiliate.FORGOT_PASSWORD',$usrData->account_id, $this->config->get('constants.ACCOUNT_TYPE.USER'),['sitename'=>$this->siteConfig->site_name, 'code'=>$verify_code, 'forgotpwd_link'=>$forgotpwd_link], true, false);					
				//$op['link'] = $forgotpwd_link;
				$op['msg'] = trans('user/auth.forgotpwd.acc_resetlink', ['email'=>maskEmail($usrData->email)]);
				$op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');
			}
			else
			{                     
				$op['msg'] = trans('user/auth.forgotpwd.acc_blocked');
				$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
			}
		}
		else
		{
			$data = [];
			if (isset($wdata['email']))
			{
				$data['email'] = $wdata['email'];
			}                
			$op['msg'] = trans('user/auth.forgotpwd.acc_notfound');
			$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
		}	
        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }
	
	public function verifyForgotpwdLink ($token)
    {
        $op = $data = $usrdata = [];		
        if (!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);
			
            if ($this->session->has('forgotpwd'))
            {
                $usrdata = $this->session->get('forgotpwd');
                $access_key = array_keys($usrdata);			
				if (!empty($access_key) && ($access_key[0] == $access_token[1]))
              //if (!empty($access_key) && ($access_key[0] == $access_token[1]) && ($usrdata[$access_key[0]]->time_out >= getGTZ()))
                {
                    $data['token'] = $access_token[1];
                    $data['pwd_resetfrm'] = true;
                }
                else
                {
                    $data['pwd_resetfrm'] = false;
                    $data['msg'] = trans('affiliate/account.forgotpwd_session_expire');
                }
            }
            else
            {
                $data['pwd_resetfrm'] = false;
                $data['msg'] = trans('affiliate/account.forgotpwd_session_expire');
            }
        }
        else
        {
            $data['pwd_resetfrm'] = false;
            $data['msg'] = trans('affiliate/account.forgotpwd_session_expire');
        }		
        return view('affiliate.account.reset_pwd', $data);
    }
	
	
	public function recoverpwd()
    {
        $data = array();
        $postdata = $this->request->all();  
        $op = array();

		$messages = [
		  'usrtoken.required' => 'Please enter your Member ID / Email ID',
		  'usrtoken.min' => 'Invalide Token1',
		  'usrtoken.max' => 'Invalide Token2',
		  'usrtoken.regex' => 'Invalide Token3',		  
		];
		$rules =  [            
            'usrtoken' => 'required|min:32|max:32|regex:/^[\w]*$/',
        ];
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {
			$data['errmsg'] = \Lang::get('affiliate/forgotpwd.validate_msg.token_invalid');
		}
		else {
			$usrdata = $this->session->get('forgotpwd');
			$res = $this->affObj->check_pwdreset_token($postdata);			
			if(!empty($res)){
				$data['usrtoken'] = $postdata['usrtoken'];
			}		
		}	
		return view('affiliate.recoverpwd',$data);
    }
	
	
	public function update_newpwd()
    {
        $data = array();
        $postdata = $this->request->all();  
        $op = array();
		$messages = [
		  'token.required' => 'Please enter your Member ID / Email ID',
		  'token.min' => 'Invalide Token1',
		  'token.max' => 'Invalide Token2',
		  'token.regex' => 'Invalide Token3',
		  'newpassword.required' => 'It should not be empty',
		  'newpassword.min' => 'It must be min 6 letters long',
		  'confirmpassword.required' => 'It should not be empty',
		  'confirmpassword.min' => 'It must be min 6 letters long',
		  'confirmpassword.same' => 'New Password and Confirm Password do not match',
		];
		$rules =  [            
            'token' => 'required|min:32|max:32|regex:/^[\w]*$/',
			'newpassword' => 'required|min:6',
			'confirmpassword' => 'required|min:6|same:newpassword'
        ];
		
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['error'][$key] =  $validator->errors()->first($key);			
				}
			$op['status'] = $this->statusCode = 400;
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		
		}
		else {
			$usrdata = $this->session->get('forgotpwd');
						$usrdata = $usrdata[$postdata['token']];
			$upStatus = $this->affObj->update_password($usrdata->account_id,$postdata);		
			$op['msg'] = $upStatus['msg'];			
			if($upStatus['status']==200){
				session()->flush();
				$mdata = array(
					'full_name'=>$usrdata->full_name,
					'uname'=>$usrdata->uname,				
					'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
					'client_ip'=>$this->request->ip(true));
				 CommonNotifSettings::affNotify('affiliate.account.settings.change_password_resetnotify',$usrdata->account_id,$this->config->get('constants.ACCOUNT_TYPE.USER'),$mdata,true,false);
				$op['status'] = 200;
				$op['msg'] = $upStatus['msg'];
			}		
			else {
				$op['status'] = 422;
				$op['msg'] = $upStatus['msg'];
			}
			return $this->response->json($op,$op['status']);
		}		
    }
	
	public function signup_accheck(){
		$data = array();
        $postdata = $this->request->all();  
        $op = array();
		$messages = $rules = [];
		$email = $mobile = 0;		
		if(isset($postdata['login_id'])) {		
			if(!empty($postdata['login_id'])){			
				if(strpos($postdata['login_id'],'@')>0){
					$messages = [					  
					  'login_id.email' => 'Please enter an valid Email Id',
					];
					$rules =  [            
						'login_id' => 'required|email',
					];
					$email = $postdata['login_id'];
				} 
				else if(is_numeric($postdata['login_id'])){
					$messages = [						
						'login_id.regex' => 'Please enter an valid Mobile Number',
					];
					$rules =  [
						'login_id' => 'required|regex:/^[0-9]{10}$/',
					];
					$mobile = $postdata['login_id'];
				}
				else {
					$op['error']['login_id'] =  ['Please enter your Email / Mobile Number'];
					$this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
				}
				
				if(empty($op['error'])){
					$validator = Validator::make($postdata, $rules,$messages);
					
					if ($validator->fails()) {
						$ers = $validator->errors();
						foreach($rules  as $key=>$formats){
							$op['error'][$key] =  $validator->errors()->first($key);			
						}
						$this->statusCode = $this->config->get('httperr.PARAMS_MISSING');		
					}
					else {
						$op = $this->affObj->signup_acverify($postdata['login_id']);
						if(!empty($op) && $op['exist']==0){
    						if(!empty($email)){
    							$op['acfld'] = ['fld'=>'email','fldval'=>$email];							
    						} else if(!empty($mobile)){
    							$op['acfld'] = ['fld'=>'email','fldval'=>$email];
    						}
    						 $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						}
						else {
						   $op['msg'] =  "You already have an account with that email";
                           $op['msgclass'] =  "warning";
                           $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						}
					}
				}				
			} else {				
				$rules =  ['login_id' => 'required'];
				$messages = [ 'login_id.required' =>  'Please enter your Email / Mobile Number'];			
				$validator = Validator::make($postdata, $rules,$messages);
				if ($validator->fails()) {
					$ers = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['error'][$key] =  $validator->errors()->first($key);			
					}
					$this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
				}				
			}		
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
  public function signup_acverify(){
		$data = array();
        $postdata = $this->request->all();  
        $op = array();
		$messages = $rules = [];
		if($this->session->has('regSess')){			
			$messages = [					  
			  'acpwd.required' => 'Please enter your password',
			  'acpwd.min' => 'Password must contain atleast 6 char long',
			  'acpwd.max' => 'Password must contain atmost 16 char long',
			  'acpwd.password' => "Please enter a valid password."
			];
			$rules =  [            
				'acpwd' => 'required|min:6|max:16|password',
			];
			$validator = Validator::make($postdata, $rules,$messages);
			if ($validator->fails()) {
				$ers = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['error'][$key] =  $validator->errors()->first($key);			
				}
				$this->statusCode = 400;
			} else {
				$acinfo = $this->session->get('regSess');
				if($acinfo->pass_key==md5($postdata['acpwd'])){
					$op['acinfo'] = $acinfo;
					$op['status'] = $this->statusCode = 200;
				} 
				else {
					$op['error']['acpwd'] =  ['Incorrect Password'];
					$this->statusCode = 401;
				}
			}	
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
}