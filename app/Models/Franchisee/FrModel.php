<?php
namespace App\Models\Franchisee;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;
use CommonLib;
class FrModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		$this->commonObj = new CommonModel;
    }
	
    public function account_validate($postdata) {

		$status = '';
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where(function($c) use($postdata){
					$c->where('uname',$postdata['uname'])
					->orWhere('email',$postdata['uname'])
					->orWhere('user_code',$postdata['uname']);
				})
				->where('is_deleted', '=', $this->config->get('constants.OFF'))
                ->where('login_block', '=', $this->config->get('constants.OFF'))
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')])
				->select(DB::Raw('account_id,user_code,pass_key,trans_pass_key,email,uname,mobile,login_block,block,is_closed,account_type_id'));
				
        $userData = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
                ->join($this->config->get('tables.FRANCHISEE_MST') . ' as fm', 'fm.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as st', 'st.account_id', '=', 'um.account_id')				
				->join($this->config->get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'um.account_type_id')
				->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'st.currency_id')			
				->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'st.country_id')			
                ->selectRaw('um.account_id,um.user_code,st.language_id,um.account_type_id,um.trans_pass_key,fm.franchisee_id,fm.franchisee_type,fm.company_name,fm.logo_path,at.account_type_name,um.is_closed,um.pass_key,um.uname,concat_ws(\' \',ud.firstname,ud.lastname) as full_name,ud.firstname,ud.lastname,um.mobile,lc.country,lc.phonecode,ud.profile_img,um.email,st.country_id,st.currency_id,cur.currency as currency_code,um.block,um.login_block,st.is_mobile_verified,st.is_email_verified,st.is_verified')
                ->addBinding($fisrtQry->getBindings())
				->first();			
        if (!empty($userData)) {
            if ($userData->is_closed == $this->config->get('tables.OFF')) {
                if ($userData->login_block == $this->config->get('tables.OFF')) {
                    if (($userData->pass_key == md5($postdata['password']))||(isset($postdata['qlogin'])) && ($postdata['qlogin'] == true)) {
                        
						if(!empty($userData->logo_path) &&  file_exists($this->config->get('constants.FRANCHISEE.LOGO.PATH').$userData->logo_path)){
							$userData->logo_imagename = $userData->logo_path;
							$userData->has_logo_img = $this->config->get('constants.ON');
							$userData->logo_path = asset($this->config->get('constants.FRANCHISEE.LOGO.PATH').$userData->logo_path);
							
						} else {
							$userData->logo_imagename = $this->config->get('constants.FRANCHISEE.LOGO.DEFAULT');
							$userData->has_logo_img = $this->config->get('constants.OFF');
							$userData->logo_path = asset($this->config->get('constants.FRANCHISEE.LOGO.LOCAL').$this->config->get('constants.FRANCHISEE.LOGO.DEFAULT')) ;
						}
						
						$sesdata = array(
                                'account_id' => $userData->account_id,
                                'uname' => $userData->uname,
								'user_code' => $userData->user_code,
								'full_name' => $userData->full_name,
								'firstname' => $userData->firstname,
								'lastname' => $userData->lastname,
                                'email' => $userData->email,                              
								'account_type_name' => $userData->account_type_name,
								'account_type_id' => $userData->account_type_id,								
								'franchisee_id' => $userData->franchisee_id,								
								'franchisee_type' => $userData->franchisee_type,
								'franchisee_name' => $userData->company_name,
								'franchisee_logo' => $userData->logo_path,
								'has_logo_img' => $userData->has_logo_img,
								'logo_imagename' => $userData->logo_imagename,								
                                'currency_id' => $userData->currency_id,
								'country' => $userData->country,
								'language_id' => $userData->language_id,
								'country_id' => $userData->country_id,
								'currency_code' => $userData->currency_code,
								'is_mobile_verified' => $userData->is_mobile_verified,
								'is_email_verified' => $userData->is_email_verified,
								'currency_code' => $userData->currency_code,                                
                                'is_verified' => $userData->is_verified,
								'block' => $userData->block,
                                'mobile' => $userData->mobile,
                                'phonecode' => $userData->phonecode,
								'has_pin' => (!empty($userData->trans_pass_key)? true:false));

                        $currentdate = date($this->config->get('constants.date_format'));
						$userData->token = $this->config->get('device_log')->token;
						$last_active = getGTZ();
						
						DB::table($this->config->get('tables.DEVICE_LOG'))
								->where('device_log_id', $this->config->get('device_log')->device_log_id)
								->update(array('account_id'=>$userData->account_id, 'status'=>$this->config->get('constants.ACTIVE'))); 
								
						$account_log_id = DB::table($this->config->get('tables.ACCOUNT_LOG'))
								   ->insertGetId(array('account_id'=>$userData->account_id, 'account_login_ip'=>request()->ip(), 'device_id'=>$this->config->get('device_log')->device_log_id, 'country_id'=>(!empty($postdata['country_id']) ? $postdata['country_id'] : $userData->country_id), 'account_log_time'=>getGTZ()));  
								
						$sesdata['account_log_id'] = $account_log_id;
						$update['token'] = md5($account_log_id);
						$sesdata['last_logged_time'] = date('h:i A d M',strtotime($last_active));						
						DB::table($this->config->get('tables.ACCOUNT_MST'))
								->where('account_id', $userData->account_id)
								->update(array('last_active'=>$last_active));
								
					    $update['token'] = request()->session()->getId().'-'.$update['token'];							
						$this->session->put($this->sessionName, (object)$sesdata);
						$token_update = DB::table($this->config->get('tables.ACCOUNT_LOG'))
									->where('account_log_id', $account_log_id)
									->update($update);  
						 
                        return ['status'=>1,'msg'=>'Your are successfully logged in'];
                    } else {
						return ['status'=>3,'msg'=>'Incorrect username or password.'];
                    }
                } else {
					return ['status'=>5,'msg'=>'Your account has been blocked. Please contact our adiminstrator'];
                }
            } else {
				return ['status'=>6,'msg'=>'Incorrect username or password..'];
            }
        } else {
			return ['status'=>2,'msg'=>'Incorrect username or password...'];
        }
        return ['status'=>7,'msg'=>'Incorrect username or password....'];
    }
	
	public function sendPwd_resetlink($postdata)
	{		
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where("email",'=',$postdata['uname'])
				->where('is_deleted', '=', $this->config->get('constants.OFF'))
                ->where('login_block', '=', $this->config->get('constants.OFF'))
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')]);
				
		$users = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
		       	->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'um.account_id')
                ->select(DB::Raw("um.account_id,um.pass_key,um.uname,ud.firstname,ud.lastname,concat_ws('',ud.firstname,ud.lastname) as full_name,ap.country_id,um.email"))
				->addBinding($fisrtQry->getBindings())
                ->first();
				
		if (!empty($users) && count($users) > 0)
		{			
			$data = array();
			$data['uname'] = $postdata['uname'];
			$data['reset_code'] = md5($users->account_id."/".date('dtyHis'));
			$data['reset_link'] = route('aff.recoverpwd').'?usrtoken='.$data['reset_code'];
			$data['full_name'] = $users->full_name;
			$data['email'] = $users->email;			
			$data['domain_name'] = $this->siteConfig->site_name;
			$email_data['email']    = $users->email;
			$email_data['siteConfig'] = $this->siteConfig;
			
			$update = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as um')
						->where('um.account_id', '=', $users->account_id)
						->update(array('pwd_reset_key'=>md5($data['reset_code']),'pwd_reset_key_sess'=>date('H:i:s',strtotime('+5 minuts'))));			
			

				 
			
			return ['status'=>1,'msg'=>\trans('affiliate/forgotpwd.validate_msg.resetpwd_code_success'),'mail'=>$mstatus];
		}
		else
	  	{
			return ['status'=>2,'msg'=>\trans('affiliate/forgotpwd.validate_msg.uname_notfound')];
	   	}
	}
	
	public function check_pwdreset_token($postdata)
	{
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as ust1')
				->join($this->config->get('tables.ACCOUNT_MST') . ' as um1', 'um1.account_id', '=', 'ust1.account_id')
                ->where("ust1.pwd_reset_key",'=',$postdata['usrtoken'])
                ->whereRaw("TIME(ust1.pwd_reset_key_sess) >= '".date('H:i:s')."'")
				->select(DB::Raw('um1.account_id,um1.uname,um1.email,ust1.country_id'));
				
		$usrRes = DB::table(DB::raw('('.$fisrtQry->toSql().') as ust'))
				->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'ust.account_id')
                ->select(DB::Raw("ust.*,ud.firstname,ud.lastname,concat_ws('',ud.firstname,ud.lastname) as full_name,ust.country_id"))
				->addBinding($fisrtQry->getBindings())
                ->first();

		if (!empty($usrRes) && count($usrRes) > 0)
		{					
			return $usrRes;
		}
		else
	  	{
			return NULL;  
	   	}
	}
	
	public function tran_password_check ($oldpassword = '', $user_id)
    {  
        $data = '';
		$data['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $data['msg'] = trans('franchisee/settings/security_pwd.incrct_trans_pwd');
        if (!empty($oldpassword))
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where(array(
                        'trans_pass_key'=>md5($oldpassword),
                        'account_id'=>$user_id,
						'is_deleted'=>$this->config->get('constants.OFF')))
                    ->first();
            if (!empty($result) && count($result) > 0)
            {
                return 1;
            }
			else { 
				return 2;
			}
        }		
		return 3;        
    }
	
	
    public function password_check($oldpassword = 0, $user_id)
    {
        $data['status'] = 'error';
        $data['msg'] = trans('affiliate/settings/changepwd.incorrect_pwd');
	
        if ($oldpassword)
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where(array(
                        'pass_key'=>md5($oldpassword),
                        'account_id'=>$user_id,
						'is_deleted'=>$this->config->get('constants.OFF')))
                       ->first();
					   
                if (!empty($result) && count($result) > 0)
                {
                    return 1;
                }
			    else { 
				      return 2;
			     }
           }	
			   return 3;        
    }
	
	public function update_password ($account_id, $postdata)
    {
		if($account_id>0 && !empty(trim($postdata['newpassword'])))
		{
			$data['pass_key'] = md5($postdata['newpassword']);
		   
			if ($data['pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id', $account_id)
							->value('pass_key'))
			{ 
				$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id', $account_id)
					->update($data);
				if (!empty($status) && isset($status))
				{
					return array(
						'status'=>true,
						'msg'=>trans('franchisee/settings/changepwd.password_change'),
						'alertclass'=>'alert-success');
				}
				else
				{
					return array(
						'status'=>false,
						'msg'=>trans('general.something_wrong'),
						'alertclass'=>'alert-danger');
				}
			}
			else{
				return array(
					'status'=>false,
					'msg'=>trans('franchisee/settings/changepwd.same_as_old'),
					'alertclass'=>'alert-danger');
			}
		}
        return array('msg'=>trans('franchisee/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning');
    }
	
	public function tran_update_password ($user_id, $postdata)
    {
			if($user_id>0 && !empty(trim($postdata['tran_newpassword'])))
		{
			$data['trans_pass_key'] = md5($postdata['tran_newpassword']);
		   
			if ($data['trans_pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id', $user_id)
							->value('trans_pass_key'))
			{ 
				$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id', $user_id)
					->update($data);
				if (!empty($status) && isset($status))
				{
					return json_encode(array('status'=>config('httperr.SUCCESS'),
						'msg'=>trans('affiliate/settings/security_pwd.password_change'),
						'alertclass'=>'alert-success'));
				}
				else
				{
					return json_encode(array(
						'status'=>config('httperr.UN_PROCESSABLE'),'msg'=>trans('general.something_wrong'),
						'alertclass'=>'alert-danger'));
				}
			}
			else{
				return json_encode(array('status'=>config('httperr.UN_PROCESSABLE'),
						'msg'=>trans('affiliate/settings/security_pwd.same_as_old'),
						'alertclass'=>'alert-danger'));
			}
		}
        return json_encode(array('status'=>config('httperr.UN_PROCESSABLE'),'msg'=>trans('affiliate/settings/security_pwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }	
	
	/* public function usercheck_for_fundtransfer($username)
	{
		$op = array();
		$op['status'] = 'error';
		$op['msg'] = trans('franchisee/wallet/fundtransfer.invalid_username');
		$op['user_id'] = 0;
		if($username != '')
		{
			$qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
						->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
						->where('um.uname', '=', $username)
						->where('um.status', '=', $this->config->get('constants.ON'))
						->where('um.block', '=', $this->config->get('constants.OFF'))
						->where('um.is_deleted', '=', $this->config->get('constants.OFF'))
						//->where('um.is_affiliate', '=',$this->config->get('constants.ON'))
						->where(function($sq){
								$sq->where("account_type_id",'=',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))								
								->orWhere('is_affiliate','=', $this->config->get('constants.ON'));
						});
						
						$result = $qry->first();
						
			if (!empty($result))
			{
				$op['status'] = 'ok';
				$op['msg'] = trans('affiliate/wallet/fundtransfer.user_available');
				$op['account_id'] = $result->account_id;
				$op['account_type_id'] = $result->account_type_id;
				$op['full_name'] = $result->firstname.' '.$result->lastname;
				$op['email'] = $result->email;
			}
			else
			{
				$op['status'] = 'error';
				$op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
			}
			return $op;
		}
	} */
	public function usercheck_for_fundtransfer($username)
	{
		$uname = $email = $user_code = '';
		if(is_numeric($username)){
			$user_code = $username;
		} 
		elseif(strpos($username,'@') > 0){			
			$email = $username;			
		}
		else{			
			$uname = $username;			
		}

		$op 			= array();
		$op['status'] 	= 400;
		//$op['msg'] 		= trans('franchisee/wallet/fundtransfer.invalid_username');
		$op['user_id']  = 0;
		
		$frLocAccess = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as frac')
								->where('frac.franchisee_id','=',$this->userSess->franchisee_id)
								->where('frac.status','=',$this->config->get('constants.ON'))
								->orderby('id','DESC')
								->first();
							
		if($frLocAccess)
		{
			//print_r($frLocAccess);
			if($username != '')
			{
				$res = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
							->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
							->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
							->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id');												
				if(isset($user_code) && !empty($user_code)){
					$res->where('um.user_code', '=', $user_code);
				}
				else if(isset($email) && !empty($email)){
					$res->where('um.email', '=', $email);
				}
				else{
					$res->where('um.uname', '=', $uname);
				}
				
				$res->where('um.is_deleted', '=', $this->config->get('constants.OFF'))
					/* ->where('um.is_affiliate', '=',1) */
					->where('um.status', '=',$this->config->get('constants.ON'))
					->where('um.block', '=',$this->config->get('constants.OFF'));
				
				if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.COUNTRY')){
					$res->where('ap.country_id','=',$frLocAccess->relation_id);
				}
				else {
					$res->where('ap.country_id','=',$frLocAccess->country_id);
				}
				
				$res->where('um.account_id','!=',$this->userSess->account_id)
					->where(function($w){
						$w->where('um.account_type_id',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
						->orWhere('um.is_affiliate',$this->config->get('constants.ACTIVE'));
					});								
				
				$res->select('um.account_id','um.user_code','um.uname','um.email','um.mobile','um.account_type_id','um.account_type_id','um.is_affiliate','um.account_type_id','um.account_type_id','um.account_type_id','um.account_type_id','um.account_type_id',DB::Raw('concat_ws(" ",ud.firstname,ud.lastname) as fullname'),'ap.country_id');				
				$acInfo = $res->first();	
				
				$access_exists = false;
				$acSess = '';
				if (!empty($acInfo))
				{
					if($acInfo->is_affiliate==$this->config->get('constants.ACTIVE')) {

						$acLoc = DB::table($this->config->get('tables.ADDRESS_MST').' as adm')
									->leftjoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'adm.state_id')
									->where('adm.address_type_id', '=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'))
									->where('adm.relative_post_id', '=',$acInfo->account_id)
									->select('adm.*','ls.region_id')
									->first();
						/* print_r($acLoc); */
						
						if($acLoc) {		
							
							if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.COUNTRY') && 
								$frLocAccess->relation_id == $acLoc->country_id){
								$access_exists  = true;
							}
							else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.REGION') && 
								$frLocAccess->country_id == $acLoc->country_id &&  $frLocAccess->relation_id == $acLoc->region_id){
								$access_exists  = true;
							}
							else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.STATE') && 
								$frLocAccess->relation_id == $acLoc->state_id){
								$access_exists  = true;
							}
							else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.DISTRICT') && 
								$frLocAccess->relation_id == $acLoc->district_id){
								$access_exists  = true;
							}
							else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.CITY') && 
								$frLocAccess->relation_id == $acLoc->city_id){
								$access_exists  = true;
							}
							$acSess = (array)$acInfo;
						}
						else {
							$op['status'] = 'error';
							$op['msg'] = trans('franchisee/wallet/fundtransfer.cant_transfer_fund');
						}						
					}
					else if($acInfo->account_type_id==$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE') && 
						$acInfo->is_affiliate==$this->config->get('constants.OFF')) {
							
						$toFrAccess = DB::table($this->config->get('tables.FRANCHISEE_MST').' as fr')									
									->join($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fra', function($join){
											$join->on('fra.franchisee_id', '=', 'fr.franchisee_id')
												->where('fra.status','=',$this->config->get('constants.ACTIVE'));
									})
									->where('fr.account_id','=',$acInfo->account_id)
									->where('fr.status','=', $this->config->get('constants.ACTIVE'))
									->orderby('fra.id','DESC')
									->first();
						
						unset($toFrAccess->created_on);
						unset($toFrAccess->updated_on);
						unset($toFrAccess->created_by);
						unset($toFrAccess->updated_by);
						
						if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.COUNTRY') && 
							$frLocAccess->relation_id == $toFrAccess->country_id){
							$access_exists  = true;
						}
						else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.REGION') && 
							$frLocAccess->country_id == $toFrAccess->country_id && $frLocAccess->relation_id == $toFrAccess->region_id){
							$access_exists  = true;
						}
						else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.STATE') && 
							$frLocAccess->relation_id == $toFrAccess->state_id){
							$access_exists  = true;
						}
						else if($frLocAccess->access_location_type==$this->config->get('constants.FRANCHISEE_TYPE.DISTRICT') && 
							$frLocAccess->relation_id == $toFrAccess->district_id){
							$access_exists  = true;
						}
						$acSess = array_merge((array)$acInfo,(array)$toFrAccess);
					}					
				
					//echo '<br>'.$access_exists;
					//die;
					
					if($access_exists){
						$this->session->set('ftac',['to_account_info'=>$acSess]);
						$op['status'] = 200;						
						//$op['msg'] = trans('franchisee/wallet/fundtransfer.user_available');						
						$op['acSess'] = $acSess;				
						$op['account_id'] = $acInfo->account_id;				
						$op['uname'] = $acInfo->uname;
						$op['user_code'] = $acInfo->user_code;
						$op['full_name'] = $acInfo->fullname;
						$op['email'] = $acInfo->email;
						$op['mobile'] = $acInfo->mobile;
						$op['is_franchasee'] = ($acInfo->account_type_id==$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))?1:0;
						if($acInfo->account_type_id==$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')){							
							$op['company_name'] = $acSess['company_name'];
							$op['frtype'] = trans('general.fr_type.'.$acSess['franchisee_type']);
						}
					}
					else {
						$op['status'] = 422;
						$op['msg'] = 'Your are not allowed to transfer funds to other locations';
					}
				}
				else
				{
					$op['status'] = 400;
					$op['msg'] = "Please provide a valid Account ID.";
				}				
			}
			else
			{
				$op['status'] = 400;
				$op['msg'] = "Please provide a valid Account ID.";
			}
		}
		else {
			$op['status'] = 422;
			$op['msg'] = 'You cannot transfer funds to others. Please check with Virob Support';
		}
		return $op;
	}

	public function getUser_loginDetails ($arr='',$fields)
	{
	    $account_id = $arr['account_id'];
	    $qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as um');
	    if (is_array($fields))
        {	
			$qry->select($fields);
        }
        if (!is_int($account_id))
        {
           $res= $qry->where('um.uname', $account_id);
        }
        else if (is_int($account_id))
        {
			 
            $qry->where('um.account_id', $account_id);
        }
        if ($account_id != '')
        {	
		    return  $qry->first();
		}
        return false;
    }
  
	public function saveBrowserInfo($account_id=0,$account_log_id=0,$purpose='2'){
		
		$bwInfo = \AppService::getBrowserInfo();
		
		$bwInfo['ip'] = \Request::getClientIp(true);
		
		$bwInfo['account_id'] = $account_id;
		$bwInfo['purpose'] = $purpose;
		$bwInfo['browser_info'] = '';
		$bwInfo['country'] = '';
		$bwInfo['location'] = '';
		try {
			$ipInfo = \Location::get($bwInfo['ip']);
			if(!empty($ipInfo)){
				$bwInfo['location'] = $ipInfo->countryName;
				$bwInfo['country'] = $ipInfo->countryCode;
				DB::table($this->config->get('tables.ACCOUNT_BROWSER_INFO'))
						->insertGetID([
							'account_id' => $account_id,
							'purpose' => $purpose, /*forgotpwd*/
							'browser_info' => addslashes(json_encode(\AppService::getBrowserInfo())),
							'country' => \Location::get($bwInfo['ip'])->countryCode]);				
			}
			return (object)$bwInfo;			
		}
		catch(Exception $e){
			return (object)$bwInfo;
		}
	}
	
	public function franchisee_access_locations ($franchisee_id=0,$franchisee_type)
    {
        if($franchisee_id>0){
			$frAccess = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
					->select(DB::raw('fal.id as loc_access_id,fal.account_id,fal.relation_id,fal.access_location_type as access_type, fal.country_id, fal.region_id, fal.state_id, fal.district_id'))
					->where('fal.status', $this->config->get('constants.ON'))
					->where('fal.franchisee_id', $franchisee_id)
					->get();
			$locArr = [];
			if(!empty($frAccess)) {
				if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY')){
					foreach($frAccess as $frac){
						$lcRes = DB::table($this->config->get('tables.LOCATION_COUNTRY').' AS lc')
							->where('adm.country_id','=',$frac->relation_id)
							->select('lc.country_id','lc.currency_id','lc.country')
							->first();
						$locArr[] = implode(',',[$lcRes->country]);
					}
				}
				else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.REGION')){
					foreach($frAccess as $frac){
						$lcRes = DB::table($this->config->get('tables.LOCATION_COUNTRY').' AS lc')
							->where('adm.country_id','=',$frac->relation_id)
							->select('lc.country_id','lc.currency_id','lc.country')
							->first();
						$rgRes = DB::table($this->config->get('tables.LOCATION_REGION').' AS rg')
							->where('rg.region_id','=',$frac->relation_id)
							->first();
						$locArr[] = implode(', ',[$lcRes->region,$lcRes->country]);
					}
				}
				else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.STATE')){
					foreach($frAccess as $frac){
						$lcRes = DB::table($this->config->get('tables.LOCATION_STATE').' AS ls')
							->join($this->config->get('tables.LOCATION_COUNTRY').' AS lc','lc.country_id','=','ls.country_id')
							->where('ls.state_id','=',$frac->relation_id)
							->select('lc.country_id','lc.country','ls.state','ls.state_id')
							->first();						
						$locArr[] = implode(', ',[$lcRes->state,$lcRes->country]);
					}
				}
				else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT')){
					foreach($frAccess as $frac){
						$lcRes = DB::table($this->config->get('tables.LOCATION_DISTRICTS').' AS ld')
							->join($this->config->get('tables.LOCATION_STATE').' AS ls','ls.state_id','=','ld.state_id')
							->join($this->config->get('tables.LOCATION_COUNTRY').' AS lc','lc.country_id','=','ls.country_id')
							->where('dt.district_id','=',$frac->relation_id)
							->select('lc.country_id','lc.country','ls.state','ls.state_id','ld.district_id','ld.district')
							->first();						
						$locArr[] = implode(', ',[$lcRes->district,$lcRes->state,$lcRes->country]);
					}
				}
				else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.CITY')){
					foreach($frAccess as $frac){
						$lcRes = DB::table($this->config->get('tables.LOCATION_TOP_CITY').' AS lcy')
							->join($this->config->get('tables.LOCATION_DISTRICTS').' AS ld','ld.district_id','=','lcy.district_id')
							->join($this->config->get('tables.LOCATION_STATE').' AS ls','ls.state_id','=','ld.state_id')
							->join($this->config->get('tables.LOCATION_COUNTRY').' AS lc','lc.country_id','=','ls.country_id')
							->where('lcy.city_id','=',$frac->relation_id)
							->select('lc.country_id','lc.country','ls.state','ls.state_id','ld.district_id','ld.district','lcy.city_name as city','lcy.city_id')
							->first();						
						$locArr[] = implode(', ',[$lcRes->city,$lcRes->district,$lcRes->state,$lcRes->country]);
					}
				}
			}
			return (!empty($locArr)) ? $locArr : false;
		}
		return false;
    }
	
	
	public function getWalletList ()
    {
        return DB::table($this->config->get('tables.WALLET').' as w')
				->join($this->config->get('tables.WALLET_LANG').' as wl',function($join){
					$join->on('w.wallet_id','=','wl.wallet_id')
						->where('wl.lang_id','=',$this->config->get('app.locale_id'));
				})
				->select('w.wallet_id','w.wallet_code','wl.wallet','wl.terms')
				->where([
					'status'=>$this->config->get('constants.ACTIVE'),
					'is_franchisee_wallet'=>$this->config->get('constants.ACTIVE')])
				->orderby('w.sort_order','ASC')
				->get();
    }

	
	public function getUserAddr($account_id,$postType=0,$addType=0){
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] = trans('affiliate/account.paramiss');
		$op['msgtype'] = 'danger';
		if($account_id>0 && $addType>0 && $postType>0) {
			$res = DB::table($this->config->get('tables.ADDRESS_MST').' AS adm')
				->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'adm.country_id')
                ->join($this->config->get('tables.LOCATION_STATE') . ' as ls', 'ls.state_id', '=', 'adm.state_id')
				->join($this->config->get('tables.LOCATION_DISTRICTS') . ' as ld', 'ld.district_id', '=', 'adm.district_id')
				->join($this->config->get('tables.LOCATION_CITY') . ' as lcy', 'lcy.city_id', '=', 'adm.city_id')
				->where('adm.relative_post_id','=',$account_id)
				->where('adm.post_type','=',$postType)
				->where('adm.address_type_id','=',$addType)
				->select('adm.address_type_id','adm.post_type',DB::Raw('adm.relative_post_id as account_id'),'adm.flatno_street','adm.landmark','adm.address','adm.postal_code','adm.status','adm.city_id','adm.district_id','adm.state_id','adm.country_id','lc.country','ls.state','ld.district','lcy.city')
				->first();
			if($res) { 
				$op = ['address'=>$res,'status'=>$this->config->get('httperr.SUCCESS')];
			}
			else {
				$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('affiliate/account.address_not_available');
			}
		}
		return $op;
	}
	
	public function getUserNominees($account_id)
    {
        $result = DB::table($this->config->get('tables.ACCOUNT_NOMINEES').' as an')
				->leftJoin($this->config->get('tables.RELATION_SHIPS_LANG').' as rs', function($subquery)
                {
                    $subquery->on('rs.relation_ship_id', '=', 'an.relation_ship_id')
                    ->where('rs.lang_id', '=', $this->config->get('app.locale_id'));
                })
				->leftjoin($this->config->get('tables.GENDER_LANG') . ' as gl ', function($join){
					$join->on('gl.gender_id', '=', 'an.gender_id')
					->where('gl.lang_id','=',$this->config->get('app.locale_id'));	
				})
                ->where('an.is_deleted', 0)
                ->where('an.account_id', $account_id)
				->select('an.account_id','an.fullname','an.gender_id','an.dob','an.relation_ship_id','an.created_on','rs.relation_ship','gl.gender')
                ->first();
        if(!empty($result)) {			
			return $result;
		} 
		else {
			return false;
		}
    }		
	public function user_email_check ($email = 0, $getdetailstatus = 0)
    {
        if ($email)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $status['status'] = 'error';
                $status['msg'] = trans('affiliate/validator/change_email_js.invalid_email');
                return $status;
            }
            if ($email == 'testerej88@gmail.com')
            {
                $status['status'] = 'ok';
                $status['msg'] = trans('affiliate/validator/change_email_js.email_available');
                return $status;
            }
            else if ($email != 'testerej88@gmail.com')
            {
                $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('email', $email)
                        ->get();
                if (empty($result) && count($result) == 0)
                {
                    $status['status'] = 'ok';
                    $status['msg'] = trans('affiliate/validator/change_email_js.email_available');
                }
                else
                {
                    $status['status'] = 'error';
                    $status['msg'] = trans('affiliate/validator/change_email_js.email_exist');
                }
                return $status;
            }
        } else
        {
            $status['status'] = 'error';
            $status['msg'] = trans('affiliate/validator/change_email_js.invalid_email');
            return $status;
        }
    }
	
	public function get_site_settings ()
    {
        return DB::table($this->config->get('tables.SITE_SETTINGS'))
                        ->where('sid', 1)
                        ->first();
    }
	
	public function update_uname($account_id, $uname)
    {		
        $update_data = array();
        if(!empty($uname)){
			if(!DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('uname', $uname)
					->exists()){
				$res = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id', $account_id)
					->update(['uname'=>$uname]);
				if($res) {					
					$this->userSess->uname = $uname;
					$this->session->set($this->sessionName, (array) $this->userSess);					
					$op['msg'] = trans('affiliate/account.change_uname.success');
					$op['status'] = $this->config->get('httperr.SUCCESS');;
				}
				else {
					$op['msg'] = trans('affiliate/account.change_uname.errmsg');
					$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				}				
			}
			else {
				$op['msg'] = trans('affiliate/account/change_uname.exists');
				$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
			}
		}
		else {
			$op['msg'] = trans('affiliate/account/change_uname.empty');
			$op['status'] =$this->config->get('httperr.UN_PROCESSABLE');
		}
		return $op;
    }
	
	
	public function update_user_email ($account_id, $email)
    {
        $update_data = array();
        $update_data['email'] = $email;
        $res = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update($update_data);
		if($res) {
			$this->userSess->email = $email;
			$this->session->set($this->sessionName, $this->userSess);
			return true;
		}
		else {
			return false;
		}
    }
	
	/* Mobile Number Update */
	
	public function user_mobile_check ($mobile = 0, $getdetailstatus = 0)
    {  
        if ($mobile)
        {
            if ($mobile == 9876543210)
            {
                $status['status'] = 'ok';
                $status['msg'] = trans('affiliate/validator/change_mobile_js.mobile_available');
                return $status;
            }
            else if ($mobile != 9876543210)
            {
                $result = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                        ->where('mobile', $mobile)
                        ->get();
                if (empty($result) && count($result) == 0)
                {
                    $status['status'] = 'ok';
                    $status['msg'] = trans('affiliate/validator/change_mobile_js.mobile_available');
                }
                else
                {
                    $status['status'] = 'error';
                    $status['msg'] = trans('affiliate/validator/change_mobile_js.unique');
                }
                return $status;
            }
        } else
        {
            $status['status'] = 'error';
            $status['msg'] = trans('affiliate/validator/change_mobile_js.invalid_mobile');
            return $status;
        }
    }
		
	public function update_user_mobile ($account_id, $mobile)
    {  
        $update_data = array();
        $update_data['mobile'] = $mobile;
        $res = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update($update_data);
		if($res) {
			$this->userSess->mobile = $mobile;
			$this->session->set($this->sessionName, (array) $this->userSess);
			return true;
		}
		else {
			return false;
		}
    }
	
	/* Marital Status */
	public function get_marital_status ()
    {
        return DB::table($this->config->get('tables.MARTIAL_STATUS_LANG'))                        
						->where('lang_id', '=', $this->config->get('app.locale_id'))
                        ->get();
    }
	
	public function updateProfile (array $arr = array())
    {    
	    extract($arr);       
        $res = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                        ->where('account_id', $account_id)
                        ->update(['gardian'=>$gardian, 'marital_status'=>$marital_status]);
						
		return ($res) ? true : false;
    }
	
	/* Bank Details */
	
	public function GetBankAccountDetails ($arr)
    {	    
		extract($arr);
		$res = DB::table($this->config->get('tables.AFF_ACCOUNT_PAYOUT_SETTINGS'))
				->where('account_id', '=', $account_id) 
				->where('is_deleted', '=', 0)
				->select('payment_settings','is_verified','account_id')
				->first('payment_settings');

		if(!empty($res) && $res->payment_settings)
		{
			$payment_settings = json_decode($res->payment_settings);
			$payment_settings->is_verified	= $res->is_verified;
			$payment_settings->account_id	= $res->account_id;
			return $payment_settings;	
		}
		return false;
		//return ($payment_settings) ? json_decode($payment_settings) : false;	
    }
	
	public function UpdateBankDetails ($arr)
    {	
    	extract($arr);	
		$count = DB::table($this->config->get('tables.AFF_ACCOUNT_PAYOUT_SETTINGS'))
				->where('account_id', '=', $account_id)
				->where('is_deleted', '=', 0)->count();	
		if ($count == 0) {			
			$ps['payment_settings'] = json_encode($payment_setings);        
			$ps['account_id'] = $account_id;			
			$ps['currency_id'] = $currency_id;
			$ps['updated_by'] = $account_id;
			$ps['created_date'] = getGTZ();		
			DB::table($this->config->get('tables.AFF_ACCOUNT_PAYOUT_SETTINGS'))->insertGetID($ps);  
		} else {
			$ps['payment_settings'] = json_encode($payment_setings);   
			$ps['updated_by'] = $account_id;			
			DB::table($this->config->get('tables.AFF_ACCOUNT_PAYOUT_SETTINGS'))->where('account_id', '=', $account_id)->update($ps);
		}
        return true;
    }	
	
	public function updateBankInfo (array $arr = array())
    {	
	    $bdata = $ifscdata = [];
    	extract($arr);	
		
        return false;
    }
	
	public function UpdateCompletedSteps (array $arr = array())
    {	
        extract($arr);
        if ($account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER'))
        {			
            $completed_steps = DB::table($this->config->get('tables.SUPPLIER_MST'))->where('supplier_id', $supplier_id)->value('completed_steps');
        }
        $completed_steps = !empty($completed_steps) ? explode(',', $completed_steps) : [];
        if (!in_array($current_step, $completed_steps))
        {
            $completed_steps[] = $current_step;
        }       
        $completed_steps = array_unique($completed_steps);

        /* $is_verified = !DB::table($this->config->get('tables.ACCOUNT_CREATION_STEPS'))
                        ->whereNotIn('step_id', $completed_steps)                        
                        ->where('account_type_id', $account_type_id)                        
                        ->orderby('priority', 'ASC')
                        ->exists(); */
		/* if(){
		
		} */
		$next = DB::table($this->config->get('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                //->havingRaw('min(priority)')
                ->orderby('priority')
                ->selectRaw('step_id, route')
                ->first();        
			
		$nextstep = !empty($next->step_id) ? $next->step_id : 0;	        
		if ($account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER'))
		{
			$result = DB::table($this->config->get('tables.SUPPLIER_MST'))->where('supplier_id', $supplier_id)
					->update(['completed_steps'=>implode(',', $completed_steps), 'updated_by'=>$account_id, 'next_step'=>$nextstep]);
			return (isset($next->route) && !empty($next->route)) ? \URL::route($next->route) : \URL::to('seller/dashboard');
		}                    
        return false;
    }
	
	/* Kyc Upload Document */
	
	public function getKycStatus ($account_id)
    { 
	    $kyc_status = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('account_id', $account_id)
						->value('kyc_status');
						
		if(!empty($kyc_status)){
		    $kyc_status = json_decode(stripslashes($kyc_status));
			$kyc_status->submitted_date = !empty($kyc_status->submitted_date) ?  showUTZ($kyc_status->submitted_date, 'd-M-Y'): '';
		}
	    return $kyc_status;
	}
	public function kyc_document_upload ($arr = array(),$userSess)
    {  	
	    $update = false;
	    $res = [];			
		$result = $this->getKycStatus($userSess->account_id);	
		$submitted_doc = (!empty($result)) ? $result->submitted_doc : 0;		
		
	    foreach($arr as $k=>$kyc){	
			$res[$k] = [];	
			if (!empty($kyc))
			{		
		        if(DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
					->where('account_id',$kyc['account_id'])
					->where('document_type_id',$kyc['document_type_id'])
					->where('status_id',$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.PENDING'))
					->exists()){ 
					$res[$k] = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
								->where('account_id',$kyc['account_id'])
								->where('document_type_id',$kyc['document_type_id'])
						        ->update(array('path'=>$kyc['path'],'doc_number'=>$kyc['doc_number'],'created_on'=>$kyc['created_on']));				 
				  
				}else {
				    $res[$k] = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
						             ->insert($kyc);	
					$submitted_doc = ++$submitted_doc;	
				}
				$update = true;
			}			
		}		
		if ($update)
		{		
			$kyc_submitted_on = getGTZ();
			if($userSess->aff_type_id == $this->config->get('constants.AFFILIATE_TYPE.CM')){
				$total_doc = $this->config->get('constants.KYC_DOCUMENT_TYPE'); 
			}else{
				$total_doc = array($this->config->get('constants.KYC_DOCUMENT_TYPE.PAN'),$this->config->get('constants.KYC_DOCUMENT_TYPE.CHEQUE'));
			} 
			$kyc_status = [					
				'total_doc'=>count($total_doc),
				'submitted_doc'=>$submitted_doc,
				'submitted_date'=>$kyc_submitted_on,
			];				
			$kyc_status = addslashes(json_encode($kyc_status));   
			
			DB::table($this->config->get('tables.ACCOUNT_TREE'))
				->where('account_id', $kyc['account_id'])
				->update(array('is_kyc_verified'=>$this->config->get('constants.OFF'), 'kyc_status'=>$kyc_status,'kyc_submitted_on'=>$kyc_submitted_on));
		}
        return $res;
    }
	
	public function getKycDocument ($arr = array())
    { 
        $res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))              
						->where('account_id', $arr['account_id']) 
						->where('is_deleted', $this->config->get('constants.NOT_DELETED'))   
						->where('document_type_id', $arr['prooftype'])				
						->select('doc_number','status_id','path')
						->orderBy('uv_id','DESC')		
						->first();
		if(!empty($res)){
		    $res->path = asset($this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.WEB').$res->path); 
		}
		return $res;
    }
	
	/* NEW */	
	
	public function get_user_verification_total ($account_id)
    {
        $result = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                ->where('status_id', $this->config->get('constants.ON'))
                ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('account_id', $account_id)
                ->get();			
        return count($result);
    }
	
	
	public function getAccInfo($params = array()) {
		extract($params);
        if(!empty($params)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as am')
					->where('am.account_id','=',$account_id)
					->where('am.is_deleted',$this->config->get('constants.OFF'))
			        ->select('am.account_id','am.uname','am.account_type_id','am.is_affiliate','am.trans_pass_key','am.trans_pass_key','am.block','am.status','am.email','am.mobile');
			$res = $qry->first();					
		    if ($res) {			
                return $res;				
            }
        }
		return NULL;
    }
	
	public function get_account_details($params = array()) {
		extract($params);
        if(!empty($params)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as am')
			        ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ad', 'ad.account_id', '=', 'am.account_id')	
					->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'am.account_id')
					->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
					->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')	
					->join($this->config->get('tables.FRANCHISEE_MST') . ' as fm', 'fm.account_id', '=', 'am.account_id')	
					->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl','fl.franchisee_typeid','=','fm.franchisee_type')
					->join($this->config->get('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', '=', 'fm.franchisee_type')
					->leftjoin($this->config->get('tables.GENDER_LANG') . ' as gl ', function($join){
							$join->on('gl.gender_id', '=', 'ad.gender')
							->where('gl.lang_id','=',$this->config->get('app.locale_id'));	
					 })
					->leftjoin($this->config->get('tables.MARTIAL_STATUS_LANG') . ' as mts ', function($join){
							$join->on('mts.marital_status_id', '=', 'ad.marital_status')
							->where('mts.lang_id','=',$this->config->get('app.locale_id'));
					 })
					->where('am.account_id','=',$account_id)
					->where('am.is_deleted',$this->config->get('constants.OFF'))
					
					   ->select(DB::raw('am.account_id,am.user_code,am.uname,am.account_type_id,am.email,am.mobile,ad.firstname,ad.lastname,
					   ad.dob,ad.pan_no,ad.gender as gender_id,concat(ad.firstname," ",ad.lastname) as full_name,mts.marital_status_id,mts.marital_status,ad.gardian,gl.gender,lc.phonecode,lc.country,fm.company_name,fm.franchisee_type,fl.level,fm.logo_path,fm.franchisee_id,fb.wallet_purchase_per'));
			        $res = $qry->first();			
				     if(!empty($res)) {		
						$res->franchisee_type_id = $res->franchisee_type;
					      $res->franchisee_type=$this->config->get('constants.FRANCHISEE_TYPE.'.$res->franchisee_type);
                        return $res;	
                }			
        }
		return NULL;
    }
	/*  saveProfilePIN  */
	public function saveProfilePIN (array $arr = array())
    { 
		if(!empty($arr)){
			extract($arr);
			if(!empty($account_id) && !empty($security_pin)){
				return   DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id', $account_id)
							->update(['trans_pass_key'=>md5($security_pin)]);
			}
		}
		return false;
    }	
	
	public function user_name_check ($arr )
    {
        extract($arr);
		  $qry =  DB::table($this->config->get('tables.ACCOUNT_MST') . ' as am')
		          ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','am.account_id')
				  ->select(DB::Raw("am.account_id,am.account_type_id,am.uname,am.email,am.mobile,am.pass_key,am.trans_pass_key,am.is_affiliate,am.signedup_on,am.activated_on,am.expiry_on,am.status,am.is_deleted,am.last_login,am.last_active,am.block,am.login_block,am.is_closed,am.activation_key,concat_ws(' ',acd.firstname,acd.lastname) as full_name"))
			      ->where('is_deleted',config('constants.OFF')); 
				  
        if (isset($account_type_id) && is_array($account_type_id)){
			$qry->whereIn('account_type_id', $account_type_id);
		} 
        if(isset($email) && !empty($email)){
			$qry ->where('email', $email);
		} 
		$result =$qry->first();
		
        if (!empty($result)) {
            return $result;
        }
        return NULL;
    }
	public function logo_image_upload ($arr=array())
    {
        extract($arr);
	
        $res = DB::table($this->config->get('tables.FRANCHISEE_MST'))
                ->where('account_id', $account_id)
                ->update(['logo_path'=>$docpath]);
        return $res;
    }
   /* Franchisee Address */
	 public function updateAddress($arr=array()){
		 
		$op = [
			'status'=>$this->config->get('httperr.UN_PROCESSABLE'),
			'msg'=> trans('affiliate.account.address_datas_missing')
		];
		if(!empty($arr)){		
			$ad_setting = [];				        
			$ad_setting['post_type'] = isset($arr['post_type'])? $arr['post_type']: $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
			$ad_setting['address_type_id'] = isset($arr['address_type_id'])? $arr['address_type_id']: $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
			$ad_setting['relative_post_id'] =  isset($arr['relative_post_id'])? $arr['relative_post_id']:$this->userSess->account_id;
			$ad_setting['country_id'] = $arr['country_id'];
			$ad_setting['flatno_street'] = isset($arr['flatno_street'])? $arr['flatno_street']:0;	
			$ad_setting['landmark'] = isset($arr['landmark'])? $arr['landmark']:0;	
			$ad_setting['city_id'] = isset($arr['city'])? $arr['city']:0;	
			$ad_setting['state_id'] = isset($arr['state'])? $arr['state']:0;
			$ad_setting['district_id'] = isset($arr['district'])? $arr['district']:0;		
			$ad_setting['postal_code'] = isset($arr['postal_code'])? $arr['postal_code']:'';		
			
			$ad_setting['geolat'] = isset($arr['geolat'])? $arr['geolat']:'';		
			$ad_setting['geolng'] = isset($arr['geolng'])? $arr['geolng']:'';
			
			if(isset($arr['formated_address']) && is_array($arr['formated_address'])){
				$ad_setting['address'] = implode(', ',$formated_address);		
			}
			else if(isset($arr['formated_address']) && is_string($arr['formated_address'])){
				$ad_setting['address'] = $arr['formated_address'];		
			} 
			else {
				$ad_setting['address'] = '';		
			}
		
			
			if(DB::table($this->config->get('tables.ADDRESS_MST'))
				->where('relative_post_id','=',$ad_setting['relative_post_id'])
				->where('post_type','=',$ad_setting['post_type'])
				->where('address_type_id','=',$ad_setting['address_type_id'])					
				->exists()){
				$usRes = DB::table($this->config->get('tables.ADDRESS_MST'))
							->where('relative_post_id','=',$ad_setting['relative_post_id'])
							->where('post_type','=',$ad_setting['post_type'])
							->where('address_type_id','=',$ad_setting['address_type_id'])
							->update($ad_setting);
				if($usRes) {
					$op['address'] = $arr['formated_address'];
					$op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.address_updated');
					$op['msgtype'] = 'success';
				}
				else {
					$op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.address_already_updated');
					$op['msgtype'] = 'warning';
				}
			} else {
				if(DB::table($this->config->get('tables.ADDRESS_MST'))
									->insertGetID($ad_setting)){
					$op['address'] = $arr['formated_address'];
					$op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.address_added');
					$op['msgtype'] = 'success';
				}
				else {
					$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('affiliate/account.address_datas_missing');
					$op['msgtype'] = 'danger';
				}
			}			
		}
		return $op;
	}
	public function update_account_activationkey($account_id,$sdata)	
	{		
	    $res ='';
		if($account_id >0 && !empty($sdata))
		{
			$res = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
					->where('account_id','=',$account_id)
					->update($sdata);
		}  
		return $res;
    }
	public function profilepin_check($account_id){		
		
		 $qry= DB::table($this->config->get('tables.ACCOUNT_MST'))
		       ->select('trans_pass_key')
		       ->where('account_id', $account_id)
			   ->first();
            return $qry;
	}	
	
	  
	  
	 public function get_franchisee_address($arr){
	    extract($arr);
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
				   ->join($this->config->get('tables.FRANCHISEE_MST').' as fs','fs.franchisee_id', '=', 'fal.franchisee_id')
				   ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'fs.account_id');
					   if(isset($district_id) && !empty($district_id)){
							$qry->where('fal.access_location_type','=',$this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'));
							$qry->where('fal.relation_id','=',$district_id);
					  }
					  if(isset($state_id) && !empty($state_id)){
							$qry->where('fal.access_location_type','=',$this->config->get('constants.FRANCHISEE_TYPE.STATE'));
							$qry->where('fal.relation_id','=',$state_id);
					  } 
						  $qry->where('fal.status','=',$this->config->get('constants.ON'));
						$qry->select('fal.account_id','fal.merchant_signup_fee','fal.profit_sharing','fal.relation_id','fal.country_id','fal.region_id','fal.state_id','fal.district_id','fal.profit_sharing_without_district','fal.deposite_amount','fs.franchisee_id','fs.franchisee_type','ap.currency_id');
						    $res = $qry->first();
							
					 if($res){
						  return $res;
					 }	
					   else{
						  return false;
					 }			   
	           }
     public function get_store_details($supplier_id){
		
        $store_details= DB::table($this->config->get('tables.ORDERS').' as mo')
		                   ->join($this->config->get('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
		                    ->join($this->config->get('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
							->where('mo.supplier_id','=',$supplier_id)
							->where('mo.status', $this->config->get('constants.ORDER.STATUS.PAID'))
							->where('mo.payment_status', $this->config->get('constants.ORDER.PAYMENT_STATUS.PAID'))
							->where('mo.order_type', $this->config->get('constants.ORDER.TYPE.IN_STORE'))
						 	->whereIn('mo.pay_through',[$this->config->get('constants.ORDER.PAID_THROUGH.PAY'),$this->config->get('constants.ORDER.PAID_THROUGH.SHOP_AND_EARN'),$this->config->get('constants.ORDER.PAID_THROUGH.REDEEM')])
							->groupby('mo.store_id')
						    ->having('order_total', '>=' , 1000)
						    ->having('order_count', '>=' , 1) 
							->select(DB::Raw('sum(oc.system_comm) as order_total'),DB::raw('COUNT(mo.order_id) as order_count'),'mo.store_id','mo.supplier_id','msm.store_name','msm.store_code','msm.address_id')
							->get();
							
						if(!empty($store_details)){
							 return $store_details;
						 }
						else{
						   return false;
					  }					
       }
	 public function get_store_address($address_id){
		/*  echo $address_id; die; */
		  $address_info=DB::table($this->config->get('tables.ADDRESS_MST').' as am')
		                 ->where('am.address_type_id',$this->config->get('constants.ADDRESS.PRIMARY'))
						 ->where('am.post_type',$this->config->get('constants.ADDRESS_POST_TYPE.STORE'))
						 ->where('address_id',$address_id) 
						 ->first();
						if(!empty($address_info)){
							 return $address_info;
						}
					    else{
						  return false;
					   }
	      }
		 public function Save_franchisee_commission_fee($arr){
		   extract($arr);
		   if(!empty($franchisee_details) && !empty($store_details)){
				$frmst['franchisee_id'] = $franchisee_details->franchisee_id;
				$frmst['account_id'] = $franchisee_details->account_id;
				$frmst['currency_id'] = $franchisee_details->currency_id;
				$frmst['store_id'] =$store_details->store_id;
				$frmst['state_id'] =$merchant_store_adrress->state_id;
				$frmst['district_id'] = $merchant_store_adrress->district_id;
				$frmst['commission_amount'] = $franchisee_details->merchant_signup_fee;
				$frmst['total_earnings'] = $store_details->order_total;
				$frmst['created_on'] = getGTZ();
				$res=  DB::table($this->config->get('tables.FRANCHISEE_MERCHANT_FEE'))
						->insert($frmst);	
			}
			 return (!empty($res)) ? $res : false;
	}
}