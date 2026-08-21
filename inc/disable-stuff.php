<?php

// disable feed links https://kinsta.com/knowledgebase/wordpress-disable-rss-feed/#disable-rss-feed-code
function sivananda_disable_feed()
{
  wp_die(__('No feed available, please visit the <a href="' . esc_url(home_url('/')) . '">homepage</a>!'));
}
add_action('do_feed', 'sivananda_disable_feed', 1);
add_action('do_feed_rdf', 'sivananda_disable_feed', 1);
add_action('do_feed_rss', 'sivananda_disable_feed', 1);
add_action('do_feed_rss2', 'sivananda_disable_feed', 1);
add_action('do_feed_atom', 'sivananda_disable_feed', 1);
add_action('do_feed_rss2_comments', 'sivananda_disable_feed', 1);
add_action('do_feed_atom_comments', 'sivananda_disable_feed', 1);

// remove feed links from header
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);



/*** DISABLE COMMENTS ***/
// https://www.wpzoom.com/blog/disable-comments-wordpress/#h-how-to-turn-off-comments-on-wordpress-manually
// https://www.reddit.com/r/Wordpress/comments/1ainx83/why_isnt_there_a_way_to_completely_fully_disable/

add_filter('rest_endpoints', function($endpoints) {
  $endpointsToRemove = [
    'comments',
    '/wp/v2/comments',
    '/wp/v2/comments/(?P<id>[\d]+)',
  ];

  foreach ($endpointsToRemove as $endpoint) {
    if (isset($endpoints[$endpoint])) {
      unset($endpoints[$endpoint]);
    }
  }

  return $endpoints;
});


add_action('admin_init', function () {
  $post_types = get_post_types();
 
  foreach ($post_types as $post_type) {
    if(post_type_supports($post_type, 'comments')) {
      remove_post_type_support($post_type, 'comments');
      remove_post_type_support($post_type, 'trackbacks');
    }
  }
});
 

add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);


/*** DISABLE PINGBACK AND TRACKBACK ***/
// https://wpsnippets.org/snippet/disable-pingbacks-and-trackbacks/

add_action('init', function () {
  update_option('default_pingback_flag', 0);
  update_option('default_ping_status', 'closed');
  update_option('default_trackback_flag', 0);
  update_option('default_trackback_status', 'closed');
});


/*** DISABLE XML RPC ***/
// https://kinsta.com/blog/xmlrpc-php/#how-to-disable-xmlrpcphp-without-a-plugin
add_filter('xmlrpc_enabled', '__return_false');