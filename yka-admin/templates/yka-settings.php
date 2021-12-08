<?php

	$screens = array(
		'general'	=> array(
			'label'			=> 'Emails',
			'template'	=> plugin_dir_path(__FILE__).'settings-email.php'
		)
		// 'sample'	=> array(
		// 	'label'			=> 'Sample Page',
		// 	'tab'				=> 'sample-page',
		// 	'template'	=> plugin_dir_path(__FILE__).'sample-page.php'
		// )
	);

	$active_tab = '';
?>
<div class="wrap">
	<h1>YKA Backend Settings</h1>
	<h2 class="nav-tab-wrapper">
	<?php
		foreach( $screens as $slug => $screen ){

			$base_settings_url = "admin.php?page=yka-settings";

			$url = admin_url( $base_settings_url );

			if( isset( $screen['tab'] ) ){
				$url =  esc_url( add_query_arg( array( 'tab' => $screen['tab'] ), admin_url( $base_settings_url ) ) );
			}

			$nav_class = "nav-tab";

			if( isset( $screen['tab'] ) && isset( $_GET['tab'] ) && $screen['tab'] == $_GET['tab'] ){
				$nav_class .= " nav-tab-active";
				$active_tab = $slug;
			}

			if( ! isset( $screen['tab'] ) && ! isset( $_GET['tab'] ) ){
				$nav_class .= " nav-tab-active";
				$active_tab = $slug;
			}

			echo '<a href="'.$url.'" class="'.$nav_class.'">'.$screen['label'].'</a>';
		}
	?>
	</h2>
	<?php

		if( file_exists( $screens[ $active_tab ][ 'template' ] ) ){
			include( $screens[ $active_tab ][ 'template' ] );
		}

	?>
</div>
