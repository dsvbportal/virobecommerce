<?php
namespace App\Models\Franchisee;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;
use App\Models\Commonsettings;
use CommonLib;
class UserModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();				
		$this->lcObj = new LocationModel;
    }
   public function save_user($postdata) 
	{
		$val=[];
        $postdata['trans_pass_key'] = rand(1111,9999);
        $currencies = '';
        $account_code = '';
        $cdate = getGTZ(); 	   
	    $country_info = $this->lcObj->getCountry(['country_id'=>$postdata['country'],'allow_signup'=>true]);
        $activation_key = rand(1111, 9999) . time();
        /* --------Assigning the Post Values -------- */
	DB::beginTransaction();
		$insert_account_mst['uname'] = $postdata['username'];
        $insert_account_mst['email'] = $postdata['email'];
		$insert_account_mst['mobile'] = $postdata['mobile'];
        $insert_account_mst['pass_key'] = md5($postdata['password']);
        $insert_account_mst['trans_pass_key'] = md5($postdata['trans_pass_key']);
        $insert_account_mst['last_active'] =  $cdate;
        $insert_account_mst['signedup_on'] = $cdate;
        $insert_account_mst['status'] = $this->config->get('constants.ON');
        $insert_account_mst['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE');	
        $id = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->insertGetId($insert_account_mst);
	
        if ($id > 0) {
			$user_code = $this->commonObj->createUserCode($id);
			DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id','=',$id)
					->update(['user_code'=>$user_code]);

            $firstname = $postdata['firstname'];
            $lastname = $postdata['lastname'];
            $insert_account_details = '';
            $insert_account_details['account_id'] = $id;            
            $insert_account_details['firstname'] = $firstname;
            $insert_account_details['lastname'] = $lastname;
			$insert_account_details['gender'] = $postdata['gender'];
			$insert_account_details['dob'] = date('Y-m-d',strtotime($postdata['dob']));
				
            $udRes = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->insertGetId($insert_account_details);
					
            $insert_setting = '';
            $insert_setting['account_id'] = $id;
			$insert_setting['country_id'] = $country_info->country_id;
			$insert_setting['currency_id'] = $country_info->currency_id;
			$insert_setting['is_email_verified'] = $this->config->get('constants.ON');
			$insert_setting['is_mobile_verified'] = $this->config->get('constants.ON');
		    $insert_setting['referral_code'] =$this->commonstObj->createReferralCode(); 
		    $insert_setting['change_email'] = 0; 
		    $insert_setting['change_payment'] =0; 
		    $insert_setting['transaction_pswd_user_edit'] =0; 
		    $insert_setting['deposite'] =0; 
		    $insert_setting['withdrawal'] =0; 
		    $insert_setting['create_tickets'] =0; 
		    $insert_setting['refer_friend'] =0; 
		    $insert_setting['promotion_tool'] =0; 
		
            $usRes = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
			                    ->insertGetId($insert_setting);
								
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
			$ad_setting['address'] = !empty($formated_address)? implode(', ',$formated_address) :'';		
            $usRes = DB::table($this->config->get('tables.ADDRESS_MST'))
			                    ->insertGetId($ad_setting);	
								
		    $fr_details['account_id'] =$id;
		    $fr_details['franchisee_id'] =$this->userSess->franchisee_id;
			$fr_details['created_by'] =$this->userSess->account_id;
			$fr_details['status'] =$this->config->get('constants.ON');
			$fr_details['created_on'] =getGTZ();
            DB::table(config('tables.FRANCHISEE_USERS'))->insert($fr_details);
			
			if (!empty($usRes))
            {
				($usRes) ? DB::commit() : DB::rollback(); 
				 return array('status'=>config('httperr.SUCCESS'),
				              'msg'=>trans('franchisee/user/create_user.created'));
            }
			
        }
    }
	public function UserList($arr = array(), $count = false){
				extract($arr);
				$query =    DB::table(config('tables.FRANCHISEE_USERS').' as fu')
				            ->join(config('tables.ACCOUNT_MST').' as um','um.account_id',' = ','fu.account_id')
							->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
							->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'um.account_id')
							->join(config('tables.LOCATION_COUNTRY').' as lcu', 'lcu.country_id', ' = ', 'acp.country_id')
							->join(config('tables.ADDRESS_MST').' as adm', function($subquery)
						    { 
							 $subquery->on('adm.relative_post_id', '=', 'um.account_id')
										->where('adm.post_type', '=',$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
										->where('adm.address_type_id', '=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
						    })
						   /*  ->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'fu.status') */
							->select(DB::Raw('um.signedup_on,um.account_id,um.user_code,um.uname,um.email,um.activated_on,um.block,um.login_block,um.status,
                            concat_ws(" ",ud.firstname,ud.lastname) as fullname,concat_ws("-",lcu.phonecode,um.mobile) as mobile,lcu.country as country_name,adm.address,fu.status'))
							->where("fu.franchisee_id",'=',$this->userSess->franchisee_id)
							->where('um.is_deleted','=',$this->config->get('constants.OFF'));
				if(isset($status) && !is_null($status)){
					$query->where("um.status",$status);
				} 				
				if (isset($from) && isset($to) && !empty($from) && !empty($to))	{ 
					$query->whereDate('um.signedup_on', '>=', getGTZ($from,'Y-m-d'));
					$query->whereDate('um.signedup_on', '<=', getGTZ($to,'Y-m-d'));
				}
				else if (!empty($from) && isset($from)){ 
					$query->whereDate('um.signedup_on', '<=', getGTZ($from,'Y-m-d'));
				}
				else if (!empty($to) && isset($to)){ 
					$query->whereDate('um.signedup_on', '>=', getGTZ($to,'Y-m-d'));
				}  
				
				if(isset($search_term) && !empty($search_term))
				{ 
					$search_term='%'.$search_term.'%'; 
					if(!empty($filterTerms) && isset($filterTerms))
					{   
						$search_field=['UserName'=>'um.uname','User_code'=>'um.user_code','FullName'=>'concat_ws(" ",ud.firstname,ud.lastname)','Email'=>'um.email','Mobile'=>'concat_ws("-",lcu.phonecode,um.mobile)'];
						$query->where(function($sub) use($filterTerms,$search_term,$search_field){
							foreach($filterTerms as $search)
							{  
								if(array_key_exists($search,$search_field)){
								  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_term);
								} 
							}
						});
					}
					else{
						
						$query->where(function($wcond) use($search_term){
						   $wcond->Where('um.uname','like',$search_term)
								 ->orwhere(DB::Raw('concat_ws(" ",ud.firstname,ud.lastname)'),'like',$search_term)
								 ->orwhere('um.user_code','like',$search_term)
								 ->orwhere('um.email','like',$search_term)
								 ->orwhere(DB::Raw('concat_ws("-",lcu.phonecode,um.mobile)'),'like',$search_term);
						}); 
					} 	
				}
				if (isset($orderby) && isset($order))
				{
				   if($orderby == 'signedup_on'){
						$query->orderBy('um.signedup_on', $order);
				   }elseif($orderby == 'uname'){
						$query->orderBy('um.uname', $order);
				   }elseif($orderby == 'country_name'){
						$query->orderBy('lcu.name', $order);
				   }elseif($orderby == 'activated_on'){
						$query->orderBy('um.activated_on', $order);
				   }elseif($orderby == 'status'){
						$query->orderBy('um.status', $order);
				   }
				} 
				if (isset($country) && !empty($country))
				{  
					$query->where('uad.country_id',$country);
				} 
				if (isset($length) && !empty($length))
				{
					$query->skip($start)->take($length);
				}
				if (isset($count) && !empty($count))
				{
					return $query->count();
				}
				else
				{
					$result= $query->orderBy('um.account_id', 'ASC') 
								   ->get();
			    if(!empty($result)) {
					array_walk($result, function(&$data)
					{  	
					    $data->status_class   = $this->config->get('dispclass.user.'.$data->status.'');
						if($data->status ==config('constants.ON')){
							$data->status_name='Active';
						}
						else{
							$data->status_name='Inactive';
						}
						$data->signedup_on = !empty($data->signedup_on) ? showUTZ($data->signedup_on, 'd-M-Y H:i:s'):'';	
                        $data->actions = [];				
						
					    $data->actions[] = ['url'=>route('fr.user.edit-details', ['uname'=>$data->uname]),'class'=>'edit_details', 'redirect'=>false, 'label'=>trans('franchisee/user/create_user.edit')];
						 
                        $data->actions[] = ['url'=>route('fr.user.change-password', ['account_id'=>$data->account_id]), 'class'=>'change_password', 'data'=>[
							'uname'=>$data->uname,
							'fullname' => $data->fullname,
						], 'redirect'=>false, 'label'=>trans('franchisee/user/create_user.change_pwd')];		
                        
                    if($data->status == config('constants.OFF'))
						{
							$data->actions[] = ['url'=>route('fr.user.active'),'class'=> 'active_status', 
							'data'=>[
								'account_id'=>$data->account_id,
								'status'=>$data->status,
								'staus_info'=>'activate',
							     ],'label'=>trans('franchisee/user/create_user.active')];
						}
						if($data->status == config('constants.ON')){
							$data->actions[] = ['url'=>route('fr.user.active'), 'class'=>'active_status', 'data'=>[
								'account_id'=>$data->account_id,
								'status'=>$data->status,
								'staus_info'=>'Inactivate',
							],'label'=>trans('franchisee/user/create_user.inactive')];
						}
					   			
					});
					return $result;
			} 
			
		}
		return false;
	}	 
	public function update_password ($postdata)
       {
       $uname=$postdata['uname'];		
		if(!empty($uname) && !empty(trim($postdata['new_pwd'])))
		{
			$data['pass_key'] = md5($postdata['new_pwd']);
		   
			if ($data['pass_key'] != DB::table(config('tables.ACCOUNT_MST'))
							 ->where('uname', $postdata['uname'])
							 ->value('pass_key'))
			   { 
			
				$status = DB::table(config('tables.ACCOUNT_MST'))
					     ->where('uname', $postdata['uname'])
					     ->update($data);
					
				if (!empty($status) && isset($status))
				{
					return array(
						   'msg'=>trans('franchisee/user/changepwd.password_change',['uname'=>$uname]),
						    'status'=>config('httperr.SUCCESS'));
				}
				else
				{
					return array(
						'msg'=>trans('general.something_wrong'),
						'status'=>config('httperr.UN_PROCESSABLE'));
				}
			}
			else{
				
				return array(
					"msg" => trans('franchisee/user/changepwd.same_as_old'),
					'status'=>config('httperr.UN_PROCESSABLE'));
			}
		}
        return json_encode(array('msg'=>trans('admin/affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
  	public function user_status (array $data = array())
    {
        $op = array();
        extract($data);
		
        if (isset($status) && $status == 1)
        {
			 $query_unblock= DB::table(config('tables.FRANCHISEE_USERS'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                            ->where('account_id', $uname)
							->where('status', config('constants.ON'))
                            ->update(['status'=>config('constants.OFF')]);
						if(!empty($query_unblock)){
					     	 return array(
							 'status'=>config('httperr.SUCCESS'),
						     'msg'=>trans('franchisee/user/create_user.user_unblock'));
							}
        }
        else
        {
           $query= DB::table(config('tables.FRANCHISEE_USERS'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                            ->where('account_id', $uname)
							->where('status', config('constants.OFF'))
                            ->update(['status'=>config('constants.ON')]);
							if(!empty($query)){
					     	   return array(
							    'status'=>config('httperr.SUCCESS'),
						        'msg'=>trans('franchisee/user/create_user.user_block'));
							}
        }
    }
        public function user_edit ($uname) {
			 $result =DB::table(config('tables.FRANCHISEE_USERS').' as fu')
				->join(config('tables.ACCOUNT_MST').' as am','am.account_id',' = ','fu.account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
				->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'am.account_id')
				->join(config('tables.LOCATION_COUNTRY').' as lcu', 'lcu.country_id', ' = ', 'acp.country_id')
				->join(config('tables.ADDRESS_MST').' as adm', function($subquery)
						    { 
							 $subquery->on('adm.relative_post_id', '=', 'am.account_id')
										->where('adm.post_type', '=',$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
										->where('adm.address_type_id', '=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
						   })
				->selectRaw('am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,am.email,ad.gender,ad.firstname,ad.lastname,concat_ws(" ",ad.firstname,ad.lastname) as fullname,ad.dob,am.account_id,concat_ws("-",lcu.phonecode,am.mobile) as mobile,lcu.country as country_name,lcu.phonecode,lcu.country_id,am.mobile,am.account_id,adm.address')
						->where('am.is_deleted',config('constants.NOT_DELETED'))
						->where('am.uname', $uname)
						->first();
					if (!empty($result))
							{
								return $result;
							}
							return false;						
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
				/* ->join($this->config->get('tables.LOCATION_CITY') . ' as lcy', 'lcy.city_id', '=', 'adm.city_id') */
				->where('adm.relative_post_id','=',$account_id)
				->where('adm.post_type','=',$postType)
				->where('adm.address_type_id','=',$addType)
				->select('adm.address_type_id','adm.post_type',DB::Raw('adm.relative_post_id as account_id'),'adm.flatno_street','adm.landmark','adm.address','adm.postal_code','adm.status','adm.city_id','adm.district_id','adm.state_id','adm.country_id','lc.country','ls.state','ld.district')
				->first();
			if($res) { 
				$op = $res;
			}
			else {
				$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('affiliate/account.address_not_available');
			}
		}
		return $op;
	}
	 /* User Address */
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
			$ad_setting['district_id'] = isset($arr['district_id'])? $arr['district_id']:0;		
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
	public function update_user_profile($postdata){
	   if(isset($postdata['account_id']) && $postdata['account_id']>0){
           $update_account_mst['email'] = $postdata['email'];
		   $update_account_mst['mobile'] = $postdata['mobile'];
             DB::table($this->config->get('tables.ACCOUNT_MST'))
                 ->where('account_id', $postdata['account_id'])
                 ->update($update_account_mst);  
            $upData['dob'] = getGTZ($postdata['dob'],'Y-m-d');  
			$upData['firstname'] = $postdata['firstname'];
			$upData['lastname'] = $postdata['lastname'];
	        $upData['gender'] = $postdata['gender'];
			  DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
					->where('account_id', $postdata['account_id'])
					->update($upData);  
                  return true;
          }
		  return false;
	}
}