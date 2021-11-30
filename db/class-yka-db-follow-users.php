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

  //CHECK IF CURRENT USER ALREADY FOLLOWED
	function is_following( $following_id ){
		global $wpdb;
		$user_id = get_current_user_id();
		$table_name = $this->getTable();
		$rs = $wpdb->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id AND following_id = $following_id" );
		if( null != $rs ) return true;
		return false;
	}

}

YKA_DB_FOLLOW_USERS::getInstance();
