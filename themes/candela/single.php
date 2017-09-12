<?php
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_header();

		if ( get_option( 'blog_public' ) == '1' || ( get_option( 'blog_public' ) == '0' && current_user_can_for_blog( $blog_id, 'read' ) ) ) {
			edit_post_link( __( 'Edit', 'pressbooks' ), '<span class="edit-link">', '</span>' );
		}
?>

		<main id="main-content">
		<h1 class="entry-title">
			<?php
			if ( $chapter_number = pb_get_chapter_number( $post->post_name ) ) {
				echo "<span>$chapter_number</span>  ";
			}
			the_title();
			?>
		</h1>

		<?php pb_get_links(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class( pb_get_section_type( $post ) ); ?>>
			<div class="entry-content">
				<?php if ( $subtitle = get_post_meta( $post->ID, 'pb_subtitle', true ) ) { ?>
					<h2 class="chapter_subtitle"><?php echo $subtitle; ?></h2>
				<?php } ?>
				<?php if ( $chap_author = get_post_meta( $post->ID, 'pb_section_author', true ) ) { ?>
					<h2 class="chapter_author"><?php echo $chap_author; ?></h2>
				<?php } ?>

				<?php
				the_content();
				if ( get_post_type( $post->ID ) === 'part' ) {
					echo get_post_meta( $post->ID, 'pb_part_content', true );
				}
				?>
			</div><!-- .entry-content -->
		</div><!-- #post-## -->

		<!-- place hidden GUIDs on applicable pages -->
		<?php do_action( 'display_outcome_html', $post->ID ); ?>

		<?php if ( $citation = Candela\Citation::renderCitation( $post->ID ) ) { ?>
			<section role="contentinfo">
				<div class="post-citations sidebar">
					<div role="button" aria-pressed="false" id="citation-header-<?php print $post->ID; ?>" class="collapsed h3-styling"><?php _e( 'Licenses and Attributions' ); ?></div>
					<div id="citation-list-<?php print $post->ID; ?>" style="display: none;">
						<?php print $citation ?>
					</div>
					<script>
						jQuery(document).ready(function($) {
							var pressed = false;
							$("#citation-header-<?php print $post->ID;?>").click(function() {
								pressed = !pressed;
								$("#citation-list-<?php print $post->ID;?>").slideToggle();
								$("#citation-header-<?php print $post->ID;?>").toggleClass('expanded collapsed');
								$("#citation-header-<?php print $post->ID;?>").attr('aria-pressed', pressed);
							});
						});
					</script>
				</div>
			</section>
		<?php } ?>

		</div><!-- #content -->

		<?php get_template_part( 'content', 'social-footer' ); ?>

<?php comments_template( '', true ); ?>

<?php
		get_footer();
	} // endwhile
} else {
	pb_private();
} // endif
?>
