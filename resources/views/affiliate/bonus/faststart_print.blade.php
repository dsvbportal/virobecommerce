<?php include('assets/user/css/print_style.css');?>
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
<?php
//foreach ($data as $key => $val)
//    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">My Fast Start Bonus List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
							<th>{{trans('affiliate/general.sl_no')}}</th>
							<th>{{trans('affiliate/bonus/faststart.dateofpurchase')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.username')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.packagename')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.Earnings')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.Commission')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.ngo')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.netpay')}}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                if ($print_data != '' && isset($print_data))
                {
                    foreach ($print_data as $row)
                    {	
                        ?>
                         <tr>                
                            <td class="text-center">{{++$i}}</td>
							<td class="text-left">{{$row->created_date}}</td> 
							<td class="text-left">{{$row->full_name."(".$row->from_user_code.")"}}</td>
                            <td class="text-left"><b>{{$row->package_name}}</b><br>Price: <b>{{$row->Famount}}</b>, QV: {{$row->qv}}</td>                            
                            <td class="text-left">{{$row->earnings}}</td> 
                            <td class="text-right">{{$row->commission}}</td>
                            <td class="text-left">{{$row->ngo_wallet_amt}}</td>
                            <td class="text-left">{{$row->net_pay}}</td>
                        </tr>
                        <?php
                    }
                 }
				 else {
					echo "<tr><td colspan='9'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">Print</button>