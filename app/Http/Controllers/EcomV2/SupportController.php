<?php
namespace App\Http\Controllers\ecom;
use App\Http\Controllers\ecomBaseController;
//use App\Http\Controllers\ecomBaseController;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\ecom\SupportModel;
use guzzle;
class SupportController extends ecomBaseController
{
    public function __construct ()
    {
        parent::__construct();
        //$this->myAccountObj = new MyAccount($this->commonObj);
        $this->supportObj = new SupportModel();
    }

    public function faqs()
    {
         $data =[];
         $data['faqs']= $this->supportObj->faq_categories();         
         return view('ecom.faqs', $data);
    }




}