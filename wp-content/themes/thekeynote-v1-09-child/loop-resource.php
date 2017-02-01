<?php
// Our include
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Our variables
$numPosts = (isset($_GET['numPosts'])) ? $_GET['numPosts'] : 0; // requested number of pages
$page = (isset($_GET['pageNumber'])) ? $_GET['pageNumber'] : 0; // the “page” number

$resRegion = (isset($_GET['regionArray'])) ? $_GET['regionArray'] :  array();
$resMobLevel = (isset($_GET['mobLevelArray'])) ? $_GET['mobLevelArray'] :  array();
$resLanguage = (isset($_GET['languageArray'])) ? $_GET['languageArray'] :  array();
$resSort = (isset($_GET['sortBy'])) ? $_GET['sortBy'] : 'Most Recent';

echo $numPosts;
echo $page;

$tax_query = array('relation' => 'AND');
if( !empty($resRegion) )
	$tax_query[] = array( 'taxonomy' => 'region', 'field' => 'slug', 'terms' => $resRegion );
if( !empty($resMobLevel) )
	$tax_query[] = array( 'taxonomy' => 'mobilizationlevel', 'field' => 'slug', 'terms' => $resMobLevel );
if( !empty($resLanguage) )
	$tax_query[] = array( 'taxonomy' => 'language', 'field' => 'slug', 'terms' => $resLanguage );

$args = array(
	'post_type' => 'resource',
	'posts_per_page' => $numPosts,
	'paged' => $page,
	'tax_query' => $tax_query,
	'post_status' => 'publish'		
);

if( $resSort == 'Oldest' ) {
	$args[orderby] = 'date';
	$args[order] = 'ASC';
} else if( $resSort == 'Newest' ) {
	$args[orderby] = 'date';
	$args[order] = 'DESC';
} else if( $resSort == 'Title: Z-A' ) {
	$args[orderby] = 'title';
	$args[order] = 'DESC';
} else {
	// Title: A-Z
	$args[orderby] = 'title';
	$args[order] = 'ASC';
}

$the_query = new WP_Query($args);

if( $the_query->have_posts() ) :
	while( $the_query->have_posts()) : $the_query->the_post();
	
		get_template_part( 'content', 'resource' );

	endwhile;
endif;
wp_reset_postdata();
?>