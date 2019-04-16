<form class="form-horizontal" id="profileUpdateFrm" action="{{route('fr.settings.update_profile')}}" enctype="multipart/form-data">	
	<div class="form-group">
		<label class="col-sm-3 control-label">Account ID</label>
		<div class="col-sm-9">
		    <label class="col-sm-3 control-label">{{$user_code}}</label>			
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Name</label>
		<div class="col-sm-9"> 
		    <label class="col-sm-3 control-label">{{$full_name}}</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Marital Status</label>
		<div class="col-sm-9">
			<select  {!!build_attribute($sfields['marital_status']['attr'])!!} class="form-control" id="marital_status">
                <option value="">-- select --</option> 
				@foreach ($marital_status as $status)
                 <option value="{{$status->marital_status_id}}" {{($status->marital_status_id == $marital ? 'selected = "selected" ' : '')}} >{{$status->marital_status}}</option> 		
				@endforeach			
			</select>				
		</div>
	</div>	
	<div class="form-group">
		<label class="col-sm-3 control-label">Father’s/Husband’s Name</label>
		<div class="col-sm-9">
			<input {!!build_attribute($sfields['gardian']['attr'])!!}  class="form-control" id="gardian" value="{{$gardian or ''}}">
		</div>
	</div>		
</form>