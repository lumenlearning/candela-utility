<?php

get_header();

$metadata = pb_get_book_information();

if ( get_option( 'blog_public' ) == '1' || ( get_option( 'blog_public' ) == '0' && current_user_can_for_blog( $blog_id, 'read' ) ) ) {
	if ( have_posts() ) {
		the_post();

		get_template_part( 'page-cover', 'top-block' );
		get_template_part( 'page-cover', 'third-block' );
		get_template_part( 'page-cover', 'second-block' );

	} else {
		get_template_part( 'page-cover', 'private-block' );
	}
}

get_footer();
