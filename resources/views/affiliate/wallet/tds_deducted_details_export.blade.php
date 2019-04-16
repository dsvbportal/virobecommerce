<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">TDS Deducted Report - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
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
                if ($export_data != '' && isset($export_data))
                {
                    foreach ($export_data as $row)
                    {
                        ?>   
                        <tr>                        
                            <td class="text-left">{{$row->created_on}}</td>
                            <td class="text-left">{{$row->amount}}</td>
                            <td class="text-left">{{$row->tax}}</td>
                            <td class="text-right">{{$row->statementline}}</td> 
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