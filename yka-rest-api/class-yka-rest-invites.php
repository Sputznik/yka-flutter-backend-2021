<?php

class YKA_REST_INVITES extends WP_REST_Controller {

  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'yka/v' . $version;
    $base = 'invites';

    register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_items' ),
        'permission_callback' => '__return_true',
        'args'                => array(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args'                => array(
          'invite_link' =>  array(
            'description'   => 'Invite Link',
            'type'          => 'String',
            'required'      => true
          ),
          'timestamp' =>  array(
            'description'   => 'Timestamp',
            'type'          => 'integer',
            'required'      => true
          )
        ),
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array( $this, 'update_item' ),
        'permission_callback' => array( $this, 'update_item_permissions_check' ),
        'args'                => array(
          'new_user_id' =>  array(
            'description'   => 'ID of the new user',
            'type'          => 'integer',
            'required'      => true
          ),
          'timestamp' =>  array(
            'description'   => 'Timestamp',
            'type'          => 'integer',
            'required'      => true
          )
        ),
      )
    ) );
  }

  /**
	 * Retrieves a collection of intites.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or an empty array on failure.
	 */
  public function get_items( $request ) {

    $invites_db = YKA_DB_INVITE::getInstance();

    $response_data = $invites_db->getResults( $request );

		$data = array();

    // GET ALL THE INVITES FROM INVITES TABLE
    foreach( $response_data['data'] as $row ){
			$item = $this->prepare_item_for_response( $row, $request );
			array_push( $data, $item );
		}

    // SET HEADERS AND RETURN RESPONSE
    $response = new WP_REST_Response( $data, 200 );
		$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
		$response->header( 'X-WP-Total', $response_data['total'] );

    return $response;
	}

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item( $request ) {

    $error = new WP_Error( 'cant-create', __( 'Cannot Invite', 'text-domain' ), array( 'status' => 500 ) );

    // THROW ERROR IF NEW USER ID EXISTS IN THE REQUEST
    if ( ! empty( $request['new_user_id'] ) ) {
			return $error;
		}

    $item = $this->prepare_item_for_database( $request );

		$invites_db = YKA_DB_INVITE::getInstance();

    if( ! $invites_db->inviteExists( $item['invitee_id'], $item['invite_link'] ) ){

      $insert_id = $invites_db->insert( $item );

      if( $insert_id ){
        return new WP_REST_Response( $item, 200 );
  		}

    }

		return $error;

  }

  /**
	 * Updates a single post.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
    $item = $this->prepare_item_for_database( $request );
    $invites_db = YKA_DB_INVITE::getInstance();
    $errors = $this->checkNewUser( $item['new_user_id'],  $item['timestamp'] );

    if( !empty( $errors ) ){
      return $errors;
    }

    $args = array(
      'new_user_id' => $item['new_user_id']
    );

    $update_invite = $invites_db->updateWhere( $args, array( 'timestamp' => $item['timestamp'] ) );

    if( $update_invite ){
      return new WP_REST_Response( $item, 200 );
		}
		return new WP_Error( 'cant-update', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
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
   * Checks if a given request has access to update a post.
   *
   * @since 4.7.0
   *
   * @param WP_REST_Request $request Full details about the request.
   * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
   */
  public function update_item_permissions_check( $request ) {
    return $this->create_item_permissions_check( $request );
  }

  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database( $request ) {
    $data = array(
      'invitee_id'	=> get_current_user_id(),
      'timestamp'   => $request['timestamp']
		);

    if( ! empty( $request['invite_link'] ) ){
      $data['invite_link'] = $request['invite_link'];
    }

    if( ! empty( $request['new_user_id'] ) ){
      $data['new_user_id'] = $request['new_user_id'];
    }

		return $data;
  }

  function prepare_item_for_response( $item, $request ){
    return array(
      'invitee_id'  => (int) $item->invitee_id,
      'invite_link' => $item->invite_link,
      'new_user_id' => $item->new_user_id ? (int) $item->new_user_id : null,
      'timestamp'   => (int) $item->timestamp,
      'created_on'  => mysql_to_rfc3339( $item->post_date )
    );
	}

  function checkNewUser( $new_user, $timestamp ){

    $invites_db = YKA_DB_INVITE::getInstance();

    // THROW ERROR IF ALREADY JOINED
    if( ! $invites_db->hasUserAlreadyJoined( $new_user, $timestamp ) ){

      $user_obj = get_userdata( $new_user );

      // THROW ERROR IF USER DOES NOT EXIST
      if ( ! $user_obj ) {
        return new WP_Error(
          'rest_invalid_author',
          __( 'Invalid author ID.' ),
          array( 'status' => 400 )
        );
      }
      return array();
    }
    else{
      return new WP_Error(
        'rest_author_exists',
        __( 'User already joined. Link expired' ),
        array( 'status' => 400 )
      );
    }

  }

}

add_action( 'rest_api_init', function(){
	$invites_controller = new YKA_REST_INVITES;
	$invites_controller->register_routes();
} );
