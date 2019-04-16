<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\AdminBonus;
use App\Models\Admin\AdminPackages;
use CommonLib;

class PackageController extends BaseController
{     
    public $data = array();
    public function __construct ()
    {
        parent::__construct();
         $this->pkObj = new AdminPackages;
    }
	 public function upgrade_history ()
     {
        $data = $filter = array();
        $post = $this->request->all();
        if (\Request::ajax())
        {
            if (!empty($post))
            {
                $filter['from'] = !empty($post['from']) ? $post['from'] : '';
                $filter['to'] = !empty($post['to']) ? $post['to'] : '';
                $filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
                $submit = isset($post['submit']) ? $post['submit'] : '';
            }
            $data['count'] = true;
            $data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->pkObj->upgrade_history($data);
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
                $pkglist = $this->pkObj->upgrade_history($data);
			
                array_walk($pkglist, function(&$package)
                {
                    if (!empty($package->package_image))
                    {
                        $package->package_image_url = url($package->package_image, [], true);
                    }
                    $diff = date_diff(date_create($package->create_date), date_create(date('Y-m-d')));
                    $package->fullname = ucwords($package->fullname);
					$package->refundable_days = ($diff->format("%a") > 0) ? $diff->format("%R%a days") : '';
                    $package->weekly_capping_qv = CommonLib::currency_format($package->weekly_capping_qv, $package->currency_id);
                    $package->paid_amt = CommonLib::currency_format($package->paid_amt, $package->currency_id);
                    $package->refund_expire_on = showUTZ('M d, Y', $package->package_image);
                    $package->create_date = showUTZ($package->create_date,'d M, Y');
                    $package->updated_date = showUTZ($package->updated_date,'d M, Y');
                    switch ($package->status)
                    {
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED'):
                            $package->status = '<span class="label label-success">Acivated</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CANCELLED'):
                            $package->status = '<span class="label label-info">Cancelled</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.EXPIRED'):
                            $package->status = '<span class="label label-danger">Expired</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.PENDING'):
                            $package->status = '<span class="label label-warning">Pending</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.WAIT_FOR_ACTIVATE'):
                            $package->status = '<span class="label label-info">Wait for Approvals</span>';
                            break;
                        case $this->config->get('constants.PACKAGE_PURCHASE_STATUS.USER_APPROVALS'):
                            $package->status = '<span class="label label-warning">Not Activated</span>';
                             if ($diff->format("%a") >= 0) {
		                       $package->actions[] = ['url'=>route('admin.report.activate', ['code'=>$package->purchase_code]), 'class'=>'pkactivate_btn', 'redirect'=>false, 'label'=>trans('general.btn.activate')];
							}
							else {
								$package->action = $diff->format("%a");
							} 
                        break;
                    }
                });

                $ajaxdata['data'] = $pkglist;
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
        }
        return view('admin.package.upgrade_history', $data);
    }
	
    public function purchase_activate ()
    {
        $op = [];
        $post = $this->request->all();
        if (!empty($post['code']))
        {
            $packInfo = $this->pkObj->getTopupPackageInfo(['purchase_code'=>$post['code']]);
			
             if (!empty($packInfo))
             {
                $packInformation = $this->pkObj->activatePackage($packInfo);
                if(!empty($packInformation)){
                     $op['msg']='Package activated successfully';
                     $op['ststus'] =$this->config->get('httperr.SUCCESS');
                     $this->statusCode = $this->config->get('httperr.SUCCESS');
               }
            } 
        }
          return $this->response->json($op, $this->statusCode,$this->headers,$this->options);
     }
  }
