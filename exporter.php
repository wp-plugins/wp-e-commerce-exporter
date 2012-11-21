<?php
/*
Plugin Name: WP e-Commerce - Store Exporter
Plugin URI: http://www.visser.com.au/wp-ecommerce/plugins/exporter/
Description: Export store details out of WP e-Commerce into a CSV-formatted file.
Version: 1.3.8
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
License: GPL2
*/

load_plugin_textdomain( 'wpsc_ce', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

include_once( 'includes/functions.php' );

include_once( 'includes/common.php' );

switch( wpsc_get_major_version() ) {

	case '3.7':
		include_once( 'includes/release-3_7.php' );
		break;

	case '3.8':
		include_once( 'includes/release-3_8.php' );
		break;

}

$wpsc_ce = array(
	'filename' => basename( __FILE__ ),
	'dirname' => basename( dirname( __FILE__ ) ),
	'abspath' => dirname( __FILE__ ),
	'relpath' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ )
);

$wpsc_ce['prefix'] = 'wpsc_ce';
$wpsc_ce['name'] = __( 'WP e-Commerce Exporter', 'wpsc_ce' );
$wpsc_ce['menu'] = __( 'Store Export', 'wpsc_ce' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_add_settings_link( $links, $file ) {

		static $this_plugin;
		if( !$this_plugin ) $this_plugin = plugin_basename( __FILE__ );
		if( $file == $this_plugin ) {
			$settings_link = '<a href="' . add_query_arg( array( 'post_type' => 'wpsc-product', 'page' => 'wpsc_ce' ), 'edit.php' ) . '">' . __( 'Export', 'wpsc_ce' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;

	}
	add_filter( 'plugin_action_links', 'wpsc_ce_add_settings_link', 10, 2 );

	function wpsc_ce_admin_init() {

		global $wpsc_ce, $export;

		include_once( 'includes/formatting.php' );

		wp_enqueue_script( 'wpsc_ce_scripts', plugins_url( '/templates/admin/wpsc-admin_ce-export.js', __FILE__ ), array( 'jquery' ) );

		$action = wpsc_get_action();
		switch( $action ) {

			case 'export':
				$export = new stdClass();
				$export->delimiter = $_POST['delimiter'];
				$export->category_separator = $_POST['category_separator'];
				$dataset = array();
				if( $_POST['dataset'] == 'products' ) {
					$dataset[] = 'products';
					$export->fields = $_POST['product_fields'];
				}
				if( $_POST['dataset'] == 'categories' )
					$dataset[] = 'categories';
				if( $_POST['dataset'] == 'tags' )
					$dataset[] = 'tags';
				if( $_POST['dataset'] == 'sales' ) {
					$dataset[] = 'orders';
					$export->fields = $_POST['sale_fields'];
				}
				if( $_POST['dataset'] == 'coupons' )
					$dataset[] = 'coupons';
				if( $dataset ) {

					if( isset( $_POST['timeout'] ) )
						$timeout = $_POST['timeout'];
					else
						$timeout = 600;

					if( !ini_get( 'safe_mode' ) )
						set_time_limit( $timeout );

					if( isset( $wpsc_ce['debug'] ) && $wpsc_ce['debug'] ) {
						wpsc_ce_export_dataset( $dataset );
					} else {
						wpsc_ce_generate_csv_header( $_POST['dataset'] );
						wpsc_ce_export_dataset( $dataset );

						exit();
					}
				}
				break;

		}
		wp_enqueue_style( 'wpsc_ce_styles', plugins_url( '/templates/admin/wpsc-admin_ce-export.css', __FILE__ ) );

	}
	add_action( 'admin_init', 'wpsc_ce_admin_init' );

	function wpsc_ce_store_admin_menu() {

		add_submenu_page( 'wpsc_sm', __( 'Store Export', 'wpsc_ce' ), __( 'Store Export', 'wpsc_ce' ), 'manage_options', 'wpsc_ce', 'wpsc_ce_html_page' );
		remove_filter( 'wpsc_additional_pages', 'wpsc_ce_add_modules_admin_pages', 10 );

	}
	add_action( 'wpsc_sm_store_admin_subpages', 'wpsc_ce_store_admin_menu' );

	function wpsc_ce_html_page() {

		global $wpdb, $wpsc_ce;

		wpsc_ce_template_header();
		wpsc_ce_support_donate();
		$action = wpsc_get_action();
		switch( $action ) {

			case 'export':
				$message = __( 'Chosen WP e-Commerce details have been exported from your store.', 'wpsc_ce' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '</strong></p></div>';
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

		include_once( 'templates/admin/wpsc-admin_ce-export.php' );

	}

	/* End of: WordPress Administration */

}
?>