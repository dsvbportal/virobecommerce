<div class="box box-primary">
	<div class="box-header with-border">
		<button class="btn btn-xs btn-danger pull-right back_btn"><i class="fa fa-times"></i> Close</button>
		<h3 class="box-title"><i class="fa fa-picture-o margin-r-5"></i>Upload Profile Image</h3>
	</div>
	<div class="box-body box-profile">		
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
<!-- /.box-body -->

<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>