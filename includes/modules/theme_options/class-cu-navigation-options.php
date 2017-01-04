<?php



namespace Candela\Utility\Modules\ThemeOptions;


class NavigationOptions {

	/**
	 * The value for option: pressbooks_theme_options_mpdf_version
	 *
	 * @see upgrade()
	 * @var int
	 */
	static $currentVersion = 0;

	function init() {

	}

	/**
	 * Get the localized title of the mPDF options tab.
	 *
	 * @return string $title
	 */
	static function getTitle() {
		return __( 'Navigation Options', 'pressbooks' );
	}

	static function getDefaults() {

	}

}