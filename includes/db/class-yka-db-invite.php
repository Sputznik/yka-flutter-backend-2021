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

  // CHECK IF USER HAS ALREADY JOINED
  function hasUserAlreadyJoined( $new_user_id, $invite_link ){
    global $wpdb;
		$table_name = $this->getTable();
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
		$query = "SELECT invitee_id, invite_link, new_user_id, created_on as post_date FROM $table";

    if( isset( $args['invitee_id'] ) && $args['invitee_id'] ){
			$invitee_id = $args['invitee_id'];
			$query .= " WHERE invitee_id=$invitee_id";
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
