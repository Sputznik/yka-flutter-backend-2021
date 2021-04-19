<?php
class YKA_Conversations_Controller extends WP_REST_Posts_Controller{

  /**
* Constructor.
 */
  public function __construct() {
    $this->namespace = 'yka/v1';
    $this->rest_base = 'conversations';
    $this->post_type = 'conversation';
  }


  /**
   * Register the component routes.
   */
  public function register_routes() {

    $schema        = $this->get_item_schema();
		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);

    register_rest_route( $this->namespace, '/' . $this->rest_base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_items' ),
        'permission_callback' => array( $this, 'get_items_permissions_check' ),
        'args'                => $this->get_collection_params(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
      ),
      'schema' => array( $this, 'get_public_item_schema' )
    ) );


    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base . '/(?P<id>[\d]+)',
      array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_item' ),
            'permission_callback' => array( $this, 'get_items_permissions_check' ),
            'args'                => array(
              'context' => $this->get_context_param( array(
                  'default' => 'view',
              ) ),
            ),
        ),
        array(
          'methods'             => WP_REST_Server::EDITABLE,
          'callback'            => array( $this, 'update_item' ),
          'permission_callback' => array( $this, 'update_item_permissions_check' ),
          'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
        ),
        array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array( $this, 'delete_item' ),
        'permission_callback' => array( $this, 'delete_item_permissions_check' ),
        'args'                => array(
          'force' => array(
            'type'        => 'boolean',
            'default'     => false,
            'description' => __( 'Whether to bypass trash and force deletion.' ),
          ),
        ),
      ),
        'schema' => array( $this, 'get_public_item_schema' ),
      )
    );

  }



  /**
   * Retrieve posts.
   */
  public function get_items( $request ) {
    $args = array(
      'post_type'      => $this->post_type,
      'posts_per_page' => $request['per_page'],
      'paged'           => $request['page'],
      //'name' => $request['slug'],
    );

    // use WP_Query to get the results with pagination
    $query = new WP_Query( $args );

    // if no posts found return
    if( empty($query->posts) ){
      return new WP_Error( 'no_posts', __('No post found'), array( 'status' => 404 ) );
    }

    // set max number of pages and total num of posts
    $posts = $query->posts;

    $max_pages = $query->max_num_pages;
    $total = $query->found_posts;

    foreach ( $posts as $post ) {
      $response = $this->prepare_item_for_response( $post, $request );
      $data[] = $this->prepare_response_for_collection( $response );
    }


    // set headers and return response
    $response = new WP_REST_Response($data, 200);

    $response->header( 'X-WP-Total', $total );
    $response->header( 'X-WP-TotalPages', $max_pages );

    return $response;
  }


  /**
   * Prepares post data for return as an object.
   */
  public function prepare_item_for_response( $post, $request ) {

    $data = array(
      // 'id'                => $post->ID,
      'title'        => $post->post_title,
      'content'      => $post->post_content,
      'attachments'  => get_attached_media('audio',$post->ID)
    );

    return $data;
  }


}
