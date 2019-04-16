@extends('admin.common.layout')
@section('title',trans('admin/finance.member_credit_debit'))
@section('breadcrumb')
<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('admin/finance.management')}}</a></li>
@stop
@section('layoutContent')
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">Members Credit & Debit </h4>
	</div>
	<div class="panel-body">
		<div id="status_msg"></div>
		<form id="search_form" class="form-horizontal form form-bordered" class="" autocomplete="off" action="{{route('admin.finance.fund-transfer.find_member')}}" method="post">
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">{{trans('admin/finance.trans_type')}}:</label>
				<div class="col-sm-5">
					<input type="hidden" name="min" id="min" value="{{$settings->min_amount}}">
					<input type="hidden" name="max" id="max" value="{{$settings->max_amount}}">
					<label class="radio-inline">
						<input type="radio" class="trans_type" {{isset($type) && $type==1?'checked':(!isset($type)?'checked':'')}} name="type" value="1"> Credit Fund
					</label>
					<label class="radio-inline">
						<input type="radio" class="trans_type" {{isset($type) && $type==2?'checked':''}} name="type" value="2"> Debit Fund
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Email (or) Account ID:</label>
				<div class="col-sm-5">
					<input type="text" class="form-control" id="member" placeholder="Email/Mobile" name="member" value="{{$member or ''}}">
					<div id="mrerror"></div>
				</div>
			</div>
			<div id="details_div" style="display:none">
				<div class="form-group frfld">
					<label class="control-label col-sm-2" for="email">Channel Partner Type:</label>
					<div class="col-sm-5">
						<label class="form-control text-left" for="frtype" id="fr_franchisee_type" disabled></label>
					</div>
				</div>
				<div class="form-group frfld">
					<label class="control-label col-sm-2" for="email">Channel Partner Name:</label>
					<div class="col-sm-5">
						<label class="form-control text-left" for="frname" id="fr_company_name" disabled></label>
					</div>
				</div>
				<div class="form-group frfld">
					<label class="control-label col-sm-2" for="email">Channel Partner Access:</label>
					<div class="col-sm-5">
						<label class="form-control text-left" for="frname" id="fr_location" disabled></label>
					</div>
				</div>				
				<div class="form-group">
					<label class="control-label col-sm-2" for="email">{{trans('admin/finance.full_name')}}:</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="fullname" name="fullname" disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="email">{{trans('admin/finance.uname')}}:</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="uname" name="uname" disabled>
					</div>
				</div>
				<input type="hidden" class="form-control" id="account_id" name="account_id">
				<div class="form-group hidden">
					<label class="control-label col-sm-2" for="email">{{trans('admin/finance.currency')}}:</label>
					<div class="col-sm-5">
						<select name="currency_id" class="form-control" id="currency_id">							
						</select>					   
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="email">{{trans('admin/finance.wallet')}}:</label>
					<div class="col-sm-5">
						<select name="wallet" class="form-control" id="wallet">							
						</select>
						 <div id="avail_bal"></div>
					</div>
				</div>
			 
				<div class="form-group">
					<label class="control-label col-sm-2" for="email">{{trans('admin/finance.amt')}}:</label>
					<div class="col-sm-5">
						<div class="input-group">
							<span class="input-group-addon" id="curreny_code_label"></span>
							<input type="text" class="form-control" onkeypress="return isNumberKeydot(event)" id="amount" name="amount" value="">
						</div>					
						<div id="amt_err"></div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-sm-2" for="email">Remarks</label>
                     <div class="col-sm-5">
                      <textarea id="remarks" name="remarks" class="form-control" rows="3" cols="45"></textarea>
                  </div>
               </div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" id="submit_btn" class="btn btn-success">{{trans('admin/finance.submit')}}</button>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="button" id="search_btn" class="btn btn-success">{{trans('admin/finance.search')}}</button>
				</div>
			</div>
		</form>
	</div>
</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/finance/member_credit_debit.js')}}"></script>
<script src="{{asset('resources/assets/admin/js/other_functionalities.js')}}"></script>
@stop
