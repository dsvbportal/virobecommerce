@extends('ecom.layouts.account_layout')
@section('pagetitle')
CHANGE PASSWORD
@stop
@section('contents')
<div class="contentpanel">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">    
					<p style="margin-left:40px;"><i class="fa fa-info-circle"></i> It's a good idea to use a strong password that you don't use elsewhere.</p><br>
					<form class="form-horizontal" id="change_pwdfrm" action="{{route('ecom.account.update-pwd')}}" method="post" autocomplete="off">
						<div class="form-group">					
							<label class="col-sm-3 control-label">Current Password<span class="red">*</span></label>
							<div class="col-sm-6">
								<div class="input-group">
									<input {!!build_attribute($cpfields ['current_password']['attr'])!!} type="password" id="current_pwd" placeholder="Current Password" class="form-control" value="" data-err-msg-to="#current_pwd_err" onkeypress="return RestrictSpace(event)">
									<span class="input-group-addon pwdHS" data-target="#current_pwd"><i class="fa fa-eye-slash"></i></span>
								</div>
								<span id="current_pwd_err"></span>
							</div>
						</div>
						<div class="form-group">						
							<label class="col-sm-3 control-label">New Password<span class="red">*</span></label>		
							<div class="col-sm-6">		
                                <div class="input-group">							
									<input {!!build_attribute($cpfields['password']['attr'])!!} type="password" id="new_password" placeholder="New Password" class="form-control" value="" data-err-msg-to="#new_password_err" onkeypress="return RestrictSpace(event)">
									<span class="input-group-addon pwdHS" data-target="#new_password"><i class="fa fa-eye-slash"></i></span>
								</div>
								<span id="new_password_err"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="new_password_confirmation">Confirm Password<span class="red">*</span></label>
							<div class="col-sm-6">	
                                <div class="input-group">								
									<input {!!build_attribute($cpfields['conf_password']['attr'])!!} type="password" id="conf_password" placeholder="Confirm Password" class="form-control" value="" data-err-msg-to="#conf_password_err" onkeypress="return RestrictSpace(event)">
									<span class="input-group-addon pwdHS" data-target="#conf_password"><i class="fa fa-eye-slash"></i></span>
								</div>
                                <span id="conf_password_err"></span>								
							</div>			
						</div>
						<div class="form-group">
							<label class="col-sm-3"></label>
							<div class="col-sm-3 fieldgroup">
								<button type="submit" class="btn btn-primary" id="submit" name="submit"><i class="fa fa-save"></i> Update</button>
							</div>
						</div>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<!-- script src="{{asset('validate/lang/login')}}" charset="utf-8"></script-->
<script type="text/javascript" src="{{asset('js/providers/ecom/account/security_settings.js')}}"></script> 
@stop