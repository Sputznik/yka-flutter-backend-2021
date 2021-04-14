<?php

class YKA_CONVERSATIONS extends YKA_BASE{

	function __construct(){

		add_filter( 'yka-admin-meta-box-fields', function( $fields ){
			$fields['yka-conversation'] = array( $this, 'render_meta_box' );
			return $fields;
		} );


		add_action( 'yka-save-meta-box', array( $this, 'save_meta_box' ), 10, 1 );

		add_action( 'wp_ajax_yka_conversations_json', array( $this, 'get_json' ) );

    /* CREATING CPT USING ORBIT BUNDLE PLUGIN AS DEPENDANCY */
  	add_filter( 'orbit_post_type_vars', function( $post_types ){
      $post_types['conversation'] = array(
    		'slug' 		=> 'conversation',
    		'labels'	=> array(
    			'name' 					=> 'Conversations',
    			'singular_name' => 'Conversation',
					'add_new'       => 'Add New',
					'edit_item'			=> 'Edit Conversation',
					'add_new_item'  => 'Add New',
					'all_items'     =>  'All Conversations'
    		),
				'menu_icon'	=>	'dashicons-groups',
    		'public'		=> true,
    		'supports'	=> array( 'title', 'editor', 'thumbnail', 'author' )
    	);
      return $post_types;
    } );

		/* PUSH INTO THE GLOBAL VARS OF ORBIT TAXNOMIES */
		add_filter( 'orbit_taxonomy_vars', function( $orbit_tax ){

			$orbit_tax['topics']	= array(
		    'label'			  => 'Topics',
		    'slug' 			  => 'topics',
		    'post_types'	=> array( 'conversation' )
		  );

		  return $orbit_tax;

		} );

	}

	function save_meta_box( $post_id ) {
		if( isset( $_POST['yka_conversation_id'] ) ){
			global $wpdb;
			$wpdb->update(
				$wpdb->posts,
				array( 'post_parent' => $_POST['yka_conversation_id'] ),
				array( 'ID' => $post_id ),
				array( '%d' ),
				array( '%d' )
			);

		}
	}

	function render_meta_box( $post ){
		$field = array(
			'label'               => 'Select Conversation',
			'slug'	              => 'yka_conversation_id',
			'value'	              => $post->post_parent,
			'placeholder'         => 'Type Something',
			'autocomplete_value'	=> $post->post_parent ? get_the_title( $post->post_parent ) : "",
			'url'	                => admin_url('admin-ajax.php?action=yka_conversations_json')
		);
		echo "<div data-behaviour='yka-autocomplete' data-field='".wp_json_encode( $field )."'></div>";
	}

	function get_json(){
		global $wpdb;
		$search = $_GET['term'];
		$query = "SELECT ID, post_title FROM ".$wpdb->posts." WHERE post_title LIKE '%".$search."%' AND post_type='conversation' ORDER BY post_title ASC LIMIT 0,10";
		$posts = array();
		foreach($wpdb->get_results($query) as $row){
			array_push( $posts, array( 'id' => $row->ID, 'value'=> $row->post_title ) );
		}
		wp_send_json( $posts );
	}


}
YKA_CONVERSATIONS::getInstance();
