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
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Leadership Bonus- <?php  echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                 <tr>                                                    
					<th>{{trans('affiliate/general.sl_no')}}</th>
					<th class="text-center no-wrap">Period</th>
					<th class="text-center no-wrap">Matching(QV)</th>
					<th class="text-center no-wrap">Earnings(QV)</th>
					<th class="text-center no-wrap">Commission</th>                               
					<th class="text-center no-wrap">NGO Wallet</th>
					<th class="text-center no-wrap">Net Pay</th>         
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
                             <td class="text-left"><?php echo $i;?></td>
							<td class="text-left"><?php echo $row->date_for;?></td>
                            <td class="text-left"><?php echo $row->clubpoint;?></td>
                            <td class="text-left"><?php echo $row->earnings;?></td>
                            <td class="text-left"><?php echo $row->income;?></td>      
                            <td class="text-left"><?php echo $row->ngo_wallet_amt;?></td>
                            <td class="text-left"><?php echo $row->paidinc;?></td>    
                        </tr>
                        <?php
                        $i++;
                    }
                 }
				 else {
					echo "<tr><td colspan='7'>".trans('affiliate/general.no_records_found')."</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">{{trans('affiliate/general.print_btn')}}</button>