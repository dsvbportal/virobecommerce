<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">My Referred Customers - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
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
                if ($export_data != '' && isset($export_data))
                {
                    foreach ($export_data as $row)
                    {
                        ?>   
                        <tr>                        
                            <td class="text-left">{{$row->firstname}} ({{$row->user_code}})</td>                            
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