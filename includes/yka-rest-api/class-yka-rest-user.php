<?php

class YKA_REST_YKA_USER extends YKA_REST_POST_BASE{

  function __construct(){
    $this->setPostType( 'user' );
    add_filter( 'rest_user_query', array( $this, 'filterRestData' ), 10, 2 );
    parent::__construct();
  }

  function addRestData(){

    // USER TOPICS
    $this->registerRestField(
      'user_topics',
      function( $post, $field_name, $request ){
        $user_topics_db = YKA_DB_USER_TOPICS::getInstance();
				$user_topic_ids = $user_topics_db->getUserTopics( $post['id'] );
				$user_topics_arr = array();
				foreach ( $user_topic_ids as $topic_id) {
					$topic_obj = array(
						'id' 		=> $topic_id,
						'slug'	=> get_term( $topic_id, 'topics' )->slug,
						'name'	=> get_term( $topic_id, 'topics' )->name
					);
					array_push( $user_topics_arr, $topic_obj );
				}
				return $user_topics_arr;
			},
			function( $value, $post, $field_name, $request, $object_type ){
        $user_topics_db = YKA_DB_USER_TOPICS::getInstance();
				foreach( $value as $term ){
					// INSERT IF THERE ARE NO PREVIOUS ENTRIES
					if( ! in_array( $term, $user_topics_db->getUserTopics( $post->ID ) ) ){
						$user_topics_db->insert(array(
							'user_id' 		=> $post->ID,
							'category_id' => $term
						));
					}
				}
			},
			array(
       'description'   => 'User Topics',
 			 'type'          => 'array',
 			 'context'       =>  array( 'view', 'edit' )
      )
  	);

    // USER DISPLAY PICTURE
    $this->registerRestField(
      'user_display_picture',
      function( $post, $field_name, $request ){
        $user_profile = get_user_meta( $post['id'], $field_name, true );
				return !empty( $user_profile ) ? $user_profile : YKA_DEFAULT_USER_AVATAR;
			},
			function( $value, $post, $field_name, $request, $object_type ){
        update_user_meta( $post->ID, $field_name, $value );
			},
			array(
       'description'   => 'User display picture link',
 			 'type'          => 'String',
 			 'context'       =>  array( 'view', 'edit' )
      )
  	);

    // USER COUNTRY
    $this->registerRestField(
      'user_country',
      function( $post, $field_name, $request ){
			  return "India";
			}
  	);

    // USER CITY
    $this->registerRestField(
      'user_city',
      function( $post, $field_name, $request ){
        $user_city = get_user_meta( $post['id'], $field_name, true );
			  return !empty( $user_city ) ? $user_city : false;
			},
			function( $value, $post, $field_name, $request, $object_type ){
        update_user_meta( $post->ID, $field_name, $value );
			},
				array(
				'description'   => 'User City',
   			'type'          => 'String',
   			'context'       =>  array( 'view', 'edit' )
      )
  	);

    // USER PHONE NUMBER
    $this->registerRestField(
      'user_phone',
      function( $post, $field_name, $request ){
        $user_phone = (int) get_user_meta( $post['id'], $field_name, true );
				return !empty( $user_phone ) ? $user_phone : false;
			},
			function( $value, $post, $field_name, $request, $object_type ){
        update_user_meta( $post->ID, $field_name, $value );
			},
			array(
       'description'   => 'User Phone Number',
 			 'type'          => 'number',
 			 'context'       =>  array( 'view', 'edit' )
      )
    );

    // USER FOLLOW
    $this->registerRestField(
      'follow',
      function( $post, $field_name, $request ){
        $follow_users_db = YKA_DB_FOLLOW_USERS::getInstance();
        $follow_flag = $follow_users_db->is_following( $post['id'] );
        if( $follow_flag ) return true;
        return false;
      },
      function( $value, $post, $field_name, $request, $object_type ){
        $follow_users_db = YKA_DB_FOLLOW_USERS::getInstance();
        $follow_id = $post->ID;
        /**
         * BODY PARAMS
         * follow = true / false
         * METHOD : PUT
         */
        if( $follow_id){
          $current_user_id = get_current_user_id();
          $item = array(
            'user_id'       => $current_user_id,
            'following_id'  =>  $follow_id
          );

          // DELETE ENTRIES IF ALREADY FOLLOWED
          if( $follow_users_db->is_following( $follow_id ) ){
            $follow_users_db->delete( $item );
          }

          // ADD AN ENTRY IF follow = true
          if( $value ){
            $follow_users_db->insert( $item );
          }

        }
      },
      array(
       'description'   => 'Follow or Unfollow User',
       'type'          => 'boolean',
       'context'       =>  array( 'view', 'edit' )
      )
    );

  }

  // OPTION TO SHOW USERS BASED on following or followers parameter
  function filterRestData( $args, $request ){
    $user_followers = $request->get_param( 'followers' );
		$user_following = $request->get_param( 'following' );
    $follow_users = array(
      'type'    => $user_followers ? "followers" : "following",
      'user_id' => (int) ( $user_followers ? $user_followers : $user_following ) // VALUE MUST BE ALWAYS >= 1
    );

    if( $follow_users['user_id'] ){
      $follow_users_db = YKA_DB_FOLLOW_USERS::getInstance();
      $user_ids = $follow_users_db->getUserIDs( $follow_users['user_id'], $follow_users['type']  );
      $args['include'] = $user_ids ? $user_ids : array(0); // SHOW EMPTY LIST IF FOLLOWING OR FOLLOWER ID'S DOES NOT EXIST
    }

    return $args;
  }

}

YKA_REST_YKA_USER::getInstance();
