<?php add_action( 'pb_catalog_overlay', 'cu_the_overlay' );

function cu_the_overlay() {
	require_once( CU_PLUGIN_DIR . 'includes/catalog/cu-catalog-overlay.php' );
}

?>
