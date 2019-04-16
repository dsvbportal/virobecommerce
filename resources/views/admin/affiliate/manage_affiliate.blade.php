@extends('admin.common.layout')
@section('pagetitle')
Affiliate
@stop
@section('top_navigation')

@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
		<div id="users-list-panel">
			<div class="panel panel-default" id="list">
				<div class="panel-heading">
                <h4 class="panel-title">Manage Affiliate</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                    <form id="manage_user_details" action="{{route('admin.aff.view')}}" method="get">
                      
                        <div class="col-sm-3">
                            <div class="input-group">
							<input type="text" id="search_text" name="search_text" class="form-control" placeholder="{{trans('admin/general.search_term_ph')}}">
                            <div class="input-group-btn">
                                <button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
                                <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="User_code" type="checkbox" checked>{{trans('admin/affiliate/admin.affiliate_id')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="UserName" type="checkbox">{{trans('admin/affiliate/admin.username')}}</label></li> 
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FullName" type="checkbox">{{trans('admin/affiliate/admin.fullname')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Email" type="checkbox">{{trans('admin/affiliate/admin.email')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Mobile" type="checkbox">{{trans('admin/affiliate/admin.mobile')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="ReferredBy" type="checkbox">{{trans('admin/affiliate/admin.referrerd_by')}}</label></li>
								<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="ReferredGroup" type="checkbox">{{trans('admin/affiliate/admin.referrerd_group')}}</label></li>
                             </ul>
                            </div>
							</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="start_date" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="end_date" placeholder="To">
                            </div>
                        </div>
                        <div class="col-sm-5">
                         <button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i>Search</button>
						 <button type="button" id="reset" class="btn btn-primary btn-sm"><i class="fa fa-repeat"></i> {{trans('admin/general.reset_btn')}}</button>
						<button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                         <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>
					   </div>
                    </form>
                </div>
            </div>
            <div id="msg"></div>
            <table id="manage_user_list" class="table table-striped">
                <thead>
                    <tr>
                    <th  nowrap="nowrap">{{trans('admin/affiliate/admin.doj')}}</th>
                    <th>Affiliate Details</th>  
					<th>{{trans('admin/affiliate/admin.referred_by')}}</th>
					<th>Generation</th>
					<th>Upline</th>
					<th>Root ID</th>
					<th>{{trans('admin/affiliate/admin.country')}}</th>
					<th>{{trans('admin/general.status')}}</th>           
				    <th>{{trans('admin/general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        @include('admin.meta-info')
    </div>
    </div>
	
<div id="view_details" >
    
</div>
		<div id="change_Member_pwd" style="display:none;">
			@include('admin.affiliate.change_pwd')
		</div>			
		<div id="change_Member_security_pin" style="display:none;">
			@include('admin.affiliate.change_security_pin')
		</div>	
		<div id="edit_details" style="display:none;">
			 @include('admin.affiliate.user_editdetails')
		</div>

		<div id="change_email" style="display:none;">
			 @include('admin.affiliate.change_email')
		</div>

		<div id="change_mobile" style="display:none;">
			 @include('admin.affiliate.change_mobile')
		</div>
	</div>	


@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('affiliate/validate/lang/change-pwd.js')}}"></script>
<script src="{{asset('affiliate/validate/lang/change-mobile.js')}}"></script>
<script src="{{asset('js/providers/admin/affiliate/manage_user.js')}}"></script>
@stop
