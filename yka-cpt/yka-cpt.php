<?php
  /*
  Plugin Name: YKA CPT
  Plugin URI: http://sputznik.com
  Description: Plugin for Custom Post Type Registartion
  Author: Sputznik
  Version: 1.0
  Author URI: https://sputznik.com
  */

	$inc_files = array(
		'class-yka-cpt-base.php',
		'class-yka-conversations-cpt.php',
		'class-yka-comments-cpt.php',
		'class-yka-learning-capsules.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
