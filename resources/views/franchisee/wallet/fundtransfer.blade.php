@extends('franchisee.layout.dashboard')
@section('title',"Fund Transfer")
@section('content')
	<section class="content-header">
      <h1><i class="fa fa-home"></i> {{\trans('affiliate/wallet/fundtransfer.page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/general.finance')}}</li>
        <li class="active">{{\trans('affiliate/wallet/fundtransfer.breadcrumb_title')}}</li>
      </ol>
    </section>
    <section class="content">        
		<div class="row" >        
            <div class="col-md-6">
		   <div class="box box-primary">
			@if($userdetails->status == 1)
		    <section class="panel">		  
                <div class="panel-body" id="fund-transfer">
						<div id="status_msg">
							<?php 
							if(session()->has('transafer_status')) {
								$err = session()->get('transafer_status');
								echo '<div class="alert alert-'.$err['msgClass'].'">'.$err['msg'].'</div>';
							} ?>
						</div>
                        <?php
						if($userdetails->status == 1 && $userdetails->block == 0 && $account_verif_count >= 1)
                        {
						?>
				                    <div class="box-header with-border">			
									<div class="form_fields">
				                    <style type="text/css">
					                	.hidefld,.hidefld1,.hidefld2,.hidefld3,#form_data{
						            	<?php
						            	if (empty($show_all))
						             	{
							                  	?>
								                 display:none;
								               <?php
							             }
							            else
							             {
								               ?>
								                 display:block;
								               <?php
						               	}
							            ?>
						}
						.help-block{
							color:#f56954;
						}
					</style>
				@if(!empty($wallet_balance))	
					 @include('franchisee.wallet.fund_transfer_form')					
				 @else	
						<div class="row">
							<div class="col-md-12"><div class="alert alert-danger">Insufficient Balance to transfer fund</div></div>
						</div>
				 @endif
                	</div>
					 <div id="confirm_form">
					 </div>
					</div>
					<?php
					}
					elseif ($account_verif_count == 0)
					{
						echo trans('affiliate/wallet/fundtransfer.kyc_document_msg');
					}
					else
					{
						echo trans('affiliate/wallet/fundtransfer.cant_transfer_fund');
					}
					?>
				</div>
			</section>
		@else
		<section class="panel">
				<div class="panel-body">
				<div id="status_msg">{{$msg}}</div>
			 </div>
		</section>
		@endif
		</div>
    </div>
	</div>
	</section>
 @stop
@section('scripts')
	<script src="{{asset('js/providers/franchisee/wallet/fundtransfer.js')}}"></script>  
	<script src="{{asset('channel-partner/validate/lang/fund_transfer')}}"></script>
	<script src="{{asset('js/providers/franchisee/wallet/other_functionalities.js')}}"></script>
@stop

