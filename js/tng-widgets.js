$j=jQuery.noConflict();

$j(document).ready(function(){

	/* Add .clickable to the following: */
	$j("section.widget_tng_profile_box").addClass("clickable");

	/* You can safely use $ in this code block to reference jQuery */
	$j('.clickable')
		.css("cursor", "pointer")
		.attr('title', "Link to Family Tree")
		.click(function() {
			var link = $j(this).find('a:first');
			var linkhref = link.attr('href');
			if (link.attr('target')) {
			    var newWindow = window.open(linkhref, link.attr('target'));
			    newWindow.focus()
			} else {
			    window.location = linkhref
			}
			return false
		});

});
