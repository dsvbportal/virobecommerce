<?php
namespace App\Models\Franchisee;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;
use CommonLib;
class StoreModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		$this->commonObj = new CommonModel;
    }

 public function getOrders(array $arr = array(), $count = false)
    {
        extract($arr);
        $orders = DB::table($this->config->get('tables.ORDERS').' as mo')
		        ->join($this->config->get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'mo.supplier_id')
		        ->join($this->config->get('tables.STORES').' as st', 'st.store_id', '=', 'mo.store_id')
                ->join($this->config->get('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'mo.account_id')
                /* ->where('mo.supplier_id', $supplier_id) */
                ->where('mo.is_deleted', $this->config->get('constants.OFF'))
                ->selectRaw('mo.order_code, mo.order_date, mo.order_type, mo.pay_through, mo.status, CONCAT(ud.firstname,\' \',ud.lastname) as customer, mo.pay_through,mo.currency_id, um.email, um.mobile, um.user_code, mo.bill_amount,sm.company_name,sm.supplier_code,st.store_code,st.store_name,st.store_logo');
				
        if (isset($from) && !empty($from))
        {
            $orders->whereDate('mo.order_date', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $orders->whereDate('mo.order_date', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_term) && !empty($search_term))
        {
            $orders->where('mo.order_code', 'like', '%'.$search_term.'%')
                    ->orWhere('um.mobile', 'like', '%'.$search_term.'%');
        }
        if (isset($store_id) && !empty($store_id))
        {
            $orders->where('mo.store_id', $store_id);
        }
        if (isset($orderby) && isset($order))
        {
            //$orders->orderby($orderby, $order);
        }
        else
        {
            $orders->orderby('mo.order_date', 'DESC');
        }
        if ($count)
        {
            return $orders->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $orders->skip($start)->take($length);
            }
            $orders = $orders->get();
            array_walk($orders, function(&$order)
            {
                $order->order_date = showUTZ($order->order_date, 'h:i A, d M Y');
                $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
                $order->order_type =$this->config->get('constants.ORDER.PAY_THROUGH.'.$order->order_type.'.'.$order->pay_through);
                 $order->status_class = Config('dispclass.seller.order.status.'.$order->status);
                if ($order->status == 1)
                {
                    $order->status = 'Success';
                }
                else
                {
                    $order->status = 'Pending';
                }
			     $order->logo = asset($this->config->get('path.SELLER.LOGO_IMG_PATH.LOCAL').$order->store_logo);
                /* $order->actions = [];
                $order->actions['details'] = ['url' => route('fr.orders', ['id' => $order->order_code]), 'redirect' => false, 'label' => 'Order Details']; */
            });
            return $orders;
        }
    }
		
	 
}