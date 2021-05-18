<?php

	require_once('yka-rest-response.php');

	$controllers = array(
		'class-yka-rest-conversations-controller.php',
		'class-yka-rest-comments-controller.php'
	);

	foreach( $controllers as $controller ){
		require_once('endpoints/'.$controller);
	}




	add_action( 'rest_api_init', function () {

		$conversations_controller = new YKA_Conversations_Controller;
	  $conversations_controller->register_routes();

		$comments_controller = new YKA_Comments_Controller;
	  $comments_controller->register_routes();

		// CPT CONVERSATION
		register_rest_field( 'conversation', 'number_of_bookmarks', array(
 				'get_callback'    => function( $object, $field_name, $request ){
					return '0';
				},
 				'schema'          => null,
 			)
  	);
		register_rest_field( 'conversation', 'tags', array(
 				'get_callback'    => function( $object, $field_name, $request ){
					return wp_get_object_terms( $object['id'], 'topics', array( 'fields' => 'names' ) );
				},
 				'schema'          => null,
 			)
  	);
		register_rest_field( 'conversation', 'author_data', array(
 				'get_callback'    => 'get_yka_author_data',
 				'schema'          => null,
 			)
  	);

		register_rest_field( 'conversation', 'comments_data', array(
					'get_callback'    => 'get_yka_comments_data',
					'schema'          => null,
		  )
	 	);

		register_rest_field( 'conversation', 'attachments', array(
 				'get_callback'    => 'get_attachments_list',
 				'schema'          => null,
 			)
 	 	);


 	 // CPT YKA-COMMENT
	 register_rest_field( 'yka-comment', 'attachments', array(
 				'get_callback'    => 'get_attachments_list',
 				'schema'          => null,
 			)
 	 );

	 register_rest_field( 'yka-comment', 'author_data', array(
				'get_callback'    => 'get_yka_author_data',
				'schema'          => null,
			)
 	 );



	} );


	function get_yka_author_data( $object ){
		return array(
			'name'    => get_the_author_meta( 'display_name', $object['author'] ),
			'avatar'  => ''
		);
	}

	function get_yka_comments_data( $object ){
		return array(
			'number_of_comments'    => count( get_pages( array( 'child_of' => $object['id'], 'post_type' => 'yka-comment') ) ),
			'number_of_users'  			=> ''
		);
	}

	function get_attachments_list( $object ) {

		$post_id = $object['id'];

		$post_attachments = array(
			'images'   => get_yka_attachment( $post_id, 'image' ),
			'audio'    => get_yka_attachment( $post_id, 'audio' )
		);
		return $post_attachments;

	}


function get_yka_attachment( $postId, $attachment_type ){
	$attachments = get_attached_media( $attachment_type, $postId );
	$attachments_arr = array();
	foreach ( $attachments as $attachment ) {
		$conv_attachment = array(
			'id' => $attachment->ID,
			'url' => wp_get_attachment_url( $attachment->ID ),
			'mime_type' => $attachment->post_mime_type
		);
		array_push( $attachments_arr, $conv_attachment );
	}

	return $attachments_arr;
}
