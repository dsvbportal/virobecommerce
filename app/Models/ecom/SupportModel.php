<?php
namespace App\Models\ecom;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;

class SupportModel extends BaseModel {

	public function faq_categories()
	{		
	    return DB::table($this->config->get('tables.FAQ_LANG'))
				->where('status', '=',$this->config->get('constants.ON'))
				->where('is_deleted', '=',$this->config->get('constants.NOT_DELETED'))
				->orderBy('id', '=','asc')
				->select('questions','answers')  
				->get();		
	}
}