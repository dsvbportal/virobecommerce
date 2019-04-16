<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;
use Request;
use Response;
use App\Models\Commonsettings;
use App\Models\Affiliate\Payments;
use AppService;

class AffiliateReports extends BaseModel
{

    private $admincommonObj = '';

    function __construct ()
    {
        parent::__construct();
		 $this->commonObj = new Commonsettings();
		 $this->walletObj = new Wallet;		 
    }

    /*  Fast Start Bonus  */

    public function faststart_bonus_details ($account_id, $arr = array())
    {
        extract($arr);
        $refSql = DB::table(config('tables.REFERRAL_EARNINGS').' as re')
                ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast', 'ast.subscribe_topup_id', '=', 're.subscrib_topup_id')
                ->join(config('tables.AFF_PACKAGE_PRICING').' as pri', 'pri.package_id', '=', 'ast.package_id')
                ->join(config('tables.ACCOUNT_MST').' as fum', 'fum.account_id', '=', 're.from_account_id')
                ->join(config('tables.ACCOUNT_TREE').' as ut', 'ut.account_id', '=', 're.from_account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as fud', 'fud.account_id', '=', 're.from_account_id')
               // ->join(config('tables.ACCOUNT_MST').' as rfum', 'rfum.account_id', '=', 'ut.sponsor_id')
                ->join(config('tables.ACCOUNT_MST').' as racm', 'racm.account_id', '=', 'ut.nwroot_id')
                //->join(config('tables.ACCOUNT_DETAILS').' as rfud', 'rfud.account_id', '=', 'rfum.account_id')
               // ->join(config('tables.ACCOUNT_MST').' as tum', 'tum.account_id', '=', 're.to_account_id')
               // ->join(config('tables.ACCOUNT_DETAILS').' as tud', 'tud.account_id', '=', 'tum.account_id')
                ->join(config('tables.AFF_PACKAGE_MST').' as pm', 'pm.package_id', '=', 'ast.package_id')
                ->join(config('tables.AFF_PACKAGE_LANG').' as pl', 'pl.package_id', '=', 'pm.package_id')
                ->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 're.currency_id')
                ->join(config('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 're.payout_type')
                ->join(config('tables.WALLET_LANG').' as wal', 'wal.wallet_id', '=', 're.wallet_id')
                ->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'fum.status')
                ->where('re.to_account_id', $this->userSess->account_id);
        $refSql->select(DB::Raw("re.*,re.ref_id,re.payout_type,racm.uname as root_group,fum.account_id,re.created_date,re.qv,fum.uname as from_uname,fum.user_code as from_user_code,concat_ws(' ',fud.firstname, fud.lastname) as full_name,pl.package_name,re.amount,IF(re.payout_type=1,(select `wallet` from ".config('tables.WALLET_LANG')." as wal where `wal`.`wallet_id` = re.wallet_id),(select `payment_type` from ".config('tables.PAYMENT_TYPES')." as pt where `pt`.`payment_type_id` = re.payout_type)) as pay_mode,(select uname from ".config('tables.ACCOUNT_MST')." where account_id = (select upline_id from ".config('tables.ACCOUNT_TREE')." where account_id = re.from_account_id )) as upline_username,cur.decimal_places,cur.currency as currency,cur.currency_symbol,re.status,pri.price as packagepricing,usl.status_name,re.earnings_qv as earnings,re.commission,re.service_tax as tax,re.ngo_wallet_amt,re.net_pay"));
        if (isset($from_date) && isset($to_date) && !empty($from_date) && !empty($to_date))
        {
            $refSql->whereDate('re.created_date', '>=', getGTZ($from_date, 'Y-m-d'));
            $refSql->whereDate('re.created_date', '<=', getGTZ($to, 'Y-m-d'));
        }
        else if (!empty($from_date) && isset($from_date))
        {
            $refSql->whereDate('re.created_date', '<=', getGTZ($from_date, 'Y-m-d'));
        }
        else if (!empty($to_date) && isset($to_date))
        {
            $refSql->whereDate('re.created_date', '>=', getGTZ($to_date, 'Y-m-d'));
        }
        if (isset($type_of_package) && !empty($type_of_package))
        {
            $refSql->where("pl.package_id", $type_of_package);
        }

        if (isset($search_term) && !empty($search_term))
        {
            if (!empty($filterchk) && !empty($filterchk))
            {
                $search_term = '%'.$search_term.'%';
                $search_field = ['FromUser'=>'fum.uname', 'Referral'=>'rfum.uname'];
                $refSql->where(function($sub) use($filterchk, $search_term, $search_field)
                {
                    foreach ($filterchk as $search)
                    {
                        if (array_key_exists($search, $search_field))
                        {
                            $sub->orWhere(DB::raw($search_field[$search]), 'like', $search_term);
                        }
                    }
                });
            }
            else
            {
                $refSql->where(function($wcond) use($search_term)
                {
                    $wcond->whereRaw("concat_ws('',tud.first_name,tud.last_name) like '%$search_term%'")
                            ->orWhereRaw("concat_ws('',rfud.first_name,rfud.last_name) like '%$search_term%'")
                            ->orWhereRaw("rfum.uname like '%$search_term%'")
                            ->orWhereRaw("fum.uname like '%$search_term%'")
                            ->orWhereRaw("tum.uname like '%$search_term%'");
                });
            }
        }
        $refSql->orderBy('re.created_date', 'desc');
        if (isset($length) && !empty($length))
        {
            $refSql->skip($start)->take($length);
        }

        if (isset($count) && !empty($count))
        {
            return $refSql->count();
        }
        else
        {
            $result = $refSql->get();

            if (!empty($result))
            {
                $status_type_arr = ['0'=>'warning', '1'=>'success', '2'=>'danger', '3'=>'info'];
                array_walk($result, function(&$ftdata) use($status_type_arr)
                {
                    $ftdata->Famount = $ftdata->currency_symbol.' '.number_format($ftdata->amount, \AppService::decimal_places($ftdata->amount), '.', ',').' '.$ftdata->currency;
                    $ftdata->commission = \CommonLib::currency_format($ftdata->commission, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
                    $ftdata->tax = \CommonLib::currency_format($ftdata->tax, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
                    $ftdata->ngo_wallet_amt = \CommonLib::currency_format($ftdata->ngo_wallet_amt, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
                    $ftdata->net_pay = \CommonLib::currency_format($ftdata->net_pay, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
                    $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                    //$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
                });
                return $result;
            }
            else
                return false;
        }
    }

    /* Team Bonus */

    public function get_teambonus_list ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if (!empty($account_id))
        {
			/*$subQry = DB::table(config('tables.AF_BINARY_BONUS'))
						->where('account_id', '=', $account_id)
                        ->where('type', '=', config('constants.BONUS.TYPE1'))						
						->where('is_deleted', '=', config('constants.OFF'))
						->orderby('date_for','DESC')
						->select('leftcarryfwd','rightcarryfwd','date_for');*/
						
	
            $query = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
					->leftjoin(config('tables.AF_BINARY_BONUS').' as bbl', function($subquery) use($account_id){                        
                        $subquery->on('bbl.account_id','=','bb.account_id')
						         ->on('bbl.bid','=','bb.last_bid');						
                    })
                    ->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'bb.currency_id')
                    ->select('bb.bid', 'bb.clubpoint', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.decimal_places', 'cur.currency as currency_code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income','bb.capping','bb.earnings')
						
					/*->select('bb.bid', 'bb.leftbinpnt', 'bb.rightbinpnt', 'bb.leftclubpoint', 'bb.rightclubpoint', 'bb.clubpoint', 'bb.totleftbinpnt', 'bb.totrightbinpnt', DB::Raw('IF(bb.last_leftcarryfwd IS NOT NULL,bbl.last_leftcarryfwd,0) as last_leftcarryfwd'), DB::Raw('IF(bb.last_rightcarryfwd IS NOT NULL,bbl.last_rightcarryfwd,0) as last_rightcarryfwd'),'bb.leftcarryfwd', 'bb.rightcarryfwd', 'bb.flushamt', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.decimal_places', 'cur.currency as code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income','bb.capping','bb.earnings')*/
					->where('bb.account_id', '=', $account_id)
					->where('bb.status', '=',  config('constants.STATUS_CONFIRMED'))
                    ->where('bb.bonus_type', '=', config('constants.BONUS_TYPE.TEAM_BONUS'))
					->where('bb.is_deleted', '=', config('constants.OFF'));
					 
            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $query->whereDate('bb.from_date', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('bb.to_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {
                $query->whereDate('bb.from_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('bb.from_date', '>=', getGTZ($to, 'Y-m-d'));
            }
			if(isset($id) && !empty($id)){
				 $query->where('bb.b_id',$id);
				 $result = $query->first();
			}
            if ($count)
            {
                return $query->count();
            }
            else
            {
                if (isset($length) && !empty($length))
                {
                    $query->skip($start)->take($length);
                }
                $query = $query->orderBy('bb.account_id', 'ASC');
                $result = $query->get();


                if (!empty($result))
                {
                    array_walk($result, function(&$ftdata)  
                    {						
                      /*  $ftdata->leftbinpnt = number_format($ftdata->leftbinpnt, \AppService::decimal_places($ftdata->leftbinpnt), '.', ',');
                        $ftdata->rightbinpnt = number_format($ftdata->rightbinpnt, \AppService::decimal_places($ftdata->rightbinpnt), '.', ',');
                        $ftdata->leftclubpoint = number_format($ftdata->leftclubpoint, \AppService::decimal_places($ftdata->leftclubpoint), '.', ',');
                        $ftdata->rightclubpoint = number_format($ftdata->rightclubpoint, \AppService::decimal_places($ftdata->rightclubpoint), '.', ',');
                        $ftdata->totleftbinpnt = number_format($ftdata->totleftbinpnt, \AppService::decimal_places($ftdata->totleftbinpnt), '.', ',');
                        $ftdata->totrightbinpnt = number_format($ftdata->totrightbinpnt, \AppService::decimal_places($ftdata->totrightbinpnt), '.', ',');
						*/
						
                         $ftdata->Fpaidamt = \CommonLib::currency_format($ftdata->paidinc, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);;
						$ftdata->income = \CommonLib::currency_format($ftdata->income, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
						$ftdata->tax = \CommonLib::currency_format($ftdata->tax, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
						$ftdata->ngo_wallet_amt = \CommonLib::currency_format($ftdata->ngo_wallet_amt, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
						$ftdata->paidinc = \CommonLib::currency_format($ftdata->paidinc, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
                        /*$ftdata->leftcarryfwd 	= number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
                        $ftdata->rightcarryfwd  = number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));*/
                        $ftdata->capping 	= number_format($ftdata->capping, \AppService::decimal_places($ftdata->capping), '.', ',');
                        $ftdata->earnings 		= number_format($ftdata->earnings, \AppService::decimal_places($ftdata->earnings), '.', ',');
						//$ftdata->date_for  		= date('d-M',strtotime($ftdata->from_date)).' - '.date('d-M',strtotime( $ftdata->to_date)).','.date('Y',strtotime( $ftdata->to_date));
						$ftdata->date_for  		= showUTZ($ftdata->from_date,'M d').' - '.showUTZ($ftdata->to_date,'d').','.showUTZ($ftdata->to_date,'Y');
                        $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                        if ($ftdata->status == 0)
                        {
                            $ftdata->status = config('constants.BONUS.PENDING');
                        }
                        if ($ftdata->status == 1)
                        {
                            $ftdata->status = config('constants.BONUS.CONFIRM');
                        }
                        if ($ftdata->confirmed_date == '')
                        {
                            $ftdata->confirmed_date = ' ';
                        }
                        else
                        {
                            $ftdata->confirmed_date = showUTZ($ftdata->confirmed_date, 'M, Y');
                        }
                        if ($ftdata->created_date == '')
                        {
                            $ftdata->created_date = ' ';
                        }
                        else
                        {
                            $ftdata->created_date = showUTZ($ftdata->created_date, 'M, Y');
                        }
                    });

                    return !empty($result) ? $result : NULL;
                }
            }
        }
        return NULL;
    }
  /*Leadership Bonus */
  
    public function get_leadership_bonus ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if (!empty($account_id))
        {
			$subQry = DB::table(config('tables.AF_BINARY_BONUS'))
						->where('account_id', '=', $account_id)
                        ->where('bonus_type', '=', config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))						
						->where('is_deleted', '=', config('constants.OFF'))
						->orderby('date_for','DESC')
						->select('leftcarryfwd','rightcarryfwd','date_for');
	
            $query = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
					->leftjoin(config('tables.AF_BINARY_BONUS').' as bbl', function($subquery) use($account_id){                        
                        $subquery->on('bbl.account_id','=','bb.account_id')
						         ->on('bbl.bid','=','bb.last_bid');						
                    })
                    ->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'bb.currency_id')
                     ->select('bb.bid', 'bb.clubpoint', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.decimal_places', 'cur.currency as currency_code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income','bb.capping','bb.earnings')
						
					/*->select('bb.bid', 'bb.leftbinpnt', 'bb.rightbinpnt', 'bb.leftclubpoint', 'bb.rightclubpoint', 'bb.clubpoint', 'bb.totleftbinpnt', 'bb.totrightbinpnt', DB::Raw('IF(bb.last_leftcarryfwd IS NOT NULL,bbl.last_leftcarryfwd,0) as last_leftcarryfwd'), DB::Raw('IF(bb.last_rightcarryfwd IS NOT NULL,bbl.last_rightcarryfwd,0) as last_rightcarryfwd'),'bb.leftcarryfwd', 'bb.rightcarryfwd', 'bb.flushamt', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.decimal_places', 'cur.currency as code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income','bb.capping','bb.earnings')*/
					->where('bb.account_id', '=', $account_id)
					->where('bb.status', '=',  config('constants.STATUS_CONFIRMED'))
                    ->where('bb.bonus_type', '=', config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
					->where('bb.is_deleted', '=', config('constants.OFF'));
					 
            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $query->whereDate('bb.from_date', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('bb.to_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {
                $query->whereDate('bb.from_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('bb.from_date', '>=', getGTZ($to, 'Y-m-d'));
            }
			if(isset($id) && !empty($id)){
				 $query->where('bb.b_id',$id);
				 $result = $query->first();
			}
            if ($count)
            {
                return $query->count();
            }
            else
            {
                if (isset($length) && !empty($length))
                {
                    $query->skip($start)->take($length);
                }
                $query = $query->orderBy('bb.account_id', 'ASC');
                $result = $query->get();


                if (!empty($result))
                {
                    array_walk($result, function(&$ftdata)  
                    {						
                       /* $ftdata->leftbinpnt = number_format($ftdata->leftbinpnt, \AppService::decimal_places($ftdata->leftbinpnt), '.', ',');
                        $ftdata->rightbinpnt = number_format($ftdata->rightbinpnt, \AppService::decimal_places($ftdata->rightbinpnt), '.', ',');
                        $ftdata->leftclubpoint = number_format($ftdata->leftclubpoint, \AppService::decimal_places($ftdata->leftclubpoint), '.', ',');
                        $ftdata->rightclubpoint = number_format($ftdata->rightclubpoint, \AppService::decimal_places($ftdata->rightclubpoint), '.', ',');
                        $ftdata->totleftbinpnt = number_format($ftdata->totleftbinpnt, \AppService::decimal_places($ftdata->totleftbinpnt), '.', ',');
                        $ftdata->totrightbinpnt = number_format($ftdata->totrightbinpnt, \AppService::decimal_places($ftdata->totrightbinpnt), '.', ',');
						*/
						
						$ftdata->income = \CommonLib::currency_format($ftdata->income, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
						
						$ftdata->tax = \CommonLib::currency_format($ftdata->tax, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
						
						$ftdata->ngo_wallet_amt = \CommonLib::currency_format($ftdata->ngo_wallet_amt, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);
						
						$ftdata->paidinc = \CommonLib::currency_format($ftdata->paidinc, ['currency_symbol'=>$ftdata->currency_symbol, 'currency_code'=>$ftdata->currency_code, 'value_type'=>(''), 'decimal_places'=>$ftdata->decimal_places]);						
						
                       /* $ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',').' '.$ftdata->code;
						$ftdata->income = $ftdata->currency_symbol.' '.number_format($ftdata->income, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->tax = $ftdata->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->ngo_wallet_amt = $ftdata->currency_symbol.' '.number_format($ftdata->ngo_wallet_amt, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->paidinc = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',');*/
                       // $ftdata->leftcarryfwd 	= number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
                       // $ftdata->rightcarryfwd  = number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
                        $ftdata->capping 	= number_format($ftdata->capping, \AppService::decimal_places($ftdata->capping), '.', ',');
                        $ftdata->earnings 		= number_format($ftdata->earnings, \AppService::decimal_places($ftdata->earnings), '.', ',');
						//$ftdata->date_for  		= date('d-M',strtotime($ftdata->from_date)).' - '.date('d-M',strtotime( $ftdata->to_date)).','.date('Y',strtotime( $ftdata->to_date));
						$ftdata->date_for  		= showUTZ($ftdata->from_date,'M-Y');
                        $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                        if ($ftdata->status == 0)
                        {
                            $ftdata->status = config('constants.BONUS.PENDING');
                        }
                        if ($ftdata->status == 1)
                        {
                            $ftdata->status = config('constants.BONUS.CONFIRM');
                        }
                        if ($ftdata->confirmed_date == '')
                        {
                            $ftdata->confirmed_date = ' ';
                        }
                        else
                        {
                            $ftdata->confirmed_date = showUTZ($ftdata->confirmed_date, 'M, Y');
                        }
                        if ($ftdata->created_date == '')
                        {
                            $ftdata->created_date = ' ';
                        }
                        else
                        {
                            $ftdata->created_date = showUTZ($ftdata->created_date, 'M, Y');
                        }
                    });

                    return !empty($result) ? $result : NULL;
                }
            }
        }
        return NULL;
    }
  
  
  
  
  
  
  
	
    /* Car Bonus & Star Bonus */
    public function car_bonus ($arr = array(), $count = false) 
    {   
        extract($arr);
        if (!empty($account_id))
        {
            $query = DB::table($this->config->get('tables.AFF_DIRECTORS_BONUS').' as adb')
			        ->join($this->config->get('tables.ACCOUNT_TREE').' as ap', 'ap.account_id', '=', 'adb.account_id')
			        ->join($this->config->get('tables.AFF_RANKING_LOOKUPS').' as arl', function($sub){
						$sub->on('arl.af_rank_id', '=', 'ap.rank')
						->where('arl.lang_id', '=', $this->config->get('app.locale_id'));
					})					
			        ->join($this->config->get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'adb.wallet_id')
                    ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'adb.currency_id')       
					->where('adb.is_deleted', '=', $this->config->get('constants.OFF'))
					->where('adb.account_id', '=', $account_id);  
			if (isset($bonus_type) && !empty($bonus_type))
            { 
                $query->where('adb.bonus_type', '=', $bonus_type);		
            }		
            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {  
                $query->whereDate('adb.bonus_date', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('adb.bonus_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {  
                $query->whereDate('adb.bonus_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('adb.bonus_date', '>=', getGTZ($to, 'Y-m-d'));
            }
            if ($count)
            {	
                return $query->count();
            }
            else
            {  
                if (isset($length) && !empty($length))
                {
                    $query->skip($start)->take($length);
                }
				$result = $query->select('adb.commission', 'adb.tax', 'adb.vi_help','adb.net_pay','adb.status','adb.bonus_date','arl.rank','cur.currency_symbol', 'cur.currency as code','cur.decimal_places')
                               ->orderBy('adb.id', 'DESC')
							   ->get();      
							   
                if (!empty($result))
                {
                    array_walk($result, function(&$res)
                    {
						$res->commission = \CommonLib::currency_format($res->commission, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->tax = \CommonLib::currency_format($res->tax, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->vi_help = \CommonLib::currency_format($res->vi_help, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->net_pay = \CommonLib::currency_format($res->net_pay, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->bonus_date = !empty($res->bonus_date) ? showUTZ($res->bonus_date):'';
						$res->status_class = !empty($res->status) ? $this->config->get('dispclass.bonus_status.'.$res->status):'';
						$res->status = !empty($res->status) ? trans('general.bonus_status.'.$res->status):'';
                    });
                    return !empty($result) ? $result : NULL;
                }
            }
        }
        return NULL;
    }

    /* Personal Commission */

    public function personal_commission ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if (!empty($account_id))
        {
            $query = DB::table(config('tables.CUSTOMER_COMMISSION_MONTHLY').' as pm')
					->where('type',config('constants.CUSTOMER_BONUS_TYPE.PERSONAL'))
                    ->select('pm.account_id', 'pm.confirm_date', 'pm.slab', 'pm.total_cv', 'pm.earnings', 'pm.commission', 'pm.tax', 'pm.ngo_wallet', 'pm.net_pay', 'pm.status','pm.direct_cv','pm.self_cv');

            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $query->whereDate('pm.confirm_date', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('pm.confirm_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {

                $query->whereDate('pm.confirm_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('pm.confirm_date', '>=', getGTZ($to, 'Y-m-d'));

            }
			if (isset($length) && !empty($length))
			{
				$query->skip($start)->take($length);
			}
			$query = $query->orderBy('pm.account_id', 'ASC');
			if(isset($count) && !empty($count)){
				$result = $query->count();
				return $result;
			}
			$result = $query->get();
			if(!empty($result))
			{
				$serial_no = 1;
				$currency_info = $this->commonObj->get_currency($this->userSess->currency_id);
				/* if(!empty($data['gusers'])){
					array_walk($data['gusers'],function($k)use($currency_info){
						$k->bill_amount = $currency_info->currency_symbol.' '.$k->bill_amount;
					});
				} */
				array_walk($result, function(&$ftdata) use($serial_no,$currency_info)
				{

					$ftdata->serial_no = $serial_no;
					$ftdata->commission = $currency_info->currency_symbol.' '.number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission));
					/* $ftdata->direct_cv 	  = number_format($ftdata->direct_cv, \AppService::decimal_places($ftdata->direct_cv));
					$ftdata->self_cv 	  = number_format($ftdata->self_cv, \AppService::decimal_places($ftdata->self_cv)); 
					$ftdata->total_cv 	  = number_format($ftdata->total_cv, \AppService::decimal_places($ftdata->total_cv));  */
					$ftdata->tax = $currency_info->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax));
					$ftdata->ngo_wallet = $currency_info->currency_symbol.' '.number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet));
					$ftdata->net_pay = $currency_info->currency_symbol.' '.number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay));
					$ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
					if ($ftdata->status == 1)
					{
						$ftdata->status = 'Confirm';
					}
					if ($ftdata->confirm_date == '')
					{
						$ftdata->confirm_date = ' ';
					}
					else
					{
						$ftdata->confirm_date = showUTZ($ftdata->confirm_date, 'M-Y');
					}
				});
				return !empty($result) ? $result : NULL;
			}
        }
        
        return NULL;
    }

    

    public function get_currency_exchange ($from_currency_id, $to_currency_id)
    {
        $result = '';
        $result = DB::table(Config('tables.CURRENCY_EXCHANGE_SETTINGS'))
                ->select('rate')
                ->where(array('from_currency_id'=>$from_currency_id, 'to_currency_id'=>$to_currency_id))
                ->first();
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        return false;
    }

    /* Leadership Save */

    public function leadership_bonus_commission ($account_id, $currency_id,$country_id,$instant_credit = true)
    {
        $g1_sale = 0;
        $g2_sale = 0;
        $g3_sale = 0;
		//print_R(date('Y-m-1',strtotime('-1 month')));exit;
        $user_count = DB::table(config('tables.ACCOUNT_TREE').' as at')
                ->where('at.upline_id', $account_id)
                ->where('is_deleted', config('constants.NOT_DELETED'))
                ->whereIn('rank', [config('constants.TEAM_GENARATION.1G'), config('constants.TEAM_GENARATION.2G'), config('constants.TEAM_GENARATION.3G')])
                ->orderBy('rank', 'ASC')
                ->selectRaw(DB::raw("account_id,nwroot_id,lft_node,rgt_node"))
                ->get();

		$op['msg'] = 'faild';
        if (count($user_count) == 3)
        {
			
            foreach ($user_count as $key=> $user_info)
            {
				$start_date = date('Y-m-01',strtotime('-1 month'));
				$end_date = date('Y-m-t',strtotime('-1 month'));
		        $pkg_qv = DB::table(config('tables.ACCOUNT_TREE').' as at')
                        ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast','ast.account_id', '=', 'at.account_id')
						->whereDate('ast.confirm_date','>=',$start_date)
						->whereDate('ast.confirm_date','<=',$end_date)
						->where('ast.status','=',config('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'))
						->where('ast.payment_status','=',config('constants.PAYMENT_PAID'))
                        ->whereBetween('at.lft_node', [$user_info->lft_node, $user_info->rgt_node])
                        ->where('at.nwroot_id', $user_info->nwroot_id)
                        ->select(DB::RAW('sum(ast.package_qv) as month_sale_qv'))
                        ->first();
		        if (!empty($pkg_qv))
                {
                    ${'g'.($key + 1).'_sale'} = $pkg_qv->month_sale_qv;
                }
				
            }
			
			$bonus_perc = DB::table(config('tables.AFF_BONUS_CV_PERC').' as p')
							->join(config('tables.AFF_BONUS_TYPES').' as t','t.bonus_type_id','=','p.bonus_type')
							->where('t.bonus_type_id', config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
							->select('p.perc','t.credit_wallet_id','p.ngo_wallet_perc')->first();	
							
            $leftbinpnt = $g1_sale + $g2_sale;
            $rightbinpnt = $g3_sale;
            $totleftpinpnt = $totrightpinpnt = 0;
            $leftcryfwd = $rgtcryfwd = 0;
            $binary_bonus = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
                    ->where('bb.account_id', $account_id)
                    ->orderBy('bid', 'Desc')
                    ->selectRaw(DB::raw("bid,account_id,leftcarryfwd,rightcarryfwd,totleftbinpnt,totrightbinpnt"))
                    ->first();
			$capping = 0;		
            if (!empty($binary_bonus))
            {
                $totleftbinpnt = $binary_bonus->totleftbinpnt + $leftbinpnt;
                $totrightbinpnt = $binary_bonus->totrightbinpnt + $rightbinpnt;
                $leftcryfwd = $binary_bonus->leftcarryfwd;
                $rgtcryfwd = $binary_bonus->rightcarryfwd;
                $handleamt = 0;
                $leftclubpoint = $leftbinpnt + $leftcryfwd;
                $rightclubpoint = $rightbinpnt + $rgtcryfwd;
                if ($leftclubpoint > $rightclubpoint)
                {
                    $cluppnt = $rightclubpoint;
                }
                else if ($rightclubpoint > $leftclubpoint)
                {
                    $cluppnt = $leftclubpoint;
                }
                $bonus_qv = $cluppnt * $bonus_perc->perc/100;
                $current_rate = 1;
                if (config('constants.DEFAULT_CURRENCY_ID') != $currency_id)
                {
                   $rate_setting = DB::table(config('tables.SETTINGS'))
								->where('setting_key',config('constants.QV_CURRENCY_RATE'))
								->value('setting_value');
					$rate 			= json_decode(stripslashes($rate_setting));
		            $current_rate 	= $rate->$currency_id;
                }
                $bonus_amt 		= $bonus_qv * $current_rate;
                $ngo_wallet_amt = $bonus_amt * $bonus_perc->ngo_wallet_perc/100;
                $capping_qry = DB::table(config('tables.ACCOUNT_TREE').' as at')
                        ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as astt', 'astt.account_id', "=", 'at.account_id')
                        ->where('at.account_id', $account_id)
                        ->where('at.is_deleted', config('constants.OFF'))
                        ->selectRaw(DB::raw('sum(astt.weekly_capping_qv) as capping_qv'))
                        ->first();
				$capping = $capping_qry->capping_qv;
            }
            else
            {
                $totleftbinpnt = 0;
                $totrightbinpnt = 0;
                $leftcryfwd = 0;
                $rgtcryfwd = 0;
                $leftclubpoint = 0;
                $rightclubpoint = 0;
                $cluppnt = 0;
                $bonus_qv = 0;
                $bonus_amt = 0;
                $paid_amt = 0;
                $flushamt = 0;
                $ngo_wallet_amt = 0;
            }
			$bonusInfo = $this->getBonusSetting($this->config->get('constants.BONUS_TYPE.LEADERSHIP_BONUS'));
			
			if($bonusInfo->has_tax){
				list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json)= $this->getTax(['account_id'=>$account_id,'amount'=>$bonus_amt,'country_id'=>$country_id,'statementline_id'=>$this->config->get('stline.FAST_START_BONUS.CREDIT')]);	
				
				$refData['service_tax_details'] = $tax_json;
				$refData['service_tax_per'] 	= $tot_tax_perc;
				$refData['service_tax'] 		= $tot_tax;	
				$refData['tax_class_id'] 		= $tax_class_id;				
			}	
			$taxAmt = number_format($tot_tax,2,'.','');		
            $current_date2 = getGtz();
            $form_date = date('Y-m-d H:i:s', strtotime('-7 days', strtotime($current_date2))).'<br>';
            $to_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime($current_date2)));
            $sdata['account_id'] = $account_id;
            //$sdata['package_id']=$upline_info->package_id;
            $sdata['bonus_value'] = $bonus_qv;
            $sdata['bonus_value_in'] = 0;
            $sdata['bonus_type'] 	 = config('constants.BONUS_TYPE.LEADERSHIP_BONUS');
            $sdata['type'] 			 = 2;
            $sdata['leftbinpnt']     = !empty($leftbinpnt)?$leftbinpnt:0;
            $sdata['rightbinpnt']    = !empty($rightbinpnt)?$rightbinpnt:0;
            $sdata['leftclubpoint']  = $leftclubpoint;
            $sdata['rightclubpoint'] = $rightclubpoint;
            $sdata['clubpoint'] 	 = $cluppnt;
            $sdata['totleftbinpnt']  = $totleftbinpnt;
            $sdata['totrightbinpnt'] = $totrightbinpnt;
            $sdata['leftcarryfwd'] 	 = $leftcryfwd;
            $sdata['rightcarryfwd']  = $rgtcryfwd;
            $sdata['income'] 		 = $bonus_amt;
			$sdata['currency_id'] 		= $currency_id;
            $sdata['tax'] 		 	 = $taxAmt;
            $sdata['ngo_wallet_amt'] = $ngo_wallet_amt;
            $sdata['paidinc'] 		 = $bonus_amt-($ngo_wallet_amt+$taxAmt);
            $sdata['wallet_id']      = $bonus_perc->credit_wallet_id;
            $sdata['status'] 	     = (!empty($instant_credit))?1:config('constants.BONUS.STATUS_PENDING');
            $sdata['from_date']      = $form_date;
            $sdata['to_date']	     = $to_date;
            $sdata['date_for'] 	     = $end_date;
            $sdata['created_date']   = getGTZ();
			$earnings 				 = 0;
			if($cluppnt >= $capping){
				$earnings = $capping;
			}else{
				$earnings = $cluppnt;
			}
			if($leftclubpoint >= $earnings){
				$sdata['left_flushout'] = ($leftclubpoint -$earnings);
			}else{
				$sdata['left_flushout'] = ($earnings - $leftclubpoint);
			}
			if($rightclubpoint >= $earnings){
				$sdata['right_flushout'] = ($rightclubpoint -$earnings);
			}else{
				$sdata['right_flushout'] = ($earnings - $rightclubpoint);
			}
	        $sdata['capping'] 			= $capping;
			$sdata['earnings']			= $earnings;
			
            if($bid = DB::table(config('tables.AF_BINARY_BONUS'))
                    ->insertGetId($sdata)){
				if(isset($instant_credit) && !empty(isset($instant_credit))){
					$cdata['bid'] = $bid;
					$cdata['wallet'] = $bonus_perc->credit_wallet_id;
					$cdata['account_id'] = $account_id;
					$cdata['amount'] = $bonus_amt;
					$cdata['currency_id'] = $currency_id;
					$cdata['tax'] = $taxAmt;
					$cdata['netpay'] = $sdata['paidinc'];
					$cdata['ngoAmt'] = $ngo_wallet_amt;
					$cdata['remark'] = 'for # Leadership bonus credit';
					$cdata['statementline_id'] = config('stline.LEADERSHIP-BONUS-CREDIT');
					$this->credit_bonus($cdata);	
				}
			}
			$op['msg'] = 'success';
        }
		return $op;
    }
	public function getBonusSetting($bonus_type,$list=false){
		if($bonus_type){
			$qry = DB::table($this->config->get('tables.AFF_BONUS_TYPES').' as bt')
						->join($this->config->get('tables.AFF_BONUS_CV_PERC') . ' as btc','btc.bonus_type','=','bt.bonus_type_id')
						->where('bt.bonus_type_id','=',$bonus_type)
						->select('bt.bonus_type_id','bt.bonus_name','bt.credit_wallet_id','bt.has_tax','bt.tax_class_id','btc.min_cv','btc.max_cv','btc.perc','btc.ngo_wallet_perc');			
			if($list==false){
				return $qry->first();
			} else {
				return $qry->get();
			}
		}
		return false;
	}
	
	
	
	public function ambassador_commission ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if (!empty($account_id))
        {
	        $query = DB::table(config('tables.CUSTOMER_COMMISSION_MONTHLY').' as pm')
                    ->select('pm.account_id', 'pm.confirm_date','pm.slab', 'pm.total_cv', 'pm.earnings', 'pm.commission', 'pm.tax', 'pm.ngo_wallet', 'pm.net_pay', 'pm.status','pm.created_on as created_date','pm.direct_cv','pm.self_cv','pm.total_earnings','pm.team_earnings')
				   ->where('pm.type',config('constants.CUSTOMER_BONUS_TYPE.AMBSSADOR'));

            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $query->whereDate('pm.confirm_date', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('pm.confirm_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {

                $query->whereDate('pm.confirm_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('pm.confirm_date', '>=', getGTZ($to, 'Y-m-d'));
            }
            if ($count)
            {
                return $query->count();
            }
            else
            {
                if (isset($length) && !empty($length))
                {
                    $query->skip($start)->take($length);
                }
                $query = $query->orderBy('pm.account_id', 'ASC');
                $result = $query->get();
			
                if (!empty($result))
                {
                    $serial_no = 1;
					$currency_info = $this->commonObj->get_currency($this->userSess->currency_id);
                    array_walk($result, function(&$ftdata) use($serial_no,$currency_info)
                    {

                        $ftdata->serial_no = $serial_no;
                        $ftdata->total_cv =number_format($ftdata->total_cv, \AppService::decimal_places($ftdata->total_cv));
                        $ftdata->self_cv =number_format($ftdata->self_cv, \AppService::decimal_places($ftdata->self_cv));
                        $ftdata->total_earnings =number_format($ftdata->total_earnings, \AppService::decimal_places($ftdata->total_earnings));
                        $ftdata->team_earnings =number_format($ftdata->team_earnings, \AppService::decimal_places($ftdata->team_earnings));
                        $ftdata->earnings =number_format($ftdata->earnings, \AppService::decimal_places($ftdata->earnings));
					    $ftdata->commission = $currency_info->currency_symbol.' '.number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission));
                        $ftdata->tax = $currency_info->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax));
                        $ftdata->ngo_wallet = $currency_info->currency_symbol.' '.number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet));
                        $ftdata->net_pay = $currency_info->currency_symbol.' '.number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay));
                        $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                        if ($ftdata->status == 1)
                        {
                            $ftdata->status = 'Confirm';
                        }
                        if ($ftdata->confirm_date == '')
                        {
                            $ftdata->confirm_date = ' ';
                        }
                        else
                        {
                            $ftdata->confirm_date = showUTZ($ftdata->confirm_date, 'M-Y');
                        }
		            });
                    return !empty($result) ? $result : NULL;
                }
            }
        }
        return NULL;
    }
	
	public function get_monthly_bonus_details($arr){
		extract($arr);
        if (!empty($account_id))
        {
	        $query = DB::table(config('tables.CUSTOMER_COMMISSION_MONTHLY').' as pm')
                    ->select('pm.account_id', 'pm.confirm_date','pm.slab', 'pm.total_cv', 'pm.earnings', 'pm.commission', 'pm.tax', 'pm.ngo_wallet', 'pm.net_pay', 'pm.status','pm.created_on as created_date')
					->where('pm.type',config('constants.CUSTOMER_BONUS_TYPE.AMBSSADOR'));
		   if (!empty($date) && isset($date))
            {

                $query->whereDate('pm.confirm_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            
                if (isset($length) && !empty($length))
                {
                    $query->skip($start)->take($length);
                }
                $query = $query->orderBy('pm.account_id', 'ASC');
                $result = $query->get();
			
                if (!empty($result))
                {
                    $serial_no = 1;
                    array_walk($result, function(&$ftdata) use($serial_no)
                    {

                        $ftdata->serial_no = $serial_no;
                        $ftdata->commission = number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission));
                        $ftdata->tax = number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax));
                        $ftdata->ngo_wallet = number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet));
                        $ftdata->net_pay = number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay));
                        $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                        if ($ftdata->status == 1)
                        {
                            $ftdata->status = 'Confirm';
                        }
                        if ($ftdata->confirm_date == '')
                        {
                            $ftdata->confirm_date = ' ';
                        }
                        else
                        {
                            $ftdata->confirm_date = showUTZ($ftdata->confirm_date, 'M-Y');
                        }
		            });
                    return !empty($result) ? $result : NULL;
                }
        }
	}
	
	
	public function getUser_bonus_Info ($params = array())
    {
        extract($params);

			if (!empty($params))
			{			
				$qry =	DB::table($this->config->get('tables.CUSTOMER_COMMISSION_MONTHLY').' as cs')
						->join($this->config->get('tables.ACCOUNT_MST').' as acm','acm.account_id','=','cs.account_id')
						->join($this->config->get('tables.ACCOUNT_TREE').' as act', 'act.account_id', '=', 'acm.account_id')
						->where('cs.account_id', '=', $account_id)
						->where('cs.type', '=',config('constants.CUSTOMER_BONUS_TYPE.AMBSSADOR'))
						->whereMonth('cs.confirm_date', '=',date('m',strtotime($date)));
			}
            if (!empty($qry))
            {
                $qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'act.account_id')
                        ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'act.upline_id')
                        ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'act.sponsor_id');
                $qry->where('acm.is_deleted', $this->config->get('constants.OFF'));
                $qry->select('act.account_id', 'acm.uname','acm.user_code', DB::Raw("concat_ws(' ',acd.firstname,acd.lastname) as full_name"), 'act.upline_id', 'act.sponsor_id', 'act.my_extream_right', 'act.rank', 'act.level', 'acm.signedup_on', 'acm.activated_on', 'acm.block', 'acm.uname', DB::Raw("((act.rgt_node-act.lft_node) DIV 2) as team_count"), 'act.nwroot_id', 'act.lft_node', 'act.rgt_node', 'act.qv', 'act.cv', 'act.referral_cnts', 'act.referral_paid_cnts', 'act.can_sponsor', 'acm.status', 'upum.uname as upline_uname', 'spum.uname as sponser_uname','cs.total_cv','cs.slab','cs.earnings','cs.commission','cs.p_id');
                $res = $qry->first();
		        if ($res)
                {
                    return $res;
                }
            }
    }
    
	public function get_ab_bonus_sales ($params)
    {
        extract($params);
        if(isset($parent_acinfo) && $account_id > 0)
        {
			$p_id = $parent_acinfo->p_id;
	        $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.GENERATION_BONUS_SLABS').' as bs',function($j)use($p_id){
				$j->on('bs.gen_account_id', ' = ', 'ut.account_id')
				   ->where('bs.customer_commission_id','=',$p_id);
			});
            $qry->leftjoin($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id');
            $qry->leftjoin($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id');
            $qry->whereMonth('bs.created_date','=',getGTZ($date,'m'));  //date('m',strtotime($date))
            $qry->whereYear('bs.created_date','=',getGTZ($date,'Y')); //date('Y',strtotime($date))
            $qry->where('ut.nwroot_id', '=', $parent_acinfo->nwroot_id);
            $qry->where('ut.level', '<=', $parent_acinfo->level + 1);
            $qry->where('ut.lft_node', '>', $parent_acinfo->lft_node);
            $qry->where('ut.rgt_node', '<', $parent_acinfo->rgt_node);
            $qry->where('um.is_deleted', $this->config->get('constants.OFF'));
            $qry->orderBy('ut.lft_node', 'ASC');
            $qry->select(DB::raw("um.account_id,um.uname as username,concat_ws(' ',ud.firstname,ud.lastname) as fullname,ut.qv,ut.cv,ut.upline_id,upum.uname as upline_uname,spum.uname as sponser_uname,(select group_concat(up.rank SEPARATOR '') from ".$this->config->get('tables.ACCOUNT_TREE')." as up where up.lft_node >= ".$parent_acinfo->lft_node." AND up.lft_node <= ut.lft_node AND up.rgt_node >= ut.rgt_node AND up.nwroot_id = ut.nwroot_id) as mypos,um.status,um.block,ut.can_sponsor,(ut.level-".$parent_acinfo->level.") as level,um.signedup_on,um.user_code,um.activated_on,((ut.rgt_node-ut.lft_node) DIV 2) as team_count,ut.referral_cnts,ut.referral_paid_cnts,um.login_block,bs.total_cv,bs.slab,bs.earning_cv")); 
            $res = $qry->get();
		    if ($parent_acinfo->qv > 0 || $parent_acinfo->cv > 0)
            {
                $parent_acinfo->status = 'Active';
            }
            else if ($parent_acinfo->qv == 0 && $parent_acinfo->cv == 0)
            {
                $parent_acinfo->status = 'Free';
            }
            else if ($parent_acinfo->block == $this->config->get('constants.ON'))
            {
                $parent_acinfo->status = 'block';
            }

            $arrname 		= 'genealogy'.$parent_acinfo->rank.'Arr';
            $genealogyArr   = $$arrname;
	        $genealogyArr[$parent_acinfo->rank] = (object) [
                        'account_id'=>$parent_acinfo->account_id,
                        'activated_on'=>$parent_acinfo->activated_on,
                        'can_sponsor'=>1,
                        'cv'=>$parent_acinfo->cv,
                        'user_code'=>$parent_acinfo->user_code,
                        'fullname'=>$parent_acinfo->full_name,
                        'level'=>$parent_acinfo->level,
                        'login_block'=>$parent_acinfo->rgt_node,
                        'mypos'=>$parent_acinfo->rank,
                        'qv'=>$parent_acinfo->qv,
                        'referral_cnts'=>$parent_acinfo->referral_cnts,
                        'referral_paid_cnts'=>$parent_acinfo->referral_paid_cnts,
                        'signedup_on'=>$parent_acinfo->signedup_on,
                        'status'=>$parent_acinfo->status,
                        'team_count'=>$parent_acinfo->team_count,
                        'upline_id'=>$parent_acinfo->upline_id,
                        'upline_uname'=>!empty($parent_acinfo->upline_uname) ? $parent_acinfo->upline_uname : '',
                        'username'=>$parent_acinfo->uname,
                        'sponser_uname'=>!empty($parent_acinfo->sponser_uname) ? $parent_acinfo->sponser_uname : '',
                        'geninfo'=>$this->getTeamGenerationSale($parent_acinfo->account_id)
            ];		

            if (!empty($res))
            {
                array_walk($res, function(&$ftdata) use (&$genealogyArr)
                {
                    $ftdata->signedup_on = date('d-M-Y ', strtotime($ftdata->signedup_on));
                    $ftdata->activated_on = date('d-M-Y ', strtotime($ftdata->activated_on));
                    if ($ftdata->qv > 0 || $ftdata->cv > 0)
                    {
                        $ftdata->status = 'Active';
                    }
                    else if ($ftdata->qv == 0 && $ftdata->cv == 0)
                    {
                        $ftdata->status = 'Free';
                    }
                    else if ($ftdata->block == $this->config->get('constants.ON'))
                    {
                        $ftdata->status = 'Blocked';
                    }
                    $ftdata->geninfo = $this->getTeamGenerationSale($ftdata->account_id);
                    $genealogyArr[$ftdata->mypos] = $ftdata;
                });
            }
            return !empty($genealogyArr) ? $genealogyArr : [];
        }
        return [];
    }
	
	public function getTeamGenerationSale ($account_id)
    {
        $generations = [
            1=>(object) ['gid'=>1, 'cnts'=>'-', 'qv'=>'-', 'cv'=>'-'],
            2=>(object) ['gid'=>2, 'cnts'=>'-', 'qv'=>'-', 'cv'=>'-'],
            3=>(object) ['gid'=>3, 'cnts'=>'-', 'qv'=>'-', 'cv'=>'-']
        ];
        $user_generations = DB::table($this->config->get('tables.ACCOUNT_TREE').' as gen')
                ->where('gen.upline_id', $account_id)
                ->leftJoin($this->config->get('tables.ACCOUNT_TREE').' as aff', function($aff)
                {
                    $aff->on('aff.lft_node', '>=', 'gen.lft_node')
                    ->on('aff.rgt_node', '<=', 'gen.rgt_node')
                    ->on('aff.nwroot_id', '=', 'gen.nwroot_id');
                })
                ->groupby('gen.rank')
                ->orderby('gen.rank', 'ASC')
                ->selectRaw('gen.rank as gid,count(aff.account_id) as cnts,sum(aff.qv) as qv,gen.cv+sum(aff.cv) as cv')
                ->get();
        if ($user_generations)
        {
            foreach ($user_generations as &$gen)
            {
                $gen->cnts = !empty($gen->cnts) ? $gen->cnts : '-';
                $gen->qv = !empty($gen->qv) ? $gen->qv : '-';
                $gen->cv = !empty($gen->cv) ? $gen->cv : '-';
                unset($generations[$gen->gid]);
            }
        }
        $user_generations = array_merge(array_values($generations), $user_generations);
        return $user_generations;
    }
	
	
	public function get_personal_bonus_sales ($params)
    {
        extract($params);
          $qry = DB::table($this->config->get('tables.PERSONAL_BONUS_MONTHLY_DETAILS').' as pb')
					->join($this->config->get('tables.ACCOUNT_DETAILS').' as um','um.account_id','=','pb.member_id')
				   ->join($this->config->get('tables.LOCATION_COUNTRY').' as ct','ct.country_id','=','pb.country')
				   ->join($this->config->get('tables.TRANS_TYPE').' as tt','tt.id','=','pb.trans_type')	
				   ->join($this->config->get('tables.BONUS_SRC_MODE').' as m','m.mode_id','=','pb.mode')	
				   ->where('account_id',$account_id)
				   ->whereMonth('date','=',date('m',strtotime($date)))
				   ->whereYear('date','=',date('Y',strtotime($date)))
				   ->select('pb.id as bonus_id','pb.member_id',"CONCAT_WS('',ud.firstname,ud.lastname) as fullname",'pb.bill_amount','pb.merchant_id','pb.cv','pb.date','ct.country','tt.trans_name as trans_type','m.mode_name','ct.iso2 as code');
            $res = $qry->get();
	       return $res;
     
    }
	
	public function teambonus_details ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if(!empty($account_id))
        {
			
				 
			$query = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
				->join(config('tables.ACCOUNT_MST').' as am', 'bb.account_id', '=', 'am.account_id')
				->join(config('tables.CURRENCIES').' as cur','cur.currency_id', '=', 'bb.currency_id')
				->where('bb.bonus_type', '=', config('constants.BONUS_TYPE.TEAM_BONUS'))
				->where('bb.bid',$id)
				->where('bb.account_id', '=', $account_id)
				 ->where('bb.is_deleted',0)
				->select('am.account_id','bb.last_bid', 'am.user_code', 'am.uname','bb.bid', 'bb.leftbinpnt', 'bb.rightbinpnt', 'bb.leftclubpoint', 'bb.rightclubpoint', 'bb.clubpoint', 'bb.totleftbinpnt', 'bb.totrightbinpnt', 'bb.leftcarryfwd', 'bb.rightcarryfwd', 'bb.flushamt', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.currency as code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income', 'bb.earnings','bb.capping','bb.left_flushout','bb.right_flushout','bb.from_date','bb.to_date');
			
			$ftdata = $query->first();
			
			
			
			
			if (!empty($ftdata))
			{
				
				$prev = DB::table(config('tables.AF_BINARY_BONUS'))
				 ->where('is_deleted',0)
				 ->where('bonus_type', '=', config('constants.BONUS_TYPE.TEAM_BONUS'))
				 ->where('bid','=',$ftdata->last_bid)
				 ->first();	
				
				$ftdata->leftbinpnt = number_format($ftdata->leftbinpnt, \AppService::decimal_places($ftdata->leftbinpnt));
				$ftdata->rightbinpnt = number_format($ftdata->rightbinpnt, \AppService::decimal_places($ftdata->rightbinpnt));
				$ftdata->leftclubpoint = number_format($ftdata->leftclubpoint, \AppService::decimal_places($ftdata->leftclubpoint));
				$ftdata->rightclubpoint = number_format($ftdata->rightclubpoint, \AppService::decimal_places($ftdata->rightclubpoint));
				$ftdata->totleftbinpnt = number_format($ftdata->totleftbinpnt, \AppService::decimal_places($ftdata->totleftbinpnt));
				$ftdata->totrightbinpnt = number_format($ftdata->totrightbinpnt, \AppService::decimal_places($ftdata->totrightbinpnt)); 
				$ftdata->leftcarryfwd = number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
				$ftdata->rightcarryfwd = number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
				$ftdata->left_flushout 	  = number_format($ftdata->left_flushout, \AppService::decimal_places($ftdata->left_flushout));
				$ftdata->right_flushout   = number_format($ftdata->right_flushout, \AppService::decimal_places($ftdata->right_flushout));
				$ftdata->earnings = number_format($ftdata->earnings, \AppService::decimal_places($ftdata->earnings));
				$ftdata->capping = number_format($ftdata->capping, \AppService::decimal_places($ftdata->capping));
				$ftdata->clubpoint = number_format($ftdata->clubpoint,\AppService::decimal_places($ftdata->clubpoint));
				$ftdata->date_for  = date('d, M',strtotime($ftdata->from_date)).' - '.date('d-M',strtotime( $ftdata->to_date)).','.date('Y',strtotime( $ftdata->to_date));
				$ftdata->leftopening 	  = !empty($prev->leftcarryfwd)?number_format($prev->leftcarryfwd):0;
				$ftdata->rightopening  	  = !empty($prev->rightcarryfwd)?number_format($prev->rightcarryfwd):0;					
				return !empty($ftdata) ? $ftdata : NULL;
			}
        }
        return NULL;
    }
	
	public function leadership_bonus_details ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if(!empty($account_id))
        {
						
			
            $query = DB::table(config('tables.ACCOUNT_MST').' as am')
                    ->join(config('tables.AF_BINARY_BONUS').' as bb', function($subquery) use($account_id)
                    {
                        $subquery->on('bb.account_id', '=', 'am.account_id')
                        ->where('bb.account_id', '=', $account_id)
						->where('bb.bonus_type', '=', config('constants.BONUS_TYPE.LEADERSHIP_BONUS'));
                    })					
					->where('bb.bid',$id)
					->where('bb.is_deleted',0)
			        ->join(config('tables.CURRENCIES').' as cur','cur.currency_id', '=', 'bb.currency_id')
			        ->select('am.account_id', 'bb.last_bid','am.user_code', 'am.uname', 'bb.leftbinpnt', 'bb.rightbinpnt', 'bb.leftclubpoint', 'bb.rightclubpoint', 'bb.clubpoint', 'bb.totleftbinpnt', 'bb.totrightbinpnt', 'bb.leftcarryfwd', 'bb.rightcarryfwd', 'bb.flushamt', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.currency as code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income', 'bb.earnings','bb.capping','bb.left_flushout','bb.right_flushout','bb.date_for');
					
			$query->orderBy('bb.account_id', 'ASC');
			$ftdata = $query->first();
					
			if (!empty($ftdata))
			{	
				$prev = DB::table(config('tables.AF_BINARY_BONUS'))
					 ->where('is_deleted',0)
					 ->where('bonus_type', '=', config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
					 ->where('bid','=',$ftdata->last_bid)
					 ->first();		
		
				$ftdata->leftbinpnt 	  = number_format($ftdata->leftbinpnt, \AppService::decimal_places($ftdata->leftbinpnt));
				$ftdata->rightbinpnt 	  = number_format($ftdata->rightbinpnt, \AppService::decimal_places($ftdata->rightbinpnt));
				$ftdata->leftclubpoint    = number_format($ftdata->leftclubpoint, \AppService::decimal_places($ftdata->leftclubpoint));
				$ftdata->rightclubpoint   = number_format($ftdata->rightclubpoint, \AppService::decimal_places($ftdata->rightclubpoint));
				$ftdata->totleftbinpnt 	  = number_format($ftdata->totleftbinpnt, \AppService::decimal_places($ftdata->totleftbinpnt));
				$ftdata->totrightbinpnt   = number_format($ftdata->totrightbinpnt, \AppService::decimal_places($ftdata->totrightbinpnt));
				$ftdata->leftcarryfwd 	  = number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
				$ftdata->rightcarryfwd    = number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
				$ftdata->left_flushout 	  = number_format($ftdata->left_flushout, \AppService::decimal_places($ftdata->left_flushout));
				$ftdata->right_flushout   = number_format($ftdata->right_flushout, \AppService::decimal_places($ftdata->right_flushout));
				$ftdata->earnings 		  = number_format($ftdata->earnings, \AppService::decimal_places($ftdata->earnings));
				$ftdata->capping 		  = number_format($ftdata->capping, \AppService::decimal_places($ftdata->capping));
				$ftdata->date_for 		  = date('M, Y',strtotime($ftdata->from_date));
				$ftdata->leftopening 	  = !empty($prev->leftcarryfwd)?number_format($prev->leftcarryfwd):0;
				$ftdata->rightopening  	  = !empty($prev->rightcarryfwd)?number_format($prev->rightcarryfwd):0;
				$ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);				
				return !empty($ftdata) ? $ftdata : NULL;
			}
        }
        return NULL;
	}

	public function survival_bonus ($arr = array(), $count = false) 
    {   
        extract($arr);
        if (!empty($account_id))
        {
            $query = DB::table($this->config->get('tables.AFF_DIRECTORS_BONUS').' as adb')
			        ->join($this->config->get('tables.ACCOUNT_TREE').' as ap', 'ap.account_id', '=', 'adb.account_id')
			        ->join($this->config->get('tables.AFF_RANKING_LOOKUPS').' as arl', function($sub){
						$sub->on('arl.af_rank_id', '=', 'ap.rank')
						->where('arl.lang_id', '=', $this->config->get('app.locale_id'));
					})					
			        ->join($this->config->get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'adb.wallet_id')
                    ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'adb.currency_id')       
					->where('adb.is_deleted', '=', $this->config->get('constants.OFF'));
			if (isset($bonus_type) && !empty($bonus_type))
            { 
                $query->where('adb.bonus_type', '=', $bonus_type);		
            }		
            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {  
                $query->whereDate('adb.bonus_date', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('adb.bonus_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {  
                $query->whereDate('adb.bonus_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('adb.bonus_date', '>=', getGTZ($to, 'Y-m-d'));
            }
            if ($count)
            {	
                return $query->count();
            }
            else
            {  
                if (isset($length) && !empty($length))
                {
                    $query->skip($start)->take($length);
                }
				$result = $query->select('adb.commission', 'adb.tax', 'adb.vi_help','adb.net_pay','adb.status','adb.bonus_date','arl.rank','cur.currency_symbol', 'cur.currency as code','cur.decimal_places')
                               ->orderBy('adb.id', 'DESC')
							   ->get();      
							   
                if (!empty($result))
                {
                    array_walk($result, function(&$res)
                    {
					
						$res->commission = \CommonLib::currency_format($res->commission, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->tax = \CommonLib::currency_format($res->tax, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->vi_help = \CommonLib::currency_format($res->vi_help, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->net_pay = \CommonLib::currency_format($res->net_pay, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
						$res->bonus_date = !empty($res->bonus_date) ? showUTZ($res->bonus_date):'';
						$res->status_class = isset($res->status) ? $this->config->get('dispclass.bonus_status.'.$res->status):'';
					    $res->status = isset($res->status) ? trans('general.bonus_status.'.$res->status):''; 
                    });
                    return !empty($result) ? $result : NULL;
                }
            }
        }
        return NULL;
    }	
}
