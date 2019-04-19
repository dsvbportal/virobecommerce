<?php
namespace App\Http\Controllers\EcomV2;
use App\Http\Controllers\ecomBaseController;
//use App\Http\Controllers\ecomBaseController;
use App\Helpers\CommonNotifSettings;
use App\Models\CommonModel;
use App\Models\BaseModel;
use guzzle;

class DataController extends ecomBaseController
{
    public function __construct ()
    {
        parent::__construct();       
		$this->commonObj = new CommonModel;
    }

	/* Page Data */
	public function pageData ()
    {
		//echo config('services.api.url').'get-sliders';exit;
        $post = $this->request->all();
        $op = $data   = $requests = [];
        $post['page'] = isset($post['page']) && !empty($post['page']) ? $post['page'] : 'home';
        switch ($post['page'])
        {
            case 'home':
                //$op['sliders'] = guzzle::getResponse(config('services.api.url').'shopping/get-sliders', 'POST', [], ['page'=>'home']);				
				$result = guzzle::getResponse(config('services.api.url').'get-page-data', 'POST', [], ['page'=>'home']);	
				$op = (array)$result;
				
				if(isset($op['sliders']->slider) && (!empty($op['sliders']->slider))){									
					array_walk($op['sliders']->slider,function(&$sliders){					
						if(isset($sliders->blocks)){
							array_walk($sliders->blocks,function(&$slide){								
								$slide->url = $this->commonObj->generateSliderUrl($slide);													
							});
						}
					});
				}			
                /* $op['payment_types'] = ShoppingPortal::getResponse('api/v1/customer/get-payment-types', 'POST', []); */
				
				//$res['header_catalogue'] = guzzle::getResponse(config('services.api.url').'shopping/main-categories', 'POST', [], []);
                //$op['menus'] = guzzle::getResponse(config('services.api.url').'shopping/get-menus', 'POST', [], []);
				
				if(isset($op['menus']->menu) && (!empty($op['menus']->menu))){
					$cateloghead = $this->change_url($res['header_catalogue']->all_category);
					$op['menus']->menu->header_catalogue = $cateloghead;
				}		
				
				if(isset($op['menus']->header_primary) && (!empty($op['menus']->header_primary))){
					array_walk($op['menus']->header_primary,function(&$menu){
						$menu->url = $this->commonObj->generateUrl($menu);
						if(isset($menu->group)){
							array_walk($menu->group,function(&$sub_menu){
								if(!empty($sub_menu->links)){
									array_walk($sub_menu->links,function(&$links){
										$links->url = $this->commonObj->generateUrl($links);	
									});
								}
								//$sub_menu->url = url('/').$this->commonObj->generateUrl($sub_menu);	
							});
						}
					});
				}				
				//$res = guzzle::getResponse('api/v1/user/change-pwd', 'POST', [], $postdata)
               /* $op['my_cart'] = ShoppingPortal::getResponse('api/v1/customer/products/my-cart-count', 'POST', []); */
               // $res['main_categories'] = guzzle::getResponse(config('services.api.url').'shopping/main-categories', 'POST', [], []); 
           	
         	    array_walk($op['main_categories'], function(&$category)
				{
					$category->url = $this->commonObj->generateUrl($category);					
				});
				$op['main_categories'] = $op['main_categories'];
				
			    /* foreach($op['main_categories']->category as &$category){					
				    $category->url = $this->commonObj->generateUrl($category);					
			      }
				
				*/
								
                //echo"<pre>"; print_r($op['main_categories']);exit; 
			   
               /* $op['notifications'] = ShoppingPortal::getResponse('api/v1/get-notifications', 'POST', []); */			 
                break;
            /* case 'browse-products':
                $op['payment_types'] = ShoppingPortal::getResponse('api/v1/customer/get-payment-types', 'POST', []);
                $op['menus'] = ShoppingPortal::getResponse('api/v1/customer/get-menus', 'POST', []);
                $op['my_cart'] = ShoppingPortal::getResponse('api/v1/customer/products/my-cart-count', 'POST', []);
                $op['main_categories'] = ShoppingPortal::getResponse('api/v1/customer/main-categories', 'POST', []);
                $op['notifications'] = ShoppingPortal::getResponse('api/v1/get-notifications', 'POST', []);
                break;
            case 'product-details':
                $op['payment_types'] = ShoppingPortal::getResponse('api/v1/customer/get-payment-types', 'POST', []);
                $op['menus'] = ShoppingPortal::getResponse('api/v1/customer/get-menus', 'POST', []);
                $op['my_cart'] = ShoppingPortal::getResponse('api/v1/customer/products/my-cart-count', 'POST', []);
                $op['main_categories'] = ShoppingPortal::getResponse('api/v1/customer/main-categories', 'POST', []);
                $op['notifications'] = ShoppingPortal::getResponse('api/v1/get-notifications', 'POST', []);
                break; */
        }
        $this->statusCode = $this->config->get('httperr.SUCCESS');
        //return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

	public function change_url($data){
		if($data){
			array_walk($data, function($val)
			{
				$val->url = $this->commonObj->generate_productDetailsUrl($val);
				if(!empty($val->chiled)){
					if(is_array($val->chiled)){
						array_walk($val->chiled, function($ch_val) {
							$ch_val->url = $this->commonObj->generate_productDetailsUrl($ch_val);
							if(!empty($ch_val->chiled)){
								if(is_array($ch_val->chiled)){
									array_walk($ch_val->chiled, function($ch_aa) {
										$ch_aa->url = $this->commonObj->generate_productDetailsUrl($ch_aa);
									});
								}
							}
						});
					}
				}
			});
		}
		return $data;
		//echo'<pre>';print_r($data);die();

	}
	/* main_categories */
    public function childrensCategories ()
    { 
        $res = '';
		$this->statusCode = 406;
		$result = guzzle::getResponse('api/v1/main-categories', 'POST', [], []);
		if(!empty($result)){				
			if(isset($result->status)){				
				$res = $result->category;
				$this->statusCode = $result->status;				
			}					
		}
	    return $this->response->json($res, $this->statusCode, $this->headers, $this->options); 
	}	
}
