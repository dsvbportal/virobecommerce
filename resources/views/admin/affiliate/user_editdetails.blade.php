  <div class="row">														
			<div class="col-sm-12">
				<div class="panel panel-default" id="list">
						<div class="panel-heading">
                                 <button class="btn btn-danger btn-sm  close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
								   <h4 class="panel-title"> {{trans('admin/affiliate/settings/user_edit.edit_details')}}</h4>
                                </div>
			     <div class="panel-body">
					<div class="col-sm-12">
						<form class="form-horizontal form-bordered" id="user_updatefrm" action="{{route('admin.account.update_details')}}" method="post" novalidate="novalidate" autocomplete="off">
						   <input type="hidden" name="account_id" id="account_id" class="form-control" value="">
						   <input type="hidden" name="uname_aff" id="uname_aff" class="form-control" value="">
											<fieldset>
										<div class="form-group">
												<label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/user_edit.first_name')}} :</label>
													<div class="col-sm-3">
														<input type="text" name="first_name" id="first_name" class="form-control" value="">
															</div>
														</div>
										   <div class="form-group">
											 <label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/user_edit.last_name')}} :</label>
															<div class="col-sm-3">
                                                           <input type="text" name="last_name" id="last_name" class="form-control" value="">
															</div>
														</div>
									<div class="form-group">
											 <label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/user_edit.dob')}} :</label>
											<div class="col-sm-3">
											<div class="input-group">
                                                  <input type="text" name="dob" id="dob" class="form-control datepicker" placeholder="DOB" value="">
												  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													      </div>
													     </div>
														</div>	
								<!--		<div class="form-group">
											  <label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/user_edit.gender')}} :</label>
											 <div class="col-sm-3">
                                                <select name="gender" id="gender" class="form-control">
												<option value="1">Male</option>
												<option value="2">Female</option>
												<option value="3">Transgender</option></select>
													</div>
										</div>	-->
											<div class="form-group">
											  <label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/user_edit.email_id')}} :</label>
												 <div class="col-sm-3">
													  <input type="text" name="email_id" id="email_id" class="form-control"  placeholder="Please Enter the Email"   value="">
												</div>
											</div>
									       <div class="form-group">
											  <label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/user_edit.mobile_no')}} :</label>
											 <div class="col-sm-3">
                                                  <input type="text" name="mobile_no" id="mobile_no" class="form-control"  placeholder="Please Enter the Mobile Number"   value="">
													</div>
											    </div>												
												        <div class="form-group">
															<label class="col-sm-3"> </label>
															<div class="col-sm-3 fieldgroup">
						                                    <button  id="update_member_details" class="btn btn-primary"> {{trans('admin/general.update')}}</button>&nbsp;
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