<?php
function wpsc_ce_clean_html( $content ) {

/*
	if( function_exists( 'mb_convert_encoding' ) ) {
		$output_encoding = 'ISO-8859-1';
		$data = mb_convert_encoding( trim( $data ), 'UTF-8', $output_encoding );
	} else {
		$data = trim( $data );
	}
	$data = str_replace( ',', '&#44;', $data );
	$data = str_replace( "\n", '<br />', $data );
*/
	return $content;

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
?>