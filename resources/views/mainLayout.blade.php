<!DOCTYPE html>
<html lang="{{$pagesettings->language_iso_code}}">
    <head>
        <title>@yield('main-title',$pagesettings->site_name)</title>
        <base href="{{URL::to('/')}}/">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="application-name" content="{{$pagesettings->site_meta_title}}" />
        <meta name="account-type" content="{{$pagesettings->account_type or ''}}" />
        <meta name="title" content="@yield('main-title',$pagesettings->site_name)"/>
        <meta name="keywords" content="{{$pagesettings->site_meta_keyword}}"/>
        <meta name="description" content="{{$pagesettings->site_meta_description}}" />
        <meta name="Robots" Content="Index, follow">
        <meta name="X-FCM-ID" content="{{$device_log->fcm_registration_id or ''}}"/>
        <meta name="X-Device-Token" content="{{$device_log->token or ''}}"/>
        <link rel="alternate" href="{{ URL::to('/')}}" hreflang="en-gb"/>
        <link rel="canonical" href="{{ URL::to('/')}}/" />
        <link rel="manifest" href="{{URL::asset('manifest.json')}}"/>
        <meta name="theme-color" content="#00ff00">		 
		<meta name="csrf-token" content="{{ csrf_token() }}">       
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
        @yield('head-style')
        @yield('head-script')
    </head>
    @yield('body')
</html>
