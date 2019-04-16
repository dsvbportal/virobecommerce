@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.change_mobile_header')
@stop
@section('content')
	<div class="contentpanel">
		<div class="text-center ">

			<div id="img">
				<div class="col-md-12 col-xs-12  logo"> <!-- URL::asset('imgs/200/40/'.$pagesettings->site_logo) "http://localhost/dsvb_affiliate/resources/assets/imgs/affiliate-logo.png"-->
					<a href="{{URL::asset('/')}}"><img alt="{{$pagesettings->site_name}}" src="{{$pagesettings->site_logo}}" /></a>
				</div>

			</div>



			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<div id="phone_editfrm"></div>

						<div id="change_form_div">
						<form class="form-horizontal" id="change_mobil_form" action="{{route('ecom.update-mobile')}}" method="post" autocomplete="off">


							<div class="row col-sm-12">




								<div class="form-group col-sm-4">
								<label class=" control-label">Mobile<span class="danger"></span></label>

								<div class="">
									<div class="input-group">
										<span class="input-group-addon">{{ $log_data['phone_code'] }}</span>
										<input type="text" class="form-control text-center" name="old_mobile" id="old_mobile" placeholder="Mobile Phone"  value="{{ $log_data['mobile'] }}"  disabled>
{{--
--}}

									</div>
								</div>
							</div>


							<div class="form-group col-sm-4">
								<label class="control-label">New Mobile<span class="danger"></span></label>
								<div class="">
									<div class="input-group">
										<span class="input-group-addon">{{ $log_data['phone_code'] }}</span>

										<input  {!!build_attribute($cpfields ['new_mobile']['attr'])!!} type="text" id="new_mobile" class="form-control" name="new_mobile"  placeholder="Mobile Phone" data-err-msg-to="#new_password_err" onkeypress="return RestrictSpace(event)">


									</div>
									<span id="new_password_err"></span>

								</div>
							</div>
							<div class="form-group col-sm-2">
								<label class="control-label"> </label>
								<div class=" fieldgroup">
									<button name="submit" type="submit"  id="save_chng" class="btn btn-md btn-primary"><i class="fa fa-save"></i> Save
									</button>
								</div>
							</div>
							</div>
						</form>
						</div>

						<div id="otp_div" style="display: none">

						<form class="form-horizontal" id="otp_mobil_form" action="{{route('ecom.otp-validation')}}" method="post" autocomplete="off">


							<div class="form-group">
								<label class="col-sm-4 control-label">Verification Code<span class="danger"></span></label>
								<div class="col-sm-4">
										<input {!!build_attribute($otpfields ['mobile_otp']['attr'])!!} type="text" id="mobile_otp" class="form-control" name="mobile_otp"  placeholder="code" data-err-msg-to="#mobile_otp_err" onkeypress="return RestrictSpace(event)">
									<span id="mobile_otp_err"></span>
									<br/>
									<a href="#" id="resend_btn">Resend OTP</a>

								</div>
							</div>
							{{--<div class="form-group">

								<div class="col-sm-6">
									<span id="resend_btn">resend otp</span>
								</div>
							</div>
--}}
							<div class="form-group">
								<label class="col-sm-4 control-label"> </label>
								<div class="col-sm-4 fieldgroup">
									<button name="submit" type="submit"  id="save_chng" class="btn btn-md btn-primary"><i class="fa fa-save"></i> Save
									</button>
								</div>
							</div>

						</form>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

@stop

@section('scripts')
	<script type="text/javascript" src="{{asset('js/providers/ecom/account/profile.js')}}"></script>
@stop