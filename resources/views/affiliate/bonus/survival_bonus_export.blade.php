
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/Survival_bonus.title')}}  - <?php echo getGTZ(null,'Y-M-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<th>{{trans('affiliate/bonus/Survival_bonus.qualified_month')}} </th>
					<th>{{trans('affiliate/bonus/Survival_bonus.rank')}} </th>
					<th>{{trans('affiliate/bonus/Survival_bonus.commission')}} </th>
					<th>{{trans('affiliate/bonus/Survival_bonus.tax')}} </th>
					<th>{{trans('affiliate/bonus/Survival_bonus.ngo_wallet')}} </th>
					<th>{{trans('affiliate/bonus/Survival_bonus.net_pay')}} </th>
					<th>{{trans('affiliate/bonus/Survival_bonus.status')}} </th>
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
					echo "<tr><td colspan='6'>{{trans('affiliate/general.no_records_found')}}</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>