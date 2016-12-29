<?php

/**
 * Adds "Revised" and "Last Modified" columns to Chapter bulk edit page.
 * (eg. https://pbj/book/wp-admin/edit.php?post_type=chapter)
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility;


/**
 * Add Revised and Last Modified columns
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
 * Print out author name that last revised the book
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
