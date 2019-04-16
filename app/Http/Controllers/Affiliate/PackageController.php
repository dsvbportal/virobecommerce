<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\Package;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\Payments;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Bonus;
use CommonLib;

class PackageController extends AffBaseController
{

    private $packageObj = '';
    private $purchase_steps = '';

    public function __construct ()
    {
        parent::__construct();
        $this->packageObj = new Package($this->commonObj);
        $this->walletObj = new Wallet;
        $this->paymentObj = new Payments;
		$this->bonusObj = new Bonus;
        $this->affObj = new AffModel();
    }

    public function packages_browse ($token = '', $package = 0)
    {
		/*$pack_details = $this->packageObj->getTopupPackageInfo(['purchase_code'=>'P18104']);		
		$this->bonusObj->addReferralBonus($pack_details);
		die;*/
        /* echo '<pre>';
		$userSess = '{"account_id":179,"uname":"dsvbdirect","full_name":"dsvb direct","email":"dsvbdirect@virob.com","is_affiliate":1,"can_sponsor":0,"account_type_name":"Customer","account_type_id":2,"currency_id":2,"language_id":0,"country_id":77,"currency_code":"INR","is_mobile_verifi	ed":0,"is_email_verified":0,"is_verified":0,"mobile":null,"phonecode":"+91","profile_image":"profile_image_blank.jpg"}';
		$pack_details = '{"package_id":2,"package_code":"1002","package_level":1,"is_refundable":1,"refundable_days":10,"expire_days":30,"package_image":"1002.jpg","is_upgradable":1,"is_adjustment_package":0,"instant_benefit_credit":0	,"currency_id":2,"price":750,"package_qv":250,"weekly_capping_qv":1000,"shopping_points_cashback":5000,"shopping_points_bonus":5000,"currency_code":"INR","package_name":"Basic","description":null,"package_image_url":"https:\/\/localhost\/dsvb_portal\/1002.jpg"}';
		$userSess = json_decode($userSess);
		$pack_details = json_decode($pack_details);
		$this->affObj->updateLineage($userSess,$pack_details);
		die; */
        $data = [];
        $data['packages'] = $this->packageObj->get_packages(['currency_id'=>$this->userSess->currency_id]);		
        $data['purchase_paymodes'] = $this->packageObj->purchase_paymodes();
		//echo"<pre>";print_r($data['purchase_paymodes']);exit;
        return view('affiliate.package.package_browse', $data);
    }

    public function paymode_select ($type = '')
    {
        $op = [];
        $data = [];
		$postdata = $this->request->all();
        if ($type == '')
        {
            $this->statusCode = $this->config->get('httperr.SUCCESS');
            $purchase_paymodes = $this->paymentObj->get_paymodes(['purpose'=>$this->config->get('constants.PAYMODE_PURPOSE_BUYPACKAGE')]);
            if (!empty($purchase_paymodes))
            {
				$ses['pack_details'] = $this->packageObj->get_packages([
                        'list'=>false,
                        'package_code'=>$postdata['id'],
                        'currency_id'=>$this->userSess->currency_id]);
						
                $this->session->set('pkSess',$ses);
				$this->session->put('pkSess.userSess',$this->userSess);
				array_walk($purchase_paymodes, function(&$pg, $key)
                {
                    if($pg->payment_type_id==$this->config->get('constants.PAYMENT_TYPES.WALLET')){
						$pg->url = route('aff.package.paymodeinfo', ['paymode'=>$pg->payment_type_id]);
					} else {
						$pg->url = route('aff.package.purchaseconfirm', ['paymode'=>$pg->payment_type_id]);
					}
                });
                $op['purchase_paymodes'] = $purchase_paymodes;
                $op['status'] = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op = ['msg'=>'Service not available', 'msgtype'=>'danger', 'status'=>$this->statusCode];
            }
        }
        else if ($type > 0 && $this->session->has('pkSess'))
        {
			$pkSess = $this->session->get('pkSess');
			$pkInfo = $pkSess['pack_details'];
            if ($type == $this->config->get('constants.PAYMENT_TYPES.WALLET'))
            {                
				$walletbal  = $this->walletObj->get_user_balance(0, ['account_id'=>$this->userSess->account_id], $this->config->get('constants.WALLETS.VP'), $this->userSess->currency_id, $this->config->get('constants.WALLET_PURPOSE.PURCHASE'));			
				//$walletbal->current_balance = 
				if(!empty($walletbal)){					
					if($walletbal->current_balance >= $pkInfo->price){
						$data['hasBalance'] = true;
						$this->session->put('pkSess.paymode',$this->config->get('constants.PAYMENT_TYPES.WALLET'));
						$this->session->put('pkSess.wallet_id',$this->config->get('constants.WALLETS.VP'));	
						$this->session->put('pkSess.currency_id',$this->userSess->currency_id);
						$data['preBalance'] = CommonLib::currency_format($walletbal->current_balance, $walletbal->currency_id);
						$data['postBalance'] =  CommonLib::currency_format(($walletbal->current_balance-$pkInfo->price),$walletbal->currency_id);
						
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					}				
					else {
						$data['hasBalance'] = false;
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					}
				}
                $op['template'] = view('affiliate.package.package_by_wallet', $data)->render();
            } 
			else if ($type != $this->config->get('constants.PAYMENT_TYPES.WALLET'))
            {
				$this->session->put('pkSess.paymode',$type);
				$this->session->put('pkSess.currency_id',$this->userSess->currency_id);
			}
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            $op = ['msg'=>'Package not found', 'msgtype'=>'danger', 'status'=>200];
        }
		//echo"<pre>"; print_r($op);exit;
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function purchase_confirm ($type = '')
    {
		$data = array();                
		if ($type != $this->config->get('constants.PAYMENT_TYPES.WALLET'))
		{
			$this->session->put('pkSess.paymode',$type);
			$this->session->put('pkSess.currency_id',$this->userSess->currency_id);
		}
		$pkSess = $this->session->get('pkSess');
		$op = $this->packageObj->doPurchase($this->session->get('pkSess'));			       
		if (!empty($op) && $op['status'] == 200)
        {
            $this->statusCode = $this->config->get('httperr.SUCCESS');
			$this->session->flash('purchase_msg', $op);
			$op['redirect'] = route('aff.package.my_packages');
        } else {
			 $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function my_packages ()
    {
        $data = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $post = $this->request->all();
        if (\Request::ajax())
        {
            if (!empty($post))
            {
                $filter['from'] = !empty($post['from']) ? $post['from'] : '';
                $filter['to'] = !empty($post['to']) ? $post['to'] : '';
                $filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
                $filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
                $filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
                $submit = isset($post['submit']) ? $post['submit'] : '';
            }
            $data['count'] = true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->packageObj->get_mypackage($data);
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal'];
                $data['start'] = !empty($post['start']) ? $post['start'] : 0;
                $data['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $data['order'] = $post['order'][0]['dir'];
                }
                unset($data['count']);
                $ajaxdata['data'] = $this->packageObj->get_mypackage($data);
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
        }			
        return view('affiliate.package.my_packages', $data);
    }

    public function upgrade_history ()
    {
        $data = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $post = $this->request->all();
        if (\Request::ajax())
        {
            if (!empty($post))
            {
                $filter['from'] = !empty($post['from']) ? $post['from'] : '';
                $filter['to'] = !empty($post['to']) ? $post['to'] : '';
                $filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
                $filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
                $filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
                $submit = isset($post['submit']) ? $post['submit'] : '';
            }
            $data['count'] = true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->packageObj->upgrade_history($data);
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal'];
                $data['start'] = !empty($post['start']) ? $post['start'] : 0;
                $data['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $data['order'] = $post['order'][0]['dir'];
                }
                unset($data['count']);
                $pkglist = $this->packageObj->upgrade_history($data);	
                array_walk($pkglist, function(&$package)
                {			 
					$package->package_qv = ($package->status == $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED')) ? $package->package_qv : '';
					//print_r($package->package_qv);exit;
                    if (!empty($package->package_image))
                    {
                        $package->package_image_url = url($package->package_image, [], true);
                    }
					if($package->status==$this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED')){
						
						$package->package_qv;
					}
					else{
					$package->package_qv='';
					}
                    $diff = date_diff(date_create($package->create_date), date_create(date('Y-m-d')));
                    $package->fullname = ucwords($package->fullname);
					$package->refundable_days = ($diff->format("%a") > 0) ? $diff->format("%R%a days") : '';
                    $package->weekly_capping_qv = CommonLib::currency_format($package->weekly_capping_qv, $package->currency_id);
                    $package->paid_amt = CommonLib::currency_format($package->paid_amt, $package->currency_id);
                    $package->refund_expire_on = showUTZ('M d, Y', $package->package_image);
                    $package->create_date = showUTZ($package->create_date,'d M, Y');
                    $package->updated_date = showUTZ($package->updated_date,'d M, Y');
                    $package->recent_package_purchased_on = !empty($package->recent_package_purchased_on) ? showUTZ($package->recent_package_purchased_on,'d M, Y'):'';
                    switch ($package->status)
                    {
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED'):
                            $package->status = '<span class="label label-success">Acivated</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CANCELLED'):
                            $package->status = '<span class="label label-info">Cancelled</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.EXPIRED'):
                            $package->status = '<span class="label label-danger">Expired</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.PENDING'):
                            $package->status = '<span class="label label-warning">Pending</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.WAIT_FOR_ACTIVATE'):
                            $package->status = '<span class="label label-info">Wait for Approvals</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.USER_APPROVALS'):
                            $package->status = '<span class="label label-warning">Not Activated</span>';
                            if ($diff->format("%a") >= 0) {
							 $package->actions[] = ['url'=>route('aff.package.refund', ['code'=>$package->purchase_code]), 'redirect'=>false, 'label'=>'Refund'];
							 $package->actions[] = ['url'=>route('aff.package.activate', ['code'=>$package->purchase_code]), 'class'=>'pkactivate_btn', 'redirect'=>false, 'label'=>trans('general.btn.activate')];
							}
							else {
								$package->action = $diff->format("%a");
							}
                            break;
                    }
                });

                $ajaxdata['data'] = $pkglist;
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
        }
        return view('affiliate.package.upgrade_history', $data);
    }

    public function purchase_activate ()
    {
        $op = [];
        $post = $this->request->all();
        if (!empty($post['code']))
        {
            $packInfo = $this->packageObj->getTopupPackageInfo(['purchase_code'=>$post['code']]);
            if (!empty($packInfo))
            {
                $packInformation = $this->packageObj->activatePackage($packInfo);
				if(!empty($packInformation)){
                     $op['msg']='Package activated successfully';
                     $op['ststus'] =$this->config->get('httperr.SUCCESS');
                     $this->statusCode = $this->config->get('httperr.SUCCESS');
               }
            }
        }
		 return $this->response->json($op, $this->statusCode,$this->headers,$this->options);
    }

    public function packageRefund ($code)
    {
        $op = [];
		$wdata['purchase_code'] = $code;
		$wdata['account_id'] = $this->userSess->account_id;
        if ($this->packageObj->packageRefund($wdata))
        {
            $statusCode = 200;
            $op['msg'] = 'Refunded successfully..';
        }
        else
        {
            $statusCode = 422;
            $op['msg'] = 'Failed to refund..';
        }
        return $this->response->json($op, $statusCode, [], JSON_PRETTY_PRINT);
    }
}