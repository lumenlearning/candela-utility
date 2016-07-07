<!DOCTYPE html>

<!--[if lt IE 7]>
<html <?php language_attributes(); ?> class="no-js ie6">
<![endif]-->

<!--[if IE 7]>
<html <?php language_attributes(); ?> class="no-js ie7">
<![endif]-->

<!--[if IE 8]>
<html <?php language_attributes(); ?> class="no-js ie8">
<![endif]-->

<!--[if IE 9]>
<html <?php language_attributes(); ?> class="no-js ie9">
<![endif]-->

<!--[if (gt IE 9)|!(IE)]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->

<head>
  <!-- METAS -->
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
    if ( is_front_page() ) {
    	echo pbt_get_seo_meta_elements();
    	echo pbt_get_microdata_meta_elements();
    } else {
    	echo pbt_get_microdata_meta_elements();
    }
  ?>

  <!-- LINKS -->
  <link rel="shortcut icon" href="<?php bloginfo( 'stylesheet_directory' ); ?>/favicon.ico" />
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

  <!-- TITLE -->
  <title>
    <?php
    	global $page, $paged;
    	wp_title( '|', true, 'right' ); bloginfo( 'name' );

    	// Add the blog description for the home/front page.
    	$site_description = get_bloginfo( 'description', 'display' );
    	if ( $site_description && ( is_home() || is_front_page() ) ) {
    		echo " | $site_description";
      }

    	// Add a page number if necessary:
    	if ( $paged >= 2 || $page >= 2 ) {
    		echo ' | ' . sprintf( __( 'Page %s', 'pressbooks' ), max( $paged, $page ) );
      }
  	?>
  </title>

  <!-- REMAINING HEAD SCRIPTS, STYLES, META TAGS -->
  <?php wp_head(); ?>

</head>

<body <?php body_class(); if( wp_title( '', false ) != '' ) { print ' id="' . str_replace( ' ', '', strtolower( wp_title( '', false ) ) ) . '"'; } ?>>

  <?php if ( is_front_page() ) : ?>
    <?php toc_header_logo(); ?>
    <!-- Front Page -->
		<span itemscope itemtype="http://schema.org/Book" itemref="about alternativeHeadline author copyrightHolder copyrightYear datePublished description editor image inLanguage keywords publisher audience educationalAlignment educationalUse interactivityType learningResourceType typicalAgeRange"></span>

    <div class="book-info-container hfeed" <?php header_color(); ?>>

	<?php else : ?>
    <!-- Not Front Page -->
    <span itemscope itemtype="http://schema.org/WebPage" itemref="about copyrightHolder copyrightYear inLanguage publisher"></span>

      <?php if ( show_nav_container() ) { ?>
        <!-- Nav Container -->
        <div class="nav-container">

      <?php } ?>

      <?php if ( show_header() ) { ?>
          <!-- Skip to Content -->
          <div class="skip-to-content">
            <a href="#main-content">Skip to main content</a>
          </div>

          <!-- Nav Bar -->
          <nav role="navigation" <?php header_color(); ?>>
            <div class="header-nav">
              <?php header_logo(); ?>

              <?php if ( show_header_link() ) { ?>
                <a class="book-title" href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
              <?php } else { ?>
                <span class="book-title" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></span>
              <?php } ?>

            </div>
      <?php } ?>

      <?php if ( show_search() || ( show_small_title() ) ) { ?>

            <!-- Sub Nav Bar -->
            <div class="sub-nav">
              <div class="center-subtext-search">

                <?php if ( show_small_title() ) { ?>
                  <!-- Nav Bar Subtext -->
                  <div class="sub-nav-subtext"><?php echo get_the_title( $post->post_parent ); ?></div>
                <?php } ?>

                <?php if ( show_search() ) { ?>
                  <!-- Search Field -->
                  <div class="sub-nav-searchform"><?php get_search_form(); ?></div>
                <?php } ?>

              </div>
            </div>
      <?php } ?>

      <?php if ( !( show_search() ) && !( show_small_title() ) ) { ?>
            <div class="no-sub-nav"></div>
      <?php } ?>

      <?php if ( show_header() ) { ?>
      </nav>
      <?php } ?>

      <?php if ( show_nav_container() ) { ?>
        </div><!-- END .nav-container -->
      <?php } ?>

    		<div id="wrap">
    			<div id="content" role="main">

	 <?php endif; ?>
