<?php

class YKA_BOOKMARKS_DB extends YKA_DB_BASE{

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'bookmarks' );
    parent::__construct();
  }

  function create(){

		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id BIGINT(20),
      post_id BIGINT(20),
			PRIMARY KEY(ID)
		) $charset_collate;";

		return $this->query( $sql );
	}

}

YKA_BOOKMARKS_DB::getInstance();
