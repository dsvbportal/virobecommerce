<?php $__env->startSection('home_page_header'); ?>
	<?php echo $__env->make('shopping.common.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
		<!-- Breadcrumb Start -->
<div class="breadcrumb-area mt-30">
	<div class="container">
		<div class="breadcrumb">
			<ul class="d-flex align-items-center">
				<li><a href="#">Home</a></li>
				<li class="active"><a href="">My Orders</a></li>
			</ul>
		</div>

	</div>
	<!-- Container End -->
</div>
<!-- Breadcrumb End -->
<!-- Wish List Start -->
<div class="cart-main-area wish-list ptb-100 ptb-sm-60">
	<div class="container">
		<br>

<?php /*
			<div class="cart_quatity_error"></div>
*/ ?>
			<!-- Left colunm -->
			<!-- ./left colunm -->
			<!-- Center colunm-->
			<div class="col-xs-12">
				<!-- page heading-->

				<br/>
				<div id="list_div">
					<h2 class="page-heading">
						<span class="page-heading-title2">My Orders</span>
					</h2>
					<div id="search_div">
						<form id="order_listfrm" class="form form-bordered" action="http://localhost/dsvb_affiliate/admin/finance/fund-transfer-history" method="get" autocomplete="off">

								<div class="row">
								<div class="col-md-3 ">
								<input type="text" id="search_term" name="search_term" class="form-control" placeholder="Search Term" value="" data-value="">
							</div>
								<div class="input-group col-md-5">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input class="form-control datepicker from_date" type="text" id="from" name="from" placeholder="From">
									<span class="input-group-addon">-</span>
									<input class="form-control datepicker to_date" type="text" id="to" name="to" placeholder="To">
								</div>
							<div class="col-md-3 searchtxt">
									<button type="button" id="search" class="btn btn-sm btn-primary btnsrch"><i class="fa fa-search"></i> Search</button>&nbsp;
									<button type="button" id="reset" class="btn btn-sm btn-warning btnsrch"><i class="fa fa-repeat"></i> Reset</button>
							</div>
							</div>


						</form>
						<!--div class="col-sm-6">
                            <div class="input-group">
                                <input id="phrase" class="form-control" placeholder="Type Order Code Here" name="phrase" type="text" data-value="" value="">
                                <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i></span>
                                <input class="form-control from_date" type="text" id="from" name="from" placeholder="From">
                                <span class="input-group-addon">-</span>
                                <input class="form-control to_date" type="text" id="to" name="to" placeholder="To">
                            </div>
                        </div-->
					</div>
					<br/>
					<div class="order_details_row">

						<table id="data_table_order_list" class="table table-bordered table-wishlist data_table_order_list" style="width: 100%">
							<thead>
							<tr>
								<th>Order</th>
								<th>Total Amount</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody id="ordersRow">
							</tbody>
						</table>
					</div>
				</div>

				<div id="order_details_row" style="display: none">
					<h2 class="page-heading">
						<span class="page-heading-title2">Order Details</span>
					</h2>

					<div class="box-body" id="order-details" data-order_code="">

						<div class="col-md-12 ">


							<a  class="pull-right close_detail" id="" style=""><i class="fa fa-close" style="font-size:20px" ></i></a>

						</div>
						<div class="col-md-12">
							<div class="col-md-8">
								<div id="ord-details">

								</div>
								<hr>

								<div id="shipping-address">

								</div>
								<hr>

								<div id="pay-details">

								</div>
								<div id="item-details">

								</div>
								<div id="net-details">

								</div>
								<hr>
								<div id="query">
									<p><strong>In case of any queries/clarification</strong>
									</p>
									<a href="#" target="_blank" class="faq"><span>Contact Us</span></a>
								</div>
								<?php /*<div id="rate_div">
									<div class="container">
										<div class="row">
											<div class="col-lg-12 abc">
												<div class="star-rating">
													<span class="fa fa-star" data-rating="1"></span>
													<span class="fa fa-star" data-rating="2"></span>
													<span class="fa fa-star" data-rating="3"></span>
													<span class="fa fa-star" data-rating="4"></span>
													<span class="fa fa-star-o" data-rating="5"></span>
													<input type="text" name="whatever1" class="rating-value" value="4">
												</div>
											</div>
										</div>

									</div>
								</div>*/ ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ./ Center colunm -->
	</div>
</div>


<?php echo $__env->make('shopping.common.datatable_js', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
	<script src="<?php echo e(asset('validate/lang/order_list')); ?>" charset="utf-8"></script>
	<script type="text/javascript" src="<?php echo e(asset('resources/assets/themes/ecom/lib/fxss-rate/rate.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo e(asset('js/providers/ecom/account/order.js')); ?>"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('shopping.layout.home_layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>