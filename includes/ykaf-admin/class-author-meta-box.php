<?php

	class YKAF_AUTHOR_METABOX extends YKAF_BASE{

		function __construct(){

			add_filter( 'ykaf-admin-meta-box-fields', function( $fields ){
				$fields['ykaf-author'] = array( $this, 'render_meta_box' );
				return $fields;
			} );

			add_action( 'ykaf-save-meta-box', array( $this, 'save_meta_box' ), 10, 1 );

			add_action( 'wp_ajax_users_json', array( $this, 'get_users_json' ) );

		}

		function save_meta_box( $post_id ) {
			if( isset( $_POST['author_posts'] ) && $_POST['author_posts'] ){
				$author_id = $_POST['author_posts'];
				global $wpdb;
				$wpdb->update(
					$wpdb->posts,
					array( 'post_author' => $author_id ),
					array( 'ID' => $post_id ),
					array( '%d' ),
					array( '%d' )
				);
			}
		}



		function get_author_name($author){
			$label = $author->display_name;
			if($author->user_email){
				$label .= " (".$author->user_email.")";
			}
			return $label;
		}

		function render_meta_box( $post ){
			$author = get_user_by('ID', $post->post_author);
			$field = array(
				'label' => 'Select Author',
				'slug'	=> 'author_posts',
				'value'	=> $author->ID,
				'placeholder' => 'Type Something',
				'autocomplete_value'	=> $this->get_author_name($author),
				'url'	=> admin_url('admin-ajax.php?action=users_json')
			);

			echo "<div data-behaviour='ykaf-autocomplete' data-field='".wp_json_encode( $field )."'></div>";

		}

		function get_users_json(){
			global $wpdb;
			$search = $_GET['term'];
			$query = "SELECT ID,display_name,user_email FROM ".$wpdb->users." WHERE display_name LIKE '%".$search."%' OR user_email LIKE '%".$search."%' ORDER BY display_name ASC LIMIT 0,10";
    	$posts = array();
			foreach($wpdb->get_results($query) as $row){
				array_push($posts, array('id'=>$row->ID, 'value'=>$this->get_author_name($row)));
			}
			wp_send_json($posts);
		}
	}

	YKAF_AUTHOR_METABOX::getInstance();
