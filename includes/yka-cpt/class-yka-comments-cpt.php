<?php

class YKA_COMMENTS extends YKA_BASE{

	function __construct(){

    /* CREATING CPT USING YKA CPT BASE */
  	add_filter( 'yka_post_type_vars', function( $post_types ){
      $post_types['yka-comment'] = array(
    		'slug' 		=> 'yka-comment',
    		'labels'	=> array(
    			'name' 					=> 'Comments',
    			'singular_name' => 'Comment',
					'add_new'       => 'Add New',
					'edit_item'			=> 'Edit Comment',
					'add_new_item'  => 'Add New',
					'all_items'     => 'All Comments'
    		),
				'menu_icon'	=>	'dashicons-format-chat',
    		'public'		=> true,
				'hierarchical' => true,
    		'supports'	=> array( 'title', 'editor', 'thumbnail' ),
				'show_in_rest' => true
			);
      return $post_types;
    } );

		add_action( 'admin_menu', function(){
			remove_menu_page( 'edit-comments.php' );
		} );
		add_action('init', function(){
			remove_post_type_support( 'post', 'comments' );
    	remove_post_type_support( 'page', 'comments' );
		}, 100);

		add_action( 'wp_before_admin_bar_render', function(){
			global $wp_admin_bar;
    	$wp_admin_bar->remove_menu('comments');
		} );

	}

}
YKA_COMMENTS::getInstance();
