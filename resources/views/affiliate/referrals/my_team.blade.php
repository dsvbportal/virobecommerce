@extends('affiliate.layout.dashboard')
@section('title','Team Referral Report')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/general.my_team')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{trans('affiliate/general.dashboard')}}</a></li>
        <li >{{trans('affiliate/general.profile_pagehead')}}</li>
		<li class="active">{{trans('affiliate/general.my_team')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">
			
			@if(!empty($generationSales))						
			@foreach($generationSales as $g=>$sales)
				<div class="col-sm-3">
					<div class="info-box">
						<span class="info-box-icon bg-maroon"><b>{{$g}}</b></span>
						<div class="info-box-content">
							<div class="info-box-text"><b class="text-green">{{trans('affiliate/general.generation.'.$g)}}</b><br>{{trans('affiliate/general.qv_sales')}}</div>
							<div class="info-box-number">{{$sales}}</div>
						</div>
					</div>
				</div>
			@endforeach
			@endif
		</div>
		<div class="row">        
			<div class="col-md-12">
             <div class="box box-primary">
					
					<div class="box-controls with-border">
	                   <form id="downline_form" class="form form-bordered" action="{{route('aff.referrals.myteam')}}" method="post">
							<div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.select-generation')}}</label>
                                    <select id="generation" name="generation" class="form-control">
										<option value="0">All</option>
										<option value="1">1G</option>
										<option value="2">2G</option>
										<option value="3">3G</option>
									</select>
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
                                <div class="form-group">
                                    <button type="button" id="searchbtn" class="btn bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>     {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                            
                        </form> 
					</div>
                    <div class="box-body">
						<table id="downlinelist" class="table table-bordered table-striped">
							<thead>
								<tr>      
									<th>{{trans('affiliate/general.sl_no')}}</th>
									<th>{{trans('affiliate/referrels/my_referrels.affiliate')}}</th>
									<th>{{trans('affiliate/referrels/my_referrels.country')}} </th>
									<th>{{trans('affiliate/referrels/my_referrels.refered_by')}} </th>
									<th>{{trans('affiliate/referrels/my_referrels.placement')}} </th>
									<th>{{trans('affiliate/referrels/my_referrels.rank')}} </th>
									<th>{{trans('affiliate/referrels/my_referrels.total_qv')}} </th>
									<th>{{trans('affiliate/referrels/my_referrels.activated_on')}}</th> 
									<th>{{trans('affiliate/referrels/my_referrels.status')}} </th>
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
<script src="{{asset('js/providers/affiliate/referrals/my_team.js')}}"></script>
@stop
