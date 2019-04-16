<?php
namespace App\Models\Franchisee;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\CommonModel;
use CommonLib;
class FrReports extends BaseModel {
	
    public function __construct() {
        parent::__construct();				
		$this->commonObj = new CommonModel;
    }
	
	public function getJoiningReport($arr = array()) {
		extract($arr);
        $op = [
            'previous_month_joining'=>0,
            'previous_month_per'=>0,
            'current_month_joining'=>0,
            'current_month_per'=>0,
            'total_joining'=>0
        ];
		
        if (isset($franchisee_type) && isset($locations) && !empty($locations))
        {	
			
            $previous_month = date('Y-m', strtotime('previous month'));
            $query = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut ')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'ut.account_id', ' = ', 'um.account_id')
					->join($this->config->get('tables.ADDRESS_MST').' as ad', function($join){
						$join->on('ad.relative_post_id', ' = ', 'um.account_id')
						->where('ad.post_type', ' = ', $this->config->get('constants.ADDRESS_POSTTYPE.ACCOUNT'))
						->where('ad.address_type_id', ' = ', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
					})
                    ->where('um.is_affiliate', $this->config->get('constants.ACTIVE'))
                    ->where('um.is_deleted', $this->config->get('constants.OFF'))
                    ->select(DB::Raw(
                            "sum(IF((DATE(ut.created_on) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."'),1,0)) as current_month_joining,"
                            ."sum(IF((DATE(ut.created_on) BETWEEN '".date('Y-m-01', strtotime('previous month'))."' AND '".date('Y-m-t', strtotime('previous month'))."'),1,0)) as previous_month_joining"
            ));
            switch ($franchisee_type)
            {
                case $this->config->get('constants.FRANCHISEE_TYPE.REGION'):
                    $query->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', ' = ', 'ad.state_id')
                            ->whereIn('ls.state_id', $locations);
                    break;
                case $this->config->get('constants.FRANCHISEE_TYPE.CITY'):
                    $query->join($this->config->get('tables.LOCATION_CITY').' as lcy', 'lcy.city_id', ' = ', 'ad.city_id')
                            ->whereIn('lcy.city_id', $locations);
                    break;
                case $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'):
                    $query->join($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', ' = ', 'ad.district_id')
                            ->whereIn('ld.district_id', $locations);
                    break;
                case $this->config->get('constants.FRANCHISEE_TYPE.STATE'):
                    $query->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', ' = ', 'ad.state_id')
                            ->whereIn('ls.state_id', $locations);
                    break;
                case $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'):
                    $query->whereIn('ad.country', $locations);
                    break;
            }  
			$user = $query->first();
            $op['previous_month_joining'] = $user->previous_month_joining;
            $op['current_month_joining'] = $user->current_month_joining;
        }
        return (object) $op;
	}
	
	public function getPackageSales ($arr = array())
    {
        extract($arr);
        $data = [
            'current_month_pk_sales'=>0,
            'current_month_per'=>0.00,
            'previous_month_pk_sales'=>0,
            'previous_month_per'=>0.00
        ];        
        $total = 0;
        //$total = $this->getIncome($arr);
        $arr['from'] = date('Y-m-01');
        $arr['to'] = date('Y-m-t');
        $current = $this->getIncome2($arr);
		print_r($current);die;
        $arr['from'] = getGTZ('Y-m-1',date('Y-m-1', strtotime('previous month')));
        $arr['to'] = getGTZ('Y-m-t',date('Y-m-1', strtotime('previous month')));
        $previous = $this->getIncome2($arr);
        if ($total > 0)
        {
            $data['current_month_per'] = number_format(($current / $total) * 100, 2, '.', ',');
            $data['previous_month_per'] = number_format(($previous / $total) * 100, 2, '.', ',');
        }
        $data['current_month_pk_sales'] = number_format($current, 2, '.', ',').' '.$currency_code;
        $data['previous_month_pk_sales'] = number_format($previous, 2, '.', ',').' '.$currency_code;
        return (object) $data;
    }
	
	public function getIncome2 ($arr = array())
    {
        extract($arr);
        $op = array();
        $commissions_earnings = 0;
        $commissions_earnings_arr = array();
        $location_types_arr = [
			1=>['field'=>'country_id', 'subacc_type'=>'2,3,4,5'], 
			2=>['field'=>'region_id', 'subacc_type'=>'3,4,5'], 
			3=>['field'=>'state_id', 'subacc_type'=>'4,5'], 
			4=>['field'=>'district_id', 'subacc_type'=>'3,4,5']
		];

        $locations_access = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                ->where('fal.account_id', $account_id)
                ->where('fal.status', $this->config->get('constants.ACTIVE'))
                ->select('fal.access_location_type', 'fal.relation_id')
                ->first();
        
		$exchange_rate = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                ->where('from_currency_id', '=', 'fc.currency_id')
                ->where('to_currency_id', '=', $currency_id)
                ->select(DB::raw('if(live_rate is not null,live_rate,1)'))
                ->take(1);
        
		$query = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')
                ->selectRaw('sum(if(fc.currency_id!='.$currency_id.',fc.amount*('.$exchange_rate->toSql().'),fc.amount)) as amount')
                ->addBinding($exchange_rate->getBindings())
                ->whereIn('fc.commission_type', [2, 3, 4])
                ->where('fc.is_deleted', $this->config->get('constants.OFF'))
                ->whereIn('fc.status', [$this->config->get('constants.COMISSION_STATUS_CONFIRMED'), $this->config->get('constants.COMISSION_STATUS_PENDING')])
                ->where('fc.account_id', $account_id)
                ->groupby('fc.account_id');
        
		if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {
            $query->whereBetween(DB::raw('DATE(fc.created_date)'), [$from,
                $to]);
        }

        $ceRes = $query->select('amount')->first();
		$commissions_earnings = !empty($ceRes)? $ceRes->amount:0;
        $commissions_earnings_arr['1st'] = $commissions_earnings;
        /*  get the sum of admin transfered funds to support centre start */
        /* ---------------------------- */

        $exchange_rate2 = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                ->where('from_currency_id', '=', 'ft.currency_id')
                ->where('to_currency_id', '=', $currency_id)
                ->select(DB::raw('if(live_rate is not null,live_rate,1)'))
                ->take(1);

        $ftquery = DB::table($this->config->get('tables.FRANCHISEE_FUND_TRANSFER').' as ft')
				->join($this->config->get('tables.ACCOUNT_MST').' as fa', 'fa.account_id', '=', 'ft.from_account_id')
				->join($this->config->get('tables.ACCOUNT_MST').' as ta', 'ta.account_id', '=', 'ft.to_account_id')
                ->selectRaw('sum(if(ft.currency_id!='.$currency_id.',ft.amount*('.$exchange_rate2->toSql().'),IF(ft.amount IS NOT NULL,ft.amount,0))) as amount')
                ->addBinding($exchange_rate2->getBindings())
                ->where(function($subquery) use($account_id, $location_types_arr, $locations_access)
                {
                    $subquery->where('ft.to_account_id', $account_id);
                    if (isset($location_types_arr[$locations_access->access_location_type]))
                    {
                        $subquery->orWhereRaw("ft.to_account_id in (select account_id from ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." where access_location_type in(".$location_types_arr[$locations_access->access_location_type]['subacc_type'].") and ".$location_types_arr[$locations_access->access_location_type]['field']." in(".$locations_access->relation_id."))");
                    }
                })                
                ->where('ta.account_type_id', '=', 3)
                ->where('fa.account_type_id', '=', 4)
                ->where('ft.is_deleted', $this->config->get('constants.OFF'))
                ->whereIn('ft.status', [$this->config->get('constants.COMISSION_STATUS_CONFIRMED')]);

        if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {
            $ftquery->whereBetween(DB::raw('DATE(ft.transferred_on)'), [$from, $to]);
        }
		$ftRes = $ftquery->first();		
        $commissions_earnings += is_numeric($ftRes->amount)? $ftRes->amount:0;
        $commissions_earnings_arr['2nd'] = $commissions_earnings;
        /* ---------------------------- */

        $exchange_rate3 = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                ->where('from_currency_id', '=', 'aft.currency_id')
                ->where('to_currency_id', '=', $currency_id)
                ->select(DB::raw('if(live_rate is not null,live_rate,1)'))
                ->take(1);
        $aftquery = DB::table($this->config->get('tables.ACCOUNT_ADD_FUND').' as aft')
                ->selectRaw('sum(if(aft.from_currency_id!='.$currency_id.',aft.amount*('.$exchange_rate3->toSql().'),aft.amount)) as amount')
                ->addBinding($exchange_rate3->getBindings())
                ->where(function($subquery) use($account_id, $location_types_arr, $locations_access)
                {
                    $subquery->where('aft.account_id', $account_id);
                    if (isset($location_types_arr[$locations_access->access_location_type]))
                    {
                        $subquery->orWhereRaw("aft.account_id in (select account_id from ".$this->config->get('tables.FRANCHISEE_ACCESS_LOCATION')." where access_location_type in (".$location_types_arr[$locations_access->access_location_type]['subacc_type'].") and ".$location_types_arr[$locations_access->access_location_type]['field']." in (".$locations_access->relation_id."))");
                    }
                })                
                ->whereIn('aft.payment_status', [1])
                ->whereIn('aft.status', [0, 1]);
        if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {
            $aftquery->whereBetween(DB::raw('DATE(aft.requested_date)'), [$from, $to]);
        }        
		$aftRes = $aftquery->pluck('amount');
        $commissions_earnings += $aftRes[0];		
        $commissions_earnings_arr['3rd'] = $commissions_earnings;
        return $commissions_earnings;
    }
	
	/* TDS Deducted Report */
  public function tds_deducted_details ($arr = array())
    {   	   
        extract($arr);    
        $qry = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as trs')
		        ->where('trs.is_deleted',$this->config->get('constants.OFF'))
		        ->where('trs.tax','>',$this->config->get('constants.OFF'));
        if (isset($account_id))
        {
            $qry->where('trs.account_id', $account_id);

            if (isset($from) && !empty($from) && isset($to) && !empty($to))
            {
                $qry->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($from))."'");
                $qry->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (isset($from) && !empty($from))
            {
                $qry->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $qry->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($to))."'");
            }

            if (isset($search_term) && !empty($search_term))
            {
                $qry->whereRaw("trs.remark like '%$search_term%'");
            }
            if (isset($wallet_id) && !empty($wallet_id))
            {
                $qry->where("trs.wallet_id", $wallet_id);
            }
            if (isset($currency_id) && !empty($currency_id))
            {
                $qry->where("trs.currency_id", $currency_id);
            }
            if (isset($orderby) && isset($order)) {
                $qry->orderBy($orderby, $order);
            }
            else {
                $qry->orderBy('trs.id', 'DESC');
                $qry->orderBy('trs.created_on', 'DESC');
            }
            if (isset($length) && !empty($length)) {
                $qry->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
			
                return $qry->count();
            }
            else   
            {		
                $qry->join($this->config->get('tables.STATEMENT_LINE').' as st', function($join)
                {
                    $join->on('st.statementline_id', '=', 'trs.statementline_id');
                    $join->where('st.tax_type', '=', $this->config->get('constants.TAX_TYPE.INCOME'));
                });
                $qry->leftJoin($this->config->get('tables.WALLET_LANG').' as b', function($join)
                {
                    $join->on('b.wallet_id', '=', 'trs.wallet_id');
                    $join->where('b.lang_id', '=', $this->config->get('app.locale_id'));
                });              
                $qry->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'trs.currency_id');
                $qry->select(DB::Raw('trs.id,trs.statementline_id,trs.account_id,trs.created_on,trs.transaction_id,trs.amt as amount,trs.tax,trs.transaction_type,st.statementline,trs.remark,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,b.wallet'));
                $transactions = $qry->get();			
                if ($transactions){
					array_walk($transactions, function(&$t)	{
						$t->created_on = date('d M Y', strtotime($t->created_on));
						if (!empty($t->remark) && strpos($t->remark, '}') > 0) {
							$t->order_code = (isset($ordDetails->data->order_code)) ? $ordDetails->data->order_code : '';                
							$t->remark = $ordDetails = json_decode(stripslashes($t->remark));							
							$t->statementline = trans('transactions.'.$t->statementline_id.'.user.statement_line', array_merge((array) $t->remark->data, array_except((array) $t,['remark'])));
							$t->remark = trans('transactions.'.$t->statementline_id.'.user.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
						}
						else {
							$t->remark = $t->statementline;
						}	
						$t->amount = \CommonLib::currency_format($t->amount, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);
						$t->tax = \CommonLib::currency_format($t->tax, ['currency_symbol'=>$t->currency_symbol, 'currency_code'=>$t->currency_code, 'value_type'=>(''), 'decimal_places'=>$t->decimal_places]);						
						unset($t->statementline);
					});
					return !empty($transactions) ? $transactions : [];					
				}
			}
        }
    }
	
	/* Franchisee Earned Commission */
	public function earned_commission($arr = array())
    { 
        extract($arr);   
		
        if (isset($account_id))
        {
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')		        
				->join($this->config->get('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'fc.statementline_id')	
				->join($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fct', function($join){
					$join->on('fct.commission_type_id', ' = ', 'fc.commission_type');
				 })
				->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fc.currency_id')
				
		        ->where('fc.is_deleted',$this->config->get('constants.OFF'))
				->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'));
			    if (isset($account_id) && !empty($account_id)){
                     $qry->where('fc.account_id', $account_id);
			   }
				if (isset($from) && isset($to) && !empty($from) && !empty($to))
				{
					$qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($from))."'");
					$qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($to))."'");
				} 
			   else if (isset($from) && !empty($from))
				{ 
					 $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($from))."'");
				}
			   else if (isset($to) && !empty($to))
				{
					   $qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($to))."'");
				}		
					   
				if (isset($orderby) && isset($order)) {
					$qry->orderBy($orderby, $order);
				}
				else {				
					$qry->orderBy('fc.fr_com_id', 'DESC');					
				} 
				if (isset($length) && !empty($length)) {
					$qry->skip($start)->take($length);
				}
				if (isset($count) && !empty($count)) {
				
					return $qry->count();
				} 
             else   
             { 	              
				 $qry->select('fc.fr_com_id','fc.created_date','fc.amount',DB::Raw('sum(fc.commission_amount) as commission_amount'),DB::Raw('sum(fc.net_pay) as net_pay'),DB::raw('DATE_FORMAT(created_date,\'%m-%Y\') as month'),'fc.remark','fc.statementline_id','fc.status','fc.confirmed_date','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places','st.statementline','fc.tax','fc.commission_type as commission_type_id','fc.from_date','fct.commission_type','fct.fct_code');  
                  $qry->groupby('month','commission_type_id');		 
                 $commission = $qry->get();		
                if ($commission){
					array_walk($commission, function(&$c)	{
						/* $c->created_date = !empty($c->created_date) ? showUTZ($c->created_date,'M-Y'):''; */
						
						$c->from_date = !empty($c->from_date) ? showUTZ($c->from_date,'M-Y'):'';
						
						
						$c->confirmed_date = !empty($c->confirmed_date) ? showUTZ($c->confirmed_date):'';
						if (!empty($c->remark) && strpos($c->remark, '}') > 0) {		
							$c->remark = json_decode(stripslashes($c->remark));			
							 $c->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark']))); 
						}
						else {
							$c->remark = $c->statementline;
						}
						$c->commission_type= trans('transactions.franchisee_commission_type.'.$c->commission_type_id.'');
						$c->amount = \CommonLib::currency_format($c->amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						$c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						
						$c->net_pay = \CommonLib::currency_format($c->net_pay, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						
						unset($c->statementline,$c->statementline_id);
					   
					 });		
				  return !empty($commission) ? $commission : [];				
				}
			} 
        }
		return NULL;
    }
  public function earned_commission_details($arr = array())
    { 
        extract($arr);   
        if (isset($account_id))
        {
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')
		        ->join($this->config->get('tables.FRANCHISEE_FUND_TRANSFER').' as fft', 'fft.fft_id', '=', 'fc.relation_id')
		        ->join($this->config->get('tables.ACCOUNT_MST').' as fam', 'fam.account_id', '=', 'fft.from_account_id')
		        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as fad', 'fad.account_id', '=', 'fam.account_id')
				->join($this->config->get('tables.ACCOUNT_MST').' as tam', 'tam.account_id', '=', 'fft.to_account_id')
		        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as tad', 'tad.account_id', '=', 'tam.account_id')		
				->join($this->config->get('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'fc.statementline_id')	
				->join($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fct', function($join){
					$join->on('fct.commission_type_id', ' = ', 'fc.commission_type');
				})
		        ->join($this->config->get('tables.FRANCHISEE_COMMISSION_STATUS_LOOKUP').' as fsl', 'fsl.com_status_id', '=', 'fc.status')
				
				->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fc.currency_id')
		        ->where('fc.is_deleted',$this->config->get('constants.OFF'))
		        ->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'));
			    if (isset($account_id) && !empty($account_id)){
                     $qry->where('fc.account_id', $account_id);
			   }
			   if(isset($commission_type) && !empty($commission_type)){
				    $qry->where('fct.fct_code', $commission_type);
			   }
		       if (isset($created_on_date) &&  !empty($created_on_date)){
					 $qry->whereRaw("MONTH(fc.from_date) ='".date('m', strtotime($created_on_date))."'");
					 $qry->whereRaw("YEAR(fc.from_date) ='".date('Y', strtotime($created_on_date))."'");
		       }
			if (isset($from) && isset($to) && !empty($from) && !empty($to))
		    {
				$qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($from))."'");
                $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($to))."'");
		    } 
		   else if (isset($from) && !empty($from))
		    { 
				 $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($from))."'");
		    }
		   else if (isset($to) && !empty($to))
		    {
				   $qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($to))."'");
		    }
			    
			 if (isset($search_term) && !empty($search_term))
             { 
		        if(is_numeric($search_term)){			
				    $qry->where(function($query) use ($search_term){
						$query->where('fam.user_code', 'LIKE', '%'.$search_term.'%')
							->orwhere('tam.user_code', 'LIKE', '%'.$search_term.'%');			
				    });
				}else{			
					$qry->where(function($query) use ($search_term){
							$query->where(DB::Raw('concat_ws(" ",fad.firstname,fad.lastname) '),'LIKE', '%'.$search_term.'%')
								->orwhere(DB::Raw('concat_ws(" ",tad.firstname,tad.lastname) '),'LIKE', '%'.$search_term.'%');			
					});
				}				
			}         
             if (isset($orderby) && isset($order)) {
                $qry->orderBy($orderby, $order);
            }
            else {				
                $qry->orderBy('fc.fr_com_id', 'DESC');
                $qry->orderBy('fc.created_date', 'DESC');
            } 
            if (isset($length) && !empty($length)) {
                $qry->skip($start)->take($length);
            }
            if (isset($count) && !empty($count)) {
			
                return $qry->count();
            } 
             else   
             { 	
                  $qry->select('fc.fr_com_id','fc.created_date','fc.amount','fc.commission_amount','fc.remark','fc.statementline_id','fc.status','fc.confirmed_date','fam.user_code as from_user_code',DB::Raw('concat_ws(" ",fad.firstname,fad.lastname) as from_name'),DB::Raw('concat_ws(" ",tad.firstname,tad.lastname) as to_name'),'tam.user_code as to_user_code','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places','fsl.status_name','fsl.label_class','st.statementline','fc.tax','fc.net_pay','fft.from_account_id','fc.commission_type as commission_type_id','fct.commission_type','fct.fct_code','fft.amount as transferred_amount');
                  $commission = $qry->get();		
                if ($commission){
					array_walk($commission, function(&$c)	{
						$c->created_date = !empty($c->created_date) ? showUTZ($c->created_date,'d-M-Y H:i:s'):'';
						$c->confirmed_date = !empty($c->confirmed_date) ? showUTZ($c->confirmed_date):'';
						if (!empty($c->remark) && strpos($c->remark, '}') > 0) {		
							$c->remark = json_decode(stripslashes($c->remark));			
							$c->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark'])));
						}
						else {
							$c->remark = $c->statementline;
						}
						$c->commission_type= \trans('transactions.franchisee_commission_type.'.$c->commission_type_id.''); 
						$c->amount = \CommonLib::currency_format($c->amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						
						$c->transferred_amount = \CommonLib::currency_format($c->transferred_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						
						$c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						
						$c->tax = \CommonLib::currency_format($c->tax, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						
						$c->net_pay = \CommonLib::currency_format($c->net_pay, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
													
						unset($c->statementline,$c->statementline_id);
					   
					 });					
				  return !empty($commission) ? $commission : [];				
				}
			} 
        }
		return NULL;
    } 
	
	public function Merchant_enrollment_fee($arr = array()){
		 extract($arr);   
        if (isset($account_id))
        {
		  $qry = DB::table($this->config->get('tables.FRANCHISEE_MERCHANT_FEE').' as fm')
		             ->join($this->config->get('tables.STORES').' as st', 'st.store_id', '=', 'fm.store_id')
			         ->join($this->config->get('tables.ADDRESS_MST').' as am', function($join){
					     $join->on('am.relative_post_id', ' = ', 'st.store_id')
							->where('am.post_type','=',$this->config->get('constants.ADDRESS_POST_TYPE.STORE'))
							->where('am.address_type_id','=',$this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
				       })
					  ->join($this->config->get('tables.LOCATION_DISTRICTS').' as lod', 'lod.district_id', '=', 'fm.district_id')
					  ->join($this->config->get('tables.LOCATION_STATE').' as los', 'los.state_id', '=', 'fm.state_id')
					  ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fm.currency_id')
					   
				      ->where('fm.account_id',$account_id)
					  ->where('fm.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'))
					  ->where('fm.is_deleted',$this->config->get('constants.OFF')); 
					  
						if (isset($created_on_date) &&  !empty($created_on_date)){
								 $qry->whereRaw("MONTH(fm.created_on) ='".date('m', strtotime($created_on_date))."'");
								 $qry->whereRaw("YEAR(fm.created_on) ='".date('Y', strtotime($created_on_date))."'");
						 }
						 
						if (isset($from) && isset($to) && !empty($from) && !empty($to)){
							$qry->whereRaw("DATE(fm.created_on) >='".date('Y-m-d', strtotime($from))."'");
							$qry->whereRaw("DATE(fm.created_on) <='".date('Y-m-d', strtotime($to))."'");
						} 
					   else if (isset($from) && !empty($from)){ 
							 $qry->whereRaw("DATE(fm.created_on) <='".date('Y-m-d', strtotime($from))."'");
						}
					   else if (isset($to) && !empty($to)){
							   $qry->whereRaw("DATE(fm.created_on) >='".date('Y-m-d', strtotime($to))."'");
						}
					 if (isset($search_term) && !empty($search_term))
					 { 
						if(is_numeric($search_term)){			
							$qry->where(function($query) use ($search_term){
								$query->where('fam.user_code', 'LIKE', '%'.$search_term.'%')
									->orwhere('tam.user_code', 'LIKE', '%'.$search_term.'%');			
							});
						}else{			
							$qry->where(function($query) use ($search_term){
									$query->where(DB::Raw('concat_ws(" ",fad.firstname,fad.lastname) '),'LIKE', '%'.$search_term.'%')
										->orwhere(DB::Raw('concat_ws(" ",tad.firstname,tad.lastname) '),'LIKE', '%'.$search_term.'%');
							});
						}				
					}         
					 if (isset($orderby) && isset($order)) {
						$qry->orderBy($orderby, $order);
					}
					else {				
						$qry->orderBy('fm.fr_fee_id', 'DESC');
						//$qry->orderBy('fm.created_on', 'DESC');
					} 
					if (isset($length) && !empty($length)) {
						$qry->skip($start)->take($length);
					}
					if (isset($count) && !empty($count)) {
					
						return $qry->count();
					} 
				 else   
				 { 	
				 $qry->select('fm.account_id','fm.store_id','fm.state_id','fm.district_id','fm.commission_amount','fm.created_on','st.store_name','st.store_code','am.address','lod.district','los.state','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places'); 
					  $commission = $qry->get();		 
					 if ($commission){
						array_walk($commission, function(&$c)	{
							$c->created_on = !empty($c->created_on) ? showUTZ($c->created_on,'d-M-Y H:i:s'):'';
						   $c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]); 
							
						 });			
					  return !empty($commission) ? $commission : [];	
					}   /*  print_r($commission); die;	 */
				} 
        }
		return NULL;
	
	}
	public function get_franchisee_profit_sharing($arr = array()){
	
		 extract($arr); 
		 if (isset($account_id))
         {
	        $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')		        
				->join($this->config->get('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'fc.statementline_id')	
				->join($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fct', function($join){
					$join->on('fct.commission_type_id', ' = ', 'fc.commission_type');
				 })
				->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'fc.currency_id')
				
				->where('fc.commission_type',$this->config->get('constants.FRANCHISEE_COMMISSION_TYPE.PS'))
		        ->where('fc.is_deleted',$this->config->get('constants.OFF'))
				->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'));
				
			    if (isset($account_id) && !empty($account_id)){
                     $qry->where('fc.account_id', $account_id);
			   }
			   if (isset($created_on_date) &&  !empty($created_on_date)){
								 $qry->whereRaw("MONTH(fc.from_date) ='".date('m', strtotime($created_on_date))."'");
								 $qry->whereRaw("YEAR(fc .from_date) ='".date('Y', strtotime($created_on_date))."'");
			     }
				if (isset($from) && isset($to) && !empty($from) && !empty($to))
				{
					$qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($from))."'");
					$qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($to))."'");
				} 
			   else if (isset($from) && !empty($from))
				{ 
					 $qry->whereRaw("DATE(fc.created_date) <='".date('Y-m-d', strtotime($from))."'");
				}
			   else if (isset($to) && !empty($to))
				{
					   $qry->whereRaw("DATE(fc.created_date) >='".date('Y-m-d', strtotime($to))."'");
				}		
					   
				if (isset($orderby) && isset($order)) {
					$qry->orderBy($orderby, $order);
				}
				else {				
					$qry->orderBy('fc.fr_com_id', 'DESC');					
				} 
				if (isset($length) && !empty($length)) {
					$qry->skip($start)->take($length);
				}
				if (isset($count) && !empty($count)) {
				
					return $qry->count();
				} 
             else   
             { 	              
				 $qry->select('fc.fr_com_id','fc.created_date','fc.amount',DB::Raw('sum(fc.commission_amount) as commission_amount'),DB::Raw('sum(fc.net_pay) as net_pay'),DB::raw('DATE_FORMAT(created_date,\'%m-%Y\') as month'),'fc.remark','fc.statementline_id','fc.status','fc.confirmed_date','cur.currency_symbol','cur.currency as currency_code','cur.decimal_places','st.statementline','fc.tax','fc.commission_type as commission_type_id','fc.from_date','fc.commission_perc','fct.commission_type','fct.fct_code');  
                  $qry->groupby('month','commission_type_id');		 
                 $commission = $qry->get();		
                if ($commission){
					array_walk($commission, function(&$c)	{
						/* $c->created_date = !empty($c->created_date) ? showUTZ($c->created_date,'M-Y'):''; */
						
						$c->from_date = !empty($c->from_date) ? showUTZ($c->from_date,'M-Y'):'';
						$c->commission_perc=!empty($c->commission_perc)? $c->commission_perc.' %' :'';
						
						$c->confirmed_date = !empty($c->confirmed_date) ? showUTZ($c->confirmed_date):'';
						if (!empty($c->remark) && strpos($c->remark, '}') > 0) {		
							$c->remark = json_decode(stripslashes($c->remark));			
							 $c->statementline = trans('transactions.'.$c->statementline_id.'.franchisee.statement_line', array_merge((array) $c->remark->data, array_except((array) $c,['remark']))); 
						}
						else {
							$c->remark = $c->statementline;
						}
						$c->commission_type= trans('transactions.franchisee_commission_type.'.$c->commission_type_id.'');
						$c->amount = \CommonLib::currency_format($c->amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						$c->commission_amount = \CommonLib::currency_format($c->commission_amount, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);	
						$c->net_pay = \CommonLib::currency_format($c->net_pay, ['currency_symbol'=>$c->currency_symbol, 'currency_code'=>$c->currency_code, 'value_type'=>(''), 'decimal_places'=>$c->decimal_places]);
						
						unset($c->statementline,$c->statementline_id);
					   
					 });		
				  return !empty($commission) ? $commission : [];				
				}
			} 
        }
		return NULL;
	}
	public function commission_type($arr = array()){
		
		  $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_TYPE_LOOKUPS').' as fc')
				 ->where('fc.fct_code', $arr)
				 ->where('fc.is_deleted',$this->config->get('constants.OFF'))
				 ->select('fc.commission_type_id');
				   $res = $qry->first();		
					   if(!empty($res)) {
						    return $res;
					 }
	         }
  public function getTotalEarnings($arr = array()){
	
	    extract($arr);		
	    $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')		       
	             ->where('fc.is_deleted',$this->config->get('constants.OFF'))
				 ->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'))
			   	  ->where('fc.account_id', $account_id)
				  ->where('fc.currency_id', $this->userSess->currency_id)
			      ->select(DB::Raw('sum(fc.commission_amount) as commission_amount'));
				  $res = $qry->first();	
				  if(!empty($res)){
						$currencyInfo = $this->commonstObj->get_currency($this->userSess->currency_id);					  
					  
					   $res->commission_amount = \CommonLib::currency_format($res->commission_amount, ['currency_symbol'=>$currencyInfo->currency_symbol, 'currency_code'=>$currencyInfo->currency_code, 'value_type'=>(''), 'decimal_places'=>$currencyInfo->decimal_places]);	
					 return !empty($res) ? $res : [];		
				 }  
          }  
	public function getTodayEarnings($arr = array()){
		 extract($arr);
		 $cur_date= showUTZ('Y-m-d');
		     $qry = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION').' as fc')			       
		            ->where('fc.is_deleted',$this->config->get('constants.OFF'))
				    ->where('fc.status',$this->config->get('constants.FRANCHISEE_COMMISSION_STATUS.CONFIRMED'))
				    ->where('fc.account_id', $account_id)
					->where('fc.currency_id', $this->userSess->currency_id)
				    ->whereDate("fc.created_date", "=", getGTZ('Y-m-d', $cur_date))
				    ->select(DB::Raw('sum(fc.commission_amount) as commission_amount'));
					  $res = $qry->first();	
					if(!empty($res)){
							$currencyInfo = $this->commonstObj->get_currency($this->userSess->currency_id);		
					       $res->commission_amount = \CommonLib::currency_format($res->commission_amount, ['currency_symbol'=>$currencyInfo->currency_symbol, 'currency_code'=>$currencyInfo->currency_code, 'value_type'=>(''), 'decimal_places'=>$currencyInfo->decimal_places]);	
						 return !empty($res) ? $res : [];		
				 }  
	     }
	public function activity_log_details($arr = array()){
		extract($arr);   
		
	  if (isset($account_id)){
		  $qry = DB::table($this->config->get('tables.ACCOUNT_LOG').' as al')
		          ->join($this->config->get('tables.DEVICE_LOG').' as dl', 'dl.device_log_id', '=', 'al.device_id')
				    ->where('al.is_deleted',$this->config->get('constants.OFF'))
					->where('al.account_id', $account_id);
			    if (isset($from) && isset($to) && !empty($from) && !empty($to))	{ 
					 $qry->whereDate('al.account_log_time', '>=', getGTZ($from,'Y-m-d'));
					 $qry->whereDate('al.account_log_time', '<=', getGTZ($to,'Y-m-d'));
				}
				else if (!empty($from) && isset($from)){ 
					 $qry->whereDate('al.account_log_time', '<=', getGTZ($from,'Y-m-d'));
				}
				else if (!empty($to) && isset($to)){ 
					 $qry->whereDate('al.account_log_time', '>=', getGTZ($to,'Y-m-d'));
				} 
				if (isset($orderby) && isset($order)) {
					$qry->orderBy($orderby, $order);
				}
				else {				
					$qry->orderBy('al.account_log_id', 'DESC');
				} 
				if (isset($length) && !empty($length)) {
					$qry->skip($start)->take($length);
				}
				if (isset($count) && !empty($count)) {
					return $qry->count();
				} 
			   else   
               { 	
                  $qry->select('al.account_log_id','al.account_id','al.account_login_ip','al.account_log_time','dl.device_info');
                  $log_details = $qry->get();		
					  if ($log_details) {
						   array_walk($log_details, function(&$c)	{
							   $c->account_log_time = !empty($c->account_log_time) ? showUTZ($c->account_log_time,'d-M-Y H:i:s'):'';
							   $c->log_message='LOGIN';
						  });					
					        return !empty($log_details) ? $log_details : [];				
					}
			 }
		 }
		 return NULL;
	}
};