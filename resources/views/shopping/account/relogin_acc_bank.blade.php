



						 <div style="margin: 40px;">
						      <div class="form-group">
							  <h6><b>Relogin before entering bank details</b></h6>
						      </div>

					          <div class="form-group">                   
						      <input type="radio" name="varify" value="password" checked>Password
                              <input type="radio" name="varify" value="otp">OTP<br>
                              </div>
                         
                             <div class="class1" style="display:block;" id="class1">
  							 <div class="form-group">
  							 <input  type="password" id="password" name="password" placeholder="Enter password" class="form-control " value="" style="width: 400px">										 
							 </div>

							<div class="form-group">							
									<button name="submit" type="submit"  id="check_relogin_bank" class="btn btn-md btn-primary" ctr_url="{{route('ecom.account.check_relogin_bank')}}" nxt_url="{{route('ecom.account.add_bank_detail')}}"> Login
									</button>                      
									
								</div>
							</div>





				           <div class="class2" style="display:none;" id="class2">
									<div class="form-group ">
										<label class="control-label">Mobile Number</label>
									</div>
									<div class="form-group">
										<div class="input-group">
									      <span  class="input-group-addon">{{$logged_userinfo->phone_code or ''}}</span> 			<input  id="mobile" placeholder="" class="form-control col-md-3" value="{{$logged_userinfo->mobile }}" disabled="disabled" name="mobile" style="width: 400px">
										</div>
								    </div> 															
								    <div class="form-group">																			<button name="" type=""  id="send_otp_bank_btn" class="btn btn-md btn-primary" ctr_url="{{route('ecom.account.send_otp_bank')}}"> Send OTP
								        </button>                      
									</div>
					        </div>

							<div style="display:none;" id="OTPdiv">
								  
									
										<div class="form-group">
										<input  type='text' id="otp" name="otp" placeholder="Enter one time password." class="form-control" value="" style="width: 400px">
										
									   </div>  
								

								<div class="form-group">							
														
										
										<button  id="varify_otp_bank_btn" class="btn btn-md btn-primary" ctr_url="{{route('ecom.account.varify_otp_bank')}}" nxt_url="{{route('ecom.account.add_bank_detail')}}"> Varify OTP
										</button>  
										<button name="" type=""  id="send_otp_bank_btn" class="btn btn-md btn-primary" ctr_url="{{route('ecom.account.send_otp_bank')}}"> Resend OTP
								        </button>                    
									
								</div>									
							</div>





							</div>                          
						  
						    
						    					
					    </div>	
                 		 				
												
					
