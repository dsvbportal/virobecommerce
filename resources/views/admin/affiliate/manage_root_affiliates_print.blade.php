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
        <h1 style="text-align:center">Manage Root Affiliates - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>								                                                 
					<th  nowrap="nowrap">{{trans('admin/affiliate/admin.doj')}}</th>
					<th>{{trans('admin/affiliate/admin.root_id_details')}}</th>  
					<th>{{trans('admin/affiliate/admin.country')}}</th>					
					<th>{{trans('admin/general.status')}}</th>  
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($manage_user_details != '' && isset($manage_user_details))
                {
                    foreach ($manage_user_details as $row)
                    {
                        ?>
                         <tr>                        
                            <td class="text-left">{{$row->signedup_on}}</td>
                            <td class="text-left">
							    <span>Affiliate ID: {{$row->uname}}</span><br>
								<span>Name: {{$row->fullname}}</span><br>
								<span>Email: {{$row->email}}</span><br>
								<span>Phone : {{$row->mobile}}</span><br>
							</td>                          
                            <td class="text-left">{{$row->country_name}}</td> 
                            <td class="text-center">{{$row->status_name}}</td>                             
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