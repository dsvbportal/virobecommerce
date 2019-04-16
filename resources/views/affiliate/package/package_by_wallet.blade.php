<div class="paymode" id="walletinfo" style="display:none">
    <div class="panel panel-default">
        <div class="panel-heading">
			<div class="box-tools pull-right">
				
			</div>
			<h4 class="panel-title"><i class="fa fa-edit"></i> <span>{{trans('affiliate/package/purchase.paymode_wallet')}}</span></h4>
		</div>
        <div class="panel-body">
            <div class="row form-group hidden">
                <label class="col-sm-3 control-label">{{trans('affiliate/package/purchase.label_wallet')}}:</label>
                <div class="col-sm-4">
                    <select name="wallet_id" id="wallet_id" class="form-control">
                    </select>                    
                </div>
            </div>
            <div class="row form-group">
                <label class="col-sm-3 control-label">{{trans('affiliate/package/purchase.label_curbal')}} :</label>
                <div class="col-md-8 balinfo">   	
                    <span class="usrbal text-success">{{isset($preBalance)? $preBalance:0}}</span>
                </div>
            </div>
            <div class="row form-group dedbalinfo">
                <label class="col-sm-3 control-label">{{trans('affiliate/package/purchase.label_dedbal')}} : <br><small class="text-muted">{{trans('affiliate/package/purchase.ded_bal_notes')}}</small></label>
                <div class="col-md-8 ">                	
                    <span class="usrbal text-success">{{isset($postBalance)? $postBalance:0}}</span>
                </div>
            </div>
            <div class="row form-group">
				<div class="col-sm-3 text-right">
				<button type="button" class="btn btn-default btn-sm backto_paymodebtn"><i class="fa fa-arrow-left"></i> Choose Paymode</button>
				</div>
				@if($hasBalance)
                <div class="col-sm-9">
                   <button class="btn btn-sm btn-primary"  name="purchasebtn" id="purchasebyWbtn" data-ptype="wallet" data-url="{{route('aff.package.purchaseconfirm',['paymode'=>config('constants.PAYMENT_TYPES.WALLET')])}}">{{trans('affiliate/package/purchase.paynow_btn')}} <i class="fa fa-angle-right"></i></button>                   
                </div>
				@endif
            </div>
        </div>
    </div>
</div>