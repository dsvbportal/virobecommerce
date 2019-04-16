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
        <h1 style="text-align:center">My Referred Customers - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<tr>                                            
						<th>Customer</th>							    
						<th>Signed Up on</th>
						<th>Country</th>
						<th>Sales</th>
						<th>CV</th> 
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
                            <td class="text-left">{{$row->firstname}}({{$row->user_code}})</td>
                            <td class="text-left">{{$row->signedup_on}}</td>
                            <td class="text-left">{{$row->country}}</td> 
                            <td class="text-right">{{$row->sales_tot}}</td>  
                            <td class="text-right">{{$row->cv_tot}}</td>                      
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