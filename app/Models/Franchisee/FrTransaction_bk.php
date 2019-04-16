<?php
namespace App\Models\Franchisee;

use DB;
use App\Models\BaseModel;
use App\Models\CommonModel;
use App\Models\Franchisee\FrModel;
use App\Models\Franchisee\WalletModel;
use App\Helpers\CommonNotifSettings;
use App\Models\Commonsettings;

class FrTransaction extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->commonObj = new Commonsettings;
		$this->frObj = new FrModel;
		$this->walletObj = new WalletModel;
    }	
	
	public function checkWalletBalance ($account_id)
    {
        $result = DB::table($this->config->get('tables.WALLET').' as w')				
				->join($this->config->get('tables.ACCOUNT_BALANCE').' as ab',function($join) use ($account_id){					
					$join->on('ab.wallet_id', '=', 'w.wallet_id')						
						->where('ab.account_id', '=', $account_id)
						->where('ab.currency_id','=',$this->userSess->currency_id);
				})
				->join($this->config->get('tables.FRANCHISEE_MST').' as fr', 'fr.account_id', '=', 'ab.account_id')
				->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ab.currency_id')
                ->where('w.is_franchisee_wallet',$this->config->get('constants.ACTIVE'))
				->where('w.fr_fund_transfer_status',$this->config->get('constants.ACTIVE'))
				->where('fr.account_id', $account_id)
                ->whereRaw("ab.current_balance < fr.deposited_amount")
                ->select(DB::Raw('cur.currency as currency_code'),'ab.current_balance', 'ab.updated_on')
                ->first();
        return !empty($result) ? $result : false;
    }
	
	 public function getTransactionDetails ($arr = array())
    {
        extract($arr);
        $finalQry = '';
        $wQry2 = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs');
        if (isset($account_id))
        {
            $wQry2->where('trs.account_id', $account_id);

            if (isset($from) && !empty($from) && isset($to) && !empty($to))
            {
                $wQry2->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($from))."'");
                $wQry2->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (isset($from) && !empty($from))
            {
                $wQry2->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $wQry2->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($to))."'");
            }

            if (isset($search_term) && !empty($search_term))
            {
                $wQry2->whereRaw("trs.remark like '%$search_term%'");
            }
            if (isset($wallet_id) && !empty($wallet_id))
            {
                $wQry2->where("trs.wallet_id", $wallet_id);
            }
            if (isset($currency_id) && !empty($currency_id))
            {
                $wQry2->where("trs.currency_id", $currency_id);
            }
            if (isset($orderby) && isset($order)) {
                $wQry2->orderBy($orderby, $order);
            }
            else {
                $wQry2->orderBy('id', 'DESC');
            }
            if (isset($length) && !empty($length)) {
                $wQry2->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
                return $wQry2->count();
            }
            else
            {
                $wQry2->join($this->config->get('tables.STATEMENT_LINE').' as st', function($join)
                {
                    $join->on('st.statementline_id', '=', 'trs.statementline_id');
                    //$join->where('st.lang_id', '=', $this->config->get('app.locale_id'));
                });
                $wQry2->leftJoin($this->config->get('tables.WALLET_LANG').' as b', function($join)
                {
                    $join->on('b.wallet_id', '=', 'trs.wallet_id');
                    $join->where('b.lang_id', '=', $this->config->get('app.locale_id'));
                });
                $wQry2->leftJoin($this->config->get('tables.PAYMENT_TYPES').' as c', function($join)
                {
                    $join->on('c.payment_type_id', '=', 'trs.payment_type_id');
                });
                $wQry2->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'trs.currency_id');
                $wQry2->select(DB::Raw('trs.id,trs.statementline_id,trs.account_id,trs.created_on,trs.transaction_id,trs.amt as amount,trs.handle_amt,trs.tax,trs.paid_amt,trs.transaction_type,trs.current_balance,st.statementline,trs.remark,trs.wallet_id,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,b.wallet'));
                $transactions = $wQry2->get();
                if ($transactions){
					array_walk($transactions, function(&$t)	{
						$t->created_on = date('d M Y', strtotime($t->created_on));
						if (!empty($t->remark) && strpos($t->remark, '}') > 0) {
							$t->order_code = (isset($ordDetails->data->order_code)) ? $ordDetails->data->order_code : '';                
							$t->remark = $ordDetails = json_decode(stripslashes($t->remark));
							$t->statementline = trans('transactions.'.$t->statementline_id.'.user.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark'])));
							$t->remark = trans('transactions.'.$t->statementline_id.'.user.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
						}
						else {
							$t->remark = $t->statementline;
						}	
						$t->Fpaidamt = \CommonLib::currency_format($t->paid_amt, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);
			
						if($t->transaction_type == 1){
							$t->CR_Fpaidamt = $t->Fpaidamt;
							$t->DR_Fpaidamt = 0;
							$t->transaction_type = 'Credit';
							$t->color = 'green';
						}
						else {
							$t->CR_Fpaidamt = 0;
							$t->DR_Fpaidamt = $t->Fpaidamt;	
							$t->transaction_type = 'Debit';		
							$t->color = 'red';							
						}
						$t->Fcurrent_balance = $t->currency_symbol.' '.number_format($t->current_balance, \AppService::decimal_places($t->current_balance), '.', ',').' '.$t->currency_code;
						unset($t->statementline);
					});
					return !empty($transactions) ? $transactions : [];					
				}				
			}
        }
		return [];
    }
	
	
	/* New Fund Transfer To Account */
	public function fund_transfer_to_account (array $postdata = array())
    {  	  	
		
		$arr = $data = $email_data = [];
		$tac_check = 0;
	    $op = array(
			'status'=>'error',
			'msg'=>'null');
		$arr['account_id'] = $postdata['account_id'];		
	 	$userdetails = $this->frObj->getAccInfo($arr);
	    $data['created_on'] = $postdata['created_on'] = getGTZ(); 		
         
		$payment_type = $this->config->get('constants.PAYMENT_TYPES.WALLET');	
		$ewallet_id = $postdata['wallet_id'];
		
		$fund_trasnfer_settings = $this->walletObj->get_fund_transfer_settings(array(
					'currency_id'=>$postdata['currency_id'],
					'transfer_type'=>$this->config->get('constants.FUND_TRANSFER_TYPE.FR_TO_USER')));
	//	print_r($fund_trasnfer_settings);exit;			
		if($postdata['totamount'] >= $fund_trasnfer_settings->min_amount || $postdata['totamount'] <= $fund_trasnfer_settings->max_amount)
		{			
			$fromuser_balance = DB::table($this->config->get('tables.ACCOUNT_WALLET_BALANCE').' as ub')
					->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ub.account_id')
					->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'um.account_id')
					->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ud', 'ud.account_id', ' = ', 'ub.account_id')
					->join($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', ' = ', 'ub.account_id')
					//->join($this->config->get('tables.FRANCHISEE_MST').' as fmst', 'fmst.account_id', ' = ', 'um.account_id')
					->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
					->join($this->config->get('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
					->where('ub.account_id', $postdata['account_id'])
					->where('ub.wallet_id', $this->config->get('constants.WALLETS.VP'))
					->where('ub.currency_id', $postdata['currency_id'])
					->whereRaw('ub.current_balance >='.$postdata['totamount'])
					->select(DB::raw("ub.*,fl.level,fb.franchisee_type,um.uname, um.account_type_id, fb.wallet_purchase_per, ud.country_id as country, fl.franchisee_type as franchisee_type_name,CONCAT_WS(' ',ad.firstname,ad.lastname) as fullname,(CASE fal.access_location_type 
					WHEN ".$this->config->get('constants.FRANCHISEE_TYPE.COUNTRY')." THEN (select country from ".$this->config->get('tables.LOCATION_COUNTRY')." where country_id = fal.relation_id)
					WHEN ".$this->config->get('constants.FRANCHISEE_TYPE.REGION')." THEN (select region from ".$this->config->get('tables.LOCATION_REGIONS')."  where region_id = fal.relation_id)
					WHEN ".$this->config->get('constants.FRANCHISEE_TYPE.STATE')." THEN (select state from ".$this->config->get('tables.LOCATION_STATE')."  where state_id = fal.relation_id)
					WHEN ".$this->config->get('constants.FRANCHISEE_TYPE.DISTRICT')." THEN (select district from ".$this->config->get('tables.LOCATION_DISTRICTS')."  where district_id = fal.relation_id)
					WHEN ".$this->config->get('constants.FRANCHISEE_TYPE.CITY')." THEN (select city from ".$this->config->get('tables.LOCATION_CITY')."  where city_id = fal.relation_id)
					END) as franchisee_location"))
                    ->first();
	        if ($fromuser_balance)
            {
		        $wallet_purchase_per = $fromuser_balance->wallet_purchase_per;
				$country			 = $fromuser_balance->country;
				  
				$frbal_details = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
			
				if ($frbal_details && count($frbal_details) > 0 && $frbal_details->current_balance > 0 && $frbal_details->current_balance >= $postdata['totamount'])
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
					    $from_transaction_id = \AppService::getTransID($postdata['account_id']); 
				
				        $transdata = array(
							'from_account_id' => $postdata['account_id'],
							'to_account_id' => $postdata['to_account_id'],
							'from_user_type' =>$postdata['to_account_type_id'],
							'to_user_type' => $postdata['from_account_type_id'],
							'transaction_id' => $from_transaction_id,
							'from_user_wallet_id' => $ewallet_id,
							'to_user_wallet_id' =>$ewallet_id,
							'currency_id' => $postdata['currency_id'],
							'amount' => $postdata['totamount'],
							'paidamt' => $paidamt,
							'handleamt' =>$handleamt,										
							'transferred_on' =>$postdata['created_on'] = getGTZ(),									 
							'ip_address' => \Request::getClientIp(true),
							'status' =>$this->config->get('constants.STATUS_CONFIRMED'));
							
					    $tstatus = $fft_relation_id = $fund_id =$this->walletObj->add_transfertund_entry($transdata);     
										
						if ($fft_relation_id)
                        {	
					        $frBal = [];
							$from_cur_balance = $cur_balance = $frbal_details->current_balance;							
							$frBal['account_id'] = $postdata['account_id'];
							$frBal['wallet_id'] = $postdata['wallet_id'];
							$frBal['currency_id'] = $postdata['currency_id'];
							$frBal['transaction_type'] = $this->config->get('constants.TRANS_TYPE.DEBIT');
							$frBal['amount'] = $postdata['totamount'];							
							$frBal['payment_type'] = $payment_type;
							$frBal['purpose'] = 'fundtransfer_status';							
							$data['from_transaction_id'] = $from_transaction_id;
							$all_user_details = $userdetails;
							$frBal['return'] = 'return';	
							
							if ($frabal = $this->walletObj->update_user_balance($frBal))  // Update Franchisee			
							{					
								/* $cur_balance1 = '';
								$bal_details1 = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
								if ($bal_details1 && count($bal_details1) > 0)
								{
									$cur_balance1 = $bal_details1->current_balance;
								} */
								
								$debit_remark_data = addSlashes(json_encode(['data'=>['to'=>$postdata['to_usercode']]]));
								$frTransArray = array(
									'account_id'=>$postdata['account_id'],
									'from_account_id'=>$postdata['account_id'],
									'to_account_id'=>$postdata['to_account_id'],
									'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),
									'currency_id'=>$postdata['currency_id'],
									'statementline_id'=>$this->config->get('stline.FUND_DEBITED_FROM_ACC'),  //
									'amt'=>$frBal['amount'],
									'paid_amt'=>$paidamt,
									'handle_amt'=>$handleamt,
									'wallet_id'=>$ewallet_id,
									'transaction_type'=>$this->config->get('constants.TRANS_TYPE.DEBIT'),
									'remark'=>$debit_remark_data,
									'relation_id'=>$fft_relation_id,
									'ip_address'=>\Request::getClientIp(true),
									'transaction_id'=>$from_transaction_id,
									'created_on'=> getGTZ(),
									'current_balance'=>$frabal->current_balance,
									'status'=>$this->config->get('constants.STATUS_CONFIRMED'));	
									
								if($tstatus = $this->walletObj->add_user_transaction($frTransArray))  // Franchisee Transaction
								{
									$userBal = [];
									$userBal['account_id'] = $postdata['to_account_id'];
									$userBal['wallet_id'] = $ewallet_id;
									$userBal['currency_id'] = $postdata['currency_id'];
									$userBal['transaction_type'] = $this->config->get('constants.TRANS_TYPE.CREDIT');
									$userBal['amount'] = $paidamt;							
									$userBal['payment_type'] = $payment_type;								
									$to_transaction_id = \AppService::getTransID($postdata['to_account_id']);  
									$data['to_transaction_id'] = $to_transaction_id;
									$userBal['purpose'] = 'fundtransfer_status';
									$userBal['return'] = 'return';	
								
									if ($affbal = $this->walletObj->update_user_balance($userBal))   //   Affiliate balance Update
									{
									    /* $cur_balance = '';
									    $afbal_details = $this->walletObj->get_user_balance($userBal['payment_type'], array('account_id'=>$postdata['to_account_id']), $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
									    if ($afbal_details && count($afbal_details) > 0)
										{
											$cur_balance = $afbal_details->current_balance;
										} */
										$credit_remark_data = addSlashes(json_encode(['data'=>['from'=>$postdata['usercode']]]));
										$userTransArray = array(
											'account_id'=>$postdata['to_account_id'],
											'from_account_id'=>$postdata['account_id'],
											'to_account_id'=>$postdata['to_account_id'],
											'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),
											'currency_id'=>$postdata['currency_id'],
											'statementline_id'=>$this->config->get('stline.FUND_CREDITED_TO_ACC'),
											'amt'=>$postdata['totamount'],
											'paid_amt'=>$paidamt,
											'handle_amt'=>$handleamt,
											'wallet_id'=>$ewallet_id,
											'transaction_type'=>$this->config->get('constants.TRANS_TYPE.CREDIT'),
											'ip_address'=>\Request::getClientIp(true),
											'transaction_id'=>$to_transaction_id,
											'current_balance'=>$affbal->current_balance,
											'created_on'=> getGTZ(),
											'remark'=>$credit_remark_data,
											'relation_id'=>$fft_relation_id,
											'status'=>$this->config->get('constants.STATUS_CONFIRMED'));
										$tstatus1 = $this->walletObj->add_user_transaction($userTransArray);    //   Affiliate Transaction
										
										if ($tstatus1 && $handleamt > 0)
										{																		
											$adminBal = [];		
											$admin_id = 1;
											$adminBal['account_id'] = $admin_id;
											$adminBal['wallet_id'] = $ewallet_id;									
											$adminBal['currency_id'] = $postdata['currency_id'];
											$adminBal['transaction_type'] = $this->config->get('constants.TRANS_TYPE.CREDIT');
											$adminBal['amount'] = $handleamt;
											$adminBal['payment_type'] = $payment_type;		
											$admin_transaction_id = \AppService::getTransID($adminBal['account_id']); 
											$adminBal['purpose'] = 'fundtransfer_status';	
											$adminBal['return'] = 'return';	
																	
											if ($adBal = $this->walletObj->update_user_balance($adminBal))   // admin balance update
											{										        
												/* $cur_balance2 = '';
												$adbal_details = $this->walletObj->get_user_balance($userBal['payment_type'], array('account_id'=>$postdata['to_account_id']), $ewallet_id, $postdata['currency_id'],'fundtransfer_status');
												if ($adbal_details && count($adbal_details) > 0)
												{
													$cur_balance2 = $adbal_details->current_balance;
												} */
												$credit_remark_data = addSlashes(json_encode(['data'=>['from'=> $postdata['to_usercode'],'to'=>$postdata['usercode']]]));
												$adminTransArray = array(
													'account_id'=>$admin_id,
													'from_account_id'=>$postdata['to_account_id'],
													'to_account_id'=>$admin_id,
													'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),
													'currency_id'=>$postdata['currency_id'],
													'statementline_id'=>$this->config->get('stline.FUND_CREDITED_TO_ACC'),
													'amt'=>$handleamt,
													'paid_amt'=>$handleamt,
													'wallet_id'=>$ewallet_id,
													'transaction_type'=>$this->config->get('constants.TRANS_TYPE.CREDIT'),
													'ip_address'=>\Request::getClientIp(true),
													'transaction_id'=>$admin_transaction_id,
													'current_balance'=>$adBal->current_balance,
													'created_on'=> getGTZ(),
													'remark'=>$credit_remark_data,
													'relation_id'=>$fft_relation_id,
													'status'=>$this->config->get('constants.STATUS_CONFIRMED'));
												$tstatus2 = $this->walletObj->add_user_transaction($adminTransArray);    // admin Transaction 												
											}
										}
									}									
									/* if (!empty($wallet_purchase_per))
                                    {
									}	 */								
									$to_accountdetails       = $this->walletObj->get_userdetails_byid($postdata['to_account_id']);
									$data['from_uname']      = $postdata['from_uname'];
									$data['from_full_name']  = $postdata['from_full_name'];									
									$data['to_email'] 		 = $to_accountdetails->email;
									$data['to_uname'] 		 = $to_accountdetails->uname;
									$data['to_full_name'] 	 = $to_accountdetails->firstname.' '.$to_accountdetails->lastname;
									$data['amount']			 = $postdata['totamount']; //$dataArray['amount'];
									//$currency 				= $this->walletObj->get_currency_name($postdata['currency_id']);   // No NEED
									$data['currency'] 		 = $postdata['currency_code']; 
									$data['payment_type'] 	 = $this->walletObj->get_payout_byid($payment_type)->payment_type; 
									$data['site_name']		 = $this->siteConfig->site_name;
									if ($frabal && $tstatus && $affbal && $tstatus1)
									{
										
										/* Commission share start */
										$franchisee_details  				  	  = (Object) $postdata;
										$fund_details  				  			  = $postdata;
										$franchisee_details->franchisee_type      = $fromuser_balance->franchisee_type;
										$franchisee_details->level 			      = $fromuser_balance->level;
										$franchisee_details->wallet_purchase_per  = $fromuser_balance->wallet_purchase_per;
										$franchisee_details->franchisee_type_name = $fromuser_balance->franchisee_type_name;
										$franchisee_details->uname 				  = $fromuser_balance->uname;
										$franchisee_details->fullname   = $fromuser_balance->fullname;
										if ($fromuser_balance->level > 1)
											{
												$com_details_data['account_id'] = $franchisee_details->account_id;
												$middle_level_franchisees = $this->get_middle_level_franchisees(
														array(
															'to_franchisee_details'=>$franchisee_details,
															'to_account_id'=>$franchisee_details->account_id));
											
												if(!empty($middle_level_franchisees))
												{
													$downline_franchisee_per = $franchisee_details->wallet_purchase_per;
													foreach ($middle_level_franchisees as $franchisee)
													{
													
														$per   =  $franchisee->wallet_purchase_per - $downline_franchisee_per;
														$downline_franchisee_per = $franchisee->wallet_purchase_per;
														if ($fund_id && $per > 0)
														{
															$amount = $postdata['totamount'];
															$franchisee_commission = 0;
															$franchisee_commission = ($per/100) * ($amount);
															$franchisee_current_balance = $franchisee_tds_per = $tds_total_commission = $frans_tds_amount = $frans_tds_relation_id = 0;
															 /* $tds_details = $this->tds_balance_update(
																	array(
																		'account_id'=>$franchisee->account_id,
																		'franchisee_commission'=>$franchisee_commission,
																		'currency_id'=>$fund_details['currency_id'],
																		'country'=>$franchisee->country));  */
															//$to_user_transaction_id = $this->admincommonObj->generateTransactionID($franchisee_details->account_id);
															$current_date = getGTZ();
															$tot_tax = 0;
															$to_user_transaction_id   = \AppService::getTransID($franchisee_details->account_id);
															list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json) = $this->getTax(['account_id'=>$franchisee->account_id,'amount'=>$franchisee_commission,'country_id'=>$franchisee->country,'statementline_id'=>75]);	
															$tds_details['service_tax_details'] = $tax_json;
															$tds_details['service_tax_per'] 	= $tot_tax_perc;
															$tds_details['service_tax'] 		= $tot_tax;	
															$tds_details['tax_class_id'] 		= $tax_class_id;	
															$remark = 'Fund Transfer from System to '.$franchisee_details->uname.' - '.$franchisee_details->franchisee_type_name;
															$data = array(
																	'account_id'=>$franchisee->account_id,
																	'relation_id'=>$fft_relation_id,
																	'currency_id'=>$fund_details['currency_id'],
																	'amount'=>$amount,
																	'commission_type'=>config('constants.FRANCHISEE_COMMISSION_ADMIN_FUND_TRANS_SC'),
																	'commission_perc'=>$per,
																	'commission_amount'=>$franchisee_commission,
																	'status'=>config('constants.STATUS_CONFIRMED'),
																	'created_date'=>$current_date,
																	'confirmed_date'=>$current_date,
																	'remark'=>addSlashes(json_encode(['data'=>$remark])),
																);
															$relation_id = DB::table(config('tables.FRANCHISEE_COMMISSION'))
																	->insertGetId($data);
															if($relation_id)
															{
																$com_details_data['fr_com_id'] = $relation_id;
																$this->addFranchiseeCommissionDetails($com_details_data);
															}
															$update_balance3 = $this->update_franchisee_balance(
																			array(
																				'account_id'=>$franchisee->account_id,
																				'amount'=>$franchisee_commission,
																				'currency_id'=>$fund_details['currency_id'],
																				'transaction_type'=>config('constants.CREDIT'),
																				'ewallet_id'=>$fund_details['wallet_id']));
															if($update_balance3)
															{
																$currency_code = $this->commonObj->get_currency_code($fund_details['currency_id']);
																$decimal_places = $this->commonObj->decimal_places($amount);
																$remark = $per.'% of '.number_format($amount, $decimal_places, '.', ',').' '.$currency_code;
																$remark.=' Fund Transfer from System to '.$franchisee_details->uname.' - '.$franchisee_details->franchisee_type_name;
																$status = DB::table(config('tables.ACCOUNT_TRANSACTION'))
																		->insertGetId(
																		array(
																			'account_id'=>$franchisee->account_id,
																			'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),
																			'statementline_id'=>75,
																			'amt'=>$franchisee_commission,
																			'paid_amt'=>$franchisee_commission,
																			'handle_amt'=>0,
																			'tax'=>$tot_tax,
																			'wallet_id'=>$fund_details['wallet_id'],
																			'currency_id'=>$fund_details['currency_id'],
																			'transaction_type'=>config('constants.CREDIT'),
																			'remark'=>addSlashes(json_encode(['data'=>$remark])),
																			'from_account_id'=>$franchisee_details->account_id,
																			'ip_address'=>\Request::getClientIp(true),
																			'transaction_id'=>$transaction_id = \AppService::getTransID($franchisee_details->account_id),
																			'current_balance'=>$update_balance3['current_balance'],
																			'status'=>1,
																			'created_on'=>$current_date,
																			'updated_on'=>$current_date,
																			'relation_id'=>$relation_id
																		));
																if($status)
																{
																	$created_on = getGTZ();
																	$ip = \Request::getClientIp(true);
																	$mdata['full_name'] = $franchisee->full_name;
																	$mdata['franchisee_type'] = $franchisee_details->franchisee_type_name;
																	$mdata['franchisee_uname'] = $franchisee_details->uname;
																	$mdata['franchisee_full_name'] = $franchisee_details->fullname;
																	$mdata['uname'] 	 = $franchisee->uname;
																	$mdata['country']	 = $franchisee->country;
																	$mdata['amount'] 	 = number_format($franchisee_commission, 2, '.', ',');
																	$mdata['currency'] 	 = $this->commonObj->get_currency_name($fund_details['currency_id']);
																	$mdata['transaction_id'] = $transaction_id;
																	$mdata['created_on'] = $created_on;
																	$mdata['country'] 	 = $this->commonObj->getCountryName($franchisee->country);
																	$mdata['ip'] 		 = $ip;
																	
																	/************* Notification  code *****************/
																	/* $this->commonObj->addNotification([
																		'access_ids'=>$franchisee->account_id,
																		'statementline_id'=>78,
																		'details'=>[
																			'description'=>'You received commission amount - '.$mdata['amount'].' '.$mdata['currency'].' from '.$franchisee_details->uname,
																			'url'=>'franchisee/fundtransfer-commission'
																		]
																	]);
																	 new MailerLib(
																			array(
																		'to'=>$franchisee->email,
																		'subject'=>$fund_details['pagesettings']->site_name.' - Commission Received :: '.$transaction_id.', Amount - '.$mdata['amount'].' '.$mdata['currency'],
																		'html'=>View::make('emails.admin_add_fund_franchisee_commission', $mdata)->render(),
																		'from'=>config('constants.SYSTEM_MAIL_ID'),
																		'fromname'=>config('constants.DOMAIN_NAME')
																	)); */
																	
																}
																
															}
														}
													}
												}
											}
											/* Commission share end */
										CommonNotifSettings::affNotify('franchisee.fundtransfer_fromuser',$postdata['account_id'], $postdata['from_account_type_id'], $data,true,false);
										CommonNotifSettings::affNotify('franchisee.fundtransfer_touser', $to_accountdetails->account_id, $postdata['to_account_type_id'], $data,true,false);
										$op['reload'] = true;
										$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
										$op['msg'] = trans('franchisee/wallet/fundtransfer.transfer_fund_completed'); 
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
					$op['msg'] = trans('franchisee/wallet/fundtransfer.insufficient_amt');	
				}
		    }
			else {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('franchisee/wallet/fundtransfer.insufficient_amt');	
			}
		}
		else {
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = trans('franchisee/wallet/fundtransfer.err_min_max_amt');	
		}
		return $op;
	}
	
	/** top level commissions */
	public function get_middle_level_franchisees ($arr = array())
    {
        $middle_level_franchisees = array();
        extract($arr);
		
        $to_franchisee_location = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION'))
                ->where('account_id', $to_account_id)
                ->where('status', config('constants.ACTIVE'))
                ->where('access_location_type', $to_franchisee_details->franchisee_type)
                ->pluck('relation_id');
		$to_franchisee_location = $to_franchisee_location[0];
	    switch ($to_franchisee_details->franchisee_type)
        {
            case config('constants.FRANCHISEE_TYPE.REGION'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_REGIONS').' as rl')
                        ->join(config('tables.LOCATION_STATE').' as s', 's.region_id', '=', 'rl.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('rl.region_id, rl.region, s.state_id,s.state as state_name,co.country_id,co.country as country_name'))
                        ->where('rl.region_id', $to_franchisee_location)
                        ->first();
                break;
            case config('constants.FRANCHISEE_TYPE.CITY'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_CITY').' as c')
                        ->join(config('tables.LOCATION_DISTRICTS').' as d', 'd.district_id', '=', 'c.district_id')
                        ->join(config('tables.LOCATION_STATE').' as s', 's.state_id', '=', 'd.state_id')
                        ->leftjoin(config('tables.LOCATION_REGIONS').' as rl', 'rl.region_id', '=', 's.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('c.city_id,c.city_name,d.district_id,d.district as district_name,s.state_id,(select GROUP_CONCAT(state_id) from '.config('tables.LOCATION_STATE').'  where is_union_territory = 1 and linked_state_id = d.state_id and status = 1 GROUP BY linked_state_id) as  union_territory_id, s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region'))
                        ->where('c.city_id', $to_franchisee_location)
                        ->first();
                break;
            case config('constants.FRANCHISEE_TYPE.DISTRICT'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_DISTRICTS').' as d')
                        ->join(config('tables.LOCATION_STATE').' as s', 's.state_id', '=', 'd.state_id')
                        ->leftjoin(config('tables.LOCATION_REGIONS').' as rl', 'rl.region_id', '=', 's.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('d.district_id,d.district as district_name,s.state_id,(select GROUP_CONCAT(state_id) from '.config('tables.LOCATION_STATE').' where is_union_territory = 1 and linked_state_id = d.state_id and status = 1 GROUP BY linked_state_id) as  union_territory_id, s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region'))
                        ->where('d.district_id', $to_franchisee_location)
                        ->first();
                break;
            case config('constants.FRANCHISEE_TYPE.STATE'):                
                $franchisee_location_details = DB::table(config('tables.LOCATION_STATE').' as s')
                        ->leftjoin(config('tables.LOCATION_REGIONS').' as rl', 'rl.region_id', '=', 's.region_id')
                        ->join(config('tables.LOCATION_COUNTRY').' as co', 'co.country_id', '=', 's.country_id')
                        ->select(DB::raw('s.state_id,s.state as state_name,co.country_id,co.country as country_name, rl.region_id, rl.region as region'))
                        ->where('s.state_id', $to_franchisee_location)
                        ->first();
		        break;
            case config('constants.FRANCHISEE_TYPE.COUNTRY'):
                $franchisee_location_details = DB::table(config('tables.LOCATION_COUNTRY'))
                        ->select(DB::raw('country_id,country as country_name'))
                        ->where('country_id', $to_franchisee_location)
                        ->first();
                break;
        }		
		
        if (isset($franchisee_location_details->city_id))
        {
            $city_id = $franchisee_location_details->city_id;
        }
        if (isset($franchisee_location_details->district_id))
        {
            $district_id = $franchisee_location_details->district_id;
        }
        if (isset($franchisee_location_details->state_id))
        {
            $state_id = $franchisee_location_details->state_id;
        }
        if (isset($franchisee_location_details->union_territory_id))
        {
            $union_territory_id = $franchisee_location_details->union_territory_id;
        }
        if (isset($franchisee_location_details->country_id))
        {
            $country_id = $franchisee_location_details->country_id;
        }
        if (isset($franchisee_location_details->region_id) && !empty($franchisee_location_details->region_id))
        {
            $region_id = $franchisee_location_details->region_id;
        }		
        switch ($to_franchisee_details->franchisee_type)
        {
            case config('constants.FRANCHISEE_TYPE.CITY'):
				if(!empty($city_id)){
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.CITY'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $city_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fl.franchisee_type,fb.wallet_purchase_per,up.country_id as country')
                        ->first();
				}
            case config('constants.FRANCHISEE_TYPE.DISTRICT'):
				if(!empty($district_id)){
					
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.DISTRICT'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $district_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fl.franchisee_type,fb.wallet_purchase_per,up.country_id as country')
                        ->first();
					
				}		
            case config('constants.FRANCHISEE_TYPE.STATE'):
				if(!empty($state_id)){
			    $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.STATE'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->whereRaw("fal.relation_id LIKE '%".$state_id."%'")
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fl.franchisee_type,fb.wallet_purchase_per,ap.country_id as country')
                        ->first();
				}	
            case config('constants.FRANCHISEE_TYPE.REGION'):
				if(!empty($region_id)){
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.REGION'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $region_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fl.franchisee_type,fb.wallet_purchase_per,ap.country_id as country')
                        ->first();
				}		
            case config('constants.FRANCHISEE_TYPE.COUNTRY'):
				if(!empty($country_id)){
                $middle_level_franchisees[] = DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                        ->join(config('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                        ->join(config('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                        ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', ' = ', 'fal.account_id')
                        ->join(config('tables.FRANCHISEE_PACKAGE').' as fp', 'fp.franchisee_type', ' = ', 'fal.access_location_type')
                        ->where('fal.access_location_type', config('constants.FRANCHISEE_TYPE.COUNTRY'))
                        ->where('fal.status', config('constants.ACTIVE'))
                        ->where('fal.relation_id', $country_id)
                        ->where('fl.level', '<', $to_franchisee_details->level)
                        ->selectRaw('fal.account_id,concat(ud.firstname," ",ud.lastname) as full_name,um.uname,um.email,fl.franchisee_type,fb.wallet_purchase_per,ap.country_id as country')
                        ->first();
				}		
        }
		//print_R($middle_level_franchisees);exit;
        return array_filter($middle_level_franchisees);
    }
	
	public function add_fund_franchisee(array $arr = array())
    {
		$fund_details = $arr;
        $user_details = $this->memberObj->get_member_details($arr);
        $bal 		  = $this->get_account_bal($arr);
		$purchase_commission = 0;
        if (($arr['type'] == $this->config->get('constants.TRANS_TYPE.DEBIT')) && (!empty($bal)) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if (!empty($user_details))
        {
		    $accId = $user_details->account_id;
			$franchisee_details = $this->fr_benefits($accId);
			//print_R($franchisee_details);exit;
		
			if(!empty($fr_commissions)){
				$purchase_commission  = $commission_paidamt = ($arr['amount'] / 100) * $franchisee_details->wallet_purchase_per;
			}
            $fund['added_by'] 		= $arr['admin_id'];
            $fund['transaction_id'] = \AppService::getTransID($franchisee_details->account_id);
			
            $fund['currency_id'] 	= $arr['currency_id'];
            $fund['amount'] 		= $arr['amount'];
            $fund['paidamt'] 		= $arr['amount'];
            $fund['handleamt']  	= 0;
            if($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $accId;
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
            //$fund['transfer_type'] = $trasn_type;
            $fund['created_on']    = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['status'] 	   = $this->config->get('constants.ON');
			$fund_id 			   = DB::table($this->config->get('tables.FUND_TRANASFER'))
										->insertGetId($fund);
            if(!empty($fund_id))
            {
				$fft_relation_id = $fund_id;
                if ($arr['type'] == $this->config->get('constants.CREDIT'))
                {
					
                    $update_trans = $this->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'],'credit_remark_data'=>['amount'=>$arr['amount']], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.DEBIT'))
                {
                    $update_trans = $this->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM','debit_remark_data'=>['amount'=>$arr['amount']]], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.TRANS_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
					/* share commission to uplines */
					
					
		        }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }
	
	public function fr_benefits($account_id){
		$qry =  DB::table(config('tables.FRANCHISEE_MST').' as mst')
				->join(config('tables.FRANCHISEE_BENIFITS').' as b','b.franchisee_type','=','mst.franchisee_type')
				->join(config('tables.ACCOUNT_MST').' as ac','ac.account_id','=','mst.account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','mst.account_id')
				->join(config('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'mst.account_id')
				->join(config('tables.FRANCHISEE_LOOKUP').' as l','l.franchisee_typeid','=','mst.franchisee_type')
				->where('mst.account_id',$account_id)
				->select('mst.account_id','b.diff_commission_per','b.flexible_commission_per','b.wallet_purchase_per','b.charity_donation_per','b.fb_id','l.level','ac.uname','b.franchisee_type','l.franchisee_type as franchisee_type_name',DB::raw("CONCAT_WS(' ',ad.firstname,ad.lastname) as fullname"),
				DB::raw("CASE fal.access_location_type
					WHEN ".config('constants.FRANCHISEE_TYPE.COUNTRY')." THEN (select country as name from ".config('tables.LOCATION_COUNTRY')." where country_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.REGION')." THEN (select region as region_name from ".config('tables.LOCATION_REGIONS')."  where region_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.STATE')." THEN (select state as name from ".config('tables.LOCATION_STATE')."  where state_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.DISTRICT')." THEN (select district as district_name from ".config('tables.LOCATION_DISTRICTS')."  where district_id = fal.relation_id)
					WHEN ".config('constants.FRANCHISEE_TYPE.CITY')." THEN (select city as city_name from ".config('tables.LOCATION_CITY')."  where city_id = fal.relation_id)
					END as franchisee_location")
				);
			$result = $qry->first();
			return $result;
	}
	
	public function addFranchiseeCommissionDetails ($arr = array())
    {
		
        $country_id = $state_id = $district_id = $region_id = $city_id = null;
        extract($arr);
        $data = compact('country_id', 'state_id', 'district_id', 'region_id', 'city_id');
	    if (empty(array_filter($data)) && isset($account_id))
        {
           //$userdetails = $this->get_userdetails_byid($user_id);
            $userdetails = $this->get_access_locations($arr);
            if (!empty($userdetails))
            {
                if (!empty($userdetails->country))
                    $data['country_id']  = $userdetails->country;
                if (!empty($userdetails->state_id))
                    $data['state_id'] 	 = $userdetails->state_id;
                if (!empty($userdetails->district_id))
                    $data['district_id'] = $userdetails->district_id;
                if (!empty($userdetails->region_id))
                    $data['region_id']   = $userdetails->region_id;
                if (!empty($userdetails->city_id))
                    $data['city_id'] 	 = $userdetails->city_id;
            }
        }
        if (!empty(array_filter($data)))
        {
            if (DB::table(config('tables.FRANCHISEE_COMMISSION_DETAILS'))
                            ->where('fr_com_id', $fr_com_id)
                            ->count() > 0)
            {
                return DB::table(config('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->where('fr_com_id', $fr_com_id)
                                ->update($data);
            }
            else
            {
                $data['fr_com_id'] = $fr_com_id;
                return DB::table(config('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->insertGetID($data);
            }
        }
    }
	public function get_access_locations($arr){
		extract($arr);
		return DB::table(config('tables.FRANCHISEE_ACCESS_LOCATION'),' as al')
				->where('account_id',$account_id)
				->where('relation_id',$fr_com_id)
				->where('status',1)
				->first();
				
	}
	
	public function update_franchisee_balance ($arrData = '')
    {
        extract($arrData);
        if ($arrData)
        {
            $tot_credit = $tot_debit = $current_balance = 0;
            $franchisee_balance = $this->commonObj->get_user_balance($account_id, $ewallet_id, $currency_id);
            if ($franchisee_balance)
            {
                $tot_credit = $franchisee_balance->tot_credit;
                $tot_debit = $franchisee_balance->tot_debit;
                $current_balance = $franchisee_balance->current_balance;
            }
            if ($transaction_type == config('constants.CREDIT'))
            {
                $data['tot_credit'] = $tot_credit + $amount;
                $data['tot_debit'] = $tot_debit;
                $data['current_balance'] = $current_balance + $amount;
            }
            elseif ($transaction_type == config('constants.DEBIT'))
            {
                $data['tot_credit'] = $tot_credit;
                $data['tot_debit'] = $tot_debit + $amount;
                $data['current_balance'] = $current_balance - $amount;
            }
            if ($franchisee_balance)
            {
                $status = DB::table(config('tables.ACCOUNT_BALANCE'))
                        ->where('balance_id', $franchisee_balance->balance_id)
                        ->update($data);
            }
            else
            {
                $status = DB::table(config('tables.ACCOUNT_BALANCE'))
                        ->insertGetID(
                        array(
                            'ewallet_id'=>$ewallet_id,
                            'currency_id'=>$currency_id,
                            'account_id'=>$account_id,
                            'tot_credit'=>$data['tot_credit'],
                            'tot_debit'=>$data['tot_debit'],
                            'current_balance'=>$data['current_balance'],
                            'updated_date'=>getGTZ()
                ));
            }
            return $data;
        }
        return false;
    }
	
	
	
		
}