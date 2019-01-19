<?php

/**
 * @wordpress-plugin
 * Plugin Name: Candela Utility
 * Description: A helper plugin that manages additional configuration and bootstrapping on top of Pressbooks.
 * Version: 1.0.0
 * Author: Lumen Learning
 * Author URI: https://lumenlearning.com
 * Text Domain: lumen
 * License: GPLv2 or later
 * GitHub Plugin URI: https://github.com/lumenlearning/candela-utility
 * Pressbooks tested up to: 5.5.6
 */

namespace Candela\Utility;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// -----------------------------------------------------------------------------
// Setup
// -----------------------------------------------------------------------------

if ( ! defined( 'CU_PLUGIN_VERSION' ) ) {
	define( 'CU_PLUGIN_VERSION', '1.0.0' );
}

if ( ! defined( 'CU_PLUGIN_DIR' ) ) {
	define( 'CU_PLUGIN_DIR', ( is_link( WP_PLUGIN_DIR . '/candela-utility' ) ? trailingslashit( WP_PLUGIN_DIR . '/candela-utility' ) : trailingslashit( __DIR__ ) ) );
}

if ( ! defined( 'CU_PLUGIN_URL' ) ) {
	define( 'CU_PLUGIN_DIR', trailingslashit( plugins_url( 'candela-utility' ) ) );
}

// -----------------------------------------------------------------------------
// Composer Autoloader
// -----------------------------------------------------------------------------

$composer = CU_PLUGIN_DIR . 'vendor/autoload.php';

if ( file_exists( $composer ) ) {
	require_once( $composer );
} else {
	if ( ! class_exists( '\Pressbooks' ) ) {
		/* translators: 1: URL to Composer documentation, 2: URL to Candela Utility latest releases */
		die( sprintf( __( 'Candela Utility dependencies are missing. Please make sure that your project&rsquo;s <a href="%1$s">Composer autoload file</a> is being required, or use the <a href="%2$s">latest release</a> instead.' ), 'https://getcomposer.org/doc/01-basic-usage.md#autoloading', 'https://github.com/lumenlearning/candela-utility/releases/latest/' ) );
	}
}

function load_after_pressbooks() {
	// ---------------------------------------------------------------------------
	// PLUGIN UPDATE SCRIPTS
	// ---------------------------------------------------------------------------
	include CU_PLUGIN_DIR . 'candela-utility-updates.php';

	// ---------------------------------------------------------------------------
	// CLASS INCLUDES
	// ---------------------------------------------------------------------------

	include CU_PLUGIN_DIR . 'includes/cu-admin-theme.php';
	include CU_PLUGIN_DIR . 'includes/cu-book-info.php';
	include CU_PLUGIN_DIR . 'includes/cu-book-theme.php';
	include CU_PLUGIN_DIR . 'includes/cu-catalog-theme.php';
	include CU_PLUGIN_DIR . 'includes/cu-edit-sort.php';
	include CU_PLUGIN_DIR . 'includes/cu-functions.php';
	include CU_PLUGIN_DIR . 'includes/cu-gettext.php';
	include CU_PLUGIN_DIR . 'includes/cu-import.php';
	include CU_PLUGIN_DIR . 'includes/cu-latex.php';
	include CU_PLUGIN_DIR . 'includes/cu-oembed.php';
	include CU_PLUGIN_DIR . 'includes/cu-theme-options.php';
	include CU_PLUGIN_DIR . 'includes/class-cu-editor.php';
	include CU_PLUGIN_DIR . 'includes/cu-assignment-meta.php';
	include CU_PLUGIN_DIR . 'includes/cu-catalog-redirect.php';

	// ---------------------------------------------------------------------------
	// MODULE INCLUDES
	// ---------------------------------------------------------------------------

	include CU_PLUGIN_DIR . 'includes/modules/import/imscc/class-cu-imscc.php';
	include CU_PLUGIN_DIR . 'includes/modules/theme_options/class-cu-navigation-options.php';
	include CU_PLUGIN_DIR . 'includes/modules/theme_options/class-cu-appearance-options.php';
	include CU_PLUGIN_DIR . 'includes/modules/api/html-export-api-endpoint.php';
}
add_action( 'plugins_loaded', 'Candela\Utility\load_after_pressbooks', 11 );
