<form class="form-horizontal" id="addressFrm" action="{{route('fr.user.address.save',['type'=>$address_type])}}" enctype="multipart/form-data">	
<input type="hidden" name="account_id" id="account_id" class="form-control" value="{{$address->account_id or ''}}">
	<div class="form-group">
		<label for="inputEmail" class="col-sm-3 control-label">Address<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="flat_no" name="address[flat_no]" placeholder="Flat No/Street" value="{{$address->flatno_street or ''}}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">LandMark</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="address" name="address[landmark]" placeholder="LandMark" value="{{$address->landmark or ''}}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Postal Code<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="postal_code" name="address[postal_code]" placeholder="Postal Code" value="{{$address->postal_code or ''}}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">State / Region<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<select  name="address[state_id]" id="state" class="form-control" ></select>
				
		</div>
	</div>	
   <div class="form-group">
		 <label class="col-sm-3 control-label">District<span class="text-danger">*</span></label>
		  <div class="col-sm-9">
			 <select  id="district"  class="form-control"  name="address[district_id]" data-url="{{route('fr.user.district')}}">
			 <option value="">Select District</option>	
			</select>
			 </label>
		</div>
		</div>
    <div class="form-group">
		<label class="col-sm-3 control-label">City / Town<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<select  name="address[city_id]" id="city_id" class="form-control" data-url="{{route('fr.user.city')}}">
			</select>				
		</div>
	</div>		
	<div class="form-group">
		<label class="col-sm-3 control-label">Country<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<select  name="address[country_id]" id="country_id" class="form-control">
					<option value="{{$address->country_id}}">{{$address->country}}</option>
					
			</select>
		</div>
	</div>	
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-3">
	    <button type="submit" id='addressSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Update Address</button>
		</div>
		</div>
</form>