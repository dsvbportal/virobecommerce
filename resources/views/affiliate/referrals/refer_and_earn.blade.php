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
		<div class="col-md-12 refer_earn">
      <img src="{{asset('resources/assets/themes/affiliate/dist/img/refer&earn.jpg')}}" width="100%">
      <div class="col-md-12">	  
      <div class="ref-link">		
       <h3>Your unique referral link for PayGyft</h3>	   
       <button type="button" class="but">
         www.virob.com</button>
       <div class="clearfix"></div>
       <button  id='invtrflfloatbtn' type="button" class="btn bg-olive but-copyss">
         Copy Link</button>
      </div>
    </div>
	<div class="col-md-12">
		<div class="col-md-6"><img src="{{asset('resources/assets/themes/affiliate/dist/img/customer-referral-scheme.svg')}}" width="100%"></div>
			<div class="col-md-6 invite"><div class="card">
				<div class="card-body">
					<p>Everyone's a winner; we get the opportunity to expand our customer base, your friend or colleague gets the same great products and services you - and you make money through affiliate program. </p>
					<div class="well">
						<h4 class="text-center">Your invitation code</h4>
						<h2 class="text-center text-success"><b>{{$userSess->referral_code or ''}}</b></h2>
						<input id="referral-code" type="hidden" value="{{$userSess->referral_code or ''}}"/>
						<div class="form-group">
						   <button class="btn btn-block btn-danger" id="refcode_cpybtn" type="button">Copy Invitation Code</button>
						</div>
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
<script>    
var invite_referrals = window.invite_referrals || {}; (function() { 
    invite_referrals.auth = { 
        bid_e : 'FAEE1A16F67821DB3F7E8B7D3E886134',
        bid : '22261',
        t : '420',
        email : 'gopintg@gmail.com',
        mobile : '',
        userParams : {'fname': 'Gopi', 'lname': 'Nagarajan', 'birthday': '', 'gender': ''},
        referrerCode : '',
        orderID : '', purchaseValue : '',
        userCustomParams : {'customValue': '', 'shareLink': '', 'shareTitle': '', 'shareDesc': '', 'shareImg': ''},
        showWidget : ''
    };  
var script = document.createElement('script');script.async = true;
script.src = (document.location.protocol == 'https:' ? "//d11yp7khhhspcr.cloudfront.net" : "//cdn.invitereferrals.com") + '/js/invite-referrals-1.0.js';
var entry = document.getElementsByTagName('script')[0];entry.parentNode.insertBefore(script, entry); })();
</script>
@stop