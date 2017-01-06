<?php

/**
 * Logic that changes options under Theme Options in the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility;


/**
 * Add tabs to the list of tabs under Themes > Theme Options
 *
 * @param $tabs Array list of current tabs
 * @return $tabs Array new list of tabs
 */
function add_theme_option_tabs( $tabs ) {
	$tabs['navigation'] = '\Candela\Utility\Modules\ThemeOptions\NavigationOptions';
	$tabs['appearance'] = '\Candela\Utility\Modules\ThemeOptions\AppearanceOptions';

	return $tabs;
}
add_filter( 'pb_theme_options_tabs', '\Candela\Utility\add_theme_option_tabs', 10, 1 );
