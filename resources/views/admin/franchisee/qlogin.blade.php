@extends('admin.common.layout')
@section('pagetitle')
Quick Login
@stop
@section('layoutContent')
    <div class="row">
       <div class="col-sm-12">
             <div class="panel panel-default">
			     <div class="panel-heading">		
				    <h4 class="panel-title">Channel Partner Quick Login</h4>
				  </div>
                  <div class="panel-body">
                     <form action="{{route('admin.franchisee.quick_login')}}" method="POST" class='form-horizontal form-validate' id="quick_login"  enctype="multipart/form-data">
                       <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Channel Partner Uname:</label>
                        <div class="col-sm-4">
                            <input type="text" name="uname" id="uname" class="form-control"  placeholder="Enter Channel Partner Username" data-rule-required="true" value="">
							<div id="err"></div>
                        </div>
                    </div>
		            <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                        <div class="col-sm-6" >
                            <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </div>
                </form>
               </div>
            </div>
          </div>
       </div>
@stop
@section('scripts')
@include('admin.common.datatable_js')
<script src="{{asset('js/providers/admin/affiliate/quick_login.js')}}"></script>  

@stop