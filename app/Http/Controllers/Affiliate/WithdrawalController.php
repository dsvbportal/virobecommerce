<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\Withdrawal;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\AffModel;
use App\Models\Commonsettings;
use App\Models\Affiliate\Payments;
use App\Helpers\CommonNotifSettings;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use Validator;
use CommonLib;

class WithdrawalController extends AffBaseController
{

    private $packageObj = '';

    public function __construct ()
    {
        parent::__construct();
        $this->withdrawalObj = new Withdrawal($this->commonObj);
        $this->paymentObj = new Payments;
        $this->walletObj = new Wallet;        
        $this->affObj = new AffModel;        
    }

    public function new_withdrawal ()
    {
        $op = array();
        $data = array();
        $postdata 			  = $this->request->all();
        $wdata['account_id']  = $this->userSess->account_id;
        $data['balance_list'] = $this->withdrawalObj->withdrawal_wallet_balance_list($wdata);
        return view('affiliate.withdrawal.create', $data);
    }

    public function withdrawal_list ($status)
    {
        $data = array();
        $data['status_array'] = array('pending'=>0, 'transferred'=>1, 'processing'=>2, 'cancelled'=>3, 0=>'pending', 1=>'transferred', 2=>'processing', 3=>'cancelled');
        if (in_array($status, $data['status_array']))
        {
            $data['status_key'] = $status;
            $data['pg_title'] = ucwords($status).' Withdrawals';
            $data['status'] = $data['status_array'][$status];
            $data['status_label_array'] = array('label label-warning', 'label label-success', 'label label-info', 'label label-danger');
        }
        $data['wallet_list'] = $this->walletObj->get_wallet_list(array('withdrawal_status'=>$this->config->get('constants.ON')));
        return view('affiliate.withdrawal.withdrawal_list', $data);
    }

    public function payoutTypesList ()
    {
        $data = [];
        $op = [];
        $data['country_id'] = $this->userSess->country_id;
        $data['payouts'] = $this->withdrawalObj->withdrawal_payout_list($data); 
		return view('affiliate.withdrawal.payout',$data);
    }

    public function payoutDetails ()
    {
        $op = [];
        $data = $this->request->all(); 
        if ($this->request->has('payout_type_key') && ($payoutType = $this->withdrawalObj->payoutTypeDetails($this->request->get('payout_type_key'))))
        {
            $data['payout_type_key'] = $payoutType->payment_key;
            $data['payment_type_id'] = $payoutType->payment_type_id;
            $data['currency_id'] 	 = $this->request->has('currency_id') ? $this->request->get('currency_id') : $this->userSess->currency_id;
	
            $data['account_id'] 	 = $this->userSess->account_id;
            if ($payoutType && !empty($payoutType) && (empty($payoutType->currency_allowed) || (!empty($payoutType->currency_allowed) && array_key_exists($data['currency_id'], $payoutType->currency_allowed))) && ($payoutType->is_country_based == $this->config->get('constants.OFF') || ($payoutType->is_country_based == $this->config->get('constants.ON') && isset($payoutType->currency_allowed[$data['currency_id']]))))
            {
		        if ($payoutType->is_user_country_based == $this->config->get('constants.OFF') || ($payoutType->is_user_country_based == $this->config->get('constants.ON') && array_key_exists($data['currency_id'], $payoutType->currency_allowed)))
                {
                    $settings = $this->withdrawalObj->get_balance_bycurrency($data);
					//print_r($settings);exit;
                    if (!empty($settings))
                    {
                        $data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['max'];
                        if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
                        {
                            $proceed = true;
                            $total_breakdowns = 0;
                            if (isset($data['breakdowns']) && !empty($data['breakdowns']))
                            {
                                foreach ($settings['breakdowns'] as $balance_breakdowns)
                                {
                                    if (isset($data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id]))
                                    {
                                        $dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id];
                                        if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->min || $dreakdown > $balance_breakdowns->max))
                                        {
                                            $proceed = false;
                                        }
                                        $total_breakdowns+=$dreakdown;
                                    }
                                }
                            }
                            else
                            {
                                $total_breakdowns = $data['amount'];
                            }
                            if ($proceed)
                            {
                                $op = array_merge((array) $op, $settings);
                                $op['amount'] = $data['amount'] = $total_breakdowns;
                                $op['account_details'] = $this->withdrawalObj->get_preBank_info($data);
                                $op['currency_code'] = $settings['currency_code'];


                                $op['currency_symbol'] = $settings['currency_symbol'];
                                unset($payoutType->payment_type_id);
                                unset($payoutType->is_country_based);
                                unset($payoutType->is_user_country_based);
                                unset($payoutType->countries_not_allowed);
                                unset($payoutType->countries_allowed);
                                $op['payout_type_details'] = $payoutType; //print_r( $op['payout_type_details']);exit;
                                $op['status'] = 200;
                            }
                            else
                            {
                                $op['msg'] = trans('user\general.invalid_breakdown');
                            }
                        }
                        else
                        {
                            $op['msg'] = trans('user\general.insufficient_bal');
                        }
                    }
                    else
                    {
                        $op['msg'] = trans('user\general.contact_administrator');
                    }
                }
                else
                {
                    $op['msg'] = trans('user\general.country_not_allowed');
                }
            }
            else
            {
                $op['msg'] = trans('user\general.please_contact_administrator');
            }

            return $this->response->json($op, 200);
        }
    }

    public function saveWithdraw ()
    {
        $op = [];
        $data = $this->request->all();

        if ($this->request->has('payout_type_key') && ($payoutType = $this->withdrawalObj->payoutTypeDetails($this->request->get('payout_type_key'))))
        {
            $data['payment_type_id'] = $payoutType->payment_type_id;
            $data['currency_id'] = $this->request->has('currency_id') ? $this->request->get('currency_id') : $this->userSess->currency_id;
            $data['account_id'] = $this->userSess->account_id;
            if ($payoutType && !empty($payoutType) && (empty($payoutType->currency_allowed) || (!empty($payoutType->currency_allowed) && array_key_exists($data['currency_id'], $payoutType->currency_allowed))) && ($payoutType->is_country_based == $this->config->get('constants.OFF') || ($payoutType->is_country_based == $this->config->get('constants.ON') && isset($payoutType->countries_allowed[$data['currency_id']]))))
            {
                if ($payoutType->is_user_country_based == $this->config->get('constants.OFF') || ($payoutType->is_user_country_based == $this->config->get('constants.ON') && array_key_exists($data['currency_id'], $payoutType->currency_allowed)))
                {
                    $settings = $this->withdrawalObj->get_balance_bycurrency($data);
                    if (!empty($settings))
                    {
                        $data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['balance'];
                        if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
                        {
                            $proceed = true;
                            $total_breakdowns = 0;
                            if (isset($data['breakdowns']) && !empty($data['breakdowns']))
                            {
                                foreach ($settings['breakdowns'] as $balance_breakdowns)
                                {
                                    if (isset($data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id]))
                                    {
                                        $dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id];
                                        if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->min || $dreakdown > $balance_breakdowns->max))
                                        {
                                            $proceed = false;
                                        }
                                        $total_breakdowns+=$dreakdown;
                                    }
                                }
                            }
                            else
                            {
                                $total_breakdowns = $data['amount'];
                            }
                            if ($proceed && $total_breakdowns == $data['amount'])
                            {
                                unset($settings['breakdowns']);
                                $data = array_merge((array) $data, $settings);
                                $op   = array_merge((array) $op, $settings);
                                $op['account_details'] = $this->withdrawalObj->get_preBank_info($data);
                                $op['currency_code'] = $settings['currency_code'];
                                $op['currency_symbol'] = $settings['currency_symbol'];
                                $op['payout_type_details'] = $payoutType;
                                if ($this->withdrawalObj->saveWithdrawal($data))
                                {
                                    $this->statusCode = 200;
                                    $op['msg'] = trans('affiliate/withdrawal.request_updated_successfully');
                                }
                                else
                                {
                                    $op['msg'] = trans('affiliate/general.something_went_wrong');
                                }
                            }
                            else
                            {
                                $op['msg'] = trans('affiliate/withdrawal.invalid_breakdown');
                            }
                        }
                        else
                        {
                            $op['msg'] = trans('affiliate/withdrawal.insufficient_bal');
                        }
                    }
                    else
                    {
                        $op['msg'] = trans('affiliate/general.please_contact_administrator');
                    }
                }
                else
                {
                    $op['msg'] = trans('affiliate/withdrawal.country_not_allowed');
                }
            }
            else
            {
                $op['msg'] = trans('affiliate/general.please_contact_administrator');
            }
            return $this->response->json($op, 200);
        }
    } 
	  
	 public function Withdrawals_history($status='') {		
	
		$data = $filter = array();
		$postdata = $this->request->all();	
		
		$data['payout_types'] = $this->withdrawalObj->withdrawal_payout_list();
		$data['currency_list']=$this->commonstObj->get_currencies_list(['allowed_curr'=>[$this->userSess->currency_id]]);
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
		  $filters['uname'] = (isset($postdata['username']) && !empty($postdata['username'])) ? $postdata['username'] : '';
		  $filters['currency'] = (isset($postdata['currency']) && !empty($postdata['currency'])) ? $postdata['currency'] : '';
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
            $output = view('affiliate.withdrawal.withdrawals_history_export',$edata);
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
            return view('affiliate.withdrawal.withdrawals_history_print',$pdata);
        } 
        else{
		    return view('affiliate.withdrawal.withdraw_history',$data);  
		} 
	}
	public function withdrawal_request(){
		  $postdata = $this->request->all();
		  $data['payment_type_id'] 	= 19;		
		  $data['payout_info'] 	    = [];
		  $current_balance['balance'] = 0;
		  $data['currency_id']		= $this->userSess->currency_id;		
		  $data['country_id'] 		= $this->userSess->country_id;		
		  /* $payout_info				= $this->withdrawalObj->account_payout($this->account_id);	
		  if(!empty($payout_info)){
			  $data['payout_info'] = $payout_info->settings;
		  } */
		 // $settings	    			= $this->withdrawalObj->get_withdrwal_settings($data);	
		 //$data['settings']	        =  stripcslashes(json_encode($settings));
		  $data['payouts']		    = $this->withdrawalObj->withdrawal_payout_list($data); 
		  $data['balance']			= $this->walletObj->my_wallets(['currency_id'=>$this->userSess->currency_id,'account_id'=>$this->account_id,'wallet_id'=>$this->config->get('constants.WALLETS.VI')]);
		  if(!empty($data['balance'])){			  
			  $current_balance['balance'] =  $data['balance']->current_balance;
		  }		 
		  $data['current_balance'] = json_encode($current_balance);		 
		  $data['support_emailid'] = $this->siteConfig->support_emailid;	
		  return view('affiliate.withdrawal.withdraw_request',$data);
	}
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
            					$op['msg'] =  trans('affiliate/withdrawal/payout_settings.missing',['redirect_link'=>route('aff.profile.bank-info')]);
            					$op['msgClass'] = 'warning';
							}
						}
						else if($payment_typeInfo->payment_type_id==$this->config->get('constants.PAYMENT_TYPES.VI_MONEY')){
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						}						
						
					} else {
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		$op['msg'] =  trans('affiliate/withdrawal/payout_settings.insufficient_bal',['amount'=>CommonLib::currency_format($op['settings']->min_amount, ['currency_code'=>$op['settings']->currency_code, 'decimal_places'=>$op['settings']->decimal_places, 'currency_symbol'=>$op['settings']->currency_symbol])]);
						$op['msgClass'] = 'warning';
					}
				} else {
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] =  trans('affiliate/withdrawal/payout_settings.missing',['redirect_link'=>route('aff.profile.bank-info')]);
					$op['msgClass'] = 'warning';
				}
			} else {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] =  trans('affiliate/withdrawal/payout_settings.not_available');
				$op['msgClass'] = 'warning';
			}
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function save_withdrawal(){
		$op 				 = [];
		$op['status'] 		 = '';	
      	$data 				 = $this->request->all();
			if(isset($this->request->security_pin))
            {
				if(!$this->userSess->has_pin)
				{					
					$data = [];
					$data['security_pin'] = $this->request->security_pin;
					$data['account_id'] = $this->userSess->account_id;
					
					if ($res = $this->affObj->saveProfilePIN($data))
					{						
						$this->userSess->has_pin = true;
						$this->session->set($this->sessionName, (array) $this->userSess);						
					}
				}	
				if($this->userSess->has_pin)
				{			
					$check_profilepin=$this->affObj->profilepin_check($this->userSess->account_id);			
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
	
	public function withdrawal_details($trans_id){
		
		$postdata 			    = $this->request->all();
		$postdata['account_id'] = $this->account_id;
		$postdata['trans_id']   = $trans_id;
		$data['wd_details']     = $this->withdrawalObj->getWithdrawalDetails($postdata);
		$op['content'] 		    = view('affiliate.withdrawal.withdrawal_details',$data)->render();
		$op['status'] 			='ok';
		$this->statusCode       = $this->config->get('httperr.SUCCESS');
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
}
