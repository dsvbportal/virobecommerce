<div class="row">														
			<div class="col-sm-12">
					  <div class="box-header with-border">
							<div class="panel panel-default">
								<div class="panel-heading">
									 <button class="btn btn-danger btn-xs  close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
									 <h4 class="panel-title">{{trans('franchisee/user/changepwd.reset_password')}}</h4>
									 </div>
									<div class="panel-body">
										<div class="col-sm-12">
										   <form class="form-horizontal form-bordered" action="{{route('fr.user.change-password')}}" id="update_member_pwdfrm" method="post" novalidate="novalidate" autocomplete="off">
												<fieldset>
												 <div class="form-group">
														<label class="col-sm-3 control-label"></label>
														<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
														   <input type="hidden" class="text-muted" id="uname_user"></p>
														   
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">{{trans('franchisee/user/changepwd.fullname')}}:</label>
														<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
														  <strong class="" id="fullname_label" ></strong>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">{{trans('franchisee/user/changepwd.new_password')}} :</label>
													<div class="col-sm-3">
													<div class="input-group">
													   <input type="password" name="new_pwd" id="new_pwd" class="form-control" value=""  placeholder="Enter the New Password"  data-err-msg-to="#new_pwd_err" onkeypress="return RestrictSpace(event)">
													   <span class="input-group-btn">
							                         		<button class="btn btn-default pwdHS" type="button" data-target="#newpassword"><i class="fa fa-eye fa-eye-slash"></i></button>
								                    </span>
														</div>
														<span id="new_pwd_err" for="" class=""></span>
														</div>
													</div>
											
													<div class="form-group">
														<label class="col-sm-3"> </label>
														<div class="col-sm-3 fieldgroup">
															 <button  id="update_member_pwd" class="btn btn-primary"> {{trans('franchisee/general.update')}}
															</button>
														</div>
													</div>
												</fieldset>   
											</form>	
										   </div>	
									   </div>
									</div>
								  </div>
							   </div>
							  </div>