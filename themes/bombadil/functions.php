<?php

/**
 * Enqueues Bombadil Stylesheets
 *
 */
function bombadil_theme_styles() {

  wp_enqueue_style( 'foundation', get_stylesheet_directory_uri() . '/css/foundation.min.css' );
  wp_enqueue_style( 'normalize', get_stylesheet_directory_uri() . '/css/normalize.css' );
  wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css' );

}
add_action( 'wp_print_styles', 'bombadil_theme_styles' );

/**
 * Enqueues Bombadil Scripts
 *
 */
function bombadil_theme_scripts() {

  wp_enqueue_script( 'foundation', get_stylesheet_directory_uri() . '/js/foundation.min.js', array( 'jquery' ), '', true );
  wp_enqueue_script( 'iframe_resizer', get_stylesheet_directory_uri() . '/js/iframe_resizer.js', array( 'jquery' ), '', true );
  wp_enqueue_script( 'embedded_audio', get_stylesheet_directory_uri() . '/js/audio_behavior.js', array( 'jquery' ), '', true );
  wp_enqueue_script( 'lti_buttons', get_stylesheet_directory_uri() . '/js/lti_buttons.js', array( 'jquery' ), '', true );
  wp_enqueue_script( 'attributions', get_stylesheet_directory_uri() . '/js/attributions.js', array( 'jquery' ), '', true );

  // Pass PHP data down to attributions.js
  $dataToBePassed = array(
    'id' => get_the_ID()
  );
  wp_localize_script( 'attributions', 'thePost', $dataToBePassed );

  wp_enqueue_script( 'hide_answers', CU_PLUGIN_URL . 'assets/js/hide-answer.js', array( 'jquery' ), '', true );
  wp_enqueue_script( 'html5shiv', 'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js', array(), '3.7.3', false );
  wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );
  wp_enqueue_script( 'typekit', '//use.typekit.net/mje6fya.js', array(), '1.0.0' );

}
add_action( 'wp_enqueue_scripts', 'bombadil_theme_scripts' );

function bombadil_color_picker_assets( $hook ) {
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'color-picker', plugins_url('js/color-pick.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'bombadil_color_picker_assets' );

/**
 * Checks to make sure the main script has been enqueued and then load the
 * typekit inline script.
 *
 */
function bombadil_typekit_inline() {

  if ( wp_script_is( 'typekit', 'enqueued' ) ) {
    echo '<script>try{Typekit.load();}catch(e){}</script>';
  }

}
add_action( 'wp_head', 'bombadil_typekit_inline' );

/**
 * Returns an html blog of meta elements
 *
 * @return string $html metadata
 */
function pbt_get_seo_meta_elements() {

	// map items that are already captured
	$meta_mapping = array(
    'author'      => 'pb_author',
    'description' => 'pb_about_50',
    'keywords'    => 'pb_keywords_tags',
    'publisher'   => 'pb_publisher'
	);

	$html     = "<meta name='application-name' content='PressBooks'>\n";
	$metadata = \PressBooks\Book::getBookInformation();

	// create meta elements
	foreach ( $meta_mapping as $name => $content ) {
		if ( array_key_exists( $content, $metadata ) ) {
			$html .= "<meta name='" . $name . "' content='" . $metadata[$content] . "'>\n";
		}
	}

	return $html;

}

/**
 * Returns an html microdata
 *
 * @return string $html microdata
 */
function pbt_get_microdata_meta_elements() {

	// map items that are already captured
	$micro_mapping = array(
    'about'               => 'pb_bisac_subject',
    'alternativeHeadline' => 'pb_subtitle',
    'author'              => 'pb_author',
    'contributor'         => 'pb_contributing_authors',
    'copyrightHolder'     => 'pb_copyright_holder',
    'copyrightYear'       => 'pb_copyright_year',
    'datePublished'       => 'pb_publication_date',
    'description'         => 'pb_about_50',
    'editor'              => 'pb_editor',
    'image'               => 'pb_cover_image',
    'inLanguage'          => 'pb_language',
    'keywords'            => 'pb_keywords_tags',
    'publisher'           => 'pb_publisher'
	);

  $html     = '';
	$metadata = \PressBooks\Book::getBookInformation();

	// create microdata elements
	foreach ( $micro_mapping as $itemprop => $content ) {
		if ( array_key_exists( $content, $metadata ) ) {
			if ( 'pb_publication_date' == $content ) {
				$content = date( 'Y-m-d', $metadata[$content] );
			} else {
				$content = $metadata[$content];
			}
			$html .= "<meta itemprop='" . $itemprop . "' content='" . $content . "' id='" . $itemprop . "'>\n";
		}
	}

	return $html;

}

/**
 * Render Previous and Next Buttons
 *
 * @param bool $echo
 */
function ca_get_links( $echo = true ) {

  global $first_chapter, $prev_chapter, $next_chapter;

  $first_chapter = pb_get_first();
  $prev_chapter  = pb_get_prev();
  $next_chapter  = pb_get_next();

  if( isset( $_GET['content_only'] ) ) {
    $next_chapter = add_query_arg( 'content_only', 1, $next_chapter );
    $prev_chapter = add_query_arg( 'content_only', 1, $prev_chapter );
  }

  if( isset( $_GET['lti_context_id'] ) ) {
    $next_chapter = add_query_arg( 'lti_context_id', $_GET['lti_context_id'], $next_chapter );
    $prev_chapter = add_query_arg( 'lti_context_id', $_GET['lti_context_id'], $prev_chapter );
  }

  if ( $echo ) : ?>
    <div class="bottom-nav-buttons">
      <?php if ( $prev_chapter != '/' ) : ?>
        <a class="page-nav-btn" id="prev" href="<?php echo esc_url( $prev_chapter ); ?>"><?php _e( 'Previous', 'pressbooks' ); ?></a>
      <?php endif; ?>

      <?php if ( $next_chapter != '/' ) : ?>
        <a class="page-nav-btn" id="next" href="<?php echo esc_url( $next_chapter ); ?>"><?php _e( 'Next', 'pressbooks' ); ?></a>
      <?php endif; ?>
    </div>
  <?php endif;

}

/**
 * Render LTI Previous and Next Buttons, for LMS Integration
 *
 * @param bool $echo
 */
function lti_get_links( $echo = true ) {

  if ( $echo ): ?>
    <div class="lti-bottom-nav-buttons">
      <a class="lti-nav-btn" id="lti-prev"><span class="lti-btn-arrow">&#10094;</span><span class="lti-btn-text">Previous</span></a>
      <a class="lti-nav-btn" id="lti-next"><span class="lti-btn-text">Next</span><span class="lti-btn-arrow">&#10095;</span></a>
      <a class="lti-nav-btn" id="study-plan">Study Plan</a>
    </div>
  <?php endif;

}

/**
 * Allows assorted tags to be used in posts
 *
 * @param array $allowed_post_tags
 */
function allow_post_tags( $allowed_post_tags ) {

  $allowed_post_tags['iframe'] = array(
    'align' => true,
    'allowFullScreen' => true,
    'class' => true,
    'frameborder' => true,
    'height' => true,
    'id' => true,
    'longdesc' => true,
    'marginheight' => true,
    'marginwidth' => true,
    'mozallowfullscreen' => true,
    'name' => true,
    'sandbox' => true,
    'seamless' => true,
    'scrolling' => true,
    'src' => true,
    'srcdoc' => true,
    'style' => true,
    'width' => true,
    'webkitAllowFullScreen' => true
  );

  return $allowed_post_tags;

}
add_filter( 'wp_kses_allowed_html', 'allow_post_tags', 1 );

/**
 * Renders the nav container if nav_show_header/_search is true
 *
 * @return bool
 */
function show_nav_container() {

  $navigation = get_option( 'pressbooks_theme_options_navigation' );

  if ( ( $navigation['nav_show_header'] == 1 ) || ( $navigation['nav_show_search'] == 1 ) ) {
    return true;
  }

}

// -----------------------------------------------------------------------------
// THEME OPTIONS > NAVIGATION OPTIONS
// -----------------------------------------------------------------------------

/**
 * Logic for rendering navigation buttons inside LMS
 *
 * @param string $selected_option
 * @return bool
 */
function show_nav_options( $selected_option ) {

  $via_LTI_launch = isset( $_GET['content_only'] );

  if ( $via_LTI_launch ) {
    $navigation = get_option( 'pressbooks_theme_options_navigation' );

    if ( $navigation[$selected_option] == 1 ) {
      return true;
    } else {
      return false;
    }
  } else {
    return true;
  }

}

/**
 * Show navigation buttons if true
 *
 * @return bool
 */
function show_navigation_buttons() {

  return show_nav_options( 'nav_show_navigation_buttons' );

}

/**
 * Show navigation header if true
 *
 * @return bool
 */
function show_header() {

  return show_nav_options( 'nav_show_header' );

}

/**
 * Show navigation header link if true
 *
 * @return bool
 */
function show_header_link() {

  return show_nav_options( 'nav_header_link' );

}

/**
 * Show search field in nav bar if true
 *
 * @return bool
 */
function show_search() {

  return show_nav_options( 'nav_show_search' );

}

/**
 * Show navigation header if true
 *
 * @return bool
 */
function show_small_title() {

  return show_nav_options( 'nav_show_small_title' );

}

/**
 * Show edit page button if true
 *
 * @return bool
 */
function show_edit_button() {

  return show_nav_options( 'nav_show_edit_button' );

}

/**
 * Show LTI navigation buttons if ?lti_nav is set
 *
 * @return bool
 */
function show_lti_buttons() {

  return isset( $_GET['lti_nav'] );

}

/**
 * Show Logo
 *
 * @param  string $chosen_logo
 * @return bool
 */
function choose_logo( $chosen_logo ) {

  $navigation = get_option( 'pressbooks_theme_options_navigation' );

  if ( ( isset( $navigation[$chosen_logo] ) && ( $navigation[$chosen_logo] == 1 ) ) ) {
    return true;
  } else {
    return false;
  }

}

/**
 * Show the Waymaker Footer Logo
 *
 * @return bool
 */
function show_waymaker_logo() {

  return choose_logo( 'nav_show_waymaker_logo' );

}

/**
 * Show a Logo
 *
 * @return bool
 */
function show_logo() {

  return choose_logo( 'nav_show_footer_logo' );

}


// -----------------------------------------------------------------------------
// THEME OPTIONS > APPEARANCE OPTIONS
// -----------------------------------------------------------------------------

/**
 * Render the Table of Contents header logo and link (if set).
 *
 * @return html
 */
function toc_header_logo() {

	$appearance = get_option( 'pressbooks_theme_options_appearance' );

	if ( isset( $appearance['toc_header_logo'] ) ) {
		if ( isset( $appearance['toc_header_link'] ) ) {
			echo '<a href="' . $appearance['toc_header_link'] . '"><img class="toc-header-logo" src="' . $appearance['toc_header_logo'] . '" /></a>';
		} else {
			echo '<img class="toc-header-logo" src="' . $appearance['toc_header_logo'] . '" />';
		}
	}

}

/**
 * Render the header logo and link (if set).
 *
 * @return html
 */
function header_logo() {

	$appearance = get_option( 'pressbooks_theme_options_appearance' );

	if ( isset( $appearance['header_logo'] ) ) {
		if ( isset( $appearance['header_link'] ) ) {
			echo '<a href="' . $appearance['header_link'] . '"><img class="header-logo" src="' . $appearance['header_logo'] . '" /><a/>';
		} else {
			echo '<a href="' . get_home_url() . '"><img class="header-logo" src="' . $appearance['header_logo'] . '" /><a/>';
		}
	} else {
		echo '<div class="pressbooks-logo">Lumen</div>';
	}

}

/**
 * Render the header color (if set).
 *
 * @return html
 */
function header_color() {

  $appearance = get_option( 'pressbooks_theme_options_appearance' );

  if ( ( isset( $appearance['header_color'] ) && strlen( $appearance['header_color'] ) !== 0 ) ) {
    echo ' style="background-color: ' . $appearance['header_color'] . '"';
  }

}
