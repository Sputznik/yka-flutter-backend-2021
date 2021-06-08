<?php

	require_once('yka-rest-response.php');

	add_action( 'rest_api_init', function () {

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
				'update_callback'	=> function( $value, $post, $field_name, $request, $object_type ){
					wp_set_object_terms( $post->ID, $value, 'topics' );
				},
 				'schema'          => array(
			    'description'   => 'Conversation tags',
			    'type'          => 'array',
					'context'       =>   array( 'view', 'edit' )
				),
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
	 register_rest_field( 'yka-comment', 'quoted-comment', array(
 				'get_callback'    => 'get_quoted_comment',
 				'update_callback' => function( $value, $post, $field_name ) {
					update_post_meta( $post->ID, $field_name, $value );
				},
				'schema'          => array(
			    'description'   => 'Quoted comment id',
			    'type'          => array('integer'),
					'context'       => array( 'view', 'edit' )
				),
			)
 	 );
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

	/* USER REGISTRATION ENDPOINT */
  register_rest_route('yka/v1', 'register', array(
    'methods' => 'POST',
    'callback' => 'yka_user_registration',
  )	);


	} );


	function get_quoted_comment( $post,  $field_name, $request ){
		$quoted_comment = (int) get_post_meta( $post['id'], $field_name, true );
    $quoted_comment = !empty( $quoted_comment ) ? $quoted_comment : null;
		return $quoted_comment;
	}

	function get_yka_author_data( $object ){
		return array(
			'name'    => get_the_author_meta( 'display_name', $object['author'] ),
			'avatar'  => ''
		);
	}

	function get_yka_comments_data( $object ){
		return array(
			'number_of_comments'    => count( get_pages( array( 'child_of' => $object['id'], 'post_type' => 'yka-comment') ) ),
			'number_of_users'  			=> count( get_yka_commented_users( $object['id'] ) )
		);
	}

	function get_yka_commented_users( $comment_parent_id ){
		global $wpdb;

		$authors = $wpdb->get_results("
	    SELECT
	        {$wpdb->prefix}users.ID
	    FROM
	        wp_users
	    INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}users.ID = {$wpdb->prefix}posts.post_author
	    WHERE
	        {$wpdb->prefix}posts.post_type = 'yka-comment' AND
					{$wpdb->prefix}posts.post_status = 'publish' AND {$wpdb->prefix}posts.post_parent = $comment_parent_id
	    GROUP BY
	        {$wpdb->prefix}users.ID;
	  ");

		return $authors;

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

function yka_user_registration( $request = null ) {

	$response 	= array();
	$parameters = $request->get_params();
	$email 			= sanitize_text_field( $parameters['email'] );
	$username 	= sanitize_text_field( $parameters['username'] );
	$password 	= sanitize_text_field( $parameters['password'] );

	$error = new WP_Error();
	if ( empty( $username ) ) {
		$error->add( 400, __("Username is required.", 'wp-rest-user'), array( 'status' => 400 ) );
		return $error;
	}
	if ( empty( $email ) ) {
		$error->add( 401, __("Email is required.", 'wp-rest-user'), array( 'status' => 400 ) );
		return $error;
	}
	if ( empty( $password ) ) {
		$error->add( 404, __("Password is required.", 'wp-rest-user'), array( 'status' => 400 ) );
		return $error;
	}

	$user_id = username_exists( $username );

	// SHOWS ERROR IF THE USER ALREADY EXISTS
	if ( !$user_id && email_exists( $email ) == false ) {
		$user_id = wp_create_user( $username, $password, $email );
		if ( !is_wp_error( $user_id ) ) {
			$user = get_user_by('id', $user_id);

			// SET USER ROLE
			$user->set_role('administrator');

			$response['code'] = 200;
			$response['message'] = __("User '" . $username . "' Registration was Successful", "wp-rest-user");

		} else {
			return $user_id;
		}
	} else {
		$error->add( 406, __("Email/Username already exists", 'wp-rest-user'), array( 'status' => 400 ) );
		return $error;
	}

	return new WP_REST_Response( $response, 123 );

}
