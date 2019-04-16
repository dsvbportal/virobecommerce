@extends('affiliate.layout.dashboard')
@section('title','Refer & Earn')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-home"></i>Referrals</h1>
     
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Referrals</li>
        <li class="active">Refer & Earn</li>
      </ol>
    </section>
   <!-- Main content -->
    <section class="content">  
		<div class="row">
		<div class="col-md-9">
			<!-- Small boxes (Stat box) -->					
			<div class="row mb-3" id="referprog">
				<div class="col-md-4">
					<div class="card">
						<div class="card-header text-center"><i class="refer-icon refer-share"></i></div>			
						<div class="card-body text-center">
							<h3 class="text-center">Share your link</h3>
							<div>with your friends via email, Facebook, Twitter and more!</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card">		
						<div class="card-header text-center"><i class="refer-icon refer-reg"></i></div>	
						<div class="card-body text-center">
							<h3 class="text-center">Friends Signs Up</h3>					
							<div>and earn Group Affiliate Income.!</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card">		
						<div class="card-header text-center"><i class="refer-icon refer-earn"></i></div>	
						<div class="card-body text-center">
							<h3 class="text-center">You earn</h3>					
							<div>based on the customer shopping and team’s Commission or Qualified Volume !</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">        
				<div class="col-md-12">
					<img src="{{asset('resources/assets/themes/affiliate/img/referral-program.jpg')}}" width="100%">
					<div class="card">		
						<div class="card-body">
							<div class="reflink-sharebox">
								<p class="text-center"><b>Share your referral link:</b></p>
								<div class="input-group input-info">
								     <input id="referral-link" type="text" class="form-control" readonly value="{{route('aff.signup',['referralname'=>$userSess->uname])}}" >
									 <span class="input-group-btn">
									   <button class="btn btn-success" id="reflink_cpybtn" type="button">Copy link</button>
									 </span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">								
									<h4>Send referral link to your friend's email</h4>
									<div class="input-group input-info">
									    <input type="text" class="form-control" id="invite_email" value="" placeholder="Enter your friend email" onkeypress="return RestrictSpace(event)">
										<span class="input-group-btn">
										    <button class="btn btn-success" id="invite_now" data-url="{{route('aff.referrals.refer-and-earn')}}" data-refurl="{{route('aff.signup',['referralname'=>$userSess->uname])}}" type="button">Invite Now</button>
										</span>
									</div>	
									<span class="errmsg" id="invite_email_err"></span>
								</div>	
								<div class="col-md-4 col-md-offset-2">								
									<h4 class="text-center">(or) share via</h4>
									<div class="row">
										<div class=" col-md-6">
											<a class="btn btn-block btn-social btn-facebook" onclick="_gaq.push(['_trackEvent', 'btn-social', 'click', 'btn-facebook']);">
												<span class="fa fa-facebook"></span> Facebook
											</a>
										</div>
										<div class=" col-md-6">
											<a class="btn btn-block btn-social btn-twitter col-md-6" onclick="_gaq.push(['_trackEvent', 'btn-social', 'click', 'btn-twitter']);">
												<span class="fa fa-twitter"></span> Twitter
											</a>
										</div>	
									</div>								
								</div>
							</div>
						</div>
					</div>					
				</div>				
			</div>			
		</div>		
		<div class="col-md-3">
			<img src="{{asset('resources/assets/themes/affiliate/img/refer-friends.jpg')}}" width="100%">
			<div class="card">		
				<div class="card-body">
					<p>Everyone's a winner; we get the opportunity to expand our customer base, your friend or colleague gets the same great products and services you - and you make money through affiliate program.</p>
					<div class="well">
						<h4 class="text-center">My invitation code</h4>
						<h2 class="text-center text-success"><b>VOBIN56767</b></h2>
						<input id="referral-code" type="hidden" value="VOBIN56767"/> 
						<div class="form-group">
						   <button class="btn btn-block btn-danger" id="refcode_cpybtn" type="button">Copy Invitation Code</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>	
    </section>
    <!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/referrals/refer_and_earn.js')}}"></script>
@stop