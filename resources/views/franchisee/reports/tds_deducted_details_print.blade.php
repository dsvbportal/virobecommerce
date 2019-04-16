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
        <h1 style="text-align:center">TDS Deducted Report - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<tr>                                                    
						<th>{{trans('franchisee/general.date')}}</th>
                        <th>{{trans('franchisee/wallet/transactions.description')}}</th>
                        <th>{{trans('franchisee/wallet/transactions.paymode')}}</th>                              
						<th>{{trans('franchisee/general.amount')}}</th>
						<th>{{trans('franchisee/wallet/transactions.tds')}}</th>
					</tr>
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
                            <td class="text-left">{{$row->created_on}}</td>
                            <td class="text-left">{{$row->remark}}</td>
                            <td class="text-left">{{$row->wallet}}</td>
                            <td class="text-right">{{$row->amount}}</td> 
                            <td class="text-right">{{$row->tax}}</td>                          
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