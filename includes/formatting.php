<?php
function wpsc_ce_format_gpf_availability( $availability ) {

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
function wpsc_ce_format_product_status( $product_status ) {

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
	return $output;

}

function wpsc_ce_format_comment_status( $comment_status ) {

	$output = $comment_status;
	switch( $comment_status ) {

		case 'open':
			$output = __( 'Open', 'wpsc_ce' );
			break;

		case 'closed':
			$output = __( 'Closed', 'wpsc_ce' );
			break;

	}
	return $output;

}
?>