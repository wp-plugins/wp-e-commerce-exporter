<?php
function wpsc_ce_clean_html( $content ) {

/*
	if( function_exists( 'mb_convert_encoding' ) ) {
		$output_encoding = 'ISO-8859-1';
		$content = mb_convert_encoding( trim( $content ), 'UTF-8', $output_encoding );
	} else {
		$content = trim( $content );
	}
	$data = str_replace( ',', '&#44;', $content );
	$data = str_replace( "\n", '<br />', $content );
*/
	return $content;

}

if( !function_exists( 'escape_csv_value' ) ) {
	function escape_csv_value( $value ) {

		$value = str_replace( '"', '""', $value ); // First off escape all " and make them ""
		$value = str_replace( PHP_EOL, ' ', $value );
		return '"' . $value . '"'; // If I have new lines or commas escape them

	}
}

function wpsc_ce_escape_csv_value( $value = '', $delimiter = ',', $format = 'all' ) {

	$output = $value;
	if( !empty( $output ) ) {
		$output = str_replace( '"', '""', $output );
		//$output = str_replace( PHP_EOL, ' ', $output );
		$output = str_replace( PHP_EOL, "\r\n", $output );
		switch( $format ) {
	
			case 'all':
				$output = '"' . $output . '"';
				break;
	
			case 'excel':
				if( strstr( $output, $delimiter ) !== false || strstr( $output, "\r\n" ) !== false )
					$output = '"' . $output . '"';
				break;
	
		}
	}
	return $output;

}

function wpsc_ce_format_product_status( $product_status, $product ) {

	$output = $product_status;
	switch( $product_status ) {

		case 'publish':
			$output = __( 'Publish', 'wpsc_ce' );
			break;

		case 'draft':
			$output = __( 'Draft', 'wpsc_ce' );
			break;

		case 'trash':
			$output = __( 'Trash', 'wpsc_ce' );
			break;

	}
	if( $product->is_variation && $product_status <> 'draft' )
		$output = '';
	return $output;

}

function wpsc_ce_format_comment_status( $comment_status, $product ) {

	$output = $comment_status;
	switch( $comment_status ) {

		case 'open':
			$output = __( 'Open', 'wpsc_ce' );
			break;

		case 'closed':
			$output = __( 'Closed', 'wpsc_ce' );
			break;

	}
	if( $product->is_variation )
		$output = '';
	return $output;

}

function wpsc_ce_format_tax_bands( $tax_band_id ) {

	if( !empty( $tax_band_id ) ) {
		$tax_bands = get_option( 'wpec_taxes_bands', true );
		print_r( $tax_bands );
	}

}

function wpsc_ce_format_gpf_availability( $availability = null ) {

	$output = '';
	if( $availability ) {
		switch( $availability ) {

			case 'in stock':
				$output = __( 'In Stock', 'wpsc_ce' );
				break;

			case 'available for order':
				$output = __( 'Available For Order', 'wpsc_ce' );
				break;

			case 'preorder':
				$output = __( 'Pre-order', 'wpsc_ce' );
				break;

		}
	}
	return $output;

}

function wpsc_ce_format_gpf_condition( $condition ) {

	switch( $condition ) {

		case 'new':
			$output = __( 'New', 'wpsc_ce' );
			break;

		case 'refurbished':
			$output = __( 'Refurbished', 'wpsc_ce' );
			break;

		case 'used':
			$output = __( 'Used', 'wpsc_ce' );
			break;

	}
	return $output;

}

function wpsc_ce_format_product_filters( $product_filters = array() ) {

	$output = array();
	if( !empty( $product_filters ) ) {
		foreach( $product_filters as $product_filter ) {
			$output[] = $product_filter;
		}
	}
	return $output;

}

function wpsc_ce_format_order_date( $date ) {

	$output = $date;
	if( $date )
		$output = str_replace( '/', '-', $date );
	return $output;

}

function wpsc_ce_expand_country_name( $country_prefix = '' ) {

	global $wpdb;

	$output = $country_prefix;
	if( $country_prefix ) {
		$country_sql = $wpdb->prepare( "SELECT `country` FROM `" . $wpdb->prefix . "wpsc_currency_list` WHERE `isocode` = '%s' LIMIT 1", $country_prefix );
		$country = $wpdb->get_var( $country_sql );
		if( $country )
			$output = $country;
	}
	return $output;

}
?>