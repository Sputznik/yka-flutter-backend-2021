<?php

class YKA_REST_CONVERSATION extends YKA_REST_POST_BASE{
  function __construct(){
    $this->setPostType( 'conversation' );
    add_filter( 'rest_conversation_query', array( $this, 'filterRestData' ), 10, 2 );
    parent::__construct();
  }

  function addRestData(){

    parent::addRestData();

    // BOOKMARKS
    $this->registerRestField(
      'number_of_bookmarks',
      function( $post, $field_name, $request ){
				return '0';
			}
  	);

    // COMMENTS-DATA
		$this->registerRestField(
      'comments_data',
      function( $post, $field_name, $request ){

        $id = $post['id'];

        global $wpdb;

    		$authors = $wpdb->get_results("
    	    SELECT
    	        {$wpdb->prefix}users.ID
    	    FROM
    	        wp_users
    	    INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}users.ID = {$wpdb->prefix}posts.post_author
    	    WHERE
    	        {$wpdb->prefix}posts.post_type = 'yka-comment'
              AND {$wpdb->prefix}posts.post_status = 'publish'
              AND {$wpdb->prefix}posts.post_parent = $id
    	    GROUP BY
    	        {$wpdb->prefix}users.ID;
    	  ");

        return array(
    			'number_of_comments'    => count( get_pages( array( 'child_of' => $post['id'], 'post_type' => 'yka-comment') ) ),
    			'number_of_users'  			=> count( $authors )
    		);
      }
	 	);

    // LEARNING CAPSULE ID
    $this->registerRestField(
      'capsule_id',
      function( $post, $field_name, $request ){
        $ccr_db = YKA_DB_CAPSULE_CONVERSATION_RELATION::getInstance();
        $capsule_id = $ccr_db->getCapsuleIDForConversation( $post['id'] );
        if( $capsule_id ) return (int) $capsule_id;
        return null;
      },
      function( $value, $post, $field_name, $request, $object_type ){
        $ccr_db = YKA_DB_CAPSULE_CONVERSATION_RELATION::getInstance();

        if( ( $value > 0 ) && ( $post->ID > 0 ) ){

          $item = array(
            'capsule_id' 		 => $value,
            'conversation_id' => $post->ID
          );

          if( ! $ccr_db->relationExists( $post->ID, $value ) ){

            // INSERT INTO RELATION DB
            $ccr_db->insert( $item );

          }
          else{
            return new WP_Error(
              'cant-create',
              __( 'Failed to add capsule ID.' ),
              array( 'status' => 500 )
            );
          }

        }

			},
      array(
       'description'   => 'Capsule ID',
       'type'          => 'integer',
       'context'       =>  array( 'view', 'edit' )
      )
    );

  }

  // OPTION TO SHOW CONVERSATIONS BASED ON THE FOLLOWING LIST OF CURRENT USER
  function filterRestData( $args, $request ){
		$user_following = $request->get_param( 'following' );
    $follow_users = array(
      'type'    => "following",
      'user_id' => (int) ( $user_following ) // VALUE MUST BE ALWAYS >= 1
    );

    if( $follow_users['user_id'] ){
      $follow_users_db = YKA_DB_FOLLOW_USERS::getInstance();
      $user_ids = $follow_users_db->getUserIDs( $follow_users['user_id'], $follow_users['type']  );
      $args['author__in'] = $user_ids ? $user_ids : array(0); // SHOW EMPTY CONVERSATIONS LIST IF USER ID DOES NOT EXIST
    }

    return $args;
  }

}

YKA_REST_CONVERSATION::getInstance();
