<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/personal_commission.per_commission')}} - <?php echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                  <tr>                                                    
                                 <th class="text-center">{{trans('affiliate/bonus/ambassador_bonus.period')}}</th>
                                 <th>User Name</th>
                                 <th>User Code</th>
                                 <th>{{trans('affiliate/bonus/ambassador_bonus.slab')}}</th>
								 <th>{{trans('affiliate/bonus/ambassador_bonus.total_cv')}}</th>							     
                                 <th>{{trans('affiliate/bonus/ambassador_bonus.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/ambassador_bonus.tax')}}</th>
								 <th>{{trans('affiliate/bonus/ambassador_bonus.ngo_wallet')}}</th>                                 
                                 <th>{{trans('affiliate/bonus/ambassador_bonus.net_pay')}}</th>
								 <th>{{trans('affiliate/general.status')}}</th>
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
                        
							<td class="text-left"><?php echo $row->confirm_date; ?></td>
							<td class="text-left"><?php echo $row->uname; ?></td>
							<td class="text-left"><?php echo $row->user_code; ?></td>
                            <td class="text-left"><?php echo $row->slab;?></td>
                            <td class="text-left"><?php echo $row->earnings;?></td>
                            <td class="text-left"><?php echo $row->commission;?></td>
                            <td class="text-left"><?php echo $row->tax;?></td>
                            <td class="text-left"><?php echo $row->ngo_wallet;?></td>
                            <td class="text-left"><?php echo $row->net_pay;?></td>
                            <td class="text-left"><?php echo $row->status;?></td>
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