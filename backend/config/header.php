<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<?php global $smof_data; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>
<?php header('Access-Control-Allow-Origin: *'); ?>
<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>
	<div class="top-header">
		<div class="container">
			<div class="top-col1">
				<ul>
					<li class="dintmiss">Follow us:</li>
					<?php if(!empty($smof_data['facebook_link'])) { ?><li><a href="<?php echo $smof_data['facebook_link']; ?>"><i class="fa fa-facebook" aria-hidden="true"></i></a></li><?php } ?>
					<?php if(!empty($smof_data['instagram_link'])) { ?><li><a href="<?php echo $smof_data['instagram_link']; ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a></li><?php } ?>
					<?php if(!empty($smof_data['twitter_link'])) { ?><li><a href="<?php echo $smof_data['twitter_link']; ?>"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li><?php } ?>
					<?php if(!empty($smof_data['pinterest_link'])) { ?><li><a href="<?php echo $smof_data['pinterest_link']; ?>"><i class="fa fa-instagram" aria-hidden="true"></i></a></li><?php } ?>
				</ul>
			</div>
			<div class="top-col2">
				<ul>
					<li class="top-phone"><span>Sales and Support </span><a href="tel:<?php echo $smof_data['phone_no']; ?>"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> <?php echo $smof_data['phone_no']; ?></a></li>
					<?php echo do_shortcode('[woo_cart_but]'); ?>
					<?php /* if ( is_user_logged_in() ) { ?>
						<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('My Account','woothemes'); ?></a>
					 <?php } 
					 else { ?>
						<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>"><?php _e('<i class="fa fa-user-o" aria-hidden="true"></i>  Login','woothemes'); ?></a>
					 <?php } */ ?>
					 <a href="#" title="Login"><i class="fa fa-user-o" aria-hidden="true"></i> Login</a>
				</ul>
			</div>
		</div>
	</div>
	<header id="masthead" class="site-header" role="banner">
		<div class="container">
			<div class="site-logo">
				<a href="<?php echo site_url(); ?>"><img src="<?php echo $smof_data['top_logo'];?>" alt="Site Logo"></a>
			</div>
			<div class="head-search">
				<?php get_search_form(); ?>
			</div>
			<?php if ( has_nav_menu( 'top' ) ) : ?>
				<div class="navigation-top">
					<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
				</div><!-- .navigation-top -->
			<?php endif; ?>
			
		</div>

	</header><!-- #masthead -->
	<?php if(is_front_page()) { ?>
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
   <!-- Wrapper for slides -->
    <div class="carousel-inner">
		<?php $i=1; foreach($smof_data['sectionSlider'] as $slider) { ?>
	<div class="item <?php if($i==1){ echo 'active';} ?>">
        <img class="home-sld-img" src="<?php echo $slider['url']; ?>" alt="<?php echo $slider['title']; ?>" style="width:100%;">
        <div class="carousel-caption">
			<div class="container">
				<div class="caption-box wow slideInLeft">
				  <p><?php echo $slider['description']; ?></p>
				  <a class="slide-learn" href="<?php echo $slider['link']; ?>">Register Now</a>
				</div>
			</div>
        </div>
        
      </div><?php $i++; } ?>
	</div>
  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="fa fa-angle-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="fa fa-angle-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div> 
	<?php } elseif(is_home()){ ?>
		<div class="eve-page-title"><div class="container"><h1><?php echo get_the_title() ?></h1></div></div>
		<?php } elseif(is_post_type_archive('news')){ ?>
		
		<div class="eve-page-title"><div class="container">
			<h1>Media</h1>
		</div></div>
		<?php } elseif(is_archive()) { ?>
			<?php if ( have_posts() ){ ?>
		<div class="eve-page-title"><div class="container">
			<?php
				the_archive_title( '<h1>', '</h1>' );
			?>
		</div></div>
	<?php } ?>
			
			<?php } else { ?> 
			<div class="eve-page-title"><div class="container"><h1><?php echo get_the_title() ?></h1></div></div>
			<?php } ?>
<?php

	/*
	 * If a regular post or page, and not the front page, show the featured image.
	 * Using get_queried_object_id() here since the $post global may not be set before a call to the_post().
	 */
	if (is_page()) :
		
	endif;
	?>

	<div class="site-content-contain">
		<div id="content" class="site-content">
