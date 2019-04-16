        

        <div style="margin: 20px;">
							<label class=" control-label" ><strong style="font-size: 18px" > My Bank Details </strong></label>
		</div> 

		@if(!empty($user_info))

							<div id="update_details_div" >
							<input type="hidden" value="{{$id}}" name="row_id">							                           
                              <div class="col-md-12">
                              	<div class="row">
							  <div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Account Holder Name<span class="danger"  style="color: red;" > * </span></label>
									
											<input {!!build_attribute($cpfields['acc_holder_name']['attr'])!!} id="" placeholder="{{$user_info->acc_holder_name or ' '}}" class="form-control" value="{{$user_info->acc_holder_name or ' '}}" name="acc_holder_name">
										 
									</div>  
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class=" control-label">Enter Account Number<span class="danger"  style="color: red;" > * </span></label>
									
											<input {!!build_attribute($cpfields['acc_number']['attr'])!!} id="" placeholder="{{$user_info->acc_number  or ' ' }}" class="form-control" value="{{$user_info->acc_number or ' '}}" name="acc_number">
										
									</div>  
								</div>
							</div>
							</div>
							<div class="col-md-12">
                             <div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Confirm Account Number<span class="danger"  style="color: red;" > * </span></label>
										
											<input {!!build_attribute($cpfields['confirm_acc_number']['attr'])!!} id="" placeholder="{{$user_info->acc_number   or ' '}}" class="form-control" value="{{$user_info->acc_number or ' '}}" name="confirm_acc_number">
										  
									</div>  
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Enter IFSC Code<span class="danger"  style="color: red;" > * </span></label>
										    <div class="input-group">
											<input  {!!build_attribute($cpfields['ifsc_code']['attr'])!!} placeholder="{{$user_info->ifsc_code  or ' '}}" class="form-control" value="{{$user_info->ifsc_code or ' '}}" name="ifsc_code" id="ifsc_code" >
											<span class="input-group-addon btn btn-primary" data-target="" ctr_url="{{route('ecom.account.find_ifsc')}}" id="find_ifsc">find ifsc</span>
										</div>
										  
									</div>  
								</div>
							</div>
							</div>

							<div  class="col-md-12">
								<input type="checkbox" value="1" name="tems&conditon" id="tems&conditon" data-err-msg-to="#err_msg"> I agree that the provided account details and correct that virob is not to be held liable in any case discrepancy please agree that details given by you are correct
								
							</div>
							<span id="err_msg"></span>

								<div class="">							
									<div class="">						
										<label class="col-sm-6 control-label"></label>										
										<input type="submit" id="save_update_bank_btn" class="btn btn-md btn-primary" value="update" ctr_url="{{route('ecom.account.update_bank_detail')}}" >
										                      
									</div>
								</div>																		
                                 
						    </div>	

      


	@endif