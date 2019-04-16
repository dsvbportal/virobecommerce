<div class="popup_wrapper">
            <div class="test">
                <span class="popup_off">Close</span>
                <div class="subscribe_area text-center mt-60">
                    <h2>Newsletter</h2>
                    <p>Subscribe to the Truemart mailing list to receive updates on new arrivals, special offers and other discount information.</p>
                    <div class="subscribe-form-group">
                       <form action="#">
							 
                            <input autocomplete="off" type="text" name="message" id="message" placeholder="Enter your email address">
                            <button type="submit">subscribe</button>
                        </form>
                    </div>
                    <div class="subscribe-bottom mt-15">
                        <input type="checkbox" id="newsletter-permission">
                        <label for="newsletter-permission">Don't show this popup again</label>
                    </div>
                </div>
            </div>
        </div>
        <!-- Newsletter Popup End -->

        <!-- Main Header Area Start Here -->
        <header>
            <!-- Header Top Start Here -->
            <div class="header-top-area">
                <div class="container">
                    <!-- Header Top Start -->
                    <div class="header-top">
                        <ul class="top_menu">
                            <li><a href="#">sell on Virob</a></li>
                            <li><a href="#">CUSTOMER CARE</a></li>
                            <li><a href="#">Track my order</a></li>
                              <?php if(isset($logged_userinfo) && !empty($logged_userinfo)): ?>
                            <li><a href="#"><?php echo e(isset($logged_userinfo->full_name) ? $logged_userinfo->full_name : ''); ?></a></li>
                            <?php else: ?>
                            <li data-toggle="modal" data-target="#login-model">Login / sign up</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <!-- Header Top End -->
                </div>
                <!-- Container End -->
            </div>
            <!-- Header Top End Here -->
            <!-- Header Middle Start Here -->
            <div class="header-middle ptb-15  header-sticky">
                <div class="container">
                    <div class="row align-items-center no-gutters">
                        <div class="col-lg-2 col-md-12">
                            <div class="logo mb-all-30">
                                <a class="navbar-brand" href="<?php echo e(url('/')); ?>"><img src="<?php echo e(asset('resources/assets/themes/shopping/img/logo/logo-white.png')); ?>" class="white-logo" alt="">
                                  <img src="<?php echo e(asset('resources/assets/themes/shopping/img/logo/logo.png')); ?>" class="logo" alt=""></a>
                            </div>
                        </div>
                        <!-- Categorie Search Box Start Here -->
                        <div class="col-lg-9 col-md-8 ml-auto mr-auto col-12">
                           <div class="categorie-search-box">
                                <form class="form-inline" id="search-products" data-search="<?php echo e(route('ecom.product.search')); ?>">
										<div class="form-group form-category">
											<select class="bootstrap-select select-category" id="searchCategory" data-category="<?php echo e(isset($category) ? $category : ''); ?>"> 
											</select>
										</div>
										<input type="text" name="search_term" id="searchTerm" value="<?php echo e(isset($searchtxt) ? $searchtxt : ''); ?>" autocomplete="off" placeholder="Search for products, brands and more...">
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
                        <div class="col-lg-1 col-md-12">
                            <div class="cart-box mt-all-30">
                                <ul class="d-flex justify-content-lg-end justify-content-center align-items-center">
                                    <li><a href="#"><i class="lnr lnr-cart"></i><span class="my-cart"><span class="total-pro cart_total_quatity"></span></span></a>
                                        <ul class="ht-dropdown cart-box-width cart-block-list">
                                        </ul>
                                    </li>
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
            <!-- Header Bottom End Here -->
		</header>
		<div class="main-page-banner pb-20 off-white-bg">

                    <!-- Slider Area Start Here -->
                    <div class="slider_box">
                        <div class="slider-wrapper theme-default">
                          <!-- Vertical Menu Start Here -->
                          <div class="col-xl-12 col-lg-12 d-none d-lg-block home-menu">
                            <div class="container">
                              <div class="col-xl-3 col-lg-4">
                            <div class="vertical-menu d-none d-lg-block">
                               <span class="categorie-title">Shop by Categories</span>
                            </div>
                              <?php echo $__env->make('shopping.common.category_navication', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                          </div>
                        </div></div>
                          <!-- Vertical Menu End Here -->
                            <!-- Slider Background  Image Start-->
                            <div id="slider" class="nivoSlider">
                                <a href="shop.html"><img src="<?php echo e(asset('resources/assets/themes/shopping/img/slider/4.jpg')); ?>" data-thumb="img/slider/1.jpg" alt="" title="#htmlcaption" /></a>
                                <a href="shop.html"><img src="<?php echo e(asset('resources/assets/themes/shopping/img/slider/3.jpg')); ?>" data-thumb="img/slider/2.jpg" alt="" title="#htmlcaption2" /></a>
                            </div>
                            <!-- Slider Background  Image Start-->
                        </div>
                    </div>
                    <!-- Slider Area End Here -->
        </div>