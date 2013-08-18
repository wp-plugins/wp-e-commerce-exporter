<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Administration menu */
	function wpsc_ce_add_modules_admin_pages( $page_hooks, $base_page ) {

		$page_hooks[] = add_submenu_page( $base_page, __( 'Store Export', 'wpsc_ce' ), __( 'Store Export', 'wpsc_ce' ), 'manage_options', 'wpsc_ce', 'wpsc_ce_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_ce_add_modules_admin_pages', 10, 2 );

	function wpsc_ce_template_header( $title = '', $icon = 'tools' ) {

		global $wpsc_ce;

		if( $title )
			$output = $title;
		else
			$output = $wpsc_ce['menu'];
		$icon = wpsc_is_admin_icon_valid( $icon ); ?>
<div class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32"><br /></div>
	<h2>
		<?php echo $output; ?>
		<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'wpsc_ce' ); ?></a>
	</h2>
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

	function wpsc_ce_save_fields( $dataset, $fields = array() ) {

		if( $dataset && !empty( $fields ) ) {
			$type = $dataset[0];
			wpsc_ce_update_option( $type . '_fields', $fields );
		}

	}

	function wpsc_ce_generate_csv_header( $dataset = '' ) {

		$filename = wpsc_ce_generate_csv_filename( $dataset );
		if( $filename ) {
			header( 'Content-Encoding: UTF-8' );
			header( 'Content-Type: text/csv; charset=UTF-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
		}

	}

	function wpsc_ce_generate_csv_filename( $dataset = '' ) {

		$date = date( 'Ymd' );
		$output = 'wpsc-export_default-' . $date . '.csv';
		if( $dataset ) {
			$filename = 'wpsc-export_' . $dataset . '-' . $date . '.csv';
			if( $filename )
				$output = $filename;
		}
		return $output;

	}

	function wpsc_ce_orders_filter_by_date() {

		$current_month = date( 'F' );
		$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
		$order_dates_from = '-';
		$order_dates_to = '-';

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-date" /> <?php _e( 'Filter Orders by Order Date', 'wpsc_ce' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_month" disabled="disabled" /> <?php _e( 'Current month', 'wpsc_ce' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_month" disabled="disabled" /> <?php _e( 'Last month', 'wpsc_ce' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="manual" disabled="disabled" /> <?php _e( 'Manual', 'wpsc_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="order_dates_from" name="order_dates_from" value="<?php echo $order_dates_from; ?>" class="text" disabled="disabled" /> to <input type="text" size="10" maxlength="10" id="order_dates_to" name="order_dates_to" value="<?php echo $order_dates_to; ?>" class="text" disabled="disabled" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first order to today.', 'wpsc_ce' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	function wpsc_ce_orders_filter_by_customer() {

		ob_start(); ?>
<p><label for="order_customer"><?php _e( 'Filter Orders by Customer', 'wpsc_ce' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<select id="order_customer" name="order_customer" disabled="disabled">
		<option value=""><?php _e( 'Show all customers', 'wpsc_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Filter Orders by Customer (unique e-mail address) to be included in the export. Default is to include all Orders.', 'wpsc_ce' ); ?></p>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	function wpsc_ce_orders_filter_by_status() {

		global $wpsc_purchlog_statuses;

		$order_statuses = $wpsc_purchlog_statuses;
		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-status" /> <?php _e( 'Filter Orders by Order Status', 'wpsc_ce' ); ?></label></p>
<div id="export-orders-filters-status" class="separator">
	<ul>
<?php foreach( $order_statuses as $order_status ) { ?>
		<li><label><input type="checkbox" name="order_filter_status[<?php echo $order_status['order']; ?>]" value="<?php echo $order_status['order']; ?>" /> <?php echo $order_status['label']; ?></label></li>
<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'wpsc_ce' ); ?></p>
</div>
<!-- #export-orders-filters-status -->
<?php
		ob_end_flush();

	}

	function wpsc_ce_add_post_mime_type( $post_mime_types = array() ) {

		$post_mime_types['text/csv'] = array( __( 'Store Exports', 'wpsc_ce' ), __( 'Manage Store Exports', 'wpsc_ce' ), _n_noop( 'Store Export <span class="count">(%s)</span>', 'Store Exports <span class="count">(%s)</span>' ) );
		return $post_mime_types;

	}
	add_filter( 'post_mime_types', 'wpsc_ce_add_post_mime_type' );

	function wpsc_ce_read_csv_file( $post = null ) {

		if( $post->post_type != 'attachment' )
			return false;

		if( $post->post_mime_type != 'text/csv' )
			return false;

		$filename = $post->post_name;
		$filepath = get_attached_file( $post->ID );
		$contents = __( 'No export entries were found, please try again with different export filters.', 'wpsc_ce' );
		if( file_exists( $filepath ) ) {
			$handle = fopen( $filepath, "r" );
			$contents = stream_get_contents( $handle );
			fclose( $handle );
		}
		if( $contents ) { ?>
	<div class="postbox-container">
		<div class="postbox">
			<h3 class="hndle"><?php _e( 'CSV File', 'wpsc_ce' ); ?></h3>
			<div class="inside">
				<textarea style="font:12px Consolas, Monaco, Courier, monospace; width:100%; height:200px;"><?php echo $contents; ?></textarea>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->
	</div>
	<!-- .postbox-container -->
<?php
		}

	}
	add_action( 'edit_form_after_editor', 'wpsc_ce_read_csv_file' );

	function wpsc_ce_return_export_types() {

		$export_types = array();
		$export_types['products'] = __( 'Products', 'wpsc_ce' );
		$export_types['categories'] = __( 'Categories', 'wpsc_ce' );
		$export_types['tags'] = __( 'Tags', 'wpsc_ce' );
		$export_types['orders'] = __( 'Orders', 'wpsc_ce' );
		$export_types['customers'] = __( 'Customers', 'wpsc_ce' );
		$export_types['coupons'] = __( 'Coupons', 'wpsc_ce' );
		return $export_types;

	}

	function wpsc_ce_export_type_label( $export_type = '', $echo = false ) {

		$output = '';
		if( !empty( $export_type ) ) {
			$export_types = wpsc_ce_return_export_types();
			if( array_key_exists( $export_type, $export_types ) )
				$output = $export_types[$export_type];
		}
		if( $echo )
			echo $output;
		else
			return $output;

	}

	function wpsc_ce_count_object( $object = 0 ) {
	
		$count = 0;
		if( is_object( $object ) ) {
			foreach( $object as $key => $item )
				$count = $item + $count;
		} else {
			$count = $object;
		}
		return $count;

	}

	function wpsc_ce_post_statuses( $extra_status = array(), $override = false ) {

		$output = array(
			'publish',
			'pending',
			'draft',
			'future',
			'private',
			'trash'
		);
		if( $override ) {
			$output = $extra_status;
		} else {
			if( $extra_status )
				$output = array_merge( $output, $extra_status );
		}
		return $output;

	}

	function wpsc_ce_get_checkout_fields( $format = 'full' ) {

		global $wpdb;

		$fields = array();
		$checkout_fields_sql = "SELECT * FROM `" . $wpdb->prefix . "wpsc_checkout_forms` WHERE `active` = 1 AND `type` <> 'heading'";
		$checkout_fields = $wpdb->get_results( $checkout_fields_sql );
		if( $checkout_fields ) {
			foreach( $checkout_fields as $key => $checkout_field ) {
				$fields[] = array(
					'name' => sprintf( 'checkout_%d', $checkout_field->id ),
					'label' => sprintf( 'Checkout: %s', $checkout_field->name ),
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

	function wpsc_ce_get_submited_form_data( $checkout_fields = '', $order_id = 0 ) {

		global $wpdb;

		$output = array();
		if( $checkout_fields ) {
			foreach( $checkout_fields as $checkout_key => $checkout_field ) {
				$key = str_replace( 'checkout_', '', $checkout_key );
				if( $key ) {
					$value_sql = $wpdb->prepare( "SELECT `value` FROM `" . $wpdb->prefix . "wpsc_submited_form_data` WHERE `form_id` = %d AND `log_id` = %d LIMIT 1", $key, $order_id );
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
			'name' => 'parent_id',
			'label' => __( 'Parent ID', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'parent_sku',
			'label' => __( 'Parent SKU', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_id',
			'label' => __( 'Product ID', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sku',
			'label' => __( 'Product SKU', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'name',
			'label' => __( 'Product Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'slug',
			'label' => __( 'Slug', 'wpsc_ce' ),
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
			'label' => __( 'Length', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'length_unit',
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
			'name' => 'notify_oos',
			'label' => __( 'Notify OOS', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'unpublish_oos',
			'label' => __( 'Unpublish OOS', 'wpsc_ce' ),
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
			'name' => 'local_shipping',
			'label' => __( 'Local Shipping Fee', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'international_shipping',
			'label' => __( 'International Shipping Fee', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'no_shipping',
			'label' => __( 'No Shipping', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'taxable_amount',
			'label' => __( 'Taxable Amount', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'tax_bands',
			'label' => __( 'Tax Bands', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'not_taxable',
			'label' => __( 'Not Taxable', 'wpsc_ce' ),
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
		if( function_exists( 'wpsc_cf_install' ) ) {
			$attributes = maybe_unserialize( get_option( 'wpsc_cf_data' ) );
			if( !empty( $attributes ) ) {
				foreach( $attributes as $key => $attribute ) {
					$fields[] = array(
						'name' => sprintf( 'attribute_%s', $attribute['slug'] ),
						'label' => sprintf( __( 'Attribute - %s', 'wpsc_ce' ), $attribute['name'] ),
						'default' => 1
					);
				}
				unset( $attributes, $attribute );
			}
		}

		/* Related Products */
		if( function_exists( 'wpsc_rp_pd_options_addons' ) ) {
			$fields[] = array(
				'name' => 'related_products',
				'label' => __( 'Related Products', 'wpsc_ce' ),
				'default' => 0
			);
		}

		/* Simple Product Options */
		if( class_exists( 'wpec_simple_product_options_admin' ) ) {
			$args = array(
				'hide_empty' => false,
				'parent' => 0
			);
			$product_options = get_terms( 'wpec_product_option', $args );
			if( $product_options ) {
				foreach( $product_options as $product_option ) {
					$fields[] = array(
						'name' => sprintf( 'simple_product_option_%s', $product_option->slug ),
						'label' => sprintf( __( 'Simple Product Option: %s', 'wpsc_ce' ), $product_option->name ),
						'default' => 1
					);
				}
			}
		}

		$remember = wpsc_ce_get_option( 'products_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
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

	/* Tags */

	/* Tags */

	function wpsc_ce_get_product_tags() {

		$output = '';
		$term_taxonomy = 'product_tag';
		$args = array(
			'hide_empty' => 0
		);
		$tags = get_terms( $term_taxonomy, $args );
		if( $tags )
			$output = $tags;
		return $output;

	}

	function wpsc_ce_get_product_assoc_tags( $product_id ) {

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

	/* Orders */

	function wpsc_ce_get_order_fields( $format = 'full' ) {

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
			'name' => 'payment_status_int',
			'label' => __( 'Payment Status (number)', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'purchase_date',
			'label' => __( 'Purchase Date', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'purchase_time',
			'label' => __( 'Purchase Time', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'tracking_id',
			'label' => __( 'Tracking ID', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'transaction_id',
			'label' => __( 'Transaction ID', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'session_id',
			'label' => __( 'Session ID', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'order_personalisation',
			'label' => __( 'Order Personalisation', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'user_id',
			'label' => __( 'User ID', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'username',
			'label' => __( 'Username', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'notes',
			'label' => __( 'Notes', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'referral',
			'label' => __( 'Referral Source', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'order_items_product_id',
			'label' => __( 'Order Items: Product ID', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'order_items_sku',
			'label' => __( 'Order Items: SKU', 'wpsc_ce' ),
			'default' => 0
		);
		$fields[] = array(
			'name' => 'order_items_product_name',
			'label' => __( 'Order Items: Product Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_product_quantity',
			'label' => __( 'Order Items: Product Quantity', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_product_personalisation',
			'label' => __( 'Order Items: Product Personalisation', 'wpsc_ce' ),
			'default' => 0
		);
		$fields = array_merge_recursive( $fields, wpsc_ce_get_checkout_fields() );
/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'wpsc_ce' ),
			'default' => 1
		);
*/

		/* Allow Plugin/Theme authors to add support for additional Order columns */
		$fields = apply_filters( 'wpsc_ce_order_fields', $fields );

		$remember = wpsc_ce_get_option( 'orders_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
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

		}

	}

	function wpsc_ce_get_order_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = wpsc_ce_get_order_fields();
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

	/* Customers */

	function wpsc_ce_get_customer_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'user_id',
			'label' => __( 'User ID', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'user_name',
			'label' => __( 'Username', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_full_name',
			'label' => __( 'Billing: Full Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_first_name',
			'label' => __( 'Billing: First Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_last_name',
			'label' => __( 'Billing: Last Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_street_address',
			'label' => __( 'Billing: Street Address', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_city',
			'label' => __( 'Billing: City', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_state',
			'label' => __( 'Billing: State (prefix)', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_zip_code',
			'label' => __( 'Billing: ZIP Code', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_country',
			'label' => __( 'Billing: Country (prefix)', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_country_full',
			'label' => __( 'Billing: Country', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_phone_number',
			'label' => __( 'Billing: Phone Number', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_email',
			'label' => __( 'E-mail Address', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_full_name',
			'label' => __( 'Shipping: Full Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_first_name',
			'label' => __( 'Shipping: First Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_last_name',
			'label' => __( 'Shipping: Last Name', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_street_address',
			'label' => __( 'Shipping: Street Address', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_city',
			'label' => __( 'Shipping: City', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_state',
			'label' => __( 'Shipping: State (prefix)', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_zip_code',
			'label' => __( 'Shipping: ZIP Code', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_country',
			'label' => __( 'Shipping: Country (prefix)', 'wpsc_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_country_full',
			'label' => __( 'Shipping: Country', 'wpsc_ce' ),
			'default' => 1
		);

		$remember = wpsc_ce_get_option( 'customers_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
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

		}

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

	/* Coupons */

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

		$remember = wpsc_ce_get_option( 'coupons_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
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

	/* Export */

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
		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

		switch( $tab ) {

			case 'export':

				global $wpsc_purchlog_statuses;

				$dataset = 'products';
				if( isset( $_POST['dataset'] ) )
					$dataset = $_POST['dataset'];

				$products = wpsc_ce_return_count( 'products' );
				$categories = wpsc_ce_return_count( 'categories' );
				$tags = wpsc_ce_return_count( 'tags' );
				$orders = wpsc_ce_return_count( 'orders' );
				$coupons = wpsc_ce_return_count( 'coupons' );
				$customers = wpsc_ce_return_count( 'customers' );

				$product_fields = wpsc_ce_get_product_fields();
				if( $product_fields ) {
					$product_categories = wpsc_ce_get_product_categories();
					$product_tags = wpsc_ce_get_product_tags();
					$product_statuses = get_post_statuses();
					$product_statuses['trash'] = __( 'Trash', 'wpsc_ce' );
				}
				$order_fields = wpsc_ce_get_order_fields();
				if( $order_fields )
					$order_statuses = $wpsc_purchlog_statuses;
				$customer_fields = wpsc_ce_get_customer_fields();
				$coupon_fields = wpsc_ce_get_coupon_fields();

				$delimiter = wpsc_ce_get_option( 'delimiter', ',' );
				$category_separator = wpsc_ce_get_option( 'category_separator', '|' );
				$bom = wpsc_ce_get_option( 'bom', 1 );
				$escape_formatting = wpsc_ce_get_option( 'escape_formatting', 'all' );
				$limit_volume = wpsc_ce_get_option( 'limit_volume' );
				$offset = wpsc_ce_get_option( 'offset' );
				$timeout = wpsc_ce_get_option( 'timeout', 0 );
				$delete_csv = wpsc_ce_get_option( 'delete_csv', 0 );
				$file_encodings = mb_list_encodings();
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

			case 'archive':
				$files = wpsc_ce_get_archive_files();
				if( $files ) {
					foreach( $files as $key => $file )
						$files[$key] = wpsc_ce_get_archive_file( $file );
				}
				break;

		}
		if( $tab )
			include_once( $wpsc_ce['abspath'] . '/templates/admin/wpsc-admin_ce-export_' . $tab . '.php' );

	}

	function wpsc_ce_save_csv_file_attachment( $filename = '' ) {

		$output = 0;
		if( !empty( $filename ) ) {
			$object = array(
				'post_title' => $filename,
				'post_type' => 'wpsc-export',
				'post_mime_type' => 'text/csv'
			);
			$post_ID = wp_insert_attachment( $object, $filename );
			if( $post_ID )
				$output = $post_ID;
		}
		return $output;

	}

	function wpsc_ce_save_csv_file_guid( $post_ID, $export_type, $upload_url ) {

		add_post_meta( $post_ID, '_wpsc_export_type', $export_type );
		$object = array(
			'ID' => $post_ID,
			'guid' => $upload_url
		);
		wp_update_post( $object );

	}

	function wpsc_ce_memory_prompt() {

		if( !wpsc_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = (int)( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				ob_start();
				$memory_url = add_query_arg( 'action', 'dismiss_memory_prompt' );
				$message = sprintf( __( 'We recommend setting memory to at least 64MB, your site has %dMB currently allocated. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'wpsc_ce' ), $memory_limit, 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ); ?>
<div class="error settings-error">
	<p>
		<strong><?php echo $message; ?></strong>
		<span style="float:right;"><a href="<?php echo $memory_url; ?>"><?php _e( 'Dismiss', 'wpsc_ce' ); ?></a></span>
	</p>
</div>
<?php
				ob_end_flush();
			}
		}

	}

	function wpsc_ce_fail_notices() {

		$message = false;
		if( isset( $_GET['failed'] ) )
			$message = __( 'A WordPress error caused the exporter to fail, please get in touch.', 'wpsc_ce' );
		if( isset( $_GET['empty'] ) )
			$message = __( 'No export entries were found, please try again with different export filters.', 'wpsc_ce' );
		if( $message ) {
			ob_start(); ?>
<div class="updated settings-error">
	<p>
		<strong><?php echo $message; ?></strong>
	</p>
</div>
<?php
			ob_end_flush();
		}
	}

	function wpsc_ce_get_archive_files() {

		$args = array(
			'post_type' => 'attachment',
			'post_mime_type' => 'text/csv',
			'meta_key' => '_wpsc_export_type',
			'meta_value' => null,
			'posts_per_page' => -1
		);
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( !empty( $filter ) )
				$args['meta_value'] = $filter;
		}
		$files = get_posts( $args );
		return $files;

	}

	function wpsc_ce_get_archive_file( $file = '' ) {

		$wp_upload_dir = wp_upload_dir();
		$file->export_type = get_post_meta( $file->ID, '_wpsc_export_type', true );
		$file->export_type_label = wpsc_ce_export_type_label( $file->export_type );
		if( empty( $file->export_type ) )
			$file->export_type = __( 'Unassigned', 'wpsc_ce' );
		if( empty( $file->guid ) )
			$file->guid = $wp_upload_dir['url'] . '/' . basename( $file->post_title );
		$file->post_mime_type = get_post_mime_type( $file->ID );
		if( !$file->post_mime_type )
			$file->post_mime_type = __( 'N/A', 'wpsc_ce' );
		$file->media_icon = wp_get_attachment_image( $file->ID, array( 80, 60 ), true );
		$author_name = get_user_by( 'id', $file->post_author );
		$file->post_author_name = $author_name->display_name;
		$t_time = strtotime( $file->post_date, current_time( 'timestamp' ) );
		$time = get_post_time( 'G', true, $file->ID, false );
		if( ( abs( $t_diff = time() - $time ) ) < 86400 )
			$file->post_date = sprintf( __( '%s ago' ), human_time_diff( $time ) );
		else
			$file->post_date = mysql2date( __( 'Y/m/d' ), $file->post_date );
		unset( $author_name, $t_time, $time );
		return $file;

	}

	function wpsc_ce_archives_quicklink_current( $current = '' ) {

		$output = '';
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( $filter == $current )
				$output = ' class="current"';
		} else if( $current == 'all' ) {
			$output = ' class="current"';
		}
		echo $output;

	}

	function wpsc_ce_archives_quicklink_count( $type = '' ) {

		$output = '0';
		$args = array(
			'post_type' => 'attachment',
			'meta_key' => '_wpsc_export_type',
			'meta_value' => null,
			'numberposts' => -1
		);
		if( $type )
			$args['meta_value'] = $type;
		$posts = get_posts( $args );
		if( $posts )
			$output = count( $posts );
		echo $output;

	}

	/* End of: WordPress Administration */

}

/* Start of: Common */

function wpsc_ce_get_option( $option = null, $default = false ) {

	global $wpsc_ce;

	$output = '';
	if( isset( $option ) ) {
		$separator = '_';
		$output = get_option( $wpsc_ce['prefix'] . $separator . $option, $default );
	}
	return $output;

}

function wpsc_ce_update_option( $option = null, $value = null ) {

	global $wpsc_ce;

	$output = false;
	if( isset( $option ) && isset( $value ) ) {
		$separator = '_';
		$output = update_option( $wpsc_ce['prefix'] . $separator . $option, $value );
	}
	return $output;

}

/* End of: Common */
?>