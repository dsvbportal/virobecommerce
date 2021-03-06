@extends('affiliate.layout.dashboard')
@section('title',"Personal Customer Commission")
@section('content')
    <!-- Content Header (Page header) -->
	@include('affiliate.common.customer_commission_header')
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12" id="report">
             <div class="box box-primary">					
					<div class="box-controls with-border">
	                   <form id="personal_customer_commission" class="form form-bordered" action="" method="post">
                       		{!! csrf_field() !!}
                           
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mt25">
		<label class="control-label">&nbsp;</label>
		<button type="button" id="searchbtn" class="btn btn-success"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
		<button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
		<button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i> {{trans('affiliate/general.export_btn')}}</button>
		<button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i> {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
						<table id="personal_commission" class="table table-bordered table-striped">
						   <thead>
								<tr>              
									 <th>{{trans('affiliate/general.sl_no')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.month')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.directs_cv')}}</th>
								<!-- <th>{{trans('affiliate/bonus/personal_commission.self_cv')}}</th> -->
									 <th>{{trans('affiliate/bonus/personal_commission.slab')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.earnings')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.commission')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.ngo_wallet')}}</th>
									 <th>{{trans('affiliate/bonus/personal_commission.net_pay')}}</th>
								<!--<th>Details</th>-->
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div id="details"></div>
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
 <script src="{{asset('js/providers/affiliate/bonus/personal_commission.js')}}"></script>
@stop