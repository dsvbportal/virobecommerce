<?php
namespace App\Http\Controllers\Franchisee;

use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\FrModel;
use App\Models\Franchisee\WalletModel;
use App\Models\Franchisee\Settings;
use App\Helpers\CommonNotifSettings;
use CommonLib;

class WalletController extends FrBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->frObj = new FrModel();
        $this->frwalletObj = new WalletModel();
		$this->settingsObj = new Settings;	
    }  
	/* 
	public function transactions(){
		$data = $filter = array(); 
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();
		$filter['account_id'] = $this->userSess->account_id;   //variable creation //
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = !empty($post['from']) ? $post['from'] : '';
			$filter['to'] = !empty($post['to']) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
			$filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
			$filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
        }
        if(\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frwalletObj->transactions($data);
			//print_r($data); exit;
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
				$ajaxdata['data'] = $this->frwalletObj->transactions($data);
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
        $data['wallet_list'] = $this->frwalletObj->get_all_wallet_list();	 
		return view('franchisee.wallet.transactions',$data);
	} */
	public function transactions(){
		$data = $filter = array(); 
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();
		$filter['account_id'] = $this->userSess->account_id;   //variable creation //
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = !empty($post['from']) ? $post['from'] : '';
			$filter['to'] = !empty($post['to']) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
			$filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
			$filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
			
        }
        if(\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->frwalletObj->transactions($data);
			//print_r($data); exit;
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
				$ajaxdata['data'] = $this->frwalletObj->transactions($data);
		
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
		$filter['account_id'] = $this->userSess->account_id;
		$filter['currency_id'] = $this->userSess->currency_id;
		$balInfo = $this->frwalletObj->my_wallets($filter);		 
		if($balInfo){			
			array_walk($balInfo, function(&$balInfos)
			{
				$balInfos->current_balance = $balInfos->currency_symbol .' '.number_format($balInfos->current_balance, \AppService::decimal_places($balInfos->current_balance), '.', ',').' '.$balInfos->currency_code;
				$balInfos->tot_credit = $balInfos->currency_symbol .' '.number_format($balInfos->tot_credit, \AppService::decimal_places($balInfos->tot_credit), '.', ',').' '.$balInfos->currency_code;
				$balInfos->tot_debit = $balInfos->currency_symbol .' '.number_format($balInfos->tot_debit, \AppService::decimal_places($balInfos->tot_debit), '.', ',').' '.$balInfos->currency_code;
			});
		}
		$data['balInfo']=$balInfo;
        $data['wallet_list'] = $this->frwalletObj->get_all_wallet_list();	
		/* $data['default_wallet_id']=$this->config->get('constants.WALLETS.VP'); */
		return view('franchisee.wallet.transactions',$data);
	}
	public function fundtransfer_history()
	{
		$data = $wdata = $filter = array();
		$post = $this->request->all();	
		$data['currencies']=$this->frwalletObj->get_currencies();
		$data['wallet_list']=$this->frwalletObj->get_all_wallet_list();
		$filter['account_id'] = $this->userSess->account_id; 
		if (isset($post['order']))
		{
			$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
			$wdata['order'] = $post['order'][0]['dir'];
		}																										
		$filter['search_term'] = $this->request->has('search_term')? $this->request->get('search_term') : '';
		$filter['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		if (\Request::ajax())        
		{
			$wdata['count'] = true;
		    $ajaxdata['recordsTotal'] = $this->frwalletObj->transfer_history_details(array_merge($wdata,$filter)); 
			
		  	$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
			
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
				$ajaxdata['recordsFiltered'] = $this->frwalletObj->transfer_history_details(array_merge($wdata,$filter));  //filtered
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				//print_r($ajaxdata);
				unset($wdata['count']);                    

				$ajaxdata['data'] = $this->frwalletObj->transfer_history_details(array_merge($wdata,$filter));  ///get data all results display//
				
			}
		    $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
		}
		else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')     //export data
		  {
			$epdata['export_data']= $this->frwalletObj->transfer_history_details(array_merge($wdata,$filter));
			
            $output = view('franchisee.wallet.fundtransfer_export_history', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=Fund_Transfer_List_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
        } 
		else if (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')   //print data
		{
			$pdata['print_data']= $this->frwalletObj->transfer_history_details(array_merge($wdata,$filter));
	        return view('franchisee.wallet.fundtransfer_print_history', $pdata);
        }
		else
		{
			return view('franchisee.wallet.fundtransfer_history',$data);
		}
	}
	
     public function my_wallet(){
		$data = array();
		$filter['account_id'] = $this->userSess->account_id;
		$filter['currency_id'] = $this->userSess->currency_id;
		$balInfo = $this->frwalletObj->my_wallets($filter);		 
		if($balInfo){			
			array_walk($balInfo, function(&$balInfos)
			  {
			  $balInfos->current_balance =\CommonLib::currency_format($balInfos->current_balance, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]); 
			 $balInfos->tot_credit =\CommonLib::currency_format($balInfos->tot_credit, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]);
			 $balInfos->tot_debit =\CommonLib::currency_format($balInfos->tot_debit, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]);  
		    });
	
			}
		 $data['balInfo']=$balInfo;
		return view('franchisee.wallet.mywallet',$data);
	}
}