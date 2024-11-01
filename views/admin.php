<?php $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'code_settings'; ?>

<div class="wrap">
	<h2><?php _e('TentBlogger Simple Adsense', 'tentblogger-adsense'); ?></h2>
</div><!-- /.wrap -->

<div id="nav-tabs" class="wrap">
	<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo ($current_tab == 'code_settings' ? 'nav-tab-active' : '') ?>" href="?page=tentblogger-adsense&tab=code_settings"><?php _e('Code', 'tentblogger-adsense'); ?></a>
		<a class="nav-tab <?php echo ($current_tab == 'position_settings' ? 'nav-tab-active' : '') ?>" href="?page=tentblogger-adsense&tab=position_settings"><?php _e('Position', 'tentblogger-adsense'); ?></a>
		<a class="nav-tab <?php echo ($current_tab == 'display_settings' ? 'nav-tab-active' : '') ?>" href="?page=tentblogger-adsense&tab=display_settings"><?php _e('Display', 'tentblogger-adsense'); ?></a>
	</h2><!-- /.nav-tab-wrapper -->
</div><!-- /#nav-tabs -->

<div class="wrap">
	
	<form method="post" action="options.php">
		<?php wp_nonce_field('tentblogger-adsense-update-options'); ?>
		<?php settings_fields($current_tab); ?>
		<?php do_settings_sections($current_tab); ?>
		<?php submit_button(); ?>
	</form>
</div><!-- /.wrap -->