@extends('user.layouts.simple_layout')
@section('page-content')
@section('breadcrumb')
<li><a href="{{route('user.my-profile')}}">Reset Password</a></li>
@stop
<div class="container mar-20-t">
    <div class="content-page" id="reset_password" {!!($pwd_resetfrm)? '' : 'style=display:none;'!!}>
        <div class="page-title">
            <h3><i class="fa fa-undo"></i> Reset Password </h3>
		</div>
        <div class="page-body">
		    <form id="password-resetfrm" class="form-horizontal profile" action="{{route('user.pwdreset-link')}}" method="post">
				<p class="alert alert-info"><i class="fa fa-info-circle"></i> Enter a new password for your account.</p>
				<div class="form-group">
				    <input name="token" id="token" required="1" data-valuemissing="Token is required." type="hidden" value="{{(isset($token) && !empty($token))? $token:''}}">
				</div>				
				<div class="form-group">
					<label for="new_password" class="col-sm-3 control-label">New Password<span class="mandatory"> *</span></label>
					<div class="col-sm-4"> 
						<div class="input-group">   <!-- pattern="/^\S{6,20}$/" data-patternmismatch="New Password must 6 to 20 characters"-->
							<input name="new_password" id="new_password" title="New Password" placeholder="New Password" required="1" data-valuemissing="New Password is required." is_editable="0" is_visible="0" type="password" class="form-control" pattern="\S{6,20}" data-patternmismatch="New Password must at least 6 to 20 characters"data-err-msg-to="#newpwd_err">
							<span class="input-group-addon new_pwd"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
						</div>
						<span id="newpwd_err"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="confirm_password" class="col-sm-3 control-label">Confirm New Password<span class="mandatory"> *</span></label>
					<div class="col-sm-4">
						<div class="input-group">  
							<input name="confirm_password" id="confirm_password" title="Confirm New Password" placeholder="Confirm New Password" required="1" data-valuemissing="Confirm Password is required." is_editable="0" is_visible="0" type="password" class="form-control" pattern="\S{6,20}" data-patternmismatch="Confirm Password must at least 6 to 20 characters" data-err-msg-to="#confirm_newpwd_err">
							<span class="input-group-addon cnfrm_pwd"><i class="old_pin fa fa-eye-slash" aria-hidden="true"></i></span>
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
    </div>
	<div class="content-page" {!!($pwd_resetfrm)? 'style=display:none;' : ''!!}>
        <div class="page-title">
            <h3><i class="fa fa-warning"></i> Sorry! </h3>
		</div>
        <div class="page-body">{{$msg}}</div>  
    </div>
</div>
@stop