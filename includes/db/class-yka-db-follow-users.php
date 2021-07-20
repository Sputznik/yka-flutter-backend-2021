<?php

class YKA_DB_FOLLOW_USERS extends YKA_DB_BASE{

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'follow_users' );
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
      following_id BIGINT(20) UNSIGNED NOT NULL,
			PRIMARY KEY(ID),
      FOREIGN KEY (user_id) REFERENCES $users_table,
      FOREIGN KEY (following_id) REFERENCES $users_table
		) $charset_collate;";

    $this->query( $sql );

  }
  
  function getUserIDs( $user_id, $type = "followers"){
    global $wpdb;
    $table = $this->getTable();
    $users_query = "SELECT user_id FROM $table WHERE following_id = $user_id;";
    if( $type == "following" ){ $users_query = "SELECT following_id FROM $table WHERE user_id = $user_id;"; }
    $result = array_map('intval', $wpdb->get_col( $users_query ) );
    return $result;
  }

}

YKA_DB_FOLLOW_USERS::getInstance();
