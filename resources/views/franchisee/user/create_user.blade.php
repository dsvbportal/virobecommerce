@extends('franchisee.layout.dashboard')
@section('title','Add User')
@section('content')
<section class="content-header">
    <h1><i class="fa fa-home"></i>{{trans('franchisee/user/create_user.title')}}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/user/create_user.profile_pagehead')}}</li>
        <li class="active">{{trans('franchisee/user/create_user.title')}}</li>
    </ol>
</section>
<section class="content">
    <div class="row">														
		<div class="col-sm-12">
            <div class="box box-primary">
               <div class="box-header with-border">
	             <div class="col-sm-12">
			        <form  action="{{route('fr.user.save')}}" method="POST" class='form-horizontal form-validate' id="create_user"  enctype="multipart/form-data">
					<div class="form-group">
                          <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['firstname']['label']}}</label>
                          <div class="col-sm-6">
                            <input type="text" id="first_name" class="form-control" {!!build_attribute($fieldValitator['firstname']['attr'])!!}  onkeypress="return alphaBets_withspace(event)">
                            </div>
                          </div>
                    <div class="form-group">
                             <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['lastname']['label']}}</label>
                            <div class="col-sm-6">
                               <input type="text" id="last_name" class="form-control" {!!build_attribute($fieldValitator['lastname']['attr'])!!}  onkeypress="return alphaBets_withspace(event)" >
                               </div>
                             </div>
				     <div class="form-group">
                             <label for="textfield" class="control-label col-sm-2">Date Of Birth</label>
                            <div class="col-sm-6">
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
					<!--		 <div class="form-group">
										 <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['dob']['label']}}</label>
										<div class="col-sm-6">
										<div class="input-group">
										   <input type="text"  id="dob" class="form-control datepicker" placeholder="DOB"  {!!build_attribute($fieldValitator['dob']['attr'])!!} >
							       	<span class="input-group-addon"><i class="fa fa-calendar form-control-feedback"></i></span>
									</div>
									 <span id="dob-err"></span>
								     </div>
							</div>-->
							 
					  <div class="form-group">
								 <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['gender']['label']}}</label>
                              <div class="col-sm-6">
								 <select  id="gender" {!!build_attribute($fieldValitator['gender']['attr'])!!} class="form-control" >
									<option value="">Select</option>
									@if(!empty($genders))
									@foreach ($genders as $g)
									<option value="{{$g->gender_id}}">{{$g->gender}}</option>
									@endforeach
									@endif
								</select>
								 </label>
							</div>
					</div>
							 
                    <div class="form-group">
                           <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['country']['label']}}</label>
                              <div class="col-sm-6">
								<div class="input-group">
								  <span class="input-group-addon"><img src="" class="country-flag"></span>
									<select class="form-control" {!!build_attribute($fieldValitator['country']['attr'])!!} data-err-msg-to="#mobile-country-err" id="country">
										@foreach($countries as $country)
										<option value="{{$country->country_id}}" data-mobile_validation="{{$country->mobile_validation}}" data-flag="{{$country->flag}}" data-phonecode="{{$country->phonecode}}">{{$country->country}}</option>
										@endforeach
									</select>
                                    </div>
                                   </div>
                                  </div>
                       <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['mobile']['label']}}</label>
						        <div class="col-sm-6">
								  <div class="input-group">
									<span class="input-group-addon country-phonecode"></span>
									<input id="mobile"  {!!build_attribute($fieldValitator['mobile']['attr'])!!} data-err-msg-to="#mobile-err" class="form-control" {!!build_attribute($fieldValitator['mobile']['attr'])!!}/>
									</div>
                           <span id="mobile-err"></span>
                          </div>
                          </div>
					   <div class="form-group">
							<label for="textfield" class="control-label col-sm-2">{{$fieldValitator['email']['label']}}</label>
							<div class="col-sm-6">
								 <input id="email" {!!build_attribute($fieldValitator['email']['attr'])!!} class="form-control" />
							</div>
                      </div>
					  
					  <div class="form-group">
							<label for="textfield" class="control-label col-sm-2">{{$fieldValitator['username']['label']}}</label>
							<div class="col-sm-6">
								 <input id="username" {!!build_attribute($fieldValitator['username']['attr'])!!} class="form-control" onkeypress="return alphaNumeric_withoutspace(event)" />
							</div>
                      </div>
					   <div class="form-group">
							<label for="textfield" class="control-label col-sm-2">{{$fieldValitator['password']['label']}}</label>
							<div class="col-sm-6">
								 <input id="password" type="password" {!!build_attribute($fieldValitator['password']['attr'])!!} class="form-control" />
							</div>
                      </div> 
					  
					  <div class="form-group">
								 <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['state']['label']}}</label>
                              <div class="col-sm-6">
								 <select  id="state"  class="form-control"  {!!build_attribute($fieldValitator['state']['attr'])!!}  data-url="{{route('fr.user.state')}}" >
										<option value="">Select State</option>		
								</select>
								 </label>
							</div>
					</div>
					<div class="form-group">
								 <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['district']['label']}}</label>
                              <div class="col-sm-6">
								 <select  id="district"  class="form-control" {!!build_attribute($fieldValitator['district']['attr'])!!} data-url="{{route('fr.user.district')}}">
								<option value="">Select District</option>	
								</select>
								 </label>
							</div>
					</div> 
                   <div class="form-group pull-center">
				        <label for="textfield" class="control-label col-sm-2"></label>
                                <div class="col-sm-2">
                                    <input type="submit" class="btn btn-success btn-block" disabled value="Continue"/>
                                </div>
                            </div>
                </form>
              </div>
              </div>
              </div>
            </div>
         </div>
		</section>
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/user/create_user.js')}}"></script>
@stop