<div class="row">														
		<div class="col-sm-12">
			<div class="panel panel-default" id="list">
					<div class="panel-heading">
							  <button class="btn btn-danger btn-sm  close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
							  <h4 class="panel-title">{{trans('admin/affiliate/admin.reset_pin')}}</h4>
							  </div>
					           <div class="panel-body">
					 			<div class="col-sm-12">
									 <form class="form-horizontal form-bordered" action="{{route('admin.account.updatepin')}}" id="update_member_pinfrm" method="post" novalidate="novalidate" autocomplete="off">
											  <input type="hidden" class="form-control" id="user_name" value="" >
											  <input type="hidden" class="form-control" id="uname_pin" value="" >
											<fieldset>
													<div class="form-group">
														<label class="col-sm-3 control-label">{{trans('admin/affiliate/admin.fullname')}} :</label>
														<div class="col-sm-6" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;">
													     <strong class="" id="fullname_pin" ></strong>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">{{trans('admin/affiliate/admin.new_pin')}} :</label>
														<div class="col-sm-3">
													       <input type="password" name="new_pin" id="new_pin" class="form-control" value=""  placeholder="Enter the New Security Pin">
														</div>
													</div>
													 <div class="form-group">
														<label class="col-sm-3"></label>
														 <div class="col-sm-3 fieldgroup">
														   <button  id="update_member_pin" class="btn btn-primary"> {{trans('admin/general.update')}}
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