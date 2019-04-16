@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/user/create_user.manage_user'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-home"></i>{{trans('franchisee/user/create_user.manage_user')}}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/user/create_user.profile_pagehead')}}</li>
        <li class="active">{{trans('franchisee/user/create_user.manage_user')}}</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-sm-12">
		<div class="col-md-12" id="users-list-panel">
            <div class="box box-primary">
                <div class="box-header with-border" id="list">
                    <form id="user_details_list" class="form form-bordered"  method="post">  
					    <div class="col-sm-3">
							 <div class="form-group">
								<label for="search_term"> {{trans('admin/general.search_term')}} </label>
								<div class="input-group">
									<input type="search" id="search_term" name="search_term" class="form-control" placeholder="{{trans('admin/general.search_term_ph')}}" value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" />
									<div class="input-group-btn">
										<button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('admin/general.filter')}} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="User_code" type="checkbox" checked>	&nbsp;{{trans('franchisee/user/create_user.usercode')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="FullName" type="checkbox">	&nbsp;{{trans('franchisee/user/create_user.full_name')}}</label>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="Email" type="checkbox">	&nbsp;{{trans('franchisee/user/create_user.email')}}</label>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="Mobile" type="checkbox">	&nbsp;{{trans('franchisee/user/create_user.mobile')}}</label>
											</li>
											
										</ul>
									</div>
								</div>
								</div>
							</div> 
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="from_date">{{trans('franchisee/general.frm_date')}}</label>
                                <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('franchisee/general.frm_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group  has-feedback">
                                <label for="to_date">{{trans('franchisee/general.to_date')}}</label>
                                <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('franchisee/general.to_date')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                            </div>
                        </div>
					    <div class="col-sm-8">						  
							<button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('franchisee/general.search_btn')}}</button>&nbsp;
							<button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('franchisee/general.reset_btn')}}</button>&nbsp;
												
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table id="user_list" class="table table-bordered table-striped">
                        <thead>
                             <tr>
							     <th>{{trans('franchisee/user/create_user.sl_no')}}</th>
							     <th>{{trans('franchisee/user/create_user.user')}}</th>
                             	<th>{{trans('franchisee/user/create_user.contact')}}</th>
								<th>{{trans('franchisee/user/create_user.address')}}</th>
								<th>{{trans('franchisee/user/create_user.doj')}}</th>					
								<th>{{trans('franchisee/user/create_user.status')}}</th>					
								<th>Actions</th>					
                              </tr>
                            </thead>
                            <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
           </div>	
        </div>	
		
	<div id="change_Member_pwd" style="display:none;">
    @include('franchisee.user.change_pwd')
    </div>
	<div id="edit_details" style="display:none;">
	
	</div>	
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/user/user_list.js')}}"></script>
@stop
