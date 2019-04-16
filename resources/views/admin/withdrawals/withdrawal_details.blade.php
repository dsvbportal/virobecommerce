 <div class="panel panel-default">
 <div class="panel_controls">
			<div class="panel-heading">
                <h4 class="panel-title">Withdrawal Details <button class="btn btn-default pull-right back">Close</button></h4>
            </div>
 	 <div class="col-md-12">
        <h5><strong><span>Transaction Details</span></strong></h5>
		<h4 class="text-left"><small>{{$wd_details->title}}</small>
		@if($wd_details->status_id == 0)<button id="cancel-req" rel="{{$wd_details->transaction_id}}" class="btn btn-danger btn-sm pull-right">Cancel Withdrawal</button>@endif</h4>
		<table class="table table-bordered table-striped">
			<tbody>
				<tr>
					<th class="text-left" nowrap>{{trans('admin/general.date')}} </th>
					<td>{{$wd_details->created_on}}</td>
				</tr>
				<tr>
					<th class="text-left" nowrap>Fullname</th>
					<td>{{$wd_details->fullname}}</td>
				</tr>
				<tr>
					<th class="text-left" nowrap>User Code</th>
					<td>{{$wd_details->user_code}}</td>
				</tr>
				<tr>
					<th class="text-left" nowrap>Payment Type</th>
					<td>{{$wd_details->payment_type}}</td>
				</tr>
				<tr>
					<th class="text-left">Total Amount</th>
					<td>{{($wd_details->amount)}}</td>
				</tr>
				<tr>
					<th class="text-left">Netpay</th>
					<td>{{($wd_details->paidamt)}}</td>
				</tr>
				<tr>
					<th class="text-left">Fee amount</th>
					<td>{{($wd_details->handleamt)}}</td>
				</tr>
				<tr>
					<th class="text-left">Status</th>
					<td><span class="label label-{{$wd_details->status_class}}">{{$wd_details->status}}</span></td>
				</tr>
			</tbody>
		</table>
   </div>
    @if(!empty($wd_details->account_info))
    <div class="col-md-12 bg-gray">
        <h5><strong><span>Payee Details</span></strong></h5>
    </div>
    <div class="clearfix"></div><br>
    <div class="col-md-12">
        <table class="table table-bordered">
            <?php
            foreach ($wd_details->account_info as $val)
            {
                ?>
                <tr>
                    <td ><?php echo $val['label'];?></td>
                    <td><?php echo $val['value'];?></td>
                </tr>
        <?php } ?>

        </table>
    </div>
    @endif
    @if ($wd_details->status_id == 3 && !empty($wd_details->reason))
    <table class="table table-bordered table-striped">
        <thead>
			<tr class ="bg-gray">
				<th><h5><strong><span>{{trans('admin/withdrawal.details.reason_for_cancel')}}</h5></span></strong></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{{$wd_details->reason or ''}}</td>
			</tr>
		</tbody>
    </table>
    @endif
    <div class="clearfix"></div>
</div>
	</div>
	
	 
