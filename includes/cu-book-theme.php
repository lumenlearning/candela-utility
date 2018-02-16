<?php

/**
 * Logic that changes some of the functionality within the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Book;


// -----------------------------------------------------------------------------
// THEMES LOGIC
// -----------------------------------------------------------------------------

/**
 * Returns the attribution type (used for cover image licensing)
 *
 * @param $type string
 * @return string
 */
function the_attribution_type( $type ) {

	$types = array(
		'original' => 'CC Licensed Content, Original',
		'cc' => 'CC Licensed Content, Shared Previously',
		'cc-attribution' => 'CC Licensed Content, Specific Attribution',
		'copyrighted_video' => 'All Rights Reserved Content',
		'pd' => 'Public Domain Content',
		'lumen' => 'Lumen Learning Authored Content',
	);

	foreach ( $types as $key => $value ) {
		if ( $key === $type ) {
			return $value;
		}
	}

}

/**
 * Returns a string of the License long name (label) and creativecommons
 * definition (link)
 *
 * @param $short_attribution String
 * @return string
 */
function the_attribution_license( $short_attribution ) {

	$attributions = array(
		'pd' => array(
			'label' => __( 'Public Domain: No Known Copyright' ),
			'link' => 'https://creativecommons.org/about/pdm',
		),
		'cc0' => array(
			'label' => __( 'CC0: No Rights Reserved' ),
			'link' => 'https://creativecommons.org/about/cc0',
		),
		'cc-by' => array(
			'label' => __( 'CC BY: Attribution' ),
			'link' => 'https://creativecommons.org/licenses/by/4.0/',
		),
		'cc-by-sa' => array(
			'label' => __( 'CC BY-SA: Attribution-ShareAlike' ),
			'link' => 'https://creativecommons.org/licenses/by-sa/4.0/',
		),
		'cc-by-nd' => array(
			'label' => __( 'CC BY-ND: Attribution-NoDerivatives' ),
			'link' => 'https://creativecommons.org/licenses/by-nd/4.0/',
		),
		'cc-by-nc' => array(
			'label' => __( 'CC BY-NC: Attribution-NonCommercial' ),
			'link' => 'https://creativecommons.org/licenses/by-nc/4.0/',
		),
		'cc-by-nc-sa' => array(
			'label' => __( 'CC BY-NC-SA: Attribution-NonCommercial-ShareAlike' ),
			'link' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
		),
		'cc-by-nc-nd' => array(
			'label' => __( 'CC BY-NC-ND: Attribution-NonCommercial-NoDerivatives ' ),
			'link' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
		),
		'arr' => array(
			'label' => __( 'All Rights Reserved' ),
		),
		'other' => array(
			'label' => __( 'Other' ),
		),
	);

	foreach ( $attributions as $key => $value ) {
		if ( $key === $short_attribution ) {
			return $value;
		}
	}

}

/**
 * Return book info metadata
 *
 * @return array
 */
function candela_get_book_info_meta() {

	$book_information = array();
	$meta = new \Pressbooks\Metadata();
	$data = $meta->getMetaPostMetadata();

	$book_information = array_map( 'array_pop', $data );

	return $book_information;
}

/**
 * Adds navigation links buttons that send the user to the next or
 * previous pages
 */
function nav_links() {
	echo( edit_post_link( 'Previous', '', '', get_pb_page_id( 'prev' ) ) . ' - ' );
	echo( edit_post_link( 'Next', '', '', get_pb_page_id( 'next' ) ) );
}

/**
 * Fetch next or previous Pressbooks post ID
 * This is taken from PB's inner code to find the next page
 *
 * @param string $what prev, next
 * @return ID of requested post
 */
function get_pb_page_id( $what = 'next' ) {

	global $blog_id;
	global $post;

	$current_post_id = $post->ID;
	$book_structure = \Pressbooks\Book::getBookStructure();
	$order = $book_structure['__order'];
	$pos = array_keys( $order );

	$what = ( 'next' == $what ? 'next' : 'prev' );

	// Move internal pointer to correct position
	reset( $pos );
	while ( $find_me = current( $pos ) ) {
		if ( $find_me == $current_post_id ) {
			break;
		} else {
			next( $pos );
		}
	}

	// Get next/previous
	$what( $pos );
	while ( $post_id = current( $pos ) ) {
		if ( 'publish' == $order[ $post_id ]['post_status'] ) {
			break;
		} elseif ( current_user_can_for_blog( $blog_id, 'read' ) ) {
			break;
		} else {
			$what( $pos );
		}
	}

	return $post_id;
}
