    <div class="row">														
					<div class="col-sm-12">
								<div class="panel panel-default" id="list">
									<div class="panel-heading">
                                       <button class="btn btn-danger btn-sm close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
									   <h4 class="panel-title">Change Mobile</h4>
                                     </div>
									 <div class="panel-body">
											<div class="col-sm-12">
												<form action="{{route('admin.account.update_mobile')}}" method="post" class="form-horizontal form-bordered" id="change_mobile_form" autocomplete="off"  novalidate="novalidate" >
												    <input type="hidden" class="form-control" id="uname_mobile" value="">
												    <input type="hidden" class="form-control" id="account_id_mobile" value="">
													<fieldset>
														<div class="form-group">
															<label class="col-sm-4 control-label" for="">{{trans('admin/affiliate/admin.current_mobile')}}</label>
															<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
															   	<strong class="" id="old_mobile"></strong>
															</div>
														</div>
														
														<div class="form-group">
															 <label class="control-label col-sm-4">{{trans('admin/affiliate/admin.new_mobile')}}</label>
															<div class="col-sm-3">
                                                          <input type="hidden" id="old_no" name="old_no" class="form-control" value="" required="">
			                                              <input type="text" id="mobile" name="mobile" class="form-control valid" value="" placeholder="Enter New Mobile Number">
															 </div>
														   </div>
														<div class="form-group">
															<label class="control-label col-sm-4">&nbsp;</label>
															<div class="col-sm-3 fieldgroup">
						                               <button  id="update_member_mobile" class="btn btn-primary"> {{trans('admin/general.update')}}
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