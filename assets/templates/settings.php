<div class="wrap">
	<h1><?php _e('Recommender Settings', 'recommender'); ?></h1>
	<?php
	settings_errors();

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'setting_options';
	?>

	<nav class="nav-tab-wrapper">
		<a href="?page=recommender_settings&tab=setting_options" class="nav-tab <?php echo $active_tab == 'setting_options' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'recommender'); ?></a>
		<a href="?page=recommender_settings&tab=advanced_setting_options" class="nav-tab <?php echo $active_tab == 'advanced_setting_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Advanced', 'recommender'); ?></a>
	</nav>
	 
	<form method="post" action="options.php">
	<?php
	if( $active_tab == 'setting_options' ):

		settings_fields( 'recommender_setting_options' );
		do_settings_sections( 'recommender_setting_options' );

	elseif( $active_tab == 'advanced_setting_options' ):

		settings_fields( 'recommender_advanced_setting_options' );
		do_settings_sections( 'recommender_advanced_setting_options' );

	endif;

	submit_button();
	?>
	</form>
</div>