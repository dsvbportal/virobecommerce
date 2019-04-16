<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\AdminWithdrawals;
use App\Models\LocationModel;
use App\Helpers\CommonNotifSettings;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use Validator;

class AdminWithdrawalsController extends BaseController
{
    public $data = array();

    public function __construct ()
    {
        parent::__construct();
        $this->withdrawalsObj = new AdminWithdrawals();
		$this->lcObj = new LocationModel();		
    }  
   public function WithdrawalsList($status='') {		
   
		$data = $filter = array();
		$postdata = $this->request->all();	
		$data['payout_types'] = $this->withdrawalsObj->get_payout_types();
		$data['currency_list']=$this->commonObj->get_currencies_list();
		$data['formUrl'] = $this->request->fullUrl();
		if(!empty($status) && $status=='history'){
			$filters['status'] = [$this->config->get('constants.WITHDRAWAL_STATUS.CONFIRMED')];
		}
		else {
			 // $filters['status'] = [$this->config->get('constants.WITHDRAWAL_STATUS.PENDING'),$this->config->get('constants.WITHDRAWAL_STATUS.PROCESSING'),$this->config->get('constants.WITHDRAWAL_STATUS.CANCELLED')];
		     $filters['status'] =(isset($postdata['status']) && !empty($postdata['status'])) ? $postdata['status'] : $this->config->get('constants.WITHDRAWAL_STATUS.PENDING');
		}		  
		if (!empty($postdata))  {			
		  $filters['uname'] = (isset($postdata['username']) && !empty($postdata['username'])) ? $postdata['username'] : '';
		 /*  $filters['withdrawal_status'] = (isset($postdata['withdraw_status']) && !empty($postdata['withdraw_status'])) ? $postdata['withdraw_status'] : $this->config->get('constants.WITHDRAWAL_STATUS.PENDING'); */
	      $filters['currency'] = (isset($postdata['currency']) && !empty($postdata['currency'])) ? $postdata['currency'] : '';
		  $filters['payout_type'] = (isset($postdata['payout_type']) && !empty($postdata['payout_type'])) ? $postdata['payout_type'] : '';
		  $filters['from'] = (isset($postdata['from_date']) && !empty($postdata['from_date'])) ? $postdata['from_date'] : '';
		  $filters['to'] = (isset($postdata['to_date']) && !empty($postdata['to_date'])) ? $postdata['to_date'] : '';
		  $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 		  
		}
		if (Request::ajax()) {
			$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
			$ajaxdata['url'] = URL::to('/');
			$ajaxdata['data'] = array();			
			$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = 0;
			$dat = array_merge($data, $filters);			
			$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->withdrawalsObj->withdrawals_list($dat, true);          
			if ($ajaxdata['recordsTotal'] > 0){
				$filter = array_filter($filters);
			   if (!empty($filter)){
					$data = array_merge($data, $filter);
					$ajaxdata['recordsFiltered'] = $this->withdrawalsObj->withdrawals_list($data, true);
				}
			   if (!empty($ajaxdata['recordsFiltered'])){
					$data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
					$data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : $this->config->get('constants.DATA_TABLE_RECORDS');
					
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
					$data = array_merge($data, $filters);
					$ajaxdata['data'] = $this->withdrawalsObj->withdrawals_list($data);
				}
			}
			return Response::json($ajaxdata);		
        }
		elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){			
			$edata['pending_withdrawals_list'] = $this->withdrawalsObj->withdrawals_list(array_merge($data,$filters));	
            $output = view('admin.withdrawals.withdrawals_list_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=Withdrawals list -' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
		 elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){			
			$pdata['pending_withdrawals_list'] = $this->withdrawalsObj->withdrawals_list(array_merge($data,$filters));			
            return view('admin.withdrawals.withdrawals_list_print',$pdata);
        } 
        else{
            return view('admin.withdrawals.withdrawal_list',$data);  
		} 
	}

	
		public function withdrawals_confirm(){
		   $postdata = $this->request->all();	
		   $data=$this->withdrawalsObj->confirm_withdrawals($postdata);
		   $op['msg']    = 'Something went wrong';
		   $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		   if($data){
			$op['msg']    = trans('admin/withdrawals.confirm_withdraw');
		    $op['status'] = $this->statusCode = 200;
		   }
		   return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}
		public function withdrawals_process(){
		   $op=[];
		   $postdata = $this->request->all();	
		   $data=$this->withdrawalsObj->withdrawal_process($postdata);
		   $op['msg']    = 'Something went wrong';
		   $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		   if($data){
			$op['msg']    = trans('admin/withdrawals.confirm_withdraw');
		    $op['status'] = $this->statusCode = 200;
		   }
		   return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}
	public function cancel_Withdrawal(){
		$postdata =  $this->request->all();
		$postdata['wd_id'] = 61;
		$postdata['account_id'] = $this->account_id;
		$result = $this->withdrawalsObj->cancel_withdrawal($postdata);
		$op['msg']    = 'Something went wrong';
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		if($result){
			$op['success'] = 'SUCCESS!';
			$op['msg']     = 'Successfully cancelled';
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		
	}
	
	public function withdrawals_details($trans_id){
		$postdata 			    = $this->request->all();
		$postdata['trans_id']   = $trans_id;
		$data['wd_details']     = $this->withdrawalsObj->getWithdrawalDetails($postdata);
		$op['content'] 		    = view('admin.withdrawals.withdrawal_details',$data)->render();
		$op['status'] 			='ok';
		$this->statusCode       = $this->config->get('httperr.SUCCESS');
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}

}

?>
