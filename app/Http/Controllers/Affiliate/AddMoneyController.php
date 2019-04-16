<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AddMoney;
use App\Models\Commonsettings;
use CommonLib;
class AddMoneyController extends AffBaseController
{
	
    public function __construct ()
    {
        parent::__construct();
        $this->addMoneyObj = new AddMoney();
 		$this->commonObj = new Commonsettings();
    }
	
    public function setAmount()
    {
        $op 					= [];
        $addmoney				= (object) [];
        $addmoney->amount 		= $this->request->amount;
        $addmoney->amount 		= 80000;
        $addmoney->account_id 	= $this->userSess->account_id;
        $addmoney->currency_id  = $this->userSess->currency_id;
        $addmoney->country_id 	= $this->userSess->country_id;
        $op['amount'] 		    = CommonLib::currency_format($addmoney->amount, $addmoney->currency_id);
		$op['payment_modes'] 	= $this->addMoneyObj->getPaymentTypes(['account_id'=>$addmoney->account_id, 'currency_id'=>$addmoney->currency_id, 'country_id'=>$addmoney->country_id, 'amount'=>$addmoney->amount]);
        $this->session->set('AM.paymentInfo', $addmoney);
        $this->statusCode = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function paymentInfo ()
    {
        $op = [];
        if ($this->session->has('AM.paymentInfo'))
        {
		    $this->request->payment_mode = 'pay-u';
            $addmoney 					= $this->session->get('AM.paymentInfo');
            $addmoney->payment_mode     = $this->request->payment_mode;
            $addmoney->payment_mode_id  = $this->config->get('constants.PAYMENT_MODES.'.$addmoney->payment_mode);
	        $payment_type 				= $this->commonObj->getPaymentTypeId(['payment_mode'=>$this->request->payment_mode, 'currency_id'=>$this->userSess->currency_id, 'country_id'=>$this->userSess->country_id]);
			//print_r($payment_type);exit;
		    $op['payment_type'] 	   = $addmoney->payment_type = $payment_type->payment_code;
		    $addmoney->payment_type_id = $payment_type->payment_type_id;
            $addmoney->id = $this->addMoneyObj->saveAddMoney((array) $addmoney);
            if ($addmoney->id)
            {
                $gi = [
                    'amount'=>$addmoney->amount,
                    'firstname'=>$this->userSess->firstname,
                    'lastname'=>$this->userSess->lastname,
                    'mobile'=>$this->userSess->mobile,
                    'email'=>$this->userSess->email,
                    'account_id'=>$addmoney->account_id,
                    'account_log_id'=>$this->userSess->account_log_id,
                    'payment_type'=>$addmoney->payment_type,
                    'payment_mode'=>$addmoney->payment_mode,
                    'purpose'=>'ADD-MONEY',
                    'id'=>$addmoney->id,
                    'ip'=>$this->request->getClientIP(),
                    'currency_id'=>$addmoney->currency_id,
                    'card_id'=>$this->request->has('id') ? $this->request->id : null,
                    'remark'=>trans('general.add_money', [ 'amount'=>CommonLib::currency_format($addmoney->amount, $addmoney->currency_id)])];
                $addmoney->gateway_info = $op['gateway_info'] = $this->commonObj->getGateWayInfo($addmoney->payment_type, $gi);
                $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
        }
        else
        {
            $op['msg'] = trans('general.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

}
