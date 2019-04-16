<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Earned Commission Details Report - <?php echo date("d-M-Y");?></h1><br/>
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<tr>                                                    
						  <th>Created On</th>
                           <th>Transaction Type</th>
						   <th>From User</th>
						   <th>To User</th>
						   <th>Amount</th>
						   <th>Earnings</th>							
						  <th>Tax</th>							
						  <th>Net Credit</th>							
					</tr>
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
                            <td class="text-left">{{$row->created_date}}</td>
                            <td class="text-left">{{$row->commission_type}}</td>
                            <td class="text-left">{{$row->from_name.'('.$row->from_user_code.')'}}</td>
                            <td class="text-left">{{$row->to_name.'('.$row->to_user_code.')'}}</td>
                            <td class="text-left">{{$row->transferred_amount}}</td>
                            <td class="text-left">{{$row->commission_amount}}</td>
                            <td class="text-left">{{$row->tax}}</td>
                            <td class="text-center">{{$row->net_pay}}</td>  							
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