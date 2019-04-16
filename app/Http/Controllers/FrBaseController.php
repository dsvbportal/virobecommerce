<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Config;
use App\Models\Commonsettings;
use App\Models\CommonModel;
use Request;
use View;

class FrBaseController extends Controller
{
	public $response = '';
	public $request = '';
	public $redirect = '';
	public $session = '';
	
	public $appSettings = '';
    public $settingsObj = '';
    public $statusCode = '';
    public $headers = [];
    public $op = [];
    public $geo = [];
    public $options = JSON_PRETTY_PRINT;
    public $userSess = null;
    public $sessionName = 'frdata';	
    public $data = array();  
    //public $options = JSON_NUMERIC_CHECK;	
	public $config = [];
	
    public function __construct ()
    {
		$this->request = request();
		$this->response = response();
		$this->redirect = redirect();
		$this->config = config();
		$this->session = session();		
		$this->geo = $this->session->has('geo') ? $this->session->get('geo') : (object) ['current'=>(object) ['address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>77, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'', 'currency_id'=>2, 'country'=>'India', 'country_code'=>'IN'], 'browse'=>(object) ['address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>77, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'', 'currency_id'=>2, 'country'=>'India', 'country_code'=>'IN']];
		$this->siteConfig = $this->config->get('settings');		
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $this->account_id = null;
        $this->device_log_id = Config::get('device_log')->device_log_id;
        $this->token = Config::get('device_log')->token;
        $this->headers['X-Device-Token'] = $this->token;		
		$this->headers['X-Route-Selection-Time'] = round((microtime(true) - LARAVEL_START) * 1000, 3).' ms';
        $this->commonstObj = new Commonsettings();
		$this->commonObj = new CommonModel();
        $this->pagesettings = (object) Config::get('site_settings');				
        $this->currency_id = $this->pagesettings->site_currency_id;      
        $this->pagesettings->account_type = Config::get('account_type');
        $this->country_id = 77;
        $this->state_id = null;
        $this->region_id = null;
        $this->city_id = null;
        $this->postal_code = null;
        if ($this->session->has('frdata'))			
        {	
            $this->config->set('data.franchisee',$this->session->get('frdata'));
			$user_details = $this->config->get('data.franchisee');				

            if (!empty($user_details))
            {		
				$user_details = (object)$user_details;
				$this->acc_type_id = $this->account_type_id = $user_details->account_type_id;
                $this->account_id = $user_details->account_id;
                $this->uname = $user_details->uname;
                $this->full_name = $user_details->full_name;
                $this->email = $user_details->email;
                $this->mobile = $user_details->mobile;
                $this->language_id = $user_details->language_id;
                //$this->time_zone_id = $user_details->time_zone_id;
                $this->currency_id = $user_details->currency_id;                
                $this->is_mobile_verified = $user_details->is_mobile_verified;
                $this->is_email_verified = $user_details->is_email_verified;               		
				
                $this->user_details = $this->userSess = $user_details;
				
                if (!empty($this->userSess))
                {

                    View::share('logged_userinfo', $user_details);
					View::share('userSess', $this->userSess);
                }								
            }
        }		
		View::share('pagesettings', $this->pagesettings);
		View::share('siteConfig', $this->pagesettings);		
        if (Request::isMethod('get'))
        {            
            View::share('device_log', Config::get('device_log'));            
        }
		
        if (Config::has('data.pincode'))
        {
            $location_info = $this->commonstObj->checkPincode();
            if (!empty($location_info))
            {
                $this->country_id = $location_info->country_id;
                $this->state_id = $location_info->state_id;
                $this->region_id = $location_info->region_id;
                $this->city_id = $location_info->city_id;
                $this->postal_code = $location_info->pincode;
            }
        }
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout ()
    {
        /* if (!is_null($this->layout))
          {
          $this->layout = View::make($this->layout, $this->data);
          } */
    }

    public function upload_file ($file, $destinationPath, $filename = '')
    {
        $year = date('Y');
        $path = base_path().$destinationPath;
        if (empty($filename))
        {
            $filename = $file->getClientOriginalName();
        }
        if (!is_dir($path))
        {
            mkdir($path, 0777, true);
        }
        if (!is_dir($path.$year))
        {
            mkdir($path.$year, 0777);
        }
        $file->move($path.$year.'/', $filename);
        return $year.'/'.$filename;
    }

    public function limit_words ($string, $word_limit)
    {
        $strip = strip_tags($string);
        $words = explode(' ', $strip);
        if (count($words) >= $word_limit)
        {
            return implode(' ', array_splice($words, 0, $word_limit)).'...';
        }
        else
            return $string;
    }

    public function slug ($text)
    {
        //replace non letter or digits by (_)
        $text = preg_replace('/\W|_/', '_', $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, '_')); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }
}
