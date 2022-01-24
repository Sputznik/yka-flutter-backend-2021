<?php

class YKA_DB_NOTIFICATIONS extends YKA_DB_BASE{

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'notifications' );
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
      action VARCHAR(255) NOT NULL,
      description LONGTEXT NOT NULL,
      payload LONGTEXT NOT NULL,
      created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY(ID),
      FOREIGN KEY (user_id) REFERENCES $users_table
    ) $charset_collate;";

    $this->query( $sql );

  }

}

YKA_DB_NOTIFICATIONS::getInstance();
