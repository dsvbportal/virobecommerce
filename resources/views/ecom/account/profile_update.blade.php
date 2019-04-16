@extends('ecom.layouts.my_account_layout')
@section('pagetitle')
PERSONAL INFORMATION
@stop
@section('account-content')
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-body">           
            <form class="form-horizontal" id="profile_updatefrm" action="{{route('ecom.account.update')}}" method="post" autocomplete="off" >
                <input name="display_name" type="hidden" id="display_name" placeholder="Display Name" class="form-control" value="{{$logged_userinfo->uname or ''}}">
                <div class="form-group">
					<label class="col-sm-3 control-label">First Name <span class="danger"  style="color: red;" > * </span></label>
					<div class="col-sm-6">
						<input {!!build_attribute($pufields['first_name']['attr'])!!} id="first_name" placeholder="First Name" class="form-control" value="{{$logged_userinfo->first_name or ''}}" onkeypress="return alphaBets(event)">
					</div>	
				</div>	
			    <div class="form-group">
					<label class="col-sm-3 control-label">Last Name <span class="danger"  style="color: red;" > * </span></label>			
                    <div class="col-sm-6">					
					    <input {!!build_attribute($pufields['last_name']['attr'])!!} id="last_name" placeholder="Last Name" class="form-control" value="{{$logged_userinfo->last_name or ''}}" onkeypress="return alphaBets(event)">
				    </div>             
				</div> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Gender <span class="danger" style="color: red;">*</span></label>
                    <div class="col-sm-6 fieldgroup">
                        <select {!!build_attribute($pufields['gender']['attr'])!!} id="gender" class="form-control">
							<option value="">Select</option>							
							@if(!empty($gender))
							@foreach ($gender as $k=>$v)
							<option value="{{$k}}" {{(isset($logged_userinfo->gender) && $k == $logged_userinfo->gender) ? 'selected="selected"': ''}} >{{$v}}</option>
							@endforeach
							@endif
                        </select>                   
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Date of birth <span class="danger" style="color: red;">*</span></label>
                    <div class="col-sm-6 fieldgroup">
					    <div class="input-group">		
                            <input {!!build_attribute($pufields['dob']['attr'])!!} type="text" name="dob" id="dob"  class="form-control datepicker" placeholder="Date of birth" value="{{$logged_userinfo->dob or ''}}" data-err-msg-to="#dob_err"/>    
                            <span class="input-group-addon pwdHS"><i class="fa fa-calendar"></i></span>							
                        </div>
						<span id="dob_err"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"> </label>
                    <div class="col-sm-6 fieldgroup">
                        <button name="submit" type="submit"  id="save_chng" class="btn btn-md btn-primary"><i class="fa fa-save"></i> Save
                        </button>                      
                    </div>
                </div>
            </form>			
        </div>
    </div>
</div>
@stop
@section('scripts')
<!-- script src="{{asset('validate/lang/login')}}" charset="utf-8"></script-->
<script type="text/javascript" src="{{asset('js/providers/ecom/account/profile.js')}}"></script> 
@stop