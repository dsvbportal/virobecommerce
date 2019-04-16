<?php include('assets/user/css/print_style.css');?>
<script>
    function myFunction() {
        window.print();
    }
</script>
<style type="text/css" media="print">
    table tr td{
        border-collapse:collapse;
        padding:5px 5px;
    }
    .noprint{
        display:none;
    }
</style>
<?php
//foreach ($data as $key => $val)
//    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{$status}} Withdrawal - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                        <th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.withdraw_date')}}</th> 
						<th nowrap="nowrap">{{trans('general.transaction_id')}}</th> 
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.amount')}}</th> 
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.payment_mode')}}</th>
						<th nowrap="nowrap">{{trans('affiliate/withdrawal/withdrawal.status')}}</th>
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
                            <td class="text-center"><?php echo $row->created_on;?></td>
							<td class="text-center"><?php echo $row->transaction_id;?></td>
                            <td class="text-right"><?php echo $row->amount;?></td>
                            <td class="text-center"><?php echo $row->payment_type;?></td> 
                            <td class="text-center"><?php echo $row->status;?></td>
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
<button class="noprint" onClick="myFunction()">Print</button>