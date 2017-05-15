<?php

/**
 * Logic for handling embedded content.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Oembed;


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
add_filter( 'embed_oembed_html', '\Candela\Utility\Oembed\embed_oembed_html', 10, 3 );

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

	wp_embed_register_handler( 'assessments.lumenlearning.com', '#https?://assessments\.lumenlearning\.com/assessments/(.*)#i', '\Candela\Utility\Oembed\lumen_asmnt_embed_handler' );
	wp_embed_register_handler( 'www.desmos.com', '#https?://www\.desmos\.com/calculator/([^?]*)#i', '\Candela\Utility\Oembed\lumen_desmos_embed_handler' );
  // handles urls like https://cerego.com/series/10309/learn & https://cerego.com/sets/796507/learn
  wp_embed_register_handler( 'cerego.com', '#https?://cerego\.com/(?:series/\d+/learn|sets/\d+/learn)#i', '\Candela\Utility\Oembed\cerego_embed_handler' );

	foreach ( $providers as $id => $info ) {
		wp_embed_register_handler( $id, $info['regex'], '\Candela\Utility\Oembed\embed_handler' );
	}
}
add_action( 'init', '\Candela\Utility\Oembed\register_oembed_providers' );

/**
 * Handle embeds. Called from \Candela\Utility\Oembed\register_oembed_providers()
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

	$parms = implode( '&', $parameters );

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
 * Handle assessment embeds. Called from \Candela\Utility\Oembed\register_oembed_providers()
 *
 * @param $matches
 * @param $attr
 * @param $url
 * @param rawattr
 */
function lumen_asmnt_embed_handler( $matches, $attr, $url, $rawattr ) {
	$assessment_id = esc_attr( $matches[1] );

	switch_to_blog( 1 );
	$external_id = get_user_meta( wp_get_current_user()->ID, 'candelalti_external_userid', true );
	$external_context_id = $_GET['lti_context_id'];
	restore_current_blog();

	$parameters = array(
        sprintf('assessment_id=%d', $assessment_id),
        'embed=1',
        'external_user_id=' . esc_attr($external_id),
        'external_context_id=' . esc_attr($external_context_id),
        sprintf('iframe_resize_id=lumen_assessment_%d', $assessment_id),
    );

	$params = implode( '&', $parameters );

	$iframe = <<<HTML
	<iframe id="lumen_assessment_%d" class="resizable" src="https://assessments.lumenlearning.com/assessments/load?%s"
		frameborder="0" style="border:none;width:100%%;height:100%%;min-height:300px;"></iframe>
HTML;

	$embed = sprintf( $iframe, $assessment_id, $params );

	return apply_filters( 'embed_oea', $embed, $matches, $attr, $url, $rawattr );
}

/**
 * Handles Desmos Calculator embeds. Called from \Candela\Utility\Oembed\register_oembed_providers()
 *
 * @param $matches
 * @param $attr
 * @param $url
 * @param rawattr
 */
function lumen_desmos_embed_handler( $matches, $attr, $url, $rawattr ) {
	$desmos_activity_id = esc_attr($matches[1]);

	// Create url like: https://www.desmos.com/calculator/u2qz73ufju?embed&editable&apiKey=blah
	$parameters = array(
		'embed',
		'editable',
		'apiKey=' . DESMOS_API_KEY,
	);

	$params = implode('&', $parameters);

	$iframe = <<<HTML
	<iframe src="https://www.desmos.com/calculator/%s?%s"
		frameborder="0" style="border:none;width:100%%;height:100%%;min-height:575px;"></iframe>
HTML;
	$embed = sprintf( $iframe, esc_attr($desmos_activity_id), $params);

	return apply_filters( 'embed_desmos', $embed, $matches, $attr, $url, $rawattr );
}

/**
 * Handles cerego embeds, which just turns them into links for now. Called from \Candela\Utility\Oembed\register_oembed_providers()
 *
 * @param $matches
 * @param $attr
 * @param $url
 * @param rawattr
 */
function cerego_embed_handler( $matches, $attr, $url, $rawattr ) {
  $html = <<<HTML
<a href="$matches[0]" target="_blank">Visit Cerego</a>
HTML;

  return apply_filters( 'embed_cerego', $html, $matches, $attr, $url, $rawattr );
}
