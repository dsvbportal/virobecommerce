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

                <h4 class="panel-title">Verify Affiliate</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                    <form id="user_verification_details" action="{{route('admin.aff.verify_affiliate')}}" method="get">
                        <div class="col-sm-3">
                                <input type="text"  placeholder="Username" name="uname" id="uname" class="form-control" value=""/>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="start_date" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="end_date" placeholder="To">
                            </div>
                        </div>
						 <div class="col-sm-3">
                                 <select name="search_status" id="status"  class="form-control" >
                                    <option value="all">- Select -</option>
                                    <option value="<?php echo Config::get('constants.VERIFY_AFFILIATE_STATUS.PENDING');?>">Unverified</option>
                                    <option value="<?php echo Config::get('constants.VERIFY_AFFILIATE_STATUS.ACTIVE');?>" >Verified</option>
                                    <option value="<?php echo Config::get('constants.VERIFY_AFFILIATE_STATUS.REJECTED');?>" >Rejected</option>
                                </select>
                        </div>
                        <div class="col-sm-3">
                            <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
							 <button type="button" id="resetbtn" class="btn btn-sm  btn-primary">Reset</button>
                        </div>
						
                    </form>
                </div>
            </div>
            <div id="msg"></div>
            <table id="user_verification_list" class="table table-striped">
                <thead>
                    <tr>
                            <th>Created On</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Proof For</th>
                            <th>Document Type</th>
                      <!--  <th>Document</th>
                            <th>Status</th>
                            <th>Action</th>-->
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
<script src="{{asset('js/providers/admin/affiliate/user_verification.js')}}"></script>
@stop
