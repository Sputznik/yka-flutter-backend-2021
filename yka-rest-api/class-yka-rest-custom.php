<?php

class YKA_REST_CUSTOM extends YKA_REST_BASE{

	function getSettingsCallback( WP_REST_Request $args ){
		global $yka_vars;

		$data = array();

		$taxonomies = $yka_vars['taxonomies'];

		foreach( $taxonomies as $key => $taxonomy ){
			$data[ $key ] = get_terms( array(
				'taxonomy' 		=> $taxonomy['slug'],
				'hide_empty' 	=> false,
				'fields'			=> 'id=>name'
			) );
		}

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function addRestData(){
		$this->registerRoute( 'settings', array( $this, 'getSettingsCallback' ) );
	}

}
YKA_REST_CUSTOM::getInstance();
