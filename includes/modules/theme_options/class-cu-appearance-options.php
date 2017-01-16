<?php

/**
 * Appearance Options
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Modules\ThemeOptions;


class AppearanceOptions extends \Pressbooks\Options {

	/**
	 * The value for option: pressbooks_theme_options_appearance_version
	 *
	 * @see upgrade()
	 * @var int
	 */
	static $currentVersion = 1;

	/**
	 * Appearance theme options.
	 *
	 * @var array
	 */
	public $options;

	/**
	 * Appearance theme defaults.
	 *
	 * @var array
	 */
	public $defaults;

	/**
	 * Constructor.
	 *
	 * @param array $options
	 */
	function __construct( array $options ) {
		$this->options = $options;
		$this->defaults = $this->getDefaults();
		$this->booleans = $this->getBooleanOptions();
		$this->strings = $this->getStringOptions();
		$this->integers = $this->getIntegerOptions();
		$this->floats = $this->getFloatOptions();
		$this->predefined = $this->getPredefinedOptions();

		foreach ( $this->defaults as $key => $value ) {
			if ( ! isset( $this->options[ $key ] ) ) {
				$this->options[ $key ] = $value;
			}
		}
	}

	/**
	 * Configure the Appearance options tab using the settings API.
	 */
	function init() {
		$_page = $_option = 'pressbooks_theme_options_' . $this->getSlug();
		$_section = $this->getSlug() . '_options_section';

		if ( false == get_option( $_option ) ) {
			add_option( $_option, $this->defaults );
		}

		add_settings_section(
			$_section,
			$this->getTitle(),
			array( $this, 'display' ),
			$_page
		);

		add_settings_field(
			'toc_header_logo',
			__( 'ToC Header Logo', 'appearance' ),
			array( $this, 'renderTocHeaderLogoField' ),
			$_page,
			$_section,
			array(
				 __( 'Image file path (include file extension)', 'appearance' )
			)
		);

		add_settings_field(
			'toc_header_link',
			__( 'ToC Header Link', 'appearance' ),
			array( $this, 'renderTocHeaderLinkField' ),
			$_page,
			$_section,
			array(
				 __( 'Link to navigate to when logo in table of contents is clicked', 'appearance' )
			)
		);

		add_settings_field(
			'header_logo',
			__( 'Header Logo', 'appearance' ),
			array( $this, 'renderHeaderLogoField' ),
			$_page,
			$_section,
			array(
				 __( 'Image file path (include file extension)', 'appearance' )
			)
		);

		add_settings_field(
			'header_link',
			__( 'Header Link', 'appearance' ),
			array( $this, 'renderHeaderLinkField' ),
			$_page,
			$_section,
			array(
				 __( 'Links to Table of Contents page if left blank', 'appearance' )
			)
		);

		add_settings_field(
			'header_color',
			__( 'Header Color', 'appearance' ),
			array( $this, 'renderHeaderColorField' ),
			$_page,
			$_section,
			array(
				 __( 'Color Hex Code', 'appearance' )
			)
		);

		register_setting(
			$_page,
			$_option,
			array( $this, 'sanitize' )
		);
	}

	/**
	 * Display the Appearance options tab description.
	 */
	function display() {
		echo '<p>' . __( 'These options apply minor presentation alterations to the current theme.', 'appearance' ) . '</p>';
	}

	/**
	 * Render the Appearance options tab form (NOT USED).
	 */
	function render() {}

	/**
	 * Upgrade handler for Appearance options.
	 *
	 * @param int $version
	 */
	function upgrade( $version ) {
		if ( $version < 1 ) {
			$this->doInitialUpgrade();
		}
	}

	/**
	 * Nothing to see here.
	 */
	function doInitialUpgrade() {}

	/**
	 * Render the toc_header_logo field.
	 * @param array $args
	 */
	function renderTocHeaderLogoField( $args ) {
		$this->renderField(
			'toc_header_logo',
			'pressbooks_theme_options_' . $this->getSlug(),
			'toc_header_logo',
			$this->options['toc_header_logo'],
			$args[0]
		);
	}

	/**
	 * Render the toc_header_link field.
	 * @param array $args
	 */
	function renderTocHeaderLinkField( $args ) {
		$this->renderField(
			'toc_header_link',
			'pressbooks_theme_options_' . $this->getSlug(),
			'toc_header_link',
			$this->options['toc_header_link'],
			$args[0]
		);
	}

	/**
	 * Render the header_logo field.
	 * @param array $args
	 */
	function renderHeaderLogoField( $args ) {
		$this->renderField(
			'header_logo',
			'pressbooks_theme_options_' . $this->getSlug(),
			'header_logo',
			$this->options['header_logo'],
			$args[0]
		);
	}

	/**
	 * Render the header_link field.
	 * @param array $args
	 */
	function renderHeaderLinkField( $args ) {
		$this->renderField(
			'header_link',
			'pressbooks_theme_options_' . $this->getSlug(),
			'header_link',
			$this->options['header_link'],
			$args[0]
		);
	}

	/**
	 * Render the header_color field.
	 * @param array $args
	 */
	function renderHeaderColorField( $args ) {
		$this->renderField(
			'header_color',
			'pressbooks_theme_options_' . $this->getSlug(),
			'header_color',
			$this->options['header_color'],
			$args[0]
		);
	}

	/**
	 * Get the slug for the Appearance options tab.
	 *
	 * @return string $slug
	 */
	static function getSlug() {
			return 'appearance';
	}

	/**
	 * Get the localized title of the Appearance options tab.
	 *
	 * @return string $title
	 */
	static function getTitle() {
		return __( 'Appearance Options', 'appearance' );
	}


	/**
	 * Filter the array of default values for the Appearance options tab.
	 *
	 * @param array $defaults
	 * @return array $defaults
	 */
	static function filterDefaults( $defaults ) {
		return $defaults;
	}

	/**
	 * Get an array of default values for the Appearance options tab.
	 *
	 * @return array $defaults
	 */
	static function getDefaults() {
		return array(
			'toc_header_logo' => '',
			'toc_header_link' => '',
			'header_logo' => '',
			'header_link' => '',
			'header_color' => '#007FAB',
		);
	}

	/**
	 * Get an array of options which return booleans.
	 *
	 * @return array $options
	 */
	static function getBooleanOptions() {
		return array();
	}

	/**
	 * Get an array of options which return strings.
	 *
	 * @return array $options
	 */
	static function getStringOptions() {
		return array(
			'toc_header_logo',
			'toc_header_link',
			'header_logo',
			'header_link',
			'header_color',
		);
	}

	/**
	 * Get an array of options which return integers.
	 *
	 * @return array $options
	 */
	static function getIntegerOptions() {
		return array();
	}

	/**
	 * Get an array of options which return floats.
	 *
	 * @return array $options
	 */
	static function getFloatOptions() {
		return array();
	}

	/**
	 * Get an array of options which return predefined values.
	 *
	 * @return array $options
	 */
	static function getPredefinedOptions() {
		return array();
	}

}
