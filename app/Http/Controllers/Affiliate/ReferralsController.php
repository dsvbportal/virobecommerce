<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\User;
use App\Models\LocationModel;
use App\Models\Affiliate\Referrals;
use App\Helpers\CommonNotifSettings;
use Config;

class ReferralsController extends AffBaseController
{

    public function __construct ()
    {
        parent::__construct();
        $this->referralsObj = new Referrals();
        $this->locObj = new LocationModel();
    }
	
	public function my_referred_customers ()
    {  
        $data = $wdata = $filter = array();
        $post = $this->request->all();	
        $data['account_id'] = $wdata['account_id'] = $this->userSess->account_id;
        if (!empty($post))
        {
	        $filter['from'] = (isset($post['from_date']) && !empty($post['from_date'])) ? $post['from_date']:'';	 
	        $filter['to'] = (isset($post['to_date']) && !empty($post['to_date'])) ? $post['to_date']:'';	 
	        $filter['search_term'] = (isset($post['search_term']) && !empty($post['search_term'])) ? trim($post['search_term']):'';	 
	        $filter['exportbtn'] = (isset($post['exportbtn']) && !empty($post['exportbtn'])) ? $post['exportbtn']:'';	 
	        $filter['printbtn'] = (isset($post['printbtn']) && !empty($post['printbtn'])) ? $post['printbtn']:'';	 
	        $filter['filterchk'] = (isset($post['filterchk']) && !empty($post['filterchk'])) ? $post['filterchk']:'';	 
        }      
        if ($this->request->ajax())
        {
            $wdata['count'] = true;
            if (isset($post['order']))
            {
                $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                $wdata['order'] = $post['order'][0]['dir'];
            }
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->referralsObj->my_referred_customers($data['account_id'], $wdata); //total records//		
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                if (!empty($filter))
                {  
                    $ajaxdata['recordsFiltered'] = $this->referralsObj->my_referred_customers($data['account_id'], array_merge($wdata, $filter));
                }
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                unset($wdata['count']);
                $ajaxdata['data'] = $this->referralsObj->my_referred_customers($data['account_id'], array_merge($wdata, $filter));
            }		
            return $this->response->json($ajaxdata);
        }
        elseif (isset($post['printbtn']) && $post['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->referralsObj->my_referred_customers($data['account_id'], array_merge($wdata, $filter));
            return view('affiliate.referrals.my_referred_customers_print', $pdata);
        }
        elseif (isset($post['exportbtn']) && $post['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->referralsObj->my_referred_customers($data['account_id'], array_merge($wdata, $filter));
            $output = view('affiliate.referrals.my_referred_customers_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=my_reffered_customers_'.getGTZ("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.referrals.my_referred_customers', $data);
        }
    }

    public function my_referrals ()
    {
        $data = $wdata = $filter = array();
        $data['account_id'] = $wdata['account_id'] = $this->userSess->account_id;
		/* print_r($data['account_id']); die; */
        $post = $this->request->all();
        if (!empty($post))
        {
            if ($this->request->has('from_date'))
                $filter['from'] = $this->request->get('from_date');
            if ($this->request->has('to_date'))
                $filter['to'] = $this->request->get('to_date');
            if ($this->request->has('generation'))
                $filter['generation'] = $this->request->get('generation');
            if ($this->request->has('exportbtn'))
                $filter['exportbtn'] = $this->request->get('exportbtn');
            if ($this->request->has('printbtn'))
                $filter['printbtn'] = $this->request->get('printbtn');
            if ($this->request->has('filterchk'))
                $filter['filterchk'] = $this->request->get('filterchk');
        }
        if (\Request::ajax())
        {
            $wdata['count'] = true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->referralsObj->my_referrals($data['account_id'], $wdata); //total records//
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {

                if (!empty($filter))
                {
                    $ajaxdata['recordsFiltered'] = $this->referralsObj->my_referrals($data['account_id'], array_merge($wdata, $filter));
                }
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $wdata['parent_details'] = $this->referralsObj->getUser_lineage(['account_id'=>$this->userSess->account_id]);
				/* print_R($wdata['parent_details']); die; */
                $ajaxdata['data'] = $this->referralsObj->my_referrals($data['account_id'], array_merge($wdata, $filter));  ///get data all results display//
            }
            return $this->response->json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $wdata['parent_details'] = $this->referralsObj->getUser_lineage(['account_id'=>$this->userSess->account_id]);
            $pdata['print_data'] = $this->referralsObj->my_referrals($data['account_id'], array_merge($wdata, $filter));
            return view('affiliate.referrals.my_referrals_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $wdata['parent_details'] = $this->referralsObj->getUser_lineage(['account_id'=>$this->userSess->account_id]);
            $epdata['export_data'] = $this->referralsObj->my_referrals($data['account_id'], array_merge($wdata, $filter));
            $output = view('affiliate.referrals.my_referrals_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=my_referrals_list_'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return \Response::make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.referrals.my_referrals', $data);
        }
    }

    public function my_team_report ()
    {
        $data = $wdata = $filter = array();
        $post = $this->request->all();
        $data['account_id'] = $wdata['account_id'] = $this->userSess->account_id;
        if (!empty($post))
        {
            if ($this->request->has('from_date'))
                $filter['from'] = $this->request->get('from_date');
            if ($this->request->has('to_date'))
                $filter['to'] = $this->request->get('to_date');
            if ($this->request->has('generation'))
                $filter['generation'] = $this->request->get('generation');
            if ($this->request->has('exportbtn'))
                $filter['exportbtn'] = $this->request->get('exportbtn');
            if ($this->request->has('printbtn'))
                $filter['printbtn'] = $this->request->get('printbtn');
            if ($this->request->has('filterchk'))
                $filter['filterchk'] = $this->request->get('filterchk');
        }
		
        $wdata['parent_details'] = $this->referralsObj->getUser_lineage(['account_id'=>$this->userSess->account_id]);
        if ($this->request->ajax())
        {
            $wdata['count'] = true;
            if (isset($post['order']))
            {
                $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                $wdata['order'] = $post['order'][0]['dir'];
            }
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->referralsObj->my_team_reports($data['account_id'], $wdata); //total records//
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                if (!empty($filter))
                {
                    $ajaxdata['recordsFiltered'] = $this->referralsObj->my_team_reports($data['account_id'], array_merge($wdata, $filter));
                }
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                unset($wdata['count']);
                $ajaxdata['data'] = $this->referralsObj->my_team_reports($data['account_id'], array_merge($wdata, $filter));
            }
            return $this->response->json($ajaxdata);
        }
        elseif (isset($post['printbtn']) && $post['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->referralsObj->my_team_reports($data['account_id'], array_merge($wdata, $filter));
            return view('affiliate.referrals.my_team_print', $pdata);
        }
        elseif (isset($post['exportbtn']) && $post['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->referralsObj->my_team_reports($data['account_id'], array_merge($wdata, $filter));
            $output = view('affiliate.referrals.my_team_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=my_team_list_'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
			$data['generationSales'] = ['1'=>0,'2'=>0,'3'=>0];
	        $downline_sales = $this->referralsObj->my_downline_sales($data);
			if(!empty($downline_sales)){
				foreach($downline_sales as $sales){
					$data['generationSales'][$sales->rank] = $sales->qv;
				}
			}
            return view('affiliate.referrals.my_team', $data);
        }
    }

    public function get_direct_geneology ($account_id = 0)
    {
        $op = array();
        $wdata['parent_acinfo'] = $this->referralsObj->getUser_treeInfo([$account_id]);
        $wdata['account_id'] = $this->request->account_id;
        $direct = $this->referralsObj->get_direct_users($wdata);
        if ($direct)
        {
            $op['status'] = "ok";
            $op['direct'] = $direct;
        }
        else
        {
            $op['status'] = "err";
            $op['direct'] = NULL;
        }
        return $this->response->json($op, 200, [], JSON_NUMERIC_CHECK);
    }

    public function my_geneology ()
    {
        $data = $opArray = array();
        $post = $this->request->all();
        if ($this->request->ajax())
        {
            $my_treeinfo = $this->referralsObj->getUser_treeInfo(['uname'=>$post['uname']]);
	        $opArray['tree_layout'] = $my_treeinfo->rank;
            $opArray['gusers'] = $this->referralsObj->get_genealogy_users(['account_id'=>$my_treeinfo->account_id, 'parent_acinfo'=>$my_treeinfo]);
            $opArray['status'] = $this->statusCode = 200;
            return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            return view('affiliate.referrals.my_geneology', $data);
        }
    }

    public function refer_and_earn ()
    {
        $data = array();
        if (\Request::ajax())
        {
            $postdata = $this->request->all();			
			if(isset($postdata['email']) && filter_var($postdata['email'], FILTER_VALIDATE_EMAIL)) {
			
				CommonNotifSettings::affNotify('AFFILIATE.INVITE_FRIEND', NULL, Config::get('constants.ACCOUNT_TYPE.USER'), ['email'=>$postdata['email'], 'referral_url'=>$postdata['referral_url'], 'full_name'=>$this->userSess->full_name], true, false);
				
				$op['msg'] = trans('affiliate/referrels/general.referral_thanks_msg');
				$op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');
			}else {
		    	$op['msg'] = 'Please enter your friend email';
				$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
			}           
            return $this->response->json($op, $this->status_code);
        }
        else
        {
            return view('affiliate.referrals.refer_and_earn', $data);
        }
    }

    public function my_directsss ()
    {
        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $post = $this->request->all();
        $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
        $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
        $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
        $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
        $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
        $filter['filterchk'] = $this->request->has('filterchk') ? $this->request->get('filterchk') : '';

        if ($this->request->ajax())
        {
            $ajaxdata['recordsTotal'] = $this->referralsObj->my_directs($data['account_id'], $wdata, true); //total records//
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                if (!empty(array_filter($filter)))
                {
                    array_merge($wdata, $filter);

                    $ajaxdata['recordsFiltered'] = $this->referralsObj->my_directs($data['account_id'], $wdata, true);
                }
                if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
                {
                    $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                    $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                    if (isset($post['order']))
                    {
                        $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                        $wdata['order'] = $post['order'][0]['dir'];
                    }
                    // print_r(array_merge($wdata,$filter)); die;

                    $ajaxdata['data'] = $this->referralsObj->my_directs($data['account_id'], array_merge($wdata, $filter));
                    if ($ajaxdata['data'])
                    {
                        array_walk($ajaxdata['data'], function(&$data)
                        {
                            $data->recent_package_purchased_on = date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                            $data->signedup_on = date('d-M-Y H:i:s', strtotime($data->signedup_on));
                            $data->username = trans('affiliate/referrels/my_referrels.user_name');
                        });
                    }
                }
            }
            //print_r($ajaxdata); die;

            return $this->response->json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->referralsObj->my_directs($data['account_id'], array_merge($wdata, $filter));
            if ($pdata['print_data'])
            {
                array_walk($pdata['print_data'], function(&$data)
                {
                    $data->recent_package_purchased_on = date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                    $data->signedup_on = date('d-M-Y H:i:s', strtotime($data->signedup_on));
                });
            }
            return view('affiliate.referrals.my_directs_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->referralsObj->my_directs($data['account_id'], array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    $data->recent_package_purchased_on = date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                    $data->signedup_on = date('d-M-Y H:i:s', strtotime($data->signedup_on));
                });
            }
            $output = view('affiliate.referrals.my_directs_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=my_directs_list_'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.referrals.my_directs', $data);
        }
    }
	
		
	

}