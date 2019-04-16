@extends('admin.common.layout')
@section('pagetitle')
Commission Report
@stop
@section('top_navigation')
@section('layoutContent')
<div class="row">
   	 <div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <h4 class="panel-title">Channel Partner Commission Report</h4>
            </div>
        <div class="panel_controls">
          <div class="row">			 
			 <form id="franchisee_commission" class="form form-bordered" action="{{route('admin.franchisee.fundtransfer_commission')}}" method="get">
				 <div class="col-sm-3">
						<div class="input-group">
							<input type="search" id="search_term" name="search_term" class="form-control" placeholder="{{trans('admin/general.search_term')}}"/>
							<div class="input-group-btn">
							</div>
						</div>
					</div>
						
				<div class="col-sm-2">
							<div class="form-group">
								<select name="status" class="form-control" id="status" >
                                <option value="">Select Status</option>
                                <?php
                                foreach ($status as $stat)
                                {
                                    ?>
                                    <option value="<?php echo $stat->com_status_id;?>"><?php echo $stat->status_name;?> </option>
                                    <?php
                                }
                                ?>
                            </select>
							</div>
						</div>		
						<div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input class="form-control" type="text" id="from_date" name="from_date" placeholder="From">
								 <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to_date" name="to_date" placeholder="To">
                            </div>
                        </div>	
                          <div class="col-sm-3">
                            <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>
                            <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>
					      </div>
                  </form>
				 </div>
                </div>
		<div class="panel-body">       
                <table id="franchisee_commission_list" class="table table-bordered table-striped dataTable no-footer" >
                <thead>
					<tr>
						<th>Date</th>
						<th>Receiver</th>
						<th>From</th>
						<th>To</th>
						<th>Transaction Details</th>
						<th>Amount</th>
						<th>Commission</th>
						<th>Status</th>
						<th>Verified On</th>
					</tr>
				  </thead>
				  <tbody>
			  </tbody>
            </table>
         </div>
        </div>
      </div>
   </div>
@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
    <script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
    <script src="{{asset('js/providers/admin/franchisee/franchisee_commission_report.js')}}"></script>
@stop
