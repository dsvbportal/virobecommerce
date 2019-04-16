<table class="table table-dark-bordered  table-dark-striped"  id="bank_details_main">
	<tr>
		<th>
			<button id="edit" type="button" class="btn btn-xs btn-primary pull-right" title="Search" <?php echo (!empty($bank_account_details)) ? "style='display:block'":"style='display:none'"?>><i class="fa fa-edit"></i> Edit</button>
		 Bank Details
		</th>
	</tr>
    <tr>
		<td>
			<form class="form-horizontal" id="bank_details" action="{{route('aff.settings.bank-details')}}" enctype="multipart/form-data" data-bank-info="{{route('aff.settings.bank-info')}}">				
				 <div id="add_details" <?php echo empty($bank_account_details)? "style='display:block'": "style='display:none'";?>>				 
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label"> Beneficiary Name </label>
						<div class="col-sm-4">
							<input class="form-control" id="account_name" {!!build_attribute($fields['payment_setings.beneficiary_name']['attr'])!!} value="{{$bank_account_details->beneficiary_name or ''}}" onkeypress="return alphaBets_withspace(event)">
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label"> Current Account Number</label>
						<div class="col-sm-4">
							<input class="form-control" id="account_no" {!!build_attribute($fields['payment_setings.account_no']['attr'])!!} value="{{$bank_account_details->account_no or ''}}" onkeypress="return alphaNumeric_withoutspace(event)">
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label">Confirm Account Number</label>
						<div class="col-sm-4">
							<input class="form-control" id="confirm_account_no" {!!build_attribute($fields['payment_setings.confirm_account_no']['attr'])!!} value="{{$bank_account_details->confirm_account_no or ''}}" onkeypress="return RestrictSpace(event)" onkeypress="return alphaNumeric_withoutspace(event)">
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label">IFSC Code</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" class="form-control"  id="ifsc_code" data-url="{{route('aff.settings.get-ifsc-details')}}" {!!build_attribute($fields['payment_setings.ifsc_code']['attr'])!!} value="{{$bank_account_details->ifsc_code or ''}}" onkeypress="return RestrictSpace(event)" placeholder="MAHB0001821" onkeypress="return alphaNumeric_withoutspace(event)" data-err-msg-to="#ifsc_code_err" >
								<span class="input-group-btn">
									<button class="btn btn-success" type="button" id="ifsc_code_verifybtn"><i class="fa fa-search"></i> Search</button>
								</span>
							</div>
							<div id="ifsc_code_err"></div>
						</div>						
					</div>
					<div class="form-group bank_det">
						<label for="inputEmail" class="col-sm-4 control-label">Bank Name</label>
						<div class="col-sm-4">
							<input class="form-control" id="bank_value"{!!build_attribute($fields['payment_setings.bank_name']['attr'])!!} value="{{$bank_account_details->bank_name or ''}}" readonly>
						</div>
					</div>
					<div class="form-group bank_det">
						<label for="inputEmail" class="col-sm-4 control-label">Branch</label>
						<div class="col-sm-4">
							<input class="form-control" id="branch_value" {!!build_attribute($fields['payment_setings.branch_name']['attr'])!!} value="{{$bank_account_details->branch_name or ''}}" readonly>
						</div>
					</div>
					<div class="form-group bank_det">
						<label for="inputEmail" class="col-sm-4 control-label">District</label>
						<div class="col-sm-4">
							<input class="form-control" id="district" {!!build_attribute($fields['payment_setings.district']['attr'])!!} value="{{$bank_account_details->district or ''}}" readonly>
						</div>
					</div>
					<div class="form-group bank_det">
						<label for="inputEmail" class="col-sm-4 control-label">State</label>
						<div class="col-sm-4">
							<input class="form-control" id="state" {!!build_attribute($fields['payment_setings.state']['attr'])!!} value="{{$bank_account_details->state or ''}}" readonly>
						</div>
					</div>
					<div class="form-group" id="editactions" <?php echo empty($bank_account_details)? "style='display:none'": "style='display:block'";?>>										
						<div class="col-sm-offset-4 col-sm-4">	
							<button type="submit" class="btn btn-success" id="save"><i class="fa fa-save"></i> Save</button>
							<button type="button" class="btn btn-danger" id="cancel_edit"><i class="fa fa-times"></i> Close</button>
						</div>
					</div>
				</div>	
				<div id="view_detail_list" <?php echo !empty($bank_account_details)? "style='display:block'" : "style='display:none'"; ?>>		
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label">Beneficiary Name</label>
						<div class="col-sm-4 pt-7">
							<span class="banklabels_val" id="view_beneficiary_name" data-target="account_name" >{{$bank_account_details->beneficiary_name or ''}}</span>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label">Current Account Number</label>
						<div class="col-sm-4 pt-7">
					   <span class="banklabels_val" id="view_account_no" data-target="account_no" >{{$bank_account_details->account_no or ''}}</span>							
						</div>
					</div> 					
					<div class="form-group">
						<label for="inputEmail" class="col-sm-4 control-label">IFSC Code</label>
						<div class="col-sm-4 pt-7">
							<span class="banklabels_val" id="view_ifsc_code" data-target="ifsc_code" >{{$bank_account_details->ifsc_code or ''}}</span>							
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-4 control-label">Bank Name</label>
						<div class="col-sm-4 pt-7">
							<span class="banklabels_val" id="view_bank_name" data-target="bank_value" >{{$bank_account_details->bank_name or ''}}</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Branch</label>
						<div class="col-sm-4 pt-7">
							<span class="banklabels_val" id="view_branch_name" data-target="branch_value" >{{$bank_account_details->branch_name or ''}}</span>
						</div>
					</div>	
					<div class="form-group">
						<label class="col-sm-4 control-label">District</label>
						<div class="col-sm-4 pt-7">
							<span class="banklabels_val" id="view_district" data-target="district" >{{$bank_account_details->district or ''}}</span>
						</div>
					</div>	
					<div class="form-group">
						<label  class="col-sm-4 control-label">State</label>
						<div class="col-sm-4 pt-7">
							<span class="banklabels_val" id="view_state" data-target="state" >{{$bank_account_details->state or ''}}</span>
						</div>
					</div>	
				</div>
			</form>
	    </td>
	</tr>
</table>
<!-- modal -->
<div id="retailer-qlogin-model1" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Find Your Bank IFSC Code</h4>
			</div>
			<div class="modal-body">
				<div id="accErr"></div>
				<div id="change_Member_pin" style="display:none;">
				</div>
			</div>
		</div>
	</div>
</div>