<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			/* WP e-Commerce */

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
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'categories':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_categories`";
				break;

			case 'orders':
			case 'coupons':
				if( function_exists( 'wpsc_cd_return_count' ) )
					$count = wpsc_cd_return_count( $dataset );
				break;

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
				if( is_object( $count ) ) {
					$count_object = $count;
					$count = 0;
					foreach( $count_object as $key => $item )
						$count = $item + $count;
				}
				return $count;
			} else {
				$count = $wpdb->get_var( $count_sql );
			}
			return $count;
		} else {
			return 0;
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
						unset( $products, $product );
					}
					break;

				/* Orders */
				/* Coupons */
				/* Customers */
				case 'orders':
				case 'coupons':
				case 'customers':
					$csv = do_action( 'wpsc_ce_export_dataset', $datatype );
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