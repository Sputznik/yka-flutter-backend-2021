<?php
/*
* RELATION TABLE BETWEEN LEARNING CAPSULE AND CONVERSATION
*/

class YKA_DB_CAPSULE_CONVERSATION_RELATION extends YKA_DB_BASE{

	function __construct(){
		$this->setTableSlug( 'capsule_conversation_relation' );

		parent::__construct();
	}

	function create(){
		global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$posts_table = $wpdb->prefix . 'posts' . '(ID)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			capsule_id BIGINT(20) UNSIGNED NOT NULL,
			conversation_id BIGINT(20) UNSIGNED NOT NULL,
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
			FOREIGN KEY (capsule_id) REFERENCES $posts_table,
			FOREIGN KEY (conversation_id) REFERENCES $posts_table
		) $charset_collate;";
		$this->query( $sql );

	}

	function getConversationIDForCapsule( $capsule_id ){
		$table = $this->getTable();
		global $wpdb;
		return $this->get_var( "SELECT conversation_id FROM $table WHERE capsule_id = $capsule_id;" );
	}

	function getCapsuleIDForConversation( $conversation_id ){
		$table = $this->getTable();
		global $wpdb;
		return $this->get_var( "SELECT capsule_id FROM $table WHERE conversation_id = $conversation_id;" );
	}

	// CHECK IF CAPSULE OR CONVERSATION ENTRIES EXIST
	function relationExists( $conversation_id, $capsule_id ){
		$table = $this->getTable();
		$relationFlag = $this->get_var( "SELECT COUNT(*) FROM $table WHERE conversation_id = $conversation_id OR capsule_id = $capsule_id;" );
		if( $relationFlag ) return true;
		return false;
	}

}

YKA_DB_CAPSULE_CONVERSATION_RELATION::getInstance();
