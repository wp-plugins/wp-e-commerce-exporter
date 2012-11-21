<?php
if( isset( $_POST['dataset'] ) )
	$dataset = $_POST['dataset'];
else
	$dataset = 'products';

$products = wpsc_ce_return_count( 'products' );
$categories = wpsc_ce_return_count( 'orders' );
$tags = wpsc_ce_return_count( 'tags' );
$sales = wpsc_ce_return_count( 'orders' );
$coupons = wpsc_ce_return_count( 'coupons' );
// $customers = wpsc_ce_return_count( 'customers' );

$product_fields = wpsc_ce_get_product_fields();
$sale_fields = wpsc_ce_get_sale_fields();
?>
<ul class="subsubsub">
	<li><a href="#export-type"><?php _e( 'Export Type', 'wpsc_ce' ); ?></a> |</li>
<?php if( $product_fields ) { ?>
	<li><a href="#export-products"><?php _e( 'Export: Products', 'wpsc_ce' ); ?></a> |</li>
<?php } ?>
<?php if( $sale_fields ) { ?>
	<li><a href="#export-sales"><?php _e( 'Export: Sales', 'wpsc_ce' ); ?></a> |</li>
<?php } ?>
	<!-- <li><a href="#export-coupons"><?php _e( 'Export: Coupons', 'wpsc_ce' ); ?></a> |</li> -->
	<li><a href="#export-options"><?php _e( 'Export Options', 'wpsc_ce' ); ?></a></li>
</ul>
<br class="clear" />
<h3><?php _e( 'Export Type', 'wpsc_ce' ); ?></h3>
<!--
<p><?php _e( 'When you click the Export button below Store Export will create a CSV file for you to save to your computer.', 'wpsc_ce' ); ?></p>
<p><?php _e( 'This formatted CSV file will contain the Product details from your Jigoshop store.', 'jigo_ce' ); ?></p>
<p><?php echo sprintf( __( 'Once you\'ve saved the download file, you can use <a href="%s"%s>Product Importer Deluxe</a> or <a href="%s"%s>Coupon Importer</a> to merge changes back into your store, or import store details into another WP e-Commerce instance.', 'wpsc_ce' ), $wpsc_pd_url, $wpsc_pd_target, $wpsc_ci_url, $wpsc_ci_target ); ?></p>
-->
<form method="post" onsubmit="showProgress()">
	<div id="poststuff">

		<div class="postbox" id="export-type">
			<h3 class="hndle"><?php _e( 'Export Type', 'wpsc_ce' ); ?></h3>
			<div class="inside">
				<table class="form-table">

					<tr>
						<th>
							<label for="products"><?php _e( 'Products', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="radio" id="products" name="dataset" value="products"<?php echo disabled( $products, 0 ) . checked( $dataset, 'products' ); ?> /> (<?php echo $products; ?>)
						</td>
					</tr>

					<tr>
						<th>
							<label for="categories"><?php _e( 'Categories', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="radio" id="categories" name="dataset" value="categories"<?php echo disabled( $categories, 0 ) . checked( $dataset, 'categories' ); ?> /> (<?php echo $categories; ?>)
						</td>
					</tr>

					<tr>
						<th>
							<label for="tags"><?php _e( 'Tags', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="radio" id="tags" name="dataset" value="tags"<?php echo disabled( $tags, 0 ) . checked( $dataset, 'tags' ); ?> /> (<?php echo $tags; ?>)
						</td>
					</tr>

					<tr>
						<th>
							<label for="sales"><?php _e( 'Sales', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="radio" id="sales" name="dataset" value="sales"<?php echo disabled( $sales, 0 ) . checked( $dataset, 'sales' ) ?>/> (<?php echo $sales; ?>)
						</td>
					</tr>

					<tr>
						<th>
							<label for="coupons"><?php _e( 'Coupons', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="radio" id="coupons" name="dataset" value="coupons"<?php echo disabled( $coupons, 0 ) . checked( $dataset, 'coupons' ); ?> /> (<?php echo $coupons; ?>)
						</td>
					</tr>

<!--
					<tr>
						<th>
							<label for="customers"><?php _e( 'Customers', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="radio" id="customers" name="dataset" value="customers"<?php echo disabled( $customers, 0 ) . checked( $dataset, 'customers' ); ?>/> (<?php echo $customers; ?>)
						</td>
					</tr>
-->

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Export', 'wpsc_ce' ); ?> " class="button-primary" />
				</p>
			</div>
		</div>
		<!-- .postbox -->

	</div>

	<h3><?php _e( 'Export: Products', 'wpsc_ce' ); ?></h3>
	<div id="poststuff">

<?php if( $product_fields ) { ?>
		<div class="postbox" id="export-products">
			<h3 class="hndle"><?php _e( 'Product Fields', 'wpsc_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Select the Product fields you would like to export.', 'wpsc_ce' ); ?></p>
				<!-- <p><a href="#"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="#"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p> -->
				<table>

	<?php foreach( $product_fields as $product_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="product_fields[<?php echo $product_field['name']; ?>]" class="product_field"<?php checked( $product_field['default'], 1 ); ?> />
								<?php echo $product_field['label']; ?>
							</label>
						</td>
					</tr>

	<?php } ?>
				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Export Products', 'wpsc_ce' ); ?> " class="button-primary" />
				</p>
			</div>
		</div>
		<!-- .postbox -->

<?php } ?>

	</div>

<?php if( $sale_fields ) { ?>
	<h3><?php _e( 'Export: Sales', 'wpsc_ce' ); ?></h3>
	<div id="poststuff">

		<div class="postbox" id="export-sales">
			<h3 class="hndle"><?php _e( 'Sale Fields', 'wpsc_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Select the Sale fields you would like to export.', 'wpsc_ce' ); ?></p>
				<!-- <p><a href="#"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="#"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p> -->
				<table>

	<?php foreach( $sale_fields as $sale_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="sale_fields[<?php echo $sale_field['name']; ?>]" class="sale_field"<?php checked( $sale_field['default'], 1 ); ?> />
								<?php echo $sale_field['label']; ?>
							</label>
						</td>
					</tr>

	<?php } ?>
				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Export Sales', 'wpsc_ce' ); ?> " class="button-primary" />
				</p>
			</div>
		</div>
		<!-- .postbox -->

	</div>

<?php } ?>

	<h3><?php _e( 'Export Options', 'wpsc_ce' ); ?></h3>
	<div id="poststuff">

		<div class="postbox" id="export-options">
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
								<option value="600"><?php echo sprintf( __( '%s minutes', 'wpsc_ce' ), 10 ); ?></option>
								<option value="1800"><?php echo sprintf( __( '%s minutes', 'wpsc_ce' ), 30 ); ?></option>
								<option value="3600"><?php echo sprintf( __( '%s hour', 'wpsc_ce' ), 1 ); ?></option>
								<option value="0" selected="selected"><?php _e( 'Unlimited', 'wpsc_ce' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Script timeout defines how long WP e-Commerce Exporter is \'allowed\' to process your CSV file, once the time limit is reached the export process halts.', 'wpsc_ce' ); ?></p>
						</td>
					</tr>
<?php } ?>
				</table>
			</div>
		</div>
		<!-- .postbox -->

	</div>
	<input type="hidden" name="action" value="export" />
</form>