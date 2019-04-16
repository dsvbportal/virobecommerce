@extends('admin.common.layout')
@section('pagetitle')
Affiliate
@stop
@section('top_navigation')

@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">

                <h4 class="panel-title">Star bonus</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
	                  <form id="star_bonus_details" class="form form-bordered" action="{{route('admin.commission.star')}}" method="post">
                       		{!! csrf_field() !!}
                           
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
									 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar form-control-feedback"></i></span>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
									 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar form-control-feedback"></i></span>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /></div>
                                </div>
                            </div>
                            <div class="col-sm-6 mt25">
                                <div class="form-group">
								<label for="from">&nbsp;</label>
                                    <button type="button" id="searchbtn" class="btn btn-success"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>&nbsp;
                                    <button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>&nbsp;
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>   {{trans('affiliate/general.export_btn')}}</button>&nbsp;
                                    <button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 

				   </div>
            </div>
            <div id="msg"></div>
				<table id="star_bonus_commission" class="table table-bordered table-striped" data-rank-url="{{route('admin.commission.rank_log')}}">
				   <thead>
						<tr>                                                    
							<th>Qualified Month</th>
						    <th>Full Name</th>
							<th>User Code</th>						
							<th>Rank</th>
							<th>Commission</th>
							<th>Tax</th>
							<th>Vi-Help</th>
							<th>Net Pay</th>
							<th>Status</th>						   
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
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Log</h4>
			  </div>
			  <div class="modal-body">
					<table id="log_table" class="table table-bordered table-striped">
					   <thead>
					   <tr>
							<td>Month</td>
							<td>Rank</td>
							</tr>
					   </thead>
					   <tbody>
					   </tbody>
					 </table>
					
			  </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			 </div>
		</div>
	</div>
</div>
@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
	<script src="{{asset('js/providers/admin/report/star_bonus.js')}}"></script>
@stop