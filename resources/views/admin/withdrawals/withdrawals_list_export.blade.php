<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Withdrawal List- <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
								<tr>                                                    
                                <th  nowrap="nowrap">{{trans('admin/withdrawals.request_on')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.uname')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.country')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.payment_mode')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.currency')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.amount')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.charges')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.net_pay')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.expected_date_credit')}}</th>
								<th  nowrap="nowrap">{{trans('admin/withdrawals.updated_on')}}</th>
							    <th  nowrap="nowrap">{{trans('admin/withdrawals.payment_details')}}</th>
                            </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($pending_withdrawals_list != '' && isset($pending_withdrawals_list))
                {
                    foreach ($pending_withdrawals_list as $row)
                    {
                        ?>
                        <tr>                        
                            <td class="text-left">{{$row->created_on}}</td>
                            <td class="text-left">{{$row->fullname.'('.$row->uname.')'}}</td>
                            <td class="text-left">{{$row->country}}</td>
                            <td class="text-left">{{$row->payment_type}}</td>
                            <td class="text-left">{{$row->code}}</td>
                            <td class="text-left">{{$row->amount}}</td>
                            <td class="text-left">{{$row->handleamt}}</td>
                            <td class="text-left">{{$row->paidamt}}</td>
                            <td class="text-left">{{$row->expected_on}}</td>
                            <td class="text-left">{{$row->updated_on}}</td>
                            <td class="text-left">{{$row->payment_status}}</td>
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