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


    /* CREATE TAXNOMIES */
    if( ! isset( $yka_vars['taxonomies'] ) ){
      $yka_vars['taxonomies'] = array();
    }
    /* HOOK TO ADD CUSTOM POST TYPE */
    $yka_vars['taxonomies'] = apply_filters( 'yka_taxonomy_vars', $yka_vars['taxonomies'] );

    foreach( $yka_vars['taxonomies'] as $taxonomy ){
      $this->create_taxonomy( $taxonomy );
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
      'hierarchical'       => isset( $post_type['hierarchical'] ) && $post_type['hierarchical'] ? $post_type['hierarchical'] : false,
      'menu_icon'						=> isset( $post_type['menu_icon'] ) && $post_type['menu_icon'] ? $post_type['menu_icon'] : 'dashicons-images-alt',
      'taxonomies'					=> isset( $post_type['taxonomies'] ) ? $post_type['taxonomies'] : array(),
      'supports'						=>	$post_type['supports'],
      'show_in_rest' 				=> isset( $post_type['show_in_rest'] ) && $post_type['show_in_rest'] ? $post_type['show_in_rest'] : false,
    ) );

  }

  /* CREATE CUSTOM TAXONOMIES */
  function create_taxonomy( $taxonomy ) {

    $defaults = array(
      'hierarchical' 		=> true,
      'show_admin_column' => true,
      'show_ui' 			=> true,
      'show_in_menu' 		=> true
    );

    $r = wp_parse_args( $taxonomy, $defaults );

    $labels = array(
      'name' 							=> _x( $r['label'], 'taxonomy general name' ),
      'singular_name' 				=> _x( $r['label'], 'taxonomy singular name' ),
      'search_items' 					=>  __( 'Search '.$r['label'] ),
      'popular_items' 				=> __( 'Popular '.$r['label'] ),
      'all_items' 					=> __( 'All '.$r['label'] ),
      'parent_item' 					=> null,
      'parent_item_colon' 			=> null,
      'edit_item' 					=> __( 'Edit '.$r['label'] ),
      'update_item' 					=> __( 'Update '.$r['label'] ),
      'add_new_item' 					=> __( 'Add New '.$r['label'] ),
      'new_item_name' 				=> __( 'New '.$r['label'] ),
      'separate_items_with_commas' 	=> __( 'Separate '.$r['label'].' with commas' ),
      'add_or_remove_items' 			=> __( 'Add or remove '.$r['label'] ),
      'choose_from_most_used' 		=> __( 'Choose from the most used '.$r['label'] ),
      'menu_name' 					=> __( $r['label'] ),
    );

    register_taxonomy( $r['slug'], $taxonomy['post_types'], array(
      'hierarchical' 			=> $r['hierarchical'],
      'labels' 				=> $labels,
      'show_ui' 				=> $r['show_ui'],
      'show_admin_column' 	=> $r['show_admin_column'],
      'update_count_callback' => '_update_post_term_count',
      'query_var' 			=> true,
      'show_in_menu' 			=> $r['show_in_menu'],
      'rewrite' 				=> array( 'slug' => $r['slug'] ),
      'show_in_rest'		=> isset( $r['show_in_rest'] ) && $r['show_in_rest'] ? $r['show_in_rest'] : false,
    ));
  }

}

YKA_CPT_BASE::getInstance();
