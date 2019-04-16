					
							
									
 <div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-body" id="account_address" data-url="{{route('ecom.account.address')}}" data-pincode-url="{{route('ecom.account.check-pincode')}}">
		<div class="col-md-12 mt-30">
			
			<!-- No address -->
			<div class="text-center" id="new_addr" style="{{(isset($billingAddr) && empty($billingAddr->address) && isset($shippingAddr) && empty($shippingAddr->address)) ? '':'display:none;'}}">
				<h3>No Address found in your account</h3>
				<p>Add a Delivery Address</p>
				<button class="btn btn-primary" id="add_address" data-heading="Add Address"><i class="fa fa-plus"></i> Add Address</button>										
			</div>	
			<!-- address details -->						
			<div class=""  id="offc-info">	
				<table class="table table-dark-bordered table-dark-striped" id="billing_addr" style="{{(!empty($billingAddr->address) || !empty($shippingAddr->address) ) ? '':'display:none;'}}">
					<tr><th>
					<a href="#" class="btn btn-primary btn-xs pull-right editAddressBtn" data-type="{{config('constants.ADDRESS_TYPE.PRIMARY')}}" data-heading="Billing Address" style="{{(isset($billingAddr->address) && !empty($billingAddr->address)) ? '':'display:none;'}}"><i class="fa fa-edit"></i> Edit</a>
					
					<a href="#" class="btn btn-primary btn-xs pull-right addAddressBtn" data-type="{{config('constants.ADDRESS_TYPE.PRIMARY')}}" data-heading="Billing Address" style="{{(!isset($billingAddr->address) && empty($billingAddr->address)) ? '':'display:none;'}}"><i class="fa fa-plus"></i> Add</a>
					<b>Home Address</b></th></tr>
					<tr><td>
					<span><i class="fa fa-map-marker"></i></span> <span id="billingAddr">{{(isset($billingAddr->address)) ? $billingAddr->address:'Update Billing Address'}}</span>
					</td></tr>
				</table>							
				<table class="table table-dark-bordered table-dark-striped" id="shipping_addr" style="{{(!empty($shippingAddr->address) || !empty($billingAddr->address)) ? '':'display:none;'}}">
					<tr><th>
					<a href="" class="btn btn-primary btn-xs pull-right editAddressBtn" data-type="{{config('constants.ADDRESS_TYPE.SHIPPING')}}"  data-heading="Shipping Address" style="{{(isset($shippingAddr->address) && !empty($shippingAddr->address)) ? '':'display:none;'}}"><i class="fa fa-edit"></i> Edit</a>
					
					<a href="" class="btn btn-primary btn-xs pull-right addAddressBtn" data-type="{{config('constants.ADDRESS_TYPE.SHIPPING')}}"  data-heading="Shipping Address"  style="{{(!isset($shippingAddr->address) && empty($shippingAddr->address)) ? '':'display:none;'}}"><i class="fa fa-plus"></i> Add</a>
					<b>Office/Commercial Address</b></th></tr>					
					<tr><td>
					<span><i class="fa fa-map-marker"></i></span> <span id="shippingAddr">{{(isset($shippingAddr->address)) ? $shippingAddr->address:'Update Shipping Address'}}</span>
					</td></tr>
				</table>   
			</div>	
			<!-- address information -->
		</div>
		</div>
	</div>
</div>	
<!-- Address model -->
<div class="modal modal-primary fade" id="address-model" role="dialog">
	<div class="modal-dialog">
	  <!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header  modal-info">
			  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			  <h4 class="modal-title"><i class="fa fa-map-marker"></i> <span></span></h4>
			</div>
			<div class="modal-body">					
			</div>   
			<div class="modal-footer">				
				<button type="submit" id='addressSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Update Address</button>
			</div>
		</div>      
	</div>
</div>