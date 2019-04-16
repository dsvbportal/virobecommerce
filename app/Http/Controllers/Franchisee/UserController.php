<?php
namespace App\Http\Controllers\Franchisee;

use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\UserModel;
use App\Models\Franchisee\FrModel;
use App\Helpers\CommonNotifSettings;
use App\Models\Commonsettings;
use App\Models\LocationModel;
use File;
use Storage;
use CommonLib;
use Request;
use Response;
use DB;

class UserController extends FrBaseController
{
    public function __construct ()
    {
        parent::__construct();
		$this->frObj = new FrModel();
        $this->userObj 	= new UserModel();
		$this->locationObj = new LocationModel();
    }
	public function create_User(){	 
	     $data = [];
		 $data['countries'] = $this->commonstObj->getCountries(['country_id' =>$this->userSess->country_id]);
		 $data['genders']= $this->commonObj->genders(); 

        $data['fieldValitator'] = CommonNotifSettings::getHTMLValidation('fr.user.save',['country'=>$this->userSess->country_id]);
		return view('franchisee.user.create_user',$data);
	   }
	 public function save_User(){
		 $data['status'] = 'error';
         $data['msg'] = '';
		 $postdata = $this->request->all();	
		 if($account_info = $this->userObj->save_user($postdata))
	        {
			$op['msg'] = $account_info['msg'];
			$this->statusCode  = $account_info['status'];
		    }
		  return $this->response->json($op, $this->statusCode, $this->headers, $this->options);			 
	 }
	 public function getState()
	 {		
		$country_id = $this->request->has('country_id')? $this->request->country_id:0;
		if($country_id>0){
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$opArray['state'] = $this->locationObj->get_states_list($country_id);
		}
		else {
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$opArray['msg'] = 'Select a Country';
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
	}
		public function getDistrict()
	    {		
		$state_id = $this->request->has('state_id')? $this->request->state_id:0;
		if($state_id>0){
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');			
			$opArray['district'] = $this->locationObj->get_district_list($state_id);
		}
		else {
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$opArray['msg'] = 'Select a State';
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
	  }
	  public function getcity()
	    {		
		$postdata = $this->request->all();
		if($postdata['district_id']>0){
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');			
			$opArray['city'] = $this->locationObj->get_city_list($postdata['state_id'], $postdata['district_id']);
		}
		else {
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$opArray['msg'] = 'Select a City';
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
	  }
	  
	 public function get_cities ()
    {

        $postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $district_id = $postdata['district_id'];
        $op['city_list'] = '';
        $op['city_list'] = $this->locationObj->get_city_list($state_id, $district_id);
        return $this->response->json($op);
    }
	
   public function manage_User() 
	{ 	
		$data = $filter		 = array(); 
		$data['account_id']  = $this->userSess->account_id;
		$post 				 = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
		    $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : null;
            $filter['filterTerms'] = $this->request->has('filterTerms') ? $this->request->get('filterTerms') : null;
			$filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->userObj->UserList($data);		
			$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {	
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal']; 
                $data['start'] = !empty($post['start']) ? $post['start'] : 0;
				$data['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
				{
					$data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
					$data['order'] = $post['order'][0]['dir'];
				}
				unset($data['count']);                    				
				$ajaxdata['data'] = $this->userObj->UserList($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }	
		return view('franchisee.user.manage_user',$data);
	} 
	public function Change_Password(){
		  $data = [];
		  $data['status'] = '';
          $data['msg'] = '';
		  $postdata = $this->request->all();
		   if (!empty($postdata)){
				if($rdata = $this->userObj->update_password($postdata)){
				 $op['msg'] = $rdata['msg'];
                 $this->statusCode = $rdata['status'];
             }
		   }
		 return $this->response->json($op, $this->statusCode, $this->headers, $this->options);    
	}
	public function user_status() {
       $op 	= $postdata = array();
	     $postdata 			= $this->request->all();
         $postdata['status'] 	=$postdata['status'];
	    if($data = $this->userObj->user_status($postdata)){
			$op['msg'] = $data['msg'];
            $this->statusCode = $data['status'];
		}
		else
        {
            $this->statusCode = config('httperr.UN_PROCESSABLE');
            $op['msg'] = 'Something went wrong';
        }
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
	}
	/*  public function edit_detail ($uname) {
         $op = array();
        if ($details = $this->userObj->user_edit($uname)) {
			$op[''] = view('franchisee.user.user_editdetails',$details)->render();
		    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
        }
        else {
            $op['status'] = $this->statusCode = config('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found');
        }
        return $this->response->json($op,$this->statusCode); 
    } */
	
	 public function edit_detail ($uname) {
         $op = array();
		 $details=array();
        if ($data = $this->userObj->user_edit($uname)) {
			$details['user_info']=$data;
			$details['genders']= $this->commonObj->genders(); 
			$details['fieldValitator'] = CommonNotifSettings::getHTMLValidation('fr.user.update_details');
			$op['template'] = view('franchisee.user.user_editdetails',$details)->render();
			$op['details']=$data;
		    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
        }
        else {
            $op['status'] = $this->statusCode = config('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found');
        }
        return $this->response->json($op,$this->statusCode); 
    }
	 public function getAddress($type,$account_id) {
		$data = [];
		$data['address_type'] = $type;
		  if($data['address_type']=='user'){
			$address = $this->userObj->getUserAddr($account_id,$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'), $this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		  }
		   if(!empty($address->country_id)){
			  $data['state_list']=$this->locationObj->get_states_list(isset($address->country_id)?$address->country_id:0);
		  }
		  else{
			   $data['state_list']='';
		  }
		   if(!empty($address->state_id)){
			   $data['district_list']=$this->locationObj->get_district_list(isset($address->state_id)?$address->state_id:0);
		   }
		   else{
			     $data['district_list']='';
		   }
		    if(!empty($address->city_id)){
			    $data['city_list']=$this->locationObj->get_city_list(isset($address->state_id)?$address->state_id:0,isset($address->district_id)?$address->district_id:0);
		   }
		   else{
			   $data['city_list']='';
		   } 
		$data['address']=$address;
		$op['data']=$data;
		$op['template'] = view('franchisee.user.user_address_update',$data)->render();
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
		public function saveAddress($type=''){
		$op = [];
		$postdata = $this->request->all();
	
		$address_type=$type;
		$type = !empty($type)? $type:$this->config->get('constants.ADDRESS_TYPE.PRIMARY');
		if($postdata)
		{		
          if($address_type=='user'){
			 $sdata['relative_post_id'] = $postdata['account_id'];
			 $sdata['post_type'] =$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
		   }
			$sdata['address_type_id'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
			$sdata['country_id'] = $postdata['address']['country_id'];
			$sdata['flatno_street'] = $postdata['address']['flat_no'];
			$sdata['landmark'] = $postdata['address']['landmark'];
			$sdata['district_id'] = $postdata['address']['district_id'];
			$sdata['city'] = $postdata['address']['city_id'];
			$sdata['state'] = $postdata['address']['state_id'];
			$sdata['postal_code'] = $postdata['address']['postal_code'];
			
			$country_info = $this->locationObj->getCountry(['country_id'=>$sdata['country_id'],'allow_signup'=>true]);
			$cityInfo = $this->locationObj->get_city_list(0,0,$postdata['address']['city_id']);			
			$stateInfo = $this->locationObj->getState(0,$postdata['address']['state_id']);
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
			$op = $this->userObj->updateAddress($sdata);	
			$this->statusCode = $op['status'];
			$op['addtype'] = $type;
			$op['user_address']=$formated_address;
			return \Response::json($op, $this->statusCode, $this->headers, $this->options);
			
		}
	}
	
	public function update_details(){
	    $sdata = $postdata = [];
        $postdata = $this->request->all();		
		if(!empty($postdata)) {
			 $result = $this->userObj->update_user_profile($postdata);    
              if (!empty($result))
              {  
		        $op['msg'] = 'User updated Successfully';
	            $op['status'] = $this->config->get('httperr.SUCCESS');
             }else{
                  $op['msg'] = 'Something went wrong.';
	             $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
            }
		}
       return $this->response->json($op, $op['status'], $this->headers, $this->options);
	}
}