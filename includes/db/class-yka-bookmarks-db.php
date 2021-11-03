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

  // RETURNS THE ID OF A USER'S BOOKMARK
  function getBookmarkIDs( $user_id ){
    global $wpdb;
    $table = $this->getTable();
    $bookmarks_query = "SELECT post_id FROM $table WHERE user_id = $user_id;";
    $result = array_map('intval', $wpdb->get_col( $bookmarks_query ) );
    return $result;
  }

  //CHECK IF ALREADY BOOKMARKED
	function isBookmarked( $post_id ){
		global $wpdb;
		$user_id = get_current_user_id();
		$table_name = $this->getTable();
		$rs = $wpdb->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id AND post_id = $post_id" );
		if( null != $rs ) return true;
		return false;
	}

  function getBookmarksCount( $post_id ){
    global $wpdb;
		$table = $this->getTable();
    $bookmarks_query = "SELECT COUNT(user_id) FROM $table WHERE post_id = $post_id;";
    $result = $this->get_var( $bookmarks_query );
    return $result;
  }

}

YKA_BOOKMARKS_DB::getInstance();
