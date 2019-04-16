<?php
namespace App\Models\Affiliate;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;
use App\Models\Commonsettings;


class AffModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		$this->commonObj = new CommonModel;
		$this->commonstObj = new Commonsettings;
    }
	
    public function account_validate($postdata) {
		$status = '';
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where(function($c) use($postdata){
					$c->where('uname',$postdata['uname'])
					->orWhere('email',$postdata['uname'])
					->orWhere('user_code',$postdata['uname']);
				})
                ->where('is_affiliate', '=', $this->config->get('constants.ON'))
				->where('is_deleted', '=', $this->config->get('constants.OFF'))
                ->where('login_block', '=', $this->config->get('constants.OFF'))
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER')])
				->select(DB::Raw('account_id,user_code,pass_key,trans_pass_key,email,uname,mobile,login_block,block,is_affiliate,is_closed,account_type_id'));
				
        $userData = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
                ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as st', 'st.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_TREE') . ' as atr', 'atr.account_id', '=', 'um.account_id')
				->leftjoin($this->config->get('tables.AFF_RANKING_LOOKUPS') . ' as ar', 'ar.af_rank_id', '=', 'atr.pro_rank_id')
				->join($this->config->get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'um.account_type_id')
				->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'st.currency_id')			
				->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'st.country_id')			
                ->selectRaw('um.account_id,um.user_code,st.language_id,um.is_affiliate,atr.can_sponsor,atr.pro_rank_id,atr.aff_type_id,ar.rank as pro_rank,um.account_type_id,at.account_type_name,um.is_closed,um.pass_key,um.trans_pass_key,um.uname,concat_ws(\' \',ud.firstname,ud.lastname) as full_name,ud.firstname,ud.lastname,um.mobile,lc.country,lc.phonecode,ud.profile_img,ud.gardian,ud.marital_status,um.email,st.country_id,st.currency_id,cur.currency as currency_code,um.block,um.login_block,st.is_mobile_verified,st.is_email_verified,st.is_verified,st.referral_code')
                ->addBinding($fisrtQry->getBindings())
				->first();			
        if (!empty($userData)) {
            if ($userData->is_closed == $this->config->get('tables.OFF')) {
                if ($userData->login_block == $this->config->get('tables.OFF')) {
                    if (($userData->pass_key == md5($postdata['password']))||(isset($postdata['qlogin'])) && ($postdata['qlogin'] == true)) {
                        
						if(!emptY($userData->profile_img) &&  file_exists(config('constants.ACCOUNT.PROFILE_IMG.PATH').$userData->profile_img)){
							$userData->profile_imagename = $userData->profile_img;
							$userData->has_profile_img = $this->config->get('constants.ON');
							$userData->profile_img = asset(config('constants.ACCOUNT.PROFILE_IMG.SM').$userData->profile_img);
						} else {
							$userData->profile_imagename = config('constants.ACCOUNT.PROFILE_IMG.DEFAULT');
							$userData->has_profile_img = $this->config->get('constants.OFF');
							$userData->profile_img = asset(config('constants.ACCOUNT.PROFILE_IMG.SM').config('constants.ACCOUNT.PROFILE_IMG.DEFAULT')) ;
						}
						
						$sesdata = array(
                                'account_id' => $userData->account_id,
                                'uname' => $userData->uname,
								'user_code' => $userData->user_code,
								'full_name' => $userData->full_name,
								'firstname' => $userData->firstname,
								'lastname' => $userData->lastname,
                                'email' => $userData->email,								
								'referral_code' => strtoupper($userData->referral_code),
                                'gardian' => $userData->gardian,
                                'marital_status' => $userData->marital_status,
								'is_affiliate' => $userData->is_affiliate,
								'can_sponsor' => $userData->can_sponsor,
								'pro_rank_id' => $userData->pro_rank_id,
								'pro_rank' => $userData->pro_rank,
								'aff_type_id' => $userData->aff_type_id,
								'account_type_name' => $userData->account_type_name,
								'account_type_id' => $userData->account_type_id,								
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
								'profile_imagename' => $userData->profile_imagename,
								'profile_image' => $userData->profile_img,
								'has_profile_img' => $userData->has_profile_img,
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
						$sesdata['last_logged_time'] = date('h:i A d M Y',strtotime($last_active));						
						DB::table($this->config->get('tables.ACCOUNT_MST'))
								->where('account_id', $userData->account_id)
								->update(array('last_active'=>$last_active));
								
						$this->session->regenerate();	
						
					    $update['token'] = request()->session()->getId().'-'.$update['token'];	
				
						$this->session->put($this->sessionName, (object)$sesdata);
						$token_update = DB::table($this->config->get('tables.ACCOUNT_LOG'))
									->where('account_log_id', $account_log_id)
									->update($update);  
						 
                        return ['status'=>1,'msg'=>'Your are successfully logged in'];
                    } else {
						return ['status'=>3,'msg'=>'Password not matched'];
                    }
                } else {
					return ['status'=>5,'msg'=>'Your account has been blocked. Please contact our adiminstrator'];
                }
            } else {
				return ['status'=>6,'msg'=>'Incorrect username or password'];
            }
        } else {
			return ['status'=>2,'msg'=>'Incorrect username or password'];
        }
        return ['status'=>7,'msg'=>'Incorrect username or password'];
    }
	
	public function sendPwd_resetlink($postdata)
	{		
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where("email",'=',$postdata['uname'])
                ->where('is_affiliate', '=', $this->config->get('constants.ON'))
				->where('is_deleted', '=', $this->config->get('constants.OFF'))
                ->where('login_block', '=', $this->config->get('constants.OFF'))
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SUPPLIER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER')]);
				
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
        $data['msg'] = trans('affiliate/settings/security_pwd.incrct_trans_pwd');
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
						'status'=>$this->config->get('httperr.SUCCESS'),
						'msg'=>trans('affiliate/settings/changepwd.password_change'),
						'alertclass'=>'alert-success');
				}
				else
				{
					return array(
						'status'=> $this->config->get('httperr.UN_PROCESSABLE'),
						'msg'=>trans('general.something_wrong'),
						'alertclass'=>'alert-danger');
				}
			}
			else{
				return array(
					'status'=>$this->config->get('httperr.UN_PROCESSABLE'),
					'msg'=>trans('affiliate/settings/changepwd.same_as_old'),
					'alertclass'=>'alert-danger');
			}
		}
        return array('status'=>$this->config->get('httperr.UN_PROCESSABLE'),'msg'=>trans('affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning');
    }
	
	
	
	public function tran_update_password ($user_id, $postdata)
    {
	    $res = [];
		$res['status'] = $this->config->get('httperr.UN_PROCESSABLE');
		$res['msg'] = trans('affiliate/settings/security_pwd.missing_parameters');
		$res['alertclass'] = 'alert-warning';
		
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
			        $res['status'] = $this->config->get('httperr.SUCCESS');
			        $res['msg'] = trans('affiliate/settings/security_pwd.password_change');
			        $res['alertclass'] = 'alert-success';					
				}
				else
				{
			        $res['status'] = $this->config->get('httperr.UN_PROCESSABLE');
			        $res['msg'] = trans('general.something_wrong');
			        $res['alertclass'] = 'alert-danger';	
				}
			}
			else{
				$res['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				$res['msg'] = trans('affiliate/settings/security_pwd.same_as_old');
				$res['alertclass'] = 'alert-danger';
			}
		}
		return $res;
        //return json_encode(array('status'=>$this->config->get('httperr.UN_PROCESSABLE'),'msg'=>trans('affiliate/settings/security_pwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
	
	public function updateLineage($userSess,$pack_details){
		if(isset($userSess) && !empty($userSess) && !empty($pack_details)){
			$userTRInfo = $this->getUser_treeInfo(['account_id'=>$userSess->account_id]);			
			if(!empty($userTRInfo) && $userTRInfo->upline_id==0 && $userTRInfo->can_sponsor==0){
			 	$updateRightnode = $this->saveNew_linaeage(['account_id' => $userTRInfo->account_id, 'sponsor_id' => $userTRInfo->sponsor_id,'pack_details'=>$pack_details]);	
				if(!empty($updateRightnode)){
					return true;
				}
			}
		}	
	}
	
	public function updateProfile (array $arr)
    {        
        if (!empty($arr))
        {
            extract($arr);	
			$res = $res2 = '';
			if(!empty($uname) && !empty($this->userSess) && $uname != $this->userSess->uname) {
				$cnt = DB::table($this->config->get('tables.ACCOUNT_MST'))
						->where('uname','=', $uname)					
						->where('is_closed','=',$this->config->get('constants.OFF'))
						->where('is_deleted','=',$this->config->get('constants.OFF'))
						->count();
				if(!$cnt){
					$sdata = ['uname'=>$uname];
					$res = DB::table($this->config->get('tables.ACCOUNT_MST'))
						->where('account_id', $account_id)
						->update($sdata);
					
				} else {
					return ['uname'=>['Display name has already been taken']];					
				}
			} 
			
			$sdData = ['firstname'=>$firstname, 'lastname'=>$lastname, 'gender'=>$gender,  'pan_no'=>$pan_no, 'dob'=>date('Y-m-d', strtotime($dob))];			
            $res2 =  DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->where('account_id', $account_id)
                    ->update($sdData);
					
			if($res || $res2) return true;
        }
    }

    public function save_account($postdata) 
	{
		$val=[];
        $postdata['trans_pass_key'] = rand(1111,9999);
		$direct_sponsor_id = '';
        $rank = '';
        $level = '';
        $currencies = '';
        $account_code = '';
        $cdate = getGTZ(); 	   
	    $sponser_account_id = $postdata['sponser_account_id'];	   
	    $refUser_info = $this->getUser_treeInfo(['account_id'=>$sponser_account_id]);	 
		
	    $country_info = $this->lcObj->getCountry(['country_code'=>$postdata['country'],'allow_signup'=>true]);
		
        $sponsor_lineage = $refUser_info->sponsor_lineage.$refUser_info->account_id. '/';
		
		DB::beginTransaction();
        $activation_key = rand(1111, 9999) . time();
        /* --------Assigning the Post Values -------- */		
		$insert_account_mst['uname'] = $postdata['username'];
        $insert_account_mst['email'] = $postdata['email'];
		$insert_account_mst['mobile'] = $postdata['mobile'];
		$insert_account_mst['is_affiliate'] = $this->config->get('constants.ON');
        $insert_account_mst['pass_key'] = md5($postdata['password']);
//        $insert_account_mst['trans_pass_key'] = md5($postdata['trans_pass_key']);
        $insert_account_mst['last_active'] =  $cdate;
        $insert_account_mst['signedup_on'] = $cdate;
        $insert_account_mst['status'] = $this->config->get('constants.ON');
        $insert_account_mst['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');	
        $id = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->insertGetId($insert_account_mst);
	
        if ($id > 0) {
			$user_code = $this->commonObj->createUserCode($id);
			DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id','=',$id)
					->update(['user_code'=>$user_code]);
			
			$insert_account_tree['account_id'] = $id;
			$insert_account_tree['sponsor_id'] = $refUser_info->account_id;			
			$insert_account_tree['sponsor_lineage'] = $sponsor_lineage;		
			$insert_account_tree['nwroot_id'] = $refUser_info->nwroot_id;			
			$insert_account_tree['rank'] = 0;
			$insert_account_tree['level'] = 0;
			$insert_account_tree['created_on'] =  $cdate;
			DB::table($this->config->get('tables.ACCOUNT_TREE'))
                ->insertGetId($insert_account_tree);
		
            $firstname = $postdata['firstname'];
            $lastname = $postdata['lastname'];
            $insert_account_details = '';
            $insert_account_details['account_id'] = $id;            
            $insert_account_details['firstname'] = $firstname;
            $insert_account_details['lastname'] = $lastname;
			$insert_account_details['gender'] = $postdata['gender'];
			$insert_account_details['dob'] = date('Y-m-d',strtotime($postdata['dob']));
			$insert_account_details['gardian'] = $postdata['gardian'];
			$insert_account_details['marital_status'] = $postdata['marital_status'];	
			
            $udRes = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->insertGetId($insert_account_details);
					
            $insert_setting = '';
            $insert_setting['account_id'] = $id;
			$insert_setting['country_id'] = $country_info->country_id;
			$insert_setting['currency_id'] = $country_info->currency_id;
			$insert_setting['is_email_verified'] = $this->config->get('constants.ON');
			$insert_setting['referral_code'] = $this->commonstObj->createReferralCode();
			$insert_setting['activation_key'] = md5($activation_key.$id);
            $usRes_ = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
			                    ->insertGetId($insert_setting);
			
			//$postcode_info = $this->lcObj->getZipcodeDetails($postdata['postcode'],$country_info->country_id);
			
			//$postcode_info = $this->lcObj->getPincodeInfo(['pincode'=>$postdata['postcode'],'country'=>$postdata['country']]);
			
			$ad_setting = '';    
			$stateInfo = $this->lcObj->getState(0,$postdata['state']);
			$distInfo = $this->lcObj->getDistrict(0,$postdata['district']);
			$formated_address = [];
			if(!empty($distInfo)) {
				$formated_address[] = $distInfo->district;
				$val['district'] = $distInfo->district;
			}
			if(!empty($stateInfo)) {
				$formated_address[] = $stateInfo->state;
				$val['state'] = $stateInfo->state;		
			}
			if(!empty($country_info)) {
				$formated_address[] = $country_info->country_name;
				$val['country'] = $country_info->country_name;
			}
			$ad_setting = [];
					
			$ad_setting['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
			$ad_setting['address_type_id'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
			$ad_setting['relative_post_id'] = $id;
			$ad_setting['country_id'] = $country_info->country_id;
			$ad_setting['state_id'] = isset($postdata['state'])? $postdata['state']:0;						
			$ad_setting['district_id'] = isset($postdata['district'])? $postdata['district']:0;		
			$ad_setting['formated_address'] = !empty($formated_address)? implode(', ',$formated_address) :'';		
            $usRes = DB::table($this->config->get('tables.ADDRESS_MST'))
			                    ->insertGetId($ad_setting);	
			
			$val['account_id']= $id;
			$val['user_name'] = $postdata['username'];
			$val['user_email'] = $postdata['email'];
			$val['user_code'] = $user_code;
		    $val['country'] = $country_info->country_name;
			$val['activation_key']=$activation_key;
			$val['t_pin']=$postdata['trans_pass_key'];
			$val['sponser_email']=$refUser_info->email;
		    $val['sponser_details']= $refUser_info;
			
			($usRes) ? DB::commit() : DB::rollback();			
            return (object)$val;
        }
		else {
			DB::rollback();	
			return false;
		}
    }
	
	
	public function save_account_upgrade($postdata) 
	{
		$val=[];
        $postdata['trans_pass_key'] = rand(1111,9999);
		$direct_sponsor_id = '';
        $rank = '';
        $level = '';
        $currencies = '';
        $account_code = '';
        $cdate = getGTZ(); 	   
		if(session()->has('regSess') && session()->get('regSess')->account_id>0){
			$regSess = session()->get('regSess');
			//print_r($regSess);die;
			if(isset($regSess->country) && $regSess->country!=''){
				$postdata['country'] = $regSess->country_code;
			}
			if(isset($regSess->postal_code) && $regSess->postal_code!=''){
				$postdata['postcode'] = $regSess->postal_code;
			}
			$country_info = $this->lcObj->getCountries(['country_code'=>$postdata['country'],'operate'=>$this->config->get('constants.ON')]);			
			
			$sponser_account_id = $postdata['sponser_account_id'];
			$refUser_info = $this->getUser_treeInfo(['account_id'=>$sponser_account_id]);
			$sponsor_lineage = $refUser_info->sponsor_lineage.$refUser_info->account_id. '/';
			$activation_key = rand(1111, 9999) . time();
			/* --------Assigning the Post Values -------- */        
			if(isset($postdata['email'])){
				$upData['email'] = $postdata['email'];
				$resSess->email =  $postdata['email'];
			}		
			if(isset($postdata['mobile'])){
				$upData['mobile'] = $postdata['mobile'];
				$resSess->mobile =  $postdata['mobile'];
			}
			$user_code = $this->commonObj->createUserCode($regSess->account_id);			
				
			
			$upData['user_code'] = $user_code;		
			//$upData['is_affiliate'] = $this->config->get('constants.ON');        

			$res = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id','=',$regSess->account_id)
					->update($upData);
			
			
			$insert_ad['firstname'] = $postdata['firstname'];
			$insert_ad['lastname'] = $postdata['lastname'];
			$insert_ad['gender'] = $postdata['gender'];
			$insert_ad['dob'] = date('Y-m-d',strtotime($postdata['dob']));
			$insert_ad['gardian'] = $postdata['gardian'];
			$insert_ad['marital_status'] = $postdata['marital_status'];
			
			if ($res) {
				if(!DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
						->where('account_id','=',$regSess->account_id)->exists()){
					$insert_ad['account_id'] = $regSess->account_id;
					DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
						->insertGetId($insert_ad);
				} 
				else {
					DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
						->where('account_id','=',$regSess->account_id)
						->update($insert_ad);
				}
				
				$insert_account_tree['account_id'] = $regSess->account_id;
				$insert_account_tree['sponsor_id'] = $sponser_account_id;
				$insert_account_tree['sponsor_lineage'] = $sponsor_lineage;			
				$insert_account_tree['rank'] = 0;
				$insert_account_tree['level'] = 0;
				$insert_account_tree['created_on'] = $cdate;				
				DB::table($this->config->get('tables.ACCOUNT_TREE'))
					->insertGetId($insert_account_tree);
				
				if(!DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
					->where('account_id','=',$regSess->account_id)
					->exists()){						
					$insert_setting = '';
					$insert_setting['account_id'] =  $regSess->account_id;
					$insert_setting['country_id'] = $country_info->country_id;
					$insert_setting['currency_id'] = $country_info->currency_id;
					$insert_setting['activation_key'] = $activation_key;
					$usRes_ = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
										->insertGetId($insert_setting);
				}
				$ad_setting = '';    
				$stateInfo = $this->lcObj->getState(0,$postdata['state']);
				$distInfo = $this->lcObj->getDistrict(0,$postdata['district']);
				$formated_address = [];
				if(!empty($distInfo)) {
					$formated_address[] = $distInfo->district;
					$val['district'] = $distInfo->district;
				}
				if(!empty($stateInfo)) {
					$formated_address[] = $stateInfo->state;
					$val['state'] = $stateInfo->state;		
				}
				if(!empty($country_info)) {
					$formated_address[] = $country_info->country_name;
					$val['country'] = $country_info->country_name;
				}
				$ad_setting = [];
				        
				$ad_setting['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
				$ad_setting['address_type_id'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
				$ad_setting['relative_post_id'] = $regSess->account_id;
				$ad_setting['country_id'] = $country_info->country_id;
				$ad_setting['state_id'] = isset($postdata['state'])? $postdata['state']:0;						
				$ad_setting['district_id'] = isset($postdata['district'])? $postdata['district']:0;		
				$ad_setting['formated_address'] = !empty($formated_address)? implode(', ',$formated_address) :'';
				
				if(DB::table($this->config->get('tables.ADDRESS_MST'))
					->where('relative_post_id','=',$regSess->account_id)
					->where('post_type','=',$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
					->where('address_type_id','=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'))	
					->where(function($sqry){
						$sqry->where('country_id','=',0)
						->orWhere('state_id','=',0)
						->orWhere('district_id','=',0);
					})
					->exists()){
					$usRes = DB::table($this->config->get('tables.ADDRESS_MST'))
										->update($ad_setting);					
				} 
				else if(!DB::table($this->config->get('tables.ADDRESS_MST'))
						->where('relative_post_id','=',$regSess->account_id)
						->where('post_type','=',$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
						->where('address_type_id','=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'))						
						->exists()){
					$usRes = DB::table($this->config->get('tables.ADDRESS_MST'))
										->insertGetId($ad_setting);
				}
				
				$atdata = [];
				$atdata['referral_cnts'] = DB::Raw('referral_cnts+1');
				DB::table($this->config->get('tables.ACCOUNT_TREE'))
					->where('account_id','=',$sponser_account_id)
					->update($atdata);
				
				$val['account_id'] = $regSess->account_id;
				$val['username'] = $regSess->uname;
				$val['email'] = !empty($regSess->email)? $regSess->email : $postdata['email'];
				$val['mobile'] = !empty($regSess->mobile)? $regSess->mobile : $postdata['mobile'];
				$val['full_name'] = !empty($regSess->full_name)? $regSess->full_name : $postdata['firstname'].' '.$postdata['lastname'];
				$val['firstname'] = !empty($regSess->firstname)? $regSess->firstname : $postdata['firstname'];
				$val['lastname'] = !empty($regSess->lastname)? $regSess->lastname : $postdata['lastname'];
				$val['country'] = $country_info->country_id;				
				$val['activation_key'] =  $activation_key;
				$val['sponser_email'] = $refUser_info->email;
				$val['sponser_details'] = $refUser_info;				
				return (object)$val;
			}
		}
        return false;
    }

    public function findRightmostElement($arr = array()) {	
        $res = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as ut')
                ->where('ut.rank', '=', 3)
                ->whereRaw("ut.upline_id = (select upline_id from " . $this->config->get('tables.ACCOUNT_TREE') . " where account_id = " . $arr['account_id'] . ")")
                ->select('ut.account_id')
                ->get();
				
			//	print_r($res); die;
        if (!empty($res)) {
            return $res;
        }
    }
//
//    public function getNew_lineages($arr = array()) {
//        $new_rank = 0;
//        $response = '';
//        if (!empty($arr['upline_id'])) {
//            $res = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as um')
//                    ->where('um.upline_id', '=', $arr['upline_id'])                    
//                    ->orderBy('rank', 'desc')
//                    ->select(DB::raw('sponsor_id as sponsor_id ,min(rank) as min_rank ,max(rank) as max_rank,(count(upline_id)) as count,(SUBSTRING_INDEX(GROUP_CONCAT(account_id), ",", -1 )) as account_3g_id'))
//                    ->first();
//            if (!empty($res)) {
//                if ($arr['sponsor_id'] == $res->account_3g_id) {
//                    if ($res->count <= 2) {
//                        if ($res->min_rank == 3) {
//                            $new_rank = 1;
//                        } else {
//                            $new_rank = $res->min_rank + 1;
//                        }
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    } else if ($res->count == 3) {
//                        $response = $this->getNew_lineages(array('upline_id' => $res->account_3g_id, 'sponsor_id' => $arr['sponsor_id']));
//                    } else if ($res->count == 0) {
//                        $new_rank = 1;
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    } else if ($res->min_rank == 3) {
//                        $new_rank = 1;
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    }
//                } else if ($arr['sponsor_id'] != $res->account_3g_id) {
//                    if ($res->count == 0) {
//                        $new_rank = 3;
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    } else if ($res->count > 0) {
//                        if ($res->max_rank == 3) {
//                            $response = $this->getNew_lineages(array('upline_id' => $res->account_3g_id, 'sponsor_id' => $arr['sponsor_id']));
//                        } else {
//                            $new_rank = 3;
//                            $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                        }
//                    }
//                }
//            }
//        }
//        return $response;
//    }

    public function getNew_lineages($arr = array()) {
        $new_rank = 3;
        $op = NULL;
		
        if (!empty($arr['upline_id'])) {                    
            $res = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as um')
                ->where('um.upline_id',$arr['upline_id'])            
                ->orderBy('rank', 'desc')
                ->select(DB::raw('um.nwroot_id,sponsor_id as sponsor_id ,min(rank) as min_rank ,max(rank) as max_rank,(count(upline_id)) as count,(SUBSTRING_INDEX(GROUP_CONCAT(account_id), ",", -1 )) as account_3g_id,(select my_extream_right from '.$this->config->get('tables.ACCOUNT_TREE').' where account_id=um.upline_id) as my_extream_right'))
                ->first();			
            if (!empty($res)) {               
			
                if ($res->count <= 2) {
                    if($res->min_rank==1 && $res->max_rank==3){
                        $new_rank = 2;
                    } else if ($res->max_rank == 3) {
                        $new_rank = 1;
                    } else {
                        $new_rank = $res->max_rank + 1;
                    }                        
                    $op = $this->getUser_lineageInfo($arr['upline_id'],$new_rank);					
                } else if ($res->count == 3) {
                    $usrRes = $this->getUser_lineageInfo($arr['upline_id'],$new_rank);
                    if(!empty($usrRes) && $usrRes->my_extream_right>0){
                       $op = $this->getUser_lineageInfo($usrRes->my_extream_right,$new_rank);
                    }
					else {
                    	$op = $this->getNew_lineages(array('upline_id' => $res->account_3g_id, 'sponsor_id' => $arr['sponsor_id']));
					}
                } else if ($res->count == 0) {
                    $new_rank = 1;
                    $op = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
                } else if ($res->max_rank == 3) {
                    $new_rank = 1;
                    $op = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
                }				
            }           
        }
        return $op;
    }
	
	/*
	purpose:  update the new lineage to those who are purchase package 
	params: sponsor_id of new user,
	*/
	
    public function saveNew_linaeage($arr = array()) {     
		if($arr['account_id']>0 && $arr['sponsor_id']>0){
			$new_account_id = $arr['account_id'];
			$lineageInfo = $this->getNew_lineages(['upline_id'=>$arr['sponsor_id'],'sponsor_id'=>$arr['sponsor_id']]);			
			if(!empty($lineageInfo)){			
				$curDate = getGTZ();
				$upData = array(
							'upline_id'=>$lineageInfo->account_id,
							'rank'=>$lineageInfo->new_rank,
							'level'=>$lineageInfo->level,							
							'nwroot_id'=>$lineageInfo->nwroot_id,
							'can_sponsor'=>$this->config->get('constants.ON'));

				if(isset($arr['pack_details'])){					
					$upData['recent_package_id']= $arr['pack_details']->package_id;
					$upData['recent_package_purchased_on'] = $curDate;
				}			
				$tree_pos = $this->addnode($lineageInfo);				
				
				$upData['lft_node'] = $tree_pos->lft_node;
				$upData['rgt_node'] = $tree_pos->rgt_node;
				$upData['activated_on'] = $curDate;
				
				$upRes = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('account_id', '=', $new_account_id)                    
						->update($upData);
	
				if($upRes){
					if($lineageInfo->new_rank==3){
						$upRes2 = DB::table($this->config->get('tables.ACCOUNT_TREE'))
							->whereRaw("(account_id ='". $lineageInfo->account_id."' OR my_extream_right='".$lineageInfo->account_id."')")
							->update(array(
								'my_extream_right'=>$new_account_id
							));						
					}
					/* update sponsor data */	
					$spUpdata = [];
					$spUpdata['referral_paid_cnts'] = DB::Raw('referral_paid_cnts+1');
					$spUpRes = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('account_id', '=', $arr['sponsor_id'])          
						->update($uplinedata);
						
					/* update upline data */
					$uplinedata = [];
					$uplinedata['noof_directs'] = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('upline_id', '=', $lineageInfo->account_id)                    
						->count();
				
					$directsUpRes = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('account_id', '=', $lineageInfo->account_id)                    
						->update($uplinedata);				
					
					$usrInfo = $this->session->get($this->sessionName);
					$usrInfo->can_sponsor = $this->config->get('constants.ON');
					$this->session->put($this->sessionName, $usrInfo);
				   	return true;
			   }
			   else {
				   /* Couldn't able to update user lineage info */
				   return 3;
			   }
			}
			else {
				/* Couldn't able to get lineage info */
				return 2;
			}
		}
		else  {
			/* datas missing */
			return 5;
		}		
    }

    public function getUser_lineageInfo($account_id = '',$new_rank='') {
        if (!empty($account_id)) {
            $result = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as um')
                    ->where('um.account_id', '=', $account_id)
                    ->select('um.level','um.account_id','um.nwroot_id','um.lft_node','um.rgt_node', 'um.my_extream_right')
                    ->first();
            if (!empty($result)) {
                $result->level += 1;
                $result->new_rank = $new_rank;
                $result->account_id = $result->account_id;
                return $result;
            }
        }
        return NULL;
    }
	
	public function getUserinfo($params = array()) {		
        if (!empty($params) && is_array($params)){			
			extract($params);
			if((isset($account_id) && $account_id > 0) || (isset($uname) && $uname != NULL)) {
				$query = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as am')
						->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ad', 'ad.account_id', '=', 'am.account_id')					
						->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'am.account_id')
						->join($this->config->get('tables.ACCOUNT_TREE') . ' as atr', 'atr.account_id', '=', 'am.account_id')
						
						->leftjoin($this->config->get('tables.AFF_RANKING_LOOKUPS') . ' as ar', 'ar.af_rank_id', '=', 'atr.pro_rank_id')
						->leftjoin($this->config->get('tables.AFF_TYPES_LANG').' as aty',function($join){
							$join->on('aty.aff_type_id', '=', 'atr.aff_type_id')
							->where('aty.lang_id','=',$this->config->get('app.locale_id'));						
						})
						->leftjoin($this->config->get('tables.AFF_PACKAGE_LANG') . ' as apk', function($join){
							$join->on('apk.package_id','=', 'atr.recent_package_id')
							->where('apk.lang_id','=',$this->config->get('app.locale_id'));
						})
						->leftjoin($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','atr.sponsor_id')
						->leftjoin($this->config->get('tables.ACCOUNT_DETAILS') . ' as spad','spad.account_id','=','spam.account_id')		
   /*new code upline*/  ->leftjoin($this->config->get('tables.ACCOUNT_MST') . ' as upm','upm.account_id','=','atr.upline_id')
/*end */				->leftjoin($this->config->get('tables.ACCOUNT_DETAILS') . ' as upmd','upmd.account_id','=','upm.account_id')						
						->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
						->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')						
						->leftjoin($this->config->get('tables.GENDER_LANG') . ' as gl ', function($join){
							$join->on('gl.gender_id', '=', 'ad.gender')
							->where('gl.lang_id','=',$this->config->get('app.locale_id'));	
						})
						->leftjoin($this->config->get('tables.MARTIAL_STATUS_LANG') . ' as mts ', function($join){
							$join->on('mts.marital_status_id', '=', 'ad.marital_status')
							->where('mts.lang_id','=',$this->config->get('app.locale_id'));	
						})
						->leftjoin($this->config->get('tables.ADDRESS_MST') . ' as adm ', function($join){
							$join->on('adm.relative_post_id', '=', 'ad.account_id')
							->where('adm.post_type','=',$this->config->get('constants.ADDRESS.PRIMARY'));
						})
						->select(DB::raw('am.account_id,am.user_code,am.uname,am.email,atr.created_on,atr.is_kyc_verified,atr.kyc_status,atr.kyc_submitted_on,atr.kyc_verified_on,atr.qv,ad.firstname,ad.lastname,ad.dob,ad.pan_no,ad.gender as gender_id,concat(ad.firstname," ",ad.lastname) as full_name,mts.marital_status_id,mts.marital_status,ad.gardian,ar.rank,aty.aff_type,apk.package_name,lc.phonecode,lc.has_pancard,am.mobile,ad.profile_img,ad.home_phone,ad.office_phone,adm.formated_address,adm.flatno_street,adm.landmark,adm.address,adm.postal_code,lc.country,adm.formated_address,gl.gender,lc.country_id,lc.country,spam.uname as sponsor_uname,spam.user_code as sponsor_code,concat_ws(\' \',spad.firstname,spad.lastname) as sponsor_name,upm.uname as upline_uname,upm.user_code as upline_code,concat_ws(\' \',upmd.firstname,upmd.lastname) as upline_name')); 

				if ($account_id){
					$query ->where('am.account_id', '=', $account_id);
				}else {
					$query ->where('am.uname', '=', $uname);
				}	   
				
				$query->where('am.is_deleted',$this->config->get('constants.OFF'));
				
				$res =  $query ->first();			
				if ($res) {
					$res->gardian = ucwords($res->gardian);
					$res->full_name = ucwords($res->full_name);
					/* $res->sponsor_name = ucwords($res->sponsor_name); */
					$res->sponsor_uname = $res->sponsor_uname;
					$res->created_on = showUTZ($res->created_on);
					$res->kyc_status = !empty($res->kyc_status) ? json_decode(stripslashes($res->kyc_status)):'';
					$res->kyc_submitted_on = !empty($res->kyc_submitted_on)? showUTZ($res->kyc_submitted_on, 'd-M-Y'):'';
					$res->kyc_verified_on = !empty($res->kyc_verified_on)? showUTZ($res->kyc_verified_on, 'd-M-Y'):'';
					//echo"<pre>";print_r($res);exit;
					return $res;
				}
			}
		}
        return NULL;
    }
	
	public function getSponsorInfo($params){
		$account_id = 0;
        if(!empty($params)){
			extract($params);
			if($account_id>0){
				$qry = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
					->join($this->config->get('tables.ACCOUNT_TREE') . ' as spt','spt.account_id','=','act.sponsor_id')
					->join($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','act.sponsor_id')
					->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as spp','spp.account_id','=','spam.account_id')
					->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','act.sponsor_id');
				$qry->where('act.account_id','=',$account_id);				
				$qry->select(DB::Raw("spt.can_sponsor,spp.country_id,spt.account_id,spam.account_type_id as account_type_id,spam.uname as uname,spam.status,spam.block,spam.status,spam.email,concat_ws(' ',acd.firstname,acd.lastname) as full_name,IF(spt.upline_id=0 AND spt.sponsor_id=0 AND spt.lft_node=1 ,1,0) as is_root_account,spt.referral_cnts,spt.referral_paid_cnts"));
				
				$res = $qry->first();					
				if ($res) {			
					return $res;				
				}
			}
        }
		return '';
	}
	
	public function getUser_treeInfo($params = array()) {
		extract($params);
        if(!empty($params)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
			->join($this->config->get('tables.ACCOUNT_MST') . ' as am','am.account_id','=','act.account_id')			
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','act.account_id')		
			->leftJoin($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','act.sponsor_id');
			
				$qry->where('act.account_id','=',$account_id);
				$qry->where('am.is_deleted',$this->config->get('constants.OFF'));
			    $qry->select(DB::Raw("act.can_sponsor,am.trans_pass_key,am.is_affiliate,am.signedup_on,act.account_id,am.account_type_id,am.uname,am.status,am.block,am.status,am.email,concat_ws(' ',acd.firstname,acd.lastname) as full_name,act.upline_id,act.sponsor_id,act.referral_cnts,act.my_extream_right,act.lft_node,act.rgt_node,act.nwroot_id,act.rank,act.level,IF(FLOOR((act.rgt_node - act.lft_node)/2)>0,FLOOR((act.rgt_node - act.lft_node)/2),0) as my_team_cnt,act.sponsor_lineage,spam.user_code as referrer_code,spam.uname as referrer_name,spam.email as referrer_email"));
				$res = $qry->first();					
		    if ($res) {			
                return $res;				
            }
        }
		return NULL;
    }

	public function usercheck_for_fundtransfer($username)
	{
		//echo strpos($username,'@');exit;
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
		$op['status'] 	= 'error';
		$op['msg'] 		= trans('affiliate/wallet/fundtransfer.invalid_username');
		$op['user_id']  = 0;
		if($username != '')
		{
			$res = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
						->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
						->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id');
						if(isset($user_code) && !empty($user_code)){
							$res->where('um.user_code', '=', $user_code);
						}
						else if(isset($email) && !empty($email)){
							$res->where('um.email', '=', $email);
						}
						else{
							$res->where('um.uname', '=', $uname);
						}						
						$res->where('um.is_deleted', '=', 0)
							->where('um.is_affiliate', '=',1)
							->where('um.status', '=',1)
							->where('um.block', '=',0)
							->where('ap.country_id','=',$this->userSess->country_id)
							->where('um.account_id','!=',$this->userSess->account_id)
							->whereIn('um.account_type_id', [$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER_EMP')]);
					
			$result = $res->first();
			if (!empty($result))
			{
				$op['status'] = 'ok';
				$op['msg'] = trans('affiliate/wallet/fundtransfer.user_available');
				$op['account_id'] = $result->account_id;				
				$op['uname'] = $result->uname;
				$op['user_code'] = $result->user_code;
				$op['full_name'] = $result->firstname.' '.$result->lastname;
				$op['email'] = $result->email;
				$op['mobile'] = $result->mobile;
			}
			else
			{
				$op['status'] = 'error';
				$op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
			}
			return $op;
		}
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
	


    public function account_check($username = 0, $optArrary = array()) {
	
        if ($username) {
            $lineage = '';
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where("uname", $username)                    
                    ->where('is_deleted', 0)
                    ->first();

            $existscheck = $referralcheck = $account_id = 0;
            $reqfor = '';
            $loguserlineage = '';
            $useravailablility = '';
            if (count($optArrary) > 0) {
                $existscheck = isset($optArrary['existscheck'])? $optArrary['existscheck']: 0;
                $referralcheck = isset($optArrary['referralcheck'])? $optArrary['referralcheck']:0;
                $account_id = isset($optArrary['account_id']) ? $optArrary['account_id'] : '';
                $reqfor = isset($optArrary['reqfor'])? $optArrary['reqfor'] : '';
                $useravailablility = isset($optArrary['useravailablility']) ? $optArrary['useravailablility'] : '';
                $loguserlineage = isset($optArrary['loguserlineage'])? $optArrary['loguserlineage']:0;
            }
			
            if (empty($result)) {
                if (isset($existscheck) && $existscheck == 1) {
                    $status['status'] = 'error';
                    $status['msg'] = Lang::get('affiliate/signup.pls_entr_valid_uname');
                } elseif (isset($username) && !(preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', $username))) {
                    $status['status'] = 'error';
                    $status['msg'] = Lang::get('affiliate/signup.uname_starts_alphhabets');
                } else {
                    $status['status'] = 'ok';
                    $status['msg'] = trans('affiliate/signup.uname_available');
                }				
            } else { 
                if ($referralcheck == 1) {                    
                    if ($result->account_id == $account_id) {
                        $status['status'] = 'error';
                        $status['msg'] = Lang::get('affiliate/signup.pls_select_member');
                        return $status;
                    }                    
                } 
				else if ($useravailablility == 1 && $reqfor == 'reg') 
				{
                    $status['status'] = 'error';
                    $status['msg'] = trans('affiliate/signup.uname_already_exist');
                } 
				else if ($existscheck == 1) 
				{
                    $status['status'] = 'ok';
                    $status['msg'] = '';
                    $status['account_currency_bal'] = json_encode($this->get_account_currency_bal($result->account_id));
                } 
				else 
				{
                    $status['status'] = 'error';
                    $status['msg'] = Lang::get('affiliate/signup.uname_already_exist');
                }
                $status['account_id'] = $result->account_id;
                $status['uname'] = $result->uname;
            }
        }
		return $status;
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
	
	public function get_full_Tree ($account_id = 0)
    {
       	/*
        SELECT node.account_id
		FROM account_tree AS node,
				account_tree AS parent
		WHERE node.lft_node BETWEEN parent.lft_node AND parent.rgt_node
				AND parent.account_id = 6
		ORDER BY node.lft_node
        */
        $qry = DB::table(DB::Raw($this->config->get('tables.ACCOUNT_TREE').' as node,'.$this->config->get('tables.ACCOUNT_TREE').' as parent'))
                ->orderBy('node.lft_node', 'ASC');
        $qry->whereBetween('node.rgt_node', ['parent.lft_node','parent.lft_node']);
        $qry->select(DB::raw('node.account_id'));
        if ($account_id > 0)
        {
            $qry->where('parent.account_id', $account_id);
        }
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
	
	/*public function addnode_org($parent_info)
    {
        $child_exist = 0;
        $increament_val = 2;        
        if (!empty($parent_info))
        {			
            $child_exist = ($parent_info->lft_node == $parent_info->rgt_node-1) ? false :true;
            if ($child_exist)
            {               
				$updateFrom = $parent_info->rgt_node;
                $lft_node = $parent_info->rgt_node+1;
                $rgt_node = $lft_node + 1;			
            }
            else
            {
                $updateFrom = $parent_info->lft_node;
                $lft_node = $parent_info->lft_node+1;
                $rgt_node = $lft_node + 1;
            }

            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('rgt_node', '>', $updateFrom)
					->where('nwroot_id', '>', $parent_info->nwroot_id)					
                    ->where('is_deleted', '=', $this->config->get('constant.OFF'))
                    ->increment('rgt_node', $increament_val);

            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('lft_node', '>', $updateFrom)
					->where('nwroot_id', '>', $parent_info->nwroot_id)	
                    ->where('is_deleted', '=', $this->config->get('constant.OFF'))
                    ->increment('lft_node', $increament_val);    
            
            // update query will come here
            return ['lft_node'=>$lft_node,'rgt_node'=>$rgt_node];
        }
    }*/
   
    public function addnode($parent_info)
    {
        $child_exist = 0;
        $increament_val = 2;        
        if (!empty($parent_info))
        {			
           $child_exist = ($parent_info->lft_node == $parent_info->rgt_node-1) ? false :true;		   
           //print_r($parent_info);
		   if ($child_exist)
            {
                if($parent_info->new_rank==1){
					$updateFrom = $parent_info->lft_node + 1;
					$lft_node = $updateFrom;
					$rgt_node = $lft_node + 1;					
				} 
				else if($parent_info->new_rank==2){
					$cildRes = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
							->where('act.upline_id','=',$parent_info->account_id)
							->select('act.lft_node','act.rgt_node')
							->orderby('act.lft_node','ASC')
							->get();
							
					if(count($cildRes)==2){
						$updateFrom = $cildRes[0]->rgt_node+1;
						$lft_node = $updateFrom;
						$rgt_node = $lft_node + 1;
					} 
					else {
						$updateFrom = $parent_info->rgt_node;
						$lft_node = $parent_info->rgt_node;
						$rgt_node = $lft_node + 1;
					}
				} 
				else if($parent_info->new_rank==3){
					$updateFrom = $parent_info->rgt_node;
					$lft_node = $parent_info->rgt_node;
					$rgt_node = $lft_node + 1;
				}				
            }
            else
            {
                $updateFrom = $parent_info->rgt_node;
                $lft_node = $parent_info->rgt_node;
                $rgt_node = $lft_node + 1;
            }
			
            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('rgt_node', '>=', $updateFrom)
					->where('nwroot_id', '=', $parent_info->nwroot_id)					
                    ->increment('rgt_node', $increament_val);

            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('lft_node', '>=', $updateFrom)
					->where('nwroot_id', '=', $parent_info->nwroot_id)	                    
                    ->increment('lft_node', $increament_val);
            
            /* update query will come here */           
            return (object)['lft_node'=>$lft_node,'rgt_node'=>$rgt_node];
        }
    }
	
	/* Kyc Upload */
	public function check_account_verification_count ($arr = array())
    { // echo"<pre>";print_r($arr);exit;
        $datalist_qry = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'uv.document_type_id')
                ->where('uv.account_id', $arr['account_id'])           
                ->whereIn('uv.status_id', array(0,1))
                ->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->whereIn('dt.proof_type', $arr['prooftypes'])
                ->select(DB::Raw('distinct(proof_type) as proof_type_id,count(distinct(proof_type)) as cnt'))
                ->groupby('dt.proof_type');
        if (isset($arr['document_type']) && is_array($arr['document_type']))
        {
            $datalist_qry->whereIn('uv.document_type_id', $arr['document_type']);
        }
        if (isset($arr['prooftypes']) && is_array($arr['prooftypes']) && count($arr['prooftypes']) == 1)
        {  
            $datalist = $datalist_qry->first();
        }
        else
        {
            $datalist = $datalist_qry->get();
			//print_r( $datalist );exit;
        }		
        $data = array();
        if (!empty($datalist))
        {
            if (is_array($datalist) && count($datalist) > 0)
            {
                foreach ($datalist as $item)
                {
                    $data[$item->proof_type_id] = $item->cnt;
                }
            }
            else
            {
                $data[$datalist->proof_type_id] = $datalist->cnt;
            }	
			//echo"<pre>";print_r($data);exit;
            return $data;
        }
        return false;
    }
	
	public function save_account_upload ($arr = array(),$userSess)
    {  
        $res = false;		
        if (!empty($arr))
        {
            $res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                    ->insert($arr);
        }
        if ($res)
        {
            if ($userSess->is_verified == $this->config->get('constants.OFF'))
            {
                DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
                        ->where('account_id', $arr['account_id'])
                        ->update(array(
                            'is_verified'=>$this->config->get('constants.OFF')));
            }
        }
        return $res;
    }
	
	public function account_kycdoc($data)
    {
        extract($data);
        $proof_types = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', ' = ', 'uv.document_type_id')
                ->where('uv.account_id', $account_id)
				->where('uv.status', $this->config->get('constants.ACTIVE'))
				->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))                                
                ->havingRaw('count(uv.uv_id)>0')
                ->groupby('dt.proof_type')
                ->lists('dt.proof_type');		
					
		/*$prflang = DB::table($this->config->get('tables.PROOF_DOCTYPES_LANG').' as prfl')
					->where('prfl.proof_type_id','=','dt.proof_type')
					->select('desc');	*/
		
        $query = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')                
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt',"dt.document_type_id",'=',"uv.document_type_id")
				->join($this->config->get('tables.PROOF_DOCTYPES').' as pdt', 'pdt.id', ' = ', 'dt.proof_type')
				->select(DB::raw("uv.uv_id,uv.account_id,uv.path,uv.document_type_id,uv.status,uv.verified_on,uv.cancelled_on,uv.created_on,uv.updated_on,uv.comments, dt.proof_type,(select a.desc FROM ".$this->config->get('tables.DOCUMENT_TYPES_LANG')." as a WHERE  a.lang_id = 1 AND a.document_type_id = uv.document_type_id) as doc_type,(select b.desc FROM ".$this->config->get('tables.PROOF_DOCTYPES_LANG')." as b WHERE  b.lang_id = 1  AND b.proof_type_id = pdt.id) as proof_type_name"))
                ->where('uv.account_id', $account_id)				
                ->where(function($fil) use($proof_types)
                {
                    $fil->where(function($fil1) use($proof_types)
                    {
                        $fil1->whereIn('dt.proof_type', $proof_types)
                        ->where('uv.status', $this->config->get('constants.ACTIVE'));
                    })
                    ->orWhere(function($fil1) use($proof_types)
                    {
                        $fil1->whereNotIn('dt.proof_type', $proof_types);
                    });
                })
                ->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->orderby('uv.created_on', 'DESC');
        return $query->get();
    }
	
	public function get_document_types($arr = array())
    {
        extract($arr);
        $result = DB::table($this->config->get('tables.DOCUMENT_TYPES').' as dt')
                ->leftJoin($this->config->get('tables.DOCUMENT_TYPES_LANG').' as dtl', function($subquery)
                {
                    $subquery->on('dt.document_type_id', '=', 'dtl.document_type_id')
                    ->where('dtl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('dt.proof_type', $proof_type)
                ->where('dt.status', $this->config->get('constants.ACTIVE'))
                ->select(DB::raw('dtl.document_type_id,dtl.desc as doctype_name'))
                ->get();
        return ($result) ? $result : NULL;
    }		

	public function update_account_activationkey($account_id,$sdata)	
	{		
	    $res ='';
		if($account_id >0 && !empty($sdata))
		{
			$res = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
					->where('account_id','=',$account_id)
					->update($sdata);
			/* if (!empty($res) && isset($res))
			{
			  return json_encode([
			   'msg'=>trans('affiliate/settings/security_pwd.forgot_pin_verifymsg'),
			   'alertclass'=>'alert-error',
			   'status'=> 'ok']);
			} */
		}  
		return $res;
    }

	 public function amount_with_decimal ($amt)
    {
	
        $amt = floatval(trim($amt));
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
            if ($decimal_places > 8)
                $decimal_places = 8;
        }
        return number_format($amt, $decimal_places, '.', ',');
    }
	

	public function get_user_verification_total ($account_id)
    {
        $result = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                ->where('status_id', 1)
                ->where('is_deleted', 0)
                ->where('account_id', $account_id)
                ->get();
			//	print_r($result); exit;
        return count($result);
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
	
	public function saveNominee($account_id,$data=array())
    {
		if ($account_id>0 && !empty($data))
		{
			$sdata['fullname'] = $data['fullname'];
			$sdata['gender_id'] = $data['gender'];
			$sdata['dob'] = date('Y-m-d',strtotime($data['dob']));
			$sdata['relation_ship_id'] = $data['relation_ship_id'];			
			
			if(DB::table($this->config->get('tables.ACCOUNT_NOMINEES'))					
					->where('is_deleted', 0)
					->where('account_id', $account_id)					
					->exists()){
				$sdata['updated_on'] = getGTZ();
				$res = DB::table($this->config->get('tables.ACCOUNT_NOMINEES'))					
					->where('is_deleted', 0)
					->where('account_id', $account_id)					
					->update($sdata);
					
				if($res) {
					$op['nominee'] = $this->getUserNominees($account_id);
					$op['nominee']->dob = date('d M, Y',strtotime($op['nominee']->dob));
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.nominee.updated');
					$op['msgtype'] = 'success';
				}
				else {
					$op['nominee'] = $this->getUserNominees($account_id);
					$op['nominee']->dob = date('d M, Y',strtotime($op['nominee']->dob));
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.nominee.no_change');
					$op['msgtype'] = 'warning';
				}
			}
			else {				
				$sdata['account_id'] = $account_id;
				$sdata['created_on'] = getGTZ();
				$res = DB::table($this->config->get('tables.ACCOUNT_NOMINEES'))								
					->InsertGetId($sdata);
				if($res) {
					$op['nominee'] = $this->getUserNominees($account_id);
					$op['nominee']->dob = date('d M, Y',strtotime($op['nominee']->dob));
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.nominee.save');
					$op['msgtype'] = 'success';
				}
				else {
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = trans('affiliate/account.nominee.not_saved');
					$op['msgtype'] = 'danger';
				}
			}
		}
		return $op;
    }	
	
	/* my_profile */
	public function my_profile($arr = array())
	{
	    $profile_info=array();
	    extract($arr);
		if (!empty($account_id))
		{   
			//$tree_info = $this->getUser_treeInfo(['account_id'=>$account_id]);

			$userdetails = $this->getUserinfo(['account_id'=>$account_id]);	

			if(!empty($tree_info) && !empty($userdetails))
			{
	          //  $profile_info['tree_info'] = $tree_info;
                $profile_info['userdetails'] = $userdetails ;
			    return $profile_info;
            }else{
			   return false;
			}
		}
		return NULL;
	}	
	
	/* profile_image upload */
	public function update_profile_image($account_id, $filename) 
	{
        $status = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                   ->where('account_id', $account_id)
                   ->update(array(
                      'profile_image' => $filename));
        return $status;
    }
	
	public function remove_profile_image ($account_id)
    {   
	    $default_image = $this->config->get('constants.DEFAULT_IMAGE');
        $profile_image = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                ->where(array(
                    'account_id'=>$account_id))
                ->pluck('profile_image');
				
        if (!empty($profile_image) && $profile_image[0] !=  $default_image)
        {
            $status = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->where('account_id', $account_id)
                    ->update(array(
                'profile_image'=>$this->config->get('constants.DEFAULT_IMAGE')));
            File::delete($this->config->get('constants.PROFILE_IMAGE_PATH').$profile_image[0]);
            return true;
        }
        return false;
    }

	public function referral_user_check ($username)
    {
        if (isset($username))
        {				
		$result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
			        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as acs', 'acs.account_id', '=', 'ad.account_id')
					->join($this->config->get('tables.ACCOUNT_TREE').' as act', 'act.account_id', '=', 'ad.account_id')
					->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'acs.country_id')
                    ->where('am.uname','=',$username) 
                    ->whereIn('am.account_type_id',  [2,3])
                    ->where('am.status', 1)
					->where('act.can_sponsor', 1)
                    ->where('am.block', 0)
                    ->where('am.is_affiliate', 1)
                    ->where('am.login_block', 0)
                    ->where('am.is_deleted', 0)
            ->select('am.account_id','am.uname','am.user_code','account_type_id','am.email','am.mobile','ad.firstname','ad.lastname','lc.country as country_name','lc.phonecode', 'acs.is_verified')
		    ->first();					
            
			if (empty($result))
            {
             $status['status'] = 'err';
             $status['msg'] = trans('affiliate/general.sponsor_not_found');
            }
            else
            {
                $status['status'] = 200;
                $status['sponser_account_id'] = $result->account_id;
                $status['account_type_id'] = $result->account_type_id;
                $status['sponser_name'] = $result->uname;
				$status['sponser_id'] = $result->user_code;
                $status['sponser_email'] = $result->email;
                $status['sponser_fullname'] = $result->firstname." ".$result->lastname;
                $status['sponser_country'] = $result->country_name;
                $status['sponser_mobile'] = $result->phonecode.' '.$result->mobile;
            }
        }
        else
        {
            $status['status'] = 'err';
            $status['msg'] = trans('affiliate/general.sponsor_id_not_exist');
        }
        return $status;
    }
    public function profilepin_check($account_id){		
		
		 $qry= DB::table($this->config->get('tables.ACCOUNT_MST'))
		       ->select('trans_pass_key')
		       ->where('account_id', $account_id)
			   ->first();
            return $qry;
        }
    
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
	
	function update_verification_code($account_id,$key){
		if(!empty($account_id)&& !empty($key)){			
			return   DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
							->where('account_id', $account_id)
							->update(['email_verification_key'=>$key]);
		}
	}
	
	public function signup_acverify($login_id){
		$res = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
				->leftJoin($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'um.account_id')
				->leftJoin($this->config->get('tables.ACCOUNT_PREFERENCE').' as acs', 'acs.account_id', '=', 'um.account_id')				
				->leftJoin($this->config->get('tables.ADDRESS_MST').' as adm',function($join){
					$join->where('adm.post_type', '=', $this->config->get('constants.ADDRESS_POSTTYPE.ACCOUNT'))
						->where('adm.address_type_id', '=', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'))
						->on('adm.relative_post_id', '=', 'um.account_id');
				})
				->leftJoin($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'acs.country_id')
				->where(function($sub) use($login_id){
					$sub->where('um.email', $login_id)
					->orWhere('um.mobile', $login_id);
				})				
				->select('um.*','ad.firstname','ad.lastname','adm.country_id','adm.state_id','adm.country_id','lc.iso2 as country_code','lc.country','lc.country','adm.postal_code')				
				->first();
		
		if(!empty($res)){	
		
			if($res->is_affiliate==0 && !in_array($res->account_type_id,[$this->config->get('constants.ACCOUNT_TYPE.ADMIN'),$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')])){
				$sesdata = [
					'account_id' => $res->account_id,
					'user_code' => $res->user_code,   
					'uname' => $res->uname,                  
					'full_name' => $res->firstname.' '.$res->lastname,
					'firstname' => $res->firstname,
					'lastname' => $res->lastname,
					'pass_key' => $res->pass_key,
					'email' => $res->email,
					'mobile' => $res->mobile,
					'is_affiliate' => $res->is_affiliate,				
					'account_type_id' => $res->account_type_id,						
					'country' => $res->country,				
					'country_id' => $res->country_id,						                
					'country_code' => $res->country_code,
					'postal_code' => $res->postal_code];
				
				$regFields = [];				
				if(empty($res->firstname)) $regFields['firstname'] = '';
				if(empty($res->lastname)) $regFields['lastname'] = '';
				if(empty($res->email)) $regFields['email'] = '';
				if(empty($res->mobile)) $regFields['mobile'] = '';
				if(empty($res->country_code)) $regFields['country'] = '';				
				
				$sesdata['regMissingFlds'] = $regFields;
				
				$this->session->put('regSess', (object)$sesdata);	
				return ['allowreg'=>1,'exist'=>1];
			} 
			else {
				return ['allowreg'=>0,'exist'=>1,'msg'=> 'We found an account already exist with this Email/Mobile','msgclass'=>'warning'];
			}
		}
		else {
			
			return ['allowreg'=>1,'exist'=>0];
		}
	}
	
	
	public function verify_email ($key)
    {   	
		$account_id = 0 ;
		if(!empty($key)){
			
			if(is_numeric($key)){
				$account_id = $key;
				$key = '';
			}			
			
			$qry = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))								
					->select('account_id','email_verification_key');
			
			if(!empty($key)) 
			{
				$qry->where('email_verification_key', $key);
			}
			else if(!empty($account_id)) 
			{
				$qry->where('account_id', $account_id);
			}
			$res = $qry->first(); 							
			if (!empty($res))
			{
				$upres = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
								->where('account_id', $res->account_id)
								->update(array('is_email_verified'=>$this->config->get('constants.ON')));
				
				if($upres && !empty($this->userSess) && $res->account_id==$this->userSess->account_id )
				{
					$usrInfo = $this->session->get($this->sessionName);
					$usrInfo->is_email_verified = $this->config->get('constants.ON');
					$this->session->put($this->sessionName, $usrInfo);
					return 1;
				} 
				else if($upres && (empty($this->userSess)  || (!empty($this->userSess) && $res->account_id!=$this->userSess->account_id) ) ){
					return 2;
				} 
				else {
					return 3; 
				}
			}     
		}
        return 0;        
    }	
	
	public function profile_image_upload ($arr=array())
    {
        extract($arr);
        $res = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                ->where('account_id', $account_id)
                ->update(['profile_img'=>$docpath]);
        return $res;
    }	
	
	public function get_affkyc_proof_types ($proof_type=NULL)
    {
        $qry = DB::table($this->config->get('tables.DOCUMENT_TYPES'));
			if($proof_type){
				$qry->where('proof_type',$proof_type);
			} 
		    $res = $qry->get();
        return !empty($res)? $res : [];
    }	
	
	
	public function get_affkyc_doc($account_id){
		$res = DB::table($this->config->get('tables.AFF_KYC_DOCUMENTS').' as kd')					
					->where('account_id',$account_id)
					->first();
					
		return !empty($res)? $res : [];		
	}
	
	public function changeEmail($account_id,$arr=array()){
		if ($account_id && $arr['new_email'])
        {
			$res = DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('account_id', $account_id)
                            ->update(['email'=>$arr['new_email']]);
			if($res){				
				return true;
			}			
        }
		return false;
	}
	
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
	public function get_ranks($arr){
		extract($arr);
		$myrank = [];
		//print_R(date('Y', strtotime("-30 day", strtotime(date('Y-m')))));exit;
				$res  = DB::table($this->config->get('tables.AFF_RANKING_LOOKUPS').' as lk')
						->leftjoin($this->config->get('tables.ACCOUNT_AF_RANKING_LOG').' as rl',function($j)use($account_id){
							$j->on('rl.af_rank_id','=','lk.af_rank_id')
							->where('rl.account_id','=',$account_id)							
							->whereIn('status',[1])
							->whereIn('is_verified',[1])
							->where('lang_id','=',1);
					})	
				->select('lk.rank','rl.af_rank_id','rl.gen_1','rl.gen_2','rl.gen_3','rl.status','rl.is_verified');
			$result = $res->get();	
			
		//echo '<pre>';print_R($result);exit;				
		 $op = ['ranks'=>$result,'myrank'=>$myrank];
		 return $op;
	}
	
	public function saveRegisterTemp($token,$data){		
		$sdata='';
		$sdata['regtoken'] = $token;
		$sdata['regdata'] = addslashes(json_encode($data));
		$sdata['create_on'] = getGTZ();
		$res = DB::table($this->config->get('tables.ACCOUNT_TEMP'))								
			->InsertGetId($sdata);
		return ($res)? true : false;
	}
	
	public function getRegisterTemp_data($token){
		$decyToken = $this->commonObj->decryptSharedToken($token);
		$res = DB::table($this->config->get('tables.ACCOUNT_TEMP').' as atmp')					
					->where('atmp.regtoken',$decyToken)
					->first();
		if(!empty($res)){
			$res->regdata = json_decode(stripslashes($res->regdata),true	);
		}
		return !empty($res)? $res : false;		
	}
}