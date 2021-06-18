<?php
// REMOVES EXTRA DATA FROM THE REST RESPONSE
function yka_remove_extra_data($data, $post, $context) {

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
      unset ( $data->data [$field]);
    }

    foreach( $data->get_links() as $_linkKey => $_linkVal ) {
      $data->remove_link($_linkKey);
    }

    return $data;

   }
}
add_filter('rest_prepare_conversation', 'yka_remove_extra_data', 12, 3);
add_filter('rest_prepare_yka-comment', 'yka_remove_extra_data', 12, 3);


// REMOVES AUTO GENERATED P TAGS
add_filter( 'the_content', function( $content ){
	$cpts = ['conversation', 'yka-comment'];
	if( in_array( get_post_type(), $cpts ) ) { remove_filter( 'the_content', 'wpautop' ); }
	return $content;
}, 0 );

add_filter( 'rest_request_after_callbacks', function( $response, array $handler, \WP_REST_Request $request ) {
	$yka_routes = array( '/wp/v2/conversation', '/wp/v2/yka-comment');
	if( in_array( $request->get_route(), $yka_routes ) ){
    if( ! ( $response instanceof \WP_REST_Response ) ){ return; }
    $data = array_map( 'modify_yka_post_response', $response->get_data() );
    $response->set_data( $data );
  }
  return $response;
}, 10, 3 );

function modify_yka_post_response( array $post ) {
	$fields = array('title','content');
	foreach( $fields as $field ){
		if( isset( $post[$field]['rendered'] ) && $post[$field]['rendered'] ) {
      $post[$field]['rendered'] = html_entity_decode( $post[$field]['rendered'] ); // DECODES HTML ENTITIES
		}
	}
  return $post;
}
