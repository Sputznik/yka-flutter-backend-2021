<?php

/* BASE CLASS TO CREATE CUSTOM POST TYPES */

class YKA_CPT_BASE extends YKA_BASE{

  function __construct(){
    add_action( 'init', array( $this, 'init' ) );
  }

  /* FIRES ON ACTION HOOK - INIT*/
  function init(){

    global $yka_vars;

    if( ! isset( $yka_vars['post_types'] ) ){
      $yka_vars['post_types'] = array();
    }


    /* HOOK TO ADD CUSTOM POST TYPE */
    $yka_vars['post_types'] = apply_filters( 'yka_post_type_vars', $yka_vars['post_types'] );

    /* ITERATE THROUGH THE POST TYPES ARRAY AND CREATE THEM */
    foreach( $yka_vars['post_types'] as $post_type ){
      $this->create_post_type( $post_type );
    }

  }

  /* CREATE CUSTOM POST TYPE */
  function create_post_type( $post_type ){

    global $yka_vars;

    if( ! isset( $yka_vars['post_types'] ) ){
      $yka_vars['post_types'] = array();
    }

    if( !isset( $yka_vars['post_types'][ $post_type['slug'] ] ) ){
      $yka_vars['post_types'][ $post_type['slug'] ] = $post_type;
    }

    if( !isset( $post_type[ 'rewrite' ] ) ){
      $post_type[ 'rewrite' ] = array('slug' => $post_type['slug'], 'with_front' => false );
    }


    register_post_type($post_type['slug'], array(
      'labels' 							=> $post_type['labels'],
      'public' 							=> isset( $post_type['public'] ) ? $post_type['public'] : true,
      'publicly_queryable' 	=> true,
      'show_ui'							=> true,
      'query_var' 					=> true,
      'rewrite' 						=> $post_type['rewrite'],
      'has_archive' 				=> true,
      'menu_icon'						=> isset( $post_type['menu_icon'] ) && $post_type['menu_icon'] ? $post_type['menu_icon'] : 'dashicons-images-alt',
      'taxonomies'					=> isset( $post_type['taxonomies'] ) ? $post_type['taxonomies'] : array(),
      'supports'						=>	$post_type['supports'],
      'show_in_rest' 				=> isset( $post_type['show_in_rest'] ) && $post_type['show_in_rest'] ? $post_type['show_in_rest'] : false,
    ) );

  }


}

YKA_CPT_BASE::getInstance();
