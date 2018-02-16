<?php

/**
 * Adds LaTeX Renderer options via action/filter hooks in Pressbooks.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Latex;

/**
 * Requires the latex classes.
 *
 * @param string $class
 */
function init( $class ) {
	if ( 'katex' == $class ) {
		require_once( CU_PLUGIN_DIR . 'includes/modules/latex/katex.php' );
	} elseif ( 'Automattic_Latex_MOMCOM' == $class ) {
		require_once( CU_PLUGIN_DIR . 'includes/modules/latex/automattic-latex-momcom.php' );
	} elseif ( 'mathjax' == $class ) {
		require_once( CU_PLUGIN_DIR . 'includes/modules/latex/mathjax.php' );
	}
}
add_filter( 'pb_require_latex', '\Candela\Utility\Latex\init' );

/**
 * Adds latex renderer options to select field.
 *
 * @param array $options
 * @return array
 */
function add_latex_renderer_options( array $options ) {
	$options['katex'] = __( 'KaTeX + MathJax in-browser', 'pb_latex' );
	$options['Automattic_Latex_MOMCOM'] = __( 'MyOpenMath.com MimeTeX server', 'pb_latex' );
	$options['mathjax'] = __( 'MathJax in-browser', 'pb_latex' );
	return $options;
}
add_filter( 'pb_add_latex_renderer_option', '\Candela\Utility\Latex\add_latex_renderer_options' );

/**
 * Adds latex renderers to proprietary list of latex renderers.
 *
 * @param array $renderers
 * @return array
 */
function add_latex_renderer_types( array $renderers ) {
	$renderers['katex'] = 'katex';
	$renderers['Automattic_Latex_MOMCOM'] = 'momcom';
	$renderers['mathjax'] = 'mathjax';
	return $renderers;
}
add_filter( 'pb_latex_renderers', '\Candela\Utility\Latex\add_latex_renderer_types' );

/**
 * Enqueues necessary scripts for katex render method.
 *
 * @param string $methods
 */
function enqueue_latex_scripts( $method ) {
	if ( 'katex' == $method || 'Automattic_Latex_MOMCOM' == $method ) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'cu_mathjax', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML.js&delayStartupUntil=configured' );
		wp_enqueue_script( 'cu_asciimathteximg', CU_PLUGIN_URL . 'assets/js/ASCIIMathTeXImg.js' );
		wp_enqueue_script( 'cu_katex', 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.js' );
		wp_enqueue_style( 'cu_katex_css', 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.css' );
		wp_enqueue_script( 'cu_katex_autorender', CU_PLUGIN_URL . 'assets/js/auto-render.js', array( 'cu_katex', 'cu_mathjax', 'jquery' ) );
		add_shortcode( 'latex', '\Candela\Utility\Latex\katex_short_codes' );
	} elseif ( 'mathjax' == $method ) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'cu_mathjax', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.2/MathJax.js?config=TeX-MML-AM_CHTML.js&delayStartupUntil=configured' );
	}
}
add_action( 'pb_enqueue_latex_scripts', '\Candela\Utility\Latex\enqueue_latex_scripts' );

/**
 * Echos the scripts in the head of the page needed for Katex.
 *
 * @param string $method
 */
function latex_config_scripts( $method ) {
	if ( 'katex' == $method ) {
		echo '<script type="text/x-mathjax-config">
			MathJax.Hub.Config({
				skipStartupTypeset: true,
				TeX: { extensions: ["cancel.js", "mhchem.js"] }
			});
		</script>
		<script type="text/javascript">
			MathJax.Hub.Configured();
		</script>';
	} elseif ( 'mathjax' == $method ) {
		echo '<script type="text/x-mathjax-config">
			MathJax.Hub.Config({
				TeX: { extensions: [ "cancel.js", "mhchem.js" ] },
				tex2jax: {inlineMath: [ [ "[latex]", "[/latex]" ] ] }
			});
		</script>
		<script type="text/javascript">
			MathJax.Hub.Configured();
		</script>';
	}
}
add_filter( 'pb_add_latex_config_scripts', '\Candela\Utility\Latex\latex_config_scripts' );

/**
 * Katex shortcodes
 *
 * @param $_atts, $latex
 * @return shortcode
 */
function katex_short_codes( $_atts, $latex ) {
	$latex = preg_replace( array( '#<br\s*/?>#i', '#</?p>#i' ), ' ', $latex );

	$latex = str_replace(
		array( '&quot;', '&#8220;', '&#8221;', '&#039;', '&#8125;', '&#8127;', '&#8217;', '&#038;', '&amp;', "\n", "\r", "\xa0", '&#8211;' ), array( '"', '``', "''", "'", "'", "'", "'", '&', '&', ' ', ' ', ' ', '-' ), $latex
	);

	return '[latex]' . $latex . '[/latex]';
}
add_shortcode( 'latex', '\Candela\Utility\Latex\katex_short_codes' );
