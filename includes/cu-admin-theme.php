<?php

/**
 * Customization to the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Admin;


/**
 * Removes Presbooks branding from Admin area
 */
function remove_pressbooks_branding() {

	remove_action( 'admin_head', '\Pressbooks\Admin\Laf\add_feedback_dialogue' );
	remove_action( 'admin_bar_menu', '\Pressbooks\Admin\Laf\replace_menu_bar_branding', 11 );
	remove_filter( 'admin_footer_text', '\Pressbooks\Admin\Laf\add_footer_link' );

}
add_action( 'plugins_loaded', '\Candela\Utility\Admin\remove_pressbooks_branding' );

/**
 * Replace logo on admin login page
 *
 * @return html
 */
function replace_login_logo() {
	$html = "<style type='text/css'>
						.login h1 a {
						  background-image: url('https://s3-us-west-2.amazonaws.com/pbj-assets/login-logo.png');
						  background-size: 300px 138px;
						  width: 300px;
						  height: 138px;
						  margin-bottom: 1em;
					  }
						.login .message {
						  border-left: 4px solid #0077cc;
						}
						.login #backtoblog a:hover, .login #backtoblog a:active, .login #backtoblog a:focus, .login #nav a:hover, .login #nav a:active, .login #nav a:focus {
						  color: #d4002d;
						}
						.no-svg .login h1 a {
							background-image: url('https://s3-us-west-2.amazonaws.com/pbj-assets/login-logo.png');
						}
					</style>";

	return $html;
}
add_filter( 'pressbooks_login_logo', '\Candela\Utility\Admin\replace_login_logo' );

/**
 * Replace logo in menu bar and add links to About page, Contact page, and forums
 *
 * @param \WP_Admin_Bar $wp_admin_bar The admin bar object as it currently exists
 */
function replace_menu_bar_branding( $wp_admin_bar ) {

	// remove wordpress menus
	$wp_admin_bar->remove_menu( 'wp-logo' );
	$wp_admin_bar->remove_menu( 'documentation' );
	$wp_admin_bar->remove_menu( 'feedback' );
	$wp_admin_bar->remove_menu( 'wporg' );
	$wp_admin_bar->remove_menu( 'about' );

	// remove pressbooks menus
	$wp_admin_bar->remove_menu( 'support-forums' );
	$wp_admin_bar->remove_menu( 'contact' );

	$wp_admin_bar->add_menu( array(
		'id' => 'wp-logo',
		'title' => 'Lumen',
		'href' => ( 'http://lumenlearning.com/' ),
		'meta' => array(
			'title' => __( 'About LumenLearning', 'lumen' ),
		),
	) );

}
add_action( 'admin_bar_menu', '\Candela\Utility\Admin\replace_menu_bar_branding', 11 );

/**
 * Replaces a custom message in admin footer
 */
function replace_footer_link() {

	printf(
		'<p id="footer-left" class="alignleft">
			<span id="footer-thankyou">%s <a href="http://lumenlearning.com">Lumen Learning</a></span>
		</p>',
		__( 'Powered by', 'lumen' )
	);

}
add_filter( 'admin_footer_text', '\Candela\Utility\Admin\replace_footer_link' );

/**
 * Enqueues Admin Area Stylesheet
 */
function enqueue_admin_stylesheet() {

	wp_enqueue_style( 'admin-theme', CU_PLUGIN_URL . 'assets/css/cu-admin.css' );

}
add_action( 'admin_enqueue_scripts', '\Candela\Utility\Admin\enqueue_admin_stylesheet' );
add_action( 'login_enqueue_scripts', '\Candela\Utility\Admin\enqueue_admin_stylesheet' );

/**
 * Removes Pressbooks Newsfeed widget from the Admin Dashboard
 */
function declutter_admin_dashboard() {

	remove_meta_box( 'pb_dashboard_widget_blog', 'dashboard', 'side' );
	remove_meta_box( 'pb_dashboard_widget_blog', 'dashboard-network', 'side' );

}
add_action( 'do_meta_boxes', '\Candela\Utility\Admin\declutter_admin_dashboard' );

/**
 * Makes adjustments to the admin menu based on user roles and privileges
 */
function adjust_admin_menu() {
	global $blog_id;

	$current_user = wp_get_current_user();

	if ( 1 != $blog_id ) {
		remove_menu_page( 'edit.php?post_type=lti_consumer' );
	}

	remove_menu_page( 'plugins.php' );

	add_submenu_page( 'pb_export', 'Export to Thin-CC', 'Thin-CC Export', 'export', 'tools.php?page=candela-thin-export.php' );

	// Remove items that non-admins should not see
	if ( ! ( in_array( 'administrator', $current_user->roles ) || is_super_admin() ) ) {
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'pb_export' );
		remove_menu_page( 'pb_import' );
		remove_menu_page( 'pb_sell' );
		remove_submenu_page( 'options-general.php', 'pb_import' );
		remove_menu_page( 'lti-maps' );
		remove_menu_page( 'edit-comments.php' );
	}

	// Remove items for non-admins and non-editors
	if ( ! ( in_array( 'administrator' , $current_user->roles ) || in_array( 'editor', $current_user->roles ) || is_super_admin() ) ) {
		$metadata = new \PressBooks\Metadata();
		$meta = $metadata->getMetaPost();
		if ( ! empty( $meta ) ) {
			$book_info_url = 'post.php?post=' . absint( $meta->ID ) . '&action=edit';
		} else {
			$book_info_url = 'post-new.php?post_type=metadata';
		}
		remove_menu_page( $book_info_url );
		remove_submenu_page( 'pb_export', 'tools.php?page=candela-thin-export.php' );
	}

}
add_action( 'admin_menu', '\Candela\Utility\Admin\adjust_admin_menu', 11 );
