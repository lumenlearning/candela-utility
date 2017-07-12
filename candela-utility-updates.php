<?php
namespace Candela\Utility\Updates;

$update = get_option( 'candela_utility_updates', '1' );
switch ( $update ) {
	case '1':
		update_0001();
		break;
}

function update_0001(){
  // https://codex.wordpress.org/Function_Reference/add_role
  $result = add_role(
      'reviewer',
      __( 'Reviewer' ),
      array(
          'read'               => true,
          'read_private_posts' => true,  // pressbooks capability
          'edit_posts'         => false,
          'edit_others_posts'  => false,
          'delete_posts'       => false
      )
  );

  //  $result = remove_role('reviewer');
  update_option( 'candela_utility_updates', '2' );
}

