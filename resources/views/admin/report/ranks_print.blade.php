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
        
           <table id="ranks_table" border="1" class="table table-bordered table-striped">
				   <thead>
						<tr>                                                    
							 <th class="text-left">Month</th>
							 <th class="text-left">Full Name</th>
							 <th class="text-left">User Name</th>
							 <th class="text-left">User Code</th>
							 <th class="text-left">Rank</th>
							 <th class="text-left">Country</th>
							 <!--<th class="text-left">Your Current Rank</th>-->
							 <th class="text-right">GQV - 1G</th>							     
							 <th class="text-right">GQV - 2G</th>
							 <th class="text-right">GQV - 3G</th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($ranks))
							@foreach($ranks as $rank)
								<tr>
								<td>{{$rank->created_on}}</td>
								<td>{{$rank->fullname}}</td>
									<td>{{$rank->uname}}</td>
									<td>{{$rank->user_code}}</td>
									<td>{{$rank->rank}}</td>
									<td>{{$rank->country}}</td>
									<td align="right">{{$rank->gen_1}}</td>
									<td align="right">{{$rank->gen_2}}</td>
									<td align="right">{{$rank->gen_3}}</td>
								</tr>
							@endforeach
						@endif		
					</tbody>
				</table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">{{trans('affiliate/general.print_btn')}}</button>