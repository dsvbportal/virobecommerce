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
            <div class="col-sm-3" id="box-vertical-megamenus">
                <div class="box-vertical-megamenus">
                    <h4 class="title">
                        <span class="title-menu">Categorie</span>
                        <span class="btn-open-mobile pull-right home-page"><i class="fa fa-bars"></i></span>
                    </h4>
                    <div class="vertical-menu-content is-home" style="display: none;">
                        <ul class="vertical-menu-list" id="header_catalogue">
                            <?php
                            if (!empty($menu_data->header_catalogue))
                            {
                                foreach ($menu_data->header_catalogue as $data)
                                {
                                    if (empty($data->group))
                                    {
                                        ?>
                                        <li><a href="{{URL::asset($data->url)}}"><img class="icon-menu" alt="{{$data->title}}" src="assets/theme/sp-theme/data/1.png">{{ $data->title or ''}}</a></li>
                                        <?php
                                    }
                                    else if (!empty($data->group))
                                    {
                                        ?>
                                        <li><a class="parent" href="{{URL::asset($data->url)}}"><img class="icon-menu" alt="Funky roots" src="assets/theme/sp-theme/data/2.png">{{$data->title or ''}}</a>
                                            <div class="vertical-dropdown-menu">
                                                <div class="vertical-groups col-sm-12">
                                                    <div class="mega-group col-sm-4">
                                                        <?php
                                                        foreach ($data->group as $grp)
                                                        {
                                                            foreach ($grp as $group)
                                                            {
                                                                if (!empty($group->links))
                                                                {
                                                                    ?>
                                                                    <h4 class="mega-group-header"><span>{{$group->title}}</span></h4>
                                                                    <?php
                                                                    foreach ($group->links as $link)
                                                                    {
                                                                        ?>
                                                                        <ul class="group-link-default">
                                                                            <li><a href="{{URL::asset($link->url)}}">{{ $link->title}}</a></li>
                                                                        </ul>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="mega-custom-html col-sm-12">
                                                        <a href="#"><img src="assets/theme/sp-theme/data/banner-megamenu.jpg" alt="Banner"></a>
                                                    </div>
                                                </div>
                                                <div class="mega-custom-html col-sm-12">
                                                    <a href="#"><img src="assets/theme/sp-theme/data/banner-megamenu.jpg" alt="Banner"></a>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                            }
                            ?>
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
                                <?php
                                if (!empty($menu_data->header_primary))
                                {
                                    foreach ($menu_data->header_primary as $data)
                                    {
                                        if (empty($data->group))
                                        {
                                            ?>
                                            <li ><a href="{{URL::asset($data->url)}}">{{$data->title or ''}}</a></li>
                                            <?php
                                        }
                                        else if (!empty($data->group))
                                        {
                                            ?>
                                            <li class="dropdown">
                                                <a href="category.html" class="dropdown-toggle" data-toggle="dropdown">{{$data->title or ''}}</a>
                                                <ul class="dropdown-menu mega_dropdown" role="menu" style="width: 830px;">
                                                    <?php
                                                    foreach ($data->group as $grp)
                                                    {
                                                        ?>
                                                        <li class="block-container col-sm-3">
                                                            <ul class="block">
                                                                <?php
                                                                foreach ($grp as $group)
                                                                {
                                                                    if (!empty($group->links))
                                                                    {
                                                                        ?>
                                                                        <li class="link_container group_header"><a href="{{URL::asset($group->url)}}">{{$group->title or ''}}</a></li>
                                                                        <?php
                                                                        foreach ($group->links as $link)
                                                                        {
                                                                            ?>
                                                                            <li class="link_container"><a href="{{URL::asset($link->url)}}">{{$link->title or ''}}</a></li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                ?>
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
            <i class="fa fa-shopping-cart"></i>
            <div class="shopping-cart-box-ontop-content"></div>
        </div>
        <div id="user-notifications-box-ontop">
            <i class="fa fa-bell"></i>
            <span class="badge count"></span>
            <div class="user-notifications-box-ontop-content">
                <ul class="dropdown-menu mega_dropdown list" role="menu">
                </ul>
            </div>
        </div>
    </div>
</div>
