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
        <h1 style="text-align:center">Star Bonus Report - <?php  echo getGTZ(null,'Y-M-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<th>Qualified Month </th>
					<th>Rank</th>
					<th>Commission</th>
					<th>Tax</th>
					<th>NGO Wallet</th>
					<th>Net Pay</th>
					<th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($print_data != '' && isset($print_data))
                {
                    foreach ($print_data as $row)
                    {
                        ?>
                        <tr>                        
                            <td class="text-left"><?php echo $row->bonus_date;?></td>
                            <td class="text-left"><?php echo $row->rank;?></td>
                            <td class="text-right"><?php echo $row->commission;?></td>
                            <td class="text-right"><?php echo $row->tax;?></td>
                            <td class="text-right"><?php echo $row->vi_help;?></td>
                            <td class="text-right"><?php echo $row->net_pay;?></td>
                            <td class="text-center"><?php echo $row->status;?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
				else {
					echo "<tr><td colspan='6'>{{trans('affiliate/general.no_records_found')}}</td></tr>";
				} 
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">Print</button>