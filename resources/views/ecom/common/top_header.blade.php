<div class="top-header">   
    <div class="container">
        <div class="nav-top-links">
            <a class="first-item" href="javascript:void(0);"><i class="fa fa-phone"></i> {{$pagesettings->phone}}</a>
            <a href="{{URL::to('contact-us')}}"><i class="fa fa-envelope"></i>Contact us today!</a>

            <!--span id="user-notifications" class="user-notifications">
                <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><i class="fa fa-bell"></i><span class="badge count"></span></a>
                <ul class="dropdown-menu mega_dropdown list" role="menu"></ul>
            </span-->

        </div>
        <!--        <div class="currency ">
                    <div class="dropdown">
                        <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">USD</a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Dollar</a></li>
                            <li><a href="#">Euro</a></li>
                        </ul>
                    </div>
                </div>
                <div class="language ">
                    <div class="dropdown">
                        <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                            <img alt="email" src="assets/theme/sp-theme/images/fr.jpg" />French
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#"><img alt="email" src="assets/theme/sp-theme/images/en.jpg" />English</a></li>
                            <li><a href="#"><img alt="email" src="assets/theme/sp-theme/images/fr.jpg" />French</a></li>
                        </ul>
                    </div>
                </div>-->
        <div class="support-link">
            <a href="#">Services</a>
            <a href="#">Support</a>
            <div id="user-info-top" class="user-info pull-right">
                <div class="dropdown">
                    @if(isset($logged_userinfo) && !empty($logged_userinfo))
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><span>{{$logged_userinfo->full_name or ''}}</span></a>
                    <ul class="dropdown-menu mega_dropdown" role="menu">
                        <li><a href="{{route('ecom.account.profile')}}">My Profile</a></li>                       
                        <li><a href="#">Compare</a></li>
                        <li><a href="#">Wishlists</a></li>
                        <li><a href="{{route('ecom.logout')}}" id="logout">Logout</a></li>
                    </ul>
                    @else
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><span>My Account</span></a>
                    <ul class="dropdown-menu mega_dropdown" role="menu">
                        <li><a href="{{route('ecom.login')}}">Login</a></li>
                    </ul>
                    @endif
                </div>
            </div>
            <div id="user-notifications" class="user-notifications pull-right">
                <div class="dropdown">
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><i class="fa fa-bell"></i><span class="badge count"></span></a>
                    <ul class="dropdown-menu mega_dropdown list" role="menu"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
