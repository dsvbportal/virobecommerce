<?php 
include('assets/user/css/print_style.css');?>
<script>
    function myFunction() {
        window.print();
    }
</script>
<style type="text/css" media="print">
    table tr td{
        border-collapse:collapse;
        padding:5px 5px;
    }
    .noprint{
        display:none;
    }
</style>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/personal_commission.per_commission')}} - <?php  echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        
         <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>                                                                                                    
									 <th>{{trans('affiliate/general.sl_no')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.month')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.directs_cv')}}</th>
						<!--		 <th>{{trans('affiliate/bonus/personal_commission.self_cv')}}</th> -->
									 <th>{{trans('affiliate/bonus/personal_commission.slab')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.earnings')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.commission')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.ngo_wallet')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.net_pay')}}</th>
								<!-- <th>Details</th>-->
                </tr>
            </thead>
            <tbody>
               
                @if($export_data != '' && isset($export_data))
					<?php $i=0; ?>
                   @foreach ($export_data as $row)
                        <tr>   
							<td class="text-center">{{++$i}}</td>
                     		<td class="text-left">{{$row->confirm_date}}</td>
                     		<td class="text-left">{{$row->direct_cv}}</td>
                    <!-- 	<td class="text-left">{{$row->self_cv}}</td>  -->
							<td class="text-left">{{$row->slab}}</td>
							<td class="text-left">{{$row->earnings}}</td>
							<td class="text-left">{{$row->commission}}</td>
							<td class="text-left">{{$row->ngo_wallet}}</td>
							<td class="text-left">{{$row->net_pay}}</td>
                        </tr>
                   
					@endforeach
				@else
					<tr><td colspan='6'>{{trans('affiliate/general.no_records_found')}}</td></tr>
				@endif
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">{{trans('affiliate/general.print_btn')}}</button>