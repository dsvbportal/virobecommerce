<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Config;
use App\Models\SchedulerModel;
use Request;
use View;

class SchedulerController extends Controller
{
    public function __construct ()
    {
         $this->schedulerObj = new SchedulerModel();
    }
    public function get_sales(){
			 $data['start_date'] = date('Y-m-01',strtotime('last month'));
             $data['end_date'] = date('Y-m-t',strtotime('last month'));
               $order_details=$this->schedulerObj->getSales_ByLocation($data);
			   if(!empty($order_details)){
				   foreach($order_details as $order_detail){
					   $detail['detail']=$order_detail;
					   $detail['date_for']= $data['start_date'];
					   $this->schedulerObj->Franchisee_profit_Sharing($detail);  
			    }
			}
	}
	public function Franchisee_merchantCommissionFee(){
		
		$fr_merchant_fee=$this->schedulerObj->Franchisee_merchantCommissionFee();
	}
	/* public function Franchisee_profit_Sharing(){
	    $district_id=5;
		$fr_profit_sharing=$this->schedulerObj->Franchisee_profit_Sharing($district_id); 
	} */
 }
