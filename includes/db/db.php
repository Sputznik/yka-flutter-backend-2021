<?php


	$inc_files = array(
		'class-yka-db-base.php',
		'class-yka-bookmarks-db.php',
		'class-yka-db-user-topics.php',
		'class-yka-db-follow-users.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
