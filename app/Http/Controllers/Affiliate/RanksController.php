<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\User;
use App\Models\Affiliate\AffModel;

class RanksController extends AffBaseController {
    
    private $userObj = '';
    
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
    }	
	
	public function myrank(){
		$data = array();
		$arr['account_id'] = $this->userSess->account_id;
		$data = $this->affObj->get_ranks($arr);
		//echo '<pre>'; print_r($data);exit;
		return view('affiliate.ranks.myrank',$data);
	}
	
	public function myrank_history(){
		$data = array();
		return view('affiliate.ranks.myrank_history',$data);
	}
	
	public function eligibilities(){
		$data = array();
		return view('affiliate.ranks.rank_eligibilities',$data);
	}
}