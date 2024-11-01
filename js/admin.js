jQuery(function($) {
	
	var $singlePosts = $('input[name="display_settings[display_single_post]"]');
	var $displayPosts = $singlePosts.parents('tr').next();
	
	$singlePosts.is(':checked') ? $displayPosts.show() : $displayPosts.hide();
	$singlePosts.click(function() {
		$(this).is(':checked') ? $displayPosts.show() : $displayPosts.hide();
	});
	
});