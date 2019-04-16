<?php
namespace App\Helpers;
use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;
use Lang;
use TWMailer;
use Request;
use Route;
use App\Http\Controllers\ecomBaseController;
use Guzzle\Http\Exception\ClientErrorResponseException;

class guzzle extends ecomBaseController
{		
	public function getResponse($url,$type='GET',array $header=[],array $arr=[])
	{
	    $this->client = new \GuzzleHttp\Client();		
		if(request()->header('usrtoken') !=''){
			$header['usrtoken'] = request()->header('usrtoken');  
		}elseif(isset($this->userSess->token) && !empty($this->userSess->token)){
			$header['usrtoken'] = $this->userSess->token;  
		}
	    $header['api-key'] = request()->header('api-key');	
		//$url = $url;		
		try {		
		    $request = $this->client->request($type,$url,['headers'=>$header,'form_params'=>$arr]);			
		    $this->response = json_decode($request->getBody()->getContents());
		  
		   /*  $this->response = $request->getBody()->getContents();
			print_r($this->response);exit; */
	        return $this->response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errors = json_decode($e->getResponse()->getBody()->getContents());
			return $this->errors;
        }
	}
	
	/* public function getResponse($url,$type='GET',array $header=[],array $arr=[], $multipart='')
	{	
	    $this->client = new \GuzzleHttp\Client();		
		if(request()->header('usrtoken') !=''){
			$header['usrtoken'] = request()->header('usrtoken');  
		}elseif(isset($this->userSess->token) && !empty($this->userSess->token)){
			$header['usrtoken'] = $this->userSess->token;  
		}
	    $header['api-key'] = request()->header('api-key');
		$url = 'http://localhost/pay_gyft/'.$url;			
		try {
			if(isset($multipart) && !empty($multipart)){			
	            $header['Content-Type'] = 'multipart/form-data';
				//print_r('qqwq');
				//$request = $this->client->request($type,$url,['headers'=>$header, 'multipart'=>$multipart]);
				//print_R($multipart);exit;
				$path = $multipart->getPathName();
				
		        $response = $this->client->request($type,$url,['multipart'=>[['name'=>'file_name','contents'=>$multipart, 'headers'=> ['Content-Type' => 'multipart/form-data']]],'headers'=>$header]);	
				
	    		$response = json_decode($response->getBody()->getContents());	
				print_R($response);exit;
			}else{ 
				$request = $this->client->request($type,$url,['headers'=>$header, 'form_params'=>$arr]);										
			}
	      
		    $this->response = json_decode($request->getBody()->getContents());				
		  //  $this->response = $request->getBody()->getContents();
	        return $this->response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errors = json_decode($e->getResponse()->getBody()->getContents());
			return $this->errors;
        }
	} */
} 