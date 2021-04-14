<?php

class YKAF_ADMIN extends YKAF_BASE{

  function __construct(){

    add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

    add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 1 );

    // LOAD ASSETS FOR WP ADMIN BACKEND
    add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );


  } // CONSTRUCT ENDS


  function add_meta_box(){
    $fields = apply_filters( 'ykaf-admin-meta-box-fields', array() );
    add_meta_box( 'ykaf-settings', 'YKAF SETTINGS', array( $this, 'render_meta_box' ), array( 'ykaf-comment' ), 'normal', 'low', $fields );
  }

  /* RENDER METABOX */
  function render_meta_box($post, $meta_box){
    wp_nonce_field( 'ykaf_meta_box', 'ykaf_meta_box_nonce' );
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
    if ( ! isset( $_POST['ykaf_meta_box_nonce'] ) ) return;

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['ykaf_meta_box_nonce'], 'ykaf_meta_box' ) ) return;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    do_action( 'ykaf-save-meta-box', $post_id );

  }



  function load_scripts($hook) {
    if( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' )
      return;

    $uri = YKAF_URI.'includes/ykaf-admin/assets/';

    wp_register_style( 'ykaf-admin', $uri.'style.css', array(), YKAF_VERSION );
    wp_enqueue_style('ykaf-admin');

    wp_enqueue_script(
      'ykaf-autocomplete',
      $uri.'autocomplete.js',
      array( 'jquery', 'jquery-ui-autocomplete' ),
      YKAF_VERSION,
      true
    );
  }



}

	YKAF_ADMIN::getInstance();
