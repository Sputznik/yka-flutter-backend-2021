<?php

class YKAF_COMMENTS extends YKAF_BASE{

	function __construct(){

    /* CREATING CPT USING ORBIT BUNDLE PLUGIN AS DEPENDANCY */
  	add_filter( 'orbit_post_type_vars', function( $post_types ){
      $post_types['ykaf-comment'] = array(
    		'slug' 		=> 'ykaf-comment',
    		'labels'	=> array(
    			'name' 					=> 'YKAF Comments',
    			'singular_name' => 'YKAF Comment',
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
YKAF_COMMENTS::getInstance();
