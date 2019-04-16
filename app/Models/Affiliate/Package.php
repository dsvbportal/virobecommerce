<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use App\Models\Affiliate\Payments;
use DB;
use AppService;
use CommonLib;
use Log;

class Package extends BaseModel
{

    private $paymentObj = '';

    public function __construct()
    {
        parent::__construct();
        $this->paymentObj = new Payments;
        $this->affObj = new AffModel();
        $this->walletObj = new Wallet;
		$this->bonusObj = new Bonus;
        $this->transactionObj = new Transaction;
		$this->amObj = new AddMoney;
    }
   
    public function get_packages ($arr = array())
    {
        $pkg = DB::table($this->config->get('tables.AFF_PACKAGE_MST').' as pm')
                ->join($this->config->get('tables.AFF_PACKAGE_PRICING').' as pp', 'pp.package_id', '=', 'pm.package_id')
                ->join($this->config->get('tables.CURRENCIES').' as cur', function($join)
                {
                    $join->on('cur.currency_id', '=', 'pp.currency_id');
                })
                ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery) use($arr)
				{
					$subquery->on('pm.package_id', '=', 'pl.package_id')
					->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
				});

        if (isset($arr['package_level']) && !empty($arr['package_level']))
        {
            $pkg = $pkg->where('pm.package_level', '>', $arr['package_level']);
        }

        if (isset($arr['package_id']) && !empty($arr['package_id']))
        {
            $pkg = $pkg->where('pm.package_id', '=', $arr['package_id']);
        } 
		else if (isset($arr['package_code']) && !empty($arr['package_code']))
        {
            $pkg = $pkg->where('pm.package_code', '=', $arr['package_code']);
        }

        $pkg = $pkg->where('pp.currency_id', $arr['currency_id'])
                        ->where('pm.status', $this->config->get('constants.ON'))
                        ->where('pm.is_deleted', $this->config->get('constants.OFF'))
                        ->where('pm.is_adjustment_package', $this->config->get('constants.OFF'))
                        ->select(DB::RAW('pm.package_id,pm.package_code	,pm.package_level,pm.is_refundable,pm.refundable_days,pm.expire_days ,pm.package_image,pm.is_upgradable, pm.is_adjustment_package,pm.instant_benefit_credit,cur.currency_id, pp.price, pp.package_qv,pp.weekly_capping_qv, pp.shopping_points_cashback, pp.shopping_points_bonus, cur.currency as currency_code, pl.package_name, pl.description'))
						->orderBy('pm.package_level')->get();

        if (!empty($pkg))
        {
            array_walk($pkg, function(&$package)
            {  
                $package->package_image_url = asset($this->config->get('constants.PACKAGE_IMG').$package->package_image);
			    $package->price  = $package->price;
				$package->fprice  = number_format($package->price , \AppService::decimal_places($package->price ), '.', ',');
            });			
            return (isset($arr['list']) && !$arr['list']) ? $pkg[0] : $pkg;
        }
        return false;
    }

    public function get_mypackage ($arr = array())
    {
        $pkgSql = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION').' as usub')
                        ->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'usub.package_id', '=', 'pm.package_id')
                        ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usub.currency_id')
                        ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)use($arr)
                        {
                            $subquery->on('pm.package_id', '=', 'pl.package_id')
                            ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                        })
						->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'usub.payment_type')
                        ->where('usub.account_id', $arr['account_id'])
                        ->where('usub.is_deleted', $this->config->get('constants.OFF'))
                      //  ->where('usub.status',$this->config->get('constants.ON'))
                        ->select(DB::RAW('usub.currency_id,cur.currency as currency_code, pl.package_name,usub.purchase_code,pm.package_image,usub.amount,usub.paid_amt,usub.handle_amt,usub.paid_amt,usub.purchased_date,usub.is_refundable,usub.subscribe_id,usub.confirm_date,usub.refund_expire_on,usub.package_qv,usub.weekly_capping_qv,usub.is_upgradable,usub.package_level,usub.status,usub.transaction_id,pt.payment_type'))->latest('usub.purchased_date');
        if (isset($arr['count']) && !empty($arr['count']))
        { 
            return $pkgSql->count();
        }
        else
        {
            $pkg = $pkgSql->get();
        }
        if (!empty($pkg))
        {
            array_walk($pkg, function(&$package)
            {
                $package->package_image_url = asset($this->config->get('constants.PACKAGE_IMG').$package->package_image);
                $package->paid_amt = CommonLib::currency_format($package->paid_amt, $package->currency_id);
                $package->refund_expire_on = !empty($package->refund_expire_on) ? showUTZ('M d, Y', $package->refund_expire_on) : '';
                $package->purchased_date = !empty($package->purchased_date) ? showUTZ($package->purchased_date,'M d, Y') : '';
				$package->confirm_date = !empty($package->confirm_date) ? showUTZ($package->confirm_date,'M d, Y') : '';
				switch ($package->status)
                    {
					case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED'):
						$package->status_label = 'Confirmed';
						$package->status_class = 'success';
					break;
					case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CANCELLED'):
						$package->status_label = 'Cancelled';
						$package->status_class = 'danger';
					break;
					case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.EXPIRED'):
						$package->status_label = 'Expired';
						$package->status_class = 'danger';
					break;
					case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.PENDING'):
						$package->status_label = 'Pending';
						$package->status_class = 'warning';
					break;
					case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.WAIT_FOR_ACTIVATE'):
						$package->status_label = 'Pending for Approvals';
						$package->status_class = 'warning';
					break;
					case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.USER_APPROVALS'):
						$package->status_label = 'Wait for Approvals';
						$package->status_class = 'warning';						
					break;
				}
            });
            return $pkg;
        }
        return false;
    }

    public function upgrade_history ($arr = array())
    {
        extract($arr);
        $pkgSql = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usubtop')
                ->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'usubtop.package_id', '=', 'pm.package_id')
                ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
                {
                    $subquery->on('pm.package_id', '=', 'pl.package_id')
                    ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usubtop.currency_id')
				->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'usubtop.payment_type')
				->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'usubtop.account_id')			
				->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'usubtop.account_id')
				->join($this->config->get('tables.ACCOUNT_TREE').' as at', 'at.account_id', '=', 'usubtop.account_id')
                ->where('usubtop.account_id', $account_id)
                ->where('usubtop.is_deleted', $this->config->get('constants.OFF'))
                ->select(DB::RAW('usubtop.currency_id,usubtop.subscribe_topup_id,usubtop.purchase_code,cur.currency as currency_code,pl.package_name,pm.package_image,usubtop.amount,usubtop.handle_amt,usubtop.paid_amt,usubtop.create_date,usubtop.is_refundable,usubtop.subscribe_id,usubtop.refund_expire_on,usubtop.package_qv,usubtop.weekly_capping_qv,usubtop.shopping_points_cashback,usubtop.shopping_points_bonus,usubtop.package_level,usubtop.updated_date,usubtop.status,usubtop.transaction_id,pt.payment_type,am.user_code,concat_ws(\' \',ad.firstname,ad.lastname) as fullname,at.recent_package_purchased_on'))
                ->latest('usubtop.subscribe_topup_id');
                 
        if (isset($subscrib_id) && $subscrib_id > 0)
        {
            $pkgSql->where('usubtop.subscrib_id', $subscrib_id);
        }

        if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {
            $wQry2->whereDate("usubtop.created_on", ">=", getUTZ('Y-m-d', $from));
            $wQry2->whereDate("usubtop.created_on", "<=", getUTZ('Y-m-d', $to));
        }
        else if (isset($from) && !empty($from))
        {
            $wQry2->whereDate('usubtop.created_on', '<=', getUTZ('Y-m-d', $from));
        }
        else if (!empty($to) && isset($to))
        {
            $wQry2->whereDate("usubtop.created_on", ">=", getUTZ('Y-m-d', $to));
        }

        if (isset($orderby) && isset($order))
        {
            $pkgSql->orderBy($orderby, $order);
        }
        if (isset($length) && !empty($length))
        {
            $pkgSql->skip($start)->take($length);
        }

        if (isset($count) && !empty($count))
        {
            return $pkgSql->count();
        }
        else
        {
            $pkg = $pkgSql->get();
        }

        if (!empty($pkg))
        {

            return $pkg;
        }
        return false;
    }

    public function purchase_paymodes ()
    {
        $modes = $this->paymentObj->get_paymodes(['purpose'=>$this->config->get('constants.PAYMODE_PURPOSE_BUYPACKAGE')]);
        return ($modes) ? $modes : NULL;
    }

    public function doPurchase ($postdata = array())
    {
        $proceed = true;
        $package_level = 0;
        $op = ['status'=>422];
        if (isset($postdata['userSess']) && !empty($postdata['userSess']) && !empty($postdata['userSess']->account_id) && is_numeric($postdata['userSess']->account_id))
        {
            $userSess = $postdata['userSess'];
            if (isset($postdata['pack_details']) && !empty($postdata['pack_details']) && !empty($postdata['paymode']))
            {
                $pack_details = '';
                if (isset($postdata['pack_details']))
                {
                    $pack_details = $postdata['pack_details'];
                }
                else
                {
                    $pack_details = $this->get_packages([
                        'list'=>false,
                        'package_id'=>$postdata['package_id'],
                        'currency_id'=>$userSess->currency_id]);
                }
                if ($pack_details)
                {

                    $payout = $this->paymentObj->get_paymodes(['purpose'=>$this->config->get('constants.PAYMODE_PURPOSE_BUYPACKAGE'), 'payment_type_id'=>$postdata['paymode'], 'list'=>false]);

                    if (!empty($payout) && $payout->check_kyc_status)
                    {
                        $payout->kyc_settings = json_decode($payout->kyc_settings);
                        if ($payout->kyc_settings->currency == $userSess->currency_id && $pack_details->price >= $payout->kyc_settings->amount)
                        {
                            $op['msg'] = trans('affiliate/package/purchase.validate.kyc_required');
                            $op['msgtype'] = 'warning';
                            $proceed = false;
                        }
                    }
                    if ($proceed)
                    {
                        $paymet_gateway_id = $postdata['paymode'];
                        $currency_id = $userSess->currency_id;
                        $package_level = $pack_details->package_level;

                        $sbdata = [
                            'payment_gatway'=>$paymet_gateway_id,
                            'transaction_id'=>AppService::getTransID($userSess->account_id),
                            'order_type'=>$this->config->get('constants.PACKAGE_NEW'),
                            'pack_details'=>$pack_details,
                            'userSess'=>$userSess];

                        if ($paymet_gateway_id == $this->config->get('constants.PAYMENT_TYPES.WALLET'))
                        {
                            $sbdata['pg_relation_id'] = $this->config->get('constants.WALLETS.VP');
                            $op = $this->add_subscription_topup($sbdata);
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');							
                        } 
						else {
							$sbdata['pg_relation_id'] = 0;
							$subTopupId =45;
							
							$afData = [];
							$afData['purpose'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE');
							$afData['payment_type_id'] = $paymet_gateway_id;
							$afData['transaction_id'] = AppService::getTransID($userSess->account_id);						
							$afData['account_id'] = $this->userSess->account_id;						
							$afData['currency_id'] = $pack_details->currency_id;
							$afData['amount'] = $pack_details->price;
							$afData['paid_amt'] = $pack_details->price;
							$afData['exch_rate'] = 1;
							$afData['wallet_id'] = $this->config->get('constants.WALLETS.VP');
							$afData['settled_currency_id'] = $pack_details->currency_id;
							$afData['settled_amount'] = $pack_details->price;
							$afData['relation_id'] = $subTopupId;
							$afData['remark'] = addslashes(json_encode(['package'=>$pack_details->package_name]));
							$afData['payment_status'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PAYMENT_STATUS.PENDING');
							$afData['status'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.PENDING');									
							$afId = $this->amObj->saveAddMoney($afData);
							
							if($afId){
								$op['gateway_info'] = $this->paymentObj->getGateWayInfo($paymet_gateway_id, [
									'amount'=>$pack_details->price,
									'firstname'=>$this->userSess->firstname,
									'lastname'=>$this->userSess->lastname,
									'mobile'=>$this->userSess->mobile,
									'email'=>$this->userSess->email,
									'account_id'=>$this->userSess->account_id,
									'account_log_id'=>$this->userSess->account_log_id,
									'payment_mode'=>$paymet_gateway_id,
									'payment_type'=>$paymet_gateway_id,
									'purpose' => 'PACKAGE-PURCHASE',
									'id'=>$afId,
									'ip'=>$this->request->getClientIP(),
									'currency_id'=>$this->userSess->currency_id,
									'card_id'=>$this->request->has('id') ? $this->request->id : null,
									'remark'=>trans('general.package_purchase_remarks', ['package'=>$pack_details->package_name, 'amount'=>CommonLib::currency_format($pack_details->price, $pack_details->currency_id)])]);
								
								$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							}
						}
                    }
                }
                else
                {
                    $op['msg'] = trans('affiliate/package/purchase.validate.packmissing');
                    $op['msgtype'] = 'danger';
                }
            }
            else
            {
                $op['msg'] = trans('affiliate/package/purchase.validate.packinvalide');
                $op['msgtype'] = 'danger';
            }
        }
        else
        {
            $op['msg'] = trans('affiliate/package/purchase.validate.sess_exp');
            $op['msgtype'] = 'danger';
        }
        return $op;
    }

    public function add_subscription_topup ($postdata = array())
    {
        $op = ['status'=>$this->config->get('httperr.UN_PROCESSABLE')];
        if ($postdata)
        {
            $subscribe_id = 0;
            $pack_details = $postdata['pack_details'];
            $userSess = $postdata['userSess'];
            $payment_gatway = $postdata['payment_gatway'];
            $current_date = getGTZ();
            $transaction_id = AppService::getTransID($userSess->account_id);
            $purchase_currency = $userSess->currency_code;

            $tpData = [
                'account_id'=>$userSess->account_id,
                'package_id'=>$pack_details->package_id,
                'package_level'=>$pack_details->package_level,
                'transaction_id'=>$transaction_id,
                'order_type'=>$postdata['order_type'],
                'payment_type'=>$payment_gatway,
                'currency_id'=>$pack_details->currency_id,
                'pg_relational_id'=>$postdata['pg_relation_id'],
                'amount'=>$pack_details->price,
                'handle_amt'=>0,
                'paid_amt'=>$pack_details->price,
                'package_qv'=>$pack_details->package_qv,
                'weekly_capping_qv'=>$pack_details->weekly_capping_qv,
                'shopping_points_cashback'=>$pack_details->shopping_points_cashback,
                'shopping_points_bonus'=>$pack_details->shopping_points_bonus,
                'is_adjustment_package'=>$pack_details->is_adjustment_package,
                'is_upgradable'=>$pack_details->is_upgradable,
                'is_refundable'=>$pack_details->is_refundable,
                'refundable_days'=>$pack_details->refundable_days,
                'refund_expire_on'=> date($this->config->get('constants.DB_DATE_FORMAT'), strtotime($current_date."+".$pack_details->refundable_days." ".$this->config->get('constants.REFUNDABLE_PERIODE_IN'))),
                'expire_days'=>$pack_details->expire_days,
                'create_date'=>getGTZ(),
                'status'=>$this->config->get('constants.PACKAGE_PURCHASE_STATUS_PENDING'),
                'payment_status'=>$this->config->get('constants.PAYMENT_UNPAID')];

			$subscribe_topup_id = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
					->insertGetId($tpData);	
			
			$purchase_code = $this->config->get('constants.SUBSCRIBE_CODE_PREFIX').date('ym').$subscribe_topup_id; 
			$stdata['purchase_code'] = $purchase_code;						
			$stdata['updated_date'] = getGTZ();
			
			$tupRes = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
                                ->where('subscribe_topup_id', $subscribe_topup_id)
                                ->update($stdata);
			
			
			
            if ($payment_gatway == $this->config->get('constants.PAYMENT_TYPES.WALLET'))
            {                
				$postdata['wallet_id'] = $postdata['pg_relation_id'];
                $postdata['transaction_id'] = $postdata['transaction_id'];

                if (isset($data['usrbal_Info']))
                {
                    $usrbal_Info = $data['usrbal_Info'];
                }
                else
                {
                    $usrbal_Info = $this->walletObj->account_balance(['account_id'=>$userSess->account_id, 'currency_id'=>$userSess->currency_id, 'wallet_id'=>$postdata['wallet_id']]);
                }

                if ($usrbal_Info)
                {
                    if ($usrbal_Info->current_balance >= $pack_details->price)
                    {                        
						$avail_balance = $usrbal_Info->current_balance;

						$usrbal_Info = $this->walletObj->update_account_balance(array('wallet_id'=>$postdata['pg_relation_id'], 'account_id'=>$userSess->account_id, 'currency_id'=>$userSess->currency_id, 'amount'=>$pack_details->price, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.DEBIT'), 'return'=>'current'));
						$purchase_code = $this->config->get('constants.SUBSCRIBE_CODE_PREFIX').date('ym').$subscribe_topup_id;                        
						
						$ptData = '';
                        $ptData['status'] = $this->config->get('constants.PACKAGE_PURCHASE_STATUS_WAITFOR_USER_ACTIVATE');
                        $ptData['payment_status'] = $this->config->get('constants.PAYMENT_PAID');						
						$ptData['updated_date'] = getGTZ();
						
						$tupRes = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
                                ->where('subscribe_topup_id', $subscribe_topup_id)
                                ->update($ptData);
						
						$trans = '';
                        $trans['account_id'] = $userSess->account_id;
						$trans['from_account_id'] = $userSess->account_id;
                        $trans['statementline_id'] = 68;
                        $trans['payment_type_id'] = $postdata['payment_gatway'];
                        $trans['amt'] = $pack_details->price;
                        $trans['handle_amt'] = 0;
                        $trans['paid_amt'] = $pack_details->price;
                        $trans['currency_id'] = $userSess->currency_id;
                        $trans['wallet_id'] = $postdata['wallet_id'];
                        $trans['transaction_id'] = $postdata['transaction_id'];
                        $trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                        $trans['relation_id'] = $subscribe_topup_id;
                        $trans['remark'] = addslashes(json_encode(['data'=>['code'=>$purchase_code,'package'=>$pack_details->package_name]]));
                        $trans['created_on'] = getGTZ();
                        $trans['current_balance'] = $usrbal_Info->current_balance;
                        $trans['status'] = $this->config->get('constants.ACTIVE');

                        $transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
                                ->insertGetId($trans);
						
						$postdata['status'] = $this->config->get('constants.PACKAGE_PURCHASE_STATUS_WAITFOR_USER_ACTIVATE');
						$postdata['payment_status'] = $this->config->get('constants.PAYMENT_PAID');
					    $postdata['purchase_code'] = $purchase_code;
                        $postdata['subscribe_topup_id'] = $subscribe_topup_id;

                        return $this->save_subscription($postdata);
                    }
                    else
                    {
                        $op['msg'] = trans('affiliate/package/purchase.validate.walletbal_insufficient');
                        $op['msgtype'] = 'danger';
                    }
                }
                else
                {
                    $op['msg'] = trans('affiliate/package/purchase.validate.walletmissing');
                    $op['msgtype'] = 'danger';
                }
            }
			else 
            {
				return $this->getTopupPackageInfo($purchase_code);
			}            
        }
        return $op;
    }

    public function save_subscription ($postdata = array())
    {
		$op = ['status'=>$this->config->get('httperr.UN_PROCESSABLE')];
        if ($postdata && !empty($postdata['subscribe_topup_id']))
        {

            if (!empty($postdata['pack_details']) && !empty($postdata['userSess']))
            {
                $userSess = $postdata['userSess'];
                $pack_details = $postdata['pack_details'];


                $existCnt = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION'))
                        ->where('purchase_code', $postdata['purchase_code'])
                        ->count();

                if ($existCnt == 0)
                {
					$current_date = getGTZ();
					$expire_date = date($this->config->get('constants.DB_DATE_FORMAT'), strtotime($current_date." +".$pack_details->expire_days." days"));

					$scData = [];
					$scData['purchase_code'] = $postdata['purchase_code'];
					$scData['account_id'] = $userSess->account_id;
					$scData['package_id'] = $pack_details->package_id;
					$scData['package_level'] = $pack_details->package_level;
					$scData['transaction_id'] = $postdata['transaction_id'];
					$scData['payment_type'] = $postdata['payment_gatway'];
					$scData['pg_relation_id'] = $postdata['pg_relation_id'];
					$scData['currency_id'] = $userSess->currency_id;
					$scData['amount'] = $pack_details->price;
					$scData['handle_amt'] = 0;
					$scData['paid_amt'] = $pack_details->price;
					$scData['is_adjustment_package'] = $pack_details->is_adjustment_package;
					$scData['is_upgradable'] = $pack_details->is_upgradable;

					if ($pack_details->instant_benefit_credit == $this->config->get('constants.ON') && $pack_details->is_refundable == $this->config->get('constants.OFF'))
					{
						$scData['package_qv'] = $pack_details->package_qv;
						$scData['weekly_capping_qv'] = $pack_details->weekly_capping_qv;
						$scData['confirm_date'] = getGTZ();
					}                            

					$scData['status'] = $postdata['status'];
					$scData['payment_status'] = $postdata['payment_status'];
					$scData['purchased_date'] = $current_date;
					$scData['expire_on'] = $expire_date;
					$subscribe_id = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION'))
							->insertGetId($scData);

					if ($subscribe_id)
					{                               
						$tupRes = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
								->where('subscribe_topup_id', $postdata['subscribe_topup_id'])
								->update([
							'subscribe_id'=>$subscribe_id,
							'updated_date'=>getGTZ()]);
							
						/*if ($tupRes)
						{*/
							if ($userSess->can_sponsor == $this->config->get('constants.OFF'))
							{
								$this->affObj->updateLineage($userSess, $pack_details);
							}
							if ($pack_details->instant_benefit_credit == $this->config->get('constants.ON'))
							{  										  
								$pack_details = $this->getTopupPackageInfo(['purchase_code'=>$postdata['purchase_code']]);
								if($pack_details){
									$res = $this->activatePackage($pack_details);																					
								}
								$op['status'] = $this->config->get('httperr.SUCCESS');
								$op['msgtype'] = 'success';
								$op['msg'] = trans('affiliate/package/purchase.validate.success_with_benefit_credit', ['package_name'=>$pack_details->package_name, 'purchase_code'=>$postdata['purchase_code']]);
							}							
							else
							{
								$op['status'] = $this->config->get('httperr.SUCCESS');
								$op['msg'] = trans('affiliate/package/purchase.validate.success_onhold_benefits', ['package_name'=>$pack_details->package_name, 'purchase_code'=>$postdata['purchase_code'], 'refund_on'=>date('M d, Y', strtotime(showUTZ('Y-m-d'), $pack_details->refundable_days))]);
								$op['msgtype'] = 'success';
							}
					   /* }
						else
						{
							$op['msg'] = trans('affiliate/package/purchase.validate.package_purchase_incomplete');
							$op['msgtype'] = 'success';
						}*/
					}
					else
					{
						$op['msg'] = trans('affiliate/package/purchase.validate.package_purchase_incomplete');
						$op['msgtype'] = 'danger';
					}                    
                }
                else
                {
                    $op['msg'] = trans('affiliate/package/purchase.validate.package_purchase_ilegale');
                    $op['msgtype'] = 'danger';
                }
            }
            else
            {
                $op['msg'] = trans('affiliate/package/purchase.validate.packmissing');
                $op['msgtype'] = 'danger';
            }
        }
        else
        {
            $op['msg'] = trans('affiliate/package/purchase.validate.packmissing');
            $op['msgtype'] = 'danger';
        }
		Log::Info("subscription: ".json_encode($op));
        return $op;
    }
	
	
	public function releasePackageCashback($pack_details){
		if(!empty($pack_details)){		
			
			if($pack_details->shopping_points_bonus>0){
		
				$balInfo = $this->walletObj->get_user_balance($this->config->get('constants.PAYMENT_TYPES.WALLET'),['account_id'=>$this->userSess->account_id],$this->config->get('constants.WALLETS.VIB'),$this->userSess->currency_id);
				
				$usrbal_upres = $this->walletObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIB'), 'account_id'=>$this->userSess->account_id, 'currency_id'=>$this->userSess->currency_id, 'amount'=>$pack_details->shopping_points_bonus, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
			
				$transaction_id = AppService::getTransID($this->userSess->account_id);				
				
				$trans = [];
				$trans['account_id'] = $this->userSess->account_id;
				$trans['statementline_id'] = 66; /* package purchase cashback credit */
				$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
				$trans['relation_id'] = $pack_details->subscribe_topup_id;
				$trans['amt'] = $pack_details->shopping_points_bonus;
				$trans['handle_amt'] = 0;
				$trans['paid_amt'] = $pack_details->shopping_points_bonus;
				$trans['currency_id'] = $this->userSess->currency_id;
				$trans['wallet_id'] = $this->config->get('constants.WALLETS.VIB');
				$trans['transaction_id'] = $transaction_id;
				$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
				$trans['remark'] = addslashes(json_encode(['package'=>$pack_details->purchase_code,'code'=>$pack_details->purchase_code]));
				$trans['created_on'] = getGTZ();
				$trans['current_balance'] = ($balInfo->current_balance+$pack_details->shopping_points_bonus);
				$trans['status'] = $this->config->get('constants.ACTIVE');
				//print_r($trans);
				$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
						->insertGetId($trans);
			}
			if($pack_details->shopping_points_cashback>0){		
				
				$balInfo = $this->walletObj->get_user_balance($this->config->get('constants.PAYMENT_TYPES.WALLET'),['account_id'=>$this->userSess->account_id],$this->config->get('constants.WALLETS.VIS'),$this->userSess->currency_id);				
				
				$usrbal_Info = $this->walletObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIS'), 'account_id'=>$this->userSess->account_id, 'currency_id'=>$this->userSess->currency_id, 'amount'=>$pack_details->shopping_points_cashback, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
				
				$transaction_id = AppService::getTransID($this->userSess->account_id);
				
				$trans = [];
				$trans['account_id'] = $this->userSess->account_id;
				$trans['statementline_id'] = 66; /* package purchase cashback credit */
				$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
				$trans['relation_id'] = $pack_details->subscribe_topup_id;
				$trans['amt'] = $pack_details->shopping_points_cashback;
				$trans['handle_amt'] = 0;
				$trans['paid_amt'] = $pack_details->shopping_points_cashback;
				$trans['currency_id'] = $this->userSess->currency_id;
				$trans['wallet_id'] = $this->config->get('constants.WALLETS.VIS');
				$trans['transaction_id'] = $transaction_id;
				$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
				$trans['remark'] = addslashes(json_encode(['data'=>['package'=>$pack_details->purchase_code,'code'=>$pack_details->purchase_code]]));
				$trans['created_on'] = getGTZ();
				$trans['current_balance'] = ($balInfo->current_balance+$pack_details->shopping_points_cashback);
				$trans['status'] = $this->config->get('constants.ACTIVE');
				//print_r($trans);
				$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
						->insertGetId($trans);
			}
		}	
	}	

    public function updateCurrent_salespoints ($arr = array())
    {
        extract($arr);
        $sdata = [];
        $wdata = [
            'account_id'=>$account_id];

        if (isset($refcnts))
        {
            $sdata['referral_cnts'] = DB::Raw('referral_cnts+1');
        }

        if (isset($bv))
        {
            $sdata['bv'] = DB::Raw('bv+'.$bv);
        }

        if (isset($pro_rank_id))
        {
            $sdata['pro_rank_id'] = $pro_rank_id;
        }

        if (isset($refpaidcnts))
        {
            $sdata['referral_paid_cnts'] = DB::Raw('referral_paid_cnts+1');
        }

        if (isset($package_id))
        {
            $sdata['recent_package_id'] = $package_id;
            $sdata['recent_package_purchased_on'] = getGTZ();
        }

        if (isset($qv))
        {
            $sdata['qv'] = DB::Raw('qv+'.$qv);			
        }
		//print_r($wdata);
        if (!empty($wdata) && $sdata)
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where($wdata)
                    ->update($sdata);
			
			if($this->userSess->pro_rank_id==$this->config->get('constants.OFF'))
			{
				$usr_tpinfo = DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('account_id','=',$account_id)
					->select('qv')
                    ->first();
					
				$rankInfo = DB::table($this->config->get('tables.AFF_RANKING_LOOKUPS'))
							->where('af_rank_id','=',$this->config->get('constants.RANK.PROMOTER'))
							->first();
							
				if($rankInfo && isset($qv) && $rankInfo->qv <= $qv){
					$result = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('account_id','=',$account_id)
						->update(['pro_rank_id'=>$this->config->get('constants.RANK.PROMOTER')]);
				}
			}
        }
    }

    public function package_list ()
    {
        $sql = DB::table($this->config->get('tables.AFF_PACKAGE_MST').' as pm')
                ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($join)
                {
					$join->on('pl.package_id', '=', 'pm.package_id');
                    $join->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                    $join->where('pm.status', '=', $this->config->get('constants.STATUS'));
                })
                ->select('pm.package_id', 'pl.package_name')
                ->where('pm.is_deleted', $this->config->get('constants.NOT_DELETED'));

        $result = $sql->get();

        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }

	
	public function activatePackage($packInfo){
		$op = [];
		if(is_object($packInfo)){			
			$stdata['status'] =   $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED');		
			
			$op['stRes'] = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
						->where('subscribe_topup_id',$packInfo->subscribe_topup_id)
						->where('account_id',$this->userSess->account_id)
						->where('is_deleted',$this->config->get('constants.OFF'))
						->update($stdata);
			
			if($op['stRes']){
				$sbdata['confirm_date'] =   getGTZ();			
				$sbdata['topup_id'] =  $packInfo->subscribe_topup_id;
				$sbdata['package_id'] =   $packInfo->package_id;			
				$sbdata['package_level'] =  $packInfo->package_level; 
				$sbdata['package_qv'] =   $packInfo->package_qv;
				$sbdata['weekly_capping_qv'] =   $packInfo->weekly_capping_qv;
				$sbdata['status'] =   $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED');
				$sbdata['payment_status'] =   $this->config->get('constants.PAYMENT_STATUS.CONFIRMED');
				$sbdata['confirm_date'] =   getGTZ();			
				$sbdata['expire_on'] =	  getGTZ(date('Y-m-d H:i:s',strtotime('+ 1 year')));
				
				$op['sbRes'] = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION'))
						->where('subscribe_id',$packInfo->subscribe_id)
						->where('account_id',$this->userSess->account_id)
						->where('is_deleted',$this->config->get('constants.OFF'))
						->update($sbdata);
						
				if($op['sbRes']){
					$op['usrSPRes'] = $this->updateCurrent_salespoints([
						  'account_id' => $packInfo->account_id,
						  'package_id' => $packInfo->package_id,
						  'qv' => $packInfo->package_qv,
						  'weekly_capping_qv' => $packInfo->weekly_capping_qv,
						  'bv' => $packInfo->amount]);
						  
					$op['pkCB'] = $this->releasePackageCashback($packInfo);					
					$op['pkRef'] = $this->bonusObj->addReferralBonus($packInfo);					
				}
				Log::info(json_encode($op).' = stcode:'.$packInfo->purchase_code);				
				return $op;
			}
		}
		return false;
	}                  

    public function getTopupPackageInfo ($arr = array())
    {
        if (!empty($arr) && (isset($arr['purchase_code']) || isset($arr['subscribe_topup_id'])))
        {
            $pkgSql = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usubtop')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'usubtop.account_id')
					->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'pm.package_id', '=', 'usubtop.package_id')
                    ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
                    {
                        $subquery->on('pm.package_id', '=', 'pl.package_id')
                        ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                    })
                    ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usubtop.currency_id')
                    ->where('usubtop.account_id', $this->userSess->account_id)
                    ->where('usubtop.is_deleted', $this->config->get('constants.OFF'))
                    ->select(DB::RAW('usubtop.account_id,am.user_code,usubtop.package_id,usubtop.purchase_code,usubtop.currency_id,usubtop.subscribe_topup_id,cur.currency as currency_code,pl.package_name,pm.package_image,usubtop.amount,usubtop.handle_amt,usubtop.paid_amt,usubtop.create_date,usubtop.is_refundable,pm.instant_benefit_credit,usubtop.subscribe_id,usubtop.refund_expire_on,usubtop.package_qv,usubtop.weekly_capping_qv,usubtop.shopping_points_cashback,usubtop.shopping_points_bonus,usubtop.package_level,usubtop.updated_date,usubtop.status'));

            if (isset($arr['purchase_code']) && !empty($arr['purchase_code']))
            {
                $pkgSql->where('usubtop.purchase_code', '=', $arr['purchase_code']);
            }
            else if (isset($arr['subscribe_topup_id']) && !empty($arr['subscribe_topup_id']))
            {
                $pkgSql->where('usubtop.subscribe_topup_id', '=', $arr['subscribe_topup_id']);
            }

            return $pkgSql->first();
        }
		return false;
    }

    public function packageRefund ($arr=array())
    {
		extract($arr);
        if($purchase_code!=''){
			$topup_details = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usubtop')
					->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'usubtop.package_id', '=', 'pm.package_id')
					->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
					{
						$subquery->on('pm.package_id', '=', 'pl.package_id')
						->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
					})
					->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usubtop.currency_id')
					->where('usubtop.account_id', $this->userSess->account_id)
					->where('usubtop.is_deleted', $this->config->get('constants.OFF'))
					->where('usubtop.purchase_code', '=', $purchase_code)
					//->where('usubtop.is_refundable', '=', $this->config->get('constants.ON'))
					->where('usubtop.status', '=', $this->config->get('constants.PACKAGE_PURCHASE_STATUS.USER_APPROVALS'))
					->select(DB::RAW('usubtop.currency_id,usubtop.subscribe_topup_id,usubtop.account_id,usubtop.purchase_code,cur.currency as currency_code,pl.package_name,usubtop.amount,usubtop.is_refundable,usubtop.subscribe_id,usubtop.refund_expire_on,usubtop.package_qv,usubtop.weekly_capping_qv,usubtop.shopping_points_cashback,usubtop.shopping_points_bonus,usubtop.package_level,usubtop.updated_date,usubtop.status'))
					->first();
			
			if (!empty($topup_details))
			{
				/*$usrbal_Info = $this->walletObj->update_account_balance(array('wallet_id'=>$this->config->get('constants.WALLETS.VP'), 'account_id'=>$topup_details->account_id, 'currency_id'=>$topup_details->currency_id, 'amount'=>$topup_details->amount, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));*/
				$usrbal_Info = (object)['current_balance'=>100000];
				if ($usrbal_Info)
				{
					$trans = [];
					$trans['account_id'] = $topup_details->account_id;
					$trans['statementline_id'] = 65; /* package refund credit */
					$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
					$trans['amt'] = $topup_details->amount;
					$trans['handle_amt'] = 0;
					$trans['paid_amt'] = $topup_details->amount;
					$trans['currency_id'] = $topup_details->currency_id;
					$trans['wallet_id'] = $this->config->get('constants.WALLETS.VP');
					$trans['transaction_id'] = AppService::getTransID($topup_details->account_id);
					$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
					$trans['relation_id'] = $topup_details->subscribe_topup_id;
					$trans['remark'] = json_encode(addslashes(['data'=>['package'=>$pack_details->purchase_code,'code'=>$pack_details->purchase_code]]));
					$trans['created_on'] = getGTZ();
					$trans['current_balance'] = $usrbal_Info->current_balance;
					$trans['status'] = $this->config->get('constants.ACTIVE');
					$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
							->insertGetId($trans);
					if ($transResID)
					{
						return DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
									->where('subscribe_topup_id', '=', $topup_details->subscribe_topup_id)
									->update([
										'status'=>$this->config->get('constants.PACKAGE_PURCHASE_STATUS.CANCELLED'),
										'payment_status'=>$this->config->get('constants.PAYMENT_STATUS.REFUNDED'),
										'cancelled_date'=>getGTZ()]);
					}
				}
			}
		}
        return false;
    }
	
	public function confirmPGPayment($topupId, $status = null, $payment_id = null){
		$details = $this->getTopupPackageInfo(['subscribe_topup_id'=>$topupId]);
		if($details)
		{
			if (empty($status) || (!empty($status) && $status == 'CONFIRMED'))
			{
				if ($details->payment_type_id != $this->config->get('constants.PAYMENT_TYPES.WALLET') || 
					($details->payment_type_id == $this->config->get('constants.PAYMENT_TYPES.WALLET')  && $payment_id = $this->updateAccountTransaction([
                'payment_type_id'=>$details->payment_type_id,
                'pay_mode'=>$details->pay_mode_id,
                'from_account_id'=>$details->account_id,
                'from_wallet_id'=>$this->config->get('constants.CASHBACK_CREDIT_WALLET'),
                'currency_id'=>$details->from_currency_id,
                'amt'=>$details->bill_amount * $this->get_currency_rate($details->to_currency_id, $details->from_currency_id),
                'paidamt'=>$details->from_amount,
                'relation_id'=>$details->pay_id,
                'debit_remark_data'=>['order_code'=>$details->order_code, 'amount'=>CommonLib::currency_format($details->from_amount, $details->from_currency_id), 'store_name'=>$details->store, 'full_name'=>$details->full_name],
                'transaction_for'=>'ORDER_DEAL_PURCHASE'
                    ])) )
				$res = $this->activatePackage($pack_details);																					
			}
		}
	}
	
}

