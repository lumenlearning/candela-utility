<?php

/**
 * Customization to the catalog area.
 *
 * @author Lumen Learning
 * @license MIT
 */

namespace Candela\Utility\Catalog;


function enqueue_catalog_stylesheets() {

  if ( strpos( $_SERVER['REQUEST_URI'], 'catalog' ) ) {
    wp_enqueue_style( 'cu-catalog', CU_PLUGIN_URL . 'assets/css/cu-catalog.css' );
  }

}
add_action( 'init', '\Candela\Utility\Catalog\enqueue_catalog_stylesheets' );
