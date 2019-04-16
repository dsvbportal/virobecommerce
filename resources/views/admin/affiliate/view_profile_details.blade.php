<div class="panel"  id="offc-info">
	<div class="panel-heading">
		<h3 class="panel-title">
			<button class="btn btn-danger btn-sm close_btn pull-right"><i class="fa fa-times"></i></button>
			Affiliate Details
		</h3>
	</div>
	<div class="panel-body">
	<table class="table table-bordered table-striped" >
		<!--<tr><th><a href="" id="personal-editBtn" class="btn btn-primary btn-xs pull-right"><i class="fa fa-edit"></i> Edit</a>Affiliate Details</th></tr>-->		
		<tr>
		<td>
		<div class="col-md-2">
			<img class="profile-user-img-edit img-responsive img-circle" src="{{asset($affiliateInfo->profile_img)}}" alt="{{trans('affiliate/profile.user_profile_picture')}}">
				</div>
				<div class="col-md-4">
					<table class="table table-bordered table-striped">
					<tr>
						<th class="text-right" width="50%">Account ID:</th>
						<td>{{$affiliateInfo->user_code}}</td>
					</tr>
					<tr>
						<th  class="text-right">Username:</th>
						<td>{{$affiliateInfo->uname}}</td>
					</tr>
					<tr class="form-group">
						<th  class="text-right">Name:</th>
						<td>{{$affiliateInfo->full_name}}</td>
					</tr>																				
					<tr>
						<th  class="text-right" width="45%">Gender / Date of Birth:</th>
						<td>{{$affiliateInfo->gender}} / {{date('d m, Y',strtotime($affiliateInfo->dob))}}</td>
					</tr>
					<tr>
						<th class="text-right" width="45%">Marital Status:</th>
						<td>{{$affiliateInfo->marital_status}}</td>
					</tr>
					<tr>
						<th  class="text-right"  width="45%">Father’s/Husband Name:</th>
						<td>{{$affiliateInfo->gardian}}</td>
					</tr>
					</table>
				</div>
				<div class="col-md-3">
					<table class="table table-bordered table-striped">
					<tr class="form-group">
						<th  class="text-right"   width="40%">Date of Joining :</th>
						<td>{{date('d M, Y',strtotime($affiliateInfo->created_on))}}</td>
					</tr>
					<tr>
						<th  class="text-right">Affiliate Type:</th>
						<td>{{$affiliateInfo->aff_type}}</td>
					</tr>										
					<tr>
						<th  class="text-right">Affiliate Rank:</th>
						<td>{{$affiliateInfo->rank}}</td>
					</tr>
					<tr>
						<th  class="text-right">Affiliate Plan:</th>
						<td>{{$affiliateInfo->package_name}}</td>
					</tr>
					<tr>
						<th class="text-right">Sponsor ID:</label>
						<td>{{$affiliateInfo->sponsor_code}}</td>
					</tr>
					<tr>
						<th  class="text-right">Sponsor Name:</label>
						<td>{{$affiliateInfo->sponsor_name}}</td>
					</tr>
					</table>
				</div>
				<div class="col-md-3">
					<table class="table table-bordered table-striped">
					<tr>
						<th class="text-right" width="45%">KYC Documents:</th>
						<td><span class="label label-warning">Pending</span></td>
					</tr>
					<tr>
						<th class="text-right">Submitted on:</th>
						<td>-</td>
					</tr>
					<tr >
						<th class="text-right">KYC Verified:</th>
						<td><span class="label label-warning">Pending</span></td>
					</tr>
					<tr>
						<th  class="text-right">Verified on:</th>
						<td>-</td>
					</tr>
					</table>
				</div>	
			</td>
		</tr>
	</table>
	</div>
</div>
<script>
    $('.close_btn').click(function (e) {
        e.preventDefault();
        $('#view_details,#change_Member_pwd,#edit_details,#change_email,#change_mobile,#change_Member_security_pin').fadeOut('fast', function () {
            $('#users-list-panel').fadeIn('slow');
        });
      });
</script>