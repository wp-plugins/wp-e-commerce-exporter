<ul class="subsubsub">
	<li><a href="#export-type"><?php _e( 'Export Type', 'wpsc_ce' ); ?></a> |</li>
	<li><a href="#export-options"><?php _e( 'Export Options', 'wpsc_ce' ); ?></a></li>
	<?php do_action( 'wpsc_ce_export_quicklinks' ); ?>
</ul>
<br class="clear" />
<p><?php _e( 'Select an export type from the list below to export entries. Once you have selected an export type you may select the fields you would like to export and optional filters available for each export type. When you click the export button below, Store Exporter will create a CSV file for you to save to your computer.', 'wpsc_ce' ); ?></p>
<form method="post" action="<?php echo add_query_arg( array( 'failed' => null, 'empty' => null ) ); ?>" id="postform">
	<div id="poststuff">

		<div class="postbox" id="export-type">
			<h3 class="hndle"><?php _e( 'Export Type', 'wpsc_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Select the data type you want to export.', 'wpsc_ce' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<input type="radio" id="products" name="dataset" value="products"<?php disabled( $products, 0 ); ?><?php checked( $dataset, 'products' ); ?> />
							<label for="products"><?php _e( 'Products', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $products; ?>)</span>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="categories" name="dataset" value="categories"<?php disabled( $categories, 0 ); ?><?php checked( $dataset, 'categories' ); ?> />
							<label for="categories"><?php _e( 'Categories', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $categories; ?>)</span>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="tags" name="dataset" value="tags"<?php disabled( $tags, 0 ); ?><?php checked( $dataset, 'tags' ); ?> />
							<label for="tags"><?php _e( 'Tags', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $tags; ?>)</span>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="orders" name="dataset" value="orders"<?php disabled( $orders, 0 ); ?><?php checked( $dataset, 'orders' ); ?>/>
							<label for="orders"><?php _e( 'Orders', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $orders; ?>)</span>
<?php if( !function_exists( 'wpsc_cd_admin_init' ) ) { ?>
							<span class="description"> - <?php echo sprintf( __( 'available in %s', 'wpsc_ce' ), $wpsc_cd_link ); ?></span>
<?php } ?>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="customers" name="dataset" value="customers"<?php disabled( $customers, 0 ); ?><?php checked( $dataset, 'customers' ); ?>/>
							<label for="customers"><?php _e( 'Customers', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $customers; ?>)</span>
<?php if( !function_exists( 'wpsc_cd_admin_init' ) ) { ?>
							<span class="description"> - <?php echo sprintf( __( 'available in %s', 'wpsc_ce' ), $wpsc_cd_link ); ?></span>
<?php } ?>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="coupons" name="dataset" value="coupons"<?php disabled( $coupons, 0 ); ?><?php checked( $dataset, 'coupons' ); ?> />
							<label for="coupons"><?php _e( 'Coupons', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $coupons; ?>)</span>
<?php if( !function_exists( 'wpsc_cd_admin_init' ) ) { ?>
							<span class="description"> - <?php echo sprintf( __( 'available in %s', 'wpsc_ce' ), $wpsc_cd_link ); ?></span>
<?php } ?>
						</td>
					</tr>

				</table>
<!--
				<p class="submit">
					<input type="submit" value="<?php _e( 'Export', 'wpsc_ce' ); ?>" class="button-primary" />
				</p>
-->
			</div>
		</div>
		<!-- .postbox -->

<?php if( $product_fields ) { ?>
		<div id="export-products">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Product Fields', 'wpsc_ce' ); ?></h3>
				<div class="inside">
	<?php if( $products ) { ?>
					<p class="description"><?php _e( 'Select the Product fields you would like to export, your field selection is saved for future exports.', 'wpsc_ce' ); ?></p>
					<p><a href="javascript:void(0)" id="products-checkall" class="checkall"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="javascript:void(0)" id="products-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p>
					<table>

		<?php foreach( $product_fields as $product_field ) { ?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="product_fields[<?php echo $product_field['name']; ?>]" class="product_field"<?php checked( $product_field['default'], 1 ); ?><?php disabled( $product_field['disabled'], 1 ); ?> />
									<?php echo $product_field['label']; ?>
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_products" value="<?php _e( 'Export Products', 'wpsc_ce' ); ?> " class="button-primary" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Product field in the above export list?', 'wpsc_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'wpsc_ce' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Products have been found.', 'wpsc_ce' ); ?></p>
	<?php } ?>
				</div>
			</div>
			<!-- .postbox -->

			<div id="export-products-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Product Filters', 'wpsc_ce' ); ?></h3>
				<div class="inside">

					<p><label><input type="checkbox" id="products-filters-categories" /> <?php _e( 'Filter Products by Product Categories', 'wpsc_ce' ); ?></label></p>
					<div id="export-products-filters-categories" class="separator">
<?php if( $product_categories ) { ?>
						<ul>
	<?php foreach( $product_categories as $product_category ) { ?>
							<li><label><input type="checkbox" name="product_filter_categories[<?php echo $product_category->term_id; ?>]" value="<?php echo $product_category->term_id; ?>" /> <?php echo $product_category->name; ?> (#<?php echo $product_category->term_id; ?>)</label></li>
	<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Products by. Default is to include all Product Categories.', 'wpsc_ce' ); ?></p>
<?php } else { ?>
						<p><?php _e( 'No Product Categories have been found.', 'wpsc_ce' ); ?></p>
<?php } ?>
					</div>
					<!-- #export-products-filters-categories -->

					<p><label><input type="checkbox" id="products-filters-tags" /> <?php _e( 'Filter Products by Product Tags', 'wpsc_ce' ); ?></label></p>
					<div id="export-products-filters-tags" class="separator">
<?php if( $product_tags ) { ?>
						<ul>
	<?php foreach( $product_tags as $product_tag ) { ?>
							<li><label><input type="checkbox" name="product_filter_tags[<?php echo $product_tag->term_id; ?>]" value="<?php echo $product_tag->term_id; ?>" /> <?php echo $product_tag->name; ?> (#<?php echo $product_tag->term_id; ?>)</label></li>
	<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Products by. Default is to include all Product Tags.', 'wpsc_ce' ); ?></p>
<?php } else { ?>
						<p><?php _e( 'No Product Tags have been found.', 'wpsc_ce' ); ?></p>
<?php } ?>
					</div>
					<!-- #export-products-filters-tags -->

					<p><label><input type="checkbox" id="products-filters-status" /> <?php _e( 'Filter Products by Product Status', 'wpsc_ce' ); ?></label></p>
					<div id="export-products-filters-status" class="separator">
						<ul>
<?php foreach( $product_statuses as $key => $product_status ) { ?>
							<li><label><input type="checkbox" name="product_filter_status[<?php echo $key; ?>]" value="<?php echo $key; ?>" /> <?php echo $product_status; ?></label></li>
<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Status options you want to filter exported Products by. Default is to include all Product Status options.', 'wpsc_ce' ); ?></p>
					</div>
					<!-- #export-products-filters-status -->

					<p><label><?php _e( 'Product Sorting', 'wpsc_ce' ); ?></label></p>
					<div>
						<select name="product_orderby">
							<option value="ID"<?php selected( 'ID', $product_orderby ); ?>><?php _e( 'Product ID', 'wpsc_ce' ); ?></option>
							<option value="title"<?php selected( 'title', $product_orderby ); ?>><?php _e( 'Product Name', 'wpsc_ce' ); ?></option>
							<option value="date"<?php selected( 'date', $product_orderby ); ?>><?php _e( 'Date Created', 'wpsc_ce' ); ?></option>
							<option value="modified"<?php selected( 'modified', $product_orderby ); ?>><?php _e( 'Date Modified', 'wpsc_ce' ); ?></option>
							<option value="rand"<?php selected( 'rand', $product_orderby ); ?>><?php _e( 'Random', 'wpsc_ce' ); ?></option>
							<option value="menu_order"<?php selected( 'menu_order', $product_orderby ); ?>><?php _e( 'Menu Order', 'wpsc_ce' ); ?></option>
						</select>
						<select name="product_order">
							<option value="ASC"<?php selected( 'ASC', $product_order ); ?>><?php _e( 'Ascending', 'wpsc_ce' ); ?></option>
							<option value="DESC"<?php selected( 'DESC', $product_order ); ?>><?php _e( 'Descending', 'wpsc_ce' ); ?></option>
						</select>
						<p class="description"><?php _e( 'Select the sorting of Products within the exported file. By default this is set to export Products by Product ID in Desending order.', 'wpsc_ce' ); ?></p>
					</div>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-products -->

<?php } ?>
		<div id="export-categories">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Category Fields', 'wpsc_ce' ); ?></h3>
				<div class="inside">
					<p class="description"><?php _e( 'Select the Category fields you would like to export.', 'wpsc_ce' ); ?></p>
					<p><a href="javascript:void(0)" id="categories-checkall" class="checkall"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="javascript:void(0)" id="categories-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p>
					<table>

<?php foreach( $category_fields as $category_field ) { ?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="category_fields[<?php echo $category_field['name']; ?>]" class="category_field"<?php checked( $category_field['default'], 1 ); ?><?php disabled( $category_field['disabled'], 1 ); ?> />
									<?php echo $category_field['label']; ?>
								</label>
							</td>
						</tr>

<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_categories" value="<?php _e( 'Export Categories', 'wpsc_ce' ); ?> " class="button-primary" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Category field in the above export list?', 'wpsc_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'wpsc_ce' ); ?></a>.</p>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-categories-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Category Filters', 'wpsc_ce' ); ?></h3>
				<div class="inside">

					<p><label><?php _e( 'Category Sorting', 'wpsc_ce' ); ?></label></p>
					<div>
						<select name="category_orderby">
							<option value="id"<?php selected( 'id', $category_orderby ); ?>><?php _e( 'Term ID', 'wpsc_ce' ); ?></option>
							<option value="name"<?php selected( 'name', $category_orderby ); ?>><?php _e( 'Category Name', 'wpsc_ce' ); ?></option>
						</select>
						<select name="category_order">
							<option value="ASC"<?php selected( 'ASC', $category_order ); ?>><?php _e( 'Ascending', 'wpsc_ce' ); ?></option>
							<option value="DESC"<?php selected( 'DESC', $category_order ); ?>><?php _e( 'Descending', 'wpsc_ce' ); ?></option>
						</select>
						<p class="description"><?php _e( 'Select the sorting of Categories within the exported file. By default this is set to export Categories by Term ID in Desending order.', 'wpsc_ce' ); ?></p>
					</div>

				</div>
				<!-- .inside -->
			</div>
			<!-- #export-categories-filters -->

		</div>
		<!-- #export-categories -->

		<div id="export-tags">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Tag Fields', 'wpsc_ce' ); ?></h3>
				<div class="inside">
					<p class="description"><?php _e( 'Select the Tag fields you would like to export.', 'wpsc_ce' ); ?></p>
					<p><a href="javascript:void(0)" id="tags-checkall" class="checkall"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="javascript:void(0)" id="tags-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p>
					<table>

<?php foreach( $tag_fields as $tag_field ) { ?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="tag_fields[<?php echo $tag_field['name']; ?>]" class="tag_field"<?php checked( $tag_field['default'], 1 ); ?><?php disabled( $tag_field['disabled'], 1 ); ?> />
									<?php echo $tag_field['label']; ?>
								</label>
							</td>
						</tr>

<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_tags" value="<?php _e( 'Export Tags', 'wpsc_ce' ); ?> " class="button-primary" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Tag field in the above export list?', 'wpsc_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'wpsc_ce' ); ?></a>.</p>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-tags-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Tag Filters', 'wpsc_ce' ); ?></h3>
				<div class="inside">

					<p><label><?php _e( 'Tag Sorting', 'wpsc_ce' ); ?></label></p>
					<div>
						<select name="tag_orderby">
							<option value="id"<?php selected( 'id', $tag_orderby ); ?>><?php _e( 'Term ID', 'wpsc_ce' ); ?></option>
							<option value="name"<?php selected( 'name', $tag_orderby ); ?>><?php _e( 'Tag Name', 'wpsc_ce' ); ?></option>
						</select>
						<select name="tag_order">
							<option value="ASC"<?php selected( 'ASC', $tag_order ); ?>><?php _e( 'Ascending', 'wpsc_ce' ); ?></option>
							<option value="DESC"<?php selected( 'DESC', $tag_order ); ?>><?php _e( 'Descending', 'wpsc_ce' ); ?></option>
						</select>
						<p class="description"><?php _e( 'Select the sorting of Tags within the exported file. By default this is set to export Tags by Term ID in Desending order.', 'wpsc_ce' ); ?></p>
					</div>

				</div>
				<!-- .inside -->
			</div>
			<!-- #export-tags-filters -->

		</div>
		<!-- #export-tags -->

<?php if( $order_fields ) { ?>
		<div id="export-orders">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Order Fields', 'wpsc_ce' ); ?></h3>
				<div class="inside">

	<?php if( $orders ) { ?>
					<p class="description"><?php _e( 'Select the Order fields you would like to export.', 'wpsc_ce' ); ?></p>
					<p><a href="javascript:void(0)" id="orders-checkall" class="checkall"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="javascript:void(0)" id="orders-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p>
					<table>

		<?php foreach( $order_fields as $order_field ) { ?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="order_fields[<?php echo $order_field['name']; ?>]" class="order_field"<?php checked( $order_field['default'], 1 ); ?><?php disabled( $wpsc_cd_exists, false ); ?> />
									<?php echo $order_field['label']; ?>
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
		<?php if( function_exists( 'wpsc_cd_admin_init' ) ) { ?>
						<input type="submit" id="export_orders" value="<?php _e( 'Export Orders', 'wpsc_ce' ); ?> " class="button-primary" />
		<?php } else { ?>
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Orders', 'wpsc_ce' ); ?>" />
		<?php } ?>
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Order field in the above export list?', 'wpsc_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'wpsc_ce' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Orders have been found.', 'wpsc_ce' ); ?></p>
	<?php } ?>

				</div>
			</div>
			<!-- .postbox -->

			<div id="export-orders-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Order Filters', 'wpsc_ce' ); ?></h3>
				<div class="inside">

					<?php do_action( 'wpsc_ce_export_order_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'wpsc_ce_export_order_options_table' ); ?>
					</table>

					<?php do_action( 'wpsc_ce_export_order_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-orders -->

<?php } ?>
<?php if( $customer_fields ) { ?>
		<div class="postbox" id="export-customers">
			<h3 class="hndle"><?php _e( 'Customer Fields', 'wpsc_ce' ); ?></h3>
			<div class="inside">
	<?php if( $customers ) { ?>
				<p class="description"><?php _e( 'Select the Customer fields you would like to export.', 'wpsc_ce' ); ?></p>
				<p><a href="javascript:void(0)" id="customers-checkall" class="checkall"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="javascript:void(0)" id="customers-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p>
				<table>

		<?php foreach( $customer_fields as $customer_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="customer_fields[<?php echo $customer_field['name']; ?>]" class="customer_field"<?php checked( $customer_field['default'], 1 ); ?><?php disabled( $wpsc_cd_exists, false ); ?> />
								<?php echo $customer_field['label']; ?>
							</label>
						</td>
					</tr>

		<?php } ?>
				</table>
				<p class="submit">
		<?php if( function_exists( 'wpsc_cd_admin_init' ) ) { ?>
					<input type="submit" id="export_customers" value="<?php _e( 'Export Customers', 'wpsc_ce' ); ?> " class="button-primary" />
		<?php } else { ?>
					<input type="button" class="button button-disabled" value="<?php _e( 'Export Customers', 'wpsc_ce' ); ?>" />
		<?php } ?>
				</p>
					<p class="description"><?php _e( 'Can\'t find a particular Customer field in the above export list?', 'wpsc_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'wpsc_ce' ); ?></a>.</p>
	<?php } else { ?>
				<p><?php _e( 'No Customers have been found.', 'wpsc_ce' ); ?></p>
	<?php } ?>
			</div>
		</div>
		<!-- .postbox -->

<?php } ?>
<?php if( $coupon_fields ) { ?>
		<div class="postbox" id="export-coupons">
			<h3 class="hndle"><?php _e( 'Coupon Fields', 'wpsc_ce' ); ?></h3>
			<div class="inside">
	<?php if( $coupons ) { ?>
				<p class="description"><?php _e( 'Select the Coupon fields you would like to export.', 'wpsc_ce' ); ?></p>
				<p><a href="javascript:void(0)" id="coupons-checkall" class="checkall"><?php _e( 'Check All', 'wpsc_ce' ); ?></a> | <a href="javascript:void(0)" id="coupons-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'wpsc_ce' ); ?></a></p>
				<table>

		<?php foreach( $coupon_fields as $coupon_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="coupon_fields[<?php echo $coupon_field['name']; ?>]" class="coupon_field"<?php checked( $coupon_field['default'], 1 ); ?><?php disabled( $wpsc_cd_exists, false ); ?> />
								<?php echo $coupon_field['label']; ?>
							</label>
						</td>
					</tr>

		<?php } ?>
				</table>
				<p class="submit">
		<?php if( function_exists( 'wpsc_cd_admin_init' ) ) { ?>
					<input type="submit" id="export_coupons" value="<?php _e( 'Export Coupons', 'wpsc_ce' ); ?> " class="button-primary" />
		<?php } else { ?>
					<input type="button" class="button button-disabled" value="<?php _e( 'Export Coupons', 'wpsc_ce' ); ?>" />
		<?php } ?>
				</p>
				<p class="description"><?php _e( 'Can\'t find a particular Coupon field in the above export list?', 'wpsc_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'wpsc_ce' ); ?></a>.</p>
	<?php } else { ?>
				<p><?php _e( 'No Coupons have been found.', 'wpsc_ce' ); ?></p>
	<?php } ?>
			</div>
		</div>
		<!-- .postbox -->

<?php } ?>
		<div class="postbox" id="export-options">
			<h3 class="hndle"><?php _e( 'Export Options', 'wpsc_ce' ); ?></h3>
			<div class="inside">
				<table class="form-table">

					<?php do_action( 'wpsc_ce_export_options' ); ?>

					<tr>
						<th>
							<label for="delimiter"><?php _e( 'Field delimiter', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="delimiter" name="delimiter" value="<?php echo $delimiter; ?>" size="1" maxlength="1" class="text" />
							<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'wpsc_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="category_separator"><?php _e( 'Category separator', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="category_separator" name="category_separator" value="<?php echo $category_separator; ?>" size="1" class="text" />
							<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'wpsc_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="escape_formatting"><?php _e( 'Field escape formatting', 'wpsc_ce' ); ?>: </label>
						</th>
						<td>
							<label><input type="radio" name="escape_formatting" value="all"<?php checked( $escape_formatting, 'all' ); ?> />&nbsp;<?php _e( 'Escape all fields', 'wpsc_ce' ); ?></label><br />
							<label><input type="radio" name="escape_formatting" value="excel"<?php checked( $escape_formatting, 'excel' ); ?> />&nbsp;<?php _e( 'Escape fields as Excel would', 'wpsc_ce' ); ?></label>
							<p class="description"><?php _e( 'Choose the field escape format that suits your spreadsheet software (e.g. Excel).', 'wpsc_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="offset"><?php _e( 'Volume offset', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="offset" name="offset" value="<?php echo $offset; ?>" size="5" class="text" />
							<p class="description"><?php _e( 'Volume offset allows for partial exporting of a dataset, to be used in conjuction with Limit volme option above. By default this is not used and is left empty.', 'wps_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="limit_volume"><?php _e( 'Limit volume', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="limit_volume" name="limit_volume" value="<?php echo $limit_volume; ?>" size="5" class="text" />
							<p class="description"><?php _e( 'Limit volume allows for partial exporting of a dataset. This is useful when encountering timeout and/or memory errors during the default export. By default this is not used and is left empty.', 'wpsc_pc' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="bom"><?php _e( 'Add BOM character', 'wpsc_ce' ); ?>: </label>
						</th>
						<td>
							<select id="bom" name="bom">
								<option value="1"<?php selected( $bom, 1 ); ?>><?php _e( 'Yes', 'wpsc_ce' ); ?></option>
								<option value="0"<?php selected( $bom, 0 ); ?>><?php _e( 'No', 'wpsc_ce' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Mark the CSV file as UTF8 by adding a byte order mark (BOM) to the export, useful for non-English character sets.', 'wpsc_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="encoding"><?php _e( 'Character encoding', 'wpsc_ce' ); ?>: </label>
						</th>
						<td>
<?php if( $file_encodings ) { ?>
							<select id="encoding" name="encoding">
								<option value=""><?php _e( 'System default', 'wpsc_ce' ); ?></option>
	<?php foreach( $file_encodings as $key => $chr ) { ?>
								<option value="<?php echo $chr; ?>"<?php selected( $chr, $encoding ); ?>><?php echo $chr; ?></option>
	<?php } ?>
							</select>
<?php } else { ?>
							<p class="description"><?php _e( 'Character encoding options are unavailable in PHP 4, contact your hosting provider to update your site install to use PHP 5 or higher.', 'wpsc_ce' ); ?></p>
<?php } ?>
						</td>
					</tr>

					<tr>
						<th>
							<label for="delete_temporary_csv"><?php _e( 'Delete temporary CSV after export', 'wpsc_ce' ); ?></label>
						</th>
						<td>
							<select id="delete_temporary_csv" name="delete_temporary_csv">
								<option value="1"<?php selected( $delete_csv, 1 ); ?>><?php _e( 'Yes', 'wpsc_ce' ); ?></option>
								<option value="0"<?php selected( $delete_csv, 0 ); ?>><?php _e( 'No', 'wpsc_ce' ); ?></option>
							</select>
						</td>
					</tr>

<?php if( !ini_get( 'safe_mode' ) ) { ?>
					<tr>
						<th>
							<label for="timeout"><?php _e( 'Script timeout', 'wpsc_ce' ); ?>: </label>
						</th>
						<td>
							<select id="timeout" name="timeout">
								<option value="600"<?php selected( $timeout, 600 ); ?>><?php echo sprintf( __( '%s minutes', 'wpsc_ce' ), 10 ); ?></option>
								<option value="1800"<?php selected( $timeout, 1800 ); ?>><?php echo sprintf( __( '%s minutes', 'wpsc_ce' ), 30 ); ?></option>
								<option value="3600"<?php selected( $timeout, 3600 ); ?>><?php echo sprintf( __( '%s hour', 'wpsc_ce' ), 1 ); ?></option>
								<option value="0"<?php selected( $timeout, 0 ); ?>><?php _e( 'Unlimited', 'wpsc_ce' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Script timeout defines how long WP e-Commerce Exporter is \'allowed\' to process your CSV file, once the time limit is reached the export process halts.', 'wpsc_ce' ); ?></p>
						</td>
					</tr>
<?php } ?>

					<tr>
						<th><?php _e( 'Date Format', 'wpsc_ce' ); ?></th>
						<td>
							<fieldset>
								<label title="F j, Y"><input type="radio" name="date_format" value="F j, Y"<?php checked( $date_format, 'F j, Y' ); ?>> <span><?php echo date( 'F j, Y' ); ?></span></label><br>
								<label title="Y/m/d"><input type="radio" name="date_format" value="Y/m/d"<?php checked( $date_format, 'Y/m/d' ); ?>> <span><?php echo date( 'Y/m/d' ); ?></span></label><br>
								<label title="m/d/Y"><input type="radio" name="date_format" value="m/d/Y"<?php checked( $date_format, 'm/d/Y' ); ?>> <span><?php echo date( 'm/d/Y' ); ?></span></label><br>
								<label title="d/m/Y"><input type="radio" name="date_format" value="d/m/Y"<?php checked( $date_format, 'd/m/Y' ); ?>> <span><?php echo date( 'd/m/Y' ); ?></span></label><br>
<!--
								<label><input type="radio" name="date_format" id="date_format_custom_radio" value="\c\u\s\t\o\m"> Custom: </label><input type="text" name="date_format_custom" value="F j, Y" class="small-text"> <span class="example"> January 6, 2014</span> <span class="spinner"></span>
								<p><a href="http://codex.wordpress.org/Formatting_Date_and_Time"><?php _e( 'Documentation on date and time formatting', 'wpsc_ce' ); ?></a>.</p>
-->
							</fieldset>
							<p class="description"><?php _e( 'The date format option affects how date\'s are presented within your CSV file. Default is set to DD/MM/YYYY.', 'wpsc_ce' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'wpsc_ce_export_options_after' ); ?>

				</table>
			</div>
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="export" />
</form>

<?php do_action( 'wpsc_ce_export_after_form' ); ?>