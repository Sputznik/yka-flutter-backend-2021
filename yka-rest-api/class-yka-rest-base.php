<?php

class YKA_REST_BASE extends YKA_BASE{

  function __construct(){
    add_action( 'rest_api_init', array( $this, 'addRestData' ) );
  }

  // WRAPPER FUNCTION TO REGISTER CUSTOM ROUTE
  function registerRoute( $route, $callback, $permission_callback = '__return_true' ){
		register_rest_route( 'yka/v1', '/' . $route, array(
    	'methods' => 'GET',
    	'callback' => $callback,
			'permission_callback'	=> $permission_callback
  	) );
	}

  // WRAPPER FUNCTION TO REGISTER REST FIELD
	function registerRestField( $field_name, $get_callback, $update_callback = '__return_false', $schema = null ){
		register_rest_field(
			$this->getPostType(),
			$field_name,
			array(
    		'get_callback'    => $get_callback,
    		'update_callback' => $update_callback,
    		'schema'          => $schema,
     	)
		);
	}

}
