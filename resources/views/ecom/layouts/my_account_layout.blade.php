@extends('ecom.layouts.content_page')
@section('pagetitle')
Authentication
@stop
@section('contents')
<div class="page-content">
    <div class="row">
        <div class="col-sm-3">
            <h2><b>My Account</b></h2>
            <br/>
            <h5><b>SETTINGS</b></h5>
            <ul>
                <li><a href="{{route('ecom.account.profile')}}"><span>Personal Information</span></a></li>
                <li><a href="{{route('ecom.account.change-pwd')}}"><span>Change Password</span></a></li>
                <li><a href="#"><span>Addresses</span></a></li>
                <li><a href="#"><span>Update Email/Mobile</span></a></li>
                <li><a href="#"><span>Deactivate Account</span></a></li>
            </ul>
            <hr />
            <h5><b>ORDERS</b></h5>
            <ul>
                <li><a href="{{URL::to('my-orders')}}"><span>My Orders</span></a></li>
            </ul>
            <hr />
            <h5><b>MY STUFF</b></h5>
            <ul>
                <li><a href="{{URL::to('account/wishlist')}}"><span>My Wishlist</span></a></li>
            </ul>
            <hr />
        </div>
        <div class="col-sm-9">	      
			@yield('account-content')
        </div>
    </div>
</div>
@stop
