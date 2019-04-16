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

                <h4 class="panel-title">Team Commssion</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
	                   <form id="form_team_bonus" class="form form-bordered" action="{{route('admin.commission.team')}}" method="post">
                       		{!! csrf_field() !!}
                           
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
									 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar form-control-feedback"></i></span>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/general.frm_date')}}" /></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
									 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar form-control-feedback"></i></span>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/general.to_date')}}" /></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
							<label for="from">&nbsp;</label>
                                <div class="form-group mt25">
                                    <button type="button" id="searchbtn" class="btn btn-success"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>&nbsp;{{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 

				   </div>
            </div>
            <div id="msg"></div>
                  
                    <table id="team_bonus" class="table table-bordered table-striped">
                       <thead>
                            <tr>                                                    
                                 <th class="text-center">{{trans('affiliate/bonus/team_bonus.month')}}</th>
                                <th>User name</th>
							     <th>User Code</th>
                                 <!-- <th>{{trans('affiliate/bonus/team_bonus.1g_new')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.2g_new')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.total_1g')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.total_2g')}}</th>-->
                                 <th>{{trans('affiliate/bonus/team_bonus.matching_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.earnings')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.Tax')}}</th>
								 <th>{{trans('affiliate/bonus/team_bonus.ngo_wallet')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.net_pay')}}</th>
                                  <th>{{trans('affiliate/bonus/team_bonus.bonus_status')}}</th>
                                                                               
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
				</div>
			</div>
		</div>	
		<!-- /.row -->
   	
</div>

@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/report/team_bonus.js')}}"></script>
@stop