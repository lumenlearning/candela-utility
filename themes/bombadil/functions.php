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
 * Renders the nav container if navigation_show_header/_search is true
 *
 * @return bool
 */
function show_nav_container() {

  $navigation = get_option( 'pressbooks_theme_options_navigation' );

  if ( ( $navigation['navigation_show_header'] == 1 ) || ( $navigation['navigation_show_search'] == 1 ) ) {
    return true;
  }

}

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

  return show_nav_options( 'navigation_show_navigation_buttons' );

}

/**
 * Show navigation header if true
 *
 * @return bool
 */
function show_header() {

  return show_nav_options( 'navigation_show_header' );

}

/**
 * Show navigation header link if true
 *
 * @return bool
 */
function show_header_link() {

  return show_nav_options( 'navigation_show_header_link' );

}

/**
 * Show search field in nav bar if true
 *
 * @return bool
 */
function show_search() {

  return show_nav_options( 'navigation_show_search' );

}

/**
 * Show navigation header if true
 *
 * @return bool
 */
function show_small_title() {

  return show_nav_options( 'navigation_show_small_title' );

}

/**
 * Show edit page button if true
 *
 * @return bool
 */
function show_edit_button() {

  return show_nav_options( 'navigation_show_edit_button' );

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

  return choose_logo( 'navigation_show_waymaker_logo' );

}

/**
 * Show a Logo
 *
 * @return bool
 */
function show_logo() {

  return choose_logo( 'navigation_hide_logo' );

}

function toc_header_logo() {

  $appearance = get_option( 'pressbooks_theme_options_appearance' );
  $image      = $appearance['toc_header_logo'];
  $link       = $appearance['toc_header_link'];

  if ( $appearance['toc_header_logo'] ) {
    if ( $appearance['toc_header_link'] ) {
      echo '<a href="' . $link . '"><img class="toc-header-logo" src="' . $image . '" /></a>';
    } else {
      echo '<img class="toc-header-logo" src="' . $image . '" />';
    }
  }

}

function header_logo() {

  $appearance = get_option( 'pressbooks_theme_options_appearance' );
  $image      = $appearance['header_logo'];
  $link       = $appearance['header_link'];

  if ( $appearance['header_logo'] ) {
    if ( $appearance['header_link'] ) {
      echo '<a href="' . $link . '"><img class="header-logo" src="' . $image . '" /><a/>';
    } else {
      echo '<a href="' . get_home_url() . '"><img class="header-logo" src="' . $image . '" /><a/>';
    }
  } else {
    echo '<div class="pressbooks-logo">Lumen</div>';
  }

}

/**
 *
 */
function header_color() {

  $appearance = get_option( 'pressbooks_theme_options_appearance' );

  if ( ( isset( $appearance['header_color'] ) && strlen( $appearance['header_color'] ) !== 0 ) ) {
    echo ' style="background-color: ' . $appearance['header_color'] . '"';
  }

}

function pressbooks_theme_options_appearance_init() {

  $_page = $_option = 'pressbooks_theme_options_appearance';
  $_section = 'appearance_options_section';
  $defaults = array(
    'header_color' => 0
  );

  if ( false == get_option( $_option ) ) {
    add_option( $_option, $defaults );
  }

  add_settings_section(
    $_section,
    __( 'Appearance Options', 'pressbooks' ),
    'pressbooks_theme_appearance_option_callback',
    $_page
  );

  add_settings_field(
  'toc_header_logo',
  __( 'ToC Header Logo', 'pressbooks' ),
  'pressbooks_theme_toc_header_logo_callback',
  $_page,
  $_section,
  array(
    __( 'Image file name (include file extension)', 'pressbooks' )
    )
  );

  add_settings_field(
  'toc_header_link',
  __( 'ToC Header Link', 'pressbooks' ),
  'pressbooks_theme_toc_header_link_callback',
  $_page,
  $_section,
  array(
    __( 'Link to navigate to when logo in table of contents is clicked', 'pressbooks' )
    )
  );

  add_settings_field(
    'header_logo',
    __( 'Header Logo', 'pressbooks' ),
    'pressbooks_theme_header_logo_callback',
    $_page,
    $_section,
    array(
      __( 'Image file name (include file extension)', 'pressbooks' )
    )
  );

  add_settings_field(
    'header_link',
    __( 'Header Link', 'pressbooks' ),
    'pressbooks_theme_header_link_callback',
    $_page,
    $_section,
    array(
      __( 'Links to Table of Contents page if left blank', 'pressbooks' )
    )
  );

  add_settings_field(
  'header_color',
  __( 'Header Color', 'pressbooks' ),
  'pressbooks_theme_header_color_callback',
  $_page,
  $_section,
  array(
    __( 'Color Hex Code', 'pressbooks' )
    )
  );

  register_setting(
    $_option,
    $_option,
    'pressbooks_theme_options_appearance_sanitize'
  );

}
add_action( 'admin_init', 'pressbooks_theme_options_appearance_init' );

function pressbooks_theme_appearance_option_callback() {

  echo '<p>' . __( 'These options allow customizaton of the page header.', 'pressbooks' ) . '</p>';

}

function pressbooks_theme_toc_header_logo_callback( $args ) {

  $options = get_option( 'pressbooks_theme_options_appearance' );

  if ( ! isset( $options['toc_header_logo'] ) ) {
    $options['toc_header_logo'] = '';
  }

  $html = '<input type="text" id="toc_header_logo" name="pressbooks_theme_options_appearance[toc_header_logo]" value="' . $options['toc_header_logo'] . '" /> ';
  $html .= '<label for="toc_header_logo">' . $args[0] . '</label><br />';

  echo $html;

}

function pressbooks_theme_toc_header_link_callback( $args ) {

  $options = get_option( 'pressbooks_theme_options_appearance' );

  if ( ! isset( $options['toc_header_link'] ) ) {
    $options['toc_header_link'] = '';
  }

  $html = '<input type="text" id="toc_header_link" name="pressbooks_theme_options_appearance[toc_header_link]" value="' . $options['toc_header_link'] . '" /> ';
  $html .= '<label for="toc_header_link">' . $args[0] . '</label><br />';

  echo $html;

}

function pressbooks_theme_header_logo_callback( $args ) {

  $options = get_option( 'pressbooks_theme_options_appearance' );

  if ( ! isset( $options['header_logo'] ) ) {
    $options['header_logo'] = '';
  }

  $html = '<input type="text" id="header_logo" name="pressbooks_theme_options_appearance[header_logo]" value="' . $options['header_logo'] . '" /> ';
  $html .= '<label for="header_logo">' . $args[0] . '</label><br />';

  echo $html;

}

function pressbooks_theme_header_link_callback( $args ) {

  $options = get_option( 'pressbooks_theme_options_appearance' );

  if ( ! isset( $options['header_link'] ) ) {
    $options['header_link'] = '';
  }

  $html = '<input type="text" id="header_link" name="pressbooks_theme_options_appearance[header_link]" value="' . $options['header_link'] . '" /> ';
  $html .= '<label for="header_link">' . $args[0] . '</label><br />';

  echo $html;

}

function pressbooks_theme_header_color_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_appearance' );

	if ( ! isset( $options['header_color'] ) ) {
		$options['header_color'] = '#007FAB';
	}

	$html = '<input type="text" id="header_color" name="pressbooks_theme_options_appearance[header_color]" value="' . $options['header_color'] . '" /> ';

	echo $html;

}

function pressbooks_theme_options_appearance_sanitize( $input ) {

	$options = get_option( 'pressbooks_theme_options_appearance' );

  foreach ( array( 'toc_header_logo' ) as $val ) {
    if ( ! isset( $input[$val] ) ) {
      $options[$val] = '';
    } else {
      $options[$val] = $input[$val];
    }
  }

  foreach ( array( 'toc_header_link' ) as $val ) {
    if ( ! isset( $input[$val] ) ) {
      $options[$val] = '';
    } else {
      $options[$val] = $input[$val];
    }
  }

  foreach ( array( 'header_logo' ) as $val ) {
    if ( ! isset( $input[$val] ) ) {
      $options[$val] = '';
    } else {
      $options[$val] = $input[$val];
    }
  }

  foreach ( array( 'header_link' ) as $val ) {
    if ( ! isset( $input[$val] ) ) {
      $options[$val] = '';
    } else {
      $options[$val] = $input[$val];
    }
  }

  foreach ( array( 'header_color' ) as $val ) {
    if ( ! isset( $input[$val] ) ) {
      $options[$val] = '';
    } else {
      $options[$val] = $input[$val];
    }
  }

  return $options;

}


/* ------------------------------------------------------------------------ *
 * Navigation Options Tab
 * ------------------------------------------------------------------------ */

// Navigation Options Registration
function pressbooks_theme_options_navigation_init() {

	$_page = $_option = 'pressbooks_theme_options_navigation';
	$_section = 'navigation_options_section';
	$defaults = array(
		'navigation_show_header' => 0,
    'navigation_show_header_link' => 0,
		'navigation_show_search' => 1,
    'navigation_show_small_title' => 0,
    'navigation_show_edit_button' => 1,
    'navigation_show_navigation_buttons' => 0,
    'navigation_show_waymaker_logo' => 0,
    'navigation_hide_logo' => 1
	);

	if ( false == get_option( $_option ) ) {
		add_option( $_option, $defaults );
	}

	add_settings_section(
		$_section,
		__( 'Navigation Options', 'pressbooks' ),
		'pressbooks_theme_options_navigation_callback',
		$_page
	);

	add_settings_field(
		'navigation_show_header',
		__( 'Header', 'pressbooks' ),
		'pressbooks_theme_navigation_show_header_callback',
		$_page,
		$_section,
		array(
			__( 'Display Header Bar with Course Title', 'pressbooks' ),
		)
	);

	add_settings_field(
		'navigation_show_header_link',
		__( 'Course Title Link', 'pressbooks' ),
		'pressbooks_theme_navigation_show_header_link_callback',
		$_page,
		$_section,
		array(
			__( 'Make Course Title a Clickable Link to Table of Contents (Header must be selected)', 'pressbooks' ),
		)
	);

	add_settings_field(
		'navigation_show_search',
		__( 'Search', 'pressbooks' ),
		'pressbooks_theme_navigation_show_search_callback',
		$_page,
		$_section,
		array(
			__( 'Enable Search Bar', 'pressbooks' )
		)
	);

  add_settings_field(
    'navigation_show_small_title',
    __( 'Part Title', 'pressbooks' ),
		'pressbooks_theme_navigation_show_small_title_callback',
		$_page,
		$_section,
		array(
			__( 'Display Part/Module/Chapter Title', 'pressbooks' )
		)
  );

  add_settings_field(
    'navigation_show_edit_button',
    __( 'Edit Button', 'pressbooks' ),
		'pressbooks_theme_navigation_show_edit_button_callback',
		$_page,
		$_section,
		array(
			__( 'Enable Edit Button', 'pressbooks' )
		)
  );

  add_settings_field(
    'navigation_show_navigation_buttons',
    __( 'Navigation Buttons', 'pressbooks' ),
		'pressbooks_theme_navigation_show_navigation_buttons_callback',
		$_page,
		$_section,
		array(
			__( 'Enable Navigation Buttons', 'pressbooks' )
		)
  );

  add_settings_field(
    'navigation_show_waymaker_logo',
    __( 'Waymaker Logo', 'pressbooks' ),
		'pressbooks_theme_navigation_show_waymaker_logo_callback',
		$_page,
		$_section,
		array(
			__( 'Enable Waymaker Logo (Candela Logo is default)', 'pressbooks' )
		)
  );

  add_settings_field(
    'navigation_hide_logo',
    __( 'Show Footer Logo', 'pressbooks' ),
    'pressbooks_theme_navigation_hide_logo_callback',
    $_page,
    $_section,
    array(
      __( 'Show Footer Logo', 'pressbooks' )
    )
  );

	register_setting(
		$_option,
		$_option,
		'pressbooks_theme_options_navigation_sanitize'
	);
}
add_action( 'admin_init', 'pressbooks_theme_options_navigation_init' );

// Navigation Options Section Callback
function pressbooks_theme_options_navigation_callback() {

	echo '<p>' . __( 'These options allow customization of the page navigation and are only available when logged in via LTI launch.', 'pressbooks' ) . '</p>';

}

// Navigation Options Field Callback
function pressbooks_theme_navigation_show_header_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_header'] ) ) {
		$options['navigation_show_header'] = 0;
	}

	$html = '<input type="checkbox" id="navigation_show_header" name="pressbooks_theme_options_navigation[navigation_show_header]" value="1"' . checked( 1, $options['navigation_show_header'], false ) . '/> ';
	$html .= '<label for="navigation_show_header">' . $args[0] . '</label><br />';

  echo $html;

}

// Navigation Options Field Callback
function pressbooks_theme_navigation_show_header_link_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_header_link'] ) ) {
		$options['navigation_show_header_link'] = 0;
	}

	$html = '<input type="checkbox" id="navigation_show_header_link" name="pressbooks_theme_options_navigation[navigation_show_header_link]" value="1"' . checked( 1, $options['navigation_show_header_link'], false ) . '/> ';
	$html .= '<label for="navigation_show_header_link">' . $args[0] . '</label><br />';

	echo $html;

}

// Navigation Options Field Callback
function pressbooks_theme_navigation_show_search_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_search'] ) ) {
		$options['navigation_show_search'] = 0;
	}

	$html = '<input type="checkbox" id="navigation_show_search" name="pressbooks_theme_options_navigation[navigation_show_search]" value="1"' . checked( 1, $options['navigation_show_search'], false ) . '/> ';
	$html .= '<label for="navigation_show_search">' . $args[0] . '</label><br />';

	echo $html;

}

// Navigation Options Field Callback
function pressbooks_theme_navigation_show_small_title_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_small_title'] ) ) {
		$options['navigation_show_small_title'] = 1;
	}

	$html = '<input type="checkbox" id="navigation_show_small_title" name="pressbooks_theme_options_navigation[navigation_show_small_title]" value="1"' . checked( 1, $options['navigation_show_small_title'], false ) . '/> ';
	$html .= '<label for="navigation_show_small_title">' . $args[0] . '</label><br />';

	echo $html;

}

// Navigation Options Field Callback
function pressbooks_theme_navigation_show_edit_button_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_edit_button'] ) ) {
		$options['navigation_show_edit_button'] = 1;
	}

	$html = '<input type="checkbox" id="navigation_show_edit_button" name="pressbooks_theme_options_navigation[navigation_show_edit_button]" value="1"' . checked( 1, $options['navigation_show_edit_button'], false ) . '/> ';
	$html .= '<label for="navigation_show_edit_button">' . $args[0] . '</label><br />';

	echo $html;

}

function pressbooks_theme_navigation_show_navigation_buttons_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_navigation_buttons'] ) ) {
		$options['navigation_show_navigation_buttons'] = 0;
	}

	$html = '<input type="checkbox" id="navigation_show_navigation_buttons" name="pressbooks_theme_options_navigation[navigation_show_navigation_buttons]" value="1"' . checked( 1, $options['navigation_show_navigation_buttons'], false ) . '/> ';
	$html .= '<label for="navigation_show_navigation_buttons">' . $args[0] . '</label><br />';

	echo $html;

}

function pressbooks_theme_navigation_show_waymaker_logo_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_show_waymaker_logo'] ) ) {
		$options['navigation_show_waymaker_logo'] = 0;
	}

	$html = '<input type="checkbox" id="navigation_show_waymaker_logo" name="pressbooks_theme_options_navigation[navigation_show_waymaker_logo]" value="1"' . checked( 1, $options['navigation_show_waymaker_logo'], false ) . '/> ';
	$html .= '<label for="navigation_show_waymaker_logo">' . $args[0] . '</label><br />';

	echo $html;

}

function pressbooks_theme_navigation_hide_logo_callback( $args ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	if ( ! isset( $options['navigation_hide_logo'] ) ) {
		$options['navigation_hide_logo'] = 0;
	}

	$html = '<input type="checkbox" id="navigation_hide_logo" name="pressbooks_theme_options_navigation[navigation_hide_logo]" value="1"' . checked( 1, $options['navigation_hide_logo'], false ) . '/> ';
	$html .= '<label for="navigation_hide_logo">' . $args[0] . '</label><br />';

	echo $html;

}

// Navigation Options Input Sanitization
function pressbooks_theme_options_navigation_sanitize( $input ) {

	$options = get_option( 'pressbooks_theme_options_navigation' );

	// Checkmarks
	foreach ( array( 'navigation_show_header' ) as $val ) {
		if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
		else $options[$val] = 1;
	}

  foreach ( array( 'navigation_show_header_link' ) as $val ) {
		if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
		else $options[$val] = 1;
	}

	foreach ( array( 'navigation_show_search' ) as $val ) {
		if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
		else $options[$val] = 1;
	}

  foreach ( array( 'navigation_show_small_title' ) as $val ) {
    if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
    else $options[$val] = 1;
  }

  foreach ( array( 'navigation_show_edit_button' ) as $val ) {
    if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
    else $options[$val] = 1;
  }

  foreach ( array( 'navigation_show_navigation_buttons' ) as $val ) {
    if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
    else $options[$val] = 1;
  }

  foreach ( array( 'navigation_show_waymaker_logo' ) as $val ) {
    if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
    else $options[$val] = 1;
  }

  foreach ( array( 'navigation_hide_logo' ) as $val ) {
    if ( ! isset( $input[$val] ) || $input[$val] != '1' ) $options[$val] = 0;
    else $options[$val] = 1;
  }

  return $options;

}

/* ------------------------------------------------------------------------ *
 * Theme Options Display (Appearance -> Theme Options)
 * ------------------------------------------------------------------------ */

if ( ! function_exists( 'pressbooks_theme_options_display' ) ) :

/**
 * Function called by the Pressbooks plugin when user is on [ Appearance → Theme Options ] page
 */
function pressbooks_theme_options_display() { ?>

	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
		<h2><?php echo wp_get_theme(); ?> Theme Options</h2>
		<?php settings_errors(); ?>
		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'global_options'; ?>
		<h2 class="nav-tab-wrapper">
		<a href="?page=pressbooks_theme_options&tab=global_options" class="nav-tab <?php echo $active_tab == 'global_options' ? 'nav-tab-active' : ''; ?>">Global Options</a>
		<a href="?page=pressbooks_theme_options&tab=web_options" class="nav-tab <?php echo $active_tab == 'web_options' ? 'nav-tab-active' : ''; ?>">Web Options</a>
		<?php if( true == \PressBooks\Utility\check_prince_install() ){ ?>
		<a href="?page=pressbooks_theme_options&tab=pdf_options" class="nav-tab <?php echo $active_tab == 'pdf_options' ? 'nav-tab-active' : ''; ?>">PDF Options</a>
		<?php } ;?>
		<?php if ( true == \PressBooks\Modules\Export\Mpdf\Pdf::isInstalled() ) { ?>
		<a href="?page=pressbooks_theme_options&tab=mpdf_options" class="nav-tab <?php echo $active_tab == 'mpdf_options' ? 'nav-tab-active' : ''; ?>">mPDF Options</a>
		<?php } ?>
		<a href="?page=pressbooks_theme_options&tab=ebook_options" class="nav-tab <?php echo $active_tab == 'ebook_options' ? 'nav-tab-active' : ''; ?>">Ebook Options</a>
    <a href="?page=pressbooks_theme_options&tab=navigation_options" class="nav-tab <?php echo $active_tab == 'navigation_options' ? 'nav-tab-active' : ''; ?>">Navigation Options</a>
    <a href="?page=pressbooks_theme_options&tab=appearance_options" class="nav-tab <?php echo $active_tab == 'appearance_options' ? 'nav-tab-active' : ''; ?>">Appearance Options</a>
		</h2>

		<!-- Create the form that will be used to render our options -->
		<form method="post" action="options.php">
			<?php if( $active_tab == 'global_options' ) {
				settings_fields( 'pressbooks_theme_options_global' );
				do_settings_sections( 'pressbooks_theme_options_global' );
			} elseif( $active_tab == 'web_options' ) {
				settings_fields( 'pressbooks_theme_options_web' );
				do_settings_sections( 'pressbooks_theme_options_web' );
			} elseif( $active_tab == 'pdf_options' ) {
				settings_fields( 'pressbooks_theme_options_pdf' );
				do_settings_sections( 'pressbooks_theme_options_pdf' );
			} elseif( $active_tab == 'mpdf_options' ) {
				settings_fields( 'pressbooks_theme_options_mpdf' );
				do_settings_sections( 'pressbooks_theme_options_mpdf' );
			} elseif( $active_tab == 'ebook_options' ) {
				settings_fields( 'pressbooks_theme_options_ebook' );
				do_settings_sections( 'pressbooks_theme_options_ebook' );
			} elseif( $active_tab == 'navigation_options' ) {
				settings_fields( 'pressbooks_theme_options_navigation' );
				do_settings_sections( 'pressbooks_theme_options_navigation' );
      } elseif( $active_tab == 'appearance_options' ) {
        settings_fields( 'pressbooks_theme_options_appearance' );
				do_settings_sections( 'pressbooks_theme_options_appearance' );
			} ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

endif;
