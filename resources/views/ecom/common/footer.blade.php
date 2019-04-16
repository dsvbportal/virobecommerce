<div id="introduce-box" class="row">
    <div class="col-md-3">
        <div id="address-box"> <!-- URL::asset('imgs/270/61/'.$pagesettings->site_logo) -->
            <a href="{{URL::to('/')}}"><img src="{{$pagesettings->site_logo}}" alt="{{$pagesettings->site_name}}" /></a>
            <div id="address-list">
                <div class="tit-name">Address:</div>
                <div class="tit-contain">{{$pagesettings->address->country}}</div>
                <div class="tit-name">Phone:</div>
                <div class="tit-contain">{{$pagesettings->phone}}</div>
                <div class="tit-name">Email:</div>
                <div class="tit-contain">{{$pagesettings->noreply_email}}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-4" id="footer_primary">
            </div>
            <div class="col-sm-4" id="footer_account">
            </div>
            <div class="col-sm-4" id="footer_support">
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
<div id="footer_catalogue" class="row">

</div>
<div id="footer-menu-box">
    <div class="col-sm-12">
        <ul class="footer-menu-list">
        </ul>
    </div>
</div>
