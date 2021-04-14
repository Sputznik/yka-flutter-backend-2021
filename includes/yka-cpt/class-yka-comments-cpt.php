<?php

class YKA_COMMENTS extends YKA_BASE{

	function __construct(){

    /* CREATING CPT USING ORBIT BUNDLE PLUGIN AS DEPENDANCY */
  	add_filter( 'orbit_post_type_vars', function( $post_types ){
      $post_types['yka-comment'] = array(
    		'slug' 		=> 'yka-comment',
    		'labels'	=> array(
    			'name' 					=> 'YKA Comments',
    			'singular_name' => 'YKA Comment',
					'add_new'       => 'Add New',
					'edit_item'			=> 'Edit Comment',
					'add_new_item'  => 'Add New',
					'all_items'     =>  'All Comments'
    		),
				'menu_icon'	=>	'dashicons-format-chat',
    		'public'		=> true,
    		'supports'	=> array( 'title', 'editor', 'thumbnail' )
    	);
      return $post_types;
    } );

	}

}
YKA_COMMENTS::getInstance();