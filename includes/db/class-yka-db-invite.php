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
      timestamp BIGINT(20) UNSIGNED NOT NULL,
      created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(ID),
      FOREIGN KEY (invitee_id) REFERENCES $users_table,
      FOREIGN KEY (new_user_id) REFERENCES $users_table
		) $charset_collate;";

		$this->query( $sql );

  }

  // function alter_table(){
	// 	$table = $this->getTable();
  // 	$sql = "ALTER TABLE $table ADD `timestamp` BIGINT(20) UNSIGNED NOT NULL AFTER `new_user_id`;";
	// 	echo "Added timestamp columm in $table <br>";
	// 	return $this->query( $sql );
	// }

  // CHECK IF USER HAS ALREADY JOINED
  function hasUserAlreadyJoined( $new_user_id, $invite_link ){
    global $wpdb;
		$table_name = $this->getTable( $invite_link );
    $link_expired = (int) $this->get_var( "SELECT COUNT(new_user_id) FROM $table_name WHERE invite_link = '$invite_link';" );

    if( ! $link_expired ){
      $rs = (int) $this->get_var( "SELECT COUNT(ID) FROM $table_name WHERE invitee_id = $new_user_id OR new_user_id = $new_user_id;" );
      if( !$rs ){
        return false;
      }

    }
    return true;
  }

  // CHECK IF USER ID OR INVITE LINK ALREADY EXISTS
  function inviteExists( $invitee_id, $invite_link ){
    $table = $this->getTable();
    $query = "SELECT COUNT(*) FROM $table WHERE (invitee_id = $invitee_id AND invite_link = '$invite_link') OR invite_link = '$invite_link';";
		$inviteFlag = $this->get_var( $query );
    if( $inviteFlag ) return true;
		return false;
  }

  function getResultsQuery( $args ){
		$table = $this->getTable();
    $filter_query = array();
    $default_filters = array( 'invitee_id','invite_link' );
		$query = "SELECT invitee_id, invite_link, new_user_id, timestamp, created_on as post_date FROM $table";

    // LOOP THROUGH FILTER PARAMS IF ANY TO GET RESULTS BY INVITEE ID, INVITE_LINK
    foreach ( $default_filters as $filter ){
			if( isset( $args[$filter] ) && $args[$filter] ){
        $str = "$filter="."'".$args[$filter]."'";
        array_push( $filter_query, $str );
      }
    }

    if( count( $filter_query ) > 0 ){
      $query .= " WHERE ".implode(" AND ", $filter_query);
    }

		return $query;
	}

  function getInvitesCount( $user_id ){
    $table = $this->getTable();
    $query = "SELECT COUNT(*) FROM $table WHERE invitee_id = $user_id;";
    return (int) $this->get_var( $query );
  }

}

YKA_DB_INVITE::getInstance();
