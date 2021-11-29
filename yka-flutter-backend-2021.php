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

define( 'YKA_VERSION', time() );
define( 'YKA_PATH', plugin_dir_path( __FILE__ ) );
define( 'YKA_URI', plugin_dir_url( __DIR__ ).'yka-flutter-backend-2021/' ); // GIVES THE ROOT URL OF THE PLUGIN
define( 'YKA_DEFAULT_USER_AVATAR', YKA_URI.'includes/assets/images/default-profile.png' );

register_activation_hook( __FILE__, array( 'YKA_FLUTTER_BACKEND', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'YKA_FLUTTER_BACKEND', 'plugin_deactivation' ) );

$inc_files = array(
  'includes/class-yka-base.php',																	   // BASE CLASS THAT PROVIDES SINGLETON DESIN PATTERN
  'includes/class-yka-flutter-backend.php',
  'includes/yka-cpt/yka-cpt.php',																    // YKA CUSTOM POST TYPES
  'includes/yka-admin/yka-admin.php',														   // ADMIN MODULES
  'includes/yka-rest-authentication/yka-rest-authentication.php',  // AUTHENTICATION
  'includes/yka-rest-api/yka-rest-api.php',											  // REST API
  'includes/db/db.php',                                           // DB
  'includes/lib/lib.php',
);


foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
