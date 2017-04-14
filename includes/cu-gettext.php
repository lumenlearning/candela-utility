<?php

/**
 * Custom gettext.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Gettext;

/**
 * Returns all translated text belonging to the "pressbooks" domain
 */
function gettext_with_context( $translated_text, $text, $context, $domain ) {
	if ( 'pressbooks' == $domain ) {
		$translated_text = gettext( $translated_text, $text, $domain );
	}

	return $translated_text;
}
add_filter( 'gettext_with_context', '\Candela\Utility\Gettext\gettext_with_context', 20, 4 );

/**
 * Translates "pressbooks" domain text to lumen-specific use case
 */
function gettext( $translated_text, $text, $domain ) {
	if ( 'pressbooks' == $domain ) {
		$translations = array(
			'Chapter Metadata' => 'Page Metadata',
			'Chapter Short Title (appears in the PDF running header)' => 'Page Short Title (appears in the PDF running header)',
			'Chapter Subtitle (appears in the Web/ebook/PDF output)' => 'Page Subtitle (appears in the Web/ebook/PDF output)',
			'Chapter Author (appears in Web/ebook/PDF output)' => 'Page Author (appears in Web/ebook/PDF output)',
			'Promote your book, set individual chapters privacy below.' => 'Promote your book, set individual page\'s privacy below.',
			'Add Chapter' => 'Add Page',
			'Reordering the Chapters' => 'Reordering the Pages',
			'Chapter 1' => 'Page 1',
			'Imported %s chapters.' => 'Imported %s pages.',
			'Chapters' => 'Pages',
			'Chapter' => 'Page',
			'Add New Chapter' => 'Add New Page',
			'Edit Chapter' => 'Edit Page',
			'New Chapter' => 'New Page',
			'View Chapter' => 'View Page',
			'Search Chapters' => 'Search Pages',
			'No chapters found' => 'No pages found',
			'No chapters found in Trash' => 'No pages found in Trash',
			'Chapter numbers' => 'Page numbers',
			'display chapter numbers' => 'display page numbers',
			'do not display chapter numbers' => 'do not display page numbers',
			'Chapter Numbers' => 'Page Numbers',
			'Display chapter numbers' => 'Display page numbers',
			'This is the first chapter in the main body of the text. You can change the ' => 'This is the first page in the main body of the text. You can change the ',
			'text, rename the chapter, add new chapters, and add new parts.' => 'text, rename the page, add new pages, and add new parts.',
			'Only users you invite can see your book, regardless of individual chapter ' => 'Only users you invite can see your book, regardless of individual page ',
		);

		if ( isset( $translations[ $translated_text ] ) ) {
			$translated_text = $translations[ $translated_text ];
		}
	}

	return $translated_text;
}
add_filter( 'gettext', '\Candela\Utility\Gettext\gettext', 20, 3 );
