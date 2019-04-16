@extends('shopping.layout.simple_layout')
@section('content')
 <!-- ('ecom.layouts.layout') ('page-content') -->
<div class="page-content pt-50">   
	<div class="panel panel-default" style="margin:auto;width:70%;">	    
		<div class="panel-body" id="reset_password" {!!($pwd_resetfrm)? '' : 'style=display:none;'!!}>   
			<div class="panel-heading" style="margin-bottom: 18px;">
				<h3 id="panel-title" class="panel-title">Change New Password</h3>		
			</div>
			<form id="password-resetfrm" class="form-horizontal profile" action="{{route('ecom.reset_pwd')}}" method="post">				
				<h6 style="padding:2px 20px;"><b>Hello {{(isset($full_name) && !empty($full_name)) ? $full_name :''}},</b></h6>
				<h6 style="padding:2px 20px;"><span class="text-muted">Enter new password for your account </span>'{{(isset($email) && !empty($email))?$email:''}}'</h6>				
				<div class="form-group">
					<input name="restoken" id="restoken" class="" required="1" data-valuemissing="Token is required." type="hidden" value="{{(isset($token) && !empty($token))? $token:''}}" data-err-msg-to="#err_msg">
				</div>				
				<div class="form-group">
					<label for="newpwd" class="col-sm-3 control-label">New Password<span class="text-danger"> *</span></label>
					<div class="col-sm-4"> 
						<div class="input-group">   <!-- pattern="/^\S{6,20}$/" data-patternmismatch="New Password must 6 to 20 characters"-->
							<input {!!build_attribute($rpfields['newpwd']['attr'])!!} id="newpwd" title="New Password" placeholder="New Password" is_editable="0" is_visible="0" type="password" class="form-control" data-err-msg-to="#newpwd_err">
							<span class="input-group-addon pwdHS" data-target="#newpwd" ><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
						</div>
						<span id="newpwd_err"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="conf_newpwd" class="col-sm-3 control-label">Confirm New Password<span class="text-danger"> *</span></label>
					<div class="col-sm-4">
						<div class="input-group">  
							<input {!!build_attribute($rpfields['conf_newpwd']['attr'])!!} id="conf_newpwd" title="Confirm New Password" placeholder="Confirm New Password" is_editable="0" is_visible="0" type="password" class="form-control" data-err-msg-to="#confirm_newpwd_err">
							<span class="input-group-addon pwdHS" data-target="#conf_newpwd" ><i class="old_pin fa fa-eye-slash" aria-hidden="true"></i></span>
						</div>
						<span id="confirm_newpwd_err"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-4">
						<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Reset Password</button>
					</div>
				</div>
			</form>  					
		</div>
		<div class="panel-body" {!!($pwd_resetfrm)? 'style=display:none;' : ''!!}>   
			<div class="page-title">
				<h3 class="text-danger"><i class="fa fa-warning"></i> Sorry! </h3>
			</div>
			<div class="page-body">{{$msg}}</div>  
		</div>   
	</div>       
</div>
@stop