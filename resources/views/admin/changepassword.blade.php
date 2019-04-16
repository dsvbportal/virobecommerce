@extends('admin.common.layout')
@section('pagetitle','Change Password')
@section('layoutContent')
<div class="row">
	<div class="col-md-12">
		<?php if (Session::has('success')){?>
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">
				<i class="ace-icon fa fa-times"></i>
			</button>
			<?php echo Session::get('success');?>		
			<br>
		</div>
		<?php } else if (Session::has('error')) {?>
		<div class="alert alert-danger">
			<button data-dismiss="alert" class="close" type="button">
				<i class="ace-icon fa fa-times"></i>
			</button>
			<?php echo Session::get('error');?>
			<br>
		</div>
		<?php }?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Change Password</h4>
			</div>
			<div class="panel_controls">				
				<form method="post" action="{{route('admin.settings.update-pwd')}}" class="form-horizontal" name="create_bank" id="changepassword" autocomplete="off">
					<div class="box-body">	
						<div class="form-group">
							<label  class="col-lg-2" for="current_password">Current Password</label>
							<div class="col-lg-4">
								<input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter current password" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2" for="new_password">New Password</label>
							<div class="col-lg-4">
								<input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter new password"  required>
							</div>
						</div>
						<div class="form-group">
							<label  class="col-lg-2" for="confirm_password">Confirm New Password</label>
							<div class="col-lg-4">
								<input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required >
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-offset-2 col-lg-4">
								<Button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>						
				</form>				
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#changepassword').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                confirm_password: {
                    equalTo: '#new_password'
                }
            },
            messages: {
                confirm_password: {
                    required: "Confirm password is required",
                },
                confirm_password:{
                    equalTo: "Confirm Password does not match"
                }
            },
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                    var controls = element.closest('div[class*="col-"]');
                    if (controls.find(':checkbox,:radio').length > 1)
                        controls.append(error);
                    else
                        error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                }
                else if (element.is('.select2')) {
                    error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
                }
                else if (element.is('.chosen-select')) {
                    error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
                }
                else
                    error.insertAfter(element.parent());
            },
            submitHandler: function (form, event) {
                event.preventDefault();
				CURFORM = $(form);
				if ($(form).valid()) {					
					$.ajax({
						type: 'POST',
						url: $(form).attr('action'),
						data: $(form).serialize(),
						beforeSend: function () {				
							$('button[type="submit"]',CURFORM).attr('disabled', true);
						},	
						success: function (op) {
							$('button[type="submit"]',CURFORM).attr('disabled',false);
							CURFORM.trigger('reset');
						}
					});
                }
                return false;
            },
            invalidHandler: function (form) {
            }
        });
    });
</script>
@stop
