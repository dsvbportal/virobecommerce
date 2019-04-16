@extends('ecom.layouts.layout')
@section('page-type','category-page')
@section('page')
<div class="columns-container">
    <div class="container" id="columns">
        <div class="breadcrumb clearfix">
            <a class="home" href="{{URL::to('/')}}" title="Return to Home">Home</a>
            <span class="navigation-pipe">&nbsp;</span>
            @yield('breadcrumb')
            <span class="navigation_page"> @yield('pagetitle')</span>
        </div>
        @yield('content')
    </div>
</div>
@stop
