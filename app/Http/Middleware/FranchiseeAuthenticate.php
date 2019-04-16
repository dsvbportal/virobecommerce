<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Redirect;
use Request;
use Session;
class FranchiseeAuthenticate
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
        if (Session::has('frdata'))
		{			
			Config::set('data.franchisee',Session::get('frdata'));			
			return $next($request);			
		}  
		else {		
			Redirect::to('channel-partner/login')->send();
		}
    }
}
