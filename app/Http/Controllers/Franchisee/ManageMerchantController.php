<?php
namespace App\Http\Controllers\Franchisee;

use App\Http\Controllers\FrBaseController;
use App\Models\Franchisee\ManageMerchant;
use App\Helpers\CommonNotifSettings;
use App\Models\Commonsettings;
use File;
use Storage;
use CommonLib;
use Response;
use Request;
use DB;

class ManageMerchantController extends FrBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->merchantObj 	= new ManageMerchant();
		$this->commonObj = new Commonsettings;
    }
	public function get_merchant_list(){
        $data = $filter = array();
        $postdata = $this->request->all();
        $data['mr_status'] = $postdata['mr_status'] = null;
        $data['route'] = route('fr.merchants.list');
        if (!empty($status))
        {
            $data['mr_status'] = $postdata['mr_status'] = 'Incomplete';
            $data['route'] = route('fr.merchants.list', ['status'=>'new']);
        }
	    if (!empty($postdata))
        {
            $filter['from'] = $this->request->has('from_date') ? $this->request->get('from_date') : null;
            $filter['to'] = $this->request->has('to_date') ? $this->request->get('to_date') : null;
            $filter['country'] = $this->request->has('country') ? $this->request->get('country') : null;
           /*  $filter['bcategory'] = $this->request->has('bcategory') ? $this->request->get('bcategory') : null; */
            $filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : null;
            $filter['filterTerms'] = $this->request->has('filterTerms') ? $this->request->get('filterTerms') : null;
            $filter['mr_status'] = $postdata['mr_status'];
        }
		
        if($this->request->ajax())
        {
            $ajaxdata = [];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->merchantObj->get_merchant_list($data, true);
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : null;
            if ($ajaxdata['recordsTotal'])
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->merchantObj->get_merchant_list($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                    if (isset($postdata['order']))
                    {
                        $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                        $data['order'] = $postdata['order'][0]['dir'];
                    }
                    $ajaxdata['data'] = $this->merchantObj->get_merchant_list($data, false);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else
        {
            $data['country_list'] = $this->merchantObj->country_list();			
            //$data['bcategory_list'] = $this->merchantObj->merchant_filter_bcategory();			
            return view('franchisee.merchants.merchant_list', $data);
        }
	}
	/* Add Merchant Code */
	public function create_Merchant(){	
                 $data = [];	
		         $data['countries'] = $this->commonObj->getCountries(['country_id' =>$this->userSess->country_id]);		
                 $data['fieldValitator'] = CommonNotifSettings::getHTMLValidation('fr.merchants.save',['country'=>$this->userSess->country_id]);
				 $data['phy_locations'] = $this->commonObj->getPhysicalLocations();
		       return view('franchisee.merchants.create_merchants',$data);
		}
		
	   public function save_merchant_old(){
	  
	     $data = $this->request->all();
		 $data['franchisee_id']=$this->userSess->franchisee_id;
		if (!empty($data)) { 
		      $res = $this->merchantObj->saveMerchants_old($data);
              if ($res){
			     $op['msg'] = $res['msg'];
				 $this->statusCode  = $res['status'];
		    }
		 }
		  return $this->response->json($op, $this->statusCode, $this->headers, $this->options);	
	  } 
      public function save_merchant(){
	    $data = [];
		$temp=[];
		if (\Request::ajax())
        {
            $data = $this->request->all();
			$password=rand(100000, 999999);
			$sdata = [];
		    $sdata['buss_name'] = $data['buss_name'];
			$sdata['country'] = $data['country'];
			$sdata['phonecode'] = $data['phonecode'];
			$sdata['service_type'] = $data['service_type'];
			$sdata['phy_locations'] = $data['phy_locations'];
			$sdata['search_form'] = $data['search_form'];
			$sdata['bcategory'] = $data['bcategory'];
			$sdata['account_mst'] = ["mobile"=>$data['mobile'],"email"=>$data['email'],'pass_key'=>md5($password)]; 
			$sdata['account_details'] = ["firstname"=>$data['firstname'],"lastname"=>$data['lastname']];
			$sdata['channelpartner']=['franchisee_id'=>$this->userSess->franchisee_id,'account_id'=>$this->userSess->account_id]; 

            $temp_id = $this->merchantObj->save_temp_supplier_data($sdata);
			if(!empty($temp_id)){
				   $temp['temp_id']=$temp_id;
				   $temp['password']=$password;
			     if($data=$this->verifyEmailID($temp)){
						 $op['msg']=$data['msg'];
						 $op['link']=$data['link'];
						 $this->statusCode = $data['status'];
				}
				else {
					$op['msg'] = trans('franchisee/general.something_wrong');
					$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				}
			}
			else{
				$op['msg'] = trans('franchisee/general.something_wrong');
				$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			}
             return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
          }
		  
	 }
  public function verifyEmailID($temp)
      {
	   extract($temp);
        $op = $info = $completedSteps = [];
        if (!empty($temp_id))
        {
                    $temp_supp_data = $this->merchantObj->get_temp_supplier_data($temp_id);
					$regtoken= $temp_supp_data->regtoken;
					$temp_data=$temp_supp_data->regdata;
                    $temp_data = (array) json_decode($temp_data); 
				
             if($this->Generate_Reg_Token($regtoken)){
				   $op['link'] = $verify_link = $this->Generate_Reg_Token($regtoken);
				   CommonNotifSettings::affNotify('franchisee.merchant',null,config('constants.ACCOUNT_TYPE.SELLER'), ['email_verify_link' => $verify_link, 'email' => $temp_data['account_mst']->email, 'firstname' => $temp_data['account_details']->firstname, 'full_name' => $temp_data['account_details']->firstname,'password'=>$password],true,false);
				   $op['status']=$this->statusCode = $this->config->get('httperr.SUCCESS');
				   $op['msg'] = trans('franchisee/merchant/merchant_details.code_sent_to_email',['email'=>$this->commonstObj->maskEmail($temp_data['account_mst']->email)]);
			       return $op;
	     	}
		  else{
			  return false;
		    }
		  }
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
	 
	 
	/* Merchant Code Ending */
	public function getCategories()
    {
        $wdata = [];
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            if (isset($postdata['pbcat_id']))
            {
                $wdata['pbcat_id'] = $postdata['pbcat_id'];
            }
            if (isset($postdata['excbcat_id']))
            {
                $wdata['excbcat_id'] = $postdata['excbcat_id'];
            }
            if (isset($postdata['cat_id']))
            {
                $wdata['cat_id'] = $postdata['cat_id'];
            }
            if (isset($postdata['excpbcat_id']))
            {
                $wdata['excpbcat_id'] = $postdata['excpbcat_id'];
            }
        }
        $op['data'] = $this->merchantObj->merchantCategories($wdata);
        $op['status'] = $this->statusCode =$this->config->get('httperr.SUCCESS');
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);		        
    }
	public function Upload_KYC_details($mcode){
		$data = [];
		$data['tax_fields'] = CommonNotifSettings::getHTMLValidation('fr.merchants.tax-information');
		$data['gst_fields'] = CommonNotifSettings::getHTMLValidation('fr.merchants.gst-information');
		$data['id_proof'] = $this->commonObj->getDocumentTypes(['proof_type' => 1]);
        $data['address_proof'] = $this->commonObj->getDocumentTypes(['proof_type' => 2]);
	    $data['details'] = $this->merchantObj->get_tax_information(52,3);//($this->userSess->supplier_id, $this->userSess->account_type_id);
		return view('franchisee.merchants.manage_merchant_kyc',$data);
	}
	  public function tax_information()
     {
        $data = [];
        if (Request::ajax())
        {
            $op = [];
            $postdata = $this->request->all();
            $postdata['relative_post_id'] = 52;//$this->userSess->supplier_id
            $postdata['account_type_id'] = 3; // $this->userSess->account_type_id
            if (!empty($postdata['pan_card_upload']) && $path = $this->uploadFile($postdata['pan_card_upload'], 'SELLER.PROOF_DETAILS'))
            {
                $postdata['pan_card_image'] = $path;
            }
            $res = $this->merchantObj->UpdateTax_info($postdata);
            if ($res)
            {
                $this->merchantObj->UpdateCompletedSteps(['current_step' => $this->config->get('constants.ACCOUNT_CREATION_STEPS.TAX_INFO'),
                    'account_type_id' => $this->config->get('constants.ACCOUNT_TYPE.SELLER'),
                    'supplier_id' => 52,//$this->supplier_id
                    'account_id' => 3]); //$this->account_id
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Tax Information Updated Successfully';
                return Response::json($op, $this->statusCode, $this->headers, $this->options);
            }
        }
    } 
	
    public function gst_information()
    {
        $postdata = $this->request->all();
        $postdata['relative_post_id'] = 52;//$this->userSess->supplier_id;
        $postdata['account_type_id'] = 3; //$this->userSess->account_type_id;
        if (!empty($postdata['gstin_image']) && $path = $this->uploadFile($postdata['gstin_image'], 'SELLER.PROOF_DETAILS'))
        {
            $postdata['tax_document_path'] = $path;
        }
        if (!empty($postdata['tan_image']) && $path = $this->uploadFile($postdata['tan_image'], 'SELLER.PROOF_DETAILS'))
        {
            $postdata['tan_image'] = $path;
        }
        if (!empty($postdata['id_image']) && $path = $this->uploadFile($postdata['id_image'], 'SELLER.PROOF_DETAILS'))
        {
            $postdata['id_proof_path'] = $path;
        }
        if (!empty($postdata['address_image']) && $path = $this->uploadFile($postdata['address_image'], 'SELLER.PROOF_DETAILS'))
        {
            $postdata['address_proof_path'] = $path;
        }
        if ($this->merchantObj->UpdateGstInfo($postdata))
        {
            $this->merchantObj->UpdateCompletedSteps([
                'current_step' => $this->config->get('constants.ACCOUNT_CREATION_STEPS.TAX_INFO'),
                'account_type_id' => $this->config->get('constants.ACCOUNT_TYPE.SELLER'),
                'supplier_id' =>52, //$this->supplier_id,
                'account_id' => 3 //$this->account_id
            ]);
            $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
            $op['msg'] = 'Information Updated Successfully';
        }
        else
        {
            $this->statusCode = $op['status'] = $this->config->get('httperr.ALREADY_UPDATED');
            $op['msg'] = 'There is no changes';
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
  public function uploadFile($file, $destinationPathKey)
    {
        $filename = false;
        $folder_path = getGTZ('Y').'/'.getGTZ('m').'/';
        $destinationPath = $this->config->get('path.'.$destinationPathKey.'.LOCAL');
        if (File::exists($destinationPath.getGTZ('Y')))
        {
            if (!File::exists($destinationPath.getGTZ('Y').'/'.getGTZ('m')))
            {
                File::makeDirectory($destinationPath.getGTZ('Y').'/'.getGTZ('m'));
            }
        }
        else
        {
            File::makeDirectory($destinationPath.getGTZ('Y'));
            File::makeDirectory($destinationPath.getGTZ('Y').'/'.getGTZ('m'));
        }
        $filename = getGTZ('dmYHis').$this->slug($file->getClientOriginalName()).'.'.strtolower($file->getClientOriginalExtension());
        return ($file->move($destinationPath.$folder_path, $filename)) ? $folder_path.$filename : false;
    } 
	
}