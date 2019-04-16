@extends('affiliate.layout.dashboard')
@section('title',"Fund Transfer")
@section('content')
		
	<section class="content-header">
      <h1><i class="fa fa-home"></i> {{\trans('affiliate/wallet/fundtransfer.page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Wallet</li>
        <li class="active">{{\trans('affiliate/wallet/fundtransfer.breadcrumb_title')}}</li>
      </ol>
    </section>
    <section class="content">  
      <div class="row"  id="kycgrid">        
            <div class="col-md-6">
		   <div class="box box-primary">
			@if($userdetails->status == 1)
		    <section class="panel">
		  
                    <div class="panel-body">
						<div id="status_msg">
						<?php 
						if(session()->has('transafer_status')) {
							$err = session()->get('transafer_status');
							echo '<div class="alert alert-'.$err['msgClass'].'">'.$err['msg'].'</div>';
						} ?>
						</div>
                        <?php
						if($userdetails->status == 1 && $userdetails->block == 0 && $account_verif_count >= 1	 &&
						$userdetails->account_type_id == config('constants.ACCOUNT_TYPE.USER') && ($userdetails->is_affiliate == 1))
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
					 @include('affiliate.wallet.fund_transfer_form')
					
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
   <script>
	 $(document).ready(function () {
		  function get_decimal_value(amt) {
				   var amt = amt.toString();
				   var decimal_places = 2;
				   var decimal_val = amt.split('.');
				   if (decimal_val[1] !== undefined) {
				if (decimal_val[1].length > 2) {
					decimal_places = (decimal_val[1].length);
							if (decimal_places > 8) {
								decimal_places = 8;
							}
						}
					}
				return decimal_places;
			};
		  var wcb = JSON.parse('{{$currency or ''}}'.replace(/&quot;/g,'"'));
		  var fts = JSON.parse('{{$fund_trasnfer_settings or ''}}'.replace(/&quot;/g,'"'));
		 
		 console.log(wcb);
		 
		 
          $(document.body).on('change', '#wallet_id', function () {
			    avi_bal = 0;
                $('#currency_id').html('<option value="">' +$curr_sel+ '</option>');
                var wallet_id = $(this).val();
				$('.hidefld1').hide();
				$.each(wcb, function (key, ele) {
                    if (wallet_id == ele.wallet_id) {
                        $('#currency_id').append('<option value="' + ele.id + '">' + ele.code + '</option>');
                    }
                });
				if (wallet_id == '')
                {
                    $('.hidefld').hide();
                    $('.hidefld1').hide();
                    $('.hidefld2').hide();
                    $('.hidefld3').hide();
                }
                else
                {
                    $('.hidefld').show();
                }
                 
            });
			
			    $('.hidefld1').hide();
                $('.hidefld2').hide();
                $('.hidefld3').hide();
                var avi_bal = 0;
                var min_amount = 1;
                var max_amount = 1;
				var balcurcy_code = '';
                balcurcy_code = $('option:selected', $(this)).text();
                $('#totamount').val('');
			
				$.each(wcb, function (key, ele) {				
							avi_bal = ele.current_balance;
                });
				
                $.each(fts, function (key, ele) {
		                min_amount = ele.min_amount;
                        max_amount = ((ele.max_amount > parseFloat(avi_bal)) ? avi_bal : ele.max_amount);
                        return;
                });
                if (max_amount == 1) {
                    max_amount = avi_bal;
                }
             var decimal_places = get_decimal_value(avi_bal);
			 if (avi_bal == '') {
				    //$('#user_balance').text(parseFloat(avi_bal).toFixed(decimal_places));
                    $('#user_avail_bal').val(parseFloat(avi_bal).toFixed(decimal_places));
                    $('#min_trans_amount').val(min_amount);
                    $('#max_trans_amount').val(max_amount);
                } else {
                    if (avi_bal >= 0) {
						   $('.hidefld1').show();
                       // $('#user_balance').text(parseFloat(avi_bal).toFixed(decimal_places) + ' ' + balcurcy_code);
                        $('#d_user_balance').text(parseFloat(avi_bal).toFixed(decimal_places) + ' ' + balcurcy_code);
                        $('#user_avail_bal').val(parseFloat(avi_bal).toFixed(decimal_places));
                        $('#min_trans_amount').val(min_amount);
                        $('#max_trans_amount').val(max_amount);
                        $(".err_msg3").remove();
                        $(".err_msg2").remove();
                    } else {
                        $('#user_balance').text(0.00);
                        $('#user_avail_bal').val(0.00);
                        $('#max_trans_amount').val(0.00);
                        $('#min_trans_amount').val(0.00);
                        avi_bal = 0;
                    }
                }
	   });
	</script>

<script>var balArray = new Array();</script>	
	<script type="text/javascript" src="{{asset('affiliate/validate/lang/fund_transfer.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/providers/affiliate/wallet/other_functionalities.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/providers/affiliate/wallet/fundtransfer.js')}}"></script>  
	@stop