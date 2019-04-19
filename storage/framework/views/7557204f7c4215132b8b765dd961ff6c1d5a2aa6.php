
  		

  		 


  		<?php if(!empty($user_info)): ?>
            
               <div  style="text-align: right" class="col-md-10">
		        <button   type="" class="btn btn-primary" id="relogin_bank" name="" ctr_url="<?php echo e(route('ecom.account.relogin_bank')); ?>" setsession="<?php echo e(route('ecom.account.setbanksession')); ?>"><i class="fa fa-save"></i>Add New</button>	
		</div>      
                     <?php foreach($user_info as $k=>$bank): ?>
                            <div class="repeat">
                              <div class="col-md-12">
                                 <label class="col-md-4 control-label">Account Holder Name </label>: <label class="col-md-4 control-label"><?php echo e(isset($bank->acc_holder_name) ? $bank->acc_holder_name : ''); ?></label>
							  </div>
							  <div class="col-md-12">							 
                                   <label class="col-md-4 control-label">Account Number  </label>:<label class=" col-md-4 control-label"><?php echo e(isset($bank->acc_number) ? $bank->acc_number : ''); ?></label>									
							   </div>
							  
							  <div class="col-md-12">
                                   <label class="col-md-4 control-label">IFSC Code </label>:<label class=" col-md-4 control-label"><?php echo e(isset($bank->ifsc_code) ? $bank->ifsc_code : ''); ?></label>															 							  </div>
							 
							  <div class="col-md-12">
                                <label class="col-md-4 control-label">Status </label>:<?php if($bank->status): ?><label class=" col-md-4 label label-success"> Active
                                   	                                                            
                                   	                                                            <?php else: ?>
                                   	                                                           <label class="col-md-4 label label-danger ">disabled<label>
                                   	                                                            <?php endif; ?>
                                                                                           </label>
									
							   </div>

							 
							  <div class="col-md-12">
							  <div class="col-md-6 text-right">
                                   <a class="btn btn-link change_bank_detail"  id="<?php echo e(isset($bank->id) ? $bank->id : ''); ?>">Change</a> 

                                    <a class="btn btn-link delete_bank_detail"  row_id="<?php echo e(isset($bank->id) ? $bank->id : ''); ?>" ctr_url="<?php echo e(route('ecom.account.remove_bank')); ?>">Remove</a>

                                   <?php if($bank->status): ?>
                                    <a class="btn btn-link change_status" status=0 row_id="<?php echo e(isset($bank->id) ? $bank->id : ''); ?>" ctr_url="<?php echo e(route('ecom.account.change_status')); ?>">Deactivate</a> 
                                    <?php else: ?>
                                     <a class="btn btn-link change_status"  status=1 row_id="<?php echo e(isset($bank->id) ? $bank->id : ''); ?>" ctr_url="<?php echo e(route('ecom.account.change_status')); ?>" >Activate</a> 

                                    <?php endif; ?>

									
							   </div>
							  </div>
							</div>

							  <?php endforeach; ?>

                                


								
					


				







						
        
        <?php else: ?>


                             <div class="text-center" id="">
                             	<div class="form-group">
                             	<img class="img-circle bank-logo"src="<?php echo e(asset('resources/uploads/ecom/bank_logo.png')); ?>">
                             </div>
                             <div class="form-group">
						    <h6>You Haven't Added Any Bank Details</h6>
					     	</div>
					         <div class="form-group">
						     <button   type="" class="btn btn-primary" id="relogin_bank" name="" ctr_url="<?php echo e(route('ecom.account.relogin_bank')); ?>" setsession="<?php echo e(route('ecom.account.setbanksession')); ?>"><i class="fa fa-plus"></i> Add Bank Details</button>
						     </div>
						    </div>
        

							
						

         <?php endif; ?>







						   
						    					
					               	
                 		 				
												
					


