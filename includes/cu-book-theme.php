<?php

/**
 * Logic that changes some of the functionality within the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Book;


/*
 * Registers book themes
 */
function register_themes() {
	register_theme_directory( CU_PLUGIN_DIR . 'themes' );
	wp_register_style( 'candela', CU_PLUGIN_DIR . 'themes/candela/style.css', array( 'pressbooks' ), CU_PLUGIN_VERSION, 'screen' );
	wp_register_style( 'bombadil', CU_PLUGIN_DIR . 'themes/bombadil/style.css', array( 'pressbooks' ), CU_PLUGIN_VERSION, 'screen' );
}
add_action( 'init', '\Candela\Utility\Book\register_themes' );

/*
 * Enqueue styles for book themes
 */
function register_child_themes() {
	wp_enqueue_style( 'candela' );
	wp_enqueue_style( 'bombadil' );
}
add_action( 'wp_enqueue_style', '\Candela\Utility\Book\register_child_themes' );

/*
 * Add registered themes to the list of Pressbooks book themes
 */
function add_theme( $themes ) {
	$merge_themes = array();

	if ( \Pressbooks\Book::isBook() ) {
		$registered_themes = search_theme_directories();
		foreach ( $registered_themes as $key => $val ) {
			if ( $val['theme_root'] == CU_PLUGIN_DIR . 'themes' ) {
				$merge_themes[$key] = 1;
			}
		}
		// add our themes
		$themes = array_merge( $themes, $merge_themes );
	}
	return $themes;
}
add_filter( 'allowed_themes', '\Candela\Utility\Book\add_theme', 12 );
