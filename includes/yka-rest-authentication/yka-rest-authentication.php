<?php
/*
Plugin Name: Flutter Authentication
Description: A simple plugin for flutter authentication
Version: 1.0.0
Author: Samuel Thomas
Text Domain: flutter-auth
*/

defined( 'ABSPATH' ) or die( 'Hey you cannot access this plugin, you silly human' );

class YKA_REST_AUTHENTICATION extends YKA_BASE{

  function __construct(){
    add_action( 'wp_ajax_auth_with_flutter', array( $this, 'authentication' ) );
    add_action( 'wp_ajax_nopriv_auth_with_flutter', array( $this, 'authentication' ) );
    add_action( 'rest_api_init', array( $this, 'signup' ) );

    // ENABLES APPLICATION_PASSWORD SECTION
    add_filter( 'wp_is_application_passwords_available', '__return_true' );
  }

  function authentication(){

    $data = array();

    $username = base64_decode($_REQUEST['ukey']);
    $password = base64_decode($_REQUEST['pkey']);

    if( !empty( $username ) && !empty( $password ) ){

      $user = wp_signon( array(
        'user_login'    => $username,
        'user_password' => $password
      ) );


      if( is_wp_error( $user ) ){
        $data = $user;
      }

      else{
        // SET APPLICATION_PASSWORD
        $data['new_password'] = $this->generateAppPassword( $user->ID );

        if( isset( $user->data ) ){
          $data['user'] = $user->data;
        }

      }

    }

    print_r( wp_json_encode( $data ) );

    wp_die();

  }

  /* USER REGISTRATION ENDPOINT */
  function signup(){
    register_rest_route('yka/v1', 'register', array(
      'methods' => 'POST',
      'callback' => array( $this, 'user_registration_callback' ),
      'permission_callback' => '__return_true'
    )	);
  }

  function user_registration_callback( $request = null ) {

    $response 	  = array();
    $parameters   = $request->get_params();
  	$username 	  = sanitize_text_field( $parameters['username'] );
    $password 	  = sanitize_text_field( $parameters['password'] );
    $display_name = sanitize_text_field( $parameters['display_name'] );
    $user_phone   = sanitize_text_field( $parameters['user_phone'] );
    $user_topics  = explode(',', $parameters['user_topics'] );

  	$error = new WP_Error();
  	if ( empty( $username ) ) {
  		$error->add( 400, __("Username is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}
  	if ( empty( $password ) ) {
  		$error->add( 404, __("Password is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

    if ( empty( $display_name ) ) {
  		$error->add( 404, __("Display Name is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

    if ( empty( $user_topics ) ) {
  		$error->add( 404, __("User topics cannot be empty.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

    // THROW ERROR IF USER HAS SELECTED LESS THAN 3 TOPICS
    if ( !empty( $user_topics ) ) {
  		if( count( $user_topics ) < 3 ){
        $error->add( 404, __( "You must choose at least 3 topics.", 'wp-rest-user'), array( 'status' => 400 ) );
    		return $error;
      }
  	}


    if ( empty( $user_phone ) ) {
  		$error->add( 404, __("Phone number is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

    // THROW ERROR IF PHONE NUMBER IS NOT OF TYPE INTEGER
    if ( !empty( $user_phone ) ) {
  		if( filter_var( $user_phone, FILTER_VALIDATE_INT ) === false ){
        $error->add( 404, __( "Invalid phone number.", 'wp-rest-user'), array( 'status' => 400 ) );
    		return $error;
      }
  	}

  	$user_id = username_exists( $username );

  	// THROW ERROR IF USER ALREADY EXISTS ELSE ADD NEW USER
    if ( !$user_id ) {

      $user_id = wp_create_user( $username, $password );
  		if ( !is_wp_error( $user_id ) ) {
  			$user = get_user_by('id', $user_id);

  			// SET USER ROLE
  			$user->set_role('administrator');

        // SET DISPLAY NAME AS FIRST NAME
        $user_fields = array(
         'ID'           => $user_id,
         'first_name'   => esc_attr( $display_name ),
         'display_name' => esc_attr( $display_name )
        );

        wp_update_user( $user_fields );

        // SET USER META
        $new_user_meta = array(
          'user_phone'  => $user_phone,
          'user_display_picture'  => YKA_URI.'includes/assets/images/default-profile.png'
        );

        foreach( $new_user_meta as $slug => $value ){
          update_user_meta( $user_id, $slug, $value );
        }

        // ADD USER TOPICS
        $user_topics_db = YKA_DB_USER_TOPICS::getInstance();

        foreach( $user_topics as $term ){
					if( ! in_array( $term, $user_topics_db->getUserTopics( $user_id ) ) ){
						$user_topics_db->insert(array(
							'user_id' 		=> $user_id,
							'category_id' => $term
						));
					}
				}

  			$response['code']         = 200;
  			$response['message']      = __("User '" . $username . "' Registration was Successful", "wp-rest-user");
        $response['new_password'] = $this->generateAppPassword( $user_id ); // SET APPLICATION_PASSWORD

  		} else {
  			return $user_id;
  		}
  	} else {
  		$error->add( 406, __("Username already exists", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

  	return new WP_REST_Response( $response, 123 );

  }

  // GENERATES NEW APPLICATION PASSWORD
  function generateAppPassword( $user_id ){
    if( class_exists('WP_Application_Passwords') ){
      $app = new WP_Application_Passwords;

      $local_time  = current_datetime();
      $current_time = $local_time->getTimestamp() + $local_time->getOffset();

      $unique_app_name = 'yka_app_'.$current_time;

      list( $new_password, $new_item ) = $app->create_new_application_password( $user_id, array( 'name'=> $unique_app_name ) );

      return $new_password;
    }
  }

}

YKA_REST_AUTHENTICATION::getInstance();
