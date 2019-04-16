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
                <h4 class="panel-title">Qualified Volume Report</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                    <form id="qualified_volume_report" class="form form-bordered" action="{{route('admin.report.qualified_volume')}}" method="post">
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
           <table id="qualified_volume_list" class="table table-bordered table-striped table-responsive ">
							<thead>
								<tr>    
                                     <th  nowrap="nowrap">{{trans('admin/affiliate/admin.doj')}}</th>                                                
									 <th>{{trans('admin/affiliate/admin.user_details')}}</th>  
                                     <th>{{trans('admin/affiliate/admin.country')}}</th>
                                     <th>{{trans('admin/affiliate/admin.qv')}}</th>
                                     <th>{{trans('admin/affiliate/admin.updated_on')}}</th>
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
     <script src="{{asset('js/providers/admin/report/qualified_volume.js')}}"></script>
@stop
