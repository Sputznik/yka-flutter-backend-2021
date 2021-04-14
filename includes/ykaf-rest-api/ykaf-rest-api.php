<?php

	$controllers = array(
		'class-ykaf-rest-conversations-controller.php'
	);

	foreach( $controllers as $controller ){
		require_once('endpoints/'.$controller);

		echo('endpoints/'.$controller);
	}




	add_action( 'rest_api_init', function () {
	  $controller = new YKAF_Conversations_Controller;
	  $controller->register_routes();
	} );
