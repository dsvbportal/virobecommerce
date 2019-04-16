<?php
namespace App\Models\Franchisee;

use DB;
use File;
use App\Helpers\CommonNotifSettings;
use App\Models\BaseModel;
use App\Models\LocationModel;
use CommonLib;
class ManageMerchant extends BaseModel {
	
    public function __construct() {
        parent::__construct();				
		$this->locObj = New LocationModel;
    }
	
	public function get_merchant_list(array $data = array(), $count = false){
		$mr_status 	= null;
        extract($data);
        $retailer = DB::table(config('tables.FRANCHISEE_SUPPLIER_SIGNUP').' as fss')                
		              ->join(config('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'fss.supplier_id')
                      ->join(config('tables.ACCOUNT_MST').' as accmst', 'accmst.account_id', '=', 'mm.account_id')
                      ->join(config('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'accmst.account_id')
				      ->join(config('tables.ADDRESS_MST').' as adm', function($subquery)
						{
							$subquery->on('adm.relative_post_id', '=', 'mm.supplier_id')
										->where('adm.post_type', '=', config('constants.ACCOUNT_TYPE.SELLER'))
										->where('adm.address_type_id', '=', config('constants.PRIMARY_ADDRESS'));
						})
		    		->leftjoin(config('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'adm.country_id')
					->where("fss.franchisee_id",'=',$this->userSess->franchisee_id)
					->where('mm.is_deleted', config('constants.OFF'))
					->where('mm.is_online', config('constants.OFF'));					
					
			if (isset($from) && !empty($from) && isset($to) && !empty($to))
                {
				$retailer->whereDate('mm.created_on', '>=', getGTZ($from, 'Y-m-d'));
				$retailer->whereDate('mm.created_on', '<=', getGTZ($to, 'Y-m-d'));
                }  
				else if (!empty($from) && isset($from))
				{
					$retailer->whereDate('mm.created_on', '<=', getGTZ($from, 'Y-m-d'));
				}
				else if (!empty($to) && isset($to))
				{
					$retailer->whereDate('mm.created_on', '>=', getGTZ($to, 'Y-m-d'));
				}
				if(isset($search_term) && !empty($search_term))
				{
					$retailer->where(function($wcond) use($search_term)
					{
						$wcond->Where('mm.supplier_code', 'like', $search_term)
							     ->orwhere(DB::Raw('concat_ws(" ",accd.firstname,accd.lastname)'),'like',$search_term);
								/* ->orwhere('accmst.uname', 'like', $search_term)
								->orwhere('accmst.mobile', 'like', $search_term)
								->orwhere('accmst.email', 'like', $search_term); */
					});
				}
        
				if (isset($count) && !empty($count))
				{
					return $retailer->count();
				}
         else
          {
            if (isset($start) && isset($length))
            {
                $retailer->skip($start)->take($length);
            }
            if (isset($orderby) && isset($order))
            {
                if ($orderby == 'created_on')
                {
                    $retailer->orderBy('mm.created_on', $order);
                }
                elseif ($orderby == 'mrcode')
                {
                    $retailer->orderBy('mm.mrcode', $order);
                }
                elseif ($orderby == 'mrbusiness_name')
                {
                    $retailer->orderBy('mm.mrbusiness_name', $order);
                }
                elseif ($orderby == 'country')
                {
                    $retailer->orderBy('loc.country', $order);
                }
                elseif ($orderby == 'activated_on')
                {
                    //$retailer->orderBy('ms.activated_on', $order);
                }
                elseif ($orderby == 'status')
                {
                    $retailer->orderBy('mm.status', $order);
                }
            }
            else
            {
                $retailer->orderBy('mm.created_on', 'DESC');
            }
            
       $retailers = $retailer->selectRaw('mm.account_id,mm.supplier_id as mrid, mm.supplier_code as mrcode, mm.account_id, mm.company_name as mrbusiness_name, mm.status, loc.country,loc.phonecode,mm.activated_on,mm.created_on,mm.is_verified, mm.created_on, mm.is_deleted, mm.block,mm.supplier_code,accmst.uname,accmst.mobile,accmst.account_type_id,accd.firstname,accd.lastname,concat_ws(\' \',accd.firstname,accd.lastname) as full_name,accmst.email,adm.address,adm.country_id,adm.state_id,adm.district_id,adm.city_id')
	   ->get();	  
            array_walk($retailers, function(&$retailer)
            {			 
			 $stateInfo=$this->locObj->getStateInfo($retailer->state_id);
		     $country=$this->getFranchiseeLocation($this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'),$retailer->country_id);
			 $state =$this->getFranchiseeLocation($this->config->get('constants.FRANCHISEE_TYPE.STATE'),$retailer->state_id);
			 $district =$this->getFranchiseeLocation($this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'),$retailer->district_id);
			 $city =$this->getFranchiseeLocation($this->config->get('constants.FRANCHISEE_TYPE.CITY'),$retailer->city_id);
			 if(!empty($stateInfo)){
			 $region=$this->getFranchiseeLocation($this->config->get('constants.FRANCHISEE_TYPE.REGION'),$stateInfo->region_id);
			 }
			 else {
				 $region = '';
			 }
			   $retailer->activated_on = !empty($retailer->activated_on) ? showUTZ($retailer->activated_on, 'd-M-Y H:i:s') : '--';
			   $retailer->created_on = showUTZ($retailer->created_on, 'd-M-Y H:i:s');
			   $retailer->country_fr=!empty($country) ? $country: '';
			   $retailer->state_fr=!empty($state) ? $state: '';
			   $retailer->district_fr=!empty($district) ? $district: '';
			   $retailer->city_fr=!empty($city) ? $city : '';
			   $retailer->region_fr=!empty($region) ? $region: '';
			   $retailer->status_class   = $this->config->get('dispclass.seller.'.$retailer->status.'');
			   if($retailer->status==$this->config->get('constants.ACTIVE')){
				   $retailer->status='Active';
			   }
			   else if($retailer->status==$this->config->get('constants.INACTIVE')){
				   $retailer->status='Inactive';
			   }
			    $retailer->actions = [];
			   $retailer->actions[] = ['url'=>route('fr.merchants.manage_kyc', ['uname'=>$retailer->supplier_code]),'class'=>'manage_merchant',
			       'data'=>[
							'supplier_id'=>$retailer->mrid,
							'account_type_id' => $retailer->account_type_id,
						],  'redirect'=>true, 'label'=>'Upload KYC'];
            });
            return $retailers;
        }
	}
	public function getFranchiseeLocation($access_location_type,$location_id){
	   $location_details = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')  
	                        ->join(config('tables.ACCOUNT_MST').' as accmst', 'accmst.account_id', '=', 'fal.account_id')
                            ->join(config('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'accmst.account_id') 
						    ->join(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'accmst.account_id')
							->join(config('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'ap.country_id')
							->where('fal.access_location_type',$access_location_type)
							->where('fal.relation_id',$location_id)
							->where('fal.status', $this->config->get('constants.ON'))
							/*->selectRaw("fal.access_location_type,fal.relation_id,accd.firstname,accd.lastname,concat_ws(' ',accd.firstname,accd.lastname) as full_name,accmst.mobile,loc.phonecode,
							CASE fal.access_location_type
							WHEN 2 THEN (select region from  ".$this->config->get('tables.LOCATION_REGIONS')." where region_id =  fal.relation_id) END")*/
							->select("fal.id","fal.access_location_type","fal.relation_id",DB::Raw("concat_ws(' ',accd.firstname,accd.lastname) as full_name"),
							"accmst.mobile","loc.phonecode",
							DB::Raw("(CASE fal.access_location_type
							WHEN 2 THEN (select region from ".$this->config->get('tables.LOCATION_REGIONS')." where region_id = fal.relation_id) 
							WHEN 3 THEN (select state from ".$this->config->get('tables.LOCATION_STATE')." where state_id = fal.relation_id) 
							WHEN 4 THEN (select district from ".$this->config->get('tables.LOCATION_DISTRICTS')." where district_id = fal.relation_id) 
							WHEN 5 THEN (select city from ".$this->config->get('tables.LOCATION_CITY')." where city_id = fal.relation_id) END) as access_location"))
							->first();
							
		    if (!empty($location_details) && count($location_details) > 0){
					return $location_details;
				}
				return null;
	   }
	  
	public function country_list ()
    {
        $result = DB::table(config('tables.SUPPLIER_MST').' as mm')
                ->join(config('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'mm.supplier_id')
							->where('adm.post_type', '=', config('constants.ACCOUNT_TYPE.SELLER'))
							->where('adm.address_type_id', '=', config('constants.PRIMARY_ADDRESS'));
                })
                ->join(config('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'adm.country_id')
                ->select('adm.country_id', 'loc.country')      
				->distinct()
                ->orderBy('loc.country', 'asc')
                ->get();
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        return null;
    }
	
	public function merchant_filter_bcategory ()
    {
        $bcategory = DB::table(config('tables.SUPPLIER_MST').' as mm')
					->join(config('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'mm.category_id')
					->join(config('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($subquery)
					{
						$subquery->on('bcl.bcategory_id', '=', 'bc.bcategory_id')
						->where('bcl.lang_id', '=', config('app.locale_id'));
					})
					->where('mm.is_deleted', config('constants.NOT_DELETED'))
					->select('bc.bcategory_id', 'bcl.bcategory_name', 'bc.slug')
					->distinct()
					->orderBy('bcl.bcategory_name', 'asc');
        $result = $bcategory->get();
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        return null;
    }
	
	public function merchantCategories(array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(config('tables.BUSINESS_CATEGORY').' as cat')
                ->join(config('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(config('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', config('constants.ACTIVE'))
                ->where('cat.is_visible', config('constants.ACTIVE'))
                ->where('cat.category_type', config('constants.ACTIVE'))
                ->where('cat.status', config('constants.ACTIVE'))
                ->where('cat.is_deleted', 0);
        $qry->selectRaw('catL.bcategory_name as name,cat.bcategory_id as id,catT.parent_bcategory_id as parent_id');
        $qry->orderBy('catL.bcategory_name', 'ASC');
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
	
	public function saveMerchants_old($arr){
	    
       extract($arr);
	     $res = false;
		  $password=rand(100000, 999999);
            $amData = [
                'account_type_id' => config('constants.ACCOUNT_TYPE.SELLER'),
                'email' => $email,
                'mobile' => $mobile,
                'signedup_on' => getGTZ(),
				'pass_key'=>md5($password),
                'status' => config('constants.ON'),
                'activated_on' => getGTZ()];
           DB::beginTransaction();
		   
            $account_id = $res = DB::table(config('tables.ACCOUNT_MST'))->insertGetID($amData);
            if (isset($account_id) && !empty($account_id))
            {
				$amst['user_code'] = $supplier_code = (rand(2, 9).rand(2, 9).rand(2, 9)).str_pad($country, 3, "0", STR_PAD_LEFT).str_pad($account_id, 4, "0", STR_PAD_LEFT);
                $amst['uname'] = 'SEL'.$country.date('ymdHi');
                $amst['activation_key'] = md5($amst['user_code']);
                DB::table(config('tables.ACCOUNT_MST'))->where('account_id', '=', $account_id)->update($amst);
			
                $account_details['account_id'] = $account_id;               
                $account_details['firstname'] = $firstname;
                $account_details['lastname'] = $lastname;               
                $res = DB::table(config('tables.ACCOUNT_DETAILS'))->insertGetId($account_details);

                $accountADD = [];
                $accountADD['post_type'] = config('constants.ADDRESS_POST_TYPE.ACCOUNT');
                $accountADD['relative_post_id'] = $account_id;
                $accountADD['address_type_id'] = config('constants.ADDRESS_TYPE.PRIMARY');   /* Permananent */
                $accountADD['country_id'] = $country;
                $address_id = DB::table(config('tables.ADDRESS_MST'))->insertGetId($accountADD);

                $account_PRE = [];
                $account_PRE['account_id'] = $account_id;
                $account_PRE['country_id'] = $country;
                $account_PRE['language_id'] = 1;
				$account_PRE['email_verification_key'] = $email_verification_key = md5($supplier_code.$account_id);
                $account_PRE['currency_id'] = $currency_id = DB::table(config('tables.LOCATION_COUNTRY'))->where('country_id', $country)->value('currency_id');
                $user_preference_id = DB::table(config('tables.ACCOUNT_PREFERENCE'))->insertGetId($account_PRE);

				
				/* $supplier_code = (rand(2, 9).rand(2, 9).rand(2, 9)).str_pad($country, 3, "0", STR_PAD_LEFT).str_pad($account_id, 4, "0", STR_PAD_LEFT); */
                $account_supplier['category_id'] = $bcategory;
                $account_supplier['account_id'] = $account_id;
                $account_supplier['created_on'] = getGTZ();
                $account_supplier['supplier_code'] = $supplier_code;
                $account_supplier['company_name'] = $buss_name;
				$account_supplier['service_type'] = $service_type;
                $account_supplier['status'] = config('constants.ON');
                $account_supplier['completed_steps'] = '1'; 
                /* $account_supplier['verified_steps'] = '3';  */
                $supplier_id = $res = DB::table(config('tables.SUPPLIER_MST'))->insertGetId($account_supplier);
				
                if (!empty($bcategory))
                {
                    $sca = [];
                    $sca['supplier_id'] = $supplier_id;
                    $sca['category_id'] = $bcategory;
                    $sca['status'] = config('constants.ON');
                    DB::table(config('tables.SUPPLIER_CATEGORY_ASSOCIATE'))->insertGetId($sca);
                }
				$supplier_preference = [];
                $supplier_preference['supplier_id'] = $supplier_id;
                $supplier_preference['phy_locations'] = $phy_locations;
                DB::table(config('tables.SUPPLIER_PREFERENCE'))->insertGetId($supplier_preference);
				
                $supp_cashback_setting['supplier_id'] = $supplier_id;
                $supp_cashback_setting['is_redeem_otp_required'] = config('constants.ON');
                $supp_cashback_setting['pay'] = config('constants.ON');
                $supp_cashback_setting['member_redeem_wallets'] = config('constants.WALLET_IDS');
                DB::table(config('tables.CASHBACK_SETTINGS'))->insertGetId($supp_cashback_setting);

                $sellerADD = [];
                $sellerADD['post_type'] =config('constants.ADDRESS_POST_TYPE.SELLER');
                $sellerADD['relative_post_id'] = $supplier_id;
                $sellerADD['address_type_id'] = config('constants.ADDRESS.PRIMARY');       /* Permananent */
                $sellerADD['country_id'] = $country;
                DB::table(config('tables.ADDRESS_MST'))->insertGetId($sellerADD);

                $store['store_name'] = $buss_name;
                $store['supplier_id'] = $supplier_id;
                $store['category_id'] = $bcategory;
                $store['is_primary'] = 1;
                $store['created_on'] = getGTZ();
                $store['updated_by'] = $account_id;
                $store['currency_id'] = $currency_id;
                $store['status'] = config('constants.OFF');
                $store['is_approved'] = config('constants.OFF');
                $store_id = DB::table(config('tables.STORES'))->insertGetId($store);

                $storeADD = [];
                $storeADD['post_type'] = config('constants.ADDRESS_POST_TYPE.STORE');
                $storeADD['relative_post_id'] = $store_id;
                $storeADD['address_type_id'] = $this->config->get('constants.ADDRESS_TYPE.PRIMARY');       /* Permananent */
                $storeADD['country_id'] = $country;
                $store_address_id = DB::table(config('tables.ADDRESS_MST'))->insertGetId($storeADD);

                $storecode = (rand(2, 9).rand(2, 9).rand(2, 9)).str_pad($country, 3, "0", STR_PAD_LEFT).str_pad($store_id, 4, "0", STR_PAD_LEFT);
				
                DB::table(config('tables.STORES'))->where('store_id', '=', $store_id)->update(['store_code' => $storecode, 'store_slug' => $storecode, 'address_id' => $store_address_id]);

                $ss = [];
                $ss['store_id'] = $store_id;
                $ss['specify_working_hours'] = 1;
                DB::table(config('tables.STORE_SETTINGS'))->insertGetId($ss);

                $store_extras['store_id'] = $store_id;
                $store_extras['country_id'] = $country;
                $store_extras['email'] = $email;
                $store_extras['mobile_no'] = $mobile;
                DB::table(config('tables.STORES_EXTRAS'))->insert($store_extras);
			
                $employee['account_id'] = $account_id;
                $employee['supplier_id'] = $supplier_id;
                $employee['store_id'] = $store_id;
                $employee['sed_id'] = DB::table(config('tables.STORE_EMPLOYEES_DESIGNATIONS'))
									->whereNull('supplier_id')
									->where('is_deleted', config('constants.OFF'))
									->where('status', config('constants.ON'))
									->value('sed_id');
				$employee['access_level'] = DB::table(config('tables.ACCESS_LEVEL_LOOKUP'))
									->where('account_type_id', config('constants.ACCOUNT_TYPE.SELLER'))
									->where('is_deleted', config('constants.OFF'))
									->where('is_default', config('constants.ON'))
									->value('access_id');
							 
			    $employee['status'] = config('constants.ON');
                $employee['updated_by'] = $account_id;
                DB::table(config('tables.STORE_EMPLOYEES'))->insert($employee);

                $fr_details['franchisee_id'] =$franchisee_id;
                $fr_details['supplier_id'] = $supplier_id;                
                $fr_details['store_id'] = $store_id;
                $fr_details['created_by'] =$this->userSess->account_id;
                $fr_details['created_on'] =getGTZ();    
                DB::table(config('tables.FRANCHISEE_SUPPLIER_SIGNUP'))->insert($fr_details);	
				$this->verifyStore($store_id);
            }
            
		if ($res)
            {
                ($res) ? DB::commit() : DB::rollback();
				$token = $this->Generate_Reg_Token($email_verification_key);
				/* $verify_link = route('seller.verify-email-link', ['token' => $token]); */	
				$verify_link = url($token);
				CommonNotifSettings::notify('franchisee.merchant.account_created', $account_id,config('constants.ACCOUNT_TYPE.SELLER'), ['email_verify_link' => $verify_link, 'email' => $email, 'firstname' => $firstname, 'full_name' => $firstname,'password'=>$password], true,false,false,true,true);
                  return array('status'=>config('httperr.SUCCESS'),
						'msg'=>'Merchant Created Successfully');
            }
        return false;
	}
	
	  public function verifyStore($store_id, $verify_step_id = null, $add = false, $storeObj = null)
    {
        $completed_steps = [];
        $verified_steps = [];
        $is_approved = false;
        if ($store = DB::table(config('tables.STORES'))
                ->where('store_id', $store_id)
                ->selectRaw('supplier_id,service_type,verified_steps')
                ->first())
        {
            $verified_steps = !empty($store->verified_steps) ? explode(',', $store->verified_steps) : [];
            $completed_steps[] = 7;
            if ($step = DB::table(config('tables.ADDRESS_MST'))
                    ->where('relative_post_id', $store_id)
                    ->where('post_type', config('constants.ADDRESS_POST_TYPE.STORE'))
                    ->selectRaw('address_id')
                    ->exists())
            {
                $completed_steps[] = 8;
            }
            if ($step = DB::table(config('tables.STORE_SETTINGS'))
                    ->where('store_id', $store_id)
                    ->exists())
            {
                $completed_steps[] = 9;
            }
            if ($store->service_type == 1 || $store->service_type == 3)
            {
                if ($step = DB::table(config('tables.STORE_EMPLOYEES'))
                        ->where('supplier_id', $store->supplier_id)
                        ->where('store_id', $store_id)
                        ->where('status', config('constants.ON'))
                        ->where('is_deleted', config('constants.OFF'))
                        ->exists())
                {
                    $completed_steps[] = 10;
                }
            }
        }
        $completed_steps = array_filter(array_unique($completed_steps));
        if ($verify_step_id != null && in_array($verify_step_id, $completed_steps))
        {
            if ($add)
            {
                $verified_steps[] = $verify_step_id;
            }
            else
            {
                unset($verified_steps[array_search($verify_step_id, $verified_steps)]);
            }
            $verified_steps = array_filter(array_unique($verified_steps));
            $is_approved = !DB::table(config('tables.ACCOUNT_CREATION_STEPS').' as aas')
                            ->where('aas.status', config('constants.ON'))
                            ->where('aas.account_type_id', config('constants.ACCOUNT_TYPE.SELLER'))
                            ->where('aas.post_type_id', config('constants.POST_TYPE.STORE'))
                            ->whereRaw('FIND_IN_SET('.$store->service_type.',aas.service_types)')
                            ->whereNotIn('aas.step_id', $verified_steps)
                            ->exists();
        }
        sort($verified_steps);
        sort($completed_steps);
        $completed_steps = !empty($completed_steps) ? implode(',', $completed_steps) : null;
        $verified_steps = !empty($verified_steps) ? implode(',', $verified_steps) : null;
        if (DB::table(config('tables.STORES'))
                        ->where('store_id', $store_id)
                        ->update(['completed_steps' => $completed_steps, 'verified_steps' => $verified_steps, 'is_approved' => $is_approved]))
        {
            if ($is_approved)
            {
                $details = $storeObj->getStoreDetails(['store_id' => $store_id]);
                CommonNotifSettings::notify('SELLER.STORE_VERIFIED', $details->account_id, $this->config->get('constants.ACCOUNT_TYPE.SELLER'), (array) $details);
            }
            return true;
        }
        return false;
    }
	
	public function get_tax_information($relative_post_id, $post_type)
      {
        $check_details = DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                ->where('relative_post_id', $relative_post_id)
                ->where('post_type', $post_type)
                ->first();
        if (empty($check_details))
        {
            DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                    ->insert(['post_type' => $post_type, 'relative_post_id' => $relative_post_id]);
            $check_details = DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                    ->where('relative_post_id', $relative_post_id)
                    ->where('post_type', $post_type)
                    ->first();
        }
        if (!empty($check_details))
        {
		$check_details->tax_document_path = !empty($check_details->tax_document_path) ? asset(config('path.SELLER.PROOF_DETAILS.LOCAL').$check_details->tax_document_path) : null;
		$check_details->tan_path = !empty($check_details->tan_path) ? asset(config('path.SELLER.PROOF_DETAILS.LOCAL').$check_details->tan_path) : null;
		$check_details->pan_card_image = !empty($check_details->pan_card_image) ? asset(config('path.SELLER.PROOF_DETAILS.LOCAL').$check_details->pan_card_image) : null;
		$check_details->id_proof_path = !empty($check_details->id_proof_path) ? asset(config('path.SELLER.PROOF_DETAILS.LOCAL').$check_details->id_proof_path) : null;
        $check_details->address_proof_path = !empty($check_details->address_proof_path) ? asset(config('path.SELLER.PROOF_DETAILS.LOCAL').$check_details->address_proof_path) : null;
        }
        return $check_details;
    }
	
	
	  public function UpdateTax_info(array $arr = array())
      {
        extract($arr);
        $insert_details['relative_post_id'] = $relative_post_id;
        $insert_details['post_type'] = $account_type_id;
        $insert_details['pan_card_no'] = $pan_number;
        $insert_details['pan_card_name'] = $pan_name;
        if (isset($pan_card_image) && !empty($pan_card_image))
        {
            $insert_details['pan_card_image'] = $pan_card_image;
        }
        $insert_details['created_on'] = getGTZ();
		
        $check_tax_details = DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                 ->where('relative_post_id', $relative_post_id)
                ->where('post_type', $account_type_id) 
                ->first();
        if (count($check_tax_details) > 0)
        {
            DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                    ->where('relative_post_id', $relative_post_id)
                    ->where('post_type', $account_type_id)
                    ->update($insert_details);
        }
        else
        {
            DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))->insertGetID($insert_details);
        }
        return true;
    } 
  
   public function UpdateGstInfo(array $arr = array())
    {
        extract($arr);
        $insert_details['relative_post_id'] = $relative_post_id;
        $insert_details['post_type'] = $account_type_id;
        if (isset($tax_document_path))
        {
            $insert_details['gstin_no'] = $gstin_no;
            $insert_details['is_registered'] = 1;
            $insert_details['tax_class_id'] = config('constants.TAX_TYPES.CGST');
            $insert_details['tax_document_path'] = $tax_document_path;
        }
        if (isset($tan_image))
        {
            $insert_details['tan_no'] = $tan_no;
            $insert_details['tan_path'] = $tan_image;
        }
        if (!empty($id_proof_path))
        {
            $insert_details['id_proof_no'] = $proof_no;
            $insert_details['id_proof_document_type_id'] = $id_proof_type;
            $insert_details['id_proof_path'] = $id_proof_path;
        }
        if (!empty($address_proof_path))
        {
            $insert_details['address_proof_no'] = $address_proof_no;
            $insert_details['address_proof_document_type_id'] = $address_proof_type;
            $insert_details['address_proof_path'] = $address_proof_path;
        }
        if (DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
                        ->where('post_type', $account_type_id)
                        ->exists())
        {
            return DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                            ->where('relative_post_id', $relative_post_id)
                            ->where('post_type', $account_type_id)
                            ->update($insert_details);
        }
        else
        {
            return DB::table(config('tables.ACCOUNT_TAX_DOCUMENTS'))
                            ->insertGetID($insert_details);
        }
        return false;
    }
	
  public function UpdateCompletedSteps(array $arr = array())
    {
        extract($arr);
        if ($account_type_id == config('constants.ACCOUNT_TYPE.SELLER'))
        {
            $completed_steps = DB::table(config('tables.SUPPLIER_MST'))->where('supplier_id', $supplier_id)->value('completed_steps');
        }
        $completed_steps = !empty($completed_steps) ? explode(',', $completed_steps) : [];
        if (!in_array($current_step, $completed_steps))
        {
            $completed_steps[] = $current_step;
        }
        $completed_steps = array_unique($completed_steps);
        $next = DB::table(config('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                //->havingRaw('min(priority)')
                ->orderby('priority')
                ->selectRaw('step_id, route')
                ->first();

        $nextstep = !empty($next->step_id) ? $next->step_id : 0;
        if ($account_type_id == config('constants.ACCOUNT_TYPE.SELLER'))
        {
            $result = DB::table(config('tables.SUPPLIER_MST'))->where('supplier_id', $supplier_id)
                    ->update(['completed_steps' => implode(',', $completed_steps), 'updated_by' => $account_id, 'next_step' => $nextstep]);
           /*  return (isset($next->route) && !empty($next->route)) ? \URL::route($next->route) : \URL::to('merchant/dashboard'); */
        }
        return false;
    }
	
  public function save_temp_supplier_data($arr)
    {
        unset($arr['g-recaptcha-response']);
        $temp['regdata'] = json_encode($arr);
        $temp['create_on'] = getGTZ();
        $temp['regtoken'] = md5(rand(111111, 999999));
        return DB::table(config('tables.ACCOUNT_TEMP'))->insertGetID($temp);
    }
    public function get_temp_supplier_token($tempid)
    {
        return DB::table(config('tables.ACCOUNT_TEMP'))->where('id', '=', $tempid)->value('regtoken');
    }
	 public function get_temp_supplier_data_by_token($token)
    {
        return DB::table(config('tables.ACCOUNT_TEMP'))->where('regtoken', '=', $token)->value('regdata');
    }
	public function get_temp_supplier_data($tempid)
    {
        return DB::table(config('tables.ACCOUNT_TEMP'))->where('id', '=', $tempid)->first();
    }
	
	 public function Generate_Reg_Token($token)
       {
        if ($token)
        {
            $char_length = rand(1, 9);
            $position = $char_length % 2;
            /* if ($postion == 1) placed at the end else placed at the begining */
            $length = strlen($token);
            if ($position == 1)
            {
                /* IF odd number placed at the end */
                $part1 = substr($token, 0, $char_length);
                $part2 = substr($token, $char_length);
                $token = $char_length.$part2.$part1;
            }
            if ($position == 0)
            {
                /* IF even number placed at the beginning */
                $part1 = substr($token, -$char_length);
                $part2 = substr($token, 0, -$char_length);
                $token = $char_length.$part1.$part2;
            }
             return url($this->config->get('constants.SELLER_ROUTE').$token); 
        }
    }
	
	
}