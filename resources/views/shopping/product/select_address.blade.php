@extends('shopping.layout.home_layout')
@section('home_page_header')
	@include('shopping.common.header')
@stop
@section('content')

    <div class="breadcrumb-area mt-30">
        <div class="container">
            <div class="breadcrumb">
                <ul class="d-flex align-items-center">
                    <li><a href="index.html">Home</a></li>
                    <li class="active"><a href="checkout.html">Checkout</a></li>
                </ul>
            </div>
        </div>
        <!-- Container End -->
    </div>
    <!-- Breadcrumb End -->
    <!-- coupon-area start -->
    <div class="coupon-area pt-100 pt-sm-60">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="coupon-accordion">
                        <!-- ACCORDION START -->
                        <h3>Returning customer? <span id="showlogin">Click here to login</span></h3>
                        <div id="checkout-login" class="coupon-content">
                            <div class="coupon-info">
                                <p class="coupon-text">Quisque gravida turpis sit amet nulla posuere lacinia. Cras sed est sit amet ipsum luctus.</p>
                                <form action="#">
                                    <p class="form-row-first">
                                        <label>Username or email <span class="required">*</span></label>
                                        <input type="text" />
                                    </p>
                                    <p class="form-row-last">
                                        <label>Password  <span class="required">*</span></label>
                                        <input type="text" />
                                    </p>
                                    <p class="form-row">
                                        <input type="submit" value="Login" />
                                        <label>
                                            <input type="checkbox" />
                                            Remember me
                                        </label>
                                    </p>
                                    <p class="lost-password">
                                        <a href="#">Lost your password?</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                        <!-- ACCORDION END -->
                        <!-- ACCORDION START -->
                        <h3>Have a coupon? <span id="showcoupon">Click here to enter your code</span></h3>
                        <div id="checkout_coupon" class="coupon-checkout-content">
                            <div class="coupon-info">
                                <form action="#">
                                    <p class="checkout-coupon">
                                        <input type="text" class="code" placeholder="Coupon code" />
                                        <input type="submit" value="Apply Coupon" />
                                    </p>
                                </form>
                            </div>
                        </div>
                        <!-- ACCORDION END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- coupon-area end -->
    <!-- checkout-area start -->




    <div class="checkout-area pb-100 pt-15 pb-sm-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="checkbox-form mb-sm-40">
                        <h3>Billing Details</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="country-select clearfix mb-30">
                                    <label>Country <span class="required">*</span></label>
                                    <select class="wide">
                                        <option value="volvo">Bangladesh</option>
                                        <option value="saab">Algeria</option>
                                        <option value="mercedes">Afghanistan</option>
                                        <option value="audi">Ghana</option>
                                        <option value="audi2">Albania</option>
                                        <option value="audi3">Bahrain</option>
                                        <option value="audi4">Colombia</option>
                                        <option value="audi5">Dominican Republic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-form-list mb-sm-30">
                                    <label>First Name <span class="required">*</span></label>
                                    <input type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-form-list mb-30">
                                    <label>Last Name <span class="required">*</span></label>
                                    <input type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkout-form-list mb-30">
                                    <label>Company Name</label>
                                    <input type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkout-form-list">
                                    <label>Address <span class="required">*</span></label>
                                    <input type="text" placeholder="Street address" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkout-form-list mtb-30">
                                    <input type="text" placeholder="Apartment, suite, unit etc. (optional)" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkout-form-list mb-30">
                                    <label>Town / City <span class="required">*</span></label>
                                    <input type="text" placeholder="Town / City" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-form-list mb-30">
                                    <label>State / County <span class="required">*</span></label>
                                    <input type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-form-list mb-30">
                                    <label>Postcode / Zip <span class="required">*</span></label>
                                    <input type="text" placeholder="Postcode / Zip" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-form-list mb-30">
                                    <label>Email Address <span class="required">*</span></label>
                                    <input type="email" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkout-form-list mb-30">
                                    <label>Phone  <span class="required">*</span></label>
                                    <input type="text" placeholder="Postcode / Zip" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkout-form-list create-acc mb-30">
                                    <input id="cbox" type="checkbox" />
                                    <label>Create an account?</label>
                                </div>
                                <div id="cbox_info" class="checkout-form-list create-accounts mb-25">
                                    <p class="mb-10">Create an account by entering the information below. If you are a returning customer please login at the top of the page.</p>
                                    <label>Account password  <span class="required">*</span></label>
                                    <input type="password" placeholder="password" />
                                </div>
                            </div>
                        </div>
                        <div class="different-address">
                            <div class="ship-different-title">
                                <h3>
                                    <label>Ship to a different address?</label>
                                    <input id="ship-box" type="checkbox" />
                                </h3>
                            </div>
                            <div id="ship-box-info">

                                <form class="form-horizontal" id="addressFrm" action="{{route('ecom.account.save-address')}}" data-pincode-url="{{route('ecom.account.check-pincode')}}" method="post" autocomplete="off">

                                    <input name="address[is_default]" id="is_default" type="hidden" value="1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="country-select clearfix mb-30">
                                            <label>Country <span class="required">*</span></label>
                                            <select name="address[country_id]" id="country_id" class="wide" disabled>
                                                <option value="{{$logged_userinfo->country_id}}">{{$logged_userinfo->country}}</option>

                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>First Name <span class="required">*</span></label>
                                            <input id="fullname" placeholder="Full Name" class="form-control" value="{{$logged_userinfo->full_name}}" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Last Name <span class="required">*</span></label>
                                            <input type="text" placeholder="" />
                                        </div>
                                    </div>
                                  {{--  <div class="col-md-12">
                                        <div class="checkout-form-list mb-30">
                                            <label>Company Name</label>
                                            <input type="text" placeholder="" />
                                        </div>
                                    </div>--}}

                                    <div class="col-md-12">

                                        <div class="checkout-form-list mb-30">
                                            <label>Address <span class="required">*</span></label>
                                            <input name="address[flat_no]" type="text" id="address" placeholder="Street Address"/>
                                            <span id="address_err"></span>

                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Postcode  <span class="required">*</span></label>
                                            <input type="text" name="address[postal_code]" id="postal_code" placeholder="Postcode"  value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Landmark  <span class="required">*</span></label>
                                            <input type="text" name="address[landmark]" id="landMark" placeholder="LandMark"  value="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Town / City <span class="required">*</span></label>
                                            <select name="address[city_id]" id="city_id"  class="form-control">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>State / County <span class="required">*</span></label>
                                            <select name="address[state_id]" id="state_id" class="form-control"></select>
                                        </div>
                                    </div>

{{--

                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Email Address <span class="required">*</span></label>
                                            <input type="email" placeholder="" />
                                        </div>
                                    </div>--}}
                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Alternate Mobile No  <span class="required">*</span></label>
                                            <span class="input-group-addon">{{$logged_userinfo->phone_code}}</span>
                                            <input type="text" class="form-control"  name="address[alternate_mobile]" id="alternate_mobile" placeholder="Mobile" onkeypress="return isNumberKey(event)" required />
                                            <span id="alternate_mobile_err"></span>

                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="checkout-form-list mb-30">
                                            <label>Address Type  <span class="required">*</span></label>
                                            <input type="radio" name="address[address_type]" class="address_type" value="1" data-err-msg-to="#addr_type_err">Home
                                            <input type="radio" name="address[address_type]" class="address_type" value="3" data-err-msg-to="#addr_type_err">Office/Commercial
                                            <span id="addr_type_err"></span>

                                        </div>
                                    </div>

                                    <div class="col-md-12">

                                    <button type="submit" id='addressSaveBtn' data-form="#" class="btn btn-primary"><i class="fa fa-save"></i> Add Address</button>

                                </div>
                                </div>

                                </form>


                            </div>
                            <div class="order-notes">
                                <div class="checkout-form-list">
                                    <label>Order Notes</label>
                                    <textarea id="checkout-mess" cols="30" rows="10" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="your-order">
                        <h3>Your order</h3>
                        <div class="your-order-table table-responsive">
                            <table>
                                <thead>
                                <tr>
                                    <th class="product-name">Product</th>
                                    <th class="product-total">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="cart_item">
                                    <td class="product-name">
                                        Vestibulum suscipit <span class="product-quantity"> × 1</span>
                                    </td>
                                    <td class="product-total">
                                        <span class="amount">£165.00</span>
                                    </td>
                                </tr>
                                <tr class="cart_item">
                                    <td class="product-name">
                                        Vestibulum dictum magna <span class="product-quantity"> × 1</span>
                                    </td>
                                    <td class="product-total">
                                        <span class="amount">£50.00</span>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="cart-subtotal">
                                    <th>Cart Subtotal</th>
                                    <td><span class="amount">£215.00</span></td>
                                </tr>
                                <tr class="order-total">
                                    <th>Order Total</th>
                                    <td><span class=" total amount">£215.00</span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="payment-method">
                            <div id="accordion">
                                <div class="card">
                                    <div class="card-header" id="headingone">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Direct Bank Transfer
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingone" data-parent="#accordion">
                                        <div class="card-body">
                                            <p>Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won’t be shipped until the funds have cleared in our account.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingtwo">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                Cheque Payment
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingtwo" data-parent="#accordion">
                                        <div class="card-body">
                                            <p>Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingthree">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                PayPal
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingthree" data-parent="#accordion">
                                        <div class="card-body">
                                            <p>Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop
@section('scripts')
<script src="{{asset('validate/lang/cart_details')}}" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('js/providers/ecom/product/add_address.js')}}"></script>
@stop