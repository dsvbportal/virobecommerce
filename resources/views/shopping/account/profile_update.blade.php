@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.header')
@stop
@section('content')

	<div class="main-shop-page  mt-30">		 
        <div class="container">
           <div class="row">
		   <div class="col-lg-3 col-md-3 col-sm-6" id="left_column">				
            	<div class="inner-desc">
                    <h4>My Account</h4>
                    <ul>
						<li><a href="{{route('ecom.account.profile')}}"><span>Personal Information</span></a></li>
						<li><a href="{{route('ecom.account.security')}}"><span>Security</span></a></li>
						<li><a href="{{route('ecom.logout')}}" id="logout"><span>Logout</span></a></li>


					</ul>
					<hr />
					<h4>ORDERS</h4>
					<ul>
					<li ><a href="{{route('ecom.account.my-orders')}}"><span> My Orders</span></a></li>
					</ul>
					<hr />
					<h4>MY STUFF</h4>
					<ul>
					<li><a href="{{route('ecom.product.wishlist')}}"><span>My Wishlist</span></a></li>
					</ul>
					<hr />
                </div>
            </div>
		   <div class="col-lg-9 col-md-3 col-sm-6">
		   <ul class="main-thumb-desc nav tabs-area">
				<li ><a class="active" data-toggle="pill" href="#home" data="{{route('ecom.account.profile')}}">My Profile</a></li>	
				<li id="change_address_tab"  ctr_url="{{route('ecom.account.get_address')}}"><a data-toggle="pill" href="#address-tab">My Address Book</a></li>
				<li id="change_bank_tab"  ctr_url="{{route('ecom.account.bank_detail')}}"><a data-toggle="pill" href="#bank-tab">Bank Details</a></li>	
			</ul>
			<div class="tab-content thumb-content border-default">
				<div id="home" class="tab-pane fade show active">      
					<div class="contentpanel">
						<div class="panel panel-default">
							<div class="panel-body">						
							
									<div class="col-md-12 mt-30">
									
									<form class="form-horizontal" id="profile_updatefrm" action="{{route('ecom.account.update')}}" method="post" autocomplete="off" enctype="multipart/form-data">
										<input name="display_name" type="hidden" id="display_name" placeholder="Display Name" class="form-control" value="{{$logged_userinfo->uname or ''}}">
										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-2">
													<!-- img src="{{asset('resources\uploads\account/default-user-icon.jpg')}}" class="img-circle" alt="Cinque Terre"-->
													<!-- img src="{{$logged_userinfo->profile_img}}" class="img-circle" alt="Cinque Terre"-->
													<img class="img img-circle editable-img col-sm-10" data-input="#attachment" id="attachment-preview" src="{{$logged_userinfo->profile_img}}"  data-old-image="{{$logged_userinfo->profile_img}}"/>
													<span id="profile_logo-error"></span>
												</div>
												<div class="col-sm-9">
													<h4>{{$logged_userinfo->uname}}</h4>
													<p>{{$logged_userinfo->email}}</p>
													<p>{{$logged_userinfo->phone_code}}-{{$logged_userinfo->mobile}}</p>
													<input class="cropper ignore-reset" data-hide="#profile" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png" data-err-msg-to="#profile_logo-error" data-typeMismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="attachment" title="adfasdf" data-default="{{config('constants.ACCOUNT.PROFILE_IMG.WEB.160x160').'store.png'}}" data-width="360" data-height="360"/>
												</div>										 
											</div>  
										</div>
										
									<div class="mt-30">
										<div class="col-sm-8">
											<div class="form-group">
												<label class="col-sm-4 control-label">First Name <span class="danger"  style="color: red;" > * </span></label>
												<div class="col-sm-8">
													<input {!!build_attribute($pufields['first_name']['attr'])!!} id="first_name" placeholder="First Name" class="form-control" value="{{$logged_userinfo->first_name or ''}}" onkeypress="return alphaBets(event)">
												</div>  
											</div>  
										</div>
										<div class="col-sm-8">
											<div class="form-group">
												<label class="col-sm-4 control-label">Last Name <span class="danger"  style="color: red;" > * </span></label>           
												<div class="col-sm-8">                  
													<input {!!build_attribute($pufields['last_name']['attr'])!!} id="last_name" placeholder="Last Name" class="form-control" value="{{$logged_userinfo->last_name or ''}}" onkeypress="return alphaBets(event)">
												</div>             
											</div> 
										</div>					
										<div class="col-sm-8">
											<div class="form-group">
												<label class="col-sm-4 control-label">Gender <span class="danger"  style="color: red;" > * </span></label>
												<div class="col-sm-8">
													@if(!empty($gender))
													   @foreach ($gender as $k=>$v)  
													<input type="radio" name="gender" value='{{$k}}'   {{(isset($logged_userinfo->gender) && $k == $logged_userinfo->gender) ? 'checked': ''}} >{{$v}}
													@endforeach
													@endif		
												</div>  
											</div>  
										</div>
										<div class="col-sm-8">
											<div class="form-group">
												<label class="col-sm-4 control-label">Date Of Birth <span class="danger"  style="color: red;" > * </span></label>           
												<div class="col-sm-8">      
													<div class="input-group">										
														<input {!!build_attribute($pufields['dob']['attr'])!!} type="text" name="dob" id="dob"  class="form-control datepicker" placeholder="Date of birth" value="{{$logged_userinfo->dob or ''}}" data-err-msg-to="#dob_err"/>   
														<!-- span class="input-group-addon"><i class="fa fa-calendar"></i></span -->										   
													</div>             
												</div>             
											</div> 									
										</div>					
										<div class="form-group">							
											<div class="col-sm-12">						
												<div class="col-sm-8">
													<button name="submit" type="submit"  id="save_chng" class="return-customer-btn"><i class="fa fa-save"></i> Save
													</button>  
												</div>												
											</div>
										</div>
									</div>
									</form>							
								</div>													
																					
							</div>




						</div>
					</div>		
				</div>                       
				<div id="address-tab" class="tab-pane fade">
					<div id='address_content'>	
					
					</div>
				</div>
				<div id="bank-tab" class="tab-pane fade">			
					<div class="contentpanel">
						<div class="panel panel-default">
							<div class="panel-body">
								<div id="idfy">
								<form class="form-horizontal" id="bank_frm" action=""  ctr_url="{{route('ecom.account.relogin_bank')}}"  autocomplete="off">	  
									<div id='bank_content'>			
									</div>
									<input type="hidden" value="" id="row_id">
								</form>
								</div>
							</div>
						</div>
					</div>
				</div>         
			</div>
			</div>
		</div>
	</div>
</div>
@include('shopping.common.cropper')
	@include('shopping.common.newsletter')
@stop
@section('scripts')
@include('shopping.common.cropper_css_js')
	<script type="text/javascript" src="{{asset('js/providers/ecom/account/profile.js')}}"></script> 
@stop