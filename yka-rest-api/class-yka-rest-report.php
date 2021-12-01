<?php

class YKA_REST_REPORT extends WP_REST_Controller {

  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'yka/v' . $version;
    $base = 'report';

    register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args'                => array(
          'post_id' =>  array(
            'description'   => 'Post ID',
            'type'          => 'integer',
            'required'      => true,
          ),
          'reason' =>  array(
            'description'   => 'Reason',
            'type'          => 'String',
            'required'      => true
          )
        ),
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

    $post = $this->get_post( $request['post_id'] );

    if ( is_wp_error( $post ) ) {
			return $post;
		}

    $data = $this->prepare_item_for_response( $post, $request );

    // REPORT POST
    do_action( 'yka_report_post', $data );

    return new WP_REST_Response( array( 'message' => "Post has been reported successfully.", status => 200 ) );

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
	 * Get the post, if the ID is valid.
	 *
	 * @since 4.7.2
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_post( $id ) {
		$error = new WP_Error(
			'rest_post_invalid_id',
			__( 'Invalid post ID.' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$post = get_post( (int) $id );

		if ( empty( $post ) || empty( $post->ID ) ) {
			return $error;
		}

		return $post;
	}

  function prepare_item_for_response( $item, $request ){
    return array(
      'post_id'       => (int) $item->ID,
      'post_title'    => $item->post_title,
      'post_author'   => ucwords( get_the_author_meta( 'display_name', $item->post_author ) ),
      'reason'        => $request['reason'],
      'current_user'  => ucwords( get_the_author_meta( 'display_name', get_current_user_id() ) )
    );
	}

}

add_action( 'rest_api_init', function(){
	$reports_controller = new YKA_REST_REPORT;
	$reports_controller->register_routes();
} );
