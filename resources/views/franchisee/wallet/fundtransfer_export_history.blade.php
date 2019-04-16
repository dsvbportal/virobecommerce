<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Fund Transfer History List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
						<th>{{trans('franchisee/wallet/fund_transfer_history.transfered_on')}}</th>  
						<th>{{trans('franchisee/wallet/fund_transfer_history.to_account')}}</th>
						<th>{{trans('franchisee/wallet/fund_transfer_history.type_of_user')}}</th>
						<th>{{trans('franchisee/general.amount')}}</th>   	                  
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
                            <td class="text-center"><?php echo $row->transferred_on;?></td>
                            <td class="text-center"><?php echo $row->Fto_name.' ('.$row->Fto_user_code.')';?></td>
                            <td class="text-center"><?php echo $row->type_of_user;?></td>
                            <td class="text-right"><?php echo $row->amount;?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
					echo "<tr><td colspan='8'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>