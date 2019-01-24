<?php
/**
*/

namespace Candela\Utility\AssignmentMeta;
if( ! defined('CANDELA_ASSIGNMENT_POINTS')){
    define('CANDELA_ASSIGNMENT_POINTS', '_cu_assignment_points_possible');
}

// Register metadata to show in WP JSON API
add_action( 'rest_api_init', function() {
  register_meta( 'post', CANDELA_ASSIGNMENT_POINTS, [
    'show_in_rest' => true,
    'single' => true,
    'type' => 'string'
  ] );
} );

add_action('admin_init', '\Candela\Utility\AssignmentMeta\candela_on_admin_init');

//Initialize
function candela_on_admin_init() {
$types = array( 'chapter' );
    foreach($types as $type){
        add_meta_box('points_possible',
        __('Assignment Meta', 'textdomain'),
        '\Candela\Utility\AssignmentMeta\assignment_metabox_render',
        $type,
        'normal',
        'low'
        );
    }
}

//Render fields
function assignment_metabox_render($post) {
    $data = get_post_meta($post->ID, CANDELA_ASSIGNMENT_POINTS, true);
    ?>
    <div class="inside">
        <label for="assignment_points_possible"><?php _e( "Set the points possible if this should be an assignment.", 'textdomain' ); ?></label>
        <input id="assignment_points_possible" class="widefat" type="text" name="candela_assignment_points_possible" placeholder="ie. 20" pattern="[0-9]*" value="<?php echo (isset($data)) ? esc_attr($data) : ''; ?>"/>
    </div>
    <?php
}


//Update metadata when user saves post
add_action('wp_insert_post', '\Candela\Utility\AssignmentMeta\candela_assignment_save_meta_value', 10, 2);

function candela_assignment_save_meta_value($id) {
    if(isset($_POST['candela_assignment_points_possible']))
    $points_possible = intval($_POST['candela_assignment_points_possible']);

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
    return $id;
    if (!current_user_can('edit_posts'))
    return;

    if (!isset($id))
    $id = (int) $_REQUEST['post_ID'];

    if (isset($points_possible)) {
        update_post_meta($id, CANDELA_ASSIGNMENT_POINTS, $points_possible);
    } else {
        delete_post_meta($id, CANDELA_ASSIGNMENT_POINTS);
    }
}

//Add Candela assignment to import meta
  add_filter( 'pb_import_metakeys', '\Candela\Utility\AssignmentMeta\get_import_metakeys' );

  function get_import_metakeys( $fields ) {
      $fields[] = CANDELA_ASSIGNMENT_POINTS;
      return $fields;
  }
