@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.header')
@stop
@section('content')
@if(!empty($contents))
    @foreach($contents as $content)
        @if(is_array($content->description))
            @foreach($content->description as $k=>$d)
                <div id="cms_content">   
                    <p>{!!$d->desc!!}</p>
                    <img src="" alt="">
                 </div>   
            @endforeach
        @else
        {!!$content->description!!}
        @endif
    @endforeach
@else

<div id="err_msg">   
                    <p>something went wrong</p>
                    <img src="" alt="">

</div>
@endif
<div class="support-area bdr-top mt-30">
            <div class="container">
                <div class="d-flex flex-wrap text-center">
                    <div class="single-support">
                        <div class="support-icon">
                            <i class="lnr lnr-gift"></i>
                        </div>
                        <div class="support-desc">
                            <h6>Great Value</h6>
                            <span>Get thousands of products at great margins!. Spot the best offers and discounts from virob.com.</span>
                        </div>
                    </div>
                    <div class="single-support">
                        <div class="support-icon">
                            <img src="{{asset('resources/assets/themes/shopping/img/Safe-Payment.svg')}}" alt="">
                        </div>
                        <div class="support-desc">
                            <h6>Safe Payment</h6>
                            <span>Feel confident while shopping with us because we employ the most secure payment options.</span>
                        </div>
                    </div>
                    <div class="single-support">
                        <div class="support-icon">
                           <i class="lnr lnr-lock"></i>
                        </div>
                        <div class="support-desc">
                            <h6>Authentic Brands</h6>
                            <span>Virob.com is committed to building brand value by delivering authentic brands with quality assurance.</span>
                        </div>
                    </div>
                    <div class="single-support">
                        <div class="support-icon">
                          <img src="{{asset('resources/assets/themes/shopping/img/Easy-Returns.svg')}}" alt="">
                        </div>
                        <div class="support-desc">
                            <h6>Easy Returns</h6>
                            <span>You may request returns for most items you buy from virob.com, except those that are explicitly identified as not returnable.</span>
                        </div>
                    </div>
                    <div class="single-support">
                        <div class="support-icon">
                            <img src="{{asset('resources/assets/themes/shopping/img/Help-Center.svg')}}" alt="">
                        </div>
                        <div class="support-desc">
                            <h6>Help Center</h6>
                            <span>You can message our customer service anytime. our friendly customer care executives are always glad to help.</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Container End -->
        </div>


@stop



