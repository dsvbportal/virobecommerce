@extends('shopping.layout.home_layout')
@section('home_page_header')
@include('shopping.common.change_mobile_header')
@stop
@section('content')
<!-- content -->

<div class="contentpanel" >
   <div class="panel panel-default">
                <div class="panel-body">    
					

					<form class="form-horizontal" id="update_email" action="{{route('ecom.account.new_email_notify')}}" method="post" autocomplete="off" align="text-center">

						@if($verification== true)
						<div class="form-group">
						         					
							<div class="col-md-12">
								<div class="col-md-offset-3 col-md-8">
								<h4 style="padding-left: 16px;padding-bottom: 12px;">Enter Your new email </h4>
								<div class="col-sm-4" >
									<input  {!!build_attribute($pufields['email']['attr'])!!}  type="text" id="email"  name ="email" placeholder="New Email" class="form-control" value=""  onkeypress="return RestrictSpace(event)">
								</div>
								<div class="col-md-2">

									<div class="col-md-2">
								 	<button type="submit" class="btn btn-primary" id="update_email_btn" name="submit"><i class="fa fa-save"></i> Update</button>
								    </div>		

								</div>
							</div>
							</div>
						</div>
						@else
						 <label> something went wrong!!!</label>
                         @endif						
					</form>




           </div>            
       </div>         
  </div>





@stop
@section('scripts')
	<script type="text/javascript" src="{{asset('js/providers/ecom/account/profile.js')}}"></script>
@stop