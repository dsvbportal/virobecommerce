<?php
namespace App\Http\Controllers\Franchisee;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\FrModel;
use App\Helpers\CommonNotifSettings;

class FrAuthcontroller extends FrBaseController
{
    
    public function __construct ()
    {
        parent::__construct();
		$this->frObj = new FrModel();
    }	
	
	public function login_check() {
		$postdata = $this->request->all(); 
		$op = array();
		$messages = [
		  'uname.required' => 'Please enter your Account Id / Email ID', // custom message for required rule.
		  'uname.idOremail' => 'Invalide Account Id / Email ID', // custom message for email rule.
		  'password.min' => 'Password must contain atleast 6 letters long',
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
            $validate = $this->frObj->account_validate($postdata);			
            if ($validate['status']==1) {                
				$op['msg'] = $validate['msg'];
				$op['url'] = \Session::has('a_go_to') ? \Session::get('a_go_to') : route('fr.dashboard');
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
        if ($this->session->has($this->sessionName))
        {
            $this->session->forget($this->sessionName);
        }
        $op['url'] = route('fr.login');
		if($this->request->isMethod('post')){
			return $this->response->json($op,200);
		}
		else {
			return \Redirect::to($op['url']);
		}
		
    }
	
	public function forgotpwd()
    {
        $op = array();
        $wdata = $this->request->all(); 
		
		$usrData = $this->frObj->user_name_check(['email'=>$wdata['uname_id']]);
		if (!empty($usrData))
		{
			if(($usrData->login_block != 1))
			{
				$verify_code 		  =  rand(111111, 999999);
				$usrData->verify_code = $verify_code; 
				$resetKey 			  = md5($verify_code);				
				$usrData->time_out    = date('Y-m-d H:i:s', strtotime('+5 minutes')); /* otp valid for 5 minutes   */
				$time_out 			  = $usrData->time_out = getGTZ($usrData->time_out, 'Y-m-d H:i:s');					
				$this->session->set('forgotpwd', [$resetKey=>$usrData]);					
				$token 				  = $this->session->getId().'.'.$resetKey;
				$forgotpwd_link 	  = route('fr.pwdreset-link',['token'=>$token]);
				CommonNotifSettings::affNotify('FRANCHISEE.FORGOT_PASSWORD',$usrData->account_id, $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'),['sitename'=>$this->siteConfig->site_name, 'code'=>$verify_code, 'forgotpwd_link'=>$forgotpwd_link], true, false);					
				//$op['link'] 		  = $forgotpwd_link;
				$op['msg'] 	= trans('franchisee/auth.forgotpwd.acc_resetlink', ['email'=>maskEmail($usrData->email)]);
				$op['status'] 		  = $this->status_code = $this->config->get('httperr.SUCCESS');
			}
			else
			{                     
				$op['msg'] = trans('franchisee/auth.forgotpwd.acc_blocked');
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
			$op['msg'] = trans('franchisee/auth.forgotpwd.acc_notfound');
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
                {
                    $data['token'] = $access_token[1];
                    $data['pwd_resetfrm'] = true;
                }
                else
                {
                    $data['pwd_resetfrm'] = false;
                    $data['msg'] = trans('franchisee/account.forgotpwd_session_expire');
                }
            }
            else
            {
                $data['pwd_resetfrm'] = false;
                $data['msg'] = trans('franchisee/account.forgotpwd_session_expire');
            }
        }
        else
        {
            $data['pwd_resetfrm'] = false;
            $data['msg'] = trans('franchisee/account.forgotpwd_session_expire');
        }		
        return view('franchisee.account.reset_pwd', $data);
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
			$data['errmsg'] = \Lang::get('user/forgotpwd.validate_msg.token_invalid');
		}
		else {
			$usrdata = $this->session->get('forgotpwd');
			$res = $this->frObj->check_pwdreset_token($postdata);			
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
		];
		$rules =  [            
            'token' => 'required|min:32|max:32|regex:/^[\w]*$/',
			'newpassword' => 'required|min:6'
        ];
		$validator = Validator::make($postdata, $rules,$messages);
		if($validator->fails()) {
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['error'][$key] =  $validator->errors()->first($key);			
			}
			return $this->response->json($op,500);			
		}
		else {
			$usrdata = $this->session->get('forgotpwd');
			$usrdata = $usrdata[$postdata['token']];
			$upStatus = $this->frObj->update_password($usrdata->account_id,$postdata);		
			$op['msg'] = $upStatus['msg'];			
			if($upStatus['status']==1){
				session()->flush();
				$mdata = array(
					'full_name'=>$usrdata->full_name,
					'uname'=>$usrdata->uname,				
					'last_activity'=>getGTZ(), //date('Y-m-d H:i:s'),
					'client_ip'=>$this->request->ip(true));
				 CommonNotifSettings::affNotify('franchisee.account.settings.change_password_resetnotify',$usrdata->account_id,$this->config->get('constants.ACCOUNT_TYPE.USER'),$mdata,true,false);
				 $op['status'] = 200;
				 $op['msg'] = $upStatus['msg'];
			}		
			else {
				$op['status'] = 422;				
				$op['msg'] = $upStatus['msg'];
			}
			return $this->response->json($op,200);
		}		
    }	
}