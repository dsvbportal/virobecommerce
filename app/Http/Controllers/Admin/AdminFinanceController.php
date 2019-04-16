<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\AdminController;
use App\Models\Admin\AdminFinance;
use App\Models\BaseModel;
use App\Models\Admin\Member;
use App\Models\Admin\AffModel;
use App\Models\Admin\Franchisee;
use CommonLib;
use Response; 
use Request;
use View;
use URL;
use Validator;
class AdminFinanceController extends AdminController
{

    public $request;

    public function __construct ()
    {
        parent::__construct();
        $this->financeObj = new AdminFinance();
        $this->baseObj = new BaseModel();
        $this->memberObj = new Member();
		$this->affObj = new AffModel();
		$this->frObj = new Franchisee();
    }

    public function merchant_finance ($type = null, $mrcode = null)
    {
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        if ($this->request->isMethod('post'))
        {
            $postdata['admin_id'] = $this->userSess->account_id;
            $postdata['admin_role_id'] = $this->userSess->account_type_id;
            $result = $this->financeObj->add_fund_merchant($postdata);
            if (!empty($result))
            {
                $op['status'] = 'ok';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $result;
            }
            else
            {
                $op['status'] = 'err';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.fund_transfer_fail');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['type'] = $this->config->get('constants.TRANS_TYPE.'.strtoupper($type));
            $data['mrcode'] = $mrcode;
            $data['wallets'] = $this->financeObj->get_wallets();
            $data['currencies'] = $this->baseObj->get_currencies();
	        $data['settings'] = $this->financeObj->fund_transfer_settings();
	        return view('admin.finance.merchant_credit_debit', $data);
        }
    }

    public function find_merchant ()
    {
        $postdata = $this->request->all();
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        $op = array();
        $bal_arr = array();
        if (!empty($postdata))
        {
            $merchant = $this->baseObj->get_merchant_details($postdata);
            if (!empty($merchant))
            {
                $balance = $this->financeObj->wallet_balance(['account_id'=>$merchant->account_id]);
                foreach ($balance as $bal)
                {
                    if (!empty($bal->currency_id))
                    {
                        $bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
                    }
                }
                $op['status'] = 'ok';
                $op['merchant'] = $merchant;
                $op['balance'] = $bal_arr;
                $this->status_code = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = 'err';
                $this->status_code = $this->config->get('httperr.SUCCESS');
                $op['merchant'] = trans('admin/finance.merchant_not_found');
            }
        }
        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }

    public function trasnferTo_affiliate ($type = null, $member = null)
    {
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        if ($this->request->isMethod('post'))
        {			
            $postdata['admin_id'] = $this->userSess->account_id;
            $postdata['admin_role_id'] = $this->userSess->account_type_id;
            $result = $this->financeObj->add_fund_member($postdata);

            if (!empty($result))
            {
                $op['status'] = 'ok';
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $result;
            }
            else
            {
                $op['status'] = 'err';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.fund_transfer_fail');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['wallets'] = $this->financeObj->get_wallets($this->config->get('constants.WALLET_PURPOSE.FUNDTRANSFER'));
            $data['currencies'] = $this->baseObj->get_currencies();
            $data['settings'] = $this->financeObj->fund_transfer_settings();
            $data['type'] = $this->config->get('constants.TRANS_TYPE.'.strtoupper($type));
            $data['member'] = $member;
	        return view('admin.finance.member_credit_debit', $data);
        }
    }

    public function find_member ()
    {   
        $postdata = $this->request->all(); 
		 //print_r($postdata);exit;
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        $op = array();
        $bal_arr = array();
        if (!empty($postdata))
        {
			$wallets = $this->commonObj->getSettings('aff_wallet_access',true);			
			if(!empty($wallets) && $wallets[$postdata['trans_type']]){
				$walletIds = $wallets[$postdata['trans_type']];		
				$userdetails = $this->memberObj->get_member_details($postdata);
				if (!empty($userdetails))
				{				
					$userdetails->is_franchasee = 0;					
					
					if($userdetails->account_type_id != $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE') && $userdetails->is_affiliate == 1){
						$afwallets = $this->commonObj->getSettings('affiliate_wallets');	
						if(!empty($afwallets)){//														
							$walletIds = array_intersect($walletIds,$afwallets);	
						}						
						$userdetails->is_franchasee = 0;
						$userdetails->fr_url = route('admin.finance.fund-transfer.to_affiliate');
						$op['status'] = $this->config->get('httperr.SUCCESS');						
					} 
					elseif($userdetails->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE') && $userdetails->is_affiliate == 0){
						
						$frInfo = $this->frObj->get_franchisee_details(['account_id'=>$userdetails->account_id]);
						if(!empty($frInfo)){
							
							$frLoc = $this->frObj->get_franchisee_access_location($frInfo->franchisee_id);
							
							if(!empty($frLoc)){								
								$locArr = '';
								$locArr[] = $frLoc->access_city_name;
								$locArr[] = $frLoc->access_district_name;
								$locArr[] = $frLoc->access_state_name;
								$locArr[] = $frLoc->access_region_name;
								$locArr[] = $frLoc->access_country_name;								
								$userdetails = (object)array_merge((array)$userdetails,(array)$frInfo);
								$userdetails->is_franchasee = 1;
								$userdetails->location = strtoupper(implode(',',array_filter($locArr)));
								$userdetails->fr_url = route('admin.finance.fund-transfer.to_franchasee');
								$op['status'] = $this->config->get('httperr.SUCCESS');
							}
							else {
								$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
								$op['msg'] = "Channel Partner access location not yet assigned.";
							}
						}
						else {
							$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
							$op['msg'] = "Channel Partner access location not yet assigned.";
						}
					}
					
					if(isset($op['status']) && $op['status'] == $this->config->get('httperr.SUCCESS')){
						$balance = $this->financeObj->wallet_balance(['account_id'=>$userdetails->account_id,'currency_id'=>$userdetails->currency_id,'wallet'=>$walletIds]);
						foreach ($balance as $bal)
						{
							if (!empty($bal->currency_id))
							{
								$bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
								$bal_arr[$bal->wallet_id]['wallet'] = $bal->wallet;
							}
						}
						$op['userdetails'] = $userdetails;
						$op['balance'] = $bal_arr;
						$this->session->put('trsession',$userdetails);
						$op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');
					}
				}
				else
				{
					$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('admin/finance.member_not_found');
				}
			}
			else
			{
				$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = "Currently this service not enabled.";
			}
        }
        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }

    public function dsa_finance ()
    {
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        if ($this->request->isMethod('post'))
        {
            $postdata['admin_id'] = $this->userSess->account_id;
            $postdata['admin_role_id'] = $this->userSess->account_type_id;
            $result = $this->financeObj->add_fund_dsa($postdata);
            if (!empty($result))
            {
                $op['status'] = 'ok';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $result;
            }
            else
            {
                $op['status'] = 'err';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.fund_transfer_fail');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['wallets'] = $this->financeObj->get_wallets();
            $data['currencies'] = $this->baseObj->get_currencies();
            $data['settings'] = $this->financeObj->fund_transfer_settings();
            return view('admin.finance.dsa_credit_debit', $data);
        }
    }

    public function find_dsa_acc ()
    {
        $postdata = $this->request->all();
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        $op = array();
        $bal_arr = array();
        if (!empty($postdata))
        {
            $userdetails = $this->baseObj->get_dsa_details($postdata);
            if (!empty($userdetails))
            {
                $balance = $this->financeObj->wallet_balance(['account_id'=>$userdetails->account_id]);
                foreach ($balance as $bal)
                {
                    if (!empty($bal->currency_id))
                    {
                        $bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
                    }
                }
                $op['status'] = 'ok';
                $op['userdetails'] = $userdetails;
                $op['balance'] = $bal_arr;
                $this->status_code = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = 'err';
                $this->status_code = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.dsa_not_found');
            }
        }

        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }
   public function fund_transfer_history ()
    {
	 $data = $filter = array();
     $postdata = $this->request->all();
      if (!empty($postdata)){	
		  $filters['wallet_id'] = (isset($postdata['wallet_id']) && !empty($postdata['wallet_id'])) ? $postdata['wallet_id'] : '';
		  $filters['terms'] = (isset($postdata['terms']) && !empty($postdata['terms'])) ? $postdata['terms'] : '';
		  $filters['type'] = (isset($postdata['sysrole']) && !empty($postdata['sysrole'])) ? $postdata['sysrole'] : '';
          $filters['from'] = (isset($postdata['from_date']) && !empty($postdata['from_date'])) ? $postdata['from_date'] : '';
		  $filters['to'] = (isset($postdata['to_date']) && !empty($postdata['to_date'])) ? $postdata['to_date'] : '';
        }
     if (Request::ajax()) {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
			
            $dat = array_merge($data, $filters);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->fund_transfer_history($dat, true); 
			if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filters);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] =$this->financeObj->fund_transfer_history($data, true);
                }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->financeObj->fund_transfer_history($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else
        {
			$data['eWallet_list'] = $this->financeObj->get_wallet_list();
			$data['sys_roles'] = $this->financeObj->get_roles();
	        return view('admin.finance.fund_transfer_history', $data);
        }
    }
	
	public function admin_fund_transfer_history(){
		
		$postdata = $this->request->all();
        $data = $filter = $ajaxdata = [];
        $ajaxdata['data'] = [];
        if ($postdata)
        {
            $filter['from'] = isset($postdata['from_date']) ? $postdata['from_date'] : null;
            $filter['to'] = isset($postdata['to_date']) ? $postdata['to_date'] : null;
            $filter['terms'] = isset($postdata['terms']) ? $postdata['terms'] : null;
           
        }
        if ($this->request->isMethod('post'))
        {
			$data['sysrole'] =  isset($postdata['sysrole']) ? $postdata['sysrole'] : '';
	        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->admin_fund_transfer_history($data, true);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                if (!empty(array_filter($filter)))
                {
                    $data = array_merge($data, $filter);
			        $ajaxdata['recordsFiltered'] = $this->financeObj->admin_fund_transfer_history($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['length'] = $ajaxdata['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                    $data['page'] = $ajaxdata['cpage'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                    $data['start'] = isset($postdata['start']) && !empty($postdata['start']) ? $postdata['start'] : ($data['page'] - 1) * $data['length'];
                    $ajaxdata['move_next'] = (($data['start'] + $data['length']) < $ajaxdata['recordsFiltered']) ? true : false;
                    $data['orderby'] = isset($postdata['orderby']) ? $postdata['orderby'] : (isset($postdata['order'][0]['column']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : null);
                    $data['order'] = isset($postdata['order']) ? (is_array($postdata['order']) ? $postdata['order'][0]['dir'] : $postdata['order']) : 'ASC';
                    $ajaxdata['data'] = $this->financeObj->admin_fund_transfer_history($data);
                }
            }
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['url'] = url('/admin');
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['tickets'] = $this->financeObj->admin_fund_transfer_history($data);
            $output = View::make($this->view_path.'tickets.member_tickets_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Member_Tickets_List_'.showUTZ("d_M_Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['tickets'] = $this->financeObj->admin_fund_transfer_history($data);
            return View::make($this->view_path.'tickets.member_tickets_print', $res);
        }
        else
        {
			$data['sys_roles'] = $this->financeObj->get_roles();
	        return view('admin.finance.admin_transfer_history', $data);
        }
	}

    public function userTransactionLog ($for = null, $account_id = null)
    {
        return $this->transactionLog($for, $account_id, $this->config->get('constants.ACCOUNT.TYPE.USER'));
    }

    public function retailerTransactionLog ($for = null, $account_id = null)
    {
        return $this->transactionLog($for, $account_id, $this->config->get('constants.ACCOUNT.TYPE.MERCHANT'));
    }

  /*   public function transactionLog ($for = null, $account_id = null, $account_type_id = null)
    {
        $data = $ajaxdata = [];
        $data['account_id'] = $account_id;		

		$data['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');
		
        $ajaxdata['data'] = [];
        $postdata = $this->request->except(['from_date', 'to_date', 'terms']);
        $filter = $this->request->only(['from_date', 'to_date', 'terms']);
        $for = ($for == null && isset($postdata['submit']) && !empty($postdata['submit'])) ? $postdata['submit'] : $for;
        if ($this->request->isMethod('post'))
         {			
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->transaction_log($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            if (!empty($ajaxdata['recordsFiltered']))
            {
                if (!empty(array_filter($filter)))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->financeObj->transaction_log($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    if (isset($postdata['start']))
                    {
                        $data['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
                        $data['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
                    }
                    $ajaxdata['data'] = $this->financeObj->transaction_log($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if ($for == 'Export' || $for == 'export')
        {
            $coulumns = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'format'=>'long-date', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'amount', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'handleamt', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'paidamt', 'format'=>'currency-colored', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'format'=>'status', 'data'=>['color'=>'statusCls'], 'align'=>'center'],
            ];
            $exp = CommonLib::export(trans('admin/finance.transaction_log'), $coulumns, $this->financeObj->transaction_log($data));
            return $this->response->make($exp->body, 200, $exp->headers);
        }
        else if ($for == 'Print' || $for == 'print')
        {
            $ajaxdata['title'] = trans('admin/finance.transaction_log');
            $ajaxdata['columns'] = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'famount', 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'fhandleamt', 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'fpaidamt', 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'align'=>'center'],
            ];
            $ajaxdata['data'] = $this->financeObj->transaction_log($data);
            return \View::make('print-layout', $ajaxdata);
        }
        else
        {
            return view('admin.finance.transaction_log', $data);
        }
    }  */
	
    public function transactionLog($for = null, $account_id = null, $account_type_id = null){
		$data = $filter = array(); 
		$data['currency']=$data['currency_list']=$this->commonObj->get_currencies_list();
		$data['eWallet_list'] = $this->financeObj->get_wallet_list();
		$post = $this->request->all();
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		    $filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
			$filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : '';
			$filter['type'] = $this->request->has('type') ? $this->request->get('type') : '';
			$filter['currency_id'] = $this->request->has('currency_id') ? $this->request->get('currency_id') : '';
			$filter['wallet_id'] = $this->request->has('wallet_id') ? $this->request->get('wallet_id') : '';
			$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		    $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
        }
        if(\Request::ajax())         //checks if call in ajax
         {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->financeObj->transaction_log($data);
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
				$ajaxdata['data'] = $this->financeObj->transaction_log($data);
				//print_r($ajaxdata['data']); exit;
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
          }
		elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
	    {
			$edata['transcation_details'] = $this->financeObj->transaction_log(array_merge($data,$filter));	
            $output = view('admin.finance.transcation_log_export',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=transcation log ' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
	 elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$pdata['transcation_details'] = $this->financeObj->transaction_log(array_merge($data,$filter));
            return view('admin.finance.transcation_log_print',$pdata);
        } 
		return view('admin.finance.transaction_log', $data);
	}
    public function transactionDetails ($id)
    {
        $op = $data = [];
        $data['id'] = $id;
        $details = $this->financeObj->getTransactionDetail($data);
        if (!empty($details))
        {
            $op['details'] = $details;
            $op['stauts'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.not_found', ['which'=>trans('general.label.transaction')]);
            $op['stauts'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function admin_credit_debit_history ()
    {
        $data = $ajaxdata = $filter = [];
        $postdata = $this->request->except(['from_date', 'to_date', 'terms', 'trans_type']);
        $filter = $this->request->only(['from_date', 'to_date', 'terms', 'trans_type']);
        if ($this->request->isMethod('post'))
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->admin_credit_debit_history($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['data'] = [];
            if (!empty($ajaxdata['recordsFiltered']))
            {
				$data['trans_type'] = isset($postdata['trans_type'])?$postdata['trans_type']:'';
                $filter = array_filter($filter);
		        if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
			        $ajaxdata['recordsFiltered'] = $this->financeObj->admin_credit_debit_history($data, true);
                }
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
		        $ajaxdata['data'] = $this->financeObj->admin_credit_debit_history($data);
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['tickets'] = $this->financeObj->admin_credit_debit_history($data);
            $output = View::make($this->view_path.'tickets.member_tickets_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Member_Tickets_List_'.showUTZ("d_M_Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['tickets'] = $this->financeObj->admin_credit_debit_history($data);
            return View::make($this->view_path.'tickets.member_tickets_print', $res);
        }
        else
        {
            return view('admin.finance.admin_credit_debit_trans', $data);
        }
    }

    /* Order payments */

    public function online_payments ()
    {
        $data = $ajaxdata = $filter = [];
        $postdata = $this->request->except(['from_date', 'to_date', 'terms', 'status', 'purpose']);
        $filter = $this->request->only(['from_date', 'to_date', 'terms', 'status', 'purpose']);
        $filter['terms'] = trim($filter['terms']);
        $for = (isset($postdata['submit']) && !empty($postdata['submit'])) ? $postdata['submit'] : '';
        if ($this->request->isMethod('post'))
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->online_payments($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['data'] = [];
            if ($ajaxdata['recordsFiltered'])
            {
                $filter = array_filter($filter, function($field)
                {
                    return !($field == "");
                });
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->financeObj->online_payments($data, true);
                }
                if ($ajaxdata['recordsFiltered'])
                {
                    if (isset($postdata['start']))
                    {
                        $data['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
                        $data['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
                    }
                    $ajaxdata['data'] = $this->financeObj->online_payments($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if ($for == 'Export' || $for == 'export')
        {
            $coulumns = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'format'=>'long-date', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'amount', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'handleamt', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'paidamt', 'format'=>'currency-colored', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'format'=>'status', 'data'=>['color'=>'statusCls'], 'align'=>'center'],
            ];
            $exp = CommonLib::export(trans('admin/finance.transaction_log'), $coulumns, $this->financeObj->transaction_log($data));
            return $this->response->make($exp->body, 200, $exp->headers);
        }
        else if ($for == 'Print' || $for == 'print')
        {
            $ajaxdata['title'] = trans('admin/finance.transaction_log');
            $ajaxdata['columns'] = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'famount', 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'fhandleamt', 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'fpaidamt', 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'align'=>'center'],
            ];
            $ajaxdata['data'] = $this->financeObj->online_payments($data);
            return \View::make('print-layout', $ajaxdata);
        }
        else
        {
            $data['status'] = trans('db_trans.payment_gateway_response.status');
            $data['purpose'] = trans('admin/finance.purpose');
            return view('admin.finance.online_payments', $data);
        }
    }

    /* Order payments Details */

    public function online_payments_details ($id = null)
    {
        $postdata = $this->request->all();
        $data['gateway_responce'] = '';
        if ($id != null)
        {
            $postdata['id'] = $id;
        }
        $data['details'] = $this->financeObj->getway_payment_details($postdata);
        if (!empty($data['details']) && !empty($data['details']->response))
        {
            $data['gateway_responce'] = json_decode($data['details']->response);
        }
        $a = view('admin.finance.online_payment_detail', $data)->render();
        $op['content'] = $a;
        $op['status'] = 'ok';
        return $this->response->json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
    }

    public function updateStatus ($id = null)
    {
        if ($id != null)
        {
            $postdata['id'] = $id;
            $result = $this->financeObj->update_payment_status($postdata);

            return $this->response->json(['msg'=>'Payment updated successfully', 'status'=>'ok'], $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
        }
        return $this->response->json(['msg'=>'Something went wrong', 'status'=>'err'], $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
    }

    public function refundPayment ($id = null)
    {
        if ($id != null)
        {
            $postdata['id'] = $id;
            $result = $this->financeObj->refundPayment($postdata);
            if (!empty($result))
            {
                return $this->response->json(['msg'=>'Payment refunded successfully', 'status'=>'ok'], $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
            }
            else
            {
                return $this->response->json(['msg'=>'Something went wrong', 'status'=>'err'], $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
            }
        }
    }

    public function confirmPayment ($id = null)
    {
        if ($id != null)
        {
            $postdata['id'] = $id;
            $result = $this->financeObj->confirmPayment($postdata);
            if (!empty($result))
            {
                return $this->response->json(['msg'=>'Payment successfully added', 'status'=>'ok'], $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
            }
            else
            {
                return $this->response->json(['msg'=>'Something went wrong', 'status'=>'err'], $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
            }
        }
    }
	
	public function affiliate_ewallet(){
        $data = $filter = array();
		$ewallet_id = '';
		$postdata 	= $this->request->all();
		$data['eWallet_list'] 	= 	$this->financeObj->get_wallet_list();
		$data['currency_list']	=	$this->commonObj->get_currencies_list();
		if (!empty($postdata))  {			
		  $filters['uname'] = (isset($postdata['username']) && !empty($postdata['username'])) ? $postdata['username'] : '';
		  $filters['currency'] = (isset($postdata['currency']) && !empty($postdata['currency'])) ? $postdata['currency'] : '';
		  $filters['ewallet_id'] = (isset($postdata['ewallet_id']) && !empty($postdata['ewallet_id'])) ? $postdata['ewallet_id'] : '';
		  $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
		}
		if (Request::ajax()) {
				$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
				$ajaxdata['url'] = URL::to('/');
				$ajaxdata['data'] = array();
				$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = 0;
				if(!array_filter($filters)){
					return Response::json($ajaxdata);
			  }	
            else {			
		 		$dat = array_merge($data, $filters);			
				$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->getWalletBalnceTotal($dat, true);          
				if ($ajaxdata['recordsTotal'] > 0){
					$filter = array_filter($filters);
				   if (!empty($filter)){
						$data = array_merge($data, $filter);
						$ajaxdata['recordsFiltered'] = $this->financeObj->getWalletBalnceTotal($data, true);
					}
				   if (!empty($ajaxdata['recordsFiltered'])){
						$data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
						$data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
						
						if (isset($data['order'])) {
							$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
							$data['order'] = $postdata['order'][0]['dir'];
						}
						$data = array_merge($data, $filters);

						$ajaxdata['data'] = $this->financeObj->getWalletBalnceTotal($data);
					}
				}
				return Response::json($ajaxdata);
		   }
        }
		

         elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$edata['wallet_summarry_details'] = $this->financeObj->getWalletBalnceTotal(array_merge($data,$filter));	
            $output = view('admin.finance.affiliates_wallet_summary_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=wallet Summary' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
           }
           elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$pdata['wallet_summarry_details'] = $this->financeObj->getWalletBalnceTotal(array_merge($data,$filter));
            return view('admin.finance.affiliates_wallet_summary_print',$pdata);
         }  
		 else{
            return view('admin.finance.affiliate_ewallet',$data);  
		 } 
	}
	
	 public function wallet_transcation(){
		$data = $filter = array(); 
		$data['currency']=$data['currency_list']=$this->commonObj->get_currencies_list();
		$data['eWallet_list'] = $this->financeObj->get_wallet_list();
		$post = $this->request->all();
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		    $filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
			$filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : '';
			$filter['type'] = $this->request->has('type') ? $this->request->get('type') : '';
			$filter['currency_id'] = $this->request->has('currency_id') ? $this->request->get('currency_id') : '';
			$filter['wallet_id'] = $this->request->has('wallet_id') ? $this->request->get('wallet_id') : '';
			$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		    $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
        }
        if(\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
			$data['payment_type_id']=$this->config->get('constants.PAYMENT_TYPES.WALLET');
            $ajaxdata['recordsTotal'] = $this->financeObj->transaction_log($data);
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
				$ajaxdata['data'] = $this->financeObj->transaction_log($data);
				//print_r($ajaxdata['data']); exit;
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
	    {
			
			$data['payment_type_id']=$this->config->get('constants.PAYMENT_TYPES.WALLET');
			$edata['wallet_transcation_details'] = $this->financeObj->transaction_log(array_merge($data,$filter));	
            $output = view('admin.finance.wallet_transcation_export',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=Wallet Transcation' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
	 elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
		    $data['payment_type_id']=$this->config->get('constants.PAYMENT_TYPES.WALLET');
			$pdata['wallet_transcation_details'] = $this->financeObj->transaction_log(array_merge($data,$filter));
            return view('admin.finance.wallet_transcation_print',$pdata);
        } 
		return view('admin.finance.wallet_transcation', $data);
	}
	
	public function trasnferTo_franchasee ($type = null, $member = null)
    {
		die;
        $postdata = $this->request->all();
        $op = array();
		$trSess = $this->session->get('trsession');
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			
        if ($this->request->ajax())
        {			
            $postdata['admin_id'] 		= $this->userSess->account_id;
            $postdata['admin_role_id']  = $this->userSess->account_type_id;
            $postdata['ewallet_id']  	= $postdata['wallet'];
			$postdata['account_id']  = $trSess->account_id;
            $postdata['currency_id']  = $trSess->currency_id;			
            $op = $this->financeObj->add_fund_franchisee($postdata);
		
			$this->statusCode = $op['status'];
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['wallets'] = $this->financeObj->get_wallets();
            $data['currencies'] = $this->baseObj->get_currencies();
            $data['settings'] = $this->financeObj->fund_transfer_settings();
            $data['type'] = $this->config->get('constants.TRANS_TYPE.'.strtoupper($type));
            $data['member'] = $member;
	        return view('admin.finance.member_credit_debit', $data);
        }
    }
	
	public function PaymentGateway_transcation(){
		
		$data = $filter = array(); 
		$data['currency']=$data['currency_list']=$this->commonObj->get_currencies_list();
		$data['eWallet_list'] = $this->financeObj->get_wallet_list();
		$data['payment_types']=$this->financeObj->get_payment_types();
		$post = $this->request->all();
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		    $filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
			$filter['search_text'] = $this->request->has('search_text') ? $this->request->get('search_text') : '';
			$filter['type'] = $this->request->has('type') ? $this->request->get('type') : '';
			$filter['currency_id'] = $this->request->has('currency_id') ? $this->request->get('currency_id') : '';
			$filter['payment_type_id'] = $this->request->has('payment_type_id') ? $this->request->get('payment_type_id') : '';
			$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		    $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
		  
        }
        if(\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
			
            $ajaxdata['recordsTotal'] = $this->financeObj->PaymentGateway_transcation($data);
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
				$ajaxdata['data'] = $this->financeObj->PaymentGateway_transcation($data);
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
	elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
	    {
			
			$data['payment_type_id']=$this->config->get('constants.PAYMENT_TYPES.WALLET');
			$edata['wallet_transcation_details'] = $this->financeObj->PaymentGateway_transcation(array_merge($data,$filter));	
            $output = view('admin.finance.payment_gateway_export',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=PaymentGateway Transcation' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
	 elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
		    $data['payment_type_id']=$this->config->get('constants.PAYMENT_TYPES.WALLET');
			$pdata['wallet_transcation_details'] = $this->financeObj->PaymentGateway_transcation(array_merge($data,$filter));
            return view('admin.finance.payment_gateway_print',$pdata);
        } 
		return view('admin.finance.payment_gateway_transcation', $data);
		
	}
}
