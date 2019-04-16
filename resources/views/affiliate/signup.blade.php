@extends( 'affiliate.layout.signuplayout' )
@section( 'page-title', 'Create Account' )
@section( 'contents' )
<div class="col-xs-7">
	<div class="signup-right">
		@if(isset($errmsg))
		<div class="msg-box msg-danger text-center">
			<i class="fa fa-warning"></i>
			<h3>Invalid Request</h3>
			<p>{{$errmsg}}</p>
			<hr/>
			<p class="text-center">Have an account? <a href="{{route('aff.login')}}" class="btn btn-sm btn-primary">LOGIN!</a>
			</p>
		</div>
		@elseif(isset($sponsor_info))
		<div class="logo"> <a href="{{route('aff.login')}}"><img src="{{asset('resources/assets/themes/affiliate/img/affiliate-logo.png')}}"></a>
		</div>
		<div class="small-12 small-centered columns regbox signup">
			<h3 class="text-center">Create a FREE account <br/><span>Already an Affiliate?  <a href="{{route('aff.login')}}" >LOGIN!</a></span></h3>
			<div class="row">
				<div class="small-6 medium-6 columns">
					<p>Referred by: <b class="text-pink">{{$sponsor_info->sponser_name }}</b>
					</p>
				</div>
				<div class="small-6 medium-6 columns">
					<p>Referral ID: <b class="text-pink">{{$sponsor_info->sponser_id}}</b>
					</p>
				</div>
			</div>
			<form id="check_acform" action="{{route('aff.signup.acverify')}}" method="post">
			<div class="row hidden">
				<div class="form-group">
					<div class="small-12 medium-12 columns">
						<label>Country							
							<select name="phonecode" id="phonecode" class="form-control" data-url="http://localhost/dsvb_affiliate/affiliate/geo/state">
								<option value="">Select</option>
								@if(!empty($countries))
									@foreach ($countries as $country_val)
									<option value="{{$country_val->iso2}}" data-id="{{$country_val->country_id}}" {{(isset($ip_country) && $ip_country==$country_val->iso2)? "selected=selected":''}}>{{$country_val->country_name}}</option>
									@endforeach
								@endif					
							</select>
						</label>
					</div>					
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<div class="small-12 medium-12 columns">
						<label>Enter your Email Address <span class="mandaory">*</span>
							<div class="absFld">
								<input type="text" name="login_id" id="login_id"  placeholder="Enter you email" value="" onkeypress="return RestrictSpace(event)" />						 					
							</div>
						</label>
					</div>
				</div>
			</div>
			<div class="row" id="acpwdfld" style="display:none">				
				<div class="form-group">
					<div class="small-12 medium-12 columns">
						 <label>Enter your password 
						 <input type="password" name="acpwd" id="acpwd"  placeholder="Password" value=""/>
						 </label>
					</div>						
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 columns">				
					<button type="button" id="chk_acbtn" data-url="{{route('aff.signup.accheck')}}" class="button large expand">Create Account</button>
					<button type="button" id="verify_btn" data-url="{{route('aff.signup.acverify')}}" class="button large expand" style="display:none">Continue</button>					
				</div>
			</div>					
		</form>
			<form id="extSignupFrm" action="{{route('aff.signup.acupgrade')}}" method="post" style="display:none">			
				<input type='hidden' name="sponser_account_id" value="{{$sponsor_info->sponser_account_id}}">
				<div class="row">
					<div class="form-group">
						<div class="small-6 medium-6 columns">
								 <label>{{trans('affiliate/signup.firstname')}}
								 <input type="text" name="firstname" id="firstname"  placeholder="First name" value=""/>
								 </label>
						</div>
						<div class="small-6 medium-6 columns">
							 <label>{{trans('affiliate/signup.lastname')}}
							 <input type="text" name="lastname"  id="lastname" placeholder="Last name" value=""/>
							  </label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="small-6 medium-6 columns">
							 <label>{{trans('affiliate/signup.gardians')}}
							 <input type="text" name="gardian" id="gardian"  placeholder="" value=""/>
							 </label>

						</div>
						<div class="small-6 medium-6 columns">
							<label>{{trans('affiliate/signup.marital_status')}}
							<select name="marital_status" id="marital_status"  class="form-control">
								<option value="">Select</option>
								<option value="1">Single</option>
								<option value="2">Married</option>							
							</select>
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<div class="small-6 medium-6 columns">
							 <label>{{trans('affiliate/signup.gender')}}
							 <select name="gender" id="gender"  class="form-control">
								<option value="">Select</option>
								@if(!empty($genders))
								@foreach ($genders as $g)
								<option value="{{$g->gender_id}}">{{$g->gender}}</option>
								@endforeach
								@endif
							</select>
							 </label>
						</div>						
						<div class="small-6 medium-6 columns" style="position:relative">
							 <label>{{trans('affiliate/signup.dob')}}<br>
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
							  </label>
						</div>
					</div>
				</div>				
				<div class="row">
				   <div class="small-6 medium-6 columns">
						<label>{{trans('affiliate/signup.mobile_no')}}</label>
						<div class="input-group">						
							<div class="input-group-prepend" style="position:relative">
								<span class="input-group-text" id="basic-addon1">
									<img src="{{asset(config('constants.COUNTRY_FLAG_PATH').'in.png')}}" id="flag" alt="in" style="margin-right:8px; width:70%">
									<select name="country" id="country" class="form-control" style="opacity:0; position:absolute; left:0;right:0" data-url="{{route('aff.state')}}">
									@if(!empty($countries))
									@foreach ($countries as $country_val)
									<option value="{{$country_val->iso2}}" data-phonecode="{{$country_val->phonecode}}" data-flag="{{asset(config('constants.COUNTRY_FLAG_PATH').strtolower($country_val->iso2).'.png')}}" data-id="{{$country_val->country_id}}">{{$country_val->country_name.' ('.$country_val->phonecode.')'}}</option>
									@endforeach
									@endif					
									</select>									
									<span class="pcode">+91</span>
									<i class="fa fa-angle-down"></i>
								</span>									
							</div>
							<input type="text"  name="mobile" id="mobile" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Mobile Number" aria-label="Mobile Number" err-msg-to="#ext_mobile_err" aria-describedby="basic-addon1">
						</div>
						<span id="ext_mobile_err"></span>
					</div>	
					<div class="small-6 medium-6 columns">
						<label>{{trans('affiliate/signup.email')}}
						<input type="text" name="email"  id="email" placeholder="you@email.com" value=""/ >
						</label>
					</div> 
				</div>					      
				<div class="row">			
					<div class="small-6 medium-6 columns">
						<label>{{trans('affiliate/signup.state')}}											
							<select name="state" id="state"  class="form-control" data-url="{{route('aff.state')}}">
								<option value="">Select State</option>								
							</select>
						</label>
					</div>
					<div class="small-6 medium-6 columns">
						<label>{{trans('affiliate/signup.district')}}
						<select name="district" id="district"  class="form-control" data-url="{{route('aff.district')}}">
							<option value="">Select District</option>								
						</select>
						</label>
					</div>		   
				</div>  
				<div class="row" >
					<div class="col-sm-12 columns">
						<input type="submit" id="submit_button" data-rel="noPromoEmail_ext" class="button large expand" value="CREATE ACCOUNT" >
						<p>
							<?php echo trans('affiliate/signup.privacy_txt',['site_name'=>$pagesettings->site_name,'terms_link'=>url('terms-and-conditions'),'privacy_link'=>url('privacy-policy'),'cookie_privacy_link'=>url('privacy-policy')])?></p>			
					</div>
				</div>								
			</form>
			<div id="signup_section" style="display:none">
				<form id="signupFrm" action="{{route('aff.signup.save')}}" method="post"  >			
					<div class="row">
						<div class="form-group">
							<div class="small-6 medium-6 columns">
									 <label>{{trans('affiliate/signup.firstname')}}
									 <input type="text" name="firstname" id="firstname"  placeholder="First name" value=""/>
									 </label>
							</div>
							<div class="small-6 medium-6 columns">
								 <label>{{trans('affiliate/signup.lastname')}}
								 <input type="text" name="lastname"  id="lastname" placeholder="Last name" value=""/>
								  </label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="small-6 medium-6 columns">
								 <label>{{trans('affiliate/signup.gardians')}}
								 <input type="text" name="gardian" id="gardian"  placeholder="" value=""/>
								 </label>
							</div>
							<div class="small-6 medium-6 columns">
								<label>{{trans('affiliate/signup.marital_status')}}
								<select name="marital_status" id="marital_status" class="form-control" >
									<option value="">Select</option>
									<option value="1">Single</option>
									<option value="2">Married</option>								
								</select>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="small-6 medium-6 columns">
								 <label>{{trans('affiliate/signup.gender')}}
								 <select name="gender" id="gender" class="form-control" >
									<option value="">Select</option>
									@if(!empty($genders))
									@foreach ($genders as $g)
									<option value="{{$g->gender_id}}">{{$g->gender}}</option>
									@endforeach
									@endif
								</select>
								 </label>
							</div>
							<div class="small-6 medium-6 columns" style="position:relative">
								 <label>{{trans('affiliate/signup.dob')}}<br>
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
								  </label>
							</div>
						</div>
					</div>									
					<div class="row">
					   	<div class="small-6 medium-6 columns">
							<label>{{trans('affiliate/signup.mobile_no')}}</label>
							<div class="input-group">						
								<div class="input-group-prepend" style="position:relative">
									<span class="input-group-text" id="basic-addon1">
										<img src="{{asset(config('constants.COUNTRY_FLAG_PATH').'in.png')}}" id="flag" alt="in" style="margin-right:8px; width:70%">
										<select name="country" id="country" class="form-control" style="opacity:0; position:absolute; left:0;right:0" data-url="{{route('aff.state')}}">
										@if(!empty($countries))
										@foreach ($countries as $country_val)
										<option value="{{$country_val->iso2}}" data-phonecode="{{$country_val->phonecode}}" data-flag="{{asset(config('constants.COUNTRY_FLAG_PATH').strtolower($country_val->iso2).'.png')}}" data-id="{{$country_val->country_id}}">{{$country_val->country_name.' ('.$country_val->phonecode.')'}}</option>
										@endforeach
										@endif					
										</select>									
										<span class="pcode">+91</span>
										<i class="fa fa-angle-down"></i>
									</span>									
								</div>
								<input type="text"  name="mobile" id="mobile" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Mobile Number" aria-label="Mobile Number"  data-err-msg-to="#signup_mobile_err" aria-describedby="basic-addon1">
							</div>
							<span id="signup_mobile_err"></span>
						</div>
					  <div class="small-6 medium-6 columns">
							<label>{{trans('affiliate/signup.email')}}
							<input type="text" name="email"  id="email" placeholder="you@email.com" value=""/ readonly>
							</label>
					  </div> 
					</div>
					<div class="row">				
						<div class="small-6 medium-6 columns">
							<label>{{trans('affiliate/signup.username')}}							
						<input type="text" name="username"  id="username"  onkeypress="return alphaNumeric_withoutspace(event)"value=""/ >
							</label>
						</div>
						<div class="small-6 medium-6 columns">
							<div class="form-group">
								<label>{{trans('affiliate/signup.password')}}</label>							
								<div class="input-group">
								  <input type="password" name="password" id="password"  class="form-control"  data-err-msg-to="#signup_password_err" />
								  <div class="input-group-append">
									<button class="input-group-text" id="pwdBtn"><i class="fa fa-eye fa-eye-slash"></i></button>
								  </div>
								</div>
								<span id="signup_password_err"></span>
							</div>						
						</div>
					</div>				
					<div class="row">				
						<div class="small-6 medium-6 columns">
							<label>{{trans('affiliate/signup.state')}}							
								<select name="state" id="state"  class="form-control" data-url="{{route('aff.state')}}">
									<option value="">Select State</option>								
								</select>
							</label>
						</div>
						<div class="small-6 medium-6 columns">
							<label>{{trans('affiliate/signup.district')}}
							<select name="district" id="district"  class="form-control" data-url="{{route('aff.district')}}">
								<option value="">Select District</option>								
							</select>
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 columns">
							<input type="submit" id="submit_button" data-rel="#noPromoEmail" class="button large expand" value="CREATE ACCOUNT">
							<p>
							<?php echo trans('affiliate/signup.privacy_txt',['site_name'=>$pagesettings->site_name,'terms_link'=>url('terms-and-conditions'),'privacy_link'=>url('privacy-policy'),'cookie_privacy_link'=>url('privacy-policy')])?></p>
						</div>
					</div>									
				</form>			
			</div>
		</div>
		@endif
		<div class="regconfirm" style="display:none">
			<div class="small-10 large-8 medium-8  small-centered columns signup text-center">
				<div class="row">
					<h2 class='text-success'><i class="fa fa-check-circle-o"></i> Congratulations!</h2>
					<p>We've sent an email with verification link to completed your registration. Please check your inbox.</p>
				</div>
			</div>
		</div>
	</div>	
</div>
<div class="col-xs-5">
	<div class="signup-left">
		<div class="reg-text">
			<h2>Join Virob Influencer Program</h2>
			<p>Refer a customer and ean up to 75% commission on company's revenue. Join the Virob Affiliate Program today and turn your recommendations into income.</p>
			<ul>
				<li> <i class="fa fa-dot-circle-o"></i><span>UNLIMITED</span> Earning potenial</li>
				<li><i class="fa fa-dot-circle-o"></i><span>DEDICATED</span> Affiliate team to assist</li>
				<li><i class="fa fa-dot-circle-o"></i><span>FREE</span> Affiliate membership</li>

			</ul>
		</div>
		<a class="reg-right" href=""><img src="{{asset('resources/assets/themes/affiliate/img/Affiliate.svg')}}"></a>

	</div>
</div>
@stop 
@section('scripts')
<script src="{{asset('resources/assets/themes/affiliate/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link rel="stylesheet" src="{{asset('resources/assets/themes/affiliate/plugins/datepicker/datepicker3.css')}}"/>
<script src="{{asset('js/providers/affiliate/signup.js')}}"></script>
<link rel="stylesheet" src="{{asset('resources/assets/themes/affiliate/plugins/intlTelInput/css/datepicker3.css')}}"/>
<script src="{{asset('resources/assets/themes/affiliate/plugins/intlTelInput/js/intlTelInput.js')}}"></script>
<script>
	$( document ).ready( function () {
		$( '.datepicker' ).datepicker();
	} );
</script>
@stop