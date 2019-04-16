@extends('affiliate.layout.dashboard')
@section('title','My Referred Customers')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>My Referred Customers</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{trans('affiliate/general.dashboard')}}</a></li>
        <li >{{trans('affiliate/general.profile_pagehead')}}</li>
		<li class="active">My Referred Customers</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<div class="col-md-12">
             <div class="box box-primary">
					<div class="box-header with-border">
	                   <form id="downline_form" class="form form-bordered" action="{{route('aff.referrals.my_referred_customers')}}" method="post">   
					        <div class="col-sm-3">
								<div class="form-group has-feedback">
									<label for="search_term">Seach Text</label>
									<div class="input-group">
										<input type="text" id="search_term" name="search_term" class="form-control">
										<div class="input-group-btn">
											<button data-toggle="dropdown" class="btn btn-default ">Filter <span class="caret"></span></button>
											<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right"> 
												<li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FirstName" type="checkbox" checked> FirstName</label></li>												
											</ul>
										</div>
									</div>							
								</div>							
							</div>							
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" value="" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" value="" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group has-feedback">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('affiliate/general.search_btn')}}</button>&nbsp;
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('affiliate/general.reset_btn')}}</button>&nbsp;
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('affiliate/general.export_btn')}}</button>&nbsp;
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>                    
                    <div class="box-body">
						<table id="downlinelist" class="table table-bordered table-striped">
							<thead>
							   <tr>                                                      
									<th>Customer</th>							    
									<th>Signed Up on</th>
									<th>Country</th>
									<th>Sales</th>
									<th>CV</th>								
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/referrals/my_referred_customers.js')}}"></script>
@stop
