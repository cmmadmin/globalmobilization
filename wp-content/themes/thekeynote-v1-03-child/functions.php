<?php add_filter('show_admin_bar', '__return_false'); 

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
function my_forcelogin_whitelist() {
   return array(
     site_url( '/login/' )
   );
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);

?>