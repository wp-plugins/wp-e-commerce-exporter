<?php
if( function_exists( 'wpsc_pd_init' ) ) {
	$wpsc_pd_url = add_query_arg( 'page', 'wpsc_pd' );
	$wpsc_pd_target = false;
} else {
	$wpsc_pd_url = 'http://www.visser.com.au/wp-ecommerce/plugins/product-importer-deluxe/';
	$wpsc_pd_target = ' target="_blank"';
}
if( function_exists( 'wpsc_ci_init' ) ) {
	$wpsc_ci_url = add_query_arg( 'page', 'wpsc_ci' );
	$wpsc_ci_target = false;
} else {
	$wpsc_ci_url = 'http://www.visser.com.au/wp-ecommerce/plugins/coupon-importer-deluxe/';
	$wpsc_ci_target = ' target="_blank"';
}
?>
<h3><?php _e( 'WP e-Commerce Tools', 'wpsc_ce' ); ?></h3>
<div id="poststuff">

	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Tools', 'wpsc_pd' ); ?></h3>
		<div class="inside">
			<table class="form-table">

				<tr>
					<td>
						<a href="<?php echo $wpsc_pd_url; ?>"<?php echo $wpsc_pd_target; ?>><?php _e( 'Import Products from CSV', 'wpsc_ce' ); ?></a>
						<p class="description"><?php _e( 'Use Product Importer Deluxe to import Product changes back into your store.', 'wpsc_ce' ); ?></p>
					</td>
				</tr>

				<tr>
					<td>
						<a href="<?php echo $wpsc_ci_url; ?>"><?php _e( 'Import Coupons from CSV', 'wpsc_ce' ); ?></a>
						<p class="description"><?php _e( 'Import Coupon details into WP e-Commerce from your CSV-formatted file.', 'wpsc_ce' ); ?></p>
					</td>
				</tr>

			</table>
		</div>
	</div>
	<!-- .postbox -->

</div>
<!-- #poststuff -->