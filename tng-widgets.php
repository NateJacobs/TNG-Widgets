<?php
/*
Plugin Name: TNG Widgets
Plugin URI: http://www.4-14.org.uk/wordpress-plugins/tng
Description: Integrates TNG (The Next Generation of Genealogy) mods as Wordpress widgets.
Version: 0.6
Author: Mike Goodstadt, John Lisle, Roger Moffat, Mark Barnes, Darrin Lythgoe
Updated by: Mike Goodstadt, 26/07/2012
Author URI: http://mikegoodstadt.com
Copyright (c) 2012 Mike Goodstadt
Licence: Licence GPL2
*/
/*  Copyright 2011  Philip King  (contact: http://kingsolutions.org.uk/wordpress/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Loads up all the widgets defined by TNG. This functionality will be released
 *  as a part of the WP/TNG Plugin in a future release.
 *
 * @package TNG
 * @subpackage Widgets
 */

if (!class_exists('TNG_Widgets')) {
	class TNG_Widgets {

	// TODO: Variable declarations go here

	// TODO: Constructor
	function __construct() {
		// TODO: Enter Plugin Actions and Filters here
			add_action('init', array($this, 'tng_jquery_init'),1);
//			add_action('plugins_loaded', 'tng_serve_widgets');    	// WHY DOESNT THIS WORK? Serves static files (.jpg, .css, etc.) as soon as possible
			// Runs initialisation.
//			function tng_serve_widgets () { // DOESNT WORK? SEE ABOVE
				add_action('widgets_init', array($this, 'tng_widgets_init'),1);		// Initialise the widgets if main TNG plugin exists.
//			} // DOESNT WORK? SEE ABOVE
	} // End constructor
	// Core functions go here
	// TODO: Enter Action and Filter Methods here
	
	function tng_widgets_init() {

//		require_once ( "widgets.php" ); // widget loader separate this core
		require_once ( "helpers.php" ); // basic non-TNG-specific functions

		/* REQUIRE widget code */
		require_once ( "widgets/tng-search-names.php" );
//		require_once ( "widgets/tng-side-menu.php" );
		require_once ( "widgets/tng-surnames-cloud.php" );
		require_once ( "widgets/tng-profile-box.php" );
		require_once ( "widgets/tng-top-surnames.php" );
		require_once ( "widgets/tng-list-events.php" );


		/* INSTANCE widget class */
		$searchnames = new TNG_Search_Names;
//		$sidemenu = new TNG_Side_Menu;
		$surnamescloud = new TNG_Surnames_Cloud;
		$profilebox = new TNG_Profile_Box;
		$profilebox = new TNG_Top_Surnames;
		$recentevents = new TNG_List_Events;

		/* REGISTER widget with Wordpress */
		register_widget( 'TNG_Search_Names' );
//		register_widget( 'TNG_Side_Menu' );
		register_widget( 'TNG_Surnames_Cloud' );
		register_widget( 'TNG_Profile_Box' );
		register_widget( 'TNG_Top_Surnames' );
		register_widget( 'TNG_List_Events' );

		if (!is_admin()) {
		    wp_register_style( 'TNG_Widgets_Styles', plugins_url('css/tng-widgets.css', __FILE__) );
		    wp_enqueue_style('TNG_Widgets_Styles'); 
			add_filter('body_class','tng_body_classes');
		} else {
		    wp_register_style( 'TNG_Widgets_Styles_Admin', plugins_url('css/tng-widgets-admin.css', __FILE__) );
		    wp_enqueue_style('TNG_Widgets_Styles_Admin'); 
		}
	}
	
	function tng_jquery_init() {
		if (!is_admin()) {
/* NOT NEEDED UNLESS USING JQUERY NOT INCLUDED WITH WORDPRESS (eg. later version of jQuery)
			wp_deregister_script('jquery');
			// load the local copy of jQuery in the footer
			wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js', false, '1.7.2', true);
			// or load the Google API copy in the footer
			//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js', false, '1.8.0', true);
*/
			// load a JS file from my theme/plugin: js/my_script.js
/* NOT NEEDED IF USING SUFFUSION which has html5.js (like default Wordpress TwentyTwelve theme).
 * BUT HOW CHECK FOR IT AND TO INCLUDE IF NOT IN THEME?
			wp_enqueue_script('tng_html5shiv', plugins_url('/js/html5shiv.js', __FILE__), array('jquery'), '3.3', true);
*/
			wp_enqueue_script('tng_widgets_script', plugins_url('/js/tng-widgets.js', __FILE__), array('jquery'), '1.0', true);
		}
	}

	// TODO: Enter Helper Methods here => see helpers.php

	} // End TNG_Widgets
} // End if (!class_exists("TNG_Widgets"))

/**
  * Instantiate (create an instance of) the class
  */
if (class_exists("TNG_Widgets")) {
	// TODO: Enter New Instance Name
	$widgets = new TNG_Widgets();
} // End instantiate class


?>
