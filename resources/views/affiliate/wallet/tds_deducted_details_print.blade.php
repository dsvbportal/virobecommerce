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
					   <th>{{trans('affiliate/general.date')}}</th>
						<th>{{trans('affiliate/wallet/transactions.earn_amount')}}</th>
						<th>{{trans('affiliate/wallet/transactions.tax_deducted')}}</th>
						<th>{{trans('affiliate/wallet/transactions.income_type')}}</th>
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
                            <td class="text-center">{{$row->created_on}}</td>
                            <td class="text-right">{{$row->amount}}</td>
                            <td class="text-right">{{$row->tax}}</td>
                            <td class="text-center">{{$row->statementline}}</td> 
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