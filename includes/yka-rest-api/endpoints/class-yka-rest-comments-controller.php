<?php
class YKA_Comments_Controller extends WP_REST_Posts_Controller{

  /**
* Constructor.
 */
  public function __construct() {
    $this->namespace = 'yka/v1';
    $this->rest_base = 'comments';
    $this->post_type = 'yka-comment';
  }

  /**
   * Prepares post data for return as an object.
   */
  public function prepare_item_for_response( $post, $request ) {

    $data = array(
      'id'           => $post->ID,
      'title'        => $post->post_title,
      'content'      => $post->post_content,
      'conversation_id'  => (int) $post->post_parent,
      'author_data'  => array(
        'name'    => get_the_author_meta( 'display_name', (int) $post->post_author ),
        'avatar'  => ''
      )
    );

    return $data;
  }


}
