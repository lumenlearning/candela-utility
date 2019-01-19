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
	switch_theme( 'bombadil' );

	// Set copyright to display by default
	$options = get_option( 'pressbooks_theme_options_global' );
	$options['copyright_license'] = 1;
	update_option( 'pressbooks_theme_options_global', $options );

	// Update new blog urls to https
	$urls = array( 'home', 'siteurl' );
	foreach ( $urls as $option ) {
		$value = get_option( $option );
		update_option( $option , str_replace( 'http://', 'https://', $value ) );
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
 *
 * @param $post_id
 *
 */
function pressbooks_new_book_info( $post_id ) {
	// There is exactly one 'metadata' post per wordpress site
	if ( get_post_type( $post_id ) == 'metadata' ) {
		$license = get_post_meta( $post_id, 'pb_book_license', true );
		$copyright_holder = get_post_meta( $post_id, 'pb_copyright_holder', true );

		if ( empty( $license ) ) {
			update_post_meta( $post_id, 'pb_book_license', 'cc-by' );
		}

		if ( empty( $copyright_holder ) ) {
			update_post_meta( $post_id, 'pb_copyright_holder', 'Lumen Learning' );
		}
	}
}
add_action( 'wp_insert_post', '\Candela\Utility\pressbooks_new_book_info' );

/**
 * Returns the original cover image url that points to Amazon S3, and not the
 * local server that Wordpress is hosted on.
 *
 * @param string $cover_url1
 * @param string $cover_url2
 *
 * @return $cover_url2 The original cover image url (on Amazon S3)
 */
function cover_image_url( $cover_url1, $cover_url2 ) {
	return $cover_url2;
}
add_filter( 'pb_cover_image', '\Candela\Utility\cover_image_url', 10, 2 );

/**
 * Skips Pressbooks EPUB dependency check.
 *
 * Pressbooks requires a dependency called EPubCheck
 * (https://github.com/idpf/epubcheck) which validates EPUB files. Pantheon
 * hosting does not allow dependencies like this to be installed directly on the
 * server. Therefore, we must opt out of this validation service.
 *
 * @return bool true
 */
function skip_epub_dependency_check() {
	return true;
}
add_filter( 'pb_epub_has_dependencies', '\Candela\Utility\skip_epub_dependency_check' );

/**
 * Keep emoticons as text.
 *
 * Wordpress 4.3 removed the ability to turn off text-to-emoji. This filter
 * keeps wordpress from converting text to emoji and disables the use of the
 * convert_smilies() function.
 */
add_filter( 'option_use_smilies', '__return_false' );


/**
 * Redirect reviewer after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 *
 * @return string
 */
function reviewer_login_redirect( $redirect_to, $request, $user ) {
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		if ( in_array( 'reviewer', $user->roles ) ) {
			return get_site_url();
		}
	}

	return $redirect_to;
}
add_filter( 'login_redirect', '\Candela\Utility\reviewer_login_redirect', 10, 3 );
