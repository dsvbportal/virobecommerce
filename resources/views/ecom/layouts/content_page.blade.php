@extends('ecom.layouts.layout')
@section('page-type','category-page')
@section('page')
<div class="columns-container">
    <div class="container" id="columns">
        <div class="clearfix">
            @yield('breadcrumb')
        </div>
        <h2 class="page-heading">
            <span class="page-heading-title2">@yield('pagetitle')</span>
        </h2>
        @yield('contents')
    </div>
</div>
@stop
