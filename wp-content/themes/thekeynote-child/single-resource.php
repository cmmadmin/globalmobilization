<?php
/**
Template Name: Resources
 */

get_header();

globmob_resource_browse();

echo "<div>";
while ( have_posts() ) : the_post();

	get_template_part( 'content', 'resource' );

	// If comments are open or we have at least one comment, load up the comment template
	if ( comments_open() || '0' != get_comments_number() )
		comments_template();

endwhile;

echo "</div>";

echo "</section>";

get_footer();