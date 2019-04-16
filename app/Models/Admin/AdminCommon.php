<?php
namespace App\Models\Admin;
use DB;
use App\Models\BaseModel;
use App\Helpers\ImageLib;
use Config;
use URL;

class AdminCommon extends BaseModel
{

    public function __construct () {
        parent::__construct();		
    }    
}