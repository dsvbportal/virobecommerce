@extends('admin.common.layout')
@section('pagetitle')
Supplier Brand
@stop
@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">Affiliate Ranks</h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="brand_list" name="brand_list" action="{{URL::to('admin.aff.ranks')}}" method="get">
                            <div class="col-sm-3">
                                <input type="text" name="term" placeholder="User name" id="term" class="form-control">
                            </div>
                            <div class="col-sm-3">
                                <select name="country_id" id="country_id" class="form-control">
									@if(!empty($countries))
										<option value="">--Country--</option>
										@foreach($countries as $country)
											<option value={{$country->country_id}}>{{$country->country}}</option>
										@endforeach
									@endif	
								</select>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table id="table3" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Affiliate Name</th>
							<th>User Name</th>
                            <th>Rank</th>
							<th>Country</th>
                            <th class="text-right">GQV-1G</th>
                            <th class="text-right">GQV-2G</th>
                            <th class="text-right">GQV-3G</th>
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
<script src="{{asset('js/providers/admin/affiliate/aff_ranks.js')}}"></script>	
@stop
