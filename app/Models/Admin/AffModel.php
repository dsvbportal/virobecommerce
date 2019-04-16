<?php
namespace App\Models\Admin;

use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;

class AffModel extends BaseModel {

public function __construct() {
	parent::__construct();		
	$this->lcObj = new LocationModel;
}
public function save_user($postdata)
{
	$direct_intro_code = '';
	$rank = 1;
	$level = 0;
	$currencies = '';
	$user_code = '';
	$user_role = 1;
	$referral = '';
	$referral_id = '';
	$lineage = '/';
	$position_lineage = '1';
	$status = 0;
	$account_paid_status = 0;
	$direct_lineage = $directLineAge = '/';
	$user_id_details = '';
	$date = date('Y-m-d H:i:s');
	$password = md5($postdata['password']);
	$postdata['trans_pass_key'] = rand(1111,9999);
	$activation_key = rand(1111, 9999).time();
	$country_info = $this->lcObj->getCountry(['country_code'=>$postdata['country'],'allow_signup'=>true]);
	/* --------Assigning the Post Values -------- */
	$insert_account_mst['is_affiliate'] = config('constants.ON');
	$insert_account_mst['uname'] = $postdata['uname'];
	$insert_account_mst['email'] = $postdata['email'];
	$insert_account_mst['mobile'] = $postdata['mobile'];
	$insert_account_mst['pass_key'] = $password;
	$insert_account_mst['trans_pass_key'] =md5($postdata['trans_pass_key']);
	$insert_account_mst['last_active'] = $date;
	$insert_account_mst['signedup_on'] = $date;
	$insert_account_mst['activated_on'] = $date;
	$insert_account_mst['account_type_id'] = config('constants.ACCOUNT_TYPE.USER');
	$insert_account_mst['status'] = config('constants.ACTIVE');				
	$id = DB::table(config('tables.ACCOUNT_MST'))
			->insertGetId($insert_account_mst);
	if($id > 0){
		
		$user_code = $this->commonObj->createUserCode($id);
		DB::table($this->config->get('tables.ACCOUNT_MST'))
				->where('account_id','=',$id)
				->update(['user_code'=>$user_code]);
		
		$insert_account_tree['account_id'] = $id;
		$insert_account_tree['upline_id'] = 0;
		$insert_account_tree['sponsor_id'] = 0;
		$insert_account_tree['sponsor_lineage'] = '/';
		$insert_account_tree['rank'] =  $rank;
		$insert_account_tree['level'] = $level;
		$insert_account_tree['can_sponsor'] = 1;
		$insert_account_tree['lft_node'] = 1;
		$insert_account_tree['rgt_node'] = 2;
		$insert_account_tree['rank'] = $position_lineage;
		$insert_account_tree['nwroot_id'] = $id;
		DB::table(config('tables.ACCOUNT_TREE'))
			->insertGetId($insert_account_tree);
			
		$insert_account_details['account_id'] = $id;
		$insert_account_details['firstname'] = $postdata['first_name'];
		$insert_account_details['lastname'] = $postdata['last_name'];
		$insert_account_details['gender'] = $postdata['gender'];
		$insert_account_details['dob'] = $postdata['dob'];
		$usRes= DB::table(config('tables.ACCOUNT_DETAILS'))
				->insertGetId($insert_account_details);
				
		$insert_setting = '';
		$insert_setting['account_id'] = $id;
		$insert_setting['country_id'] = $country_info->country_id;
		$insert_setting['currency_id'] = $country_info->currency_id;
		$insert_setting['activation_key'] = $activation_key;			
		$usRes= DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
							->insertGetId($insert_setting);		
							
		$insert_ranking = '';
		$insert_ranking['account_id'] = $id;
		$insert_ranking['bv']=0;
		$insert_ranking['cv']=0;
		$insert_ranking['qv']=0;
		$insert_ranking['gqv']=0;
		DB::table($this->config->get('tables.ACCOUNT_SALE_POINTS'))
							->insertGetId($insert_ranking);
		if (!empty($usRes) && isset($usRes))
		{
		   /*  $response['status'] = "success";
			   $response['msg'] = " <div class='alert alert-success'>Affiliate created successfully</div>";
			   return json_encode($response);; //Session::put('success','User added Successfully'); */
		   return array(
					  'status'=>config('httperr.SUCCESS'),
					   'msg'=>'Affiliate created successfully',
					   );
		}
		else
		{
			$response['status'] = "error";
			$response['msg'] = " <div class='alert alert-danger'>We have issue on creating Affiliate Account. Please try later</div>";
			return json_encode($response); //Session::put('error','Something Went Wrong');
		}
	}
	return false;
}
public function check_user ($username = 0, $param = array()) {
	$qry = '';
	$qry = DB::table(Config('tables.ACCOUNT_MST').' as um')
			->where('um.uname', '=', $username)
			->where('um.is_deleted',0);
			 $resFound =$qry->get();
	  return $resFound;
}
	
 public function check_email ($email = 0, $param = array()) {
	$qry = '';
	$qry = DB::table(Config('tables.ACCOUNT_MST').' as um')
			->where('um.email', '=', $email)
			->where('um.is_deleted', Config('constants.OFF'));
	$resFound =$qry->get();
	return $resFound;
}
 public function check_mobile ($mobile = 0, $param = array()) {
	$qry = '';
	$result = false;
	$qry = DB::table(Config('tables.ACCOUNT_MST').' as um')
			->where('um.mobile', '=', $mobile)
			->where('um.is_deleted', Config('constants.OFF'));
	$resFound =$qry->get();
	return $resFound;
}

public function manage_affiliate_details($arr = array(), $count = false){

			extract($arr);
			$query = DB::table(config('tables.ACCOUNT_MST').' as um')
						->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
						->join(config('tables.ACCOUNT_TREE').' as tr', 'tr.account_id', ' = ', 'um.account_id')
						->join(config('tables.ACCOUNT_MST').' as tm','tm.account_id', ' = ', 'tr.sponsor_id')
						->leftjoin(config('tables.ACCOUNT_MST').' as up','up.account_id', ' = ', 'tr.upline_id')
						->join(config('tables.ACCOUNT_MST').' as rd', 'rd.account_id', ' = ', 'tr.nwroot_id')
						->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'um.account_id')
						->leftJoin(config('tables.LOCATION_COUNTRY').' as lcu', 'lcu.country_id', ' = ', 'acp.country_id')
						->leftJoin(config('tables.ACCOUNT_TREE').' as at', 'at.account_id', ' = ', 'um.account_id')						
						->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
					/* ->where('at.level',$level) */
						->select(DB::Raw('um.signedup_on,um.account_id,um.user_code,um.uname,um.email,um.activated_on,um.block,um.status,concat_ws(" ",ud.firstname,ud.lastname) as fullname,concat_ws(" ",lcu.phonecode,um.mobile) as mobile,lcu.country as country_name,usl.status_name,tm.uname  as reffered_by,rd.uname as rootuser,up.uname as upline_id,tr.rank'))
						->where('um.is_deleted','=',$this->config->get('constants.OFF'));
			if(!empty($user_role)){
				$query->where("um.is_affiliate",$user_role);
			} 
			if(!empty($is_affiliate)){
				$query->where("um.is_affiliate",$is_affiliate);
				$query->where("at.level",$level);
			} 	 
			if(isset($status) && !empty($status)){
				$query->where("um.status",$status);
			} 
			if(!empty($free_is_affiliate)){
				$query->where("um.is_affiliate",$free_is_affiliate);
				$query->where("at.can_sponsor",$can_sponser);
			}
			
			if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date))	{ 
				$query->whereDate('um.signedup_on', '>=', getGTZ($start_date,'Y-m-d'));
				$query->whereDate('um.signedup_on', '<=', getGTZ($end_date,'Y-m-d'));
			}
			else if (!empty($start_date) && isset($start_date)){ 
				$query->whereDate('um.signedup_on', '<=', getGTZ($start_date,'Y-m-d'));
			}
			else if (!empty($end_date) && isset($end_date)){ 
				$query->whereDate('um.signedup_on', '>=', getGTZ($end_date,'Y-m-d'));
			}  
			
		  if(isset($search_text) && !empty($search_text))
			{ 
	
		        $search_text='%'.$search_text.'%';
				if(!empty($filterchk) && !empty($filterchk))
				{   
					$search_field=['UserName'=>'um.uname','User_code'=>'um.user_code','FullName'=>'concat_ws(" ",ud.firstname,ud.lastname)','Email'=>'um.email','Mobile'=>'concat_ws(" ",ud.phonecode,ud.mobile)','ReferredBy'=>'tm.uname','ReferredGroup'=>'rd.uname'];
					$query->where(function($sub) use($filterchk,$search_text,$search_field){
						foreach($filterchk as $search)
						{  
							if(array_key_exists($search,$search_field)){
							  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
							} 
						}
					});
				}
				else{
					$query->where(function($wcond) use($search_text){
					   $wcond->Where('um.uname','like',$search_text)
							 ->orwhere(DB::Raw('concat_ws(" ",ud.firstname,ud.lastname)'),'like',$search_text)
							 ->orwhere('um.email','like',$search_text)
							 ->orwhere('um.mobile','like',$search_text)
							 ->orwhere('tm.uname','like',$search_text)
							 ->orwhere('rd.uname','like',$search_text);
					}); 
				} 			
			}   
			if (isset($orderby) && isset($order))
			{
			   if($orderby == 'signedup_on'){
					$query->orderBy('um.signedup_on', $order);
			   }elseif($orderby == 'uname'){
					$query->orderBy('um.uname', $order);
			   }elseif($orderby == 'country_name'){
					$query->orderBy('lcu.name', $order);
			   }elseif($orderby == 'activated_on'){
					$query->orderBy('um.activated_on', $order);
			   }elseif($orderby == 'status'){
					$query->orderBy('um.status', $order);
			   }
			} 
			if (isset($country) && !empty($country))
			{  
				$query->where('uad.country_id',$country);
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
				$result= $query->orderBy('um.account_id', 'ASC') 
							   ->get();
			if(!empty($result)) {
				array_walk($result, function(&$data)
				{  
					$data->actions = [];
					$data->signedup_on = !empty($data->signedup_on) ? showUTZ($data->signedup_on, 'd-M-Y H:i:s'):'';							
					$data->actions[] = ['url'=>route('admin.account.view-details', ['account_id'=>$data->account_id,]), 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.view_details')]; 
					
					$data->actions[] = ['url'=>route('admin.account.edit-details', ['account_id'=>$data->account_id]), 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.edit')];
					
					$data->actions[] = ['url'=>route('admin.account.change-password', ['account_id'=>$data->account_id]), 'class'=>'change_password', 'data'=>[
						'account_id'=>$data->account_id,
						'uname'=>$data->uname,
						'full_name'=>$data->fullname."($data->user_code)"
					], 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_pwd')];
					
					$data->actions[] = ['url'=>route('admin.account.reset-pin', ['account_id'=>$data->account_id]), 'class'=>'change_pin', 'data'=>[
						'account_id'=>$data->account_id,
						'full_name'=>$data->fullname."($data->user_code)",
						'uname'=>$data->uname
					], 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.reset_pin')];
					
					if($data->block == config('constants.OFF'))
					{
						$data->actions[] = ['url'=>route('admin.account.block_status', ['account_id'=>$data->account_id, 'status'=>'block']),'class'=> 'block_status', 'data'=>[
							'account_id'=>$data->account_id,
							'status'=>'block'
						],'label'=>trans('admin/affiliate/admin.block')];
						$data->status_class = 'success';
					}
					else
					{
						$data->actions[] = ['url'=>route('admin.account.block_status', ['account_id'=>$data->account_id, 'status'=>'unblock']), 'class'=>'block_status', 'data'=>[
							'account_id'=>$data->account_id,
							'status'=>'unblock'
						],'label'=>trans('admin/affiliate/admin.un_block')];
						$data->status_name = 'Blocked';
						$data->status_class = 'danger';
					}
					
					$data->actions[] = ['class'=> 'edit_email',
					  'data'=>['uname'=>$data->uname,'email'=>$data->email,'account_id'=>$data->account_id],'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_email')];	
					  
					$data->actions[] = ['class'=> 'edit_mobile',
					  'data'=>['uname'=>$data->uname,'mobile'=>$data->mobile,'account_id'=>$data->account_id],'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_mobile')]; 
				});
				return $result;
		} 
	}
	return false;
}

      public function manage_root_affiliates($arr = array(), $count = false){
			extract($arr);
			$query = DB::table(config('tables.ACCOUNT_TREE').' as tr')
						->join(config('tables.ACCOUNT_MST').' as um', 'tr.account_id', ' = ', 'um.account_id')
						->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')							
						->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'um.account_id')
						->join(config('tables.LOCATION_COUNTRY').' as lcu', 'lcu.country_id', ' = ', 'acp.country_id')
						->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
						->select(DB::Raw('um.signedup_on,um.account_id,um.user_code,um.uname,um.email,um.activated_on,um.block,um.status,
						concat_ws(" ",ud.firstname,ud.lastname) as fullname,concat_ws(" ",lcu.phonecode,um.mobile) as mobile,lcu.country as country_name,usl.status_name,tr.rank'))
						->whereRaw("tr.account_id = tr.nwroot_id")		
						->where("tr.level",'=',0)
						->where("um.is_affiliate",'=',$this->config->get('constants.ON'))
						->where('um.is_deleted','=',$this->config->get('constants.OFF'));
			
			if(isset($status) && !is_null($status)){
			
				$query->where("um.status",$status);
			} 				
			if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date))	{ 
				$query->whereDate('um.signedup_on', '>=', getGTZ($start_date,'Y-m-d'));
				$query->whereDate('um.signedup_on', '<=', getGTZ($end_date,'Y-m-d'));
			}
			else if (!empty($start_date) && isset($start_date)){ 
				$query->whereDate('um.signedup_on', '<=', getGTZ($start_date,'Y-m-d'));
			}
			else if (!empty($end_date) && isset($end_date)){ 
				$query->whereDate('um.signedup_on', '>=', getGTZ($end_date,'Y-m-d'));
			}  
			
			if(isset($search_text) && !empty($search_text))
			{ 
				$search_text='%'.$search_text.'%'; 
				if(!empty($filterchk) && isset($filterchk))
				{   
		
					$search_field=['UserName'=>'um.uname','User_code'=>'um.user_code','FullName'=>'concat_ws(" ",ud.firstname,ud.lastname)','Email'=>'um.email','Mobile'=>'concat_ws("-",lcu.phonecode,um.mobile)'];
					$query->where(function($sub) use($filterchk,$search_text,$search_field){
						foreach($filterchk as $search)
						{  
							if(array_key_exists($search,$search_field)){
							  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
							} 
						}
					});
				}
				else{
					$query->where(function($wcond) use($search_text){
					   $wcond->Where('um.uname','like',$search_text)
							 ->orwhere(DB::Raw('concat_ws(" ",ud.firstname,ud.lastname)'),'like',$search_text)
							 ->orwhere('um.user_code','like',$search_text)
							 ->orwhere('um.email','like',$search_text)
							 ->orwhere(DB::Raw('concat_ws("-",lcu.phonecode,um.mobile)'),'like',$search_text);
					}); 
				} 	
			}
			if (isset($orderby) && isset($order))
			{
			   if($orderby == 'signedup_on'){
					$query->orderBy('um.signedup_on', $order);
			   }elseif($orderby == 'uname'){
					$query->orderBy('um.uname', $order);
			   }elseif($orderby == 'country_name'){
					$query->orderBy('lcu.name', $order);
			   }elseif($orderby == 'activated_on'){
					$query->orderBy('um.activated_on', $order);
			   }elseif($orderby == 'status'){
					$query->orderBy('um.status', $order);
			   }
			} 
			if (isset($country) && !empty($country))
			{  
				$query->where('uad.country_id',$country);
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
				$result= $query->orderBy('um.account_id', 'ASC') 
							   ->get();
			if(!empty($result)) {
				array_walk($result, function(&$data)
				{  
					$data->actions = [];
					$data->signedup_on = !empty($data->signedup_on) ? showUTZ($data->signedup_on, 'd-M-Y H:i:s'):'';							
					$data->actions[] = ['url'=>route('admin.account.view-details', ['account_id'=>$data->account_id,]), 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.view_details')]; 
					
					$data->actions[] = ['url'=>route('admin.account.edit-details', ['account_id'=>$data->account_id]), 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.edit')];
					
					$data->actions[] = ['url'=>route('admin.account.change-password', ['account_id'=>$data->account_id]), 'class'=>'change_password', 'data'=>[
						'account_id'=>$data->account_id,
						'uname'=>$data->uname,
						'full_name'=>$data->fullname."($data->user_code)"
					], 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_pwd')];
					
					$data->actions[] = ['url'=>route('admin.account.reset-pin', ['account_id'=>$data->account_id]), 'class'=>'change_pin', 'data'=>[
						'account_id'=>$data->account_id,
						'full_name'=>$data->fullname."($data->user_code)",
						'uname'=>$data->uname
					], 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.reset_pin')];
					
					if($data->block == config('constants.OFF'))
					{
						$data->actions[] = ['url'=>route('admin.account.block_status', ['account_id'=>$data->account_id, 'status'=>'block']),'class'=> 'block_status', 'data'=>[
							'account_id'=>$data->account_id,
							'status'=>'block'
						],'label'=>trans('admin/affiliate/admin.block')];
						$data->status_class = 'success';
					}
					else
					{
						$data->actions[] = ['url'=>route('admin.account.block_status', ['account_id'=>$data->account_id, 'status'=>'unblock']), 'class'=>'block_status', 'data'=>[
							'account_id'=>$data->account_id,
							'status'=>'unblock'
						],'label'=>trans('admin/affiliate/admin.un_block')];
						$data->status_name = 'Blocked';
						$data->status_class = 'danger';
					}
					
					$data->actions[] = ['class'=> 'edit_email',
					  'data'=>['uname'=>$data->uname,'email'=>$data->email,'account_id'=>$data->account_id],'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_email')];	
					  
					$data->actions[] = ['class'=> 'edit_mobile',
					  'data'=>['uname'=>$data->uname,'mobile'=>$data->mobile,'account_id'=>$data->account_id],'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_mobile')]; 
				});
				return $result;
		} 
	}
	return false;
}	

 /*  public function view_details ($uname) {
	$result = DB::table(config('tables.ACCOUNT_MST').' as am')
			->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
			->join(config('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', ' = ', 'am.account_id')
		   ->selectRaw('am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,ast.is_verified,ad.firstname,ad.lastname,concat_ws(" ",ad.firstname,ad.lastname) as fullname,ad.dob,am.mobile')
			->where('am.is_deleted',config('constants.OFF'))
		   // ->where('am.system_role_id',config('constants.SYSTEM_ROLE.ADMIN'))
			->where('am.uname', $uname)
			->first();

	if (!empty($result))
	{
		$result->signedup_on = showUTZ($result->signedup_on, 'd-M-Y H:i:s');
		$result->activated_on = showUTZ($result->activated_on, 'd-M-Y H:i:s');
		$result->dob = showUTZ($result->dob, 'd-M-Y H:i:s');
	   
		if ($result->block == config('constants.OFF'))
		{
			 $result->status_name = trans('admin/account/user.user_account_status.'.$result->status);
			 
			$result->status_disp_class =config('dispclass.user_account_status.'.$result->status); 
		}
		else
		{
			$result->status_name = trans('admin/account/user.user_account_status.'.$result->status);
			$result->status_disp_class = config('dispclass.user_account_status.'.$result->status); 
		}
	}
	return $result;
} */
	public function getUserinfo($account_id) {	
  /*   if (!empty($params) && is_array($params)){		 */	
		
		if((isset($account_id) && $account_id > 0) || (isset($uname) && $uname != NULL)) {
		
			$query = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as am')
					->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ad', 'ad.account_id', '=', 'am.account_id')					
					->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'am.account_id')
					->join($this->config->get('tables.ACCOUNT_TREE') . ' as atr', 'atr.account_id', '=', 'am.account_id')
					->leftjoin($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','atr.sponsor_id')
					->leftjoin($this->config->get('tables.ACCOUNT_DETAILS') . ' as spad','spad.account_id','=','spam.account_id')
					->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
					->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')
					->leftjoin($this->config->get('tables.AFF_RANKING_LOOKUPS') . ' as ar', 'ar.af_rank_id', '=', 'atr.pro_rank_id')
					->leftjoin($this->config->get('tables.AFF_TYPES_LANG').' as aty',function($join){
						$join->on('aty.aff_type_id', '=', 'atr.aff_type_id')
						->where('aty.lang_id','=',$this->config->get('app.locale_id'));						
					})
					->leftjoin($this->config->get('tables.AFF_PACKAGE_LANG') . ' as apk', function($join){
						$join->on('apk.package_id','=', 'atr.recent_package_id')
						->where('apk.lang_id','=',$this->config->get('app.locale_id'));
					})
											
					->leftjoin($this->config->get('tables.GENDER_LANG') . ' as gl ', function($join){
						$join->on('gl.gender_id', '=', 'ad.gender')
						->where('gl.lang_id','=',$this->config->get('app.locale_id'));	
					})
					->leftjoin($this->config->get('tables.MARTIAL_STATUS_LANG') . ' as mts ', function($join){
						$join->on('mts.marital_status_id', '=', 'ad.marital_status')
						->where('mts.lang_id','=',$this->config->get('app.locale_id'));	
					})
					->leftjoin($this->config->get('tables.ADDRESS_MST') . ' as adm ', function($join){
						$join->on('adm.relative_post_id', '=', 'ad.account_id')
						->where('adm.post_type','=',$this->config->get('constants.ADDRESS.PRIMARY'));
					})
					->select(DB::raw('am.account_id,am.user_code,am.uname,am.email,atr.created_on,ad.firstname,ad.lastname,ad.dob,ad.pan_no,ad.gender as gender_id,concat(ad.firstname," ",ad.lastname) as full_name,mts.marital_status_id,mts.marital_status,ad.gardian,ar.rank,aty.aff_type,apk.package_name,lc.phonecode,lc.has_pancard,am.mobile,ad.profile_img,adm.formated_address,adm.flatno_street,adm.landmark,adm.address,adm.postal_code,lc.country,adm.formated_address,gl.gender,lc.country_id,lc.country,spam.uname as sponsor_uname,spam.user_code as sponsor_code,concat_ws(\' \',spad.firstname,spad.lastname) as sponsor_name'));
				
			if ($account_id){
				$query ->where('am.account_id', '=', $account_id);
			}else {
				$query ->where('am.uname', '=', $uname);
			}	   
			
			$query->where('am.is_deleted',$this->config->get('constants.OFF'));
			
			$res =  $query ->first();

			 if ($res) {
				$res->gardian = ucwords($res->gardian);
				$res->full_name = ucwords($res->full_name);
				$res->sponsor_name = ucwords($res->sponsor_name);
				$res->created_on = showUTZ($res->created_on);
				if(!empty($res->profile_img) &&  file_exists(config('constants.ACCOUNT.PROFILE_IMG.PATH').$res->profile_img)){
					
						$res->profile_img = asset(config('constants.ACCOUNT.PROFILE_IMG.SM').$res->profile_img);
					} else {
				
						$res->profile_img = asset(config('constants.ACCOUNT.PROFILE_IMG.PATH').config('constants.ACCOUNT.PROFILE_IMG.DEFAULT'));
					}
				return $res;
			} 
		}
	/* } */
	return NULL;
}

public function user_block_status (array $data = array())
{
	$op = array();
	extract($data);
	
	if (isset($status) && $status == 1)
	{
		$query= DB::table(config('tables.ACCOUNT_MST'))
						->where('is_deleted',config('constants.NOT_DELETED'))
						->where('account_id', $account_id)
						->update(['block'=>config('constants.BLOCK')]);
						if(!empty($query)){
						   return json_encode(array(
							'status'=>200,
							'msg'=>trans('admin/affiliate/settings/block.affiliate_block'),
							'alertclass'=>'alert-success'));
						}
	}
	else
	{
		$query_unblock= DB::table(config('tables.ACCOUNT_MST'))
						->where('is_deleted',config('constants.NOT_DELETED'))
						->where('account_id', $account_id)
						->update(['block'=>config('constants.UNBLOCK')]);
					if(!empty($query_unblock)){
						 return json_encode(array(
						 'status'=>200,
						 'msg'=>trans('admin/affiliate/settings/block.affiliate_unblock'),
						 'alertclass'=>'alert-success'));
						
						}
	}
}
public function Update_email($postdata){
	 $uname=$postdata['uname'];
	if(!empty($postdata['user_account_id'])){
		 $data['email'] =$postdata['email'];
		  if ($data['email'] != DB::table(config('tables.ACCOUNT_MST'))
						->where('account_id',$postdata['user_account_id'])
						->value('email'))
		   {
	$status = DB::table(config('tables.ACCOUNT_MST'))
				->where('account_id',$postdata['user_account_id'])
				->update($data);
			
		if (!empty($status))
			{
				/*  $this->commonstObj->logoutAllDevices($postdata['user_account_id']); */
				return array(
				   'status'=>config('httperr.SUCCESS'),
					'msg'=>trans('admin/affiliate/settings/change_email.update_email_success',['uname'=>$uname])
					);
			}
			else
			{
				return array(
					'msg'=>trans('admin/general.something_wrong'),
					'status'=>config('httperr.UN_PROCESSABLE'));
			}
		}
		else{
			return array(
					'msg'=>trans('admin/affiliate/settings/change_email.same_old'),
					'status'=>config('httperr.UN_PROCESSABLE'));
		}
  }
  return json_encode(array('msg'=>trans('admin/affiliate/settings/change_email.missing_parameters'), 'alertclass'=>'alert-warning'));
}
public function Update_mobile($postdata){
  
   $uname=$postdata['uname'];
 if(!empty($postdata['mobile_account_id'])){
	 $data['mobile'] =$postdata['mobile'];
	  if ($data['mobile'] != DB::table(config('tables.ACCOUNT_MST'))
						->where('account_id',$postdata['mobile_account_id'])
						->value('mobile'))
						{
   $status = DB::table(config('tables.ACCOUNT_MST'))
		->where('account_id',$postdata['mobile_account_id'])
		->update($data);
		
		if (!empty($status)){
			/*  $this->commonstObj->logoutAllDevices($postdata['mobile_account_id']); */
				return array(
				'status'=>config('httperr.SUCCESS'),
				'msg'=>trans('admin/affiliate/admin.update_mobile_success',['uname'=>$uname]),
				);
			}
			else{
				return array(
				'msg'=>trans('admin/general.something_wrong'),
				'status'=>config('httperr.UN_PROCESSABLE'));
			}
		}
		else{
			return array(
					'msg'=>trans('admin/affiliate/settings/change_email.mobile_same_old'),
					'status'=>config('httperr.UN_PROCESSABLE'));
		}
	 }
	 return json_encode(array('msg'=>trans('admin/affiliate/settings/change_email.missing_parameters'), 'alertclass'=>'alert-warning'));
}
public function update_password ($postdata)
{
   $uname=$postdata['uname'];		
	if($postdata['account_id']>0 && !empty(trim($postdata['new_pwd'])))
	{
		$data['pass_key'] = md5($postdata['new_pwd']);
	   
		if ($data['pass_key'] != DB::table(config('tables.ACCOUNT_MST'))
						->where('account_id',$postdata['account_id'])
						->value('pass_key'))
		   { 
			$status = DB::table(config('tables.ACCOUNT_MST'))
				->where('account_id',$postdata['account_id'])
				->update($data);
				
			if (!empty($status) && isset($status))
			{
				/* $this->commonstObj->logoutAllDevices($postdata['account_id']); */
				return array(
					  'status'=>200,
					   'msg'=>trans('admin/affiliate/settings/changepwd.password_changed',['uname'=>$uname]),
						'alertclass'=>'alert-success');
			}
			else
			{
				return array(
					'msg'=>trans('general.something_wrong'),
					'status'=>config('httperr.UN_PROCESSABLE'));
			}
		}
		else{
			return [
					'error'=>[
					"new_pwd" => trans('admin/affiliate/settings/changepwd.same_as_old'),
					],
				'status'=>$this->config->get('httperr.PARAMS_MISSING')];
		}
	}
	return json_encode(array('msg'=>trans('admin/affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
}	
public function user_edit ($account_id) {
	
return  $result = DB::table(config('tables.ACCOUNT_MST').' as am')
	->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
	->join(config('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', ' = ', 'am.account_id')
	->selectRaw('am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,am.email,am.mobile,ad.gender,ast.is_verified,ad.firstname,ad.lastname,concat_ws(" ",ad.firstname,ad.lastname) as fullname,ad.dob,am.mobile,am.account_id')
	->where('am.is_deleted',config('constants.NOT_DELETED'))
	->where('am.account_id', $account_id)
	->first();	
 }
public function user_update (array $data = array())
{
extract($data);
	if (isset($account_id) && !empty($account_id))
	{
		$result = DB::table(config('tables.ACCOUNT_MST'))
				->where('is_deleted', config('constants.NOT_DELETED'))
				->where('account_id', $account_id)
				->select('account_id')
				->first();
			 DB::table(config('tables.ACCOUNT_MST'))
					 ->where('account_id', $account_id)
				   ->update(array(
				'email'=>$email,
				'mobile'=>$mobile));
	
		if (isset($result->account_id) && $result->account_id > 0)
		{
			$result2 = DB::table(config('tables.ACCOUNT_DETAILS'))
					->where('account_id', $result->account_id)
					->update(array(
				'firstname'=>$first_name,
				'lastname'=>$last_name,
				'dob'=>getGTZ($dob, 'Y-m-d'),
				'gender'=>$gender,
			  /*   'updated_on'=>getGTZ() */));
			return json_encode(array(
					'status'=>200,
					'msg'=>trans('admin/affiliate/settings/user_edit.user_details_update',['uname'=>$uname]),
					'alertclass'=>'alert-success'));
		}
		return NULL;
	}
	return NULL; 
}	
public function update_pin ($postdata)
{
	$uname=$postdata['uname'];
	if($postdata['account_id']>0 && !empty(trim($postdata['new_pin'])))
	{
		$data['trans_pass_key'] = md5($postdata['new_pin']);
	   
		if ($data['trans_pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
						->where('account_id',$postdata['account_id'])
						->value('trans_pass_key'))
		   { 
			$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
				->where('account_id',$postdata['account_id'])
				->update($data);
			if (!empty($status) && isset($status))
			{
				return array(
					'status'=>200,
					'msg'=>trans('admin/affiliate/admin.pin_changed',['uname'=>$uname]));
			}
			else
			{
				return array(
					'msg'=>trans('general.something_wrong'),
					'status'=>$this->config->get('httperr.UN_PROCESSABLE'));
			  }
		   }
			else{
				 return [
					  'error'=>[
					"new_pin" => trans('admin/affiliate/admin.same_as_old'),
					],
				'status'=>$this->config->get('httperr.PARAMS_MISSING')];
		}
	}
	return json_encode(array('msg'=>trans('admin/affiliate/admin.missing_parameters'), 'alertclass'=>'alert-warning'));
}


public function account_validate($postdata) {
	$status = '';
	$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
			->where(function($c) use($postdata){
				$c->where('uname',$postdata['uname'])
				->orWhere('email',$postdata['uname'])
				->orWhere('user_code',$postdata['uname']);
			})
		   ->where('is_affiliate', '=', $this->config->get('constants.ON'))
			->where('is_deleted', '=', $this->config->get('constants.OFF'))
			->where('login_block', '=', $this->config->get('constants.OFF'))
			->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER')])
			->select(DB::Raw('account_id,user_code,pass_key,email,uname,mobile,login_block,block,is_affiliate,is_closed,account_type_id'));		
	//echo '<pre>';
	//print_R($fisrtQry); die; 
			
   $userData = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
			->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as st', 'st.account_id', '=', 'um.account_id')
			->join($this->config->get('tables.ACCOUNT_TREE') . ' as atr', 'atr.account_id', '=', 'um.account_id')
			->leftjoin($this->config->get('tables.AFF_RANKING_LOOKUPS') . ' as ar', 'ar.af_rank_id', '=', 'atr.pro_rank_id')
			->join($this->config->get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'um.account_type_id')
			->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'st.currency_id')			
			->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'st.country_id')			
			->selectRaw('um.account_id,um.user_code,st.language_id,um.is_affiliate,atr.can_sponsor,atr.pro_rank_id,ar.rank as pro_rank,um.account_type_id,at.account_type_name,um.is_closed,um.pass_key,um.uname,concat_ws(\' \',ud.firstname,ud.lastname) as full_name,ud.firstname,ud.lastname,um.mobile,lc.country,lc.phonecode,ud.profile_img,um.email,st.country_id,st.currency_id,cur.currency as currency_code,um.block,um.login_block,st.is_mobile_verified,st.is_email_verified,st.is_verified')
			->addBinding($fisrtQry->getBindings())
			->first();	
		/* echo '<pre>';
	print_R($userData); die;*/
	
	if (!empty($userData)) {
		if ($userData->is_closed == $this->config->get('constants.OFF')) {
			if ($userData->login_block == $this->config->get('constants.OFF')) {
			
				/* if ((isset($postdata['qlogin'])) && ($postdata['qlogin'] == true)) {
						echo "erasaEE"; die; */
					if(file_exists(config('constants.ACCOUNT.PROFILE_IMG.PATH').$userData->profile_img)){
						$userData->profile_imagename = $userData->profile_img;
						$userData->has_profile_img = $this->config->get('constants.ON');
						$userData->profile_img = asset(config('constants.ACCOUNT.PROFILE_IMG.SM').$userData->profile_img);
					} else {
						$userData->profile_imagename = config('constants.ACCOUNT.PROFILE_IMG.DEFAULT');
						$userData->has_profile_img = $this->config->get('constants.OFF');
						$userData->profile_img = asset(config('constants.ACCOUNT.PROFILE_IMG.SM').config('constants.ACCOUNT.PROFILE_IMG.DEFAULT')) ;
					}
					
					$sesdata = array(
							'account_id' => $userData->account_id,
							'uname' => $userData->uname,
							'user_code' => $userData->user_code,
							'full_name' => $userData->full_name,
							'firstname' => $userData->firstname,
							'lastname' => $userData->lastname,
							'email' => $userData->email,
							'is_affiliate' => $userData->is_affiliate,
							'can_sponsor' => $userData->can_sponsor,
							'pro_rank_id' => $userData->pro_rank_id,
							'pro_rank' => $userData->pro_rank,
							'account_type_name' => $userData->account_type_name,
							'account_type_id' => $userData->account_type_id,								
							'currency_id' => $userData->currency_id,
							'country' => $userData->country,
							'language_id' => $userData->language_id,
							'country_id' => $userData->country_id,
							'currency_code' => $userData->currency_code,
							'is_mobile_verified' => $userData->is_mobile_verified,
							'is_email_verified' => $userData->is_email_verified,
							'currency_code' => $userData->currency_code,                                
							'is_verified' => $userData->is_verified,
							'block' => $userData->block,
							'mobile' => $userData->mobile,
							'phonecode' => $userData->phonecode,								
							'profile_imagename' => $userData->profile_imagename,
							'profile_image' => $userData->profile_img,
							'has_profile_img' => $userData->has_profile_img);
							

				   
					$currentdate = date($this->config->get('constants.date_format')); 

					$userData->token = $this->config->get('device_log')->token;
					$last_active = getGTZ();
					
					DB::table($this->config->get('tables.DEVICE_LOG'))
							->where('device_log_id', $this->config->get('device_log')->device_log_id)
							->update(array('account_id'=>$userData->account_id, 'status'=>$this->config->get('constants.ACTIVE'))); 
					
					$account_log_id = DB::table($this->config->get('tables.ACCOUNT_LOGIN_LOG'))
							->insertGetID(array('device_log_id'=>$this->config->get('device_log')->device_log_id, 'account_id'=>$userData->account_id, 'login_on'=>$last_active)); 
							
					$sesdata['account_log_id'] = $account_log_id;
					$sesdata['last_logged_time'] = date('h:i A d M',strtotime($last_active));						
					DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id', $userData->account_id)
							->update(array('last_active'=>$last_active));
							
					 $this->session->put($this->sessionName, (object)$sesdata);
					 
					return ['status'=>1,'msg'=>'Your are successfully logged in'];
				} else {
					return ['status'=>3,'msg'=>'Incorrect username or password'];
				}
		   /* }  else {
				return ['status'=>5,'msg'=>'Your account has been blocked. Please contact our adiminstrator'];
			} */
		} else {
			return ['status'=>6,'msg'=>'Please check your Login Id / Email Id'];
		}
	} else {
		return ['status'=>2,'msg'=>'Please check your Login Id / Email Id'];
	}
	return ['status'=>7,'msg'=>'Please check your Login Id / Email Id'];
}

public function get_affiliate_details (array $arr)
{
	extract($arr);
	$qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
			->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
			->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', '=', 'am.account_id')
			->whereIn('am.account_type_id', [config('constants.ACCOUNT_TYPE.USER'),config('constants.ACCOUNT_TYPE.SELLER')])
			->where('am.is_affiliate', config('constants.ON'))
			->where('am.status', config('constants.ON'))
			->where('am.is_deleted', config('constants.OFF'))
			->where('am.block', config('constants.OFF'));

	if (isset($member) && !empty($member))
	{
		if (strpos($member, '@') > 0)
		{
			$qry->where('am.email', 'like', $member);
			//->where('ast.is_email_verified', config('constants.ON'));
		}
		else if (is_numeric($member))
		{
			$qry->where('am.mobile', $member);
			//->where('ast.is_mobile_verified', config('constants.ON'));
		}
		else
		{
			$qry->where('am.uname', $member);
		}
	}
	elseif (isset($account_id) && !empty($account_id))
	{
		$qry->where('am.account_id', $account_id);
	}
	return $qry->select('am.uname', 'am.email', 'am.account_id', 'ast.currency_id', 'am.mobile', DB::raw('CONCAT_WS(\' \',ad.firstname,ad.lastname) as full_name', 'ast.currency_id', 'ast.country_id'))
					->first();
}
public function find_affiliate ()
{   
	$postdata = $this->request->all(); 
	 //print_r($postdata);exit;
	$this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
	$op = array();
	$bal_arr = array();
	if (!empty($postdata))
	{
		$userdetails = $this->get_affiliate_details($postdata);
		if (!empty($userdetails))
		{				
			$affWallets = $this->commonObj->getSettings('affiliate_wallets');
		
			$balance = $this->financeObj->wallet_balance(['account_id'=>$userdetails->account_id,'currency_id'=>$userdetails->currency_id]);
			foreach ($balance as $bal)
			{
				if (!empty($bal->currency_id))
				{
					$bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
				}
			}
			$op['status'] = 'ok';
			$op['userdetails'] = $userdetails;
			$op['balance'] = $bal_arr;
			$this->status_code = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['status'] = 'err';
			$this->status_code = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('admin/finance.affiliate_not_found');
		}
	}
	return $this->response->json($op, $this->status_code, $this->headers, $this->options);
}
/*
  public function get_user_verification_list ($arr = array(), $count = false)
   {
	extract($arr);
	$users = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')
			->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'uv.account_id')
			->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
			->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'um.account_id')
			->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'uv.document_type_id')
			->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'acp.country_id')
			->select(DB::raw('uv.*,dt.type,dt.proof_type,um.uname,CONCAT(ud.firstname,ud.lastname) as fullname,um.uname,lc.country as country_name'))
			->where('uv.is_deleted', $this->config->get('constants.OFF'))
			->where('um.is_affiliate', $is_affiliate)
			->orderby('uv.created_on', 'DESC');
	  
		if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date))	{ 
				 $users->whereDate('um.signedup_on', '>=', getGTZ($start_date,'Y-m-d'));
				 $users->whereDate('um.signedup_on', '<=', getGTZ($end_date,'Y-m-d'));
			}
			else if (!empty($start_date) && isset($start_date)){ 
				 $users->whereDate('um.signedup_on', '<=', getGTZ($start_date,'Y-m-d'));
			}
			else if (!empty($end_date) && isset($end_date)){ 
				 $users->whereDate('um.signedup_on', '>=', getGTZ($end_date,'Y-m-d'));
			} 
			if (!empty($uname))
			{
			$users->whereRaw("um.uname like '%".$uname."%'");
			}
			if ($status != 'all')
			{
			$users->where('uv.status_id', $status);
			}
		   if (isset($length) && !empty($length))
			{
				$users->skip($start)->take($length);
			}
			if (isset($count) && !empty($count))
			{
				return $users->count();
			}
			else
			{
				$result= $users->orderBy('um.account_id', 'ASC') 
							   ->get();
				return $result;
			}
			return false;
 }
} */

public function documentList ($arr)
{  
	extract($arr);
	$res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as av')
			->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'av.document_type_id')
			->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'av.account_id')
			->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'av.account_id')
			->where('am.is_affiliate', $this->config->get('constants.ON'))
			->where('am.is_deleted', $this->config->get('constants.OFF'))
			->where('av.is_deleted', $this->config->get('constants.OFF'));
			
	if (isset($search_term) && !empty($search_term))
	{
		$res->where(function($sub) use($search_term)
		{
			$sub->Where('am.uname', 'like', '%'.$search_term.'%')
					->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'), 'like', '%'.$search_term.'%');
		});
	}      
	if (isset($arr['account_id']) && !empty($arr['account_id']))
	{
		$res->where('av.account_id', $arr['account_id']);
	}
	if (isset($arr['uname']) && !empty($arr['uname']))
	{
		$res->where('am.uname', $arr['uname']);
	}
	if (isset($arr['status']) && $arr['status'] != '')
	{
		$res->where('av.status_id', $arr['status']);
	}
	if (isset($arr['type_filer']) && !empty($arr['type_filer']))
	{
		$res->where('av.document_type_id', $arr['type_filer']);
	}
	if (!empty($arr['from']))
	{
		$res->whereDate('av.created_on', '>=', date('Y-m-d', strtotime($arr['from'])));
	}
	if (!empty($arr['to']))
	{
		$res->whereDate('av.created_on', '<=', date('Y-m-d', strtotime($arr['to'])));
	}
	if (isset($arr['start']) && isset($arr['length']))
	{
		$res->skip($arr['start'])->take($arr['length']);
	}
	if (isset($arr['orderby']) && !empty($arr['orderby']))
	{
		$res->orderby('av.created_on', $arr['order']);
	}
	else
	{
		$res->orderby('av.created_on', 'DESC');
	}
	if (isset($arr['counts']) && $arr['counts'] == true)
	{
		return $res->count();
	}
	else
	{
		$result = $res->selectRaw('av.*, dt.type, dt.document_type_id, dt.other_fields as doc_other_fields, am.uname, concat(ad.firstname,\' \',ad.lastname) as full_name')->get();
							
		array_walk($result, function(&$v)
		{ 
			$v->actions = [];				
			$v->created_on = !empty($v->created_on) ?  showUTZ($v->created_on, 'd-M-Y H:i:s'): '';
			$v->other_fields = !empty($v->other_fields) ? json_decode($v->other_fields) : [];
			$v->doc_other_fields = !empty($v->doc_other_fields) ? json_decode($v->doc_other_fields, true) : [];
			array_walk($v->other_fields, function(&$field, $k) use($v)
			{
				$field = ['id'=>$k, 'label'=>$v->doc_other_fields[$k]['label'], 'value'=>$field];
			});
			$v->status = trans('admin/general.verification_status.'.$v->status_id);
			$v->status_class =  $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS_CLASS.'.$v->status_id);
			$v->path = asset($this->config->get('constants.ACCOUNT_VERIFICATION_SRC_UPLOADPATH.WEB').$v->path); 
					
			if ($v->status_id == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.PENDING'))
			{	
				$v->actions[] = ['url'=>route('admin.aff.change-document-status'), 'class'=>'change_status', 'data'=>['id'=>$v->uv_id,'status'=>$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.VERIFIED'),'curstatus'=>$v->status_id,'account_id'=>$v->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Verify']; 					
				$v->actions[] = ['url'=>route('admin.aff.change-document-status'), 'class'=>'change_status', 'data'=>['id'=>$v->uv_id,'status'=>$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED'),'curstatus'=>$v->status_id,'account_id'=>$v->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Reject']; 
							 
			}elseif($v->status_id == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.VERIFIED')){
				$v->actions[] = ['url'=>route('admin.aff.change-document-status'), 'class'=>'change_status', 'data'=>['id'=>$v->uv_id,'status'=>$this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED'),'curstatus'=>$v->status_id,'account_id'=>$v->account_id, 'confirm'=>trans('admin/general.confirm_msg')], 'label'=>'Reject']; 
			}
			unset($v->doc_other_fields);
		});	
		return $result;
	}
}

public function changeDocumentStatus ($data = array())
{  	
	$kyc=[];	
	extract($data);
	if (DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
					->where('uv_id', $uv_id)
					->update(array('status_id'=>$status)))
	{
		$res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as av')  
				->join($this->config->get('tables.ACCOUNT_TREE').' as at', 'at.account_id', '=', 'av.account_id')
				->where('av.uv_id', $uv_id)			
				->select('av.account_id','at.kyc_status')
				->first();
				
		$kyc_status = json_decode(stripslashes($res->kyc_status));
		
		/* $kyc_status->verified_doc = (!empty($kyc_status->verified_doc)) ? $kyc_status->verified_doc : 0; */
		
		$total_doc = $kyc_status->total_doc;
		
		if($status == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED'))
		{
			$kyc_status->submitted_doc = --$kyc_status->submitted_doc;
			$kyc['kyc_status'] = addslashes(json_encode($kyc_status));
		} 
		
		else if($status == $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.VERIFIED') && $curstatus = $this->config->get('constants.ACCOUNT_VERIFICATION_STATUS.REJECTED')){
			/* $kyc_status->submitted_doc = ++$kyc_status->submitted_doc; */
			$kyc_status->verified_doc = ++$kyc_status->verified_doc;
			$kyc['kyc_status'] = addslashes(json_encode($kyc_status));		
		} 
				
		$verified = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as av')
					->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'av.document_type_id', '=', 'dt.document_type_id')
					->where('av.account_id', $res->account_id)
					->where('dt.status', $this->config->get('constants.ON'))
					->where('av.status_id', $this->config->get('constants.ON'))
					->where('av.is_deleted', $this->config->get('constants.OFF'))													
					->selectRaw('av.document_type_id')
					->count();					
		
		if ($total_doc  ==  $verified)
		{
		   $kyc['is_kyc_verified'] = $this->config->get('constants.ON');
		   $kyc['kyc_verified_on'] = getGTZ();
		}else {								
		   $kyc['is_kyc_verified'] = $this->config->get('constants.OFF');			  
		   $kyc['kyc_verified_on'] = NULL;
		}
	
		DB::table($this->config->get('tables.ACCOUNT_TREE'))
			->where('account_id', $res->account_id)
			->update($kyc);
		return true;
	}
	return false;
}

public function doc_list ()
{
	return DB::table($this->config->get('tables.DOCUMENT_TYPES'))
					->where('status', $this->config->get('constants.ON'))
					->select('document_type_id', 'type', 'other_fields')
					->get();
}

/* public function delete_doc ($data)
{
	return DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
					->where('uv_id', $data['uv_id'])
					->update(array(
						'is_deleted'=>$this->config->get('constants.ON')));
} */

public function get_aff_ranks($arr){
	extract($arr);
	$res = DB::table(config('tables.ACCOUNT_AF_RANKING_LOG').' as rl')
			   ->join(config('tables.ACCOUNT_MST').' as am','am.account_id','=','rl.account_id')
			   ->join(config('tables.ACCOUNT_DETAILS').' as ad','ad.account_id','=','am.account_id')
			   ->join(config('tables.ACCOUNT_PREFERENCE').' as ap','ap.account_id','=','am.account_id')
			   ->join(config('tables.LOCATION_COUNTRY').' as c','c.country_id','=','ap.country_id')
			   ->join(config('tables.AF_RANKING_LOOKUP').' as lk','lk.af_rank_id','=','rl.af_rank_id')
			   ->where('rl.status',1)
			   ->where('rl.is_verified',1);
	if(!empty($country_id))
	{
		$res->where('c.country_id',$country_id);
	}
	if(!empty($term))
	{
		$res->where('am.uname','like','%'.$term.'%');
	}
	if(isset($country_list) && $country_list == true)
	{
	   $result = $res->distinct('c.country_id')->select('c.country','c.country_id')->get();
	   return $result;
	}
	if(isset($counts) && $counts == true)
	{
		return $res->count();
	}
	else
	{
		$res->select('am.account_id','rl.created_on',DB::raw("CONCAT_WS(' ',ad.firstname,ad.lastname) as fullname"),'am.email','am.user_code','am.uname','lk.rank','lk.af_rank_id','rl.gen_1','rl.gen_2','rl.gen_3','c.country');
		$result = $res->get();
		array_walk($result,function($k){
			$k->created_on = date('d-m-Y',strtotime($k->created_on));
		});
		return $result;
	}
}
public function user_details($arr = array(), $count = false){
	extract($arr);
			$query = DB::table(config('tables.ACCOUNT_MST').' as um')
			  ->select(DB::raw('um.uname,um.account_id,um.user_code,um.email,um.mobile,um.last_active,um.signedup_on'))
			  ->where('um.is_deleted','=',$this->config->get('constants.OFF'))
			  ->where('um.is_affiliate','=',$this->config->get('constants.ON'))
			  ->where('um.account_type_id','!=',1);
			  
	 if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date))	{ 
		 $query->whereDate('um.signedup_on', '>=', getGTZ($start_date,'Y-m-d'));
		 $query->whereDate('um.signedup_on', '<=', getGTZ($end_date,'Y-m-d'));
	   }
	   else if (!empty($start_date) && isset($start_date)){ 
		 $query->whereDate('um.signedup_on', '<=', getGTZ($start_date,'Y-m-d'));
	  }
	   else if (!empty($end_date) && isset($end_date)){ 
		 $query->whereDate('um.signedup_on', '>=', getGTZ($end_date,'Y-m-d'));
	   } 
	if (isset($uname) && $uname)
		  {
		 $query->where('um.uname','like','%'.$uname.'%'); 
		  }
	 if (isset($length) && !empty($length))
		{
			$query->skip($start)->take($length);
		}
		if (isset($count) && !empty($count))
		{
			return $query->count();
		}
 else{
	   $result=$query->orderBy('um.account_id', 'ASC')
			   ->get();
	if(!empty($result)) {
		array_walk($result, function(&$data)
		 {
		if(!empty($data->signedup_on)){
			 $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y H:i:s');
		}else{
			$data->signedup_on='';
		}
		 $data->last_active = showUTZ($data->last_active, 'd-M-Y H:i:s');
		/* $data->actions = [];
		   $data->actions[] = ['url'=>route('admin.aff.activate_user', ['account_id'=>$data->account_id]),'class'=> 'activate_user', 'data'=>[
		   'account_id'=>$data->account_id,
		  ],'label'=>'Activate']; */
		});
	   return $result;
	}
}
}

public function get_user_details($arr = array()){
	extract($arr);
	if(!empty($account_id)){
		$query = DB::table(config('tables.ACCOUNT_MST').' as um')
					->where('um.account_id','=',$account_id)
					->select('um.reg_data')
					->first();
		return json_decode(stripslashes($query->reg_data));
	}
}

public function getSponsorInfo($params){
	$account_id = 0;
	if(!empty($params)){
		extract($params);
		if($account_id>0){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
				->join($this->config->get('tables.ACCOUNT_TREE') . ' as spt','spt.account_id','=','act.sponsor_id')
				->join($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','act.sponsor_id')
				->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as spp','spp.account_id','=','spam.account_id')
				->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','act.sponsor_id');
			$qry->where('act.account_id','=',$account_id);				
			$qry->select(DB::Raw("spt.can_sponsor,spp.country_id,spt.account_id,spam.account_type_id as account_type_id,spam.uname as uname,spam.status,spam.block,spam.status,spam.email,concat_ws(' ',acd.firstname,acd.lastname) as full_name,IF(spt.upline_id=0 AND spt.sponsor_id=0 AND spt.lft_node=1 ,1,0) as is_root_account,spt.referral_cnts,spt.referral_paid_cnts"));
			
			$res = $qry->first();					
			if ($res) {			
				return $res;				
			}
		}
	}
	return false;
}	
   public function qualified_volume_details($arr = array(), $count = false){
	  extract($arr); 
	  
		$query=  DB::table(config('tables.ACCOUNT_TREE').' as act')
				 ->join(config('tables.ACCOUNT_MST').' as am', 'am.account_id', ' = ', 'act.account_id')
				 ->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'am.account_id')
				 ->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'am.account_id')
				 ->join(config('tables.LOCATION_COUNTRY').' as lcu', 'lcu.country_id', ' = ', 'acp.country_id')
				 ->join(config('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'acp.currency_id')	
				 ->select(DB::Raw('act.created_on,am.account_id,am.user_code,am.uname,am.email,am.status,concat_ws(" ",ud.firstname,ud.lastname) as fullname,( SELECT confirm_date FROM `account_subscription` where account_id=act.account_id group by account_id order by confirm_date desc) as confirm_date,am.mobile,lcu.country as country_name,act.qv,act.activated_on,cur.currency_symbol,cur.currency'))
				 ->where('am.is_deleted','=',$this->config->get('constants.OFF'))
				 ->where('act.qv','>',0);

			  if (isset($search_term) && !empty($search_term)){
				if (is_numeric($search_term)){
					$query->where('am.user_code', $search_term);
				}
			 else{
				$query->where(DB::Raw('concat_ws(" ",ud.firstname,ud.lastname)'), 'like', '%'.$search_term.'%');
				 }
			  }
			 if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date)){ 
					 $query->whereDate('act.created_on', '>=', getGTZ($start_date,'Y-m-d'));
					 $query->whereDate('act.created_on', '<=', getGTZ($end_date,'Y-m-d'));
				}
			 else if (!empty($start_date) && isset($start_date)){ 
					$query->whereDate('act.created_on', '<=', getGTZ($start_date,'Y-m-d'));
			 }
			else if (!empty($end_date) && isset($end_date)){ 
				  $query->whereDate('act.created_on', '>=', getGTZ($end_date,'Y-m-d'));
			 } 
			if (isset($orderby) && isset($order)){
				$query->orderBy($orderby, $order);
			}
			if (isset($length) && !empty($length)){
				$query->skip($start)->take($length);
			}
			if (isset($count) && !empty($count)){
				return $query->count();
			}				
		else
		{
			$result= $query->orderBy('am.account_id', 'ASC') 
			   ->get();
		  if(!empty($result)) {
				array_walk($result, function(&$data)
				{
			  if($data->created_on ==null){
					$data->created_on='';
				}else{
					$data->created_on= date('d-M-Y', strtotime($data->created_on));
					}
			   if($data->confirm_date ==null){
					$data->confirm_date='';
				}else{
					$data->confirm_date= date('d-M-Y', strtotime($data->confirm_date));
					}
					$data->qv =number_format($data->qv, \AppService::decimal_places($data->qv), '.', ',');
		   });
			return $result;
		  }
	   }
	 return false;
} 
}