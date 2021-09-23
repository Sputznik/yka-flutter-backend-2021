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
