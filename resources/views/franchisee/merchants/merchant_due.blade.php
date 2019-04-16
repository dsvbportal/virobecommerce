@extends('franchisee.layout.dashboard')
@section('title','Merchants')
@section('content')																																					
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i>Merchants</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>Merchants</li>
        <li class="active">{{\trans('franchisee/wallet/transactions.breadcrumb_title')}}</li>
    </ol>
</section>
<section class="content">
<div class="row">
    <div class="col-sm-12">
      <div class="box box-primary">
                <div class="box-header with-border">
					<form id="retailers_listfrm" class="form form-bordered" action="#" method="post">						
							<!--  Country List -->
							<div class="col-md-3">
								 <div class="form-group">
									<label for="from"> {{trans('admin/general.country')}}</label>
									<select name="country" id="country" class="form-control">
										<option value="">{{trans('admin/general.country_search')}}</option>
										@if(!empty($country_list))
										@foreach ($country_list as $row)
										<option value="{{$row->country_id}}" {{ (isset($country) && $country == $row->country_id) ? 'selected':''}}>{{$row->country}}</option>
										@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-sm-3">
							 <div class="form-group">
								<label for="search_term"> {{trans('admin/general.search_term')}} </label>
								<div class="input-group">
									<input type="search" id="search_term" name="search_term" class="form-control" placeholder="{{trans('admin/general.search_term_ph')}}" value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" />
									<div class="input-group-btn">
										<button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('admin/general.filter')}} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_code" type="checkbox" checked>	&nbsp;{{trans('admin/seller.merchant_code')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_name" type="checkbox">	&nbsp;{{trans('admin/seller.merchant_name')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_mobile" type="checkbox">	&nbsp;{{trans('admin/seller.mobile')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_email" type="checkbox">	&nbsp;{{trans('admin/seller.email')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_uname" type="checkbox">	&nbsp;{{trans('admin/seller.uname')}}</label>
											</li>
										</ul>
									</div>
								</div>
								</div>
							</div> 
						
							<div class="col-sm-2">
								<div class="form-group has-feedback">
									<label for="from_date"> {{trans('admin/general.frm_date')}}</label>
									<input type="text" id="from" name="from_date" class="form-control datepicker" /> 
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group has-feedback">
									<label for="to_date"> {{trans('admin/general.to_date')}}</label>
									<input type="text" id="to" name="to_date" class="form-control datepicker"/>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group" style="margin-top:25px;">
									<button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>&nbsp;
									<button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>
								</div>
							</div>
						
					</form>
            </div>
            <div class="box-body">
				<table id="retailer" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Merchant</th>
							<th>Due Amount</th>
							<th>City</th>
							<th>Actions</th>														
						</tr>
					</thead>
					<tbody> </tbody>
				</table>
			@include('admin.meta-info')
    </div>
</div>
</section>
<div class="modal fade" id="suppliers_details" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Suppliers Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
<div class="modal fade " id="suppliers_rpwd" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> Supplier Reset Password</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form class="form-horizontal" id="suppliers_reset_pwd">
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">Supplier Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="uname"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">User Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="sid"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">New Password</label>
                            <div class="col-sm-8">
                                <input name="login_password" class="form-control" type="password" id="login_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">Confirm New Password</label>
                            <div class="col-sm-8">
                                <input name="confirm_login_password" class="form-control" type="password" id="confirm_login_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <input type="submit" name="save" id="save" class="btn btn-sm btn-primary" value="Update" >
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Edit Supplier Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('resources/supports/Jquery-loadselect.js')}}"></script>	
<!--<script src="{{asset('js/providers/franchisee/mercahants/seller_list.js')}}"></script>	
<!--<script src="{{asset('js/providers/franchisee/mercahants/meta-info.js')}}"></script>-->	
@stop
