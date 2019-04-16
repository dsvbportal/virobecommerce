@include('ecom.common.top_header')
<div class="container main-header">
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 logo"> <!-- URL::asset('imgs/200/40/'.$pagesettings->site_logo) "http://localhost/dsvb_affiliate/resources/assets/imgs/affiliate-logo.png"-->
            <a href="{{URL::asset('/')}}"><img alt="{{$pagesettings->site_name}}" src="{{$pagesettings->site_logo}}" /></a>
        </div>
        <div class="col-xs-9 col-sm-6 col-md-7 col-lg-7 header-search-box">
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
        <div id="cart-block" class="col-xs-3 col-sm-3 col-md-2 col-lg-2 shopping-cart-box">
            <a class="cart-link" href=""><span class="notify">0</span></a>
        </div>
    </div>
</div>
<div id="nav-top-menu" class="nav-top-menu">
    <div class="container">
        <div class="row">
            <div class="col-sm-3" id="box-vertical-megamenus">
                <div class="box-vertical-megamenus">
                    <h4 class="title">
                        <span class="title-menu">Categorie</span>
                        <span class="btn-open-mobile pull-right home-page"><i class="fa fa-bars"></i></span>
                    </h4>
                    <div class="vertical-menu-content is-home" style="display: none;">
                        <ul class="vertical-menu-list" id="header_catalogue">

                        </ul>
                        <div class="all-category"><span class="open-cate">All Categories</span></div>
                    </div>
                </div>
            </div>
            <div id="main-menu" class="col-sm-9 main-menu">
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                <i class="fa fa-bars"></i>
                            </button>
                            <a class="navbar-brand" href="#">MENU</a>
                        </div>
                        <div id="navbar" class="navbar-collapse collapse">
                            <ul class="nav navbar-nav" id="header_primary">
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div id="form-search-opntop">
        </div>
        <div id="user-info-opntop" style="display: block;">
        </div>
        <div id="shopping-cart-box-ontop">
            <a href="#"><i class="fa fa-shopping-cart"></i></a>
        </div>
        <div id="user-notifications-box-ontop">
        </div>
    </div>
</div>