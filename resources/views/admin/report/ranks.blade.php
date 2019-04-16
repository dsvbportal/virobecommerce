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
                <h4 class="panel-title">Affiliate Ranks</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
	                  <form id="form" class="form form-bordered" action="" method="post">
							<div class="col-sm-2">
									<div class="form-group has-feedback">
										<label for="from">Search terms</label>
										<input type="text" id="terms" name="terms" class="form-control" placeholder="User name/User code" />
									</div>
							</div>							
							<div class="col-sm-2">
                                <div class="form-group has-feedback">
								    <label for="from">Country</label>
								    <select name="country_id" id="country_id" class="form-control">
										<option value="">Country</option>
									  @if(!empty($contries))
										  @foreach($contries as $country)
												<option value="{{$country->country_id}}">{{$country->country}}</option>
											@endforeach
									 @endif
									</select>
                                </div>
                            </div>							
                            <div class="col-sm-6">
                                <div class="form-group mt25">
								 <label for="from">&nbsp;</label>
                                    <button type="button" id="searchbtn" class="btn btn-success"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>{{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
				   </div>
            </div>
            <div id="msg"></div>
				<table id="ranks_table" class="table table-bordered table-striped">
				   <thead>
						<tr> 
							 <th class="text-left">Month</th>						
							 <th class="text-left">Full Name</th>
							 <th class="text-left">User Name</th>
							 <th class="text-left">User Code</th>
							 <th class="text-left">Rank</th>
							 <th class="text-left">Country</th>
							 <!--<th class="text-left">Your Current Rank</th>-->
							 <th class="text-right">GQV - 1G</th>							     
							 <th class="text-right">GQV - 2G</th>
							 <th class="text-right">GQV - 3G</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
</div>
	<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Log</h4>
						  </div>
						  <div class="modal-body">
								<table id="log_table" class="table table-bordered table-striped">
								   <thead>
								   <tr>
										<td>Month</td>
										<td>Rank</td>
										</tr>
								   </thead>
								   <tbody>
								   </tbody>
								 </table>
								
						  </div>
						 <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						 </div>
					</div>
				</div>
			</div>
	@include('admin.common.datatable_js')
	@include('admin.common.assets')
	
@stop

@section('scripts')
	<script src="{{asset('js/providers/admin/report/ranks.js')}}"></script>
@stop