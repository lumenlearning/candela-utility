<?php

/**
 * Customization to the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Admin;

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
