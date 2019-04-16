<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use Request;
use Response;
use App\Models\Affiliate\AffiliateReports;
use App\Models\Affiliate\Wallet;
use App\Models\Commonsettings;

class AffiliatereportsController extends AffBaseController
{

    public function __construct ()
    {
        parent::__construct();
        $this->affiliatereportObj = new AffiliateReports();
		$this->walletObj = new Wallet;
        $this->commonObj = new Commonsettings();
    }

    public function ambassador_bonus ()
    {
        $data = $wdata = $filter = array();
        $data['from'] = '';
        $data['to'] = '';
        $data['step'] = $data['level'] = $data['type_of_package'] = '';
        $data['account_id'] = $this->userSess->account_id;
        $postdata = $this->request->all();
		
        $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
        $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
        $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
        $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
        $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
        $filter['filterchk'] = $this->request->has('filterchk') ? $this->request->get('filterchk') : '';
        if (\Request::ajax())
        {
            $wdata = array_merge($data, $filter);
            $wdata['count'] = true;
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->ambassador_commission($data['account_id'], array_merge($wdata, $filter));
            //print_r($ajaxdata['recordsTotal'] ); exit;
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal'];
                $wdata['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
                $wdata['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
                if (isset($postdata['order']))
                {
                    $wdata['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $wdata['order'] = $postdata['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->ambassador_commission($data['account_id'], $wdata);
                if (!empty($ajaxdata['data']))
                {
                    array_walk($ajaxdata['data'], function(&$refData)
                    {
                        $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                        switch ($refData->status)
                        {
                            case 0:
                                $refData->status_label = "<label class='label label-danger'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                            case 1:
                                $refData->status_label = "<label class='label label-success'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                            case 4:
                                $refData->status_label = "<label class='label label-info'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                            case 5:
                                $refData->status_label = "<label class='label label-info'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                        }
                    });
                }
            }
            return \Response::json($ajaxdata);
        }
        else if (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {

            $pdata['print_data'] = $this->affiliatereportObj->ambassador_commission($data['account_id'], array_merge($wdata, $filter));
            if (!empty($pdata['print_data']))
            {
                array_walk($pdata['print_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            return view('affiliate.bonus.ambassador_bonus_print', $pdata);
        }
        else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->ambassador_commission($data['account_id'], array_merge($wdata, $filter));
            if (!empty($epdata['export_data']))
            {
                array_walk($epdata['export_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            $output = view('affiliate.bonus.ambassador_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=FastStart_Bonus_list_'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return \Response::make($output, 200, $headers);
        }
        else
        {
            //$data['package_list']=$this->packageObj->package_list();
            return view('affiliate.bonus.ambassador_bonus');
        }
    }

    public function faststart_bonus ()
    {
        $data = $wdata = $filter = array();
        $data['from'] = '';
        $data['to'] = '';
        $data['step'] = $data['level'] = $data['type_of_package'] = '';
        $data['account_id'] = $this->userSess->account_id;
        $postdata = $this->request->all();

        $filter['from_date'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
        $filter['to_date'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
        $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
        $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
        $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
        $filter['filterchk'] = $this->request->has('filterchk') ? $this->request->get('filterchk') : '';
        if(\Request::ajax())
        {
            $wdata = array_merge($data, $filter);
            $wdata['count'] = true;
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->faststart_bonus_details($data['account_id'], array_merge($wdata, $filter));
            //print_r($ajaxdata['recordsTotal'] ); exit;
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal'];
                $wdata['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
                $wdata['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
                if (isset($postdata['order']))
                {
                    $wdata['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $wdata['order'] = $postdata['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->faststart_bonus_details($data['account_id'], $wdata);
                if (!empty($ajaxdata['data']))
                {
                    array_walk($ajaxdata['data'], function(&$refData)
                    {
                        $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                        switch ($refData->status)
                        {
                            case 0:
                                $refData->status_label = "<label class='label label-danger'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                            case 1:
                                $refData->status_label = "<label class='label label-success'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                            case 4:
                                $refData->status_label = "<label class='label label-info'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                            case 5:
                                $refData->status_label = "<label class='label label-info'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
                                break;
                        }
                    });
                }
            }
            return \Response::json($ajaxdata);
        }
        else if (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {

            $pdata['print_data'] = $this->affiliatereportObj->faststart_bonus_details($data['account_id'], array_merge($wdata, $filter));
            if (!empty($pdata['print_data']))
            {
                array_walk($pdata['print_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            return view('affiliate.bonus.faststart_print', $pdata);
        }
        else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->faststart_bonus_details($data['account_id'], array_merge($wdata, $filter));
            if (!empty($epdata['export_data']))
            {
                array_walk($epdata['export_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            $output = view('affiliate.bonus.faststart_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=FastStart_Bonus_list_'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return \Response::make($output, 200, $headers);
        }
        else
        {
            //$data['package_list']=$this->packageObj->package_list();
            return view('affiliate.bonus.faststart_bonus');
        }
    }

    public function team_bonus ()
    {
	    $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;
        $post = $this->request->all();
        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if (\Request::ajax())
        {
            $data = array_merge($data, $filter);
            //print_r($data); die;
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], $data, true); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], $data, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                $ajaxdata['data'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], $data);
            }
            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['export_data'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], array_merge($wdata, $filter));
            if ($pdata['export_data'])
            {
                array_walk($pdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            return view('affiliate.bonus.team_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('affiliate.bonus.team_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=team_bonus'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.bonus.team_bonus', $data);
        }
    }

    public function personal_commission ()
    {

        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $post = $this->request->all();
        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if(\Request::ajax())
        {
            $wdata['count'] = true;
            $data 			= array_merge($data, $filter);
			$data['count']  = true;
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->personal_commission($this->userSess->account_id, $data); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();

            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->personal_commission($this->userSess->account_id, $data);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->personal_commission($data['account_id'], array_merge($wdata, $filter));
            }


            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['export_data'] = $this->affiliatereportObj->personal_commission($data['account_id'], array_merge($wdata, $filter));
  
            return view('affiliate.bonus.personal_commission_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->personal_commission($data['account_id'], array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('affiliate.bonus.personal_commission_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Personal bonus Commission'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.bonus.personal_commission_bonus', $data);
        }
    }

    public function leadership_bonus ()
    {
        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;
        $post = $this->request->all();

        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if (\Request::ajax())
        {
            $wdata['count'] = true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], $data, true); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], $data, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], array_merge($wdata, $filter));
            }


            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
         {
            $pdata['export_data'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], array_merge($wdata, $filter));
  
            return view('affiliate.bonus.leadership_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('affiliate.bonus.leadership_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=leadership bonus Commission'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.bonus.leadership_bonus', $data);
        }
    }

    public function car_bonus ()
    { 
        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $data['bonus_type'] = $this->config->get('constants.BONUS_TYPE.CAR_BONUS');
        $post = $this->request->all();

        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';            
        }
        if (\Request::ajax())
        {
            $wdata['count'] = true;
            $wdata = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->car_bonus($wdata, true);            
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->car_bonus($wdata, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->car_bonus($wdata);
				
            }
            return \Response::json($ajaxdata);
        }
		elseif (isset($post['printbtn']) && $post['printbtn'] == 'Print')
        {  
            $pdata['print_data'] = $this->affiliatereportObj->car_bonus($data);		
            return view('affiliate.bonus.car_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->car_bonus($data);
            $output = view('affiliate.bonus.car_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=car_bonus'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }		
        else
        {
            return view('affiliate.bonus.car_bonus', $data);
        }
    }

    public function star_bonus ()
    {
        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $data['bonus_type'] = $this->config->get('constants.BONUS_TYPE.STAR_BONUS');
        $post = $this->request->all();		
        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';          
        }
        if (\Request::ajax())
        {
            $wdata['count'] = true;
            $wdata = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->car_bonus($wdata, true); //total records//

            $ajaxdata['draw'] 			 = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] 			 = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->car_bonus($wdata, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->car_bonus($wdata);
            }


            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->affiliatereportObj->car_bonus($data);         
            return view('affiliate.bonus.star_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->car_bonus($data);
            $output = view('affiliate.bonus.star_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=star_bonus'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.bonus.start_bonus', $data);
        }
    }

    public function ranks ()
    {
        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;
        $post = $this->request->all();

        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if (\Request::ajax())
        {
            $wdata['count'] = true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], $data, true); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], $data, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'], array_merge($wdata, $filter));
            }


            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], array_merge($wdata, $filter));
            if ($pdata['print_data'])
            {
                array_walk($pdata['print_data'], function(&$data)
                {
                    /* $data->confirmed_date = showUTZ($data->confirm_date, 'd-M-Y H:i:s');
                      $data->created_on = showUTZ($data->created_on, 'd-M-Y H:i:s'); */
                });
            }
            return view('affiliate.bonus.leadership_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->get_teambonus_list($data['account_id'], array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('affiliate.bonus.leadership_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=leadership_bonus'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.bonus.ranks', $data);
        }
    }

  
	public function monthly_bonus_details(){
		$postdata = $this->request->all();
		$postdata['account_id'] =  $this->userSess->currency_id;
		$postdata['uname'] =  $this->userSess->uname;
		$data = $opArray = array();
        if ($this->request->ajax())
        {
            $my_treeinfo 	   = $this->affiliatereportObj->getUser_bonus_Info(['date'=>$postdata['date'],'account_id'=>$this->userSess->account_id]);	
		    $data['upline']    = $my_treeinfo;			
	        $data['gusers']    = $this->affiliatereportObj->get_ab_bonus_sales(['account_id'=>$my_treeinfo->account_id,'date'=>$postdata['date'],'parent_acinfo'=>$my_treeinfo]);		
			$data['date']	   = $postdata['date'];
            $opArray['status'] = $this->statusCode = 200;
			$op['content']     = view('affiliate.bonus.ambassador_bonus_details',$data)->render();
			$op['status'] 	   = 'ok';
			return $this->response->json($op,200,$this->headers);
        }
	}
	
	public function personal_monthly_bonus_details(){
		$postdata = $this->request->all();
        if ($this->request->ajax())
        {
	        $data['gusers']    = $this->affiliatereportObj->get_personal_bonus_sales(['date'=>$postdata['date'],'account_id'=>$this->userSess->account_id]);
			$currency_info = $this->commonObj->get_currency($this->userSess->currency_id);
			if(!empty($data['gusers'])){
				array_walk($data['gusers'],function($k)use($currency_info){
					$k->bill_amount = $currency_info->currency_symbol.' '.number_format($k->bill_amount,'2');
				});
			}
			$data['date']	   = $postdata['date'];
            $opArray['status'] = $this->statusCode = 200;
			$op['content']     = view('affiliate.bonus.personal_bonus_montly_details',$data)->render();
			$op['status'] 	   = 'ok';
			return $this->response->json($op,200,$this->headers);
        }
	
	}
	
	public function team_bonus_details(){
		$postdata = $this->request->all();
        if($this->request->ajax())
        {
			$postdata['account_id'] = $this->userSess->account_id;
	        $op['details'] 	= $this->affiliatereportObj->teambonus_details($this->userSess->account_id,$postdata,false);
			$op['status'] 	= 'ok';
			return $this->response->json($op,200,$this->headers);
        }
	}
	
	public function leadership_bonus_details(){
		$postdata = $this->request->all();
        if($this->request->ajax())
        {
			$postdata['account_id'] = $this->userSess->account_id;
	        $op['details'] 	= $this->affiliatereportObj->leadership_bonus_details($this->userSess->account_id,$postdata,false);
     		$op['status'] 	= 'ok';
			return $this->response->json($op,200,$this->headers);
        }
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
            $ajaxdata['recordsTotal'] = $this->walletObj->tds_deducted_details($data);		
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
				$ajaxdata['data'] = $this->walletObj->tds_deducted_details($data);			
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
		elseif (isset($post['printbtn']) && $post['printbtn'] == 'Print')
        {  
            $pdata['print_data'] = $this->walletObj->tds_deducted_details(array_merge($data, $filter));
            return view('affiliate.wallet.tds_deducted_details_print', $pdata);
        }
        elseif (isset($post['exportbtn']) && $post['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->walletObj->tds_deducted_details(array_merge($data, $filter));
            $output = view('affiliate.wallet.tds_deducted_details_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Tds_deduct_report '.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }	
		return view('affiliate.wallet.tds_deducted_details',$data);
	}
	
	public function tds_view_details($trans_id){
		$postdata 			    = $this->request->all();
		$postdata['account_id'] = $this->account_id;
		$postdata['trans_id']   = $trans_id;
		$data['tds_details']     = $this->walletObj->getTdsDetails($postdata);
	    $op['content'] 		    = view('affiliate.wallet.tds_view_details',$data)->render();
		$op['status'] 			='ok';
		$this->statusCode       = $this->config->get('httperr.SUCCESS');
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options); 
	}
	public function survival_bonus ()
    {
        $data = $wdata = $filter = array();
        $data['account_id'] = $this->userSess->account_id;
        $data['bonus_type'] = $this->config->get('constants.BONUS_TYPE.STAR_BONUS');
        $post = $this->request->all();		
        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';          
        }
        if (\Request::ajax())
        {
            $wdata['count'] = true;
            $wdata = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->affiliatereportObj->survival_bonus($wdata, true); //total records//

            $ajaxdata['draw'] 			 = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] 			 = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->survival_bonus($wdata, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->affiliatereportObj->survival_bonus($wdata);
            }


            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->affiliatereportObj->survival_bonus($data);         
            return view('affiliate.bonus.survival_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->affiliatereportObj->survival_bonus($data);
            $output = view('affiliate.bonus.survival_bonus_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=survival_bonus'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
            return view('affiliate.bonus.survival_bonus', $data);
        }
    }
}
