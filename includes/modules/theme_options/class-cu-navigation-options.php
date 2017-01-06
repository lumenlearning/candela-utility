<?php

/**
 * Navigation Options
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Modules\ThemeOptions;


class NavigationOptions extends \Pressbooks\Options {

	/**
	 * The value for option: pressbooks_theme_options_navigation_version
	 *
	 * @see upgrade()
	 * @var int
	 */
	static $currentVersion = 1;

	/**
	 * Navigation theme options.
	 *
	 * @var array
	 */
	public $options;

	/**
	 * Navigation theme defaults.
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
	 * Configure the Navigation options tab using the settings API.
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
			'nav_show_header',
			__( 'Show Header', 'navigation' ),
			array( $this, 'renderShowHeaderField' ),
			$_page,
			$_section,
			array(
				 __( 'Display header bar with the course title', 'navigation' )
			)
		);

		add_settings_field(
			'nav_header_link',
			__( 'Header Link', 'navigation' ),
			array( $this, 'renderHeaderLinkField' ),
			$_page,
			$_section,
			array(
				 __( 'Make Course Title a link to the Table of Contents (Header must be selected)', 'navigation' )
			)
		);

		add_settings_field(
			'nav_show_search',
			__( 'Search Field', 'navigation' ),
			array( $this, 'renderShowSearchField' ),
			$_page,
			$_section,
			array(
				 __( 'Enable Search Bar', 'navigation' )
			)
		);

		add_settings_field(
			'nav_show_small_title',
			__( 'Part Title', 'navigation' ),
			array( $this, 'renderShowSmallTitleField' ),
			$_page,
			$_section,
			array(
				 __( 'Display part/module/chapter title', 'navigation' )
			)
		);

		add_settings_field(
			'nav_show_edit_button',
			__( 'Edit Button', 'navigation' ),
			array( $this, 'renderShowEditButtonField' ),
			$_page,
			$_section,
			array(
				 __( 'Enable the edit button', 'navigation' )
			)
		);

		add_settings_field(
			'nav_show_navigation_buttons',
			__( 'Navigation Buttons', 'navigation' ),
			array( $this, 'renderShowNavButtonsField' ),
			$_page,
			$_section,
			array(
				 __( 'Enable the navigation buttons', 'navigation' )
			)
		);

		add_settings_field(
			'nav_show_waymaker_logo',
			__( 'Waymaker Logo', 'navigation' ),
			array( $this, 'renderShowWaymakerLogoField' ),
			$_page,
			$_section,
			array(
				 __( 'Enable Waymaker logo (Candela logo is default)', 'navigation' )
			)
		);

		add_settings_field(
			'nav_show_footer_logo',
			__( 'Footer Logo', 'navigation' ),
			array( $this, 'renderShowFooterLogoField' ),
			$_page,
			$_section,
			array(
				 __( 'Show footer logo', 'navigation' )
			)
		);

		register_setting(
			$_page,
			$_option,
			array( $this, 'sanitize' )
		);
	}

	/**
	 * Display the Navigation options tab description.
	 */
	function display() {
		echo '<p>' . __( 'These options allow customization of the page navigation and are only available when logged in via an LTI launch.', 'navigation' ) . '</p>';
	}

	/**
	 * Render the Navigation options tab form (NOT USED).
	 */
	function render() {}

	/**
	 * Upgrade handler for Navigation options.
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
	 * Render the nav_show_header field.
	 * @param array $args
	 */
	function renderShowHeaderField( $args ) {
		$this->renderCheckbox(
			'nav_show_header',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_header',
			$this->options['nav_show_header'],
			$args[0]
		);
	}

	/**
	 * Render the nav_header_link field.
	 * @param array $args
	 */
	function renderHeaderLinkField( $args ) {
		$this->renderCheckbox(
			'nav_header_link',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_header_link',
			$this->options['nav_header_link'],
			$args[0]
		);
	}

	/**
	 * Render the nav_show_search field.
	 * @param array $args
	 */
	function renderShowSearchField( $args ) {
		$this->renderCheckbox(
			'nav_show_search',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_search',
			$this->options['nav_show_search'],
			$args[0]
		);
	}

	/**
	 * Render the nav_show_small_title field.
	 * @param array $args
	 */
	function renderShowSmallTitleField( $args ) {
		$this->renderCheckbox(
			'nav_show_small_title',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_small_title',
			$this->options['nav_show_small_title'],
			$args[0]
		);
	}

	/**
	 * Render the nav_show_edit_button field.
	 * @param array $args
	 */
	function renderShowEditButtonField( $args ) {
		$this->renderCheckbox(
			'nav_show_edit_button',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_edit_button',
			$this->options['nav_show_edit_button'],
			$args[0]
		);
	}

	/**
	 * Render the nav_show_navigation_buttons field.
	 * @param array $args
	 */
	function renderShowNavButtonsField( $args ) {
		$this->renderCheckbox(
			'nav_show_navigation_buttons',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_navigation_buttons',
			$this->options['nav_show_navigation_buttons'],
			$args[0]
		);
	}

	/**
	 * Render the nav_show_waymaker_logo field.
	 * @param array $args
	 */
	function renderShowWaymakerLogoField( $args ) {
		$this->renderCheckbox(
			'nav_show_waymaker_logo',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_waymaker_logo',
			$this->options['nav_show_waymaker_logo'],
			$args[0]
		);
	}

	/**
	 * Render the nav_show_footer_logo field.
	 * @param array $args
	 */
	function renderShowFooterLogoField( $args ) {
		$this->renderCheckbox(
			'nav_show_footer_logo',
			'pressbooks_theme_options_' . $this->getSlug(),
			'nav_show_footer_logo',
			$this->options['nav_show_footer_logo'],
			$args[0]
		);
	}

	/**
	 * Get the slug for the Navigation options tab.
	 *
	 * @return string $slug
	 */
	static function getSlug() {
			return 'navigation';
	}

	/**
	 * Get the localized title of the Navigation options tab.
	 *
	 * @return string $title
	 */
	static function getTitle() {
		return __( 'Navigation Options', 'navigation' );
	}


	/**
	 * Filter the array of default values for the Navigation options tab.
	 *
	 * @param array $defaults
	 * @return array $defaults
	 */
	static function filterDefaults( $defaults ) {
		return $defaults;
	}

	/**
	 * Get an array of default values for the Navigation options tab.
	 *
	 * @return array $defaults
	 */
	static function getDefaults() {
		return array(
			'nav_show_header' => 0,
			'nav_header_link' => 0,
			'nav_show_search' => 1,
			'nav_show_small_title' => 0,
			'nav_show_edit_button' => 1,
			'nav_show_navigation_buttons' => 0,
			'nav_show_waymaker_logo' => 0,
			'nav_show_footer_logo' => 1,
		);
	}

	/**
	 * Get an array of options which return booleans.
	 *
	 * @return array $options
	 */
	static function getBooleanOptions() {
		return array(
			'nav_show_header',
			'nav_header_link',
			'nav_show_search',
			'nav_show_small_title',
			'nav_show_edit_button',
			'nav_show_navigation_buttons',
			'nav_show_waymaker_logo',
			'nav_show_footer_logo',
		);
	}

	/**
	 * Get an array of options which return strings.
	 *
	 * @return array $options
	 */
	static function getStringOptions() {
		return array();
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