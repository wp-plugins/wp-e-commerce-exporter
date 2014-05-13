<?php
// Function to generate filename of CSV file based on the Export type
function wpsc_ce_generate_csv_filename( $dataset = '' ) {

	// Get the filename from WordPress options
	$filename = wpsc_ce_get_option( 'export_filename', 'wpsc-export_%dataset%-%date%.csv' );

	// Populate the available tags
	$date = date( 'Y_m_d' );
	$time = date( 'H_i_s' );
	$store_name = sanitize_title( get_bloginfo( 'name' ) );

	// Switch out the tags for filled values
	$filename = str_replace( '%dataset%', $dataset, $filename );
	$filename = str_replace( '%date%', $date, $filename );
	$filename = str_replace( '%time%', $time, $filename );
	$filename = str_replace( '%store_name%', $store_name, $filename );

	// Return the filename
	return $filename;

}

// File output header for CSV file
function wpsc_ce_generate_csv_header( $dataset = '' ) {

	global $export;

	if( $filename = wpsc_ce_generate_csv_filename( $dataset ) ) {
		header( sprintf( 'Content-Encoding: %s', $export->encoding ) );
		header( sprintf( 'Content-Type: text/csv; charset=%s', $export->encoding ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( sprintf( 'Content-Disposition: attachment; filename=%s', $filename ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

}
?>