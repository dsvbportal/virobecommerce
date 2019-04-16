<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Redirect;
use Request;
use Session;
class EcomAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {	
        if (Session::has('userdata'))
		{
			//Config::set('data.user',Session::get('userdata'));			
			Config::set('app.accountInfo',Session::get('userdata'));
			return $next($request);			
		}  
		else {		
			Redirect::to('/')->send();
		}
    }
}
