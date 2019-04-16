<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use Request;
use Response;
use App\Models\Affiliate\AffiliateReports;
use App\Models\Affiliate\scheduler;
use App\Models\Commonsettings;

class scheduleController extends AffBaseController
{

    public function __construct ()
    {
        parent::__construct();
		$this->affiliatereportObj = new AffiliateReports();
        $this->schedulerObj = new scheduler();
        $this->commonObj = new Commonsettings();
    }

	public function promoter_ranking(){
		
		$postdata 			= $this->request->all();
		$arr['account_id']  = $this->account_id;
		$result 			= $this->schedulerObj->add_promoter_ranking($arr);
		return $result;
	}
	
	public function generateTeamCommission ()
    {
        $result = $this->schedulerObj->generateTeamCommission();
		return $result;
    }

    public function generateLeadership_bonus ()
    {
        $result = $this->schedulerObj->generateLeadershipBonus();
		return $result;
    }
	
	public function releaseTeamCommission(){
		$result = $this->schedulerObj->releaseTeamCommission();
		return $result;
	}
	
	public function releaseLeaderShippBonus(){
		$result = $this->schedulerObj->releaseLeaderShippBonus();
		return $result;
	}
}
