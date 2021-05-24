<?php

class YKA_BOOKMARKS_DB extends YKA_BASE{

  private $table;
	private $table_slug;

  function __construct(){

    // SET TABLE SLUG
    $this->setTableSlug( 'bookmarks' );

    // SET TABLE
		$this->setTable( $this->getTablePrefix() . $this->getTableSlug() );

		// CREATE TABLE
		$this->create();

  }

  /* GETTER AND SETTER FUNCTIONS */
	function setTable( $table ){ $this->table = $table; }
	function getTable(){ return $this->table; }
	function setTableSlug( $slug ){ $this->table_slug = $slug; }
	function getTableSlug(){ return $this->table_slug; }
	function getTablePrefix(){
		global $wpdb;
		return $wpdb->prefix.'yka_';
	}


  // WRAPPER AROUND WPDB->GET_CHARSET_COLLATE
	function get_charset_collate(){
		global $wpdb;
		return $wpdb->get_charset_collate();
	}

  // WRAPPER AROUND WPDB->QUERY
	function query( $sql ){
		global $wpdb;
		return $wpdb->query( $sql );
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
