<?php
// Returns Product Tags associated to a specific Product
function wpsc_ce_get_product_assoc_tags( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_tag';
	$tags = wp_get_object_terms( $product_id, $term_taxonomy );
	if( is_wp_error( $tags ) == false ) {
		$size = count( $tags );
		for( $i = 0; $i < $size; $i++ ) {
			if( $tag = get_term( $tags[$i]->term_id, $term_taxonomy ) )
				$output .= $tag->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}
// Returns a list of Product export columns
function wpsc_ce_get_product_fields( $format = 'full' ) {

	$fields = array();
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent ID', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'parent_sku',
		'label' => __( 'Parent SKU', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'sku',
		'label' => __( 'Product SKU', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Product Name', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'permalink',
		'label' => __( 'Permalink', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'additional_description',
		'label' => __( 'Additional Description', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Product Published', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'post_modified',
		'label' => __( 'Product Modified', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'price',
		'label' => __( 'Price', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'sale_price',
		'label' => __( 'Sale Price', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'weight',
		'label' => __( 'Weight', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'weight_unit',
		'label' => __( 'Weight Unit', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'height',
		'label' => __( 'Height', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'height_unit',
		'label' => __( 'Height Unit', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'width',
		'label' => __( 'Width', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'width_unit',
		'label' => __( 'Width Unit', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'length',
		'label' => __( 'Length', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'length_unit',
		'label' => __( 'Length Unit', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'category',
		'label' => __( 'Category', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'tag',
		'label' => __( 'Tag', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Image', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'quantity',
		'label' => __( 'Quantity', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'notify_oos',
		'label' => __( 'Notify OOS', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'unpublish_oos',
		'label' => __( 'Unpublish OOS', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'file_download',
		'label' => __( 'File Download', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'external_link',
		'label' => __( 'External Link', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'external_link_text',
		'label' => __( 'External Link Text', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'external_link_target',
		'label' => __( 'External Link Target', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'local_shipping',
		'label' => __( 'Local Shipping Fee', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'international_shipping',
		'label' => __( 'International Shipping Fee', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'no_shipping',
		'label' => __( 'No Shipping', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'taxable_amount',
		'label' => __( 'Taxable Amount', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'tax_bands',
		'label' => __( 'Tax Bands', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'not_taxable',
		'label' => __( 'Not Taxable', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'product_status',
		'label' => __( 'Product Status', 'wpsc_ce' )
	);
	$fields[] = array(
		'name' => 'comment_status',
		'label' => __( 'Comment Status', 'wpsc_ce' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'wpsc_ce' )
	);
*/

	// Allow Plugin/Theme authors to add support for additional Product columns
	$fields = apply_filters( 'wpsc_ce_product_fields', $fields );

	// Advanced Google Product Feed - http://www.leewillis.co.uk/wordpress-plugins/
	if( function_exists( 'wpec_gpf_install' ) ) {
		$fields[] = array(
			'name' => 'gpf_availability',
			'label' => __( 'Advanced Google Product Feed - Availability', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_condition',
			'label' => __( 'Advanced Google Product Feed - Condition', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_brand',
			'label' => __( 'Advanced Google Product Feed - Brand', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_productype',
			'label' => __( 'Advanced Google Product Feed - Product Type', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_google_product_category',
			'label' => __( 'Advanced Google Product Feed - Google Product Category', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_gtin',
			'label' => __( 'Advanced Google Product Feed - Global Trade Item Number (GTIN)', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_mpn',
			'label' => __( 'Advanced Google Product Feed - Manufacturer Part Number (MPN)', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_gender',
			'label' => __( 'Advanced Google Product Feed - Gender', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_agegroup',
			'label' => __( 'Advanced Google Product Feed - Age Group', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_colour',
			'label' => __( 'Advanced Google Product Feed - Colour', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_size',
			'label' => __( 'Advanced Google Product Feed - Size', 'wpsc_ce' )
		);
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( function_exists( 'aioseop_activate' ) ) {
		$fields[] = array(
			'name' => 'aioseop_keywords',
			'label' => __( 'All in One SEO - Keywords', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_description',
			'label' => __( 'All in One SEO - Description', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_title',
			'label' => __( 'All in One SEO - Title', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_title_attributes',
			'label' => __( 'All in One SEO - Title Attributes', 'wpsc_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_menu_label',
			'label' => __( 'All in One SEO - Menu Label', 'wpsc_ce' )
		);
	}

	// Custom Fields - http://wordpress.org/plugins/wp-e-commerce-custom-fields/
	if( function_exists( 'wpsc_cf_install' ) ) {
		$attributes = maybe_unserialize( get_option( 'wpsc_cf_data' ) );
		if( !empty( $attributes ) ) {
			foreach( $attributes as $key => $attribute ) {
				$fields[] = array(
					'name' => sprintf( 'attribute_%s', $attribute['slug'] ),
					'label' => sprintf( __( 'Attribute: %s', 'wpsc_ce' ), $attribute['name'] )
				);
			}
			unset( $attributes, $attribute );
		}
	}

	// Related Products - http://www.visser.com.au/plugins/related-products/
	if( function_exists( 'wpsc_rp_pd_options_addons' ) ) {
		$fields[] = array(
			'name' => 'related_products',
			'label' => __( 'Related Products', 'wpsc_ce' )
		);
	}

	// Simple Product Options - http://wordpress.org/plugins/wp-e-commerce-simple-product-options/
	if( class_exists( 'wpec_simple_product_options_admin' ) ) {
		$args = array(
			'hide_empty' => false,
			'parent' => 0
		);
		$product_options = get_terms( 'wpec_product_option', $args );
		if( is_wp_error( $product_options ) == false ) {
			foreach( $product_options as $product_option ) {
				$fields[] = array(
					'name' => sprintf( 'simple_product_option_%s', $product_option->slug ),
					'label' => sprintf( __( 'Simple Product Option: %s', 'wpsc_ce' ), $product_option->name )
				);
			}
		}
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( function_exists( 'wpseo_admin_init' ) ) {
		$fields[] = array(
			'name' => 'wpseo_focuskw',
			'label' => __( 'WordPress SEO - Focus Keyword', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'wpseo_metadesc',
			'label' => __( 'WordPress SEO - Meta Description', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'wpseo_title',
			'label' => __( 'WordPress SEO - SEO Title', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'wpseo_googleplus_description',
			'label' => __( 'WordPress SEO - Google+ Description', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_description',
			'label' => __( 'WordPress SEO - Facebook Description', 'wpsc_pd' )
		);
	}

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( function_exists( 'su_wp_incompat_notice' ) ) {
		$fields[] = array(
			'name' => 'useo_meta_title',
			'label' => __( 'Ultimate SEO - Title Tag', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'useo_meta_description',
			'label' => __( 'Ultimate SEO - Meta Description', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'useo_meta_keywords',
			'label' => __( 'Ultimate SEO - Meta Keywords', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'useo_social_title',
			'label' => __( 'Ultimate SEO - Social Title', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'useo_social_description',
			'label' => __( 'Ultimate SEO - Social Description', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noindex',
			'label' => __( 'Ultimate SEO - NoIndex', 'wpsc_pd' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noautolinks',
			'label' => __( 'Ultimate SEO - Disable Autolinks', 'wpsc_pd' )
		);
	}

	if( $remember = wpsc_ce_get_option( 'products_fields', array() ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = 0;
			$fields[$i]['default'] = 1;
			if( !array_key_exists( $fields[$i]['name'], $remember ) )
				$fields[$i]['default'] = 0;
		}
	}

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

// Returns the export column header label based on an export column slug
function wpsc_ce_get_product_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = wpsc_ce_get_product_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						break;

					case 'full':
						$output = $fields[$i];
						break;

				}
				$i = $size;
			}
		}
	}
	return $output;

}
?>