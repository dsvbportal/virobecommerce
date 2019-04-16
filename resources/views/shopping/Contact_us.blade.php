@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.header')
@stop
@section('content')

 <!-- Contact Email Area Start -->
        <div class="contact-area ptb-50 ptb-sm-60">
            <div class="container">
              <div class="section-heading text-center">
                <h2 class="mb-10 inner-title">CONTACT US</h2>
                <div class="reen-dots wow fadeInUp justify-content-center" data-wow-delay="0.2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
  <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
  </div>
                        </div>
                        <div class="row">

            <div class="col-lg-4 col-md-6">
                <div class="single-contact-info bg-1 white"><!-- single contact info -->
                    <div class="icon">
                      <i class="lnr lnr-phone"></i>
                    </div>
                    <div class="content">
                        <h4 class="title">CALL</h4>
                        <span class="details">+91 80 29791919</span>
                        <span class="details">Timings 10am - 7pm (Mon - Sat)</span>
                    </div>
                </div><!-- //.single contact info -->
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="single-contact-info bg-2 white"><!-- single contact info -->
                    <div class="icon">
                        <i class="lnr lnr-home"></i>
                    </div>
                    <div class="content">
                        <h4 class="title">OFFICE</h4>
                        <span class="details"><b>Virob Ecommerce India Pvt Ltd</b><br>
4th Floor, No. 13, Paripoorna Layout, Phase 3, Yelahanka, Bangalore - 560064
</span>
                    </div>
                </div><!-- //.single contact info -->
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="single-contact-info bg-3 white"><!-- single contact info -->
                    <div class="icon">
                        <i class="lnr lnr-envelope"></i>
                    </div>
                    <div class="content">
                        <h4 class="title">EMAIL</h4>
                        <span class="details">care@virob.com</span>
                        <span class="details">support@virob.com</span>
                    </div>
                </div><!-- //.single contact info -->
            </div>

        </div>
                <form  class="contact-form pt-50"  id="contact_usfrm" action="{{route('ecom.update_contact_us')}}" method="post" autocomplete="off" >
                    <div class="address-wrapper row">
                        <div class="col-md-12">
                            <div class="address-fname">
                                <input class="form-control" type="text" name="name" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="address-email">
                                <input class="form-control" type="email" name="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="address-web">
                                <input class="form-control" type="text" name="website" placeholder="Website">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="address-subject">
                                <input class="form-control" type="text" name="subject" placeholder="Subject">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="address-textarea">
                                <textarea name="message" class="form-control" placeholder="Write your message"></textarea>
                            </div>
                        </div>
                    </div>
                    <p class="form-message"></p>
                    <div class="footer-content mail-content clearfix">
                        <div class="send-email float-md-right">
                            <input value="Submit" class="return-customer-btn" type="submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Contact Email Area End -->
       

@stop
@section('scripts')
<script src="{{asset('validate/lang/cart_details')}}" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/providers/ecom/product/add_cart.js')}}"></script>
@stop