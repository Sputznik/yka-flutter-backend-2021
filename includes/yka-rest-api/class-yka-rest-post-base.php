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
        $user_avatar = get_user_meta( $post['author'], 'user_display_picture', true );
        return array(
    			'name'    => get_the_author_meta( 'display_name', $post['author'] ),
    			'avatar'  => !empty( $user_avatar ) ? $user_avatar : YKA_DEFAULT_USER_AVATAR
    		);
      }
    );

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

  function get_yka_attachment( $postId, $attachment_type ){
    $attachments = get_attached_media( $attachment_type, $postId );
    $attachments_arr = array();
    foreach ( $attachments as $attachment ) {
      $yka_post_attachment = array(
        'id' => $attachment->ID,
        'url' => wp_get_attachment_url( $attachment->ID ),
        'mime_type' => $attachment->post_mime_type
      );
      array_push( $attachments_arr, $yka_post_attachment );
    }

    return $attachments_arr;
  }

}