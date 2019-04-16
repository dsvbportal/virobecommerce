@extends('affiliate.layout.dashboard')
@section('title',"Leadership Bonus")
@section('content')
    <!-- Content Header (Page header) -->
    @include('affiliate.common.affiliate_commission_header')
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">
             <div class="box box-primary">
					<div class="box-controls with-border">
	                   <form id="leadership_bonus_details" class="form form-bordered" action="{{route('aff.reports.leadership_bonus')}}" method="post">
                       		{!! csrf_field() !!}
                           
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-6 mt25">
                                <div class="form-group">
                                    <button type="button" id="searchbtn" class="btn btn-success"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>  {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="leadership_bonus_commission" class="table table-bordered table-striped">
                       <thead>
                            <tr>                                                    
								<th>{{trans('affiliate/general.sl_no')}}</th>
								<th class="text-center no-wrap">Period</th>
                                <th class="text-center no-wrap">Matching(QV)</th>
                                <th class="text-center no-wrap">Earnings(QV)</th>
                                <th class="text-center no-wrap">Commission</th>                               
								<th class="text-center no-wrap">NGO Wallet</th>
                                <th class="text-center no-wrap">Net Pay</th>                                                                                           
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
					</div>
				</div>
			</div>
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">
		
		  <!-- Modal content-->
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="fa fa-times"></i></button>
			  <h4 class="modal-title"><i class="fa fa-calendar"></i> Leadership Bonus for the Period of <span></span></h4>
			</div>
			<div class="modal-body">
			  <table id="bonus_details" class="table table-bordered table-striped">	
				    <tr>
						<td class="bg-aqua"></td>
						<td class="bg-aqua text-center"><strong>(1G+2G)</strong></td>
						<td class="bg-aqua text-center"><strong>3G</strong></td>
					</tr>
					<tr>
						<td>Opening Bal. QV - c/f</td>
						<td class="leftopening text-right"></td>
						<td class="rightopening text-right"></td>
					</tr>
					<tr>
						<td>QV for <span class="date_for"></span></td>
						<td class="leftbinpnt text-right"></td>
						<td class="rightbinpnt text-right"></td>
					</tr>
					<tr>
						<td>Total QV</td>
						<td class="leftclubpoint text-right"></td>
						<td class="rightclubpoint text-right"></td>
					</tr>
					<tr>
						<td>Eligible QV</td>
						<td class="capping text-right"></td>
						<td class="capping text-right"></td>
					</tr>
					<tr>
						<td>Matching QV</td>
						<td class="clubpoint text-right"></td>
						<td class="clubpoint text-right"></td>
					</tr>
					<tr>
						<td>Flushout QV</td>
						<td class="left_flushout text-right"></td>
						<td class="right_flushout text-right"></td>
					</tr>
					<tr>
						<td>Payout Matching QV</td>
						<td class="earnings text-right"></td>
						<td class="earnings text-right"></td>
					</tr>
					<tr>
						<td>c/f QV</td>
						<td class="leftcarryfwd text-right"></td>
						<td class="rightcarryfwd text-right"></td>
					</tr>
			  </table>
			</div>			
		  </div>
		  
		</div>
  </div>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
 <script src="{{asset('js/providers/affiliate/bonus/leadership_bonus.js')}}"></script>
@stop