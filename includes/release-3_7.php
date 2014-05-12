<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// Returns number of an Export type prior to export, used on Store Exporter screen
	function wpsc_ce_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			case 'products':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_list`";
				break;

			case 'variations':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_variations`";
				break;

			case 'images':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_images`";
				break;

			case 'files':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_files`";
				break;

			case 'categories':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_categories`";
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'orders':
			case 'coupons':
			case 'customers':
				if( function_exists( 'wpsc_cd_return_count' ) )
					$count = wpsc_cd_return_count( $dataset );
				break;

			// 3rd Party

			case 'wishlist':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_wishlist`";
				break;

			case 'enquiries':
				$post_type = 'wpsc-enquiry';
				$count = wp_count_posts( $post_type );
				break;

			case 'credit-cards':
				$post_type = 'offline_payment';
				$count = wp_count_posts( $post_type );
				break;

			case 'related-products':
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				$count = wpsc_ce_count_object( $count );
				return $count;
			} else {
				if( $count_sql )
					$count = $wpdb->get_var( $count_sql );
				else
					$count = 0;
			}
			return $count;
		} else {
			return 0;
		}

	}

	// Returns a list of Category export columns
	function wpsc_ce_get_category_fields( $format = 'full' ) {
	
		$fields = array();
		$fields[] = array(
			'name' => 'name',
			'label' => __( 'Category', 'wpsc_ce' ),
			'default' => 1
		);
		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;
	
			case 'full':
			default:
				return $fields;
				break;

		}

	}

	if( !function_exists( 'wpsc_find_purchlog_status_name' ) ) {
		function wpsc_find_purchlog_status_name( $status ) {

			global $wpdb;

			$output = $status;
			if( !empty( $status ) ) {
				$status_name_sql = $wpdb->prepare( "SELECT `name` FROM `" . $wpdb->prefix . "wpsc_purchase_statuses` WHERE `id` = %d LIMIT 1", $status );
				$status_name = $wpdb->get_var( $status_name_sql );
				if( $status_name )
					$output = $status_name;
			}
			return $output;

		}
	}

	/* End of: WordPress Administration */

}

/* Start of: Common */

// Export process for CSV file
function wpsc_ce_export_dataset( $dataset, $args = array() ) {

	global $wpdb, $export;

	$csv = '';
	if( $export->bom )
		// $csv .= chr(239) . chr(187) . chr(191) . '';
		$csv .= "\xEF\xBB\xBF";
	$separator = $export->delimiter;
	$export->args = $args;
	$export->columns = array();
	set_transient( WPSC_CE_PREFIX . '_running', time(), wpsc_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );

	$csv = '';
	switch( $dataset ) {

		// Products
		case 'products':
			$export->columns = array(
				__( 'SKU', 'wpsc_ce' ),
				__( 'Product Name', 'wpsc_ce' ),
				__( 'Description', 'wpsc_ce' ),
				__( 'Additional Description', 'wpsc_ce' ),
				__( 'Price', 'wpsc_ce' ),
				__( 'Sale Price', 'wpsc_ce' ),
				__( 'Slug', 'wpsc_ce' ),
				__( 'Permalink', 'wpsc_ce' ),
				__( 'Weight', 'wpsc_ce' ),
				__( 'Weight Unit', 'wpsc_ce' ),
				__( 'Height', 'wpsc_ce' ),
				__( 'Height Unit', 'wpsc_ce' ),
				__( 'Width', 'wpsc_ce' ),
				__( 'Width Unit', 'wpsc_ce' ),
				__( 'Length', 'wpsc_ce' ),
				__( 'Length Unit', 'wpsc_ce' ),
				__( 'Category', 'wpsc_ce' ),
				__( 'Tag', 'wpsc_ce' ),
				__( 'Image', 'wpsc_ce' ),
				__( 'Quantity', 'wpsc_ce' ),
				__( 'File Download', 'wpsc_ce' ),
				__( 'External Link', 'wpsc_ce' ),
				__( 'Merchant Notes', 'wpsc_ce' ),
				__( 'Local Shipping Fee', 'wpsc_ce' ),
				__( 'International Shipping Fee', 'wpsc_ce' ),
				__( 'Product Status', 'wpsc_ce' )
			);
			$export->data_memory_start = wpsc_ce_current_memory_usage();
			if( $products = wpsc_ce_get_products( $export->args ) ) {
				$export->total_rows = count( $products );
				$size = count( $export->columns );
				$export->total_columns = $size;
				for( $i = 0; $i < $size; $i++ ) {
					if( $i == ( $size - 1 ) )
						$csv .= wpsc_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
					else
						$csv .= wpsc_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
				}
				unset( $export->columns );
				foreach( $products as $product ) {

					$product->sku = get_product_meta( $product->ID, 'sku' );
					$product->slug = get_product_meta( $product->ID, 'url_name' );
					$product->permalink = wpsc_product_url( $product->ID );
					$product->dimensions = get_product_meta( $product->ID, 'dimensions' );
					if( $product->dimensions ) {
						$product->height = $product->dimensions['height'];
						$product->height_unit = $product->dimensions['height_unit'];
						$product->width = $product->dimensions['width'];
						$product->width_unit = $product->dimensions['width_unit'];
						$product->length = $product->dimensions['length'];
						$product->length_unit = $product->dimensions['length_unit'];
					}
					$product->external_link = get_product_meta( $product->ID, 'external_link' );
					$product->merchant_notes = get_product_meta( $product->ID, 'merchant_notes' );
					$product->category = wpsc_ce_get_product_assoc_categories( $product->ID );
					$product->tags = wpsc_ce_get_product_assoc_tags( $product->ID );

					foreach( $product as $key => $value )
						$product->$key = wpsc_ce_escape_csv_value( $value );

					$csv .= 
						$product->sku . $separator . 
						$product->name . $separator . 
						$product->description . $separator . 
						$product->additional_description . $separator . 
						$product->price . $separator . 
						$product->sale_price . $separator . 
						$product->permalink . $separator . 
						$product->weight . $separator . 
						$product->weight_unit . $separator . 
						$product->height . $separator . 
						$product->height_unit . $separator . 
						$product->width . $separator . 
						$product->width_unit . $separator . 
						$product->length . $separator . 
						$product->length_unit . $separator . 
						$product->category . $separator . 
						$product->tag . $separator . 
						$product->image . $separator . 
						$product->quantity . $separator . 
						$product->file_download . $separator . 
						$product->external_link . $separator . 
						$product->merchant_notes . $separator . 
						$product->local_shipping . $separator . 
						$product->international_shipping . $separator . 
						$product->status . 
					"\n";

				}
				unset( $products, $product );
			}
			$export->data_memory_end = wpsc_ce_current_memory_usage();
			break;

		// Categories
		case 'categories':
			$export->data_memory_start = wpsc_ce_current_memory_usage();
			$export->columns = array(
				__( 'Category', 'wpsc_ce' )
			);
			if( $categories = wpsc_ce_get_product_categories() ) {
				$export->total_rows = count( $categories );
				$size = count( $export->columns );
				$export->total_columns = $size;
				for( $i = 0; $i < $size; $i++ ) {
					if( $i == ( $size - 1 ) )
						$csv .= wpsc_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
					else
						$csv .= wpsc_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
				}
				unset( $export->columns );
				foreach( $categories as $category ) {
					$csv .= wpsc_ce_escape_csv_value( $category->name, $export->delimiter, $export->escape_formatting ) . "\n";
				}
				unset( $categories, $category );
			}
			$export->data_memory_end = wpsc_ce_current_memory_usage();
			break;

		// Tags
		case 'tags':
			$export->data_memory_start = wpsc_ce_current_memory_usage();
			$export->columns = array(
				__( 'Tags', 'wpsc_ce' )
			);
			$tag_args = array(
				'orderby' => ( isset( $args['tag_orderby'] ) ? $args['tag_orderby'] : 'ID' ),
				'order' => ( isset( $args['tag_order'] ) ? $args['tag_order'] : 'ASC' ),
			);
			if( $tags = wpsc_ce_get_product_tags( $tag_args ) ) {
				$export->total_rows = count( $tags );
				$size = count( $export->columns );
				$export->total_columns = $size;
				for( $i = 0; $i < $size; $i++ ) {
					if( $i == ( $size - 1 ) )
						$csv .= wpsc_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
					else
						$csv .= wpsc_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
				}
				unset( $export->columns );
				foreach( $tags as $tag ) {
					$csv .= wpsc_ce_escape_csv_value( $tag->name, $export->delimiter, $export->escape_formatting ) . "\n";
				}
				unset( $tags, $tag );
			}
			$export->data_memory_end = wpsc_ce_current_memory_usage();
			break;

		// Orders
		case 'orders':
		// Customers
		case 'customers':
		// Coupons
		case 'coupons':
			$csv = apply_filters( 'wpsc_ce_export_dataset', $export->type, $export );
			break;

	}
	// Export completed successfully
	delete_transient( WPSC_CE_PREFIX . '_running' );
	if( $csv ) {
		$csv = wpsc_ce_file_encoding( $csv );
		if( WPSC_CE_DEBUG )
			set_transient( WPSC_CE_PREFIX . '_debug_log', base64_encode( $csv ), wpsc_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
		else
			return $csv;
	}

}

// Returns a list of WP e-Commerce Products to export process
function wpsc_ce_get_products( $args = array() ) {

	global $wpdb;

	$products_sql = "SELECT `id` AS ID, `name`, `description`, `additional_description`, `publish` as status, `price`, `weight`, `weight_unit`, `pnp` as local_shipping, `international_pnp` as international_shipping, `quantity`, `special_price` as sale_price FROM `" . $wpdb->prefix . "wpsc_product_list` WHERE `active` = 1";
	$products = $wpdb->get_results( $products_sql );
	return $products;

}

// Returns Product Categories associated to a specific Product
function wpsc_ce_get_product_assoc_categories( $product_id = 0 ) {

	global $export, $wpdb;

	$output = '';
	if( $product_id ) {
		$categories_sql = $wpdb->prepare( "SELECT wpsc_product_categories.`name` FROM `" . $wpdb->prefix . "wpsc_item_category_assoc` as item_category_assoc, `" . $wpdb->prefix . "wpsc_product_categories` as wpsc_product_categories WHERE item_category_assoc.category_id = wpsc_product_categories.id AND item_category_assoc.`product_id` = %d", $product_id );
		$categories = $wpdb->get_results( $categories_sql );
		if( $categories ) {
			foreach( $categories as $category ) {
				$output .= $category->name . $export->category_separator;
			}
			$output = substr( $output, 0, -1 );
		} else {
			$output .= __( 'Uncategorized', 'wpsc_ce' );
		}
	}
	return $output;

}

// Returns a list of WP e-Commerce Product Categories to export process
function wpsc_ce_get_product_categories( $args = array() ) {

	global $wpdb;

	$output = '';
	$categories_sql = "SELECT `name` FROM `" . $wpdb->prefix . "wpsc_product_categories` WHERE `active` = 1";
	$categories = $wpdb->get_results( $categories_sql );
	if( $categories )
		$output = $categories;
	return $output;

}

/* End of: Common */
?>