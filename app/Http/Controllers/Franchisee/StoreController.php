<?php
namespace App\Http\Controllers\Franchisee;
use App\Http\Controllers\FrBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Franchisee\StoreModel;
use App\Models\LocationModel;
use App\Helpers\CommonNotifSettings;
use File;
use Storage;
use CommonLib;
use Request;
use Response;

class StoreController extends FrBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->storeObj = new StoreModel();
    }  
	
	public function orders_recent() 
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
            $ajaxdata['recordsTotal'] = $this->storeObj->getOrders($data);		
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
				$ajaxdata['data'] = $this->storeObj->getOrders($data);			
            }
            $statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	   elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
        {  
            $pdata['print_data'] = $this->storeObj->getOrders(array_merge($data, $filter));
            /* return view('franchisee.reports.earned_commission_print', $pdata); */
        }
       elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
            $epdata['export_data'] = $this->storeObj->getOrders(array_merge($data, $filter));
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
		return view('franchisee.dashboard',$data);
	} 
		
}