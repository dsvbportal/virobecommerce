<?php
namespace App\Models\ecom;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;

class AccountModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		$this->commonObj = new CommonModel;
    }

    public function save_contactus(array $arr =[]) 
    {		
	    if(!empty($arr)) 
	    {				 
		    $result = DB::table($this->config->get('tables.CONTACT_US'))->insertGetId($arr);		   
		    return (!empty($result)) ? $result : '';
		    
		}  
		return false;  
    }
    



   
}