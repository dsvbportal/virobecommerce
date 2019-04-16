<script>
    function myFunction() {
        window.print();
    }
</script>

<div class="box box-primary">
<div class="box-header with-border">
<div class="withd-trans">
	<div id="msg">
	
    <h4 class="text-left"><small></small><button class="btn btn-sm btn-danger pull-right back"><i class="fa fa-times"></i>Close</button></h4>
    <p class="text-center"></p>

	<div class="col-md-8">
    <table class="table table-bordered table-striped">
        <tbody>
		
		    <tr>
                <th class="text-left" nowrap>Affiliate ID </th>
                <td>{{$tds_details->user_code}}</td>
            </tr>
            <tr>
                <th class="text-left" nowrap>Full Name </th>
                <td>{{$tds_details->fullname}}</td>
            </tr>
            <tr>
                <th class="text-left" nowrap>Email</th>
                <td>{{$tds_details->email}}</td>
            </tr>
            <tr>
                <th class="text-left">Mobile</th>
                <td><span>{{$tds_details->phonecode}}</span>  {{($tds_details->mobile)}}</td>
            </tr>
			<tr>
                <th class="text-left">PAN Number</th>
                <td>{{($tds_details->doc_number)}}</td>
            </tr>
			<tr>
                <th class="text-left">Earning Amount</th>
                <td>{{($tds_details->amt)}}</td>
            </tr>
            <tr>
                <th class="text-left">Tax Deducted</th>
                <td>{{($tds_details->tax)}}</td>
            </tr>
			 <tr>
                <th class="text-left">Date</th>
                <td>{{($tds_details->created_on)}}</td>
            </tr>
        </tbody>
      </table>
	  <button class="noprint btn btn-sm bg-blue col-sm-offset-3" onClick="myFunction()">Print</button>
	  
           </div>
	      </div>
		</div>
	   </div>
			<!-- ./col -->			
	</div>
		<!-- /.row -->
   
