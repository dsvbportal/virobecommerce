<?php
namespace App\Models;

use DB;
use App\Models\BaseModel;

class LocationModel extends BaseModel{

    public function __construct ()
    {
         parent::__construct();	 
    }
	
	public function getCountries($arr=array()) {	
	    $operate = '-1';
		extract($arr);
		$qry = DB::table(config('tables.LOCATION_COUNTRY').' as lc')
			->join($this->config->get('tables.CURRENCIES').' as cu', 'cu.currency_id', '=', 'lc.currency_id')
			->select('lc.country_id', DB::raw('lc.country as country_name'),'iso2','lc.phonecode','lc.currency_id',DB::raw('cu.currency as currency_code'))
			->where('lc.status', '=', 1);
		
		if($operate>=0){
			$qry->where('lc.operate', '=', $operate);
		}
		if(!empty($country_code)){
			$qry->where('iso2', '=', $country_code);
		}
		if(isset($country_id) && $country_id>0){
			$qry->where('lc.country_id', '=', $country_id);
		}
		
		$result = (isset($country_code) || isset($country_id))? $qry->first():$qry->get();		
		if(!empty($result)) {
			return $result;
		}
		return [];
	}
	
	
	public function getCountry($arr = []){	

		$country_id=0;
		$operate = '-1';
		$country_code = '';
		$op = false;
		extract($arr);
		$operate = '-1';
		if($country_id>0 || !empty($country_code)){
			$qry = DB::table(config('tables.LOCATION_COUNTRY'))
				->select('country_id','currency_id', DB::raw('country as country_name'),'iso2')
				->where('status', '=', 1);
	
			if($country_id>0){
				$qry->where('country_id', '=', $country_id);
			}			
			if(!empty($country_code)){
				$qry->where('iso2', '=', $country_code);
			}
			if($operate>=0){
				$qry->where('operate', '=', $operate);
			}
			
			$res = $qry->first();		
		
			if(!empty($res)) {
				$op =  $res;
			}
		}
		return $op;	
	}

	public function get_states_list ($country_id = '', $region_id = '')
    {
        $query = DB::table($this->config->get('tables.LOCATION_STATE'))
                ->where('status', $this->config->get('constants.ON'))
                ->orderby('state');
        if (!empty($country_id))
        {
            $query->where('country_id', $country_id);
        }
        if (!empty($region_id))
        {
            $query->where('region_id', $region_id);
        }
        return $query->get();
    }	
	
	public function get_region_list ($country_id = '')
    {
        $query = DB::table($this->config->get('tables.LOCATION_REGIONS'))
                ->where('status', $this->config->get('constants.ON'))
                ->orderBy('region', 'asc');
        if (!empty($country_id))
        {
            $query->where('country_id', $country_id);
        }
        $result = $query->get();
        return (!empty($result) && count($result) > 0) ? $result : false;
    }
	
	
	public function get_district_list ($state_id = '', $territory_state_id = '')
    {    
        $query = DB::table($this->config->get('tables.LOCATION_DISTRICTS'))
                ->where('status', $this->config->get('constants.ON'))
                ->orderBy('district', 'asc');
        if (!empty($state_id))
        {
            if (is_numeric($state_id))
                $query->where('state_id', $state_id);
            else
            {
                $query->where('state_id', function($d) use($state_id)
                {
                    $d->from($this->config->get('tables.LOCATION_STATE'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('state', $state_id)
                            ->pluck('state_id');
                });
            }
        }
        if (!empty($territory_state_id))
        {  
            if (is_numeric($territory_state_id))
                $query->orWhere('state_id', $territory_state_id);
            else
            {
                $query->where('state_id', function($d) use($territory_state_id)
                {
                    $d->from($this->config->get('tables.LOCATION_STATE'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('state', $territory_state_id)
                            ->pluck('state_id');
                });
            }
        }
        $result = $query->get();		
        return (!empty($result) && count($result) > 0) ? $result : false;
    }

    public function get_city_list ($state_id = 0, $district_id = 0, $city_id = 0)
    {
        if ($state_id>0 || $district_id>0 || $city_id > 0)
		{
			$query = DB::table($this->config->get('tables.LOCATION_CITY').' as lc')
					->where('lc.status', $this->config->get('constants.ON'));
			
			if (!empty($city_id))
			{					
				$query->where('lc.city_id', $city_id);					
			}
			if (!empty($state_id))
			{
				$query->where('lc.state_id', $state_id);
			}
			if (!empty($district_id))
			{
				$query->where('lc.district_id', $district_id);
			}
			
			$query->join(config('tables.LOCATION_DISTRICTS').' as ld','ld.district_id','=','lc.district_id')
					->join(config('tables.LOCATION_STATE').' as ls','ls.state_id','=','lc.state_id')
					->select('lc.city_id','lc.city','lc.state_id','lc.district_id','lc.pincode_id','lc.status','ls.state','ld.district');
			
			$result = $query->orderBy('lc.city', 'asc');
			
			$res = ($city_id>0)? $query->first() : $query->get();
			

			if (!empty($res))
			{
				return $res;
			}
		}
		return [];
    }
		
	public function getZipcodeDetails($pincode,$country_id) {				
		$res = DB::table(config('tables.LOCATION_PINCODES').' as lp')
				->join(config('tables.LOCATION_COUNTRY').' as lc','lc.country_id','=','lp.country_id')
				->join(config('tables.LOCATION_DISTRICTS').' as ld','ld.district_id','=','lp.district_id')
				->join(config('tables.LOCATION_STATE').' as ls','ls.state_id','=','ld.state_id')				
				->select('lc.country','lp.country_id','ls.state_id','ls.state','ld.district_id','ld.district')
				->where('lp.country_id', '=', $country_id)
				->where('lp.pincode', '=', $pincode)
				->where('lp.status', '=', $this->config->get('constants.ON'))
				->first();
		
		$op['country_id'] = $country_id;
		$op['state_id'] = 0;
		$op['postcode'] = $pincode;
		$op['city_id'] = 0;
		if($res){
			$op['district_id'] = $res->district_id;
			$op['state_id'] = $res->state_id;	
			$op['district'] = $res->district;
			$op['state'] = $res->state;				
		}
		return (object)$op;
	}
	
	public function getPincodeInfo (array $arr = array(), $with_values = false)
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
				$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$this->config->get('services.google.map_api_key').'&result_type=country|administrative_area_level_1|administrative_area_level_2|sublocality|locality|postal_code';
			} 
			else if (isset($pincode) && $pincode!=='')
			{
				$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$pincode.'+India&key='.$this->config->get('services.google.map_api_key').'&result_type=country|administrative_area_level_1|administrative_area_level_2|sublocality|locality|postal_code';
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
			//echo '<pre>';
			//print_r($result) ;
			
            if (!empty($result) && !empty($result->results[0]->address_components))
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
            $query = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                    ->leftJoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.country_id', '=', 'lc.country_id')
                    ->leftJoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.state_id', '=', 'ls.state_id')
                    ->leftJoin($this->config->get('tables.LOCATION_PINCODES').' as lp', function($join) use($pincode){
							$join->on('lp.district_id', '=', 'ld.district_id')
							->where('lp.pincode','=',$pincode);
					})
                    ->leftJoin($this->config->get('tables.LOCATION_CITY').' as ll', 'll.pincode_id', '=', 'lp.pincode_id')
                    ->selectRaw('lc.country,lc.country_id,lc.currency_id,ls.state,ls.state_id,ld.district_id,lc.distance_unit, lc.iso2 as country_code,lc.phonecode,lp.pincode as postcode');
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
                $query->leftjoin($this->config->get('tables.LOCATION_POPULAR_LOCALITIES').' as lpl', 'lpl.locality_id', '=', 'll.city_id')
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
                $locations->city = isset($locations->city) ? explode(',', $locations->city) : (!empty($city) ? $city : null);
                $locations->city_id = explode(',', $locations->city_id);
                $locations->lat = isset($lat) ? $lat : (isset($locations->lat) ? $locations->lat : null);
                $locations->lng = isset($lng) ? $lng : (isset($locations->lng) ? $locations->lng : null);
            }			
            return $locations;
        }
        return false;
    }
	
	public function getState($country_id,$state_id=0)
	{
		if (!empty($country_id) || $state_id>0)
		{
			$query = DB::table($this->config->get('tables.LOCATION_STATE').' as ls')                    
						->selectRaw('ls.state,ls.state_id')
						->where('ls.status','=',$this->config->get('constants.ON'));			
			
			if (!empty($country_id) && $country_id>0)
            {
                $query->where('ls.country_id', $country_id);
            }
			if (!empty($state_id) && $state_id>0)
            {
                $query->where('ls.state_id', $state_id);
            }
			
			$query->orderby('ls.state');
			
			$res = ($state_id>0)? $query->first() : $query->get();
			
			if (!empty($res))
			{
				return $res;
			}
		}		
		return [];
	}

	public function getDistrict($state_id,$district_id=0)
	{
		if (!empty($state_id) || $district_id>0)
		{
			$query = DB::table($this->config->get('tables.LOCATION_DISTRICTS').' as ld')                    
						->selectRaw('ld.district_id,ld.district')
						->where('ld.status','=',$this->config->get('constants.ON'));		
			
			if (!empty($state_id) && $state_id>0)
            {
                $query->where('ld.state_id', $state_id);
            }	
			
			if (!empty($district_id) && $district_id>0)
            {
                $query->where('ld.district_id', $district_id);
            }
			
			$res = $query->orderby('district')->get();
			
			$res = ($district_id>0)? $query->first() : $query->get();
			
			if (!empty($res))
			{
				return $res;
			}
		}
		return [];
	}

	public function get_territory_list ($state_id)
    {
        $result = DB::table($this->config->get('tables.LOCATION_STATE'))
                ->where('is_union_territory', $this->config->get('constants.ON'))
                ->where('linked_state_id', $state_id)
                ->where('status', $this->config->get('constants.ON'))
                ->select('state_id', 'state as state_name')
                ->get();
        return ($result) ? $result : false;
    }
	
	public function getRegionID ($state_id = '')
    {
        if (!empty($state_id))
        {
            return DB::table(config('tables.LOCATION_STATE'))
					->where('state_id', $state_id)
					->value('region_id');
        }
        return false;
    }
	
	public function addNewDistrict ($district_name, $state_id)
    {
        $result = DB::table(config('tables.LOCATION_DISTRICTS'))
                ->insertGetId(array(
            'district_name'=>$district_name,
            'state_id'=>$state_id));
        return $result;
    }
	
	public function addNewCity ($city_name, $state_id, $district_id)
    {
        $result = DB::table(config('tables.LOCATION_CITY'))
                ->insertGetId(array(
            'city_name'=>$city_name,
            'state_id'=>$state_id,
            'district_id'=>$district_id));
        return $result;
    }
	
	public function addNewTopCity ($city_name, $state_id, $district_id)
    {
        $result = DB::table(config('tables.LOCATION_TOP_CITY'))
                ->insertGetId(array(
            'city_name'=>$city_name,
            'state_id'=>$state_id,
            'status'=>$this->config->get('constants.ON')));
        return $result;
    }
	
	
	public function get_topcity_list ($state_id = 0, $district_id = 0, $city_id = 0)
    {
        if ($state_id>0 || $district_id>0 || $city_id > 0)
		{
			$query = DB::table($this->config->get('tables.LOCATION_TOP_CITY').' as lc')								
					->join(config('tables.LOCATION_DISTRICTS').' as ld','ld.district_id','=','lc.district_id')
					->join(config('tables.LOCATION_STATE').' as ls','ls.state_id','=','ld.state_id')
					->select('lc.city_id','lc.city_name as city','ls.state_id','lc.district_id','lc.status','ls.state','ld.district')
					->where('lc.status', $this->config->get('constants.ON'));
					
			if (!empty($city_id))
			{					
				$query->where('lc.city_id', $city_id);					
			}
			if (!empty($state_id))
			{					
				$query->where('ls.state_id', $state_id);					
			}			
			if (!empty($district_id))
			{
				$query->where('lc.district_id', $district_id);
			}
			
			$result = $query->orderBy('lc.city_name', 'asc');
			
			$res = ($city_id>0)? $query->first() : $query->get();
			

			if (!empty($res))
			{
				return $res;
			}
		}
		return [];
    }
	
	 public function getStateInfo($state_id){
		  $region=DB::table($this->config->get('tables.LOCATION_STATE').' as ls') 
					->join(config('tables.LOCATION_REGIONS').' as rg','rg.region_id','=','ls.region_id')
		            ->where('ls.state_id',$state_id)
		            ->where('ls.status', $this->config->get('constants.ON'))
					->select('ls.state_id','ls.state','rg.region_id','rg.region')
					->first();
		   if (!empty($region) && count($region) > 0){
				return $region;
			}
			return null;
	  }
}