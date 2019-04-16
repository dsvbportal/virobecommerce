 <div class="row">														
				<div class="col-sm-12">
								<div class="panel panel-default" id="list">
								    <div class="panel-heading">
                                         <button class="btn btn-danger btn-sm  close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
										 <h4 class="panel-title">{{trans('admin/affiliate/settings/changepwd.login_password')}}</h4>
                                         </div>
									    <div class="panel-body">
											<div class="col-sm-12">
										       <form class="form-horizontal form-bordered" action="{{route('admin.account.updatepwd')}}" id="update_member_pwdfrm" method="post" novalidate="novalidate" autocomplete="off">
													<fieldset>
													 <div class="form-group">
															<label class="col-sm-3 control-label"></label>
															<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
															   <input type="hidden" class="text-muted" id="uname_label" ></p>
															   <input type="hidden" class="text-muted" id="uname_affiliate" ></p>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/changepwd.fullname')}}:</label>
															<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
															  <strong class="" id="fullname_label" ></strong>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/changepwd.new_password')}} :</label>
														<div class="col-sm-3">
														<div class="input-group">
                                                           <input type="password" name="new_pwd" id="new_pwd" class="form-control" value=""  placeholder="Enter the New Password"  data-err-msg-to="#new_pwd_err">
														   <span class="input-group-addon pwdHS"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
															</div>
															<span id="new_pwd_err" for="" class=""></span>
															</div>
														</div>
								
														<div class="form-group">
															<label class="col-sm-3"> </label>
															<div class="col-sm-3 fieldgroup">
																 <button  id="update_member_pwd" class="btn btn-primary"> {{trans('admin/general.update')}}
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