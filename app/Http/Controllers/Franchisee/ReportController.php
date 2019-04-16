<?php
namespace App\Http\Controllers\Franchisee;

use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\FrModel;
use App\Models\Franchisee\FrReports;
use App\Helpers\CommonNotifSettings;
use CommonLib;
use Request;
use Response;
use URL;
class ReportController extends FrBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->frObj = new FrModel();	
		$this->frReportObj = new FrReports();
    }  
 /* TDS */
	public function tds_deducted_details() 
	{ 	
		$data = $filter = array(); 
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();		
		$filter['account_id'] = $this->userSess->account_id;  
        if (!empty($post))  
        {			
            $filter['from'] = !empty($post['from_date']) ? $post['from_date'] : '';
			$filter['to'] = !empty($post['to_date']) ? $post['to_date'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
			$filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
			$filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
			$filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frReportObj->tds_deducted_details($data);		
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
				$ajaxdata['data'] = $this->frReportObj->tds_deducted_details($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');	
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
		elseif (isset($post['printbtn']) && $post['printbtn'] == 'Print')
        {  
            $pdata['print_data'] = $this->frReportObj->tds_deducted_details(array_merge($data, $filter));
            return view('franchisee.reports.tds_deducted_details_print', $pdata);
        }
        elseif (isset($post['exportbtn']) && $post['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->frReportObj->tds_deducted_details(array_merge($data, $filter));
            $output = view('franchisee.reports.tds_deducted_details_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=TDS Deducted Report'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }	
		return view('franchisee.reports.tds_deducted_details',$data);
	}
	
	/* Franchisee Earn Commission */	
	public function earned_commission() 
	{ 	
		$data = $filter		 = array(); 
		$data['account_id']  = $this->userSess->account_id;
		$post 				 = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? trim($post['search_term']) : '';
			$filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frReportObj->earned_commission($data);	
			
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
				$ajaxdata['data'] = $this->frReportObj->earned_commission($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	   elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
        {  
            $pdata['print_data'] = $this->frReportObj->earned_commission(array_merge($data, $filter));
            return view('franchisee.reports.earned_commission_print', $pdata);
        }
       elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
            $epdata['export_data'] = $this->frReportObj->earned_commission(array_merge($data, $filter));
            $output = view('franchisee.reports.earned_commission_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Earned Commission Report'.getGTZ('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }	
		return view('franchisee.reports.earned_commission',$data);
	} 
	
	public function earned_commission_details($commission_type,$created_on_date){
	    $data = $filter		 = array(); 
		$ajaxdata=array();
   	    $data['account_id']  = $this->userSess->account_id; 
		$data['created_on_date']  = $created_on_date;
		$data['commission_type']  = $commission_type;
		$type=$this->frReportObj->commission_type($commission_type);
		$data['type'] =trans('transactions.franchisee_commission_type.'.$type->commission_type_id.'');
		
		$post = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? trim($post['search_term']) : '';
			$filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
			if($data['commission_type']=='MCMF'){
               $ajaxdata['recordsTotal'] = $this->frReportObj->Merchant_enrollment_fee($data);
			}
			else if($data['commission_type']=='PS'){
				 $ajaxdata['recordsTotal'] = $this->frReportObj->get_franchisee_profit_sharing($data);
			} 
			else{
				$ajaxdata['recordsTotal'] = $this->frReportObj->earned_commission_details($data);
			}
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
				if($data['commission_type']=='MCMF'){
				   	$ajaxdata['data'] = $this->frReportObj->Merchant_enrollment_fee($data);			
				}
				else if($data['commission_type']=='PS'){
				   	$ajaxdata['data'] = $this->frReportObj->get_franchisee_profit_sharing($data);			
				} 
				else{
				    $ajaxdata['data'] = $this->frReportObj->earned_commission_details($data);
				}					
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	   elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
        {  
            $pdata['print_data'] = $this->frReportObj->earned_commission_details(array_merge($data, $filter));
            return view('franchisee.reports.earned_commission_details_print', $pdata);
        }
       elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
            $epdata['export_data'] = $this->frReportObj->earned_commission_details(array_merge($data, $filter));
            $output = view('franchisee.reports.earned_commission_details_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Earned Commission Report'.getGTZ('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }	
		if($data['commission_type']=='MCMF'){
			return view('franchisee.reports.merchant_enrollment_fee_details',$data);
		 }
		if($data['commission_type']=='PS'){
			return view('franchisee.reports.profit_sharing_details',$data);
		}
		else{
			return view('franchisee.reports.earned_commission_details',$data);
		}
	 }
	 
	public function Activity_log(){
	    $data = $filter		 = array(); 
		$ajaxdata=array();
   	    $data['account_id']  = $this->userSess->account_id;
		$post 				 = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? trim($post['search_term']) : '';
			$filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frReportObj->activity_log_details($data);		
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
				$ajaxdata['data'] = $this->frReportObj->activity_log_details($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
		return view('franchisee.reports.activity_log_history',$data);
	}
	
	
	public function merchant_due(){
	  
	  
	    $data = $filter		 = array(); 
		$ajaxdata=array();
   	    $data['account_id']  = $this->userSess->account_id;
		$post 				 = $this->request->all();	
        if(!empty($post))  
        {			
		    $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : '';
		    $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? trim($post['search_term']) : '';
			$filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';
        }
        if(Request::ajax())       
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frReportObj->activity_log_details($data);		
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
				$ajaxdata['data'] = $this->frReportObj->activity_log_details($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
		return view('franchisee.reports.merchant_due',$data);
	}
	
	
	 
}