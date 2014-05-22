<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="overview" class="nav-tab<?php wpsc_ce_admin_active_tab( 'overview' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'wpsc_ce', 'tab' => 'overview' ), $wpsc_ce_url ); ?>"><?php _e( 'Overview', 'wpsc_ce' ); ?></a>
		<a data-tab-id="export" class="nav-tab<?php wpsc_ce_admin_active_tab( 'export' ); ?>" href="<?php echo add_query_arg( array( 'tab' => 'export' ), $wpsc_ce_url ); ?>"><?php _e( 'Export', 'wpsc_ce' ); ?></a>
		<a data-tab-id="archive" class="nav-tab<?php wpsc_ce_admin_active_tab( 'archive' ); ?>" href="<?php echo add_query_arg( array( 'tab' => 'archive' ), $wpsc_ce_url ); ?>"><?php _e( 'Archives', 'wpsc_ce' ); ?></a>
		<a data-tab-id="settings" class="nav-tab<?php wpsc_ce_admin_active_tab( 'settings' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'wpsc_ce', 'tab' => 'settings' ), $wpsc_ce_url ); ?>"><?php _e( 'Settings', 'wpsc_ce' ); ?></a>
		<a data-tab-id="tools" class="nav-tab<?php wpsc_ce_admin_active_tab( 'tools' ); ?>" href="<?php echo add_query_arg( array( 'tab' => 'tools' ), $wpsc_ce_url ); ?>"><?php _e( 'Tools', 'wpsc_ce' ); ?></a>
	</h2>
	<?php wpsc_ce_tab_template( $tab ); ?>

</div>
<!-- #content -->
