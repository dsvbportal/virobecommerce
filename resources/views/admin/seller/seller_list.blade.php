@extends('admin.common.layout')
@section('pagetitle')
Suppliers List
@stop
@section('top_navigation')
@include('admin.top_nav.supplier_navigation')
@stop
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
<!--                <a href="{{URL::to('admin/suppliers/add')}}" id="create_supplier" class="btn btn-success btn-sm pull-right"><span class="fa fa-plus"></span>Add Suppliers </a>-->
                <h4 class="panel-title">Seller List </h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                    <form id="supplier_list" action="{{URL::to('admin/suppliers')}}" method="get">
                        <input type="hidden" class="form-control" id="status_col"  value ="status_value">
                        <div class="input-group col-sm-3">
                            <input type="text" id="search_text" name="search_text" class="form-control">
                            <div class="input-group-btn">
                                <button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
                                <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
                                    <li><label class="col-sm-12"><input type="checkbox"  name="filterTerms[]"  value="uname"/>Account ID</label></li>
                                    <li><label class="col-sm-12"><input type="checkbox"  name="filterTerms[]"  value="supplier"/>Code</label></li>
                                    <li><label class="col-sm-12"><input type="checkbox"  name="filterTerms[]"  value="mobile"/>Mobile</label></li>
<!--                                <li><label class="col-sm-12"><input type="checkbox" name="filterTerms[]" value="code"/> User Code</label></li>
                                    -->                                </ul>
                            </div>
                        </div>
                        <div class="input-group col-sm-2" class="form-control">
                            <select name="country" id="country" class="form-control selectfont" data-required="true">

                            </select>
                        </div>

                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input class="form-control" type="text" id="from" name="start_date" placeholder="From">
                                <span class="input-group-addon">-</span>
                                <input class="form-control" type="text" id="to" name="end_date" placeholder="To">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                            <!--input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Export" formtarget="_new"/>
                            <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/-->
                        </div>
                    </form>
                </div>
            </div>
            <div id="msg"></div>
            <table id="dt_basic" class="table table-striped">
                <thead>
                    <tr>
                        <th nowrap="nowrap">Created On</th>
                        <th>Supplier (Account ID)</th>
                        <th>Contacts</th>
                        @if($status_name != 'approvals')
                        <th>Products & Orders</th>
                        @endif
                        <th>Country</th>
                        @if($status_name != '')
                        <th>Registration Steps</th>
                        @endif
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        @include('admin.meta-info')
    </div>
</div>
<div class="modal fade" id="suppliers_details" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Suppliers Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
<div class="modal fade " id="suppliers_rpwd" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> Supplier Reset Password</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form class="form-horizontal" id="suppliers_reset_pwd">
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">Supplier Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="uname"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">User Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="sid"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">New Password</label>
                            <div class="col-sm-8">
                                <input name="login_password" class="form-control" type="password" id="login_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">Confirm New Password</label>
                            <div class="col-sm-8">
                                <input name="confirm_login_password" class="form-control" type="password" id="confirm_login_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <input type="submit" name="save" id="save" class="btn btn-sm btn-primary" value="Update" >
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Edit Supplier Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/Jquery-loadselect.js')}}"></script>	
<script src="{{asset('resources/supports/admin/seller/seller_list.js')}}"></script>	
<script src="{{asset('resources/supports/admin/seller/meta-info.js')}}"></script>	
@stop
