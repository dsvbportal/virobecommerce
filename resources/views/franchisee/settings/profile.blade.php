@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/profile.my_profile'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>{{trans('franchisee/profile.my_profile')}}</h1>
        <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('franchisee/general.dashboard')}}</a></li>
			<li >{{trans('franchisee/profile.page_title')}}</li>
			<li class="active">{{trans('franchisee/profile.my_profile')}}</li>
        </ol>
    </section>	
    <!-- Main content -->
    <section class="content" >
	<?php 
	$curtab = '';
	$urlArr = explode('.',\Route::currentroutename());
	if(!empty($urlArr)){
	$curtab =  array_reverse($urlArr)[0];
	}
	?>
		<div class="panel" id="my-account">
			<div class="panel-body">			
				<ul class="nav subnav nav-tabs" role="tablist">		
                 <li {!!($curtab=='profile' || $curtab=='account-details')? "class='active'":'' !!}><a href="#account-details" role="tab" data-toggle="tab">Account Details</a></li>
				<li {!!($curtab=='ac-settings')? "class='active'":'' !!}><a href="#ac-settings" role="tab" data-toggle="tab">Security Settings</a></li>				
				 <li {!!($curtab=='contact_details')? "class='active'":'' !!}><a href="#contact_details" role="tab" data-toggle="tab">Contact Details</a></li>
				<li {!!($curtab=='bank-info')? "class='active'":'' !!}><a href="#bank-info" role="tab" data-toggle="tab">Bank Details</a></li>	
				<li {!!($curtab=='kyc-info')? "class='active'":'' !!}><a href="#kyc-info" role="tab" data-toggle="tab">KYC Details</a></li>
				</ul>
				<div class="tab-content">
					<!-- Account  details -->
					<div class="tab-pane {{($curtab== 'profile' || $curtab=='account-details')? 'active':''}}" id="account-details">
						
						<div class="col-lg-3 col-xl-3 col-md-4 col-3 col-sm-3">
							<div class="card">
								<div class="card-header">
									<h4>Channel Partner Logo</h4>
								</div>
								<div class="card-body">
									<div class="panel-pro">
									<!--<img class="profile-user-img-edit img-responsive img-circle" src="{{isset($franchise_logo_path) ? asset($franchise_logo_path): ''}}" id="franchisee_logo" alt="Logo">-->
									@if(isset($userSess->franchisee_logo) && !empty($userSess->franchisee_logo)) 											
									<img class="profile-user-img-edit img-responsive img-circle" src="{{asset($userSess->franchisee_logo)}}" id="franchisee_logo" alt="Logo">
									@endif
									   <p class="text-center"><a id="add_prof_image" class="btn btn-default"  href="#"><i class="fa fa-img" ></i>Upload Your Logo</a>								
									</div>
								</div>
							</div>
						</div>						
						<div class="col-lg-5 col-xl-5 col-md-12 col-12 col-sm-12">
							<div class="card">
								<div class="card-header">
									<h4>Basic Information</h4>
								</div>
								<div class="card-body">
									<table class="table border projectstatus" id="offc-info">
									   <tr>
										  <th class="text-right" width="35%">Channel Partner Name :</th>
										  <td>{{isset($userInfo->company_name) ? $userInfo->company_name : ''}}</td>
									   </tr>
									   <th class="text-right"> Address</th>
									  <td>
							          <p><i class="fa fa-map-marker"></i> <span id="franchiseeAddr"><?php echo isset($franchiseeAddr)?  $franchiseeAddr->address:'<span class="text-muted">'.trans('affiliate/account.update_address').'</span>';?></span></p> [<a href="" class="link  editAddressBtn" data-url="{{route('fr.settings.address',['type'=>'franchisee'])}}" data-heading="Channel Partner Address"> <i class="fa fa-edit"></i> Edit</a> ]</td></tr>									   
									   <tr>
										  <th  class="text-right">Username:</th>
										  <td>{{isset($userInfo->uname) ? $userInfo->uname : ''}}</td>
									   </tr>
									   <tr>
										  <th  class="text-right">Channel Partner Type:</th>
										  <td>{{isset($userInfo->franchisee_type) ? $userInfo->franchisee_type : ''}}</td>
									   </tr>
									   <tr>
										  <th  class="text-right">Country:</th>
										  <td>{{isset($userInfo->country) ? $userInfo->country : ''}}</td>
									   </tr>
										<tr>
											<th  class="text-right">Access Locations:</th>
											<td>
											@if(isset($fr_access) && !empty($fr_access))
												<ul class="list-unstyled">
												@foreach($fr_access as $fra)
													<li>{{$fra}}</li>
												@endforeach
												</ul>
											@endif
											</td>
									   </tr>									   
									<tr>									  
									</table>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-xl-4 col-md-12 col-12 col-sm-12">
							<div class="card">
								<div class="card-header">
									<h4><a href="" id="personal-editBtn" class="" data-url=""></a>Contact Information</h4>
									<!--<h4><a href="" id="personal-editBtn" class="btn btn-primary btn-xs pull-right" data-url="{{route('fr.settings.profile_info')}}"><i class="fa fa-edit"></i> Edit</a>Contact Information</h4>-->
								</div>
								<div class="card-body">
									<div class="table-responsive projectstatus"><table class="table border ">
									   <tr>
										  <th class="text-right" width="30%">First Name:</th>
										  <td>{{isset($userInfo->firstname) ? $userInfo->firstname : ''}}</td>
									   </tr>
									   <tr>
										  <th  class="text-right">Last Name:</th>
										  <td>{{isset($userInfo->lastname) ? $userInfo->lastname : ''}}</td>
									   </tr>
									   <tr>
										  <th  class="text-right">Gender:</th>
										  <td>{{isset($userInfo->gender) ? $userInfo->gender : ''}}</td>
									   </tr>
									   <tr>
										  <th  class="text-right" >Date of Birth:</th>
										  <td>{{date('d m, Y',strtotime($userInfo->dob))}}</td>
									   </tr>
								   <tr>
									  <th class="text-right"> Address</th>
									  <td>
							          <p><i class="fa fa-map-marker"></i> <span id="personalAddr"><?php echo isset($personalAddr)?  $personalAddr->address:'<span class="text-muted">'.trans('affiliate/account.update_address').'</span>';?></span></p>[<a href="" class="link editAddressBtn" data-url="{{route('fr.settings.address',['type'=>'personal'])}}" data-heading="Contact Address">
									   <i class="fa fa-edit"></i> Edit</a>]</td>
									   </tr>
									</table>
								</div>
							</div>
						</div>
					</div>	
					</div>	
					
					<div class="tab-pane" id="ac-settings">		
						@include('franchisee.settings.account_settings')
					</div>	
					 <div class="tab-pane" id="contact_details">		
                            <div class="col-xs-10 col-sm-6 col-md-6 col-lg-6">	
                               <div class="card">		
                                 <div class="card-header">
				                    <h4>Primary Contacts</h4>
			                     </div>	
                           <div class="card-body">								 
					          	<form class="form-verticle" id="profile_editfrm" action="">						
								<!--  <h4 class="border"> Primary Contacts</h4>-->
								 <div class="form-group">
									<label for="exampleInputEmail1">
								        	Email
											</label>
									   <div class="input-group">
										<span class="input-group-addon"><i class="fa fa-envelope"></i></span>      
									  <input type="text" class="form-control" name="email" placeholder="Email"   value="{{$userInfo->email}}"  disabled>
									   <span class="input-group-btn">											
											<button title="Change Email" class="btn btn-success" type="button" data-url="{{route('fr.settings.emailverification')}}" id="changeEmailBtn" ><i class="fa fa-edit"></i> Change</button>
										  </span>
									</div>
								</div>
							    <div class="form-group">
								<label for="exampleInputPassword1" id="verfy_mobile">
									Mobile/Phone
									@if($userSess->is_mobile_verified==0)
									<span class="text-danger"><i class="fa fa-warning"></i> Please verify your mobile number</span>
									@endif
								  </label>
								<div id="verifyMobBtnBlk"  class="input-group">      
								  <span class="input-group-addon">{{$userInfo->phonecode}}</span>
								  <input type="text" class="form-control" name="mobile"  placeholder="Mobile Phone"  value="{{$userInfo->mobile}}"  disabled>
								  @if($userSess->is_mobile_verified==1)
								<span class="input-group-btn">											
											<button title="Change Mobile" class="btn btn-success" type="button" data-url="{{route('fr.settings.mobileverification')}}" id="changeMobileButn" ><i class="fa fa-edit"></i> Change</button>
										</span>
									@else
								    <span id="verifyMobBtnBlk" class="input-group-btn">
									<button class="btn btn-danger" type="button" data-url="{{route('fr.settings.mobile.verify')}}"  id="verifyMobileBtn" >Verify Now</button>
								    </span>
								@endif
							    </div>
							   </div>
						    </form>
						    </div>
						   </div>
						 </div>	
					  </div>
					<div class="tab-pane" id="bank-info">	
				      @include('franchisee.settings.bank_details')
			       </div>
				   <div class="tab-pane" id="kyc-info">	
				     @include('franchisee.settings.kyc_settings')
			       </div>											
				</div>				
			</div>
		</div>		
		<!-- modals -->
		<div class="row" id="image_upload" style="display:none">
		    <div class="col-md-8">	
				<!-- Upload Profile Image -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<button class="btn btn-xs btn-danger pull-right back_btn"><i class="fa fa-times"></i> Close</button>
						<h3 class="box-title"><i class="fa fa-picture-o margin-r-5"></i>Upload Your Logo</h3>
					</div>
					<div class="box-body">		
						<form id="profile_image_form" class="form-horizontal" action="{{route('fr.profile.franchiseelogo_save')}}" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">				
									<div class="col-sm-3">
										<img class="img editable-img col-sm-12" data-input="#attachment" id="attachment-preview" src="{{ $userSess->franchisee_logo}}"  data-old-image="{{$userSess->franchisee_logo}}"/>
										<span id="profile_logo-error"></span>
									</div>
									<div class="col-sm-9">
										<div class="btn btn-sm bg-green mt-20 waves-effect">
											<span>Choose files</span>
											<input class="cropper ignore-reset" data-hide="#image_upload" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png" data-err-msg-to="#profile_logo-error" data-typeMismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="attachment" title="Choose File" data-default="{{asset(config('constants.ACCOUNT.PROFILE_IMG.DEFAULT'))}}" data-width="360" data-height="360"/>
										</div>										
										<button id="partner_form_sbt" class="btn btn-info btn-sm" title="Remove Image" type="submit"><i class="fa fa-save"></i> Save</button>
										<button id="prof-image-remove" class="btn btn-sm btn-warning" title="Remove Image" type="text"><i class="fa fa-refresh"></i> Cancel</button>
										<p>
										<div class="well">
											<b>Upload Notes :</b>
											<div>Please select valid format (*.gif, *.jpg, *.jpeg, *.png)</div>
										</div>
										</p>
									</div>									
								</div>					
							</div>
						</form>
					</div>	
				</div>				
				<!-- Upload Profile Image -->
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
			<div class="modal-footer">				
				<button type="submit" id='addressSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Update Address</button>
			</div>
		</div>      
	</div>
</div>
 <div class="modal modal-primary fade" id="profile-model" role="dialog">
	 <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header  modal-info">
				<button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
				<h4 class="modal-title"><i class="fa fa-edit"></i> Personal Information</h4>
			 </div>
			    <div class="modal-body">
			 </div>   
			<div class="modal-footer">				
				<button type="submit" id='profileSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
			     </div>
		       </div>      
	        </div>
     </div>
		<div class="modal fade modal-sm" id="change-mobile-model" role="dialog">
			<div class="modal-dialog">
			    <!-- Modal content-->
			    <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
					  <h4 class="modal-title text-center">Change Mobile Number</h4>
					</div>
					<div class="modal-body">
						<form id="change-mobile-form" class="col-md-12" action="{{route('fr.settings.change_mobile')}}">						
							<div class="form-group">
									<input type="text" name="mobile_no" id="mobile_no"  placeholder="Mobile Number" class="form-control input-lg"/>
							</div>
							<div class="row">
								<div class="col-md-6">
								  <button type="button" class="btn btn-default pull-right" data-dismiss="modal">CANCEL</button>
								</div>
								<div class="col-md-6">
								  <button type="button" class="btn btn-success" id="change-mobile-btn">SAVE</button>
								</div>
							</div>
						</form>
					</div>        
			    </div>      
			</div>
		</div>
		<div class="modal modal-primary fade" id="nominee-model" role="dialog">
			<div class="modal-dialog">
			  <!-- Modal content-->
			    <div class="modal-content">
					<div class="modal-header  modal-info">
					  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
					  <h4 class="modal-title"><i class="fa fa-user"></i> Your Nominee</h4>
					</div>
					<div class="modal-body"></div>   
					<div class="modal-footer">				
						<button type="submit" id='nomineeSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
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
					<div class="modal-footer">				
						<button type="submit" id='addressSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Update Address</button>
					</div>
			    </div>      
			</div>
		</div>
		<div class="modal modal-primary fade" id="profile-model" role="dialog">
			<div class="modal-dialog">
			    <!-- Modal content-->
			    <div class="modal-content">
					<div class="modal-header  modal-info">
					    <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
					    <h4 class="modal-title"><i class="fa fa-edit"></i> Personal Information</h4>
					</div>
					<div class="modal-body">
					</div>   
					<div class="modal-footer">				
						<button type="submit" id='profileSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
					</div>
			    </div>      
			</div>
		</div>
		<div class="modal modal-primary fade" id="change-email-model" role="dialog">
			<div class="modal-dialog">
			  <!-- Modal content-->
			  <div class="modal-content">
				<div class="modal-header  modal-info">
				  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
				  <h4 class="modal-title"><i class="fa fa-edit"></i> Change Email</h4>
				</div>
				<div class="modal-body">
					<form action="{{route('fr.settings.emailverification')}}" method="post" class="form-horizontal form-bordered" id="change-email-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
						<div class="form-group" id="error">
							<label class="col-sm-4 control-label" for="oldemail">{{trans('affiliate/general.current_email_id')}}</label>
							<div class="col-sm-8">
								<input type="email" class="form-control" id="crnt_email" value="{{$userSess->email}}" disabled="disabled">
							</div>
						</div>	
						<div class="form-group form-actions">
							<div class="col-sm-12 col-sm-offset-4">
								<button name="Send" type="submit" class="btn btn-primary" id="send_verification_code"><i class="fa fa-angle-right"></i> Send Verification</button>
							</div>
						</div>
					</form>
				</div>   				
			  </div>     

			</div>
		</div>
		
		<div class="modal modal-primary fade" id="verify-mobile-model" role="dialog">
		<div class="modal-dialog">
		  <!-- Modal content-->
		  <div class="modal-content">
			<div class="modal-header  modal-info">
			  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			  <h4 class="modal-title"><i class="fa fa-edit"></i> Verify Mobile</h4>
			</div>
			<div class="modal-body">
				<form action="{{route('fr.settings.mobile.verifyotp')}}" method="post" class="form-horizontal form-bordered" id="verifymobile_otpform" autocomplete="off" onsubmit="return false;" novalidate="novalidate" >					
					<div class="form-group">
						<label class="control-label col-sm-4 text-right">Enter your OTP</label>
						<div class="col-sm-4">
							<input type="text" id="verification_code" name="verify_code" class="form-control" placeholder="{{trans('affiliate/settings/change_mobile.enter_verification_code')}}" maxlength="6" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-sm-offset-4  col-sm-4">
							<button name="verifymobile" type="submit" class="btn btn-sm btn-primary" id="verifymobileBtn" >{{trans('franchisee/general.continue_btn')}}</button>							
						</div>							
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
						<!--   <a href="" id="verify_mobile_resendOTP">{{trans('franchisee/settings/change_mobile.dont_get_code')}}</a>-->
						   <a  href="#" class="" type="button"  data-url="{{route('fr.settings.mobile.verifyotp_resend')}}"  id="verifyMobile_Resend_otp" >{{trans('franchisee/settings/change_mobile.dont_get_code')}}</a>								
						</div>
					</div>					
				</form>
			</div>   				
		  </div>
		</div>
    </div>
		
		@include('affiliate.common.cropper')
	</section>
@stop
@section('scripts')
@include('affiliate.common.datepicker')
@include('affiliate.common.cropper_css_js')
<script>
$(function () {
	var dob = $('#dob').datepicker({
		minDate: "-80Y",
		maxDate: "-18Y",
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,		
        format: 'yyyy-mm-dd'
    });
});
</script>
<script src="{{route('fr.lang',['langkey'=>'profile'])}}" charset="utf-8"></script> 
<script src="{{route('fr.lang',['langkey'=>'change-pin'])}}"></script>
<script src="{{route('fr.lang',['langkey'=>'change-pwd'])}}"></script>
<script src="{{asset('js/providers/franchisee/account/profile.js')}}" ></script> 
<script src="{{asset('js/providers/franchisee/setting/bank_details.js')}}" ></script> 
<script src="{{asset('js/providers/franchisee/setting/update-password.js')}}"></script>
<script src="{{asset('js/providers/franchisee/setting/update_securitypwd.js')}}"></script>
<script src="{{asset('js/providers/franchisee/setting/kyc_document_upload.js')}}"></script>
@stop