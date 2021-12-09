<?php

class YKA_REST_LEARNING_CAPSULE extends YKA_REST_POST_BASE{

  function __construct(){

    $this->setPostType( 'learning-capsules' );

    // REORDER SLIDES
    add_action( 'rest_api_init', array( $this, 'reorderSlides' ) );

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

    // THEME FIELD
    $this->registerRestField(
      'theme',
      function( $post, $field_name, $request ){
        $capsule_theme = (int) get_post_meta( $post['id'], $field_name, true );
        $capsule_theme = !empty( $capsule_theme ) ? $capsule_theme : wp_rand( 1, 4 );  // REMOVE FROM PRODUCTION
				return $capsule_theme;
      },
      function( $value, $post, $field_name, $request, $object_type ){
        update_post_meta( $post->ID, $field_name, $value );
      },
        array(
        'description'   => 'Theme',
        'type'          => 'integer',
        'context'       =>   array( 'view', 'edit' )
      )
    );

    // CONVERSATION ID
    $this->registerRestField(
      'conversation_id',
      function( $post, $field_name, $request ){
        $ccr_db = YKA_DB_CAPSULE_CONVERSATION_RELATION::getInstance();
        $conversation_id = $ccr_db->getConversationIDForCapsule( $post['id'] );
        if( $conversation_id ) return (int) $conversation_id;
        return null;
      },
      array(
       'description'   => 'Conversation ID',
       'type'          => 'integer'
      )
    );

  }

  function reorderSlides(){
    register_rest_route('yka/v1', 'reorder_slides', array(
      'methods' => 'POST',
      'callback' => array( $this, 'reorderSlidesCallback' ),
      'permission_callback' => function( $request ){
        return current_user_can( 'edit_posts' );
      },
      'args'                => array(
        'slide_ids' =>  array(
          'description'   => 'Slide Ids',
          'type'          => 'array',
          'items'         =>  array(
            'type' =>  'integer'
          )
        )
      )
    )	);
  }

  function reorderSlidesCallback( $request ){

    $slide_ids = $request['slide_ids'];

    if( !count( $slide_ids ) > 0 ){
      return new WP_Error(
        'rest_property_required',
        __( 'slide_ids is a required property.' ),
        array( 'param' => 'slide_ids is a required property.', 'status' => 400 )
      );
    }

    $index = 1;
    foreach( $slide_ids as $slide_id ){
      $attachment = array(
        'ID'          =>  $slide_id,
        'menu_order'  =>  $index
      );

      // UPDATE ATTACHMENT MENU ORDER
      wp_update_post( $attachment );

      $index++;
    }

    return new WP_REST_Response( array( 'message' => "The slides have been reordered successfully.", 'status' => 200 ) );

  }

}

YKA_REST_LEARNING_CAPSULE::getInstance();
