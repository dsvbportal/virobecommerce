<div class="row">														
		<div class="col-sm-12">
			<div class="box-header with-border">
				<div class="panel panel-default">
				   <div class="panel-heading">
							<button class="btn btn-danger btn-xs  close_btn pull-right">  <i class="fa fa-times"></i> Close</button>
							  <h4 class="panel-title"> {{trans('franchisee/user/create_user.edit_details')}}</h4>
						</div>
			 <div class="panel-body">
				<div class="col-sm-6">
					<form class="form-horizontal form-bordered" id="user_updatefrm" action="{{route('fr.user.update_details')}}" method="post" novalidate="novalidate" autocomplete="off">
					  
				<!--	   <input type="hidden" name="uname_aff" id="uname_aff" class="form-control" value="">-->
					   <input type="hidden" name="account_id" id="account_id" class="form-control" value="{{$user_info->account_id or ''}}">
										<fieldset>
							  <div class="form-group">
									  <label for="textfield" class="control-label col-sm-3">{{$fieldValitator['firstname']['label']}}</label>
									  <div class="col-sm-8">
										<input type="text" id="first_name" class="form-control" {!!build_attribute($fieldValitator['firstname']['attr'])!!} value="{{$user_info->firstname or ''}}">
									  </div>
								</div>
							     <div class="form-group">
										 <label for="textfield" class="control-label col-sm-3">{{$fieldValitator['lastname']['label']}}</label>
										<div class="col-sm-8">
										   <input type="text" id="last_name" class="form-control" {!!build_attribute($fieldValitator['lastname']['attr'])!!}  value="{{$user_info->lastname or ''}}" >
										 </div>
									 </div>
			               <!--    <div class="form-group">
										 <label for="textfield" class="control-label col-sm-3">{{$fieldValitator['dob']['label']}}</label>
										<div class="col-sm-8">
										<div class="input-group">
										<input type="text" id="dob" class="form-control datepicker" placeholder="DOB" value="{{$user_info->dob or ''}}" {!!build_attribute($fieldValitator['dob']['attr'])!!}>
								<span class="input-group-addon"><i class="fa fa-calendar form-control-feedback"></i></span>
									</div>
									 <span id="dob-err"></span>
								     </div>
							</div> -->
						<div class="form-group">
                             <label for="textfield" class="control-label col-sm-3">{{$fieldValitator['dob']['label']}}</label>
                            <div class="col-sm-8">
                            <select style="width:32.5%; display:inline-block"  name="dob_year" id="dob_year" class="form-control">
									<option value="">Year</option>
								</select>
								<select style="width:32.5%;display:inline-block" name="dob_month" id="dob_month" class="form-control">
									<option value="">Month</option>
								</select>
								<select style="width:32%;display:inline-block" name="dob_day" id="dob_day" class="form-control">
									<option value="">Day</option>
								</select>
								<div id="doberrors"></div>
								<input type="hidden" required="" readonly="" name="dob" class="input-text full-width" placeholder="MM/DD/YYYY" id="dob" value="">
                               </div>
                             </div>	 
									<div class="form-group">
										 <label for="textfield" class="control-label col-sm-3">{{$fieldValitator['gender']['label']}}</label>
									     <div class="col-sm-8">
											 <select  id="gender" {!!build_attribute($fieldValitator['gender']['attr'])!!} class="form-control" >
												<option value="">Select</option>
												@if(!empty($genders))
												@foreach ($genders as $g)
												<option value="{{$g->gender_id}}"{{(isset($user_info->gender) && $user_info->gender == $g->gender_id) ? 'selected':''}}>{{$g->gender}}</option>
												@endforeach
												@endif
											</select>
							
										 </label>
									   </div>
									 </div>
				
										<div class="form-group">
										<label for="textfield" class="control-label col-sm-3">{{$fieldValitator['email']['label']}}</label>
										<div class="col-sm-8">
											<input id="email" {!!build_attribute($fieldValitator['email']['attr'])!!} class="form-control"value="{{$user_info->email or ''}}"  />
										</div>
										 </div>
									   <div class="form-group">
										  <label for="textfield" class="control-label col-sm-3">{{$fieldValitator['mobile']['label']}}</label>
											<div class="col-sm-8">
											  <div class="input-group">
												<span class="input-group-addon country-phonecode">{{!empty($user_info->phonecode)?$user_info->phonecode:''}}</span>
												<input id="mobile"  {!!build_attribute($fieldValitator['mobile']['attr'])!!}  class="form-control" value="{{$user_info->mobile or ''}}" data-err-msg-to="#mobile-err" />
												</div>
											<span id="mobile-err"></span>
												 </div>
											   </div>		
                                      <div class="form-group">
										<div class="col-sm-8">
											<input type="hidden" name="country" id="country"  value="{{isset($user_info->country_id)? $user_info->country_id:''}}" />									
										     </div>
							                </div>
													<div class="form-group">
														  <label for="textfield" class="control-label col-sm-3"></label>
														<div class="col-sm-3">
													  <input type="submit" class="btn btn-success btn-block"  value="Update"/>
												   </div>
												 </div>
											 </fieldset>   
											</form>	
										</div>	
										<div class="col-sm-6" id="addrss_list">
										<strong class="text-right" width="45%"> Address</strong>
							          <p><i class="fa fa-map-marker"></i> <span id="franchiseeAddr"><?php echo isset($user_info)?  $user_info->address:'<span class="text-muted">'.trans('affiliate/account.update_address').'</span>';?></span></p> [<a href="" class="link  editAddressBtn" data-url="{{route('fr.user.address',['type'=>'user','account_id'=>$user_info->account_id])}}" data-heading="Channel Partner Address"> <i class="fa fa-edit"></i> Edit</a> ]
										</div>
									 </div>
								   </div>
								 </div>
							   </div>
							   </div>
						
	<div class="modal modal-primary fade" id="address-model" role="dialog">
	     <div class="modal-dialog">
	  <!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header  modal-info">
			  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			  <h4 class="modal-title"><i class="fa fa-map-marker"></i> <span></span></h4>
			</div>
			<div class="modal-body"></div>   
			
		</div>      
	</div>
</div>