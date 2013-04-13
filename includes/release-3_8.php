<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_admin_page_item( $menu = array() ) {

		global $wpsc_ce;

		$title = $wpsc_ce['menu'];
		$link = add_query_arg( array( 'post_type' => 'wpsc-product', 'page' => 'wpsc_ce' ), 'edit.php' );
		$description = __( 'Export store details out of WP e-Commerce into a CSV-formatted file.', 'wpsc_ce' );

		$menu[] = array( 'title' => $title, 'link' => $link, 'description' => $description );

		return $menu;

	}
	add_filter( 'wpsc_sm_store_admin_page', 'wpsc_ce_admin_page_item', 1 );

	function wpsc_ce_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			/* WP e-Commerce */

			case 'products':
				$post_type = 'wpsc-product';
				$count = wp_count_posts( $post_type );
				break;

			case 'variations':
				$post_type = 'wpsc-variation';
				$count = wp_count_posts( $post_type );
				break;

			case 'images':
				$post_type = 'attachment';
				$count_sql = $wpdb->prepare( "SELECT COUNT(`id`) FROM `" . $wpdb->posts . "` WHERE `post_type` = '%s' AND `post_mime_type` LIKE 'image/%'", $post_type );
				break;

			case 'files':
				$post_type = 'wpsc-product-file';
				$count = wp_count_posts( $post_type );
				break;

			case 'categories':
				$term_taxonomy = 'wpsc_product_category';
				$count = wp_count_terms( $term_taxonomy );
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

			/* 3rd Party */

			case 'wishlist':
				$post_type = 'wpsc-wishlist';
				$count = wp_count_posts( $post_type );
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

	function wpsc_ce_export_dataset( $dataset, $args = array() ) {

		global $wpdb, $wpsc_ce, $export;

		$csv = '';
		$separator = $export->delimiter;

		foreach( $dataset as $datatype ) {

			$csv = '';
			switch( $datatype ) {

				/* Products */
				case 'products':
					$fields = wpsc_ce_get_product_fields( 'summary' );
					$export->fields = array_intersect_assoc( $fields, $export->fields );
					if( $export->fields ) {
						foreach( $export->fields as $key => $field )
							$export->columns[] = wpsc_ce_get_product_field( $key );
					}
					$size = count( $export->columns );
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$csv .= escape_csv_value( $export->columns[$i] ) . "\n";
						else
							$csv .= escape_csv_value( $export->columns[$i] ) . $separator;
					}
					$products = wpsc_ce_get_products( $args['limit_volume'], $args['offset'] );
					if( $products ) {
						foreach( $products as $product ) {
							foreach( $export->fields as $key => $field ) {
								if( isset( $product->$key ) ) {
									if( is_array( $value ) ) {
										foreach( $value as $array_key => $array_value ) {
											if( !is_array( $array_value ) )
												$csv .= escape_csv_value( $array_value );
										}
									} else {
										$csv .= escape_csv_value( $product->$key );
									}
								}
								$csv .= $separator;
							}
							$csv .= "\n";

						}
						unset( $products, $product );
					}
					break;

				/* Categories */
				case 'categories':
					$term_taxonomy = 'wpsc_product_category';
					$args = array(
						'hide_empty' => 0
					);
					$categories = get_terms( $term_taxonomy, $args );
					if( $categories ) {
						$columns = array(
							__( 'Category', 'wpsc_ce' )
						);
						for( $i = 0; $i < count( $columns ); $i++ ) {
							if( $i == ( count( $columns ) - 1 ) )
								$csv .= $columns[$i] . "\n";
							else
								$csv .= $columns[$i] . $separator;
						}
						foreach( $categories as $category ) {
							$csv .= 
								$category->name
								 . 
							"\n";
						}
						unset( $categories, $category );
					}
					break;

				/* Tags */
				case 'tags':
					$term_taxonomy = 'product_tag';
					$args = array(
						'hide_empty' => 0
					);
					$tags = get_terms( $term_taxonomy, $args );
					if( $tags ) {
						$columns = array(
							__( 'Tags', 'wpsc_ce' )
						);
						for( $i = 0; $i < count( $columns ); $i++ ) {
							if( $i == ( count( $columns ) - 1 ) )
								$csv .= $columns[$i] . "\n";
							else
								$csv .= $columns[$i] . $separator;
						}
						foreach( $tags as $tag ) {
							$csv .= 
								$tag->name
								 . 
							"\n";
						}
						unset( $tags, $tag );
					}
					break;

				/* Orders */
				case 'orders':
				/* Coupons */
				case 'coupons':
				/* Customers */
				case 'customers':
					$csv = apply_filters( 'wpsc_ce_export_dataset', $datatype, $export );
					break;

			}
			$csv = utf8_decode( $csv );

			if( isset( $wpsc_ce['debug'] ) && $wpsc_ce['debug'] )
				echo '<code>' . str_replace( "\n", '<br />', $csv ) . '</code>' . '<br />';
			else
				echo $csv;

		}

	}

	function wpsc_ce_get_products( $limit_volume = -1, $offset = 0 ) {

		$post_type = 'wpsc-product';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => $limit_volume,
			'offset' => $offset,
			'post_status' => wpsc_ce_post_statuses()
		);
		$products = get_posts( $args );
		if( $products ) {
			foreach( $products as $key => $product ) {
				$product_data = get_post_meta( $product->ID, '_wpsc_product_metadata', true );

				$products[$key]->sku = get_product_meta( $product->ID, 'sku', true );
				$products[$key]->name = $product->post_title;
				$products[$key]->description = wpsc_ce_clean_html( $product->post_content );
				$products[$key]->additional_description = wpsc_ce_clean_html( $product->post_excerpt );
				if( get_product_meta( $product->ID, 'price', true ) )
					$products[$key]->price = get_product_meta( $product->ID, 'price', true );
				else
					$products[$key]->price = 0;
				if( get_product_meta( $product->ID, 'special_price', true ) )
					$products[$key]->sale_price = get_product_meta( $product->ID, 'special_price', true );
				else
					$products[$key]->sale_price = '0.00';
				$products[$key]->permalink = $product->post_name;
				$products[$key]->weight = wpsc_ce_convert_product_raw_weight( $product_data['weight'], $product_data['weight_unit'] );
				if( !$products[$key]->weight )
					$products[$key]->weight = 0;
				if( isset( $product_data['weight_unit'] ) )
					$products[$key]->weight_unit = $product_data['weight_unit'];
				if( !$products[$key]->weight_unit )
					$products[$key]->weight_unit = 'kg';
				if( isset( $product_data['dimensions']['height'] ) )
					$products[$key]->height = trim( $product_data['dimensions']['height'] );
				if( !$products[$key]->height )
					$products[$key]->height = 0;
				if( isset( $product_data['dimensions']['height_unit'] ) )
					$products[$key]->height_unit = $product_data['dimensions']['height_unit'];
				if( !$products[$key]->height_unit )
					$products[$key]->height_unit = 'in';
				if( isset( $product_data['dimensions']['width'] ) )
					$products[$key]->width = trim( $product_data['dimensions']['width'] );
				if( !$products[$key]->width )
					$products[$key]->width = 0;
				if( isset( $product_data['dimensions']['width_unit'] ) )
					$products[$key]->width_unit = $product_data['dimensions']['width_unit'];
				if( !$products[$key]->width_unit )
					$products[$key]->width_unit = 'in';
				if( isset( $product_data['dimensions']['length'] ) )
					$products[$key]->length = trim( $product_data['dimensions']['length'] );
				if( !$products[$key]->length )
					$products[$key]->length = 0;
				if( isset( $product_data['dimensions']['length_unit'] ) )
					$products[$key]->length_unit = $product_data['dimensions']['length_unit'];
				if( !$products[$key]->length_unit )
					$products[$key]->length_unit = 'in';
				$products[$key]->category = wpsc_ce_get_product_categories( $product->ID );
				$products[$key]->tag = wpsc_ce_get_product_tags( $product->ID );
				$products[$key]->image = wpsc_ce_get_product_images( $product->ID );
				$products[$key]->quantity_limited = $product_data['quantity_limited'];
				$products[$key]->quantity = get_product_meta( $product->ID, 'stock', true );
				if( $products[$key]->quantity_limited && empty( $products[$key]->quantity ) )
					$products[$key]->quantity = 0;
				$products[$key]->external_link = $product_data['external_link'];
				$products[$key]->external_link_text = $product_data['external_link_text'];
				$products[$key]->external_link_target = $product_data['external_link_target'];
				if( isset( $product_data['shipping']['local'] ) )
					$products[$key]->local_shipping = $product_data['shipping']['local'];
				if( isset( $product_data['shipping']['international'] ) )
					$products[$key]->international_shipping = $product_data['shipping']['international'];
				$products[$key]->product_status = wpsc_ce_format_product_status( $product->post_status );
				$products[$key]->comment_status = wpsc_ce_format_comment_status( $product->comment_status );

				/* Allow Plugin/Theme authors to add support for additional Product columns */
				$products[$key] = apply_filters( 'wpsc_ce_product_data', $products[$key] );

				/* Advanced Google Product Feed */
				if( function_exists( 'wpec_gpf_install' ) ) {
					$products[$key]->gpf_data = get_post_meta( $product->ID, '_wpec_gpf_data', true );
					$products[$key]->gpf_availability = wpsc_ce_format_gpf_availability( $product->gpf_data['availability'] );
					$products[$key]->gpf_condition = wpsc_ce_format_gpf_condition( $product->gpf_data['condition'] );
					$products[$key]->gpf_brand = $product->gpf_data['brand'];
					$products[$key]->gpf_product_type = $product->gpf_data['product_type'];
					$products[$key]->gpf_google_product_category = $product->gpf_data['google_product_category'];
					$products[$key]->gpf_gtin = $product->gpf_data['gtin'];
					$products[$key]->gpf_mpn = $product->gpf_data['mpn'];
					$products[$key]->gpf_gender = $product->gpf_data['gender'];
					$products[$key]->gpf_age_group = $product->gpf_data['age_group'];
					$products[$key]->gpf_color = $product->gpf_data['color'];
					$products[$key]->gpf_size = $product->gpf_data['size'];
				}
				/* All in One SEO Pack */
				if( function_exists( 'aioseop_activate' ) ) {
					$products[$key]->aioseop_keywords = get_post_meta( $product->ID, '_aioseop_keywords', true );
					$products[$key]->aioseop_description = get_post_meta( $product->ID, '_aioseop_description', true );
					$products[$key]->aioseop_title = get_post_meta( $product->ID, '_aioseop_title', true );
					$products[$key]->aioseop_titleatr = get_post_meta( $product->ID, '_aioseop_titleatr', true );
					$products[$key]->aioseop_menulabel = get_post_meta( $product->ID, '_aioseop_menulabel', true );
				}
				/* Custom Fields */
				if( $custom_fields ) {
					$product->custom_fields = array();
					foreach( $custom_fields as $custom_field )
						$product->custom_fields[$custom_field['slug']] = get_product_meta( $product->ID, $custom_field['slug'], true );
				}
				/* Related Products */
				if( isset( $product_data['wpsc_rp_manual'] ) )
					$products[$key]->related_products = wpsc_ce_get_related_products( $product->ID );
			}
		}
		return $products;

	}

	function wpsc_ce_convert_product_raw_weight( $weight = null, $weight_unit = null ) {

		if( $weight && $weight_unit )
			$output = wpsc_convert_weight( $weight, 'pound', $weight_unit, false );
		return $output;

	}

	function wpsc_ce_get_product_images( $product_id ) {

		global $export;

		$post_type = 'attachment';
		$args = array(
			'post_type' => $post_type,
			'post_parent' => $product_id,
			'post_status' => 'inherit',
			'post_mime_type' => 'image',
			'numberposts' => -1
		);
		$images = get_children( $args );
		if( $images ) {
			$output = '';
			/* Check for Featured Image */
			$featured_image = get_post_meta( $product_id, '_thumbnail_id', true );
			if( $featured_image ) {
				$image = get_post( $featured_image );
				$output .= $image->guid . $export->category_separator;
			} else {
				$featured_image = 0;
			}
			foreach( $images as $image ) {
				if( $featured_image <> $image->ID )
					$output .= $image->guid . $export->category_separator;
			}
			unset( $featured_image );
			$output = substr( $output, 0, -1 );
		}
		unset( $images );
		return $output;

	}

	function wpsc_ce_get_product_categories( $product_id = null ) {

		global $export, $wpdb;

		$output = '';
		$term_taxonomy = 'wpsc_product_category';
		$categories = wp_get_object_terms( $product_id, $term_taxonomy );
		if( $categories ) {
			$size = count( $categories );
			for( $i = 0; $i < $size; $i++ ) {
				if( $categories[$i]->parent == '0' ) {
					$output .= $categories[$i]->name . $export->category_separator;
				} else {
					// Check if Parent -> Child
					$parent_category = get_term( $categories[$i]->parent, $term_taxonomy );
					// Check if Parent -> Child -> Subchild
					if( $parent_category->parent == '0' ) {
						$output .= $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
						$output = str_replace( $parent_category->name . $export->category_separator, '', $output );
					} else {
						$root_category = get_term( $parent_category->parent, $term_taxonomy );
						$output .= $root_category->name . '>' . $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
						$output = str_replace( array(
							$root_category->name . '>' . $parent_category->name . $export->category_separator,
							$parent_category->name . $export->category_separator
						), '', $output );
					}
					unset( $root_category, $parent_category );
				}
			}
			$output = substr( $output, 0, -1 );
		} else {
			$output .= __( 'Uncategorized', 'wpsc_ce' );
		}
		return $output;

	}

	function wpsc_ce_get_product_tags( $product_id ) {

		global $wpdb, $export;

		$output = '';
		$term_taxonomy = 'product_tag';
		$tags = wp_get_object_terms( $product_id, $term_taxonomy );
		if( $tags ) {
			$size = count( $tags );
			for( $i = 0; $i < $size; $i++ ) {
				$tag = get_term( $tags[$i]->term_id, $term_taxonomy );
				$output .= $tag->name . $export->category_separator;
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	function wpsc_ce_get_related_products( $product_id ) {

		global $export;

		$output = '';
		$product_data = maybe_unserialize( get_product_meta( $product_id, 'product_metadata', true ) );
		if( isset( $product_data['wpsc_rp_manual'] ) && $product_data['wpsc_rp_manual'] ) {
			foreach( $product_data['wpsc_rp_manual'] as $related_product )
				$output .= $related_product . $export->category_separator;
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	/* End of: WordPress Administration */

}
?>