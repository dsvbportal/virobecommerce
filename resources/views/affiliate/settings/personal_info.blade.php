<form class="form-horizontal" id="addressFrm" action="{{route('aff.settings.address.save',['type'=>$address_type])}}" enctype="multipart/form-data">	
	<div class="form-group">
		<label for="inputEmail" class="col-sm-3 control-label">Flat No/ Street</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="flat_no" name="address[flat_no]" placeholder="Flat No/Street" value="{{$address_details->flatno_street or ''}}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">LandMark</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="address" name="address[landmark]" placeholder="LandMark" value="{{$address_details->landmark or ''}}" >
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Postal Code</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="postal_code" name="address[postal_code]" placeholder="Postal Code" value="{{$address_details->postal_code or ''}}">
		</div>
	</div>
	<div class="form-group cityFld hidden">
		<label class="col-sm-3 control-label">City / Town</label>
		<div class="col-sm-9">
			<select  name="address[city_id]" id="city_id" class="form-control"></select>				
		</div>
	</div>	
	<div class="form-group stateFld hidden">
		<label class="col-sm-3 control-label">State / Region</label>
		<div class="col-sm-9">
			<select  name="address[state_id]" id="state_id" class="form-control"></select>
		</div>
	</div>	
	<div class="form-group">
		<label class="col-sm-3 control-label">Country</label>
		<div class="col-sm-9">
			<select  name="address[country_id]" id="country_id" class="form-control" disabled >
				<option value="{{$userSess->country_id}}">{{$userSess->country}}</option>
			</select>
		</div>
	</div>	
</form>