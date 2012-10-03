<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Administration Menu */
	function wpsc_ce_add_modules_admin_pages( $page_hooks, $base_page ) {

		$page_hooks[] = add_submenu_page( $base_page,__( 'WP e-Commerce Exporter', 'wpsc_ce' ), __( 'Store Export', 'wpsc_ce' ), 'manage_options', 'wpsc_ce', 'wpsc_ce_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_ce_add_modules_admin_pages', 10, 2 );

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

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count_sql = "SELECT COUNT(`term_taxonomy_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'categories':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_categories`";
				break;

			case 'orders':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_purchase_logs`";
				break;

			case 'wishlist':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_wishlist`";
				break;

			case 'enquiries':
				$count_sql = "SELECT COUNT(`ID`) FROM `" . $wpdb->posts . "` WHERE `post_type` = 'wpsc-enquiry'";
				break;

			case 'credit-card':
				break;

			case 'related-products':
				break;

		}
		if( $count_sql ) {
			$count = $wpdb->get_var( $count_sql );
			return $count;
		} else {
			return false;
		}

	}

	function wpsc_ce_export_dataset( $dataset ) {

		global $wpdb, $wpsc_ce, $export;

		$csv = '';
		$separator = $export->delimiter;

		foreach( $dataset as $datatype ) {

			$csv = null;

			switch( $datatype ) {

				case 'categories':
					break;

				case 'products':
					$columns = array(
						__( 'SKU', 'wpsc_ce' ),
						__( 'Product Name', 'wpsc_ce' ),
						__( 'Description', 'wpsc_ce' ),
						__( 'Additional Description', 'wpsc_ce' ),
						__( 'Price', 'wpsc_ce' ),
						__( 'Sale Price', 'wpsc_ce' ),
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
					$size = count( $columns );
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$csv .= '"' . $columns[$i] . "\"\n";
						else
							$csv .= '"' . $columns[$i] . '"' . $separator;
					}
					$products_sql = "SELECT `id` AS ID, `name`, `description`, `additional_description`, `publish` as status, `price`, `weight`, `weight_unit`, `pnp` as local_shipping, `international_pnp` as international_shipping, `quantity`, `special_price` as sale_price FROM `" . $wpdb->prefix . "wpsc_product_list` WHERE `active` = 1";
					$products = $wpdb->get_results( $products_sql );
					if( $products ) {
						foreach( $products as $product ) {

							$product->sku = get_product_meta( $product->ID, 'sku' );
							$product->permalink = get_product_meta( $product->ID, 'url_name' );
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

							foreach( $product as $key => $value )
								$product->$key = wpsc_ce_has_value( $value );

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
					}
					unset( $products, $product );
					break;

			}

			if( isset( $wpsc_ce['debug'] ) && $wpsc_ce['debug'] )
				echo '<code>' . str_replace( "\n", '<br />', $csv ) . '</code>' . '<br />';
			else
				echo $csv;

		}

	}

	/* End of: WordPress Administration */

}
?>