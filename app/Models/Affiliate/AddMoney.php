<?php
namespace App\Models\Affiliate;
use App\Models\BaseModel;
use App\Models\Commonsettings;
use DB;
use AppService;

class AddMoney extends BaseModel
{
	
    public function __construct ()
    {
        parent::__construct();
        $this->commonObj = new Commonsettings();
    }

    public function getPaymentTypes (array $arr = array())
    {
        extract($arr);
        $result = DB::table(config('tables.PAYMENT_MODES_LOOKUPS').' as apm')
                ->join(config('tables.PAY_PAYMENT_SETTINGS').' as ps', 'ps.pay_mode', '=', 'apm.paymode_id')
                ->join(config('tables.PAYMENT_TYPES').' as pt', function($pt)
                {
                    $pt->on('pt.payment_type_id', '=', 'ps.payment_type_id');
                    //->where('pt.source_type', '=', $this->config->get('constants.SOURCE_TYPE.OUT'));
                })
                ->where('ps.currency_id', $currency_id)
				->where('ps.country_id', $country_id)
				->where(function($check_amount) use($amount)
				{
					$check_amount->where(function($check_null)
					{
						$check_null->whereNull('min_amount')
						->whereNull('max_amount');
					})
					->orWhere(function($max_null) use($amount)
					{
						$max_null->whereNotNull('min_amount')
						->whereNull('max_amount')
						->where('min_amount', '<=', $amount);
					})
					->orWhere(function($min_null) use($amount)
					{
						$min_null->whereNotNull('max_amount')
						->whereNull('min_amount')
						->where('max_amount', '>=', $amount);
					})
					->orWhere(function($check) use($amount)
					{
						$check->whereNotNull('min_amount')
						->whereNotNull('max_amount')
						->where('min_amount', '<=', $amount)
						->where('max_amount', '>=', $amount);
					});
				})
                ->selectRaw('apm.mode_name as name, pt.payment_key as id, pt.save_card as has_card_ui, apm.logo as icon, apm.is_online as is_pg,pt.payment_type as paymentgateway_name')
                ->get();
        array_walk($result, function(&$p) use($arr)
        {
            $p->icon = asset($this->config->get('constants.PAYMENT_MODE_IMG_PATH.WEB').$p->icon);
            $p->saved_cards = ($p->has_card_ui) ? $this->getStoredCards($arr) : null;
        });
        return $result;
    }

    public function getStoredCards (array $arr = array())
    {
        extract($arr);
        $details = DB::table(config('tables.ACCOUNT_PAYMENT_CARD_SETTINGS').' as pcs')
                ->join(config('tables.PAYMENT_CARD_TYPES').' as pct', 'pct.card_type_id', '=', 'pcs.card_type_id')
                ->where('pcs.is_deleted', $this->config->get('constants.OFF'))
                ->where('pcs.status', $this->config->get('constants.ON'))
                ->where('pcs.account_id', $account_id)
                ->selectRaw('id,display_card_no as card_no,card as card_type,img_path as card_type_img')
                ->get();
        array_walk($details, function(&$d)
        {
            $d->card_type_img = asset($d->card_type_img);
        });
        return !empty($details) ? $details : null;
    }

    public function saveAddMoney ($sdata = array(),$id=0)
    {
		if(!empty($sdata)){
			if (!empty($id))
			{            
				$sdata['updated_on'] = getGTZ();
				$sdata['updated_by'] = $account_id;
				if (DB::table(config('tables.ACCOUNT_ADD_FUND'))
								->where('uaf_id', $id)
								->update($sdata))
				{
					return $id;
				}
			}
			else
			{
				$sdata['requested_date'] = getGTZ();
				return DB::table(config('tables.ACCOUNT_ADD_FUND'))
								->insertGetID($sdata);
			}
		}
        return false;
    }

    public function confirmAddMoney ($uaf_id, $status, $payment_id)
    {
        if ($status == 'CONFIRMED')
        {
            $add_money = DB::table(config('tables.ACCOUNT_ADD_FUND').' as am')
                    ->join(config('tables.APP_PATMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'am.payment_mode_id')
                    ->join(config('tables.PAYMENT_TYPES_LANG').' as ptl', function($ptl)
                    {
                        $ptl->on('ptl.payment_type_id', '=', 'am.payment_type_id')
                        ->where('ptl.lang_id', '=', $this->config->get('app.locale_id'));
                    })
                    ->where('uaf_id', $uaf_id)
                    ->selectRaw('am.uaf_id,am.account_id,am.payment_type_id,am.payment_mode_id,am.currency_id,am.amount,apm.pay_mode,ptl.payment_type')
                    ->first();
            if (!empty($add_money))
            {
                if ($this->updateAccountTransaction([
                            'payment_type_id'=>$add_money->payment_type_id,
                            'pay_mode'=>$add_money->payment_mode_id,
                            'to_account_id'=>$add_money->account_id,
                            'credit_source_account_id'=>$add_money->account_id,
                            'to_wallet_id'=>$this->config->get('constants.WALLET.xpc'),
                            'currency_id'=>$add_money->currency_id,
                            'amt'=>$add_money->amount,
                            'relation_id'=>$add_money->uaf_id,
                            'credit_remark_data'=>['pay_mode'=>$add_money->pay_mode, 'payment_type'=>$add_money->payment_type],
                            'transaction_for'=>'ADD_FUND']))
                {
                    return DB::table(config('tables.ACCOUNT_ADD_FUND'))
                                    ->where('uaf_id', $add_money->uaf_id)
                                    ->update(['updated_on'=>getGTZ(), 'payment_id'=>$payment_id, 'updated_by'=>$add_money->account_id, 'status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.CONFIRMED')]);
                }
            }
        }
        else
        {
            return DB::table(config('tables.ACCOUNT_ADD_FUND'))
                            ->where('uaf_id', $uaf_id)
                            ->update(['updated_on'=>getGTZ(), 'payment_id'=>$payment_id, 'status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.FAILED')]);
        }
        return false;
    }

}
