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
    <section class="content">
		<!-- Small boxes (Stat box) -->		
		@if(!empty($profileInfo) && !empty($userSess))			
		<div class="row" id="my_profile">
			<!-- ./col -->
			<div class="col-md-3">
				<!-- Profile Image -->
				<div class="box box-primary">
					<div class="box-body box-profile">
					   <div class="row">
							@if(isset($userSess->profile_image) && !empty($userSess->profile_image)) 
							<img class="profile-user-img img-responsive img-circle" src="{{asset($userSess->profile_image)}}" alt="{{trans('affiliate/profile.user_profile_picture')}}">@endif 	
						</div>
						<p></p>
						<div class="row text-center">
							<p><i class="fa fa-fw fa-upload"></i>
							<a id="add_prof_image"  href="#">{{trans('affiliate/profile.upload_your_photo')}}</a></p>
							<p id="remove_prof_image">
							@if(isset($profileInfo['userdetails']->profile_image) && !empty($profileInfo['userdetails']->profile_image) && $profileInfo['userdetails']->profile_image != 
                            config('constants.DEFAULT_IMAGE'))							 
							<i class="fa fa-fw fa-times" ></i><a href="#"  class="text-danger">{{trans('affiliate/profile.remove_profile_photo')}}</a>
							@endif
							</p>
						</div>
						<h3 class=" text-center">{{isset($profileInfo['userdetails']->full_name) ? $profileInfo['userdetails']->full_name : ''}}</h3>
						<p class="text-center text-danger profile-username">({{isset($profileInfo['userdetails']->uname) ? $profileInfo['userdetails']->uname : ''}})</p>

						<ul class="list-group list-group-unbordered">
						<li class="list-group-item">						
						  <b>{{trans('affiliate/general.my_refferals')}}</b> <a class="pull-right">{{isset($profileInfo['tree_info']->referral_cnts) ? number_format($profileInfo['tree_info']->referral_cnts) : ''}}</a>
						</li>
						<li class="list-group-item">
						  <b>{{trans('affiliate/general.my_downlines')}}</b> <a class="pull-right">{{isset($profileInfo['tree_info']->my_team_cnt)  ? number_format($profileInfo['tree_info']->my_team_cnt) : ''}}</a>
						</li>						
						</ul>
					</div>
					<!-- /.box-body -->
				</div>
			</div>				
			<div class="col-md-6">	
			    <div class="box box-primary">
					<div class="box-header with-border">
						<button class="btn btn-sm btn-default pull-right" id="profile_edit_btn"><i class="fa fa-edit"></i> Edit</button>
						<h3 class="box-title"><i class="fa fa-user margin-r-5"></i> 
						{{trans('affiliate/general.about_me')}}
						</h3>
					</div>
					<div class="box-body">
						@if($userSess->can_sponsor)
					    <div class="col-sm-12">
						    <div class="form-group alert alert-success">
							   <strong>{{trans('affiliate/account/profile.promotional_url')}}</strong>
							   <p class="text-muted"><a>{{url('/'.$profileInfo['userdetails']->uname)}}</a></p>
							</div>
						</div>
						@endif
					    <div class="col-sm-6">
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.signed_up_on')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->created_on) ? $profileInfo['userdetails']->created_on : ''}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.gender')}} / {{trans('affiliate/account.dob')}}</strong>
							   <p class="text-muted">
							   {{isset($profileInfo['userdetails']->dob) ? date('d m, Y',strtotime($profileInfo['userdetails']->dob)).', ' : ''}}
							   {{isset($profileInfo['userdetails']->gender) ? $profileInfo['userdetails']->gender:''}}							   
							   </p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.invited_by')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['tree_info']->referrer_name) ? $profileInfo['tree_info']->referrer_name : ''}}</p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.your_email')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->email) ? $profileInfo['userdetails']->email : ''}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/general.mobile_no')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->phonecode) ? $profileInfo['userdetails']->phonecode.'-'.$profileInfo['userdetails']->mobile : '-'}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.invited_email')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['tree_info']->referrer_email) ? $profileInfo['tree_info']->referrer_email : ''}}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="profile_edit" style="display:none">
		    <div class="col-md-9">
				<div class="box box-primary">
					<div class="box-header with-border">						
						<h3 class="box-title"><i class="fa fa-map-marker margin-r-5"></i>{{trans('affiliate/account/profile.edit')}}</h3>
					</div>
					<div class="box-body">						
						<form class="form-verticle" id="profile_editfrm" action="{{route('aff.profile.update')}}" >
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputEmail1">Country of residence</label>
							<div class="input-group">      
							  <input type="text" class="form-control" name="country" placeholder="Country"  value="{{$profileInfo['userdetails']->country}}" disabled>
							  <span class="input-group-addon"><i class="fa fa-lock"></i></span>
							</div>
						</div>
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputEmail1">Email</label>
							<div class="input-group">      
							  <input type="text" class="form-control" name="email" placeholder="Email"   value="{{$profileInfo['userdetails']->email}}"  disabled>
							  <span class="input-group-addon"><a href="{{route('aff.settings.change_email')}}" title="Change Email"><i class="fa fa-edit"></i></a></span>
							</div>
						</div>
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputPassword1">Mobile/Phone#</label>
							<div class="input-group">      
							  <span class="input-group-addon">{{$profileInfo['userdetails']->phonecode}}</span>
							  <input type="text" class="form-control" name="mobile"  placeholder="Mobile Phone"  value="{{$profileInfo['userdetails']->mobile}}"  disabled>							  
							  <span class="input-group-addon"><a href="{{route('aff.settings.change_email')}}" title="Change Mobile"><i class="fa fa-edit"></i></a></span>
							</div>
						</div>	
						
						<div class="clearfix"></div>
						
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputPassword1">First name <span class="mandatory">*</span></label>
							<input type="text" class="form-control" name="firstname" value="{{$profileInfo['userdetails']->firstname}}" placeholder="First name">
						</div>
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputPassword1">Last name <span class="mandatory">*</span></label>
							<input type="text" class="form-control" name="lastname" value="{{$profileInfo['userdetails']->lastname}}"  placeholder="Last name">
						</div>
						<div class="form-group col-xs-4 col-sm-2 col-md-2 col-lg-2">
							<label for="exampleInputPassword1">Date of Birth</label>
							<input type="text" name="dob" class="form-control datepicker" id="dob" value="{{isset($profileInfo['userdetails']->dob)?  $profileInfo['userdetails']->dob:''}}" placeholder="Date of Birth">
						</div>
						<div class="form-group col-xs-4 col-sm-2 col-md-2 col-lg-2">						
							<label for="exampleInputPassword1">Gender</label>
							<select class="form-control" name="gender" id="gender">
								<option valie="">Select</option>
								@if(!empty($genders))
									@foreach($genders as $g)
									<option value="{{$g->gender_id}}" <?php echo ($profileInfo['userdetails']->gender_id==$g->gender_id)? "selected=selected":''?>>{{$g->gender}}</option>
									@endforeach
								@endif
							</select>
						</div>
						
						<div class="clearfix"></div>
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputPassword1">Display name/User name <span class="mandatory">*</span></label>
							<div class="input-group">  
								<input type="text" class="form-control" name="uname" value="{{$profileInfo['userdetails']->uname}}" <?php if(!empty($profileInfo['userdetails']->uname)) echo "disabled='disabled'";?>  placeholder="Display name">
								<span class="input-group-addon"><a id="changeunameBtn" title="Change Mobile"><i class="fa fa-edit"></i></a></span>
							</div>
						</div>									
						@if(isset($profileInfo['userdetails']->has_pancard)) 
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">
							<label for="exampleInputPassword1">PAN Number  <span class="mandatory">*</span></label>
							<input type="text" class="form-control"  name="pan_no" id="pan_no"  value="{{isset($profileInfo['userdetails']->pan_no)? $profileInfo['userdetails']->pan_no:''}}" placeholder="PAN Number" <?php if($userSess->is_verified && !empty($profileInfo['userdetails']->pan_no)) echo "disabled='disabled'";?>>
						</div>						
						@endif
						</form>
					</div>	
					<div class="box-footer text-right">
						<button type="button" id="back_btn" class="btn btn-default pull-left"><i class="fa fa-arrow-left"></i> Back</button>
						<button type="button" class="btn btn-primary" id="profilefrmBtn"><i class="fa fa-save"></i> Save Changes</button>
					</div>
				</div>
			</div>
		</div>	
		<div class="row" id="change_username" style="display:none">
		    <div class="col-md-9">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-map-marker margin-r-5"></i>Change Display Name</h3>
					</div>
					<div class="box-body">						
						<form class="form-verticle" id="change_unamefrm" action="{{route('aff.settings.change_uname')}}" >
						<div class="form-group col-xs-10 col-sm-4 col-md-4 col-lg-4">							
							<input type="text" class="form-control" name="new_uname" placeholder="Enter your display name"  value="">
						</div>
						<div class="form-group">
						<button type="submit" class="btn btn-primary" id="change_unamefrmBtn"><i class="fa fa-save"></i> Update</button>
						<button type="button" id="change_uname_back_btn" class="btn btn-danger"><i class="fa fa-times"></i> Cancel</button>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
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
									<div class="col-sm-4">
										<div class="btn btn-sm bg-green mt-20 waves-effect">
											<span>Choose files</span>
											<input class="cropper ignore-reset" data-hide="#image_upload" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png" data-err-msg-to="#profile_logo-error" data-typeMismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="attachment" title="Choose File" data-default="{{asset(config('constants.ACCOUNT.PROFILE_IMG.DEFAULT'))}}" data-width="360" data-height="360"/>
										</div>									
										</br></br>
										<button id="partner_form_sbt" class="btn btn-info btn-sm" title="Remove Image" type="submit"><i class="fa fa-save"></i> Save</button>&nbsp;&nbsp;
										<!--input id="partner_form_sbt" class="btn btn-info btn-sm" value="Submit" type="submit"-->&nbsp;&nbsp;
										<button id="prof-image-remove" class="btn btn-sm btn-warning" title="Remove Image" type="text"><i class="fa fa-refresh"></i> Reset</button>
									</div>
									<div class="col-sm-5">
										<div class="well well-sm">
											<b>Upload Notes :</b>
											<div>Please select valid format (*.gif, *.jpg, *.jpeg, *.png)</div>
										</div>
									</div>									
								</div>					
							</div>
						</form>
					</div>	
				</div>				
				<!-- Upload Profile Image -->
			</div>
		</div>
        <div class="row" id="location"> 
            <div class="col-md-3">		
				<!-- About Me Box -->
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-map-marker margin-r-5"></i>{{trans('affiliate/general.location')}}</h3>
					</div>
					<div class="box-body">
						<strong> {{trans('affiliate/account/profile.address')}}</strong>						
						<p class="text-muted">{{isset($profileInfo['userdetails']->formated_address) ? $profileInfo['userdetails']->formated_address : 'sd'}}</p>
					</div>	
				</div>
				<!-- /.box -->
			</div>
		</div>
		@include('affiliate.common.cropper')		
		@endif
		<!-- /.row -->
    </section>
    <!-- /.content -->
	

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
<?php /*
<script src="{{url('affiliate/validate/lang/update_profile_image')}} " charset="utf-8"></script>
<script src="{{asset('js/providers/affiliate/account/updateprofileimage.js')}}" ></script> 
*/
?>
<script src="{{asset('js/providers/affiliate/account/profile.js')}}" ></script> 
@stop  	