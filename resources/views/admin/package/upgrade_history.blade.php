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
                <h4 class="panel-title">Purchase History</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                    <form id="upgrade_history" class="form form-bordered" action="{{route('admin.report.purchase_history')}}" method="post">
							<div class="input-group col-sm-3">
								<input type="text" id="search_term" name="search_term" class="form-control" placeholder="{{trans('franchisee/general.search_term')}}">
							</div>
							<div class="col-sm-4">
								 <div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input class="form-control" type="text" id="from_date" name="from_date" placeholder="From">
									<span class="input-group-addon">-</span>
									<input class="form-control datepicker" type="text" id="to_date" name="to_date" placeholder=" To">
								</div>
								</div>

							<div class="col-sm-5">
							  <button type="button" id="search_btn" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
							  <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
							</div>
				     </div>
                  </form>
            </div>
            <div id="msg"></div>
           <table id="purchase_upgrade_histroy" class="table table-bordered table-striped table-responsive ">
							<thead>
								<tr>                                                    
									<th>{{trans('affiliate/general.requested_date')}}</th>
									<th>{{trans('affiliate/general.ref_no')}}</th>									
									<th>{{trans('affiliate/general.amount')}}</th>
									<th>{{trans('affiliate/general.qv')}}</th>
									<th>{{trans('affiliate/general.affiliate')}}</th>
									<th>{{trans('affiliate/general.paymode')}}</th>
									<th>{{trans('affiliate/general.status')}}</th>
									<th>{{trans('affiliate/general.action')}}</th>									
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
          </div>
        @include('admin.meta-info')
      </div>
    </div>
</div>

@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
     <script src="{{asset('js/providers/admin/package/upgrade_histroy.js')}}"></script>
@stop
