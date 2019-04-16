
  		

  		 


  		@if(!empty($user_info))
            
               <div  style="text-align: right" class="col-md-10">
		        <button   type="" class="btn btn-primary" id="relogin_bank" name="" ctr_url="{{route('ecom.account.relogin_bank')}}" setsession="{{route('ecom.account.setbanksession')}}"><i class="fa fa-save"></i>Add New</button>	
		</div>      
                     @foreach($user_info as $k=>$bank)
                            <div class="repeat">
                              <div class="col-md-12">
                                 <label class="col-md-4 control-label">Account Holder Name </label>: <label class="col-md-4 control-label">{{$bank->acc_holder_name or ''}}</label>
							  </div>
							  <div class="col-md-12">							 
                                   <label class="col-md-4 control-label">Account Number  </label>:<label class=" col-md-4 control-label">{{$bank->acc_number or ''}}</label>									
							   </div>
							  
							  <div class="col-md-12">
                                   <label class="col-md-4 control-label">IFSC Code </label>:<label class=" col-md-4 control-label">{{$bank->ifsc_code or ''}}</label>															 							  </div>
							 
							  <div class="col-md-12">
                                <label class="col-md-4 control-label">Status </label>:@if($bank->status)<label class=" col-md-4 label label-success"> Active
                                   	                                                            
                                   	                                                            @else
                                   	                                                           <label class="col-md-4 label label-danger ">disabled<label>
                                   	                                                            @endif
                                                                                           </label>
									
							   </div>

							 
							  <div class="col-md-12">
							  <div class="col-md-6 text-right">
                                   <a class="btn btn-link change_bank_detail"  id="{{$bank->id or ''}}">Change</a> 

                                    <a class="btn btn-link delete_bank_detail"  row_id="{{$bank->id or ''}}" ctr_url="{{route('ecom.account.remove_bank')}}">Remove</a>

                                   @if($bank->status)
                                    <a class="btn btn-link change_status" status=0 row_id="{{$bank->id or ''}}" ctr_url="{{route('ecom.account.change_status')}}">Deactivate</a> 
                                    @else
                                     <a class="btn btn-link change_status"  status=1 row_id="{{$bank->id or ''}}" ctr_url="{{route('ecom.account.change_status')}}" >Activate</a> 

                                    @endif

									
							   </div>
							  </div>
							</div>

							  @endforeach

                                


								
					


				







						
        
        @else


                             <div class="text-center" id="">
                             	<div class="form-group">
                             	<img class="img-circle bank-logo"src="{{asset('resources/uploads/ecom/bank_logo.png')}}">
                             </div>
                             <div class="form-group">
						    <h6>You Haven't Added Any Bank Details</h6>
					     	</div>
					         <div class="form-group">
						     <button   type="" class="btn btn-primary" id="relogin_bank" name="" ctr_url="{{route('ecom.account.relogin_bank')}}" setsession="{{route('ecom.account.setbanksession')}}"><i class="fa fa-plus"></i> Add Bank Details</button>
						     </div>
						    </div>
        

							
						

         @endif







						   
						    					
					               	
                 		 				
												
					


