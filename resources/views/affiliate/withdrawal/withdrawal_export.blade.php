<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>{{$status}} Withdrawal - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
               
								<th>{{trans('affiliate/withdrawal/history.requested_on')}}</th>  
                                <th>{{trans('affiliate/withdrawal/history.username')}}</th>
                                <th>{{trans('affiliate/general.country')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.payment_mode')}}</th>
                                <th>{{trans('affiliate/general.amount')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.charges')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.netpay')}}</th>
                                <th>{{trans('affiliate/general.status')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.expected_date_of_credit')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.updated_on')}}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($export_data != '')
                {
                    foreach ($export_data as $row)
                    {
                        ?>
                        <tr>
                        
                            <td class="text-left"><?php echo date("d-M-Y h:i:s", strtotime($row->created_on));?></td>
                            <td class="text-left"><?php echo $row->uname;?></td>
                            <td class="text-left"><?php echo $row->name; ?></td>
                            <td class="text-left"><?php echo $row->withdrawal_payout_type;?></td>
                            <td class="text-right"><?php echo $row->amount.'  '. $row->currency_code;?></td>
                            <td class="text-right"><?php echo $row->charges;?></td> 
                            <td class="text-right"><?php echo $row->paidamt.'  '.$row->currency_code;?></td>
                            <td class="text-center"><?php echo $row->status_label;?></td>
                            <td class="text-left"><?php if($row->expected_date!=NULL){echo date("d-M-Y", strtotime($row->expected_date));}?></td>
                            <td class="text-left"><?php echo date("d-M-Y h:i:s", strtotime($row->timeflag));?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
					echo "<tr><td colspan='10'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>