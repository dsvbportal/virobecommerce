@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.header')
@stop
@section('content')
        <!-- Breadcrumb Start -->
<div class="breadcrumb-area mt-30">
    <div class="container">
        @if(!empty($details->breadcrums))
        <div class="breadcrumb">
            <ul class="d-flex align-items-center">
                @foreach($details->breadcrums as $val)
                <li><a href="{{ $val->url }}" title="{{ $val->title }}">{{ $val->title }}</a></li>
                @endforeach
                <li class="active"><a href="">{{$details->productDetails->product_name}}</a></li>
            </ul>
        </div>
        @endif

    </div>
    <!-- Container End -->

</div>
<!-- Breadcrumb End -->

@if(!empty($details->productDetails))

<!-- Product Thumbnail Start -->
<div class="main-product-thumbnail ptb-100 ptb-sm-60">
    <div class="container">
        <div class="thumb-bg">
            <div class="row">
                <!-- Main Thumbnail Image Start -->
                @if(!empty($details->productDetails->imgs))

                <div class="col-lg-5 mb-all-40">
                    <!-- Thumbnail Large Image start -->
                    <div class="tab-content">
                        <div id="thumb1" class="tab-pane fade show active">
                            <a data-fancybox="images" class="main_img" href=""><img id="product-img" src="" data-zoom-image="" alt="product-view"></a>
                        </div>
                    </div>
                    <!-- Thumbnail Large Image End -->
                    <!-- Thumbnail Image End -->
                    <div class="product-thumbnail mt-15">
                        <div class="thumb-menu owl-carousel nav tabs-area" role="tablist">
                            @foreach($details->productDetails->imgs as $val)
                            <a data-toggle="tab" href="" class="sliding_img" data-zoom-image-d="{{$val->img_path->product_details_zoom  or ''}}" data-image="{{$val->img_path->product_details or ''}}"><img src="{{$val->img_path->product_details or ''}}" alt="product-thumbnail"></a>
                            @endforeach

                        </div>
                    </div>
                    <!-- Thumbnail image end -->
                </div>
                    @endif

                            <!-- Main Thumbnail Image End -->
                <!-- Thumbnail Description Start -->
                <div class="col-lg-7">
                    <div class="thubnail-desc fix">
                        <h3 class="product-header">{{$details->productDetails->product_name}}</h3>
                        <div class="rating-summary fix mtb-10">
                            @if(!empty($details->productDetails->rating_count))

                            <div class="rating">
                                {{$details->productDetails->rating}}
                                <i class="fa fa-star"></i>

                            </div>
                                @endif


                            <div class="rating-feedback">
                                @if(!empty($details->productDetails->rating_count))
                                    <a href="#">Based on {{ $details->productDetails->rating_count }} ratings</a>
                                @endif
                                    <a href="#">add to your review</a>
                            </div>


{{--

                                <div class="comments-advices">
                                    @if(!empty($details->productDetails->rating_count))
                                        <a href="#">Based on {{ $details->productDetails->rating_count }} ratings</a>
                                    @endif
                                    <a href="#"><i class="fa fa-pencil"></i> write a review</a>
                                </div>
--}}


                        </div>


                        <div class="pro-price mtb-30">
                            <p class="d-flex align-items-center"><span class="prev-price">{{ $details->productDetails->mrp_price or '' }}</span><span class="price">{{ $details->productDetails->price }}</span><span class="saving-price">save {{ $details->productDetails->off_per or '' }}</span></p>
                        </div>
                        <p class="mb-20 pro-desc-details line-control"> {!! $details->productDetails->description !!}</p>


                        <form id="add_cart_form" action="{{route('ecom.product.add-to-cart' ,['code'=>$details->productDetails->supplier_product_code])}}" method="post">
                            <input id="supplier_product_code" name="supplier_product_code" type="hidden" value="{{ $details->productDetails->supplier_product_code }}">
                            <input id="category_url_str" name="category_url_str" type="hidden" value="{{ $details->productDetails->category_url_str }}">
                            <input id="product_slug" name="product_slug" type="hidden" value="{{ $details->productDetails->product_slug }}">
                            <input id="product_colour_id" name="product_colour_id" type="hidden" value="">
                            <input id="select_size_id" name="select_size_id" type="hidden" value="">
                            <input type="hidden" id="product_colour" name="product_colour" value="">

                            @if(!empty($details->productDetails->properties))

                            <div class="product-size mb-20 clearfix">
                                <label>Size</label>
                                <select name="select_size" id="select_size" class="">

                                    @foreach($details->productDetails->properties as $val)
                                        @if($val->property=='Size')
                                            <option value="{{$val->value}}" data-content="{{ $val->value_id }}">{{$val->value}}</option>
                                        @endif
                                    @endforeach

                                </select>
                            </div>
                            <div class="color clearfix mb-20">
                                <label>color</label>
                                <ul class="color-list">

                                    @foreach($details->productDetails->properties as $val)
                                        @if($val->property=='Color')
                                            <li><a data-content="{{$val->value}}" data-content-val="{{ $val->value_id }}" class="product_colour_select" style="background:{{$val->value}}" href="#"></a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            @endif

                            <div class="box-quantity d-flex hot-product2">
                                    <input id="option-product-qty" name="product_qty" onkeypress="return isNumberKey(event)" class="quantity mr-15" data-value="{{ $details->productDetails->stock }}" type="number" min="1" value="1">
                                <div class="pro-actions">
                                    <div class="actions-primary button-group">
                                        <a href="" id="btn_add_cart" title="" data-original-title="Add to Cart"> + Add To Cart</a>
                                    </div>
                                   <div class="actions-secondary">
                                        @if($in_wishlist)
                                       <a> <i class="fa fa-heart wishlist-active"></i></a>

                                        @else

                                        <a class="wishlist"  data-original-title="WishList" href="{{route('ecom.product.add-to-wishlist',['code'=>$details->productDetails->supplier_product_code])}}"  product_id="{{ $details->productDetails->supplier_product_code}}" category="{{ $details->productDetails->category_url_str}}" product="{{$details->productDetails->product_slug}}"><i class="fa fa-heart-o wishlist-active"></i><span id='wishlist_txt'>Add to WishList</span></a>

                                        @endif

                                    </div>
                                </div>
                            </div>

                                </form>




                        <div class="pro-ref mt-20">
                            <p><span class="in-stock"><i class="ion-checkmark-round"></i> {{ $details->productDetails->stock_on_hand }}</span></p>
                        </div>

                    </div>
                </div>
                <!-- Thumbnail Description End -->
            </div>
            <!-- Row End -->
        </div>
    </div>
    <!-- Container End -->
</div>
<!-- Product Thumbnail End -->
<!-- Product Thumbnail Description Start -->
<div class="thumnail-desc pb-100 pb-sm-60">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ul class="main-thumb-desc nav tabs-area" role="tablist">
                    <li><a class="active" data-toggle="tab" href="#dtail">Product Details</a></li>
                    <li><a data-toggle="tab" href="#review">Reviews</a></li>
                    <li><a data-toggle="tab" href="#information">Information</a></li>
                    <li><a data-toggle="tab" href="#policy_desc">Policy Description</a></li>
                </ul>
                <!-- Product Thumbnail Tab Content Start -->
                <div class="tab-content thumb-content border-default">
                    <div id="dtail" class="tab-pane fade show active">
                        <p>{!! $details->productDetails->description !!}</p>
                    </div>
                    <div id="review" class="tab-pane fade">
                        <!-- Reviews Start -->
                        <div class="review border-default universal-padding">
                            <div class="group-title">
                                <h2>Rating & Reviews</h2>
                            </div>
{{--
                            <h4 class="review-mini-title">Truemart</h4>
--}}

                            <h4>Seller: {{ $details->productDetails->supplier or '' }}({{ $details->productDetails->rating or '' }})</h4>
                            <p></p>{{ $details->productDetails->rating_count or '' }} Ratings</p>

                            <?php
                            $delivery=10;
                            $NewDate=Date('d-M-Y', strtotime("+".$delivery. "days"));
                            echo $NewDate;?>
                            <fieldset>
                                {{--    <div class="form-group">

                                        <div class="row">
                                            <label for="textfield" class="control-label col-sm-2">5</label>
                                            <div class="col-sm-6"> <span>{{ $details->productDetails->rating_5 or '' }}</span><br />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="textfield" class="control-label col-sm-2">4</label>
                                            <div class="col-sm-6"> <span>{{ $details->productDetails->rating_4 or '' }}</span><br />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="textfield" class="control-label col-sm-2">3</label>
                                            <div class="col-sm-6"> <span>{{ $details->productDetails->rating_3 or '' }}</span><br />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="textfield" class="control-label col-sm-2">2</label>
                                            <div class="col-sm-6"> <span>{{ $details->productDetails->rating_2 or '' }}</span><br />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="textfield" class="control-label col-sm-2">1</label>
                                            <div class="col-sm-6"> <span>{{ $details->productDetails->rating_1 or '' }}</span><br />
                                            </div>
                                        </div>

                                    </div>--}}
                            </fieldset>




                        </div>
                        <!-- Reviews End -->
                        <!-- Reviews Start -->

                        <!-- Reviews End -->



                    </div>


                    <div id="information" class="tab-pane fade">

                        <div class="group-title">
                            <h2>Seller Information</h2>
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <td width="200">Seller</td>
                                <td>{{ $details->productDetails->supplier or '' }}</td>
                            </tr>
                            <tr>
                                <td>Seller code</td>
                                <td>{{ $details->productDetails->supplier_code or '' }}</td>
                            </tr>
                            <tr>
                                <td>Expected Delivery</td>
                                <td>{{ $details->productDetails->expected_delivery_date or '' }}</td>
                            </tr>
                            <tr>
                                <td>Delivery Charge</td>
                                <td>{{ $details->productDetails->delivery_charge or '' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div id="policy_desc" class="tab-pane fade">
                      <p> {!! $details->productDetails->policy_desc!!}</p>
                    </div>
                </div>
                <!-- Product Thumbnail Tab Content End -->
            </div>
        </div>
        <!-- Row End -->
    </div>
    <!-- Container End -->
</div>
<!-- Product Thumbnail Description End -->
<!-- Realted Products Start Here -->
<div class="hot-deal-products off-white-bg pt-100 pb-90 pt-sm-60 pb-sm-50">
    @if(!empty($details->relatedProducts))

    <div class="container">
        <!-- Product Title Start -->
        <div class="post-title pb-30">
            <h2>Realted Products</h2>
        </div>
        <!-- Product Title End -->
        <!-- Hot Deal Product Activation Start -->
        <div class="hot-deal-active owl-carousel">
            <!-- Single Product Start -->

            @foreach($details->relatedProducts as $val)

            <div class="single-product">
                <!-- Product Image Start -->
                <div class="pro-img">
                    <a href="{{ $val->url }}">
                       {{-- <img class="primary-img" src="img/products/17.jpg" alt="single-product">
                        <img class="secondary-img" src="img/products/18.jpg" alt="single-product">
--}}
                        @if(!empty($val->imgs[0]))
                            <img class="primary-img" alt="product"
                                 src="{{ $val->imgs[0]->img_path }}"/>
                            <img class="secondary-img" alt="product"
                                 src="{{ $val->imgs[0]->img_path }}"/>
                        @endif
                    </a>
                    <a href="#" class="quick_view" data-toggle="modal" data-target="#myModal" title="Quick View"><i class="lnr lnr-magnifier"></i></a>
                </div>
                <!-- Product Image End -->
                <!-- Product Content Start -->
                <div class="pro-content">
                    <div class="pro-info">
                        <h4><a href="{{ $val->url }}">{{ $val->name }}</a></h4>
                        <p><span class="price">{{ $val->price }}</span></p>
                    </div>
                    <div class="pro-actions">
                        <div class="actions-primary">
                            <a href="cart.html" title="Add to Cart"> + Add To Cart</a>
                        </div>
                        <div class="actions-secondary">
                            <a href="compare.html" title="Compare"><i class="lnr lnr-sync"></i> <span>Add To Compare</span></a>
                            <a class="wishlist" href="{{route('ecom.product.add-to-wishlist',['code'=>$details->productDetails->supplier_product_code])}}"  product_id="{{ $details->productDetails->supplier_product_code}}" category="{{ $details->productDetails->category_url_str}}" product="{{$details->productDetails->product_slug}}"><i class="lnr lnr-heart"></i> <span>Add to WishList</span></a>
                        </div>
                    </div>
                </div>
                <!-- Product Content End -->
                <span class="sticker-new">new</span>
            </div>
            @endforeach


        </div>

            <!-- Single Product End -->
        </div>
        <!-- Hot Deal Product Active End -->
    @endif

    </div>
    <!-- Container End -->
<div class="hot-deal-products off-white-bg pt-100 pb-90 pt-sm-60 pb-sm-50">
    @if(!empty($details->recentProducts))

        <div class="container">
            <!-- Product Title Start -->
            <div class="post-title pb-30">
                <h2>Recent Products</h2>
            </div>
            <!-- Product Title End -->
            <!-- Hot Deal Product Activation Start -->
            <div class="hot-deal-active owl-carousel">
                <!-- Single Product Start -->

                @foreach($details->recentProducts as $val)

                    <div class="single-product">
                        <!-- Product Image Start -->
                        <div class="pro-img">
                            <a href="{{ $val->url }}">
                                {{-- <img class="primary-img" src="img/products/17.jpg" alt="single-product">
                                 <img class="secondary-img" src="img/products/18.jpg" alt="single-product">
         --}}
                                @if(!empty($val->imgs[0]))
                                    <img class="primary-img" alt="product"
                                         src="{{ $val->imgs[0]->img_path }}"/>
                                    <img class="secondary-img" alt="product"
                                         src="{{ $val->imgs[0]->img_path }}"/>
                                @endif
                            </a>
                            <a href="#" class="quick_view" data-toggle="modal" data-target="#myModal" title="Quick View"><i class="lnr lnr-magnifier"></i></a>
                        </div>
                        <!-- Product Image End -->
                        <!-- Product Content Start -->
                        <div class="pro-content">
                            <div class="pro-info">
                                <h4><a href="{{ $val->url }}">{{ $val->name }}</a></h4>
                                <p><span class="price">{{ $val->price }}</span></p>
                            </div>
                            <div class="pro-actions">
                                <div class="actions-primary">
                                    <a href="cart.html" title="Add to Cart"> + Add To Cart</a>
                                </div>
                                <div class="actions-secondary">
                                    <a href="compare.html" title="Compare"><i class="lnr lnr-sync"></i> <span>Add To Compare</span></a>
                                    <a href="wishlist.html" title="WishList"><i class="lnr lnr-heart"></i> <span>Add to WishList</span></a>
                                </div>
                            </div>
                        </div>
                        <!-- Product Content End -->
                        <span class="sticker-new">new</span>
                    </div>
                @endforeach


            </div>

            <!-- Single Product End -->
        </div>
        <!-- Hot Deal Product Active End -->
    @endif

</div>

</div>
<!-- Realated Products End Here -->
<!-- Support Area Start Here -->
<div class="support-area bdr-top">
    <div class="container">
        <div class="d-flex flex-wrap text-center">
            <div class="single-support">
                <div class="support-icon">
                    <i class="lnr lnr-gift"></i>
                </div>
                <div class="support-desc">
                    <h6>Great Value</h6>
                    <span>Nunc id ante quis tellus faucibus dictum in eget.</span>
                </div>
            </div>
            <div class="single-support">
                <div class="support-icon">
                    <i class="lnr lnr-rocket" ></i>
                </div>
                <div class="support-desc">
                    <h6>Worlwide Delivery</h6>
                    <span>Quisque posuere enim augue, in rhoncus diam dictum non</span>
                </div>
            </div>
            <div class="single-support">
                <div class="support-icon">
                    <i class="lnr lnr-lock"></i>
                </div>
                <div class="support-desc">
                    <h6>Safe Payment</h6>
                    <span>Duis suscipit elit sem, sed mattis tellus accumsan.</span>
                </div>
            </div>
            <div class="single-support">
                <div class="support-icon">
                    <i class="lnr lnr-enter-down"></i>
                </div>
                <div class="support-desc">
                    <h6>Shop Confidence</h6>
                    <span>Faucibus dictum suscipit eget metus. Duis  elit sem, sed.</span>
                </div>
            </div>
            <div class="single-support">
                <div class="support-icon">
                    <i class="lnr lnr-users"></i>
                </div>
                <div class="support-desc">
                    <h6>24/7 Help Center</h6>
                    <span>Quisque posuere enim augue, in rhoncus diam dictum non.</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Container End -->
</div>

@endif
<script type="text/javascript" src="{{asset('js/providers/ecom/product/product_details.js')}}"></script>

@stop
{{--@section('scripts')


@stop--}}
