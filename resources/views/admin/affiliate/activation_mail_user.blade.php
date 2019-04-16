@extends('admin.common.layout')
@section('pagetitle')
Activation Mail
@stop
@section('top_navigation')
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <h4 class="panel-title">Activation Mail</h4>
            </div>
            <div class="panel_controls">
              
                    <form id="activation_mail_user" action="{{route('admin.aff.activation_mail')}}" method="get">
                        <div class="row">
						    <div class="col-sm-3">
                                <input type="text"  placeholder="User Name" name="username" id="username" class="form-control" value=""/>
                             </div>
					 <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="start_date" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="end_date" placeholder="To">
                            </div>
                        </div>
                       
                        <div class="col-sm-6">
                            <button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
						<!-- <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                             <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>-->
							 <button type="button" id="resetbtn" class="btn btn-sm  btn-primary"><i class="fa fa-repeat"></i> Reset</button>
                        </div>  
					</div>
                    </form>
            </div>
            <div id="msg"></div>
            <table id="user_details" class="table table-striped">
                <thead>
                    <tr>
                    <th  nowrap="nowrap">Updated On</th>
                    <th  nowrap="nowrap">Affiliate ID</th>
                    <th>Affiliate Email</th>
                   <th>{{trans('admin/affiliate/admin.action')}}</th>
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

<script src="{{asset('js/providers/admin/affiliate/activation_mail_users.js')}}"></script>
@stop
