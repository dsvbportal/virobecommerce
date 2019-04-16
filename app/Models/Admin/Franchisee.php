<?php
namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Admin\AdminCommon;
use App\Models\LocationModel;
use DB;
use URL;

class Franchisee extends BaseModel
{
    public function __construct ()
    {
      parent::__construct();
	  $this->adminCommon = new AdminCommon();
	  $this->locObj = new LocationModel();
    }	
    
    public function get_franchisee_details ($params = '')
    {
		$wdata = [];
		//print_r($params);die;
		if(is_array($params)) extract($params);
        if(isset($account_id) && !empty($account_id)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
					->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
					->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
					->join($this->config->get('tables.FRANCHISEE_MST').' as fs', 'fs.account_id', '=', 'um.account_id')
					->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fs.franchisee_type')
					->join($this->config->get('tables.ADDRESS_MST').' as adm', 'adm.relative_post_id', '=', 'um.account_id')
					->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ap.currency_id')
					->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')                       
					->select(DB::raw("um.uname,ud.*,concat_ws('',ud.firstname,ud.lastname) as full_name,um.email, fs.franchisee_id,fs.company_name, fs.company_address, fs.office_available,lc.country, lc.phonecode, fs.is_deposited, fs.deposited_amount,fl.franchisee_type, cur.currency as currency_code, adm.address, adm.postal_code"));
			
			if(is_numeric($account_id)){
				$qry->where(function($sbq) use ($account_id){
					$sbq->where('um.account_id','=',$account_id)
						->orWhere('um.user_code','=',$account_id);
				});
			} else if(strpos($account_id,'@')){
				$qry->where('um.email','=',$account_id);			
			} else {
				$qry->where('um.uname','=',$account_id);
			}
			$result = $qry->first();		
			return (!empty($result)) ? $result : NULL;
		}
        return false;
    }
	
	/* Get address */
	public function getAddress(array $arr=[])
	{		   
		extract($arr);
		if($account_id>0 || !empty($account_id)){
			$qry = DB::table($this->config->get('tables.ADDRESS_MST'))				
				  ->where('is_deleted', $this->config->get('constants.OFF'))
				   ->where('relative_post_id', $account_id);				
			if(isset($post_type) && !empty($post_type)){
			   $qry->where('post_type',  $post_type);
			}
			if(isset($address_type) && !empty($address_type)){
			    $qry->where('address_type_id',  $address_type);
			}
			$res = $qry->select('address','flatno_street','landmark','city_id','state_id','district_id','country_id','postal_code')
			           ->first();				
			return !empty($res) ? $res : false;			
		}
		return false;	
	}
	
	public function getUserAddr($account_id,$postType=0,$addType=0){
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] = trans('affiliate/account.paramiss');
		$op['msgtype'] = 'danger';
		if($account_id>0 && $addType>0 && $postType>0) {
			
			$res = DB::table($this->config->get('tables.ADDRESS_MST').' AS adm')
				->leftjoin($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'adm.country_id')
                ->leftjoin($this->config->get('tables.LOCATION_STATE') . ' as ls', 'ls.state_id', '=', 'adm.state_id')
				->leftjoin($this->config->get('tables.LOCATION_DISTRICTS') . ' as ld', 'ld.district_id', '=', 'adm.district_id')
				->leftjoin($this->config->get('tables.LOCATION_CITY') . ' as lcy', 'lcy.city_id', '=', 'adm.city_id')
				->where('adm.relative_post_id','=',$account_id)
				->where('adm.post_type','=',$postType)
				->where('adm.address_type_id','=',$addType)
				->select('adm.address_type_id','adm.post_type',DB::Raw('adm.relative_post_id as account_id'),'adm.flatno_street','adm.landmark','adm.address','adm.postal_code','adm.status','adm.city_id','adm.district_id','adm.state_id','adm.country_id','lc.country','ls.state','ld.district','lcy.city')
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

    public function save_franchisee ($postdata)
    {	
        $admindata = $this->session->get('admin');	
		$country_info = $this->locObj->getCountries(['country_id'=>$postdata['country'],'operate'=>true]); 
		$user_data = array();
        $current_date = date('Y-m-d H:i:s');
        $acMst['uname'] = $postdata['uname'];
        $acMst['email'] = $postdata['email'];
		$acMst['mobile'] = $postdata['mobile'];
        $acMst['pass_key'] = md5($postdata['password']);
        $acMst['trans_pass_key'] = md5($postdata['tpin']);
        $acMst['status'] = $this->config->get('constants.ACTIVE');
        $acMst['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE');
		$acMst['signedup_on'] = $current_date;
		$acMst['activated_on'] = $current_date;
        $add = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->insertGetId($acMst);
        if (!empty($add))
        {
			$acMstUp['user_code'] = $this->commonObj->createUserCode($add);
			$upRes = DB::table($this->config->get('tables.ACCOUNT_MST'))
				->where('account_id','=', $add)
                ->update($acMstUp);
				
			$user_code = $acMstUp['user_code'];					
			$frSt['account_id'] = $add;
			$frSt['franchisee_type'] = $postdata['fran_type'];
			$postdata['currency'] = $country_info->currency_id;

			$frSt['office_available'] = $postdata['office_available'];
			$frSt['company_name'] = $postdata['company_name'];
		 
			$frSt['currency'] = $postdata['currency'];
			$frSt['created_by'] = $admindata->account_id;
			$frSt['updated_by'] = $admindata->account_id;
			$frSt['created_on'] = getGTZ();
			
		/*	if ($postdata['isdeposited'] == $this->config->get('constants.ON'))
			{
				$frSt['is_deposited'] = $this->config->get('constants.ON');
			} 
			else{
				$frSt['is_deposited'] = $this->config->get('constants.OFF');
			} */
			
			$franchisee_id = DB::table($this->config->get('tables.FRANCHISEE_MST'))
					->insertGetId($frSt);

			$acDet['account_id'] = $add;            
            $acDet['firstname'] = $postdata['first_name'];
            $acDet['lastname'] = $postdata['last_name'];            
            $acDet['gender'] = $postdata['gender'];
            $acDet['dob'] = date('Y-m-d', strtotime($postdata['dob']));
          
            $adduser_detail = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->insertGetId($acDet);	

			$acSt['account_id'] = $add;
			$acSt['country_id'] = $postdata['country'];
			$acSt['currency_id'] = $postdata['currency'];
			$acSt['referral_code'] = $this->commonstObj->createReferralCode();
			$acSt['change_email'] = $this->config->get('constants.ON');
			$acSt['change_payment'] = $this->config->get('constants.OFF');
			$acSt['transaction_pswd_user_edit'] = $this->config->get('constants.ON');
			$acSt['create_tickets'] = $this->config->get('constants.ON');
			$acSt['refer_friend'] = $this->config->get('constants.OFF');
			$acSt['promotion_tool'] = $this->config->get('constants.OFF');
			$acSt['is_verified'] = $this->config->get('constants.OFF');
		    
			$status = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
					->insertGetId($acSt);			
			$frAcc = [];
			$frAcc['account_id'] = $add;            
            $frAcc['franchisee_id'] = $franchisee_id;
            $frAcc['created_on'] = getGTZ();
            $frAcc['updated_on'] = getGTZ();
            $frAcc['franchisee_access_permission'] = $this->config->get('constants.FRANCHISEE_ACCESS_TYPE.PRIMARY');
          
            $adduser_detail = DB::table($this->config->get('tables.FRANCHISEE_ACCOUNTS'))
                    ->insertGetId($frAcc);	
					
			$stateInfo = $this->locObj->getState(0,$postdata['state']);
			$distInfo = $this->locObj->getDistrict(0,$postdata['district']);
			$locInfo = $this->locObj->get_city_list($postdata['state'],$postdata['district'],$postdata['city']);
			
			$formated_address = [];
			if(!empty($postdata['address'])) {
				$formated_address[] = $postdata['address'];
				$val['flatno_street'] = $postdata['address'];
			}
			if(!empty($postdata['user_landmark'])) {
				$formated_address[] = $postdata['user_landmark'];
				$val['landmark'] = $postdata['user_landmark'];
			}	
		    if(!empty($locInfo)) {
				$formated_address[] = $locInfo->city;
				$val['locality'] = $locInfo->city;
			}  			
			if(!empty($distInfo)) {
				$formated_address[] = $distInfo->district;
				$val['district'] = $distInfo->district;
			}
			if(!empty($stateInfo)) {
				$formated_address[] = $stateInfo->state.'-'.$postdata['zipcode'];
				$val['state'] = $stateInfo->state.'-'.$postdata['zipcode'];		
			}			
			if(!empty($country_info)) {
				$formated_address[] = $country_info->country_name;
				$val['country'] = $country_info->country_name;
			}
			
			$fr_formated_address = [];
			if($postdata['office_available']){
				/*franchisee addresss */
				$fr_country_info = $this->locObj->getCountry(['country_id'=>$postdata['country'],'allow_signup'=>true]);
				$fr_stateInfo = $this->locObj->getState(0,$postdata['franchisee_state']);
				$fr_distInfo = $this->locObj->getDistrict(0,$postdata['franchisee_district']);
				$fr_locInfo = $this->locObj->get_city_list($postdata['franchisee_state'],$postdata['franchisee_district'],$postdata['franchisee_city']);
					
				if(!empty($postdata['company_address'])) {
					$fr_formated_address[] =  $postdata['company_address'];
					$val['flatno_street'] =  $postdata['company_address'];
				}
				if(!empty($postdata['landmark'])) {
					$fr_formated_address[]  = $postdata['landmark'];
					$val['landmark'] = $postdata['landmark'];
				}	
				if(!empty($fr_locInfo)) {
					$fr_formated_address[] = $fr_locInfo->city;
					$val['locality'] = $fr_locInfo->city;
				}
				if(!empty($fr_distInfo)) {
					$fr_formated_address[] =$fr_distInfo->district;
					$val['district'] = $fr_distInfo->district;
				}
				if(!empty($fr_stateInfo)) {
					$fr_formated_address[] = $fr_stateInfo->state.'-'.$postdata['franchisee_zipcode'];
					$val['state'] = $fr_stateInfo->state.'-'.$postdata['franchisee_zipcode'];		
				}
				if(!empty($fr_country_info)) {
					$fr_formated_address[] = $country_info->country_name;;
					$val['country'] = $country_info->country_name;		
				}	
		    }
			/* full address */
			$addr['address'] = !empty($formated_address)? implode(',',$formated_address) :'';	
			$fradr['address'] = !empty($fr_formated_address)? implode(', ',$fr_formated_address) :'';	
			
			$addr['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');	
			$addr['relative_post_id'] = $add;
			$addr['address_type_id'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');			
			$addr['flatno_street'] = $postdata['address'];		
            $addr['landmark'] = $postdata['user_landmark'];									
			$addr['country_id'] = $postdata['country'];
			$addr['district_id'] = $postdata['district'];
			$addr['state_id'] = $postdata['state'];
            $addr['locality_id'] = $postdata['city'];
            $addr['postal_code'] = $postdata['zipcode'];
            $addr['created_on'] = $current_date;
            $adduser_detail = DB::table($this->config->get('tables.ADDRESS_MST'))
                    ->insertGetId($addr);
					
		    $fradr['flatno_street'] = $postdata['company_address'];
	 	    $fradr['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE');	
		    $fradr['address_type_id'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
            $fradr['relative_post_id'] = $franchisee_id;		   
		    $fradr['landmark'] = $postdata['landmark'];						
			$fradr['country_id'] = $postdata['country'];
			$fradr['state_id'] = $postdata['franchisee_state'];
			$fradr['district_id'] = $postdata['franchisee_district'];
            $fradr['locality_id'] = $postdata['franchisee_city'];
            $fradr['postal_code'] = $postdata['franchisee_zipcode'];
            $fradr['created_on'] = $current_date;
		    $franchisee_address = DB::table($this->config->get('tables.ADDRESS_MST'))
                    ->insertGetId($fradr);			
			
        }        
        return (!empty($franchisee_id)) ? $add : false;
    }

    public function franchisee_access_location ($postdata)
    {
        $access = array();
        $admindata = Session::get('admindata');
        $franchisee_type = $postdata['franchi_type'];
        $access['country_id'] = $access['region_id'] = $access['state_id'] = $access['district_id'] = 0;
        $access['account_id'] = $postdata['account_id'];
        $password = $postdata['pwd'];
        $tpin = $postdata['tpin'];
        $access['access_location_type'] = $franchisee_type;
        $access['created_by'] = $admindata[0]['admin_id'];
        $access['updated_by'] = $admindata[0]['admin_id'];
        $access['created_on'] = date('Y-m-d H:i:s');
        $access['updated_on'] = date('Y-m-d H:i:s');
        $relation_id = '0';
        if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'))
        {
            $relation_id = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.REGION'))
        {
            $relation_id = $postdata['region'];
            $access['country_id'] = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.STATE'))
        {
            $relation_id = $postdata['state'];
            if (isset($postdata['union_territory']))
            {
                if (is_array($postdata['union_territory']))
                {
                    $union_territory = implode(',', $postdata['union_territory']);
                }
                $relation_id = $relation_id.','.$union_territory;
            }
            $access['country_id'] = $postdata['country'];
            $access['region_id'] = $this->locObj->getRegionID($postdata['state']);
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'))
        {            
            $relation_id = $postdata['district'];
            $access['country_id'] = $postdata['country'];
            $access['region_id'] = $this->locObj->getRegionID($postdata['state']);
            $access['state_id'] = $postdata['state'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.CITY'))
        {            
            $relation_id = $postdata['city'];
            $access['country_id'] = $postdata['country'];
            $access['region_id'] = $this->locObj->getRegionID($postdata['state']);
            $access['state_id'] = $postdata['state'];
            $access['district_id'] = $postdata['district'];
        }
        $access['relation_id'] = $relation_id;
        $add_locations = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))
                ->insertGetId($access);
				
        $acinfo = $this->commonstObj->get_userdetails_byid($postdata['account_id']);
        if (!empty($acinfo))
        {
            $data['email'] = $acinfo->email;
            $data['username'] = $acinfo->uname;
            $data['pwd'] = $password;
            $data['tpin'] = $tpin;
            $data['fullname'] = $acinfo->first_name.' '.$acinfo->last_name;
            $data['country'] = $this->locObj->getCountry()[0]->name;            
        }
        return (!empty($add_locations)) ? $add_locations : false;
    }

    public function franchisee_email_check ($email = 0, $acinfo = 0, $old_email = 0)
    {
        if ($email)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $status['status'] = 'error';
                $status['msg'] = 'Please Enter a valid Email Address.';
                return $status;
            }
            if ($email == 'testerej88@gmail.com' || (!empty($old_email) && $old_email == $email))
            {
                $status['status'] = 'ok';
                $status['msg'] = 'Email ID Available';
                return $status;
            }
            else if ($email != 'testerej88@gmail.com')
            {
                $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('email', $email)
                        ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                        ->get();
                if (empty($result) && count($result) == 0)
                {
                    $status['status'] = 'ok';
                    $status['msg'] = 'Email ID Available';
                }
                else
                {
                    $status['status'] = 'error';
                    $status['msg'] = 'Email ID Already Exists';
                }
                return $status;
            }
        }
        else
        {
            $status['status'] = 'error';
            $status['msg'] = 'Please Enter a valid Email Address';
            return $status;
        }
    }

    public function franchisee_mobile_check ($mobile = '', $old_mobile = '')
    {
        if ($mobile)
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_DETAILS').' as ud')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'ud.account_id')
                    ->where('um.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                    ->where('um.mobile', $mobile)
                    ->get();
            if (empty($result) && count($result) == 0 || (!empty($old_mobile) && $old_mobile == $mobile))
            {
                $status['status'] = 'ok';
                $status['msg'] = 'Mobile Number Available';
            }
            else
            {
                $status['status'] = 'error';
                $status['msg'] = 'Mobile Number  Already Exists';
            }
            return $status;
        }
        else
        {
            $status['status'] = 'error';
            $status['msg'] = 'Please Enter a Mobile number.';
            return $status;
        }
    }

    public function franchisee_check_username ($uname = 0)
    {
		$op = [];
        if ($uname)
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('uname', $uname)
                    ->first();
					
            if (empty($result) && count($result) == 0)
            {
                $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Username Available';
            }
            else
            {
                $op['status'] = $this->config->get('httperr.PARAMS_MISSING');
                $op['error'] = ['uname'=>'Username Already Exists'];
            }
            return $op;
        }
    }  

    public function get_franchisee_list ($data = array(), $uname = '', $account_id = 0)
    {   
		$franchisee_type_arr = (array(
            1=>'LOCATION_COUNTRY',
            2=>'LOCATION_REGIONS',
            3=>'LOCATION_STATE',
            4=>'LOCATION_DISTRICTS',
            5=>'LOCATION_CITY'));
        $franchisee_field_arr = (array(
            1=>'name',
            2=>'region_name',
            3=>'name',
            4=>'district_name',
            5=>'city_name'));
        $franchisee_wfield_arr = (array(
            1=>'country_id',
            2=>'region_id',
            3=>'state_id',
            4=>'district_id',
            5=>'city_id'));

        $users = $from = $to = $user_name = $ustatus = '';		
		
        $users = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id', 'left')
				->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', '=', 'um.account_id', 'left')
				->join($this->config->get('tables.FRANCHISEE_MST').' as fs', 'fs.account_id', '=', 'um.account_id')
				->where('um.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'));
				
		if(!empty($uname) || (isset($account_id) && !empty($account_id))){
			$users->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'up.currency_id', 'left')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'up.country_id', 'left')                
                ->leftjoin($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fs.franchisee_type')                
                ->select(DB::raw("um.*, ud.*,concat_ws(' ',ud.firstname,ud.lastname) as full_name, um.account_id, um.status as ustatus,c.currency,fs.account_id as franchisee_acc_id,fs.franchisee_type,fl.franchisee_type as franchisee_type_name,up.country_id,lc.country,lc.phonecode,um.status as user_status,fs.company_name,fs.company_address, fs.office_available,fal.merchant_signup_fee,fal.profit_sharing,fal.profit_sharing_without_district,fal.deposite_amount,fal.relation_id,
				(CASE WHEN (fal.access_location_type = '1')
				THEN (select country from  ".$this->config->get('tables.LOCATION_COUNTRY')." where country_id =  fal.relation_id) ELSE	'' END) as access_country_name,
				(CASE WHEN (fal.access_location_type = '2')
				THEN (select region from  ".$this->config->get('tables.LOCATION_REGIONS')." where region_id =  fal.relation_id) ELSE	'' END) as access_region_name,
				(CASE WHEN (fal.access_location_type = '3')
				THEN (select state from  ".$this->config->get('tables.LOCATION_STATE')." where state_id =  fal.relation_id) ELSE	'' END) as access_state_name,
				(CASE WHEN (fal.access_location_type = '4')
				THEN (select district from  ".$this->config->get('tables.LOCATION_DISTRICTS')." where district_id =  fal.relation_id) ELSE	'' END) as access_district_name,
				 (CASE WHEN (fal.access_location_type = '5')
				THEN (select city_name as city from  ".$this->config->get('tables.LOCATION_TOP_CITY')." where city_id =  fal.relation_id) ELSE '' END) as access_city_name,
				(CASE WHEN (fs.franchisee_type = '2')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.	access_location_type = '1' and cfal.relation_id IN(select country_id from location_regions where region_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname,
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.	access_location_type = '1' and cfal.relation_id IN(select country_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname1,
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as region_frname,
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal 
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select state_id from location_districts where district_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname,
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname1,
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname2,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '4' and cfal.relation_id IN(select district_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as district_frname,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select ld.state_id from location_top_cities as ltc inner join location_districts as ld on ld.district_id=ltc.district_id where ltc.city_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname1,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from location_states where state_id = (select ld.state_id from location_top_cities as ltc inner join location_districts as ld on ld.district_id=ltc.district_id where ltc.city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname2,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_states where state_id = (select ld.state_id from location_top_cities as ltc inner join location_districts as ld on ld.district_id=ltc.district_id where ltc.city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname3,IF(fal.access_location_type IS NOT NULL,1,0) as access_exist"));
			
		    $account_id = (isset($account_id) && !empty($account_id)) ? $account_id : $uname;			
			if(is_numeric($account_id)){
				$users->where(function($sbq) use($account_id){
					$sbq->where('um.account_id','=',$account_id)
						->orWhere('um.user_code','=',$account_id);
				});
			} else if(strpos($account_id,'@')){
				$users->where('um.email','=',$account_id);
			} else {
				$users->where('um.uname','=',$account_id);
			}
			$result = $users->first();			
			return (!empty($result)) ? $result : NULL;		
		}
        if (is_array($data) && count($data) > 0)
        {           
			extract($data);						
            if (isset($from) && !empty($from))
            {
                $users = $users->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from))."'");
            }
			if (isset($from) && !empty($to))
            {
                $users = $users->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            if (isset($franchisee_type) && !empty($franchisee_type))
            {
                $users->where('fs.franchisee_type', $franchisee_type);
            }

            if (isset($search_term) && !empty($search_term))
            {	
				if(isset($filterchk) && !empty($filterchk))
				{   
					$search_term = '%'.$search_term.'%'; 
					$search_field = ['UserName'=>'um.uname','FranchiseeName'=>'fs.company_name'];
					$users->where(function($sub) use($filterchk,$search_term,$search_field){
						foreach($filterchk as $search)
						{							
							if(array_key_exists($search,$search_field)){
								if($search=='UserName'){
									if(preg_match ("/[0-9]/", $search_term)) {
										$sub->where(function($sbq) use($search_term){
											$sbq->where('um.account_id','like',$search_term)
												->orWhere('um.user_code','like',$search_term);
										});
									}
									else if(strpos($search_term,'@')){
										$sub->where('um.email','like',$search_term);
									}
									else {
										$sub->where('um.uname','like',$search_term);
									}
								}
								else {
									$sub->orWhere(DB::raw($search_field[$search]),'like',$search_term);
								}
							}							
						} 
					});
				}
				else{
					$users->where(function($wcond) use($search_term){
						$wcond->Where('um.uname','like',$search_term)
							->orwhere(DB::Raw('concat_ws(" ",ud.firstname,ud.lastname)'),'like',$search_term)
							->orwhere('um.email','like',$search_term)
							->orwhere('um.mobile','like',$search_term)
							->orwhere('spum.uname','like',$search_term)
							->orwhere('upum.uname','like',$search_term);
					});
				}
            }
        }  
		
		if (!isset($count) || (isset($length) && !empty($length))) {			
			$users->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'up.currency_id', 'left')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'up.country_id', 'left')                
                ->leftjoin($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fs.franchisee_type')                
                ->select(DB::raw("um.*, ud.*,concat_ws(' ',ud.firstname,ud.lastname) as full_name, um.account_id, um.status as ustatus,c.currency,fs.account_id as franchisee_acc_id,fs.franchisee_type,fl.franchisee_type as franchisee_type_name,up.country_id,lc.country,lc.phonecode,um.status as user_status,fs.company_name,fs.company_address, fs.office_available,fal.merchant_signup_fee,fal.profit_sharing,fal.profit_sharing_without_district,fal.deposite_amount,fal.relation_id,
				(CASE WHEN (fal.access_location_type = '1')
				THEN (select country from  ".$this->config->get('tables.LOCATION_COUNTRY')." where country_id =  fal.relation_id) ELSE	'' END) as access_country_name,
				(CASE WHEN (fal.access_location_type = '2')
				THEN (select region from  ".$this->config->get('tables.LOCATION_REGIONS')." where region_id =  fal.relation_id) ELSE	'' END) as access_region_name,
				(CASE WHEN (fal.access_location_type = '3')
				THEN (select state from  ".$this->config->get('tables.LOCATION_STATE')." where state_id =  fal.relation_id) ELSE	'' END) as access_state_name,
				(CASE WHEN (fal.access_location_type = '4')
				THEN (select district from  ".$this->config->get('tables.LOCATION_DISTRICTS')." where district_id =  fal.relation_id) ELSE	'' END) as access_district_name,
				 (CASE WHEN (fal.access_location_type = '5')
				THEN (select city_name as city from  ".$this->config->get('tables.LOCATION_TOP_CITY')." where city_id =  fal.relation_id) ELSE '' END) as access_city_name,
				(CASE WHEN (fs.franchisee_type = '2')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.	access_location_type = '1' and cfal.relation_id IN(select country_id from location_regions where region_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname,
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.	access_location_type = '1' and cfal.relation_id IN(select country_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname1,
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as region_frname,
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal 
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select state_id from location_districts where district_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname,
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname1,
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname2,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '4' and cfal.relation_id IN(select district_id from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as district_frname,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select ld.state_id from location_top_cities as ltc inner join location_districts as ld on ld.district_id=ltc.district_id where ltc.city_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname1,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from location_states where state_id = (select ld.state_id from location_top_cities as ltc inner join location_districts as ld on ld.district_id=ltc.district_id where ltc.city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname2,
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." cfal
				inner join ".$this->config->get('tables.ACCOUNT_MST')." cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_states where state_id = (select ld.state_id from location_top_cities as ltc inner join location_districts as ld on ld.district_id=ltc.district_id where ltc.city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname3,IF(fal.access_location_type IS NOT NULL,1,0) as access_exist"))
			->skip($start)->take($length);
		}
		if (isset($count) && !empty($count)) {
			return $users->count();
		} else {
			return  $users->get();			
		}
        return false;
    }

    public function add_access_location ($postdata)
    {
		
	/* 	print_r($postdata); die; */
        $access = array();
        $admindata = $this->session->get('admin');
        $franchisee_type = $postdata['franchi_type'];
		
		$frInfo = $this->get_franchisee_details(['account_id'=>$postdata['account_id']]);
        $update_data['country_id'] = $update_data['region_id'] = $update_data['state_id'] = $update_data['district_id'] = 0;
		
        $access['franchisee_id'] = $frInfo->franchisee_id;
		$access['account_id'] = $postdata['account_id'];
        $access['access_location_type'] = $franchisee_type;
        $access['created_by'] = $admindata->account_id;
        $access['updated_by'] = $admindata->account_id;
        $access['created_on'] = getGTZ();
        $access['updated_on'] = getGTZ();
        $relation_id = '0';

		$access['updated_on'] = getGTZ();
		
        if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'))
        {
            $relation_id = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.REGION'))
        {
            $relation_id = $postdata['region'];
            $update_data['country_id'] = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.STATE'))
        {
            $relation_id = $postdata['state'];
            if (isset($postdata['union_territory']))
            {
                if (is_array($postdata['union_territory']))
                {
                    $union_territory = implode(',', $postdata['union_territory']);
                }
                $relation_id = $relation_id.','.$union_territory;
            }
            $update_data['country_id'] = $postdata['country'];
            $regionInfo = $this->locObj->getRegionID($postdata['state']);			
			if($regionInfo){
				$update_data['region_id'] = $regionInfo;
			}
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'))
        {
            if ($postdata['district'] == 0)
            {
                $postdata['district'] = $this->locObj->addNewDistrict($postdata['district_others'], $postdata['state']);
            }
            $relation_id = $postdata['district'];
            $update_data['country_id'] = $postdata['country'];
            $regionInfo = $this->locObj->getRegionID($postdata['state']);			
			if($regionInfo){
				$update_data['region_id'] = $regionInfo;
			}
            $update_data['state_id'] = $postdata['state'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.CITY'))
        {
					
            if ($postdata['city'] == 0)
            {
                $postdata['city'] = $this->locObj->addNewTopCity($postdata['city_others'], $postdata['state'], $postdata['district']);
            }		
            $relation_id = $postdata['city'];
            $update_data['country_id'] = $postdata['country'];
            $regionInfo = $this->locObj->getRegionID($postdata['state']);			
			if($regionInfo){
				$update_data['region_id'] = $regionInfo;
			}
            $update_data['state_id'] = $postdata['state'];
            $update_data['district_id'] = $postdata['district'];
        }
        $access['relation_id'] = $update_data['relation_id'] = $relation_id;

        $update_data['status'] = $this->config->get('constants.ON');
		$update_data['merchant_signup_fee']=!empty($postdata['merchant_signup']) ? $postdata['merchant_signup'] :'';
		$update_data['profit_sharing']=!empty($postdata['profit_sharing']) ? $postdata['profit_sharing'] :'';
		$update_data['profit_sharing_without_district']=!empty($postdata['pro_sharing_without_district']) ? $postdata['pro_sharing_without_district'] :'';
		$update_data['deposite_amount']=!empty($postdata['desposited_amount']) ? $postdata['desposited_amount'] :'';
		$result = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))->where('access_location_type', $postdata['franchi_type'])->where('account_id', $postdata['account_id'])->get();      		
				
        if (isset($result) && count($result)>0)
        {
            $update = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))
                    ->where('account_id', $postdata['account_id'])
                    ->update($update_data);
			return !empty($update) ? $update : false;
        }
        else
        {			
			$update_data['access_location_type'] = $postdata['franchi_type'];
            $access = array_merge($access,$update_data);
			$add_locations = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))
                    ->insertGetId($access);
            //$ACCOUNT_DETAILS = $this->commonstObj->get_userdetails_byid($postdata['account_id']);
            /*  if (!empty($ACCOUNT_DETAILS))
              {
              $data['email']      = $ACCOUNT_DETAILS->email;
              $data['username']   = $ACCOUNT_DETAILS->uname;
              $data['pwd']        = $password;
              $data['tpin']       = $tpin;
              $data['fullname']   = $ACCOUNT_DETAILS->first_name.' '.$ACCOUNT_DETAILS->last_name;
              $data['pagesettings'] = $this->pagesettings;
              $htmls = View::make('emails.franchisee.create_franchisee',$data)->render();
              $mailstatus = new MailerLib(array(
              'to' => $data['email'],
              'subject' => "Channel Partner Confirmation.",
              'html' => $htmls,
              'from' => $this->config->get('constants.SYSTEM_MAIL_ID'),
              'fromname' => $this->config->get('constants.DOMAIN_NAME')
              ));
              } */
            return (!empty($add_locations)) ? $add_locations : false;
        }
    }
	
	
	public function update_franchisee_accessinfo ($postdata)
    {
        $access = array();
        $admindata = $this->session->get('admin');
        $franchisee_type = $postdata['franchi_type'];        
		$wdata['account_id'] = $postdata['account_id'];
        $wdata['access_location_type'] = $franchisee_type;        
		$update_data['updated_on'] = getGTZ();
		
        if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'))
        {
            $relation_id = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.REGION'))
        {
            $relation_id = $postdata['region'];
            $wdata['country_id'] = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.STATE'))
        {
            $relation_id = $postdata['state'];
            if (isset($postdata['union_territory']))
            {
                if (is_array($postdata['union_territory']))
                {
                    $union_territory = implode(',', $postdata['union_territory']);
                }
                $relation_id = $relation_id.','.$union_territory;
            }
            $wdata['country_id'] = $postdata['country'];
            $regionInfo = $this->locObj->getRegionID($postdata['state']);			
			if($regionInfo){
				$wdata['region_id'] = $regionInfo;
			}
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'))
        {
            if ($postdata['district'] == 0)
            {
                $postdata['district'] = $this->locObj->addNewDistrict($postdata['district_others'], $postdata['state']);
            }
            $relation_id = $postdata['district'];
            $wdata['state_id'] = $postdata['state'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.CITY'))
        {					
            if ($postdata['city'] == 0)
            {
                $postdata['city'] = $this->locObj->addNewTopCity($postdata['city_others'], $postdata['state'], $postdata['district']);
            }		
            $relation_id = $postdata['city'];
            $wdata['district_id'] = $postdata['district'];
        }
        $wdata['relation_id'] = $relation_id;        
		$update_data['merchant_signup_fee'] = (isset($postdata['merchant_signup']) && !empty($postdata['merchant_signup'])) ? $postdata['merchant_signup'] :0;
		$update_data['profit_sharing'] = (isset($postdata['profit_sharing']) && !empty($postdata['profit_sharing'])) ? $postdata['profit_sharing'] :0;
		$update_data['profit_sharing_without_district'] = (isset($postdata['pro_sharing_without_district']) && !empty($postdata['pro_sharing_without_district'])) ? $postdata['pro_sharing_without_district'] : 0;
		$update_data['deposite_amount']= (!empty($postdata['desposited_amount'])) ? $postdata['desposited_amount'] :0;		
		$result = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))->where($wdata)->update($update_data);
				
        return $result? true : false;
    }

    public function get_frachisee_access ($account_id=0,$franchisee_id=0)
    {
        if($account_id>0 || $franchisee_id>0){
			$qry = DB::table($this->config->get('tables.FRANCHISEE_MST').' as fs')
					->leftjoin($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'fs.account_id')					
					->where('fal.status', $this->config->get('constants.ON'))
					->select(DB::raw('fal.account_id,fal.relation_id as location_access,fal.access_location_type as access_type, fal.country_id, fal.region_id, fal.state_id, fal.district_id'))
					->groupBy('fal.account_id');
			
			if($account_id>0){
				$qry->where('fs.account_id', $account_id);
			}
			else {
				$qry->where('fs.franchisee_id', $franchisee_id);
			}
			$res = $qry->first();				
			return (!empty($res)) ? $res : false;
		}
		return false;
    }
	
	
	public function get_frachisee_access_list ($account_id=0,$franchisee_id=0)
    {
        if($account_id>0 || $franchisee_id>0){
			$qry = DB::table($this->config->get('tables.FRANCHISEE_MST').' as fs')
					->leftjoin($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'fs.account_id')					
					->select(DB::raw('fal.id as loc_access_id,fal.account_id,fal.relation_id as location_access,fal.access_location_type as access_type, fal.country_id, fal.region_id, fal.state_id, fal.district_id'))
					->where('fal.status', $this->config->get('constants.ON'));
					
			
			if($account_id>0){
				$qry->where('fs.account_id', $account_id);
			}
			else {
				$qry->where('fs.franchisee_id', $franchisee_id);
			}
			$res = $qry->first();
			return (!empty($res)) ? $res : false;
		}
		return false;
    }
	
	public function get_franchisee_access_location ($franchisee_id)
    {        
		if(!empty($franchisee_id)){
			/*
			(CASE WHEN (fal.access_location_type = 1)
					THEN (select country from  ".$this->config->get('tables.LOCATION_COUNTRY')." where country_id =  fal.relation_id) END) as access_country_name,
				(CASE WHEN (fal.access_location_type = 2)
					THEN (select region from  ".$this->config->get('tables.LOCATION_REGIONS')." where region_id =  fal.relation_id) END) as access_region_name,
				(CASE WHEN (fal.access_location_type = 3)
					THEN (select state from  ".$this->config->get('tables.LOCATION_STATE')." where state_id =  fal.relation_id) END) as access_state_name,
				(CASE WHEN (fal.access_location_type = 4)
					THEN (select district from  ".$this->config->get('tables.LOCATION_DISTRICTS')." where district_id =  fal.relation_id) END) as access_district_name,
				(CASE WHEN (fal.access_location_type = 5)
					THEN (select city from  ".$this->config->get('tables.LOCATION_CITY')." where city_id =  fal.relation_id) END) as access_city_name
			*/
			
			
			$res = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')					
					->leftjoin($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'fal.country_id')
					->leftjoin($this->config->get('tables.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'fal.region_id')
					->leftjoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'fal.state_id')
					->leftjoin($this->config->get('tables.LOCATION_DISTRICTS').' as dt', 'dt.district_id', '=', 'fal.district_id')					
					->select(DB::raw("fal.id,fal.account_id,fal.relation_id as location_access,fal.access_location_type as access_type, fal.country_id, fal.region_id, fal.state_id, fal.district_id,
		IF(fal.access_location_type=1,(select country from ".$this->config->get('tables.LOCATION_COUNTRY')." where country_id =  fal.relation_id),lc.country) as access_country_name,
		IF(fal.access_location_type=2,(select region from ".$this->config->get('tables.LOCATION_REGIONS')." where region_id =  fal.relation_id),rg.region) as access_region_name,
		IF(fal.access_location_type=3,(select state from ".$this->config->get('tables.LOCATION_STATE')." where state_id =  fal.relation_id),ls.state) as access_state_name,
		IF(fal.access_location_type=4,(select district from ".$this->config->get('tables.LOCATION_DISTRICTS')." where district_id =  fal.relation_id),dt.district) as access_district_name,
		IF(fal.access_location_type=5,(select city_name as city from ".$this->config->get('tables.LOCATION_TOP_CITY')." where city_id =  fal.relation_id),NULL) as access_city_name"))
					->where('fal.status','=',$this->config->get('constants.ON'))
					->where('fal.franchisee_id', $franchisee_id)
					->orderby('fal.id','DESC')					
					->first();
			return (!empty($res)) ? $res : false;			
		}       
    }

    public function get_city_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_TOP_CITY').' as ct')
                ->join($this->config->get('tables.LOCATION_DISTRICTS').' as dt', 'dt.district_id', '=', 'ct.district_id')
                ->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'dt.state_id')
                ->leftjoin($this->config->get('tables.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'ls.region_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                ->where('ct.city_id', $location_id)
                ->where('ct.status', 1)
                ->select('ct.city_id', 'dt.district_id', 'ls.state_id', 'lc.country_id', 'rg.region_id')
                ->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_district_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_DISTRICTS').' as dt')
                ->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'dt.state_id')
                ->leftjoin($this->config->get('tables.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'ls.region_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                ->where('dt.district_id', $location_id)
                ->where('dt.status', 1)
                ->select('dt.district_id', 'ls.state_id', 'lc.country_id', 'rg.region_id')
                ->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_state_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_STATE').' as ls')
                        ->leftjoin($this->config->get('tables.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'ls.region_id')
                        ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                        ->where('ls.state_id', $location_id)
                        ->where('ls.status', 1)
                        ->select('ls.state_id', 'lc.country_id', 'rg.region_id')
						->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_region_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_REGIONS').' as rg')
                        ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'rg.country_id')
                        ->where('rg.region_id', $location_id)
                        ->where('rg.status', 1)
                        ->select('rg.region_id', 'lc.country_id')
						->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_country_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                        ->where('lc.country_id', $location_id)
                        ->where('lc.status', 1)
                        ->select('lc.country_id')->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_franchisee_package ($params = array())
    {
		if(!empty($params)){
			extract($params);
		}
		$res = DB::table($this->config->get('tables.FRANCHISEE_PACKAGE').' as fp')
				->join($this->config->get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'fp.currency')
                ->select('franchisee_type', 'fr_pack_amount','country_id','state_id','district_id','city_id', 'cr.currency_id','cr.currency as currency_code');
				
		if (isset($frans_type) && !empty($frans_type)) {
           $res->where('fp.franchisee_type', $frans_type);
        }
		
		if (isset($country_id) && !empty($country_id)) {
            $res->where('fp.country_id', $country_id);
        }		
		
		if (isset($state_id) && !empty($state_id)) {
            $res->where('fp.state_id', $state_id);
        }
		else {
			$res->where('fp.state_id',0);
		}
		
		if (isset($district_id) && !empty($district_id)) {
            $res->where('fp.district_id', $district_id);
        }
		else {
			$res->where('fp.district_id',0);
		}
		
		if (isset($city_id) && !empty($city_id)) {
            $res->where('fp.city_id', $city_id);
        }
		else {
			$res->where('fp.city_id',0);
		}
		
        $res = $res->get();
        if (!empty($res)) {				
			return $res;
		} 
		return ['msg'=>'','status'=>$this->config->get('httperr.UN_PROCESSABLE')];
    }

    public function check_franchise_access ($franchise_type, $relation_id)
    {
        if (is_array($relation_id))
        {
            $relation_id = implode(',', $relation_id);
        }
        $res = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fs')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'fs.account_id')
                ->where('um.status', 1)
                ->where('um.is_deleted', 0)
                ->where('fs.status', 1)
                ->whereRaw("fs.relation_id LIKE '%".$relation_id."%'")
                ->where('fs.access_location_type', $franchise_type)
                ->pluck('uname');
        return !empty($res) ? $res : false;
    }

    public function update_franchisee_profile ($postdata)
    {
        $upData = [];    
		if(isset($postdata['account_id']) && $postdata['account_id']>0){	
   		    $upData['dob'] = getGTZ($postdata['dob'],'Y-m-d'); 
			$upData['firstname'] = $postdata['firstname'];
			$upData['lastname'] = $postdata['lastname'];
	
			  DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
					->where('account_id', $postdata['account_id'])
					->update($upData);  
	
				$frData['office_available']=$postdata['office_available'];
				$frData['company_name']=$postdata['company_name'];
		 
		if(!empty($postdata['editaddr'])){
			
			$ad_setting['country_id'] =isset($postdata['personal_address']['country_id'])? $postdata['personal_address']['country_id']:0;
			$ad_setting['flatno_street'] = isset($postdata['personal_address']['flatno_street'])? $postdata['personal_address']['flatno_street']:0;	
			$ad_setting['landmark'] =  isset($postdata['personal_address']['landmark'])? $postdata['personal_address']['landmark']:0;	
			$ad_setting['city_id'] = isset($postdata['personal_address']['city'])? $postdata['personal_address']['city']:0;	
			$ad_setting['state_id'] = isset($postdata['personal_address']['state'])? $postdata['personal_address']['state']:0;
			$ad_setting['district_id'] = isset($postdata['personal_address']['district_id'])? $postdata['personal_address']['district_id']:0;
			$ad_setting['postal_code'] = isset($postdata['personal_address']['postal_code'])? $postdata['personal_address']['postal_code']:'';
			if(isset($postdata['personal_address']['address']) && is_array($postdata['personal_address']['address'])){
				$ad_setting['address'] = implode(', ',$postdata['personal_address']['address']);		
			 }
			else if(isset($postdata['personal_address']['address']) && is_string($postdata['personal_address']['address'])){
				$ad_setting['address'] = $postdata['personal_address']['address'];		
			} 
			else {
				$ad_setting['address'] = '';		
			}
		  DB::table($this->config->get('tables.ADDRESS_MST'))
				->where('post_type',$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
				->where('address_type_id', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'))
				->where('relative_post_id', $postdata['account_id'])
				->update($ad_setting); 
	 }
     if(!empty($postdata['office_available'])){		
      
            if(!empty($postdata['edit_fr_addr'])){
		
				$fr_ad_setting['flatno_street'] = isset($postdata['franchisee_address']['flatno_street'])? $postdata['franchisee_address']['flatno_street']:0;	
				$fr_ad_setting['landmark'] = isset($postdata['franchisee_address']['landmark'])? $postdata['franchisee_address']['landmark']:0;	
				$fr_ad_setting['city_id'] = isset($postdata['franchisee_address']['city'])? $postdata['franchisee_address']['city']:0;	
				$fr_ad_setting['state_id'] = isset($postdata['franchisee_address']['state'])? $postdata['franchisee_address']['state']:0;
				$fr_ad_setting['district_id'] = isset($postdata['franchisee_address']['district_id'])? $postdata['franchisee_address']['district_id']:0;		
				$fr_ad_setting['postal_code'] = isset($postdata['franchisee_address']['postal_code'])? $postdata['franchisee_address']['postal_code']:'';
						if(isset($postdata['franchisee_address']['address']) && is_array($postdata['franchisee_address']['address'])){
							$fr_ad_setting['address'] = implode(', ',$postdata['franchisee_address']['address']);		
						 }
						else if(isset($postdata['franchisee_address']['address']) && is_string($postdata['franchisee_address']['address'])){
							$fr_ad_setting['address'] = $postdata['franchisee_address']['address'];		
						} 
						else {
							$fr_ad_setting['address'] = '';		
						}
						 DB::table($this->config->get('tables.ADDRESS_MST'))
							->where('post_type',$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'))
							->where('address_type_id', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'))
							->where('relative_post_id', $postdata['fr_account_id'])
							->update($fr_ad_setting); 
					  }			
	            }			  
	          else{
				$res = DB::table($this->config->get('tables.ADDRESS_MST').' AS adm')
					->where('adm.relative_post_id','=',$postdata['fr_account_id'])
					->where('adm.post_type','=',$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'))
					->where('adm.address_type_id','=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'))
					->select('address','flatno_street','landmark','city_id','state_id','district_id','country_id','postal_code','updated_on')
					->first();
				  if(!empty($res)){
					  $data['created_date']=$res->updated_on;
					  $data['updated_date']=showUTZ();
					  $data['address']=$res->address;
					  $data['has_address']=$this->config->get('constants.OFF');

					 $fr_ad_setting['address_change_log']=json_encode($data);
					 $fr_ad_setting['flatno_street']='';
					 $fr_ad_setting['address']='';
					 $fr_ad_setting['landmark']='';
					 $fr_ad_setting['city_id']=0;
					 $fr_ad_setting['district_id']=0;
					 $fr_ad_setting['state_id']=0;
					 $fr_ad_setting['postal_code']='';
				
				   DB::table($this->config->get('tables.ADDRESS_MST'))
					->where('post_type',$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'))
					->where('address_type_id', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'))
					->where('relative_post_id', $postdata['fr_account_id'])
					->update($fr_ad_setting); 
				  }
					
	       }
		    return true;
		}
        return false;
    }

    public function check_franchise_region ($franchise_type, $state_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_STATE').' as ls')
                ->join($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fs', 'fs.relation_id', '=', 'ls.region_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'fs.account_id')
                ->where('ls.state_id', $state_id)
                ->whereNotNull('ls.region_id')
                ->whereRaw("ls.region_id <> ''")
                ->where('um.status', 1)
                ->where('fs.status', 1)
                ->where('fs.access_location_type', $franchise_type)
                ->pluck('uname');
        return !empty($res) ? $res : false;
    }

    public function change_block_franchisee ($post_data)
    {
        $response['status'] 		= $this->config->get('httperr.UN_PROCESSABLE');
        $response['msg'] 			= 'Failed to change Block';
        $response['label'] 			= '';
        $response['button_status']  = '';
        if (!empty($post_data))
        {
            $status = $post_data['status'];
            $block  = $post_data['status'];
            $response['status'] = 'ok';
           
            $data['block'] = $block;
            DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('account_id', $post_data['account_id'])
                    ->update($data);
            if ($block == 0)
            {
                if ($status == 0)
                {
                    $response['label'] = '<span class="label label-warning">Activated</span>';
                    $response['button_status'] = 'Block';
                }
                else if ($status == 1)
                {
                    $response['label'] = '<span class="label label-success">Verified</span>';
                    $response['button_status'] = 'Block';
                }
            }
            else if ($block == 1)
            {
                $response['label'] = '<span class="label label-danger">Blocked</span>';
                $response['button_status'] = 'UnBlock';
            }
            $response['msg'] = 'Block has been changed Successfully';
        }
        return $response;
    }

    public function change_franchisee_loginblock ($post_data)
    {
        $response['status'] = 'ERR';
        $response['msg'] = 'Failed to change Block';
        $response['label'] = '';
        $response['button_status'] = '';
        if (!empty($post_data))
        {
            $userid = $post_data['account_id'];
            $status = $post_data['status'];
            $block = $post_data['block'];
            $response['status'] = 'ok';
            if ($block == 1)
                $block = 0;
            else if ($block == 0)
                $block = 1;
            $data['login_block'] = $block;
            DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('account_id', $userid)
                    ->update($data);
            if ($block == 0)
            {
                if ($status == 0)
                {
                    $response['label'] = '<span class="label label-warning">Login Activated</span>';
                    $response['button_status'] = 'Block Login';
                }
                else if ($status == 1)
                {
                    $response['label'] = '<span class="label label-success">Login Activated</span>';
                    $response['button_status'] = 'Block Login';
                }
            }
            else if ($block == 1)
            {
                $response['label'] = '<span class="label label-danger">Login Blocked</span>';
                $response['button_status'] = 'UnBlock Login';
            }
            $response['msg'] = 'Login Block has been changed Successfully';
        }
        return $response;
    }

    public function update_fixed_commission ($country_id, $month, $year)
    {
        $sql = "SELECT ust.account_id, ust.subscrib_topup_id, ust.amount, ust.currency_id, ust.topup_date, ust.payment_type, ust.currency_id, ust.topup_date, ud.country, ud.state, ud.district, (SELECT payout_types FROM payout_types WHERE type_id = ust.payment_type) AS payout_types_name, (SELECT uname FROM ACCOUNT_MST WHERE account_id = (SELECT to_account_id FROM referral_earnings WHERE subscrib_topup_id = ust.subscrib_topup_id AND is_systemfee =0 AND is_lappsed_income =0 AND is_deleted =0)) AS to_account_name FROM (SELECT *FROM user_subscription_topup WHERE DATE( topup_date ) >= '2016-11-01' AND (payment_type =9 OR payment_type =14)) AS ust INNER JOIN ACCOUNT_MST AS um ON um.account_id = ust.account_id AND um.account_type_id <=2 AND um.is_deleted =0 INNER JOIN ACCOUNT_DETAILS AS ud ON ud.account_id = ust.account_id AND ud.country = '77' ";
        $sql .= "WHERE (um.direct_lineage NOT REGEXP CONCAT( '.*/', 832, '/.*' )AND um.account_id != '832')";
        $result = DB::select(DB::raw($sql));
        if (isset($result) && count($result))
        {
            foreach ($result as $sub_ACCOUNT_DETAILS)
            {
                $franchisee_commission = $this->usercommonObj->check_franchisee_commission($sub_ACCOUNT_DETAILS->payment_type);
                if ($franchisee_commission)
                {
                    echo $sub_ACCOUNT_DETAILS->subscrib_topup_id.'<br />';
                    $this->franchisee_commission(array(
                        'account_id'=>$sub_ACCOUNT_DETAILS->account_id,
                        'userdetails'=>$sub_ACCOUNT_DETAILS,
                        'relation_id'=>$sub_ACCOUNT_DETAILS->subscrib_topup_id,
                        'commission_type'=>$this->config->Get('constants.FR_COMMISSION_TYPE.FIXED_CONTRIBUTION'),
                        'amount'=>$sub_ACCOUNT_DETAILS->amount,
                        'currency_id'=>$sub_ACCOUNT_DETAILS->currency_id,
                        'payment_gateway'=>$sub_ACCOUNT_DETAILS->payout_types_name,
                        'to_account_name'=>$sub_ACCOUNT_DETAILS->to_account_name,
                        'created_date'=>$sub_ACCOUNT_DETAILS->topup_date));
                }
            }
        }
    }

    public function update_addfunds_commission ($country_id, $month, $year)
    {
        $sql = "SELECT uaf.uaf_id, uaf.account_id, uaf.amount, uaf.currency_id, uaf.payment_type, uaf.released_date, ud.country, ud.state, ud.district, (SELECT payout_types FROM payout_types WHERE type_id = uaf.payment_type) AS payout_types_name FROM (SELECT *FROM user_add_fund WHERE DATE(released_date ) >= '2016-11-01' AND (payment_type =9 OR payment_type =14) AND payment_status = 1 AND status <=1 AND purpose = 1) AS uaf INNER JOIN ACCOUNT_MST AS um ON um.account_id = uaf.account_id AND um.account_type_id <=2 AND um.is_deleted =0 INNER JOIN ACCOUNT_DETAILS AS ud ON ud.account_id = um.account_id AND ud.country ='77' ";
        $sql .= "WHERE (um.direct_lineage NOT REGEXP CONCAT(  '.*/', 832,  '/.*' ) AND um.account_id !=  '832')";
        $result = DB::select(DB::raw($sql));
        if (isset($result) && count($result))
        {
            foreach ($result as $sub_ACCOUNT_DETAILS)
            {
                $franchisee_commission = $this->usercommonObj->check_franchisee_commission($sub_ACCOUNT_DETAILS->payment_type);
                if ($franchisee_commission)
                {
                    echo $sub_ACCOUNT_DETAILS->uaf_id.'<br />';
                    $this->franchisee_commission(array(
                        'account_id'=>$sub_ACCOUNT_DETAILS->account_id,
                        'userdetails'=>$sub_ACCOUNT_DETAILS,
                        'relation_id'=>$sub_ACCOUNT_DETAILS->uaf_id,
                        'commission_type'=>$this->config->Get('constants.FR_COMMISSION_TYPE.ADD_FUNDS'),
                        'amount'=>$sub_ACCOUNT_DETAILS->amount,
                        'currency_id'=>$sub_ACCOUNT_DETAILS->currency_id,
                        'payment_gateway'=>$sub_ACCOUNT_DETAILS->payout_types_name,
                        'created_date'=>$sub_ACCOUNT_DETAILS->released_date));
                }
            }
        }
    }

    public function update_flexible_commission ($country_id, $month, $year)
    {
        $sql = "SELECT cf.pf_id, cf.donor_id, um.account_id, cf.amount, cf.from_currency_id as currency_id, cf.payment_type_id, cf.confirmed_on, dm.country, dm.state, dm.district, (SELECT payout_types FROM payout_types WHERE type_id = cf.payment_type_id) AS payout_types_name, um.uname as to_account_name FROM (SELECT *FROM campaign_funds WHERE DATE(confirmed_on ) >= '2016-11-01' AND (payment_type_id =9 OR payment_type_id =14) AND payment_status = 1 AND status = 1) AS cf INNER join campaigns as camp on camp.project_id = cf.project_id INNER JOIN ACCOUNT_MST AS um ON um.account_id = camp.account_id AND um.account_type_id <=2 AND um.is_deleted =0 INNER JOIN donor_mst as dm ON dm.donor_id = cf.donor_id AND dm.country ='77' ";
        $sql .= "WHERE (um.direct_lineage NOT REGEXP CONCAT(  '.*/', 832,  '/.*' ) AND um.account_id !=  '832')";
        $result = DB::select(DB::raw($sql));
        if (isset($result) && count($result))
        {
            foreach ($result as $sub_ACCOUNT_DETAILS)
            {
                $franchisee_commission = $this->usercommonObj->check_franchisee_commission($sub_ACCOUNT_DETAILS->payment_type_id);
                if ($franchisee_commission)
                {
                    echo $sub_ACCOUNT_DETAILS->pf_id.'<br />';
                    $this->franchisee_commission(array(
                        'account_id'=>$sub_ACCOUNT_DETAILS->account_id,
                        'userdetails'=>$sub_ACCOUNT_DETAILS,
                        'relation_id'=>$sub_ACCOUNT_DETAILS->pf_id,
                        'commission_type'=>$this->config->Get('constants.FR_COMMISSION_TYPE.FLEXIBLE_CONTRIBUTION'),
                        'amount'=>$sub_ACCOUNT_DETAILS->amount,
                        'currency_id'=>$sub_ACCOUNT_DETAILS->currency_id,
                        'payment_gateway'=>$sub_ACCOUNT_DETAILS->payout_types_name,
                        'to_account_name'=>$sub_ACCOUNT_DETAILS->to_account_name,
                        'created_date'=>$sub_ACCOUNT_DETAILS->confirmed_on));
                }
            }
        }
    }

    public function franchisee_commission ($arrData = array())
    {
        if (count($arrData))
        {
            extract($arrData);
            
            $userinfo = $this->commonstObj->get_userdetails_byid($userdetails->account_id);            
            $com_details_data = [];
            if (!empty($userinfo->country))
                $data['country_id'] = $userinfo->country;
            if (!empty($userinfo->state_id))
                $data['state_id'] = $userinfo->state_id;
            if (!empty($userinfo->district_id))
                $data['district_id'] = $userinfo->district_id;
            if (!empty($userinfo->region_id))
                $data['region_id'] = $userinfo->region_id;
            $com_details_data = $data;
            //print_r($data);
            $middle_level_franchisees = '';
            $current_date = date('Y-m-d H:i:s');
            $middle_level_franchisees = $this->usercommonObj->get_franchisee($data);
            if ($middle_level_franchisees && !empty($middle_level_franchisees))
            {
                foreach ($middle_level_franchisees as $franchisee)
                {
                    $remark = '';
					
					
	
                    $com_data['account_id'] = $franchisee->account_id;
                    $com_data['commission_type'] = $commission_type;
                    $com_data['relation_id'] = $relation_id;
                    $com_data['amount'] = $amount;
                    $com_data['currency_id'] = $currency_id;
                    //check commission already exists
                    $res = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
                            ->where('account_id', $franchisee->account_id)
                            ->where('relation_id', $relation_id)
                            ->where('commission_type', $commission_type)
                            ->get();
                    if (!($res && count($res)))
                    {
                       
                        $com_data['commission_perc'] = $per = $franchisee->diff_commission_per;
                        $com_data['commission_amount'] = ($amount * $per) / 100;
                        if (isset($created_date))
                        {
                            $com_data['created_date'] = $created_date;
                        }
                        else
                        {
                            $com_data['created_date'] = $current_date;
                        }
                        if ($commission_type == $this->config->get('constants.FR_COMMISSION_TYPE.ADD_FUNDS'))
                        {
                            $com_data['remark'] = "Add Funds through (".$payment_gateway.")";
                        }
                        $franchiseeObj = new Franchisee();
                        //$franchisee_balance_count = $franchiseeObj->get_balance_info($franchisee->account_id);
                        $com_data['status'] = $this->config->get('constants.COMISSION_STATUS_PENDING');
                        if (!empty($franchisee->low_transaction_details))
                        {
                            $low_transaction_details = json_decode(stripslashes($franchisee->low_transaction_details), true);
                            $count = 0;
                            $year = date('Y');
                            $month = date('m');
                            if (isset($low_transaction_details['count'][$year][$month]))
                            {
                                $count = $low_transaction_details['count'][$year][$month];
                            }
                            if ($count >= 1)
                            {
                                $com_data['status'] = $this->config->get('constants.COMISSION_STATUS_WAITING');
                            }
                        }
                        $relation_id = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
                                ->insertGetID($com_data);
                        if ($relation_id)
                        {
                            $com_details_data['fr_com_id'] = $relation_id;
                            $this->addFranchiseeCommissionDetails($com_details_data);
                        }
                    }                   
                }
            }
        }
    }
	
	
    public function getFranchiseeTypes ()
    {
        return DB::table($this->config->get('tables.FRANCHISEE_LOOKUP'))
                        ->get();
    }
	
	
	public function addFranchiseeCommissionDetails ($arr = array())
    {
        $country_id = $state_id = $district_id = $region_id = $city_id = null;
        extract($arr);
        $data = compact('country_id', 'state_id', 'district_id', 'region_id', 'city_id');
        if (empty(array_filter($data)) && isset($account_id))
        {
            $userdetails = $this->get_franchisee_details($account_id);
            if (!empty($userdetails))
            {
                if (!empty($userdetails->country))
                    $data['country_id'] = $userdetails->country;
                if (!empty($userdetails->state_id))
                    $data['state_id'] = $userdetails->state_id;
                if (!empty($userdetails->district_id))
                    $data['district_id'] = $userdetails->district_id;
                if (!empty($userdetails->region_id))
                    $data['region_id'] = $userdetails->region_id;
                if (!empty($userdetails->city_id))
                    $data['city_id'] = $userdetails->city_id;
            }
        }
        if (!empty(array_filter($data)))
        {
            if (DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_DETAILS'))
                            ->where('fr_com_id', $fr_com_id)
                            ->count() > 0)
            {
                return DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->where('fr_com_id', $fr_com_id)
                                ->update($data);
            }
            else
            {
                $data['fr_com_id'] = $fr_com_id;
                return DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->insertGetID($data);
            }
        }
    }

    public function get_franchisee ($arrData = array(), $get_ACCOUNT_DETAILS_only = false)
    {
        $middle_level_franchisees = [];
        if (!empty($arrData) && count($arrData))
        {
            foreach ($arrData as $k=> $v)
            {
                if (!empty($v))
                {
                    $query = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'fal.account_id')
                            ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
							->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
							->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                            ->where('fal.status', $this->config->get('constants.ACTIVE'))
                            ->where('um.status', $this->config->get('constants.ACTIVE'))
                            ->where('um.is_deleted', $this->config->get('constants.OFF'))
                            ->where('um.block', $this->config->get('constants.OFF'))
                            ->whereRaw('FIND_IN_SET('.$v.',fal.relation_id)');
                    if ($get_ACCOUNT_DETAILS_only)
                    {
                        $query->selectRaw('um.email,um.account_id,um.uname,concat(ud.first_name," ",ud.last_name) as full_name,um.uname,concat(lc.phonecode," ",um.mobile) as mobile');
                    }
                    else
                    {
                        $query->join($this->config->get('tables.FRANCHISEE_MST').' as fs', 'fs.account_id', '=', 'fal.account_id')
                                ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                                ->join($this->config->get('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                                ->where('fs.office_available', $this->config->Get('constants.ON'))
                                ->selectRaw('fal.account_id,fb.diff_commission_per,fb.flexible_commission_per,fs.low_transaction_details,ap.country_id,fl.level,um.email,um.account_id,um.uname,concat(ud.first_name," ",ud.last_name) as full_name,um.uname,concat(lc.phonecode," ",um.mobile) as mobile');
                    }
                    if ($k == 'city_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.FRANCHISEE_TYPE.CITY'));
                        $middle_level_franchisees['city'] = $query->first();
                    }
                    if ($k == 'district_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'));
                        $middle_level_franchisees['district'] = $query->first();
                    }
                    if ($k == 'state_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.FRANCHISEE_TYPE.STATE'));
                        $middle_level_franchisees['state'] = $query->first();
                    }
                    if ($k == 'region_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.FRANCHISEE_TYPE.REGION'));
                        $middle_level_franchisees['region'] = $query->first();
                    }
                    if ($k == 'country_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'));
                        $middle_level_franchisees['country'] = $query->first();
                    }
                }
            }
        }
        return array_filter($middle_level_franchisees);
    }

    public function getFranchiseeCommissionStatusByID ($status_id)
    {
        return DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_STATUS_LOOKUP'))
                        ->where('com_status_id', $status_id)
                        ->pluck('status_name');
    }
	
	public function check_franchisee_commission ($payment_type_id)
    {
        $result = DB::table($this->config->get('tables.PAYMENT_TYPES'))
                ->where('type_id', $payment_type_id)
                ->pluck('franchisee_commission_status');
        return ($result) ? $result : false;
    }

	public function block_status (array $data = array())
    {
        $op = array();
        extract($data);
		
        if (isset($status) && $status == 1)
        {
            $query= DB::table(config('tables.ACCOUNT_MST'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                            ->where('account_id', $account_id)
                            ->update(['block'=>config('constants.BLOCK')]);
							if(!empty($query)){
					     	   return json_encode(array(
							    'status'=>200,
						        'msg'=>trans('admin/franchisee/settings/block.affiliate_block'),
						        'alertclass'=>'alert-success'));
							}
        }
        else
        {
            $query_unblock= DB::table(config('tables.ACCOUNT_MST'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                            ->where('account_id', $account_id)
                            ->update(['block'=>config('constants.UNBLOCK')]);
						if(!empty($query_unblock)){
					     	 return json_encode(array(
							 'status'=>200,
						     'msg'=>trans('admin/franchisee/settings/block.affiliate_unblock'),
						     'alertclass'=>'alert-success'));
							
							}
        }
    }
	
	public function update_email($postdata){
        $uname=$postdata['uname'];
	    if(!empty($postdata['account_id'])){
			$details = $this->get_acc_details_by_id($postdata['account_id']);
	        $data['email'] =$postdata['email'];
	        if ($data['email'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['account_id'])
							->value('email'))
			   {
			$status = DB::table(config('tables.ACCOUNT_MST'))
					->where('account_id',$postdata['account_id'])
					->update($data);
				
			if (!empty($status)) {
				/*  $this->commonstObj->logoutAllDevices($postdata['user_account_id']); */
				return array(
				   'status'=>config('httperr.SUCCESS'),
					'msg'=>trans('admin/franchisee/settings/change_email.update_email_success',['uname'=>$details->firstname.' '.$details->lastname])
					);
				}
				else
				{
					return array(
						'msg'=>trans('admin/general.something_wrong'),
						'status'=>config('httperr.UN_PROCESSABLE'));
				}
			}
			else{
				return array(
						'msg'=>trans('admin/franchisee/settings/change_email.same_old'),
						'status'=>config('httperr.UN_PROCESSABLE'));
			}
		}
		return json_encode(array('msg'=>trans('admin/franchisee/settings/change_email.missing_parameters'), 'alertclass'=>'alert-warning'));
	}
	
	public function update_mobile($postdata) {	  

		$uname=$postdata['account_id'];		
		if(!empty($postdata['account_id'])){
			$details 		= $this->get_acc_details_by_id($postdata['account_id']);
			$data['mobile'] = $postdata['new_mobile'];
			if ($data['mobile'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['account_id'])
							->value('mobile')) {

				$status = DB::table(config('tables.ACCOUNT_MST'))
				->where('account_id',$postdata['account_id'])
				->update($data);
			
				if (!empty($status)){
					return array(
					'status'=>config('httperr.SUCCESS'),
					'msg'=>trans('admin/franchisee.update_mobile_success',['uname'=>$details->firstname.' '.$details->lastname]),
					);
				}
				else{
				    return [
					'msg'=>trans('admin/general.something_wrong'),
					'status'=>config('httperr.UN_PROCESSABLE')];
				}
	        }
			else{
				return [
					'msg'=>trans('admin/franchisee/settings/change_email.mobile_same_old'),
					'status'=>config('httperr.UN_PROCESSABLE')];
			}
	     }
		 return json_encode(array('msg'=>trans('admin/franchisee/settings/change_email.missing_parameters'), 'alertclass'=>'alert-warning'));
	}

	public function update_password ($postdata) {
       $uname=$postdata['uname'];		
	   $op = array('status'=>config('httperr.UN_PROCESSABLE'),'msg'=>trans('admin/franchisee/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning');
	   $details = $this->get_acc_details_by_id($postdata['account_id']);
		if(!empty($details) && !empty($details->account_id) && ($postdata['account_id']>0 || $postdata['uname']!='' )&& !empty(trim($postdata['new_pwd'])))
		{
			$data['pass_key'] = md5($postdata['new_pwd']);
		   $wdata = [];
/* 					   if(isset($postdata['account_id']) && !empty($postdata['account_id'])){
							$wdata['account_id'] = $postdata['account_id'];
						}				
						else if(isset($postdata['uname']) && !empty($postdata['uname'])){
							$wdata['uname'] = $postdata['uname'];
						}
 */			if ($data['pass_key'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id',$details->account_id)
							->value('pass_key'))  { 
				
				if(!empty($details)){
					$status = DB::table(config('tables.ACCOUNT_MST'))
						->where('account_id',$details->account_id)
						->update($data);
					
					if (!empty($status) && isset($status))
					{
						/* $this->commonstObj->logoutAllDevices($postdata['account_id']); */
						 $op = array(
							  'status'=>200,
							   'msg'=>trans('admin/franchisee/settings/changepwd.password_changed',['uname'=>strtoupper($details->firstname.' '.$details->lastname)]),
								'alertclass'=>'alert-success');
					}
					else
					{
						$op = array(
							'msg'=>trans('general.something_wrong'),
							'status'=>config('httperr.UN_PROCESSABLE'));
					}
				}
			}
			else{
			
				$op = [
					    'error'=>[
						"new_pwd" => trans('admin/franchisee/settings/changepwd.same_as_old'),
					    ],
					'status'=>$this->config->get('httperr.PARAMS_MISSING')];
			}
		}
        return $op;
    }
	
	public function update_pin ($postdata)
    {
		$uname=$postdata['uname'];
		if($postdata['account_id']>0 && !empty(trim($postdata['new_pin'])))
		{
			$data['trans_pass_key'] = md5($postdata['new_pin']);
		     $details = $this->get_acc_details_by_id($postdata['account_id']);
		
			if ($data['trans_pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['account_id'])
							->value('trans_pass_key'))
			   { 
				$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id',$postdata['account_id'])
					->update($data);
				if (!empty($status) && isset($status))
				{
					return array(
					    'status'=>200,
						'msg'=>trans('admin/franchisee.pin_changed',['uname'=>$details->firstname.' '.$details->lastname]));
				}
				else
				{
					return array(
						'msg'=>trans('general.something_wrong'),
						'status'=>$this->config->get('httperr.UN_PROCESSABLE'));
				  }
		       }
		    	else{
			     	 return [
					      'error'=>[
						"new_pin" => trans('admin/franchisee.same_as_old'),
					    ],
					'status'=>$this->config->get('httperr.PARAMS_MISSING')];
			}
		}
        return json_encode(array('msg'=>trans('admin/franchisee.missing_parameters'), 'alertclass'=>'alert-warning'));
    }

	
	/* KYC Verification */
	public function kycDocumentList ($arr)
    {   
	    extract($arr);
        $res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as av')
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'av.document_type_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'av.account_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'av.account_id')
                ->where('am.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                ->where('am.is_deleted', $this->config->get('constants.OFF'))
                ->where('av.is_deleted', $this->config->get('constants.OFF'));				
		
		if (isset($search_term) && !empty($search_term))
        {
            $res->where(function($sub) use($search_term)
            {
                $sub->Where('am.uname', 'like', '%'.$search_term.'%')
                        ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'), 'like', '%'.$search_term.'%');
            });
        }     
        if (isset($arr['account_id']) && !empty($arr['account_id']))
        {
            $res->where('av.account_id', $arr['account_id']);
        }
        if (isset($arr['uname']) && !empty($arr['uname']))
        {
            $res->where('am.uname', $arr['uname']);
        }
        if (isset($arr['status']) && $arr['status'] != '')
        {
            $res->where('av.status_id', $arr['status']);
        }
        if (isset($arr['type_filer']) && !empty($arr['type_filer']))
        {
            $res->where('av.document_type_id', $arr['type_filer']);
        }
        if (!empty($arr['from']))
        {
            $res->whereDate('av.created_on', '>=', date('Y-m-d', strtotime($arr['from'])));
        }
        if (!empty($arr['to']))
        {
            $res->whereDate('av.created_on', '<=', date('Y-m-d', strtotime($arr['to'])));
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']) && !empty($arr['orderby']))
        {
            $res->orderby('av.created_on', $arr['order']);
        }
        else
        {
            $res->orderby('av.created_on', 'DESC');
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            $result = $res->selectRaw('av.*, dt.type, dt.document_type_id, dt.other_fields as doc_other_fields, am.uname, concat(ad.firstname,\' \',ad.lastname) as full_name')->get();
								
            array_walk($result, function(&$v)
            { 
			    $v->actions = [];				
                $v->created_on = !empty($v->created_on) ?  showUTZ($v->created_on, 'd-M-Y H:i:s'): '';
                $v->other_fields = !empty($v->other_fields) ? json_decode($v->other_fields) : [];
                $v->doc_other_fields = !empty($v->doc_other_fields) ? json_decode($v->doc_other_fields, true) : [];
                array_walk($v->other_fields, function(&$field, $k) use($v)
                {
                    $field = ['id'=>$k, 'label'=>$v->doc_other_fields[$k]['label'], 'value'=>$field];
                });
				$v->status = trans('admin/general.verification_status.'.$v->status_id);
				$v->status_class =  $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS_CLASS.'.$v->status_id);
				$v->path = asset($this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.WEB').$v->path); 
						
				if ($v->status_id == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.PENDING'))
                {	
					$v->actions[] = ['url'=>route('admin.franchisee.change-document-status'), 'class'=>'change_status', 'data'=>['id'=>$v->uv_id,'status'=>$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.VERIFIED'),'curstatus'=>$v->status_id,'account_id'=>$v->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Verify']; 					
					$v->actions[] = ['url'=>route('admin.franchisee.change-document-status'), 'class'=>'change_status', 'data'=>['id'=>$v->uv_id,'status'=>$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED'),'curstatus'=>$v->status_id,'account_id'=>$v->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Reject']; 
								 
				}elseif($v->status_id == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.VERIFIED')){
				    $v->actions[] = ['url'=>route('admin.franchisee.change-document-status'), 'class'=>'change_status', 'data'=>['id'=>$v->uv_id,'status'=>$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED'),'curstatus'=>$v->status_id,'account_id'=>$v->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Reject']; 
				}
				unset($v->doc_other_fields);
            });	
            return $result;
        }
    }
	
	public function changeKycDocStatus ($data = array())
    {  	
	    $kyc=[];	
        extract($data);
        if (DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                        ->where('uv_id', $uv_id)
                        ->update(array('status_id'=>$status)))
        {
            $res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as av')  
					->join($this->config->get('tables.FRANCHISEE_MST').' as at', 'at.account_id', '=', 'av.account_id')
					->where('av.uv_id', $uv_id)			
					->select('av.account_id','at.kyc_status')
					->first();
					
		    $kyc_status = json_decode(stripslashes($res->kyc_status));				
			$total_doc = $kyc_status->total_doc;
			if($status == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED')){
				$kyc_status->submitted_doc = --$kyc_status->submitted_doc;
				$kyc['kyc_status'] = addslashes(json_encode($kyc_status));
			} else if($status == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.VERIFIED') && $curstatus = $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED')){
				/* $kyc_status->submitted_doc = ++$kyc_status->submitted_doc; */
				$kyc_status->verified_doc = ++$kyc_status->verified_doc;
				$kyc['kyc_status'] = addslashes(json_encode($kyc_status));		
			} 
					
		    $verified = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as av')
						->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'av.document_type_id', '=', 'dt.document_type_id')
						->where('av.account_id', $res->account_id)
						->where('dt.status', $this->config->get('constants.ON'))
						->where('av.status_id', $this->config->get('constants.ON'))
						->where('av.is_deleted', $this->config->get('constants.OFF'))													
						->selectRaw('av.document_type_id')
						->count();					
			
			if ($total_doc  ==  $verified)
            {
			   $kyc['is_kyc_verified'] = $this->config->get('constants.ON');
			   $kyc['kyc_verified_on'] = getGTZ();
			}else {								
			   $kyc['is_kyc_verified'] = $this->config->get('constants.OFF');			  
			   $kyc['kyc_verified_on'] = NULL;
			}
			DB::table($this->config->get('tables.FRANCHISEE_MST'))
				->where('account_id', $res->account_id)
				->update($kyc);
			return true;
        }
        return false;
    }
	
		public function kycDocTypelist ()
		{
			return DB::table($this->config->get('tables.DOCUMENT_TYPES'))
							->where('status', $this->config->get('constants.ON'))
							->select('document_type_id', 'type', 'other_fields')
							->get();
		}
		public function franchisee_types(){
		   return DB::table($this->config->get('tables.FRANCHISEE_LOOKUP'))
							->where('status', $this->config->get('constants.ON'))
							->select('franchisee_typeid', 'franchisee_type')
							->get();
		}	
      public function get_franchisee_commission_status ()
        {
        $status = DB::table(config('tables.FRANCHISEE_COMMISSION_STATUS_LOOKUP'))
                ->select('status_name', 'com_status_id')
                ->get();
        if (isset($status) && count($status)){
            return $status;
        }
        else {
            return false;
        }
      }
	
  public function fundtransfer_commission_details($arr = array())
    {       
       extract($arr);
        $commissions = DB::table(Config('tables.FRANCHISEE_COMMISSION').' as fc')
                ->join(Config('tables.FRANCHISEE_FUND_TRANSFER').' as fft', 'fft.fft_id', '=', 'fc.relation_id')
				->join(config('tables.ACCOUNT_MST').' as ru', 'ru.account_id', '=', 'fc.account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as rum', 'rum.account_id', '=', 'fc.account_id')
				->join(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'fc.account_id')
				->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')

                ->join(config('tables.ACCOUNT_MST').' as fu', 'fu.account_id', '=', 'fft.from_account_id')
                ->join(config('tables.ACCOUNT_DETAILS').' as fud', 'fud.account_id', '=', 'fft.from_account_id')
                ->join(config('tables.ACCOUNT_MST').' as tu', 'tu.account_id', '=', 'fft.to_account_id')
                ->join(config('tables.ACCOUNT_DETAILS').' as tud', 'tud.account_id', '=', 'fft.to_account_id')
                ->join(config('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'fc.currency_id')
                ->join(config('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as tl', 'tl.commission_type_id', '=', 'fc.commission_type')
                ->join(config('tables.FRANCHISEE_COMMISSION_STATUS_LOOKUP').' as sl', 'sl.com_status_id', '=', 'fc.status')
                ->where('fc.is_deleted',config('constants.OFF'))
                ->where(function($sub)
				   {
					$sub->where(function($ft)
					{
						$ft->whereIn('fc.commission_type', array(config('constants.FR_COMMISSION_TYPE.FRFTU'), config('constants.FRANCHISEE_COMMISSION_ADMIN_FUND_TRANS_SC')) );
					});
        })
		
	->select(DB::raw("fc.fr_com_id,fc.commission_type,fc.created_date,fft.transaction_id, IF( fft.from_user_type = 4,'Admin',concat(fud.firstname,' ',fud.lastname)) as from_full_name,fu.user_code as from_user_code,concat(tud.firstname,' ',tud.lastname) as to_full_name,tu.user_code as to_user_code,fc.amount,fc.commission_amount,c.currency as currency,c.currency_symbol,c.decimal_places,fc.status,sl.status_name,sl.label_class as status_label, fc.confirmed_date, fc.remark, fc.statementline_id,ru.uname as receiver_uname, concat(rum.firstname,' ',rum.lastname) as receiver_fullname, (CASE fal.access_location_type
					WHEN ".config('constants.FRANCHISEE_TYPE.COUNTRY')." THEN (select country from ".config('tables.LOCATION_COUNTRY')." where country_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.REGION')." THEN (select region from ".config('tables.LOCATION_REGIONS')."  where region_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.STATE')." THEN (select state from ".config('tables.LOCATION_STATE')."  where state_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.DISTRICT')." THEN (select district from ".config('tables.LOCATION_DISTRICTS')."  where district_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.CITY')." THEN (select city from ".config('tables.LOCATION_CITY')."  where city_id = fal.relation_id)
					END) as franchisee_location, fl.franchisee_type as franchisee_type_name"));
					
					$result= $commissions->get();
		
		
		if (!empty($search_term) && isset($search_term))
        {
            /* $commissions->where(function($search) use($search_term)
            {
                $search_term = '%'.$search_term.'%';
				 if(is_numeric($search_term)){ 
                      
                 }					  
                $search->where('fft.transaction_id', 'like', $search_term)
					
                        ->orWhere('fu.user_code', 'like', $search_term)
                        ->orWhere('tu.user_code', 'like', $search_term)
                        ->orWhere(DB::raw('concat(fud.firstname," ",fud.lastname)'), 'like', $search_term)
                        ->orWhere(DB::raw('concat(tud.firstname," ",tud.lastname)'), 'like', $search_term)
                        ->orWhere(DB::raw('concat(rum.firstname," ",rum.lastname)'), 'like', $search_term);
            }); */
			 if(is_numeric($search_term)){   
					  $commissions->where(function($search) use ($search_term){
					  $search->where('fft.transaction_id', 'like', $search_term)
                             ->orWhere('fu.user_code', 'like', $search_term)
                              ->orWhere('tu.user_code', 'like', $search_term);
                 });
              }
			  else{   
                      $commissions->where(function($search) use ($search_term){
                          $search->Where(DB::raw('concat(fud.firstname," ",fud.lastname)'), 'like', $search_term)
                                  ->orWhere(DB::raw('concat(tud.firstname," ",tud.lastname)'), 'like', $search_term)
                                  ->orWhere(DB::raw('concat(rum.firstname," ",rum.lastname)'), 'like', $search_term);   
                              });
                   }  
        }
        if (!empty($status))
        {
            $commissions->where('fc.status', $status);
        }
		if (isset($from) && isset($to) && !empty($from) && !empty($to))	{ 
					 $commissions->whereDate('fc.created_date', '>=', getGTZ($from,'Y-m-d'));
					 $commissions->whereDate('fc.created_date', '<=', getGTZ($to,'Y-m-d'));
				}
				else if (!empty($from) && isset($from)){ 
					 $commissions->whereDate('fc.created_date', '<=', getGTZ($from,'Y-m-d'));
				}
				else if (!empty($to) && isset($to)){ 
					 $commissions->whereDate('fc.created_date', '>=', getGTZ($to,'Y-m-d'));
				} 
			   if (isset($length) && !empty($length))
				{
					$commissions->skip($start)->take($length);
				}
				if (isset($orderby) && isset($order))
                {
                    $commissions->orderBy($orderby, $order);
                }
				if (isset($count) && !empty($count))
				{
					return $commissions->count();
				}
			   else
                 {
					$result= $commissions->get();
					if(!empty($result)) {
					array_walk($result, function(&$data)
					{
					/*  $remar=json_decode($data->remark); */
				    
				/* 	if (!empty($data->remark) && strpos($data->remark, '}') > 0) {		
							 $data->remark = json_decode(stripslashes($data->remark));			
							 $data->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark']))); 
						}
						else {
							 $data->remark = $data->statementline;
						} */
				   	$data->amount = $data->currency_symbol.' '.number_format($data->amount, \AppService::decimal_places($data->amount), '.', ',').' '.$data->currency;
					$data->commission_amount = $data->currency_symbol.' '.number_format($data->commission_amount, \AppService::decimal_places($data->commission_amount), '.', ',').' '.$data->currency; 
					$data->created_date = (!empty($data->created_date)) ? showUTZ($data->created_date) :'';
					$data->confirmed_date = (!empty($data->confirmed_date)) ? showUTZ($data->confirmed_date) :'';  
					});
			
					 return $result; 
                }
			}
    }       
	public function merchant_enrolment_commission($arr = array())
    { 
        extract($arr);   
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')	        
					 ->join($this->config->get('tables.FRANCHISEE_MST').' as fm', 'fm.account_id', '=', 'fc.account_id')
					 ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fm.franchisee_type')
				     ->join($this->config->get('tables.ACCOUNT_MST') . ' as am', 'am.account_id', '=', 'fm.account_id')
                     ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'fm.account_id')
					 ->join($this->config->get('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'fc.statementline_id')	
					 ->join($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fct', function($join){
						$join->on('fct.commission_type_id', ' = ', 'fc.commission_type');
					 })
					->join($this->config->get('tables.FRANCHISEE_COMMISSION_STATUS_LOOKUP').' as fsl', 'fsl.com_status_id', '=', 'fc.status')
					->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fc.currency_id')
					
					->where('fc.commission_type',$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.MCMF'))
					->where('fc.is_deleted',$this->config->get('constants.OFF'))
					->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'));
							if(isset($fr_type) && !empty($fr_type)){
								$qry->where('fm.franchisee_type',$fr_type);
							}
				if (isset($from) && isset($to) && !empty($from) && !empty($to))
				{
					$qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($from))."'");
					$qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($to))."'");
				} 
			   else if (isset($from) && !empty($from))
				{ 
					 $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($from))."'");
				}
			   else if (isset($to) && !empty($to))
				{
					   $qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($to))."'");
				}
                if(isset($search_text) && !empty($search_text))
				       { 
		
						$search_text='%'.$search_text.'%';
						if(!empty($filterchk) && !empty($filterchk))
						{   
							$search_field=['channel_name'=>'concat_ws(" ",ad.firstname,ad.lastname)','chanel_code'=>'am.user_code'];
							$qry->where(function($sub) use($filterchk,$search_text,$search_field){
								foreach($filterchk as $search)
								{  
									if(array_key_exists($search,$search_field)){
									  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
									} 
								}
							});
						}
						else{
							$qry->where(function($wcond) use($search_text){
							   $wcond->Where('am.user_code','like',$search_text)
								 ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'),'like',$search_text);
							}); 
						} 			
			         }

				if (isset($orderby) && isset($order)) {
					$qry->orderBy($orderby, $order);
				}
				else {				
					$qry->orderBy('fc.fr_com_id', 'DESC');					
				} 
				if (isset($length) && !empty($length)) {
					$qry->skip($start)->take($length);
				}
				if (isset($count) && !empty($count)) {
				
					return $qry->count();
				} 
             else   
             { 	              
				 $qry->select('fc.fr_com_id','fc.created_date','fc.amount',DB::Raw('sum(fc.commission_amount) as commission_amount'),DB::Raw('sum(fc.net_pay) as net_pay'),DB::raw('DATE_FORMAT(created_date,\'%m-%Y\') as month'),'fc.remark','fc.statementline_id','fc.status','fc.confirmed_date','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places','fsl.status_name','fsl.label_class','st.statementline','fc.tax','fc.commission_type as commission_type_id','fc.from_date','fct.commission_type','fct.fct_code','fc.account_id','fl.franchisee_type','am.user_code','fm.company_name',DB::raw('concat_ws(\' \',ad.firstname,ad.lastname) as full_name'));  
                  $qry->groupby('month','commission_type_id');		 
                 $commission = $qry->get();		
                if ($commission){
					array_walk($commission, function(&$c)	{
						/* $c->created_date = !empty($c->created_date) ? showUTZ($c->created_date,'M-Y'):''; */
						
						$c->from_date = !empty($c->from_date) ? showUTZ($c->from_date,'M-Y'):'';
				
						$c->confirmed_date = !empty($c->confirmed_date) ? showUTZ($c->confirmed_date):'';
						if (!empty($c->remark) && strpos($c->remark, '}') > 0) {		
							$c->remark = json_decode(stripslashes($c->remark));			
							 $c->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark']))); 
						}
						else {
							$c->remark = $c->statementline;
						}
						$c->commission_type= trans('transactions.franchisee_commission_type.'.$c->commission_type_id.'');
						$c->amount = \CommonLib::currency_format($c->amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						$c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						
						$c->net_pay = \CommonLib::currency_format($c->net_pay, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
											
						unset($c->statementline,$c->statementline_id);
					   
					 });		
				  return !empty($commission) ? $commission : [];				
				}
			} 
       
		return NULL;
    }
		
    public function Merchant_enrollment_fee_details($arr = array()){	
		
	 extract($arr);   
	 
		  $qry = DB::table($this->config->get('tables.FRANCHISEE_MERCHANT_FEE').' as fm')
		             ->join($this->config->get('tables.STORES').' as st', 'st.store_id', '=', 'fm.store_id')
			         ->join($this->config->get('tables.ADDRESS_MST').' as am', function($join){
					     $join->on('am.relative_post_id', ' = ', 'st.store_id')
							->where('am.post_type','=',$this->config->get('constants.ADDRESS_POST_TYPE.STORE'))
							->where('am.address_type_id','=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
				       })
					  ->join($this->config->get('tables.LOCATION_DISTRICTS').' as lod', 'lod.district_id', '=', 'fm.district_id')
					  ->join($this->config->get('tables.LOCATION_STATE').' as los', 'los.state_id', '=', 'fm.state_id')
					  ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fm.currency_id')
					   
				      ->where('fm.account_id',$account_id)
					  ->where('fm.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'))
					  ->where('fm.is_deleted',$this->config->get('constants.OFF')); 
					  
						if (isset($created_on_date) &&  !empty($created_on_date)){
								 $qry->whereRaw("MONTH(fm.created_on) ='".date('m', strtotime($created_on_date))."'");
								 $qry->whereRaw("YEAR(fm.created_on) ='".date('Y', strtotime($created_on_date))."'");
						 }
						 
						if (isset($from) && isset($to) && !empty($from) && !empty($to)){
							$qry->whereRaw("DATE(fm.created_on) >='".date('Y-m-d', strtotime($from))."'");
							$qry->whereRaw("DATE(fm.created_on) <='".date('Y-m-d', strtotime($to))."'");
						} 
					   else if (isset($from) && !empty($from)){ 
							 $qry->whereRaw("DATE(fm.created_on) <='".date('Y-m-d', strtotime($from))."'");
						}
					   else if (isset($to) && !empty($to)){
							   $qry->whereRaw("DATE(fm.created_on) >='".date('Y-m-d', strtotime($to))."'");
						}
					if(isset($search_text) && !empty($search_text))
				       { 
		
						$search_text='%'.$search_text.'%';
						if(!empty($filterchk) && !empty($filterchk))
						{   
							$search_field=['store_name'=>'st.store_name','store_code'=>'st.store_code'];
							$qry->where(function($sub) use($filterchk,$search_text,$search_field){
								foreach($filterchk as $search)
								{  
									if(array_key_exists($search,$search_field)){
									  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
									} 
								}
							});
						}
						else{
							$qry->where(function($wcond) use($search_text){
							   $wcond->Where('st.store_code','like',$search_text)
									 ->orwhere('st.store_name','like',$search_text);
							}); 
						} 			
			         }         
					 if (isset($orderby) && isset($order)) {
						$qry->orderBy($orderby, $order);
					}
					else {				
						$qry->orderBy('fm.fr_fee_id', 'DESC');
						//$qry->orderBy('fm.created_on', 'DESC');
					} 
					if (isset($length) && !empty($length)) {
						$qry->skip($start)->take($length);
					}
					if (isset($count) && !empty($count)) {
					
						return $qry->count();
					} 
				 else   
				 { 	
				 $qry->select('fm.account_id','fm.store_id','fm.state_id','fm.district_id','fm.commission_amount','fm.created_on','st.store_name','st.store_code','am.address','lod.district','los.state','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places'); 
					  $commission = $qry->get();		 
					 if ($commission){
						array_walk($commission, function(&$c)	{
							
							$c->month_for = !empty($c->created_on) ? showUTZ($c->created_on,'M-Y'):'';
							$c->created_on = !empty($c->created_on) ? showUTZ($c->created_on,'d-M-Y H:i:s'):'';
						    $c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]); 
							
						 });
					  return !empty($commission) ? $commission : [];	
					}   /*  print_r($commission); die;	 */
				} 
		return NULL;
	
	}
	
	public function get_profit_sharing($arr = array()){
		  extract($arr);   
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')	        
					 ->join($this->config->get('tables.FRANCHISEE_MST').' as fm', 'fm.account_id', '=', 'fc.account_id')
					 ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fm.franchisee_type')
				     ->join($this->config->get('tables.ACCOUNT_MST') . ' as am', 'am.account_id', '=', 'fm.account_id')
                     ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'fm.account_id')
					 ->join($this->config->get('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'fc.statementline_id')	
					 ->join($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fct', function($join){
						$join->on('fct.commission_type_id', ' = ', 'fc.commission_type');
					 })
					->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fc.currency_id')
					
					->where('fc.commission_type',$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.PS'))
					->where('fc.is_deleted',$this->config->get('constants.OFF'))
					->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'));
					   
				if(isset($fr_type) && !empty($fr_type)){
					$qry->where('fm.franchisee_type',$fr_type);
				}
				if (isset($from) && isset($to) && !empty($from) && !empty($to))
				{
					$qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($from))."'");
					$qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($to))."'");
				} 
			   else if (isset($from) && !empty($from))
				{ 
					 $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($from))."'");
				}
			   else if (isset($to) && !empty($to))
				{
					   $qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($to))."'");
				}
                if(isset($search_text) && !empty($search_text))
				       { 
		
						$search_text='%'.$search_text.'%';
						if(!empty($filterchk) && !empty($filterchk))
						{   
							$search_field=['channel_name'=>'concat_ws(" ",ad.firstname,ad.lastname)','chanel_code'=>'am.user_code'];
							$qry->where(function($sub) use($filterchk,$search_text,$search_field){
								foreach($filterchk as $search)
								{  
									if(array_key_exists($search,$search_field)){
									  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
									} 
								}
							});
						}
						else{
							$qry->where(function($wcond) use($search_text){
							   $wcond->Where('am.user_code','like',$search_text)
								 ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'),'like',$search_text);
							}); 
						} 			
			         }

				if (isset($orderby) && isset($order)) {
					$qry->orderBy($orderby, $order);
				}
				else {				
					$qry->orderBy('fc.fr_com_id', 'DESC');					
				} 
				if (isset($length) && !empty($length)) {
					$qry->skip($start)->take($length);
				}
				if (isset($count) && !empty($count)) {
				
					return $qry->count();
				} 
             else   
             { 	              
				 $qry->select('fc.fr_com_id','fc.created_date','fc.amount',DB::Raw('sum(fc.commission_amount) as commission_amount'),DB::Raw('sum(fc.net_pay) as net_pay'),DB::raw('DATE_FORMAT(created_date,\'%m-%Y\') as month'),'fc.remark','fc.statementline_id','fc.status','fc.confirmed_date','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places','st.statementline','fc.tax','fc.commission_type as commission_type_id','fc.from_date','fct.commission_type','fct.fct_code','fc.account_id','fl.franchisee_type','am.user_code','fm.company_name',DB::raw('concat_ws(\' \',ad.firstname,ad.lastname) as full_name'));  
				  $qry->groupby('account_id','month');
             /*  $qry->groupby('month','commission_type_id'); */		 
                 $commission = $qry->get();		
                if ($commission){
					array_walk($commission, function(&$c)	{
						/* $c->created_date = !empty($c->created_date) ? showUTZ($c->created_date,'M-Y'):''; */
						
						$c->from_date = !empty($c->from_date) ? showUTZ($c->from_date,'M-Y'):'';
				
						$c->confirmed_date = !empty($c->confirmed_date) ? showUTZ($c->confirmed_date):'';
						if (!empty($c->remark) && strpos($c->remark, '}') > 0) {		
							$c->remark = json_decode(stripslashes($c->remark));			
							 $c->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark']))); 
						}
						else {
							$c->remark = $c->statementline;
						}
						$c->commission_type= trans('transactions.franchisee_commission_type.'.$c->commission_type_id.'');
						$c->amount = \CommonLib::currency_format($c->amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						$c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						
						$c->net_pay = \CommonLib::currency_format($c->net_pay, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						unset($c->statementline,$c->statementline_id);
					   
					 });		
				  return !empty($commission) ? $commission : [];				
				}
			} 
		return NULL;
  }
  
   public function get_profit_sharing_details($arr = array()){
		 extract($arr); 
		 if (isset($account_id))
         {
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')
			        ->join($this->config->get('tables.FRANCHISEE_MST').' as fm', 'fm.account_id', '=', 'fc.account_id')
					 ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fm.franchisee_type')
				     ->join($this->config->get('tables.ACCOUNT_MST') . ' as am', 'am.account_id', '=', 'fm.account_id')
                     ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'fm.account_id')
					 ->join($this->config->get('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'fc.statementline_id')	
					 ->join($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fct', function($join){
						$join->on('fct.commission_type_id', ' = ', 'fc.commission_type');
					 })
				->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fc.currency_id')
				
				->where('fc.commission_type',$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.PS'))
		        ->where('fc.is_deleted',$this->config->get('constants.OFF'))
				->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'));
			
			    if (isset($account_id) && !empty($account_id)){
                     $qry->where('fc.account_id', $account_id);
			   }
			   if (isset($created_on_date) &&  !empty($created_on_date)){
								 $qry->whereRaw("MONTH(fc.from_date) ='".date('m', strtotime($created_on_date))."'");
								 $qry->whereRaw("YEAR(fc .from_date) ='".date('Y', strtotime($created_on_date))."'");
			     }
				if (isset($from) && isset($to) && !empty($from) && !empty($to))
				{
					$qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($from))."'");
					$qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($to))."'");
				} 
			   else if (isset($from) && !empty($from))
				{ 
					 $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($from))."'");
				}
			   else if (isset($to) && !empty($to))
				{
					   $qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($to))."'");
				}		
					   
				if (isset($orderby) && isset($order)) {
					$qry->orderBy($orderby, $order);
				}
				else {				
					$qry->orderBy('fc.fr_com_id', 'DESC');					
				} 
				if (isset($length) && !empty($length)) {
					$qry->skip($start)->take($length);
				}
				if (isset($count) && !empty($count)) {
				
					return $qry->count();
				} 
             else   
             { 	              
				 $qry->select('fc.fr_com_id','fc.created_date','fc.amount',DB::Raw('sum(fc.commission_amount) as commission_amount'),DB::Raw('sum(fc.net_pay) as net_pay'),DB::raw('DATE_FORMAT(created_date,\'%m-%Y\') as month'),'fc.remark','fc.statementline_id','fc.status','fc.confirmed_date','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places','st.statementline','fc.tax','fc.commission_type as commission_type_id','fc.from_date','fc.commission_perc','fct.commission_type','fct.fct_code','fl.franchisee_type','am.user_code','fm.company_name',DB::raw('concat_ws(\' \',ad.firstname,ad.lastname) as full_name'));  
                  $qry->groupby('month','commission_type_id');		 
                 $commission = $qry->get();		
                if ($commission){
					array_walk($commission, function(&$c)	{
						/* $c->created_date = !empty($c->created_date) ? showUTZ($c->created_date,'M-Y'):''; */
						
						$c->from_date = !empty($c->from_date) ? showUTZ($c->from_date,'M-Y'):'';
						$c->commission_perc=!empty($c->commission_perc)? $c->commission_perc.'%' :'';
						
						$c->confirmed_date = !empty($c->confirmed_date) ? showUTZ($c->confirmed_date):'';
						if (!empty($c->remark) && strpos($c->remark, '}') > 0) {		
							$c->remark = json_decode(stripslashes($c->remark));			
							 $c->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark']))); 
						}
						else {
							$c->remark = $c->statementline;
						}
						$c->commission_type= trans('transactions.franchisee_commission_type.'.$c->commission_type_id.'');
						$c->amount = \CommonLib::currency_format($c->amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						$c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						$c->net_pay = \CommonLib::currency_format($c->net_pay, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						
						unset($c->statementline,$c->statementline_id);
					   
					 });		
				  return !empty($commission) ? $commission : [];				
				}
			} 
        }
		return NULL;
	}
  
  public function get_acc_details_by_id($account_id){
		return DB::table(config('tables.ACCOUNT_MST').' as am')
				->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','am.account_id')
				->where('am.account_id',$account_id)
				->where('am.is_deleted',0)
				->first();
	}	
	
	public function franchisee_details($account_id){
		
	 return   $qry=  DB::table($this->config->get('tables.FRANCHISEE_MST').' as fm')
					 ->join($this->config->get('tables.ACCOUNT_MST') . ' as amt', 'amt.account_id', '=', 'fm.account_id')
                     ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'fm.account_id')
					 ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fm.franchisee_type')
		             ->where('fm.account_id',$account_id)
					 ->where('fm.status',$this->config->get('constants.ON'))
					 ->select('amt.user_code','fm.company_name',DB::raw('concat_ws(\' \',ad.firstname,ad.lastname) as full_name'),'fl.franchisee_type')
					 ->first();
	   }
	 public function get_franchisee_type(){
		 
		  return   $qry=  DB::table($this->config->get('tables.FRANCHISEE_LOOKUP').' as fm')
					      ->where('fm.status',$this->config->get('constants.ON'))
					      ->select('fm.franchisee_typeid','fm.franchisee_type')
					      ->get();
	   }
	//Quick Login //

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
				->select(DB::Raw('account_id,user_code,pass_key,trans_pass_key,email,uname,mobile,login_block,block,is_affiliate,is_closed,account_type_id'));				
				
	         $userData = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
                ->join($this->config->get('tables.FRANCHISEE_MST') . ' as fm', 'fm.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as st', 'st.account_id', '=', 'um.account_id')				
				->join($this->config->get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'um.account_type_id')
				->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'st.currency_id')			
				->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'st.country_id')
			    ->selectRaw('um.account_id,um.user_code,st.language_id,um.account_type_id,fm.franchisee_id,fm.franchisee_type,fm.company_name,fm.logo_path,at.account_type_name,um.is_closed,um.pass_key,um.trans_pass_key,um.uname,concat_ws(\' \',ud.firstname,ud.lastname) as full_name,ud.firstname,ud.lastname,um.mobile,lc.country,lc.phonecode,ud.profile_img,um.email,st.country_id,st.currency_id,cur.currency as currency_code,um.block,um.login_block,st.is_mobile_verified,st.is_email_verified,st.is_verified')
                ->addBinding($fisrtQry->getBindings())
			    ->first();
        if (!empty($userData)) {
            if ($userData->is_closed == $this->config->get('constants.OFF')) {
                if ($userData->login_block == $this->config->get('constants.OFF')) {
				
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
								'has_pin' => (!empty($userData->trans_pass_key)? true : false));
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
				
						$this->session->put('frdata', (object)$sesdata);
						$token_update = DB::table($this->config->get('tables.ACCOUNT_LOG'))
									->where('account_log_id', $account_log_id)
									->update($update);  
						
                        return ['status'=>1,'msg'=>'Your are successfully logged in'];
                    } else {
						return ['status'=>3,'msg'=>'Incorrect username or password'];
                    }
              
            } else {
				return ['status'=>6,'msg'=>'Please check your Login Id / Email Id'];
            }
        } else {
			return ['status'=>2,'msg'=>'Please check your Login Id / Email Id'];
        }
        return ['status'=>7,'msg'=>'Please check your Login Id / Email Id'];
    }
	
	
}