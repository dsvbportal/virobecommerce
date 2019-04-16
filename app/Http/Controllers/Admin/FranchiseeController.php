<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Admin\AdminCommon;
use App\Models\Admin\Franchisee;
use App\Models\LocationModel;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use Validator;

class FranchiseeController extends AdminController
{

    public function __construct ()
    {
        parent::__construct();        
        $this->admincommonObj = new AdminCommon();
        $this->frObj = new Franchisee();        
		$this->locationObj = new LocationModel();
		//print_r($this->request->all());die;
    }

    public function check_email ()
    {
        $old_email = '';
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $email = $postdata['email'];
            if (isset($postdata['old_email']))
                $old_email = $postdata['old_email'];
            $email_status = $this->frObj->franchisee_email_check($email, $user_details = 0, $old_email);
            $op = $email_status;
            return $this->response->json($op);
        }
    }

    public function check_mobile ()
    {
        $old_mobile = '';
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $mobile = $postdata['mobile'];
            if (isset($postdata['old_mobile']))
                $old_mobile = $postdata['old_mobile'];
            $mobile_status = $this->frObj->franchisee_mobile_check($mobile, $old_mobile);
            $op = $mobile_status;
            return $this->response->json($op);
        }
    }

    public function check_username ()
    {
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $uname = $postdata['uname'];
            $res = $this->frObj->franchisee_check_username($uname);            		
            return $this->response->json($res, $res['status'], $this->headers, $this->options);
        }
    }

    public function create_franchise ()
    {
        $data['franchisee_types'] = $this->frObj->getFranchiseeTypes();
        $data['country'] = $this->locationObj->getCountries();
        return view('admin.franchisee.create_franchisee', $data);
    }
	
	public function packages ()
    {
		$wdata = [];
        $wdata = $this->request->all();
        $res = $this->frObj->get_franchisee_package($wdata);	
		
		return $this->response->json($res, $res['status'], $this->headers, $this->options);
    }

    public function save_franchisee ()
    {
        $postdata = $this->request->all();	
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $op['msg'] = 'Somethig Went Wrong';
		
        $scope = array(
            '1'=>'.country',
            '2'=>'.country, .region',
            '3'=>'.country, .region, .state',
            '4'=>'.country, .region, .state, .district',
            '5'=>'.country, .region, .state, .district, .city');
        $type = array(
            '1'=>'Master',
            '2'=>'Region',
            '3'=>'State',
            '4'=>'District',
            '5'=>'City');	

        if (!empty($postdata))
        {
			$add_user = $this->frObj->save_franchisee($postdata);
            if (!empty($add_user))
            {
                $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $postdata['uname']." - Channel Partner created successfully. To set ".$postdata['uname']." access locations <a href='".route('admin.franchisee.access.edit', ['uname'=>$postdata['uname']])."'>Click here</a>.";
                $op['access_type'] = $scope[$postdata['fran_type']];
                $op['franchisee_type'] = $postdata['fran_type'];
                $op['type_name'] = $type[$postdata['fran_type']];               
                $op['country'] = $postdata['country'];
            }
        }
        return $this->response->json($op, $op['status'], $this->headers, $this->options);
    }

    public function add_franchisee_access ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'error';
        $op['msg'] = 'Somethig Went Wrong';
        if ($this->request->ajax())
        {
            if (!empty($postdata))
            {
                $add_franchi_access = $this->frObj->franchisee_access_location($postdata);
                if (!empty($add_franchi_access))
                {
                    $op['status'] = 'ok';
                    $op['msg'] = 'Virob Channel Partner access added successfully';
                }
            }
        }
        else
        {
            return App::abort('404');
        }
        return $this->response->json($op);
    }

	public  function view_franchisee(){	
		$data = $filter = array(); 
		$postdata = $this->request->all();
        if (!empty($postdata))   //not empty value
        {	   
			$filter['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : '';
			$filter['franchisee_type'] = isset($postdata['franchisee_type']) ? $postdata['franchisee_type'] : '';
			$filter['from'] = isset($postdata['from']) ? $postdata['from'] : '';
			$filter['to'] = isset($postdata['to']) ? $postdata['to'] : '';
			$filter['filterchk'] = isset($postdata['filterchk']) ? $postdata['filterchk'] : [];
        }
        if(\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  			
            $ajaxdata['recordsTotal'] = $this->frObj->get_franchisee_list($data);			
			$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {	
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal']; 
                $data['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
				$data['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
				if (isset($postdata['order']))
				{
					$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
					$data['order'] = $postdata['order'][0]['dir'];
				}
				unset($data['count']);                    				
				$franchisee = $this->frObj->get_franchisee_list($data);
				
				if(!empty($franchisee)){
					array_walk($franchisee, function(&$fr) {						
						$fr->signedup_on = showUTZ('d-M-Y H:i:s',$fr->signedup_on);
						/* access_locations */
						if(isset($fr->access_country_name) && !empty($fr->access_country_name)) {
							$fr->access_locations = $fr->access_country_name.')';
						}
						elseif(isset($fr->access_region_name) && !empty($fr->access_region_name)){
							$fr->access_locations = '('.$fr->access_region_name.')';
						}
						elseif(isset($fr->access_state_name) && !empty($fr->access_state_name)) {
							$fr->access_locations = '('.$fr->access_state_name.')';
						}
						elseif(isset($fr->access_district_name) && !empty($fr->access_district_name)) {
							$fr->access_locations =  '('.$fr->access_district_name.')';
						}
						elseif(isset($fr->access_city_name) && !empty($fr->access_city_name)) {
							$fr->access_locations = '('.$fr->access_city_name.')';
						}
						
						if(isset($fr->district_frname) && empty($fr->district_frname) ){
							$fr->district_frname  = '-';
						}
						
						if(isset($fr->state_frname) && !empty($fr->state_frname) ){
							$fr->state_frname = $fr->state_frname;
						}
						elseif(isset($fr->state_frname1) && !empty($fr->state_frname1) ) {
							$fr->state_frname = $fr->state_frname1;
						}
						else {
							$fr->state_frname =  '-';
						}
						
						if(isset($fr->region_frname) && !empty($fr->region_frname) ) {
							$fr->region_frname = $fr->region_frname;
						}
						elseif(isset($fr->region_frname1) && !empty($fr->region_frname1) ) {
							$fr->region_frname = $fr->region_frname1;
						}
						elseif(isset($fr->region_frname2) && !empty($fr->region_frname2) ) {
							$fr->region_frname = $fr->region_frname2;
						}
						else {
							$fr->region_frname = '-';
						}
						
						if(isset($fr->country_frname) && !empty($fr->country_frname) ) {
							$fr->country_frname = $fr->country_frname;
						}
						elseif(isset($fr->country_frname1) && !empty($fr->country_frname1)) {
							$fr->country_frname = $fr->country_frname1;
						}
						elseif(isset($fr->country_frname3) && !empty($fr->country_frname2)) {
							$fr->country_frname = $fr->country_frname2;
						}
						elseif(isset($fr->country_frname3) && !empty($fr->country_frname3)) {
							$fr->country_frname = $fr->country_frname3;
						}
						else {
							$fr->country_frname = '-';
						}
						
						if ($fr->block == 0){
							if ($fr->login_block == 1){
								$fr->status = 'Login Blocked';
								$fr->statusDispClass = 'danger';
							}
							else {
								if ($fr->user_status == 1) {
									$fr->status = 'Active';
									$fr->statusDispClass = 'success';
								}
								else if ($fr->user_status == 0) {
									$fr->status = 'Inactive';
									$fr->statusDispClass = 'warning';
								}
							}
						}
						else {
							$fr->status = 'Blocked';
							$fr->statusDispClass = 'danger';
						}						
			/* action buttons */
						$fr->actions = [];   
						
						$fr->actions[] = ['url'=>route('admin.franchisee.edit',['uname'=>$fr->uname]),'redirect'=>true, 'class'=>'editInfo', 'label'=>'Edit'];
						
						$fr->actions[] = ['url'=>route('admin.franchisee.reset-pwd',['account_id'=>$fr->account_id]), 'class'=>'change_password', 'data'=>[
							'code'=>$fr->account_id,
							'uname'=>$fr->uname,
							'full_name'=>$fr->full_name."($fr->user_code)"
						], 'redirect'=>false, 'label'=>trans('admin/franchisee.change_pwd')];
						
						$fr->actions[] = ['url'=>route('admin.franchisee.reset-pin', ['account_id'=>$fr->account_id]), 'class'=>'change_pin', 'data'=>[
							'account_id'=>$fr->account_id,
							'full_name'=>$fr->full_name."($fr->user_code)",
							'uname'=>$fr->uname
						], 'redirect'=>false, 'label'=>trans('admin/franchisee.reset_pin')];
						
						if ($fr->block == config('constants.OFF')) {							 
							$fr->actions[] = ['url'=>route('admin.franchisee.block'),'class'=> 'block_status', 'data'=>[
								'account_id'=>$fr->account_id,
								'status'=>'1'
							],'label'=>trans('admin/franchisee.block')];
						}
						else {
							$fr->actions[] = ['url'=>route('admin.franchisee.block'), 'class'=>'block_status', 'data'=>[
								'account_id'=>$fr->account_id,
								'status'=>'0'
							],'label'=>trans('admin/franchisee.un_block')];
						} 		
						$fr->actions[] = ['url'=>route('admin.franchisee.change-email'), 'class'=> 'edit_email',
						  'data'=>['uname'=>$fr->uname,'email'=>$fr->email,'account_id'=>$fr->account_id],'redirect'=>false, 'label'=>trans('admin/franchisee.change_email')];	
						  
						$fr->actions[] = ['url'=>route('admin.franchisee.change-mobile', ['account_id'=>$fr->account_id]), 'class'=> 'edit_mobile',
						  'data'=>['uname'=>$fr->uname,'mobile'=>$fr->mobile,'account_id'=>$fr->account_id],'redirect'=>false, 'label'=>trans('admin/franchisee.change_mobile')];
						$fr->actions[] = ['url'=>route('admin.franchisee.access.edit', ['uname'=>$fr->uname]), 
						  'data'=>['uname'=>$fr->uname,'mobile'=>$fr->mobile,'account_id'=>$fr->account_id],'redirect'=>true, 'label'=>'Edit Access'];
					});
					$ajaxdata['data'] = $franchisee;
				}
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }		
        $data['franchisee_types'] = $this->frObj->getFranchiseeTypes();
		return view('admin.franchisee.franchisee_list', $data);
	}
	
    public function view_franchisee_old ()
    {
        $data = [];
        $postdata = $this->request->all();
        $submit = isset($postdata['submit']) ? $postdata['submit'] : '';
        $data['search_feilds'] = ['username'=>'um.uname', 'fullname'=>'concat(ud.first_name," ",ud.last_name)', 'mobile'=>'concat(ud.phonecode," ",ud.mobile)', 'email'=>'um.email'];
        $data['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : '';
        $data['search_feild'] = isset($postdata['search_feild']) ? $postdata['search_feild'] : '';
        $data['franchisee_type'] = isset($postdata['franchisee_type']) ? $postdata['franchisee_type'] : '';
        $data['from'] = isset($postdata['from']) ? $postdata['from'] : '';
        $data['to'] = isset($postdata['to']) ? $postdata['to'] : '';
        $data['franchisee_list'] = $this->frObj->get_franchisee_list($data);
        if ($submit == "Export")
        {
            $output = view('admin.franchisee.franchisee_list_excel', $data);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=UserProfileReport_'.date("Y-m-d").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->json($output, 200, $headers);
        }
        else if ($submit == "Print")
        {
            return view('admin.franchisee.franchisee_list_print', $data);
        }
        $data['franchisee_types'] = $this->frObj->getFranchiseeTypes();
        return view('admin.franchisee.franchisee_list', $data);
    }
	
	public function franchisee_edit_profile ($uname)
    {  
	    $address ='';
        $data = array();      
        if (!empty($uname))
        {
            $uname = $uname;		
            $user_details = $this->frObj->get_franchisee_list([],$uname);		

            if (!empty($user_details))
            {
                $op['status'] = "ok";
                $op['msg'] = "";				
                $op['account_id'] =  $data['account_id'] = $user_details->account_id;
                $op['uname'] = $user_details->uname;
                $op['franchisee_typename'] = $user_details->franchisee_type_name;
                $op['email'] = $user_details->email;             
				
				$getaddr['account_id'] = $user_details->account_id;
				$getaddr['post_type'] = $this->config->get('constants.ADDRESS_POSTTYPE.ACCOUNT');
				$getaddr['address_type'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
				$address = $this->frObj->getAddress($getaddr);	

				$frgetaddr['account_id'] = $user_details->franchisee_acc_id;
				$frgetaddr['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE');
				$frgetaddr['address_type'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');
			    $franchisee_address= $this->frObj->getAddress($frgetaddr);
				
				if (!empty($address))
                {     
			        $user_details->address = $address->address;
					$user_details->flatno_street = $address->flatno_street;
					$user_details->landmark = $address->landmark;
					$user_details->city_id = $address->city_id;
					$user_details->district_id = $address->district_id;
					$user_details->state_id = $address->state_id;
					$user_details->postal_code = $address->postal_code;	
				}	
				if(!empty($franchisee_address))
				{
					$user_details->fr_address = $franchisee_address->address;
					$user_details->fr_flatno_street = $franchisee_address->flatno_street;
					$user_details->fr_landmark = $franchisee_address->landmark;
					$user_details->fr_city_id = $franchisee_address->city_id;
					$user_details->fr_state_id = $franchisee_address->state_id;
					$user_details->fr_district_id = $franchisee_address->district_id;
					$user_details->fr_postal_code = $franchisee_address->postal_code;	
			    }
					/* return view('admin.franchisee.franchisee_edit_profile', $data); */
				 $data['user_details'] = $user_details;
				 $data['msg'] = '';
			     $data['status'] = $this->config->get('httperr.SUCCESS');
            }
            else
            {                
                $data['msg'] = "Virob Channel Partner not Available";
				$data['status'] = $this->config->get('httperr.UN_PROCESSABLE');
            }            
          }
			else
			{           	
				$data['msg'] = "Something went wrong!";
				$data['status'] = $this->config->get('httperr.UN_PROCESSABLE');
			}	
		return view('admin.franchisee.franchisee_edit_profile', $data);
    }
	
	
	public function getAddress($type='personal') {
       $postdata = $this->request->all();	
	
		$data = [];$add_type=[];
	      $add_type['address_type'] = $type; 
		if($add_type['address_type']=='personal'){
			 $address = $this->frObj->getUserAddr($postdata['account_id'],$this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
		  }
		   if($add_type['address_type']=='franchisee'){
			$address = $this->frObj->getUserAddr($postdata['account_id'],$this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE'),$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
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
			  $op = $data;
			  $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');		
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function update_franchisee_profile ()
    {	  
        $sdata = $postdata = [];
		$op['msg'] = 'Something went wrong.';
		$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();			
        if(!empty($postdata))
        {     
	        if(!empty($postdata['editaddr']))
			{
			$sdata['country_id'] = $postdata['country'];
			$sdata['flatno_street'] = $postdata['address']['flatno_street'];
			$sdata['landmark'] = $postdata['address']['landmark'];
			$sdata['state'] = $postdata['address']['state_id'];
			$sdata['district_id'] = $postdata['address']['district_id'];
			$sdata['city'] = $postdata['address']['city_id'];
			$sdata['postal_code'] = $postdata['address']['postal_code']; 

			 $country_info = $this->locationObj->getCountry(['country_id'=>$postdata['country'],'allow_signup'=>true]);
			 $cityInfo = $this->locationObj->get_city_list(0,0,$postdata['address']['city_id']);			
			 $stateInfo = $this->locationObj->getState(0,$postdata['address']['state_id']);
			 
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
			     $sdata['address'] = !empty($formated_address)? implode(',',$formated_address) :'';
				 $postdata['personal_address']=$sdata;
	      }
		 
   if(!empty($postdata['office_available'])){	
	    
		if(!empty($postdata['edit_fr_addr'])){

	        $fdata['country_id'] = $postdata['country'];
			$fdata['flatno_street'] = $postdata['fr_address']['company_address'];
			$fdata['landmark'] = $postdata['fr_address']['landmark'];
			$fdata['state'] = $postdata['fr_address']['fr_state_id'];
			$fdata['district_id'] = $postdata['fr_address']['fr_district_id'];
			$fdata['city'] = $postdata['fr_address']['fr_city_id'];
			$fdata['postal_code'] = $postdata['fr_address']['franchisee_zipcode'];
			
			 $country_info = $this->locationObj->getCountry(['country_id'=>$fdata['country_id'],'allow_signup'=>true]);
			 $cityInfo = $this->locationObj->get_city_list(0,0,$postdata['fr_address']['fr_city_id']);	
			 $stateInfo = $this->locationObj->getState(0,$postdata['fr_address']['fr_state_id']);
			 
			   $fr_formated_address = [];
				if(!empty($fdata['flatno_street'])) {
				    $fr_formated_address[] = $val['flatno_street'] = $fdata['flatno_street'];
			      }
			   if(!empty($fdata['landmark'])) {
				   $fr_formated_address[] = $val['landmark'] = $fdata['landmark'];
			     }	
			   if(!empty($cityInfo)) {
				    $fdata['district'] = $cityInfo->district_id;
				    $fr_formated_address[] = $val['city'] = $cityInfo->city;		
		   
					if(empty($stateInfo) && !empty($fdata['postal_code'])) {
						$fr_formated_address[] = $cityInfo->district.'-'.$fdata['postal_code'];
					} 
					else {
						$fr_formated_address[] = $val['district'] = $cityInfo->district;
					}
		        }
		       if(!empty($stateInfo)) {
				   
				if(!empty($fdata['postal_code'])) {
					
					$fr_formated_address[] = $stateInfo->state.'-'.$fdata['postal_code'];
				}
				else {
					$fr_formated_address[] = $val['state'] = $stateInfo->state;
				}
			 }
			if(!empty($country_info)) {
				$fr_formated_address[] = $val['country'] =  $country_info->country_name;
			 }
			     $fdata['address'] = !empty($fr_formated_address)? implode(',',$fr_formated_address) :'';
				 $postdata['franchisee_address']=$fdata;	
		   }
      }		   
            $result = $this->frObj->update_franchisee_profile($postdata);    
		
            if (!empty($result))
            {  
		        $op['msg'] = 'Profile updated Successfully';
	            $op['status'] = $this->config->get('httperr.SUCCESS');
	            $op['address']['personal'] = isset($postdata['personal_address']) ? $postdata['personal_address'] :'';
				$op['address']['franchisee'] = isset($postdata['franchisee_address']) ? $postdata['franchisee_address'] :'';
	            
            }
        }
		return $this->response->json($op, $op['status'], $this->headers, $this->options);
    }

    public function edit_franchisee_access ()
    {
        return view('admin.franchisee.edit_franchisee_access');
    }

    public function check_franchisee ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'error';
        $op['msg'] = 'Something Went Wrong';
        if (!empty(!empty($postdata)))
        {
            $uname = $postdata['uname'];
            $data['access_city'] = '';
            $data['access_state'] = '';
            $data['access_district'] = '';
            $data['access_country'] = '';
            $data['account_id'] = '';
            $data['access_region'] = '';
            $data['type'] = '';
            $data['uname'] = '';
            $data['email'] = '';
            $data['franchisee_typename'] = '';
			$data['is_new'] = false;
            $access_country = $access_region = $access_state = $access_district = $access_city = '';
            $scope = array(
                '0'=>'.country',
                '1'=>'.country',
                '2'=>'.country, .region',
                '3'=>'.country, .state',
                '4'=>'.country, .state, .district',
                '5'=>'.country, .state, .district, .city');
			$access_detail = '';
            $user_details = $this->frObj->get_franchisee_list($arr = array(), $uname);
            if (!empty($user_details))
            {			
                $data['franchisee_types'] = $this->frObj->getFranchiseeTypes();
                $data['country'] = $this->locationObj->getCountries();			
                $user_access = $this->frObj->get_frachisee_access_list($user_details->account_id);				
                if (isset($user_access->access_type) && !empty($user_access->access_type))
                {
                    $type = $user_access->access_type;
                    $location_access = $user_access->location_access;
                }
                else
                {
                    $type = $user_details->franchisee_type;
                    $location_access = 0;
					$data['is_new'] = true;
                }

                if ($type == $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'))
                {
                    //$access_detail = $this->frObj->get_country_access($location_access);
                    $data['access_country'] = $access_country = $location_access;
                }
                else if ($type == $this->config->get('constants.FRANCHISEE_TYPE.REGION'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->frObj->get_region_access($location_access);						
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_region'] = $access_region = $location_access;
					if (!empty($access_detail))
					{
					$data['access_country'] = $access_country = ($access_detail->country_id)? $access_detail->country_id:0; //$access_detail->country_id;
					}

                }
                else if ($type == $this->config->get('constants.FRANCHISEE_TYPE.STATE'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->frObj->get_state_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_state'] = $access_state = $location_access;
                    $data['access_region'] = $access_region = ($access_detail)? $access_detail->region_id:0;
                    $data['access_country'] = $access_country = ($access_detail)? $access_detail->country_id:0;
                }
                else if ($type == $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->frObj->get_district_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_state'] = $access_state = ( $access_detail)?  $access_detail->state_id:0;
                    $data['access_district'] = $access_district = $location_access;
                    $data['access_country'] = $access_country =( $access_detail)? $access_detail->country_id:0;
                    $data['access_region'] = $access_region = ($access_detail)? $access_detail->region_id:0;
                }
                else if ($type == $this->config->get('constants.FRANCHISEE_TYPE.CITY'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->frObj->get_city_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_city'] = $access_city = $location_access;
                    $data['access_country'] = $access_country = ($access_detail)? $access_detail->country_id:0;
					$data['access_region'] = $access_region = ($access_detail)? $access_detail->region_id:0;
					$data['access_state'] = $access_state = ($access_detail)? $access_detail->state_id:0;
                    $data['access_district'] = $access_district = ($access_detail)? $access_detail->district_id:0;                    
                    
                }
                $data['regions'] = $this->locationObj->get_region_list($access_country);
                $data['states'] = $this->locationObj->get_states_list($access_country);
                $data['districts'] = $this->locationObj->get_district_list($access_state);
                $data['citys'] = $this->locationObj->get_topcity_list($access_state, $access_district);

                $data['type'] = $type;
                $data['account_id'] = $user_details->account_id;                
                $op['email'] = $user_details->email;
                $data['uname'] = $user_details->uname;
                $data['franchisee_typename'] = $user_details->franchisee_type_name;
				$data['company_name'] = $user_details->company_name;
				$data['user_code'] = $user_details->user_code;
                $data['email'] = $user_details->email;
                $data['merchant_fee'] = $user_details->merchant_signup_fee;
                $data['profit_sharing'] = $user_details->profit_sharing;
                $data['profit_sharing_without_district'] = $user_details->profit_sharing_without_district;
                $data['deposite_amount'] = $user_details->deposite_amount;
                $data['franchisee_details'] = $user_details;
				$this->session->set('freditacc',$user_details);
				
                $a = view('admin.franchisee.update_access', $data)->render();
                $op['content'] = $a;
                $op['status'] = "ok";
                $op['msg'] = "";
                $op['scope'] = $scope[$type];
				$this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = "not_avail";
                $op['msg'] = "Channel Partner not Available";
				$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
		//print_r($data);die;
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }

    public function update_newaccess ()
    {
        $postdata = $this->request->all();
        $op['status'] = "error";
        $op['msg'] = "Something Went Wrong..!";

        if ($this->request->ajax())
        {
			
            if (!empty($postdata))
            {
                $status = $this->frObj->add_access_location($postdata);				
                if (!empty($status))
                {
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                    $op['msg'] = "Access updated Successfully";
                    //$op['account_id'] = $postdata['account_id'];
                }
				else {
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    $op['msg'] = "Please try again later";
				}
            }
        }
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }
	
	
	public function update_franchisee_accessinfo ()
    {
        $postdata = $this->request->all();
        $op['status'] = "error";
        $op['msg'] = "Something Went Wrong..!";
        if ($this->request->ajax())
        {			
            if (!empty($postdata))
            {
                $status = $this->frObj->update_franchisee_accessinfo($postdata);				
                if (!empty($status))
                {
					$frInfo = $this->session->get('freditacc');					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                    $op['msg'] = $frInfo->company_name." - Access updated Successfully";
					$this->session->flash('flmsg', $op['msg']);
                    //$op['account_id'] = $postdata['account_id'];
                }
				else {
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    $op['msg'] = "Please try again later";
				}
            }
        }
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }

    public function get_states ()
    {
        $region_id = '';
        $postdata = $this->request->all();
        $country_id = $postdata['country_id'];
        if (isset($postdata['region_id']))
            $region_id = $postdata['region_id'];

        $op['region_list'] = '';
        $op['region_list'] = $this->locationObj->get_region_list($country_id);
        $op['state_list'] = $op['phone_code_list'] = '';

        $op['state_list'] = $this->locationObj->get_states_list($country_id);
        $op['phone_code_list'] = $this->locationObj->getCountries(['country_id'=>$country_id]);

        return $this->response->json($op);
    }

    public function get_franchisee_state_phonecode ()
    {
        $region_id = '';
        $postdata = $this->request->all();
        $country_id = $postdata['country_id'];
        if (isset($postdata['region_id']))
            $region_id = $postdata['region_id'];

        $op['region_list'] = '';
        $op['region_list'] = $this->locationObj->get_region_list($country_id);
        $op['state_list'] = $op['phone_code_list'] = '';

        $op['state_list'] = $this->locationObj->get_states_list($country_id);
        $op['phone_code_list'] = $this->locationObj->getCountries(['country_id'=>$country_id]);

        return $this->response->json($op);
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

    public function get_districts ()
    {
        $postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $op['district_list'] = '';
        $op['district_list'] = $this->locationObj->get_district_list($state_id);
        return $this->response->json($op);
    }

	public function get_franchisee_city ()
    {
        $postdata = $this->request->all();;
        $state_id = $postdata['state_id'];
        $district_id = $postdata['district_id'];
        $op['city_list'] = '';
        $op['city_list'] = $this->locationObj->get_topcity_list($state_id, $district_id);
        return Response::json($op);
    }

    public function get_franchisee_district ()
    {
        $postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $op['district_list'] = '';
        $op['territory_list'] = '';
        $territory_state_id = '';
        $territory = $this->locationObj->get_territory_list($state_id);
        $op['territory_list'] = $territory;
        if ($territory)
        {
            $territory_state_id = $territory[0]->state_id;
        }
        $op['district_list'] = $this->locationObj->get_district_list($state_id, $territory_state_id);
        return $this->response->json($op);
    }

    public function get_region ()
    {
        $postdata = $this->request->all();
        $country_id = $postdata['country_id'];

        return $this->response->json($op);
    }

    public function check_franchise_access ()
    {		
        $postdata = $this->request->all();
        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $op['msg'] = trans('general.something_wrong');
        extract($postdata);
        if ($franchise_type == 5)
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = '';
        }
        else
        {
            $is_exists_check = $this->frObj->check_franchise_access($franchise_type, $relation_id);
            if (!empty($is_exists_check))
            {				
				if(isset($purpose) && $purpose=='mapping'){
					$op = $this->check_franchise_mapped();
					$this->statusCode = $op['status'];
					$op['purpose'] = $postdata['purpose'];
				}
				else {
					$franchi_name = $is_exists_check[0];
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
					switch($franchise_type){
						case $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'):
							$op['error'] = ['country'=>trans('admin/franchisee.access_exists',['franchisee'=>$franchi_name])];
						break;
						case $this->config->get('constants.FRANCHISEE_TYPE.REGION'):
							$op['error'] = ['region'=>trans('admin/franchisee.access_exists',['franchisee'=>$franchi_name])];
						break;
						case $this->config->get('constants.FRANCHISEE_TYPE.STATE'):
							$op['error'] = ['state'=>trans('admin/franchisee.access_exists',['franchisee'=>$franchi_name])];
						break;
						case $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'):
							$op['error'] = ['district'=>trans('admin/franchisee.access_exists',['franchisee'=>$franchi_name])];
						break;
						case $this->config->get('constants.FRANCHISEE_TYPE.CITY'):
							$op['error'] = ['city'=>trans('admin/franchisee.access_exists',['franchisee'=>$franchi_name])];
						break;						
					}
					
					$op['msgClass'] = 'danger';
				}
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $op['msgClass'] = '';
            }
        }
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }

    public function check_franchise_mapped ()
    {
        $postdata = $this->request->all();
        $country_id = $state_id = $district_id = $franchisee_type = '';
        $op['country_franchisee'] = $op['state_franchisee'] = $op['district_franchisee'] = $op['region_franchisee'] = '';

        if (isset($postdata['franchise_type']))
            $franchisee_type = $postdata['franchise_type'];

        if (isset($postdata['country_id']))
            $country_id = $postdata['country_id'];
        if (isset($postdata['state_id']))
            $state_id = $postdata['state_id'];
        if (isset($postdata['district_id']))
            $district_id = $postdata['district_id'];

        if (isset($franchisee_type) && !empty($franchisee_type))
        {
            if (!empty($country_id) && $franchisee_type > $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'))
                $op['country_franchisee'] = $this->frObj->check_franchise_access($this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'), $country_id);
            if (!empty($state_id))
            {
                if ($franchisee_type > $this->config->get('constants.FRANCHISEE_TYPE.STATE'))
                    $op['state_franchisee'] = $this->frObj->check_franchise_access($this->config->get('constants.FRANCHISEE_TYPE.STATE'), $state_id);
                if ($franchisee_type > $this->config->get('constants.FRANCHISEE_TYPE.REGION'))
                    $op['region_franchisee'] = $this->frObj->check_franchise_region($this->config->get('constants.FRANCHISEE_TYPE.REGION'), $state_id);
            }
            if (!empty($district_id) && $franchisee_type > $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'))
                $op['district_franchisee'] = $this->frObj->check_franchise_access($this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'), $district_id);
        }
        return $op;
    }

    public function change_block_franchisee ($userid = 0)
    {
        $postdata = $this->request->all();
		//print_r($postdata);exit;
        $postdata['status'] = $postdata['status'];
        $postdata['account_id'] = $postdata['id'];
		$this->statusCode = 200;
        $op = $this->frObj->change_block_franchisee($postdata);
		return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 
    }

    /* Block Login */

    public function change_franchisee_loginblock ($userid = 0)
    {
        $response = '';
        $postdata['account_id'] = $userid;
        $postdata['status'] = $this->request->get('status');
        $postdata['block'] = $this->request->get('login_block');
        $params['account_id'] = $userid;
        $checku_Res = $this->admincommonObj->franchisee_user_check('', $params);
        if ($checku_Res && $checku_Res['status'] == 'ok')
        {
            $response = $this->frObj->change_franchisee_loginblock($postdata);
        }
        else
        {
            $response['status'] = 'ERR';
            $response['msg'] = $checku_Res['msg'];
        }
        return $this->response->json($response);
    }

    public function release_old_commission ($commission_type = 0)
    {
        /* echo $commission_type;
          exit; */
        if (!empty($commission_type))
        {
            $country_id = "77";
            $month = "11";
            $year = "2016";
            switch ($commission_type)
            {
                case $this->config->get('constants.FRANCHISEE_COMMISSION_FIXED_CONTRIBUTION'):
                    $subscription_details = $this->frObj->update_fixed_commission($country_id, $month, $year);
                    break;
                case $this->config->get('constants.FRANCHISEE_COMMISSION_ADD_FUNDS'):
                    $subscription_details = $this->frObj->update_addfunds_commission($country_id, $month, $year);
                    break;
                case $this->config->get('constants.FRANCHISEE_COMMISSION_FLEXIBLE_CONTRIBUTION'):
                    $subscription_details = $this->frObj->update_flexible_commission($country_id, $month, $year);
                    break;
            }
        }
    }

    public function get_currency_list ()
    {
        $postdata = $this->request->all();
        $currency_ids = $postdata['currency_ids'];
        $current_currency = isset($postdata['current_currency']) ? $postdata['current_currency'] : '';
        $op['currencylist'] = '';
        $currencies = $this->locationObj->get_currencies_list($currency_ids);
        $currency_list = '';
        if ($currencies)
        {
            foreach ($currencies as $row)
            {
                $currency_list .= "<option value='".$row->id."'";
                if (!empty($current_currency) && $current_currency == $row->id)
                    $currency_list .= "selected = 'selected'";
                $currency_list .= ">".$row->code."</option>";
            }
        }
        $op['currencylist'] = $currency_list;
        return $this->response->json($op);
    }
	
	/*  Kyc Verification */
	public function kycDocVerification ($uname = '')
    { 
        $op = $data =[];   
        $postdata = $this->request->all();	
		$data['uname'] = (isset($uname) && !empty($uname)) ? $uname : '';        
        if (!empty($postdata))
        {
            $data['type_filer'] = isset($postdata['type_filer']) ? $postdata['type_filer'] : '';
            $data['from'] = isset($postdata['from']) ? $postdata['from'] : '';
            $data['to'] = isset($postdata['to']) ? $postdata['to'] : '';
            $data['search_term'] = isset($postdata['search_term']) ? trim($postdata['search_term']) : '';
            $data['status'] = isset($postdata['status']) ? $postdata['status'] : '';
            $data['uname'] = isset($postdata['uname']) ? $postdata['uname'] : '';
            $data['account_id'] = isset($postdata['account_id']) ? $postdata['account_id'] : '';
        }
        if (Request::ajax())
        {
            $data['counts'] = true;			
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->frObj->kycDocumentList($data);
            if(!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] 		= (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] 	= (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : $this->config->get('constants.DATA_TABLE_RECORDS');
                unset($data['counts']);
                $ajaxdata['data'] 	= $this->frObj->kycDocumentList($data);
				//echo"<pre>";print_r($ajaxdata['data'] );exit;
                $ajaxdata['draw'] 	= isset($postdata['draw'])?$postdata['draw']:'';
                $ajaxdata['url']  	= URL::to('/');
            }else{
                $ajaxdata['data'] = array();
            }			
            return Response::json($ajaxdata);
        }
        else
        {
            if (empty($uname))
            {
                $data['status'] = 0;
            }
			$data['doc_types'] = $this->frObj->kycDocTypelist();
			$data['doc_status'] = trans('admin/general.verification_status');		
            return view('admin.franchisee.kyc-verification', $data);
        }
    }
	
	public function changeKycDocStatus ()
    {         
        $op['msg'] = trans('general.something_went_wrong');
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $postdata['admin_id'] = $this->userSess->account_id;
            $data = $this->frObj->changeKycDocStatus($postdata);
            if (!empty($data))
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('general.status_updated_successfully');
            }
        }
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }

	public function updateLoginAccess($account_id,$status) {
        $op = $postdata = array();
        $postdata['account_id'] = $account_id;
        $postdata['status'] =config('constants.ACCOUNT_USER.'.strtoupper($status));
	    return  $data=$this->frObj->user_block_status($postdata);	   
	}

	public function updateding_email(){
	  $data['status'] = '';
      $data['msg'] = '';
	  $postdata = $this->request->all();
	  $rdata = $this->frObj->update_email($postdata);
	  $this->statusCode = $rdata['status'];
      return $this->response->json($rdata,$this->statusCode); 
	}

    public function update_mobile(){
		$data['status'] = '';
		$data['msg'] = '';
		$postdata = $this->request->all();
	    $rdata = $this->frObj->update_mobile($postdata);
		$this->statusCode = $rdata['status'];
        return $this->response->json($rdata,$this->statusCode); 
	}

	public function reset_pwd() {
        $op = [];
		$op['status'] = $this->statusCode = config('httperr.UN_PROCESSABLE');
        $op['msg'] = '';		
        $postdata = $this->request->all();
	    if (!empty($postdata)){
			$op = $this->frObj->update_password($postdata);		
				$this->statusCode = $op['status'];
        }
		return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 
	}

	public function reset_pin(){
	    $op=[];
        $postdata = $this->request->all();
	     if (!empty($postdata)){
	        $rdata = $this->frObj->update_pin($postdata);			
		    $status = $rdata['status'];
           return $this->response->json($rdata,$status,$this->headers,$this->options); 
        }
	}

    public function franchiseeFundTransferCommission ()
    {
		$data = $filter = array();
		$postdata = $this->request->all();
        if(!empty($postdata)){	
		  $filter['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
		  $filter['status'] = (isset($postdata['status']) && !empty($postdata['status'])) ? $postdata['status'] : '';
          $filter['from'] = (isset($postdata['from_date']) && !empty($postdata['from_date'])) ? $postdata['from_date'] : '';
		  $filter['to'] = (isset($postdata['to_date']) && !empty($postdata['to_date'])) ? $postdata['to_date'] : '';
        }	
		if(\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frObj->fundtransfer_commission_details($data);
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
				$ajaxdata['data'] = $this->frObj->fundtransfer_commission_details($data);
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
        else
        {
		   $data['status'] = $this->frObj->get_franchisee_commission_status($data);
	        return view('admin.franchisee.franchisee_commission_details', $data);
        }
    }
	
	public function Merchant_enrolment_fee() 
	{ 	
		$data = $filter		 = array(); 
		$post 				 = $this->request->all();
	    $data['franchisee_type']=$this->frObj->get_franchisee_type();
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
			$filter['fr_type'] = (isset($post['fr_type']) && !empty($post['fr_type'])) ? $post['fr_type'] : '';
			$filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
		    $filter['filterchk']  = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frObj->merchant_enrolment_commission($data);	
			
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
				$ajaxdata['data'] = $this->frObj->merchant_enrolment_commission($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	   /* elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
        {  
            $pdata['print_data'] = $this->frReportObj->earned_commission(array_merge($data, $filter));
            return view('franchisee.reports.earned_commission_print', $pdata);
        }
       elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
            $epdata['export_data'] = $this->frReportObj->earned_commission(array_merge($data, $filter));
            $output = view('franchisee.reports.earned_commission_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Earned Commission Report'.getGTZ('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }	 */
		return view('admin.franchisee.merchant_enrolment_fee',$data);

	}
	
	public function Merchant_enrolment_details($account_id,$created_on_date){

	    $data = $filter		 = array(); 
		$ajaxdata=array();
   	    $data['account_id']  = $account_id; 
		$data['created_on_date']  = $created_on_date;
		$data['franchisee_details']=$this->frObj->franchisee_details($account_id);
		$post 				 = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
		    $filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
		    $filter['filterchk']  = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frObj->Merchant_enrollment_fee_details($data);
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
				 $ajaxdata['data'] = $this->frObj->Merchant_enrollment_fee_details($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	   elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
        {  
            $pdata['print_data'] = $this->frObj->Merchant_enrollment_fee_details(array_merge($data, $filter));
            return view('franchisee.reports.earned_commission_details_print', $pdata);
        }
       elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
            $epdata['export_data'] = $this->frObj->Merchant_enrollment_fee_details(array_merge($data, $filter));
            $output = view('franchisee.reports.earned_commission_details_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Earned Commission Report'.getGTZ('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
         }	
			return view('admin.franchisee.merchant_enrolment_fee_details',$data);
	 }
	 
/*  Profit Sharing */

     public function Profit_sharing(){ 	
		$data = $filter		 = array(); 
		$post 				 = $this->request->all();
	    $data['franchisee_type']=$this->frObj->get_franchisee_type();
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
			$filter['fr_type'] = (isset($post['fr_type']) && !empty($post['fr_type'])) ? $post['fr_type'] : '';
			$filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
		    $filter['filterchk']  = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frObj->get_profit_sharing($data);	
			
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
				$ajaxdata['data'] = $this->frObj->get_profit_sharing($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
		return view('admin.franchisee.franchisee_profit_sharing',$data);
	}
	public function profit_sharing_details($account_id,$created_on_date) {
	    $data = $filter		 = array(); 
		$ajaxdata=array();
   	    $data['account_id']  = $account_id; 
		$data['created_on_date']  = $created_on_date;
     /* $data['franchisee_details']=$this->frObj->franchisee_details($account_id); */
		$post 				 = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
		    $filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
		    $filter['filterchk']  = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frObj->get_profit_sharing_details($data);
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
				 $ajaxdata['data'] = $this->frObj->get_profit_sharing_details($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }	
	   return view('admin.franchisee.franchisee_profit_sharing_details',$data);
	 }
  /* Profit Sharing End */
  
   public function qlogin(){
			  return view('admin.franchisee.qlogin');
		}
	
	public function quick_login(){
		
		$postdata = $this->request->all(); 
		$op = array();
		$messages = [
		  'uname.required' => 'Please enter your Account Id / Email ID', // custom message for required rule.
		  'uname.idOremail' => 'Invalide Account Id / Email ID', // custom message for email rule.
		];
		$rules =  [            
            'uname' => 'required|idOremail',
        ];
		
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {	
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['error'][$key] =  $validator->errors()->first($key);			
				}
			return $this->response->json($op,500);
		}
		
        $op = array();
        $op['status'] = 'fail';
        $op['msg'] = 'Invalid Username and Password';
		$postdata = $this->request->all();        
        $validate = '';
        if(!empty($postdata['uname'])){ 	
		$postdata['q ']		 = true;
            $validate = $this->frObj->account_validate($postdata);			
            if ($validate['status']==1) {                
				$op['status'] = 'ok';				
				$op['msg'] = $validate['msg'];
			    $op['url'] =  route('frdashboard'); 
				return $this->response->json($op,200);                
            } 
			else {                
                $op['status'] = 'error';
                $op['msg'] = $validate['msg'];
            }
        } else {
			$op['status'] = 'fail';
            $op['msg'] = 'Account Id (or) Password should not be empty';
		}
		
		return $this->response->json($op,200);	
	}
	
}