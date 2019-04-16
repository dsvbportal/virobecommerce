<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use App\Models\LocationModel;

class CommonController extends AffBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
		$this->lcObj = new LocationModel();		
    }  
	
	public function getCountries()
	{		
		
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$opArray['list'] = $this->lcObj->getCountries();		
		
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
	}
	
	public function getState()
	{		
		$country_id = $this->request->has('country_id')? $this->request->country_id:0;
		if($country_id>0){
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$opArray['state'] = $this->lcObj->getState($country_id);
		}
		else {
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$opArray['msg'] = 'Select a Country';
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
	}
	
	public function getDistrict()
	{		
		$state_id = $this->request->has('state_id')? $this->request->state_id:0;
		if($state_id>0){
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');			
			$opArray['district'] = $this->lcObj->getDistrict($state_id);
		}
		else {
			$opArray['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$opArray['msg'] = 'Select a State';
		}
		return $this->response->json($opArray, $this->statusCode, $this->headers, $this->options);   
	}
}
