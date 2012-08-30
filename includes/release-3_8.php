<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Administration Menu */
	function wpsc_ce_add_modules_admin_pages( $page_hooks, $base_page ) {

		$page_hooks[] = add_submenu_page( $base_page, __( 'Store Export', 'wpsc_ce' ), __( 'Store Export', 'wpsc_ce' ), 7, 'wpsc_ce', 'wpsc_ce_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_ce_add_modules_admin_pages', 10, 2 );

	function wpsc_ce_admin_page_item( $menu = array() ) {

		global $wpsc_ce;

		$title = $wpsc_ce['menu'];
		$link = 'admin.php?page=wpsc_ce';
		$description = __( 'Export store details out of WP e-Commerce into a CSV-formatted file.', 'wpsc_ce' );

		$menu[] = array( 'title' => $title, 'link' => $link, 'description' => $description );

		return $menu;

	}
	add_filter( 'wpsc_sm_store_admin_page', 'wpsc_ce_admin_page_item', 1 );

	function wpsc_ce_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			case 'products':
				$post_type = 'wpsc-product';
				$count = wp_count_posts( $post_type );
				break;

			case 'variations':
				$term_taxonomy = 'wpsc-variation';
				$count_sql = "SELECT COUNT(`term_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'images':
				$post_type = 'attachment';
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->posts . "` WHERE `post_type` = '" . $post_type . "' AND `post_mime_type` LIKE 'image/%'";
				break;

			case 'files':
				$post_type = 'wpsc-product-file';
				$count = wp_count_posts( $post_type );
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count_sql = "SELECT COUNT(`term_taxonomy_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'categories':
				$term_taxonomy = 'wpsc_product_category';
				$count_sql = "SELECT COUNT(terms.`term_id`) FROM `" . $wpdb->terms . "` as terms, `" . $wpdb->term_taxonomy . "` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'coupons':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_coupon_codes`";
				break;

			case 'orders':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_purchase_logs`";
				break;

			case 'wishlist':
				$post_type = 'wpsc-wishlist';
				$count = wp_count_posts( $post_type );
				break;

			case 'enquiries':
				$post_type = 'wpsc-enquiry';
				$count = wp_count_posts( $post_type );
				break;

			case 'credit-card':
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
			} else {
				$count = $wpdb->get_var( $count_sql );
			}
			return $count;
		} else {
			return false;
		}

	}

	function wpsc_ce_export_dataset( $dataset ) {

		global $wpdb, $wpsc_ce, $export;

		$csv = '';

		foreach( $dataset as $datatype ) {

			$csv = null;

			switch( $datatype ) {

				case 'coupons':
					$coupons_sql = "SELECT `coupon_code`, `value` as coupon_value, `is-percentage` as is_percenage, `use-once` as use_once, `active`, `every_product`, `start`, `expiry` FROM `" . $wpdb->prefix . "wpsc_coupon_codes`";
					$coupons = $wpdb->get_results( $coupons_sql );
					if( $coupons ) {
						$columns = array(
							__( 'Coupon Code', 'wpsc_ce' ),
							__( 'Coupon Value', 'wpsc_ce' ),
							__( 'Use Once', 'wpsc_ce' ),
							__( 'Is Used', 'wpsc_ce' ),
							__( 'Active', 'wpsc_ce' ),
							__( 'Apply to All Products', 'wpsc_ce' ),
							__( 'Valid From', 'wpsc_ce' ),
							__( 'Valid To', 'wpsc_ce' )
						);
						for( $i = 0; $i < count( $columns ); $i++ ) {
							if( $i == ( count( $columns ) - 1 ) )
								$csv .= $columns[$i] . "\n";
							else
								$csv .= $columns[$i] . $export->delimiter;
						}
						foreach( $coupons as $coupon ) {
							switch( $coupon->is_percentage ) {

								case '0':
									/* Dollar-bsaed value */
									$coupon->coupon_value = '$' . $coupon->coupon_value;
									break;

								case '1':
									/* Percentage-based value */
									$coupon->coupon_value = (int)$coupon->coupon_value . '%';
									break;

								case '2':
									$coupon->coupon_value = __( 'Free Shipping', 'wpsc_ce' );
									/* Free Shipping */
									break;

							}
							$csv .= 
								$coupon->coupon_code . $export->delimiter . 
								$coupon->coupon_value . $export->delimiter . 
								$coupon->use_once . $export->delimiter . 
								$coupon->active . $export->delimiter . 
								$coupon->every_product . $export->delimiter . 
								$coupon->start . $export->delimiter . 
								$coupon->expiry . 
							"\n";
						}
					}
					break;

				case 'categories':
					$categories_sql = "SELECT terms.`name` as name FROM `" . $wpdb->term_taxonomy . "` as term_taxonomy, `" . $wpdb->terms . "` as terms WHERE term_taxonomy.term_id = terms.term_id AND term_taxonomy.`taxonomy` = 'wpsc_product_category' ORDER BY terms.`name` ASC";
					$categories = $wpdb->get_results( $categories_sql );
					if( $categories ) {
						$columns = array(
							__( 'Category', 'wpsc_ce' )
						);
						for( $i = 0; $i < count( $columns ); $i++ ) {
							if( $i == ( count( $columns ) - 1 ) )
								$csv .= $columns[$i] . "\n";
							else
								$csv .= $columns[$i] . $export->delimiter;
						}
						foreach( $categories as $category ) {
							$csv .= 
								$category->name
								 . 
							"\n";
						}
					}
					break;

				case 'tags':
					$tags_sql = "SELECT terms.`name` as name FROM `" . $wpdb->term_taxonomy . "` as term_taxonomy, `" . $wpdb->terms . "` as terms WHERE term_taxonomy.term_id = terms.term_id AND term_taxonomy.`taxonomy` = 'product_tag' ORDER BY terms.`name` ASC";
					$tags = $wpdb->get_results( $tags_sql );
					if( $tags ) {
						$columns = array(
							__( 'Tags', 'wpsc_ce' )
						);
						for( $i = 0; $i < count( $columns ); $i++ ) {
							if( $i == ( count( $columns ) - 1 ) )
								$csv .= $columns[$i] . "\n";
							else
								$csv .= $columns[$i] . $export->delimiter;
						}
						foreach( $tags as $tag ) {
							$csv .= 
								$tag->name
								 . 
							"\n";
						}
					}
					break;

				case 'products':
					$export->columns = array(
						__( 'SKU', 'wpsc_ce' ),
						__( 'Product Name', 'wpsc_ce' ),
						__( 'Permalink', 'wpsc_ce' ),
						__( 'Description', 'wpsc_ce' ),
						__( 'Additional Description', 'wpsc_ce' ),
						__( 'Price', 'wpsc_ce' ),
						__( 'Sale Price', 'wpsc_ce' ),
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
						__( 'Local Shipping Fee', 'wpsc_ce' ),
						__( 'International Shipping Fee', 'wpsc_ce' ),
						__( 'Product Status', 'wpsc_ce' ),
						__( 'Comment Status', 'wpsc_ce' )
					);

					/* Allow Plugin/Theme authors to add support for additional Product details */
					$export->columns = apply_filters( 'wpsc_ce_options_addons', $export->columns );

					/* Advanced Google Product Feed */
					if( function_exists( 'wpec_gpf_install' ) ) {
						$export->columns[] = __( 'Advanced Google Product Feed - Availability', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Condition', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Brand', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Product Type', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Google Product Category', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Global Trade Item Number (GTIN)', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Manufacturer Part Number (MPN)', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Gender', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Age Group', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Colour', 'wpsc_ce' );
						$export->columns[] = __( 'Advanced Google Product Feed - Size', 'wpsc_ce' );
					}
					/* All in One SEO Pack */
					if( function_exists( 'aioseop_activate' ) ) {
						$export->columns[] = __( 'All in One SEO - Keywords', 'wpsc_ce' );
						$export->columns[] = __( 'All in One SEO - Description', 'wpsc_ce' );
						$export->columns[] = __( 'All in One SEO - Title', 'wpsc_ce' );
						$export->columns[] = __( 'All in One SEO - Title Attributes', 'wpsc_ce' );
						$export->columns[] = __( 'All in One SEO - Menu Label', 'wpsc_ce' );
					}
					/* Custom Fields */
					if( function_exists( 'wpsc_cf_install' ) ) {
						$attributes = maybe_unserialize( get_option( 'wpsc_cf_data' ) );
						if( isset( $attributes ) && $attributes ) {
							foreach( $attributes as $attribute )
								$export->columns[] = sprintf( __( 'Attribute - %s', 'wpsc_ce' ), $attribute['name'] );
						}
					}

					$size = count( $export->columns );
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$csv .= '"' . $export->columns[$i] . "\"\n";
						else
							$csv .= '"' . $export->columns[$i] . '"' . $export->delimiter;
					}
					$post_type = 'wpsc-product';
					$products_args = array(
						'post_type' => $post_type,
						'numberposts' => -1
					);
					$products = get_posts( $products_args );
					if( $products ) {
						foreach( $products as $product ) {

							$product_data = get_post_meta( $product->ID, '_wpsc_product_metadata', true );

							$product->sku = get_product_meta( $product->ID, 'sku', true );
							$product->name = $product->post_title;
							$product->permalink = $product->post_name;
							$product->description = wpsc_ce_clean_html( $product->post_content );
							$product->additional_description = wpsc_ce_clean_html( $product->post_excerpt );
							if( get_product_meta( $product->ID, 'price', true ) )
								$product->price = get_product_meta( $product->ID, 'price', true );
							else
								$product->price = 0;
							if( get_product_meta( $product->ID, 'special_price', true ) )
								$product->sale_price = get_product_meta( $product->ID, 'special_price', true );
							else
								$product->sale_price = '0.00';
							$product->weight = wpsc_ce_convert_product_raw_weight( $product_data['weight'], $product_data['weight_unit'] );
							if( !$product->weight )
								$product->weight = 0;
							if( isset( $product_data['weight_unit'] ) )
								$product->weight_unit = $product_data['weight_unit'];
							if( !$product->weight_unit )
								$product->weight_unit = 'kg';
							if( isset( $product_data['dimensions']['height'] ) )
								$product->height = trim( $product_data['dimensions']['height'] );
							if( !$product->height )
								$product->height = 0;
							if( isset( $product_data['dimensions']['height_unit'] ) )
								$product->height_unit = $product_data['dimensions']['height_unit'];
							if( !$product->height_unit )
								$product->height_unit = 'in';
							if( isset( $product_data['dimensions']['width'] ) )
								$product->width = trim( $product_data['dimensions']['width'] );
							if( !$product->width )
								$product->width = 0;
							if( isset( $product_data['dimensions']['width_unit'] ) )
								$product->width_unit = $product_data['dimensions']['width_unit'];
							if( !$product->width_unit )
								$product->width_unit = 'in';
							if( isset( $product_data['dimensions']['length'] ) )
								$product->length = trim( $product_data['dimensions']['length'] );
							if( !$product->length )
								$product->length = 0;
							if( isset( $product_data['dimensions']['length_unit'] ) )
								$product->length_unit = $product_data['dimensions']['length_unit'];
							if( !$product->length_unit )
								$product->length_unit = 'in';
							$product->category = wpsc_ce_get_product_categories( $product->ID );
							$product->tag = wpsc_ce_get_product_tags( $product->ID );
							$product->image = wpsc_ce_get_product_images( $product->ID );
							$product->quantity = get_product_meta( $product->ID, 'stock', true );
							if( !$product->quantity )
								$product->quantity = 0;
							$product->external_link = $product_data['external_link'];
							if( isset( $product_data['shipping']['local'] ) )
								$product->local_shipping = $product_data['shipping']['local'];
							if( isset( $product_data['shipping']['international'] ) )
								$product->international_shipping = $product_data['shipping']['international'];
							$product->product_status = wpsc_ce_format_product_status( $product->post_status );
							$product->comment_status = wpsc_ce_format_comment_status( $product->comment_status );
							/* Advanced Google Product Feed */
							if( function_exists( 'wpec_gpf_install' ) ) {
								$product->gpf_data = get_post_meta( $product->ID, '_wpec_gpf_data', true );
								$product->gpf_availability = wpsc_ce_format_gpf_availability( $product->gpf_data['availability'] );
								$product->gpf_condition = wpsc_ce_format_gpf_condition( $product->gpf_data['condition'] );
								$product->gpf_brand = $product->gpf_data['brand'];
								$product->gpf_product_type = $product->gpf_data['product_type'];
								$product->gpf_google_product_category = $product->gpf_data['google_product_category'];
								$product->gpf_gtin = $product->gpf_data['gtin'];
								$product->gpf_mpn = $product->gpf_data['mpn'];
								$product->gpf_gender = $product->gpf_data['gender'];
								$product->gpf_age_group = $product->gpf_data['age_group'];
								$product->gpf_color = $product->gpf_data['color'];
								$product->gpf_size = $product->gpf_data['size'];
							}
							/* All in One SEO Pack */
							if( function_exists( 'aioseop_activate' ) ) {
								$product->aioseop_keywords = get_post_meta( $product->ID, '_aioseop_keywords', true );
								$product->aioseop_description = get_post_meta( $product->ID, '_aioseop_description', true );
								$product->aioseop_title = get_post_meta( $product->ID, '_aioseop_title', true );
								$product->aioseop_titleatr = get_post_meta( $product->ID, '_aioseop_titleatr', true );
								$product->aioseop_menulabel = get_post_meta( $product->ID, '_aioseop_menulabel', true );
							}
							/* Custom Fields */
							if( $custom_fields ) {
								$product->custom_fields = array();
								foreach( $custom_fields as $custom_field )
									$product->custom_fields[$custom_field['slug']] = get_product_meta( $product->ID, $custom_field['slug'], true );
							}

							foreach( $product as $key => $value ) {
								if( is_array( $value ) ) {
									foreach( $value as $array_key => $array_value )
										$value[$array_key] = escape_csv_value( $array_value );
									$product->$key = $value;
								} else {
									$product->$key = escape_csv_value( $value );
								}
							}

							$csv .= 
								$product->sku . $export->delimiter . 
								$product->name . $export->delimiter . 
								$product->permalink . $export->delimiter . 
								$product->description . $export->delimiter . 
								$product->additional_description . $export->delimiter . 
								$product->price . $export->delimiter . 
								$product->sale_price . $export->delimiter . 
								$product->weight . $export->delimiter . 
								$product->weight_unit . $export->delimiter . 
								$product->height . $export->delimiter . 
								$product->height_unit . $export->delimiter . 
								$product->width . $export->delimiter . 
								$product->width_unit . $export->delimiter . 
								$product->length . $export->delimiter . 
								$product->length_unit . $export->delimiter . 
								$product->category . $export->delimiter . 
								$product->tag . $export->delimiter . 
								$product->image . $export->delimiter . 
								$product->quantity . $export->delimiter . 
								$product->file_download . $export->delimiter . 
								$product->external_link . $export->delimiter . 
								$product->local_shipping . $export->delimiter . 
								$product->international_shipping . $export->delimiter . 
								$product->product_status . $export->delimiter . 
								$product->comment_status;
							if( function_exists( 'wpec_gpf_install' ) ) {
								$csv .= 
									$export->delimiter . 
									$product->gpf_availability . $export->delimiter . 
									$product->gpf_condition . $export->delimiter . 
									$product->gpf_brand . $export->delimiter . 
									$product->gpf_product_type . $export->delimiter . 
									$product->gpf_google_product_category . $export->delimiter . 
									$product->gpf_gtin . $export->delimiter . 
									$product->gpf_mpn . $export->delimiter . 
									$product->gpf_gender . $export->delimiter . 
									$product->gpf_age_group . $export->delimiter . 
									$product->gpf_color . $export->delimiter . 
									$product->gpf_size;
							}
							if( function_exists( 'aioseop_activate' ) ) {
								$csv .= 
									$export->delimiter . 
									$product->aioseop_keywords . $export->delimiter . 
									$product->aioseop_description . $export->delimiter . 
									$product->aioseop_title . $export->delimiter . 
									$product->aioseop_titleatr . $export->delimiter . 
									$product->aioseop_menulabel;
							}
							if( $custom_fields ) {
								$csv .= 
									$export->delimiter;
								foreach( $custom_fields as $custom_field )
									$csv .= $product->custom_fields[$custom_field['slug']] . $export->delimiter;
							}
							$csv .= "\n";

						}
					}
					break;

			}

			if( isset( $wpsc_ce['debug'] ) && $wpsc_ce['debug'] )
				echo '<code>' . str_replace( "\n", '<br />', $csv ) . '</code>' . '<br />';
			else
				echo $csv;

		}

	}

	function wpsc_ce_convert_product_raw_weight( $weight = null, $weight_unit = null ) {

		if( $weight && $weight_unit )
			$output = wpsc_convert_weight( $weight, 'pound', $weight_unit, false );
		return $output;

	}

	function wpsc_ce_get_product_images( $product_id ) {

		global $wpdb;

		$images_sql = "SELECT guid FROM `" . $wpdb->posts . "` WHERE `post_parent` = " . $product_id . " AND `post_type` = 'attachment' AND `post_mime_type` LIKE 'image/%'";
		$images = $wpdb->get_results( $images_sql );
		if( $images ) {
			$output = '';
			foreach( $images as $image )
				$output .= $image->guid . '|';
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	function wpsc_ce_get_product_categories( $product_id = null ) {

		global $export, $wpdb;

		$term_taxonomy = 'wpsc_product_category';
		$categories = wp_get_object_terms( $product_id, $term_taxonomy );
		$output = '';
		if( $categories ) {
			for( $i = 0; $i < count( $categories ); $i++ ) {
				if( $categories[$i]->parent == '0' ) {
					$output .= $categories[$i]->name . $export->category_separator;
				} else {
					// Check if Parent -> Child
					$parent_category = get_term( $categories[$i]->parent, $term_taxonomy );
					// Check if Parent -> Child -> Subchild
					if( $parent_category->parent == '0' ) {
						$output .= $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					} else {
						$root_category = get_term( $parent_category->parent, $term_taxonomy );
						$output .= $root_category->name . '>' . $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					}
					unset( $root_category, $parent_category );
				}
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	function wpsc_ce_get_product_tags( $product_id ) {

		global $wpdb;

		$tags_sql = "SELECT term_taxonomy.`term_id` as term_id FROM `" . $wpdb->term_taxonomy . "` as term_taxonomy, `" . $wpdb->term_relationships . "` as term_relationships WHERE term_relationships.`term_taxonomy_id` = term_taxonomy.`term_taxonomy_id` AND term_relationships.`object_id` = " . $product_id . " AND term_taxonomy.`taxonomy` = 'product_tag'";
		$tags = $wpdb->get_results( $tags_sql, ARRAY_A );
		if( $tags ) {
			$term_taxonomy = 'product_tag';
			$output = '';
			for( $i = 0; $i < count( $tags ); $i++ ) {
				$tag = get_term( $tags[$i]['term_id'], $term_taxonomy );
				$output .= $tag->name . '|';
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	/* End of: WordPress Administration */

}
?>