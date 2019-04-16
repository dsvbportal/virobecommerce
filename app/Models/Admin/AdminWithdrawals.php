<?php
namespace App\Models\Admin;
use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;
use CommonLib;
use AppService;
class AdminWithdrawals extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
    }
   public function get_payout_types ()
    {
        $result = DB::table($this->config->get('tables.PAYMENT_TYPES'))
                  ->where('status', $this->config->get('constants.ON'))
                  ->select('payment_type','payment_type_id')
				  ->get();
			if(!empty($result)){
					return $result;
				}
    }
   public function withdrawals_list($arr = array(), $count = false){
     
	  extract($arr);
	      $users = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as a')
	                       ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'a.account_id')
						   
						   ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
						   ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
						   ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
						   ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'a.currency_id')
						   ->leftjoin($this->config->get('tables.PAYMENT_TYPES').' as pay_t', 'pay_t.payment_type_id', '=', 'a.payment_type_id')
						   ->where('a.is_deleted','=',$this->config->get('constants.OFF'))
						   ->where('um.is_affiliate','=',1)
						   //->where('a.status_id','=',$status) 
						  ->select(DB::raw('a.*,um.uname,um.account_id,um.email,c.currency as code,concat_ws(" ",ud.firstname,ud.lastname) as fullname,lc.country as country,um.mobile,pay_t.payment_type,c.currency_symbol,c.decimal_places'));
         if (isset($from) && !empty($from) && isset($to) && !empty($to))
            {
                $users->whereRaw("DATE(a.created_on) >='".date('Y-m-d', strtotime($from))."'");
                $users->whereRaw("DATE(a.created_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (isset($from) && !empty($from))
            {
                $users->whereRaw("DATE(a.created_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $users->whereRaw("DATE(a.created_on) >='".date('Y-m-d', strtotime($to))."'");
            }
	       if (isset($payout_type) && !empty($payout_type))
            {
                $users->where("a.payment_type_id", $payout_type);
            }			   
			if (isset($currency) && !empty($currency))
            {
                $users->where("a.currency_id", $currency);
            }		
			/* if (isset($status) && !empty($status))
            {
                $users->whereIn("a.status_id", $status);
            }	
			 */
            if (isset($uname) && !empty($uname))
            {
               $users->Where("um.uname",'like',$uname);
            }	
            if (isset($orderby) && isset($order)) {
                $users->orderBy($orderby, $order);
            }
            else {
                $users->orderBy('a.wd_id', 'DESC');
            }
            if (isset($length) && !empty($length)) {
                $users->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
                return $users->count();
            }
           $withdrawals_details = $users->get();
		  
 /* echo $this->config->get('constants.WITHDRAWAL_STATUS.am.payment_status'); die; */

 
	if(!empty($withdrawals_details)){
		  array_walk($withdrawals_details, function(&$t)	{
			$t->created_on = showUTZ($t->created_on, 'd-M-Y H:i:s');
			$t->amount = \CommonLib::currency_format($t->amount, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);	
			$t->handleamt =  CommonLib::currency_format($t->handleamt, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->code, 'decimal_places'=>$t->decimal_places]);	
			$t->paidamt =  CommonLib::currency_format($t->paidamt, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->code, 'decimal_places'=>$t->decimal_places]);	
			$t->expected_on = showUTZ($t->expected_on, 'd-M-Y');
			$t->updated_on = showUTZ($t->updated_on, 'd-M-Y');
			$t->payment_status = ucfirst(strtolower($this->config->get('constants.WITHDRAWAL_STATUS.'.$t->status_id.'')));
			$t->status_class   = $this->config->get('dispclass.withdrawal_status.'.$t->status_id.'');
		    $t->actions 	   = [];
			$t->actions[] 	   = ['class'=>'details','label'=>'Details','url'=>route('admin.withdrawals.details', ['trans_id'=>$t->transaction_id])];
			if(($t->status_id != 3) && ($t->status_id != 4)){
				$t->actions[]  = ['url'=>route('admin.withdrawals.confirm', ['account_id'=>$t->account_id, 'status'=>'1']),'class'=> 'change_status', 'data'=>[
                            'account_id'=>$t->account_id,
						    'status'=>'1',
						    'withdrawal_id'=>$t->wd_id,
                         ],'label'=>'Confirm'];
			}
			if(($t->status_id != 3) && ($t->status_id != 4)&&($t->status_id != 2)){
				$t->actions[] = ['url'=>route('admin.withdrawals.process'),'class'=> 'withdraw_process', 'data'=>[
                            'account_id'=>$t->account_id,
						    'status'=>'2',
						    'withdrawal_id'=>$t->wd_id,
                            ],'label'=>'Processing'];
			}
					
		});
				return !empty($withdrawals_details) ? $withdrawals_details : [];			
			}
       }

     public function confirm_withdrawals($arr = array()){
		  extract($arr);
		   $data=[];
         if(isset($withdrawal_id) && !empty($withdrawal_id)){
			 $data['trans_data']=$msg;
			 $data['status_id']=$status;
			    $query= DB::table(config('tables.WITHDRAWAL_MST'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                            ->where('wd_id', $withdrawal_id)
                            ->update($data);
							if(!empty($query)){
								return true;
							 }
					       return false;
							}
         }
	  public function withdrawal_process($arr = array()){
		extract($arr);
		   $data=[];
         if(isset($withdraw_id) && !empty($withdraw_id)){
			    $data['status_id']=$status;
			    $query= DB::table(config('tables.WITHDRAWAL_MST'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                            ->where('wd_id', $withdraw_id)
                            ->update($data);
							if(!empty($query)){
								return true;
							}
					   return false;
	            } 
         }

	   
	public function cancel_withdrawal($arr){
		extract($arr);
		$res = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wid')
				   ->whereIn('payment_status',[config('constants.WITHDRAWAL_STATUS.PENDING')])
				   ->whereIn('status_id',[config('constants.WITHDRAWAL_STATUS.PENDING')])
				   ->where('transaction_id',$trans_id)
				   //->where('account_id',$account_id)
				   ->select('wd_id','account_id','payment_type_id','currency_id','wallet_id','amount')
				   ->first();
	
		if(!empty($res)){
			$status = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wid')
			  ->whereIn('payment_status',[config('constants.WITHDRAWAL_STATUS.PENDING'),config('constants.WITHDRAWAL_STATUS.PROCESSING')])
			  ->whereIn('status_id',[config('constants.WITHDRAWAL_STATUS.PENDING'),config('constants.WITHDRAWAL_STATUS.PROCESSING')])
			  ->where('wd_id',$res->wd_id)
			  ->update(['status_id'=>config('constants.WITHDRAWAL_STATUS.CANCEL'),'payment_status'=>config('constants.WITHDRAWAL_STATUS.CANCEL'),'cancelled_on'=>getGTZ(),'updated_by'=>$account_id]);
			if($status)	{
				$credit_trans = [
					'to_account_id'=>$res->account_id,
					'to_wallet_id'=>$this->config->get('constants.WALLETS.VI'),
					'currency_id'=>$res->currency_id,
					'amt'=>$res->amount,
					'paidamt'=>$res->amount,
					'from_transaction_id'=>AppService::getTransID($res->account_id),
					'relation_id'=>$res->wd_id,
					'payment_type_id'=>$res->payment_type_id,
					'statementline_id'=>config('stline.WITHDRAWAL_REFUND'),
					'transaction_for'=>'WITHDRAW_CANCEL',
					'tds'=>0,
					'debit_remark_data'=>['amount'=>CommonLib::currency_format($res->amount,$res->currency_id)],
					'credit_remark_data'=>['amount'=>CommonLib::currency_format($res->amount,$res->currency_id)]
				];
				$result = $this->updateAccountTransaction($credit_trans,false,true);
				return true;
			}  
		}else{
			return false;
		}
				  
	}
	
	public function getWithdrawalDetails(array $arr = array())
    {
        extract($arr);
        $query = DB::table($this->config->get('tables.ACCOUNT_WITHDRAWAL').' as wid')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wid.payment_type_id')   
				->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'wid.account_id')  				
				->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')  				
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($wt)
                {
                    $wt->on('wt.wallet_id', '=', 'wid.wallet_id');                    
                })
                ->join($this->config->get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'wid.currency_id')
                ->where('wid.is_deleted', $this->config->get('constants.OFF')) 
                ->where('wid.transaction_id', $trans_id);
         $query->selectRaw('pt.payment_type, wid.transaction_id,wid.status_id,wid.status_id as status, wid.amount, wid.paidamt, wid.handleamt, wid.created_on, wid.expected_on, wid.cancelled_on, wid.confirmed_on, ci.currency, ci.currency_symbol, wid.account_info,wid.reason, wid.payment_details, wt.wallet as from_wallet, wid.wd_id,am.user_code,am.uname,ad.firstname,ad.lastname');
                 $withdrawal = $query->first();
		//print_r($withdrawal);exit;
        if (!empty($withdrawal))
        {
            $withdrawal->amount 	  = $withdrawal->currency_symbol.' '.number_format($withdrawal->amount, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->paidamt 		= $withdrawal->currency_symbol.' '.number_format($withdrawal->paidamt, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->handleamt    	= $withdrawal->currency_symbol.' '.number_format($withdrawal->handleamt, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->expected_on  	= ($withdrawal->expected_on != null) ? showUTZ($withdrawal->expected_on, 'd-M-Y') : '';
            $withdrawal->created_on   	= ($withdrawal->created_on != null) ? showUTZ($withdrawal->created_on) : '';
            $withdrawal->confirmed_on 	= !empty($withdrawal->confirmed_on) ? showUTZ($withdrawal->confirmed_on) : '';
            $withdrawal->cancelled_on 	= !empty($withdrawal->cancelled_on) ? showUTZ($withdrawal->cancelled_on) : '';
           // $withdrawal->conversion_details = ($withdrawal->conversion_details != null) ? json_decode($withdrawal->conversion_details) : '';
            $withdrawal->reason 	  	=	 ($withdrawal->reason != null) ? json_decode($withdrawal->reason) : '';
            $withdrawal->account_info 	= json_decode($withdrawal->account_info);
            $withdrawal->fullname 		= $withdrawal->firstname.' '.$withdrawal->lastname;
            $withdrawal->title 		  	= trans('general.withdraw_title',['payment_type'=>$withdrawal->payment_type,'trans_id'=>$withdrawal->transaction_id]);
            $withdrawal->payment_details = ($withdrawal->payment_details) ? json_decode($withdrawal->payment_details) : '';

            if (!empty($withdrawal->account_info))
            {
                array_walk($withdrawal->account_info, function(&$a, $k)
                {					
					$a = ['label'=>trans('affiliate/withdrawal/withdrawal.account_details.'.$k), 'value'=>$a];					
                });
            }
            if (!empty($withdrawal->payment_details))
            {
                array_walk($withdrawal->payment_details, function(&$a, $k)
                {
                    $a = ['label'=>trans('affil/withdrawal.payment_details.'.$k), 'value'=>$a];
                });
            }
            $withdrawal->account_info = array_values((array) $withdrawal->account_info);
          
            $withdrawal->status_class = $this->config->get('dispclass.withdrawal_status.'.$withdrawal->status);
            $withdrawal->status = trans('general.withdrawal_status.'.$withdrawal->status);
        }
        return $withdrawal;
    }


}
?>