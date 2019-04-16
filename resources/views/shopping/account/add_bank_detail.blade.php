			
				<div style="margin: 20px;" id=''>
							<label class=" control-label" ><strong style="font-size: 18px" > Add Bank Details </strong></label>
						     </div>


                            <div class="col-md-12">
                            <div class="row">
							  <div class="col-md-6">
									<div class="form-group">
										<label  control-label">Account Holder Name<span class="danger"  style="color: red;" > * </span></label>
										
											<input {!!build_attribute($cpfields['acc_holder_name']['attr'])!!} id="" placeholder="" class="form-control" value="" name="acc_holder_name" >
										  
									</div>  
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label control-label">Enter Account Number<span class="danger"  style="color: red;" > * </span></label>
										
											<input {!!build_attribute($cpfields['acc_number']['attr'])!!} id="" placeholder="" class="form-control" value="" name="acc_number">
										
									</div>  
								</div>
							</div>
							</div>

                             <div class="col-md-12">
                             	<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Confirm Account Number<span class="danger"  style="color: red;" > * </span></label>
										
											<input {!!build_attribute($cpfields['confirm_acc_number']['attr'])!!} id="" placeholder="" class="form-control" value="" name="confirm_acc_number">
									 
									</div>  
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label control-label">Enter IFSC Code<span class="danger"  style="color: red;" > * </span></label>
										
											<div class="input-group">
											<input {!!build_attribute($cpfields['ifsc_code']['attr'])!!}  id="ifsc_code" placeholder="" class="form-control" value="" name="ifsc_code" data-err-msg-to="#ifsc_err">
											<span class="input-group-addon btn btn-primary" data-target="" ctr_url="{{route('ecom.account.find_ifsc')}}" id="find_ifsc">find ifsc</span>
											<span id="ifsc_err"></span>
										   </div>  
									</div>  
								</div>
							</div>
							</div>
							<div  class="col-md-12">
								<input type="checkbox" value="1" name="tems&conditon" id="tems&conditon" data-err-msg-to="#err_msg"> I agree that the provided account details and correct that virob is not to be held liable in any case discrepancy please agree that details given by you are correct
								
							</div>
							<span id="err_msg"></span>

								<div class="form-group">							
									<div class="col-sm-12 fieldgroup">						
										<label class="col-sm-6 control-label"></label>										
										<button   id="save_bank_btn" class="btn btn-md btn-primary" ctr_url="{{route('ecom.account.save_bank_detail')}}" disabled="disabled"><i class="fa fa-save"></i> Save
										</button>                      
									</div>
								</div>

							
						