  <div class="row">														
			<div class="col-sm-12">
				<div class="panel panel-default" id="list">
						<div class="panel-heading">
						<button class="btn btn-danger btn-sm  close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
                            <h4 class="panel-title">Change Email</h4>
                                  </div>
									 <div class="panel-body">
											<div class="col-sm-12">
												  <form action="{{route('admin.account.email')}}" method="post" class="form-horizontal form-bordered" id="change_email_form" autocomplete="off"  novalidate="novalidate" >
												  <input type="hidden" class="form-control" id="user_name" name="user_name" value="" >
												  <input type="hidden" class="form-control" id="user_account_id" name="user_account_id" value="" >
													<fieldset>
														<div class="form-group">
															<label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/changepwd.current_email')}}</label>
															<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
															    <strong class="" id="old_emails"></strong>
															</div>
														</div>
											
											<div class="form-group">
												<label class="col-sm-3 control-label">{{trans('admin/affiliate/settings/changepwd.new_email')}}</label>
													<div class="col-sm-3">
													 <div class="input-group">
														<a class="input-group-addon" href="#"><i class="fa fa-envelope"></i></a>
                                                            <input type="hidden" id="old_email" name="old_email" class="form-control" value="" required="">
			                                                <input type="text" id="email" name="email" class="form-control valid" value="" placeholder="Enter the New Email">
															</div>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3"> </label>
															<div class="col-sm-3 fieldgroup">
						                              <button  id="update_member_email" class="btn btn-primary"> {{trans('admin/general.update')}}
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