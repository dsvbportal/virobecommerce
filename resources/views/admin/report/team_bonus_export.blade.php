<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/team_bonus.t_bonus')}} - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
				<th>Month</th>
					 <th>User name</th>
					 <th>User Code</th>
					 <th>{{trans('affiliate/bonus/team_bonus.matching_qv')}}</th>
					 <th>{{trans('affiliate/bonus/team_bonus.earnings')}}</th>
					 <th>{{trans('affiliate/bonus/team_bonus.commission')}}</th>
					 <th>{{trans('affiliate/bonus/team_bonus.Tax')}}</th>
					 <th>{{trans('affiliate/bonus/team_bonus.ngo_wallet')}}</th>
					 <th>{{trans('affiliate/bonus/team_bonus.net_pay')}}</th>
					 <th>{{trans('affiliate/bonus/team_bonus.bonus_status')}}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($export_data != '' && isset($export_data))
                {
                    foreach ($export_data as $row)
                    {
                        ?>
                        <tr>                        
                            <td class="text-left"><?php echo $row->date_for;?></td>
                            <td class="text-right"><?php echo $row->uname;?></td>
                            <td class="text-right"><?php echo $row->user_code;?></td>
                            <td class="text-right"><?php echo $row->capping;?></td>
                            <td class="text-right"><?php echo $row->earnings;?></td>
                            <td class="text-right"><?php echo $row->income;?></td>
                            <td class="text-right"><?php echo $row->tax;?></td>
                            <td class="text-right"><?php echo $row->ngo_wallet_amt;?></td>
                            <td class="text-right"><?php  echo $row->paidinc;?></td>
                            <td class="text-right"><?php echo $row->status;?></td>
                        </tr>
                        <?php
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