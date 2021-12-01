<?php

class YKA_WP_UTIL extends YKA_BASE{

  /**
   * Determines whether the given user phone number exists.
   *
   * @param string $userphone The userphone number to check for existence.
   * @return true|false true if userphone number already exists , false if does not exist.
   */
  function yka_userphone_exists( $userphone ){
    if( $userphone ){
      global $wpdb;
      $usermeta_table = $wpdb->prefix.'usermeta';
      $query = "SELECT COUNT(user_id) FROM $usermeta_table WHERE meta_key = 'user_phone' AND meta_value = '$userphone';";
      $res = (int) $wpdb->get_var( $query );
      return ( $res > 0 ) ? true : false;
    }
    else{
      return false;
    }
  }

  function yka_username_by_phone( $userphone ){
    if( $userphone ){
      global $wpdb;
      $usermeta_table = $wpdb->prefix.'usermeta';
      $query = "SELECT user_id FROM $usermeta_table WHERE meta_key = 'user_phone' AND meta_value = '$userphone';";
      $user_id = (int) $wpdb->get_var( $query );
      return get_the_author_meta( 'user_login', $user_id );
    }
    else{
      return false;
    }
  }

}

YKA_WP_UTIL::getInstance();
