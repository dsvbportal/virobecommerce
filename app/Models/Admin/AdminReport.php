<?php

namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Helpers\ImageLib;
use Config;
use URL;

class AdminReport extends BaseModel
{

	public function team_commission($arr,$count=false){
	
        extract($arr);
      
            $query = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
					->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','bb.account_id')
						/* ->join(config('tables.AF_BINARY_BONUS').' as bb', function($subquery) use($account_id)
						{
							$subquery->on('bb.account_id', '=', 'am.account_id')
							->where('bb.account_id', '=', $account_id)
							->where('bb.type', '=', config('constants.BONUS.TYPE1'));
						}) */
						->where('bb.bonus_type', '=', config('constants.BONUS_TYPE.TEAM_BONUS'))
						->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'bb.currency_id')
						->select('am.account_id', 'am.user_code', 'am.uname','bb.bid', 'bb.leftbinpnt', 'bb.rightbinpnt', 'bb.leftclubpoint', 'bb.rightclubpoint', 'bb.clubpoint', 'bb.totleftbinpnt', 'bb.totrightbinpnt', 'bb.leftcarryfwd', 'bb.rightcarryfwd', 'bb.flushamt', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.currency as code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income','bb.capping','bb.earnings');
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
			            $ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',').' '.$ftdata->code;
						$ftdata->income = $ftdata->currency_symbol.' '.number_format($ftdata->income, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->tax = $ftdata->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->ngo_wallet_amt = $ftdata->currency_symbol.' '.number_format($ftdata->ngo_wallet_amt, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->paidinc = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',');
                        $ftdata->leftcarryfwd 	= number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
                        $ftdata->rightcarryfwd  = number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
                        $ftdata->capping 	= number_format($ftdata->capping);
                        $ftdata->earnings 		= number_format($ftdata->earnings);
						$ftdata->date_for  		= date('d-M',strtotime($ftdata->from_date)).' - '.date('d-M',strtotime( $ftdata->to_date)).','.date('Y',strtotime( $ftdata->to_date));
                        $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                        if ($ftdata->status == 0)
                        {
                            $ftdata->status = config('constants.ACCOUNT.PENDING');
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
        
        return NULL;
	}
	
	
    public function get_leadership_bonus($arr = array(), $count = false)
    {
        extract($arr);
		$query = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
					->join((config('tables.ACCOUNT_MST').' as am').' as am','am.account_id','=','bb.account_id')
                   /*  ->join(config('tables.AF_BINARY_BONUS').' as bb', function($subquery) use($account_id)
                    {
                        $subquery->on('bb.account_id', '=', 'am.account_id')
                        ->where('bb.account_id', '=', $account_id)
                        ->where('bb.type', '=', config('constants.BONUS.TYPE2'));
                    }) */
					->where('bb.bonus_type', '=', config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
                    ->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'bb.currency_id')
                    ->select('am.account_id', 'am.user_code', 'am.uname','bb.bid','bb.leftbinpnt', 'bb.rightbinpnt', 'bb.leftclubpoint', 'bb.rightclubpoint', 'bb.clubpoint', 'bb.totleftbinpnt', 'bb.totrightbinpnt', 'bb.leftcarryfwd', 'bb.rightcarryfwd', 'bb.flushamt', 'bb.confirmed_date', 'bb.created_date', 'bb.status', 'bb.from_date', 'bb.to_date', 'bb.bonus_value', 'bb.paidinc', 'cur.currency_symbol', 'cur.currency as code', 'bb.tax', 'bb.ngo_wallet_amt', 'bb.income','bb.capping','bb.earnings','bb.date_for');
            if(isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $query->whereDate('bb.date_for', '>=', getGTZ($from, 'Y-m-d'));
                $query->whereDate('bb.date_for', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {
                $query->whereDate('bb.date_for', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereDate('bb.date_for', '>=', getGTZ($to, 'Y-m-d'));
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
						$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',').' '.$ftdata->code;
						$ftdata->income = $ftdata->currency_symbol.' '.number_format($ftdata->income, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->tax = $ftdata->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->ngo_wallet_amt = $ftdata->currency_symbol.' '.number_format($ftdata->ngo_wallet_amt, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->paidinc = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',');
						$ftdata->bonus_value 	 = number_format($ftdata->bonus_value);
                        $ftdata->earnings 		 = number_format($ftdata->earnings);
						     $ftdata->capping 	 = number_format($ftdata->capping);
                        $ftdata->leftcarryfwd 	 = number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
                        $ftdata->rightcarryfwd	 = number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
                        $ftdata->flushamt 		 = number_format($ftdata->flushamt, \AppService::decimal_places($ftdata->flushamt));
                        $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
						$ftdata->date 		 	 = date('M, Y',strtotime($ftdata->date_for));
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
						$ftdata->link=route('admin.commission.leadership.details',['period'=>date('Y-M',strtotime($ftdata->date_for)])
                    });
                    return !empty($result) ? $result : NULL;
                }
            }
        
        return NULL;
    }
	
	public function leadership_bonus_details ($account_id, $arr = array(), $count = false)
    {
        extract($arr);
        if(!empty($account_id))
        {		
			
            $query = DB::table(config('tables.AF_BINARY_BONUS').' as bb')
                    ->join(config('tables.ACCOUNT_MST').' as am', function($subquery) use($account_id)
                    {
                        $subquery->on('bb.account_id', '=', 'am.account_id')

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
	
	
	public function ambassador_commission ($arr = array(), $count = false)
    {
        extract($arr);
            $query = DB::table(config('tables.CUSTOMER_COMMISSION_MONTHLY').' as pm')
					->join(config('tables.CURRENCIES').' as c','c.currency_id','=','pm.currency_id')
					->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','pm.account_id')
                    ->select('pm.account_id', 'pm.confirm_date','pm.slab', 'pm.total_cv', 'pm.earnings', 'pm.commission', 'pm.tax', 'pm.ngo_wallet', 'pm.net_pay', 'pm.status','pm.created_on as created_date','c.currency_symbol','am.uname','am.user_code')
					->where('pm.type',config('constants.CUSTOMER_BONUS_TYPE.AMBSSADOR'));

            if(isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $query->whereMonth('pm.confirm_date', '>=', getGTZ($from, 'Y-m-d'));
              //  $query->whereMonth('pm.confirm_date', '<=', getGTZ($to, 'Y-m-d'));
            }
            else if (!empty($from) && isset($from))
            {

                $query->whereMonth('pm.confirm_date', '<=', getGTZ($from, 'Y-m-d'));
            }
            else if (!empty($to) && isset($to))
            {
                $query->whereMonth('pm.confirm_date', '>=', getGTZ($to, 'Y-m-d'));
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
                    $serial_no 		= 1;
					//$currency_info  = $this->commonObj->get_currency($this->userSess->currency_id);
                    array_walk($result, function(&$ftdata) use($serial_no)
                    {

                        $ftdata->serial_no  = $serial_no;
                        $ftdata->commission = $ftdata->currency_symbol.' '.number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission));
                        $ftdata->tax 		= $ftdata->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax));
                        $ftdata->ngo_wallet = $ftdata->currency_symbol.' '.number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet));
                        $ftdata->net_pay 	= $ftdata->currency_symbol.' '.number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay));
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
	
    public function faststart_bonus($arr = array(),$count='')
    {
        extract($arr);
        $refSql = DB::table(config('tables.REFERRAL_EARNINGS').' as re')
                ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast', 'ast.subscribe_topup_id', '=', 're.subscrib_topup_id')
                ->join(config('tables.AFF_PACKAGE_PRICING').' as pri', 'pri.package_id', '=', 'ast.package_id')
                ->join(config('tables.ACCOUNT_MST').' as fum', 'fum.account_id', '=', 're.from_account_id')
                ->join(config('tables.ACCOUNT_TREE').' as ut', 'ut.account_id', '=', 're.from_account_id')
                ->join(config('tables.ACCOUNT_MST').' as rfum', 'rfum.account_id', '=', 'ut.sponsor_id')
                ->join(config('tables.ACCOUNT_MST').' as racm', 'racm.account_id', '=', 'ut.nwroot_id')
                ->join(config('tables.ACCOUNT_DETAILS').' as rfud', 'rfud.account_id', '=', 'rfum.account_id')
                ->join(config('tables.ACCOUNT_MST').' as tum', 'tum.account_id', '=', 're.to_account_id')
                ->join(config('tables.ACCOUNT_DETAILS').' as tud', 'tud.account_id', '=', 'tum.account_id')
                ->join(config('tables.AFF_PACKAGE_MST').' as pm', 'pm.package_id', '=', 'ast.package_id')
                ->join(config('tables.AFF_PACKAGE_LANG').' as pl', 'pl.package_id', '=', 'pm.package_id')
                ->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 're.currency_id')
                ->join(config('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 're.payout_type')
                ->join(config('tables.WALLET_LANG').' as wal', 'wal.wallet_id', '=', 're.wallet_id')
                ->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'fum.status');
				
        $refSql->select(DB::Raw("re.*,re.ref_id,re.payout_type,rfum.account_id,rfum.uname as sponser_uname,concat_ws('',rfud.firstname,rfud.lastname) as sponser_full_name,racm.uname as root_group,fum.account_id,re.created_date,re.qv,tum.uname as to_uname,fum.uname as from_uname,concat_ws(' ',tud.firstname, tud.lastname) as to_full_name,pl.package_name,re.amount,IF(re.payout_type=1,(select `wallet` from ".config('tables.WALLET_LANG')." as wal where `wal`.`wallet_id` = re.wallet_id),(select `payment_type` from ".config('tables.PAYMENT_TYPES')." as pt where `pt`.`payment_type_id` = re.payout_type)) as pay_mode,(select uname from ".config('tables.ACCOUNT_MST')." where account_id = (select upline_id from ".config('tables.ACCOUNT_TREE')." where account_id = re.from_account_id )) as upline_username,cur.currency as currency,cur.currency_symbol,re.status,pri.price as packagepricing,usl.status_name,re.earnings_qv,re.commission,re.service_tax as tax,re.ngo_wallet_amt,re.net_pay"));
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
                    $ftdata->commission = number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission), '.', ',');
                    $ftdata->tax = number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax), '.', ',');
                    $ftdata->ngo_wallet_amt = number_format($ftdata->ngo_wallet_amt, \AppService::decimal_places($ftdata->ngo_wallet_amt), '.', ',');
                    $ftdata->net_pay = number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay), '.', ',');
                    $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
                    //$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
                });
                return $result;
            }
            else
                return false;
        }
    }
	
	/* Car Bonus & Star Bonus */
    public function car_bonus ($arr = array(), $count = false) 
    {  
        extract($arr);       
		$query = DB::table(config('tables.AFF_DIRECTORS_BONUS').' as adb')
		        ->join(config('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'adb.account_id')
				->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','adb.account_id')
				->join(config('tables.ACCOUNT_TREE').' as ap', 'ap.account_id', '=', 'adb.account_id')				
				->join(config('tables.AFF_RANKING_LOOKUPS').' as arl', function($sub){
					$sub->on('arl.af_rank_id', '=', 'ap.rank')
					->where('arl.lang_id', '=', config('app.locale_id'));
				})					
				->join(config('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'adb.wallet_id')
				->join(config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'adb.currency_id')       
				->where('adb.is_deleted', '=', config('constants.OFF'));
			
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
			$result = $query->select('adb.id','adb.commission', 'adb.tax', 'adb.vi_help','adb.net_pay','adb.status','adb.bonus_date','adb.account_id','amst.user_code','amst.uname','arl.rank','cur.currency_symbol', 'cur.currency as code','cur.decimal_places', DB::RAW('concat_ws(" ",ad.firstname,ad.lastname) as full_name'))
						   ->orderBy('adb.id', 'DESC')
						   ->get();      
						   
			if (!empty($result))
			{
				array_walk($result, function(&$res)
				{		
				    $res->actions = [];	
					$res->commission = \CommonLib::currency_format($res->commission, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
					$res->tax = \CommonLib::currency_format($res->tax, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
					$res->vi_help = \CommonLib::currency_format($res->vi_help, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
					$res->net_pay = \CommonLib::currency_format($res->net_pay, ['currency_symbol'=>$res->currency_symbol, 'currency_code'=>$res->code, 'value_type'=>(''), 'decimal_places'=>$res->decimal_places]);
					$res->bonus_date = !empty($res->bonus_date) ? showUTZ($res->bonus_date):'';					
					if ($res->status == config('constants.BONUS_STATUS.PENDING'))
					{	
						$res->actions[] = ['url'=>route('admin.commission.change-bonus-status'), 'class'=>'change_status', 'data'=>['id'=>$res->id,'status'=>config('constants.BONUS_STATUS.RELEASED'), 'account_id'=>$res->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Release']; 
					}
					$res->status_class = config('dispclass.bonus_status.'.$res->status);
					$res->status = trans('general.bonus_status.'.$res->status);
				});
				return !empty($result) ? $result : NULL;
			}
		}      
        return NULL;
    }
	
	public function changeBonusStatus (array $data = array())
    {
	    extract($data);
	    if($status = config('constants.BONUS_STATUS.RELEASED')){
		    $bonusInfo = DB::table(config('tables.AFF_DIRECTORS_BONUS').' as adb')
			                ->where('adb.id', '=', $id)
			                ->select('adb.id','adb.bonus_type','adb.account_id', 'adb.wallet_id','adb.currency_id','adb.commission','adb.tax','adb.vi_help','adb.net_pay')
							->first();
					
	        if(!empty($bonusInfo) && $bonusInfo->net_pay>0){
			    			
				$bonus_type	= trans('admin/finance.bonus_type.'.$bonusInfo->bonus_type);	 					
				$update_trans = $this->updateAccountTransaction(['to_account_id'=>$bonusInfo->account_id, 'relation_id'=>$bonusInfo->id, 'to_wallet_id'=>$bonusInfo->wallet_id, 'currency_id'=>$bonusInfo->currency_id, 'amt'=>$bonusInfo->net_pay, 'credit_remark_data'=>['remarks'=>trans('admin/finance.bonus_received',['bonus'=>$bonus_type,'date'=>getGTZ('M,Y')])],'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
								
				if($update_trans>0 && $bonusInfo->vi_help>0)
				{	
				    $update_trans2 = $this->updateAccountTransaction(['to_account_id'=>$bonusInfo->account_id, 'relation_id'=>$bonusInfo->id, 'to_wallet_id'=>$this->config->get('constants.WALLETS.VIH'), 'currency_id'=>$bonusInfo->currency_id, 'amt'=>$bonusInfo->vi_help, 'transaction_for'=>'FUND_TRANS_BY_SYSTEM','ngo_amt'=>$bonusInfo->vi_help], false, true);
				}
				
				if($update_trans>0){
					DB::table(config('tables.AFF_DIRECTORS_BONUS'))
							->where('id', $id)
							->update(array('status'=>$status));
							
					return true;	
				}
			}
		}		
		return false;
    }
	
	public function personal_commission ($arr = array(), $count = false)
    {
        extract($arr);
		$query = DB::table(config('tables.CUSTOMER_COMMISSION_MONTHLY').' as pm')
					->join(config('tables.CURRENCIES').' as c','c.currency_id','=','pm.currency_id')
					->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','pm.account_id')
				->where('type',config('constants.CUSTOMER_BONUS_TYPE.PERSONAL'))
				->select('pm.account_id', 'pm.confirm_date', 'pm.slab', 'pm.total_cv', 'pm.earnings', 'pm.commission', 'pm.tax', 'pm.ngo_wallet', 'pm.net_pay', 'pm.status','c.currency_symbol','am.uname','am.user_code');

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
			//$currency_info = $this->commonObj->get_currency($this->userSess->currency_id);
			array_walk($result, function(&$ftdata) use($serial_no)
			{

				$ftdata->serial_no 		= $serial_no;
				$ftdata->commission 	= $ftdata->currency_symbol.' '.number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission));
				$ftdata->tax = $ftdata->currency_symbol.' '.number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax));
				$ftdata->ngo_wallet = $ftdata->currency_symbol.' '.number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet));
				$ftdata->net_pay = $ftdata->currency_symbol.' '.number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay));
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
        
        return NULL;
    }
	
	public function get_ranks($arr){
		extract($arr);
		$myrank = [];
		$query  = DB::table(config('tables.ACCOUNT_AF_RANKING_LOG').' as rl')
						->join(config('tables.AFF_RANKING_LOOKUPS').' as lk','rl.af_rank_id','=','lk.af_rank_id')
						->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','rl.account_id')
						->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','am.account_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as af','af.account_id','=','ad.account_id')
						->join(config('tables.LOCATION_COUNTRY').' as lc','lc.country_id','=','af.country_id')
						->whereIn('rl.status',[1])
						->whereIn('rl.is_verified',[1])
						->where('lk.lang_id','=',1);
		if(isset($countries) && !empty($countries)){
			$query->select('lc.country_id','lc.country')->distinct('lc.country_id');
			$res = $query->get();
			return $res;
		}else{				
			$query->select('lk.rank','rl.af_rank_id','rl.gen_1','rl.gen_2','rl.gen_3','rl.status','rl.is_verified','am.user_code','am.uname',DB::raw('CONCAT_WS(" ",ad.firstname,ad.lastname) as fullname'),'lc.country','rl.account_id','rl.created_on');
		}
		if (isset($length) && !empty($length))
		{
			$query->skip($start)->take($length);
		}
		if (isset($terms) && !empty($terms))
		{
			$query->where('am.uname','like','%'.$terms.'%')
				  ->Orwhere('am.user_code','%'.$terms.'%');
		}
		if(isset($count) && !empty($count)){
			$result = $query->count();
			return $result;
		}else{
			$result = $query->get();
			array_walk($result,function($f){
				$f->created_on = date('M-Y',strtotime($f->created_on));
			});
			return $result;
		}
	}
	
	public function get_rank_log(array $arr=[]){
		extract($arr);
		$res  = DB::table(config('tables.ACCOUNT_AF_RANKING_LOG').' as rl')
					->join(config('tables.AFF_RANKING_LOOKUPS').' as lk','rl.af_rank_id','=','lk.af_rank_id')
					->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','rl.account_id')
					->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','am.account_id')
					->join(config('tables.ACCOUNT_PREFERENCE').' as af','af.account_id','=','ad.account_id')
					->join(config('tables.LOCATION_COUNTRY').' as lc','lc.country_id','=','af.country_id')
					->where('rl.account_id',$account_id)
					->where('rl.is_verified',1)
					->where('lk.lang_id','=',1)
					->select('lk.rank','rl.created_on')
					->get();					
		if(!empty($res)){
			array_walk($res,function($f){
				$f->created_on = !empty($f->created_on) ? showUTZ($f->created_on, 'M-Y') : '';
			});
			return $res;
		}else {
			return [];
		}					
	}	
}