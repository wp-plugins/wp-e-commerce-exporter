<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_template_header( $title = '', $icon = 'tools' ) {

		global $wpsc_ce;

		if( $title )
			$output = $title;
		else
			$output = $wpsc_ce['menu'];
		$icon = wpsc_is_admin_icon_valid( $icon ); ?>
<div class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32"><br /></div>
	<h2><?php echo $output; ?></h2>
<?php
	}

	function wpsc_ce_template_footer() { ?>
</div>
<?php
	}

	function wpsc_ce_support_donate() {

		global $wpsc_ce;

		$output = '';
		$show = true;
		if( function_exists( 'wpsc_vl_we_love_your_plugins' ) ) {
			if( in_array( $wpsc_ce['dirname'], wpsc_vl_we_love_your_plugins() ) )
				$show = false;
		}
		if( function_exists( 'wpsc_cd_admin_init' ) )
			$show = false;
		if( $show ) {
			$donate_url = 'http://www.visser.com.au/#donations';
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . $wpsc_ce['dirname'];
			$output = '
	<div id="support-donate_rate" class="support-donate_rate">
		<p>' . sprintf( __( '<strong>Like this Plugin?</strong> %s and %s.', 'wpsc_ce' ), '<a href="' . $donate_url . '" target="_blank">' . __( 'Donate to support this Plugin', 'wpsc_ce' ) . '</a>', '<a href="' . add_query_arg( array( 'rate' => '5' ), $rate_url ) . '#postform" target="_blank">rate / review us on WordPress.org</a>' ) . '</p>
	</div>
';
		}
		echo $output;

	}

	function wpsc_ce_generate_csv_header( $dataset = '' ) {

		$filename = 'wpsc-export_' . $dataset . '.csv';

		header( 'Content-type: application/csv' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

	}

	function wpsc_ce_has_value( $value = '' ) {

		switch( $value ) {

			case '0':
				$value = null;
				break;

			default:
				if( is_string( $value ) )
					$value = htmlspecialchars_decode( $value );
				break;

		}
		return $value;

	}

	function wpsc_ce_clean_html( $data ) {

		$output_encoding = 'ISO-8859-1';
		$data = mb_convert_encoding( trim( $data ), 'UTF-8', $output_encoding );
		$data = str_replace( ',', '&#44;', $data );
		$data = str_replace( "\n", '<br />', $data );

		return $data;

	}

	if( !function_exists( 'escape_csv_value' ) ) {
		function escape_csv_value( $value ) {

			$value = str_replace( '"', '""', $value ); // First off escape all " and make them ""
			$value = str_replace( PHP_EOL, ' ', $value );
			return '"' . $value . '"'; // If I have new lines or commas escape them

		}
	}

	function wpsc_ce_post_statuses() {

		$output = array(
			'publish',
			'pending',
			'draft',
			'future',
			'private',
			'trash'
		);
		return $output;

	}

	function wpsc_ce_get_checkout_fields( $format = 'full' ) {

		global $wpdb;

		$fields = array();
		$checkout_fields_sql = "SELECT * FROM `wp_wpsc_checkout_forms` WHERE `active` = 1 AND `type` <> 'heading'";
		$checkout_fields = $wpdb->get_results( $checkout_fields_sql );
		if( $checkout_fields ) {
			foreach( $checkout_fields as $key => $checkout_field ) {
				$fields[] = array(
					'name' => sprintf( 'checkout_%d', $checkout_field->id ),
					'label' => $checkout_field->name,
					'default' => 1
				);
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

		}

	}

	function wpsc_ce_get_submited_form_data( $checkout_fields = '' ) {

		global $wpdb;

		$output = array();
		if( $checkout_fields ) {
			foreach( $checkout_fields as $checkout_key => $checkout_field ) {
				$key = str_replace( 'checkout_', '', $checkout_key );
				if( $key ) {
					$value_sql = $wpdb->prepare( "SELECT `value` FROM `" . $wpdb->prefix . "wpsc_submited_form_data` WHERE `form_id` = %d LIMIT 1", $key );
					$value = $wpdb->get_var( $value_sql );
					if( $value )
						$checkout_fields[$checkout_key] = $value;
					else
						unset( $checkout_fields[$checkout_key] );
				}
			}
			$output = $checkout_fields;
		}
		return $output;

	}

	function wpsc_ce_get_product_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'sku',
			'label' => __( 'SKU', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'name',
			'label' => __( 'Product Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'permalink',
			'label' => __( 'Permalink', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'description',
			'label' => __( 'Description', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'additional_description',
			'label' => __( 'Additional Description', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'price',
			'label' => __( 'Price', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sale_price',
			'label' => __( 'Sale Price', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'weight',
			'label' => __( 'Weight', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'weight_unit',
			'label' => __( 'Weight Unit', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'height',
			'label' => __( 'Height', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'height_unit',
			'label' => __( 'Height Unit', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'width',
			'label' => __( 'Width', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'width_unit',
			'label' => __( 'Width Unit', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'length',
			'label' => __( 'Length Unit', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'category',
			'label' => __( 'Category', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'tag',
			'label' => __( 'Tag', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'image',
			'label' => __( 'Image', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'quantity',
			'label' => __( 'Quantity', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'file_download',
			'label' => __( 'File Download', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'external_link',
			'label' => __( 'External Link', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'external_link_text',
			'label' => __( 'External Link Text', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'external_link_target',
			'label' => __( 'External Link Target', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'local_shipping_fee',
			'label' => __( 'Local Shipping Fee', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'international_shipping_fee',
			'label' => __( 'International Shipping Fee', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_status',
			'label' => __( 'Product Status', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'comment_status',
			'label' => __( 'Comment Status', 'wpsc_ce' ),
			'default' => 1
		);
/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'wpsc_ce' ),
			'default' => 1
		);
*/

		/* Allow Plugin/Theme authors to add support for additional Product columns */
		$fields = apply_filters( 'wpsc_ce_product_fields', $fields );

		/* Advanced Google Product Feed */
		if( function_exists( 'wpec_gpf_install' ) ) {
			$fields[] = array(
				'name' => 'gpf_availability',
				'label' => __( 'Advanced Google Product Feed - Availability', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_condition',
				'label' => __( 'Advanced Google Product Feed - Condition', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_brand',
				'label' => __( 'Advanced Google Product Feed - Brand', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_productype',
				'label' => __( 'Advanced Google Product Feed - Product Type', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_google_product_category',
				'label' => __( 'Advanced Google Product Feed - Google Product Category', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_gtin',
				'label' => __( 'Advanced Google Product Feed - Global Trade Item Number (GTIN)', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_mpn',
				'label' => __( 'Advanced Google Product Feed - Manufacturer Part Number (MPN)', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_gender',
				'label' => __( 'Advanced Google Product Feed - Gender', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_agegroup',
				'label' => __( 'Advanced Google Product Feed - Age Group', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_colour',
				'label' => __( 'Advanced Google Product Feed - Colour', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array(
				'name' => 'gpf_size',
				'label' => __( 'Advanced Google Product Feed - Size', 'wpsc_ce' ),
				'default' => 0
			);
		}

		/* All in One SEO Pack */
		if( function_exists( 'aioseop_activate' ) ) {
			$fields[] = array( 
				'name' => 'aioseop_keywords',
				'label' => __( 'All in One SEO - Keywords', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array( 
				'name' => 'aioseop_description',
				'label' => __( 'All in One SEO - Description', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array( 
				'name' => 'aioseop_title',
				'label' => __( 'All in One SEO - Title', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array( 
				'name' => 'aioseop_title_attributes',
				'label' => __( 'All in One SEO - Title Attributes', 'wpsc_ce' ),
				'default' => 0
			);
			$fields[] = array( 
				'name' => 'aioseop_menu_label',
				'label' => __( 'All in One SEO - Menu Label', 'wpsc_ce' ),
				'default' => 0
			);
		}

		/* Custom Fields */
/*
		if( function_exists( 'wpsc_cf_install' ) ) {
			$attributes = maybe_unserialize( get_option( 'wpsc_cf_data' ) );
			if( isset( $attributes ) && $attributes ) {
				foreach( $attributes as $attribute ) {
					$export->columns[] = sprintf( __( 'Attribute - %s', 'wpsc_ce' ), $attribute['name'] );
				}
				unset( $attributes, $attribute );
			}
		}
*/

		/* Related Products */
		if( function_exists( 'wpsc_rp_pd_options_addons' ) ) {
			$fields[] = array( 
				'name' => 'related_products',
				'label' => __( 'Related Products', 'wpsc_ce' ),
				'default' => 0
			);
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

		}

	}

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

	function wpsc_ce_get_coupon_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'coupon_code',
			'label' => __( 'Coupon Code', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'coupon_value',
			'label' => __( 'Coupon Value', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'use_once',
			'label' => __( 'Use Once', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'active',
			'label' => __( 'Active', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'every_product',
			'label' => __( 'Apply to All Products', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'start',
			'label' => __( 'Valid From', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'expiry',
			'label' => __( 'Valid To', 'wpsc_ce' ),
			'default' => 1
		);
/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'wpsc_ce' ),
			'default' => 1
		);
*/

		/* Allow Plugin/Theme authors to add support for additional Coupon columns */
		$fields = apply_filters( 'wpsc_ce_coupon_fields', $fields );

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

		}

	}

	function wpsc_ce_get_coupon_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = wpsc_ce_get_coupon_fields();
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

	function wpsc_ce_get_sale_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array( 
			'name' => 'purchase_id',
			'label' => __( 'Purchase ID', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'purchase_total',
			'label' => __( 'Purchase Total', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'payment_gateway',
			'label' => __( 'Payment Gateway', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_method',
			'label' => __( 'Shipping Method', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array( 
			'name' => 'payment_status',
			'label' => __( 'Payment Status', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'purchase_date',
			'label' => __( 'Purchase Date', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'tracking_id',
			'label' => __( 'Tracking ID', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array( 
			'name' => 'notes',
			'label' => __( 'Notes', 'wpsc_ce' ),
			'default' => 0
		);
		$fields = array_merge_recursive( $fields, wpsc_ce_get_checkout_fields() );
/*
		$fields[] = array( 
			'name' => '',
			'label' => __( '', 'woo_ce' ),
			'default' => 1
		);
*/

		/* Allow Plugin/Theme authors to add support for additional Sale columns */
		$fields = apply_filters( 'wpsc_ce_sale_fields', $fields );

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

		}

	}

	function wpsc_ce_get_sale_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = wpsc_ce_get_sale_fields();
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

	function wpsc_ce_get_customer_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array( 
			'name' => 'full_name',
			'label' => __( 'Full Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'first_name',
			'label' => __( 'First Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'last_name',
			'label' => __( 'Last Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'street_address',
			'label' => __( 'Street Address', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'city',
			'label' => __( 'City', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'state',
			'label' => __( 'State', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'zip_code',
			'label' => __( 'ZIP Code', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'country',
			'label' => __( 'Country', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'phone_number',
			'label' => __( 'Phone Number', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'email',
			'label' => __( 'E-mail Address', 'wpsc_ce' ),
			'default' => 1
		);
		return $fields;

	}

	function wpsc_ce_get_customer_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = wpsc_ce_get_customer_fields();
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

	function wpsc_ce_admin_active_tab( $tab_name = null, $tab = null ) {

		if( isset( $_GET['tab'] ) && !$tab )
			$tab = $_GET['tab'];
		else
			$tab = 'overview';

		$output = '';
		if( isset( $tab_name ) && $tab_name ) {
			if( $tab_name == $tab )
				$output = ' nav-tab-active';
		}
		echo $output;

	}

	function wpsc_ce_tab_template( $tab = '' ) {

		global $wpsc_ce;

		if( !$tab )
			$tab = 'overview';

		/* Store Exporter Deluxe */
		$wpsc_cd_exists = false;
		if( !function_exists( 'wpsc_cd_admin_init' ) ) {
			$wpsc_cd_url = 'http://www.visser.com.au/wp-ecommerce/plugins/exporter-deluxe/';
			$wpsc_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'wpsc_ce' ) . '</a>', $wpsc_cd_url );
		} else {
			$wpsc_cd_exists = true;
		}

		switch( $tab ) {

			case 'export':
				if( isset( $_POST['dataset'] ) )
					$dataset = $_POST['dataset'];
				else
					$dataset = 'products';

				$products = wpsc_ce_return_count( 'products' );
				$categories = wpsc_ce_return_count( 'orders' );
				$tags = wpsc_ce_return_count( 'tags' );
				$sales = wpsc_ce_return_count( 'orders' );
				$coupons = wpsc_ce_return_count( 'coupons' );
				$customers = wpsc_ce_return_count( 'customers' );

				$product_fields = wpsc_ce_get_product_fields();
				$sale_fields = wpsc_ce_get_sale_fields();
				$customer_fields = wpsc_ce_get_customer_fields();
				$coupon_fields = wpsc_ce_get_coupon_fields();
				break;

			case 'tools':
				/* Product Importer Deluxe */
				if( function_exists( 'wpsc_pd_init' ) ) {
					$wpsc_pd_url = add_query_arg( 'page', 'wpsc_pd' );
					$wpsc_pd_target = false;
				} else {
					$wpsc_pd_url = 'http://www.visser.com.au/wp-ecommerce/plugins/product-importer-deluxe/';
					$wpsc_pd_target = ' target="_blank"';
				}
				/* Coupon Importer Deluxe */
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
			include_once( $wpsc_ce['abspath'] . '/templates/admin/wpsc-admin_ce-export_' . $tab . '.php' );

	}

	/* End of: WordPress Administration */

}
?>