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
 * Returns the origin cover image url that points to Amazon S3, and not the
 * local server that this instance of Wordpress is hosted on.
 *
 * @return $cover_url2 The original cover image url (on Amazon S3)
 */
function cover_image_url( $cover_url1, $cover_url2 ) {
	return $cover_url2;
}
add_filter( 'pb_cover_image', '\Candela\Utility\cover_image_url' );


// -----------------------------------------------------------------------------
// OEMBED HANDLING
// -----------------------------------------------------------------------------

/**
 * Filter embed_oembed_html.
 * Replace all 'http://' links with 'https://
 */
function embed_oembed_html( $html, $url, $attr ) {
	if ( is_ssl() ) {
		return str_replace( 'http://', 'https://', $html );
	}

	return $html;
}
add_filter( 'embed_oembed_html', '\Candela\Utility\embed_oembed_html', 10, 3 );

/**
 * Add any new oembed_providers (This is currently a workaround for https://github.com/tatemae/oea/issues/44)
 */
function register_oembed_providers() {
	$providers = array(
		'openassessments.com' => array(
			'regex' => '#https?://(openassessments\.com)/assessments/(.*)#i',
		),
		'openassessments.org' => array(
			'regex' => '#https?://(openassessments\.org)/assessments/(.*)#i',
		),
		'wwwopenassessments.com' => array(
			'regex' => '#https?://(www\.openassessments\.com)/assessments/(.*)#i',
		),
		'wwwopenassessments.org' => array(
			'regex' => '#https?://(www.\openassessments\.org)/assessments/(.*)#i',
		),
		'oea.herokuapp.com' => array(
			'regex' => '#https?://(oea\.herokuapp\.com)/assessments/(.*)#i',
		),
	);

	wp_embed_register_handler( 'assessments.lumenlearning.com', '#https?://assessments\.lumenlearning\.com/assessments/(.*)#i', '\Candela\Utility\lumen_asmnt_embed_handler' );

	foreach ( $providers as $id => $info ) {
		wp_embed_register_handler( $id, $info['regex'], '\Candela\Utility\embed_handler' );
	}
}
add_action( 'init', '\Candela\Utility\register_oembed_providers' );

/**
 * Handle embeds. Called from \Candela\Utility\register_oembed_providers()
 *
 * @param $matches
 * @param $attr
 * @param $url
 * @param rawattr
 */
function embed_handler( $matches, $attr, $url, $rawattr ) {
	// Use the current post as the external id
	$permalink = get_permalink();
	if ( empty( $permalink ) ) {
		$permalink = get_bloginfo( 'url' );
	}

	$parameters = array(
		'confidence_levels=true',
		'enable_start=true',
		'eid=' . esc_url( $permalink ),
	);

	$parms = implode('&', $parameters);

	$embed = sprintf( '<iframe src="//%s/assessments/load?src_url=https://%s/api/assessments/%d.xml&results_end_point=https://%s/api&assessment_id=%d&%s" frameborder="0" style="border:none;width:100%%;height:100%%;min-height:400px;"></iframe>',
		esc_attr( $matches[1] ),
		esc_attr( $matches[1] ),
		esc_attr( $matches[2] ),
		esc_attr( $matches[1] ),
		esc_attr( $matches[2] ),
		$parms
	);

	return apply_filters( 'embed_oea', $embed, $matches, $attr, $url, $rawattr );
}

/**
 * Handle assessment embeds. Called from \Candela\Utility\register_oembed_providers()
 *
 * @param $matches
 * @param $attr
 * @param $url
 * @param rawattr
 */
function lumen_asmnt_embed_handler( $matches, $attr, $url, $rawattr ) {
	$assessment_id = esc_attr( $matches[1] );

	switch_to_blog(1);
	$external_id = get_user_meta( wp_get_current_user()->ID, 'candelalti_external_userid', true );
	$external_context_id = $_GET['lti_context_id'];
	restore_current_blog();

	$parameters = array(
		sprintf( 'src_url=https://assessments.lumenlearning.com/api/assessments/%d.xml', $assessment_id ),
		sprintf( 'assessment_id=%d', $assessment_id ),
		'results_end_point=https://assessments.lumenlearning.com/api',
		'confidence_levels=true',
		'enable_start=true',
		'style=lumen_learning',
		'assessment_kind=formative',
		'external_user_id=' . esc_attr( $external_id ),
		'external_context_id=' . esc_attr( $external_context_id ),
		sprintf( 'iframe_resize_id=lumen_assessment_%d', $assessment_id ),
	);

	$params = implode( '&', $parameters );

	$iframe = <<<HTML
	<iframe id="lumen_assessment_%d" class="resizable" src="https://assessments.lumenlearning.com/assessments/load?%s"
		frameborder="0" style="border:none;width:100%%;height:100%%;min-height:575px;"></iframe>
HTML;

	$embed = sprintf( $iframe, $assessment_id, $params );

	return apply_filters( 'embed_oea', $embed, $matches, $attr, $url, $rawattr );
}

// -----------------------------------------------------------------------------
// BULK EDIT SORTING
// -----------------------------------------------------------------------------

/**
 * Add Revised and Last Modified columns to Chapter bulk edit page
 * (eg. https://pbj/book/wp-admin/edit.php?post_type=chapter)
 *
 * @return $columns The column headings with 'Revised' and 'Last modified by'
 */
function add_revised_column( $columns ) {
	$columns['revised'] = 'Revised';
	$columns[ 'modified_author' ] = 'Last modified by';
	return $columns;
}
add_filter( 'manage_edit-chapter_columns', '\Candela\Utility\add_revised_column' );

/**
 * Print out revised column
 */
function echo_revised_column( $column, $id ) {
	if ( 'revised' == $column ) {
		echo get_post_field( 'post_modified', $id );
	}

	if ( 'modified_author' == $column ) {
		$last_id = get_post_meta( $id, '_edit_last', TRUE );

		if ( ! $last_id ) {
			print '<i>Unknown</i>';
			return;
		}

		$last_user = get_userdata( $last_id );
		print esc_html( $last_user->display_name );
	}
}
add_action( 'manage_chapter_posts_custom_column', '\Candela\Utility\echo_revised_column', 10, 2 );

/**
 * Returns the 'Revised' column
 *
 * @return $columns
 */
function chapter_sortable_columns( $columns ) {
	$columns['revised'] = 'Revised';
	return $columns;
}
add_filter( 'manage_edit-chapter_sortable_columns', '\Candela\Utility\chapter_sortable_columns' );

/**
 * Calls \Candela\Utility\sort_chapters on request
 *
 * @return $columns
 */
function edit_chapter_load() {
	add_filter( 'request', '\Candela\Utility\sort_chapters' );
}
add_action( 'load-edit.php', '\Candela\Utility\edit_chapter_load' );

/**
 * Sort the chapters
 *
 * @param $vars
 * @return $vars
 */
function sort_chapters( $vars ) {
	/* Check if we're viewing the 'chapter' post type. */
	if ( isset( $vars['post_type'] ) && 'chapter' == $vars['post_type'] ) {
		/* Check if 'orderby' is set to 'duration'. */
		if ( isset( $vars['orderby'] ) && 'post_modified' == $vars['orderby'] ) {
			/* Merge the query vars with our custom variables. */
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'post_modified',
					'orderby' => 'meta_value_num',
				)
			);
		}
	}
	return $vars;
}
