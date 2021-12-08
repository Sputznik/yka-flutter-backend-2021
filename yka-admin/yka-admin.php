<?php

	$modules = array(
		'class-author-meta-box.php',
		'class-yka-admin.php',
		'class-yka-admin-settings.php'
	);

	foreach($modules as $module){
		require_once($module);
	}
