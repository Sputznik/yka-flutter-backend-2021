<?php

	$modules = array(
		'class-author-meta-box.php',
		'class-ykaf-admin.php'
	);

	foreach($modules as $module){
		require_once($module);
	}
