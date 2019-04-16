@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/package/purchase.buypackage_page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa fa-files-o"></i> {{\trans('affiliate/package/purchase.buypackage_page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{\trans('affiliate/package.breadcrumb_title')}}</li>
        <li class="active">{{\trans('affiliate/package/purchase.buypackage_page_title')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content" id="package_purchase">   		
		<!-- /.row -->
		<h2 class="text-center">SALES ASSURANCE PACKAGES</h2>
		<div class="row" id="packagegrid">
			<div class="col-sm-12">
			<!-- ./packages list  -->
            @if(!empty($packages))
			<?php $colr=1;?>            
            @foreach($packages as $pack)
			<div class="col-sm-4">
               <ul class="package_list">
			    <li>
				  <div class="most_p"><h4>MOST POPULAR</h4></div>
                <article class="pricing-row">
					<?php if($colr>7){ $colr=1;}?>
					<div class="text-center plan-header">
						<img src="{{$pack->package_image_url}}">                            
					</div>
					<div class="col-sm-12">
						<h3 class="plan-title">{{$pack->package_name}}</h3>
						<ul class="list-unstyled list-inline">						
					<li>{{trans('affiliate/package/purchase.pack_qv')}} <b>{{$pack->package_qv}}</b></li>
					<li>{{trans('affiliate/package/purchase.pack_shopping_points_cashback')}} <b>{{number_format($pack->shopping_points_cashback,2,'.',',')}}</b></li>
					<li>{{trans('affiliate/package/purchase.pack_shopping_points_bonus')}} <b>{{number_format($pack->shopping_points_bonus,2,'.',',')}}</b></li>                       
						</ul>  
					</div>
					<div class="col-sm-12 plan-price">		
						<h3>{{$pack->price}}<span>.00 {{$pack->currency_code}}</span></h3>												
						<a href="{{route('aff.package.paymodes')}}" class="btn btn-md btn-social btn-success buy_now" data-id="{{$pack->package_code}}" data-info="{{json_encode($pack)}}"><i class="fa fa-shopping-cart" aria-hidden="true"></i> {{trans('affiliate/package/purchase.buypack_buynowbtn')}}</a>
					</div>
                </article>     
</li>	
               </ul>
</div>			   
                @endforeach
            @endif
			<?php /*
			@foreach($packages as $pack)
                <article class="pricing-column col-sm-3 col-md-4  col-lg-3">
                    <div class="inner-box card-box">
                        <?php if($colr>7){ $colr=1;}?>
                        <div class="plan-header header_color{{$pack->package_code}} text-center">
                            <h3 class="plan-title">{{$pack->package_name}}</h3>
							<div class=""><img src="{{$pack->package_image_url}}"></div>
                            <h2 class="plan-price">{{$pack->price}}<span>{{$pack->currency_code}}</span></h2>                            
                        </div>
                        <ul class="plan-stats list-unstyled text-center">
                            <li>{{trans('affiliate/package/purchase.pack_qv')}} <b>{{$pack->package_qv}}</b></li>
                            <li>{{trans('affiliate/package/purchase.pack_shopping_points_cashback')}} <b>{{number_format($pack->shopping_points_cashback,2,',','.')}}</b></li>
							<li>{{trans('affiliate/package/purchase.pack_shopping_points_bonus')}} <b>{{number_format($pack->shopping_points_bonus,2,',','.')}}</b></li>                        
                        </ul>                                                                            
                        <div class="text-center">
                            <a href="{{route('aff.package.paymodes')}}" class="btn btn-sm btn-success buy_now" data-id="{{$pack->package_id}}" data-info="{{json_encode($pack)}}">{{trans('affiliate/package/purchase.buypack_buynowbtn')}}</a>
                        </div>
                    </div>
                </article>                
                @endforeach
             @endif
			*/?>
			<!-- ./packages list -->	
			</div>
			<div class="terms_t">
			<ul><li><i class="fa fa-hand-o-right" aria-hidden="true"></i>
			 ALL SALES ARE FINAL AND NON-TRANSFERABLE.  </li>
			<li><i class="fa fa-hand-o-right" aria-hidden="true"></i>
			 NO REFUNDS, NO EXCHANGES, NO RESALE, NOT REDEEMABLE FOR CASH.</li></ul>
			</div>
		</div>
		<!-- paymodes -->
			<div class="row" id="paymodes" style="display:none">
				<!-- ./col -->
				<div class="col-md-3">	
					<div class="box box-primary">
						<div class="box-header with-border">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">{{trans('affiliate/package/purchase.package_page_title')}}</h3>    
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-default btn-sm backto_packagebtn"><i class="fa fa-arrow-left"></i> {{trans('affiliate/package/purchase.backbtn')}}</button>
							</div>	                    			
						</div>
						<div class="box-body">            
							<ul class="list-group list-group-bordered"  id="packInfo">
							<li class="list-group-item">
							  <b>{{trans('affiliate/package/purchase.package_name_label')}}</b> <span class="pull-right text-info pkname">1,322</span>
							</li>
							<li class="list-group-item">
							  <b>{{trans('affiliate/package/purchase.package_price')}}</b> <span class="pull-right text-success pkamt">543</span>
							</li>                
						  </ul>                 
						</div>
					</div>
				</div>
				<div class="col-md-9">	
					<div class="box box-primary"  id="paymentprocess">
						<div class="box-header with-border">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">{{trans('affiliate/package/purchase.paymodes')}}</h3>                        			
						</div>
						<div class="box-body">
							<div class="form-group">
								<ul class="selpaymode">                   
								</ul>
							</div>                
						</div>
					</div>
					<div class="row" id="payment-forms" style="display:none">
						@include('affiliate.common.paymentGateway')
					</div>
				</div>				
			</div>					
			<!-- paymodes -->
		<!-- /.row -->        
    </section>
    <!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/package/purchase.js')}}"></script>
@stop