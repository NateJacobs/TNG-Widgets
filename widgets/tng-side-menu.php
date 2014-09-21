<?php

class TNG_Side_Menu extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'tng_side_menu', // Base ID
			'TNG Side Menu', // Name
			array( 'description' => __( 'Displays a side menu of TNG pages.', 'tng_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		global $languages_path;
		
//		if ( !mbtng_display_widget() ) {
			extract( $args );	
			// TNG general calls
			$tng_folder = get_option( 'mbtng_path' );
			chdir( $tng_folder );
			include( 'begin.php' );
			include_once( $cms['tngpath'] . "genlib.php" );
			include( $cms['tngpath'] . "getlang.php" );
			include( $cms['tngpath'] . "{$mylanguage}/text.php" );
			// TNG widget specfic calls
			$base_url = mbtng_base_url();
			$content = tng_get_side_menu( $base_url, $instance );

			/* User-selected settings. */
			$title = apply_filters( 'widget_title', $instance['title'] );

			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			/* Display widget content from widget settings. */
			if ( $content ) {
				echo $content;
			}

			/* After widget (defined by themes). */
			echo $after_widget;
//		}
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tree'] = strip_tags( $new_instance['tree'] );

		$instance['thisone'] = ( isset( $new_instance['thisone'] ) ? 1 : 0 );
		return $instance;
	}

 	public function form( $instance ) {
		// outputs the options form on admin
// TODO: Add option to select tree
		if ( empty( $instance[ 'title' ] ) ) {
			$instance['title'] = __( 'Surnames Cloud', 'text_domain' );
		}
		if ( empty( $instance[ 'tree' ] ) ) {
			$instance['tree'] = "tree1";
		}

		$title = $instance[ 'title' ]; // widget title (text)
		$tree = $instance['tree']; // TNG tree (text)


		$thisone = $instance[ 'thisone' ]; // Random order (default = yes)

		?>
		<p class="tng-widget-admin tng-option-title">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="tng-widget-label tng-title"><?php _e( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="tng-widget-input tng-title" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'tree' ); ?>" class="tng-widget-label tng-tree"><?php _e( 'TNG Tree code:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'tree' ); ?>" name="<?php echo $this->get_field_name( 'tree' ); ?>" type="text" value="<?php echo esc_attr( $tree ); ?>" class="tng-widget-input tng-tree" />
		</p>

		<p class="tng-widget-admin tng-option-checkbox">
			<input id="<?php echo $this->get_field_id( 'thisone' ); ?>" name="<?php echo $this->get_field_name( 'thisone' ); ?>" <?php checked( $instance['thisone'], true ); ?> type="checkbox" class="checkbox tng-widget-input tng-thisone" /> 
			<label for="<?php echo $this->get_field_id( 'thisone' ); ?>" class="tng-widget-label tng-thisone"><?php _e( 'This One' ); ?></label>
		</p>
		<?php
	}
}

function tng_get_menu( $instance ) {
// SELECTION IFs
// GROUP? FIND/MEDIA/INFO
// ARRAY from Options
// FOREACH from array
	$menu_html = '<span class="tng-widget-content">' . "\n";
	$menu_html .= '<ul>' . "\n";


	$menu_html .= "<li class=\"suggest\" style=\"font-weight:bold\"><a href=\"{$base_url}suggest.php\" target=\"_blank\">{$text['contactus']}</a></li>\n";
		$menu_html .= "</ul>\n";

// FIND
		$menu_html .= "<ul style=\"margin-top:0.75em\">\n";
		$menu_html .= "<li class=\"surnames\"><a href=\"{$base_url}surnames.php\" target=\"_blank\">{$text['mnulastnames']}</a></li>\n";
		$menu_html .= "<li class=\"bookmarks\"><a href=\"{$base_url}bookmarks.php\">{$text['bookmarks']}</a></li>\n";
		$menu_html .= "<li class=\"places\"><a href=\"{$base_url}places.php\" target=\"_blank\">{$text['places']}</a></li>\n";
//								dates
//								calendar
		$menu_html .= "<li class=\"cemeteries\"><a href=\"{$base_url}cemeteries.php\" target=\"_blank\">{$text['mnucemeteries']}</a></li>\n";
//								search people
//								search families
// MEDIA
		$menu_html .= "<ul>\n";
		$menu_html .= "<li class=\"photos\"><a href=\"{$base_url}browsemedia.php?mediatypeID=photos\" target=\"_blank\">{$text['mnuphotos']}</a></li>\n";
		$menu_html .= "<li class=\"documents\"><a href=\"{$base_url}browsemedia.php?mediatypeID=documents\" target=\"_blank\">{$text['documents']}</a></li>\n";
		$menu_html .= "<li class=\"headstones\"><a href=\"{$base_url}browsemedia.php?mediatypeID=headstones\" target=\"_blank\">{$text['mnutombstones']}</a></li>\n";
		$menu_html .= "<li class=\"histories\"><a href=\"{$base_url}browsemedia.php?mediatypeID=histories\" target=\"_blank\">{$text['mnuhistories']}</a></li>\n";
		$menu_html .= "<li class=\"recordings\"><a href=\"{$base_url}browsemedia.php?mediatypeID=recordings\" target=\"_blank\">{$text['recordings']}</a></li>\n";
		$menu_html .= "<li class=\"videos\"><a href=\"{$base_url}browsemedia.php?mediatypeID=videos\" target=\"_blank\">{$text['videos']}</a></li>\n";
		$menu_html .= "<li class=\"albums\"><a href=\"{$base_url}browsealbums.php\" target=\"_blank\">{$text['albums']}</a></li>\n";		
		$menu_html .= "<li class=\"media\"><a href=\"{$base_url}browsemedia.php\" target=\"_blank\">{$text['allmedia']}</a>\n";
		$menu_html .= "</ul></li>";

// INFO
		$menu_html .= "<li class=\"whatsnew\"><a href=\"{$base_url}whatsnew.php\" target=\"_blank\">{$text['mnuwhatsnew']}</a></li>\n";
		$menu_html .= "<li class=\"mostwanted\"><a href=\"{$base_url}mostwanted.php\" target=\"_blank\">{$text['mostwanted']}</a></li>\n";
//		$menu_html .= "<li class=\"reports\"><a href=\"{$base_url}reports.php\">{$text['mnureports']}</a></li>\n";
//								stats
//		$menu_html .= "<li class=\"trees\"><a href=\"{$base_url}browsetrees.php\" target=\"_blank\">{$text['mnustatistics']}</a></li>\n";
//		$menu_html .= "<li class=\"notes\"><a href=\"{$base_url}browsenotes.php\">{$text['notes']}</a></li>\n";
//		$menu_html .= "<li class=\"sources\"><a href=\"{$base_url}browsesources.php\">{$text['mnusources']}</a></li>\n";
//		$menu_html .= "<li class=\"repos\"><a href=\"{$base_url}browserepos.php\">{$text['repositories']}</a></li>\n";
//								contact
		$menu_html .= "</ul>\n";
		$menu_html .= "</span>\n";

// UNKNOWN
//		$menu_html .= "<li class=\"anniversaries\"><a href=\"{$base_url}anniversaries.php\" target=\"_blank\">{$text['anniversaries']}</a></li>\n";


// ADMIN MENU
//		$menu_html .= "<li class=\"language\"><a href=\"{$base_url}changelanguage.php\">{$text['mnulanguage']}</a></li>\n";
		$menu_html .= "<ul style=\"margin-top:0.75em\">\n";
			if ($allow_admin) {
			$menu_html .= "<li class=\"admin\" style=\"font-weight:bold\"><a href=\"{$base_url}admin.php\" target=\"_blank\">{$text['mnuadmin']}</a></li>\n";
			$menu_html .= "<li class=\"showlog\"><a href=\"{$base_url}showlog.php\" target=\"_blank\">{$text['mnushowlog']}</a></li>\n";
			}
		$menu_html .= "</ul>\n";
// LOGGED IN OUT
		$menu_html .= "<ul style=\"margin-top:0.75em\">\n";
			if (!is_user_logged_in()) {
			$menu_html .= "<li class=\"register\" style=\"font-weight:bold\"><a href=\"{$base_url}newacctform.php\">{$text['mnuregister']}</a></li>\n";
			$menu_html .= "<li class=\"login\" style=\"font-weight:bold\"><a href=\"{$base_url}login.php\">{$text['mnulogon']}</a></li>\n";
			} else {
				if (function_exists('wp_logout_url'))
				$menu_html .= "<li class=\"logout\" style=\"font-weight:bold\"><a href=\"".html_entity_decode(wp_logout_url())."\">{$text['logout']}</a></li>\n";
				else
				$menu_html .= "<li class=\"logout\" style=\"font-weight:bold\"><a href=\"".trailingslashit(get_bloginfo('wpurl'))."wp-login.php?action=logout"."\">{$text['logout']}</a></li>\n";
			}
		$menu_html .= "</ul>";

	return $menu_html;
}

function tng_get_menu_data( $instance ) { 
	global $people_table;
// ASK: IS USER CHECK REQUIRED?
//	if( $currentuser && $allow_living ) {}
	$link = mbtng_db_connect() or exit;
	$tree = $instance['tree'];
	$wherestr = "WHERE lastname != \"\" AND lastname != \"[--?--]\""; //this suppresses "[no surname]" AND [--?--] from the list
	$query = "SELECT * FROM ( SELECT lastname, count( lastname ) AS mycount FROM  $people_table $wherestr GROUP BY lastname ORDER BY count( lastname ) DESC LIMIT 0 , $howmany) AS presort"; //ORDER BY rand()";
	$result = mysql_query( $query ) or die ( "$text[cannotexecutequery]: $query" );
	$arr = array(); // declare variable?
	if( $result ) { // given die above, is this redundant?
		while( $row = mysql_fetch_assoc( $result ) ) { 
			$arr[] = $row;
		}
		mysql_free_result( $result );
	}
}
