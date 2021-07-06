<?php

class YKA_REST_RESPONSE extends YKA_BASE{

  function __construct(){
    add_filter( 'the_content', array( $this, 'yka_remove_autop' ), 1 );
    add_filter("rest_prepare_conversation", array( $this, 'yka_remove_extra_data' ), 12, 3);
    add_filter('rest_prepare_yka-comment', array( $this, 'yka_remove_extra_data' ), 12, 3);
    add_filter( "rest_prepare_conversation", array( $this, 'yka_entity_decode' ), 10, 1 );
    add_filter( "rest_prepare_yka-comment", array( $this, 'yka_entity_decode' ), 10, 1 );
  }

  // REMOVES EXTRA DATA FROM THE REST RESPONSE
  function yka_remove_extra_data( $data, $post, $context ) {

    if( $context !== 'view' || is_wp_error( $data ) ) {

      $fields_arr = array(
        'modified',
        'modified_gmt',
        'featured_media',
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
  	$cpts = ['conversation', 'yka-comment'];
  	if( in_array( get_post_type(), $cpts ) ) { remove_filter( 'the_content', 'wpautop' ); }
    return $content;
  }

}

YKA_REST_RESPONSE::getInstance();
