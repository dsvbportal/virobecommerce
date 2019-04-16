<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use App\Models\Affiliate\Package;
use DB;

class Payments extends BaseModel
{
	 public function __construct ()
    {
         parent::__construct(); 
		 $affObj = new AffModel();  
		 $this->applang = /* (Session::has('applang')) ? Session::get('applang') : */ 'en';
    }		
	
	public function get_paymodes($arr=array()){	
		extract($arr);
		$res = '';
		if(!empty($purpose)){
		 $qry = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
				   ->where('pt.status',$this->config->get('constants.ACTIVE'))				   
				   ->select(DB::Raw('payment_type_id,pt.payment_key,payment_type,payment_key,description,image_name,check_kyc_status,kyc_settings'));
			$qry->where($purpose,'=',$this->config->get('constants.ACTIVE'));
			$qry->orderBy('priority','asc'); 			
			
			if(isset($payment_type_id) && $payment_type_id>0) {
				$qry->where('payment_type_id','=',$payment_type_id);
				$res = $qry->first();				
			}
			else {
				$res = $qry->get();
				 if (!empty($res))
				{
					array_walk($res, function(&$pg, $key)
					{
						$pg->icon = asset($this->config->get('constants.PAYOUT_IMAGE_PATH').$pg->image_name);
					});
				}
			}			
		}
		return ($res)? $res : NULL;
	}
	
	public function get_currencies($arr=array()) {
		extract($arr);
        $qry = DB::table($this->config->get('tables.CURRENCIES'))                        
						->where('status','=',$this->config->get('constants.ACTIVE'))						
                        ->select('currency_id','currency_symbol','currency as currency_code');	
		if(isset($currencies) && is_array($currencies)){
			$qry->whereIn('currency_id',$currencies);
		}
		else if(isset($currency_id) && is_array($currency_id)){
			$qry->whereIn('currency_id',$currency_id);
		}						
		$res = $qry->get();		
	    if (!empty($res) && count($res) > 0) {
	        return $res;
        } 
        return false;
    }
	
	
	public function getGateWayInfo ($paymet_gateway_id, array $arr = array())
    {
        extract($arr);
        $payment_details = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->where('payment_type_id', $paymet_gateway_id)
                ->selectRaw('pt.payment_type_id,pt.payment_key,gateway_settings,save_card,pt.payment_type as paymentgateway_name')
                ->first();
			
        if (!empty($payment_details))
        {
            $settings = $payment_details->gateway_settings = json_decode($payment_details->gateway_settings);
            if (!empty($settings) && is_object($settings))
            {               
                $payment_code = $payment_details->payment_key;
				$settings = $settings->status ? (array) $settings->live : (array) $settings->sandbox;
                $pgr = [];
                $pgr['account_id'] = $account_id;
                $pgr['account_log_id'] = $account_log_id;
                $pgr['payment_type_id'] = $payment_details->payment_type_id;
                $pgr['pay_mode_id'] = $this->config->get('constants.PAYMENT_TYPES.'.$payment_mode);
                $pgr['purpose'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.'.$purpose);
                $pgr['relative_post_id'] = $id;
                $pgr['currency_id'] = $currency_id;
                $pgr['amount'] = $amount;
                $pgr['created_on'] = getGTZ();
                $pgr_id = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->insertGetID($pgr);						
                switch ($payment_code)
                {
                    case 'pay-u':
                        $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        $settings = array_merge($settings, ['productinfo'=>$remark, 'firstname'=>$firstname, 'email'=>$email, 'mobile'=>$mobile, 'udf1'=>$pgr_id]);
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['amount'] = number_format((float) $amount, 2, '.', '');
                        $settings['txnid'] = substr(hash('sha256', mt_rand().microtime()), 0, 20);

                        $hashSequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt';
                        $hashVarsSeq = explode('|', $hashSequence);
                        $hash_string = '';
                        foreach ($hashVarsSeq as $hash_var)
                        {
                            $hash_string .= isset($settings[$hash_var]) ? $settings[$hash_var] : ($this->config->get('app.is_api') && in_array($hash_var, ['udf1', 'udf2', 'udf3', 'udf4', 'udf5']) ? $hash_var : '');
                            $hash_string .= '|';
                        }
                        $settings['hash_values_ulr'] = route('aff.payment-gateway-response.check-sum', ['payment_type'=>$payment_code]);
                        $settings['hash'] = strtolower(hash('sha512', trim($hash_string, '|')));
                        $settings['vas_for_mobile_sdk_hash'] = strtolower(hash('sha512', $settings['key'].'|vas_for_mobile_sdk|default|'.$settings['salt']));
                        $settings['verify_payment_hash'] = strtolower(hash('sha512', $settings['key'].'|verify_payment|'.$settings['txnid'].'|'.$settings['salt']));
                        $settings['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $settings['key'].'|payment_related_details_for_mobile_sdk|default|'.$settings['salt']));                        
                        $settings['surl'] = route('aff.payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['furl'] = route('aff.payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['curl'] = route('aff.payment-gateway-response.cancelled', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        break;
                    case 'pay-dollar':
                        $settings['orderRef'] = $pgr_id;
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['currCode'] = $payment_details->gateway_settings->currCode->{$currency_id};
                        $settings['payType'] = $payment_details->gateway_settings->payType;
                        $settings['lang'] = $payment_details->gateway_settings->lang;
                        $settings['amount'] = number_format((float) $amount, 2, '.', '');
                        $settings['billingFirstName'] = $firstname;
                        $settings['billingLastName'] = $lastname;
                        $settings['billingEmail'] = $email;
                        $settings['custIPAddress'] = $ip;
                        if (isset($card_details) && !empty($card_details))
                        {
                            $settings['epMonth'] = $card_details['month'];
                            $settings['epYear'] = $card_details['year'];
                            $settings['cardNo'] = $card_details['card_no'];
                            $settings['cardHolder'] = $card_details['holder'];
                            $settings['pMethod'] = $payment_details->gateway_settings->pMethod->{$card_details->card_type_id};
                        }
                        else
                        {
                            $settings['epMonth'] = null;
                            $settings['epYear'] = null;
                            $settings['cardNo'] = null;
                            $settings['cardHolder'] = null;
                            $settings['pMethod'] = NULL;
                        }
                        $settings['securityCode'] = null;
                        $settings['remark'] = $remark;
                        $settings['successUrl'] = route('aff.payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['failUrl'] = route('aff.payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['cancelUrl'] = route('aff.payment-gateway-response.cancelled', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        break;
                    case 'cashfree':
                        $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['paymentModes'] = $payment_details->gateway_settings->modes[$payment_mode];
                        $settings['merchant_name'] = $this->siteConfig->site_name;
                        $settings['merchant_url'] = url('/');
                        $settings['orderId'] = $pgr_id;
                        $settings['orderNote'] = 'test'; //$remark;
                        $settings['orderCurrency'] = $this->get_currency_code($currency_id);
                        $settings['customerName'] = $firstname;
                        $settings['customerEmail'] = $email;
                        $settings['customerPhone'] = $mobile;
                        $settings['orderAmount'] = number_format($amount, 2, '.', ',');
                        $settings['returnUrl'] = route('aff.payment-gateway-response.return', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['notifyUrl'] = route('aff.payment-gateway-response.notify', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['checksumUrl'] = route('aff.payment-gateway-response.check-sum', ['payment_type'=>$payment_code]);
                        ksort($settings);
                        //$signatureData = 'appId='.$settings['appId'].'&orderId='.$settings['orderId'].'&orderAmount='.$settings['orderAmount'].'&customerEmail='.$settings['customerEmail'].'&customerPhone='.$settings['customerPhone'].'&orderCurrency='.$settings['orderCurrency'];
                        $signatureData = 'appId='.$settings['appId'].'&orderId='.$settings['orderId'].'&orderAmount='.$settings['orderAmount'].'&returnUrl='.$settings['returnUrl'].'&paymentModes='.$settings['paymentModes'];
                        $settings['signature'] = base64_encode(hash_hmac('sha256', $signatureData, $settings['secretKey'], true));
                        break;
                }
                $settings['id'] = base64_encode($pgr_id);
				$settings['payment_type'] = $payment_code;				
                $settings['datafeed'] = route('aff.payment-gateway-response.datafeed', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                return $this->xpb_encrypt($settings);
            }
        }
        return false;
    }

    public function getPaymentGateWayDeatils ($payment_code)
    {
        $payment_details = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->where('payment_code', $payment_code)
                ->value('gateway_settings');
        $settings = json_decode($payment_details);
        return $settings->status ? $settings->live : $settings->sandbox;
    }
	
	public function getPGRdetails ($id)
    {
        return DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $id)
                        ->first();
    }
	
	public function saveResponse (array $pgr = array(), array $arr = array(), $withSuccessResponse = false)
    {
        extract($arr);
		echo '<pre>';
        $pgr_details = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                ->where('id', $pgr['id'])
                ->first();
				
        if (!empty($pgr_details))
        {
            $op = [];
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['purpose'] = $pgr_details->purpose;
            if ($pgr_details->status != $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED'))
            {
                $payment_status = $pgr['payment_status'];
                $pgr['payment_status'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.'.$pgr['payment_status']);
                $pgr['response'] = isset($pgr['response']) && !empty($pgr['response']) ? json_encode($pgr['response']) : null;
                $pgr = array_filter($pgr);
                DB::beginTransaction();
                $pgr['updated_on'] = getGTZ();
				
                $upRes = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $pgr['id'])
                        ->update($pgr);
				
				if($upRes){
					$pgr_details->payment_status = $pgr['payment_status']; 
					$pgr_details->response = $pgr['response'];
				}
                $pgr_details->pay_mode = trans('general.payment_modes.'.$pgr_details->pay_mode_id);
				
                switch ($pgr_details->purpose)
                {
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE'):
                        $this->pkObj = new Package($this);
						echo $pgr_details->relative_post_id;die;
                        $trans_id = $this->pkObj->confirmPGPayment($pgr_details->relative_post_id, $payment_status, $pgr_details->payment_id);
                        if ($trans_id)
                        {							
                            $data = [];
                            $this->accObj = new AffModel($this);
                            $data['account_id'] = $pgr_details->account_id;
                            $data['order_code'] = DB::table($this->config->get('tables.ADD_MONEY').' as p')
                                    ->join($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as mo', 'mo.order_id', '=', 'p.order_id')
                                    ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'am.account_id')
                                    ->where('p.pay_id', $pgr_details->relative_post_id)
                                    ->value('mo.order_code');
                            $data['lat'] = $lat;
                            $data['lng'] = $lng;
                            $data['distance_unit'] = $distance_unit;
                            $data['user_location'] = ['lat'=>$lat, 'lng'=>$lng, 'distance_unit'=>$distance_unit];
                            $op['deal'] = $this->accObj->getMyDealDetails($data);
                            $op['msg'] = trans('general.updated', ['which'=>'Deal', 'what'=>trans('general.actions.purchased')]);
                            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                        }
                        else
                        {
                            $op['deal'] = DB::table($this->config->get('tables.ADD_MONEY').' as p')
                                    ->join($this->config->get('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'p.order_id')
                                    ->join($this->config->get('tables.PAYBACK_DEALS').' as pd', 'pd.pb_deal_id', '=', 'oi.pb_deal_id')
                                    ->where('p.pay_id', $pgr_details->relative_post_id)
                                    ->selectRaw('pd.deal_slug,pd.deal_code')
                                    ->first();
                            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed');
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'):
                        $this->addMoneyModel = new AddMoney($this);
                        if ($this->addMoneyModel->confirmAddMoney($pgr_details->relative_post_id, $payment_status, $pgr_details->payment_id))
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                            $op['msg'] = trans('general.money_added', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed', []);
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                }
                if ($op['status'] == $this->statusCode = $this->config->get('httperr.SUCCESS'))
                {
                    DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $pgr_details->id)
                            ->update(['status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED')]);
                }
            }
            else if ($pgr_details->status == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED'))
            {
                switch ($pgr_details->purpose)
                {
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE'):
                        $this->accObj = new AffModel($this);
                        $order_code = DB::table($this->config->get('tables.PAY').' as p')
                                ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                                ->join($this->config->get('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                                ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                                ->where('p.pay_id', $pgr_details->relative_post_id)
                                ->where('oi.status', $this->config->get('constants.ORDER.ITEM.STATUS.BOUGHT'))
                                ->value('mo.order_code');
                        if ($order_code)
                        {
                            $data = [];
                            $data['account_id'] = $pgr_details->account_id;
                            $data['order_code'] = $order_code;
                            $data['lat'] = $lat;
                            $data['lng'] = $lng;
                            $data['distance_unit'] = $distance_unit;
                            $data['user_location'] = ['lat'=>$lat, 'lng'=>$lng, 'distance_unit'=>$distance_unit];
                            $op['deal'] = $this->affObj->getMyDealDetails($data);
                            $op['msg'] = trans('general.updated', ['which'=>'Deal', 'what'=>trans('general.actions.purchased')]);
                            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed');
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'):
                        if (DB::table($this->config->get('tables.ADD_MONEY'))
                                        ->where('am_id', $pgr_details->relative_post_id)
                                        ->where('status', $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.CONFIRMED'))
                                        ->exists())
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                            $op['msg'] = trans('general.money_added', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed', []);
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                }
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                $op['msg'] = trans('affiliate/cashback.payment_already_done', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                $op['title'] = trans('affiliate/cashback.payment_already_done_title');
            }
            DB::commit();
            return $op;
        }
        DB::rollback();
        return false;
    }
	
	 
}
