<?php
namespace App\Models\Admin;

use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\Admin\PackageModel;
use App\Models\Admin\AdminFinance;
use App\Models\Admin\AdminBonus;
use App\Models\Admin\AffModel;

class AdminPackages extends BaseModel {
	
    public function __construct() {
        parent::__construct();	
		$this->financeObj = new AdminFinance;
		$this->bonusObj = new AdminBonus;
		$this->affObj = new AffModel;
    }

	public function affActivatePackage($purchase_code){
		if(!empty($purchase_code)){
			$pack_details = $this->getTopupPackageInfo(['purchase_code'=>$purchase_code]);
			if($pack_details){
				$res = $this->activatePackage($pack_details);																					
			}
		}
	}

   public function upgrade_history ($arr = array())
    {
        extract($arr);
        $pkgSql = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usubtop')
                ->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'usubtop.package_id', '=', 'pm.package_id')
                ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
                {
                    $subquery->on('pm.package_id', '=', 'pl.package_id')
                    ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usubtop.currency_id')
				->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'usubtop.payment_type')
				->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'usubtop.account_id')
				->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'usubtop.account_id')
                ->where('usubtop.is_deleted', $this->config->get('constants.OFF'))
                ->select(DB::RAW('usubtop.currency_id,usubtop.subscribe_topup_id,usubtop.purchase_code,cur.currency as currency_code,pl.package_name,pm.package_image,usubtop.amount,usubtop.handle_amt,usubtop.paid_amt,usubtop.create_date,usubtop.is_refundable,usubtop.subscribe_id,usubtop.refund_expire_on,usubtop.package_qv,usubtop.weekly_capping_qv,usubtop.shopping_points_cashback,usubtop.shopping_points_bonus,usubtop.package_level,usubtop.updated_date,usubtop.status,pt.payment_type,am.user_code,am.account_id,concat_ws(\' \',ad.firstname,ad.lastname) as fullname'))
                ->latest('usubtop.subscribe_topup_id');

        if (isset($subscrib_id) && $subscrib_id > 0)
        {
            $pkgSql->where('usubtop.subscrib_id', $subscrib_id);
        }

       if (isset($search_term) && !empty($search_term))
         {
            if (is_numeric($search_term))
            {
                $pkgSql->where('am.user_code', $search_term);
            }
            else
            {
                $pkgSql->Where('am.uname', 'like', '%'.$search_term.'%')
                        ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'), 'like', '%'.$search_term.'%');
            }
        }
        if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {
            $wQry2->whereDate("usubtop.created_on", ">=", getUTZ('Y-m-d', $from));
            $wQry2->whereDate("usubtop.created_on", "<=", getUTZ('Y-m-d', $to));
        }
        else if (isset($from) && !empty($from))
        {
            $wQry2->whereDate('usubtop.created_on', '<=', getUTZ('Y-m-d', $from));
        }
        else if (!empty($to) && isset($to))
        {
            $wQry2->whereDate("usubtop.created_on", ">=", getUTZ('Y-m-d', $to));
        }

        if (isset($orderby) && isset($order))
        {
            $pkgSql->orderBy($orderby, $order);
        }
        if (isset($length) && !empty($length))
        {
            $pkgSql->skip($start)->take($length);
        }

        if (isset($count) && !empty($count))
        {
            return $pkgSql->count();
        }
        else
        {
            $pkg = $pkgSql->get();
        }

        if (!empty($pkg))
        {

            return $pkg;
        }
        return false;
    }
	public function getTopupPackageInfo ($arr = array())
    {
        if (!empty($arr) && (isset($arr['purchase_code']) || isset($arr['subscribe_topup_id'])))
        {
            $pkgSql = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usubtop')
                    ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'usubtop.account_id')
					->join($this->config->get('tables.ACCOUNT_TREE').' as atr', 'atr.account_id', '=', 'usubtop.account_id')
					->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'pm.package_id', '=', 'usubtop.package_id')
                    ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
                    {
                        $subquery->on('pm.package_id', '=', 'pl.package_id')
                        ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
                    })
                    ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usubtop.currency_id')
                    ->where('usubtop.is_deleted', $this->config->get('constants.OFF'))
                    ->select(DB::RAW('usubtop.account_id,am.user_code,atr.pro_rank_id as aff_pro_rank_id,usubtop.package_id,usubtop.purchase_code,usubtop.currency_id,usubtop.subscribe_topup_id,cur.currency as currency_code,pl.package_name,pm.package_image,usubtop.amount,usubtop.handle_amt,usubtop.paid_amt,usubtop.create_date,usubtop.is_refundable,pm.instant_benefit_credit,usubtop.subscribe_id,usubtop.refund_expire_on,usubtop.package_qv,usubtop.weekly_capping_qv,usubtop.shopping_points_cashback,usubtop.shopping_points_bonus,usubtop.package_level,usubtop.updated_date,usubtop.status'));

            if (isset($arr['purchase_code']) && !empty($arr['purchase_code']))
            {
                $pkgSql->where('usubtop.purchase_code', '=', $arr['purchase_code']);
            }
            else if (isset($arr['subscribe_topup_id']) && !empty($arr['subscribe_topup_id']))
            {
                $pkgSql->where('usubtop.subscribe_topup_id', '=', $arr['subscribe_topup_id']);
            }
            return $pkgSql->first();
        }
    }

	public function activatePackage($packInfo){		

		$op = ['status'=>422,'msg'=>'Please try again later'];
		if(is_object($packInfo)){
	
			$sbdata['status'] =   $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED');
			$sbdata['confirm_date'] =   getGTZ();
			$sbdata['updated_date'] =   getGTZ();
			
			$op['stRes'] = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
						->where('subscribe_topup_id',$packInfo->subscribe_topup_id)
						->where('account_id',$packInfo->account_id)
						->whereIn('status',[$this->config->get('constants.PACKAGE_PURCHASE_STATUS.WAIT_FOR_ACTIVATE'),$this->config->get('constants.PACKAGE_PURCHASE_STATUS.USER_APPROVALS')])
						->where('is_deleted',$this->config->get('constants.OFF'))
						->update($sbdata);
			
			//$op['stRes'] = true;
			
			if($op['stRes']){
				$stdata['confirm_date'] = $sbdata['confirm_date'];			
				$stdata['topup_id'] =  $packInfo->subscribe_topup_id;
				$stdata['package_id'] =   $packInfo->package_id;			
				$stdata['package_level'] =  $packInfo->package_level; 
				$stdata['package_qv'] =   $packInfo->package_qv;
				$stdata['weekly_capping_qv'] =   $packInfo->weekly_capping_qv;
				$stdata['status'] =   $this->config->get('constants.PACKAGE_PURCHASE_STATUS.CONFIRMED');
				$stdata['payment_status'] =   $this->config->get('constants.PAYMENT_STATUS.CONFIRMED');
				$stdata['expire_on'] =	  getGTZ(date('Y-m-d H:i:s',strtotime($sbdata['confirm_date'].' +1 year')));
				
				$op['sbRes'] = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION'))
						->where('subscribe_id',$packInfo->subscribe_id)
						->where('account_id',$packInfo->account_id)
						->where('is_deleted',$this->config->get('constants.OFF'))
						->update($stdata);
				
				if($op['sbRes']){
					$op['usrSaleRes'] = $this->update_salesPoints([
						  'account_id' => $packInfo->account_id,
						  'aff_pro_rank_id' => $packInfo->aff_pro_rank_id,
						  'package_id' => $packInfo->package_id,
						  'qv' => $packInfo->package_qv,
						  'weekly_capping_qv' => $packInfo->weekly_capping_qv,
						  'bv' => $packInfo->amount]);


					$op['pkCB'] = $this->releasePackageCashback($packInfo);				
					$op['pkRef'] = $this->bonusObj->addReferralBonus($packInfo);					
				}				
			}
		}
		return $op;
	}

	public function update_salesPoints ($arr = array())
    {
		$op = ['status'=>200,'msg'=>'Sales point not updated'];
        extract($arr);
        $sdata = [];
        $wdata = [
            'account_id'=>$account_id];

        /*if (isset($refcnts))
        {
            $sdata['referral_cnts'] = DB::Raw('referral_cnts+1');
        }*/

        if (isset($bv))
        {
            $sdata['bv'] = DB::Raw('bv+'.$bv);
        }

        if (isset($pro_rank_id))
        {
            $sdata['pro_rank_id'] = $pro_rank_id;
        }

        /*if (isset($refpaidcnts))
        {
            $sdata['referral_paid_cnts'] = DB::Raw('referral_paid_cnts+1');
        }*/

        if (isset($package_id))
        {
            $sdata['recent_package_id'] = $package_id;
            $sdata['recent_package_purchased_on'] = getGTZ();
        }

        if (isset($qv))
        {
            $sdata['qv'] = DB::Raw('qv+'.$qv);			
        }
		//print_r($wdata);
        if (!empty($wdata) && $sdata)
        {
            if(empty($aff_pro_rank_id) || $aff_pro_rank_id==$this->config->get('constants.RANK.AFFILIATE'))
			{					
				$rankInfo = DB::table($this->config->get('tables.AFF_RANKING_LOOKUPS'))
							->where('af_rank_id','=',$this->config->get('constants.RANK.PROMOTER'))
							->first();
							
				if($rankInfo && isset($qv) && $rankInfo->qv <= $qv){
					$sdata['pro_rank_id'] = $this->config->get('constants.RANK.PROMOTER');					
				}
			}

			$result = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where($wdata)
						->update($sdata);
			$op = ['status'=>200,'msg'=>'Sales point updated'];
        }
		return $op;
    }

	public function releasePackageCashback($pack_details){
		$op = ['status'=>200,'msg'=>'vouchers release'];
		if(!empty($pack_details)){		
			
			if($pack_details->shopping_points_bonus>0){
		
				$balInfo = $this->financeObj->get_user_balance($this->config->get('constants.PAYMENT_TYPES.WALLET'),['account_id'=>$pack_details->account_id],$this->config->get('constants.WALLETS.VIB'),$pack_details->currency_id);
				
				$usrbal_upres = $this->financeObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIB'), 'account_id'=>$pack_details->account_id, 'currency_id'=>$pack_details->currency_id, 'amount'=>$pack_details->shopping_points_bonus, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
			
				$transaction_id = \AppService::getTransID($pack_details->account_id);				
				
				$trans = [];
				$trans['account_id'] = $pack_details->account_id;
				$trans['to_account_id'] = $pack_details->account_id;
				$trans['statementline_id'] = $this->config->get('stline.PACKAGE_CASHBACK.CREDIT');
				$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
				$trans['relation_id'] = $pack_details->subscribe_topup_id;
				$trans['amt'] = $pack_details->shopping_points_bonus;
				$trans['handle_amt'] = 0;
				$trans['paid_amt'] = $pack_details->shopping_points_bonus;
				$trans['currency_id'] = $pack_details->currency_id;
				$trans['wallet_id'] = $this->config->get('constants.WALLETS.VIB');
				$trans['transaction_id'] = $transaction_id;
				$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
				$trans['remark'] = addslashes(json_encode(['data'=>['package'=>$pack_details->purchase_code,'code'=>$pack_details->purchase_code]]));
				$trans['created_on'] = getGTZ();
				$trans['current_balance'] = ($balInfo->current_balance+$pack_details->shopping_points_bonus);
				$trans['status'] = $this->config->get('constants.ACTIVE');
				$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
						->insertGetId($trans);
				$op['bonus'][] = !empty($transResID)? 'BP released':'BP Not released';
			}
			if($pack_details->shopping_points_cashback>0){		
				
				$balInfo = $this->financeObj->get_user_balance($this->config->get('constants.PAYMENT_TYPES.WALLET'),['account_id'=>$pack_details->account_id],$this->config->get('constants.WALLETS.VIS'),$pack_details->currency_id);				
				
				$usrbal_Info = $this->financeObj->update_account_balance(array('payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.WALLET'),'wallet_id'=>$this->config->get('constants.WALLETS.VIS'), 'account_id'=>$pack_details->account_id, 'currency_id'=>$pack_details->currency_id, 'amount'=>$pack_details->shopping_points_cashback, 'type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'), 'return'=>'current'));
				
				$transaction_id = \AppService::getTransID($pack_details->account_id);
				
				$trans = [];
				$trans['account_id'] = $pack_details->account_id;
                $trans['to_account_id'] = $pack_details->account_id; 
				$trans['statementline_id'] = $this->config->get('stline.PACKAGE_CASHBACK.CREDIT'); /* package purchase cashback credit */
				$trans['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
				$trans['relation_id'] = $pack_details->subscribe_topup_id;
				$trans['amt'] = $pack_details->shopping_points_cashback;
				$trans['handle_amt'] = 0;
				$trans['paid_amt'] = $pack_details->shopping_points_cashback;
				$trans['currency_id'] = $pack_details->currency_id;
				$trans['wallet_id'] = $this->config->get('constants.WALLETS.VIS');
				$trans['transaction_id'] = $transaction_id;
				$trans['transaction_type'] = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');				
				$trans['remark'] = addslashes(json_encode(['data'=>['package'=>$pack_details->purchase_code,'code'=>$pack_details->purchase_code]]));
				$trans['created_on'] = getGTZ();
				$trans['current_balance'] = ($balInfo->current_balance+$pack_details->shopping_points_cashback);
				$trans['status'] = $this->config->get('constants.ACTIVE');
				//print_r($trans);
				$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
						->insertGetId($trans);
				$op['bonus'][] = !empty($transResID)? 'SP released':'SP Not released';
			}
		}	
		return $op;
	}
}