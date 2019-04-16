
@extends('admin.common.layout')
@section('title',trans('admin/general.add_new_route'))
@section('layoutContent')

    <div class="row">														
		<div class="col-sm-12">
            <div class="panel panel-default">
			   <div class="panel-heading">		
				<h4 class="panel-title">{{trans('admin/general.add_new_route')}}</h4>
				</div>
               <div class="panel-body">
	                  <div class="col-sm-12">
			        <form action="{{route('admin.aff.save')}}" method="POST" class='form-horizontal form-validate' id="create_user"  enctype="multipart/form-data">
                         <input type="hidden" name="user_role" id="user_role" value="" />
						   <input type="hidden" id="currency" name="currency"  value="1" />
                        <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">First Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter First name" data-rule-required="true" value="">
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Last Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Enter Last name" data-rule-required="true" value="">
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">User Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="uname" id="uname" data-url="{{URL::to('admin/affiliate/check-uname')}}" class="form-control"  placeholder="Enter User name" data-rule-required="true" value="" >
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Email:</label>
                        <div class="col-sm-6">
							<div class="input-group email_err">
							  <a class="input-group-addon" href="#"><i class="fa fa-envelope"></i></a>
                            <input type="email" name="email" id="email" class="form-control" data-url="{{URL::to('admin/affiliate/user_email_check')}}"   placeholder="Enter Email" data-rule-required="true" value="" data-err-msg-to="#errmsg">
							</div>
							 <span id="errmsg" for="" class=""></span>
                        </div>
                    </div>
                  <!--  <div class="form-group">
                        <input type="hidden" id="currency" name="currency"  value="1" />
                    </div>-->
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Password:</label>
                        <div class="col-sm-6">
						<div class="input-group">
                            <input type="password" name="password" id="password" class="form-control"  placeholder="Enter Password" value=""  data-err-msg-to="#password_err">
							 <span class="input-group-addon pwdHS"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                        </div>
						 <span id="password_err" for="" class=""></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Confirm Password:</label>
                        <div class="col-sm-6">
						 <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"  placeholder="Enter Confirm Password"  value="" data-err-msg-to="#confirm_password_err" >
							 <span class="input-group-addon pwdHS"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                          </div>
						  <span id="confirm_password_err" for="" class=""></span>
                         </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Gender:</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="gender" id="gender">
                                <option value="">Select Gender</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                    </div>
                   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Date of birth:</label>
                        <div class="col-sm-6">
						    <div class="input-group">
                            <input type="text" name="dob" id="dob" class="form-control"  placeholder="DOB"   value="">
						    	<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
                        </div>
                    </div>
			
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Country:</label>
                        <div class="col-sm-6">
                             <select name="country"   class="form-control" id="country_id">
								<option value="">Select Country</option>
								@if(!empty($countries))
								@foreach ($countries as $country_val)
								<option value="{{$country_val->iso2}}" data-phonecode="{{$country_val->phonecode}}">{{$country_val->country_name}}</option>
								@endforeach
								@endif
							</select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Mobile:</label>
                        <div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon" id="phonecode">-</span>
								<input type="text" name="mobile" id="mobile" data-url="{{URL::to('admin/affiliate/user_mobile_check')}}"  class="form-control" maxlength="16"  placeholder="Enter Mobile"  data-rule-required="true" value="">
							</div>
							<div class="mobile_err"></div>
						</div>
                     </div>
			
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Zip/Pin Code:</label>
                        <div class="col-sm-6">
                            <input type="text" name="zipcode" id="zipcode" class="form-control"  placeholder="Enter Zip/Pin Code" data-rule-required="true" >
                        </div>
                    </div>
                   
                    <input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue"
                           value="<?php echo Config::get('constants.ACTIVE');?>"  >
						   
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                        <div class="col-sm-6" >
                            <input type="submit" name="save_user" id="save_user" class="btn btn-primary" value="Save">
                        </div>
                    </div>
                </form>
              </div>
              </div>
              </div>
            </div>
         </div>
@stop
@section('scripts')
@include('admin.common.datatable_js')
<script src="{{asset('js/providers/admin/affiliate/create_user.js')}}"></script>  
@stop