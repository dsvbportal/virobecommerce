<?php
namespace App\Helpers;
use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;
use Lang;
use TWMailer;
use SGMailer;


class CommonNotifSettings
{    

    /**
     * @param string $which which mail to be send
     * @param int|array $id notification to whom single id or array of ids
     * @param int $account_type_id user type id <i>(default 2-customer)</i>
     * @param array $data datas required to frame a content <i>(default empty)</i>
     * @param bool $email send email <i>(default true)</i>
     * @param bool $sms send sms <i>(default true)</i>
     * @param bool $notification send notification <i>(default true)</i>
     * @param bool $must_send it must be sent without any checking <i>(default false)</i>
     *
     * @return array|false sent user list or false
     */
    public static function notify ($which, $id, $account_type_id = 2, $data = [],  $email = true, $sms = true, $notification = true, $must_send = false, $preview = false)
    {   		
		$id = is_array($id) ? array_filter($id) : [$id];
        if (!empty($id))
        {
            switch ($account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.SELLER'):
                    $account_ids = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->whereIn('supplier_id', $id)
                            ->orwhereIn('account_id', $id)
                            ->lists('account_id');
                    break;
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                    $account_ids = DB::table(Config::get('tables.ADMIN_MST'))
                            ->where('status', Config::get('constants.ACTIVE'))
                            ->whereIn('admin_id', $id)
                            ->lists('account_id');
                    break;                
                default:
                    $account_ids = $id;
            }
			
            $account_ids = array_filter($account_ids);           
				if (!empty($account_ids)) {
                $query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                        ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                        ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                        ->where('am.is_deleted', Config::get('constants.OFF'))
                        ->where('am.status', Config::get('constants.ACTIVE'))
                        ->whereIn('am.account_id', $account_ids)
                        //->whereNotNull('am.email')
                        //->whereNotNull('am.mobile')
                        ->selectRaw('am.account_id, concat(ad.firstname," ",ad.lastname) as name, am.uname, am.email, am.mobile, ap.send_email, ap.send_sms, ap.send_notification, ap.is_mobile_verified, ap.is_email_verified');
					$users_list = $query->get();	
				} else {
					$users_list[0] =  [
						'account_id'=>null,
						'uname'=>isset($data['uname']) && !empty($data['uname']) ? $data['uname'] : null,
						'mobile'=>isset($data['mobile']) && !empty($data['mobile']) ? $data['mobile'] : null,
						'email'=>isset($data['email']) && !empty($data['email']) ? $data['email'] : null,
						'name'=>isset($data['full_name']) && !empty($data['full_name']) ? $data['full_name'] : null,
						'is_email_verified'=>isset($data['is_email_verified']) && !empty($data['is_email_verified']) ? $data['is_email_verified'] : false,
						'is_mobile_verified'=>isset($data['is_mobile_verified']) && !empty($data['is_mobile_verified']) ? $data['is_mobile_verified'] : false,
						'push_notification'=>isset($data['push_notification']) && !empty($data['push_notification']) ? $data['push_notification'] : false,
						'lang'=>isset($data['lang']) && !empty($data['lang']) ? $data['lang'] : 'en'
					];
				}	
					
                //return $data = array_merge($data, Config::get('site_settings'));
				
                $settings = (object) config('notify_settings.'.strtoupper($which));	
                if (!empty($settings))
                {				
                    foreach ($users_list as $user)
                    {
						$user = (object) $user;
						$data['name'] = !empty($user->name) ? $user->name : null;
						$data['full_name'] = !empty($user->name) ? $user->name : null;
                        $data['uname'] = !empty($user->uname) ? $user->uname : null;						
                        //$must_send = $preview = 1;
						if (isset($settings->email) && $settings->email['status'] && ($email && ($must_send || ($user->send_email && $user->is_email_verified))))
                        {   
							self::email($user->email, $settings->email['view'], Lang::get($settings->email['subject']), $data, $preview);
                        }
                        if (isset($settings->sms) && $settings->sms['status'] && ($sms && ($must_send || ($user->send_sms && $user->is_mobile_verified))))
                        { 			
							self::sms($user->mobile, Lang::get($settings->sms['message'], $data));
                        }
                        if (isset($settings->notification) && $settings->notification['status'] && ($notification && ($must_send || $user->send_notification)))
                        {
                            //PushNotification::send($user->account_id, Lang::get($settings->notification['title'], $data), Lang::get($settings->notification['body'], $data), $settings->notification['click_action']);
                        }
                    }
					//return true;
                    return $users_list;
                }
            
        }
        return false;
    }
	
	/**
     * @param string $which which mail to be send
     * @param int|array $id notification to whom single id or array of ids
     * @param int $account_type_id user type id <i>(default 2-customer)</i>
     * @param array $data datas required to frame a content <i>(default empty)</i>
     * @param bool $email send email <i>(default true)</i>
     * @param bool $sms send sms <i>(default true)</i>
     * @param bool $notification send notification <i>(default true)</i>
     * @param bool $must_send it must be sent without any checking <i>(default false)</i>
     *
     * @return array|false sent user list or false
     */
    public static function affNotify ($which, $id, $account_type_id = 2, $data = [],$must_send = false, $preview = false)
    {
		$settings = (object) config('notify_settings.'.strtoupper($which));			
        if (!empty($id))
        {
			$id = is_array($id) ? array_filter($id) : [$id];
            switch ($account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.SELLER'):
                    $account_ids = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->whereIn('supplier_id', $id)
                            ->orwhereIn('account_id', $id)
                            ->lists('account_id');
                    break;
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                    $account_ids = DB::table(Config::get('tables.ADMIN_MST'))
                            ->where('status', Config::get('constants.ACTIVE'))
                            ->whereIn('admin_id', $id)
                            ->lists('account_id');
                    break;                
                default:
                    $account_ids = $id;
            }
            $account_ids = array_filter($account_ids);
			
            if (!empty($account_ids))
            {
                $query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                        ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                        ->leftjoin(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                        ->where('am.is_deleted', Config::get('constants.OFF'))
                        //->where('am.status', Config::get('constants.ACTIVE'))
                        ->whereIn('am.account_id', $account_ids)
                        //->whereNotNull('am.email')
                        //->whereNotNull('am.mobile')
                        ->selectRaw('am.account_id, concat(ad.firstname," ",ad.lastname) as name, am.uname, am.email, am.mobile, ap.send_email, ap.send_sms, ap.send_notification, ap.is_mobile_verified, ap.is_email_verified');
                $users_list = $query->get();			
														
                if (!empty($settings) && !empty($users_list))
                {
                  
               		
					foreach ($users_list as $user)
                    {
						$data['name'] = $user->name;
						$user->is_email_verified =1;
                        $must_send = 1;	
						if (isset($settings->email) && $settings->email['status'] && (($must_send || ($user->send_email && $user->is_email_verified))))
                        {   					
							self::email($user->email, $settings->email['view'], $settings->email['subject'], $data, $preview);
                        }
                        if (isset($settings->sms) && $settings->sms['status'] && (($must_send || ($user->send_sms && $user->is_mobile_verified))))
                        { 			
							self::sms($user->mobile, $settings->sms['message'],$data);
                        }
                        if (isset($settings->notification) && $settings->notification['status'] && (($must_send || $user->send_notification)))
                        {
                            //PushNotification::send($user->account_id, Lang::get($settings->notification['title'], $data), Lang::get($settings->notification['body'], $data), $settings->notification['click_action']);
                        }
                    }
					return true;
                    //return $users_list;
                }
            }
        }
		else {	
			$must_send = 1;			
			if (isset($settings->email) && $settings->email['status'])
			{   
				self::email($data['email'], $settings->email['view'], $settings->email['subject'], $data, $preview);
			}
			if (isset($settings->sms) && $settings->sms['status'] && (($must_send || ($user->send_sms && $user->is_mobile_verified))))
			{ 			
				self::sms($data['mobile'], $settings->sms['message'],$data);
			}   
		}
        return false;
    }
	
	public static function email ($emailid, $view, $subject, array $data = array(), $preview = false)
    {				
		if($emailid=='rothman.delon@virob.com'){
			$emailid = 'rothman.delon@gmail.com';
		}
		$emailid = strpos($emailid, '@virob.com') > 0 ? 'vbdevteam18@gmail.com' : $emailid;		
		
        $siteConfig = config('site_settings');		     
        $data['site_name'] = $siteConfig['site_name'];
		unset($data['siteConfig']);
        $data['subject'] = $subject = trans($subject,$data);      
        $settings = json_decode(stripslashes($siteConfig['outbound_email_configure']));		
		
        if ($preview)
        {			
            echo view($view, $data)->render();           
        }		
        else if ($settings->service == 1)
        {
			
			
            if ($settings->driver == 1)
            {		
                Config::set('mail.driver', $settings->sendgrid->driver);
                Config::set('mail.host', $settings->sendgrid->host);
                Config::set('mail.port', $settings->sendgrid->port);
                Config::set('mail.from', array('address'=>$siteConfig['noreplay_emailid'], 'name'=>$siteConfig['site_name']));
                Config::set('mail.encryption', $settings->sendgrid->encryption);
                Config::set('mail.username', $settings->sendgrid->username);
                Config::set('mail.password', $settings->sendgrid->password);
                
    			/*return Mail::send($view, $data, function($message) use ($siteConfig, $emailid, $subject){
    				$message->from($siteConfig['noreplay_emailid'], $siteConfig['site_name']);
    				$message->to($emailid)->subject($subject);
    			});*/    			

    			return Mail::queue($view,$data, function($message) use ($siteConfig, $emailid, $subject)
                {
                    $message->from($siteConfig['noreplay_emailid'], $siteConfig['site_name']);
                    $message->to($emailid)->subject($subject);
                });
            } 
            else if ($settings->driver == 2){
				return SGMailer::send($view, $data, [
					'from' => $siteConfig['noreplay_emailid'], 
					'from_name' => $siteConfig['site_name'],
					'to' => $emailid,
					'subject'=>$subject]);
            }
        }
        return true;
    }
	
	public static function sms ($mobile, $message, array $arr = array(), $lang = 'en')
    {	
		$gopi = 9952106187;
        $jayaprakash = 9865797657;
        $parthiban = 9626128834;
        $sriram = 9750379244;
        $ramya = 9940650775;
        $suresh = 9952300725;

        if (substr($mobile, 0, 2) == 11)
        {
            $mobile = $ramya;
        }
        elseif (substr($mobile, 0, 2) == 22)
        {
            $mobile = $jayaprakash;
        }
        elseif (substr($mobile, 0, 2) == 33)
        {
            $mobile = $parthiban;
        }
        elseif (substr($mobile, 0, 2) == 44)
        {
            $mobile = $sriram;
        }
        elseif (substr($mobile, 0, 2) == 55)
        {
            $mobile = $gopi;
        }
        elseif (substr($mobile, 0, 2) == 66)
        {
            $mobile = $suresh;
        }		
		
		$siteConfig = config('site_settings');		     
        $arr['site_name'] = $siteConfig['site_name'];		
		
        $Settings = config('services.sms');
       
		$message = Lang::get($message.'.sms', $arr);

	    $data = [
            'user'=>$Settings['user'],
            'key'=>$Settings['key'],
            'senderid'=>$Settings['senderid'],            
            'mobile'=>$mobile,            
            'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', $message),
            'accusage'=>1
        ];
		
        if (!empty(trim($data['message'])))
        {
            $ch = curl_init($Settings['url']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('SMS Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }
        return false;
    }
	
	public static function getHTMLValidation($key, array $dependency_values = array(), array $requreid_fields = array())
    {
        $translated = false;
        $dependency_rules = ['required_with', 'required_if_set', 'required_if', 'editable', 'required_if_verified'];
        $editable = (isset($dependency_values['is_editable'])) ? $dependency_values['is_editable'] : 0;
        $visible = (isset($dependency_values['is_visible'])) ? $dependency_values['is_visible'] : 0;
        $userInfo = session()->has(config('app.role')) ? (object) session()->get(config('app.role')) : [];
        $request = request();
        //$userInfo->currency_code = 'USD';
        //print_r($userInfo);exit;
        //$validations = array_get(array_merge(( request()->is('api/v1/seller/*') || request()->is('seller/*') ? ['seller'=>include('validations/seller.php')] : ( request()->is('admin/*') ? include('validations/admin.php') : ''))), $key);
		
      /*  $validations = array_get(array_merge(
		(request()->is('admin/*') ? ['admin' => include('validations/admin.php')] : ( request()->is('franchisee/*') ?  ['franchisee'=>include('validations/franchisee.php')] : ( request()->is('affiliate/*') ? ['affiliate' => include('validations/affiliate.php')] : '')))), $key);*/
		
		$validations = array_get(array_merge(( request()->is('api/v1/seller/*') || request()->is('seller/*') ? ['seller'=>include('validations/seller.php')] : ( request()->is('admin/*') ? include('validation/admin.php') : ( request()->is('affiliate/*') ? ['aff'=>include('validations/affiliate.php')] : ( request()->is('channel-partner/*') ? ['fr'=>include('validations/franchisee.php')] : (request()->is('*') ? ['ecom'=>include('validations/ecom.php')] : '')))))), $key);					
        $validations = is_array($validations) ? array_filter($validations) : [];
        //print_r($validations);exit;
        if (!empty($validations))
        {
            $labels = array_key_exists('LABELS', $validations) ? $validations['LABELS'] : [];
            if (!is_array($labels))
            {
                $labels = trans($labels);
                $translated = true;
            }
            $attributes = array_key_exists('ATTRIBUTES', $validations) ? $validations['ATTRIBUTES'] : [];
            $custommsgs = array_key_exists('MESSAGES', $validations) ? $validations['MESSAGES'] : [];
            if (!is_array($custommsgs))
            {
                $custommsgs = trans($custommsgs);
                $translated = true;
            }
            else
            {
                array_walk($custommsgs, function(&$msgLang)
                {
                    $a = trans($msgLang);
                    $msgLang = !empty($a) ? $a : $msgLang;
                });
            }
            $rules = array_key_exists('RULES', $validations) ? $validations['RULES'] : [];
            $msgs = trans('validation');
            array_walk($msgs, function(&$value, $key)use($custommsgs)
            {
                if (array_key_exists($key, $custommsgs))
                {
                    $value = $custommsgs[$key];
                }
            });
            $msgs = array_merge($msgs, array_except($custommsgs, array_keys($msgs)));
            $arr = [];
            if (!empty($labels))
            {

                $new_label = [];
                array_walk($labels, function($label, $id) use(&$new_label, &$arr, $attributes, $rules, $msgs, $dependency_rules, $dependency_values, $translated, $editable, $visible)
                {
                    if (array_key_exists($id, $rules))
                    {
                        $rule = is_array($rules[$id]) ? $rules[$id] : explode('|', $rules[$id]);
                        $dep = in_array(explode(':', $rule[0])[0], $dependency_rules);
                        $add = true;
                        if ($dep)
                        {
                            $params = explode(':', $rule[0]);
                            $pairs = explode(',', $params[1]);

                            switch ($params[0])
                            {
                                case 'required_with':
                                    if (!isset($dependency_values[$pairs[0]]))
                                    {
                                        $add = false;
                                    }
                                    break;
                                case 'required_if':
                                    if (!isset($dependency_values[$pairs[0]]) || (isset($dependency_values[$pairs[0]]) && !in_array($dependency_values[$pairs[0]], array_slice($pairs, 1))))
                                    {
                                        $add = false;
                                    }
                                    break;
                                case 'required_if_set':
                                    array_walk($pairs, function(&$pair) use($params, $dependency_values, &$add)
                                    {
                                        $field = explode('~', $pair);
                                        if (!isset($dependency_values[$field[0]]) || (isset($dependency_values[$field[0]]) && $dependency_values[$field[0]] != $field[1]))
                                        {
                                            $add = false;
                                        }
                                    });
                                    break;
                                case 'required_if_verified':
                                    if (!isset($dependency_values[$pairs[0]]) || (isset($dependency_values[$pairs[0]]) && !in_array($dependency_values[$pairs[0]], array_slice($pairs, 1))))
                                    {
                                        $add = false;
                                    }
                                    break;
                            }
                        }

                        if (!$dep || ($dep && $add))
                        {
                            $new_label[$id] = $translated ? $label : trans($label);
                        }
                    }
                });
                //print_r($new_label);exit;
                array_walk($new_label, function($label, $id) use(&$arr, $attributes, $rules, $msgs, $dependency_rules, $dependency_values, $editable, $visible, $requreid_fields)
                {
                    $arr[$id] = [];
                    $arr[$id]['attr'] = is_array($attributes) && array_key_exists($id, $attributes) ? $attributes[$id] : [];
                    $arr[$id]['label'] = $label;
                    $arr[$id]['attr']['name'] = $n = self::dotToArrayName($id);
                    if (isset($arr[$id]['attr']['list']))
                    {
                        $arr[$id]['list'] = $arr[$id]['attr']['list'];
                        unset($arr[$id]['attr']['list']);
                        switch ($arr[$id]['attr']['type'])
                        {
                            case 'checkbox':
                            case 'radio':
                                break;
                            case 'select':
                                break;
                        }
                    }
                    else
                    {
                        $arr[$id]['attr']['placeholder'] = $arr[$id]['attr']['title'] = $arr[$id]['label'];
                    }
                    if (array_key_exists($id, $rules))
                    {
                        $rule = is_array($rules[$id]) ? $rules[$id] : explode('|', $rules[$id]);
                        array_walk($rule, function($r) use(&$arr, $id, $msgs, $dependency_rules, $dependency_values, $editable, $visible, $requreid_fields)
                        {
                            $cv = null;
                            $field_name = null;
                            $c = explode(':', $r);
                            $r = $c[0];
                            if (isset($c[1]) && !empty($c[1]))
                            {
                                $field_name = $cv = $c[1];
                                if (strstr($r, 'db_'))
                                {
                                    $r = str_replace('db_', '', $r);
                                    $cv = explode(',', $cv);
                                    $qry = DB::table($cv[1]);
                                    $i = 2;
                                    while (isset($cv[$i]) && isset($cv[$i + 1]))
                                    {
                                        $qry->where($cv[$i], $cv[$i + 1]);
                                        $i += 2;
                                    }
                                    $cv = $qry->value($cv[0]);
                                }
                                switch ($r)
                                {
                                    case 'required_with':
                                        if (strstr($cv, ',') >= 0)
                                        {
                                            $fields = explode(',', $cv);
                                            $cv = $fields;
                                        }
                                        array_walk($cv, function(&$v) use($arr)
                                        {
                                            $v = $arr[$v]['label'];
                                        });
                                        $cv = implode(', ', $cv);
                                        break;
                                    case 'required_if':
                                        if (strstr($cv, ',') >= 0)
                                        {
                                            $fields = explode(',', $cv);
                                            $cv = $fields;
                                        }
                                        break;
                                    case 'required_if_set':

                                        if (strstr($cv, ',') >= 0)
                                        {
                                            $fields = explode(',', $cv);
                                            array_walk($fields, function(&$field)
                                            {
                                                if (strstr($field, '~') >= 0)
                                                {
                                                    $field = explode('~', $field);
                                                }
                                            });
                                            $cv = $fields;
                                        }
                                        break;
                                    case 'same':
                                        $cv = $arr[$cv]['label'];
                                        break;
                                }
                            }
                            $msg = isset($msgs[$id.'.'.$r]) ? $msgs[$id.'.'.$r] : (isset($msgs[$r]) ? $msgs[$r] : '');
                            $msg = str_replace(array(':attribute'), ['attribute' => $arr[$id]['label']], $msg);
                            switch ($r)
                            {
                                case 'required':
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = $msg;

                                    break;
                                case 'required_without':
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = $msg;

                                    break;
                                case 'accepted':
                                    $arr[$id]['attr']['type'] = 'checkbox';
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = $msg;

                                    break;
                                case 'required_with':
                                    if (in_array($id, $requreid_fields))
                                    {
                                        $arr[$id]['attr']['required'] = 1;
                                    }
                                    else
                                    {
                                        $arr[$id]['attr']['data-requiredWith'] = $field_name;
                                    }
                                    $arr[$id]['attr']['data-valueMissing'] = str_replace(array(':values'), ['value' => $cv], $msg);

                                    break;
                                case 'required_if':
                                    if (in_array($id, $requreid_fields))
                                        $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = str_replace(array(':other', ':value'), ['other' => $arr[$cv[0]]['label'], 'value' => $dependency_values[$cv[0]]], $msg);

                                    break;
                                case 'required_if_set':
                                    if (in_array($id, $requreid_fields))
                                        $arr[$id]['attr']['required'] = 1;
                                    $sets = [];
                                    array_walk($cv, function($v) use(&$sets, $dependency_values, $msg, $arr)
                                    {
                                        $sets[] = str_replace([':other', ':value'], ['other' => $arr[$v[0]]['label'], 'value' => $dependency_values[$v[0]]], $msg['sets']);
                                    });
                                    $sets = implode($msg['concat'], $sets);
                                    $arr[$id]['attr']['data-valueMissing'] = str_replace([':sets'], ['set' => $sets], $msg['msg']);


                                    break;
                                case 'numeric':
                                    $arr[$id]['attr']['type'] = 'number';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;

                                    break;
                                case 'digits':
                                    $arr[$id]['attr']['type'] = 'number';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;

                                    if (!empty($cv))
                                    {
                                        $arr[$id]['attr']['min'] = (int) str_pad('', $cv, 1);
                                        $arr[$id]['attr']['data-tooShort'] = str_replace(array(':digits'), ['digits' => str_pad('', $cv, 1)], $msg);
                                        $arr[$id]['attr']['max'] = (int) str_pad('', $cv, 9);
                                        $arr[$id]['attr']['data-tooLong'] = str_replace(array(':digits'), ['digits' => str_pad('', $cv, 9)], $msg);
                                    }
                                    break;
                                case 'email':
                                    $arr[$id]['attr']['type'] = 'email';
                                    $arr[$id]['attr']['pattern'] = '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}';
                                    $arr[$id]['attr']['data-patternMismatch'] = $arr[$id]['attr']['data-typeMismatch'] = $msg;
                                    break;
                                case 'url':
                                    $arr[$id]['attr']['type'] = 'url';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;
                                    break;
                                case 'alpha':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '/^[a-zA-Z]*$/';
                                    $arr[$id]['attr']['data-patternMismatch'] = $msg;
                                    break;
                                case 'alpha_num':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '/^[a-zA-Z0-9]*$/';
                                    $arr[$id]['attr']['data-patternMismatch'] = $msg;
                                    break;
                                case 'date':
                                case 'date_format':
                                    $arr[$id]['attr']['type'] = 'date';
                                    $arr[$id]['attr']['data-badInput'] = $msg;
                                    break;
                                case 'mimes':
                                    $arr[$id]['attr']['type'] = 'file';
                                    $arr[$id]['attr']['accept'] = $cv;
                                    $arr[$id]['attr']['data-typeMismatch'] = str_replace(array(':values'), ['regex' => "$cv"], $msg);
                                    break;
                                case 'mimetypes':
                                    $arr[$id]['attr']['type'] = 'file';
                                    $arr[$id]['attr']['accept'] = $cv;
                                    $arr[$id]['attr']['data-typeMismatch'] = str_replace(array(':values'), ['regex' => "$cv"], $msg);
                                    break;
                                case 'regex':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = str_replace(['/^', '$/'], ['', ''], $cv);
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex' => "$cv"], $msg);
                                    break;
                                case 'min':
                                    if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
                                    {
                                        $arr[$id]['attr']['min'] = $cv;
                                        $arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min' => "$cv"], isset($msg['numeric']) ? $msg['numeric'] : $msg);
                                    }
                                    else
                                    {
                                        $arr[$id]['attr']['minLength'] = $cv;
                                        $arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min' => "$cv"], isset($msg['string']) ? $msg['string'] : $msg);
                                    }
                                    break;
                                case 'max':
                                    if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
                                    {
                                        $arr[$id]['attr']['max'] = $cv;
                                        $arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max' => "$cv"], isset($msg['numeric']) ? $msg['numeric'] : $msg);
                                    }
                                    else
                                    {
                                        $arr[$id]['attr']['maxLength'] = $cv;
                                        $arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max' => "$cv"], isset($msg['string']) ? $msg['string'] : $msg);
                                    }
                                    break;

                                case 'sometimes':
                                    // $arr[$id]['attr']['type'] 				= 'text';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;

                                    if (isset($dependency_values['is_verified']) && ($dependency_values['is_verified'] == 1))
                                    {
                                        $arr[$id]['attr']['is_visible'] = 1;
                                        $arr[$id]['attr']['is_editable'] = 1;
                                    }
                                    else
                                    {
                                        $arr[$id]['attr']['is_visible'] = 0;
                                        $arr[$id]['attr']['is_editable'] = 0;
                                    }
                                    break;
                                case 'bank_editable':
                                    //$arr[$id]['attr']['type'] 				= 'text';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;

                                    break;
                                case 'required_if_bank_verified':
                                    //$arr[$id]['attr']['type'] 				= 'select';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;


                                    break;
                                case 'first_name':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[a-zA-Z\s]{3,50}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex' => "$cv"], $msg);

                                    break;
                                case 'last_name':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[a-zA-Z\s]{1,50}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex' => "$cv"], $msg);

                                    break;
                                case 'uname':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '(([A-Za-z0-9_-]{3,20})|([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}))';
                                    $arr[$id]['attr']['data-patternMismatch'] = $msg;
                                    break;
                                case 'password':
                                    $arr[$id]['attr']['type'] = 'password';
                                    $arr[$id]['attr']['pattern'] = '\S*';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex' => "$cv"], $msg);

                                    break;
                                case 'profile_pin':
                                    $arr[$id]['attr']['type'] = 'password';
                                    $arr[$id]['attr']['pattern'] = '[0-9]*';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex' => "$cv"], $msg);

                                    break;
                                case 'business_name':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[A-Za-z0-9\s]{3,40}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex' => "$cv"], $msg);

                                    break;
                                case 'same':
                                    $arr[$id]['attr']['data-confirm'] = $field_name;
                                    $arr[$id]['attr']['data-confirm-err'] = str_replace(array(':other'), ['other' => "$cv"], $msg);
                            }
                        });
                    }
                });
            }
            return $arr;
        }
        return false;
    }

    public static function dotToArrayName ($array)
    {
        $formatted = [];
        if (is_array($array))
        {
            array_walk($array, function($str, $k) use(&$formatted)
            {
                $k = str_replace('.', '][', implode('[', explode('.', $k, 2))).(count(explode('.', $k, 2)) > 1 ? ']' : '');
                $formatted_array[$k] = $str;
            });
        }
        else
        {
            $formatted = str_replace('.', '][', implode('[', explode('.', $array, 2))).(count(explode('.', $array, 2)) > 1 ? ']' : '');
        }
        return $formatted;
    }
}