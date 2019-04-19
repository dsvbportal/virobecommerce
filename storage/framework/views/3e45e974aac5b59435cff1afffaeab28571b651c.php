<form class="form-horizontal" id="addressFrm" action="<?php echo e(route('ecom.account.save-address')); ?>" method="post" autocomplete="off"> 
	<div class="form-group">
		<label class="control-label">Country <span class="text-danger">*</span></label>
		<div>
			<select  name="address[country_id]" id="country_id" class="form-control" disabled >
				<option value="<?php echo e($logged_userinfo->country_id); ?>"><?php echo e($logged_userinfo->country); ?></option>
			</select>
		</div>
	</div>	
	<div class="form-group">
		<label class=" control-label">Full Name<span class="text-danger"> * </span></label>
		<div class="">
			<input id="fullname" placeholder="Full Name" class="form-control" value="<?php echo e($logged_userinfo->full_name); ?>" disabled>
		</div>	
	</div>	
	<div class="form-group">
		<label class=" control-label">Mobile Number <span class="text-danger"> * </span></label>
		<div class="">
			<div class="input-group">
				<span class="input-group-addon"><?php echo e($logged_userinfo->phone_code); ?></span>
				<input id="mobile" placeholder="Mobile" class="form-control" value="<?php echo e($logged_userinfo->mobile); ?>" disabled>
			</div>	
		</div>	
	</div>
	<div class="form-group">
		<label class=" control-label">Pincode <span class="text-danger"> * </span></label>
		<div class="">
			<input name="address[postal_code]" id="postal_code" placeholder="Pincode" class="form-control" value="<?php echo e(isset($address->postal_code) ? $address->postal_code : ''); ?>" >
		</div>	
	</div>						
	<div class="form-group">
		<label class=" control-label">Street Address <span class="text-danger"> * </span></label>
		<div class="">
			<input name="address[flat_no]" id="address" placeholder="Street Address" class="form-control" value="<?php echo e(isset($address->flatno_street) ? $address->flatno_street : ''); ?>">
		</div>	
	</div>	
	<div class="form-group">
		<label class=" control-label">Landmark <span class="text-danger"> * </span></label>
		<div class="">
			<input name="address[landmark]" id="landMark" placeholder="LandMark" class="form-control" value="<?php echo e(isset($address->landmark) ? $address->landmark : ''); ?>">
		</div>	
	</div>               		
	<div class="form-group cityFld hidden">
		<label class=" control-label">City / Town<span class="text-danger">*</span></label>
		<div class="">
			<select name="address[city_id]" id="city_id" class="form-control" data-selected="<?php echo e(isset($address->city_id) ? $address->city_id : ''); ?>">
			    <?php if(isset($address->city_id) && !empty($address->city_id)): ?>
				<option value="<?php echo e($address->city); ?>" selected><?php echo e($address->city); ?></option>
				<?php endif; ?>				
			</select>	
		</div>
	</div>	
	<div class="form-group stateFld hidden">
		<label class=" control-label">State / Region<span class="text-danger">*</span></label>
		<div class="">
			<select name="address[state_id]" id="state_id" class="form-control" data-selected="<?php echo e(isset($address->state_id) ? $address->state_id : ''); ?>">
	            <?php if(isset($address->state_id) && !empty($address->state_id)): ?>
				<option value="<?php echo e($address->state_id); ?>" selected><?php echo e($address->state); ?></option>
				<?php endif; ?>			
			</select>
		</div>
	</div> 
	<div class="form-group">
		<label class=" control-label">Alternate Mobile No.</label>
		<div class="">
			<div class="input-group">
				<span class="input-group-addon"><?php echo e($logged_userinfo->phone_code); ?></span>
				<input name="address[alternate_mobile]" id="alternate_mobile" placeholder="Alternate Mobile" class="form-control" value="<?php echo e(isset($address->alternate_mobile) ? $address->alternate_mobile : ''); ?>" onkeypress="return isNumberKey(event)" data-err-msg-to="#alternate_mobile_err">
			</div>	
			<span id="alternate_mobile_err"></span>
		</div>			
	</div>
	<?php if(empty($address_type) && empty($add_address)): ?>
	<div class="form-group">
		<label class=" control-label">Address Type <span class="text-danger">*</span></label>
		<div class="">								
			<label class="radio-inline">
			  <input type="radio" name="address[address_type]" value="1" data-err-msg-to="#addr_type_err">Home
			</label>
			<label class="radio-inline">
			  <input type="radio" name="address[address_type]" value="3" data-err-msg-to="#addr_type_err">Office/Commercial
			</label>
			<span id="addr_type_err"></span>
		</div>	
	</div>		
    <?php elseif(isset($address_type) && !empty($address_type)): ?>
        <input type="hidden" name="address[address_type]" value="<?php echo e($address_type); ?>">
	<?php elseif(isset($add_address) && !empty($add_address)): ?>
	    <input type="hidden" name="address[address_type]" value="<?php echo e($add_address); ?>">
	<?php endif; ?>
	<div class="form-group">	
		<div class="col-sm-offset-3 ">								
			<div class="checkbox-inline">
			  <label><input name="address[is_default]" type="checkbox" value="1" <?php echo e((!empty($address->is_default))? 'checked':''); ?>>Make this is my default address</label>
			</div>			
		</div>	
	</div>
	<!--div class="form-group">
		<label class=" control-label"> </label>
		<div class=" fieldgroup">
			<button name="submit" type="submit"  id="save_chng" class="btn btn-md btn-primary" disabled><i class="fa fa-save"></i> Save
			</button>                      
		</div>
	</div-->
</form>