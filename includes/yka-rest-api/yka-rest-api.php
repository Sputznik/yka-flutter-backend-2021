<?php

	$controllers = array(
		'class-yka-rest-conversations-controller.php'
	);

	foreach( $controllers as $controller ){
		require_once('endpoints/'.$controller);
	}




	add_action( 'rest_api_init', function () {
	  $controller = new YKA_Conversations_Controller;
	  $controller->register_routes();
	} );
