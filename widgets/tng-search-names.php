<?php

class TNG_Search_Names extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'tng_search_names', // Base ID
			'TNG Search Names', // Name
			array( 'description' => __( 'Displays a search box for TNG surnames.', 'tng_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		global $languages_path, $text, $cms, $dates;
				
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
			$tree = $instance['tree'];
			$content = tng_search_box( $base_url, $instance );

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
		return $instance;
	}

 	public function form( $instance ) {
		// outputs the options form on admin
		if ( empty( $instance[ 'title' ] ) ) {
			$instance['title'] = __( 'Search Box', 'text_domain' );
		}
		if ( empty( $instance[ 'tree' ] ) ) {
			$instance['tree'] = "tree1";
		}
		$title = $instance[ 'title' ]; // widget title (text)
		$tree = $instance['tree']; // TNG tree (text)

		?>
		<p class="tng-widget-admin tng-option-title">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'tree' ); ?>" class="tng-widget-label tng-title"><?php _e( 'TNG Tree code:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'tree' ); ?>" name="<?php echo $this->get_field_name( 'tree' ); ?>" type="text" value="<?php echo esc_attr( $tree ); ?>" class="tng-widget-input tng-tree" />
		</p>
		<?php
	}
}

function tng_search_box( $base_url, $instance ) {
	global $text;
	//Outputs the TNG search in the sidebar
	$searchbox = '<span class="tng-widget-content">';
	$searchbox .= "<form action=\"".$base_url."search.php\" method=\"post\" >\n"; // specify $tree
	$searchbox .= "<table class=\"menuback\">\n";
	$searchbox .= "<tr><td><span class=\"normal\">".$text['mnulastname'].":<br /><input type=\"text\" name=\"mylastname\" class=\"searchbox\" size=\"14\" /></span></td></tr>\n";
	$searchbox .= "<tr><td><span class=\"normal\">".$text['mnufirstname'].":<br /><input type=\"text\" name=\"myfirstname\" class=\"searchbox\" size=\"14\" /></span></td></tr>\n";
	$searchbox .= "<tr><td><input type=\"hidden\" name=\"mybool\" value=\"AND\" /><input type=\"submit\" name=\"search\" value=\"".$text['mnusearchfornames']."\" class=\"small\" /></td></tr>\n";
	$searchbox .= "</table>\n";
	$searchbox .= "</form>\n";
	$searchbox .= "<ul>\n";
	$searchbox .= "<li style=\"font-weight:bold\"><a href=\"".$base_url."searchform.php\" >".$text['mnuadvancedsearch']."</a></li>\n";
	$searchbox .= "</ul>\n</span>\n";
	return 	$searchbox;
}
