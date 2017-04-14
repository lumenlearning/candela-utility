<?php
if ( ! is_front_page() ) {
	if ( ! isset( $_GET['content_only'] ) ) {
		get_sidebar();
	}
?>

	</div><!-- #wrap -->

<?php } ?>

<div class="footer">
	<div class="row">
		<!-- logo options -->
		<?php if ( show_logo() ) { ?>
			<?php if ( show_waymaker_logo() ) { ?>
				<img class="lumen-footer-logo" src="<?php echo get_stylesheet_directory_uri(); ?>/images/FooterLumenWaymaker.png" alt="Footer Logo Lumen Waymaker" />
			<?php } else { ?>
				<img class="lumen-footer-logo" src="<?php echo get_stylesheet_directory_uri(); ?>/images/FooterLumenCandela.png" alt="Footer Logo Lumen Candela" />
			<?php } ?>
		<?php } ?>
	</div><!-- #inner -->
</div><!-- #footer -->

<?php wp_footer(); ?>

</body>
</html>
