<!-- MAIN CONTENT -->
<main id="main-content">

	<?php the_title('<h1 class="entry-title">', '</h1>'); ?>

	<div class="difficulty">
		<?php do_action('display_difficulty_rating', $post->ID); ?>
	</div>

	<div id="post-<?php the_ID(); ?>" <?php post_class( pb_get_section_type( $post ) ); ?>>
		<div class="entry-content">

			<?php
				the_content();
				if ( get_post_type( $post->ID ) === 'part' ) {
					echo get_post_meta( $post->ID, 'pb_part_content', true );
				}
			?>

		</div>
	</div>

	<?php if ( $citation = CandelaCitation::renderCitation( $post->ID ) ): ?>
		<!-- CITATIONS AND ATTRIBUTIONS -->
		<section role="contentinfo">
			<div class="post-citations sidebar">
				<div role="button" aria-pressed="false" id="citation-header-<?php print $post->ID; ?>" class="collapsed license-attribution-dropdown"><?php _e( 'Licenses and Attributions' ); ?></div>
				<div id="citation-list-<?php print $post->ID; ?>" style="display:none;">
					<?php print $citation ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( show_edit_button() ) { ?>
		<!-- EDIT PAGE BUTTON -->
    <?php edit_post_link( __( 'Edit This Page', 'lumen' ), '<div class="edit-page-btn">', '</div>' ); ?>
	<?php } ?>

	<!-- PAGE NAVIGATION BUTTONS -->
	<?php if ( show_lti_buttons() ) {
					lti_get_links();
				} elseif ( show_navigation_buttons() ) {
					ca_get_links();
				} ?>

</div><!-- END CONTENT -->

<?php comments_template( '', true ); ?>
