<?php
namespace App\Http\Controllers\EcomV2;

use App\Http\Controllers\ecomBaseController;
use App\Models\ecom\AccountModel;
//use App\Http\Controllers\ecomBaseController;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\Commonsettings;
use App\Models\LocationModel;
use guzzle;
use Cart;
use Fpdf;
class AccountController extends ecomBaseController
{
    public function __construct()
    {
        parent::__construct();
		
        $this->lcObj = new LocationModel(); 

        $this->accObj = new AccountModel();
        $this->commonSettingObj = new Commonsettings();
        //$this->myAccountObj = new MyAccount($this->commonObj);
    }

    public function home()
    {  
        $data = [];
        /*  if (Cookie::has('referral_code'))
         {
             $data['referral_code'] = Cookie::get('referral_code');
         } */
        return view('shopping.home', $data);
    }
	
	public function new_home()
    {
        $data = [];
        return view('shopping.home', $data);
    }

    /* Login */
    public function login()
    {

        $data = array();
        $data['url'] = '';

        if (empty($this->account_id) && !isset($this->account_id)) {

            $data['lfields'] = CommonNotifSettings::getHTMLValidation('ecom.login');
            $data['fpfields'] = CommonNotifSettings::getHTMLValidation('ecom.forgot_pwd');
            $data['rpfields'] = CommonNotifSettings::getHTMLValidation('ecom.reset_pwd');
            return view('shopping.login', $data);
        } else {
            // If Session Exist Redirect to Home Page           
            return $this->redirect->route('ecom.home');
        }
    }
    public function sign_up()
    {            
        $data=[];
       // $data['fpfields'] = CommonNotifSettings::getHTMLValidation('ecom.sign_up_save');
       //$data['country']     = $this->commonSettingObj->getCountryList();
       $data['ip_country'] = $this->commonObj->getIpCountry('192.168.1.15'); 
                       
            
       $data['countries']= $this->lcObj->getCountries(['operate'=>true]);
       
   
       foreach ($data['countries'] as $key => $value) {
          $data['countries'][$key]->img_url=asset('resources/assets/imgs/flags')."/".mb_strtolower($value->iso2).".png";
          
       }
       return view('shopping.sign_up',$data);
    }
	
    
		public function sign_up_save()
		{
			$postdata = $this->request->all();
			unset($postdata['temsandcondition']);
			$res = guzzle::getResponse($this->config->get('services.api.url').'signup', 'POST', [], $postdata);

			if(isset($res->status))
			{
				$result = guzzle::getResponse($this->config->get('services.api.url').'signup-code-resend', 'POST', ['regtoken'=>$res->regtoken], $postdata);
				if($result->status==200){ 
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = "OTP Send To Your Mobile Number"; 
				$op['code'] = $result->code;
				$op['regtoken'] = $result->regtoken;

				} 
			} 
			else if(isset($res->error)){
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['error'] = $res->error;
				$op['msg']="something went wrong"; 
				return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
			} 
			else
			{
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = "Something Went Wrong....";
			}
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}

    public function signup_varification()
    {
        $op['msg'] = 'Invalid OTP';
        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();
        $regtoken = $this->request->header('regtoken');
        
        $res = guzzle::getResponse($this->config->get('services.api.url').'confirm-signup', 'POST',['regtoken'=>$regtoken],$postdata);
        
     
       /*  if($res->status==200){
            $op['data'] = $res;
            $op['msg'] = 'Virob account successfully created.';
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        } */				
		if (!empty($res)) {
			if (isset($res->status)) {
				if ($res->status == $this->config->get('httperr.SUCCESS')) {
              					
					$this->session->put($this->sessionName, $res);
					$device_log = $this->config->get('device_log');
					$device_log->token = $res->token;
					$this->config->set('device_log', $device_log);
					$op['has_pin'] = $res->has_pin;
					$op['token'] = $res->token;
					$op['account_id'] = $res->account_id;
					$op['full_name'] = $res->full_name;
					$op['first_name'] = $res->first_name;
					$op['last_name'] = $res->last_name;
					$op['uname'] = $res->uname;
					$op['is_merchant'] = 0;
					$op['user_code'] = $res->user_code;
					$op['account_type'] = $res->account_type;
					$op['account_type_name'] = $res->account_type_name;
					$op['mobile'] = $res->mobile;
					$op['email'] = $res->email;
					$op['gender'] = $res->gender;
					$op['dob'] = $res->dob;
					$op['language_id'] = $res->language_id;
					$op['currency_id'] = $res->currency_id;
					$op['currency_code'] = $res->currency_code;
					$op['country_flag'] = $res->country_flag;
					$op['is_mobile_verified'] = $res->is_mobile_verified;
					$op['is_email_verified'] = $res->is_email_verified;
					$op['is_affiliate'] = $res->is_affiliate;
					if (isset($res->can_sponser)) {
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
					$op['url'] =route('ecom.login');
				}
				//$op['msg'] = $res->msg;
				$op['msg'] = 'Virob account successfully created.';
				$op['status'] = $this->statusCode = $res->status;
			}elseif (isset($res->error)) {
				$op['error'] = $res->error;
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
				unset($op['msg']);
			}
		}else{
			$op['msg'] = 'Something went wrong!';
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
    public function sign_up_resend_otp()
    { 
        $postdata = $this->request->all();
        $op['msg'] = "Something Went Wrong...";

        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $regtoken = $this->request->header('regtoken');
        $result = guzzle::getResponse($this->config->get('services.api.url').'signup-code-resend', 'POST', ['regtoken'=>$regtoken],$postdata);
       
        if($result->status==200)
        {
             $op['code'] = $result->code;
             $op['regtoken'] = $result->regtoken;
             $op['msg'] = "OTP Send To Your Mobile Number";
             $op['status'] =  $this->statusCode = $this->config->get('httperr.SUCCESS');

        }
       
         return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        
    }


    /* Logout */
    public function logout()
    {        
        $op = $postdata = [];
        //$postdata['account_log_id'] = $this->userSess->account_log_id;
	
        if ($this->session->has('userdata')) {
	        $res = guzzle::getResponse($this->config->get('services.api.url').'user/logout', 'POST', [], []);
		    if (!empty($res)) {

                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.TEMPORARY_REDIRECT')) {
                        $this->session->forget('userdata');
                        $this->config->set('app.accountInfo', null);
                        $op['msg'] = $res->msg;
                    }
                }
            }
        }
        $op['url'] = route('ecom.new_home');
        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }   

    public function checklogin()
    {
        $op = [];
        $op['msg'] = 'Something went wrong.';
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();
        // print_r($this->config->get('services.api.url'));
        if (!empty($postdata)) {
        
            $res = guzzle::getResponse($this->config->get('services.api.url').'login', 'POST', [], $postdata);
            
            if(!empty($res)) {
                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.SUCCESS')) {						
                        $this->session->put($this->sessionName, $res);
                        $device_log = $this->config->get('device_log');
                        $device_log->token = $res->token;
                        $this->config->set('device_log', $device_log);
                        $op['has_pin'] = $res->has_pin;
                        $op['token'] = $res->token;
                        $op['account_id'] = $res->account_id;
                        $op['full_name'] = $res->full_name;
                        $op['first_name'] = $res->first_name;
                        $op['last_name'] = $res->last_name;
                        $op['uname'] = $res->uname;
                        $op['is_merchant'] = 0;
                        $op['user_code'] = $res->user_code;
                        $op['account_type'] = $res->account_type_id;
                        $op['account_type_name'] = $res->account_type_name;
                        $op['mobile'] = $res->mobile;
                        $op['email'] = $res->email;
                        $op['gender'] = $res->gender;
                        $op['dob'] = $res->dob;
                        $op['language_id'] = $res->locale_id;
                        $op['currency_id'] = $res->currency_id;
                        $op['currency_code'] = $res->currency_code;
                        $op['country_flag'] = $res->country_flag;
                        $op['is_mobile_verified'] = $res->is_mobile_verified;
                        $op['is_email_verified'] = $res->is_email_verified;
                        $op['is_affiliate'] = $res->is_affiliate;
                        if (isset($res->can_sponser)) {
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
                        if(Cart::instance('ecomCart')->count()>0)
                        {
                             $op['url'] =route('ecom.product.cart-items-view');
                        } 
                        else
                        {
                            $op['url'] =url('');
                            
                        }
                    }
                    $op['msg'] = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    unset($op['msg']);
                }
            }
        }  
            
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* Forgot password */
    public function forgot_password()
    {		
        $op = [];
        $op['msg'] = 'Something went wrong.';
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all(); 	
        if (!empty($postdata)){		    
            $res = guzzle::getResponse($this->config->get('services.api.url').'forgot-pwd', 'POST', [], $postdata);     
            if (!empty($res)) {						
                if (isset($res->status)) {	  		
				    if($this->config->get('app.env') == 'local'){
					    $op['link'] = route('ecom.resetpwd-link',['token'=>$res->token]);
					}
                    $op['msg']  = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;											
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    unset($op['msg']);
                }
            }
        }		
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);      
    }
	
	/* Forgot Pwd Verification Link */
    public function verifyForgotpwdLink ($token)
    {
        $op = $data = $usrdata = [];				
        $data['msg'] = trans('general.session_expire');
		$data['token'] = '';
        $data['pwd_resetfrm'] = false;		
        $data['status'] = $this->config->get('httperr.UN_PROCESSABLE');		
        if (!empty($token) && strpos($token, '.'))
		{			
	        $res = guzzle::getResponse($this->config->get('services.api.url').'resetpwd-link/'.$token, 'POST', [], []);  
			if (!empty($res)) {		
				if($res->status == $this->config->get('httperr.SUCCESS')){				
			    	$restoken = Decrypt_Reg_Token($token);
                    $access_token = explode('.', $restoken);					
				    $data['pwd_resetfrm'] = true;
					$data['token'] = $access_token[1];		
					$data['email'] = $res->email;		
					$data['full_name'] = $res->full_name;	
				}				
				$data['msg']  = $res->msg;
				$data['status'] = $res->status;	
            }
        }       
		$data['rpfields'] = CommonNotifSettings::getHTMLValidation('ecom.reset_pwd');					
		return view('shopping.account.forgot_pwd', (array) $data);      
    }
	
	/* Reset password */
	public function reset_pwd()
    {
        $op = [];
        $op['msg'] = 'Something went wrong.';
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $header['token'] = $this->request->header('token');
        $postdata = $this->request->all();						
        if(!empty($postdata)) {
		    $res = guzzle::getResponse($this->config->get('services.api.url').'reset-password', 'POST', $header, $postdata); 
            if (!empty($res)) {
                if (isset($res->status)) {   
					if($res->status == $this->config->get('httperr.SUCCESS')){
						$op['url'] = route('ecom.new_home');
					}
                    $op['msg'] = $res->msg;					
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    unset($op['msg']);
                }
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	

    public function profile()
    {
        $data = array();
        $data['gender'] = trans('ecom/account.gender');
        $data['pufields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update');
        return view('shopping.account.profile_update', $data);
    }

    /* Update Profile  */
    public function updateProfile()
    {
        $op = $multipart =[];
        $postdata = $this->request->all();				
        $postdata['account_id'] = $this->userSess->account_id;		
        if (!empty($postdata)) {		
          /*   print_r($postdata);exit;   
			$multipart = $postdata['attachment'];	 */	
            //print_r($postdata);exit;    					
	        //$res = guzzle::getResponse('api/v1/user/profile-settings/profile/update', 'POST', [], $postdata, $multipart);
	        $res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/profile/update', 'POST', [], $postdata);				
			
            if (!empty($res)) {
                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.SUCCESS')) {
                        $op['first_name'] = $this->userSess->first_name = $postdata['first_name'];
                        $op['last_name'] = $this->userSess->last_name = $postdata['last_name'];
                        $op['gender'] = $this->userSess->gender = $postdata['gender'];
                        $op['dob'] = $this->userSess->dob = $postdata['dob'];
						if(isset($res->profile_img) && !empty($res->profile_img)){
							$op['profile_img'] = $this->userSess->profile_img = $res->profile_img;
						}
                        $this->userSess->uname = $postdata['display_name'];
                        $this->session->set($this->sessionName, $this->userSess);
                        $this->config->set('app.accountInfo', $this->userSess);
                        $this->config->set('data.user', $this->userSess);
                    }
                    $op['msg'] = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                }
            } else {
                $op['msg'] = trans('ecom/account.edit_profile.no_changes');
                $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            }
        } else {
            $op['msg'] = trans('ecom/account.edit_profile.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* New Profile Image Upload */
	/* Public function profile_image_upload ()
    {       
        $op = array();
		$postdata = $this->request->all();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $attachment = $postdata['attachment'];
        $filename = '';
        $folder_path = getGTZ(null, 'Y').'/'.getGTZ(null, 'm').'/';
        $path = $this->config->get('constants.ACCOUNT.PROFILE_IMG.LOCAL');
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
        $file_name = $file_name.'.'.$ext;
        $filename = getGTZ(null, 'dmYHis').$file_name;
        if ($attachment->move($path.$folder_path, $filename))
        {
            $postdata['filename'] = $filename;
            $postdata['docpath'] = $folder_path.$filename;
            if ($this->accountObj->profile_image_upload($postdata))
            {
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('user/account.profile_image_update');
                $op['profile'] = $this->userSess->profile = asset($this->config->get('constants.ACCOUNT.PROFILE_IMG.WEB.160x160').$postdata['docpath']);
                $this->config->set('app.accountInfo', $this->userSess);
                $this->session->set($this->sessionName, (array) $this->userSess);
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('general.no_changes');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->header, $this->options);
    } */

    public function changepassword()
    {
        $data = array();
        $data['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update-pwd');
        return view('shopping.account.change_pwd', $data);
    }

    public function updatepwd()
    {
        $op = [];
        $postdata = $this->request->all();
        $postdata['account_id'] = $this->userSess->account_id;				
        if ($this->userSess->pass_key == md5($this->request->current_password)) {
            if ($this->userSess->pass_key != md5($this->request->conf_password)) {
                if ($res = guzzle::getResponse($this->config->get('services.api.url').'change-pwd', 'POST', [], $postdata)) {
                    if (isset($res->status)) {
                        if ($res->status == $this->config->get('httperr.SUCCESS')) {
                            $this->userSess->pass_key = md5($this->request->password);
                            $this->session->set($this->sessionName, $this->userSess);
                            $this->config->set('app.accountInfo', $this->userSess);
                            $this->config->set('data.user', $this->userSess);
                        }
                        $op['msg'] = $res->msg;
                        $op['status'] = $this->statusCode = $res->status;
                    } elseif (isset($res->error)) {
                        $op['error'] = $res->error;
                        $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    }
                } else {
                    $op['msg'] = trans('ecom/account.changepwd.savepwd_unable');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            } else {
                $op['msg'] = trans('ecom/account.changepwd.newpwd_same');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        } else {
            $op['msg'] = trans('ecom/account.changepwd.curr_pwd_incorrect');
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* contact-us  */
    public function contact_us()
    {
        $data = array();
        return view('shopping.Contact_us', $data);
    }

    /* Update contact-us  */
   public function update_contact_us()
    {        
        $op = [];
        $op['msg'] = trans('ecom.account.something_wrong');
        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

        $postdata = $this->request->all();


        if (!empty($postdata)) {
          
            $res = guzzle::getResponse($this->config->get('services.api.url').'contact-us-email', 'POST', [], $postdata);

            
           if(!empty($res) && isset($res->status)){

				if($res->status==200)
				{    

					 $result= $this->accObj->save_contactus($postdata);

					 if($result)
						{
							$op['msg'] = trans('ecom/account.contact_sumbit_success');
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						}
					   
				}

            }

        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* Change Email */
   public function change_email()
    {

         $data['userinfo'] = $this->userSess;
        // return view('shopping.account.change_email', $data);

        $a = view('shopping.account.change_email', $data)->render();
        $op['content'] = $a;
        $op['status'] = $this->statusCode = 200;
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);


    }

    public function current_email_notify()
    {
        $new_email = '';
        $postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        // print_r($postdata);exit;
        if (!empty($postdata)) {
            $new_email = $postdata['email'];
        }
        $postdata['email'] = $this->userSess->email;
        $ses = [
            'new_email' => $new_email,
            'account_id' => $this->userSess->account_id,
            'code' => '',
            'hash_code' => ''];

        $data = array_merge($ses, ['email' => $new_email]);
        $ses['code'] = rand(100000, 999999);
        $ses['token'] = md5($ses['code']);
        $this->session->set('chang_email_sess', $ses);

        $postdata['link'] = route('ecom.change-email.varify_link', ['token' => $ses['token']]);
        $postdata['partner_site_name'] = 'virob';
        $res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/change-email/send-verification', 'POST', [], $postdata);

        if (!empty($res)) {

            $op['link'] = $postdata['link'];

            $op['msg'] = trans('ecom/account.check_email');
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }

        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);


    }

    public function varify_link($token = '')
    {
        $data['verification'] = false;
        $data['pufields'] = CommonNotifSettings::getHTMLValidation('ecom.account.new_email_notify');
        if ($this->session->has('chang_email_sess')) {
            $email_sess = $this->session->get('chang_email_sess');

			if ($email_sess['token'] == $token) { 
				$data['verification'] = true;

				   $postdata 		= $this->request->all();
					$op['msg'] 		= trans('affiliate/general.something_wrong');
					$op['status'] 	= trans('affiliate/general.error');  
					 $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$ses = [
							'new_email'=>'',
							'account_id'=>$this->userSess->account_id,
							'code'=>'',
							'hash_code'=>'']; 			
					
					$ses['code'] 	  = rand(100000, 999999);
					$ses['token']	  = md5($ses['code']);		
					$this->session->set('new_email_sess', $ses);
		 

			}
        }
        return view('shopping.account.change_to_newemail', $data);

    }


    public function new_email_notify()
    {

        $postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $ses = [
            'new_email' => $postdata['email'],
            'account_id' => $this->userSess->account_id,
            'code' => '',
            'hash_code' => ''];

        $ses['code'] = rand(100000, 999999);
        $ses['token'] = md5($ses['code']);
        $this->session->set('new_email_sess', $ses);


        $postdata['link'] = route('ecom.change-email.varify_new_link', ['token' => $ses['token']]);
        $postdata['partner_site_name'] = 'virob';

        $res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/change-email/send-verification', 'POST', [], $postdata);

        if (!empty($res)) {
            $op['link'] = $postdata['link'];
            $op['msg'] = trans('ecom/account.check_email');
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }

        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);


    }

    public function varify_new_link($token = '')
    {
        // $postdata = $this->request->all();
        $data['verification'] = false;
        if ($this->session->has('new_email_sess')) {
            $email_sess = $this->session->get('new_email_sess');

            if ($email_sess['token'] == $token) {
                $data = [];
                $data['acc_id'] = $email_sess['account_id'];
                $data['email'] = $email_sess['new_email'];
                $result=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/change-email/email-table-update', 'POST', [], $data);                              
                if ($result->status=200) {
                    $user_details = $this->userSess;
                    $user_details->email = $data['email'];
                    $this->session->set('userdata', $user_details);
                    $op['msg'] = "Email updated successfully";
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                } else {
                    $op['msg'] = " Sorry something went wrong!!!!";
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

                }
            }
        } else {
            $op['msg'] = "Token missing";
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

        }

        $data['rs'] = $op;

        return view('shopping.account.change_email_showmsg', $data);

    }
/*  change email */

/* bank details */

public function bank_detail()
 {
       $data['acc_id']=$this->userSess->account_id;
       $result=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/get-payout-list', 'POST', [], $data);
      
       $res['user_info']='';
       $res['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update_bank_detail');
       // echo "<pre>"; print_r($res);exit;
          
      if($result->status==200)
             {  

                 $demo=array();
                 $arr= $result->contents;
                 array_walk($arr,function(&$res) {
                       $res->user_info = json_decode($res->payment_settings,true);
                       $res->user_info['id'] = $res->id;
                       $res->user_info['status'] = $res->status;
                       unset($res->payment_settings);
                       unset($res->id);
                       $res = (Object)$res->user_info;
                      
                });

                $res['user_info']=$arr;
               //print_R($res['user_info']);exit;
                $this->session->set('bank_status', 'update');
                $a = view('shopping.account.bank_detail', $res)->render();
                $op['content']=$a;
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                // $op['bank_status']=$this->session->get('bank_status');
                return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
     }
     else
     {          
                 $a = view('shopping.account.bank_detail', $res)->render();
                 $op['content']=$a;
                 $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                 // $op['bank_status']=$this->session->get('bank_status');
                 return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
     }    
  }
  public function setbanksession()
  {

     $this->session->set('bank_status', 'add');
     $op['msg']="success";
     $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
      return $this->response->json($op, $this->statusCode, $this->headers, $this->options);

  }





 public function relogin_bank()
 {

    $postdata= $this->request->all();
    if(!empty($postdata)){
       $this->session->set('bank_row_id',$postdata['row_id']); 
    }
     

     $data = array();
     $data['user_info'] = $this->userSess;          
     $a = view('shopping.account.relogin_acc_bank', $data)->render();
     $op['content']=$a;
     $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');


     return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    
 }

 public function check_relogin_bank()
 {
    $postdata=$this->request->all();
    $pass=md5($postdata['password']);
     if($pass==$this->userSess->pass_key)
        {
                  $op['msg']='login  successfully';
                  $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');                         
        }
     else{                 
                  $op['msg']='somethig went wrong';
                  $op['status'] =  $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');              
        }
    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);

    
 }

    public function send_otp_bank()
    {

        // $data['mobile']=$this->userSess->mobile; 
        $data['mobile']=8138024495;
        $ses = [
            'mobile' =>$data['mobile'],
            'account_id' => $this->userSess->account_id,
            'code' => '',
            'hash_code' => ''];

        $ses['code'] = rand(100000, 999999);
        $ses['token'] = md5($ses['code']);
        $this->session->set('OTP_bank', $ses);

        $data['hash_code'] =$ses['token'];
        $data['code'] =$ses['code'];
        $data['site_name'] = 'virob';
        // print_r($data);

        //print_r($this->config->get('services.api.url').'user/profile-settings/bank/send-otp-bank');exit;
        $res=$result=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/send-otp-bank', 'POST', [], $data);
        // print_r($res);

        if(!empty($res) && isset($res->status))
        {
            if($res->status==200){

                $op['msg']='OTP Sent To Your Mobile Number';
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['code']=$data['code'];

            }
            else{
                $op['msg']='somethig went wrong';
                $op['status'] =  $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg']='something went wrong';
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

        }

        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);


    }
 public function varify_otp_bank()
 {
      $postdata=$this->request->all();
      $otp=$postdata['otp'];
      $OTP_bank_sess = $this->session->get('OTP_bank');
      $otp_sess=$OTP_bank_sess['code'];
      
  
    if($otp == $otp_sess)
    {
                  $op['msg']='OTP varified successfully';
                  $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');   

    }
    else
    {
            $op['msg']='Mismatch in OTP';
            $op['status'] =  $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

    }
    
return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
 }

//both add and update have  same function( add_bank_details())
  public function add_bank_detail()
 {
     $postdata['row_id']=$this->session->get('bank_row_id');
    
     $data = array();
     $data['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.save_bank_detail');
     $bank_status=$this->session->get('bank_status');

    if($bank_status=='add')
    {
        $a = view('shopping.account.add_bank_detail', $data)->render();
        $op['content']=$a;
        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
    }
    else
    {
        $postdata['row_id']=$this->session->get('bank_row_id');
        $result=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/get-bank-details', 'POST', [], $postdata);
        $data['id']=$result->id;
        $data['user_info']=$result->details;
        $a = view('shopping.account.update_bank_detail', $data)->render();
        $op['content']=$a;
        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');

    }
   
     return $this->response->json($op, $this->statusCode, $this->headers, $this->options);

 }
  public function save_bank_detail()
  {
   // die('dsds')
     // print_r($this->request->all());exit;


      $postdata=$this->request->all();           
      
       $data=array();
       $data['account_id']=$this->userSess->account_id;
       $data['currency_id']=$this->userSess->account_id;
       $data['acc_holder_name']=$postdata['acc_holder_name'];
       $data['acc_number']=$postdata['acc_number'];
       $data['ifsc_code']=$postdata['ifsc_code'];

     
      
   
      $res=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/bank-add-details', 'POST', [], $data);  
       

     if ($res->status==200) {
                    
                    $op['msg'] = trans('ecom/account.bank_added');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                    } else {
                    $op['msg'] = trans('ecom/account.failed');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

                }
                 return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
  }

 public function update_bank_detail()
 {
    
      $postdata=$this->request->all();           
       $data=array();
       $data['account_id']=$this->userSess->account_id;
       $data['currency_id']=$this->userSess->account_id;
       $data['acc_holder_name']=$postdata['acc_holder_name'];
       $data['acc_number']=$postdata['acc_number'];
       $data['ifsc_code']=$postdata['ifsc_code'];
       $data['row_id']=$postdata['row_id'];

    
     $res=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/update-bank-details', 'POST', [], $data);
              if ($res->status==200) {
                    
                    $op['msg'] = trans('ecom/account.bank_updated');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                    } 
                    else {
                    $op['msg'] = trans('ecom/account.failed');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

                }
   return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
     
 }
 public function find_ifsc()
 {  

        $postdata=$this->request->all();
     
        $postdata['country_id']=$this->userSess->country_id;
        $postdata['account_id']=$this->userSess->account_id;
         // echo"<pre>";  print_r($postdata);exit;

    $res=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/find-ifsc', 'POST', [], $postdata);
   
    if($res->status==200)
    {
        //print_r("hjdfahjhj");exit;
     $op['msg'] = "IFSC Code Successfully Varified";
     $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');   
       
    }
    else
    {
        //print_r("else");exit;

      $op['msg'] = "Please Check Your IFSC Code";
      $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');  

    }
    
    
     return $this->response->json($op, $this->statusCode, $this->headers, $this->options);


 }
 public function change_status()
 {
   $postdata=$this->request->all();
   $postdata['account_id']=$this->userSess->account_id;

    $res=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/change-status', 'POST', [], $postdata);
    if($res->status==200)
        {
            if($postdata['status']==0)
            {
                 $op['msg'] ="Deactivated successfully";
            }
            else
            {
                 $op['msg'] ="Activated successfully";
            }
           
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
       else
       {
         $op['msg'] = "Something went wrong";
         $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

       }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);

   

 }

 public function remove_bank()
 {
    $postdata=$this->request->all();
     $res=guzzle::getResponse($this->config->get('services.api.url').'profile-settings/bank/remove-bank', 'POST', [], $postdata);
     if($res->status==200)
        {
              $op['msg'] = "Bank details removed";
              $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');

        }
       else{
              $op['msg'] = "Something went wrong";
              $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

        }
         return $this->response->json($op, $this->statusCode, $this->headers, $this->options);



 }

  /*bank details */




    /* Change Mobile */

    public function changemobile()
    {
        $data = array();
        // return view('shopping.account.change_mobile', $data);
        $a = view('shopping.account.change_mobile', $data)->render();
        $op['content'] = $a;
        $op['status'] = $this->statusCode = 200;
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changemobilesend()
    {
        $op = [];
        $token = $this->session->getId() . md5(rand('000000', '999999'));
        $data['link'] = url('verify_link/' . $token);
        $res = guzzle::getResponse($this->config->get('services.api.url').'change-mobile-request', 'POST', [], $data);
        if (isset($res->status)) {
            $op['msg'] = $res->msg;
            $op['status'] = $this->statusCode = $res->status;
            $op['token_data'] = $token;
            $this->session->set('session_token', $token);
            $user_details = [
                'account_id' => $this->userSess->account_id,
                'mobile' => $this->userSess->mobile,
                'phone_code' => $this->userSess->phone_code

            ];
            $this->session->set('user_change_mobile', $user_details);

        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function verify_link($token)
    {
        if ($this->session->has('session_token')) {
            if ($token == $this->session->get('session_token')) {
                $data = array();
                $data['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update-mobile');
                $data['otpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.otp-validation');
                if ($this->session->has('user_change_mobile')) {
                    $session_userarray = $this->session->get('user_change_mobile');
                    $data['log_data'] = $session_userarray;
                }
                return view('shopping.account.change_mobile_form', $data);
            }
        }
    }

    public function updatemobile()
    {
        $op = [];
        $postdata = $this->request->all();
        $session_userarray = $this->session->get('user_change_mobile');
        $postdata['account_id'] = $session_userarray['account_id'];
        //echo'<pre>';print_r($session_userarray);die();
        $this->session->forget('updatemobSess');
        if (!empty($postdata)) {
            $res = guzzle::getResponse($this->config->get('services.api.url').'change-mobile-update', 'POST', [], $postdata);
            if (!empty($res)) {
                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.SUCCESS')) {
                        //$this->session->set('otp_for_mobile_change',$res->code);
                        $change_password = [
                            'otp_code' => $res->code,
                            'new_number' => $postdata['new_mobile']
                        ];

                        $this->session->set('updatemobSess', $change_password);
                        //echo'<pre>';print_r($this->sessionName);die();
                        $op['msg'] = $res->msg;
                        $op['status'] = $this->statusCode = $res->status;
                        $op['otp'] = $res->code;
                    }

                }
            } else {
                $op['msg'] = trans('ecom/account.change_mobile_error');
                $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
    } 
	
    public function otp_validation()
    {
        $op = [];
        $otp = $this->request->mobile_otp;
        if ($this->session->has('updatemobSess')) {
            $session_array = $this->session->get('updatemobSess');
            if ($otp == $session_array['otp_code']) {
                $postdata['new_mobile'] = $session_array['new_number'];
                $session_userarray = $this->session->get('user_change_mobile');
                $postdata['account_id'] = $session_userarray['account_id'];
                if (!empty($postdata)) {
                    $res = guzzle::getResponse($this->config->get('services.api.url').'change-mobile-save', 'POST', [], $postdata);
                    if (!empty($res)) {
                        if (isset($res->status)) {
                            if ($res->status == $this->config->get('httperr.SUCCESS')) {

                                $this->session->forget('updatemobSess');
                                $this->session->forget('session_token');
                                $this->session->forget('user_change_mobile');
                                if ($this->userSess) {
                                    $this->userSess->mobile = $postdata['new_mobile'];
                                    $this->session->set($this->sessionName, $this->userSess);
                                    $this->config->set('app.accountInfo', $this->userSess);
                                    $this->config->set('data.user', $this->userSess);
                                }
                                $op['msg'] = $res->msg;
                                $op['status'] = $this->statusCode = $res->status;
                            }
                        }
                    } else {
                        $op['msg'] = trans('ecom/account.change_mobile_wrong');
                        $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
                    }
                }

            } else {
                $op['msg'] = trans('ecom/account.change_mobile_otpwrong');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
    }
	
	/* Get Address */
	/* public function getAddress ($type='billing')
    {	
	    //print_r($this->userSess->token);exit;
	
        $data = []; 
		$data['address_type'] = $type;	
		$header['token'] = $this->request->header('token');		
		//$header['usrtoken'] = $this->userSess->token;
		$res = guzzle::getResponse('api/v1/user/profile-settings/'.$type.'-address', 'POST', $header, []);
		if(!empty($res)){		
			if(isset($res->status)){								
				$data['address'] = $res->address;
				$op['status'] = $this->statusCode = $res->status;
			}			
	   }
	   $data['country'] = $this->userSess->country;		
	   $data['country_id'] = $this->userSess->country_id;
	   return view('shopping.account.address', $data);     
    } */
	
	/* Address */
	public function checkPincode ()
    {
		$op = [];
		$op['msg'] = 'Something went wrong.';
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');	
		$header['token'] = $this->request->header('token');			
		$postdata = $this->request->all();
        if(!empty($postdata)){
			$res = guzzle::getResponse($this->config->get('services.api.url').'check-pincode', 'POST', $header, $postdata);	
            if(!empty($res)){
				if(isset($res->status)){								
					$op['data'] = $res->data;
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
	
	/* New Get Address */
	public function getAddress() 
    {  
	    $header['token'] = $this->request->header('token');			
		$header['usrtoken'] = $this->userSess->token;		
	    if ($this->request->ajax())         
        {     
		    $op = [];
			$data['address'] ='';
		    $postdata = $this->request->all();		
			$data['address_type'] = (isset($postdata['address_type']) && !empty($postdata['address_type'])) ? $postdata['address_type']:'';		
            $data['add_address'] = (isset($postdata['type']) && !empty($postdata['type'])) ? $postdata['type']:'';			
			if(!empty($postdata['address_type'])){
				$res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/get-address', 'POST', $header, $postdata);	 
				if(!empty($res)){		
					if(isset($res->status)){
						if(!empty($res->address) && $res->status == $this->config->get('httperr.SUCCESS')){
							$data['address'] = $res->address;					    
							//$data['address_type'] = $postdata['address_type'];					    
							//$op['status'] = $this->statusCode = $res->status;					    
						}									
					}			
				}		
            }	
			$data['adfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.save-address');
			$op['template'] = view('shopping.account.address_form',$data)->render();
	        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');	
		    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}else
        {
		    $data = [];
			$data['billingAddr'] = $data['shippingAddr'] ='';
		    $postdata['address_type'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
			$res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/get-address', 'POST', $header, $postdata);    
			if(!empty($res)){				
				if(isset($res->status)){
                    if(!empty($res->address) && $res->status == $this->config->get('httperr.SUCCESS')){
						$data['billingAddr'] = $res->address;					    
					}									
				}		
			}			
			$postdata['address_type'] = $this->config->get('constants.ADDRESS_TYPE.SHIPPING');
			$res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/get-address', 'POST', $header, $postdata);	      
			if(!empty($res)){		
				if(isset($res->status)){
                    if(!empty($res->address) && $res->status == $this->config->get('httperr.SUCCESS')){
						$data['shippingAddr'] = $res->address;					    
					}									
				}			
			} 
	   		$data['country'] = $this->userSess->country;		
	        $data['country_id'] = $this->userSess->country_id;	
			$data['adfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.save-address');
			/* $data['billingAddr'] = '';
			$data['shippingAddr'] =''; */
			//echo"<pre>"; print_r($data);exit;
            return view('shopping.account.address',$data);  
		}
	}
	
    /******** ambika ************/
    public function get_address()    
	{       
    	$data = [];	
		$header['token'] = $this->request->header('token');          
		$header['usrtoken'] = $this->userSess->token;	
		$data['billingAddr'] = $data['shippingAddr'] ='';
		$postdata['address_type'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
		$res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/get-address', 'POST', $header, $postdata);    
		if(!empty($res)){               
			if(isset($res->status)){
				if(!empty($res->address) && $res->status == $this->config->get('httperr.SUCCESS')){
					$data['billingAddr'] = $res->address;                       
				}                                   
			}       
		}           
		$postdata['address_type'] = $this->config->get('constants.ADDRESS_TYPE.SHIPPING');
		$res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/get-address', 'POST', $header, $postdata);       
		if(!empty($res)){       
			if(isset($res->status)){
				if(!empty($res->address) && $res->status == $this->config->get('httperr.SUCCESS')){
					$data['shippingAddr'] = $res->address;                      
				}                                   
			}           
		} 
		$data['country'] = $this->userSess->country;         
		$data['country_id'] = $this->userSess->country_id;     
	    $data['adfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.save-address');
        //echo"<pre>";print_r($data);exit;        
		// $a = view('shopping.account.address',$data)->render();		
		$op['msg']='success';
		$op['status']=$this->statusCode=$this->config->get('httperr.SUCCESS');
		$op['data']=view('shopping.account.address',$data)->render();
	    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);     
    }    
	
	/* Update Address */
	public function saveAddress ()
    {
		$op = [];
		$op['msg'] = 'Something went wrong.';
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');	
		$header['token'] = $this->request->header('token');

		$postdata = $this->request->all();
		//print_r($postdata);exit;
		if(!empty($postdata)){
			$res = guzzle::getResponse($this->config->get('services.api.url').'profile-settings/save-address', 'POST', $header, $postdata);
			if(!empty($res)){
				if(isset($res->status)){	
					$op['address_id'] = $res->address_id;
					$op['address_type_id'] = $res->address_type_id;
					$op['addtype'] = $res->addtype;
					$op['address'] = $res->address;					
					$op['status'] = $this->statusCode = $res->status;
					$op['msg'] = $res->msg;					
				}elseif(isset($res->error)){	
				    $op['error'] = $res->error;
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');				
				}					
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
    } 

    public function account_security()
    {
      $data['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update-pwd');
      return view('shopping.account.account_security',$data);
    }
    public function my_orders()
    {
        $data =[];
        return view('shopping.account.my_order', $data);
    }
	
    public function my_orders_search()
    {
        $op = $postdata = [];
		$header['usrtoken'] = $this->request->header('usrtoken');	
		$postdata = $this->request->all();
        /* $postdata['phrase'] = !empty($this->request->phrase) ? $this->request->phrase : '';
        $postdata['from'] = !empty($this->request->from_date) ? $this->request->from_date : '';
        $postdata['to'] = !empty($this->request->to_date) ? $this->request->to_date : ''; 
		$res = guzzle::getResponse('api/v1/user/my-orders/all', 'POST', [], $postdata);*/

        $res = guzzle::getResponse($this->config->get('services.api.url').'order/my-orders', 'POST', $header, $postdata);

       
        array_walk($res->data,function(&$res) {
			array_walk($res->actions,function(&$result, $key) {
				if($key == 'view'){
					$result->url = route('ecom.account.my-orders-details',['order_code'=>$result->data->order_code]);
				}elseif($key == 'cancel'){
					$result->url = route('ecom.account.my-orders-cancel',['order_code'=>$result->data->order_code, 'status'=>$result->data->status]);
				}
			});
		});
        if(!empty($res)){
            $op = $res;
            $this->statusCode=$this->config->get('httperr.SUCCESS');
        }				
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
    public function my_orders_details($order_code)
    {
       

        if($order_code){
            $res = guzzle::getResponse($this->config->get('services.api.url').'shopping/order/my-orders/'.$order_code, 'POST', [], []);
           // echo "<pre>"; print_r($res);exit;
          // print_r($res);exit;
            $demo =[];
            if($res){
                $demo['order_details']= $res;
                $demo['status']=$this->statusCode= $this->config->get('httperr.SUCCESS');
            }
         
            return $this->response->json($demo, $this->statusCode, $this->headers, $this->options);
        }

    }
	
    public function my_orders_cancel($order_code,$status)
    {
		$res = guzzle::getResponse($this->config->get('services.api.url').'shopping/order/my-orders-change-status/'.$order_code.'/'.$status, 'POST', [], []);
       // echo'<pre>';print_r($res);die();
        if(!empty($res)){
		    $op['msg']=$res->msg;
			$op['status']=$this->statusCode = $res->status;
		}else{            
		    $op['msg']= 'Something went wrong!';
			$op['status']= $this->config->get('httperr.UN_PROCESSABLE');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
    public function my_orders_pdf(){
        $pdf = new Fpdf();
        $pdf::AddPage();
        $pdf::SetFont('Arial','B',18);
        $pdf::SetTextColor(55 , 55, 55);
        $pdf::Cell(0,10,"Title",0,"","C");
        $pdf::Ln();
        $pdf::Ln();
        $pdf::SetFont('Arial','B',12);
        $pdf::cell(25,8,"ID",1,"","C");
        $pdf::cell(45,8,"Name",1,"","L");
        $pdf::cell(35,8,"Address",1,"","L");
        $pdf::Ln();
        $pdf::SetFont("Arial","",10);
        $pdf::cell(25,8,"1",1,"","C");
        $pdf::cell(45,8,"John",1,"","L");
        $pdf::cell(35,8,"New York",1,"","L");
        $pdf::Ln();
        $pdf::Output();
        exit;
    }
    public function order_ratings_feedbacks()
    {
        $op=[];
        $postdata = $this->request->all();
        if(!empty($postdata)){
            $header['token'] = $this->request->header('token');
            $res = guzzle::getResponse($this->config->get('services.api.url').'shopping/order/submit-ratings-feedbacks', 'POST', $header, $postdata);
            if(!empty($res)){
                $op['msg']=$res->msg;
                $op['status']=$this->statusCode = $res->status;
            } else{
                $op['msg']= 'Something went wrong!';
                $op['status']= $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
}