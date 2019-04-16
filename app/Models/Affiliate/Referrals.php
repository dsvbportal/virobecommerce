<?php
namespace App\Models\Affiliate;
use App\Models\BaseModel;
use DB;
class Referrals extends BaseModel
{
    public function __construct ()
    {
        parent::__construct();
    }
	public function my_referred_customers ($account_id, $arr =[])
    {        
        extract($arr);		
        if (!empty($account_id))
        {					
			$query =DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap')			         
					->join($this->config->get('tables.ACCOUNT_MST').' as acm', 'acm.account_id', '=', 'ap.account_id')
					->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'acm.account_id')	
					->join($this->config->get('tables.LOCATION_COUNTRY').' as cnty', 'cnty.country_id', ' = ', 'ap.country_id')
					->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', ' = ', 'ap.currency_id')
					->where('acm.is_deleted', '=', $this->config->get('constants.OFF'))	
					->where('ap.referral_account_id', '=', $account_id);					
					
			if (isset($from) && isset($to) && !empty($from) && !empty($to))
			{
				$query->whereDate('acm.signedup_on', '>=', getGTZ($from,'Y-m-d'));
				$query->whereDate('acm.signedup_on', '<=', getGTZ($to,'Y-m-d'));
			} 
			if (isset($from) && !empty($from))
			{ 
				$query->whereDate('acm.signedup_on', '<=', getGTZ($from,'Y-m-d'));
			}
			if (isset($to) && !empty($to))
			{
				$query->whereDate('acm.signedup_on', '>=', getGTZ($to,'Y-m-d'));
			}	
            if (isset($search_term) && !empty($search_term))
            {		        
                if (!empty($filterchk) && !empty($filterchk))
                {
                    $search_term = '%'.$search_term.'%';
                    $search_field = ['FirstName'=>'ad.firstname', 'UserCode'=>'acm.user_code'];
                    $query->where(function($sub) use($filterchk,$search_term,$search_field)
                    {
                        foreach ($filterchk as $search)
                        {  
                            if (array_key_exists($search,$search_field))
                            { 
                                $sub->orWhere(DB::raw($search_field[$search]),'like',$search_term);
                            }
                        }
                    });
                }
                else
                {
                    $query->where(function($sub) use($search_term)
                    {
					    $sub->Where('ad.firstname','like',$search_term)
						  	->orwhere('acm.user_code','like',$search_term);						
                    });
                }
            }
            if (isset($length) && !empty($length))
            {
                $query->skip($start)->take($length);
            }
            if (isset($count) && !empty($count))
            {
                return $query->count();
            }
            else
            {               
                $query->selectRAW('acm.signedup_on,acm.user_code,ad.firstname,cnty.country,cur.currency_symbol,cur.currency as currency_code,cur.decimal_places,(select concat_ws(" ",SUM(bill_amount),SUM(cv)) from '.$this->config->get('tables.PERSONAL_BONUS_MONTHLY_DETAILS').' where account_id = ap.account_id) as sales_qv')				      
				      ->orderBy('acm.signedup_on', 'DESC');
                $result = $query->get();	
                if (!empty($result))
                {
					foreach($result as $res)
					{	
					    $res->signedup_on = !empty($res->signedup_on) ? showUTZ($res->signedup_on):'';
						$amt = explode(" ",$res->sales_qv);						
						$res->sales_tot = isset($amt[0]) && !empty($amt[0]) ? number_format($amt[0], \AppService::decimal_places($amt[0]), '.', ',').' '.$res->currency_code : number_format(0, \AppService::decimal_places(0), '.', ',').' '.$res->currency_code;		
						$res->cv_tot = isset($amt[1]) && !empty($amt[0]) ? number_format($amt[1], \AppService::decimal_places($amt[1]), '.', ',') : 0;
					}
                } 
                return $result;
            }
        }
		return NULL;
    }
	
    public function my_referrals ($account_id, $arr)
    {
        $gen_data = '';
        $orderby = 'signedup_on';
        $order = 'DESC';
        extract($arr);
        if (!empty($account_id))
        {
			if(isset($generation) && $generation>0){
                $generationinfo = $this->getDirects_treeinfo($account_id,$generation);
			}
            $subqry = DB::table(config('tables.ACCOUNT_TREE').' as utr')
                    ->join(config('tables.ACCOUNT_MST').' as um', function($join)
                    {
                        $join->on('um.account_id', ' = ', 'utr.account_id');
                    })
                    ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id');

            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $subqry->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from))."'");
                $subqry->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (!empty($from) && isset($from))
            {
                $subqry->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $subqry->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($to))."'");
            }

            if (isset($search_term) && !empty($search_term))
            {
                if (!empty($filterchk) && !empty($filterchk))
                {
                    $search_term = '%'.$search_term.'%';
                    $search_field = ['UserName'=>'um.uname', 'Fullname'=>'CONCAT_WS("",ud.firstname,ud.lastname)', 'Mobile'=>'ud.mobile', 'Sponsor'=>'upum.uname'];
                    $subqry->where(function($sub) use($filterchk, $search_term, $search_field)
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
                    $subqry->where(function($wcond) use($search_term)
                    {
                        $subqry->WhereRaw("um.uname like '%$search_term%'")
                                ->orwhereRaw("concat_ws('',ud.firstname,ud.lastname) like '%$search_term%'")
                                ->orwhereRaw("CONCAT_WS('',ud.mobile) like '%$search_term%'");
                    });
                }
            }

			if (isset($account_id) && isset($account_id))
            {
                $subqry->where('utr.sponsor_id', $account_id);
            }
			if(isset($generationinfo) && !empty($generationinfo))
			{
                   $subqry->where('utr.lft_node', '>=', $generationinfo[0]->lft_node)
                        ->where('utr.rgt_node', '<=', $generationinfo[0]->rgt_node)
						->where('utr.nwroot_id', '=', $generationinfo[0]->nwroot_id);
			}
            if (isset($orderby) && isset($order))
            {
                if ($orderby == 'uname')
                {
                    $subqry->orderBy('um.'.$orderby, $order);
                }
                elseif ($orderby == 'mobile')
                {
                    $subqry->orderBy('ud.'.$orderby, $order);
                }
                elseif ($orderby == 'level')
                {
                    $subqry->orderBy('utr.'.$orderby, $order);
                }
                elseif ($orderby == 'signedup_on')
                {
                    $subqry->orderBy('um.'.$orderby, $order);
                }
                elseif ($orderby == 'recent_package_purchased_on')
                {
                    $subqry->orderBy('utr.'.$orderby, $order);
                }
            }

            if (isset($length) && !empty($length))
            {
                $subqry->skip($start)->take($length);
            }

            if (isset($count) && !empty($count))
            {
                return $subqry->count();
            }
           else
            {
                $subqry->select('utr.account_id','um.user_code','utr.lft_node', 'utr.rgt_node','utr.pro_rank_id', 'utr.can_sponsor','utr.created_on as signedup_on' ,'utr.activated_on','um.uname', 'um.mobile', DB::Raw("(select uname from account_mst where account_id=utr.upline_id) as upline_name"), DB::Raw("CONCAT_WS(' ',ud.firstname,ud.lastname) as full_name"), 'um.email', 'um.status', 'utr.sponsor_id', 'utr.recent_package_purchased_on', 'utr.cv', 'utr.qv', 'utr.level', 'utr.rank', 'utr.upline_id', 'utr.recent_package_id');
             
                $pkQry = DB::table(config('tables.ACCOUNT_SUBSCRIPTION').' as asub')
                        ->where('asub.account_id', ' = ', 'ut.account_id')
                        ->where('asub.status', ' = ', $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED'))
                        ->where('asub.payment_status', ' = ', $this->config->get('constants.PAYMENT_STATUS.CONFIRMED'))
                        ->selectRaw("sum(asub.amount)")
                        ->groupby('asub.account_id');

                $query = DB::table(DB::raw('('.$subqry->tosql().') as ut'))
                        ->addBinding($subqry->getBindings(), 'join')
                        ->leftjoin(config('tables.ADDRESS_MST').' as adm', function($join)
                        {
                            $join->on('adm.relative_post_id', ' = ', 'ut.account_id')
                            ->where('adm.post_type', ' = ', $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
							->where('adm.address_type_id', ' = ', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
                        })
                        ->leftjoin(config('tables.LOCATION_CITY').' as cty', 'cty.city_id', ' = ', 'adm.city_id')
                        ->leftjoin(config('tables.LOCATION_COUNTRY').' as cnty', 'cnty.country_id', ' = ', 'adm.country_id')
                        ->leftjoin(config('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id')
						->leftjoin(config('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id')                        
						->leftjoin(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'ut.status')                        
                        ->leftjoin(config('tables.AFF_RANKING_LOOKUPS').' as af_r', 'af_r.af_rank_id', ' = ', 'ut.pro_rank_id')
						
                        ->leftjoin(config('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
                         {
							$subquery->on('pl.package_id', ' = ', 'ut.recent_package_id')
							->where('pl.lang_id', '=', 1);
                         });

              $query->select('ut.account_id','ut.full_name','ut.user_code','ut.email','ut.can_sponsor', 'ut.lft_node', 'ut.rgt_node', 'adm.city_id','cnty.country','ut.signedup_on', 'ut.activated_on', 'ut.uname', 'ut.rank as direrank', 'ut.mobile','af_r.rank',DB::RAW("IF(upum.uname IS NOT NULL,upum.uname,'') as upline_uname"),DB::RAW("IF(upum.user_code IS NOT NULL,upum.user_code,'') as upline_code"), DB::Raw("IF(ut.email IS NOT NULL ,ut.email,'') as email"), 'ut.status', 'ut.sponsor_id', 'spum.uname as sponsor_uname',"spum.user_code as sponsor_code",'ut.recent_package_purchased_on', DB::Raw('('.$pkQry->tosql().') as package_amount'),'ut.cv', 'ut.qv','ut.level', DB::Raw("IF(pl.package_name IS NOT NULL,pl.package_name,'') as package_name"),DB::Raw("CONCAT_WS(',  ',cty.city,cnty.country) as location"),DB::Raw("IF(ut.mobile IS NOT NULL,CONCAT_WS('-',cnty.phonecode,ut.mobile),'') as mobile"));
                $query->addBinding($pkQry->getBindings(), 'select');

                $query = $query->get();

                if ($query)
                {
                    $spDirectsList = $this->getDirects_treeinfo($account_id);
//print_r($spDirectsList);
                    array_walk($query, function(&$data) use($parent_details, $spDirectsList)
                    {
                        $data->package_amount = number_format($data->package_amount, \AppService::decimal_places($data->package_amount), '.', ',');
                        if ($data->can_sponsor == 1 && $data->qv > 0)
                        {
                            $data->status = 'Active';
                            $data->status_class = 'success';
                        }
                        else if (empty($data->qv) && $data->cv > 0)
                        {
                            $data->status = 'Client';
                            $data->status_class = 'maroon';
                        }
                        else if ($data->qv == 0 && $data->cv == 0)
                        {
                            $data->status = '';
                            $data->status_class = '';
                        }						
                        $data->location = !empty($data->location) ? $data->location : '';
                        $data->upline_uname = !empty($data->upline_uname) ? $data->upline_uname : '';
                        $data->qv = number_format($data->qv, 0);
                        $data->cv = number_format($data->cv, 0);
                        $data->generation = !empty($data->upline_uname) ? $this->findDownline_Generation($data, $spDirectsList, 'G') : '';
                        $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y');
						$data->activated_on = showUTZ($data->activated_on, 'd-M-Y');
                        $data->recent_package_purchased_on = showUTZ($data->recent_package_purchased_on, 'd-M-Y');
                        $data->mobile  = '';
						$data->email = ''; 
                    });
                }
                return $query;
            } 
        }
    }

    public function findDownline_Generation ($acInfo, $spDirectsList, $postfix = '')
    {
        $g = '';
        if (!empty($spDirectsList))
        {
            foreach ($spDirectsList as $ac)
            {
                if (($acInfo->lft_node > $ac->lft_node && $acInfo->lft_node < $ac->rgt_node) || 
					($acInfo->lft_node == $ac->lft_node && $acInfo->rgt_node == $ac->rgt_node))
                {
                    $g = $ac->rank.$postfix;
                    break;
                }				
            }
        }
        return $g;
    }

    public function getUser_lineage ($params = array())
    {
        extract($params);

        if (!empty($params) && $params['account_id'] > 0)
        {
            $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as act');

            $qry->where('act.account_id', '=', $account_id);

            $qry->select('act.account_id', 'act.upline_id', 'act.sponsor_id', 'act.my_extream_right', 'act.rank', 'act.level', DB::Raw("((act.rgt_node-act.lft_node) DIV 2) as team_count"), 'act.nwroot_id', 'act.lft_node', 'act.rgt_node');
            $res = $qry->first();

            if ($res)
            {
                return $res;
            }
        }
        return NULL;
    }

    public function getUser_treeInfo ($params = array())
    {
        extract($params);

        if (!empty($params))
        {
            $qry = '';
            if (isset($params['account_id']))
            {
                $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as act')
                        ->join($this->config->get('tables.ACCOUNT_MST').' as acm', 'acm.account_id', '=', 'act.account_id');
                $qry->where('act.account_id', '=', $account_id);
            }
            else if (isset($params['uname']))
            {
                $qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as acm')
                        ->join($this->config->get('tables.ACCOUNT_TREE').' as act', 'acm.account_id', '=', 'act.account_id');
                $qry->where('acm.uname', '=', $uname);
            }
            if (!empty($qry))
            {
                $qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'act.account_id')
                        ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'act.upline_id')
                        ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'act.sponsor_id');

                $qry->where('acm.is_deleted', $this->config->get('constants.OFF'));

                $qry->select('act.account_id','acm.user_code', 'acm.uname', DB::Raw("concat_ws(' ',acd.firstname,acd.lastname) as full_name"), 'act.upline_id', 'act.sponsor_id', 'act.my_extream_right', 'act.rank', 'act.level', 'acm.signedup_on', 'acm.activated_on', 'acm.block', 'acm.uname', DB::Raw("((act.rgt_node-act.lft_node) DIV 2) as team_count"), 'act.nwroot_id', 'act.lft_node', 'act.rgt_node', 'act.qv', 'act.cv', 'act.referral_cnts', 'act.referral_paid_cnts', 'act.can_sponsor', 'acm.status', 'upum.uname as upline_uname',  'upum.user_code as upline_user_code', 'spum.uname as sponser_uname','spum.user_code as sponser_user_code');
                $res = $qry->first();

                if ($res)
                {
                    return $res;
                }
            }
        }
        return NULL;
    }

    public function my_team_reports ($account_id, $arr)
    {
        $gen_data = '';
        extract($arr);
        if (!empty($account_id))
        {
			if(isset($generation)){
                 $generationinfo = $this->getDirects_treeinfo($account_id,$generation);
			}
			
            $subqry = DB::table(config('tables.ACCOUNT_TREE').' as utr')
                    ->join(config('tables.ACCOUNT_MST').' as um', function($join)
                    {
                        $join->on('um.account_id', ' = ', 'utr.account_id');
                    })
                    ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id');
			
			if(isset($generation) && $generation>0 && isset($generationinfo))
			{
				if(!empty($generationinfo)){
                   $subqry->where('utr.lft_node', '>=', $generationinfo[0]->lft_node)
                        ->where('utr.rgt_node', '<=', $generationinfo[0]->rgt_node)
						->where('utr.nwroot_id', '=', $generationinfo[0]->nwroot_id);
				}
				else {
					return [];
				}
			}
            else if (isset($account_id) && isset($account_id))
            {
                $subqry->where('utr.lft_node', '>', $parent_details->lft_node)
                        ->where('utr.rgt_node', '<', $parent_details->rgt_node)
                        ->where('utr.nwroot_id', '=', $parent_details->nwroot_id);
            }

            if (isset($from) && isset($to) && !empty($from) && !empty($to))
            {
                $subqry->whereRaw("DATE(utr.activated_on) >='".date('Y-m-d', strtotime($from))."'");
                $subqry->whereRaw("DATE(utr.activated_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            else if (!empty($from) && isset($from))
            {
                $subqry->whereRaw("DATE(utr.activated_on) <='".date('Y-m-d', strtotime($from))."'");
            }
            else if (!empty($to) && isset($to))
            {
                $subqry->whereRaw("DATE(utr.activated_on) >='".date('Y-m-d', strtotime($to))."'");
            }

            if (isset($search_term) && !empty($search_term))
            {
                if (!empty($filterchk) && !empty($filterchk))
                {
                    $search_term = '%'.$search_term.'%';
                    $search_field = ['UserName'=>'um.uname', 'Fullname'=>'CONCAT_WS("",ud.firstname,ud.lastname)', 'Mobile'=>'ud.mobile', 'Sponsor'=>'upum.uname'];
                    $subqry->where(function($sub) use($filterchk, $search_term, $search_field)
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
                    $subqry->where(function($wcond) use($search_term)
                    {
                        $subqry->WhereRaw("um.uname like '%$search_term%'")
                                ->orwhereRaw("concat_ws('',ud.firstname,ud.lastname) like '%$search_term%'")
                                ->orwhereRaw("CONCAT_WS('',ud.mobile) like '%$search_term%'");
                    });
                }
            }

            if (isset($length) && !empty($length))
            {
                $subqry->skip($start)->take($length);
            }

            if (isset($count) && !empty($count))
            {
                return $subqry->count();
            }
            else
            {
                $subqry->select('utr.account_id','utr.lft_node', 'utr.rgt_node', 'utr.can_sponsor','utr.created_on as signedup_on', 'utr.activated_on', 'um.uname','um.user_code','um.mobile', DB::Raw("(select uname from account_mst where account_id=utr.upline_id) as upline_name"), DB::Raw("CONCAT_WS(' ',ud.firstname,ud.lastname) as full_name"), 'um.email', 'um.status', 'utr.sponsor_id', 'utr.cv', 'utr.qv', 'utr.level', 'utr.rank', 'utr.upline_id', DB::Raw("(utr.level-".$parent_details->level.") as ref_level"), 'utr.pro_rank_id');

                $query = DB::table(DB::raw('('.$subqry->tosql().') as ut'))
                        ->addBinding($subqry->getBindings(), 'join')
                        ->leftjoin(config('tables.ADDRESS_MST').' as adm', function($join)
                        {
                            $join->on('adm.relative_post_id', ' = ', 'ut.account_id')
                            ->where('adm.post_type', ' = ', $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
							->where('adm.address_type_id', ' = ', $this->config->get('constants.ADDRESS_TYPE.PRIMARY'));
                        })
                        ->leftjoin(config('tables.LOCATION_CITY').' as cty', 'cty.city_id', ' = ', 'adm.city_id')
                        ->leftjoin(config('tables.LOCATION_COUNTRY').' as cnty', 'cnty.country_id', ' = ', 'adm.country_id')
                        ->join(config('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id')
                        ->join(config('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id')
                        ->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'ut.status')

						 ->leftjoin(config('tables.AFF_RANKING_LOOKUPS').' as af_r', 'af_r.af_rank_id', ' = ', 'ut.pro_rank_id')

                        ->leftJoin(config('tables.ACCOUNT_SALE_POINTS').' as asp', 'asp.account_id', ' = ', 'ut.account_id');
              $query->select('ut.account_id','ut.user_code','ut.can_sponsor', 'ut.lft_node','af_r.rank','ut.rgt_node','adm.city_id', 'cnty.country','ut.signedup_on','ut.activated_on', 'ut.uname','ut.rank as direrank', 'ut.mobile', DB::Raw("IF(ut.upline_id>0,(select uname from account_mst where account_id=ut.upline_id),'') as upline_name"),DB::Raw("IF(ut.upline_id>0,(select user_code from account_mst where account_id=ut.upline_id),'') as upline_user_code"),'ut.full_name', DB::Raw("IF(ut.email IS NOT NULL ,ut.email,'') as email"), 'ut.status', 'ut.sponsor_id', 'upum.uname as upline_uname', 'upum.user_code as upline_code', 'spum.uname as sponsor_uname', 'spum.user_code as sponsor_code', 'ut.cv', 'ut.qv', 'ut.level', 'ut.ref_level',DB::Raw("CONCAT_WS(', ',cty.city,cnty.country) as location"),DB::Raw("IF(ut.mobile IS NOT NULL,CONCAT_WS('-',cnty.phonecode,ut.mobile),'') as mobile"));
                $query->orderBy('ut.ref_level', 'ASC');
                $query->orderBy('ut.lft_node', 'ASC');

                $query = $query->get();

                if ($query)
                {
                    $spDirectsList = $this->getDirects_treeinfo($account_id);
                    array_walk($query, function(&$data) use($parent_details, $spDirectsList)
                    {
						if ($data->can_sponsor == 1 && $data->qv > 0)
                        {
                            $data->status = 'Active';
                            $data->status_class = 'success';
                        }
                        else if (empty($data->qv) && $data->cv > 0)
                        {
                            $data->status = 'Client';
                            $data->status_class = 'maroon';
                        }
                        else if ($data->qv == 0 && $data->cv == 0)
                        {
                            $data->status = 'Free';
                            $data->status_class = 'warning';
                        }
						$data->mobile =  '';
							$data->email =  '';
                        $data->location = !empty($data->location) ? $data->location : '';
                        $data->qv = number_format($data->qv, 0);
                        $data->cv = number_format($data->cv, 0);
                        $data->generation = !empty($data->upline_name) ? $this->findDownline_Generation($data, $spDirectsList, 'G') : '';
                        $data->ref_level = !empty($data->ref_level) ? $data->ref_level.' Level' : '';
                        $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y');
						$data->activated_on = showUTZ($data->activated_on, 'd-M-Y');
                    });
                }
                return $query;
            }
        }
    }

    public function getDirects_treeinfo ($account_id,$generation=0)
    {   
		if (!empty($account_id) && $account_id > 0)
        {
            $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut')
			           ->where('ut.upline_id', $account_id);
					if(isset($generation) && $generation>0){
					   $qry->where('ut.rank',$generation);
					 }
				   $qry->select('ut.lft_node', 'ut.rgt_node','ut.rank','ut.nwroot_id');
                   $res=  $qry->get();
       
			return !empty($res) ? $res : [];
        }
        return [];
    }
	
	/* public function getGenerationinfo($account_id,$generation){
		if (!empty($account_id) && $account_id > 0)
        {
		   $res = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut')
                    ->where('ut.upline_id', $account_id)
					->where('ut.rank',$generation)
                    ->select('ut.lft_node','ut.rgt_node','ut.nwroot_id','ut.account_id')
					 ->first();
				return !empty($res) ? $res : [];
		}				
		 return [];		
	} */

    public function my_directs ($account_id, $arr, $count = false)
    {
        extract($arr);
        $parent_details = $this->getUser_treeInfo(['account_id'=>$account_id]);

        if (!empty($account_id) && $parent_details)
        {
            $query = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id')
                    ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id')
                    ->join($this->config->get('tables.ACCOUNT_DETAILS').' as spud', 'spud.account_id', ' = ', 'upum.account_id')
                    ->join($this->config->get('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
                    /* ->join($this->config->get('tables.ACCOUNT_STATUS_LANG').' as uslg', function($subquery)
                      {
                      $subquery->on('uslg.status_id', ' = ', 'usl.status_id')
                      ->where('uslg.lang_id', '=', $this->config->get('app.locale_id'));
                      }) */
                    ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
                    {
                        $subquery->on('pl.package_id', ' = ', 'ut.recent_package_id')
                        ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                    })
                    ->select('ut.account_id','um.user_code', 'um.signedup_on', 'um.uname', 'um.mobile', DB::Raw("CONCAT_WS('',ud.firstname,ud.lastname) as full_name"), 'um.email', 'um.status', 'ut.sponsor_id', 'upum.uname as upline_uname', 'spum.uname as direct_sponser_uname', 'ut.recent_package_purchased_on', DB::Raw("(ut.level-".$parent_details->level.") as level"), 'ut.rank');


            if (!empty($from) && isset($from))
            {
                $query->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from))."'");
            }
            if (!empty($to) && isset($to))
            {
                $query->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            if (isset($search_term) && !empty($search_term))
            {
                // echo "DfsFD"; die;

                if (!empty($filterchk) && !empty($filterchk))
                {
                    $search_term = '%'.$search_term.'%';
                    $search_field = ['UserName'=>'um.uname', 'FullName'=>'CONCAT_WS("",ud.firstname,ud.lastname)', 'InvitedBy'=>'spum.uname'];
                    $query->where(function($sub) use($filterchk, $search_term, $search_field)
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
                    $query->where(function($wcond) use($search_term)
                    {
                        $wcond->Where('um.uname', 'like', $search_term)
                                ->orwhere(DB::raw('CONCAT_WS("",ud.firstname,ud.lastname)'), 'like', $search_term)
                                ->orwhere('spum.uname', 'like', $search_term);
                    });
                }
            }
            if (isset($account_id))
            {
                $query->where('ut.upline_id', $account_id);
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
                if (isset($orderby) && isset($order))
                {
                    if ($orderby == 'uname')
                    {
                        $query->orderBy('um.'.$orderby, $order);
                    }
                    elseif ($orderby == 'full_name')
                    {
                        $query->orderBy('ud.firstname', $order);
                    }
                    elseif ($orderby == 'sponser_uname')
                    {
                        $query->orderBy('spum.uname', $order);
                    }
                    elseif ($orderby == 'level')
                    {
                        $query->orderBy('ut.level', $order);
                    }
                    elseif ($orderby == 'package_name')
                    {
                        $query->orderBy('pl.'.$orderby, $order);
                    }
                    elseif ($orderby == 'purchased_on')
                    {
                        $query->orderBy('ut.recent_package_purchased_on', $order);
                    }
                    elseif ($orderby == 'signedup_on')
                    {
                        $query->orderBy('um.'.$orderby, $order);
                    }
                    elseif ($orderby == 'status')
                    {
                        $query->orderBy('um.'.$orderby, $order);
                    }
                }
                $query = $query->orderBy('ut.rank', 'ASC');
                $result = $query->get();

                if (!empty($result))
                {
                    return $result;
                }
            }
        }
        return NULL;
    }

    public function get_users_info ($user_id)
    {
        $logged_user_id = $user_id;

        if (!empty($logged_user_id))
        {
            $res = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                    ->leftJoin($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                    ->where('um.account_id', '=', $user_id)
                    ->where('um.is_deleted', '=', 0)
                    ->select(DB::raw('um.account_id,um.uname,concat(ud.firstname," ",ud.lastname) as full_name,um.activated_on,um.signedup_on'))
                    ->first();

            return !empty($res) ? $res : false;
        }
    }

    public function get_sponsor_users ($params)
    {
        extract($params);
        if (isset($parent_acinfo) && $account_id > 0)
        {
            $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id');
            $qry->where('ut.sponsor_id', '=', $account_id);
            $qry->where('um.is_deleted', $this->config->get('constants.NOT_DELETED'));
            $qry->orderBy('ut.rank', 'ASC');
            $qry->select(DB::raw("um.account_id,ut.sponsor_id,ut.upline_id,um.uname as username,concat_ws(' ',ud.firstname,ud.lastname) as fullname,spum.uname as sponser_uname,upum.uname as upline_uname,um.status,um.block,um.can_sponsor,(ut.level-".$parent_acinfo->level.") as level,um.signedup_on ,um.activated_on,um.block_login"));
            $result = $qry->get();
            if (!empty($result))
            {
                array_walk($result, function(&$ftdata)
                {
                    $ftdata->signedup_on = date('d-M-Y ', strtotime($ftdata->signedup_on));
                    $ftdata->activated_on = date('d-M-Y ', strtotime($ftdata->activated_on));
                });
                return !empty($result) ? $result : NULL;
            }
            return NULL;
        }
    }

    public function get_genealogy_users ($params)
    {
        extract($params);
        if (isset($parent_acinfo) && $account_id > 0)
        {
            $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'ut.account_id');
            $qry->leftjoin($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id');
            $qry->leftjoin($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id');
            $qry->where('ut.nwroot_id', '=', $parent_acinfo->nwroot_id);
            $qry->where('ut.level', '<=', $parent_acinfo->level + 2);
            $qry->where('ut.lft_node', '>', $parent_acinfo->lft_node);
            $qry->where('ut.rgt_node', '<', $parent_acinfo->rgt_node);
            $qry->where('um.is_deleted', $this->config->get('constants.OFF'));
            $qry->orderBy('ut.lft_node', 'ASC');
            $qry->select(DB::raw("um.account_id,um.user_code,um.uname as username,concat_ws(' ',ud.firstname,ud.lastname) as fullname,ut.qv,ut.cv,ut.upline_id,upum.uname as upline_uname,upum.user_code as upline_user_code,spum.uname as sponser_uname,spum.user_code as sponser_user_code,(select group_concat(up.rank ORDER BY up.lft_node ASC SEPARATOR '') from ".$this->config->get('tables.ACCOUNT_TREE')." as up where up.lft_node >= ".$parent_acinfo->lft_node." AND up.lft_node <= ut.lft_node AND up.rgt_node >= ut.rgt_node AND up.nwroot_id = ut.nwroot_id) as mypos,um.status,um.block,ut.can_sponsor,(ut.level-".$parent_acinfo->level.") as level,um.signedup_on,ut.activated_on,((ut.rgt_node-ut.lft_node) DIV 2) as team_count,ut.referral_cnts,ut.referral_paid_cnts,um.login_block"));
            $res = $qry->get();

            $genealogy1Arr2L = ['1'=>'', '11'=>'', '12'=>'', '13'=>''];
            $genealogy2Arr2L = ['2'=>'', '21'=>'', '22'=>'', '23'=>''];
            $genealogy3Arr2L = ['3'=>'', '31'=>'', '32'=>'', '33'=>''];
			
			$genealogy1Arr3L = ['1'=>'', '11'=>'', '111'=>'', '112'=>'', '113'=>'', '12'=>'', '121'=>'', '122'=>'', '123'=>'', '13'=>'', '131'=>'', '132'=>'', '133'=>''];
            $genealogy2Arr3L = ['2'=>'', '21'=>'', '211'=>'', '212'=>'', '213'=>'', '22'=>'', '221'=>'', '222'=>'', '223'=>'', '23'=>'', '231'=>'', '232'=>'', '233'=>''];
            $genealogy3Arr = ['3'=>'', '31'=>'', '311'=>'', '312'=>'', '313'=>'', '32'=>'', '321'=>'', '322'=>'', '323'=>'', '33'=>'', '331'=>'', '332'=>'', '333'=>''];

            if ($parent_acinfo->can_sponsor == 1)
            {
                $parent_acinfo->status = 'Active';
            }
            else if ($parent_acinfo->can_sponsor = 0)
            {
                $parent_acinfo->status = 'Active';
            }
            else if ($parent_acinfo->block == $this->config->get('constants.ON'))
            {
                $parent_acinfo->status = 'Blocked';
            }

            $arrname = 'genealogy'.$parent_acinfo->rank.'Arr2L';
            $genealogyArr = $$arrname;

            $genealogyArr[$parent_acinfo->rank] = (object) [
                        'account_id'=>$parent_acinfo->account_id,
						'user_code'=>$parent_acinfo->user_code,
                        'activated_on'=>$parent_acinfo->activated_on,
                        'can_sponsor'=>1,
                        'cv'=>$parent_acinfo->cv,
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
						'upline_user_code'=>!empty($parent_acinfo->upline_user_code) ? $parent_acinfo->upline_uname : '',
                        'username'=>$parent_acinfo->uname,
                        'sponser_uname'=>!empty($parent_acinfo->sponser_uname) ? $parent_acinfo->sponser_uname : '',
						'sponser_user_code'=>!empty($parent_acinfo->sponser_user_code) ? $parent_acinfo->sponser_user_code : '',
                        'geninfo'=>$this->getTeamGenerationSale($parent_acinfo->account_id)
            ];

            if (!empty($res))
            {
                array_walk($res, function(&$ftdata) use (&$genealogyArr)
                {
                    //$ftdata->mypos = substr($ftdata->mypos,1,strlen($ftdata->mypos)-1);
                    $ftdata->signedup_on = date('d-M-Y ', strtotime($ftdata->signedup_on));
                    $ftdata->activated_on = date('d-M-Y ', strtotime($ftdata->activated_on));
                    if ($ftdata->qv > 0 || $ftdata->cv > 0)
                    {
                        $ftdata->status = 'Active';
                    }
                    else if ($ftdata->qv == 0 && $ftdata->cv == 0)
                    {
                        $ftdata->status = 'Active';
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

    public function get_direct_users ($params)
    {
        extract($params);
        if (isset($parent_acinfo) && $account_id > 0)
        {
            $qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'ut.account_id');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id');
            $qry->join($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id');
            $qry->where('ut.upline_id', '=', $account_id);
            $qry->where('ut.lineage', 'like', '%/'.$parent_acinfo->account_id.'/%');
            $qry->where('um.is_deleted', $this->config->get('constants.OFF'));
            $qry->orderBy('ut.rank', 'ASC');
            $qry->select(DB::raw("ut.upline_id,um.account_id,um.uname as username,concat_ws(' ',ud.firstname,ud.lastname) as fullname,upum.uname as upline_uname,upum.user_code as upline_user_code,spum.uname as sponser_uname,spum.user_code as sponser_user_code,um.status,um.block,ut.can_sponsor,(ut.level-".$parent_acinfo->level.") as level,um.signedup_on,ut.activated_on,((ut.rgt_node-ut.lft_node) DIV 2) as team_count,ut.referral_cnts,ut.referral_paid_cnts,um.login_block"));
            $res = $qry->get();

            if (!empty($res))
            {
                array_walk($res, function(&$ftdata)
                {
                    $ftdata->signedup_on = date('d-M-Y ', strtotime($ftdata->signedup_on));
                    $ftdata->activated_on = date('d-M-Y ', strtotime($ftdata->activated_on));
                });
                return !empty($res) ? $res : NULL;
            }
            return NULL;
        }
    }
	
	
	public function getReferralCnts($account_id){
		$data = [];
		if($account_id>0){			
			$parentTree = DB::table($this->config->get('tables.ACCOUNT_TREE'))
					->where('account_id','=',$account_id)
					->select('account_id','upline_id','sponsor_id','nwroot_id','lft_node','rgt_node')
					->first();
			if(!empty($parentTree)){
				$data['referral_today'] 	= $this->getUserCnts($parentTree,['type'=>1,'period'=>'today']);
				$data['referral_week'] 		= $this->getUserCnts($parentTree,['type'=>1,'period'=>'week']);
				$data['referral_month'] 	= $this->getUserCnts($parentTree,['type'=>1,'period'=>'month']);
				$data['referral_total'] 	= $this->getUserCnts($parentTree,['type'=>1]);
				$data['team_referral_today'] 	= $this->getUserCnts($parentTree,['type'=>2,'period'=>'today']);		
				$data['team_referral_week']		= $this->getUserCnts($parentTree,['type'=>2,'period'=>'week']);
				$data['team_referral_month'] 	= $this->getUserCnts($parentTree,['type'=>2,'period'=>'month']);
				$data['team_referral_total'] 	= $this->getUserCnts($parentTree,['type'=>2]);
			}
			return $data;
		}
	}
	
	
	public function getUserCnts($pTree,$arr=array()){
		$period = '';
		$type = 1;				
		if(!empty($pTree)){
			extract($arr);
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as at')							
					->join($this->config->get('tables.ACCOUNT_MST').' as am',function($join){
							$join->on('am.account_id','=','at.account_id')
								->where('am.is_deleted','=',$this->config->get('constants.OFF'));
					})
					->where('at.nwroot_id','=',$pTree->nwroot_id);							
			if($type==1) {
				$qry->where('at.sponsor_id','=',$pTree->account_id);
			} 	
			else {
				$qry->where('at.lft_node','>',$pTree->lft_node);
				$qry->where('at.rgt_node','<',$pTree->rgt_node);
			}							
			switch($period){
				case 'today';
					if($type==1) {
						$qry->whereDate('at.created_on','=',getGTZ('Y-m-d'));
					}
					else {
						$qry->whereDate('at.activated_on','=',getGTZ('Y-m-d'));
					}
				break;
				case 'week';
					$from = date('Y-m-d', strtotime( 'monday this week' ) );
					$to = date('Y-m-d', strtotime( 'sunday this week' ) );
					if($type==1) {
						$qry->whereBetween('at.created_on',[getGTZ($from,'Y-m-d'),getGTZ($to,'Y-m-d')]);
					} 	
					else {
						$qry->whereBetween('at.activated_on',[getGTZ($from,'Y-m-d'),getGTZ($to,'Y-m-d')]);
					}
					
					//$qry->whereBetween('at.activated_on',[getGTZ($from,'Y-m-d'),getGTZ($to,'Y-m-d')]);
				break;
				case 'month';
					if($type==1) {
						$qry->whereMonth('at.created_on','=',getGTZ('m'));
						$qry->whereYear('at.created_on','=',getGTZ('Y'));		
					} 	
					else {
						$qry->whereMonth('at.activated_on','=',getGTZ('m'));
						$qry->whereYear('at.activated_on','=',getGTZ('Y'));		
					}
					/*	$qry->whereMonth('at.activated_on','=',getGTZ('m'));
						$qry->whereYear('at.activated_on','=',getGTZ('Y'));		*/				
				break;
			}
			$cnts = $qry->count();
			
			return !empty($cnts)? $cnts:0;
		}
		return 0;
	}
	
	/* My Downline Sales */	
	public function my_downline_sales(array $arr =[])
    {
	    $sales = [];
	    extract($arr);
	    $qry = DB::table(config('tables.ACCOUNT_TREE').' as at')
				->join(config('tables.ACCOUNT_TREE').' as td','td.upline_id','=','at.account_id')
				->join(config('tables.ACCOUNT_PREFERENCE').' as pf','pf.account_id','=','at.account_id')				
				->where('at.account_id','=', $account_id)
				->groupBy('td.upline_id')
			//	->havingRaw('COUNT(td.account_id) = 3')				
				->select('at.account_id','pf.country_id','pf.currency_id',DB::raw('GROUP_CONCAT(td.account_id ORDER BY td.rank ASC) as my_downline'))
				->get();		
		
		foreach($qry as $gs){			
			$sales = DB::table(config('tables.ACCOUNT_TREE').' as t')
					->leftjoin(config('tables.ACCOUNT_TREE').' as tt',function($join){
						$join->on('tt.nwroot_id','=','t.nwroot_id')
							->on('tt.lft_node','>=','t.lft_node')
							->on('tt.rgt_node','<=','t.rgt_node');
					})
					->leftjoin(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as st',function($j){
						$j->on('st.account_id','=','tt.account_id')
							->where('st.payment_status','=',1)
							->where('st.status','=',1);							
					})
					->whereRaw('t.account_id in ('.$gs->my_downline.')')		
					->select(DB::raw('t.account_id,t.rank,SUM(st.package_qv) as qv'))
					->havingRaw('SUM(st.package_qv) > 0')
					->orderBy('t.rank','asc')
					->groupby('t.account_id')
					->get();
		}		
		if(!empty($sales)){
		    foreach($sales as $sale){
		        $sale->qv = !empty($sale->qv) ? number_format($sale->qv) : '';
		    }
		}
		return $sales;
    }
	

}
