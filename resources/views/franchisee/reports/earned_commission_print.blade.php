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
        <h1 style="text-align:center">Earned Commission Report - <?php echo date("d-M-Y");?></h1><br/>
        <table id="earned_commission_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<tr>                                                    
						 <th>Created On</th>
                          <th>Transaction Type</th>
						  <th>Earnings</th>							
						  <th>Tax</th>							
						  <th>Net Credit</th>							
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
						/* echo '<pre>';
						print_r($row); die; */
                        ?>
                        <tr>                        
                            <td class="text-left">{{$row->created_date}}</td>
                            <td class="text-left">{{$row->commission_type}}</td>
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
<button class="noprint" onClick="myFunction()">Print</button>