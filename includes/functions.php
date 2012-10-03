<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_ce_template_header() {

		global $wpsc_ce; ?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
	<h2><?php echo $wpsc_ce['menu']; ?></h2>
<?php
	}

	function wpsc_ce_template_footer() { ?>
</div>
<?php
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

	function wpsc_ce_format_product_status( $product_status ) {

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

	/* End of: WordPress Administration */

}
?>