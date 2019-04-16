<div class="row">
	<div class="col-lg-6 col-xl-6 col-md-12 col-12 col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4>Change Password</h4>
			</div>
			<div class="card-body">
				<form  method="post" id="changepassword" onsubmit="return false;" action="{{route('fr.settings.updatepwd')}}" data-checkpwd="{{route('fr.settings.password_check')}}" autocomplete="off">
					<p><i class="fa fa-info-circle"></i> It's a good idea to use a strong password that you don't use elsewhere</p>
					<input type="hidden" name="_token" id="csrf-token" value="{!! csrf_token() !!}"/>
					<div class="form-group">
						<label for="exampleInputEmail1">{{trans('franchisee/settings/changepwd.current_password')}}</label>
						<div class="input-group">
							<input type="password" id="oldpassword" name="oldpassword" class="form-control"  placeholder="{{trans('franchisee/settings/changepwd.current_password')}}" data-err-msg-to="#oldpassword_err" onkeypress="return RestrictSpace(event)">							
							<!-- span class="input-group-addon curnt_pwd"><i class="fa fa-eye-slash" aria-hidden="true"></i></span-->
							<span class="input-group-btn">
								<button class="btn btn-default pwdHS" type="button" data-target="#oldpassword"><i class="fa fa-eye fa-eye-slash"></i></button>
							</span>
						</div>
						<span class="help-block" id="oldpassword_err"></span>
					</div>
					<div class="row">
						<div class="col-sm-6 form-group">
							<label for="exampleInputPassword1">{{trans('franchisee/settings/changepwd.new_password')}}</label>
							<div class="input-group">
								<input type="password" id="newpassword" name="newpassword" class="form-control" id="newpassword" placeholder="{{trans('franchisee/settings/changepwd.new_password')}}" data-err-msg-to="#newpassword_err" onkeypress="return RestrictSpace(event)" maxlength="16">
								<span class="input-group-btn">
									<button class="btn btn-default pwdHS" type="button" data-target="#newpassword"><i class="fa fa-eye fa-eye-slash"></i></button>
								</span>
							</div>
							<span class="help-block" id="newpassword_err"></span>
						</div>
						<div class=" col-sm-6 form-group">
							<label for="exampleInputPassword2">{{trans('franchisee/settings/changepwd.confirm_password')}}</label>
							<div class="input-group">
								<input type="password" id="confirmpassword" name="confirmpassword" class="form-control" id="exampleInputPassword2"			  placeholder="{{trans('franchisee/settings/changepwd.confirm_password')}}" data-err-msg-to="#confirmpassword_err" onkeypress="return RestrictSpace(event)" maxlength="16">
								<span class="input-group-btn">
									<button class="btn btn-default pwdHS" type="button" data-target="#confirmpassword"><i class="fa fa-eye fa-eye-slash"></i></button>
								</span>
							</div>
							<span class="help-block" id="confirmpassword_err"></span>
						</div>
					</div>    
					<div class="form-group text-right" >
						<button name ="Send" type="submit" id="updatepwd" class="btn btn-sm btn-success"><i class="fa fa-save"></i> {{trans('franchisee/settings/changepwd.update_btn')}}</button>                    
					</div>	
				</form>
				<div style="clear:both"></div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-xl-6 col-md-12 col-12 col-sm-12 change_security_form" style="{{(isset($userSess->has_pin) && $userSess->has_pin)? '' : 'display: none;'}}">
		<div class="card" id="change_pin">
			<div class="card-header">
				<h4 id="change_pin_title">Change Security PIN</h4>
			</div>
			<div class="card-body">			
				<div class="" id="resetspwd">  
					<form  method="post" id="change_security_pin" onsubmit="return false;" action="{{route('fr.settings.securitypin.reset')}}" autocomplete="off">
						<input type="hidden" name="_token" id="csrf-token" value="{!! csrf_token() !!}"/>
						<div class="form-group">
							<label for="exampleInputEmail1">{{trans('franchisee/settings/security_pwd.current_password')}}</label>
							<div class="input-group">
								<input type="password" id="tran_oldpassword" name="tran_oldpassword" class="form-control" id="exampleInputEmail1"	   placeholder="{{trans('franchisee/settings/security_pwd.current_sec_phn')}}" maxlength="4" data-url="{{route('fr.settings.securitypin.verify')}}" onkeypress="return isNumberKey(event)" data-err-msg-to="#tran_oldpassword_err">
								<span class="input-group-btn">
									<button class="btn btn-default pwdHS" data-target="#tran_oldpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
								</span>
							</div>
							<span class="help-block" id="tran_oldpassword_err"></span>
						</div>
						<div class="row">
							<div class="col-sm-6 form-group">									
								<label for="exampleInputPassword1">{{trans('franchisee/settings/security_pwd.new_password')}}</label>
								<div class="input-group">
									<input type="password" id="tran_newpassword" name="tran_newpassword" class="form-control"  maxlength="4" id="exampleInputPassword1" placeholder="{{trans('franchisee/settings/security_pwd.new_security_pin')}}" onkeypress="return isNumberKey(event)" data-err-msg-to="#tran_newpassword_err">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tran_newpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>								
								</div>
								<span class="help-block" id="tran_newpassword_err"></span>									
							</div>						
							<div class="col-sm-6 form-group">
								<label for="exampleInputPassword2">Confirm PIN</label>
								<div class="input-group">
									<input type="password" id="tran_confirmpassword" name="tran_confirmpassword"  maxlength="4" class="form-control" id="exampleInputPassword2" placeholder="Confirm PIN" onkeypress="return isNumberKey(event)" data-err-msg-to="#tran_confirmpassword_err">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#tran_confirmpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>
								<span class="help-block" id="tran_confirmpassword_err"></span>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 link_font">
							<a href="{{route('fr.settings.forgot_security_pin')}}" id="forgot_sec_pwd">{{trans('franchisee/settings/security_pwd.forgot_security_pin')}}</a>
							</div>
							<div class="form-group col-sm-6 text-right">
								<button name ="Send" type="submit" id="update_securitypwd" class="btn btn-sm btn-success"><i class="fa fa-save"></i> {{trans('franchisee/settings/security_pwd.update_btn')}}
								</button>                    
							</div>			
						</div>					
					</form>
				</div>
				<!-- Small boxes (Stat box) -->
				<div class=""  id="forgotspwd_otp" style="display:none"> 						
					<form  method="post" id="forgotspwd_otpfrm" action="{{route('fr.settings.securitypin.forgototp.verify')}}" autocomplete="off">
						<!--p><i class="fa fa-info-circle"></i> Your 6 digit OTP code has been sent to your email address. Please check your inbox.</p-->
						<div class="row">
							<div class="col-sm-8">
								<label for="exampleInputEmail1">{{trans('franchisee/settings/security_pwd.otp')}}</label>
								<div class="input-group input-info">
									<input name="otp" id="otp" class="form-control" maxlength="6" placeholder="OTP" onkeypress="return isNumberKey(event)" data-err-msg-to="#otp_err">
									<span class="input-group-btn">
									   <button class="btn btn-primary" type="submit"  id="verify_otp">Continue</button>
									</span>
								</div>
								<span id="otp_err"></span>
							</div>		
							<div class="form-group col-sm-6 link_font">
								<a href="{{route('fr.settings.forgot_security_pin')}}" id="resent_forgotpin_otp"><i class="fa fa-undo"></i>Resent OTP</a>
							</div>
						</div>
					 </form>						
				   </div>
				   <div class=""  id="newspwd" style="display:none">
						<form  method="post" id="forgotspwd_save" onsubmit="return false;" action="{{route('fr.settings.securitypin.save')}}" autocomplete="off">	
							<div class="form-group">
								<label for="exampleInputPassword2">{{trans('franchisee/settings/security_pwd.new_password')}}</label>
								<div class="input-group">
									<input type="password" name="tran_newpassword" id="forgot_tran_newpassword" class="form-control" id="exampleInputPassword1" maxlength="4"placeholder="{{trans('franchisee/settings/security_pwd.new_password')}}" onkeypress="return isNumberKey(event)" data-err-msg-to="#newpin-err">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#forgot_tran_newpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>	
								<span id="newpin-err"></span>
							</div>
							<div class="form-group">
								<label for="exampleInputPassword2">{{trans('franchisee/settings/security_pwd.confirm_password')}}</label>
								<div class="input-group">
									<input  type="password" name="tran_confirmpassword" id="forgot_tran_confirmpassword" class="form-control"  placeholder="{{trans('franchisee/settings/security_pwd.confirm_password')}}" maxlength="4" onkeypress="return isNumberKey(event)" data-err-msg-to="#conformpin-err" type="pa">
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#forgot_tran_confirmpassword" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>	
								<span id="conformpin-err"></span>
							</div>
							<div class="form-group text-right">							
								<button name ="Send" type="submit" id="forgotspwd_savebtn" class="btn btn-sm btn-success"><i class="fa fa-save"></i> {{trans('franchisee/settings/security_pwd.update_btn')}}
								</button>  
							</div>
						</form>						
					</div> 
				 </div>
			   </div>
			 </div>
				<!-- Small boxes (Stat box) -->
		@if(!isset($userSess->has_pin) || (isset($userSess->has_pin) && !$userSess->has_pin))
	  	<div class="col-sm-6 create_security_form" style="{{(isset($userSess->trans_pass_key) && !empty($userSess->trans_pass_key))?'display: none;':''}}">
			<div class="card">
			      <div class="card-header">
				       <h4 id="change_pin_title">Security PIN</h4>
			        </div>
					<div class="card-body">	
					<form  method="post" id="create_security_pin" onsubmit="return false;" action="{{route('fr.settings.securitypin.create')}}" autocomplete="off">
						<div class="col-sm-8 form-group">
								<label for="new_security_pin">{{trans('franchisee/settings/security_pwd.create_pin')}}</label>
								<div class="input-group">
									<input type="password" id="new_security_pin" name="new_security_pin" class="form-control"  placeholder="{{trans('franchisee/settings/security_pwd.create_pin')}}" maxlength="4" onkeypress="return isNumberKey(event)" data-err-msg-to="#new_security_pin_err">
									
									<span class="input-group-btn">
										<button class="btn btn-default pwdHS" data-target="#new_security_pin" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
									</span>
								</div>
								<span class="help-block" id="new_security_pin_err"></span>
							</div>
						 <div class="col-sm-8 form-group">
									<label for="confirm_security_pin">{{trans('franchisee/settings/security_pwd.confirm_pin')}}</label>
									<div class="input-group">
										<input type="password" id="confirm_security_pin" name="confirm_security_pin"  maxlength="4" class="form-control" placeholder="{{trans('franchisee/settings/security_pwd.confirm_pin')}}" onkeypress="return isNumberKey(event)" data-err-msg-to="#confirm_security_pin_err">
										
										<span class="input-group-btn">
											<button class="btn btn-default pwdHS" data-target="#confirm_security_pin" type="button"><i class="fa fa-eye fa-eye-slash"></i></button>
										</span>

									</div>
									<span class="help-block" id="confirm_security_pin_err"></span>
								</div>
						     <div class="row">
								<div class="form-group col-sm-8 text-right">
									<button name ="Send" type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> {{trans('franchisee/settings/security_pwd.save_btn')}}
									</button>                    
						   </div>	
						</div>	
					</form>			
				  </div>		
			  </div>		
			</div>	
	   @endif			
	     </div>	
