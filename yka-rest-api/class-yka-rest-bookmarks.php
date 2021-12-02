<?php

class YKA_REST_BOOKMARKS extends WP_REST_Controller {

  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'yka/v' . $version;
    $base = 'users';

    register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/bookmarks', array(
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
          'post_id' =>  array(
            'description'   => 'Post ID',
            'type'          => 'integer',
            'required'      => true
          )
        ),
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array( $this, 'delete_item' ),
        'permission_callback' => array( $this, 'delete_item_permissions_check' ),
        'args'                => array(
          'post_id' =>  array(
            'description'   => 'Post ID',
            'type'          => 'integer',
            'required'      => true
          )
        ),
      )
    ) );
  }

  /**
	 * Retrieves a collection of posts.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or an empty array on failure.
	 */
  public function get_items( $request ) {

		if( ! empty( $request['post_type'] ) && ! post_type_exists( $request['post_type'] ) ){
      return new WP_Error(
        'rest_no_route',
        __( 'No route was found matching the URL and request method.' ),
        array( 'status' => 404 )
      );
		}

    $bookmarks_db = YKA_BOOKMARKS_DB::getInstance();

    $user_id = $request['id'];

    if( ! $this->user_id_exists( $user_id ) ){
      return new WP_Error( 'rest_user_invalid_id', __('Invalid user ID.'), array( 'status' => 404 ) );
    }

		$bookmarked_ids = $bookmarks_db->getBookmarkIDs( $user_id );

    if( count( $bookmarked_ids ) < 1 ){
      return new WP_Error( 'no_bookmarks', __('No bookmark found.'), array( 'status' => 404 ) );
    }

    $default_types = array('conversation','learning-capsules');

    $args = array(
      'post_status'    => 'publish',
      'post_type'      => $request['type'] ? $request['type'] : $default_types,
      'posts_per_page' => $request['per_page'],
      'paged'          => $request['page'],
      'post__in'       => $bookmarked_ids
    );

    // USE WP_Query TO GET ALL THE BOOKMARKS WITH PAGINATION
    $query = new WP_Query( $args );


    // RETURN AN EMPTY ARRAY IF NO POSTS FOUND
    if( empty( $query->posts ) ){
      return [];
    }

    // SET MAX NUMBER OF PAGES AND TOTAL NUMBER OF BOOKMARKS
    $posts = $query->posts;
    $max_pages = $query->max_num_pages;
    $total = $query->found_posts;

    $data = array();

    foreach ( $posts as $post ) {
      $posts_controller = new WP_REST_Posts_Controller($post->post_type);

      /* PREPARES A SINGLE POST OUTPUT FOR RESPONSE */
      $response  = $posts_controller->prepare_item_for_response($post, $request);
      $data[]   = $posts_controller->prepare_response_for_collection($response);
    }

    // SET HEADERS AND RETURN RESPONSE
    $response = new WP_REST_Response($data, 200);
    $response->header( 'X-WP-Total', $total );
    $response->header( 'X-WP-TotalPages', $max_pages );

    return $response;

	}

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item( $request ) {
    $item = $this->prepare_item_for_database( $request );
		$bookmarks_db = YKA_BOOKMARKS_DB::getInstance();
		$insert_id = $bookmarks_db->insert( $item );

    if( $insert_id ){
      return new WP_REST_Response( $item, 200 );
		}
		return new WP_Error( 'cant-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function delete_item( $request ) {

		$bookmarks_db = YKA_BOOKMARKS_DB::getInstance();

    $bookmark = array(
      'user_id' => get_current_user_id(),
      'post_id' => $request['post_id']
    );

		if( $bookmarks_db->delete( $bookmark ) ){
			return new WP_REST_Response( true, 200 );
		}

    return new WP_Error( 'cant-delete', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
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
   * Check if a given request has access to delete a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function delete_item_permissions_check( $request ) {
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
			'post_id'	=> isset( $request['post_id'] ) ? $request['post_id'] : 0,
			'user_id'	=> get_current_user_id()
		);
		return $data;
  }

  // CHECKS IF USER ID EXISTS
  function user_id_exists( $user_id ) {
    global $wpdb;
    $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users WHERE ID = %d", $user_id ) );
    return empty( $count ) || 1 > $count ? false : true;
  }

}

add_action( 'rest_api_init', function(){
	$bookmarks_controller = new YKA_REST_BOOKMARKS;
	$bookmarks_controller->register_routes();
} );
