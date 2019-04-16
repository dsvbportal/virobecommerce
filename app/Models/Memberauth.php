<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use Lang;
use Request;
use App\Models\Commonsettings;

class Memberauth extends Model
{

    public function __construct ()
    {
        //$this->commonObj = $commonObj;
		$this->commonstObj = new Commonsettings();
    } 

    public function validateUser ($arr = array(), $account_type_id = NULL)
    {	
        $account_type_id = !empty($account_type_id) ? $account_type_id : Config::get('constants.ACCOUNT_TYPE.USER');
		$role = !empty($role) ? $role : Config::get('app.role');
        extract($arr);
        $op = [
            'response'=>[
                'msg'=>'Parameter Missing'
            ],
            'statusCode'=>422];
		
        if (isset($username) && !empty($username) && isset($password) && !empty($password))
        {				
			 $userData = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                    ->selectRaw('am.account_id, am.account_type_id, concat(ad.firstname,\' \',ad.lastname) as full_name, am.email, am.mobile, am.uname, am.is_deleted, am.pass_key, am.login_block, ap.language_id, ap.currency_id, ap.is_mobile_verified, ap.is_email_verified, ap.send_email, ap.send_sms, ap.send_notification')
                    ->where('am.account_type_id', $account_type_id)
                    ->where(function($subquery) use($username)
                    {
                        $subquery->where('am.uname', $username)
                        ->orWhere('am.email', $username)
                        ->orWhere('am.mobile', $username);
                    })
                    ->first();
            if (!empty($userData))
            {				
                if ($userData->is_deleted == Config::get('constants.OFF'))
                {					
                    if ($userData->pass_key == md5($password))
                    {						
                        if ($userData->login_block == Config::get('constants.UNBLOCKED'))
                        {							
                            unset($userData->is_deleted);
                            unset($userData->pass_key);
                            unset($userData->login_block);
                            switch ($userData->account_type_id)
                            {
                                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                                    $op['response']['url'] = URL::to('admin/dashboard');
                                    $userData->admin_id = DB::table(Config::get('tables.ADMIN_MST'))
                                            ->where('account_id', $userData->account_id)
                                            ->where('is_deleted', Config::get('constants.OFF'))
                                            ->pluck('admin_id');
                                    break;
                                case Config::get('constants.ACCOUNT_TYPE.SELLER'):
										
                                    $s = DB::table(Config::get('tables.SUPPLIER_MST').' as sm')
                                            ->leftJoin(Config::get('tables.ACCOUNT_CREATION_STEPS').' as acs', 'acs.step_id', '=', 'sm.next_step')
                                            ->where('sm.account_id', $userData->account_id)
                                            ->where('sm.is_deleted', Config::get('constants.OFF'))
                                            ->select('sm.supplier_id', 'sm.completed_steps', 'sm.verified_steps','acs.route as next_step')
                                            ->first();
                                    if (!empty($s))
                                    {
                                        $userData->supplier_id = $s->supplier_id;
                                        $userData->next_step = $s->next_step;
                                        $userData->completed_steps = $s->completed_steps;
                                        $userData->verified_steps = $s->verified_steps;
                                        $userData->is_verified = $this->commonstObj->getSupplierVerificationStatus($userData->supplier_id);
                                        $op['response']['url'] = URL::to('seller/dashboard');
                                        if (!empty($userData->is_mobile_verified) && !empty($userData->is_email_verified))
                                        {
                                            $op['response']['url'] = URL::to('seller/dashboard');
                                           
                                        }
                                        else
                                        {                                            
                                            $op['response']['url'] = URL::to('seller/sign-up/mobile-verification');
                                        }
                                        $address = $this->commonstObj->getUserAddress($userData->account_id, $userData->account_type_id);
										if (!empty($address))
                                        {
                                            $userData->country_name = $address[0]->country;
                                            $userData->country_id = $address[0]->country_id;
                                        }
                                    }
                                    break;                                
                                case Config::get('constants.ACCOUNT_TYPE.USER'):
                                    $op['response']['url'] = URL::to('/');
                                    break;
                                default:
                                    $op['response']['url'] = URL::to('api/v1/customer');
                            }
                            
							$userData->token = Config::get('device_log')->token;
                            
							DB::table(Config::get('tables.DEVICE_LOG'))
                                    ->where('device_log_id', Config::get('device_log')->device_log_id)
                                    ->update(array('account_id'=>$userData->account_id, 'status'=>Config::get('constants.ACTIVE'))); 
                            
							DB::table(Config::get('tables.ACCOUNT_LOGIN_LOG'))
                                    ->insertGetID(array('device_log_id'=>Config::get('device_log')->device_log_id, 'account_id'=>$userData->account_id, 'login_on'=>date('Y-m-d H:i:s'))); 
                            
							DB::table(Config::get('tables.ACCOUNT_MST'))
                                    ->where('account_id', $userData->account_id)
                                    ->update(array('last_active'=>date('Y-m-d H:i:s')));
                           
  						    request()->session()->set($role, $userData);							
							$op['response']['msg'] = Lang::get('general.you_are_successfully_logged_in');
                            $op['response']['UserDetails'] = $userData;
                            $op['statusCode'] = 308;
                            $op['status'] = 'OK';
                        }
                        else
                        {
                            $op['response']['msg'] = Lang::get('general.your_account_has_been_blocked');
                            $op['statusCode'] = 403;
                            unset($op['response']['UserDetails']);
                        }
                    }
                    else
                    {
                        $op['response']['msg'] = Lang::get('general.incorrect_password');
                        $op['statusCode'] = 406;
                        unset($op['response']['UserDetails']);
                    }
                }
                else
                {
                    $op['response']['msg'] = Lang::get('general.your_account_not_avaliable_or_deleted');
                    $op['statusCode'] = 403;
                    unset($op['response']['UserDetails']);
                }
            }
            else
            {
                $op['response']['msg'] = Lang::get('general.incorrect_username');
                $op['statusCode'] = 406;
                unset($op['response']['UserDetails']);
            }
        }
        return (object) $op;
    }
	
	public function get_userbyId ($acc_id)
    {
		$user = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.DEVICE_LOG').' as dl', 'dl.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'am.account_type_id')
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
				->leftjoin(Config::get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
				->leftjoin(Config::get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')	
                ->selectRaw('am.account_id,am.is_affiliate, at.account_type_name, concat(ad.firstname,\' \',ad.lastname) as full_name, am.email, am.mobile, am.uname, am.account_type_id, dl.token, ap.language_id, ap.currency_id,cur.currency as currency_code, ap.is_mobile_verified, ap.is_email_verified, ap.send_email, ap.send_sms, ap.send_notification, am.pass_key')
                ->where('am.account_id', $acc_id)
                ->where('am.is_deleted', Config::get('constants.OFF'))
                ->first();					
        
        if (!empty($user))
        {
            switch ($user->account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
						$user->admin_id = DB::table(Config::get('tables.ADMIN_MST'))
								->where('account_id', $user->account_id)
								->where('is_deleted', Config::get('constants.OFF'))
								->value('admin_id');
						break;
				case Config::get('constants.ACCOUNT_TYPE.SELLER'):
					$s = DB::table(Config::get('tables.SUPPLIER_MST').' as s')
							->leftJoin(Config::get('tables.ACCOUNT_CREATION_STEPS').' as acs', 'acs.step_id', '=', 's.next_step')
							->where('s.account_id', $user->account_id)
							->where('s.is_deleted', Config::get('constants.OFF'))
							->select('s.supplier_id', 'acs.route as next_step')
							->first();
						if (!empty($s))
						{
							$user->supplier_id = $s->supplier_id;
							$user->next_step = $s->next_step;							
							/* $user->is_verified = DB::table(Config::get('tables.SUPPLIER_MST'))
													->where('supplier_id', $user->supplier_id)
													->value('is_verified'); */
							$data = DB::table(Config::get('tables.SUPPLIER_MST'))
													->where('supplier_id', $user->supplier_id)
													->selectRaw('is_verified, completed_steps, verified_steps')->first();
							$user->is_verified = $data->is_verified;
							$user->completed_steps = $data->completed_steps;
							$user->verified_steps = $data->verified_steps;
						}
					break;	             
            }
        }
        return $user;
    }

    public function get_userbyId_bk ($acc_id)
    {
        $user = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as lm', 'am.account_id', '=', 'lm.account_id')
                ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'lm.account_type_id')
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'lm.account_id')
                ->selectRaw('am.account_id, at.account_type_name, concat(am.firstname,\' \',am.lastname) as full_name,lm.email,lm.mobile, lm.uname, lm.account_type_id,lm.token,ap.language_id,ap.locale_id,ap.time_zone_id,ap.currency_id,ap.is_mobile_verified,ap.is_email_verified,ap.send_email,ap.send_sms,ap.send_notification')
                ->where('am.account_id', $acc_id)
                ->where('am.is_deleted', Config::get('constants.OFF'))
                ->first();
        if (!empty($user))
        {
            switch ($user->account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                    $user->admin_id = DB::table(Config::get('tables.ADMIN_MST'))
                            ->where('account_id', $user->account_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->pluck('admin_id');
                    break;
                case Config::get('constants.ACCOUNT_TYPE.SUPPLIER'):
                    $s = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS').' as s')
                            ->leftJoin(Config::get('tables.ACCOUNT_CREATION_STEPS').' as acs', 'acs.step_id', '=', 's.next_step')
                            ->where('s.account_id', $user->account_id)
                            ->where('s.is_deleted', Config::get('constants.OFF'))
                            ->select('s.supplier_id', 'acs.route as next_step')
                            ->first();
                    if (!empty($s))
                    {
                        $user->supplier_id = $s->supplier_id;
                        $user->next_step = $s->next_step;
                        $user->is_verified = $this->commonObj->getSupplierVerificationStatus($user->supplier_id);
                    }
                    break;
                case Config::get('constants.ACCOUNT_TYPE.PARTNER'):
                    $user->partner_id = DB::table(Config::get('tables.PARTNER'))
                            ->where('account_id', $user->account_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->pluck('partner_id');
                    break;
            }
        }
        return $user;
    }

    /**
     * @param int $account_id account_id which is login
     * @param int $device_log_id from which device have to logout
     * @return boolean true if logout else false
     */
    public function logoutUser ($account_id, $device_log_id)
    {
        if (DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('account_id', $account_id)
                        ->where('device_log_id', $device_log_id)
                        ->update(array('status'=>Config::get('constants.INACTIVE'))))
        {
            Config::has('data.user', null);
            return true;
        }
        return false;
    }

    /**
     * @param int $account_id account_id which is login
     * @param int $device_log_id from which device have to logout
     * @return boolean true if not logout else false
     */
    public function checkAutoLogout ($account_id, $device_log_id)
    {
        $cur = date('Y-m-d H:i:s');
        if (Request::isMethod('get'))
        {
            if (DB::table(Config::get('tables.ACCOUNT_MST'))
                            ->where('account_id', $account_id)
                            ->whereRaw('TIMESTAMPDIFF(MINUTE,last_active,\''.$cur.'\')<60')
                            ->exists())
            {
                DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(array('last_active'=>$cur));
                return true;
            }
            else
            {
                if ($this->logoutUser($account_id, $device_log_id))
                {
                    return false;
                }
            }
        }
        else
        {
            DB::table(Config::get('tables.ACCOUNT_MST'))
                    ->where('account_id', $account_id)
                    ->update(array('last_active'=>$cur));
            return true;
        }
    }

}
