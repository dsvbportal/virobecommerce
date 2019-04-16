	<div id="franchisee_status" style="margin:5px;"></div>                    
			   <form action="{{route('admin.franchisee.access.update')}}" data-updateUrl="{{route('admin.franchisee.access.update')}}" data-saveUrl="{{route('admin.franchisee.access.savenew')}}" method="POST" class='form-horizontal form-validate' id="franchisee_access"  enctype="multipart/form-data" >
			   <div class="col-lg-6">
				   <div class="form-group fld">
						<label for="textfield" class="col-md-4" style="font-weight:bold">Franchisee Name: </label>
						<div class="col-md-8">
							<div id="company_name">{{!empty($company_name)?$company_name:''}}</div>
						</div>
					 </div>
						  <div class="form-group fld">
						<label for="textfield" class="col-sm-4" style="font-weight:bold">Email: </label>
						<div class="col-sm-8">
							<div id="email">{{!empty($email)?$email:''}}</div>
						</div>
					 </div>
					 <div class="form-group fld">
					<label for="textfield" class="col-sm-4" style="font-weight:bold">Channel Partner Type: </label>
					<div class="col-sm-8">
						<div id="franchi_typename">{{!empty($franchisee_typename)?$franchisee_typename:''}}</div>
					</div>
				 </div>
				 </div>                   
  
				 <div class="col-lg-6">                     
				  <div class="form-group fld">
					<label for="textfield" class="col-sm-4" style="font-weight:bold">Country Channel Partner: </label>
					<div class="col-sm-8">
						<div id="franchi_typename">
						
							@if(isset($franchisee_details->country_frname) && !empty($franchisee_details->country_frname) )
							{{ $franchisee_details->country_frname }}
							@elseif(isset($franchisee_details->country_frname1) && !empty($franchisee_details->country_frname1))
								{{$franchisee_details->country_frname1}}
							@elseif(isset($franchisee_details->country_frname3) && !empty($franchisee_details->country_frname2))
								 {{$franchisee_details->country_frname2}} 
							@elseif(isset($franchisee_details->country_frname3) && !empty($franchisee_details->country_frname3))
								 {{$franchisee_details->country_frname3}} 
							@else
								{{ '-' }}              
							@endif
						</div>
					</div>
				 </div>
				 <div class="form-group fld">
					<label for="textfield" class="col-sm-4" style="font-weight:bold">Regional Channel Partner: </label>
					<div class="col-sm-8">
						<div id="franchi_typename">
							@if(isset($franchisee_details->region_frname) && !empty($franchisee_details->region_frname) )
							{{ $franchisee_details->region_frname }}
							@elseif(isset($franchisee_details->region_frname1) && !empty($franchisee_details->region_frname1) )
								{{ $franchisee_details->region_frname1 }}
							@elseif(isset($franchisee_details->region_frname2) && !empty($franchisee_details->region_frname2) )
								{{ $franchisee_details->region_frname2 }}  
							@else
								{{ '-' }}      
							@endif
						</div>
					</div>
				 </div>
				 <div class="form-group fld">
					<label for="textfield" class="col-sm-4" style="font-weight:bold">State Channel Partner: </label>
					<div class="col-sm-8">
						<div id="franchi_typename">
							@if(isset($franchisee_details->state_frname) && !empty($franchisee_details->state_frname) )
							{{ $franchisee_details->state_frname }}
						   @elseif(isset($franchisee_details->state_frname1) && !empty($franchisee_details->state_frname1) )
								{{ $franchisee_details->state_frname1 }}
							 @else
								{{ '-' }}        
							@endif
						</div>
					</div>
				 </div>
				 <div class="form-group fld">
					<label for="textfield" class="col-sm-4" style="font-weight:bold">District Channel Partner: </label>
					<div class="col-sm-8">
						<div id="franchi_typename">
							 @if(isset($franchisee_details->district_frname) && !empty($franchisee_details->district_frname) )
							{{ $franchisee_details->district_frname }}
							@else
							{{ '-' }}      
						  @endif  
						</div>
					</div>
				 </div>					 
				</div>
				<hr style="clear:both" width="100%" />					
				<div class="form-group hidden">
					<label for="textfield" class="col-sm-3">Channel Partner Type:</label>
					<div class="col-sm-6">
						@if(!empty($franchisee_types))
						<select name="franchi_type" class="form-control" id="franchi_type"  required>
							<option value="">Select Type</option>
							@foreach($franchisee_types as $row)
							 <option @if($type == $row->franchisee_typeid){{'selected'}}@endif value="{{$row->franchisee_typeid}}">{{$row->franchisee_type}}</option>
							@endforeach
						</select>
						@endif
					</div>
				</div> 	                    
				<div class="form-group country" style="">
					<label for="textfield" class="col-sm-3">Country:</label>
					<div class="col-sm-6">
						 <input type="hidden" name="account_id" id="account_id" value="{{!empty($account_id)?$account_id:''}}">
						 <select name="country" class="form-control" id="country" data-url="{{route('admin.franchisee.states-check')}}" required>
							<option value="">--Select Country--</option>
							<?php 
							foreach ($country as $row) {
								echo '<option value="'.$row->country_id.'"';                                    
								if (!empty($access_country) && $access_country == $row->country_id) {
									echo 'selected';									
								}									
								echo '>'.$row->country_name.'</option>';
							}
							?>
						</select>
					</div>
				</div>
				 <div class="form-group region" style="display:none">
					<label for="textfield" class="col-sm-3">Region:</label>
					<div class="col-sm-6">                           
						<select name="region" class="form-control" id="region"  required>
							<option value="">--Select Region--</option>
							<?php 
							if(!empty($regions)) {
								foreach ($regions as $row) {
									echo '<option value="'.$row->region_id.'"';                                    
									if (!empty($access_region) && $access_region == $row->region_id) {
										echo 'selected';									
									}									
									echo '>'.$row->region.'</option>';
								}
							}
							?>								
						</select>
					</div>
				</div>
				<div class="form-group state" style="display:none">
					<label for="textfield" class="col-sm-3">State:</label>
					<div class="col-sm-6">
						
							<select name="state" class="form-control" id="state"  data-url="{{route('admin.franchisee.district-check')}}" required>
								<option value="">--Select State--</option>
								@if(!empty($states))
								@foreach($states as $row)
								<option value="{{ $row->state_id}}" @if($access_state == $row->state_id) {{'selected'}} @endif>{{ $row->state }}</option>
								@endforeach
								@endif
							</select>
							
					</div>
				</div>
				<div class="form-group union_territory" style="display:none">
					<label for="textfield" class="col-sm-3">Union Territory:</label>
					<div class="col-sm-6">                          
							<select name="union_territory[]" class="form-control" id="union_territory" multiple="multiple" style="height:100px;" required>																	
							</select>							
					</div>
				</div>
				<div class="form-group district" style="display:none">
					<label for="textfield" class="col-sm-3">District:</label>
					<div class="col-sm-6">
						<select name="district" class="form-control" id="district" data-url="{{route('admin.franchisee.city-check')}}"  required>
								<option value="">--District--</option>
								@if(!empty($districts))
								@foreach($districts as $district)
								<option value="{{ $district->district_id}}" @if($access_district== $district->district_id) {{'selected'}} @endif >{{ $district->district }}</option>
								@endforeach
								@endif
							</select>
							 <input type="text" style="display:none" class="form-control" name="district_others" id="district_others" placeholder = "Enter District Name" />
					</div>
				</div>
				 <div class="form-group city" style="display:none">
					<label for="textfield" class="col-sm-3">City:</label>
					<div class="col-sm-6">
						<select name="city" class="form-control" id="city" required>
								<option value="">--City--</option>
								 @if(!empty($citys))
								@foreach($citys as $city)
								<option value="{{ $city->city_id}}" @if($access_city== $city->city_id) {{'selected'}} @endif  >{{ $city->city }}</option>
								@endforeach
								 @endif
							</select>                                
							<input type="text" style="display:none" class="form-control" name="city_others" id="city_others" placeholder = "Enter City Name" />
					 </div>
				 </div>
				   <div class="form-group">
                        <label for="textfield" class="col-sm-3">Desposited Amount:</label>
                        <div class="col-sm-6">
                            <input type="text" name="desposited_amount" id="desposited_amount" class="form-control"  placeholder="Enter Desposited amount"  value="{{(isset($deposite_amount)) ? $deposite_amount:''}}"  onkeydown="return isNumberKey(event)" onkeyup="return RestrictNumericDot(event)">
                      
                        </div>
                    </div>
				  <div class="form-group merchant_fee" style="display:none">
					<label for="textfield" class="col-sm-3">Merchant Signup Fee:</label>
					<div class="col-sm-6">
							<input type="text"  class="form-control" name="merchant_signup" id="merchant_signup" placeholder ="Enter Merchant Signup Fee" value="{{(isset($merchant_fee)) ? $merchant_fee:''}}" onkeydown="return isNumberKeydot(event)" onkeyup="return RestrictNumericDot(event)"/>
					 </div>
				 </div>
				  <div class="form-group profit_sharing" style="display:none" >
					<label for="textfield" class="col-sm-3">Profit Sharing</label>
					<div class="col-sm-6">
						<div class="input-group">    
							<input type="text"  class="form-control" name="profit_sharing" id="profit_sharing" placeholder = "Enter Profit Sharing" value="{{(isset($profit_sharing)) ? $profit_sharing:''}}"  onkeydown="return isNumberKeydot(event)"  onkeyup="return RestrictNumericDot(event);" maxlength=3 />
							<span class="input-group-addon">%</span>
						</div>
					</div>
				 </div>
				<div class="form-group prf_shr_out_dist" style="display:none">
					<label for="textfield" class="col-sm-3">Profit Sharing without District</label>
					<div class="col-sm-6">
							<div class="input-group">    
							<input type="text"  class="form-control" name="pro_sharing_without_district" id="pro_sharing_without_district" placeholder = "Enter Profit Sharing" value="{{(isset($profit_sharing_without_district)) ? $profit_sharing_without_district:''}}" maxlength=3/>
							 <span class="input-group-addon">%</span>
					 </div>
				 </div>
				 </div>
				  <div id="franchisee_mapped_user">
				  </div>
				<input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue" value="<?php echo Config::get('constants.ACTIVE');?>"  >
				<div class="form-group hidden" id="btnFld">
					<div class="col-sm-offset-3 col-sm-6" >
						<input type="button" name="update_access" id="update_access" class="btn btn-primary" value="Save">
					</div>
				</div>
				<div class="form-group" id="savebtnFld">
					<div class="col-sm-offset-3 col-sm-6" >
						<input type="button" name="update_changes" id="update_changes" class="btn btn-primary" value="Save">
					</div>
				</div>
			   </form>
 <script src="{{URL::asset('js/providers/admin/franchisee/franchisee_update_access.js')}}"></script>