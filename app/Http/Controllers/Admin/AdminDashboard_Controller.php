<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\AdminController;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminCatalog;
use App\Helpers\ShoppingPortal;
use App\Models\MemberAuth;
use App\Models\Admin\SupplierProductOrder;
use App\Models\Admin\AdminPackages;
use View;
use Input;
use Config;
use Response;

class AdminDashboard_Controller extends AdminController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
		$this->pkObj = new AdminPackages;
    }

    public function dashboard ()
    {
        $data = [];        
        return View('admin.dashboard', $data);
    }

    public function change_password ()
    {
        return View::make('admin.changepassword');
    }

    public function updatePasswrord ()
    {
        $op = [];
        $post = Input::all();
        $post['account_id'] = $this->userSess->account_id;
		$op = $this->commonObj->changePassword($post);
        $this->statusCode = $op['status'];
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

}
