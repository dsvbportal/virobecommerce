<header class="main-header">
    <!-- Logo -->
    <a href="{{route('fr.dashboard')}}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <div class="logo-mini"><img src="{{asset('resources/assets/themes/franchisee/img/logo-mini.png')}}" title="{{$pagesettings->site_name}} Affiliate"></div>
      <!-- logo for regular state and mobile devices -->
      <div class="logo-lg"><img src="{{asset('resources/assets/themes/franchisee/img/logo.png')}}" title="{{$pagesettings->site_name}} Affiliate"></div>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
	  <div class="top-cont"><h3>Your Relationship Manager - <span>  {{$userSess->uname}} </span> </h3></div>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">          
         <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">0</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">Notifications not available</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;"><ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
                  <!-- messages will come here -->
                </ul><div class="slimScrollBar" style="background: rgb(0, 0, 0) none repeat scroll 0% 0%; width: 3px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px;"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div></div>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
          <li class="dropdown user user-menu">
            <a href="{{route('fr.logout')}}" class="logoutBtn">
				<i class="fa fa fa-sign-out"></i>
              Logout
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <!--<img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">-->
                <p>
				  {{$userSess->full_name}}
                  <small>ID:{{$userSess->uname}}</small>
                </p>
              </li>              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{route('fr.profile')}}" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="{{route('fr.logout')}}" class="btn btn-default btn-flat logoutBtn">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>