<?php

class TNG_Top_Surnames extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'tng_top_surnames', // Base ID
			'TNG Top Surnames', // Name
			array( 'description' => __( 'Displays a list of the top surnames in TNG.', 'tng_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		global $languages_path, $tree;
		
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

			/* User-selected settings. */
			$title = apply_filters( 'widget_title', $instance['title'] );
			$tree = $instance['tree'];
			$top = $instance['top'];

			$content = tng_get_topsurnames_list( $base_url, $tree, $top );
			
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
		$instance['top'] = strip_tags( $new_instance['top'] );
		return $instance;
	}

 	public function form( $instance ) {
		// outputs the options form on admin
// TODO: Add option to select tree
		if ( empty( $instance[ 'title' ] ) ) {
			$instance['title'] = __( 'Top Surnames List', 'text_domain' );
		}
		if ( empty( $instance[ 'tree' ] ) ) {
			$instance['tree'] = "tree1";
		}
		if ( empty( $instance[ 'top' ] ) ) {
			$instance['top'] = "10";
		}
		$title = $instance[ 'title' ];
		$tree = $instance['tree'];
		$top = $instance['top'];

		?>
		<p class="tng-widget-admin tng-option-title">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="tng-widget-label tng-title"><?php _e( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="tng-widget-input tng-title" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'tree' ); ?>" class="tng-widget-label tng-tree"><?php _e( 'TNG Tree:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'tree' ); ?>" name="<?php echo $this->get_field_name( 'tree' ); ?>" type="text" value="<?php echo esc_attr( $tree ); ?>" class="tng-widget-input tng-tree" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'top' ); ?>" class="tng-widget-label tng-top"><?php _e( 'How Many?' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'top' ); ?>" name="<?php echo $this->get_field_name( 'top' ); ?>" type="text" value="<?php echo esc_attr( $top ); ?>" class="tng-widget-input tng-top" />
		</p>
		<?php
	}
}

function tng_get_topsurnames_list( $base_url, $tree, $top ) {
// TO DO: Convert to HTML5 Canvas compatible jQuery Cloud eg. http://www.goat1000.com/tagcanvas.php
// ASK: What advantage using TNGs getURL function?
	$surnames = tng_get_topsurnames_data($top);
	$list_html = '<span class="tng-widget-content">';
	$list_html .= '<ol class="tng-list">';
	foreach ($surnames as $surname) {
		$lntitle = $surname[0];
		$lnsearch = urlencode( $surname[0] );
		$lntotal = $surname[1];
		$list_html .= '<li>'
		. '<a class="tng-list-item"'
		. 'href="' . $base_url . 'search.php?mylastname=' . $lnsearch . '&amp;lnqualify=equals&amp;mybool=AND&amp;tree=' . $tree . '"'
		. 'title="\'' . $title  . '\' returned a count of ' . $lntotal . '">' 
		. htmlspecialchars(stripslashes($lntitle))
		. '</a>'
		. ' <span class="tng-list-value">(' . $lntotal . ')</span>'
		. '</li>';
	}
	$list_html .= "</ol>\n";
	$list_html .= "</span>\n";
	return $list_html;
}

function tng_get_topsurnames_data($top) { 
	$link = mbtng_db_connect() or exit;
	$people_table = "`tng_people`";
	$wherestr = "WHERE lastname != \"\" and lastname != \"[--?--]\""; // suppress "[no surname]" and [--?--]
	// TODO: make top number a widget option
	$topnum = $top;
	$query = "SELECT lastname, binary(lastname) as binlast, count(lastname) as lncount FROM $people_table $wherestr GROUP BY binlast ORDER by lncount DESC, binlast LIMIT $topnum";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
// ASK: declare variable?
	$arr = array();
// ASK: given die above, is this 'if' redundant?
	if( $result ) {
		while( $row = mysql_fetch_array($result) ) { 
			$arr[] = array( $row['binlast'], $row['lncount'] );
		}
		mysql_free_result($result);
	}
	return $arr; 
}
