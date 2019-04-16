<?php
namespace App\Http\Controllers\ecom;
use App\Http\Controllers\ecomBaseController;
//use App\Http\Controllers\ecomBaseController;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use guzzle;

class ProductController extends ecomBaseController
{
    public function __construct ()
    {
        parent::__construct();
        //$this->myAccountObj = new MyAccount($this->commonObj);
    }
	
	public function productList ()
	{ 
	    $data = [];		
		$qrystr = strstr($this->request->fullurl(),'?',true);		
		$res = guzzle::getResponse('api/v1/shopping/products/list'.$qrystr, 'POST', [], []);	     
		
		if(!empty($res)){		
			if(isset($res->status)){
				if($res->status == $this->config->get('httperr.TEMPORARY_REDIRECT')){	
					$this->session->forget('userdata');	
					$this->config->set('app.accountInfo',null);
					$op['msg'] = $res->msg;						
				}
			}					
		}
        return view('ecom.product.list', $data);
    }
	
	
	/* Login */
	public function login ()
    {	
        $data = array();
        $data['url'] = '';    
        if (empty($this->account_id) && !isset($this->account_id))
        { 			        
			$data['lfields'] = CommonNotifSettings::getHTMLValidation('ecom.login');			
			$data['fpfields'] = CommonNotifSettings::getHTMLValidation('ecom.forgot_pwd');	        		
			$data['rpfields'] = CommonNotifSettings::getHTMLValidation('ecom.reset_pwd');
            return view('ecom.login', $data);
        }
        else
        {  
            // If Session Exist Redirect to Home Page           
            return $this->redirect->route('ecom.home');         
        }
    } 
	
	/* Logout */	
	public function logout()
    { 
        $op = $postdata = [];	
		//$postdata['account_log_id'] = $this->userSess->account_log_id;
		if ($this->session->has('userdata'))
        {
			$res = guzzle::getResponse('api/v1/user/logout', 'POST', [], []);			
			if(!empty($res)){		
				if(isset($res->status)){
					if($res->status == $this->config->get('httperr.TEMPORARY_REDIRECT')){	
                       	$this->session->forget('userdata');	
		                $this->config->set('app.accountInfo',null);
						$op['msg'] = $res->msg;						
					}
				}					
			}							
		}		
		$op['url'] = route('ecom.login');
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
    }	
	
	/* Check Login */
	public function checklogin ()
    {	
		$op = [];
		$op['msg'] = 'Something went wrong.';
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');	
		$postdata = $this->request->all();			
		if(!empty($postdata))
		{	
			$res = guzzle::getResponse('api/v1/user/login', 'POST', [], $postdata);
			if(!empty($res)){		
				if(isset($res->status)){
					if($res->status == $this->config->get('httperr.SUCCESS')){		
						$this->session->put($this->sessionName, $res);	
						$device_log =$this->config->get('device_log');
						$device_log->token = $res->token;						
						$this->config->set('device_log',$device_log);
						$op['has_pin'] = $res->has_pin;					
						$op['token'] =  $res->token;					
						$op['account_id'] = $res->account_id;
						$op['full_name'] =  $res->full_name;
						$op['first_name']=$res->first_name;
						$op['last_name']=$res->last_name;					
						$op['uname'] =  $res->uname;
						$op['is_merchant'] = 0;	
						$op['user_code'] = $res->user_code;          
						$op['account_type'] =  $res->account_type;
						$op['account_type_name'] = $res->account_type_name;       					
						$op['mobile'] = $res->mobile;
						$op['email'] = $res->email;
						$op['gender'] = $res->gender;
						$op['dob'] = $res->dob;
						$op['language_id'] =  $res->language_id;	
						$op['currency_id'] = $res->currency_id;   
						$op['currency_code'] =  $res->currency_code;							
						$op['country_flag'] =  $res->country_flag;	
						$op['is_mobile_verified'] = $res->is_mobile_verified;          
						$op['is_email_verified'] = $res->is_email_verified;          
						$op['is_affiliate'] = $res->is_affiliate;     
						if(isset($res->can_sponser)) {
							$op['can_sponser'] = $res->can_sponser;
						}
						$op['account_log_id'] = $res->account_log_id; 					
						$op['profile_img'] = $res->profile_img;     					
						$op['is_verified'] = $res->is_verified;     					                       
						$op['country'] = $res->country;     					                       
						$op['country_code'] = $res->country_code;     					                       
						$op['country_id'] = $res->country_id;     					                       
						$op['phone_code'] = $res->phone_code;     					                       
						$op['has_pin'] = $res->has_pin;    					                       
						$op['is_guest'] = $res->is_guest;     					                       
						$op['toggle_app_lock'] = $res->toggle_app_lock;	
					}				
					$op['msg'] = $res->msg;
					$op['status'] = $this->statusCode = $res->status;					
				}elseif(isset($res->error)){	
				    $op['error'] = $res->error;
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
					unset($op['msg']);
				}					
			}	
		}	   		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
	}
	
	/* Forgot password */
	public function forgot_password ()
    {
		$op = [];
		$op['msg'] = 'Something went wrong.';
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');	
		$postdata = $this->request->all();		
		if(!empty($postdata)){
			$res = guzzle::getResponse('api/v1/user/forgot-pwd', 'POST', [], $postdata);	
			if(!empty($res)){				
				if(isset($res->status)){								
					$op['code'] = $res->code;
					$op['token'] = $res->token;
					$op['msg'] = $res->msg;
					$op['status'] = $this->statusCode = $res->status;
				}elseif(isset($res->error)){	
				    $op['error'] = $res->error;
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
					unset($op['msg']);
				}					
			}		
		}		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
    }
	
	/* Reset password */
	public function reset_pwd ()
    {
		$op = [];
		$op['msg'] = 'Something went wrong.';
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');	
		$header['token'] = $this->request->header('token');			
		$postdata = $this->request->all();	
		if(!empty($postdata)){			
			$res = guzzle::getResponse('api/v1/user/reset-pwd', 'POST', $header, $postdata);					
			if(!empty($res)){		
				if(isset($res->status)){								
					$op['msg'] = $res->msg;
					$op['status'] = $this->statusCode = $res->status;
				}elseif(isset($res->error)){	
				    $op['error'] = $res->error;
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
					unset($op['msg']);
				}					
			}
		}		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
    }
	
	public function profile ()
    {		
        $data = array();
		$data['gender'] = trans('ecom/account.gender');    
		$data['pufields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update');	
        return view('ecom.account.profile_update', $data);      
    } 
	
	/* Update Profile  */
	public function updateProfile ()
	{		
        $op = [];
        $postdata = $this->request->all();		
        $postdata['account_id'] = $this->userSess->account_id;	  
        if (!empty($postdata))
        {  
			$res = guzzle::getResponse('api/v1/user/profile-settings/profile/update', 'POST', [], $postdata);	
			if (!empty($res))
            {
				if(isset($res->status))
				{		
                    if($res->status == $this->config->get('httperr.SUCCESS'))
					{						
						$op['first_name'] = $this->userSess->first_name = $postdata['first_name'];					
						$op['last_name'] = $this->userSess->last_name = $postdata['last_name'];						
						$op['gender'] = $this->userSess->gender = $postdata['gender'];						
						$op['dob'] = $this->userSess->dob = $postdata['dob'];						
						$this->userSess->uname= $postdata['display_name'];
						$this->session->set($this->sessionName, $this->userSess);
						$this->config->set('app.accountInfo', $this->userSess);
						$this->config->set('data.user', $this->userSess);
					}
					$op['msg'] = $res->msg;
					$op['status'] = $this->statusCode = $res->status;
				}elseif(isset($res->error)){	
				    $op['error'] = $res->error;
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
				}
            }
            else
            {
                $op['msg'] = trans('ecom/account.edit_profile.no_changes');
				$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            }
        }
        else
        {
            $op['msg'] = trans('ecom/account.edit_profile.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
       return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
    }	
	
	public function changepassword ()
    {		
        $data = array();
		$data['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update-pwd');
        return view('ecom.account.change_pwd', $data);
    } 
	
	public function updatepwd ()
    {
        $op = [];
        $postdata = $this->request->all();				
        $postdata['account_id'] = $this->userSess->account_id;	  
        if ($this->userSess->pass_key == md5($this->request->current_password))
        {
            if ($this->userSess->pass_key != md5($this->request->conf_password))
            {                 
                if ($res = guzzle::getResponse('api/v1/user/change-pwd', 'POST', [], $postdata))
                {   			
                    if(isset($res->status))
					{		
						if($res->status == $this->config->get('httperr.SUCCESS'))
						{	
							$this->userSess->pass_key = md5($this->request->password);
							$this->session->set($this->sessionName, $this->userSess);
						 	$this->config->set('app.accountInfo', $this->userSess);
						    $this->config->set('data.user', $this->userSess);
						}
						$op['msg'] = $res->msg;
						$op['status'] = $this->statusCode = $res->status;
					}elseif(isset($res->error)){	
						$op['error'] = $res->error;
						$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
					}                               
                }
                else
                {                    
                    $op['msg'] = trans('ecom/account.changepwd.savepwd_unable');
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('ecom/account.changepwd.newpwd_same');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('ecom/account.changepwd.curr_pwd_incorrect');
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	   
}
