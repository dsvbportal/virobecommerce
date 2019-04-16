@extends('admin.common.layout')
@section('pagetitle')
Account Verification
@stop
@section('layoutContent')
<style>
    .thumb-image {
        float:left;
        width:100px;
        position:relative;
        padding:5px;
    }
    .danger {
        color: red;
    }
    .info {
        height:71px;
        border-left:0px ! important;
    }
    .align{
        padding-top:25px ! important;
        border-right:0px ! important;
        border-left:1px solid #CCC ! important;
    }
    .error{
        color:red;
    }
    .select_font {
        color: #666E77 ! important;
    }
</style>
<div class="pageheader">
    <div class="row">
        <div id="alert-msg" class="alert-msg"></div>
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6"> Affiliate KYC Verification </h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="verification_docs_list" action="{{route('admin.aff.verification')}}">
                            <div class="col-sm-3">
                                <input class="form-control" type="text" id="search_term" name="search_term" placeholder="Search Name">
                            </div>
                            <div class="col-sm-2">
                                <select  name="type_filer"  id="type_filer" class="form-control">
									<option value="" hidden="hidden">-- Select --</option>
									@if(!empty($doc_types))
									@foreach($doc_types as $doc)								
									<option value="{{$doc->document_type_id}}">{{$doc->type}}</option>
									@endforeach
									@endif
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select  name="status"  id="status" class="form-control">
                                    <option value="">-- All --</option>
									@if(!empty($doc_status))
									@foreach($doc_status as $k=>$v)								
									<option value="{{$k}}">{{$v}}</option>
									@endforeach
									@endif
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                    <span class="input-group-addon">-</span>
                                    <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <button id="search" type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
                            </div>
							<div class="col-sm-1">
							    <button id="resetbtn" type="button" class="btn bg-orange btn-sm"><i class="fa fa-refresh"></i> Reset</button>
							</div>
                            <input type="hidden" id="uname" name="uname" value="{{$uname or ''}}">
                        </form>
                    </div>
                </div>
                <table id="image_verify_list" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Name</th>
                            <th>Doc.Type</th>
                            <th>Document</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/affiliate/account_verification.js')}}"></script>	
@stop
