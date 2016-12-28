<?php

/**
 * Candela Utility
 * Plugin Name: Candela Utility
 * Description: Candela Utility is a helper plugin that manages additional configuration and bootstrapping on top of Pressbooks.
 * Version: 0.2
 * Author: Lumen Learning
 * Author URI: http://lumenlearning.com
 * Text Domain: lumen
 * License: GPLv2 or later
 * GitHub Plugin URI: https://github.com/lumenlearning/candela
 */

namespace Candela\Utility;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// -----------------------------------------------------------------------------
// SETUP
// -----------------------------------------------------------------------------

if ( ! defined( 'CU_PLUGIN_VERSION' ) ) {
	define( 'CU_PLUGIN_VERSION', '0.2' );
}

if ( ! defined( 'CU_PLUGIN_DIR' ) ) {
	define( 'CU_PLUGIN_DIR', __DIR__ . '/' );
}

if ( ! defined( 'CU_PLUGIN_URL' ) ) {
	define( 'CU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// -----------------------------------------------------------------------------
// CLASS INCLUDES
// -----------------------------------------------------------------------------

include CU_PLUGIN_DIR . 'includes/cu-admin-theme.php';
include CU_PLUGIN_DIR . 'includes/cu-book-info.php';
include CU_PLUGIN_DIR . 'includes/cu-book-theme.php';
include CU_PLUGIN_DIR . 'includes/cu-functions.php';
include CU_PLUGIN_DIR . 'includes/cu-gettext.php';
include CU_PLUGIN_DIR . 'includes/cu-import.php';
include CU_PLUGIN_DIR . 'includes/class-cu-editor.php';

// -----------------------------------------------------------------------------
// MODULE INCLUDES
// -----------------------------------------------------------------------------

include CU_PLUGIN_DIR . 'includes/modules/import/imscc/class-cu-imscc.php';
