@extends('admin.common.layout')
@section('pagetitle')
Merchant Enrolment Fee
@stop
@section('top_navigation')
@section('layoutContent')  
	<div id="users-list-panel">
		<div class="panel panel-default" id="list">
			<div class="panel-heading">
				<h4 class="panel-title">Profit Sharing</h4>
			</div>
			<div class="panel_controls">
				<div class="row">
					<form id="profit_sharing_form" action="{{route('admin.franchisee.profit_sharing')}}" method="get">
						<input type="hidden" class="form-control" id="status_col"  value ="status_value">
						<div class="col-sm-3">
							<div class="input-group">
							<input type="text" id="search_text" name="search_text" class="form-control" placeholder="{{trans('admin/general.search_term_ph')}}">
							<div class="input-group-btn">
								<button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
								<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="chanel_code" type="checkbox" checked>Channel Partner ID</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="channel_name" type="checkbox">Channel Partner Name</label></li> 
							 </ul>
							</div>
							</div>
						</div>
					<div class="col-sm-2">
                        	<div class="from-group">
							<select name="fr_type" class="form-control" id="fr_type">
							   <option value="">Select Franchisee Type</option>
								@if(!empty($franchisee_type))
								   @foreach ($franchisee_type as $fr_type)
								   <option value="{{$fr_type->franchisee_typeid}}">{{$fr_type->franchisee_type}}</option>
								   @endforeach
								   @endif
								  </select>
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
	
						<div class="col-sm-3">
							<button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i>Search</button>
							<button type="button" id="reset" class="btn btn-primary btn-sm"><i class="fa fa-repeat"></i> {{trans('admin/general.reset_btn')}}</button>
						</div>					
					</form>
				</div>
			</div>
			<div class="panel-body no-padding">
				<div id="msg"></div>
				<table id="profit_sharing_tbl" class="table table-striped">
					<thead>
						 <tr>
                                <th>Created On</th>
                             	<th>Channel Partner</th>
								<th>Channel Partner Type</th>
								<th>Earnings</th>							
								<th>Tax</th>							
								<th>Net Credit</th>							
								<th>Action</th>
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
<script src="{{asset('js/providers/admin/franchisee/profit_sharing.js')}}"></script>
@stop