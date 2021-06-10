<?php
/*
Plugin Name: Flutter Authentication
Description: A simple plugin for flutter authentication
Version: 1.0.0
Author: Samuel Thomas
Text Domain: flutter-auth
*/

defined( 'ABSPATH' ) or die( 'Hey you cannot accesse this plugin, you silly human' );

add_action( 'wp_ajax_auth_with_flutter', 'authentication' );
add_action( 'wp_ajax_nopriv_auth_with_flutter', 'authentication' );

function authentication(){

  $data = array();

  $username = base64_decode($_REQUEST['ukey']);
  $password = base64_decode($_REQUEST['pkey']);

  //echo $username;
  //$username = "sam";
  //$password = "sam";

  if( !empty( $username ) && !empty( $password ) ){

    $user = wp_signon( array(
      'user_login'    => $username,
      'user_password' => $password
    ) );


    if( is_wp_error( $user ) ){
      $data = $user;
    }

    else if( class_exists('WP_Application_Passwords') ){
      $app = new WP_Application_Passwords;

      $local_time  = current_datetime();
      $current_time = $local_time->getTimestamp() + $local_time->getOffset();

      $unique_app_name = 'yka_app_'.$current_time;

      list( $new_password, $new_item ) = $app->create_new_application_password( $user->ID, array( 'name'=> $unique_app_name ) );

      // APPLICATION_PASSWORD
      $data['new_password'] = $new_password;

      if( isset( $user->data ) ){
        $data['user'] = $user->data;
      }

    }

  }

  print_r( wp_json_encode( $data ) );

  wp_die();

}

// ENABLES APPLICATION_PASSWORD SECTION
add_filter( 'wp_is_application_passwords_available', '__return_true' );
