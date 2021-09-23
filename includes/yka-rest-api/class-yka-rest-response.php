<?php

class YKA_REST_RESPONSE extends YKA_BASE{

  var $cpts;

  function __construct(){

    $this->cpts = ['conversation', 'yka-comment','learning-capsules'];

    $this->init();

    add_filter( 'the_content', array( $this, 'yka_remove_autop' ), 1 );
    add_filter( 'rest_user_query', array( $this, 'yka_show_all_users' ), 10, 2 );
  }

  function init(){
    foreach( $this->cpts as $cpt ){
      add_filter( 'rest_prepare_'.$cpt, array( $this, 'yka_remove_extra_data' ), 12, 3 );
      add_filter( 'rest_prepare_'.$cpt, array( $this, 'yka_entity_decode' ), 10, 1 );
    }
  }

  // REMOVES EXTRA DATA FROM THE REST RESPONSE
  function yka_remove_extra_data( $data, $post, $context ) {

    if( $context !== 'view' || is_wp_error( $data ) ) {

      $fields_arr = array(
        'modified',
        'modified_gmt',
        // 'featured_media',
        'template',
        'author',
        'slug',
        'link',
        'type',
        'status',
        'password',
        'generated_slug',
        'permalink_template'
      );

      foreach ( $fields_arr as $field ) {
        unset( $data->data[$field]);
      }

      foreach( $data->get_links() as $_linkKey => $_linkVal ) {
        $data->remove_link($_linkKey);
      }

      return $data;

     }
  }

  // DECODE HTML ENTITIES
  function yka_entity_decode( $response ){

    $data = $response->get_data();
    $fields = array('title','content');

    foreach( $fields as $field ){
  		if( isset( $data[$field]['rendered'] ) && $data[$field]['rendered'] ) {
        $data[$field]['rendered'] = html_entity_decode( $data[$field]['rendered'] ); // DECODES HTML ENTITIES
  		}
  	}

    $response->set_data($data);

    return $response;
  }

  // REMOVES AUTO GENERATED P TAGS
  function yka_remove_autop( $content ){
  	if( in_array( get_post_type(), $this->cpts ) ) { remove_filter( 'the_content', 'wpautop' ); }
    return $content;
  }

  /**
   * Removes `has_published_posts` from the query args so even users who have not
   * published content are returned by the request.
   * @see https://developer.wordpress.org/reference/classes/wp_user_query/
   */
  function yka_show_all_users( $prepared_args, $request ){
    unset( $prepared_args['has_published_posts'] );
    return $prepared_args;
  }

}

YKA_REST_RESPONSE::getInstance();
