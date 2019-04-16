<form class="form-horizontal" id="addressFrm" action="{{route('fr.settings.address.save',['type'=>$address_type])}}" enctype="multipart/form-data">	
	<div class="form-group">
		<label for="inputEmail" class="col-sm-3 control-label">Address<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="flat_no" name="address[flat_no]" placeholder="Flat No/Street" value="{{$addresstype['address']->flatno_street or ''}}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">LandMark</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="address" name="address[landmark]" placeholder="LandMark" value="{{$addresstype['address']->landmark or ''}}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Postal Code<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="postal_code" name="address[postal_code]" placeholder="Postal Code" value="{{$addresstype['address']->postal_code or ''}}">
		</div>
	</div>
	<div class="form-group cityFld hidden">
		<label class="col-sm-3 control-label">City / Town<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<select  name="address[city_id]" id="city_id" class="form-control" data-selected="{{$addresstype['address']->city_id or ''}}"></select>				
		</div>
	</div>	
	<div class="form-group stateFld hidden">
		<label class="col-sm-3 control-label">State / Region<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<select  name="address[state_id]" id="state_id" class="form-control" data-selected="{{$personaladdRes['address']->state_id or ''}}"></select>
		</div>
	</div>	
	<div class="form-group">
		<label class="col-sm-3 control-label">Country<span class="text-danger">*</span></label>
		<div class="col-sm-9">
			<select  name="address[country_id]" id="country_id" class="form-control" disabled >
				<option value="{{$userSess->country_id}}">{{$userSess->country}}</option>
			</select>
		</div>
	</div>	
</form>