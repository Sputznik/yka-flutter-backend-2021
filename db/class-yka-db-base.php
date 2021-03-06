<?php

/*
* ABSTRACT MODEL THAT HANDLES SQL QUERIES AND ACTS AS A WRAPPER FOR WPDB
*/

class YKA_DB_BASE extends YKA_BASE{

  private $table;
	private $table_slug;

  function __construct(){

    // SET TABLE SLUG
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

  /* GETTER AND SETTER FUNCTIONS */

  // WRAPPER AROUND WPDB->QUERY
	function query( $sql ){
		global $wpdb;
		return $wpdb->query( $sql );
	}

  // WRAPPER AROUND WPDB->INSERT
	function insert( $data ){
		global $wpdb;
		$wpdb->insert( $this->getTable(), $data );
		return $wpdb->insert_id;
	}

  function update( $id, $data, $format = array() ){
		global $wpdb;
		return $wpdb->update( $this->getTable(), $data, array( 'ID' => $id ), $format );
	}

  // UPDATE A ROW WITH A CUSTOM WHERE CLAUSE
  function updateWhere( $data, $where, $format = array() ){
		global $wpdb;
		return $wpdb->update( $this->getTable(), $data, $where, $format );
	}

  // DELETE SPECIFIC ROW
	function delete_row( $ID ){
		$table = $this->getTable();
		$sql = "DELETE FROM $table WHERE ID = %d;";
		$this->query( $this->prepare( $sql, $ID ) );
	}

	// WRAPPER AROUND WPDB->GET_RESULTS
	function get_results( $sql ){
		global $wpdb;
		return $wpdb->get_results( $sql );
	}

	// WRAPPER AROUND WPDB->GET_VAR
	function get_var( $sql ){
		global $wpdb;
		return $wpdb->get_var( $sql );
	}

	// WRAPPER AROUND WPDB->GET_CHARSET_COLLATE
	function get_charset_collate(){
		global $wpdb;
		return $wpdb->get_charset_collate();
	}

  function delete( $where, $where_format = null ){
		global $wpdb;
		return $wpdb->delete(
			$this->getTable(),
			$where,
			$where_format
		);
	}

  // DROP TABLE
	function drop_table(){
		$table = $this->getTable();
		$query = "DROP TABLE IF EXISTS $table";
		$res = $this->query( $query );
    if( $res ){
      echo "$table Table dropped.<br/>";
    }
	}

  function _limit_query( $page, $per_page ){
		$offset = ( $page - 1 ) * $per_page;
		return " LIMIT $offset,$per_page";
	}

  function getResultsQuery( $args ){
    return '';
  }

  function _orderby_query(){
    return " ORDER BY post_date DESC ";
  }

  function getResults( $args ){
    global $wpdb;

    $page = isset( $args[ 'page' ]  ) ? $args[ 'page' ] : 1;
    $per_page = isset( $args[ 'per_page' ]  ) ? $args[ 'per_page' ] : 10;

    $query = $this->getResultsQuery( $args );
    $countquery = "SELECT count(*) FROM ( $query ) history";
    $mainquery = $query . $this->_orderby_query() . $this->_limit_query( $page, $per_page );
    $rows = $wpdb->get_results( $mainquery );
    $total_count = $wpdb->get_var( $countquery );
    $total_pages = ceil( $total_count/$per_page );

    return array(
      'data'				=> $rows,
      'total'				=> $total_count,
      'total_pages'	=> $total_pages
    );
  }

  // TO BE IMPLEMENTED BY CHILD CLASSES - HANDLES TABLE CREATION
  function create(){}

}
