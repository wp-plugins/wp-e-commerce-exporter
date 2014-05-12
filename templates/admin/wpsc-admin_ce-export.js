// @mod - This may be deprecated and if required removed
function showProgress() {
	window.scrollTo(0,0);
	document.getElementById('progress').style.display = 'block';
	document.getElementById('content').style.display = 'none';
	document.getElementById('support-donate_rate').style.display = 'none';
}

var $j = jQuery.noConflict();
$j(function() {

	// This controls the Skip Overview link on the Overview screen
	$j('#skip_overview').click(function(){
		$j('#skip_overview_form').submit();
	});

	// @mod - This may be deprecated and if required removed
	// This controls the progress bar that is displayed for non-saved exports (print to browser only)
	$j('#postform').submit(function() {
		if( $j('#delete_temporary_csv').val() == 1 ) {
			showProgress();
			return false;
		}
	});

	// Date Picker
	$j('.datepicker').datepicker({
		dateFormat: 'dd/mm/yy'
	});

	// Chosen
	$j(".chzn-select").chosen({
		search_contains: true
	});

	// Show Products as default export type
	$j('#export-products').show();
	// Categories
	$j('#export-products-filters-categories').hide();
	if( $j('#products-filters-categories').attr('checked') ) {
		$j('#export-products-filters-categories').show();
	}
	// Tags
	$j('#export-products-filters-tags').hide();
	if( $j('#products-filters-tags').attr('checked') ) {
		$j('#export-products-filters-tags').show();
	}
	// Product Status
	$j('#export-products-filters-status').hide();
	if( $j('#products-filters-status').attr('checked') ) {
		$j('#export-products-filters-status').show();
	}
	$j('#export-categories').hide();
	$j('#export-tags').hide();
	$j('#export-orders').hide();
	$j('#export-orders-filters-status').hide();
	if( $j('#orders-filters-status').attr('checked') ) {
		$j('#export-orders-filters-status').show();
	}
	$j('#export-orders-filters-date').hide();
	if( $j('#orders-filters-date').attr('checked') ) {
		$j('#export-orders-filters-date').show();
	}
	$j('#export-orders-filters-user_role').hide();
	if( $j('#orders-filters-user_role').attr('checked') ) {
		$j('#export-orders-filters-user_role').show();
	}
	$j('#export-customers').hide();
	$j('#export-coupons').hide();

	$j('#products-filters-categories').click(function(){
		$j('#export-products-filters-categories').toggle();
	});
	$j('#products-filters-tags').click(function(){
		$j('#export-products-filters-tags').toggle();
	});
	$j('#products-filters-status').click(function(){
		$j('#export-products-filters-status').toggle();
	});
	$j('#orders-filters-status').click(function(){
		$j('#export-orders-filters-status').toggle();
	});
	$j('#orders-filters-date').click(function(){
		$j('#export-orders-filters-date').toggle();
	});
	$j('#orders-filters-user_role').click(function(){
		$j('#export-orders-filters-user_role').toggle();
	});

	// Export types
	// Change visibility of export type options
	$j('#products').click(function(){
		$j('#export-products').show();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();

		$j('.export-options').hide();
		$j('.product-options').show();
	});
	// Change visibility of export type options
	$j('#categories').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').show();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();

		$j('.export-options').hide();
		$j('.category-options').show();
	});
	// Change visibility of export type options
	$j('#tags').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').show();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();

		$j('.export-options').hide();
		$j('.tag-options').show();
	});
	// Change visibility of export type options
	$j('#orders').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').show();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();

		$j('.export-options').hide();
		$j('.order-options').show();
	});
	// Change visibility of export type options
	$j('#customers').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').show();
		$j('#export-coupons').hide();

		$j('.export-options').hide();
		$j('.customer-options').show();
	});
	// Change visibility of export type options
	$j('#coupons').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').show();

		$j('.export-options').hide();
		$j('.coupon-options').show();
	});

	// Export button
	$j('#export_products').click(function(){
		$j('input:radio[name=dataset]:nth(0)').attr('checked',true);
	});
	$j('#export_orders').click(function(){
		$j('input:radio[name=dataset]:nth(3)').attr('checked',true);
	});
	$j('#export_customers').click(function(){
		$j('input:radio[name=dataset]:nth(4)').attr('checked',true);
	});
	$j('#export_coupons').click(function(){
		$j('input:radio[name=dataset]:nth(5)').attr('checked',true);
	});

	// Select all field options for this export type
	$j('.checkall').click(function () {
		$j(this).closest('.postbox').find(':checkbox').attr('checked', true);
	});
	// Unselect all field options for this export type
	$j('.uncheckall').click(function () {
		$j(this).closest('.postbox').find(':checkbox').attr('checked', false);
	});

	// This auto-selects the export type based on the link from the Overview screen
	$j(document).ready(function() {
		var href = jQuery(location).attr('href');
		if (href.toLowerCase().indexOf('tab=export') >= 0) {
			if (href.toLowerCase().indexOf('#') >= 0 ) {
				var type = href.substr(href.indexOf("#") + 1)
				var type = type.replace('export-','');
				$j('#'+type).trigger('click');
			}
		}
	});

});