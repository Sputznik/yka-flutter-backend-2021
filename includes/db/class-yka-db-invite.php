<?php

class YKA_DB_INVITE extends YKA_DB_BASE{

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'invites' );
    parent::__construct();

  }

  function create(){
    global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

    $users_table = $wpdb->prefix . 'users' . '(ID)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      invitee_id BIGINT(20) UNSIGNED NOT NULL,
      invite_link VARCHAR(255) NOT NULL,
      new_user_id BIGINT(20) UNSIGNED NULL,
      created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
      FOREIGN KEY (invitee_id) REFERENCES $users_table,
      FOREIGN KEY (new_user_id) REFERENCES $users_table
		) $charset_collate;";

		$this->query( $sql );

  }

}

YKA_DB_INVITE::getInstance();
