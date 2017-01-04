<?php

/**
 * Logic that changes some of the functionality within the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility;


/**
 * Pantheon Hosting required session handling
 */
function pantheon_session_config() {
	ini_set( 'session.save_handler', 'files' );
}
add_filter( 'pressbooks_session_configuration', '\Candela\Utility\pantheon_session_config' );

/**
 * Necessary configuration updates and changes when a new book is created.
 */
function pressbooks_new_book() {
	// Change to a different theme
	switch_theme( 'bombadil' );

	// Set copyright to display by default
	$options = get_option( 'pressbooks_theme_options_global' );
	$options['copyright_license'] = 1;
	update_option('pressbooks_theme_options_global', $options);

	// Update new blog urls to https
	$urls = array('home', 'siteurl');
	foreach ( $urls as $option ) {
		$value = get_option( $option );
		update_option($option , str_replace( 'http://', 'https://', $value) );
	}

}
add_action( 'pressbooks_new_blog', '\Candela\Utility\pressbooks_new_book' );

/**
 * Update default book info settings.
 *
 * A default book info post is created when the "Book Info" section is first
 * visited but not prior. Unfortunately the wp_insert_post action is not *ONLY*
 * called on new posts as documented so we check for empty values on those we
 * want defaults set for.
 */
function pressbooks_new_book_info( $post_id ) {
	// There is exactly one 'metadata' post per wordpress site
	if ( get_post_type( $post_id ) == 'metadata') {
		$license = get_post_meta( $post_id, 'pb_book_license', TRUE);
		if ( empty( $license ) ) {
			update_post_meta( $post_id, 'pb_book_license', 'cc-by' );
		}

		$copyright_holder = get_post_meta( $post_id, 'pb_copyright_holder', TRUE );
		if ( empty( $copyright_holder ) ) {
			update_post_meta( $post_id, 'pb_copyright_holder', 'Lumen Learning' );
		}
	}
}
add_action( 'wp_insert_post', '\Candela\Utility\pressbooks_new_book_info' );

/**
 * Returns the original cover image url that points to Amazon S3, and not the
 * local server that this instance of Wordpress is hosted on.
 *
 * @return $cover_url2 The original cover image url (on Amazon S3)
 */
function cover_image_url( $cover_url1, $cover_url2 ) {
	return $cover_url2;
}
add_filter( 'pb_cover_image', '\Candela\Utility\cover_image_url', 10, 2 );