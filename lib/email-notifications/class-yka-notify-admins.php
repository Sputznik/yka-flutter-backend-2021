<?php

class YKA_NOTIFY_ADMINS extends YKA_BASE{

  function __construct(){
    add_action( 'yka_report_post', array( $this, 'notifyReport' ) );
  }

  function notifyReport( $report_details ){
    $this->notify( $report_details, 'report' );
  }

  function notify( $data, $template ){
    ob_start();
    include( "templates/$template.php" );
    $body = ob_get_contents();
    ob_end_clean();
    $this->sendEmail( $body );
  }

  function sendEmail( $body ){
    $site_name = get_bloginfo( 'name' );
    $subject = " Report Notification From " . $site_name;
    $email_details = get_option( 'yka_settings');
    $to_mail = isset( $email_details['email_to_address'] ) ? $email_details['email_to_address'] : '';

    $header = array(
      'Content-Type: text/html; charset=UTF-8'
    );

    $response = wp_mail( $to_mail, $subject, $body, $header );

  }

}

YKA_NOTIFY_ADMINS::getInstance();
