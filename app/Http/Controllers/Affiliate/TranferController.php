<?php

namespace App\Http\Controllers\Affiliate;
use App\Http\Controllers\AffBaseController;
use App\Library\MailerLib;
use SendSMS;
use App\Models\Affiliate\Package;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Payments;
use App\Helpers\CommonNotifSettings;
use Session;

use Illuminate\Support\Facades\Input;
use Request;
use Response; 

class TranferController extends AffBaseController
{
	private $smsObj;
	private $packageObj = '';
	
  public function __construct ()
    {
        parent::__construct();
		$this->walletObj = new Wallet;
		$this->affObj = new AffModel;
	    $this->smsObj = new SendSMS;	
		$this->paymentsObj= new Payments;
		//$this->mailerLib = new MailerLib;
		
    }
	
	public function fundtransfer()
	{
	    $arr=[];
		$arr['account_id']=$this->userSess->account_id;
		$data = array();
        $data['show_all'] 			 = 0;
        $data['account_verif_count'] = 0;
	    $account_verification_count  = $this->affObj->get_user_verification_total($arr);

		$data['account_verif_count'] = 1 ;
		$data['userdetails'] = $this->affObj->getUser_treeInfo($arr);
		if (!empty($data['userdetails'])&& $data['userdetails']->status == 1)
		{
				$charge = 0;
				$data['currency'] =json_encode($this->walletObj->get_currencies($arr));
				$ud = $this->affObj->getUser_loginDetails($arr, array('trans_pass_key'));
			if ($ud)
			 {
				
				Session::put('fund_transfer', $ud->trans_pass_key);
			 }
			if (empty($postdata))
			 {
				$data['user_setting_key_charges'] = $this->walletObj->getSetting_key_charges();
			 }
			$data['current_balance'] 	= 0;
			$data['user_balance_det'] 	= $this->walletObj->getWalletBalnceTotal(array('account_id'=>$this->userSess->account_id));
			$data['availbalance'] 		= 0;
			$data['fund_trasnfer_settings'] = json_encode($this->walletObj->get_fund_transfer_settings(array(
							'transfer_type'=>$this->config->get('constants.FUND_TRANSFER'),'currency_id'=>$this->userSess->currency_id)));
							
			$data['wallet_balance'] = $this->walletObj->getWalletBalnceTotal(['wallet_id'=>$this->config->get('constants.WALLETS.VP'),'account_id'=>$this->userSess->account_id]);
			
			$data['from_account_id']    = $arr['account_id'];
          	 
        }else{
			$data['msg'] = 'Please Verify your account';
		}
 /*   echo '<pre>';print_R($data);exit;     */
		return view('affiliate.wallet.fundtransfer',$data);
    }
		
	public function searchacc($user_name = '')
    {
		
        $op = array();
        $op['status'] = 'error';
        $op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
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
			if($postdata['username']!=$this->userSess->user_code && $postdata['username']!=$this->userSess->email && $postdata['username']!=$this->userSess->uname){			
				$userid = 0;
				$status = $this->affObj->usercheck_for_fundtransfer($postdata['username']);
				$userid = $this->userSess->account_id;
				if (!empty($status) && $status['status'] == 'error')
				{				
					$op['status'] = $this->statusCode = 400;
					$op['error'] = ['to_account'=>[trans('affiliate/wallet/fundtransfer.invalid_username')]];			
					return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
				}
				else
				{
					unset($status['msg']);
					$sessdata['to_account_info'] = ['account_id'=>$status['account_id'],'uname'=>$status['uname'],'user_code'=>$status['user_code'],'fullname'=>$status['full_name'],'email'=>$status['email']];
					$this->session->put('ftsess',$sessdata);
					$op = $status;
				}
			}
			else {
				$op['status'] = $this->statusCode = 400;
				$op['error'] = ['to_account'=>[trans('affiliate/wallet/fundtransfer.disabled_tosame_account')]];
				return $this->response->json($op, $this->statusCode, $this->headers, $this->options);  
			}
        }
        if (Request::ajax())
        {
            if ($user_pass == 1)	
            {
                return $op;
            }
            return Response::json($op);
        }
        else
        {
            return $op;
        }
    }
	
	public function get_tac_code ()
    {
		$arr=[];
		$postdata = $this->request->all();
        $account_id = $this->userSess->account_id;
		$arr['account_id']=$this->userSess->account_id;
		$op = array(
            'status'=>'error',
            'msg'=>'null');
        $postdata = $this->request->all();      
        $current_account_id = $arr['account_id'];
	    $data['email'] = $this->userSess->email;;
		$tac_code = rand(100000, 999999);
        $data['tac_code'] = $tac_code;
        $op['tac_code'] = $tac_code;                
		CommonNotifSettings::affNotify('affiliate.fund_transfer.verify_otp',0, 0, $data,true,false);	
		$op['status'] = 200;
		$op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
					'email_id'=>maskemail($data['email'])));	       
        return $op;
    }
	
	
	public function fund_transfer_to_account_confirm ()
    {
		$data=[];
		$sessdata = [];
		$arr['account_id']=$this->userSess->account_id;
		$sessdata = $this->session->get('ftsess');
        if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }
		$userdetails = $this->affObj->getUser_treeInfo(['account_id'=>$this->userSess->account_id]);
	    if (!empty($userdetails)&& $userdetails->block == 0)
        {
			$data['userObj'] = $this->affObj;
			//print_r($data['userObj']); exit;
			$sessdata['current_balance'] = 0;
			$sessdata['account_settings']= $this->walletObj->get_user_settings($arr);
			$postdata = $this->request->all();	
			if ($postdata)
			{
				$tacRes = $this->get_tac_code();
				if(!empty($tacRes) && $tacRes['status']==200 && !empty($tacRes['tac_code'])){
				$data['vcode'] = $sessdata['vcode'] = $tacRes['tac_code'];
				$data['showmsg'] = $tacRes['msg'];
				//$sessdata['currency_id'] = $currency_id = $postdata['currency_id'];//us->currency_id
				$sessdata['currency_id'] = $currency_id =   $this->userSess->currency_id;
				$currency_code = $this->walletObj->get_currency_name($this->userSess->currency_id);
				$sessdata['currency_code'] = $currency_code[0];
				$sessdata['wallet_id'] = $wallet_id = $this->config->get('constants.WALLETS.VP');
				$sessdata['ewallet_name'] = $postdata['ewallet_name'];
	           if (isset($postdata['ewallet_name'])) 
				{
					$sessdata['ewallet_name'] = $this->walletObj->get_wallet_name($this->config->get('constants.WALLETS.VP'));
				}	
				$sessdata['to_account'] 		= $postdata['to_account'];
				$sessdata['rec_name'] 			= $postdata['rec_name'];
				$sessdata['rec_email'] 			= $postdata['rec_email'];
				$sessdata['totamount'] 			= $postdata['totamount'];
				$sessdata['min_trans_amount']   = $postdata['min_trans_amount'];
				$sessdata['max_trans_amount']   = $postdata['max_trans_amount'];
				$sessdata['charge'] 			= $postdata['charge'];
				$sessdata['from_account_id']	= $arr['account_id']; 
				$sessdata['remark']		        = $postdata['remarks'];
				}				
			}
			
            $charge = 0;            
			$sessdata['currency'] =json_encode($this->walletObj->get_currencies($arr));			
            $user_balance_det = $this->walletObj->get_user_balance(1,$arr, $wallet_id, $currency_id,'fundtransfer_status');
			$sessdata['availbalance'] = 0;
            if ($user_balance_det)
            {
                $sessdata['availbalance'] = $user_balance_det->current_balance;				
            }
            $sessdata['fund_trasnfer_settings'] = json_encode($this->walletObj->get_fund_transfer_settings(array('transfer_type'=>$this->config->get('constants.FUND_TRANSFER'))));
            $sessdata['from_account_id'] = $arr;			
            $sessdata['security_pwd'] = $userdetails->trans_pass_key;
			$this->session->put('ftsess',$sessdata);
			$data = array_merge($data,$sessdata);
        }		
		else {
		   $data['error']= trans('affiliate/wallet/fundtransfer.cant_transfer_fund');
		}
		return view('affiliate.wallet.fund_transfer_confirm',$data);
    }
	
	public function fund_transfer_to_account ()
    {
		$arr=[];
		$arr['account_id']=$this->userSess->account_id;
	    $data['created_on'] = $postdata['created_on'] = date('Y-m-d H:i:s');
		 //print_r($data['created_on']); exit;
        if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }
        $op = array(
            'status'=>'error',
            'msg'=>'null');
        if($this->session->has('ftsess')){
			$postdata = $this->request->all();
			$postdata = array_merge($postdata,$this->session->get('ftsess'));
			$check_to_account = $postdata['to_account_info'];		
			if ($postdata['submit'] == 'Back')
			{            
				$op['viewdata'] = $this->fund_transfer_to_account_back();
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
			}
		   else{			 
				$email_data = array();
				$data = array();
				$data['siteConfig'] =$this->siteConfig;
				$payment_type = 1;
				$tac_check = 0;
				//$data['account_settings']= $this->walletObj->get_user_settings($arr);
				//$acc_details = $this->walletObj->get_userdetails_byid($this->userSess->account_id);
				if ($postdata['vcode'] == $postdata['tac_code'])
				{
					$data['from_uname'] = $this->userSess->uname;
					$data['from_full_name'] = $this->userSess->full_name;
					$data['from_email'] = $this->userSess->email;
					$this->email = $this->userSess->email;
					$email_data['from_email'] = $this->email;
					$ewallet_id = $postdata['wallet_id'];
					$bal_details = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
					
					if ($bal_details && count($bal_details) > 0 && $bal_details->current_balance > 0 && $bal_details->current_balance >= $postdata['totamount'])
					{
						$fund_trasnfer_settings = $this->walletObj->get_fund_transfer_settings(array(
							'currency_id'=>$postdata['currency_id'],
							'transfer_type'=>$this->config->get('constants.FUND_TRANSFER')));
							
						$fund_trasnfer_settings = $fund_trasnfer_settings[0];
						//$check_to_account = $this->searchacc($postdata['to_account']);
			
						if ($check_to_account['account_id']>0  && $check_to_account['account_id']!=$this->userSess->account_id) {
							if($postdata['totamount'] >= $fund_trasnfer_settings->min_amount || $postdata['totamount'] <= $fund_trasnfer_settings->max_amount) {						
								$postdata['to_account_id'] = $check_to_account['account_id'];
								$cur_balance = $bal_details->current_balance;
								$from_cur_balance = $cur_balance;
								$dataArray['account_id'] = $this->userSess->account_id;
								$dataArray['wallet_id'] = $postdata['wallet_id'];
								$dataArray['currency_id'] = $postdata['currency_id'];
								$dataArray['transaction_type'] = $this->config->get('constants.TRANS_TYPE.DEBIT');
								$dataArray['amount'] = $postdata['totamount'];
								$dataArray['paidamt'] = $postdata['totamount'];
								$dataArray['payment_type'] = $payment_type;
								$dataArray['purpose'] = 'fundtransfer_status';
								
								$from_transaction_id = \AppService::getTransID($this->userSess->account_id);
								$data['from_transaction_id'] = $from_transaction_id;
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
											'transaction_id' => $from_transaction_id,
											'from_account_ewallet_id' => $ewallet_id,
											'to_account_ewallet_id' =>$ewallet_id,
											'currency_id' => $postdata['currency_id'],
											'amount' => $dataArray['amount'],
											'paidamt' => $dataArray['amount'],
											'handleamt' =>'0',
											//'ast_type' => $this->config->get('constants.USER'),
											'transfered_on' =>$postdata['created_on'] = date('Y-m-d H:i:s'),
											'remark'=>$postdata['remark'],
										 //	'transfered_by' => $admin_account_id,
											'ip_address' => Request::getClientIp(true),
											'status' =>$this->config->get('constants.STATUS_CONFIRMED'));
											
									$tstatus = $this->walletObj->add_transfertund_entry($transdata);
									$transfer_remarks = $postdata['remark'];
									
									
									$debit_remark_data = addSlashes(json_encode(['data'=>['comments'=>$transfer_remarks,'to_account_name'=>$check_to_account['fullname'],'to_account_code'=>$postdata['to_account']]]));
									$dataTransArray = array(
										'account_id'=>$this->userSess->account_id,
										'payment_type_id'=>$this->config->get('constant.PAYMENT_TYPES.WALLET'),
										'currency_id'=>$postdata['currency_id'],
										'statementline_id'=>$this->config->get('stline.FUND_DEBITED_FROM_ACC'),
										'amt'=>$dataArray['amount'],
										'paid_amt'=>$dataArray['amount'],
										'handle_amt'=>$postdata['charge'],
										'wallet_id'=>$ewallet_id,
										'transaction_type'=>$this->config->get('constants.TRANS_TYPE.DEBIT'),
										'remark'=>$debit_remark_data,
										'ip_address'=>Request::getClientIp(true),
										'transaction_id'=>$from_transaction_id,
										'created_on'=>date('Y-m-d H:i:s'),
										'current_balance'=>$cur_balance1,
										'status'=>1);	
										
									$tstatus = $this->walletObj->add_user_transaction($dataTransArray);
									
									$dataArray = [];
									$dataArray['account_id'] = $postdata['to_account_id'];
									$dataArray['wallet_id'] = $ewallet_id;
									$dataArray['currency_id'] = $postdata['currency_id'];
									$dataArray['transaction_type'] = $this->config->get('constants.TRANS_TYPE.CREDIT');
									$dataArray['amount'] = $postdata['totamount'];
									$dataArray['paidamt'] = $postdata['totamount'];
									$dataArray['payment_type'] = 1;								
									$to_transaction_id =  \AppService::getTransID($postdata['to_account_id']);
									$data['to_transaction_id'] = $to_transaction_id;
									$dataArray['purpose'] = 'fundtransfer_status';
									//print_R($dataArray);exit;
									$status1 = $this->walletObj->update_user_balance($dataArray);

									if ($status1)
									{
									   $bal_details = $this->walletObj->get_user_balance($dataArray['payment_type'], array('account_id'=>$postdata['to_account_id']), $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
									   if ($bal_details && count($bal_details) > 0)
										{
											$cur_balance = $bal_details->current_balance;
										}
										$credit_remark_data = addSlashes(json_encode(['data'=>['comments'=>$transfer_remarks,'from_account_name'=>$this->userSess->full_name,'from_account_code'=>$this->userSess->user_code]]));
										$dataTransArray = array(
											'account_id'=>$postdata['to_account_id'],
											'from_account_id'=>$this->userSess->account_id,
											'to_account_id'=>$postdata['to_account_id'],
											'payment_type_id'=>1,
											'currency_id'=>$postdata['currency_id'],
											'statementline_id'=>$this->config->get('stline.FUND_CREDITED_TO_ACC'),
											'amt'=>$postdata['totamount'],
											'paid_amt'=>$postdata['totamount'],
											'wallet_id'=>$ewallet_id,
											'transaction_type'=>$this->config->get('constants.TRANS_TYPE.CREDIT'),
											'ip_address'=>Request::getClientIp(true),
											'transaction_id'=>$to_transaction_id,
											'current_balance'=>$cur_balance,
											'created_on'=>date('Y-m-d H:i:s'),
											'remark'=>$credit_remark_data,
											'status'=>1);
										$tstatus1 = $this->walletObj->add_user_transaction($dataTransArray);
									}								
									$data['to_uname'] 		= $check_to_account['uname'];
									$data['to_user_code'] 	= $check_to_account['user_code'];
									$data['to_full_name'] 	= $check_to_account['fullname'];
									$data['from_user_code'] 	= $this->userSess->user_code;
									$data['transfer_remarks'] = $transfer_remarks;
									$data['amount']			= $dataArray['amount'];
									$currency 				= $this->walletObj->get_currency_name($dataArray['currency_id']);
									$data['currency'] 		= $currency[0];
									if ($status && $tstatus && $status1 && $tstatus1)
									{
										Session::put('success', trans('affiliate/wallet/fundtransfer.transfer_fund_completed'));
										CommonNotifSettings::affNotify('affiliate.fundtransfer_fromuser',$this->userSess->account_id, 0, $data,true);					
										CommonNotifSettings::affNotify('affiliate.fundtransfer_touser', $postdata['to_account_id'], 0, $data,true);									
										$op['reload'] = false;
										$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
										$op['msg'] = trans('affiliate/wallet/fundtransfer.transfer_fund_completed');
										$sess_msg = ['msg'=>trans('affiliate/wallet/fundtransfer.transfer_fund_completed'),'msgClass'=>'success'];
										Session::flash('transafer_status',$sess_msg);
										$this->session->forget('ftsess');
									}
									else
									{								
										$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
										$op['msg'] = trans('affiliate/wallet/fundtransfer.transfer_fund_failed');
									}
								}
								else
								{
									$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
									$op['msg'] = trans('affiliate/wallet/fundtransfer.transfer_fund_failed');
								}
							}
							else {
								$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
								$op['msg'] = trans('affiliate/wallet/fundtransfer.err_min_max_amt');	
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							$op['msg'] = trans('affiliate/wallet/fundtransfer.cant_transfer_amt');						
						}
					}
					else {
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = trans('affiliate/wallet/fundtransfer.insufficient_amt');	
					}
				}
				else
				{				
					$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');	
					$op['error'] = ['tac_code'=>trans('affiliate/wallet/fundtransfer.incorrect_tac_code')];
				}			
			}
		}
		else {
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');	
			$op['msg'] = trans('affiliate/wallet/fundtransfer.session_expired');	
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);   
	}
	
	
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
	   $viewdata = view('affiliate.wallet.fund_transfer_form',$data)->render();
       return $viewdata;
    }

	
	
	 
}
