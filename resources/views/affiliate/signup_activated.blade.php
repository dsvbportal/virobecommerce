@extends('affiliate.layout.signuplayout')
@section('page-title','Create Account')
@section('contents')
<header class="header">
  <a id="logo" href="{{route('aff.login')}}"><img src="{{asset('resources/assets/themes/affiliate/img/affiliate-logo.png')}}"></a>
</header>  
<div class="small-10 large-8 medium-8  small-centered columns signup text-center">		
	<div class="row">			
		@if(isset($valError))
			<h2 class='text-warning'><i class="fa fa-info-circle"></i> Sorry!</h2>
			<ul class="list">			
			@foreach($valError as $err)
				@foreach($err as $errtxt)
				<li>{{$errtxt}}</li>
				@endforeach
			@endforeach
			</ul>
		@elseif($status==200)
			<h2 class='text-success'><i class="fa fa-check-circle-o"></i> Congratulation!</h2>
			<p>Your acount activated successfully. Thank you for become a part of {{$siteConfig->site_name}}.</p>
			<p><a href="{{route('aff.login')}}">Click here</a> to login</p>
		@else
			<h2 class='text-warning'><i class="fa fa-info-circle"></i> Sorry!</h2>
			<p>Your registration session has been expired. Please do signup again.</p>
		@endif
	</div>
</div>
@stop