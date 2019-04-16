<?php

namespace App\Http\Controllers\Affiliate;

use DB;
use Log;
use App\Models\Affiliate\Payments;
use App\Http\Controllers\AffBaseController;


class PaymentGatewayController extends AffBaseController
{

    public function __construct ()
    {
        parent::__construct();
		$this->paymentObj = new Payments;
    }

    private function checkNull ($value)
    {
        return ($value == null) ? '' : $value;
    }

    public function checkSum ($payment_type)
    {
        $op = [];
        switch ($payment_type)
        {
            case 'pay-u':
                $data = $this->request->all();
                $key = 'gtKFFx';
                $salt = 'eCwWELxi';
                $op['payment_hash'] = strtolower(hash('sha512', $key.'|'.$this->checkNull($this->request->txnid).'|'.$this->checkNull($data['amount']).'|'.$this->checkNull($data['productinfo']).'|'.$this->checkNull($data['firstname']).'|'.$this->checkNull($data['email']).'|'.$this->checkNull($data['udf1']).'|'.$this->checkNull($data['udf2']).'|'.$this->checkNull($data['udf3']).'|'.$this->checkNull($data['udf4']).'|'.$this->checkNull($data['udf5']).'||||||'.$salt));
                $op['get_merchant_ibibo_codes_hash'] = strtolower(hash('sha512', $key.'|get_merchant_ibibo_codes|default|'.$salt));
                $op['vas_for_mobile_sdk_hash'] = strtolower(hash('sha512', $key.'|vas_for_mobile_sdk|default|'.$salt));
                $op['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $key.'|payment_related_details_for_mobile_sdk|default|'.$salt));
                $op['verify_payment_hash'] = strtolower(hash('sha512', $key.'|verify_payment|'.$this->request->txnid.'|'.$salt));
                if ($data['user_credentials'] != NULL && $data['user_credentials'] != '')
                {
                    $op['delete_user_card_hash'] = strtolower(hash('sha512', $key.'|delete_user_card|'.$data['user_credentials'].'|'.$salt));
                    $op['get_user_cards_hash'] = strtolower(hash('sha512', $key.'|get_user_cards|'.$data['user_credentials'].'|'.$salt));
                    $op['edit_user_card_hash'] = strtolower(hash('sha512', $key.'|edit_user_card|'.$data['user_credentials'].'|'.$salt));
                    $op['save_user_card_hash'] = strtolower(hash('sha512', $key.'|save_user_card|'.$data['user_credentials'].'|'.$salt));
                    $op['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $key.'|payment_related_details_for_mobile_sdk|'.$data['user_credentials'].'|'.$salt));
                }
                $op['send_sms_hash'] = strtolower(hash('sha512', $key.'|send_sms|'.$data['udf3'].'|'.$salt));
                if ($data['offerKey'] != NULL && !empty($data['offerKey']))
                {
                    $op['check_offer_status_hash'] = strtolower(hash('sha512', $key.'|check_offer_status|'.$data['offerKey'].'|'.$salt));
                }
                if ($data['cardBin'] != NULL && !empty($data['cardBin']))
                {
                    $op['check_isDomestic_hash'] = strtolower(hash('sha512', $key.'|check_isDomestic|'.$data['cardBin'].'|'.$salt));
                }
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                break;
            case 'cashfree':
                $op['status'] = 'ERROR';
                $data = $this->request->all();
                $appId = '3135c5ca65a7ec9f54b3830313'; //replace it with your appId
                $secretKey = '4ca06060c5c649754e055317725a0067e23cb9db'; //replace it with your secret key
                Log::info('Cashfree Input : '.json_encode($data).' from '.$this->request->header('User-Agent'));
                //$gateway_settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
                //$gateway_settings=$gateway_settings->status?$gateway_settings->live:$gateway_settings->sandbox;
                if (isset($data['orderId']) && isset($data['orderAmount']) && isset($data['customerEmail']) && isset($data['customerPhone']))
                {
                    $op['status'] = 'OK';
                    $op['orderId'] = $data['orderId'];
                    $checksumData = '';
                    if (stripos($this->request->header('User-Agent'), 'Android') === false)
                    {
                        $checksumData = 'appId='.$appId.'&orderId='.$data['orderId'].'&orderAmount='.$data['orderAmount'].'&customerEmail='.$data['customerEmail'].'&customerPhone='.$data['customerPhone'].'&orderCurrency='.$data['orderCurrency'];
                    }
                    else
                    {
                        ksort($data);
                        foreach ($data as $key=> $value)
                        {
                            $checksumData .= $key.$value;
                        }
                    }
                    $op['checksum'] = base64_encode(hash_hmac('sha256', $checksumData, $secretKey, true));
                    DB::table($this->config->get('tbl.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $data['orderId'])
                            ->update(['checksum'=>$op['checksum']]);
                }
                $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        return $this->response->json($op, $this->statusCode, $this->header, $this->options);
    }
	
	

    public function _return ($payment_type, $id)
    {
        $data = $this->request->all();
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);
        switch ($payment_type)
        {
            case 'pay-u':
                if (isset($data['status']))
                {
                    $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['udf1']);
                    $pgr['payment_id'] = isset($data['txnid']) ? $data['txnid'] : null;
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
                }
                break;
            case 'pay-dollar':
                if (isset($data['successcode']) && ((isset($data['Ref']) && $data['Ref'] != 'TestDatafeed') || !empty($id)))
                {
                    $gateway_settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                    $pgr['payment_status'] = $data['successcode'] == 0 ? 'CONFIRMED' : ($data['successcode'] == 1 ? 'FAILED' : 'CANCELLED');
                    $pgr['id'] = base64_decode(!empty($id) ? $id : $data['Ref']);
                }
                break;
            case 'cashfree':
                $settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                if ($data['signature'] == base64_encode(hash_hmac('sha256', $data['orderId'].$data['orderAmount'].$data['referenceId'].$data['txStatus'].$data['paymentMode'].$data['txMsg'].$data['txTime'], $settings->secretKey, true)))
                {
                    if (isset($data['txStatus']))
                    {
                        $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['orderId']);
                        if ($data['txStatus'] == 'SUCCESS')
                        {
                            $pgr['payment_status'] = 'CONFIRMED';
                        }
                        elseif ($data['txStatus'] == 'CANCELED')
                        {
                            $pgr['payment_status'] = 'CANCELED';
                        }
                        else
                        {
                            $pgr['payment_status'] = 'FAILED';
                        }
                    }
                    $pgr['payment_id'] = isset($data['referenceId']) ? $data['referenceId'] : null;
                }
                break;
        }
        $pgr['response'] = $data;
        $pgr['payment_status'] = (!empty($pgr['payment_status'])) ? $pgr['payment_status'] : 'FAILED';
        return $this->getResponse($pgr['id'], $pgr);
    }

    public function success ($payment_type, $id)
    {
        $data = $this->request->all();
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);
        if (isset($data['response']) && !empty($data['response']))
        {
            $data = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];
        }
        switch ($payment_type)
        {
            case 'pay-u':
                if (isset($data['status']))
                {
//                    $gateway_settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
//                    if (isset($data['additionalCharges']))
//                    {
//                        $retHashSeq = $data['additionalCharges'].'|'.$gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
//                    }
//                    else
//                    {
//                        $retHashSeq = $gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
//                    }
//                    $hash = hash('sha512', $retHashSeq);
//                    if ($hash != $data['hash'])
//                    {
//                        $pgr['payment_status'] = 'FAILED';
//                    }
//                    else
//                    {
                    $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['udf1']);
                    $pgr['payment_id'] = isset($data['txnid']) ? $data['txnid'] : null;
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
//                    }
                }
                break;
            case 'pay-dollar':
                if (isset($data['successcode']) && ((isset($data['Ref']) && $data['Ref'] != 'TestDatafeed') || !empty($id)))
                {
                    $gateway_settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                    $pgr['payment_status'] = $data['successcode'] == 0 ? 'CONFIRMED' : ($data['successcode'] == 1 ? 'FAILED' : 'CANCELLED');
                    $pgr['id'] = base64_decode(!empty($id) ? $id : $data['Ref']);
                }
                break;
            case 'cashfree':
                $settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                if ($data['signature'] == base64_encode(hash_hmac('sha256', $data['orderId'].$data['orderAmount'].$data['referenceId'].$data['txStatus'].$data['paymentMode'].$data['txMsg'].$data['txTime'], $settings->secretkey, true)))
                {
                    if (isset($data['txStatus']))
                    {
                        $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['orderId']);
                        if ($data['txStatus'] == 'SUCCESS')
                        {
                            $pgr['payment_status'] = 'CONFIRMED';
                        }
                        elseif ($data['txStatus'] == 'CANCELED')
                        {
                            $pgr['payment_status'] = 'CANCELED';
                        }
                        else
                        {
                            $pgr['payment_status'] = 'FAILED';
                        }
                    }
                    $pgr['payment_id'] = isset($data['referenceId']) ? $data['referenceId'] : null;
                }
                break;
        }
        $pgr['response'] = $data;
        $pgr['payment_status'] = (!empty($pgr['payment_status'])) ? $pgr['payment_status'] : 'FAILED';
        return $this->getResponse($pgr['id'], $pgr);
    }

    public function failure ($payment_type, $id)
    {
        $data = $this->request->all();
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);
        if (isset($data['response']) && !empty($data['response']))
        {
            $data = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];
        }
        switch ($payment_type)
        {
            case 'pay-u':
                $gateway_settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                If (isset($data['additionalCharges']))
                {
                    $retHashSeq = $data['additionalCharges'].'|'.$gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
                }
                else
                {
                    $retHashSeq = $gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
                }
                $hash = hash('sha512', $retHashSeq);
                if ($hash != $data['hash'])
                {
                    $pgr['payment_status'] = 'FAILED';
                }
                else
                {
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
                }
                $pgr['payment_id'] = isset($data['txnid']) ? $data['txnid'] : null;
                break;
            case 'pay-dollar':
                $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
                break;
        }
        return $this->getResponse($pgr['id'], $pgr);
    }

    public function cancelled ($payment_type, $id)
    {
        $pgr = [];
        $pgr['id'] = base64_decode($id);
        //$pgr['payment_type'] = $payment_type;
        $pgr['response'] = $this->request->all();
		// http://localhost/dsvb_affiliate/payment-gateway-response/pay-u/cancelled/MQ==
        $data =json_decode( '{"mihpayid":"403993715518702252","mode":"","status":"failure","unmappedstatus":"userCancelled","key":"gtKFFx","txnid":"5f572c9a7bf53b1297cc","amount":"7500.00","discount":"0.00","net_amount_debit":"0.00","addedon":"2018-12-08 17:18:16","productinfo":"Package Starter Purchase amp 8377 7 500.00","firstname":"dsvb","lastname":"","address1":"","address2":"","city":"","state":"","country":"","zipcode":"","email":"dsvbdirect105@virob.com","phone":"","udf1":"5","udf2":"","udf3":"","udf4":"","udf5":"","udf6":"","udf7":"","udf8":"","udf9":"","udf10":"","hash":"78ac366fb0b623fa74b763a1dc721874fcce3b8793d8578f7afd2b5ceb8026b73bbe717960fa15ea583ab0cd5c457f192a871b5f5ddcdce6b4943e4d19aa9cce","field1":"","field2":"","field3":"","field4":"","field5":"","field6":"","field7":"","field8":"","field9":"Cancelled by user","payment_source":"payu","PG_TYPE":"","bank_ref_num":"","bankcode":"","error":"E1605","error_Message":"Transaction failed due to customer pressing cancel button."}',true);
        
		$pgr['response'] = $data;
		switch ($payment_type)
        {
            case 'pay-u':
                $pgr['payment_status'] = 'CANCELLED';
                $pgr['payment_id'] = isset($data['txnid']) ? $data['txnid'] : null;
                break;
        }
        return $this->getResponse($pgr['id'], $pgr);
    }

    public function dataFeed ($payment_type, $id = null)
    {
        $data = $this->request->all();
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);
        if (isset($data['response']) && !empty($data['response']))
        {
            $data = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];
        }
        switch ($payment_type)
        {
            case 'pay-u':
                if (isset($data['status']))
                {
//                    $gateway_settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
//                    if (isset($data['additionalCharges']))
//                    {
//                        $retHashSeq = $data['additionalCharges'].'|'.$gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
//                    }
//                    else
//                    {
//                        $retHashSeq = $gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
//                    }
//                    $hash = hash('sha512', $retHashSeq);
//                    if ($hash == $data['hash'])
//                    {
                    $pgr['id'] = !empty($id) ? base64_decode($id) : $data['udf1'];
                    $pgr['payment_id'] = isset($data['txnid']) ? $data['txnid'] : null;
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
//                    }
                }
                break;
            case 'pay-dollar':
                if (isset($data['successcode']) && ((isset($data['Ref']) && $data['Ref'] != 'TestDatafeed') || !empty($id)))
                {
                    $gateway_settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                    $pgr['payment_status'] = $data['successcode'] == 0 ? 'CONFIRMED' : ($data['successcode'] == 1 ? 'FAILED' : 'CANCELLED');
                    $pgr['id'] = !empty($id) ? base64_decode($id) : $data['Ref'];
                }
                if (empty($id))
                {
                    return 'OK';
                }
                break;
            case 'cashfree':
                /* response[name]:PAYMENT_RESPONSE
                  response[status]:SUCCESS
                  response[message]:Payment response recieved
                  response[response][orderId]:22
                  response[response][orderAmount]:30.00
                  response[response][referenceId]:13519
                  response[response][txStatus]:SUCCESS
                  response[response][paymentMode]:DEBIT_CARD
                  response[response][txMsg]:Transaction Successful
                  response[response][txTime]:2018-01-16 15:48:31
                  response[response][signature]:IqzvVYq95boXXna5ErA39sQKYB7vh4QFr4LzIPGcDG8= */
                $settings = $this->paymentObj->getPaymentGateWayDeatils($payment_type);
                if (isset($data['signature']) && $data['signature'] == base64_encode(hash_hmac('sha256', $data['orderId'].$data['orderAmount'].$data['referenceId'].$data['txStatus'].$data['paymentMode'].$data['txMsg'].$data['txTime'], $settings->secretKey, true)))
                {
                    if (isset($data['txStatus']))
                    {
                        $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['orderId']);
                        $pgr['payment_id'] = $data['referenceId'];
                        if ($data['txStatus'] == 'SUCCESS')
                        {
                            $pgr['payment_status'] = 'CONFIRMED';
                        }
                        elseif ($data['txStatus'] == 'CANCELED')
                        {
                            $pgr['payment_status'] = 'CANCELED';
                        }
                        else
                        {
                            $pgr['payment_status'] = 'FAILED';
                        }
                    }
                    $pgr['payment_id'] = isset($data['referenceId']) ? $data['referenceId'] : null;
                }
                break;
        }
        $pgr['response'] = $data;
        $pgr['payment_status'] = (!empty($pgr['payment_status'])) ? $pgr['payment_status'] : 'FAILED';
        return $this->getResponse($pgr['id'], $pgr, true);
    }

    private function getResponse ($id, $pgr = array(), $is_repeated = false)
    {	
		$op = [];
		$data = [];
        if ($details = $this->paymentObj->getPGRdetails($id))
        {
            $res = $this->paymentObj->saveResponse($pgr, $data, $is_repeated);			
            if ($details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE'))
            {
                if ($token = $this->commonObj->getAccountLogToken($details->account_log_id))
                {
                    $token = explode('-', $token);
                    $this->session->setId($token[0], true);                    
                }
            }            
            $this->statusCode = $res['status'];
            if ($this->config->get('app.is_api'))
            {
                return $this->response->json($res, $this->statusCode, $this->header, $this->options);
            }
            else
            {
                $this->session->flash('paymentGatewayResponse', $res);
                switch ($details->purpose)
                {
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE'):
                        return $this->statusCode == $this->config->get('httperr.SUCCESS') || $this->statusCode == $this->config->get('httperr.ALREADY_UPDATED') ? redirect()->route('aff.package.my_packages') : redirect()->route('aff.package.browse');                    
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'):
                        return $this->statusCode == $this->config->get('httperr.SUCCESS') ? redirect()->route('aff.wallet.balance-list') : redirect()->route('aff.wallet.add-money');
                }
            }
        }
        else
        {
            return $this->config->get('app.is_api') ? $this->response->json($res, $this->statusCode, $this->header, $this->options) : app()->abort(404);
        }
    }

}
