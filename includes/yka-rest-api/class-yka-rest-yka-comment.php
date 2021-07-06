<?php

class YKA_REST_YKA_COMMENT extends YKA_REST_POST_BASE{
  function __construct(){
    $this->setPostType( 'yka-comment' );
    parent::__construct();
  }

  function addRestData(){

    parent::addRestData();

    // QUOTED COMMENT
    $this->registerRestField(
      'quoted-comment',
      function( $post, $field_name, $request ){
        $quoted_comment = (int) get_post_meta( $post['id'], $field_name, true );
        $quoted_comment = !empty( $quoted_comment ) ? $quoted_comment : null;
				return $quoted_comment;
			},
    	function( $value, $post, $field_name, $request, $object_type ){
				update_post_meta( $post->ID, $field_name, $value );
			},
			array(
		    'description'   => 'Quoted comment id',
		    'type'          => array('integer'),
				'context'       => array( 'view', 'edit' )
			)
  	);
  }
}

YKA_REST_YKA_COMMENT::getInstance();
