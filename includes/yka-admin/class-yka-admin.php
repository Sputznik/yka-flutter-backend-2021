<?php

class YKA_ADMIN extends YKA_BASE{

  function __construct(){

    add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

    add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 1 );

    // LOAD ASSETS FOR WP ADMIN BACKEND
    add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

    // MIME TYPE SUPPORT
    add_filter( 'wp_check_filetype_and_ext', array( $this, 'yka_allow_upload_extension' ), 10, 4 );

    // DELETE USER DATA FROM CUSTOM TABLES
    add_action( 'delete_user', array( $this, 'yka_delete_user_data' ) );

    // DELETE USER TOPIC(S) WHEN A TOPIC IS DELETED DIRECTLY
    add_action( 'delete_term_taxonomy', array( $this, 'yka_delete_user_topics' ) );

    // DELETE POSTS WITH USER FROM CPT THAT DO NOT HAVE AN AUTHOR FIELD
    add_filter("post_types_to_delete_with_user", array( $this, "yka_post_types_to_delete_with_user" ), 10, 2);

  } // CONSTRUCT ENDS


  function add_meta_box(){
    $fields = apply_filters( 'yka-admin-meta-box-fields', array() );
    add_meta_box( 'yka-settings', 'YKA SETTINGS', array( $this, 'render_meta_box' ), array( 'yka-comment' ), 'normal', 'low', $fields );
  }

  /* RENDER METABOX */
  function render_meta_box($post, $meta_box){
    wp_nonce_field( 'yka_meta_box', 'yka_meta_box_nonce' );
    if( isset( $meta_box['args' ] ) && is_array( $meta_box['args'] ) ){
      foreach ($meta_box['args'] as $slug => $callback ) {
        if( is_callable( $callback ) ){
          call_user_func( $callback, $post );
        }
      }
    }
  }


  function save_meta_box( $post_id ){
    /*
     * We need to verify this came from our screen and with proper authorization,
    * because the save_post action can be triggered at other times.
    */

    // Check if our nonce is set.
    if ( ! isset( $_POST['yka_meta_box_nonce'] ) ) return;

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['yka_meta_box_nonce'], 'yka_meta_box' ) ) return;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    do_action( 'yka-save-meta-box', $post_id );

  }



  function load_scripts($hook) {
    if( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' )
      return;

    $uri = YKA_URI.'includes/yka-admin/assets/';

    wp_register_style( 'yka-admin', $uri.'style.css', array(), YKA_VERSION );
    wp_enqueue_style('yka-admin');

    wp_enqueue_script(
      'yka-autocomplete',
      $uri.'autocomplete.js',
      array( 'jquery', 'jquery-ui-autocomplete' ),
      YKA_VERSION,
      true
    );
  }

  function yka_allow_upload_extension( $data, $file, $filename, $mimes ) {
    if ( ! empty( $data ) ) {
			$filetype = wp_check_filetype( $filename );
			$data['ext'] = $filetype['ext'];
			$data['type'] = $filetype['type'];
		}

    return $data;
  }

  function yka_delete_user_data( $user_id ) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $user_obj = get_userdata( $user_id );

    $custom_user_tables = array(
      $prefix.'yka_follow_users' => array(
        'where' => "user_id = $user_obj->ID OR following_id = $user_obj->ID"
      ),
      $prefix.'yka_user_topics' => array(
        'where' => "user_id = $user_obj->ID"
      )
    );

    foreach( $custom_user_tables as $table => $value ){
      $wpdb->query( "DELETE FROM $table WHERE ".$value['where'] );
    }

  }

  function yka_delete_user_topics( $tt_id ){
    global $wpdb;
    $user_topics_table = $wpdb->prefix.'yka_user_topics';
    $wpdb->query( "DELETE FROM $user_topics_table WHERE `category_id` =".$tt_id );
  }

  function yka_post_types_to_delete_with_user( array $post_types_to_delete, $user_id ){
    $post_types_to_delete[] = "yka-comment";
    return $post_types_to_delete;
  }

}

	YKA_ADMIN::getInstance();
