<nav id="top_navigation">
    <div class="container">
        <ul id="icon_nav_h" class="top_ico_nav clearfix">
            <li>
                <a href="{{URL::to('admin/dashboard')}}">
                    <i class="fa fa-home icon-2x"></i>
                    <span class="menu_label">Home</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('admin/suppliers')}}">
                    <i class="fa fa-exchange icon-2x"></i>
                    <span class="menu_label">Suppliers</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('admin/products')}}">
                    <i class="fa fa-tags icon-2x"></i>
                    <span class="menu_label">Products</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('admin/feedback')}}">
                    <i class="fa fa-comments-alt icon-2x"></i>
                    <span class="menu_label">FeedBacks</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('settings')}}">
                    <i class="fa fa-wrench icon-2x"></i>
                    <span class="menu_label">Settings</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
