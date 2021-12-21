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

		add_action( 'admin_menu', array( $this, 'adminMenu' ) );

  }

  /*
	* GETTER AND SETTER FUNCTIONS
	*/

	function getMenu(){ return $this->menu; }
	function setMenu( $menu ){ $this->menu = $menu; }

  /*
	* END OF GETTER AND SETTER FUNCTIONS
	*/

  function adminMenu(){

		foreach( $this->getMenu() as $slug => $menu_item ){

			$menu_item['slug'] = $slug;

			// CHECK FOR MAIN MENU OR SUB MENU
			if( !isset( $menu_item['menu'] ) ){
				add_menu_page( $menu_item['title'], $menu_item['title'], 'manage_options', $menu_item['slug'], array( $this, 'menuPage' ), $menu_item['icon'] );
			}
			else{
				add_submenu_page( $menu_item['menu'], $menu_item['title'], $menu_item['title'], 'manage_options', $menu_item['slug'], array( $this, 'menuPage' ) );
			}

		}

	}

  /* MENU PAGE */
	function menuPage(){
		$page = $_GET[ 'page' ];
		include( 'templates/'.$page.'.php' );
	}

  function formField( $atts ){

    if( isset( $atts['type'] ) ){

      // CHECK IF FORM VALUE IS NOT SET FOR CHECKBOXES THEN SET DEFAULT VALUE TO ARRAY
      switch( $atts['type'] ){
        case 'bt_dropdown_checkboxes':
        case 'checkbox':
          if( !isset( $atts['value'] ) || !is_array( $atts['value'] ) ){ $atts['value'] = array();}
          break;
      }

      $form_field_dir = YKA_PATH . "includes/form-fields/" . $atts['type'] . ".php";

      /* INCLUDE THE FILTER FORM */
      if( file_exists( $form_field_dir ) ){ include( $form_field_dir ); }

    }

  }

  function displayUpdateNotice(){
    _e('<div class="updated inline"><p>Your settings have been saved.</p></div>');
  }

  function getSettings(){
    $value = get_option( 'yka_settings' );
    if( !$value || !is_array( $value ) ) return array();
    return $value;
  }

}

YKA_ADMIN_SETTINGS::getInstance();
