
						<form class="form-horizontal" id="change_mobil" action="" method="post" autocomplete="off">

							<div class="form-group">
								<label class="col-sm-3 control-label">Mobile<span class="danger"></span></label>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">{{$logged_userinfo->phone_code or ''}}</span>

										<input type="text" class="form-control" name="mobile"  placeholder="Mobile Phone"  value="{{$logged_userinfo->mobile or ''}}"  disabled>
{{--
--}}
										<span class="input-group-addon"><a id="changeMobileButn" href="{{route('ecom.account.send-email')}}" title="Change Mobile"><i class="fa fa-edit"></i></a></span>

									</div>
								</div>
							</div>


						</form>
					