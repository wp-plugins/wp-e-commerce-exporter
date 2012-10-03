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
				$count_sql = "SELECT COUNT(`term_taxonomy_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE taxonomy = 'product_tag'";
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

		global $wpdb, $export;

		$csv = '';
		$separator = $export->delimiter;

		foreach( $dataset as $datatype ) {

			$csv = null;

			switch( $datatype ) {

				case 'categories':
					break;

				case 'products':
					$columns = array(
						'SKU',
						'Product Name',
						'Description',
						'Additional Description',
						'Price',
						'Sale Price',
						'Permalink',
						'Weight',
						'Weight Unit',
						'Height',
						'Height Unit',
						'Width',
						'Width Unit',
						'Length',
						'Length Unit',
						'Category',
						'Tag',
						'Image',
						'Quantity',
						'File Download',
						'External Link',
						'Merchant Notes',
						'Local Shipping Fee',
						'International Shipping Fee',
						'Product Status'
					);
					for( $i = 0; $i < count( $columns ); $i++ ) {
						if( $i == ( count( $columns ) - 1 ) )
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
					break;

			}

			if( WP_DEBUG )
				echo '<code>' . str_replace( "\n", '<br />', $csv ) . '</code>' . '<br />';
			else
				echo $csv;

		}

	}

	/* End of: WordPress Administration */

}
?>