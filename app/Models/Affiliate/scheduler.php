<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;
use Request;
use Response;
use App\Models\Commonsettings;
use App\Models\Affiliate\Bonus;

class scheduler extends BaseModel
{

    private $admincommonObj = '';

    function __construct ()
    {
        parent::__construct();
		 $this->commonObj = new Commonsettings();
		 $this->bonusObj = new Bonus();		 
    }

   
    public function add_promoter_ranking($arr){
	   extract($arr);
		
	   $qry = DB::table(config('tables.ACCOUNT_TREE').' as at')
				->join(config('tables.ACCOUNT_TREE').' as td','td.upline_id','=','at.account_id')
				->join(config('tables.ACCOUNT_PREFERENCE').' as pf','pf.account_id','=','at.account_id')
				->where('at.pro_rank_id','>',1)
				->groupBy('td.upline_id')
				->havingRaw('COUNT(td.account_id) = 3')
				->select('at.account_id','pf.country_id','pf.currency_id',DB::raw('GROUP_CONCAT(td.account_id ORDER BY td.rank ASC) as my_downline'))->get();
		$op['msg'] =  'No account found';
		
		foreach($qry as $gs){
			
			$res = DB::table(config('tables.ACCOUNT_TREE').' as t')
					->leftjoin(config('tables.ACCOUNT_TREE').' as tt',function($join){
						$join->on('tt.nwroot_id','=','t.nwroot_id')
							->on('tt.lft_node','>=','t.lft_node')
							->on('tt.rgt_node','<=','t.rgt_node');
					})
					->leftjoin(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as st',function($j){
						$j->on('st.account_id','=','tt.account_id')
							->where('st.payment_status','=',1)
							->where('st.status','=',1)
							->where(DB::Raw('MONTH(st.create_date)'),'=',date('m',strtotime('-1 months')));
							//->where(DB::Raw('YEAR(st.create_date)'),'=',date('m',strtotime('-1 months')));
					})
					->whereRaw('t.account_id in ('.$gs->my_downline.')')		
					->select(DB::raw('t.account_id,SUM(st.package_qv) as qv'))
					->havingRaw('SUM(st.package_qv) > 0')
					->orderBy('t.rank','asc')
					->groupby('t.account_id')
					->get();
			$status = true;
			
			if(!empty($res) && (count($res) == 3)){  //check tree active downline
					 /* $res = [
						(Object)['account_id'=>11,'qv'=>1000],
						(Object)['account_id'=>182,'qv'=>1060],
						(Object)['account_id'=>221,'qv'=>800]
					];  */
				//find total QV and remove non-purchased down line
				$total_qv 		= 0;
				foreach($res as $key =>$val){
					$total_qv = $total_qv+$val->qv;
				}
				$rank_limit = DB::table(config('tables.AFF_RANKING_LOOKUPS'))
						  ->whereRaw('gqv <='.$total_qv.' AND max_gqv >='.$total_qv)	
						  ->select('af_rank_id','gqv')->first();
				if(!empty($rank_limit)){
					$percent_val = round($rank_limit->gqv*60/100);
					$generation  = 1;
					
					/* check 60% of rank QV coming from one downline  */
					foreach($res as $val){   
						if($val->qv >= $percent_val){
							$status = false;
							break;
						}
						$g[$generation] = $val->qv;
						$generation++;
					}
					if($status == true){
							//$rank_limit->af_rank_id = config('constants.RANK.REGIONAL_DIRECTOR');
							$insdata['account_id']  = $gs->account_id;
							$insdata['af_rank_id']  = $rank_limit->af_rank_id;
							$insdata['status'] 		= 1;
							$insdata['is_verified'] = 1;
							$insdata['gen_1'] 		= $g[1];
							$insdata['gen_2'] 		= $g[2];
							$insdata['gen_3'] 		= $g[3];
							$insdata['created_on']  = date('Y-m-d');
							$insdata['verified_by'] = 0;
							DB::table(config('tables.ACCOUNT_AF_RANKING_LOG'))
								->where('account_id',$gs->account_id)	
								->update(['status'=>2]);
							DB::table(config('tables.ACCOUNT_AF_RANKING_LOG'))
							->insertGetId($insdata);
							DB::table(config('tables.ACCOUNT_TREE'))
								->where('account_id',$gs->account_id)
								->update(['pro_rank_id'=>$rank_limit->af_rank_id]); 
							
							//insert for car bonus
						if($rank_limit->af_rank_id == config('constants.RANK.REGIONAL_DIRECTOR')){
							 $prev =  DB::table(config('tables.ACCOUNT_AF_RANKING_LOG'))
									 ->where('account_id',$gs->account_id)	
									 ->where('status',2)	
									 ->where('is_verified',1)	
									 ->orderBy('ar_id','desc')
									 ->skip(1)
									 ->first();
							
								if(!empty($prev) && ($prev->af_rank_id == config('constants.RANK.REGIONAL_DIRECTOR'))){
									$bonus = DB::table(config('tables.AFF_BONUS_TYPES').' as bt')
										->join(config('tables.AFF_BONUS_CV_PERC').' as cv','cv.bonus_type','=','bt.bonus_type_id')
										->where('cv.currency_id',$gs->currency_id)
										->where('bt.bonus_type_id',config('constants.BONUS_TYPE.CAR_BONUS'))
										->select('bt.credit_wallet_id','bt.has_tax','bt.tax_class_id','cv.perc','cv.ngo_wallet_perc','cv.fixed_amt')->first();
										$tot_tax = 0;	
										if($bonus->has_tax){
											
											list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json) = $this->getTax(['account_id'=>$gs->account_id,'amount'=>$bonus->fixed_amt,'country_id'=>$gs->country_id,'statementline_id'=>$this->config->get('stline.CAR_BONUS.CREDIT')]);	
											
											$refData['service_tax_details'] = $tax_json;
											$refData['service_tax_per'] = $tot_tax_perc;
											$refData['service_tax'] = $tot_tax;	
											$refData['tax_class_id'] = $tax_class_id;				
										}						
									//	$taxAmt = number_format($tot_tax,2,'.','');	
									
									
									$carbonus['account_id']  = $gs->account_id;
									$carbonus['bonus_type']  = config('constants.BONUS_TYPE.CAR_BONUS');
									$carbonus['wallet_id']   = $bonus->credit_wallet_id;
									$carbonus['currency_id'] = $gs->currency_id;
									$carbonus['commission']  = $bonus->fixed_amt;
									$carbonus['tax'] 		 = $tot_tax;
									$carbonus['vi_help'] 	 = $bonus->fixed_amt * $bonus->ngo_wallet_perc/100;
									$carbonus['net_pay'] 	 = $bonus->fixed_amt - ($tot_tax+$carbonus['vi_help']);
									$carbonus['created_on']  = getGTZ();
									$carbonus['bonus_date']  = getGTZ();
									$carbonus['status'] 	 = 0;
									
									DB::table(config('tables.AFF_DIRECTORS_BONUS'))
									->insertGetId($carbonus);
								}		 
							}	
								/* $result = DB::table(config('tables.ACCOUNT_TREE'))
									->where('account_id',$account_id)
									->update(['pro_rank_id'=>$rank_limit->af_rank_id]); */
								$op['msg'] = 'Success';
							
					}else{
						$op['msg'] = '60% QV come from one downline';
					}
				}
			}else{
				DB::table(config('tables.ACCOUNT_AF_RANKING_LOG'))
								->where('account_id',$gs->account_id)	
								->update(['status'=>2]);
					$insdata['account_id']  = $gs->account_id;
					$insdata['af_rank_id']  = 1;
					$insdata['status'] 		= 1;
					$insdata['is_verified'] = 1;
					$insdata['gen_1'] 		= 0;
					$insdata['gen_2'] 		= 0;
					$insdata['gen_3'] 		= 0;
					$insdata['created_on']  = date('Y-m-d');
					$insdata['verified_by'] = 1;
					
					DB::table(config('tables.ACCOUNT_AF_RANKING_LOG'))
							->insertGetId($insdata);
					DB::table(config('tables.ACCOUNT_TREE'))
								->where('account_id',$gs->account_id)
								->update(['pro_rank_id'=>1]); 	
				$op['msg'] = 'Success2';
			}
		}
		return $op;
    }
	
	public function generateTeamCommission(){
	
		$userList = DB::table(config('tables.ACCOUNT_TREE').' as ut')
				->join(config('tables.ACCOUNT_PREFERENCE').' as pf','pf.account_id','=','ut.account_id')				
                //->where('ut.account_id','=',15)
				->where('ut.can_sponsor','=',1)
				->where('ut.referral_paid_cnts','>=',2)                
                ->orderBy('ut.rank', 'ASC')				
                ->selectRaw(DB::raw("ut.account_id,ut.nwroot_id,ut.lft_node,ut.rgt_node,pf.country_id,pf.currency_id"))
                ->get();
		echo '<pre>';
		/*$curdate = '2019-01-04';
		$start_date = date('Y-m-d',strtotime($curdate.' last week friday'));
		$end_date  =  date('Y-m-d',strtotime($curdate.' last thursday'));
		*/
		//$curdate = '2019-01-04';
		//echo date('d-m-Y H:i:s');
		//die;
		$start_date = date('Y-m-d',strtotime('last week friday'));
		$end_date  =  date('Y-m-d',strtotime('last thursday'));
		
		echo $start_date.'=='.$end_date.'<br>';
		//die;
		
        if (!empty($userList) && count($userList)>0){
			foreach($userList as $usr)
			{				
				$bonusExists = DB::table(config('tables.AF_BINARY_BONUS').' as pf')				
					->where('pf.account_id','=',$usr->account_id)
					->where('pf.bonus_type',config('constants.BONUS_TYPE.TEAM_BONUS'))
					->whereDate('pf.from_date','=',$start_date)                					
					->exists();
				
				if(!$bonusExists){
					echo '<br>Bonus chekcing Account: '.$usr->account_id.'<br>';	
					$this->save_team_bonus($usr->account_id, $usr->currency_id,$usr->country_id,$start_date,$end_date);
				}
			}
		}
	}
	
	
	/* Team Bonus Save */

    public function save_team_bonus ($account_id, $currency_id,$country_id,$start_date,$end_date,$instant_credit = true)
    {
	    $g1_sale = 0;
        $g2_sale = 0;
        $user_count = DB::table(config('tables.ACCOUNT_TREE').' as ut')
                ->where('ut.upline_id', $account_id)            
                ->whereIn('rank', [config('constants.TEAM_GENARATION.1G'), config('constants.TEAM_GENARATION.2G')])
                ->orderBy('rank', 'ASC')
                ->selectRaw(DB::raw("account_id,nwroot_id,lft_node,rgt_node"))
                ->get();
		$op['msg'] = 'faild';
        if (count($user_count) == 2)
        {
			echo '<pre>';
			$capping = 0;
			$sales = [];
            foreach ($user_count as $key=> $user_info)
            {
				$pkg_qv = DB::table(config('tables.ACCOUNT_TREE').' as at')
					->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast', function($subquery)use($start_date,$end_date)
					{
						$subquery->on('ast.account_id', '=', 'at.account_id');
					})
					->whereDate('ast.confirm_date','>=',$start_date)
					->whereDate('ast.confirm_date','<=',$end_date)
					->where('ast.status', config('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'))
					->where('ast.payment_status', config('constants.PAYMENT_PAID'))
					->where('at.lft_node','>=',$user_info->lft_node)
					->where('at.lft_node','<=',$user_info->rgt_node)
					->where('at.nwroot_id', $user_info->nwroot_id)
					->select(DB::RAW('sum(ast.package_qv) as month_sale_qv'))
					->first();
		
                if (!empty($pkg_qv))
                {
                    ${'g'.($key + 1).'_sale'} = $pkg_qv->month_sale_qv;
					$sales[] = $pkg_qv;					
                }
				print_r($pkg_qv);
	        }
			
			if($g1_sale == 0 &&  $g2_sale == 0) {
			    return false;
			}
			
			$bonusInfo = $this->bonusObj->getBonusSetting($this->config->get('constants.BONUS_TYPE.TEAM_BONUS'));
			print_r($bonusInfo );
			
			echo '<br>leftbinpnt:'.$g1_sale;
            echo '<br>rightbinpnt:'.$g2_sale;
			
            $leftbinpnt 	= $g1_sale;
            $rightbinpnt 	= $g2_sale;
			$handleamt 		 = 0;
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
			$last_binary_id = 0;
            $binary_bonus 	= DB::table(config('tables.AF_BINARY_BONUS').' as bb')
								->where('bb.account_id', $account_id)
								->where('bb.bonus_type',config('constants.BONUS_TYPE.TEAM_BONUS'))
								->orderBy('bid', 'Desc')
								->selectRaw(DB::raw("bid,account_id,leftcarryfwd,rightcarryfwd,totleftbinpnt,totrightbinpnt"))
								->first();
			print_r($binary_bonus);
			//		$rightbinpnt = 150;
            if (!empty($binary_bonus))
            {
                $totleftbinpnt   = $binary_bonus->totleftbinpnt + $leftbinpnt;
                $totrightbinpnt  = $binary_bonus->totrightbinpnt + $rightbinpnt;
                $leftcryfwd 	 = $binary_bonus->leftcarryfwd;
                $rgtcryfwd 		 = $binary_bonus->rightcarryfwd;  
				$last_binary_id = $binary_bonus->bid;  
            } 
			else {
				$totleftbinpnt   = $leftbinpnt;
                $totrightbinpnt  = $rightbinpnt;
			}
			
			$leftclubpoint   = $leftbinpnt + $leftcryfwd;
			$rightclubpoint  = $rightbinpnt + $rgtcryfwd;
			
			echo '<br>leftclubpoint:'.$leftclubpoint;
			echo '<br>rightclubpoint:'.$rightclubpoint;
			if($leftclubpoint>0 && $rightclubpoint>0) {
			
				if ($leftclubpoint > $rightclubpoint)
				{
					$cluppnt = $rightclubpoint;
				}
				else if ($rightclubpoint > $leftclubpoint)
				{
					$cluppnt = $leftclubpoint;
				}
				else if ($rightclubpoint == $leftclubpoint){
					$cluppnt = $leftclubpoint;
				}
			
				$leftcryfwd = $leftclubpoint - $cluppnt;
				$rgtcryfwd = $rightclubpoint - $cluppnt;			
				
				echo $cluppnt.'<br>';
				
				
				$my_capping = DB::table(config('tables.ACCOUNT_TREE').' as at')
						->leftjoin(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as astt', 'astt.account_id', "=", 'at.account_id')
						->where('at.account_id', $account_id)
						->select('at.nwroot_id',DB::raw('IF(sum(astt.package_qv)>0,sum(astt.package_qv),0) as self_qv'))
						->first();
				
				$self_qv = $my_capping->self_qv;

				$pkCapping = DB::table(config('tables.AFF_PACKAGE_MST').' as pk')
									->join(config('tables.AFF_PACKAGE_PRICING').' as pp',function($join) use ($currency_id){
										$join->on('pp.package_id','=','pk.package_id')
											->where('pp.currency_id','=',$currency_id);
									})
									->where('pk.status','=',config('constants.ON'))
									->orderby('pk.package_level','ASC')
									->select('pp.package_qv','pp.weekly_capping_qv','pp.weekly_capping_qv')
									->get();			
			
				$capping = 0;
				if($account_id!=$my_capping->nwroot_id){
					foreach($pkCapping as $cp) {
						if($cp->package_qv==$self_qv){
							$capping = $cp->weekly_capping_qv;
							break;
						} 
						else if($cp->package_qv>$self_qv){					
							break;
						} 
						else if($cp->package_qv<$self_qv) {
							$capping = $cp->weekly_capping_qv;
						}
					}
				}
				
				if($capping==0){ 
					if($self_qv>0 && $account_id!=$my_capping->nwroot_id){
						$cp = $pkCapping[count($pkCapping)-1];
						$capping = $cp->weekly_capping_qv;
					}
					else if($account_id==$my_capping->nwroot_id){
						$cp = $pkCapping[count($pkCapping)-1];
						$capping = $cp->weekly_capping_qv;
					}
				}
				
				if($cluppnt >= $capping){
					$bonus_qv = $capping;
				} else{
					$bonus_qv = $cluppnt;
				}		
		
				$current_rate = 1;
				
				$rate_setting = DB::table(config('tables.SETTINGS'))
									->where('setting_key',config('constants.QV_CURRENCY_RATE'))
									->value('setting_value');
				$rate 			= json_decode(stripslashes($rate_setting));
				
				echo $bonus_qv.'==='.$rate->$currency_id;
				
				if(isset($rate->$currency_id) && $bonus_qv>0) 
				{
					$current_rate 	= $rate->$currency_id;
					
					$earnings_qv 	  = $bonus_qv * $bonusInfo->perc / 100;
					
					$bonus_amt 		= $earnings_qv * $current_rate;
					
					$ngo_wallet_amt = $bonus_amt * $bonusInfo->ngo_wallet_perc/100;
					
					if($bonusInfo->has_tax){
						list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json)= $this->getTax(['account_id'=>$account_id,'amount'=>$bonus_amt,'country_id'=>$country_id,'statementline_id'=>$this->config->get('stline.FAST_START_BONUS.CREDIT')]);	
						
						$refData['service_tax_details'] = $tax_json;
						$refData['service_tax_per'] 	= $tot_tax_perc;
						$refData['service_tax'] 		= $tot_tax;	
						$refData['tax_class_id'] 		= $tax_class_id;				
					}
					$taxAmt = number_format($tot_tax,2,'.','');
					
					$current_date2  = getGtz();
					
					$form_date 		= $start_date;
					$to_date 		= $end_date;
					$date_for 		= date('Y-m-d', strtotime($end_date.' +4 days'));
					
					$sdata['last_bid'] 			= $last_binary_id;
					$sdata['account_id'] 		= $account_id;
					$sdata['bonus_type'] 		= config('constants.BONUS_TYPE.TEAM_BONUS');
					$sdata['bonus_value'] 		= $bonusInfo->perc;
					$sdata['bonus_value_in']	= 0;
					$sdata['leftbinpnt']    	= !empty($leftbinpnt)?$leftbinpnt:0;
					$sdata['rightbinpnt']    	= !empty($rightbinpnt)?$rightbinpnt:0;
					$sdata['leftclubpoint']  	= $leftclubpoint;
					$sdata['rightclubpoint'] 	= $rightclubpoint;
					$sdata['clubpoint'] 		= $cluppnt;				
					$sdata['totleftbinpnt'] 	= $totleftbinpnt;
					$sdata['totrightbinpnt']	= $totrightbinpnt;
					$sdata['leftcarryfwd']  	= $leftcryfwd;
					$sdata['rightcarryfwd'] 	= $rgtcryfwd;
					$sdata['left_flushout'] 	= 0;
					$sdata['right_flushout']	= 0;
					$sdata['capping'] 			= $capping;
					$sdata['bonus_qv'] 			= $bonus_qv;				
					$sdata['earnings']			= $earnings_qv;		
					$sdata['convertion_rate']			= $current_rate;				
					$sdata['currency_id'] 		= $currency_id;
					$sdata['income'] 		= $bonus_amt;
					$sdata['tax'] 		 	 = $taxAmt;
					$sdata['ngo_wallet_amt'] = $ngo_wallet_amt;
					$sdata['paidinc'] 		 = $bonus_amt - ($ngo_wallet_amt+$taxAmt);
					$sdata['wallet_id'] 	= $bonusInfo->credit_wallet_id;
					$sdata['status'] 		= config('constants.BONUS.STATUS_PENDING');
					$sdata['from_date'] 	= $form_date;
					$sdata['to_date'] 		= $to_date;
					$sdata['date_for'] 		= $date_for;				
					$sdata['created_date']  = getGTZ();
					//print_r($sdata);
					
					$bid = DB::table(config('tables.AF_BINARY_BONUS'))
							->insertGetId($sdata);
					/*if($bid){
						if(isset($instant_credit) && !empty($instant_credit)){
							$cdata['bid'] = $bid;
							$cdata['wallet'] = $bonusInfo->credit_wallet_id;
							$cdata['account_id'] = $account_id;
							$cdata['amount'] = $bonus_amt;
							$cdata['currency_id'] = $currency_id;
							$cdata['tax'] = $taxAmt;
							$cdata['netpay'] = $sdata['paidinc'];
							$cdata['ngoAmt'] = $ngo_wallet_amt;
							$cdata['remark'] = ['from_date'=>$form_date,'to_date'=>$to_date];
							$cdata['statementline_id'] = config('stline.TEAM-BONUS-CREDIT');						
							$this->bonusObj->credit_bonus($cdata);	
						}
					}*/
					echo '<br>-----------------<br>';
					$op['msg']	= 'success';	
				}
			}
        }
		return $op;
    }	
    
    public function generateLeadershipBonus(){
		
		$userList = DB::table(config('tables.ACCOUNT_TREE').' as ut')
				->join(config('tables.ACCOUNT_PREFERENCE').' as pf','pf.account_id','=','ut.account_id')
                ->where('ut.can_sponsor','=',1)
				->where('ut.noof_directs','>=',2)              
                ->orderBy('ut.rank', 'ASC')
                ->selectRaw(DB::raw("ut.account_id,ut.nwroot_id,ut.lft_node,ut.rgt_node,pf.country_id,pf.currency_id"))
                ->get();
		echo '<pre>';
		
		$start_date = date('Y-m-01',strtotime('last month'));
		$end_date  =  date('Y-m-t',strtotime('last month'));	
		
        if (!empty($userList) && count($userList)>0){
			foreach($userList as $usr)
			{				
				$bonusExists = DB::table(config('tables.AF_BINARY_BONUS').' as pf')				
					->where('pf.account_id','=',$usr->account_id)
					->where('pf.bonus_type',config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
					->whereDate('pf.from_date','=',$start_date)                					
					->exists();
				
				if(!$bonusExists){
					$this->save_leadership_bonus($usr->account_id, $usr->currency_id,$usr->country_id,$start_date,$end_date);
				}
			}
		}
	}
	
	/* Team Bonus Save */
    public function save_leadership_bonus ($account_id, $currency_id,$country_id,$start_date,$end_date,$instant_credit = true)
    {
	    $g1_sale = 0;
        $g2_sale = 0;
		$g3_sale = 0;
        $user_count = DB::table(config('tables.ACCOUNT_TREE').' as ut')
                ->where('ut.upline_id', $account_id)            
                ->whereIn('rank', [config('constants.TEAM_GENARATION.1G'), config('constants.TEAM_GENARATION.2G'), config('constants.TEAM_GENARATION.3G')])
                ->orderBy('rank', 'ASC')
                ->selectRaw(DB::raw("account_id,nwroot_id,rank,lft_node,rgt_node"))
                ->get();
		$op['msg'] = 'faild';
        if (count($user_count) >= 2)
        {
			echo '<pre>';
			$capping = 0;			
			
            foreach ($user_count as $key=> $user_info)
            {
				$pkg_qv = DB::table(config('tables.ACCOUNT_TREE').' as at')
					->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast', function($subquery)use($start_date,$end_date)
					{
						$subquery->on('ast.account_id', '=', 'at.account_id');
					})
					->whereDate('ast.confirm_date','>=',$start_date)
					->whereDate('ast.confirm_date','<=',$end_date)
					->where('ast.status', config('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'))
					->where('ast.payment_status', config('constants.PAYMENT_PAID'))
					->where('at.lft_node','>=',$user_info->lft_node)
					->where('at.lft_node','<=',$user_info->rgt_node)
					->where('at.nwroot_id', $user_info->nwroot_id)
					->select(DB::RAW('sum(ast.package_qv) as month_sale_qv'))
					->first();
		
                if (!empty($pkg_qv))
                {
                    ${'g'.($key + 1).'_sale'} = $pkg_qv->month_sale_qv;
                }
				print_r($pkg_qv);
	        }
			
			$bonusInfo = $this->bonusObj->getBonusSetting($this->config->get('constants.BONUS_TYPE.LEADERSHIP_BONUS'));
			print_r($bonusInfo );
			
            $leftbinpnt 	= ($g1_sale+$g2_sale);
            $rightbinpnt 	= $g3_sale;
			$handleamt 		= 0;
            $totleftbinpnt  = 0;
			$totrightbinpnt = 0;
			$leftcryfwd 	= 0;
			$rgtcryfwd 		= 0;
			$leftclubpoint 	= 0;
			$rightclubpoint = 0;
			$cluppnt 		= 0;
			$bonus_qv 		= 0;
			$bonus_amt 		= 0;
			$paid_amt 		= 0;
			$flushamt 		= 0;
			$ngo_wallet_amt = 0;
			$last_binary_id = 0;
            $binary_bonus 	= DB::table(config('tables.AF_BINARY_BONUS').' as bb')
								->where('bb.account_id', $account_id)
								->where('bb.bonus_type',config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
								->orderBy('bid', 'Desc')
								->selectRaw(DB::raw("bid,account_id,leftcarryfwd,rightcarryfwd,totleftbinpnt,totrightbinpnt"))
								->first();
			print_r($binary_bonus);
			//		$rightbinpnt = 150;
            if (!empty($binary_bonus))
            {
                $totleftbinpnt   = $binary_bonus->totleftbinpnt + $leftbinpnt;
                $totrightbinpnt  = $binary_bonus->totrightbinpnt + $rightbinpnt;
                $leftcryfwd 	 = $binary_bonus->leftcarryfwd;
                $rgtcryfwd 		 = $binary_bonus->rightcarryfwd;  
				$last_binary_id = $binary_bonus->bid;  
            } 
			else {
				$totleftbinpnt   = $leftbinpnt;
                $totrightbinpnt  = $rightbinpnt;
			}
			
			$leftclubpoint   = $leftbinpnt + $leftcryfwd;
			$rightclubpoint  = $rightbinpnt + $rgtcryfwd;
			
			if ($leftclubpoint > $rightclubpoint)
			{
				$cluppnt = $rightclubpoint;
			}
			else if ($rightclubpoint > $leftclubpoint)
			{
				$cluppnt = $leftclubpoint;
			}
			else if ($rightclubpoint == $leftclubpoint){
				$cluppnt = $leftclubpoint;
			}
			
			$leftcryfwd = $leftclubpoint - $cluppnt;
            $rgtcryfwd = $rightclubpoint - $cluppnt;			
			
			echo $cluppnt.'<br>';
			
			
			$my_capping = DB::table(config('tables.ACCOUNT_TREE').' as at')
					->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as astt', 'astt.account_id', "=", 'at.account_id')
					->where('at.account_id', $account_id)
					->select('at.nwroot_id',DB::raw('IF(sum(astt.package_qv)>0,sum(astt.package_qv),0) as self_qv'))
					->first();
			
			$self_qv = $my_capping->self_qv;

			$pkCapping = DB::table(config('tables.AFF_PACKAGE_MST').' as pk')
						->join(config('tables.AFF_PACKAGE_PRICING').' as pp',function($join) use ($currency_id){
							$join->on('pp.package_id','=','pk.package_id')
								->where('pp.currency_id','=',$currency_id);
						})
						->where('pk.status','=',config('constants.ON'))
						->orderby('pk.package_level','ASC')
						->select('pp.package_qv','pp.weekly_capping_qv','pp.weekly_capping_qv')
						->get();
			
			$capping = 0;
			
			foreach($pkCapping as $cp) {
				if($cp->package_qv==$self_qv){
					$capping = $cp->weekly_capping_qv;
					break;
				} 
				else if($cp->package_qv>$self_qv){					
					break;
				} 
				else if($cp->package_qv<$self_qv) {
					$capping = $cp->weekly_capping_qv;
				}
			}
			
			if($capping==0){
				$cp = $pkCapping[count($pkCapping)-1];
				$capping = $cp->weekly_capping_qv;
			}
			
			if($cluppnt >= $capping){
				$bonus_qv = $capping;
			} else{
				$bonus_qv = $cluppnt;
			}		
	
			$current_rate = 1;
			
			$rate_setting = DB::table(config('tables.SETTINGS'))
								->where('setting_key',config('constants.QV_CURRENCY_RATE'))
								->value('setting_value');
			$rate 			= json_decode(stripslashes($rate_setting));
			
			
			if(isset($rate->$currency_id) && $bonus_qv>0) 
			{
				$current_rate 	= $rate->$currency_id;
				
				$earnings_qv 	  = $bonus_qv * $bonusInfo->perc / 100;
				
				$bonus_amt 		= $earnings_qv * $current_rate;
				
				$ngo_wallet_amt = $bonus_amt * $bonusInfo->ngo_wallet_perc/100;
				
				if($bonusInfo->has_tax){
					list($tot_tax, $taxes,$tax_class_id,$tot_tax_perc,$tax_json)= $this->getTax(['account_id'=>$account_id,'amount'=>$bonus_amt,'country_id'=>$country_id,'statementline_id'=>$this->config->get('stline.LEADERSHIP-BONUS-CREDIT')]);	
					
					$refData['service_tax_details'] = $tax_json;
					$refData['service_tax_per'] 	= $tot_tax_perc;
					$refData['service_tax'] 		= $tot_tax;	
					$refData['tax_class_id'] 		= $tax_class_id;				
				}	
				$taxAmt = number_format($tot_tax,2,'.','');
				
				$current_date2  = getGtz();				
				$form_date 		= $start_date;
				$to_date 		= $end_date;
				
				$date_for 		= date('Y-m-15');
				
				
				$sdata['last_bid'] 			= $last_binary_id;
				$sdata['account_id'] 		= $account_id;
				$sdata['bonus_type'] 		= config('constants.BONUS_TYPE.LEADERSHIP_BONUS');
				$sdata['bonus_value'] 		= $bonusInfo->perc;
				$sdata['bonus_value_in']	= 0;
				$sdata['leftbinpnt']    	= !empty($leftbinpnt)?$leftbinpnt:0;
				$sdata['rightbinpnt']    	= !empty($rightbinpnt)?$rightbinpnt:0;
				$sdata['leftclubpoint']  	= $leftclubpoint;
				$sdata['rightclubpoint'] 	= $rightclubpoint;
				$sdata['clubpoint'] 		= $cluppnt;				
				$sdata['totleftbinpnt'] 	= $totleftbinpnt;
				$sdata['totrightbinpnt']	= $totrightbinpnt;
				$sdata['leftcarryfwd']  	= $leftcryfwd;
				$sdata['rightcarryfwd'] 	= $rgtcryfwd;
				$sdata['left_flushout'] 	= 0;
				$sdata['right_flushout']	= 0;
				$sdata['capping'] 			= $capping;
				$sdata['bonus_qv'] 			= $bonus_qv;				
				$sdata['earnings']			= $earnings_qv;		
				$sdata['convertion_rate']			= $current_rate;				
				$sdata['currency_id'] 		= $currency_id;
				$sdata['income'] 		= $bonus_amt;
				$sdata['tax'] 		 	 = $taxAmt;
				$sdata['ngo_wallet_amt'] = $ngo_wallet_amt;
				$sdata['paidinc'] 		 = $bonus_amt - ($ngo_wallet_amt+$taxAmt);
				$sdata['wallet_id'] 	= $bonusInfo->credit_wallet_id;
				$sdata['status'] 		= config('constants.BONUS.STATUS_PENDING');
				$sdata['from_date'] 	= $form_date;
				$sdata['to_date'] 		= $to_date;				
				$sdata['date_for'] 		= $date_for;	
				$sdata['created_date']  = getGTZ();
				print_r($sdata);
				$bid = DB::table(config('tables.AF_BINARY_BONUS'))
						->insertGetId($sdata);
				/*if($bid){
					if(isset($instant_credit) && !empty($instant_credit)){
						$cdata['bid'] = $bid;
						$cdata['wallet'] = $bonusInfo->credit_wallet_id;
						$cdata['account_id'] = $account_id;
						$cdata['amount'] = $bonus_amt;
						$cdata['currency_id'] = $currency_id;
						$cdata['tax'] = $taxAmt;
						$cdata['netpay'] = $sdata['paidinc'];
						$cdata['ngoAmt'] = $ngo_wallet_amt;
						$cdata['remark'] = ['from_date'=>$form_date,'to_date'=>$to_date];
						$cdata['statementline_id'] = config('stline.LEADERSHIP-BONUS-CREDIT');						
						$this->bonusObj->credit_bonus($cdata);	
					}
				}*/
				$op['msg']	 = 'success';	
			}
        }
		return $op;
    }
    
    
    public function releaseTeamCommission()
    {       
		$date_for = date('Y-m-d');
		
       	$binary_bonus_list 	= DB::table(config('tables.AF_BINARY_BONUS').' as bb')
							->where('bb.bonus_type',config('constants.BONUS_TYPE.TEAM_BONUS'))
							->where('bb.date_for', $date_for)
							->where('bb.status', config('constants.BONUS.STATUS_PENDING'))														
							->orderBy('bid', 'ASC')
							->get();
        
		if (!empty($binary_bonus_list) && count($binary_bonus_list)>0){
			foreach($binary_bonus_list as $binaryData)
			{	
				$cdata = [];
				$cdata['bid'] = $binaryData->bid;
				$cdata['wallet'] = $binaryData->wallet_id;
				$cdata['account_id'] = $binaryData->account_id;
				$cdata['amount'] = $binaryData->income;
				$cdata['currency_id'] = $binaryData->currency_id;
				$cdata['tax'] = $binaryData->tax;
				$cdata['ngoAmt'] = $binaryData->ngo_wallet_amt;
				$cdata['netpay'] = $binaryData->paidinc;
				$cdata['remark'] = ['from_date'=>$binaryData->from_date,'to_date'=>$binaryData->to_date];
				$cdata['statementline_id'] = config('stline.TEAM-BONUS-CREDIT');										
				$res = $this->bonusObj->credit_bonus($cdata);	
				print_r($res);
				if($res){
					$upData = [
						'status' =>1,
						'confirmed_date' => getGTZ()
					];
					DB::table(config('tables.AF_BINARY_BONUS').' as bb')
						->where('bb.bid', $binaryData->bid)
						->update($upData);
				}
			}
        }
    }   
    public function releaseLeaderShippBonus()
    {
		/*its should  release on every 15th */
       	$date_for 		= date('Y-m-15');
       	
       	$binary_bonus_list 	= DB::table(config('tables.AF_BINARY_BONUS').' as bb')
							->where('bb.bonus_type',config('constants.BONUS_TYPE.LEADERSHIP_BONUS'))
							->where('bb.date_for', $date_for)
							->where('bb.status', config('constants.BONUS.STATUS_PENDING'))														
							->orderBy('bid', 'ASC')
							->get();
		
        if (!empty($binary_bonus_list) && count($binary_bonus_list)>0){
			foreach($binary_bonus_list as $binaryData)
			{	
				$cdata = [];
				$cdata['bid'] = $binaryData->bid;
				$cdata['wallet'] = $binaryData->wallet_id;
				$cdata['account_id'] = $binaryData->account_id;
				$cdata['amount'] = $binaryData->income;
				$cdata['currency_id'] = $binaryData->currency_id;
				$cdata['tax'] = $binaryData->tax;
				$cdata['ngoAmt'] = $binaryData->ngo_wallet_amt;
				$cdata['netpay'] = $binaryData->paidinc;
				$cdata['remark'] = ['from_date'=>$binaryData->from_date,'to_date'=>$binaryData->to_date];
				$cdata['statementline_id'] = config('stline.LEADERSHIP-BONUS-CREDIT');						
				$res = $this->bonusObj->credit_bonus($cdata);
				print_r($res);				
				if($res){
					$upData = [
						'status' =>1,
						'confirmed_date' => getGTZ()
					];
					DB::table(config('tables.AF_BINARY_BONUS').' as bb')
						->where('bb.bid', $binaryData->bid)
						->update($upData);
				}
			}
        }
    }
    
}


/*
update aff_account_tree t1 set t1.referral_cnts=0,t1.referral_paid_cnts=0,noof_directs = '0';
update aff_account_tree t1,(SELECT sponsor_id,count(account_id) as cnts FROM `aff_account_tree` where sponsor_id>0 group by sponsor_id) t2 set t1.referral_cnts=t2.cnts where t1.account_id=t2.sponsor_id;
update aff_account_tree t1,(SELECT sponsor_id,count(account_id) as cnts FROM `aff_account_tree` where sponsor_id>0 and upline_id>0 group by sponsor_id) t2 set t1.referral_paid_cnts=t2.cnts where t1.account_id=t2.sponsor_id;
update aff_account_tree t1,(SELECT upline_id,count(account_id) as cnts FROM `aff_account_tree` where upline_id>0 group by upline_id) t2 set t1.noof_directs=t2.cnts where t1.account_id=t2.upline_id;
*/