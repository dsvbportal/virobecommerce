<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\AdminReport;
use App\Models\Admin\AffModel;
use Request;
use Response; 
use URL;
class AdminReportController extends BaseController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
		$this->reportObj = new AdminReport();
         $this->affObj = new AffModel();
    }

    public function team_commission()
    {
		$data = $wdata = $filter = array();
        $post = $this->request->all();
        if(!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if($this->request->ajax())
        {
            $data = array_merge($data, $filter);
            //print_r($data); die;
            $ajaxdata['recordsTotal'] = $this->reportObj->team_commission($data, true); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->reportObj->team_commission($data, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                $ajaxdata['data'] = $this->reportObj->team_commission($data);
            }
            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['export_data'] = $this->reportObj->team_commission(array_merge($wdata, $filter));
            if ($pdata['export_data'])
            {
                array_walk($pdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            return view('admin.report.team_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->team_commission(array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('admin.report.team_bonus_export', $epdata);
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
		else{
			return view('admin.report.team_commission',$data);
		}
    }

	public function leadership_bonus ()
    {
        $data = $wdata = $filter = array();
        $post = $this->request->all();
		if(!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if ($this->request->ajax())
        {
            $wdata['count'] 	= true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->reportObj->get_leadership_bonus($data, true); //total records//
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->reportObj->get_leadership_bonus($data, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->reportObj->get_leadership_bonus(array_merge($wdata, $filter));
            }
            return $this->response->json($ajaxdata,200,$this->headers);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['export_data'] = $this->reportObj->get_leadership_bonus(array_merge($wdata, $filter));
            if ($pdata['export_data'])
            {
                array_walk($pdata['export_data'], function(&$data)
                {
                    /* $data->confirmed_date = showUTZ($data->confirm_date, 'd-M-Y H:i:s');
                      $data->created_on = showUTZ($data->created_on, 'd-M-Y H:i:s'); */
                });
            }
            return view('affiliate.bonus.leadership_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->get_leadership_bonus(array_merge($wdata, $filter));
			//print_r($epdata['export_data']);exit;
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
            return view('admin.report.leadershipbonus',$data);
        }
    }
	
	public function ambassador_bonus ()
    {
		$data = $wdata  = $filter = array();
        $data['from'] 	= '';
        $data['to'] 	= '';
        $data['step'] 	= $data['level'] = $data['type_of_package'] = '';
        $postdata = $this->request->all();
		
        $filter['from'] 	 = $this->request->has('from_date') ? $this->request->get('from_date') : '';
        $filter['to'] 		 = $this->request->has('to_date') ? $this->request->get('to_date') : '';
        $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
        $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
        $filter['printbtn']	 = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
        $filter['filterchk'] = $this->request->has('filterchk') ? $this->request->get('filterchk') : '';
        if($this->request->ajax())
        {
            $wdata = array_merge($data, $filter);
            $wdata['count'] = true;
            $ajaxdata['recordsTotal'] = $this->reportObj->ambassador_commission(array_merge($wdata, $filter));
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
                $ajaxdata['data'] = $this->reportObj->ambassador_commission($wdata);
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

            $pdata['print_data'] = $this->reportObj->ambassador_commission(array_merge($wdata, $filter));
            if (!empty($pdata['print_data']))
            {
                array_walk($pdata['print_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            return view('admin.report.ambassador_bonus_print', $pdata);
        }
        else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->ambassador_commission(array_merge($wdata, $filter));
            if (!empty($epdata['export_data']))
            {
                array_walk($epdata['export_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            $output = view('admin.report.ambassador_bonus_export', $epdata);
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
            return view('admin.report.ambassadar_bonus');
        }
    }
	
	public function faststart_bonus()
    {
        $data = $wdata  = $filter = array();
        $data['from'] 	= '';
        $data['to'] 	= '';
        $data['step'] 	= $data['level'] = $data['type_of_package'] = '';
        $postdata = $this->request->all();

        $filter['from_date'] 	= $this->request->has('from_date') ? $this->request->get('from_date') : '';
        $filter['to_date'] 		= $this->request->has('to_date') ? $this->request->get('to_date') : '';
        $filter['search_term']  = $this->request->has('search_term') ? $this->request->get('search_term') : '';
        $filter['exportbtn']	= $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
        $filter['printbtn']	    = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
        $filter['filterchk'] 	= $this->request->has('filterchk') ? $this->request->get('filterchk') : '';
        if($this->request->ajax())
        {
            $wdata 				 = array_merge($data, $filter);
            $wdata['count']		 = true;
            $ajaxdata['recordsTotal'] 	 = $this->reportObj->faststart_bonus(array_merge($wdata, $filter),true);
	        $ajaxdata['draw'] 	 		 = !empty($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data']	 = array();
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
                $ajaxdata['data'] = $this->reportObj->faststart_bonus($wdata,false);
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
            $pdata['print_data'] = $this->reportObj->faststart_bonus(array_merge($wdata, $filter));
            if (!empty($pdata['print_data']))
            {
                array_walk($pdata['print_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            return view('admin.report.faststart_print', $pdata);
        }
        else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->faststart_bonus(array_merge($wdata, $filter));
            if (!empty($epdata['export_data']))
            {
                array_walk($epdata['export_data'], function(&$refData)
                {
                    $refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
                    $refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
                });
            }
            $output = view('admin.report.faststart_export', $epdata);
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
            return view('admin.report.fast_track_bonus');
        }
    }
	
	public function car_bonus ()
    { 
        $data = $wdata = $filter = array();      
		$post = $this->request->all();
        $data['bonus_type'] = $this->config->get('constants.BONUS_TYPE.CAR_BONUS');
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
            $ajaxdata['recordsTotal'] = $this->reportObj->car_bonus($wdata, true);            
            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->reportObj->car_bonus($wdata, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->reportObj->car_bonus($wdata);
				
            }
            return \Response::json($ajaxdata);
        }
		elseif (isset($post['printbtn']) && $post['printbtn'] == 'Print')
        {  
            $pdata['print_data'] = $this->reportObj->car_bonus($data);		
            return view('admin.report.car_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->car_bonus($data);
            $output = view('admin.report.car_bonus_export', $epdata);
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
            return view('admin.report.car_bonus', $data);
        }
    }
	
	public function changeBonusStatus ()
    {         	
        $op['msg'] = trans('general.something_went_wrong');
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();		
        if (!empty($postdata))
        {           
            $data = $this->reportObj->changeBonusStatus($postdata);
            if (!empty($data))
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('general.status_updated_successfully');
            }
        }
        return $this->response->json($op,$this->statusCode,$this->headers,$this->options); 	
    }
	
	 public function star_bonus ()
    {
        $data = $wdata = $filter = array();   
		$post = $this->request->all();		
        $data['bonus_type'] = $this->config->get('constants.BONUS_TYPE.STAR_BONUS');        
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
            $ajaxdata['recordsTotal'] = $this->reportObj->car_bonus($wdata, true); //total records//

            $ajaxdata['draw'] 			 = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] 			 = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->reportObj->car_bonus($wdata, true);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->reportObj->car_bonus($wdata);
            }
            return \Response::json($ajaxdata);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['print_data'] = $this->reportObj->car_bonus($data);         
            return view('admin.report.star_bonus_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->car_bonus($data);
            $output = view('admin.report.star_bonus_export', $epdata);
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
            return view('admin.report.star_bonus', $data);
        }
    }
	
	
	public function personal_commission ()
    {
        $data = $wdata = $filter = array();
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
        if($this->request->ajax())
        {
            $wdata['count'] = true;
            $data 			= array_merge($data, $filter);
			$data['count']  = true;
            $ajaxdata['recordsTotal'] = $this->reportObj->personal_commission($data); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();

            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->reportObj->personal_commission($data);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->reportObj->personal_commission(array_merge($wdata, $filter));
            }


            return $this->response->json($ajaxdata,200,$this->headers);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['export_data'] = $this->reportObj->personal_commission(array_merge($wdata, $filter));
  
            return view('admin.report.personal_commission_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['export_data'] = $this->reportObj->personal_commission(array_merge($wdata, $filter));
            if ($epdata['export_data'])
            {
                array_walk($epdata['export_data'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('admin.report.personal_commission_export', $epdata);
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
            return view('admin.report.personal_customer_bonus', $data);
        }
    }
	
	public function get_ranks ()
    {
        $data = $wdata = $filter = array();
        $post = $this->request->all();
        if (!empty($post))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : '';
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : '';
            $filter['terms'] = $this->request->has('terms') ? $this->request->get('terms') : '';
            $filter['exportbtn'] = $this->request->has('exportbtn') ? $this->request->get('exportbtn') : '';
            $filter['printbtn'] = $this->request->has('printbtn') ? $this->request->get('printbtn') : '';
            //$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
        }
        if($this->request->ajax())
        {
            $wdata['count'] = true;
            $data 			= array_merge($data, $filter);
			$data['count']  = true;
            $ajaxdata['recordsTotal'] = $this->reportObj->get_ranks($data); //total records//

            $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();

            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->reportObj->get_ranks($data);
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;
                $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
                if (isset($post['order']))
                {
                    $wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $wdata['order'] = $post['order'][0]['dir'];
                }
                unset($wdata['count']);
                $ajaxdata['data'] = $this->reportObj->get_ranks(array_merge($wdata, $filter));
            }


            return $this->response->json($ajaxdata,200,$this->headers);
        }
        elseif (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')
        {
            $pdata['ranks'] = $this->reportObj->get_ranks(array_merge($wdata, $filter));
  
            return view('admin.report.ranks_print', $pdata);
        }
        elseif (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')
        {
            $epdata['ranks'] = $this->reportObj->get_ranks(array_merge($wdata, $filter));
            if ($epdata['ranks'])
            {
                array_walk($epdata['ranks'], function(&$data)
                {
                    /* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
                      $data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
                });
            }
            $output = view('admin.report.ranks_export', $epdata);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=Ranks-'.date("d-M-Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else
        {
			 $data['contries'] = $this->reportObj->get_ranks(['countries'=>1]);
	         return view('admin.report.ranks', $data);
        }
    }
	
	public function rank_log(){
		$postdata = $this->request->all();
		$log = $this->reportObj->get_rank_log($postdata);
		$op['status'] = 'ok';
		$op['log'] = $log;
		return $this->response->json($op,200);
	}
      
	public function Qualified_volume_details(){

	   $data = $filter = array();
		$postdata = $this->request->all();
		 if (!empty($postdata))  {			
		  $filters['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
		  $filters['start_date'] = (isset($postdata['from_date']) && !empty($postdata['from_date'])) ? $postdata['from_date'] : '';
		  $filters['end_date'] = (isset($postdata['to_date']) && !empty($postdata['to_date'])) ? $postdata['to_date'] : '';
		}
		if (Request::ajax()) {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
			
            $dat = array_merge($data, $filters);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->qualified_volume_details($dat, true); 
			if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filters);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->affObj->qualified_volume_details($data, true);
                }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->affObj->qualified_volume_details($data);
                 
                }
            }
            return Response::json($ajaxdata);
        }
	   elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$data['free_is_affiliate'] = $user_role;
			$data['can_sponser']=$can_sponser;
			$edata['manage_user_details'] = $this->affObj->qualified_volume_details(array_merge($data,$filter));	
            $output = view('admin.affiliate.free_affiliate_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=free Affiliate' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$data['free_is_affiliate'] = $user_role;
			$data['can_sponser']=$can_sponser;
			$pdata['manage_user_details'] = $this->affObj->qualified_volume_details(array_merge($data,$filter));
            return view('admin.affiliate.free_affiliate_print',$pdata);
        } 
		else{
            return view('admin.report.qualified_volume_details');  
		} 
	}
	
	public function leadership_bonus_details(){
		$postdata = $this->request->all();
        if($this->request->ajax())
        {
			$postdata['account_id'] = $this->userSess->account_id;
	        $op['details'] 	= $this->adminreportObj->leadership_bonus_details($postdata,false);
     		$op['status'] 	= 'ok';
			return $this->response->json($op,200,$this->headers);
        }
	}

}
