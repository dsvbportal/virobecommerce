<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\Referrals;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\Bonus;
use AppService;
class DashboardController extends AffBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->refObj = new Referrals();
		$this->walletObj = new Wallet();
		$this->bonusObj = new Bonus();
    }  
	
	public function dashboard() 
	{ 
        /*$data['referral_today'] 	= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>1,'period'=>'today']);
		$data['referral_week'] 		= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>1,'period'=>'week']);
		$data['referral_month'] 	= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>1,'period'=>'month']);
		$data['referral_total'] 	= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>1]);
		
		$data['team_referral_today'] 	= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>2,'period'=>'today']);		
		$data['team_referral_week']		= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>2,'period'=>'week']);
		$data['team_referral_month'] 	= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>2,'period'=>'month']);
		$data['team_referral_total'] 	= $this->refObj->getReferralCnts($this->userSess->account_id,['type'=>2]);*/
		$data 	= $this->refObj->getReferralCnts($this->userSess->account_id);
		
		$filter['account_id'] = $this->userSess->account_id;
		$filter['currency_id'] = $this->userSess->currency_id;
		$balList = $this->walletObj->my_wallets($filter);
		$balInfo = ['2'=>'','3'=>'','5'=>'','6'=>'','4'=>''];
		if($balList){
			array_walk($balList, function(&$balInfos) use(&$balInfo)
			{		
	$balInfos->current_balance =\CommonLib::currency_format($balInfos->current_balance, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]);
	$balInfos->tot_credit =\CommonLib::currency_format($balInfos->tot_credit, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]);
	$balInfos->tot_debit =\CommonLib::currency_format($balInfos->tot_debit, ['currency_symbol'=>$balInfos->currency_symbol, 'currency_code'=>$balInfos->currency_code, 'value_type'=>(''), 'decimal_places'=>$balInfos->decimal_places]);				
	if(isset($balInfo[$balInfos->wallet_id])){
		$balInfo[$balInfos->wallet_id] = $balInfos;
	}				
			});
			$data['balInfo']= $balInfo;
		}
		$data['cv']= (object)['current'=>$this->bonusObj->getCV_totals(['account_id'=>$this->userSess->account_id]),
		'last'=>$this->bonusObj->getCV_totals(['account_id'=>$this->userSess->account_id],'last')];
		
		$data['qv']= (object)[
			'current'=>$this->bonusObj->getQV_totals(['account_id'=>$this->userSess->account_id]),
			'last'=>$this->bonusObj->getQV_totals(['account_id'=>$this->userSess->account_id],'last')];				
		return view('affiliate.dashboard',$data);
	}
}
