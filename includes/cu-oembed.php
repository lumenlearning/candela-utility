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
 * Handle assessment embeds. Called from \Candela\Utility\Oembed\register_oembed_providers()
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
