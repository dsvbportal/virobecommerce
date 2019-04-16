@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/merchant/merchant_details.manage_merchant'))
@section('content')
 <!-- Content Header (Page header) -->
	 <section class="content">
          <div class="row">														
			<div class="box-header with-border">
				<div class="panel panel-default">
				   <div class="panel-heading">
					  <h4 class="panel-title"> Kyc Documents Upload</h4>
					</div>
	 <div class="panel-body">
			<div class="col-sm-12">
			   <div class="tax-proof">
			     <div class="panel tax-proof-display" {!!!empty($details->pan_card_image)?'':'style="display:none;"'!!}>
                      <div class="panel-body"> 
					 <div class="form-group">
						<label for="pan" class="col-sm-2">PAN</label>
                            <div class="col-sm-10">
                                <div class="form-control-static">
                                    <span>{{$details->pan_card_name or ''}} ({{$details->pan_card_no or ''}})</span>
                                    @if (isset($details->pan_card_status) && ($details->pan_card_status == 1))
                                    <span class="label label-success"><b>VERIFIED</b></span>
                                    @elseif (isset($details->pan_card_status) && ($details->pan_card_status == 0))
                                    <span class="label label-warning"><b>NOT VERIFIED</b></span>
                                    @elseif(isset($details->pan_card_status) && ($details->pan_card_status == 2))
                                    <span class="label label-danger"><b>REJECTED</b></span>
                                    @endif
                                    <div class="btn-group pull-right">
                                        <a href="{{$details->pan_card_image}}" target="_blank" class="btn btn-sm btn-info" title="Download"><i class="fa fa-download"><span class="sr-only">Download</span></i></a>
                                        {!!($details->pan_card_status != 1)?'<a href="#" style="margin-left:5px;" class="tax-proof-edit-btn btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"><span class="sr-only">Edit</span></i></a>':''!!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                   </div>
				   @if($details->pan_card_status != 1)
			         <div class="tax-proof-edit" {!!!empty($details->pan_card_image)?'style="display:none;"':''!!}>
                        <form class="form-horizontal" id="tax_info" action="{{route('fr.merchants.tax-information')}}" enctype=  "multipart/form-data" method="post" novalidate="novalidate" autocomplete="off">
						  <!--  <input type="text" id="supp_id" name="supp_id" value="">
						       <input type="text" id="acc_type_id" name="acc_type_id" value="">-->
							<div class="form-group">
                                    <div class="col-sm-3">
									   <label for="pan_name">PAN<span class="text-danger">*</label>
                                            <input type="text" class="form-control" id="pan_name"  {!!build_attribute($tax_fields['pan_name']['attr'])!!}  value="{{$details->pan_card_name or ''}}"  placeholder="Enter Name on Pan Card" onkeypress="return alphaBets_withspace(event)">
                                        </div>
									 <div class="col-sm-3">
										    <label for="pan_name">PAN Number<span class="text-danger">*</label>
                                             <input  type="text" class="form-control" id="pan_number"
								             {!!build_attribute($tax_fields['pan_number']['attr'])!!}  placeholder="Enter Pan Card Number" value="{{$details->pan_card_no or ''}}"  onkeypress="return alphaNumeric_withoutspace(event)">
									   </div>
									  <div class="col-sm-3">
										     <label for="Pan_card">PAN Card<span class="text-danger">*</span> </label>
											   <input  type="file" id="pan_card_upload" name="pan_card_upload" accept="image/gif,image/jpg,image/jpeg,image/png,application/pdf" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png, *pdf)" class="file_upload">
											   
											</div>
										<div class="col-sm-3" style="margin-top:20px;">
										      <input type="submit" class="btn btn-success btn-md" value="Save"/>
                                        </div>
                                     </div>
							 </form>	
			           </div>
					   @endif
			         </div>
			    
			<form class="form-horizontal" id="gstin_form" action="{{route('fr.merchants.gst-information')}}" enctype="multipart/form-data"  method="post" novalidate="novalidate" autocomplete="off">
				 <div class="tax-proof">  
				    <div class="tax-proof-display" {!!!empty($details->gstin_no)?'':'style="display:none;"'!!}>  
				     <div class="form-group">
                            <div class="col-sm-10">
                                <div class="form-control-static" >
								<label for="textfield" class="col-sm-2">GSTIN</label>
                                    <span>{{$details->gstin_no or ''}}</span>
                                    @if (isset($details->gst_status) && ($details->gst_status == 1))
                                    <span class="label label-success"><b>VERIFIED</b></span>
                                    @elseif (isset($details->gst_status) && ($details->gst_status == 0))
                                    <span class="label label-warning"><b>NOT VERIFIED</b></span>
                                    @elseif(isset($details->gst_status) && ($details->gst_status == 2))
                                    <span class="label label-danger"><b>REJECTED</b></span>
                                    @endif
                                    <div class="btn-group pull-right">
                                        <a href="{{$details->tax_document_path}}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-download"> <span class="sr-only">Download</span></i></a>
                                        <a href="#" style="margin-left:5px;" class="tax-proof-edit-btn btn btn-sm btn-primary"><i class="fa fa-edit"> <span class="sr-only">Edit</span></i></a>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>
						@if($details->gst_status != 1)
								  <div class="form-group tax-proof-edit" {!!!empty($details->gstin_no)?'style="display:none;"':''!!}>
									 <div class="col-sm-4">
										<label for="gstin_no">GSTIN<span class="text-danger">*</span> </label>
										<input type="text" class="form-control" id="gstin_no" 
										{!!build_attribute($gst_fields['gstin_no']['attr'])!!} placeholder="Enter GSTIN Number" onkeypress="return alphaNumeric_withoutspace(event)"/ value="{{$details->gstin_no or ''}}">
									   </div>
									 <div class="col-sm-4">
									   <label for="gstin">&nbsp;<span class="text-danger"></span> </label>
									   <input type="file" data-err-msg-to="#gstin_no-err" id="gstin_image" name="gstin_image" accept="image/gif,image/jpg,image/jpeg,image/png,application/pdf" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png, *pdf)" class="file_upload">
								 </div>
							    </div>
							 @endif
							</div>
				  <div class="tax-proof">
				    <div class="tax-proof-display" {!!!empty($details->tan_path)?'':'style="display:none;"'!!}>
					<div class="form-group">
						   <div class="col-sm-10">
						    <label for="inputEmail" class="col-sm-2">TAN</label>
                                <div class="form-control-static">
                                    <span>{{$details->tan_no or ''}}</span>&nbsp;&nbsp;
                                    @if (isset($details->tan_status) && ($details->tan_status == 1))
                                    <span class="label label-success"><b>VERIFIED</b></span>
                                    @elseif (isset($details->tan_status) && ($details->tan_status == 0))
                                    <span class="label label-warning"><b>Not VERIFIED</b></span>
                                    @elseif(isset($details->tan_status) && ($details->tan_status == 2))
                                    <span class="label label-danger"><b>REJECTED</b></span>
                                    @endif
                                    <div class="btn-group pull-right">
                                        <a href="{{$details->tan_path}}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-download"> <span class="sr-only">Download</span></i></a>
                                        <a href="#" style="margin-left:5px;" class="tax-proof-edit-btn btn btn-sm btn-primary"><i class="fa fa-edit"> <span class="sr-only">Edit</span></i></a>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>
						 <div class="form-group tax-proof-edit" {!!!empty($details->tan_path)?'style="display:none;"':''!!}>
							  <div class="col-sm-4">
									 <label for="gstin_no">TAN<span class="text-danger">*</span> </label>
									  <input type="text" class="form-control" id="tan_no" {!!build_attribute($gst_fields['tan_no']['attr'])!!} value="{{$details->tan_no or ''}}" placeholder="Enter GSTIN Number" onkeypress="return alphaNumeric_withoutspace(event)"/>
								   </div>
							   <div class="col-sm-4">
								    <label for="tan">&nbsp;<span class="text-danger"></span> </label>
									<input type="file" data-err-msg-to="#tan_no-err" id="tan_image" name="tan_image" accept="image/gif,image/jpg,image/jpeg,image/png,application/pdf" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png, *pdf)" class="file_upload">
									</div>
						</div>
				   </div>
						  <table id="mange_center" class="table table-striped" >
                            <thead>
                                <tr>
                                    <th>Information</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h5><b>ID Proof</b></h5>
                                    <td class="tax-proof">
									  <div class="form-control-static tax-proof-display" {!!!empty($details->id_proof_path)?'':'style="display:none;"'!!}>
                                            {{$details->id_proof_no or ''}}
                                            {{$details->id_proof_name or ''}}
                                            <div class="btn-group pull-right">
                                                <a href="{{$details->id_proof_path}}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"> <span class="sr-only">Download</span></i></a>
                                                <a href="#" style="margin-left:5px;" class="tax-proof-edit-btn btn btn-sm btn-primary"><i class="fa fa-edit"> <span class="sr-only">Edit</span></i></a>
                                            </div>
                                        </div>
									 @if($details->id_proof_status != 1)
                                        <div class="input-group tax-proof-edit" {!!!empty($details->id_proof_path)?'style="display:none;"':''!!} >
                                            <input type="text" class="form-control" name="proof_no" id="proof_no" placeholder="Enter ID Proof No" value="{{$details->id_proof_no or ''}}">
                                            <span class="input-group-btn" style="width:0px;"></span>
                                            <select name="id_proof_type" class="form-control" id="id_proof_type">
                                                <option value="">Select ID Proof</option>
											 @if(isset($id_proof) && !empty($id_proof))
											   @foreach($id_proof as $key=>$filed)
                                                <option value="{{$filed->document_type_id}}" @if(!empty($details->id_proof_document_type_id)) {{($details->id_proof_document_type_id == $filed->document_type_id)?' selected ':''}} @endif >{{ucfirst($filed->type)}}</option>
                                                @endforeach
												 @endif
                                                </option>
                                            </select>
                                            <span class="input-group-btn">
                                                <input type="file" id="id_image" name="id_image" accept="image/gif,image/jpg,image/jpeg,image/png,application/pdf" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png, *pdf)" class="btn btn-primary"/>
                                            </span>
                                        </div>
										 @endif
                                    </td>
                                   <td style="padding-top:20px;">
                                        @if (isset($details->id_proof_status) && ($details->id_proof_status == 1))
                                        <span class="label label-success"><b>VERIFIED</b></span>
                                        @elseif (isset($details->id_proof_status) && ($details->id_proof_status == 0))
                                        <span class="label label-warning"><b>Not VERIFIED</b></span>
                                        @elseif(isset($details->id_proof_status) && ($details->id_proof_status == 2))
                                        <span class="label label-danger"><b>REJECTED</b></span>
                                        @endif
                                    </td>
                                   </tr>
								    <tr>
                                    <td>
                                        <h5><b>Address Proof</b></h5>
                                    </td>
                                    <td class="tax-proof">
									      <div class="form-control-static tax-proof-display" {!!!empty($details->address_proof_path)?'':'style="display:none;"'!!}>
                                            {{$details->address_proof_no or ''}}
                                            {{$details->address_proof_document_type or ''}}
                                            <div class="btn-group pull-right">
                                                <a href="{{$details->address_proof_path}}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"> <span class="sr-only">Download</span></i></a>
                                                <a href="#" style="margin-left:5px;" class="tax-proof-edit-btn btn btn-sm btn-primary"><i class="fa fa-edit"> <span class="sr-only">Edit</span></i></a>
                                            </div>
                                        </div>
								 @if($details->address_proof_status != 1)
                                        <div class="input-group tax-proof-edit" {!!!empty($details->address_proof_path)?'style="display:none;"':''!!} >
                                            <input type="text" class="form-control" name="address_proof_no" id="address_proof_no"  value="{{$details->address_proof_no or ''}}" placeholder="Enter Address Proof No">
                                            <span class="input-group-btn" style="width:0px;"></span>
                                            <select name="address_proof_type" class="form-control" id="address_proof_type">
                                               <option value="">Select Address Proof</option>
                                                @if(isset($address_proof) && !empty($address_proof))
                                                @foreach($address_proof as $key=>$filed)
                                                <option value="{{$filed->document_type_id}}" @if(!empty($details->address_proof_document_type_id)) {{($details->address_proof_document_type_id== $filed->document_type_id)?' selected ':''}} @endif >{{ucfirst($filed->type)}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="input-group-btn">
                                                <input type="file" id="address_image" name="address_image" accept="image/gif,image/jpg,image/jpeg,image/png,application/pdf" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png, *pdf)" class="btn btn-primary"/>
                                            </span>
                                        </div>
								   @endif
                                    </td>
                                   <td style="padding-top:20px;">
                                        @if (isset($details->address_proof_status) && ($details->address_proof_status == 1))
                                        <span class="label label-success"><b>VERIFIED</b></span>
                                        @elseif (isset($details->address_proof_status) && ($details->address_proof_status == 0))
                                        <span class="label label-warning"><b>Not VERIFIED</b></span>
                                        @elseif (isset($details->address_proof_status) && ($details->address_proof_status == 2))
                                        <span class="label label-danger"><b>REJECTED</b></span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
					<div class="text-right">
						 <input type="submit" class="btn btn-success btn-md" value="Save"/>
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
<script src="{{asset('js/providers/franchisee/merchant/kyc_info.js')}}"></script>
@stop