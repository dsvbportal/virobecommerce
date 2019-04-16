<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Wallet Summary Report - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
			     	<th>S.No</th>
                    <th  nowrap="nowrap">{{trans('admin/finance.user_name')}}</th>
                    <th>{{trans('admin/finance.currency')}}</th>
                    <th>{{trans('admin/finance.total_credit')}}</th>
                    <th>{{trans('admin/finance.total_debit')}}</th>
                    <th>{{trans('admin/finance.available_balance')}}</th>
                    <th>{{trans('admin/finance.wallet')}}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($wallet_summarry_details != '' && isset($wallet_summarry_details))
                {
                    foreach ($wallet_summarry_details as $row)
                    {
                        ?>
                        <tr>
                            <td nowrap><?php echo $i;?></td>
                            <td><?php echo $row->full_name;?></td>
                            <td><?php echo $row->currency_code;?></td>
                            <td><?php echo $row->tot_credit;?></td>
                            <td><?php echo $row->tot_debit;?></td>
                            <td><?php echo $row->current_balance;?></td>
                            <td><?php echo $row->wallet;?></td>
                        </tr>
                        <?php
                        $i++;
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