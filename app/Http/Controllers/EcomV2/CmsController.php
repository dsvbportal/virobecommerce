<?php
namespace App\Http\Controllers\EcomV2;
use App\Http\Controllers\ecomBaseController;

use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\ecom\AccountModel;
use guzzle;

class CmsController extends ecomBaseController
{
    public function __construct ()
    {
        parent::__construct();
     
        $this->accObj = new AccountModel();
    }

    public function cmsPage ($cms_url)
    { 
        $content = guzzle::getResponse(config('services.api.url').'cms/page/'.$cms_url, 'POST', [], []);
        if (!empty($content)) 
		{   if($content->status==200){   
            
            if(isset($content->type)&&($content->type == 'json'))
            {
                $data['contents'][] =  $content;
            }else{
            	$data['contents'] = $content->data->contents;
            }
             return view('shopping.cms', $data);
           }
        }
        return app()->abort(404);
    }

}