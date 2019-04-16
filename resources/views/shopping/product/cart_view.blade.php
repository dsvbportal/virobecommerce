@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.header')
@stop
@section('content')
	  <div class="breadcrumb-area mt-30">
            <div class="container">
                <div class="breadcrumb">
                    <ul class="d-flex align-items-center">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li class="active"><a href="{{url('product/cart-items-view')}}">Cart</a></li>
                    </ul>
                </div>
            </div>
            <!-- Container End -->
        </div>
	
	
   <div class="cart-main-area ptb-100 ptb-sm-60">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
            			<div class="cart_quatity_error"></div>
						<div id="cartItems" class="cart_details_row table-content table-responsive mb-45"></div>
						
					</div>
            <!-- ./ Center colunm -->
        </div>
        <!-- ./row-->
    </div>
</div>
@stop
@section('scripts')
<script src="{{asset('validate/lang/cart_details')}}" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/providers/ecom/product/add_cart.js')}}"></script>
@stop