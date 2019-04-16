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
                                 <tr>              
									 <th>{{trans('affiliate/bonus/personal_commission.month')}}</th>
									 <th>CV</th>
									 <th>Slab</th>
									 <th>{{trans('affiliate/bonus/personal_commission.earnings')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.commission')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.tax')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.ngo_wallet')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.net_pay')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.status')}}</th>
								</tr>
                                                                               
                </tr>
            </thead>
            <tbody>
               
                @if($export_data != '' && isset($export_data))
                   @foreach ($export_data as $row)
                        <tr>                        
                     		<td class="text-left">{{$row->confirm_date}}</td>
							<td class="text-left">{{$row->total_cv}}</td>
							<td class="text-left">{{$row->slab}}</td>
							<td class="text-left">{{$row->earnings}}</td>
							<td class="text-left">{{$row->commission}}</td>
							<td class="text-left">{{$row->tax}}</td>
							<td class="text-left">{{$row->ngo_wallet}}</td>
							<td class="text-left">{{$row->net_pay}}</td>
							<td class="text-left"><span class="label label-{{$row->status_dispclass}}">{{$row->status}}</span></td>
                            
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