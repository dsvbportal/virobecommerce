@extends('shopping.layout.home_layout')
@section('home_page_header')
    @include('shopping.common.header')
@stop
@section('content')

        <!-- Breadcrumb Start -->
<div class="breadcrumb-area mt-30">
    <div class="container">
        <div class="breadcrumb">
            <ul class="d-flex align-items-center">
                <li><a href="#">Home</a></li>
                <li class="active"><a href="wishlist.html">Wishlist</a></li>
            </ul>
        </div>
    </div>
    <!-- Container End -->
</div>
<!-- Breadcrumb End -->
<!-- Wish List Start -->
<div class="cart-main-area wish-list ptb-100 ptb-sm-60">
    <div class="container">

        @if(empty($product))
            <div class="" style="text-align:  center;">
                <div class="form-group">
                    <img class="img-circle wishlist-logo"src="{{asset('resources/uploads/ecom/heart_log.png')}}">
                </div>

                <div class="form-group">Your Wishlist Is Empty!</div>
                <a href="{{url('/')}}"><button class="button" >Continue Shopping</button></a>
            </div>

        @else
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <!-- Form Start -->
                <form action="#">
                    <div class="msg_empty">
                    <!-- Table Content Start -->
                    <div class="table-content table-responsive table-wishlist">
                        <table>
                            <thead>
                            <tr>
                                <th class="product-remove">Remove</th>
                                <th class="product-thumbnail">Image</th>
                                <th class="product-name">Product</th>
                                <th class="product-price">Unit Price</th>
                                <th class="product-subtotal">add to cart</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product as $prod)

                                <tr class="rowss">

                                <td class="product-remove"> <a href="{{route('ecom.product.remove-to-wishlist')}}" row_id="{{$prod['rowId']}}" class="del_wishlist"><i class="fa fa-times" aria-hidden="true"></i></a></td>
                                <td class="product-thumbnail">
                                    <a href="#"><img src="{{$prod['options']['image']['product_details']}}" alt="cart-image" /></a>
                                </td>
                                <td class="product-name"><a href="#">{{$prod['name']}}</a></td>
                                <td class="product-price"><span class="amount">{{$prod['price']}}</span></td>
                                <td class="product-add-to-cart"><a class="addcart_fromwishlist"  href="{{route('ecom.product.add-to-cart' ,['code'=>$prod['id']])}}" data-id="{{$prod['id']}}" row_delete="{{route('ecom.product.remove-to-wishlist')}}" row_id="{{$prod['rowId']}}">add to cart</a></td>


                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>

                    </div>
                    <!-- Table Content Start -->
                </form>
                <!-- Form End -->
            </div>
        </div>
            @endif

                    <!-- Row End -->
    </div>
</div>






    @include('shopping.common.newsletter')
@stop
@section('scripts')
    <script type="text/javascript" src="{{asset('js/providers/ecom/product/product_details.js')}}"></script>
@stop