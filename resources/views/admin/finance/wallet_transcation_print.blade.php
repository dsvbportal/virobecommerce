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
        <h1 style="text-align:center">Wallet Transcation - <?php echo date("d-M-Y");?></h1><br/>
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
							<tr>                                                    
                               <th class="text-left">{{trans('admin/finance.wallet_transcation.created_on')}}</th>
                                <th class="text-left">{{trans('admin/finance.wallet_transcation.full_name')}}</th>
                                <th class="text-left">{{trans('admin/finance.wallet_transcation.remarks')}}</th>
                                <th class="text-left">{{trans('admin/finance.wallet_transcation.wallet')}}</th>
                                <th class="text-left">{{trans('admin/finance.wallet_transcation.transaction_id')}}</th>
								<th class="text-left">{{trans('admin/finance.wallet_transcation.cr_amt')}}</th>
						        <th class="text-left">{{trans('admin/finance.wallet_transcation.dr_amt')}}</th>
                                <th class="text-left" >{{trans('admin/finance.wallet_transcation.balance')}}</th>
                            </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($wallet_transcation_details != '' && isset($wallet_transcation_details))
                {
                    foreach ($wallet_transcation_details as $row)
                    {
                        ?>
                         <tr>                        
                             <td class="text-left">{{$row->created_on}}</td>
                             <td class="text-left">{{$row->fullname}}</td>
                             <td class="text-left">{{$row->remark}}</td>
                             <td class="text-left">{{$row->wallet}}</td> 
                             <td class="text-left">{{$row->transaction_id}}</td> 
							 <td class="text-left">{{$row->CR_Fpaidamt}}</td>
                             <td class="text-left">{{$row->DR_Fpaidamt}}</td>
                            <td class="text-left">{{$row->Fcurrent_balance}}</td> 
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