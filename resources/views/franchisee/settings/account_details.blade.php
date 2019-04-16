<div class="tab-pane" id="affiliate-details">				
        <div class="row">	
		   <div class="col-sm-6">
		   <table class="table table-dark-bordered  table-dark-striped">
		      <tbody>
				<form class="form-verticle" id="profile_editfrm" action="">						
					<div class="col-xs-10 col-sm-4 col-md-4 col-lg-4">
						<h4 class="border"> Primary Contacts</h4>
						<div class="form-group">
							<label for="exampleInputEmail1">Email</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-envelope"></i></span>      
							  <input type="text" class="form-control" name="email" placeholder="Email"   value="{{}}"  disabled>
							  <span class="input-group-addon"><a id="changeEmailBtn" href="{{route('aff.settings.emailverification')}}" title="Change Email"><i class="fa fa-edit"></i></a></span>
							</div>
						</div>
						<div class="form-group">
						<label for="exampleInputPassword1">Mobile/Phone#</label>
						<div class="input-group">      
						  <span class="input-group-addon">{{$userInfo->phonecode}}</span>
						  <input type="text" class="form-control" name="mobile"  placeholder="Mobile Phone"  value="{{$userInfo->mobile}}"  disabled>							  
						 <!-- <span class="input-group-addon"><a id="changeMobileBtn" href="{{route('aff.settings.change_mobile')}}" title="Change Mobile"><i class="fa fa-edit"></i></a></span>-->
					    <span class="input-group-addon"><a id="changeMobileButn" href="{{route('aff.settings.mobileverification')}}" title="Change Mobile"><i class="fa fa-edit"></i></a></span>
						</div>
						</div>
					</div>	
					<div class="col-xs-10 col-sm-4 col-md-4 col-lg-4">												
						<h4 class="border"> Secondary Contacts</h4>
						<div class="form-group">
							<label for="exampleInputEmail1">Home Phone</label>
							<div class="input-group">
								<span class="input-group-addon">{{$userInfo->phonecode}}</span>								
							  <input type="text" class="form-control" name="home_phone" id="home_phone" placeholder="Home Phone" value="{{$userInfo->home_phone}}">
							</div>
						</div>
						<div class="form-group">
							<label for="exampleInputEmail1">Office Phone</label>
							<div class="input-group">
								<span class="input-group-addon">{{$userInfo->phonecode}}</span>
							  <input type="text" class="form-control" name="office_phone"  id="office_phone"  placeholder="Office Phone" value="{{$userInfo->office_phone}}">
							</div>
						</div>
						<button type="buttuon" id='update_contacts' data-url="{{route('aff.settings.update_contacts')}}" class="btn btn-primary"><i class="fa fa-save"></i> Update Contacts</button>
					</div>					
										
					<div class="clearfix"></div>
					<div class="col-xs-10 col-sm-4 col-md-4 col-lg-4">
						<div class="form-group">						
							@if(isset($profileInfo['userdetails']->has_pancard)) 
							<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
								<label for="exampleInputPassword1">PAN Number  <span class="mandatory">*</span></label>
								<input type="text" class="form-control"  name="pan_no" id="pan_no"  value="{{isset($profileInfo['userdetails']->pan_no)? $profileInfo['userdetails']->pan_no:''}}" placeholder="PAN Number" <?php if($userSess->is_verified && !empty($profileInfo['userdetails']->pan_no)) echo "disabled='disabled'";?>>
							</div>
							@endif
						</div>
						<div class="clearfix"></div>
						@if(isset($profileInfo['userdetails']->has_pancard))
						<div class="col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<div class="form-group">
								<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
									<label for="exampleInputPassword1">PAN Number  <span class="mandatory">*</span></label>
									<input type="text" class="form-control"  name="pan_no" id="pan_no"  value="{{isset($profileInfo['userdetails']->pan_no)? $profileInfo['userdetails']->pan_no:''}}" placeholder="PAN Number" <?php if($userSess->is_verified && !empty($profileInfo['userdetails']->pan_no)) echo "disabled='disabled'";?>>
								</div>	
							</div>						
						</div>						
						@endif
					</div>
				</form>
			</div>
		</div>
	</div>
	
	</table>