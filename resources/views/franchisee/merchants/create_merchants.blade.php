@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/merchant/merchant_details.add_merchant'))
@section('content')

<section class="content-header">
    <h1><i class="fa fa-home"></i>{{trans('franchisee/merchant/merchant_details.add_merchant')}}</h1>
     <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{\trans('franchisee/dashboard.page_title')}}</a></li>
        <li>{{trans('franchisee/merchant/merchant_details.profile_pagehead')}}</li>
        <li class="active">{{trans('franchisee/merchant/merchant_details.add_merchant')}}</li>
      </ol>
</section>
<section class="content">
    <div class="row">														
		<div class="col-sm-12">
            <div class="box box-primary">
               <div class="box-header with-border">
	                  <div class="col-sm-12">
			        <form  action="{{route('fr.merchants.save')}}" method="POST" class='form-horizontal form-validate' id="create_merchant"  enctype="multipart/form-data">
                       
                        <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['buss_name']['label']}}</label>
                        <div class="col-sm-6">
                            <input type="text" id="business_name" class="form-control"  {!!build_attribute($fieldValitator['buss_name']['attr'])!!}>
                        </div>
                    </div>
					<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['firstname']['label']}}</label>
                        <div class="col-sm-6">
                            <input type="text" id="first_name" class="form-control" {!!build_attribute($fieldValitator['firstname']['attr'])!!}>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['lastname']['label']}}</label>
                        <div class="col-sm-6">
                            <input type="text" id="last_name" class="form-control" {!!build_attribute($fieldValitator['lastname']['attr'])!!} >
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['country']['label']}}</label>
                        <div class="col-sm-6">
                               <div class="input-group">
                                        <span class="input-group-addon"><img src="" class="country-flag"></span>
                                        <select class="form-control" {!!build_attribute($fieldValitator['country']['attr'])!!} data-err-msg-to="#mobile-country-err" id="country">
                                            @foreach($countries as $country)
                                            <option value="{{$country->country_id}}" data-mobile_validation="{{$country->mobile_validation}}" data-flag="{{$country->flag}}" data-phonecode="{{$country->phonecode}}">{{$country->country}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                   </div>
                                  </div>
                       <div class="form-group">
					   
					    <input type="hidden" name="phonecode" class="country-phonecode" value="">
						
                         <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['mobile']['label']}}</label>
						  <div class="col-sm-6">
                          <div class="input-group">
							<span class="input-group-addon country-phonecode"></span>
							<input id="mobile"  {!!build_attribute($fieldValitator['mobile']['attr'])!!} data-err-msg-to="#mobile-err" class="form-control" {!!build_attribute($fieldValitator['mobile']['attr'])!!}/>
                            </div>
                           <span id="mobile-err"></span>
                          </div>
                          </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Merchant Type:</label>
                        <div class="col-sm-6">
                                    <select  class="form-control" id="service_type" {!!build_attribute($fieldValitator['service_type']['attr'])!!}>
                                        <option value="1">In-Store</option>
                                        <option value="2">Online</option>
                                        <option value="3">Online and In-Store</option>
                                    </select>
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Number of Physical locations:</label>
                         <div class="col-sm-6">
                                    <label class="control-label sr-only" for="phy_locations">Number of Physical Locations</label>
                                    <select name="phy_locations" class="form-control" id="phy_locations">
										<option value="">Select Physical Location</option>
                                        @foreach($phy_locations as $phy_location)
                                        <option value="{{$phy_location->id}}">{{$phy_location->label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                     </div>
					   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{$fieldValitator['email']['label']}}</label>
                        <div class="col-sm-6">
							 <input id="email" {!!build_attribute($fieldValitator['email']['attr'])!!} class="form-control" />
                        </div>
                      </div>
			<div class="row" id="cateFld">
			<label for="textfield" class="control-label col-sm-2">Business Category:</label>
                    <div class="form-group">
                        
                        <div class="col-sm-6">
                            <div class="btn-group hierarchy-select" data-resize="auto" id="example-one" >
								<button type="button" class="form-control dropdown-toggle" data-toggle="dropdown">
									<span class="selected-label pull-left">Select In-Store Category&nbsp;</span>
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu open">
									<div class="hs-searchbox">
										<input type="text" class="form-control" autocomplete="off">
									</div>
									<ul class="dropdown-menu inner" role="menu" style="max-height: 208px; overflow-y: auto;"></ul>
								</div>
								<input class="hidden hidden-field" name="search_form[category]" readonly aria-hidden="true" type="text"/>
							</div>
							<input type="hidden" name="bcategory" title="Category" placeholder="Category" required="1" data-valuemissing="Category is required." id="bcategory" value="" data-url="{{route('fr.merchants.categories.in-store')}}">
					   </div>
                     </div>
                  </div>
                   <div class="form-group pull-center">
				        <label for="textfield" class="control-label col-sm-2"></label>
                                <div class="col-sm-2">
                                    <input type="submit" class="btn btn-success btn-block" disabled value="Continue"/>
                                </div>
                            </div>
                </form>
              </div>
              </div>
              </div>
            </div>
         </div>
	</section>
@stop
@section('scripts')
<script src="{{asset('js/providers/franchisee/merchant/create.js')}}"></script>
<script src="{{asset('resources/assets/plugins/Jquery-loadSelect.js')}}"></script>
<script src="{{asset('resources/assets/plugins/tree-search/hierarchy-select.js')}}"></script>
<link rel="stylesheet" href="{{asset('resources/assets/plugins/tree-search/Tree-searchSelect.css')}}">
    <script>
	$(document).ready(function () {
		$('#example-one').hierarchySelect({
			width: '100%',
			hierarchy: true,
			search: true
		});
	});
    </script>
@stop