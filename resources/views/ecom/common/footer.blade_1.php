<div id="introduce-box" class="row">
    <div class="col-md-3">
        <div id="address-box">
            <a href="{{URL::to('/')}}"><img src="{{URL::asset($pagesettings->site_logo)}}" alt="{{$pagesettings->site_name}}" /></a>
            <div id="address-list">
                <div class="tit-name">Address:</div>
                <div class="tit-contain">{{$pagesettings->address}}</div>
                <div class="tit-name">Phone:</div>
                <div class="tit-contain">{{$pagesettings->phone}}</div>
                <div class="tit-name">Email:</div>
                <div class="tit-contain">{{$pagesettings->noreply_email}}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-4">
                @if(isset($menu_data->footer_primary)&&!empty($menu_data->footer_primary))
                @foreach($menu_data->footer_primary as $fpmenu)
                <div class="introduce-title">{{$fpmenu->title}}</div>
                <ul id = "introduce-support"  class="introduce-list">
                    @foreach($fpmenu->normal as $link)
                    <li><a href="{{URL::asset($link->url)}}">{{$link->title}}</a></li>
                    @endforeach
                </ul>
                @endforeach
                @endif
            </div>
            <div class="col-sm-4">
                @if(isset($menu_data->footer_account)&&!empty($menu_data->footer_account))
                @foreach($menu_data->footer_account as $acc)
                <div class="introduce-title">{{$acc->title}}</div>
                <ul id = "introduce-support"  class="introduce-list">
                    @foreach($acc->normal as $link)
                    <li><a href="{{URL::asset($link->url)}}">{{$link->title}}</a></li>
                    @endforeach
                </ul>
                @endforeach
                @endif
            </div>
            <div class="col-sm-4">
                @if(isset($menu_data->footer_support)&&!empty($menu_data->footer_support))
                @foreach($menu_data->footer_support as $supp)
                <div class="introduce-title">{{$supp->title}}</div>
                <ul id = "introduce-support"  class="introduce-list">
                    @foreach($supp->normal as $link)
                    <li><a href="{{URL::asset($link->url)}}">{{$link->title}}</a></li>
                    @endforeach
                </ul>
                @endforeach
                @endif
            </div>

        </div>
    </div>
    <div class="col-md-3">
        <div id="contact-box">
            <form id="subscribe-form">
                <div class="introduce-title">Newsletter</div>
                <div class="input-group" id="mail-box">
                    <input type="email" name="subscribe[email_id]" placeholder="Your Email Address"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">OK</button>
                    </span>
                </div><!-- /input-group -->
            </form>
            <div class="introduce-title">Let's Socialize</div>
            <div class="social-link">
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-pinterest-p"></i></a>
                <a href="#"><i class="fa fa-vk"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-google-plus"></i></a>
            </div>
        </div>

    </div>
</div>
<div id="trademark-box" class="row">
    <div class="col-sm-12">
        <ul id="trademark-list" title="Accepted Payment Methods">
        </ul>
    </div>
</div>
<div id="trademark-text-box" class="row">
    @if(!empty($menu_data->footer_catlog))
    @foreach($menu_data->footer_catlog as $f_menu)
    @if($f_menu->type == 2 && isset($f_menu->normal)&& !empty($f_menu->normal))
    <div class="col-sm-12">
        <ul id="trademark-search-list" class="trademark-list">
            <li class="trademark-text-tit">{{$f_menu->title}}:</li>
            @foreach($f_menu->normal as $norm)
            <li><a href="{{URL::asset($norm->url)}}" >{{$norm->title}}</a></li>
            @endforeach
        </ul>
    </div>
    @endif
    @endforeach
    @endif
</div>
<div id="footer-menu-box">
    <div class="col-sm-12">
        <ul class="footer-menu-list">
            @if(!empty($menu_data->footer_secondary))
            @foreach($menu_data->footer_secondary as $footer)
            <li><a href="{{URL::asset($footer->url)}}" >{{$footer->title}}</a></li>
            @endforeach
            @endif
        </ul>
    </div>
</div>
