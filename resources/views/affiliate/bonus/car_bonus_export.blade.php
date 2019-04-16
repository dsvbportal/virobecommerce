
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Car Bonus Report  - <?php echo getGTZ(null,'Y-M-d'); ?></h1><br/>        
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
                if ($export_data != '' && isset($export_data))
                {
                    foreach ($export_data as $row)
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
					echo "<tr><td colspan='6'>{{trans('user/general.no_records_found')}}</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>