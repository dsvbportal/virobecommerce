@extends('affiliate.layout.dashboard')
@section('title',"Dashboard")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="row">
			<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="card-panel border-info border-left-sm">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-left">    
                <h4 class="text-info"><span>Welcome</span> {{strtok($userSess->full_name,' ')}}</h4>
                <span>Username: <b>{{$userSess->uname}}</span>
              </div>
            </div>
            </div>
          </div>
			</div>	
			<div class="col-xs-12 col-sm-3 col-md-4  col-lg-3 text-center">
			<div class="card-panel border-danger border-left-sm">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-left">
               
                <h4 class="text-danger">Account ID</h4>
                <span>{{$userSess->user_code}}</span>
                
              </div><div class="align-self-center w-circle-icon rounded-circle gradient-orange">
                <i class="fa fa-key text-white"></i></div>
            </div>
            </div>
            
          </div>
			</div>
			<div class="col-xs-12 col-sm-3 col-lg-3">  
			
			 <div class="card-panel border-success border-left-sm">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-left">
               
                <h4 class="text-success">Current Rank</h4>
                <span>{{$userSess->pro_rank}}</span>
                
              </div>
              <div class="align-self-center w-circle-icon rounded-circle gradient-quepal">
                <i class="fa fa-trophy text-white"></i></div>
            </div>
            </div>
          </div> 
			</div>
		
			<div class="col-lg-3 col-xs-12">
			 <div class="card-panel border-primary border-left-sm">
            <div class="card-body">
              <div class="media">
              <div class="media-body text-left">
               
                <h4 class="text-primary">Your Last Login</h4>
                <span>{{$userSess->last_logged_time}}</span>
                
              </div>
              <div class="align-self-center w-circle-icon rounded-circle gradient-purpink">
                <i class="fa fa-clock-o text-white"></i></div>
            </div>
            </div>
          </div> 
			</div>
			<!-- END Top Stats -->
		</div>      
    </section>
	<section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">
			<div class="col-md-6">
				<div class="ref-link">
					<div class="no-float text-center">
						<h3>Your Affiliate Referral Link</h3>
						<input id="referral-link" type="hidden" class="form-control" readonly value="{{route('aff.signup',['referralname'=>$userSess->uname])}}" >
						<button type="button" class="but" id="reflink_cpybtn">
							</i>{{route('aff.signup',['referralname'=>$userSess->uname])}}</button>
						<div>
							<div class="col-md-5"><img src="{{asset('resources/assets/themes/affiliate/dist/img/refer.svg')}}"></div>
							<div class="col-md-6 col-xs-offset-1">
								<a class="facebok-share" href="javascript:void(0);" style="white-space: nowrap; display:block"><i class="fa fa-facebook text-white"></i> Share on Facebook</a>
								<a class="twitter-share" href="javascript:void(0);"  style="white-space: nowrap; display:block"><i class="fa fa-twitter text-white"></i> Share on Twitter</a>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="ref-link">
					<h3>Your unique referral link for PayGyft</h3>
					<button type="button" class="but">
						</i>www.virob.com</button>
					<div class="clearfix"></div>
					<button type="button" class="btn bg-olive but-copy">
						</i>Copy Link</button>
					<div class="copy-ref">
						<input placeholder="Enter your friend email" value="" type="text">
						<button type="button" class="btn btn-sm bg-orange"></i>SHARE</button>
					</div>
					<div class="pad-share">				
						<a class="facebok-share" href="javascript:void(0);" onclick="_gaq.push(['_trackEvent', 'btn-social', 'click', 'btn-facebook']);"><i class="fa fa-facebook text-white"></i> Share on Facebook</a>
						<a class="twitter-share" href="javascript:void(0);" onclick="_gaq.push(['_trackEvent', 'btn-social', 'click', 'btn-twitter']);"><i class="fa fa-twitter text-white"></i> Share on Twitter</a>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
     </section>
    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
		<div class="col-md-4">
			<!-- Advanced Active Theme Color Widget -->
			<div class="widget">
				<div class="widget-advanced">
					<div class="widget-header text-center bg-blue" style="padding: 4px 15px 33px 6px;">	<h3 class="widget-content-light">Direct Affiliates</h3></div>					
					<div class="widget-main dash" style="padding: 43px 9px 0 15px;">
						<a class="widget-image-container  bg-aqua" href="javascript:void(0)">
							<span class="widget-icon themed-background"><small>today</small><br>{{$referral_today}}</span>
						</a>
						<table class="table table-borderless table-striped table-condensed table-vcenter">
							<tbody>
								<tr>
									<td style="width:70%;"><strong>This Week</strong> </td>
									<td class="text-right">{{$referral_week}}</td>
								</tr>
								<tr>
									<td><strong>This Month</strong> </td>
									<td class="text-right">{{$referral_month}}</td>
								</tr>
								<tr> 
								<td><strong>All Time</strong> </td>
									<td class="text-right">{{$referral_total}}</td>
								</tr>
							   
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<!-- Advanced Active Theme Color Widget -->
			<div class="widget">
				<div class="widget-advanced">
					<div class="widget-header text-center bg-blue" style="padding: 4px 15px 33px 6px;">	<h3 class="widget-content-light">Team Affiliates</h3></div>					
					<div class="widget-main dash" style="padding: 43px 9px 0 15px;">
						<a class="widget-image-container  bg-aqua" href="javascript:void(0)">
							<span class="widget-icon"><small>today</small><br>{{$team_referral_today}}</span>
						</a>
						<table class="table table-striped">
							<tbody>
								<tr>
									<td style="width:70%;"><strong>This Week</strong> </td>
									<td class="text-right">{{$team_referral_week}}</td>
								</tr>
								<tr>
									<td><strong>This Month</strong> </td>
									<td class="text-right">{{$team_referral_month}}</td>
								</tr>
								<tr> 
								<td><strong>All Time</strong> </td>
									<td class="text-right">{{$team_referral_total}}</td>
								</tr>							   
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="widget">
				<div class="widget-advanced">
					<div class="widget-header bg-maroon">
						<h4>Your Account Balance ({{$userSess->currency_code}})</h4>
					</div>
					<div class="widget-extra-full">              
						@if(!empty($balInfo))
						<table class="table table-striped"  style="margin:0px">
						<tbody>
						@foreach($balInfo as $balance)
						<tr>
							<th style="width:60%;">{{$balance->wallet_name}}</th>
							<td class="text-right text-green">{{$balance->current_balance}}</td>
						</tr>
						@endforeach
						</tbody>
						</table>
						@endif
					</div>
				</div>
			</div>
		<!--	<a href="{{route('aff.addfund')}}" class="btn btn-block btn-lg btn-social bg-green">
                <i class="fa fa-plus-circle"></i> Add Fund
              </a>-->
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="widget">
				<div class="widget-advanced">
					<div class="widget-header bg-maroon">
						<!--<h4>COMMISSION VOLUME (CV) - REPURCHASE</h4>-->
						<h4>COMMISSION VOLUME (CV)</h4>
					</div>
					<div class="widget-extra-full">              
						<table class="table table-bordered table-striped">
						<thead>						
						<tr>
							<th style="width:50%;" valign="middle" class="text-center" >Last Month</th>
							<th style="width:50%;" valign="middle" class="text-center" >Current Month</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td  class="text-center">{{!empty($cv->last) ? $cv->last->total_cv : 0}}</td>
							<td class="text-center">{{!empty($cv->current) ? $cv->current->total_cv : 0}}</td>
						</tr>						
						</tbody>
						</table>
					</div>
				</div>
			</div>			
		</div>
		<div class="col-md-4">
			<div class="widget">
				<div class="widget-advanced">
					<div class="widget-header bg-maroon">
						<h4>QUALIFIED VOLUME (QV)</h4>
					</div>
					<div class="widget-extra-full">
						<table class="table table-bordered table-striped">
						<thead>						
						<tr>
							<th style="width:50%;" valign="middle" class="text-center" >Last Month</th>
							<th style="width:50%;" valign="middle" class="text-center" >Current Month</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td class="text-center">{{(!empty($qv->last) && $qv->last) ? $qv->last : 0}}</td>
							<td class="text-center">{{!empty($qv->current && $qv->current) ? $qv->current : 0}}</td>
						</tr>						
						</tbody>
						</table>						
					</div>
				</div>
			</div>						
		</div>
		<!-- div class="col-md-4">
			<div class="widget">
				<div class="widget-advanced">					
					
					<div class="widget-header bg-maroon">
						<div class="widget-options">
							<a href="{{route('aff.withdrawal.history')}}" class="btn btn-sm btn-default pull-right">View History</a>
						</div>
						<h4>Payments</h4>
					</div>
					<div class="widget-extra-full">
						<table class="table table-bordered table-striped" style="margin:0" >
						<thead>						
						<tr>
							<th style="width:50%;" valign="middle" class="text-center">Approved <i class="fa fa-question-circle text-orange"></i> <br><small class="text-muted">(From last payment)</small></th>
							<th style="width:50%;"valign="middle" class="text-center">Pending <i class="fa fa-question-circle text-orange"></i></th>							
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><h3 class="text-center"></h3></td>
							<td><h3 class="text-center text-yellow"></h3></td>
						</tr>						
						</tbody>
						</table>						
					</div>
				</div>
			</div>						
		</div -->		
	</div>
  <!-- /.row -->    
</section>
<!-- /.content -->
@stop
@section('scripts')
@stop
