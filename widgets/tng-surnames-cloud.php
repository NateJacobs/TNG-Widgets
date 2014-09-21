<?php

class TNG_Surnames_Cloud extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'tng_surnames_cloud', // Base ID
			'TNG Surnames Cloud', // Name
			array( 'description' => __( 'Displays a tag cloud of TNG surnames.', 'tng_domain' ), ) // Args
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
			$content = tng_get_surnames_cloud( $base_url, $instance );

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
// TODO: Verify howmany: INTEGER > 0
		$instance['howmany'] = strip_tags( $new_instance['howmany'] );
// TODO: Verify maxsize: minsize < INTEGER < 72px?
		$instance['maxsize'] = strip_tags( $new_instance['maxsize'] );
// TODO: Verify minsize: 6px? < INTEGER < maxsize
		$instance['minsize'] = strip_tags( $new_instance['minsize'] );
		$instance['random'] = ( isset( $new_instance['random'] ) ? 1 : 0 );
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
		if ( empty( $instance[ 'howmany' ] ) ) {
			$instance['howmany'] = "20";
		}
		if ( empty( $instance[ 'maxsize' ] ) ) {
			$instance['maxsize'] = "30";
		}
		if ( empty( $instance[ 'minsize' ] ) ) {
			$instance['minsize'] = "12";
		}

		$title = $instance[ 'title' ]; // widget title (text)
		$tree = $instance['tree']; // TNG tree (text)
		$howmany = $instance[ 'howmany' ]; // Number of surnames in the cloud (integer)
		$maxsize = $instance[ 'maxsize' ]; // Size of top surname in the cloud (px)
		$minsize = $instance[ 'minsize' ]; // Size of lowest surname in the cloud (px)
		$random = $instance[ 'random' ]; // Random order (default = yes)

		?>
		<p class="tng-widget-admin tng-option-title">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="tng-widget-label tng-title"><?php _e( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="tng-widget-input tng-title" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'tree' ); ?>" class="tng-widget-label tng-tree"><?php _e( 'TNG Tree code:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'tree' ); ?>" name="<?php echo $this->get_field_name( 'tree' ); ?>" type="text" value="<?php echo esc_attr( $tree ); ?>" class="tng-widget-input tng-tree" />
		</p>
		<p class="tng-widget-admin tng-option-number">
			<label for="<?php echo $this->get_field_id( 'howmany' ); ?>" class="tng-widget-label tng-howmany"><?php _e( 'How many?:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'howmany' ); ?>" name="<?php echo $this->get_field_name( 'howmany' ); ?>" type="text" value="<?php echo esc_attr( $howmany ); ?>" class="tng-widget-input tng-howmany" />
		</p>
		<p class="tng-widget-admin tng-option-number">
			<label for="<?php echo $this->get_field_id( 'maxsize' ); ?>" class="tng-widget-label tng-maxsize"><?php _e( 'Maximum Size (px):' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'maxsize' ); ?>" name="<?php echo $this->get_field_name( 'maxsize' ); ?>" type="text" value="<?php echo esc_attr( $maxsize ); ?>" class="tng-widget-input tng-maxsize" />
		</p>
		<p class="tng-widget-admin tng-option-number">
			<label for="<?php echo $this->get_field_id( 'minsize' ); ?>" class="tng-widget-label tng-minsize"><?php _e( 'Minimum Size (px):' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'minsize' ); ?>" name="<?php echo $this->get_field_name( 'minsize' ); ?>" type="text" value="<?php echo esc_attr( $minsize ); ?>" class="tng-widget-input tng-minsize" />
		</p>
		<p class="tng-widget-admin tng-option-checkbox">
			<input id="<?php echo $this->get_field_id( 'random' ); ?>" name="<?php echo $this->get_field_name( 'random' ); ?>" <?php checked( $instance['random'], true ); ?> type="checkbox" class="checkbox tng-widget-input tng-random" /> 
			<label for="<?php echo $this->get_field_id( 'random' ); ?>" class="tng-widget-label tng-random"><?php _e( 'Random order' ); ?></label>
		</p>
		<?php
	}
}

function tng_get_surnames_cloud( $base_url, $instance ) {
// TO DO: Convert to HTML5 Canvas compatible jQuery Cloud eg. http://www.goat1000.com/tagcanvas.php
// Sort array based on tag heirarchy, output list, put css in file, add plugin options page etc.
	// Default font sizes
	$min_font_size = $instance['minsize'];
	$max_font_size = $instance['maxsize'];
	// Pull in tag data
	$tags = tng_get_surnames_data( $instance );
		// largest and smallest array values
		$max_qty = max(array_values($tags));
		$min_qty = min(array_values($tags));
		// find the range of values
		$spread = $max_qty - $min_qty;
	//Finally we start the HTML building process to display our tags. For this demo the tag simply searches Google using the provided tag.
	$cloud_tags = array(); // create an array to hold tag code
	foreach ($tags as $tag => $count) {
		$size = $min_font_size + ($count - $minimum_count) * ($max_font_size - $min_font_size) / $spread;
//		$cloud_tags[] = '<a style="font-size: '. floor($size) . 'px"' 
//			. ' class="tag-cloud"'
		$cloud_tags[] = '<a'
 			. ' style="font-size: ' . floor($size) . 'px"'
			. ' class="tng-cloud-link"'
			. ' href=' . $base_url . 'search.php?mylastname=' . $tag . '&amp;lnqualify=equals"'
			. ' title="\'' . $tag  . '\' returned a count of ' . $count . '">' 
			. htmlspecialchars(stripslashes( $tag )) . '</a>';	
	}
	$cloud_html = '<span class="tng-widget-content">' . join( "\n", $cloud_tags ) . "</span>\n";
	return $cloud_html;
}

function tng_get_surnames_data( $instance ) { 
	global $people_table;
// ASK: IS USER CHECK REQUIRED?
//	if( $currentuser && $allow_living ) {}
	$link = mbtng_db_connect() or exit;
	$tree = $instance['tree'];
	$howmany = $instance['howmany'];
	$random = $instance['random'];
	$wherestr = "WHERE lastname != \"\" AND lastname != \"[--?--]\""; //this suppresses "[no surname]" AND [--?--] from the list
	$query = "SELECT * FROM ( SELECT lastname, count( lastname ) AS mycount FROM  $people_table $wherestr GROUP BY lastname ORDER BY count( lastname ) DESC LIMIT 0 , $howmany) AS presort"; //ORDER BY rand()";
	$result = mysql_query( $query ) or die ( "$text[cannotexecutequery]: $query" );
	$arr = array(); // declare variable?
	if( $result ) { // given die above, is this redundant?
		while( $row = mysql_fetch_assoc( $result ) ) { 
			$arr[$row['lastname']] = $row['mycount'];
		}
		mysql_free_result( $result );
	}
	if ( $random ) {
		$arr = shuffle_assoc($arr);
	}
	return $arr; 
}

