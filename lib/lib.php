<?php

$inc_files = array(
  'class-yka-flutter-backend.php',
  'class-yka-wp-util.php'
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}