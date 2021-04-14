<?php
class YKAF_Conversations_Controller extends WP_REST_Controller {

  /**
* Constructor.
 */
  public function __construct() {
    $this->namespace = 'ykaf/v1';
    $this->rest_base = 'conversations';
    $this->post_type = 'conversation';
  }


  /**
   * Register the component routes.
   */
  public function register_routes() {
    register_rest_route( $this->namespace, '/' . $this->rest_base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_items' ),
        'permission_callback' => array( $this, 'get_items_permissions_check' ),
        'args'                => $this->get_collection_params(),
      )
    ) );
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
  * Check if a given request has access to post items.
  */
  public function get_items_permissions_check( $request ) {
    return true;
  }

  /**
  * Get the query params for collections
  */
  public function get_collection_params() {
    return array(
      'page'     => array(
        'description'       => 'Current page of the collection.',
        'type'              => 'integer',
        'default'           => 1,
        'sanitize_callback' => 'absint',
      ),
      'per_page' => array(
        'description'       => 'Maximum number of items to be returned in result set.',
        'type'              => 'integer',
        'default'           => 10,
        'sanitize_callback' => 'absint',
      ),
    );
  }

  /**
   * Prepares post data for return as an object.
   */
  public function prepare_item_for_response( $post, $request ) {

    $data = array(
      // 'id'                => $post->ID,
      'title'             => $post->post_title,
      'content'           => $post->post_content,
      'featured_img'      => get_the_post_thumbnail_url($post->ID, 'full'),
      'audio_attachment'  => '',
      // 'date'         => date( "F j, Y", strtotime($post->post_date))
    );

    return $data;
  }


}
