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
  'class-yka-base.php',															         // BASE CLASS THAT PROVIDES SINGLETON DESIN PATTERN
  'yka-cpt/yka-cpt.php',																    // YKA CUSTOM POST TYPES
  'yka-admin/yka-admin.php',														   // ADMIN MODULES
  'yka-rest-authentication/yka-rest-authentication.php',  // AUTHENTICATION
  'yka-rest-api/yka-rest-api.php',											  // REST API
  'db/db.php',                                           // DB
  'lib/lib.php',
);


foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
