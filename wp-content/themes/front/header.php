<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Accounting - Free Bootstrap 4 Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
 
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?> /css/animate.css">
    
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?> /css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?> /css/owl.theme.default.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?> /css/magnific-popup.css">

    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?> /css/flaticon.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri();?> /css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body>

    <div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="bg-wrap">
							<div class="row">
								<div class="col-md-6 d-flex align-items-center">
									<p class="mb-0 phone pl-md-2">
										<a href="#" class="mr-2"><span class="fa fa-phone mr-1"></span> + 91 22 2202 5452</a> 
										<a href="#"><span class="fa-sharp fa-solid fa-envelope"></span> youremail@email.com</a>

									</p>
								</div>
								<div class="col-md-6 d-flex justify-content-md-end">
									<div class="social-media">
						    		<p class="mb-0 d-flex">
						    			<a href="#" class="d-flex align-items-center justify-content-center"><span class="fa fa-facebook"><i class="sr-only">Facebook</i></span></a>
						    			<a href="#" class="d-flex align-items-center justify-content-center"><span class="fa fa-twitter"><i class="sr-only">Twitter</i></span></a>
						    			<a href="#" class="d-flex align-items-center justify-content-center"><span class="fa fa-instagram"><i class="sr-only">Instagram</i></span></a>
						    			<a href="#" class="d-flex align-items-center justify-content-center"><span class="fa fa-dribbble"><i class="sr-only">Dribbble</i></span></a>
						    		</p>
					        </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<nav class="navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
			<?php
                         $logoimg=get_header_image();
                     ?>
	    <div class="container">
         <div class="row d-flex justify-content-between">
          
	    	<a class="navbar-brand" href="<?php echo site_url();?>"><img src="<?php echo $logoimg; ?>"></a>
	    	<form action="#" class="searchform order-sm-start order-lg-last">
          <div class="form-group d-flex">
            <input type="text" class="form-control pl-3" placeholder="Search">
            <button type="submit" placeholder="" class="form-control search"><span class="fa fa-search"></span></button>
          </div>

         </div> 

        </form>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="fa fa-bars"></span> Menu
	      </button>         
	    </div><br>
	    <div class="menu_area">
	    <div class="container">
	    	 <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav m-auto">
	        	<!-- <li class="nav-item active"><a href="index.html" class="nav-link">Home</a></li>
	        	<li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
	        	<li class="nav-item"><a href="services.html" class="nav-link">Services</a></li>
	          <li class="nav-item"><a href="cases.html" class="nav-link">Case Study</a></li>
	          <li class="nav-item"><a href="blog.html" class="nav-link">Blog</a></li>
	          <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li> -->
	         <h6><b><?php echo wp_nav_menu(array('theme_location'=>'primary_menu','menu_class'=>'menu-cust'))?><b></h6>
	        </ul>
	      </div>
	    </div>
	</div>
	    
	  </nav>
    <!-- END nav -->

    <i class="fa-sharp fa-solid fa-envelope"></i>