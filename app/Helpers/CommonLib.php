<?php

namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;

class CommonLib
{

    // function to get gravatar photo/image from gravatar.com using email id.
    public function getGravatarURL ($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img)
        {
            $url = '<img src="'.$url.'"';
            foreach ($atts as $key=> $val)
                $url .= ' '.$key.'="'.$val.'"';
            $url .= ' />';
        }
        return $url;
    }    

    public static function currency_format ($amount, $currency = '', $concat = true, $with_code = false)
    {	//print_r($currency);exit;
        $currency_id = 0;
        $currency_code = '';
        if (is_object($currency))
        {
            $currency = !empty(array_filter((array) $currency)) ? $currency : 1;
        }
        elseif (is_array($currency))
        {
            $currency = !empty(array_filter($currency)) ? (object) $currency : 1;
            if (isset($currency->currency_code))
            {
                $currency_code = $currency->currency_code;
            }
        }
        else
        {
            //$currency = 1; / USD /
            //$currency = 2; / INR /
        }

        if ((is_integer($currency) && $currency > 0) || (is_string($currency_code) && !empty($currency_code)))
        {
            $currency_id = is_integer($currency) ? $currency : 0;
			//session()->forget('currency_list');
            if (!session()->has('currency_listt') || (session()->has('currency_listt') && $currency_id > 0))
            {
                $currencies = session()->has('currency_listt') ? session()->get('currency_listt') : [];
				if (empty($currencies) || (!empty($currencies) && !isset($currencies[$currency_id])))
                {					
					//return $currency_id;
                    $qry = DB::table(Config::get('tables.CURRENCIES'))
                            ->select('currency as currency_code', 'currency_symbol', 'decimal_places');
                    if ($currency_id > 0)
                    {
                        $qry->where('currency_id', $currency_id);
                    }
                    else if (!empty($currency_code))
                    {
                        $qry->where('currency', $currency_code);
                    }					
                    $currency = $qry->first();
                    session()->put('currency_listt', [$currency_id=>$currency]);
                }
            }
            if (session()->has('currency_listt'))
            {
                $currencies = session()->get('currency_listt');
                if (isset($currencies[$currency_id]))
                {
                    $currency = $currencies[$currency_id];
                }
            }
        }		
        if (!empty($currency))
        {
            $currency->amount = number_format($amount, $currency->decimal_places, '.', ',');
            //unset($currency->decimal_places);
            return $concat ? $currency->currency_symbol.' '.(isset($currency->value_type) && !empty($currency->value_type) ? $currency->value_type : '').$currency->amount.($with_code ? ' '.$currency->currency_code : '') : $currency;
        }
        else
        {
            return false;
        }
    }

    public static function getHTMLValidation ($key, array $dependency_values = array())
    {
        $translated = false;
        $dependency_rules = ['required_with', 'required_if_set', 'required_if', 'editable', 'required_if_verified'];
        $editable = (isset($dependency_values['is_editable'])) ? $dependency_values['is_editable'] : 0;
        $visible = (isset($dependency_values['is_visible'])) ? $dependency_values['is_visible'] : 0;
        $session = session();
        $userInfo = (object) session(config('app.role'));
        $validations = array_get(array_merge((request()->is('admin/*') ? ['admin'=>include('validations/admin.php')] : ( request()->is('api/v1/dsa/*') || request()->is('dsa/*') ? ['dsa'=>include('validations/dsa.php')] : ( request()->is('api/v1/retailer/*') || request()->is('retailer/*') ? ['retailer'=>include('validations/retailer.php')] : ( request()->is('*') || request()->is('api/v1/user/*') ? ['user'=>include('validations/user.php')] : []))))), $key);
        $validations = is_array($validations) ? array_filter($validations) : [];
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
            //print_R($attributes);exit;
            if (!is_array($custommsgs))
            {
                $custommsgs = trans($custommsgs);
                $translated = true;
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
                        $rule = explode('|', $rules[$id]);
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
                array_walk($new_label, function($label, $id) use(&$arr, $attributes, $rules, $msgs, $dependency_rules, $dependency_values, $editable, $visible)
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
                        $rule = explode('|', $rules[$id]);
                        array_walk($rule, function($r) use(&$arr, $id, $msgs, $dependency_rules, $dependency_values, $editable, $visible)
                        {
                            $cv = null;
                            $c = explode(':', $r);
                            $r = $c[0];
                            if (isset($c[1]) && !empty($c[1]))
                            {
                                $cv = $c[1];
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
                                }
                            }
                            $msg = isset($msgs[$id.'.'.$r]) ? $msgs[$id.'.'.$r] : (isset($msgs[$r]) ? $msgs[$r] : '');
                            $msg = str_replace(array(':attribute'), ['attribute'=>$arr[$id]['label']], $msg);
                            switch ($r)
                            {
                                case 'required':
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = $msg;
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'accepted':
                                    $arr[$id]['attr']['type'] = 'checkbox';
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = $msg;
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'required_with':
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = str_replace(array(':values'), ['value'=>$cv], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'required_if':
                                    $arr[$id]['attr']['required'] = 1;
                                    $arr[$id]['attr']['data-valueMissing'] = str_replace(array(':other', ':value'), ['other'=>$arr[$cv[0]]['label'], 'value'=>$dependency_values[$cv[0]]], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'required_if_set':
                                    $arr[$id]['attr']['required'] = 1;
                                    $sets = [];
                                    array_walk($cv, function($v) use(&$sets, $dependency_values, $msg, $arr)
                                    {
                                        $sets[] = str_replace([':other', ':value'], ['other'=>$arr[$v[0]]['label'], 'value'=>$dependency_values[$v[0]]], $msg['sets']);
                                    });
                                    $sets = implode($msg['concat'], $sets);
                                    $arr[$id]['attr']['data-valueMissing'] = str_replace([':sets'], ['set'=>$sets], $msg['msg']);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;

                                    break;
                                case 'numeric':
                                    $arr[$id]['attr']['type'] = 'number';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;

                                    break;
                                case 'digits':
                                    $arr[$id]['attr']['type'] = 'number';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    if (!empty($cv))
                                    {
                                        $arr[$id]['attr']['min'] = (int) str_pad('', $cv, 1);
                                        $arr[$id]['attr']['data-tooShort'] = str_replace(array(':digits'), ['digits'=>str_pad('', $cv, 1)], $msg);
                                        $arr[$id]['attr']['max'] = (int) str_pad('', $cv, 9);
                                        $arr[$id]['attr']['data-tooLong'] = str_replace(array(':digits'), ['digits'=>str_pad('', $cv, 9)], $msg);
                                    }
                                    break;
                                case 'email':
                                    $arr[$id]['attr']['type'] = 'email';
                                    $arr[$id]['attr']['data-typeMismatch'] = $msg;
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
                                case 'min':
                                    if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
                                    {
                                        $arr[$id]['attr']['min'] = $cv;
                                        $arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min'=>"$cv"], $msg['numeric']);
                                    }
                                    else
                                    {
                                        $arr[$id]['attr']['minLength'] = $cv;
                                        $arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min'=>"$cv"], $msg['string']);
                                    }
                                    break;
                                case 'max':
                                    if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
                                    {
                                        $arr[$id]['attr']['max'] = $cv;
                                        $arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max'=>"$cv"], $msg['numeric']);
                                    }
                                    else
                                    {
                                        $arr[$id]['attr']['maxLength'] = $cv;
                                        $arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max'=>"$cv"], $msg['string']);
                                    }
                                    break;
                                case 'regex':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = str_replace(['/^', '$/'], ['', ''], $cv);
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
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
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    break;
                                case 'required_if_bank_verified':
                                    //$arr[$id]['attr']['type'] 				= 'select';
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
                                case 'first_name':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[a-zA-Z\s]{3,50}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'last_name':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[a-zA-Z\s]{1,50}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'password':
                                    $arr[$id]['attr']['type'] = 'password';
                                    $arr[$id]['attr']['pattern'] = '\S{6,16}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'security_pin':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[0-9]{4}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
                                    break;
                                case 'business_name':
                                    $arr[$id]['attr']['type'] = 'text';
                                    $arr[$id]['attr']['pattern'] = '[A-Za-z0-9\s]{3,40}';
                                    $arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
                                    $arr[$id]['attr']['is_editable'] = $editable;
                                    $arr[$id]['attr']['is_visible'] = $visible;
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

    public static function notify ($account_id, $id, array $data = array(), array $new = array(), $preview = false)
    { //echo '<pre>' ;
        $settings = config('notification.'.$id);
		
        if (!empty($settings))
        {			
            $data['route'] = isset($data['route']) && !empty($data['route']) ? $data['route'] : null;
            $data['icon'] = isset($data['icon']) && !empty($data['icon']) ? $data['icon'] : null;
            $settings = (object) $settings;
            if (!empty($account_id))
            {
                return $user = DB::table(config('tables.ACCOUNT_MST').' as am')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                        ->join(config('tables.ACCOUNT_PREFERENCE').' as s', 's.account_id', '=', 'am.account_id')
                        ->join(config('tables.LANGUAGE_LOOKUPS').' as l', 'l.lang_id', '=', 's.lang_id')
                        ->selectRaw('am.account_id,am.uname,am.mobile,am.email,CONCAT(ad.firstname,\' \',ad.lastname) as full_name,is_email_verified,is_mobile_verified,l.lang_key as lang')
                        ->where('am.account_id', $account_id)
                        ->first();
            }
            else
            {
                $user = (object) [
					'account_id'=>null,
					'uname'=>isset($new['uname']) && !empty($new['uname']) ? $new['uname'] : null,
					'mobile'=>isset($new['mobile']) && !empty($new['mobile']) ? $new['mobile'] : null,
					'email'=>isset($new['email']) && !empty($new['email']) ? $new['email'] : null,
					'full_name'=>isset($new['full_name']) && !empty($new['full_name']) ? $new['full_name'] : null,
					'is_email_verified'=>isset($new['is_email_verified']) && !empty($new['is_email_verified']) ? $new['is_email_verified'] : false,
					'is_mobile_verified'=>isset($new['is_mobile_verified']) && !empty($new['is_mobile_verified']) ? $new['is_mobile_verified'] : false,
					'push_notification'=>isset($new['push_notification']) && !empty($new['push_notification']) ? $new['push_notification'] : false,
				   // 'lang'=>isset($new['lang']) && !empty($new['lang']) ? $new['lang'] : 'en'
					];
            }
			
            if ($user)
            {
                $user->mobile = isset($data['mobile']) && !empty($data['mobile']) ? $data['mobile'] : (isset($new['mobile']) && !empty($new['mobile']) ? $new['mobile'] : $user->mobile);
                $user->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : (isset($new['email']) && !empty($new['email']) ? $new['email'] : $user->email); 
                $user->store_code = isset($data['store_code']) && !empty($data['store_code']) ? $data['store_code'] : (isset($new['store_code']) && !empty($new['store_code']) ? $new['store_code'] : ''); 
                $user->name = isset($data['name']) && !empty($data['name']) ? $data['name'] : (isset($new['name']) && !empty($new['name']) ? $new['name'] : ''); 
                $user->store = isset($data['store']) && !empty($data['store']) ? $data['store'] : (isset($new['store']) && !empty($new['store']) ? $new['store'] : ''); 
                $user->customer_id = isset($data['user_mobile']) && !empty($data['user_mobile']) ? $data['user_mobile'] : (isset($new['user_mobile']) && !empty($new['user_mobile']) ? $new['user_mobile'] : ''); 
                $user->bill_amount = isset($data['bill_amount']) && !empty($data['bill_amount']) ? $data['bill_amount'] : (isset($new['bill_amount']) && !empty($new['bill_amount']) ? $new['bill_amount'] : ''); 
				
				//$user->email = 'ejdevteam@gmail.com'; 					
				//$user->email = 'deepika.ejugiter@gmail.com'; 			
				
                $data = array_merge($data, (array) $user);
                $data = array_merge($data, (array) config('settings'));
                $data = array_filter($data, function($s)
                {
                    return is_array($s) || is_object($s) ? false : true;
                });			
     
                if ($settings->sms && (!$settings->check_verification || ($settings->check_verification && $user->is_mobile_verified)))
                {
					
                    self::sms($user->mobile, $id, $data);
                }				
                if ($settings->email && (!$settings->check_verification || ($settings->check_verification && $user->is_email_verified)))
                {
	
                    self::email($user->email, $id, $data, $preview);                    
                }
                if ($settings->notification && $user->push_notification)
                {
                    self::notification($user->account_id, $id, $data, $data['route'], $data['icon']);
                }
            }
        }
        else
        {
            Log::error('Notification settings missing for :'.$id);
            abort(500, 'Notification settings missing for :'.$id);
        }
        return false;
    }

    public static function email ($emailid, $id, array $data = array(), $preview = false)
    {
        $siteConfig = config('settings');
        $s = trans('notifications.'.$id.'.email', $data);
        $data['subject'] = $subject = trans('notifications.'.$id.'.email.subject', $data);
        $view = config('notification.'.$id.'.email_view');
        $data['content'] = trans('notifications.'.$id.'.email.content', $data);
        $settings = json_decode(stripslashes($siteConfig->outbound_email_configure));
		
        if ($preview)
        {			
		    view($view, $data)->render();
        }
	
        if ($settings->service == 1)
        {
            if ($settings->driver == 1)
            {
                Config::set('mail.driver', $settings->sendgrid->driver);
                Config::set('mail.host', $settings->sendgrid->host);
                Config::set('mail.port', $settings->sendgrid->port);
                Config::set('mail.from', array('address'=>$siteConfig->noreplay_emailid, 'name'=>$siteConfig->site_name));
                Config::set('mail.encryption', $settings->sendgrid->encryption);
                Config::set('mail.username', $settings->sendgrid->username);
                Config::set('mail.password', $settings->sendgrid->password);
            }
            return Mail::queue($view, $data, function($message) use ($siteConfig, $emailid, $subject){
					$message->from($siteConfig->noreplay_emailid, $siteConfig->site_name);
					$message->to($emailid)->subject($subject);
				});
        }
		
        return false;
    }

    public static function sms ($mobile, $key, array $arr = array(), $lang = 'en')
    {
	    $Settings = config('services.sms');
        $data = [
            'user'=>$Settings['user'],
            'key'=>$Settings['key'],
            'senderid'=>$Settings['senderid'],
            'mobile'=>$mobile,
            'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', trans('notifications.'.$key.'.sms', $arr)),
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

    public static function notification ($account_id, $id, array $data = array(), $route = '', $icon = '')
    {
        $siteConfig = config('settings');
		
        $account_id = is_array($account_id) ? $account_id : [$account_id];
        $icon = !empty($icon) ? $icon : $siteConfig->favicon;
        $route = !empty($route) ? $route : '';
        $data['lang_id'] = isset($data['lang_id']) ? $data['lang_id'] : config('app.locale_id');
        $title = trans('notifications.'.$id.'.notification.title', $data, $data['lang_id']);
        $msg = trans('notifications.'.$id.'.notification.msg', $data, $data['lang_id']);
        $fcm_registration_ids = DB::table(config('tables.ACCOUNT_LOG').' as dl')
                ->join(config('tables.ACCOUNT_SETTINGS').' as ap', function($ap)
                {
                    $ap->on('ap.account_id', '=', 'dl.account_id')
                    ->where('ap.push_notification', '=', config('constants.ACTIVE'));
                })
                ->whereIn('dl.account_id', $account_id)
                ->whereNotNull('dl.fcm_registration_id')
                ->lists('dl.fcm_registration_id');
        
		$registatoin_ids = array_values(array_unique(array_filter($fcm_registration_ids)));
        
		if (!empty($registatoin_ids))
        {
            $message_data = [];
            $message_data['data'] = [
                'notification'=>[
                    'title'=>$title,
                    'body'=>$msg,
                    'click_action'=>$route,
                    'icon'=>$icon,
                    'color'=>'#111111',
                    'sound'=>true,
                    'vibrate'=>true
                ]
            ];
            $notifications = [];
            $notifications['account_ids'] = implode(',', array_filter($account_id));
            $notifications['created_on'] = getGTZ();
            $notifications['json_data'] = json_encode($message_data['data']);
            $message_data['data']['notification']['id'] = DB::table(config('tables.ACCOUNT_NOTIFICATIONS'))
                    ->insertGetId($notifications);
            $message_data['registration_ids'] = $registatoin_ids;
            $Settings = config('services.google');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $Settings['fcm_url']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: key='.$Settings['api_key'], 'Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('Notification Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }
        return false;
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

        return ['minlat'=>$minlat, 'maxlat'=>$maxlat, 'minlng'=>$minlng, 'maxlng'=>$maxlng];
    }

    public static function amount_with_decimal2 ($amt)
    {
        $amt = floatval(trim($amt));
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
            if ($decimal_places > 8)
                $decimal_places = 8;
        }
        return number_format($amt, $decimal_places, '.', ',');
    }

    public function export ($filename, array $columns = array(), array $data = array())
    {
        $headers = array(
            'Pragma'=>'public',
            'Expires'=>'public',
            'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
            'Cache-Control'=>'private',
            'Content-Disposition'=>'attachment; filename='.$filename.'-'.date('dMYHis').'.xls',
            'Content-Transfer-Encoding'=>' binary'
        );
        return (object) ['body'=>\View::make('export-layout', ['data'=>$data, 'columns'=>$columns, 'title'=>$filename]), 'headers'=>$headers];
    }
	
	/*public static function validetIFSC($ifsc_code)
    {
        
        if (!empty(trim($ifsc_code)))
        {
			$settings = config('services.ifsc');  
			$message_data = [];
			$message_data['value'] = $ifsc_code;
			$message_data['searchBy'] = 'ifsc';			
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $settings['url']);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-RapidAPI-Key:'.$settings['key'], 'Content-Type: application/json']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                Log::error('Notification Sending failed:  '.curl_error($ch));
                die('SMS Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
			if(!empty($result)){
				return json_decode($result)[0];
			}
			return false;
        }
        return false;
    }*/
    
    public static function validetIFSC($ifsc_code)
    {
        
        if (!empty(trim($ifsc_code)))
        {
			
			$settings = config('services.ifsc');  
			/*$message_data = [];
			$message_data['searchBy'] = 'ifsc';			
			$message_data['value'] = $v;	*/		
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://ifsc.razorpay.com/'.$ifsc_code);
			//curl_setopt($ch, CURLOPT_POST, true);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-RapidAPI-Key:6af0d32a35mshabca09a19e5932cp18fcdcjsn8e5a9011ad4f']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
            $result = curl_exec($ch);	
            if ($result === FALSE)
            {
                Log::error('Notification Sending failed:  '.curl_error($ch));
                die('SMS Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
			if(!empty($result)){
				$result = json_decode($result,true);
				if(is_array($result)){
					$result = array_change_key_case($result, CASE_LOWER);					
					return (object)($result);
				}		
				else {				
					return false;
				}
				
			}
			return false;
        }
        return false;
    }
	

}