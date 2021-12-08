<?php

// $post_id      = $data['post_id'];
$post_title   = $data['post_title'];
$post_author  = $data['post_author'];
$current_user = $data['current_user'];
$reason       = $data['reason'];

?>
<p><?php echo $current_user; ?> has reported a post.</p>
<p>
  <strong>Details:</strong>
  <br/>
  Post Author: <?php echo $post_author; ?>
  <br/>
  Post Title: <?php echo $post_title; ?>
  <br/>
  Reason: <?php echo $reason; ?>
</p>
