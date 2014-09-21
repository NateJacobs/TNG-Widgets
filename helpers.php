<?php

/************************************************
*                                               *
*     NON-TNG SPECIFIC 'HELPER' FUNCTIONS       *
*                                               *
* tng_insert_tng_url                            *
* tng_insert_tng_folder                         *
* tng_change_image_link                         *
* tng_unstyle_html                              *
* tng_unstyle_html                              *
************************************************/

	function tng_insert_tng_url($path){
		$newpath = preg_replace('#img/#i', $base_url.'img/', $path);
		return $newpath;
	}

	function tng_insert_tng_folder($base_url, $path){
		$newpath = preg_replace('#src="#i', 'src="'.$base_url, $path);
		return $newpath;
	}

	function tng_change_image_link($personHREF, $path){
		$pattern = "/(?<=href=(\"|'))[^\"']+(?=(\"|'))/";
		$newpath = preg_replace($pattern,$personHREF,$path);
		return $newpath;
	}

	function tng_unstyle_html($html){
		$dom = new DOMDocument;                 // init new DOMDocument
		$dom->loadHTML($html);                  // load HTML into it
		$xpath = new DOMXPath($dom);            // create a new XPath
		$nodes = $xpath->query('//*[@style]');  // Find elements with a style attribute
		foreach($nodes as $node) {              // Iterate over found elements
		    $node->removeAttribute('style');    // Remove style attribute
		    $node->removeAttribute('border');   // Remove Border attribute
		}
		return $dom->saveHTML();
	}
	
	// TO DO: IDEALLY THIS SHOULD BE ADDED TO THE WP-TNG PLUGIN
	function tng_body_classes($classes) {
	    global $wp_query;

		if ( mbtng_display_widget() ) {
			if ( is_page() ) {
				$classes[] = "tng-homepage";
			} else {
				$classes[] = "tng-content";
			}
		}

	    return array_unique($classes);
	}
 
// FROM ROGER THEKIWI

	//the functions below get a client's time offset to figure out their local date and time
	//Code contributed by Luke from TNG Forums.

	function get_client_time_offset( ){
	if(!isset($_COOKIE['GMT_bias'])) {
	?>
	<script type="text/javascript">
	var Cookies = {};
	/***
	* @name = string, name of cookie
	* @value = string, value of cookie
	* @days = int, number of days before cookie expires
	***/
	Cookies.create = function (name, value, days) {
	if (days) {
	var date = new Date();
	date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	var expires = "; expires=" + date.toGMTString();
	} else {
	var expires = "";
	}
	document.cookie = name + "=" + value + expires + "; path=/";
	this[name] = value;
	}
	var now = new Date();
	Cookies.create("GMT_bias",now.getTimezoneOffset(),7);
	window.location = "<?php echo $_SERVER['PHP_SELF'];?>";
	</script>
	<?php
	}
	return $_COOKIE['GMT_bias'];

	}


	//function get_local_date( )
	//{
	//return gmdate( 'Y-m-d' , ( time() - get_client_time_offset( ) * 60 ) );
	//}
	function get_local_time( )
	{
	return gmdate( 'Y-m-d H:i:s' , ( time() - get_client_time_offset( ) * 60 ) );
	}
	function get_local_date($format)
	{
	return gmdate( $format , ( time() - get_client_time_offset( ) * 60 ) );
	}

	// end of functions for client date and time.
	
	function shuffle_assoc( $array ){
	   $keys = array_keys( $array );
	   shuffle( $keys );
	   return array_merge( array_flip( $keys ) , $array );
	}
