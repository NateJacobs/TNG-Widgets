<?php

class TNG_Profile_Box extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'tng_profile_box', // Base ID
			'TNG Profile Box', // Name
			array( 'description' => __( 'Displays an profile of a chosen TNG person.', 'tng_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		global $languages_path, $text, $cms, $dates;
// from getperson
		global $rootpath, $photopath, $documentpath, $headstonepath, $historypath, $mediapath, $mediatypes_assoc;
		global $photosext, $tree, $medialinks_table, $media_table, $text, $cms, $admtext, $tngconfig;
// from pedigree
		global $pedigree, $generations, $pedmax, $pedoptions, $boxes, $flags, $offpageimgh, $offpageimgw, $cms, $rounded, $templatepath;

//		if ( !mbtng_display_widget() ) {
			extract( $args );
			/* TNG general calls */
			$tng_folder = get_option( 'mbtng_path' );
			chdir( $tng_folder );
			include( 'begin.php' );
			include_once( $cms['tngpath'] . "genlib.php" );
			include( $cms['tngpath'] . "getlang.php" );
			include( $cms['tngpath'] . "{$mylanguage}/text.php" );
			initMediaTypes(); // to generate Media array for use of images
			/* TNG widget specific calls */
			$base_url = mbtng_base_url();

			/* User-selected settings. */
			$title = apply_filters( 'widget_title', $instance['title'] );
			$tree = $instance['tree'];
			$personID = $instance['personID'];
			$description = $instance['description'];

			$content = tng_get_profile_box( $base_url, $tree, $personID, $description );

			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			/* Display widget content from widget settings. */
			if ( $content )
				echo $content; 

			/* After widget (defined by themes). */
			echo $after_widget;
//		}
	}

	public function update( $new_instance, $old_instance ) {
// TO DO: TEST IF PERSON IS IN TNG DB
//		$verifyID = tng_get_profile_data($instance['personID']);
//		if (is_null($verifyID))
//			echo "No foo data!";
//		else
//			echo "Foo data=".$verifyID;
		// processes widget options to be saved
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tree'] = strip_tags( $new_instance['tree'] );
		$instance['personID'] = strip_tags( $new_instance['personID'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		return $instance;
	}

 	public function form( $instance ) {
		// outputs the options form on admin
		if ( empty( $instance[ 'title' ] ) ) {
			$instance['title'] = __( 'Individual Profile', 'text_domain' );
		}
		$title = $instance['title'];
		$tree = $instance['tree'];
		$personID = $instance['personID'];
		$description = $instance['description'];

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
			<label for="<?php echo $this->get_field_id( 'personID' ); ?>" class="tng-widget-label tng-personID"><?php _e( 'TNG Person ID:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'personID' ); ?>" name="<?php echo $this->get_field_name( 'personID' ); ?>" type="text" value="<?php echo esc_attr( $personID ); ?>" class="tng-widget-input tng-personID" />
		</p>
		<p class="tng-widget-admin tng-option-textarea">
			<label for="<?php echo $this->get_field_id( 'description' ); ?>" class="tng-widget-label tng-description"><?php _e( 'TNG Description:' ); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" class="widefat tng-widget-input"><?php echo esc_attr( $description ); ?></textarea>
		</p>
		<?php
	}
}

function tng_get_profile_box( $base_url, $tree, $personID, $description ) {
	$profilearr = tng_get_profile_data( $tree, $personID );
	$personHREF = $base_url."pedigree.php?personID=".$personID."&amp;tree=".$tree;
	$namestr = $profilearr[0];
	$photostr = tng_insert_tng_folder($base_url, $profilearr[1]);
	$photostr_link_to_person = tng_change_image_link( $personHREF, $photostr );
//	$yearsstr = $profilearr[2];

 	$profile = "<span class=\"tng-widget-content\">";
	$profile .= "<a href=\"".$personHREF."\">\n";
 	$profile .= "</a>\n";
 	$profile .= "<h5 class=\"tng-profile-name\">" . $namestr . "</h5>";
 	$profile .= "<span class=\"tng-profile-photo\">" . $photostr_link_to_person . "</span>";
// 	$profile .= "<h6 class=\"tng-profile-years\">" . $yearsstr . "</h6>";
 	$profile .= "<p class=\"tng-widget-description\">" . $description . "</p>";
 	$profile .= "</span>";
 	return $profile;
}

function tng_get_profile_data( $tree, $personID ) { 
	global $people_table;

	$link = mbtng_db_connect() or exit;
	$query = "SELECT * FROM $people_table WHERE personID = \"$personID\" AND gedcom = \"$tree\"";
	$result = mysql_query($query) or die ($text['cannotexecutequery'] . ": $query");

	$row = mysql_fetch_assoc($result);
	if( !mysql_num_rows($result) ) {
		mysql_free_result($result);
//		echo  "{\"error\":\"No one in database with that ID and tree\"}";
// TO DO: ADD MORE APPROPRIATE ERROR CHECKING
		return NULL;
		exit;
	}
	else
		mysql_free_result($result);

	$righttree = checktree($tree);
	$rightbranch = checkbranch($row['branch']);
	$rights = determineLivingPrivateRights($row, $righttree);
    $row['allow_living'] = $rights['living'];
    $row['allow_private'] = $rights['private'];

	$namestr = getName( $row );
//	debugPrint($namestr);
//	$photostr = showSmallPhoto( $personID, $namestr, $rights['both'], 0, false, $row['sex'] );
	$photostr = tng_unstyle_html( showSmallPhoto( $personID, $namestr, $rights['both'], 0, false, $row['sex'] ) );
//	debugPrint($photostr);
	$yearsstr = getYears( $row );
//	debugPrint($yearsstr);

	$arr = array( $namestr, $photostr, $yearsstr );	
	return $arr;

}
