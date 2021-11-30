<?php

$inc_files = array(
	'class-yka-rest-base.php',
	'class-yka-rest-post-base.php',
	'class-yka-rest-conversation.php',
	'class-yka-rest-yka-comment.php',
	'class-yka-rest-learning-capsule.php',
	'class-yka-rest-user.php',
	'class-yka-rest-custom.php',
	'class-yka-rest-response.php',
	'class-yka-rest-signup-validation.php',
	'class-yka-rest-password-reset.php',
	'class-yka-rest-bookmarks.php',
	'class-yka-rest-invites.php'
);

foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
