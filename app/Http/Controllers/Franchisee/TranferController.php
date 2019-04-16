<?php

namespace App\Http\Controllers\Franchisee;
use App\Http\Controllers\FrBaseController;
use App\Library\MailerLib;
use SendSMS;
//use App\Models\Franchisee\Package;
use App\Models\Franchisee\FrModel;
use App\Models\Franchisee\WalletModel;
use App\Models\Franchisee\FrTransaction;
//use App\Models\Franchisee\Payments;
use App\Helpers\CommonNotifSettings;
use Session;

use Illuminate\Support\Facades\Input;
use Request;
use Response; 

class TranferController extends FrBaseController
{
	private $smsObj;
	private $packageObj = '';
	
    public function __construct ()
    {
        parent::__construct();
		$this->frObj = new FrModel;
		$this->walletObj = new WalletModel;
		$this->frTransferObj = new FrTransaction();	
	    $this->smsObj = new SendSMS;	
		//$this->paymentsObj= new Payments;
		//$this->mailerLib = new MailerLib;
		
    }
	
	public function fundtransfer()
	{
	    $arr = $data = $postdata =[];
		$arr['account_id']= $this->userSess->account_id; //259	
        $data['show_all'] 			 = 0;
        $data['account_verif_count'] = 0;
	    $data['account_verif_count']  = $this->frObj->get_user_verification_total($arr);
		$data['account_verif_count'] = 1 ;
		$data['userdetails'] = $this->frObj->getAccInfo($arr);		
		if (!empty($data['userdetails'])&& $data['userdetails']->status == 1)
		{
			$charge = 0;			
			$data['currency'] =json_encode($this->walletObj->get_currencies($arr));	
			$ud = $this->frObj->getUser_loginDetails($arr, array('trans_pass_key'));			
			/* if ($ud)
			{				
				Session::put('fund_transfer', $ud->trans_pass_key);  // dont get
			} */
			if (empty($postdata))
			{			
				$data['user_setting_key_charges'] = $this->walletObj->getSetting_key_charges();				
			}	
			$data['current_balance'] 	= 0;
			$data['availbalance'] 		= 0;
			
			$data['settings'] = $this->walletObj->get_fund_transfer_settings(array(
							'transfer_type'=>$this->config->get('constants.FUND_TRANSFER'),
							'currency_id'=>$this->userSess->currency_id));

			$data['wallet_balance'] = $this->walletObj->getWalletBalnceTotal(['wallet_id'=>$this->config->get('constants.WALLETS.VP'),'currency_id'=>$this->userSess->currency_id,'account_id'=>$this->userSess->account_id,'purpose'=>'transfer']);
			
			$data['from_account_id']    = $arr['account_id'];        

        }else{
			$data['msg'] = 'Please Verify your account';
		}		
		//echo '<pre>';print_R($data);exit;
		return view('franchisee.wallet.fundtransfer',$data);
    }
		
	public function searchacc($user_name = '')
    {		
        $op = array();
        $op['status'] = 'error';
        $op['msg'] = trans('franchisee/wallet/fundtransfer.invalid_username');
        $user_pass = 0;
        if (Request::ajax())
        {
            $postdata = $this->request->all();	
			
		    if (!isset($postdata['username']) && empty($postdata['username']))
            {
                $postdata['username'] = $user_name;
                $user_pass = 1;
            }
        }
        else
        {
            $postdata['username'] = $user_name;
        }
        if ($postdata)
        {
            $userid = 0;
            $status = $this->frObj->usercheck_for_fundtransfer($postdata['username']);
            $userid = $this->userSess->account_id;
            if (!empty($status))
            {
				if($status['status'] == 400){
					$op['status'] = $this->statusCode =  $status['status'];
					$op['error'] = ['to_account'=>[$status['msg']]];			
				}
				else {
					 $op = $status;
					 $this->statusCode =  $status['status'];
					 $status['status'] = 'ok';
				}
				return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
            }
            else
            {				
                $op = $status;
            }
        }
        if (Request::ajax()) {
            if ($user_pass == 1) {
                return $op;
            }
			else {
				return Response::json($op);
			}
        }
        else {
            return $op;
        }
		return $op;
    }
	
	public function get_tac_code ()
    {		
        $data['siteConfig'] =$this->siteConfig;
		$arr=[];
		$postdata = $this->request->all();
        $account_id = $this->userSess->account_id;
        $data['userdetails']=$this->userSess;
		$arr['account_id']=$this->userSess->account_id;
		$op = array(
            'status'=>'error',
            'msg'=>'null');
        $postdata = $this->request->all();
        $req = (Input::get('req')) ? Input::get('req') : '';
        Session::forget($arr['account_id'].'_fund_transfer_tac_code');
        $current_account_id = $arr['account_id'];
	    $data['email'] 		= $data['userdetails']->email;
		$tac_code 			= rand(100000, 999999);
        $data['tac_code'] 	= $tac_code;
        $op['tac_code'] 	= $tac_code;                
	    $data['user'] 		= $data['userdetails'];
        if (isset($account_id) && !empty($account_id) && $current_account_id == $account_id)
        {
			
          if(empty($req) && !Session::has($account_id.'_fund_transfer_tac_code'))
            {
			    Session::put($account_id.'_fund_transfer_tac_code', $tac_code);
                $htmls = view('emails.account.settings.tac_code', $data)->render();
		        $mstatus = new MailerLib(array(
                    'to'=>$data['email'],
                    'subject'=>'TAC Code for Fund Transfer',
                    'html'=>$htmls,
					'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                ));
				$res=$this->smsObj->send_sms(['reset_code'=>$tac_code,'phonecode'=>$this->userSess->phonecode,'mobile'=>$this->userSess->mobile,'site_name'=>$this->siteConfig->site_name],$this->config->get('sms_service.FUNDTRANSFER_CODE'));
	            $op['status'] = 'ok';
			    $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
                            'email_id'=>$data['email']));
            }
			else if (!empty($req) && $req == 'usrexg' && !Session::has($account_id.'_usrexchange_tac_code'))
            {
                Session::put($account_id.'_usrexchange_tac_code', $tac_code);
                $htmls = view('emails.account.settings.tac_code', $data)->render();
                $mstatus = new MailerLib(array(
                    'to'=>$data['email'],
                    'subject'=>'TAC Code for Exchange Currency',
                    'html'=>$htmls,
                    'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                ));
                $op['status'] = 'ok';
               $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
                            'email_id'=>$data['email']));
            }
            else if (!empty($req) && $req == 'usrauth' && !Session::has($account_id.'usrtoken'))
            {
                Session::put($account_id.'usrtoken', $tac_code);
                $htmls = view('emails.account.settings.tac_code', $data)->render();
                $mstatus = new MailerLib(array(
                    'to'=>$data['email'],
                    'subject'=>'TAC Code for Account Login',
                    'html'=>$htmls,
                     'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                ));
                $op['status'] = 'ok';
                $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
                            'email_id'=>$data['email']));
            }
            else
            {
                $op['status'] = 'ERR';
                $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_already_send_msg', array(
                            'email_id'=>$data[email]));
            }
        }
        return json_encode($op);
    }
	
	
	public function fund_transfer_to_account_confirm ()
    {	
		$data = $sessdata = $postdata = [];
		$arr['account_id']=$this->userSess->account_id;
		$postdata = $this->request->all();			
        if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }
		$userdetails = $this->frObj->getAccInfo(['account_id'=>$this->userSess->account_id]);	  
	    if (!empty($userdetails)&& $userdetails->block == 0 && !empty($postdata))
        {	
			$ftSess = $this->session->get('ftac');
		//	print_r($ftSess);die;
			$toAcInfo = (object)$ftSess['to_account_info'];	
			$sessdata['current_balance'] = 0;
			//$touser = $this->frObj->usercheck_for_fundtransfer($postdata['to_account']);
			$sessdata['account_settings']= $this->walletObj->get_user_settings($arr);	// NO NEED	
			$data['wallet_balance'] = $this->walletObj->getWalletBalnceTotal(['wallet_id'=>$this->config->get('constants.WALLETS.VP'),'currency_id'=>$this->userSess->currency_id,'account_id'=>$this->userSess->account_id,'purpose'=>'transfer']);				
			
			$settings = $this->walletObj->get_fund_transfer_settings(array(
							'transfer_type'=>$this->config->get('constants.FUND_TRANSFER'),
							'currency_id'=>$this->userSess->currency_id));

			$postdata['min_trans_amount'] = $settings->min_amount;
			$postdata['max_trans_amount'] = $settings->max_amount;
			$postdata['charge'] = $settings->charge_percentage;
			
			$sessdata['currency_id'] = $currency_id = $this->userSess->currency_id;			
			$sessdata['currency_code'] = $this->userSess->currency_code;		
			$sessdata['wallet_id'] = $wallet_id = $data['wallet_balance']->wallet_id;
			$sessdata['ewallet_name'] = $wallet_id = $data['wallet_balance']->wallet;	
			$sessdata['to_account'] 		= $toAcInfo->uname;
		 	$sessdata['to_account_id'] 		= $toAcInfo->account_id;
		 	$sessdata['to_usercode'] 		= $toAcInfo->user_code;
		 	$sessdata['to_account_type_id'] = $toAcInfo->account_type_id;
			if($toAcInfo->account_type_id==$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')){
				$sessdata['to_franchisee_typeid'] = $toAcInfo->franchisee_type;
			}
			$sessdata['rec_name'] 			= $toAcInfo->fullname;
			$sessdata['rec_email'] 			= $toAcInfo->email;
			$sessdata['totamount'] 			= $postdata['totamount'];
			$sessdata['min_trans_amount']   = $postdata['min_trans_amount'];
			$sessdata['max_trans_amount']   = $postdata['max_trans_amount'];
			$sessdata['charge'] 			= $postdata['charge'];
			$sessdata['remark'] 			= $postdata['remarks'];
			$sessdata['from_account_id']	= $this->userSess->account_id;	
			$sessdata['from_account_type_id']	= $this->userSess->account_type_id;	 
			$sessdata['from_uname'] = $this->userSess->uname;
            $sessdata['from_full_name'] = $this->userSess->full_name;

            /*$charge = 0;            
			$sessdata['currency'] =json_encode($this->walletObj->get_currencies($arr));	
            $user_balance_det = $this->walletObj->get_user_balance(1,$arr, $wallet_id, $currency_id,'fundtransfer_status');
			$sessdata['availbalance'] = 0;
            if ($user_balance_det)
            {
                $sessdata['availbalance'] = $user_balance_det->current_balance;				
            } */
			$sessdata['availbalance'] = $data['wallet_balance']->current_balance;
            $sessdata['fund_trasnfer_settings'] = json_encode($this->walletObj->get_fund_transfer_settings(array('transfer_type'=>$this->config->get('constants.FUND_TRANSFER')))); // NO NEED	
            //$sessdata['from_account_id'] = $arr;
			$this->session->put('ftsess',$sessdata);
			$data = array_merge($data,$sessdata);
        }		
		else {
		    $data['error']= trans('franchisee/wallet/fundtransfer.cant_transfer_fund');
		}
		return view('franchisee.wallet.fund_transfer_confirm',$data);
    }
	
	/* To Confirm */
	public function fund_transfer_to_account ()
    {  
		$arr = $postdata = [];	   
		$postdata = $this->request->all();	 
	    if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }	
		if(!empty($postdata))
		{
	        if ($postdata['submit'] == 'Back')
			{            
				$op['viewdata'] = $this->fund_transfer_to_account_back();
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
			}else{
				if($this->session->has('ftsess')){			
					$acc_details = $this->walletObj->get_userdetails_byid($this->userSess->account_id);				
					if (!empty($acc_details) && $acc_details->trans_pass_key == md5($postdata['tac_code']))
					{			
						$postdata = array_merge($postdata,$this->session->get('ftsess'));						
						$postdata['from_uname'] = $this->userSess->uname;
						$postdata['from_full_name'] = $this->userSess->franchisee_name;
						$postdata['from_email'] = $this->userSess->email;	
						$postdata['account_id']=$this->userSess->account_id;
						$postdata['usercode']=$this->userSess->user_code;
						
						$result = $this->frTransferObj->fund_transfer_to_account($postdata);
					
						if(!empty($result)){
							$this->session->forget('ftsess');
							$this->session->forget('ftac');
							$op['status'] = $this->statusCode = $result['status'];
							$op['msg'] = $result['msg'];		
							$op['reload'] = isset($result['reload'])?$result['reload']:false;	
							$sess_msg = ['msg'=>$result['msg'],'msgClass'=>'success'];
							$this->session->flash('transafer_status',$sess_msg);						
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							$op['msg'] = trans('franchisee/wallet/fundtransfer.cant_transfer_amt');						
						}
					}
					else
					{				
						$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');	
						$op['error'] = ['tac_code'=>trans('franchisee/wallet/fundtransfer.incorrect_trans_code')];
					}
				}
				else
				{				
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');	
					$op['msg'] = trans('franchisee/wallet/fundtransfer.invalid_req');
				}
			}		
		}
		else{				
			$op['status'] = 422; //$this->statusCode = $this->config->get('httperr.PARAMS_MISSING');	
			$op['error'] = 'Something ent wrong!.';
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
	}
	
	/* public function fund_transfer_to_account_old ()
    {  
		$arr = $postdata = [];
	    $op = array(
			'status'=>'error',
			'msg'=>'null');
		$postdata = $this->request->all();
		$arr['account_id']=$this->userSess->account_id;		
	 	$userdetails = $this->frObj->getAccInfo($arr);
	    $data['created_on'] = $postdata['created_on'] = getGTZ(); 
		
        if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }
		$postdata = array_merge($postdata,$this->session->get('ftsess'));	
	
        if ($postdata['submit'] == 'Back')
        {            
			$op['viewdata'] = $this->fund_transfer_to_account_back();
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else{		 
			$data = $email_data = [];	
			$payment_type = $this->config->get('constant.PAYMENT_TYPES.WALLET');
			$tac_check = 0;
			$data['siteConfig'] =$this->siteConfig;		
			$acc_details = $this->walletObj->get_userdetails_byid($this->userSess->account_id);
			if ($acc_details->trans_pass_key == md5($postdata['tac_code']))
			{
				$data['from_uname'] = $this->userSess->uname;
				$data['from_full_name'] = $this->userSess->full_name;
				$data['from_email'] = $this->userSess->email;
				$email_data['from_email'] = $this->email = $this->userSess->email;				
				$ewallet_id = $postdata['wallet_id'];
			
				$bal_details = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
												
				if ($bal_details && count($bal_details) > 0 && $bal_details->current_balance > 0 && $bal_details->current_balance >= $postdata['totamount'])
				{					
					$check_to_account = $this->searchacc($postdata['to_account']);
		
					if ($check_to_account['status'] == "ok") {
					
						$fund_trasnfer_settings = $this->walletObj->get_fund_transfer_settings(array(
							'currency_id'=>$postdata['currency_id'],
							'transfer_type'=>$this->config->get('constants.FUND_TRANSFER_TYPE.FT_FR_TO_USER')));
						
						if($postdata['totamount'] >= $fund_trasnfer_settings->min_amount || $postdata['totamount'] <= $fund_trasnfer_settings->max_amount)
						{
						    $handleamt = 0;
							$paidamt = $postdata['totamount'];
							if (!empty($fund_trasnfer_settings->charge_percentage) && $fund_trasnfer_settings->charge_percentage > 0)
							{
							    $handleamt = ($fund_trasnfer_settings->charge_percentage / 100) * $postdata['totamount'];
							}
							else if (!empty($fund_trasnfer_settings->charge_amount) && $fund_trasnfer_settings->charge_amount > 0)
							{
								$handleamt = $fund_trasnfer_settings->charge_amount;
							}
							$paidamt = $postdata['totamount'] - $handleamt;							
						
						    if($postdata['totamount'] > $handleamt)
							{							
								$postdata['to_account_id'] = $check_to_account['account_id'];
								$from_cur_balance = $cur_balance = $bal_details->current_balance;							
								$dataArray['account_id'] = $this->userSess->account_id;
								$dataArray['wallet_id'] = $postdata['wallet_id'];
								$dataArray['currency_id'] = $postdata['currency_id'];
								$dataArray['transaction_type'] = $this->config->get('constants.TRANS_TYPE.DEBIT');
								$dataArray['amount'] = $postdata['totamount'];
								$dataArray['paidamt'] = $paidamt;
								$dataArray['payment_type'] = $payment_type;
								$dataArray['purpose'] = 'fundtransfer_status';	
								$from_transaction_id = \AppService::getTransID($this->userSess->account_id); 	
								$data['from_transaction_id'] = $from_transaction_id;
								$all_user_details = $userdetails;						
								$status = $this->walletObj->update_user_balance($dataArray);								
								if ($status)
								{					
									$cur_balance1 = '';
									$bal_details1 = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
									if ($bal_details1 && count($bal_details1) > 0)
									{
										$cur_balance1 = $bal_details1->current_balance;
									}
									$transdata = array(
											'from_account_id' => $this->userSess->account_id,
											'to_account_id' => $postdata['to_account_id'],
											'from_user_type' =>$this->userSess->account_type_id,
											'to_user_type' => $postdata['account_type_id'],
											'transaction_id' => $from_transaction_id,
											'from_user_wallet_id' => $ewallet_id,
											'to_user_wallet_id' =>$ewallet_id,
											'currency_id' => $postdata['currency_id'],
											'amount' => $dataArray['amount'],
											'paidamt' => $dataArray['paidamt'],
											'handleamt' =>$handleamt,										
											'transferred_on' =>$postdata['created_on'] = getGTZ(),									 
											'ip_address' => Request::getClientIp(true),
											'status' =>$this->config->get('constants.STATUS_CONFIRMED'));
											
									$tstatus = $this->walletObj->add_transfertund_entry($transdata);     
									
									$debit_remark_data = addSlashes(json_encode(['data'=>['to'=>$postdata['to_account']]]));
									$dataTransArray = array(
										'account_id'=>$this->userSess->account_id,
										'payment_type_id'=>$this->config->get('constant.PAYMENT_TYPES.WALLET'),
										'currency_id'=>$postdata['currency_id'],
										'statementline_id'=>$this->config->get('stline.FUND_DEBITED_FROM_ACC'),  //
										'amt'=>$dataArray['amount'],
										'paid_amt'=>$dataArray['amount'],
										'handle_amt'=>$postdata['charge'],
										'wallet_id'=>$ewallet_id,
										'transaction_type'=>$this->config->get('constants.TRANS_TYPE.DEBIT'),
										'remark'=>$debit_remark_data,
										'ip_address'=>Request::getClientIp(true),
										'transaction_id'=>$from_transaction_id,
										'created_on'=> getGTZ(),
										'current_balance'=>$cur_balance1,
										'status'=>$this->config->get('constants.STATUS_CONFIRMED'));	
										
									$tstatus = $this->walletObj->add_user_transaction($dataTransArray);  
									
									$dataArray = [];
									$dataArray['account_id'] = $postdata['to_account_id'];
									$dataArray['wallet_id'] = $ewallet_id;
									$dataArray['currency_id'] = $postdata['currency_id'];
									$dataArray['transaction_type'] = $this->config->get('constants.TRANS_TYPE.CREDIT');
									$dataArray['amount'] = $postdata['totamount'];
									$dataArray['paidamt'] = $paidamt;
									$dataArray['payment_type'] = $payment_type;								
									$to_transaction_id = \AppService::getTransID($postdata['to_account_id']);  
									$data['to_transaction_id'] = $to_transaction_id;
									$dataArray['purpose'] = 'fundtransfer_status';
								
									$status1 = $this->walletObj->update_user_balance($dataArray);

									if ($status1)
									{
									   $bal_details = $this->walletObj->get_user_balance($dataArray['payment_type'], array('account_id'=>$postdata['to_account_id']), $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
									   if ($bal_details && count($bal_details) > 0)
										{
											$cur_balance = $bal_details->current_balance;
										}
										$credit_remark_data = addSlashes(json_encode(['data'=>['from'=>$all_user_details->uname]]));
										$dataTransArray = array(
											'account_id'=>$postdata['to_account_id'],
											'from_account_id'=>$this->userSess->account_id,
											'to_account_id'=>$postdata['to_account_id'],
											'payment_type_id'=>$this->config->get('constant.PAYMENT_TYPES.WALLET'),
											'currency_id'=>$postdata['currency_id'],
											'statementline_id'=>$this->config->get('stline.FUND_CREDITED_TO_ACC'),
											'amt'=>$postdata['totamount'],
											'paid_amt'=>$postdata['totamount'],
											'wallet_id'=>$ewallet_id,
											'transaction_type'=>$this->config->get('constants.TRANS_TYPE.CREDIT'),
											'ip_address'=>Request::getClientIp(true),
											'transaction_id'=>$to_transaction_id,
											'current_balance'=>$cur_balance,
											'created_on'=> getGTZ(),
											'remark'=>$credit_remark_data,
											'status'=>$this->config->get('constants.STATUS_CONFIRMED'));
										$tstatus1 = $this->walletObj->add_user_transaction($dataTransArray);
									}
									$to_accountdetails = $this->walletObj->get_userdetails_byid($postdata['to_account_id']);									
									$email_data['to_email'] = $to_accountdetails->email;
									$data['to_uname'] 		= $to_accountdetails->uname;
									$data['to_full_name'] 	= $to_accountdetails->firstname.' '.$to_accountdetails->lastname;
									$data['amount']			= $dataArray['amount'];
									$currency 				= $this->walletObj->get_currency_name($dataArray['currency_id']);   // No NEED
									$data['currency'] 		= $currency[0];
									if ($status && $tstatus && $status1 && $tstatus1)
									{
										Session::put('success', trans('franchisee/wallet/fundtransfer.transfer_fund_completed'));
										CommonNotifSettings::affNotify('emails.franchisee.account.settings.fundtransfer_fromuser',$this->userSess->account_id, 0, $data,true);
										CommonNotifSettings::affNotify('emails.franchisee.account.settings.fundtransfer_touser', $to_accountdetails->account_id, 0, $data,true);
										$op['reload'] = true;
										$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
										$op['msg'] = trans('franchisee/wallet/fundtransfer.transfer_fund_completed');
										$sess_msg = ['msg'=>trans('franchisee/wallet/fundtransfer.transfer_fund_completed'),'msgClass'=>'success'];
										Session::flash('transafer_status',$sess_msg);
									}
									else
									{								
										$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
										$op['msg'] = trans('franchisee/wallet/fundtransfer.transfer_fund_failed');
									}
								}
								else
								{
									$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
									$op['msg'] = trans('franchisee/wallet/fundtransfer.transfer_fund_failed');
								}
							}
							else {
								$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
								$op['msg'] = trans('franchisee/wallet/fundtransfer.request_not_process');	
							}
						}
						else {
							$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							$op['msg'] = trans('franchisee/wallet/fundtransfer.err_min_max_amt');	
						}
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = trans('franchisee/wallet/fundtransfer.cant_transfer_amt');						
					}
				}
				else {
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('franchisee/wallet/fundtransfer.insufficient_amt');	
				}
			}
			else
			{				
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');	
				$op['error'] = ['tac_code'=>trans('franchisee/wallet/fundtransfer.incorrect_trans_code')];
			}
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
		}
	} */
	
	
    public function transactions(){
		$data = array();
		return view('affiliate.wallet.fundtransfer_history',$data);
	}
	
	public function fundtransfer_history()
	{
		$data = $wdata = $filter = array();
		$post = $this->request->all();	
		$data['currencies']=$this->paymentsObj->get_currencies();
		$data['wallet_list']=$this->walletObj->get_all_wallet_list();
		$filter['account_id'] = $this->userSess->account_id; 
		if (isset($post['order']))
		{
			$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
			$wdata['order'] = $post['order'][0]['dir'];
		}																										
		$filter['search_term'] = $this->request->has('search_term')? $this->request->get('search_term') : '';
		$filter['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['wallet_id'] = $this->request->has('wallet_id')? $this->request->get('wallet_id') : '';
		$filter['currency_id'] = $this->request->has('currency_id')? $this->request->get('currency_id') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		if (\Request::ajax())        
		{
			$wdata['count'] = true;
		    $ajaxdata['recordsTotal'] = $this->walletObj->transfer_history_details(array_merge($wdata,$filter)); 
			
		  	$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
			
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
				$ajaxdata['recordsFiltered'] = $this->walletObj->transfer_history_details(array_merge($wdata,$filter));  //filtered
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				//print_r($ajaxdata);
				unset($wdata['count']);                    

				$ajaxdata['data'] = $this->walletObj->transfer_history_details(array_merge($wdata,$filter));  ///get data all results display//
				
			}
		    $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
		}
		else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')     //export data
		  {
			$epdata['export_data']= $this->walletObj->transfer_history_details(array_merge($wdata,$filter));
			//print_r($epdata);
			//exit;
            $output = view('affiliate.wallet.fundtransfer_export_history', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=Fund_Transfer_List_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
        } 
		else if (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')   //print data
		{
			$pdata['print_data']= $this->walletObj->transfer_history_details(array_merge($wdata,$filter));
	        return view('affiliate.wallet.fundtransfer_print_history', $pdata);
        }
		else
		{
			return view('affiliate.wallet.fundtransfer_history',$data);
		}
	}
	 
	public function fund_transfer_to_account_back ()
    {
	   $data['current_balance'] = 0;
	   $data['availbalance'] = 0;
	   $data['wallet_list'] = $this->walletObj->get_all_wallet_list(array(
                'fundtransfer_status'=>1));
	   $data['from_account_id'] = $this->userSess->account_id;
	   $viewdata = view('franchisee.wallet.fund_transfer_form',$data)->render();
       return $viewdata;
    }

	
	
	 
}
