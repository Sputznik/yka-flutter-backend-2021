<?php

class YKA_REST_POST_BASE extends YKA_REST_BASE{

  private $post_type;

  /* GETTER AND SETTER FUNCTIONS */
  function getPostType(){ return $this->post_type; }
  function setPostType( $post_type ){ $this->post_type = $post_type; }
  /* GETTER AND SETTER FUNCTIONS */

  function addRestData(){

    // AUTHOR DATA
    $this->registerRestField(
      'author_data',
      function( $post, $field_name, $request ){
        $author_id   = get_post_field( 'post_author', $post['id'] );
        $user_avatar = get_the_author_meta( 'user_display_picture', $author_id );

        return array(
          'id'      => $author_id,
    			'name'    => get_the_author_meta( 'display_name', $author_id ),
    			'avatar'  => !empty( $user_avatar ) ? $user_avatar : YKA_DEFAULT_USER_AVATAR
    		);
      }
    );

    // SHOW ATTACHMENTS FIELD IF CPT IS NOT Learning-Capsules
    if( $this->getPostType() != 'learning-capsules' ){
      // ATTACHMENTS
      $this->registerRestField(
        'attachments',
        function( $post, $field_name, $request ){
          $post_id = $post['id'];
      		return $post_attachments = array(
      			'images'   => $this->get_yka_attachment( $post_id, 'image' ),
      			'audio'    => $this->get_yka_attachment( $post_id, 'audio' )
          );
        }
      );
    }

    // SHOW TAG FIELD IF CPT IS NOT YKA-COMMENT
    if( $this->getPostType() != 'yka-comment' ){

      // TAGS
      $this->registerRestField(
        'tags',
        function( $post, $field_name, $request ){
  				return wp_get_object_terms( $post['id'], 'topics', array( 'fields' => 'names' ) );
        },
  			function( $value, $post, $field_name, $request, $object_type ){
  				wp_set_object_terms( $post->ID, explode(',', $value), 'topics' );
  			},
  				array(
  		    'description'   => 'Tag Names',
  		    'type'          => 'string',
  				'context'       =>   array( 'view', 'edit' )
  			)
    	);

      // BOOKMARK FIELD
      $this->registerRestField(
        'bookmark',
        function( $post, $field_name, $request ){
          $bookmarks_db = YKA_BOOKMARKS_DB::getInstance();
          $bookmark_flag = $bookmarks_db->isBookmarked( $post['id'] );
          if( $bookmark_flag ) return true;
          return false;
        },
        array(
         'description'   => 'Bookmark',
         'type'          => 'boolean',
         'context'       =>  array( 'view', 'edit' )
        )
      );

      // BOOKMARKS COUNT
      $this->registerRestField(
        'number_of_bookmarks',
        function( $post, $field_name, $request ){
          $bookmarks_db = YKA_BOOKMARKS_DB::getInstance();
          $total_count = $bookmarks_db->getBookmarksCount( $post['id'] );
  				return $total_count;
  			}
    	);


    }

  }

  function get_yka_attachment( $postId, $attachment_type ){
    $attachments = get_attached_media( $attachment_type, $postId );
    $attachments_arr = array();
    foreach ( $attachments as $attachment ) {
      $yka_post_attachment = array(
        'id' => $attachment->ID,
        'url' => wp_get_attachment_url( $attachment->ID ),
        'mime_type' => $attachment->post_mime_type,
      );

      if( $this->getPostType() == 'learning-capsules' ){
        $yka_post_attachment['description'] = $attachment->post_content;
      }

      array_push( $attachments_arr, $yka_post_attachment );
    }

    return $attachments_arr;
  }

}
