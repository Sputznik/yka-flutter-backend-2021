<?php

class YKA_FLUTTER_BACKEND{

  public static function plugin_activation(){
    // UPDATE AUTHOR CAPABILITY
    $authorRole = get_role( 'author' );
    $authorRole->add_cap( 'edit_users' );
  }

  public static function plugin_deactivation(){
    // RESET AUTHOR CAPABILITY
    $authorRole = get_role( 'author' );
    $authorRole->remove_cap( 'edit_users' );
  }

}
