@extends('affiliate.layout.signuplayout')
@section('page-title','Create Account')
@section('contents')
<header class="header">
  <a id="logo" href="{{route('aff.login')}}"><img src="{{asset('resources/assets/themes/affiliate/img/affiliate-logo.png')}}"></a>
</header> 
<div class="row mtb-20">
	<div class="small-12 medium-12 columns">
	@if(isset($msg))		
		@if($msg=='success')
			<div class="msg-box msg-success text-center">
				<i class="fa fa-check-circle"></i>
				<h3>Your account activated!</h3>				
				<p class="text-center"><a href="{{route('aff.login')}}" class="btn btn-sm btn-primary">Login Now</a></p>
			</div>
		@elseif($msg=='danger')
			<div class="msg-box msg-danger text-center">
				<i class="fa fa-warning"></i>
				<h3>Invalid Access</h3>
				<p>Invalid email varification link.</p>
				<p class="text-center"><a href="{{route('aff.login')}}" class="btn btn-sm btn-primary">Back to Login</a></p>
			</div>
		@endif
	@endif
	</div>	
</div>