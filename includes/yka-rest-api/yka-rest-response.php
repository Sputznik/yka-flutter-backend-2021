<?php
// REMOVES EXTRA DATA FROM THE REST RESPONSE
function yka_remove_extra_data($data, $post, $context) {

  if( $context !== 'view' || is_wp_error( $data ) ) {

    $fields_arr = array(
      'date',
      'date_gmt',
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
