<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Leadership Bonus - <?php echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                 <tr>                                                    
					 <th class="text-center">{{trans('affiliate/bonus/leadership_bonus.month')}}</th>
					 <th>User name</th>
					 <th>User Code</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.matching_qv')}}</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.earnings')}}</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.commission')}}</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.tax')}}</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.ngo_wallet')}}</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.net_pay')}}</th>
					 <th>{{trans('affiliate/bonus/leadership_bonus.status')}}</th>
																   
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
                            <!--<td class="text-left"><?php //echo $row->account_id;?></td>-->
							<td class="text-left"><?php echo $row->date;?></td>
								<td class="text-left"><?php echo $row->uname;?></td>
							<td class="text-left"><?php echo $row->user_code;?></td>
                            <td class="text-left"><?php echo $row->capping;?></td>
                            <td class="text-left"><?php echo $row->earnings;?></td>
                            <td class="text-left"><?php echo $row->income;?></td>
                            <td class="text-left"><?php echo $row->tax;?></td>
                            <td class="text-left"><?php echo $row->ngo_wallet_amt;?></td>
                            <td class="text-left"><?php echo $row->Fpaidamt;?></td>
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