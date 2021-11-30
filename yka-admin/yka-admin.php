<?php

	$modules = array(
		'class-author-meta-box.php',
		'class-yka-admin.php'
	);

	foreach($modules as $module){
		require_once($module);
	}
