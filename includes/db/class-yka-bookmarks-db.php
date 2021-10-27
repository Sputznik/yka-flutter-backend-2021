<?php

class YKA_BOOKMARKS_DB extends YKA_DB_BASE{

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'bookmarks' );
    parent::__construct();

  }

  function create(){
    global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

    $users_table = $wpdb->prefix . 'users' . '(ID)';
    $posts_table = $wpdb->prefix . 'posts' . '(ID)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id BIGINT(20) UNSIGNED NOT NULL,
      post_id BIGINT(20) UNSIGNED NOT NULL,
      created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
      FOREIGN KEY (user_id) REFERENCES $users_table,
      FOREIGN KEY (post_id) REFERENCES $posts_table
		) $charset_collate;";

		return $this->query( $sql );
	}

}

YKA_BOOKMARKS_DB::getInstance();
