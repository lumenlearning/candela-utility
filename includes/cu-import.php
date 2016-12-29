<?php

/**
 * Logic that adds more import choices to the default import options in Pressbooks.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Import;


add_filter( 'pb_initialize_import', array( '\Pressbooks\Modules\Import\IMSCC\IMSCC', 'init' ) );

/**
 * Adds IMS-CC (Common Cartridge) as an option in import select field.
 *
 * @param array $options
 * @return array
 */
function set_import_select_value( array $options ) {
	$options['imscc'] = __( 'IMS-CC (Common Cartridge)' );
	return $options;
}
add_filter( 'pb_select_import_type', '\Candela\Utility\Import\set_import_select_value' );

/**
 * Adds imscc to list of allowed file types.
 *
 * @param array $file_types
 * @return array
 */
function add_imscc_import_file_type( array $file_types ) {
	$file_types['imscc'] = 'application/zip';
	return $file_types;
}
add_filter( 'pb_import_file_types', '\Candela\Utility\Import\add_imscc_import_file_type' );