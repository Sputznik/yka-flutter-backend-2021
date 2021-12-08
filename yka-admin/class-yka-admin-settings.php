<?php

class YKA_ADMIN_SETTINGS extends YKA_BASE{

  var $menu;
	var $settings;

  function __construct(){

    $this->setMenu( array(
      'yka-settings' => array(
        'title'	=> 'YKA Settings',
        'icon'	=> 'dashicons-editor-kitchensink'
      )
      // 'sample-page'	=> array(
			// 	'title'	=> 'Sample Page',
			// 	'menu'	=> 'yka-settings'
			// )
		) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

  }

  /*
	* GETTER AND SETTER FUNCTIONS
	*/

	function getMenu(){ return $this->menu; }
	function setMenu( $menu ){ $this->menu = $menu; }

  /*
	* END OF GETTER AND SETTER FUNCTIONS
	*/

  function admin_menu(){

		foreach( $this->getMenu() as $slug => $menu_item ){

			$menu_item['slug'] = $slug;

			// CHECK FOR MAIN MENU OR SUB MENU
			if( !isset( $menu_item['menu'] ) ){
				add_menu_page( $menu_item['title'], $menu_item['title'], 'manage_options', $menu_item['slug'], array( $this, 'menu_page' ), $menu_item['icon'] );
			}
			else{
				add_submenu_page( $menu_item['menu'], $menu_item['title'], $menu_item['title'], 'manage_options', $menu_item['slug'], array( $this, 'menu_page' ) );
			}

		}

	}

  /* MENU PAGE */
	function menu_page(){
		$page = $_GET[ 'page' ];
		include( 'templates/'.$page.'.php' );
	}

}

YKA_ADMIN_SETTINGS::getInstance();
