<?php
include_once( WPSC_CE_PATH . 'includes/product.php' );
include_once( WPSC_CE_PATH . 'includes/category.php' );
include_once( WPSC_CE_PATH . 'includes/tag.php' );
include_once( WPSC_CE_PATH . 'includes/order.php' );
include_once( WPSC_CE_PATH . 'includes/customer.php' );
include_once( WPSC_CE_PATH . 'includes/coupon.php' );

include_once( WPSC_CE_PATH . 'includes/formatting.php' );

include_once( WPSC_CE_PATH . 'includes/export-csv.php' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_detect_non_wpsc_install() {

		if( !wpsc_is_wpsc_activated() && ( wpsc_is_jigo_activated() || wpsc_is_woo_activated() ) ) {
			$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';
			$message = __( 'We have detected another e-Commerce Plugin than WP e-Commerce activated, please check that you are using Store Exporter Deluxe for the correct platform.', 'wpsc_ce' ) . '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'wpsc_ce' ) . '</a>';
			wpsc_ce_admin_notice( $message, 'error', 'plugins.php' );
		}
		wpsc_ce_plugin_page_notices();

	}

	function wpsc_ce_plugin_page_notices() {

		global $pagenow;

		if( $pagenow == 'plugins.php' ) {
			if( wpsc_is_woo_activated() || wpsc_is_jigo_activated() ) {
				$r_plugins = array(
					'wp-e-commerce-store-exporter/exporter.php',
					'wp-e-commerce-exporter/exporter.php'
				);
				$i_plugins = get_plugins();
				foreach( $r_plugins as $path ) {
					if( isset( $i_plugins[$path] ) ) {
						add_action( 'after_plugin_row_' . $path, 'wpsc_ce_plugin_page_notice', 10, 3 );
						break;
					}
				}
			}
		}
	}

	function wpsc_ce_plugin_page_notice( $file, $data, $context ) {

		if( is_plugin_active( $file ) ) { ?>
<tr class='plugin-update-tr su-plugin-notice'>
	<td colspan='3' class='plugin-update colspanchange'>
		<div class='update-message'>
			<?php printf( __( '%1$s is intended to be used with a WP e-Commerce store, please check that you are using Store Exporter with the correct e-Commerce platform.', 'wpsc_ce' ), $data['Name'] ); ?>
		</div>
	</td>
</tr>
<?php
		}

	}

	// Display admin notice on screen load
	function wpsc_ce_admin_notice( $message = '', $priority = 'updated', $screen = '' ) {

		if( empty( $priority ) )
			$priority = 'updated';
		if( !empty( $message ) )
			add_action( 'admin_notices', wpsc_ce_admin_notice_html( $message, $priority, $screen ) );

	}

	// HTML template for admin notice
	function wpsc_ce_admin_notice_html( $message = '', $priority = 'updated', $screen = '' ) {

		// Display admin notice on specific screen
		if( !empty( $screen ) ) {
			global $pagenow;
			if( $pagenow <> $screen )
				return;
		} ?>
<div id="message" class="<?php echo $priority; ?>">
	<p><?php echo $message; ?></p>
</div>
<?php

	}

	// Add Store Export to WordPress Administration menu
	function wpsc_ce_add_modules_admin_pages( $page_hooks, $base_page ) {

		$page_hooks[] = add_submenu_page( $base_page, __( 'Store Export', 'wpsc_ce' ), __( 'Store Export', 'wpsc_ce' ), 'manage_options', 'wpsc_ce', 'wpsc_ce_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_ce_add_modules_admin_pages', 10, 2 );

	// HTML template header on Store Exporter screen
	function wpsc_ce_template_header( $title = '', $icon = 'tools' ) {

		if( $title )
			$output = $title;
		else
			$output = __( 'Store Export', 'wpsc_ce' );
		$icon = wpsc_is_admin_icon_valid( $icon ); ?>
<div class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32"><br /></div>
	<h2>
		<?php echo $output; ?>
		<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'wpsc_ce' ); ?></a>
	</h2>
<?php
	}

	// HTML template footer on Store Exporter screen
	function wpsc_ce_template_footer() { ?>
</div>
<?php
	}

	// HTML template for header prompt on Store Exporter screen
	function wpsc_ce_support_donate() {

		$output = '';
		$show = true;
		if( function_exists( 'wpsc_vl_we_love_your_plugins' ) ) {
			if( in_array( WPSC_CE_DIRNAME, wpsc_vl_we_love_your_plugins() ) )
				$show = false;
		}
		if( function_exists( 'wpsc_cd_admin_init' ) )
			$show = false;
		if( $show ) {
			$donate_url = 'http://www.visser.com.au/#donations';
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . WPSC_CE_DIRNAME;
			$output = '
	<div id="support-donate_rate" class="support-donate_rate">
		<p>' . sprintf( __( '<strong>Like this Plugin?</strong> %s and %s.', 'wpsc_ce' ), '<a href="' . $donate_url . '" target="_blank">' . __( 'Donate to support this Plugin', 'wpsc_ce' ) . '</a>', '<a href="' . add_query_arg( array( 'rate' => '5' ), $rate_url ) . '#postform" target="_blank">rate / review us on WordPress.org</a>' ) . '</p>
	</div>
';
		}
		echo $output;

	}

	// Saves the state of Export fields for next export
	function wpsc_ce_save_fields( $type, $fields = array() ) {

		if( $fields == false )
			$fields = array();
		if( $type && isset( $fields ) )
			wpsc_ce_update_option( $type . '_fields', $fields );

	}

	// Add Store Export to filter types on the WordPress Media screen
	function wpsc_ce_add_post_mime_type( $post_mime_types = array() ) {

		$post_mime_types['text/csv'] = array( __( 'Store Exports', 'wpsc_ce' ), __( 'Manage Store Exports', 'wpsc_ce' ), _n_noop( 'Store Export <span class="count">(%s)</span>', 'Store Exports <span class="count">(%s)</span>' ) );
		return $post_mime_types;

	}
	add_filter( 'post_mime_types', 'wpsc_ce_add_post_mime_type' );

	// In-line display of CSV file and export details when viewed via WordPress Media screen
	function wpsc_ce_read_csv_file( $post = null ) {

		if( !$post ) {
			if( isset( $_GET['post'] ) )
				$post = get_post( $_GET['post'] );
		}

		if( $post->post_type != 'attachment' )
			return false;

		if( $post->post_mime_type != 'text/csv' )
			return false;

		$filename = $post->post_name;
		$filepath = get_attached_file( $post->ID );
		$contents = __( 'No export entries were found, please try again with different export filters.', 'wpsc_ce' );
		if( file_exists( $filepath ) ) {
			$handle = fopen( $filepath, "r" );
			$contents = stream_get_contents( $handle );
			fclose( $handle );
		} else {
			// This resets the _wp_attached_file Post meta key to the correct value
			update_attached_file( $post->ID, $post->guid );
			// Try grabbing the file contents again
			$filepath = get_attached_file( $post->ID );
			if( file_exists( $filepath ) ) {
				$handle = fopen( $filepath, "r" );
				$contents = stream_get_contents( $handle );
				fclose( $handle );
			}
		}
		if( !empty( $contents ) )
			include_once( WPSC_CE_PATH . 'templates/admin/media-csv_file.php' );

		$export_type = get_post_meta( $post->ID, '_wpsc_export_type', true );
		$columns = get_post_meta( $post->ID, '_wpsc_columns', true );
		$rows = get_post_meta( $post->ID, '_wpsc_rows', true );
		$start_time = get_post_meta( $post->ID, '_wpsc_start_time', true );
		$end_time = get_post_meta( $post->ID, '_wpsc_end_time', true );
		$idle_memory_start = get_post_meta( $post->ID, '_wpsc_idle_memory_start', true );
		$data_memory_start = get_post_meta( $post->ID, '_wpsc_data_memory_start', true );
		$data_memory_end = get_post_meta( $post->ID, '_wpsc_data_memory_end', true );
		$idle_memory_end = get_post_meta( $post->ID, '_wpsc_idle_memory_end', true );
		include_once( WPSC_CE_PATH . 'templates/admin/media-export_details.php' );

	}
	add_action( 'edit_form_after_editor', 'wpsc_ce_read_csv_file' );

	// List of Export types used on Store Exporter screen
	function wpsc_ce_return_export_types() {

		$export_types = array();
		$export_types['products'] = __( 'Products', 'wpsc_ce' );
		$export_types['categories'] = __( 'Categories', 'wpsc_ce' );
		$export_types['tags'] = __( 'Tags', 'wpsc_ce' );
		$export_types['orders'] = __( 'Orders', 'wpsc_ce' );
		$export_types['customers'] = __( 'Customers', 'wpsc_ce' );
		$export_types['coupons'] = __( 'Coupons', 'wpsc_ce' );
		$export_types = apply_filters( 'wpsc_ce_export_types', $export_types );
		return $export_types;

	}

	// Returns label of Export type slug used on Store Exporter screen
	function wpsc_ce_export_type_label( $export_type = '', $echo = false ) {

		$output = '';
		if( !empty( $export_type ) ) {
			$export_types = wpsc_ce_return_export_types();
			if( array_key_exists( $export_type, $export_types ) )
				$output = $export_types[$export_type];
		}
		if( $echo )
			echo $output;
		else
			return $output;

	}

	// HTML active class for the currently selected tab on the Store Exporter screen
	function wpsc_ce_admin_active_tab( $tab_name = null, $tab = null ) {

		if( isset( $_GET['tab'] ) && !$tab )
			$tab = $_GET['tab'];
		else if( !isset( $_GET['tab'] ) && wpsc_ce_get_option( 'skip_overview', false ) )
			$tab = 'export';
		else
			$tab = 'overview';

		$output = '';
		if( isset( $tab_name ) && $tab_name ) {
			if( $tab_name == $tab )
				$output = ' nav-tab-active';
		}
		echo $output;

	}

	// HTML template for each tab on the Store Exporter screen
	function wpsc_ce_tab_template( $tab = '' ) {

		if( !$tab )
			$tab = 'overview';

		// Store Exporter Deluxe
		$wpsc_cd_exists = false;
		if( !function_exists( 'wpsc_cd_admin_init' ) ) {
			$wpsc_cd_url = 'http://www.visser.com.au/wp-ecommerce/plugins/exporter-deluxe/';
			$wpsc_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'wpsc_ce' ) . '</a>', $wpsc_cd_url );
		} else {
			$wpsc_cd_exists = true;
		}
		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

		switch( $tab ) {

			case 'overview':
				$skip_overview = wpsc_ce_get_option( 'skip_overview', false );
				break;

			case 'export':

				global $wpsc_purchlog_statuses;

				$export_type = ( isset( $_POST['dataset'] ) ? $_POST['dataset'] : 'products' );

				$products = wpsc_ce_return_count( 'products' );
				$categories = wpsc_ce_return_count( 'categories' );
				$tags = wpsc_ce_return_count( 'tags' );
				$orders = wpsc_ce_return_count( 'orders' );
				$coupons = wpsc_ce_return_count( 'coupons' );
				$customers = wpsc_ce_return_count( 'customers' );

				if( $product_fields = wpsc_ce_get_product_fields() ) {
					foreach( $product_fields as $key => $product_field ) {
						if( !isset( $product_fields[$key]['disabled'] ) )
							$product_fields[$key]['disabled'] = 0;
					}
					$args = array(
						'hide_empty' => 1
					);
					$product_categories = wpsc_ce_get_product_categories( $args );
					$args = array(
						'hide_empty' => 1
					);
					$product_tags = wpsc_ce_get_product_tags( $args );
					$product_statuses = get_post_statuses();
					$product_statuses['trash'] = __( 'Trash', 'wpsc_ce' );
					$product_orderby = wpsc_ce_get_option( 'product_orderby', 'ID' );
					$product_order = wpsc_ce_get_option( 'product_order', 'DESC' );
				}
				if( $category_fields = wpsc_ce_get_category_fields() ) {
					$category_orderby = wpsc_ce_get_option( 'category_orderby', 'ID' );
					$category_order = wpsc_ce_get_option( 'category_order', 'DESC' );
				}
				if( $tag_fields = wpsc_ce_get_tag_fields() ) {
					$tag_orderby = wpsc_ce_get_option( 'tag_orderby', 'ID' );
					$tag_order = wpsc_ce_get_option( 'tag_order', 'DESC' );
				}
				if( $order_fields = wpsc_ce_get_order_fields() )
					$order_statuses = $wpsc_purchlog_statuses;
				$customer_fields = wpsc_ce_get_customer_fields();
				$coupon_fields = wpsc_ce_get_coupon_fields();

				$limit_volume = wpsc_ce_get_option( 'limit_volume' );
				$offset = wpsc_ce_get_option( 'offset' );
				break;

			case 'archive':
				if( isset( $_GET['deleted'] ) ) {
					$message = __( 'Archived export has been deleted.', 'wpsc_ce' );
					wpsc_ce_admin_notice( $message );
				}
				if( $files = wpsc_ce_get_archive_files() ) {
					foreach( $files as $key => $file )
						$files[$key] = wpsc_ce_get_archive_file( $file );
				}
				break;

			case 'settings':
				$export_filename = wpsc_ce_get_option( 'export_filename', 'wpsc-export_%dataset%-%date%.csv' );
				$delete_csv = wpsc_ce_get_option( 'delete_csv', 0 );
				$timeout = wpsc_ce_get_option( 'timeout', 0 );
				$encoding = wpsc_ce_get_option( 'encoding', 'UTF-8' );
				$bom = wpsc_ce_get_option( 'bom', 1 );
				$delimiter = wpsc_ce_get_option( 'delimiter', ',' );
				$category_separator = wpsc_ce_get_option( 'category_separator', '|' );
				$escape_formatting = wpsc_ce_get_option( 'escape_formatting', 'all' );
				$date_format = wpsc_ce_get_option( 'date_format', 'd/m/Y' );
				$file_encodings = ( function_exists( 'mb_list_encodings' ) ? mb_list_encodings() : false );
				break;

			case 'tools':
				// Product Importer Deluxe
				if( function_exists( 'wpsc_pd_init' ) ) {
					$wpsc_pd_url = add_query_arg( 'page', 'wpsc_pd' );
					$wpsc_pd_target = false;
				} else {
					$wpsc_pd_url = 'http://www.visser.com.au/wp-ecommerce/plugins/product-importer-deluxe/';
					$wpsc_pd_target = ' target="_blank"';
				}
				// Coupon Importer Deluxe
				if( function_exists( 'wpsc_ci_init' ) ) {
					$wpsc_ci_url = add_query_arg( 'page', 'wpsc_ci' );
					$wpsc_ci_target = false;
				} else {
					$wpsc_ci_url = 'http://www.visser.com.au/wp-ecommerce/plugins/coupon-importer-deluxe/';
					$wpsc_ci_target = ' target="_blank"';
				}
				break;

		}
		if( $tab )
			include_once( WPSC_CE_PATH . 'templates/admin/tabs-' . $tab . '.php' );

	}

	function wpsc_ce_export_options_export_format() {

		ob_start(); ?>
<tr>
	<th>
		<label><?php _e( 'Export format', 'wpsc_ce' ); ?></label>
	</th>
	<td>
		<label><input type="radio" name="export_format" value="csv"<?php checked( 'csv', 'csv' ); ?> /> <?php _e( 'CSV', 'wpsc_ce' ); ?> <span class="description"><?php _e( '(Comma separated values)', 'wpsc_ce' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xml" disabled="disabled" /> <?php _e( 'XML', 'wpsc_ce' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'wpsc_ce' ); ?></label>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats.', 'wpsc_ce' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	// Displays a HTML notice where the memory allocated to WordPress falls below 64MB
	function wpsc_ce_memory_prompt() {

		if( !wpsc_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = (int)( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				$memory_url = add_query_arg( 'action', 'dismiss_memory_prompt' );
				$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';
				$message = sprintf( __( 'We recommend setting memory to at least %dMB, your site has only %dMB allocated to it. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'wpsc_ce' ), $minimum_memory_limit, $memory_limit, $troubleshooting_url ) . '<span style="float:right;"><a href="' . $memory_url . '">' . __( 'Dismiss', 'wpsc_ce' ) . '</a></span>';
				wpsc_ce_admin_notice( $message, 'error' );
			}
		}

	}

	// Displays a HTML notice when a WordPress or Store Exporter error is encountered
	function wpsc_ce_fail_notices() {

		wpsc_ce_memory_prompt();
		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';
		if( isset( $_GET['failed'] ) ) {
			$message = '';
			if( isset( $_GET['message'] ) )
				$message = urldecode( $_GET['message'] );
			if( $message )
				$message = __( 'A WordPress or server error caused the exporter to fail, the exporter was provided with a reason: ', 'wpsc_ce' ) . '<em>' . $message . '</em>' . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'wpsc_ce' ) . '</a>)';
			else
				$message = __( 'A WordPress or server error caused the exporter to fail, no reason was provided, please get in touch so we can reproduce and resolve this.', 'wpsc_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'wpsc_ce' ) . '</a>)';
			wpsc_ce_admin_notice( $message, 'error' );
		}
		if( isset( $_GET['empty'] ) ) {
			$message = __( 'No export entries were found, please try again with different export filters.', 'wpsc_ce' );
			wpsc_ce_admin_notice( $message, 'error' );
		}
		if( get_transient( WPSC_CE_PREFIX . '_running' ) ) {
			$message = __( 'A WordPress or server error caused the exporter to fail with a blank screen, this is either a memory or timeout issue, please get in touch so we can reproduce and resolve this.', 'wpsc_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'wpsc_ce' ) . '</a>)';
			wpsc_ce_admin_notice( $message, 'error' );
			delete_transient( WPSC_CE_PREFIX . '_running' );
		}

	}

	// Returns a list of archived exports
	function wpsc_ce_get_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_wpsc_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => 'text/csv',
			'meta_key' => $meta_key,
			'meta_value' => null,
			'posts_per_page' => -1,
			'cache_results' => false,
			'no_found_rows' => false
		);
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( !empty( $filter ) )
				$args['meta_value'] = $filter;
		}
		$files = get_posts( $args );
		return $files;

	}

	// Returns an archived export with additional details
	function wpsc_ce_get_archive_file( $file = '' ) {

		$wp_upload_dir = wp_upload_dir();
		$file->export_type = get_post_meta( $file->ID, '_wpsc_export_type', true );
		$file->export_type_label = wpsc_ce_export_type_label( $file->export_type );
		if( empty( $file->export_type ) )
			$file->export_type = __( 'Unassigned', 'wpsc_ce' );
		if( empty( $file->guid ) )
			$file->guid = $wp_upload_dir['url'] . '/' . basename( $file->post_title );
		$file->post_mime_type = get_post_mime_type( $file->ID );
		if( !$file->post_mime_type )
			$file->post_mime_type = __( 'N/A', 'wpsc_ce' );
		$file->media_icon = wp_get_attachment_image( $file->ID, array( 80, 60 ), true );
		if( $author_name = get_user_by( 'id', $file->post_author ) )
			$file->post_author_name = $author_name->display_name;
		$t_time = strtotime( $file->post_date, current_time( 'timestamp' ) );
		$time = get_post_time( 'G', true, $file->ID, false );
		if( ( abs( $t_diff = time() - $time ) ) < 86400 )
			$file->post_date = sprintf( __( '%s ago' ), human_time_diff( $time ) );
		else
			$file->post_date = mysql2date( __( 'Y/m/d' ), $file->post_date );
		unset( $author_name, $t_time, $time );
		return $file;

	}

	// HTML template for displaying the current export type filter on the Archives screen
	function wpsc_ce_archives_quicklink_current( $current = '' ) {

		$output = '';
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( $filter == $current )
				$output = ' class="current"';
		} else if( $current == 'all' ) {
			$output = ' class="current"';
		}
		echo $output;

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function wpsc_ce_archives_quicklink_count( $type = '' ) {

		$output = '0';
		$post_type = 'attachment';
		$meta_key = '_wpsc_export_type';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'cache_results' => false,
			'no_found_rows' => false
		);
		if( $type )
			$args['meta_value'] = $type;
		if( $posts = get_posts( $args ) )
			$output = count( $posts );
		echo $output;

	}

	/* End of: WordPress Administration */

}

// Returns the Post object of the export file saved as an attachment to the WordPress Media library
function wpsc_ce_save_file_attachment( $filename = '', $post_mime_type = 'text/csv' ) {

	$output = 0;
	if( !empty( $filename ) ) {
		$post_type = 'wpsc-export';
		$args = array(
			'post_title' => $filename,
			'post_type' => $post_type,
			'post_mime_type' => $post_mime_type
		);
		if( $post_ID = wp_insert_attachment( $args, $filename ) )
			$output = $post_ID;
	}
	return $output;

}

// Updates the GUID of the export file attachment to match the correct file URL
function wpsc_ce_save_file_guid( $post_ID, $export_type, $upload_url = '' ) {

	add_post_meta( $post_ID, '_wpsc_export_type', $export_type );
	if( !empty( $upload_url ) ) {
		$args = array(
			'ID' => $post_ID,
			'guid' => $upload_url
		);
		wp_update_post( $args );
	}

}

// Save critical export details against the archived export
function wpsc_ce_save_file_details( $post_ID ) {

	global $export;

	add_post_meta( $post_ID, '_wpsc_start_time', $export->start_time );
	add_post_meta( $post_ID, '_wpsc_idle_memory_start', $export->idle_memory_start );
	add_post_meta( $post_ID, '_wpsc_columns', $export->total_columns );
	add_post_meta( $post_ID, '_wpsc_rows', $export->total_rows );
	add_post_meta( $post_ID, '_wpsc_data_memory_start', $export->data_memory_start );
	add_post_meta( $post_ID, '_wpsc_data_memory_end', $export->data_memory_end );

}

// Update detail of existing archived export
function wpsc_ce_update_file_detail( $post_ID, $detail, $value ) {

	if( strstr( $detail, '_wpsc_' ) !== false )
		update_post_meta( $post_ID, $detail, $value );

}

// Returns a list of allowed Export type statuses, can be overridden on a per-Export type basis
function wpsc_ce_post_statuses( $extra_status = array(), $override = false ) {

	$output = array(
		'publish',
		'pending',
		'draft',
		'future',
		'private',
		'trash'
	);
	if( $override ) {
		$output = $extra_status;
	} else {
		if( $extra_status )
			$output = array_merge( $output, $extra_status );
	}
	return $output;

}

// Returns a list of WordPress User Roles
function wpsc_ce_get_user_roles() {

	global $wp_roles;

	$user_roles = $wp_roles->roles;
	return $user_roles;

}

function wpsc_ce_add_missing_mime_type( $mime_types = array() ) {

	// Add CSV mime type if it has been removed
	if( !isset( $mime_types['csv'] ) )
		$mime_types['csv'] = 'text/csv';
	return $mime_types;

}
add_filter( 'upload_mimes', 'wpsc_ce_add_missing_mime_type', 10, 1 );

if( !function_exists( 'wpsc_ce_current_memory_usage' ) ) {
	function wpsc_ce_current_memory_usage() {

		$output = '';
		if( function_exists( 'memory_get_usage' ) )
			$output = round( memory_get_usage( true ) / 1024 / 1024, 2 );
		return $output;

	}
}

function wpsc_ce_get_option( $option = null, $default = false, $allow_empty = false ) {

	$output = '';
	if( isset( $option ) ) {
		$separator = '_';
		$output = get_option( WPSC_CE_PREFIX . $separator . $option, $default );
		if( $allow_empty == false && $output != 0 && ( $output == false || $output == '' ) )
			$output = $default;
	}
	return $output;

}

function wpsc_ce_update_option( $option = null, $value = null ) {

	$output = false;
	if( isset( $option ) && isset( $value ) ) {
		$separator = '_';
		$output = update_option( WPSC_CE_PREFIX . $separator . $option, $value );
	}
	return $output;

}
?>