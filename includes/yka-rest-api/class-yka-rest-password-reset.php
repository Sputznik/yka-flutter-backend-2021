<?php

class YKA_REST_PASSWORD_RESET extends YKA_REST_BASE{

  function __construct(){
    add_action( 'rest_api_init', array( $this, 'reset_user_password' ) );
  }

  /* USER PASSWORD RESET ENDPOINT */
  function reset_user_password(){
    register_rest_route('yka/v1', 'password_reset', array(
      'methods' => 'POST',
      'callback' => array( $this, 'reset_user_password_callback' ),
      'permission_callback' => '__return_true'
    )	);
  }

  function reset_user_password_callback( $request = null ){
    $response 	      = array();
    $parameters       = $request->get_params();
  	$username	        = sanitize_text_field( $parameters['username'] );
    $new_password 	  = sanitize_text_field( $parameters['new_password'] );
    $current_user_obj = get_user_by('login', $username);


    $error = new WP_Error();
  	if ( empty( $username ) ) {
  		$error->add( 400, __("Username is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}
  	if ( empty( $new_password ) ) {
  		$error->add( 400, __("Password is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

    $user_id = username_exists( $username );

    // THROW ERROR IF USER DOES NOT EXISTS ELSE RESET THE PASSWORD
    if ( $user_id && $user_id > 0 ) {

      // RESET USER PASSWORD
      reset_password( $current_user_obj, $new_password );

      $response['code']         = 200;
      $response['message']      = __("Hey '" . $username . "' you have successfully reset your password", "wp-rest-user");

    } else {
  		$error->add( 406, __("Username does not exists", 'wp-rest-user'), array( 'status' => 400 ) );
  		return $error;
  	}

    return new WP_REST_Response( $response, 123 );

  }

}

YKA_REST_PASSWORD_RESET::getInstance();
