<?php

class YKA_REST_CONVERSATION extends YKA_REST_POST_BASE{
  function __construct(){
    $this->setPostType( 'conversation' );
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

    // TAGS
    $this->registerRestField(
      'tags',
      function( $post, $field_name, $request ){
				return wp_get_object_terms( $post['id'], 'topics', array( 'fields' => 'names' ) );
			},
			function( $value, $post, $field_name, $request, $object_type ){
				wp_set_object_terms( $post->ID, $value, 'topics' );
			},
				array(
		    'description'   => 'Conversation tags',
		    'type'          => 'array',
				'context'       =>   array( 'view', 'edit' )
			)
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

}

YKA_REST_CONVERSATION::getInstance();
