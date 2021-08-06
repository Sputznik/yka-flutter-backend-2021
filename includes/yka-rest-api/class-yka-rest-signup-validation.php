<?php

class YKA_REST_SIGNUP_VALIDATION extends YKA_REST_BASE{

  function signupValidationCallback( WP_REST_Request $args ){
    if( $args['username'] ){
      if( username_exists( $args['username'] ) ){
        return new WP_REST_Response( array('exists' => true ) );
      }
      return new WP_REST_Response( array('exists' => false ) );
    }
    else {
      return new WP_Error( 400, __("Username is required.", 'wp-rest-user'), array( 'status' => 400 ) );
    }

  }

  function addRestData(){
		$this->registerRoute( 'signup_validation', array( $this, 'signupValidationCallback' ) );
	}

}

YKA_REST_SIGNUP_VALIDATION::getInstance();
