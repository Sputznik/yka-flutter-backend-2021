<?php
class YKA_DB_USER_DEVICE_DETAILS extends YKA_DB_BASE{

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'user_device_details' );
    parent::__construct();

  }

  function create(){
    global $wpdb;
    $table = $this->getTable();
    $charset_collate = $this->get_charset_collate();

    $users_table = $wpdb->prefix . 'users' . '(ID)';

    $sql = "CREATE TABLE IF NOT EXISTS $table (
      ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id BIGINT(20) UNSIGNED NOT NULL,
      fcm_token LONGTEXT NOT NULL,
      login_status TINYINT(1) NOT NULL,
      created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY(ID),
      FOREIGN KEY (user_id) REFERENCES $users_table
    ) $charset_collate;";

    $this->query( $sql );


  }

  // CHECK IF FCM TOKEN ALREADY EXISTS
  function fcmTokenExists( $fcm_token, $user_id ){
    global $wpdb;
    $table_name = $this->getTable();
    $rs = (int) $this->get_var( "SELECT COUNT(user_id) FROM $table_name WHERE user_id = $user_id AND fcm_token = '$fcm_token';" );
    if( !$rs ){
      return false;
    }
    return true;
  }

  // RETURN USER DEVICE DETAILS
  function getUserDeviceDetails( $user_id ){
    global $wpdb;
    $data = array();
    $table_name = $this->getTable();
    $query = $wpdb->prepare( "SELECT fcm_token, login_status FROM $table_name WHERE user_id = %d", array( $user_id ) );
		$rows = $wpdb->get_results( $query );
    foreach( $rows as $row ){
		  $data[] = array(
        'fcm_token' =>  $row->fcm_token,
        'login_status'  =>  $row->login_status ? true : false
      );
		}

    return $data;
  }

}

YKA_DB_USER_DEVICE_DETAILS::getInstance();
