<?php

namespace App\Http\Middleware;

use \App\Models\Api\CommonModel;
use Closure;

class SetLocation
{

    public function handle ($request, Closure $next, $type = 'browse')
    {
        $this->request = $request;
        $this->session = $request->session();
        $this->config = config();
        $this->siteConfig = $this->config->get('settings');
		
        /* if ($request->has('locality_id') && !empty($request->locality_id))
        {
			
            $geo = $this->session->has('geo') ? $this->session->get('geo') : (object) ['current'=>(object) ['formated_address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'1', 'currency_id'=>0, 'country'=>'', 'country_code'=>''], 'browse'=>(object) ['formated_address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'1', 'currency_id'=>0, 'country'=>'', 'country_code'=>'']];
			
            $locality_id = $request->locality_id;
            if ($geo->{$type}->locality_id != $locality_id)
            {
                $this->commonObj = new CommonModel($this);
                if ($type == 'browse')
                {
                    $location_info = $this->commonObj->getNearestPopularLocation(['locality_id'=>$locality_id]);
                    if ($location_info)
                    {
                        $location_info->distance_unit = !empty($location_info->distance_unit) ? $location_info->distance_unit : $this->config->get('constants.DISTANCE_UNIT.DEFAULT');
                        $geo->browse = $location_info;
                        $geo->current = !empty(array_filter((array) $geo->current)) ? $geo->current : $geo->current;
                        $request->session()->set('geo', $geo);
                    }
                }
                else
                {
                    $location_info = $this->commonObj->getLocationInfo(['locality_id'=>$locality_id]);
                    if ($location_info)
                    {
                        $location_info->distance_unit = !empty($location_info->distance_unit) ? $location_info->distance_unit : $this->config->get('constants.DISTANCE_UNIT.DEFAULT');
                        $geo->current = $location_info;
                        $geo->browse = !empty(array_filter((array) $geo->browse)) ? $geo->browse : $geo->current;
                        $request->session()->set('geo', $geo);
                    }
                }
            }
        } */
		$geo = $this->session->has('geo') ? $this->session->get('geo') : (object) ['current'=>(object) ['formated_address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>1, 'currency_id'=>0, 'country'=>'', 'country_code'=>'', 'boundries'=>''], 'browse'=>(object) ['formated_address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>1, 'currency_id'=>0, 'country'=>'', 'country_code'=>'', 'boundries'=>'']];
			
        if (!empty($request->header('lat')) && !empty($request->header('lng')))
        {
            $lat = $request->header('lat');
            $lng = $request->header('lng');		
			//$geo->current->lat = 0;
            //$geo->current->lng = 0;
            if (($lat != $geo->{$type}->lat) || ($lng != $geo->{$type}->lng))
            {			
				
				$geo->current->boundries = $this->boundingCoordinates($lat, $lng, $this->siteConfig->distance, $this->siteConfig->distance_unit);								
				$geo->current->lat = $lat;
				$geo->current->lng = $lng;								
	            $this->commonObj = new CommonModel($this);					
				if ($type == 'current')
                {
					/* $location_info = $this->commonObj->getLocationInfo(['lat'=>$lat, 'lng'=>$lng]);					
		            if($location_info)
                    {
                        $location_info->distance_unit = !empty($location_info->distance_unit) ? $location_info->distance_unit : $this->config->get('constants.DISTANCE_UNIT.DEFAULT');
                        $geo->current = $location_info;
						$geo->current->lat = $lat;
                        $geo->current->lng = $lng;
					} */					
				    $location_info = $this->commonObj->getNearestPopularLocation(['lat'=>$lat, 'lng'=>$lng, 'boundries'=>$geo->current->boundries]);	
									
                    if (!empty($location_info))
                    {
                        $location_info->distance_unit = !empty($location_info->distance_unit) ? $location_info->distance_unit : $this->config->get('constants.DISTANCE_UNIT.DEFAULT');
                        $geo->current = $location_info;  
						$geo->current->boundries = $this->boundingCoordinates($lat, $lng, $this->siteConfig->distance, $this->siteConfig->distance_unit);	
						//$geo->current->lat = $lat;
                        //$geo->current->lng = $lng;
						$request->session()->set('geo', $geo);
                    } else {
						$response = ['msg'=>'Service Not available for this location', 'status'=>config('httperr.UN_PROCESSABLE')];	
						return response()->json($response, config('httperr.UN_PROCESSABLE'));
					}
					
                }              
                else
                {
                    $location_info = $this->commonObj->getLocationInfo(['lat'=>$lat, 'lng'=>$lng]);
		            if($location_info)
                    {
                        $location_info->distance_unit = !empty($location_info->distance_unit) ? $location_info->distance_unit : $this->config->get('constants.DISTANCE_UNIT.DEFAULT');
                        $geo->current = $location_info;
                        $geo->browse = !empty(array_filter((array) $geo->browse)) ? $geo->browse : $geo->current;
                        $geo->current->lat = $lat;
                        $geo->current->lng = $lng;
                        $request->session()->set('geo', $geo);
                    }
                }
            }
        }  else if ((empty($request->header('lat')) && empty($request->header('lng'))) && (empty($geo->{$type}->lat) && empty($geo->{$type}->lng))) {
			$response = ['msg'=>'Set latitude and longitude', 'status'=>config('httperr.HEADER_MISSING')];	
			return response()->json($response, config('httperr.HEADER_MISSING'));
		} 
        return $next($request);
    }	
	
	function boundingCoordinates ($lat, $lng, $distance, $distance_unit = null)
    {
        //$distance = 10;
        if ($distance_unit == 1 || $distance_unit == null)
        {
            // earth's radius in km = ~6371
            $radius = 6371.01;
        }
        else
        {
            // earth's radius in Miles = ~3959
            $radius = 3959;
        }
        // latitude boundaries
        $maxlat = $lat + rad2deg($distance / $radius);
        $minlat = $lat - rad2deg($distance / $radius);

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxlng = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
        $minlng = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));

        return (object) ['minlat'=>$minlat, 'maxlat'=>$maxlat, 'minlng'=>$minlng, 'maxlng'=>$maxlng];
    }

}
