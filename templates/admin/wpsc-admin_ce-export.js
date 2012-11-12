var $j = jQuery.noConflict();

$j(".checkAll").click(function() {
    if("checkall" === $(this).val()) {
         $(".product_field").attr('checked', true);
         $(this).val("uncheckall"); //change button text
    }
    else if("uncheckall" === $(this).val()) {
         $(".product_field").attr('checked', false);
         $(this).val("checkall"); //change button text
    }
});