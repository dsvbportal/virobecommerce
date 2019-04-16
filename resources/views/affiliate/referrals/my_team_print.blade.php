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
        <h1 style="text-align:center">{{trans('affiliate/general.my_team_list')}} - <?php echo date("d-M-Y");?></h1><br/>
        
           <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>                                                      
					    <th>{{trans('affiliate/referrels/my_referrels.affiliate')}}</th>
						<th>{{trans('affiliate/referrels/my_referrels.country')}} </th>
						<th>{{trans('affiliate/referrels/my_referrels.refered_by')}} </th>
						<th>{{trans('affiliate/referrels/my_referrels.placement')}} </th>
						<th>{{trans('affiliate/referrels/my_referrels.rank')}} </th>
						<th>{{trans('affiliate/referrels/my_referrels.qv')}} </th>
						<th>{{trans('affiliate/referrels/my_referrels.status')}} </th>	
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
                             <td class="text-left">
							   <span>Full Name: {{$row->full_name}}</span><br>
							   <span>Affiliate ID: {{$row->user_code}}</span><br>
							   <span>Email: {{$row->email}}</span><br>
							   <span>Phone : {{$row->mobile}}</span><br> </td>
                              <td class="text-left"><?php echo $row->country;?></td>
							  <td class="text-center"><?php echo $row->sponsor_code.'('.$row->sponsor_uname.')';?></td>
							  <td class="text-center">
							  <span>{{$row->upline_code.'('.$row->upline_uname.')'}}</span><br>
							  <span>Generation:{{$row->generation}}</span><br>
							  </td>
							 <td class="text-center"><?php echo $row->rank;?></td>
                             <td class="text-right"><?php echo $row->qv;?></td>
                             <td class="text-center"><?php echo $row->status;?></td>
                        </tr>   
                        <?php
                        $i++;
                    }
                 }
				 else {
					echo "<tr><td colspan='6'>{{trans('affiliate/general.no_records_found')}}</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">{{trans('affiliate/general.print_btn')}}</button>