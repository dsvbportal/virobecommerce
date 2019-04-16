<?php $__env->startSection('home_page_header'); ?>
	<?php echo $__env->make('shopping.common.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
	<div class="breadcrumb-area mt-30">
		<div class="container">
			<div class="breadcrumb">
				
			</div>
		</div>
	</div>
  <div class="main-shop-page pt-100 pb-100 ptb-sm-60">
            <div class="container">
                <!-- Row End -->
                <div class="row">
                    <!-- Sidebar Shopping Option Start -->
                    <div class="col-lg-3 order-2 order-lg-1">
						<div class="electronics mb-40">
								<h3 class="sidebar-title">Categories</h3>
								<div id="shop-cate-toggle" class="category-menu sidebar-menu sidbar-style">
									<ul class="categories-list">
									</ul>
								</div>
						</div>
					<form id="filter-form">
                        <div class="sidebar" id="filters">
                            <!--<div class="electronics mb-40">
                                <h3 class="sidebar-title">Electronics</h3>
                                <div id="shop-cate-toggle" class="category-menu sidebar-menu sidbar-style">
                                    <ul>
                                        <li class="has-sub"><a href="#">Camera</a>
                                            <ul class="category-sub">
                                                <li><a href="shop.html">Cords and Cables</a></li>
                                                <li><a href="shop.html">gps accessories</a></li>
                                                <li><a href="shop.html">Microphones</a></li>
                                                <li><a href="shop.html">Wireless Transmitters</a></li>
                                            </ul>
                                          
                                        </li>
                                        <li class="has-sub"><a href="#">gamepad</a>
                                            <ul class="category-sub">
                                                <li><a href="shop.html">cube lifestyle hd</a></li>
                                                <li><a href="shop.html">gopro hero4</a></li>
                                                <li><a href="shop.html">bhandycam cx405ags</a></li>
                                                <li><a href="shop.html">vixia hf r600</a></li>
                                            </ul>
                                         
                                        </li>
                                        <li class="has-sub"><a href="#">Digital Cameras</a>
                                            <ul class="category-sub">
                                                <li><a href="shop.html">Gold eye</a></li>
                                                <li><a href="shop.html">Questek</a></li>
                                                <li><a href="shop.html">Snm</a></li>
                                                <li><a href="shop.html">vantech</a></li>
                                            </ul>
                                          
                                        </li>
                                        <li class="has-sub"><a href="#">Virtual Reality</a>
                                            <ul class="category-sub">
                                                <li><a href="shop.html">Samsung</a></li>
                                                <li><a href="shop.html">Toshiba</a></li>
                                                <li><a href="shop.html">Transcend</a></li>
                                                <li><a href="shop.html">Sandisk</a></li>
                                            </ul>
                                           
                                        </li>
                                    </ul>
                                </div>
                            </div>-->
							
                            <!-- Sidebar Electronics Categorie End -->
                            <!-- Price Filter Options Start -->
                            <div class="search-filter mb-40">
                                <h3 class="sidebar-title">filter by price</h3>
                                <form action="#" class="sidbar-style">
                                    <div id="slider-range"></div>
                                    <input type="text" id="amount" class="amount-range" readonly>
                                </form>
                            </div>
                            <div class="col-img">
                                <a href="shop.html"><img src="img/banner/banner-sidebar.jpg" alt="slider-banner"></a>
                            </div>
                            <!-- Single Banner End -->
                        </div>
					</form>	
                    </div>
                    <!-- Sidebar Shopping Option End -->
                    <!-- Product Categorie List Start -->
                    <div class="col-lg-9 order-1 order-lg-2">
                        <!-- Grid & List View Start -->
                        <div class="grid-list-top border-default universal-padding d-md-flex justify-content-md-between align-items-center mb-30">
                            <div class="grid-list-view  mb-sm-15">
                                <ul class="nav tabs-area d-flex align-items-center">
                                    <li><a class="active" data-toggle="tab" href="#grid-view"><i class="fa fa-th"></i></a></li>
                                    <li><a data-toggle="tab" href="#list-view"><i class="fa fa-list-ul"></i></a></li>
                                </ul>
                            </div>
                            <!-- Toolbar Short Area Start -->
                            <div class="main-toolbar-sorter clearfix">
                                <div class="toolbar-sorter d-flex align-items-center">
                                    <label>Sort By:</label>
                                    <select class="sorter wide" id="sortby" name="sortby">
                                        <option value="">Newest</option>
                                        <option value="PRICE_LOW_TO_HIGH" selected>Price low to heigh</option>
                                        <option value="PRICE_HIGH_TO_LOW">Price heigh to low</option>
                                    </select>
                                </div>
                            </div>
					
                            <!-- Toolbar Short Area End -->
                            <!-- Toolbar Short Area Start -->
                            <div class="main-toolbar-sorter clearfix">
                                <div class="toolbar-sorter d-flex align-items-center">
                                    <label>Show:</label>
                                    <select class="sorter wide">
                                        <option value="12">12</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="75">75</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Toolbar Short Area End -->
                        </div>
                        <!-- Grid & List View End -->
                        <div class="main-categorie mb-all-40">
                            <!-- Grid & List Main Area End -->
                            <div class="tab-content fix">
                                <div id="grid-view" class="tab-pane fade show active">
                                    <div id="view-product-list" class="row product-list">
                                       
                                    </div>
                                </div>
                                    <!-- Single Product End -->
                                </div>
                                <!-- #list view End -->
                                <div class="pro-pagination">
                                    <ul class="blog-pagination">
                                        <li class="active"><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                                    </ul>
                                    <div class="product-pagination">
                                        <span class="grid-item-list">Showing 1 to 12 of 51 (5 Pages)</span>
                                    </div>
                                </div>
                                <!-- Product Pagination Info -->
                            </div>
                            <!-- Grid & List Main Area End -->
                        </div>
                    </div>
                    <!-- product Categorie List End -->
                </div>
                <!-- Row End -->
            </div>
            <!-- Container End -->
        </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" src="<?php echo e(asset('js/providers/ecom/product/browse_products.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shopping.layout.home_layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>