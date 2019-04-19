
					<form class="form-horizontal" id="change_email" action="<?php echo e(route('ecom.account.current_email_notify')); ?>" method="post" autocomplete="off">
						<div class="form-group">					
							<label class="col-sm-3 control-label">Current Email</label>
							<div class="col-sm-6">
								<div class="input-group">
									<input  type="text" disabled id="current_pwd" placeholder="<?php echo e($userinfo->email); ?>" class="form-control" value="" data-err-msg-to="#current_pwd_err" onkeypress="return RestrictSpace(event)">
									
								</div>
								<span id="current_pwd_err"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3"></label>
							<div class="col-sm-3 fieldgroup">
								<button type="submit" class="btn btn-primary" id="change_email_btn" name="submit"><i class="fa fa-save"></i> Change</button>
							</div>
						</div>
					</form>
                
