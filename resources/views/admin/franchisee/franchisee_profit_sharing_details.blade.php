@extends('admin.common.layout')
@section('pagetitle')
Merchant Enrolment Fee
@stop
@section('top_navigation')
@section('layoutContent')  
	<div id="users-list-panel">
		<div class="panel panel-default" id="list">
		
			<div class="panel-heading">
				<h4 class="panel-title">Profit Sharing Details - {{$created_on_date}}</h4>
			</div>
		
			<div class="panel_controls">
				
			</div>
			
			<div class="panel-body no-padding">
				<div id="msg"></div>
				<table id="profit_sharing_details_tbl" class="table table-striped">
					<thead>
						 <tr>
						        <th>Date</th>
								<th>Channel Partner</th>
								<th>Channel Partner Type</th>
								<th>Sales Amount</th>
								<th>Commission Percentage</th>							
								<th>Earnings</th>
								<th>Tax</th>
								<th>Net Credit</th>
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
<script src="{{asset('js/providers/admin/franchisee/profit_sharing_details.js')}}"></script>
@stop
