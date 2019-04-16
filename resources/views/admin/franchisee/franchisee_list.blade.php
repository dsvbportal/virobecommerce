@extends('admin.common.layout')
@section('title','Manage Centers')
@section('layoutContent')
<div class="row">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <h4 class="panel-title">Channel Partner</h4>
            </div>
            <div class="panel_controls">
				<div class="row"> 
					<form id="franchiseeListFrm" action="{{route('admin.franchisee.list')}}" class="form form-bordered" method="post">                
						<div class="col-sm-3">
							<div class="input-group">
								<input type="text" id="search_term" name="search_term" class="form-control">
								<div class="input-group-btn">
									<button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
									<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" style="width:auto">
									<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="UserName" type="checkbox" checked> Account Id</label></li> 
									<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FranchiseeName" type="checkbox"> Channel Partner Name</label></li>
									
								 </ul>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="franchisee_type" id="franchisee_type" class="form-control" >
									<option value="">All Type</option>
									@if(isset($franchisee_types) && !empty($franchisee_types))
									@foreach($franchisee_types as $type)
									<option value="{{$type->franchisee_typeid}}" {{isset($franchisee_type) && $franchisee_type==$type->franchisee_typeid?'selected="selected"':''}}>{{$type->franchisee_type}}</option>
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
							<div class="form-group">
								<input  name ="search" type="button" class="btn btn-sm btn-primary" value="Search" />
								<?php /*<input  name ="export" type="button" class="btn btn-sm btn-primary" value="Export" />
								<input  name ="print" type="button" class="btn btn-sm btn-primary" value="Print" /> */?>
								<button name="resetBtn" id="resetBtn" type="reset" class="btn btn-sm btn-warning"><i class="fa fa-repeat"></i> Reset</button>
							</div>
						</div>                    
					</form>
				</div>
			</div>
			<div class="panel-body">       
                <table id="franchiseeList" data-url="{{route('admin.franchisee.list')}}" class="table table-bordered table-striped dataTable no-footer">
                    <thead>
                        <tr>
                            <th>DOR</th>                            
                         	<th>Channel Partner Name</th>                    
                            <th>Channel Partner Type</th>
                            <th>Country</th>        							
                            <th>Contact Person</th>        							
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>                                           
                </table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="view_user_profile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 650px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> CHANGE YOUR PASSWORD</h4>
            </div>
            <div class="modal-body">
                <div id="msg"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@include('admin.franchisee.change_pwd')
@include('admin.franchisee.change_security_pin')
@include('admin.franchisee.change_email')
@include('admin.franchisee.change_mobile')
@include('admin.common.datatable_js')
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/franchisee/franchisee.js')}}"></script>
@stop