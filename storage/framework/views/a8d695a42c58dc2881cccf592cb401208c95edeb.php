<footer class="off-white-bg2 pt-50 bdr-top pt-sm-55">
            <!-- Footer Top Start -->
            <div class="footer-top">
                <div class="container">
                    <!-- Signup Newsletter Start -->

                    <!-- Signup-Newsletter End -->
                    <div class="row">
                        <!-- Single Footer Start -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="single-footer mb-sm-40">
                                <h3 class="footer-title">POLICY</h3>
                                <div class="footer-content">
                                    <ul class="footer-list">
                                        <li><a href="return-policy.html">Return Policy</a></li>
                                        <li><a href="privacy-policy.html">Privacy Policy</a></li>
                                        <li><a href="terms-of-sale.html">Terms of Sale</a></li>
                                        <li><a href="terms-of-use.html">Terms of Use</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Single Footer Start -->
                        <!-- Single Footer Start -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="single-footer mb-sm-40">
                                <h3 class="footer-title">COMPANY</h3>
                                <div class="footer-content">
                                    <ul class="footer-list">
                                        <li><a href="about.html">About Virob</a></li>
                                        <li><a href="careers.html">Careers</a></li>
                                        <li><a href="https://virob.com/affiliate" target="_blank">Be an Affiliate</a></li>
                                        <li><a href="https://paygyft.com/merchant" target="_blank">Sell on Virob</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Single Footer Start -->
                        <!-- Single Footer Start -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="single-footer mb-sm-40">
                                <h3 class="footer-title">HELP</h3>
                                <div class="footer-content">
                                    <ul class="footer-list">
                                        <li><a href="#">FAQs </a></li>
                                        <li><a href="shipping-and-delivery.html">Shipping & Delivery</a></li>
                                        <li><a href="#">Help Center</a></li>
                                        <li><a href="contact.html">CONTACT US</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Single Footer Start -->
                        <!-- Single Footer Start -->
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="single-footer mb-sm-40">
                                <h3 class="footer-title">CONTACT US</h3>
                                <div class="footer-content">
                                    <ul class="footer-list address-content">
                                        <li><i class="lnr lnr-map-marker"></i> Address: 4th Floor, No. 13, Paripoorna Layout, Phase 3, Yelahanka, Bangalore - 560064</li>
                                        <li><i class="lnr lnr-envelope"></i><a href="#"> mail Us: info@virob.com </a></li>
                                        <li>
                                            <i class="lnr lnr-phone-handset"></i> Phone: +91 80 29791919
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                          </div>

                    <!-- Row End -->
                </div>
                <!-- Container End -->
            </div>
            <div class="bdr-top ptb-20 ">
                <div class="container">
                <div class="row">
              <div class="col-lg-8 col-md-12 col-sm-12">
            <div class="payment">
              <p>Payment</p>
                <a href="#"><img class="img" src="img/payment/payment-method.svg" alt=""></a>
            </div>
          </div>
          <div class="col-lg-4 col-md-12 col-sm-12">
                  <div class="footer-middle-content">
                      <p>Connect</p>
                          <ul class="social-footer">
                              <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                              <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                              <li><a href="#"><i class="fa fa-google-plus-official"></i></a></li>
                              <li><a href="#"><i class="fa fa-youtube-play"></i></a></li>
                              <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                              <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                          </ul>
                  </div>
          </div></div></div></div>
            <!-- Footer Top End -->
            <!-- Footer Bottom Start -->
            <div class="footer-bottom bdr-top ptb-15">
                <div class="container">

                     <div class="copyright-text text-center">
                        <p>Copyright © 2019 <a target="_blank" href="#">Virob</a> All Rights Reserved.</p>
                     </div>
                </div>
                <!-- Container End -->
            </div>
            <!-- Footer Bottom End -->
        </footer>
        <!-- Footer Area End Here -->
		 <div class="modal fade" id="login-model">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Modal body -->
                        <div class="modal-body">
                         <div class="cont">
                
                            <div class="login-panel">   

               <form method="post" id="loginfrm" action="<?php echo e(route('ecom.checklogin')); ?>">                                    
                              <h2 class="title">Login</h2>
                              <div class="input-container">
                                <input <?php echo build_attribute($validiate['lfields']['username']['attr']); ?> id="username" name="username">
                                <label for="#{label}">Email address/Mobile No</label>
                              </div>
                              <div class="input-container">
                                <input <?php echo build_attribute($validiate['lfields']['password']['attr']); ?> type="password" id="password" name="password">
                                <label for="#{label}">Password</label>
                              </div>

                              <button type="submit" class="btn">Login</button>
                              <div class="text-center p-t-12">
                           <span class="txt1">
                          Forgot Password?
                          </span>
                          <a class="txt2 forgot-pass" href="#">
                          Click Here
                          </a>
                          </div>  
                 </form>                          
                         </div>
                



                            <div class="forgot-panel">
                              <h2 class="title">Forgot your password?</h2>
                              <div class="input-container">
                                <input type="#{type}" id="#{label}" required/>
                                <label for="#{label}">Email address/Mobile No</label>
                              </div>
                              <button type="button" class="btn">Reset Password</button>
                              <div class="text-center p-t-12">

                          <a class="txt2 back-login" href="#">
                          Back to Login
                          </a>
                          </div>
                            </div>
                            <div class="sub-cont">
                              <div class="img">

                                <div class="img__text m--up">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h2>NEW HERE?</h2>
                                  <ul><li>Access account and manage orders</li>
                                    <li>Manage cancellations & returns.</li>
                                      <li>Get access to Wishlist and Recommendations</li></ul>
                                </div>
                                <div class="img__text m--in">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h2>Welcome Back!</h2>
                                  <p>If you have an account with us, please login. We've missed you!</p>
                                </div>
                                <div class="img__btn">
                                  <span class="m--up">Sign Up</span>
                                  <span class="m--in">Login</span>
                                </div>
                              </div>





                              <div class="reg-panel">

                     <form  method="post" id="signupfrm" action="<?php echo e(route('ecom.sign_up_save')); ?>" >
                                <h2 class="title">Signup</h2>
                                <div class="input-container">
                                  <input <?php echo build_attribute($validiate['regfields']['full_name']['attr']); ?> type="text" id="full_name" name="full_name">
                                  <label for="#{label}">FullName</label>
                                </div>
                               
                                <div class="input-container">
                                <?php foreach($validiate['countries'] as $key=>$con): ?>
                                <?php if($con->iso2==$validiate['ip_country']): ?>
                               <!--  <span  class="input-group-addon"><img src="<?php echo e($con->img_url); ?>" id="f_img"><label id='phonecode'><?php echo e($con->phonecode); ?></label></span> -->
                                <input  type="text" id="mob_number" name="mob_number" onkeypress="return isNumberKey(event)" >   
                                <label for="#{label}">Phone Number</label>                                        
                                <?php endif; ?>
                                <?php endforeach; ?>
                                </div>
                                <div class="input-container">
                                  <select <?php echo build_attribute($validiate['regfields']['country']['attr']); ?> name="country" id="country" placeholder="Select Your Country">
                                    <?php foreach($validiate['countries'] as $key=>$con): ?>
                                      <?php if($con->iso2==$validiate['ip_country']): ?>
                                      <option value="<?php echo e($con->country_id); ?>" selected="selected" data_url="<?php echo e($con->img_url); ?>" data_code="<?php echo e($con->phonecode); ?>"><?php echo e($con->country_name); ?>

                                     </option>
                                    <?php else: ?>
                                     <option value="<?php echo e($con->country_id); ?>" " data_url="<?php echo e($con->img_url); ?>" data_code="<?php echo e($con->phonecode); ?>"><?php echo e($con->country_name); ?>

                                   </option>
                                   <?php endif; ?>
                                   <?php endforeach; ?>                                                          
                                  </select>
                                </div>
                                <div class="input-container">
                                  <input <?php echo build_attribute($validiate['regfields']['email']['attr']); ?> type="text"  id="email" name="email">
                                  <label for="#{label}">Email Address</label>
                                </div>
                                <div class="input-container mar-10">
                                  <input type="password" id="password" name="password" >
                                  <label for="#{label}">Password</label>
                                </div>
                                <div class="txt1">
                                  <input <?php echo build_attribute($validiate['regfields']['temsandcondition']['attr']); ?>  type="checkbox" id="temsandcondition" name="temsandcondition" placeholder="" data-err-msg-to="#err_msg_td"> I want to receive exclusive offers and promotions from virob.
                                By Clicking REGISTER Your accept our User Agreement and <span class="txt2"><a href="#"> privacy policy</a></span>
                              </div>
                                <span id="err_msg_td"></span>
                              

                                <button type="submit" class="btn" id="register" >Register</button>
                                <div class="text-center p-t-12">
                            </div>
              </form>

             
              
               <form  method="post"  id="varification"  class="form-horizontal"  action="<?php echo e(route('ecom.sign_up_varification')); ?>" style="display:none">                <h6>Enter OTP</h6>
          
                                <div class="input-container">
                                   <input name="code" id="code">
                                  <label for="#{label}">Enter OTP</label>                           
                                </div>
                                

                                   <p><a class="link" href="#" ctr_url="<?php echo e(route('ecom.sign_up_resend_otp')); ?>" id="resendotp" >Resend OTP</a></p>
                                 <button  class="btn btn-sm btn-info" id="varify_otp">Varify OTP</button>
                                 
              
               </form>
              


                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div> 




   