<form class="form-horizontal" id="nomineeFrm" action="{{route('aff.settings.nominee.save')}}">
	<div class="form-group">								
		<label class="col-sm-3 control-label">{{trans('affiliate/signup.full_name')}}</label>
		<div class="col-sm-9">
			<input {!!build_attribute($fields['fullname']['attr'])!!} type="text" name="fullname" id="fullname"  class="form-control" placeholder="Enter Nominee Name" value="{{isset($nominee->fullname)? $nominee->fullname:''}}"/>
		</div>								 
	</div>	
	<div class="form-group">
		<label class="col-sm-3 control-label">{{trans('affiliate/general.gender')}}</label>
		<div class="col-sm-9">
			<select name="gender" id="gender" {!!build_attribute($fields['gender']['attr'])!!}  class="form-control">
				<option value="">Select</option>
				@if(!empty($genders))
				@foreach ($genders as $g)
				<option value="{{$g->gender_id}}" {{(!empty($nominee) && $g->gender_id==$nominee->gender_id)? "selected=selected":''}}>{{$g->gender}}</option>
				@endforeach
				@endif
			</select>
		</div>
	</div>
	<div class="form-group">
		 <label class="col-sm-3 control-label">{{trans('affiliate/general.dob')}}</label>
		 <div class="col-sm-9">
			<select style="width:32.5%; display:inline-block"  name="dob_year" id="dob_year" class="form-control">
				<option value="">Year</option>
			</select>
			<select style="width:32.5%;display:inline-block" name="dob_month" id="dob_month" class="form-control">
				<option value="">Month</option>
			</select>
			<select style="width:33%;display:inline-block" name="dob_day" id="dob_day" class="form-control">
				<option value="">Day</option>
			</select>
			<input type="hidden" {!!build_attribute($fields['dob']['attr'])!!} data-err-msg-to="#doberrors"  name="dob" class="hidden" id="dob" value="">
			<div id="doberrors"></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">{{trans('affiliate/general.relation_ship')}}</label>
		<div class="col-sm-9">			
			<select name="relation_ship_id" id="relation_ship_id"  class="form-control" {!!build_attribute($fields['relation_ship_id']['attr'])!!}>
				<option value="">{{trans('affiliate/general.select')}}</option>
				@if(!empty($relation_ships))
				@foreach ($relation_ships as $r)
				<option value="{{$r->relation_ship_id}}" {{(!empty($nominee) && $r->relation_ship_id==$nominee->relation_ship_id)?"selected=selected":''}}>{{$r->relation_ship}}</option>
				@endforeach
				@endif
			</select>
		</div>
	</div>
</form>