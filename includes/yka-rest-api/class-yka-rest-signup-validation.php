<?php

class YKA_REST_SIGNUP_VALIDATION extends YKA_REST_BASE{

  function addRestData(){
    $this->registerRoute( 'username_validation', array( $this, 'usernameValidationCallback' ) );
    $this->registerRoute( 'userphone_validation', array( $this, 'userPhoneValidationCallback' ) );
  }

  // USERNAME VALIDATION
  function usernameValidationCallback( WP_REST_Request $args ){
    if( $args['username'] ){
      return username_exists( $args['username'] ) ? $this->signupSuccessResponse(true) : $this->signupSuccessResponse(false);
    }
    else {
      return $this->signupErrorResponse("Username");
    }
  }

  // USER PHONE NUMBER VALIDATION
  function userPhoneValidationCallback( WP_REST_Request $args ){
    if( $args['user_phone'] ){
      $yka_util = YKA_WP_UTIL::getInstance();
      return $yka_util->yka_userphone_exists( $args['user_phone'] ) ? $this->signupSuccessResponse(true) : $this->signupSuccessResponse(false);
    }
    else {
      return $this->signupErrorResponse("User Phone Number");
    }
  }

  function signupSuccessResponse( $field_exists ){
    return new WP_REST_Response( array('exists' => $field_exists ) );
  }

  function signupErrorResponse( $field ){
    return new WP_Error( 400, __("$field is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  }

}

YKA_REST_SIGNUP_VALIDATION::getInstance();
