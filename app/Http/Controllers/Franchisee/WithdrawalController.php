<?php

namespace App\Http\Controllers\Franchisee;

use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\Withdrawal;
use App\Models\Franchisee\WalletModel;
use App\Models\Franchisee\FrModel;
use App\Models\Commonsettings;
//use App\Models\Franchisee\Payments;
use App\Helpers\CommonNotifSettings;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use Validator;

class WithdrawalController extends FrBaseController
{

    private $packageObj = '';
	
    public function __construct ()
    {
        parent::__construct();
        $this->withdrawalObj = new Withdrawal($this->commonObj);
        $this->walletObj = new WalletModel;
        $this->frObj = new FrModel;
        $this->commonObj = new Commonsettings;
    }

	/* Withdrawal Request */
	public function withdrawal_request()
	{  
    	$data['payout_info'] = [];
		$current_balance['balance'] = 0;
		$postdata = $this->request->all();
		$data['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.LOCAL_MONEY');			
		$data['currency_id'] = $this->userSess->currency_id;		
		$data['country_id'] = $this->userSess->country_id;	
		$data['payouts'] = $this->withdrawalObj->withdrawal_payout_list($data); 		
		$data['balance'] = $this->walletObj->my_wallets(['currency_id'=>$this->userSess->currency_id,'account_id'=>$this->account_id,'wallet_id'=>$this->config->get('constants.WALLETS.VI')]);		
		if(!empty($data['balance'])){
			$current_balance['balance'] =  $data['balance']->current_balance;
		}
		$data['current_balance'] = json_encode($current_balance);
		$data['withdraw_fields'] = CommonNotifSettings::getHTMLValidation('fr.withdrawal.save_withdrawal');
		$data['support_emailid'] = $this->siteConfig->support_emailid;	
		return view('franchisee.withdrawal.withdraw_request',$data);
	}	
	
	/*  Payout WIthdrawl Settings */
	public function payout_withdrawal_settings(){
		$op = [];		
	    $postdata 				  = $this->request->all();
	    $data['payment_type'] 	      = $postdata['payment_type'];
	    $data['currency_id']		  = $this->userSess->currency_id;		
	    $data['country_id'] 		  = $this->userSess->country_id;
		$payment_typeInfo = $this->withdrawalObj->payoutTypeDetails($postdata['payment_type']);
		
		if(!empty($payment_typeInfo)){
			$currencies = $this->withdrawalObj->withdrawal_permission($payment_typeInfo,$this->userSess->country_id,$this->userSess->currency_id);
			if(!empty($currencies)){
				$data['payment_type_id'] = $payment_typeInfo->payment_type_id;			
				$op['settings'] = $this->withdrawalObj->get_withdrwal_settings($data);			
				if(!empty($op['settings'])){	
					$balInfo = $this->walletObj->my_wallets(['currency_id'=>$this->userSess->currency_id,'account_id'=>$this->account_id,'wallet_id'=>$this->config->get('constants.WALLETS.VI')]);	
					if(!empty($balInfo)){
						$op['current_balance'] = $balInfo->current_balance;
					} else {
						$op['current_balance'] = 0;
					}		
					if($op['current_balance']>0 && $op['settings']->min_amount<$op['current_balance']){
						if($payment_typeInfo->payment_type_id!=$this->config->get('constants.PAYMENT_TYPES.VI_MONEY')){
							$payout_acinfo = $this->withdrawalObj->account_payout(['account_id'=>$this->userSess->account_id,'payment_type_id'=>$payment_typeInfo->payment_type_id]);
							if(!empty($payout_acinfo)){
								$op['payout_acinfo'] = $payout_acinfo->settings;					
								$op['payment_id'] = $payout_acinfo->payment_id;
								$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							}
							else {
							    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            					$op['msg'] =  trans('franchisee/withdrawal/payout_settings.missing',['redirect_link'=>route('fr.profile.bank-info')]);
            					$op['msgClass'] = 'warning';
							}
						}
						else if($payment_typeInfo->payment_type_id==$this->config->get('constants.PAYMENT_TYPES.VI_MONEY')){
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						}						
						
					} else {
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] =  trans('franchisee/withdrawal/payout_settings.insufficient_bal',['amount'=>\CommonLib::currency_format($op['settings']->min_amount, ['currency_code'=>$op['settings']->currency_code, 'decimal_places'=>$op['settings']->decimal_places, 'currency_symbol'=>$op['settings']->currency_symbol])]);
						$op['msgClass'] = 'warning';
					}
				} else {
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] =  trans('franchisee/withdrawal/payout_settings.missing',['redirect_link'=>route('fr.profile.bank-info')]);
					$op['msgClass'] = 'warning';
				}
			} else {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] =  trans('franchisee/withdrawal/payout_settings.not_available');
				$op['msgClass'] = 'warning';
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	/* Save Withdrawal */
	public function save_withdrawal(){
		$op 				 = [];
		$op['status'] 		 = '';	
      	$data 				 = $this->request->all();
			if(isset($this->request->security_pin))
            {
				if(empty($this->userSess->has_pin))
				{					
					$data = [];
					$data['security_pin'] = $this->request->security_pin;
					$data['account_id'] = $this->userSess->account_id;
					
					if ($res = $this->frObj->saveProfilePIN($data))
					{						
						$this->userSess->has_pin = true;
						$this->session->set($this->sessionName, (array) $this->userSess);						
					}
				}	
				if($this->userSess->has_pin)
				{					
			         $check_profilepin=$this->frObj->profilepin_check($this->userSess->account_id);			
					if ($check_profilepin->trans_pass_key == md5($this->request->security_pin))
					 {	
						$data['currency_id']	 = $this->currency_id;
						$data['country_id']	 = $this->country_id;
						$data['account_id']	 = $this->account_id;
						$data['payout_id']	 	 = $this->account_id;						
						if(($data['payment_type_id'] == $this->config->get('constants.PAYMENT_TYPES.BANK')) || ($data['payment_type_id'] == $this->config->get('constants.PAYMENT_TYPES.VI_MONEY'))){
							 $result = $this->withdrawalObj->saveWithdrawal($data);						
						 
							if (!empty($result) && ($result!=2) && ($result!=3))
							{
								$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
								$postdata = [];
								$postdata['id'] = $result;
								$postdata['account_id'] = $this->userSess->account_id;
								$op['success'] = 'SUCCESS!';
								$op['msg']	   = 'Withdrawal request submit successfully';
							}
							elseif($result==2)
							{
								$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
								$op['msg'] = 'Enter Valid withdrawal amount';
							} 
							elseif($result==2){
								$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
								$op['msg'] = 'Insufficiant balance to withdraw';
							}
							else
							{
								$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
								$op['msg'] = trans('general.something_wrong');
							}
						}
					}
					else
					{
						$op['msg'] = trans('user/account.invalid_security');
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					}
				}else
				{
					$op['msg'] = trans('user/account.generate_profile_pin');
				}
			 }else
			 {
				$op['msg'] = trans('user/account.invalid_security');
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	 }
	 public function Withdrawals_history($status='') {		
	
		$data = $filter = array();
		$postdata = $this->request->all();	
		$data['payout_types'] = $this->withdrawalObj->withdrawal_payout_list();
	    $data['formUrl'] = $this->request->fullUrl();
	 	if(!isset($postdata['status']) || empty($postdata['status'])){	
				$data['status'] = [$this->config->get('constants.WITHDRAWAL_STATUS.PENDING')];
		}
		else {
			  $data['status'] = $postdata['status'];
		}
		if (!empty($postdata))  {	
          $filters['account_id'] = $this->userSess->account_id;		
		  $filters['status'] = (isset($postdata['status']) && !empty($postdata['status'])) ? $postdata['status'] : $this->config->get('constants.WITHDRAWAL_STATUS.PENDING');
		  $filters['payout_type'] = (isset($postdata['payout_type']) && !empty($postdata['payout_type'])) ? $postdata['payout_type'] : '';
		  $filters['from'] = (isset($postdata['from']) && !empty($postdata['from'])) ? $postdata['from'] : '';
		  $filters['to'] = (isset($postdata['to']) && !empty($postdata['to'])) ? $postdata['to'] : '';
		  $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		}
		if (Request::ajax()) {
		
			$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
			$ajaxdata['url']  = URL::to('/');
			$ajaxdata['data'] = array();			
			$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = 0;
			$dat 			  = array_merge($data, $filters);			
			$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->withdrawalObj->withdrawals_history($dat, true);    

			if ($ajaxdata['recordsTotal'] > 0){
				$filter = array_filter($filters);
			   if (!empty($filter)){
					$data = array_merge($data, $filters);
					$ajaxdata['recordsFiltered'] = $this->withdrawalObj->withdrawals_history($data, true);
	
				}
			   if (!empty($ajaxdata['recordsFiltered'])){
					$data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
					$data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : $this->config->get('constants.DATA_TABLE_RECORDS');
					
					if (isset($data['order'])) {
						
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
					$data = array_merge($data, $filters);
					$ajaxdata['data'] = $this->withdrawalObj->withdrawals_history($data);
				}
			}
			return Response::json($ajaxdata);		
        }
		elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){			
		    $status=$this->config->get('constants.WITHDRAWAL_STATUS.'.$filters['status'].'');
			$edata['status']=ucfirst(strtolower($status));
			$edata['pending_withdrawals_list'] = $this->withdrawalObj->withdrawals_history(array_merge($data,$filters));	
            $output = view('franchisee.withdrawal.withdrawals_history_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename='.$edata['status'].' Withdrawals -' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
		 elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){	
            $status=$this->config->get('constants.WITHDRAWAL_STATUS.'.$filters['status'].'');
			$pdata['status']=ucfirst(strtolower($status));		 
			$pdata['pending_withdrawals_list'] = $this->withdrawalObj->withdrawals_history(array_merge($data,$filters));			
            return view('franchisee.withdrawal.withdrawals_history_print',$pdata);
        } 
        else{
		    return view('franchisee.withdrawal.withdraw_history',$data);  
		} 
	}
	public function withdrawal_details($trans_id){
		$postdata 			    = $this->request->all();
		$postdata['account_id'] = $this->account_id;
		$postdata['trans_id']   = $trans_id;
		$data['wd_details']     = $this->withdrawalObj->getWithdrawalDetails($postdata);
		$op['content'] 		    = view('franchisee.withdrawal.withdrawal_details',$data)->render();
		$op['status'] 			='ok';
		$this->statusCode       = $this->config->get('httperr.SUCCESS');
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
   public function cancel_withdrawal_request(){
		$postdata 				= $this->request->all();
		$postdata['account_id'] = $this->account_id;
		$result = $this->withdrawalObj->cancel_withdrawal($postdata);
		$op['msg']    = 'Something went wrong';
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		if(!empty($result)){
			$op['success'] = 'SUCCESS!';
			$op['msg']     = 'Successfully cancelled';
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		
	}
}
