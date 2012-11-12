<script type="text/javascript">
	function showProgress() {
		window.scrollTo(0,0);
		document.getElementById('progress').style.display = 'block';
		document.getElementById('content').style.display = 'none';
	}
</script>
<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="overview" class="nav-tab<?php wpsc_ce_admin_active_tab( 'overview' ); ?>" href="edit.php?post_type=wpsc-product&amp;page=wpsc_ce"><?php _e( 'Overview', 'wpsc_ce' ); ?></a>
		<a data-tab-id="export" class="nav-tab<?php wpsc_ce_admin_active_tab( 'export' ); ?>" href="edit.php?post_type=wpsc-product&amp;page=wpsc_ce&amp;tab=export"><?php _e( 'Export', 'wpsc_ce' ); ?></a>
		<a data-tab-id="tools" class="nav-tab<?php wpsc_ce_admin_active_tab( 'tools' ); ?>" href="edit.php?post_type=wpsc-product&amp;page=wpsc_ce&amp;tab=tools"><?php _e( 'Tools', 'wpsc_ce' ); ?></a>
	</h2>
	<?php wpsc_ce_tab_template( $tab ); ?>

</div>
<div id="progress" style="display:none;">
	<p><?php _e( 'Chosen WP e-Commerce details are being exported, this process can take awhile. Time for a beer?', 'wpsc_ce' ); ?></p>
	<img src="<?php echo plugins_url( '/templates/admin/images/progress.gif', $wpsc_ce['relpath'] ); ?>" alt="" />
	<p><?php _e( 'Return to <a href="' . $url . '">WP e-Commerce Exporter</a>.', 'wpsc_ce' ); ?>
</div>