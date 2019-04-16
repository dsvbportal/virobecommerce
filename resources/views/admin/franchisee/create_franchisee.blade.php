@extends('admin.common.layout')
@section('title','Create Channel Partner')
@section('layoutContent')
<div class="panel panel-default">
    <div class="panel-heading">		
        <h4 class="panel-title">Create New Channel Partner</h4>
     </div>
    <div class="panel-body">
		<div id="msg"></div>
                <!-- form start -->		
                <form action="{{route('admin.franchisee.save')}}" method="POST" class='form-horizontal' id="create_franchisee" onsubmit="return false;">
					<div class="row">
						<div class="col-sm-6">
							<!-- form left -->
							<div class="form-group">
								<div class="col-sm-12">                  
								<select name="fran_type" id="fran_type" class="form-control">
									<option value="">Select a type</option>
									@if(!empty($franchisee_types))
									@foreach($franchisee_types as $franchisee)
									<option value="{{$franchisee->franchisee_typeid}}">{{$franchisee->franchisee_type}}</option>
									@endforeach
									@endif
								</select>                        
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">                        
									<input type="text" name="uname" id="uname" class="form-control"  placeholder="Enter User name" data-url="{{route('admin.franchisee.validate.username')}}" data-rule-required="true" value="" >
									<div id="uname_status"></div>                        
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">                  
								<input type="email" name="email" id="email" class="form-control"  placeholder="Enter Email"  data-url="{{route('admin.franchisee.validate.email')}}" data-rule-required="true" value="" >
								<div id="email_status"></div>                        
								</div>
							</div>						
							<div class="form-group">							
									<div class="col-sm-6"> 
										<input type="password" name="password" id="password" class="form-control"  placeholder="Enter Password"  value="" minlength=6 >
									</div>
									<div class="col-sm-6">
										<input type="password" name="tpin" id="tpin" class="form-control"  placeholder="Enter Security Pin" value="" maxlength=4 >     
									</div>
								
							</div>						
							<div class="form-group">
								<div class="col-sm-12">                       
									<input type="text" name="company_name" id="company_name" class="form-control"  placeholder="Enter Support Center name(Company or Firm Name)" data-rule-required="true" value="" >                        
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">                        
									<select name="country" class="form-control" id="user_country" data-url="{{route('admin.franchisee.states')}}">
										<option value="">--Select Country--</option>
										<?php foreach ($country as $row) { ?>
										<option  data-currency_ids="{{$row->currency_id.':'.$row->currency_code}}" data-phonecode="<?php echo $row->phonecode;?>" value="<?php echo $row->country_id;?>" <?php
										if (isset($user_details) && !empty($user_details))
											if ($user_details->country_id == $row->country_id){
												echo "selected";                                                     
											 }
											 ?>
											 ><?php echo $row->country_name;?>
										</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="textfield" class="control-label col-sm-6 text-right forradio">Desposited Amount:</label>
								<div class="col-sm-6">
									<div class="input-group forpadding1">
										<div id="radioBtn" class="btn-group radioBtn">
											<a class="btn btn-primary btn-sm active" data-toggle="isdeposited"  value="{{Config::get('constants.ON')}}" data-title="{{Config::get('constants.ON')}}">YES</a>
											<a class="btn btn-primary btn-sm notActive" data-toggle="isdeposited"  value="{{Config::get('constants.OFF')}}" data-title="{{Config::get('constants.OFF')}}">NO</a>
										</div>
										<input type="hidden" name="isdeposited" id="isdeposited" value="1">
									</div>	
									<div id="isdeposited_err"></div>
								</div>
							</div>
							<!-- form left -->
						</div>
						<div class="col-sm-6">
							<!-- form right -->
							<div class="form-group addr">
								<div class="col-sm-12">                   
								<input type="text" class="form-control" id="company_address" name="company_address" placeholder="Company (or) Firm address" value="{{$address_details->landmark or ''}}" required>
								</div>
							</div>
							<div class="form-group addr">
								<div class="col-sm-12">	             
									<input type="text" class="form-control" id="landmark" name="landmark" placeholder="LandMark" value="{{$address_details->landmark or ''}}" >
								</div>
							</div>
							<div class="form-group addr">
								<div class="col-sm-12">                       
									<select name="franchisee_state" class="form-control" id="franchisee_state" required data-url="{{route('admin.franchisee.districts')}}">
										<option value="">--Select State--</option>
									</select>                       
								</div>
							</div>
							<div class="form-group addr">
								<div class="col-sm-12">                        
									<select name="franchisee_district" class="form-control" id="franchisee_district" required data-url="{{route('admin.franchisee.cities')}}">
										<option value="">--Select District--</option>
									</select>
									<input type="text" style="display:none" class="form-control" name="franchisee_district_others" id="user_district_others" placeholder = "Enter District Name" />                        
								</div>
							</div>
							<div class="form-group  addr city">    
								<div class="col-sm-12">                   
									<select name="franchisee_city" class="form-control" id="franchisee_city" required>
										<option value="">--Select City--</option>
									</select>
									<input type="text" style="display:none" class="form-control" name="franchisee_city_others" id="franchisee_city_others" placeholder = "Enter City Name" />                        
								</div>
							</div>
							<div class="form-group addr">
								<div class="col-sm-12">                        
								<input type="text" name="franchisee_zipcode" id="franchisee_zipcode" class="form-control"  placeholder="Enter Zip/Pin Code" data-rule-required="true" value="<?php if (isset($user_details) && !empty($user_details)) echo $user_details->zipcode;?>" >
								</div>
							</div>
							<div class="form-group">
								<label for="textfield" class="control-label col-sm-6 text-right forradio">Office Available:</label>
								<div class="col-sm-6">
									<div class="input-group forpadding1">
										<div id="radioBtn" class="btn-group radioBtn">
											<a class="btn btn-primary btn-sm active" data-toggle="office_available"  value="{{Config::get('constants.ON')}}" data-title="{{Config::get('constants.ON')}}">YES</a>
											<a class="btn btn-primary btn-sm notActive" data-toggle="office_available"  value="{{Config::get('constants.OFF')}}" data-title="{{Config::get('constants.OFF')}}">NO</a>
										</div>
										<input type="hidden" name="office_available" id="office_available">
									</div>							
									<div id="office_available_err"></div>							
								</div>
							</div>	
							<!-- form right -->
						</div>
					</div>
					<div class="row">
						
							<div class=" col-md-offset-3 col-md-6 col-md-offset-3">
					         <div class="midltxt txtsize text-center">
								<p>Contact Person Details</p><hr>

							 </div>
							 </div> 
					</div>
					<div class="row">		
						<div class="col-sm-6">
							<!-- form left -->
							<div class="form-group">
								<div class="col-sm-12">                      
								<input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter First name" data-rule-required="true" value="" > 
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">                        
								 <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Enter Last name" data-rule-required="true" value="" >
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">                       
								<select class="form-control" name="gender" id="gender">
									<option value="">Select Gender</option>
									<option value="1">Male</option>
									<option value="2">Female</option>
									<option value="3">Others</option>
								</select>                        
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="dob" id="dob" class="form-control"  placeholder="DOB" value="" >                       
								</div>
							</div>
							<div class="col-sm-12">
								<div class="input-group">
								 <input type="hidden" name="phonecode" id="phonecode" value="" >  
                                <span class="input-group-addon" id="phonecode_label">-</span>
                                 <input class="form-control" type="text" name="mobile" id="mobile" maxlength="16"  placeholder="Enter Mobile" data-rule-required="true" value="<?php if (isset($user_details) && !empty($user_details)) echo $user_details->mobile;?>">
                                
								 </div>
								 <div id="mobile_status"></div>
                            </div>							
							<!-- form left -->
						</div>
						<div class="col-sm-6">
							<!-- form right -->   
							<div class="form-group">
								<div class="col-sm-12">                        
								<input type="text" name="address" id="address" class="form-control"  placeholder="Enter address" data-rule-required="true" value="" >
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
								<input type="text" class="form-control" id="user_landmark" name="user_landmark" placeholder="LandMark" value="" >		              
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">                       
									<select name="state" class="form-control" id="user_state" required data-url="{{route('admin.franchisee.districts')}}">
										<option value="">--Select State--</option>
									</select>                       
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<select name="district" class="form-control" id="user_district" required data-url="{{route('admin.franchisee.cities')}}">
										<option value="">--Select District--</option>
									</select>							
									<input type="text" style="display:none" class="form-control" name="user_district_others" id="user_district_others" placeholder = "Enter District Name" />
								</div>
							</div>
							<div class="form-group city">								
								<div class="col-sm-6">                        
									<select name="city" class="form-control" id="user_city" required>
										<option value="">--Select City--</option>
									</select>
									<input type="text" style="display:none" class="" name="user_city_others" id="user_city_others" placeholder = "Enter City Name" />
								</div>							
								<div class="col-sm-6">                        
									<input type="text" name="zipcode" id="zipcode" class="form-control"  placeholder="Enter Zip/Pin Code" data-rule-required="true" value="<?php if (isset($user_details) && !empty($user_details)) echo $user_details->zipcode;?>" >                       
								</div>								
							</div>
							<!-- form right -->                  
						</div> 
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-sm-12 text-center">
								<input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue" value="<?php echo Config::get('constants.ACTIVE');?>">								
								<input type="submit" name="savebtn" id="savebtn" class="btn btn-primary" value="Save Channel Partner">								
							</div>
						</div>    
					</div>										
                </form>
                <!-- end create User details -->
                <!-- Franchisee access details -->
                <div id="access_form" style="display:none">
                    <br />
                    <div id="franchisee_status"></div>
                    <br />
                    <form action="<?php echo URL::to('admin/franchisee_access');?>" method="POST" class='form-horizontal form-validate' id="franchisee_access"  onsubmit="return false;">
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">Support Center Type:</label>
                            <div class="col-sm-6">
                                <h5><div id="franchi_name"></div></h5>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">Franchiseename:</label>
                            <div class="col-sm-6">
                                <h5><div id="franchi_uname"></div></h5>
                            </div>
                        </div>
                        <div class="form-group country"  style="display:none">
                            <label for="textfield" class="control-label col-sm-2">Country:</label>
                            <div class="col-sm-6">
                                                               
                            </div>
                        </div>
                        <div class="form-group region1" style="display:none">
                            <label for="textfield" class="control-label col-sm-2">Region:</label>
                            <div class="col-sm-6">
                                <select name="region" class="form-control" id="region"  required>
                                    <option value="">--Select Region--</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group state" style="display:none">
                            <label for="textfield" class="control-label col-sm-2">State:</label>
                            <div class="col-sm-6">
                                <select name="state" class="form-control" id="state" required>
                                    <option value="">--Select State--</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group union_territory" style="display:none">
                            <label for="textfield" class="control-label col-sm-2">Union Territory:</label>
                            <div class="col-sm-6">
                                <select name="union_territory[]" class="form-control" id="union_territory" required multiple="multiple">
                                </select>
                            </div>
                        </div>
                        <div class="form-group district"  style="display:none">
                            <label for="textfield" class="control-label col-sm-2">District:</label>
                            <div class="col-sm-6">
                                <select name="district" class="form-control" id="district" required>
                                    <option value="">--District--</option>
                                </select>                                
                                <input type="text" style="display:none" class="form-control" name="district_others" id="district_others" placeholder = "Enter District Name" />
                            </div>
                        </div>
                        <div class="form-group city"  style="display:none">
                            <label for="textfield" class="control-label col-sm-2">City:</label>
                            <div class="col-sm-6">
                                <select name="city" class="form-control" id="city" required>
                                    <option value="">--City--</option>
                                </select>                               
                                <input type="text" style="display:none" class="form-control" name="city_others" id="city_others" placeholder = "Enter City Name" />
                            </div>
                        </div>
                        <div id="franchisee_mapped_user">
                        </div>
                        <input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue"
                               value="<?php echo Config::get('constants.ACTIVE');?>"  >
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                            <div class="col-sm-6" >
                                <input type="submit" name="submit_access" id="submit_access" class="btn btn-primary" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
				<div id="access_edit" data-url="{{route('admin.franchisee.check')}}">
			</div>
	</div>
</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/franchisee/register.js')}}" type="text/javascript" charset="utf-8"></script>
@stop