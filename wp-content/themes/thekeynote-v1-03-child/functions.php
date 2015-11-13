<?php

add_filter('show_admin_bar', '__return_false');

/**
 * Enqueue scripts and styles
 */
function globmob_scripts() {

  // Glyphicons
  if( !is_admin() ) {
    wp_enqueue_style( 'glyphicons-halflings-css', get_stylesheet_directory_uri() . '/css/halflings.css' );
  }

  // Ajax loop loading
  if( is_page('Resources') || is_tax() ) {
    wp_register_script('ajax-loop-resource', get_stylesheet_directory_uri() . '/js/ajax-loop-resource.js', array('jquery'), NULL);
    wp_enqueue_script('ajax-loop-resource');
    wp_localize_script( 'ajax-loop-resource', 'ajax_loop_vars', array('template_path' => get_stylesheet_directory_uri()) );
  }

}
add_action( 'wp_enqueue_scripts', 'globmob_scripts' );

/**
 * Set the URL to redirect to on login.
 *
 * @return string URL to redirect to on login. Must be absolute.
 **/
function my_forcelogin_redirect() {
  return site_url();
}
add_filter('v_forcelogin_redirect', 'my_forcelogin_redirect', 10, 1);

/**
 * Filter Force Login to allow exceptions for specific URLs.
 *
 * @return array An array of URLs. Must be absolute.
 **/
//function my_forcelogin_whitelist() {
//   return array(
//     site_url( '/login/' ),
//     site_url( '/resources/' ),
//     site_url( '/loop-resource.php' ),
//     site_url( '/wp-admin/admin-ajax.php' ),
//     'http://www.globalmobilization.org/wp-content/themes/thekeynote-v1-03-child/',
//     'http://www.globalmobilization.org/wp-content/themes/thekeynote-v1-03-child/loop-resource.php',
//     'http://www.globalmobilization.org/wp-content/themes/thekeynote-v1-03-child/images/ajax-loader.gif'
//   );
//}
//add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);

function my_forcelogin_whitelist( $whitelist ) {
  $whitelist[] = site_url('/login/');
//  $whitelist[] = site_url('/resources/');
  // whitelist any URL within the specified directory
//  if( in_array('wp-admin', explode('/', $_SERVER['REQUEST_URI'])) ) {
//    $whitelist[] = site_url($_SERVER['REQUEST_URI']);
//  }
  return $whitelist;
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);


if ( ! function_exists( 'globmob_field' ) ) :
  /**
   * Prints a custom field for current post.
   */
  function globmob_field($slug, $before = "", $after = "", $postid = null) {
    global $post;
    if( empty($postid) )
      $postid = $post->ID;

    $customdata = get_post_meta($postid, $slug, true);
    if( !empty($customdata) )
      echo $before . $customdata . $after;
  }
endif;

if ( ! function_exists( 'globmob_tax' ) ) :
  /**
   * Prints taxonomy term for current post as comma-seperated string
   */
  function globmob_tax($tax, $getslug = true, $postid = null) {
    global $post;
    if( empty($postid) )
      $postid = $post->ID;

    $term_list = wp_get_post_terms( $postid, $tax );
    /* if ($term_list) {
        echo $term_list[0]->slug;
        echo $term_list[0]->name;
    } */

    $terms_out = "";
    foreach ( $term_list as $term ) {
      if ($terms_out != "")
        $terms_out .= ", ";
      if ($getslug)
        $terms_out .= $term->slug;
      else
        $terms_out .= $term->name;
    }
    return $terms_out;
  }
endif;

if( !function_exists('globmob_tax_url') ) :
  /**
   * Prints taxonomy url for current post complete with <a> tag.
   */
  function globmob_tax_url($tax) {
    global $post;

    $term_list = wp_get_post_terms( $post->ID, $tax );

    //need to convert to use array to handle multiple terms!!
    $output = "";
    foreach ( $term_list as $term ) {
      if( $output != "" )
        $output .= ", ";
      if( is_singular('resource') )
        $output .= '<a href="'.get_term_link($term->slug, $tax).'">'.$term->name.'</a>';
      else
        $output .= "<a data-tax='" . $tax . "' data-term='" . $term->slug . "' class='tax-link'>" . $term->name . "</a>";
    }

    return $output;
  }
endif;

if ( ! function_exists( 'globmob_get_terms_dropdown' ) ) :
  /**
   * Display dropdown menu for taxonomy
   */
  function globmob_get_terms_dropdown( $taxdisplayname, $taxonomies, $args ) {
    $root_url = get_bloginfo('url');
    $myterms = get_terms($taxonomies, $args);
    $output = "<li class='dropitem'>";
    $output .= "<a>" . $taxdisplayname . "</a>";
    $output .= "<ul class='sub-menu'>";

    foreach( $myterms as $term ) {
      $term_taxonomy = $term->taxonomy;
      $term_slug = $term->slug;
      $term_name = $term->name;
      $link = $term_slug;
      $count = $term->count;
      $output .= "<li data-tax='" . $taxonomies . "' data-term='" . $term_slug . "' data-count='" . $count . "' class='menu-item'><a>" . $term_name . "</a></li>";
    }
    $output .= "</ul></li>";
    return $output;
  }
endif; // globmob_get_terms_dropdown


if( !function_exists('globmob_resource_browse') ) :
  /**
   * Display browse navigation for resources
   */
  function globmob_resource_browse() {
   if( !is_singular('resource') && !is_page_template('single-resource.php') ) : ?>
      <div id="content-browse" class="group">
        <span class="menu-label">Refine by:</span>
        <nav class="dropmenu">
          <ul>
            <?php
            $args = array( 'hide_empty' => '1' );
            echo globmob_get_terms_dropdown( 'Region', 'region', $args );
            echo globmob_get_terms_dropdown( 'Mobilization Level', 'mobilizationlevel', $args );
            echo globmob_get_terms_dropdown( 'Language', 'language', $args );
            ?>
          </ul>
        </nav>

        <span class="menu-label">Sort by:</span>
        <nav id="sortmenu" class="dropmenu">
          <ul>
            <li class="dropitem"><a id="current-sort" href="#">Most Recent</a>
              <ul>
                <li data-sortby="Title: A-Z" class="sort-item"><span class='selected-menu-item'>Title: A-Z</span></li>
                <li data-sortby="Title: Z-A" class="sort-item"><a>Title: Z-A</a></li>
                <li data-sortby="Newest" class="sort-item"><a>Newest</a></li>
                <li data-sortby="Oldest" class="sort-item"><a>Oldest</a></li>
              </ul>
            </li>
          </ul>
        </nav>

      </div>
    <?php endif; ?>

    <section id="content-wrap" class="content-bg group">
      <div id="filter-legend" class="content-separator"></div>

    <?php
  }
endif; // globmob_resource_browse

