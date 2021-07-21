<?php

class YKA_REST_FOLLOW_USERS extends WP_REST_Controller {

	/**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'yka/v' . $version;
    $base = 'follow_users';

		register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args' => array(
          'following_id' => array(
            'description'       => 'User id which the current user has requested to follow.',
            'type'              => 'integer',
            'required'          => true,
            'sanitize_callback' => 'absint',
            'validate_callback' => function( $param, $request, $key ) {
              return ( $param && get_current_user_id() != $param && is_numeric( $param ) && get_userdata( $param ) );
            }
          ),
        )
      )
    ) );

  }

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item( $request ) {
    $item = $this->prepare_item_for_database( $request );
    $follow_users_db = YKA_DB_FOLLOW_USERS::getInstance();

    if( $follow_users_db->is_following( $item['following_id'] ) ){
      $result['db'] = $follow_users_db->delete( $item );
      $result['text'] = "Follow";
    }
    else{
      $result['db'] = $follow_users_db->insert( $item );
      $result['text'] = "Following";
    }

		if( $result['db'] ){
      return new WP_REST_Response( $result, 200 );
    }
		return new WP_Error( 'invalid-user-id', __( 'Unable to follow', 'text-domain' ), array( 'status' => 500 ) );
  }

  /**
   * Check if a given request has access to create items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function create_item_permissions_check( $request ) {
    return current_user_can( 'edit_posts' );
  }

  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database( $request ) {
    $data = array(
      'user_id'	      => get_current_user_id(),
			'following_id'	=> $request['following_id'],
		);
		return $data;
  }

}

add_action( 'rest_api_init', function(){
	$yka_follow_users_controller = new YKA_REST_FOLLOW_USERS;
	$yka_follow_users_controller->register_routes();
} );
