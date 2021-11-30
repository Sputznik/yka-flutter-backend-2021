<?php

class YKA_WP_UTIL extends YKA_BASE{

  /**
   * Determines whether the given user phone number exists.
   *
   * @param string $userphone The userphone number to check for existence.
   * @return true|false true if userphone number does not exist , false if already exists.
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

}

YKA_WP_UTIL::getInstance();
