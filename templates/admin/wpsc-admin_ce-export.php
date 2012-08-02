<script type="text/javascript">
	function showProgress() {
		window.scrollTo(0,0);
		document.getElementById('progress').style.display = 'block';
		document.getElementById('content').style.display = 'none';
	}
</script>
<div id="content">
	<h3><?php _e( 'Export to CSV', 'wpsc_ce' ); ?></h3>
	<p><?php _e( 'When you click the Export button below Store Export will create a CSV file for you to save to your computer.', 'wpsc_ce' ); ?></p>
	<p><?php _e( 'This formatted CSV file will contain the Product details from your Jigoshop store.', 'jigo_ce' ); ?></p>
	<p><?php _e( 'Once you\'ve saved the download file, you can use <a href="' . $wpsc_pd_url . '">Product Importer Deluxe</a> or <a href="' . $wpsc_ci_url .'">Coupon Importer</a> to merge changes back into your store, or import store details into another WP e-Commerce instance.', 'wpsc_ce' ); ?></p>
	<form method="post" onsubmit="showProgress()">
		<div id="poststuff">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Export WP e-Commerce Details', 'wpsc_ce' ); ?></h3>
				<div class="inside">
					<table class="form-table">

						<tr>
							<th>
								<label for="products"><?php _e( 'Products', 'wpsc_ce' ); ?></label>
							</th>
							<td>
								<input type="radio" id="products" name="dataset" value="products"<?php if( $products == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $products; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="categories"><?php _e( 'Categories', 'wpsc_ce' ); ?></label>
							</th>
							<td>
								<input type="radio" id="categories" name="dataset" value="categories"<?php if( $categories == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $categories; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="tags"><?php _e( 'Tags', 'wpsc_ce' ); ?></label>
							</th>
							<td>
								<input type="radio" id="tags" name="dataset" value="tags"<?php if( $tags == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $tags; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="coupons"><?php _e( 'Coupons', 'wpsc_ce' ); ?></label>
							</th>
							<td>
								<input type="radio" id="coupons" name="dataset" value="coupons"<?php if( $coupons == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $coupons; ?>)
							</td>
						</tr>

					</table>
				</div>
			</div>
			<!-- .postbox -->

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Export Options', 'wpsc_ce' ); ?></h3>
				<div class="inside">
					<table class="form-table">

						<tr>
							<th>
								<label for="delimiter"><?php _e( 'Field delimiter', 'wpsc_ce' ); ?></label>
							</th>
							<td>
								<input type="text" size="3" id="delimiter" name="delimiter" value="," size="1" class="text" />
								<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'wpsc_pc' ); ?></p>
							</td>
						</tr>

						<tr>
							<th>
								<label for="category_separator"><?php _e( 'Category separator', 'wpsc_ce' ); ?></label>
							</th>
							<td>
								<input type="text" size="3" id="category_separator" name="category_separator" value="|" size="1" class="text" />
								<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'wpsc_ce' ); ?></p>
							</td>
						</tr>

<?php if( !ini_get( 'safe_mode' ) ) { ?>
						<tr>
							<th>
								<label for="timeout"><?php _e( 'Script timeout', 'wpsc_ce' ); ?>: </label>
							</th>
							<td>
								<select id="timeout" name="timeout">
									<option value="600" selected="selected">10 <?php _e( 'minutes', 'wpsc_ce' ); ?>&nbsp;</option>
									<option value="1800">30 <?php _e( 'minutes', 'wpsc_ce' ); ?>&nbsp;</option>
									<option value="3600">1 <?php _e( 'hour', 'wpsc_ce' ); ?>&nbsp;</option>
									<option value="0" selected="selected"><?php _e( 'Unlimited', 'wpsc_ce' ); ?>&nbsp;</option>
								</select>
								<p class="description"><?php _e( 'Script timeout defines how long WP e-Commerce Exporter is \'allowed\' to process your CSV file, once the time limit is reached the export process halts.', 'wpsc_ce' ); ?></p>
							</td>
						</tr>
<?php } ?>
					</table>
				</div>
			</div>
			<!-- .postbox -->

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
		<p class="submit">
			<input type="submit" value="<?php _e( 'Export', 'wpsc_ce' ); ?> " class="button-primary" />
		</p>
		<input type="hidden" name="action" value="export" />
	</form>
</div>
<div id="progress" style="display:none;">
	<p><?php _e( 'Chosen WP e-Commerce details are being exported, this process can take awhile. Time for a beer?', 'wpsc_ce' ); ?></p>
	<img src="<?php echo plugins_url( '/templates/admin/images/progress.gif', $wpsc_ce['relpath'] ); ?>" alt="" />
	<p><?php _e( 'Return to <a href="' . $url . '">WP e-Commerce Exporter</a>.', 'wpsc_ce' ); ?>
</div>