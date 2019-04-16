<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Validator;
use Closure;
use Log;
use session;

class ValidateRequest
{
    public function handle ($request, Closure $next)
    {   	
        if ($request->isMethod('post') || $request->isMethod('get'))
        {	
            $route_name  = Route::currentRouteName();			
	        $session 	 = session();
            $userInfo 	 = (object) session(config('app.role'));
			$validateRules = [];
			if($request->is('affiliate/*')) {				
				$userInfo 	 =  session('userdata');				
				$regMissingFlds = (session()->has('regSess'))? session()->get('regSess')->regMissingFlds:[];				
				$validateRules = ['aff'=>include('validations/affiliate.php')];				
			} else if($request->is('admin/*')) {
				$validateRules = ['admin'=>include('validations/admin.php')];
			} else if($request->is('channel-partner/*')) {
				$validateRules = ['fr'=>include('validations/franchisee.php')];
			} else if($request->is('api/v1/user/*')) {
				$validateRules = ['api'=>['v1'=>['user'=>include('validations/user_api.php')]]];

			} else {
				$validateRules = ['ecom'=>include('validations/ecom.php')];   
			}
			$validations = array_get(array_merge($validateRules), $route_name);
			//print_r($validations);exit;
			/*
            $validations = array_get(array_merge(
			($request->is('affiliate/*') ? ['aff'=>include('validations/affiliate.php')] : 
			($request->is('admin/*') ? ['admin'=>include('validations/admin.php')] : 
			(($request->is('seller/*') || $request->is('api/v1/seller/*')) ? ['seller'=>include('validations/seller.php')] : 
			($request->is('api/v1/affiliate/*') ? ['api'=>['v1'=>['affiliate'=>include('validations/affiliate_api.php')]]] : 
			($request->is('api/v1/user/*') ? ['api'=>['v1'=>['user'=>include('validations/user_api.php')]]] : [])))))), $route_name);	*/			
			
			$validations = is_array($validations) ? array_filter($validations) : [];		
		
            if (!empty($validations))
            {
                $rules = array_key_exists('RULES', $validations) ? $validations['RULES'] : [];
                $messages = array_key_exists('MESSAGES', $validations) ? $validations['MESSAGES'] : [];
                $attributes = array_key_exists('LABELS', $validations) ? $validations['LABELS'] : [];
                $attributes = is_array($attributes) ? $attributes : trans($attributes);
                array_walk($attributes, function(&$attribute)
                {
                    $a = trans($attribute);
                    $attribute = !empty($a) ? $a : $attribute;
                });
                array_walk($messages, function(&$msgLang)
                {
                    $a = trans($msgLang);
                    $msgLang = !empty($a) ? $a : $msgLang;
                });
                
                $reqData = $request->all();                
                $validator = Validator::make($reqData, $rules, $messages, $attributes);
                if ($validator->fails())
                {
                    $response = [];
                    $response['error'] = $validator->messages(true);
                    return response()->json($response, config('httperr.PARAMS_MISSING'));
                }
            }
            else if (!is_array($validations))
            {
                Log::error('Validation Configuration missing for the route: '.$route_name);
                abort(500, 'Validation Configuration missing for the route: '.$route_name);
            }
        }
        return $next($request);
    }
}