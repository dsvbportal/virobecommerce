<?php
namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageLib;
use Config;
use URL;
use App\Helpers\ShoppingPortal;

class Suppliers extends Model
{

    public function __construct (&$commonObj)
    {
        $this->commonObj = $commonObj;
    }
	
	public function get_suppliers_list ($arr = array(), $count = false)
    {	
        $res = DB::table(Config::get('tables.SUPPLIER_MST').' as asu')
                ->leftjoin(Config::get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'asu.account_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'asu.account_id')
                ->join(Config::get('tables.ACCOUNT_STATUS_LOOKUPS').' as sta', 'sta.status_id', '=', 'amst.status')
                //->leftjoin(Config::get('tables.ADMIN_MST').' as adm', 'adm.admin_id', '=', 'asu.updated_by')
                ->join(config::get('tables.ADDRESS_MST').' as ad', function($ad)
                {
                    $ad->on('ad.relative_post_id', '=', 'asu.supplier_id')
                    ->where('ad.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                    ->where('ad.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
                })
				->join(config::get('tables.LOCATION_COUNTRY').' as con', 'con.country_id', '=', 'ad.country_id')
                ->join(Config::get('tables.STORES').' as str', function($subquery)
				{
					$subquery->on('str.supplier_id', '=', 'asu.supplier_id')
					->where('str.primary_store', '=', Config::get('constants.ON'));
				}); 
		
        if (isset($arr['is_closed']))
        {
            if ($arr['is_closed'] != Config::get('constants.ON'))
            {
                $res->where('amst.status', $arr['status']);                
            }
            else
            {
                $res->where('asu.is_closed', $arr['is_closed']);
            }
        }
		
        if (isset($arr['status_name']) && !empty($arr['status_name']) && $arr['status_name'] == 'approvals')
        {
            //$res->selectRaw('asu.office_fax,asu.office_phone,mst.mobile,con.phonecode,str.store_name,asu.supplier_id,mst.email,concat(amst.firstname,amst.lastname) as full_name,sta.status_id,sta.status_name,mst.uname,asu.created_on,asu.updated_on,asu.account_id,adm.admin_name,asu.company_name,con.country as country_name,mst.user_code,asu.completed_steps,asu.verified_steps, asu.is_verified');
        }
        else
        {           
			$res->selectRaw('asu.office_fax, asu.office_phone, amst.mobile, con.phonecode, str.store_name, asu.supplier_id, amst.email, concat(acd.firstname,acd.lastname) as full_name, amst.status as status_id, sta.status_name, amst.uname, asu.created_on, asu.updated_on, asu.account_id, asu.company_name, con.country as country_name, amst.user_code, (SELECT COUNT(supplier_product_id) as cnt FROM '.Config::get('tables.SUPPLIER_PRODUCT_ITEMS').'  as spi where spi.supplier_id = asu.supplier_id ) as product_cnts, (SELECT COUNT(sub_order_id) as cnt FROM '.Config::get('tables.SUB_ORDERS').'  as spo where spo.supplier_id = asu.supplier_id ) as order_cnts, asu.completed_steps, asu.verified_steps, asu.is_verified, asu.status');
        }		
        if (empty($arr['filterTerms']) && !empty($arr['search_text']))
        {
            $res->where('amst.uname', 'like', '%'.$arr['search_text'].'%');
        }
        if (!empty($arr['filterTerms']))
        {
            $subsql = '';
            $arr['filterTerms'] = !is_array($arr['filterTerms']) ? array(
                $arr['filterTerms']) : $arr['filterTerms'];
            if (in_array('uname', $arr['filterTerms']))
            {
                $subsql[] = 'amst.uname like (\'%'.$arr['search_text'].'%\')';
            }
            if (in_array('supplier', $arr['filterTerms']))
            {
                $subsql[] = 'asu.company_name like (\'%'.$arr['search_text'].'%\')';
            }
            if (in_array('code', $arr['filterTerms']))
            {
                $subsql[] = 'amst.user_code like (\'%'.$arr['search_text'].'%\')';
            }

            if (in_array('mobile', $arr['filterTerms']))
            {
                $subsql[] = 'amst.mobile like (\'%'.$arr['search_text'].'%\')';
            }
            if (!empty($subsql))
            {
                $res->whereRaw('('.implode(' OR ', $subsql).')');
            }
        }
        if (!empty($arr['user_name']) && !empty($arr['search_text']))
        {
            $res->where('mst.uname', 'like', '%'.$arr['search_text'].'%');
        }

        if (!empty($arr['country']) && !empty($arr['country']))
        {
            $res->where('ad.country_id', 'like', '%'.$arr['country'].'%');
        }
        if (!empty($arr['start_date']))
        {
            $res->whereDate('asu.created_on', '>=', date('Y-m-d', strtotime($arr['start_date'])));
        }
        if (!empty($arr['end_date']))
        {
            $res->whereDate('asu.created_on', '<=', date('Y-m-d', strtotime($arr['end_date'])));
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        else
        {
            $res->orderby('asu.created_on', 'DESC');
        }
        if ($count)
        {
            return $res->count();
        }
        else
        {
            $suppliers = $res->get();
            array_walk($suppliers, function(&$supplier)
            {
                $supplier->completed_steps = count(explode(',', $supplier->completed_steps));
                $supplier->verified_steps = count(explode(',', $supplier->verified_steps));
            });
            return $suppliers;
        }
    }
	
	public function suppliers_details ($wdata = array())
    {
        extract($wdata);
        $query = DB::table(Config::get('tables.SUPPLIER_MST').' as asu')                
                ->Join(Config::get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'asu.account_id')
				->Join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'asu.account_id')
                ->Join(config::get('tables.ACCOUNT_STATUS_LOOKUPS').' as asl', 'asl.status_id', '=', 'amst.status')
				->join(config::get('tables.ADDRESS_MST').' as addr', function($ad)
                {
                    $ad->on('addr.relative_post_id', '=', 'asu.supplier_id')
                    ->where('addr.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                    ->where('addr.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
                })            
                ->leftJoin(config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'addr.country_id')
                ->leftJoin(config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'addr.state_id')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'addr.city_id') 
                //->selectRaw('mst.email, asu.*, mst.uname, mst.user_code, concat(amst.firstname, amst.lastname) as full_name, amst.status_id, addr.*, mst.mobile, lc.country, ls.state, lc.country_id as cou_id, addr.country_id, lc.phonecode, asu.office_fax, asu.office_phone, asl.status_name, lci.city, mst.mobile, asu.account_id');
                ->selectRaw('asu.*, addr.*, amst.email, amst.uname, amst.user_code, CONCAT(ad.firstname, ad.lastname) as full_name, amst.status as status_id, amst.mobile, lc.country, ls.state, lc.country_id as cou_id, addr.country_id, lc.phonecode, asu.office_fax, asu.office_phone, asl.status_name, lci.city, asu.account_id');
        if (isset($account_id) && !empty($account_id))
        {
            $query->where('asu.account_id', $account_id);
        }
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $query->where('asu.supplier_id', $supplier_id);
        }
        if (isset($uname) && !empty($uname))
        {
            $query->where('amst.uname', $uname);
        }
        $details = $query->first();

        if ($details)
        {
            $details->completed_steps = explode(',', $details->completed_steps);
            $details->verified_steps = explode(',', $details->verified_steps);
            $details->steps = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->selectRaw('step_id, name')
                    ->orderby('priority', 'ASC')
                    ->get();
			//return $details;
            array_walk($details->steps, function(&$step) use(&$details)
            {
                $step->comleted = in_array($step->step_id, $details->completed_steps) ? 'Completed' : 'Not Completed';
                $step->status = in_array($step->step_id, $details->verified_steps) ? 'Verified' : 'Not Verified';
                $step->fields = [];
                $step->links = [];
                switch ($step->step_id)
                {
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.ACCOUNT_DETAILS'):
                        $step->fields[] = ['label'=>'Supplier Name', 'value'=>$details->full_name, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Username', 'value'=>$details->uname, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Supplier Code', 'value'=>$details->user_code, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Email', 'value'=>$details->email, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Mobile', 'value'=>$details->mobile, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Created On', 'value'=>date('d-M-Y H:i:s', strtotime($details->created_on)), 'type'=>'text'];
                        $step->fields[] = ['label'=>'Company Name', 'value'=>$details->company_name, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Address', 'value'=>implode(', ', array_filter([$details->flatno_street, $details->address, $details->city, $details->state, implode('-', [$details->country, $details->postal_code])])), 'type'=>'text'];
                        $step->fields[] = ['label'=>'Website', 'value'=>$details->website, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Phone', 'value'=>$details->office_phone, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Fax', 'value'=>$details->office_fax, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Status', 'value'=>$details->status_name, 'type'=>'text'];
                        break;
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.ACCOUNT_UPDATE'):
                        $details->store_details = DB::table(Config::get('tables.STORES').' as s')
                                ->leftJoin(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 's.store_id')
                                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'se.city_id')
                                ->leftJoin(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode_id', '=', 'lci.pincode_id')
                                ->leftJoin(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
                                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
                                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                                ->where('s.supplier_id', $details->supplier_id)
                                ->where('s.primary_store', Config::get('constants.ON'))
                                ->selectRaw('s.store_id, s.store_name, s.store_code, se.address1, se.address2, lci.city, se.website, se.postal_code, se.landline_no, se.phonecode, se.mobile_no, se.email, se.working_days, se.working_hours_from, se.working_hours_to, lc.country, ls.state, se.city_id, ls.state_id, lc.country_id')
                                ->first();
                        if ($details->store_details)
                        {
                            $step->fields[] = ['label'=>'Store Name', 'value'=>$details->store_details->store_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Store Code', 'value'=>$details->store_details->store_code, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Email', 'value'=>$details->store_details->email, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Mobile', 'value'=>$details->store_details->mobile_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Address', 'value'=>implode(', ', array_filter([$details->store_details->address1, $details->store_details->address2, $details->store_details->city, $details->store_details->state, implode('-', [$details->store_details->country, $details->store_details->postal_code])])), 'type'=>'text'];
                            $step->fields[] = ['label'=>'Website', 'value'=>$details->store_details->website, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Working Hours', 'value'=>implode('-', [$details->store_details->working_hours_from, $details->store_details->working_hours_to]), 'type'=>'text'];
                        }
                        break;
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.STORE_BANK'):
                        $details->payment_details = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                                ->where('supplier_id', $details->supplier_id)
                                ->selectRaw('payment_settings, sps_id')
                                ->first();

                        if (isset($details->payment_details->sps_id) && !empty($details->payment_details->sps_id))
                        {
                            $details->payment_details->payment_settings = json_decode($details->payment_details->payment_settings);
                            $step->fields[] = ['label'=>'Bank Name', 'value'=>$details->payment_details->payment_settings->bank_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Account Holder Name', 'value'=>$details->payment_details->payment_settings->account_holder_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Account No', 'value'=>$details->payment_details->payment_settings->account_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Account Type', 'value'=>$details->payment_details->payment_settings->account_type, 'type'=>'text'];
                            $step->fields[] = ['label'=>'IFSC CODE', 'value'=>$details->payment_details->payment_settings->ifsc_code, 'type'=>'text'];
                            if (isset($details->payment_details->payment_settings->country_id))
                            {
                                $details->payment_details->payment_settings->country = $this->commonObj->getCountryName($details->payment_details->payment_settings->country_id);
                                $details->payment_details->payment_settings->state = $this->commonObj->getStateName($details->payment_details->payment_settings->state_id);
                                $details->payment_details->payment_settings->city = $this->commonObj->getCityName($details->payment_details->payment_settings->city_id);
                            }
                            else
                            {
                                $details->payment_details->payment_settings->country = 0;
                                $details->payment_details->payment_settings->state = 0;
                                $details->payment_details->payment_settings->city = 0;
                            }
                            $details->payment_details->payment_settings->setting_id = $details->payment_details->sps_id;                           
                            $step->fields[] = ['label'=>'Address', 'value'=>implode(', ', array_filter([$details->payment_details->payment_settings->address1, $details->payment_details->payment_settings->address2, $details->payment_details->payment_settings->city, $details->payment_details->payment_settings->state, implode('-', [$details->payment_details->payment_settings->country, $details->payment_details->payment_settings->postal_code])])), 'type'=>'text'];
                        }
                        break;
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.VERIFY_KYC'):
                        $details->kyc_details = DB::table(Config::get('tables.KYC_DOCUMENTS').' as skv')
                                ->leftjoin(config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'skv.id_proof_document_type_id')
                                ->where('skv.relative_post_id', $details->supplier_id)
                                ->where('skv.post_type', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                                ->selectRaw('skv.pan_card_no, skv.pan_card_name, skv.dob, skv.pan_card_image, skv.vat_no, skv.cst_no, skv.auth_person_name, skv.auth_person_id_proof, skv.status_id, dt.type')
                                ->first();
                        if ($details->kyc_details)
                        {
                            $step->fields[] = ['label'=>'PAN Card No', 'value'=>$details->kyc_details->pan_card_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'PAN Card Name', 'value'=>$details->kyc_details->pan_card_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'PAN Card Image', 'value'=>URL::asset($details->kyc_details->pan_card_image), 'type'=>'link'];
                            $step->fields[] = ['label'=>'DOB', 'value'=>date('d-m-Y', strtotime($details->kyc_details->dob)), 'type'=>'text'];
                            $step->fields[] = ['label'=>'VAT No', 'value'=>$details->kyc_details->vat_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'CST No', 'value'=>$details->kyc_details->cst_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Authorized Person Name', 'value'=>$details->kyc_details->auth_person_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'ID Proof Type', 'value'=>$details->kyc_details->type, 'type'=>'text'];
                            $step->fields[] = ['label'=>'ID Proof', 'value'=>URL::asset($details->kyc_details->auth_person_id_proof), 'type'=>'link'];
                        }
                        break;
                }
            });
        }
        return $details;
    }

    public function save_suppliers ($data, $admin_id)
    {
        $user_name = $this->genetare_user_code();
        $password = $this->rKeyGen(10, 0);
        //insert in to account master
        $account_id = DB::table(Config::get('tables.ACCOUNT_MST'))->insertGetID(array(
            'salutation'=>1,
            'firstname'=>$data['supplier_first_name'],
            'lastname'=>$data['supplier_last_name'],
            'status_id'=>1,
            'created_on'=>date('Y-m-d H:i:s'),
            'updated_by'=>$admin_id
        ));
        //$supplier_code = $this->generate_supplier_code($account_id);
        $supplier_code = Commonsettings::generateUserCode();
        $activation_key = md5($supplier_code);
        //insert in to login_mst
        $login = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))->insertGetId(array(
            'account_id'=>$account_id,
            'uname'=>$supplier_code,
            'email'=>$data['email'],
            'mobile'=>$data['mobile'],
            'pass_key'=>md5(''.$password),
            'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SUPPLIER'),
            'user_code'=>$supplier_code,
            'activation_key'=>$activation_key
        ));
        $account_PRE = [];
        $account_PRE['account_id'] = $account_id;
        $user_preference_id = DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                ->insertGetId($account_PRE);
        //insert in to account supplier
        $supplier_id = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))->insertGetId(array(
            'account_id'=>$account_id,
            'company_name'=>$data['company_name'],
            'supplier_code'=>$supplier_code,
            'office_phone'=>$data['phone'],
            'office_fax'=>$data['fax'],
            'website'=>$data['website'],
            'created_on'=>date('Y-m-d H:i:s'),
            'file_path'=>Config::get('path.SUPPLIER_PRODUCT_IMAGE_PATH').$supplier_code.'/',
        ));
        //insert into account address
        $address_id = DB::table(Config::get('tables.ACCOUNT_ADDRESS'))->insertGetId(array(
            'account_id'=>$account_id,
            'address_type_id'=>1,
            'street1'=>$data['street1'],
            'street2'=>$data['street2'],
            'city_id'=>$data['city_id'],
            'state_id'=>$data['state_id'],
            'country_id'=>$data['country_id'],
            'postal_code'=>$data['Postcode'],
            'created_on'=>date('Y-m-d H:i:s')
        ));
        if (!empty($account_id))
        {
            $email_data = array(
                'acc_email'=>$data['email'],
                'user_name'=>$user_name,
                'password'=>$password);

            if (!empty($supplier_id))
            {
                ShoppingPortal::notify('SUPPLIER_SIGNUP', $supplier_id, Config::get('constants.ACCOUNT_TYPE.SUPPLIER'), $email_data, true, true, true, true);
                return $supplier_id;
            }
        }
        else
        {
            return false;
        }
    }

    public function verification_list ($arr)
    {
        $res = DB::table(Config::get('tables.ACCOUNT_VERIFICATION').' as av')
                ->join(Config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'av.document_type_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'av.account_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'av.account_id')
                ->where('av.is_deleted', Config::get('constants.OFF'));
        if (isset($arr['search_term']) && !empty($arr['search_term']))
        {
            $res->where('am.firstname', 'like', '%'.$arr['search_term'].'%');
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
            $verifications = $res->selectRaw('av.*, dt.type, dt.document_type_id, dt.other_fields as doc_other_fields, am.uname, concat(ad.firstname,\' \',ad.lastname) as full_name')->get();
            array_walk($verifications, function(&$v)
            {
                $v->other_fields = !empty($v->other_fields) ? json_decode($v->other_fields) : [];
                $v->doc_other_fields = !empty($v->doc_other_fields) ? json_decode($v->doc_other_fields, true) : [];
                array_walk($v->other_fields, function(&$field, $k) use($v)
                {
                    $field = ['id'=>$k, 'label'=>$v->doc_other_fields[$k]['label'], 'value'=>$field];
                });
                unset($v->doc_other_fields);
            });
            return $verifications;
        }
    }

    public function doc_list ()
    {
        return DB::table(Config::get('tables.DOCUMENT_TYPES'))
                        ->select('document_type_id', 'type', 'other_fields')
                        ->get();
    }

    public function change_verify_status ($data = array())
    {	
        extract($data);
        $udata['updated_by'] = $admin_id;
		$udata['is_verified'] = $status;
		DB::table(Config::get('tables.SUPPLIER_MST'))
				->where('account_id', $account_id)
				->update($udata);
        return true;
    }
	
	public function change_status ($data = array())
    {	
        extract($data);
        if (DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('uv_id', $data['uv_id'])
                        ->update(array('status_id'=>$data['status'])))
        {
            $data['account_id'] = DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                    ->where('uv_id', $data['uv_id'])
                    ->pluck('account_id');

            $proofs_verified = DB::table(Config::get('tables.ACCOUNT_VERIFICATION').' as accs')
                    ->leftJoin(Config::get('tables.DOCUMENT_TYPES').' as dt', 'accs.document_type_id', '=', 'dt.document_type_id')
                    ->where('accs.account_id', $data['account_id'])
                    ->where('accs.status_id', 1)
                    ->where('accs.is_deleted', Config::get('constants.OFF'))
                    ->groupby('accs.account_id')
                    ->selectRaw('sum(if(dt.proof_type=1,1,0)) as id_proof, sum(if(accs.document_type_id=19,1,0)) as pan_proof, sum(if(accs.document_type_id=4,1,0)) as bank_proof')
                    ->first();

            if ($proofs_verified->pan_proof >= 1 && ($proofs_verified->id_proof >= 1 || $proofs_verified->bank_proof >= 1))
            {
               /*  $steps = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                        ->where('account_id', $data['account_id'])
                        ->pluck('verified_steps');
                $steps = explode(',', $steps);
                if (in_array(4, $steps))
                {
                    $steps[] = 4;
                }
                $udata = [];
                $udata['verified_steps'] = implode(',', $steps); */
                $udata['updated_by'] = $admin_id;
                $udata['is_verified'] = 1;
                DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                        ->where('account_id', $data['account_id'])
                        ->update($udata);
            }
        }
        return true;
    }

    public function delete_doc ($data)
    {
        return DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('uv_id', $data['uv_id'])
                        ->update(array(
                            'is_deleted'=>Config::get('constants.ON')));
    }

    

    public function get_stores_list ($arr = array())
    {
        $res = DB::table(Config::get('tables.STORES').' as st')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as as', 'st.supplier_id', '=', 'as.supplier_id')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'se.city_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'se.state_id')
                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'se.country_id')
                ->selectRaw('st.store_id, as.company_name, st.store_name, se.mobile_no, CONCAT(se.address1,\' \',se.address2) as address, se.address1, se.address2, se.email, se.website, se.country_id, lci.city, st.status, st.store_code, st.updated_on, se.city_id, se.state_id, se.postal_code, st.supplier_id, st.store_logo, se.landline_no');

        if (!empty($arr['start_date']))
        {
            $res->whereDate('st.created_on', '>=', date('Y-m-d', strtotime($arr['start_date'])));
        }

        if (!empty($arr['end_date']))
        {
            $res->whereDate('st.created_on', '<=', date('Y-m-d', strtotime($arr['end_date'])));
        }

        if (!empty($arr['filterTerms']) && !empty($arr['search_text']))
        {
            $subsql = '';
            $arr['filterTerms'] = !is_array($arr['filterTerms']) ? array($arr['filterTerms']) : $arr['filterTerms'];
            if (in_array('store_name', $arr['filterTerms']))
            {
                $subsql[] = 'st.store_name like (\'%'.$arr['search_text'].'%\')';
            }
            if (in_array('phone', $arr['filterTerms']))
            {
                $subsql[] = 'se.mobile_no like (\'%'.$arr['search_text'].'%\')';
            }
            if (in_array('code', $arr['filterTerms']))
            {
                $subsql[] = 'st.store_code like (\'%'.$arr['search_text'].'%\')';
            }
            if (!empty($subsql))
            {
                $res->whereRaw('('.implode(' OR ', $subsql).')');
            }
        }

        if (!empty($arr['store_code']) && !empty($arr['store_code']))
        {
            $res->where('st.store_code', $arr['store_code']);
            return $res->first();
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        else
        {
            $res->orderby('st.store_name', 'DESC');
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            return $res->get();
        }
    }

    public function update_stores ($arr = array())
    {
        //return $arr;
        $supplier_id = '';
        $store_code = '';
        $update_values = '';
        extract($arr);
        if (isset($store_code) && !empty($store_code))
        {
            $res = DB::table(Config::get('tables.STORES').' as st')
                    ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                    ->where('st.store_code', $store_code)
                    ->update(array(
                'st.supplier_id'=>$create['supplier_id'],
                'st.store_name'=>$create['store_name'],
                'st.store_logo'=>$create['store_logo'],
                'st.status'=>$create['status'],
                'se.mobile_no'=>$store_extras['mobile_no'],
                'se.landline_no'=>$store_extras['landline_no'],
                'se.email'=>$store_extras['email'],
                'se.address1'=>$store_extras['address1'],
                'se.address2'=>$store_extras['address2'],
                'se.city_id'=>$store_extras['city'],
                'se.postal_code'=>$store_extras['postal_code'],
                'se.website'=>$store_extras['website']));
            if ($res)
            {
                return 2;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $create['primary_store'] = 1;
            $create['updated_by'] = $admin_id;
            $create['created_on'] = date('Y-m-d H:i:s');
            $store_id = DB::table(Config::get('tables.STORES'))
                    ->insertGetId($create);
            $store_code = 'SUP'.rand().$store_id;
            $createe['store_code'] = $store_code;
            if ($createe['store_code'])
            {
                DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $store_id)
                        ->update($createe);
            }
            $store_extras['store_id'] = $store_id;
            $store_extras['country_id'] = $store_extras['country'];
            $store_extras['state_id'] = $store_extras['state'];
            $store_extras['city_id'] = $store_extras['city'];
            unset($store_extras['country']);
            unset($store_extras['state']);
            unset($store_extras['city']);
            //return $store_extras;
            DB::table(Config::get('tables.STORES_EXTRAS'))
                    ->insert($store_extras);
            return 1;
        }
    }    

    public function verifyStep ($arr = array())
    {	
        $status = '';
        extract($arr);
        $query = DB::table(Config::get('tables.SUPPLIER_MST'))
                ->where('supplier_id', $supplier_id)
                ->whereRaw('FIND_IN_SET('.$step_id.',completed_steps)')
                ->where(function($sub) use($status, $step_id)
        {
            $sub->whereNULL('verified_steps')
            ->orWhere(function($sub2)use($status, $step_id)
            {
                $sub2->whereNotNull('verified_steps');
                if (!empty($status))
                {
                    $sub2->whereRaw('!FIND_IN_SET('.$step_id.',verified_steps)');
                }
                else
                {
                    $sub2->whereRaw('FIND_IN_SET('.$step_id.',verified_steps)');
                }
            });
        });
        $steps = $query->selectRaw('completed_steps, verified_steps')->first();
        if (!empty($steps))
        {
            $verified_steps = explode(',', $steps->verified_steps);
            if (!empty($status))
            {
                if (!in_array($step_id, $verified_steps))
                    $verified_steps[] = $step_id;
            }
            else
            {
                unset($verified_steps[array_search($step_id, $verified_steps)]);
            }
            $verified_steps = array_filter($verified_steps);
            $verified_steps_count = count($verified_steps);
            $steps_count = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->count();
            $udata = [];
            $udata['is_verified'] = ($steps_count == $verified_steps_count) ? Config::get('constants.ON') : Config::get('constants.OFF');
            $verified_steps = implode(',', $verified_steps);
            $verified_steps = !empty($verified_steps) ? $verified_steps : NULL;
            $udata['verified_steps'] = $verified_steps;
            $udata['updated_by'] = $admin_id;
            $s = DB::table(Config::get('tables.SUPPLIER_MST'))
                    ->where('supplier_id', $supplier_id)
                    ->update($udata);
            if ($udata['is_verified'])
            {
                $email_data['supplier_details'] = $this->suppliers_details(['supplier_id'=>$supplier_id]);
                //ShoppingPortal::notify('SUPPLIER_ACCOUNT_ACTIVATION', $supplier_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), $email_data, true, true, true, true);
            }
            //return $s ? ($udata['is_verified'] ? 2 : 1) : false;
            return $s ? ($udata['is_verified'] ? 1 : 1) : false;
        }
        return false;
    }

    public function status_update ($account_id, $status)
    {
        return DB::table(Config::get('tables.SUPPLIER_MST'))
                        ->where('account_id', $account_id)
                        ->update(array('status'=>$status['status']));
    }

    public function supplier_edit ($postdata)
    {
        extract($postdata);
        $currentdate = date('Y-m-d H:i:s');
        $res1 = $res2 = $res3 = $res4 = $res5 = $res6 = false;
        if (!empty($postdata))
        {
            $res1 = DB::table(Config::get('tables.ACCOUNT_MST'))
                    ->where('account_id', $postdata['supplier_account_id'])
                    ->update(array('email'=>$postdata['email'], 'mobile'=>$postdata['mobile']));

            $res2 = DB::table(Config::get('tables.SUPPLIER_MST'))
                    ->where('account_id', $postdata['supplier_account_id'])
                    ->update(array('office_phone'=>$postdata['officePhone'], 'office_fax'=>$postdata['officeFax']));
			
            if (isset($address_id) && !empty($address_id))
            {

                $res3 = DB::table(Config::get('tables.ADDRESS_MST'))
                        ->where('relative_post_id', $postdata['supplier_account_id'])
                        ->where('post_type', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                        ->update(array(
							'city_id'=>$postdata['city'],
							'state_id'=>$postdata['state'],
							'country_id'=>$postdata['country'],
							'postal_code'=>$postdata['Postcode'],
							'flatno_street'=>$postdata['street1'],
							'address'=>$postdata['street2'],
							'updated_on'=>$currentdate
						));
            }
            else
            {
                $address['account_id'] = $supplier_account_id;
                $address['flatno_street'] = $street1;
                $address['address'] = $street2;
                $address['city_id'] = $city;
                $address['state_id'] = $state;
                $address['country_id'] = $country;
                $address['postal_code'] = $Postcode;
                $address['status'] = 0;
                $address['created_on'] = date('Y-m-d H:i:s');
                $address['address_type_id'] = Config::get('constants.ADDRESS.PRIMARY');
                $res3 = $res2 = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($address);
            }

            if (isset($store_id) && !empty($store_id))
            {
                $store_details['updated_on'] = $currentdate;
                $store_details['updated_by'] = $account_id;
                $res4 = DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $postdata['store_id'])
                        ->update($store_details);
            }
            else
            {
                $store_details['supplier_id'] = $supplier_id;
                $store_details['status'] = Config::get('constants.ON');
                $store_details['updated_by'] = $account_id;
                $store_details['created_on'] = $currentdate;
                $store_id = DB::table(Config::get('tables.STORES'))->insertGetId($store_details);
                $store_code = 'SUP'.rand(22222,99999).$store_id;
                $create['primary_store'] = 1;
                $create['store_code'] = $store_code;
                if ($create['store_code'])
                {
                    $res4 = DB::table(Config::get('tables.STORES'))->where('store_id', $store_id)->update($create);
                    unset($store_extra);
                    $store_extras['store_id'] = $store_id;
                    DB::table(Config::get('tables.STORES_EXTRAS'))->insertGetId($store_extras);
                }
            }

            if (isset($store_extra) && !empty($store_extra))
            {
                $res4 = DB::table(Config::get('tables.STORES_EXTRAS'))
                        ->where('store_id', $postdata['store_id'])
                        ->update($store_extra);
            }

            if (isset($setting_id) && !empty($setting_id))
            {
                $res5 = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                        ->where('supplier_id', $postdata['supplier_id'])
                        ->where('sps_id', $setting_id)
                        ->update(['payment_settings'=>json_encode($payment_settings)]);
            }
            else
            {

                $res5 = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))->insertGetId(['supplier_id'=>$postdata['supplier_id'], 'payment_settings'=>json_encode($payment_settings), 'updated_by'=>$postdata['account_id']]);
            }
        }
        return $res1 || $res2 || $res3 || $res4 || $res5;
    }

    public function genetare_user_code ()
    {
        $user_codes = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST').' as um')
                ->where('um.uname', 'LIKE', '%'.'SUP'.'%')
                ->lists('um.uname');
        re_create:
        $code = 'SUP'.mt_rand(10000000, 99999999);
        if (in_array($code, $user_codes))
        {
            goto re_create;
        }
        else
        {
            return $code;
        }
    }

    public function generate_supplier_code ($account_id)
    {
        $function_ret = '';
        $profix = $account_id;
        $iLoop = true;
        $disp = $this->rKeyGen(3, 1);
        $disp1 = 'SP'.$disp.$profix;
        return $disp1;
    }

    public function change_pwd ($data, $wdata = array())
    {		
        if (!empty($data) && !empty($wdata))
        {
            $suppliers['pass_key'] = md5($data['login_password']);
            return DB::table(Config::get('tables.ACCOUNT_MST'))
                            ->where('account_id', $wdata['account_id'])
                            ->update($suppliers);
        }
        return false;
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }
        else
        {
            $key = $this->rKeyGen_ALPHA($digits, false);
        }
        return $key;
    }

    function rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $j = 0;
        // Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

    public function email_validate ($postdata)
    {
        return (DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))
                        ->where('email', $postdata['email'])
                        ->count()) > 0 ? false : true;
    }

    public function order_details ($data = array())
    {
        return DB::table(Config::get('tables.SUB_ORDERS').' as sop')
                        ->leftJoin(Config::get('tables.ORDER_ITEMS').' as op', 'op.order_id', '=', 'sop.order_id')
                        ->leftJoin(Config::get('tables.PRODUCT_ITEMS').' as pi', 'pi.product_id', '=', 'op.product_id')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_ORDER_STATUS_LOOKUPS').' as posl', 'posl.order_status_id', '=', 'sop.order_status_id')
                        ->where('sop.order_id', $data['order_id'])
                        ->selectRaw('sop.*,pi.*,sop.order_code,sop.amount,sop.order_date,sop.last_updated')
                        ->get();
    }

    public function order_cancel ($arr = array())
    {
        $status['order_status_id'] = 5;
        $status['last_updated'] = date(Config::get('constants.DB_DATE_TIME_FORMAT'));
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->whereIn('order_status_id', array(
                            0,
                            1,
                            2))
                        ->where('order_id', $arr['order_id'])
                        ->update($status);
    }

    public function get_stock_log_report ($data = array())
    {
        $product = DB::table(Config::get('tables.SUPPLER_PRODUCT_STOCK_LOG').' as sop')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'sop.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as sup', 'sup.supplier_id', '=', 'spi.supplier_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as mst', 'mst.account_id', '=', 'sup.account_id')
                // ->where('spi.supplier_id', $data['supplier_id'])
                ->selectRaw('sop.*,pro.product_name,sup.supplier_id,mst.account_id,sup.company_name');
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $product->where('pro.product_id', $data['product_id']);
        }
        if (isset($data['supplier_id']) && !empty($data['supplier_id']))
        {
            $product->where('sup.supplier_id', $data['supplier_id']);
        }
        if (isset($data['search_term']) && !empty($data['search_term']))
        {
            $product->whereRaw('(sup.company_name like \'%'.$arr['search_txt'].'%\'  OR  pro.product_name like \'%'.$arr['search_txt'].'%\')');
        }
        if (!empty($arr['from']))
        {
            $res->whereDate('sop.created_on', '>=', date('Y-m-d', strtotime($arr['from'])));
        }
        if (!empty($arr['to']))
        {
            $res->whereDate('sop.created_on', '<=', date('Y-m-d', strtotime($arr['to'])));
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        if (isset($data['counts']) && !empty($data['counts']))
        {
            return $product->count();
        }
        else
        {
            return $product->get();
        }
    }

    public function supplier_check ($data = array())
    {
        return DB::table(Config::get('tables.ACCOUNT_LOGIN_MST').' as lmst')
                        ->join(Config::get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'lmst.account_id')
                        ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as as', 'as.account_id', '=', 'lmst.account_id')
                        ->leftjoin(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS').' as com', 'com.supplier_id', '=', 'as.supplier_id')
                        ->selectRaw('as.supplier_id, com.supplier_id as commission_supplier, com.commission_type, com.currency_id, com.commission_unit, com.commission_value, lmst.email, lmst.user_code, concat(amst.firstname, amst.lastname) as full_name, lmst.mobile, lmst.uname, lmst.account_id, as.company_name')
                        ->where('amst.is_deleted', Config::get('constants.OFF'))
                        ->where('lmst.uname', $data['supplier_name'])
                        ->where('lmst.account_type_id', 3)
                        ->first();
    }

    public function save_commissions ($postdata)
    {
        $get_commission = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))
                ->where('is_deleted', Config::get('constants.OFF'))
                ->where('supplier_id', $postdata['supplier_id'])
                ->pluck('commission_id');
        $data = array();
        $currentdate = date('Y-m-d H:i:s');
        $data['supplier_id'] = $postdata['supplier_id'];
        if ($postdata['fixed_rates'] == 1)
        {
            $data['commission_value'] = $postdata['amount'];
            $data['commission_unit'] = $postdata['commission_unit'];
            if ($postdata['commission_unit'] == 2)
            {
                $data['currency_id'] = $postdata['currency_id'];
            }
            else
            {
                //$data['currency_id'] = 0;
                $data['currency_id'] = $postdata['currency_id'];
            }
        }

        if ($postdata['fixed_rates'] == 2)
        {
            $data['commission_value'] = 0;
            $data['commission_unit'] = 0;
            //$data['currency_id'] = 0;
            $data['currency_id'] = $postdata['currency_id'];
        }
        $data['commission_type'] = $postdata['fixed_rates'];
        $data['status'] = Config::get('constants.ACTIVE');

        if (!empty($get_commission))
        {
            $data['updated_by'] = $postdata['admin_id'];
            $result = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->where('commission_id', $get_commission)
                    ->update($data);
        }
        else
        {
            $data['created_on'] = $currentdate;
            $data['created_by'] = $postdata['admin_id'];
            $result = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))->insertGetId($data);
        }
        if (!empty($result))
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    public function supplier_payment_report ($arr = array(), $count = false)
    {
        extract($arr);
        $payments = DB::table(Config::get('tables.ODER_SALES_COMMISSION').' as sosc')
                ->leftjoin(Config::get('tables.ORDERS').' as o', 'o.order_id', '=', 'sosc.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as so', 'so.sub_order_id', '=', 'sosc.sub_order_id')
                ->leftjoin(Config::get('tables.ORDER_ITEMS').' as oi', 'oi.order_item_id', '=', 'sosc.order_item_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'oi.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_COMBINATIONS').' as proc', 'proc.product_cmb_id', '=', 'spi.product_cmb_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sosc.currency_id')
                ->leftJoin(Config::get('tables.ODER_SALES_COMMISSION_PAYMENT').' as soscp', 'soscp.osc_id', '=', 'sosc.osc_id')
                ->leftJoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as p', 'p.payment_status_id', '=', 'soscp.supplier_payment_status_id')
                ->leftJoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'sosc.supplier_id')
                ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'd.account_id', '=', 's.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as ud', 'ud.account_id', '=', 's.account_id')
                ->where('sosc.is_deleted', 0);
        if (!empty($from))
        {
            $payments->whereDate('a.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (!empty($to))
        {
            $payments->whereDate('a.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($term) && !empty($term))
        {
            $payments->where(function($subquery) use($term)
            {
                $subquery->orWhere('d.uname', 'like', '%'.$term.'%')
                        ->orWhere('a.remark', 'like', '%'.$term.'%');
            });
        }
        if (isset($start) && isset($length))
        {
            $payments->skip($start)
                    ->take($length);
        }
        if (isset($orderby) && isset($order))
        {
            $payments->orderBy($orderby, $order);
        }
        else
        {
            $payments->orderBy('sosc.osc_id', 'DESC');
        }
        if ($count)
        {
            return $payments->count();
        }
        else
        {
            $payments->selectRaw('sosc.osc_id,sosc.qty,sosc.mrp_price,sosc.supplier_sold_price,sosc.supplier_price_sub_total,sosc.created_on,oi.order_item_code,cur.currency,cur.currency_symbol,p.payment_status,s.company_name,s.supplier_code,o.order_code,so.sub_order_code,oi.discount,if(proc.product_cmb is not null,concat(pro.product_name,proc.product_cmb),pro.product_name) as product_name');
            $payments = $payments->get();
            array_walk($payments, function(&$payment)
            {
                $payment->Fcreated_on = date('d-M-Y H:i:s', strtotime($payment->created_on));
                $payment->Fqty = number_format($payment->qty, 0, '.', ',');
                $payment->Fsupplier_price_sub_total = $payment->currency_symbol.' '.number_format($payment->supplier_price_sub_total, 0, '.', ',').' '.$payment->currency;
                $payment->Fsupplier_sold_price = $payment->currency_symbol.' '.number_format($payment->supplier_sold_price, 0, '.', ',').' '.$payment->currency;
                $payment->Fmrp_price = $payment->currency_symbol.' '.number_format($payment->mrp_price, 0, '.', ',').' '.$payment->currency;
            });
            return $payments;
        }
    }

    public function get_wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET').' as wa')
                        ->where('wa.status', Config::get('constants.ON'))
                        ->get();
    }

    public function supplier_order_details ($arr = array())
    {
        extract($arr);
        $orders = DB::table(Config::get('tables.SUB_ORDERS').' as sub')
                ->leftjoin(Config::get('tables.ORDERS').' as pr', 'pr.order_id', '=', 'sub.order_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'pr.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'pr.payment_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as os', 'os.order_status_id', '=', 'pr.order_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'sub.sub_order_status_id')->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'd.account_id', '=', 'sub.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as ud', 'ud.account_id', '=', 'd.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'sub.supplier_id')
                ->leftjoin(Config::get('tables.SUPPLIER_COMMISSIONS_LOOKUPS').' as cl', 'cl.commission_type_id', '=', 'sub.commission_type')
                ->where('sub.is_deleted', Config::get('constants.OFF'))
                ->where('sub.sub_order_code', $id)
                ->selectRaw('sub.sub_order_code,sub.created_on,s.supplier_code,s.company_name,pt.payment_type,ps.payment_status,ps.payment_status_class,os.status as order_status,os.order_status_class,sub.commission_type as sub_commission_type,cl.commission_type,sub.commission_amount,sub.commission_unit,sub.commission_value')
                ->first();
        if (!empty($orders))
        {
            return $orders;
        }
        else
        {
            return NULL;
        }
    }

    public function supplier_payment_details ($arr = array())
    {
        extract($arr);
        $payments = DB::table(Config::get('tables.ODER_SALES_COMMISSION').' as sosc')
                ->leftjoin(Config::get('tables.ORDERS').' as o', 'o.order_id', '=', 'sosc.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as so', 'so.sub_order_id', '=', 'sosc.sub_order_id')
                ->leftjoin(Config::get('tables.ORDER_ITEMS').' as oi', 'oi.order_item_id', '=', 'sosc.order_item_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'oi.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_COMBINATIONS').' as proc', 'proc.product_cmb_id', '=', 'spi.product_cmb_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sosc.currency_id')
                ->leftJoin(Config::get('tables.ODER_SALES_COMMISSION_PAYMENT').' as soscp', 'soscp.osc_id', '=', 'sosc.osc_id')
                ->leftJoin(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'o.payment_type_id')
                ->leftJoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as p', 'p.payment_status_id', '=', 'soscp.supplier_payment_status_id')
                ->leftJoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'sosc.supplier_id')
                ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'd.account_id', '=', 's.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as ud', 'ud.account_id', '=', 's.account_id')
                ->where('sosc.is_deleted', 0)
                ->where('oi.order_item_code', $order_item_code)
                ->selectRaw('sosc.*,oi.order_item_code,cur.currency,pt.payment_type,cur.currency_symbol,p.payment_status,s.company_name,s.supplier_code,o.order_code,so.sub_order_code,oi.discount,if(proc.product_cmb is not null,concat(pro.product_name,proc.product_cmb),pro.product_name) as product_name')
                ->first();
        if (!empty($payments))
        {
            return $payments;
        }
        else
        {
            return NULL;
        }
    }

    public function updateNextStep ($arr = array())
    {
        $ASdata = [];
        extract($arr);
        $next = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                ->havingRaw('min(priority)')
                ->selectRaw('step_id,route')
                ->first();
        $ASdata['next_step'] = $next->step_id;
        if (!empty($next_step))
        {
            $ASdata['completed_steps'] = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->where('priority', '<=', $next_step)
                    ->selectRaw('GROUP_CONCAT(step_id) as completed_steps')
                    ->pluck('completed_steps');
        }
        DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                ->where('supplier_id', $supplier_id)
                ->update($ASdata);
        return (isset($next->route) && !empty($next->route)) ? URL::route($next->route) : URL::to('supplier/dashboard');
    }

    public function getSupplierPreferences ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.ACCOUNT_MST').' as lm')
                        ->join(Config::get('tables.SUPPLIER_MST').' as s', 's.account_id', '=', 'lm.account_id')
                        ->leftjoin(Config::get('tables.SUPPLIER_PREFERENCE').' as sp', 'sp.supplier_id', '=', 's.supplier_id')
                        ->where('lm.uname', $uname)
                        ->selectRaw('sp.*,s.supplier_id')
                        ->first();
    }

    public function savePerferences ($arr = array())
    {
        extract($arr);
        $preferences['is_ownshipment'] = (isset($preferences['is_ownshipment']) && !empty($preferences['is_ownshipment'])) ? Config::get('constants.ON') : Config::get('constants.OFF');
        if (DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                        ->where('supplier_id', $supplier_id)
                        ->exists())
        {
            return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                            ->where('supplier_id', $supplier_id)
                            ->update($preferences);
        }
        else
        {
            $preferences['supplier_id'] = $supplier_id;
            return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                            ->insert($preferences);
        }
    }

    public function getBrandsList ($data, $count = false)
    {
        extract($data);
        $brands = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as spa')
                ->join(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'spa.brand_id')
                ->join(Config::get('tables.SUPPLIER_MST').' as s', 's.supplier_id', '=', 'spa.supplier_id')
                ->where('spa.is_deleted', Config::get('constants.OFF'));
        if (!empty($search_term) && isset($search_term))
        {
            $search_term = '%'.$search_term.'%';
            $brands->where(function($s) use($search_term)
            {
                $s->where('pb.brand_name', 'like', $search_term)
                        ->orWhere('s.company_name', 'like', $search_term);
            });
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $brands->where('spa.brand_id', $brand_id);
        }
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $brands->where('spa.supplier_id', $supplier_id);
        }
        if ($count)
        {
            return $brands->count();
        }
        else
        {
            if (isset($data['start']) && isset($data['length']))
            {
                $brands->skip($data['start'])->take($data['length']);
            }
            if (isset($data['orderby']))
            {
                $brands->orderby($data['orderby'], $data['order']);
            }
            $brands = $brands->leftjoin(Config::get('tables.LOGIN_STATUS_LOOKUPS').' as ls', 'ls.status_id', '=', 'spa.status')
                    ->leftjoin(Config::get('tables.VERIFICATION_STATUS_LOOKUPS').' as vs', 'vs.is_verified', '=', 'spa.is_verified')
                    ->selectRaw('pb.created_on, pb.brand_id, pb.brand_name, ls.status_id, ls.status_class, ls.status, pb.is_verified as main_is_verified, pb.is_exclusive_for_supplier,vs.is_verified,vs.verification,vs.verification_class,s.company_name')
                    ->get();
            if (!empty($brands))
            {
                array_walk($brands, function(&$brand)
                {
                    $brand->created_on = date('d-M-Y H:i:s', strtotime($brand->created_on));
                    $brand->status = !empty($brand->status) ? $brand->status : 'Inactive';
                    $brand->status_class = !empty($brand->status_class) ? $brand->status_class : 'label label-danger';
                });
            }
            return $brands;
        }
    }

    public function deleteBrand ($data)
    {
        extract($data);
        return Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('brand_id', $data['id'])
                        ->update(['is_deleted'=>1, 'updated_by'=>$account_id, 'updated_on'=>date('Y-m-d H:i:s')]);
    }

    public function updateBrandStatus ($arr)
    {
        extract($arr);
        $update = [];
        $update['status'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                ->where('brand_id', $id);
        if ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('status', Config::get('constants.INACTIVE'));
        }
        elseif ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('status', Config::get('constants.ACTIVE'));
        }
        return $query->update($update);
    }

    public function updateBrandVerification ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['is_verified'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                ->where('brand_id', $brand_id);
        if ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('is_verified', Config::get('constants.INACTIVE'));
        }
        elseif ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('is_verified', Config::get('constants.ACTIVE'));
        }
        return $query->update($update);
    }

    public function saveBrand ($arr)
    {
        extract($arr);
        $res = false;
        DB::beginTransaction();
        $brand = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                ->where('brand_name', $brand_name)
                ->selectRaw('brand_id,is_deleted')
                ->first();
        if (!empty($brand))
        {
            $brand_id = $brand->brand_id;
            if ($brand->is_deleted)
            {
                DB::table(Config::get('tables.PRODUCT_BRANDS'))
                        ->where('brand_id', $brand_id)
                        ->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id]);
            }
        }
        else
        {
            $ibrand['brand_name'] = $brand_name;
            $ibrand['sku'] = $ibrand['url_str'] = str_replace(' ', '-', strtolower($brand_name));
            $ibrand['created_by'] = $ibrand['updated_by'] = $account_id;
            $ibrand['created_on'] = $ibrand['updated_on'] = date('Y-m-d H:i:s');
            $brand_id = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                    ->insertGetId($ibrand);
        }
        if (!DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('supplier_id', $supplier_id)
                        ->where('brand_id', $brand_id)
                        ->exists())
        {
            $ins_associate['brand_id'] = $brand_id;
            $ins_associate['supplier_id'] = $supplier_id;
            $ins_associate['updated_by'] = $account_id;
            $ins_associate['updated_on'] = date('Y-m-d H:i:s');
            $res = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->insertGetId($ins_associate);
        }
        else
        {
            $res = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->where('supplier_id', $supplier_id)
                    ->where('brand_id', $brand_id)
                    ->where('is_deleted', Config::get('constants.ON'))
                    ->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id]);
        }
        $res ? DB::commit() : DB::rollback();
        return $res;
    }
	
	public function get_meta_info ($arr = array())
    {
        extract($arr);
        $query = Db::table(Config::get('tables.META_INFO'));
        if (isset($meta_info_id) && !empty($meta_info_id))
        {
            $query->where('meta_info_id', $meta_info_id);
        }
        if (isset($post_type_id) && !empty($post_type_id) && isset($relative_post_id) && !empty($relative_post_id))
        {
            $query->where('post_type_id', $post_type_id)
                    ->where('relative_post_id', $relative_post_id);
        }

        return $query->first();
    }
	
	public function save_meta_info ($arr = array())
    {		
        extract($arr);
        $meta_info_id = Db::table(Config::get('tables.META_INFO'))
                ->where('post_type_id', $meta_info['post_type_id'])
                ->where('relative_post_id', $meta_info['relative_post_id'])
                ->pluck('meta_info_id');
        if (!$meta_info_id)
        {
            $meta_info['created_on'] = date('Y-m-d H:i:s');
            return Db::table(Config::get('tables.META_INFO'))
                            ->insertGetId($meta_info);
        }
        else
        {
            return Db::table(Config::get('tables.META_INFO'))
                            ->where('meta_info_id', $meta_info_id)
                            ->update($meta_info);
        }
        return false;
    }
	
	public function getProfitSharingList (array $arr = array(), $count = false)
    {
        extract($arr);        
        $merchants = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
                ->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'ps.supplier_id')
                ->where('ps.is_deleted', Config::get('constants.OFF'));
		
        if (isset($status))
        {
            $merchants->where('ps.status', $status);
        }
        if (isset($from) && !empty($from))
        {
            //$merchants->whereDate('ps.updated_on', '<=', showUTZ($from, 'Y-m-d'));
        }
        else if (isset($to) && !empty($to))
        {
            //$merchants->whereDate('ps.updated_on', '>=', showUTZ($to, 'Y-m-d'));
        }
        if ((isset($search_text) && !empty($search_text)))
        {
            $merchants->where('sm.company_name', 'like', '%'.$search_text.'%')
                        ->orwhere('sm.supplier_code', 'like', '%'.$search_text.'%')                        
                        ->orwhere('msm.reg_company_name', 'like', '%'.$search_text.'%');
        }        
		 
        if ($count)
        {
            return $merchants->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $merchants->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                //$merchants->orderby($orderby, $order);
            }
            else
            {
                //$merchants->orderby('ps.sps_id', 'DESC');
            }
            $merchants = $merchants->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'ps.created_by')
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'ps.created_by')
                    ->selectRaw('ps.sps_id as id, sm.company_name, sm.supplier_code, ps.status, ps.created_on, ps.updated_on, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn, CONCAT(ad.firstname,\' \',ad.lastname,\' (\',am.uname,\')\') as created_by')
                    ->get();
					
			//return $merchants;	
			
            if (!empty($merchants))
            {
                array_walk($merchants, function(&$merchant)
                {
                    $merchant->created_on = showUTZ($merchant->created_on, 'd-M-Y H:i:s');
                    $merchant->updated_on = ($merchant->updated_on != null) ? showUTZ($merchant->updated_on, 'd-M-Y H:i:s') : '--';
                    //$merchant->store_name = !empty($merchant->store_name) ? $merchant->store_name : '-';
                    //$merchant->store_code = !empty($merchant->store_code) ? $merchant->store_code : '-';
                    $merchant->profit_sharing = !empty($merchant->profit_sharing) ? $merchant->profit_sharing.'%' : '-';
                    $merchant->cashback_on_pay = !empty($merchant->cashback_on_pay) ? $merchant->cashback_on_pay.'%' : '-';
                    $merchant->cashback_on_redeem = !empty($merchant->cashback_on_redeem) ? $merchant->cashback_on_redeem.'%' : '-';
                    $merchant->cashback_on_shop_and_earn = !empty($merchant->cashback_on_shop_and_earn) ? $merchant->cashback_on_shop_and_earn.'%' : '-';
                    $merchant->actions = [];
                    $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'view']), 'label'=>'View'];
                    switch ($merchant->status)
                    {
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.PENDING'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'edit']), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', [ 'id'=>$merchant->id, 'status'=>strtolower('REJECTED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED'))];
                            break;
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.ACCEPTED'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'edit']), 'label'=>'Edit'];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', [ 'id'=>$merchant->id, 'status'=>strtolower('REJECTED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', [ 'id'=>$merchant->id, 'status'=>strtolower('CLOSED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'))];
                            break;
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.REJECTED'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'edit']), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', ['id'=>$merchant->id, 'status'=>strtolower('CLOSED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.delete', ['id'=>$merchant->id]), 'confirm'=>'Are you sure, you wants to delete?', 'label'=>'Delete'];
                            break;
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.CLOSED'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.delete', ['id'=>$merchant->id]), 'confirm'=>'Are you sure, you wants to delete?', 'label'=>'Delete'];
                            break;
                    }
                    $merchant->status_class = Config::get('dispclass.seller.profit-sharing.status.'.$merchant->status);
                    $merchant->status = trans('general.seller.profit-sharing.status.'.$merchant->status);
                });
            }
            return $merchants;
        }
    }
	
	public function getProfitSharingDetails (array $arr = array())
    {
        $merchant['details'] = null;
        $merchant['new_request'] = null;
        $merchant['current_details'] = null;
        extract($arr);
        $mr_details = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
						->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'ps.supplier_id')
						->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'sm.account_id')
						->join(Config::get('tables.ADDRESS_MST').' as add', function($join) 
						{
							$join->on('add.relative_post_id', '=', 'sm.supplier_id')
								 ->where('add.address_type_id', '=', 1)
								 ->where('add.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'));
						})           
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'add.country_id')
                ->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'add.state_id')                
                ->leftjoin(Config::get('tables.LOCATION_CITY').' as ll', 'll.city_id', '=', 'add.city_id')
                ->join(Config::get('tables.CASHBACK_SETTINGS').' as cs', 'cs.supplier_id', '=', 'ps.supplier_id')            
				->selectRaw('ps.created_on, ps.sps_id as id, sm.company_name, sm.supplier_code, ps.status, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn, am.uname, am.mobile, am.email, CONCAT_WS(\', \', add.flatno_street, add.address, ll.city, ls.state, concat(lc.country, \'-\', add.postal_code)) as address, ps.supplier_id, cs.pay, cs.shop_and_earn as offer_cashback, cs.is_cashback_period, cs.member_redeem_wallets, cs.cashback_start, cs.cashback_end')
                ->where('sps_id', $id)
                ->first();
			
        if (!empty($mr_details))
        {

            $mr_details->created_on = showUTZ($mr_details->created_on, 'd-M-Y H:i:s');
            $mr_details->profit_sharing = !empty($mr_details->profit_sharing) ? $mr_details->profit_sharing.'%' : '-';
            $mr_details->profit_sharing_in_label = '%';
            ///$mr_details->store_name = !empty($mr_details->store_name) ? $mr_details->store_name : '-';
            //$mr_details->store_code = !empty($mr_details->store_code) ? $mr_details->store_code : '-';

            switch ($mr_details->status)
            {
                case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'):
                    $mr_details->action = ['url'=>route('admin.seller.commission.update-status', ['id'=>$mr_details->id, 'status'=>strtolower('ACCEPTED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))];
                    break;
                default:
                    $mr_details->action = ['url'=>route('admin.seller.commission.save', ['id'=>$mr_details->id]), 'label'=>trans('general.btn.save')];
            }
            $merchant['details'] = $mr_details;
        }
		
		
        $new_request = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
                ->selectRaw('ps.status, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn')
                ->where('ps.status', Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'))
                ->where('sps_id', $id)
                ->first();

        if (empty($new_request->status))
        {
            $merchant['new_request'] = $new_request;
        }		
        $current_details = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
                ->selectRaw('ps.status, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn')
                ->where('ps.status', Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))
                ->orderBy('ps.sps_id', 'DESC')                
                ->where('supplier_id', $mr_details->supplier_id)
                ->first();

        if (!empty($current_details))
        {
            $merchant['current_details'] = $current_details;
        }
        return $merchant;
    }
	
	public function profitSharingStatusUpdate (array $arr = array())
    {	
        $profit_share = [];
        extract($arr);
        $profit_share['updated_on'] = getGTZ();
        $profit_share['updated_by'] = $account_id;
        $profit_share['status'] = $status;		
        $query = DB::table(Config::get('tables.PROFIT_SHARING'))
                ->where('sps_id', $id);
        switch ($status)
        {
            case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'):
                $query->whereIn('status', [Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'), Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED')]);
                DB::beginTransaction();				
                if ($query->update($profit_share))
                {					
                    $profit_share['status'] = Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED');
                    $details = DB::table(Config::get('tables.PROFIT_SHARING'))
                            ->where('sps_id', $id)
                            ->selectRaw('supplier_id, bcategory_id, cashback_on_shop_and_earn, is_cashback_period, cashback_start, cashback_end')
                            ->first();

                    $update['status'] = 2;
                    $update['updated_by'] = $account_id;
                    $update['updated_on'] = getGTZ();
                    //$update['bcategory_id'] = $details->bcategory_id;
                    /* DB::table(Config::get('tables.CASHBACK_OFFFERS'))
                            ->where('supplier_id', $details->supplier_id)
                            ->where('cboffer_type', Config::get('constants.CBOFFER_TYPE.DISCOUNT'))
                            ->where('is_deleted', config('constants.OFF'))
                            ->where('is_approved', Config::get('constants.ON'))
                            ->whereIn('status', [0, 1])
                            ->update($update); */

                    $update_offer['status'] = Config::get('constants.ON');
                    $update_offer['is_approved'] = Config::get('constants.ON');
                    $update_offer['updated_by'] = $account_id;
                    $update_offer['updated_on'] = getGTZ();
                    $update_offer['new_cashback'] = $details->cashback_on_shop_and_earn;
                    $update_offer['start_date'] = getGTZ($details->cashback_start, 'Y-m-d');
                    $update_offer['end_date'] = getGTZ($details->cashback_end, 'Y-m-d');

                    $update_setting['is_cashback_period'] = $details->is_cashback_period;
                    $update_setting['cashback_start'] = getGTZ($details->cashback_start, 'Y-m-d');
                    $update_setting['cashback_end'] = getGTZ($details->cashback_end, 'Y-m-d');
					
                    DB::table(Config::get('tables.CASHBACK_SETTINGS'))
                            ->where('supplier_id', $details->supplier_id)
                            ->update($update_setting);

                    /* DB::table(Config::get('tbl.CASHBACK_OFFFERS'))
                            ->where('supplier_id', $details->supplier_id)
                            ->where('cboffer_type', Config::get('constants.CBOFFER_TYPE.DISCOUNT'))
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->where('bcategory_id', $details->bcategory_id)
                            //->where('status', config('constants.OFF'))
                            ->update($update_offer); */

                    DB::table(Config::get('tables.PROFIT_SHARING'))
                            ->where('sps_id', $id)
                            //->where('store_id', $details->store_id)
                            //->where('bcategory_id', $details->bcategory_id)
                            ->where('sps_id', '!=', $id)
                            ->where('status', '!=', Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'))
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->update($profit_share);

                    //$settings['completed_steps'] = DB::Raw('CONCAT(completed_steps,\',5\')');
                    //DB::table($this->config->get('tbl.MERCHANT_SETTINGS'))->where('supplier_id', $details->supplier_id)->update($settings);
                    DB::commit();
                    return true;
                }
                DB::rollback();
                return false;
            case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED'):
                $query->whereIn('status', [Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'), Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED')]);
                break;
            case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'):
                $query->whereIn('status', [Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'), Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED')]);
                break;
        }
        return $query->update($profit_share);
    }

}
