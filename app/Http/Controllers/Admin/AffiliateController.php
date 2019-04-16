<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\AffModel;
use App\Models\LocationModel;
use App\Helpers\CommonNotifSettings;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use Validator;

class AffiliateController extends BaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
		$this->lcObj = new LocationModel();		
    }    

  public function create_root_user ($user_type = ''){
		$data=[];
		$data['countries']= $this->lcObj->getCountries(['allow_signup'=>true]); 
		return view('admin.affiliate.create_user',$data);
    }
  
  public function save_root_user(){
		$user_id = 0;
        $user_role = '1';
        $user_type = 'root';
        $data = array();
        $response['status'] = "error";
        $response['msg'] = "Invalid Details";
        $data['user_details'] = '';
        $postdata=$this->request->all();
       $result = $this->affObj->save_user($postdata);
	   $this->statusCode = $result['status'];
        return $this->response->json($result,$this->statusCode); 
	  }
   public function checkUnameAvaliable ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Invalid Username';
        $postdata =  $this->request->all();
        if (isset($postdata['uname']))
        {
            $result = $this->affObj->check_user($postdata['uname']);
            if (!empty($result))
            {
                $op['status'] = '';
                $op['msg'] = 'Username Already Exist';
            }
            else
            {
                $op['status'] = '';
                $op['msg'] = 'Username Avaliable';
            }
        }
        return $this->response->json($op,$this->statusCode); 
    }
 public function checkEmailAvaliable ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Invalid Email';
        $postdata =  $this->request->all();
        if (isset($postdata['email']))
        {
            $result = $this->affObj->check_email($postdata['email']);
         if (empty($result)){
				 $op['status'] =config('httperr.SUCCESS');
                 $op['msg'] = 'Email Avaliable';
            }
         else {
				 $op['status'] =config('httperr.UN_PROCESSABLE');
                 $op['msg'] = 'Email Already Exist';
            }
        }
        return Response::json($op);
    }
 public function CheckMobileAvailable ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Invalid Mobile';
        $postdata =  $this->request->all();
        if (isset($postdata['mobile']))
        {
			$result = $this->affObj->check_mobile($postdata['mobile']);
         if (!empty($result)){
                $op['status'] =config('httperr.UN_PROCESSABLE');
                $op['msg'] = 'Mobile Already Exist';
            }
         else {
				 $op['status'] = config('httperr.SUCCESS');
                $op['msg'] = 'Mobile Avaliable';
            }
        }
        return Response::json($op);
    }
  public function package_purchase_report(){
	
		$data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
        $data['wallet_list'] = $this->ewalletsObj->get_wallet_list();
		$data['wallet_id'] = $data['transaction_type'] = $data['from_date'] = $data['to_date'] = '';
		
		if (!empty($postdata))  
        {			
            $filter['from_date'] = !empty($postdata['from_date']) ? $postdata['from_date'] : '';
			$filter['to_date'] = !empty($postdata['to_date']) ? $postdata['to_date'] : '';
			$filter['search_term'] = !empty($postdata['search_term']) ? $postdata['search_term'] : '';
			$filter['wallet_id'] = !empty($postdata['ewallet_id']) ? $postdata['ewallet_id'] : '';
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		    $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
        }
		if ($this->request->ajax())         
        {   
			$data['count'] = true;
			$dat = array_merge($data,$filter); 
		    $ajaxdata['recordsTotal'] = $this->adminreportObj->package_purchase($dat);
			$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal'];
		
                $dat['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
				$dat['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
				 if (isset($postdata['order']))
				{
					$dat['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
					$dat['order'] = $postdata['order'][0]['dir'];
				} 
				unset($dat['count']);                    				
				$ajaxdata['data'] = $this->adminreportObj->package_purchase($dat);
			
            }
             return \Response::json($ajaxdata); 
        
		}
	elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
	    {
			$edata['purchase_details'] = $this->adminreportObj->package_purchase(array_merge($data,$filter));	
            $output = view('admin.account.package_purchase_Excel',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=package_purchase_report' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
		{
			$pdata['purchase_details'] = $this->adminreportObj->package_purchase(array_merge($data,$filter));
            return view('admin.account.package_purchase_print',$pdata);
        }
		else
        {
            return view('admin.account.package_purchase_report',$data);  
		}
}
   public function manage_affiliate($user_role = 1){
	  
		$data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
		if (!empty($postdata))  {			
		  $filter['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
		  $filter['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
		  $filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
          $filter['filterchk'] = $this->request->has('filterchk') ? $this->request->get('filterchk') : null;
		  
		  $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
		 
		}
		if (Request::ajax()) {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
			$data['user_role'] = $user_role;
            $dat = array_merge($data, $filter);
	
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->manage_affiliate_details($dat, true); 
			if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filter);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->affObj->manage_affiliate_details($data, true);
                }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filter);
                    $ajaxdata['data'] = $this->affObj->manage_affiliate_details($data);
                }
            }
            return Response::json($ajaxdata);
        }
	   elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$data['user_role'] = $user_role;
			$edata['manage_user_details'] = $this->affObj->manage_affiliate_details(array_merge($data,$filter));	
            $output = View::make('admin.affiliate.manage_affiliate_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=View Affiliate' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$data['user_role'] = $user_role;
			$pdata['manage_user_details'] = $this->affObj->manage_affiliate_details(array_merge($data,$filter));
            return View::make('admin.affiliate.manage_affiliate_print',$pdata);
        } 
		else{
            return View::make('admin.affiliate.manage_affiliate');  
		} 
    }
	
	public function manage_root_affiliates($user_role = 1,$level = 0){
		$data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
		if (!empty($postdata))  {
			
		  $filters['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
		  $filters['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
		  $filters['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
          $filters['filterchk'] = $this->request->has('filterchk') ? $this->request->get('filterchk') : null;
		  $filters['status'] = $this->request->has('status')? $this->request->get('status'):null;	
		  
		  $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
		}
	
	if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filters);  
            $ajaxdata['recordsTotal'] = $this->affObj->manage_root_affiliates($data);		
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
				$ajaxdata['data'] = $this->affObj->manage_root_affiliates($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	   elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
		   	$data['is_affiliate'] = $user_role;
			$data['level'] = $level;
			$edata['manage_user_details'] = $this->affObj->manage_root_affiliates(array_merge($data,$filter));	
            $output = View::make('admin.affiliate.manage_root_affiliates_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=Manage Root Affiliate' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$data['is_affiliate'] = $user_role;
			$data['level'] = $level;
			$pdata['manage_user_details'] = $this->affObj->manage_root_affiliates(array_merge($data,$filter));
            return View::make('admin.affiliate.manage_root_affiliates_print',$pdata);
        } 
		else{
            return view('admin.affiliate.manage_root_affiliates');  
		} 
	}
	
	public function view_details($account_id){
		   $details=array();
		   $op = array();
		   $details['affiliateInfo'] = $this->affObj->getUserinfo($account_id);
	      if($details['affiliateInfo']) {
			$op['data'] = View('admin.affiliate.view_profile_details',$details)->render();
        }
        else{
            $op['status'] = $this->statusCode =config('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found');
        }
        return $this->response->json($op);
	}
    public function change_password(){
         $data = array();
         return view('admin.affiliate.change_pwd',$data);
    } 
	
	public function user_block_status() {
       $op 	= $postdata = array();
	   $postdata 			= $this->request->all();
       $postdata['status'] 	= config('constants.ACCOUNT_USER.'.strtoupper($postdata['status']));
	   return $data = $this->affObj->user_block_status($postdata);
	   /* $op['msg']= $data['msg'];
		$this->statusCode = $data['status'];
        return $this->response->json($op,$this->statusCode);   */
	}
	public function updating_email(){
	  $data['status'] = '';
      $data['msg'] = '';
	  $postdata = $this->request->all();
	  $rdata = $this->affObj->Update_email($postdata);
	  $this->statusCode = $rdata['status'];
      return $this->response->json($rdata,$this->statusCode); 
	}
   public function update_mobile(){
		$data['status'] = '';
		 $data['msg'] = '';
		 $postdata = $this->request->all();
	     $rdata = $this->affObj->Update_mobile($postdata);
		 $this->statusCode = $rdata['status'];
        return $this->response->json($rdata,$this->statusCode); 
	}
   public function updatepwd() {
        $data['status'] = '';
        $data['msg'] = '';
		$op = [];
        $postdata = $this->request->all();
	    if (!empty($postdata)){
	    $rdata = $this->affObj->update_password($postdata);
		$this->statusCode = $rdata['status'];
        return $this->response->json($rdata,$this->statusCode); 
        }
	}
    public function edit_detail ($account_id) {
         $op = array();
        if ($details = $this->affObj->user_edit($account_id)) {
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['edit'] = $details;
        }
        else {
            $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found');
        }
        return $this->response->json($op,$this->statusCode); 
    }
   public function update_details () {
        $postdata = $this->request->all(); 
        if (!empty($postdata)) {
            $res =$this->affObj->user_update($postdata);
            return $res;
        }
        return $this->response->json($op);
    }
	public function reset_security_pin(){
		  $data = array();
       return view('admin.affiliate.change_security_pin',$data);
	}
   public function updatepin(){
	    $op=[];
        $postdata = $this->request->all();
	     if (!empty($postdata)){
	        $rdata = $this->affObj->update_pin($postdata);			
		    $status = $rdata['status'];
           return $this->response->json($rdata,$status,$this->headers,$this->options); 
        }
	}
	public function qlogin(){
       return view('admin.affiliate.qlogin');
	  // $op['template'] = view('affiliate.settings.profile_update',$data)->render();
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
            $validate = $this->affObj->account_validate($postdata);			
            if ($validate['status']==1) {                
				$op['status'] = 'ok';				
				$op['msg'] = $validate['msg'];
				$op['url'] =  route('affdashboard');
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
	
/*
	public function user_verification_list($user_role=1){
        $data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
		if (!empty($postdata))  {			
		  $filters['uname'] = (isset($postdata['uname']) && !empty($postdata['uname'])) ? $postdata['uname'] : '';
		  $filters['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
		  $filters['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
		  $filters['status'] = (isset($postdata['search_status']) && !empty($postdata['search_status'])) ? $postdata['search_status'] : '';
		}
		if (Request::ajax()) {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
			$data['is_affiliate'] = $user_role;
            $dat = array_merge($data, $filters);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->get_user_verification_list($dat, true); 
			
			if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filters);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->affObj->get_user_verification_list($data, true);
                }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->affObj->get_user_verification_list($data);
                }
            }
            return Response::json($ajaxdata);
        }
		else{
            return view('admin.affiliate.user_verification_list');  
		} 
*/
	public function documentVerification ($uname = '')
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
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->documentList($data);
            if(!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] 		= (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] 	= (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                unset($data['counts']);
                $ajaxdata['data'] 	= $this->affObj->documentList($data);
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
			$data['doc_types'] = $this->affObj->doc_list();
			$data['doc_status'] = trans('admin/general.verification_status');		
            return view('admin.affiliate.verification', $data);
        }
    }
	
	public function changeDocumentStatus ()
    {         
        $op['msg'] = trans('general.something_went_wrong');
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $postdata['admin_id'] = $this->userSess->account_id;
            $data = $this->affObj->changeDocumentStatus($postdata);
            if (!empty($data))
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('general.status_updated_successfully');
            }
        }
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }
	
	/* public function delete_doc ()
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $data = $this->affObj->delete_doc($postdata);
            if (!empty($data))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.actions.deleted', ['label'=>Lang::get('general.fields.document')]);
            }
        }
        return Response::json($op);
    } */
		
	/* public function doc_list ()
    {
        $data = $this->affObj->doc_list();
        if (!empty($data))
        {
            return $data;
        }
        else
        {
            return false;
        }
    } */
	
	public function user_verification_list(){
       echo "dfSDFSDF"; die;		

	}
	
	public function get_affiliate_ranks(){
		
		$arr 	  = [];
		$op		  = array();
        $data 	  = array();
        $postdata = $this->request->all();	
        if(!empty($postdata))
        {
            $data['term'] = isset($postdata['term']) ? $postdata['term'] : '';
            $data['country_id']  = isset($postdata['country_id']) ? $postdata['country_id'] : '';
        }
        if(Request::ajax())
        {
            $data['counts'] = true;			
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->get_aff_ranks($data);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
                 unset($data['counts']);
                $ajaxdata['data'] = $this->affObj->get_aff_ranks($data);
		        $ajaxdata['draw'] = $postdata['draw'];
                $ajaxdata['url'] = URL::to('/');
            }
            else
            {
                $ajaxdata['data'] = array();
            }			
            return Response::json($ajaxdata);
        }
        else
        {
			$arr['country_list'] = true;
			$data['countries'] = $this->affObj->get_aff_ranks($arr);		
            return view('admin.affiliate.aff_ranks', $data);
        }
	}
	public function free_affiliate($user_role=1,$can_sponser=0){
		  
		$data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
		if (!empty($postdata))  {			
		 
		  $filters['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
		  $filters['end_date']   = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
		
		  $filters['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : null;
		  $filters['filterchk']  = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	
		  
		  $filter['exportbtn']   = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] 	 = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
		  
		}
		if (Request::ajax()) {


            $ajaxdata['draw']  		= !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url']   		= URL::to('/');
            $ajaxdata['data']  		= array();
			$data['free_is_affiliate']  = $user_role;
			$data['can_sponser']  	=  $can_sponser;
            $dat = array_merge($data, $filters);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->manage_affiliate_details($dat, true); 
			if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filters);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->affObj->manage_affiliate_details($data, true);
               }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->affObj->manage_affiliate_details($data);
                }
            }
            return Response::json($ajaxdata);
        }
	   elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$data['free_is_affiliate'] = $user_role;
			$data['can_sponser']=$can_sponser;
			$edata['manage_user_details'] = $this->affObj->manage_affiliate_details(array_merge($data,$filter));	
			$output = view('admin.affiliate.free_affiliate_export',$edata);
			$headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=free Affiliate' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
			return Response::make($output, 200, $headers);
		}
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$data['free_is_affiliate'] = $user_role;
			$data['can_sponser']=$can_sponser;
			$pdata['manage_user_details'] = $this->affObj->manage_affiliate_details(array_merge($data,$filter));
            return view('admin.affiliate.free_affiliate_print',$pdata);
        } 
		else{
            return view('admin.affiliate.free_affiliate');  
		} 
    }
	
	public function activation_mail(){
	   $data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
		if (!empty($postdata))  {			
		 $filters['uname'] = (isset($postdata['username']) && !empty($postdata['username'])) ? $postdata['username'] : '';
		  $filters['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
		  $filters['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
		/*   $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';  */
		}
		if (Request::ajax()) {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
			
            $dat = array_merge($data, $filters);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->user_details($dat, true); 
			if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filters);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->affObj->user_details($data, true);
                }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->affObj->user_details($data);
                }
            }
            return Response::json($ajaxdata);
        }
	   elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$data['free_is_affiliate'] = $user_role;
			$data['can_sponser']=$can_sponser;
			$edata['manage_user_details'] = $this->affObj->user_details(array_merge($data,$filter));	
            $output = view('admin.affiliate.free_affiliate_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=free Affiliate' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$data['free_is_affiliate'] = $user_role;
			$data['can_sponser']=$can_sponser;
			$pdata['manage_user_details'] = $this->affObj->user_details(array_merge($data,$filter));
            return view('admin.affiliate.free_affiliate_print',$pdata);
        } 
		else{
            return view('admin.affiliate.activation_mail_user');  
		} 
	}
	
	public function activate_mail_user(){
			$account_id = $this->request->all();
			if($account_id>0){
				$data=$this->affObj->get_user_details($account_id); 
				print_r($data);die;
				
				$data['username']='Deepika';
				$data['fullname']='Deepika Dhanasekaran';
				$data['referral_email'] ='gopi@gmail.com';
				$data['referral_name'] = 'Gopi';
				$data['referral_fullname'] = 'Gopi Shankar';
				$data['referral_link'] = url("/".$data['referral_name']);				
				$data['code'] = rand(100000, 999999);
				$data['hash_code'] = md5($data['username'].'-'.$data['code']);
				$token = $this->session->getId().'.'.$data['hash_code'];		
				$data['activate_link'] = url('affiliate/signup/activation',['token'=>$token]);
				/* $this->session->set('newRegSess',array_merge($data,$postdata)); */			
				CommonNotifSettings::affNotify('affiliate.signup.verification', $account_id,0,$data,true,false);	
				 $this->statusCode = $this->config->get('httperr.SUCCESS');
				$opArray['status'] = $this->statusCode;
				$opArray['msg'] = 'Activation Mail Sent Successfully For '.$data["username"].'';	
			}
           	else {
				$opArray['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');;
				$opArray['msg'] = 'Activation Mail Sent Successfully For '.$data["username"].'';	
			}	
			return $this->response->json($opArray, $this->statusCode);  
	}
}
