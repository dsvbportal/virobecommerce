@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/profile.my_profile'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
<h1>{{trans('affiliate/profile.my_profile')}}</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
	<li >{{trans('affiliate/profile.page_title')}}</li>
	<li class="active">{{trans('affiliate/profile.my_profile')}}</li>
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
				<li {!!($curtab=='affiliate-details')? "class='active'":'' !!}><a href="#affiliate-details" role="tab" data-toggle="tab">Contact Details</a></li>				
				<li {!!($curtab=='bank-info')? "class='active'":'' !!}><a href="#bank-info" role="tab" data-toggle="tab">Bank Details</a></li>
				<li {!!($curtab=='ac-settings')? "class='active'":'' !!}><a href="#ac-settings" role="tab" data-toggle="tab">Security Settings</a></li>
				<li {!!($curtab=='kyc-settings')? "class='active'":'' !!}><a href="#kyc-settings" role="tab" data-toggle="tab">Verify your KYC</a></li>
			</ul>
			<div class="tab-content">
				<!-- office information -->
				<div class="tab-pane {{($curtab== 'profile' || $curtab=='account-details')? 'active':''}}" id="account-details">
					<!-- account details -->
					<table class="table table-dark-bordered  table-dark-striped"  id="offc-info">
						<tr>
							<th><a href="" id="personal-editBtn" class="btn btn-primary btn-xs pull-right" data-url="{{route('aff.settings.profile_info')}}"><i class="fa fa-edit"></i> Edit</a>Account information</th>
						</tr>
						<tr>
							<td>
								<div class="col-md-6">
									<div class="pro-panel"> Profile Photo</div>
									<div class="panel-pro">
									@if(isset($userSess->profile_image) && !empty($userSess->profile_image)) 											
									<img class="profile-user-img-edit img-responsive img-circle" src="{{asset($userSess->profile_image)}}" alt="{{trans('affiliate/profile.user_profile_picture')}}">
									@endif
									<p class="text-center"><a id="add_prof_image" class="btn btn-default"  href="#"><i class="fa fa-img" ></i>{{trans('affiliate/profile.upload_your_photo')}}</a>								
									<?php /*@if($userSess->profile_image != config('constants.DEFAULT_IMAGE'))							 
									<p id="remove_prof_image" class="text-center">
									<i class="fa fa-fw fa-times" ></i><a href="#"  class="text-danger">{{trans('affiliate/profile.remove_profile_photo')}}</a>
									</p>
									@endif */?>
									</div>								
								</div>
								<div class="col-md-6">
									<div class="pro-panel"> KYC Information</div>
									<table class="table table-striped">
										<tr>
											<th class="text-right" width="45%">No. of Documents:</th>
											<td style="{{(!empty($userInfo->kyc_status) ? '':'display:none')}}" id="kyc_submit">{{(!empty($userInfo->kyc_status) ?$userInfo->kyc_status->submitted_doc.'/'.$userInfo->kyc_status->total_doc :'')}}</td>
											<td style="{{(empty($userInfo->kyc_status) ? '':'display:none')}}" id="kyc_pending"><span class="label label-warning">Pending</span></td>
										</tr>
										<tr>
											<th class="text-right">Submitted on:</th>
											<td id="submitted_on">{{$userInfo->kyc_submitted_on}}</td>
										</tr>
										<tr >
											<th class="text-right">KYC Verified:</th>
											@if(!empty($userInfo->is_kyc_verified))
												<td><span class="label label-success">Verified</span></td>
											@else
												<td><span class="label label-warning">Pending</span></td>
											@endif														
										</tr>
										<tr>
											<th  class="text-right">Verified on:</th>
											<td>{{$userInfo->kyc_verified_on}}</td>
										</tr>
										<tr style="background: transparent !important;">
											<th  class="text-right"></th>
											<td></td>
										</tr>
										<tr>
											<th  class="text-right"></th>
											<td></td>
										</tr>
										<tr style="background: transparent !important;">
											<th  class="text-right"></th>
											<td></td>
										</tr>
										<tr>
											<th  class="text-right"></th>
											<td></td>
										</tr>
									</table>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-6">
									<div class="pro-panel">Basic Information</div>
									<table class="table table-striped">
										<tr>
											<th class="text-right" width="50%">Account ID:</th>
											<td>{{isset($userInfo->user_code) ? $userInfo->user_code : ''}}</td>
										</tr>
										<tr>
											<th  class="text-right">Username:</th>
											<td>{{isset($userInfo->uname) ? $userInfo->uname : ''}}</td>
										</tr>
										<tr class="form-group">
											<th  class="text-right">Full Name:</th>
											<td>{{isset($userInfo->full_name) ? $userInfo->full_name : ''}}</td>
										</tr>																				
										<tr>
											<th  class="text-right" width="45%">Gender:</th>
											<td>{{$userInfo->gender}}</td>
										</tr>
									   <tr>
											<th  class="text-right" width="45%">Date of Birth:</th>
											<td>{{showUTZ($userInfo->dob, 'd M, Y')}}</td>
											
										</tr>									
										<tr>
											<th class="text-right" width="45%">Marital Status:</th>
											<td id="marital_status">{{$userInfo->marital_status}}</td>
										</tr>
										<tr>
											<th  class="text-right"  width="45%">Father’s/Guardian’s Name:</th>
											<td id="gardian">{{$userInfo->gardian}}</td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									<div class="pro-panel"> Additional Information</div>
									<table class="table table-striped">
										<tr class="form-group">
											<th  class="text-right"   width="45%">Date of Registration :</th>
											<td>{{date('d M, Y',strtotime($userInfo->created_on))}}</td>
										</tr>
										<tr>
											<th  class="text-right">Affiliate Type:</th>
											<td>{{$userInfo->aff_type}}</td>
										</tr>										
										<tr>
											<th  class="text-right">Your Rank:</th>
											<td>{{$userInfo->rank}}</td>
										</tr>
										<tr>
											<th  class="text-right">Your SAP QV:</th>
											<td>{{$userInfo->qv}}</td>
										</tr>
										<tr>
											<th class="text-right">Referral ID:</th>
											<td>{{$userInfo->sponsor_code}}</td>
										</tr>
								
										<tr>
											<th class="text-right" width="50%">Referred By:</th>
											<td>{{$userInfo->sponsor_uname}}</td>
										</tr>
										<tr>
											<th  class="text-right">Placement ID:</label>
											<td>{{$userInfo->upline_code}}</td>
										</tr>
									</table>
								</div>								
							</td>
						</tr>
					</table>
					<!-- address information -->
					<!-- address details -->
					<div class="row"  id="offc-info">
						<div class="col-md-6">
							<table class="table table-dark-bordered table-dark-striped">
								<tr><th><a href="" class="btn btn-primary btn-xs pull-right editAddressBtn" data-url="{{route('aff.settings.address',['type'=>'billing'])}}" data-heading="Billing Address"><i class="fa fa-edit"></i> Edit</a>Billing Address</th></tr>
								<tr><td>
								<span><i class="fa fa-map-marker"></i></span> <span id="billingAddr"><?php echo isset($billingAddr)? htmlspecialchars($billingAddr->address):'<span class="text-muted">'.trans('affiliate/account.update_address').'</span>';?></span>
								</td></tr>
							</table>										
						</div>
						<div class="col-md-6">
							<table class="table table-dark-bordered table-dark-striped">									
								<tr><th><a href="" class="btn btn-primary btn-xs pull-right editAddressBtn" data-url="{{route('aff.settings.address',['type'=>'shipping'])}}"  data-heading="Shipping Address"><i class="fa fa-edit"></i> Edit</a>Shipping Address</th></tr>
								
								<tr><td>
								<span><i class="fa fa-map-marker"></i></span> <span id="shippingAddr"><?php echo isset($shippingAddr)? htmlspecialchars($shippingAddr->address):'<span class="text-muted">'.trans('affiliate/account.update_address').'</span>';?></span>
								</td></tr>
							</table>										
						</div>
					</div>	
					<!-- address information -->
					<!-- nominee information -->
					<div class="row"  id="nominee-info">
						<div class="col-md-12">
							<table class="table table-dark-bordered table-dark-striped">
								<tr>
								<th>
								<a href="#nominee-edit" data-url="{{route('aff.settings.nominee')}}" class="btn btn-primary btn-xs pull-right" id="nominee-editBtn")><i class="fa fa-edit"></i> Edit</a>
								Nominee Information <?php echo !is_object($nominee)? "<span class='label label-default'><i class='fa fa-warning'></i> Update your nominee details</span>": ''?></th>
								</tr>
								<tr>
								<td>
									<div class="row">
									<div class="col-md-4">
										<table class="table table-striped">
										<tr>
											<th class="text-right" width="45%">Name:</th>
											<td id="fullname">{{is_object($nominee)? $nominee->fullname : '-' }}</td>
										</tr>
										</table>
									</div>
									<div class="col-md-4">
										<table class="table table-striped">
										<tr>
											<th class="text-right" width="45%">Gender / Date of Birth:</th>
											<td id="dob">{{is_object($nominee)? $nominee->gender.' / '.date('d M, Y',strtotime($nominee->dob)): '-'}}</td>
										</tr>
										</table>
									</div>
									<div class="col-md-4">
										<table class="table table-striped">
										<tr>
											<th class="text-right" width="45%">Relation:</th>
											<td id="relation_ship">{{is_object($nominee)? $nominee->relation_ship : '-'}}</td>
										</tr>
										</table>
									</div>
									</div>
								</td>
								</tr>
							</table>										
						</div>
					</div>
				</div>
				<!-- office information -->
				<div class="tab-pane {{($curtab=='affiliate-details')? 'active':''}}" id="affiliate-details">			
					<div class="row">
					<div class="col-sm-6">	
						<div class="panel panel-shadow">
							<div class="pro-panel"> Primary Contacts</div>
							<div class="panel-body bg-gray-light">
								<form class="form-verticle" id="primary_contacts_editfrm">						
									<div class="form-group">
										<label for="exampleInputEmail1">
											Email
											@if($userSess->is_email_verified==0)
											<span class="label text-danger"><i class="fa fa-warning"></i> Please verify your email address</span>
											@endif
										</label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
										  <input type="text" class="form-control" name="email" placeholder="Email"   value="{{$userInfo->email}}"  disabled>
										  @if($userSess->is_email_verified==1)
										  <span class="input-group-btn">											
											<button title="Change Email" class="btn btn-success" type="button" data-url="{{route('aff.settings.emailverification')}}" id="changeEmailBtn" ><i class="fa fa-edit"></i> Change</button>
										  </span>
										  @else
										  <span class="input-group-btn">
											<button class="btn btn-danger" type="button" data-url="{{route('aff.settings.email.verify')}}" id="verifyEmailBtn" >Verify</button>
										  </span>								  
										  @endif
										</div>
									</div>
									<div class="form-group">
									<label for="exampleInputPassword1">
										Mobile/Phone#
										@if($userSess->is_mobile_verified==0)
										<span class="label text-danger"><i class="fa fa-warning"></i> Please verify your mobile number</span>
										@endif
									</label>
									<div id="verifyMobBtnBlk" class="input-group">      
										<span class="input-group-addon">{{$userInfo->phonecode}}</span>
										<input type="text" class="form-control" name="mobile"  placeholder="Mobile Phone"  value="{{$userInfo->mobile}}"  disabled>
										@if($userSess->is_mobile_verified==1)
										<span class="input-group-btn">											
											<button title="Change Mobile" class="btn btn-success" type="button" data-url="{{route('aff.settings.mobileverification')}}" id="changeMobileButn" ><i class="fa fa-edit"></i> Change</button>											
										</span>
										@else
										<span id="verifyMobBtnBlk" class="input-group-btn">
											<button class="btn btn-danger" type="button" data-url="{{route('aff.settings.mobile.verify')}}"  id="verifyMobileBtn" >Verify Now</button>
										</span>								  
										@endif
									</div>
									</div>
									 </form>
							</div>
						</div>	
					</div>
					<div class="col-sm-6">
						<div class="panel panel-shadow">
							<div class="pro-panel"> Secondary Contacts</div>	
							<div class="panel-body bg-gray-light">
								<form class="form-verticle" id="profile_editfrm" action="{{route('aff.profile.update')}}">
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
									<button type="buttuon" id='update_contacts' data-url="{{route('aff.settings.update_contacts')}}" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Update Contacts</button>								  
								</form>
							</div>
						</div>
					</div>
					</div>
				</div>
				<!-- Bank details -->
				<div class="tab-pane {{($curtab=='bank-info')? 'active':''}}" id="bank-info">	
					@include('affiliate.settings.bank_details')
				</div>
				<div class="tab-pane {{($curtab=='ac-settings')? 'active':''}}" id="ac-settings">		
					@include('affiliate.settings.account_settings')
				</div>	
				<div class="tab-pane {{($curtab=='kyc-settings')? 'active':''}}" id="kyc-settings">					
					@include('affiliate.settings.kyc_settings')
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
					<h3 class="box-title"><i class="fa fa-picture-o margin-r-5"></i>Upload Profile Image</h3>
				</div>
				<div class="box-body">		
					<form id="profile_image_form" class="form-horizontal" action="{{route('aff.profile.profileimage_save')}}" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-sm-12">				
								<div class="col-sm-3">
									<img class="img editable-img col-sm-12" data-input="#attachment" id="attachment-preview" src="{{ $userSess->profile_image}}"  data-old-image="{{$userSess->profile_image}}"/>
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
	<div class="modal modal-primary fade" id="change-mobile-model" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
				  <h4 class="modal-title">Change Mobile Number</h4>
				</div>
				<div class="modal-body">
					<div class="row">
					<form id="change-mobile-form" class="" action="{{route('aff.settings.change_mobile')}}">						
						<div class="col-md-12">
							<div class="form-group">
								<input type="text" name="mobile" id="mobile"  placeholder="Mobile Number" class="form-control input-lg"/>
							</div>
						</div>
						<div class="col-md-12 text-center">				
							<button type="button" class="btn btn-default" data-dismiss="modal">CANCEL</button>
							<button type="button" class="btn btn-success" id="change-mobile-btn">SAVE</button>
						</div>
					</form>
					<form action="{{route('aff.settings.update_mobile')}}" style="display:none" method="post" class="form-horizontal form-bordered" id="code_verify_form" autocomplete="off" onsubmit="return false;" novalidate="novalidate" >
						<div class="col-md-12">
						<h3>{{trans('affiliate/settings/change_mobile.enter_a_code')}}</h3><hr>
						<p id="code_msg"></p>
						<div class="form-group">
							<div class="col-sm-4">
								<input type="text" id="verification_code" name="verify_code" class="form-control" placeholder="{{trans('affiliate/settings/change_mobile.enter_verification_code')}}">
							</div>
						</div>
						<div class="form-group form-actions">
							<div class="col-sm-4">
								<button name="update_mobile" type="submit" class="btn btn-sm btn-primary" id="update_mobile" >{{trans('affiliate/general.continue_btn')}}</button>&nbsp;
								<button name="cancel" type="submit" class="btn btn-sm btn-default" id="cancel" >{{trans('affiliate/general.cancel_btn')}}</button>
							</div>
							<div class="col-sm-4">
							   <a href="javascript:void(0)" id="mobile_no">{{trans('affiliate/settings/change_mobile.dont_get_code')}}</a>								
							</div>
						</div>
						</div>
					</form>
					</div>
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
				<form action="{{route('aff.settings.emailverification')}}" method="post" class="form-horizontal form-bordered" id="change-email-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
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
				<form action="{{route('aff.settings.mobile.verifyotp')}}" method="post" class="form-horizontal form-bordered" id="verifymobile_otpform" autocomplete="off" onsubmit="return false;" novalidate="novalidate" >					
					<div class="form-group">
						<label class="control-label col-sm-4 text-right">Enter your OTP</label>
						<div class="col-sm-4">
							<input type="text" id="verification_code" name="verify_code" class="form-control" placeholder="{{trans('affiliate/settings/change_mobile.enter_verification_code')}}" maxlength="6">
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-sm-offset-4  col-sm-4">
							<button name="verifymobile" type="submit" class="btn btn-sm btn-primary" id="verifymobileBtn" >{{trans('affiliate/general.continue_btn')}}</button>							
						</div>							
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
						   <a href="javascript:void(0)" id="verify_mobile_resendOTP">{{trans('affiliate/settings/change_mobile.dont_get_code')}}</a>								
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
	<script type="text/javascript" src="{{asset('affiliate/validate/lang/profile.js')}}"  ></script> 
	<script type="text/javascript" src="{{asset('affiliate/validate/lang/change-pwd.js')}}"></script> 
	<script type="text/javascript" src="{{asset('affiliate/validate/lang/change-pin.js')}}"></script>
	<script src="{{asset('js/providers/affiliate/account/profile.js')}}" ></script> 
	<script src="{{asset('js/providers/affiliate/setting/bank_details.js')}}" ></script> 
	<script src="<?php echo URL::asset('affiliate/validate/lang/change-pwd.js');?>"></script>
	<script src="{{asset('js/providers/affiliate/setting/update-password.js')}}"></script>
	<script src="{{asset('js/providers/affiliate/setting/update_securitypwd.js')}}"></script>
	<script src="{{asset('js/providers/affiliate/setting/kyc_document_upload.js')}}"></script>
@stop