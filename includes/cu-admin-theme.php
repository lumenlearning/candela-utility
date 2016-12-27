<?php

/**
 * Customization to the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Admin;

/*
 * Removes Presbooks branding from Admin area
 */
function remove_pressbooks_branding() {
	remove_action( 'admin_head', '\Pressbooks\Admin\Laf\add_feedback_dialogue' );
	remove_action( 'admin_bar_menu', '\Pressbooks\Admin\Laf\replace_menu_bar_branding', 11 );
	remove_filter( 'admin_footer_text', '\Pressbooks\Admin\Laf\add_footer_link' );
}
add_action( 'plugins_loaded', '\Candela\Utility\Admin\remove_pressbooks_branding' );

/**
 * Add a custom message in admin footer
 */
function add_footer_link() {

	printf(
		'<p id="footer-left" class="alignleft">
			<span id="footer-thankyou">%s <a href="http://lumenlearning.com">Lumen Learning</a></span>
		</p>',
		__( 'Powered by', 'lumen' )
	);

}
add_filter( 'admin_footer_text', '\Candela\Utility\Admin\add_footer_link' );

/*
 * Enqueues Admin Area Stylesheet
 */
function enqueue_admin_stylesheet() {

	wp_enqueue_style( 'admin-theme', CU_PLUGIN_URL . 'assets/css/cu-admin.css' );

}
add_action( 'admin_enqueue_scripts', '\Candela\Utility\Admin\enqueue_admin_stylesheet' );
add_action( 'login_enqueue_scripts', '\Candela\Utility\Admin\enqueue_admin_stylesheet' );


/*
 * Removes Pressbooks Newsfeed widget from the Admin Dashboard
 */
function declutter_admin_dashboard() {

	remove_meta_box( 'pb_dashboard_widget_blog', 'dashboard', 'side' );
	remove_meta_box( 'pb_dashboard_widget_blog', 'dashboard-network', 'side' );

}
add_action( 'do_meta_boxes', '\Candela\Utility\Admin\declutter_admin_dashboard' );
