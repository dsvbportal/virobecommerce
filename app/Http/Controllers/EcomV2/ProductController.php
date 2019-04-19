<?php
namespace App\Http\Controllers\EcomV2;

use App\Http\Controllers\ecomBaseController;
//use App\Http\Controllers\ecomBaseController;
use App\Helpers\CommonNotifSettings;
use App\Models\Commonsettings;
use App\Models\CommonModel;
use App\Models\BaseModel;
use guzzle;
use Cart;

class ProductController extends ecomBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->commonObj = new Commonsettings;
        $this->CommonModel = new CommonModel;

        //$this->myAccountObj = new MyAccount($this->commonObj);
    }

    public function productList($category_slug)
    {
        $data = [];
		$postdata = $this->request->all();
        $data['searchtxt']  = isset($postdata['searchTerm'])?$postdata['searchTerm']:'';
        $data['category']   = $category_slug;	
        return view('shopping.product.browse', $data);
    }

    /* Browse Product */
    public function browse_products()
    {
        $op 		= [];
        $postdata 	= $this->request->all();	
		$searchTerm = '';
		$category_slug = 'all';
		if(isset($postdata['searchTerm']) && !empty( $postdata['searchTerm'])){
			$searchTerm =  $postdata['searchTerm'];
		}
		if(isset($postdata['category_slug']) && !empty( $postdata['category_slug'])){
			$category_slug =  $postdata['category_slug'];
		}		
	    if (!empty($postdata)) {
			
            $res = guzzle::getResponse($this->config->get('services.api.url').'product/'.$category_slug.'?searchTerm=' . $searchTerm, 'POST', [], $postdata);	
        //   echo'<pre>';print_r($res);die();
            if (!empty($res)) {
				if(isset($res->breadcrums)) {				
					array_walk($res->breadcrums, function(&$breadcrum) {
						$breadcrum->url = $this->CommonModel->generateUrl($breadcrum);
					});
				}	
				if(isset($res->categories)) {				
					array_walk($res->categories, function(&$categorie) {
						$categorie->url = $this->CommonModel->generateUrl($categorie);						
						if(isset($categorie->children)) {				
							array_walk($categorie->children, function(&$childrens) {
								$childrens->url = $this->CommonModel->generateUrl($childrens);
							});
						}						
					});
				}			
				if(isset($res->products)) {
					array_walk($res->products, function(&$product) {
						$product->add_cart_url = route('ecom.product.add-to-cart',['code'=>$product->code]);
						$product->url = $this->CommonModel->generate_productDetailsUrl($product);						
					});
				} 			
				
				//echo"<pre>";print_r($res);exit;
			/* 	if($res->relatedProducts) {
					array_walk($res->relatedProducts, function (&$products) {
						$products->url = $this->CommonModel->generateUrl($products);
					});
				}		 */		
                $op = $res;
            }
        }
        $this->statusCode = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* Login */
    public function login()
    {
        $data = array();
        $data['url'] = '';
        if (empty($this->account_id) && !isset($this->account_id)) {
            $data['lfields'] = CommonNotifSettings::getHTMLValidation('ecom.login');
            $data['fpfields'] = CommonNotifSettings::getHTMLValidation('ecom.forgot_pwd');
            $data['rpfields'] = CommonNotifSettings::getHTMLValidation('ecom.reset_pwd');
            return view('ecom.login', $data);
        } else {
            // If Session Exist Redirect to Home Page           
            return $this->redirect->route('ecom.home');
        }
    }

    /* Logout */
    public function logout()
    {
        $op = $postdata = [];
        //$postdata['account_log_id'] = $this->userSess->account_log_id;
        if ($this->session->has('userdata')) {
            $res = guzzle::getResponse('api/v1/user/logout', 'POST', [], []);
            if (!empty($res)) {
                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.TEMPORARY_REDIRECT')) {
                        $this->session->forget('userdata');
                        $this->config->set('app.accountInfo', null);
                        $op['msg'] = $res->msg;
                    }
                }
            }
        }
        $op['url'] = route('ecom.login');
        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* Check Login */
    public function checklogin()
    {
        $op = [];
        $op['msg'] = 'Something went wrong.';
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();
        if (!empty($postdata)) {
            $res = guzzle::getResponse('api/v1/user/login', 'POST', [], $postdata);
            if (!empty($res)) {
                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.SUCCESS')) {

                        $this->session->put($this->sessionName, $res);
                        $device_log = $this->config->get('device_log');
                        $device_log->token = $res->token;
                        $this->config->set('device_log', $device_log);
                        $op['has_pin'] = $res->has_pin;
                        $op['token'] = $res->token;
                        $op['account_id'] = $res->account_id;
                        $op['full_name'] = $res->full_name;
                        $op['first_name'] = $res->first_name;
                        $op['last_name'] = $res->last_name;
                        $op['uname'] = $res->uname;
                        $op['is_merchant'] = 0;
                        $op['user_code'] = $res->user_code;
                        $op['account_type'] = $res->account_type;
                        $op['account_type_name'] = $res->account_type_name;
                        $op['mobile'] = $res->mobile;
                        $op['email'] = $res->email;
                        $op['gender'] = $res->gender;
                        $op['dob'] = $res->dob;
                        $op['language_id'] = $res->language_id;
                        $op['currency_id'] = $res->currency_id;
                        $op['currency_code'] = $res->currency_code;
                        $op['country_flag'] = $res->country_flag;
                        $op['is_mobile_verified'] = $res->is_mobile_verified;
                        $op['is_email_verified'] = $res->is_email_verified;
                        $op['is_affiliate'] = $res->is_affiliate;
                        if (isset($res->can_sponser)) {
                            $op['can_sponser'] = $res->can_sponser;
                        }
                        $op['account_log_id'] = $res->account_log_id;
                        $op['profile_img'] = $res->profile_img;
                        $op['is_verified'] = $res->is_verified;
                        $op['country'] = $res->country;
                        $op['country_code'] = $res->country_code;
                        $op['country_id'] = $res->country_id;
                        $op['phone_code'] = $res->phone_code;
                        $op['has_pin'] = $res->has_pin;
                        $op['is_guest'] = $res->is_guest;
                        $op['toggle_app_lock'] = $res->toggle_app_lock;
                       
                    }
                    $op['msg'] = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    unset($op['msg']);
                }
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* Forgot password */
    public function forgot_password()
    {
        $op = [];
        $op['msg'] = 'Something went wrong.';
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $postdata = $this->request->all();
        if (!empty($postdata)) {
            $res = guzzle::getResponse('api/v1/user/forgot-pwd', 'POST', [], $postdata);
            if (!empty($res)) {
                if (isset($res->status)) {
                    $op['code'] = $res->code;
                    $op['token'] = $res->token;
                    $op['msg'] = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    unset($op['msg']);
                }
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* Reset password */
    public function reset_pwd()
    {
        $op = [];
        $op['msg'] = 'Something went wrong.';
        $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        $header['token'] = $this->request->header('token');
        $postdata = $this->request->all();
        if (!empty($postdata)) {
            $res = guzzle::getResponse('api/v1/user/reset-pwd', 'POST', $header, $postdata);
            if (!empty($res)) {
                if (isset($res->status)) {
                    $op['msg'] = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    unset($op['msg']);
                }
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function profile()
    {
        $data = array();
        $data['gender'] = trans('ecom/account.gender');
        $data['pufields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update');
        return view('ecom.account.profile_update', $data);
    }

    /* Update Profile  */
    public function updateProfile()
    {
        $op = [];
        $postdata = $this->request->all();
        $postdata['account_id'] = $this->userSess->account_id;
        if (!empty($postdata)) {
            $res = guzzle::getResponse('api/v1/user/profile-settings/profile/update', 'POST', [], $postdata);
            if (!empty($res)) {
                if (isset($res->status)) {
                    if ($res->status == $this->config->get('httperr.SUCCESS')) {
                        $op['first_name'] = $this->userSess->first_name = $postdata['first_name'];
                        $op['last_name'] = $this->userSess->last_name = $postdata['last_name'];
                        $op['gender'] = $this->userSess->gender = $postdata['gender'];
                        $op['dob'] = $this->userSess->dob = $postdata['dob'];
                        $this->userSess->uname = $postdata['display_name'];
                        $this->session->set($this->sessionName, $this->userSess);
                        $this->config->set('app.accountInfo', $this->userSess);
                        $this->config->set('data.user', $this->userSess);
                    }
                    $op['msg'] = $res->msg;
                    $op['status'] = $this->statusCode = $res->status;
                } elseif (isset($res->error)) {
                    $op['error'] = $res->error;
                    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                }
            } else {
                $op['msg'] = trans('ecom/account.edit_profile.no_changes');
                $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            }
        } else {
            $op['msg'] = trans('ecom/account.edit_profile.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changepassword()
    {
        $data = array();
        $data['cpfields'] = CommonNotifSettings::getHTMLValidation('ecom.account.update-pwd');
        return view('ecom.account.change_pwd', $data);
    }

    public function updatepwd()
    {
        $op = [];
        $postdata = $this->request->all();
        $postdata['account_id'] = $this->userSess->account_id;
        if ($this->userSess->pass_key == md5($this->request->current_password)) {
            if ($this->userSess->pass_key != md5($this->request->conf_password)) {
                if ($res = guzzle::getResponse('api/v1/user/change-pwd', 'POST', [], $postdata)) {
                    if (isset($res->status)) {
                        if ($res->status == $this->config->get('httperr.SUCCESS')) {
                            $this->userSess->pass_key = md5($this->request->password);
                            $this->session->set($this->sessionName, $this->userSess);
                            $this->config->set('app.accountInfo', $this->userSess);
                            $this->config->set('data.user', $this->userSess);
                        }
                        $op['msg'] = $res->msg;
                        $op['status'] = $this->statusCode = $res->status;
                    } elseif (isset($res->error)) {
                        $op['error'] = $res->error;
                        $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                    }
                } else {
                    $op['msg'] = trans('ecom/account.changepwd.savepwd_unable');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            } else {
                $op['msg'] = trans('ecom/account.changepwd.newpwd_same');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        } else {
            $op['msg'] = trans('ecom/account.changepwd.curr_pwd_incorrect');
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function product_details($category_slug = '', $slug = '')
    {
        $data = [];
        $postdata = $this->request->all();       
        $res = guzzle::getResponse($this->config->get('services.api.url').'product/' . $category_slug . '/' . $slug . '?pid=' . $postdata['pid'], 'POST', [], $postdata);  

		//echo '<pre>';print_r($res);exit;	
        if($res){  
            if($res->breadcrums) {
                array_walk($res->breadcrums, function(&$breadcrum) {
                    $breadcrum->url = $this->CommonModel->generateUrl($breadcrum);
                });
            }		
            if($res->recentProducts) {
                array_walk($res->recentProducts, function(&$products) {
                    $products->url = $this->CommonModel->generate_productDetailsUrl($products);
                });
            }
            if($res->relatedProducts) {
                array_walk($res->relatedProducts, function (&$products) {
                    $products->url = $this->CommonModel->generate_productDetailsUrl($products);
                });
            }
        } 

        $data['details'] = $res;
        $data['in_wishlist']=false;
              
        $id=$res->productDetails->supplier_product_code;
        $w=Cart::instance('wishlist')->content();
        foreach ($w as $key => $value) {
        if($value->id==$id)
            {
             $data['in_wishlist']=true;
            }           
        }
           
        return view('shopping.product.product_details', $data);
    }

    public function product_add_cart()
    {
        $op = [];	
		
        if (isset($this->request->supplier_product_code) && !empty($this->request->supplier_product_code)) {
            if (!empty($this->request->category_url_str)) {
                $category = $this->request->category_url_str;
            } else {
                $category = 'category';
            }
            if (!empty($this->request->product_slug)) {
                $product = $this->request->product_slug;
            } else {
                $product = 'product';
            }			
			 
            //$res = guzzle::getResponse('api/v1/shopping/product/'.$category_slug.'/'.$slug.'?pid='.$postdata['pid'], 'POST', [], $postdata);
            $res = guzzle::getResponse($this->config->get('services.api.url').'product/'. $category . '/' . $product . '?pid=' . $this->request->supplier_product_code, 'POST', [], []);
         
            if (!empty($res)) {
                if (isset($res->productDetails->imgs[0])) {
                    if ($res->productDetails->imgs[0]->img_path->product_details) {
                        $img_data = $res->productDetails->imgs[0]->img_path->product_details;
                    }
                }
                $cart_count = Cart::instance('ecomCart')->count();
                $cart_data = json_decode(Cart::instance('ecomCart')->content(), true);
                $qny ='';
                    foreach($cart_data as $val){
                        if($val['id']==$this->request->supplier_product_code){
                            $qny += $val['qty'];
                            $row_ids[]=$val['rowId'];
                        }
                    }
                if(($qny+$this->request->product_qty)<$res->productDetails->stock){
                    $qqty = $this->request->product_qty;
                } else{
                    foreach($row_ids as $row){
                        Cart::instance('ecomCart')->remove($row);
                    }
                    $qqty = $res->productDetails->stock;
                }
				
				//$res->productDetails->numeric_price = '75,000.00';
                Cart::instance('ecomCart')->add(
                    array(
                        'id' => $res->productDetails->supplier_product_code,
                        'name' => $res->productDetails->product_name,
                        'qty' => $qqty,
                        'price' => $res->productDetails->numeric_price,
                        'options' => array(
                            'imgs' => $img_data,
                            'off_per' => $res->productDetails->off_per,
                            'old_price' => $res->productDetails->mrp_price,
                            'product_size' => $this->request->select_size,
                            'seller' => $res->productDetails->supplier,
                            'colour' => $this->request->product_colour,
                            'product_colour_id' => $this->request->product_colour_id,
                            'select_size_id' => $this->request->select_size_id,
                        )
                    )
                );
                if ($cart_count < Cart::instance('ecomCart')->count()) {
                    $this->statusCode = 200;
                    $op['cartCount'] = Cart::instance('ecomCart')->count();
                    $op['msg'] = trans('ecom/product.add_to_cart_success');
                    $op['status'] = $this->config->get('httperr.SUCCESS');
                }
            } else {
                $op['msg'] = trans('ecom/product.add_to_cart_fail');
                $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
            }
        } else {
            $op['msg'] = 'Product code missing';
            $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	public function cart_items_remove()
    {
        $row_id = $this->request->row_id;
        $result = Cart::instance('ecomCart')->remove($row_id);
        $this->statusCode = 200;
        $op['status'] = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
    public function cart_items_old()
    {
        //$cart_count = Cart::instance('ecomCart')->content();
        $this->statusCode = $this->config->get('httperr.SUCCESS');
        $cart_data = json_decode(Cart::instance('ecomCart')->content(), true);
        $demo = [];
        $total = 0;
        $tax_total=0;
        $data = $this->commonObj->get_currency($this->currency_id);
        foreach ($cart_data as $key => $value) {
            $value['sub_total_innumber'] =numer_formbat($value['subtotal'],2).' '. $data->currency;
            $demo['cart_details'][] = $value;
            $total += $value['subtotal'];
            $tax_total+= $value['tax'];
           // $demo['cart_details']['sub_total_innumber'] = number_format($value['subtotal'],2);
        }
        $demo['cart_count'] = Cart::instance('ecomCart')->count();
        $demo['tax']=$tax_total;
        $demo['tax']= number_format($tax_total,2). ' ' . $data->currency;

        $demo['total'] = $total;
        $demo['total'] = number_format($total,2). ' ' . $data->currency;
       

        return $this->response->json($demo, $this->statusCode, $this->headers, $this->options);
    }
	public function cart_items()
    {      
        $data = $this->commonObj->get_currency($this->currency_id);
       //Cart::instance('ecomCart')->destroy();
        $op['cart_count']        = number_format(Cart::instance('ecomCart')->count(), 0, '.', ',');
        $mycartcontent          = json_decode(Cart::instance('ecomCart')->content(), true);
        $currency               = null;
        $currency_symbol        = null;
        $op['cart_sub_total']   = $op['cart_tax'] = $op['cart_shipping_charge'] = $op['cart_total'] = 0;
        //Cart::instance('myCart')->destroy();
        //return Response::json(array_values(array_values($mycartcontent)), 200, $this->headers, $this->options);
        $op['cart_details']             = array_values(array_values($mycartcontent));
         foreach ($op['cart_details']  as $key => $value) {
            $op['cart_details'][$key]['subtotal'] = number_format($value['subtotal'],2).' '. $data->currency;

         }
        array_walk($mycartcontent, function(&$item) use(&$currency, &$currency_symbol,&$op)
        {
            $product_details = $this->commonObj->supplierProductDetails(['supplier_product_code'=>$item['id'], 'currency_id'=>$this->currency_id, 'country_id'=>$this->country_id, 'qty'=>$item['qty'], 'user'=>['postal_code'=>$this->postal_code, 'country_id'=>$this->country_id, 'region_id'=>$this->region_id, 'city_id'=>$this->city_id]]);
       
            if($product_details->tax_info->total_tax_per > 0){

                Cart::setTax($item['rowId'], $product_details->tax_info->total_tax_per);
            }else{
                Cart::setTax($item['rowId'], 0);
            }


            if (!empty($product_details))
            {
                $item = (object) array(
                    'id'=>$product_details->supplier_product_code,
                    'name'=>$product_details->product_name,
                    'qty'=>$item['qty'],
                    'price'=>$product_details->price,
                    'tax_amt'=>$product_details->tax,
                    'subtotal'=>$product_details->sub_total,
                    'options'=>array(
                        'tax'=>$product_details->tax,
                        'shipping_charge'=>$product_details->shipping_charge,
                        'off_per'=>$product_details->off_per,
                        'stock_status'=>$product_details->stock_status,
                        'imgs'=>$product_details->imgs
                    )
                );
            
                $op['cart_sub_total']   +=$product_details->sub_total;
                $op['cart_tax']         +=$product_details->tax;
                $op['cart_shipping_charge'] +=$product_details->shipping_charge;
                $op['cart_total']       +=$product_details->net_pay;
                $currency                = $product_details->currency;
                $currency_symbol         = $product_details->currency_symbol;
                $item->price         = $product_details->currency_symbol.' '.number_format($item->price, 2, '.', ',').' '.$product_details->currency;
                $item->subtotal = $product_details->currency_symbol.' '.number_format($item->subtotal, 2, '.', ',').' '.$product_details->currency;
                $item->options['tax'] = $product_details->currency_symbol.' '.number_format($item->options['tax'], 2, '.', ',').' '.$product_details->currency;
                $item->options['shipping_charge'] = $product_details->currency_symbol.' '.number_format($item->options['shipping_charge'], 2, '.', ',').' '.$product_details->currency;
                $item->qty = number_format($item->qty, 0, '.', ',');
            }
        });
        $op['cart_price_sub_total_numeric'] = number_format(($op['cart_sub_total'] + $op['cart_tax']+ $op['cart_shipping_charge']), 2, '.', ',').' '.$currency;
        $op['cart_price_sub_total'] = $currency_symbol.' '.number_format(($op['cart_sub_total'] + $op['cart_tax']+ $op['cart_shipping_charge']), 2, '.', ',').' '.$currency;



       $op['cart_sub_total_numeric']       = number_format($op['cart_sub_total'], 2, '.', ',').' '.$currency;
        $op['cart_sub_total']       = $currency_symbol.' '.number_format($op['cart_sub_total'], 2, '.', ',').' '.$currency;

        $op['cart_tax']             = $currency_symbol.' '.number_format($op['cart_tax'], 2, '.', ',').' '.$currency;
        $op['cart_shipping_charge'] = $currency_symbol.' '.number_format($op['cart_shipping_charge'], 2, '.', ',').' '.$currency;
        $op['cart_total']           = $currency_symbol.' '.number_format($op['cart_total'], 2, '.', ',').' '.$currency;
        $op['status']       = $this->config->get('httperr.SUCCESS');
        $this->statusCode                       = 200;
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }




	
    /* AddToWishList */
	/* public function add_to_wishlist($id)
	{		  
		$postdata = $this->request->all();
		//print_r($postdata);exit;
		//$id=$postdata['product_code'];
		$res = guzzle::getResponse('api/v1/shopping/product/details?pid='.$id, 'POST', [], []);   
       	if(!empty($res))
		{
		    $product_code = $res->productDetails->product_code;
		    $product_name = $res->productDetails->product_name;
		    $product_price = $res->productDetails->numeric_price;
		    $product_qty = '1';
		    $product_image = $res->productDetails->imgs[0]->img_path;          
		    $description = $res->productDetails->description; 
		    $supplier_product_code = $res->productDetails->supplier_product_code;			
			
		    Cart::instance('wishlist')->add(['id'=>$supplier_product_code, 'name' =>$product_name, 'price' => $product_price, 'qty' =>$product_qty,'options' => ['supplier_name' => 'dshjdf','image'=>$product_image]]);
			
			$op['msg'] ="Product added to wishlist successfully";
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['wishlist']=Cart::instance('wishlist')->content();						
		}
		else
		{
			$op['msg'] = "something went wrong";
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	} */
	
	/* Remove To Wishlist */
	public function remove_to_wishlist()
	{
	    $postdata=$this->request->all();
		$rowId=$postdata['row_id'];	
	    if(!empty($rowId)){

            Cart::instance('wishlist')->remove($rowId);
            $op['wishlist']= Cart::instance('wishlist')->content();
            $op['msg'] = 'Product removed to wishlist successfully';
            $op['status']=$this->statusCode = $this->config->get('httperr.SUCCESS');
		}else {
             $op['wishlist']= Cart::instance('wishlist')->content();
		   $op['msg'] = 'Something went wrong';
		   $op['status']=$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
	    }
    	return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
   /* 
    public function delivery_address_old()
    {
	    $data = [];
	    $postdata=array();
	    $res = guzzle::getResponse('api/v1/user/profile-settings/get-address', 'POST', [], $postdata);
		if($this->session->has('userdata'))
		{
			$data['address']=$res->address;          
			if($res->status==200)
			{
			    return view('ecom.product.select_address', $data);
			}
		}
		else
		{
		  return $this->redirect->route('ecom.login');  
		}        
    } */
	

	public function delivery_address()
    {
	    $data = [];
	    $postdata=array();	  
		if($this->session->has('userdata'))
		{
			$res = guzzle::getResponse('api/v1/user/profile-settings/get-address', 'POST', [], $postdata);	
			$data['address']= $res->address;                	
			return view('ecom.product.select_address', $data);		
		}
		else
		{
		  return $this->redirect->route('ecom.login');  
		}        
    }
	
    public function payment_types()
    {		
        $op = [];
        $postdata=array();
        $postdata['cart'] = json_decode(Cart::instance('ecomCart')->content(), true);
        $postdata['bill_amount'] = Cart::instance('ecomCart')->total();
        if($postdata)
        {
            $res = guzzle::getResponse('api/v1/shopping/purchase/payment-types', 'POST', [], $postdata);		
            $op['payment_type']= $res;
			$op['status']=$this->statusCode = $this->config->get('httperr.SUCCESS');			
            //return view('ecom.product.select_address', $data);
        }
    	return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
    public function cart_items_view()
    {
        $demo = [];		
        return view('shopping.product.cart_view', $demo);
    }

    public function update_cart_qty()
    {
        $op=[];
        if ($this->request->has('rowid') && $this->request->has('qty') && $this->request->get('qty') >= 0) {
            $data = $this->request->all();
            $product_details = Cart::instance('ecomCart')->content();
            $product_details = json_decode(json_encode($product_details));
            if (array_key_exists($data['rowid'], $product_details)) {
                $details = Cart::instance('ecomCart')->get($data['rowid']);
                $product_details = json_decode(json_encode($details));
                $data['supplier_product_code'] = $product_details->id;
                $res = guzzle::getResponse($this->config->get('services.api.url').'product/check-stock-avaliablity', 'POST', [], $data);
               // echo'<pre>';print_r($data['qty']);die();
                if ($res) {
                    if ( $res->product_max->stock_on_hand >= $data['qty']) {
                        Cart::instance('ecomCart')->update($data['rowid'], $data['qty']);
                        $op['msg'] = trans('general.product.stock_quantity_update_success');
                        $this->statusCode = 200;
                    } else if($data['qty']>$res->product_max->stock_on_hand) {
                        Cart::instance('ecomCart')->update($data['rowid'], $res->product_max->stock_on_hand);
                        $op['msg'] = trans('general.product.stock_quantity_not_available');
                        $op['error_quantity'] = true;
                        $op['max_value'] = $res->product_max->stock_on_hand;
                        $this->statusCode = 200;
                    } else{
                        $op['msg'] = trans('general.product.invalid');
                        $op['max_value'] = $res->product_max->stock_on_hand;
                        $this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                } else {
                    $op['msg'] = trans('general.product.invalid');
                    $this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);

    }
        /***********ambika***********/

        /* AddToWishList */

    public function add_to_wishlist()
    {     
        $op['msg'] = "something went wrong";
        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

		$postdata = $this->request->all();
        $res = guzzle::getResponse($this->config->get('services.api.url').'product/' . $postdata['category'] . '/' . $postdata['product'] . '?pid=' . $postdata['id'], 'POST', [], []);
		if (!empty($res)) {           
            $product_code = $res->productDetails->product_code;
            $product_name = $res->productDetails->product_name;
            $product_price =$res->productDetails->numeric_price;
            $product_qty = 1;
            $product_image = $res->productDetails->imgs[0]->img_path;
            $description = $res->productDetails->description;
            $supplier_product_code = $res->productDetails->supplier_product_code;        
            Cart::instance('wishlist')->add(['id' => $supplier_product_code, 'name' => $product_name, 'price' => $product_price, 'qty' => $product_qty, 'options' => ['supplier_name' => 'dshjdf', 'image' => $product_image]]);
            $op['msg'] = "Added to wishlist";
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['wishlist'] = Cart::instance('wishlist')->content();      	
		}
    
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);       
	}
     
	// public function del_wishlist()
	// {
	//     $postdata=$this->request->all();
	//     $rowId=$postdata['row_id'];
	//     if(!empty($rowId)){
	// 	    Cart::instance('wishlist')->remove($rowId);
	// 	    $op['msg'] = 'Wishlist item deleted successfully';
	// 	    $op['status']=$this->statusCode = $this->config->get('httperr.SUCCESS');
	//     }
	//     else
	//     {
	// 	   $op['msg'] = 'process failed';
	// 	   $op['status']=$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
	//     }
 //        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	// } 
		
	/*  Search Category */
	public function productSearch()
	{
		$op = $op['data']= [];			
		$postdata = $this->request->all();	
		if (!empty($postdata)) {		
			$res = guzzle::getResponse($this->config->get('services.api.url').'product/search', 'POST', [], $postdata);	          	
			if(!empty($res)) {	
                if(!empty($res->data)){			
					array_walk($res->data, function(&$result) {						
						$result->url = route('ecom.product.list',['category_slug'=>$result->url_str]).'?searchTerm='.$result->category;
					});	
				}
				$op['data'] = $res->data;	
				$op['status'] = $this->statusCode = $res->status;				
			}
		} else {		   
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');				
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}	

	/* public function add_to_wishlist($id)
	{
		print_r($id);exit;
		$postdata=$this->request->all();
		$id=$postdata['product_code'];
		$res = guzzle::getResponse('api/v1/shopping/product/details?pid='.$id, 'POST', [], $postdata);
		//print_r($res);exit;
		if(!empty($res))
		{
			 $product_code=$res->productDetails->product_code;
			 $product_name=$res->productDetails->product_name;
			 $product_price=200;
			 $product_qty=1;
			 $product_image=$res->productDetails->imgs[0]->img_path;
			 $description=$res->productDetails->description;
			 $supplier_product_code=$res->productDetails->supplier_product_code;


			 Cart::instance('wishlist')->add(['id' => $supplier_product_code, 'name' =>$product_name, 'price' => $product_price, 'qty' =>$product_qty,'options' => ['supplier_name' => 'dshjdf','image'=>$product_image]]);

				$op['msg'] ="added to wishlist";
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['wishlist']=Cart::instance('wishlist')->content();
		}
		else
		{
				$op['msg'] = "something went wrong";
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');

		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	} */

    /*  Search Category */
   /*  public function productSearch()
	{
		$op = [];
		$postdata = $this->request->all();
		if (!empty($postdata)) {
			$res = guzzle::getResponse('api/v1/shopping/product/search', 'POST', [], $postdata);
			if (!empty($res)) {
				$op['data'] = $res->category;
				$op['status'] = $this->statusCode = 200;
			}
		} else {
			$op['data'] = '';
			$op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	} */

    public function view_wishlist()
	{
		$data['product'] = json_decode(Cart::instance('wishlist')->content(), true);
        
		return view('ecom.product.wishlist', $data);
	}

    public function get_cat_products($cat)
	{
		$postdata = [];
		$res = guzzle::getResponse('api/v1/shopping/product/list?cat=' . $cat, 'POST', [], $postdata);
		echo "<pre>";
		print_r($res);
	}
	
	public function checkout()
	{
        $data = $this->request->all();
	    if(Cart::instance('ecomCart')->count() > 0){
			$cartdata = json_decode(Cart::instance('ecomCart')->content(),true);
			$postdata['cart'] = $cartdata;
			$postdata['address_id'] = $data['address_id'];
			$postdata['payment_type'] = $data['payment_id'];
            $res = guzzle::getResponse('api/v1/shopping/purchase/confirm-purchase', 'POST', [],$postdata);
            if($res){
                Cart::instance('ecomCart')->destroy();
                $op['msg'] = $res->msg;
                $op['status'] = $this->statusCode =$res->status;
            } else{
                $op['msg'] = trans('general.checkout.place_order_failed');
                $this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');

            }
		} else {
            $op['msg'] = trans('general.checkout.cart_empty');
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
}
