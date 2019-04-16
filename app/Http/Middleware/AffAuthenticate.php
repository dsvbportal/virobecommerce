<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Redirect;
use Request;
use Session;
class AffAuthenticate
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
			Config::set('data.user',Session::get('userdata'));
			
			if($this->affHasPermission()) {
				return $next($request);
			}
			else {
				$data['pagesettings'] = (object) Config::get('site_settings');
				$data['userSess'] = Session::get('userdata');
				return response(view('affiliate.forbidden',$data), 403);
			}
		}  
		else {		
			Redirect::to('affiliate/login')->send();
		}
    }
	
	
	public function affHasPermission(){
		$arr = [
			'aff.dashboard',
			'aff.package.my_packages',
			'aff.referrals.mydirects',
			'aff.referrals.myteam',
			'aff.referrals.my_geneology',
			'aff.wallet.fundtransfer',
			'aff.wallet.fundtransfer.history',
			'aff.reports.fast_start_bonus',
			'aff.ranks.myrank',
			'aff.reports.ambassador_bonus',
		];
		$acInfo  = Session::get('userdata');
		$curRoute = Route::currentRouteName();		
		if($acInfo->can_sponsor==0 && in_array($curRoute,$arr)){
			return false;
		} 
		else {
			return true;
		}
	}
}
