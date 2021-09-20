<?php

class YKA_LEARNING_CAPSULES extends YKA_BASE{

	function __construct(){

    /* CREATING CPT USING YKA CPT BASE */
  	add_filter( 'yka_post_type_vars', function( $post_types ){
      $post_types['learning-capsules'] = array(
    		'slug' 		=> 'learning-capsules',
    		'labels'	=> array(
    			'name' 					=> 'Learning Capsules',
    			'singular_name' => 'Learning Capsule',
					'add_new'       => 'Add New Capsule',
					'edit_item'			=> 'Edit Capsule',
					'add_new_item'  => 'Add New Capsule',
					'all_items'     => 'All Capsules'
    		),
				'menu_icon'	=>	'dashicons-welcome-learn-more',
    		'public'		=> true,
    		'supports'	=> array( 'title', 'editor', 'thumbnail' ),
        'show_in_rest' => true
    	);
      return $post_types;
    } );

	}

}
YKA_LEARNING_CAPSULES::getInstance();
