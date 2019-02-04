<?php
/* @var $this yii\web\View */

$this->title = 'Crispdata';

use frontend\controllers\SiteController;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
$ajaxURL = Yii::$app->params['AJAX_URL'];
?>
<input type = "hidden" value = "<?= $ajaxURL ?>" id = "base_url">
<header id="header" class="okayNav-header navbar-fixed-top main-navbar-top">
    <div class="container">
        <div class="row">

            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">

                <a class="okayNav-header__logo navbar-brand" href="#">
                    <img src="<?= $imageURL ?>assets/images/crispdatalogo.png" alt="" class="logo-1 img-responsive">
                    <img src="<?= $imageURL ?>assets/images/crispdatalogo.png" alt="" class="logo-2 img-responsive">
                </a>

            </div> <!-- End: .col-xs-3 -->
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-6">

                <nav role="navigation" class="okayNav pull-right" id="js-navbar-menu">
                    <ul id="navbar-nav" class="navbar-nav">
                        <li><a class="btn-nav btn-scroll" href="#home">Home</a></li>
                        <li><a class="btn-nav btn-scroll" href="#aboutus">About Us</a></li>
                        <li><a class="btn-nav btn-scroll" href="#contactus">Contact Us</a></li>
                        <!--li><a class="btn-nav" href="#" data-toggle="modal" data-target="#sign-in-form">Sign In</a></li-->
                        <li><a class="btn-nav btn-border" href="" data-toggle="modal" data-target="#sign-up-form">Register</a></li>
                    </ul>
                </nav>

            </div> <!-- End: .col-xs-9 -->
        </div> <!-- End: .row -->
    </div> <!-- End: .container -->
</header><!-- /header -->
<!-- End: Navbar Area
============================= -->




<!-- Start: Sign In Form
================================== -->
<div id="sign-in-form" class="sign-form modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <!-- Modal Close Button -->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <form method="post" class="single-form" action="">

                <div class="col-xs-12 text-center">
                    <h2 class="section-heading p-b-30">Sign In</h2>
                </div>

                <div class="col-xs-12">
                    <!-- Email -->
                    <input name="email" class="contact-email form-control" type="email" placeholder="Email*" required="">
                </div>
                <div class="col-xs-12">
                    <!-- Subject -->
                    <input name="password" class="contact-password form-control" type="pass" placeholder="Password">
                </div>

                <div class="col-xs-12">
                    <div class="checkbox">
                        <input type="checkbox" id="remember-me">
                        <label for="remember-me">Remember me</label>
                    </div>
                </div>

                <!-- Subject Button -->
                <div class="btn-form text-center col-xs-12">
                    <button class="btn btn-fill">Sign In</button>
                </div>
            </form>

        </div><!-- End: .modal-content -->
    </div><!-- End: .modal-dialog -->
</div><!-- End: .modal -->
<!-- End: Sign In Form
================================== -->




<!-- Start: Sign Out Form
================================== -->
<div id="sign-up-form" class="sign-form modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-t-30 p-b-30">

            <!-- Modal Close Button -->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <form id="register" method="post" class="single-form" action="">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                <div class="col-xs-12 text-center">
                    <h2 class="section-heading p-b-30">Register</h2>
                </div>

                <div class="col-xs-12 contact-type">
                    <select name='type' id="rtype" class="contact-type browser-default">
                        <option value=''>Register as</option>
                        <option value='1'>Manufacturer</option>
                        <option value='2'>Contractor</option>
                        <option value='3'>Dealer</option>
                        <option value='4'>Supplier</option>
                    </select>
                </div>
                <input type="hidden" name="htype" id="hiddentype" value="">
                <div id="commondiv" style="display: none;">
                    <div class="col-xs-2">
                        <input name="prefirm" id="prefirm" class="prefirm-name form-control" type="text" value="M/S" readonly="" required="">
                    </div>
                    <div class="col-xs-10">
                        <input name="firm" id="firm" class="firm-name form-control" type="text" placeholder="Firm name*" required="">
                    </div>
                    <div class="col-xs-12">
                        <input name="gst" id="gst" class="gst form-control" type="text" placeholder="GST No*" required="">
                    </div>
                    <div id='contractor' class='box2' style="display: none;">
                        <div class="col-xs-12 contact-contracttype">
                            <select name='contracttype' id="contracttype" class="contact-contracttype browser-default" required="">
                                <option value=''>Select Firm Type</option>
                                <option value='1'>Proprietorship</option>
                                <option value='2'>Partnership</option>
                                <option value='3'>Limited Liability Partnership</option>
                                <option value='4'>Pvt. Ltd. Company</option>
                                <option value='5'>Ltd. Company</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <textarea name="address" id="address" class="firm-address form-control"  placeholder="Address*" required=""></textarea>
                    </div>
                    <div class="col-xs-12 states">
                        <select name='state' class="contact-state browser-default" id="state" required="">
                            <option value="">Select State</option>
                            <?php
                            if (@$states) {
                                foreach ($states as $state) {
                                    ?>
                                    <option value="<?= $state->id ?>"><?= $state->name ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 cities">
                        <select name='city' class="contact-city browser-default" id="city"  required="">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="col-xs-12">
                        <input name="pcode" class="contact-code form-control" placeholder="Pincode" id="pincode" type="text" required="">
                    </div>
                    <div class="col-xs-2">
                        <select name='ctype' class="contact-ctype browser-default" required="">
                            <option value='Mr.'>Mr.</option>
                            <option value='Mrs.'>Mrs.</option>
                            <option value='Ms.'>Ms.</option>
                        </select>
                    </div>
                    <div class="col-xs-10">
                        <!-- Email -->
                        <input name="cperson" class="contact-person form-control" type="text" placeholder="Contact Person*" required="">
                    </div>
                    <div class="col-xs-12">
                        <!-- Email -->
                        <input name="phone" class="contact-phone form-control" type="text" placeholder="Enter multiple numbers by putting comma i.e (1234567,2345678)">
                    </div>
                    <div class="col-xs-12">
                        <!-- Subject -->
                        <input name="cnumber" id="mobile" class="contact-number form-control" type="number" placeholder="Mobile No.*" required="">
                        <a class="btn" id='mobileotp'>Click here to generate OTP</a>
                        <input style="display:none;" id="otpmobile" class="contact-email form-control" type="number" placeholder="Enter OTP" required="">
                        <a style="display:none;" class="btn" id='vmobileotp'>Validate OTP</a>
                    </div>
                    <div class="col-xs-12 emaildiv">
                        <!-- Subject -->
                        <input name="cemail" id="email" class="contact-email form-control" type="email" placeholder="E-mail Id*" required="">
                        <a class="btn" id='emailotp'>Click here to generate OTP</a>
                        <input style="display:none;" name="emailotp" id="otpemail" class="contact-email form-control" type="number" placeholder="Enter OTP" required="">
                        <a style="display:none;" class="btn" id='vemailotp'>Validate OTP</a>
                    </div>
                </div>
                <div id='dealer' class='box1' style="display: none;">
                    <div class="col-xs-12 authdealers">
                        Authorised Dealers
                    </div>
                    <div class="col-xs-12 products">
                        <select name='authtype' id="authtype" class="contact-authtype browser-default" required="">
                            <option value=''>Select Product</option>
                            <option value='1'>Cables</option>
                            <option value='2'>Lighting</option>
                        </select>
                    </div>
                    <div class="col-xs-12" id="cablesdiv" style="display: none;">
                        <select name='cables[]' class="cmakes browser-default" id="cables" multiple>
                            <?php
                            if (@$cables) {
                                foreach ($cables as $cable_) {
                                    ?>
                                    <option value="<?= $cable_->id ?>"><?= $cable_->make ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12" id="lightdiv" style="display: none;">
                        <select name='lighting[]' class="lmakes browser-default" id="lighting" multiple>
                            <?php
                            if (@$lights) {
                                foreach ($lights as $light_) {
                                    ?>
                                    <option value="<?= $light_->id ?>"><?= $light_->make ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div id="supplier" style="display: none;"><span>Coming Soon</span></div>
                <div id="manufacturer" style="display: none;">
                    
                </div>
                <div class="col-xs-12">
                    <div class="checkbox">
                        <input name="terms" type="checkbox" id="agreement" value="1">
                        <label for="agreement">I agree to Your <a href="index.php/site/terms" target="_blank">Terms of Service</a> </label>
                    </div>
                </div>

                <!-- Subject Button -->
                <div class="btn-form text-center col-xs-12">
                    <button  id="signbutton" type="submit"  class="btn btn-fill" disabled>Register</button>
                </div>
            </form>

        </div><!-- End: .modal-content -->
    </div><!-- End: .modal-dialog -->
</div><!-- End: .modal -->
<!-- End: Sign Out Form
================================== -->




<!-- Start: Header Section
================================ -->
<section class="header-section-1 bg-image-1 header-js" id="home" >
    <div class="overlay-color" style="background: url('frontend/web/assets/images/boat.jpg'); background-repeat: no-repeat;    background-position: 50% 50%;
         background-size: cover; height:650px;">
        <div class="container">
            <div class="row section-separator">

                <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1">
                    <div class="part-inner text-center">

                        <!--  Header SubTitle Goes here -->
                        <h1 class="title">Its Free</h1> 


                        <div class="detail service">
                            <p>Services of <img src="<?= $imageURL ?>assets/images/crispdatalogo.png" alt="" class="logoservice"> are free upto 31st March 2019.</p>
                        </div>


                    </div>
                </div> <!-- End: .part-1 -->

            </div> <!-- End: .row -->
        </div> <!-- End: .container -->
    </div> <!-- End: .overlay-color -->
</section>
<!-- End: Header Section
================================ -->




<!-- Start: Features Section 1
====================================== -->
<section class="features-section-1 relative background-semi-dark" id="aboutus" >
    <div class="container">
        <div class="row section-separator">

            <div class="each-features col-sm-6">
                <div class="inner text-center bg-cover light-text" style="background-image: url(frontend/web/assets/images/background-2.jpg);">
                    <div class="overlay-color aboutus">

                        <div class="group">
                            <h4 class="title">ABOUT <img src="<?= $imageURL ?>assets/images/crispdatalogo.png" alt="" class="logoabout"></h4>
                            <div class="detail">
                                <p>We are a bunch of Govt Contractors, Engineers, IT Professionals and Managers who have ample of experience in their own professional spheres & have come together to provide customised solutions to Govt. Contractors, Manufacturers including their Dealers/Distributors, Suppliers and Govt. Departments in reference to their own working spheres.</p>
                                <p>Our leaders have 20+ years of experience in working with the Govt. departments pan India through works contracts and they are, as well versed with the procedures being adopted at various stages of Tendering/Contracting by the Govt. departments as they are with the execution of works and the issues related with the successful execution/completion of Govt. works contracts.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="each-features col-sm-6">
                <div class="inner text-center bg-cover light-text" style="background-image: url(frontend/web/assets/images/background-3.jpg);">
                    <div class="overlay-color aboutus">

                        <div class="group">
                            <h4 class="title">OUR MISSION</h4>
                            <div class="detail">
                                <p>The mission associated with <img src="<?= $imageURL ?>assets/images/crispdatalogo.png" alt="" class="logomission"> is to provide customised solutions to Govt. Contractors, Manufacturers including their Dealers/Distributors, Suppliers and Govt. Departments by providing them hassle free access to the data related to their day to day working thereby helping them become more efficient and in turn increase their business pan India. The Govt. Contracts related data collected in real time from public domain is being analysed by our team of professionals and segregated to the liking of different stratas of audience.</p>
                                <p>In a single line the mission of <img src="<?= $imageURL ?>assets/images/crispdatalogo.png" alt="" class="logomission"> is to bring Contractors, Manufacturers including their Dealers/distributors, Suppliers and Govt. Departments on a common platform.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div> <!-- End: .row -->
    </div> <!-- End: .container -->
</section>
<!-- End: Features Section 1
======================================-->


<!-- Start: Features Section 8
================================== -->
<section class="features-section-8 relative background-semi-dark" id="contactus">
    <div class="container">
        <div class="row section-separator contact-us">

            <!-- Start: Section Header -->
            <div class="section-header col-md-8 col-md-offset-2 ">

                <h2 class="section-heading">Contact Us</h2>

            </div>
            <!-- End: Section Header -->

            <div class="clearfix"></div>

            <div class="col-md-8 col-md-offset-2 col-sm-12">
                <div class="row form-outer">

                    <form id="contact-form" method="post" class="single-form" action="index.php/site/sendmail">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                        <div class="message col-xs-12">
                            <div class="inner"> 

                                <p class="email-loading"><img src="<?= $imageURL; ?>assets/images/loading.gif" alt="">&nbsp;&nbsp;&nbsp;Sending...</p>
                                <p class="email-success"><i class="icon icon-icon-check-alt2"></i> Thank you for contacting us. we will get back to you shortly.</p>
                                <p class="email-failed"><i class="icon icon-icon-close-alt2"></i> Something went wrong!</p>

                            </div> <!-- End: .inner -->
                        </div> <!-- End: .message -->


                        <div class="col-sm-6">
                            <input name="name" class="contact-name form-control" id="contact-name" type="text" placeholder="Name"  required="">
                        </div>

                        <div class="col-sm-6">
                            <input name="email" class="contact-email form-control" id="contact-email" type="email" placeholder="Email"  required="">
                        </div>

                        <div class="col-sm-6">
                            <input name="mobile" class="contact-mobile form-control" id="contact-mobile" type="text" placeholder="Phone No."  required="">
                        </div>
                        <div class="col-sm-6">
                            <input name="subject" class="contact-subject form-control" id="contact-subject" type="text" placeholder="Subject"  required="">
                        </div>

                        <div class="col-sm-12">
                            <textarea name="message" class="contact-message form-control" id="contact-message" rows="3" placeholder="Message" required=""></textarea>
                        </div>

                        <!-- Subject Button -->
                        <div class="btn-form text-center col-xs-12">
                            <button class="btn btn-fill right-icon">send message</button>
                        </div>
                    </form>

                </div>
            </div>

        </div> <!-- End: .row -->
    </div> <!-- End: .container -->
</section>
<footer class="footer-section background-dark">
    <div class="container">
        <div class="row">

            <div class="footer-header">
                <div class="section-separator">

                    <div class="each-section col-sm-3 col-xs-12">

                        <p class="title"><strong>Menu</strong></p>
                        <ul class="nav link-list">
                            <li><a class="btn-scroll" href="#home">Home</a></li>
                            <li><a class="btn-scroll" href="#aboutus">About Us</a></li>
                            <li><a class="btn-scroll" href="#contactus">Contact Us</a></li>
                        </ul>

                    </div> <!-- End: .each-section -->
                    <div class="each-section col-sm-3 col-xs-12">

                        <p class="title"><strong>Links</strong></p>
                        <ul class="nav link-list">
                            <li><a href="index.php/site/terms" target="_blank">Terms & Conditions</a></li>
                            <li><a href="index.php/site/privacy" target="_blank">Privacy Policy</a></li>
                        </ul>

                    </div> <!-- End: .each-section -->
                    <div class="each-section col-sm-3 col-xs-12">

                        <p class="title"><strong>Contact us</strong></p>
                        <ul class="nav link-list">
                            <li>Aadh Datamatics (P) Ltd.</li>
                            <li>JCB-502, Juniper Block</li>
                            <li>Ireo Rise, Sector – 99</li>
                            <li>Mohali – 140308 (Punjab)</li>
                            <li><a href="callto:+911724039981">Call us 0172 – 4039981</a></li>
                            <li><a href="mailto:info@crispdata.co.in">info@crispdata.co.in</a></li>
                        </ul>

                    </div> <!-- End: .each-section -->
                    <div class="each-section vertical-bottom col-sm-3 col-xs-12">

                        <ul class="nav">
                            <li>
                                <div class="li-inner">
                                    <ul class="nav social-icon">
                                        <li><a target="_blank" href="https://www.facebook.com/aadhdatamatics"><i class="icon icons8-facebook"></i></a></li>
                                        <li><a href="#"><i class="icon icons8-twitter"></i></a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>

                    </div> <!-- End: .each-section -->

                </div>
            </div> <!-- End: .footer-header -->

            <div class="copyright text-center col-xs-12">
                <p>Copyright Aadh Datamatics (P) Ltd. © 2019</p>
                <p>Number of visitors</p>

                <img src="https://www.duplichecker.com/counterDisplay?code=d64a7c7a35ca1319da9ec02340242e4c&style=0015&pad=4&type=ip&initCount=1"  title="Web Counter" Alt="Web Counter" border="0">
            </div> <!-- End: .copyright -->

        </div><!-- End: .section-separator  -->
    </div> <!-- End: .container  -->
</footer>