<?php
/*
Plugin Name: WP e-Commerce - Store Exporter
Plugin URI: http://www.visser.com.au/wp-ecommerce/plugins/exporter/
Description: Export store details out of WP e-Commerce into a CSV-formatted file.
Version: 1.4.9
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
License: GPL2
*/

load_plugin_textdomain( 'wpsc_ce', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

$wpsc_ce = array(
	'filename' => basename( __FILE__ ),
	'dirname' => basename( dirname( __FILE__ ) ),
	'abspath' => dirname( __FILE__ ),
	'relpath' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ )
);

$wpsc_ce['prefix'] = 'wpsc_ce';
$wpsc_ce['name'] = __( 'WP e-Commerce Exporter', 'wpsc_ce' );
$wpsc_ce['menu'] = __( 'Store Export', 'wpsc_ce' );

include_once( $wpsc_ce['abspath'] . '/includes/functions.php' );
include_once( $wpsc_ce['abspath'] . '/includes/functions-alternatives.php' );
include_once( $wpsc_ce['abspath'] . '/includes/common.php' );

switch( wpsc_get_major_version() ) {

	case '3.7':
		include_once( $wpsc_ce['abspath'] . '/includes/release-3_7.php' );
		break;

	case '3.8':
		include_once( $wpsc_ce['abspath'] . '/includes/release-3_8.php' );
		break;

}

if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_add_settings_link( $links, $file ) {

		static $this_plugin;
		if( !$this_plugin ) $this_plugin = plugin_basename( __FILE__ );
		if( $file == $this_plugin ) {
			if( function_exists( 'wpsc_find_purchlog_status_name' ) )
				$settings_link = sprintf( '<a href="%s">' . __( 'Export', 'wpsc_ce' ) . '</a>', add_query_arg( array( 'post_type' => 'wpsc-product', 'page' => 'wpsc_ce' ), 'edit.php' ) );
			else
				$settings_link = sprintf( '<a href="%s">' . __( 'Export', 'wpsc_ce' ) . '</a>', add_query_arg( 'page', 'wpsc_ce', 'admin.php' ) );
			array_unshift( $links, $settings_link );
		}
		return $links;

	}
	add_filter( 'plugin_action_links', 'wpsc_ce_add_settings_link', 10, 2 );

	function wpsc_ce_enqueue_scripts( $hook ) {

		/* Export */
		$pages = array( 'wpsc-product_page_wpsc_ce', 'store_page_wpsc_ce' );
		if( in_array( $hook, $pages ) ) {
			/* Date Picker */
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/templates/admin/jquery-ui-datepicker.css', __FILE__ ) );

			/* Chosen */
			wp_enqueue_script( 'jquery-chosen', plugins_url( '/js/chosen.jquery.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style( 'jquery-chosen', plugins_url( '/templates/admin/chosen.css', __FILE__ ) );

			/* Common */
			wp_enqueue_style( 'wpsc_ce_styles', plugins_url( '/templates/admin/wpsc-admin_ce-export.css', __FILE__ ) );
			wp_enqueue_script( 'wpsc_ce_scripts', plugins_url( '/templates/admin/wpsc-admin_ce-export.js', __FILE__ ), array( 'jquery' ) );
		}

	}
	add_action( 'admin_enqueue_scripts', 'wpsc_ce_enqueue_scripts' );

	function wpsc_ce_store_admin_menu() {

		add_submenu_page( 'wpsc_sm', __( 'Store Export', 'wpsc_ce' ), __( 'Store Export', 'wpsc_ce' ), 'manage_options', 'wpsc_ce', 'wpsc_ce_html_page' );
		remove_filter( 'wpsc_additional_pages', 'wpsc_ce_add_modules_admin_pages', 10 );

	}
	add_action( 'wpsc_sm_store_admin_subpages', 'wpsc_ce_store_admin_menu' );

	function wpsc_ce_admin_init() {

		global $wpsc_ce, $export;

		include_once( 'includes/formatting.php' );

		$action = wpsc_get_action();
		switch( $action ) {

			case 'dismiss_memory_prompt':
				wpsc_ce_update_option( 'dismiss_memory_prompt', 1 );
				$url = add_query_arg( 'action', null );
				wp_redirect( $url );
				break;

			case 'export':
				$export = new stdClass();
				$export->delimiter = $_POST['delimiter'];
				if( $export->delimiter <> wpsc_ce_get_option( 'delimiter' ) )
					wpsc_ce_update_option( 'delimiter', $export->delimiter );
				$export->category_separator = $_POST['category_separator'];
				if( $export->category_separator <> wpsc_ce_get_option( 'category_separator' ) )
					wpsc_ce_update_option( 'category_separator', $export->category_separator );
				$export->bom = $_POST['bom'];
				if( $export->bom <> wpsc_ce_get_option( 'bom' ) )
					wpsc_ce_update_option( 'bom', $export->bom );
				$export->escape_formatting = $_POST['escape_formatting'];
				if( $export->escape_formatting <> wpsc_ce_get_option( 'escape_formatting' ) )
					wpsc_ce_update_option( 'escape_formatting', $export->escape_formatting );
				$export->limit_volume = -1;
				if( !empty( $_POST['limit_volume'] ) ) {
					$export->limit_volume = $_POST['limit_volume'];
					if( $export->limit_volume <> wpsc_ce_get_option( 'limit_volume' ) )
						wpsc_ce_update_option( 'limit_volume', $export->limit_volume );
				}
				$export->offset = 0;
				if( !empty( $_POST['offset'] ) ) {
					$export->offset = (int)$_POST['offset'];
					if( $export->offset <> wpsc_ce_get_option( 'offset' ) )
						wpsc_ce_update_option( 'offset', $export->offset );
				}
				$export->delete_temporary_csv = 0;
				if( !empty( $_POST['delete_temporary_csv'] ) ) {
					$export->delete_temporary_csv = (int)$_POST['delete_temporary_csv'];
					if( $export->limit_volume <> wpsc_ce_get_option( 'delete_csv' ) )
						wpsc_ce_update_option( 'delete_csv', $export->delete_temporary_csv );
				}
				$export->encoding = $_POST['encoding'];
				$export->order_dates_filter = false;
				$export->order_dates_from = '';
				$export->order_dates_to = '';
				$export->order_status = false;
				$export->fields = false;
				$export->product_categories = false;
				$export->product_tags = false;
				$export->product_status = false;
				$export->order_customer = false;

				$dataset = array();
				$export->type = $_POST['dataset'];
				switch( $export->type ) {

					case 'products':
						$dataset[] = 'products';
						if( isset( $_POST['product_fields'] ) )
							$export->fields = $_POST['product_fields'];
						if( isset( $_POST['product_filter_categories'] ) )
							$export->product_categories = wpsc_ce_format_product_filters( $_POST['product_filter_categories'] );
						if( isset( $_POST['product_filter_tags'] ) )
							$export->product_tags = wpsc_ce_format_product_filters( $_POST['product_filter_tags'] );
						if( isset( $_POST['product_filter_status'] ) )
							$export->product_status = wpsc_ce_format_product_filters( $_POST['product_filter_status'] );
						break;

					case 'categories':
						$dataset[] = 'categories';
						break;

					case 'tags':
						$dataset[] = 'tags';
						break;

					case 'orders':
						$dataset[] = 'orders';
						$export->fields = $_POST['order_fields'];
						if( isset( $_POST['order_filter_status'] ) )
							$export->order_status = wpsc_ce_format_product_filters( $_POST['order_filter_status'] );
						if( isset( $_POST['order_dates_filter'] ) )
							$export->order_dates_filter = $_POST['order_dates_filter'];
						$export->order_dates_from = $_POST['order_dates_from'];
						$export->order_dates_to = $_POST['order_dates_to'];
						if( isset( $_POST['order_customer'] ) )
							$export->order_customer = $_POST['order_customer'];
						break;

					case 'customers':
						$dataset[] = 'customers';
						$export->fields = $_POST['customer_fields'];
						break;

					case 'coupons':
						$dataset[] = 'coupons';
						$export->fields = $_POST['coupon_fields'];
						break;

				}
				if( $dataset ) {

					$timeout = 600;
					if( isset( $_POST['timeout'] ) ) {
						$timeout = $_POST['timeout'];
						if( $timeout <> wpsc_ce_get_option( 'timeout' ) )
							wpsc_ce_update_option( 'timeout', $timeout );
					}

					if( !ini_get( 'safe_mode' ) )
						set_time_limit( $timeout );

					@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

					$args = array(
						'limit_volume' => $export->limit_volume,
						'offset' => $export->offset,
						'encoding' => $export->encoding,
						'product_categories' => $export->product_categories,
						'product_tags' => $export->product_tags,
						'product_status' => $export->product_status,
						'order_status' => $export->order_status,
						'order_dates_filter' => $export->order_dates_filter,
						'order_dates_from' => wpsc_ce_format_order_date( $export->order_dates_from ),
						'order_dates_to' => wpsc_ce_format_order_date( $export->order_dates_to ),
						'order_customer' => $export->order_customer
					);
					wpsc_ce_save_fields( $dataset, $export->fields );
					$export->filename = wpsc_ce_generate_csv_filename( $export->type );
					if( isset( $wpsc_ce['debug'] ) && $wpsc_ce['debug'] ) {
						wpsc_ce_export_dataset( $dataset, $args );
					} else {

						/* Generate CSV contents */

						$bits = wpsc_ce_export_dataset( $dataset, $args );
						if( !$bits ) {
							wp_redirect( add_query_arg( 'empty', true ) );
							exit();
						}
						if( isset( $export->delete_temporary_csv ) && $export->delete_temporary_csv ) {

							/* Print to browser */

							wpsc_ce_generate_csv_header( $export->type );
							echo $bits;
							exit();

						} else {

							/* Save to file and insert to WordPress Media */

							if( $export->filename && $bits ) {
								$post_ID = wpsc_ce_save_csv_file_attachment( $export->filename );
								$upload = wp_upload_bits( $export->filename, null, $bits );
								$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
								wp_update_attachment_metadata( $post_ID, $attach_data );
								if( $post_ID )
									wpsc_ce_save_csv_file_guid( $post_ID, $export->type, $upload['url'] );
								wpsc_ce_generate_csv_header( $export->type );
								readfile( $upload['file'] );
							} else {
								wp_redirect( add_query_arg( 'failed', true ) );
							}
							exit();

						}
					}
				}
				break;

			default:
				add_action( 'wpsc_ce_export_order_options_before_table', 'wpsc_ce_orders_filter_by_date' );
				add_action( 'wpsc_ce_export_order_options_before_table', 'wpsc_ce_orders_filter_by_status' );
				add_action( 'wpsc_ce_export_order_options_before_table', 'wpsc_ce_orders_filter_by_customer' );
				break;

		}

	}
	add_action( 'admin_init', 'wpsc_ce_admin_init' );

	function wpsc_ce_html_page() {

		global $wpdb, $wpsc_ce;

		$title = apply_filters( 'wpsc_ce_template_header', '' );
		wpsc_ce_template_header( $title );
		wpsc_ce_support_donate();
		$action = wpsc_get_action();
		switch( $action ) {

			case 'export':
				$message = __( 'Chosen WP e-Commerce details have been exported from your store.', 'wpsc_ce' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '</strong></p></div>';
				if( isset( $wpsc_ce['debug'] ) && $wpsc_ce['debug'] ) {
					if( !isset( $wpsc_ce['debug_log'] ) )
						$wpsc_ce['debug_log'] = __( 'No export entries were found, please try again with different export filters.', 'wpsc_ce' );
					$output .= '<h3>' . sprintf( __( 'Export Log: %s', 'wpsc_ce' ), $export->filename ) . '</h3>';
					$output .= '<textarea id="export_log">' . $wpsc_ce['debug_log'] . '</textarea>';
				}
				echo $output;

				wpsc_ce_manage_form();
				break;

			default:
				wpsc_ce_manage_form();
				break;

		}
		wpsc_ce_template_footer();

	}

	function wpsc_ce_manage_form() {

		global $wpsc_ce;

		$tab = false;
		if( isset( $_GET['tab'] ) )
			$tab = $_GET['tab'];
		$url = add_query_arg( 'page', 'wpsc_ce' );
		wpsc_ce_memory_prompt();
		wpsc_ce_fail_notices();
		switch( wpsc_get_major_version() ) {

			case '3.8':
				$wpsc_ce_url = add_query_arg( array( 'post_type' => 'wpsc-product', 'page' => 'wpsc_ce' ), 'edit.php' );
				break;

			case '3.7':
				$wpsc_ce_url = add_query_arg( array( 'page' => 'wpsc_ce' ), 'admin.php' );
				break;

		}

		include_once( 'templates/admin/wpsc-admin_ce-export.php' );

	}

	/* End of: WordPress Administration */

}
?>