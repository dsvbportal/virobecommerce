@include('frontend.common.top_header')
<div class="container main-header">
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 logo">
            <a href="{{URL::asset('/')}}"><img alt="{{$pagesettings->site_name}}" src="{{URL::asset($pagesettings->site_logo)}}" /></a>
        </div>
        <div class="col-xs-7 col-sm-7 col-md-6 header-search-box">
            <form class="form-inline" id="search-products">
                <div class="form-group form-category">
                    <select class="select-category" id="searchCategory">
                    </select>
                </div>
                <div class="form-group input-serach">
                    <input type="text"  placeholder="Keyword here..." id="searchTerm">
                </div>
                <button type="submit" class="pull-right btn-search"></button>
            </form>
        </div>
        <div id="cart-block" class="col-xs-5 col-sm-2 col-md-3 shopping-cart-box">
            <a class="cart-link" href=""><span class="title">Shopping cart</span><span class="total">0 items - USD $ 0. 00</span><span class="notify notify-left">0</span></a>
        </div>
    </div>
</div>
<div id="nav-top-menu" class="nav-top-menu">
    <div class="container">
        <div class="row">
            <div id="main-menu" class="main-menu">
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand title" href="#">@yield('pagetitle')</a>
                        </div>
                        <div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
                            <ul class="nav navbar-nav">
                                <li class="active"><a href="#" class="title">@yield('pagetitle')</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div id="form-search-opntop">
        </div>
        <div id="user-info-opntop">
        </div>
        <div id="shopping-cart-box-ontop" style="display: none;">
            <i class="fa fa-shopping-cart"></i>
            <div class="shopping-cart-box-ontop-content"></div>
        </div>
    </div>
</div>
