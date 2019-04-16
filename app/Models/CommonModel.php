<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Config;

class CommonModel extends Model
{

    public function __construct ()
    {
        parent::__construct();
    }
    
    public function genders ()
    {
        return DB::table(config('tables.ACCOUNT_GENDER_LOOKUPS'))
                        ->select('gender_id', 'gender')
                        ->get();
    }
	
	public function relation_ships ()
    {
        return DB::table(config('tables.RELATION_SHIPS'))
                        ->select('relation_ship_id', 'relation_ship')
                        ->get();
    }

    public function get_currency_code ($currency_id)
    {
        return DB::table(config('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->value('currency');
    }

    public function get_user_balance ($account_id, $wallet_id, $currency_id)
    {
        fetch:
        $result = DB::table(config('tables.ACCOUNT_BALANCE'))
                ->where(array(
                    'account_id'=>$account_id,
                    'wallet_id'=>$wallet_id,
                    'currency_id'=>$currency_id))
                ->first();

        if (empty($result))
        {
            $curresult = DB::table(config('tables.CURRENCIES'))
                    ->where('id', $currency_id)
                    ->where('status', config('constants.ON'))
                    ->count();
            $ewalresult = DB::table(config('tables.WALLET'))
                    ->where(array(
                        'wallet_id'=>$wallet_id,
                        'status'=>config('constants.ON')))
                    ->count();

            if ($curresult && $ewalresult)
            {
                $insert['account_id'] = $account_id;
                $insert['current_balance'] = '0';
                $insert['tot_credit'] = '0 ';
                $insert['tot_debit'] = '0';
                $insert['currency_id'] = $currency_id;
                $insert['wallet_id'] = $wallet_id;
                $status = DB::table(config('tables.ACCOUNT_BALANCE'))
                        ->insert($insert);
                goto fetch;
            }
        }
        return $result;
    }
	
	public function getLocationInfo (array $arr = array(), $with_values = false, $geocode_only=false)
    {
        $pincode = null;
        $locality = null;
        $country = null;
        $state = null;
        $district = null;
        extract($arr);
		$addressParamArr = [];
        if ((isset($lat) && isset($lng)) || isset($pincode))
        {
            $ch = curl_init();
			$url = '';
			if (isset($lat) && isset($lng))
			{
				$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.config('services.google.map_api_key').'&result_type=country|administrative_area_level_1|administrative_area_level_2|sublocality|locality|postal_code';
			} 
			else if (isset($pincode) && $pincode!=='')
			{
				$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$pincode.'+India&key='.config('services.google.map_api_key').'&result_type=country|administrative_area_level_1|administrative_area_level_2|sublocality|locality|postal_code';
			}
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('Curl failed: '.curl_error($ch));
            }
            curl_close($ch);
            $result = json_decode($result);			
			
			if (!empty($result) && $geocode_only==true && !empty($result->results[0]->geometry))
            {
				return (object)['lat'=>$result->results[0]->geometry->location->lat,'lng'=>$result->results[0]->geometry->location->lng];
			}
            else if (!empty($result) && !empty($result->results[0]->address_components))
            {
			   
			   foreach ($result->results[0]->address_components as $c)
                {
                    if (in_array('country', $c->types))
                    {
                        $country = $c->long_name;						
                    }
                    if (in_array('administrative_area_level_1', $c->types))
                    {
                        $state = $c->long_name;						
                    }
                    if (in_array('administrative_area_level_2', $c->types))
                    {
                        $district = $c->long_name;						
                    }
                    if (in_array('sublocality', $c->types))
                    {
                        $locality = $c->long_name;						
                    }
                    if (empty($locality) && in_array('locality', $c->types))
                    {
                        $locality = $c->long_name;						
                    }
                    if (in_array('postal_code', $c->types))
                    {
                        $pincode = $c->long_name;						
                    }
                }
            }
        }
		$formated_address = implode(',',array_unique(array_filter([$locality,$district,$state.'-'.$pincode,$country])));
		
        if ((isset($pincode) && !empty($pincode)) || (isset($locality) && !empty($locality)) || (isset($city_id) && !empty($city_id)))
        {
            $query = DB::table(config('tables.LOCATION_COUNTRY').' as lc')
                    ->leftJoin(config('tables.LOCATION_STATE').' as ls', 'ls.country_id', '=', 'lc.country_id')
                    ->leftJoin(config('tables.LOCATION_DISTRICTS').' as ld', 'ld.state_id', '=', 'ls.state_id')
                    ->leftJoin(config('tables.LOCATION_PINCODES').' as lp', function($join) use($pincode){
							$join->on('lp.district_id', '=', 'ld.district_id')
							->where('lp.pincode','=',$pincode);
					})
                    ->leftJoin(config('tables.LOCATION_CITY').' as ll', 'll.pincode_id', '=', 'lp.pincode_id')
                    ->selectRaw('lc.country,lc.country_id,lc.currency_id,ls.state_id,ld.district_id,lc.distance_unit, lc.iso2 as country_code,lc.phonecode,lp.pincode');
            if (!empty($country))
            {
                $query->where(DB::raw('REPLACE(lc.country,\' \',\'\')'), str_replace(' ', '', $country));
            }
            if (!empty($country_id))
            {
                $query->where('lc.country_id', $country_id);
            }
            /*if (!empty($locality_id))
            {
                $query->leftjoin(config('tables.LOCATION_POPULAR_LOCALITIES').' as lpl', 'lpl.locality_id', '=', 'll.city_id')
                        ->where('ll.locality_id', $locality_id)
                        ->addSelect('lpl.latitude as lat', 'lpl.longitude as lng');
            }*/
            if (!empty($pincode))
            {
                $query->where('lp.pincode', $pincode)
                        ->addSelect(DB::raw('group_concat(ll.city_id) as city_id,group_concat(city) as city'))
                        ->groupby('lp.pincode');
            }
            else
            {
                $query->addSelect(DB::raw('ll.city_id, ll.city'));
            }
            $query->where(function($sq) use($state, $district, $pincode, $locality)
            {
                if (!empty($state))
                {
                    $sq->orWhere(function($s) use($state)
                    {
                        $s->whereNull('ls.state')
                                ->orWhere(DB::raw('REPLACE(ls.state,\' \',\'\')'), str_replace(' ', '', $state));
                    });
                }
                if (!empty($district))
                {
                    $sq->orWhere(function($s) use($district)
                    {
                        $s->whereNull('ld.district')
                                ->orWhere(DB::raw('REPLACE(ld.district,\' \',\'\')'), str_replace(' ', '', $district));
                    });
                }

                if (!empty($locality))
                {
                    $sq->orWhere(function($s) use($locality)
                    {
                        $s->whereNull('ll.city')
                          ->orWhere(DB::raw('REPLACE(ll.city,\' \',\'\')'), 'like', '%'.str_replace(' ', '', $locality).'%');
                    });
                }
            });
            $locations = $query->orderby('city')->first();
            if (!empty($locations))
            {
				$locations->formated_address = $formated_address;
                $locations->city = isset($locations->city) ? $locations->city : (!empty($city) ? $city : null);
                $locations->city_id = !$with_values ? explode(',', $locations->city_id)[0] : explode(',', $locations->city_id);
                $locations->lat = isset($lat) ? $lat : (isset($locations->lat) ? $locations->lat : null);
                $locations->lng = isset($lng) ? $lng : (isset($locations->lng) ? $locations->lng : null);
            }			
            return $locations;
        }
        return false;
    }

	public function createUserCode($id){			
		return date('y').sprintf('%06d', $id).rand(11,99);
	}

	public function getIpCountry ($ip='')
    {
        $ip = !empty($ip)? $ip: $_SERVER['REMOTE_ADDR'];
		try
        {
            //$ipInfo = json_decode(file_get_contents('http://ipinfo.io/'.$ip.'/json'));            
			//return !empty($ipInfo)? $ipInfo->country : '';
			return 'IN';
        }
        catch (Exception $e)
        {
            return NULL;
        }
    }

	
	
	
	public function getAccountLogToken ($account_log_id)
    {
        return DB::table(config('tables.ACCOUNT_LOG'))
                        ->where('account_log_id', $account_log_id)
                        ->where('is_deleted', config('constants.OFF'))
                        ->value('token');
    }
	
	
	public function saveResponse (array $pgr = array(), array $arr = array(), $withSuccessResponse = false)
    {
        extract($arr);
        $pgr_details = DB::table(config('tables.PAYMENT_GATEWAY_RESPONSE'))
                ->where('id', $pgr['id'])
                ->first();
        if (!empty($pgr_details))
        {
            $op = [];
            $op['status'] = $this->statusCode = config('httperr.UN_PROCESSABLE');
            $op['purpose'] = $pgr_details->purpose;
            if ($pgr_details->status != config('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED'))
            {
                $payment_status = $pgr['payment_status'];
                $pgr['payment_status'] = config('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.'.$pgr['payment_status']);
                $pgr['response'] = isset($pgr['response']) && !empty($pgr['response']) ? json_encode($pgr['response']) : null;
                $pgr = array_filter($pgr);
                DB::beginTransaction();
                $pgr['updated_on'] = getGTZ();
                DB::table(config('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $pgr['id'])
                        ->update($pgr);
                $pgr_details = DB::table(config('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $pgr['id'])
                        ->first();
                $pgr_details->pay_mode = trans('general.payment_modes.'.$pgr_details->pay_mode_id);
                switch ($pgr_details->purpose)
                {
                    case config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE'):
                        $this->storeObj = new Affiliate\PackageModel($this);
                        $trans_id = $this->pkObj->confirmPurchaseDeal($pgr_details->relative_post_id, $payment_status, $pgr_details->payment_id);
                        if ($trans_id)
                        {
                            $data = [];
                            $this->updateSessionFile($pgr_details->account_log_id, ['confirmPurchaseDeal'], true);
                            $this->accObj = new User\AccountModel($this);
                            $data['account_id'] = $pgr_details->account_id;
                            $data['order_code'] = DB::table(config('tables.PAY').' as p')
                                    ->join(config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                                    ->join(config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                                    ->where('p.pay_id', $pgr_details->relative_post_id)
                                    ->value('mo.order_code');
                            $data['lat'] = $lat;
                            $data['lng'] = $lng;
                            $data['distance_unit'] = $distance_unit;
                            $data['user_location'] = ['lat'=>$lat, 'lng'=>$lng, 'distance_unit'=>$distance_unit];
                            $op['deal'] = $this->accObj->getMyDealDetails($data);
                            $op['msg'] = trans('general.updated', ['which'=>'Deal', 'what'=>trans('general.actions.purchased')]);
                            $op['status'] = $this->statusCode = config('httperr.SUCCESS');
                        }
                        else
                        {
                            $op['deal'] = DB::table(config('tables.PAY').' as p')
                                    ->join(config('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'p.order_id')
                                    ->join(config('tables.PAYBACK_DEALS').' as pd', 'pd.pb_deal_id', '=', 'oi.pb_deal_id')
                                    ->where('p.pay_id', $pgr_details->relative_post_id)
                                    ->selectRaw('pd.deal_slug,pd.deal_code')
                                    ->first();
                            $op['status'] = $this->statusCode = config('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed');
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                    case config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'):
                        $this->addMoneyModel = new Retailer\AddMoney($this);
                        if ($this->addMoneyModel->confirmAddMoney($pgr_details->relative_post_id, $payment_status, $pgr_details->payment_id))
                        {
                            $op['status'] = $this->statusCode = config('httperr.SUCCESS');
                            $op['msg'] = trans('general.money_added', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = config('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed', []);
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                }
                if ($op['status'] == $this->statusCode = config('httperr.SUCCESS'))
                {
                    DB::table(config('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $pgr_details->id)
                            ->update(['status'=>config('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED')]);
                }
            }
            else if ($pgr_details->status == config('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED'))
            {
                switch ($pgr_details->purpose)
                {
                    case config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PACKAGE-PURCHASE'):
                        $this->accObj = new User\AccountModel($this);
                        $order_code = DB::table(config('tables.PAY').' as p')
                                ->join(config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                                ->join(config('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                                ->join(config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                                ->where('p.pay_id', $pgr_details->relative_post_id)
                                ->where('oi.status', config('constants.ORDER.ITEM.STATUS.BOUGHT'))
                                ->value('mo.order_code');
                        if ($order_code)
                        {
                            $data = [];
                            $this->updateSessionFile($pgr_details->account_log_id, ['confirmPurchaseDeal'], true);
                            $data['account_id'] = $pgr_details->account_id;
                            $data['order_code'] = $order_code;
                            $data['lat'] = $lat;
                            $data['lng'] = $lng;
                            $data['distance_unit'] = $distance_unit;
                            $data['user_location'] = ['lat'=>$lat, 'lng'=>$lng, 'distance_unit'=>$distance_unit];
                            $op['deal'] = $this->accObj->getMyDealDetails($data);
                            $op['msg'] = trans('general.updated', ['which'=>'Deal', 'what'=>trans('general.actions.purchased')]);
                            $op['status'] = $this->statusCode = config('httperr.SUCCESS');
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = config('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed');
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                    case config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'):
                        if (DB::table(config('tables.ADD_MONEY'))
                                        ->where('am_id', $pgr_details->relative_post_id)
                                        ->where('status', config('constants.PAYMENT_GATEWAY_RESPONSE.CONFIRMED'))
                                        ->exists())
                        {
                            $op['status'] = $this->statusCode = config('httperr.SUCCESS');
                            $op['msg'] = trans('general.money_added', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = config('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('affiliate/cashback.payment_failed', []);
                            $op['title'] = trans('affiliate/cashback.payment_failed_title');
                        }
                        break;
                }
            }
            else
            {
                $op['status'] = $this->statusCode = config('httperr.ALREADY_UPDATED');
                $op['msg'] = trans('affiliate/cashback.payment_already_done', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                $op['title'] = trans('affiliate/cashback.payment_already_done_title');
            }
            DB::commit();
            return $op;
        }
        DB::rollback();
        return false;
    }
	
	
	
	public function getShareToken($code)
    {
        if ($code)
        {
            $token = $hashcode = session()->getId().'.'.md5($code);
			$char_length = rand(2, 9);
            $position = $char_length % 2;
            /* if ($postion == 1) placed at the end else placed at the begining */
            $length = strlen($token);
            if ($position == 1)
            {
                /* IF odd number placed at the end */
                $part1 = substr($token, 0, $char_length);
                $part2 = substr($token, $char_length);
                $token = $char_length.$part2.$part1;
            }
            else if ($position == 0)
            {
                /* IF even number placed at the beginning */
                $part1 = substr($token, -$char_length);
                $part2 = substr($token, 0, -$char_length);
                $token = $char_length.$part1.$part2;
            }
            return ['hashcode'=>$hashcode,'decryHashcode'=>strrev($token)];
        }
    }
	
	public function decryptSharedToken($token)
    {
        if ($token)
        {
			$token = strrev($token);
            $char_length = $token[0];
            $position = $char_length % 2;
            $length = strlen($token);
            if ($position == 1)
            {
                $part1 = substr($token, -$char_length);
                $part2 = substr($token, 1, -$char_length);
                $token = $part1.$part2;
            }
            if ($position == 0)
            {
                $part1 = substr($token, 1, $char_length);
                $part2 = substr($token, $char_length + 1, $length);
                $token = $part2.$part1;
            }
            return $token;
        }
    }
	/* Change URL */	
	public function generateUrl ($apidata)
    {	
        $data 				  = [];
        $data[] 			  = 'product';
        if((isset($apidata->url_str) && !empty($apidata->url_str)) && $apidata->url_str != 'all')
        {		
	        $code = (isset($apidata->category_code)) ? '?spath='.$apidata->category_code:'';
            $data[] = $apidata->url_str.$code;
        }else{
			return '';
		}
        return url(implode('/', $data));
    }	
	
	public function generateSliderUrl ($apidata)
    {			
        $data 				  = [];
        $data[] 			  = 'product';
        if((isset($apidata->url) && !empty($apidata->url)) && $apidata->url != 'all')
        {		
	       /*  $code = (isset($apidata->category_code))?'?spath='.$apidata->category_code:'';
            $data[] = $apidata->url_str.$code; */			
			$data[] = (isset($apidata->url))? $apidata->url:'';          
        }else{
			return '';
		}
		//echo"<pre>";print_r($data);exit;
        return url(implode('/', $data));
    }	
	  public function generate_productDetailsUrl($product)
    {
        $data       = [];
        $data[]     = 'product';
        if (isset($product->url_str)&& ($product->url_str != 'all'))
        {
            $data[] = $product->url_str;
        }
        if(isset($product->category_url_str)){
            $data[] = $product->category_url_str;
        }
        if(isset($product->product_slug)){
            $data[] = $product->product_slug;
        }
        $url_string = url(implode('/', $data));
        if(isset($product->supplier_product_code)){
            $url_string = $url_string.'?pid='.$product->supplier_product_code;
        }
        /* if (isset($category->product_cmb_code))
        {
            $data[] = $category->product_slug.'-'.$category->cmb_sku.'?pid='.$category->product_code.'&cid='.$category->product_cmb_code;
        }
        else
        {
            $data[] = $category->product_slug.'?pid='.$category->product_code;
        } */
        return $url_string;
    }

}