<!DOCTYPE html>
<html lang="en">
<head>
<!-- ============ META =============== -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Virob</title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="robots" content="">
<meta name="author" content="">
<!-- ============ FAVICON =============== -->
<link rel="icon" href="{{asset("resources/assets/static/images/favicon/favicon.png")}}">
<!-- ============ GOOGLE FONT =============== -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:600,600i,700" rel="stylesheet">
<!-- ============ CSS =============== -->
<!-- Bootstrap -->
<link href="{{asset("resources/assets/static/css/bootstrap.min.css")}}" rel="stylesheet">
<!-- Font Awesome -->
<link href="{{asset("resources/assets/static/fonts/font-awesome/css/font-awesome.min.css")}}" rel="stylesheet">
<!-- Linearicons -->
<link href="{{asset("resources/assets/static/fonts/linearicons/css/linearicons.css")}}" rel="stylesheet">
<!-- Owl Carousel -->
<link href="{{asset("resources/assets/static/css/owl.carousel.min.css")}}" rel="stylesheet">
<link href="{{asset("resources/assets/static/css/owl.theme.min.css")}}" rel="stylesheet">
<!-- Magnific popup -->
<link href="{{asset("resources/assets/static/css/magnific-popup.css")}}" rel="stylesheet">
<!-- YTPlayer -->
<link href="{{asset("resources/assets/static/css/jquery.mb.YTPlayer.min.css")}}" rel="stylesheet">
<!-- Vegas Slider -->
<link href="{{asset("resources/assets/static/css/vegas.min.css")}}" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="{{asset("resources/assets/static/css/style.css")}}" rel="stylesheet">
</head>

<body id="body" class="wide-layout">
<!-- ============ PRELOADER =============== 
    <div id="preloader" class="preloader orange">
        <div class="loader">
            <span></span>
        </div>
    </div>--> 
<!-- ============ WRAPPER =============== -->
<div id="pageWrapper" class="page-wrapper t-center t-sm-left">
  <header class="main-header transparent">
    <div class="navbar navbar-default navbar-orange" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
          <a class="navbar-brand" href="#"> <img src="{{asset("resources/assets/static/images/logo.png")}}" alt=""> </a> </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a class="page-scroll" href="#pageWrapper">Home</a></li>
            <li><a class="page-scroll" href="#about">ABOUT US</a></li>
            <li><a class="page-scroll" href="#why-viob">WHY VIROB</a></li>
            <li><a class="page-scroll" href="#how-it-works">HOW IT WORKS</a></li>
            <li><a class="page-scroll" href="#opprtunity">OPPORTUNITY</a></li>
            <li><a class="page-scroll" href="#our-projects">OUR PROJECTS</a></li>
            <li><a class="page-scroll" href="#contact">CONTACT US</a></li>
            <li class="signup"><a class="btn" href="{{url('/affiliate/login')}}">Login</a> </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /.navbar --> 
  </header>
  <main class="main-content">
    <div class="page-container"> 
      <!-- Start Hero Area -->
      <section class="section hero-area t-center" data-bg-img="{{asset("resources/assets/static/images/bg/10.jpg")}}">
        <div class="owl-carousel owl-carousel owl-controls-1" data-nav="true" data-loop="true" data-autoplay="true" data-smart-speed="100" data-autoplay-timeout="10000">
          <div class="item" data-bg-img="{{asset("resources/assets/static/images/bg/10.jpg")}}">
            <div class="overlay bg-overlay alpha-60"></div>
            <div class="hero-container container h-full-screen is-flex flex-items-center">
              <div class="slide-content color-white flex-1 zi-2">
                <h2 class="font-50 t-uppercase mb-20">Register for free as our affiliate partner and make MONEY</h2>
                <ul>
                  <li><i class="fa fa-check"></i> No Investment</li>
                  <li><i class="fa fa-check"></i> Flexible Generous Commission Structure</li>
                  <li><i class="fa fa-check"></i> Virob Influencer Program</li>
                  <li><i class="fa fa-check"></i> Fantastic Technical & Customer Support</li>
                </ul>
                <div class="pt-20"> <a href="{{url('/affiliate/login')}}" class="btn btn-green btn-lg">Get started</a> </div>
              </div>
            </div>
          </div>
          <div class="item" data-bg-img="{{asset("resources/assets/static/images/bg/09.jpg")}}">
            <div class="overlay bg-overlay alpha-60"></div>
            <div class="hero-container container h-full-screen is-flex flex-items-center">
              <div class="slide-content color-white flex-1 zi-2">
                <h2 class="font-50 t-uppercase mb-20">SPREAD THE KNOWLEDGE AND EARN EXCELLENT COMMISSIONS</h2>
                <h5 class="mb-40 font-18 alpha-90">Become an affiliate and maximise your earnings. Promote a product that is always useful, that people always want!</h5>
                <div class="pt-20"> <a href="{{url('/affiliate/login')}}" class="btn btn-green btn-lg">Get started</a> </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- End Hero Area --> 
      <!-- Start About Us Area -->
      <section class="section about-us-area about-us ptb-60" id="about">
        <div class="container">
          <div class="row mb-30">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center mb-40">
              <h2 class="mb-10 font-24 t-uppercase">About Us</h2>
              <span class="dots bg-orange mb-10"></span> </div>
          </div>
          <div class="row row-tb-15">
            <div class="col-md-6"> <img src="{{asset("resources/assets/static/images/about-image.png")}}" width="80%" alt=""/> </div>
            <div class="col-md-6">
              <div class="ptb-10">
                <p class="mb-20 color-mid">Vir-o-b (Virtual Online Business), an eCommerce platform leverages technology to engage savvy shoppers with india's best brands offering unbeatable value,cashback & affiliate rewards in their daily purchases.</p>
				<p class="mb-20 color-mid">Vir-o-b simultaneously introduces new trends in the traditional market place by 'Retargeting' & 'Consumer Engagement' creating  a level playing market place, for online & offline merchants & thereby,enabling merchants with building loyal & lucrative relationship with customers to drive sales.</p>
				<p class="mb-20 color-mid">Our state-of-the-art technology with real time information backed up with proactive CRM enables merchants & customers with ease-of-use & convenience & many pioneering firsts in the industry.</p>				
                <blockquote>
                  <div class="plan-bull">
                    <ul>
                      <li><i class="fa fa-check"></i>INCREASE YOUR ROI BY TEN TIMES! </li>
                      <li><i class="fa fa-check"></i>GET REPEAT CUSTOMERS! </li>
                      <li><i class="fa fa-check"></i>MYSTERY SHOPPING!</li>
                    </ul>
                  </div>
                </blockquote>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section subscribe-area pt-40 pb-20" data-bg-img="{{asset("resources/assets/static/images/bg/20.jpg")}}" style="background-image: url("{{asset("resources/assets/static/images/bg/20.jpg")}})">
                    <div class="overlay bg-overlay alpha-75"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="get-quotes-content text-center color-white row">
                                    <div class="col-lg-7 col-md-8 col-sm-10 col-xs-center">
                                        <h2 class="mb-20">Become an Affiliate Partner Today!</h2>
                                        <div class="dots bg-white alpha-75 mb-20"></div>
                                        <h6 class="mb-40 font-15">Join thousands of others who are earning great commission promoting Virob. To become an affiliate on this platform is easy and free. Earn money promoting local deals to your followers, family and friends!</h6>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
      <!-- End About Us Area --> 
      <!-- Start Why Viob Aera -->
      <section class="section services-area services-two pt-60 pb-80 bg-gray" id="why-viob">
        <div class="container">
          <div class="row mb-30">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center mb-40">
              <h2 class="mb-10 font-24 t-uppercase">WHY VIROB</h2>
              <span class="dots bg-orange mb-10"></span> </div>
          </div>
          <div class="row row-tb-15 row-masnory">
            <div class="col-md-4 col-sm-6 col-xs-12 blue">
              <div class="service-single blue-bg foo">
                <div class="why_virob-icon mb-5"> <img src="{{asset("http://localhost/dsvb_affiliate/resources/assets/static/images/master-of-your-time.svg")}}" alt=""/> </div>
                <div class="service-content pt-5">
                  <h5 class="t-uppercase mb-10">Excellent Commissions</h5>
                  <p>For each sale you make, you will receive a commission which can be up to 75% of the total earnings of the company..</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12 pink">
              <div class="service-single pink-bg foo">
                <div class="why_virob-icon mb-5"> <img src="{{asset("resources/assets/static/images/Passive-income.svg")}}" alt=""/> </div>
                <div class="service-content pt-5">
                  <h5 class="t-uppercase mb-10">Passive income</h5>
                  <p>Make money while you sleep. You can grow a lucrative business without leaving your home and with unlimited sales.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12 orange">
              <div class="service-single orange-bg foo">
                <div class="why_virob-icon mb-5"> <img src="{{asset("resources/assets/static/images/master-of-your-time.svg")}}" alt=""/> </div>
                <div class="service-content pt-5">
                  <h5 class="t-uppercase mb-10">You are the master of your time</h5>
                  <p>It's your chioce how much time you want to spare for this and when</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12 green">
              <div class="service-single green-bg foo">
                <div class="why_virob-icon mb-5"> <img src="{{asset("resources/assets/static/images/Work-from-anywhere.svg")}}" alt=""/> </div>
                <div class="service-content pt-5">
                  <h5 class="t-uppercase mb-10">Work from anywhere</h5>
                  <p>All you need is a smartphone with internet to do wonders. And you will to do great things</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12 yellow">
              <div class="service-single yellow-bg foo">
                <div class="why_virob-icon mb-5"> <img src="{{asset("resources/assets/static/images/earn-extra-money.svg")}}" alt=""/> </div>
                <div class="service-content pt-5">
                  <h5 class="t-uppercase mb-10">You earn extra money</h5>
                  <p>You can make some extra money in your free time or you can bravely take up affiliate marketing as a full time lifestyle or business</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12 purple">
              <div class="service-single purple-bg foo">
                <div class="why_virob-icon mb-5"> <img src="{{asset("resources/assets/static/images/big-brands.svg")}}" alt=""/> </div>
                <div class="service-content pt-5">
                  <h5 class="t-uppercase mb-10">You work with big brands</h5>
                  <p>Ever thought of working for a big brand? Now you can work WITH big brands, as partners. That would make your portfolio look impressive!</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- End HOW IT WORKS? Aera -->
      <section class="section our-team-area our-team-two ptb-60" id="how-it-works">
        <div class="container">
          <div class="row mb-40">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center">
              <h2 class="mb-10 font-24 t-uppercase">HOW IT WORKS?</h2>
              <span class="dots bg-orange mb-10"></span> </div>
          </div>
          <div class="row row-tb-15 how-it-works">
            <ul class="steps">
              <li class="step">
                <div class="step-thumbnail"><i class="fa fa-wpforms" aria-hidden="true"></i> </div>
                <div class="step-info">
                  <h4 class="step-title">Register</h4>
                  <span class="step-desc">Register for free with virob affiliate program</span></div>
              </li>
              <li class="step">
                <div class="step-thumbnail"><i class="fa fa-users" aria-hidden="true"></i> </div>
                <div class="step-info">
                  <h4 class="step-title">Refer</h4>
                  <span class="step-desc">Refer virob to your friends and relatives</span></div>
              </li>
              <li class="step">
                <div class="step-thumbnail"> <i class="fa fa-database" aria-hidden="true"></i> </div>
                <div class="step-info">
                  <h4 class="step-title">Earn</h4>
                  <span class="step-desc">Money on each referral</span></div>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <section class="section fun-facts-area ptb-40 color-white" data-bg-img="{{asset("resources/assets/static/images/bg/19.jpg")}}" style="background-image: url("{{asset("resources/assets/static/images/bg/19.jpg")}})">
        <div class="overlay bg-overlay alpha-60"></div>
        <div class="container">
          <div class="row mb-40">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center">
              <h3 class="mb-10 font-24 t-uppercase">AN OPPORTUNITY UNTHINKABLE!</h3>
            </div>
          </div>
          <div class="row row-tb-30 pt-20">
            <h5 class="t-center">Ready to start earning commissions every month? Join our affiliate program today!</h5>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <div class="counter-box t-center"> <i class="counter-icon fa fa-line-chart font-50 mb-15"></i>
                <h2 class="counter font-30 mb-10">Real-time statistics</h2>
                <h5 class="t-uppercase">and an awesome comprehensive dashboard</h5>
              </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
              <div class="counter-box t-center"> <i class="counter-icon fa fa-credit-card font-50 mb-15"></i>
                <h2 class="counter font-30 mb-10">Weekly and convenient payment</h2>
                <h5 class="t-uppercase">so you can make the most of it</h5>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- End HOW IT WORKS? Area --> 
      <!-- End OPPORTUNITY Aera -->
      <section class="section our-team-area our-team-two ptb-60" id="opprtunity">
        <div class="container">
          <div class="row mb-40">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center">
              <h2 class="mb-10 font-24 t-uppercase">OPPORTUNITY</h2>
              <span class="dots bg-orange mb-10"></span>
              <p class="color-mid">Who can become a virob affiliate partner? Practically anyone!</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="main-services orange_bg"> <img src="{{asset("resources/assets/static/images/main-service1.png")}}" class="width-100" alt="pic">
                <h3>Individuals & Freelancers</h3>
                <p>Refer your friends and family members.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="main-services yellow_bg"> <img src="{{asset("resources/assets/static/images/main-service2.png")}}" class="width-100" alt="pic">
                <h3>E-commerce Consultants</h3>
                <p>Spread the word and start referring now.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="main-services purple_bg"> <img src="{{asset("resources/assets/static/images/main-service3.png")}}" class="width-100" alt="pic">
                <h3>Students</h3>
                <p>Earn while you learn!</p>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="pt-80">
             <div class="col-md-6">
              <img src="{{asset("resources/assets/static/images/img-1.jpg")}}" class="shadow" alt=""/> </div>
           <div class="col-md-6">
            <h3 class="t-uppercase  mb-20 pt-50">Our Marketing Strategy is Affiliate Marketing</h3>
                <p>We have decided to focus our Marketing Strategy on our Affiliate Network, with whom we want to share and grow our success. The reason is simple, it's the most efficient and cost-effective solution for everybody including the end user.</p>
		    </div>
            </div>
             <div class="clearfix"></div>
            <div class="pt-80">
           <div class="col-md-6">
            <h3 class="t-uppercase mb-20 pt-50">ENJOY MORE FREEDOM AND MONEY WITH AFFILIATE MARKETING</h3>
                <p>Spend more time doing what you love. Work on your schedule and where your heart desires. Affiliate marketing can set you on the right track to freedom.</p>
		    </div>
          <div class="col-md-6">
              <img src="{{asset("resources/assets/static/images/img-2.jpg")}}" class="shadow" alt=""/> </div>
          </div>
			</div>
        </div>
      </section>
      <!-- End OPPORTUNITY Area --> 
      <!-- Start OUR PROJECTS Area -->
      <section class="section features-area pt-60 bg-gray pb-80" id="our-projects">
        <div class="container">
          <div class="row mb-20">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center mb-40">
              <h2 class="mb-10 font-24 t-uppercase">OUR PROJECTS</h2>
              <span class="dots bg-orange mb-10"></span> </div>
          </div>
          <div class="features-wrapper row no-gutter">
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="feature-single bg-white ptb-30 prl-40 t-center">
                <div class="mb-20 projects-icon"><img src="{{asset("resources/assets/static/images/store-cashback.svg")}}" alt=""/></div>
                <h3 class="mb-15">IN-STORE CASHBACK</h3>
                <p class="color-mid">You can earn Cash Back when you shop from participating stores! Pay securely using your card, net banking or cash. It’s safe. It’s Secure. Get real cash back in your account and withdraw your cash back into your bank as real cash.</p>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="feature-single bg-white ptb-30 prl-40 t-center">
                <div class="mb-20 projects-icon"><img src="{{asset("resources/assets/static/images/online-cashback.svg")}}" alt=""/></div>
                <h3 class="mb-15">ONLINE STORE CASHBACK</h3>
                <p class="color-mid">Shop at your favorite brand name stores (Flipkart, Amazon, Makemytrip, Myntra etc.) and save on every purchase. Cashbacks are an amazing way for shoppers to make money they wouldn’t otherwise make. </p>
              </div>
            </div>
            <div class="col-md-4 col-sm-12 col-xs-12">
              <div class="feature-single bg-white ptb-30 prl-40 t-center">
                <div class="mb-20 projects-icon"><img src="{{asset("resources/assets/static/images/bill-payment.svg")}}" alt=""/></div>
                <h3 class="mb-15">BILL PAYMENT & RECHARGE</h3>
                <p class="color-mid">Make Utility Bill Payment and Recharge your Mobile, Data Card and DTH Connection. Pay bill for Electricity, Gas, Water, Broadband, Landline. Amazing offers on Recharge, Bill Payments and more.</p>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="feature-single bg-white ptb-30 prl-40 t-center">
                <div class="mb-20 projects-icon"><img src="{{asset("resources/assets/static/images/market-place.svg")}}" alt=""/></div>
                <h3 class="mb-15">MARKETPLACE</h3>
                <p class="color-mid">Sell products online and reach out to customers across India. No listing fees and professional support that help you grow your business. We deduct fees only after you make a sale and ensure you get timely payments.</p>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="feature-single bg-white ptb-30 prl-40 t-center">
                <div class="mb-20 projects-icon"><img src="{{asset("resources/assets/static/images/own-lable-products.svg")}}" alt=""/></div>
                <h3 class="mb-15">OWN LABEL PRODUCTS</h3>
                <p class="color-mid">To introduce innovative products every year a range of daily consumable products in FMCG - Household, Home care, Personal care, Eatable, Edible products, Health & Nutrition, Fashion,  Travel, Footwear  and accessories.</p>
              </div>
            </div>
            <div class="col-md-4 col-sm-12 col-xs-12">
              <div class="feature-single bg-white ptb-30 prl-40 t-center">
                <div class="mb-20 projects-icon"><img src="{{asset("resources/assets/static/images/franchisee-store.svg")}}" alt=""/></div>
                <h3 class="mb-15">FRANCHISE STORES</h3>
                <p class="color-mid">Franchising with the Virob is easier than you think, for all kinds of reasons. Department store, Supermarket, Baker, Chemist, Stationer, Optician, Jewellery Shop, Toy Shop, Clothes Shop, Shoe Shop, Mobile Store, Electronics Shop etc.</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- End OUR PROJECTS Area --> 
      <!-- Start Contact US Area -->
      <section class="section contact-us-area ptb-60" id="contact">
        <div class="container">
          <div class="row mb-40">
            <div class="col-lg-7 col-md-8 col-sm-10 col-xs-12 col-xs-center t-center">
              <h2 class="mb-10 font-24 t-uppercase">Contact Us</h2>
              <span class="dots bg-orange mb-10"></span>
              <p class="color-mid">Let’s build the future of influencer marketing. We’ll get you the help you need.</p>
            </div>
          </div>
          <div class="row mb-20">
            <div class="col-md-3 col-sm-6 mb-30">
              <div class="contact-box t-center p-20">
                <div class="contact-icon"> <i class="lnr lnr-map-marker"></i> </div>
                <h5 class="t-uppercase mb-10">Our Location</h5>
                <p class="color-mid">3rd Floor, No. 13, Paripoorna Layout, Phase 3, Yelahanka, Bangalore - 560064</p>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-30">
              <div class="contact-box t-center p-20">
                <div class="contact-icon"> <i class="lnr lnr-phone"></i> </div>
                <h5 class="t-uppercase mb-10">Call Us</h5>
                <p class="color-mid">Phone : +91 80 29791919<br><br></p>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-30">
              <div class="contact-box t-center p-20">
                <div class="contact-icon"> <i class="lnr lnr-laptop-phone"></i> </div>
                <h5 class="t-uppercase mb-10">Connect Online</h5>
                <p class="color-mid">Email : info@virob.com <br>
                  Website : www.virob.com</p>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-30">
              <div class="contact-box t-center p-20">
                <div class="contact-icon"> <i class="lnr lnr-calendar-full"></i> </div>
                <h5 class="t-uppercase mb-10">Open Hours</h5>
                <p class="color-mid">Mon-Sat : 9 AM – 6 PM <br>
                  Sunday : Closed</p>
              </div>
            </div>
          </div>
          <form id="contactForm" class="contact-form" action="{{route("contact-us")}}" method="post">
            <div id="contactResponse"></div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-15">
                  <input type="text" name="contactName" class="form-control input-lg" placeholder="Your Name">
                </div>
                <div class="mb-15">
                  <input type="email" name="contactEmail" class="form-control input-lg" placeholder="Address Email">
                </div>
                <div>
                  <input type="text" name="contactSubject" class="form-control input-lg" placeholder="Subject">
                </div>
              </div>
              <div class="col-md-6">
                <textarea name="contactMessage" class="form-control input-lg" placeholder="Message" style="height:174px"></textarea>
              </div>
              <div class="col-xs-12">
                <button class="btn btn-orange btn-lg btn-rounded btn-orange center-block">Send Message</button>
              </div>
            </div>
          </form>
        </div>
      </section>
	  <div class="portfolio-modal modal fade in" id="privacypolicy" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="close-modal" data-dismiss="modal">
                    <div class="lr">
                        <div class="rl">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2">
                            <div class="modal-body" style="text-align:justify">
                                <!-- Project Details Go Here -->
                                <h2>OUR PRIVACY POLICY</h2>
								<p>Your privacy is very important to us. To better  protect your privacy the following explains the information we collect, how it  is used, how it is safeguarded, and how to contact us if you have any concerns.  This Website Privacy Notice describes how Virob Ecommerce India Pvt. Ltd.  (hereinafter &quot;Virob&quot;) uses personal data collected or received from  visitors (&quot;Visitors&quot;) of this website (the &quot;Website&quot;). It  describes how we may collect or receive your personal data, the types of  personal data we may collect, how we use, share and protect these data, how  long we retain these data, your rights, and how you can contact us about our  privacy practices.</p>
<p>We will only collect information where it is necessary  for us to do so and we will only collect information if it is relevant to our  dealings with you. By mere use of the Website, you expressly consent to our use  and disclosure of your personal information in accordance with this Privacy  Policy.</p>
<p><strong>CONSENT</strong></p>
<p>By using the Website and/ or by providing your  information, you consent to the collection and use of the information you  disclose on the site in accordance with this Privacy Policy, including but not  limited to your consent for sharing your information as per this privacy  policy. If we decide to change our privacy policy, we will post those changes  on this page so that you are always aware of what information we collect, how  we use it, and under what circumstances we disclose it.</p>
<p><strong>POLICY UPDATES</strong><br />
  We reserve the right to change or update this policy at  any time without notice. To make sure you are aware of any changes, please  review this policy periodically.</p>
<p><strong>THE TYPES OF  PERSONAL DATA WE MAY COLLECT</strong></p>
<p>We may collect information regarding your website  usage, IP-address, browser type and operating system. In addition, through the  communications channels made available on the Website’s &quot;Contact Us&quot;  webpage, we may collect your contact details, such as your name, email address,  address, and telephone number. You may also voluntarily provide other data that  is related to you in connection with your inquiries or comments. We encourage  you, however, to provide no more personal data relating to you than is  necessary in order for us to provide an appropriate response to your inquiries  or comments.<br />
  <br />
  <strong>USE OF PERSONAL  INFORMATION</strong></p>
<p>We may use Personal Information for the purposes for  which you specifically provided it including, without limitation, to enable us  to respond to your inquiries and process and fulfill your requests;</p>
<ol class="list-styled">
  <li>to send you information about your relationship or  transactions with us;</li>
  <li>to tell you about products, services, programs, and offers  that we believe may be of interest to you via e-mail, SMS, telephone or postal  mail.</li>
  <li>to personalize your experience with us including by  presenting products or offers tailored to you;</li>
  <li>to allow you to use, communicate and interact with others on  our Site;</li>
  <li>for our internal business purposes, such as data analysis,  audits, developing new products, enhancing our website, improving our services,  identifying usage trends, and determining the effectiveness of our promotional  campaigns;</li>
  <li>to complete and fulfill your order, or otherwise provide you  with products or services, for example, to process your payments, have your  order delivered to you, communicate with you regarding your purchase and  provide you with related customer service.</li>
</ol>
<p> <br />
    <strong>HOW WE USE THE  PERSONAL DATA WE COLLECT</strong></p>
<p>We use your personal data to (i) address your comments  or inquiries; (ii) to bring you into contact with our Affiliates, (iii) improve  the Website, including to enhance user experience and (iv) diagnose problems  with our servers. If you choose not to provide your personal data, we may not  be able to provide the above services.<br />
  <br />
  <strong>HOW WE MAY  SHARE PERSONAL DATA</strong></p>
<p>Virob does not sell, rent or trade or publish your  personal data. Virob may share your personal data only with:</p>
<ol class="list-styled">
  <li>Entities within the Virob group to whom it is reasonably  necessary or desirable for Virob to disclose personal data;</li>
  <li>Virob Affiliates, to allow communication regarding  registration, product advice, ordering advice, product information for the  products that you may express interest in. and</li>
  <li>Government authorities or other third parties, if required by  law or reasonably necessary to protect the rights, property and safety of  others or ourselves.</li>
</ol>
<p><strong>HOW WE PROTECT  PERSONAL DATA</strong> </p>
<p>We maintain appropriate technical and organizational  security safeguards designed to protect the personal data you provide against  accidental, unlawful or unauthorized destruction, loss, alteration, access,  disclosure or use.<br />
  <br />
  <strong>DATA RETENTION</strong> <br />
  We store personal data for as long as necessary to  fulfil the purposes for which we collect the data (see above under &quot;How We  Use the Personal Data We Collect&quot;), except if required otherwise by law.<br />
  <br />
  <strong>NON-PERSONAL  INFORMATION</strong></p>
<ol class="list-styled">
  <li> “Non-Personal  Information“ is any information that does not reveal your specific identity,  such as:</li>
  <li>Browser information;</li>
  <li>Information collected through cookies, pixel tags and other  technologies;</li>
  <li>Demographic information and other information provided by you  or our Affiliates;</li>
  <li>Aggregated information.</li>
</ol>
<p> <br />
  <strong>AGGREGATING  INFORMATION</strong></p>
<p>We may aggregate Personal Information so that the  aggregated information does not personally identify you or anyone else, such as  by using Personal Information to calculate the percentage of our customers who  live in a particular area. In some instances, we may combine Non-Personal  Information with Personal Information (such as combining your name with your  geographical location). If we combine any Non-Personal Information with  Personal Information, the combined information will be treated by us as  Personal Information as long as it is combined.<br />
  <br />
  <strong>USE AND SHARING  OF NON-PERSONAL INFORMATION</strong></p>
<p>Because Non-Personal Information does not personally  identify you, we may collect, use and disclose Non-Personal Information for any  purpose.<br />
  <br />
  <strong>ONLINE PRIVACY  PROTECTION ACT COMPLIANCE</strong> </p>
<p>Virob is in compliance with the requirements of the Information  Technology Act (ITA) 2000. We will not intentionally collect any information  from anyone under 18 years of age. Our website, products and services are all  directed at people who are at least 18 years old or older.</p>
<p><strong>THIRD PARTY  LINKS</strong></p>
<p>Occasionally, at our discretion, we may include or  offer third party products or services on our website. These third party sites  have separate independent privacy policies. We therefore have no responsibility  or liability for the content and activities of these linked websites.  Nonetheless, we seek to protect the integrity of our website and welcome any  feedback about these websites.<br />
  <br />
  <strong>HOW WE USE  COOKIES</strong></p>
<p>A cookie is a small file which asks permission to be  placed on your computer's hard drive. Once you agree, the file is added and the  cookie helps analyze web traffic or lets you know when you visit a particular  site. Cookies allow web applications to respond to you as an individual. The  web application can tailor its operations to your needs, likes and dislikes by  gathering and remembering information about your preferences. We use traffic  log cookies to identify which pages are being used. This helps us analyse data  about webpage traffic and improve our website in order to tailor it to customer  needs. We only use this information for statistical analysis purposes and then  the data is removed from the system. Overall, cookies help us provide you with  a better website by enabling us to monitor which pages you find useful and  which you do not. They also help online retailers to keep track of a user’s  electronic shopping cart before completing a purchase. A cookie in no way gives  us access to your computer or any information about you, other than the data  you choose to share with us. We have to use cookie-based authentication to  identify you as a registered distributor or send cookies to your computer to  support personalized features on our website like your country and language  codes as well as shopping and browsing functions. You can choose to accept or  decline cookies. Most web browsers automatically accept cookies, but you can  usually modify your browser setting to decline cookies if you prefer. This may  prevent you from taking full advantage of the website.<br />
  <br />
  If you identify  any error in your personal information or need to make a change to that  information, please contact us and we will promptly update our records.</p>
<p>If you have any questions or concerns, please contact  us by e-mail at support@virob.com or call us at Virob HO.</p>
<p>The content on the Virob website may only be  reproduced, distributed, published or otherwise used only after prior written  consent of Virob.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<div class="portfolio-modal modal fade in" id="incomedisclamier" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="close-modal" data-dismiss="modal">
                    <div class="lr">
                        <div class="rl">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2">
                            <div class="modal-body" style="text-align:justify">
                                <!-- Project Details Go Here -->
                                <h2>Income Disclaimer</h2>
								<p>VIROB strictly prohibits its affiliates from publicly  disclosing their own earnings to any person, organisation or third party and  affiliates are bound strictly to the non disclure commitment made to Virob. </p>
<p>Virob Affiliate Network system does not guarantee that  you will make any money from your use or promotion of our products and  services. What you earn as an Virob affiliate is wholly determined by your own  individual efforts. </p>
<p>We provide our affiliates who are contractors, not employed  by Virob, a commission earned for referring the sale of retail products. We  provide training that has led thousands of affiliates to earn substantial  income, but your ability to follow our training, and the amount of time and/or  money you invest in your business, will determine your success. </p>
<p>There is no promise or representation that you will  make a certain amount of money, or any money, or not lose money, as a result of  using our products and services. As with any business, your results may vary, and  will be based on your individual capacity, business experience, expertise, and  level of desire. There are no guarantees concerning the level of success you  may experience. The testimonials and examples used are exceptional results,  which do not apply to the average purchaser, and are not intended to represent  or guarantee that anyone will achieve the same or similar results. Each  individual’s success depends on his or her background, dedication, desire and  motivation.</p>
<p>There is no assurance that examples of past earnings  can be duplicated in the future. We cannot guarantee your future results and/or  success. There are some unknown risks in business and on the internet that we  cannot foresee which could reduce results you experience. We are not responsible  for your actions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="portfolio-modal modal fade in" id="term_of_use" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="close-modal" data-dismiss="modal">
                    <div class="lr">
                        <div class="rl">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2">
                            <div class="modal-body" style="text-align:justify">
                                <!-- Project Details Go Here -->
                                <h2>Terms of Use</h2>
								<p><b>
This document is an electronic record in terms of Information Technology Act, 2000 and rules there under as applicable and the amended provisions pertaining to electronic records in various statutes as amended by the Information Technology Act, 2000. This electronic record is generated by a computer system and does not require any physical or digital signatures.</b></p>
<p><b>This document is published in accordance with the provisions of Rule 3 (1) of the Information Technology (Intermediaries guidelines) Rules, 2011 that require publishing the rules and regulations, privacy policy and Terms of Use for access or usage of www.virob.com website.</b></p>
<p>Welcome to virob.com – a marketplace for performance marketing programs dedicated to e-commerce industry – an affiliate network with a new approach (the “Platform“, as defined in Section 2 below).</p>
<p>In order to use the Platform, You have to agree on this entire document, which constitutes the legal binding agreement between our Users (You) and Us (“Virob“, as defined in Section 1 below). As We’re updating this document from time to time, if you have a Virob account, We will inform You by email, each time We do so.</p>
<p>We’re sure You’ve seen this kind of documents hundreds of times before, but please read it carefully before creating an account and use Our Platform. We’re very serious about respecting all applicable laws AND about respecting this legal agreement in all aspects</p>
<p>ACCESSING, BROWSING OR OTHERWISE USING THE SITE INDICATES YOUR AGREEMENT TO ALL THE TERMS AND CONDITIONS UNDER THESE TERMS OF USE, SO PLEASE READ THE TERMS OF USE CAREFULLY BEFORE PROCEEDING. By impliedly or expressly accepting these Terms of Use, You also accept and agree to be bound by Virob Policies (including but not limited to Privacy Policy) as amended from time to time. As long as You comply with these Terms of Use, We grant You a personal, non-exclusive, non-transferable, limited privilege to enter and use the Website.
</p>
<p>Before going deep into legal details, here are the principles We believe in:</p>
 <ol class="list-styled">> <li>Everyone is here to learn and make money in a respectful manner. We are kindly asking You to keep all discussions and correspondence on a civilized level but taking into account cultural and personality differences; </li> 
 <li>Online marketing is constantly evolving: new traffic sources and new advertising formats arise every day and consumers’ shopping decisions are influenced in ever so different manners. If a partner does anything that You do not understand, it does not necessarily mean that there is something wrong or that You are being fooled in any way. Always ask before drawing any conclusions and always try to see things from Your business partners’ perspective;</li><li>We do believe that any problem may be solved through communication and a problem solving, business oriented attitude, as long as there is no bad faith involved. We are here to work together for the long run and We wish to build long-lasting, win-win-win relationships, intended to bring value added to each partner;</li>
 <li>We are a marketplace, not an advertising agency nor an advertising network. We bring You together and provide technical means for You to work together, but We do not guarantee instant sales but We are doing anything within our power to attract better and motivated people to work and get more sales together;</li><li>We are as transparent as We can be, subject to privacy and confidentiality restrictions herein. We are sharing best practices and aggregate or anonymized figures/data, with the aim of ensuring that everyone has a fair chance to rapidly evolve, thus bringing value added to the ecosystem.</li></ol><p></p>
 <p>That being said, if You breach the applicable Law and/or this document and You do not remedy it – should it be subject to remediation as per provisions herein – We will enforce our rights and our honest partners’ without second thoughts.</p>
 <h4>1. PARTIES</h4>
 <p>This agreement governs legal relationships between Virob Ecommerce India Pvt. Ltd., a company incorporated under the Companies Act, 1956, having its headquarters in 3rd Floor, #13, Paripoorna Layout Phase 3, Rajankunte, Yelahanka, Bengaluru - 560064, email address contact@virob.com (“Virob“, “We“, “Us“), as owner, provider, and operator of the Platform, and Platform’s Users, meaning:</p>
 </p><b>Affiliates</b> – legal or natural persons aged over 18 years, holding duly registered Affiliate Accounts in the Platform, who act as professionals, willing to introduce customers to Virob.com or its associated platform.</p>
 <p>And</p>
 <p><b>Merchants</b> – legal entities who own or have the right to sell products and services, holding a duly registered Merchant Account in the Platform, who are acting as professionals and who are willing to benefit from Promotion of their Stores and Products by Affiliates, against Commissions to be paid on a ‘Cost per Action’ model.</p>
 
 <h4>2. DEFINITIONS AND INTERPRETATION</h4>
 <p>Unless otherwise expressly defined herein, the following terms in capital letters shall bear the following meanings:</p>
 <p><b>VIROB-</b> VIROB is the owner and manager of Ecommerce Website Page, on which VIROB and other third parties (marketplace sellers) are offering products/goods and/or services.</p>
 <p><b>VIROB’s Product -</b> The website and the application on which VIROB or third parties (marketplace sellers) offer to sell, sell and distribute products/goods and/or services.</p>
 <p><b>VIROB’s Affiliate Platform -</b> The online Platform is provided by VIROB, which enables the Affiliate to participate in the VIROB Affiliate Program and provides the Affiliate with statistical and financial information via VIROB’s Affiliate Platform, the Affiliate is able to find all the necessary information and materials, including: Affiliate’s performance, retrieve advertising materials, access to Affiliate’s amount of commission.</p>
 <p><b>Action -</b> Action means any Sale or Lead, as such are defined below; Action may be also called Conversion within VIROB platform.</p>
 <p><b>Advertising Tool - </b>Advertising Tool means banners (of any size), text-links, text-ads, e-mails, videos as made available through Affiliate Programs, or quick links generated by Affiliates through the Platform, in view of being used by Affiliates for Products’ (and/or Merchants Stores) Promotion; All advertising media, including but not limited to website, application and newsletter, Affiliate networks' Sub Affiliates, their owned and brokered medias whether or not registered to the Affiliate Program by the Affiliate and approved by VIROB.</p>
 <p><b>Sub-Affiliates -</b> An entity or an individual who participates in the Affiliate Program through the Affiliate.</p>
 <p><b>Affiliate Program -</b> VIROB’s affiliate program that offers to sell, sell and distribute products/goods and/or services to Customers via Hyperlinks on the Affiliate’s Media.</p>
  <p><b> App -</b>PAYGYFT mobile application.</p>
  <p><b>Affiliate Commission -</b>The fee received by an Affiliate for delivering a sale or an agreed action excluding chargeback. Affiliate Commission means the performance-related payment amount to which Affiliates are entitled for each and all completed Actions, as per Section 6 below; Approval Time means the maximum time-frame available to Merchant/Seller/Advertisers for rejecting or accepting any Commissions generated from completed Actions. Approval Time depends on each Affiliate Program and it is indicated therein;</p>
  <p><b>Invalid transactions -</b>Virob shall not make commission payouts on, and reserves the right to set-off or initiate chargebacks on, invalid transactions.</p>
  <p><b>Click-</b>A user’s call of a hyperlink for the Affiliate Program, leading to the VIROB’s Product.</p>
  <p><b>Hyperlink - </b>A link to the VIROB’s Product in the form of the exact URL, provided via the Affiliate Program, for use by the Affiliate in the Affiliate’s Media (e.g. registered websites), that identifies the Affiliate.</p>
  <p><b>Sale (also known as order or transaction) - </b>The act of purchasing a product or service by one of VIROB’s customers via the Hyperlink. Payout is based on net sales (valid sales).</p>
  <p><b>SEM (Search Engine Marketing) - </b>The acronym which means search engine marketing and includes any form of online marketing that seeks to promote websites by increasing their visibility in search engine result pages through the use of paid placement, contextual advertising or paid inclusion.</p>
  <p><b>SEO (Search Engine Optimization) - </b>The acronym which means search engine optimization and includes the process of (i) improving the volume or quality of traffic to a website or a web page from search engines via "natural" or un-paid ("organic" or "algorithmic") search results, or (ii) realizing or creating an improved or better ranking in search engine results for a specific keyword or keywords.</p>
  <p><b>Advertising Material - Including but not limited to banner, pop-up or any product information shown in equivalent forms.</b></p>
  <p><b>Sign-Up Form - </b>The Sign-up form that is accessible via the Affiliate Program for registration to the Affiliate Program.</p>
  <p><b>View (or impression) -</b>The number of times which an advertisement is shown on the Advertising Media.</p>
  <p><b>Products -</b>mean any and all goods and/or services owned/provided by any Merchant, available for being purchased by Customers on Merchants Stores;
  </p>
  <p><b>ToS -</b>means this agreement together with all its appendices, as they may be amended from time to time, governing the legal relationship between Virob and both the Merchants, on one hand, and the Affiliates, on the other hand;</p>
	<h4>3. CONTRACT FORMATION</h4>
	<p>An agreement between VIROB and the Affiliate in respect of the placement of VIROB advertising materials shall be formed exclusively via VIROB platform's application procedure, in the context of which the Affiliate shall submit an application to participate in the Affiliate Program, thereby accepting the terms and conditions of this Agreement. </p>
	<p>The Sign-Up Form together with this Agreement and the acceptance into the program will together constitute a framework agreement between VIROB and the Affiliate. In the case of a conflict between the Sign-Up Form and this Agreement, this Agreement shall be the governing document.</p>
	<h4>4. SCOPE OF WORK</h4>
	<ol class="list-styled">>
	<li>Scope of work shall be the participation in the Affiliate Program and promotion for VIROB by the Affiliate as an Affiliate in the context of VIROB’s Affiliate Platform. To this end, VIROB shall make a selection of Advertising Materials available to the Affiliate as an advertiser via the VIROB’s Affiliate Platform.</li>
	<li>The Affiliate Program shall not establish any other contractual relationship between the Parties that goes beyond this Agreement.</li>
	<li>The Affiliate shall be solely responsible for placing Advertising Materials on Affiliate’s Media registered in the VIROB Affiliate Program. Subject to VIROB’s rights under this Agreement or otherwise, the Affiliate shall be free to decide whether and how long to place the VIROB advertising materials on the Affiliate’s Media, unless otherwise required by VIROB. The Affiliate shall be entitled to remove the Advertising Materials at any time. The Affiliate is only allowed to place VIROB advertising materials on the Advertising Media provided that such Advertising Media has been registered with and approved by VIROB.</li>
	<li>In return for the successful brokerage, the Affiliate shall receive from VIROB the Commission, which shall depend on the extent and real net value of the service.</li>
	<li>The Affiliate's own terms and conditions shall require the express written consent of VIROB and shall therefore not be applicable even if VIROB does not object to their validity.</li>
	<li>Any time Affiliates are removed or ban from the Affiliate Programs, as well if Affiliates are, otherwise, in breach of this ToS and/or the Law, VIROB may immediately suspend Affiliates Accounts with the Platform, without payment of corresponding Affiliate Commissions.</li>
	</ol>
	<h4>5. OBLIGATIONS AND COVENANTS OF THE AFFILIATE</h4>
	<ol>
	<p>1. The Affiliate shall be expressly prohibited from using and/or modifying the Advertising Materials and content accessed via the VIROB’s Affiliate Platform other than as expressly allowed under the terms of this Contract without VIROB’s prior written agreement.</p>
	<p>2. The Affiliate shall not, without prior written consent by VIROB, be allowed to use advertising emails to promote VIROB. VIROB will be free of all third-party requirements in case of issues because of the mailing Affiliate. The Affiliate guarantees that they take responsibility in case of complaints concerning the e-mail. The Affiliate is not allowed to use the brand “VIROB” within the email address, within the URL, within the source code, and within the subject of the email. The Affiliate has to make sure that it is clear that the email comes from an Affiliate and not from VIROB directly. The email has to be approved by VIROB before it is sent. The Affiliate has to compensate the costs in case of breach of third party requirements or breach of the above restrictions.</p>
	<p>3. The Affiliate shall be responsible for the content and routine operation of the Affiliate’s Media or other relevant Affiliate Media, and shall, for the term of this Agreement, place no content on said Affiliate Media that breaches applicable law, public morals or third-party rights (“Non-Permitted Traffic and Sources”). Prohibitions shall include, but not be limited to, representations that glorify or promote hate, violence, sexual and pornographic content and illustrations, misleading statements or discriminatory content (e.g. in respect of gender, race, politics, religion, nationality or disability). Such content may neither be mentioned on the Affiliate’s Media or other relevant advertising media, nor may links be created from the Affiliate’s Media or other relevant advertising media to corresponding content on other websites.</p>
	<p>4. The Affiliate’s Media or other relevant advertising media shall not conduct, undertake, use, perform or exercise deal, torrent or streaming activities without VIROB’s prior consent.</p>
	<p>5. The Affiliate shall be prohibited from creating and/or maintaining websites/apps that might lead to risk of confusion with the web/mobile presence of VIROB. The Affiliate shall neither be allowed to mirror said presence nor to copy graphics, texts or other content from VIROB website. It is strictly prohibited to crawl any of VIROB’s webpages. In particular, the Affiliate shall avoid creating the impression whether publicly or privately that the Affiliate’s Website is a project of VIROB or that its operator is economically linked to VIROB in any way or any other relationship or affiliation between the Affiliate and VIROB that goes beyond the VIROB Affiliate Program and this Agreement. Any use, by the Affiliate, of materials or content from VIROB web presence or its logos or brands shall require VIROB prior written approval.</p>
	<p>6. The Affiliate shall be liable, vis-à-vis VIROB, for ensuring that its advertising content are neither in direct nor in indirect breach of domestic or foreign third-party property rights or other rights that do not meet any special statutory protection.</p>
	<p>7. It is strictly prohibited to drive SEM and other keyword-based advertising traffic using the VIROB brand or private labels, to VIROB’s Product. In other words, "VIROB" and other similar words which could be misleading as VIROB must be entered as a negative keyword.</p>
	<p>8. Advertising VIROB through social media activities (including but not limited to Facebook, Pinterest, Twitter) is granted upon request and should not include any trademarks of VIROB, or display misleading content (i.e. that may look like official VIROB social media activities).Social media activities through Facebook platform shall be executed through a “Fan Page” only and not through a “Personal Page” in accordance with Facebook’s policy. Inclusion of hyperlinks for every social media post is required unless done in a platform where doing so would not be possible. Posting of hyperlinks through Virob’s Official Facebook pages is strictly prohibited. In case of a violation, a 30% deduction will be applied to the affiliate’s next payout. Should the violation be repeated, the affiliate will be blocked from the Virob Affiliate Program.</p>
	<p>9. The Affiliate shall not set up campaigns on third party Affiliate Networks. The Affiliate is only allowed to direct its own traffic and/or its own Sub Affiliate traffic in case of networks, to the VIROB‘s Product.</p>
	<p>10. The Affiliate shall warrant that it will set cookies only if advertising material made available by the VIROB Affiliate Program is in visible use on the Affiliate’s Website and the user clicks voluntarily and consciously. The use of layers, add-ons, iFrames, pop-up, pop-under, site-under, Auto-redirect advertisements which automatically redirect the user to Advertiser websites without the user’s engagement or action (e.g. click, touch), cookie dropping, postview technology, misleading advertisements that result in misleading clicks that display expected content, shall not be permitted and are strictly prohibited. In particular for Apps campaigns, advertisements that result in forced installations of Advertiser applications. For clarification purposes, forced-installation also includes the act of not asking the Users for permission before initiating a download/ redirect.</p>
	<p>11. The use of offers, creative or brand names for any case of competition or lottery is strictly prohibited.</p>
	<p>12. The Affiliate may promote solely vouchers that VIROB has approved explicitly for affiliates or communicated by means of Affiliate newsletters. The promotion of other vouchers, including but not limited to end customer newsletters, print advertisements or customer service contacts, shall not be permitted and strictly prohibited.</p>
	<p>13. Any breach, by the Affiliate, of its obligations stipulated in this Agreement or any other industrial property rights or copyrights of VIROB shall entitle VIROB to terminate this Agreement for good cause in accordance with the statutory provisions. This shall not affect any additional claims against the Affiliate to which VIROB is entitled. In particular, VIROB shall be entitled, vis-à-vis the Affiliate, to withhold or cease all and any services related to said Affiliate.</p>
	<p>14. The Affiliate shall remove VIROB advertising material without delay from the Affiliate’s Website if VIROB requests it to do so.</p>
	<p>15. If VIROB is sued by third parties on account of the Affiliate's breach of contractual obligations or on account of the Affiliate's violation of a statutory provision in relation to the placement of VIROB advertising material, the Affiliate shall be obliged to indemnify VIROB against all third-party claims that are asserted on account of the aforementioned breaches. If, for its legal defense, VIROB requires the Affiliate to provide information or explanations, the Affiliate shall be obliged to make the same available to VIROB within necessary period no later than three (03) days and also to provide reasonable support to VIROB in its legal defense.</p>
	<p>16. In addition, the Affiliate shall compensate VIROB for any costs resulting from a claim by third parties on account of the infringement of the aforementioned rights and/or obligations; such costs shall, for example, include lawyers' fees, court or other dispute resolution costs, particularly costs of independent proceedings for taking evidence, damages and other disadvantages that VIROB suffers thereby.</p>
	<p>17. The Affiliate shall not purchase any Product(s) through his/her own Affiliate promotions. Also, the Affiliate shall not cause any third parties to use the Affiliate Program to purchase any Product(s) with the intention of reselling such product or for commercial use of any kind. </p>
	<p>18. Transactions are not eligible for payouts, where the Affiliate or sub-Affiliate is simultaneously owning or managing the Seller account (whether directly or not). </p>
	<p>19. For the avoidance of doubt, such transactions shall be deemed as being brought about through collusion and considered an invalid transaction per Clause 1.9.</p>
	<p>20. The Affiliate shall not take advantage of any platform limitations. Exposing procedures which override Virob rules on purchases including, but not limited to, voucher usage and shipping fees is prohibited.</p>
	<p>21. The Affiliate covenants that it has and will maintain all licenses, permits, approvals, registrations or the like, to perform the matters contemplated under this Agreement and that it shall carry out this Agreement in compliance with relevant law of Philippines, particularly the Law on Advertisement, its guiding legislation and legal provisions on data privacy.</p>
	<p>22. In the event of a breach, (including sending Virob invalid transactions or violating of the terms stated in this Agreement), VIROB reserves the right to deem as chargeback: (i) any pending payment owed to the Affiliate, (ii) the total amount of the payout for the period when the breach was found, (iii) any future payout earned by the affiliate proven to have originated from the breach or violation.</p>
	<p>23. The Affiliate shall register each of its Sub-Affiliates with VIROB. The Affiliate acknowledges that by allowing its Sub-Affiliates to participate in the Affiliate Program, the Affiliate shall procure that such Sub-Affiliate shall be bound by the terms and conditions of VIROB’s Affiliate Program.In the case of a violation originated by an identified Affiliate network’s Sub Affiliate, an additional chargeback can be applied equivalent to 30% of the Sub Affiliate payout.</p>
	<h4>6. COMMISSIONS</h4>
	<p>When a Client performs an Action on the Merchant’s Store, they will be informed regarding the value of the generated Commission.</p>
	<p>The procedure set forth below regarding Commissions’ approval, modification or rejection shall accordingly apply.</p>
	<p>In the case of completed Actions whose value was later modified (for whatever reason, e.g. insufficient Product stock, Products return, etc) a proportional corresponding amendment of corresponding Affiliate Commission will be done.</p>
	<p>Any Affiliate Commission may fall under the following categories:</p>
	<ul class="list-styled">>
	<li><b>Pending:</b> is the first status that a generated Commission has. When an Affiliate Commission has the ‘Pending’ status it means that an Action has been completed as per these ToS and the cumulative fulfilment of conditions set forth above is checked by the Advertiser/Merchant;</li>
	<li><b>Rejected: </b>it means that the Commission is not eligible for payment it is not owed and it will not be paid by Advertiser/Merchant. A Commission may be rejected if any of the conditions above is not met;</li>
	<li><b>Approved:</b> it means that, as a result of all cumulative conditions above under this Section 6 being met, or the Approval Time elapsed without the Commission having been rejected by Advertiser; in case the Approval Time is not respected by Advertiser/Merchant, Commissions are automatically Approved by the Platform and shall be deducted and paid out of the Advertiser’s Deposit;</li>
	<li><b>Payable:</b>it means that the Commission is eligible for payment to the Affiliate, automatically after being approved in a pre-paid Affiliate Program or after having been paid by the Advertiser for a post-paid Affiliate Program; Commission is eligible for payment and it will be paid subject to the Affiliate request, as per these ToS;</li>
	<ul>
	<p>Except if otherwise expressly provided herein, once having been Approved, a Commission cannot be diminished, Rejected or otherwise modified by the Advertiser.</p>
	<p>Commissions are being calculated as percentage of the value of Sales generated for the Advertiser or fixed amount for Leads or Sales, as per these ToS. Such percentage is applied to the net amount (excluding VAT or any other taxes) and final amount (after having applied all discounts offered by the Advertiser over the list prices) of Products bought by the directed Clients. Transportation charges applied for delivery of Products purchased by Clients on Advertisers Websites are NOT generating Commissions.</p>
	<p>Advertisers may: (i) decrease Commissions applicable to a certain Program, subject to priory notifying all Affiliates participating in such Affiliate Program; a prior notice of applicable Cookie Life time but not more than 30 days shall be sent in this respect; or (ii) increase the Commission. In all situations, the Platform notifies by email all Affiliates who are active in the relevant Affiliate Program.</p>
	<p>Customised conditions (for ex, preferred Commissions, either higher or lower than the standard available for the respective Affiliate Program, as well as different Cookie Lifetime) may be offered to Affiliates.</p>
	<p>Any Advertiser may grant bonuses to Affiliates, in any amount he may consider appropriate. Bonuses are automatically Approved and shall be immediately disbursed from the available balance of the Deposit to the Affiliate Commissions Payable account.</p>
	<h4>7. SERVICES BY VIROB</h4>
	<ol class="list-styled">><li>Once the Affiliate has been admitted to the VIROB Affiliate Program, it shall be provided with a wide range of advertising materials, which shall be adapted at regular intervals in line with the product range and seasonal influences. The Affiliate may request individual provision of formats or newsletter templates from VIROB at any time.</li>
	<li>VIROB shall operate its website and the services offered thereon, such as the provision of product feed, within the limits of the technical capacities available to VIROB. VIROB shall not be obliged, within these limits, to provide error-free and interruption-free availability of the website. The quality and correctness of the products, advertising material and csv files offered on the VIROB’s Affiliate platform shall fall within the exclusive discretion of VIROB.</li>
	<li>All activities of the Affiliate shall be logged via the platform tracking system and made accessible to the Affiliate via the platform statistics and reports. The commission that VIROB pays to the Affiliate shall be based on the brokered orders and the resulting net shopping basket value. </li>
	<li>The Affiliate shall, in the context of its participation in the platform and in accordance with the terms and conditions that the Affiliate agreed with VIROB in this respect, be entitled to receive a commission from VIROB in relation to net transactions that are generated, by its active promotion of VIROB on the Affiliate’s Website/App, within the first session and for thirty days thereafter if the action of using the Advertising materials is leading to a net transaction and it is the last paid marketing advertorial the end-user is using.</li>
	</ol>
	<h4>8. LIABILITY OF VIROB</h4>
	<ol class="list-styled">
	<li>In the event of an ordinarily negligent breach of an obligation which is material to the achievement of the contractual purpose (material contractual obligation), the liability of VIROB shall not exceed the total of the commissions paid or payable to the Affiliate under this Agreement in the six months immediately prior to when the event giving rise to the most recent claim of liability occurred.</li>
	<li>No further liability on the part of VIROB shall exist.</li>
	<li>The aforementioned limitation of liability shall also apply to the personal liability of VIROB employees, representatives and executive bodies.</li>
	</ol>
	<h4>AFFILIATE COMMISSION FOR TRANSACTION GENERATED FROM VIROB WEBSITE (NON-APP)</h4>
	<ol>
	<li>VIROB agrees to pay a commission on sales generated on Virob’s website, by the traffic coming from the Affiliate’s Website. In order to reward best performing affiliates, VIROB has put in place a category-based commission structure for transactions generated from VIROB’s Affiliate platform. The commission structure could be referred via Virob Affiliate Page</li>
	<li>The commission structure can be modified at any time by adding or reducing points of commission to selected affiliates, in order to incentivize best practices and harmonize Affiliate’s performance.</li>
	<li>Without prejudice to other rights or remedies available to VIROB, VIROB has the right to withhold, and the Affiliate agrees that it shall not be eligible for, any commission otherwise payable under this Agreement if VIROB determines that the Affiliate is not in compliance with any requirement or restriction under this Agreement, including but not limited to technical errors, such as improper link formatting, by the Affiliate.</li>
	</ol>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

		<div class="portfolio-modal modal fade in" id="refundpolicy" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="close-modal" data-dismiss="modal">
						<div class="lr">
							<div class="rl">
							</div>
						</div>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-lg-8 col-lg-offset-2">
								<div class="modal-body" style="text-align:justify">
									<h2>Return / Replacement Criteria</h2>
									<p>We thrive to give you the best products and ecommerce experience. Every product undergoes
strict quality assurance standards at our end. Still if you are not 100% satisfied with your
purchase from Virob, you can return your item(s) under the following conditions.</p>
									<table class="table table-stripped">
										<tr>
											<th>Issue</th>
											<th>Items which can be returned*</th>
										</tr>
										<tr>
											<td>
												<ul class="list-styled">
												<li>Item is physically</li>
												<li>damaged/defective</li>
												<li>Varies from the description</li>
												<li>Wrong item delivered</li>
												<li>Wrong colour</li>
												<li>Wrong style</li>
												<li>Wrong size</li>
												<li>Wrong quantity</li>
												<li>Missing parts/accessories</li>
												</ul>
											</td>
											<td>
												<p><b>All Items except:</b></p>												
												<ul class="list-styled">
													<li>Two-wheelers, Four-wheelers and Commercial Vehicles</li>
												</ul>												
											</td>
										</tr>
										<tr>
											<td>Dissatisfied with the item</td>
											<td>
												<p><b>All Items except:</b></p>	
												<ul class="list-styled">
													<li>Electronics</li>
													<li>Virob Gift Voucher</li>
													<li>Baby Care</li>
													<li>Perfumes &amp; fragrances</li>
													<li>Binoculars &amp; Telescopes</li>
													<li>Camera Lenses &amp; Accessories</li>
													<li>Precious jewellery</li>
													<li>Consumables</li>
													<li>Pet food</li>
													<li>Two-wheelers, Four-wheelers and Commercial Vehicles</li>
													<li>Car care &amp; fresheners</li>
													<li>Cartridges &amp; Toners</li>
													<li>Customized items</li>													
													<li>Digital Entertainment</li>
													<li>Electronic smart watches</li>
													<li>Furniture</li>
													<li>Gaming</li>
													<li>Handkerchiefs</li>
													<li>Health, wellness &amp; medicine</li>
													<li>Inner wear &amp; sleepwear</li>
													<li>Kitchen Appliances</li>
													<li>Laptop Bags &amp; Sleeves</li>
													<li>Movies &amp; Music</li>
													<li>Musical Instruments</li>
													<li>Nutrition &amp; supplements</li>
													<li>Office Equipment</li>
													<li>Online Education</li>
													<li>Socks</li>
													<li>Tyres &amp; Alloys</li>													
												</ul>
											</td>
										</tr>										
									</table>
									<h4>Terms &amp; conditions</h4>
									<ul class="list-styled">
										<li>Electronic items: Upon receipt of your return request, we will arrange for a quality check
										to examine the complaint of the product being faulty/defective. Upon successful
										validation of the complaint we will process your request for return/replacement with
										regard to the faulty/defective electronic item(s). The returns/replacements will be
										accepted for only those items which are found to carry any manufacturing defect and the same shall be rectified by the manufacturer. All returns for any elecrtonic goods will only be processed wihtin [insert] days
from purchase.</li>
										<li>For certain types of defects reported, we may require a document from the brand/OEM&#39;s
										service centre confirming that the delivered item was defective.</li>
										<li>Items that you return should not be used, washed, altered/tampered or soiled. All original
										packing, labels, tags, leaflets, manuals, warranty/guarantee cards, freebies, accessories
										such as belts, locks, straps, etc. should be intact. The courier will not accept your item in
										absence of these. Items with locks/passwords should be returned unlocked/disabled.</li>
										<li>Some items are bound by the brand&#39;s specific policies regarding repair, exchange and
										returns. These policies will be binding on the customer.</li>
										<li>Replacements will depend on the availability of the item.</li>
										<li>Refund or replacement will be initiated once we receive your item and pass it through
										the necessary quality checks.</li>
										<li>Automobile Products (two-wheelers, four-wheelers and Commercial Vehicles) cannot be
										cancelled or returned.</li>
										<li>Virob Gift Vouchers are not returnable or refundable for cash.</li>										
										<li>The following items are non-refundable and non-replaceable: Mobiles Insurance &amp;
										Warranty, Vitamins &amp; Mineral Suppliments, Proteins &amp; Sports Nutrition, Books, Paints, Cement,
										Ayurveda &amp; Organic Products, Family Nutrition, Pharmacy Products, Health &amp; Safety
										Utilities, Hospital &amp; Medical Equipment, Alternative Health Therapies, E-Cigarette &amp; E-
										Shisha, Sexual Wellness, Respiratory Care, Supports &amp; Rehabilitation, Beauty &amp;
										Personal Care, World Food/Indian Food, Household Essentials, Fragrances, Precious
										Jewellery, Lingerie Accessories, Software, Gaming Title, DTH Services, Gift cards,
										Janitorial Supplies, Oil &amp; Additives, Car Care &amp; Fresheners, Pet Supplies, Gift Sets,
										Perfume, Deodorants, Innerwear, Socks, Educational Devices, Extended Warranty &amp;
										Insurance, Diapers, Gaming Consoles, Cartridges &amp; Toners, Memory Cards, Graphic
										card, Processor, RAM, Motherboard.</li>
										<li>Items sold as sets/combos cannot be exchanged or returned individually.</li>
										<li>The following items are only eligible for Replacement: Mobile Phones, Tablets,
										Wearables and Smartwatches, Power banks, Camera Lenses, Camcorders, Digital
										Cameras, DSLRs, Laptop Batteries, Internal Hard drives, Computer Components,
										Cartridges and Toners, External Hard Disks, Movies, Music, TV Shows, Extended
										Warranty, Projectors, Tyres &amp; Alloys, Bean Bags, Shelves, Home Security, Headphones
										&amp; Earphones, Iron, Personal Care Appliances, Printers &amp; Scanners, Air Conditioner, Air
										Conditioners Portable AC, Air Conditioners Split AC, Air Conditioners Window AC, Air
										Conditioners Tower AC, Air Conditioners Cassette AC, Air Conditioners Cube AC,
										Binoculars &amp; Telescopes, Laptops, Monitors, Televisions, Home Theatre Systems, Air
										Coolers, Refrigerator, Washing Machines &amp; Dryers, Outdoor Utility Appliances, Bicycles
										&amp; Accessories, Sanitaryware, Note Counters &amp; Paper Shredders, Labeling &amp; Stamping
										Machine, Laminators &amp; Binders, POS Equipment, Refrigerator, Washing Machines &amp;
										Dryers, Microwave Ovens &amp; OTGs, Vacuum Cleaners, Gaming Consoles, Air Purifiers &amp;
										Humidifiers, Memory Cards, Inverters &amp; Stabilizers, Geysers &amp; Heating appliances,
										Fans, Data Cards, Desktops, Keyboard, Routers &amp; Modems, Webcams, Gaming
										Accessories, Computer Speakers, Headsets with Mic, MP3 &amp; Media Players, Portable
										Audio Players, Speakers, Stereo Components, Video Players, Chimneys &amp; Hoods, Gas
										Stoves &amp; Hobs, Weight Management, Weighing Scales &amp; Daily Needs, Massager &amp; Pain
										Relief, BP &amp; Heart Rate Monitors, Health Monitors &amp; Devices, Contact Lenses, Roti
										maker &amp; Snack maker.</li>
										<li>Mobile phones should be returned in their original brand package with all accessories
										intact and CDs/DVDs, precious jewellery which should be returned in tamper-proof
										packaging only.</li>
										<li>Refurbished and unboxed items can only be returned and not replaced.</li>
										<li>For Furniture, any product related issues will be checked by an authorised service
										personnel (free of cost) and attempted to be resolved by replacing the faulty/ defective
										part of the product. Full replacement will be provided only in cases where the service
										personnel opines that replacing the faulty/defective part will not resolve the issue.</li>										
									</ul>
									<h4>Note:</h4>
									<ul class="list-styled">
									<li>In case the replacement item is out of stock, we will refund your amount.</li>
									<li>Items with locks/passwords should be returned unlocked/disabled.</li>
									</ul>
									<p>All transactions which are conducted are non-refundable in the case of partially used cashback
or unclaimed purchases. In cases whereby you have been wrongfully billed, a “case-to-case”
basis approach will be taken in the presence of credible evidence of such. The Company holds
full authority and discretion towards the outcome of such circumstances. All refunds will be
processed in Vi-Money. Cash refund requests will be considered on a case-by-case basis and
will be subjected to a surcharge of ten per centum (10%) on the transacted amount. The
Company reserves the right to conduct thorough investigations over a period of time deemed
that is deemed necessary by customer service agents of the Company.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
      <!-- End Contact US Area --> 
    </div>
  </main>
  <footer class="main-footer pt-40">
    <div class="container pb-40">
      <div class="footer-widgets">
        <div class="row row-tb-20 row-masnory">
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="widget pt-5">
              <figure class="mb-10"> <img src="{{asset("resources/assets/static/images/logo.png")}}" width="170" alt=""> </figure>
             <ul class="social-icons social-icons--colored list-inline pt-5">
                <li class="social-icons__item"> <a href="#"><i class="fa fa-facebook"></i></a> </li>
                <li class="social-icons__item"> <a href="#"><i class="fa fa-twitter"></i></a> </li>
                <li class="social-icons__item"> <a href="#"><i class="fa fa-linkedin"></i></a> </li>
                <li class="social-icons__item"> <a href="#"><i class="fa fa-google-plus"></i></a> </li>
                <li class="social-icons__item"> <a href="#"><i class="fa fa-pinterest"></i></a> </li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="widget instagram-widget pt-5">
              <ul class="menu-widget">
                <li><a href="#">Branches</a></li>
                <li><a href="#">Downloads</a></li>
                <li><a href="#">Become a Stockist</a></li>
                <li><a href="#">Become a Seller </a></li>
                
                
              </ul>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="widget instagram-widget pt-5">
              <ul class="menu-widget">
                <li><a href="#incomedisclamier" data-toggle="modal">Income Disclaimer</a></li>
                 <li><a href="#">Career</a></li>
                <li><a href="#">Gallery</a></li>
                <li><a href="#">Core Values</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6">  
          <div class="widget instagram-widget pt-5">
              <ul class="menu-widget">
                <li><a href="#privacypolicy" data-toggle="modal">Privacy Policy</a></li>
                <li><a href="#refundpolicy" data-toggle="modal">Refund Policy</a></li>
                <li><a href="resources/uploads/documets/terms-of-use.pdf" target="_blank">Terms of Use</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="sub-footer">
      <div class="container">
        <h6 class="copyright"> Copyright &copy; 2018 Virob. All Rights Reserved. The materials available for download on this site are intended as a general reference for website visitors, and Virob is not engaged in providing professional advice. The materials on this site may include views or recommendations of third parties, which do not necessarily reflect the views of Virob and does not constitute endorsement of any third party product or service. Virob does not warrant that the site or the materials on this site will meet your requirements, be uninterrupted or secure, that any defects or errors will be corrected; or that the site or materials on this site are free of any viruses or other harmful components. Your use of this site and the materials on this site are exclusively at your own risk. </h6>
        <h6 class="copyright mt-10"><strong>DISCLAIMER:</strong> Virob Affiliate Network system does not guarantee that you will make any money from your use or promotion of our products and services.<br>

Your success depends on your own work ethic and other factors that may or may not be mentioned here. Income examples on our promotional materials for illustrations purposes only.</h6>
      </div>
    </div>
  </footer>
</div>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5bfe920a79ed6453ccab890b/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
<!-- ============ Back To Top =============== -->
<div id="backTop" class="back-top is-hidden-sm-down"> <i class="fa fa-angle-up" aria-hidden="true"></i> </div>
<!-- ============ Javascript Libs =============== --> 
<!-- jQuery  --> 
<script src="{{asset("resources/assets/static/js/jquery-1.12.3.min.js")}}"></script> 
<!-- Google Map API --> 
<script type='text/javascript' src='https://maps.google.com/maps/api/js?key=AIzaSyDb11uWNZ0KWaVTYeNbzKULXefC2DzEmkk'></script> 
<!-- Bootstrap  --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/bootstrap.min.js")}}"></script> 
<!-- jQuery Appear  --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/jquery.appear.js")}}"></script> 
<!-- Owl Carousel --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/owl.carousel.min.js")}}"></script> 
<!-- Magnific popup --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/jquery.magnific-popup.min.js")}}"></script> 
<!-- jQuery Easing v1.3 --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/jquery.easing.1.3.min.js")}}"></script> 
<!-- jQuery Mixitup --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/jquery.mixitup.js")}}"></script> 
<!-- YTPlayer --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/jquery.mb.YTPlayer.min.js")}}"></script> 
<!-- Vegas Slider --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/vegas.min.js")}}"></script> 
<!-- gmaps.js --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/gmaps.js")}}"></script> 
<!-- Animated Headline --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/animated-headline.js")}}"></script> 
<!-- Custom JavaScript  --> 
<script type="text/javascript" src="{{asset("resources/assets/static/js/script.js")}}"></script>
</body>
</html>
