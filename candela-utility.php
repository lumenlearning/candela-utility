<?php

/**
 * Candela Utility
 * Plugin Name: Candela Utility
 * Description: Candela Utility is a helper plugin that manages additional configuration and bootstrapping on top of Pressbooks.
 * Version: 0.2
 * Author: Lumen Learning
 * Author URI: http://lumenlearning.com
 * Text Domain: lumen
 * License: GPLv2 or later
 * GitHub Plugin URI: https://github.com/lumenlearning/candela
 */

namespace Candela\Utility;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// -----------------------------------------------------------------------------
// SETUP
// -----------------------------------------------------------------------------

if ( ! defined( 'CU_PLUGIN_VERSION' ) ) {
	define( 'CU_PLUGIN_VERSION', '0.2' );
}

if ( ! defined( 'CU_PLUGIN_DIR' ) ) {
	define( 'CU_PLUGIN_DIR', __DIR__ . '/' );
}

if ( ! defined( 'CU_PLUGIN_URL' ) ) {
	define( 'CU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// -----------------------------------------------------------------------------
// CLASS INCLUDES
// -----------------------------------------------------------------------------

include CU_PLUGIN_DIR . 'includes/cu-functions.php';
include CU_PLUGIN_DIR . 'includes/cu-admin-theme.php';
include CU_PLUGIN_DIR . 'includes/cu-book-theme.php';
include CU_PLUGIN_DIR . 'includes/cu-gettext.php';
include CU_PLUGIN_DIR . 'includes/cu-book-info.php';
include CU_PLUGIN_DIR . 'includes/class-cu-editor.php';


init();

function init() {
	add_action( 'init', '\Candela\Utility\wp2_init' );
	add_filter( 'embed_oembed_html', '\Candela\Utility\embed_oembed_html', 10, 3 );
}

/*
 * Initializes registeration of book themes and oembed provider list
 */
function wp2_init() {
	register_oembed_providers();
}

/**
 * Filter embed_oembed_html.
 *
 * Replace all 'http://' links with 'https://
 */
function embed_oembed_html($html, $url, $attr) {
	if ( is_ssl() ) {
		return str_replace('http://', 'https://', $html);
	}

	return $html;
}

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

  wp_embed_register_handler( 'assessments.lumenlearning.com',
      '#https?://assessments\.lumenlearning\.com/assessments/(.*)#i',
      '\Candela\Utility\lumen_asmnt_embed_handler' );

	foreach ($providers as $id => $info ) {
		wp_embed_register_handler( $id, $info['regex'], '\Candela\Utility\embed_handler' );
	}
}

/**
 * Handle embeds
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
		'eid=' . esc_url($permalink),
	);

	$parms = implode('&', $parameters);

	$embed = sprintf( '<iframe src="//%s/assessments/load?src_url=https://%s/api/assessments/%d.xml&results_end_point=https://%s/api&assessment_id=%d&%s" frameborder="0" style="border:none;width:100%%;height:100%%;min-height:400px;"></iframe>',
		esc_attr($matches[1]),
		esc_attr($matches[1]),
		esc_attr($matches[2]),
		esc_attr($matches[1]),
		esc_attr($matches[2]),
		$parms
	);

	return apply_filters( 'embed_oea', $embed, $matches, $attr, $url, $rawattr );
}

function lumen_asmnt_embed_handler( $matches, $attr, $url, $rawattr ) {
  $assessment_id = esc_attr($matches[1]);

  switch_to_blog(1);
  $external_id = get_user_meta( wp_get_current_user()->ID, 'candelalti_external_userid', true );
	$external_context_id = $_GET['lti_context_id'];
  restore_current_blog();

	$parameters = array(
      sprintf('src_url=https://assessments.lumenlearning.com/api/assessments/%d.xml', $assessment_id),
      sprintf('assessment_id=%d', $assessment_id),
      'results_end_point=https://assessments.lumenlearning.com/api',
      'confidence_levels=true',
      'enable_start=true',
      'style=lumen_learning',
      'assessment_kind=formative',
      'external_user_id=' . esc_attr($external_id),
			'external_context_id=' . esc_attr($external_context_id),
      sprintf('iframe_resize_id=lumen_assessment_%d', $assessment_id),
  );

	$params = implode('&', $parameters);

  $iframe = <<<HTML
  <iframe id="lumen_assessment_%d" class="resizable" src="https://assessments.lumenlearning.com/assessments/load?%s"
  frameborder="0" style="border:none;width:100%%;height:100%%;min-height:575px;"></iframe>
HTML;
	$embed = sprintf( $iframe, $assessment_id, $params);

	return apply_filters( 'embed_oea', $embed, $matches, $attr, $url, $rawattr );
}

/**
 * Returns the attribution type (used for cover image licensing)
 */
function the_attribution_type( $type ) {

	$types = array(
		'original' => 'CC Licensed Content, Original',
		'cc' => 'CC Licensed Content, Shared Previously',
		'cc-attribution' => 'CC Licensed Content, Specific Attribution',
		'copyrighted_video' => 'All Rights Reserved Content',
		'pd' => 'Public Domain Content',
		'lumen' => 'Lumen Learning Authored Content'
	);

	foreach( $types as $key => $value ) {
		if ( $key === $type ) {
			return $value;
		}
	}

}

/**
 * Returns an array of the License long name (label) and creativecommons
 * definition (link)
 *
 * @return array
 */
function the_attribution_license( $short_attribution ) {

	$attributions = array(
		'pd' =>  array(
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
			'label' =>  __( 'All Rights Reserved' ),
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

	$book_information = array_map('array_pop', $data);

	return $book_information;
}

function nav_links() {
  echo(edit_post_link("Previous", '', '', get_pb_page_id("prev")) . " - ");
  echo(edit_post_link("Next", '', '', get_pb_page_id('next')));
}

/**
 * Fetch next or previous Pressbooks post ID
 * This is taken from PB's inner code to find the next page
 *
 * @param string $what prev, next
 *
 * @return ID of requested post
 */
function get_pb_page_id( $what = 'next' ) {

  global $blog_id;
  global $post;

  $current_post_id = $post->ID;
  $book_structure = \PressBooks\Book::getBookStructure();
  $order = $book_structure['__order'];
  $pos = array_keys( $order );

  $what = ( $what == 'next' ? 'next' : 'prev' );

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
    if ( $order[$post_id]['post_status'] == 'publish' ) {
      break;
    } elseif ( current_user_can_for_blog( $blog_id, 'read' ) ) {
      break;
    } else {
      $what( $pos );
    }
  }

  return $post_id;
}
