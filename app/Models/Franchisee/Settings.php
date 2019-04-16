<?php

namespace App\Models\Franchisee;
use App\Models\BaseModel;

use DB;
use CommonLib;
class Settings extends BaseModel
{
    /* Email Update */
	
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
				//->value('payment_settings');
				->select('payment_settings','is_verified')
				->first('payment_settings');
				
		if($res->payment_settings)
		{
			$payment_settings = json_decode($res->payment_settings);
			$payment_settings->is_verified	= $res->is_verified;
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
			$ps['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.BANK');
			$ps['account_id'] = $account_id;			
			$ps['currency_id'] = $currency_id;
			$ps['updated_by'] = $account_id;
			$ps['withdrawal_status'] = $this->config->get('constants.ON');
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
	    $kyc_status = DB::table($this->config->get('tables.FRANCHISEE_MST'))
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
		       if(!empty($kyc['path'])){
				   $kyc_path=$kyc['path'];
			   }
			   else{
				   $kyc_path='';
			   }
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
			$total_doc = array($this->config->get('constants.KYC_DOCUMENT_TYPE.PAN'),$this->config->get('constants.KYC_DOCUMENT_TYPE.CHEQUE'));
			$kyc_status = [					
				'total_doc'=>count($total_doc),
				'submitted_doc'=>$submitted_doc,
				'verified_doc'=>(!empty($result)) ? $result->verified_doc : 0,
				'submitted_date'=>$kyc_submitted_on,
			];				
			$kyc_status = addslashes(json_encode($kyc_status));   
			
			DB::table($this->config->get('tables.FRANCHISEE_MST'))
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
	
	public function update_mobile_verification ($account_id)
    {   
        if($account_id>0){
			$res = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
							->where('account_id', $account_id)
							->update(['is_mobile_verified'=>$this->config->get('constants.ON')]);
			if($res) {
				$this->userSess->is_mobile_verified = $this->config->get('constants.ON');
				$this->session->set($this->sessionName, $this->userSess);
				return true;
			}
		}		
		return false;		
    }
	/* public function check_account_verification_counts ($arr = array())
    { 
        $datalist_qry = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'uv.document_type_id')
                ->where('uv.account_id', $arr['account_id'])           
                ->whereIn('uv.status_id', array(0,1))
                ->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->whereIn('dt.proof_type', $arr['prooftypes'])
                ->select(DB::Raw('distinct(proof_type) as proof_type_id,count(distinct(proof_type)) as cnt,path,is_verified'))
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
        }		
        $data = array();		
        if (!empty($datalist))
        {
            if (is_array($datalist) && count($datalist) > 0)
            {
                foreach ($datalist as $item)
                {
                    $data[$item->proof_type_id]['cnt'] = $item->cnt;                   
					$data[$item->proof_type_id]['path'] = asset($this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.WEB').$item->path); 
					$data[$item->proof_type_id]['is_verified'] = $item->is_verified;
                }
            }
            else
            {  
                $data[$datalist->proof_type_id]['cnt'] = $datalist->cnt;
            }
			echo"<pre>";print_r($data);exit;
            return $data;
        }
        return false;
    } */
}
