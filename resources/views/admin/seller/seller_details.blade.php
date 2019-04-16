@extends('admin.common.layout')
@section('pagetitle')
Suppliers Details
@stop
@section('layoutContent')
@if(!empty($suppliers_details))
	
<div class="panel panel-default">
    <div class="panel-heading">
        <a href="{{URL::to('admin/seller/edit/'.$suppliers_details->uname)}}" id="create_supplier" class="btn btn-success btn-sm pull-right"><span class="fa fa-edit"></span> Edit</a>
        <h4 class="panel-title">Suppliers Details</h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabbable tabs-left tabbable-bordered">
                    <ul class="nav nav-tabs">
                        @if(!empty($suppliers_details->steps))
							@foreach($suppliers_details->steps as $step)								
								<li ><a href="#step-{{$step->step_id}}" data-toggle="tab">{{$step->name}}</a></li>								
							@endforeach
                        @endif
                    </ul>
                    <div class="tab-content" style="min-height:250px;">
                        @if(!empty($suppliers_details->steps))
                        @foreach($suppliers_details->steps as $step)
                        <div id="step-{{$step->step_id}}" class="tab-pane">
                            <div class="checkbox pull-right">
								
                                @if(in_array($step->step_id, $suppliers_details->completed_steps))
                                <label><input type="checkbox" data-supplier_id="{{$suppliers_details->supplier_id}}" class="step-status" data-step_id="{{$step->step_id}}" data-url="{{URL::to('admin/seller/verify-step')}}" {{in_array($step->step_id,$suppliers_details->verified_steps)?'checked="checked"':''}}/>Verified</label>
                                @endif
                            </div>
                            <h3>{{$step->name}}</h3><hr/>
                            @if($step->step_id!=4)
                            <table class="table table-bordered table-default details">
                                <tbody>
                                    @if(!empty($step->fields))
                                    @foreach($step->fields as $field)
                                    <tr>
                                        <th>{{$field['label']}}</th>
                                        <td>{{$field['type']=='text'?$field['value']:'<a target="_blank" href="'.$field['value'].'">Download</a>'}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @else
                            <div class="panel panel-default" id="list">
                                <div class="panel-heading">
                                    <h4 class="panel-title col-sm-6">Supplier Verification </h4>
                                </div>
                                <div class="panel_controls">
                                    <div class="row">
                                        <form id="verification_docs_list" class="col-sm-12" action="{{URL::to('admin/seller/verification/'.$suppliers_details->uname)}}">
                                            <!--div class="col-sm-3">
                                                <input class="form-control" type="text" id="search_term" name="search_term" placeholder="Search">                                   
                                            </div-->
                                             <input type="hidden" id="account_id" name="account_id" value="{{$suppliers_details->account_id}}">
                                            <div class="col-sm-3">
                                                <select name="type_filer" id="type_filer" class="form-control"></select>
                                            </div>
                                            <div class="col-sm-3">
                                                <!--select  name="status"  id="status" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="0" {{isset($status) && $status==0?'selected="selected"':''}}>Pending</option>
                                                    <option value="1" {{isset($status) && $status==1?'selected="selected"':''}}>Verified</option>
                                                    <option value="2" {{isset($status) && $status==2?'selected="selected"':''}}>Cancelled</option>
                                                </select-->
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Verified</option>
                                                    <option value="2">Cancelled</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                                    <span class="input-group-addon">-</span>
                                                    <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <table id="image_verify_list" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Created On</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/admin/seller/details.js')}}"></script>	
<script src="{{asset('resources/supports/admin/seller/account_verification.js')}}"></script>	
@stop

