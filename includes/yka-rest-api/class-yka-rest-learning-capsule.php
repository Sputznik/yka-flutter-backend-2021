<?php

class YKA_REST_LEARNING_CAPSULE extends YKA_REST_POST_BASE{

  function __construct(){

    $this->setPostType( 'learning-capsules' );

    parent::__construct();
  }

  function addRestData(){

    parent::addRestData();

    // COVER IMAGE
    $this->registerRestField(
		  'cover_image',
		  function( $post, $field_name, $request ){
		    $id = $post['id'];
		    if( has_post_thumbnail( $id ) ){
		      $img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
		      $url = $img_arr[0];
		      return $url;
		    } else {
		      return null;
		    }
		  },
			array(
  	   'description'   => 'Cover Image'
			)
		);

    // AUDIO FIELD
    $this->registerRestField(
      'audio',
      function( $post, $field_name, $request ){
        $post_id = $post['id'];
        return $this->get_yka_attachment( $post_id, 'audio' )[0];
      }
    );

    // SLIDES FIELD
    $this->registerRestField(
      'slides',
      function( $post, $field_name, $request ){
        $post_id = $post['id'];
    		return $this->get_yka_attachment( $post_id, 'image' );
      }
    );

  }

}

YKA_REST_LEARNING_CAPSULE::getInstance();
