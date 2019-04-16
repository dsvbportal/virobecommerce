<?php

namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageLib;
use Config;
use URL;

class Admin extends Model
{

    public function __construct ()
    {
        //$this->imageObj = new ImageController();        
		$this->imageObj = new ImageLib();
    }
	
    public function pages_list_chosen ()
    {
        return DB::table(Config::get('tables.PAGES'))
                        ->selectRaw('page_id as id, page as text')
                        ->get();
    }   
}
