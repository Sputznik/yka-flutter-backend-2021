<?php

	$controllers = array(
		'class-yka-rest-conversations-controller.php',
		'class-yka-rest-comments-controller.php'
	);

	foreach( $controllers as $controller ){
		require_once('endpoints/'.$controller);
	}




	add_action( 'rest_api_init', function () {

		$conversations_controller = new YKA_Conversations_Controller;
	  $conversations_controller->register_routes();

		$comments_controller = new YKA_Comments_Controller;
	  $comments_controller->register_routes();

	} );
