<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/ambassador_bonus.title')}} - <?php echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                  <tr>                                                    
					<th>{{trans('affiliate/general.sl_no')}}</th>
					<th class="text-center">{{trans('affiliate/bonus/ambassador_bonus.month')}}</th>
					 <th>{{trans('affiliate/bonus/ambassador_bonus.tot_cv')}}</th>
					 <th>{{trans('affiliate/bonus/ambassador_bonus.net_earnings')}}</th>
					 <th>{{trans('affiliate/bonus/ambassador_bonus.commission')}}</th>
					 <th>{{trans('affiliate/bonus/ambassador_bonus.ngo_wallet')}}</th>                                 
					 <th>{{trans('affiliate/bonus/ambassador_bonus.net_pay')}}</th>
				</tr>
            </thead>
           <tbody>
                <?php
                $i = 1;
                if ($print_data != '' && isset($print_data))
                {
					<?php $i=0;?>
                    foreach ($print_data as $row)
                    {
                        ?>
                        <tr>
							<td class="text-center">{{++$i}}</td>
							<td class="text-left"><?php echo $row->confirm_date; ?></td>							
                            <td class="text-left"><?php echo $row->earnings;?></td>
							<td class="text-left"><?php echo $row->net_earnings;?></td>
                            <td class="text-left"><?php echo $row->commission;?></td>
                            <td class="text-left"><?php echo $row->ngo_wallet;?></td>
                            <td class="text-left"><?php echo $row->net_pay;?></td>
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