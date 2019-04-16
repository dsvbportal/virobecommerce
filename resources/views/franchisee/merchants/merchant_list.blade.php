@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/merchant/merchant_details.manage_merchant'))
@section('content')			
    <!-- Content Header (Page header) -->
    <section class="content-header">
         <h1><i class="fa fa-home"></i>{{trans('franchisee/merchant/merchant_details.manage_merchant')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/merchant/merchant_details.profile_pagehead')}}</li>
        <li class="active">{{trans('franchisee/merchant/merchant_details.manage_merchant')}}</li>
      </ol>
	    
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">
			
             <div class="box box-primary" id="users-list-panel">
					<div class="box-header with-border">
	                   <form id="retailers_listfrm" class="form form-bordered" action="{{route('fr.merchants.list')}}" method="post">		
                       {!! csrf_field() !!}                           
							<div class="col-sm-3">
							 <div class="form-group">
								<label for="search_term"> {{trans('franchisee/general.search_term')}} </label>
								<div class="input-group">
									<input type="search" id="search_term" name="search_term" class="form-control" placeholder="{{trans('franchisee/general.search_term')}}" value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" />
									<div class="input-group-btn">
										<button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('franchisee/general.filter')}} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_code" type="checkbox" checked>	&nbsp;{{trans('franchisee/merchant/merchant_details.mer_id')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_name" type="checkbox">	&nbsp;{{trans('franchisee/merchant/merchant_details.mer_name')}}</label>
											</li>
											
										</ul>
									</div>
								</div>
								</div>
							</div> 
                            
                           <div class="col-sm-2">
								<div class="form-group has-feedback">
									<label for="from_date"> {{trans('franchisee/general.frm_date')}}</label>
									<input type="text" id="from" name="from_date" class="form-control datepicker" placeholder="{{trans('franchisee/general.frm_date')}}" /> 
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group has-feedback">
									<label for="to_date"> {{trans('franchisee/general.to_date')}}</label>
									<input type="text" id="to" name="to_date" class="form-control datepicker"  placeholder="{{trans('franchisee/general.to_date')}}"/>
								
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
                <table id="supplier_list" class="table table-bordered table-striped">
					<thead>
						<tr>
						    <th>{{trans('franchisee/general.sl_no')}}</th>
							<th>{{trans('franchisee/merchant/merchant_details.merchant_detail')}}</th>
                            <th>{{trans('franchisee/merchant/merchant_details.rsm')}}</th>							
							<th>{{trans('franchisee/merchant/merchant_details.fsm')}}</th>
							<th>{{trans('franchisee/merchant/merchant_details.fse')}}</th> 
							<th>{{trans('franchisee/merchant/merchant_details.asm')}}</th>
							<th>{{trans('franchisee/merchant/merchant_details.signed_on')}}</th>
							<th>{{trans('franchisee/general.status')}}</th>
							<th>{{trans('franchisee/general.action')}}</th>
						</tr>
					  </thead>
					<tbody> </tbody>
				    </table>
					</div>
				</div>
				
			</div>
			<!-- ./col -->		
            		
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('franchisee.common.datatable_js')
<script src="{{asset('js/providers/franchisee/merchant/merchant_list.js')}}"></script>	
<script src="{{asset('js/providers/file_upload.js')}}"></script>
@stop