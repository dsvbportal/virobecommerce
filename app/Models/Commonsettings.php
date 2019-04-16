<?php
namespace App\Models;
use DB;
use CommonLib;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\ImageController;
use Config;

class Commonsettings extends Model
{
	  public function __construct ()
    {
       
        $this->imageObj = new ImageController();
    }
    static public function getSiteSettings ()
    {
        return DB::table(Config::get('tables.SITE_SETTINGS').' as ss')
                        ->join(Config::get('tables.LANGUAGE_LOOKUPS').' as ll', 'll.language_id', '=', 'ss.site_language_id')
                        ->selectRaw('ss.*,ll.iso_code as language_iso_code')
                        ->first();
    }

    public function getSettings($setting_key = 0,$format_as_array=false)
    {
		/* 0=> json object, 1=> array */
        $stVal = DB::table(Config::get('tables.SETTINGS'))
                        ->where('setting_key', $setting_key)						
                        ->value('setting_value');
		if($stVal){
			return (strpos($stVal,'[')>-1 || strpos($stVal,'{')>-1)? json_decode(stripslashes($stVal),$format_as_array): $stVal;
		}
		return false;
    }
	
	public function get_userdetails_byid ($account_id)
    {
        return DB::table(config('tables.ACCOUNT_MST').' as um')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                        ->join(config('tables.ACCOUNT_PREFERENCE').' as us', 'us.account_id', '=', 'um.account_id')
                        ->leftjoin(config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'us.country_id')
                        ->leftjoin(config('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_name', '=', 'us.district')
                        ->leftjoin(config('tables.LOCATION_STATE').' as ls', 'ls.name', '=', 'us.state')
                        ->select(DB::raw('um.*, ud.*, ld.district_id, ls.region_id, ls.state_id, us.is_verified, lc.currency_id as country_default_currency_id'))
                        ->where('um.account_id', $account_id)
                        ->first();
    }

    public static function defaultCurrency ()
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('default_currency', Config::get('constants.ON'))
                        ->select('currency_id', 'currency', 'currency_symbol', 'flag_char', 'default_currency')
                        ->first();
    }

    public function payment_mode_list ()
    {
        return DB::table(Config::get('tables.PAYMENT_MODES_LOOKUPS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('status', Config::get('constants.ON'))
                        ->get();
    }

    public function get_available_payment_gateway ($data)
    {
        return DB::table(Config::get('tables.PAYMENT_TYPES'))
                        ->whereRaw('payment_modes  REGEXP \'[[:<:]]'.$data['mode_id'].'[[:>:]]\'')
                        ->where('status', Config::get('constants.OFF'))
                        ->select('payment_type_id', 'payment_type')
                        ->get();
    }

    public function get_setting_value ($key)
    {
        return DB::table(Config::get('tables.SETTINGS'))
                        ->where('setting_key', $key)
                        ->pluck('setting_value');
    }

    public function getUserAddress ($account_id, $acc_type_id)
    {
        return DB::table(Config::get('tables.ADDRESS_MST').' as am')
                        ->join(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id')
                        ->join(Config::get('tables.LOCATION_CITY').' as lct', 'lct.city_id', '=', 'am.city_id')
                        ->join(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                        ->selectRaw('am.flatno_street, am.address, am.city_id, lct.city, am.state_id, ls.state, am.country_id, lc.country as country, am.postal_code')
                        ->where('am.is_deleted', Config::get('constants.OFF'))
                        ->where('am.relative_post_id', $account_id)
                        ->where('am.post_type', $acc_type_id)
                        ->get();
    }

    public function decimal_places ($amt)
    {
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
        }
        return $decimal_places;
    }

    public function get_exchange_rate ($from_currency_id, $to_currency_id)
    {
        $rate = ($from_currency_id != $to_currency_id) ? DB::table(Config::get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                        ->where('from_currency_id', $from_currency_id)
                        ->where('to_currency_id', $to_currency_id)
                        ->pluck('rate') : 1;
        return (!empty($rate) ) ? $rate : 1;
    }

    public function getCountryList ($whereIncountry_ids = [])
    {
        $query = DB::table(Config::get('tables.LOCATION_COUNTRY'))
				->where('status', 1)
                ->orderBy('country', 'asc');
        if (!empty($whereIncountry_ids))
        {
            $query->whereIn('country_id', $whereIncountry_ids);
        }
        return $query->lists('country', 'country_id');
    }

    public function getCurrencies ($whereInCurrencies = [])
    {
        $query = DB::table(Config::get('tables.CURRENCIES'))
                ->orderBy('currency', 'asc');
        if (!empty($whereInCurrencies))
        {
            $query->whereIn('currency_id', $whereInCurrencies);
        }
        return $query->lists('currency', 'currency_id');
    }

    public function getCountryName ($country_id)
    {
        return DB::table(Config::get('tables.LOCATION_COUNTRY'))
                        ->where('country_id', $country_id)
                        ->value('country');
    }

    public function getStateName ($state_id)
    {
        return DB::table(Config::get('tables.LOCATION_STATE'))
                        ->where('state_id', $state_id)
                        ->value('state');
    }

    public function getCityName ($city_id)
    {
        return DB::table(Config::get('tables.LOCATION_CITY'))
                        ->where('city_id', $city_id)
                        ->value('city');
    }

    public function get_wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET'))
                        ->where('withdrawal_status', Config::get('constants.ACTIVE'))
                        ->select('wallet_id', 'wallet_name')
                        ->orderby('wallet_name', 'ASC')
                        ->first();
    }

    public function get_wallet_name ($wallet_id)
    {
        return DB::table(Config::get('tables.WALLET'))
                        ->where('wallet_id', $wallet_id)
                        ->pluck('wallet_name');
    }

    public function get_currency_name ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency');
    }

    public function get_currency ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->select('currency', 'currency_symbol','decimal_places','currency as currency_code')
                        ->first();
    }

    public function get_user_balance ($account_id, $wallet_id, $currency_id)
    {
        fetch:
        $result = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                ->where(array(
                    'account_id'=>$account_id,
                    'wallet_id'=>$wallet_id,
                    'currency_id'=>$currency_id))
                ->first();

        if (empty($result))
        {
            $curresult = DB::table(Config::get('tables.CURRENCIES'))
                    ->where('currency_id', $currency_id)
                    ->where('status', Config::get('constants.ON'))
                    ->count();
            $ewalresult = DB::table(Config::get('tables.WALLET'))
                    ->where(array(
                        'wallet_id'=>$wallet_id,
                        'status'=>Config::get('constants.ON')))
                    ->count();

            if ($curresult && $ewalresult)
            {
                $insert['account_id'] = $account_id;
                $insert['current_balance'] = '0';
                $insert['tot_credit'] = '0 ';
                $insert['tot_debit'] = '0';
                $insert['currency_id'] = $currency_id;
                $insert['wallet_id'] = $wallet_id;
                $status = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->insertGetId($insert);

                goto fetch;
            }
        }

        return $result;
    }

    public function get_country_list ($arr = array(), $forselect2 = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.LOCATION_COUNTRY').' as lc')
                ->where('lc.status', Config::get('constants.ACTIVE'));
        if (isset($state_id) && !empty($state_id))
        {
            $query->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.country_id', '=', 'lc.country_id')->where('ls.state_id', $state_id);
        }
        if (isset($country) && !empty($country))
        {
            $query->where('lc.country', 'like', $country.'%');
        }
        if ($forselect2)
        {
            $query->select('lc.country_id as id', 'lc.country as text');
        }
        else
        {
            $query->select('lc.country_id', 'lc.country as text', 'lc.phonecode');
        }
        return $query->orderby('country', 'ASC')
                        ->distinct('lc.country_id')->get();
    }

    public function get_state_list ($arr = array(), $forselect2 = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.LOCATION_STATE').' as ls')
                ->where('ls.status', Config::get('constants.ACTIVE'));
        if (!empty($country_id))
        {
            $query->where('ls.country_id', $country_id);
        }
        if (isset($state) && !empty($state))
        {
            $query->where('ls.state', 'like', $state.'%');
        }
        if (!empty($city_id))
        {
            $query->join(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.state_id', '=', 'ls.state_id')
                    ->join(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.district_id', '=', 'ld.district_id')
                    ->join(Config::get('tables.LOCATION_CITY').' as lct', 'lct.pincode_id', '=', 'lp.pincode_id')
                    ->where('lct.city_id', $city_id);
        }
        if ($forselect2)
        {
            $query->select('ls.state_id as id', 'ls.state as text');
        }
        else
        {
            $query->select('ls.state_id', 'ls.state');
        }
        return $query->orderby('ls.state', 'ASC')
                        ->distinct('ls.state_id')->get();
    }

    public function get_city_list ($arr = array(), $forselect2 = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.LOCATION_CITY').' as lc')
                ->where('lc.status', Config::get('constants.ACTIVE'));
        if (!empty($state_id))
        {
            $query->join(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode_id', '=', 'lc.pincode_id')
                    ->join(Config::get('tables.LOCATION_DISTRICTS').' as ld', function($ld) use($state_id)
                    {
                        $ld->on('ld.district_id', '=', 'lp.district_id')
                        ->where('ld.state_id', '=', $state_id);
                    });
        }
        if (isset($city) && !empty($city))
        {
            $query->where('lc.city', 'like', $city.'%');
        }
        if (!empty($district_id))
        {
            $query->where('lc.district_id', '=', $district_id);
        }
        if ($forselect2)
        {
            $query->select('lc.city_id as id', 'lc.city as text');
        }
        else
        {
            $query->select('lc.city_id', 'lc.city');
        }
        return $query->orderby('lc.city', 'ASC')->distinct('lc.city_id')->get();
    }

    public function language_list ()
    {
        return DB::table(Config::get('constants.LANGUAGE_LOOKUPS'))
                        ->get();
    }

    public function locale_list ()
    {
        return DB::table(Config::get('constants.LOCALE_LOOKUPS'))
                        ->get();
    }

    public function time_zone_list ()
    {
        return DB::table(Config::get('constants.TIME_ZONE_LOOKUPS'))
                        ->get();
    }

    public function get_currencies_list ($arr = array())
    {
        $curr = DB::table(Config::get('tables.CURRENCIES'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->orderby('currency', 'ASC')
                ->select('currency', 'currency_id', 'currency_symbol', 'decimal_places', 'default_currency', 'flag_char');
        if (isset($arr['allowed_curr']))
        {
            $curr->whereIn('currency_id', $arr['allowed_curr']);
        }
        return $curr->get();
    }

    public function get_currency_code ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->value('currency');
    }

    public function get_currency_symbol ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency_symbol');
    }    

    public function tax_status_list ()
    {
        return DB::table(Config::get('tables.TAX_STATUS_LOOKUPS'))
                        ->select('status_id', 'status')
                        ->get();
    }

    public function zone_list ()
    {
        return DB::table(Config::get('tables.GEO_ZONE'))
                        ->select('geo_zone_id', 'zone')
                        ->get();
    }

    

    public function get_address_type_list ()
    {
        return DB::table(Config::get('tables.ADDRESS_TYPE_LOOKUP'))
                        ->select('address_type_id', 'address_type')
                        ->get();
    }

    public function get_tax_list ()
    {
        return DB::table(Config::get('tables.TAXES'))
                        ->select('tax_id', 'tax')
                        ->get();
    }

    
    public function wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET'))
                        ->where('creditable', 1)
                        ->get();
    }

    public function payment_list ()
    {
        return DB::table(Config::get('tables.PAYMENT_TYPES'))
                        ->where('status', Config::get('constants.ON'))
                        ->get();
    }

    public function withdrawalPaymentList ()
    {
        return DB::table(Config::get('tables.PAYMENT_TYPES').' as p')
                        ->join(Config::get('tables.WITHDRAWAL_PAYMENT_TYPE').' as pw', 'pw.payment_type_id', '=', 'p.payment_type_id')
                        ->where('p.status', Config::get('constants.ON'))
                        ->selectRaw('p.payment_type_id,p.payment_type')
                        ->get();
    }

   

    public function tax_classes_list ()
    {
        return DB::table(Config::get('tables.TAX_CLASSES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->select('tax_class_id', 'tax_class')
                        ->get();
    }

    public function faq_category ()
    {
        return DB::table(Config::get('tables.FAQ_CATEGORIES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->get();
    }

   
    /*
     * Function Name        : update_account_transaction
     * Params               : (from_account_id, to_account_id)(BOTH or ANYONE), from_wallet_id,to_wallet_id, currency_id, amt, relation_id, transaction_for, handle[[][amt,transaction_for]](optional)
     * Returns              : Transaction ID or False
     */

    public function update_account_transaction ($arr = array(), $debit_only = false, $credit_only = false)
    {
        $debit_remark_data = [];
        $credit_remark_data = [];
        $debitted = $credited = false;
        $tax_amt = $handle_amt = 0;
        extract($arr);
        $relation_id = (isset($relation_id) && empty($relation_id)) ? (is_array($relation_id) ? implode(',', $relation_id) : $relation_id) : null;
        $from_account_id = (isset($from_account_id) && !empty($from_account_id)) ? $from_account_id : Config::get('constants.ADMIN_ACCOUNT_ID');
        $to_account_id = (isset($to_account_id) && !empty($to_account_id)) ? $to_account_id : Config::get('constants.ADMIN_ACCOUNT_ID');
        $payment_type_id = isset($payment_type_id) && !empty($payment_type_id) ? $payment_type_id : Config::get('constants.PAYMENT_TYPES.WALLET');
        $to_wallet_id = (isset($to_wallet_id) && !empty($to_wallet_id)) ? $to_wallet_id : $from_wallet_id;
        if ($from_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
        {
            if (isset($handle) && !empty($handle))
            {
                array_walk($handle, function(&$charge) use(&$handle_amt, $currency_id)
                {
                    $handle_amt += $charge['amt'];
                    $charge['currency_id'] = $currency_id;
                });
            }
            if (isset($taxes) && !empty($taxes))
            {
                array_walk($taxes, function($tax) use(&$tax_amt)
                {
                    $tax_amt += $tax['amt'];
                });
            }
        }
        if (!$credit_only)
        {
            if ($bal = $this->updateBalance($from_account_id, $from_wallet_id, $currency_id, $amt, false))
            {
                $from_transaction_id = isset($from_transaction_id) && !empty($from_transaction_id) ? $from_transaction_id : $this->generateTransactionID($from_account_id);
                $debitted = DB::table(Config::get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetID([
                    'account_id'=>$from_account_id,
                    'from_or_to_account_id'=>$to_account_id,
                    'wallet_id'=>$from_wallet_id,
                    'payment_type_id'=>$payment_type_id,
                    'relation_id'=>$relation_id,
                    'amt'=>$amt,
                    'paid_amt'=>$amt,
                    'tax'=>$tax_amt,
                    'currency_id'=>$currency_id,
                    'status'=>Config::get('constants.TRANSACTION_STATUS.CONFIRMED'),
                    'ip_address'=>Request::getClientIp(true),
                    'created_on'=>date('Y-m-d H:i:s'),
                    'transaction_id'=>$from_transaction_id,
                    'transaction_type'=>Config::get('constants.TRANSACTION_TYPE.DEBIT'),
                    'handle_amt'=>$handle_amt,
                    'current_balance'=>$bal,
                    'statementline_id'=>Config::get('stline.'.$transaction_for.'.DEBIT'),
                    'remark'=>json_encode(['key'=>$transaction_for, 'data'=>$debit_remark_data])
                ]);
                if ($debitted && $from_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
                {
                    if (isset($taxes) && !empty($taxes))
                    {
                        foreach ($taxes as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$from_account_id,
                                        'from_wallet_id'=>$from_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                    if (isset($handle) && !empty($handle))
                    {
                        foreach ($handle as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$from_account_id,
                                        'from_wallet_id'=>$from_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ($to_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
        {
            if (isset($handle) && !empty($handle))
            {
                array_walk($handle, function(&$charge) use(&$handle_amt, $currency_id)
                {
                    $handle_amt += $charge['amt'];
                    $charge['currency_id'] = $currency_id;
                });
            }
            if (isset($taxes) && !empty($taxes))
            {
                array_walk($taxes, function($tax) use(&$tax_amt)
                {
                    $tax_amt += $tax['amt'];
                });
            }
        }
        if (!$debit_only && ($credit_only || $debitted))
        {
            $currency_id = (isset($to_currency_id) && !empty($to_currency_id)) ? $to_currency_id : $currency_id;
            $amt = (isset($to_amt) && !empty($to_amt)) ? $to_amt : $amt;
            $to_transaction_id = isset($to_transaction_id) && !empty($to_transaction_id) ? $to_transaction_id : $this->generateTransactionID($to_account_id);
            if ($bal = $this->updateBalance($to_account_id, $to_wallet_id, $currency_id, $amt))
            {
                $credited = DB::table(Config::get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetID([
                    'account_id'=>$to_account_id,
                    'from_or_to_account_id'=>$from_account_id,
                    'wallet_id'=>$to_wallet_id,
                    'payment_type_id'=>$payment_type_id,
                    'relation_id'=>$relation_id,
                    'amt'=>$amt,
                    'paid_amt'=>$amt,
                    'tax'=>$tax_amt,
                    'currency_id'=>$currency_id,
                    'status'=>Config::get('constants.TRANSACTION_STATUS.CONFIRMED'),
                    'ip_address'=>Request::getClientIp(true),
                    'created_on'=>date('Y-m-d H:i:s'),
                    'transaction_id'=>$to_transaction_id,
                    'transaction_type'=>Config::get('constants.TRANSACTION_TYPE.CREDIT'),
                    'handle_amt'=>$handle_amt,
                    'current_balance'=>$bal,
                    'statementline_id'=>Config::get('stline.'.$transaction_for.'.CREDIT'),
                    'remark'=>json_encode(['key'=>$transaction_for, 'data'=>$credit_remark_data])
                ]);
                if ($credited && $to_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
                {
                    if (isset($taxes) && !empty($taxes))
                    {
                        foreach ($taxes as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$to_account_id,
                                        'from_wallet_id'=>$to_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                    if (isset($handle) && !empty($handle))
                    {
                        foreach ($handle as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$to_account_id,
                                        'from_wallet_id'=>$to_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ($debit_only && $debitted)
        {
            return $from_transaction_id;
        }
        else if ($credit_only && $credited)
        {
            return $to_transaction_id;
        }
        else if ($credited)
        {
            return $from_transaction_id;
        }
        return false;
    }

    public function checkBalance ($arr = array())
    {
        $wallet_id = Config::get('constants.WALLET.SELLS');
        extract($arr);
        return DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->where('account_id', $account_id)
                        ->where('currency_id', $currency_id)
                        ->where('wallet_id', $wallet_id)
                        ->where('current_balance', '>=', $amount)
                        ->exists();
    }

    public function updateBalance ($account_id, $wallet_id, $currency_id, $amount, $increment = true)
    {
        $current_balance = 0;

		$balance = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                ->where('account_id', $account_id)
                ->where('currency_id', $currency_id)
                ->where('wallet_id', $wallet_id)
                ->first();

        if ($balance)
        {
            if ($increment || (!$increment && $balance->current_balance >= $amount))
            {
                if ($increment)
                {
                    $current_balance = $balance->current_balance + $amount;
                    $balance->tot_credit = $balance->tot_credit + $amount;
                }
                else
                {
                    $current_balance = $balance->current_balance - $amount;
                    $balance->tot_debit = $balance->tot_debit + $amount;
                }
                DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->where('account_id', $account_id)
                        ->where('currency_id', $currency_id)
                        ->where('wallet_id', $wallet_id)
                        ->update(['tot_credit'=>$balance->tot_credit, 'tot_debit'=>$balance->tot_debit, 'current_balance'=>$current_balance]);
                return $current_balance;
            }
        }
        elseif ($increment)
        {
            if ($increment)
            {
                DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->insert(['account_id'=>$account_id, 'currency_id'=>$currency_id, 'wallet_id'=>$wallet_id, 'tot_credit'=>$amount, 'current_balance'=>$amount]);
                return $amount;
            }
        }
        return false;
    }

    public function generateTransactionID ($account_id)
    {
        $disp = $this->rKeyGen(3, 1);
        return $disp.$account_id.date('dmYHis');
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }
        else
        {
            $key = $this->rKeyGen_ALPHA($digits, false);
        }
        return $key;
    }

    function rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $poss = array();
        $j = 0;
        // Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

    public function replacement_days ()
    {
        return DB::table(Config::get('tables.SERVICE_POLICIES'))
                        ->where('policy_type', 1)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('CONCAT(policy_period,\' \',policy_title) as replacement_day,service_policy_id')
                        ->get();
    }	
	
	function random_strings($length_of_string) 
	{ 
	  
		// String of all alphanumeric character 
		$str_result = '23456789ABCDEFGHJKMNPQRSTUVWXYZ'; 
	  
		// Shufle the $str_result and returns substring 
		// of specified length 
		return substr(str_shuffle($str_result),  
						   0, $length_of_string); 
	} 
	
	
	public function createReferralCode(){
		$ucArr = DB::table(config('tables.ACCOUNT_MST'))                        
                        ->pluck('user_code');
		do{
			$uCode = $this->random_strings(6);
		} while(in_array($uCode,$ucArr));
	
		return $uCode;
	}
   
    public static function generate_BrowseURLS ($data, $mode)
    {
        $url = '';
        if (!empty($data) && !empty($mode))
        {
            switch ($mode)
            {
                case 3:  //browse-from-brands
                    $urlPath[] = $data['cat_path'];
                    $urlPath[] = $data['brand_slug'].'~brand';
                    $urlPath[] = 'br';

                    $qVars[] = 'spath='.$data['sid'];

                    $path = implode('/', $urlPath);
                    $qstr = !empty($qVars) ? '?'.implode('&', $qVars) : '';
                    $url = URL::to($path.$qstr);
                    break;
            }
            switch ($mode)
            {
                case 4:  //browse-category-brands
                    $urlPath[] = 'catalogue';
                    $urlPath[] = 'brands';
                    $urlPath[] = $data['cat_path'];
                    $qVars[] = 'spath='.$data['sid'];

                    $path = implode('/', $urlPath);
                    $qstr = !empty($qVars) ? '?'.implode('&', $qVars) : '';
                    $url = URL::to($path.$qstr);
                    break;
            }
        }
        return $url;
    }

    public function getAvaliablePaymentModes ()
    {
        return DB::table(Config::get('tables.PAYMENT_MODES_LOOKUPS'))
                        ->where('status', Config::get('constants.ACTIVE'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->select('paymode_id as id', 'mode_name as name')
                        ->get();
    }

    public function getAvaliablePaymentTypes ()
    {
        $payments = DB::table(Config::get('tables.PAYMENT_TYPES'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->select('payment_type as title', 'image_name as img')
                ->get();
        array_walk($payments, function(&$p)
        {
            $p->img = URL::asset($p->img);
        });
        return $payments;
    }

    public function gender_list ()
    {
        return DB::table(Config::get('tables.ACCOUNT_GENDER_LOOKUPS'))
                        ->get();
    }   

    public function subscribe ($arr = array())
    {
        extract($arr);
        if (DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                        ->where($subscribe)
                        ->where('is_deleted', Config::get('constants.ON'))
                        ->update(['is_deleted'=>Config::get('constants.OFF')]))
        {
            return true;
        }
        else
        {
            return DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                            ->insertGetId($subscribe);
        }
        return false;
    }

    public function unsubscribe ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                        ->where('email_id', openssl_decrypt($id, Config::get('cipher'), Config::get('key')))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update(['is_deleted'=>Config::get('constants.ON')]);
    }

    public function sendNewLetter ($arr = array())
    {
        extract($arr);
        $to = DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                ->where($subscribe)
                ->where('is_deleted', Config::get('constants.ON'))
                ->select('email_id')
                ->lists('email_id');
        foreach ($to as $email)
        {
            $data['unsubscribe'] = URL::to('api/v1/customer/unsubscribe/'.openssl_encrypt($id, Config::get('cipher'), Config::get('key')));
            Mailer::send($email, $view, $subject, $data);
        }
        return true;
    }

    public function get_pincodes ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.LOCATION_PINCODES'))
                        ->where('pincode', 'like', $search_term.'%')
                        ->selectRaw('pincode_id as id, pincode as text')
                        ->get();
    }

    public function decode_attribute_string ($arr = array())
    {
        if (!empty($arr))
        {
            $str = json_decode(stripslashes(($item_val->specification)));
            if (!empty($str))
            {
                return $str;
            }
        }
    }

    public function getBankingAccountTypes ()
    {
        return DB::table(Config::get('tables.BANKING_ACCOUNT_TYPES'))
                        ->selectRaw('id, account_type')
                        ->orderby('account_type', 'ASC')
                        ->get();
    }

    public function getBusinessTypes ()
    {
        return DB::table(Config::get('tables.TYPE_OF_BUSINESS'))
                        ->selectRaw('business_id,business')
                        ->orderby('business', 'ASC')
                        ->get();
    }

    public function getDocumentTypes ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.DOCUMENT_TYPES'))
                ->selectRaw('document_type_id,type')
                ->orderby('type', 'ASC');
        if (isset($proof_type) && !empty($proof_type))
        {
            $query->where('proof_type', $proof_type);
        }
        return $query->get();
    }

    public static function getDocumentDetails ($document_type_id, $prefix = '')
    {
        $details = DB::table(Config::get('tables.DOCUMENT_TYPES'))
                ->where('document_type_id', $document_type_id)
                ->pluck('other_fields');
        $details = json_decode($details, true);
        if (!empty($details))
        {
            array_walk($details, function(&$field) use($prefix)
            {
                $k = [];
                $k[(!empty($prefix) ? $prefix.'.' : '').$field['id']] = $field['validate']['rules'];
                $field['rules'] = $k;
                $field['validate']['message'] = [$field['id']=>$field['validate']['message']];
                if (!empty($prefix))
                {
                    $field['validate']['message'] = [$prefix=>$field['validate']['message']];
                }
                $field['message'] = array_dot($field['validate']['message']);
                $field = (array) $field;
            });
            $a = [];
            foreach (array_column($details, 'message') as $m)
            {
                $a = array_merge($a, $m);
            }
        }
        return $details;
    }


    public static function generateAPPKey ($arr = array())
    {
        extract($arr);
        if (isset($partner_id) && isset($subscribe_id))
        {
            return base64_encode($partner_id.','.$subscribe_id.','.date('YMdHis'));
        }
        return false;
    }

    public function get_account_list ()
    {

        $query = DB::table(Config::get('tables.ACCOUNT_TYPES'))
                ->selectRaw('account_type_name, id')
                ->where('has_wallet', Config::get('constants.ON'))
                ->orderby('id', 'ASC');

        return $query->get();
    }

    public function randomString ($length = 8)
    {
        $str = '';
        $characters = array_merge(range('a', 'z'), range('0', '9'));
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, 35);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function generateSlug ($string)
    {
        return strtolower(str_replace(' ', '-', $string));
    }

    /*
     * Function Name        : taxValue
     * Params               : $product
     */

    public function taxValue (&$product)
    {
        $product->tax_info = (object) ['total_tax_per'=>0, 'total_tax_amount'=>0, 'taxes'=>[]];
        $date = isset($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $taxes = $taxes = DB::table(Config::get('tables.TAXES').' as t')
                ->join(Config::get('tables.TAX_VALUES').' as tv', function($tv) use($product)
                {
                    $tv->on('tv.tax_id', '=', 't.tax_id')
                    ->where('tv.post_type_id', '=', Config::get('constants.POST_TYPE.CATEGORY'))
                    ->where('tv.relative_id', '=', $product->category_id)
                    ->where('tv.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->where(function($range) use($product)
                {
                    $range->where('tv.is_range', Config::get('constants.OFF'))
                    ->orWhere(function($range1) use($product)
                    {
                        $range1->where('tv.is_range', Config::get('constants.ON'))
                        ->where('tv.range_start_from', '>=', $product->price)
                        ->where('tv.range_end_to', '<=', $product->price);
                    });
                })
                ->where('t.start_date', '<=', $date)
                ->where('t.end_date', '>=', $date)
                ->where('t.is_deleted', Config::get('constants.OFF'))
                ->where('t.status', Config::get('constants.ACTIVE'))
                ->select('t.tax', 't.value_type', 'tv.tax_value', 't.currency_id')
                ->get();
        $product->tax_info->taxes = $taxes;
        array_walk($taxes, function($tax) use(&$product)
        {
            if ($tax->value_type == Config::get('constants.TAX_VALUE_TYPE.PERCENTAGE'))
            {
                $product->tax_info->total_tax_per+=$tax->tax_value;
            }
            elseif (isset($currency_id) && !empty($currency_id))
            {
                $rate = $this->get_exchange_rate($tax->currency_id, $currency_id);
                $product->tax_info->total_tax_amount+=($rate * $tax->tax_value);
            }
        });
    }

    public function getNotifications ($arr = array(), $count = false, $limit = 5)
    {
        extract($arr);
        $res = DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS').' as n')
                ->leftJoin(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ').' as st', function ($subquery) use($account_id)
                {
                    $subquery->on('st.notification_id', '=', 'n.notification_id')
                    ->where('st.account_id', '=', $account_id);
                })
                ->whereRaw('FIND_IN_SET('.$account_id.',n.account_ids)')
                ->where('n.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('n.notification_id,n.data,n.created_on,st.read_on')
                ->orderby('n.created_on', 'desc');
        if ($count)
        {
            return $res->count();
        }
        else
        {
            $notifications = $res->take($limit)->get();
            array_walk($notifications, function(&$notification)
            {
                $notification->data = json_decode($notification->data);
                $notification->data->id = $notification->notification_id;
                $notification->data->created_on = date('d-M-Y H:i:s', strtotime($notification->created_on));
                $notification = $notification->data;
            });
            return $notifications;
        }
    }

    public function updateNotificationToken ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('device_log_id', $device_log_id)
                        ->update(['fcm_registration_id'=>$fcm_registration_id]);
    }

    public function markNotificationRead ($arr = array())
    {
        $arr['read_on'] = date('Y-m-d H:i:s');
        return DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ'))
                        ->insertGetID($arr);
    }

    public function getAccountActivationKey ($account_id)
    {
        return $activation_key = DB::table(Config::get('tables.ACCOUNT_MST'))
                ->where('account_id', $account_id)
                ->value('activation_key');
    }

    public function updateAccountVerificationCode ($device_log_id)
    {		
        return $code = rand(100000, 999999);
        return (DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('device_log_id', $device_log_id)
                        ->update(['code'=>$code])) ? $code : false;
    }

    public function checkAccountVerificationCode ($device_log_id, $code, $update = true)
    {
		return true;
        $query = DB::table(Config::get('tables.DEVICE_LOG'))
                ->where('device_log_id', $device_log_id)
                ->where('code', $code);
        return $update ? $query->update(['code'=>null]) : $query->exists();
    }

    public function checkAccount ($username, $account_type_id = NULL)
    {
        $query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                ->selectRaw('am.account_id, am.account_type_id,  concat(ad.firstname,\' \',ad.lastname) as full_name, ad.firstname, ad.lastname, am.email, am.mobile, am.uname, am.is_deleted, am.pass_key, am.login_block')
                ->where('am.is_deleted', Config::get('constants.OFF'))
                //->where('am.account_type_id', '!=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                ->where(function($subquery) use($username)
				{
					$subquery->where('am.uname', $username)
							->orWhere('am.email', 'like' , '%'.$username.'%')
							->orWhere('am.mobile', $username);
				});
				if (!empty($account_type_id))
				{
					$query->where('am.account_type_id', $account_type_id);
				}
        return $query->first();
    }

    /**
     * @param array $arr associate array of username and password
     * @param int $account_type_id check account type<i>Option(Default NULL)</i>
     * @return boolean in update means true else false
     */
    public function updatePassword ($arr, $account_type_id = NULL)
    {
        extract($arr);
        DB::beginTransaction();
        if ($this->checkAccountVerificationCode($device_log_id, $verification_code))
        {
            $query = DB::table(Config::get('tables.ACCOUNT_MST'))
                    ->where(function($subquery) use($username)
            {
                $subquery->where('uname', $username)
                ->orWhere('email', $username)
                ->orWhere('mobile', $username);
            });
            if (!empty($account_type_id))
            {
                $query->where('account_type_id', $account_type_id);
            }
            if ($query->update(['pass_key'=>md5($password)]))
            {
                DB::commit();
                return 1;
            }
            return 2;
        }
        DB::rollback();
        return false;
    }

    /**
     * @param array $arr associate array with account_id, current_password,new_password
     * @return bool True if password changed or else false if incorrect old password/Old and new passwords are same
     */
    public function changePassword ($arr = array())
    {
		extract($arr);
		if($account_id>0 && !empty(trim($new_password)))
		{
			$data['pass_key'] = md5($new_password);
		   
			if (DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id', $account_id)
							->where('pass_key', md5($current_password))
							->count()){ 
				$status = DB::table(config('tables.ACCOUNT_MST'))
					->where('account_id', $account_id)
					->update(['pass_key'=>md5($new_password)]);
				if ($status)
				{
					return array(
						'status'=>config('httperr.SUCCESS'),
						'msg'=>'Password updated successfully',
						'alertclass'=>'success');
				}
				else
				{
					return array(
						'status'=> config('httperr.UN_PROCESSABLE'),
						'msg'=> 'Please try with different password',
						'alertclass'=>'danger');
				}
			}
			else{
				return array(
					'status'=>config('httperr.UN_PROCESSABLE'),
					'msg'=> "Request not completed. Please try later",
					'alertclass'=>'danger');
			}
		}
        return array('status'=>config('httperr.UN_PROCESSABLE'),'msg'=>'Account details are missing', 'alertclass'=>'warning');
    }
    

    /**
     * @param string $activation_key  which account's activation_key
     * @return bool True if email id verified else false if code is incorrect
     */
    public function updateEmailVerification ($activation_key)
    {
        $account_id = DB::table(Config::get('tables.ACCOUNT_MST'))
                ->where('activation_key', $activation_key)                
                ->value('account_id');
        if ($account_id)
        {
            return DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                            ->where('account_id', $account_id)
                            ->where('is_email_verified', Config::get('constants.OFF'))
                            ->update(['is_email_verified'=>Config::get('constants.ON')]);
        }
        return false;
    }

    /**
     * @param int $account_id  which account's mobile verification to be changed
     * @param int $device_log_id  to check verification code sent in mobile
     * @param int $code User input code
     * @return bool True if mobile id verified else false if code is incorrect
     */
    public function updateMobileVerification ($account_id, $device_log_id, $code)
    {		
        DB::beginTransaction();
        if ($this->checkAccountVerificationCode($device_log_id, $code))
        {
            if (DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                            ->where('account_id', $account_id)
                            ->where('is_mobile_verified', Config::get('constants.OFF'))
                            ->update(['is_mobile_verified'=>Config::get('constants.ON')]))
            {
                DB::commit();
                return true;
            }
        }
        DB::rollback();
        return false;
    }

    public function getCustomerCount ()
    {
        $count = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                /* ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as al', function($p)
                {
                    $p->on('al.account_id', '=', 'am.account_id')
                    ->where('al.account_type_id', '=', Config::get('constants.ACCOUNT_TYPE.USER'));
                }) */				
                ->where('account_type_id', Config::get('constants.ACCOUNT_TYPE.USER'))
                ->where('is_deleted', Config::get('constants.OFF'))
                ->selectRaw('count(am.account_id) as total,sum(IF(am.status='.Config::get('constants.ACTIVE').',1,0)) as active')
                ->first();
        if ($count)
        {
            $count->total = number_format($count->total, 0, '.', ',');
            $count->active = number_format($count->active, 0, '.', ',');
        }
        else
        {
            $count = (object) ['total'=>0, 'active'=>0];
        }
        return $count;
    }

    public static function currency_format ($arr = array())
    {
        $currency = $currency_symbol = null;
        $decimal = 2;
        extract($arr);
        if (isset($currency_id) && (!isset($currency) || !isset($currency_symbol)))
        {
            $c = DB::table(Config::get('tables.CURRENCIES'))
                    ->where('currency_id', $currency_id)
                    ->select('currency', 'currency_symbol', 'decimal_places')
                    ->first();
            if (!empty($c))
            {
                $currency = $c->currency;
                $currency_symbol = $c->currency_symbol;
                $decimal = $c->decimal_places;
            }
        }
        return $currency.' '.number_format($amt, $decimal, '.', ',').' '.$currency_symbol;
    }

    public static function randomCode ($length = 8, $c = 'A-Za-z0-9')
    {
        switch ($c)
        {
            case 'A-Za-z0-9':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz1234567890';
                $chars_count = 41;
                break;
            case 'A-Za-z':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
                $chars_count = 41;
                break;
            case 'A-Z0-9':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                $chars_count = 35;
                break;
            case 'a-z0-9':
                $chars = 'abcdefghijkmnpqrstuvwxyz1234567890';
                $chars_count = 35;
                break;
            case 'a-z':
                $chars = 'abcdefghijkmnpqrstuvwxyz';
                $chars_count = 25;
                break;
            case 'A-Z':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $chars_count = 25;
                break;
        }
        $string = $chars{rand(0, $chars_count)};
        for ($i = 1; $i < $length; $i = strlen($string))
        {
            $r = $chars{rand(0, $chars_count)};
            if ($r != $string{$i - 1})
                $string .= $r;
        }
        return $string;
    }

    public static function generateUserCode ()
    {
        $user_codes = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))
                ->lists('user_code');
        re_create:
        $code = self::randomCode(8, 'A-Z0-9');
        if (in_array($code, $user_codes))
        {
            goto re_create;
        }
        else
        {
            return $code;
        }
    }

    public function checkPincode ($pincode, $with_cities = false)
    {
        $lQuery = DB::table(Config::get('tables.LOCATION_PINCODES').' as lp')
                ->where('lp.pincode', $pincode)
                ->leftJoin(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
                ->leftJoin(Config::get('tables.LOCATION_REGIONS').' as lr', 'lr.region_id', '=', 'ls.region_id')
                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id');
        if ($with_cities)
        {
            $locations = $lQuery->select('lp.pincode_id', 'lp.pincode', 'lc.country_id', 'lc.country', 'ls.state_id', 'ls.state', 'ls.region_id', 'lr.region', 'ld.district_id', 'ld.district')
			->orderby('lp.pincode_id','DESC')
            ->first();
			
            if (!empty($locations))
            {
                $locations->cities = DB::table(Config::get('tables.LOCATION_CITY'))
                        ->where('pincode_id', $locations->pincode_id)
                        ->select('city_id as id', 'city as text')
                        ->get();
            }
            return $locations;
        }
        else
        {
            return $lQuery->select('lp.pincode_id', 'lp.pincode', 'lc.country_id', 'lc.country', 'ls.state_id', 'ls.state', 'ls.region_id', 'lr.region', 'ld.district_id', 'ld.district')
                            ->first();
        }
    }

    public function postRequest ($url, $data = array())
    {
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, ['X-Device-Token:'.Config::get('device_log')->token, 'Content-Type: application/json']);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        if ($response === FALSE)
        {
            die('Curl failed: '.curl_error($request));
        }
        curl_close($request);
        return (object) ['status'=>$status, 'responseJSON'=>!empty($response) ? json_decode($response) : []];
    }	
	
	public function country_list (array $arr = array(), $check_status = true){
        $qry =  DB::table(config('tables.LOCATION_COUNTRY'))
                ->orderby('country', 'ASC')
                ->selectRAW('country_id,country,phonecode');

        if ($check_status)
        {
            $qry->where('status', config('constants.ON'));
        }
        return $qry->get();
    }

  public function get_currency_exchange($from_currency_id,$to_currency_id){

		$result = '';
		$result = DB::table(Config('tables.CURRENCY_EXCHANGE_SETTINGS'))
							->select('rate')
							->where(array('from_currency_id'=>$from_currency_id,'to_currency_id'=>$to_currency_id))
							->first();

		if(!empty($result) && count($result) > 0)
		{              
		  return $result;
		  
		}
		return false;
	}

 public function get_countries ($country_id = '')
    {
  
        $res = DB::table(Config::get('tables.LOCATION_COUNTRY'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->where('operate', Config::get('constants.ACTIVE'));
        if (!empty($country_id))
        {
            $res->where('country_id', $country_id);
        }
        $query = $res->select('country', 'country_id', 'iso2', 'phonecode','mobile_validation')
                ->orderBy('country', 'asc')
                ->get();
        return $query;
    }
	
	public function getIpCountry ()
    {
				
		$ip = request()->server('SERVER_ADDR');
		if($ip == '::1'){
			$ip = '103.231.216.102';
		}
        try
        {
            $ipInfo = json_decode(file_get_contents('http://ipinfo.io/'.$ip.'/json'));
	        return $ipInfo->country;
        }
        catch (Exception $e)
        {
            return NULL;
        }
    }
	
	public function maskMobile ($mobile)
    {
        $len = strlen($mobile);
        return substr_replace($mobile, str_repeat('*', $len - ( $len > 5 ? 4 : 2)), $len > 5 ? 2 : 1, $len - ($len > 5 ? 4 : 2));
    }

    public function maskEmail ($email)
    {
        $email = explode('@', $email);
        $len = strlen($email[0]);
        $v = $len - ( $len > 5 ? 4 : 2);
        return ($v >= 0 ? substr_replace($email[0], str_repeat('*', $v), $len > 5 ? 2 : 1, $v) : $email[0]).'@'.$email[1];
    }
	
	public function get_access_leveles($account_type = ''){
		$qry = DB::table(Config('tables.ACCESS_LEVEL_LOOKUP'))
				->where('is_deleted',0);
				if($account_type !=''){
					$qry = $qry->where('account_type_id',$account_type);
				}
			$qry->select('access_id','access_name');
			$result = $qry->get();
			return $result;
	}
	
	
	/* LogOut All Devices */
	public function logoutAllDevices ($account_id,$account_log_id = null)
    {  	   
        $qry = DB::table(Config::get('tables.ACCOUNT_LOG').' as al')
		        ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'al.account_id')
		        ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'am.account_type_id')
                ->where('al.account_id', $account_id)
                ->where('al.is_deleted', Config::get('constants.OFF'));
		if(!empty($account_log_id) && $account_log_id != null)
		{
		    $qry->where('al.account_log_id', '!=', $account_log_id);
		}		
        $tokens = $qry->selectRaw('al.token,al.account_log_id,at.account_type_key')->get();

        foreach ($tokens as $token)
        {
            $token->token = explode('-', $token->token);
		
			$path= Config::get('session.files').'/'.$token->token[0];
            if (!empty($token->token[0]) && file_exists($path))
            {
				$data = unserialize(file_get_contents($path));
				unset($data[$token->account_type_key]);
				file_put_contents($path, serialize($data));				
                $this->logoutAccountLog($token->account_log_id);
            }
        }       
        //return $paths;
		return true;
    }
	
	public function logoutAccountLog ($id, $isCurrent = false)
    {
        if (DB::table(Config::get('tables.ACCOUNT_LOG'))
                        ->where('account_log_id', $id)
                        ->update(['is_deleted'=>Config::get('constants.ON')]))
        {
		    if ($isCurrent)
            {
				session()->forget(Config::get('app.role'));
				if (!Config::get('app.is_api'))
				{
					Cookie::queue(Cookie::forget(Config::get('app.session_key')));
				}
            }
            return true;
        }
        return false;
    }
	
	/* Get_Ifsc_Bank_Details */
	public function Get_Ifsc_Bank_Details (array $arr = array())
    {	

	    $res = $ifscdata = $bdata = [];
    	extract($arr);
		$bankInfo =  DB::table(Config::get('tables.PAYOUT_BANK_IFSC_MST').' as pbi')
					->join(Config::get('tables.PAYOUT_BANK_MST') . ' as pb', 'pb.bank_id', '=', 'pbi.bank_id')
					->where('pbi.ifsc_code', '=', $ifsc)
					//->where('pb.country_id', '=', $country_id)
					->select('pb.bank_id','pb.bank_name as bank')
					->first();		
		if(!empty($res))
		{   
	        $res->valid = true;
		} 
		else 
		{
		    $bankData = CommonLib::validetIFSC($ifsc);				
			/*print_r($res);die;
			[{"bank":"BANK OF MAHARASHTRA","ifsc":"MAHB0001821","micr":"444014548","branch":"CHANDUR BAZAR","address":"NEAR KALPANA GAS SERVICE, PLOT NO.128, WARD NO.12, BELORA ROAD, CHANDUR BAZAR, DIST AMRAVATI, PIN CODE 444704","contact":"243232","city":"AMRAVATI","district":"CHANDUR BAZAR","state":"MAHARASHTRA"}]
					    */
           /* $bankData = json_decode('{"ifsc":"MAHB0001821","micr":"444014548","bank":"BANK OF MAHARASHTRA","branch":"CHANDUR BAZAR","address":"NEAR KALPANA GAS SERVICE, PLOT NO.128, WARD NO.12, BELORA ROAD, CHANDUR BAZAR, DIST AMRAVATI, PIN CODE 444704","city":"AMRAVATI","district":"CHANDUR BAZAR","state":"MAHARASHTRA","contact":"243232","valid":"true"}'); */

		    if(!empty($bankData))
			{
				$bankData->valid = true;
				DB::beginTransaction();		
				if(!($bank_id=DB::table(Config::get('tables.PAYOUT_BANK_MST').' as pb')				
						->where('pb.bank_name', '=', $bankData->bank)
						->value('bank_id'))){
					$bdata['bank_name'] = $bankData->bank;
					$bdata['country_id'] = $country_id;
					$bdata['created_by'] = $account_id;
					$bdata['created_on'] = getGTZ();	
					$bank_id = DB::table(Config::get('tables.PAYOUT_BANK_MST'))
							->insertGetID($bdata);	
				}
				if(!empty($bank_id))
				{
					if(!DB::table(Config::get('tables.PAYOUT_BANK_IFSC_MST').' as pbi')					
						->where('pbi.ifsc_code', '=', $bankData->ifsc)
						->where('pbi.bank_id', '=', $bank_id)					
						->exists()){
						$ifscdata = [];
						$ifscdata['bank_id'] = $bank_id;		
						$ifscdata['ifsc_code'] = $bankData->ifsc;
						$ifscdata['branch'] = $bankData->branch;
						$ifscdata['district'] = $bankData->district;
						$ifscdata['state'] = $bankData->state;
						$ifscdata['created_on'] = getGTZ();			
						DB::table(Config::get('tables.PAYOUT_BANK_IFSC_MST'))
							->insertGetID($ifscdata);
					}
					DB::commit();
				}		
				DB::rollback();	
				$bankData->bank_name = $bankData->bank;
				$bankData->branch_name = $bankData->branch;
		    }
			
			return ($bankData);
		}
		return $res;
    }	  

	public function getPaymentTypeId (array $arr = array())
    {
        extract($arr);
        $qry = DB::table(config('tables.PAY_PAYMENT_SETTINGS').' as ps')
					->join(config('tables.PAYMENT_MODES_LOOKUPS').' as apm', 'apm.paymode_id', '=', 'ps.pay_mode')
					->join(config('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'ps.payment_type_id')
					->where('ps.currency_id', $currency_id)
					->where('ps.country_id', $country_id)
					->select('pt.payment_key as payment_code', 'pt.payment_type_id', 'pt.payment_type');
        if (is_array($payment_mode))
        {
            if (is_numeric($payment_mode[0]))
            {
                $qry->whereIn('apm.paymode_id', $payment_mode);
            }
            else
            {
                $qry->whereIn('pt.payment_key', $payment_mode);
            }
        }
        else
        {
            if (is_numeric($payment_mode))
            {
                $qry->where('apm.paymode_id', $payment_mode);
            }
            else
            {
                $qry->where('pt.payment_key', $payment_mode);
            }
        }
        return $qry->first();
    }
	
	
	
	public function getGateWayInfo ($payment_code, array $arr = array())
    {
		print_r($arr);exit;
        extract($arr);
        $payment_details = DB::table(config('tables.PAYMENT_TYPES').' as pt')
                ->where('pt.payment_key', $payment_code)
                ->selectRaw('pt.payment_type_id,gateway_settings,save_card,pt.payment_type as paymentgateway_name')
                ->first();
			
        if (!empty($payment_details))
        {
            $settings = $payment_details->gateway_settings = json_decode($payment_details->gateway_settings);
			//print_r($settings);exit;
            if (!empty($settings) && is_object($settings))
            {
                if (!empty($card_id))
                {
                    $card_details = DB::table(config('tables.ACCOUNT_PAYMENT_CARD_SETTINGS'))
                            ->where('is_deleted', $this->config->get('constants.OFF'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('account_id', $account_id)
                            ->where('id', $card_id)
                            ->selectRaw('card_type_id,account_details')
                            ->first();
                    if (!empty($card_details))
                    {
                        $card_details->account_details = $this->xpb_decrypt($card_details->account_details);
                    }
                }
                $settings = $settings->status ? (array) $settings->live : (array) $settings->sandbox;
                $pgr = [];
                $pgr['account_id'] 		= $account_id;
                $pgr['account_log_id'] 	= $account_log_id;
                $pgr['payment_type_id'] = $payment_details->payment_type_id;
                $pgr['pay_mode_id'] 	= $this->config->get('constants.PAYMENT_MODES.'.$payment_mode);
                $pgr['purpose'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.'.$purpose);
                $pgr['relative_post_id'] = $id;
                $pgr['currency_id'] = $currency_id;
                $pgr['amount'] = $amount;
                $pgr['created_on'] = getGTZ();
                $pgr_id = DB::table(config('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->insertGetID($pgr);
                switch ($payment_code)
                {
                    case 'pay-u':
                        $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        $settings = array_merge($settings, ['productinfo'=>$remark, 'firstname'=>$firstname, 'email'=>$email, 'mobile'=>$mobile, 'udf1'=>$pgr_id]);
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['amount'] = number_format((float) $amount, 2, '.', '');
                        $settings['txnid'] = substr(hash('sha256', mt_rand().microtime()), 0, 20);

                        $hashSequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt';
                        $hashVarsSeq = explode('|', $hashSequence);
                        $hash_string = '';
                        foreach ($hashVarsSeq as $hash_var)
                        {
                            $hash_string .= isset($settings[$hash_var]) ? $settings[$hash_var] : ($this->config->get('app.is_api') && in_array($hash_var, ['udf1', 'udf2', 'udf3', 'udf4', 'udf5']) ? $hash_var : '');
                            $hash_string .= '|';
                        }
                        $settings['hash_values_ulr'] = route('payment-gateway-response.check-sum', ['payment_type'=>$payment_code]);
                        $settings['hash'] = strtolower(hash('sha512', trim($hash_string, '|')));
                        $settings['vas_for_mobile_sdk_hash'] = strtolower(hash('sha512', $settings['key'].'|vas_for_mobile_sdk|default|'.$settings['salt']));
                        $settings['verify_payment_hash'] = strtolower(hash('sha512', $settings['key'].'|verify_payment|'.$settings['txnid'].'|'.$settings['salt']));
                        $settings['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $settings['key'].'|payment_related_details_for_mobile_sdk|default|'.$settings['salt']));
                        $settings['pg'] = $payment_details->gateway_settings->modes[$payment_mode];
                        $settings['surl'] = route('payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['furl'] = route('payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['curl'] = route('payment-gateway-response.cancelled', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        break;
                    case 'pay-dollar':
                        $settings['orderRef'] 	= $pgr_id;
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['currCode'] 	= $payment_details->gateway_settings->currCode->{$currency_id};
                        $settings['payType'] 	= $payment_details->gateway_settings->payType;
                        $settings['lang'] 		= $payment_details->gateway_settings->lang;
                        $settings['amount'] 	= number_format((float) $amount, 2, '.', '');
                        $settings['billingFirstName'] 	= $firstname;
                        $settings['billingLastName'] 	= $lastname;
                        $settings['billingEmail'] 		= $email;
                        $settings['custIPAddress'] 		= $ip;
                        if (isset($card_details) && !empty($card_details))
                        {
                            $settings['epMonth'] = $card_details['month'];
                            $settings['epYear'] = $card_details['year'];
                            $settings['cardNo'] = $card_details['card_no'];
                            $settings['cardHolder'] = $card_details['holder'];
                            $settings['pMethod'] = $payment_details->gateway_settings->pMethod->{$card_details->card_type_id};
                        }
                        else
                        {
                            $settings['epMonth'] 	= null;
                            $settings['epYear'] 	= null;
                            $settings['cardNo'] 	= null;
                            $settings['cardHolder'] = null;
                            $settings['pMethod'] 	= NULL;
                        }
                        $settings['securityCode'] 	= null;
                        $settings['remark'] 		= $remark;
                        $settings['successUrl'] 	= route('payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['failUrl']   		= route('payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['cancelUrl'] 		= route('payment-gateway-response.cancelled', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        break;
                    case 'cashfree':
                        $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['paymentModes']  = $payment_details->gateway_settings->modes[$payment_mode];
                        $settings['merchant_name'] = $this->siteConfig->site_name;
                        $settings['merchant_url']  = url('/');
                        $settings['orderId'] 	   = $pgr_id;
                        $settings['orderNote'] 	   = 'test'; //$remark;
                        $settings['orderCurrency'] = $this->get_currency_code($currency_id);
                        $settings['customerName']  = $firstname;
                        $settings['customerEmail'] = $email;
                        $settings['customerPhone'] = $mobile;
                        $settings['orderAmount']   = number_format($amount, 2, '.', ',');
                        $settings['returnUrl'] = route('payment-gateway-response.return', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['notifyUrl'] = route('payment-gateway-response.notify', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['checksumUrl'] = route('payment-gateway-response.check-sum', ['payment_type'=>$payment_code]);
                        ksort($settings);
                        //$signatureData = 'appId='.$settings['appId'].'&orderId='.$settings['orderId'].'&orderAmount='.$settings['orderAmount'].'&customerEmail='.$settings['customerEmail'].'&customerPhone='.$settings['customerPhone'].'&orderCurrency='.$settings['orderCurrency'];
                        $signatureData = 'appId='.$settings['appId'].'&orderId='.$settings['orderId'].'&orderAmount='.$settings['orderAmount'].'&returnUrl='.$settings['returnUrl'].'&paymentModes='.$settings['paymentModes'];
                        $settings['signature'] = base64_encode(hash_hmac('sha256', $signatureData, $settings['secretKey'], true));
                        break;
                }
                $settings['id'] = base64_encode($pgr_id);
                $settings['datafeed'] = route('payment-gateway-response.datafeed', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                return $this->xpb_encrypt($settings);
            }
        }
        return false;
    }
	public function getCountries(array $arr = array())
    {
        $arr = array_filter($arr);
        $countries = DB::table(Config::get('tables.LOCATION_COUNTRY'))
                ->where('status', Config::get('constants.ON'));
        if (!empty($arr))
        {
            $countries->where($arr);
        }
        $countries = $countries->selectRaw('country_id,country,phonecode,mobile_validation,iso2 as flag')
                ->get();
        foreach ($countries as &$country)
        {
            $country->flag = asset('resources/assets/imgs/flags/'.strtolower($country->flag).'.png');
            $country->mobile_validation = str_replace('$/', '', (str_replace('/^', '', $country->mobile_validation)));
        }
        return $countries;
    }
	public function getPhysicalLocations()
    {
        return DB::table(Config::get('tables.PHYSICAL_LOCATIONS_LOOKUP'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('id,label')
                        ->get();
    }
	
	public function supplierProductDetails ($arr = array())
    {
        extract($arr);
       //$product = DB::table(config('tables.SUPPLIER_PRODUCT_ITEMS').' as pi')
        $product = DB::table(config('tables.SUPPLIER_PRODUCTS_LIST').' as pi')
                    ->leftjoin(config('tables.PRODUCTS').' as p', 'p.product_id', '=', 'pi.product_id')
                    ->leftjoin(config('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'pi.product_id')
                    ->leftjoin(config('tables.PRODUCT_CATEGORIES').' as pcat', 'pcat.category_id', '=', 'p.category_id')
                    ->leftjoin(config('tables.PRODUCT_STOCK_STATUS_LOOKUPS').' as ss', 'ss.stock_status_id', '=', 'pi.stock_status_id')
                    ->leftJoin(config('tables.RATING').' as r', function($subquery)
                    {
                            $subquery->on('r.relative_post_id', '=', 'p.product_id')
                                     ->where('r.post_type_id', '=', config('constants.POST_TYPE.PRODUCT'));
                    })
                    ->leftjoin(config('tables.SUPPLIER_PRODUCT_PRICE').' as pp','pp.supplier_product_id','=','pi.supplier_product_id')
                     
               /* ->leftjoin(config('tables.PRODUCT_MRP_PRICE').' as mrp', function($mrp) use($currency_id)
                {
                    $mrp->on('mrp.product_id', '=', 'pi.product_id')
                    ->where('mrp.currency_id', '=', $currency_id);
                })
                ->leftjoin(config('tables.SUPPLIER_PRODUCT_CMB_PRICE').' as pcp', function($pcp)
                {
                    $pcp->on('pcp.product_cmb_id', '=', 'pi.product_cmb_id')
                    ->on('pcp.supplier_id', '=', 'pi.supplier_id')
                    ->on('pcp.currency_id', '=', 'pp.currency_id');
                })
                ->leftjoin(config('tables.SUPPLIER_BRAND_ASSOCIATE').' as spba', function($spba)
                {
                    $spba->on('spba.brand_id', '=', 'p.brand_id')
                    ->on('spba.supplier_id', '=', 'pi.supplier_id');
                }) 
                ->leftjoin(config('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as spca', function($spba)
                {
                    $spba->on('spca.category_id', '=', 'p.category_id')
                    ->on('spca.supplier_id', '=', 'pi.supplier_id');
                }) */
                ->leftjoin(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'pp.currency_id')
                ->leftjoin(config('tables.SERVICE_POLICIES').' as sp', 'sp.service_policy_id', '=', 'pcat.replacement_service_policy_id')
                ->selectRAW('pp.off_perc,pi.supplier_product_code,pi.supplier_id,p.product_id,pi.product_cmb_id,pi.is_shipping_beared,sp.policy_period,sp.policy_title,sp.policy_desc,sp.policy_period as replacement_due_days,p.product_name,greatest(pd.weight,((pd.height*pd.length*pd.width)/500)) as weight,p.category_id,pi.brand_id as supplier_brand_id,pi.category_id as supplier_category_id,p.brand_id,pi.supplier_product_id,pi.is_shipping_beared,cur.currency_id,cur.currency,cur.currency_symbol,pp.price as numeric_price,pi.mrp_price as numeric_mrp_price, pi.mrp_price, pi.price,ss.status as stock_status')
                ->where('pi.supplier_product_code', $supplier_product_code)
                ->first();

        if($product)
        {
            $product->user = $user;
            $product->qty = (isset($qty) && !empty($qty)) ? $qty : 1;
            $product->country_id = $country_id;
            $product->properties = $this->productChoosableProperties($product->product_id, $product->product_cmb_id);
            $product->specification = [];
            $properties = [];
            array_walk($product->properties, function($p) use(&$properties)
            {
                $properties[] = $p->description;
            });
            $product->specification['pro_attr'] = implode(', ', $properties);
            $product->replacement_due_date = date('Y-m-d H:i:s', strtotime('+'.$product->replacement_due_days.'days'));
            $product->imgs = $this->imageObj->get_imgs($product->product_id);

           //$product->geo_zone_id = $this->getGeoZone(['']);
            $this->getShipmentDetails($product);
            $this->productCommissions($product);
            $this->get_product_discounts($product, true);
            $product->expected_delivery_date = date('Y-m-d');
            $product->shippment_info     = [];
            $this->taxValue($product);
            $product->tax_info->tax_per  = ($product->sub_total > 0) ? $product->tax_info->total_tax_per + (($product->tax_info->total_tax_amount / $product->sub_total) * 100) : 0;
            //$product->tax = $product->sub_total - (($product->sub_total / (100 + $product->tax_info->tax_per)) * 100);
            
            $product->tax = ($product->sub_total*$product->tax_info->tax_per)/100;
            $product->tax = round($product->tax, 2);
            //$product->commission_info->supplier_tax_total = $product->commission_info->supplier_price_sub_total - (($product->commission_info->supplier_price_sub_total / (100 + $product->tax_info->tax_per)) * 100);
            $product->commission_info->supplier_tax_total = ($product->commission_info->supplier_price_sub_total * $product->tax_info->tax_per)/100;
            
            $product->commission_info->supplier_tax_total = round($product->commission_info->supplier_tax_total, 2);
            $product->commission_info->partner_tax_total = $product->commission_info->partner_margin_sub_total - (($product->commission_info->partner_margin_sub_total / (100 + $product->tax_info->tax_per)) * 100);
            $product->commission_info->partner_tax_total = round($product->commission_info->partner_tax_total, 2);
            $product->net_pay = $product->sub_total + $product->shipping_charge;
        }
        
        return $product;
    }
	
	public function productChoosableProperties ($product_id, $product_cmb_id = NULL)
    {
        $query = DB::table(config('tables.PRODUCT_PROPERTY').' as pp')
                ->join(config('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'pp.property_id')
                ->join(config('tables.PRODUCT_PROPERTY_VALUES').' as ppv', 'ppv.pp_id', '=', 'pp.pp_id')
                ->join(config('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv', 'pv.value_id', '=', 'ppv.value_id')
                ->leftjoin(config('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
                ->where('ppv.is_deleted', config('constants.OFF'))
                ->where('pp.is_deleted', config('constants.OFF'))
                ->where('pp.product_id', $product_id)
                ->where('pp.choosable', config('constants.ON'))
                ->selectRaw('pk.property_id,pk.property,pk.values_options_type,pv.value_id,concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value');
        if (!empty($product_cmb_id))
        {
            $query->join(config('tables.PRODUCT_CMB_PROPERTIES').' as pcp', function($pcp)
            {
                $pcp->on('pcp.property_id', '=', 'pp.property_id')
                        ->on('pcp.value_id', '=', 'ppv.value_id')
                        ->where('pcp.is_deleted', '=', config('constants.OFF'));
            });
        }
        $properties = $query->get();
        array_walk($properties, function(&$property)
        {
            $property->description = $property->property.': '.$property->value;
        });
        return $properties;
    }


    public function getShipmentDetails (&$product)
    {
        $product->shipping_charge = 0;
        $product->delivery_days = 0;
        $product->mode = null;
        if (isset($product->product_id) && !empty($product->product_id) && !isset($product->weight))
        {
            $weightQry = DB::table(config('tables.PRODUCTS_LIST'))
                    ->where('product_id', $product->product_id);
            if (isset($product->product_cmb_id) && !empty($product->product_cmb_id))
            {
                $weightQry->where('product_cmb_id', $product->product_cmb_id);
            }
            $product->weight = $weightQry->selectRaw('greatest(weight,volumetric_weight) as weight')->pluck('weight');
        }
        if (isset($product->supplier_product_id) && !empty($product->supplier_product_id) && !isset($product->is_shipping_beared))
        {
            $is_shipping_beared = DB::table(config('tables.SUPPLIER_PRODUCT_ITEMS'))
                    ->where('supplier_product_id', $product->supplier_product_id)
                    ->pluck('is_shipping_beared');
            $product->is_shipping_beared = $is_shipping_beared;
        }
        $supplierdetails = DB::table(config('tables.SUPPLIER_PREFERENCE').' as sp')
                ->leftJoin(config('tables.SUPPLIER_PICKUP_ADDRESS').' as spa', 'spa.supplier_id', '=', 'sp.supplier_id')
                ->leftJoin(config('tables.LOCATION_PINCODES').' as lp', 'lp.pincode', '=', 'spa.postal_code')
                ->leftJoin(config('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
                ->leftJoin(config('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
                ->selectRaw('sp.is_ownshipment,sp.logistic_id,spa.postal_code,ls.region_id,ls.country_id')
                ->where('sp.supplier_id', $product->supplier_id)
                ->first();
        if (!empty($supplierdetails))
        {
            $supplierdetails->is_shipping_beared = $product->is_shipping_beared;
            $supplierdetails->logistic_id = (!empty($supplierdetails) && !$supplierdetails->is_ownshipment && !empty($supplierdetails->logistic_id)) ? $supplierdetails->logistic_id : Config::get('constants.DEFAULT.LOGISTIC_ID');
            $supplierdetails->mode_id = $product->mode_id = isset($product->mode_id) && !empty($product->mode_id) ? $product->mode_id : Config::get('constants.DEFAULT.MODE_ID');

            $weight_slab = DB::table(config('tables.PRODUCT_WEIGHT_SLAB'))
                    ->where('min_grams', '<=', $product->weight)
                    ->where(function($max) use($product)
                    {
                        $max->whereNUll('max_grams')
                        ->orWhere(function($m) use($product)
                        {
                            $m->WhereNotNull('max_grams')
                            ->where('max_grams', '>=', $product->weight);
                        });
                    })
                    ->select('weight_slab_id', 'for_each_grams')
                    ->first();
            if ($weight_slab)
            {
                $supplierdetails->for_each_grams = $weight_slab->for_each_grams;
                $shippment_details = DB::table(config('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS').' as sh')
                        ->join(config('tables.COURIER_MODE_LOOKUPS').' as m', 'm.mode_id', '=', 'sh.mode_id')
                        ->where('sh.country_id', $product->user['country_id'])
                        ->where('sh.weight_slab_id', $weight_slab->weight_slab_id)
                        ->where('sh.currency_id', $product->currency_id)
                        ->where('sh.mode_id', $product->mode_id)
                        ->where('sh.logistic_id', $supplierdetails->logistic_id)
                        ->where(function($sc) use($product)
                        {
                            $sc->where('sh.supplier_id', $product->supplier_id)
                            ->orWhereNull('sh.supplier_id');
                        })
                        ->select('m.mode', 'sh.delivery_charge', 'sh.delivery_days', 'sh.zone_delivery_days', 'sh.zone_delivery_charges', 'sh.national_delivery_days', 'sh.national_delivery_charges')
                        ->first();
                if (!empty($supplierdetails) && !empty($shippment_details))
                {
                    $supplierdetails->user = $product->user;
                    $supplierdetails->shippment_details = $shippment_details;
                    $product->shipping_info = $supplierdetails;
                    $product->shipping_charge = null;
                    $product->mode = $shippment_details->mode;
                    if ($supplierdetails->postal_code == $product->user['postal_code'])
                    {
                        $product->shipping_charge = $product->qty * (($product->weight / $weight_slab->for_each_grams) * $shippment_details->delivery_charge);
                        $product->delivery_days = $shippment_details->delivery_days;
                    }
                    else if ($supplierdetails->region_id == $product->user['region_id'])
                    {
                        $product->shipping_charge = $product->qty * (($product->weight / $weight_slab->for_each_grams) * $shippment_details->zone_delivery_charges);
                        $product->delivery_days = $shippment_details->zone_delivery_days;
                    }
                    elseif ($supplierdetails->country_id == $product->user['country_id'])
                    {
                        $product->shipping_charge = $product->qty * (($product->weight / $weight_slab->for_each_grams) * $shippment_details->national_delivery_charges);
                        $product->delivery_days = $shippment_details->national_delivery_days;
                    }
                    if (isset($product->commission_info))
                    {
                        $product->commission_info->shipping_charge = $product->shipping_charge;
                    }
                    $product->shipping_info->shipping_charge = $product->shipping_charge;
                    if ($product->is_shipping_beared)
                    {
                        $product->shipping_charge = 0;
                    }
                }
            }
        }
    }


    public function productCommissions (&$product)
    {
        $product->commission_info = (object) [
                    'site_commission_unit'=>NULL,
                    'site_commission_value'=>0,
                    'site_commission_amount'=>0,
                    'site_commission_sub_total'=>0,
                    'partner_commission_amount'=>0,
                    'partner_commission_sub_total'=>0
        ];
        $commission = DB::table(config('tables.SUPPLIER_COMMISSIONS_SETTINGS'))
                ->where('supplier_id', $product->supplier_id)
                ->where(function($cv) use($product)
                {
                    $cv->where('commission_unit', config('constants.COMMISSION_UNIT.PERCENTAGE'))
                    ->orWhere(function($cvor) use($product)
                    {
                        $cvor->where('commission_unit', config('constants.COMMISSION_UNIT.FIXED_RATE'))
                        ->where('currency_id', $product->currency_id);
                    });
                })
                ->selectRaw('commission_type,commission_unit,commission_value')
                ->first();
        if ($commission)
        {
            if ($commission->commission_type == config('constants.COMMISSION_TYPE.FIXED'))
            {
                $product->commission_info->site_commission_unit = $commission->commission_unit;
                $product->commission_info->site_commission_value = (float) $commission->commission_value;
            }
            elseif ($commission->commission_type == config('constants.COMMISSION_TYPE.FLEXIBLE'))
            {
                $commission = DB::table(config('tables.SUPPLIER_FLEXIBLE_COMMISSIONS'))
                        ->where('relation_id', $product->product_id)
                        ->where('supplier_id', $product->supplier_id)
                        ->where('post_type_id', config('constants.POST_TYPE.PRODUCT'))
                        ->where(function($cv) use($product)
                        {
                            $cv->where('commission_unit', config('constants.COMMISSION_UNIT.PERCENTAGE'))
                            ->orWhere(function($cvor) use($product)
                            {
                                $cvor->where('commission_unit', config('constants.COMMISSION_UNIT.FIXED_RATE'))
                                ->where('currency_id', $product->currency_id);
                            });
                        })
                        ->selectRaw('commission_unit,commission_value')
                        ->first();
                if ($commission)
                {
                    $product->commission_info->site_commission_unit = $commission->commission_unit;
                    $product->commission_info->site_commission_value = (float) $commission->commission_value;
                }
                else
                {
                    $commission = $this->getCategoryCommission(['category_id'=>$product->category_id, 'supplier_id'=>$product->supplier_id, 'currency_id'=>$product->currency_id]);
                    if ($commission)
                    {
                        $product->commission_info->site_commission_unit = $commission->commission_unit;
                        $product->commission_info->site_commission_value = (float) $commission->commission_value;
                    }
                }
            }
        }
        $product->commission_info->site_commission_amount = (($product->commission_info->site_commission_unit == config('constants.COMMISSION_UNIT.PERCENTAGE')) ? (($product->commission_info->supplier_sold_price / 100) * $product->commission_info->site_commission_value) : $product->commission_info->site_commission_value);
        $product->commission_info->site_commission_sub_total = $product->qty * $product->commission_info->site_commission_amount;
        if (!empty($product->partner_id))
        {
            $product->commission_info->partner_commission_amount = (($product->commission_info->partner_commission_unit == config('constants.COMMISSION_UNIT.PERCENTAGE')) ? (($product->commission_info->site_commission_amount / 100) * $product->commission_info->partner_commission_value) : $product->commission_info->partner_commission_value);
            $product->commission_info->partner_commission_sub_total = $product->qty * $product->commission_info->partner_commission_amount;
        }
    }


    public function get_product_discounts (&$product, $with_sales_commission = false) /* Have to check  */
    {
        $discount_info = [];
        $discount_amt = ['site'=>(object) ['amount'=>0, 'percentage'=>0], 'supplier'=>(object) ['amount'=>0, 'percentage'=>0]];
        $commission_info = (object) [
                    'mrp_price'=>$product->mrp_price,
                    'supplier_price'=>$product->price,
                    'supplier_discount_per'=>0,
                    'supplier_sold_price'=>0,
                    'site_commission_unit'=>NULL,
                    'site_commission_value'=>0,
                    'site_commission_amount'=>0,
                    'site_margin_price'=>0,
                    'site_discount_per'=>0,
                    'site_sold_price'=>0,
                    'partner_margin_price'=>0,
                    'partner_sold_price'=>0,
                    'partner_commission_unit'=>NULL,
                    'partner_commission_value'=>0,
                    'partner_commission_amount'=>0,
                    'price_sub_total'=>0,
                    'site_commission_sub_total'=>0,
                    'shipping_fee'=>0,
                    'supplier_price_sub_total'=>0,
                    'collection_fee'=>0,
                    'fixed_fee'=>0,
                    'partner_margin_sub_total'=>0,
                    'supplier_tax_total'=>0,
                    'partner_tax_total'=>0,
                    'partner_commission_sub_total'=>0
        ];
        if (isset($product->supplier_product_id) && !empty($product->supplier_product_id) && isset($product->qty) && $product->qty > 0)
        {

            $current_date = date('Y-m-d');
            $discounts = DB::table(config('tables.DISCOUNTS').' as d')
                    ->join(config('tables.DISCOUNT_TYPE_LOOKUPS').' as dtl', 'dtl.discount_type_id', '=', 'd.discount_type_id')
                    ->leftJoin(config('tables.DISCOUNT_POSTS').' as dp', 'dp.discount_id', '=', 'd.discount_id')
                    ->leftJoin(config('tables.DISCOUNT_VALUE').' as dv', 'dv.dp_id', '=', 'dp.dp_id')
                    ->where('d.is_deleted', config('constants.OFF'))
                    ->where('dtl.status', config('constants.ACTIVE'))
                    ->where('d.status', config('constants.DISCOUNT_STATUS.PUBLISHED'))
                    ->where(DB::raw('date(d.start_date)'), '<=', $current_date)
                    ->where(DB::raw('date(d.end_date)'), '>=', $current_date)
                    ->groupby('dp.discount_id')
                    ->selectRaw('d.discount_id,d.discount,d.description,d.discount_by,dtl.discount_type,dp.discount_value_type,dv.discount_value,dv.currency_id')
                    ->where(function($value_type) use($product)
                    {
                        $value_type->where(function($per)
                        {
                            $per->where('dp.discount_value_type', config('constants.DISCOUNT_VALUE_TYPE.PERCENTAGE'))
                            ->whereNull('dv.currency_id');
                        })
                        ->orWhere(function($amount) use($product)
                        {
                            $amount->where('dp.discount_value_type', config('constants.DISCOUNT_VALUE_TYPE.FIXED_AMOUNT'))
                            ->where('dv.currency_id', $product->currency_id);
                        });
                    })
                    ->where('d.country_id', '==', $product->country_id)
                    ->where(function($subquery) use($product)
                    {
                        $subquery->where('dp.is_qty_based', config('constants.OFF'))
                        ->orWhere(function($qty_based) use($product)
                        {
                            $qty_based->where('dp.is_qty_based', config('constants.ON'))
                            ->where(function($qt)use($product)
                            {
                                $qt->where('dv.min_qty', '>=', $product->qty)
                                ->where(function($max_qty) use($product)
                                {
                                    $max_qty->where('dv.max_qty', '=', 0)
                                    ->orWhere(function($subquery2)use($product)
                                    {
                                        $subquery2->where('dv.max_qty', '>', 0)
                                        ->where('dv.max_qty', '<=', $product->qty);
                                    });
                                });
                            });
                        });
                    })
                    ->where(function($subquery2)use($product)
                    {
                        $subquery2->whereNull('dp.brand_ids')
                        ->where(function($subquery3) use($product)
                        {
                            $subquery3->whereNotNull('dp.brand_ids')
                            ->whereRaw('find_in_set('.$product->product_id.',dp.brand_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.category_ids')
                        ->where(function($subquery3)use($product)
                        {
                            $subquery3->whereNotNull('dp.category_ids')
                            ->whereRaw('find_in_set('.$product->category_id.',dp.category_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.supplier_ids')
                        ->where(function($subquery3)use($product)
                        {
                            $subquery3->whereNotNull('dp.supplier_ids')
                            ->whereRaw('find_in_set('.$product->supplier_id.',dp.supplier_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
            {
                $subquery2->whereNull('dp.product_ids')
                ->where(function($subquery3)use($product)
                {
                    $subquery3->whereNotNull('dp.product_ids')
                    ->whereRaw('find_in_set('.$product->product_id.',dp.product_ids)');
                });
            });
            if (!empty($product->product_cmb_id))
            {
                $discounts->where(function($subquery2) use($product)
                {
                    $subquery2->whereNull('dp.product_cmb_ids')
                            ->where(function($subquery3)use($product)
                            {
                                $subquery3->whereNotNull('dp.product_cmb_ids')
                                ->whereRaw('find_in_set('.$product->product_cmb_id.',dp.product_cmb_ids)');
                            });
                });
            }
            $discounts = $discounts->get();

            if (!empty($discounts))
            {
                array_walk($discounts, function(&$discount) use(&$product, &$discount_amt, &$discount_info)
                {
                    if ($discount->discount_by == config('constants.ACCOUNT_TYPE.ADMIN'))
                    {
                        if ($discount->discount_value_type == config('constants.DISCOUNT_VALUE_TYPE.FIXED_AMOUNT'))
                        {
                            $discount_amt['site']->amount+= $discount->discount_value;
                        }
                        else
                        {
                            $discount_amt['site']->percentage+= $discount->discount_value;
                        }
                    }
                    else
                    {
                        if ($discount->discount_value_type == config('constants.DISCOUNT_VALUE_TYPE.FIXED_AMOUNT'))
                        {
                            $discount_amt['supplier']->amount+= $discount->discount_value;
                        }
                        else
                        {
                            $discount_amt['supplier']->percentage+= $discount->discount_value;
                        }
                    }
                    $product->discounts[] = $discount->description;
                    $discount_info[] = $discount;
                });
            }

            $commission_info->supplier_price        = $product->price;
            $commission_info->supplier_sold_price   = $product->price - ($discount_amt['supplier']->amount + (($product->price / 100) * $discount_amt['supplier']->percentage));
            $commission_info->supplier_discount_per = $product->mrp_price > 0 ? round((($product->mrp_price - $commission_info->supplier_sold_price) / $product->mrp_price) * 100) : 0;
            $commission_info->site_sold_price = $commission_info->supplier_sold_price - ($discount_amt['site']->amount + (($commission_info->supplier_sold_price / 100) * $discount_amt['site']->percentage));
            $commission_info->site_discount_per = $commission_info->supplier_sold_price > 0 ? round((($commission_info->site_sold_price - $commission_info->supplier_sold_price) / $commission_info->supplier_sold_price) * 100) : 0;
            $product->price = $commission_info->site_sold_price;
           //$product->discount = $product->price > 0 ? round((($product->mrp_price - $product->price) / $product->mrp_price) * 100) : 0;
            $product->discount = $product->price > 0 ? round((($product->mrp_price - $product->price) / $product->mrp_price) * 100) : 0;
            $product->off_per =  $product->off_perc.'% '.trans('product_browse.off');
            $commission_info->price_sub_total = $product->price * $product->qty;
            $product->sub_total = $product->price * $product->qty;
            $commission_info->supplier_price_sub_total = $commission_info->supplier_sold_price * $product->qty;
            $commission_info->partner_margin_sub_total = $commission_info->partner_margin_price * $product->qty;
            if ($with_sales_commission)
            {
                $product->commission_info = $commission_info;
                $product->discount_info = $discount_info;
            }

        }
    }  
}