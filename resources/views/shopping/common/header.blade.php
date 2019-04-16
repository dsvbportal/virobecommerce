<header>
			<div class="header-top-area">
                <div class="container">
                    <!-- Header Top Start -->
                    <div class="header-top">
                        <ul class="top_menu">
                            <li><a href="#">sell on Virob</a></li>
                            <li><a href="#">CUSTOMER CARE</a></li>
                            <li><a href="#">Track my order</a></li>
                             @if(isset($logged_userinfo) && !empty($logged_userinfo))
								<li><a href="{{route('ecom.account.profile')}}">{{$logged_userinfo->full_name or ''}}</a></li>
							@else
								<li data-toggle="modal" data-target="#login-model">Login / sign up</li>
							@endif
                        </ul>
                    </div>
                    <!-- Header Top End -->
                </div>
                <!-- Container End -->
            </div>
            <!-- Header Top Start Here -->
            <div class="header-middle ptb-15">
                <div class="container">
                    <div class="row align-items-center no-gutters">
                        <div class="col-lg-3 col-md-12">
                            <div class="logo mb-all-30">
                                <a href="{{url('/')}}"><img src="{{asset('resources/assets/themes/shopping/img/logo/logo.png')}}" alt="logo-image"></a>
                            </div>
                        </div>
                        <!-- Categorie Search Box Start Here -->
                        <div class="col-lg-5 col-md-8 ml-auto mr-auto col-10">
					        <div class="categorie-search-box">
                                 <form class="form-inline" id="search-products" data-search="{{route('ecom.product.search')}}">
										<div class="form-group form-category">
											<select class="bootstrap-select select-category" id="searchCategory" data-category="{{$category or ''}}"> 
											</select>
										</div>
										<input type="text" name="search_term" id="searchTerm" value="{{$searchtxt or ''}}" autocomplete="off" placeholder="Search for products, brands and more...">
											<ul class="list-group " id="search-options-list" style="display:none;z-index:1000">
												<option value="Mob" class="list-group-item">SamSung</option>
												<option value="Mob" class="list-group-item">Nokia</option>
												<option value="Mob" class="list-group-item">Sony</option>    
											</ul>	
										<button class="btn-search"><i class="lnr lnr-magnifier"></i></button>
                                </form>
                            </div>
                        </div>
                        <!-- Categorie Search Box End Here -->
                        <!-- Cart Box Start Here -->
                        <div class="col-lg-4 col-md-12">
								<div class="cart-box mt-all-30">
								<ul class="d-flex justify-content-lg-end justify-content-center align-items-center">
										<li><a href="{{route('ecom.product.cart-items-view')}}"><i class="lnr lnr-cart"></i><span class="my-cart"><span class="total-pro cart_total_quatity"></span><span>cart</span></span></a>
											<ul class="ht-dropdown cart-box-width cart-block-list">
												
											</ul>
										</li>
										<li><a href="#"><i class="lnr lnr-heart"></i><span class="my-cart"><span>Wish</span><span>list (0)</span></span></a>
										</li>
										<li><a href="#"><i class="lnr lnr-user"></i><span class="my-cart"><span> <strong>Sign in</strong> Or</span><span> Join My Site</span></span></a>

										</li>
								    </div>
                               </ul>
                            </div>
                        </div>
						 
                        <!-- Cart Box End Here -->
                    </div>
                    <!-- Row End -->
                </div>
                <!-- Container End -->
            </div>
            <!-- Header Middle End Here -->
            <!-- Header Bottom Start Here -->
            <div class="header-bottom  header-sticky">
                <div class="container">
                    <div class="row align-items-center">
                         <div class="col-xl-3 col-lg-4 col-md-6 vertical-menu d-none d-lg-block">
                            <span class="categorie-title">Shop by Categories</span>
                        </div>
                        <div class="col-xl-9 col-lg-8 col-md-12 ">
                            <nav class="d-none d-lg-block">
                                <ul class="header-bottom-list d-flex">
                                    <li class="active"><a href="index.html">home<i class="fa fa-angle-down"></i></a></li>
                                    <li><a href="shop.html">shop<i class="fa fa-angle-down"></i></a>
                                        <!-- Home Version Dropdown Start -->
                                        <ul class="ht-dropdown dropdown-style-two">
                                            <li><a href="product.html">product details</a></li>
                                            <li><a href="compare.html">compare</a></li>
                                            <li><a href="cart.html">cart</a></li>
                                            <li><a href="checkout.html">checkout</a></li>
                                            <li><a href="wishlist.html">wishlist</a></li>
                                        </ul>
                                        <!-- Home Version Dropdown End -->
                                    </li>
                                    <li><a href="blog.html">blog<i class="fa fa-angle-down"></i></a>
                                        <!-- Home Version Dropdown Start -->
                                        <ul class="ht-dropdown dropdown-style-two">
                                            <li><a href="single-blog.html">blog details</a></li>
                                        </ul>
                                        <!-- Home Version Dropdown End -->
                                    </li>
                                    <li><a href="#">pages<i class="fa fa-angle-down"></i></a>
                                        <!-- Home Version Dropdown Start -->
                                        <ul class="ht-dropdown dropdown-style-two">
                                            <li><a href="contact.html">CONTACT US</a></li>
                                            <li><a href="register.html">register</a></li>
                                            <li><a href="login.html">sign in</a></li>
                                            <li><a href="forgot-password.html">forgot password</a></li>
                                            <li><a href="404.html">404</a></li>
                                        </ul>
                                        <!-- Home Version Dropdown End -->
                                    </li>
                                    <li><a href="about.html">About us</a></li>
                                    <li><a href="contact.html">CONTACT US</a></li>
                                </ul>
                            </nav>
                            <div class="mobile-menu d-block d-lg-none">
                                <nav>
                                    <ul>
                                        <li><a href="index.html">home</a>
                                            <!-- Home Version Dropdown Start -->
                                            <ul>
                                                <li><a href="index.html">Home Version 1</a></li>
                                                <li><a href="index-2.html">Home Version 2</a></li>
                                                <li><a href="index-3.html">Home Version 3</a></li>
                                                <li><a href="index-4.html">Home Version 4</a></li>
                                            </ul>
                                            <!-- Home Version Dropdown End -->
                                        </li>
                                        <li><a href="shop.html">shop</a>
                                            <!-- Mobile Menu Dropdown Start -->
                                            <ul>
                                                <li><a href="product.html">product details</a></li>
                                                <li><a href="compare.html">compare</a></li>
                                                <li><a href="cart.html">cart</a></li>
                                                <li><a href="checkout.html">checkout</a></li>
                                                <li><a href="wishlist.html">wishlist</a></li>
                                            </ul>
                                            <!-- Mobile Menu Dropdown End -->
                                        </li>
                                        <li><a href="blog.html">Blog</a>
                                            <!-- Mobile Menu Dropdown Start -->
                                            <ul>
                                                <li><a href="single-blog.html">blog details</a></li>
                                            </ul>
                                            <!-- Mobile Menu Dropdown End -->
                                        </li>
                                        <li><a href="#">pages</a>
                                            <!-- Mobile Menu Dropdown Start -->
                                            <ul>
                                                <li><a href="register.html">register</a></li>
                                                <li><a href="login.html">sign in</a></li>
                                                <li><a href="forgot-password.html">forgot password</a></li>
                                                <li><a href="404.html">404</a></li>
                                            </ul>
                                            <!-- Mobile Menu Dropdown End -->
                                        </li>
                                        <li><a href="about.html">about us</a></li>
                                        <li><a href="contact.html">CONTACT US</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <!-- Row End -->
                </div>
                <!-- Container End -->
            </div>
            <!-- Header Bottom End Here -->
            <!-- Mobile Vertical Menu Start Here -->
            <div class="container d-block d-lg-none">
                <div class="vertical-menu mt-30">
                    <span class="categorie-title mobile-categorei-menu">Shop by Categories </span>
                    <nav>
                        <div id="cate-mobile-toggle" class="category-menu sidebar-menu sidbar-style mobile-categorei-menu-list menu-hidden ">
                            <ul>
                                <li class="has-sub"><a href="#">Automotive & Motorcycle </a>
                                    <ul class="category-sub">
                                        <li class="has-sub"><a href="shop.html">Office chair</a>
                                            <ul class="category-sub">
                                                <li><a href="shop.html">Bibendum Cursus</a></li>
                                                <li><a href="shop.html">Dignissim Turpis</a></li>
                                                <li><a href="shop.html">Dining room</a></li>
                                                <li><a href="shop.html">Dining room</a></li>
                                            </ul>
                                        </li>
                                        <li class="has-sub"><a href="shop.html">Purus Lacus</a>
                                            <ul class="category-sub">
                                                <li><a href="shop.html">Magna Pellentesq</a></li>
                                                <li><a href="shop.html">Molestie Tortor</a></li>
                                                <li><a href="shop.html">Vehicula Element</a></li>
                                                <li><a href="shop.html">Sagittis Blandit</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="shop.html">gps accessories</a></li>
                                        <li><a href="shop.html">Microphones</a></li>
                                        <li><a href="shop.html">Wireless Transmitters</a></li>
                                    </ul>
                                    <!-- category submenu end-->
                                </li>
                                <li class="has-sub"><a href="#">Sports & Outdoors</a>
                                    <ul class="category-sub">
                                        <li class="menu-tile">Cameras</li>
                                        <li><a href="shop.html">Cords and Cables</a></li>
                                        <li><a href="shop.html">gps accessories</a></li>
                                        <li><a href="shop.html">Microphones</a></li>
                                        <li><a href="shop.html">Wireless Transmitters</a></li>
                                    </ul>
                                    <!-- category submenu end-->
                                </li>
                                <li class="has-sub"><a href="#">Home & Kitchen</a>
                                    <ul class="category-sub">
                                        <li><a href="shop.html">kithen one</a></li>
                                        <li><a href="shop.html">kithen two</a></li>
                                        <li><a href="shop.html">kithen three</a></li>
                                        <li><a href="shop.html">kithen four</a></li>
                                    </ul>
                                    <!-- category submenu end-->
                                </li>
                                <li class="has-sub"><a href="#">Phones & Tablets</a>
                                    <ul class="category-sub">
                                        <li><a href="shop.html">phone one</a></li>
                                        <li><a href="shop.html">Tablet two</a></li>
                                        <li><a href="shop.html">Tablet three</a></li>
                                        <li><a href="shop.html">phone four</a></li>
                                    </ul>
                                    <!-- category submenu end-->
                                </li>
                                <li class="has-sub"><a href="#">TV & Video</a>
                                    <ul class="category-sub">
                                        <li><a href="shop.html">smart tv</a></li>
                                        <li><a href="shop.html">real video</a></li>
                                        <li><a href="shop.html">Microphones</a></li>
                                        <li><a href="shop.html">Wireless Transmitters</a></li>
                                    </ul>
                                    <!-- category submenu end-->
                                </li>
                                <li><a href="#">Beauty</a> </li>
                                <li><a href="#">Sport & tourisim</a></li>
                                <li><a href="#">Meat & Seafood</a></li>
                            </ul>
                        </div>
                        <!-- category-menu-end -->
                    </nav>
                </div>
            </div>
            <!-- Mobile Vertical Menu Start End -->
        </header>
		<div class="main-page-banner home-3">
            <div class="container">
                <div class="row">
                    <!-- Vertical Menu Start Here -->
                    <div class="col-xl-3 col-lg-4 d-none d-lg-block">
                          @include('shopping.common.category_navication')
                    </div>
                    <!-- Vertical Menu End Here -->
                </div>
                <!-- Row End -->
            </div>
            <!-- Container End -->
        </div>