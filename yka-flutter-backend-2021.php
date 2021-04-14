<?php
/*
Plugin Name: YKA FLUTTER BACKEND 2021
Plugin URI: https://sputznik.com/
Description: YKA FLUTTER BACKEND 2021
Version: 1.0.0
Author: Stephen Anil, Sputznik
Author URI: https://sputznik.com/
*/


if( ! defined( 'ABSPATH' ) ){ exit; }

define( 'YKAF_VERSION', time() );
define( 'YKAF_PATH', plugin_dir_path( __FILE__ ) );
define( 'YKAF_URI', plugin_dir_url( __DIR__ ).'yka-flutter-backend-2021/' ); // GIVES THE ROOT URL OF THE PLUGIN


$inc_files = array(
  'includes/class-ykaf-base.php',																	// BASE CLASS THAT PROVIDES SINGLETON DESIN PATTERN
  'includes/ykaf-cpt/ykaf-cpt.php',																// YKA CUSTOM POST TYPES
  'includes/ykaf-admin/ykaf-admin.php',														// ADMIN MODULES
  'includes/ykaf-rest-api/ykaf-rest-api.php'											// REST API
);


	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
