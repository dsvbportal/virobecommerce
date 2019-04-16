<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Admin\Admin;
use App\Models\Admin\Suppliers;
use App\Helpers\ShoppingPortal;
use App\Models\MemberAuth;
use App\Models\Admin\SupplierProductOrder;
use View;
use Input;
use Config;
use Response;
use Request;
use URL;
use Lang;
use Illuminate\Support\Facades\Validator;

class SuppliersController extends AdminController
{
    public function __construct ()
    {
        parent::__construct();
        $this->suppObj = new Suppliers($this->commonObj);
    }

    public function add_suppliers ()
    {
        return View::make('admin.suppliers.add_suppliers');
    }

	/* New  */
    public function verification ($uname = '')
    {
        $op = array();
        $data = array();
        $postdata = $this->request->all();
        if (isset($uname) && !empty($uname))
        {
            $data['uname'] = $uname;
        }
        else
        {
            $data['uname'] = "";
        }
        if (!empty($postdata))
        {
            $data['type_filer'] = isset($postdata['type_filer']) ? $postdata['type_filer'] : '';
            $data['from'] = isset($postdata['from']) ? $postdata['from'] : '';
            $data['to'] = isset($postdata['to']) ? $postdata['to'] : '';
            $data['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : '';
            $data['status'] = isset($postdata['status']) ? $postdata['status'] : '';
            $data['uname'] = isset($postdata['uname']) ? $postdata['uname'] : '';
            $data['account_id'] = isset($postdata['account_id']) ? $postdata['account_id'] : '';
        }
        if (Request::ajax())
        {
            $data['counts'] = true;
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->verification_list($data);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
                $data['type_filer'] = ($postdata['type_filer']) ? $postdata['type_filer'] : '';
                unset($data['counts']);
                $ajaxdata['data'] = $this->suppObj->verification_list($data);
                $ajaxdata['draw'] = $postdata['draw'];
                $ajaxdata['url'] = URL::to('/');
            }
            else
            {
                $ajaxdata['data'] = array();
            }
            return Response::json($ajaxdata);
        }
        else
        {
            if (empty($uname))
            {
                $data['status'] = 0;
            }
            return View::make('admin.seller.verification', $data);
        }
    }

    public function doc_list ()
    {
        $data = $this->suppObj->doc_list();
        if (!empty($data))
        {
            return $data;
        }
        else
        {
            return false;
        }
    }

    public function change_status ()
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $postdata['admin_id'] = $this->account_id;
            $data = $this->suppObj->change_status($postdata);
            if (!empty($data))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.actions.status.'.$postdata['status'], ['label'=>Lang::get('general.fields.document')]);
            }
        }
        return Response::json($op, 200);
    }

    public function change_verify_status ()
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $postdata['admin_id'] = $this->account_id;
            $data = $this->suppObj->change_verify_status($postdata);
            if (!empty($data))
            {
                $op['status'] = 'OK';
                $op['msg'] = !empty($postdata['status']) ? 'Seller Verified Successfully' : 'Seller Un-Verified Successfully';
            }
        }
        return Response::json($op, 200);
    }

    public function delete_doc ()
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $data = $this->suppObj->delete_doc($postdata);
            if (!empty($data))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.actions.deleted', ['label'=>Lang::get('general.fields.document')]);
            }
        }
        return Response::json($op);
    }

    public function get_store_details ($store_code)
    {
        $op = array();
        if ($store_code && !empty($store_code))
        {
            $post['store_code'] = $store_code;
            if ($res = $this->suppObj->get_stores_list($post))
            {
                $data['store_details'] = $res;
                $op['data'] = $res;
            }
        }
        return Response::json($op);
    }

    public function save_stores ($store_code = 0, $arr = array())
    {
        $data = $filters = array();
        $op = array();
        $status = 422;
        $op['status'] = 'ERR';
        $submit = '';
        if (!empty($arr))
        {
            $post = $arr;
        }
        else
        {
            $post = Input::all();
        }

        $post['admin_id'] = $this->account_id;
        if ($store_code && !empty($store_code))
        {
            $post['store_code'] = $store_code;
        }
        $data['supplier_id'] = $post['create']['supplier_id'];
        $result = $this->suppObj->update_stores($post);
        if ($result)
        {
            $status = 200;
            if ($result == 1)
            {
                $op['msg'] = Lang::get('general.actions.created', ['label'=>Lang::get('general.store')]);
                $op['status'] = 'OK';
            }
            if ($result == 2)
            {
                $op['msg'] = Lang::get('general.actions.updated', ['label'=>Lang::get('general.store')]);
                $op['status'] = 'OK';
            }
            $op['url'] = URL::to('admin/suppliers');
        }


        if (!empty($arr))
        {
            return $op;
        }
        else
        {
            return Response::json($op, $status);
        }
    }

    public function stores_management ($store_code = '')
    {
        $data = $filters = array();
        $submit = '';
        $postdata = Input::all();
        if (!empty($store_code))
        {
            $data['store_code'] = $store_code;
        }
        if (!empty($postdata))
        {
            $filters['search_text'] = (isset($postdata['search_text']) && !empty($postdata['search_text'])) ? $postdata['search_text'] : '';
            $filters['filterTerms'] = (isset($postdata['filterTerms']) && !empty($postdata['filterTerms'])) ? $postdata['filterTerms'] : '';
            $filters['start_date'] = (isset($postdata['from']) && !empty($postdata['from'])) ? $postdata['from'] : '';
            $filters['end_date'] = (isset($postdata['to']) && !empty($postdata['to'])) ? $postdata['to'] : '';
        }
        if (Request::ajax())
        {
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
            $data['counts'] = true;
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->get_stores_list($data);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $filter = array_filter($filters);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->suppObj->get_stores_list($data);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    $data = array_merge($data, $filters);
                    unset($data['counts']);
                    $ajaxdata['data'] = $this->suppObj->get_stores_list($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $data = array_merge($data, $filters);
            $res['store_list'] = $this->suppObj->get_stores_list($data);
            $output = View::make('admin.stores.view_store_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Stores_List_'.date('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $data = array_merge($data, $filters);
            $res['store_list'] = $this->suppObj->get_stores_list($data);
            return View::make('admin.stores.view_store_list_print', $res);
        }
        else
        {
            return View::make('admin.stores.stores_list', $data);
        }
    }

    public function save_suppliers ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();
        $status = 422;
        if (isset($postdata['company_name']))
        {
            $data['supplier_id'] = $this->suppObj->save_suppliers($postdata, $this->account_id);
            if ($data['supplier_id'])
            {
                $data['current_step'] = Config::get('constants.ACCOUNT_CREATION_STEPS.ACCOUNT_DETAILS');
                $this->suppObj->updateNextStep($data);
                $op['supplier_id'] = $data['supplier_id'];
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.supplier.created');
            }
            return Response::json($op);
        }
        if (Session::has('previous_form'))
        {
            $admin_id = $this->account_id;
            $data['update'] = $this->suppObj->save_suppliers(Session::get('previous_form'), $admin_id);
            if ($data['update'])
            {
                Session::forget('previous_form');
                $postdata['create']['supplier_id'] = $data['update'];
                $res = $this->save_stores($storecode = 0, $postdata);
                if ($res['status'] == 'ERR')
                {
                    $status = 422;
                    $op['error'] = $res['error'];
                }
                else
                {
                    $status = 200;
                    $op['status'] = 'OK';
                    $op['supplier_id'] = $data['update'];
                    $op['msg'] = Lang::get('general.supplier.created');
                    $op['url'] = URL::to('admin/suppliers');
                }
            }
            else
            {
                $op['status'] = 'ERR';
                $op['msg'] = Lang::get('suppliers_controller.fail_msg_create');
            }
        }
        return Response::json($op, $status);
    }

    public function suppliers_reset_pwd ($account_id)
    {
        $postdata = Input::all();
        $op = array();
        if (!empty($account_id) && isset($postdata['save']))
        {
            $wdata['account_id'] = $account_id;
            $data['login_password'] = $postdata['login_password'];
            $res = $this->suppObj->change_pwd($data, $wdata);			
            if ($res)
            {
                $op['msg'] = Lang::get('general.seller.password_changed_successfully');
            }
        }
        else
        {
            // $data['supplier_det'] =  $this->suppObj->get_suppliers_list($data);
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op);
    }

    public function suppliers_list ($status = '')
    {
        $data = $filters = array();
        $submit = '';
        $postdata = Input::all();
        $data['is_verified'] = 1;
        $data['status'] = 1;
        $data['is_closed'] = 0;
        if ($status == 'inactive')
        {
            $data['is_verified'] = 1;
            $data['status'] = 2;
        }
        if ($status == 'approvals')
        {
            $data['status'] = 0;
            $data['is_verified'] = 0;
            $data['completed_steps'] = 0;
        }
        if ($status == 'closed')
        {
            $data['is_closed'] = 1;
        }
        $data['status_name'] = $status;
        if (!empty($postdata))
        {
            $filters['search_text'] = (isset($postdata['search_text']) && !empty($postdata['search_text'])) ? $postdata['search_text'] : '';
            $filters['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
            $filters['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
            $filters['company_name'] = (isset($postdata['company_name']) && !empty($postdata['company_name'])) ? $postdata['company_name'] : '';
            $filters['user_name'] = (isset($postdata['user_name']) && !empty($postdata['user_name'])) ? $postdata['user_name'] : '';
            $filters['country'] = (isset($postdata['country']) && !empty($postdata['country'])) ? $postdata['country'] : '';
            $filters['filterTerms'] = (isset($postdata['filterTerms']) && !empty($postdata['filterTerms'])) ? $postdata['filterTerms'] : '';
        }
        if (Request::ajax())
        {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
            $ajaxdata['status_name'] = $data['status_name'];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->get_suppliers_list($data, true);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $filter = array_filter($filters);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->suppObj->get_suppliers_list($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->suppObj->get_suppliers_list($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $data = array_merge($data, $filters);
            $res['supplier_list'] = $this->suppObj->get_suppliers_list($data);
            $output = View::make('admin.suppliers.view_suppliers_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Suppliers_List_'.date('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $data = array_merge($data, $filters);
            $res['supplier_list'] = $this->suppObj->get_suppliers_list($data);
            return View::make('admin.suppliers.view_suppliers_list_print', $res);
        }
        else
        {
            $data['tabl_title'] = 'Active Supplier List';
            $data['is_verified'] = 1;
            $data['status'] = 1;
            $data['is_closed'] = 0;
            $data['active_cnts'] = $this->suppObj->get_suppliers_list($data, true);			
            $data['is_verified'] = 1;
            $data['status'] = 2;
            $data['is_closed'] = 0;
            $data['inactive_cnts'] = $this->suppObj->get_suppliers_list($data, true);
            $data['is_verified'] = 0;
            $data['status'] = 0;
            $data['is_closed'] = 0;
            $data['approvals_cnts'] = $this->suppObj->get_suppliers_list($data, true);
            $data['is_closed'] = 1;
            $data['closed_cnts'] = $this->suppObj->get_suppliers_list($data, true);
            if ($status == 'inactive')
            {
                $data['tabl_title'] = Lang::get('general.inactive_supplier_list');
            }
            if ($status == 'approvals')
            {
                $data['tabl_title'] = Lang::get('general.supplier.approved_list');
            }
            if ($status == 'closed')
            {
                $data['tabl_title'] = Lang::get('general.supplier.closed_list');
            }			
            return View::make('admin.seller.seller_list', $data);
        }
    }

    public function suppliers_status_update ($account_id)
    {
        $op = array();
        $status = Input::all();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.we_cound_not_able_to_process_your_request');
        if (!empty($account_id))
        {
            $res = $this->suppObj->status_update($account_id, $status);
            if (!empty($res))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.supplier.status.'.$status['status']);
            }
        }
        return Response::json($op, 200);
    }

    public function get_suppliers_details ($account_uname)
    {
        $postdata = Input::all();
        $data['status'] = 'ERR';
        $wdata['uname'] = $account_uname;
        $data['suppliers_details'] = $this->suppObj->suppliers_details($wdata);		
		//echo '<pre>';print_r($data);exit;
        if (!empty($data['suppliers_details']))
        {
            $data['status'] = 'OK';
            $data['admin_name'] = $this->full_name;			
			return View::make('admin.seller.seller_details', $data);            
        }
        return App::abort(404);
    }

    public function verifyStep ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        $postdata['admin_id'] = $this->account_id;
        $result = $this->suppObj->verifyStep($postdata);		
        if ($result)
        {
            $op['status'] = 'OK';
            $op['msg'] = ($result == 1 ? Lang::get('general.updated_successfully') : Lang::get('general.account.verified_and_email_sent'));
        }
        return Response::json($op);
    }

    public function email_validate ()
    {
        $postdata = Input::all();
        $data['suppliers_details'] = $this->suppObj->email_validate($postdata);
        if ($data['suppliers_details'])
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.avaliable');
        }
        else
        {
            $op['status'] = 'ERR';
            $op['msg'] = Lang::get('general.not_avaliable');
        }
        return Response::json($op);
    }

    public function edit_suppliers ($uname)
    {
        $op = array();
        if (Request::ajax())
        {
            $postdata = Input::all();
            if ($this->suppObj->supplier_edit($postdata))
            {
                $op['msg'] = Lang::get('general.supplier.updated');
                $op['status'] = 'OK';
                return Response::json($op);
            }
            return Response::json($op);
        }
        else
        {
            $data = array();
            $data['uname'] = $uname;
            $data['supplier_det'] = $this->suppObj->suppliers_details($data);			
            return View::make('admin.seller.seller_edit', $data);
        }
    }

    public function update_suppliers ()
    {
        $op = array();
        $postdata = Input::all();
        if (!empty($postdata['supplier_id']))
        {
            $postdata['account_id'] = $this->account_id;
            $response = $this->suppObj->supplier_edit($postdata);			
            if (!empty($response))
            {
                $op['msg'] = Lang::get('general.supplier.updated');
                $op['status'] = 'OK';
                return Response::json($op);
            }
        }
    }

    public function order_details ($supplier_id)
    {

    }

    public function order_cancel ()
    {
        $postdata = Input::all();
        $postdata['order_id'] = '1';
        $response = $this->suppObj->order_cancel($postdata);
        $data['order_details'] = $this->suppObj->order_details($postdata);
        //print_r($response);
        $data['order_id'] = $postdata['order_id'];
        if ($response > 0)
        {
            $mstatus = Mailer::send(array(
                        'to'=>$emails,
                        'subject'=>'Order Cancelation - Your Order with '.App::site_settings()->site_domain.' [#'.$postdata['order_id'].'] has been successfully Cancelled!',
                        'view'=>'emails.product_order_cancelation',
                        'data'=>$data
            ));
        }
        else
        {
            echo 'Since, your ordered item has been dispatched. You cant cancel your order.';
        }
    }

    public function Product_stock_log_Report ()
    {
        $data = array();
        //$submit = '';
        //$from = '';
        $postdata = Input::all();
        //   $data['supplier_id'] = $this->supplier_id;
        $data['from'] = '';
        $data['to'] = '';
        if (!empty($postdata))
        {
            $data['from'] = $postdata['from'];
            $data['to'] = $postdata['to'];
            $data['product_id'] = $postdata['product_id'];
            $data['supplier_id'] = $postdata['supplier_id'];
        }
        //print_r($data);exit;
        if (Request::ajax())
        {
            $data['counts'] = true;
            $count = $this->suppObj->get_stock_log_report($data);
            $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
            $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
            $data['order'] = $postdata['order'][0]['dir'];
            unset($data['counts']);
            $ajaxdata['data'] = $this->suppObj->get_stock_log_report($data);
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['recordsTotal'] = $count;
            $ajaxdata['recordsFiltered'] = $count;
            //print_r($ajaxdata);exit;
            return Response::json($ajaxdata);
        }
        else
        {
            return View::make('admin.suppliers.product_stock_log', $data);
        }
    }

    public function supplier_commissions ()
    {
        return View::make('admin.suppliers.supplier_commissions');
    }

    public function supplier_check ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Supplier Code Not Exists';
        $postdata = Input::all();
        $data['supplier_name'] = $postdata['supplier_name'];
        $result = $this->suppObj->supplier_check($data);
        if ($result)
        {
            $op['status'] = 'OK';
            $op['msg'] = 'Supplier Available';
            $op['supplier_id'] = $result->supplier_id;
            $op['full_name'] = $result->full_name;
            $op['company_name'] = $result->company_name;
            $op['user_code'] = $result->user_code;
            $op['mobile'] = $result->mobile;
            $op['email'] = $result->email;
            $op['commission_supplier'] = $result->commission_supplier;
            $op['commission_type'] = $result->commission_type;
            $op['currency_id'] = $result->currency_id;
            $op['commission_unit'] = $result->commission_unit;
            $op['commission_value'] = $result->commission_value;
        }
        return Response::json($op);
    }

    public function save_commissions ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();
        $postdata['admin_id'] = $this->account_id;
        $postdata['currency_id'] = 1;

        $result = $this->suppObj->save_commissions($postdata);
        if ($result)
        {
            $op['data'] = $result;
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.commission_updated_successfully');
        }
        else
        {
            $op['status'] = 'ERR';
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op);
    }

    public function supplier_payment_report ()
    {
        $data = $filter = array();
        $post = Input::all();
        $submit = '';
        if (is_array($post) && !empty($post))
        {
            $filter['from'] = isset($post['from']) && !empty($post['from']) ? $post['from'] : NULL;
            $filter['to'] = isset($post['to']) && !empty($post['to']) ? $post['to'] : NULL;
            $filter['term'] = isset($post['term']) && !empty($post['term']) ? $post['term'] : NULL;
        }
        if (Request::ajax())
        {
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $post['draw'];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->supplier_payment_report([], true);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->suppObj->supplier_payment_report($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
                    $data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    if (isset($post['order']))
                    {
                        $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                        $data['order'] = $post['order'][0]['dir'];
                    }
                    $ajaxdata['data'] = $this->suppObj->supplier_payment_report($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (isset($post['submit']) && !empty($post['submit']))
        {
            $submit = $post['submit'];
        }
        if ($submit == 'Export')
        {
            $data['transaction_log'] = $this->suppObj->supplier_payment_report($data);
            $output = View::make('admin.transaction_log.excel')
                    ->with('data', $data);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=TransactionLog_'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if ($submit == 'Print')
        {
            $data['transaction_log'] = $this->suppObj->supplier_payment_report($data);
            return View::make('admin.transaction_log.print')
                            ->with('data', $data);
        }
        else
        {
            return View::make('admin.suppliers.supplier_payment_report', $data);
        }
    }

    public function supplier_payment_details ($order_item_code)
    {
        $data = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();
        $data['order_item_code'] = $order_item_code;
        $data['payment_details'] = $this->suppObj->supplier_payment_details($data);
        if (!empty($data['payment_details']))
        {
            if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
            {
                $output = View::make('admin.products.view_categories_list_excel', $res);
                $headers = array(
                    'Pragma'=>'public',
                    'Expires'=>'public',
                    'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                    'Cache-Control'=>'private',
                    'Content-Disposition'=>'attachment; filename=Category_list'.date('Y-m-d').'.xls',
                    'Content-Transfer-Encoding'=>'binary'
                );
                return Response::make($output, 200, $headers);
            }
            else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
            {
                return View::make('admin.products.view_categories_list_print', $res);
            }
            else
            {
                if (Request::ajax())
                {
                    $op['status'] = 'OK';
                    $op['layoutContent'] = View::make('admin.suppliers.supplier_payment_details', $data)->render();
                    return Response::json($op);
                }
            }
        }
    }

    public function supplierPerferences ($uname)
    {
        $data = [];
        $data['uname'] = $uname;
        $data['preferences'] = $this->suppObj->getSupplierPreferences($data);				
        return View::make('admin.seller.preferences', $data);
    }

    public function supplierSavePerferences ()
    {
        $op = array();
        $status = 422;
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        if ($this->suppObj->savePerferences($data))
		{
			$op['msg'] = Lang::get('general.updated_successfully');
			$status = 200;
		}        
        return Response::json($op, $status);
    }
	
	public function meta_info ()
    {
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $meta_info = $this->suppObj->get_meta_info($postdata);
            if (!empty($meta_info))
            {
                return Response::json($meta_info);
            }
        }
        return Response::json(array());
    }
	
	public function save_meta_info ()
    {
        $op = array();
        $postdata = Input::all();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['admin_id'] = $this->account_id;
            if ($res = $this->suppObj->save_meta_info($postdata))
            {				
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.updated_successfully');
            }
            else
            {
                $op['status'] = 'WARN';
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op);
    }

    public function brandList ()
    {
        $data = $filter = array();
        $postdata = Input::all();
        $data['search_term'] = '';
        if (!empty($postdata))
        {
            $filter['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : NULL;
            $filter['brand_id'] = isset($postdata['brand_id']) && !empty($postdata['brand_id']) ? $postdata['brand_id'] : NULL;
            $filter['supplier_id'] = isset($postdata['supplier_id']) && !empty($postdata['supplier_id']) ? $postdata['supplier_id'] : NULL;
        }
        if (Request::ajax())
        {
            $ajaxdata = [];
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = [];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->getBrandsList($data, true);
            if (!empty($ajaxdata['recordsTotal']))
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->suppObj->getBrandsList($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    $ajaxdata['data'] = $this->suppObj->getBrandsList($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['brands'] = $this->suppObj->getBrandsList($data);
            $output = View::make('admin.products.view_brand_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Brand_list_'.date('d-M-Y').'.xls',
                'Content-Transfer-Encoding'=>'binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['brands'] = $this->suppObj->getBrandsList($data);
            return View::make('admin.products.view_brand_list_print', $res);
        }
        else
        {
            return View::make('admin.products.supplier_brand_list', $data);
        }
    }

    public function saveBrand ()
    {
        $postdata = Input::all();
        $op = array();
        //$validator = Validator::make($postdata, Config::get('validations.CREATE_BRAND.RULES'), Config::get('validations.CREATE_BRAND.MESSAGES'));
        $validator = Validator::make($postdata, ['brand_name'=>'required|min:2|max:100'], ['brand_name.required'=>'Brand Name Required', 'brand_name.min'=>'Brand Name must be atleast :min characters', 'brand_name.max'=>'Brand Name must be lesser that :max characters']);
        if (!$validator->fails())
        {
            $postdata['account_id'] = $this->account_id;
            if ($this->suppObj->saveBrand($postdata))
            {
                $this->statusCode = 200;
                $op['msg'] = 'Brand Successfully Added.';
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = 'Brand Already Exists.';
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteBrand ($bid = '')
    {
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        if (isset($bid) && !empty($bid))
        {
            $postdata['id'] = $bid;
        }
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->suppObj->deleteBrand($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.brand.deleted');
            }
        }
        return Response::json($op, $this->statusCode);
    }

    public function updatebrandStatus ($id)
    {
        $postdata = Input::all();
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($id) && !empty($postdata))
        {
            $postdata['id'] = $id;
            $postdata['account_id'] = $this->account_id;
            $result = $this->suppObj->updateBrandStatus($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.brand.status.'.$postdata['status']);
            }
        }
        return Response::json($op, $this->statusCode);
    }

    public function updateBrandVerification ()
    {
        $postdata = Input::all();
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->suppObj->updateBrandVerification($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.brand.verification.'.$postdata['is_verified']);
            }
        }
        return Response::json($op, $this->statusCode);
    }
	
	public function profitSharingList ()
    {
        $op = array();
        $data = $filter = array();
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $filter['search_text'] = isset($postdata['search_text']) && !empty($postdata['search_text']) ? $postdata['search_text'] : NULL;
            $filter['from'] = isset($postdata['from']) && !empty($postdata['from']) ? $postdata['from'] : NULL;
            $filter['to'] = isset($postdata['to']) && !empty($postdata['to']) ? $postdata['to'] : NULL;
            $data['status'] = $postdata['status'];
        }
        if ($this->request->isMethod('post'))
        {
            $ajaxdata = [];
            $ajaxdata['data'] = array();
            if (isset($postdata['draw']))
            {
                $ajaxdata['draw'] = $postdata['draw'];
            }
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->getProfitSharingList($data, true);

            if (!empty($ajaxdata['recordsTotal']))
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->suppObj->getProfitSharingList($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    $ajaxdata['data'] = $this->suppObj->getProfitSharingList($data);
                }
            }
            $this->statusCode = 200;
			return Response::json($ajaxdata, $this->statusCode);            
        }
        else
        {
            return view('admin.seller.profit_sharing', $data);
        }
    }
	
	public function profitSharingDetails ($for, $id)
    {
        $op = $data = [];
        $data['id'] = $id;
        if ($details = $this->suppObj->getProfitSharingDetails($data))
        {
            $op[$for] = $details;
            $op['status'] = $this->statusCode = 200;
        }
        else
        {
            $op['status'] = $this->statusCode = 404;
            $op['msg'] = trans('general.not_found', ['which'=>trans('general.retailers.profit-sharing.title')]);
        }
        return Response::json($op, $this->statusCode);
    }
	
	public function profitSharingStatusUpdate ($id, $status)
    {
        $op = [];
        $data = $this->request->all();
        $data['id'] = $id;
        $data['status'] = Config::get('constants.SELLER.PROFIT_SHARING.STATUS.'.strtoupper($status));
        $data['account_id'] = $this->userSess->account_id;
        $res = $this->suppObj->profitSharingStatusUpdate($data);		
        if ($res)
        {
            $op['status'] = $this->statusCode = 200;
            $op['msg'] = 'Updated Successfully';
        }
        else
        {
            $op['status'] = $this->statusCode = 404;           
        }
		return Response::json($op, $this->statusCode);       
    }

}
