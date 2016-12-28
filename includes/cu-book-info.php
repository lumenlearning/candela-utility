<?php

/**
 * Logic that changes some of the functionality within the admin area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility;


/**
 * Add metadata information for Candela
 */
function add_meta_boxes() {

	lumen_course_info_meta_box();
	cover_image_attribution_meta_box();

	add_meta_box( 'nav-links', 'Edit Navigation Links', __NAMESPACE__ . '\nav_links', 'chapter', 'side', 'low' );

}
add_action( 'custom_metadata_manager_init_metadata', '\Candela\Utility\add_meta_boxes' );

/**
 * Defines "Book Info > Edit Book Information > Lumen Course Information" metabox
 */
function lumen_course_info_meta_box() {

	x_add_metadata_group( $group = 'lumen-course-information', 'metadata', array(
		'label' => __( 'Lumen Course Information', 'pressbooks' ),
		'priority' => 'high',
	) );

	$fields = array(
		'candela-credit-statement' => array(
			'label' => __('Credit Statement'),
			'field_type' => 'textarea',
			'description' => __('A short acknowledgement of institutions, funders and/or contributors responsible for developing the course. This will be displayed on the Table of Contents.'),
		),
		'candela-previous-textbook-cost' => array(
			'label' => __('Previous Textbook Cost'),
			'description' => __('Previous textbook cost rounded down to the nearest dollar. This information is not shown in the public view of the course.'),
		),
	);

	render_meta_box_fields( $group, $fields );

}

/**
 * Defines "Book Info > Edit Book Information > Cover Image Attribution" metabox
 */
function cover_image_attribution_meta_box() {

	x_add_metadata_group( $group = 'cover-image-attributions', 'metadata', array(
		'label' => __( 'Cover Image Attributions' ),
		'priority' => 'low',
	) );

	$fields = array(
		'attribution-type' => array(
			'label' => __( 'Type' ),
			'field_type' => 'select',
			'values' => array(
				'' => 'Choose citation type',
				'original' => 'CC licensed content, Original',
				'cc' => 'CC licensed content, Shared previously',
				'cc-attribution' => 'CC licensed content, Specific attribution',
				'copyrighted_video' => 'All rights reserved content',
				'pd' => 'Public domain content',
				'lumen' => 'Lumen Learning authored content'
			)
		),
		'attribution-description' => array(
			'label' => __( 'Description' ),
		),
		'attribution-author' => array(
			'label' => __( 'Author' ),
		),
		'attribution-organization' => array(
			'label' => __( 'Organization' ),
		),
		'attribution-url' => array(
			'label' => __( 'URL' ),
		),
		'attribution-project' => array(
			'label' => __( 'Project' ),
		),
		'attribution-licensing' => array(
			'label' => __( 'Licensing' ),
			'field_type' => 'select',
			'values' => array(
				'' => 'Choose licensing',
				'pd' => 'Public Domain: No Known Copyright',
				'cc0' => 'CC0: No Rights Reserved',
				'cc-by' => 'CC BY: Attribution',
				'cc-by-sa' => 'CC BY-SA: Attribution-ShareAlike',
				'cc-by-nd' => 'CC BY-ND: Attribution-NoDerivatives',
				'cc-by-nc' => 'CC BY-NC: Attribution-NonCommercial',
				'cc-by-nc-sa' => 'CC BY-NC-SA: Attribution-NonCommercial-ShareAlike',
				'cc-by-nc-nd' => 'CC BY-NC-ND: Attribution-NonCommercial-NoDerivatives',
			),
		),
		'attribution-license-terms' => array(
			'label' => __( 'License Terms' ),
		)
	);

	render_meta_box_fields( $group, $fields );

}

/**
 * Helper function for rendering metafields for metabox groups
 */
function render_meta_box_fields( $group, $fields ) {

	foreach ($fields as $key => $info) {
		$info['group'] = $group;
		x_add_metadata_field( $key, 'metadata', $info);
	}

}
