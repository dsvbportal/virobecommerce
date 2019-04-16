<?php

namespace App\Models;
use App\Models\BaseModel;
use App\Models\Franchisee\Bonus;
use DB;
use Request;
use Response;
use App\Models\Commonsettings;

class SchedulerModel extends BaseModel
{
    public function __construct ()
    {
         parent::__construct();
	     $this->bonusObj = new Bonus();		 
    }
  
  public function Franchisee_merchantCommissionFee(){
	 
	 $start_date = date('Y-m-01',strtotime('last month'));
	 $end_date = date('Y-m-t',strtotime('last month'));
	 
	 $comm_details=  DB::table($this->config->get('tables.FRANCHISEE_MERCHANT_FEE').' as fms')
	                      ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'fms.account_id')
						  ->where('fms.status', $this->config->get('constants.ON'))
						  ->where('fms.is_deleted', $this->config->get('constants.OFF'))
						  ->whereDate('fms.created_on','>=',$start_date)
						  ->whereDate('fms.created_on','<=',$end_date)
						  ->groupby('fms.franchisee_id')
						  ->select(DB::Raw('sum(fms.commission_amount) as commission_amount'),'fms.franchisee_id','fms.account_id','ap.currency_id')
						  ->get();
			 if(!empty($comm_details)) {
				  $data=[];
				  DB::beginTransaction();
				    foreach($comm_details as $k=>$data){
			           $fr_commission['account_id']=$data->account_id;
			           $fr_commission['commission_type']=$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.MCMF');
			           $fr_commission['currency_id']=$data->currency_id;
			           $fr_commission['currency_rate']=$this->config->get('constants.ON');
			           $fr_commission['commission_amount']=$data->commission_amount;
			           $fr_commission['net_pay']=$data->commission_amount;
			           $fr_commission['remark']=addSlashes(json_encode(['data'=>['period'=> $start_date,'date_format'=>'M-Y']]));
					   $fr_commission['statementline_id']=config('stline.MERCHANT_FR_FEE_COMM.CREDIT');
					   $fr_commission['from_date']=$start_date;
			           $fr_commission['status']=$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.PENDING');
			           $fr_commission['created_date']=getGTZ();
					   $fr_com_id=  DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
						->insertGetId($fr_commission);	 
						 if(!empty($fr_com_id)){ 
							   $fr_commission['fr_com_id']= $fr_com_id;
						       $this->releaseFranchiseeCommission($fr_commission); 
						 } 
					}
		        } 
      }
   public function releaseFranchiseeCommission($commission_details)
    {       
		if (!empty($commission_details)){
			  extract($commission_details);
				$cdata = [];
				$cdata['fr_com_id'] = $fr_com_id;
				$cdata['wallet'] = $this->config->get('constants.WALLETS.VI');
				$cdata['account_id'] = $account_id;
				$cdata['amount'] = $commission_amount;
				$cdata['currency_id'] = $currency_id;
				$cdata['tax'] = 0;
				$cdata['ngoAmt'] = 0;
				$cdata['netpay'] = $commission_amount;
				$cdata['remark']=['period'=> $from_date,'date_format'=>'M-Y'];
				$cdata['statementline_id'] = $this->config->get('stline.MERCHANT_FR_FEE_COMM.CREDIT');	
				$res = $this->bonusObj->credit_bonus($cdata);	
				 if($res){
					$upData = [
						'status' =>$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'),
						'confirmed_date' => getGTZ()
					];
					DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')
						->where('fc.fr_com_id', $fr_com_id)
						->update($upData);
					DB::commit();
			}
            else{
                 DB::rollback();
                  return false;
         }
	   }
	}
	public function Franchisee_profit_Sharing($arr){
    extract($arr);
	$sales_amount=$detail->sales_amount;
		if(!empty($detail->district_id)){
			$fr_district_details= $this->get_franchisee_details(['district_id'=>$detail->district_id]);
			  if($fr_district_details){
				  
					   if(!empty($fr_district_details->profit_sharing)){
						 $tax=0;
						 $amount=$sales_amount*$fr_district_details->profit_sharing/100;
						 DB::beginTransaction();
						   $fr_commission['account_id']=$fr_district_details->account_id;
						   $fr_commission['commission_type']=$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.PS');
						   $fr_commission['currency_id']=$fr_district_details->currency_id;
						   $fr_commission['currency_rate']=$this->config->get('constants.currency_rate.ON');
						   $fr_commission['amount']=$sales_amount;
						   $fr_commission['commission_amount']=$amount;
						   $fr_commission['commission_perc']=$fr_district_details->profit_sharing;
						   $fr_commission['tax']=($amount*$tax/100);
						   $fr_commission['net_pay']=$amount-$fr_commission['tax'];						  
						   $fr_commission['remark']=addSlashes(json_encode(['data'=>['period'=> $date_for,'date_format'=>'M-Y']]));
						   $fr_commission['statementline_id']=config('stline.FR_PROFIT_SHARING.CREDIT');
						   $fr_commission['from_date']=$date_for;
						   $fr_commission['status']=$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.PENDING');
						   $fr_commission['created_date']=getGTZ();
						   $fr_com_id=  DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
							->insertGetId($fr_commission);	 
						     if(!empty($fr_com_id)) { 
							      $fr_commission['fr_com_id']= $fr_com_id;
						          $res=$this->releaseProfitSharing($fr_commission);
								if(!empty($res)) {
								       DB::commit();
						          }
								   else{
									  DB::rollback();
                                     return false;  
							   }
						} 
				}
			     /* Code For State */	
                if(!empty($detail->state_id)){
				     $fr_state_detail= $this->get_franchisee_details(['state_id'=>$detail->state_id]);
					
					   if(!empty($fr_state_detail->profit_sharing)){					     
						   $tax=0;
						   $comm_amt=$sales_amount*$fr_state_detail->profit_sharing/100;
						   $fra_commission['account_id']=$fr_state_detail->account_id;
						   $fra_commission['commission_type']=$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.PS');
						   $fra_commission['currency_id']=$fr_state_detail->currency_id;
						   $fra_commission['currency_rate']=$this->config->get('constants.currency_rate.ON');
						   $fra_commission['amount']=$sales_amount;
						   $fra_commission['commission_amount']=$comm_amt;
						   $fra_commission['commission_perc']=$fr_state_detail->profit_sharing;
						   $fra_commission['tax']=($comm_amt*$tax/100);
						   $fra_commission['net_pay']=$comm_amt-$fra_commission['tax'];		
						   $fra_commission['remark']=addSlashes(json_encode(['data'=>['period'=> $date_for,'date_format'=>'M-Y']]));
						   $fra_commission['statementline_id']=config('stline.FR_PROFIT_SHARING.CREDIT');
						   $fra_commission['from_date']=$date_for;
						   $fra_commission['status']=$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.PENDING');
						   $fra_commission['created_date']=getGTZ();
						   $fra_com_id=  DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
							->insertGetId($fra_commission);	 
						     if(!empty($fra_com_id)){ 
							      $fra_commission['fr_com_id']= $fra_com_id;
						          $result=$this->releaseProfitSharing($fra_commission);
								   if(!empty($result)) {
								       DB::commit();
						           }
								  else{
									  DB::rollback();
				                      return false;  
							   } 
						   } 
					 }
				 }
			  }
			else{
				 $fr_state_details=$this->get_franchisee_details(['state_id'=>$detail->state_id]);
				
					if(!empty($fr_state_details)){
						
						  if(!empty($fr_state_details->profit_sharing_without_district)){
							  $tax_per=0;
							  $amount=$sales_amount*$fr_state_details->profit_sharing_without_district/100;
							   DB::beginTransaction();
							   $fr_commission['account_id']=$fr_state_details->account_id;
							   $fr_commission['commission_type']=$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.PS');
							   $fr_commission['currency_id']=$fr_state_details->currency_id;
							   $fr_commission['currency_rate']=$this->config->get('constants.currency_rate.ON');
							   $fr_commission['amount']=$sales_amount;
							   $fr_commission['commission_amount']=$amount;
							   $fr_commission['commission_perc']=$fr_state_details->profit_sharing_without_district;
							   $fr_commission['tax']=($amount*$tax_per/100);
							   $fr_commission['net_pay']=$amount-$fr_commission['tax'];
							   $fr_commission['remark']=addSlashes(json_encode(['data'=>['period'=> $date_for,'date_format'=>'M-Y']]));
							   $fr_commission['statementline_id']=config('stline.FR_PROFIT_SHARING.CREDIT');
							   $fr_commission['from_date']=$date_for;
							   $fr_commission['status']=$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.PENDING');
							   $fr_commission['created_date']=getGTZ();
							   $fr_comm_id=  DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
								->insertGetId($fr_commission);	 
								 if(!empty($fr_comm_id)){ 
									  $fr_commission['fr_com_id']= $fr_comm_id;
									  $result=$this->releaseProfitSharing($fr_commission); 
									   if(!empty($result)) {
										   DB::commit();
									   }
									  else{
										  DB::rollback();
										  return false;  
								   }   
							} 
					   }
				 } 
		 } 
     /* Code End */		 
	}
}	  
	
	
	public function get_franchisee_details($arr){
	 extract($arr);
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
				   ->join($this->config->get('tables.FRANCHISEE_MST').' as fs','fs.franchisee_id', '=', 'fal.franchisee_id')
				   ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'fs.account_id');
					   if(isset($district_id) && !empty($district_id)){
							$qry->where('fal.access_location_type','=',$this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'));
							$qry->where('fal.relation_id','=',$district_id);
					  }
					  if(isset($state_id) && !empty($state_id)){
							$qry->where('fal.access_location_type','=',$this->config->get('constants.FRANCHISEE_TYPE.STATE'));
							$qry->where('fal.relation_id','=',$state_id);
					  } 
						  $qry->where('fal.status','=',$this->config->get('constants.ON'));
						  $qry->select('fal.account_id','fal.profit_sharing','fal.profit_sharing_without_district','fal.state_id','fs.franchisee_id','fs.franchisee_type','ap.currency_id');
						    $res = $qry->first();
							
					 if($res){
						  return $res;
					 }	
					   else{
						  return false;
					 }			   
	           }	
     /* public function get_district_info($district_id){

		    $fr_state = DB::table($this->config->get('tables.LOCATION_DISTRICTS').' as ld')
					    ->where('ld.district_id','=',$district_id)
					    ->where('ld.status','=',$this->config->get('constants.ON'))
						  ->select('ld.state_id')
						  ->first();
						  if($fr_state){
								return $fr_state;
							 }	
						   else{
							  return false;
					   }
	      } */
		  

    public function releaseProfitSharing($commission_details) {       
		if (!empty($commission_details)){
			  extract($commission_details);
				$cdata = [];
				$cdata['fr_com_id'] = $fr_com_id;
				$cdata['wallet'] = $this->config->get('constants.WALLETS.VI');
				$cdata['account_id'] = $account_id;
				$cdata['amount'] = $commission_amount;
				$cdata['currency_id'] = $currency_id;
				$cdata['tax'] = 0;
				$cdata['ngoAmt'] = 0;
				$cdata['netpay'] = $commission_amount;
				$cdata['remark']=['period'=> $from_date,'date_format'=>'M-Y'];
				$cdata['statementline_id'] = $this->config->get('stline.FR_PROFIT_SHARING.CREDIT');	
				$res = $this->bonusObj->credit_bonus($cdata);	
				 if($res){
					$upData = [
						'status' =>$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'),
						'confirmed_date' => getGTZ()
					];
					DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')
						->where('fc.fr_com_id', $fr_com_id)
						->update($upData);
						return true;
			     }
				else{
					 return false;
			  }
	       }
	   }
  public function getSales_ByLocation($arr){
	  extract($arr);
	  $qry = DB::table($this->config->get('tables.ORDER_COMMISSION').' as oc')
		         ->join($this->config->get('tables.ORDERS').' as or','or.order_id', '=', 'oc.order_id')

				 ->join($this->config->get('tables.STORES').' as st', 'st.store_id', '=', 'or.store_id')
				
				  ->join($this->config->get('tables.ADDRESS_MST').' as add', function($subquery)
							{ 
							 $subquery->on('add.address_id', '=', 'st.address_id')
										->where('add.address_type_id','=',$this->config->get('constants.ADDRESS.PRIMARY'))
										->where('add.post_type','=',$this->config->get('constants.ADDRESS_POST_TYPE.STORE'));
							})
				  ->where('or.status', $this->config->get('constants.ORDER.STATUS.PAID'))
				  ->where('or.payment_status', $this->config->get('constants.ORDER.PAYMENT_STATUS.PAID'))
				  ->whereIn('or.pay_through',[$this->config->get('constants.ORDER.PAID_THROUGH.PAY'),$this->config->get('constants.ORDER.PAID_THROUGH.SHOP_AND_EARN'),$this->config->get('constants.ORDER.PAID_THROUGH.REDEEM')])
				  ->whereDate('oc.created_on','>=',$start_date)
				  ->whereDate('oc.created_on','<=',$end_date)
				  
				  ->select(DB::Raw('sum(oc.system_comm) as sales_amount'),'add.district_id','add.state_id')
				  ->groupby('add.state_id','add.district_id')
				  ->get();
				 if(!empty($qry)){
					    return $qry;
				 }
					else{
						return false;
					}
      }  
  }