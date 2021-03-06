<?php

class YKA_REST_SIGNUP_VALIDATION extends YKA_REST_BASE{

  function addRestData(){
    $this->registerRoute( 'username_validation', array( $this, 'usernameValidationCallback' ) );
    $this->registerRoute( 'userphone_validation', array( $this, 'userPhoneValidationCallback' ) );
  }

  // USERNAME VALIDATION
  function usernameValidationCallback( WP_REST_Request $args ){
    if( $args['username'] ){
      return username_exists( $args['username'] ) ? $this->signupSuccessResponse( array('exists' => true ) ) : $this->signupSuccessResponse( array('exists' => false ) );
    }
    else {
      return $this->signupErrorResponse("Username");
    }
  }

  // USER PHONE NUMBER VALIDATION
  function userPhoneValidationCallback( WP_REST_Request $args ){
    if( $args['user_phone'] ){
      $yka_util = YKA_WP_UTIL::getInstance();
      if( $yka_util->yka_userphone_exists( $args['user_phone'] ) ){
        return $this->signupSuccessResponse( array(
                        'exists' => true,
                        'username' => $yka_util->yka_username_by_phone( $args['user_phone'] )
                      ) );
      }

      else{
        return $this->signupSuccessResponse( array( 'exists' => false, 'username' => null ) );
      }
    }
    else {
      return $this->signupErrorResponse("User Phone Number");
    }
  }

  function signupSuccessResponse( $response_obj ){
    return new WP_REST_Response( $response_obj );
  }

  function signupErrorResponse( $field ){
    return new WP_Error( 400, __("$field is required.", 'wp-rest-user'), array( 'status' => 400 ) );
  }

}

YKA_REST_SIGNUP_VALIDATION::getInstance();
