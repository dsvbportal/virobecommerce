@extends('admin.common.layout')
@section('pagetitle')
Merchant Enrolment Fee
@stop
@section('top_navigation')
@section('layoutContent')  
	<div id="users-list-panel">
		<div class="panel panel-default" id="list">
		
			<div class="panel-heading">
				<h4 class="panel-title">Merchant Enrolment Fee - {{$created_on_date}}</h4>
			</div>
		
			<div class="panel_controls">
				<div class="row">
					<form id="merchant_enrol_fee_details" action="{{route('admin.franchisee.mer_enrollment_fee')}}" method="get">
					
						<div class="col-sm-3">
							<div class="input-group">
							<input type="text" id="search_text" name="search_text" class="form-control" placeholder="{{trans('admin/general.search_term_ph')}}">
							<div class="input-group-btn">
								<button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
								<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
							    <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="store_code" type="checkbox" checked>Merchant ID</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="store_name" type="checkbox">Merchant</label></li>
							 </ul>
							</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								 <input class="form-control" type="text" id="from" name="from" placeholder="From">
								<span class="input-group-addon">-</span>
								 <input class="form-control" type="text" id="to" name="to" placeholder="To">
							</div>
						</div>
	
						<div class="col-sm-5">
							<button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i>Search</button>
							<button type="button" id="reset" class="btn btn-primary btn-sm"><i class="fa fa-repeat"></i> {{trans('admin/general.reset_btn')}}</button>
							 
							 <!--<button type="button" id="resetbtn" class="btn btn-sm  btn-primary">Clear filter</button>-->
						</div>					
					</form>
				</div>
				
				<div class="row" style="margin-top:10px;">
				<div class="col-sm-12">
			   <h5>
			   <p>Channel Partner : <b> {{$franchisee_details->company_name.' '.'('.$franchisee_details->user_code.')'}}</b>&nbsp;&nbsp;Access : <b>{{$franchisee_details->franchisee_type}}</b></p>
			     
				 </h5>
				 </div>
		    	</div>
			</div>
			
			<div class="panel-body no-padding">
				<div id="msg"></div>
				<table id="merchant_enrolment_fee_details" class="table table-striped">
					<thead>
						 <tr>
						    <th>Created on</th>
							<th>Merchant Enrollment Fee</th>
							<th>State</th>							
							<th>District</th>
							<th>Fee</th>
						</tr>
					  </thead>
					  <tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('admin.meta-info')
	</div>


@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/franchisee/merchant_enrolment_fee_details.js')}}"></script>
@stop
