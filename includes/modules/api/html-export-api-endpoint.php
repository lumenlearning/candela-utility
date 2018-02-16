<?php

namespace Candela\Utility\Modules\Api;
use Pressbooks\Metadata;

/**
 * Fetch a URL that is valid for 5 minutes to GET an "XHTML" PressBooks export
 * https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
 *
 * @return array
 */
function export_html($request) {
  $args = array();
  $foo = new \Pressbooks\Modules\Export\Xhtml\Xhtml11( $args );
  $res['html_url_valid_until'] = time() + (60 * 5);
  $res['temp_html_url'] = $foo->url;

  $res['blog_id'] = get_current_blog_id();
  $res['book_name'] = get_bloginfo( 'name' );
  $res['admin_url'] = get_admin_url();
  $res['site_url'] = get_site_url();
  $meta = new Metadata();
  $res['is_lumen_master'] = metadata_exists('post', $meta->getMetaPost()->ID, 'candela-is-master-course');

  return $res;
}

add_action('rest_api_init', function () {
  register_rest_route('lumen/candela/v1', '/export_html', array(
      'methods' => 'GET',
      'callback' => 'Candela\Utility\Modules\Api\export_html',
      'permission_callback' => function () {
        return current_user_can( 'export' );
      }
  ));
});
