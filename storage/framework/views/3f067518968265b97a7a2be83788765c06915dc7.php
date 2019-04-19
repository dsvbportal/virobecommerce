<?php $__env->startSection('home_page_header'); ?>
    <?php echo $__env->make('shopping.common.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="main-shop-page  mt-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6" id="left_column">
                    <div class="inner-desc">
                        <h4>My Account</h4>
                        <ul>
                            <li><a href="<?php echo e(route('ecom.account.profile')); ?>"><span>Personal Information</span></a></li>
                            <li><a href="<?php echo e(route('ecom.account.security')); ?>"><span>Security</span></a></li>
                            <li><a href="<?php echo e(route('ecom.logout')); ?>" id="logout"><span>Logout</span></a></li>

                        </ul>
                        <hr />
                        <h4>ORDERS</h4>
                        <ul>
                            <li ><a href="<?php echo e(route('ecom.account.my-orders')); ?>"><span> My Orders</span></a></li>
                        </ul>
                        <hr />
                        <h4>MY STUFF</h4>
                        <ul>
                            <li><a href="<?php echo e(route('ecom.product.wishlist')); ?>"><span>My Wishlist</span></a></li>
                        </ul>
                        <hr />
                    </div>
                </div>
                <div class="col-lg-9 col-md-3 col-sm-6">
                    <ul class="main-thumb-desc nav tabs-area">
                        <li ><a class="active" data-toggle="pill" href="#home">Change Password</a></li>
                        <li><a data-toggle="pill" href="#menu1"  id='change_mobile_tab' ctr_url="<?php echo e(route('ecom.account.change-mobile')); ?>">Change Mobile</a></li>
                        <li><a data-toggle="pill" href="#menu2" id="change_email_tab" ctr_url="<?php echo e(route('ecom.account.change_email')); ?>">Change Email</a></li>
                    </ul>
                    <div class="tab-content thumb-content border-default">
                        <div id="home" class="tab-pane fade show active">
                            <div class="contentpanel">
                                <div class="panel panel-default">
                                    <div class="panel-body">

                                        <div class="col-md-12 mt-30">


                                            <p style="margin-left:36px;"><i class="fa fa-info-circle"></i> It's a good idea to use a strong password that you don't use elsewhere.</p><br>
                                            <form class="form-horizontal" id="change_pwdfrm" action="<?php echo e(route('ecom.account.update-pwd')); ?>" method="post" autocomplete="off">
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Current Password <span class="red">*</span></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <input <?php echo build_attribute($cpfields ['current_password']['attr']); ?> type="password" id="current_pwd" placeholder="Current Password" class="form-control" value="" data-err-msg-to="#current_pwd_err" onkeypress="return RestrictSpace(event)">
                                                            <span class="input-group-addon pwdHS" data-target="#current_pwd"><i class="fa fa-eye-slash"></i></span>
                                                        </div>
                                                        <span id="current_pwd_err"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">New Password <span class="red">*</span></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <input <?php echo build_attribute($cpfields['password']['attr']); ?> type="password" id="new_password" placeholder="New Password" class="form-control" value="" data-err-msg-to="#new_password_err" onkeypress="return RestrictSpace(event)">
                                                            <span class="input-group-addon pwdHS" data-target="#new_password"><i class="fa fa-eye-slash"></i></span>
                                                        </div>
                                                        <span id="new_password_err"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label" for="new_password_confirmation">Confirm Password <span class="red">*</span></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <input <?php echo build_attribute($cpfields['conf_password']['attr']); ?> type="password" id="conf_password" placeholder="Confirm Password" class="form-control" value="" data-err-msg-to="#conf_password_err" onkeypress="return RestrictSpace(event)">
                                                            <span class="input-group-addon pwdHS" data-target="#conf_password"><i class="fa fa-eye-slash"></i></span>
                                                        </div>
                                                        <span id="conf_password_err"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3"></label>
                                                    <div class="col-sm-3 fieldgroup">
                                                        <button type="submit" class="btn btn-primary" id="submit" name="submit"><i class="fa fa-save"></i> Update</button>
                                                    </div>
                                                </div>
                                            </form>


                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div id="menu1" class="tab-pane fade">
                            <div class="contentpanel">
                                <div class="panel panel-default">
                                    <div class="panel-body">

                                        <div style="margin: 20px;">

                                            <label class=" control-label" ><strong style="font-size: 18px" > Update Your Mobile</strong></label>
                                        </div>


                                        <div id='mob_div'>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="menu2" class="tab-pane fade">
                            <div class="contentpanel">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div style="margin: 20px;">
                                            <label class=" control-label" ><strong style="font-size: 18px" > Update Your Email</strong></label>
                                        </div>
                                        <div id='email_div'>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('shopping.common.newsletter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript" src="<?php echo e(asset('js/providers/ecom/account/profile.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shopping.layout.home_layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>