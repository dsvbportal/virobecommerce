@extends('admin.common.layout')
@section('title','Edit Channel Partner\'s Profile')
@section('layoutContent')

<div class="panel panel-default">
    <div class="panel-heading">		
        <h4 class="panel-title"><i class="fa fa-edit"></i> Edit Channel Partner's Profile</h4>
    </div>
    <div class="panel-body">
		<div id="msg"></div>           
            <div class="box-body update_form">
                <div id="update_form">				
				<!--  Edit Form -->	
				
				    <div class="col-sm-12" style="{{($status == config('httperr.SUCCESS') && empty($msg)) ? 'display:none': 'display:block'}}">
					   <div class="alert alert-danger">{{(isset($msg)) ? $msg :''}} <a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>
					</div>
				    <form method="post" class="form-horizontal form-validate" name="create_bank" id="edit_prf" action="{{route('admin.franchisee.edit-save',['uname'=>isset($user_details->uname) ? $user_details->uname:''])}}" autocomplete="off">						
						<input type="hidden" id="account_id" name="account_id" value="{{(isset($user_details->account_id)) ? $user_details->account_id :''}}"/>
						<input type="hidden" id="fr_account_id" name="fr_account_id" value="{{(isset($user_details->franchisee_acc_id)) ? $user_details->franchisee_acc_id :''}}"/>
						
				 <!--   <input type="hidden" id="old_email" name="old_email" value="{{(isset($user_details->email)) ?  $user_details->email :''}}"/>
						<input type="hidden" id="old_mobile" name="old_mobile" value="{{(isset($user_details->mobile)) ? $user_details->mobile :''}}"/>-->
												
						<input type="hidden" id="currency_id" value="{{isset($user_details->currency)? $user_details->currency:''}}"/>			
						
						<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">Username:</label>
								<div class="col-sm-6">
									<div><strong>{{(isset($user_details->uname)) ? $user_details->uname : ''}}</strong></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">Support Center Type:</label>
								<div class="col-sm-6">
									<div><strong>{{(isset($user_details->franchisee_type_name)) ? $user_details->franchisee_type_name : ''}}
											<br/>
											@if(isset($user_details->access_country_name) && !empty($user_details->access_country_name))
											{{ '('.$user_details->access_country_name.')' }}
											@elseif(isset($user_details->access_region_name) && !empty($user_details->access_region_name))
											{{ '('.$user_details->access_region_name.')' }}
											@elseif(isset($user_details->access_state_name) && !empty($user_details->access_state_name))
											{{ '('.$user_details->access_state_name.')' }}
											@elseif(isset($user_details->access_district_name) && !empty($user_details->access_district_name))
											{{ '('.$user_details->access_district_name.')' }}
											@elseif(isset($user_details->access_city_name) && !empty($user_details->access_city_name))
											{{ '('.$user_details->access_city_name.')' }}
											@endif
										</strong></div>
								</div>
							</div>
						</div>
		<!--			<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">Deposited:</label>
								<div class="col-sm-6">
									<div><strong>{{((isset($user_details->is_deposited)) && $user_details->is_deposited == 1) ? "Yes" : "No"}}</strong></div>
								</div>
							</div>
						</div>-->
		 			<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">Deposited Amount:</label>
								<div class="col-sm-6">
									<div><strong>{{(isset($user_details->deposite_amount)) ? $user_details->deposite_amount." ".$user_details->currency : ''}}</strong></div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">Country Support Center:</label>
								<div class="col-sm-6">
									<div><strong>
											@if(isset($user_details->country_frname) && !empty($user_details->country_frname) )
											{{ $user_details->country_frname }}
											@elseif(isset($user_details->country_frname1) && !empty($user_details->country_frname1))
											{{$user_details->country_frname1}}
											@elseif(isset($user_details->country_frname3) && !empty($user_details->country_frname2))
											{{$user_details->country_frname2}}
											@elseif(isset($user_details->country_frname3) && !empty($user_details->country_frname3))
											{{$user_details->country_frname3}}
											@else
											{{ '-' }}
											@endif
										</strong></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">Regional Support Center:</label>
								<div class="col-sm-6">
									<div><strong>
											@if(isset($user_details->region_frname) && !empty($user_details->region_frname) )
											{{ $user_details->region_frname }}
											@elseif(isset($user_details->region_frname1) && !empty($user_details->region_frname1) )
											{{ $user_details->region_frname1 }}
											@elseif(isset($user_details->region_frname2) && !empty($user_details->region_frname2) )
											{{ $user_details->region_frname2 }}
											@else
											{{ '-' }}
											@endif
										</strong></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">State Support Center:</label>
								<div class="col-sm-6">
									<div><strong>
											@if(isset($user_details->state_frname) && !empty($user_details->state_frname) )
											{{ $user_details->state_frname }}
											@elseif(isset($user_details->state_frname1) && !empty($user_details->state_frname1) )
											{{ $user_details->state_frname1 }}
											@else
											{{ '-' }}
											@endif
										</strong></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="textfield" class="col-sm-6">District Support Center:</label>
								<div class="col-sm-6">
									<div><strong>
											@if(isset($user_details->district_frname) && !empty($user_details->district_frname) )
											{{ $user_details->district_frname }}
											@else
											{{ '-' }}
											@endif
										</strong></div>
								</div>
							</div>
						</div>
						<div style="clear:both"></div>
						<hr width="100%" />
						<div class="col-sm-12">
						        <div class="form-group">
                                  <label for="textfield" class="col-sm-3">Support Center Name:</label>
                                   <div class="col-sm-6">
										<input type="text" name="company_name" id="company_name" class="form-control"  placeholder="Enter Support Center name" value="{{(isset($user_details->company_name)) ? $user_details->company_name:''}}" >
										<span><small>Company or Firm Name</small></span>
                                  </div>
                                </div>
							</div>
				     
						
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">Office Available:</label>
								<div class="col-sm-6">
						          <input type="radio" name="office_available" class="simple" value="{{Config::get('constants.ON')}}" {{(isset($user_details->office_available) && $user_details->office_available == 1) ? "checked='checked'" : ""}} />Yes
									<input type="radio" name="office_available" class="simple" value="{{Config::get('constants.OFF')}}" {{(isset($user_details->office_available) && $user_details->office_available == 0) ? "checked='checked'" : ""}} />No
									<div id="office_available_err"></div>
								</div>
							</div>
					 
					  <div class="form-group" id="editactions" <?php echo (isset($user_details->office_available) && $user_details->office_available == 1) ? "class='display:block'": "style='display:none'";?>>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">Address:</label>
								<div class="col-sm-6">
								    <span id="fran_address">{{(isset($user_details->fr_address)) ? $user_details->fr_address :''}}</span> 
							
								<a href="" class="edit_fr_address edit" data-url="{{route('admin.franchisee.address',['type'=>'franchisee'])}}"  data-heading="Channel Partner Partner Address"><i class="fa fa-edit"></i> Edit</a>
								
									<input name="edit_fr_addr" type="hidden" id="edit_fr_addr" value="0" />									
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group franchisee_address" style="display:none">
								<label class="col-sm-3">Flat No/Street</label>
								<div class="col-sm-6">
									 <textarea name="fr_address[company_address]" id="company_address" class="form-control" placeholder="Flat No/Street" ></textarea>
                           
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group franchisee_address" style="display:none">
								<label class="col-sm-3">LandMark</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="fr_landmark" name="fr_address[landmark]" placeholder="LandMark" value="" >
									
								</div>
							</div>						
						</div>						
						<!-- -->						
				  <div class="col-sm-12">
                     <div class="form-group fr_stateFld hidden">
	                    <label class="col-sm-3">State / Region<span class="text-danger"></span></label>
	                  	<div class="col-sm-6">
			                <select  name="fr_address[fr_state_id]" id="fr_state_id" class="form-control"  data-url="{{route('admin.franchisee.districts')}}"></select>
		                  </div>
                    	</div>							
                    </div>	
			 <div class="col-sm-12">
                     <div class="form-group fr_districtFld hidden">
					 <label class="col-sm-3">District:<span class="text-danger"></span></label>
                        <div class="col-sm-6">
                            <select name="fr_address[fr_district_id]" class="form-control" id="fr_district_id" data-url="{{route('admin.franchisee.cities')}}" >
                            </select>
                        </div>
                    </div>
                    </div>
					<div class="col-sm-12">
					<div class="form-group fr_cityFld hidden">
						<label class="col-sm-3">City / Town<span class="text-danger"></span></label>
						<div class="col-sm-6">
							<select  name="fr_address[fr_city_id]" id="fr_city_id" class="form-control" ></select>				
						</div>
					   </div>	
					</div>	
					<div class="col-sm-12" >
							<div class="form-group franchisee_address" style="display:none">		
								<label for="textfield" class="col-sm-3">Zip/Postal Code:</label>
								<div class="col-sm-6">
									  <input type="text" name="fr_address[franchisee_zipcode]" id="franchisee_zipcode" class="form-control"  placeholder="Enter Zip/Pin Code"  value="" >
								</div>
							</div>
						</div>
					</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label><b>&nbsp;Contact Person Details</b></label>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">First Name:</label>
								<div class="col-sm-6">
									<input name="firstname" class="form-control" type="text" id="firstname" value="{{(isset($user_details->firstname))? $user_details->firstname:''}}" onkeypress="return alphaBets(event)"/>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">Last Name:</label>
								<div class="col-sm-6">
									<input name="lastname" class="form-control" type="text" id="lastname" value="{{(isset($user_details->lastname)) ? $user_details->lastname:''}}" onkeypress="return alphaBets(event)"/>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">DOB:</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input name="dob" class="form-control datepicker" placeholder="" type="text" id="dob" value="{{isset($user_details->dob) ? $user_details->dob:''}}" data-err-msg-to="#dob_err"/>
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
									<span id="dob_err"></span>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">Contact Address:</label>
								<div class="col-sm-6">
								    <span id="address">{{(isset($user_details->address)) ? $user_details->address :''}}</span> 
									<!--<button class="btn btn-xs btn-primary edit" id="edit_address" type="button"><i class="fa fa-edit"></i> Edit</button>
									<input name="editaddr" type="hidden" id="editaddr" value="0" />	-->
									
									<a href="" class="editAddressBtn edit"  data-url="{{route('admin.franchisee.address',['type'=>'personal'])}}"  data-heading="Personal Address"><i class="fa fa-edit"></i> Edit</a>
									<input name="editaddr" type="hidden" id="editaddr" value="0" />
								</div>
							</div>
						</div>
						<!--  -->
						<div class="col-sm-12">
							<div class="form-group fr_address" style="display:none">
								<label class="col-sm-3">Address</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="flatno_street" name="address[flatno_street]" placeholder="Flat No/Street" value="">
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group fr_address" style="display:none">
								<label class="col-sm-3">LandMark</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="landmark" name="address[landmark]" placeholder="LandMark" value="">
								</div>
							</div>						
						</div>						
						<!-- -->						
					<div class="col-sm-12">
                     <div class="form-group stateFld hidden">
	                    <label class="col-sm-3">State / Region<span class="text-danger"></span></label>
	                  	<div class="col-sm-6">
			                <select  name="address[state_id]" id="state_id" class="form-control" data-url="{{route('admin.franchisee.districts')}}" ></select>
		                  </div>
                    	</div>							
                    </div>	
	            <div class="col-sm-12">
                     <div class="form-group districtFld hidden">
					 <label class="col-sm-3">District:<span class="text-danger"></span></label>
                        <div class="col-sm-6">
                            <select name="address[district_id]" class="form-control" id="district_id" data-url="{{route('admin.franchisee.cities')}}" >
                            </select>
							
                        </div>
                    </div>
                    </div>
                       <div class="col-sm-12">
						<div class="form-group cityFld hidden">
							<label class="col-sm-3">City / Town<span class="text-danger"></span></label>
							<div class="col-sm-6">
								<select  name="address[city_id]" id="city_id" class="form-control"></select>	
							</div>
	                       </div>	
	                    </div>	
					<div class="col-sm-12" >
							<div class="form-group fr_address" style="display:none">		
								<label for="textfield" class="col-sm-3">Zip/Postal Code:</label>
								<div class="col-sm-6">
									<input name="address[postal_code]" class="form-control" type="text" id="postal_code" placeholder="Enter Zip/Pin Code" value=""/>
								</div>
							</div>
						</div>	
				  <div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">Country:</label>
								<div class="col-sm-6">
									<input type="hidden" name="country" id="user_country"  value="{{isset($user_details->country_id)? $user_details->country_id:''}}" />
							    	<span><b id="country">{{(isset($user_details->country)) ? $user_details->country:''}}</b></span>									
								</div>
							</div>
					</div>					
					
						<div class="col-sm-12">
							<div class="form-group">
								<label for="textfield" class="col-sm-3">&nbsp;</label>
								<div class="col-sm-6">
									<!-- input type="submit" name="submit" id="submit" class="btn btn-primary" value="Save"-->
									<button type="submit" name="submit" id="submit" class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>
						
					</form>
				
                </div>
            </div>
    </div>
</div>
<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/franchisee/franchisee_update_profile.js')}}"></script>
<script>
     /*  $(document).ready(function () {	
        $('#check').click(function (e) {
            e.preventDefault();
            var uname = $('#uname').val();
            if (uname != '') {
                $.ajax({
                    dataType: 'json',
                    type: 'post',
                    data: {uname: uname},
                    url: $('#search_user').attr('action'),
                    beforeSend: function () {
                        $('#check').text('Processing..');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            // $('.check-btn').hide();
                            // $('#uname').attr('readonly','readonly');
                            $('#update_form').html(data.content);
                            $('#check').text('Check Channel Partner');
                            $('#uname_status').html('');
                            $('#access_form').hide();
                        } else if (data.status == 'not_avail') {
                            $('#uname_status').html('Channel Partner Not Avaliable');
                            $('#check').text('Check Franchisee');
                        }
                    },
                    error: function (data) {
                        alert("something Went Wrong");
                    }
                })
            } else {
                alert("Please Enter validate UserName");
            }
        })  

        $('#uname').on('keyup', function () {
            $('#update_form').html('');
        }).on('mouseup', function () {
            $('#update_form').html('');
        });
    });*/
</script>
@stop
