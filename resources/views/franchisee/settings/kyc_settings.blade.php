<div class="row">
<!--  Kyc Documents  -->
	<div class="col-sm-12">
	    <table class="table table-dark-bordered  table-dark-striped"  id="bank_details_main">
			<tbody>
			<tr>
				<th>Kyc Documents Upload</th>
			</tr>
			<tr>
				<td>
					<div class="row">
						<form  method="post" id="kyc_uploadfrm" onsubmit="return false;" action="{{route('fr.settings.kyc_document_upload')}}" enctype="multipart/form-data" autocomplete="off">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-4">					    
										<div class="form-group">
											<label for="exampleInputPassword1">Channel Partner's PAN number<span class="text-danger">*</span></label> 
											<input {!!build_attribute($kycfields['pan_no']['attr'])!!} id="pan_no" class="form-control" style="" {{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')])) ? 'disabled="disabled"': ''}} value="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->doc_number)) ? $kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->doc_number:''}}">
										</div>
										<div class="form-group">
											<label for="exampleInputPassword1">PAN Card<span class="text-danger">*</span> <span class="label label-info edit" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]) &&  isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->status_id != 1 ? '' :'display:none')}}">Edit</span></label>
											
											<h4 style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')])  ? '' :'display:none')}}">
											
												[<a class="link" href="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')])  ? $kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->path :'')}}"
												download><i class="fa fa-download" aria-hidden="true"></i> Download</a>]
												
												@if(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->status_id == 1)
												<span class="link text-success"><i class="fa fa-check-circle"></i>Verfied</span>
												@elseif(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]->status_id == 2)
												<span class="link text-danger"><i class="fa fa-ban"></i>Rejected</span>
												@endif
											</h4>
											<input {!!build_attribute($kycfields['pan']['attr'])!!} id="pan" class="file_upload" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.PAN')])  ? 'display:none' :'')}}">
											
											
											<!-- div class="btn btn-sm btn-success mt-20 waves-effect">
												<span>Choose files</span>														
												<input type="file" class="ignore-reset" data-hide="#profile-panel" name="store_image" accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error2" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="gal_image" title="Choose File" data-default="http://localhost/dsvb_portal/imgs/merchant/75/75/store.jpg" data-width="700" data-height="450">
											</div-->
										</div>	
									</div>
									<!-- ID Proof -->
									<div class="col-sm-4">		
										<div class="form-group">
											<label for="exampleInputPassword2">ID proof of Contact person<span class="text-danger">*</span> <span class="label label-info edit" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')])  &&  isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->status_id != 1 ? '' :'display:none')}}">Edit</span></label>
												
											<h4 style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) ? '' :'display:none')}}">
											
												[<a class="link" href="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) ? $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->path :'')}}" download><i class="fa fa-download"></i> Download</a>]
												
												@if(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->status_id == 1)
												<span class="link text-success"><i class="fa fa-check-circle"></i>Verfied</span>
												@elseif(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]->status_id == 2)
												<span class="link text-danger"><i class="fa fa-ban"></i>Rejected</span>	
												@endif
											</h4>
											<input {!!build_attribute($kycfields['id_proof']['attr'])!!} id="id_proof" class="file_upload" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ID_PROOF')]) ? 'display:none' :'')}}">
										</div>					
										
										<div class="form-group">
											<label for="exampleInputPassword2">Channel Partner office address proof<span class="text-danger">*</span> <span class="label label-info edit" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')])  &&  isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->status_id != 1 ? '' :'display:none')}}">Edit</span></label>
												
											<h4 style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) ? '' :'display:none')}}">
											
												[<a class="link"  href="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) ? $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->path :'')}}" download><i class="fa fa-download"></i> Download</a>]
												
												@if(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->status_id == 1)
												<span class="link text-success"><i class="fa fa-check-circle"></i>Verfied</span>
												@elseif(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]->status_id == 2)
												<span class="link text-danger"><i class="fa fa-ban"></i>Rejected</span>	
												@endif
											</h4>
											<input {!!build_attribute($kycfields['address_proof']['attr'])!!} id="address_proof" class="file_upload" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.ADDRESS_PROOF')]) ? 'display:none' :'')}}">
										</div>						
									</div>
									<!-- ID Proof end -->
									
									<div class="col-sm-4">		
										<div class="form-group">
											<label for="exampleInputPassword2">Cancelled Cheque<span class="text-danger">*</span> <span class="label label-info edit" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')])  &&  isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->status_id != 1 ? '' :'display:none')}}">Edit</span></label>
												
											<h4 style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) ? '' :'display:none')}}">
											
												[<a class="link"  href="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) ? $kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->path :'')}}" download><i class="fa fa-download"></i> Download</a>]
												
												@if(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->status_id == 1)
												<span class="link text-success"><i class="fa fa-check-circle"></i>Verfied</span>
												@elseif(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->status_id) && $kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]->status_id == 2)
												<span class="link text-danger"><i class="fa fa-ban"></i>Rejected</span>	
												@endif
											</h4>
											<input {!!build_attribute($kycfields['cheque']['attr'])!!} id="cheque" class="file_upload" style="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.CHEQUE')]) ? 'display:none' :'')}}">
										</div>						
									</div>
									@if($userSess->country_id == '77')
									<div class="col-sm-4">	
										<div class="form-group">
											<label for="exampleInputPassword2">GST Number (optional)<span class="text-danger">*</span></label>
											<input {!!build_attribute($kycfields['tax']['attr'])!!} id="tax" class="form-control" style="" {{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.TAX')]) && !empty($kyc_document[config('constants.KYC_DOCUMENT_TYPE.TAX')])) ? 'disabled=""disabled""' : ''}} value="{{(is_array($kyc_document) && isset($kyc_document[config('constants.KYC_DOCUMENT_TYPE.TAX')]->doc_number)) ? $kyc_document[config('constants.KYC_DOCUMENT_TYPE.TAX')]->doc_number : ''}}">
										</div>										
									</div>
									@endif
								</div>
							</div>
							
							<div class="col-sm-12">
								<div class="row">							
									<div class="form-group col-sm-12 text-right">
										<button name ="Send" type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Update Documents</button>
									</div>			
								</div>	
							</div>
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-12">
										<div class="well">
											<p><span class="label label-info">Note</span> Please select valid format (*.gif, *.jpg, *.jpeg, *.png, *.pdf).</p>
											<p><span class="label label-info">Note</span> Maximum filesize is allowed is 2MB.</p>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>