<?php

// $post_id      = $data['post_id'];
$post_title   = $data['post_title'];
$post_author  = $data['post_author'];
$post_type    = get_post_type( $data['post_id'] );
$current_user = $data['current_user'];
$reason       = $data['reason'];
$settings     = get_option('yka_settings');
$email_content = isset( $settings['emails']['report']['email_body'] ) ? $settings['emails']['report']['email_body'] : '';

eval("\$email_content = \"$email_content\";");

echo $email_content;
?>
